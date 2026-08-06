<?php
/**
 * ALEA — Collection template ("The Manufacturer's Record")
 * Serves /modular-kitchen/essential|signature|atelier/ from one file.
 * Variant arrives via $GLOBALS['alea_page_args']['collection'], whitelisted
 * below with a safe default. Included inside <main class="alea-main">.
 *
 * Every business number is read from facts.php or computed from it, with the
 * estimation assumption stated in visible text. No materials/carcass specs are
 * invented: the copy says plainly that exact specifications are itemised in
 * the customer's quotation.
 */

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/facts.php';
require_once __DIR__ . '/images.php';

$f = alea_facts();

/* ---- Variant: whitelist + safe default ---- */
$alea_valid_collections = array( 'essential', 'signature', 'atelier' );
$alea_args = ( isset( $GLOBALS['alea_page_args'] ) && is_array( $GLOBALS['alea_page_args'] ) )
	? $GLOBALS['alea_page_args']
	: array();
$slug = isset( $alea_args['collection'] ) ? strtolower( trim( (string) $alea_args['collection'] ) ) : 'signature';
if ( ! in_array( $slug, $alea_valid_collections, true ) ) {
	$slug = 'signature';
}

$col       = $f['collections'][ $slug ];
$col_name  = $col['name'];
/* "an Essential", "a Signature" — the collection names are owner-set in
   facts.php, so the article is derived rather than written into the copy. */
$article   = in_array( strtoupper( substr( $col_name, 0, 1 ) ), array( 'A', 'E', 'I', 'O', 'U' ), true ) ? 'an' : 'a';
$col_index = (int) array_search( $slug, $alea_valid_collections, true ) + 1;
$band      = alea_price_band( $slug );

/* Base path for the three collection URLs. MUST stay identical to the paths
   registered in alea_redesign_map() (functions.php) — sibling links are built
   from this one string so a route and a link can never disagree again. */
$col_base = '/modular-kitchen/';

$calc_url   = home_url( '/kitchen-cost-calculator/' );
/* page-calculator.php reads ?collection= and opens on that tier (whitelisted
   against facts.php there, falling back to Signature). */
$calc_pre   = $calc_url . '?collection=' . rawurlencode( $slug );
/* This page quotes a guide band and states the written warranty, but linked
   neither the page the bands are published on nor the warranty page — which
   had no inbound link anywhere in the redesign. Both are linked from the
   blocks that already make the claim. Paths match functions.php. */
$price_url    = home_url( '/modular-kitchen-price/' );
$warranty_url = home_url( '/warranty/' );
$tel_href   = 'tel:' . alea_fact( 'phone_tel' );
$wa_href    = alea_wa_link( sprintf( "Hi ALEA, I'd like a free estimate for %s %s kitchen.", $article, $col_name ) );
$phone_disp = alea_fact( 'phone_display' );
$sqft_rft   = (int) alea_fact( 'sqft_per_rft' );

/* ---- Per-collection presentation. Copy is deliberately general and honest.
   HONESTY NOTE: facts.php holds NO cross-collection equivalence fact — it holds
   per-collection name/from/to/character plus company-wide keys (warranty_years,
   install_days, factory_sqft, factory_place). So this copy may say what is
   company-wide ONLY where those keys back it, and must never claim the
   construction, machines or hardware are identical across tiers. Materials and
   carcass specs are not invented anywhere: the quotation itemises them.
   IMAGES: only a catalogue key is stored here. The picture and its alt text come
   from images.php, which was written from what is actually in each frame — so a
   caption on this page can never contradict the photograph it sits under. Each
   key below is verified experience-centre display photography (kind
   'photo-alea'); none of it is a delivered project or a customer's home, and
   the page never says otherwise. ---- */
