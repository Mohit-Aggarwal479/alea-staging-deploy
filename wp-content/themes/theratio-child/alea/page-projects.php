<?php
/**
 * ALEA — Projects ("The Manufacturer's Record")
 * Replaces /projects/. Included inside <main class="alea-main"> by the shell;
 * header, footer and the site-wide sticky mobile bar come from the theme.
 *
 * SINGLE-URL template: it serves one path only, so it reads no variant from
 * $GLOBALS['alea_page_args']. (Multi-URL siblings — page-collection.php —
 * whitelist their variant there.)
 *
 * HONESTY CONSTRAINT (the whole reason this page is built the way it is):
 * we hold NO verified delivered-project data — no locations, no costs, no
 * client names, no dates, and no photograph of a customer's home. So this is
 * deliberately NOT a case-study grid. It is a gallery of our own experience
 * centre, captioned as exactly that, plus a plain statement of what has not
 * been published yet. Nothing here is captioned as a delivered project, a
 * client home or a factory floor.
 *
 * Every business number is read from facts.php or computed from it in PHP,
 * with the estimation assumption stated in visible text. Nothing is hard-coded.
 */

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/facts.php';

$f = alea_facts();

$calc_url   = home_url( '/kitchen-cost-calculator/' );
$visit_url  = home_url( '/book-a-free-design-visit/' );
$tel_href   = 'tel:' . alea_fact( 'phone_tel' );
$wa_href    = alea_wa_link( "Hi ALEA, I'd like to visit and see your kitchens in person." );
$phone_disp = alea_fact( 'phone_display' );
$brands     = implode( ' and ', $f['hardware_brands'] );
$areas      = implode( ', ', $f['service_area'] );
$sqft_rft   = (int) alea_fact( 'sqft_per_rft' );

/* One worked whole-kitchen estimate, computed via alea_kitchen_total(). The
   sq-ft-per-running-foot assumption is stated in visible text beside it. The
   example collection falls back to the first one defined, so removing
   'signature' from facts.php degrades instead of breaking. */
$ex_rft  = 12;
$ex_slug = isset( $f['collections']['signature'] ) ? 'signature' : (string) key( $f['collections'] );
$ex_name = isset( $f['collections'][ $ex_slug ]['name'] ) ? (string) $f['collections'][ $ex_slug ]['name'] : '';
list( $ex_low, $ex_high, $ex_sqft ) = alea_kitchen_total( $ex_rft, $ex_slug );

/* ---- The plates.
   Every image below was opened and read against the bitmap before use, and the
   alt says only what is visible in the frame. All seven were shot in one
   location — our own experience centre, identifiable by the ALEA sign in plate
   06 — so "experience centre" is a description, not a claim.

   Six files in the wider media library are deliberately not used on this page.
   Each was opened and checked (2026-08-05); what the bitmap actually contains:
     2022/02/L-Shape-Kitchen-01.jpg  white line art on a black ground — a
                                     layout diagram, not a photograph
     2022/02/k1.jpg                  blue-painted kitchen, bare winter woodland
                                     through the windows
     2022/03/par.jpg                 CGI render, parallel kitchen
     2022/03/w2.jpg                  CGI render, U-shaped KITCHEN (not a
                                     wardrobe, despite the filename)
     2022/03/w3.jpg                  CGI render, kitchenette with a washing
                                     machine (not a wardrobe either)
     2022/03/project.jpg             an unidentified kitchen; the filename is
                                     not evidence, and nothing else ties it to
                                     ALEA
   The pool therefore holds no verified wardrobe photograph and no verified
   customer home, so this page publishes neither rather than captioning a render
   as a display. This list is a record of what was checked, not a rule: restore
   a file the moment a verified photograph replaces it. */
