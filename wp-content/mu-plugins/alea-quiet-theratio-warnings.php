<?php
/**
 * Plugin Name: ALEA — Quiet Theratio PHP-8 Warnings
 * Description: Suppresses the known PHP-8 "undefined array key" warning storm from the
 *              Theratio PARENT theme's Elementor widgets (portfolio/testimonial carousels),
 *              which floods the error log (~54k/cycle) and slows dynamic page builds.
 *              Only WARNING/NOTICE/DEPRECATED from the theratio theme are swallowed —
 *              all other warnings, and every error/fatal, pass through and log normally.
 *              This is a safe stop-gap; the proper fix is updating the Theratio theme.
 */

defined( 'ABSPATH' ) || exit;

set_error_handler(
	function ( $errno, $errstr, $errfile = '' ) {
		$file = str_replace( '\\', '/', (string) $errfile );
		if (
			in_array( $errno, array( E_WARNING, E_NOTICE, E_DEPRECATED ), true )
			&& strpos( $file, '/themes/theratio/' ) !== false
		) {
			return true; // handled — do not log this specific noise
		}
		return false; // defer to PHP/WordPress default handling for everything else
	},
	E_WARNING | E_NOTICE | E_DEPRECATED
);
