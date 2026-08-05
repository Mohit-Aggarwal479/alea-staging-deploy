<?php
/**
 * ALEA — Wardrobes hub ("The Manufacturer's Record")
 * Replaces /wardrobe/. Included inside <main class="alea-main"> by shell.php.
 *
 * FACTUAL INTEGRITY NOTES (read before editing):
 * - NO rupee figure appears anywhere on this page. Wardrobe pricing is NOT
 *   verified, so this page states plainly that wardrobes are priced per design
 *   after a free measurement, and points at the KITCHEN estimator for kitchens.
 * - Every number here (factory sq ft, warranty years, install days, hardware
 *   brands, dates, place) is read from facts.php. Nothing is hard-coded.
 * - Every photograph in the approved pool is experience-centre / display
 *   photography. Alt text and captions therefore never claim a delivered
 *   project, a client home, or a factory floor.
 * - FLAGGED POOL LIMITATION: the approved pool contains exactly TWO frames
 *   that show a wardrobe (2022/03/w2.jpg and 2022/03/w3.jpg). w3 is the hero;
 *   w2 carries the experience-centre strip. The strip is deliberately short
 *   rather than padded out with lounge and reception photographs, which would
 *   make a wardrobes page whose visual evidence is mostly not wardrobes.
 *   Wardrobe display photography is pending from the owner.
 * - ROUTING (outside this file, flagged not fixed): alea_redesign_map() in
 *   functions.php has no '/wardrobe/' entry yet, so this template is not
 *   routed and its CSS inliner will not fire. The build contract scopes this
 *   deliverable to one PHP file in alea/, so the map entry is left to the
 *   theme-level change that lands the other new templates too.
 */

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/facts.php';

$f          = alea_facts();
$sqft       = $f['factory_sqft'];
$place      = $f['factory_place'];
$wty        = (int) $f['warranty_years'];
$days       = (int) $f['install_days'];
$brands     = implode( ' and ', $f['hardware_brands'] );
$brands_amp = implode( ' & ', $f['hardware_brands'] );
$calc_url   = home_url( '/kitchen-cost-calculator/' );
$tel_href   = 'tel:' . alea_fact( 'phone_tel' );
$phone_disp = alea_fact( 'phone_display' );
$wa_href    = alea_wa_link( 'Hi ALEA, I would like a free wardrobe measurement.' );

/* The three wardrobe types. Cards are deliberately TEXT cards: the approved
   photo pool has no photograph verified to show a sliding / hinged / walk-in
   wardrobe, and putting an unlabelled display photo under a type heading
   would make a claim we cannot stand behind. The "when" lines are generic
   industry guidance, framed as guidance — never as an ALEA-specific claim. */
$types = array(
	array(
		'name' => 'Sliding wardrobes',
		'url'  => '/wardrobe/sliding/',
		'when' => 'Generally the choice when there is no clear floor space in front of the wardrobe — the doors run across the front instead of swinging into the room.',
		'link' => 'See sliding wardrobes',
	),
	array(
		'name' => 'Hinged wardrobes',
		'url'  => '/wardrobe/hinged/',
		'when' => 'Generally the choice when a door has room to open — you see the full depth of the interior at once, and shelves stay easy to reach.',
		'link' => 'See hinged wardrobes',
	),
	array(
		'name' => 'Walk-in wardrobes',
		'url'  => '/wardrobe/walk-in/',
		'when' => 'Generally the choice when a small room or a deep recess can be given over to storage, and you want it open, lit and walkable.',
		'link' => 'See walk-in wardrobes',
	),
);

/* Experience-centre plates. Captioned as exactly what they are.
   Deliberately TWO plates, not four: the approved pool holds only two
   wardrobe frames (w3 is the hero, w2 is here), and the second plate is the
   internal-fittings display this page's pricing section actually talks about.
   The lounge and reception photographs used on the homepage are left off —
   on a wardrobes page they would be three quarters of the visual evidence
   showing something other than a wardrobe. */
$plates = array(
	array(
		'src' => '/wp-content/uploads/2022/03/w2.jpg',
		'alt' => 'Wardrobe interior and hanging space on display at the ALEA experience centre',
		'cap' => 'Wardrobe interior / ALEA experience centre',
	),
	array(
		'src' => '/wp-content/uploads/2022/09/Alea-Modular-Kitchen-Wardrobes-5.jpg',
		'alt' => 'Open shelving and internal fittings on display at the ALEA experience centre',
		'cap' => 'Shelving and internal fittings / ALEA experience centre',
	),
);

/* FAQ — the visible HTML and the JSON-LD are rendered from this ONE array,
   so the schema can never drift from the text on the page. No prices. */
