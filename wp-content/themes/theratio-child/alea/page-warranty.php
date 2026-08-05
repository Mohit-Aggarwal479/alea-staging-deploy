<?php
/**
 * ALEA — Warranty page ("The Manufacturer's Record") — /warranty/
 * Included inside <main class="alea-main"> by the shell.
 *
 * The claim on this page is exactly one: a 10-year warranty, in writing —
 * both read from facts.php. Coverage DETAILS beyond "panels and hardware
 * against manufacturing defects" are NOT verified, so this page never
 * renders a covered/not-covered table and never characterises competitors'
 * warranties. It describes coverage in honest general terms and sends
 * people to read the full written document on a free visit.
 */

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/facts.php';

$f          = alea_facts();
$calc_url   = home_url( '/kitchen-cost-calculator/' );
$tel_href   = 'tel:' . alea_fact( 'phone_tel' );
$phone_disp = alea_fact( 'phone_display' );
$wty        = (int) $f['warranty_years'];
$brands     = implode( ' and ', $f['hardware_brands'] );
$wa_href    = alea_wa_link( sprintf( "Hi ALEA, I'd like to know more about the %d-year written warranty.", $wty ) );

/* FAQ — HTML answers and JSON-LD are rendered from this ONE array, so the
   schema always matches the visible text exactly. */
