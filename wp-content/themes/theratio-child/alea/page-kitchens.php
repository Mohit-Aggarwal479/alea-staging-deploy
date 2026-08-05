<?php
/**
 * ALEA — Modular kitchens hub ("The Manufacturer's Record")
 * Replaces /modular-kitchen/. Included inside <main class="alea-main"> by the
 * shell; header, footer and the site-wide sticky mobile bar come from the theme.
 *
 * SINGLE-URL template: it serves one path only, so it reads no variant from
 * $GLOBALS['alea_page_args']. (Multi-URL siblings — page-collection.php —
 * whitelist their variant there.)
 *
 * Every business number is read from facts.php or computed from it in PHP, with
 * the estimation assumption stated in visible text. Nothing is hard-coded.
 */

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/facts.php';

$f = alea_facts();

$calc_url   = home_url( '/kitchen-cost-calculator/' );
$tel_href   = 'tel:' . alea_fact( 'phone_tel' );
$wa_href    = alea_wa_link( "Hi ALEA, I'd like a free estimate for a modular kitchen." );
$phone_disp = alea_fact( 'phone_display' );
$brands     = implode( ' and ', $f['hardware_brands'] );
$areas      = implode( ', ', $f['service_area'] );
$sqft_rft   = (int) alea_fact( 'sqft_per_rft' );

/* Public band across the whole range, derived from EVERY collection band in
   facts.php — never from two hard-keyed slugs — so adding or retiring a
   collection there cannot leave this page quoting a stale range. */
$band_low  = 0;
$band_high = 0;
foreach ( $f['collections'] as $band_col ) {
	$band_low  = $band_low ? min( $band_low, (int) $band_col['from'] ) : (int) $band_col['from'];
	$band_high = max( $band_high, (int) $band_col['to'] );
}

/* Every band label on this page comes from alea_price_band(), which composes
   the unit from facts.php ('price_unit'). One source, so the cards, the spec
   strip and the FAQ can never disagree about the unit. */
$price_unit = (string) alea_fact( 'price_unit' );
$band_lines = array();
foreach ( $f['collections'] as $band_slug => $band_col ) {
	$band_lines[] = $band_col['name'] . ' ' . alea_price_band( $band_slug );
}

/* One worked whole-kitchen estimate, computed via alea_kitchen_total().
   The 8 sq ft-per-running-foot assumption is stated in visible text below.
   The example collection falls back to the first one defined, so removing
   'signature' from facts.php degrades instead of breaking. */
$ex_rft  = 12;
$ex_slug = isset( $f['collections']['signature'] ) ? 'signature' : (string) key( $f['collections'] );
$ex_name = isset( $f['collections'][ $ex_slug ]['name'] ) ? (string) $f['collections'][ $ex_slug ]['name'] : '';
list( $ex_low, $ex_high, $ex_sqft ) = alea_kitchen_total( $ex_rft, $ex_slug );

/* Collection cards. Images are experience-centre display photography — the only
   photography verified to be ALEA's own. Alts say "display", never "delivered
   project", "client home" or "factory floor". */
$collection_imgs = array(
	'essential' => array(
		'src' => '/wp-content/uploads/2022/09/Alea-Modular-Kitchen-Wardrobes-6.jpg',
		'alt' => 'ALEA modular kitchen display with a breakfast table at the experience centre',
	),
	'signature' => array(
		'src' => '/wp-content/uploads/2022/09/Alea-Modular-Kitchen-Wardrobes-3.jpg',
		'alt' => 'ALEA modular kitchen display with an island at the experience centre',
	),
	'atelier'   => array(
		'src' => '/wp-content/uploads/2022/09/Alea-Modular-Kitchen-Wardrobes-1.jpg',
		'alt' => 'Sculpted timber joinery at the ALEA experience centre',
	),
);

/* The six layouts. "When it works" is general kitchen-planning guidance, framed
   as guidance in the visible copy — never presented as an ALEA measurement.
   Each links to the layout page that already exists on the site. */
