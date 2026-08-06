<?php
/**
 * ALEA — Customer process ("The Manufacturer's Record")
 * Replaces /about/customer-process/. Included inside <main class="alea-main">
 * by the shell; header, footer and the site-wide sticky mobile bar come from
 * the theme.
 *
 * SINGLE-URL template: it serves one path only, so it reads no variant from
 * $GLOBALS['alea_page_args']. (Multi-URL siblings — page-collection.php —
 * whitelist their variant there.)
 *
 * ROUTING (outside this file): alea_redesign_map() in functions.php must carry
 * an '/about/customer-process/' entry pointing here. Without it the shell never
 * includes this file, the wp_head design-system.css inliner never fires, and
 * the old Elementor page keeps serving.
 *
 * WHAT THIS PAGE IS: the BUYER's journey — first call to cooking. Its sibling
 * page tells the manufacturing story; this one tells the customer what happens
 * to them, in order, and what they hold in their hand at the end of each step.
 *
 * HONESTY NOTES — a process page is the easiest place on a site to invent an
 * operations promise, so the rules this file works under are written down:
 *
 * - THE ONLY DURATION CLAIMED IS 'install_days' FROM facts.php. No visit
 *   length, no design turnaround, no quotation turnaround, no total
 *   project duration and no callback time appears anywhere below, because
 *   facts.php verifies none of them. Where a step has no verified duration its
 *   .ax-step__time carries a SEQUENCE marker ("begins on your approval"), not
 *   an invented number. page-book-visit.php used to print "about an hour" for
 *   the visit; that claim has been struck there too, so the site refuses it
 *   consistently rather than in one place only. If a visit length is ever
 *   confirmed, add it to facts.php and render it from there on both pages.
 * - install_days is recorded in facts.php as a bare figure with no anchor, so
 *   this page says installation "takes about N days" — the same phrasing
 *   page-about.php and page-kitchens.php use. It is deliberately NOT rendered
 *   as "within N days OF APPROVAL": that anchor is an inference, and the
 *   sentence beside it sends the reader to get their own dates in writing.
 * - NO PAYMENT SCHEDULE. facts.php holds no stage-payment percentages, no
 *   booking amount and no advance, so none is printed. The visible answer is
 *   that the schedule is set out in the quotation — which is true, checkable
 *   and costs a reader nothing to verify before committing.
 * - Warranty is "10 years, in writing" and nothing more specific; what it
 *   covers lives in the written document the customer receives, and the page
 *   directs people to read it on the free visit.
 * - Hettich and Blum are BOTH standard. Neither is framed as an upgrade and no
 *   price difference between them is implied.
 * - SERVICE AREA is the four cities in facts.php and nothing else. The legacy
 *   city pages on this site are not linked and not claimed.
 * - The change-after-approval answer is general guidance about how joinery
 *   works — no ALEA change-order policy, fee or window is asserted, because
 *   none is verified.
 * - Photography comes from images.php only. No frame is captioned as a
 *   customer's home, a delivered project or a factory floor: the catalogue
 *   holds none of those.
 */

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/facts.php';
require_once __DIR__ . '/images.php';

$install_days = (int) alea_fact( 'install_days', 0 );   // 15
$warranty_yrs = (int) alea_fact( 'warranty_years', 0 ); // 10
$place        = alea_fact( 'factory_place' );           // 'Raipur Rani, Panchkula district'
$sqft         = alea_fact( 'factory_sqft' );            // '95,000'
$phone_disp   = alea_fact( 'phone_display' );
$tel_href     = 'tel:' . alea_fact( 'phone_tel' );
$wa_href      = alea_wa_link( "Hi ALEA, I'd like to book a free site visit and measurement." );

$brands       = implode( ' and ', alea_fact( 'hardware_brands', array() ) );
$areas        = implode( ', ', alea_fact( 'service_area', array() ) );

$calc_url     = home_url( '/kitchen-cost-calculator/' );
$visit_url    = home_url( '/book-a-free-design-visit/' );
$price_url    = home_url( '/modular-kitchen-price/' );
$warranty_url = home_url( '/warranty/' );
$factory_url  = home_url( '/our-factory/' );

/* ---- The steps. Every count printed on the page is derived from this array
 * (hero, spec strip, section eyebrow), so adding or removing one entry can
 * never leave the page contradicting itself.
 *
 * 'time' is the mono caption on the right of each step. Only step 4 carries a
 * number, and that number is facts.php's install_days — every other caption is
 * a SEQUENCE or PRICE marker ("free / no obligation", "begins on your
 * approval"), never a duration this site cannot stand behind.
 *
 * 'we' / 'you' / 'get' answer the three questions a buyer actually has at each
 * stage: what does the company do, what does it need from me, and what do I
 * physically hold afterwards.
 */
