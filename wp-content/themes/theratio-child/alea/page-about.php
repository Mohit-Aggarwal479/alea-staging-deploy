<?php
/**
 * ALEA — About ("The Manufacturer's Record")
 * Replaces /about/. Included inside <main class="alea-main"> by the shell;
 * header, footer and the site-wide sticky mobile bar come from the theme.
 *
 * SINGLE-URL template: it serves one path only, so it reads no variant from
 * $GLOBALS['alea_page_args']. (Multi-URL siblings — page-collection.php —
 * whitelist their variant there.)
 *
 * - ROUTING (outside this file): alea_redesign_map() in functions.php now
 *   carries an '/about/' entry pointing at this template. Routing lives there,
 *   not here — without that entry alea_redesign_entry() returns null, the shell
 *   never includes this file, the wp_head design-system.css inliner never fires
 *   (it early-returns on an empty $GLOBALS['alea_page_file']), and the old
 *   Elementor /about/ keeps serving. If the map entry is ever removed, this
 *   file ships dead and unstyled.
 *
 * HONESTY NOTES — this page is the one most tempted to invent a story:
 * - The ONLY dates claimed are the two in facts.php: 'founded_furniture' (the
 *   parent furniture business) and 'alea_since' (ALEA's own start). No founding
 *   anecdote, no "first branch", no expansion milestones, no award years.
 * - AGE IS NEVER ASSERTED AS A COUNT OF YEARS. facts.php carries a [SITE]
 *   'years_experience' string ('13+') that has gone stale against its own
 *   'alea_since' (2009). This page renders 2009 in 3.25rem mono and invites the
 *   reader to subtract, so printing a years figure beside it would publish a
 *   contradiction on the very page that exists to end one. The fix belongs in
 *   facts.php (derive the figure from 'alea_since'); until then this template
 *   reads 'alea_since' only and says "since 2009", never "N years".
 * - facts.php holds NO key for founder names, staff counts, project counts,
 *   certifications, machine lists or quality-check counts, so none appear.
 *   It also holds no key for the parent firm's NAME — only the 1998 date — so
 *   this page says "the furniture business we grew out of" rather than naming it.
 * - The live site contradicts itself ("25+ Years of Trust" on some location
 *   pages vs "13+ years" on the homepage). This page corrects that in visible
 *   copy by stating which date belongs to whom. It does NOT promise that the
 *   older pages are being changed: this deliverable does not touch them, and a
 *   future-tense promise about site state is exactly the sort of unbacked claim
 *   the rest of this file refuses to make.
 * - COMPETITORS ARE NEVER CHARACTERISED. No claim is made about what other
 *   companies in the service area do, how many of them do it, or what they
 *   cannot offer — none of that is in facts.php or checkable by a reader. The
 *   seller/maker distinction is stated as general industry guidance, and every
 *   claim attached to ALEA is first-person and verifiable on a free visit.
 * - Photography: the approved pool is experience-centre / display photography.
 *   No image is captioned as a delivered project, a client home or a factory
 *   floor, because none of that is verified. Alt text is differentiated per
 *   frame so no two photographs describe themselves identically.
 * - Warranty is "10 years, in writing" and nothing more specific; the terms
 *   live in the written document the customer receives.
 */

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/facts.php';

$f = alea_facts();

$since_parent = alea_fact( 'founded_furniture' );        // '1998'
$since_alea   = alea_fact( 'alea_since' );               // '2009'
/* NOTE: facts.php's 'years_experience' ('13+') is deliberately NOT read here.
   It is a stale [SITE] string that disagrees with 'alea_since' (2009), and this
   page prints 2009 at 3.25rem and asks the reader to do the subtraction. Age is
   expressed as a date, never as a count. */
