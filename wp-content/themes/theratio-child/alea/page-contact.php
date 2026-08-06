<?php
/**
 * ALEA — Contact ("The Manufacturer's Record")
 * Replaces /contact/. Included inside <main class="alea-main"> by the shell;
 * header, footer and the site-wide sticky mobile bar come from the theme.
 *
 * SINGLE-URL template: it serves one path only, so it reads no variant from
 * $GLOBALS['alea_page_args']. (Multi-URL siblings — page-collection.php —
 * whitelist their variant there, with a safe default.)
 *
 * Routed from alea_redesign_map() in functions.php at '/contact/'.
 *
 * HONESTY NOTES
 * - Every business fact comes from facts.php. Nothing is hard-coded, and no
 *   claim is made that facts.php cannot back — including about who answers the
 *   phone or how a message is routed once it arrives. facts.php holds a number,
 *   not a call-handling process, so the page says only that all three channels
 *   reach ALEA.
 * - NO map embed of any kind: an external iframe is a third-party request and
 *   this page makes none.
 * - NO street address and NO opening hours are printed. Neither is verified,
 *   and the page says plainly why: we fix a time with you instead. The only
 *   location claim is facts.php 'factory_place' — Raipur Rani, Panchkula
 *   district — never "in Panchkula city".
 * - NO response-time SLA. The page promises only a call back within working
 *   hours, which is what the business actually does. The hours themselves are
 *   not printed: facts.php 'unverified.working_hours' is the TODO key holding
 *   that gap open until the owner confirms them.
 * - Only the four areas in facts.php 'service_area' are claimed as served.
 *   No landmarks, sectors, drive times or city-specific promises are invented.
 * - The only rupee figure is the published PER SQ FT rate band, derived by
 *   min/max over the collection bands in facts.php. No whole-kitchen total is
 *   rendered anywhere here, so no sq-ft-per-running-foot assumption applies.
 * - The one photograph comes from images.php by catalogue key, never by path,
 *   and its alt text comes with it — no page hand-writes a description, so two
 *   pages cannot describe the same frame differently. The frame is the client
 *   seating area at ALEA's experience centre: a real photograph of ALEA's own
 *   premises, not a customer's home, not a delivered project and NOT the
 *   factory floor. Its credit line therefore carries NO location — captioning
 *   an experience-centre interior "FACTORY AT ..." would be exactly the
 *   mis-framing the image rules exist to prevent. The factory location is
 *   stated as prose in section 5 instead.
 * - This page IS the CTA. The form sits directly under the hero, both anchors
 *   land ON the form card rather than on the section that contains it, and the
 *   page ends on the FAQ — there is no second CTA band.
 */

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/facts.php';
require_once __DIR__ . '/images.php';

/* A11Y — the shared lead form ships from the DATABASE with zero <label>
 * elements and a submittable fake <select> prompt. Both are repaired on CF7's
 * own 'wpcf7_form_elements' filter, which now lives in alea/cf7.php and is
 * loaded by functions.php for EVERY redesigned page rather than only by the
 * template that happens to define it. Required here as well so this template
 * keeps working if it is ever included outside that router. */
require_once __DIR__ . '/cf7.php';

$f          = alea_facts();
$tel_href   = 'tel:' . alea_fact( 'phone_tel' );
$phone_disp = alea_fact( 'phone_display' );
$wa_href    = alea_wa_link( "Hi ALEA, I'd like to talk about a modular kitchen." );
$areas      = $f['service_area'];
$price_unit = (string) alea_fact( 'price_unit' );

/* Published rate band, derived from EVERY collection band in facts.php — never
   from two hard-keyed slugs — so adding or retiring a collection there cannot
   leave this page quoting a stale band. A rate, not a kitchen total, so it
   carries no estimation assumption. */
$band_low  = (int) min( array_column( $f['collections'], 'from' ) );
$band_high = (int) max( array_column( $f['collections'], 'to' ) );

/* The spec strip wants a short value + a short qualifier. Both are SPLIT off
   the one facts.php string rather than retyped, so the strip cannot drift from
   the prose — and cannot become "Panchkula city" if that string is corrected. */
