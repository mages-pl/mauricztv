<?php

/**
 * Event Added to Cart
 */

 $getCart = edd_get_cart_contents();

  
 $user_id    = get_current_user_id();
 $saved_cart = get_user_meta( $user_id, 'edd_saved_cart', true );
 $cart = EDD()->session->get( 'edd_cart' );

 $items = [];
   foreach($cart as $key => $item) {
      $items[] = $item['id'];
   }
 $cartContainer = [];
 $cartItemNames = [];
 
 /**
  * Items
  */
 foreach($getCart as $key => $cartItem) {  
 
     // Cart item price
     $item_sale_price_from_date = get_post_meta( $cartItem['id'],  'sale_price_from_date', true);
     $item_sale_price_to_date = get_post_meta( $cartItem['id'],  'sale_price_to_date', true);
 
     if(((date('Y-m-d') >= $item_sale_price_from_date) && (date('Y-m-d') < $item_sale_price_to_date)) && (!is_numeric(get_post_meta($cartItem['id'],  'sale_price', true)))) {
         $cart_product_price =  number_format(get_post_meta( $cartItem['id'],  'sale_price', true),2,'.','');
      } else { 
         $cart_product_price = get_post_meta( $cartItem['id'],  'edd_price', true);
      }
 
     // $cart_product_price = get_post_meta( $cartItem['id'],  'edd_price', true);
 
     // Cart item category 
 
     $cart_item_categories = [];
     $categories_terms_item = get_the_terms($cartItem['id'],  'download_category', true);
 
     foreach($categories_terms as $term) {
         $cart_item_categories[] = $term->name;
     }

     /**
      * Thumbnail
      */
      if(!empty(get_the_post_thumbnail_url($cartItem['id']))) { 
         $thumbnail = get_the_post_thumbnail_url($cartItem['id']);
      } else {
         $thumbnail =  get_template_directory_uri()."/img/logo.svg";
      }
 
     // Cart item name 
     $cartItemNames[] =  get_the_title($cartItem['id']);
 
 
     // Cart item price 
     $cartItemPrices[] = edd_get_cart_item_final_price($key);//$cart_product_price;
 
     $cartContainer[$key]['ProductID'] =  $cartItem['id'];
     $cartContainer[$key]['SKU'] =  $cartItem['id'];
     $cartContainer[$key]['ProductName'] =  get_the_title($cartItem['id']);
     $cartContainer[$key]['Quantity'] =  1;
     $cartContainer[$key]['ItemPrice'] =  edd_get_cart_item_final_price($key);//$cart_product_price;
     $cartContainer[$key]['RowTotal'] =  edd_get_cart_item_final_price($key);//$cart_product_price;
     $cartContainer[$key]['ProductURL'] =  get_the_permalink($cartItem['id']);
     $cartContainer[$key]['ProductCategories'] = $cart_item_categories;
     $cartContainer[$key]['ImageURL'] = $thumbnail;
     
 }

 /**
  * Started Checkout
  */
 $jsonOutputStartedCheckout= [];

$id_cart = $user_id.''.implode("",$items);// id_user + ids_products

 $jsonOutputStartedCheckout['$event_id'] = $id_cart; // event_id
 $jsonOutputStartedCheckout['$value'] = number_format(array_sum((array)$cartItemPrices),2,'.',''); // Suma
 $jsonOutputStartedCheckout['ItemNames'] = (array)$cartItemNames;
 $jsonOutputStartedCheckout['CheckoutURL'] = edd_get_checkout_uri();
 $jsonOutputStartedCheckout['Items'] = (array)$cartContainer;
 
 
 $jsonResponseStartedCheckout = json_encode($jsonOutputStartedCheckout);

// if(!empty($_GET['add-to-cart'])){
//    echo "dodano do koszyka";
// }

?>

<script type="text/javascript">

   var response = jQuery.parseJSON ( ' <?php echo $jsonResponseStartedCheckout; ?> ' );


   window.onload = function() {

      klaviyo.track("Started Checkout", {
      "$event_id":  response.$event_id,
      "$value": response.$value,
      "ItemNames": response.ItemNames,
      "CheckoutURL": response.CheckoutURL,
      "Categories": response.Categories,
      "Items": response.Items
      });

   }
 </script>

<div class="content_koszyk"><?= bpmj_eddcm_scarlet_edd_checkout_form() ?></div>