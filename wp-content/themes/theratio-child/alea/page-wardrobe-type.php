<?php
/**
 * ALEA — Wardrobe type template ("The Manufacturer's Record")
 * Serves /wardrobe/sliding|hinged|walk-in/ from one file. The variant arrives
 * via $GLOBALS['alea_page_args']['type'], whitelisted below with a safe default,
 * exactly as page-collection.php does it. Included inside <main class="alea-main">.
 *
 * FACTUAL INTEGRITY NOTES (read before editing):
 * - NO rupee figure appears anywhere on this page, and none may be added.
 *   Wardrobe pricing is NOT verified. The page explains the METHOD (priced per
 *   design after a free measurement) instead of inventing a rate.
 * - Every business number (factory sq ft, place, warranty years, install days,
 *   hardware brands, founding dates, service area) is read from facts.php.
 * - The "when to choose" and "trade-offs" copy is general wardrobe-planning
 *   guidance, framed as guidance in the visible text. It is never presented as
 *   an ALEA measurement, test or delivered-project claim.
 * - Hardware: Hettich and Blum are named as fitted-as-standard only. Neither is
 *   framed as a paid upgrade and no price difference between them is implied.
 *   The brands are NOT attributed to sliding-door gear or any other component
 *   category — only the brand names themselves are verified.
 * - IMAGERY: every picture comes from images.php via alea_img() — never a
 *   hard-coded path, never hand-written alt text.
 * - FLAGGED POOL LIMITATION: the media library contains NO wardrobe photography
 *   at all. The two frames this page once used as wardrobes were opened and
 *   catalogued in images.php: both are CGI KITCHEN renders — not wardrobes, and
 *   not photographs. No wardrobe image is therefore shown anywhere on this page;
 *   all three types carry a visible note saying so. The one photograph on the
 *   page is the experience-centre reception desk, captioned as exactly that.
 *   Wardrobe display photography is pending from the owner.
 * - ROUTING: alea_redesign_map() in functions.php registers '/wardrobe/sliding/',
 *   '/wardrobe/hinged/' and '/wardrobe/walk-in/' against this file, each passing
 *   its variant in 'args' — the same shape page-collection.php uses for the three
 *   collection URLs. Those paths are built here from $type_base and are the ones
 *   page-wardrobes.php already links to, so a route and a link cannot disagree.
 */

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/facts.php';
require_once __DIR__ . '/images.php';

$f = alea_facts();

/* ---- Variant: whitelist + safe default (same pattern as page-collection.php) ---- */
$alea_valid_types = array( 'sliding', 'hinged', 'walk-in' );
$alea_args        = ( isset( $GLOBALS['alea_page_args'] ) && is_array( $GLOBALS['alea_page_args'] ) )
	? $GLOBALS['alea_page_args']
	: array();
$type = isset( $alea_args['type'] ) ? strtolower( trim( (string) $alea_args['type'] ) ) : 'sliding';
if ( ! in_array( $type, $alea_valid_types, true ) ) {
	$type = 'sliding';
}

/* Base path for the three type URLs. MUST stay identical to the paths used in
   page-wardrobes.php and registered in alea_redesign_map(), so a route and a
   link can never disagree. Sibling links are built from this one string. */
$type_base = '/wardrobe/';

$sqft       = $f['factory_sqft'];
$place      = $f['factory_place'];
$wty        = (int) $f['warranty_years'];
$days       = (int) $f['install_days'];
$brands     = implode( ' and ', $f['hardware_brands'] );
$brands_amp = implode( ' & ', $f['hardware_brands'] );
$areas      = implode( ', ', $f['service_area'] );
$book_url   = home_url( '/book-a-free-design-visit/' );
$calc_url   = home_url( '/kitchen-cost-calculator/' );
$hub_url    = home_url( $type_base );
$tel_href   = 'tel:' . alea_fact( 'phone_tel' );
$phone_disp = alea_fact( 'phone_display' );