$layouts = array(
	array(
		'name' => 'L-shape',
		'url'  => '/modular-kitchen/l-shape/',
		'svg'  => '<path d="M5 4v16h15"/>',
		'when' => 'Two runs meeting at a corner. The most forgiving layout in a standard flat, and it usually still leaves room for a small table.',
		'rft'  => '8–12 running feet',
	),
	array(
		'name' => 'U-shape',
		'url'  => '/modular-kitchen/u-shape/',
		'svg'  => '<path d="M4 4v16h16V4"/>',
		'when' => 'Three walls of storage and worktop. Works when the kitchen is a room of its own and more than one person cooks in it.',
		'rft'  => '12–18 running feet',
	),
	array(
		/* Label must stay identical to the 'name' this layout carries in
		   page-layout.php ('Straight-line'), so the card and the H1 it leads to
		   read the same. */
		'name' => 'Straight-line',
		'url'  => '/modular-kitchen/straight-shape/',
		'svg'  => '<path d="M3 18h18"/>',
		'when' => 'Everything along one wall. The sensible answer in a compact flat, or in an open-plan corner where the kitchen should stay quiet.',
		'rft'  => '6–10 running feet',
	),
	array(
		'name' => 'Parallel',
		'url'  => '/modular-kitchen/parallel-shape/',
		'svg'  => '<path d="M4 7h16M4 17h16"/>',
		'when' => 'Two facing runs with a walkway between them. Efficient in a long, narrow room — cooking on one side, storage on the other.',
		'rft'  => '10–16 running feet',
	),
	array(
		'name' => 'Island',
		'url'  => '/modular-kitchen/island-shape/',
		'svg'  => '<path d="M4 4v16h16V4"/><path d="M9 12h6"/>',
		'when' => 'A free-standing counter in the middle. Needs real floor space around it, so it suits wide or open-plan kitchens.',
		'rft'  => '12–20 running feet',
	),
	array(
		'name' => 'G-shape',
		'url'  => '/modular-kitchen/g-shape/',
		'svg'  => '<path d="M20 9V4H4v16h16v-5h-6"/>',
		'when' => 'A U-shape with a return or peninsula. The most storage of the six, and the return doubles as a breakfast counter.',
		'rft'  => '14–20 running feet',
	),
);

/* Experience-centre plates. Captioned as exactly what they show: our own display
   centre. The approved pool contains no verified customer-home or factory-floor
   photography, so none is claimed. On a KITCHENS hub the plates must show
   kitchens and the drawers/hardware the copy invites you to handle — two kitchen
   displays plus the accessories run, not the reception desk and the lounge. */
$gallery = array(
	array(
		'src' => '/wp-content/uploads/2022/02/k1.jpg',
		'alt' => 'Modular kitchen display with base and wall units at the ALEA experience centre',
		'cap' => 'Kitchen display / ALEA experience centre',
		'no'  => '01',
	),
	array(
		'src' => '/wp-content/uploads/2022/03/par.jpg',
		'alt' => 'Parallel-layout modular kitchen display at the ALEA experience centre',
		'cap' => 'Parallel kitchen display / ALEA experience centre',
		'no'  => '02',
	),
	array(
		'src' => '/wp-content/uploads/2022/09/Alea-Modular-Kitchen-Wardrobes-5.jpg',
		'alt' => 'Open shelving and accessories display at the ALEA experience centre',
		'cap' => 'Accessories display / ALEA experience centre',
		'no'  => '03',
	),
);

/* FAQ — the visible answers and the JSON-LD are rendered from this ONE array,
   so the schema can never drift from the text on the page. */
