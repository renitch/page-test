<?php
/**
 * Cards view for The Events Calendar — registration, assets and template routing.
 *
 * Adds a "Cards" option next to List / Month / Day in The Events Calendar
 * (target: The Events Calendar 6.17.1 and Events Calendar Pro 7.8.0, Views V2).
 * Nothing in this feature touches the plugin folder: the view is registered
 * through the public `tribe_events_views` filter, its template is served from
 * this folder via `tribe_template_file`, and the grid styling is an enqueued
 * stylesheet — the whole feature lives inside the theme, under /cards.
 *
 * Include from the theme's functions.php:
 *   require get_stylesheet_directory() . '/cards/events-cards-view.php';
 *
 * After including it, enable the view and register its URL (one-time):
 *   1. Events → Settings → Display → "Enable Views" → tick "Cards" (this file
 *      also appends it automatically, so this is only a fallback).
 *   2. Settings → Permalinks → "Save Changes" so /events/cards/ resolves.
 *
 * @package ABMA\Events_Cards
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * View slug shared by the URL, the selector, the container class and the template.
 */
const EVENTS_CARDS_VIEW_SLUG = 'cards';

/**
 * Stylesheet handle.
 */
const EVENTS_CARDS_STYLE_HANDLE = 'abma-events-cards';

/**
 * Register the Cards view with The Events Calendar's Views V2 manager.
 *
 * Runs on the `tribe_events_views` filter, which fires only after the plugin
 * (and its autoloader) are fully loaded, so the parent List view class is
 * guaranteed to be available here and the class file can be loaded lazily.
 *
 * @param array<string,string> $views Map of view slug => fully-qualified class name.
 * @return array<string,string>
 */
function events_cards_register_view( $views ) {
    if ( ! is_array( $views ) || ! class_exists( '\Tribe\Events\Views\V2\Views\List_View' ) ) {
        return $views;
    }

    require_once __DIR__ . '/class-events-cards-view.php';

    if ( class_exists( '\ABMA\Events_Cards_View' ) ) {
        $views[ EVENTS_CARDS_VIEW_SLUG ] = \ABMA\Events_Cards_View::class;
    }

    return $views;
}
add_filter( 'tribe_events_views', 'events_cards_register_view' );

/**
 * Make sure the Cards view is offered in the front-end view selector.
 *
 * The Events Calendar only shows views that are ticked under
 * Events → Settings → Display → "Enable Views". A freshly registered view is
 * not ticked, so append Cards to the enabled list — additively, never removing
 * an admin's other choices. When the option is unset (its default), every
 * public view is already shown, so there is nothing to do. Remove this filter
 * if you would rather manage the view purely from that settings screen.
 *
 * @param mixed  $value       Stored option value.
 * @param string $option_name Option being read.
 * @return mixed
 */
function events_cards_enable_view( $value, $option_name = '' ) {
    if ( 'tribeEnableViews' !== $option_name || ! is_array( $value ) ) {
        return $value;
    }

    if ( ! in_array( EVENTS_CARDS_VIEW_SLUG, $value, true ) ) {
        $value[] = EVENTS_CARDS_VIEW_SLUG;
    }

    return $value;
}
add_filter( 'tribe_get_option', 'events_cards_enable_view', 10, 2 );

/**
 * Serve the Cards view's root template from this folder.
 *
 * The Cards view asks the template engine for the `cards` template, which does
 * not exist in the plugin or in the theme's tribe/ overrides. We intercept that
 * one lookup and hand back cards.php from this folder, keeping the feature
 * self-contained. Every other template (including the `list` template that
 * cards.php re-uses) is left untouched.
 *
 * @param string                   $file Located template path ('' when not found).
 * @param array<int,string>|string $name Template name segments.
 * @return string
 */
function events_cards_template_file( $file, $name ) {
    $slug = is_array( $name ) ? (string) end( $name ) : (string) $name;

    if ( EVENTS_CARDS_VIEW_SLUG !== $slug ) {
        return $file;
    }

    $custom = __DIR__ . '/templates/cards.php';

    return file_exists( $custom ) ? $custom : $file;
}
add_filter( 'tribe_template_file', 'events_cards_template_file', 10, 2 );

/**
 * Absolute filesystem path to a file inside this feature folder.
 *
 * @param string $relative Path relative to this folder (no leading slash).
 * @return string
 */
function events_cards_path( $relative ) {
    return __DIR__ . '/' . ltrim( $relative, '/' );
}

/**
 * Public URL for a file inside this feature folder.
 *
 * Derived from this file's own location so it stays correct whether the feature
 * lives in a child theme or a parent theme, and regardless of the folder name.
 *
 * @param string $relative Path relative to this folder (no leading slash).
 * @return string
 */
function events_cards_url( $relative ) {
    $path = wp_normalize_path( events_cards_path( $relative ) );

    $stylesheet_dir = wp_normalize_path( get_stylesheet_directory() );
    if ( 0 === strpos( $path, $stylesheet_dir ) ) {
        return get_stylesheet_directory_uri() . substr( $path, strlen( $stylesheet_dir ) );
    }

    $template_dir = wp_normalize_path( get_template_directory() );
    if ( 0 === strpos( $path, $template_dir ) ) {
        return get_template_directory_uri() . substr( $path, strlen( $template_dir ) );
    }

    return '';
}

/**
 * Enqueue the card-grid styles on The Events Calendar's front-end views.
 *
 * Loaded on any TEC view (not only Cards) so the styles are already present
 * when a visitor switches to Cards through the selector's AJAX, which swaps the
 * markup without reloading page assets. Cache-busted with filemtime().
 */
function events_cards_enqueue_assets() {
    if ( ! function_exists( 'tribe_is_event_query' ) || ! tribe_is_event_query() ) {
        return;
    }

    $style_path = events_cards_path( 'assets/cards.css' );
    $style_url  = events_cards_url( 'assets/cards.css' );

    if ( '' === $style_url || ! file_exists( $style_path ) ) {
        return;
    }

    wp_enqueue_style(
        EVENTS_CARDS_STYLE_HANDLE,
        $style_url,
        [],
        (string) filemtime( $style_path )
    );
}
add_action( 'wp_enqueue_scripts', 'events_cards_enqueue_assets' );
