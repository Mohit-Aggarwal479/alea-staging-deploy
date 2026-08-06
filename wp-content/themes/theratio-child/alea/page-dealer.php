<?php
/**
 * ALEA — Become a dealer ("The Manufacturer's Record")
 * Replaces /become-a-dealer/. Included inside <main class="alea-main"> by the
 * shell; header, footer and the site-wide sticky mobile bar come from the theme.
 *
 * SINGLE-URL template: it serves one path only, so it reads no variant from
 * $GLOBALS['alea_page_args']. (Multi-URL siblings — page-collection.php —
 * whitelist their variant there.)
 *
 * - ROUTING: alea_redesign_map() in functions.php carries the
 *   '/become-a-dealer/' entry that points at this template. Without it
 *   alea_redesign_entry() returns null, the shell never includes this file, the
 *   wp_head design-system.css inliner never fires, and the old page keeps
 *   serving — so the entry and this file are edited together or not at all.
 *
 * HONESTY NOTES — this is the page most likely to invent a business:
 * - THERE IS NO VERIFIED DEALER PROGRAMME. facts.php holds no margin, no
 *   territory rule, no investment figure, no dealer count, no support package,
 *   no application deadline and no onboarding timeline. None of those appear
 *   here, in any form, including as a range or an "up to". Where a reader would
 *   expect one, this page states the ABSENCE in plain words and puts the
 *   question on the call. That is the whole design of the page.
 * - What IS legitimate, and all of it comes from facts.php: ALEA manufactures in
 *   its own 95,000 sq ft factory at Raipur Rani (Panchkula district), has made
 *   furniture since 1998 and kitchens since 2009, fits Hettich and Blum as
 *   standard, publishes per-sq-ft rates, and backs product with a 10-year
 *   written warranty. A dealer would be selling factory-made product with a
 *   real warranty behind it — that is the offer, and it is the only offer made.
 * - HARDWARE: Hettich and Blum are BOTH standard. Neither is framed as an
 *   upgrade and no price difference between them is implied.
 * - WARRANTY is "10 years, in writing" and nothing more specific. What it covers
 *   lives in the written document the customer receives. No coverage table.
 * - SERVICE AREA: only the four cities in facts.php are named, and they are
 *   named as where ALEA ITSELF installs — never as a dealer territory, a
 *   protected zone, or a list of places a dealer may or may not sell. The site's
 *   legacy city pages (Jaipur, Delhi, Ludhiana...) are neither linked nor
 *   implied, because those service claims are not verified.
 * - NO whole-kitchen rupee totals are computed on this page, so the
 *   sq-ft-per-running-foot assumption does not arise; only the published
 *   per-sq-ft bands from alea_price_band() are shown, with their unit.
 * - Installation-day figures are deliberately NOT printed here. The 15-day
 *   figure in facts.php is a claim about installation at a CUSTOMER'S home; on a
 *   B2B page it would read as a supply lead time to a dealer, which nobody has
 *   verified. Omitted rather than reframed.
 * - Photography: every picture comes from images.php and its alt travels with
 *   the file. Nothing is captioned as a delivered project, a client home, a
 *   dealer showroom or a factory floor. The library holds NO wardrobe
 *   photography, so the wardrobe copy is a statement about what we make and
 *   never about a frame on screen.
 * - COMPETITORS ARE NEVER CHARACTERISED. Every advantage claimed below is
 *   first-person and checkable by walking into the building. facts.php holds no
 *   data about any other company, so this page makes no claim — not even a
 *   throwaway one — about what other suppliers do, publish or withhold.
 * - THE ENQUIRY FORM IS THE SHARED SITE-WIDE ONE (CF7 7dcf010). Its fields are
 *   fixed and are listed in alea/cf7.php: name, phone, email, a REQUIRED
 *   "Planning within" select written for homeowners, and a message box. This
 *   page must describe it as it actually is, and must not claim the enquiry is
 *   routed to any particular person: no routing and no roles are recorded
 *   anywhere, so none are promised.
 */

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/facts.php';
require_once __DIR__ . '/images.php';
/* Labels + a non-submittable select prompt for the shared lead form. Loaded
   site-wide from functions.php; required here too so this template is never
   the page that ships the form unrepaired. */
