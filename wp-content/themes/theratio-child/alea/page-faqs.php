<?php
/**
 * ALEA — Frequently asked questions ("The Manufacturer's Record")
 * Replaces /faqs/. Included inside <main class="alea-main"> by the shell;
 * header, footer and the site-wide sticky mobile bar come from the theme.
 *
 * SINGLE-URL template: it serves one path only, so it reads no variant from
 * $GLOBALS['alea_page_args']. (Multi-URL siblings — page-collection.php,
 * page-layout.php — whitelist their variant there.)
 *
 * - ROUTING (outside this file): alea_redesign_map() in functions.php must
 *   carry a '/faqs/' entry pointing at this template. Routing lives there, not
 *   here — without that entry alea_redesign_entry() returns null, the shell
 *   never includes this file, the wp_head design-system.css inliner never fires
 *   (it early-returns on an empty $GLOBALS['alea_page_file']), and the old
 *   /faqs/ page keeps serving. This file ships dead and unstyled until the map
 *   entry exists.
 *
 * WHAT THIS PAGE IS: the answer hub. Every other redesigned page carries a
 * short FAQ block about itself; this one collects the questions that are asked
 * before every kitchen, in five groups, and answers them from the same numbers
 * the rest of the site is built on.
 *
 * HONESTY NOTES — an FAQ page is the easiest place on a site to invent a fact,
 * because a question invites an answer even when there is not one to give:
 * - EVERY business number is read from facts.php or computed from it. The
 *   whole-kitchen figure is produced by alea_kitchen_total() and its sq-ft-per-
 *   running-foot assumption is stated in the visible answer text, exactly where
 *   the number is.
 * - NO DELIVERY SLA IS PUBLISHED for the design stage or for manufacturing.
 *   facts.php records exactly one duration — 'install_days' — so that is the
 *   only one printed. The design and manufacturing answers say plainly that we
 *   do not publish a number and point the reader at dates in writing, which is
 *   a better answer than a figure nobody could be held to.
 * - THE INSTALLATION SENTENCE IS NOT WRITTEN HERE. It comes from
 *   alea_install_sentence() in facts.php, which hedges the figure ("about")
 *   because 'install_days' is recorded as a bare number with no anchor. The
 *   same question is answered on other pages that also emit FAQPage schema, so
 *   the sentence has one home; change it there, not here. It is the ON-SITE
 *   window only and never a total lead time, which this site does not publish.
 * - NO WARRANTY COVERAGE TABLE. The warranty is "10 years, in writing" and the
 *   terms live "in the written document you receive". The answer says why a
 *   web-page summary is deliberately not offered in its place.
 * - NO PAYMENT SCHEDULE. facts.php holds no payment terms, so the payment
 *   question is answered by declining to print one rather than by inventing a
 *   milestone split.
 * - HARDWARE: Hettich and Blum are BOTH standard, in every collection. Neither
 *   is framed as an upgrade and no price difference between them is implied,
 *   because none is verified.
 * - NO MATERIAL SPECIFICATION is claimed (no board grade, thickness, core or
 *   finish spec), because facts.php records none. The collections are described
 *   in general terms, which is also the truthful thing to say about a finish.
 * - CLEANING AND CARE is written as general industry guidance and labelled as
 *   such in the visible text — never as an ALEA-specific measured instruction.
 * - SERVICE AREA is the four cities in facts.php and nothing else. The four
 *   location links below are built from that same array, so a city cannot be
 *   linked here as served unless facts.php says it is.
 * - Photography comes from images.php, which was built by VIEWING each file.
 *   No alt text is written here and no image is captioned as a delivered
 *   project, a client home or a factory floor.
 * - The visible answers and the FAQPage JSON-LD are rendered from ONE array, so
 *   the schema can never drift from the text on the page.
 * - A MISSING FACT DEGRADES TO SILENCE, never to a zero. Clauses that name a
 *   duration are built guarded above; an answer whose whole point is a number
 *   carries an 'if' key and is pruned before the count, the accordions and the
 *   schema are built, so the page can lose a sentence but cannot assert "0".
 */

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/facts.php';
require_once __DIR__ . '/images.php';

$f = alea_facts();

/* No value is repeated in a comment here: a second copy of a business number
   that no lint reconciles against facts.php is exactly how the two drift. The
   keys are self-describing and facts.php is one file away. */
