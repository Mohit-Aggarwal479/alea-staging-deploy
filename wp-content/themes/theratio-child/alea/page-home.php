<?php
/**
 * ALEA — Homepage ("The Manufacturer's Record")
 * Included inside <main class="alea-main"> by the shell. Header/footer/sticky
 * bar come from the theme (.aleac-mbar is injected site-wide — none built here).
 *
 * Every business number on this page is read from facts.php or computed from
 * it in PHP with the assumption stated in visible text. Nothing is hard-coded.
 */

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/facts.php';

$f          = alea_facts();
$calc_url   = home_url( '/kitchen-cost-calculator/' );
$tel_href   = 'tel:' . alea_fact( 'phone_tel' );
$wa_href    = alea_wa_link();
$phone_disp = alea_fact( 'phone_display' );

/* Overall public price band, derived from the collection bands. */
$band_low  = (int) $f['collections']['essential']['from'];
$band_high = (int) $f['collections']['atelier']['to'];

/* Per-month worked examples — one per collection, computed from the published
   bands. Assumptions (mid-band rate, running feet, tenure) are stated in the
   visible text below. */
$emi_rft    = 15; // assumed running feet
$emi_months = 36; // assumed tenure
$emi_rows   = array();
foreach ( $f['collections'] as $emi_slug => $emi_col ) {
	$emi_rate              = (int) round( ( (int) $emi_col['from'] + (int) $emi_col['to'] ) / 2 ); // mid-band ₹/rft
	$emi_rows[ $emi_slug ] = array(
		'name'  => $emi_col['name'],
		'rate'  => $emi_rate,
		'total' => $emi_rate * $emi_rft,
		'month' => (int) ceil( ( $emi_rate * $emi_rft ) / $emi_months ),
	);
}

/* Kitchen shapes for the estimator teaser. */
$shapes = array(
	'l'        => array( 'label' => 'L-shape',  'svg' => '<path d="M5 4v16h15"/>' ),
	'u'        => array( 'label' => 'U-shape',  'svg' => '<path d="M4 4v16h16V4"/>' ),
	'straight' => array( 'label' => 'Straight', 'svg' => '<path d="M3 18h18"/>' ),
	'parallel' => array( 'label' => 'Parallel', 'svg' => '<path d="M4 7h16M4 17h16"/>' ),
	'island'   => array( 'label' => 'Island',   'svg' => '<path d="M4 4v16h16V4"/><path d="M9 12h6"/>' ),
	'g'        => array( 'label' => 'G-shape',  'svg' => '<path d="M20 9V4H4v16h16v-5h-6"/>' ),
);

/* Collection cards: slug => image + alt.
   HONESTY NOTE: the approved upload pool contains exactly three photographs
   that actually show a kitchen (Wardrobes-3, Wardrobes-6, aleaabout) — all
   shot at the ALEA experience centre, none in a customer home. Alts therefore
   claim only "display", never "installed" / "in a real home" / a collection
   attribution. aleaabout.jpg also serves as the hero (pool limitation —
   flagged for the owner to supply real project photography). */
$collection_imgs = array(
	'essential' => array(
		'src' => '/wp-content/uploads/2022/09/Alea-Modular-Kitchen-Wardrobes-6.jpg',
		'alt' => 'ALEA modular kitchen display with breakfast table',
	),
	'signature' => array(
		'src' => '/wp-content/uploads/2022/09/Alea-Modular-Kitchen-Wardrobes-3.jpg',
		'alt' => 'ALEA modular kitchen display with island',
	),
	'atelier'   => array(
		'src' => '/wp-content/uploads/2022/04/aleaabout.jpg',
		'alt' => 'ALEA modular kitchen and dining display',
	),
);

/* Experience-centre plates — photographs of our own display centre, each
   captioned as exactly what it shows. NO delivered-project claims: the pool
   contains no verified customer-home photography. */