require_once __DIR__ . '/cf7.php';

$f = alea_facts();

$sqft         = alea_fact( 'factory_sqft' );             // '95,000'
$place        = alea_fact( 'factory_place' );            // 'Raipur Rani, Panchkula district'
$since_alea   = alea_fact( 'alea_since' );               // '2009'
$since_parent = alea_fact( 'founded_furniture' );        // '1998'
$warranty_yrs = (int) alea_fact( 'warranty_years', 0 );  // 10
$price_unit   = (string) alea_fact( 'price_unit' );      // 'per sq ft'
$phone_disp   = alea_fact( 'phone_display' );
$tel_href     = 'tel:' . alea_fact( 'phone_tel' );
$wa_href      = alea_wa_link( 'Hi ALEA, I am enquiring about becoming a dealer.' );

$brands       = implode( ' and ', alea_fact( 'hardware_brands', array() ) );
$areas        = implode( ', ', alea_fact( 'service_area', array() ) );

$kitchens_url = home_url( '/modular-kitchen/' );
$wardrobe_url = home_url( '/wardrobe/' );
$price_url    = home_url( '/modular-kitchen-price/' );
$factory_url  = home_url( '/our-factory/' );
$warranty_url = home_url( '/warranty/' );
$projects_url = home_url( '/projects/' );
$about_url    = home_url( '/about/' );

/* The published bands, read through alea_price_band() so this page can never
   quote a rate the pricing page has moved on from. These are the rates we
   publish to CUSTOMERS; nothing here is presented as a dealer rate, because no
   dealer rate is recorded anywhere and inventing one would be a fabrication. */
$band_lines = array();
foreach ( $f['collections'] as $band_slug => $band_col ) {
	$band_lines[] = array(
		'name' => $band_col['name'],
		'band' => alea_price_band( $band_slug ),
	);
}

/* What happens after the enquiry. Three steps, no .ax-step__time on any of them:
   a timing caption here would be an invented service-level promise. Step three
   states the absence of published terms rather than filling it with a number. */
$steps = array(
	array(
		'title' => 'You send the note below',
		'text'  => 'Your business name, the city you trade in, what you sell today, and a time of day that suits you for a call. That is enough to start; there is no application pack to download and no form fee.',
	),
	array(
		'title' => 'We call you back to talk',
		'text'  => 'A conversation, not an assessment. What you sell now, where you sell it, and what you would want a manufacturer to do for you — and you get to ask us the same kind of questions back.',
	),
	array(
		'title' => 'If it fits both sides, we discuss terms directly',
		'text'  => 'We publish no margin schedule, no territory map and no investment figure, because none of that is decided in advance. It is worked out in that conversation, not on a web page.',
	),
);

/* FAQ — the visible answers and the JSON-LD render from this ONE array, so the
   schema can never drift from the text on the page. Every answer here either
   states a fact from facts.php or states plainly that the thing being asked
   about is not published. Neither kind is invented. */