$faq = array(
	array(
		'q' => 'What does an ALEA modular kitchen cost?',
		'a' => sprintf(
			'Our rates are published per square foot of cabinetry: %1$s. A %2$d-running-foot kitchen — assuming standard base and wall units, about %3$d sq ft of cabinetry per running foot — works out to roughly ₹%4$s–%5$s in the %6$s band. The estimator gives you the range for your own kitchen before we ask for your phone number.',
			implode( ', ', $band_lines ),
			$ex_rft,
			$sqft_rft,
			alea_inr( $ex_low ),
			alea_inr( $ex_high ),
			$ex_name
		),
	),
	array(
		'q' => 'How long does a modular kitchen take?',
		'a' => sprintf(
			'Installation at your home takes %1$d days. Before that your kitchen is manufactured in our own %2$s sq ft factory at %3$s, so we are never waiting on an outside workshop. Ask for the dates in writing on your free site visit.',
			(int) $f['install_days'],
			$f['factory_sqft'],
			$f['factory_place']
		),
	),
	array(
		'q' => 'Which kitchen layout should I choose?',
		'a' => 'It is decided by the room, not by preference: where the door, window, water line and gas point already are. As general guidance, one wall suits a compact kitchen, an L-shape suits most standard flats, a U or G-shape suits a kitchen that is a room of its own, and an island needs genuine floor space around it. We measure your kitchen free of charge on the site visit and tell you plainly which layouts the room will take.',
	),
	array(
		'q' => 'Is the site visit really free?',
		'a' => sprintf(
			'Yes — free, and with no obligation. We measure your kitchen at your home and you keep the measurements either way. You are also welcome to visit the factory at %1$s and watch kitchens being built before you pay anything. Call %2$s or use the form on this page.',
			$f['factory_place'],
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
	/* Page-specific: layout-grid cards and the hero's secondary text link. */
	.axp-hero-alt{margin-top:var(--sp-3);font-size:.9375rem;color:#E4E1DC}
	.axp-hero-alt a{color:inherit}
	.axp-layout__icon{width:34px;height:34px;color:var(--graphite)}
	.axp-layout__cta{margin-top:auto;padding-top:var(--sp-4)}
	</style>

	<!-- ================================================== 1. HERO -->
	<section class="ax-hero">
		<?php /* L-Shape-Kitchen-01.jpg — an L-shaped kitchen display, deliberately
		         NOT the homepage hero (aleaabout.jpg), so a visitor arriving from
		         the homepage is not shown the same photograph twice. Experience-centre
		         display photography: the alt claims a display, never a delivered
		         project, a client home or a factory floor. */ ?>
		<img
			class="ax-hero__img"
			src="<?php echo esc_url( home_url( '/wp-content/uploads/2022/02/L-Shape-Kitchen-01.jpg' ) ); ?>"
			alt="L-shaped modular kitchen display at the ALEA experience centre"
			loading="eager"
			fetchpriority="high">
		<div class="ax-hero__inner">
			<p class="ax-eyebrow">Modular kitchens &mdash; <?php echo esc_html( $f['factory_place'] ); ?></p>
			<?php /* No "factory floor" wording over a display photograph: a full-bleed
			         photo plus an overlaid headline reads as a caption, and the approved
			         pool holds no factory photography. The manufacturing claim stands
			         without a locative implication. */ ?>
			<h1 class="ax-hero__title">Kitchens made in our own factory.</h1>
			<p class="ax-hero__sub">
				<?php echo (int) count( $f['collections'] ); ?> collections, <?php echo (int) count( $layouts ); ?> layouts, one factory standard &mdash; priced in public from
				<span class="ax-mono">&#8377;<?php echo esc_html( alea_inr( $band_low ) ); ?></span> <?php echo esc_html( $price_unit ); ?> of cabinetry.
			</p>
			<div class="ax-hero__actions">
				<a class="ax-btn ax-btn--primary ax-btn--lg" href="<?php echo esc_url( $calc_url ); ?>">Get my price</a>
			</div>
			<p class="axp-hero-alt">or <a href="#alea-book">book a free site visit</a> &mdash; free, no obligation</p>
			<?php /* Hub-specific credit line — the homepage already carries the
			         factory / warranty / hardware triplet, so this one leads with
			         what this page is for. Counts are derived, never typed. */ ?>
			<p class="ax-hero__credit">
				<?php echo (int) count( $f['collections'] ); ?> COLLECTIONS
				/ <?php echo (int) count( $layouts ); ?> LAYOUTS
				/ <?php echo (int) $f['warranty_years']; ?>-YEAR WRITTEN WARRANTY
			</p>
		</div>
	</section>

	<!-- ================================================== 2. SPEC STRIP -->
	<!-- Two cells are hub-specific (collections, layouts) so this page does not
	     repeat the homepage strip verbatim; the unit label on the band comes from
	     facts.php ('price_unit'), the same source alea_price_band() composes from. -->
	<section class="ax-section ax-section--flush" aria-label="ALEA key facts">
		<div class="ax-wrap ax-wrap--flush">
			<div class="ax-specstrip">
				<div class="ax-specstrip__item">
					<span class="ax-specstrip__label">Collections</span>
					<span class="ax-specstrip__value"><?php echo (int) count( $f['collections'] ); ?><span class="ax-specstrip__unit">price bands</span></span>
				</div>
				<div class="ax-specstrip__item">
					<span class="ax-specstrip__label">Layouts</span>
					<span class="ax-specstrip__value"><?php echo (int) count( $layouts ); ?><span class="ax-specstrip__unit">shapes</span></span>
				</div>
				<div class="ax-specstrip__item">
					<span class="ax-specstrip__label">Installation</span>
					<span class="ax-specstrip__value"><?php echo (int) $f['install_days']; ?><span class="ax-specstrip__unit">days</span></span>
				</div>
				<div class="ax-specstrip__item">
					<span class="ax-specstrip__label">Published band</span>
					<span class="ax-specstrip__value">&#8377;<?php echo esc_html( alea_inr( $band_low ) ); ?>&ndash;<?php echo esc_html( alea_inr( $band_high ) ); ?><span class="ax-specstrip__unit"><?php echo esc_html( $price_unit ); ?></span></span>
				</div>
			</div>
		</div>
	</section>

	<!-- ================================================== 3. COLLECTIONS -->
	<section class="ax-section">
		<div class="ax-wrap">
			<header class="ax-head ax-reveal">
				<p class="ax-eyebrow">The range</p>
				<h2 class="ax-h2">Three collections. Every band published.</h2>
				<p class="ax-lead">
					The same factory line, the same <?php echo esc_html( $brands ); ?> hardware and the same
					<?php echo (int) $f['warranty_years']; ?>-year written warranty in all three. What changes is
					the depth of finish and fitting choice &mdash; and the rate is on the page, not behind a form.
				</p>
			</header>
			<div class="ax-grid ax-grid--3">
				<?php
				foreach ( $f['collections'] as $slug => $col ) :
					/* The image map is page-local; facts.php is the file that is meant
					   to change. A collection added there must render text-first rather
					   than emit an undefined-key warning and a broken <img>. */
					$col_img = isset( $collection_imgs[ $slug ] ) ? $collection_imgs[ $slug ] : null;
					?>
				<article class="ax-card ax-card--collection ax-reveal">
					<?php if ( $col_img ) : ?>
					<div class="ax-card__media">
						<img
							src="<?php echo esc_url( home_url( $col_img['src'] ) ); ?>"
							alt="<?php echo esc_attr( $col_img['alt'] ); ?>"
							loading="lazy">
					</div>
					<?php endif; ?>
					<div class="ax-card__inner">
						<h3 class="ax-card__name"><?php echo esc_html( $col['name'] ); ?></h3>
						<p class="ax-card__character"><?php echo esc_html( $col['character'] ); ?></p>
						<p class="ax-card__meta">
							<a class="ax-card__link" href="<?php echo esc_url( home_url( '/modular-kitchen/' . $slug . '/' ) ); ?>">
								See the <?php echo esc_html( $col['name'] ); ?> collection
							</a>
						</p>
					</div>
					<div class="ax-card__price">
						<span class="ax-card__price-label">Guide price</span>
						<span class="ax-card__price-value"><?php echo esc_html( alea_price_band( $slug ) ); ?></span>
					</div>
				</article>
				<?php endforeach; ?>
			</div>
			<p class="ax-btn-note">Rates are per square foot of cabinetry &mdash; the same rates the estimator uses</p>
		</div>
	</section>

	<!-- ================================================== 4. LAYOUTS -->
	<section class="ax-section ax-section--ruled">
		<div class="ax-wrap">
			<header class="ax-head ax-head--wide ax-reveal">
				<p class="ax-eyebrow">Six layouts</p>
				<h2 class="ax-h2">The room decides the layout. Here is how to read yours.</h2>
				<p class="ax-lead">
					General kitchen-planning guidance, not an ALEA measurement of your home &mdash; the running-foot
					figures below are typical ranges for each shape. We measure your own kitchen free of charge on the
					site visit and tell you plainly which layouts the room will take.
				</p>
			</header>
			<div class="ax-grid ax-grid--2 ax-grid--3">
				<?php foreach ( $layouts as $lay ) : ?>
				<article class="ax-card axp-layout ax-reveal">
					<svg class="axp-layout__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><?php echo wp_kses( $lay['svg'], array( 'path' => array( 'd' => true ) ) ); ?></svg>
					<h3 class="ax-card__title ax-mt-3"><?php echo esc_html( $lay['name'] ); ?></h3>
					<p class="ax-card__body"><?php echo esc_html( $lay['when'] ); ?></p>
					<p class="ax-mono--label ax-mt-3">Typically <?php echo esc_html( $lay['rft'] ); ?></p>
					<p class="axp-layout__cta">
						<a class="ax-btn ax-btn--link ax-card__link" href="<?php echo esc_url( home_url( $lay['url'] ) ); ?>">
							<?php echo esc_html( $lay['name'] ); ?> kitchens
						</a>
					</p>
				</article>
				<?php endforeach; ?>
			</div>

			<!-- id="estimate": landing anchor for the theme's site-wide sticky mobile
			     bar (.aleac-mbar "Free Estimate" button in functions.php). It lands
			     on the worked arithmetic and its "Get my price" CTA. -->
			<span id="estimate" class="ax-sr-only"></span>
			<div class="ax-grid ax-grid--split ax-mt-7">
				<div class="ax-reveal">
					<h3 class="ax-h3">What a length turns into, in rupees.</h3>
					<p class="ax-lead ax-mt-4">
						Our rates are per square foot of cabinetry, so a kitchen length becomes a price like this &mdash;
						the arithmetic is on the page, and the estimator does it for your own numbers in about a minute.
					</p>
					<div class="ax-btnrow ax-mt-5">
						<a class="ax-btn ax-btn--primary" href="<?php echo esc_url( $calc_url ); ?>">Get my price</a>
					</div>
				</div>
				<div class="ax-reveal">
					<div class="ax-spectable--rows">
						<div class="ax-spectable__row">
							<span class="ax-spectable__key">Kitchen length</span>
							<span class="ax-spectable__val"><?php echo (int) $ex_rft; ?> running feet</span>
						</div>
						<div class="ax-spectable__row">
							<span class="ax-spectable__key"><?php echo (int) $ex_rft; ?> rft &times; <?php echo (int) $sqft_rft; ?> sq ft</span>
							<span class="ax-spectable__val"><?php echo (int) $ex_sqft; ?> sq ft of cabinetry</span>
						</div>
						<div class="ax-spectable__row">
							<span class="ax-spectable__key"><?php echo esc_html( $ex_name ); ?> band</span>
							<span class="ax-spectable__val"><?php echo esc_html( alea_price_band( $ex_slug ) ); ?></span>
						</div>
						<div class="ax-spectable__row">
							<span class="ax-spectable__key">Estimated range</span>
							<span class="ax-spectable__val"><strong>&#8377;<?php echo esc_html( alea_inr( $ex_low ) ); ?>&ndash;<?php echo esc_html( alea_inr( $ex_high ) ); ?></strong></span>
						</div>
					</div>
					<p class="ax-spectable__note">
						Illustrative arithmetic only, not a delivered project &mdash; assumes standard base + wall units,
						about <?php echo (int) $sqft_rft; ?> sq ft of cabinetry per running foot. Cabinetry only, before the
						items named as not included below.
					</p>
				</div>
			</div>
		</div>
	</section>

	<!-- ================================================== 5. AS STANDARD -->
	<section class="ax-section ax-section--ruled ax-section--sheet">
		<div class="ax-wrap ax-wrap--narrow">
			<header class="ax-head ax-reveal">
				<p class="ax-eyebrow">As standard</p>
				<h2 class="ax-h2">What every ALEA kitchen comes with &mdash; and what it does not.</h2>
				<p class="ax-lead">
					The same list the estimator prices from. Everything excluded is named here and itemised
					separately on your quotation, so nothing appears later.
				</p>
			</header>
			<div class="ax-included ax-reveal">
				<div class="ax-included__col ax-included--in">
					<h3 class="ax-included__title">Included in the per-sq-ft rate</h3>
					<ul class="ax-included__list">
						<li>Cabinet carcasses and shutters, built in our own factory</li>
						<li>Machine edge banding on every panel</li>
						<li><?php echo esc_html( $brands ); ?> soft-close hinges and runners</li>
						<li>Delivery across <?php echo esc_html( $areas ); ?></li>
						<li>Installation by our own factory team</li>
						<li>A <?php echo (int) $f['warranty_years']; ?>-year written warranty</li>
					</ul>
				</div>
				<div class="ax-included__col ax-included--out">
					<h3 class="ax-included__title">Not included &mdash; itemised separately</h3>
					<ul class="ax-included__list">
						<li>Countertop &mdash; priced by material and run</li>
						<li>Chimney and hob</li>
						<li>Sink and faucet</li>
						<li>Appliances</li>
						<li>Civil work &mdash; tiling, plumbing, electrical</li>
					</ul>
				</div>
			</div>
			<p class="ax-prose ax-mt-6">
				The warranty covers panels and hardware against manufacturing defects for
				<?php echo (int) $f['warranty_years']; ?> years. The full terms are in the written document you
				receive with your kitchen &mdash; ask to read them on your free site visit, before you commit to anything.
			</p>
		</div>
	</section>

	<!-- ================================================== 6. EXPERIENCE CENTRE -->
	<!-- Honest labels: these are photographs of our own display centre. The
	     approved pool holds no verified customer-home or factory-floor
	     photography, so none is claimed here. On this hub the plates are kitchen
	     displays and the accessories run, so the pictures show what the copy
	     invites you to open. -->
	<section class="ax-section">
		<div class="ax-wrap">
			<header class="ax-head ax-reveal">
				<p class="ax-eyebrow">The experience centre</p>
				<h2 class="ax-h2">Open the drawers before you decide.</h2>
				<p class="ax-lead">
					Kitchen displays standing in our own experience centre, captioned as exactly what they show. Come and
					open the drawers and handle the <?php echo esc_html( $brands ); ?> hardware yourself &mdash; then visit the factory at
					<?php echo esc_html( $f['factory_place'] ); ?> and watch kitchens being built.
				</p>
			</header>
			<div class="ax-grid ax-grid--3">
				<?php foreach ( $gallery as $p ) : ?>
				<figure class="ax-media ax-media--43 ax-reveal">
					<span class="ax-media__frame">
						<img
							src="<?php echo esc_url( home_url( $p['src'] ) ); ?>"
							alt="<?php echo esc_attr( $p['alt'] ); ?>"
							loading="lazy">
						<span class="ax-media__tag">PLATE <?php echo esc_html( $p['no'] ); ?></span>
					</span>
					<figcaption class="ax-media__caption"><?php echo esc_html( $p['cap'] ); ?></figcaption>
				</figure>
				<?php endforeach; ?>
			</div>
			<div class="ax-btnrow ax-mt-6">
				<a class="ax-btn ax-btn--ghost" href="#alea-book">Book a free visit &mdash; see it in person</a>
			</div>
		</div>
	</section>

	<!-- ================================================== 7. FAQ -->
	<section class="ax-section ax-section--ruled">
		<div class="ax-wrap ax-wrap--narrow">
			<header class="ax-head ax-reveal">
				<p class="ax-eyebrow">Questions</p>
				<h2 class="ax-h2">Asked before every kitchen we build.</h2>
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

	<!-- ================================================== 8. FINAL CTA -->
	<section class="ax-section ax-section--ruled" id="alea-book">
		<div class="ax-wrap">
			<div class="ax-grid ax-grid--split">
				<div class="ax-reveal">
					<p class="ax-eyebrow">Book a free visit</p>
					<h2 class="ax-h2">Let us measure your kitchen &mdash; or come and see the factory.</h2>
					<p class="ax-lead ax-mt-4">
						Both are free and carry no obligation: a site visit and measurement at your home across
						<?php echo esc_html( $areas ); ?>, or a factory visit at <?php echo esc_html( $f['factory_place'] ); ?>
						where you can watch kitchens being built before you pay a rupee.
					</p>
					<div class="ax-btnrow ax-mt-5">
						<a class="ax-btn ax-btn--ink" href="<?php echo esc_url( $tel_href ); ?>">Call <?php echo esc_html( $phone_disp ); ?></a>
						<a class="ax-btn ax-btn--wa" href="<?php echo esc_url( $wa_href ); ?>">WhatsApp us</a>
					</div>
					<p class="ax-btn-note">Would rather see the number first? The estimator is free and needs no sign-up.</p>
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
