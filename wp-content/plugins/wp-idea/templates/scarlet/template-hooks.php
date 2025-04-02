<?php

use bpmj\wpidea\admin\helpers\fonts\Fonts_Helper;
use bpmj\wpidea\assets\Assets;
use bpmj\wpidea\data_types\exceptions\Object_Uninitialized_Exception;
use bpmj\wpidea\sales\product\legal\Information_About_Lowest_Price;
use bpmj\wpidea\sales\product\model\Product_ID;
use bpmj\wpidea\templates_system\admin\modules\Interface_Templates_System_Modules_Factory;
use bpmj\wpidea\templates_system\admin\modules\settings_handlers\Interface_Templates_Settings_Handler;
use bpmj\wpidea\View_Hooks;
use bpmj\wpidea\helpers\Translator_Static_Helper;
use bpmj\wpidea\modules\cart\api\Cart_API_Static_Helper;
use bpmj\wpidea\sales\product\service\Information_About_Available_Quantities;

if (!defined('ABSPATH')) {
    exit;
}

add_action('bpmj_eddcm_layout_template_settings_save', function ($settings) {
    if (!$settings || !is_array($settings)) {
        return;
    }

    $assets = new Assets(__DIR__);

    $assets_src_dir = $assets->get_source_dir();
    $assets_dest_dir = $assets->get_absolute_dir();

    /** @var Interface_Templates_Settings_Handler $templates_settings_handler */
    $templates_settings_handler = WPI()->container->get(Interface_Templates_System_Modules_Factory::class)->get_settings_handler();

    $settings = $templates_settings_handler->get_current_template_options() ?? $settings;

    $modify_css = function ($source, $settings) use ($assets) {
        $regexes = array();
        $replacements = array();
        $colors_and_fonts = array_diff_key(
            $settings,
            array_flip(array(
                'main_font',
                'secondary_font',
                'login_bg_file',
                'bg_file',
                'section_bg_file',
                'disable_banners',
            ))
        );
        // we clear redundant bg colors so that the relevant layer "sees through"
        foreach ($colors_and_fonts as $key => $color_or_font) {
            $color_subpattern = '(\/\*\s*' . preg_quote($key) . '\s*\*\/)';
            // We search for things like color: #ff0000 /* text_color */;
            $regexes[] = '/(?:#[a-fA-F0-9]{3,6}|"[a-zA-Z0-9\s]+")\s*' . $color_subpattern . '|' . $color_subpattern . '\s*(?:#[a-fA-F0-9]{3,6}|"[a-zA-Z0-9\s]+")/';

            if ('font_family' === substr($key, -11)) {
                $replacements[] = '"' . ($color_or_font ?: 'inherit') . '" $1';
            } else {
                $replacements[] = ($color_or_font ?: 'none') . ' $1';
            }
        }

        foreach (['login_bg_file', 'bg_file', 'section_bg_file'] as $bg_file_key) {
            if ($settings[$bg_file_key] !== null) {
                $regexes[] = '/\/\*\s*' . preg_quote($bg_file_key, '/') . '\s*\*\//';
                $replacements[] = 'background-image: url(' . $settings[$bg_file_key] . '); $0';
            }
        }

        // replace variables
        foreach (['{TEMPLATE_ASSETS_DIR}'] as $variable_name) {
            $regexes[] = '/' . $variable_name . '/';
            $replacements[] = BPMJ_EDDCM_TEMPLATES_URL . 'scarlet' . $assets->get_old_dir(true);
        }

        // search for fonts
        foreach (['main_font_family', 'secondary_font_family'] as $css_key) {
            $settings_key = str_replace('_family', '', $css_key); //in settings there are no '_family' suffix
            $font_type = str_replace('_font', '', $settings_key); //remove '_font' to get only 'main' or 'secondary'
            $valid_font_name = str_replace('-', ' ', $settings[$settings_key]); //change dashes to spaces
            if ($settings[$settings_key] !== null) {
                $regexes[] = '/\/\*\s*' . preg_quote($css_key, '/') . '\s*\*\//';
                $replacements[] = '--' . $font_type . '-font:"' . $valid_font_name . '", sans-serif;'; //create css variable that holds font family
            }
        }

        return preg_replace($regexes, $replacements, $source);
    };

    /* Combine and prepare CSS files */
    $import_clause = '@import url(\'https://fonts.googleapis.com/css?family='
        . urlencode(Fonts_Helper::get_google_font_name_by_slug($settings['main_font'])) . ':400,600,700|'
        . urlencode(Fonts_Helper::get_google_font_name_by_slug($settings['secondary_font'])) . ':400,500,600,700'
        . '&subset=latin-ext\');';
    $css_string = $import_clause . "\n\n";
    foreach (glob($assets_src_dir . '/css/*.css') as $css_file) {
        $css_source = file_get_contents($css_file);
        $css_source_new = preg_replace('/^@charset.+?$/m', '', $modify_css($css_source, $settings));
        $css_string .= "\n/* " . basename($css_file) . " */\n" . $css_source_new;
    }

    file_put_contents($assets_dest_dir . '/css/style.css', $css_string);

    /* Prepare SVGs copy them to destination location */
    foreach (glob($assets_src_dir . '/svg/*.svg') as $svg_file) {
        $svg_source = file_get_contents($svg_file);
        $svg_source_new = $modify_css($svg_source, $settings);
        file_put_contents($assets_dest_dir . '/img/' . basename($svg_file), $svg_source_new);
    }

    WPI()->templates->reload_template_config();
    WPI()->templates->minify_css();
});

add_filter('bpmj_eddcm_layout_filter_settings', function ($sane_settings) {
    $sane_settings['scripts_version'] = base_convert(time(), 10, 36);

    return $sane_settings;
});

add_filter('bpmj_eddcm_layout_script_version', function ($script, $template_settings) {
    if ('assets/js/courses.js' == $script) {
        return BPMJ_EDDCM_VERSION;
    }

    if (isset($template_settings['scripts_version']) && in_array($script, array(
            'assets/css/style.css',
        ))
    ) {
        return $template_settings['scripts_version'];
    }

    return false;
}, 10, 2);

add_action('bpmj_eddcm_after_upgrade', function () {
    $assets = new Assets(__DIR__);
    $assets->regenerate();
});

add_filter('edd_downloads_query', function ($query, $atts) {
    if (empty($atts['bpmj_eddcm_courses_tag'])) {
        // This filter applies only to [courses] tag
        return $query;
    }

    if (!empty($query['post__in'])) {
        // We force the query to return no rows
        $query['bpmj_eddcm_courses__post__in'] = $query['post__in'];
        $query['post__in'] = array(-1);
    }

    return $query;
}, 10, 2);

