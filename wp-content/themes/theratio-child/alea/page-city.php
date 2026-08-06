<?php
/**
 * ALEA — City template ("The Manufacturer's Record")
 * Serves /locations/panchkula|chandigarh|mohali|zirakpur/ from one file.
 * Variant arrives via $GLOBALS['alea_page_args']['city'], whitelisted below
 * with a safe default. Included inside <main class="alea-main"> by the shell;
 * header, footer and the site-wide sticky mobile bar come from the theme.
 *
 * HONESTY CONTRACT FOR THIS TEMPLATE (the whole reason it exists):
 * The ONLY city-specific facts asserted anywhere on this page are (a) the city's
 * name and (b) that ALEA serves it — and (b) is read from facts.php
 * 'service_area', never typed here. Everything else on the page is a
 * company-wide fact from facts.php: the factory and its location, the hardware
 * brands, the written warranty, the installation days, and the SAME published
 * price bands used on every other page.
 * Deliberately absent, because none of it is verified: sector names, landmarks,
 * distances or drive times, local project or customer claims, city showroom
 * addresses, city-specific delivery promises, and per-city pricing. The copy
 * says outright that there is no separate city price list, which is both true
 * and the strongest thing we can say here.
 *
 * IMAGES: every picture on this page comes from images.php by key. That file
 * holds the verified description of what is actually in each frame (the media
 * library's filenames are misleading), so no alt text or caption is written
 * here. Nothing on this page may be captioned as a delivered project, a
 * customer's home in this city, or the factory floor — the only photography
 * verified as ALEA's own is of the experience centre.
 */

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/facts.php';
require_once __DIR__ . '/images.php';

$f = alea_facts();

/* ---- Variant: whitelist, cross-checked against facts.php, safe default ----
   The four slugs below are the whitelist; the DISPLAY name for each is taken
   from facts.php 'service_area'. A city that is dropped from service_area
   therefore stops resolving here rather than continuing to claim we serve it. */
$alea_city_allowed = array( 'panchkula', 'chandigarh', 'mohali', 'zirakpur' );

$alea_cities = array();
foreach ( $f['service_area'] as $alea_area ) {
	$alea_area      = trim( (string) $alea_area );
	$alea_area_slug = strtolower( str_replace( ' ', '-', $alea_area ) );
	if ( '' !== $alea_area && in_array( $alea_area_slug, $alea_city_allowed, true ) ) {
		$alea_cities[ $alea_area_slug ] = $alea_area;
	}
}
/* Fail safe: if facts.php and the whitelist ever stop overlapping, render
   nothing rather than a page that names a city we cannot back. */
if ( empty( $alea_cities ) ) {
	return;
}

$alea_args = ( isset( $GLOBALS['alea_page_args'] ) && is_array( $GLOBALS['alea_page_args'] ) )
	? $GLOBALS['alea_page_args']
	: array();
$city_slug = isset( $alea_args['city'] ) ? strtolower( trim( (string) $alea_args['city'] ) ) : 'panchkula';
if ( ! isset( $alea_cities[ $city_slug ] ) ) {
	$city_slug = isset( $alea_cities['panchkula'] ) ? 'panchkula' : (string) key( $alea_cities );
}
$city = $alea_cities[ $city_slug ];

/* Every "how many areas" figure on this page counts the SAME filtered array the
   sibling links and the served-city whitelist use, so a count, a link and a
   served-area claim cannot disagree on one scroll. */
$city_count = count( $alea_cities );

/* Base paths. These MUST stay identical to the paths registered in
   alea_redesign_map() (functions.php), so a link and its route cannot drift:
   sibling city links are built from $city_base — '/locations/{slug}/' is
   registered there for all four city slugs — and collection links from
   $col_base, the same string page-collection.php builds its siblings from. */
$city_base = '/locations/';
$col_base  = '/modular-kitchen/';

$calc_url   = home_url( '/kitchen-cost-calculator/' );
$visit_url  = home_url( '/book-a-free-design-visit/' );
/* The city pages quote the same guide bands as everywhere else but did not
   link the page those bands are published on. Path matches functions.php. */
$price_url  = home_url( '/modular-kitchen-price/' );
$tel_href   = 'tel:' . alea_fact( 'phone_tel' );
$wa_href    = alea_wa_link( sprintf( "Hi ALEA, I'd like a free modular kitchen estimate for my home in %s.", $city ) );
$phone_disp = alea_fact( 'phone_display' );
$brands     = implode( ' and ', $f['hardware_brands'] );
/* Built from the FILTERED list, not raw service_area: every place named here is
   also a place this page can link to and count, so the prose, the sibling nav
   and the counts cannot silently disagree. */
