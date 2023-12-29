<?php

use bpmj\wpidea\View_Hooks;
use bpmj\wpidea\sales\product\legal\Information_About_Lowest_Price;
use bpmj\wpidea\sales\product\model\Product_ID;
use bpmj\wpidea\data_types\exceptions\Object_Uninitialized_Exception;

require __DIR__.'/product-list-fnc.php';
require __DIR__.'/cart-crosseling.php';

function bpmj_courses_panel_shortcode($attr, $content ) {
	global $post;

	$attr = shortcode_atts( array(
	    'id'	 => '',
	    'mode'	 => '',
	    'time'	 => '',
	    'img'	 => '',
	    'txt'	 => ''
		), $attr );

	$top = $post->ID;

	$childpages = new WP_Query( array(
	    'post_type'	 => 'page',
	    'post_parent'	 => $top,
	    'posts_per_page' => 100,
	    'order'		 => 'ASC',
	    'orderby'	 => 'menu_order'
		) );

	ob_start();
	?>

	<div style="width: 968px; margin: 0 auto;">

		<?php
		while ( $childpages->have_posts() ) : $childpages->the_post();
			?>


			<figure id="<?php echo $top . '_' . $post->ID; ?>" style="width: 300px" class="wp-caption alignleft">
				<a href="<?php echo esc_url( get_permalink() ); ?>">
					<?php
					$thumb = get_the_post_thumbnail();
					if ( !empty( $thumb ) ) {
						echo $thumb;
					} else {
						echo '<img width="300" height="169" src="' . plugins_url( 'assets/imgs/tn-style-4-video-4.jpg', dirname( __FILE__ ) ) . '" class="attachment-post-thumbnail size-post-thumbnail wp-post-image" alt="' . get_the_title() . '">';
					}
					?>
				</a>
				<figcaption class="wp-caption-text"><a href="<?php echo esc_url( get_permalink() ); ?>"><?php the_title(); ?></a></figcaption></figure>


			<?php
		endwhile;
		wp_reset_query();
		?>

	</div>

	<?php
	return ob_get_clean();
}

add_shortcode( 'bpmj_courses_panel', 'bpmj_courses_panel_shortcode' );

function bpmj_template_content_button( $attr, $content ) {
	$attr = shortcode_atts( array(
	    'href'	 => '',
	    'title'	 => ''
		), $attr );

	return '<a href="' . $attr[ 'href' ] . '"><span class="button">' . $attr[ 'title' ] . '</span></a>';
}

add_shortcode( 'wpi_btn', 'bpmj_template_content_button' );

function bpmj_eddcm_courses_list( $shortcode_atts, $content = null ) {
	global $wpdb;

	$wpidea_settings = get_option( 'wp_idea' );
	$list_options    = array( 'ids' => null );
	if ( !empty( $wpidea_settings ) ) {
		foreach ( $wpidea_settings as $setting => $value ) {
			if ( substr( $setting, 0, 5 ) === 'list_' ) {
				$list_options[ substr( $setting, 5 ) ] = $value;
			}
		}
	}
	$attr                             = shortcode_atts( $list_options, $shortcode_atts );
	$attr[ 'bpmj_eddcm_courses_tag' ] = true;

	$query = "
        SELECT p.ID   
          FROM {$wpdb->posts} p 
         WHERE p.post_type = 'download'
               AND p.post_status = 'publish'
               AND (
                    -- select all published bundles
                    EXISTS (SELECT 1 
                              FROM {$wpdb->postmeta} pm 
                             WHERE pm.post_id = p.ID 
                                   AND pm.meta_key = '_edd_product_type' 
                                   AND pm.meta_value = 'bundle') OR
                    -- select all published courses
                    EXISTS (SELECT 1 
                              FROM {$wpdb->posts} p_course, {$wpdb->postmeta} pm_course 
                             WHERE p_course.ID = pm_course.post_id 
                                   AND pm_course.meta_key = 'product_id' 
                                   AND pm_course.meta_value = p.ID 
                                   AND p_course.post_Type = 'courses' 
                                   AND p_course.post_status = 'publish')
               )
               -- select only if the product should be visible on a list
               AND NOT EXISTS (SELECT 1 
                                 FROM {$wpdb->postmeta} pm 
                                WHERE pm.post_id = p.ID 
                                      AND pm.meta_key = 'hide_from_lists'
                                      AND pm.meta_value = 'on')
	";

	$all_product_ids = wp_list_pluck( $wpdb->get_results( $query ), 'ID' );
	$output          = '<p class="no-results">' . __( 'There are no courses in the catalog at this time', BPMJ_EDDCM_DOMAIN ) . '</p>';
	if ( ! empty( $all_product_ids ) ) {
		
		if( empty ( $attr[ 'ids' ] ) ) {
			$attr[ 'ids' ] = implode( ',', array_unique( $all_product_ids ) );
		}
		else {
			$ids_arr = explode( ',', $attr[ 'ids' ] );
			foreach( $ids_arr as $k => $v ) {
				$ids_arr[ $k ] = WPI()->courses->get_product_by_course( $v );
			}
			$attr[ 'ids' ] = implode( ',', array_unique( $ids_arr ) );
		}

		/**
		 * The below is not pretty, but it's the only sure way to check if @see edd_downloads_query() returned
		 * any products
		 */
		$did_action_before = did_action( 'edd_download_before' );
		$output_query      = edd_downloads_query( $attr, $content );
		$has_any_download  = did_action( 'edd_download_before' ) > $did_action_before;
		if ( $has_any_download ) {
			$output = $output_query;
		}
	}

	return $output;
}