add_filter('downloads_shortcode', function ($display, $atts, $buy_button, $columns, $ignore, $downloads, $excerpt, $full_content, $price, $thumbnails, $query) {
    global $wp_query;

    if (empty($atts['bpmj_eddcm_courses_tag'])) {
        // This filter applies only to [courses] tag
        return $display;
    }

    if (!empty($query['bpmj_eddcm_courses__post__in'])) {
        // Restore the post__in query arg
        $query['post__in'] = $query['bpmj_eddcm_courses__post__in'];
        unset($query['bpmj_eddcm_courses__post__in']);
    }

    if (!empty($wp_query->query['paged'])) {
        $query['paged'] = $wp_query->query['paged'];
    }
    $downloads = new WP_Query($query);
    if ($downloads->have_posts()) :
        $i = 1;
        $wrapper_class = 'edd_download_columns_3';
        $download_class = 'box-wrapper';

        $wpidea_settings = get_option('wp_idea');
        $default_view = isset($wpidea_settings['default_view']) ? $wpidea_settings['default_view'] : 'grid';

        $user_id = get_current_user_id();
        ob_start(); ?>
        <div class="widok">
            <div class="contenter">
                <p><?php
                    _e('View mode', BPMJ_EDDCM_DOMAIN) ?></p>
                <div id="kwadrat" class="opcje_widoku active"><i class="fa fa-th-large"></i></div>
                <div id="prostokat" class="opcje_widoku "><i class="fa fa-th"></i></div>
                <div id="lista" class="opcje_widoku"><i class="fa fa-th-list"></i></div>
            </div>
        </div>

        <div class="glowna_boxy <?php
        echo apply_filters('edd_downloads_list_wrapper_class', $wrapper_class, $atts); ?>"
             data-default-view="<?= $default_view ?>"
            <?php
            View_Hooks::run(View_Hooks::RENDER_INLINE_ELEMENTS_IN_PRODUCT_LIST_BY_DOWNLOADS, $downloads); ?> >
            <div class="row">
                <?php
                while ($downloads->have_posts()) : $downloads->the_post();
                    $product_id = get_the_ID();
                    $course = WPI()->courses->get_course_by_product($product_id);
                    $show_open_padlock = false;
                    $download = new EDD_Download($product_id);
                    $sales_disabled = false;
                    if (false === $course) {
                        $product_type = get_post_meta($product_id, '_edd_product_type', true);
                        $sales_status = WPI()->courses->get_sales_status($product_id, $product_id);

                        if ('bundle' == $product_type && 'disabled' === $sales_status['status']) {
                            $sales_disabled = true;
                        }
                    } else {
                        $course_page_id = get_post_meta($course->ID, 'course_id', true);
                        $restricted_to = array(array('download' => $product_id));
                        $access = bpmj_eddpc_user_can_access($user_id, $restricted_to, $course_page_id);
                        if ('valid' === $access['status'] || 'waiting' === $access['status']) {
                            $show_open_padlock = true;
                        }
                        $sales_status = WPI()->courses->get_sales_status($course->ID, $product_id);
                        if ('disabled' === $sales_status['status']) {
                            $sales_disabled = true;
                        }
                    }

                    ?>
                    <?php
                    View_Hooks::run(View_Hooks::BEFORE_PRODUCT_LIST_ITEM) ?>
                    <div class="col-sm-12 <?php
                    echo apply_filters('edd_download_class', $download_class, get_the_ID(), $atts, $i); ?>"
                         id="edd_download_<?php
                         echo get_the_ID(); ?>">
                        <div class="box">
                            <?php

                            do_action('edd_download_before');
                            ?>
                            <div class="glowna_box_zdjecie col-sm-4">
                                <a href="<?php
                                the_permalink(); ?>" title="<?php
                                the_title_attribute(); ?>"
                                    <?php
                                    View_Hooks::run(View_Hooks::RENDER_INLINE_ELEMENTS_IN_HYPERLINK_PRODUCT, $product_id); ?>
                                   class="thumb">
                                    <?php
                                    if (in_array(get_post_thumbnail_id(get_the_ID()), ['', -1, 0], true)):
                                        ?>
                                        <span class="course-thumbnail-default">
											<span><i class="icon-hat"></i></span>
										</span>
                                    <?php
                                    else: ?>
                                        <?= get_the_post_thumbnail(get_the_ID()) ?>
                                    <?php
                                    endif; ?>
                                </a>
                            </div>
                            <?php
                            do_action('edd_download_after_thumbnail');

                            ?>
                            <div class="col-sm-5 glowna_box_product_details">
                                <?php
                                edd_get_template_part('shortcode', 'content-title');
                                ?>

                                <?php
                                bpmj_eddcm_get_course_cats_and_tags(); ?>

                                <?php
                                bpmj_eddcm_get_course_excerpt($atts); ?>

                            </div>


                            <?php
                            bpmj_eddcm_get_course_prices($atts, $download); ?>


                        </div>
                    </div>
                    <?php
                    $i++; endwhile; ?>

                <?php
                wp_reset_postdata(); ?>


            </div>
        </div>
        <?php
        if (filter_var($atts['pagination'], FILTER_VALIDATE_BOOLEAN)) : ?>

            <?php
            $pagination_args = array(
                'current' => max(1, $query['paged']),
                'total' => $downloads->max_num_pages,
                'prev_text' => '<i class="fa fa-angle-left"></i>',
                'next_text' => '<i class="fa fa-angle-right"></i>',
                'type' => 'array',
            );
            if (is_single()) {
                $pagination = paginate_links(
                    apply_filters(
                        'edd_download_pagination_args',
                        array_merge($pagination_args, array(
                            'base' => get_permalink() . '%#%',
                            'format' => '?paged=%#%',
                        )),
                        $atts,
                        $downloads,
                        $query
                    )
                );
            } else {
                $big = 999999;
                $search_for = array($big, '#038;');
                $replace_with = array('%#%', '&');
                $pagination = paginate_links(
                    apply_filters(
                        'edd_download_pagination_args',
                        array_merge($pagination_args, array(
                            'base' => str_replace($search_for, $replace_with, get_pagenum_link($big)),
                            'format' => '?paged=%#%',
                        )),
                        $atts,
                        $downloads,
                        $query
                    )
                );
            }
            ?>

            <?php
            if (!empty($pagination)) : ?>
                <div class="paginacja_boxy">
                    <div class="contenter">
                        <ul>
                            <?php
                            foreach ($pagination as $page_link): ?>
                                <?php
                                if (false === strpos($page_link, 'page-numbers current')): // not current page ?>
                                    <li>
                                        <?php
                                        if (false === strpos($page_link, 'page-numbers dots')): // not "dots" ?>
                                            <?php
                                            echo $page_link; ?>
                                        <?php
                                        else: // "dots" link ?>
                                            <a href="#"><?php
                                                echo $page_link; ?></a>
                                        <?php
                                        endif; ?>
                                    </li>
                                <?php
                                else: // current page ?>
                                    <li class="active"><a href="#"><?php
                                            echo $page_link; ?></a></li>
                                <?php
                                endif; ?>
                            <?php
                            endforeach; ?>
                        </ul>
                    </div>
                </div>
            <?php
            endif; ?>

        <?php
        endif; ?>
        <?php
        $display = ob_get_clean();
    else:
        $display = sprintf(_x('No %s found', 'download post type name', 'easy-digital-downloads'), edd_get_label_plural());
    endif;

    return $display;
}, 999, 11);

