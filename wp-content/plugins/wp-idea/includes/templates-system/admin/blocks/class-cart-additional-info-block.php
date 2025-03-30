<?php
namespace bpmj\wpidea\templates_system\admin\blocks;

class Cart_Additional_Info_Block extends Block
{
    const BLOCK_NAME = 'wpi/additional-info';

    public function __construct() {
        parent::__construct();
        
        $this->title = __('Additional Info', BPMJ_EDDCM_DOMAIN);
    }
    
    public function get_content_to_render($atts)
    {
        //@todo: wyciągnąć kod do widoków a pobieranie danych do api
        global $wpidea_settings;
    
        $scarlet_cart_additional_info_1_title = isset( $wpidea_settings[ 'scarlet_cart_additional_info_1_title' ] ) ? $wpidea_settings[ 'scarlet_cart_additional_info_1_title' ] : '';
        $scarlet_cart_additional_info_1_desc = isset( $wpidea_settings[ 'scarlet_cart_additional_info_1_desc' ] ) ? $wpidea_settings[ 'scarlet_cart_additional_info_1_desc' ] : '';
        $scarlet_cart_additional_info_2_title = isset( $wpidea_settings[ 'scarlet_cart_additional_info_2_title' ] ) ? $wpidea_settings[ 'scarlet_cart_additional_info_2_title' ] : '';
        $scarlet_cart_additional_info_2_desc = isset( $wpidea_settings[ 'scarlet_cart_additional_info_2_desc' ] ) ? $wpidea_settings[ 'scarlet_cart_additional_info_2_desc' ] : '';
        
        $scarlet_cart_secure_payments_cb     = !empty( $wpidea_settings[ 'scarlet_cart_secure_payments_cb' ] ) && 'on' === $wpidea_settings[ 'scarlet_cart_secure_payments_cb' ];
    
        $gateways = edd_get_enabled_payment_gateways( true );
        $payments = '';
        foreach ( $gateways as $gateway_id => $gateway ) {
            $slug = explode('_', $gateway_id);
            $slug = $slug[0];
            $file = bpmj_eddcm_template_get_file( 'assets/img/' . $slug . '.png' );
            if ( file_exists( BPMJ_EDDCM_DIR . 'templates/scarlet/assets/img/' . $slug . '.png' ) ) {
                $payments .= '<img src="' . $file . '" />';
            }
        }

        $content = '<div class="koszyk_right">';

        $content .= '<h3>Podsumowanie</h3>';

        $content .= '<table>';

        if(edd_cart_has_discounts()) {
        $content .= '<tr class="koszyk_cena_bez_rabatu">';
    } else {
        $content .= '<tr class="koszyk_cena_bez_rabatu" style="display:none;">';
    }
        $content .= '<td>';
            $content .= 'Kwota przed rabatem ';
        $content .= '</td>';

        $content .= '<td style="text-align:right;
        color: #a9a9a9;">';
            $content .= '<span class="price edd_cart_amount">';

            $totalWithoutDiscount = edd_get_cart_total( true ) + edd_get_cart_discounted_amount();
                $content .= apply_filters( 'edd_cart_total', edd_currency_filter( edd_format_amount( $totalWithoutDiscount ) ) );                    
                //$content .= edd_get_cart_discounted_amount();
            $content .= '</span>';
        $content .= '</td>';
    $content .= '</tr>';

       
            $content .= '<tr>';
                $content .= '<td>';
                    $content .= 'Kwota';
                $content .= '</td>';

                $content .= '<td style="text-align:right;">';
                    $content .= '<span class="price edd_cart_amount  cart_total" data-subtotal="'.edd_get_cart_total().'" data-total="'.edd_get_cart_total().'"><?>';
                        $content .= edd_cart_total(false);                    
                    $content .= '</span>';
                $content .= '</td>';
            $content .= '</tr>';

            $content .= '<tr>';
                $content .= '<td colspan="2">';
                    $content .= 'Płatność';
                $content .= '</td>';
            $content .= '</tr>';
        $content .= '</table>';

        if(!is_user_logged_in()) { 
            $content .= '<div class="alert alert-danger">
            Aby zakupić szkolenie <a href="'.get_bloginfo('url').'/wp-login.php">zaloguj się lub utwórz konto</a>
        </div>';
        } else {
            $content .= edd_checkout_form_without_cart();
        }
        $content .= '<img src="'.get_bloginfo('url').'/wp-content/themes/mauricztv/img/cart_payments.png" style="display:block;margin:auto;"/>';
    
        #$content .= bpmj_eddcm_scarlet_edd_discount_field();
        // if( !empty( $scarlet_cart_additional_info_1_title ) ) {
        //     $content .= '<div class="tytul_ikona">
        //         <img src="' . bpmj_eddcm_template_get_file( 'assets/img/gwiazda check.png' ) . '"> ' . 
        //         $scarlet_cart_additional_info_1_title . '
        //     </div>
        //     <div class="zwykly_tekst">' .
        //         $scarlet_cart_additional_info_1_desc . '
        //     </div>';
        // }
        
        // if( !empty( $scarlet_cart_additional_info_2_title ) ) {
        //     $content .= '<div class="tytul_ikona">
        //         <img src="' . bpmj_eddcm_template_get_file( 'assets/img/tarcza.png' ) . '"> ' . 
        //         $scarlet_cart_additional_info_2_title . '
        //     </div>
        //     <div class="zwykly_tekst">' .
        //         $scarlet_cart_additional_info_2_desc . '
        //     </div>';
        // }
        
        // if( $scarlet_cart_secure_payments_cb ) {
        //     $content .= '<div class="tytul_ikona">
        //         <img src="' . bpmj_eddcm_template_get_file( 'assets/img/klodka2.png' ) . '"> ' .
        //         __( 'Secure payments', BPMJ_EDDCM_DOMAIN ) . '
        //     </div>
        //     <div class="platnosci">' . $payments . '</div>';
        // }
            
        $content .= '</div>';
        
        $content .= '<img src="'.get_bloginfo('url').'/wp-content/themes/mauricztv/img/cart_benefits.png" style="display:table;margin:15px auto;"/>';

        return $content;
    }
}