$areas      = implode( ', ', array_values( $alea_cities ) );
$areas_dot  = implode( ' · ', array_values( $alea_cities ) );
$sqft_rft   = (int) alea_fact( 'sqft_per_rft' );
$price_unit = (string) alea_fact( 'price_unit' );

/* Published band across the whole range, derived from EVERY collection in
   facts.php rather than two hard-keyed slugs, so retiring a collection there
   cannot leave this page quoting a stale range. */
$band_low  = 0;
$band_high = 0;
foreach ( $f['collections'] as $band_col ) {
	$band_low  = $band_low ? min( $band_low, (int) $band_col['from'] ) : (int) $band_col['from'];
	$band_high = max( $band_high, (int) $band_col['to'] );
}
$band_lines = array();
foreach ( $f['collections'] as $band_slug => $band_col ) {
	$band_lines[] = $band_col['name'] . ' ' . alea_price_band( $band_slug );
}

/* One worked whole-kitchen estimate, computed via alea_kitchen_total(). The
   sq-ft-per-running-foot assumption is stated in visible text below. The
   example collection falls back to the first defined, so removing 'signature'
   from facts.php degrades instead of breaking. */
$ex_rft  = 12;
$ex_slug = isset( $f['collections']['signature'] ) ? 'signature' : (string) key( $f['collections'] );
$ex_name = isset( $f['collections'][ $ex_slug ]['name'] ) ? (string) $f['collections'][ $ex_slug ]['name'] : '';
list( $ex_low, $ex_high, $ex_sqft ) = alea_kitchen_total( $ex_rft, $ex_slug );

/* ---- Per-city presentation. Real prose per city so the city pages are not a
   find-and-replace mesh — but every SUBSTANTIVE claim in them is a
   company-wide fact from facts.php, restated in that city's own words. The
   only city-level assertion is "we serve here", which service_area backs.
   The Panchkula variant is the one page allowed to note that the factory is
   in the same district, because facts.php 'factory_place' says so verbatim —
   and it still says nothing about distance or drive time, neither of which is
   verified. */

/* Derived, never typed: the "how many places" wording, phrased so it stays
   true (and grammatical) if service_area ever shrinks to a single entry. */
$serve_phrase = ( 1 === $city_count )
	? 'the one place we serve'
	: sprintf( 'one of the %d places we serve', $city_count );

