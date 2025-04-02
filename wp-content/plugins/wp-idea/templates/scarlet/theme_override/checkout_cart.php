<?php
/**
 *  This template is used to display the Checkout page when items are in the cart
 */
use bpmj\wpidea\sales\product\Invoice_Tax_Payer_Helper;
use bpmj\wpidea\helpers\Translator_Static_Helper;
use bpmj\wpidea\View_Hooks;
use bpmj\wpidea\modules\increasing_sales\api\Increasing_Sales_API_Static_Helper;

global $post;

$cart_items = edd_get_cart_contents();
$is_vat_payer = Invoice_Tax_Payer_Helper::is_enabled();

$crosselingDiscountID = get_option( 'mauricz_crosseling_discount');
//echo "ID:".$crosselingDiscountID;

$discount_code = edd_get_discount_code((int)$crosselingDiscountID);

//echo "CODE:".$discount_code;
// Pobierz rabaty z koszyka klienta
$discounts = (array)edd_get_cart_discounts();

$discounts_ids = [];
foreach($discounts as $code) {
	$discount_id = edd_get_discount_id_by_code( $code);

	$discounts_ids[] = $discount_id;
}
// echo "RABAT:".$discount_id."KONFIG:".get_option( 'mauricz_crosseling_discount');
// print_r($discounts);
/**
 * Jeśli mamy w koszyku rabat zgodny z ustawieniami crosselingu lub jesli nie mamy w ogole w koszyku rabatu
 */
