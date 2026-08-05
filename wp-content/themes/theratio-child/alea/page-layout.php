<?php
/**
 * ALEA — Kitchen layout template ("The Manufacturer's Record")
 * Serves /modular-kitchen/{l-shape|u-shape|g-shape|straight-shape|parallel-shape|island-shape}/
 * from one file. Variant arrives via $GLOBALS['alea_page_args']['layout'],
 * whitelisted below with a safe default. Included inside <main class="alea-main">;
 * header, footer and the site-wide sticky mobile bar come from the theme.
 *
 * HONESTY NOTE: everything on this page is either (a) a business fact read from
 * facts.php, (b) arithmetic computed from facts.php with the assumption stated in
 * visible text, (c) GENERAL kitchen-planning guidance, framed as guidance in
 * the visible copy — never as an ALEA measurement of anyone's home, or (d) an
 * image drawn from the verified catalogue in images.php, which also owns its alt
 * text. The running-foot ranges are typical industry ranges for each shape,
 * labelled as such everywhere they appear. No project counts, testimonials,
 * materials lists, machine lists or local landmarks are invented anywhere.
 */

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/facts.php';
require_once __DIR__ . '/images.php';

$f = alea_facts();

/* ---- The six layouts. Order here IS the order of the sibling nav grid.
 *
 * 'calc' is the value handed to /kitchen-cost-calculator/?shape= . It is NOT the
 * URL slug: page-calculator.php whitelists its own alias table, which knows
 * 'straight' / 'parallel' / 'island' but NOT 'straight-shape' / 'parallel-shape'
 * / 'island-shape'. Passing the URL slug would silently fall back to L-shape, so
 * the canonical calculator key is stored explicitly per layout.
 *
 * 'rft_lo' / 'rft_hi' are TYPICAL RANGES — general planning guidance, stated as
 * guidance in every place they are rendered. They are not a measurement.
 *
 * 'svg' reuses the line-drawn icon vocabulary from page-calculator.php verbatim,
 * so a shape drawn in the estimator and the same shape drawn here always match.
 *
 * 'img' is a KEY into the verified catalogue in images.php — never a file path.
 * The alt travels with the file there, so no page can describe a picture as
 * something it is not. Only two plates in the library are both real photography
 * and portrait enough to survive a full-bleed hero crop: 'kitchen-tall' and
 * 'kitchen-island'. ('kitchen-wide' is a 1920x500 letterbox banner — it is real,
 * but a tall hero crop would throw most of it away, so it is not used here.)
 * 'kitchen-island' is reserved for the island page, because its verified subject
 * actually contains an island; every other layout takes the neutral
 * 'kitchen-tall' display plate. NO plate claims a layout, a delivered project, a
 * client's home or a factory floor — the inline SVG diagram carries the shape.
 *
 * 'fig' is an OPTIONAL second image: a layout diagram or a CGI design
 * visualisation, shown in the planning-notes section and captioned in visible
 * text as exactly that. Only four layouts have one, because the library holds
 * only one diagram and three renders; the other two show none rather than
 * borrowing a picture of a different shape. ---- */