$sqft         = alea_fact( 'factory_sqft' );             // '95,000'
$place        = alea_fact( 'factory_place' );            // 'Raipur Rani, Panchkula district'
$warranty_yrs = (int) alea_fact( 'warranty_years', 0 );  // 10
$install_days = (int) alea_fact( 'install_days', 0 );    // 15
$sqft_rft     = (int) alea_fact( 'sqft_per_rft', 0 );    // 8
$phone_disp   = alea_fact( 'phone_display' );
$tel_href     = 'tel:' . alea_fact( 'phone_tel' );
$wa_href      = alea_wa_link( "Hi ALEA, I'd like to know more about your factory and a free estimate." );

$brands_txt   = implode( ' and ', alea_fact( 'hardware_brands', array() ) );
$areas_txt    = implode( ', ', alea_fact( 'service_area', array() ) );

$calc_url     = home_url( '/kitchen-cost-calculator/' );
$visit_url    = home_url( '/book-a-free-design-visit/' );
$factory_url  = home_url( '/our-factory/' );
$price_url    = home_url( '/modular-kitchen-price/' );

/* ---- The published bands, read from facts.php via alea_price_band() so this
   page can never quote a rate the pricing page has moved on from. ---- */
$band_lines = array();
foreach ( $f['collections'] as $band_slug => $band_col ) {
	$band_lines[] = array(
		'name' => $band_col['name'],
		'band' => alea_price_band( $band_slug ),
	);
}

/* ---- One worked whole-kitchen estimate, computed via alea_kitchen_total().
   The sq-ft-per-running-foot assumption is stated in visible text beside it.
   The example collection falls back to the first one defined, so retiring
   'signature' in facts.php degrades instead of breaking. ---- */
$ex_rft  = 12;
$ex_slug = isset( $f['collections']['signature'] ) ? 'signature' : (string) key( $f['collections'] );
$ex_name = isset( $f['collections'][ $ex_slug ]['name'] ) ? (string) $f['collections'][ $ex_slug ]['name'] : '';
list( $ex_low, $ex_high, $ex_sqft ) = alea_kitchen_total( $ex_rft, $ex_slug );

/* ---- The two dates. This array IS the timeline: there is no third entry,
   because facts.php records no third date. ---- */
$milestones = array(
	array(
		'year'  => $since_parent,
		'label' => 'The furniture business begins',
		'text'  => 'The family furniture business we grew out of has been making furniture since ' . $since_parent . '. That is where the workshop habits came from — but it is not ALEA\'s age, and we do not count it as ours.',
	),
	array(
		'year'  => $since_alea,
		'label' => 'ALEA begins',
		'text'  => 'ALEA\'s own journey began in ' . $since_alea . ': modular kitchens and wardrobes, manufactured rather than bought in. That is the date to hold us to, and it is the only one that is ours.',
	),
);

/* ---- Experience-centre plates. Captioned as exactly what they show: our own
   display centre. The approved pool contains no verified customer-home or
   shop-floor photography, so none is claimed. ---- */
$gallery = array(
	array(
		'src' => '/wp-content/uploads/2022/09/Alea-Modular-Kitchen-Wardrobes-4.jpg',
		'alt' => 'Modular kitchen cabinetry on display at the ALEA experience centre',
		'cap' => 'Kitchen display / ALEA experience centre',
		'no'  => '01',
	),
	array(
		'src' => '/wp-content/uploads/2022/09/Alea-Modular-Kitchen-Wardrobes-7.jpg',
		'alt' => 'Modular kitchen storage and shutters on display at the ALEA experience centre',
		'cap' => 'Storage display / ALEA experience centre',
		'no'  => '02',
	),
	array(
		'src' => '/wp-content/uploads/2022/03/w3.jpg',
		'alt' => 'Wardrobe display at the ALEA experience centre',
		'cap' => 'Wardrobe display / ALEA experience centre',
		'no'  => '03',
	),
);

/* ---- FAQ: the visible answers and the JSON-LD render from this ONE array, so
   the schema can never drift from the text on the page. ---- */
