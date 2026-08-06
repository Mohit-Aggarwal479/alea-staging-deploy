<?php
/**
 * ALEA — Kitchen care ("The Manufacturer's Record")
 * Replaces /kitchen-care/. Included inside <main class="alea-main"> by the
 * shell; header, footer and the site-wide sticky mobile bar come from the theme.
 *
 * SINGLE-URL template: it serves one path only, so it reads no variant from
 * $GLOBALS['alea_page_args']. (Multi-URL siblings — page-collection.php —
 * whitelist their variant there.)
 *
 * ROUTING (outside this file): alea_redesign_map() in functions.php must carry
 * a '/kitchen-care/' entry pointing here, or the shell never includes this file
 * and the old page keeps serving.
 *
 * HONESTY NOTES — this is the one page whose whole body is general industry
 * guidance, which is legitimate BECAUSE it is framed as guidance:
 * - Everything below is care advice for a MATERIAL TYPE (laminate, acrylic,
 *   PU/matt, glass, stone and engineered quartz). Nothing here claims which
 *   material ALEA uses in which collection, on which shutter, or at what price
 *   — none of that is in facts.php. The copy repeatedly sends the reader to the
 *   finish named on their own quotation instead of guessing for them.
 * - NO measured claim is made anywhere: no "lasts N years with this routine",
 *   no cleaning frequency presented as an ALEA test result, no service SLA, no
 *   response time, no warranty coverage table. Where a limit would be a fact we
 *   cannot defend (drawer load ratings, sealing intervals), the page does NOT
 *   deflect it onto a document either — asserting what a quotation contains is
 *   just a second undefendable fact, and the site-wide convention only ever
 *   claims the quotation prices every panel, finish and fitting line by line.
 *   So the page names the limit as a limit and invites a call about it.
 * - WARRANTY: "10 years, in writing" and nothing more specific. What it covers
 *   lives in the written document handed over with the kitchen at installation.
 * - HARDWARE: Hettich and Blum are BOTH standard (facts.php). Neither is framed
 *   as an upgrade and no price difference between them is implied.
 * - No testimonials, ratings, project counts, staff counts, certifications,
 *   opening hours or addresses — none of those are verified.
 * - Photography comes only from images.php, which was built by VIEWING each
 *   file. Alt text is never written here. No frame is captioned as a customer's
 *   home, a factory floor or a wardrobe, because the library holds none.
 * - Service area is the four cities in facts.php. Legacy city pages elsewhere on
 *   the site are not linked and their coverage is not asserted.
 */

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/facts.php';
require_once __DIR__ . '/images.php';

$warranty_yrs = (int) alea_fact( 'warranty_years', 0 );   // 10
$brands       = implode( ' and ', alea_fact( 'hardware_brands', array() ) );
$areas        = implode( ', ', alea_fact( 'service_area', array() ) );
$place        = alea_fact( 'factory_place' );             // 'Raipur Rani, Panchkula district'
$phone_disp   = alea_fact( 'phone_display' );
$tel_href     = 'tel:' . alea_fact( 'phone_tel' );
$wa_href      = alea_wa_link( 'Hi ALEA, I have a question about looking after my kitchen.' );

$visit_url    = home_url( '/book-a-free-design-visit/' );
$warranty_url = home_url( '/warranty/' );
$kitchens_url = home_url( '/modular-kitchen/' );
$contact_url  = home_url( '/contact/' );

/* ---- The daily routine. Three habits, written as habits — not as a schedule
   we have measured, and not as a condition of anything. ---- */
$habits = array(
	array(
		'title' => 'Wipe it when it happens',
		'text'  => 'Nearly everything that damages a kitchen is water, oil or something acidic that was left sitting. A spill dealt with in the moment is a wipe; the same spill found the next morning is a stain, and at a panel joint it can become a swelling.',
	),
	array(
		'title' => 'Spray the cloth, not the shutter',
		'text'  => 'Spraying a cleaner straight at a cabinet puts liquid into the edges, the joints and the hinge cups, which is exactly where you do not want it. Dampen a soft cloth instead and work from the cloth.',
	),
	array(
		'title' => 'Finish dry',
		'text'  => 'A second pass with a dry, clean, soft cloth is the single most useful habit on this page. It takes off the film a damp cloth leaves behind and it stops water standing anywhere it can soak in.',
	),
);