$alea_layouts = array(

	'l-shape' => array(
		'name'   => 'L-shape',
		'calc'   => 'l',
		'svg'    => '<path d="M4 5v13h16"/>',
		'rft_lo' => 8,
		'rft_hi' => 12,
		'sub'    => 'Two runs of cabinetry meeting at one corner — the most forgiving plan in a standard flat.',
		'lede'   => 'It is the plan most standard flats end up with, and usually for good reason. Keeping the sink, hob and fridge across two adjacent walls leaves the rest of the room free to walk through, and in most homes there is still space for a small table. It also forgives a door or a window sitting where a tighter plan could not absorb it.',
		'works'  => array(
			'The room has two usable walls meeting at a corner',
			'You want a walkway, or space for a small dining table, left over',
			'There is one door and one window to plan around, not three',
		),
		'not'    => array(
			'Both long walls are broken by a door or a balcony exit',
			'The room is long and narrow — two facing runs usually suit it better',
			'You need the maximum possible storage; a U or G-shape holds more',
		),
		'tips'   => array(
			array(
				't' => 'Keep the working triangle short',
				'b' => 'Sink, hob and fridge want to sit across the two runs rather than bunched on one. As general planning guidance, if you can walk between all three without crossing the room, the layout is working.',
			),
			array(
				't' => 'Decide the inside corner early',
				'b' => 'The corner where the two runs meet is the hardest cubic foot in any kitchen. Whether it gets a corner unit, a pull-out carousel or simply a blank filler changes the cabinet schedule, so it is settled at design stage, not on site.',
			),
			array(
				't' => 'Put the hob where the duct can leave',
				'b' => 'Chimney ducting is easiest on the run nearest an outside wall. Deciding which of the two runs carries the hob before civil work starts saves cutting and re-routing later.',
			),
		),
		'faq_q'  => 'What can I do with the corner in an L-shaped kitchen?',
		'faq_a'  => 'The inside corner is the one cabinet position that has to be decided deliberately. In general kitchen planning it gets one of three answers: a corner unit with a carousel or pull-out mechanism, a plain deep cabinet reached from one side, or a filler panel with the run simply starting past it. Each costs a different amount and each changes the cabinet schedule, so we settle it on the free design visit rather than on installation day.',
		'img'    => 'kitchen-tall',
		/* The one true L-shape asset in the library is a line diagram, not a
		   photograph, so it appears below as a diagram and never as the hero. */
		'fig'    => array(
			'key' => 'diagram-l',
			'tag' => 'Diagram',
			'cap' => 'Layout diagram &mdash; an L-shaped plan drawn as line art. Not a photograph, and not a drawing of your kitchen.',
		),
	),

	'u-shape' => array(
		'name'   => 'U-shape',
		'calc'   => 'u',
		'svg'    => '<path d="M4 5v13h16V5"/>',
		'rft_lo' => 12,
		'rft_hi' => 18,
		'sub'    => 'Three walls of cabinetry around one working space — the most storage and worktop for the floor area.',
		'lede'   => 'It buys more worktop and storage per square foot of floor than any layout without an island, and it keeps everything within a step or two of where you stand. What it asks for in return is a room of its own: three walls that are genuinely free, and enough clear floor in the middle for two people to pass each other.',
		'works'  => array(
			'The kitchen is a separate room with three usable walls',
			'More than one person cooks at the same time',
			'You want the most worktop and storage the floor area can hold',
		),
		'not'    => array(
			'The gap between the facing runs would be tight for two people',
			'One of the three walls carries a doorway or a full-height window',
			'You want a dining table inside the kitchen itself',
		),
		'tips'   => array(
			array(
				't' => 'You are solving two corners, not one',
				'b' => 'A U-shape has an inside corner at each end of the middle run. Both need the same decision — corner mechanism, deep cabinet or filler — and they are usually the two most expensive cabinet positions in the kitchen.',
			),
			array(
				't' => 'Leave real floor between the facing runs',
				'b' => 'General planning guidance is roughly three and a half to four feet of clear floor between opposite runs, so two people can pass and a door or drawer can open fully. Measure this before anything else; it decides whether a U-shape fits at all.',
			),
			array(
				't' => 'Sink to the window, hob to the duct',
				'b' => 'The middle run often carries the window. Putting the sink there and the hob on a run nearer an outside wall usually gives the shortest, cleanest chimney route.',
			),
		),
		'faq_q'  => 'How wide does a room need to be for a U-shaped kitchen?',
		'faq_a'  => 'It is decided by the clear floor left between the two facing runs once the cabinets are in, not by the room width on paper. General planning guidance is about three and a half to four feet of clear floor so two people can pass and drawers open fully. Below that a U-shape starts to feel tight and a parallel or L-shaped plan usually serves the room better. We measure the room free of charge on the site visit and tell you plainly which layouts it will take.',
		'img'    => 'kitchen-tall',
		'fig'    => array(
			'key' => 'render-u',
			'tag' => 'Design render',
			'cap' => 'Design visualisation of a U-shaped kitchen &mdash; a CGI render, not a photograph of an installed kitchen.',
		),
	),

	'g-shape' => array(
		'name'   => 'G-shape',
		'calc'   => 'g',
		'svg'    => '<path d="M4 5v13h16V5h-6"/>',
		'rft_lo' => 14,
		'rft_hi' => 20,
		'sub'    => 'A U-shape with a fourth, partial return — usually a peninsula that doubles as a breakfast counter.',
		'lede'   => 'It holds more than any other layout without a free-standing island, and the return doubles as a breakfast counter or a serving edge onto the next room. It also draws the line where the kitchen stops, which is what makes it useful in an open-plan home — and what makes it close an average-sized room in.',
		'works'  => array(
			'You want the most storage and worktop of any wall-based layout',
			'The kitchen opens onto a dining or living space that wants a boundary',
			'You would use a breakfast counter rather than a separate table',
		),
		'not'    => array(
			'The room is average-sized — a G-shape will close it in',
			'You want the kitchen to feel open on more than one side',
			'You would rather not solve three inside corners',
		),
		'tips'   => array(
			array(
				't' => 'Three corners, three decisions',
				'b' => 'The return adds a third inside corner to the two a U-shape already has. Each one needs its own answer — mechanism, deep cabinet or filler — and together they are usually the biggest single variable in a G-shape quotation.',
			),
			array(
				't' => 'Protect the entry gap',
				'b' => 'The opening left between the return and the opposite run is the only way in and out. As general guidance it wants to stay wide enough for a person carrying a full tray, and it should not be where the fridge or oven door swings.',
			),
			array(
				't' => 'Seating or storage — pick one per side',
				'b' => 'A return cannot give full knee room for stools on one face and full-depth cabinets on the other. Decide which face the return is really for before the carcass depths are fixed.',
			),
		),
		'faq_q'  => 'Is a G-shaped kitchen the same as a peninsula kitchen?',
		'faq_a'  => 'They overlap. A G-shape is a U-shape with a fourth partial run returning from one end, and that return is what most people call a peninsula. The distinction that matters when planning is not the name but whether the return is attached to a full U of cabinetry, because that is what sets the storage, the number of inside corners and the size of the opening left to walk through. We work that out on the measured drawing.',
		'img'    => 'kitchen-tall',
		/* No G-shape diagram and no G-shape render exist in the library, so this
		   layout shows none rather than borrowing a picture of another shape. */
		'fig'    => null,
	),

	'straight-shape' => array(
		'name'   => 'Straight-line',
		'calc'   => 'straight',
		'svg'    => '<path d="M3 18h18"/>',
		'rft_lo' => 6,
		'rft_hi' => 10,
		'sub'    => 'Everything along a single wall — the sensible plan in a compact flat, and the quiet one in an open-plan room.',
		'lede'   => 'It gives the room back to the room. A single run with tall units at one end reads as joinery rather than as a kitchen, which is why it sits quietly in an open-plan space, and it is the least expensive layout to build simply because there is less of it. The trade is storage: one wall runs out of it before any other plan does.',
		'works'  => array(
			'The kitchen is compact, or is one wall of a larger room',
			'One person usually cooks at a time',
			'You want the kitchen to stay visually quiet in an open-plan space',
		),
		'not'    => array(
			'You need heavy storage — a single run runs out of it first',
			'The run would be long enough to walk the length of it repeatedly',
			'Two people need to work at the same time without queueing',
		),
		'tips'   => array(
			array(
				't' => 'Order the run the way you cook',
				'b' => 'General planning guidance is fridge, then prep space, then sink, then more prep, then hob. On a single run the working triangle collapses into a line, so the sequence is doing all the work.',
			),
			array(
				't' => 'Stack the storage upward',
				'b' => 'A straight-line kitchen makes its storage back in height rather than length: full-height tall units at one end and wall units above the run recover most of what the missing second wall would have given you.',
			),
			array(
				't' => 'Keep the hob away from the ends',
				'b' => 'A hob at the very end of a run leaves no landing space on one side, and the chimney duct still has to reach an outside wall. Both are easier to solve if the hob sits inboard of the last cabinet.',
			),
		),
		'faq_q'  => 'Is a straight-line kitchen too small for a family?',
		'faq_a'  => 'Not necessarily — it depends on the length of the run and on how the storage is planned rather than on the shape itself. As general guidance, a single run of six to ten running feet with full-height tall units at one end and wall units above holds a surprising amount, and it is the standard plan in compact flats everywhere. What it does not do is let two people work side by side comfortably. We measure the room free of charge on the visit, say plainly which layouts it will take, and you leave with a ballpark price.',
		'img'    => 'kitchen-tall',
		'fig'    => array(
			'key' => 'render-compact',
			'tag' => 'Design render',
			'cap' => 'Design visualisation of a compact single-run kitchen with a laundry appliance &mdash; a CGI render, not a photograph of an installed kitchen.',
		),
	),

	'parallel-shape' => array(
		'name'   => 'Parallel',
		'calc'   => 'parallel',
		'svg'    => '<path d="M3 6h18M3 18h18"/>',
		'rft_lo' => 10,
		'rft_hi' => 16,
		'sub'    => 'Two facing runs with a walkway between them — the galley plan, and the most efficient use of a narrow room.',
		'lede'   => 'In a long, narrow room it wastes almost nothing. Everything is within a turn rather than a walk, wet work sits on one run with dry work opposite, and two people can work back to back without reaching across each other. It works best when the room is a corridor for cooking and not the corridor the rest of the house walks through.',
		'works'  => array(
			'The room is long and narrow with a wall on each side',
			'You want wet work on one run and dry work on the other',
			'Two people cook together and can work back to back',
		),
		'not'    => array(
			'The kitchen is the main route between two parts of the home',
			'The gap between the runs would be tight for two people',
			'You want seating or a table inside the kitchen',
		),
		'tips'   => array(
			array(
				't' => 'The gap is the whole design',
				'b' => 'General planning guidance is roughly three and a half to four feet of clear floor between the two runs — enough for a person to pass and for a drawer or dishwasher door to open fully. Measure this first; everything else follows from it.',
			),
			array(
				't' => 'Split wet from dry',
				'b' => 'Sink and dishwasher on one run, hob and prep on the other is the usual split. It keeps plumbing on one wall, keeps the chimney duct on the other, and stops two people reaching across each other.',
			),
			array(
				't' => 'Watch the door swings',
				'b' => 'In a parallel kitchen two doors can meet in the middle. Check the tall unit, the fridge and the oven against whatever is opposite them before the drawing is signed off.',
			),
		),
		'faq_q'  => 'How much gap should I leave between two parallel runs?',
		'faq_a'  => 'As general kitchen-planning guidance, about three and a half to four feet of clear floor between the finished fronts of the two runs. That is enough for one person to pass another and for a drawer, dishwasher or oven door to open fully without hitting the cabinets opposite. Less than that and the kitchen works for one person but not two. It is measured from the finished cabinet fronts, not the bare walls, which is why we measure the room ourselves before drawing anything.',
		'img'    => 'kitchen-tall',
		'fig'    => array(
			'key' => 'render-parallel',
			'tag' => 'Design render',
			'cap' => 'Design visualisation of a parallel-layout kitchen &mdash; a CGI render, not a photograph of an installed kitchen.',
		),
	),

	'island-shape' => array(
		'name'   => 'Island',
		'calc'   => 'island',
		'svg'    => '<path d="M4 5v13h16"/><rect x="11" y="8" width="7" height="4"/>',
		'rft_lo' => 12,
		'rft_hi' => 20,
		'sub'    => 'A free-standing counter in the middle of the room — the layout with the strictest floor-space requirement.',
		'lede'   => 'It adds worktop and storage without adding another wall run, and it gives people somewhere to stand while someone cooks — which is why it suits a kitchen people gather in. It is also the strictest of the six: it wants real, clear floor on every side, and any water or gas has to reach the middle of the room before the tiling goes down.',
		'works'  => array(
			'The room is wide or open-plan, with floor to spare on all four sides',
			'The kitchen is somewhere people gather, not only where food is cooked',
			'You want extra worktop without adding another wall run',
		),
		'not'    => array(
			'The clear floor around the island would be tight on any side',
			'The room is a standard flat kitchen — a wall-based layout will serve better',
			'Routing water or gas to the middle of the room is not possible',
		),
		'tips'   => array(
			array(
				't' => 'Clearance decides whether an island is possible',
				'b' => 'General planning guidance is about three to three and a half feet of clear floor on every side of the island, measured to the finished fronts. If one side fails that test, a peninsula or a G-shape usually gives the same worktop without the problem.',
			),
			array(
				't' => 'Services are a civil-work decision, not a cabinet one',
				'b' => 'A sink or hob on the island means water, drainage or gas has to run under the floor to the middle of the room. That is decided before tiling begins — if it is decided afterwards, it is decided expensively.',
			),
			array(
				't' => 'A hob on the island needs its own extraction route',
				'b' => 'With no wall behind it, an island hob needs either a ceiling-mounted or downdraft extractor and a duct route to match. Planning it with the false ceiling rather than after it is what keeps it tidy.',
			),
		),
		'faq_q'  => 'Can I put the sink or hob on the island?',
		'faq_a'  => 'Yes, but it is a civil-work decision rather than a cabinet one. A sink needs water and drainage run under the floor to the middle of the room, and a hob needs a gas line plus an extraction route with no wall behind it — usually a ceiling-mounted or downdraft extractor. Both are straightforward if they are decided before tiling and false ceiling work, and awkward afterwards. If the island is to stay a plain worktop with storage under it, none of this applies.',
		/* The only plate in the library whose verified subject contains an island. */
		'img'    => 'kitchen-island',
		/* No island render or island diagram exists in the library. */
		'fig'    => null,
	),
);

