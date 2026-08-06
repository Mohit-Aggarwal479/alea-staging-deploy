<?php
/**
 * ALEA redesign shell.
 * Renders a redesigned page inside the theme's existing header/footer,
 * bypassing Elementor. Selected by the router in functions.php (block 11).
 *
 * The shell also owns everything the redesign has to correct in markup it does
 * not author — the parent theme's header/footer chrome and the SEO plugin's
 * <head>. Those live in the database, not in this repo, so they cannot be
 * fixed at source from here; get_header() / get_footer() are buffered and the
 * known-wrong fragments are repaired on the way out. Each repair is narrow,
 * fails open (the untouched buffer is returned if the markup does not match
 * what it expects), and is described where it is defined.
 *
 * Scope: the mapped redesign routes only. Legacy Elementor pages are untouched.
 */

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/facts.php';
require_once __DIR__ . '/images.php';

/* =====================================================================
 * 1. The parent theme's .page-header banner — second <h1> on the page.
 *
 * theratio prints its own <h1 class="page-title"> banner above <main> for
 * every route backed by a real WP post, so 13 redesigned URLs served two
 * h1s: the theme's ("About") and the redesign hero's. Routes with no
 * backing post never showed it, which is why the other pages are clean.
 * Removing it makes all 27 consistent and leaves the hero h1 as the only
 * one. Matched by balancing <div>s rather than by a greedy regex, so a
 * banner containing extra markup cannot swallow the rest of the document;
 * an unbalanced document is returned untouched.
 * ===================================================================== */
if ( ! function_exists( 'alea_shell_strip_page_header' ) ) {
	function alea_shell_strip_page_header( $html ) {
		$pos = stripos( $html, '<div class="page-header' );
		if ( false === $pos ) {
			return $html;
		}
		if ( ! preg_match_all( '#<div\b|</div\s*>#i', $html, $m, PREG_OFFSET_CAPTURE, $pos ) ) {
			return $html;
		}
		$depth = 0;
		foreach ( $m[0] as $tok ) {
			if ( '</' === substr( $tok[0], 0, 2 ) ) {
				$depth--;
				if ( 0 === $depth ) {
					$end = $tok[1] + strlen( $tok[0] );
					return substr( $html, 0, $pos ) . substr( $html, $end );
				}
			} else {
				$depth++;
			}
		}
		return $html; // unbalanced markup: leave the page exactly as it was
	}
}

/* =====================================================================
 * 2. <head> metadata — one description, one social card, both ours.
 *
 * Rank Math populates its tags from the WP post a route happens to resolve
 * to, which for these routes is either a stale pre-redesign page, the wrong
 * page (/modular-kitchen/signature/ resolved to the legacy /signature/
 * post), or nothing at all. The result was duplicate <meta name="description">
 * on 17 URLs (the stale one first, so it won), share cards contradicting the
 * page on 14, and no card whatsoever on 8.
 *
 * The router's map is the only authority for what these URLs say, so every
 * title/description/URL tag is removed and re-emitted from the map entry.
 * og:locale, og:site_name and twitter:card are left alone — they are not
 * page-specific and are not wrong.
 * ===================================================================== */
/** preg_replace that never loses the subject: a PCRE failure returns null. */
if ( ! function_exists( 'alea_shell_replace' ) ) {
	function alea_shell_replace( $pattern, $replacement, $subject ) {
		$out = preg_replace( $pattern, $replacement, $subject );
		return is_string( $out ) ? $out : $subject;
	}
}