/* ---- Care by material type. GENERAL GUIDANCE for each material, not a
   statement about which material is in which ALEA kitchen. Each block is
   h3 + a short paragraph + a do/avoid table rendered with .ax-spectable, which
   weights both columns equally — see the note at the render site. ---- */
$surfaces = array(
	array(
		'name' => 'Laminate',
		'lead' => 'The most forgiving of the finishes and the easiest to live with. Its weak point is not the face of the panel — it is the edges and joints, where water can find a way in and sit.',
		'do'   => array(
			'Warm water, a soft cloth and a drop of mild dishwashing liquid',
			'Dry the panel afterwards, edges and the underside of handles included',
			'A trivet under hot vessels rather than the panel or the edge',
		),
		'no'   => array(
			'Scouring pads, steel wool and scouring powders',
			'Bleach, thinner, acetone and nail-polish remover',
			'Water left standing along a joint, an edge or a cut-out',
		),
	),
	array(
		'name' => 'Acrylic and high-gloss',
		'lead' => 'A mirror finish shows everything, so the enemy here is grit rather than dirt. Most marks on a gloss shutter are put there by dust dragged across it on a dry or reused cloth, not by the cleaner.',
		'do'   => array(
			'A clean, soft microfibre cloth, rinsed often',
			'Mild soapy water, then a dry microfibre cloth in straight strokes',
			'Lift dust off first, so nothing gritty is dragged across the face',
		),
		'no'   => array(
			'Circular scrubbing, which is what leaves swirl marks',
			'Abrasive creams, powders and solvent-based polishes',
			'Reusing the cloth that just did the hob or the floor',
		),
	),
	array(
		'name' => 'PU and matt',
		'lead' => 'A matt surface hides fingerprints beautifully and shows patchiness instead. The trick is to clean the whole panel face rather than the one spot you noticed, so the finish stays even across it.',
		'do'   => array(
			'A soft damp cloth, blotting a mark before scrubbing at it',
			'Cleaning the whole panel face, corner to corner, then drying it',
			'Testing anything unfamiliar somewhere out of sight first',
		),
		'no'   => array(
			'Alcohol-based sprays, solvents and strong degreasers',
			'Abrasive creams or hard rubbing on one spot',
			'Wax or oily polish, which leaves shiny patches on a matt face',
		),
	),
	array(
		'name' => 'Glass',
		'lead' => 'Glass shutters and back-painted glass clean up quickly, but the coating and the silicone joints at the edges are less robust than the face. Work inward from the middle and keep liquid away from the perimeter.',
		'do'   => array(
			'A little warm soapy water, or a mild glass cleaner on the cloth',
			'A dry lint-free cloth to finish, so nothing dries in streaks',
			'A soft cloth for anything stuck on, given time to soften first',
		),
		'no'   => array(
			'Blades, scrapers or anything that can chip an edge',
			'Ammonia-heavy cleaner on back-painted or lacquered glass',
			'Spraying near the edges, seals and silicone joints',
		),
	),
	array(
		'name' => 'Stone and engineered quartz counters',
		'lead' => 'Counters take the acids: lemon, tamarind, vinegar, wine, turmeric and hot oil. Natural stone and engineered quartz are not the same material and are not looked after the same way — some natural stones want periodic sealing, engineered quartz generally does not.',
		'do'   => array(
			'Wipe acidic and oily spills promptly, then rinse and dry',
			'Mild soap and water for everyday cleaning',
			'A chopping board for cutting and a trivet under hot vessels',
		),
		'no'   => array(
			'Descalers, lime removers, oven cleaner and bleach',
			'Abrasive pads and powders on a polished surface',
			'A hot pan set straight down, or cutting on the stone itself',
		),
	),
);

/* ---- Moisture and ventilation. Indian-kitchen specific, and deliberately
   non-committal: habits and reasons, no frequencies presented as measured. ---- */