add_shortcode( 'courses', 'bpmj_eddcm_courses_list' );

function bpmj_eddcm_purchase_shortcode( $attr ) {
    global $edd_receipt_args;

    if( empty( $edd_receipt_args ) )
        return '';

    $payment = get_post( $edd_receipt_args[ 'id' ] );
    if( empty( $payment ) )
        return '';

    $attr = shortcode_atts( array(
        'param'	 => 'id',
    ), $attr );

    $return_value = '';

    if( $attr[ 'param' ] == 'id' ) {
        $return_value = $payment->ID;
    } else if( $attr[ 'param' ] == 'total' ) {
        $return_value = edd_get_payment_amount( $payment->ID );
    }

    return $return_value;
}

add_shortcode( 'wpi_purchase', 'bpmj_eddcm_purchase_shortcode' );

function bpmj_eddcm_products_shortcode( $shortcode_atts, $content = null ) {
    global $wpdb;

    $wpidea_settings = get_option( 'wp_idea' );
    $list_options    = array( 'ids' => null );
    if ( !empty( $wpidea_settings ) ) {
        foreach ( $wpidea_settings as $setting => $value ) {
            if ( substr( $setting, 0, 5 ) === 'list_' ) {
                $list_options[ substr( $setting, 5 ) ] = $value;
            }
        }
    }
    $attr                             = shortcode_atts( $list_options, $shortcode_atts );
    $attr[ 'bpmj_eddcm_courses_tag' ] = true;

    $query = "
        SELECT p.ID   
          FROM {$wpdb->posts} p 
         WHERE p.post_type = 'download'
               AND p.post_status = 'publish'
               -- select only if the product should be visible on a list
               AND NOT EXISTS (SELECT 1 
                                 FROM {$wpdb->postmeta} pm 
                                WHERE pm.post_id = p.ID 
                                      AND pm.meta_key = 'hide_from_lists'
                                      AND pm.meta_value = 'on')
	";

    $all_product_ids = wp_list_pluck( $wpdb->get_results( $query ), 'ID' );
    $products_to_display = array();
    foreach( $all_product_ids as $download_id ) {
        $args = array(
            'post_type' => 'courses',
            'post_status' => array('any'),
            'meta_query' => array(
                array(
                    'key' => 'product_id',
                    'value' => $download_id
                )
            )
        );

        $course_query = new WP_Query( $args );

        if( $course_query->have_posts() ) {
            while( $course_query->have_posts() ) {
                $post = $course_query->the_post();
                $course_id = get_the_ID();
                $status = get_post_status($course_id );
                if( $status == 'publish' ) {
                    $products_to_display[] = $download_id;
                }
            }
        } else {
            $products_to_display[] = $download_id;
        }
    }

    $output          = __( 'There are no products in the catalog at this time', BPMJ_EDDCM_DOMAIN );
    if ( ! empty( $products_to_display ) ) {

        if( empty ( $attr[ 'ids' ] ) ) {
            $attr[ 'ids' ] = implode( ',', array_unique( $products_to_display ) );
        }
        else {
            $ids_arr = explode( ',', $attr[ 'ids' ] );
            $attr[ 'ids' ] = implode( ',', array_unique( $ids_arr ) );
        }

        /**
         * The below is not pretty, but it's the only sure way to check if @see edd_downloads_query() returned
         * any products
         */
        $did_action_before = did_action( 'edd_download_before' );
        $output_query      = edd_downloads_query( $attr, $content );
        $has_any_download  = did_action( 'edd_download_before' ) > $did_action_before;
        if ( $has_any_download ) {
            $output = $output_query;
        }
    }

    return $output;
}