$steps = array(
	array(
		'title' => 'A free site visit and measurement, at your home',
		'text'  => 'It starts with someone standing in your kitchen. We measure the room as it actually is — where the door, the window, the water line and the gas point already are — because those decide the layout far more than taste does.',
		'time'  => 'Free / no obligation',
		'we'    => 'Come to your home across ' . $areas . ', measure the kitchen, and tell you plainly which layouts the room will take and which it will not.',
		'you'   => 'Be there, let us in, and say how you actually cook — who uses the kitchen, what is always on the counter, what never gets reached.',
		'get'   => 'Your measurements, written down. They are yours to keep whether or not you order anything from us.',
	),
	array(
		'title' => 'A design, and an itemised quotation you can read line by line',
		'text'  => 'We design to your measurements, then price the design in the open: every panel, finish and fitting on its own line rather than one lump sum at the bottom. A quotation you cannot read is not a quotation.',
		'time'  => 'Free / itemised in writing',
		'we'    => 'Draw the kitchen, choose the fittings, and price it line by line — cabinetry, ' . $brands . ' hardware and installation itemised, with countertop, chimney, hob, sink, appliances and civil work listed separately rather than folded in.',
		'you'   => 'Read it and interrogate it. Ask what a line means, what it costs to remove, and what changes if you pick a different finish.',
		'get'   => 'The itemised quotation, in writing. The payment schedule for your kitchen is set out in it — agreed with you before anything is cut.',
	),
	array(
		'title' => 'You approve, and production begins at our factory',
		'text'  => 'Approval is the moment the kitchen stops being a drawing. Nothing is cut before you give it, and once you do, your kitchen joins our own factory queue rather than being placed with an outside workshop.',
		'time'  => 'Begins on your approval',
		'we'    => 'Release your kitchen to our own ' . $sqft . ' sq ft factory at ' . $place . ' — cut, edged, assembled and finished on our own line.',
		'you'   => 'Approve the drawing and the quotation, and settle anything you were still unsure about first. Changes are simplest while the kitchen is still a drawing.',
		'get'   => 'A confirmed specification: the version of the kitchen the factory is building, matching the quotation you approved.',
	),
	array(
		'title' => 'Installation at your home',
		'text'  => 'Our own factory team delivers and fits the kitchen. Installation at your home takes about ' . $install_days . ' days. The dates for your kitchen are confirmed with you in writing — ask for them on the free visit, before you commit to anything.',
		'time'  => 'About ' . $install_days . ' days',
		'we'    => 'Deliver and install with our own team, because the manufacturing queue is ours rather than an outside workshop\'s.',
		'you'   => 'Have the civil work finished — tiling, plumbing and electrical are itemised separately from the kitchen and are not part of the per-sq-ft rate.',
		'get'   => 'The kitchen, fitted. Walk it with the team before they leave and say anything that is not right while they are still standing in it.',
	),
	array(
		'title' => 'Handover, the written warranty, and a number that still answers',
		'text'  => 'We hand over a ' . $warranty_yrs . '-year warranty as a document, not as a sentence someone said to you in a showroom. What it covers is set out in that written document — ask to read a copy on your free visit, long before you commit.',
		'time'  => $warranty_yrs . '-year written warranty',
		'we'    => 'Hand over the written warranty and show you how the ' . $brands . ' hardware works — how the soft-close is adjusted, how a drawer comes out and goes back.',
		'you'   => 'Keep the document. Check the kitchen with us at handover rather than after we have gone.',
		'get'   => 'The ' . $warranty_yrs . '-year warranty in writing, and the same phone number you started with: the company that quoted you is the one that built it, fitted it and answers afterwards.',
	),
);

/* ---- "What you never have to do." Two entries, because two are checkable on
   this site today: the rates are published, and the quotation is itemised. */
$nevers = array(
	array(
		'title' => 'Chase us for a price',
		'text'  => 'The rates are already published, per square foot of cabinetry, on the price list — and the estimator does the arithmetic for your own kitchen before we ask for your phone number. You should not have to fill in a form to find out whether a company is in your range.',
	),
	array(
		'title' => 'Guess what is included',
		'text'  => 'The quotation itemises it: cabinetry, ' . $brands . ' hardware, delivery across ' . $areas . ', installation and the ' . $warranty_yrs . '-year written warranty on their own lines, with countertop, chimney, hob, sink, appliances and civil work priced separately rather than buried. Nothing should appear on the bill that was not on the page.',
	),
);

