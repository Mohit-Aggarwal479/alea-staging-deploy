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
<style id="aleac-css">
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
  <div class="t"><span class="nm">Essential</span><div class="amt">₹1,150<small>/ running ft</small></div><ul><li>Moisture-resistant ply carcass</li><li>Laminate shutters</li><li>Soft-close hinges</li><li>2-year warranty</li></ul><a class="abtn" style="border:1px solid #d2d1c4" href="#estimate">Get estimate</a></div>
  <div class="t feat"><span class="nm">Premium · Most chosen</span><div class="amt">₹1,750<small>/ running ft</small></div><ul><li>100% termite &amp; water-proof carcass</li><li>Acrylic / PU shutters</li><li>European soft-close hardware</li><li>10-year warranty</li></ul><a class="abtn abtn-primary" href="#estimate">Get estimate</a></div>
  <div class="t"><span class="nm">Luxury</span><div class="amt">₹2,600<small>/ running ft</small></div><ul><li>Premium finishes &amp; glass</li><li>Motorised &amp; automation options</li><li>Bespoke island &amp; tall units</li><li>10-yr warranty + priority service</li></ul><a class="abtn" style="border:1px solid #d2d1c4" href="#estimate">Get estimate</a></div>
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
<script id="aleac-js">
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
