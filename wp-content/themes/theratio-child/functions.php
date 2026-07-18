<?php
/**
 * ALEA Modular — child theme functions
 * Added by Claude (growth-plan fixes). Each block is self-contained and guarded.
 * Safe to remove any single block. Nothing here overrides parent-theme behaviour.
 */

defined( 'ABSPATH' ) || exit;

/* =========================================================================
 * 1) LEAD CONVERSION TRACKING  (growth plan: "fire a real Lead event")
 * Fires Meta Pixel `Lead` + GA4/dataLayer `generate_lead` the moment any
 * Contact Form 7 form is submitted successfully — independent of the
 * thank-you-page redirect (which can fail). This is what lets Facebook/Google
 * optimise ads toward real leads and build retargeting/lookalike audiences.
 * ========================================================================= */
add_action( 'wp_footer', function () {
	if ( is_admin() ) {
		return;
	}
	?>
<script id="alea-cf7-lead-tracking">
document.addEventListener('wpcf7mailsent', function (e) {
	try {
		var formId = e && e.detail ? e.detail.contactFormId : '';
		// Meta (Facebook) Pixel — standard Lead event
		if (typeof fbq === 'function') {
			fbq('track', 'Lead', { content_name: 'CF7 form ' + formId });
		}
		// GA4 / Google Tag Manager dataLayer
		window.dataLayer = window.dataLayer || [];
		window.dataLayer.push({ event: 'generate_lead', form_id: formId, form_source: 'contact-form-7' });
	} catch (err) { /* never block the form */ }
}, false);
</script>
	<?php
}, 99 );

/* =========================================================================
 * 2) PERFORMANCE — strip legacy junk resource hints
 * The site emits ~45 dns-prefetch/preconnect hints to third parties it never
 * loads (Disqus, BuySellAds, Pinterest, Hotjar, AddThis, DoubleClick, etc.),
 * forcing pointless DNS lookups on every page. Keep only fonts + real analytics.
 * ========================================================================= */
add_filter( 'wp_resource_hints', function ( $hints, $relation_type ) {
	if ( empty( $hints ) || ! is_array( $hints ) ) {
		return $hints;
	}
	// Hosts we actually use — everything else in dns-prefetch/preconnect is dropped.
	$allow = array(
		'fonts.googleapis.com', 'fonts.gstatic.com',
		'www.googletagmanager.com', 'www.google-analytics.com', 'connect.facebook.net',
		'www.google.com', 'www.gstatic.com', // reCAPTCHA
		's.w.org',
	);
	if ( in_array( $relation_type, array( 'dns-prefetch', 'preconnect' ), true ) ) {
		$hints = array_filter( $hints, function ( $hint ) use ( $allow ) {
			$url = is_array( $hint ) && isset( $hint['href'] ) ? $hint['href'] : $hint;
			if ( ! is_string( $url ) ) {
				return true;
			}
			$host = wp_parse_url( $url, PHP_URL_HOST );
			if ( ! $host ) {
				return true; // keep relative/odd entries untouched
			}
			foreach ( $allow as $ok ) {
				if ( $host === $ok || substr( $host, -strlen( '.' . $ok ) ) === '.' . $ok ) {
					return true;
				}
			}
			return false;
		} );
	}
	return $hints;
}, 99, 2 );

/* =========================================================================
 * 3) PERFORMANCE — force font-display:swap on Google Fonts
 * Eliminates invisible-text delay so headlines + the "Get a Free Quote" CTA
 * paint immediately (better LCP / perceived speed).
 * ========================================================================= */
add_filter( 'style_loader_src', function ( $src ) {
	if ( is_string( $src ) && strpos( $src, 'fonts.googleapis.com' ) !== false && strpos( $src, 'display=' ) === false ) {
		$src = add_query_arg( 'display', 'swap', $src );
	}
	return $src;
}, 10, 1 );

/* =========================================================================
 * 4) LOCAL SEO — LocalBusiness structured data
 * Gives Google the location signal it currently lacks so the site can rank for
 * "modular kitchen in / near me". NAP/geo below are best-known values — confirm
 * against the Google Business Profile before relying on them. Rating/reviews are
 * intentionally NOT injected here (Google requires those to reflect on-page
 * review content).
 * ========================================================================= */
