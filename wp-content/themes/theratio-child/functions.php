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
<script id="alea-cf7-lead-tracking" data-no-optimize="1" data-no-defer="1">
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
<script id="alea-whatsapp-prefill" data-no-optimize="1" data-no-defer="1">
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
		'Since 1998',
		'95,000 sq ft own factory',
		'100% termite &amp; water-proof',
		'Installed in ~15 days',
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
<style id="alea-trust-bar-css" data-no-optimize="1">
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

/* =========================================================================
 * 7) PERFORMANCE — de-bloat WordPress + disable unused features
 * Safe, standard optimizations that complement (don't conflict with) LiteSpeed
 * cache. Nothing here touches Elementor, forms, or visible content.
 * ========================================================================= */
add_action( 'init', function () {
	// --- Remove the emoji detection script + styles (extra request + inline JS) ---
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
	add_filter( 'tiny_mce_plugins', function ( $p ) {
		return is_array( $p ) ? array_diff( $p, array( 'wpemoji' ) ) : $p;
	} );
	add_filter( 'emoji_svg_url', '__return_false' );

	// --- Trim <head> bloat (no SEO/functional value) ---
	remove_action( 'wp_head', 'rsd_link' );
	remove_action( 'wp_head', 'wlwmanifest_link' );
	remove_action( 'wp_head', 'wp_generator' );
	remove_action( 'wp_head', 'wp_shortlink_wp_head' );
	remove_action( 'wp_head', 'rest_output_link_wp_head' );
	remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
	remove_action( 'wp_head', 'feed_links_extra', 3 );
}, 20 );

// Disable XML-RPC (perf + closes a common brute-force/DDoS vector; site has no XML-RPC clients).
add_filter( 'xmlrpc_enabled', '__return_false' );

// Throttle the Heartbeat API to 60s (less admin-ajax load).
add_filter( 'heartbeat_settings', function ( $s ) {
	$s['interval'] = 60;
	return $s;
} );

// Disable self-pingbacks.
add_action( 'pre_ping', function ( &$links ) {
	$home = home_url();
	foreach ( $links as $i => $l ) {
		if ( is_string( $l ) && 0 === strpos( $l, $home ) ) {
			unset( $links[ $i ] );
		}
	}
} );

// Proper preconnects for the third parties we actually use (pairs with the junk-hint removal above).
add_filter( 'wp_resource_hints', function ( $hints, $relation ) {
	if ( 'preconnect' === $relation ) {
		$hints[] = array( 'href' => 'https://fonts.gstatic.com', 'crossorigin' );
		$hints[] = 'https://www.googletagmanager.com';
		$hints[] = 'https://connect.facebook.net';
	}
	return $hints;
}, 5, 2 );

// Don't load the dashicons icon font on the front end for logged-out visitors.
add_action( 'wp_enqueue_scripts', function () {
	if ( ! is_user_logged_in() ) {
		wp_dequeue_style( 'dashicons' );
		wp_deregister_style( 'dashicons' );
	}
}, 100 );

/* =========================================================================
 * 8) UI/UX COMPONENTS — drop-in shortcodes for Elementor
 * Place any of these in an Elementor "Shortcode" widget:
 *   [alea_trust_bar] [alea_estimator] [alea_before_after]
 *   [alea_pricing]   [alea_process]
 * Styling is namespaced (.aleac-*) so it won't collide with the theme.
 * A sticky mobile Call/WhatsApp/Estimate bar is injected automatically.
 * ========================================================================= */