add_shortcode( 'products', 'bpmj_eddcm_products_shortcode' );

function bpmj_eddcm_downloads_query( $atts, $content = null ) {
	global $wpdb;
	
	$atts = shortcode_atts( array(
		'category'         => '',
		'exclude_category' => '',
		'tags'             => '',
		'exclude_tags'     => '',
		'relation'         => 'OR',
		'number'           => 9,
		'price'            => 'no',
		'excerpt'          => 'yes',
		'full_content'     => 'no',
		'buy_button'       => 'yes',
		'columns'          => 3,
		'thumbnails'       => 'true',
		'orderby'          => 'post_date',
		'order'            => 'DESC',
		'ids'              => '',
		'pagination'       => 'true'
	), $atts, 'downloads' );

	$query = array(
		'post_type'      => 'download',
		'orderby'        => $atts['orderby'],
		'order'          => $atts['order'],
		'meta_query'	 => array(
			'relation'	 => 'OR',
			array (
				'key'     => 'hide_from_lists',
				'value'   => 'on',
				'compare' => '!='
			),
			array(
				'key' => 'hide_from_lists',
				'compare' => 'NOT EXISTS'
			),
		)
	);

	if ( filter_var( $atts['pagination'], FILTER_VALIDATE_BOOLEAN ) || ( ! filter_var( $atts['pagination'], FILTER_VALIDATE_BOOLEAN ) && $atts[ 'number' ] ) ) {

		$query['posts_per_page'] = (int) $atts['number'];

		if ( $query['posts_per_page'] < 0 ) {
			$query['posts_per_page'] = abs( $query['posts_per_page'] );
		}
	} else {
		$query['nopaging'] = true;
	}

	switch ( $atts['orderby'] ) {
		case 'price':
			$atts['orderby']   = 'meta_value';
			$query['meta_key'] = 'edd_price';
			$query['orderby']  = 'meta_value_num';
		break;

		case 'title':
			$query['orderby'] = 'title';
		break;

		case 'id':
			$query['orderby'] = 'ID';
		break;

		case 'random':
			$query['orderby'] = 'rand';
		break;

		default:
			$query['orderby'] = 'post_date';
		break;
	}

	if ( $atts['tags'] || $atts['category'] || $atts['exclude_category'] || $atts['exclude_tags'] ) {

		$query['tax_query'] = array(
			'relation' => $atts['relation']
		);

		if ( $atts['tags'] ) {

			$tag_list = explode( ',', $atts['tags'] );

			foreach( $tag_list as $tag ) {

				if( is_numeric( $tag ) ) {

					$term_id = $tag;

				} else {

					$term = get_term_by( 'slug', $tag, 'download_tag' );

					if( ! $term ) {
						continue;
					}

					$term_id = $term->term_id;
				}

				$query['tax_query'][] = array(
					'taxonomy' => 'download_tag',
					'field'    => 'term_id',
					'terms'    => $term_id
				);
			}

		}

		if ( $atts['category'] ) {

			$categories = explode( ',', $atts['category'] );

			foreach( $categories as $category ) {

				if( is_numeric( $category ) ) {

					$term_id = $category;

				} else {

					$term = get_term_by( 'slug', $category, 'download_category' );

					if( ! $term ) {
						continue;
					}

					$term_id = $term->term_id;

				}

				$query['tax_query'][] = array(
					'taxonomy' => 'download_category',
					'field'    => 'term_id',
					'terms'    => $term_id,
				);

			}

		}

		if ( $atts['exclude_category'] ) {

			$categories = explode( ',', $atts['exclude_category'] );

			foreach( $categories as $category ) {

				if( is_numeric( $category ) ) {

					$term_id = $category;

				} else {

					$term = get_term_by( 'slug', $category, 'download_category' );

					if( ! $term ) {
						continue;
					}

					$term_id = $term->term_id;
				}

				$query['tax_query'][] = array(
					'taxonomy' => 'download_category',
					'field'    => 'term_id',
					'terms'    => $term_id,
					'operator' => 'NOT IN'
				);
			}

		}

		if ( $atts['exclude_tags'] ) {

			$tag_list = explode( ',', $atts['exclude_tags'] );

			foreach( $tag_list as $tag ) {

				if( is_numeric( $tag ) ) {

					$term_id = $tag;

				} else {

					$term = get_term_by( 'slug', $tag, 'download_tag' );

					if( ! $term ) {
						continue;
					}

					$term_id = $term->term_id;
				}

				$query['tax_query'][] = array(
					'taxonomy' => 'download_tag',
					'field'    => 'term_id',
					'terms'    => $term_id,
					'operator' => 'NOT IN'
				);

			}

		}
	}

	if ( $atts['exclude_tags'] || $atts['exclude_category'] ) {
		$query['tax_query']['relation'] = 'AND';
	}

	if( ! empty( $atts['ids'] ) )
		$query['post__in'] = explode( ',', $atts['ids'] );

	if ( get_query_var( 'paged' ) )
		$query['paged'] = get_query_var('paged');
	else if ( get_query_var( 'page' ) )
		$query['paged'] = get_query_var( 'page' );
	else
		$query['paged'] = 1;

	// Allow the query to be manipulated by other plugins
	$query = apply_filters( 'edd_downloads_query', $query, $atts );

	$downloads = new WP_Query( $query );
	if ( $downloads->have_posts() ) :
		$i = 1;
		$wrapper_class = 'edd_download_columns_' . $atts['columns'];
		ob_start(); ?>
		<div class="edd_downloads_list <?php echo apply_filters( 'edd_downloads_list_wrapper_class', $wrapper_class, $atts ); ?>">
			<?php while ( $downloads->have_posts() ) : $downloads->the_post(); ?>
				<?php View_Hooks::run(View_Hooks::BEFORE_PRODUCT_LIST_ITEM) ?>
                class="<?php echo apply_filters( 'edd_download_class', 'edd_download', get_the_ID(), $atts, $i ); ?>" id="edd_download_<?php echo get_the_ID(); ?>">
					<div class="edd_download_inner">
						<?php

						do_action( 'edd_download_before' );

						if ( 'false' != $atts['thumbnails'] ) :
							edd_get_template_part( 'shortcode', 'content-image' );
							do_action( 'edd_download_after_thumbnail' );
						endif;

						edd_get_template_part( 'shortcode', 'content-title' );
						do_action( 'edd_download_after_title' );

						if ( $atts['excerpt'] == 'yes' && $atts['full_content'] != 'yes' ) {
							edd_get_template_part( 'shortcode', 'content-excerpt' );
							do_action( 'edd_download_after_content' );
						} else if ( $atts['full_content'] == 'yes' ) {
							edd_get_template_part( 'shortcode', 'content-full' );
							do_action( 'edd_download_after_content' );
						}

						if ( $atts['price'] == 'yes' ) {
							edd_get_template_part( 'shortcode', 'content-price' );
							do_action( 'edd_download_after_price' );
						}

						if ( $atts['buy_button'] == 'yes' )
							edd_get_template_part( 'shortcode', 'content-cart-button' );

						do_action( 'edd_download_after' );

						?>
					</div>
				</div>
				<?php if ( $atts['columns'] != 0 && $i % $atts['columns'] == 0 ) { ?><div style="clear:both;"></div><?php } ?>
			<?php $i++; endwhile; ?>

			<div style="clear:both;"></div>

			<?php wp_reset_postdata(); ?>

			<?php if ( filter_var( $atts['pagination'], FILTER_VALIDATE_BOOLEAN ) ) : ?>

			<?php
				$pagination = false;

				if ( is_single() ) {
					$pagination = paginate_links( apply_filters( 'edd_download_pagination_args', array(
						'base'    => get_permalink() . '%#%',
						'format'  => '?paged=%#%',
						'current' => max( 1, $query['paged'] ),
						'total'   => $downloads->max_num_pages
					), $atts, $downloads, $query ) );
				} else {
					$big = 999999;
					$search_for   = array( $big, '#038;' );
					$replace_with = array( '%#%', '&' );
					$pagination = paginate_links( apply_filters( 'edd_download_pagination_args', array(
						'base'    => str_replace( $search_for, $replace_with, get_pagenum_link( $big ) ),
						'format'  => '?paged=%#%',
						'current' => max( 1, $query['paged'] ),
						'total'   => $downloads->max_num_pages
					), $atts, $downloads, $query ) );
				}
			?>

			<?php if ( ! empty( $pagination ) ) : ?>
			<div id="edd_download_pagination" class="navigation">
				<?php echo $pagination; ?>
			</div>
			<?php endif; ?>

			<?php endif; ?>

		</div>
		<?php
		$display = ob_get_clean();
	else:
		$display = sprintf( _x( 'No %s found', 'download post type name', 'easy-digital-downloads' ), edd_get_label_plural() );
	endif;

	return apply_filters( 'downloads_shortcode', $display, $atts, $atts['buy_button'], $atts['columns'], '', $downloads, $atts['excerpt'], $atts['full_content'], $atts['price'], $atts['thumbnails'], $query );
}
add_shortcode( 'downloads_wpi', 'bpmj_eddcm_downloads_query' );