$plates = array(
	array(
		'src' => '/wp-content/uploads/2022/09/Alea-Modular-Kitchen-Wardrobes-3.jpg',
		'alt' => 'Island kitchen display with a dining extension, tall units and built-in ovens at the ALEA experience centre',
		'cap' => 'Island kitchen with a dining extension, tall units and built-in ovens',
		'no'  => '01',
	),
	array(
		'src' => '/wp-content/uploads/2022/09/Alea-Modular-Kitchen-Wardrobes-6.jpg',
		'alt' => 'Kitchen display with a high table, extractor and tall units at the ALEA experience centre',
		'cap' => 'Kitchen display with a high table, extractor and tall units',
		'no'  => '02',
	),
	array(
		'src' => '/wp-content/uploads/2022/09/Alea-Modular-Kitchen-Wardrobes-5.jpg',
		'alt' => 'Open shelving display holding crockery and accessories at the ALEA experience centre',
		'cap' => 'Open shelving holding crockery and accessories',
		'no'  => '03',
	),
	array(
		'src' => '/wp-content/uploads/2022/09/Alea-Modular-Kitchen-Wardrobes-1.jpg',
		'alt' => 'Sculpted timber slat panel at the ALEA experience centre',
		'cap' => 'Sculpted timber slat panel, joinery rather than a kitchen',
		'no'  => '04',
	),
	array(
		'src' => '/wp-content/uploads/2022/09/Alea-Modular-Kitchen-Wardrobes-4.jpg',
		'alt' => 'Curved counter with a stone top and shaped timber ribs beneath it at the ALEA experience centre',
		'cap' => 'Curved counter, stone top over shaped timber ribs',
		'no'  => '05',
	),
	array(
		'src' => '/wp-content/uploads/2022/09/Alea-Modular-Kitchen-Wardrobes-2.jpg',
		'alt' => 'Reception counter with a slatted timber front below the ALEA sign at the experience centre',
		'cap' => 'Reception counter below the ALEA sign',
		'no'  => '06',
	),
	array(
		'src' => '/wp-content/uploads/2022/09/Alea-Modular-Kitchen-Wardrobes-7.jpg',
		'alt' => 'Seating area with a leather sofa against a stone-clad wall at the ALEA experience centre',
		'cap' => 'Seating area, where the drawings get read',
		'no'  => '07',
	),
);

/* The hero is itself an experience-centre photograph, so it is held here rather
   than typed into the markup — and it is COUNTED. On a page whose premise is
   that every picture can be checked, the published figure has to survive being
   counted off the screen: it is the plates plus this one, never count($plates)
   on its own. */
$hero = array(
	'src' => '/wp-content/uploads/2022/04/aleaabout.jpg',
	'alt' => 'Kitchen display with a dining table, built-in ovens and tall units at the ALEA experience centre',
);
$photo_total = count( $plates ) + 1;

/* Responsive delivery. The plate originals are 700x962 and the hero is 1920x500,
   all of it landing in boxes a third that width on a phone. Where WordPress
   knows the file, hand rendering to wp_get_attachment_image() and inherit
   srcset/sizes/width/height from the media library; where it does not, fall back
   to a plain <img> carrying explicit width/height so the layout still cannot
   shift. Guarded because a template can legitimately be included twice. */
if ( ! function_exists( 'alea_projects_img' ) ) {
	function alea_projects_img( $path, $alt, $w, $h, $eager = false, $class = '' ) {
		$url  = home_url( $path );
		$attr = array(
			'alt'      => (string) $alt,
			'loading'  => $eager ? 'eager' : 'lazy',
			'decoding' => 'async',
		);
		if ( $eager ) {
			$attr['fetchpriority'] = 'high';
		}
		if ( '' !== $class ) {
			$attr['class'] = $class;
		}
		$id = function_exists( 'attachment_url_to_postid' ) ? (int) attachment_url_to_postid( $url ) : 0;
		if ( $id ) {
			$html = wp_get_attachment_image( $id, 'full', false, $attr );
			if ( $html ) {
				return $html;
			}
		}
		$attr['width']  = (int) $w;
		$attr['height'] = (int) $h;
		$out = '<img src="' . esc_url( $url ) . '"';
		foreach ( $attr as $k => $v ) {
			$out .= ' ' . $k . '="' . esc_attr( $v ) . '"';
		}
		return $out . '>';
	}
}

