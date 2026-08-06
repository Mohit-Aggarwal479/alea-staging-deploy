<?php
/**
 * ALEA — single source of truth for every factual claim on the redesigned site.
 *
 * RULE: page templates must NEVER hard-code a business fact. Read it from here.
 * That way a claim can be corrected in one place, and nothing unverified ships.
 *
 * Provenance of each value is recorded so future edits stay honest:
 *   OWNER   — confirmed directly by the business owner (2026-07-24)
 *   SITE    — verified present on the existing live site aleamodular.com
 *   TODO    — NOT yet verified. Must not be rendered until confirmed.
 */

defined( 'ABSPATH' ) || exit;

function alea_facts() {
	static $f = null;
	if ( null !== $f ) {
		return $f;
	}

	/* ALEA kitchens since this year. 'years_experience' below is DERIVED from it
	   so the two can never disagree again: the old literal '13+' copied from the
	   live site had gone stale against 2009 (it read as 13 years for a span that
	   is now 17). No '+' suffix — the plain count is what we can defend, and
	   /about/ tells the reader 1998 and 2009 are the numbers to trust. */
	$since_alea = '2009';
	$year_now   = (int) ( function_exists( 'current_time' ) ? current_time( 'Y' ) : gmdate( 'Y' ) );

	/* The five things the per-sq-ft rate does NOT cover, written ONCE in the two
	   shapes the site needs: 'list' for the "Not included" column, 'phrase' for
	   the mid-sentence summaries. Both 'scope_excluded' and
	   'scope_excluded_prose' below are derived from this, so a page can no longer
	   name four items in a list and five in a sentence — which is exactly how the
	   faucet went missing from the calculator. */
	$excludes = array(
		array(
			'list'   => 'Countertop — priced by material and run',
			'phrase' => 'the countertop',
		),
		array(
			'list'   => 'Chimney and hob',
			'phrase' => 'the chimney and hob',
		),
		array(
			'list'   => 'Sink and faucet',
			'phrase' => 'the sink and faucet',
		),
		array(
			'list'   => 'Appliances',
			'phrase' => 'appliances',
		),
		array(
			'list'   => 'Civil work — tiling, plumbing, electrical',
			'phrase' => 'civil work such as tiling, plumbing and electrical',
		),
	);
	$excl_list   = array();
	$excl_phrase = array();
	foreach ( $excludes as $ex ) {
		$excl_list[]   = $ex['list'];
		$excl_phrase[] = $ex['phrase'];
	}

	$f = array(

		/* ---------- contact ---------- [SITE] */
		'phone_display'   => '+91 95549 95449',
		'phone_tel'       => '+919554995449',
		'whatsapp'        => '919554995449',
		'wa_message'      => "Hi ALEA, I'd like a free modular kitchen estimate.",

		/* ---------- company ---------- */
		'founded_furniture' => '1998',   // [SITE] Shiv Shakti Furniture, furniture since 1998
		'alea_since'        => $since_alea, // [SITE] "journey in 2009"
		// Derived from 'alea_since', never a literal — see the note above.
		'years_experience'  => (string) max( 0, $year_now - (int) $since_alea ),
		'factory_sqft'      => '95,000', // [SITE] "manufacturing unit of 95,000 sq. ft"
		'factory_place'     => 'Raipur Rani, Panchkula district', // [SITE] site says Raipur Rani — do NOT claim "Panchkula city"
		'service_area'      => array( 'Panchkula', 'Chandigarh', 'Mohali', 'Zirakpur' ),

		/* ---------- the four owner-confirmed differentiators ---------- [OWNER] */
		'warranty_years'    => 10,
		'warranty_written'  => true,
		'install_days'      => 15,
		'hardware_brands'   => array( 'Hettich', 'Blum' ),

		/* ---------- pricing, per SQUARE FOOT of cabinetry ---------- [OWNER 2026-07-25]
		 * Owner corrected the unit: rates are per sq ft of cabinet surface,
		 * NOT per running foot (per-rft totals were ~10x below market).   */
		'price_unit'   => 'per sq ft',
		/* Estimation assumption (shown to users, not a business fact):
		 * standard base + wall units ≈ this many sq ft of cabinetry
		 * per running foot of kitchen. */
		'sqft_per_rft' => 8,
		/* The ONE reference kitchen and tenure every "per month" figure on the
		 * site is worked from (illustrative arithmetic, not a business fact).
		 * It used to be a literal on each page and drifted: the homepage assumed
		 * a 15-running-foot kitchen and printed ₹5,667 a month for Signature
		 * while /modular-kitchen-price/ assumed 12 and printed ₹4,533 for the
		 * same collection over the same 36 months. Read from here, the two
		 * cannot disagree again. Arithmetic rule for callers: mid-point of the
		 * estimated range divided by 'emi_months', rounded with round() — ceil()
		 * puts the pages a rupee apart. */
		'example_rft'  => 12,
		'emi_months'   => 36,
		'collections' => array(
			'essential' => array(
				'name'      => 'Essential',
				'from'      => 1150,
				'to'        => 1450,
				'character' => 'Everything that matters, nothing that does not. Built to the same factory standard as every ALEA kitchen.',
			),
			'signature' => array(
				'name'      => 'Signature',
				'from'      => 1450,
				'to'        => 1950,
				'character' => 'Our most-chosen kitchen. Where the finishes start to feel considered rather than chosen from a list.',
				'featured'  => true,
			),
			'atelier'   => array(
				'name'      => 'Atelier',
				'from'      => 1950,
				'to'        => 2600,
				/* Checkable, and deliberately NOT a durability claim: the old line
				   ("for the kitchen you intend to keep for twenty years") implied a
				   service life twice the written warranty, which nothing here
				   supports, and said nothing a reader could hold us to. What is
				   left is what the band actually buys and what the quotation
				   itemises — the same substance as the Atelier bullets on
				   /modular-kitchen-price/ and the intro on /modular-kitchen/atelier/,
				   so the three cannot drift apart again. */
				'character' => 'The widest finish and fitting choice we make, and our most involved cabinetry — every panel and fitting named on your quotation.',
			),
		),

		/* ---------- scope of supply ---------- [OWNER]
		 * The included / not-included lists. /modular-kitchen-price/,
		 * /kitchen-cost-calculator/ and /modular-kitchen/ each tell the reader
		 * this is "the same list", so it is written once here and rendered from
		 * here — the three had already drifted (the calculator had lost the
		 * faucet). Placeholders are substituted by alea_scope() so the hardware,
		 * service-area and warranty facts above stay the source for those too.
		 * NOTE: no installation DURATION here on purpose — the only honest form of
		 * that figure is the hedged one composed by alea_install_window(), and a
		 * bullet reading "15-day install" would put the flat promise back. */
		'scope_included' => array(
			'Cabinet carcasses and shutters, factory-built',
			'Machine edge banding on every panel',
			'{brands} soft-close hinges and runners',
			'Delivery across {areas}',
			'Installation by our own factory team',
			'{warranty}-year written warranty',
		),
		// Derived from $excludes above — same five items, 'list' shape.
		'scope_excluded' => $excl_list,
		/* The SAME exclusions in running prose, for pages that summarise them in a
		 * sentence instead of rendering the list above (see alea_excludes_text()).
		 * DERIVED from $excludes, never a second literal, so the sentence and the
		 * list cannot name different things.
		 * CIVIL WORK is the item pages kept dropping when they retyped this by
		 * hand, which left the reader believing tiling, plumbing and electrical sat
		 * inside the per-sq-ft rate — the opposite of what /about/customer-process/
		 * tells the same reader they must finish before we start. */
		'scope_excluded_prose' => $excl_phrase,

		/* ---------- NOT VERIFIED — never render these until confirmed ---------- [TODO] */
		'unverified' => array(
			'google_rating'    => null, // rating + review count
			'projects_count'   => null, // "2,000+"
			'staff_count'      => null, // "315+ people"
			'quality_checks'   => null, // "38-point check"
			'on_time_pct'      => null, // "99.9% on-time"
			'working_hours'    => null, // the hours we actually answer the phone, e.g.
			                            // "10am–7pm, Mon–Sat". Pages currently promise
			                            // only a call back "within working hours" and
			                            // print no hours at all, because none are
			                            // confirmed. Once the owner confirms them, set
			                            // this and the promise can be made concrete.
		),
	);

	return $f;
}