/**
 * Displays "go to the next undone lesson" button
 * 
 * @param type $atts
 * @return string
 * 
 * @note $atts['course_id'] can contains course id (cpt) or top page id (course panel page)
 */

function bpmj_eddcm_wpi_continue_shortcode( $atts ) {
	// needed for last lesson in course
	$course_id_from_post = array_key_exists('course_page_id', $_POST) ? $_POST['course_page_id'] : null;

    $atts = shortcode_atts( array(
        'course_id' => null,
        'text'      => null,
        'wpi_icon'  => null,
    ), $atts );

	if( empty( $atts['course_id'] ) ) {
        $atts['course_id'] = $course_id_from_post;
    }

	if( empty( $atts['course_id'] ) ) {
        $atts['course_id'] = WPI()->courses->get_course_top_page(get_the_ID());
    }
	    
	// try to check if we have id of top page or course cpt
	$type = get_post_type( $atts['course_id'] );
	if ( 'page' !== $type ) {
        $atts['course_id'] = WPI()->courses->get_page_id_by_course_id( $atts['course_id'] );
    }

	$course = WPI()->courses->get_course_by_page( $atts['course_id'] );

	if( empty( $course ) ) {
		return;
	}
	
	if ( ! WPI()->courses->course_has_content( $course->ID) )
        return '';

    if ( ! is_null( $atts['wpi_icon'] ) ) {
        $atts['text'] .= '<i class="fas fa-angle-double-right"></i>';
    }

    return '<a href="' . get_the_permalink( $atts['course_id'] ) . '?continue_course=' . $atts['course_id'] . '" title="' . __( 'Continue course', BPMJ_EDDCM_DOMAIN ) . '">' . $atts['text'] . '</a>';
}

