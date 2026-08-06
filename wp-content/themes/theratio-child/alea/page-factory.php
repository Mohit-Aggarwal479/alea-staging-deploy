<?php
/**
 * ALEA — /our-factory/ — "Inside Our Factory"
 * The Manufacturer's Record. Documentary tone, verified facts only.
 *
 * Included inside <main class="alea-main"> by the page shell.
 * Every business fact on this page is read from facts.php — nothing hard-coded.
 *
 * HONESTY NOTE: no photography of the shop floor or of a finished installation
 * exists yet. Every photograph on this page was taken in ALEA's own experience
 * centre and is captioned as exactly that — never as the factory, never as a
 * customer's home. The page says shop-floor photography is coming. All images
 * come from the verified catalogue in images.php; never hard-code a path and
 * never hand-write alt text. Do not swap in stock imagery.
 */

require_once __DIR__ . '/facts.php';
require_once __DIR__ . '/images.php';

$sqft         = alea_fact( 'factory_sqft' );            // '95,000'
$place        = alea_fact( 'factory_place' );           // 'Raipur Rani, Panchkula district'
$since_parent = alea_fact( 'founded_furniture' );       // '1998'
$since_alea   = alea_fact( 'alea_since' );              // '2009'
$years        = alea_fact( 'years_experience' );        // '13+'
$warranty_yrs = (int) alea_fact( 'warranty_years', 0 ); // 10
$brands       = alea_fact( 'hardware_brands', array() );// Hettich, Blum
$service      = alea_fact( 'service_area', array() );   // Panchkula, Chandigarh...
$phone_disp   = alea_fact( 'phone_display' );
$phone_tel    = alea_fact( 'phone_tel' );

$brands_txt   = implode( ' and ', $brands );            // "Hettich and Blum"
$service_txt  = implode( ', ', $service );

$wa_visit     = alea_wa_link( "Hi ALEA, I'd like to book a free factory visit at " . $place . '.' );
$wa_pin       = alea_wa_link( 'Hi ALEA, please send me the location pin for the factory at ' . $place . '.' );

/* FAQ — one array feeds both the visible <details> and the JSON-LD, so the
   two can never drift apart. Plain descriptions of this page only. */
$faqs = array(
	array(
		'q' => 'Is the factory visit really free?',
		'a' => 'Yes. A factory visit costs nothing and carries no obligation to buy. You walk the floor, see the materials and the ' . $brands_txt . ' hardware first-hand, and leave with whatever questions answered you came with.',
	),
	array(
		'q' => 'Where is the factory?',
		'a' => 'The factory is at ' . $place . '. It is a ' . $sqft . ' sq ft manufacturing unit, and we serve ' . $service_txt . ' from it. Message us on WhatsApp and we will send you a live location pin and help you plan the drive.',
	),
	array(
		'q' => 'What will I actually see on a visit?',
		'a' => 'The working floor: boards being cut, edges being sealed, carcasses being assembled and finished units being checked before dispatch. You can pick up the board material, open and close ' . $brands_txt . ' hinges and runners, and see kitchens at every stage of manufacture.',
	),
	array(
		'q' => 'Do I get the warranty in writing?',
		'a' => 'Yes. Every ALEA kitchen carries a ' . $warranty_yrs . '-year written warranty, handed over as a document — not a verbal promise. Ask for the written warranty terms on your free site visit and we will take you through exactly what is covered.',
	),
);