$presentation = array(
	'panchkula' => array(
		'img'   => 'kitchen-island',
		'sub'   => sprintf(
			'Built at our own factory at %1$s — the same district you live in — then delivered and installed at your home.',
			$f['factory_place']
		),
		'intro' => array(
			sprintf(
				'Panchkula is where the factory district is. Our manufacturing unit stands at %1$s, and a kitchen for a Panchkula home is made on that floor rather than bought in from an outside workshop and badged as ours.',
				$f['factory_place']
			),
			sprintf(
				'That does not buy you a different kitchen from anyone else — it buys you the same one, made where you can go and look at it. The same %1$s sq ft factory, %2$s hardware fitted as standard, a %3$d-year warranty in writing, installation in %4$d days, and the same published rates per square foot of cabinetry.',
				$f['factory_sqft'],
				$brands,
				(int) $f['warranty_years'],
				(int) $f['install_days']
			),
		),
		'how'   => 'The five steps below are the same five for every kitchen we build. Nothing in them is special to Panchkula, and we would rather say so than invent a local process.',
		/* No distance or drive-time claim: facts.php holds the factory's place
		   and nothing about how far it is from anywhere. The factory-visit
		   offer is company-wide and true for every area we serve. */
		'visit' => 'We come out to homes in Panchkula to measure — free, and with nothing to sign at the end of it. You are also welcome to visit the factory and watch kitchens being built before you commit to one.',
	),
	'chandigarh' => array(
		'img'   => 'kitchen-tall',
		'sub'   => sprintf(
			'Made at our own %1$s sq ft factory at %2$s, then delivered and installed at your home in Chandigarh.',
			$f['factory_sqft'],
			$f['factory_place']
		),
		'intro' => array(
			sprintf(
				'We serve Chandigarh from exactly one place: our own factory at %1$s. Nothing is subcontracted to a workshop we could not walk you around, and no part of a Chandigarh kitchen is made anywhere else.',
				$f['factory_place']
			),
			sprintf(
				'What arrives at your flat is what every other ALEA customer gets — %1$s hardware as standard, a %2$d-year warranty handed over in writing, installation in %3$d days, and the same published bands. We publish one set of rates instead of quoting a different number city by city.',
				$brands,
				(int) $f['warranty_years'],
				(int) $f['install_days']
			),
		),
		'how'   => 'This is our whole process, start to finish. It runs the same way for a kitchen in Chandigarh as for one anywhere else we work — we have not written a special version of it for this page.',
		'visit' => 'A designer comes to your home in Chandigarh, measures the kitchen and talks it through with you. It is free, there is no obligation, and you keep the measurements whatever you decide afterwards.',
	),
	'mohali'    => array(
		'img'   => 'kitchen-wide',
		'sub'   => sprintf(
			'Manufactured at our own factory at %1$s, delivered to Mohali and installed in %2$d days.',
			$f['factory_place'],
			(int) $f['install_days']
		),
		'intro' => array(
			sprintf(
				'Mohali is %1$s, and a Mohali kitchen is built the way every ALEA kitchen is: on our own factory floor at %2$s, in a %3$s sq ft unit you are welcome to walk through before you pay anything.',
				$serve_phrase,
				$f['factory_place'],
				$f['factory_sqft']
			),
			sprintf(
				'There is no separate Mohali price list. The bands published further down this page are the bands we quote against everywhere, %1$s hardware is fitted as standard, and the %2$d-year warranty comes in writing. Your own itemised figure follows a free measurement at your home.',
				$brands,
				(int) $f['warranty_years']
			),
		),
		'how'   => 'Five steps, in the order they actually happen. They are deliberately the same for Mohali as for every other kitchen — a process that changed by city would be a process we had made up.',
		'visit' => 'We measure at your home in Mohali, free of charge, and leave you with the numbers. If you would rather see the machines and the hardware first, come to the factory instead and we will measure afterwards.',
	),
	'zirakpur'  => array(
		/* Previously opened on a stock Western kitchen that is not ALEA's work
		   (images.php refuses it). Removed; this page reuses a verified
		   experience-centre plate rather than someone else's photograph. */
		'img'   => 'kitchen-island',
		'sub'   => sprintf(
			'Made at our own factory at %1$s, then delivered to your home in Zirakpur and fitted by arrangement with you.',
			$f['factory_place']
		),
		'intro' => array(
			sprintf(
				'For Zirakpur we work the way we work everywhere else. Your kitchen is manufactured at our own unit at %1$s, then delivered to your home and installed there in %2$d days.',
				$f['factory_place'],
				(int) $f['install_days']
			),
			sprintf(
				'The things worth checking are the same wherever you live: %1$s sq ft of our own factory behind the kitchen, %2$s hardware as standard, a %3$d-year warranty you receive in writing, and prices published per square foot of cabinetry rather than invented per locality.',
				$f['factory_sqft'],
				$brands,
				(int) $f['warranty_years']
			),
		),
		'how'   => 'Here is the whole thing, step by step. It is written city-neutral on purpose: the process for a Zirakpur kitchen is our process, unchanged.',
		'visit' => 'Someone comes to your home in Zirakpur with a tape measure, not a contract. The visit is free, nothing is signed on the day, and the factory is open to you as well if you would rather start there.',
	),
);
$pres = isset( $presentation[ $city_slug ] ) ? $presentation[ $city_slug ] : reset( $presentation );

/* Collection card images — catalogue keys only, so the description travels with
   the file. All three are verified experience-centre photography; the alts in
   images.php say "display" / "experience centre" and never a delivered project,
   a customer's home in this city, or a factory floor. */
$collection_imgs = array(
	'essential' => 'kitchen-tall',
	'signature' => 'kitchen-island',
	'atelier'   => 'timber-feature',
);

/* ---- FAQ: named for the city, honest in substance. The visible answers and
   the JSON-LD render from this ONE array, so the schema cannot drift from the
   text on the page. ---- */
$faq = array(
	array(
		'q' => sprintf( 'Do you make and install modular kitchens in %s?', $city ),
		'a' => sprintf(
			'Yes. %1$s is one of the areas we serve — %2$s. Your kitchen is manufactured at our own factory at %3$s, delivered to your home in %1$s and installed there in %4$d days. The site visit and measurement are free and carry no obligation.',
			$city,
			$areas,
			$f['factory_place'],
			(int) $f['install_days']
		),
	),
	array(
		'q' => sprintf( 'What does a modular kitchen cost in %s?', $city ),
		'a' => sprintf(
			'The same as anywhere else we work — there is no separate %1$s price list. We publish one set of rates %2$s of cabinetry: %3$s. A %4$d-running-foot kitchen — assuming standard base and wall units, about %5$d sq ft of cabinetry per running foot — works out to roughly ₹%6$s–%7$s in the %8$s band. Your own itemised figure comes after a free measurement at your home.',
			$city,
			$price_unit,
			implode( ', ', $band_lines ),
			$ex_rft,
			$sqft_rft,
			alea_inr( $ex_low ),
			alea_inr( $ex_high ),
			$ex_name
		),
	),
	array(
		'q' => sprintf( 'Where is my %s kitchen actually made?', $city ),
		'a' => sprintf(
			'At our own %1$s sq ft factory at %2$s — not at an outside workshop. We fit %3$s hardware as standard and hand over a %4$d-year warranty in writing, with the full terms in the written document you receive. You are welcome to visit the factory and watch kitchens being built before you pay anything.',
			$f['factory_sqft'],
			$f['factory_place'],
			$brands,
			(int) $f['warranty_years']
		),
	),
);

