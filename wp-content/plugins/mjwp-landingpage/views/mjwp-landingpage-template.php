<?php
get_header();
?>
<div class="wrap">
 
    <p>Widok darmowego szkolenia</p>
    POST :<br/>
    <?php 
 
    print_r($post);
    ?>
     <br/>
    Zwrócony obiekt produktu :<br/>
    <?php 
    $product = get_query_var('product');

    $regular_price = get_query_var('regular_price');
    $sale_price = get_query_var('sale_price');

    print_r($product);


    print_r($regular_price);

    print_r($sale_price);
    ?>



<?php 
echo "id landing".MjwpLandingpage::$id_landing_page;
 $course = WPI()->courses->get_course_by_product( $product->ID );
 $course_page_id = get_post_meta( $course->ID, 'course_id', true );
 $restricted_to  = array( array( 'download' => $product->ID ) );
 $user_id = get_current_user_id();
$access         = bpmj_eddpc_user_can_access( $user_id, $restricted_to, $course_page_id );
if ( 'valid' === $access[ 'status' ] || 'waiting' === $access[ 'status' ] ) {
$show_open_padlock = true;
} else   { 
$show_open_padlock = false;
}

 
/**
 * Komunikaty flash
 */
$message_error = get_transient('mjwp_landingpage_flash_message_error');
$message_success = get_transient('mjwp_landingpage_flash_message_success');
if ($message_error) {
    echo '<div class="alert alert-danger"><p>' . esc_html($message_error) . '</p></div>';
    delete_transient('mjwp_landingpage_flash_message_error');
}
if ($message_success) {
    echo '<div class="alert alert-success"><p>' . esc_html($message_success) . '</p></div>';
    delete_transient('mjwp_landingpage_flash_message_success');
}

/**
 * User ma szkolenie
 */
    if($show_open_padlock) { 
        $course_url = get_permalink($course_page_id);
        
        $home_url = home_url('/');
                ?>
                <?php
                if(empty($message_success)) {
                    ?>
            <div class="alert alert-success"><p>
            Posiadasz już to szkolenie na swoim koncie
            </p></div>
            <?php
                }
            ?>

            <a href="<?php echo $course_url ?>" class="box_glowna_add_to_cart_link more-green" style=" background: #333;color: #fff;"><i
            class="fa fa-arrow-right"></i> Przejdź do kursu
            </a>
            
        <?php 
    } else {
        /**
         * User nie ma szkolenia, zwróć fornularz
         */
        $landingPageComponentForm = plugin_dir_path( __FILE__ ) . 'mjwp-landingpage-form.php';
        include $landingPageComponentForm;
    }
    ?>
    TEST: 
    <?php

    #$current_user = wp_get_current_user();

   #print_r(createEvent("Klienci", "Zarejestrowani", $current_user->user_email));

?>

</div>
<?php
get_footer();
?>