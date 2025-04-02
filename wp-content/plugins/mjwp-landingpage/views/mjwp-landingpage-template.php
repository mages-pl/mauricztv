<div class="mjwp-landingpage">
<?php


    get_header();
    $acf_fields = get_fields('options');
    $product = get_query_var('product');

    $regular_price = get_query_var('regular_price');
    $sale_price = get_query_var('sale_price');

    $product_fields = get_fields($product->ID);
    
// print_r($acf_fields);

// // Add custom class to body
// add_filter('body_class', function($classes) {
//     $classes[] = 'mjwp-landing-page'; // Your custom class name
//     return $classes;
// });
?>

<?php $hero = $acf_fields['hero']; if($hero) : ?>

    <section class="hero col-12">
        <div class="hero-container">
            <div class="hero-container__wrapper">
                <div class="subtitle">
                    <h3 class="text-34 text-center"><?php echo $hero['podtytul']; ?></h3>
                </div>
                <div class="title">
                    <?php echo $hero['tytul']; ?>
                </div>
                
            </div>
            <div class="hero-container__video">
                <?php if($product_fields['filmik']) : ?>
                    <?= $product_fields['filmik'] ?>
                <?php else : ?>
                    <img src="<?php echo $product_fields['grafika_zamiast_filmu']; ?>" />
                <?php endif; ?>
            </div>
            <div class="hero-container__button">
                <a href="<?= $hero['przycisk']['url'] ?>" class="more"><?= $hero['przycisk']['title'] ?></a>
            </div>
        </div>
    </section>

<?php endif; ?>

<?php $opinions = $acf_fields['opinie']; if($opinions) : ?>

    <section class="opinions col-12">
        <div class="opinions-container">
            <div class="opinions-container__wrapper">
                <?= do_shortcode($opinions['opinie']) ?>
            </div>
        </div>
    </section>

<?php endif; ?>

<?php $learn = $acf_fields['czego_sie_dowiesz']; if($learn) : ?>

    <section class="learn col-12">
        <div class="learn-container">
            <div class="learn-container__title">
                <?= $learn['tytul'] ?>
            </div>
            <div class="learn-container__wrapper">
                <?php foreach($learn['boxy'] as $item) : ?>
                    <div class="box">
                        <div class="box-container">
                            <div class="box-container__icon">
                                <img src="<?= $item['ikona']['url'] ?>" />
                            </div>
                            <div class="box-container__content">
                                <?= $item['tresc'] ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="learn-container__button">
                <a href="<?= $learn['przycisk']['url'] ?>" class="more"><?= $learn['przycisk']['title'] ?></a>
            </div>
        </div>
    </section>

<?php endif; ?>

<?php $foryou = $acf_fields['dla_ciebie']; if($foryou) : ?>

    <section class="foryou col-12">
        <div class="foryou-container">
            <div class="foryou-container__title">
                <?= $foryou['tresc'] ?>
            </div>
            <div class="foryou-container__wrapper">
                <?= $foryou['lista'] ?>
            </div>
        </div>
    </section>

<?php endif; ?>

<?php $courseFields = $acf_fields['o_kursie']; if($courseFields) :  ?>

    <section class="course col-12">
        <div class="course-container">
            <div class="course-container__title">
                <h3 class="text-34 text-center text-uppercase"><?= $product->post_title ?></h3>
                <h2 class="text-62 text-center">Już ponad <span><?= $product_fields['ilosc_kursantow'] ?> kursantów </span>wykorzystało wiedzę z tego szkolenia</h2>
            </div>
            <div class="course-container__wrapper">
                <div class="form" id="uzyskaj-szkolenie">
                    <div class="form-container">
                        <div class="form-container__label">
                            <div class="wrapper">
                                <div class="price text-34 text-center text-bold"><?= $regular_price ?></div>
                                <div class="free color-accent text-uppercase text-46 text-center">bezpłatnie</div>
                            </div>
                        </div>
                        <div class="form-container__wrapper">
                            <div class="info inner">
                                <?php if ( get_field( 'nieograniczony_dostep', $product->ID ) ): ?>
                                    <p class="inner01">Nieograniczony dostęp</p>
                                <?php else: ?>	
                                    <p class="inner01">Dostęp na 365 dni</p>
                                <?php endif; ?>
                                <p class="inner04">Liczba lekcji: <?php the_field('liczba_lekcji', $product->ID); ?></p>
                                <?php if ( get_field( 'imienny_certyfikat', $product->ID ) ): ?>
                                    <p class="inner02">Imienny certyfikat</p>
                                <?php endif; ?>
                                
                                <p class="inner05">Czas kursu: <?php the_field('czas_kursu', $product->ID); ?>min</p>
                                <?php if ( get_field( 'materialy_dydaktyczne', $product->ID ) ): ?>
                                    <p class="inner03">Dodatkowe materiały PDF</p>
                                <?php endif; ?>
                                
                                
                                
                                <p class="inner07<?php if (get_field('prowadzacy', $product->ID) == 'Jakub Mauricz') { ?>-jakub
                                <?php } elseif (get_field('prowadzacy', $product->ID) == 'Patrycja Szachta') { ?>-patrycja
                                <?php } elseif (get_field('prowadzacy', $product->ID) == 'Małgorzata Ostrowska') { ?>-malgorzata
                                <?php } ?>
                                ">Prowadzący: <?php the_field('prowadzacy', $product->ID); ?></p>
                            </div>
                        </div>
                        <div class="form-container__form">
                            <div class="title">
                                <?= $course['gdzie_wyslac'] ?>
                            </div>
                            <?php 
                                // echo "id landing".MjwpLandingpage::$id_landing_page;
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
                                    echo '<div class="alert alert-danger"><p>' . ($message_error) . '</p></div>';
                                    delete_transient('mjwp_landingpage_flash_message_error');
                                }
                                if ($message_success) {
                                    echo '<div class="alert alert-success"><p>' . ($message_success) . '</p></div>';
                                    delete_transient('mjwp_landingpage_flash_message_success');
                                }
                                echo '<div class="button-wrapper">';
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
                                    <!-- TEST:  -->
                                    <?php

                                    #$current_user = wp_get_current_user();

                                #print_r(createEvent("Klienci", "Zarejestrowani", $current_user->user_email));
                                echo '</div>';
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="course-container__images container-full width-full">
                <div class="signature">
                    <?php 
                        $image = $courseFields['podpis']['id'];
                        $size = 'full'; // (thumbnail, medium, large, full or custom size)
                        if( $image ) {
                            echo wp_get_attachment_image( $image, $size );
                        }
                    ?>
                </div>
                <div class="person">
                    <?php 
                        $image = $courseFields['mauricz']['id'];
                        $size = 'full'; // (thumbnail, medium, large, full or custom size)
                        if( $image ) {
                            echo wp_get_attachment_image( $image, $size );
                        }
                    ?>
                </div>
            </div>
        </div>
    </section>

    <!-- <script>
        jQuery(document).ready(function(){
            jQuery('.width-full').appendTo("#content");
        });
    </script> -->
<?php endif; ?>
<!-- 
<div class="wrap">
 
    <p>Widok darmowego szkolenia</p>
    POST :<br/>
    <?php 
 
    print_r($post);
    ?>
     <br/>
    Zwrócony obiekt produktu :<br/>
    <?php 
    
    // print_r($product_fields);
    print_r($product);


    print_r($regular_price);

    print_r($sale_price);
    ?>




</div> -->

<?php
get_footer();
?>
</div>