add_action( 'wp_head', function () {
	if ( is_admin() ) {
		return;
	}
	$data = array(
		'@context'    => 'https://schema.org',
		'@type'       => array( 'HomeGoodsStore', 'LocalBusiness' ),
		'@id'         => home_url( '/#localbusiness' ),
		'name'        => 'ALEA Modular Kitchen & Wardrobe',
		'url'         => home_url( '/' ),
		'telephone'   => '+91-95549-95449',
		'email'       => 'info@aleamodular.com',
		'image'       => home_url( '/wp-content/uploads/2022/04/Logo.svg' ),
		'priceRange'  => '₹₹',
		'address'     => array(
			'@type'           => 'PostalAddress',
			'addressLocality' => 'Panchkula',
			'addressRegion'   => 'Haryana',
			'addressCountry'  => 'IN',
		),
		'areaServed'  => array( 'Panchkula', 'Chandigarh', 'Mohali', 'Zirakpur' ),
		'makesOffer'  => array(
			array( '@type' => 'Offer', 'itemOffered' => array( '@type' => 'Product', 'name' => 'Modular Kitchen' ) ),
			array( '@type' => 'Offer', 'itemOffered' => array( '@type' => 'Product', 'name' => 'Modular Wardrobe' ) ),
		),
	);
	echo "\n<script type=\"application/ld+json\" id=\"alea-localbusiness-schema\">"
		. wp_json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE )
		. "</script>\n";
}, 20 );

/* =========================================================================
 * 5) WHATSAPP — pre-fill a message on every WhatsApp link
 * The site already has WhatsApp buttons; this makes each one open a chat
 * pre-filled with an enquiry, so the visitor's intent is captured instantly
 * and the sales team knows why they messaged.
 * ========================================================================= */
add_action( 'wp_footer', function () {
	if ( is_admin() ) {
		return;
	}
	?>
<script id="alea-whatsapp-prefill">
document.addEventListener('DOMContentLoaded', function () {
	var text = "Hi ALEA, I'd like a free modular kitchen estimate.";
	var sel = 'a[href*="wa.me/"],a[href*="api.whatsapp.com/send"],a[href*="web.whatsapp.com/send"]';
	document.querySelectorAll(sel).forEach(function (a) {
		try {
			var u = new URL(a.href, window.location.origin);
			if (!u.searchParams.get('text')) {
				u.searchParams.set('text', text);
				a.setAttribute('href', u.toString());
			}
		} catch (e) { /* leave link untouched */ }
	});
});
</script>
	<?php
}, 100 );

/* =========================================================================
 * 6) TRUST BAR — reusable shortcode  [alea_trust_bar]
 * Drop this shortcode into Elementor (ideally just under the hero) to show a
 * compact strip of the proof points ALEA already earned but currently buries.
 * Styling is scoped so it won't collide with theme CSS.
 * ========================================================================= */
add_shortcode( 'alea_trust_bar', function () {
	$items = array(
		'4.6★ Rated',
		'Since 1998',
		'95,000 sq ft own factory',
		'100% termite &amp; water-proof',
		'99.9% on-time delivery',
	);
	$html  = '<div class="alea-trust-bar" role="list">';
	foreach ( $items as $it ) {
		$html .= '<span class="alea-trust-item" role="listitem">' . $it . '</span>';
	}
	$html .= '</div>';
	return $html;
} );

add_action( 'wp_head', function () {
	?>
<style id="alea-trust-bar-css">
.alea-trust-bar{display:flex;flex-wrap:wrap;justify-content:center;align-items:center;gap:10px 26px;
	padding:14px 18px;background:#111;border-radius:8px;margin:0 auto;max-width:1100px}
.alea-trust-item{color:#fff;font-size:14px;font-weight:600;letter-spacing:.2px;white-space:nowrap;
	position:relative;line-height:1.2}
.alea-trust-item:not(:last-child)::after{content:"";position:absolute;right:-14px;top:10%;height:80%;
	width:1px;background:rgba(177,201,0,.5)}
.alea-trust-item{color:#b1c900}
@media(max-width:600px){.alea-trust-bar{gap:8px 16px;padding:10px}.alea-trust-item{font-size:12px}
	.alea-trust-item:not(:last-child)::after{right:-8px}}
</style>
	<?php
}, 21 );