add_shortcode( 'wpi_continue_anchor', 'bpmj_eddcm_wpi_continue_shortcode' );

/**
 * Shortcode wyprowadzające listę produktów
 */

add_shortcode('mjcourses','mjcourses');


function mjcourses($atts) { 

	$categoryProduct = (string)$atts['category'];
	$quantityProduct = (int)$atts['quantity'];
	$categoryLabels = (int)$atts['category-labels'];
	$tagLabels = (int)$atts['tag-labels'];

	$output = '';

	$outputCategories = '';
	$outputLevels = '';

	$output .= '<form method="POST" class="mjfilter">';

	
	$output .= '<input type="hidden" name="filter_type"  id="filter_type" value="category"/>';
	$output .= '<input type="hidden" name="id_category_tag"  id="id_category_tag" value="null"/>';
	// $output .= '<input type="hidden" name="id_category"  id="id_category" value="null"/>';
	// $output .= '<input type="hidden" name="id_tag"  id="id_tag" value="null"/>';

	$output .= '<input type="hidden" name="cols" value="3"/>';

	$output .= ' <input type="hidden" name="url_mjfilter" id="url_mjfilter" value="'.get_bloginfo('url').'/wp-content/plugins/wp-idea/includes/pages/views/ajax-filter-result.php"/>';

	if($categoryLabels == 1) {
		// Blok kategorie
		$outputCategories .= '<ul class="home-category-items product-list-categories">';

		$categories = get_terms( array(
			'taxonomy' => 'download_category',
			'hide_empty' => false
			) );

		foreach ($categories as $key => $category) {
			$outputCategories .= "<li>";
			$outputCategories .=  "<button name='id_category_tag' data-filter_type='category' onclick='getAjaxResults(this)' value='".$category->term_id."' data-term='".$category->term_id."' data-href='".get_term_link($category->term_id)."' type='button'>";
			$outputCategories .= $category->name;
			$outputCategories .= "</button>";
			$outputCategories .= "</li>";
		}

		$outputCategories .= '</ul>';
	
		$output .= $outputCategories;
		// End Blok kategorie
	}
	if($tagLabels == 1) {
		// Blok Poziomy
		$outputLevels .= '<ul class="home-levels-items levels">';

		$tags = get_terms( array(
			'taxonomy' => 'download_tag', 
			'hide_empty' => false, 
			) );

		foreach ($tags as $key => $tag) {
			
			$outputLevels .= "<li>";
			$outputLevels .= "<button  name='id_category_tag' data-filter_type='tag' data-href='".get_term_link($tag->term_id)."'  type='button'  value='".$tag->term_id."'  data-term='".$tag->term_id."' onclick='getAjaxResults(this)' >";
			$outputLevels .= $tag->name;
			$outputLevels .= "</button>";
			$outputLevels .= "</li>";
		}

		$outputLevxels .= '</ul>';
		$output .= $outputLevels;
		// End Blok Poziomy
	}
	

	$output .= '</form>';

		$output .= '<div class="products-list ajax-product-list">';
		$output .= '<div class="row">';
	if(!empty($categoryProduct)) {
			$args = array(
				'post_type'      => 'download',
				'post_status' => 'publish',
				'posts_per_page' => $quantityProduct,
				'meta_key' => 'sales_disabled',
				'meta_value' => 'off',	
				'tax_query'      => array(
					array(
						'taxonomy' => 'download_category',
						'field'    => 'slug',
						'terms'    => $categoryProduct,
					),
				),	
			);
		} else { 
			$args = array(
				'post_type'      => 'download',
				'post_status' => 'publish',
				'posts_per_page' => $quantityProduct,
				'meta_key' => 'sales_disabled',
				'meta_value' => 'off',		
			);
		}


	$all_product = get_posts( $args );

	 foreach($all_product as $product) { 
		$output .= "<div class='col-md-6 col-lg-3'>";
		$output .= "<div class='product'>";
		 //Miniatura
		 $output .= "<div class='product-thumbnail'>";

		// Sprawdz czy jest promocja lub bestseller
		$output .= getProductLabel($product->ID);

		$output .= "<a href='".get_permalink($product->ID)."'>";
			if(empty(get_the_post_thumbnail_url( $product->ID, 'thumbnail'))) {
				$output .= "<img src='data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAASwAAAEsCAYAAAB5fY51AAAAAXNSR0IArs4c6QAAFcNJREFUeF7tnYeSpcYSRFl577333uv//0Dee++999IqkhAKpqaA5t5hY3PqEDGh93aAqT5ZJN3VDZx46qmnTnZsEIAABAwInMCwDFQiRAhAoCeAYZEIEICADQEMy0YqAoUABDAscgACELAhgGHZSEWgEIAAhkUOQAACNgQwLBupCBQCEMCwyAEIQMCGAIZlIxWBQgACGBY5AAEI2BDAsGykIlAIQADDIgcgAAEbAhiWjVQECgEIYFjkAAQgYEMAw7KRikAhAAEMixyAAARsCGBYNlIRKAQggGGRAxCAgA0BDMtGKgKFAAQwLHIAAhCwIYBh2UhFoBCAAIZFDkAAAjYEMCwbqQgUAhDAsMgBCEDAhgCGZSMVgUIAAhgWOQABCNgQwLBspCJQCEAAwyIHIAABGwIYlo1UBAoBCGBY5AAEIGBDAMOykYpAIQABDIscgAAEbAhgWDZSESgEIIBhkQMQgIANAQzLRioChQAEMCxyAAIQsCGAYdlIRaAQgACGRQ5AAAI2BDAsG6kIFAIQwLDIAQhAwIYAhmUjFYFCAAIYFjkAAQjYEMCwbKQiUAhAAMMiByAAARsCGJaNVAQKAQhgWOQABCBgQwDDspGKQCEAAQyLHIAABGwIYFg2UhEoBCCAYZEDEICADQEMy0YqAoUABDAscgACELAhgGHZSEWgEIAAhkUOQAACNgQwLBupCBQCEMCwyAEIQMCGAIZlIxWBQgACGBY5AAEI2BDAsGykIlAIQADDIgcgAAEbAhiWjVQECgEIYFjkAAQgYEMAw7KRikAhAAEMixyAAARsCGBYNlIRKAQggGGRAxCAgA0BDMtGKgKFAAQwLHIAAhCwIYBh2UhFoBCAAIZFDkAAAjYEMCwbqQgUAhDAsMgBCEDAhgCGZSMVgUIAAhgWOQABCNgQwLBspCJQCEAAwyIHIAABGwIYlo1UBAoBCGBY5AAEIGBDAMOykYpAIQABDIscgAAEbAhgWDZSESgEIIBhkQMQgIANAQzLRioChQAEMCxyAAIQsCGAYdlIRaAQgACGRQ5AAAI2BDAsG6kIFAIQwLDIAQhAwIYAhmUjFYFCAAIYFjkAAQjYEMCwbKQiUAhAAMMiByAAARsCGJaNVAQKAQhgWOQABCBgQwDDspGKQCEAAQyLHIAABGwIYFg2UhEoBCCAYZEDEICADQEMy0YqAoUABDAscgACELAhgGHZSEWgEIAAhkUOQAACNgQwLBupCBQCEMCwyAEIQMCGAIZlIxWBQgACGBY5AAEI2BDAsGykIlAIQADDIgcgAAEbAhiWjVQECgEIYFjkAAQgYEMAw7KRikAhAAEMixyAAARsCGBYNlIRKAQggGGRAxCAgA0BDMtGKgKFAAQwLHIAAhCwIYBh2UhFoBCAAIZFDkAAAjYEMCwbqQgUAhDAsMgBCEDAhgCGZSMVgUIAAhgWOQABCNgQwLBspCJQCEAAwyIHIAABGwIYlo1UBAoBCGBY5AAEIGBDAMOykYpAIQABDIscgAAEbAhgWDZSESgEIIBhkQMQgIANAQzLRioChQAEMCxyAAIQsCGAYdlIRaAQgACGleTAOeec01122WXdueee25199tnd33//3f3666/9z88//9z9888/O2eOznfRRRd1F1xwQXfmmWd2f/31V3/eH3/8sf/fu24614UXXtj/6G8oxt9++60/7++//77raW2OU7svvvjiTtqdddZZ3Z9//tlz/eWXX/r/njx5cue2nHfeeT3X888/vzvjjDN6nmL7ww8/7HXerXJh54YaHIhhjUS64ooruptuuqm/4Oc2Jet7773Xm1frJoO6/fbbOyX/1KaL68MPP+x++umn1tP2F+dtt93WXXrppZPH6OL99NNPuy+//LL5vC47Sq+rrrqqN/+57fvvv+/ef//93shatyuvvLLPBzHONpmgbgjKhTXn3SoXWtvlvB+G9Z96d955Z9+rWrMpWd94443FQ2644Ybu+uuvX9xv2OGTTz7pDWZpU4/innvuWdrt/9+rR/DWW2/t1Sto/mMb7yiDuv/++/te8JpNXMV3abvvvvv6XlXLJuMSV/Fd2rbKhaW/e1x+j2F1XXfzzTd311xzzU6afvHFF32vaGrTeXX+tdvHH3/cffbZZ5OH6S6ti+rEiROrTq3e2+uvv77qmNNx5wcffHC2tzoX85tvvjlrLnfffXd3ySWXrG72q6++2g9BT3UurA7U+IDyhqW6xAMPPHBIwqFupWGfaksyCPXAMoNQL0u9rbjp7v/QQw8d+nfVl3Remce4nhV3fPnll/taSbY99thjh4ZButMPtRXVcnTubHj7wQcfWA8P1VtV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'/>";
			} else {
				$output .= "<img src='".get_the_post_thumbnail_url( $product->ID, 'thumbnail')."'/>";
			}
			$output .= "</a>";
			$output .= "</div>";

			// Tytuł produktu
			$output .= "<h3 class='product-title'>";
				$output .= "<a href='".get_permalink($product->ID)."'>";
					$output .= $product->post_title;
				$output .= "</a>";
			$output .= "</h3>";



			//  Czas trwania / liczba lekcji
			$output .= "<table class='product-feature'>";
			$output .= "<tr>";
				$output .= "<td>";
					$output .= "Czas trwania";
				$output .= "</td>";
				$output .= "<td>";
					$output .= get_field('czas_kursu', $product->ID);
				$output .= "min</td>";
			$output .= "</tr>";

				$output .= "<tr>";
				$output .= "<td>";
					$output .= "Liczba modułów";
				$output .= "</td>";
				$output .= "<td>";
					$output .= get_field('liczba_lekcji', $product->ID);
				$output .= "</td>";
			$output .= "</tr>";
		$output .= "</table>";

		// Cena produktu
		$sale_price_from_date = get_post_meta($product->ID,  'sale_price_from_date', true);
		$sale_price_to_date = get_post_meta($product->ID,  'sale_price_to_date', true);

		$output .= '<div class="price-container">';
		
		if((date('Y-m-d') >= $sale_price_from_date) && (date('Y-m-d') < $sale_price_to_date)) { 
	
		$output .= "<h4 class='product-price sale'>";
			$output .= number_format(get_post_meta($product->ID,  'sale_price', true),2,'.','');
		$output .= " PLN</h4>";

		$output .=	'<h4 class="crossed">';
		$output .= number_format(get_post_meta($product->ID,  'edd_price', true),2,'.',''); 
		$output .= " PLN</h4>";

} else { 
	$output .= "<h4 class='product-price'>";
	$output .= number_format(get_post_meta($product->ID,  'edd_price', true),2,'.','');
	$output .= " PLN</h4>";
	 
}

	$output .= '</div>';

	$output .= '<small class="omniprice">';
	#if ( get_field( 'cena_przed_obnizka' , $product->ID) ):
		// $output .= 'Najniższa cena z 30 dni: ';
		// $output .= get_field('cena_przed_obnizka', $product->ID); 
		$output .= shortcode_render_lowest_price_information($product->ID);
		// $output .= ' PLN';
	#endif;
	$output .= '</small>';
 
	// Dodaj do koszyka
	$output .= '<a href="'.get_permalink($product->ID).'" class="more-green">
			<i class="fa fa-shopping-bag"></i> 
			Sprawdź szkolenie</a>';
	$output .= "</div>";
	$output .= "</div>";
	}

	$output .= '</div>';
	$output .= '</div>';


	$output .= '
	<script type="text/javascript">
	function getAjaxResults(obj) { 
	
		document.getElementById("filter_type").value = obj.getAttribute("data-filter_type");
		document.getElementById("id_category_tag").value = obj.value;
		
		/*
		// Jesli kliknelismy na tag
		if(obj.getAttribute("data-filter_type") == "tag") {
			document.getElementById("id_tag").value = obj.value;
			$(".mjfilter button[data-filter_type=tag]").removeClass("active");
		}
		// Jesli kliknelismy na kategorie
		if(obj.getAttribute("data-filter_type") == "category") {
			document.getElementById("id_category").value = obj.value;
			$(".mjfilter button[data-filter_type=category]").removeClass("active");
		}
		*/
		$(".mjfilter button").removeClass("active");
		$(obj).addClass("active");

	   $.ajax({
		   method:"POST",
		   url:$("#url_mjfilter").val(),
		   data: $(".mjfilter").serialize(),
		   beforeSend: function() {
			   $(".ajax-product-list").css("opacity", "0.5");
			   $(".ajax-product-list").html("Ładowanie...");
		   },
		   success: function(data) {
			   $(".ajax-product-list").css("opacity", "1");
			   $(".ajax-product-list").html(data);
		   },
		   error: function(xhr) {
			   $(".ajax-product-list").css("opacity", "1");
			   $(".ajax-product-list").html("error"+data);
		   }
	   });
	}

	</script>
	';

	return $output;
}

function shortcode_render_lowest_price_information($product_id)
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

    return $lowest_price_information_html;
}