<?php
/**
 * ALEA — authoritative image catalogue.
 *
 * Every entry below was VIEWED (2026-08-06) and described from what is
 * actually in the frame — not from its filename. Filenames in this media
 * library are actively misleading (w2.jpg and w3.jpg are kitchens, not
 * wardrobes; L-Shape-Kitchen-01.jpg is a line diagram, not a photograph).
 *
 * RULE: templates must take images from here via alea_img(). Never hard-code
 * a path, and never write your own alt text — the alt lives with the file so
 * one description can never contradict another page's.
 *
 * 'kind' governs how a picture may be presented:
 *   photo-alea   real photography of ALEA's own experience centre — may be
 *                shown as "our experience centre". NOT a customer's home.
 *   render       a CGI design visualisation. MUST be captioned as a design
 *                render / visualisation, never as a photograph.
 *   diagram      line art. Use as a diagram only, never as a hero image.
 *   unverified   provenance unknown (likely stock). MUST NOT be presented as
 *                ALEA's own work. alea_img() refuses to return these.
 *
 * NOTE: the library contains NO wardrobe photography. Wardrobe pages must not
 * claim any image shows a wardrobe until real wardrobe photos are supplied.
 */

defined( 'ABSPATH' ) || exit;

function alea_images() {
	return array(

		/* ---------- genuine ALEA experience-centre photography ---------- */
		'kitchen-island'  => array(
			'src'  => '/wp-content/uploads/2022/09/Alea-Modular-Kitchen-Wardrobes-3.jpg',
			'alt'  => 'Kitchen display with an island and dining seating at the ALEA experience centre',
			'kind' => 'photo-alea',
			'w'    => 700, 'h' => 990, 'hero' => true,
		),
		'kitchen-tall'    => array(
			'src'  => '/wp-content/uploads/2022/09/Alea-Modular-Kitchen-Wardrobes-6.jpg',
			'alt'  => 'Kitchen display with a high dining table and tall units at the ALEA experience centre',
			'kind' => 'photo-alea',
			'w'    => 699, 'h' => 962, 'hero' => true,
		),
		'kitchen-wide'    => array(
			'src'  => '/wp-content/uploads/2022/04/aleaabout.jpg',
			'alt'  => 'Wide view of a kitchen and dining display at the ALEA experience centre',
			'kind' => 'photo-alea',
			'w'    => 1920, 'h' => 500, 'hero' => true, // letterbox — best as a banner
		),
		'reception'       => array(
			'src'  => '/wp-content/uploads/2022/09/Alea-Modular-Kitchen-Wardrobes-2.jpg',
			'alt'  => 'Reception desk beneath the ALEA sign at the experience centre',
			'kind' => 'photo-alea',
			'w'    => 700, 'h' => 962,
		),
		'timber-feature'  => array(
			'src'  => '/wp-content/uploads/2022/09/Alea-Modular-Kitchen-Wardrobes-1.jpg',
			'alt'  => 'Sculpted timber slat feature wall at the ALEA experience centre',
			'kind' => 'photo-alea',
			'w'    => 700, 'h' => 962,
		),
		'stone-desk'      => array(
			'src'  => '/wp-content/uploads/2022/09/Alea-Modular-Kitchen-Wardrobes-4.jpg',
			'alt'  => 'Sculpted stone counter and pendant lighting at the ALEA experience centre',
			'kind' => 'photo-alea',
			'w'    => 699, 'h' => 962,
		),
		'accessories'     => array(
			'src'  => '/wp-content/uploads/2022/09/Alea-Modular-Kitchen-Wardrobes-5.jpg',
			'alt'  => 'Open shelving and kitchen accessories on display at the ALEA experience centre',
			'kind' => 'photo-alea',
			'w'    => 700, 'h' => 962,
		),
		'lounge'          => array(
			'src'  => '/wp-content/uploads/2022/09/Alea-Modular-Kitchen-Wardrobes-7.jpg',
			'alt'  => 'Seating area for clients at the ALEA experience centre',
			'kind' => 'photo-alea',
			'w'    => 700, 'h' => 962,
		),

		/* ---------- CGI renders — caption as design visualisations ---------- */
		'render-parallel' => array(
			'src'  => '/wp-content/uploads/2022/03/par.jpg',
			'alt'  => 'Design visualisation of a parallel-layout modular kitchen',
			'kind' => 'render',
			'w'    => 600, 'h' => 600,
		),
		'render-u'        => array(
			'src'  => '/wp-content/uploads/2022/03/w2.jpg',
			'alt'  => 'Design visualisation of a U-shaped modular kitchen',
			'kind' => 'render',
			'w'    => 600, 'h' => 600,
		),
		'render-compact'  => array(
			'src'  => '/wp-content/uploads/2022/03/w3.jpg',
			'alt'  => 'Design visualisation of a compact kitchen run with laundry appliance',
			'kind' => 'render',
			'w'    => 600, 'h' => 600,
		),

		/* ---------- diagram ---------- */
		'diagram-l'       => array(
			'src'  => '/wp-content/uploads/2022/02/L-Shape-Kitchen-01.jpg',
			'alt'  => 'Diagram of an L-shaped kitchen layout',
			'kind' => 'diagram',
			'w'    => 750, 'h' => 673,
		),

		/* ---------- NOT ALEA'S WORK — alea_img() refuses these ---------- */
		'stock-blue'      => array(
			'src'  => '/wp-content/uploads/2022/02/k1.jpg',
			'alt'  => '',
			'kind' => 'unverified', // Western/American kitchen, snow outside — not ALEA's work
		),
		'stock-cream'     => array(
			'src'  => '/wp-content/uploads/2022/03/project.jpg',
			'alt'  => '',
			'kind' => 'unverified', // provenance unknown; must not be shown as an ALEA project
		),
	);
}

