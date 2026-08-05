<?php
/**
 * ALEA — Book a Free Design Visit ("The Manufacturer's Record")
 * /book-a-free-design-visit/ — the site's single hard-conversion destination.
 * Included inside <main class="alea-main"> by the shell.
 *
 * HONESTY NOTES
 * - Every business fact comes from facts.php. Nothing is hard-coded.
 * - No response-time SLA is promised anywhere: the page says only that we
 *   call back within working hours, which is what the business actually does.
 * - No whole-kitchen rupee estimate is rendered here, so no sqft-per-rft
 *   assumption needs stating; the only figures shown are the published rate
 *   band, always named as "per sq ft of cabinetry" and derived by min/max
 *   over the collection bands in facts.php.
 * - Warranty: the page says you can ASK to read the written terms on the
 *   visit. It never promises them as a handout, and never describes coverage.
 * - Two visit types, two different promises: measurement happens at your
 *   home. Nothing on the page claims we measure your kitchen at the factory.
 * - The photograph is experience-centre display photography (approved pool).
 *   Its alt text claims only what is visible — never a customer home, never
 *   a delivered project, never the factory floor. Kept deliberately general:
 *   the uploads directory is not in this repo, so no detail inside the frame
 *   can be verified from the tree.
 * - This page IS the CTA, so it deliberately ends on the FAQ. No second
 *   CTA band.
 */

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/facts.php';

$f          = alea_facts();
$calc_url   = home_url( '/kitchen-cost-calculator/' );
$tel_href   = 'tel:' . alea_fact( 'phone_tel' );
$phone_disp = alea_fact( 'phone_display' );
$wa_href    = alea_wa_link( "Hi ALEA, I'd like to book a free design visit." );
$areas      = $f['service_area'];

/* Published per-sq-ft rate band, derived from the collection bands in
   facts.php — a rate, not a kitchen total, so it needs no assumption note.
   Computed from the array (min/max), never by naming a collection, so adding
   or reordering a tier in facts.php can never publish a wrong band here. */
$band_low  = (int) min( array_column( $f['collections'], 'from' ) );
$band_high = (int) max( array_column( $f['collections'], 'to' ) );

/* The two visit types. Rendered from one array so the cards and the copy
   below them can never drift apart. */
$visits = array(
	array(
		'kicker' => 'Option A',
		'title'  => 'At your home',
		'lead'   => 'A site visit and measurement. We measure the room ourselves, look at where the water, gas and power already are, and talk through what will actually fit.',
		'rows'   => array(
			'Where'    => 'Your home',
			'We bring' => 'Tape, notepad, sample finishes',
			'You get'  => 'Measurements and a ballpark',
		),
	),
	array(
		'kicker' => 'Option B',
		'title'  => 'At our factory',
		'lead'   => 'Watch kitchens being made. See the panels, the edge banding and the ' . implode( ' and ', $f['hardware_brands'] ) . ' hardware first-hand — open and close the drawers yourself before you order anything. Your kitchen is measured at your home afterwards, once you decide to go ahead.',
		'rows'   => array(
			'Where'           => $f['factory_place'],
			'You see'         => 'Materials and hardware, in the flesh',
			'You can ask for' => 'The written warranty terms, to read on the spot',
		),
	),
);

/* FAQ — the HTML answers and the JSON-LD are rendered from this ONE array,
   so the schema always matches the visible text exactly. */