// echo count($discounts);
// print_r($discounts);
if(($discount_id == get_option( 'mauricz_crosseling_discount')) || ($discounts[0] == ''))  {
	// echo "AAA";
// Jesli mamy skonfigurowana kategorie i dyskont
if(!empty(get_option( 'mauricz_crosseling_category')) && !empty(get_option( 'mauricz_crosseling_discount')))  {

//print_r(edd_get_discount_code(get_option( 'mauricz_crosseling_discount')));

// $code = edd_get_discount_code(get_option( 'mauricz_crosseling_discount'));

// echo "code:".$code;

// print_r(edd_get_discount_type( get_option( 'mauricz_crosseling_discount') ));
// echo "<br/>--<br/>";
// print_r(edd_get_discount_product_reqs( get_option( 'mauricz_crosseling_discount') ));
// echo "<br/>--<br/>";
// print_r(edd_get_discount_excluded_products( $code ));
// echo "<br/>--<br/>";


// print_r(edd_get_discount_product_condition(get_option( 'mauricz_crosseling_discount')));
// echo "<br/>--<br/>";

// Ilosc produktow w koszyku > 1
if(count($cart_items) > 1) { 
	// Jesli rabat jest aktywny i nie jest uzyty

	if(edd_is_discount_active( $crosselingDiscountID ) && !in_array($crosselingDiscountID, $discounts_ids)) {
		
		//wp_redirect( edd_get_checkout_uri( '?action=edd_apply_discount&discount_code=ABC' ) );
	//echo "DAJ RABAT";

	?>
	<script type="text/javascript">
	//alert('OK');
		
	var form = document.getElementById("edd_checkout_cart_form");

var formData = "action=edd_apply_discount&code=<?=$discount_code?>";
var xhr = new XMLHttpRequest();

// 	action: edd_apply_discount
// code: ABC
// form: payment-mode=tpay_gateway&edd_action=gateway_select&page_id=48&edd_email=wordpress%40virtualpeople.pl&edd_first=&edd_last=&bpmj_edd_invoice_data_invoice_type=person&bpmj_edd_invoice_data_invoice_person_name=&bpmj_edd_invoice_data_invoice_nip=&bpmj_edd_invoice_data_invoice_company_name=&bpmj_edd_invoice_data_invoice_street=&bpmj_edd_invoice_data_invoice_building_number=&bpmj_edd_invoice_data_invoice_apartment_number=&bpmj_edd_invoice_data_invoice_postcode=&bpmj_edd_invoice_data_invoice_city=&edd-user-id=3&edd_action=purchase&edd-gateway=tpay_gateway&edd-process-checkout-nonce=9f9777a590
xhr.open("POST", "<?= get_bloginfo('url'); ?>/wp-admin/admin-ajax.php", true);  

xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest"); 
xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

xhr.onreadystatechange = function () {

if (xhr.readyState === 4 && xhr.status === 200) {
	// Obsłuż odpowiedź serwera tutaj
	console.log(xhr.responseText);
	
	$(".edd_cart_item .cart_price,  .koszyk_right .edd_cart_amount").addClass("placeholder-loading-root placeholder-loading-text placeholder-loading-pulse");
	
	var response = JSON.parse(xhr.responseText);

	if(response.msg == 'valid') {
		//document.querySelector('.podsumowanie_lacznie').innerHTML = response.total;

		document.querySelector('.koszyk_right .price.edd_cart_amount.cart_total').innerHTML = response.total;
		
		document.querySelector('.edd_cart_footer_row.edd_cart_discount_row').style.display = 'block';
		//document.querySelector('.edd_cart_footer_row.edd_cart_discount_row').innerHTML = response.html;

		document.querySelector(".koszyk_cena_bez_rabatu").style.display='';
		
	}
	console.log(JSON.parse(xhr.responseText));
	window.location.reload();
	 //window.location.href = window.location.href;
}
};

xhr.onerror = function () {
// Obsłuż błąd tutaj
console.error("Wystąpił błąd podczas wysyłania żądania.");
};

xhr.send(formData);

	</script>
	<?php
}
} else {
	// Jesli w tablicy mamy rabat crosselingowy
	if(in_array($crosselingDiscountID, $discounts_ids)) {
		// Jesli mamy mniej niz 2 produkty
		if(count($cart_items) <= 1) {
			//echo "Nie dawaj rabatu";
			// usun automatyczny rabat			
			wp_redirect( edd_get_checkout_uri( '?edd_action=remove_cart_discount&discount_id='.$crosselingDiscountID.'&discount_code='.$discount_code ) );
		}
	}

}
}
}
?>
<table
        id="edd_checkout_cart" class="podsumowanie_koszyk<?php if ( ! edd_is_ajax_disabled() ) { echo ' ajaxed'; } ?>" >
	<thead>
		<tr class="edd_cart_header_row">
            <th><?= Translator_Static_Helper::translate('templates.checkout_cart.products_in_cart') ?></th>
		</tr>
	</thead>
	<tbody>
		<?php do_action( 'edd_cart_items_before' ); ?>
		<?php if ( $cart_items ) : ?>
			<?php foreach ( $cart_items as $key => $item ) :
                    $item_price = edd_cart_item_price( $item['id'], $item['options'] );
                ?>
				<tr class="edd_cart_item" id="edd_cart_item_<?php echo esc_attr( $key ) . '_' . esc_attr( $item['id'] ); ?>" data-download-id="<?php echo esc_attr( $item['id'] ); ?>">
					<?php do_action( 'edd_checkout_table_body_first', $item ); ?>
					<td>
                        <div class="checkout_cart_img">
                           <?php
							if ( current_theme_supports( 'post-thumbnails' ) ) {
								$thumb = get_the_post_thumbnail( $item['id'], apply_filters( 'edd_checkout_image_size', array( 80, 80 ) ) );
								if( empty( $thumb ) ) {
									$thumb = '<img width="80" height="80" src="' . bpmj_eddcm_template_get_file( 'assets/img/box1-200x197.jpg' ) . '" class="attachment-80x80 size-80x80 wp-post-image" alt="">';
								}
								echo $thumb;
							}
                            ?>
                        </div>
                        <div class="checkout_cart_title">
                            <?php
                            $item_title = edd_get_cart_item_name( $item );
                            echo esc_html( $item_title );
                            do_action( 'edd_checkout_cart_item_title_after', $item );
                            ?>
                        </div>
                        <div class="checkout_cart_price_button">
                            <div class="cart_price">
								
                                <?php
                                echo $item_price;
							
                                do_action( 'edd_checkout_cart_item_price_after', $item );
                                ?>
                            </div>
                            <div class="cart_button_remove">
                                <?php if( edd_item_quantities_enabled() ) : ?>
                                    <input type="number" min="1" step="1" name="edd-cart-download-<?php echo $key; ?>-quantity" data-key="<?php echo $key; ?>" class="edd-input edd-item-quantity" value="<?php echo edd_get_cart_item_quantity( $item['id'], $item['options'] ); ?>"/>
                                    <input type="hidden" name="edd-cart-downloads[]" value="<?php echo $item['id']; ?>"/>
                                    <input type="hidden" name="edd-cart-download-<?php echo $key; ?>-options" value="<?php echo esc_attr( json_encode( $item['options'] ) ); ?>"/>
                                <?php endif; ?>
                                <?php do_action( 'edd_cart_actions', $item, $key ); ?>
                                <a class="remove_from_cart" <?php View_Hooks::run(View_Hooks::RENDER_INLINE_ELEMENTS_IN_REMOVE_FROM_CART_HYPERLINK, $item['id']); ?> href="<?php echo esc_url( edd_remove_item_url( $key ) ); ?>"><i class="icon icon-remove"></i> 
								<!-- &nbsp; <?= Translator_Static_Helper::translate('templates.checkout_cart.remove_from_cart') ?> -->
							</a>

                            </div>
                        </div>
                    </td>
					<?php do_action( 'edd_checkout_table_body_last', $item ); ?>
				</tr>
			<?php endforeach; ?>
		<?php endif; ?>
		<?php do_action( 'edd_cart_items_middle' ); ?>

        <?= Increasing_Sales_API_Static_Helper::render_offers() ?>

		<!-- Show any cart fees, both positive and negative fees -->
		<?php if( edd_cart_has_fees() ) : ?>
			<?php foreach( edd_get_cart_fees() as $fee_id => $fee ) : ?>
				<tr class="edd_cart_fee" id="edd_cart_fee_<?php echo $fee_id; ?>">

					<?php do_action( 'edd_cart_fee_rows_before', $fee_id, $fee ); ?>

                    <td>
                        <div class="checkout_cart_title">
                            <?php echo esc_html($fee['label']); ?>
                        </div>
                        <div class="checkout_fee_amount">
                            <?php echo esc_html(edd_currency_filter(edd_format_amount($fee['amount']))); ?>
                        </div>
                    </td>
                    <td>
						<?php if( ! empty( $fee['type'] ) && 'item' == $fee['type'] ) : ?>
							<a href="<?php echo esc_url( edd_remove_cart_fee_url( $fee_id ) ); ?>"><?php _e( 'Remove', 'easy-digital-downloads' ); ?></a>
						<?php endif; ?>

					</td>

					<?php do_action( 'edd_cart_fee_rows_after', $fee_id, $fee ); ?>

				</tr>
			<?php endforeach; ?>
		<?php endif; ?>

		<?php do_action( 'edd_cart_items_after' ); ?>
	</tbody>
	<tfoot>

		<?php if( has_action( 'edd_cart_footer_buttons' ) ) : ?>
			<tr class="edd_cart_footer_row<?php if ( edd_is_cart_saving_disabled() ) { echo ' edd-no-js'; } ?>">
				<th>
					<?php do_action( 'edd_cart_footer_buttons' ); ?>
				</th>
			</tr>
		<?php endif; ?>

		<?php if( edd_use_taxes() && ! edd_prices_include_tax() ) : ?>
			<tr class="edd_cart_footer_row edd_cart_subtotal_row"<?php if ( ! edd_is_cart_taxed() ) echo ' style="display:none;"'; ?>>
				<?php do_action( 'edd_checkout_table_subtotal_first' ); ?>
				<th class="edd_cart_subtotal">
					<?php _e( 'Subtotal', 'easy-digital-downloads' ); ?>:&nbsp;<span class="edd_cart_subtotal_amount"><?php echo edd_cart_subtotal(); ?></span>
				</th>
				<?php do_action( 'edd_checkout_table_subtotal_last' ); ?>
			</tr>
		<?php endif; ?>

		<tr class="edd_cart_footer_row edd_cart_discount_row" <?php if( ! edd_cart_has_discounts() )  echo ' style="display:none;"'; ?>>
			<?php do_action( 'edd_checkout_table_discount_first' ); ?>
			<th class="edd_cart_discount">
				<?php edd_cart_discounts_html(); ?>
			</th>
			<?php do_action( 'edd_checkout_table_discount_last' ); ?>
		</tr>

		<?php if( edd_use_taxes() ) : ?>
			<tr class="edd_cart_footer_row edd_cart_tax_row"<?php if( ! edd_is_cart_taxed() ) echo ' style="display:none;"'; ?>>
				<?php do_action( 'edd_checkout_table_tax_first' ); ?>
				<th class="edd_cart_tax">
					<?php _e( 'Tax', 'easy-digital-downloads' ); ?>:&nbsp;<span class="edd_cart_tax_amount" data-tax="<?php echo edd_get_cart_tax( false ); ?>"><?php echo esc_html( edd_cart_tax() ); ?></span>
				</th>
				<?php do_action( 'edd_checkout_table_tax_last' ); ?>
			</tr>

		<?php endif; ?>

        <?php if( edd_has_active_discounts() ) : ?>
		<tr class="edd_cart_footer_row">
			<th class="edd_cart_total">
				<div class="podsumowanie_contenter">
                    <?php bpmj_eddcm_scarlet_edd_discount_field() ?>
                </div>
			</th>
		</tr>
        <?php endif; ?>
        <tr class="edd_cart_footer_row">
            <?php do_action( 'edd_checkout_table_footer_first' ); ?>
            <th class="edd_cart_total">
                <div class="podsumowanie_contenter">
                    <div class="cena_netto_brutto">
                        <?php if ( $is_vat_payer ) : ?>
                            <div class="podsumowanie_netto_vat">
                                <?php
                                do_action( 'get_cart_net_vat_total_price');
                                ?>
                            </div><br>
                        <?php endif; ?>
                        <div class="podsumowanie">
                            <div class="title"><?php _e( 'Total', 'easy-digital-downloads' ); ?>:</div>
                            <div class="price edd_cart_amount" data-subtotal="<?php echo edd_get_cart_total(); ?>" data-total="<?php echo edd_get_cart_total(); ?>"><?php edd_cart_total(); ?></div>
                        </div>
                    </div>
                </div>
            </th>
            <?php do_action( 'edd_checkout_table_footer_last' ); ?>
        </tr>
	</tfoot>
