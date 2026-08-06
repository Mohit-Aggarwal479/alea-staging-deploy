<?php
/**
 * ALEA — Manufacturing process ("The Manufacturer's Record")
 * Replaces /about/manufacturing-process/. Included inside <main class="alea-main">
 * by the shell; header, footer and the site-wide sticky mobile bar come from the
 * theme, so no navigation is built here.
 *
 * SINGLE-URL template: it serves one path only, so it reads no variant from
 * $GLOBALS['alea_page_args'].
 *
 * SIBLING, NOT A DUPLICATE: /our-factory/ is about the PLACE — where it is, what
 * a free visit is, how to find it. This page is about the PROCESS — what happens
 * to a sheet of board between your signed-off drawing and your fitted kitchen.
 * It links to /our-factory/ prominently rather than repeating it: the visit
 * mechanics, the directions, the "what will I see" copy AND the hardware
 * prooflist all stay over there. This page carries no hardware section of its
 * own; hardware appears only where the sequence actually touches it (stages 4
 * and 5), which is the process fact, not a second sales pitch for the brands.
 *
 * THE SEQUENCE IS NOT DEFINED HERE. It lives in sequence.php and /our-factory/
 * reads the same array, because when each page held its own copy they drifted:
 * one headline said "Five stages" while the other rendered count() over six, and
 * a reader one click away got two official answers. One array, one count, one
 * wording. This page adds the 'means' rider — what each stage changes for the
 * buyer — which is the layer /our-factory/ deliberately does not carry.
 *
 * HONESTY NOTES — a process page is the easiest place on a website to invent
 * engineering that nobody can check, so this file refuses the usual temptations:
 * - NO machine names, machine counts, brand names of plant, line speeds or
 *   capacity figures. facts.php records none of that, and a reader cannot
 *   verify any of it from outside the building.
 * - NO quality-check counts ("38-point check"), NO staff numbers, NO project
 *   counts, NO certifications, NO awards. facts.php lists all of those under
 *   'unverified' precisely so they never render.
 * - The stages are described as GENERAL manufacturing practice in plain
 *   language, which is what a homeowner actually needs. Nothing here is dressed
 *   up as a measured ALEA-specific claim; the ALEA-specific claims on this page
 *   are only the ones facts.php carries (own factory, its size and place, the
 *   hardware brands, the installation figure, the written warranty).
 * - install_days is recorded in facts.php as a BARE figure with no anchor — it
 *   is not stated there whether 15 means days of fitting at the customer's home
 *   or the whole order-to-installed lead time, and page-home.php reads it the
 *   other way round. So this page uses the site's least-committal phrasing,
 *   "installation at your home takes about N days", the same wording
 *   page-about.php, page-kitchens.php and page-customer-process.php use. It
 *   deliberately does NOT anchor the figure to dispatch or to approval: that
 *   anchor would be an inference, and inventing it here would make this page
 *   the most specific — and least supported — statement of it on the site.
 *   If the owner confirms which it is, record it in facts.php as an explicit
 *   key ('install_days_onsite' vs 'lead_days_total') and the anchor can be
 *   printed everywhere at once.
 * - NO PHOTOGRAPHS OF THE FACTORY FLOOR EXIST. Every picture here comes from
 *   images.php and is captioned as the experience centre, never as the shop
 *   floor and never as a customer's home. Alt text is never written in this
 *   file — it travels with the file in the catalogue.
 * - Factory is at Raipur Rani, in Panchkula DISTRICT — never "Panchkula city".
 * - Hettich and Blum are both standard. Neither is framed as a paid upgrade and
 *   no price difference between them is implied, because none is verified.
 * - Warranty is "10 years, in writing" and nothing more specific; what it
 *   covers lives in the written document the customer receives.
 */

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/facts.php';
require_once __DIR__ . '/images.php';
require_once __DIR__ . '/sequence.php';

/* Every value below comes through alea_fact(), which returns '' for anything
   facts.php marks unverified. alea_facts() is deliberately NOT held in a local
   array here: reaching into the raw array bypasses that guard. */
$sqft         = alea_fact( 'factory_sqft' );             // '95,000'
$place        = alea_fact( 'factory_place' );            // 'Raipur Rani, Panchkula district'
$since_alea   = alea_fact( 'alea_since' );               // '2009'
$since_parent = alea_fact( 'founded_furniture' );        // '1998'
$years        = alea_fact( 'years_experience' );         // DERIVED from alea_since — never a literal
$warranty_yrs = (int) alea_fact( 'warranty_years', 0 );  // 10
$install_days = (int) alea_fact( 'install_days', 0 );    // 15
$phone_disp   = alea_fact( 'phone_display' );
$tel_href     = 'tel:' . alea_fact( 'phone_tel' );
$wa_href      = alea_wa_link( 'Hi ALEA, I have a question about how you manufacture your kitchens.' );