/** Fetch one fact. Returns '' for anything unverified, so nothing false can render. */
function alea_fact( $key, $default = '' ) {
	$f = alea_facts();
	return isset( $f[ $key ] ) && '' !== $f[ $key ] && null !== $f[ $key ] ? $f[ $key ] : $default;
}

/**
 * Small counts as English words, for prose and headings.
 *
 * HOUSE RULE, applied across the redesign: numbers one to ten are SPELLED OUT
 * in running prose and in headings ("Three collections", "Four areas"), and
 * kept as numerals only where the mono treatment is the point — stat-strip and
 * spec-table values, the uppercase credit lines, and measurements ("15 days",
 * "10 years", "₹1,150"). Pages that derive a count with count() must pass it
 * through here before printing it in a sentence, so the same number can never
 * appear as "4 places" on one page and "Four things" on the next.
 *
 * Anything above ten falls back to the numeral, which is what the rule wants.
 *
 * @param int  $n          the count.
 * @param bool $capitalise true when the word opens a sentence or a heading.
 * @return string
 */
function alea_num_word( $n, $capitalise = false ) {
	$words = array( 1 => 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine', 'ten' );
	$n     = (int) $n;
	$w     = isset( $words[ $n ] ) ? $words[ $n ] : (string) $n;
	return $capitalise ? ucfirst( $w ) : $w;
}

/**
 * The indefinite article for a NUMERAL, chosen from how the number is spoken.
 *
 * WHY THIS EXISTS: several pages build sentences like "a %d-running-foot
 * kitchen" from data, and the article was written into the format string. That
 * is correct only while the number happens to start with a consonant sound —
 * the moment a page renders 8 or 18 it prints "a 8-running-foot kitchen", in
 * the visible copy and in the FAQPage JSON-LD with it. The article is therefore
 * derived here rather than typed, the same principle the layout and collection
 * pages already apply to their name articles.
 *
 * The rule is the spoken form, not the digit: "eight", "eighty", "eleven" and
 * "eighteen" all open on a vowel sound and take "an"; everything else takes
 * "a". Anything leading with 8 covers eight / eighty / eight hundred.
 *
 * @param int $n the number that follows the article.
 * @return string 'a' or 'an'.
 */
function alea_num_article( $n ) {
	$s = (string) (int) $n;
	return ( 0 === strpos( $s, '8' ) || '11' === $s || '18' === $s ) ? 'an' : 'a';
}

/**
 * The hardware brands as one string, in the house style.
 *
 * Both brands are standard; neither is an upgrade. The only thing that varies
 * is the connector, and it varied at random before this existed — the same page
 * carried "Hettich & Blum" in one sentence and "Hettich and Blum" in the next.
 * The rule now has one home:
 *   'prose' — "Hettich and Blum". Use in every sentence.
 *   'tight' — "Hettich & Blum". Use ONLY where the label is set in mono and
 *             space is the constraint: stat-strip values, spec-table values,
 *             eyebrows, credit lines and dot-separated meta strips.
 *
 * @param string $style 'prose' (default) or 'tight'.
 * @return string
 */
function alea_brands( $style = 'prose' ) {
	$brands = alea_fact( 'hardware_brands', array() );
	if ( ! is_array( $brands ) || ! $brands ) {
		return '';
	}
	return implode( 'tight' === $style ? ' & ' : ' and ', $brands );
}

/** Format rupees Indian-style: 145000 -> 1,45,000 */
function alea_inr( $n ) {
	$n = (int) $n;
	$s = (string) $n;
	if ( strlen( $s ) <= 3 ) {
		return $s;
	}
	$last3 = substr( $s, -3 );
	$rest  = substr( $s, 0, -3 );
	$rest  = preg_replace( '/\B(?=(\d{2})+(?!\d))/', ',', $rest );
	return $rest . ',' . $last3;
}

/** Guide-price band for a collection, e.g. "₹1,450–1,950 per sq ft". */
function alea_price_band( $slug ) {
	$f = alea_facts();
	$c = $f['collections'];
	if ( empty( $c[ $slug ] ) ) {
		return '';
	}
	return '₹' . alea_inr( $c[ $slug ]['from'] ) . '–' . alea_inr( $c[ $slug ]['to'] ) . ' ' . $f['price_unit'];
}

/**
 * Estimated total range for a kitchen, from running feet + collection.
 * Model: running feet x sqft_per_rft x per-sq-ft band. Returns array(low, high, sqft).
 */
function alea_kitchen_total( $rft, $slug ) {
	$f = alea_facts();
	$c = $f['collections'];
	if ( empty( $c[ $slug ] ) || $rft <= 0 ) {
		return array( 0, 0, 0 );
	}
	$sqft = (int) round( $rft * $f['sqft_per_rft'] );
	return array( $sqft * $c[ $slug ]['from'], $sqft * $c[ $slug ]['to'], $sqft );
}

/**
 * One column of the "what the rate includes / does not" block, as plain strings
 * ready to render as <li>s. Callers escape.
 *
 * WHY THIS EXISTS: /modular-kitchen-price/ tells the reader "the same list our
 * calculator uses" and /modular-kitchen/ says "the same list the estimator
 * prices from" — but the three pages each held their own hand-typed copy, and
 * the calculator's had lost the faucet and titled its column "quoted separately"
 * while the other two said "itemised separately". A price page that says "the
 * same list" and then shows a different list is the one thing it cannot do, so
 * the lists live in facts.php and every page renders from here.
 *
 * @param string $which 'included' or 'excluded'.
 * @return string[] Plain text lines, or array() if the fact is missing.
 */
function alea_scope( $which ) {
	$f    = alea_facts();
	$keys = array(
		'included' => 'scope_included',
		'excluded' => 'scope_excluded',
	);
	/* Anything else returns nothing rather than falling through to a column the
	   caller did not ask for — a mistyped argument must render an empty list, not
	   the exclusions under an "Included" heading. */
	if ( ! isset( $keys[ $which ] ) ) {
		return array();
	}
	$key = $keys[ $which ];
	if ( empty( $f[ $key ] ) || ! is_array( $f[ $key ] ) ) {
		return array();
	}
	$repl = array(
		'{brands}'   => implode( ' and ', (array) $f['hardware_brands'] ),
		'{areas}'    => implode( ', ', (array) $f['service_area'] ),
		'{warranty}' => (string) $f['warranty_years'],
	);
	$out = array();
	foreach ( $f[ $key ] as $line ) {
		$out[] = strtr( (string) $line, $repl );
	}
	return $out;
}

/**
 * The exclusions as one prose clause, for pages that summarise them in a
 * sentence rather than rendering 'scope_excluded' as a list (/pricing/ and
 * /kitchens/ do the latter).
 *
 * WHY THIS EXISTS: the clause was retyped on every page that summarised it and
 * kept losing CIVIL WORK, so those pages read as though tiling, plumbing and
 * electrical were inside the per-sq-ft rate. Composed here, it cannot be short.
 *
 * The sentence and the "Not included" list cannot name different things: both
 * 'scope_excluded_prose' and 'scope_excluded' are derived from the one
 * $excludes array in alea_facts(), so completeness is guaranteed upstream and
 * needs no check here.
 *
 * Degrades to '' if the fact is ever removed. A page that says nothing is
 * recoverable; a page that names four of five exclusions reads as a complete
 * list and is not — so callers must treat '' as "say nothing", never as
 * "nothing is excluded", and omit the sentence entirely.
 *
 * @return string e.g. "the countertop, the chimney and hob, the sink and
 *                faucet, appliances and civil work such as tiling, plumbing and
 *                electrical", or ''.
 */
function alea_excludes_text() {
	$items = alea_fact( 'scope_excluded_prose', array() );
	if ( ! is_array( $items ) || ! $items ) {
		return '';
	}
	$items = array_values( $items );
	$last  = array_pop( $items );
	return $items ? implode( ', ', $items ) . ' and ' . $last : $last;
}

/**
 * The installation window, HEDGED, phrased in exactly one place.
 *
 * WHY THIS EXISTS: 'install_days' is recorded above as a bare figure with no
 * anchor — it is not stated what it is measured from, and nothing here says the
 * business has committed to it as a guarantee. Pages that printed a flat
 * "15 days" were therefore making a promise the record does not support, while
 * others printed "about 15 days"; the same FAQ question ended up with two
 * different answers, both emitted into FAQPage schema. The hedge is the honest
 * form, so it is composed here and read by every page rather than typed on each.
 *
 * Degrades to '' — never "about 0 days" — if the fact is ever removed. Callers
 * must treat an empty return as "say nothing", not as a zero.
 *
 * @return string e.g. "about 15 days", or '' when install_days is missing.
 */
function alea_install_window() {
	$days = (int) alea_fact( 'install_days', 0 );
	return $days > 0 ? sprintf( 'about %d days', $days ) : '';
}

/**
 * The one sentence every page opens the installation answer with. Pages add
 * their own following clause after it; this leading clause is shared so the
 * duration cannot be worded two ways again.
 *
 * NOTE: this is the ON-SITE installation window only. It is deliberately NOT a
 * total lead time for the whole project — no such figure is recorded, and
 * /about/customer-process/ states plainly that we do not publish one.
 *
 * @return string Plain text (callers escape it), or '' when the fact is missing.
 */
function alea_install_sentence() {
	$window = alea_install_window();
	return $window ? 'Installation at your home takes ' . $window . '.' : '';
}

/** Prefilled WhatsApp link. */
function alea_wa_link( $msg = '' ) {
	$f = alea_facts();
	return 'https://wa.me/' . $f['whatsapp'] . '?text=' . rawurlencode( $msg ? $msg : $f['wa_message'] );
}
