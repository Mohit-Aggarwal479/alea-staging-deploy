<?php
/**
 * ALEA — Locations hub ("The Manufacturer's Record")
 * Replaces /locations/. Included inside <main class="alea-main"> by the shell;
 * header, footer and the site-wide sticky mobile bar come from the theme.
 *
 * SINGLE-URL template: it serves one path only, so it reads no variant from
 * $GLOBALS['alea_page_args']. (Multi-URL siblings — page-city.php — whitelist
 * their variant there.)
 *
 * HONESTY CONTRACT FOR THIS PAGE (it is the entire reason the page exists):
 * - The ONLY places named as served are the ones in facts.php 'service_area',
 *   intersected with the city slugs actually routed by alea_redesign_map().
 *   The list is never typed into the copy: drop a city from facts.php and it
 *   stops being claimed here, stops being counted here and stops being linked.
 * - The site still carries LEGACY pages for other cities (Jaipur, Delhi,
 *   Srinagar, Ludhiana, Surat and more) whose service claims are NOT verified.
 *   None of them is listed, linked, counted or alluded to on this page, and no
 *   sentence here implies coverage beyond the four. That is deliberate: this
 *   template does not touch those pages, so it also does not vouch for them.
 * - Nothing city-specific is asserted beyond (a) the city's name and (b) that we
 *   serve it. No sectors, landmarks, distances, drive times, city showrooms,
 *   local project counts, per-city delivery promises or per-city pricing —
 *   none of that is verified. The one-line description on each card restates a
 *   COMPANY-WIDE fact from facts.php in that city's words, and nothing more.
 * - The factory is at Raipur Rani, in Panchkula DISTRICT — never "in Panchkula
 *   city". The string comes from facts.php 'factory_place'.
 * - Travel: the free visit is promised for the served cities only. For anywhere
 *   else the page says "call and ask" rather than inventing a travel charge, a
 *   radius or a coverage promise.
 * - Warranty is "10 years, in writing", with the terms in the written document
 *   the customer receives. Hettich and Blum are BOTH standard — neither is
 *   framed as an upgrade and no price difference between them is implied.
 *
 * IMAGES: every picture comes from images.php by key. That file holds the
 * verified description of what is actually in each frame (this media library's
 * filenames are misleading), so no path and no alt text is written here.
 * Nothing may be captioned as a delivered project, a customer's home in a named
 * city, or the factory floor — the only verified photography is of the
 * experience centre.
 */

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/facts.php';
require_once __DIR__ . '/images.php';

$f = alea_facts();

/* ---- The served list. Built by intersecting facts.php 'service_area' with the
   city slugs that page-city.php actually serves and alea_redesign_map()
   actually routes, so this hub can never link a city page that does not exist,
   nor claim a city facts.php has dropped. The whitelist is identical to the one
   in page-city.php on purpose — the two files must agree on who we serve. ---- */
$alea_city_allowed = array( 'panchkula', 'chandigarh', 'mohali', 'zirakpur' );

$cities = array();
foreach ( $f['service_area'] as $alea_area ) {
	$alea_area      = trim( (string) $alea_area );
	$alea_area_slug = strtolower( str_replace( ' ', '-', $alea_area ) );
	if ( '' !== $alea_area && in_array( $alea_area_slug, $alea_city_allowed, true ) ) {
		$cities[ $alea_area_slug ] = $alea_area;
	}
}
/* Fail safe: if facts.php and the whitelist ever stop overlapping, render
   nothing rather than a locations page that names nowhere — or worse, one that
   falls back to the unverified legacy city list. */
if ( empty( $cities ) ) {
	return;
}

/* Every count, every link and every list on this page reads THIS array, so a
   number, a card and a sentence cannot disagree on one scroll. */
$city_count = count( $cities );
$city_base  = '/locations/';   // must match alea_redesign_map() in functions.php

$calc_url   = home_url( '/kitchen-cost-calculator/' );
$visit_url  = home_url( '/book-a-free-design-visit/' );
$price_url  = home_url( '/modular-kitchen-price/' );
$factory_url = home_url( '/our-factory/' );
$tel_href   = 'tel:' . alea_fact( 'phone_tel' );
$wa_href    = alea_wa_link( "Hi ALEA, I'd like to know whether you cover my area, and get a free estimate." );
$phone_disp = alea_fact( 'phone_display' );