$brands       = alea_fact( 'hardware_brands', array() ); // Hettich, Blum
$brands_txt   = implode( ' and ', $brands );
$areas        = alea_fact( 'service_area', array() );
$areas_txt    = implode( ', ', $areas );

/* The spec strip wants the town and the district on separate lines. Both are
   SPLIT OUT OF the fact rather than typed, so if facts.php's factory_place is
   ever corrected this cell moves with it. This is the one fact the site carries
   a standing rule about — Raipur Rani is in Panchkula DISTRICT, never Panchkula
   city — so it must never be a literal string in a template. */
$place_bits   = array_map( 'trim', explode( ',', (string) $place, 2 ) );
$place_town   = isset( $place_bits[0] ) ? $place_bits[0] : '';
$place_dist   = isset( $place_bits[1] ) ? $place_bits[1] : '';

$factory_url  = home_url( '/our-factory/' );
$visit_url    = home_url( '/book-a-free-design-visit/' );
$warranty_url = home_url( '/warranty/' );
$calc_url     = home_url( '/kitchen-cost-calculator/' );
$kitchens_url = home_url( '/modular-kitchen/' );

/* ---- The sequence comes from sequence.php, which /our-factory/ also reads, so
   the two pages can never print different stage counts or different wording for
   the same stage again. Nothing about the sequence is defined in this file.
   $stage_count is used for both the spec-strip cell and the section headline,
   so neither can be typed out of step with the list rendered below. ---- */
$stages      = alea_sequence();
$stage_count = alea_sequence_count();
$stage_word  = alea_sequence_count_word( $stage_count );

/* ---- What being made in-house changes. Every line is either a facts.php value
   or something a reader can settle by walking into the building. No claim is
   made about how any other company operates: facts.php holds no competitor
   data, and a claim about somebody else's workshop is not checkable. ---- */
$changes = array(
	array(
		'text'  => 'The company that quotes you is the company that cuts, edges, assembles and finishes your kitchen in our own ' . $sqft . ' sq ft unit at ' . $place . ', installs it, and answers the phone afterwards.',
		'never' => 'Never placed with an outside workshop',
	),
	array(
		/* install_days, unanchored — see the note in the file header. The figure
		   is printed the way the rest of the site prints it and nothing is added
		   about what it is measured from, because facts.php does not say. */
		'text'  => 'The manufacturing queue is ours, so the dates on your quotation are ours to keep. Installation at your home takes about ' . $install_days . ' days, and the dates for your kitchen are confirmed with you in writing.',
		'never' => '',
	),
	array(
		'text'  => 'You can come and watch. A factory visit is free and carries no obligation, so the whole sequence above is something you can check with your own eyes — including opening and closing the ' . $brands_txt . ' hinges and runners from our own stock — before you spend a rupee.',
		'never' => 'Never a closed door',
	),
);

/* ---- FAQ: the visible answers and the JSON-LD render from this ONE array, so
   the schema can never drift from the text on the page. ---- */