$moisture = array(
	array(
		'title' => 'Run the chimney early and late',
		'text'  => 'Switch it on before the pan gets hot and leave it running for a few minutes after you finish. The sticky film that builds up on shutters above a hob is mostly cooking oil that never got extracted. Clean the filters and the oil collector as often as the appliance manual asks.',
	),
	array(
		'title' => 'Treat the sink cabinet as the hard case',
		'text'  => 'It is the most hard-worked cabinet in the kitchen. Look under it now and then for a slow drip at the trap or the connections, dry the counter edge and the shutter below after washing up, and do not park a wet mop or a wet cloth inside it.',
	),
	array(
		'title' => 'Give the monsoon somewhere to go',
		'text'  => 'Air the kitchen for part of the day, wipe condensation off shutters rather than leaving it, and dry utensils before they go back in. Angle a pressure-cooker vent away from a shutter or a tall unit — a swollen edge is almost always steam or water that sat somewhere for a long time.',
	),
);

/* ---- Signs worth a call. Symptoms only: no diagnosis, no promised remedy and
   no repair timeline, because none of that is verified. ---- */
$signs = array(
	'A door that has drifted out of line with its neighbour, or one that rubs',
	'A drawer that no longer closes softly, or one that feels notchy on its run',
	'A swelling, a lifted edge or a dark line near the sink, the hob or a joint',
	'A handle, hinge or leg that has worked loose',
	'A shutter that no longer sits flush when it is closed',
);

/* ---- FAQ — the visible answers and the JSON-LD render from this ONE array, so
   the schema can never drift from the text on the page. ---- */
