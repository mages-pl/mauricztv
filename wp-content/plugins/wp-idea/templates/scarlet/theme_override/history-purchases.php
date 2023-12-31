<?php
use bpmj\wpidea\Info_Message;
use bpmj\wpidea\helpers\Translator_Static_Helper;
?>

<div class="row">
<div class="col-sm-12 content_koszyk">
<?php if( ! empty( $_GET['edd-verify-success'] ) ) : ?>
<p class="edd-account-verified edd_success">
	<?php _e( 'Your account has been successfully verified!', 'easy-digital-downloads' ); ?>
</p>
<?php
endif;
/**
 * This template is used to display the purchase history of the current user.
 */
if ( is_user_logged_in() ):
	$purchases = edd_get_users_purchases( get_current_user_id(), 20, true, 'any' );
	if ( $purchases ) :
		do_action( 'edd_before_purchase_history' ); ?>
		<table id="edd_user_history">
			<thead>
				<tr class="edd_purchase_row">
					<?php do_action('edd_purchase_history_header_before'); ?>
					<th class="edd_purchase_id"><?php _e('ID','easy-digital-downloads' ); ?></th>
					<th class="edd_purchase_date"><?php _e('Date','easy-digital-downloads' ); ?></th>
					<th class="edd_purchase_amount"><?php _e('Amount','easy-digital-downloads' ); ?></th>
					<th class="edd_purchase_details"><?= Translator_Static_Helper::translate('user_account.orders.invoice'); ?></th>
					<th class="edd_purchase_details"><?php _e('Details','easy-digital-downloads' ); ?></th>
					<?php do_action('edd_purchase_history_header_after'); ?>
				</tr>
			</thead>
			<?php foreach ( $purchases as $post ) : setup_postdata( $post ); ?>
				<?php $purchase_data = edd_get_payment_meta( $post->ID ); ?>
				<tr class="edd_purchase_row">
					<?php do_action( 'edd_purchase_history_row_start', $post->ID, $purchase_data ); ?>
					<td class="edd_purchase_id">#<?php echo edd_get_payment_number( $post->ID ); ?></td>
					<td class="edd_purchase_date"><?php echo date_i18n( get_option('date_format'), strtotime( get_post_field( 'post_date', $post->ID ) ) ); ?></td>
					<td class="edd_purchase_amount">
						<span class="edd_purchase_amount"><?php echo edd_currency_filter( edd_format_amount( edd_get_payment_amount( $post->ID ) ) ); ?></span>
					</td>
                    <td class='edd_purchase_invoice'>
                        <?php
                            $has_invoice_data = !empty(get_post_meta($post->ID, 'wpi_remote_invoice_id', true));
                        ?>
                        <?php if($has_invoice_data): ?>
                            <a class="resend-order-invoice" data-order-id="<?= $post->ID ?>" data-loading-message="<?= __('Sending...',BPMJ_EDDCM_DOMAIN ); ?>">
                                <?=Translator_Static_Helper::translate('user_account.orders.send_on_email'); ?>
                            </a>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
					<td class="edd_purchase_details">
						<?php if( $post->post_status != 'publish' ) : ?>
						<span class="edd_purchase_status <?php echo $post->post_status; ?>"><?php echo edd_get_payment_status( $post, true ); ?></span>
						<a href="<?php echo esc_url( add_query_arg( 'payment_key', edd_get_payment_key( $post->ID ), edd_get_success_page_uri() ) ); ?>">&raquo;</a>
						<?php else: ?>
						<a href="<?php echo esc_url( add_query_arg( 'payment_key', edd_get_payment_key( $post->ID ), edd_get_success_page_uri() ) ); ?>"><?php _e( 'View Details and Downloads', 'easy-digital-downloads' ); ?></a>
						<?php endif; ?>
					</td>
					<?php do_action( 'edd_purchase_history_row_end', $post->ID, $purchase_data ); ?>
				</tr>
			<?php endforeach; ?>
		</table>
		<div id="edd_purchase_history_pagination" class="edd_pagination navigation">
			<?php
			$big = 999999;
			echo paginate_links( array(
				'base'    => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
				'format'  => '?paged=%#%',
				'current' => max( 1, get_query_var( 'paged' ) ),
				'total'   => ceil( edd_count_purchases_of_customer() / 20 ) // 20 items per page
			) );
			?>
		</div>
		<?php do_action( 'edd_after_purchase_history' ); ?>
		<?php wp_reset_postdata(); ?>
	<?php else : ?>
        <?php
        $message = new Info_Message( __( 'You have not made any purchases','easy-digital-downloads' ) );
        $message->render();
        ?>
	<?php endif;
else:
    echo '<p>' . Translator_Static_Helper::translate('user_account.history_transaction.login_page') . '</p>';
    echo edd_login_form();
endif;
?>
</div>
</div>