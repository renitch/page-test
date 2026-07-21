<?php
/**
 * Front-end assets for the Partners block "See all partners" reveal.
 *
 * Enqueues assets/js/partners-toggle.js, which collapses each partners logo
 * grid to its first batch and reveals the rest in steps (see partners.php).
 *
 * Include from functions.php:
 *   require get_stylesheet_directory() . '/inc/partners-toggle.php';
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Enqueue the partners toggle script.
 */
function partners_toggle_enqueue_assets() {
    $script_path = get_stylesheet_directory() . '/assets/js/partners-toggle.js';

    wp_enqueue_script(
        'partners-toggle',
        get_stylesheet_directory_uri() . '/assets/js/partners-toggle.js',
        [],
        file_exists( $script_path ) ? (string) filemtime( $script_path ) : '1.0.0',
        true
    );
}
add_action( 'wp_enqueue_scripts', 'partners_toggle_enqueue_assets' );