add_filter('bpmj_eddcm_template_section_css_class', function ($css_class) {
    if (!is_singular()) {
        return $css_class;
    }
    $post = get_post();
    $wpidea_settings = get_option('wp_idea');

    if (has_shortcode($post->post_content, 'courses') || has_shortcode($post->post_content, 'products')) {
        return 'content columns columns-3 bg';
    }

    return $css_class;
});

add_action('login_header', function () {
    ?>
    <div id="strona_logowania">
        <div id="panel_logowania">
            <?php
            #echo WPI()->templates->header();
           # echo WPI()->templates->get_logo(); ?>
            <div class="panel_logowania_container">
                <?php
                });

                add_action('login_footer', function () {
                ?>
            </div>
        </div>
    </div>
    <?php
});

add_action('wp_ajax_bpmj_eddcm_search_google_fonts', function () {
    global $bpmj_eddcm_template_default_get_fonts;

    $slugify = function ($text) {
        return preg_replace('/[^a-z]/', '', strtolower($text));
    };

    $fonts = $bpmj_eddcm_template_default_get_fonts();
    $limit = 10;
    $search = !empty($_REQUEST['term']) ? $slugify($_REQUEST['term']) : '';
    $search_split = str_split($search);
    $search_regex = '/' . implode('.*?', $search_split) . '/';
    $result = array();
    $found = 0;
    foreach ($fonts as $font_slug => $font) {
        if (!$search || 1 === preg_match($search_regex, $font_slug)) {
            $result[] = $font;
            ++$found;
        }
        if ($found === $limit) {
            break;
        }
    }

    wp_die(json_encode($result));
});

function display_course_categories()
{
    global $post;

    $wpidea_settings = get_option('wp_idea');
    $display_categories = empty($wpidea_settings['display_categories']) || $wpidea_settings['display_categories'] !== 'off';
    if (!$display_categories) {
        return;
    }

    $course = WPI()->courses->get_course_by_product($post->ID);
    $categories = get_the_terms($course ? $course->ID : $post->ID, 'download_category');

    if (empty($categories)) {
        return;
    }

    ?>
    <div class="box_glowna_kategorie">
        <p><?= Translator_Static_Helper::translate('product.item.categories') ?></p>
        <ul>
            <?php
            foreach ($categories as $category):
                /** @var WP_Term $category */ ?>
                <li><a href="<?php
                    echo get_term_link($category); ?>"><?php
                        echo $category->name; ?></a></li>
            <?php
            endforeach; ?>
        </ul>
    </div>
    <?php
}

add_action('edd_download_after_title', 'display_course_categories', 10);

function display_course_tags()
{
    global $post;

    $wpidea_settings = get_option('wp_idea');
    $display_tags = empty($wpidea_settings['display_tags']) || $wpidea_settings['display_tags'] !== 'off';
    if (!$display_tags) {
        return;
    }

    $course = WPI()->courses->get_course_by_product($post->ID);
    $tags = get_the_terms($course ? $course->ID : $post->ID, 'download_tag');

    if (empty($tags)) {
        return;
    }

    ?>
    <div class="box_glowna_kategorie box_glowna_kategorie--tagi">
        <div class="box_glowna_tagi">
            <p><?= Translator_Static_Helper::translate('product.item.tags') ?></p>
            <ul>
                <?php
                foreach ($tags as $tag):
                    /** @var WP_Term $tag */ ?>
                    <li><a href="<?php
                        echo get_term_link($tag); ?>">#<?php
                            echo $tag->name; ?></a></li>
                <?php
                endforeach; ?>
            </ul>
        </div>
    </div>
    <?php
}

add_action('edd_download_after_title', 'display_course_tags', 15);


add_filter('bpmj_eddcm_template_section_css_class', function ($css_class) {
    if (!is_tax('download_category')) {
        return $css_class;
    }

    $wpidea_settings = get_option('wp_idea');

    return 'content columns columns-3 bg';
});

function bpmj_edd_download_category_add_body_class($classes)
{
    if (!is_tax('download_category')) {
        return $classes;
    }

    $classes[] = 'courses';

    return $classes;
}

add_filter('body_class', 'bpmj_edd_download_category_add_body_class');

function bpmj_edd_display_404_page()
{
    if (!is_admin() && is_404()) {
        include('template_parts/404.php');
    }
}

add_action('loop_no_results', 'bpmj_edd_display_404_page');

function bpmj_edd_products_body_class($classes)
{
    $post = get_post();
    if (!empty($post->post_content)) {
        if (has_shortcode($post->post_content, 'products')) {
            $classes[] = 'courses';
        }
    }

    return $classes;
}

add_filter('body_class', 'bpmj_edd_products_body_class');


function bpmj_eddcm_scarlet_default_logo_url($logo_url)
{
    return WPI()->templates->get_template_url() . '/assets/img/wp-idea-logo.png';
}

add_filter('bpmj_eddcm_default_logo_url', 'bpmj_eddcm_scarlet_default_logo_url');

register_nav_menu('bpmj_eddcm_scarlet_footer', __('WP Idea scarlet footer', BPMJ_EDDCM_DOMAIN));

/**
 * @return string
 */
function bpmj_eddcm_scarlet_breadcrumbs_template()
{
    $tax = is_tax() ? ' breadcrumbs_tax' : '';

    return '
        <div class="breadcrumbs' . $tax . '">
            <ul>
                %s
            </ul>
        </div>';
}

add_filter('bpmj_eddcm_breadcrumbs_template', 'bpmj_eddcm_scarlet_breadcrumbs_template');

/**
 * @return string
 */
function bpmj_eddcm_scarlet_breadcrumbs_separator_template()
{
    return '<li><i class="fas fa-chevron-right"></i></li>';
}

add_filter('bpmj_eddcm_breadcrumbs_separator_template', 'bpmj_eddcm_scarlet_breadcrumbs_separator_template');

/**
 * @return string
 */
function bpmj_eddcm_scarlet_breadcrumbs_element_template()
{
    return '<li><a href="%2$s">%1$s</a></li>';
}

add_filter('bpmj_eddcm_breadcrumbs_element_template', 'bpmj_eddcm_scarlet_breadcrumbs_element_template');

/**
 * @return string
 */
function bpmj_eddcm_scarlet_breadcrumbs_current_element_template()
{
    return '<li>%1$s</li>';
}

add_filter('bpmj_eddcm_breadcrumbs_current_element_template', 'bpmj_eddcm_scarlet_breadcrumbs_current_element_template');

function bpmj_eddcm_scarlet_breadcrumbs_parents_ids($ids)
{
    if (empty($ids)) {
        $ids = array();
    }

    $ids[] = get_option('page_on_front');

    return $ids;
}

add_filter('bpmj_eddcm_breadcrumbs_parents_ids', 'bpmj_eddcm_scarlet_breadcrumbs_parents_ids');