$sqft       = alea_fact( 'factory_sqft' );
$place      = alea_fact( 'factory_place' );
$brands     = implode( ' and ', alea_fact( 'hardware_brands', array() ) );
$warranty   = (int) alea_fact( 'warranty_years', 0 );
$install    = (int) alea_fact( 'install_days', 0 );
$sqft_rft   = (int) alea_fact( 'sqft_per_rft', 0 );
$price_unit = (string) alea_fact( 'price_unit' );

/* Names taken from the FILTERED list, never from raw service_area, so the prose
   can only ever name a place this page also links and counts.
   Two forms, both derived: the mono meta lines use the dot-separated list, and
   every SENTENCE uses $areas_and, which joins the last name with "and" so the
   copy never reads as a comma splice. Dropping a city from facts.php degrades
   both correctly ("A, B and C", then "A and B", then "A"). */
$areas_names = array_values( $cities );
$areas_dot   = implode( ' · ', $areas_names );
$areas_last  = (string) array_pop( $areas_names );   // mutates $areas_names — $areas_dot is built first on purpose
$areas_and   = $areas_names ? implode( ', ', $areas_names ) . ' and ' . $areas_last : $areas_last;

/* Published band across the whole range, derived from EVERY collection in
   facts.php rather than hard-keyed slugs, so retiring a collection there cannot
   leave this page quoting a stale range. */
$band_low  = 0;
$band_high = 0;
foreach ( $f['collections'] as $band_col ) {
	$band_low  = $band_low ? min( $band_low, (int) $band_col['from'] ) : (int) $band_col['from'];
	$band_high = max( $band_high, (int) $band_col['to'] );
}
$band_lines = array();
foreach ( $f['collections'] as $band_slug => $band_col ) {
	$band_lines[] = array(
		'name' => $band_col['name'],
		'band' => alea_price_band( $band_slug ),
	);
}
/* One worked whole-kitchen estimate via alea_kitchen_total(). The
   sq-ft-per-running-foot assumption is stated in visible text beside it. The
   example collection falls back to the first defined, so removing 'signature'
   from facts.php degrades instead of breaking. */
$ex_rft  = 12;
$ex_slug = isset( $f['collections']['signature'] ) ? 'signature' : (string) key( $f['collections'] );
$ex_name = isset( $f['collections'][ $ex_slug ]['name'] ) ? (string) $f['collections'][ $ex_slug ]['name'] : '';
list( $ex_low, $ex_high, $ex_sqft ) = alea_kitchen_total( $ex_rft, $ex_slug );

/* ---- One line per city. Each line restates a COMPANY-WIDE fact from facts.php
   in that city's own words; the only city-level assertion anywhere on the card
   is that we serve the place, which 'service_area' backs. A city present in
   facts.php but absent from this map still renders, on the shared line below —
   so adding a served city cannot produce a blank card. ---- */
$city_lines = array(
	'panchkula'  => sprintf(
		'The factory district. Our unit stands at %1$s, so a Panchkula kitchen is made in the same district it is installed in.',
		$place
	),
	'chandigarh' => sprintf(
		'Measured at your home, manufactured at the factory, installed by our own team in about %1$d days.',
		$install
	),
	'mohali'     => sprintf(
		'The same published rates as every other place we work — no separate Mohali price list, and %1$s hardware as standard.',
		$brands
	),
	'zirakpur'   => sprintf(
		'Free measurement at your home, an itemised quotation after it, and the same %1$d-year warranty in writing.',
		$warranty
	),
);
$city_line_default = sprintf(
	'Designed, measured and installed by us — the same factory, the same %1$d-year written warranty and the same published rates as everywhere else we work.',
	$warranty
);

/* ---- The two kinds of free visit. Both are company-wide offers from the
   existing pages; neither carries a time slot, an SLA or a working-hours
   promise, because facts.php holds none of those. ---- */