$faq = array(
	array(
		'q' => 'How should I clean my kitchen shutters day to day?',
		'a' => 'As general guidance for kitchen cabinetry: a soft damp cloth, warm water and a drop of mild dishwashing liquid handles almost everything, and a second pass with a dry cloth finishes the job. Dampen the cloth rather than spraying the shutter, so cleaner does not run into edges, joints and hinge cups. Keep scouring pads, scouring powders, bleach, thinner and acetone away from cabinetry altogether. Different finishes want slightly different handling — the finish on your own kitchen is named on your quotation, and if you are not sure which you have, ask us and we will tell you.',
	),
	array(
		'q' => 'Can I use a steam cleaner, a scouring pad or a magic eraser on my kitchen?',
		'a' => 'As general guidance, no to all three on cabinetry. A steam cleaner drives hot moisture into edges and joints, which is the one place a panel is vulnerable. A scouring pad and a melamine foam eraser both work by abrading the surface, so they take the finish off along with the mark — on a gloss or matt shutter that shows immediately. A soft cloth, mild soapy water and a little patience will get further, and anything unfamiliar is worth testing somewhere out of sight first.',
	),
	array(
		'q' => 'My drawer has stopped closing softly. What should I do?',
		/* Brands come from facts.php and are named as BOTH standard — never as an
		   upgrade, and with no price difference implied between them. */
		'a' => sprintf(
			'Start by clearing the runner track: crumbs and grit under a runner are the usual reason a smooth drawer starts to feel notchy or stops pulling itself shut. Brush or vacuum the track and wipe it with a dry cloth. Do not slam it and do not force it — a soft-close system works against you when it is pushed. Most household oils are the wrong answer too, because they collect dust. If it still does not close properly, call us rather than dismantling it: hinges and runners are adjustable and serviceable. ALEA fits %1$s hardware as standard.',
			$brands
		),
	),
	array(
		'q' => 'What does the ALEA warranty cover, and how do I use it?',
		/* "N years, in writing" and nothing more specific: the terms live in the
		   document handed over at installation. No coverage table, no exclusions. */
		'a' => sprintf(
			'Every ALEA kitchen carries a %1$d-year warranty, in writing. What it covers, and what it asks of you, is set out in the written document handed over with your kitchen at installation — that document is the authority, not this page. Keep it with your itemised quotation, which also names the finishes and hardware on your kitchen. If you have not received a kitchen from us yet, ask to read the warranty document on your free design visit, before you commit to anything. To raise something, call %2$s or message us on WhatsApp.',
			$warranty_yrs,
			$phone_disp
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
	/* Page-specific: the hero's secondary link and the per-material blocks.
	   Everything else is design-system. Colour comes from the token .ax-hero
	   already re-points to its on-ink muted value — never a raw hex. The link
	   is padded to clear the kit's 44px tap target: it is the only route from
	   above the fold to the phone/WhatsApp block. */
	.axp-hero-alt{margin-top:var(--sp-2);font-size:15px;line-height:1.6;color:var(--ax-fg-muted)}
	.axp-hero-alt a{color:inherit;display:inline-block;padding-block:10px}
	.axp-surf+.axp-surf{margin-top:var(--sp-7);padding-top:var(--sp-6);border-top:1px solid var(--ax-rule)}
	.axp-surf__lead{margin-top:var(--sp-3);max-width:62ch;font-size:15px;line-height:1.6;color:var(--ax-fg-muted);text-wrap:pretty}
</style>

	<!-- ================================================== 1. HERO -->
	<section class="ax-hero ax-hero--short ax-hero--flat">
		<?php
		/* 'kitchen-island' — a verified experience-centre photograph, catalogued as
		   hero grade. Alt comes from images.php, so it claims a display and never a
		   customer's home. */
		echo alea_img( 'kitchen-island', array( 'class' => 'ax-hero__img', 'eager' => true ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built and escaped by alea_img().
		?>
		<div class="ax-hero__inner">
			<p class="ax-eyebrow">Kitchen care</p>
			<h1 class="ax-hero__title">Looking after your kitchen.</h1>
			<p class="ax-hero__sub">
				Plain maintenance guidance &mdash; what to use, what to keep away from a finish, and when a
				small thing is worth a phone call. Written as general guidance for kitchen materials, not as a
				condition of anything.
			</p>
			<div class="ax-hero__actions">
				<a class="ax-btn ax-btn--primary ax-btn--lg" href="#alea-book">Book a free design visit</a>
			</div>
			<p class="axp-hero-alt">or <a href="#alea-call">ask us about your own kitchen</a> &mdash; free, no obligation</p>
			<p class="ax-hero__credit">
				GENERAL CARE GUIDANCE
				/ <?php echo (int) $warranty_yrs; ?>-YEAR WRITTEN WARRANTY
				/ <?php echo esc_html( $brands ); ?> HARDWARE AS STANDARD
			</p>
		</div>
	</section>

	<!-- ================================================== 2. DAILY CARE -->
	<section class="ax-section">
		<div class="ax-wrap ax-wrap--narrow">
			<header class="ax-head ax-head--wide ax-reveal">
				<p class="ax-eyebrow">Every day</p>
				<h2 class="ax-h2">Three habits do most of the work.</h2>
				<p class="ax-lead">
					A kitchen is not kept by deep cleaning it twice a year. It is kept by what happens in the
					thirty seconds after something spills.
				</p>
			</header>

			<div class="ax-steps ax-reveal">
				<?php foreach ( $habits as $h ) : ?>
				<div class="ax-step">
					<div class="ax-step__body">
						<h3 class="ax-step__title"><?php echo esc_html( $h['title'] ); ?></h3>
						<p class="ax-step__text"><?php echo esc_html( $h['text'] ); ?></p>
					</div>
				</div>
				<?php endforeach; ?>
			</div>

			<?php /* General guidance about materials, deliberately not framed as an ALEA
			         test result and not tied to any particular collection. */ ?>
			<div class="ax-prose ax-mt-7 ax-reveal">
				<h3 class="ax-h3">What does not belong near cabinetry</h3>
				<p>
					This holds for kitchen furniture generally, whatever the finish. If you are dealing with
					something stubborn, more time with a soft cloth beats more force with a harder one.
				</p>
				<ul>
					<li>Scouring pads, steel wool and scouring powders &mdash; they abrade the finish along with the mark.</li>
					<li>Bleach, thinner, acetone, nail-polish remover and strong degreasers.</li>
					<li>Melamine foam &ldquo;magic&rdquo; erasers, which work by abrasion even though they feel soft.</li>
					<li>Steam cleaners, which push hot moisture into edges and joints.</li>
					<li>Water left standing anywhere &mdash; a joint, an edge, a cut-out, the counter lip above a shutter.</li>
					<li>A wet cloth hung over a shutter or a drawer front to dry.</li>
				</ul>
			</div>
		</div>
	</section>

	<!-- ================================================== 3. BY SURFACE -->
	<!-- General guidance for each MATERIAL TYPE. Nothing here says which material
	     is used in which ALEA collection, or on which shutter — that is not in
	     facts.php, and the reader's own quotation names it. -->
	<section class="ax-section ax-section--ruled">
		<div class="ax-wrap">
			<div class="ax-grid ax-grid--split">
				<header class="ax-head ax-head--wide ax-mb-0 ax-reveal">
					<p class="ax-eyebrow">By surface</p>
					<h2 class="ax-h2">Different finishes, different handling.</h2>
					<p class="ax-lead ax-mt-4">
						What follows is general guidance for each material type, not a statement about what is on
						your kitchen. The finishes actually fitted to yours are named, line by line, on your itemised
						quotation &mdash; and if you are not sure which you have, ask us and we will tell you.
					</p>
				</header>
				<?php
				$plate_stone = alea_img( 'stone-desk' );
				if ( $plate_stone ) :
					?>
				<figure class="ax-media ax-media--43 ax-reveal">
					<span class="ax-media__frame">
						<?php echo $plate_stone; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built and escaped by alea_img(). ?>
						<span class="ax-media__tag">Display</span>
					</span>
					<figcaption class="ax-media__caption">
						Sculpted stone counter / ALEA experience centre &mdash;
						<b>come and see the finishes in person</b>
					</figcaption>
				</figure>
				<?php endif; ?>
			</div>

			<div class="ax-mt-7">
				<?php
				/* Do / Avoid as .ax-spectable, NOT .ax-included. On a care page the
				   "Avoid" column is the higher-stakes half — solvents, blades, bleach —
				   and .ax-included deliberately renders its second column in muted
				   graphite behind a neutral em rule, which would rank the warnings
				   BELOW the benign advice. .ax-spectable gives both columns the same
				   type colour and weight. It also keeps the olive inside its <=2%
				   budget: .ax-included--in would have put a tick on all fifteen rows. */
				foreach ( $surfaces as $s ) :
					$do_list  = array_values( $s['do'] );
					$no_list  = array_values( $s['no'] );
					$row_qty  = max( count( $do_list ), count( $no_list ) );
					?>
				<div class="axp-surf ax-reveal">
					<h3 class="ax-h3"><?php echo esc_html( $s['name'] ); ?></h3>
					<p class="axp-surf__lead"><?php echo esc_html( $s['lead'] ); ?></p>
					<div class="ax-scroll-x ax-mt-5">
						<table class="ax-spectable">
							<caption>General guidance &mdash; <?php echo esc_html( $s['name'] ); ?></caption>
							<thead>
								<tr>
									<th scope="col">Do</th>
									<th scope="col">Avoid</th>
								</tr>
							</thead>
							<tbody>
								<?php for ( $i = 0; $i < $row_qty; $i++ ) : ?>
								<tr>
									<td class="ax-spectable__wrap"><?php echo isset( $do_list[ $i ] ) ? esc_html( $do_list[ $i ] ) : ''; ?></td>
									<td class="ax-spectable__wrap"><?php echo isset( $no_list[ $i ] ) ? esc_html( $no_list[ $i ] ) : ''; ?></td>
								</tr>
								<?php endfor; ?>
							</tbody>
						</table>
					</div>
				</div>
				<?php endforeach; ?>
			</div>

			<p class="ax-prose ax-mt-6 ax-reveal">
				One rule covers all five: try anything unfamiliar somewhere it will not be seen &mdash; the inside
				of a shutter, the back of a cabinet door &mdash; before you take it to the front of your kitchen.
			</p>
		</div>
	</section>

	<!-- ================================================== 4. HARDWARE -->
	<section class="ax-section ax-section--ruled ax-section--sheet">
		<div class="ax-wrap">
			<div class="ax-grid ax-grid--split ax-grid--split-reverse">
				<?php
				$plate_acc = alea_img( 'accessories' );
				if ( $plate_acc ) :
					?>
				<figure class="ax-media ax-media--43 ax-reveal">
					<span class="ax-media__frame">
						<?php echo $plate_acc; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built and escaped by alea_img(). ?>
						<span class="ax-media__tag">Display</span>
					</span>
					<figcaption class="ax-media__caption">
						Shelving and accessories display / ALEA experience centre
					</figcaption>
				</figure>
				<?php endif; ?>
				<div class="ax-reveal">
					<div class="ax-head ax-mb-0">
						<p class="ax-eyebrow">Hinges and runners</p>
						<h2 class="ax-h2">Hardware asks for very little. Mostly, a clear track.</h2>
						<p class="ax-lead ax-mt-4">
							<?php echo esc_html( $brands ); ?> hardware is fitted as standard on every ALEA kitchen.
							The guidance below is general to soft-close hinges and runners of that kind.
						</p>
					</div>
					<div class="ax-prose ax-mt-5">
						<ul>
							<li>Keep the runner tracks clear. Crumbs and grit under a runner are the usual reason a smooth drawer starts to feel notchy &mdash; brush or vacuum the track, then wipe it with a dry cloth.</li>
							<li>Wipe hinges dry and do not let cleaner sit in the hinge cup.</li>
							<li>Do not oil hinges or runners as a matter of habit. Soft-close systems are lubricated where they need to be, and household oil mostly collects dust.</li>
							<li>Let a soft-close door or drawer close itself. Pushing it shut works against the damper, and over time the damper is what gives.</li>
							<?php /* A runner's load limit is a real limit, but WHAT that limit is on a
							         given kitchen is not in facts.php — and neither is what an ALEA
							         quotation lists, beyond the line-by-line pricing of every panel,
							         finish and fitting. So: name the limit, invite the call, assert
							         nothing about a document. */ ?>
							<li>Load drawers low and even, heavy things nearest the sides rather than piled at the front. Runners have a load limit &mdash; if you are about to load a drawer heavily, ask us what yours will take rather than guessing at it.</li>
							<li>If a door has drifted out of line, the hinge is adjustable &mdash; that is a call to us, not a job for force.</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- ================================================== 5. MOISTURE AND AIR -->
	<section class="ax-section ax-section--ruled">
		<div class="ax-wrap">
			<header class="ax-head ax-head--wide ax-reveal">
				<p class="ax-eyebrow">Moisture and air</p>
				<h2 class="ax-h2">In an Indian kitchen, steam and oil do more damage than dirt.</h2>
				<p class="ax-lead">
					Frying, pressure cooking and a humid monsoon put more moisture and more airborne oil into a
					kitchen than most cabinetry is asked to deal with anywhere else. General guidance, and the
					reasoning behind it, so you can judge your own kitchen.
				</p>
			</header>
			<div class="ax-grid ax-grid--3">
				<?php foreach ( $moisture as $m ) : ?>
				<article class="ax-card ax-reveal">
					<h3 class="ax-card__title"><?php echo esc_html( $m['title'] ); ?></h3>
					<p class="ax-card__body"><?php echo esc_html( $m['text'] ); ?></p>
				</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<!-- ================================================== 6. WHEN TO CALL US -->
	<section class="ax-section ax-section--ruled" id="alea-call">
		<div class="ax-wrap">
			<div class="ax-grid ax-grid--split">
				<div class="ax-reveal">
					<div class="ax-head ax-mb-0">
						<p class="ax-eyebrow">When to call us</p>
						<h2 class="ax-h2">Early is cheaper than later, and the call costs nothing.</h2>
						<p class="ax-lead ax-mt-4">
							Small misalignments are easy to put right and easy to ignore until they are not. If you
							see any of these, tell us rather than living with it &mdash; and rather than forcing it.
						</p>
					</div>
					<div class="ax-prose ax-mt-5">
						<ul>
							<?php foreach ( $signs as $sign ) : ?>
							<li><?php echo esc_html( $sign ); ?></li>
							<?php endforeach; ?>
						</ul>
					</div>
					<div class="ax-btnrow ax-mt-5">
						<a class="ax-btn ax-btn--ink" href="<?php echo esc_url( $tel_href ); ?>">Call <?php echo esc_html( $phone_disp ); ?></a>
						<a class="ax-btn ax-btn--wa" href="<?php echo esc_url( $wa_href ); ?>">WhatsApp us</a>
					</div>
					<p class="ax-btn-note">Tell us what you are seeing and where &mdash; a photograph on WhatsApp usually saves a conversation.</p>
				</div>
				<div class="ax-reveal">
					<div class="ax-spectable--rows">
						<div class="ax-spectable__row">
							<span class="ax-spectable__key">Phone</span>
							<span class="ax-spectable__val"><?php echo esc_html( $phone_disp ); ?></span>
						</div>
						<div class="ax-spectable__row">
							<span class="ax-spectable__key">WhatsApp</span>
							<span class="ax-spectable__val"><?php echo esc_html( $phone_disp ); ?></span>
						</div>
						<div class="ax-spectable__row">
							<span class="ax-spectable__key">Warranty</span>
							<span class="ax-spectable__val"><?php echo (int) $warranty_yrs; ?> years, in writing</span>
						</div>
						<div class="ax-spectable__row">
							<span class="ax-spectable__key">We work across</span>
							<span class="ax-spectable__val"><?php echo esc_html( $areas ); ?></span>
						</div>
					</div>
					<?php /* WARRANTY: the fact is "10 years, in writing" and nothing more.
					         The terms live in the document handed over at installation —
					         no coverage table, no exclusions list, no service promise. */ ?>
					<p class="ax-prose ax-mt-5">
						Every ALEA kitchen carries a <?php echo (int) $warranty_yrs; ?>-year warranty, in writing. What
						it covers &mdash; and what it asks of you &mdash; is set out in the written document handed over
						with your kitchen at installation, and that document is the authority rather than this page. Keep
						it with your itemised quotation, which names the finishes and the hardware on your own kitchen.
						<a class="ax-link" href="<?php echo esc_url( $warranty_url ); ?>">More about the warranty</a>
					</p>
					<p class="ax-prose ax-mt-3">
						Not an ALEA customer yet? Ask to read the warranty document on your
						<a class="ax-link" href="<?php echo esc_url( $visit_url ); ?>">free design visit</a>, before you
						commit to anything, and have a look at
						<a class="ax-link" href="<?php echo esc_url( $kitchens_url ); ?>">what we make</a> while you are at it.
					</p>
				</div>
			</div>
		</div>
	</section>

	<!-- ================================================== 7. FAQ -->
	<section class="ax-section ax-section--ruled">
		<div class="ax-wrap ax-wrap--narrow">
			<header class="ax-head ax-reveal">
				<p class="ax-eyebrow">Questions</p>
				<h2 class="ax-h2">Asked after the kitchen is in.</h2>
			</header>
			<div class="ax-faq ax-reveal">
				<?php foreach ( $faq as $item ) : ?>
				<details class="ax-faq__item">
					<summary class="ax-faq__q"><?php echo esc_html( $item['q'] ); ?></summary>
					<div class="ax-faq__a"><p><?php echo esc_html( $item['a'] ); ?></p></div>
				</details>
				<?php endforeach; ?>
			</div>
			<p class="ax-btn-note">Something not answered here? <a class="ax-link" href="<?php echo esc_url( $contact_url ); ?>">Get in touch</a> &mdash; we would rather you asked than guessed.</p>
		</div>
	</section>
	<script type="application/ld+json"><?php echo wp_json_encode( $faq_schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ); ?></script>

	<!-- id="estimate": landing anchor for the theme's site-wide sticky mobile bar
	     (.aleac-mbar "Free Estimate" button in functions.php). -->
	<span id="estimate" class="ax-sr-only"></span>

	<!-- ================================================== 8. BOOK A FREE VISIT -->
	<section class="ax-section ax-section--ruled" id="alea-book">
		<div class="ax-wrap">
			<div class="ax-grid ax-grid--split">
				<div class="ax-reveal">
					<p class="ax-eyebrow">Book a free design visit</p>
					<h2 class="ax-h2">Planning a new kitchen, or asking about the one you have?</h2>
					<p class="ax-lead ax-mt-4">
						Both visits are free and carry no obligation: a measurement at your home across
						<?php echo esc_html( $areas ); ?>, or a factory visit at <?php echo esc_html( $place ); ?>
						where you can watch kitchens being built before you pay a rupee. Bring your questions about
						finishes and hardware &mdash; that is the easiest way to settle them.
					</p>
					<div class="ax-btnrow ax-mt-5">
						<a class="ax-btn ax-btn--ink" href="<?php echo esc_url( $tel_href ); ?>">Call <?php echo esc_html( $phone_disp ); ?></a>
						<a class="ax-btn ax-btn--wa" href="<?php echo esc_url( $wa_href ); ?>">WhatsApp us</a>
					</div>
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