function bpmj_eddcm_scarlet_price_after_html($formatted_price, $download_id, $price, $price_id)
{
    $display_from_word = false;
    if (edd_has_variable_prices($download_id)) {
        $prices = edd_get_variable_prices($download_id);

        if (false !== $price_id && isset($prices[$price_id])) {
            if (!isset($prices[$price_id]['regular_amount'])) {
                $regular_price = (float)$prices[$price_id]['amount'];
            } else {
                $regular_price = (float)$prices[$price_id]['regular_amount'];
                $sale_price = (float)$prices[$price_id]['sale_price'];
            }
        } else {
            $display_from_word = true;
            $lowest_id = edd_get_lowest_price_id($download_id);

            // Set prices
            $regular_price = isset($prices[$lowest_id]['regular_amount']) ? $prices[$lowest_id]['regular_amount'] : $prices[$lowest_id]['amount'];
            $sale_price = isset($prices[$lowest_id]['sale_price']) ? $prices[$lowest_id]['sale_price'] : null;
        }
    } else {
        $regular_price = get_post_meta($download_id, 'edd_price', true);
        $sale_price = get_post_meta($download_id, 'edd_sale_price', true);
    }

    $currency = edd_get_currency();
    if ($display_from_word) {
        $from_html = '<p class="glowna_box_cena_od">' . __('from', BPMJ_EDDCM_DOMAIN) . '</p>';
    } else {
        $from_html = '';
    }

    if (isset($sale_price) && '0' === $sale_price) {
        $sale_price = '0.00';
    }

    ob_start();
    if (!empty($sale_price)) : ?>
        <div class="glowna_box_cena glowna_box_cena_promo">
            <?php
            echo $from_html; ?>
            <p class="glowna_box_cena_cena"><?php
                echo edd_format_amount($sale_price) ?></p>
            <p class="glowna_box_cena_waluta"><?php
                echo $currency; ?></p>
        </div>
        <div class="glowna_box_cena glowna_box_cena_promocyjna">
            <?php
            echo $from_html; ?>
            <p class="glowna_box_cena_cena"><?php
                echo edd_format_amount($regular_price) ?></p>
            <p class="glowna_box_cena_waluta"><?php
                echo $currency; ?></p>
        </div>
    <?php
    else: ?>
        <?php
        $regular_price = floatval($regular_price); ?>
        <?php
        if ($regular_price == 0) : ?>
            <div class="glowna_box_cena glowna_box_cena_dostepny">
                <p class="glowna_box_cena_dostepny_opis gratis"><?php
                    _e('Free', BPMJ_EDDCM_DOMAIN); ?></p>
            </div>
        <?php
        else: ?>
            <div class="glowna_box_cena">
                <?php
                echo $from_html; ?>
                <p class="glowna_box_cena_cena"><?php
                    echo edd_format_amount($regular_price) ?></p>
                <p class="glowna_box_cena_waluta"><?php
                    echo $currency; ?></p>
            </div>
        <?php
        endif; ?>
    <?php
    endif;

    return ob_get_clean();
}

add_filter('edd_download_price_after_html', 'bpmj_eddcm_scarlet_price_after_html', 10, 4);

function bpmj_eddcm_sale_price_remove_filter($in)
{
    if (!class_exists('EDD_Sale_Price')) {
        return $in;
    }

    return array(
        EDD_Sale_Price()->price,
        'edd_price_maybe_display_sale_price'
    );
}

remove_filter('edd_download_price_after_html', 'bpmj_eddcm_sale_price_remove_filter');

function bpmj_eddcm_scarlet_variable_prices($download_id, $is_from_home_page_slider = false)
{
    $select_display = '';
    if ($is_from_home_page_slider) {
        $select_display = ' style="display: none;"';
    }

    $variable_pricing = edd_has_variable_prices($download_id);

    if (!$variable_pricing) {
        return;
    }

    if (edd_item_in_cart($download_id) && !edd_single_price_option_mode($download_id)) {
        return;
    }

    $type = edd_single_price_option_mode($download_id) ? 'checkbox' : 'radio';
    $mode = edd_single_price_option_mode($download_id) ? 'multi' : 'single';
    $prices = apply_filters('edd_purchase_variable_prices', edd_get_variable_prices($download_id), $download_id);

    do_action('edd_before_price_options', $download_id);

    if ('multi' === $mode): ?>
        <div class="box_glowna_wariant">
            <div class="fake-select"<?php
            echo $select_display; ?>>
                <?php
                _e('Selected variants', BPMJ_EDDCM_DOMAIN); ?>:&nbsp;<span>2</span>
            </div>
            <select class="edd_options_price_id_multi" name="edd_options[price_id]" title="<?php
            esc_attr_e('Select a variant', BPMJ_EDDCM_DOMAIN); ?>" multiple="multiple" data-download="<?php
            echo $download_id; ?>"<?php
            echo $select_display; ?>>
                <?php
                $checked_key = isset($_GET['price_option']) ? absint($_GET['price_option']) : edd_get_default_variable_price($download_id);
                if ($prices) :
                    foreach ($prices as $key => $price) :
                        if ($price['bpmj_eddcm_purchase_limit_items_left'] === 0) {
                            $checked_key = null;
                        }
                        ?>
                        <option id="edd_price_option_<?php
                    echo $download_id . '_' . sanitize_key($price['name']); ?>"
                        <?php
                    View_Hooks::run(
                        View_Hooks::RENDER_INLINE_ELEMENTS_IN_ADD_TO_CART_VARIANT_OPTIONS,
                        ['product_id' => $download_id, 'variant' => esc_attr($key)]
                    ) ?>
                                value="<?php
                                echo esc_attr($key); ?>"
                        <?php
                        selected($key, $checked_key); ?>
                        <?php
                        echo $price['bpmj_eddcm_purchase_limit_items_left'] === 0 ? ' disabled' : ''; ?>><?php
                        echo esc_html($price['name']);

                        $currency = edd_get_currency();
                        $amount = number_format($price['amount'], 2);
                        echo(" ($amount $currency)");

                        ?></option><?php

                    endforeach;
                endif;
                do_action('edd_after_price_options_list', $download_id, $prices, $type);
                ?>
            </select>
            <div class="edd_options_price_id_multi_hidden_checkboxes">
                <?php
                foreach ($prices as $key => $price) : ?>
                    <input <?php
                    checked($key, $checked_key); ?> type="checkbox" name="" class="edd_price_option_<?php
                    echo $download_id; ?> edd_price_option_hidden_checkbox_<?php
                    echo $download_id; ?>_<?php
                    echo $key; ?> edd_price_option_hidden_checkbox" value="<?php
                    echo esc_attr($key); ?>">
                <?php
                endforeach; ?>
            </div>
        </div>
    <?php
    else: ?>
        <div class="box_glowna_wariant">
            <?php
            foreach ($prices as $key => $price) : ?>
                <div>
                    <?php
                    $currency = edd_get_currency();
                    $amount = number_format($price['amount'], 2);
                    ?>
                </div>
            <?php
            endforeach; ?>
            <select name="edd_options[price_id]" title="<?php
            esc_attr_e('Select a variant', BPMJ_EDDCM_DOMAIN); ?>"<?php
            echo $select_display; ?>>
                <?php
                $checked_key = isset($_GET['price_option']) ? absint($_GET['price_option']) : edd_get_default_variable_price($download_id);
                $next_checked_key = false;
                if ($prices) :
                    foreach ($prices as $key => $price) :
                        if ($next_checked_key) {
                            $checked_key = $key;
                            $next_checked_key = false;
                        }

                        if ($price['bpmj_eddcm_purchase_limit_items_left'] === 0) {
                            $checked_key = null;
                            $next_checked_key = true;
                        }
                        ?>
                    <option id="edd_price_option_<?php
                    echo $download_id . '_' . sanitize_key($price['name']); ?>"
                        <?php
                        View_Hooks::run(
                            View_Hooks::RENDER_INLINE_ELEMENTS_IN_ADD_TO_CART_VARIANT_OPTIONS,
                            ['product_id' => $download_id, 'variant' => esc_attr($key)]
                        ) ?>
                            value="<?php
                            echo esc_attr($key); ?>"
                        <?php
                        selected($key, $checked_key); ?>
                        <?php
                        echo $price['bpmj_eddcm_purchase_limit_items_left'] === 0 ? ' disabled' : ''; ?>
                    >
                        <?php
                        echo esc_html($price['name']);

                        $currency = edd_get_currency();
                        $amount = number_format($price['amount'], 2);
                        echo(" ($amount $currency)");
                        ?></option><?php

                    endforeach;
                endif;
                do_action('edd_after_price_options_list', $download_id, $prices, $type);
                ?>
            </select>
            <input type="hidden" name="" class="edd_price_option_<?php
            echo $download_id; ?>"
                   value="<?php
                   echo esc_attr($checked_key); ?>"/>
        </div><!--end .edd_price_options-->
    <?php
    endif; ?>
    <?php
    do_action('edd_after_price_options', $download_id);
}

