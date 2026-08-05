<?php
/**
 * ALEA — Pricing page template.
 * URL: /modular-kitchen-price/
 *
 * Included inside <main class="alea-main"> by the page shell.
 * Every business number on this page is read from facts.php or computed
 * from facts.php values in the PHP below. Nothing is hard-coded.
 */

require_once __DIR__ . '/facts.php';

$ax_f      = alea_facts();
$ax_cols   = $ax_f['collections'];
$ax_phone  = alea_fact( 'phone_display' );
$ax_tel    = 'tel:' . alea_fact( 'phone_tel' );
$ax_wa     = alea_wa_link( "Hi ALEA, I have a question about your kitchen price list." );
$ax_calc   = home_url( '/kitchen-cost-calculator/' );
$ax_visit  = home_url( '/book-a-free-design-visit/' );
$ax_years  = alea_fact( 'years_experience' );
$ax_since  = alea_fact( 'alea_since' );
$ax_est    = alea_fact( 'founded_furniture' );
$ax_sqft   = alea_fact( 'factory_sqft' );
$ax_place  = alea_fact( 'factory_place' );
$ax_wyrs   = alea_fact( 'warranty_years' );
$ax_days   = alea_fact( 'install_days' );
$ax_brands = implode( ' and ', (array) $ax_f['hardware_brands'] );
$ax_areas  = implode( ', ', (array) $ax_f['service_area'] );

/* Overall band across all collections, computed. */
$ax_min = null;
$ax_max = null;
foreach ( $ax_cols as $ax_c ) {
	$ax_min = ( null === $ax_min ) ? (int) $ax_c['from'] : min( $ax_min, (int) $ax_c['from'] );
	$ax_max = ( null === $ax_max ) ? (int) $ax_c['to'] : max( $ax_max, (int) $ax_c['to'] );
}
$ax_band_all = '₹' . alea_inr( $ax_min ) . '–' . alea_inr( $ax_max );

/* Worked examples — running feet -> sq ft -> ₹, via alea_kitchen_total().
   The rft-to-sqft assumption comes from facts.php (sqft_per_rft). */
$ax_sqft_rft = (int) alea_fact( 'sqft_per_rft' );
$ax_ex_defs  = array(
	array( 'essential', 10 ),
	array( 'signature', 12 ),
	array( 'atelier', 14 ),
);
$ax_examples = array();
foreach ( $ax_ex_defs as $ax_i => $ax_d ) {
	$ax_c   = $ax_cols[ $ax_d[0] ];
	$ax_rft = (int) $ax_d[1];
	list( $ax_ex_lo, $ax_ex_hi, $ax_ex_sq ) = alea_kitchen_total( $ax_rft, $ax_d[0] );
	$ax_examples[] = array(
		'num'  => sprintf( '%02d', $ax_i + 1 ),
		'name' => $ax_c['name'],
		'rft'  => $ax_rft,
		'sqft' => $ax_ex_sq,
		'from' => (int) $ax_c['from'],
		'to'   => (int) $ax_c['to'],
		'low'  => $ax_ex_lo,
		'high' => $ax_ex_hi,
	);
}

/* EMI translation of the Signature example (index 1). Simple division —
   the assumptions are stated in the visible text beside the figure. */
$ax_sig_ex     = $ax_examples[1];
$ax_emi_months = 36;
$ax_sig_mid    = (int) round( ( $ax_sig_ex['low'] + $ax_sig_ex['high'] ) / 2 );
$ax_emi        = (int) round( $ax_sig_mid / $ax_emi_months );

/* Per-collection band strings for FAQ text. */
$ax_bands_txt = array();
foreach ( $ax_cols as $ax_c ) {
	$ax_bands_txt[] = $ax_c['name'] . ' ₹' . alea_inr( $ax_c['from'] ) . '–' . alea_inr( $ax_c['to'] );
}
$ax_bands_txt = implode( ', ', $ax_bands_txt );

