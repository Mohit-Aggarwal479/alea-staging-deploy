<?php
/**
 * Plugin Name: ALEA — guest page cache
 * Description: A conservative static page cache for logged-out visitors.
 *
 *              WHY THIS EXISTS: the site runs the LiteSpeed Cache plugin, but
 *              the web server reports `Server: Apache`. LiteSpeed's page cache
 *              is implemented by the LiteSpeed web server reading the
 *              X-LiteSpeed-Cache-Control header — on Apache nothing consumes
 *              it, so NO page caching happens at all and every visit pays a
 *              full PHP + database render (measured 2-6s, and 17s on live).
 *              This fills that gap until the account is moved to a LiteSpeed
 *              server, at which point this can simply be deleted.
 *
 *              DESIGN: deliberately narrow. Only plain GET requests from
 *              logged-out visitors with no query string and no WordPress
 *              cookies are cached, and only successful HTML responses. Any
 *              doubt at all and the request goes to PHP as normal.
 *
 *              SAFETY: mu-plugins load before the theme and before regular
 *              plugins, so a cache hit skips Elementor, the theme, and every
 *              query. It cannot serve a cached page to a logged-in user, to a
 *              form submission, or to anything with a query string. Contact
 *              Form 7 remains correct because it refreshes its nonce over REST
 *              after load, which is exactly how CF7 is designed to work behind
 *              a page cache.
 *
 *              CONTROL: append ?alea-nocache=1 to bypass and refresh an entry.
 *              Delete the cache directory (or this file) to disable entirely.
 *
 * Author: Claude (ALEA redesign)
 */

defined( 'ABSPATH' ) || exit;

define( 'ALEA_CACHE_DIR', WP_CONTENT_DIR . '/cache/alea-page' );
define( 'ALEA_CACHE_TTL', 6 * HOUR_IN_SECONDS );

/** Is this request even a candidate for caching? */
function alea_cache_eligible() {
	if ( defined( 'DOING_CRON' ) && DOING_CRON ) {
		return false;
	}
	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
		return false;
	}
	if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
		return false;
	}
	if ( defined( 'WP_CLI' ) && WP_CLI ) {
		return false;
	}
	if ( is_admin() ) {
		return false;
	}
	if ( ! isset( $_SERVER['REQUEST_METHOD'] ) || 'GET' !== $_SERVER['REQUEST_METHOD'] ) {
		return false;
	}
	// Any query string at all: skip. Keeps previews, UTM-tagged A/B behaviour,
	// the calculator's ?shape=/?collection= presets and ?alea-off out of cache.
	if ( ! empty( $_SERVER['QUERY_STRING'] ) ) {
		return false;
	}
	// Any WordPress/WooCommerce/comment cookie means a personalised response.
	if ( ! empty( $_COOKIE ) ) {
		foreach ( array_keys( $_COOKIE ) as $name ) {
			if ( preg_match( '/^(wordpress_|wp-|comment_|woocommerce_|wp_woocommerce)/i', (string) $name ) ) {
				return false;
			}
		}
	}
	return true;
}

/** Cache file for the current URL. */
function alea_cache_file() {
	$host = isset( $_SERVER['HTTP_HOST'] ) ? (string) $_SERVER['HTTP_HOST'] : '';
	$uri  = isset( $_SERVER['REQUEST_URI'] ) ? (string) $_SERVER['REQUEST_URI'] : '/';
	$path = (string) wp_parse_url( $uri, PHP_URL_PATH );
	return ALEA_CACHE_DIR . '/' . md5( $host . '|' . $path ) . '.html';
}

/* ---------------------------------------------------------------------
 * SERVE — runs as early as mu-plugins allow.
 * ------------------------------------------------------------------ */