/* ---- Variant: whitelist + safe default (same pattern as page-collection.php) ---- */
$alea_valid_layouts = array_keys( $alea_layouts );
$alea_args = ( isset( $GLOBALS['alea_page_args'] ) && is_array( $GLOBALS['alea_page_args'] ) )
	? $GLOBALS['alea_page_args']
	: array();
$slug = isset( $alea_args['layout'] ) ? strtolower( trim( (string) $alea_args['layout'] ) ) : 'l-shape';
if ( ! in_array( $slug, $alea_valid_layouts, true ) ) {
	$slug = 'l-shape';
}

$lay      = $alea_layouts[ $slug ];
$lay_name = $lay['name'];
$rft_lo   = (int) $lay['rft_lo'];
$rft_hi   = (int) $lay['rft_hi'];

/* "an L-shape", "a U-shape", "an Island" — same principle as page-collection.php:
   the name is DATA, so the indefinite article is derived rather than written into
   the copy. Two cases, because these names are of two kinds:
     - a spoken LETTER name ("L-shape", "U-shape", "G-shape"): the article follows
       how the letter is said, so A, E, F, H, I, L, M, N, O, R, S and X take "an"
       — but U is said "yoo", so it takes "a" ("a U-shape"), and G is said "jee".
     - an ordinary WORD ("Straight-line", "Parallel", "Island"): the plain vowel
       rule, so "an Island" but "a Straight-line" (S is a letter-name vowel sound,
       which is exactly why the two cases cannot share one list). */