</table>

<div class="product-inner-block text-center cart">

<?php
	
		$ignoreProducts = [];
		// Pobierz elementy z koszyka i wrzuc je do tablicy ignorowanych
		foreach ($cart_items as $key => $item) {
			$ignoreProducts[] = $item['id'];
		}
		
    $args = array(
		'post_type'      => 'download',
		'post_status' => 'publish',
		'posts_per_page' => 2,
		'meta_key' => 'sales_disabled',
		'meta_value' => 'off',
		'post__not_in' => (array)$ignoreProducts,
		'numberposts' => 2,
		'tax_query'      => array(
			array(
				'taxonomy' => 'download_category',
				'field'    => 'term_id',
				'terms'    => (int)get_option( 'mauricz_crosseling_category'),
			),
		),
		'orderby'          => 'date',
		'order'            => 'DESC',	
	);

    $getProducts = get_posts($args);

	if(count($getProducts) > 0) {
	?>
<h1 class="title-section h2">Dobierz kolejne szkolenie i odbierz
</h1>
<h1 class="title-section green-text h2">rabat w wysokości <?php
if(!empty((int)get_option('mauricz_crosseling_discount'))) {
echo edd_get_discount_amount((int)get_option('mauricz_crosseling_discount'));
} else { 
	echo '30';
}
?>%!</h1>
<p style="color: red; font-weight: bold;">(Kody rabatowe nie łączą się z tym rabatem oraz nie działają na pakiety szkoleń)</p>
<?php 
	}
