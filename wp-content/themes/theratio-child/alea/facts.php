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

	$f = array(

		/* ---------- contact ---------- [SITE] */
		'phone_display'   => '+91 95549 95449',
		'phone_tel'       => '+919554995449',
		'whatsapp'        => '919554995449',
		'wa_message'      => "Hi ALEA, I'd like a free modular kitchen estimate.",

		/* ---------- company ---------- */
		'founded_furniture' => '1998',   // [SITE] Shiv Shakti Furniture, furniture since 1998
		'alea_since'        => '2009',   // [SITE] "journey in 2009"
		'years_experience'  => '13+',    // [SITE] "over 13+ years"
		'factory_sqft'      => '95,000', // [SITE] "manufacturing unit of 95,000 sq. ft"
		'factory_place'     => 'Raipur Rani, Panchkula district', // [SITE] site says Raipur Rani — do NOT claim "Panchkula city"
		'service_area'      => array( 'Panchkula', 'Chandigarh', 'Mohali', 'Zirakpur' ),

		/* ---------- the four owner-confirmed differentiators ---------- [OWNER] */
		'warranty_years'    => 10,
		'warranty_written'  => true,
		'install_days'      => 15,
		'hardware_brands'   => array( 'Hettich', 'Blum' ),

		/* ---------- pricing, per running foot ---------- [OWNER] */
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

/** Guide-price band for a collection, e.g. "₹1,450–1,950 per running foot". */
function alea_price_band( $slug ) {
	$c = alea_facts()['collections'];
	if ( empty( $c[ $slug ] ) ) {
		return '';
	}
	return '₹' . alea_inr( $c[ $slug ]['from'] ) . '–' . alea_inr( $c[ $slug ]['to'] ) . ' per running foot';
}

/** Prefilled WhatsApp link. */
function alea_wa_link( $msg = '' ) {
	$f = alea_facts();
	return 'https://wa.me/' . $f['whatsapp'] . '?text=' . rawurlencode( $msg ? $msg : $f['wa_message'] );
}