$faq_ld = array(
	'@context'   => 'https://schema.org',
	'@type'      => 'FAQPage',
	'mainEntity' => array(),
);
foreach ( $faqs as $f ) {
	$faq_ld['mainEntity'][] = array(
		'@type'          => 'Question',
		'name'           => $f['q'],
		'acceptedAnswer' => array(
			'@type' => 'Answer',
			'text'  => $f['a'],
		),
	);
}
?>
<div class="ax-root ax-has-stickybar">

	<!-- ================================================================
	     HERO — a photograph of our experience centre. No factory photos
	     exist yet; the credit line says so plainly.
	     ================================================================ -->
	<section class="ax-hero">
		<?php echo alea_img( 'kitchen-wide', array( 'class' => 'ax-hero__img', 'eager' => true ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		<div class="ax-hero__inner">
			<p class="ax-eyebrow">Inside our factory</p>
			<h1 class="ax-hero__title">Come and watch your kitchen being made.</h1>
			<p class="ax-hero__sub">Every ALEA kitchen is manufactured in our own <?php echo esc_html( $sqft ); ?> sq ft factory at <?php echo esc_html( $place ); ?> — and the door is open. See the materials, the machines and the <?php echo esc_html( $brands_txt ); ?> hardware before you spend a rupee.</p>
			<div class="ax-hero__actions">
				<a class="ax-btn ax-btn--primary ax-btn--lg" href="#book-visit">Book a free factory visit</a>
				<a class="ax-btn ax-btn--ghost ax-btn--lg" href="tel:<?php echo esc_attr( $phone_tel ); ?>">Call <span class="ax-btn__note"><?php echo esc_html( $phone_disp ); ?></span></a>
			</div>
			<p class="ax-hero__credit">Photograph: a kitchen and dining display at our experience centre &mdash; shop-floor photography is being shot and will appear here</p>
		</div>
	</section>

	<!-- ================================================================
	     FACTORY FACTS — mono spec strip, every value from facts.php
	     ================================================================ -->
	<section class="ax-section ax-section--tight">
		<div class="ax-wrap">
			<div class="ax-specstrip" aria-label="Factory facts">
				<div class="ax-specstrip__item">
					<span class="ax-specstrip__label">Own factory</span>
					<span class="ax-specstrip__value"><?php echo esc_html( $sqft ); ?><span class="ax-specstrip__unit">sq ft</span></span>
				</div>
				<div class="ax-specstrip__item">
					<span class="ax-specstrip__label">Location</span>
					<span class="ax-specstrip__value">Raipur Rani<span class="ax-specstrip__unit">Panchkula dist.</span></span>
				</div>
				<div class="ax-specstrip__item">
					<span class="ax-specstrip__label">Making furniture since</span>
					<span class="ax-specstrip__value"><?php echo esc_html( $since_parent ); ?><span class="ax-specstrip__unit">ALEA since <?php echo esc_html( $since_alea ); ?></span></span>
				</div>
				<div class="ax-specstrip__item">
					<span class="ax-specstrip__label">Warranty</span>
					<span class="ax-specstrip__value"><?php echo esc_html( $warranty_yrs ); ?><span class="ax-specstrip__unit">years, in writing</span></span>
				</div>
			</div>
		</div>
	</section>

	<!-- ================================================================
	     WHY THIS PAGE EXISTS — short documentary intro
	     ================================================================ -->
	<section class="ax-section ax-section--ruled ax-reveal">
		<div class="ax-wrap ax-wrap--text">
			<div class="ax-prose">
				<p>Most kitchen brands in the Tricity sell kitchens somebody else builds. We are the other kind: our parent firm has been making furniture since <?php echo esc_html( $since_parent ); ?>, ALEA has been making modular kitchens since <?php echo esc_html( $since_alea ); ?> — <?php echo esc_html( $years ); ?> years of it — and everything we sell is manufactured under our own roof at <?php echo esc_html( $place ); ?>.</p>
				<p>That is easy to write and easy to fake, which is why this page ends with an invitation rather than a slogan: come and see it. A factory visit is free, and no photograph argues as well as the floor itself.</p>
				<p class="ax-mono--label">A note on the photographs: documentary photography of the shop floor is in progress. Until it is ready, every photograph on this page was taken in our own experience centre &mdash; displays you can walk around yourself, not the factory floor, and nothing stock.</p>
			</div>
		</div>
	</section>

	<!-- ================================================================
	     WHAT HAPPENS HERE — five plain-language manufacturing stages
	     ================================================================ -->
	<section class="ax-section ax-section--ruled ax-reveal">
		<div class="ax-wrap">
			<div class="ax-head">
				<p class="ax-eyebrow">What happens here</p>
				<h2 class="ax-h2">Five stages, in plain language.</h2>
				<p class="ax-lead">No mystique. This is what happens to a sheet of board between your final design and your fitted kitchen.</p>
			</div>
			<ol class="ax-steps">
				<li class="ax-step">
					<div class="ax-step__body">
						<h3 class="ax-step__title">Cutting</h3>
						<p class="ax-step__text">Full sheets of board are cut by machine into the exact panels on your kitchen&rsquo;s cutting list. Machine cutting is what keeps every panel square and every repeat identical &mdash; the difference you feel later as doors that line up.</p>
					</div>
				</li>
				<li class="ax-step">
					<div class="ax-step__body">
						<h3 class="ax-step__title">Edge banding</h3>
						<p class="ax-step__text">Every cut edge is sealed under heat and pressure with edge banding. This is the unglamorous step that decides how a kitchen survives an Indian kitchen&rsquo;s steam and spills &mdash; an unsealed edge is where cheap furniture starts to swell.</p>
					</div>
				</li>
				<li class="ax-step">
					<div class="ax-step__body">
						<h3 class="ax-step__title">Drilling</h3>
						<p class="ax-step__text">Hinge cups, shelf pins and fitting holes are drilled by machine at set positions, so the hardware seats square and doors hang true &mdash; not marked out by eye on site with a hand drill.</p>
					</div>
				</li>
				<li class="ax-step">
					<div class="ax-step__body">
						<h3 class="ax-step__title">Assembly</h3>
						<p class="ax-step__text">Panels become carcasses on the factory floor, and the <?php echo esc_html( $brands_txt ); ?> hinges and runners are fitted here &mdash; not out of a toolbox in your flat. Factory assembly is why an ALEA install is fitting, not carpentry.</p>
					</div>
				</li>
				<li class="ax-step">
					<div class="ax-step__body">
						<h3 class="ax-step__title">Quality check &amp; dispatch</h3>
						<p class="ax-step__text">Before anything leaves, each unit is checked against your order &mdash; sizes, finish, hardware action &mdash; then packed and dispatched for installation. If a door does not sit right, it is fixed here, where fixing it is easy.</p>
					</div>
				</li>
			</ol>
		</div>
	</section>

	<!-- ================================================================
	     HARDWARE — Hettich / Blum, tied to the written warranty
	     ================================================================ -->
	<section class="ax-section ax-section--ruled ax-reveal">
		<div class="ax-wrap ax-wrap--text">
			<!-- hardware photography pending: the library has no image of the hardware
			     store room or of a hinge/runner, and the previous photo here was stock
			     imagery of a kitchen that is not ALEA's work. Text only until real
			     hardware photography exists. -->
			<div class="ax-head ax-mb-5">
				<p class="ax-eyebrow">Hardware store room</p>
				<h2 class="ax-h2">The hardware has a name.</h2>
				<p class="ax-lead">The part of a kitchen you touch fifty times a day is the hinge and the runner. Ours are branded, and you can inspect the stock yourself on a visit.</p>
			</div>
			<ul class="ax-prooflist">
				<?php foreach ( $brands as $brand ) : ?>
				<li class="ax-proof">
					<span class="ax-proof__tick" aria-hidden="true"></span>
					<div class="ax-proof__text">
						<strong><?php echo esc_html( $brand ); ?></strong> hinges and runners, fitted at the factory during assembly.
						<span class="ax-proof__never">Never unbranded hardware</span>
					</div>
				</li>
				<?php endforeach; ?>
				<li class="ax-proof">
					<span class="ax-proof__tick" aria-hidden="true"></span>
					<div class="ax-proof__text">
						Covered by the <?php echo esc_html( $warranty_yrs ); ?>-year written warranty that comes with every ALEA kitchen.
						<span class="ax-proof__never">Never a verbal promise</span>
					</div>
				</li>
			</ul>
		</div>
	</section>

	<!-- ================================================================
	     THE PAPERWORK — written warranty at handover. No PDF exists yet,
	     so there is deliberately no download link here.
	     ================================================================ -->
	<section class="ax-section ax-section--timber ax-reveal">
		<div class="ax-wrap ax-wrap--narrow">
			<div class="ax-head">
				<p class="ax-eyebrow">The paperwork you receive</p>
				<h2 class="ax-h2">The warranty is a document, not a sentence.</h2>
			</div>
			<div class="ax-prose">
				<p>When your kitchen is handed over, so is a written <?php echo esc_html( $warranty_yrs ); ?>-year warranty. It exists on paper so that in year six you are holding a document, not remembering a conversation.</p>
			</div>
			<dl class="ax-spectable--rows ax-mt-6">
				<div class="ax-spectable__row">
					<dt class="ax-spectable__key">Document</dt>
					<dd class="ax-spectable__val"><?php echo esc_html( $warranty_yrs ); ?>-year warranty</dd>
				</div>
				<div class="ax-spectable__row">
					<dt class="ax-spectable__key">Format</dt>
					<dd class="ax-spectable__val">In writing</dd>
				</div>
				<div class="ax-spectable__row">
					<dt class="ax-spectable__key">Handed over</dt>
					<dd class="ax-spectable__val">With your kitchen</dd>
				</div>
			</dl>
			<p class="ax-mono--label ax-mt-5">Want to read it first? Ask for the written warranty terms on your free site visit.</p>
		</div>
	</section>

	<!-- ================================================================
	     BOOK A FREE FACTORY VISIT — CF7 form + call / WhatsApp
	     ================================================================ -->
	<section class="ax-section ax-section--ruled ax-reveal" id="book-visit">
		<div class="ax-wrap">
			<div class="ax-grid ax-grid--split">
				<div>
					<div class="ax-head">
						<p class="ax-eyebrow">The open door</p>
						<h2 class="ax-h2">What a free factory visit is.</h2>
						<p class="ax-lead">Not a sales meeting. You walk the working floor at <?php echo esc_html( $place ); ?> with one of us, at your pace, and see with your own hands what you would otherwise take on trust.</p>
					</div>
					<ul class="ax-prooflist">
						<li class="ax-proof">
							<span class="ax-proof__tick" aria-hidden="true"></span>
							<div class="ax-proof__text">Handle the actual board material and sealed edges &mdash; not a laminate swatch book.</div>
						</li>
						<li class="ax-proof">
							<span class="ax-proof__tick" aria-hidden="true"></span>
							<div class="ax-proof__text">Open and close the <?php echo esc_html( $brands_txt ); ?> hinges and runners from our hardware stock, first-hand.</div>
						</li>
						<li class="ax-proof">
							<span class="ax-proof__tick" aria-hidden="true"></span>
							<div class="ax-proof__text">See kitchens mid-manufacture at every stage &mdash; cutting, edge banding, assembly, final checks.</div>
						</li>
						<li class="ax-proof">
							<span class="ax-proof__tick" aria-hidden="true"></span>
							<div class="ax-proof__text">Free, and no obligation. Come, look, ask, leave. The floor makes the argument or it doesn&rsquo;t.</div>
						</li>
					</ul>
					<figure class="ax-media ax-mt-6 ax-hide-lg">
						<div class="ax-media__frame">
							<?php echo alea_img( 'kitchen-island' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
							<span class="ax-media__tag">Our experience centre</span>
						</div>
						<figcaption class="ax-media__caption">A kitchen display at our experience centre &mdash; the same units, made at the factory you are booking to visit</figcaption>
					</figure>
				</div>
				<div class="ax-form ax-form--card">
					<h3 class="ax-form__title">Book a free factory visit</h3>
					<p class="ax-form__note">Free / No obligation / <?php echo esc_html( $place ); ?></p>
					<?php echo do_shortcode( '[contact-form-7 id="7dcf010" title="Home Page form"]' ); ?>
					<p class="ax-btn-note">Prefer to talk first?</p>
					<div class="ax-btnrow ax-mt-3">
						<a class="ax-btn ax-btn--ink ax-btn--block-sm" href="tel:<?php echo esc_attr( $phone_tel ); ?>">Call <span class="ax-btn__note"><?php echo esc_html( $phone_disp ); ?></span></a>
						<a class="ax-btn ax-btn--wa ax-btn--block-sm" href="<?php echo esc_url( $wa_visit ); ?>" target="_blank" rel="noopener">
							<svg class="ax-btn__icon" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2a10 10 0 0 0-8.6 15.1L2 22l5-1.3A10 10 0 1 0 12 2zm0 18.2a8.1 8.1 0 0 1-4.2-1.2l-.3-.2-3 .8.8-2.9-.2-.3A8.2 8.2 0 1 1 12 20.2zm4.6-6.1c-.3-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1-.2.3-.6.8-.8 1-.1.2-.3.2-.5.1a6.7 6.7 0 0 1-3.3-2.9c-.3-.4 0-.5.1-.7l.4-.5c.1-.2.1-.3.2-.5 0-.2 0-.4-.1-.5l-.8-1.9c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.5.1-.7.3-.2.3-.9.9-.9 2.2s.9 2.5 1 2.7a11 11 0 0 0 4.3 3.8c.6.3 1.1.4 1.4.5.6.2 1.2.2 1.6.1.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.2-1.2-.1-.1-.2-.2-.4-.3z"/></svg>
							WhatsApp us
						</a>
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- ================================================================
	     FINDING US — directions in text, no embedded map
	     ================================================================ -->
	<section class="ax-section ax-section--sheet ax-section--ruled ax-reveal">
		<div class="ax-wrap ax-wrap--narrow">
			<div class="ax-head">
				<p class="ax-eyebrow">Finding us</p>
				<h2 class="ax-h2">Where the factory is.</h2>
			</div>
			<div class="ax-prose">
				<p>The factory is at <strong>Raipur Rani, in Panchkula district</strong> &mdash; the manufacturing side of the district, not Panchkula city itself. From it we serve <?php echo esc_html( $service_txt ); ?>.</p>
				<p>Rather than an approximate map pin, message us on WhatsApp before you set out and we will send you a live location pin and talk you in for the last stretch &mdash; and make sure someone is at the gate expecting you.</p>
			</div>
			<dl class="ax-spectable--rows ax-mt-6">
				<div class="ax-spectable__row">
					<dt class="ax-spectable__key">Address</dt>
					<dd class="ax-spectable__val"><?php echo esc_html( $place ); ?></dd>
				</div>
				<div class="ax-spectable__row">
					<dt class="ax-spectable__key">Phone</dt>
					<dd class="ax-spectable__val"><a class="ax-link" href="tel:<?php echo esc_attr( $phone_tel ); ?>"><?php echo esc_html( $phone_disp ); ?></a></dd>
				</div>
				<div class="ax-spectable__row">
					<dt class="ax-spectable__key">Directions</dt>
					<dd class="ax-spectable__val"><a class="ax-link" href="<?php echo esc_url( $wa_pin ); ?>" target="_blank" rel="noopener">WhatsApp us for a location pin</a></dd>
				</div>
			</dl>
		</div>
	</section>

	<!-- ================================================================
	     FAQ — <details>/<summary> + FAQPage JSON-LD from the same array
	     ================================================================ -->
	<section class="ax-section ax-section--ruled ax-reveal">
		<div class="ax-wrap ax-wrap--narrow">
			<div class="ax-head">
				<p class="ax-eyebrow">Visit questions</p>
				<h2 class="ax-h2">Before you come.</h2>
			</div>
			<div class="ax-faq">
				<?php foreach ( $faqs as $f ) : ?>
				<details class="ax-faq__item">
					<summary class="ax-faq__q"><?php echo esc_html( $f['q'] ); ?></summary>
					<div class="ax-faq__a"><p><?php echo esc_html( $f['a'] ); ?></p></div>
				</details>
				<?php endforeach; ?>
			</div>
			<div class="ax-btnrow ax-mt-7">
				<a class="ax-btn ax-btn--primary ax-btn--lg ax-btn--block-sm" href="#book-visit">Book a free factory visit</a>
				<a class="ax-btn ax-btn--ghost ax-btn--lg ax-btn--block-sm" href="<?php echo esc_url( home_url( '/kitchen-cost-calculator/' ) ); ?>">Get my price first</a>
			</div>
		</div>
	</section>
	<script type="application/ld+json"><?php echo wp_json_encode( $faq_ld, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ); ?></script>

	<!-- ================================================================
	     STICKY MOBILE BAR — two thumb targets, retires at 768px via CSS
	     ================================================================ -->
	<?php /* Sticky mobile bar rendered once, centrally, by the redesign router. */ ?>

	<script data-no-optimize="1" data-no-defer="1">
	(function () {
		'use strict';
		try {
			var root = document.querySelector('.ax-root');
			if (!root) { return; }

			/* Gate the reveal styles behind JS so a broken script never hides content. */
			root.classList.add('ax-js');

			var items = root.querySelectorAll('.ax-reveal');
			if (items.length && 'IntersectionObserver' in window) {
				var io = new IntersectionObserver(function (entries) {
					for (var i = 0; i < entries.length; i++) {
						if (entries[i].isIntersecting) {
							entries[i].target.classList.add('is-visible');
							io.unobserve(entries[i].target);
						}
					}
				}, { rootMargin: '0px 0px -10% 0px', threshold: 0.05 });
				for (var j = 0; j < items.length; j++) { io.observe(items[j]); }
			} else {
				for (var k = 0; k < items.length; k++) { items[k].classList.add('is-visible'); }
			}
		} catch (e) {
			/* Never throw: worst case the page is simply static and fully visible. */
			try {
				var all = document.querySelectorAll('.ax-reveal');
				for (var m = 0; m < all.length; m++) { all[m].classList.add('is-visible'); }
			} catch (e2) {}
		}
	})();
	</script>

</div>