$lay_initial = strtoupper( substr( $lay_name, 0, 1 ) );
if ( preg_match( '/^[A-Za-z][-\s]/', $lay_name ) ) {
	$lay_article = in_array( $lay_initial, array( 'A', 'E', 'F', 'H', 'I', 'L', 'M', 'N', 'O', 'R', 'S', 'X' ), true ) ? 'an' : 'a';
} else {
	$lay_article = in_array( $lay_initial, array( 'A', 'E', 'I', 'O', 'U' ), true ) ? 'an' : 'a';
}

/* Hero and the optional diagram/render figure, both taken from the catalogue in
   images.php. The alt text belongs to the file there, so nothing on this page can
   describe a picture as something it is not, and an unusable image returns '' —
   which is why the figure block below is only printed when there is one. */
$lay_hero_img = alea_img( $lay['img'], array( 'class' => 'ax-hero__img', 'eager' => true ) );
$lay_fig      = ( ! empty( $lay['fig'] ) && is_array( $lay['fig'] ) ) ? $lay['fig'] : null;
$lay_fig_img  = $lay_fig ? alea_img( $lay_fig['key'], array( 'class' => 'axp-fig__img' ) ) : '';

/* Base path for the six layout URLs. MUST stay identical to the paths registered
   in alea_redesign_map() (functions.php) — sibling links are built from this one
   string so a route and a link can never disagree. */
$lay_base = '/modular-kitchen/';

/* The three COLLECTION URLs live on their own base — the plural /modular-kitchens/
   form page-collection.php declares canonical ($col_base there) and the approved
   brief uses. Kept separate from $lay_base on purpose: the collections are also
   registered under the singular path, and building them off the layout base would
   link them at their duplicate URLs and silently retarget them if $lay_base moved. */
$col_base = '/modular-kitchens/';

$calc_url   = home_url( '/kitchen-cost-calculator/' );
$calc_pre   = $calc_url . '?shape=' . rawurlencode( $lay['calc'] );
$visit_url  = home_url( '/book-a-free-design-visit/' );
$tel_href   = 'tel:' . alea_fact( 'phone_tel' );
$wa_href    = alea_wa_link( sprintf( "Hi ALEA, I'd like a free estimate for %s %s modular kitchen.", $lay_article, $lay_name ) );
$phone_disp = alea_fact( 'phone_display' );
$sqft_rft   = (int) alea_fact( 'sqft_per_rft' );
$price_unit = (string) alea_fact( 'price_unit' );
$brands     = implode( ' and ', $f['hardware_brands'] );
$areas      = implode( ', ', $f['service_area'] );

/* Published band across the whole range, derived from EVERY collection in
   facts.php — never from two hard-keyed slugs — so adding or retiring a
   collection there cannot leave this page quoting a stale range. */
