<?php
/**
 * ALEA — the manufacturing sequence, defined ONCE.
 *
 * WHY THIS FILE EXISTS: /our-factory/ (page-factory.php) and the manufacturing
 * process page (page-manufacturing.php) both describe the same sequence. When
 * each held its own copy they drifted — one said "Five stages", the other
 * rendered count() over six — so a reader one click away got two official
 * answers. The sequence now lives here and both pages read it, which means the
 * stage count and the wording can never disagree again.
 *
 * WHAT EACH PAGE DOES WITH IT:
 *   /our-factory/  — the PLACE. Renders 'title' + 'text' only, as context for
 *                    the free visit.
 *   manufacturing  — the PROCESS. Renders the same 'title' + 'text' plus the
 *                    'means' rider, which is what each stage changes for the
 *                    buyer once they are living with the kitchen.
 *
 * HONESTY RULES THIS ARRAY WORKS UNDER:
 * - NO machine names, machine counts, brand names of plant, line speeds,
 *   cycle times, tolerances, capacity figures or quality-check counts. None of
 *   that is in facts.php and none of it is checkable by a reader, so none of it
 *   is claimed here.
 * - Every stage is GENERAL manufacturing practice described in plain language.
 *   The only ALEA-specific claims are the ones facts.php carries — the own
 *   factory, its size and place, and the hardware brands.
 * - Hettich and Blum are BOTH standard. Neither is framed as a paid upgrade and
 *   no price difference between them is implied, because none is verified.
 *
 * PLAIN TEXT, NOT MARKUP: every string below is PLAIN TEXT and both pages pass
 * it through esc_html(). Punctuation is written as real characters, never as
 * HTML entities, or the entity would show up literally on the page.
 */

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/facts.php';

/**
 * The canonical manufacturing sequence.
 *
 * @return array<int,array{title:string,text:string,means:string}>
 */
function alea_sequence() {
	static $seq = null;
	if ( null !== $seq ) {
		return $seq;
	}

	$brands     = alea_fact( 'hardware_brands', array() );
	$brands_txt = implode( ' and ', $brands );

	$seq = array(
		array(
			'title' => 'Design and measurement sign-off',
			'text'  => 'Before anything is cut, your kitchen is measured at your home and drawn up, and the quotation is itemised line by line rather than given as one lump sum. You sign the drawing off. That signed drawing is what the factory works from.',
			'means' => 'Nothing is cut from a catalogue assumption about your room',
		),
		array(
			'title' => 'Panel cutting',
			'text'  => 'Full sheets of board are cut down to the exact panels on your kitchen’s cutting list. Cutting by machine to a list is what keeps every panel square and every repeated part identical — the difference you notice later as doors and drawer fronts that line up with each other.',
			'means' => 'Square panels now are aligned doors later',
		),
		array(
			'title' => 'Edge banding',
			'text'  => 'Every cut edge is sealed with edge banding under heat and pressure. It is the least glamorous stage and one of the most important in an Indian kitchen: an unsealed edge is where steam and spills get into the board, and where cheap furniture starts to swell.',
			'means' => 'Sealed edges are what stand up to steam and spills',
		),
		array(
			'title' => 'Drilling and hardware preparation',
			'text'  => 'Hinge cups, shelf pins and runner positions are drilled to set positions rather than marked out by eye, and the ' . $brands_txt . ' hardware for your kitchen is drawn from stock and prepared alongside the panels it belongs to.',
			'means' => 'Hardware seats square, so doors hang true from day one',
		),
		array(
			'title' => 'Assembly and finishing',
			'text'  => 'Panels become carcasses, shutters are finished, and hinges and runners are fitted and adjusted here — in the factory, on a bench, not out of a toolbox in your flat. It is why fitting day at your home is fitting rather than carpentry.',
			'means' => 'Less cutting and drilling inside your home on install day',
		),
		array(
			'title' => 'Checking, packing and dispatch',
			'text'  => 'Each unit is checked against your order — sizes, finish and the action of the hardware — then packed and dispatched for installation. Anything that is not right is corrected here, where correcting it is easy, instead of in your kitchen after the tiles are on.',
			'means' => 'Problems are found in the factory, not in your kitchen',
		),
	);

	return $seq;
}

/**
 * How many stages the sequence has. Both pages print this rather than a typed
 * number, so neither can ever say "five" while listing six.
 *
 * @return int
 */
function alea_sequence_count() {
	return count( alea_sequence() );
}

/**
 * The stage count as an English word, for headlines that read badly with a
 * numeral. Falls back to the numeral for any count the list does not cover.
 *
 * @param int $n stage count.
 * @return string
 */
function alea_sequence_count_word( $n ) {
	$words = array( 1 => 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine', 'Ten' );
	$n     = (int) $n;
	return isset( $words[ $n ] ) ? $words[ $n ] : (string) $n;
}