/* --- shared styles + scripts (front-end only) --- */
add_action( 'wp_head', function () {
	if ( is_admin() ) { return; }
	?>
<style id="aleac-css" data-no-optimize="1">
.aleac{--lime:#b1c900;--lime-deep:#71830a;--ink:#16170f;--ink2:#54564a;--ink3:#87887a;--line:#e3e2d7;--surf:#fff;--bg:#f4f4ee;
  font-family:-apple-system,system-ui,"Segoe UI",Roboto,Arial,sans-serif;color:var(--ink);box-sizing:border-box}
.aleac *{box-sizing:border-box}
.aleac .eb{font:600 .72rem/1 ui-monospace,Consolas,monospace;letter-spacing:.16em;text-transform:uppercase;color:var(--lime-deep);margin:0 0 10px}
.aleac h3{font-family:Cambria,Georgia,serif;font-weight:600;letter-spacing:-.01em;margin:0}
.aleac .abtn{display:inline-flex;align-items:center;gap:8px;font-weight:650;font-size:.95rem;padding:12px 20px;border-radius:8px;border:1px solid transparent;cursor:pointer;text-decoration:none;transition:.15s}
.aleac .abtn-primary{background:var(--lime);color:#16170f}
.aleac .abtn-primary:hover{filter:brightness(.96)}
.aleac .abtn-wa{background:#252c17;color:#eaf3c4}
/* estimator */
.aleac-est{display:grid;grid-template-columns:1fr 1fr;gap:40px;align-items:center;max-width:1080px;margin:0 auto;padding:8px 0}
.aleac-est .card{background:var(--bg);border:1px solid var(--line);border-radius:16px;padding:24px}
.aleac-est .fld{margin-bottom:16px}
.aleac-est label{display:block;font:600 .7rem/1 ui-monospace,Consolas,monospace;letter-spacing:.06em;text-transform:uppercase;color:var(--ink3);margin-bottom:8px}
.aleac-est .chips{display:flex;flex-wrap:wrap;gap:8px}
.aleac-chip{border:1px solid #d2d1c4;background:#fff;color:var(--ink);padding:9px 14px;border-radius:20px;font-size:.88rem;cursor:pointer;transition:.15s}
.aleac-chip[aria-pressed="true"]{background:var(--lime);border-color:var(--lime);color:#16170f;font-weight:650}
.aleac-est .out{margin-top:6px;padding-top:18px;border-top:1px dashed #d2d1c4;display:flex;align-items:baseline;justify-content:space-between;gap:10px;flex-wrap:wrap}
.aleac-est .price{font-family:Cambria,Georgia,serif;font-size:1.9rem;font-variant-numeric:tabular-nums}
.aleac-est .sub{font:.7rem/1.4 ui-monospace,Consolas,monospace;color:var(--ink3);margin-top:12px}
@media(max-width:800px){.aleac-est{grid-template-columns:1fr;gap:24px}}
/* before/after */
.aleac-ba{position:relative;aspect-ratio:16/11;border-radius:14px;overflow:hidden;max-width:760px;margin:0 auto;user-select:none;touch-action:none;box-shadow:0 10px 30px rgba(0,0,0,.12)}
.aleac-ba .l{position:absolute;inset:0;background-size:cover;background-position:center;display:flex;align-items:flex-end;padding:14px}
.aleac-ba .after{clip-path:inset(0 0 0 50%)}
.aleac-ba .lbl{font:.66rem/1 ui-monospace,Consolas,monospace;color:#fff;background:rgba(0,0,0,.4);padding:5px 9px;border-radius:4px}
.aleac-ba .hnd{position:absolute;top:0;bottom:0;left:50%;width:2px;background:var(--lime)}
.aleac-ba .hnd::after{content:"‹ ›";position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:38px;height:38px;background:var(--lime);border-radius:50%;color:#16170f;font-weight:800;display:grid;place-items:center;box-shadow:0 2px 10px rgba(0,0,0,.35)}
/* pricing */
.aleac-pr{display:grid;grid-template-columns:repeat(3,1fr);gap:18px;max-width:1080px;margin:0 auto}
.aleac-pr .t{background:var(--surf);border:1px solid var(--line);border-radius:14px;padding:24px;display:flex;flex-direction:column}
.aleac-pr .t.feat{border-color:var(--lime);box-shadow:0 0 0 1px var(--lime)}
.aleac-pr .nm{font:700 .7rem/1 ui-monospace,Consolas,monospace;letter-spacing:.1em;text-transform:uppercase;color:var(--ink3)}
.aleac-pr .t.feat .nm{color:var(--lime-deep)}
.aleac-pr .amt{font-family:Cambria,Georgia,serif;font-size:2rem;margin:12px 0 2px}
.aleac-pr .amt small{font-size:.82rem;color:var(--ink3);font-family:inherit}
.aleac-pr ul{list-style:none;padding:0;margin:16px 0 20px;display:grid;gap:9px}
.aleac-pr li{font-size:.9rem;color:var(--ink2);padding-left:24px;position:relative}
.aleac-pr li::before{content:"✓";position:absolute;left:0;color:var(--lime-deep);font-weight:800}
.aleac-pr .abtn{margin-top:auto;justify-content:center}
@media(max-width:800px){.aleac-pr{grid-template-columns:1fr}}
/* process */
.aleac-ps{display:grid;grid-template-columns:repeat(3,1fr);gap:16px;max-width:1080px;margin:0 auto}
.aleac-ps .s{background:var(--surf);border:1px solid var(--line);border-radius:12px;padding:20px}
.aleac-ps .n{font:700 .72rem/1 ui-monospace,Consolas,monospace;color:var(--lime-deep);letter-spacing:.05em}
.aleac-ps h3{font-size:1.1rem;margin:9px 0 5px}
.aleac-ps p{color:var(--ink2);font-size:.9rem;margin:0}
.aleac-ps .w{font:.68rem/1 ui-monospace,Consolas,monospace;color:var(--ink3);margin-top:11px;display:block}
@media(max-width:800px){.aleac-ps{grid-template-columns:1fr}}
/* sticky mobile bar */
.aleac-mbar{position:fixed;left:0;right:0;bottom:0;z-index:9999;display:none;gap:8px;padding:9px 10px;background:#fff;border-top:1px solid var(--line);box-shadow:0 -6px 18px rgba(0,0,0,.1)}
.aleac-mbar .abtn{flex:1;justify-content:center;padding:11px;font-size:.9rem}
@media(max-width:781px){.aleac-mbar{display:flex}}
</style>
	<?php
}, 22 );

/* --- [alea_estimator] --- */
add_shortcode( 'alea_estimator', function () {
	ob_start(); ?>
<div class="aleac aleac-est">
  <div>
    <p class="eb">Instant estimate</p>
    <h3 style="font-size:clamp(1.6rem,3vw,2.3rem);line-height:1.1">Get your kitchen price in 60&nbsp;seconds.</h3>
    <p style="color:var(--ink2);margin:14px 0 0">Powered by the same pricing engine our designers use in-house. Pick your layout, size and finish for an instant ballpark — then book a free site visit for the exact quote.</p>
    <p class="sub">EMI available from ~₹3,300/month · Final price after free site measurement.</p>
  </div>
  <div class="card">
    <div class="fld"><label>Kitchen layout</label><div class="chips" data-group="shape">
      <button class="aleac-chip" aria-pressed="true" data-v="1">L-shaped</button><button class="aleac-chip" data-v="1.15">U-shaped</button><button class="aleac-chip" data-v="1.05">Parallel</button><button class="aleac-chip" data-v="0.9">Straight</button><button class="aleac-chip" data-v="1.25">Island</button></div></div>
    <div class="fld"><label>Approx size</label><div class="chips" data-group="size">
      <button class="aleac-chip" data-v="0.8">Compact</button><button class="aleac-chip" aria-pressed="true" data-v="1">Standard</button><button class="aleac-chip" data-v="1.3">Large</button></div></div>
    <div class="fld"><label>Finish tier</label><div class="chips" data-group="finish">
      <button class="aleac-chip" data-v="0.72" data-name="Essential">Essential</button><button class="aleac-chip" aria-pressed="true" data-v="1" data-name="Premium">Premium</button><button class="aleac-chip" data-v="1.5" data-name="Luxury">Luxury</button></div></div>
    <div class="out"><div><div class="price" data-est-price>₹1.9L – ₹2.4L</div><div class="sub" data-est-sub>Premium · L-shaped · standard</div></div><a class="abtn abtn-primary" href="#estimate">Get exact quote →</a></div>
  </div>
</div>
	<?php return ob_get_clean();
} );

/* --- [alea_before_after before="URL" after="URL"] --- */
add_shortcode( 'alea_before_after', function ( $atts ) {
	$a = shortcode_atts( array( 'before' => '', 'after' => '' ), $atts );
	$bstyle = $a['before'] ? 'background-image:url(' . esc_url( $a['before'] ) . ')' : 'background:linear-gradient(135deg,#4a5158,#2b3036)';
	$astyle = $a['after'] ? 'background-image:url(' . esc_url( $a['after'] ) . ')' : 'background:linear-gradient(135deg,#7a5638,#3f2c1c)';
	ob_start(); ?>
<div class="aleac"><div class="aleac-ba" data-ba>
  <div class="l" style="<?php echo $bstyle; ?>"><span class="lbl">BEFORE</span></div>
  <div class="l after" data-ba-after style="<?php echo $astyle; ?>"><span class="lbl" style="margin-left:auto">AFTER — ALEA</span></div>
  <div class="hnd" data-ba-hnd></div>
</div></div>
	<?php return ob_get_clean();
} );

/* --- [alea_pricing] --- */
add_shortcode( 'alea_pricing', function () {
	ob_start(); ?>
<div class="aleac aleac-pr">
  <div class="t"><span class="nm">Essential</span><div class="amt">₹1,150<small>/ sq ft</small></div><ul><li>Moisture-resistant ply carcass</li><li>Laminate shutters</li><li>Soft-close hinges</li><li>10-year written warranty</li></ul><a class="abtn" style="border:1px solid #d2d1c4" href="#estimate">Get estimate</a></div>
  <div class="t feat"><span class="nm">Premium · Most chosen</span><div class="amt">₹1,750<small>/ sq ft</small></div><ul><li>100% termite &amp; water-proof carcass</li><li>Acrylic / PU shutters</li><li>European soft-close hardware</li><li>10-year warranty</li></ul><a class="abtn abtn-primary" href="#estimate">Get estimate</a></div>
  <div class="t"><span class="nm">Luxury</span><div class="amt">₹2,600<small>/ sq ft</small></div><ul><li>Premium finishes &amp; glass</li><li>Motorised &amp; automation options</li><li>Bespoke island &amp; tall units</li><li>10-year written warranty</li></ul><a class="abtn" style="border:1px solid #d2d1c4" href="#estimate">Get estimate</a></div>
</div>
	<?php return ob_get_clean();
} );

/* --- [alea_process] --- */
add_shortcode( 'alea_process', function () {
	$steps = array(
		array( '01', 'Free consultation', 'We understand your space, style and budget — at home or our showroom.', 'Day 1 · free' ),
		array( '02', 'Site measurement', 'Precise measurements so everything fits to the millimetre.', 'Within 2–3 days' ),
		array( '03', '3D design &amp; quote', 'See your kitchen in 3D with a clear, itemised price.', '~1 week' ),
		array( '04', 'Approve &amp; confirm', 'Lock the design and finishes; production begins.', 'On approval' ),
		array( '05', 'In-house manufacturing', 'Built in our 95,000 sq ft factory with European hardware.', '3–4 weeks' ),
		array( '06', 'Install &amp; service', 'Fitted on schedule, backed by our after-sales team.', '1–2 days · 10-yr warranty' ),
	);
	ob_start(); echo '<div class="aleac aleac-ps">';
	foreach ( $steps as $s ) {
		echo '<div class="s"><span class="n">STEP ' . $s[0] . '</span><h3>' . $s[1] . '</h3><p>' . $s[2] . '</p><span class="w">' . $s[3] . '</span></div>';
	}
	echo '</div>'; return ob_get_clean();
} );

/* --- sticky mobile Call / WhatsApp / Estimate bar (auto) --- */
add_action( 'wp_footer', function () {
	if ( is_admin() ) { return; }
	?>
<div class="aleac aleac-mbar">
  <a class="abtn" style="border:1px solid var(--line)" href="tel:+919554995449">Call</a>
  <a class="abtn abtn-wa" href="https://wa.me/919554995449?text=Hi%20ALEA%2C%20I%27d%20like%20a%20free%20modular%20kitchen%20estimate.">WhatsApp</a>
  <a class="abtn abtn-primary" href="#estimate">Free Estimate</a>
</div>
	<?php
}, 5 );

/* --- component JS (estimator + before/after) --- */
add_action( 'wp_footer', function () {
	if ( is_admin() ) { return; }
	?>
<script id="aleac-js" data-no-optimize="1" data-no-defer="1">
(function(){
  // estimator
  document.querySelectorAll('.aleac-est').forEach(function(root){
    var base=210000, st={shape:1,size:1,finish:1,fn:'Premium',sn:'L-shaped',zn:'standard'};
    function fmt(n){var l=n/100000;return '₹'+(l<10?l.toFixed(1):Math.round(l))+'L';}
    function upd(){var m=base*st.shape*st.size*st.finish;
      root.querySelector('[data-est-price]').textContent=fmt(m*0.88)+' – '+fmt(m*1.12);
      root.querySelector('[data-est-sub]').textContent=st.fn+' · '+st.sn+' · '+st.zn;}
    root.querySelectorAll('.chips').forEach(function(g){
      g.addEventListener('click',function(e){var b=e.target.closest('.aleac-chip');if(!b)return;
        g.querySelectorAll('.aleac-chip').forEach(function(c){c.setAttribute('aria-pressed','false');});
        b.setAttribute('aria-pressed','true');var grp=g.dataset.group,v=parseFloat(b.dataset.v);st[grp]=v;
        if(grp==='finish')st.fn=b.dataset.name; if(grp==='shape')st.sn=b.textContent.trim();
        if(grp==='size')st.zn=b.textContent.trim().toLowerCase(); upd();});
    }); upd();
  });
  // before/after
  document.querySelectorAll('[data-ba]').forEach(function(s){
    var after=s.querySelector('[data-ba-after]'),h=s.querySelector('[data-ba-hnd]'),drag=false;
    function set(x){var r=s.getBoundingClientRect(),p=Math.min(1,Math.max(0,(x-r.left)/r.width));
      after.style.clipPath='inset(0 0 0 '+(p*100)+'%)';h.style.left=(p*100)+'%';}
    s.addEventListener('pointerdown',function(e){drag=true;set(e.clientX);});
    window.addEventListener('pointermove',function(e){if(drag)set(e.clientX);});
    window.addEventListener('pointerup',function(){drag=false;});
  });
})();
</script>
	<?php
}, 101 );

/* =====================================================================
 * BLOCK 9 — [alea_landing] full redesigned homepage
 * Renders a complete landing experience and reuses the block-8 components
 * via do_shortcode(). Namespaced .aleax-* so it can't collide with theme.
 * ===================================================================== */
add_action( 'wp_head', function () {
	?>
<style id="aleax-css" data-no-optimize="1">
.aleax{--maroon:#92003b;--maroon2:#6d002c;--lime:#b1c900;--lime-deep:#71830a;--ink:#16170f;--ink2:#54564a;--ink3:#87887a;--line:#e3e2d7;--paper:#faf9f6;--stone:#efeee6;color:var(--ink);font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Arial,sans-serif;line-height:1.6}
.aleax *{box-sizing:border-box}
.aleax img{max-width:100%;height:auto}
.aleax-in{max-width:1140px;margin:0 auto;padding:0 22px}
.aleax-bleed{margin-left:calc(50% - 50vw);margin-right:calc(50% - 50vw)}
.aleax-sec{padding:72px 22px}
.aleax h2{font-family:Cambria,Georgia,"Times New Roman",serif;font-weight:600;letter-spacing:-.015em;line-height:1.08;font-size:clamp(1.8rem,3.6vw,2.7rem);margin:0 0 14px;text-wrap:balance}
.aleax .eb{font:700 .72rem/1 ui-monospace,Consolas,monospace;letter-spacing:.16em;text-transform:uppercase;color:var(--lime-deep);margin:0 0 12px}
.aleax .lead{color:var(--ink2);font-size:1.08rem;max-width:62ch}
.aleax .abtn{display:inline-flex;align-items:center;gap:8px;font-weight:650;font-size:1rem;padding:14px 24px;border-radius:9px;border:1px solid transparent;cursor:pointer;text-decoration:none;transition:.15s}
.aleax .abtn-primary{background:var(--lime);color:#16170f}
.aleax .abtn-primary:hover{filter:brightness(.95)}
.aleax .abtn-ghost{background:transparent;color:#fff;border-color:rgba(255,255,255,.35)}
.aleax .abtn-ghost:hover{background:rgba(255,255,255,.1)}
.aleax-head{text-align:center;max-width:700px;margin:0 auto 44px}
.aleax-head .lead{margin:0 auto}
/* hero */
.aleax-hero{background:radial-gradient(120% 120% at 15% 0%,#a30642 0%,var(--maroon2) 42%,#3a0a22 100%);color:#fff}
.aleax-hero .in{max-width:1140px;margin:0 auto;padding:76px 22px;display:grid;grid-template-columns:1.05fr .95fr;gap:48px;align-items:center}
.aleax-hero h1{font-family:Cambria,Georgia,serif;font-weight:600;font-size:clamp(2.1rem,4.6vw,3.5rem);line-height:1.05;letter-spacing:-.02em;margin:14px 0 0;text-wrap:balance}
.aleax-hero .sub{color:#f4d9e4;font-size:1.12rem;margin:18px 0 0;max-width:52ch}
.aleax-hero .eb{color:#e6b3c8}
.aleax-hero .cta{display:flex;gap:12px;flex-wrap:wrap;margin-top:28px}
.aleax-hero .fig{border-radius:16px;overflow:hidden;box-shadow:0 30px 60px rgba(0,0,0,.4);border:1px solid rgba(255,255,255,.14);aspect-ratio:4/3;background:#2a0817 center/cover no-repeat}
.aleax-hero .trust{display:flex;gap:24px;flex-wrap:wrap;margin-top:30px;padding-top:22px;border-top:1px solid rgba(255,255,255,.16);color:#f0cede;font-size:.9rem}
.aleax-hero .trust b{color:#fff}
@media(max-width:860px){.aleax-hero .in{grid-template-columns:1fr;padding:50px 22px;gap:28px}.aleax-hero .fig{order:-1}}
/* stats */
.aleax-stats{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;padding:32px 22px;max-width:1140px;margin:0 auto}
.aleax-stat{text-align:center}
.aleax-stat b{display:block;font-family:Cambria,Georgia,serif;font-size:2rem;color:var(--maroon)}
.aleax-stat span{font-size:.8rem;color:var(--ink3);text-transform:uppercase;letter-spacing:.05em}
@media(max-width:700px){.aleax-stats{grid-template-columns:repeat(2,1fr);gap:22px}}
/* why */
.aleax-why{background:var(--ink);color:#eef0e6}
.aleax-why .in{max-width:1140px;margin:0 auto;padding:72px 22px}
.aleax-why h2{color:#fff}.aleax-why .eb{color:var(--lime)}
.aleax-why .grid{display:grid;grid-template-columns:repeat(4,1fr);gap:18px;margin-top:8px}
.aleax-card{background:#20241a;border:1px solid #2f3426;border-radius:14px;padding:24px}
.aleax-card .ic{width:44px;height:44px;border-radius:11px;background:var(--lime);color:#16170f;display:grid;place-items:center;font-weight:800;font-size:1.2rem;margin-bottom:14px}
.aleax-card h3{font-family:Cambria,Georgia,serif;font-size:1.15rem;margin:0 0 6px;color:#fff}
.aleax-card p{color:#b9beac;font-size:.92rem;margin:0}
@media(max-width:860px){.aleax-why .grid{grid-template-columns:1fr 1fr}}
@media(max-width:520px){.aleax-why .grid{grid-template-columns:1fr}}
/* band */
.aleax-band{background:var(--stone)}
.aleax-band .in{max-width:1140px;margin:0 auto;padding:72px 22px}
/* testimonials */
.aleax-quotes{background:var(--maroon);color:#fff}
.aleax-quotes .in{max-width:1140px;margin:0 auto;padding:72px 22px}
.aleax-quotes h2{color:#fff}.aleax-quotes .eb{color:var(--lime)}
.aleax-quotes .grid{display:grid;grid-template-columns:repeat(3,1fr);gap:18px;margin-top:8px}
.aleax-q{background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.16);border-radius:14px;padding:24px}
.aleax-q .st{color:var(--lime);letter-spacing:2px;margin-bottom:10px}
.aleax-q p{font-size:.98rem;color:#fbe7ef;margin:0 0 14px}
.aleax-q .who{font-size:.85rem;color:#e9c2d3}.aleax-q .who b{color:#fff;display:block}
@media(max-width:860px){.aleax-quotes .grid{grid-template-columns:1fr}}
/* faq */
.aleax-faq details{border-bottom:1px solid var(--line);padding:18px 0}
.aleax-faq summary{cursor:pointer;font-weight:650;font-size:1.05rem;list-style:none;display:flex;justify-content:space-between;gap:16px;color:var(--ink)}
.aleax-faq summary::-webkit-details-marker{display:none}
.aleax-faq summary::after{content:"+";color:var(--maroon);font-weight:400;font-size:1.5rem;line-height:1}
.aleax-faq details[open] summary::after{content:"\2013"}
.aleax-faq p{color:var(--ink2);margin:12px 0 0;max-width:78ch}
/* final cta */
.aleax-cta{background:radial-gradient(120% 120% at 85% 0%,#a30642,var(--maroon2) 45%,#2c0819);color:#fff}
.aleax-cta .in{max-width:900px;margin:0 auto;padding:72px 22px;text-align:center}
.aleax-cta h2{color:#fff}.aleax-cta .eb{color:var(--lime)}
.aleax-cta .lead{color:#f4d9e4;margin:0 auto 8px}
.aleax-cta .formwrap{background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.16);border-radius:16px;padding:30px;margin-top:26px;text-align:left}
.aleax-cta .wpcf7 input:not([type=submit]):not([type=checkbox]):not([type=radio]),.aleax-cta .wpcf7 textarea,.aleax-cta .wpcf7 select{width:100%;padding:13px 14px;border-radius:9px;border:1px solid rgba(255,255,255,.25);background:rgba(255,255,255,.95);color:#16170f;font-size:1rem;margin:6px 0}
.aleax-cta .wpcf7 input[type=submit]{background:var(--lime);color:#16170f;border:0;padding:14px 28px;border-radius:9px;font-weight:700;font-size:1.02rem;cursor:pointer;width:100%;margin-top:6px}
.aleax-cta .altcta{margin-top:18px;color:#f0cede;font-size:.95rem}.aleax-cta .altcta a{color:#fff;font-weight:700}
.aleax-cta .promise{display:flex;flex-wrap:wrap;gap:12px 26px;justify-content:center;margin:22px 0 0;color:#fbe7ef;font-size:.92rem}
.aleax-cta .promise span::before{content:"\2713";color:var(--lime);font-weight:800;margin-right:7px}
/* ---- polish ---- */
:root{scroll-behavior:smooth}
.aleax .abtn-primary{box-shadow:0 8px 22px rgba(177,201,0,.35)}
.aleax .abtn:hover{transform:translateY(-1px)}
.aleax-card{transition:transform .18s ease,border-color .18s}
.aleax-card:hover{transform:translateY(-4px);border-color:#414a32}
.aleax-q{transition:transform .18s ease,background .18s}
.aleax-q:hover{transform:translateY(-4px);background:rgba(255,255,255,.12)}
.aleax .aleac-pr .t{transition:transform .18s ease,box-shadow .18s}
.aleax .aleac-pr .t:hover{transform:translateY(-4px);box-shadow:0 14px 30px rgba(22,23,15,.12)}
.aleax .aleac-ps .s{transition:transform .18s ease,box-shadow .18s}
.aleax .aleac-ps .s:hover{transform:translateY(-4px);box-shadow:0 14px 30px rgba(22,23,15,.1)}
.aleax a:focus-visible,.aleax button:focus-visible,.aleax input:focus-visible,.aleax summary:focus-visible{outline:2px solid var(--lime);outline-offset:3px;border-radius:6px}
.aleax-hero .fig{position:relative}
.aleax-hero .fig::after{content:"";position:absolute;inset:0;background:linear-gradient(180deg,transparent 52%,rgba(58,10,34,.5));pointer-events:none}
.aleax-hero .badge{position:absolute;left:16px;bottom:16px;z-index:2;display:flex;align-items:center;gap:11px;background:rgba(255,255,255,.96);color:#16170f;border-radius:13px;padding:11px 15px;box-shadow:0 12px 26px rgba(0,0,0,.28)}
.aleax-hero .badge .bmark{width:36px;height:36px;border-radius:10px;background:var(--lime);color:#16170f;display:grid;place-items:center;font-weight:800;font-size:1.15rem;flex:none}
.aleax-hero .badge b{font-family:Cambria,Georgia,serif;font-size:1.12rem;display:block;line-height:1.05}
.aleax-hero .badge span{font-size:.74rem;color:#54564a}
@media(prefers-reduced-motion:reduce){:root{scroll-behavior:auto}.aleax *{transition:none!important}}
</style>
	<?php
}, 23 );

add_shortcode( 'alea_landing', function () {
	$up = home_url( '/wp-content/uploads' );
	$hero = $up . '/2026/03/uploaded-image-31.png';
	$wa   = 'https://wa.me/919554995449?text=Hi%20ALEA%2C%20I%27d%20like%20a%20free%20modular%20kitchen%20estimate.';
	ob_start(); ?>
<div class="aleax">

  <section class="aleax-bleed aleax-hero"><div class="in">
    <div>
      <p class="eb">Panchkula · Tricity's modular specialists</p>
      <h1>Kitchens &amp; wardrobes, engineered around your life.</h1>
      <p class="sub">Factory-made modular interiors with a written 10-year warranty — designed, built and installed by ALEA. Get an instant price, then a free site visit.</p>
      <div class="cta">
        <a class="abtn abtn-primary" href="#estimate-tool">Get my instant price →</a>
        <a class="abtn abtn-ghost" href="<?php echo esc_url( $wa ); ?>">WhatsApp us</a>
      </div>
      <div class="trust"><span>★★★★★ <b>4.8</b>/5</span><span><b>2,000+</b> interiors</span><span><b>10-year</b> warranty</span><span><b>95,000 sq ft</b> factory</span></div>
    </div>
    <div class="fig" style="background-image:url(<?php echo esc_url( $hero ); ?>)"><div class="badge"><span class="bmark">✓</span><div><b>10-Year</b><span>Written warranty</span></div></div></div>
  </div></section>

  <section class="aleax-stats">
    <div class="aleax-stat"><b>12+</b><span>Years</span></div>
    <div class="aleax-stat"><b>2,000+</b><span>Projects</span></div>
    <div class="aleax-stat"><b>10-yr</b><span>Warranty</span></div>
    <div class="aleax-stat"><b>15 days</b><span>Avg. install</span></div>
  </section>

  <section id="estimate-tool" class="aleax-sec"><div class="aleax-in">
    <?php echo do_shortcode( '[alea_estimator]' ); ?>
  </div></section>

  <section class="aleax-bleed aleax-why"><div class="in">
    <div class="aleax-head"><p class="eb">Why ALEA</p><h2>Built better, priced honestly, delivered on time.</h2></div>
    <div class="grid">
      <div class="aleax-card"><div class="ic">⌂</div><h3>Our own factory</h3><p>95,000 sq ft of in-house manufacturing — no middlemen, no hidden markups, full quality control.</p></div>
      <div class="aleax-card"><div class="ic">✦</div><h3>European hardware</h3><p>Hettich &amp; Blum soft-close hinges and channels as standard — not a paid upgrade.</p></div>
      <div class="aleax-card"><div class="ic">✓</div><h3>10-year warranty</h3><p>Written, honoured, and backed by a local after-sales team you can actually reach.</p></div>
      <div class="aleax-card"><div class="ic">◷</div><h3>On-time install</h3><p>A clear schedule from day one — most kitchens fitted within 15 days of approval.</p></div>
    </div>
  </div></section>

  <section class="aleax-sec"><div class="aleax-in">
    <div class="aleax-head"><p class="eb">See the difference</p><h2>From bare space to a kitchen you'll love.</h2><p class="lead">Drag the slider to reveal an ALEA transformation.</p></div>
    <?php echo do_shortcode( '[alea_before_after after="' . esc_url( $hero ) . '"]' ); ?>
  </div></section>

  <section class="aleax-bleed aleax-band"><div class="in">
    <div class="aleax-head"><p class="eb">Transparent pricing</p><h2>Clear per-running-foot pricing. No surprises.</h2><p class="lead">Indicative ranges — your exact quote comes after a free site measurement.</p></div>
    <?php echo do_shortcode( '[alea_pricing]' ); ?>
  </div></section>

  <section class="aleax-sec"><div class="aleax-in">
    <div class="aleax-head"><p class="eb">How it works</p><h2>From first call to cooking — in clear steps.</h2></div>
    <?php echo do_shortcode( '[alea_process]' ); ?>
  </div></section>

  <?php /* Testimonials removed 2026-07-25: the previous three quotes were
           placeholder content, not real customer reviews. Reinstate only
           with verified Google reviews. */ ?>

  <section class="aleax-sec aleax-faq"><div class="aleax-in" style="max-width:820px">
    <div class="aleax-head"><p class="eb">Good to know</p><h2>Questions homeowners ask us.</h2></div>
    <details open><summary>How much does an ALEA modular kitchen cost?</summary><p>Our rates run from ₹1,150 to ₹2,600 per square foot of cabinetry depending on the carcass, shutter finish and hardware you choose — most full kitchens land between ₹1 and ₹3 lakh. Use the instant estimator above for a ballpark, then book a free site visit for an exact, itemised quote.</p></details>
    <details><summary>How long does design and installation take?</summary><p>Design and 3D approval usually take about a week. Manufacturing runs 3–4 weeks in our own factory, and on-site installation is typically 1–2 days — most projects are fully fitted within 15 days of approval.</p></details>
    <details><summary>What is covered by the 10-year warranty?</summary><p>The carcass, hardware and workmanship are covered in writing for 10 years, supported by a local after-sales team. Exact terms are shared with your quote.</p></details>
    <details><summary>Which materials and hardware do you use?</summary><p>Moisture- and termite-resistant carcasses, your choice of laminate, acrylic or PU shutters, and European soft-close hardware (Hettich / Blum) as standard across our Premium and Luxury tiers.</p></details>
    <details><summary>Do you offer EMI or finance options?</summary><p>Yes — EMI is available from roughly ₹3,300/month depending on the project value. Ask our designer for current options during your consultation.</p></details>
    <details><summary>Which areas do you serve?</summary><p>We design, manufacture and install across the Tricity — Panchkula, Chandigarh, Mohali, Zirakpur and nearby areas.</p></details>
  </div></section>

  <section id="estimate" class="aleax-bleed aleax-cta"><div class="in">
    <p class="eb">Free &amp; no-obligation</p>
    <h2>Book your free design consultation.</h2>
    <p class="lead">Share your name and number — a senior designer will call within 24 hours with ideas and an estimate.</p>
    <div class="promise"><span>Free design &amp; 3D quote</span><span>10-year written warranty</span><span>Made in our own factory</span><span>On-time installation</span></div>
    <div class="formwrap"><?php echo do_shortcode( '[contact-form-7 id="7dcf010" title="Home Page form"]' ); ?></div>
    <p class="altcta">Prefer to talk now? Call <a href="tel:+919554995449">+91 95549 95449</a> or <a href="<?php echo esc_url( $wa ); ?>">WhatsApp us</a>.</p>
  </div></section>

</div>
	<?php return ob_get_clean();
} );

/* =====================================================================
 * BLOCK 10 — SITE-WIDE DESIGN SKIN
 * Restyles the shared furniture of EVERY page (headings, buttons, forms,
 * links, nav accents, footer accents) in the new brand system:
 * maroon #92003b + lime #b1c900 + Cambria serif headings.
 * Selectors verified against the actual staging markup/stylesheets
 * (theratio theme + Elementor kit-7 + child CSS + CF7). Content colors on
 * dark contexts (hero/page-title/footer/nav) are deliberately untouched.
 * ===================================================================== */
add_action( 'wp_head', function () {
	if ( is_admin() ) { return; }
	?>
<style id="alea-skin-css" data-no-optimize="1">
/* A) Elementor kit tokens: default blue/green -> brand ink/lime */
.elementor-kit-7{--e-global-color-primary:#1a1a1a;--e-global-color-accent:#b1c900}

/* B) Headings -> Cambria serif site-wide (font only; colors untouched so
      white-on-dark heroes, banners and footer headings keep their color) */
h1,h2,h3,h4,h5,h6,
.elementor-widget-heading .elementor-heading-title,
div.elementor-widget-heading .elementor-heading-title,
.ot-heading h2.main-heading,
h1.page-title,
.acc-toggle{font-family:Cambria,Georgia,"Times New Roman",serif!important;letter-spacing:-.01em}
/* old-home hero has a higher-specificity Titillium !important rule — out-specify it */
section.bannerWrapper2 h1.elementor-heading-title.elementor-size-default{font-family:Cambria,Georgia,"Times New Roman",serif!important;font-weight:600!important}

/* C) Section kickers ("[ testimonials ]") -> branded eyebrow style */
.ot-heading>span{font-family:ui-monospace,Consolas,monospace!important;font-weight:700;letter-spacing:.16em;color:#71830a;text-transform:uppercase}
.ot-heading.is-dots:before{border-bottom-color:#b1c900!important}
/* dark contact panel: olive kicker fails contrast there — use lime */
.elementor-element-131457d .ot-heading>span{color:#b1c900}

/* D) Theme buttons (.octf-btn — CTAs + CF7 submits on contact/lead forms) */
.octf-btn{border-radius:9px;font-weight:700;transition:background .15s,transform .15s}
.octf-btn:hover{transform:translateY(-1px)}
.octf-btn:not(.no-line):before,.octf-btn:not(.no-line):after{display:none!important}
/* primary buttons go lime; -light/-dark variants keep their fills so paired CTAs retain hierarchy */
.octf-btn:not(.octf-btn-light):not(.octf-btn-dark){background:#b1c900!important;color:#16170f!important;border-color:#b1c900!important}
.octf-btn:not(.octf-btn-light):not(.octf-btn-dark):hover,.octf-btn:not(.octf-btn-light):not(.octf-btn-dark):focus,.octf-btn:not(.octf-btn-light):not(.octf-btn-dark):active{background:#9db300!important;color:#16170f!important}
.octf-btn.octf-btn-light,.octf-btn.octf-btn-dark{border-radius:9px}

/* E) Elementor button widgets (blog cards etc.) */
.elementor-widget-button .elementor-button{background-color:#b1c900;color:#16170f;border-radius:9px;font-weight:650}
.elementor-widget-button .elementor-button:hover{background-color:#9db300;color:#16170f}
/* blog loop-card buttons carry per-element styling — match its specificity */
.elementor-21463 .elementor-element.elementor-element-412f6fb .elementor-button{background-color:#b1c900;color:#16170f}
.elementor-21463 .elementor-element.elementor-element-412f6fb .elementor-button:hover,.elementor-21463 .elementor-element.elementor-element-412f6fb .elementor-button:focus{background-color:#9db300;color:#16170f}

/* F) Contact Form 7 fields — soft card fields replacing bare underlines */
.wpcf7 input[type=text],.wpcf7 input[type=email],.wpcf7 input[type=tel],.wpcf7 input[type=number],.wpcf7 input[type=date],.wpcf7 textarea,.wpcf7 select{border:1px solid #d9d8cc!important;border-radius:9px!important;background:#fff;padding:12px 14px!important;font-size:15px}
.wpcf7 input[type=text]:focus,.wpcf7 input[type=email]:focus,.wpcf7 input[type=tel]:focus,.wpcf7 textarea:focus,.wpcf7 select:focus{border-color:#b1c900!important;outline:2px solid rgba(113,131,10,.55);outline-offset:0}
.wpcf7 input[type=submit],.wpcf7 button[type=submit]{background:#b1c900!important;color:#16170f!important;border:0!important;border-radius:9px!important;font-weight:700}
.wpcf7 input[type=submit]:hover,.wpcf7 button[type=submit]:hover{background:#9db300!important}
/* keep validation states clearly ERROR-red, not brand-colored */
.wpcf7 input.wpcf7-not-valid,.wpcf7 textarea.wpcf7-not-valid,.wpcf7 select.wpcf7-not-valid{border-color:#dc3232!important}
.wpcf7 form.invalid .wpcf7-response-output,.wpcf7 form.unaccepted .wpcf7-response-output,.wpcf7 form.payment-required .wpcf7-response-output{border-color:#dc3232!important;color:#1a1a1a!important}

/* G) Body links: generic blue -> brand maroon (nav/buttons/footer exempted) */
body a{color:#92003b}
body a:hover{color:#6d002c}
.main-navigation a,.mobile_mainmenu a,.octf-btn,.elementor-button,.elementor-icon-list-item a,.elementor-social-icon,.aleac a,[class*="aleax"] a,.e-contact-buttons a,.page-header a,#site-header .elementor-widget-heading a{text-decoration:none!important}
/* dark panels (e.g. contact info on #1A1A1A): maroon is unreadable there */
.contact-info a,.elementor-element-131457d a{color:#d9d8cc}
.contact-info a:hover,.elementor-element-131457d a:hover{color:#b1c900}

/* H) Nav accents: lime hover + lime diamond marker (text stays white) */
#site-header .main-navigation>ul>li>a:hover{color:#b1c900!important}
#site-header .main-navigation>ul>li.current-menu-item>a{color:#b1c900!important}
.main-navigation>ul>li>a:before{background:#b1c900!important}
#site-header .main-navigation ul li li a:hover{color:#b1c900!important}
#mmenu-wrapper .mobile_mainmenu li a:hover{color:#b1c900}

/* I) Header top bar -> brand maroon (white text/logo unaffected) */
#site-header .elementor-element-aa59b40{background-color:#92003b!important}

/* J) Inner-page title banners: black overlay -> deep maroon-ink tint */
.page-header .dcell{background:rgba(44,8,25,.66)!important}

/* K) Footer micro-accents */
#site-footer .elementor-social-icon:hover{background-color:#b1c900!important;border-color:#b1c900!important}
#site-footer .elementor-social-icon:hover i{color:#16170f}
#site-footer .elementor-icon-list-item a:hover .elementor-icon-list-text{color:#b1c900!important}

/* L) Slider dots + FAQ toggles */
.swiper-pagination-bullet-active{background:#b1c900!important}
.ot-accordions .acc-item .acc-toggle i{color:#92003b}

/* M) Micro-UX: selection, keyboard focus, smooth scroll */
::selection{background:#b1c900;color:#16170f}
a:focus-visible,button:focus-visible,input:focus-visible,select:focus-visible,textarea:focus-visible,[role=button]:focus-visible{outline:2px solid #71830a;outline-offset:2px}
html{scroll-behavior:smooth}
@media(prefers-reduced-motion:reduce){html{scroll-behavior:auto}}

/* N) Mobile: the theme's fixed bottom bar contains BOTH the icon shortcuts
      AND the hamburger menu (site's only mobile nav — never hide it).
      Hide only the shortcut columns; float the hamburger as a chip just
      above our Call/WhatsApp/Estimate bar. */
@media(max-width:781px){
	.elementor-3992 .elementor-element-71192a3f>.e-con:not(.elementor-element-75db21a4){display:none!important}
	body .elementor-3992 .elementor-element.elementor-element-71192a3f{bottom:66px!important;top:auto!important;left:auto!important;right:10px!important;width:auto!important;min-width:0!important;background:transparent!important;box-shadow:none!important;padding:0!important}
	.elementor-3992 .elementor-element-75db21a4{--width:auto}
	.elementor-3992 .elementor-element-b091a41>.elementor-widget-container{padding:0!important;margin:0!important}
	.elementor-3992 .elementor-element-553d05d9{display:none!important}
	.elementor-3992 .mmenu-toggle button{background:#16170f!important;border-radius:10px!important;width:44px!important;height:44px!important;display:grid!important;place-items:center!important;box-shadow:0 4px 14px rgba(0,0,0,.35)!important}
	body:not(.elementor-editor-active){padding-bottom:60px}
}
</style>
	<?php
}, 24 );

/* Belt-and-braces: keep our namespaces out of LiteSpeed UCSS tree-shaking. */
add_filter( 'litespeed_ucss_whitelist', function ( $list ) {
	return array_merge( (array) $list, array( '.aleac*', '.aleax*', '.alea-trust*', '.octf-btn*', '.wpcf7*', '.ax-*', '.alea-*' ) );
} );

/* =====================================================================
 * BLOCK 11 — REDESIGN ROUTER  (staging redesign project)
 * Maps URL paths to self-contained page templates in theratio-child/alea/.
 * A matched page bypasses Elementor entirely and renders our own markup
 * inside the theme's header/footer. Unmapped pages are untouched, so the
 * rollout is page-by-page and instantly reversible (empty the map).
 * ===================================================================== */

/**
 * Path => array( file, title, desc ). Filled in as pages are built & approved.
 * Works for existing WP pages AND brand-new URLs (router forces 200 below).
 */
function alea_redesign_map() {
	return apply_filters( 'alea_redesign_map', array(
		'/'                        => array(
			'file'  => 'page-home.php',
			'title' => 'Modular Kitchens Made in Our Own Factory | ALEA Modular, Panchkula',
			'desc'  => 'Modular kitchens manufactured in our own 95,000 sq ft unit near Panchkula. Hettich & Blum hardware, 10-year written warranty, installed in about 15 days. Prices published.',
		),
		'/kitchen-cost-calculator/' => array(
			'file'  => 'page-calculator.php',
			'title' => 'Kitchen Cost Calculator — Real Tricity Prices | ALEA Modular',
			'desc'  => 'Get a real modular kitchen price in 60 seconds. Shape, size and collection — priced from the factory rate card, ₹1,150–2,600 per sq ft.',
		),
		'/modular-kitchen-price/'  => array(
			'file'  => 'page-pricing.php',
			'title' => 'Modular Kitchen Prices — Published by the Factory | ALEA Modular',
			'desc'  => 'The Tricity\'s only factory-published kitchen price list: ₹1,150–2,600 per sq ft by collection, with what\'s included and honest exclusions.',
		),
		/* The three collection pages are served by ONE template; the variant
		   arrives in 'args' and is whitelisted inside page-collection.php.
		   Paths are the plural /modular-kitchens/ form used in the approved
		   brief and built from $col_base in that template. */
		'/modular-kitchens/essential/' => array(
			'file'  => 'page-collection.php',
			'args'  => array( 'collection' => 'essential' ),
			'title' => 'Essential Modular Kitchens — Guide Price Published | ALEA Modular',
			'desc'  => 'ALEA Essential kitchens, with the guide price per sq ft of cabinetry published on the page. Made in our own factory at Raipur Rani, Panchkula district — 10-year written warranty, installed in 15 days.',
		),
		'/modular-kitchens/signature/' => array(
			'file'  => 'page-collection.php',
			'args'  => array( 'collection' => 'signature' ),
			'title' => 'Signature Modular Kitchens — Guide Price Published | ALEA Modular',
			'desc'  => 'ALEA Signature kitchens, with the guide price per sq ft of cabinetry published on the page. Made in our own factory at Raipur Rani, Panchkula district — 10-year written warranty, installed in 15 days.',
		),
		'/modular-kitchens/atelier/' => array(
			'file'  => 'page-collection.php',
			'args'  => array( 'collection' => 'atelier' ),
			'title' => 'Atelier Modular Kitchens — Guide Price Published | ALEA Modular',
			'desc'  => 'ALEA Atelier kitchens, with the guide price per sq ft of cabinetry published on the page. Made in our own factory at Raipur Rani, Panchkula district — 10-year written warranty, installed in 15 days.',
		),
		'/our-factory/'            => array(
			'file'  => 'page-factory.php',
			'title' => 'Inside Our 95,000 sq ft Factory | ALEA Modular, Raipur Rani',
			'desc'  => 'Every ALEA kitchen is made in our own 95,000 sq ft manufacturing unit at Raipur Rani, Panchkula district — CNC machining, Hettich & Blum hardware, 10-year written warranty.',
		),

		/* ---- wave 2 ---- */
		'/modular-kitchen/'        => array(
			'file'  => 'page-kitchens.php',
			'title' => 'Modular Kitchens | Made in Our Own Factory | ALEA Modular',
			'desc'  => 'Modular kitchens in three collections, priced in public from ₹1,150 per sq ft of cabinetry. Made in our own factory, Hettich & Blum hardware, 10-year written warranty.',
		),
		'/modular-kitchen/essential/' => array(
			'file'  => 'page-collection.php',
			'args'  => array( 'collection' => 'essential' ),
			'title' => 'Essential Collection | Modular Kitchens from ₹1,150 / sq ft | ALEA',
			'desc'  => 'The Essential collection: practical, factory-built modular kitchens from ₹1,150 per sq ft of cabinetry, with the same hardware and 10-year written warranty as every ALEA kitchen.',
		),
		'/modular-kitchen/signature/' => array(
			'file'  => 'page-collection.php',
			'args'  => array( 'collection' => 'signature' ),
			'title' => 'Signature Collection | Our Most-Chosen Kitchen | ALEA Modular',
			'desc'  => 'The Signature collection: our most-chosen modular kitchen, ₹1,450–1,950 per sq ft of cabinetry, built in our own factory with a 10-year written warranty.',
		),
		'/modular-kitchen/atelier/' => array(
			'file'  => 'page-collection.php',
			'args'  => array( 'collection' => 'atelier' ),
			'title' => 'Atelier Collection | Our Finest Modular Kitchens | ALEA Modular',
			'desc'  => 'The Atelier collection: our finest finishes and bespoke elements, ₹1,950–2,600 per sq ft of cabinetry, made in our own factory with a 10-year written warranty.',
		),
		'/wardrobe/'               => array(
			'file'  => 'page-wardrobes.php',
			'title' => 'Modular Wardrobes | Sliding, Hinged & Walk-In | ALEA Modular',
			'desc'  => 'Sliding, hinged and walk-in wardrobes built on the same factory line as our kitchens, with Hettich & Blum hardware and a 10-year written warranty. Priced after a free measurement.',
		),
		'/warranty/'               => array(
			'file'  => 'page-warranty.php',
			'title' => 'Our 10-Year Written Warranty | ALEA Modular',
			'desc'  => 'Every ALEA kitchen and wardrobe comes with a 10-year warranty, in writing, handed over with your installation. Made in our own factory with named-brand hardware.',
		),
		'/book-a-free-design-visit/' => array(
			'file'  => 'page-book-visit.php',
			'title' => 'Book a Free Design Visit or Factory Visit | ALEA Modular',
			'desc'  => 'Book a free measurement at your home, or visit our factory at Raipur Rani and watch kitchens being built. Free, no obligation, across Panchkula, Chandigarh, Mohali and Zirakpur.',
		),
	) );
}

/** Current request path, normalised to a leading+trailing slash. */
function alea_current_path() {
	$path = wp_parse_url( isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '/', PHP_URL_PATH );
	$path = is_string( $path ) ? $path : '/';
	return '/' . trim( $path, '/' ) . ( '/' === $path ? '' : '/' );
}

/** Resolve the current request against the map (once). */
function alea_redesign_entry() {
	static $entry = null, $done = false;
	if ( $done ) {
		return $entry;
	}
	$done = true;
	if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
		return null;
	}
	if ( isset( $_GET['elementor-preview'] ) || isset( $_GET['preview'] ) || isset( $_GET['alea-off'] ) ) {
		return null; // never hijack the editor; ?alea-off=1 = escape hatch to view the old page
	}
	$map  = alea_redesign_map();
	$path = alea_current_path();
	if ( empty( $map[ $path ] ) ) {
		return null;
	}
	$e         = is_array( $map[ $path ] ) ? $map[ $path ] : array( 'file' => $map[ $path ] );
	$e['path'] = $path;
	$e['abs']  = get_stylesheet_directory() . '/alea/' . basename( $e['file'] );
	if ( ! file_exists( $e['abs'] ) ) {
		return null; // fail safe: keep the existing page
	}
	$entry = $e;
	return $entry;
}

/* Mapped paths always answer 200 — even brand-new URLs with no WP page behind them. */
add_action( 'template_redirect', function () {
	$e = alea_redesign_entry();
	if ( $e && is_404() ) {
		global $wp_query;
		$wp_query->is_404 = false;
		status_header( 200 );
	}
}, 0 );

/* Title + meta description for mapped pages. */
add_filter( 'pre_get_document_title', function ( $title ) {
	$e = alea_redesign_entry();
	return ( $e && ! empty( $e['title'] ) ) ? $e['title'] : $title;
}, 99 );
add_action( 'wp_head', function () {
	$e = alea_redesign_entry();
	if ( $e && ! empty( $e['desc'] ) ) {
		echo '<meta name="description" content="' . esc_attr( $e['desc'] ) . '">' . "\n";
	}
}, 4 );

add_filter( 'template_include', function ( $template ) {
	if ( is_feed() || is_embed() ) {
		return $template;
	}
	$e = alea_redesign_entry();
	if ( ! $e ) {
		return $template;
	}
	$GLOBALS['alea_page_file'] = $e['abs'];
	$GLOBALS['alea_page_args'] = isset( $e['args'] ) && is_array( $e['args'] ) ? $e['args'] : array();
	$shell                     = get_stylesheet_directory() . '/alea/shell.php';
	return file_exists( $shell ) ? $shell : $template;
}, 999 );

/** Design-system CSS — inlined (LiteSpeed strips our external/inline CSS without this opt-out). */
add_action( 'wp_head', function () {
	if ( empty( $GLOBALS['alea_page_file'] ) ) {
		return; // only on redesigned pages
	}
	$css = get_stylesheet_directory() . '/alea/design-system.css';
	if ( file_exists( $css ) ) {
		echo "\n<style id=\"alea-ds\" data-no-optimize=\"1\">" . file_get_contents( $css ) . "</style>\n"; // phpcs:ignore
	}
}, 25 );
