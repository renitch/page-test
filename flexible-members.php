<?php

    $members_image    = get_sub_field('members_image');
    $members_tp       = get_sub_field('members_tp');
    $members_bp       = get_sub_field('members_bp');
    $members_bg_color = get_sub_field('members_bg_color');
    $members_category = get_sub_field('members_category');
    $members_slider   = get_sub_field('members_slider');
    $members_filter   = get_sub_field('members_filter');
    $members_white   = get_sub_field('members_white');
    $enable_news   = get_sub_field('enable_news');
    $enable_socials   = get_sub_field('enable_socials');
    $members_heading_left   = get_sub_field('members_heading_left');

    $members_category_ids = [];

    if (is_array($members_category)) {
        $members_category_ids = array_filter(
            array_map('absint', $members_category)
        );
    }

    $styles = [];
    $classes = [];

    if (is_numeric($members_tp)) {
        $styles[] = 'padding-top: ' . px_to_rem((float) $members_tp);
    }

    if (is_numeric($members_bp)) {
        $styles[] = 'padding-bottom: ' . px_to_rem((float) $members_bp);
    }

    if (is_string($members_bg_color) && $members_bg_color !== '') {
        $styles[] = 'background-color: ' . sanitize_hex_color($members_bg_color);
    }

    if($members_white) {
        $classes[] = 'members-section--white-color';
    }

    if($members_bg_color) {
        $classes[] = 'members-section--bg';
    }

    $classes_attr = implode(
        ' ',
        array_map( 'sanitize_html_class', array_filter( $classes ) )
    );

    $styles_attr = '';

    if (!empty($styles)) {
        $styles_attr = sprintf(
            ' style="%s"',
            esc_attr(implode('; ', $styles))
        );
    }

?>