/* FAQ — the visible answers and the JSON-LD are rendered from this ONE array,
   so the schema can never drift from the text on the page. */
$faq = array(
	array(
		'q' => 'Can I see finished ALEA kitchens before I order?',
		'a' => sprintf(
			'Yes. Complete kitchens stand in our experience centre and you are welcome to open every drawer and handle the %1$s hardware that is fitted as standard. Every photograph on this page was taken there, which is why each one is captioned as a display rather than as somebody’s home. Come and see it before you commit to anything — the visit is free and carries no obligation.',
			$brands
		),
	),
	array(
		'q' => 'Can I visit the factory?',
		'a' => sprintf(
			'Yes, and it is free. Our manufacturing unit is at %1$s and you are welcome to walk it and watch kitchens being built before you pay a rupee. Call %2$s or use the form on this page and we will fix a time. We have not published photographs of the shop floor yet, so we are inviting you to see it rather than showing you pictures of it.',
			$f['factory_place'],
			$phone_disp
		),
	),
	array(
		'q' => 'Do you share past clients’ details or photographs of their homes?',
		'a' => 'No. We do not publish customer names, addresses or photographs of their kitchens, and we will not pass your details to anyone else either. That is also the honest reason there is no delivered-project gallery on this page yet: we are collecting photographs and written permission from customers first, and when they are signed off we will publish them and say plainly whose kitchen you are looking at. Your own details are used only to arrange your visit and estimate.',
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
	/* Page-specific: the "not published yet" note and the hero's secondary link.
	   No colour is declared here — .axp-hero-alt rides on .ax-hero__sub, which
	   already owns the hero's body colour, so the palette stays in one file. */
	.axp-pending{padding:var(--sp-4);border:1px solid var(--ax-rule);border-left:3px solid var(--ax-fg);font-family:var(--font-mono);font-size:var(--fs-micro);line-height:1.75;letter-spacing:var(--ls-mono);text-transform:uppercase;color:var(--ax-fg-muted)}
	.axp-pending b{color:var(--ax-fg);font-weight:600}
	.axp-hero-alt{margin-top:var(--sp-3);font-size:.9375rem}
	.axp-hero-alt a{display:inline-flex;align-items:center;min-height:var(--tap)}
	</style>

	<!-- ================================================== 1. HERO -->
	<section class="ax-hero ax-hero--short">
		<?php
		/* aleaabout.jpg — a wide view of a kitchen display in our own experience
		   centre (same room as plate 02). Experience-centre photography: the alt
		   claims a display, never a delivered project, a client home or a factory
		   floor. Counted in $photo_total, because it is a photograph on this page. */
		echo alea_projects_img( $hero['src'], $hero['alt'], 1920, 500, true, 'ax-hero__img' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- attrs escaped in helper.
		?>
		<div class="ax-hero__inner">
			<p class="ax-eyebrow">Our work</p>
			<h1 class="ax-hero__title">Photographs, not claims.</h1>
			<p class="ax-hero__sub">
				Every picture on this page was taken in our own experience centre, and is captioned
				as exactly that. We have not published a gallery of delivered kitchens yet &mdash;
				because those are customers&rsquo; homes, and they are not ours to put online.
			</p>
			<div class="ax-hero__actions">
				<a class="ax-btn ax-btn--primary ax-btn--lg" href="<?php echo esc_url( $visit_url ); ?>">Book a free visit</a>
			</div>
			<?php /* Secondary path is a real target, not an 11px inline link:
			         .axp-hero-alt a carries min-height:var(--tap). */ ?>
			<p class="ax-hero__sub axp-hero-alt">or <a href="#alea-book">use the form below</a> &mdash; free, no obligation</p>
			<p class="ax-hero__credit">
				<?php echo (int) $photo_total; ?> PHOTOGRAPHS
				/ ALL AT OUR EXPERIENCE CENTRE
				/ NONE STAGED AS A CUSTOMER HOME
			</p>
		</div>
	</section>

	<!-- ================================================== 2. SPEC STRIP -->
	<!-- Every cell is a statement about this page's own evidence, not a business
	     claim: two are derived counts, two are the honest status of work we have
	     not published. Nothing here is a number about projects delivered. -->
	<section class="ax-section ax-section--flush" aria-label="What this gallery is">
		<div class="ax-wrap ax-wrap--flush">
			<div class="ax-specstrip">
				<?php /* Counts the hero too — every photograph on the page, so the
				         figure survives being counted off the screen. The gallery's
				         own subtotal is stated on the gallery, where it applies. */ ?>
				<div class="ax-specstrip__item">
					<span class="ax-specstrip__label">Photographs published</span>
					<span class="ax-specstrip__value"><?php echo (int) $photo_total; ?><span class="ax-specstrip__unit"><?php echo (int) count( $plates ); ?> in the gallery</span></span>
				</div>
				<div class="ax-specstrip__item">
					<span class="ax-specstrip__label">Photographed at</span>
					<span class="ax-specstrip__value">Our own<span class="ax-specstrip__unit">experience centre</span></span>
				</div>
				<div class="ax-specstrip__item">
					<span class="ax-specstrip__label">Delivered-project gallery</span>
					<span class="ax-specstrip__value">Pending<span class="ax-specstrip__unit">owner sign-off</span></span>
				</div>
				<div class="ax-specstrip__item">
					<span class="ax-specstrip__label">Seeing it in person</span>
					<span class="ax-specstrip__value">Free<span class="ax-specstrip__unit">no obligation</span></span>
				</div>
			</div>
		</div>
	</section>

	<!-- ================================================== 3. WHAT THIS PAGE IS -->
	<section class="ax-section">
		<div class="ax-wrap">
			<div class="ax-grid ax-grid--split">
				<div class="ax-reveal">
					<header class="ax-head ax-mb-0">
						<p class="ax-eyebrow">Read this first</p>
						<h2 class="ax-h2">What you are looking at &mdash; and what we have not published.</h2>
					</header>
					<div class="ax-prose ax-mt-5">
						<p>
							Most kitchen websites open this page with a grid of &ldquo;projects&rdquo; and a
							counter. We are not going to do that, because we cannot show you the paperwork
							behind it. What we can show you is our own experience centre: full kitchens,
							storage and joinery standing where you can walk up and open them.
						</p>
						<p>
							So every photograph below is captioned as a display, with a description of what
							is actually in the frame &mdash; no locality, no cost, no customer, no date. If a
							caption does not claim it, we could not verify it.
						</p>
					</div>
				</div>
				<div class="ax-reveal">
					<?php /* delivered-project gallery pending: needs verified photos + owner sign-off */ ?>
					<!-- delivered-project gallery pending: needs verified photos + owner sign-off -->
					<?php /* Two paragraphs, not one paragraph split by <br><br>: they are
					         separate statements to a screen reader as well as to the eye. */ ?>
					<div class="axp-pending ax-stack--sm">
						<p>
							<b>Not on this page yet:</b> photographs of delivered kitchens in customers&rsquo;
							homes. Those need the customer&rsquo;s written permission and our owner&rsquo;s
							sign-off before they go online, and we are collecting both.
						</p>
						<p>
							<b>Also not published:</b> project locations, project costs, customer names and
							dates &mdash; and photographs of the factory floor. Until each of those is verified
							it stays off the site. You are welcome to come and see all of it in person instead.
						</p>
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- ================================================== 4. THE GALLERY -->
	<!-- Honest labels: these are photographs of our own display centre. The
	     approved pool holds no verified customer-home or factory-floor
	     photography, so none is claimed. Captions describe only what is visible
	     in each frame. -->
	<section class="ax-section ax-section--ruled">
		<div class="ax-wrap">
			<header class="ax-head ax-head--wide ax-reveal">
				<p class="ax-eyebrow"><?php echo (int) count( $plates ); ?> plates &mdash; experience centre</p>
				<h2 class="ax-h2">Kitchens and joinery you can walk up to.</h2>
				<?php /* HONESTY: facts.php backs "we fit Hettich and Blum as standard"
				         as a COMPANY-WIDE claim. It backs nothing about what is fitted
				         to these seven display units, and no caption claims it — so the
				         sentence stays company-wide and sends you to the room, rather
				         than asserting a property of the photographed frames. */ ?>
				<p class="ax-lead">
					Shot in our own experience centre. Every one of these is standing there now, so the
					fair test of these photographs is simple: come and see whether the real thing matches
					them. We fit <?php echo esc_html( $brands ); ?> as standard &mdash; open the drawers on
					the kitchens standing in the experience centre and handle the hardware yourself.
				</p>
			</header>
			<?php /* .ax-rail, not a 1-column grid: seven 4:3 figures stacked at 375px is
			         roughly 2,200px of uninterrupted image scroll. The rail snaps
			         horizontally on a phone and becomes a grid from 768px. */ ?>
			<div class="ax-rail">
				<?php foreach ( $plates as $p ) : ?>
				<figure class="ax-media ax-media--43 ax-reveal">
					<span class="ax-media__frame">
						<?php echo alea_projects_img( $p['src'], $p['alt'], 700, 962 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- attrs escaped in helper. ?>
						<span class="ax-media__tag">Plate <?php echo esc_html( $p['no'] ); ?> / display</span>
					</span>
					<figcaption class="ax-media__caption">
						<?php echo esc_html( $p['cap'] ); ?>
						<b>&mdash; ALEA experience centre</b>
					</figcaption>
				</figure>
				<?php endforeach; ?>
			</div>
			<p class="ax-btn-note">
				No render, no stock photograph and no customer&rsquo;s home appears above &mdash;
				a picture we could not stand behind was left out rather than captioned vaguely.
			</p>
		</div>
	</section>

	<!-- ================================================== 5. SEE IT IN PERSON -->
	<?php /* Sheet ground, not ink: the olive proof tick is a 3.5:1 glyph colour
	         that all but disappears on the ink ground, and the design system
	         allows no substitute. */ ?>
	<section class="ax-section ax-section--ruled ax-section--sheet">
		<div class="ax-wrap">
			<div class="ax-grid ax-grid--split">
				<div class="ax-reveal">
					<header class="ax-head ax-mb-0">
						<p class="ax-eyebrow">In person</p>
						<h2 class="ax-h2">The gallery we would rather you judged us on.</h2>
					</header>
					<p class="ax-lead ax-mt-4">
						Two visits, both free, both with no obligation: the experience centre, where the
						kitchens above are standing; or the factory at
						<?php echo esc_html( $f['factory_place'] ); ?>, where you can watch kitchens being
						built before you pay a rupee. We measure at your home free of charge too, across
						<?php echo esc_html( $areas ); ?>.
					</p>
					<?php /* Ghost, not primary. The hero already carries this exact label
					         and href, and section 6's "Get my price" sits one scroll away —
					         on a 1280x800 desktop the two can share a screen, because
					         .ax-grid--split centres this shorter column against the
					         4-item prooflist. One primary per screen, so this one yields. */ ?>
					<div class="ax-btnrow ax-mt-5">
						<a class="ax-btn ax-btn--ghost ax-btn--lg" href="<?php echo esc_url( $visit_url ); ?>">Book a free visit</a>
					</div>
					<p class="ax-btn-note">Free &middot; no obligation &middot; you keep the measurements either way</p>
				</div>
				<ul class="ax-prooflist ax-reveal">
					<li class="ax-proof">
						<span class="ax-proof__tick" aria-hidden="true"></span>
						<div class="ax-proof__text">
							<?php /* Company-wide claim, as facts.php backs it — not a claim about
							         what is fitted to the seven photographed display units. */ ?>
							We fit <?php echo esc_html( $brands ); ?> as standard. Open the drawers on the
							kitchens standing in the experience centre and handle them yourself.
							<span class="ax-proof__never">Never a photograph you cannot check</span>
						</div>
					</li>
					<li class="ax-proof">
						<span class="ax-proof__tick" aria-hidden="true"></span>
						<div class="ax-proof__text">
							Walk our own <?php echo esc_html( $f['factory_sqft'] ); ?> sq ft factory at
							<?php echo esc_html( $f['factory_place'] ); ?> and watch kitchens being made.
							<span class="ax-proof__never">Never outsourced</span>
						</div>
					</li>
					<li class="ax-proof">
						<span class="ax-proof__tick" aria-hidden="true"></span>
						<div class="ax-proof__text">
							Ask to read the <?php echo (int) $f['warranty_years']; ?>-year written warranty
							before you commit &mdash; the full terms are in the written document you receive.
							<span class="ax-proof__never">Never a verbal promise</span>
						</div>
					</li>
					<li class="ax-proof">
						<span class="ax-proof__tick" aria-hidden="true"></span>
						<div class="ax-proof__text">
							Installation at your home in <?php echo (int) $f['install_days']; ?> days.
							ALEA has been making kitchens for <?php echo esc_html( $f['years_experience'] ); ?>
							years, since <?php echo esc_html( $f['alea_since'] ); ?>, out of a furniture
							business that started in <?php echo esc_html( $f['founded_furniture'] ); ?>.
						</div>
					</li>
				</ul>
			</div>
		</div>
	</section>

	<!-- ================================================== 6. PRICE INSTEAD OF PROOF-BY-GALLERY -->
	<!-- id="estimate": landing anchor for the theme's site-wide sticky mobile bar
	     (.aleac-mbar "Free Estimate" button in functions.php). It lands on the
	     worked arithmetic and its "Get my price" CTA. -->
	<section class="ax-section ax-section--ruled" id="estimate">
		<div class="ax-wrap">
			<div class="ax-grid ax-grid--split">
				<div class="ax-reveal">
					<header class="ax-head ax-mb-0">
						<p class="ax-eyebrow">Instead of project costs</p>
						<h2 class="ax-h2">We will not print other people&rsquo;s numbers. Here is yours.</h2>
					</header>
					<p class="ax-lead ax-mt-4">
						A gallery of past projects with prices attached would need those prices verified
						and those customers&rsquo; permission, and we have neither. What we can publish is
						our own rate card and the arithmetic that runs off it &mdash; the same rates the
						estimator uses, on the page, before we ask for your phone number.
					</p>
					<div class="ax-btnrow ax-mt-5">
						<a class="ax-btn ax-btn--primary" href="<?php echo esc_url( $calc_url ); ?>">Get my price</a>
					</div>
					<p class="ax-btn-note">Free &middot; no sign-up &middot; about a minute</p>
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
						Illustrative arithmetic only, not a delivered project &mdash; assumes standard base
						+ wall units, about <?php echo (int) $sqft_rft; ?> sq ft of cabinetry per running
						foot. Cabinetry only; countertop, chimney, hob, sink, faucet and appliances are
						itemised separately on your quotation.
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
				<h2 class="ax-h2">Asked about our work.</h2>
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
					<h2 class="ax-h2">Come and inspect the work yourself.</h2>
					<p class="ax-lead ax-mt-4">
						Both visits are free and carry no obligation: the experience centre and factory at
						<?php echo esc_html( $f['factory_place'] ); ?>, or a measurement at your own home
						across <?php echo esc_html( $areas ); ?>.
					</p>
					<div class="ax-btnrow ax-mt-5">
						<a class="ax-btn ax-btn--ink" href="<?php echo esc_url( $tel_href ); ?>">Call <?php echo esc_html( $phone_disp ); ?></a>
						<a class="ax-btn ax-btn--wa" href="<?php echo esc_url( $wa_href ); ?>">WhatsApp us</a>
					</div>
					<?php /* The offer is now a link. Section 6 is the last calculator CTA
					         above this point, so an unlinked mention here dead-ends. */ ?>
					<p class="ax-btn-note">Would rather see the number first? <a href="<?php echo esc_url( $calc_url ); ?>">The estimator</a> is free and needs no sign-up.</p>
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