if ( alea_cache_eligible() && ! isset( $_GET['alea-nocache'] ) ) {
	$alea_cf = alea_cache_file();
	if ( is_readable( $alea_cf ) ) {
		$age = time() - (int) filemtime( $alea_cf );
		if ( $age < ALEA_CACHE_TTL ) {
			$body = file_get_contents( $alea_cf ); // phpcs:ignore
			if ( is_string( $body ) && strlen( $body ) > 500 ) {
				header( 'Content-Type: text/html; charset=UTF-8' );
				header( 'X-ALEA-Cache: HIT' );
				header( 'X-ALEA-Cache-Age: ' . (int) $age );
				echo $body; // phpcs:ignore
				exit;
			}
		}
	}
}

/* ---------------------------------------------------------------------
 * STORE — buffer the response and write it if it is safely cacheable.
 * ------------------------------------------------------------------ */
add_action( 'template_redirect', function () {
	if ( ! alea_cache_eligible() || isset( $_GET['alea-nocache'] ) ) {
		return;
	}
	if ( is_user_logged_in() || is_404() || is_search() || is_feed() || is_preview() || post_password_required() ) {
		return;
	}
	header( 'X-ALEA-Cache: MISS' );

	ob_start( function ( $html ) {
		if ( ! is_string( $html ) || strlen( $html ) < 1000 ) {
			return $html;
		}
		// Only ever store a complete, successful HTML document.
		if ( 200 !== http_response_code() ) {
			return $html;
		}
		if ( false === stripos( $html, '</html>' ) ) {
			return $html;
		}
		// Never store a page that rendered a PHP error or a logged-in admin bar.
		if ( false !== stripos( $html, 'id="wpadminbar"' ) || preg_match( '/<b>(Fatal error|Warning|Notice|Parse error)<\/b>/i', $html ) ) {
			return $html;
		}
		if ( ! wp_mkdir_p( ALEA_CACHE_DIR ) ) {
			return $html;
		}
		$file = alea_cache_file();
		// Atomic write: a partial file must never be served.
		$tmp = $file . '.' . getmypid() . '.tmp';
		if ( false !== file_put_contents( $tmp, $html . "\n<!-- alea-cache " . gmdate( 'c' ) . " -->" ) ) { // phpcs:ignore
			@rename( $tmp, $file ); // phpcs:ignore
		}
		return $html;
	} );
}, 0 );

/* ---------------------------------------------------------------------
 * PURGE — on any content change, and on demand.
 * ------------------------------------------------------------------ */
function alea_cache_purge_all() {
	if ( ! is_dir( ALEA_CACHE_DIR ) ) {
		return 0;
	}
	$n     = 0;
	$files = glob( ALEA_CACHE_DIR . '/*.html' );
	if ( is_array( $files ) ) {
		foreach ( $files as $f ) {
			if ( @unlink( $f ) ) { // phpcs:ignore
				$n++;
			}
		}
	}
	return $n;
}
foreach ( array( 'save_post', 'deleted_post', 'switch_theme', 'customize_save_after', 'wp_update_nav_menu', 'update_option_page_on_front', 'update_option_show_on_front', 'activated_plugin', 'deactivated_plugin' ) as $alea_hook ) {
	add_action( $alea_hook, 'alea_cache_purge_all' );
}
/* Piggyback LiteSpeed's own purge so one action clears both. */
add_action( 'litespeed_purged_all', 'alea_cache_purge_all' );

/* Admin-bar control + a purge endpoint for logged-in admins. */
add_action( 'admin_bar_menu', function ( $bar ) {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$bar->add_node( array(
		'id'    => 'alea-cache-purge',
		'title' => 'Purge ALEA cache',
		'href'  => wp_nonce_url( home_url( '/?alea-purge=1' ), 'alea_purge' ),
	) );
}, 100 );
add_action( 'init', function () {
	if ( ! isset( $_GET['alea-purge'] ) || ! current_user_can( 'manage_options' ) ) {
		return;
	}
	check_admin_referer( 'alea_purge' );
	$n = alea_cache_purge_all();
	wp_safe_redirect( add_query_arg( 'alea-purged', (int) $n, home_url( '/' ) ) );
	exit;
} );