<div class="members-section rel-wrap <?php echo esc_attr( $classes_attr ); ?>" <?php echo $styles_attr; ?>>

    <?php if($enable_news): get_template_part( 'parts/blocks/latest-news' ); endif; ?>
    <?php if($enable_socials): get_template_part( 'parts/blocks/feed' ); endif; ?>

    <?php if($members_image) : ?>
        <?php echo wp_get_attachment_image($members_image['id'], '', false, array('class' => 'stretched-img members-section__bg')); ?>
    <?php endif; ?>

    <div class="grid-container rel-content">
        <div class="members__inner <?php echo $members_heading_left ? 'left-title' : ''; ?>">
            <?php if(get_sub_field('members_heading')) : ?>
                <div class="members-section__title">
                    <?php the_sub_field('members_heading'); ?>
                </div>
            <?php endif; ?>
            <?php if(get_sub_field('members_text')) : ?>
                <div class="members-section__text">
                    <?php the_sub_field('members_text'); ?>
                </div>
            <?php endif; ?>
            <?php if($members_filter) : ?>
                <div class="member-filter js-member-filter"
                     data-ajax-url="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>"
                     data-nonce="<?php echo esc_attr( wp_create_nonce( 'members_filter' ) ); ?>"
                     data-categories="<?php echo esc_attr( wp_json_encode( array_values( $members_category_ids ) ) ); ?>">
                    <div class="member-filter__item">
                        <label class="member-filter__title" for="by_county"><?php echo __('County', ''); ?></label>
                        <input class="member-filter__input js-filter-county" type="text" id="by_county" name="by_county" autocomplete="off" placeholder="">
                    </div>
                    <div class="">
                        <label class="member-filter__title" for="by_name"><?php echo __('Name', ''); ?></label>
                        <input class="member-filter__input js-filter-name" type="text" id="by_name" name="by_name" autocomplete="off" placeholder="">
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <?php if($members_filter) : ?>
            <div class="members members--search-results js-members-results" hidden></div>
            <div class="js-members-popups" hidden></div>
            <div class="members-search-empty js-members-empty" hidden><?php echo __('No members found.', ''); ?></div>
        <?php endif; ?>

        <?php

        $members_args = [
            'post_type'      => 'members',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'no_found_rows'  => true,
        ];

        if (!empty($members_category_ids)) {
            $members_args['tax_query'] = [
                [
                    'taxonomy' => 'members_cat',
                    'field'    => 'term_id',
                    'terms'    => $members_category_ids,
                    'operator' => 'IN',
                ],
            ];
        }

        $members_query = new WP_Query($members_args);

        $member_ids = wp_list_pluck( $members_query->posts, 'ID' );

        if ( empty( $member_ids ) ) {
            return;
        }

        $member_groups = array_chunk( $member_ids, 7 ); ?>

        <?php if( $members_query->have_posts() ) : ?>
            <div class="js-members-default">
            <?php if($members_slider) : ?>
                <div class="members-slider">
                    <?php foreach ( $member_groups as $member_group ) : ?>
                        <div class="members-slider__slide">
                            <div class="members-slider__grid">
                                <?php foreach ( $member_group as $member_id ) : ?>
                                    <?php
                                    $member_name     = get_the_title( $member_id );
                                    $member_position = get_field( 'member_position', $member_id );
                                    $member_city     = get_field( 'member_city', $member_id );
                                    $member_phone     = get_field( 'member_phone', $member_id );
                                    $member_email     = get_field( 'member_email', $member_id );
                                    ?>

                                    <div class="member" data-member="<?php echo $member_id; ?>">
                                        <div class="member__img">
                                            <?php if ( has_post_thumbnail($member_id) ) :
                                                echo get_the_post_thumbnail($member_id);  ?>
                                            <?php else: ?>
                                                <img class="" alt="<?php echo __('post thumb placeholder', ''); ?>" src="<?php echo IMAGE_ASSETS . 'member-thmb.jpg'; ?>">
                                            <?php endif; ?>
                                        </div>
                                        <div class="member__content">
                                            <?php if ( $member_name ) : ?>
                                                <div class="member__name">
                                                    <?php echo esc_html( $member_name ); ?>
                                                </div>
                                            <?php endif; ?>

                                            <?php if ( $member_position ) : ?>
                                                <div class="member__position">
                                                    <?php echo esc_html( $member_position ); ?>
                                                </div>
                                            <?php endif; ?>

                                            <?php if ( $member_city ) : ?>
                                                <div class="member__city">
                                                    <?php echo esc_html( $member_city ); ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>

                                    </div>
                                    <div class="member-popup" data-popup="<?php echo $member_id ?>" >
                                        <?php require get_stylesheet_directory() . '/assets/images/close.svg'; ?>
                                        <div class="member__img">
                                            <?php if ( has_post_thumbnail($member_id) ) :
                                                echo get_the_post_thumbnail($member_id);  ?>
                                            <?php else: ?>
                                                <img class="" alt="<?php echo __('post thumb placeholder', ''); ?>" src="<?php echo IMAGE_ASSETS . 'member-thmb.jpg'; ?>">
                                            <?php endif; ?>
                                        </div>
                                        <div class="member__content">
                                            <?php if ( $member_name ) : ?>
                                                <div class="member__name">
                                                    <?php echo esc_html( $member_name ); ?>
                                                </div>
                                            <?php endif; ?>

                                            <?php if ( $member_position ) : ?>
                                                <div class="member__position">
                                                    <?php echo esc_html( $member_position ); ?>
                                                </div>
                                            <?php endif; ?>
                                            <div class="member__contacts">
                                                <?php if($member_phone) : ?>
                                                    <div class="member__phone">
                                                        <span>T:</span> <a href="tel:<?php echo sanitize_number($member_phone) ?>"><?php echo $member_phone; ?></a>
                                                    </div>
                                                <?php endif; ?>
                                                <?php if($member_email) : ?>
                                                    <div class="member__email">
                                                        <span>E:</span> <a href="mailto:<?php echo $member_email; ?>"><?php echo $member_email; ?></a>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                    </div>
                                <?php endforeach; ?>
                                <?php wp_reset_postdata(); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>

                </div>
            <?php else: ?>
                <div class="members">
                    <?php while( $members_query->have_posts() ) : $members_query->the_post();?>
                        <div class="member" data-member="<?php echo get_the_id(); ?>" >
                            <?php if ( has_post_thumbnail() ) : ?>
                                <div class="member__img">
                                    <?php the_post_thumbnail( 'large', array( 'class' => '' ) ); ?>
                                </div>
                            <?php else: ?>
                                <div class="member__img">
                                    <img class="" alt="<?php echo __('post thumb placeholder', ''); ?>" src="<?php echo IMAGE_ASSETS . 'member-thmb.jpg'; ?>">
                                </div>
                            <?php endif; ?>

                            <div class="member__content">
                                <div class="member__name">
                                    <?php echo get_the_title() ?: __( 'No title', 'default' ); ?>
                                </div>
                                <?php if(get_field('member_city')) : ?>
                                    <div class="member__city">
                                        <?php the_field('member_city'); ?>
                                    </div>
                                <?php endif; ?>
                                <?php if(get_field('member_position')) : ?>
                                    <div class="member__position">
                                        <?php the_field('member_position'); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>

                <?php while( $members_query->have_posts() ) : $members_query->the_post(); ?>
                    <div class="member-popup" data-popup="<?php echo get_the_id(); ?>" >
                        <?php require get_stylesheet_directory() . '/assets/images/close.svg'; ?>

                        <?php if ( has_post_thumbnail() ) : ?>
                            <div class="member__img">
                                <?php the_post_thumbnail( 'large', array( 'class' => '' ) ); ?>
                            </div>
                        <?php else: ?>
                            <div class="member__img">
                                <img class="" alt="<?php echo __('post thumb placeholder', ''); ?>" src="<?php echo IMAGE_ASSETS . 'member-thmb.jpg'; ?>">
                            </div>
                        <?php endif; ?>

                        <div class="member__content">
                            <div class="member__name">
                                <?php echo get_the_title() ?: __( 'No title', 'default' ); ?>
                            </div>
                            <?php if(get_field('member_position')) : ?>
                                <div class="member__position">
                                    <?php the_field('member_position'); ?>
                                </div>
                            <?php endif; ?>
                            <div class="member__contacts">
                                <?php if($member_phone = get_field('member_phone')) : ?>
                                    <div class="member__phone">
                                        <span>T:</span> <a href="tel:<?php echo sanitize_number($member_phone) ?>"><?php echo $member_phone; ?></a>
                                    </div>
                                <?php endif; ?>
                                <?php if($member_email = get_field('member_email')) : ?>
                                    <div class="member__email">
                                        <span>E:</span> <a href="mailto:<?php echo $member_email; ?>"><?php echo $member_email; ?></a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>

                <?php wp_reset_postdata(); ?>
            <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>


</div>