/* FAQ — one source array drives BOTH the visible answers and the JSON-LD,
   so they can never disagree. Answers avoid apostrophes so the mono-number
   wrapper below cannot touch an HTML entity. */
$ax_faqs = array(
	array(
		'q' => 'What does a price per square foot mean?',
		'a' => 'The band prices one square foot of finished cabinetry — carcass, shutter, hardware and installation together — at ' . $ax_bands_txt . ' per sq ft. To estimate a whole kitchen from its length, multiply its running feet by ' . $ax_sqft_rft . ' to get the cabinetry area, then multiply that area by the band of the collection you choose. The conversion assumes standard base + wall units — about ' . $ax_sqft_rft . ' sq ft of cabinetry per running foot. The exact figure follows a free site measurement at your home.',
	),
	array(
		'q' => 'Why is the price a band and not one number?',
		'a' => 'Within each collection, the finish, colour and internal fittings you choose move the figure inside the band. The floor and the ceiling of each band are fixed by the factory, so the range you calculate from this page is the same range your quotation is built from.',
	),
	array(
		'q' => 'Is the per-sq-ft price the final price of my kitchen?',
		'a' => 'It covers the cabinetry: carcasses, shutters, factory edge banding, ' . $ax_brands . ' soft-close hardware, delivery, installation by our own team, and the ' . $ax_wyrs . '-year written warranty. The countertop, chimney, hob, sink, faucet and appliances are not included — they are itemised separately on the same quotation, so nothing appears later. Ask for the written warranty terms on your free site visit.',
	),
	array(
		'q' => 'Do you charge for the estimate or the site visit?',
		'a' => 'No. The online estimate is free and shows you a live range before anyone asks for your phone number, and the site visit and measurement at your home is free with no obligation. You can also visit our factory at ' . $ax_place . ' and watch kitchens being built before you pay anything.',
	),
);

/* Wrap digit runs (and rupee bands) in .ax-mono AFTER escaping, so every
   number in FAQ text renders mono while the text content still matches the
   JSON-LD exactly. */
$ax_mono_wrap = function ( $txt ) {
	$safe = esc_html( $txt );
	return preg_replace(
		'/₹?\d[\d,]*(?:\s?–\s?₹?\d[\d,]*)?/u',
		'<span class="ax-mono">$0</span>',
		$safe
	);
};