$faq = array(
	array(
		'q' => 'How much does an ALEA wardrobe cost?',
		'a' => 'We do not publish a wardrobe rate, because two wardrobes of the same width can cost very different amounts once the inside is decided — drawer banks, hanging heights, shelves, mirrors, lighting and loft units all move the figure. Your wardrobe is priced per design, in writing, after a free measurement at your home. Our kitchen rates are published, and the online kitchen estimator is free to use.',
	),
	array(
		'q' => 'Are wardrobes built to the same standard as your kitchens?',
		'a' => sprintf(
			'Yes. The same %1$s sq ft factory at %2$s, the same %3$s hardware as standard, and the same written warranty. We have been making furniture since %4$s and modular kitchens since %5$s.',
			$sqft,
			$place,
			$brands,
			$f['founded_furniture'],
			$f['alea_since']
		),
	),
	array(
		'q' => 'What warranty comes with a wardrobe?',
		'a' => sprintf(
			'A %d-year warranty, in writing — never a verbal promise. It covers panels and hardware against manufacturing defects; the full terms live in the written document you receive, and you are welcome to read them on your free measurement visit before you order anything.',
			$wty
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
         occupies exactly this space. Do not remove it, and do not read it as a
         promise that this file builds a bar. */ ?>
<div class="ax-root ax-has-stickybar">

	<style data-no-optimize="1">
	/* Page-specific only. Type cards are text cards; the link sits on the
	   card's baseline so three cards of unequal copy still line up. */
	.axp-type .axp-more{margin-top:auto;padding-top:var(--sp-5)}
	.axp-when{margin-top:var(--sp-2)}
	.axp-note{max-width:60ch}
	.axp-hero-note{margin-top:var(--sp-4);font-size:.9375rem;color:#E4E1DC}
	/* The design system drops the sticky-bar reserve at 768px, but the THEME's
	   .aleac-mbar stays visible to 781px — so the bar would cover the bottom of
	   the page on an iPad in portrait. Re-reserve across that 14px band. */
	@media(min-width:768px) and (max-width:781px){.ax-root.ax-has-stickybar{padding-bottom:64px}}
	</style>

	<!-- ==================== 1. HERO ==================== -->
	<section class="ax-hero">
		<img class="ax-hero__img"
			src="<?php echo esc_url( home_url( '/wp-content/uploads/2022/03/w3.jpg' ) ); ?>"
			alt="Sliding wardrobe display at the ALEA experience centre"
			loading="eager" fetchpriority="high">
		<div class="ax-hero__inner">
			<p class="ax-eyebrow">Wardrobes &mdash; <?php echo esc_html( $place ); ?></p>
			<h1 class="ax-hero__title">Wardrobes, made on the same factory floor as our kitchens.</h1>
			<p class="ax-hero__sub">
				Sliding, hinged and walk-in &mdash; built in our own <?php echo esc_html( $sqft ); ?>&nbsp;sq&nbsp;ft factory,
				fitted with <?php echo esc_html( $brands_amp ); ?> hardware as standard, and carrying the same
				<?php echo esc_html( $wty ); ?>-year written warranty.
			</p>
			<div class="ax-hero__actions">
				<a class="ax-btn ax-btn--primary ax-btn--lg" href="#alea-book">Book a free measurement</a>
				<a class="ax-btn ax-btn--ghost ax-btn--lg" href="<?php echo esc_url( $tel_href ); ?>">Call <span class="ax-btn__note"><?php echo esc_html( $phone_disp ); ?></span></a>
			</div>
			<p class="axp-hero-note">Free, at your home, no obligation &mdash; measurements are yours to keep either way.</p>
			<p class="ax-hero__credit">Photograph: sliding wardrobe display at our experience centre</p>
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
					<span class="ax-specstrip__value"><?php echo esc_html( $wty ); ?><span class="ax-specstrip__unit">years</span></span>
				</div>
				<?php /* install_days is an OWNER fact evidenced for KITCHENS. It is
				         labelled as such here rather than silently transferred to
				         wardrobes, which have no verified fitting time. */ ?>
				<div class="ax-specstrip__item">
					<span class="ax-specstrip__label">Kitchen installation</span>
					<span class="ax-specstrip__value"><?php echo esc_html( $days ); ?><span class="ax-specstrip__unit">days</span></span>
				</div>
			</div>
		</div>
		<div class="ax-wrap">
			<p class="ax-mono--label ax-mt-3 ax-pb-5">The same floor, the same hardware and the same written warranty as every ALEA kitchen &mdash; the <?php echo esc_html( $days ); ?>-day figure is our kitchen installation time; wardrobe fitting time is confirmed on the free measurement</p>
		</div>
	</section>

	<!-- ==================== 3. THREE TYPES ==================== -->
	<section class="ax-section ax-section--ruled" id="types">
		<div class="ax-wrap">
			<div class="ax-head ax-reveal">
				<p class="ax-eyebrow">Three ways to build one</p>
				<h2 class="ax-h2">Sliding, hinged or walk-in.</h2>
				<p class="ax-lead">The right one is usually decided by the room, not by taste &mdash; how much floor there is in front of the wardrobe, and how much of the interior you want to see at once.</p>
			</div>
			<div class="ax-grid ax-grid--3">
				<?php foreach ( $types as $type ) : ?>
				<article class="ax-card axp-type ax-reveal">
					<p class="ax-mono--label">When to choose it</p>
					<h3 class="ax-card__title ax-mt-2"><?php echo esc_html( $type['name'] ); ?></h3>
					<p class="ax-card__body axp-when"><?php echo esc_html( $type['when'] ); ?></p>
					<p class="ax-card__meta axp-more">
						<a class="ax-card__link" href="<?php echo esc_url( home_url( $type['url'] ) ); ?>"><?php echo esc_html( $type['link'] ); ?></a>
					</p>
				</article>
				<?php endforeach; ?>
			</div>
			<p class="ax-btn-note">General guidance, not a rule &mdash; we will say which one suits your room on the free measurement visit</p>
		</div>
	</section>

	<!-- ==================== 4. BUILT LIKE OUR KITCHENS ==================== -->
	<section class="ax-section ax-section--ink">
		<div class="ax-wrap">
			<div class="ax-grid ax-grid--split">
				<div class="ax-head ax-mb-0 ax-reveal">
					<p class="ax-eyebrow">Built like our kitchens</p>
					<h2 class="ax-h2">Nothing about a wardrobe is a lesser job here.</h2>
					<p class="ax-lead ax-mt-4">
						It is cut, edge-banded and assembled on the same <?php echo esc_html( $sqft ); ?> sq ft floor at
						<?php echo esc_html( $place ); ?>, by the same people, from the same hardware shelf &mdash; and it
						leaves with the same written warranty. You are welcome to come and watch.
					</p>
				</div>
				<div class="ax-reveal">
					<div class="ax-spectable--rows">
						<div class="ax-spectable__row">
							<span class="ax-spectable__key">Made in</span>
							<span class="ax-spectable__val">Our own <?php echo esc_html( $sqft ); ?> sq ft factory</span>
						</div>
						<div class="ax-spectable__row">
							<span class="ax-spectable__key">Where</span>
							<span class="ax-spectable__val"><?php echo esc_html( $place ); ?></span>
						</div>
						<div class="ax-spectable__row">
							<span class="ax-spectable__key">Hardware</span>
							<span class="ax-spectable__val"><?php echo esc_html( $brands_amp ); ?> as standard</span>
						</div>
						<div class="ax-spectable__row">
							<span class="ax-spectable__key">Warranty</span>
							<span class="ax-spectable__val"><?php echo esc_html( $wty ); ?> years, in writing</span>
						</div>
						<div class="ax-spectable__row">
							<span class="ax-spectable__key">Furniture since</span>
							<span class="ax-spectable__val"><?php echo esc_html( $f['founded_furniture'] ); ?></span>
						</div>
					</div>
					<ul class="ax-prooflist ax-mt-5">
						<li class="ax-proof">
							<span class="ax-proof__tick" aria-hidden="true"></span>
							<div class="ax-proof__text">
								Manufactured on our own floor, not bought in and badged.
								<span class="ax-proof__never">Never outsourced</span>
							</div>
						</li>
						<li class="ax-proof">
							<span class="ax-proof__tick" aria-hidden="true"></span>
							<div class="ax-proof__text">
								<?php /* Site-wide wording, sourced from facts.php hardware_brands.
								         Do NOT extend this to sliding-door gear or any other
								         component category: only the brand names are verified. */ ?>
								<?php echo esc_html( $brands ); ?> hinges, runners and soft-close systems as standard.
								<span class="ax-proof__never">Never generic hardware</span>
							</div>
						</li>
						<li class="ax-proof">
							<span class="ax-proof__tick" aria-hidden="true"></span>
							<div class="ax-proof__text">
								A <?php echo esc_html( $wty ); ?>-year warranty covering panels and hardware against manufacturing defects &mdash; full terms in the written document you receive.
								<span class="ax-proof__never">Never a verbal promise</span>
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

	<!-- ==================== 5. HOW PRICING WORKS ==================== -->
	<!-- No rupee figure appears in this section, on purpose: wardrobe rates
	     are not verified, so the page explains the method instead of
	     inventing a number. -->
	<section class="ax-section ax-section--ruled" id="pricing">
		<div class="ax-wrap">
			<div class="ax-grid ax-grid--split">
				<div class="ax-head ax-mb-0 ax-reveal">
					<p class="ax-eyebrow">How pricing works</p>
					<h2 class="ax-h2">A wardrobe is priced after it is measured, not before.</h2>
					<p class="ax-lead ax-mt-4">
						We publish a rate card for kitchens because a kitchen is largely base and wall units.
						Wardrobes are not like that: two wardrobes of exactly the same width can cost very
						different amounts once the inside is decided. So we measure first, design, and then
						price it &mdash; per design, in writing.
					</p>
				</div>
				<div class="ax-reveal">
					<div class="ax-prose axp-note">
						<p>What actually moves the number:</p>
						<ul>
							<li>How much of it is hanging space, and at what heights.</li>
							<li>How many drawer banks are inside, and on which runners.</li>
							<li>Shelves, shoe racks, trouser pull-outs and other internal fittings.</li>
							<li>Mirrors, glass, lighting and a loft unit above.</li>
							<li>The finish on the shutters, and whether doors slide or swing.</li>
						</ul>
					</div>
					<div class="ax-btnrow ax-mt-5">
						<a class="ax-btn ax-btn--primary" href="#alea-book">Book a free measurement</a>
						<a class="ax-btn ax-btn--ghost" href="<?php echo esc_url( $calc_url ); ?>">Price a kitchen instead</a>
					</div>
					<p class="ax-btn-note">The online estimator prices kitchens only &mdash; wardrobes are quoted after the measurement</p>
				</div>
			</div>
		</div>
	</section>

	<!-- ==================== 6. EXPERIENCE CENTRE ==================== -->
	<section class="ax-section">
		<div class="ax-wrap">
			<div class="ax-head ax-reveal">
				<p class="ax-eyebrow">The experience centre</p>
				<h2 class="ax-h2">Two photographs. The rest you have to come and open.</h2>
				<p class="ax-lead">
					Two photographs of our own display centre, captioned as exactly what they
					show. Only two, because we would rather show you the wardrobes than fill
					the page with pictures of our reception. There is a good deal more on the
					floor: come and open a door, pull a drawer out to full extension, and see
					the <?php echo esc_html( $brands_amp ); ?> hinges and runners working
					before you decide anything.
				</p>
			</div>
			<div class="ax-grid ax-grid--2">
				<?php foreach ( $plates as $plate_i => $plate ) : ?>
				<figure class="ax-media ax-media--43 ax-reveal">
					<span class="ax-media__frame">
						<img src="<?php echo esc_url( home_url( $plate['src'] ) ); ?>"
							alt="<?php echo esc_attr( $plate['alt'] ); ?>"
							loading="lazy">
						<span class="ax-media__tag">PLATE <?php echo esc_html( sprintf( '%02d', $plate_i + 1 ) ); ?></span>
					</span>
					<figcaption class="ax-media__caption"><?php echo esc_html( $plate['cap'] ); ?></figcaption>
				</figure>
				<?php endforeach; ?>
			</div>
			<div class="ax-btnrow ax-mt-6">
				<a class="ax-btn ax-btn--ghost" href="#alea-book">Book a free measurement &mdash; or just come and look</a>
			</div>
		</div>
	</section>

	<!-- ==================== 7. FAQ ==================== -->
	<section class="ax-section ax-section--ruled">
		<div class="ax-wrap ax-wrap--narrow">
			<div class="ax-head ax-reveal">
				<p class="ax-eyebrow">Questions</p>
				<h2 class="ax-h2">Asked before every wardrobe we build.</h2>
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

	<!-- ==================== 8. CTA BAND + FORM ==================== -->
	<!-- TWO ids on purpose, both landing on the measurement form:
	     id="estimate" is the target of the theme's site-wide sticky mobile bar
	       (.aleac-mbar "Free Estimate" button, functions.php) — without it that
	       button is a dead link on the whole mobile experience;
	     id="alea-book" is the house convention used by every other ALEA
	       template, so cross-page links written as /wardrobe/#alea-book work. -->
	<section class="ax-section ax-section--ruled" id="estimate">
		<div class="ax-wrap" id="alea-book">
			<div class="ax-grid ax-grid--split">
				<div class="ax-reveal">
					<p class="ax-eyebrow">Book a free measurement</p>
					<h2 class="ax-h2">We measure, then we price it.</h2>
					<p class="ax-lead ax-mt-4">
						Someone comes to your home, measures the space, and talks through what goes inside.
						It is free, it carries no obligation, and the measurements are yours to keep
						whoever you build with. We work across <?php echo esc_html( implode( ', ', $f['service_area'] ) ); ?>.
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
						Tell us it is wardrobes you are after and we will bring the right samples.
						We use your details only to arrange the visit.
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
