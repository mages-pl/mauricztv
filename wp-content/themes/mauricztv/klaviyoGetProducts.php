<?php

require_once("../../../wp-load.php");

header('content-type:json');
/**
 * Pobierz produkty z publigo
 */

 /**
  * wyjściowy wzór json
  */

//   [
//     {
//       "id":"KLAVIYO-TSHIRT1",
//       "title":"Classic Klaviyo T-Shirt 1",
//       "link":"https://klaviyogear.myshopify.com/collections/klaviyo-classics/products/short-sleeve-t-shirt-1",
//       "description":"Standard issue for all Klaviyos. This t-shirt has the Klaviyo logo on the front and mark diagram on the back.",
//       "price":10,
//       "image_link":"https://www.klaviyo.com/media/images/examples/products/klaviyo-tshirt-thumbnail.png",
//       "categories":["apparel","t-shirt","new-arrival","swag"],
//       "inventory_quantity":25,
//       "inventory_policy":1
//     },
//     {
//       "id":"KLAVIYO-TSHIRT2",
//       "title":"Classic Klaviyo T-Shirt 2",
//       "link":"https://klaviyogear.myshopify.com/collections/klaviyo-classics/products/short-sleeve-t-shirt-1",
//       "description":"Standard issue for all Klaviyos. This t-shirt has the Klaviyo logo on the front and mark diagram on the back.",
//       "price":10,
//       "image_link":"https://www.klaviyo.com/media/images/examples/products/klaviyo-tshirt-thumbnail.png",
//       "categories":["apparel","t-shirt","new-arrival","swag"],
//       "inventory_quantity":30,
//       "inventory_policy":1
//     },
//   ]


$argsAll = array(
  'post_type'      => 'download',
  'post_status'    => 'publish',
  'posts_per_page' => -1,
  'meta_query'     => array(
      'relation' => 'AND', // Dodajemy relację, aby obsługiwać wiele meta_query
      array(
          'key'     => 'sales_disabled',
          'value'   => 'off',
      ),
      array(
          'key'     => 'hide_from_lists',
          'value'   => 'off',
      ),
  ),
  'tax_query'      => array(
      // array(
      //     'taxonomy' => 'download_category',
      //     'field'    => 'term_id',
      //     'terms'    => [21],  // 22 -> pakiety
      //     'operator' => 'NOT IN'
      // ),
  ),
  'orderby'        => 'date',
  'order'          => 'DESC',
);


$getProducts = get_posts( $argsAll );

$response = [];


foreach($getProducts as $key => $product) { 


  $description = wp_strip_all_tags($product->post_content);

  /**
   * Categories
   */
  $categories_terms = get_the_terms($product->ID,  'download_category', true);
  
  $productCategoriesItem = [];
  foreach($categories_terms as $term) {
      if(!in_array($term->name, $productCategories)) {
          $productCategories[] = $term->name;
      }
      $productCategoriesItem[] = $term->name;
  }

  /**
   * Thumbnail
   */
  if(!empty(get_the_post_thumbnail_url($product->ID))) { 
      $thumbnail = get_the_post_thumbnail_url($product->ID);
  } else {
      $thumbnail =  get_template_directory_uri()."/img/logo.svg";
  }
  
  $response[$key]['id'] = $product->ID;
  $response[$key]['title'] = $product->post_title;
  $response[$key]['link'] = ($product->guid);
  $response[$key]['description'] = $description;
  $response[$key]['price'] = json_decode(getPricesFromCourses($product->ID, false), true)['price'];
  $response[$key]['image_link'] = $thumbnail;
  $response[$key]['categories'] = (array)$productCategories;

  $response[$key]['inventory_quantity'] = 100;
  $response[$key]['inventory_policy'] = 1;
  
}

echo json_encode($response);
exit();
