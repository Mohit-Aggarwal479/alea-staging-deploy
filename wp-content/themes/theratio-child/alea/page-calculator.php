<?php
/**
 * ALEA — /kitchen-cost-calculator/
 * The site's centrepiece: tool-as-hero live estimator.
 * Every business number on this page comes from facts.php. The JS reads its
 * numbers from the JSON blob emitted below — no literals in the script.
 */

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/facts.php';
require_once __DIR__ . '/images.php';

$f           = alea_facts();
$collections = $f['collections'];
$hw_std      = $f['hardware_brands'][0]; // first named brand
$hw_up       = $f['hardware_brands'][1]; // second named brand

/* ---- Shapes. The per-shape running-feet DEFAULT is a page assumption
        (a prefilled starting point, stated in visible text and fully
        user-adjustable) — not a business fact. ---- */
$shapes = array(
	'straight' => array( 'label' => 'Straight', 'feet' => 8 ),
	'l'        => array( 'label' => 'L-Shape', 'feet' => 10 ),
	'parallel' => array( 'label' => 'Parallel', 'feet' => 12 ),
	'u'        => array( 'label' => 'U-Shape', 'feet' => 14 ),
	'island'   => array( 'label' => 'Island', 'feet' => 14 ),
	'g'        => array( 'label' => 'G-Shape', 'feet' => 16 ),
);

/* Honour ?shape= (e.g. /kitchen-cost-calculator/?shape=u or ?shape=l-shaped). */
$shape_aliases = array(
	'straight' => 'straight',
	'l'        => 'l',
	'l-shape'  => 'l',
	'l-shaped' => 'l',
	'parallel' => 'parallel',
	'u'        => 'u',
	'u-shape'  => 'u',
	'u-shaped' => 'u',
	'island'   => 'island',
	'g'        => 'g',
	'g-shape'  => 'g',
	'g-shaped' => 'g',
);
$shape_param = isset( $_GET['shape'] ) ? sanitize_key( wp_unslash( $_GET['shape'] ) ) : '';
$sel_shape   = isset( $shape_aliases[ $shape_param ] ) ? $shape_aliases[ $shape_param ] : 'l';

/* Honour ?collection= (the collection pages' CTAs link here pre-set, e.g.
   /kitchen-cost-calculator/?collection=essential). Whitelisted against
   facts.php; anything unknown falls back to the featured collection. */
$tier_param = isset( $_GET['collection'] ) ? sanitize_key( wp_unslash( $_GET['collection'] ) ) : '';

/* Defaults: shape (URL or L), that shape's prefilled feet, collection from the
   URL or Signature (the collection facts.php marks featured), standard hardware. */
$sel_tier = isset( $collections[ $tier_param ] ) ? $tier_param : 'signature';
$sel_feet = $shapes[ $sel_shape ]['feet'];
$sel_hw   = 'No preference';

$feet_min     = 4;   // UI clamp only
$feet_max     = 40;  // UI clamp only
$feet_presets = array( 6, 8, 10, 12, 14, 16, 18, 20 );

/* Initial estimate, server-rendered so a no-JS visitor still sees a price:
   running feet -> sq ft of cabinetry (x sqft_per_rft) -> x per-sq-ft band. */
list( $init_low, $init_high, $init_sqft ) = alea_kitchen_total( $sel_feet, $sel_tier );
$init_sub = strtoupper( $shapes[ $sel_shape ]['label'] . ' / ' . $sel_feet . ' RFT ≈ ' . $init_sqft . ' SQ FT / ' . $collections[ $sel_tier ]['name'] . ' / ' . $sel_hw );

/* The whole market's band, for copy that must trace to facts.php. */
$band_min = $collections['essential']['from'];
$band_max = $collections['atelier']['to'];

/* Initial WhatsApp message mirrors what the JS rebuilds on every change. */
$init_wa_msg = 'Hi ALEA, I configured a kitchen on your cost calculator: '
	. $shapes[ $sel_shape ]['label'] . ', ' . $sel_feet . ' running feet (about ' . $init_sqft . ' sq ft of cabinetry), '
	. $collections[ $sel_tier ]['name'] . ' collection, ' . $sel_hw . ' hardware. '
	. 'Estimate shown: ₹' . alea_inr( $init_low ) . '–₹' . alea_inr( $init_high ) . '. '
	. 'Please send me the itemised estimate.';

