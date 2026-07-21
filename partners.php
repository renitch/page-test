<?php if( have_rows('partners') ) :

    /**
     * Logos shown before the first "See all partners" click, and how many
     * each click reveals afterwards. Everything past the first batch is
     * rendered but collapsed (see .partner-image--overflow / partners-toggle.js).
     */
    $partners_step = 8;

    $partners_rows  = get_field('partners');
    $partners_total = is_array($partners_rows) ? count($partners_rows) : 0;
    $partners_has_overflow = $partners_total > $partners_step;

    // Unique id so the button's aria-controls still resolves when several
    // partners blocks share a page.
    $partners_images_id = function_exists('wp_unique_id')
        ? wp_unique_id('partners-images-')
        : 'partners-images';
    ?>
    <div class="partners js-partners">
        <div class="grid-container">
            <?php if($partners_heading = get_field('partners_heading')) : ?>
                <div class="partners__heading">
                    <?php echo $partners_heading; ?>
                </div>
            <?php endif; ?>

            <div class="partners__images" id="<?php echo esc_attr($partners_images_id); ?>" data-partners-step="<?php echo esc_attr($partners_step); ?>">
                <?php
                $partner_index = 0;

                while( have_rows('partners') ): the_row();
                    $partner_logo = get_sub_field('partner_logo');

                    $partner_classes = 'partner-image';

                    if ( $partner_index >= $partners_step ) {
                        $partner_classes .= ' partner-image--overflow';
                    }
                    ?>
                    <div class="<?php echo esc_attr($partner_classes); ?>">
                        <?php
                        if ( ! empty($partner_logo['id']) ) {
                            echo wp_get_attachment_image($partner_logo['id'], '', false, array('class' => ''));
                        }
                        ?>
                    </div>
                    <?php
                    $partner_index++;
                endwhile; ?>
            </div>

            <?php if ( $partners_has_overflow ) : ?>
                <div class="partners__see-all">
                    <button type="button"
                            class="button partners__see-all-btn js-see-all-partners"
                            aria-controls="<?php echo esc_attr($partners_images_id); ?>"
                            hidden>
                        <?php esc_html_e('See all partners', ''); ?>
                    </button>
                </div>

                <noscript>
                    <style>#<?php echo esc_attr($partners_images_id); ?> .partner-image--overflow { display: flex !important; }</style>
                </noscript>
            <?php endif; ?>

            <?php if($partners_button = get_field('partners_button')) : ?>
                <div class="partners__button">
                    <?php acf_link($partners_button, 'button'); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>