$visits = array(
	array(
		'title' => 'We come to you',
		'text'  => sprintf(
			'A designer visits your home anywhere in %1$s, measures the kitchen and talks the layout through with you. It is free, there is nothing to sign, and you keep the measurements whatever you decide afterwards. An itemised quotation follows — every panel, finish and fitting priced line by line.',
			$areas_and
		),
		'meta'  => 'Free / no obligation / ' . $areas_dot,
	),
	array(
		'title' => 'Or you come to the factory',
		'text'  => sprintf(
			'You are welcome at our own %1$s sq ft factory at %2$s to watch kitchens being built before you pay anything. Bring your questions about the %3$s hardware and the %4$d-year written warranty — ask to read the warranty document itself while you are there.',
			$sqft,
			$place,
			$brands,
			$warranty
		),
		'meta'  => 'Free / no obligation / call ahead on ' . $phone_disp,
	),
);

/* ---- FAQ: the visible answers and the JSON-LD render from this ONE array, so
   the schema can never drift from the text on the page. All three answers are
   about coverage, which is the only thing this page is qualified to answer.
   Each answer must stand on its own: Google can surface it with no page around
   it, so nothing here says "on this page", and nothing describes our own
   editorial process — a customer wants to know what we do, not how we write. -- */
$faq = array(
	array(
		'q' => 'Do you serve my area?',
		'a' => sprintf(
			'We design, measure and install in %1$s — those %2$d places are the ones we can promise. If you are somewhere else, call %3$s and ask before you make any arrangements: we would rather tell you plainly on the phone what we can and cannot do than take a booking we are not certain we can keep.',
			$areas_and,
			$city_count,
			$phone_disp
		),
	),
	array(
		'q' => 'Do you charge for travel to my home?',
		'a' => sprintf(
			'No — the free design visit covers %1$s, and there is nothing to pay and nothing to sign for it. We do not publish a travel charge or a coverage radius for anywhere beyond those %2$d places, so if your address is outside them, call %3$s and ask first — you will get a straight answer about your own address before anything is arranged.',
			$areas_and,
			$city_count,
			$phone_disp
		),
	),
	array(
		'q' => 'Where is the factory?',
		'a' => sprintf(
			'Our own %1$s sq ft factory is at %2$s — in the district, not in the city itself. Every ALEA kitchen and wardrobe is made there and then delivered and installed at your home. A factory visit is free and carries no obligation, so you can watch kitchens being built before you spend a rupee.',
			$sqft,
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
	/* Page-specific: the hero's secondary text link and the city-card footer.
	   The colour is the token .ax-hero itself sets (--ax-fg-muted), never a literal,
	   so a palette correction reaches this line too. */
	.axp-hero-alt{margin-top:var(--sp-3);font-size:.9375rem;color:var(--ax-fg-muted)}
	.axp-hero-alt a{color:inherit}
	.axp-city__cta{margin-top:auto;padding-top:var(--sp-4)}
	.axp-visit__meta{margin-top:var(--sp-4);padding-top:var(--sp-3);border-top:1px solid var(--ax-rule)}
	</style>

	<!-- ================================================== 1. HERO -->
	<section class="ax-hero ax-hero--short">
		<?php
		/* 'kitchen-wide' — the only landscape frame verified as ALEA's own, and
		   catalogued as banner grade. Its alt comes from images.php, so it claims
		   an experience-centre display and never a home in a named city. */
		echo alea_img( 'kitchen-wide', array( 'class' => 'ax-hero__img', 'eager' => true ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built and escaped by alea_img().
		?>
		<div class="ax-hero__inner">
			<p class="ax-eyebrow">Locations</p>
			<h1 class="ax-hero__title">Where we work</h1>
			<p class="ax-hero__sub">
				<?php echo (int) $city_count; ?> places, one factory. We design, measure and install in
				<?php echo esc_html( $areas_and ); ?> &mdash; and this page lists those and nothing else,
				because those are the ones we can promise.
			</p>
			<div class="ax-hero__actions">
				<a class="ax-btn ax-btn--primary ax-btn--lg" href="<?php echo esc_url( $calc_url ); ?>">Get my price</a>
			</div>
			<p class="axp-hero-alt">or <a href="#alea-book">book a free visit</a> &mdash; free, no obligation</p>
			<p class="ax-hero__credit">
				<?php echo (int) $city_count; ?> CITIES SERVED
				/ OWN <?php echo esc_html( $sqft ); ?> SQ FT FACTORY
				/ <?php echo (int) $warranty; ?>-YEAR WRITTEN WARRANTY
			</p>
		</div>
	</section>

	<!-- ================================================== 2. SPEC STRIP -->
	<section class="ax-section ax-section--flush" aria-label="ALEA key facts">
		<div class="ax-wrap ax-wrap--flush">
			<div class="ax-specstrip">
				<div class="ax-specstrip__item">
					<span class="ax-specstrip__label">We serve</span>
					<span class="ax-specstrip__value"><?php echo (int) $city_count; ?><span class="ax-specstrip__unit">cities</span></span>
				</div>
				<div class="ax-specstrip__item">
					<span class="ax-specstrip__label">Made at</span>
					<span class="ax-specstrip__value"><?php echo esc_html( $sqft ); ?><span class="ax-specstrip__unit">sq ft factory</span></span>
				</div>
				<div class="ax-specstrip__item">
					<span class="ax-specstrip__label">Installation</span>
					<span class="ax-specstrip__value"><?php echo (int) $install; ?><span class="ax-specstrip__unit">days</span></span>
				</div>
				<div class="ax-specstrip__item">
					<span class="ax-specstrip__label">Published band</span>
					<span class="ax-specstrip__value">&#8377;<?php echo esc_html( alea_inr( $band_low ) ); ?>&ndash;<?php echo esc_html( alea_inr( $band_high ) ); ?><span class="ax-specstrip__unit"><?php echo esc_html( $price_unit ); ?></span></span>
				</div>
			</div>
		</div>
	</section>

	<!-- ================================================== 3. ONE FACTORY, FOUR CITIES -->
	<!-- The plain statement. No coverage claim beyond the filtered service list,
	     and the factory's place string comes from facts.php verbatim. -->
	<section class="ax-section">
		<div class="ax-wrap ax-wrap--narrow">
			<header class="ax-head ax-reveal">
				<p class="ax-eyebrow">One address, then yours</p>
				<?php /* The place name is never typed into copy — it is one string in
				         facts.php ('factory_place'), so a correction there cannot leave a
				         heading behind claiming somewhere else. */ ?>
				<h2 class="ax-h2">Made at one factory. Installed at your home.</h2>
			</header>
			<div class="ax-prose ax-reveal">
				<p>
					Every ALEA kitchen and wardrobe is manufactured at our own <?php echo esc_html( $sqft ); ?> sq ft
					factory at <?php echo esc_html( $place ); ?>. Nothing is bought in from an outside workshop and
					badged as ours. From there it is delivered and installed at your home across the Tricity area:
					<?php echo esc_html( $areas_and ); ?>.
				</p>
				<p>
					Those <?php echo (int) $city_count; ?> are the places we design, measure and install in, so they are
					the only places listed on this page. If your address is outside them, call
					<?php echo esc_html( $phone_disp ); ?> and ask before you arrange anything &mdash; a straight answer
					on the phone is worth more than a map we cannot stand behind.
				</p>
				<p>
					What you get is the same wherever you are among them:
					<?php echo esc_html( $brands ); ?> hardware fitted as standard, a
					<?php echo (int) $warranty; ?>-year warranty handed over in writing, installation in about
					<?php echo (int) $install; ?> days, and the same published rates. There is no separate city price list.
				</p>
			</div>
			<div class="ax-btnrow ax-mt-5 ax-reveal">
				<a class="ax-btn ax-btn--ghost" href="<?php echo esc_url( $factory_url ); ?>">See inside the factory</a>
			</div>
		</div>
	</section>

	<!-- ================================================== 4. THE CITIES -->
	<?php /* Exactly the cities in facts.php 'service_area' that page-city.php serves.
	         The site's other, legacy city pages are NOT listed and NOT linked: their
	         service claims are unverified, and listing them here would be this page
	         vouching for them. Deliberately a PHP comment, not an HTML one — naming
	         those places, or admitting in the page source that live pages carry
	         unverified claims, is an internal note and must never reach the client. */ ?>
	<section class="ax-section ax-section--ruled">
		<div class="ax-wrap">
			<header class="ax-head ax-head--wide ax-reveal">
				<p class="ax-eyebrow">The <?php echo (int) $city_count; ?> we serve</p>
				<h2 class="ax-h2">Where we design, measure and install.</h2>
				<p class="ax-lead">
					A short list is the honest kind. Each of these has a page of its own with the same facts on it &mdash;
					same factory, same hardware, same written warranty, same published bands.
				</p>
			</header>
			<div class="ax-grid ax-grid--2 ax-grid--4">
				<?php
				foreach ( $cities as $slug => $name ) :
					$line = isset( $city_lines[ $slug ] ) ? $city_lines[ $slug ] : $city_line_default;
					?>
				<article class="ax-card ax-reveal">
					<p class="ax-mono--label">Kitchens &amp; wardrobes</p>
					<h3 class="ax-card__title ax-mt-2"><?php echo esc_html( $name ); ?></h3>
					<p class="ax-card__body"><?php echo esc_html( $line ); ?></p>
					<p class="axp-city__cta">
						<a class="ax-btn ax-btn--link ax-card__link" href="<?php echo esc_url( home_url( $city_base . $slug . '/' ) ); ?>">
							<?php echo esc_html( $name ); ?> kitchens
						</a>
					</p>
				</article>
				<?php endforeach; ?>
			</div>
			<?php /* .ax-lead, not .ax-btn-note: the latter is the mono uppercase caption
			         that sits directly under a button, and there is no button above this
			         sentence. The number is a tel: link so a phone can dial it. */ ?>
			<p class="ax-lead ax-mt-5">
				Somewhere else? Call
				<a class="ax-link" href="<?php echo esc_url( $tel_href ); ?>"><?php echo esc_html( $phone_disp ); ?></a>
				and ask &mdash; we will tell you plainly whether we can reach you.
			</p>
		</div>
	</section>

	<!-- ================================================== 5. WHAT A VISIT INVOLVES -->
	<?php /* No #estimate anchor here, deliberately. The legacy sticky bar that used
	         it (.aleac-mbar, with a "Free Estimate" button) returns early on every
	         redesigned page — functions.php bails when alea_redesign_entry() matches.
	         What actually renders is .ax-stickybar, and it holds Call and WhatsApp
	         only, so nothing on this page targets #estimate. The booking form is
	         reached through #alea-book instead. */ ?>
	<section class="ax-section ax-section--ruled ax-section--sheet">
		<div class="ax-wrap">
			<header class="ax-head ax-head--wide ax-reveal">
				<p class="ax-eyebrow">What a visit involves</p>
				<h2 class="ax-h2">Two ways to meet us. Both free, both with nothing to sign.</h2>
				<p class="ax-lead">
					Whichever you choose, you leave with measurements or answers rather than a commitment.
				</p>
			</header>
			<div class="ax-grid ax-grid--2 ax-grid--ruled ax-reveal">
				<?php foreach ( $visits as $v ) : ?>
				<div>
					<h3 class="ax-h3"><?php echo esc_html( $v['title'] ); ?></h3>
					<p class="ax-card__body"><?php echo esc_html( $v['text'] ); ?></p>
					<p class="ax-mono--label axp-visit__meta"><?php echo esc_html( $v['meta'] ); ?></p>
				</div>
				<?php endforeach; ?>
			</div>
			<div class="ax-btnrow ax-mt-6 ax-reveal">
				<a class="ax-btn ax-btn--primary ax-btn--lg" href="<?php echo esc_url( $visit_url ); ?>">Book a free design visit</a>
			</div>
			<p class="ax-btn-note">Free across <?php echo esc_html( $areas_and ); ?> &mdash; no obligation either way.</p>
		</div>
	</section>

	<!-- ================================================== 6. ONE PRICE LIST -->
	<section class="ax-section ax-section--ruled">
		<div class="ax-wrap">
			<div class="ax-grid ax-grid--split">
				<div class="ax-reveal">
					<div class="ax-head ax-mb-0">
						<p class="ax-eyebrow">One price list</p>
						<h2 class="ax-h2">The same published bands everywhere we serve.</h2>
						<p class="ax-lead ax-mt-4">
							We publish a rate per square foot of cabinetry and quote against it in
							<?php echo esc_html( $areas_and ); ?> alike. Your city does not change the rate; the finishes,
							fittings and layout you choose are what move you inside the band, and your quotation itemises
							all of it after a free measurement.
						</p>
					</div>
					<div class="ax-btnrow ax-mt-5">
						<a class="ax-btn ax-btn--ghost" href="<?php echo esc_url( $price_url ); ?>">See the published price list</a>
					</div>
				</div>
				<div class="ax-reveal">
					<div class="ax-spectable--rows">
						<?php foreach ( $band_lines as $bl ) : ?>
						<div class="ax-spectable__row">
							<span class="ax-spectable__key"><?php echo esc_html( $bl['name'] ); ?></span>
							<span class="ax-spectable__val"><?php echo esc_html( $bl['band'] ); ?></span>
						</div>
						<?php endforeach; ?>
						<div class="ax-spectable__row">
							<span class="ax-spectable__key"><?php echo (int) $ex_rft; ?> running ft &middot; <?php echo esc_html( $ex_name ); ?></span>
							<span class="ax-spectable__val"><strong>&#8377;<?php echo esc_html( alea_inr( $ex_low ) ); ?>&ndash;<?php echo esc_html( alea_inr( $ex_high ) ); ?></strong></span>
						</div>
					</div>
					<?php /* The mandated assumption travels with every whole-kitchen figure on
					         the site, in visible text right beside the number. */ ?>
					<p class="ax-spectable__note">
						Illustrative arithmetic, not a quotation &mdash; assumes standard base + wall units, about
						<?php echo (int) $sqft_rft; ?> sq ft of cabinetry per running foot, so
						<?php echo (int) $ex_rft; ?> running feet &asymp; <?php echo (int) $ex_sqft; ?> sq ft at the
						published <?php echo esc_html( $ex_name ); ?> band.
					</p>
					<p class="ax-prose ax-mt-3">
						Cabinetry only; countertop, chimney, hob, sink and appliances are itemised separately.
						<a class="ax-link" href="<?php echo esc_url( $calc_url ); ?>">Run the free estimator</a> for your own kitchen.
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
				<h2 class="ax-h2">Asked about coverage.</h2>
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

	<!-- ================================================== 8. BOOKING FORM -->
	<section class="ax-section ax-section--ruled" id="alea-book">
		<div class="ax-wrap">
			<div class="ax-grid ax-grid--split">
				<div class="ax-reveal">
					<p class="ax-eyebrow">Book a free visit</p>
					<h2 class="ax-h2">Tell us where you are.</h2>
					<p class="ax-lead ax-mt-4">
						A measurement at your home across <?php echo esc_html( $areas_and ); ?>, or a visit to the factory at
						<?php echo esc_html( $place ); ?> where you can watch kitchens being built. Both are free and
						carry no obligation. Outside those <?php echo (int) $city_count; ?> places, call and ask first.
					</p>
					<div class="ax-btnrow ax-mt-5">
						<a class="ax-btn ax-btn--ink" href="<?php echo esc_url( $tel_href ); ?>">Call <?php echo esc_html( $phone_disp ); ?></a>
						<a class="ax-btn ax-btn--wa" href="<?php echo esc_url( $wa_href ); ?>">WhatsApp us</a>
					</div>
					<p class="ax-btn-note">Would rather see a number first? The estimator is free and needs no sign-up.</p>
				</div>
				<div class="ax-form ax-form--card ax-reveal">
					<h3 class="ax-form__title">Book your free visit</h3>
					<p class="ax-form__note">Free / no obligation / <?php echo esc_html( $areas_dot ); ?></p>
					<?php echo do_shortcode( '[contact-form-7 id="7dcf010" title="Home Page form"]' ); ?>
					<p class="ax-form__fineprint ax-mt-4">
						We use your details only to arrange your visit and estimate.
					</p>
				</div>
			</div>
		</div>
	</section>

	<!-- Sticky mobile bar: provided site-wide by the theme (.ax-stickybar — Call and
	     WhatsApp only). Not rebuilt here. -->

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