$faq = array(
	array(
		'q' => 'How long has ALEA been making kitchens?',
		'a' => sprintf(
			'ALEA has been making modular kitchens and wardrobes since %1$s. The furniture business we grew out of has been making furniture since %2$s, which is a longer story but a different one, and we do not add the two together. Where an older page on this site quotes a bigger number, these two dates are the ones to hold us to: %1$s is ALEA, %2$s is the furniture business.',
			$since_alea,
			$since_parent
		),
	),
	array(
		'q' => 'Do you actually manufacture, or do you subcontract?',
		'a' => sprintf(
			'We manufacture. Every ALEA kitchen and wardrobe is made in our own %1$s sq ft factory at %2$s — the same company quotes you, builds your kitchen, installs it and answers for it afterwards. You do not have to take that on trust: a factory visit is free and carries no obligation, so come and watch kitchens being built before you pay anything.',
			$sqft,
			$place
		),
	),
	array(
		'q' => 'What do I get in writing?',
		'a' => sprintf(
			'An itemised quotation after a free measurement — every panel, finish and fitting priced line by line rather than as one lump sum — and a %1$d-year warranty, in writing, handed over as a document rather than promised verbally. What that warranty covers is set out in the written document you receive; ask to read it on your free visit, before you commit to anything. Installation at your home takes about %2$d days. We fit %3$s hardware as standard.',
			$warranty_yrs,
			$install_days,
			$brands_txt
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
	/* Page-specific: the two-date mono block and the correction call-out. */
	.axp-mile{min-width:0}
	.axp-mile__label{display:block;margin-bottom:var(--sp-3);font-family:var(--font-mono);font-size:var(--fs-nano);letter-spacing:var(--ls-mono-wide);text-transform:uppercase;color:var(--ax-fg-muted)}
	.axp-mile__year{display:block;font-family:var(--font-mono);font-weight:600;font-size:clamp(2.25rem,9vw,3.25rem);line-height:1;letter-spacing:.02em;color:var(--ax-fg);font-variant-numeric:tabular-nums;font-feature-settings:"tnum" 1}
	.axp-mile__text{margin-top:var(--sp-4);font-size:.9375rem;line-height:1.6;color:var(--ax-fg-muted);text-wrap:pretty}
	.axp-correct{margin-top:var(--sp-6);padding-left:var(--sp-4);border-left:2px solid var(--ax-fg);max-width:62ch}
	.axp-hero-alt{margin-top:var(--sp-3);font-size:.9375rem;color:#E4E1DC}
	.axp-hero-alt a{color:inherit}
	</style>

	<!-- ================================================== 1. HERO -->
	<section class="ax-hero">
		<?php /* aleaabout.jpg — experience-centre display photography. The alt
		         claims a display, never a delivered project, a client home or a
		         factory floor, because the approved pool verifies none of those. */ ?>
		<img
			class="ax-hero__img"
			src="<?php echo esc_url( home_url( '/wp-content/uploads/2022/04/aleaabout.jpg' ) ); ?>"
			alt="ALEA modular kitchen and dining display at the experience centre"
			loading="eager"
			fetchpriority="high">
		<div class="ax-hero__inner">
			<p class="ax-eyebrow">About ALEA</p>
			<h1 class="ax-hero__title">A furniture business since <?php echo esc_html( $since_parent ); ?>. A kitchen factory since <?php echo esc_html( $since_alea ); ?>.</h1>
			<p class="ax-hero__sub">
				ALEA makes modular kitchens and wardrobes in our own <?php echo esc_html( $sqft ); ?> sq ft
				factory at <?php echo esc_html( $place ); ?>, and has done since <?php echo esc_html( $since_alea ); ?>.
				Two dates, one factory, and nothing on this page we cannot show you in person.
			</p>
			<div class="ax-hero__actions">
				<a class="ax-btn ax-btn--primary ax-btn--lg" href="<?php echo esc_url( $calc_url ); ?>">Get my price</a>
			</div>
			<p class="axp-hero-alt">or <a href="<?php echo esc_url( $visit_url ); ?>">book a free visit</a> &mdash; free, no obligation</p>
			<p class="ax-hero__credit">
				ALEA SINCE <?php echo esc_html( $since_alea ); ?>
				/ OWN <?php echo esc_html( $sqft ); ?> SQ FT FACTORY
				/ <?php echo (int) $warranty_yrs; ?>-YEAR WRITTEN WARRANTY
			</p>
		</div>
	</section>

	<!-- ================================================== 2. THE TWO DATES -->
	<!-- Two entries because facts.php records two dates. No founding anecdote,
	     no branch openings, no award years — none of that is verified. -->
	<section class="ax-section">
		<div class="ax-wrap">
			<header class="ax-head ax-head--wide ax-reveal">
				<p class="ax-eyebrow">The record</p>
				<h2 class="ax-h2">The only two dates we claim.</h2>
				<p class="ax-lead">
					Most company timelines are decorated. This one is not: two dates, stated separately,
					because they belong to two different businesses.
				</p>
			</header>
			<div class="ax-grid ax-grid--2 ax-grid--ruled ax-reveal">
				<?php foreach ( $milestones as $m ) : ?>
				<div class="axp-mile">
					<span class="axp-mile__label"><?php echo esc_html( $m['label'] ); ?></span>
					<span class="axp-mile__year"><?php echo esc_html( $m['year'] ); ?></span>
					<p class="axp-mile__text"><?php echo esc_html( $m['text'] ); ?></p>
				</div>
				<?php endforeach; ?>
			</div>
			<?php /* The live site contradicts itself on this number. Correcting it in
			         public is cheaper than being caught by a customer who noticed.
			         This says only what is true NOW and checkable on this page: it does
			         not promise that the older pages are being changed, because this
			         template does not change them. */ ?>
			<div class="axp-correct ax-reveal">
				<p class="ax-prose">
					You may still find &ldquo;25+ years&rdquo; on an older page of this site. It is not ALEA&rsquo;s age.
					<?php echo esc_html( $since_alea ); ?> belongs to ALEA;
					<?php echo esc_html( $since_parent ); ?> belongs to the furniture business we came from, not to us.
					Where two numbers on this site disagree, these are the two dates we stand behind &mdash; and you are
					welcome to hold this page against any other.
				</p>
			</div>
		</div>
	</section>

	<!-- ================================================== 3. WHAT WE ACTUALLY DO -->
	<section class="ax-section ax-section--ruled">
		<div class="ax-wrap ax-wrap--narrow">
			<header class="ax-head ax-reveal">
				<p class="ax-eyebrow">What we actually do</p>
				<h2 class="ax-h2">We manufacture. We do not place your kitchen with somebody else.</h2>
			</header>
			<?php /* HONESTY NOTE: the seller/maker split below is stated as general
			         industry guidance and asserts NOTHING about any other company —
			         not how many of them there are, not how they operate, not what
			         they can or cannot offer. facts.php holds no competitor data and
			         a reader cannot check a claim about someone else's workshop.
			         Every sentence attached to ALEA here is first-person and settled
			         by walking into the building. */ ?>
			<div class="ax-prose ax-reveal">
				<p>
					Companies selling modular kitchens fall into two kinds: sellers and makers. A seller takes the order
					and places the work with a workshop it does not own. A maker builds it. We are the second kind.
				</p>
				<p>
					Your kitchen is cut, edged, assembled and finished in our own
					<?php echo esc_html( $sqft ); ?> sq ft factory at <?php echo esc_html( $place ); ?>. The company that
					quotes you is the company that builds it, installs it and answers the phone afterwards. Wardrobes are
					made on the same line.
				</p>
				<p>
					That is a claim, and claims are cheap &mdash; so we would rather you checked it than believed it.
					The factory door is open, a visit costs nothing and carries no obligation, and you can watch kitchens
					being built before you spend a rupee.
				</p>
			</div>
		</div>
	</section>

	<!-- ================================================== 4. THE FACTORY -->
	<section class="ax-section ax-section--ruled">
		<div class="ax-wrap">
			<div class="ax-grid ax-grid--split">
				<div class="ax-reveal">
					<div class="ax-head ax-mb-0">
						<p class="ax-eyebrow">The factory</p>
						<h2 class="ax-h2">One address does most of the arguing.</h2>
						<p class="ax-lead ax-mt-4">
							<?php echo esc_html( $sqft ); ?> sq ft at <?php echo esc_html( $place ); ?> &mdash; ours,
							not rented floor space in somebody else&rsquo;s workshop. It is why we can say what a kitchen
							costs before you fill in a form, and why installation is a queue we control.
						</p>
					</div>
					<div class="ax-spectable--rows ax-mt-6">
						<div class="ax-spectable__row">
							<span class="ax-spectable__key">Own factory</span>
							<span class="ax-spectable__val"><?php echo esc_html( $sqft ); ?> sq ft</span>
						</div>
						<div class="ax-spectable__row">
							<span class="ax-spectable__key">Where</span>
							<span class="ax-spectable__val"><?php echo esc_html( $place ); ?></span>
						</div>
						<div class="ax-spectable__row">
							<span class="ax-spectable__key">Making kitchens since</span>
							<span class="ax-spectable__val"><?php echo esc_html( $since_alea ); ?></span>
						</div>
						<div class="ax-spectable__row">
							<span class="ax-spectable__key">We serve</span>
							<span class="ax-spectable__val"><?php echo esc_html( $areas_txt ); ?></span>
						</div>
					</div>
					<div class="ax-btnrow ax-mt-5">
						<a class="ax-btn ax-btn--ghost" href="<?php echo esc_url( $factory_url ); ?>">See inside the factory</a>
					</div>
				</div>
				<?php /* Alt differs from every other frame on the page (gallery plate 01
				         carries the general cabinetry description) so no two photographs
				         describe themselves identically to a screen reader or a crawler.
				         The "on display" qualifier stays: this is not a factory floor. */ ?>
				<figure class="ax-media ax-media--43 ax-reveal">
					<span class="ax-media__frame">
						<img
							src="<?php echo esc_url( home_url( '/wp-content/uploads/2022/09/Alea-Modular-Kitchen-Wardrobes-2.jpg' ) ); ?>"
							alt="Base and wall units on display at the ALEA experience centre"
							loading="lazy">
						<span class="ax-media__tag">Display</span>
					</span>
					<figcaption class="ax-media__caption">
						Kitchen display / ALEA experience centre &mdash;
						<b>the factory itself is a free visit away</b>
					</figcaption>
				</figure>
			</div>
		</div>
	</section>

	<!-- ================================================== 5. HOW WE PRICE -->
	<!-- id="estimate": landing anchor for the theme's site-wide sticky mobile
	     bar (.aleac-mbar "Free Estimate" button in functions.php). -->
	<span id="estimate" class="ax-sr-only"></span>
	<section class="ax-section ax-section--ruled ax-section--sheet">
		<div class="ax-wrap">
			<div class="ax-grid ax-grid--split">
				<div class="ax-reveal">
					<div class="ax-head ax-mb-0">
						<p class="ax-eyebrow">How we price</p>
						<h2 class="ax-h2">Rates on the page, not behind a phone number.</h2>
						<p class="ax-lead ax-mt-4">
							We publish a rate per square foot of cabinetry for each collection, and the estimator does the
							arithmetic for your own kitchen for free. Where your kitchen lands inside its band depends on
							the finishes, fittings and layout you choose &mdash; and your quotation itemises all of it
							after a free measurement.
						</p>
					</div>
					<p class="ax-prose ax-mt-5">
						Wardrobes are quoted after a free measurement rather than from a published rate, because a
						wardrobe rate is not something we are willing to print until we can stand behind it.
					</p>
					<div class="ax-btnrow ax-mt-5">
						<a class="ax-btn ax-btn--primary" href="<?php echo esc_url( $calc_url ); ?>">Get my price</a>
						<a class="ax-btn ax-btn--ghost" href="<?php echo esc_url( $price_url ); ?>">See the full price list</a>
					</div>
				</div>
				<div class="ax-reveal">
					<div class="ax-spectable--rows">
						<?php foreach ( $band_lines as $bl ) : ?>
						<div class="ax-spectable__row">
							<span class="ax-spectable__key"><?php echo esc_html( $bl['name'] ); ?></span>
							<span class="ax-spectable__val"><?php echo esc_html( $bl['band'] ); ?></span>
						</div>
						<?php endforeach; ?>
						<div class="ax-spectable__row">
							<span class="ax-spectable__key"><?php echo (int) $ex_rft; ?> running ft &middot; <?php echo esc_html( $ex_name ); ?></span>
							<span class="ax-spectable__val"><strong>&#8377;<?php echo esc_html( alea_inr( $ex_low ) ); ?>&ndash;<?php echo esc_html( alea_inr( $ex_high ) ); ?></strong></span>
						</div>
					</div>
					<?php /* Every whole-kitchen figure on the site carries this assumption
					         in visible text, right where the number is. .ax-spectable__note is
					         11px uppercase mono, so it is kept to the mandated assumption only
					         — roughly the length of the same note in page-kitchens.php. The
					         exclusions run below in normal-case prose, where they are legible
					         at 375px instead of being buried in tracked-out caps. */ ?>
					<p class="ax-spectable__note">
						Illustrative arithmetic, not a quotation &mdash; assumes standard base + wall units, about
						<?php echo (int) $sqft_rft; ?> sq ft of cabinetry per running foot, so
						<?php echo (int) $ex_rft; ?> running feet &asymp; <?php echo (int) $ex_sqft; ?> sq ft at the
						published <?php echo esc_html( $ex_name ); ?> band.
					</p>
					<p class="ax-prose ax-mt-3">
						Cabinetry only; countertop, chimney, hob, sink and appliances are itemised separately.
					</p>
				</div>
			</div>
		</div>
	</section>

	<!-- ================================================== 6. WHAT WE PROMISE -->
	<section class="ax-section ax-section--ruled">
		<div class="ax-wrap ax-wrap--narrow">
			<header class="ax-head ax-reveal">
				<p class="ax-eyebrow">What we promise</p>
				<h2 class="ax-h2">Four things, and all four are checkable.</h2>
				<p class="ax-lead">
					Not adjectives. Each of these is either written on your quotation, handed to you as a document,
					or standing in a building you are welcome to walk into.
				</p>
			</header>
			<ul class="ax-prooflist ax-reveal">
				<li class="ax-proof">
					<span class="ax-proof__tick" aria-hidden="true"></span>
					<div class="ax-proof__text">
						<?php /* facts.php lists both brands as standard. No upgrade framing and no
						         price difference between them is claimed, because neither is verified. */ ?>
						Hardware from <?php echo esc_html( $brands_txt ); ?>, fitted as standard &mdash; hinges, runners
						and soft-close systems from named brands, specified line by line on your quotation.
						<span class="ax-proof__never">Never generic hardware</span>
					</div>
				</li>
				<li class="ax-proof">
					<span class="ax-proof__tick" aria-hidden="true"></span>
					<div class="ax-proof__text">
						A <?php echo (int) $warranty_yrs; ?>-year warranty, in writing &mdash; handed over as a document
						with your kitchen. What it covers is set out in the written document you receive; ask to read it
						on your free visit, before you commit to anything.
						<span class="ax-proof__never">Never a verbal promise</span>
					</div>
				</li>
				<li class="ax-proof">
					<span class="ax-proof__tick" aria-hidden="true"></span>
					<div class="ax-proof__text">
						Installation at your home in about <?php echo (int) $install_days; ?> days, because the
						manufacturing queue is ours rather than an outside workshop&rsquo;s.
					</div>
				</li>
				<li class="ax-proof">
					<span class="ax-proof__tick" aria-hidden="true"></span>
					<div class="ax-proof__text">
						Built in our own <?php echo esc_html( $sqft ); ?> sq ft factory at
						<?php echo esc_html( $place ); ?> &mdash; visit it and watch kitchens being made before you pay
						anything.
						<span class="ax-proof__never">Never outsourced</span>
					</div>
				</li>
			</ul>
		</div>
	</section>

	<!-- ================================================== 7. EXPERIENCE CENTRE -->
	<!-- Honest labels: photographs of our own display centre. The approved pool
	     holds no verified customer-home or shop-floor photography, so none is
	     claimed here. -->
	<section class="ax-section">
		<div class="ax-wrap">
			<header class="ax-head ax-reveal">
				<p class="ax-eyebrow">The experience centre</p>
				<h2 class="ax-h2">Handle it before you decide.</h2>
				<p class="ax-lead">
					Kitchen and wardrobe displays standing in our own experience centre, captioned as exactly what they
					show. Open the drawers, work the <?php echo esc_html( $brands_txt ); ?> hardware yourself, then drive
					out to <?php echo esc_html( $place ); ?> and see the factory behind them.
				</p>
			</header>
			<div class="ax-grid ax-grid--3">
				<?php foreach ( $gallery as $p ) : ?>
				<figure class="ax-media ax-media--43 ax-reveal">
					<span class="ax-media__frame">
						<img
							src="<?php echo esc_url( home_url( $p['src'] ) ); ?>"
							alt="<?php echo esc_attr( $p['alt'] ); ?>"
							loading="lazy">
						<span class="ax-media__tag">PLATE <?php echo esc_html( $p['no'] ); ?></span>
					</span>
					<figcaption class="ax-media__caption"><?php echo esc_html( $p['cap'] ); ?></figcaption>
				</figure>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<!-- ================================================== 8. FAQ -->
	<section class="ax-section ax-section--ruled">
		<div class="ax-wrap ax-wrap--narrow">
			<header class="ax-head ax-reveal">
				<p class="ax-eyebrow">Questions</p>
				<h2 class="ax-h2">Asked about the company.</h2>
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

	<!-- ================================================== 9. FINAL CTA -->
	<section class="ax-section ax-section--ruled" id="alea-book">
		<div class="ax-wrap">
			<div class="ax-grid ax-grid--split">
				<div class="ax-reveal">
					<p class="ax-eyebrow">Book a free visit</p>
					<h2 class="ax-h2">Come and check the claim.</h2>
					<p class="ax-lead ax-mt-4">
						Both visits are free and carry no obligation: a measurement at your home across
						<?php echo esc_html( $areas_txt ); ?>, or a factory visit at <?php echo esc_html( $place ); ?>
						where you can watch kitchens being built before you pay a rupee.
					</p>
					<div class="ax-btnrow ax-mt-5">
						<a class="ax-btn ax-btn--ink" href="<?php echo esc_url( $tel_href ); ?>">Call <?php echo esc_html( $phone_disp ); ?></a>
						<a class="ax-btn ax-btn--wa" href="<?php echo esc_url( $wa_href ); ?>">WhatsApp us</a>
					</div>
					<p class="ax-btn-note">Would rather see a number first? The estimator is free and needs no sign-up.</p>
				</div>
				<div class="ax-form ax-form--card ax-reveal">
					<h3 class="ax-form__title">Book your free visit</h3>
					<p class="ax-form__note">Free / no obligation / <?php echo esc_html( implode( ' · ', alea_fact( 'service_area', array() ) ) ); ?></p>
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