$place_parts = array_map( 'trim', explode( ',', (string) $f['factory_place'], 2 ) );
$place_main  = isset( $place_parts[0] ) ? $place_parts[0] : (string) $f['factory_place'];
$place_qual  = isset( $place_parts[1] ) ? $place_parts[1] : '';

/* Quick links. Every destination is a page in alea_redesign_map(); this page
   never sends anyone away to convert — the form above is the conversion. */
$quick_links = array(
	array(
		'url'   => '/kitchen-cost-calculator/',
		'label' => 'Kitchen cost calculator',
		'text'  => 'See a price band for your own kitchen length, yourself. Free, and it asks for no phone number.',
		'cta'   => 'Get my price',
	),
	array(
		'url'   => '/modular-kitchen-price/',
		'label' => 'Our published prices',
		'text'  => sprintf(
			'The rate card, in public: %1$s–%2$s %3$s of cabinetry, with what is included and what is not.',
			'₹' . alea_inr( $band_low ),
			'₹' . alea_inr( $band_high ),
			$price_unit
		),
		'cta'   => 'See the prices',
	),
	array(
		'url'   => '/book-a-free-design-visit/',
		'label' => 'Book a free design visit',
		'text'  => sprintf(
			'A free measurement at your home, or a free visit to the factory at %s to watch kitchens being built.',
			$f['factory_place']
		),
		'cta'   => 'Book a free design visit',
	),
);

/* FAQ — the visible answers and the JSON-LD are rendered from this ONE array,
   so the schema can never drift from the text on the page. */
