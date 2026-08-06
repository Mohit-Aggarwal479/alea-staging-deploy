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
/**
 * The balanced span of one element, given the offset of its opening '<'.
 *
 * Every repair below that has to CUT markup uses this rather than a greedy or
 * lazy regex: a lazy `<div ...>.*?</div>` stops at the first inner close and
 * leaves a torn document, and a greedy one swallows the rest of the page. This
 * counts opens and closes instead, and returns false for anything unbalanced —
 * which is what makes every caller fail open.
 *
 * @param string $html the buffer.
 * @param string $tag  element name, e.g. 'div' or 'li'.
 * @param int    $pos  offset of the opening '<'.
 * @return array{0:int,1:int}|false start and end-exclusive offsets.
 */
if ( ! function_exists( 'alea_shell_element_span' ) ) {
	function alea_shell_element_span( $html, $tag, $pos ) {
		$re = '#<' . preg_quote( $tag, '#' ) . '\b|</' . preg_quote( $tag, '#' ) . '\s*>#i';
		if ( ! preg_match_all( $re, $html, $m, PREG_OFFSET_CAPTURE, $pos ) ) {
			return false;
		}
		$depth = 0;
		foreach ( $m[0] as $tok ) {
			if ( '</' === substr( $tok[0], 0, 2 ) ) {
				$depth--;
				if ( 0 === $depth ) {
					return array( $pos, $tok[1] + strlen( $tok[0] ) );
				}
			} else {
				$depth++;
			}
		}
		return false; // unbalanced markup
	}
}