?>
</div>

<div class="mjcart-container">
	<?php
    // Zwroc liste produktów w koszyku
    foreach($getProducts as $product) { 
        // Jesli produkt zawiera ID inne niz te ktore jest w koszyku dodaj do listy

		?>

<div class="crosseling-item">
<div class="checkout_cart_img">
<?php

echo "<a href='".get_permalink($product->ID)."'>";
			if(empty(get_the_post_thumbnail_url( $product->ID, 'thumbnail'))) {
				echo "<img src='data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAASwAAAEsCAYAAAB5fY51AAAAAXNSR0IArs4c6QAAFcNJREFUeF7tnYeSpcYSRFl577333uv//0Dee++999IqkhAKpqaA5t5hY3PqEDGh93aAqT5ZJN3VDZx46qmnTnZsEIAABAwInMCwDFQiRAhAoCeAYZEIEICADQEMy0YqAoUABDAscgACELAhgGHZSEWgEIAAhkUOQAACNgQwLBupCBQCEMCwyAEIQMCGAIZlIxWBQgACGBY5AAEI2BDAsGykIlAIQADDIgcgAAEbAhiWjVQECgEIYFjkAAQgYEMAw7KRikAhAAEMixyAAARsCGBYNlIRKAQggGGRAxCAgA0BDMtGKgKFAAQwLHIAAhCwIYBh2UhFoBCAAIZFDkAAAjYEMCwbqQgUAhDAsMgBCEDAhgCGZSMVgUIAAhgWOQABCNgQwLBspCJQCEAAwyIHIAABGwIYlo1UBAoBCGBY5AAEIGBDAMOykYpAIQABDIscgAAEbAhgWDZSESgEIIBhkQMQgIANAQzLRioChQAEMCxyAAIQsCGAYdlIRaAQgACGRQ5AAAI2BDAsG6kIFAIQwLDIAQhAwIYAhmUjFYFCAAIYFjkAAQjYEMCwbKQiUAhAAMMiByAAARsCGJaNVAQKAQhgWOQABCBgQwDDspGKQCEAAQyLHIAABGwIYFg2UhEoBCCAYZEDEICADQEMy0YqAoUABDAscgACELAhgGHZSEWgEIAAhkUOQAACNgQwLBupCBQCEMCwyAEIQMCGAIZlIxWBQgACGBY5AAEI2BDAsGykIlAIQADDIgcgAAEbAhiWjVQECgEIYFjkAAQgYEMAw7KRikAhAAEMixyAAARsCGBYNlIRKAQggGGRAxCAgA0BDMtGKgKFAAQwLHIAAhCwIYBh2UhFoBCAAIZFDkAAAjYEMCwbqQgUAhDAsMgBCEDAhgCGZSMVgUIAAhgWOQABCNgQwLBspCJQCEAAwyIHIAABGwIYlo1UBAoBCGBY5AAEIGBDAMOykYpAIQABDIscgAAEbAhgWDZSESgEIIBhkQMQgIANAQzLRioChQAEMCxyAAIQsCGAYdlIRaAQgACGRQ5AAAI2BDAsG6kIFAIQwLDIAQhAwIYAhmUjFYFCAAIYFjkAAQjYEMCwbKQiUAhAAMMiByAAARsCGJaNVAQKAQhgWOQABCBgQwDDspGKQCEAAQyLHIAABGwIYFg2UhEoBCCAYZEDEICADQEMy0YqAoUABDAscgACELAhgGHZSEWgEIAAhkUOQAACNgQwLBupCBQCEMCwyAEIQMCGAIZlIxWBQgACGBY5AAEI2BDAsGykIlAIQADDIgcgAAEbAhiWjVQECgEIYFjkAAQgYEMAw7KRikAhAAEMixyAAARsCGBYNlIRKAQggGGRAxCAgA0BDMtGKgKFAAQwLHIAAhCwIYBh2UhFoBCAAIZFDkAAAjYEMCwbqQgUAhDAsMgBCEDAhgCGZSMVgUIAAhgWOQABCNgQwLBspCJQCEAAwyIHIAABGwIYlo1UBAoBCGBY5AAEIGBDAMOykYpAIQABDIscgAAEbAhgWDZSESgEIIBhkQMQgIANAQzLRioChQAEMCxyAAIQsCGAYdlIRaAQgACGleTAOeec01122WXdueee25199tnd33//3f3666/9z88//9z9888/O2eOznfRRRd1F1xwQXfmmWd2f/31V3/eH3/8sf/fu24614UXXtj/6G8oxt9++60/7++//77raW2OU7svvvjiTtqdddZZ3Z9//tlz/eWXX/r/njx5cue2nHfeeT3X888/vzvjjDN6nmL7ww8/7HXerXJh54YaHIhhjUS64ooruptuuqm/4Oc2Jet7773Xm1frJoO6/fbbOyX/1KaL68MPP+x++umn1tP2F+dtt93WXXrppZPH6OL99NNPuy+//LL5vC47Sq+rrrqqN/+57fvvv+/ef//93shatyuvvLLPBzHONpmgbgjKhTXn3SoXWtvlvB+G9Z96d955Z9+rWrMpWd94443FQ2644Ybu+uuvX9xv2OGTTz7pDWZpU4/innvuWdrt/9+rR/DWW2/t1Sto/mMb7yiDuv/++/te8JpNXMV3abvvvvv6XlXLJuMSV/Fd2rbKhaW/e1x+j2F1XXfzzTd311xzzU6afvHFF32vaGrTeXX+tdvHH3/cffbZZ5OH6S6ti+rEiROrTq3e2+uvv77qmNNx5wcffHC2tzoX85tvvjlrLnfffXd3ySWXrG72q6++2g9BT3UurA7U+IDyhqW6xAMPPHBIwqFupWGfaksyCPXAMoNQL0u9rbjp7v/QQw8d+nfVl3Remce4nhV3fPnll/taSbY99thjh4ZButMPtRXVcnTubHj7wQcfWA8P1VtV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'/>";
			} else {
				echo "<img src='".get_the_post_thumbnail_url( $product->ID, 'thumbnail')."'/>";
			}
			echo "</a>";
			?>
	<!-- <img width="80" height="80" src="https://vp.mauricz.tv/wp-content/uploads/2023/07/free-green-man-150x150.jpg" class="attachment-80x80 size-80x80 wp-post-image" alt="" decoding="async" loading="lazy" srcset="https://vp.mauricz.tv/wp-content/uploads/2023/07/free-green-man-150x150.jpg 150w, https://vp.mauricz.tv/wp-content/uploads/2023/07/free-green-man-300x300.jpg 300w" sizes="(max-width: 80px) 100vw, 80px"> -->
</div>
<div class="checkout_cart_title">
		<?= $product->post_title;?>
</div>

<div class="checkout_cart_price_button">
	<div class="cart_price">
		<p class="podsumowanie_koszyk_price">
			<?php
			$sale_price_from_date = get_post_meta($product->ID,  'sale_price_from_date', true);
			$sale_price_to_date = get_post_meta($product->ID,  'sale_price_to_date', true);

			$salePrice = get_post_meta($product->ID,  'sale_price', true);
			$regularPrice = get_post_meta($product->ID,  'edd_price', true);

			if((date('Y-m-d') >= $sale_price_from_date) && (date('Y-m-d') < $sale_price_to_date)) { 
				echo number_format($salePrice,2,'.',''); 
				echo " PLN";
			} else {
				if(($salePrice>0) && ($salePrice != $regularPrice) &&  (empty($sale_price_to_date))) { 
					echo number_format($salePrice,2,'.',''); 
					echo " PLN";
					echo "<br/>";
					echo "<del>";
					echo number_format($regularPrice,2,'.',''); 
					echo " PLN";
					echo "</del>";
				} else {
					 echo number_format($regularPrice,2,'.',''); 
					 echo " PLN";
				}
			}
			 ?>  

		</p>            
	</div>                            
</div>

<a href="<?php echo esc_attr( edd_get_checkout_uri( array(
               'add-to-cart' => (int)$product->ID,
           ) ) ); ?>" class="btn btn-primary more">
			<i class="fa fa-shopping-bag"></i> 
			Dodaj do koszyka</a>
    </div>

		<?php
    }
	?>
</div>



