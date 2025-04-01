<?php

/**
 * Load functions
 */


function load_styles() {
	wp_enqueue_style('app', get_template_directory_uri() . '/dist/build-style.css', true, '1.0', 'all');
}
add_action('wp_enqueue_scripts', 'load_styles');

function add_custom_editor_styles() {
    add_editor_style(get_template_directory_uri() . '/dist/editor-styles.css');
}
add_action('admin_init', 'add_custom_editor_styles');


/**
 * Load scripts
 */
function load_scripts()
{
	wp_enqueue_script('app', get_template_directory_uri() . '/dist/build-js.js', array('jquery'), '1.0', true);

	wp_localize_script( 'app', 'ajax', array(
    	'url' => admin_url( 'admin-ajax.php' )
	));
}
add_action('wp_footer', 'load_scripts');


function customize_acf_wysiwyg_toolbar($toolbars) {
    // Przykład: modyfikacja istniejącej grupy narzędzi "Full"
    if (isset($toolbars['Full'])) {
        // Dodanie przycisku "formats" do grupy
        array_unshift($toolbars['Full'][2], 'styleselect');
    }

    return $toolbars;
}
add_filter('acf/fields/wysiwyg/toolbars', 'customize_acf_wysiwyg_toolbar');

function add_custom_styles_to_tinymce($settings) {
    $style_formats = [
        [
            'title' => 'Font Size',
            'items' => [
                
                ['title' => '19', 'selector' => '*', 'inline' => 'span', 'classes' => 'text-19'],
                ['title' => '27', 'selector' => '*', 'inline' => 'span', 'classes' => 'text-27'],
                ['title' => '34', 'selector' => '*', 'inline' => 'span', 'classes' => 'text-34'],
                ['title' => '62', 'selector' => '*', 'inline' => 'span', 'classes' => 'text-62'],
                ['title' => '50', 'selector' => '*', 'inline' => 'span', 'classes' => 'text-50'],
                ['title' => '70', 'selector' => '*', 'inline' => 'span', 'classes' => 'text-70'],
                ['title' => '80', 'selector' => '*', 'inline' => 'span', 'classes' => 'text-80'],
            ],
        ],
        // [
        //     'title' => 'Font Weight',
        //     'items' => [
        //         ['title' => 'Light', 'selector' => '*', 'inline' => 'span', 'classes' => 'text-light'],
        //         ['title' => 'Regular', 'selector' => '*', 'inline' => 'span', 'classes' => 'text-regular'],
        //         ['title' => 'SemiBold', 'selector' => '*', 'inline' => 'span', 'classes' => 'text-semibold'],
        //         ['title' => 'Bold', 'selector' => '*', 'inline' => 'span', 'classes' => 'text-bold'],
        //         ['title' => 'ExtraBold', 'selector' => '*', 'inline' => 'span', 'classes' => 'text-extrabold'],
        //     ],
        // ],
        [
            'title' => 'Font Color',
            'items' => [
                ['title' => 'White', 'selector' => '*', 'inline' => 'span', 'classes' => 'color-white'],
                ['title' => 'Black', 'selector' => '*', 'inline' => 'span', 'classes' => 'color-black'],
                ['title' => 'Accent', 'selector' => '*', 'inline' => 'span', 'classes' => 'color-accent'],
            ],
        ],
        // [
        //     'title' => 'Font Family',
        //     'items' => [
        //         ['title' => 'Lexend Giga', 'selector' => '*', 'inline' => 'span', 'classes' => 'text-lexendgiga'],
        //         ['title' => 'DMSans', 'selector' => '*', 'inline' => 'span', 'classes' => 'text-dmsans'],
        //     ],
        // ],
        // [
        //     'title' => 'Letter Spacing',
        //     'items' => [
        //         ['title' => '-125', 'selector' => '*', 'inline' => 'span', 'classes' => 'letter-spacing-125'],
        //         ['title' => '-150', 'selector' => '*', 'inline' => 'span', 'classes' => 'letter-spacing-150'],
        //     ]
        // ],
        // [
        //     'title' => 'Font Transform',
        //     'items' => [
        //         ['title' => 'Uppercase', 'selector' => '*', 'inline' => 'span', 'classes' => 'text-uppercase'],
        //     ],
        // ],
        // [
        //     'title' => 'Margins',
        //     'items' => [
        //         ['title' => 'Top 30px', 'selector' => '*', 'inline' => 'span', 'classes' => 'mt-30'],
        //         ['title' => 'Top 40px', 'selector' => '*', 'inline' => 'span', 'classes' => 'mt-40'],
        //         ['title' => 'Top 50px', 'selector' => '*', 'inline' => 'span', 'classes' => 'mt-50'],
        //         ['title' => 'Top 60px', 'selector' => '*', 'inline' => 'span', 'classes' => 'mt-60'],
        //         ['title' => 'Top 90px', 'selector' => '*', 'inline' => 'span', 'classes' => 'mt-90'],
        //         ['title' => 'Top 120px', 'selector' => '*', 'inline' => 'span', 'classes' => 'mt-120'],
        //         ['title' => 'Bottom 30px', 'selector' => '*', 'inline' => 'span', 'classes' => 'mb-30'],
        //         ['title' => 'Bottom 40px', 'selector' => '*', 'inline' => 'span', 'classes' => 'mb-40'],
        //         ['title' => 'Bottom 50px', 'selector' => '*', 'inline' => 'span', 'classes' => 'mb-50'],
        //         ['title' => 'Bottom 60px', 'selector' => '*', 'inline' => 'span', 'classes' => 'mb-60'],
        //         ['title' => 'Bottom 90px', 'selector' => '*', 'inline' => 'span', 'classes' => 'mb-90'],
        //         ['title' => 'Bottom 120px', 'selector' => '*', 'inline' => 'span', 'classes' => 'mb-120'],
        //     ],
        // ],
        // [
        //     'title' => 'Decors',
        //     'items' => [
        //         ['title' => 'Decor heading', 'selector' => '*', 'inline' => 'span', 'classes' => 'decor-heading'],
        //     ]
        // ]
    ];

    $settings['style_formats'] = json_encode($style_formats);
    return $settings;
}
add_filter('tiny_mce_before_init', 'add_custom_styles_to_tinymce');