if ( ! function_exists( 'alea_shell_head_meta' ) ) {
	function alea_shell_head_meta( $html, $entry ) {
		$close = stripos( $html, '</head>' );
		if ( false === $close || empty( $entry['title'] ) ) {
			return $html; // not a document we recognise: change nothing
		}

		$self  = home_url( $entry['path'] );
		$title = $entry['title'];
		$desc  = isset( $entry['desc'] ) ? $entry['desc'] : '';
		$img   = alea_img_src( 'kitchen-wide' ); // verified ALEA experience-centre photograph

		$head = substr( $html, 0, $close );
		$rest = substr( $html, $close );

		$head = alea_shell_replace( '#<meta[^>]*\bname=["\']description["\'][^>]*>#i', '', $head );
		$head = alea_shell_replace(
			'#<meta[^>]*\b(?:property|name)=["\'](?:og:title|og:description|og:url|og:type|og:image(?::[a-z_]+)?|twitter:title|twitter:description|twitter:image(?::[a-z_]+)?)["\'][^>]*>#i',
			'',
			$head
		);
		$head = alea_shell_replace( '#<link[^>]*\brel=["\']canonical["\'][^>]*>#i', '', $head );

		$out  = "\n<!-- ALEA redesign: canonical head metadata, from alea_redesign_map() -->\n";
		$out .= '<link rel="canonical" href="' . esc_url( $self ) . '">' . "\n";
		if ( '' !== $desc ) {
			$out .= '<meta name="description" content="' . esc_attr( $desc ) . '">' . "\n";
		}
		$out .= '<meta property="og:type" content="website">' . "\n";
		$out .= '<meta property="og:url" content="' . esc_url( $self ) . '">' . "\n";
		$out .= '<meta property="og:title" content="' . esc_attr( $title ) . '">' . "\n";
		$out .= '<meta name="twitter:title" content="' . esc_attr( $title ) . '">' . "\n";
		if ( '' !== $desc ) {
			$out .= '<meta property="og:description" content="' . esc_attr( $desc ) . '">' . "\n";
			$out .= '<meta name="twitter:description" content="' . esc_attr( $desc ) . '">' . "\n";
		}
		if ( '' !== $img ) {
			$out .= '<meta property="og:image" content="' . esc_url( $img ) . '">' . "\n";
			$out .= '<meta name="twitter:image" content="' . esc_url( $img ) . '">' . "\n";
		}

		return $head . $out . $rest;
	}
}

/* =====================================================================
 * 3. JSON-LD — drop nodes that assert things facts.php does not hold.
 *
 * Page-level schema stored in the database shipped, on top of the
 * redesign's own LocalBusiness node: a 4.6/179 aggregateRating and two
 * named-customer reviews on /locations/panchkula/ (facts.php records
 * google_rating as unverified and the page copy disowns exactly this), a
 * second premises with a street address and opening hours on
 * /locations/mohali/ (facts.php holds one facility, Raipur Rani, and no
 * working hours), and a breadcrumb trail for /signature/ on
 * /modular-kitchen/signature/.
 *
 * Three rules, all of them "delete", never "invent":
 *   - a node carrying aggregateRating or review is dropped;
 *   - a node whose telephone is not the published number is dropped
 *     (that is what identifies both fabricated premises);
 *   - a BreadcrumbList whose @id belongs to another URL is dropped.
 * A block that will not parse, or that nothing matched, is passed through
 * byte-for-byte.
 * ===================================================================== */
if ( ! function_exists( 'alea_shell_node_is_ours' ) ) {
	function alea_shell_node_is_ours( $node, $self ) {
		if ( ! is_array( $node ) ) {
			return true;
		}
		if ( isset( $node['aggregateRating'] ) || isset( $node['review'] ) || isset( $node['reviewRating'] ) ) {
			return false;
		}
		if ( isset( $node['telephone'] ) && is_string( $node['telephone'] ) ) {
			$digits = (string) preg_replace( '/\D+/', '', $node['telephone'] );
			if ( '' !== $digits && ltrim( $digits, '0' ) !== alea_fact( 'whatsapp' ) ) {
				return false;
			}
		}
		$types = isset( $node['@type'] ) ? (array) $node['@type'] : array();
		if ( in_array( 'BreadcrumbList', $types, true ) && ! empty( $node['@id'] ) && is_string( $node['@id'] ) ) {
			if ( 0 !== strpos( $node['@id'], $self ) ) {
				return false;
			}
		}
		return true;
	}
}