/* ---- Config blob for the page script. ALL numbers the JS uses live here. ---- */
$axp_cfg = array(
	'shapes'   => array(),
	'tiers'    => array(),
	'hw'       => array( 'std' => $hw_std, 'up' => $hw_up ),
	'sqftPerRft' => $f['sqft_per_rft'],
	'feetMin'  => $feet_min,
	'feetMax'  => $feet_max,
	'wa'       => $f['whatsapp'],
	'initial'  => array(
		'shape' => $sel_shape,
		'feet'  => $sel_feet,
		'tier'  => $sel_tier,
		'hw'    => $sel_hw,
	),
);
foreach ( $shapes as $slug => $s ) {
	$axp_cfg['shapes'][ $slug ] = array( 'label' => $s['label'], 'feet' => $s['feet'] );
}
foreach ( $collections as $slug => $c ) {
	$axp_cfg['tiers'][ $slug ] = array( 'name' => $c['name'], 'from' => $c['from'], 'to' => $c['to'] );
}

/* Line-drawn shape icons: inline SVG constants authored here, stroke inherits
   the chip text colour. These are NOT media-library images — do not swap in a
   catalogue picture. images.php holds no shape iconography, and its one
   'diagram-l' entry is a layout diagram, not a chip icon. */
$shape_icons = array(
	'straight' => '<path d="M3 18h18"/>',
	'l'        => '<path d="M4 5v13h16"/>',
	'parallel' => '<path d="M3 6h18M3 18h18"/>',
	'u'        => '<path d="M4 5v13h16V5"/>',
	'island'   => '<path d="M4 5v13h16"/><rect x="11" y="8" width="7" height="4"/>',
	'g'        => '<path d="M4 5v13h16V5h-6"/>',
);

/* ---- FAQ: one array drives both the visible answers and the JSON-LD,
        so the schema can never drift from the page text. ---- */
$faqs = array(
	array(
		'q' => 'How accurate is this estimate?',
		'a' => 'It is a guide range, not a quotation. We first convert your running feet into square feet of cabinetry — this assumes standard base + wall units, about ' . alea_fact( 'sqft_per_rft' ) . ' sq ft of cabinetry per running foot — and then multiply by the per-sq-ft rate band of the collection you choose: ₹' . alea_inr( $band_min ) . ' to ₹' . alea_inr( $band_max ) . ' per sq ft depending on collection. The exact figure inside the band depends on your finishes and internal fittings, and is confirmed after a free site measurement at your home.',
	),
	array(
		'q' => 'Why do I see a range instead of one price?',
		'a' => 'Within each collection the per-sq-ft rate moves with the shutter finish, the internal fittings, and how many drawers you choose over shelves. The bottom of the range is the simplest build of that collection; the top is the fullest. Your itemised estimate narrows the range to a single figure.',
	),
	array(
		'q' => 'Will the final quote be higher than this range?',
		'a' => 'Not for the work this estimate covers. The range includes the carcass, shutters, hardware, edge banding, installation and the ' . $f['warranty_years'] . '-year written warranty. The countertop, chimney, hob, sink, appliances and civil work are not part of any kitchen estimate on this page — they are quoted as separate line items, so nothing is hidden inside the kitchen price.',
	),
	array(
		'q' => 'Do I have to share my phone number to see the price?',
		'a' => 'No. The range on this page is calculated with nothing asked of you. Share a number only if you want the estimate itemised and sent to you on WhatsApp, or if you want to book a free site measurement.',
	),
);

$faq_schema = array(
	'@context'   => 'https://schema.org',
	'@type'      => 'FAQPage',
	'mainEntity' => array(),
);
foreach ( $faqs as $fq ) {
	$faq_schema['mainEntity'][] = array(
		'@type'          => 'Question',
		'name'           => $fq['q'],
		'acceptedAnswer' => array(
			'@type' => 'Answer',
			'text'  => $fq['a'],
		),
	);
}
?>
<div class="ax-root">