$faq = array(
	array(
		'q' => 'Do you manufacture the kitchens yourselves, or subcontract them?',
		'a' => sprintf(
			'We manufacture. Every ALEA kitchen is made at our own %1$s sq ft unit at %2$s rather than placed with an outside workshop, and has been since %3$s. You do not have to take that on trust: a factory visit is free and carries no obligation, so you can watch kitchens being made before you commit to anything.',
			$sqft,
			$place,
			$since_alea
		),
	),
	array(
		'q' => 'What happens before anything is cut?',
		'a' => 'Your kitchen is measured at your home, drawn up, and quoted line by line rather than as one lump sum, and you sign the drawing off. The factory works from that signed drawing, so the panels are cut to your room rather than to a standard assumption about it. The measurement visit is free and carries no obligation.',
	),
	array(
		/* The answer stays on the question. An earlier draft ended on the
		   hardware brands, which are not something you get in writing — and
		   because this array also feeds the JSON-LD, the drift would have
		   shipped into the FAQPage entry too. */
		'q' => 'What do I get in writing at the end of the process?',
		'a' => sprintf(
			'An itemised quotation, and a %1$d-year warranty in writing, handed over as a document rather than promised verbally. What that warranty covers is set out in the written document you receive — ask to read it on your free visit, before you commit to anything.',
			$warranty_yrs
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
<div class="ax-root">

	<style data-no-optimize="1">
	/* Page-specific: the hero's secondary text link and the mono "what it means
	   for you" rider under each manufacturing stage. The "For you" label is real
	   markup (.axp-means__k), never CSS content: it is copy the reader is meant
	   to read, so it has to be selectable, translatable and present in the DOM. */
	.axp-hero-alt{margin-top:var(--sp-3);font-size:15px;color:#E4E1DC}
	.axp-hero-alt a{color:inherit}
	.axp-means{display:block;margin-top:var(--sp-3);padding-top:var(--sp-2);border-top:1px solid var(--ax-rule);max-width:56ch;font-family:var(--font-mono);font-size:var(--fs-micro);line-height:1.5;letter-spacing:var(--ls-mono);text-transform:uppercase;color:var(--ax-fg)}
	.axp-means__k{margin-right:.6em;color:var(--ax-fg-muted)}
	</style>

	<!-- ================================================== 1. HERO -->
	<section class="ax-hero ax-hero--short">
		<?php
		/* 'kitchen-island' — a verified experience-centre photograph. The
		   catalogue holds NO shop-floor photography, so the credit line says
		   plainly what this frame is, and the headline makes no locative claim
		   about the picture. Alt comes from images.php. */
		echo alea_img( 'kitchen-island', array( 'class' => 'ax-hero__img', 'eager' => true ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built and escaped by alea_img().
		?>
		<div class="ax-hero__inner">
			<p class="ax-eyebrow">How we manufacture</p>
			<h1 class="ax-hero__title">Made by us, in our own unit.</h1>
			<p class="ax-hero__sub">
				Every ALEA kitchen is manufactured at our own <?php echo esc_html( $sqft ); ?> sq ft unit at
				<?php echo esc_html( $place ); ?> &mdash; not ordered in from a workshop we do not own.
				This page is the sequence your kitchen goes through, in plain language.
			</p>
			<div class="ax-hero__actions">
				<a class="ax-btn ax-btn--primary ax-btn--lg" href="#alea-book">Book a free design visit</a>
				<a class="ax-btn ax-btn--ghost ax-btn--lg" href="<?php echo esc_url( $factory_url ); ?>">See inside the factory</a>
			</div>
			<p class="axp-hero-alt">Free, no obligation &mdash; or <a href="<?php echo esc_url( $calc_url ); ?>">get a price first</a></p>
			<p class="ax-hero__credit">Photograph: a kitchen display at our experience centre &mdash; not the shop floor</p>
		</div>
	</section>

	<!-- ================================================== 2. SPEC STRIP -->
	<!-- Four values. Three come from facts.php (the place, split into town and
	     district; the unit size; the warranty term) and the fourth is count() of
	     the shared sequence in sequence.php. NOTHING in this strip is a typed
	     literal, so a correction in facts.php moves every cell with it. -->
	<section class="ax-section ax-section--flush" aria-label="Manufacturing facts">
		<div class="ax-wrap ax-wrap--flush">
			<div class="ax-specstrip">
				<div class="ax-specstrip__item">
					<span class="ax-specstrip__label">Made at</span>
					<span class="ax-specstrip__value"><?php echo esc_html( $place_town ); ?><span class="ax-specstrip__unit"><?php echo esc_html( $place_dist ); ?></span></span>
				</div>
				<div class="ax-specstrip__item">
					<span class="ax-specstrip__label">Own unit</span>
					<span class="ax-specstrip__value"><?php echo esc_html( $sqft ); ?><span class="ax-specstrip__unit">sq ft</span></span>
				</div>
				<div class="ax-specstrip__item">
					<span class="ax-specstrip__label">The sequence</span>
					<span class="ax-specstrip__value"><?php echo (int) $stage_count; ?><span class="ax-specstrip__unit">stages</span></span>
				</div>
				<div class="ax-specstrip__item">
					<span class="ax-specstrip__label">Warranty</span>
					<span class="ax-specstrip__value"><?php echo (int) $warranty_yrs; ?><span class="ax-specstrip__unit">years, in writing</span></span>
				</div>
			</div>
		</div>
	</section>

	<!-- ================================================== 3. INTRO -->
	<section class="ax-section ax-section--ruled">
		<div class="ax-wrap ax-wrap--text">
			<header class="ax-head ax-reveal">
				<p class="ax-eyebrow">Why this page exists</p>
				<h2 class="ax-h2">Your kitchen is made by us, not bought in.</h2>
			</header>
			<div class="ax-prose ax-reveal">
				<p>
					A modular kitchen can be sold by a company that never touches it. The order is taken in a showroom
					and placed with a workshop somewhere else, and by the time something is wrong it is not obvious who
					owns the problem. We work the other way round: ALEA has been making modular kitchens since
					<?php echo esc_html( $since_alea ); ?> &mdash; <?php echo esc_html( $years ); ?> years &mdash; in our own
					<?php echo esc_html( $sqft ); ?> sq ft unit at <?php echo esc_html( $place ); ?>, and the furniture
					business we grew out of has been making furniture since <?php echo esc_html( $since_parent ); ?>.
				</p>
				<p>
					So this page is not a brochure about machinery. It is the sequence a sheet of board actually goes
					through between your signed-off drawing and your fitted kitchen, written the way we would explain
					it standing next to you &mdash; and every stage of it is something you are welcome to come and watch.
				</p>
				<p class="ax-mono--label">
					A note on the photographs: documentary photography of the shop floor does not exist yet. Every
					picture on this page was taken in our own experience centre and is captioned as exactly that.
				</p>
			</div>
		</div>
	</section>

	<!-- ================================================== 4. THE SEQUENCE -->
	<!-- The stages come from sequence.php, which /our-factory/ reads too, and the
	     headline count is that array's count() rather than a typed word — so the
	     two pages cannot disagree about how many stages there are. No machine
	     names, no counts, no timings per stage — none of that is verified. -->
	<section class="ax-section ax-section--ruled">
		<div class="ax-wrap">
			<header class="ax-head ax-head--wide ax-reveal">
				<p class="ax-eyebrow">The sequence</p>
				<h2 class="ax-h2"><?php echo esc_html( $stage_word ); ?> stages, from signed drawing to dispatch.</h2>
				<p class="ax-lead">
					No mystique and no jargon. This is what happens to your kitchen, and what each stage changes
					for you once you are living with it.
				</p>
			</header>
			<ol class="ax-steps ax-reveal">
				<?php foreach ( $stages as $stage ) : ?>
				<li class="ax-step">
					<div class="ax-step__body">
						<h3 class="ax-step__title"><?php echo esc_html( $stage['title'] ); ?></h3>
						<p class="ax-step__text"><?php echo esc_html( $stage['text'] ); ?></p>
						<span class="axp-means"><span class="axp-means__k">For you</span> <?php echo esc_html( $stage['means'] ); ?></span>
					</div>
				</li>
				<?php endforeach; ?>
			</ol>
			<?php /* install_days printed with no anchor — facts.php does not record
			         whether the 15 is on-site fitting or the whole lead time, so
			         this page does not decide it. See the file header. */ ?>
			<p class="ax-btn-note">
				Installation at your home takes about <?php echo (int) $install_days; ?> days.
				<a class="ax-link" href="<?php echo esc_url( $kitchens_url ); ?>">See what a kitchen includes</a>
			</p>
		</div>
	</section>

	<!-- ================================================== 5. WHAT IN-HOUSE CHANGES -->
	<section class="ax-section ax-section--ruled ax-section--sheet">
		<div class="ax-wrap">
			<div class="ax-grid ax-grid--split">
				<div class="ax-reveal">
					<div class="ax-head ax-mb-0">
						<p class="ax-eyebrow">What being made in-house changes</p>
						<h2 class="ax-h2">One roof, one company to answer for it.</h2>
						<p class="ax-lead ax-mt-4">
							Making it ourselves is not a slogan &mdash; it changes three practical things: who controls the
							quality, who controls the dates, and whether you are allowed to come and look.
						</p>
					</div>
					<div class="ax-btnrow ax-mt-6">
						<a class="ax-btn ax-btn--ghost" href="<?php echo esc_url( $factory_url ); ?>">See inside the factory</a>
						<a class="ax-btn ax-btn--ghost" href="<?php echo esc_url( $visit_url ); ?>">Book a free design visit</a>
					</div>
					<p class="ax-btn-note">Both visits are free and carry no obligation.</p>
				</div>
				<div class="ax-reveal">
					<ul class="ax-prooflist">
						<?php foreach ( $changes as $change ) : ?>
						<li class="ax-proof">
							<span class="ax-proof__tick" aria-hidden="true"></span>
							<div class="ax-proof__text">
								<?php echo esc_html( $change['text'] ); ?>
								<?php if ( $change['never'] ) : ?>
								<span class="ax-proof__never"><?php echo esc_html( $change['never'] ); ?></span>
								<?php endif; ?>
							</div>
						</li>
						<?php endforeach; ?>
					</ul>
				</div>
			</div>
		</div>
	</section>

	<!-- Section 6 (a standalone Hettich/Blum prooflist) was REMOVED. /our-factory/
	     already carries it, this page links there twice, and two hardware pitches
	     one click apart is exactly the duplication the PLACE-vs-PROCESS split
	     exists to avoid. The brands still appear here where the PROCESS actually
	     touches them - stages 4 and 5, sourced from facts.php via sequence.php -
	     and the prooflist above sends the reader to handle them on a free visit. -->

	<!-- ================================================== 6. THE END OF THE PROCESS -->
	<section class="ax-section ax-section--ruled ax-section--timber">
		<div class="ax-wrap">
			<div class="ax-grid ax-grid--split">
				<div class="ax-reveal">
					<div class="ax-head ax-mb-0">
						<p class="ax-eyebrow">The end of the process</p>
						<h2 class="ax-h2">The last stage is a document.</h2>
						<p class="ax-lead ax-mt-4">
							A process that ends with a handshake is a process you cannot hold anybody to. Ours ends with a
							<?php echo (int) $warranty_yrs; ?>-year warranty, in writing, handed over with your kitchen &mdash;
							so that years from now you are holding a document rather than remembering a conversation.
						</p>
					</div>
					<div class="ax-prose ax-mt-5">
						<p>
							What the warranty covers is set out in the written document you receive. Ask to read it on your
							free visit, before you commit to anything &mdash; we would rather you read it early than
							discover it late.
						</p>
					</div>
					<div class="ax-btnrow ax-mt-5">
						<a class="ax-btn ax-btn--ink" href="<?php echo esc_url( $warranty_url ); ?>">What the warranty covers</a>
					</div>
				</div>
				<?php /* Experience-centre frame, captioned as exactly what it is. The
				         catalogue holds no shop-floor and no customer-home photography,
				         so neither is claimed here. Alt comes from images.php. */ ?>
				<figure class="ax-media ax-media--43 ax-reveal">
					<span class="ax-media__frame">
						<?php echo alea_img( 'accessories' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built and escaped by alea_img(). ?>
						<span class="ax-media__tag">Display</span>
					</span>
					<figcaption class="ax-media__caption">
						Shelving and accessories display / ALEA experience centre &mdash;
						<b>the unit that made them is a free visit away</b>
					</figcaption>
				</figure>
			</div>
		</div>
	</section>

	<!-- ================================================== 7. FAQ -->
	<section class="ax-section ax-section--ruled">
		<div class="ax-wrap ax-wrap--narrow">
			<header class="ax-head ax-reveal">
				<p class="ax-eyebrow">Questions</p>
				<h2 class="ax-h2">Asked about how it is made.</h2>
			</header>
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

	<!-- ================================================== 8. BOOKING -->
	<!-- id="estimate": landing anchor for the theme's site-wide sticky mobile bar
	     (.aleac-mbar "Free Estimate" button in functions.php). -->
	<span id="estimate" class="ax-sr-only"></span>
	<section class="ax-section ax-section--ruled" id="alea-book">
		<div class="ax-wrap">
			<div class="ax-grid ax-grid--split">
				<div class="ax-reveal">
					<p class="ax-eyebrow">Book a free design visit</p>
					<h2 class="ax-h2">Come and watch the sequence for yourself.</h2>
					<p class="ax-lead ax-mt-4">
						Both visits are free and carry no obligation: a measurement at your home across
						<?php echo esc_html( $areas_txt ); ?>, or a visit to the unit at <?php echo esc_html( $place ); ?>
						where you can see kitchens at every stage above before you pay a rupee.
					</p>
					<div class="ax-btnrow ax-mt-5">
						<a class="ax-btn ax-btn--ink" href="<?php echo esc_url( $tel_href ); ?>">Call <?php echo esc_html( $phone_disp ); ?></a>
						<a class="ax-btn ax-btn--wa" href="<?php echo esc_url( $wa_href ); ?>" target="_blank" rel="noopener">WhatsApp us</a>
					</div>
					<?php /* The estimator link belongs HERE, not only in the hero nine
					         screens up: this is where a reader who has read the whole
					         sequence and now wants a price actually is. */ ?>
					<p class="ax-btn-note">
						Would rather see a number first? The estimator is free and needs no sign-up.
						<a class="ax-link" href="<?php echo esc_url( $calc_url ); ?>">Use the estimator</a>
					</p>
				</div>
				<div class="ax-form ax-form--card ax-reveal">
					<h3 class="ax-form__title">Book your free visit</h3>
					<p class="ax-form__note">Free / no obligation / <?php echo esc_html( implode( ' · ', $areas ) ); ?></p>
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
		'use strict';
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