if ( ! function_exists( 'alea_shell_strip_page_header' ) ) {
	function alea_shell_strip_page_header( $html ) {
		$pos = stripos( $html, '<div class="page-header' );
		if ( false === $pos ) {
			return $html;
		}
		$span = alea_shell_element_span( $html, 'div', $pos );
		if ( ! $span ) {
			return $html; // unbalanced markup: leave the page exactly as it was
		}
		return substr( $html, 0, $span[0] ) . substr( $html, $span[1] );
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
 *
 * Two families of tag are stripped without a replacement because nothing here
 * can state them truthfully:
 *   - article:* (published/modified time, publisher) contradicted the og:type
 *     we emit — these are website pages, not articles — and the dates were the
 *     legacy post's, not this page's. og:updated_time was stale in the same way.
 *   - the og:image:* the plugin emitted described whatever image it had picked.
 *     Ours are re-emitted from the catalogue entry instead (images.php holds the
 *     pixel size and the verified alt), so a share card can never announce a
 *     size or a description that does not match the file it points at.
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

		$self    = home_url( $entry['path'] );
		$title   = $entry['title'];
		$desc    = isset( $entry['desc'] ) ? $entry['desc'] : '';
		$img_key = 'kitchen-wide'; // verified ALEA experience-centre photograph
		$img     = alea_img_src( $img_key );

		$head = substr( $html, 0, $close );
		$rest = substr( $html, $close );

		$head = alea_shell_replace( '#<meta[^>]*\bname=["\']description["\'][^>]*>#i', '', $head );
		$head = alea_shell_replace(
			'#<meta[^>]*\b(?:property|name)=["\'](?:og:title|og:description|og:url|og:type|og:updated_time|og:image(?::[a-z_]+)?|article:[a-z_]+|twitter:title|twitter:description|twitter:image(?::[a-z_]+)?)["\'][^>]*>#i',
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
			$dims = alea_img_dims( $img_key );
			$ialt = alea_img_alt( $img_key );
			$out .= '<meta property="og:image" content="' . esc_url( $img ) . '">' . "\n";
			if ( $dims ) {
				$out .= '<meta property="og:image:width" content="' . (int) $dims[0] . '">' . "\n";
				$out .= '<meta property="og:image:height" content="' . (int) $dims[1] . '">' . "\n";
			}
			if ( '' !== $ialt ) {
				$out .= '<meta property="og:image:alt" content="' . esc_attr( $ialt ) . '">' . "\n";
			}
			$out .= '<meta name="twitter:image" content="' . esc_url( $img ) . '">' . "\n";
			if ( '' !== $ialt ) {
				$out .= '<meta name="twitter:image:alt" content="' . esc_attr( $ialt ) . '">' . "\n";
			}
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
 * The rules are "delete", never "invent":
 *   - a node carrying aggregateRating or review is dropped;
 *   - a node whose telephone is not the published number is dropped
 *     (that is what identifies both fabricated premises);
 *   - a BreadcrumbList is dropped unless its trail actually ends at this URL.
 *     Checking the node @id alone was not enough: /modular-kitchen/signature/
 *     shipped a correctly-@id'd trail whose items both pointed at the legacy
 *     /signature/ post, and eight other routes shipped a trail containing
 *     nothing but Home. A breadcrumb that does not end at the page it is on
 *     describes a different page.
 *
 * One rule rewrites rather than deletes. Rank Math builds its WebPage/Article
 * node from whatever post the route resolved to, so name/headline/description
 * were the stale pre-redesign post's while <title> and og:title (repaired in
 * step 2) carry the map's — every such page shipped two contradicting titles,
 * and /about/'s node contradicted itself. Those keys are re-stated from the
 * same map entry step 2 uses, so there is one title per page and it cannot
 * drift. Only keys the node already has are rewritten; none is added.
 *
 * A block that will not parse, or that nothing matched, is passed through
 * byte-for-byte.
 * ===================================================================== */
/** The URL a BreadcrumbList ListItem points at, whichever shape it uses. */
if ( ! function_exists( 'alea_shell_listitem_url' ) ) {
	function alea_shell_listitem_url( $item ) {
		if ( is_string( $item ) ) {
			return $item;
		}
		if ( ! is_array( $item ) ) {
			return '';
		}
		if ( isset( $item['item'] ) ) {
			return alea_shell_listitem_url( $item['item'] );
		}
		foreach ( array( '@id', 'url' ) as $k ) {
			if ( ! empty( $item[ $k ] ) && is_string( $item[ $k ] ) ) {
				return $item[ $k ];
			}
		}
		return '';
	}
}

/**
 * Does this trail end at the page it is on? Fails open: a list we cannot read
 * (no items, or a last item with no URL) is left for the caller to keep.
 */
if ( ! function_exists( 'alea_shell_breadcrumb_ends_here' ) ) {
	function alea_shell_breadcrumb_ends_here( $node, $self ) {
		if ( empty( $node['itemListElement'] ) || ! is_array( $node['itemListElement'] ) ) {
			return true;
		}
		$items = array_values( $node['itemListElement'] );
		$last  = null;
		$best  = null;
		foreach ( $items as $item ) {
			$pos = ( is_array( $item ) && isset( $item['position'] ) && is_numeric( $item['position'] ) ) ? (int) $item['position'] : null;
			if ( null === $pos ) {
				$best = null;
				break; // positions are not usable: fall back to document order
			}
			if ( null === $last || $pos > $last ) {
				$last = $pos;
				$best = $item;
			}
		}
		if ( null === $best ) {
			$best = end( $items );
		}
		$url = alea_shell_listitem_url( $best );
		if ( '' === $url ) {
			return true;
		}
		return rtrim( $url, '/' ) === rtrim( $self, '/' );
	}
}

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
		if ( in_array( 'BreadcrumbList', $types, true ) ) {
			if ( ! empty( $node['@id'] ) && is_string( $node['@id'] ) && 0 !== strpos( $node['@id'], $self ) ) {
				return false;
			}
			if ( ! alea_shell_breadcrumb_ends_here( $node, $self ) ) {
				return false;
			}
		}
		/* A second, DB-stored WebSite node on the homepage duplicated the
		   '#website' entity the SEO plugin already declares, and its sitelinks
		   SearchAction was broken: the target read '?s=search_term' with no
		   {search_term_string} placeholder, so the URL template can never be
		   filled and the action can never fire. The test is the defect itself —
		   a SearchAction whose target carries no placeholder — so the valid
		   node, whoever emits it, is never the one dropped. */
		if ( in_array( 'WebSite', $types, true ) && isset( $node['potentialAction'] ) ) {
			$actions = $node['potentialAction'];
			$actions = ( isset( $actions['@type'] ) || ! is_array( $actions ) ) ? array( $actions ) : $actions;
			foreach ( (array) $actions as $action ) {
				if ( ! is_array( $action ) ) {
					continue;
				}
				$a_types = isset( $action['@type'] ) ? (array) $action['@type'] : array();
				if ( ! in_array( 'SearchAction', $a_types, true ) ) {
					continue;
				}
				if ( isset( $action['target'] ) && is_string( $action['target'] ) && false === strpos( $action['target'], '{' ) ) {
					return false;
				}
			}
		}
		return true;
	}
}