/**
 * Register Custom Post Type "Opinie" (Testimonials)
 */
function register_opinie_post_type() {
    $labels = array(
        'name'                  => 'Opinie',
        'singular_name'         => 'Opinia',
        'menu_name'             => 'Opinie',
        'name_admin_bar'        => 'Opinie',
        'archives'              => 'Archiwum Opinii',
        'attributes'            => 'Atrybuty Opinii',
        'parent_item_colon'     => 'Opinia nadrzędna:',
        'all_items'             => 'Wszystkie Opinie',
        'add_new_item'          => 'Dodaj nową Opinię',
        'add_new'               => 'Dodaj nową',
        'new_item'              => 'Nowa Opinia',
        'edit_item'             => 'Edytuj Opinię',
        'update_item'           => 'Aktualizuj Opinię',
        'view_item'             => 'Zobacz Opinię',
        'view_items'            => 'Zobacz Opinie',
        'search_items'          => 'Szukaj Opinii',
        'not_found'             => 'Nie znaleziono',
        'not_found_in_trash'    => 'Nie znaleziono w koszu',
        'featured_image'        => 'Zdjęcie wyróżniające',
        'set_featured_image'    => 'Ustaw zdjęcie wyróżniające',
        'remove_featured_image' => 'Usuń zdjęcie wyróżniające',
        'use_featured_image'    => 'Użyj jako zdjęcie wyróżniające',
        'items_list'            => 'Lista Opinii',
        'items_list_navigation' => 'Nawigacja listy Opinii',
        'filter_items_list'     => 'Filtruj listę Opinii',
    );
    
    $args = array(
        'label'                 => 'Opinia',
        'description'           => 'Opinie klientów',
        'labels'                => $labels,
        'supports'              => array('title'),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 5,
        'menu_icon'             => 'dashicons-format-quote',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => true,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'capability_type'       => 'post',
        'show_in_rest'          => true,
    );
    
    register_post_type('opinie', $args);
}
add_action('init', 'register_opinie_post_type');


/**
 * Shortcode for displaying testimonials slider
 */
function opinie_slider_shortcode() {
    ob_start();
    
    $opinie = new WP_Query(array(
        'post_type' => 'opinie',
        'posts_per_page' => -1,
        'order' => 'DESC',
        'orderby' => 'date'
    ));
    
    if ($opinie->have_posts()) : ?>
        <div class="opinie-slider-container">
            <div class="swiper opinie-slider">
                <div class="swiper-wrapper">
                    <?php while ($opinie->have_posts()) : $opinie->the_post();
                        $zdjecie = get_field('zdjecie');
                        $tresc = get_field('opinia');
                        $imie_nazwisko = get_field('imie_nazwisko');
                        ?>
                        <div class="swiper-slide opinie-slide">
                            <div class="opinia-item">
                                <div class="opinia-item__container">
                                    <div class="opinia-item__content">
                                        <!-- <?php if ($zdjecie) : ?>
                                            <div class="opinie-image">
                                                <img src="<?php echo esc_url($zdjecie['url']); ?>" alt="<?php echo esc_attr($imie_nazwisko); ?>">
                                            </div>
                                        <?php endif; ?> -->
                                        
                                        <div class="opinia-item__rating">
                                            <div class="stars">
                                                <span class="star">★</span>
                                                <span class="star">★</span>
                                                <span class="star">★</span>
                                                <span class="star">★</span>
                                                <span class="star">★</span>
                                            </div>
                                        </div>
                                        
                                        
                                        <?php if ($imie_nazwisko) : ?>
                                            <div class="opinia-item__author text-34 text-bold">
                                                <?php echo esc_html($imie_nazwisko); ?>
                                            </div>
                                        <?php endif; ?>
                                        <?php if ($tresc) : ?>
                                            <div class="opinia-item__text">
                                                <?php echo wp_kses_post($tresc); ?>
                                            </div>
                                        <?php endif; ?>
                                        <div class="opinia-item__verified">
                                            <img src="<?= get_template_directory_uri(  ) ?>/img/verified.png" alt=""><p>Zweryfikowany zakup</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
                
            </div>
            <div class="swiper-button-next opinie-slider-next"></div>
            <div class="swiper-button-prev opinie-slider-prev"></div>
        </div>
        
    <?php endif;
    
    wp_reset_postdata();
    return ob_get_clean();
}
add_shortcode('opinie_slider', 'opinie_slider_shortcode');