// Koszyk
function bpmj_eddcm_scarlet_edd_discount_field2()
{
    $output = '';
    if (isset($_GET['payment-mode']) && edd_is_ajax_disabled()) {
        return; // Only show before a payment method has been selected if ajax is disabled
    }

    if (!edd_is_checkout()) {
        return;
    }

    if (edd_has_active_discounts() && edd_get_cart_total()) :
       

    $output .= "<script>
            jQuery(document).ready(function ($) {
                $('.kod_rabatowy_contener').click(function () {
                    $(this).hide();
                    $('.kod_rabatowy_input').show();
                });

                $('#kod_rabatowy_dodaj').click(function () {
                    $('.kod_rabatowy_input').hide();
                    $('.kod_rabatowy_contener').show();
                    //$('.rabat_dodany').show();
                    //alert('a');
                   // window.location.reload();
                });
            });
        </script>";

        $output .='<p class="kod_rabatowy">';
        $output .= 'Kod znizkowy';
        $output .= '</p>';

        $output .= '<p class="kod_rabatowy_contener">';
        $output .= 'Dodaj';
        $output .= '</p>';

        $output .= '
        <div class="kod_rabatowy_input" style="display:none;">
            <input class="edd-input" type="text" id="edd-discount" name="edd-discount" placeholder="Wykorzystaj kupon"/>';

        $output .= '<div id="kod_rabatowy_dodaj" class="edd-apply-discount">';
        $output .= 'Wykorzystaj kupon</div>';

        $output .= '</div>';
        $output .= '
        <div class="rabat_dodany" style="display:none;">
            <div class="rabat_dodany_rabat">-5%</div>
        </div>
        <span id="edd-discount-error-wrap" class="edd_error edd-alert edd-alert-error" style="display:none;"></span>
        <img src="'.EDD_PLUGIN_URL.'assets/images/loading.gif" id="edd-discount-loader" style="display:none;"/>';

        return $output;
    endif;
}

function bpmj_eddcm_scarlet_edd_discount_field()
{
    if (isset($_GET['payment-mode']) && edd_is_ajax_disabled()) {
        return; // Only show before a payment method has been selected if ajax is disabled
    }

    if (!edd_is_checkout()) {
        return;
    }

    if (edd_has_active_discounts() && edd_get_cart_total()) :
        ?>

        <script>
            jQuery(document).ready(function ($) {
                $('.kod_rabatowy_contener').click(function () {
                    $(this).hide();
                    $('.kod_rabatowy_input').show();
                });

                $('#kod_rabatowy_dodaj').click(function () {
                    $('.kod_rabatowy_input').hide();
                    $('.kod_rabatowy_contener').show();
                    //$('.rabat_dodany').show();
                    //alert('a');
                   //window.location.reload();
                });
            });
        </script>

        <p class="kod_rabatowy"><?php
            _e('Discount code', BPMJ_EDDCM_DOMAIN); ?></p>
        <p class="kod_rabatowy_contener"><?php
            _e('Add', BPMJ_EDDCM_DOMAIN); ?></p>
        <div class="kod_rabatowy_input" style="display:none;">
            <input class="edd-input" type="text" id="edd-discount" name="edd-discount" placeholder="<?php
            _e('Enter discount', 'easy-digital-downloads'); ?>"/>
            <div id="kod_rabatowy_dodaj" class="edd-apply-discount"><?php
                echo _x('Apply', 'Apply discount at checkout', 'easy-digital-downloads'); ?></div>
        </div>
        <div class="rabat_dodany" style="display:none;">
            <div class="rabat_dodany_rabat">-5%</div>
        </div>
        <span id="edd-discount-error-wrap" class="edd_error edd-alert edd-alert-error" style="display:none;"></span>
        <img src="<?php
        echo EDD_PLUGIN_URL; ?>assets/images/loading.gif" id="edd-discount-loader" style="display:none;"/>
    <?php
    endif;
}

function edd_checkout_form_without_cart() { 

    $payment_mode = edd_get_chosen_gateway();
    $form_action = esc_url(edd_get_checkout_uri('payment-mode=' . $payment_mode));

    ob_start();
    ?>
    <div class="edd_clearfix">
            <?php
            do_action('edd_before_purchase_form'); ?>
            <form id="edd_purchase_form" class="edd_form" action="<?php
            echo $form_action; ?>" method="POST">
                <?php
                /**
                 * Hooks in at the top of the checkout form
                 *
                 * @since 1.0
                 */
                do_action('edd_checkout_form_top');

                if (edd_show_gateways()) {
                    do_action('edd_payment_mode_select');
                } else {
                    do_action('edd_purchase_form');
                }

                /**
                 * Hooks in at the bottom of the checkout form
                 *
                 * @since 1.0
                 */
                do_action('edd_checkout_form_bottom')
                ?>
            </form>
            <?php
            do_action('edd_after_purchase_form'); ?>
        </div><!--end #edd_checkout_form_wrap-->
    <?php
    return ob_get_clean();
}
function bpmj_eddcm_scarlet_edd_checkout_form()
{
    $payment_mode = edd_get_chosen_gateway();
    $form_action = esc_url(edd_get_checkout_uri('payment-mode=' . $payment_mode));

    ob_start();
    echo '<div id="edd_checkout_wrap">';
    echo '<div id="edd_checkout_form_wrap">';
    if (edd_get_cart_contents() || edd_cart_has_fees()) :

        edd_checkout_cart();
        ?>
        <!--end #edd_checkout_form_wrap-->
    <?php
    else:
        /**
         * Fires off when there is nothing in the cart
         *
         * @since 1.0
         */
        do_action('edd_cart_empty');
    endif;
    echo '</div></div><!--end #edd_checkout_wrap-->';
    return ob_get_clean();
}