if ( ! function_exists( 'alea_shell_clean_jsonld' ) ) {
	function alea_shell_clean_jsonld( $html, $self ) {
		$out = preg_replace_callback(
			'#(<script[^>]*type=["\']application/ld\+json["\'][^>]*>)(.*?)(</script>)#is',
			function ( $m ) use ( $self ) {
				$data = json_decode( $m[2], true );
				if ( ! is_array( $data ) ) {
					return $m[0]; // not JSON we understand: leave it
				}

				$changed = false;
				$filter  = function ( $list ) use ( $self, &$changed ) {
					$kept = array();
					foreach ( $list as $node ) {
						if ( alea_shell_node_is_ours( $node, $self ) ) {
							$kept[] = $node;
						} else {
							$changed = true;
						}
					}
					return $kept;
				};

				if ( isset( $data['@graph'] ) && is_array( $data['@graph'] ) ) {
					$data['@graph'] = array_values( $filter( $data['@graph'] ) );
					if ( ! $data['@graph'] ) {
						return '';
					}
				} elseif ( isset( $data[0] ) ) {
					$data = array_values( $filter( $data ) );
					if ( ! $data ) {
						return '';
					}
				} elseif ( ! alea_shell_node_is_ours( $data, $self ) ) {
					return '';
				}

				if ( ! $changed ) {
					return $m[0]; // nothing removed: hand back the original bytes
				}
				return $m[1] . wp_json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . $m[3];
			},
			$html
		);
		return is_string( $out ) ? $out : $html;
	}
}

/* =====================================================================
 * 4. WhatsApp links in theme/Elementor chrome.
 *
 * The floating Elementor "Contact Buttons" widget stores the number with a
 * leading + and spaces, producing href="https://wa.me/+91 95549 95449",
 * which WhatsApp resolves to a not-found page — the site's most prominent
 * WhatsApp CTA, dead on every page. Normalised here to the digits-only
 * form facts.php holds. Only numeric wa.me paths are rewritten, so a
 * wa.me/message/<code> short link is left alone, and the query string
 * (the pre-filled message) is preserved.
 * ===================================================================== */
if ( ! function_exists( 'alea_shell_fix_whatsapp' ) ) {
	function alea_shell_fix_whatsapp( $html ) {
		$out = preg_replace_callback(
			'#href=(["\'])(https?://wa\.me/[^"\']*)\1#i',
			function ( $m ) {
				$url  = $m[2];
				$tail = '';
				$pos  = strcspn( $url, '?#' );
				if ( $pos < strlen( $url ) ) {
					$tail = substr( $url, $pos );
					$url  = substr( $url, 0, $pos );
				}
				if ( ! preg_match( '#^https?://wa\.me/(.+)$#i', $url, $p ) ) {
					return $m[0];
				}
				$path = rawurldecode( $p[1] );
				if ( preg_match( '/[a-z]/i', $path ) ) {
					return $m[0]; // named short link, not a phone number
				}
				$digits = (string) preg_replace( '/\D+/', '', $path );
				if ( strlen( $digits ) < 10 ) {
					return $m[0];
				}
				return 'href=' . $m[1] . esc_url( 'https://wa.me/' . $digits . $tail ) . $m[1];
			},
			$html
		);
		return is_string( $out ) ? $out : $html;
	}
}

/* =====================================================================
 * 5. Accessible names for the theme's icon-only chrome.
 *
 * The header and footer ship five controls whose only content is an icon
 * font <i> — no text, no aria-label, no title — including the mobile menu
 * toggle, which is the only route to navigation on a phone. Labels are
 * added, never replaced: any control that already has an accessible name
 * is skipped. The Elementor icon links are labelled from their own href,
 * so no name is invented for a destination we cannot read.
 * ===================================================================== */
