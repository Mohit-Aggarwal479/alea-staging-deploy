<?php
/**
 * ALEA — shared repair for the site's one lead form.
 *
 * Required by functions.php (so EVERY redesigned page gets it, not just the
 * template that happens to define it) and defensively by the templates that
 * embed the form. require_once + function_exists make both safe.
 *
 * A11Y — the lead form has no field names at all.
 *
 * The markup for [contact-form-7 id="7dcf010"] (CF7 post 9262) lives in the
 * form template in the DATABASE, which this repo does not version, so it cannot
 * be corrected from here directly. Rendered, it emits five placeholder-only
 * controls and ZERO <label> elements: a screen-reader or voice-control user
 * gets no field names, and every visible hint disappears the moment the visitor
 * types. WCAG 1.3.1, 3.3.2 and 4.1.2.
 *
 * Two further faults in that same template:
 *   - your-email and your-requirement carry no id, so nothing can point a
 *     'for' at them until one is minted;
 *   - the <select> opens on <option value="Planing with in"> — a real,
 *     SUBMITTABLE value (and a typo for "Planning within"), so the field's own
 *     "required" rule passes on a non-answer.
 *
 * Both are repaired on the way out, on CF7's own 'wpcf7_form_elements' filter:
 * a real <label class="ax-label"> before each control, using the .ax-label /
 * .ax-req classes design-system.css already defines for exactly this; an id
 * minted where one is missing; and the fake first option turned into an empty,
 * non-submittable prompt so "required" starts meaning something. The required
 * asterisk is READ OFF the emitted markup (aria-required / CF7's own required
 * class) and never assumed. Placeholders are left untouched — they stay hints,
 * they are no longer the name.
 *
 * Self-cancelling by design: if the form output already contains a <label>, the
 * filter hands it back untouched. Correcting the DB template therefore retires
 * this code with no edit here, and it can never emit a second label.
 *
 * FIELD MAP (the form is shared by every page, so no page may describe it as
 * asking for less than this): your-name, your-phone, your-email,
 * planning-with-in (required select) and your-requirement.
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'alea_cf7_label_fields' ) ) {
	/**
	 * Give the shared lead form real, programmatically associated labels.
	 *
	 * @param string $form Rendered CF7 form HTML.
	 * @return string
	 */
	function alea_cf7_label_fields( $form ) {
		if ( ! is_string( $form ) || '' === $form ) {
			return $form;
		}
		/* Already labelled — the DB template was fixed, or another copy of this
		   block ran first. Never label twice. */
		if ( false !== stripos( $form, '<label' ) ) {
			return $form;
		}

		/* The <select>'s fake prompt: make it non-submittable so the field's
		   required rule stops passing on a non-answer, and fix the typo. Only
		   an option whose value IS the prompt text is touched; the five real
		   choices below it are left exactly as the form owner set them. */
		$out = preg_replace_callback(
			'#<select\b[^>]*\bname=(["\'])planning-with-in\1[^>]*>.*?</select>#is',
			static function ( $sel ) {
				return preg_replace(
					'#<option\b[^>]*\bvalue=(["\'])\s*plann?ing\s*with\s*in\s*\1[^>]*>.*?</option>#is',
					'<option value="">Choose one</option>',
					$sel[0],
					1
				);
			},
			$form,
			1
		);
		if ( is_string( $out ) ) {
			$form = $out;
		}

		/* CF7 stamps every rendered instance with a unique unit tag. Minted ids
		   are suffixed with it so that a page carrying two copies of this form
		   cannot end up with two elements sharing one id — which would make the
		   'for' on the second label point at the first form's control. Only ids
		   this code creates are affected; the template's own ids are left alone,
		   because existing CSS and JS may reference them. */
		$unit = '';
		if ( preg_match( '#\bname=(["\'])_wpcf7_unit_tag\1[^>]*\bvalue=(["\'])([^"\']+)\2#i', $form, $u_m ) ) {
			$unit = '-' . $u_m[3];
		}

		/* Field name => visible label. Interface wording only: none of this is a
		   business claim, so none of it belongs in facts.php. */
		$fields = array(
			'your-name'        => 'Your name',
			'your-phone'       => 'Phone number',
			'your-email'       => 'Email',
			'planning-with-in' => 'Planning within',
			'your-requirement' => 'What you need',
		);

		foreach ( $fields as $name => $text ) {
			$out = preg_replace_callback(
				'#<(input|select|textarea)\b([^>]*\bname=(["\'])' . preg_quote( $name, '#' ) . '\3[^>]*)>#i',
				static function ( $m ) use ( $name, $text, $unit ) {
					$tag   = $m[0];
					$attrs = $m[2];

					/* Reuse the control's own id when it has one; mint a stable
					   one from the field name when it does not, so 'for' always
					   resolves and the id does not change between renders. */
					if ( preg_match( '#\bid=(["\'])([^"\']+)\1#i', $attrs, $id_m ) ) {
						$id = $id_m[2];
					} else {
						$id  = 'alea-field-' . $name . $unit;
						$tag = '<' . $m[1] . ' id="' . esc_attr( $id ) . '"' . $attrs . '>';
					}

					/* Required is read off the markup CF7 itself emitted. */
					$required = ( preg_match( '#\baria-required=(["\'])true\1#i', $attrs )
						|| false !== stripos( $attrs, 'wpcf7-validates-as-required' ) );

					return '<label class="ax-label" for="' . esc_attr( $id ) . '">'
						. esc_html( $text )
						. ( $required ? '<span class="ax-req">*</span>' : '' )
						. '</label>' . $tag;
				},
				$form,
				1
			);
			if ( is_string( $out ) ) {
				$form = $out;
			}
		}

		return $form;
	}
}
/* add_filter() de-dupes an identical callback at the same priority, so a second
   require from a template can never register this twice. */
add_filter( 'wpcf7_form_elements', 'alea_cf7_label_fields', 20 );