function bpmj_eddcm_scarlet_do_shortcode_tag_download_checkout($output, $tag, $attr, $m)
{
    if ('download_checkout' != $tag) {
        return $output;
    }

    global $wpidea_settings;

    $scarlet_cart_additional_info_1_title = isset($wpidea_settings['scarlet_cart_additional_info_1_title']) ? $wpidea_settings['scarlet_cart_additional_info_1_title'] : '';
    $scarlet_cart_additional_info_1_desc = isset($wpidea_settings['scarlet_cart_additional_info_1_desc']) ? $wpidea_settings['scarlet_cart_additional_info_1_desc'] : '';
    $scarlet_cart_additional_info_2_title = isset($wpidea_settings['scarlet_cart_additional_info_2_title']) ? $wpidea_settings['scarlet_cart_additional_info_2_title'] : '';
    $scarlet_cart_additional_info_2_desc = isset($wpidea_settings['scarlet_cart_additional_info_2_desc']) ? $wpidea_settings['scarlet_cart_additional_info_2_desc'] : '';

    $scarlet_cart_secure_payments_cb = !empty($wpidea_settings['scarlet_cart_secure_payments_cb']) && 'on' === $wpidea_settings['scarlet_cart_secure_payments_cb'];

    $gateways = edd_get_enabled_payment_gateways(true);
    $payments = '';
    foreach ($gateways as $gateway_id => $gateway) {
        $slug = explode('_', $gateway_id);
        $slug = $slug[0];
        $file = bpmj_eddcm_template_get_file('assets/img/' . $slug . '.png');
        if (file_exists(BPMJ_EDDCM_DIR . 'templates/scarlet/assets/img/' . $slug . '.png')) {
            $payments .= '<img src="' . $file . '" />';
        }

        if ('paynow' === $slug) {
            $payments .= '<img src="' . bpmj_eddcm_template_get_file('assets/img/mastercard.png') . '"/>';
        }
    }

    $ret = '<div class="row"><div class="col-sm-8 content_koszyk"></div>
	<div class="col-sm-4 koszyk_right">';

    // if (!empty($scarlet_cart_additional_info_1_title)) {
    //     $ret .= '<div class="tytul_ikona">
	// 		<img src="' . bpmj_eddcm_template_get_file('assets/img/gwiazda check.png') . '"> ' .
    //         $scarlet_cart_additional_info_1_title . '
	// 	</div>
	// 	<div class="zwykly_tekst">' .
    //         $scarlet_cart_additional_info_1_desc . '
	// 	</div>';
    // }

    // if (!empty($scarlet_cart_additional_info_2_title)) {
    //     $ret .= '<div class="tytul_ikona">
	// 		<img src="' . bpmj_eddcm_template_get_file('assets/img/tarcza.png') . '"> ' .
    //         $scarlet_cart_additional_info_2_title . '
	// 	</div>
	// 	<div class="zwykly_tekst">' .
    //         $scarlet_cart_additional_info_2_desc . '
	// 	</div>';
    // }

    // if ($scarlet_cart_secure_payments_cb) {
    //     $ret .= '<div class="tytul_ikona">
	// 		<img src="' . bpmj_eddcm_template_get_file('assets/img/klodka2.png') . '"> ' .
    //         __('Secure payments', BPMJ_EDDCM_DOMAIN) . '
	// 	</div>
	// 	<div class="platnosci">' . $payments . '</div>';
    // }

    $ret .= '</div>	
	</div>';

    return $ret;
}

add_filter('do_shortcode_tag', 'bpmj_eddcm_scarlet_do_shortcode_tag_download_checkout', 10, 4);

function calculate_the_net_vat_total_price_and_display_the_label()
{
    $total_net_price = Cart_API_Static_Helper::get_formatted_amount(
            Cart_API_Static_Helper::get_the_net_total_price()
    );

    $total_net_price_without_discounts = Cart_API_Static_Helper::get_formatted_amount(
            Cart_API_Static_Helper::get_the_net_total_price(true)
    );

    $total_vat = Cart_API_Static_Helper::get_formatted_amount(
            Cart_API_Static_Helper::get_total_vat_price()
    );

    $total_vat_without_discounts = Cart_API_Static_Helper::get_formatted_amount(
            Cart_API_Static_Helper::get_total_vat_price(true)
    );

    $label = '<div class="title">'.Translator_Static_Helper::translate('templates.checkout_cart.net').':</div><div class="price" data-total-net-price="'. Cart_API_Static_Helper::get_formatted_price_with_currency($total_net_price_without_discounts). '" id="checkout_cart_total_net_price"> ' . Cart_API_Static_Helper::get_formatted_price_with_currency($total_net_price) . ' </div><br>';
    $label .= '<div class="title">'.Translator_Static_Helper::translate('templates.checkout_cart.vat').':</div><div class="price" data-total-vat="'. Cart_API_Static_Helper::get_formatted_price_with_currency($total_vat_without_discounts). '" id="checkout_cart_total_vat">' . Cart_API_Static_Helper::get_formatted_price_with_currency($total_vat) . '</div>';

    echo $label;
}

add_filter('get_cart_net_vat_total_price', 'calculate_the_net_vat_total_price_and_display_the_label');