$band_low  = 0;
$band_high = 0;
foreach ( $f['collections'] as $band_col ) {
	$band_low  = $band_low ? min( $band_low, (int) $band_col['from'] ) : (int) $band_col['from'];
	$band_high = max( $band_high, (int) $band_col['to'] );
}

/* Illustrative whole-kitchen figures for THIS layout, computed via
   alea_kitchen_total() at each end of the typical running-feet range, for the
   Signature collection. Falls back to the first collection defined, so removing
   'signature' from facts.php degrades instead of breaking. */
$ex_slug = isset( $f['collections']['signature'] ) ? 'signature' : (string) key( $f['collections'] );
$ex_name = isset( $f['collections'][ $ex_slug ]['name'] ) ? (string) $f['collections'][ $ex_slug ]['name'] : '';
$ex_band = alea_price_band( $ex_slug );
list( $lo_low, $lo_high, $lo_sqft ) = alea_kitchen_total( $rft_lo, $ex_slug );
list( $hi_low, $hi_high, $hi_sqft ) = alea_kitchen_total( $rft_hi, $ex_slug );

/* ---- FAQ: the visible answers and the JSON-LD render from this ONE array, so
        the schema can never drift from the text on the page. ---- */
$faq = array(
	array(
		'q' => sprintf( 'How many running feet is a typical %s kitchen?', $lay_name ),
		'a' => sprintf(
			'As general kitchen-planning guidance, %1$s kitchens typically run %2$d to %3$d running feet. That is a typical range for the shape, not a measurement of your home — the real figure depends on where your door, window, water line and gas point already are. We measure your kitchen free of charge on the site visit and give you the exact running feet, which you keep either way.',
			$lay_name,
			$rft_lo,
			$rft_hi
		),
	),
	array(
		'q' => sprintf( 'What does %s %s kitchen cost?', $lay_article, $lay_name ),
		'a' => sprintf(
			'Our rates are published per square foot of cabinetry, and %1$s is %2$s. Assuming standard base and wall units — about %3$d sq ft of cabinetry per running foot — a %4$d-running-foot kitchen works out to roughly ₹%5$s–%6$s, and a %7$d-running-foot one to roughly ₹%8$s–%9$s. That is illustrative arithmetic from the published band, not a quotation, and it covers cabinetry only. The estimator does the same sum for your own length before we ask for your phone number.',
			$ex_name,
			$ex_band,
			$sqft_rft,
			$rft_lo,
			alea_inr( $lo_low ),
			alea_inr( $lo_high ),
			$rft_hi,
			alea_inr( $hi_low ),
			alea_inr( $hi_high )
		),
	),
	array(
		'q' => $lay['faq_q'],
		'a' => $lay['faq_a'],
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

/* Inline SVG allowlist: the diagrams are constants defined above, but they are
   still run through wp_kses so nothing but drawing primitives can ever render. */
$svg_allowed = array(
	'path' => array( 'd' => true ),
	'rect' => array( 'x' => true, 'y' => true, 'width' => true, 'height' => true ),
);
?>
<div class="ax-root">

	<style data-no-optimize="1">
	/* Page-specific: the layout diagram, the guide-price block, the nav cards. */
	.axp-dia{width:60px;height:60px;display:block;stroke:currentColor;fill:none;stroke-width:1.5;stroke-linecap:square;margin-bottom:var(--sp-4)}
	.axp-dia--card{width:34px;height:34px;color:var(--graphite);margin-bottom:var(--sp-3)}
	.axp-hero-alt{margin-top:var(--sp-3);font-size:.9375rem;color:var(--ax-fg-muted)}
	.axp-hero-alt a{color:inherit}
	.axp-band{border-top:2px solid var(--ax-fg);padding-top:var(--sp-4)}
	.axp-band__label{display:block;margin-bottom:var(--sp-2);font-family:var(--font-mono);font-size:var(--fs-nano);letter-spacing:var(--ls-mono-wide);text-transform:uppercase;color:var(--ax-fg-muted)}
	.axp-band__value{display:block;font-family:var(--font-mono);font-weight:600;font-size:clamp(1.375rem,4.5vw,2rem);line-height:1.15;letter-spacing:.01em;color:var(--ax-fg);font-variant-numeric:tabular-nums;font-feature-settings:"tnum" 1;overflow-wrap:anywhere}
	.axp-band__note{margin-top:var(--sp-3);font-family:var(--font-mono);font-size:var(--fs-micro);line-height:1.6;letter-spacing:var(--ls-mono);text-transform:uppercase;color:var(--ax-fg-muted)}
	.axp-nav__cta{margin-top:auto;padding-top:var(--sp-4)}
	/* Diagram / design-render figure: shown whole, never cropped, so a line
	   diagram keeps its edges and a render is not mistaken for a photograph. */
	.axp-fig{max-width:480px;margin:var(--sp-7) 0 0}
	.axp-fig__img{display:block;width:100%;height:auto;border-radius:var(--radius);background:var(--rule)}
	</style>

	<!-- ================================================== 1. HERO -->
	<section class="ax-hero ax-hero--short">
		<?php /* Experience-centre display photography from the images.php catalogue,
		         which owns the alt. It describes a display at our own centre — never
		         a delivered project, a client's home or a factory floor — and it does
		         not name this page's layout. The inline SVG below carries the shape. */ ?>
		<?php echo $lay_hero_img; // phpcs:ignore WordPress.Security.EscapeOutput — built by alea_img(), which escapes ?>
		<div class="ax-hero__inner">
			<svg class="axp-dia" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><?php echo wp_kses( $lay['svg'], $svg_allowed ); ?></svg>
			<p class="ax-eyebrow">Kitchen layouts &mdash; <?php echo (int) ( array_search( $slug, $alea_valid_layouts, true ) + 1 ); ?> of <?php echo (int) count( $alea_valid_layouts ); ?></p>
			<h1 class="ax-hero__title"><?php echo esc_html( $lay_name ); ?> kitchens</h1>
			<?php /* One short line in the hero — the full guidance paragraph lives in
			         section 3, so a 375px screen keeps the CTA above the fold. */ ?>
			<p class="ax-hero__sub">
				<?php echo esc_html( $lay['sub'] ); ?>
			</p>
			<div class="ax-hero__actions">
				<a class="ax-btn ax-btn--primary ax-btn--lg" href="<?php echo esc_url( $calc_pre ); ?>">Price this layout</a>
			</div>
			<p class="axp-hero-alt">or <a href="#alea-book">book a free measurement</a> &mdash; free, no obligation</p>
			<p class="ax-hero__credit">
				TYPICALLY <?php echo (int) $rft_lo; ?>&ndash;<?php echo (int) $rft_hi; ?> RUNNING FEET (GUIDANCE)
				/ <?php echo (int) $f['warranty_years']; ?>-YEAR WRITTEN WARRANTY
				/ <?php echo (int) $f['install_days']; ?>-DAY INSTALLATION
			</p>
		</div>
	</section>

	<!-- ================================================== 2. SPEC STRIP -->
	<!-- Four facts, every one of them read from or derived from facts.php. The
	     layout's running-feet range is deliberately NOT in this strip: it is
	     general planning guidance, and a mono fact strip would read it as a
	     measured ALEA claim. It appears in the copy, labelled as guidance. -->
	<section class="ax-section ax-section--flush" aria-label="ALEA key facts">
		<div class="ax-wrap ax-wrap--flush">
			<div class="ax-specstrip">
				<div class="ax-specstrip__item">
					<span class="ax-specstrip__label">Our own factory</span>
					<span class="ax-specstrip__value"><?php echo esc_html( $f['factory_sqft'] ); ?><span class="ax-specstrip__unit">sq ft</span></span>
				</div>
				<div class="ax-specstrip__item">
					<span class="ax-specstrip__label">Written warranty</span>
					<span class="ax-specstrip__value"><?php echo (int) $f['warranty_years']; ?><span class="ax-specstrip__unit">years</span></span>
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

	<!-- ================================================== 3. WHEN IT WORKS / WHEN IT DOESN'T -->
	<section class="ax-section ax-section--sheet ax-section--ruled-b">
		<div class="ax-wrap ax-wrap--narrow">
			<header class="ax-head ax-reveal">
				<p class="ax-eyebrow">Honestly</p>
				<h2 class="ax-h2">When <?php echo esc_html( $lay_article ); ?> <?php echo esc_html( $lay_name ); ?> works &mdash; and when it does not.</h2>
				<p class="ax-lead">
					General kitchen-planning guidance, not a judgement on your home. The room decides the
					layout: where the door, window, water line and gas point already are settles most of it
					before anyone chooses a finish.
				</p>
			</header>
			<div class="ax-prose ax-reveal ax-mb-6">
				<p><?php echo esc_html( $lay['lede'] ); ?></p>
			</div>
			<div class="ax-included ax-reveal">
				<div class="ax-included__col ax-included--in">
					<h3 class="ax-included__title">Choose it when</h3>
					<ul class="ax-included__list">
						<?php foreach ( $lay['works'] as $line ) : ?>
						<li><?php echo esc_html( $line ); ?></li>
						<?php endforeach; ?>
					</ul>
				</div>
				<div class="ax-included__col ax-included--out">
					<h3 class="ax-included__title">Think again when</h3>
					<ul class="ax-included__list">
						<?php foreach ( $lay['not'] as $line ) : ?>
						<li><?php echo esc_html( $line ); ?></li>
						<?php endforeach; ?>
					</ul>
				</div>
			</div>
			<?php /* Scoped to exactly what page-book-visit.php promises for the same free
			         event: a measurement, a plain answer on which layouts the room takes,
			         and a ballpark. No comparative drawing is promised — facts.php holds
			         no such service fact. */ ?>
			<p class="ax-prose ax-mt-6">
				If your room does not suit this layout we will say so on the free measurement, tell you plainly
				which layouts it will take, and leave you with a ballpark price. You keep the measurements
				either way.
			</p>
		</div>
	</section>

	<!-- ================================================== 4. PLANNING NOTES -->
	<section class="ax-section">
		<div class="ax-wrap ax-wrap--narrow">
			<header class="ax-head ax-reveal">
				<p class="ax-eyebrow">Planning notes</p>
				<h2 class="ax-h2">Three things worth settling before anyone quotes you.</h2>
				<p class="ax-lead">
					These are the decisions that move the cabinet schedule &mdash; and therefore the price &mdash;
					on <?php echo esc_html( $lay_article ); ?> <?php echo esc_html( $lay_name ); ?>. General guidance, useful whoever builds your kitchen.
				</p>
			</header>
			<div class="ax-steps ax-reveal">
				<?php foreach ( $lay['tips'] as $tip ) : ?>
				<div class="ax-step">
					<div class="ax-step__body">
						<h3 class="ax-step__title"><?php echo esc_html( $tip['t'] ); ?></h3>
						<p class="ax-step__text"><?php echo esc_html( $tip['b'] ); ?></p>
					</div>
				</div>
				<?php endforeach; ?>
			</div>
			<?php
			/* Optional second image: a layout DIAGRAM or a CGI design visualisation,
			   never presented as photography. The visible caption says which it is.
			   Printed only when the catalogue actually holds one for this shape. */
			if ( $lay_fig_img ) :
				?>
			<figure class="axp-fig ax-reveal">
				<?php echo $lay_fig_img; // phpcs:ignore WordPress.Security.EscapeOutput — built by alea_img(), which escapes ?>
				<figcaption class="ax-media__caption">
					<b><?php echo esc_html( $lay_fig['tag'] ); ?></b> &mdash;
					<?php echo wp_kses( $lay_fig['cap'], array() ); ?>
				</figcaption>
			</figure>
			<?php endif; ?>
		</div>
	</section>

	<!-- ================================================== 5. PRICE BAND FOR THIS LAYOUT -->
	<!-- id="estimate": landing anchor for the theme's site-wide sticky mobile bar
	     (.aleac-mbar "Free Estimate" button in functions.php). It lands on the
	     worked arithmetic and its "Price this layout" CTA. -->
	<span id="estimate" class="ax-sr-only"></span>
	<section class="ax-section ax-section--ruled">
		<div class="ax-wrap">
			<div class="ax-grid ax-grid--split">
				<div class="ax-reveal">
					<div class="ax-head ax-mb-0">
						<p class="ax-eyebrow">Illustrative guide</p>
						<h2 class="ax-h2">What <?php echo esc_html( $lay_article ); ?> <?php echo esc_html( $lay_name ); ?> costs, as arithmetic.</h2>
						<p class="ax-lead ax-mt-4">
							Both figures below are the published <?php echo esc_html( $ex_name ); ?> rate multiplied out at
							each end of the typical range for this shape. Nothing here is quoted or promised &mdash;
							your own kitchen gets its own itemised number after a free measurement.
						</p>
					</div>
					<div class="ax-btnrow ax-mt-5">
						<a class="ax-btn ax-btn--primary" href="<?php echo esc_url( $calc_pre ); ?>">Price this layout</a>
					</div>
					<p class="ax-btn-note">Free &middot; no sign-up &middot; opens with <?php echo esc_html( $lay_name ); ?> already selected</p>
				</div>
				<div class="ax-reveal">
					<p class="axp-band">
						<span class="axp-band__label">Illustrative range &mdash; <?php echo esc_html( $lay_name ); ?>, <?php echo (int) $rft_lo; ?>&ndash;<?php echo (int) $rft_hi; ?> running feet, <?php echo esc_html( $ex_name ); ?></span>
						<span class="axp-band__value">&#8377;<?php echo esc_html( alea_inr( $lo_low ) ); ?>&ndash;<?php echo esc_html( alea_inr( $hi_high ) ); ?></span>
						<span class="axp-band__note">Cabinetry only &middot; arithmetic from the published <?php echo esc_html( $ex_band ); ?> band</span>
					</p>
					<div class="ax-scroll-x ax-mt-5">
						<table class="ax-spectable">
							<caption><?php echo esc_html( $lay_name ); ?> &middot; <?php echo esc_html( $ex_name ); ?> collection &middot; illustrative only</caption>
							<thead>
								<tr>
									<th scope="col">Kitchen</th>
									<th scope="col" class="ax-spectable__num">&asymp; Cabinetry</th>
									<th scope="col" class="ax-spectable__num">Estimated range</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<th scope="row"><?php echo (int) $rft_lo; ?> running ft</th>
									<td class="ax-spectable__num"><?php echo (int) $lo_sqft; ?> sq ft</td>
									<td class="ax-spectable__num">&#8377;<?php echo esc_html( alea_inr( $lo_low ) ); ?>&ndash;<?php echo esc_html( alea_inr( $lo_high ) ); ?></td>
								</tr>
								<tr>
									<th scope="row"><?php echo (int) $rft_hi; ?> running ft</th>
									<td class="ax-spectable__num"><?php echo (int) $hi_sqft; ?> sq ft</td>
									<td class="ax-spectable__num">&#8377;<?php echo esc_html( alea_inr( $hi_low ) ); ?>&ndash;<?php echo esc_html( alea_inr( $hi_high ) ); ?></td>
								</tr>
							</tbody>
						</table>
					</div>
					<p class="ax-spectable__note">
						<?php echo (int) $rft_lo; ?>&ndash;<?php echo (int) $rft_hi; ?> running feet is general planning guidance for this
						shape, not a measurement of your kitchen. Assumes standard base + wall units &mdash; about
						<?php echo (int) $sqft_rft; ?> sq ft of cabinetry per running foot. Range = sq ft &times; the published
						<?php echo esc_html( $ex_band ); ?> band. Countertop, chimney, hob, sink and appliances are quoted separately.
					</p>
				</div>
			</div>
		</div>
	</section>

	<!-- ================================================== 6. THE THREE COLLECTIONS -->
	<section class="ax-section">
		<div class="ax-wrap">
			<header class="ax-head ax-reveal">
				<p class="ax-eyebrow">The range</p>
				<h2 class="ax-h2">The same <?php echo esc_html( $lay_name ); ?>, in three collections.</h2>
				<p class="ax-lead">
					The layout does not change the rate &mdash; the collection does. <?php echo esc_html( $brands ); ?>
					hardware is fitted as standard and the <?php echo (int) $f['warranty_years']; ?>-year written warranty
					applies in all three. What changes is the depth of finish and fitting choice.
				</p>
			</header>
			<div class="ax-grid ax-grid--3">
				<?php foreach ( $f['collections'] as $c_slug => $col ) : ?>
				<article class="ax-card ax-card--collection ax-reveal">
					<div class="ax-card__inner">
						<h3 class="ax-card__name"><?php echo esc_html( $col['name'] ); ?></h3>
						<p class="ax-card__character"><?php echo esc_html( $col['character'] ); ?></p>
						<p class="ax-card__meta">
							<?php /* Hrefs are built from $col_base — the canonical plural collection
							         base page-collection.php declares and alea_redesign_map() registers
							         — never from $lay_base, so these land on the collections' canonical
							         URLs and cannot be retargeted by a change to the layout base. */ ?>
							<a class="ax-card__link" href="<?php echo esc_url( home_url( $col_base . $c_slug . '/' ) ); ?>">
								See the <?php echo esc_html( $col['name'] ); ?> collection
							</a>
						</p>
					</div>
					<div class="ax-card__price">
						<span class="ax-card__price-label">Guide price</span>
						<span class="ax-card__price-value"><?php echo esc_html( alea_price_band( $c_slug ) ); ?></span>
					</div>
				</article>
				<?php endforeach; ?>
			</div>
			<p class="ax-btn-note">Rates are per square foot of cabinetry &mdash; the same rates the estimator uses</p>
		</div>
	</section>

	<!-- ================================================== 7. THE OTHER FIVE LAYOUTS -->
	<section class="ax-section ax-section--ruled">
		<div class="ax-wrap">
			<header class="ax-head ax-head--wide ax-reveal">
				<p class="ax-eyebrow">The other <?php echo (int) ( count( $alea_valid_layouts ) - 1 ); ?></p>
				<h2 class="ax-h2">Not sure <?php echo esc_html( $lay_article ); ?> <?php echo esc_html( $lay_name ); ?> is your room?</h2>
				<p class="ax-lead">
					The running-foot figures are typical ranges for each shape &mdash; general planning guidance,
					not a measurement of your home. We measure your kitchen free of charge and tell you plainly
					which layouts the room will take.
				</p>
			</header>
			<div class="ax-grid ax-grid--2 ax-grid--3">
				<?php
				foreach ( $alea_layouts as $o_slug => $o ) :
					if ( $o_slug === $slug ) {
						continue;
					}
					?>
				<article class="ax-card ax-reveal">
					<svg class="axp-dia axp-dia--card" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><?php echo wp_kses( $o['svg'], $svg_allowed ); ?></svg>
					<h3 class="ax-card__title"><?php echo esc_html( $o['name'] ); ?></h3>
					<p class="ax-mono--label ax-mt-3">Typically <?php echo (int) $o['rft_lo']; ?>&ndash;<?php echo (int) $o['rft_hi']; ?> running feet</p>
					<p class="axp-nav__cta">
						<a class="ax-btn ax-btn--link ax-card__link" href="<?php echo esc_url( home_url( $lay_base . $o_slug . '/' ) ); ?>">
							<?php echo esc_html( $o['name'] ); ?> kitchens
						</a>
					</p>
				</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<!-- ================================================== 8. EXPERIENCE CENTRE -->
	<!-- One plate, captioned as exactly what it shows: shelving and accessories on
	     display at our own centre. The images.php catalogue holds no verified
	     customer-home or factory-floor photography, so none is claimed here, and
	     this plate makes no layout claim at all. -->
	<section class="ax-section ax-section--sheet ax-section--ruled">
		<div class="ax-wrap">
			<div class="ax-grid ax-grid--split">
				<div class="ax-reveal">
					<div class="ax-head ax-mb-0">
						<p class="ax-eyebrow">The experience centre</p>
						<h2 class="ax-h2">Open the drawers before you decide the layout.</h2>
						<?php /* facts.php records that Hettich and Blum are fitted as standard —
						         it records nothing about which brands are on show at the centre,
						         so the invitation is to the display itself, not to named stock. */ ?>
						<p class="ax-lead ax-mt-4">
							Standing displays are the fastest way to tell whether a shape suits how you cook.
							Come and open and close the drawers and fittings yourself &mdash; then visit the
							factory at <?php echo esc_html( $f['factory_place'] ); ?> and watch kitchens being built.
						</p>
					</div>
					<div class="ax-btnrow ax-mt-5">
						<a class="ax-btn ax-btn--ghost" href="<?php echo esc_url( $visit_url ); ?>">Book a free design visit</a>
					</div>
				</div>
				<figure class="ax-media ax-media--43 ax-reveal">
					<span class="ax-media__frame">
						<?php echo alea_img( 'accessories' ); // phpcs:ignore WordPress.Security.EscapeOutput — built by alea_img(), which escapes ?>
						<span class="ax-media__tag">Display</span>
					</span>
					<figcaption class="ax-media__caption">
						Accessories on display / ALEA experience centre &mdash;
						<b>open and close the fittings yourself on a free visit</b>
					</figcaption>
				</figure>
			</div>
		</div>
	</section>

	<!-- ================================================== 9. FAQ -->
	<section class="ax-section ax-section--ruled">
		<div class="ax-wrap ax-wrap--narrow">
			<header class="ax-head ax-reveal">
				<p class="ax-eyebrow">Questions</p>
				<h2 class="ax-h2">Asked about <?php echo esc_html( $lay_name ); ?> kitchens.</h2>
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

	<!-- ================================================== 10. FINAL CTA + FORM -->
	<section class="ax-section ax-section--ruled" id="alea-book">
		<div class="ax-wrap">
			<div class="ax-grid ax-grid--split">
				<div class="ax-reveal">
					<p class="ax-eyebrow">Book a free visit</p>
					<h2 class="ax-h2">Let us measure the room before you commit to a shape.</h2>
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