/* ---- Per-type presentation. -------------------------------------------------
   'choose' and 'against' are generic planning guidance and are labelled as such
   in the visible copy.
   'h_inside' and 'h_centre' are per-type H2s, carried here for the same reason
   'nav' and 'sub' are: a walk-in has NO doors, so a heading written around doors
   would contradict this page's own hero copy on /wardrobe/walk-in/. Every H2 on
   the page is either type-neutral or read from this array.
   PHOTOGRAPHY: there is none. The media library holds no wardrobe photograph of
   any kind — the two frames previously heroed here turned out to be CGI kitchen
   renders. No image key is carried per type any more; the disclosure below is
   printed on all three variants, beside the hero and again in section 8.
   ------------------------------------------------------------------------- */

/* The single photography disclosure, printed on every variant. The short form
   sits in the hero (a visitor who reads the hero and leaves must still see it);
   the long form is repeated in section 8. */
$photo_hero_note = 'To be straight with you: we have no photograph of a wardrobe we can honestly show you, so this page shows none rather than passing off a picture of something else.';
$photo_note      = 'We have no wardrobe photography we can stand behind yet, so we are not going to show you a picture of something else and call it a wardrobe. There is a good deal on the floor at the experience centre — come and open a door. Real wardrobe photographs go up here as soon as we have them.';

$presentation = array(
	'sliding'  => array(
		'name'      => 'Sliding wardrobes',
		'short'     => 'sliding',
		'wa'        => 'Hi ALEA, I would like a free measurement for a sliding wardrobe.',
		'nav'       => 'Doors run across the front',
		'sub'       => 'Doors that run across the front of the wardrobe instead of swinging into the room. The usual answer where there is no floor space to spare — and you reach half the opening at a time.',
		'h_inside'  => 'The doors are the smaller half of the decision.',
		'h_centre'  => 'Open a door before you decide anything.',
		'choose'    => array(
			'There is no clear floor in front of the wardrobe — a bed, a passage or a corner sits right against it.',
			'The room is narrow, and a swinging door would take space the room cannot spare.',
			'You want one long, unbroken run of wardrobe along a wall.',
			'A full-height mirror would be useful on the door, staying where it is rather than swinging about.',
		),
		'against'   => array(
			'Only half the opening is reachable at a time — one door is always covering the other.',
			'The track and the door gear take up depth at the front, so the usable interior is a little shallower than a hinged wardrobe of the same footprint.',
			'The floor channel wants keeping clear and the odd wipe: this is a moving part, not a static one.',
			'The far ends of a long run are the hardest places to reach into.',
		),
	),
	'hinged'   => array(
		'name'      => 'Hinged wardrobes',
		'short'     => 'hinged',
		'wa'        => 'Hi ALEA, I would like a free measurement for a hinged wardrobe.',
		'nav'       => 'Doors swing open, full access',
		'sub'       => 'Doors that swing open, so the whole interior is in front of you at once. The simpler mechanism of the two — it only asks for room to open.',
		'h_inside'  => 'The doors are the smaller half of the decision.',
		'h_centre'  => 'Open a door before you decide anything.',
		'choose'    => array(
			'There is clear floor in front of the wardrobe for a door to swing into.',
			'You want to see the full depth of the interior in one go, front to back.',
			'The inside is shelf-heavy or drawer-heavy and you would rather reach all of it at once.',
			'As a rule, hinges are the simpler and less expensive of the two mechanisms, so more of the budget can go into the interior.',
		),
		'against'   => array(
			'It needs swing clearance — roughly the width of the door, kept clear in front of it.',
			'In a narrow room an open door can block the walkway, or foul the bed.',
			'A wide opening needs two doors rather than one, so there is a frame line down the middle.',
			'The doors carry their weight on the hinges, so tall or mirrored shutters need the hinge count matched to them — that is settled on the design, not guessed at.',
		),
	),
	'walk-in'  => array(
		'name'      => 'Walk-in wardrobes',
		'short'     => 'walk-in',
		'wa'        => 'Hi ALEA, I would like a free measurement for a walk-in wardrobe.',
		'nav'       => 'A room given over to storage',
		'sub'       => 'Not a cupboard with doors but a small room given over to storage. With no shutters to pay for, it generally buys the most storage for the money — provided you have the area to give it.',
		'h_inside'  => 'What is inside is the whole decision.',
		'h_centre'  => 'Walk into one before you decide anything.',
		'choose'    => array(
			'A small room, a deep recess or one end of the bedroom can be given over to storage.',
			'You want the most storage for the money: with no shutters, the budget goes into the interior instead.',
			'You would rather see the whole wardrobe open, laid out and lit.',
			'Two people get ready at the same time, and a single door becomes a bottleneck.',
		),
		'against'   => array(
			'It takes floor area away from the room permanently — that area is the real price of it.',
			'Everything is on show, so it looks tidy only for as long as you keep it tidy.',
			'Open storage collects dust more readily than a shuttered wardrobe does.',
			'It wants its own lighting, and often a little electrical or civil work to make the space usable.',
		),
	),
);