function bpmj_eddcm_scarlet_item_price_discount_after($label, $item_id, $options)
{
    if (edd_is_checkout()) {
        $regularPrice = (float)($label);
    
        /**
         * Jeśli pozycja w koszyku jest w promocji
         */
        if (false !== strpos($label, '<ins>')) {

            /**
              * Jeśli na pozycję w promocji ma działać rabat (zweryfikuj z aktywnym rabatem i jego regułami względem pozycji)
              */
              if(checkIfIsDiscounted($item_id)) { 

                $discounts = edd_get_cart_discounts();

                foreach($discounts as $code) {
                      $discount = edd_get_discount_id_by_code($code);
                      $discount_value = edd_get_discount_amount($discount);

                      $discount_type = edd_get_discount_type($discount);

                }

                if($discount_type == 'percent') {
                    $salePrice = (number_format(get_post_meta($item_id,  'edd_sale_price', true)*(1-((int)$discount_value/100)),2,'.',''));
                $label .= '<p class="podsumowanie_koszyk_price" style="color:#16d778;">'.(number_format(get_post_meta($item_id,  'edd_sale_price', true)*(1-((int)$discount_value/100)),2,'.','')).' PLN</p>';
                } else {
                    $salePrice = (number_format(get_post_meta($item_id,  'edd_sale_price', true)*(1-((int)$discount_value/edd_get_cart_total( true ))),2,'.',''));
                    $label .= '<p class="podsumowanie_koszyk_price" style="color:#16d778;">'.(number_format(get_post_meta($item_id,  'edd_sale_price', true)*(1-((int)$discount_value/edd_get_cart_total( true ))),2,'.','')).' PLN</p>';
                }
                
                $diff = (float)$regularPrice-(float)$salePrice;
                $label .= '<div class="discount" style="background: #1CD779;color: #fff;position: absolute;right: 0;top: 50%;padding: 0 15px;transform: translateX(50%) translateY(-50%)">Oszczędzasz '.$diff.' PLN</div>';
              } else {
                /**
                 * Jeśli rabat nie działa na pozycję zwróć ją normalnie
                 */
                $label = str_replace('<ins>', '<p class="podsumowanie_koszyk_price">', $label);
                $label = str_replace('</ins>', '</p>', $label);
                $label = str_replace('<del>', '<p class="podsumowanie_koszyk_promo_price">', $label);
                $label .= '</p>';

                /**
                 *  Jeśli produkt jest w promocji ale nie obejmuje go rabat
                 */
                $salePrice = number_format(get_post_meta($item_id,  'edd_sale_price', true),2,'.','');
                if($salePrice > 0) {
                    $regularPrice = number_format(get_post_meta($item_id,  'edd_price', true),2,'.','');
                    $diff = $regularPrice - $salePrice;
                    $label .=  '<div class="discount" style="background: #1CD779;color: #fff;position: absolute;right: 0;top: 50%;padding: 0 15px;transform: translateX(50%) translateY(-50%)">Oszczędzasz '.$diff.' PLN</div>';
                }
              }
        } else {
            /**
             * Jeśli pozycja w koszyku nie jest w promocji
             */

             /**
              * Jeśli na pozycję ma działać rabat (zweryfikuj z aktywnym rabatem i jego regułami względem pozycji)
              */
                if(checkIfIsDiscounted($item_id)) { 
                    $label = '<p class="podsumowanie_koszyk_price" style="text-decoration:line-through;">' . $label . '</p>';
                    //edd_get_discount_amount(10)

      
                    $discounts = edd_get_cart_discounts();

                      foreach($discounts as $code) {
                            $discount = edd_get_discount_id_by_code($code);
                            $discount_value = edd_get_discount_amount($discount);

                            $discount_type = edd_get_discount_type($discount);

                      }
                      if($discount_type == 'percent') {
                        $salePrice = (number_format(get_post_meta($item_id,  'edd_price', true)*(1-((int)$discount_value/100)),2,'.',''));
                      } else {
                        $salePrice = (number_format(get_post_meta($item_id,  'edd_price', true)*(1-((int)$discount_value/edd_get_cart_total( true ))),2,'.',''));
                      }
                      $oldValue = $regularPrice;//(float)(get_post_meta($item_id,  'edd_sale_price', true));
                      $newValue = $salePrice;
                      $diffValue = $oldValue -  $newValue;
                      $label .= '<div class="discount" style="background: #1CD779;color: #fff;position: absolute;right: 0;top: 50%;padding: 0 15px;transform: translateX(50%) translateY(-50%)">Oszczędzasz '.$diffValue.' PLN</div>';


                      /**
                       * Jeśli jest to rabat procentowy przelicz
                       * cena * (1 - (procent/100))
                       */
                      if($discount_type == 'percent') {
                        $label .= '<p class="podsumowanie_koszyk_price" style="color:#16d778;">'.(number_format(get_post_meta($item_id,  'edd_price', true)*(1-((int)$discount_value/100)),2,'.','')).' PLN</p>';
                      }
                      else {
                        /**
                         * Jeśli jest to rabat kwotowy przelicz
                         * cena * (1 - (kwota / total))
                         */
                        //edd_get_cart_total( true ) + edd_get_cart_discounted_amount();
                        $label .= '<p class="podsumowanie_koszyk_price" style="color:#16d778;">'.(number_format(get_post_meta($item_id,  'edd_price', true)*(1-((int)$discount_value/edd_get_cart_total( true ))),2,'.','')).' PLN</p>';
                      }
                    
                   // $label .= "-".print_r(get_product_by_page_id( $item_id));
                } else {
                    /**
                     * Jeśli ma nie działać rabat na pozycję zwróć ją normalnie
                     */
                    $label = '<p class="podsumowanie_koszyk_price">' . $label . '.</p>';
                }
            
        }
       // $label .= $item_id.' ';
     
    }

    return $label;
}

/**
 * Sprawdź czy produkt kwalifikuje się do rabatu
 */
function checkIfIsDiscounted($item_id) { 

    $discounts = edd_get_cart_discounts();
   /// print_r($discounts);
    foreach($discounts as $code) {
        $discount = edd_get_discount_id_by_code($code);

        $productsRequirment = edd_get_discount_product_reqs($discount);
        
        /**
         * Jeśli w ustawieniach są wymagane produkty
         */
        if(count($productsRequirment) > 0) {

            /**
             * Jeśli produkt znajduje się w zbiorze wymaganych
             */
            if(in_array($item_id, $productsRequirment)) {
                return true;
             } else {
                 return false;
             }

        }

        $productsIgnore = edd_get_discount_excluded_products($discount);

        /**
         * Jeśli w ustawieniach rabatu są ignorowane produkty
         */
        if(count($productsIgnore) > 0) { 

            /**
             * Jeśli produkt znajduje się w zbiorze ignorowanych
             */
             if(in_array($item_id, $productsIgnore)) {
                return false;
             } else  {
                 return true;
             }
        }

        return true;
        // print_r($productsReq);

        // print_r(edd_get_discount_product_condition(514));
         
        // print_r(edd_get_discount_excluded_products(514));
        // exit();
    }
    
    //edd_get_discount_product_reqs
   
    // return true;
}

add_filter('edd_cart_item_price_label', 'bpmj_eddcm_scarlet_item_price_discount_after', 11, 3);

remove_action('edd_checkout_form_top', 'edd_discount_field', -1);

function bpmj_eddcm_scarlet_get_lesson_nav_label($label)
{
    return mb_strtoupper($label);
}

add_filter('bpmj_eddcm_get_lesson_nav_label', 'bpmj_eddcm_scarlet_get_lesson_nav_label');

function bpmj_eddcm_scarlet_the_file_icon($typeName)
{
    if ('doc' == $typeName) {
        return 'alt';
    }
    return $typeName;
}

add_filter('bpmj_eddcm_the_file_icon', 'bpmj_eddcm_scarlet_the_file_icon');