$faq = array(
	array(
		'q' => sprintf( 'What does the ALEA %d-year warranty cover?', $wty ),
		'a' => sprintf(
			'It covers the kitchen we build — panels and hardware — against manufacturing defects, for %1$d years. The full terms live in the written warranty document you receive with your kitchen; ask to read them on your free site visit or factory visit before you order.',
			$wty
		),
	),
	array(
		'q' => 'Is the warranty really in writing?',
		'a' => sprintf(
			'Yes. Every ALEA kitchen ships with the signed warranty document, handed over with the kitchen itself — you hold the paper, never a verbal promise. ALEA has been building modular kitchens since %1$s, and our parent furniture workshop has been making furniture since %2$s.',
			$f['alea_since'],
			$f['founded_furniture']
		),
	),
	array(
		'q' => 'How do I make a warranty claim?',
		'a' => sprintf(
			'Call or WhatsApp us on %1$s and keep your written warranty document at hand — it is the record of your kitchen. Every ALEA kitchen is built in our own factory at %2$s and installed by our own team, so the people who made your kitchen are the same people who look after it.',
			$phone_disp,
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
<div class="ax-root">

	<style data-no-optimize="1">
	/* Page-specific: the warranty record card only. */
	.axp-doc{background:var(--sheet);border:1px solid var(--rule);border-radius:var(--radius-lg);padding:var(--sp-5);--ax-ground:var(--sheet);--ax-rule:var(--rule)}
	.axp-doc__head{display:flex;align-items:baseline;justify-content:space-between;gap:var(--sp-3);padding-bottom:var(--sp-3);border-bottom:2px solid var(--ink);font-family:var(--font-mono);font-size:var(--fs-nano);letter-spacing:var(--ls-mono-wide);text-transform:uppercase;color:var(--graphite)}
	.axp-doc__foot{margin-top:var(--sp-4);font-family:var(--font-mono);font-size:var(--fs-micro);line-height:1.6;letter-spacing:var(--ls-mono);text-transform:uppercase;color:var(--graphite)}
	</style>

	<!-- ============ 1. HERO ============ -->
	<section class="ax-hero ax-hero--short">
		<?php /* Experience-centre display photograph (approved pool) — alt claims
		         only what is visible, never a delivered project. */ ?>
		<img
			class="ax-hero__img"
			src="<?php echo esc_url( home_url( '/wp-content/uploads/2022/09/Alea-Modular-Kitchen-Wardrobes-3.jpg' ) ); ?>"
			alt="Modular kitchen display with island at the ALEA experience centre"
			loading="eager"
			fetchpriority="high">
		<div class="ax-hero__inner">
			<p class="ax-eyebrow">The warranty</p>
			<h1 class="ax-hero__title">A <?php echo (int) $wty; ?>-year warranty. <em>In writing.</em></h1>
			<p class="ax-hero__sub">
				You receive the signed warranty document at handover, with the kitchen itself.
				You hold the paper — the promise is not made over the phone.
			</p>
			<div class="ax-hero__actions">
				<a class="ax-btn ax-btn--primary ax-btn--lg" href="#alea-book">Book a free visit — read the terms</a>
			</div>
			<p class="ax-hero__credit">
				<?php echo (int) $wty; ?>-YEAR WRITTEN WARRANTY
				/ <?php echo esc_html( strtoupper( implode( ' & ', $f['hardware_brands'] ) ) ); ?> HARDWARE
				/ <?php echo esc_html( $f['factory_sqft'] ); ?> SQ FT OWN FACTORY
			</p>
		</div>
	</section>

	<!-- ============ 2. SPEC STRIP ============ -->
	<section class="ax-section ax-section--flush" aria-label="Warranty key facts">
		<div class="ax-wrap ax-wrap--flush">
			<div class="ax-specstrip">
				<div class="ax-specstrip__item">
					<span class="ax-specstrip__label">Warranty term</span>
					<span class="ax-specstrip__value"><?php echo (int) $wty; ?><span class="ax-specstrip__unit">years</span></span>
				</div>
				<div class="ax-specstrip__item">
					<span class="ax-specstrip__label">Form</span>
					<span class="ax-specstrip__value">Written<span class="ax-specstrip__unit">signed</span></span>
				</div>
				<div class="ax-specstrip__item">
					<span class="ax-specstrip__label">Hardware</span>
					<span class="ax-specstrip__value"><?php echo esc_html( implode( ' & ', $f['hardware_brands'] ) ); ?></span>
				</div>
				<div class="ax-specstrip__item">
					<span class="ax-specstrip__label">Built in</span>
					<span class="ax-specstrip__value"><?php echo esc_html( $f['factory_sqft'] ); ?><span class="ax-specstrip__unit">sq ft own factory</span></span>
				</div>
			</div>
		</div>
	</section>

	<!-- ============ 3. WHAT IT MEANS IN PRACTICE ============ -->
	<section class="ax-section">
		<div class="ax-wrap">
			<div class="ax-grid ax-grid--split">
				<div class="ax-reveal">
					<div class="ax-head ax-mb-0">
						<p class="ax-eyebrow">In practice</p>
						<h2 class="ax-h2">What the paper says, in plain terms.</h2>
					</div>
					<div class="ax-prose ax-mt-5">
						<p>
							The warranty covers the kitchen we manufacture — panels and hardware —
							against manufacturing defects, for <?php echo (int) $wty; ?> years.
							The full terms live in the written document itself: every ALEA kitchen
							ships with the signed warranty, and you are welcome to read it
							line by line on your free site visit or factory visit before you order.
						</p>
						<p>
							We would rather hand you the actual document than summarise it into
							a marketing table. Ask for it — reading the terms costs you nothing
							and commits you to nothing.
						</p>
					</div>
					<ul class="ax-prooflist ax-mt-6">
						<li class="ax-proof">
							<span class="ax-proof__tick" aria-hidden="true"></span>
							<div class="ax-proof__text">
								<?php echo (int) $wty; ?> years, signed and handed over with the kitchen.
								<span class="ax-proof__never">Never a verbal promise</span>
							</div>
						</li>
						<li class="ax-proof">
							<span class="ax-proof__tick" aria-hidden="true"></span>
							<div class="ax-proof__text">
								Panels and hardware covered against manufacturing defects — the full terms in the document you receive.
								<span class="ax-proof__never">Never fine print you cannot read first</span>
							</div>
						</li>
						<li class="ax-proof">
							<span class="ax-proof__tick" aria-hidden="true"></span>
							<div class="ax-proof__text">
								You call us directly — the factory that built your kitchen, not a middleman who ordered it in.
								<span class="ax-proof__never">Never an anonymous claims queue</span>
							</div>
						</li>
					</ul>
				</div>
				<div class="ax-reveal">
					<div class="axp-doc">
						<div class="axp-doc__head">
							<span>ALEA / Warranty record</span>
							<span><?php echo (int) $wty; ?> YRS</span>
						</div>
						<div class="ax-spectable--rows">
							<div class="ax-spectable__row">
								<span class="ax-spectable__key">Term</span>
								<span class="ax-spectable__val"><?php echo (int) $wty; ?> years</span>
							</div>
							<div class="ax-spectable__row">
								<span class="ax-spectable__key">Form</span>
								<span class="ax-spectable__val">Written, signed</span>
							</div>
							<div class="ax-spectable__row">
								<span class="ax-spectable__key">You receive it</span>
								<span class="ax-spectable__val">With your kitchen, at handover</span>
							</div>
							<div class="ax-spectable__row">
								<span class="ax-spectable__key">Covers</span>
								<span class="ax-spectable__val">Panels &amp; hardware, mfg. defects</span>
							</div>
							<div class="ax-spectable__row">
								<span class="ax-spectable__key">Full terms</span>
								<span class="ax-spectable__val">In the document — read on a free visit</span>
							</div>
						</div>
						<p class="axp-doc__foot">
							Ask to read the full terms on your free site visit or factory visit.
						</p>
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- ============ 4. WHY WE CAN OFFER IT ============ -->
	<section class="ax-section ax-section--ink">
		<div class="ax-wrap">
			<div class="ax-grid ax-grid--split">
				<div class="ax-head ax-mb-0 ax-reveal">
					<p class="ax-eyebrow">Why we can offer it</p>
					<h2 class="ax-h2">A ten-year promise only works if you build the kitchen yourself.</h2>
					<p class="ax-lead ax-mt-4">
						We are not reselling someone else&rsquo;s cabinets. Every ALEA kitchen is built in our own
						<?php echo esc_html( $f['factory_sqft'] ); ?> sq ft factory at <?php echo esc_html( $f['factory_place'] ); ?>,
						on <?php echo esc_html( $brands ); ?> hardware, by a company that has made furniture since
						<?php echo esc_html( $f['founded_furniture'] ); ?>. When we warrant the build, we warrant our own work.
					</p>
				</div>
				<div class="ax-reveal">
					<div class="ax-spectable--rows">
						<div class="ax-spectable__row">
							<span class="ax-spectable__key">Own factory</span>
							<span class="ax-spectable__val"><?php echo esc_html( $f['factory_sqft'] ); ?> sq ft, <?php echo esc_html( $f['factory_place'] ); ?></span>
						</div>
						<div class="ax-spectable__row">
							<span class="ax-spectable__key">Hardware, named</span>
							<span class="ax-spectable__val"><?php echo esc_html( implode( ' & ', $f['hardware_brands'] ) ); ?></span>
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

	<!-- ============ 5. HOW TO CLAIM ============ -->
	<!-- No invented SLAs: the steps name the channel and the document, never a
	     response time or turnaround we have not verified. -->
	<section class="ax-section ax-section--ruled">
		<div class="ax-wrap">
			<div class="ax-head ax-reveal">
				<p class="ax-eyebrow">How to claim</p>
				<h2 class="ax-h2">Call the people who built it.</h2>
			</div>
			<ol class="ax-steps ax-reveal">
				<li class="ax-step">
					<div class="ax-step__body">
						<h3 class="ax-step__title">Call or WhatsApp us</h3>
						<p class="ax-step__text">On <?php echo esc_html( $phone_disp ); ?> — our local number, answered by us.</p>
					</div>
				</li>
				<li class="ax-step">
					<div class="ax-step__body">
						<h3 class="ax-step__title">Have your warranty document at hand</h3>
						<p class="ax-step__text">It is the record of your kitchen — the terms you and we agreed to, on paper.</p>
					</div>
				</li>
				<li class="ax-step">
					<div class="ax-step__body">
						<h3 class="ax-step__title">The factory takes it from there</h3>
						<p class="ax-step__text">Your kitchen was built on our own floor and installed by our own team — the people who made it are the people who look after it.</p>
					</div>
				</li>
			</ol>
			<div class="ax-btnrow ax-mt-6">
				<a class="ax-btn ax-btn--ink" href="<?php echo esc_url( $tel_href ); ?>">Call <?php echo esc_html( $phone_disp ); ?></a>
				<a class="ax-btn ax-btn--wa" href="<?php echo esc_url( $wa_href ); ?>">WhatsApp us</a>
			</div>
		</div>
	</section>

	<!-- ============ 6. FAQ ============ -->
	<section class="ax-section ax-section--ruled">
		<div class="ax-wrap ax-wrap--narrow">
			<div class="ax-head ax-reveal">
				<p class="ax-eyebrow">Questions</p>
				<h2 class="ax-h2">Asked about the warranty.</h2>
			</div>
			<div class="ax-faq ax-reveal">
				<?php foreach ( $faq as $item ) : ?>
				<details class="ax-faq__item">
					<summary class="ax-faq__q"><?php echo esc_html( $item['q'] ); ?></summary>
					<div class="ax-faq__a"><p><?php echo esc_html( $item['a'] ); ?></p></div>
				</details>
				<?php endforeach; ?>
			</div>
			<p class="ax-btn-note">
				Planning a new kitchen? <a class="ax-link" href="<?php echo esc_url( $calc_url ); ?>">See your price band first</a> &mdash; free, no sign-up.
			</p>
		</div>
	</section>
	<script type="application/ld+json"><?php echo wp_json_encode( $faq_schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ); ?></script>

	<!-- ============ 7. FINAL CTA BAND ============ -->
	<section class="ax-section ax-section--ruled" id="alea-book">
		<div class="ax-wrap">
			<div class="ax-grid ax-grid--split">
				<div class="ax-reveal">
					<p class="ax-eyebrow">Read it yourself</p>
					<h2 class="ax-h2">Come and read the warranty before you spend a rupee.</h2>
					<p class="ax-lead ax-mt-4">
						Book a free site visit at your home, or a free factory visit at
						<?php echo esc_html( $f['factory_place'] ); ?> — either way, ask for the written
						warranty terms and take your time with them. Both visits are free and carry no obligation.
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
