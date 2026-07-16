<?php
/**
 * AJAX search filter for the Members flexible block.
 *
 * Filters the `members` post type by post title (Name input) and the
 * ACF field `member_city` (County input).
 *
 * Include from functions.php:
 *   require get_stylesheet_directory() . '/inc/members-filter-ajax.php';
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

const MEMBERS_FILTER_ACTION = 'members_filter';

/**
 * Enqueue the front-end filter script.
 */
function members_filter_enqueue_assets() {
    $script_path = get_stylesheet_directory() . '/assets/js/members-filter.js';

    wp_enqueue_script(
        'members-filter',
        get_stylesheet_directory_uri() . '/assets/js/members-filter.js',
        [],
        file_exists( $script_path ) ? (string) filemtime( $script_path ) : '1.0.0',
        true
    );
}
add_action( 'wp_enqueue_scripts', 'members_filter_enqueue_assets' );

/**
 * Restrict the `s` search term to the post title only.
 *
 * Applied via the custom `members_title_like` query var so it never
 * touches other queries. Works on any WP version (no `search_columns`
 * dependency).
 *
 * @param string   $where SQL WHERE clause.
 * @param WP_Query $query Current query.
 * @return string
 */
function members_filter_title_like( $where, $query ) {
    $title = $query->get( 'members_title_like' );

    if ( is_string( $title ) && $title !== '' ) {
        global $wpdb;
        $where .= $wpdb->prepare(
            " AND {$wpdb->posts}.post_title LIKE %s",
            '%' . $wpdb->esc_like( $title ) . '%'
        );
    }

    return $where;
}
add_filter( 'posts_where', 'members_filter_title_like', 10, 2 );

/**
 * Render one member card (matches the grid markup in flexible-members.php).
 *
 * @param int $member_id Member post ID.
 * @return string
 */
function members_filter_render_card( $member_id ) {
    $member_name     = get_the_title( $member_id );
    $member_position = get_field( 'member_position', $member_id );
    $member_city     = get_field( 'member_city', $member_id );

    ob_start();
    ?>
    <div class="member" data-member="<?php echo esc_attr( $member_id ); ?>" >
        <?php if ( has_post_thumbnail( $member_id ) ) : ?>
            <div class="member__img">
                <?php echo get_the_post_thumbnail( $member_id, 'large' ); ?>
            </div>
        <?php else : ?>
            <div class="member__img">
                <img class="" alt="<?php echo __( 'post thumb placeholder', '' ); ?>" src="<?php echo IMAGE_ASSETS . 'member-thmb.jpg'; ?>">
            </div>
        <?php endif; ?>

        <div class="member__content">
            <div class="member__name">
                <?php echo $member_name ? esc_html( $member_name ) : __( 'No title', 'default' ); ?>
            </div>
            <?php if ( $member_city ) : ?>
                <div class="member__city">
                    <?php echo esc_html( $member_city ); ?>
                </div>
            <?php endif; ?>
            <?php if ( $member_position ) : ?>
                <div class="member__position">
                    <?php echo esc_html( $member_position ); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Render one member popup (matches the popup markup in flexible-members.php).
 *
 * @param int $member_id Member post ID.
 * @return string
 */
function members_filter_render_popup( $member_id ) {
    $member_name     = get_the_title( $member_id );
    $member_position = get_field( 'member_position', $member_id );
    $member_phone    = get_field( 'member_phone', $member_id );
    $member_email    = get_field( 'member_email', $member_id );

    ob_start();
    ?>
    <div class="member-popup" data-popup="<?php echo esc_attr( $member_id ); ?>" >
        <?php require get_stylesheet_directory() . '/assets/images/close.svg'; ?>

        <?php if ( has_post_thumbnail( $member_id ) ) : ?>
            <div class="member__img">
                <?php echo get_the_post_thumbnail( $member_id, 'large' ); ?>
            </div>
        <?php else : ?>
            <div class="member__img">
                <img class="" alt="<?php echo __( 'post thumb placeholder', '' ); ?>" src="<?php echo IMAGE_ASSETS . 'member-thmb.jpg'; ?>">
            </div>
        <?php endif; ?>

        <div class="member__content">
            <div class="member__name">
                <?php echo $member_name ? esc_html( $member_name ) : __( 'No title', 'default' ); ?>
            </div>
            <?php if ( $member_position ) : ?>
                <div class="member__position">
                    <?php echo esc_html( $member_position ); ?>
                </div>
            <?php endif; ?>
            <div class="member__contacts">
                <?php if ( $member_phone ) : ?>
                    <div class="member__phone">
                        <span>T:</span> <a href="tel:<?php echo sanitize_number( $member_phone ); ?>"><?php echo esc_html( $member_phone ); ?></a>
                    </div>
                <?php endif; ?>
                <?php if ( $member_email ) : ?>
                    <div class="member__email">
                        <span>E:</span> <a href="mailto:<?php echo esc_attr( $member_email ); ?>"><?php echo esc_html( $member_email ); ?></a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * AJAX handler: search members by title and/or member_city.
 */
function members_filter_ajax_handler() {
    check_ajax_referer( MEMBERS_FILTER_ACTION, 'nonce' );

    $name = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
    $city = isset( $_POST['county'] ) ? sanitize_text_field( wp_unslash( $_POST['county'] ) ) : '';

    $category_ids = [];

    if ( isset( $_POST['categories'] ) ) {
        $decoded = json_decode( wp_unslash( $_POST['categories'] ), true );

        if ( is_array( $decoded ) ) {
            $category_ids = array_filter( array_map( 'absint', $decoded ) );
        }
    }

    $args = [
        'post_type'      => 'members',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'no_found_rows'  => true,
    ];

    if ( $name !== '' ) {
        $args['members_title_like'] = $name;
    }

    if ( $city !== '' ) {
        $args['meta_query'] = [
            [
                'key'     => 'member_city',
                'value'   => $city,
                'compare' => 'LIKE',
            ],
        ];
    }

    if ( ! empty( $category_ids ) ) {
        $args['tax_query'] = [
            [
                'taxonomy' => 'members_cat',
                'field'    => 'term_id',
                'terms'    => $category_ids,
                'operator' => 'IN',
            ],
        ];
    }

    $query = new WP_Query( $args );

    $cards  = '';
    $popups = '';

    foreach ( $query->posts as $member_post ) {
        $cards  .= members_filter_render_card( $member_post->ID );
        $popups .= members_filter_render_popup( $member_post->ID );
    }

    wp_send_json_success(
        [
            'count'  => count( $query->posts ),
            'cards'  => $cards,
            'popups' => $popups,
        ]
    );
}
add_action( 'wp_ajax_' . MEMBERS_FILTER_ACTION, 'members_filter_ajax_handler' );
add_action( 'wp_ajax_nopriv_' . MEMBERS_FILTER_ACTION, 'members_filter_ajax_handler' );