if ( ! function_exists( 'alea_shell_label_icons' ) ) {
	function alea_shell_label_icons( $html ) {
		/* Mobile menu toggle: <button><i class="ot-flaticon-menu"></i></button> */
		$html = alea_shell_replace(
			'#<button(?![^>]*aria-label)([^>]*)>(\s*<i[^>]*ot-flaticon-menu)#i',
			'<button aria-label="Open menu"$1>$2',
			$html
		);

		$named = array(
			'mmenu-close'      => 'Close menu',
			'side-panel-close' => 'Close panel',
		);
		foreach ( $named as $class => $label ) {
			$html = alea_shell_replace(
				'#<a(?![^>]*aria-label)([^>]*\bclass=["\'][^"\']*\b' . preg_quote( $class, '#' ) . '\b[^"\']*["\'][^>]*)>#i',
				'<a aria-label="' . esc_attr( $label ) . '"$1>',
				$html
			);
		}

		$html = alea_shell_replace(
			'#<a(?![^>]*aria-label)([^>]*\bid=["\']back-to-top["\'][^>]*)>#i',
			'<a aria-label="Back to top"$1>',
			$html
		);

		/* Elementor icon-only nav links: name each one from where it goes. */
		$html = preg_replace_callback(
			'#<a\b[^>]*\bclass=["\'][^"\']*\belementor-icon\b[^"\']*["\'][^>]*>#i',
			function ( $m ) {
				if ( preg_match( '#\baria-label=#i', $m[0] ) ) {
					return $m[0];
				}
				if ( ! preg_match( '#\bhref=["\']([^"\']*)["\']#i', $m[0], $h ) ) {
					return $m[0];
				}
				$path  = (string) wp_parse_url( $h[1], PHP_URL_PATH );
				$label = '';
				if ( 0 === strpos( $path, '/locations' ) ) {
					$label = 'Locations';
				} elseif ( 0 === strpos( $path, '/wardrobe' ) ) {
					$label = 'Wardrobes';
				} elseif ( 0 === strpos( $path, '/modular-kitchen' ) ) {
					$label = 'Modular kitchens';
				} elseif ( '' === $path || '/' === $path ) {
					$label = 'Home';
				}
				if ( '' === $label ) {
					return $m[0]; // unknown destination: do not invent a name
				}
				return substr( $m[0], 0, 2 ) . ' aria-label="' . esc_attr( $label ) . '"' . substr( $m[0], 2 );
			},
			$html
		);

		return is_string( $html ) ? $html : '';
	}
}

/* ---------------------------------------------------------------------
 * Render.
 * ------------------------------------------------------------------ */
$alea_entry = function_exists( 'alea_redesign_entry' ) ? alea_redesign_entry() : null;
$alea_self  = ( $alea_entry && ! empty( $alea_entry['path'] ) ) ? home_url( $alea_entry['path'] ) : home_url( '/' );

ob_start();
get_header();
$alea_header = (string) ob_get_clean();

$alea_header = alea_shell_strip_page_header( $alea_header );
if ( $alea_entry ) {
	$alea_header = alea_shell_head_meta( $alea_header, $alea_entry );
	$alea_header = alea_shell_clean_jsonld( $alea_header, $alea_self );
}
$alea_header = alea_shell_label_icons( $alea_header );
$alea_header = alea_shell_fix_whatsapp( $alea_header );

echo $alea_header; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- theme output, repaired above; inserted values escaped at source.

$alea_page_file = isset( $GLOBALS['alea_page_file'] ) ? $GLOBALS['alea_page_file'] : '';
$alea_dir       = get_stylesheet_directory() . '/alea/';

// Only ever include a file from inside alea/.
if ( $alea_page_file && 0 === strpos( realpath( $alea_page_file ), realpath( $alea_dir ) ) && file_exists( $alea_page_file ) ) {
	echo '<main class="alea-main" id="alea-main">';
	include $alea_page_file;
	echo '</main>';
} else {
	echo '<main class="alea-main"><div style="padding:80px 22px;text-align:center">Page template not found.</div></main>';
}

ob_start();
get_footer();
$alea_footer = (string) ob_get_clean();

$alea_footer = alea_shell_label_icons( $alea_footer );
$alea_footer = alea_shell_fix_whatsapp( $alea_footer );

echo $alea_footer; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- theme output, repaired above; inserted values escaped at source.