$presentation = array(
	'essential' => array(
		'img_key' => 'kitchen-tall',
		'desc' => array(
			sprintf(
				'Essential is the practical ALEA kitchen: laminate finishes, straightforward layouts, and every rupee going where you can see it. It is made in our own %1$s sq ft factory at %2$s and carries the same %3$d-year written warranty as every ALEA kitchen.',
				$f['factory_sqft'],
				$f['factory_place'],
				(int) $f['warranty_years']
			),
			'Choose it when you want a straightforward kitchen at the published rate, and would rather spend the difference elsewhere in the house. What you actually receive — carcass, shutters, finish and every fitting — is itemised in your quotation rather than guessed at here.',
		),
	),
	'signature' => array(
		'img_key' => 'kitchen-island',
		'desc' => array(
			'Signature is our most-chosen kitchen. The finish options are richer and more considered than Essential’s — this is the tier where the kitchen starts to feel designed for your home rather than picked from a list.',
			sprintf(
				'It sits in the middle of our published range on purpose. Like every ALEA kitchen it is made in our own factory at %1$s, installed at your home in %2$d days, and carries a %3$d-year written warranty. What that buys you at Signature — panel by panel, fitting by fitting — is set out in your quotation.',
				$f['factory_place'],
				(int) $f['install_days'],
				(int) $f['warranty_years']
			),
		),
	),
	'atelier'   => array(
		'img_key' => 'kitchen-wide',
		'desc' => array(
			'Atelier is the top of our published range: our top-tier finishes and bespoke elements, made without compromise, for the kitchen you intend to keep for twenty years.',
			sprintf(
				'Like every ALEA kitchen it is made in our own %1$s sq ft factory at %2$s and carries a %3$d-year written warranty. What goes furthest at this level is the finish and the bespoke work — and all of it, fitting by fitting, is itemised in your quotation.',
				$f['factory_sqft'],
				$f['factory_place'],
				(int) $f['warranty_years']
			),
		),
	),
);
$pres = $presentation[ $slug ];

/* ---- Illustrative totals at 8 / 10 / 12 running feet, computed from the
   published band via alea_kitchen_total(). Assumption stated visibly below. */
$examples = array();
foreach ( array( 8, 10, 12 ) as $ex_rft ) {
	list( $ex_lo, $ex_hi, $ex_sqft ) = alea_kitchen_total( $ex_rft, $slug );
	$examples[] = array(
		'rft'  => $ex_rft,
		'sqft' => (int) $ex_sqft,
		'lo'   => (int) $ex_lo,
		'hi'   => (int) $ex_hi,
	);
}
$ex_mid = $examples[1]; // the 10-rft example, reused in the FAQ

