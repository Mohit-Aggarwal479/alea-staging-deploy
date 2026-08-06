<?php
/**
 * Plugin Name: ALEA — site-wide markup repairs
 * Description: Fixes two defects that live in the database (an Elementor widget
 *              and page-level SEO meta), so they cannot be corrected at source
 *              from the theme repo. Applies to EVERY page, including the legacy
 *              Elementor ones the redesign has not replaced.
 *
 *              1. The floating "Chat" contact button renders
 *                 href="https://wa.me/+91 95549 95449" — a leading plus and
 *                 literal spaces. WhatsApp rejects it, so the site-wide primary
 *                 WhatsApp CTA is dead on every page.
 *              2. Page-level JSON-LD publishes a fabricated aggregateRating
 *                 (4.6 / 179), named customer reviews, and a second premises
 *                 with a street address and opening hours. None of it is
 *                 verified. Fabricated review markup is a Google penalty risk.
 *
 *              Both repairs only ever DELETE or CORRECT — they never invent.
 *              Every one fails open: if the markup does not look like what it
 *              expects, the buffer is returned untouched.
 *
 * Author: Claude (ALEA redesign)
 */

defined( 'ABSPATH' ) || exit;

/** The one correct WhatsApp number. Kept in step with alea/facts.php. */
function alea_repair_wa_number() {
	$facts = get_stylesheet_directory() . '/alea/facts.php';
	if ( file_exists( $facts ) ) {
		require_once $facts;
		if ( function_exists( 'alea_fact' ) ) {
			$n = alea_fact( 'whatsapp' );
			if ( $n ) {
				return $n;
			}
		}
	}
	return '919554995449';
}

/**
 * Repair malformed wa.me links: strip '+', spaces, hyphens and brackets from
 * the number part. Leaves already-correct links alone.
 */
function alea_repair_whatsapp( $html ) {
	if ( false === stripos( $html, 'wa.me/' ) ) {
		return $html;
	}
	$good = alea_repair_wa_number();
	$out  = preg_replace_callback(
		'#(https?://(?:api\.)?wa\.me/)([^"\'<>]+)#i',
		function ( $m ) use ( $good ) {
			$rest  = $m[2];
			$query = '';
			$qpos  = strpos( $rest, '?' );
			if ( false !== $qpos ) {
				$query = substr( $rest, $qpos );
				$rest  = substr( $rest, 0, $qpos );
			}
			$digits = preg_replace( '/\D+/', '', rawurldecode( $rest ) );
			if ( '' === $digits ) {
				return $m[0]; // nothing recognisable: leave it
			}
			// Only rewrite when it is actually malformed.
			if ( $digits === $rest ) {
				return $m[0];
			}
			return $m[1] . $good . $query;
		},
		$html
	);
	return is_string( $out ) ? $out : $html;
}

/**
 * Drop JSON-LD nodes asserting things the business has not verified:
 * any aggregateRating/review, and any premises whose telephone is not the
 * published number (that is what identifies the fabricated second address).
 */
function alea_repair_jsonld( $html ) {
	if ( false === stripos( $html, 'application/ld+json' ) ) {
		return $html;
	}
	$phone = alea_repair_wa_number();

	$keep = function ( $node ) use ( $phone ) {
		if ( ! is_array( $node ) ) {
			return true;
		}
		if ( isset( $node['aggregateRating'] ) || isset( $node['review'] ) || isset( $node['reviewRating'] ) ) {
			return false;
		}
		if ( isset( $node['telephone'] ) && is_string( $node['telephone'] ) ) {
			$d = preg_replace( '/\D+/', '', $node['telephone'] );
			if ( '' !== $d && ltrim( $d, '0' ) !== $phone ) {
				return false;
			}
		}
		return true;
	};

	$out = preg_replace_callback(
		'#(<script[^>]*type=["\']application/ld\+json["\'][^>]*>)(.*?)(</script>)#is',
		function ( $m ) use ( $keep ) {
			$data = json_decode( trim( $m[2] ), true );
			if ( null === $data || ! is_array( $data ) ) {
				return $m[0]; // unparseable: pass through byte-for-byte
			}
			$changed = false;

			$filter_list = function ( $list ) use ( $keep, &$changed ) {
				$outl = array();
				foreach ( $list as $n ) {
					if ( $keep( $n ) ) {
						$outl[] = $n;
					} else {
						$changed = true;
					}
				}
				return $outl;
			};

			if ( isset( $data['@graph'] ) && is_array( $data['@graph'] ) ) {
				$data['@graph'] = $filter_list( $data['@graph'] );
				if ( ! $changed ) {
					return $m[0];
				}
				if ( empty( $data['@graph'] ) ) {
					return '';
				}
			} elseif ( isset( $data[0] ) ) {
				$data = $filter_list( $data );
				if ( ! $changed ) {
					return $m[0];
				}
				if ( empty( $data ) ) {
					return '';
				}
			} else {
				if ( $keep( $data ) ) {
					return $m[0];
				}
				return '';
			}

			return $m[1] . wp_json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . $m[3];
		},
		$html
	);
	return is_string( $out ) ? $out : $html;
}

/* Buffer the whole response on the front end and repair on the way out. */
add_action( 'template_redirect', function () {
	if ( is_admin() || is_feed() || is_embed() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
		return;
	}
	if ( isset( $_GET['elementor-preview'] ) || isset( $_GET['alea-norepair'] ) ) {
		return; // never touch the editor; ?alea-norepair=1 to inspect raw output
	}
	ob_start( function ( $html ) {
		if ( ! is_string( $html ) || strlen( $html ) < 200 ) {
			return $html;
		}
		$html = alea_repair_whatsapp( $html );
		$html = alea_repair_jsonld( $html );
		return $html;
	} );
}, 1 );