$warranty_yrs = (int) alea_fact( 'warranty_years', 0 );
$install_days = (int) alea_fact( 'install_days', 0 );
$sqft_rft     = (int) alea_fact( 'sqft_per_rft', 0 );
$sqft         = alea_fact( 'factory_sqft' );
$place        = alea_fact( 'factory_place' );
$phone_disp   = alea_fact( 'phone_display' );
$tel_href     = 'tel:' . alea_fact( 'phone_tel' );
$wa_href      = alea_wa_link( "Hi ALEA, I have a question about a modular kitchen." );
$price_unit   = (string) alea_fact( 'price_unit' );

$areas_arr    = alea_fact( 'service_area', array() );
$areas        = implode( ', ', $areas_arr );
$brands       = implode( ' and ', alea_fact( 'hardware_brands', array() ) );

$calc_url     = home_url( '/kitchen-cost-calculator/' );
$visit_url    = home_url( '/book-a-free-design-visit/' );
$price_url    = home_url( '/modular-kitchen-price/' );
$factory_url  = home_url( '/our-factory/' );
$warranty_url = home_url( '/warranty/' );
$kitchens_url = home_url( '/modular-kitchen/' );
$wardrobe_url = home_url( '/wardrobe/' );
$projects_url = home_url( '/projects/' );
$about_url    = home_url( '/about/' );
$contact_url  = home_url( '/contact/' );

/* Published band across the whole range, derived from EVERY collection in
   facts.php — never from two hard-keyed slugs — so adding or retiring a
   collection there cannot leave this page quoting a stale range. */
$band_low  = 0;
$band_high = 0;
foreach ( $f['collections'] as $band_col ) {
	$band_low  = $band_low ? min( $band_low, (int) $band_col['from'] ) : (int) $band_col['from'];
	$band_high = max( $band_high, (int) $band_col['to'] );
}

/* Every band label comes from alea_price_band(), which composes the unit from
   facts.php, so the hero, the strip and the answers cannot disagree.
   'character' is the descriptor facts.php ships for exactly the "how do the
   collections differ" question — the same field page-kitchens.php renders on
   each collection card. It answers that question; a price band does not. */
$band_lines = array();
$char_lines = array();
foreach ( $f['collections'] as $band_slug => $band_col ) {
	$band_lines[] = $band_col['name'] . ' ' . alea_price_band( $band_slug );
	if ( ! empty( $band_col['character'] ) ) {
		$char_lines[] = $band_col['name'] . ' — ' . $band_col['character'];
	}
}
$bands_txt = implode( ', ', $band_lines );
/* Trailing space carried here, not in the sentence, so an empty catalogue
   leaves no double space behind. */
$chars_txt = $char_lines ? implode( ' ', $char_lines ) . ' ' : '';

/* One worked whole-kitchen estimate, computed via alea_kitchen_total(). The
   sq-ft-per-running-foot assumption is stated in the answer text beside it.
   The example collection falls back to the first one defined, so retiring
   'signature' in facts.php degrades instead of breaking. */
$ex_rft  = 12;
$ex_slug = isset( $f['collections']['signature'] ) ? 'signature' : (string) key( $f['collections'] );
$ex_name = isset( $f['collections'][ $ex_slug ]['name'] ) ? (string) $f['collections'][ $ex_slug ]['name'] : '';
list( $ex_low, $ex_high, $ex_sqft ) = alea_kitchen_total( $ex_rft, $ex_slug );

/* ---------------------------------------------------------------------------
   DEGRADE TO SILENCE, NOT TO A FALSE NUMBER. If a duration key were ever
   removed from facts.php, alea_fact() returns the 0 default above — and a
   sentence reading "about 0 days" or a spec cell reading "0 years, in writing"
   is a worse failure than the missing sentence. So every clause that names one
   of those numbers is built here, guarded, and collapses to an empty string
   (or drops its whole answer, via the 'if' key below) when the fact is absent.
   --------------------------------------------------------------------------- */

/* The worked whole-kitchen sum, with its sq-ft-per-running-foot assumption
   stated in the same sentence as the number it produced. Printed only when
   every part of the arithmetic actually exists. */
$whole_kitchen_txt = '';
if ( $sqft_rft > 0 && $ex_sqft > 0 && $ex_low > 0 && $ex_name ) {
	$whole_kitchen_txt = sprintf(
		' To turn that into a whole-kitchen figure we assume standard base + wall units — about %1$d sq ft of cabinetry per running foot — so a %2$d-running-foot kitchen is about %3$d sq ft of cabinetry, roughly ₹%4$s–%5$s in the %6$s band. That is illustrative arithmetic rather than a quotation.',
		$sqft_rft,
		$ex_rft,
		$ex_sqft,
		alea_inr( $ex_low ),
		alea_inr( $ex_high ),
		$ex_name
	);
}