/* ---- FAQ: HTML and JSON-LD render from this ONE array so they match. ---- */
$faq = array(
	array(
		'q' => sprintf( 'What does %s %s kitchen cost?', $article, $col_name ),
		'a' => sprintf(
			'The guide price for %1$s is %2$s. For a %3$d-running-foot kitchen — assuming standard base and wall units, about %4$d sq ft of cabinetry per running foot — that works out to roughly ₹%5$s–%6$s. Use the online estimator to see the band for your own kitchen length before we ever ask for your phone number.',
			$col_name,
			$band,
			(int) $ex_mid['rft'],
			$sqft_rft,
			alea_inr( $ex_mid['lo'] ),
			alea_inr( $ex_mid['hi'] )
		),
	),
	array(
		'q' => sprintf( 'What comes with %s %s kitchen?', $article, $col_name ),
		'a' => sprintf(
			'A %1$d-year warranty in writing — covering panels and hardware against manufacturing defects, with the full terms in the written document you receive — and installation at your home in %2$d days. Every ALEA kitchen is built in our own %3$s sq ft factory at %4$s. We fit hardware from %5$s; exactly which fittings your kitchen gets is itemised in your quotation, so ask us to read it through with you on the free visit.',
			(int) $f['warranty_years'],
			(int) $f['install_days'],
			$f['factory_sqft'],
			$f['factory_place'],
			implode( ' and ', $f['hardware_brands'] )
		),
	),
	array(
		'q' => 'What decides where my kitchen lands in the band?',
		'a' => sprintf(
			'Your choice of finishes, internal fittings and accessories, and the exact layout of your kitchen. We do not price from a brochure: after a free site visit and measurement, your quotation itemises the exact specification — every panel, finish and fitting — line by line against the published %s band. The visit is free and carries no obligation.',
			$col_name
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
	/* Page-specific: the guide-price mono block only. */
	.axp-band{border-top:2px solid var(--ax-fg);padding-top:var(--sp-4)}
	.axp-band__label{display:block;margin-bottom:var(--sp-2);font-family:var(--font-mono);font-size:var(--fs-nano);letter-spacing:var(--ls-mono-wide);text-transform:uppercase;color:var(--ax-fg-muted)}
	.axp-band__value{display:block;font-family:var(--font-mono);font-weight:600;font-size:clamp(1.375rem,4.5vw,2rem);line-height:1.15;letter-spacing:.01em;color:var(--ax-fg);font-variant-numeric:tabular-nums;font-feature-settings:"tnum" 1;overflow-wrap:anywhere}
	.axp-band__note{margin-top:var(--sp-3);font-family:var(--font-mono);font-size:var(--fs-micro);line-height:1.6;letter-spacing:var(--ls-mono);text-transform:uppercase;color:var(--ax-fg-muted)}
	/* Sibling-collection rows: the whole row is the tap target, not the label. */
	.axp-sibrow{min-height:var(--tap-lg,56px);align-items:center}
	.axp-sibrow--link{text-decoration:none;color:inherit}
	.axp-sibrow--link .ax-spectable__key{color:var(--ax-fg);text-decoration:underline;text-decoration-color:var(--ax-rule);text-underline-offset:.22em}
	.axp-sibrow--link:hover .ax-spectable__key{text-decoration-color:var(--ax-fg)}
	</style>

	<!-- ============ 1. HERO ============ -->
	<section class="ax-hero ax-hero--short">
		<?php
		/* Experience-centre display photography, taken from the catalogue with its
		   verified alt. Not captioned as a delivered project or a customer's home,
		   because it is neither. alea_img() prints nothing if a key is ever
		   downgraded, so an unusable picture cannot appear here. */
		echo alea_img( $pres['img_key'], array( 'class' => 'ax-hero__img', 'eager' => true ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside alea_img().
		?>
		<div class="ax-hero__inner">
			<p class="ax-eyebrow">The range — collection <?php echo (int) $col_index; ?> of <?php echo count( $alea_valid_collections ); ?></p>
			<h1 class="ax-hero__title"><?php echo esc_html( $col_name ); ?></h1>
			<p class="ax-hero__sub"><em class="ax-serif"><?php echo esc_html( $col['character'] ); ?></em></p>
			<div class="ax-hero__actions">
				<a class="ax-btn ax-btn--primary ax-btn--lg" href="<?php echo esc_url( $calc_pre ); ?>">Get my <?php echo esc_html( $col_name ); ?> price</a>
			</div>
			<p class="ax-hero__credit">
				GUIDE PRICE <?php echo esc_html( $band ); ?>
				/ <?php echo (int) $f['warranty_years']; ?>-YEAR WRITTEN WARRANTY
				/ <?php echo (int) $f['install_days']; ?>-DAY INSTALLATION
			</p>
		</div>
	</section>

	<!-- ============ 2. GUIDE PRICE ============ -->
	<section class="ax-section ax-section--ruled-b">
		<div class="ax-wrap">
			<div class="ax-grid ax-grid--split">
				<div class="ax-head ax-mb-0 ax-reveal">
					<p class="ax-eyebrow">Guide price</p>
					<h2 class="ax-h2">The <?php echo esc_html( $col_name ); ?> band, in public.</h2>
					<p class="ax-lead ax-mt-4">
						Rates are per square foot of cabinetry, stated on the page — not hidden behind a form.
						Where your kitchen lands inside the band depends on the finishes and fittings you choose.
					</p>
				</div>
				<div class="ax-reveal">
					<p class="axp-band">
						<span class="axp-band__label">Guide price — <?php echo esc_html( $col_name ); ?></span>
						<span class="axp-band__value"><?php echo esc_html( $band ); ?></span>
						<span class="axp-band__note">Per sq ft of cabinetry &middot; exact specification itemised in your quotation &middot; <a class="ax-link" href="<?php echo esc_url( $price_url ); ?>">all three bands, published</a></span>
					</p>
				</div>
			</div>
		</div>
	</section>

	<!-- ============ 3. THE COLLECTION, HONESTLY ============ -->
	<section class="ax-section">
		<div class="ax-wrap ax-wrap--narrow">
			<div class="ax-head ax-reveal">
				<p class="ax-eyebrow">What <?php echo esc_html( $col_name ); ?> is</p>
				<h2 class="ax-h2">Made in our own factory. Specified in your quotation.</h2>
			</div>
			<div class="ax-prose ax-reveal">
				<?php foreach ( $pres['desc'] as $para ) : ?>
				<p><?php echo esc_html( $para ); ?></p>
				<?php endforeach; ?>
				<p>
					We do not print a materials table here, because we will not guess at your kitchen.
					The exact specification — carcass, shutters, finish and every fitting — is itemised
					line by line in the quotation you receive after a free measurement.
				</p>
			</div>
		</div>
	</section>

	<!-- ============ 4. WHAT IT COSTS AT YOUR LENGTH ============ -->
	<section class="ax-section ax-section--ruled">
		<div class="ax-wrap">
			<div class="ax-grid ax-grid--split">
				<div class="ax-head ax-mb-0 ax-reveal">
					<p class="ax-eyebrow">Worked examples</p>
					<h2 class="ax-h2">Three kitchen lengths, priced from the band.</h2>
					<p class="ax-lead ax-mt-4">
						Every figure below is arithmetic from the published <?php echo esc_html( $col_name ); ?> rate —
						nothing is quoted, nothing is promised. Your own kitchen gets its own itemised number.
					</p>
				</div>
				<div class="ax-reveal">
					<div class="ax-scroll-x">
						<table class="ax-spectable">
							<caption><?php echo esc_html( $col_name ); ?> &middot; estimated range by kitchen length</caption>
							<thead>
								<tr>
									<th scope="col">Kitchen</th>
									<th scope="col" class="ax-spectable__num">&asymp; Cabinetry</th>
									<th scope="col" class="ax-spectable__num">Estimated range</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ( $examples as $ex ) : ?>
								<tr>
									<th scope="row"><?php echo (int) $ex['rft']; ?> running ft</th>
									<td class="ax-spectable__num"><?php echo (int) $ex['sqft']; ?> sq ft</td>
									<td class="ax-spectable__num">&#8377;<?php echo esc_html( alea_inr( $ex['lo'] ) ); ?>&ndash;<?php echo esc_html( alea_inr( $ex['hi'] ) ); ?></td>
								</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
					<p class="ax-spectable__note">
						Assumes standard base + wall units &mdash; about <?php echo (int) $sqft_rft; ?> sq ft of cabinetry
						per running foot. Range = sq ft &times; the published
						<?php echo esc_html( $band ); ?> band. Your finishes and layout set the final figure.
					</p>
					<div class="ax-btnrow ax-mt-5">
						<a class="ax-btn ax-btn--ghost" href="<?php echo esc_url( $calc_pre ); ?>">Price my exact length</a>
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- ============ 5. STANDARD AT EVERY TIER ============ -->
	<section class="ax-section ax-section--ruled">
		<div class="ax-wrap">
			<div class="ax-grid ax-grid--split">
				<div class="ax-reveal">
					<div class="ax-head ax-mb-0">
						<p class="ax-eyebrow">Every tier</p>
						<h2 class="ax-h2">What you get on every ALEA kitchen — including this one.</h2>
					</div>
					<?php /* Experience-centre display photography — the only verified pool.
					         Image and alt come from images.php so the caption below can
					         never contradict the frame. Placed in the shared-standards
					         section on purpose: it is not captioned as this collection, as
					         a delivered project or as a customer home, because it is none
					         of those. */ ?>
					<figure class="ax-media ax-media--169 ax-mt-6">
						<span class="ax-media__frame">
							<?php echo alea_img( 'accessories' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside alea_img(). ?>
							<span class="ax-media__tag">Display</span>
						</span>
						<figcaption class="ax-media__caption">
							Shelving and accessories on display / ALEA experience centre &mdash;
							<b>open and close the fittings yourself on a free visit</b>
						</figcaption>
					</figure>
				</div>
				<ul class="ax-prooflist ax-reveal">
					<li class="ax-proof">
						<span class="ax-proof__tick" aria-hidden="true"></span>
						<div class="ax-proof__text">
							Hardware from <?php echo esc_html( implode( ' and ', $f['hardware_brands'] ) ); ?> — hinges, runners and soft-close systems from named brands, specified line by line in your quotation.
							<span class="ax-proof__never">Never generic hardware</span>
						</div>
					</li>
					<li class="ax-proof">
						<span class="ax-proof__tick" aria-hidden="true"></span>
						<div class="ax-proof__text">
							A <?php echo (int) $f['warranty_years']; ?>-year warranty, in writing — panels and hardware covered against manufacturing defects, full terms in the written document you receive. <a class="ax-link" href="<?php echo esc_url( $warranty_url ); ?>">What the warranty covers</a>, or ask to read it on your free visit.
							<span class="ax-proof__never">Never a verbal promise</span>
						</div>
					</li>
					<li class="ax-proof">
						<span class="ax-proof__tick" aria-hidden="true"></span>
						<div class="ax-proof__text">
							<?php /* facts.php holds install_days only — it holds nothing about
							         who installs, so no installer-ownership claim is made here. */ ?>
							Installation at your home in <?php echo (int) $f['install_days']; ?> days.
						</div>
					</li>
					<li class="ax-proof">
						<span class="ax-proof__tick" aria-hidden="true"></span>
						<div class="ax-proof__text">
							Built in our own <?php echo esc_html( $f['factory_sqft'] ); ?> sq ft factory at <?php echo esc_html( $f['factory_place'] ); ?> — visit and watch kitchens being made before you pay anything.
							<span class="ax-proof__never">Never outsourced</span>
						</div>
					</li>
				</ul>
			</div>
		</div>
	</section>

	<!-- ============ 6. ESTIMATOR CTA ============ -->
	<!-- id="estimate": landing anchor for the theme's site-wide sticky mobile
	     bar (.aleac-mbar "Free Estimate" button in functions.php). -->
	<section class="ax-section ax-section--ink" id="estimate">
		<div class="ax-wrap">
			<div class="ax-head ax-mb-0 ax-reveal">
				<p class="ax-eyebrow">60-second estimate</p>
				<h2 class="ax-h2">See your <?php echo esc_html( $col_name ); ?> number before we ask for yours.</h2>
				<p class="ax-lead ax-mt-4">
					The online estimator opens with <?php echo esc_html( $col_name ); ?> already selected.
					Set your kitchen length and see a live price band — before we ask for your phone number.
					You can switch collection there too, and watch the band move.
				</p>
				<div class="ax-btnrow ax-mt-5">
					<a class="ax-btn ax-btn--primary ax-btn--lg" href="<?php echo esc_url( $calc_pre ); ?>">Get my <?php echo esc_html( $col_name ); ?> price</a>
				</div>
				<p class="ax-btn-note">Free &middot; no sign-up &middot; priced off the published <?php echo esc_html( $band ); ?> band</p>
			</div>
		</div>
	</section>

	<!-- ============ 7. THE REST OF THE RANGE ============ -->
	<section class="ax-section">
		<div class="ax-wrap ax-wrap--narrow">
			<div class="ax-head ax-reveal">
				<p class="ax-eyebrow">The range</p>
				<h2 class="ax-h2">Compare the three bands.</h2>
			</div>
			<div class="ax-spectable--rows ax-reveal">
				<?php
				/* Sibling rows are whole-row links, not an 11px inline text link:
				   the row itself is the tap target. Hrefs are built from $col_base
				   — the same path string registered in alea_redesign_map() — so a
				   link and its route cannot drift apart. */
				foreach ( $alea_valid_collections as $r_slug ) :
					$r_name = $f['collections'][ $r_slug ]['name'];
					$r_band = alea_price_band( $r_slug );
					if ( $r_slug === $slug ) :
					?>
				<div class="ax-spectable__row axp-sibrow">
					<span class="ax-spectable__key"><?php echo esc_html( $r_name ); ?> &mdash; this page</span>
					<span class="ax-spectable__val"><?php echo esc_html( $r_band ); ?></span>
				</div>
					<?php else : ?>
				<a class="ax-spectable__row axp-sibrow axp-sibrow--link" href="<?php echo esc_url( home_url( $col_base . $r_slug . '/' ) ); ?>">
					<span class="ax-spectable__key"><?php echo esc_html( $r_name ); ?> &mdash; see this collection</span>
					<span class="ax-spectable__val"><?php echo esc_html( $r_band ); ?></span>
				</a>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<!-- ============ 8. FAQ ============ -->
	<section class="ax-section ax-section--ruled">
		<div class="ax-wrap ax-wrap--narrow">
			<div class="ax-head ax-reveal">
				<p class="ax-eyebrow">Questions</p>
				<h2 class="ax-h2">Asked about <?php echo esc_html( $col_name ); ?>.</h2>
			</div>
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

	<!-- ============ 9. FINAL CTA BAND ============ -->
	<section class="ax-section ax-section--ruled" id="alea-book">
		<div class="ax-wrap">
			<div class="ax-grid ax-grid--split">
				<div class="ax-reveal">
					<p class="ax-eyebrow">Book a free visit</p>
					<h2 class="ax-h2">See <?php echo esc_html( $col_name ); ?> in person, then get your itemised number.</h2>
					<p class="ax-lead ax-mt-4">
						Both are free and carry no obligation: a site visit and measurement at your home,
						or a factory visit at <?php echo esc_html( $f['factory_place'] ); ?> where you can
						watch kitchens being built.
					</p>
					<div class="ax-btnrow ax-mt-5">
						<a class="ax-btn ax-btn--ink" href="<?php echo esc_url( $tel_href ); ?>">Call <?php echo esc_html( $phone_disp ); ?></a>
						<a class="ax-btn ax-btn--wa" href="<?php echo esc_url( $wa_href ); ?>">WhatsApp us</a>
					</div>
					<p class="ax-btn-note">Prefer to type? The form works too &mdash; we reply on the number you give.</p>
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