$t          = $presentation[ $type ];
$type_name  = $t['name'];
$type_short = $t['short'];
$type_index = (int) array_search( $type, $alea_valid_types, true ) + 1;
$wa_href    = alea_wa_link( $t['wa'] );

/* ---- What goes inside. General, honest categories — no invented materials,
   no fitting counts, no dimensions. The visible copy says plainly that the
   internals are specified per design and itemised in the quotation. ---- */
$inside = array(
	array( 'Hanging', 'Full-height and half-height runs, set to what you actually hang' ),
	array( 'Shelving', 'Fixed and adjustable shelves, and the loft unit above if the height allows' ),
	array( 'Drawers', 'Drawer banks on soft-close runners, sized around what goes in them' ),
	array( 'Accessories', 'Shoe racks, pull-outs, mirrors and lighting, chosen on the design' ),
);

/* ---- FAQ: the visible HTML and the JSON-LD render from this ONE array, so the
   schema can never drift from the text on the page. No prices anywhere. ---- */
$faq_q1 = array(
	'sliding' => 'Should I choose a sliding wardrobe?',
	'hinged'  => 'Should I choose a hinged wardrobe?',
	'walk-in' => 'Should I choose a walk-in wardrobe?',
);
$faq_a1 = array(
	'sliding' => 'As general guidance: yes, when there is no clear floor in front of the wardrobe for a door to swing into, or when the room is narrow enough that a swinging door would be in the way. The trade-off is that half the opening is covered at any one time, and the door gear takes a little depth off the front of the interior. If the floor in front is free, a hinged wardrobe will give you the whole interior at once. We will say which one your room actually takes on the free measurement visit.',
	'hinged'  => 'As general guidance: yes, when there is room for a door to swing — you get the full depth of the interior in one view, and the mechanism is the simpler of the two. The trade-off is the clearance it needs, roughly the width of the door, which is exactly what a narrow room does not have. If the floor in front is committed to something else, sliding doors are usually the answer instead. We will say which one your room takes on the free measurement visit.',
	'walk-in' => 'As general guidance: yes, when you can genuinely give up the floor area — a small room, a deep recess or one end of a bedroom. With no shutters to pay for, it usually buys the most storage for the money, and everything is visible at once. The trade-offs are that the area is gone for good, everything is on show, and open storage needs lighting and a bit more upkeep. We will tell you plainly on the free measurement whether the space is worth turning over to it.',
);

$faq = array(
	array(
		'q' => $faq_q1[ $type ],
		'a' => $faq_a1[ $type ],
	),
	array(
		'q' => sprintf( 'What goes inside a %s wardrobe?', $type_short ),
		'a' => 'Hanging space at full and half height, shelving, drawer banks on soft-close runners, and accessories such as shoe racks, pull-outs, mirrors, lighting and a loft unit above. How much of each you get is decided on your design rather than fixed in advance — two wardrobes of exactly the same width can be laid out completely differently inside. Whatever is specified for yours is itemised in the written quotation you receive after the free measurement.',
	),
	array(
		'q' => sprintf( 'Is a %s wardrobe built to the same standard as your kitchens?', $type_short ),
		'a' => sprintf(
			'Yes. It is cut, edge-banded and assembled in the same %1$s sq ft factory at %2$s, by the same people, with %3$s hardware fitted as standard — and it leaves with the same %4$d-year warranty, in writing, never a verbal promise. The warranty covers panels and hardware against manufacturing defects; the full terms are in the written document you receive, and you are welcome to read them on the free measurement visit before you order anything. We have made furniture since %5$s and modular kitchens since %6$s.',
			$sqft,
			$place,
			$brands,
			$wty,
			$f['founded_furniture'],
			$f['alea_since']
		),
	),
);

