<?php
/**
 * Cards view root template.
 *
 * The Cards view re-uses the List view's markup so it inherits every feature of
 * the list (featured image, date/time, excerpt, venue, pagination) without
 * duplicating — and therefore without having to maintain — the plugin's
 * template across updates. The only visible difference comes from CSS: because
 * this view's slug is "cards", the container is output with the
 * `tribe-events-view--cards` class, which assets/cards.css turns into a
 * responsive card grid.
 *
 * @var \Tribe\Events\Views\V2\Template $this Template engine, scoped to the view.
 *
 * @package ABMA\Events_Cards
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$this->template( 'list' );