$gallery = array(
	array(
		'src'   => '/wp-content/uploads/2022/09/Alea-Modular-Kitchen-Wardrobes-2.jpg',
		'plate' => '01',
		'alt'   => 'Sculpted timber reception desk at the ALEA experience centre',
		'cap'   => 'Reception / ALEA experience centre',
	),
	array(
		'src'   => '/wp-content/uploads/2022/09/Alea-Modular-Kitchen-Wardrobes-4.jpg',
		'plate' => '02',
		'alt'   => 'Sculptural front desk and ceiling at the ALEA experience centre',
		'cap'   => 'Front desk / ALEA experience centre',
	),
	array(
		'src'   => '/wp-content/uploads/2022/09/Alea-Modular-Kitchen-Wardrobes-5.jpg',
		'plate' => '03',
		'alt'   => 'Open shelving and accessories display at the ALEA experience centre',
		'cap'   => 'Accessories display / ALEA experience centre',
	),
	array(
		'src'   => '/wp-content/uploads/2022/09/Alea-Modular-Kitchen-Wardrobes-7.jpg',
		'plate' => '04',
		'alt'   => 'Client lounge seating at the ALEA experience centre',
		'cap'   => 'Client lounge / ALEA experience centre',
	),
);

/* FAQ — the HTML answers and the JSON-LD are rendered from this ONE array,
   so the schema always matches the visible text exactly. */