$faq_schema = array(
	'@context'   => 'https://schema.org',
	'@type'      => 'FAQPage',
	'mainEntity' => array(),
);
foreach ( $faq as $faq_item ) {
	$faq_schema['mainEntity'][] = array(
		'@type'          => 'Question',
		'name'           => $faq_item['q'],
		'acceptedAnswer' => array(
			'@type' => 'Answer',
			'text'  => $faq_item['a'],
		),
	);
}
?>
<?php /* ax-has-stickybar reserves bottom space for a sticky bar. This page does
         NOT render .ax-stickybar itself — the class is here because the THEME
         injects its own site-wide bar (.aleac-mbar) below 781px and that bar
         occupies exactly this space. Do not read it as a promise that this file
         builds a bar. */ ?>
<div class="ax-root ax-has-stickybar">

	<style data-no-optimize="1">
	/* Page-specific only. Everything else is design-system .ax-*. */
	/* Colour is inherited from the hero's own ground context (.ax-hero sets
	   --ax-fg-muted); no hex literal — the palette is fixed in design-system.css. */
	.axp-hero-note{margin-top:var(--sp-4);font-size:.9375rem;color:var(--ax-fg-muted)}
	.axp-hero-note--flag{max-width:46ch}
	.axp-note{max-width:60ch}
	/* Sibling-type rows: the whole row is the tap target, not the label. */
	.axp-sibrow{min-height:var(--tap-lg,52px);align-items:center}
	.axp-sibrow--link{text-decoration:none;color:inherit}
	.axp-sibrow--link .ax-spectable__key{color:var(--ax-fg);text-decoration:underline;text-decoration-color:var(--ax-rule);text-underline-offset:.22em}
	.axp-sibrow--link:hover .ax-spectable__key{text-decoration-color:var(--ax-fg)}
	/* The design system drops the sticky-bar reserve at 768px, but the THEME's
	   .aleac-mbar stays visible to 781px — re-reserve across that 14px band. */
	@media(min-width:768px) and (max-width:781px){.ax-root.ax-has-stickybar{padding-bottom:64px}}
	</style>

	<!-- ==================== 1. HERO ==================== -->
	<section class="ax-hero ax-hero--short">
		<!-- wardrobe photography pending: library has none -->
		<div class="ax-hero__inner">
			<p class="ax-eyebrow">Wardrobes &mdash; type <?php echo (int) $type_index; ?> of <?php echo count( $alea_valid_types ); ?></p>
			<h1 class="ax-hero__title"><?php echo esc_html( $type_name ); ?>.</h1>
			<p class="ax-hero__sub"><?php echo esc_html( $t['sub'] ); ?></p>
			<div class="ax-hero__actions">
				<a class="ax-btn ax-btn--primary ax-btn--lg" href="<?php echo esc_url( $book_url ); ?>">Book a free measurement</a>
			</div>
			<p class="axp-hero-note">Free, at your home, no obligation &mdash; the measurements are yours to keep either way.</p>
			<?php /* The photography limitation is disclosed HERE, where a hero photograph
			         would otherwise sit — a visitor who reads the hero and leaves must
			         still see it. Section 8 carries the full wording. */ ?>
			<p class="axp-hero-note axp-hero-note--flag"><?php echo esc_html( $photo_hero_note ); ?></p>
		</div>
	</section>

	<!-- ==================== 2. SPEC STRIP ==================== -->
	<section class="ax-section ax-section--flush" aria-label="ALEA manufacturing facts">
		<div class="ax-wrap ax-wrap--flush">
			<div class="ax-specstrip">
				<div class="ax-specstrip__item">
					<span class="ax-specstrip__label">Own factory</span>
					<span class="ax-specstrip__value"><?php echo esc_html( $sqft ); ?><span class="ax-specstrip__unit">sq ft</span></span>
				</div>
				<div class="ax-specstrip__item">
					<span class="ax-specstrip__label">Hardware</span>
					<span class="ax-specstrip__value"><?php echo esc_html( $brands_amp ); ?></span>
				</div>
				<div class="ax-specstrip__item">
					<span class="ax-specstrip__label">Written warranty</span>
					<span class="ax-specstrip__value"><?php echo (int) $wty; ?><span class="ax-specstrip__unit">years</span></span>
				</div>
				<?php /* install_days is an OWNER fact evidenced for KITCHENS. It is
				         labelled as such rather than silently transferred to
				         wardrobes, which have no verified fitting time. */ ?>
				<div class="ax-specstrip__item">
					<span class="ax-specstrip__label">Kitchen installation</span>
					<span class="ax-specstrip__value"><?php echo (int) $days; ?><span class="ax-specstrip__unit">days</span></span>
				</div>
			</div>
		</div>
		<div class="ax-wrap">
			<p class="ax-mono--label ax-mt-3 ax-pb-5">
				Factory at <?php echo esc_html( $place ); ?> &middot; <?php echo esc_html( $brands_amp ); ?> fitted as standard &middot;
				the <?php echo (int) $days; ?>-day figure is our kitchen installation time &mdash; wardrobe fitting time is confirmed on the free measurement
			</p>
		</div>
	</section>

	<!-- ==================== 3. WHEN TO CHOOSE IT / TRADE-OFFS ==================== -->
	<section class="ax-section ax-section--ruled">
		<div class="ax-wrap ax-wrap--narrow">
			<div class="ax-head ax-reveal">
				<p class="ax-eyebrow">When to choose it</p>
				<?php /* Phrased around the wardrobe, not around "doors": a walk-in has
				         none, and this heading is shared by all three types. */ ?>
				<h2 class="ax-h2">Where a <?php echo esc_html( $type_short ); ?> wardrobe wins &mdash; and where it costs you.</h2>
				<p class="ax-lead">
					General wardrobe-planning guidance, not a measurement of your room. Every type is a trade,
					and we would rather you knew both halves of it before we come and measure.
				</p>
			</div>
			<div class="ax-included ax-reveal">
				<div class="ax-included__col ax-included--in">
					<h3 class="ax-included__title">Choose <?php echo esc_html( $type_short ); ?> when</h3>
					<ul class="ax-included__list">
						<?php foreach ( $t['choose'] as $line ) : ?>
						<li><?php echo esc_html( $line ); ?></li>
						<?php endforeach; ?>
					</ul>
				</div>
				<div class="ax-included__col ax-included--out">
					<h3 class="ax-included__title">What you give up</h3>
					<ul class="ax-included__list">
						<?php foreach ( $t['against'] as $line ) : ?>
						<li><?php echo esc_html( $line ); ?></li>
						<?php endforeach; ?>
					</ul>
				</div>
			</div>
			<p class="ax-btn-note">Guidance, not a rule &mdash; the room usually decides, and we will say so plainly on the free measurement</p>
		</div>
	</section>

	<!-- ==================== 4. WHAT'S INSIDE ==================== -->
	<!-- Deliberately general: no fitting counts, no dimensions, no invented
	     materials. The copy states that internals are specified per design. -->
	<section class="ax-section">
		<div class="ax-wrap">
			<div class="ax-grid ax-grid--split">
				<div class="ax-head ax-mb-0 ax-reveal">
					<p class="ax-eyebrow">What's inside</p>
					<?php /* Per-type heading: a walk-in has no doors, so it must not be
					         sold on a door-vs-interior trade-off. See $presentation. */ ?>
					<h2 class="ax-h2"><?php echo esc_html( $t['h_inside'] ); ?></h2>
					<p class="ax-lead ax-mt-4">
						What actually decides whether a wardrobe works is the inside of it: how much of it hangs,
						at what heights, how many drawers there are and what else has to fit. That is specified
						per design &mdash; not picked off a standard list &mdash; and itemised in your quotation.
					</p>
					<div class="ax-btnrow ax-mt-5">
						<a class="ax-btn ax-btn--ghost" href="#alea-book">Talk it through on a free measurement</a>
					</div>
				</div>
				<div class="ax-reveal">
					<div class="ax-spectable--rows">
						<?php foreach ( $inside as $row ) : ?>
						<div class="ax-spectable__row">
							<span class="ax-spectable__key"><?php echo esc_html( $row[0] ); ?></span>
							<span class="ax-spectable__val"><?php echo esc_html( $row[1] ); ?></span>
						</div>
						<?php endforeach; ?>
					</div>
					<p class="ax-spectable__note">
						Categories, not a specification &mdash; two wardrobes of the same width can be laid out
						completely differently inside. Yours is written down after the measurement.
					</p>
				</div>
			</div>
		</div>
	</section>

	<!-- ==================== 5. BUILT LIKE OUR KITCHENS ==================== -->
	<section class="ax-section ax-section--ink">
		<div class="ax-wrap">
			<div class="ax-grid ax-grid--split">
				<div class="ax-head ax-mb-0 ax-reveal">
					<p class="ax-eyebrow">Built like our kitchens</p>
					<h2 class="ax-h2">Nothing about a wardrobe is a lesser job here.</h2>
					<p class="ax-lead ax-mt-4">
						Your <?php echo esc_html( $type_short ); ?> wardrobe is cut, edge-banded and assembled on the same
						<?php echo esc_html( $sqft ); ?> sq ft floor at <?php echo esc_html( $place ); ?>, by the same people,
						from the same hardware shelf as our kitchens &mdash; and it leaves with the same written warranty.
						You are welcome to come and watch.
					</p>
				</div>
				<div class="ax-reveal">
					<ul class="ax-prooflist">
						<li class="ax-proof">
							<span class="ax-proof__tick" aria-hidden="true"></span>
							<div class="ax-proof__text">
								Made on our own floor at <?php echo esc_html( $place ); ?>, not bought in and badged.
								<span class="ax-proof__never">Never outsourced</span>
							</div>
						</li>
						<li class="ax-proof">
							<span class="ax-proof__tick" aria-hidden="true"></span>
							<div class="ax-proof__text">
								<?php /* Site-wide wording, sourced from facts.php hardware_brands.
								         Do NOT extend this to sliding-door gear or any other
								         component category: only the brand names are verified,
								         and neither brand is an upgrade over the other. */ ?>
								<?php echo esc_html( $brands ); ?> hinges, runners and soft-close systems, fitted as standard.
								<span class="ax-proof__never">Never generic hardware</span>
							</div>
						</li>
						<li class="ax-proof">
							<span class="ax-proof__tick" aria-hidden="true"></span>
							<div class="ax-proof__text">
								A <?php echo (int) $wty; ?>-year warranty covering panels and hardware against manufacturing defects &mdash; full terms in the written document you receive.
								<span class="ax-proof__never">Never a verbal promise</span>
							</div>
						</li>
						<li class="ax-proof">
							<span class="ax-proof__tick" aria-hidden="true"></span>
							<div class="ax-proof__text">
								Furniture since <?php echo esc_html( $f['founded_furniture'] ); ?>, modular kitchens since <?php echo esc_html( $f['alea_since'] ); ?> &mdash; <?php echo esc_html( $f['years_experience'] ); ?> years of it.
							</div>
						</li>
					</ul>
					<div class="ax-btnrow ax-mt-5">
						<a class="ax-btn ax-btn--ghost" href="<?php echo esc_url( home_url( '/our-factory/' ) ); ?>">See inside the factory</a>
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- ==================== 6. HOW WARDROBES ARE PRICED ==================== -->
	<!-- No rupee figure appears in this section, on purpose: wardrobe rates are
	     not verified, so the page explains the method instead of inventing one. -->
	<section class="ax-section ax-section--ruled" id="pricing">
		<div class="ax-wrap">
			<div class="ax-grid ax-grid--split">
				<div class="ax-head ax-mb-0 ax-reveal">
					<p class="ax-eyebrow">How wardrobes are priced</p>
					<h2 class="ax-h2">Measured first, priced after &mdash; per design, in writing.</h2>
					<p class="ax-lead ax-mt-4">
						We publish a rate card for kitchens because a kitchen is largely base and wall units.
						A wardrobe is not like that. Two <?php echo esc_html( $type_short ); ?> wardrobes of exactly the
						same width can cost very different amounts once the inside is settled, so we will not
						print a figure here that your wardrobe would then have to be squeezed into.
					</p>
				</div>
				<div class="ax-reveal">
					<div class="ax-prose axp-note">
						<p>What actually moves the number:</p>
						<ul>
							<li>The dimensions &mdash; width, height and depth of the run.</li>
							<li>How much of it hangs, and at what heights.</li>
							<li>How many drawer banks are inside, and on what runners.</li>
							<li>Shelves, shoe racks, pull-outs and the rest of the internal fittings.</li>
							<li>Mirrors, glass, lighting and a loft unit above.</li>
							<li>The finish on the shutters, and whether the doors slide, swing or are left off.</li>
						</ul>
						<p>
							So the order is: free measurement at your home, then a design, then a written price
							that itemises what is in it. Nothing is quoted before it is measured.
						</p>
					</div>
					<div class="ax-btnrow ax-mt-5">
						<a class="ax-btn ax-btn--primary" href="<?php echo esc_url( $book_url ); ?>">Book a free measurement</a>
						<a class="ax-btn ax-btn--ghost" href="<?php echo esc_url( $calc_url ); ?>">Price a kitchen instead</a>
					</div>
					<p class="ax-btn-note">The online estimator prices kitchens only &mdash; wardrobes are quoted after the measurement</p>
				</div>
			</div>
		</div>
	</section>

	<!-- ==================== 7. THE OTHER TWO TYPES ==================== -->
	<section class="ax-section">
		<div class="ax-wrap ax-wrap--narrow">
			<div class="ax-head ax-reveal">
				<p class="ax-eyebrow">The three types</p>
				<h2 class="ax-h2">Compare it with the other two.</h2>
			</div>
			<div class="ax-spectable--rows ax-reveal">
				<?php
				/* Whole-row links, not an 11px inline text link: the row itself is
				   the tap target. Hrefs are built from $type_base — the same path
				   string page-wardrobes.php links to — so a link and its route
				   cannot drift apart. */
				foreach ( $alea_valid_types as $r_type ) :
					$r = $presentation[ $r_type ];
					if ( $r_type === $type ) :
					?>
				<div class="ax-spectable__row axp-sibrow">
					<span class="ax-spectable__key"><?php echo esc_html( $r['name'] ); ?> &mdash; this page</span>
					<span class="ax-spectable__val"><?php echo esc_html( $r['nav'] ); ?></span>
				</div>
					<?php else : ?>
				<a class="ax-spectable__row axp-sibrow axp-sibrow--link" href="<?php echo esc_url( home_url( $type_base . $r_type . '/' ) ); ?>">
					<span class="ax-spectable__key"><?php echo esc_html( $r['name'] ); ?> &mdash; see this type</span>
					<span class="ax-spectable__val"><?php echo esc_html( $r['nav'] ); ?></span>
				</a>
					<?php endif; ?>
				<?php endforeach; ?>
				<?php /* The hub link is a fourth full-width row, not an 11px uppercase
				         inline link: it inherits the same 52px row tap target as the
				         three above it, which is what the rest of this block promises. */ ?>
				<a class="ax-spectable__row axp-sibrow axp-sibrow--link" href="<?php echo esc_url( $hub_url ); ?>">
					<span class="ax-spectable__key">All ALEA wardrobes &mdash; the hub page</span>
					<span class="ax-spectable__val">The three types together, and how they are made</span>
				</a>
			</div>
		</div>
	</section>

	<!-- ==================== 8. EXPERIENCE CENTRE PLATE ==================== -->
	<!-- One photograph, captioned as exactly what it shows: the reception desk at
	     our own experience centre. It is atmosphere, NOT a wardrobe — the caption
	     says so in as many words. The library holds no wardrobe photograph, and no
	     verified customer-home or factory-floor photography, so none is claimed. -->
	<section class="ax-section ax-section--ruled ax-section--sheet">
		<div class="ax-wrap">
			<div class="ax-grid ax-grid--split">
				<!-- wardrobe photography pending: library has none -->
				<figure class="ax-media ax-media--43 ax-reveal">
					<span class="ax-media__frame">
						<?php echo alea_img( 'reception' ); ?>
						<span class="ax-media__tag">Experience centre</span>
					</span>
					<figcaption class="ax-media__caption">Our experience centre &mdash; the reception desk, not a wardrobe. We have no wardrobe photography to show you yet.</figcaption>
				</figure>
				<div class="ax-head ax-mb-0 ax-reveal">
					<p class="ax-eyebrow">The experience centre</p>
					<?php /* Per-type heading, same reason as section 4: no doors on a
					         walk-in, so it is not invited to open one. */ ?>
					<h2 class="ax-h2"><?php echo esc_html( $t['h_centre'] ); ?></h2>
					<?php /* $brands ("Hettich and Blum") in prose; $brands_amp is the
					         mono-label form and belongs in the spec strip only. */ ?>
					<p class="ax-lead ax-mt-4">
						Come and pull a drawer out to full extension and see the <?php echo esc_html( $brands ); ?>
						hinges and runners working, on our own floor, before you commit to a design.
					</p>
					<p class="ax-lead ax-mt-4"><?php echo esc_html( $photo_note ); ?></p>
					<div class="ax-btnrow ax-mt-5">
						<a class="ax-btn ax-btn--ghost" href="#alea-book">Book a free measurement &mdash; or just come and look</a>
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- ==================== 9. FAQ ==================== -->
	<section class="ax-section ax-section--ruled">
		<div class="ax-wrap ax-wrap--narrow">
			<div class="ax-head ax-reveal">
				<p class="ax-eyebrow">Questions</p>
				<h2 class="ax-h2">Asked about <?php echo esc_html( $type_short ); ?> wardrobes.</h2>
			</div>
			<div class="ax-faq ax-reveal">
				<?php foreach ( $faq as $faq_item ) : ?>
				<details class="ax-faq__item">
					<summary class="ax-faq__q"><?php echo esc_html( $faq_item['q'] ); ?></summary>
					<div class="ax-faq__a"><p><?php echo esc_html( $faq_item['a'] ); ?></p></div>
				</details>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<script type="application/ld+json"><?php echo wp_json_encode( $faq_schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ); ?></script>

	<!-- ==================== 10. CTA BAND + INLINE FORM ==================== -->
	<!-- TWO ids on purpose, both landing on the measurement form:
	     id="estimate" is the target of the theme's site-wide sticky mobile bar
	       (.aleac-mbar "Free Estimate" button, functions.php) — without it that
	       button is a dead link across the whole mobile experience;
	     id="alea-book" is the house convention every other ALEA template uses,
	       so cross-page links written as /wardrobe/sliding/#alea-book work. -->
	<section class="ax-section ax-section--ruled" id="estimate">
		<div class="ax-wrap" id="alea-book">
			<div class="ax-grid ax-grid--split">
				<div class="ax-reveal">
					<p class="ax-eyebrow">Book a free measurement</p>
					<h2 class="ax-h2">We measure, then we price it.</h2>
					<p class="ax-lead ax-mt-4">
						Someone comes to your home, measures the space and talks through what goes inside a
						<?php echo esc_html( $type_short ); ?> wardrobe there. It is free, it carries no obligation, and the
						measurements are yours to keep whoever you end up building with. We work across <?php echo esc_html( $areas ); ?>.
					</p>
					<div class="ax-btnrow ax-mt-5">
						<a class="ax-btn ax-btn--ink" href="<?php echo esc_url( $tel_href ); ?>">Call <?php echo esc_html( $phone_disp ); ?></a>
						<a class="ax-btn ax-btn--wa" href="<?php echo esc_url( $wa_href ); ?>">WhatsApp us</a>
					</div>
					<p class="ax-btn-note">Prefer to type? The form works too &mdash; we reply on the number you give</p>
				</div>
				<div class="ax-form ax-form--card ax-reveal">
					<h3 class="ax-form__title">Book your free measurement</h3>
					<p class="ax-form__note">Free / no obligation / <?php echo esc_html( implode( ' · ', $f['service_area'] ) ); ?></p>
					<?php echo do_shortcode( '[contact-form-7 id="7dcf010" title="Home Page form"]' ); ?>
					<p class="ax-form__fineprint ax-mt-4">
						Tell us it is a <?php echo esc_html( $type_short ); ?> wardrobe you are after and we will bring the
						right samples. We use your details only to arrange the visit.
					</p>
				</div>
			</div>
		</div>
	</section>

	<!-- Sticky mobile bar: provided site-wide by the theme (.aleac-mbar). Not
	     rebuilt here. Its "Free Estimate" button targets #estimate, which is the
	     CTA band above. Its Call and WhatsApp buttons are the theme's own. -->

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
