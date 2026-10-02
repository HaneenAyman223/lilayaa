<?php
/**
 * Loads lk-woocommerce-style.css sitewide — only needs to run once the file
 * exists at inc/lk-woocommerce-style.css, so this can be pasted straight
 * into functions.php alongside the other require_once lines.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_style(
		'lk-woocommerce-style',
		get_stylesheet_directory_uri() . '/inc/lk-woocommerce-style.css',
		array(),
		filemtime( get_stylesheet_directory() . '/inc/lk-woocommerce-style.css' )
	);
}, 20 );