$faq = array(
	array(
		'q' => 'What does an ALEA modular kitchen cost?',
		'a' => sprintf(
			'We publish our prices. Essential is %1$s, Signature is %2$s and Atelier is %3$s. Use the online estimator to see a live price band for your kitchen before we ever ask for your phone number.',
			alea_price_band( 'essential' ),
			alea_price_band( 'signature' ),
			alea_price_band( 'atelier' )
		),
	),
	array(
		'q' => 'How long does installation take?',
		'a' => sprintf(
			'Installation at your home takes %1$d days. Your kitchen is manufactured before that in our own %2$s sq ft factory at %3$s, so we are never waiting on an outside workshop.',
			(int) $f['install_days'],
			$f['factory_sqft'],
			$f['factory_place']
		),
	),
	array(
		'q' => 'What warranty do I get?',
		'a' => sprintf(
			'Every ALEA kitchen carries a %d-year warranty, in writing — never a verbal promise. Ask for the written warranty terms on your free site visit.',
			(int) $f['warranty_years']
		),
	),
	array(
		'q' => 'What hardware do you use?',
		'a' => sprintf(
			'%s hardware comes as standard on every collection — hinges, runners and soft-close systems from the named brands, never generic unbranded fittings.',
			implode( ' and ', $f['hardware_brands'] )
		),
	),
	array(
		'q' => 'Which areas do you serve?',
		'a' => sprintf(
			'Our factory is at %1$s, and we design and install across %2$s. ALEA has been building modular kitchens since %3$s; our parent furniture workshop has been making furniture since %4$s.',
			$f['factory_place'],
			implode( ', ', $f['service_area'] ),
			$f['alea_since'],
			$f['founded_furniture']
		),
	),
	array(
		'q' => 'Can I visit the factory before I order?',
		'a' => sprintf(
			'Yes — and we encourage it. Book a free factory visit and watch kitchens being built on our %s sq ft floor before you pay anything. Use the booking form on this page, or call or WhatsApp us.',
			$f['factory_sqft']
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
	/* Page-specific: estimator-teaser shape chips only. */
	.axp-shapes{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:var(--sp-2)}
	@media(min-width:640px){.axp-shapes{grid-template-columns:repeat(3,minmax(0,1fr))}}
	@media(min-width:1024px){.axp-shapes{grid-template-columns:repeat(6,minmax(0,1fr))}}
	.axp-shape{flex-direction:column;gap:var(--sp-2);padding:var(--sp-4) var(--sp-2);min-height:84px;text-decoration:none}
	.axp-shape svg{width:30px;height:30px;display:block}
	.axp-hero-alt{margin-top:var(--sp-3);font-size:.9375rem;color:#E4E1DC}
	.axp-hero-alt a{color:inherit}
	</style>

	<!-- ============ 1. HERO ============ -->
	<section class="ax-hero">
		<?php /* aleaabout.jpg: the only wide-landscape photograph in the pool that
		         actually shows a kitchen (1920px, experience-centre display).
		         Alt describes only what is visible — no "real home" claim. */ ?>
		<img
			class="ax-hero__img"
			src="<?php echo esc_url( home_url( '/wp-content/uploads/2022/04/aleaabout.jpg' ) ); ?>"
			alt="Modular kitchen and dining display at the ALEA experience centre"
			loading="eager"
			fetchpriority="high">
		<div class="ax-hero__inner">
			<p class="ax-eyebrow">Modular kitchens &amp; wardrobes — <?php echo esc_html( $f['factory_place'] ); ?></p>
			<h1 class="ax-hero__title">Made in our own factory. Installed in <?php echo (int) $f['install_days']; ?> days.</h1>
			<p class="ax-hero__sub">
				Kitchens and wardrobes built in our <?php echo esc_html( $f['factory_sqft'] ); ?>&nbsp;sq&nbsp;ft factory —
				from <span class="ax-mono">&#8377;<?php echo esc_html( alea_inr( $band_low ) ); ?></span> per running foot, priced in public.
			</p>
			<div class="ax-hero__actions">
				<a class="ax-btn ax-btn--primary ax-btn--lg" href="<?php echo esc_url( $calc_url ); ?>">Get my price</a>
			</div>
			<p class="axp-hero-alt">or <a href="#alea-book">book a free site visit</a> — free, no obligation</p>
			<p class="ax-hero__credit">
				<?php echo esc_html( $f['factory_sqft'] ); ?> SQ FT FACTORY
				/ <?php echo (int) $f['warranty_years']; ?>-YEAR WRITTEN WARRANTY
				/ <?php echo esc_html( implode( ' & ', $f['hardware_brands'] ) ); ?> HARDWARE
			</p>
		</div>
	</section>

	<!-- ============ 2. SPEC STRIP ============ -->
	<section class="ax-section ax-section--flush" aria-label="ALEA key facts">
		<div class="ax-wrap ax-wrap--flush">
			<div class="ax-specstrip">
				<div class="ax-specstrip__item">
					<span class="ax-specstrip__label">Own factory</span>
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
					<span class="ax-specstrip__label">Public price band</span>
					<span class="ax-specstrip__value">&#8377;<?php echo esc_html( alea_inr( $band_low ) ); ?>&ndash;<?php echo esc_html( alea_inr( $band_high ) ); ?><span class="ax-specstrip__unit">per rft</span></span>
				</div>
			</div>
		</div>
	</section>

	<!-- ============ 3. ESTIMATOR TEASER ============ -->
	<!-- id="estimate": landing anchor for the theme's site-wide sticky mobile
	     bar (.aleac-mbar "Free Estimate" button in functions.php). -->
	<section class="ax-section ax-section--ruled-b" id="estimate">
		<div class="ax-wrap">
			<div class="ax-head ax-reveal">
				<p class="ax-eyebrow">60-second estimate</p>
				<h2 class="ax-h2">What shape is your kitchen?</h2>
				<p class="ax-lead">
					Tap your layout and see a live price band —
					from <span class="ax-mono ax-ink">&#8377;<?php echo esc_html( alea_inr( $band_low ) ); ?></span> per running foot.
					We show the number first; the phone number is yours to give later.
				</p>
			</div>
			<div class="axp-shapes ax-reveal" role="list">
				<?php foreach ( $shapes as $slug => $shape ) : ?>
				<a role="listitem" class="ax-chip axp-shape" href="<?php echo esc_url( $calc_url . '?shape=' . $slug ); ?>">
					<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><?php echo wp_kses( $shape['svg'], array( 'path' => array( 'd' => true ) ) ); ?></svg>
					<span><?php echo esc_html( $shape['label'] ); ?></span>
				</a>
				<?php endforeach; ?>
			</div>
			<p class="ax-btn-note">Free &middot; no sign-up &middot; priced off our published per-running-foot rates</p>
		</div>
	</section>

	<!-- ============ 4. THREE COLLECTIONS ============ -->
	<section class="ax-section">
		<div class="ax-wrap">
			<div class="ax-head ax-reveal">
				<p class="ax-eyebrow">The range</p>
				<h2 class="ax-h2">Three collections. Every price published.</h2>
				<p class="ax-lead">One factory standard, three levels of finish — each with its guide price stated on the page, not hidden behind a form.</p>
			</div>
			<div class="ax-grid ax-grid--3">
				<?php foreach ( $f['collections'] as $slug => $col ) : ?>
				<article class="ax-card ax-card--collection ax-reveal">
					<div class="ax-card__media">
						<img
							src="<?php echo esc_url( home_url( $collection_imgs[ $slug ]['src'] ) ); ?>"
							alt="<?php echo esc_attr( $collection_imgs[ $slug ]['alt'] ); ?>"
							loading="lazy">
					</div>
					<div class="ax-card__inner">
						<h3 class="ax-card__name"><?php echo esc_html( $col['name'] ); ?></h3>
						<p class="ax-card__character"><?php echo esc_html( $col['character'] ); ?></p>
						<p class="ax-card__meta">
							<a class="ax-card__link" href="<?php echo esc_url( $calc_url ); ?>">Get a price in this range</a>
						</p>
					</div>
					<div class="ax-card__price">
						<span class="ax-card__price-label">Guide price</span>
						<span class="ax-card__price-value">&#8377;<?php echo esc_html( alea_inr( $col['from'] ) ); ?>&ndash;<?php echo esc_html( alea_inr( $col['to'] ) ); ?> / rft</span>
					</div>
				</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<!-- ============ 5. WHY ALEA ============ -->
	<section class="ax-section ax-section--ruled">
		<div class="ax-wrap">
			<div class="ax-grid ax-grid--split">
				<div class="ax-head ax-mb-0 ax-reveal">
					<p class="ax-eyebrow">Why ALEA</p>
					<h2 class="ax-h2">Claims you can walk into our factory and check.</h2>
				</div>
				<ul class="ax-prooflist ax-reveal">
					<li class="ax-proof">
						<span class="ax-proof__tick" aria-hidden="true"></span>
						<div class="ax-proof__text">
							Our own <?php echo esc_html( $f['factory_sqft'] ); ?> sq ft factory at <?php echo esc_html( $f['factory_place'] ); ?> builds every kitchen we sell.
							<span class="ax-proof__never">Never outsourced</span>
						</div>
					</li>
					<li class="ax-proof">
						<span class="ax-proof__tick" aria-hidden="true"></span>
						<div class="ax-proof__text">
							<?php echo esc_html( implode( ' and ', $f['hardware_brands'] ) ); ?> hardware as standard, named on every collection.
							<span class="ax-proof__never">Never generic hardware</span>
						</div>
					</li>
					<li class="ax-proof">
						<span class="ax-proof__tick" aria-hidden="true"></span>
						<div class="ax-proof__text">
							A <?php echo (int) $f['warranty_years']; ?>-year warranty, on paper — ask for the written terms on your free site visit.
							<span class="ax-proof__never">Never a verbal promise</span>
						</div>
					</li>
				</ul>
			</div>
		</div>
	</section>

	<!-- ============ 6. INSIDE OUR FACTORY ============ -->
	<!-- No photograph here on purpose: the approved upload pool contains no
	     documentary factory photography, and presenting a showroom image as
	     the factory would fake the page's most important claim. Text only
	     until real factory photos exist (brief Register B imagery). -->
	<section class="ax-section ax-section--ink">
		<div class="ax-wrap">
			<div class="ax-grid ax-grid--split">
				<div class="ax-head ax-mb-0 ax-reveal">
					<p class="ax-eyebrow">Inside our factory</p>
					<h2 class="ax-h2">Come and watch your kitchen being made.</h2>
					<p class="ax-lead ax-mt-4">
						We have been making furniture since <?php echo esc_html( $f['founded_furniture'] ); ?> and modular kitchens since
						<?php echo esc_html( $f['alea_since'] ); ?> — <?php echo esc_html( $f['years_experience'] ); ?> years of ALEA kitchens,
						all built on our own floor. Visit before you pay a rupee.
					</p>
				</div>
				<div class="ax-reveal">
					<div class="ax-spectable--rows">
						<div class="ax-spectable__row">
							<span class="ax-spectable__key">Factory floor</span>
							<span class="ax-spectable__val"><?php echo esc_html( $f['factory_sqft'] ); ?> sq ft</span>
						</div>
						<div class="ax-spectable__row">
							<span class="ax-spectable__key">Where</span>
							<span class="ax-spectable__val"><?php echo esc_html( $f['factory_place'] ); ?></span>
						</div>
						<div class="ax-spectable__row">
							<span class="ax-spectable__key">Furniture since</span>
							<span class="ax-spectable__val"><?php echo esc_html( $f['founded_furniture'] ); ?></span>
						</div>
						<div class="ax-spectable__row">
							<span class="ax-spectable__key">ALEA kitchens since</span>
							<span class="ax-spectable__val"><?php echo esc_html( $f['alea_since'] ); ?></span>
						</div>
					</div>
					<div class="ax-btnrow ax-mt-5">
						<a class="ax-btn ax-btn--ghost" href="#alea-book">Book a free factory visit</a>
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- ============ 7. EXPERIENCE CENTRE ============ -->
	<!-- Honest relabel: these are photographs of our own display centre, not
	     delivered customer projects. No verified project photography exists
	     in the approved pool. delivered-project-photos pending. -->
	<section class="ax-section">
		<div class="ax-wrap">
			<div class="ax-head ax-reveal">
				<p class="ax-eyebrow">The experience centre</p>
				<h2 class="ax-h2">Walk through it before you decide.</h2>
				<p class="ax-lead">
					Photographs of our own experience centre, captioned as exactly what they show.
					See the displays in person, then watch kitchens being built on our factory floor
					at <?php echo esc_html( $f['factory_place'] ); ?>.
				</p>
			</div>
			<div class="ax-grid ax-grid--4">
				<?php foreach ( $gallery as $p ) : ?>
				<figure class="ax-media ax-media--43 ax-reveal">
					<span class="ax-media__frame">
						<img
							src="<?php echo esc_url( home_url( $p['src'] ) ); ?>"
							alt="<?php echo esc_attr( $p['alt'] ); ?>"
							loading="lazy">
						<span class="ax-media__tag">PLATE <?php echo esc_html( $p['plate'] ); ?></span>
					</span>
					<figcaption class="ax-media__caption"><?php echo esc_html( $p['cap'] ); ?></figcaption>
				</figure>
				<?php endforeach; ?>
			</div>
			<div class="ax-btnrow ax-mt-6">
				<a class="ax-btn ax-btn--ghost" href="#alea-book">Book a free visit — see it in person</a>
			</div>
		</div>
	</section>

	<!-- verified-reviews pending -->

	<!-- ============ 9. HOW IT WORKS ============ -->
	<section class="ax-section ax-section--ruled">
		<div class="ax-wrap">
			<div class="ax-head ax-reveal">
				<p class="ax-eyebrow">How it works</p>
				<h2 class="ax-h2">Five steps. The first one is free.</h2>
			</div>
			<ol class="ax-steps ax-reveal">
				<li class="ax-step">
					<div class="ax-step__body">
						<h3 class="ax-step__title">Free site visit and measurement</h3>
						<p class="ax-step__text">We measure your kitchen at your home. No fee, no obligation — you keep the measurements either way.</p>
						<span class="ax-step__time">Free</span>
					</div>
				</li>
				<li class="ax-step">
					<div class="ax-step__body">
						<h3 class="ax-step__title">Design and itemised quote</h3>
						<p class="ax-step__text">Your layout, finishes and hardware — priced per running foot against our published bands, item by item.</p>
						<span class="ax-step__time">Priced in writing</span>
					</div>
				</li>
				<li class="ax-step">
					<div class="ax-step__body">
						<h3 class="ax-step__title">Manufacturing in our own factory</h3>
						<p class="ax-step__text">Built on our <?php echo esc_html( $f['factory_sqft'] ); ?> sq ft floor at <?php echo esc_html( $f['factory_place'] ); ?> — you are welcome to visit while it is being made.</p>
						<span class="ax-step__time">Own factory</span>
					</div>
				</li>
				<li class="ax-step">
					<div class="ax-step__body">
						<h3 class="ax-step__title">Installation</h3>
						<p class="ax-step__text">Our own team installs at your home.</p>
						<span class="ax-step__time"><?php echo (int) $f['install_days']; ?> days</span>
					</div>
				</li>
				<li class="ax-step">
					<div class="ax-step__body">
						<h3 class="ax-step__title">Written warranty and after-sales</h3>
						<p class="ax-step__text">You receive the warranty in writing, and one local number to call if anything ever needs attention.</p>
						<span class="ax-step__time"><?php echo (int) $f['warranty_years']; ?> years</span>
					</div>
				</li>
			</ol>
		</div>
	</section>

	<!-- ============ 10. HARDWARE PROOF ============ -->
	<section class="ax-section ax-section--sheet ax-section--ruled">
		<div class="ax-wrap">
			<div class="ax-head ax-reveal">
				<p class="ax-eyebrow">Hardware, named</p>
				<h2 class="ax-h2">We put the brand on the page, not just &ldquo;German hardware&rdquo;.</h2>
			</div>
			<div class="ax-grid ax-grid--2">
				<?php foreach ( $f['hardware_brands'] as $brand ) : ?>
				<div class="ax-card ax-card--flat ax-reveal">
					<h3 class="ax-mono ax-upper"><?php echo esc_html( $brand ); ?></h3>
					<p class="ax-card__body">
						Hinges, drawer runners and soft-close systems fitted as standard across our collections —
						open and close them yourself on a free factory visit.
					</p>
				</div>
				<?php endforeach; ?>
			</div>
			<p class="ax-btn-note">Standard on every ALEA kitchen &mdash; never generic, unbranded fittings</p>
		</div>
	</section>

	<!-- ============ 11. WHAT IT COSTS PER MONTH ============ -->
	<section class="ax-section ax-section--ruled">
		<div class="ax-wrap">
			<div class="ax-grid ax-grid--split">
				<div class="ax-head ax-mb-0 ax-reveal">
					<p class="ax-eyebrow">Per month</p>
					<h2 class="ax-h2">A kitchen is a monthly number, not a lakh.</h2>
					<p class="ax-lead ax-mt-4">
						Three worked examples, one from each published band — every figure below is arithmetic
						from the rates on this page, with the assumptions stated.
					</p>
				</div>
				<div class="ax-reveal">
					<div class="ax-spectable--rows">
						<?php foreach ( $emi_rows as $row ) : ?>
						<div class="ax-spectable__row">
							<span class="ax-spectable__key"><?php echo esc_html( $row['name'] ); ?> &middot; &#8377;<?php echo esc_html( alea_inr( $row['rate'] ) ); ?> / rft</span>
							<span class="ax-spectable__val ax-ox">&#8377;<?php echo esc_html( alea_inr( $row['month'] ) ); ?> / month</span>
						</div>
						<?php endforeach; ?>
					</div>
					<p class="ax-spectable__note">
						Worked examples only: a <?php echo (int) $emi_rft; ?>-running-foot kitchen at the midpoint of each
						collection&rsquo;s published band, divided equally over <?php echo (int) $emi_months; ?> months.
						Signature, for instance: &#8377;<?php echo esc_html( alea_inr( $emi_rows['signature']['rate'] ) ); ?> &times;
						<?php echo (int) $emi_rft; ?> rft = &#8377;<?php echo esc_html( alea_inr( $emi_rows['signature']['total'] ) ); ?>,
						or &#8377;<?php echo esc_html( alea_inr( $emi_rows['signature']['month'] ) ); ?> a month.
						Bank interest, fees and your final specification will change these figures.
					</p>
					<div class="ax-btnrow ax-mt-5">
						<a class="ax-btn ax-btn--primary" href="<?php echo esc_url( $calc_url ); ?>">Get my exact price band</a>
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- ============ 12. FAQ ============ -->
	<section class="ax-section ax-section--ruled">
		<div class="ax-wrap ax-wrap--narrow">
			<div class="ax-head ax-reveal">
				<p class="ax-eyebrow">Questions</p>
				<h2 class="ax-h2">Asked before every kitchen we build.</h2>
			</div>
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

	<!-- ============ 13. FINAL CTA BAND ============ -->
	<section class="ax-section ax-section--ruled" id="alea-book">
		<div class="ax-wrap">
			<div class="ax-grid ax-grid--split">
				<div class="ax-reveal">
					<p class="ax-eyebrow">Book a free visit</p>
					<h2 class="ax-h2">Let us measure your kitchen — or come and see the factory.</h2>
					<p class="ax-lead ax-mt-4">
						Both are free and carry no obligation: a site visit and measurement at your home,
						or a factory visit at <?php echo esc_html( $f['factory_place'] ); ?> where you can watch kitchens being built.
					</p>
					<div class="ax-btnrow ax-mt-5">
						<a class="ax-btn ax-btn--ink" href="<?php echo esc_url( $tel_href ); ?>">Call <?php echo esc_html( $phone_disp ); ?></a>
						<a class="ax-btn ax-btn--wa" href="<?php echo esc_url( $wa_href ); ?>">WhatsApp us</a>
					</div>
					<p class="ax-btn-note">Prefer to type? The form works too &mdash; we reply on the number you give.</p>
				</div>
				<div class="ax-form ax-form--card ax-reveal">
					<h3 class="ax-form__title">Book your free visit</h3>
					<p class="ax-form__note">Free / no obligation / <?php echo esc_html( implode( ' · ', $f['service_area'] ) ); ?></p>
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