/* ---- FAQ: the visible answers and the JSON-LD render from this ONE array, so
   the schema can never drift from the text on the page. ---- */
$faq = array(
	array(
		'q' => 'Is the site visit really free?',
		'a' => sprintf(
			'Yes — free, and with no obligation. We measure your kitchen at your home across %1$s and you keep the measurements either way, whether or not you order. A visit to the factory at %2$s is free too: you can watch kitchens being built before you pay anything. Call %3$s or use the form on this page.',
			$areas,
			$place,
			$phone_disp
		),
	),
	array(
		'q' => 'How long does the whole thing take, from first call to cooking?',
		'a' => sprintf(
			'The part we can put a number against is installation: at your home it takes about %1$d days. We do not publish a total for the whole process, because the stage before installation depends on decisions that are yours — how long you take over finishes, and when you approve the drawing and the quotation. Rather than a number we cannot stand behind, ask for your own dates in writing on the free site visit, and hold us to those.',
			$install_days
		),
	),
	array(
		'q' => 'When do I pay?',
		'a' => 'The payment schedule for your kitchen is set out in your quotation, and it is agreed with you in writing before anything is cut. We do not print a schedule on this page, because the honest version of that answer is the one written into your own quotation rather than a general rule quoted at you. Ask to see the schedule on your free visit — you are entitled to read it before you commit to anything.',
	),
	array(
		'q' => 'Can I change the design after I have approved it?',
		'a' => 'Speak up as early as you can. Approval is what releases your kitchen to the factory, so a change made while it is still a drawing costs nothing but a conversation, while a change made after panels are cut means remaking parts — which can affect both the price and the dates. That is how joinery works anywhere, not a penalty we invented. So use the quotation stage properly: ask what each alternative would cost before you approve. If something does have to change afterwards, tell us as soon as you know, and we will tell you plainly what is still possible and what it affects.',
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
	/* Page-specific: the hero's secondary text link, and the three-line
	   we / you / you-get list inside each step. */
	.axp-hero-alt{margin-top:var(--sp-3);font-size:15px;color:var(--ax-fg-muted)}
	.axp-hero-alt a{color:inherit}
	.axp-do{margin-top:var(--sp-4);border-top:1px solid var(--ax-rule)}
	.axp-do__row{display:grid;grid-template-columns:minmax(0,1fr);gap:var(--sp-1);padding:var(--sp-3) 0;border-bottom:1px solid var(--ax-rule)}
	.axp-do__k{font-family:var(--font-mono);font-size:var(--fs-nano);line-height:1.4;letter-spacing:var(--ls-mono-wide);text-transform:uppercase;color:var(--ax-fg-muted)}
	.axp-do__v{min-width:0;font-size:15px;line-height:1.55;color:var(--ax-fg);text-wrap:pretty}
	@media (min-width:640px){.axp-do__row{grid-template-columns:136px minmax(0,1fr);gap:var(--sp-4);align-items:baseline}}
	</style>

	<!-- ================================================== 1. HERO -->
	<section class="ax-hero">
		<?php
		/* 'kitchen-tall' — a verified experience-centre photograph, hero-graded
		   and portrait. Deliberately NOT 'kitchen-island': the sibling
		   manufacturing page opens on that frame, and two adjacent journey pages
		   should not open on the same picture. The alt travels with the file, so
		   it claims a display and never a customer's home: this page is about the
		   buyer's journey, and the catalogue holds no photograph of anybody's
		   finished kitchen. 'sizes' is overridden to 100vw because the hero spans
		   the full viewport, not the catalogue default's 700px. */
		echo alea_img( 'kitchen-tall', array( 'class' => 'ax-hero__img', 'eager' => true, 'sizes' => '100vw' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built and escaped by alea_img().
		?>
		<div class="ax-hero__inner">
			<p class="ax-eyebrow">How it works</p>
			<h1 class="ax-hero__title">From first call to cooking.</h1>
			<p class="ax-hero__sub">
				<?php echo esc_html( alea_num_word( count( $steps ), true ) ); ?> steps, in order, with what we do, what we need from you and what you
				actually receive at each one. It begins with a free measurement at your home and ends with a
				<?php echo (int) $warranty_yrs; ?>-year warranty in writing.
			</p>
			<div class="ax-hero__actions">
				<a class="ax-btn ax-btn--primary ax-btn--lg" href="<?php echo esc_url( $calc_url ); ?>">Get my price</a>
			</div>
			<p class="axp-hero-alt">or <a href="#alea-book">book a free design visit</a> &mdash; free, no obligation</p>
			<p class="ax-hero__credit">
				<?php echo (int) count( $steps ); ?> STEPS
				/ FREE SITE VISIT
				/ <?php echo (int) $warranty_yrs; ?>-YEAR WRITTEN WARRANTY
			</p>
		</div>
	</section>

	<!-- ================================================== 2. SPEC STRIP -->
	<!-- Four cells, and only two of them are numbers: the step count is derived
	     from the array below, and the two durations/terms come from facts.php.
	     Nothing here is a service-level promise. -->
	<section class="ax-section ax-section--flush" aria-label="The ALEA process at a glance">
		<div class="ax-wrap ax-wrap--flush">
			<div class="ax-specstrip">
				<div class="ax-specstrip__item">
					<span class="ax-specstrip__label">The process</span>
					<span class="ax-specstrip__value"><?php echo (int) count( $steps ); ?><span class="ax-specstrip__unit">steps</span></span>
				</div>
				<div class="ax-specstrip__item">
					<span class="ax-specstrip__label">Site visit</span>
					<span class="ax-specstrip__value">Free<span class="ax-specstrip__unit">no obligation</span></span>
				</div>
				<div class="ax-specstrip__item">
					<span class="ax-specstrip__label">Installation</span>
					<span class="ax-specstrip__value"><?php echo (int) $install_days; ?><span class="ax-specstrip__unit">days</span></span>
				</div>
				<div class="ax-specstrip__item">
					<span class="ax-specstrip__label">Warranty</span>
					<span class="ax-specstrip__value"><?php echo (int) $warranty_yrs; ?><span class="ax-specstrip__unit">years, written</span></span>
				</div>
			</div>
		</div>
	</section>

	<!-- ================================================== 3. THE STEPS -->
	<!-- The count is derived from $steps everywhere it appears, eyebrow included,
	     so adding a step can never leave the page contradicting itself. -->
	<section class="ax-section">
		<div class="ax-wrap">
			<header class="ax-head ax-head--wide ax-reveal">
				<p class="ax-eyebrow">The <?php echo esc_html( alea_num_word( count( $steps ) ) ); ?> steps</p>
				<h2 class="ax-h2">What happens, in the order it happens.</h2>
				<p class="ax-lead">
					Each step says three things: what we do, what we need from you, and what you hold in your hand
					afterwards. Where a stage has a duration we can stand behind, it is printed. Where it does not,
					we say so instead of inventing one.
				</p>
			</header>
			<ol class="ax-steps ax-reveal">
				<?php foreach ( $steps as $s ) : ?>
				<li class="ax-step">
					<div class="ax-step__body">
						<h3 class="ax-step__title"><?php echo esc_html( $s['title'] ); ?></h3>
						<p class="ax-step__text"><?php echo esc_html( $s['text'] ); ?></p>
						<?php /* .ax-step__time sits INSIDE .ax-step__body: as a direct child of
						         .ax-step it would land in the 44px numeral column below
						         1024px. Nested it is not a grid item, which is why the kit's
						         >=1024px rules for it are scoped to `.ax-step > .ax-step__time`
						         — the chip keeps its own margin-top here. */ ?>
						<span class="ax-step__time"><?php echo esc_html( $s['time'] ); ?></span>
						<?php /* A real <dl>: each label is tied to its value, so a screen
						         reader reads three pairs rather than six loose fragments. */ ?>
						<dl class="axp-do">
							<div class="axp-do__row">
								<dt class="axp-do__k">We do</dt>
								<dd class="axp-do__v"><?php echo esc_html( $s['we'] ); ?></dd>
							</div>
							<div class="axp-do__row">
								<dt class="axp-do__k">You do</dt>
								<dd class="axp-do__v"><?php echo esc_html( $s['you'] ); ?></dd>
							</div>
							<div class="axp-do__row">
								<dt class="axp-do__k">You receive</dt>
								<dd class="axp-do__v"><?php echo esc_html( $s['get'] ); ?></dd>
							</div>
						</dl>
					</div>
				</li>
				<?php endforeach; ?>
			</ol>
			<?php /* Normal-case prose, not .ax-btn-note: a sentence this long in
			         tracked-out 11px caps is unreadable at 375px, and the warranty
			         route is a real control with a 44px tap target rather than an
			         18px inline link. .ax-btn-note stays for short mono riders. */ ?>
			<p class="ax-prose ax-mt-6">
				The only duration on this page is installation, and it comes from the same place as every other number
				we print. The <?php echo (int) $warranty_yrs; ?>-year warranty is handed over in writing, and what it
				covers is set out in that written document.
			</p>
			<div class="ax-btnrow ax-mt-5">
				<a class="ax-btn ax-btn--ghost" href="<?php echo esc_url( $warranty_url ); ?>">Read about the written warranty</a>
			</div>
		</div>
	</section>

	<!-- ================================================== 4. WHAT YOU NEVER HAVE TO DO -->
	<!-- Both entries are checkable on this site today: the rates are published,
	     and the quotation is itemised. Nothing here claims what anyone else does. -->
	<section class="ax-section ax-section--ruled ax-section--sheet">
		<div class="ax-wrap">
			<header class="ax-head ax-head--wide ax-reveal">
				<p class="ax-eyebrow">What you never have to do</p>
				<h2 class="ax-h2">Two things this process is designed to spare you.</h2>
				<p class="ax-lead">
					Buying a kitchen usually involves a chase and a guess. Neither is necessary, and both are settled
					before you speak to anyone.
				</p>
			</header>
			<div class="ax-grid ax-grid--2 ax-grid--ruled ax-reveal">
				<?php foreach ( $nevers as $n ) : ?>
				<div>
					<h3 class="ax-card__title"><?php echo esc_html( $n['title'] ); ?></h3>
					<p class="ax-card__body"><?php echo esc_html( $n['text'] ); ?></p>
				</div>
				<?php endforeach; ?>
			</div>
			<div class="ax-btnrow ax-mt-6">
				<a class="ax-btn ax-btn--ghost" href="<?php echo esc_url( $price_url ); ?>">See the published price list</a>
				<a class="ax-btn ax-btn--ghost" href="<?php echo esc_url( $factory_url ); ?>">See inside the factory</a>
			</div>
		</div>
	</section>

	<!-- ================================================== 5. TWO FREE THINGS -->
	<!-- id="estimate": landing anchor for the theme's site-wide sticky mobile
	     bar (.aleac-mbar "Free Estimate" button in functions.php). -->
	<span id="estimate" class="ax-sr-only"></span>
	<section class="ax-section ax-section--ruled">
		<div class="ax-wrap">
			<header class="ax-head ax-head--wide ax-reveal">
				<p class="ax-eyebrow">Start here</p>
				<h2 class="ax-h2">Two free things you can do right now.</h2>
				<p class="ax-lead">
					Both cost nothing and neither commits you to anything. Most people do the first one first, because
					it answers the only question that matters before the rest is worth your evening.
				</p>
			</header>
			<div class="ax-grid ax-grid--2 ax-reveal">
				<article class="ax-card">
					<p class="ax-mono--label">Option 01</p>
					<h3 class="ax-card__title ax-mt-3">Get an instant estimate</h3>
					<p class="ax-card__body">
						Put in your kitchen&rsquo;s length and a collection, and the estimator gives you a range from the
						published per-square-foot rates &mdash; no sign-up, no phone number, no waiting for a call back.
					</p>
					<p class="ax-mt-5">
						<a class="ax-btn ax-btn--primary ax-btn--block-sm" href="<?php echo esc_url( $calc_url ); ?>">Get my price</a>
					</p>
				</article>
				<article class="ax-card">
					<p class="ax-mono--label">Option 02</p>
					<h3 class="ax-card__title ax-mt-3">Book the free site visit</h3>
					<p class="ax-card__body">
						We measure your kitchen at your home across <?php echo esc_html( $areas ); ?>, tell you which layouts
						the room will take, and leave the measurements with you. Free, no obligation, and yours to keep
						either way.
					</p>
					<p class="ax-mt-5">
						<a class="ax-btn ax-btn--ghost ax-btn--block-sm" href="<?php echo esc_url( $visit_url ); ?>">Book a free design visit</a>
					</p>
				</article>
			</div>
		</div>
	</section>

	<!-- ================================================== 6. FAQ -->
	<section class="ax-section ax-section--ruled">
		<div class="ax-wrap ax-wrap--narrow">
			<header class="ax-head ax-reveal">
				<p class="ax-eyebrow">Questions</p>
				<h2 class="ax-h2">Asked before people commit.</h2>
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

	<!-- ================================================== 7. BOOK — step one -->
	<section class="ax-section ax-section--ruled" id="alea-book">
		<div class="ax-wrap">
			<div class="ax-grid ax-grid--split">
				<div class="ax-reveal">
					<p class="ax-eyebrow">Book a free design visit</p>
					<h2 class="ax-h2">Step one is a measurement, and it costs nothing.</h2>
					<p class="ax-lead ax-mt-4">
						Both visits are free and carry no obligation: a measurement at your home across
						<?php echo esc_html( $areas ); ?>, or a visit to the factory at <?php echo esc_html( $place ); ?>
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
