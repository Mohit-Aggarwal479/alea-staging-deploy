<?php
/**
 * ALEA redesign shell.
 * Renders a redesigned page inside the theme's existing header/footer,
 * bypassing Elementor. Selected by the router in functions.php (block 11).
 */

defined( 'ABSPATH' ) || exit;

get_header();

$alea_page_file = isset( $GLOBALS['alea_page_file'] ) ? $GLOBALS['alea_page_file'] : '';
$alea_dir       = get_stylesheet_directory() . '/alea/';

// Only ever include a file from inside alea/.
if ( $alea_page_file && 0 === strpos( realpath( $alea_page_file ), realpath( $alea_dir ) ) && file_exists( $alea_page_file ) ) {
	echo '<main class="alea-main" id="alea-main">';
	include $alea_page_file;
	echo '</main>';
} else {
	echo '<main class="alea-main"><div style="padding:80px 22px;text-align:center">Page template not found.</div></main>';
}

get_footer();