$faq = array(
	array(
		'q' => 'Which territories are available?',
		'a' => sprintf(
			'We do not publish a territory map, and we are not going to invent one on a web page. Territory is discussed case by case, on the call, once we know where you trade and what you already sell. What we can tell you now is where ALEA installs directly today: %1$s. That is a statement about our own installation coverage, not a boundary drawn around you — how your area would work alongside ours is exactly the sort of thing the conversation is for.',
			$areas
		),
	),
	array(
		'q' => 'What support would I get?',
		'a' => sprintf(
			/* Four facts in one 63-word sentence read as a wall. Same four facts,
			   one sentence each — the answer is scanned, not studied. */
			'The honest answer is that we publish no support package, so anything specific — display units, training, marketing, credit — is settled on the call rather than promised here. What is already true, and checkable before you ever speak to us, is what sits behind the product. It is made in our own %1$s sq ft factory at %2$s, not bought in. %3$s hardware is fitted as standard. Our per-sq-ft rates are published on this site. Every kitchen carries a %4$d-year warranty in writing. You would be selling factory-made product with a real document behind it.',
			$sqft,
			$place,
			$brands,
			$warranty_yrs
		),
	),
	array(
		'q' => 'How do I start?',
		'a' => sprintf(
			'Send the enquiry form on this page and put four things in its message box: your business name, your city, your current line of business and a good time to call. The form is the one the whole site uses, so it also asks how soon you are planning. That question is written for homeowners — pick the nearest option and put your real position in the message box. The call and WhatsApp buttons on this page do the same job if that is quicker. You are also welcome to come to %1$s and see the factory first; that visit is free and carries no obligation, and it is the fastest way to judge whether any of this is worth your time.',
			$place
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
	/* Page-specific: the hero's secondary text link and the plain-statement
	   call-out used wherever this page has to say "we do not publish that". */
	.axp-hero-alt{margin-top:var(--sp-3);font-size:15px;color:#E4E1DC}
	.axp-hero-alt a{color:inherit}
	.axp-plain{margin-top:var(--sp-6);padding-left:var(--sp-4);border-left:2px solid var(--ax-fg);max-width:62ch}
	.axp-plain__label{display:block;margin-bottom:var(--sp-2);font-family:var(--font-mono);font-size:var(--fs-nano);letter-spacing:var(--ls-mono-wide);text-transform:uppercase;color:var(--ax-fg-muted)}
	/* Spec-strip cell holding two brand names: wrap at the spaces, never mid-word. */
	.axp-nobreak{overflow-wrap:normal;word-break:normal}
</style>

	<!-- ================================================== 1. HERO -->
	<section class="ax-hero ax-hero--short">
		<?php /* 'kitchen-wide' — the only landscape photograph verified as ALEA's
		         own. Alt comes from images.php, so it claims an experience-centre
		         display and never a factory floor or a dealer showroom. */ ?>
		<?php echo alea_img( 'kitchen-wide', array( 'class' => 'ax-hero__img', 'eager' => true ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built and escaped by alea_img(). ?>
		<div class="ax-hero__inner">
			<p class="ax-eyebrow">Dealer enquiries</p>
			<h1 class="ax-hero__title">Sell kitchens made in our own factory.</h1>
			<p class="ax-hero__sub">
				ALEA manufactures modular kitchens and wardrobes at <?php echo esc_html( $place ); ?> &mdash;
				<?php echo esc_html( $sqft ); ?> sq ft of our own floor, <?php echo esc_html( $brands ); ?> hardware
				as standard, and a <?php echo (int) $warranty_yrs; ?>-year written warranty behind the product you would
				be putting your name to.
			</p>
			<div class="ax-hero__actions">
				<a class="ax-btn ax-btn--primary ax-btn--lg" href="#alea-book">Start the conversation</a>
			</div>
			<p class="axp-hero-alt">or <a href="<?php echo esc_url( $factory_url ); ?>">come and see the factory</a> &mdash; free, no obligation</p>
			<p class="ax-hero__credit">
				OWN <?php echo esc_html( $sqft ); ?> SQ FT FACTORY
				/ <?php echo esc_html( strtoupper( $brands ) ); ?> AS STANDARD
				/ <?php echo (int) $warranty_yrs; ?>-YEAR WRITTEN WARRANTY
			</p>
		</div>
	</section>

	<!-- ================================================== 2. SPEC STRIP -->
	<!-- Four cells, four facts, all of them from facts.php. No dealer count, no
	     margin, no investment figure: none of those exist in the record. -->
	<section class="ax-section ax-section--flush" aria-label="ALEA manufacturing facts">
		<div class="ax-wrap ax-wrap--flush">
			<div class="ax-specstrip">
				<div class="ax-specstrip__item">
					<span class="ax-specstrip__label">Own factory</span>
					<span class="ax-specstrip__value"><?php echo esc_html( $sqft ); ?><span class="ax-specstrip__unit">sq ft</span></span>
				</div>
				<div class="ax-specstrip__item">
					<span class="ax-specstrip__label">Kitchens since</span>
					<span class="ax-specstrip__value"><?php echo esc_html( $since_alea ); ?></span>
				</div>
				<div class="ax-specstrip__item">
					<span class="ax-specstrip__label">Hardware</span>
					<?php /* .axp-nobreak turns off the strip's overflow-wrap:anywhere for
					         this one cell: at 375px the two-column strip is ~155px wide and
					         "Hettich and Blum" would otherwise break mid-word. It wraps at
					         the spaces instead, and both brands stay in the value line so
					         neither is demoted to the muted unit caption. */ ?>
					<span class="ax-specstrip__value axp-nobreak"><?php echo esc_html( $brands ); ?><span class="ax-specstrip__unit">both standard</span></span>
				</div>
				<div class="ax-specstrip__item">
					<span class="ax-specstrip__label">Warranty</span>
					<span class="ax-specstrip__value"><?php echo (int) $warranty_yrs; ?><span class="ax-specstrip__unit">years, in writing</span></span>
				</div>
			</div>
		</div>
	</section>

	<!-- ================================================== 3. WHY A MANUFACTURER -->
	<!-- General trade guidance about makers vs resellers. It asserts NOTHING
	     about any other company — no counts, no practices, no comparison of
	     terms — because facts.php holds no competitor data and a reader cannot
	     check a claim about somebody else's supply chain. Every line attached to
	     ALEA below is first-person and settled by walking into the building. -->
	<section class="ax-section">
		<div class="ax-wrap ax-wrap--narrow">
			<header class="ax-head ax-head--wide ax-reveal">
				<p class="ax-eyebrow">Why a manufacturer</p>
				<h2 class="ax-h2">Buy from the line, not from the middle of the chain.</h2>
				<p class="ax-lead">
					A dealer&rsquo;s exposure is whatever sits behind the product when something goes wrong two years
					later. These four are what sits behind ours, and all four are checkable before you commit to anything.
				</p>
			</header>
			<ul class="ax-prooflist ax-reveal">
				<li class="ax-proof">
					<span class="ax-proof__tick" aria-hidden="true"></span>
					<div class="ax-proof__text">
						Direct from the line &mdash; cut, edged, assembled and finished in our own
						<?php echo esc_html( $sqft ); ?> sq ft factory at <?php echo esc_html( $place ); ?>. The company
						you would be buying from is the company that builds it and answers for it afterwards.
						<span class="ax-proof__never">Never outsourced</span>
					</div>
				</li>
				<li class="ax-proof">
					<span class="ax-proof__tick" aria-hidden="true"></span>
					<div class="ax-proof__text">
						<?php /* facts.php lists both brands as standard. No upgrade framing and
						         no price difference between them is claimed: neither is verified. */ ?>
						Named hardware &mdash; <?php echo esc_html( $brands ); ?>, both fitted as standard, specified
						line by line rather than described as &ldquo;imported fittings&rdquo;. Your customer can read the
						brand on the quotation.
						<span class="ax-proof__never">Never generic hardware</span>
					</div>
				</li>
				<li class="ax-proof">
					<span class="ax-proof__tick" aria-hidden="true"></span>
					<div class="ax-proof__text">
						A <?php echo (int) $warranty_yrs; ?>-year warranty, in writing &mdash; handed over as a document
						with the kitchen. What it covers is set out in that written document; ask to read one on your
						visit, before any of this becomes an agreement.
						<span class="ax-proof__never">Never a verbal promise</span>
					</div>
				</li>
				<li class="ax-proof">
					<span class="ax-proof__tick" aria-hidden="true"></span>
					<div class="ax-proof__text">
						<?php /* First person only. No claim about what any other supplier
						         publishes or withholds: facts.php holds no competitor data,
						         and a reader cannot check a claim about somebody else. */ ?>
						Published pricing &mdash; our rates are printed openly on this site, <?php echo esc_html( $price_unit ); ?>
						of cabinetry, so your customer can check the number before they walk in and you are quoting from
						a rate the manufacturer already publishes.
						<span class="ax-proof__never">Never a rate on request</span>
					</div>
				</li>
			</ul>
			<?php /* The page states the absence rather than filling it. This paragraph
			         is the reason the rest of the page can be trusted. */ ?>
			<div class="axp-plain ax-reveal">
				<span class="axp-plain__label">What this page does not say</span>
				<p class="ax-prose">
					No margins, no territory rules, no investment figure, no count of dealers already appointed. We are
					not publishing numbers we have not settled. Those are exactly the questions to put to us directly
					&mdash; the enquiry below reaches us, and we would rather answer them on a call than guess at them here.
				</p>
			</div>
		</div>
	</section>

	<!-- ================================================== 4. WHAT WE MAKE -->
	<section class="ax-section ax-section--ruled ax-section--sheet">
		<div class="ax-wrap">
			<div class="ax-grid ax-grid--split">
				<div class="ax-reveal">
					<div class="ax-head ax-mb-0">
						<p class="ax-eyebrow">What we make</p>
						<h2 class="ax-h2">Modular kitchens and wardrobes, on one line.</h2>
						<p class="ax-lead ax-mt-4">
							Kitchens are sold in three collections, each with its rate published
							<?php echo esc_html( $price_unit ); ?> of cabinetry. Wardrobes are made on the same line and
							quoted after a measurement rather than from a published rate &mdash; we are not willing to print
							a wardrobe rate until we can stand behind it.
						</p>
					</div>
					<div class="ax-spectable--rows ax-mt-6">
						<?php foreach ( $band_lines as $bl ) : ?>
						<div class="ax-spectable__row">
							<span class="ax-spectable__key"><?php echo esc_html( $bl['name'] ); ?></span>
							<span class="ax-spectable__val"><?php echo esc_html( $bl['band'] ); ?></span>
						</div>
						<?php endforeach; ?>
					</div>
					<p class="ax-spectable__note">
						The rates we publish to customers &mdash; cabinetry only, countertop, chimney, hob, sink and
						appliances itemised separately. What a dealer pays is not published here.
					</p>
					<div class="ax-btnrow ax-mt-5">
						<a class="ax-btn ax-btn--ghost" href="<?php echo esc_url( $kitchens_url ); ?>">Modular kitchens</a>
						<a class="ax-btn ax-btn--ghost" href="<?php echo esc_url( $wardrobe_url ); ?>">Wardrobes</a>
					</div>
					<?php /* These four are real navigation, so they sit in prose at body
					         size rather than in .ax-btn-note — an 11px uppercase mono
					         caption is far too small a target for a link, and that slot
					         holds one short caption at most. */ ?>
					<div class="ax-prose ax-mt-5">
						<p>
							See also the <a href="<?php echo esc_url( $price_url ); ?>">published price list</a>,
							the <a href="<?php echo esc_url( $warranty_url ); ?>">warranty</a>,
							<a href="<?php echo esc_url( $projects_url ); ?>">our work</a> and
							<a href="<?php echo esc_url( $about_url ); ?>">the company record</a>.
						</p>
					</div>
				</div>
				<?php /* Experience-centre display, captioned as exactly that. The library
				         holds no wardrobe photograph and no factory-floor photograph, so
				         neither is shown or implied. Alt comes from images.php. */ ?>
				<figure class="ax-media ax-media--43 ax-reveal">
					<span class="ax-media__frame">
						<?php echo alea_img( 'kitchen-island' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built and escaped by alea_img(). ?>
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

	<!-- ================================================== 5. WHAT HAPPENS NEXT -->
	<!-- No .ax-step__time on any step: a timing caption here would be a
	     service-level promise nobody has verified. -->
	<section class="ax-section ax-section--ruled">
		<div class="ax-wrap ax-wrap--narrow">
			<header class="ax-head ax-reveal">
				<p class="ax-eyebrow">What happens next</p>
				<h2 class="ax-h2">Three steps, and none of them is a form to fill in twice.</h2>
				<p class="ax-lead">
					We are not going to promise you a date on a web page. What we can describe honestly is the shape
					of it &mdash; you write, we call, and the terms get discussed by people rather than by a brochure.
				</p>
			</header>
			<div class="ax-steps ax-reveal">
				<?php foreach ( $steps as $st ) : ?>
				<div class="ax-step">
					<div class="ax-step__body">
						<h3 class="ax-step__title"><?php echo esc_html( $st['title'] ); ?></h3>
						<p class="ax-step__text"><?php echo esc_html( $st['text'] ); ?></p>
					</div>
				</div>
				<?php endforeach; ?>
			</div>
			<?php /* The number is a real tel: link, not caption text: a printed number
			         a phone cannot dial is worse than no number at all. */ ?>
			<div class="ax-btnrow ax-mt-5">
				<a class="ax-btn ax-btn--ink" href="<?php echo esc_url( $tel_href ); ?>">Call <?php echo esc_html( $phone_disp ); ?></a>
				<a class="ax-btn ax-btn--ghost" href="<?php echo esc_url( $factory_url ); ?>">See the factory</a>
			</div>
			<p class="ax-btn-note">
				The quickest version of all three &mdash; talk to us, or come to
				<?php echo esc_html( $place ); ?> before you decide anything.
			</p>
		</div>
	</section>

	<!-- ================================================== 6. FAQ -->
	<section class="ax-section ax-section--ruled">
		<div class="ax-wrap ax-wrap--narrow">
			<header class="ax-head ax-reveal">
				<p class="ax-eyebrow">Questions</p>
				<?php /* Sibling convention: name the subject, claim no volume. Nothing
				         here implies a body of existing dealer enquiries. */ ?>
				<h2 class="ax-h2">Asked about becoming a dealer.</h2>
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

	<!-- ================================================== 7. ENQUIRY -->
	<!-- id="alea-book": the inline form section every redesigned page ends on.
	     The CF7 fields are the shared site-wide set (see alea/cf7.php): name,
	     phone, email, a REQUIRED "Planning within" select written for homeowners,
	     and a message box. What a trade enquiry needs beyond that is given as
	     guidance beside the form rather than as invented extra fields — and the
	     copy says what the form actually asks, including the select. -->
	<span id="estimate" class="ax-sr-only"></span>
	<section class="ax-section ax-section--ruled" id="alea-book">
		<div class="ax-wrap">
			<div class="ax-grid ax-grid--split">
				<div class="ax-reveal">
					<p class="ax-eyebrow">Dealer enquiry</p>
					<h2 class="ax-h2">What we&rsquo;d like to know about you.</h2>
					<p class="ax-lead ax-mt-4">
						Four things, and the message box takes all of them. It is a trade enquiry, not an application:
						nothing you write here commits either of us to anything. The form is the one the whole site
						uses, so it also asks how soon you are planning &mdash; a homeowner&rsquo;s question. Pick the
						nearest option and put your real position in the message.
					</p>
					<div class="ax-prose ax-mt-5">
						<ul>
							<li><b>Your business name</b> &mdash; and whether you trade under it already.</li>
							<li><b>Your city</b> &mdash; where you actually sell, so the conversation starts in the right place.</li>
							<li><b>Your current line of business</b> &mdash; interiors, furniture, hardware, construction, or starting fresh. All of those are worth a call.</li>
							<li><b>A good time to call</b> &mdash; so we reach you when you can talk properly rather than between two meetings.</li>
						</ul>
					</div>
					<div class="ax-btnrow ax-mt-5">
						<a class="ax-btn ax-btn--ink" href="<?php echo esc_url( $tel_href ); ?>">Call <?php echo esc_html( $phone_disp ); ?></a>
						<a class="ax-btn ax-btn--wa" href="<?php echo esc_url( $wa_href ); ?>">WhatsApp us</a>
					</div>
					<p class="ax-btn-note">
						Rather see the product first? The factory at <?php echo esc_html( $place ); ?> is a free visit,
						and ALEA has been making kitchens since <?php echo esc_html( $since_alea ); ?> &mdash; out of a
						furniture business that has been making furniture since <?php echo esc_html( $since_parent ); ?>.
					</p>
				</div>
				<div class="ax-form ax-form--card ax-reveal">
					<h3 class="ax-form__title">Send a dealer enquiry</h3>
					<p class="ax-form__note">Trade enquiry / no obligation</p>
					<?php /* The form fields are fixed, so this line tells the reader what to
					         put in the message box rather than pretending fields exist —
					         and names the one field a trade enquirer will not expect. */ ?>
					<p class="ax-help ax-mb-4">
						Please put your business name, city, current line of business and a good time to call in
						&ldquo;What you need&rdquo;. &ldquo;Planning within&rdquo; is asked of every visitor and is
						required &mdash; choose whichever timing is closest.
					</p>
					<?php echo do_shortcode( '[contact-form-7 id="7dcf010" title="Home Page form"]' ); ?>
					<p class="ax-form__fineprint ax-mt-4">
						We use your details only to reply to your enquiry.
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