$faq_schema = array(
	'@context'   => 'https://schema.org',
	'@type'      => 'FAQPage',
	'mainEntity' => array(),
);
foreach ( $faq as $item ) {
	$faq_schema['mainEntity'][] = array(
		'@type'          => 'Question',
		'name'           => $item['q'],
		'acceptedAnswer' => array(
			'@type' => 'Answer',
			'text'  => $item['a'],
		),
	);
}
?>
<div class="ax-root">

	<style data-no-optimize="1">
	/* Page-specific: the hero's secondary text link and the whole-row city links. */
	.axp-hero-alt{margin-top:var(--sp-3);font-size:.9375rem;color:var(--ax-fg-muted)}
	.axp-hero-alt a{display:inline-block;min-height:var(--tap);line-height:var(--tap);padding-inline:2px;color:inherit}
	.axp-sibrow{min-height:var(--tap-lg,56px);align-items:center}
	.axp-sibrow--link{text-decoration:none;color:inherit}
	.axp-sibrow--link .ax-spectable__key{color:var(--ax-fg);text-decoration:underline;text-decoration-color:var(--ax-rule);text-underline-offset:.22em}
	.axp-sibrow--link:hover .ax-spectable__key{text-decoration-color:var(--ax-fg)}
	</style>

	<!-- ================================================== 1. HERO -->
	<section class="ax-hero ax-hero--short">
		<?php /* Experience-centre photography — the only verified pool — pulled
		         by key from images.php, which supplies the alt. It describes the
		         experience centre and nothing else: never a delivered project,
		         never a customer's home in this city. */ ?>
		<?php echo alea_img( $pres['img'], array( 'class' => 'ax-hero__img', 'eager' => true ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<div class="ax-hero__inner">
			<p class="ax-eyebrow">Areas we serve &mdash; <?php echo esc_html( $city ); ?></p>
			<h1 class="ax-hero__title">Modular kitchens in <?php echo esc_html( $city ); ?>, made in our own factory.</h1>
			<p class="ax-hero__sub"><?php echo esc_html( $pres['sub'] ); ?></p>
			<div class="ax-hero__actions">
				<a class="ax-btn ax-btn--primary ax-btn--lg" href="<?php echo esc_url( $calc_url ); ?>">Get my price</a>
			</div>
			<p class="axp-hero-alt">or <a href="#alea-book">book a free visit</a> &mdash; free, no obligation</p>
			<p class="ax-hero__credit">
				MADE AT <?php echo esc_html( $f['factory_place'] ); ?>
				/ <?php echo (int) $f['warranty_years']; ?>-YEAR WRITTEN WARRANTY
				/ <?php echo (int) $f['install_days']; ?>-DAY INSTALLATION
			</p>
		</div>
	</section>

	<!-- ================================================== 2. SPEC STRIP -->
	<?php /* Every cell is a company-wide fact from facts.php. Nothing here is
	         city-specific, because nothing city-specific is verified. */ ?>
	<section class="ax-section ax-section--flush" aria-label="ALEA key facts">
		<div class="ax-wrap ax-wrap--flush">
			<div class="ax-specstrip">
				<div class="ax-specstrip__item">
					<span class="ax-specstrip__label">Our own factory</span>
					<span class="ax-specstrip__value"><?php echo esc_html( $f['factory_sqft'] ); ?><span class="ax-specstrip__unit">sq ft</span></span>
				</div>
				<div class="ax-specstrip__item">
					<span class="ax-specstrip__label">Written warranty</span>
					<span class="ax-specstrip__value"><?php echo (int) $f['warranty_years']; ?><span class="ax-specstrip__unit">years</span></span>
				</div>
				<div class="ax-specstrip__item">
					<span class="ax-specstrip__label">Installation</span>
					<span class="ax-specstrip__value"><?php echo (int) $f['install_days']; ?><span class="ax-specstrip__unit">days</span></span>
				</div>
				<div class="ax-specstrip__item">
					<span class="ax-specstrip__label">Published band</span>
					<span class="ax-specstrip__value">&#8377;<?php echo esc_html( alea_inr( $band_low ) ); ?>&ndash;<?php echo esc_html( alea_inr( $band_high ) ); ?><span class="ax-specstrip__unit"><?php echo esc_html( $price_unit ); ?></span></span>
				</div>
			</div>
		</div>
	</section>

	<!-- ================================================== 3. THE CITY, HONESTLY -->
	<section class="ax-section">
		<div class="ax-wrap ax-wrap--narrow">
			<header class="ax-head ax-reveal">
				<p class="ax-eyebrow">Serving <?php echo esc_html( $city ); ?></p>
				<h2 class="ax-h2">One factory. The same kitchen, wherever you live.</h2>
			</header>
			<div class="ax-prose ax-reveal">
				<?php foreach ( $pres['intro'] as $para ) : ?>
				<p><?php echo esc_html( $para ); ?></p>
				<?php endforeach; ?>
				<?php /* Said plainly rather than implied: this page invents no local
				         detail, and the absence is the point. */ ?>
				<p>
					You will not find sector names, drive times or &ldquo;projects near you&rdquo; on this page.
					We only publish what we can stand behind: that <?php echo esc_html( $city ); ?> is one of the
					areas we serve, where your kitchen is made, what it comes with, and what it costs.
				</p>
			</div>
		</div>
	</section>

	<!-- ================================================== 4. HOW IT WORKS -->
	<section class="ax-section ax-section--ruled">
		<div class="ax-wrap">
			<header class="ax-head ax-head--wide ax-reveal">
				<p class="ax-eyebrow">How it works</p>
				<h2 class="ax-h2">How it works for <?php echo esc_html( $city ); ?> homes.</h2>
				<p class="ax-lead"><?php echo esc_html( $pres['how'] ); ?></p>
			</header>
			<ol class="ax-steps ax-reveal">
				<li class="ax-step">
					<div class="ax-step__body">
						<h3 class="ax-step__title">A free visit and measurement</h3>
						<p class="ax-step__text">
							We come to your home in <?php echo esc_html( $city ); ?>, measure the kitchen and talk
							through layout and finishes with you. Nothing is signed, and you keep the measurements
							either way.
						</p>
						<span class="ax-step__time">Free &middot; no obligation</span>
					</div>
				</li>
				<li class="ax-step">
					<div class="ax-step__body">
						<h3 class="ax-step__title">An itemised quotation</h3>
						<p class="ax-step__text">
							Your kitchen is priced line by line against the published bands on this page &mdash; every
							panel, finish and fitting named on the quotation, so nothing appears later that you had
							not seen.
						</p>
						<span class="ax-step__time">Priced off the published bands</span>
					</div>
				</li>
				<li class="ax-step">
					<div class="ax-step__body">
						<h3 class="ax-step__title">Made at our own factory</h3>
						<p class="ax-step__text">
							It is manufactured at our <?php echo esc_html( $f['factory_sqft'] ); ?> sq ft unit at
							<?php echo esc_html( $f['factory_place'] ); ?>, with <?php echo esc_html( $brands ); ?>
							hardware fitted as standard. Come and watch it being built if you would like to.
						</p>
						<span class="ax-step__time">Never outsourced</span>
					</div>
				</li>
				<li class="ax-step">
					<div class="ax-step__body">
						<h3 class="ax-step__title">Delivered to <?php echo esc_html( $city ); ?> and installed</h3>
						<p class="ax-step__text">
							The finished kitchen is delivered to your home and installed there. We agree the dates
							with you in advance rather than turning up.
						</p>
						<span class="ax-step__time"><?php echo (int) $f['install_days']; ?> days</span>
					</div>
				</li>
				<li class="ax-step">
					<div class="ax-step__body">
						<h3 class="ax-step__title">A warranty you can read</h3>
						<p class="ax-step__text">
							<?php echo (int) $f['warranty_years']; ?> years, in writing, handed over with the kitchen.
							The full terms are in the written document you receive &mdash; ask to read them on the free
							visit, before you commit to anything.
						</p>
						<span class="ax-step__time"><?php echo (int) $f['warranty_years']; ?> years, in writing</span>
					</div>
				</li>
			</ol>
		</div>
	</section>

	<!-- ================================================== 5. COLLECTIONS + BANDS -->
	<!-- id="estimate": landing anchor for the theme's site-wide sticky mobile
	     bar (.aleac-mbar "Free Estimate" button in functions.php). It lands on
	     the published bands and the worked arithmetic beneath them. -->
	<section class="ax-section ax-section--ruled" id="estimate">
		<div class="ax-wrap">
			<header class="ax-head ax-head--wide ax-reveal">
				<p class="ax-eyebrow">The range</p>
				<?php /* Both counts are derived — the band count from facts.php
				         'collections' (the same array the cards loop over) and the
				         area count from the filtered list — so adding or retiring
				         either cannot leave this heading quoting a stale number. */ ?>
				<h2 class="ax-h2">The same <?php echo (int) count( $f['collections'] ); ?> bands in <?php echo esc_html( $city ); ?> as everywhere else.</h2>
				<p class="ax-lead">
					Rates are per square foot of cabinetry and they are the same in all
					<?php echo (int) $city_count; ?> areas we serve. There is no
					<?php echo esc_html( $city ); ?> price list, and no local surcharge hiding behind a form.
				</p>
			</header>
			<div class="ax-grid ax-grid--3">
				<?php
				foreach ( $f['collections'] as $c_slug => $col ) :
					/* The image map is page-local; facts.php is the file meant to
					   change. A collection added there must render text-first
					   rather than emit a warning and a broken <img>. alea_img()
					   also returns '' for anything unusable, so the media block
					   is skipped rather than left as an empty frame. */
					$col_img = isset( $collection_imgs[ $c_slug ] ) ? alea_img( $collection_imgs[ $c_slug ] ) : '';
					?>
				<article class="ax-card ax-card--collection ax-reveal">
					<?php if ( $col_img ) : ?>
					<div class="ax-card__media">
						<?php echo $col_img; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</div>
					<?php endif; ?>
					<div class="ax-card__inner">
						<h3 class="ax-card__name"><?php echo esc_html( $col['name'] ); ?></h3>
						<p class="ax-card__character"><?php echo esc_html( $col['character'] ); ?></p>
						<p class="ax-card__meta">
							<a class="ax-card__link" href="<?php echo esc_url( home_url( $col_base . $c_slug . '/' ) ); ?>">
								See the <?php echo esc_html( $col['name'] ); ?> collection
							</a>
						</p>
					</div>
					<div class="ax-card__price">
						<span class="ax-card__price-label">Guide price</span>
						<span class="ax-card__price-value"><?php echo esc_html( alea_price_band( $c_slug ) ); ?></span>
					</div>
				</article>
				<?php endforeach; ?>
			</div>

			<div class="ax-grid ax-grid--split ax-mt-7">
				<div class="ax-reveal">
					<h3 class="ax-h3">What a kitchen length turns into, in rupees.</h3>
					<p class="ax-lead ax-mt-4">
						Because the rates are per square foot of cabinetry, a length becomes a price like this.
						The arithmetic is on the page; the estimator does it for your own numbers in about a
						minute, before we ask for your phone number.
						<a class="ax-link" href="<?php echo esc_url( $price_url ); ?>">See the published price list</a>.
					</p>
					<div class="ax-btnrow ax-mt-5">
						<a class="ax-btn ax-btn--primary" href="<?php echo esc_url( $calc_url ); ?>">Get my price</a>
					</div>
				</div>
				<div class="ax-reveal">
					<div class="ax-spectable--rows">
						<div class="ax-spectable__row">
							<span class="ax-spectable__key">Kitchen length</span>
							<span class="ax-spectable__val"><?php echo (int) $ex_rft; ?> running feet</span>
						</div>
						<div class="ax-spectable__row">
							<span class="ax-spectable__key"><?php echo (int) $ex_rft; ?> rft &times; <?php echo (int) $sqft_rft; ?> sq ft</span>
							<span class="ax-spectable__val"><?php echo (int) $ex_sqft; ?> sq ft of cabinetry</span>
						</div>
						<div class="ax-spectable__row">
							<span class="ax-spectable__key"><?php echo esc_html( $ex_name ); ?> band</span>
							<span class="ax-spectable__val"><?php echo esc_html( alea_price_band( $ex_slug ) ); ?></span>
						</div>
						<div class="ax-spectable__row">
							<span class="ax-spectable__key">Estimated range</span>
							<span class="ax-spectable__val"><strong>&#8377;<?php echo esc_html( alea_inr( $ex_low ) ); ?>&ndash;<?php echo esc_html( alea_inr( $ex_high ) ); ?></strong></span>
						</div>
					</div>
					<p class="ax-spectable__note">
						Illustrative arithmetic only, not a quotation and not a delivered project &mdash; assumes
						standard base + wall units, about <?php echo (int) $sqft_rft; ?> sq ft of cabinetry per
						running foot. Cabinetry only; countertop, chimney, hob, sink and appliances are itemised
						separately.
					</p>
				</div>
			</div>
		</div>
	</section>

	<!-- ================================================== 6. WHY BUY FROM THE FACTORY -->
	<section class="ax-section ax-section--sheet ax-section--ruled">
		<div class="ax-wrap">
			<div class="ax-grid ax-grid--split">
				<div class="ax-reveal">
					<header class="ax-head ax-mb-0">
						<p class="ax-eyebrow">Why buy from the factory</p>
						<h2 class="ax-h2">Four things you can check before you pay us anything.</h2>
						<p class="ax-lead ax-mt-4">
							Each of these is either written down or standing in a building you can walk into.
							That is the only kind of claim we are prepared to put on a city page.
						</p>
					</header>
					<?php /* Experience-centre display photography, captioned as exactly
					         what it is; the alt comes from images.php. Nothing here is
					         presented as a project in this city, because no such
					         photography is verified. */ ?>
					<figure class="ax-media ax-media--169 ax-mt-6">
						<span class="ax-media__frame">
							<?php echo alea_img( 'accessories' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							<span class="ax-media__tag">Display</span>
						</span>
						<figcaption class="ax-media__caption">
							Accessories on display / ALEA experience centre &mdash;
							<b>open and close the fittings yourself on a free visit</b>
						</figcaption>
					</figure>
				</div>
				<ul class="ax-prooflist ax-reveal">
					<li class="ax-proof">
						<span class="ax-proof__tick" aria-hidden="true"></span>
						<div class="ax-proof__text">
							Our own <?php echo esc_html( $f['factory_sqft'] ); ?> sq ft factory at
							<?php echo esc_html( $f['factory_place'] ); ?> &mdash; where your kitchen is actually
							made, and where you can stand and watch it happen.
							<span class="ax-proof__never">Never outsourced</span>
						</div>
					</li>
					<li class="ax-proof">
						<span class="ax-proof__tick" aria-hidden="true"></span>
						<div class="ax-proof__text">
							Hardware from <?php echo esc_html( $brands ); ?> fitted as standard &mdash; hinges,
							runners and soft-close systems from named brands, itemised on your quotation.
							<span class="ax-proof__never">Never generic hardware</span>
						</div>
					</li>
					<li class="ax-proof">
						<span class="ax-proof__tick" aria-hidden="true"></span>
						<div class="ax-proof__text">
							A <?php echo (int) $f['warranty_years']; ?>-year warranty, in writing, with the full
							terms in the written document you receive. Ask to read it on the free visit.
							<span class="ax-proof__never">Never a verbal promise</span>
						</div>
					</li>
					<li class="ax-proof">
						<span class="ax-proof__tick" aria-hidden="true"></span>
						<div class="ax-proof__text">
							Prices published on the page &mdash; the same
							&#8377;<?php echo esc_html( alea_inr( $band_low ) ); ?>&ndash;<?php echo esc_html( alea_inr( $band_high ) ); ?>
							<?php echo esc_html( $price_unit ); ?> bands in <?php echo esc_html( $city ); ?> as everywhere.
							<span class="ax-proof__never">Never a different number by area</span>
						</div>
					</li>
				</ul>
			</div>
		</div>
	</section>

	<!-- ================================================== 7. FREE VISIT -->
	<section class="ax-section ax-section--ink">
		<div class="ax-wrap">
			<div class="ax-head ax-mb-0 ax-reveal">
				<p class="ax-eyebrow">Free visit</p>
				<h2 class="ax-h2">We visit homes in <?php echo esc_html( $city ); ?>.</h2>
				<p class="ax-lead ax-mt-4"><?php echo esc_html( $pres['visit'] ); ?></p>
				<div class="ax-btnrow ax-mt-5">
					<a class="ax-btn ax-btn--ghost ax-btn--lg" href="<?php echo esc_url( $visit_url ); ?>">Book a free visit in <?php echo esc_html( $city ); ?></a>
				</div>
				<p class="ax-btn-note">
					Free &middot; no obligation &middot; we visit <?php echo esc_html( $areas ); ?>
					&mdash; or use the form at the bottom of this page
				</p>
			</div>
		</div>
	</section>

	<!-- ================================================== 8. THE OTHER AREAS -->
	<section class="ax-section">
		<div class="ax-wrap ax-wrap--narrow">
			<header class="ax-head ax-reveal">
				<p class="ax-eyebrow">Areas we serve</p>
				<h2 class="ax-h2">The <?php echo (int) $city_count; ?> areas we deliver and install in.</h2>
				<p class="ax-lead">
					These are the only areas we claim. If you are just outside one of them, call and ask
					&mdash; we will tell you plainly whether we can reach you.
				</p>
			</header>
			<div class="ax-spectable--rows ax-reveal">
				<?php
				/* Whole-row links, not an 11px inline text link: the row itself is
				   the tap target. Hrefs are built from $city_base — the same path
				   registered in alea_redesign_map() — and the labels come from
				   facts.php service_area, so a link, a route and a served-area
				   claim cannot drift apart. */
				foreach ( $alea_cities as $r_slug => $r_name ) :
					if ( $r_slug === $city_slug ) :
					?>
				<div class="ax-spectable__row axp-sibrow">
					<span class="ax-spectable__key"><?php echo esc_html( $r_name ); ?> &mdash; this page</span>
					<span class="ax-spectable__val">&#8377;<?php echo esc_html( alea_inr( $band_low ) ); ?>&ndash;<?php echo esc_html( alea_inr( $band_high ) ); ?> <?php echo esc_html( $price_unit ); ?></span>
				</div>
					<?php else : ?>
				<a class="ax-spectable__row axp-sibrow axp-sibrow--link" href="<?php echo esc_url( home_url( $city_base . $r_slug . '/' ) ); ?>">
					<span class="ax-spectable__key">Modular kitchens in <?php echo esc_html( $r_name ); ?></span>
					<span class="ax-spectable__val">&#8377;<?php echo esc_html( alea_inr( $band_low ) ); ?>&ndash;<?php echo esc_html( alea_inr( $band_high ) ); ?> <?php echo esc_html( $price_unit ); ?></span>
				</a>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
			<p class="ax-spectable__note">
				Same factory, same hardware, same written warranty, same published bands in all
				<?php echo (int) $city_count; ?>.
			</p>
		</div>
	</section>

	<!-- ================================================== 9. FAQ -->
	<section class="ax-section ax-section--ruled">
		<div class="ax-wrap ax-wrap--narrow">
			<header class="ax-head ax-reveal">
				<p class="ax-eyebrow">Questions</p>
				<?php /* Named for the city without asserting that customers there
				         exist or asked anything — the only city-level claim this
				         page makes is that we serve it. */ ?>
				<h2 class="ax-h2">Questions about buying in <?php echo esc_html( $city ); ?>.</h2>
			</header>
			<div class="ax-faq ax-reveal">
				<?php foreach ( $faq as $item ) : ?>
				<details class="ax-faq__item">
					<summary class="ax-faq__q"><?php echo esc_html( $item['q'] ); ?></summary>
					<div class="ax-faq__a"><p><?php echo esc_html( $item['a'] ); ?></p></div>
				</details>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<script type="application/ld+json"><?php echo wp_json_encode( $faq_schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ); ?></script>

	<!-- ================================================== 10. FINAL CTA + FORM -->
	<section class="ax-section ax-section--ruled" id="alea-book">
		<div class="ax-wrap">
			<div class="ax-grid ax-grid--split">
				<div class="ax-reveal">
					<p class="ax-eyebrow">Book a free visit</p>
					<h2 class="ax-h2">Let us measure your kitchen in <?php echo esc_html( $city ); ?>.</h2>
					<p class="ax-lead ax-mt-4">
						Both are free and carry no obligation: a measurement at your home in
						<?php echo esc_html( $city ); ?>, or a factory visit at
						<?php echo esc_html( $f['factory_place'] ); ?> where you can watch kitchens being built
						before you pay a rupee.
					</p>
					<div class="ax-btnrow ax-mt-5">
						<a class="ax-btn ax-btn--ink" href="<?php echo esc_url( $tel_href ); ?>">Call <?php echo esc_html( $phone_disp ); ?></a>
						<a class="ax-btn ax-btn--wa" href="<?php echo esc_url( $wa_href ); ?>">WhatsApp us</a>
					</div>
					<p class="ax-btn-note">Would rather see the number first? The estimator is free and needs no sign-up.</p>
				</div>
				<div class="ax-form ax-form--card ax-reveal">
					<h3 class="ax-form__title">Book your free visit</h3>
					<p class="ax-form__note">Free / no obligation / <?php echo esc_html( $areas_dot ); ?></p>
					<?php echo do_shortcode( '[contact-form-7 id="7dcf010" title="Home Page form"]' ); ?>
					<p class="ax-form__fineprint ax-mt-4">
						We use your details only to arrange your visit and estimate.
					</p>
				</div>
			</div>
		</div>
	</section>

	<!-- Sticky mobile bar: provided site-wide by the theme (.aleac-mbar). Not rebuilt here. -->

	<script data-no-optimize="1" data-no-defer="1">
	(function () {
		try {
			var root = document.querySelector('.ax-root');
			if (!root) { return; }
			var els = root.querySelectorAll('.ax-reveal');
			if (!('IntersectionObserver' in window) || !els.length) { return; }
			root.classList.add('ax-js');
			var io = new IntersectionObserver(function (entries) {
				entries.forEach(function (entry) {
					if (entry.isIntersecting) {
						entry.target.classList.add('is-visible');
						io.unobserve(entry.target);
					}
				});
			}, { rootMargin: '0px 0px -10% 0px', threshold: 0.05 });
			for (var i = 0; i < els.length; i++) { io.observe(els[i]); }
		} catch (e) {
			/* Never break the page: reveal everything. */
			try {
				var all = document.querySelectorAll('.ax-reveal');
				for (var j = 0; j < all.length; j++) { all[j].classList.add('is-visible'); }
			} catch (err) {}
		}
	})();
	</script>

</div><!-- /.ax-root -->