/* Warranty phrasings. Without the year count the warranty is still written —
   'warranty_written' is its own fact — so the clause loses the number rather
   than the promise, and never prints "0 years". */
$warranty_incl = $warranty_yrs > 0 ? sprintf( ', and a %d-year warranty in writing', $warranty_yrs ) : '';
$warranty_name = $warranty_yrs > 0 ? sprintf( '%d-year written warranty', $warranty_yrs ) : 'written warranty';
$warranty_doc  = $warranty_yrs > 0 ? sprintf( 'a %d-year warranty', $warranty_yrs ) : 'the warranty';

/* ---------------------------------------------------------------------------
   THE ANSWER SET. Five groups. The accordions and the FAQPage JSON-LD are both
   rendered from this array — there is no second copy of any answer anywhere in
   this file, so the schema text and the visible text are the same string.
   --------------------------------------------------------------------------- */
$groups = array(

	/* ---------------- 1. PRICE & PAYMENT ---------------- */
	array(
		'id'      => 'price',
		'title'   => 'Price and payment',
		'lead'    => 'Our rates are published on the site rather than held behind a phone call. Here is what they mean, what they include, and what we will not print.',
		'items'   => array(
			array(
				'q' => 'What does an ALEA modular kitchen cost?',
				'a' => sprintf(
					'Our rates are published per square foot of cabinetry: %1$s.%2$s The estimator does the same sum for your own kitchen, free and with no sign-up.',
					$bands_txt,
					$whole_kitchen_txt
				),
			),
			array(
				'q' => 'Why is the price a range instead of one number?',
				'a' => 'Because no two kitchens are the same length or the same specification. Where your kitchen lands inside its band depends on the finishes, the fittings and the layout the room will actually take — and the only honest way to turn a range into a number is to measure. The measurement is free and carries no obligation, and what comes back is an itemised quotation.',
			),
			array(
				'q' => 'What is included in the per-square-foot rate?',
				'a' => sprintf(
					'Cabinet carcasses and shutters built in our own factory, machine edge banding on every panel, %1$s soft-close hinges and runners, delivery and installation by our own team across %2$s%3$s. Those are the items the published rate prices.',
					$brands,
					$areas,
					$warranty_incl
				),
			),
			array(
				'q' => 'What is not included in the rate?',
				'a' => 'The countertop, the chimney and hob, the sink and faucet, appliances, and civil work such as tiling, plumbing and electrical. None of it is hidden: each item is named here and itemised separately on your quotation, so nothing appears later that you did not see at the start.',
			),
			array(
				'q' => 'Is the estimate really free?',
				'a' => sprintf(
					'Yes. The estimator on this site is free, needs no sign-up, and gives you a range before we ask for your phone number. The site visit and measurement at your home are free too and carry no obligation — you keep the measurements either way. Call %1$s if you would rather just ask.',
					$phone_disp
				),
			),
			array(
				/* No payment schedule is published, because facts.php records none.
				   Declining to print one is the honest answer here. */
				'q' => 'What are the payment terms?',
				'a' => 'We do not print a payment schedule on this page. Terms belong on your own quotation, beside the items they are paying for, rather than on a web page you cannot hold anyone to. Ask for them in writing on the free visit — along with the written warranty — and read both before you commit to anything.',
			),
		),
	),

	/* ---------------- 2. TIMELINE & INSTALLATION ---------------- */
	array(
		'id'      => 'timeline',
		'title'   => 'Timeline and installation',
		'lead'    => 'One duration is published on this site, and it is the one at your end: installation. Everything before it is a date we agree with you in writing rather than a number we advertise.',
		'items'   => array(
			array(
				'q' => 'How long does the design stage take?',
				'a' => 'It begins with a free measurement at your home, and how long it runs after that is mostly up to you: one household settles the drawings in a single sitting, another wants three rounds of changes to the island. We would rather not print a number here that your own project would then be measured against. Ask for your dates in writing once the drawings are agreed.',
			),
			array(
				'q' => 'How long does manufacturing take?',
				'a' => sprintf(
					'Your kitchen is manufactured in our own %1$s sq ft factory at %2$s rather than placed with an outside workshop, so the queue is ours to answer for. We do not publish a fixed manufacturing time on this page, because the honest answer depends on what you have chosen — ask for your dates in writing when you approve the drawings.',
					$sqft,
					$place
				),
			),
			array(
				/* The only duration this site publishes, and its leading sentence is
				   alea_install_sentence() from facts.php rather than a string typed
				   here: the same question is answered on the home page and on
				   /modular-kitchen/ too, and all three feed FAQPage schema, so the
				   wording has to come from one place or Google can surface two
				   different answers to one question. Only the clause after it is
				   local to this page. Dropped entirely rather than printed as
				   "about 0 days" if facts.php ever loses the key. */
				'if' => $install_days > 0,
				'q'  => 'How long does installation take?',
				'a'  => alea_install_sentence() . ' The cabinets arrive from the factory already built, so the work in your flat is fitting, levelling and aligning rather than carpentry on your floor.',
			),
			array(
				'q' => 'What actually happens on installation day?',
				'a' => 'Our own team sets the units out and levels them, fixes them to the walls, then hangs and adjusts the doors and drawers so the gaps line up and the soft-close works as it should. Civil work — tiling, plumbing and the electrical points — is not in our scope, so it needs to be finished before we start, and the countertop, chimney, hob and sink are fitted as separate items. If you want to know exactly what your own installation will involve, ask on the free visit.',
			),
		),
	),

	/* ---------------- 3. MATERIALS & HARDWARE ---------------- */
	array(
		'id'      => 'materials',
		'title'   => 'Materials and hardware',
		'lead'    => 'What goes into the cabinet, described in the terms we can stand behind — and a plain admission of what a web page cannot tell you about a finish.',
		'items'   => array(
			array(
				'q' => 'Which hardware do you fit?',
				'a' => sprintf(
					'%1$s — both are standard on every ALEA kitchen, in every collection. Neither is a paid upgrade and neither is the cheaper option: hinges, runners and soft-close systems from named brands, specified line by line on your quotation. You can work the hardware yourself at our experience centre before you decide anything.',
					$brands
				),
			),
			array(
				'q' => 'What does soft-close actually mean?',
				'a' => 'It is a damper built into the hinge or the drawer runner. In the last few centimetres of travel it takes the speed out of the door or drawer so it settles shut quietly instead of slamming, which is also easier on the panel and on the hardware over the years. It is standard here rather than an extra, in every collection.',
			),
			array(
				/* Answered with each collection's own 'character' line from
				   facts.php — the field page-kitchens.php prints on the cards.
				   The price bands are answered in group 01 and are deliberately
				   not repeated here: they do not describe a finish. */
				'q' => 'How do the three collections differ?',
				'a' => sprintf(
					'The factory line is the same, the %1$s hardware is the same and the %2$s is the same in all three. What changes is the depth of the finish and how far the fitting choices go. %3$sWe describe them in general terms here on purpose — a finish is something to see and handle rather than to read a specification of, so come and look at the samples before you choose.',
					$brands,
					$warranty_name,
					$chars_txt
				),
			),
			array(
				/* General industry guidance, labelled as guidance in the visible
				   text. No ALEA-specific measured claim about any finish. */
				'q' => 'How should I clean and look after the finish?',
				'a' => 'General guidance rather than an instruction specific to your kitchen: a soft, damp cloth and a mild detergent handle almost everything, and drying the surface afterwards matters more than what you cleaned it with. Keep abrasive scourers, bleach and strong solvents off shutters and edges, wipe spills near the edge banding rather than letting them sit, and let steam out of the room instead of into the cabinets above the hob. For anything specific to the finish you have chosen, ask us on the visit.',
			),
		),
	),

	/* ---------------- 4. WARRANTY & AFTER-SALES ---------------- */
	array(
		'id'      => 'warranty',
		'title'   => 'Warranty and after-sales',
		'lead'    => 'A written document, and one phone number that reaches the people who built the kitchen. No coverage table is printed here, and the answer below says why.',
		'items'   => array(
			array(
				/* The whole answer is the year count, so it is dropped rather
				   than degraded into "0 years, in writing". */
				'if' => $warranty_yrs > 0,
				'q'  => 'What warranty do I get?',
				'a'  => sprintf(
					'%1$d years, in writing. It is handed over as a document with your kitchen rather than promised verbally, and it is the same %1$d years in all three collections.',
					$warranty_yrs
				),
			),
			array(
				/* No coverage table anywhere on this page: the terms live in the
				   written document, and only there. */
				'q' => 'What does the warranty cover?',
				'a' => 'What it covers, and what it does not, is set out in the written document you receive with your kitchen. We deliberately do not print a coverage table on this page: a summary written for a website is not the thing you would be holding us to, and the document is. Ask to read it on your free visit, before you commit to anything, and take your time over it.',
			),
			array(
				'q' => 'Something needs attention after installation. What do I do?',
				'a' => sprintf(
					'Call %1$s, or send a WhatsApp message on the same number, and tell us what the kitchen is doing. The company that quoted you, built the kitchen and installed it is the same company that answers that phone — there is no outside workshop to chase. Keep your warranty document to hand when you call.',
					$phone_disp
				),
			),
			array(
				/* The one place the itemised-quotation clause is spelled out in
				   full — the other two answers refer to it rather than repeat it,
				   so the FAQPage schema does not carry three near-duplicates. */
				'q' => 'What do I get in writing?',
				'a' => sprintf(
					'Two documents. An itemised quotation after the free measurement — every panel, finish and fitting priced line by line rather than as one lump sum — and %1$s, handed over as a written document with your kitchen.',
					$warranty_doc
				),
			),
		),
	),

	/* ---------------- 5. VISITS & BUYING ---------------- */
	array(
		'id'      => 'visits',
		'title'   => 'Visits and buying',
		'lead'    => 'Both visits are free: we come to you, or you come to the factory. Neither one obliges you to buy anything.',
		'items'   => array(
			array(
				'q' => 'Is the site visit really free?',
				'a' => 'Yes — free, and with no obligation. We come to your home, measure the kitchen and tell you plainly which layouts the room will take. You keep the measurements whether or not you buy anything from us.',
			),
			array(
				'q' => 'Do I need drawings or a floor plan before you come?',
				'a' => 'No. Bring them if you have them — a builder\'s plan is useful, and so is a photograph of the room with the door, window, water line and gas point visible — but nothing is required. Taking the measurements is our job, and the layout is decided by where those four things already are rather than by preference.',
			),
			array(
				'q' => 'Can I visit the factory?',
				'a' => sprintf(
					'Yes. The factory is at %1$s — %2$s sq ft, ours rather than rented floor space in somebody else\'s workshop — and you are welcome to come and watch kitchens being built before you pay anything. That visit is free as well.',
					$place,
					$sqft
				),
			),
			array(
				/* Only the four cities in facts.php. The site carries legacy pages
				   for other cities whose service claims are not verified; none of
				   them is claimed or linked here. */
				'q' => 'Which areas do you serve?',
				'a' => sprintf(
					'%1$s. If you are just outside those, ask us rather than assuming either way — we would rather tell you plainly on the phone than have you find out after a measurement.',
					$areas
				),
			),
			array(
				'q' => 'What happens after the visit?',
				'a' => 'You get the itemised quotation, built from the measurements, and you owe nothing for it. If something in it is wrong, say so and we will price it again. If it is not for you, keep the measurements; they are yours.',
			),
		),
	),
);