/**
 * Re-state a document node's title from the router map, so the page's schema
 * cannot say something its <title> and og:title do not. Rewrites only keys the
 * node already carries — an absent name is left absent, never invented.
 */
if ( ! function_exists( 'alea_shell_node_titles' ) ) {
	function alea_shell_node_titles( $node, $entry ) {
		if ( ! is_array( $node ) || empty( $entry['title'] ) ) {
			return $node;
		}
		$is_doc = false;
		foreach ( ( isset( $node['@type'] ) ? (array) $node['@type'] : array() ) as $type ) {
			// WebPage/ItemPage/CollectionPage/…, Article/BlogPosting/NewsArticle/…
			if ( is_string( $type ) && preg_match( '/(?:Page|Article|BlogPosting)$/', $type ) ) {
				$is_doc = true;
				break;
			}
		}
		if ( ! $is_doc ) {
			return $node;
		}
		foreach ( array( 'name', 'headline' ) as $key ) {
			if ( isset( $node[ $key ] ) && is_string( $node[ $key ] ) ) {
				$node[ $key ] = $entry['title'];
			}
		}
		if ( ! empty( $entry['desc'] ) && isset( $node['description'] ) && is_string( $node['description'] ) ) {
			$node['description'] = $entry['desc'];
		}
		return $node;
	}
}