$ax_faq_ld = array(
	'@context'   => 'https://schema.org',
	'@type'      => 'FAQPage',
	'mainEntity' => array(),
);
foreach ( $ax_faqs as $ax_q ) {
	$ax_faq_ld['mainEntity'][] = array(
		'@type'          => 'Question',
		'name'           => $ax_q['q'],
		'acceptedAnswer' => array(
			'@type' => 'Answer',
			'text'  => $ax_q['a'],
		),
	);
}
?>
<div class="ax-root ax-has-stickybar ax-has-pricestrip">

	<!-- ================================================== HERO -->
	<section class="ax-hero ax-hero--short">
		<img class="ax-hero__img"
			src="<?php echo esc_url( home_url( '/wp-content/uploads/2022/09/Alea-Modular-Kitchen-Wardrobes-1.jpg' ) ); ?>"
			alt="Installed ALEA modular kitchen in a Tricity home"
			loading="eager" fetchpriority="high">
		<div class="ax-hero__inner">
			<p class="ax-eyebrow">Factory price list</p>
			<h1 class="ax-hero__title">A kitchen price list published by the factory that makes them.</h1>
			<p class="ax-hero__sub">Every ALEA kitchen is built in our own <span class="ax-mono"><?php echo esc_html( $ax_sqft ); ?></span> sq ft factory at <?php echo esc_html( $ax_place ); ?> — and priced in public, from <span class="ax-mono"><?php echo esc_html( $ax_band_all ); ?></span> per sq ft of cabinetry.</p>
			<div class="ax-hero__actions">
				<a class="ax-btn ax-btn--primary ax-btn--lg" href="<?php echo esc_url( $ax_calc ); ?>">Get my price in 60 seconds</a>
				<a class="ax-btn ax-btn--ghost ax-btn--lg" href="<?php echo esc_url( $ax_visit ); ?>">Book a free site visit</a>
			</div>
			<p class="ax-hero__credit">ALEA since <?php echo esc_html( $ax_since ); ?> / furniture makers since <?php echo esc_html( $ax_est ); ?> / <?php echo esc_html( $ax_sqft ); ?> sq ft / <?php echo esc_html( $ax_place ); ?></p>
		</div>
	</section>

	<!-- ================================================== SPEC STRIP -->
	<section class="ax-section ax-section--tight ax-section--ruled-b">
		<div class="ax-wrap">
			<div class="ax-specstrip">
				<div class="ax-specstrip__item">
					<span class="ax-specstrip__label">Own factory</span>
					<span class="ax-specstrip__value"><?php echo esc_html( $ax_sqft ); ?><span class="ax-specstrip__unit">sq ft</span></span>
				</div>
				<div class="ax-specstrip__item">
					<span class="ax-specstrip__label">Written warranty</span>
					<span class="ax-specstrip__value"><?php echo esc_html( $ax_wyrs ); ?><span class="ax-specstrip__unit">years</span></span>
				</div>
				<div class="ax-specstrip__item">
					<span class="ax-specstrip__label">Installation</span>
					<span class="ax-specstrip__value"><?php echo esc_html( $ax_days ); ?><span class="ax-specstrip__unit">days</span></span>
				</div>
				<div class="ax-specstrip__item">
					<span class="ax-specstrip__label">Kitchens</span>
					<span class="ax-specstrip__value"><?php echo esc_html( $ax_band_all ); ?><span class="ax-specstrip__unit">/ sq ft</span></span>
				</div>
			</div>
		</div>
	</section>

	<!-- ================================================== PRICE TABLE -->
	<section class="ax-section">
		<div class="ax-wrap">
			<header class="ax-head ax-reveal">
				<p class="ax-eyebrow">Per square foot</p>
				<h2 class="ax-h2">The price list.</h2>
				<p class="ax-lead">Three collections, one factory standard. Multiply the square feet of cabinetry in your kitchen by the band of the collection you choose.</p>
			</header>
			<div class="ax-scroll-x">
				<table class="ax-spectable">
					<caption>Guide price per square foot of cabinetry, by collection</caption>
					<thead>
						<tr>
							<th scope="col">Collection</th>
							<th scope="col">Character</th>
							<th scope="col" class="ax-spectable__num">₹ / sq ft</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $ax_cols as $ax_c ) : ?>
						<tr>
							<th scope="row"><?php echo esc_html( $ax_c['name'] ); ?></th>
							<td class="ax-spectable__wrap"><?php echo esc_html( $ax_c['character'] ); ?></td>
							<td class="ax-spectable__num">₹<?php echo esc_html( alea_inr( $ax_c['from'] ) ); ?>–<?php echo esc_html( alea_inr( $ax_c['to'] ) ); ?></td>
						</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
			<p class="ax-spectable__note">Guide bands from the factory price list / exact figure after a free site measurement</p>
		</div>
	</section>

	<!-- ================================================== TIER SPECS -->
	<section class="ax-section ax-section--ruled">
		<div class="ax-wrap">
			<header class="ax-head ax-reveal">
				<p class="ax-eyebrow">What each band buys</p>
				<h2 class="ax-h2">Same factory. Same warranty. Different finish depth.</h2>
				<p class="ax-lead">Every collection leaves the same line, carries the same <?php echo esc_html( $ax_brands ); ?> hardware and the same <span class="ax-mono"><?php echo esc_html( $ax_wyrs ); ?></span>-year written warranty. The band buys the depth of finish and fitting choice.</p>
			</header>
			<div class="ax-grid ax-grid--3">

				<article class="ax-card ax-card--collection ax-reveal">
					<div class="ax-card__media">
						<img src="<?php echo esc_url( home_url( '/wp-content/uploads/2022/02/k1.jpg' ) ); ?>"
							alt="Installed ALEA modular kitchen" loading="lazy">
					</div>
					<div class="ax-card__inner">
						<h3 class="ax-card__name"><?php echo esc_html( $ax_cols['essential']['name'] ); ?></h3>
						<p class="ax-card__character"><?php echo esc_html( $ax_cols['essential']['character'] ); ?></p>
						<div class="ax-included--in ax-mt-4">
							<ul class="ax-included__list">
								<li>Factory-built carcass, machine edge-banded</li>
								<li>Laminate finishes in a focused range</li>
								<li><?php echo esc_html( $ax_brands ); ?> soft-close hardware</li>
								<li><span class="ax-mono"><?php echo esc_html( $ax_wyrs ); ?></span>-year written warranty</li>
							</ul>
						</div>
					</div>
					<div class="ax-card__price">
						<span class="ax-card__price-label">Guide price</span>
						<span class="ax-card__price-value">₹<?php echo esc_html( alea_inr( $ax_cols['essential']['from'] ) ); ?>–<?php echo esc_html( alea_inr( $ax_cols['essential']['to'] ) ); ?> / sq ft</span>
					</div>
				</article>

				<article class="ax-card ax-card--collection ax-reveal ax-reveal--d1">
					<div class="ax-card__media">
						<img src="<?php echo esc_url( home_url( '/wp-content/uploads/2022/02/L-Shape-Kitchen-01.jpg' ) ); ?>"
							alt="Installed L-shaped ALEA modular kitchen" loading="lazy">
					</div>
					<div class="ax-card__inner">
						<h3 class="ax-card__name"><?php echo esc_html( $ax_cols['signature']['name'] ); ?></h3>
						<p class="ax-card__character"><?php echo esc_html( $ax_cols['signature']['character'] ); ?></p>
						<div class="ax-included--in ax-mt-4">
							<ul class="ax-included__list">
								<li>Everything the Essential is built with</li>
								<li>A wider choice of shutter finishes and colours</li>
								<li>More internal fittings and organiser options</li>
								<li><?php echo esc_html( $ax_brands ); ?> soft-close hardware</li>
							</ul>
						</div>
					</div>
					<div class="ax-card__price">
						<span class="ax-card__price-label">Guide price</span>
						<span class="ax-card__price-value">₹<?php echo esc_html( alea_inr( $ax_cols['signature']['from'] ) ); ?>–<?php echo esc_html( alea_inr( $ax_cols['signature']['to'] ) ); ?> / sq ft</span>
					</div>
				</article>

				<article class="ax-card ax-card--collection ax-reveal ax-reveal--d2">
					<div class="ax-card__media">
						<img src="<?php echo esc_url( home_url( '/wp-content/uploads/2022/03/par.jpg' ) ); ?>"
							alt="Installed parallel ALEA modular kitchen" loading="lazy">
					</div>
					<div class="ax-card__inner">
						<h3 class="ax-card__name"><?php echo esc_html( $ax_cols['atelier']['name'] ); ?></h3>
						<p class="ax-card__character"><?php echo esc_html( $ax_cols['atelier']['character'] ); ?></p>
						<div class="ax-included--in ax-mt-4">
							<ul class="ax-included__list">
								<li>The widest finish and fitting choices we make</li>
								<li>Our most involved cabinetry, made without compromise</li>
								<li><?php echo esc_html( $ax_brands ); ?> soft-close hardware</li>
								<li><span class="ax-mono"><?php echo esc_html( $ax_wyrs ); ?></span>-year written warranty</li>
							</ul>
						</div>
					</div>
					<div class="ax-card__price">
						<span class="ax-card__price-label">Guide price</span>
						<span class="ax-card__price-value">₹<?php echo esc_html( alea_inr( $ax_cols['atelier']['from'] ) ); ?>–<?php echo esc_html( alea_inr( $ax_cols['atelier']['to'] ) ); ?> / sq ft</span>
					</div>
				</article>

			</div>
			<p class="ax-mono--label ax-mt-5">Ask for the written spec sheet of each collection on your free site visit</p>
		</div>
	</section>

	<!-- ================================================== INCLUDED / NOT -->
	<section class="ax-section ax-section--ruled">
		<div class="ax-wrap ax-wrap--narrow">
			<header class="ax-head ax-reveal">
				<p class="ax-eyebrow">The honest list</p>
				<h2 class="ax-h2">What the band includes — and what it does not.</h2>
				<p class="ax-lead">The same list our calculator uses. Everything excluded is named here and itemised separately on your quotation, so nothing appears later.</p>
			</header>
			<div class="ax-included">
				<div class="ax-included__col ax-included--in">
					<h3 class="ax-included__title">Included in the per-sq-ft price</h3>
					<ul class="ax-included__list">
						<li>Cabinet carcasses and shutters, factory-built</li>
						<li>Machine edge banding on every panel</li>
						<li><?php echo esc_html( $ax_brands ); ?> soft-close hinges and runners</li>
						<li>Delivery across <?php echo esc_html( $ax_areas ); ?></li>
						<li>Installation by our own factory team</li>
						<li><span class="ax-mono"><?php echo esc_html( $ax_wyrs ); ?></span>-year written warranty</li>
					</ul>
				</div>
				<div class="ax-included__col ax-included--out">
					<h3 class="ax-included__title">Not included — itemised separately</h3>
					<ul class="ax-included__list">
						<li>Countertop — priced by material and run</li>
						<li>Chimney and hob</li>
						<li>Sink and faucet</li>
						<li>Appliances</li>
						<li>Civil work — tiling, plumbing, electrical</li>
					</ul>
				</div>
			</div>
		</div>
	</section>

	<!-- ================================================== WORKED EXAMPLES -->
	<section class="ax-section ax-section--ruled ax-section--sheet">
		<div class="ax-wrap">
			<header class="ax-head ax-reveal">
				<p class="ax-eyebrow">Worked examples</p>
				<h2 class="ax-h2">The arithmetic, in the open.</h2>
				<p class="ax-lead">Three illustrative examples computed from the published bands — not real delivered projects. Each assumes standard base + wall units — about <span class="ax-mono"><?php echo esc_html( $ax_sqft_rft ); ?></span> sq ft of cabinetry per running foot. Your own range takes sixty seconds in the calculator.</p>
			</header>
			<div class="ax-grid ax-grid--3">
				<?php foreach ( $ax_examples as $ax_ei => $ax_ex ) : ?>
				<article class="ax-card ax-reveal<?php echo 0 < $ax_ei ? esc_attr( ' ax-reveal--d' . $ax_ei ) : ''; ?>">
					<p class="ax-mono--label">Illustrative example <?php echo esc_html( $ax_ex['num'] ); ?></p>
					<h3 class="ax-card__title ax-mt-2"><span class="ax-mono"><?php echo esc_html( $ax_ex['rft'] ); ?></span> running feet · <?php echo esc_html( $ax_ex['name'] ); ?></h3>
					<div class="ax-spectable ax-spectable--rows ax-mt-4">
						<div class="ax-spectable__row">
							<span class="ax-spectable__key">Band</span>
							<span class="ax-spectable__val">₹<?php echo esc_html( alea_inr( $ax_ex['from'] ) ); ?>–<?php echo esc_html( alea_inr( $ax_ex['to'] ) ); ?> / sq ft</span>
						</div>
						<div class="ax-spectable__row">
							<span class="ax-spectable__key"><?php echo esc_html( $ax_ex['rft'] ); ?> rft × <?php echo esc_html( $ax_sqft_rft ); ?></span>
							<span class="ax-spectable__val"><?php echo esc_html( $ax_ex['sqft'] ); ?> sq ft</span>
						</div>
						<div class="ax-spectable__row">
							<span class="ax-spectable__key"><?php echo esc_html( $ax_ex['sqft'] ); ?> × ₹<?php echo esc_html( alea_inr( $ax_ex['from'] ) ); ?></span>
							<span class="ax-spectable__val">₹<?php echo esc_html( alea_inr( $ax_ex['low'] ) ); ?></span>
						</div>
						<div class="ax-spectable__row">
							<span class="ax-spectable__key"><?php echo esc_html( $ax_ex['sqft'] ); ?> × ₹<?php echo esc_html( alea_inr( $ax_ex['to'] ) ); ?></span>
							<span class="ax-spectable__val">₹<?php echo esc_html( alea_inr( $ax_ex['high'] ) ); ?></span>
						</div>
						<div class="ax-spectable__row">
							<span class="ax-spectable__key">Working range</span>
							<span class="ax-spectable__val"><strong>₹<?php echo esc_html( alea_inr( $ax_ex['low'] ) ); ?>–<?php echo esc_html( alea_inr( $ax_ex['high'] ) ); ?></strong></span>
						</div>
					</div>
				</article>
				<?php endforeach; ?>
			</div>
			<p class="ax-mono--label ax-mt-5">Illustrative only / length × <?php echo esc_html( $ax_sqft_rft ); ?> sq ft per running foot × published band / cabinetry, before the not-included items above</p>
		</div>
	</section>

	<!-- ================================================== EMI -->
	<section class="ax-section ax-section--ruled">
		<div class="ax-wrap ax-wrap--narrow">
			<header class="ax-head ax-reveal">
				<p class="ax-eyebrow">Per month</p>
				<h2 class="ax-h2">The same number, said monthly.</h2>
				<p class="ax-lead">Take the <?php echo esc_html( $ax_sig_ex['name'] ); ?> example above. Its mid-point is <span class="ax-mono">₹<?php echo esc_html( alea_inr( $ax_sig_mid ) ); ?></span>. Divided over <span class="ax-mono"><?php echo esc_html( $ax_emi_months ); ?></span> equal monthly instalments, that is <span class="ax-mono">₹<?php echo esc_html( alea_inr( $ax_emi ) ); ?></span> a month.</p>
			</header>
			<div class="ax-spectable ax-spectable--rows">
				<div class="ax-spectable__row">
					<span class="ax-spectable__key"><?php echo esc_html( $ax_sig_ex['name'] ); ?> example, mid-point</span>
					<span class="ax-spectable__val">₹<?php echo esc_html( alea_inr( $ax_sig_mid ) ); ?></span>
				</div>
				<div class="ax-spectable__row">
					<span class="ax-spectable__key">Assumed tenure</span>
					<span class="ax-spectable__val"><?php echo esc_html( $ax_emi_months ); ?> months</span>
				</div>
				<div class="ax-spectable__row">
					<span class="ax-spectable__key">₹<?php echo esc_html( alea_inr( $ax_sig_mid ) ); ?> ÷ <?php echo esc_html( $ax_emi_months ); ?></span>
					<span class="ax-spectable__val"><strong>₹<?php echo esc_html( alea_inr( $ax_emi ) ); ?> / month</strong></span>
				</div>
			</div>
			<p class="ax-spectable__note">Illustrative arithmetic only — simple division, no interest or fees included. EMI availability and terms depend on your bank; ask on your free site visit.</p>
		</div>
	</section>

	<!-- ================================================== WHY WE PUBLISH -->
	<section class="ax-section ax-section--ruled">
		<div class="ax-wrap">
			<div class="ax-grid ax-grid--split">
				<figure class="ax-media ax-media--43 ax-reveal">
					<span class="ax-media__frame">
						<img src="<?php echo esc_url( home_url( '/wp-content/uploads/2022/04/aleaabout.jpg' ) ); ?>"
							alt="Inside the ALEA factory at Raipur Rani, Panchkula district" loading="lazy">
					</span>
					<figcaption class="ax-media__caption"><b>The factory</b> / <?php echo esc_html( $ax_place ); ?> / <?php echo esc_html( $ax_sqft ); ?> sq ft</figcaption>
				</figure>
				<div class="ax-prose ax-reveal ax-reveal--d1">
					<p class="ax-eyebrow">Why we publish prices</p>
					<h2 class="ax-h2">Most kitchen prices are a negotiation. Ours is a list.</h2>
					<p>We have made kitchens since <span class="ax-mono"><?php echo esc_html( $ax_since ); ?></span>, in a family firm that has made furniture since <span class="ax-mono"><?php echo esc_html( $ax_est ); ?></span>. When you own the factory, you know your costs — and when you know your costs, you can put the number in public and stand behind it.</p>
					<p>A published price does two useful things. It lets you rule us in or out before anyone visits anyone. And it turns the final quotation into an itemisation rather than a revelation: the same per-sq-ft bands on this page, multiplied by the measured square feet of your cabinetry, with everything excluded listed by name.</p>
					<p>If a number on this page ever disagrees with a number on your quotation, the page is wrong and the factory will correct it. That is what a price list is for.</p>
				</div>
			</div>
		</div>
	</section>

	<!-- ================================================== FAQ -->
	<section class="ax-section ax-section--ruled">
		<div class="ax-wrap ax-wrap--narrow">
			<header class="ax-head ax-reveal">
				<p class="ax-eyebrow">Price questions</p>
				<h2 class="ax-h2">Asked before, answered here.</h2>
			</header>
			<div class="ax-faq">
				<?php foreach ( $ax_faqs as $ax_q ) : ?>
				<details class="ax-faq__item">
					<summary class="ax-faq__q"><?php echo esc_html( $ax_q['q'] ); ?></summary>
					<div class="ax-faq__a">
						<p><?php echo $ax_mono_wrap( $ax_q['a'] ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside the wrapper. ?></p>
					</div>
				</details>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<script type="application/ld+json" data-no-optimize="1"><?php echo wp_json_encode( $ax_faq_ld, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ); ?></script>

	<!-- ================================================== CTA BAND -->
	<section class="ax-section ax-section--ink">
		<div class="ax-wrap ax-wrap--narrow">
			<header class="ax-head ax-reveal">
				<p class="ax-eyebrow">Next step</p>
				<h2 class="ax-h2">See your number before anyone asks for yours.</h2>
				<p class="ax-lead">Sixty seconds in the calculator for a live range — or a free measurement at your home, with no obligation either way.</p>
			</header>
			<div class="ax-btnrow">
				<a class="ax-btn ax-btn--primary ax-btn--lg" href="<?php echo esc_url( $ax_calc ); ?>">Get my price in 60 seconds</a>
				<a class="ax-btn ax-btn--wa ax-btn--lg" href="<?php echo esc_url( $ax_wa ); ?>">WhatsApp us</a>
			</div>
			<p class="ax-btn-note">Free / no obligation / or call <a class="ax-link" href="<?php echo esc_url( $ax_tel ); ?>"><span class="ax-mono"><?php echo esc_html( $ax_phone ); ?></span></a></p>
		</div>
	</section>

	<!-- Inline booking form: every page terminates in a form, never a link away. -->
	<section class="ax-section ax-section--ruled" id="alea-book">
		<div class="ax-wrap">
			<div class="ax-grid ax-grid--split">
				<div class="ax-reveal">
					<p class="ax-eyebrow">Book a free visit</p>
					<h2 class="ax-h2">Have us measure it, and the range becomes a quotation.</h2>
					<p class="ax-lead ax-mt-4">
						The bands above price the cabinetry. A free measurement at your home turns them into an
						itemised figure &mdash; or visit the factory at <?php echo esc_html( alea_fact( 'factory_place' ) ); ?>
						and see how the kitchens are built before you decide.
					</p>
					<p class="ax-btn-note ax-mt-5">Free / no obligation / <?php echo esc_html( implode( ' &middot; ', (array) alea_fact( 'service_area', array() ) ) ); ?></p>
				</div>
				<div class="ax-form ax-form--card ax-reveal">
					<h3 class="ax-form__title">Book your free measurement</h3>
					<p class="ax-form__note">Tell us your area and a good time to call.</p>
					<?php echo do_shortcode( '[contact-form-7 id="7dcf010" title="Home Page form"]' ); ?>
					<p class="ax-form__fineprint ax-mt-4">We use your details only to arrange your visit and estimate.</p>
				</div>
			</div>
		</div>
	</section>

	<!-- ================================================== MOBILE FURNITURE -->
	<div class="ax-pricestrip">
		<span class="ax-pricestrip__label">Kitchens from</span>
		<span class="ax-pricestrip__value"><?php echo esc_html( $ax_band_all ); ?> / sq ft</span>
		<a class="ax-pricestrip__cta" href="<?php echo esc_url( $ax_calc ); ?>">Get my price</a>
	</div>
	<div class="ax-stickybar">
		<a class="ax-stickybar__btn ax-stickybar__btn--call" href="<?php echo esc_url( $ax_tel ); ?>">
			<svg class="ax-btn__icon" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M6.62 10.79a15.05 15.05 0 0 0 6.59 6.59l2.2-2.2a1 1 0 0 1 1.02-.24c1.12.37 2.33.57 3.57.57a1 1 0 0 1 1 1V20a1 1 0 0 1-1 1C10.4 21 3 13.6 3 4a1 1 0 0 1 1-1h3.5a1 1 0 0 1 1 1c0 1.24.2 2.45.57 3.57a1 1 0 0 1-.25 1.02l-2.2 2.2Z"/></svg>
			Call
		</a>
		<a class="ax-stickybar__btn ax-stickybar__btn--wa" href="<?php echo esc_url( alea_wa_link() ); ?>">
			<svg class="ax-btn__icon" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12.04 2a9.9 9.9 0 0 0-8.5 14.94L2 22l5.2-1.5A9.9 9.9 0 1 0 12.04 2Zm5.77 14.06c-.24.68-1.4 1.3-1.96 1.38-.5.07-1.14.1-1.84-.12a16.7 16.7 0 0 1-1.66-.61c-2.93-1.27-4.84-4.22-4.99-4.41-.14-.2-1.19-1.58-1.19-3.02 0-1.44.75-2.15 1.02-2.44.27-.3.58-.37.78-.37h.56c.18 0 .42-.06.65.5.24.58.82 2.02.9 2.17.07.14.12.31.02.5-.1.2-.14.32-.29.49-.15.17-.31.38-.44.51-.15.15-.3.3-.13.6.17.29.76 1.24 1.63 2.01 1.12 1 2.06 1.3 2.35 1.45.3.15.47.13.64-.07.17-.2.73-.86.93-1.15.2-.3.4-.24.67-.15.27.1 1.72.81 2.01.96.3.15.49.22.56.34.07.12.07.7-.17 1.38Z"/></svg>
			WhatsApp
		</a>
	</div>

	<script data-no-optimize="1" data-no-defer="1">
	(function () {
		try {
			var root = document.querySelector('.ax-root');
			if (!root) { return; }
			if (!('IntersectionObserver' in window)) { return; }
			var targets = root.querySelectorAll('.ax-reveal');
			if (!targets.length) { return; }
			root.classList.add('ax-js');
			var io = new IntersectionObserver(function (entries) {
				entries.forEach(function (entry) {
					if (entry.isIntersecting) {
						entry.target.classList.add('is-visible');
						io.unobserve(entry.target);
					}
				});
			}, { rootMargin: '0px 0px -10% 0px', threshold: 0.05 });
			targets.forEach(function (el) { io.observe(el); });
		} catch (e) { /* never break the page */ }
	})();
	</script>

</div>