$faq = array(
	array(
		'q' => 'Is the design visit really free?',
		'a' => 'Yes. The visit, the measurement and the ballpark price are free, and you are under no obligation to order anything afterwards. If you decide against us, you still keep the measurements.',
	),
	array(
		'q' => 'Do I need drawings or a floor plan before you come?',
		'a' => 'No. Bring nothing if you have nothing — we measure the room ourselves on the visit. A floor plan from your builder, a few photographs or a rough idea of the layout you want all help, but none of them is needed to book.',
	),
	array(
		'q' => 'Which areas do you visit?',
		'a' => sprintf(
			'We design and install across %1$s. Our factory is at %2$s, so you are also welcome to come to us instead of — or as well as — a visit at your home.',
			implode( ', ', $areas ),
			$f['factory_place']
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
<div class="ax-root ax-has-stickybar">

	<style data-no-optimize="1">
	/* Page-specific only. Anything design-system.css already does (card padding,
	   .ax-btnrow stacking, the mono label) is NOT restated here. */
	.axp-hero-alt{margin-top:var(--sp-3);font-size:.9375rem;color:#E4E1DC}
	/* tel: link in the hero — a real 44px tap target, not a 25px inline box. */
	.axp-hero-alt a{color:inherit;display:inline-flex;align-items:center;min-height:var(--tap);vertical-align:middle}
	.axp-visit{gap:var(--sp-4)}
	.axp-talk{text-align:left}
	</style>

	<!-- ============ 1. HERO — the reassurance ============ -->
	<section class="ax-hero ax-hero--short">
		<?php /* Experience-centre display photograph (approved pool). Alt states
		         only what is visible — no customer-home or project claim. */ ?>
		<img
			class="ax-hero__img"
			src="<?php echo esc_url( home_url( '/wp-content/uploads/2022/09/Alea-Modular-Kitchen-Wardrobes-6.jpg' ) ); ?>"
			alt="Modular kitchen display at the ALEA experience centre"
			loading="eager"
			fetchpriority="high">
		<div class="ax-hero__inner">
			<p class="ax-eyebrow">Book a free design visit</p>
			<h1 class="ax-hero__title">A free visit. <em>At your home or our factory.</em> No obligation.</h1>
			<p class="ax-hero__sub">
				At your home we measure the kitchen; at our factory you see how it is made.
				Either way you talk through layout, finishes and hardware, and leave with a
				ballpark price — whether or not you ever order from us.
			</p>
			<div class="ax-hero__actions">
				<a class="ax-btn ax-btn--primary ax-btn--lg" href="#alea-book">Book my free visit</a>
			</div>
			<p class="axp-hero-alt">or <a href="<?php echo esc_url( $tel_href ); ?>">call <?php echo esc_html( $phone_disp ); ?></a> if you would rather just talk</p>
			<p class="ax-hero__credit">
				FREE / NO DEPOSIT
				/ NOTHING TO SIGN ON THE DAY
			</p>
		</div>
	</section>

	<!-- ============ 2. SPEC STRIP ============ -->
	<section class="ax-section ax-section--flush" aria-label="Visit key facts">
		<div class="ax-wrap ax-wrap--flush">
			<div class="ax-specstrip">
				<div class="ax-specstrip__item">
					<span class="ax-specstrip__label">Cost of the visit</span>
					<span class="ax-specstrip__value">Free<span class="ax-specstrip__unit">always</span></span>
				</div>
				<div class="ax-specstrip__item">
					<span class="ax-specstrip__label">Obligation</span>
					<span class="ax-specstrip__value">None<span class="ax-specstrip__unit">no deposit</span></span>
				</div>
				<div class="ax-specstrip__item">
					<span class="ax-specstrip__label">Visit at</span>
					<span class="ax-specstrip__value">Home<span class="ax-specstrip__unit">or our factory</span></span>
				</div>
				<?php /* A single fact, not a list: the strip is a mono band of short
				         values. The four areas are named once, in the FAQ below. */ ?>
				<div class="ax-specstrip__item">
					<span class="ax-specstrip__label">We design &amp; install in</span>
					<span class="ax-specstrip__value"><?php echo (int) count( $areas ); ?><span class="ax-specstrip__unit">areas &mdash; see FAQ</span></span>
				</div>
			</div>
		</div>
	</section>

	<!-- ============ 3. WHAT HAPPENS ON THE VISIT ============ -->
	<section class="ax-section">
		<div class="ax-wrap">
			<div class="ax-grid ax-grid--split">
				<div class="ax-head ax-mb-0 ax-reveal">
					<p class="ax-eyebrow">What happens</p>
					<h2 class="ax-h2">Three things, and then you decide.</h2>
					<p class="ax-lead ax-mt-4">
						A visit at your home usually takes about an hour. Whichever visit you
						choose, nobody asks you for a deposit and nothing is signed on the day.
					</p>
				</div>
				<ul class="ax-prooflist ax-reveal">
					<li class="ax-proof">
						<span class="ax-proof__tick" aria-hidden="true"></span>
						<div class="ax-proof__text">
							At your home we measure the kitchen properly — the room, the openings,
							and where the water, gas and power already are. If you come to the
							factory first, that measurement happens at your home afterwards.
							<span class="ax-proof__never">Never a guess from a photograph</span>
						</div>
					</li>
					<li class="ax-proof">
						<span class="ax-proof__tick" aria-hidden="true"></span>
						<div class="ax-proof__text">
							We talk through layout, finishes and
							<?php echo esc_html( implode( ' and ', $f['hardware_brands'] ) ); ?> hardware —
							what fits the room, and what fits the budget you have in mind.
							<span class="ax-proof__never">Never a fixed package you must take whole</span>
						</div>
					</li>
					<li class="ax-proof">
						<span class="ax-proof__tick" aria-hidden="true"></span>
						<div class="ax-proof__text">
							You leave with a ballpark, priced off our published rates of
							<span class="ax-mono ax-ink">&#8377;<?php echo esc_html( alea_inr( $band_low ) ); ?>&ndash;<?php echo esc_html( alea_inr( $band_high ) ); ?> per sq ft of cabinetry</span>.
							<span class="ax-proof__never">Never a number invented at the door</span>
						</div>
					</li>
				</ul>
			</div>
			<p class="ax-btn-note">
				Want a rough figure before we even meet?
				<a class="ax-link" href="<?php echo esc_url( $calc_url ); ?>">Use the estimator</a> &mdash; free, no sign-up.
			</p>
		</div>
	</section>

	<!-- ============ 4. CHOOSE YOUR VISIT TYPE ============ -->
	<!-- Both cards point at the one form below; neither carries its own
	     primary button, so this screen keeps a single conversion. -->
	<section class="ax-section ax-section--ruled">
		<div class="ax-wrap">
			<div class="ax-head ax-reveal">
				<p class="ax-eyebrow">Choose your visit</p>
				<h2 class="ax-h2">Come to us, or we come to you.</h2>
				<p class="ax-lead">
					Both are free, and you can do both — measure at home first, then come and
					see the materials before you commit.
				</p>
			</div>
			<div class="ax-grid ax-grid--2 axp-visit">
				<?php foreach ( $visits as $v ) : ?>
				<article class="ax-card ax-reveal">
					<p class="ax-mono--label ax-mb-3"><?php echo esc_html( $v['kicker'] ); ?></p>
					<h3 class="ax-card__title"><?php echo esc_html( $v['title'] ); ?></h3>
					<p class="ax-card__body"><?php echo esc_html( $v['lead'] ); ?></p>
					<div class="ax-spectable--rows ax-mt-5">
						<?php foreach ( $v['rows'] as $k => $val ) : ?>
						<div class="ax-spectable__row">
							<span class="ax-spectable__key"><?php echo esc_html( $k ); ?></span>
							<span class="ax-spectable__val"><?php echo esc_html( $val ); ?></span>
						</div>
						<?php endforeach; ?>
					</div>
				</article>
				<?php endforeach; ?>
			</div>
			<p class="ax-btn-note">Tell us which one you would prefer in the form below &mdash; you can change your mind later.</p>
		</div>
	</section>

	<!-- ============ 5. THE FORM ============ -->
	<!-- id="estimate": landing anchor for the theme's site-wide sticky mobile
	     bar (.aleac-mbar "Free Estimate" button in functions.php). The section
	     itself already carries id="alea-book" for this page's own hero link,
	     and an element can only hold one id, so the bar gets its own marker. -->
	<span id="estimate" class="ax-sr-only"></span>
	<section class="ax-section ax-section--ruled" id="alea-book">
		<div class="ax-wrap">
			<div class="ax-grid ax-grid--split">
				<div class="ax-reveal">
					<p class="ax-eyebrow">The booking</p>
					<h2 class="ax-h2">Four lines, and we will do the rest.</h2>
					<p class="ax-lead ax-mt-4">
						In the message box, please mention three things: the day and time that
						suit you, your area, and whether you would like the visit at your home or
						at our factory at <?php echo esc_html( $f['factory_place'] ); ?>.
					</p>
					<div class="ax-spectable--rows ax-mt-5">
						<div class="ax-spectable__row">
							<span class="ax-spectable__key">Preferred day &amp; time</span>
							<span class="ax-spectable__val">e.g. Saturday morning</span>
						</div>
						<div class="ax-spectable__row">
							<span class="ax-spectable__key">Your area</span>
							<span class="ax-spectable__val"><?php echo esc_html( implode( ' / ', $areas ) ); ?></span>
						</div>
						<div class="ax-spectable__row">
							<span class="ax-spectable__key">Visit type</span>
							<span class="ax-spectable__val">At home, or at the factory</span>
						</div>
					</div>
					<p class="ax-btn-note">We use what you write only to arrange the visit.</p>
				</div>
				<div class="ax-form ax-form--card ax-reveal">
					<h3 class="ax-form__title">Book your free visit</h3>
					<p class="ax-form__note">Free / no obligation / at your home or our factory</p>
					<?php echo do_shortcode( '[contact-form-7 id="7dcf010" title="Home Page form"]' ); ?>
					<p class="ax-form__fineprint ax-mt-4">
						We use your details only to arrange your visit and estimate.
					</p>
				</div>
			</div>
		</div>
	</section>

	<!-- ============ 6. WHAT HAPPENS NEXT ============ -->
	<!-- Deliberately no response-time promise. "Within working hours" is what
	     we actually do; an SLA we have not verified is not published here. -->
	<section class="ax-section ax-section--ruled">
		<div class="ax-wrap">
			<div class="ax-head ax-reveal">
				<p class="ax-eyebrow">What happens next</p>
				<h2 class="ax-h2">Three steps, and none of them cost you anything.</h2>
			</div>
			<ol class="ax-steps ax-reveal">
				<li class="ax-step">
					<div class="ax-step__body">
						<h3 class="ax-step__title">We call you back</h3>
						<p class="ax-step__text">Someone from our team rings the number you gave, within working hours, to check the day, the time and the address.</p>
						<span class="ax-step__time">Within working hours</span>
					</div>
				</li>
				<li class="ax-step">
					<div class="ax-step__body">
						<h3 class="ax-step__title">We confirm the slot</h3>
						<p class="ax-step__text">You tell us what actually suits, and we confirm it back to you on WhatsApp or by call, so the time is written down somewhere you can see it.</p>
						<span class="ax-step__time">By call or WhatsApp</span>
					</div>
				</li>
				<li class="ax-step">
					<div class="ax-step__body">
						<h3 class="ax-step__title">The visit itself</h3>
						<p class="ax-step__text">At your home we measure, talk layout and finishes, and leave you with a ballpark. At the factory we walk you through the materials and hardware instead, and measure at your home afterwards. Either way: no deposit, nothing to sign.</p>
						<span class="ax-step__time">About an hour at your home</span>
					</div>
				</li>
			</ol>
		</div>
	</section>

	<!-- ============ 7. PREFER TO TALK ============ -->
	<section class="ax-section ax-section--ink">
		<div class="ax-wrap">
			<div class="ax-grid ax-grid--split">
				<div class="ax-head ax-mb-0 ax-reveal">
					<p class="ax-eyebrow">Rather not fill a form?</p>
					<h2 class="ax-h2">Then just call us.</h2>
					<p class="ax-lead ax-mt-4">
						One local number, answered by the company that builds the kitchens —
						not a call centre in another city. WhatsApp works just as well if
						typing is easier.
					</p>
				</div>
				<div class="axp-talk ax-reveal">
					<div class="ax-btnrow">
						<a class="ax-btn ax-btn--primary ax-btn--lg" href="<?php echo esc_url( $tel_href ); ?>">Call <?php echo esc_html( $phone_disp ); ?></a>
						<a class="ax-btn ax-btn--wa ax-btn--lg" href="<?php echo esc_url( $wa_href ); ?>">WhatsApp us</a>
					</div>
					<p class="ax-btn-note">Free &middot; no obligation &middot; ask for a site visit or a factory visit</p>
				</div>
			</div>
		</div>
	</section>

	<!-- ============ 8. FAQ ============ -->
	<section class="ax-section ax-section--ruled">
		<div class="ax-wrap ax-wrap--narrow">
			<div class="ax-head ax-reveal">
				<p class="ax-eyebrow">Questions</p>
				<h2 class="ax-h2">Asked before the visit.</h2>
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

	<!-- No second CTA band: this page IS the CTA. The form above is the only
	     conversion surface. The sticky mobile bar is site-wide (.aleac-mbar);
	     its "Free Estimate" button lands on the #estimate marker above, and
	     .ax-has-stickybar on .ax-root reserves the space it occupies. -->

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