/**
 * Render an <img> from the catalogue. Returns '' for unknown or unverified
 * keys so an unusable picture can never reach a page.
 *
 * @param string $key    catalogue key
 * @param array  $args   class, sizes, eager (bool), caption_prefix
 */
function alea_img( $key, $args = array() ) {
	$all = alea_images();
	if ( empty( $all[ $key ] ) ) {
		return '';
	}
	$im = $all[ $key ];
	if ( 'unverified' === $im['kind'] || '' === $im['alt'] ) {
		return '';
	}
	$class = isset( $args['class'] ) ? $args['class'] : '';
	$eager = ! empty( $args['eager'] );

	/*
	 * Prefer WordPress's own attachment markup: it emits srcset/sizes, so a
	 * phone downloads a ~768px file instead of the full-size original (the
	 * hero was shipping 1920px to every device). The URL->ID lookup is a DB
	 * query, so the result is cached for a day. Falls back to a plain <img>
	 * if the file is not in the media library.
	 */
	$id = false;
	if ( function_exists( 'attachment_url_to_postid' ) ) {
		$ck = 'alea_att_' . md5( $im['src'] );
		$id = get_transient( $ck );
		if ( false === $id ) {
			$id = (int) attachment_url_to_postid( home_url( $im['src'] ) );
			set_transient( $ck, $id, DAY_IN_SECONDS );
		}
		$id = (int) $id;
	}

	if ( $id > 0 ) {
		$attr = array(
			'alt'      => $im['alt'], // catalogue alt always wins over the media-library one
			'class'    => $class,
			'loading'  => $eager ? 'eager' : 'lazy',
			'decoding' => 'async',
			'sizes'    => isset( $args['sizes'] ) ? $args['sizes'] : '(max-width: 700px) 100vw, 700px',
		);
		if ( $eager ) {
			$attr['fetchpriority'] = 'high';
		}
		$html = wp_get_attachment_image( $id, isset( $args['size'] ) ? $args['size'] : 'large', false, $attr );
		if ( $html ) {
			return $html;
		}
	}

	$out  = '<img src="' . esc_url( home_url( $im['src'] ) ) . '"';
	$out .= ' alt="' . esc_attr( $im['alt'] ) . '"';
	if ( $class ) {
		$out .= ' class="' . esc_attr( $class ) . '"';
	}
	if ( ! empty( $im['w'] ) ) {
		$out .= ' width="' . (int) $im['w'] . '" height="' . (int) $im['h'] . '"';
	}
	$out .= $eager ? ' loading="eager" fetchpriority="high"' : ' loading="lazy" decoding="async"';
	$out .= '>';
	return $out;
}

/** Just the URL (for CSS background-image heroes). '' if unusable. */
function alea_img_src( $key ) {
	$all = alea_images();
	if ( empty( $all[ $key ] ) || 'unverified' === $all[ $key ]['kind'] ) {
		return '';
	}
	return home_url( $all[ $key ]['src'] );
}

/** The verified alt text, for background-image heroes needing an aria-label. */
function alea_img_alt( $key ) {
	$all = alea_images();
	return empty( $all[ $key ] ) ? '' : $all[ $key ]['alt'];
}

/**
 * Intrinsic pixel size, for metadata that has to declare it (og:image:width /
 * height). Lives here with the file, like the alt text, so no template can
 * publish a size that does not match the picture. array( w, h ), or array() if
 * the catalogue does not record one.
 */
function alea_img_dims( $key ) {
	$all = alea_images();
	if ( empty( $all[ $key ]['w'] ) || empty( $all[ $key ]['h'] ) ) {
		return array();
	}
	return array( (int) $all[ $key ]['w'], (int) $all[ $key ]['h'] );
}

/** Keys of a given kind, e.g. alea_img_keys('photo-alea'). */
function alea_img_keys( $kind ) {
	$out = array();
	foreach ( alea_images() as $k => $im ) {
		if ( $im['kind'] === $kind ) {
			$out[] = $k;
		}
	}
	return $out;
}
