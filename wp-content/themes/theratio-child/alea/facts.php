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
				'character' => 'Made without compromise, for the kitchen you intend to keep for twenty years.',
			),
		),

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

/** Prefilled WhatsApp link. */
function alea_wa_link( $msg = '' ) {
	$f = alea_facts();
	return 'https://wa.me/' . $f['whatsapp'] . '?text=' . rawurlencode( $msg ? $msg : $f['wa_message'] );
}