<style data-no-optimize="1">
.axp-shapes .ax-chip{flex-direction:column;gap:.4rem;padding:.75rem .5rem}
.axp-shapes svg{width:26px;height:26px;display:block;stroke:currentColor;fill:none;stroke-width:1.5;stroke-linecap:square}
.axp-tiers .ax-chip{flex-direction:column;align-items:flex-start;text-align:left;gap:.35rem;padding:.875rem 1rem}
.axp-tiers .axp-char{font-family:Georgia,'Times New Roman',serif;font-style:italic;font-weight:400;font-size:.8125rem;line-height:1.45;letter-spacing:normal;text-transform:none}
.axp-feetrow{display:flex;flex-wrap:wrap;align-items:center;gap:.5rem}
.axp-feetrow .ax-stepper{flex:0 0 auto}
@media (max-width:639px){.axp-tiers{grid-template-columns:minmax(0,1fr)}}
</style>

	<!-- ============ TOOL-AS-HERO: minimal head, then the instrument ============ -->
	<section class="ax-section ax-section--tight">
		<div class="ax-wrap">
			<div class="ax-head ax-mb-4">
				<p class="ax-eyebrow">Kitchen cost calculator</p>
				<h1 class="ax-h2">Your kitchen, priced before anyone asks your name.</h1>
				<p class="ax-lead">A live guide range built from the same per-sq-ft rates our factory quotes on paper. Adjust it; the price follows.</p>
			</div>
		</div>
	</section>

	<section class="ax-section ax-section--flush-top ax-section--tight">
		<div class="ax-wrap">
			<div class="ax-estimator ax-estimator--split" id="axp-estimator">
				<div class="ax-estimator__main">
					<div class="ax-estimator__head">
						<h2 class="ax-estimator__title">Build your estimate</h2>
					</div>
					<div class="ax-estimator__body">

						<!-- 01 · SHAPE -->
						<div class="ax-estimator__group">
							<p class="ax-estimator__q"><span>Kitchen shape</span><span class="ax-estimator__q-index">01 / 04</span></p>
							<div class="ax-chips ax-chips--cols axp-shapes" id="axp-shape-chips">
								<?php foreach ( $shapes as $slug => $s ) : ?>
								<label class="ax-chip"<?php echo ( $slug === $sel_shape ) ? ' data-selected="true"' : ''; ?>>
									<input class="ax-chip__input" type="radio" name="axp-shape" value="<?php echo esc_attr( $slug ); ?>"<?php checked( $slug, $sel_shape ); ?>>
									<svg viewBox="0 0 24 24" aria-hidden="true"><?php echo $shape_icons[ $slug ]; // constant markup ?></svg>
									<span><?php echo esc_html( $s['label'] ); ?></span>
								</label>
								<?php endforeach; ?>
							</div>
						</div>

						<!-- 02 · SIZE -->
						<div class="ax-estimator__group">
							<p class="ax-estimator__q"><span>Size in running feet</span><span class="ax-estimator__q-index">02 / 04</span></p>
							<div class="ax-chips" id="axp-feet-chips">
								<?php foreach ( $feet_presets as $ft ) : ?>
								<label class="ax-chip"<?php echo ( $ft === $sel_feet ) ? ' data-selected="true"' : ''; ?>>
									<input class="ax-chip__input" type="radio" name="axp-feet" value="<?php echo esc_attr( $ft ); ?>"<?php checked( $ft, $sel_feet ); ?>>
									<span><?php echo esc_html( $ft ); ?> ft</span>
								</label>
								<?php endforeach; ?>
							</div>
							<div class="axp-feetrow ax-mt-3">
								<span class="ax-stepper">
									<button type="button" class="ax-stepper__btn" id="axp-dec" aria-label="One running foot less">&minus;</button>
									<span class="ax-stepper__value" id="axp-feetval" aria-live="polite"><?php echo esc_html( $sel_feet ); ?> ft</span>
									<button type="button" class="ax-stepper__btn" id="axp-inc" aria-label="One running foot more">+</button>
								</span>
								<span class="ax-help ax-mt-0">Fine-tune by the foot</span>
							</div>
							<p class="ax-help" id="axp-sizenote">Prefilled default for <?php echo esc_html( strtoupper( $shapes[ $sel_shape ]['label'] ) ); ?>: <?php echo esc_html( $sel_feet ); ?> running feet — adjust to match your kitchen.</p>
							<p class="ax-help">Running feet = the length of your counter run along the wall, base and wall units together.</p>
						</div>

						<!-- 03 · COLLECTION -->
						<div class="ax-estimator__group">
							<p class="ax-estimator__q"><span>Collection</span><span class="ax-estimator__q-index">03 / 04</span></p>
							<div class="ax-chips ax-chips--cols axp-tiers" id="axp-tier-chips">
								<?php foreach ( $collections as $slug => $c ) : ?>
								<label class="ax-chip"<?php echo ( $slug === $sel_tier ) ? ' data-selected="true"' : ''; ?>>
									<input class="ax-chip__input" type="radio" name="axp-tier" value="<?php echo esc_attr( $slug ); ?>"<?php checked( $slug, $sel_tier ); ?>>
									<span><?php echo esc_html( $c['name'] ); ?></span>
									<span class="axp-char"><?php echo esc_html( $c['character'] ); ?></span>
									<span class="ax-chip__delta">₹<?php echo esc_html( alea_inr( $c['from'] ) ); ?>–<?php echo esc_html( alea_inr( $c['to'] ) ); ?> / sq ft</span>
								</label>
								<?php endforeach; ?>
							</div>
						</div>

						<!-- 04 · HARDWARE -->
						<div class="ax-estimator__group">
							<p class="ax-estimator__q"><span>Hardware preference</span><span class="ax-estimator__q-index">04 / 04</span></p>
							<div class="ax-chips" id="axp-hw-chips">
								<label class="ax-chip" data-selected="true">
									<input class="ax-chip__input" type="radio" name="axp-hw" value="No preference" checked>
									<span>No preference</span>
									<span class="ax-chip__delta">We advise</span>
								</label>
								<label class="ax-chip">
									<input class="ax-chip__input" type="radio" name="axp-hw" value="<?php echo esc_attr( $hw_std ); ?>">
									<span><?php echo esc_html( $hw_std ); ?></span>
								</label>
								<label class="ax-chip">
									<input class="ax-chip__input" type="radio" name="axp-hw" value="<?php echo esc_attr( $hw_up ); ?>">
									<span><?php echo esc_html( $hw_up ); ?></span>
								</label>
							</div>
							<p class="ax-help">We fit <?php echo esc_html( $hw_std ); ?> and <?php echo esc_html( $hw_up ); ?> hinges, runners and soft-close systems. Tell us if you prefer one and we will note it &mdash; the exact hardware for your kitchen is specified line by line in your quotation.</p>
						</div>

					</div>
					<div class="ax-estimator__foot">
						Guide estimate — not a quotation. Assumes standard base + wall units — about <?php echo esc_html( alea_fact( 'sqft_per_rft' ) ); ?> sq ft of cabinetry per running foot. Confirmed after a free site measurement at your home. Rates set at our factory in <?php echo esc_html( $f['factory_place'] ); ?>.
					</div>
				</div>

				<!-- Live price: docked to the viewport bottom on mobile, sticky rail on desktop -->
				<div class="ax-estimator__side">
					<div class="ax-price" id="axp-price">
						<div class="ax-price__body" aria-live="polite">
							<span class="ax-price__label">Your guide estimate</span>
							<span class="ax-price__figure" id="axp-figure">₹<?php echo esc_html( alea_inr( $init_low ) ); ?> – ₹<?php echo esc_html( alea_inr( $init_high ) ); ?></span>
							<span class="ax-price__sub" id="axp-sub"><?php echo esc_html( $init_sub ); ?></span>
						</div>
						<div class="ax-price__cta">
							<a class="ax-btn ax-btn--primary" href="#itemised">Get this itemised on WhatsApp</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- ============ WHAT THE ESTIMATE COVERS ============ -->
	<section class="ax-section ax-section--ruled">
		<div class="ax-wrap">
			<div class="ax-head">
				<p class="ax-eyebrow">Read before you compare quotes</p>
				<h2 class="ax-h2">What this estimate covers — and what it doesn't.</h2>
				<p class="ax-lead">Most kitchen quotes look cheaper by leaving things out. Ours is only for the kitchen itself, and says so.</p>
			</div>
			<div class="ax-included">
				<div class="ax-included__col ax-included--in">
					<h3 class="ax-included__title">Included in the estimate</h3>
					<ul class="ax-included__list">
						<li>Cabinet carcass</li>
						<li>Shutters</li>
						<li><?php echo esc_html( $hw_std . ' / ' . $hw_up ); ?> hardware</li>
						<li>Edge banding</li>
						<li>Installation (<?php echo esc_html( $f['install_days'] ); ?>-day install)</li>
						<li><?php echo esc_html( $f['warranty_years'] ); ?>-year written warranty</li>
					</ul>
				</div>
				<div class="ax-included__col ax-included--out">
					<h3 class="ax-included__title">Not included — quoted separately</h3>
					<ul class="ax-included__list">
						<li>Countertop</li>
						<li>Chimney</li>
						<li>Hob</li>
						<li>Sink</li>
						<li>Appliances</li>
						<li>Civil work (plumbing, electrical, tiling)</li>
					</ul>
				</div>
			</div>

			<div class="ax-mt-6">
				<p class="ax-mono--label ax-mb-3">How we calculate this</p>
				<p class="ax-prose">Your estimate is your running feet converted into square feet of cabinetry, then multiplied by the per-sq-ft band of the collection you chose. The conversion assumes standard base + wall units — about <?php echo esc_html( alea_fact( 'sqft_per_rft' ) ); ?> sq ft of cabinetry per running foot. The bands are the factory's own rates:</p>
				<div class="ax-spectable--rows ax-mt-4" style="max-width:32rem">
					<?php foreach ( $collections as $slug => $c ) : ?>
					<div class="ax-spectable__row">
						<span class="ax-spectable__key"><?php echo esc_html( $c['name'] ); ?></span>
						<span class="ax-spectable__val">₹<?php echo esc_html( alea_inr( $c['from'] ) ); ?>–<?php echo esc_html( alea_inr( $c['to'] ) ); ?> / sq ft</span>
					</div>
					<?php endforeach; ?>
				</div>
				<?php list( $eg_low, $eg_high, $eg_sqft ) = alea_kitchen_total( 12, 'signature' ); ?>
				<p class="ax-help ax-mt-3">Worked example: 12 running feet &times; <?php echo esc_html( alea_fact( 'sqft_per_rft' ) ); ?> = <?php echo esc_html( $eg_sqft ); ?> sq ft of cabinetry &times; ₹<?php echo esc_html( alea_inr( $collections['signature']['from'] ) ); ?>–<?php echo esc_html( alea_inr( $collections['signature']['to'] ) ); ?> per sq ft (<?php echo esc_html( $collections['signature']['name'] ); ?>) = ₹<?php echo esc_html( alea_inr( $eg_low ) ); ?> – ₹<?php echo esc_html( alea_inr( $eg_high ) ); ?>.</p>
			</div>
		</div>
	</section>

	<!-- ============ LEAD GATE — after the value is shown ============ -->
	<section class="ax-section ax-section--sheet ax-section--ruled" id="itemised">
		<div class="ax-wrap">
			<div class="ax-grid ax-grid--2 ax-grid--loose">
				<div>
					<p class="ax-eyebrow">You've seen the number — now get it in writing</p>
					<h2 class="ax-h2">Get this estimate itemised on WhatsApp.</h2>
					<p class="ax-lead ax-mt-4">Your configuration and range travel with the message — cabinet by cabinet, with the <?php echo esc_html( $f['warranty_years'] ); ?>-year written warranty stated. Ask for the written warranty terms on your free site visit.</p>
					<div class="ax-btnrow ax-mt-5">
						<a class="ax-btn ax-btn--wa ax-btn--lg" id="axp-wa" href="<?php echo esc_url( alea_wa_link( $init_wa_msg ) ); ?>" target="_blank" rel="noopener">WhatsApp my configuration</a>
						<a class="ax-btn ax-btn--ghost ax-btn--lg" href="<?php echo esc_url( 'tel:' . $f['phone_tel'] ); ?>">Call <?php echo esc_html( $f['phone_display'] ); ?></a>
					</div>
					<p class="ax-btn-note">No obligation — your configuration is included in the message</p>
				</div>
				<div>
					<div class="ax-form ax-form--card">
						<h3 class="ax-form__title">Prefer we send it to you?</h3>
						<p class="ax-form__note">Leave your details and we get back with the itemised estimate</p>
						<?php echo do_shortcode( '[contact-form-7 id="7dcf010" title="Home Page form"]' ); ?>
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- ============ FAQ — the estimate's accuracy ============ -->
	<section class="ax-section ax-section--ruled">
		<div class="ax-wrap ax-wrap--narrow">
			<div class="ax-head">
				<p class="ax-eyebrow">About this estimate</p>
				<h2 class="ax-h2">Four honest answers about the number above.</h2>
			</div>
			<div class="ax-faq">
				<?php foreach ( $faqs as $fq ) : ?>
				<details class="ax-faq__item">
					<summary class="ax-faq__q"><?php echo esc_html( $fq['q'] ); ?></summary>
					<div class="ax-faq__a"><p><?php echo esc_html( $fq['a'] ); ?></p></div>
				</details>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<script type="application/ld+json"><?php echo wp_json_encode( $faq_schema, JSON_HEX_TAG | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ); ?></script>

	<script type="application/json" id="axp-cfg" data-no-optimize="1"><?php echo wp_json_encode( $axp_cfg, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT ); ?></script>

	<script data-no-optimize="1" data-no-defer="1">
	(function () {
		'use strict';
		try {
			var cfgEl = document.getElementById('axp-cfg');
			if (!cfgEl) { return; }
			var cfg = JSON.parse(cfgEl.textContent || '{}');
			if (!cfg || !cfg.tiers || !cfg.shapes || !cfg.sqftPerRft) { return; }

			var state = {
				shape: cfg.initial.shape,
				feet:  cfg.initial.feet,
				tier:  cfg.initial.tier,
				hw:    cfg.initial.hw
			};

			var figure   = document.getElementById('axp-figure');
			var sub      = document.getElementById('axp-sub');
			var panel    = document.getElementById('axp-price');
			var sizeNote = document.getElementById('axp-sizenote');
			var feetVal  = document.getElementById('axp-feetval');
			var waBtn    = document.getElementById('axp-wa');
			if (!figure || !panel) { return; }

			var shown = { low: 0, high: 0 };
			var rafId = null;
			var flashTimer = null;

			/* Indian-style grouping: 145000 -> 1,45,000 */
			function inr(n) {
				n = Math.max(0, Math.round(n));
				var s = String(n);
				if (s.length <= 3) { return s; }
				var last3 = s.slice(-3);
				var rest = s.slice(0, -3).replace(/\B(?=(\d{2})+(?!\d))/g, ',');
				return rest + ',' + last3;
			}

			function render(low, high) {
				figure.textContent = '₹' + inr(low) + ' – ₹' + inr(high);
			}

			/* 220ms count from the previous figure to the new one. */
			function animateTo(low, high) {
				if (rafId) { window.cancelAnimationFrame(rafId); rafId = null; }
				var fromL = shown.low, fromH = shown.high;
				shown.low = low; shown.high = high;
				if (!window.requestAnimationFrame || (fromL === 0 && fromH === 0)) {
					render(low, high);
					return;
				}
				var t0 = null, DUR = 220;
				panel.classList.add('is-updating');
				if (flashTimer) { window.clearTimeout(flashTimer); }
				flashTimer = window.setTimeout(function () {
					panel.classList.remove('is-updating');
				}, DUR + 40);
				function step(ts) {
					if (t0 === null) { t0 = ts; }
					var p = Math.min(1, (ts - t0) / DUR);
					render(fromL + (low - fromL) * p, fromH + (high - fromH) * p);
					if (p < 1) { rafId = window.requestAnimationFrame(step); }
				}
				rafId = window.requestAnimationFrame(step);
			}

			function syncChips(name, value) {
				var inputs = document.querySelectorAll('input[name="' + name + '"]');
				for (var i = 0; i < inputs.length; i++) {
					var match = String(inputs[i].value) === String(value);
					inputs[i].checked = match;
					var chip = inputs[i].closest ? inputs[i].closest('.ax-chip') : inputs[i].parentNode;
					if (chip && chip.setAttribute) {
						chip.setAttribute('data-selected', match ? 'true' : 'false');
					}
				}
			}

			function recalc(animate) {
				var tier = cfg.tiers[state.tier];
				var shape = cfg.shapes[state.shape];
				if (!tier || !shape) { return; }
				/* running feet -> sq ft of cabinetry -> ₹ range (per-sq-ft rates) */
				var sqft = Math.round(state.feet * cfg.sqftPerRft);
				var low = sqft * tier.from;
				var high = sqft * tier.to;
				if (animate) { animateTo(low, high); } else { shown.low = low; shown.high = high; render(low, high); }
				if (sub) {
					sub.textContent = (shape.label + ' / ' + state.feet + ' RFT ≈ ' + sqft + ' SQ FT / ' + tier.name + ' / ' + state.hw).toUpperCase();
				}
				if (feetVal) { feetVal.textContent = state.feet + ' ft'; }
				if (waBtn) {
					var msg = 'Hi ALEA, I configured a kitchen on your cost calculator: '
						+ shape.label + ', ' + state.feet + ' running feet (about ' + sqft + ' sq ft of cabinetry), '
						+ tier.name + ' collection, ' + state.hw + ' hardware. '
						+ 'Estimate shown: ₹' + inr(low) + '–₹' + inr(high) + '. '
						+ 'Please send me the itemised estimate.';
					waBtn.href = 'https://wa.me/' + cfg.wa + '?text=' + encodeURIComponent(msg);
				}
			}

			function setFeet(n, fromPreset) {
				n = Math.round(n);
				if (isNaN(n)) { return; }
				n = Math.min(cfg.feetMax, Math.max(cfg.feetMin, n));
				state.feet = n;
				if (!fromPreset) { syncChips('axp-feet', n); }
				recalc(true);
			}

			function onGroupChange(name, handler) {
				var inputs = document.querySelectorAll('input[name="' + name + '"]');
				for (var i = 0; i < inputs.length; i++) {
					inputs[i].addEventListener('change', function (e) {
						try {
							syncChips(name, e.target.value);
							handler(e.target.value);
						} catch (err) { /* never throw */ }
					});
				}
			}

			onGroupChange('axp-shape', function (v) {
				state.shape = v;
				var shape = cfg.shapes[v];
				if (shape) {
					state.feet = shape.feet;
					syncChips('axp-feet', shape.feet);
					if (sizeNote) {
						sizeNote.textContent = 'Prefilled default for ' + shape.label.toUpperCase() + ': ' + shape.feet + ' running feet — adjust to match your kitchen.';
					}
				}
				recalc(true);
			});
			onGroupChange('axp-feet', function (v) { setFeet(parseInt(v, 10), true); });
			onGroupChange('axp-tier', function (v) { state.tier = v; recalc(true); });
			onGroupChange('axp-hw', function (v) { state.hw = v; recalc(true); });

			var dec = document.getElementById('axp-dec');
			var inc = document.getElementById('axp-inc');
			if (dec) { dec.addEventListener('click', function () { try { setFeet(state.feet - 1, false); } catch (e) {} }); }
			if (inc) { inc.addEventListener('click', function () { try { setFeet(state.feet + 1, false); } catch (e) {} }); }

			/* Settle initial state (covers a stale back-forward-cache form). */
			syncChips('axp-shape', state.shape);
			syncChips('axp-tier', state.tier);
			syncChips('axp-hw', state.hw);
			syncChips('axp-feet', state.feet);
			recalc(false);
		} catch (e) { /* defensive: the server-rendered price still stands */ }
	})();
	</script>

</div>