/* Prune any answer whose governing fact is missing, BEFORE the count, the
   accordions and the JSON-LD are built — so all three agree and none of them
   asserts a zero. */
foreach ( $groups as $gi => $g ) {
	foreach ( $g['items'] as $qi => $item ) {
		if ( isset( $item['if'] ) && ! $item['if'] ) {
			unset( $groups[ $gi ]['items'][ $qi ] );
		}
	}
	$groups[ $gi ]['items'] = array_values( $groups[ $gi ]['items'] );
}

/* Derived, never typed: the hero and the spec strip both count the answers. */
$answer_count = 0;
foreach ( $groups as $g ) {
	$answer_count += count( $g['items'] );
}

/* ONE FAQPage covering every group, built from the same array that renders the
   visible text — so an answer cannot be edited in one place and not the other. */
$faq_schema = array(
	'@context'   => 'https://schema.org',
	'@type'      => 'FAQPage',
	'mainEntity' => array(),
);
foreach ( $groups as $g ) {
	foreach ( $g['items'] as $item ) {
		$faq_schema['mainEntity'][] = array(
			'@type'          => 'Question',
			'name'           => $item['q'],
			'acceptedAnswer' => array(
				'@type' => 'Answer',
				'text'  => $item['a'],
			),
		);
	}
}
?>
<div class="ax-root">

	<style data-no-optimize="1">
	/* Page-specific: the hero's secondary text link, the mono jump index, and
	   scroll-margin so a deep link does not land under the theme header. */
	.axp-hero-alt{margin-top:var(--sp-3);font-size:15px;color:#E4E1DC}
	/* Hero link: a real 44px tap target, not a 25px inline box. (The city links
	   below get theirs from the kit's .ax-btn-note a rule.) */
	.axp-hero-alt a{color:inherit;display:inline-flex;align-items:center;min-height:var(--tap);vertical-align:middle}
	.axp-jump{display:flex;flex-wrap:wrap;gap:var(--sp-2) var(--sp-5)}
	.axp-jump__link{display:inline-flex;align-items:center;min-height:var(--tap);font-family:var(--font-mono);font-size:var(--fs-micro);letter-spacing:var(--ls-mono-wide);text-transform:uppercase;color:var(--ax-fg);text-decoration:none;border-bottom:1px solid var(--ax-rule)}
	.axp-jump__link:hover{border-bottom-color:var(--ax-fg)}
	.axp-jump__no{color:var(--ax-fg-muted);margin-right:.5em;font-variant-numeric:tabular-nums}
	.axp-group,.axp-q,#alea-book,#estimate{scroll-margin-top:88px}
	.axp-q:target>.ax-faq__q{padding-left:var(--sp-3);border-left:2px solid var(--ax-fg)}
	</style>

	<!-- ================================================== 1. HERO -->
	<section class="ax-hero ax-hero--short">
		<?php
		/* Catalogue key only — the file and its verified alt come from images.php,
		   which describes what is in the frame rather than what the filename
		   claims. This is an experience-centre display, and the alt says so. */
		echo alea_img( 'kitchen-tall', array( 'class' => 'ax-hero__img', 'eager' => true ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built and escaped by alea_img().
		?>
		<div class="ax-hero__inner">
			<p class="ax-eyebrow">Questions</p>
			<h1 class="ax-hero__title">Every question, answered on one page.</h1>
			<p class="ax-hero__sub">
				<?php echo (int) $answer_count; ?> answers on price, timelines, hardware, warranty and visits &mdash;
				built from the same numbers as the rest of this site, and with a plain
				&ldquo;we do not publish that&rdquo; wherever we cannot back a claim.
			</p>
			<div class="ax-hero__actions">
				<a class="ax-btn ax-btn--primary ax-btn--lg" href="<?php echo esc_url( $calc_url ); ?>">Get my price</a>
			</div>
			<p class="axp-hero-alt">or <a href="#alea-book">book a free design visit</a> &mdash; free, no obligation</p>
			<p class="ax-hero__credit">
				<?php echo (int) $answer_count; ?> ANSWERS
				/ &#8377;<?php echo esc_html( alea_inr( $band_low ) ); ?>&ndash;<?php echo esc_html( alea_inr( $band_high ) ); ?> <?php echo esc_html( strtoupper( $price_unit ) ); ?>
				<?php if ( $warranty_yrs > 0 ) : ?>
				/ <?php echo (int) $warranty_yrs; ?>-YEAR WRITTEN WARRANTY
				<?php endif; ?>
			</p>
		</div>
	</section>

	<!-- ================================================== 2. SPEC STRIP -->
	<!-- Four facts the page is asked about most. Every value is read from
	     facts.php or derived in PHP above; none is typed here. -->
	<section class="ax-section ax-section--flush" aria-label="ALEA key facts">
		<div class="ax-wrap ax-wrap--flush">
			<div class="ax-specstrip">
				<div class="ax-specstrip__item">
					<span class="ax-specstrip__label">Answers here</span>
					<span class="ax-specstrip__value"><?php echo (int) $answer_count; ?><span class="ax-specstrip__unit">questions</span></span>
				</div>
				<div class="ax-specstrip__item">
					<span class="ax-specstrip__label">Published band</span>
					<span class="ax-specstrip__value">&#8377;<?php echo esc_html( alea_inr( $band_low ) ); ?>&ndash;<?php echo esc_html( alea_inr( $band_high ) ); ?><span class="ax-specstrip__unit"><?php echo esc_html( $price_unit ); ?></span></span>
				</div>
				<?php if ( $install_days > 0 ) : ?>
				<div class="ax-specstrip__item">
					<span class="ax-specstrip__label">Installation</span>
					<span class="ax-specstrip__value"><?php echo (int) $install_days; ?><span class="ax-specstrip__unit">days</span></span>
				</div>
				<?php endif; ?>
				<?php if ( $warranty_yrs > 0 ) : ?>
				<div class="ax-specstrip__item">
					<span class="ax-specstrip__label">Warranty</span>
					<span class="ax-specstrip__value"><?php echo (int) $warranty_yrs; ?><span class="ax-specstrip__unit">years, in writing</span></span>
				</div>
				<?php endif; ?>
			</div>
		</div>
	</section>

	<!-- ================================================== 3. JUMP INDEX -->
	<section class="ax-section ax-section--tight">
		<div class="ax-wrap">
			<p class="ax-mono--label ax-mb-3">Jump to a group</p>
			<nav class="axp-jump" aria-label="Question groups">
				<?php foreach ( $groups as $gi => $g ) : ?>
				<a class="axp-jump__link" href="#faq-<?php echo esc_attr( $g['id'] ); ?>">
					<span class="axp-jump__no"><?php echo esc_html( str_pad( (string) ( $gi + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
					<?php echo esc_html( $g['title'] ); ?>
				</a>
				<?php endforeach; ?>
			</nav>
		</div>
	</section>

	<!-- ================================================== 4. THE FIVE GROUPS -->
	<?php
	/* Each group is one .ax-faq accordion. The answers here and the JSON-LD
	   below are the same strings from $groups — there is no second copy. */
	foreach ( $groups as $gi => $g ) :
		?>
	<section class="ax-section ax-section--ruled axp-group<?php echo 1 === $gi % 2 ? ' ax-section--sheet' : ''; ?>" id="faq-<?php echo esc_attr( $g['id'] ); ?>">
		<div class="ax-wrap ax-wrap--narrow">
			<header class="ax-head ax-head--wide ax-reveal">
				<?php /* Group number derived from the loop index — the same
				         expression the jump index uses — so reordering the groups
				         cannot leave the two disagreeing. */ ?>
				<p class="ax-eyebrow">Group <?php echo esc_html( str_pad( (string) ( $gi + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></p>
				<h2 class="ax-h2"><?php echo esc_html( $g['title'] ); ?></h2>
				<p class="ax-lead"><?php echo esc_html( $g['lead'] ); ?></p>
			</header>
			<div class="ax-faq ax-reveal">
				<?php foreach ( $g['items'] as $qi => $item ) : ?>
				<details class="ax-faq__item axp-q" id="q-<?php echo esc_attr( $g['id'] ); ?>-<?php echo (int) ( $qi + 1 ); ?>">
					<summary class="ax-faq__q"><?php echo esc_html( $item['q'] ); ?></summary>
					<div class="ax-faq__a"><p><?php echo esc_html( $item['a'] ); ?></p></div>
				</details>
				<?php endforeach; ?>
			</div>

			<?php /* One quiet route onward per group. Ghost buttons only: the single
			         oxblood CTA on this screen is the estimator in the price group. */ ?>
			<div class="ax-btnrow ax-mt-6 ax-reveal">
				<?php if ( 'price' === $g['id'] ) : ?>
					<a class="ax-btn ax-btn--primary" href="<?php echo esc_url( $calc_url ); ?>">Get my price</a>
					<a class="ax-btn ax-btn--ghost" href="<?php echo esc_url( $price_url ); ?>">See the published price list</a>
				<?php elseif ( 'timeline' === $g['id'] ) : ?>
					<?php /* One label for one action, site-wide: "Book a free design visit"
					         is this destination's own H1, <title> and slug. What happens on
					         the visit is still called a measurement in prose — it is just
					         not a second name for the button. */ ?>
					<a class="ax-btn ax-btn--ghost" href="#alea-book">Book a free design visit</a>
					<a class="ax-btn ax-btn--ghost" href="<?php echo esc_url( $factory_url ); ?>">Inside the factory</a>
				<?php elseif ( 'materials' === $g['id'] ) : ?>
					<a class="ax-btn ax-btn--ghost" href="<?php echo esc_url( $kitchens_url ); ?>">The three collections</a>
					<a class="ax-btn ax-btn--ghost" href="<?php echo esc_url( $wardrobe_url ); ?>">Wardrobes</a>
				<?php elseif ( 'warranty' === $g['id'] ) : ?>
					<a class="ax-btn ax-btn--ghost" href="<?php echo esc_url( $warranty_url ); ?>">More on the warranty</a>
					<a class="ax-btn ax-btn--ghost" href="<?php echo esc_url( $contact_url ); ?>">Contact us</a>
				<?php else : ?>
					<a class="ax-btn ax-btn--ghost" href="<?php echo esc_url( $visit_url ); ?>">Book a free design visit</a>
					<a class="ax-btn ax-btn--ghost" href="<?php echo esc_url( $projects_url ); ?>">See our work</a>
				<?php endif; ?>
			</div>

			<?php
			/* The four cities are printed from facts.php's service_area, so a city
			   this company has not confirmed serving cannot be linked here as one.
			   The legacy /locations/ pages for other cities are deliberately not
			   linked: those service claims are not verified. */
			if ( 'visits' === $g['id'] && $areas_arr ) :
				?>
			<p class="ax-btn-note axp-cities ax-reveal">
				Where we work:
				<?php foreach ( $areas_arr as $ai => $city ) : ?>
					<?php echo $ai ? ' &middot; ' : ' '; ?>
					<a class="ax-link" href="<?php echo esc_url( home_url( '/locations/' . strtolower( $city ) . '/' ) ); ?>"><?php echo esc_html( $city ); ?></a>
				<?php endforeach; ?>
			</p>
			<?php endif; ?>
		</div>
	</section>
	<?php endforeach; ?>

	<!-- ONE FAQPage for all five groups, generated from the same $groups array
	     that rendered the visible answers above. -->
	<?php /* JSON_HEX_TAG (and no JSON_UNESCAPED_SLASHES) so a "</script>" inside any
	         future FAQ string can never terminate this block. */ ?>
	<script type="application/ld+json"><?php echo wp_json_encode( $faq_schema, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG ); ?></script>

	<!-- ================================================== 5. STILL UNANSWERED -->
	<!-- id="estimate": landing anchor for the theme's site-wide sticky mobile
	     bar (.aleac-mbar "Free Estimate" button in functions.php). -->
	<span id="estimate" class="ax-sr-only"></span>
	<section class="ax-section ax-section--ruled" id="alea-book">
		<div class="ax-wrap">
			<div class="ax-grid ax-grid--split">
				<div class="ax-reveal">
					<p class="ax-eyebrow">Still not answered</p>
					<h2 class="ax-h2">Ask us the question this page missed.</h2>
					<p class="ax-lead ax-mt-4">
						Some answers only exist once someone has measured your kitchen. Both visits are free and
						carry no obligation: a measurement at your home across <?php echo esc_html( $areas ); ?>,
						or a factory visit at <?php echo esc_html( $place ); ?> where you can watch kitchens
						being built before you pay a rupee.
					</p>
					<div class="ax-btnrow ax-mt-5">
						<a class="ax-btn ax-btn--ink" href="<?php echo esc_url( $tel_href ); ?>">Call <?php echo esc_html( $phone_disp ); ?></a>
						<a class="ax-btn ax-btn--wa" href="<?php echo esc_url( $wa_href ); ?>">WhatsApp us</a>
					</div>
					<?php /* Sentence-case body text, not .ax-btn-note: that class is an
					         11px uppercase mono caption, and prose (plus a nested
					         .ax-link) set in it reads as shouting. */ ?>
					<p class="ax-mt-3 ax-muted">
						Would you rather see a number first? The
						<a class="ax-link" href="<?php echo esc_url( $calc_url ); ?>">estimator</a>
						is free and needs no sign-up. More about the company on
						<a class="ax-link" href="<?php echo esc_url( $about_url ); ?>">our About page</a>.
					</p>
				</div>
				<div class="ax-form ax-form--card ax-reveal">
					<h3 class="ax-form__title">Book your free visit</h3>
					<p class="ax-form__note">Free / no obligation / <?php echo esc_html( implode( ' · ', $areas_arr ) ); ?></p>
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
		var root = null;
		try {
			root = document.querySelector('.ax-root');
			if (!root) { return; }
		} catch (e) { return; }

		/* Deep links: /faqs/#q-price-3 must OPEN that question, not just scroll
		   past a closed <summary>. Runs on load and on every hash change. */
		function openFromHash() {
			try {
				var h = window.location.hash;
				if (!h || h.length < 2) { return; }
				var el = root.querySelector(h);
				if (!el) { return; }
				var d = (el.tagName === 'DETAILS') ? el : (el.closest ? el.closest('details') : null);
				if (d) { d.open = true; }
			} catch (err) {}
		}
		openFromHash();
		try { window.addEventListener('hashchange', openFromHash, false); } catch (err) {}

		/* Scroll reveal. Content is visible until .ax-js is added, so a blocked
		   script can never leave a blank page. */
		try {
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
			try {
				var all = document.querySelectorAll('.ax-reveal');
				for (var j = 0; j < all.length; j++) { all[j].classList.add('is-visible'); }
			} catch (err2) {}
		}
	})();
	</script>

</div><!-- /.ax-root -->