$faq = array(
	array(
		'q' => 'What is the quickest way to reach you?',
		'a' => sprintf(
			'A call or a WhatsApp message to %1$s — the same number for both, and it reaches the company that builds the kitchens. If typing is easier, use the form on this page: we call you back on the number you give, within working hours.',
			$phone_disp
		),
	),
	array(
		'q' => 'Can I come and see the factory?',
		'a' => sprintf(
			'Yes, and it is free. Our manufacturing unit is at %1$s — %2$s sq ft of it — and you are welcome to watch kitchens being built before you pay anything. We do not print walk-in hours here because we would rather fix a time with you than have you arrive to a closed gate: call or WhatsApp %3$s and we will arrange it.',
			$f['factory_place'],
			$f['factory_sqft'],
			$phone_disp
		),
	),
	array(
		'q' => 'Can you give me a price over the phone?',
		'a' => sprintf(
			'A range, yes. Our rates are published — ₹%1$s–%2$s %3$s of cabinetry — and the online estimator uses those same rates, so you can see a band for your own kitchen for free without speaking to anyone. The exact figure comes after a free measurement at your home, itemised line by line rather than guessed at on a call.',
			alea_inr( $band_low ),
			alea_inr( $band_high ),
			$price_unit
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
	/* Page-specific only: the giant click-to-call, the hero's secondary line and
	   the quick-link card foot. Anything design-system.css already does is not
	   restated here. The number is chalk, never oxblood — oxblood on this page
	   is spent on the form's submit and the eyebrow rules, and nowhere else. */
	.axp-call{display:inline-block;max-width:100%;margin-top:var(--sp-5);padding-bottom:var(--sp-2);border-bottom:2px solid var(--chalk-a38);color:#FFF;text-decoration:none}
	.axp-call:hover,.axp-call:focus-visible{border-bottom-color:#FFF}
	.axp-call__label{display:block;margin-bottom:var(--sp-2);font-family:var(--font-mono);font-size:var(--fs-nano);letter-spacing:var(--ls-mono-wide);text-transform:uppercase;color:#C6C2BB}
	.axp-call__num{display:block;min-height:var(--tap);font-family:var(--font-mono);font-size:clamp(24px,7.5vw,44px);font-weight:700;line-height:1.2;letter-spacing:.01em;font-variant-numeric:tabular-nums;font-feature-settings:"tnum" 1;overflow-wrap:anywhere}
	.axp-linkcard__cta{margin-top:auto;padding-top:var(--sp-4)}
	/* The design system drops the sticky-bar reserve at 768px, but the THEME's
	   .aleac-mbar stays visible to 781px — so the bar would cover the bottom of
	   the page on an iPad in portrait. Re-reserve across that 14px band. */
	@media(min-width:768px) and (max-width:781px){.ax-root.ax-has-stickybar{padding-bottom:64px}}
	</style>

	<!-- ================================================== 1. HERO -->
	<section class="ax-hero ax-hero--short">
		<?php
		/* 'lounge' = the client seating area at ALEA's own experience centre, a
		   verified photograph. Taken by catalogue key so the path and the alt
		   both live in images.php — this page writes neither. It is not a
		   customer's home, not a delivered project and not the factory floor,
		   and nothing on this page says otherwise. */
		echo alea_img( 'lounge', array( 'class' => 'ax-hero__img', 'eager' => true ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- alea_img() escapes its own attributes.
		?>
		<div class="ax-hero__inner">
			<p class="ax-eyebrow">Contact ALEA</p>
			<h1 class="ax-hero__title">Talk to the factory.</h1>
			<?php /* Only what facts.php can back: ONE number, and it is ALEA's own.
			         Nothing is claimed about who picks up, where they sit, or how a
			         message is routed internally — none of that is a recorded fact. */ ?>
			<p class="ax-hero__sub">
				One number for calls and WhatsApp, and it reaches ALEA &mdash; the company
				that makes the kitchens. Call it, WhatsApp it, or leave your details and
				we will call you back.
			</p>
			<?php /* The number IS the primary control here: a full-width tap target
			         in mono, not a 12px line of footer text. */ ?>
			<a class="axp-call" href="<?php echo esc_url( $tel_href ); ?>">
				<span class="axp-call__label">Call us</span>
				<span class="axp-call__num"><?php echo esc_html( $phone_disp ); ?></span>
			</a>
			<div class="ax-hero__actions">
				<a class="ax-btn ax-btn--wa ax-btn--lg" href="<?php echo esc_url( $wa_href ); ?>">WhatsApp us</a>
				<a class="ax-btn ax-btn--ghost ax-btn--lg" href="#alea-book">Or send a message</a>
			</div>
			<?php /* Non-locational on purpose. .ax-hero__credit is the credit line
			         UNDER a photograph, and this photograph shows the client seating
			         area of the experience centre — printing "FACTORY AT ..." across
			         it would caption an experience-centre interior as the factory.
			         The factory location is stated as prose in section 5 instead,
			         where it belongs. */ ?>
			<p class="ax-hero__credit">
				CALL OR WHATSAPP
				/ SAME NUMBER FOR BOTH
				/ FREE, NO OBLIGATION
			</p>
		</div>
	</section>

	<!-- ================================================== 2. SPEC STRIP -->
	<section class="ax-section ax-section--flush" aria-label="How to reach ALEA">
		<div class="ax-wrap ax-wrap--flush">
			<div class="ax-specstrip">
				<div class="ax-specstrip__item">
					<span class="ax-specstrip__label">Call or WhatsApp</span>
					<span class="ax-specstrip__value"><?php echo esc_html( $phone_disp ); ?><span class="ax-specstrip__unit">one number</span></span>
				</div>
				<?php /* Place and qualifier are split OFF the single facts.php string,
				         never retyped — so correcting 'factory_place' there corrects this
				         cell too, and this page can never drift into "Panchkula city". */ ?>
				<div class="ax-specstrip__item">
					<span class="ax-specstrip__label">Our factory</span>
					<span class="ax-specstrip__value"><?php echo esc_html( $place_main ); ?><?php if ( '' !== $place_qual ) : ?><span class="ax-specstrip__unit"><?php echo esc_html( $place_qual ); ?></span><?php endif; ?></span>
				</div>
				<div class="ax-specstrip__item">
					<span class="ax-specstrip__label">We design &amp; install in</span>
					<span class="ax-specstrip__value"><?php echo (int) count( $areas ); ?><span class="ax-specstrip__unit">areas &mdash; named below</span></span>
				</div>
				<?php /* No SLA: "working hours" is what the business actually does, and
				         it is the only reply promise made anywhere on this page. The
				         hours themselves are NOT printed because they are not confirmed
				         — facts.php 'unverified.working_hours' is the TODO key holding
				         that gap open. Fill it there and this cell, step 2 and FAQ 1 can
				         all be made concrete from one place. The value carries the
				         promise VERBATIM as step 2 and FAQ 1 word it — "within working
				         hours" — so label + value + unit read as one sentence ("We call
				         you back within working hours, on your number") rather than the
				         bare fragment "Working hours". Do not swap in a duration such as
				         "same day": no response time is recorded anywhere, and inventing
				         one here would be an SLA the business has not agreed to. */ ?>
				<div class="ax-specstrip__item">
					<span class="ax-specstrip__label">We call you back</span>
					<span class="ax-specstrip__value">Within working hours<span class="ax-specstrip__unit">on your number</span></span>
				</div>
			</div>
		</div>
	</section>

	<!-- ================================================== 3. THE FORM -->
	<!-- BOTH anchors sit ON the form card, not on this section. .ax-grid--split
	     is one column below 1024px, so a section-level anchor would land the
	     visitor on ~400px of intro copy and they would still have to scroll to
	     reach the first input. id="alea-book" is on the card itself (this page's
	     hero button target) and id="estimate" is a marker INSIDE it for the
	     theme's site-wide sticky mobile bar (.aleac-mbar "Free Estimate" button
	     in functions.php) — one id per element, both landing on the fields.
	     The marker lives inside the card, never as a third grid child, so the
	     two-column desktop spread is untouched. -->
	<section class="ax-section">
		<div class="ax-wrap">
			<div class="ax-grid ax-grid--split">
				<div class="ax-reveal">
					<p class="ax-eyebrow">Send a message</p>
					<h2 class="ax-h2">Two lines from you, and we will call.</h2>
					<p class="ax-lead ax-mt-4">
						In the message box, please tell us two things: your area, and the time
						of day that suits you for a call. That is genuinely all we need to get
						back to you usefully rather than at random.
					</p>
					<div class="ax-spectable--rows ax-mt-5">
						<div class="ax-spectable__row">
							<span class="ax-spectable__key">Your area</span>
							<span class="ax-spectable__val"><?php echo esc_html( implode( ' / ', $areas ) ); ?></span>
						</div>
						<div class="ax-spectable__row">
							<span class="ax-spectable__key">Best time to call</span>
							<span class="ax-spectable__val">e.g. after 6pm, or Saturday morning</span>
						</div>
						<div class="ax-spectable__row">
							<span class="ax-spectable__key">Useful, not required</span>
							<span class="ax-spectable__val">Kitchen length, or what you need</span>
						</div>
					</div>
					<?php /* No claim about who answers: facts.php records the number and
					         nothing about call handling. It says only that it is the same
					         number printed above, which the page itself demonstrates. */ ?>
					<p class="ax-btn-note">
						Would rather not type? <a class="ax-link" href="<?php echo esc_url( $tel_href ); ?>">Call <?php echo esc_html( $phone_disp ); ?></a>
						&mdash; the same number as above.
					</p>
				</div>
				<div class="ax-form ax-form--card ax-reveal" id="alea-book">
					<span id="estimate" class="ax-sr-only"></span>
					<h3 class="ax-form__title">Leave your details</h3>
					<p class="ax-form__note">Free / no obligation / <?php echo esc_html( implode( ' · ', $areas ) ); ?></p>
					<?php echo do_shortcode( '[contact-form-7 id="7dcf010" title="Home Page form"]' ); ?>
					<p class="ax-form__fineprint ax-mt-4">
						We use your details only to call you back and arrange your visit or estimate.
					</p>
				</div>
			</div>
		</div>
	</section>

	<!-- ================================================== 4. WHAT HAPPENS NEXT -->
	<!-- Deliberately no response-time promise. "Within working hours" is what we
	     actually do; an SLA nobody has verified is not published here. -->
	<section class="ax-section ax-section--ruled">
		<div class="ax-wrap">
			<div class="ax-head ax-reveal">
				<p class="ax-eyebrow">After you contact us</p>
				<h2 class="ax-h2">Three steps, and none of them cost you anything.</h2>
			</div>
			<ol class="ax-steps ax-reveal">
				<?php /* "Reach ALEA" is all that is claimed: the phone and WhatsApp are
				         the one number in facts.php and the form is ALEA's own. How a
				         message is routed after that is not a fact we hold, so it is
				         not described. */ ?>
				<li class="ax-step">
					<div class="ax-step__body">
						<h3 class="ax-step__title">Your message reaches ALEA</h3>
						<p class="ax-step__text">
							Call, WhatsApp and the form all reach ALEA &mdash; the company that
							makes the kitchens. Use whichever is easiest for you.
						</p>
						<span class="ax-step__time">Call, WhatsApp or form</span>
					</div>
				</li>
				<li class="ax-step">
					<div class="ax-step__body">
						<h3 class="ax-step__title">We call you back</h3>
						<p class="ax-step__text">
							Someone rings the number you gave, within working hours, to understand what
							you need and answer the obvious questions — price, timing and what is included.
						</p>
						<span class="ax-step__time">Within working hours</span>
					</div>
				</li>
				<li class="ax-step">
					<div class="ax-step__body">
						<h3 class="ax-step__title">We fix a time, if you want one</h3>
						<p class="ax-step__text">
							A free measurement at your home, or a free visit to the factory at
							<?php echo esc_html( $f['factory_place'] ); ?> to watch kitchens being built.
							Both are free, neither obliges you to anything, and you can say no to both
							and still keep the answers.
						</p>
						<span class="ax-step__time">Free &middot; no obligation</span>
					</div>
				</li>
			</ol>
		</div>
	</section>

	<!-- ================================================== 5. WHERE WE ARE -->
	<!-- No map iframe (no third-party embeds on this site), no street address and
	     no opening hours: none of the three is verified, and the copy says so
	     plainly instead of inventing them. The location claim is facts.php
	     'factory_place' verbatim — the district, never "Panchkula city". -->
	<section class="ax-section ax-section--ruled ax-section--sheet">
		<div class="ax-wrap">
			<div class="ax-grid ax-grid--split">
				<div class="ax-head ax-mb-0 ax-reveal">
					<p class="ax-eyebrow">Where we are</p>
					<h2 class="ax-h2">Our factory is at <?php echo esc_html( $f['factory_place'] ); ?>.</h2>
					<p class="ax-lead ax-mt-4">
						That is where every ALEA kitchen is actually made &mdash;
						<?php echo esc_html( $f['factory_sqft'] ); ?> sq ft of manufacturing under
						our own roof, not a workshop we hire when an order comes in.
					</p>
					<p class="ax-prose ax-mt-5">
						We have not printed a walk-in address or opening hours on this page on
						purpose. We would rather fix a time with you than have you drive out and
						find the gate shut. Call or WhatsApp us and we will arrange it &mdash;
						including a factory visit, where you can watch kitchens being built
						before you pay anything.
					</p>
					<div class="ax-btnrow ax-mt-5">
						<a class="ax-btn ax-btn--ink" href="<?php echo esc_url( $tel_href ); ?>">Call <?php echo esc_html( $phone_disp ); ?></a>
						<a class="ax-btn ax-btn--wa" href="<?php echo esc_url( $wa_href ); ?>">WhatsApp us</a>
					</div>
				</div>
				<div class="ax-reveal">
					<div class="ax-spectable--rows">
						<div class="ax-spectable__row">
							<span class="ax-spectable__key">Factory</span>
							<span class="ax-spectable__val"><?php echo esc_html( $f['factory_place'] ); ?></span>
						</div>
						<div class="ax-spectable__row">
							<span class="ax-spectable__key">Manufacturing area</span>
							<span class="ax-spectable__val"><?php echo esc_html( $f['factory_sqft'] ); ?> sq ft</span>
						</div>
						<div class="ax-spectable__row">
							<span class="ax-spectable__key">Visits</span>
							<span class="ax-spectable__val">By arrangement &mdash; call or WhatsApp</span>
						</div>
						<div class="ax-spectable__row">
							<span class="ax-spectable__key">Phone &amp; WhatsApp</span>
							<span class="ax-spectable__val"><?php echo esc_html( $phone_disp ); ?></span>
						</div>
						<?php /* facts.php records 2009 as the year ALEA started, and nothing
						         about what was made in which year — so the row says exactly
						         that and no more. */ ?>
						<div class="ax-spectable__row">
							<span class="ax-spectable__key">ALEA since</span>
							<span class="ax-spectable__val"><?php echo esc_html( $f['alea_since'] ); ?></span>
						</div>
					</div>
					<p class="ax-spectable__note">
						No map embed on this page &mdash; ask us for directions on the call
						and we will send them to you on WhatsApp.
					</p>
				</div>
			</div>
		</div>
	</section>

	<!-- ================================================== 6. WHAT WE SERVE -->
	<!-- Exactly the four areas in facts.php 'service_area'. No sectors, no
	     landmarks, no drive times and no city-specific delivery promises. -->
	<section class="ax-section">
		<div class="ax-wrap">
			<div class="ax-head ax-reveal">
				<p class="ax-eyebrow">What we serve</p>
				<?php /* Still counted, never typed — the spec strip above counts the same
				         array, so adding or retiring an area in facts.php cannot leave a
				         number here contradicting the number there. Spelled out because
				         this is a heading: alea_num_word() holds the site-wide rule, so
				         the count and its spelling stay in step. */ ?>
				<h2 class="ax-h2"><?php echo esc_html( alea_num_word( count( $areas ), true ) ); ?> areas, named plainly.</h2>
				<p class="ax-lead">
					These are the areas we design and install in. If you are somewhere else,
					call and ask rather than guess &mdash; we will tell you straight whether
					we can take it on.
				</p>
			</div>
			<div class="ax-grid ax-grid--2 ax-grid--4 ax-grid--ruled ax-reveal">
				<?php foreach ( $areas as $i => $area ) : ?>
				<div>
					<p class="ax-mono--label">Area <?php echo esc_html( sprintf( '%02d', $i + 1 ) ); ?></p>
					<p class="ax-h3 ax-mt-2"><?php echo esc_html( $area ); ?></p>
				</div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<!-- ================================================== 7. QUICK LINKS -->
	<!-- Headed for where it actually sits. This band renders below the form, so
	     it is not "before you call" for anyone who has already sent a message —
	     it is what they can read while they wait for the call back. The links
	     stay after the conversion surface deliberately: nothing above the form
	     sends anyone away from it. -->
	<section class="ax-section ax-section--ruled">
		<div class="ax-wrap">
			<div class="ax-head ax-reveal">
				<p class="ax-eyebrow">While you wait for our call</p>
				<h2 class="ax-h2">Three things you can do without speaking to anyone.</h2>
				<p class="ax-lead">
					We publish the prices and the estimator in public, so you already know the
					numbers by the time we speak.
				</p>
			</div>
			<div class="ax-grid ax-grid--2 ax-grid--3">
				<?php foreach ( $quick_links as $link ) : ?>
				<article class="ax-card ax-reveal">
					<h3 class="ax-card__title"><?php echo esc_html( $link['label'] ); ?></h3>
					<p class="ax-card__body"><?php echo esc_html( $link['text'] ); ?></p>
					<p class="axp-linkcard__cta">
						<a class="ax-btn ax-btn--link ax-card__link" href="<?php echo esc_url( home_url( $link['url'] ) ); ?>">
							<?php echo esc_html( $link['cta'] ); ?>
						</a>
					</p>
				</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<!-- ================================================== 8. FAQ -->
	<section class="ax-section ax-section--ruled">
		<div class="ax-wrap ax-wrap--narrow">
			<div class="ax-head ax-reveal">
				<p class="ax-eyebrow">Questions</p>
				<h2 class="ax-h2">Asked before the first call.</h2>
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

	<!-- No second CTA band: this page IS the CTA. The form in section 3 is the
	     only conversion surface, and the hero's anchor button lands on the form
	     card itself, not on the copy above it. The sticky mobile bar is site-wide
	     (.aleac-mbar); its "Free Estimate" button lands on the #estimate marker
	     inside that same card, and .ax-has-stickybar on .ax-root reserves the
	     space it occupies — re-reserved 768–781px in the page CSS above, because
	     the design system drops the reserve at 768px while the theme's bar stays
	     visible to 781px. -->

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