if ( ! function_exists( 'alea_shell_clean_jsonld' ) ) {
	function alea_shell_clean_jsonld( $html, $self, $entry = array() ) {
		$out = preg_replace_callback(
			'#(<script[^>]*type=["\']application/ld\+json["\'][^>]*>)(.*?)(</script>)#is',
			function ( $m ) use ( $self, $entry ) {
				$data = json_decode( $m[2], true );
				if ( ! is_array( $data ) ) {
					return $m[0]; // not JSON we understand: leave it
				}

				$changed = false;
				$filter  = function ( $list ) use ( $self, $entry, &$changed ) {
					$kept = array();
					foreach ( $list as $node ) {
						if ( ! alea_shell_node_is_ours( $node, $self ) ) {
							$changed = true;
							continue;
						}
						$fixed = alea_shell_node_titles( $node, $entry );
						if ( $fixed !== $node ) {
							$changed = true;
						}
						$kept[] = $fixed;
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
				} else {
					$fixed = alea_shell_node_titles( $data, $entry );
					if ( $fixed !== $data ) {
						$changed = true;
						$data    = $fixed;
					}
				}

				if ( ! $changed ) {
					return $m[0]; // nothing dropped or restated: hand back the original bytes
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

/* =====================================================================
 * 6. The footer "Locations" list — 20 cities against a 4-city promise.
 *
 * The shared footer linked 20 cities as ALEA locations (Delhi, Jaipur,
 * Surat, Srinagar, Jammu, Rohtak, Ludhiana, Kullu, Mandi, Palampur,
 * Haridwar, Dehradun, Jalandhar, Patiala, Kaithal, Goraya, Barnala plus
 * three of the four real ones), on every page — including /locations/,
 * whose own body says "those are the ones we can promise", and whose
 * LocalBusiness areaServed lists exactly four. Zirakpur, one of the four
 * actually served, was the one missing. A footer that names Delhi turns
 * that sentence into a lie, so facts.php 'service_area' wins: the list is
 * rebuilt as the four served areas plus one link to /locations/.
 *
 * The list is REBUILT FROM AN EXISTING ITEM rather than written here, so
 * whatever classes, icons and markup the footer widget uses are carried
 * over untouched — nothing about the footer's appearance changes except
 * which places it names. Anything that does not match what this expects is
 * returned unchanged, and a list that does not link /locations/ is never
 * touched at all.
 *
 * OUT OF SCOPE HERE: the 16 legacy out-of-area city PAGES are still live.
 * Unlinking them stops the footer promising coverage we do not have; the
 * pages themselves are database content and have to be retired or 410'd
 * in WordPress.
 * ===================================================================== */
if ( ! function_exists( 'alea_shell_footer_locations' ) ) {
	function alea_shell_footer_locations( $html ) {
		$areas = (array) alea_fact( 'service_area', array() );
		if ( ! $areas ) {
			return $html;
		}

		$rebuilt_any = false;
		$offset      = 0;
		$guard       = 0;
		while ( $guard++ < 20 && preg_match( '#<ul\b[^>]*elementor-icon-list-items[^>]*>#i', $html, $m, PREG_OFFSET_CAPTURE, $offset ) ) {
			$open = $m[0][1];
			$span = alea_shell_element_span( $html, 'ul', $open );
			if ( ! $span ) {
				break;
			}
			$offset = $span[1];

			$list  = substr( $html, $span[0], $span[1] - $span[0] );
			$inner = substr( $list, strlen( $m[0][0] ), -strlen( '</ul>' ) );
			if ( false !== stripos( $inner, '<ul' ) ) {
				continue; // nested list: the flat-item match below would tear it
			}
			if ( ! preg_match_all( '#<li\b[^>]*>.*?</li\s*>#is', $inner, $items ) || ! $items[0] ) {
				continue;
			}

			/* A place list is any list mostly made of place links, naming at
			   least one place outside the service area. Two URL shapes carry
			   them in this footer: /locations/{city}/ and the standalone
			   landing pages /modular-kitchen(s)-in-{city}. Both must be
			   recognised — matching only the first left a second footer
			   column still advertising Surat, Jaipur and Jalandhar. */
			$located = 0;
			$stray   = false;
			foreach ( $items[0] as $item ) {
				$slug = '';
				if ( preg_match( '#href=["\'][^"\']*/locations/([^"\'/]+)/?["\']#i', $item, $h ) ) {
					$slug = strtolower( $h[1] );
				} elseif ( preg_match( '#href=["\'][^"\']*/modular-kitchens?-in-([^"\'/]+)/?["\']#i', $item, $h ) ) {
					$slug = strtolower( $h[1] );
				} else {
					continue;
				}
				$located++;
				$hit = false;
				foreach ( $areas as $area ) {
					if ( sanitize_title( $area ) === $slug ) {
						$hit = true;
						break;
					}
				}
				if ( ! $hit ) {
					$stray = true;
				}
			}
			if ( $located < 3 || ! $stray || $located < count( $items[0] ) - 1 ) {
				continue;
			}

			/* Rebuild from the first item, so icons and classes survive. */
			$template = $items[0][0];
			$rebuilt  = '';
			$rows     = array();
			foreach ( $areas as $area ) {
				$rows[] = array( home_url( '/locations/' . sanitize_title( $area ) . '/' ), $area );
			}
			$rows[] = array( home_url( '/locations/' ), 'All areas we serve' );

			foreach ( $rows as $row ) {
				$li = alea_shell_replace( '#\bhref=(["\'])[^"\']*\1#i', 'href="' . esc_url( $row[0] ) . '"', $template );
				$done = false;
				$li   = preg_replace_callback(
					'#(<span[^>]*elementor-icon-list-text[^>]*>).*?(</span\s*>)#is',
					function ( $t ) use ( $row, &$done ) {
						$done = true;
						return $t[1] . esc_html( $row[1] ) . $t[2];
					},
					$li,
					1
				);
				if ( ! is_string( $li ) ) {
					return $html; // PCRE failure: keep the footer exactly as it was
				}
				if ( ! $done ) {
					$li = alea_shell_replace( '#(<a\b[^>]*>).*?(</a\s*>)#is', '${1}' . esc_html( $row[1] ) . '${2}', $li );
				}
				$rebuilt .= $li;
			}
			if ( '' === $rebuilt ) {
				continue;
			}

			$new         = $m[0][0] . $rebuilt . '</ul>';
			$html        = substr( $html, 0, $span[0] ) . $new . substr( $html, $span[1] );
			$offset      = $span[0] + strlen( $new );
			$rebuilt_any = true;
		}

		/* The block heading read as a coverage promise, so it is renamed for
		   what the list under it now is — but ONLY if the list was actually
		   rebuilt. Retitling a list we could not cut would put a stronger
		   promise over the same 20 cities. */
		if ( $rebuilt_any ) {
			$html = alea_shell_replace(
				'#(<h[1-6][^>]*>)\s*Locations\s*(</h[1-6]\s*>)#i',
				'${1}Where we install${2}',
				$html
			);
		}

		return $html;
	}
}

/* =====================================================================
 * 7. The footer blurb — four filler phrases in two sentences.
 *
 * "built on the strong foundation of ... a trusted name ... stylish and
 * reliable ... solutions to your home" is the exact register the 33 pages
 * above it spend their whole length refusing, and not one clause of it is
 * checkable. Replaced with the two facts the site already stands on, both
 * composed from facts.php values and worded the way /about/ words them —
 * so no new claim is introduced and the parent firm is described, not
 * re-asserted by a name this repo does not hold as a verified fact.
 * ===================================================================== */
if ( ! function_exists( 'alea_shell_footer_blurb' ) ) {
	function alea_shell_footer_blurb( $html ) {
		$sqft  = alea_fact( 'factory_sqft' );
		$place = alea_fact( 'factory_place' );
		$since = alea_fact( 'founded_furniture' );
		if ( '' === $sqft || '' === $place || '' === $since ) {
			return $html;
		}
		$new = 'ALEA makes modular kitchens and wardrobes in our own ' . $sqft . ' sq ft factory at ' . $place
			. '. The furniture business we grew out of has been making furniture since ' . $since . '.';

		return alea_shell_replace(
			'#Alea\s+Modular\s+Kitchen\s+is\s+built\s+on\s+the\s+strong\s+foundation.*?solutions\s+to\s+your\s+home\.#is',
			esc_html( $new ),
			$html
		);
	}
}

/* =====================================================================
 * 8. The legacy Elementor popup on two city pages.
 *
 * Popup 21796 fired on /locations/panchkula/ and /locations/mohali/ only,
 * promising "3D visuals" — a deliverable no page on this site offers and
 * that /about/customer-process/ deliberately does not list (what the buyer
 * receives there is a signed-off drawing and an itemised quotation). Its
 * lead form also asked a different question set from the one every
 * redesigned page uses, and "personalized" was the single American -ize
 * spelling on a site that otherwise writes centre, colour and itemised.
 * Two of the four city pages made a promise the other two did not.
 *
 * Removed from the redesigned routes only. The dialog wrapper goes with it
 * where there is one, so no empty modal is left for Elementor to open. The
 * popup is database content: this stops it appearing, it does not unpublish
 * it. Nothing is put in its place — the page's own booking form already is.
 * ===================================================================== */
if ( ! function_exists( 'alea_shell_strip_popups' ) ) {
	function alea_shell_strip_popups( $html ) {
		$patterns = array(
			'#<div\b[^>]*\bid=["\']elementor-popup-modal-\d+["\'][^>]*>#i',
			'#<div\b[^>]*\bdata-elementor-type=["\']popup["\'][^>]*>#i',
		);
		foreach ( $patterns as $pattern ) {
			$guard = 0;
			while ( $guard++ < 10 && preg_match( $pattern, $html, $m, PREG_OFFSET_CAPTURE ) ) {
				$span = alea_shell_element_span( $html, 'div', $m[0][1] );
				if ( ! $span ) {
					break; // unbalanced: leave the document alone
				}
				$html = substr( $html, 0, $span[0] ) . substr( $html, $span[1] );
			}
		}
		return $html;
	}
}

/* =====================================================================
 * 9. /our-factory/ in the navigation.
 *
 * sequence.php splits the two factory pages deliberately: /our-factory/ is
 * the PLACE (and the target of the "Book a free factory visit" CTAs on
 * other pages), /about/manufacturing-process/ is the PROCESS. The menu had
 * it backwards — the label "Manufacturing Unit" pointed at the process
 * page, and the place had no menu entry at all, so the one page other
 * pages send people to could not be reached from the navigation.
 *
 * The label follows the page it names: "Manufacturing Unit" moves to
 * /our-factory/ (facts.php calls the building exactly that), and the
 * process page keeps its place under a label that describes it. The entry
 * is CLONED from the existing one so it inherits the menu's own classes
 * and markup. A menu that already links /our-factory/, or an item with a
 * submenu inside it, is left untouched.
 * ===================================================================== */
if ( ! function_exists( 'alea_shell_nav_factory' ) ) {
	function alea_shell_nav_factory( $html ) {
		$process = '/about/manufacturing-process/';
		$factory = home_url( '/our-factory/' );

		$anchor = '#<a\b[^>]*href=["\'][^"\']*' . preg_quote( $process, '#' ) . '["\'][^>]*>#i';
		if ( ! preg_match( $anchor, $html, $m, PREG_OFFSET_CAPTURE ) ) {
			return $html; // menu does not carry the process link: nothing to do
		}
		$at = $m[0][1];

		$li_open = strripos( substr( $html, 0, $at ), '<li' );
		if ( false === $li_open ) {
			return $html;
		}
		$span = alea_shell_element_span( $html, 'li', $li_open );
		if ( ! $span || $span[1] <= $at ) {
			return $html;
		}
		$li = substr( $html, $span[0], $span[1] - $span[0] );
		if ( preg_match( '#<li\b#i', substr( $li, 3 ) ) ) {
			return $html; // has a submenu: not a leaf we can safely clone
		}

		$relabelled = str_ireplace( 'Manufacturing Unit', 'How we make it', $li );

		if ( false !== stripos( $html, '/our-factory/' ) ) {
			// Already linked somewhere in the chrome: relabel only, add nothing.
			return substr( $html, 0, $span[0] ) . $relabelled . substr( $html, $span[1] );
		}

		$clone = alea_shell_replace( '#\bhref=(["\'])[^"\']*\1#i', 'href="' . esc_url( $factory ) . '"', $li );
		/* Strip identifiers that must stay unique to the item we copied. */
		$clone = alea_shell_replace( '#\s\bid=(["\'])[^"\']*\1#i', '', $clone );
		$clone = alea_shell_replace( '#\s\baria-current=(["\'])[^"\']*\1#i', '', $clone );
		$clone = alea_shell_replace( '#\bmenu-item-\d+\b#i', '', $clone );

		/* Rename the visible text only — the first text run inside the anchor. */
		$clone = preg_replace_callback(
			'#(<a\b[^>]*>)(.*?)(</a\s*>)#is',
			function ( $t ) {
				$inner = $t[2];
				if ( preg_match( '#[^<>]*\S[^<>]*#', $inner, $txt ) ) {
					$inner = str_replace( $txt[0], 'Manufacturing Unit', $inner );
				} else {
					$inner = 'Manufacturing Unit';
				}
				return $t[1] . $inner . $t[3];
			},
			$clone,
			1
		);
		if ( ! is_string( $clone ) ) {
			return $html;
		}

		return substr( $html, 0, $span[0] ) . $clone . $relabelled . substr( $html, $span[1] );
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
	$alea_header = alea_shell_clean_jsonld( $alea_header, $alea_self, $alea_entry );
}
$alea_header = alea_shell_label_icons( $alea_header );
$alea_header = alea_shell_fix_whatsapp( $alea_header );
$alea_header = alea_shell_nav_factory( $alea_header );

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
$alea_footer = alea_shell_footer_locations( $alea_footer );
$alea_footer = alea_shell_footer_blurb( $alea_footer );
$alea_footer = alea_shell_strip_popups( $alea_footer );
/* The duplicate WebSite node is emitted after the footer on the homepage,
   so the same JSON-LD cleaner has to see this buffer too. */
$alea_footer = alea_shell_clean_jsonld( $alea_footer, $alea_self, (array) $alea_entry );

echo $alea_footer; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- theme output, repaired above; inserted values escaped at source.