function bpmj_eddcm_scarlet_order_settings($order_settings)
{
    $order_settings[] = array(
        'name' => 'scarlet_cart_additional_info_1_title',
        'label' => __('Cart additional information 1st title', BPMJ_EDDCM_DOMAIN),
        'desc' => __(
            'The title for additional information displayed on the cart sidebar (e.g. guarantee). Leave empty to disable this section.',
            BPMJ_EDDCM_DOMAIN
        ),
        'type' => 'text',
        'size' => 'regular',
    );

    $order_settings[] = array(
        'name' => 'scarlet_cart_additional_info_1_desc',
        'label' => __('Cart additional information 1st description', BPMJ_EDDCM_DOMAIN),
        'desc' => __('The description of additional information displayed on the cart sidebar (e.g. guarantee)', BPMJ_EDDCM_DOMAIN),
        'type' => 'wysiwyg',
        'options' => array(
            'wpautop' => false,
            'textarea_rows' => 1,
            'teeny' => true,
            'media_buttons' => false,
            'quicktags' => false,
        ),
    );

    $order_settings[] = array(
        'name' => 'scarlet_cart_additional_info_2_title',
        'label' => __('Cart additional information 2nd title', BPMJ_EDDCM_DOMAIN),
        'desc' => __(
            'The title for additional information displayed on the cart sidebar (e.g. secure connection). Leave empty to disable this section.',
            BPMJ_EDDCM_DOMAIN
        ),
        'type' => 'text',
        'size' => 'regular',
    );

    $order_settings[] = array(
        'name' => 'scarlet_cart_additional_info_2_desc',
        'label' => __('Cart additional information 2nd description', BPMJ_EDDCM_DOMAIN),
        'desc' => __('The description of additional information displayed on the cart sidebar (e.g. secure connection)', BPMJ_EDDCM_DOMAIN),
        'type' => 'wysiwyg',
        'options' => array(
            'wpautop' => false,
            'textarea_rows' => 1,
            'teeny' => true,
            'media_buttons' => false,
            'quicktags' => false,
        ),
    );

    $order_settings[] = array(
        'name' => 'scarlet_cart_secure_payments_cb',
        'label' => __('Show secure payments icons', BPMJ_EDDCM_DOMAIN),
        'desc' => __('If enabled, the secure payments icons are displayed inside the additional information section.', BPMJ_EDDCM_DOMAIN),
        'type' => 'checkbox',
    );

    return $order_settings;
}

add_filter('bpmj_eddcm_order_settings', 'bpmj_eddcm_scarlet_order_settings');

// Remove 'Show full content' option from settings
add_filter('bpmj_eddcm_list_settings', 'bpmj_eddcm_remove_unsupported_options');

function bpmj_eddcm_remove_unsupported_options($options)
{
    if (isset($options['list_full_content'])) {
        unset($options['list_full_content']);
    }
    if (isset($options['list_columns'])) {
        unset($options['list_columns']);
    }
    if (isset($options['list_thumbnails'])) {
        unset($options['list_thumbnails']);
    }
    if (isset($options['list_price_button'])) {
        unset($options['list_price_button']);
    }

    return $options;
}

add_filter('bpmj_eddcm_view_settings', 'bpmj_eddcm_remove_unsupported_view_options');
function bpmj_eddcm_remove_unsupported_view_options($options)
{
    if (isset($options['download_section'])) {
        unset($options['download_section']);
    }
    if (isset($options['lesson_navigation_section'])) {
        unset($options['lesson_navigation_section']);
    }
    if (isset($options['lesson_progress_section'])) {
        unset($options['lesson_progress_section']);
    }

    return $options;
}


// Additional settings
add_filter('bpmj_eddcm_list_settings', 'bpmj_eddcm_additional_scarlet_settings_options');

function bpmj_eddcm_additional_scarlet_settings_options($options)
{
    $options['default_view'] = array(
        'name' => 'default_view',
        'label' => __('Default view', BPMJ_EDDCM_DOMAIN),
        'type' => 'select',
        'default' => 'grid',
        'options' => array(
            'grid' => __('Grid', BPMJ_EDDCM_DOMAIN),
            'grid_small' => __('Small grid', BPMJ_EDDCM_DOMAIN),
            'list' => __('List', BPMJ_EDDCM_DOMAIN),
        )
    );

    return $options;
}


// category pagination fix
function bpmj_eddcm_request($query_string)
{
    if (isset($query_string['page'])) {
        if ('' != $query_string['page']) {
            if (isset($query_string['name'])) {
                unset($query_string['name']);
            }
        }
    }
    return $query_string;
}

add_filter('request', 'bpmj_eddcm_request');

add_action('pre_get_posts', 'bpmj_eddcm_pre_get_posts');
function bpmj_eddcm_pre_get_posts($query)
{
    if ($query->is_main_query() && !$query->is_feed() && !is_admin()) {
        if (!empty($query->get('paged'))) {
            return;
        }

        $query->set('paged', str_replace('/', '', get_query_var('page')));
    }
}

require_once 'classes/class-colors-customizer.php';

new BPMJ_WPIDEA_Colors_Customizer(WPI()->settings);

add_action('bpmj_eddcm_layout_template_settings_regenerate', 'bpmj_eddcm_regenerate_scarlet_colors_setting');
function bpmj_eddcm_regenerate_scarlet_colors_setting($layout_template_settings)
{
    BPMJ_WPIDEA_Colors_Customizer::regenerate_css($layout_template_settings);
}

// FIX: #451 - problem z liczbą produktów w kategorii / tagu
function bpmj_eddcm_posts_per_page($query)
{
    if (is_archive() && (isset($query->query_vars['download_category']) || isset($query->query_vars['download_tag']))) {
        $query->set('posts_per_page', -1);
    }
}

add_action('pre_get_posts', 'bpmj_eddcm_posts_per_page', 999);

function bpmj_render_lowest_price_information($product_id)
{
    try {
        $lowest_price_information = Information_About_Lowest_Price::get_instance()->get_lowest_price_information(new Product_ID($product_id));
    } catch (Object_Uninitialized_Exception $e) {
        return;
    }

    if (empty($lowest_price_information)) {
        return;
    }

    $lowest_price_information_html = '';
    foreach ($lowest_price_information as $variant_id => $information) {
        $display = empty($variant_id) ? '' : 'style="display: none;"';
        $lowest_price_information_html .= "<div class='lowest_price_information variant_id_{$variant_id}' {$display} >";
        $lowest_price_information_html .= $information;
        $lowest_price_information_html .= '</div>';
    }

    echo $lowest_price_information_html;
}

function bpmj_render_available_quantities_information($product_id)
{
    try {
        $available_quantities_information = Information_About_Available_Quantities::get_instance()->get_available_quantities_information(new Product_ID($product_id));
    } catch (Object_Uninitialized_Exception $e) {
        return;
    }

    if (empty($available_quantities_information)) {
        return;
    }

    $available_quantities_information_html = '';
    foreach ($available_quantities_information as $variant_id => $information) {
        $display = empty($variant_id) ? '' : 'style="display: none;"';
        $available_quantities_information_html .= "<div class='available_quantities_information variant_id_{$variant_id}' {$display} >";
        $available_quantities_information_html .= $information;
        $available_quantities_information_html .= '</div>';
    }

    echo $available_quantities_information_html;
}
