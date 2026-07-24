<?php
/**
 * Cards view class for The Events Calendar (Views V2).
 *
 * A first-class calendar view that sits alongside List, Month and Day in the
 * view selector. It inherits all of the List view's query, ordering,
 * pagination and AJAX behaviour and changes only presentation: the events are
 * rendered with the List template (see templates/cards.php) while the view
 * container carries the `tribe-events-view--cards` class — derived from this
 * view's slug — which assets/cards.css lays out as a responsive card grid.
 *
 * @package ABMA\Events_Cards
 */

namespace ABMA;

use Tribe\Events\Views\V2\Views\List_View;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class Events_Cards_View
 */
class Events_Cards_View extends List_View {

    /**
     * The view slug — drives the URL, the view selector entry and the
     * `tribe-events-view--cards` container class the stylesheet hooks into.
     *
     * @var string
     */
    protected $slug = 'cards';

    /**
     * Show the view in the front-end view selector.
     *
     * @var bool
     */
    protected static $publicly_visible = true;

    /**
     * Label shown in the view selector (instance path, used by Views V2).
     *
     * @return string
     */
    public function get_label() {
        return static::get_view_label();
    }

    /**
     * Label shown in the view selector (static path, used by some builds).
     *
     * @return string
     */
    public static function get_view_label() {
        return _x( 'Cards', 'The label of the Cards view in the selector.', 'abma' );
    }
}
