<?php 
use bpmj\wpidea\Course_Progress;
use bpmj\wpidea\Courses;
use bpmj\wpidea\courses\core\entities\Course_Structure;

use bpmj\wpidea\resources\Resource_Type;
use bpmj\wpidea\sales\product\model\Gtu;
use bpmj\wpidea\wolverine\user\User;

use bpmj\wpidea\wolverine\product\Product;
//use bpmj\wpidea\wolverine\product\course\Product;

global $post;
?>
<?php
  if($post->post_type != 'download') {
       
    $getProductByPage = WPI()->courses->get_product_by_page_id((int)$post->ID);
    $product_id = (int)$getProductByPage->id;
    $product_price = $getProductByPage->price;
    $product_access = $getProductByPage->productAccess;
 
} else { 
    // typ DOWNLAOD (produkt)
    $product_id = (int)$post->ID;
    $p = new Product($product_id);
    $product_price = get_post_meta( $product_id,  'edd_price', true);
    $sale_price = get_post_meta( $product_id,  'sale_price', true);
    $sale_price_from_date = get_post_meta( $product_id,  'sale_price_from_date', true);
    $sale_price_to_date = get_post_meta( $product_id,  'sale_price_to_date', true);
    
    
    $product_access = $p->productAccess;

   // echo "IMG".get_post_meta($product_id,  'edd_featured_image', true);
//     print_r($post);
//     print_r($sale_price);
// echo "#";
//     print_r($sale_price_from_date);
//     print_r($sale_price_to_date);
  
}


$course = WPI()->courses->get_course_by_product( $product_id );
$course_page_id = get_post_meta( $course->ID, 'course_id', true );
$restricted_to  = array( array( 'download' => $product_id ) );
$user_id = get_current_user_id();
$access         = bpmj_eddpc_user_can_access( $user_id, $restricted_to, $course_page_id );
if ( 'valid' === $access[ 'status' ] || 'waiting' === $access[ 'status' ] ) {
$show_open_padlock = true;
} else   { 
$show_open_padlock = false;
}
?>


<?php if (!is_page('zamowienie')){ ?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

<div class="entry-content">

        <div class="container">
            <div class="row">
				<div class="col-md-12">
					<div class="breadcrumbs breadcrumbs-product" typeof="BreadcrumbList" vocab="https://schema.org/">
						<?php if(function_exists('bcn_display'))
						{
							bcn_display();
						}?>
					</div>
				</div>
			</div>
		</div>

    <div class="top-kursy row-full">
    
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h5>Kurs online</h5>
                    <h1><?php the_title(); ?></h1>
                </div>
                <div class="col-md-6">
                
                    <h6>Ten kurs obejmuje:</h6>
                    
                    <div class="inner">
                    
                        <?php if ( get_field( 'nieograniczony_dostep' ) ): ?>
                            <p class="inner01"><b>Nieograniczony dostęp</b></p>
						<?php else: ?>	
							<p class="inner01"><b>Dostęp na 365 dni</b></p>
                        <?php endif; ?>
						
						
						

						
						
                        
                        <?php if ( get_field( 'imienny_certyfikat' ) ): ?>
                            <p class="inner02"><b>Imienny certyfikat</b></p>
                        <?php endif; ?>
                        
                        <?php if ( get_field( 'materialy_dydaktyczne' ) ): ?>
                            <p class="inner03">Materiały dydaktyczne w formie PDF </p>
                        <?php endif; ?>
                        
                        <p class="inner04">Liczba lekcji: <?php the_field('liczba_lekcji'); ?></p>
                        
                        <p class="inner05">Czas kursu: <?php the_field('czas_kursu'); ?>min</p>
                        
                        <p class="inner06">Szkolenie kupiło aż <?php the_field('ilosc_kursantow'); ?> kursantów!</p>
                        
                        <p class="inner07<?php if (get_field('prowadzacy') == 'Jakub Mauricz') { ?>-jakub
						<?php } elseif (get_field('prowadzacy') == 'Patrycja Szachta') { ?>-patrycja
						<?php } elseif (get_field('prowadzacy') == 'Małgorzata Ostrowska') { ?>-malgorzata
						<?php } ?>
						">Szkolenie prowadzi: <?php the_field('prowadzacy'); ?></p>
 
                    </div>
                    
                    <h6 class="price">Cena:</h6>
                    
                    <?php
                    if((date('Y-m-d') >= $sale_price_from_date) && (date('Y-m-d') < $sale_price_to_date)) { 
                        ?>
            <h4 class="crossed"><?php echo $product_price; ?> PLN</h4>
        <?php
    }
    ?>

                    <!-- <?php if ( get_field( 'cena_przekreslona' ) ): ?>
                        <h4 class="crossed"><?php the_field('cena_przekreslona'); ?> PLN</h4>
                    <?php endif; ?> -->
                    

                    <?php
                    if((date('Y-m-d') >= $sale_price_from_date) && (date('Y-m-d') < $sale_price_to_date)) { 
                        ?>
        <h4><?php echo number_format($sale_price,2,'.',''); ?> PLN</h4>
    <?php
    } else {?>
        <h4><?php echo $product_price; ?> PLN</h4>
    <?php
    }
    ?>
                    <!-- <h4><?php echo $product_price; ?> PLN</h4> -->
                    
                    
                        <small>
                        <?= bpmj_render_lowest_price_information($product_id); ?>
                            <!-- Najniższa cena z 30 dni:  -->
                             <!-- PLN -->
                            </small>
                    
                    
                    <div class="links">
        <!--  BEGIN: Dodaj do koszyka -->
        <?php 
            if($show_open_padlock != '1') { 
        ?>
        <a href="<?php echo esc_attr( edd_get_checkout_uri( array(
               'add-to-cart' => (int)$product_id,
           ) ) ); ?>" class="more">Kup teraz</a>
       <?php 
        }
       ?>
        <!--  END: Dodaj do koszyka -->      
        <!-- BEGIN: PRZEJDŹ DO PANELU  -->
<?php 
if($show_open_padlock) { 
?>
<a href="<?php echo get_permalink($course_page_id); ?>" class="box_glowna_add_to_cart_link more" style=" background: #333;color: #fff;"><i
    class="fa fa-arrow-right"></i><?php _e( 'GO TO COURSE', BPMJ_EDDCM_DOMAIN ) ?>
</a>
<?php 
}
?>
<?php 
?>
<!-- END: PRZEJDŹ DO PANELU -->

                        <a href="#kursy-content" class="more-empty">Więcej o kursie</a>
                    </div>

<!-- BEGIN: Sprawdź czy kurs dostępny -->

<?php 

// $sales_status = WPI()->courses->get_sales_status( $course->ID, $product_id );
// if ( 'disabled' === $sales_status[ 'status' ] ) {
//     $sales_disabled = true;
// }

// echo "PRODUCT ID: ".(int)$product_id;
// echo "<br/>";
// echo "COURSE PAGE ID: ".(int)$course_page_id;
// echo "<br/>";
// echo "STAN KURSU : ".(boolean)$show_open_padlock;


?>
<!-- END: Sprawdź czy kurs dostępny -->
<br/><Br/>
                    <?php 
// Jesli user jest zalogowany
//   if(is_user_logged_in())  { 
//       echo "Zalogowany";

// ?> 
<?php
//   } else { 
//      echo "Niezalogowany";

//   }
?>

                    <?php 
    
//     echo "PRICE:";
// echo $product_price;

// echo "ID: ";
// print_r($product_id); 
?>


                </div>
                <div class="col-md-6">
                    
                    <div class="movie">
					
						<?php if (get_field('filmik')) { ?>

							<?php the_field('filmik'); ?>

						<?php } ?>
					
						<?php if (get_field('grafika_zamiast_filmu')) { ?>

							<img src="<?php the_field('grafika_zamiast_filmu'); ?>" />

						<?php } ?>
						
                    </div>	

                </div>
                
                <div class="col-md-12 top-kursy-links">
                
                    <a href="#kursy-why" class="little-mouse"><img src="<?php echo get_template_directory_uri(); ?>/img/little-mouse.png" alt="Mauricz TV"/></a>
                    <a href="#kursy-why"><img src="<?php echo get_template_directory_uri(); ?>/img/little-triangle.png" alt="Mauricz TV"/></a>
                
                </div>
                
            </div>
        </div>

    </div>
	
	<div class="kursy-content row" id="kursy-content">
	
		<h3>Opis szkolenia</h3>
	
		<?php the_content(); ?>
	
	</div>
    
    
    <div class="kursy-why row" id="kursy-why">
    
        <div class="col-md-12">
            <h3><?php the_field('tytul_sekcji_dlaczego'); ?></h3>
        </div>
        
        <div class="col-md-6">
            <?php the_field('lewa_strona_sekcji'); ?>
        </div>
        <div class="col-md-6">
            <?php the_field('prawa_strona_sekcji'); ?>
        </div>
        

    </div>
    
    <div class="kursy-competences row">
    
        <div class="col-md-12">
            <h3><?php the_field('tytul_sekcji_kompetencje'); ?></h3>
        </div>
        
        <div class="col-md-4 first">
            <?php the_field('pierwsze_pole'); ?>
        </div>
        <div class="col-md-4 second">
            <?php the_field('drugie_pole'); ?>
        </div>
        <div class="col-md-4 third">
            <?php the_field('trzecie_pole'); ?>
        </div>
        
    </div>
	

    <div class="kursy-agenda row-full">
    
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                
                    <h3 class="upper">Agenda szkolenia</h3>
            
                    <?php the_field('miejsce_na_shortcode'); ?>
                    
                    <h3 class="lower">Zainteresował Cię ten kurs?</h3>
                    
                    <div class="text-center">
                        <?php 

                        ?>
                        <?php 
if($show_open_padlock != '1') { 
?>
                          <!--  BEGIN: Dodaj do koszyka -->
<a href="<?php echo esc_attr( edd_get_checkout_uri( array(
               'add-to-cart' => (int)$product_id,
           ) ) ); ?>" class="more">Kup teraz</a>
          <!--  END: Dodaj do koszyka -->  
<?php 
}
else {
?>
<!-- BEGIN: PRZEJDZ DO KURSU -->
<a href="<?php echo get_permalink($course_page_id); ?>" class="box_glowna_add_to_cart_link more" style=" background: #333;color: #fff;"><i
    class="fa fa-arrow-right"></i><?php _e( 'GO TO COURSE', BPMJ_EDDCM_DOMAIN ) ?>
</a>
<!-- END: PRZEJDZ DO KURSU -->
<?php 
}
?>
 
                    </div>	
                    
                </div>
            </div>
        </div>
        
    </div>	

    <div class="kursy-who row-full
	
		<?php if (get_field('prowadzacy') == 'Jakub Mauricz') { ?>kw-jakub
		<?php } elseif (get_field('prowadzacy') == 'Patrycja Szachta') { ?>kw-patrycja
		<?php } elseif (get_field('prowadzacy') == 'Małgorzata Ostrowska') { ?>kw-malgorzata
		<?php } ?>
	
	">
    
        <div class="container">
            <div class="row">
            
                <div class="col-md-12">
                    <h3>Szkolenie opracowane przez</h3>
                </div>
                
                <div class="col-md-6">
                    <h4><?php the_field('prowadzacy'); ?><?php //the_field('imie_i_nazwisko'); ?></h4>
                    
						<?php if (get_field('prowadzacy') == 'Jakub Mauricz') { ?>
						
							<?php the_field('kto_opracowal_tresc'); ?>
						
						<?php } elseif (get_field('prowadzacy') == 'Patrycja Szachta') { ?>
						
							<?php the_field('kto_opracowal_tresc_patrycja'); ?>
						
						<?php } elseif (get_field('prowadzacy') == 'Małgorzata Ostrowska') { ?>
						
							<?php the_field('kto_opracowal_tresc_malgorzata'); ?>
						
						<?php } ?>
                    
                    
                </div>
                
                <div class="col-md-6">
                </div>
                
            </div>
        </div>
        
    </div>	
    

    <div class="kursy-what row-full">
    
        <div class="container">
            <div class="row">
            
                <div class="col-md-12">
                    <h3>Czego dowiesz się na szkoleniu?</h3>
                </div>
                
                <div class="col-xs-6 col-lg-3">
                    <div class="inner">
                        <img src="<?php echo get_template_directory_uri(); ?>/img/icon-what01.png" alt="Mauricz TV">
                        <p><?php the_field('pierwszy_tekst'); ?></p>
                    </div>
                </div>
                <div class="col-xs-6 col-lg-3">
                    <div class="inner">
                        <img src="<?php echo get_template_directory_uri(); ?>/img/icon-what02.png" alt="Mauricz TV">
                        <p><?php the_field('drugi_tekst'); ?></p>
                    </div>
                </div>
                <div class="col-xs-6 col-lg-3">
                    <div class="inner">
                        <img src="<?php echo get_template_directory_uri(); ?>/img/icon-what03.png" alt="Mauricz TV">
                        <p><?php the_field('trzeci_tekst'); ?></p>
                    </div>
                </div>
                <div class="col-xs-6 col-lg-3">
                    <div class="inner">
                        <img src="<?php echo get_template_directory_uri(); ?>/img/icon-what04.png" alt="Mauricz TV">
                        <p><?php the_field('czwarty_tekst'); ?></p>
                    </div>
                </div>
                
                <div class="col-md-12">
                <?php 
					if($show_open_padlock != '1') { 
					?>
											  <!--  BEGIN: Dodaj do koszyka -->
					<a href="<?php echo esc_attr( edd_get_checkout_uri( array(
								   'add-to-cart' => (int)$product_id,
							   ) ) ); ?>" class="more">Kup teraz</a>
							  <!--  END: Dodaj do koszyka -->  
					<?php 
					}
					else {
					?>
					<!-- BEGIN: PRZEJDZ DO KURSU -->
					<a href="<?php echo get_permalink($course_page_id); ?>" class="box_glowna_add_to_cart_link more" style=" background: #333;color: #fff;"><i
						class="fa fa-arrow-right"></i><?php _e( 'GO TO COURSE', BPMJ_EDDCM_DOMAIN ) ?>
					</a>
					<!-- END: PRZEJDZ DO KURSU -->
					<?php 
					}
					?>
                </div>
                
            </div>
        </div>
        
    </div>


    <div class="kursy-list row-full">
    
        <div class="container">
            <div class="row">
            
                <div class="col-md-12">
                    <h3>Uczestnicy kursu kupili również</h3>
                </div>
                
                
                <?= do_shortcode("[mjcourses category='bestsellery' quantity='4' tag-labels='0' category-labels='0']"); ?>
            
            </div>
        </div>
        
    </div>


    <div class="kursy-cert row-full">
    
        <div class="container">
            <div class="row">
    
                <div class="col-md-6">
                
                    <h5>Po ukończeniu kursu otrzymasz</h5>
                    <h6>certyfikat</h6>
                
                </div>
                <div class="col-md-6">
                
                    <img src="<?php the_field('certyfikat'); ?>" />
                
                </div>
        
            </div>
        </div>
    
    </div>

    <div class="kursy-opinions row">
    
        <div class="col-md-12">
            <h3>Opinie o kursie</h3>
        </div>
    
        <?php echo do_shortcode("[ic_add_posts template='template-opinion.php' category='opinie' showposts='3']"); ?>
        
    </div>	

    <div class="kursy-faq row-full">
    
        <div class="container">
            <div class="row">
    
                <div class="col-md-12">
                    <h3 class="upper">FAQ</h3>
                </div>

                <div class="col-md-12">
                    <?php echo do_shortcode("[sp_easyaccordion id='91']"); ?>
                </div>

            </div>
        </div>
    
    </div>
                
                
    <div class="kursy-bottom row">
    
        <div class="col-md-12">
            <h3>Zamów szkolenie teraz</h3>
        </div>
        
        <div class="box">
            <h6><?php the_title(); ?></h6>
            
            <?php
                    if((date('Y-m-d') >= $sale_price_from_date) && (date('Y-m-d') < $sale_price_to_date)) { 
                        ?>
        <h4><?php echo number_format($sale_price,2,'.',''); ?> PLN</h4>
    <?php
    } else {?>
        <h4><?php echo $product_price; ?> PLN</h4>
    <?php
    }
    ?>
                    
            
                <small class="omniprice">
                    <!-- Najniższa cena z 30 dni: -->
                    <?= bpmj_render_lowest_price_information($product_id); ?>
                     <!-- PLN -->
                    </small>
         
            
            <div class="row">
            
                <div class="col-xs-6 text-right">
                    Liczba lekcji:
                </div>
                <div class="col-xs-6">
                    <?php the_field('liczba_lekcji'); ?>
                </div>	
                <div class="col-xs-6 text-right">		
                    Czas trwania:
                </div>	
                <div class="col-xs-6">
                    <?php the_field('czas_kursu'); ?>min
                </div>	
            </div>
            

            <!--  BEGIN: Dodaj do koszyka -->
        <?php 
            if($show_open_padlock != '1') { 
        ?>
        <a href="<?php echo esc_attr( edd_get_checkout_uri( array(
               'add-to-cart' => (int)$product_id,
           ) ) ); ?>" class="more">Kup teraz</a>
       <?php 
        }
       ?>
        <!--  END: Dodaj do koszyka -->      
        <!-- BEGIN: PRZEJDŹ DO PANELU  -->
<?php 
if($show_open_padlock) { 
?>
<a href="<?php echo get_permalink($course_page_id); ?>" class="box_glowna_add_to_cart_link more" style=" background: #333;color: #fff;"><i
    class="fa fa-arrow-right"></i><?php _e( 'GO TO COURSE', BPMJ_EDDCM_DOMAIN ) ?>
</a>
<?php 
}
?>
<?php 
?>
<!-- END: PRZEJDŹ DO PANELU -->


            <!--  BEGIN: Moduły kursu -->
            
            <?php
            if($post->post_type == 'page') :
  $modules = WPI()->courses->get_course_level1_modules_or_lessons( $course_page_id );
 
?>
<div class="row panel_kursu_moduly">

    <?php

        foreach ( $modules as $id => $module ) {
            ?>
            <div class="col-sm-4">
                <div class="modul_lekcja<?php if ( $module->should_be_grayed_out() ) echo ' lek_niedostepna'; ?>">
                    <?php if ( $module->should_be_grayed_out() ): ?>
                        <div class="modul_lekcja_zdjecie">
                            <?php $drip = $module->get_calculated_drip();
                            if( !empty( $drip ) ) { 
                                
                            ?>
                            <div class="lekcja_niedostepna" data-drip="<?php echo $drip ?>">
                                <p><?php if( $module->is_lesson() ) { _e( 'The lesson will be available in', BPMJ_EDDCM_DOMAIN ); } else {
                                    _e( 'The module will be available in', BPMJ_EDDCM_DOMAIN ); } ?>:</p>
                                <div>
                                    <div class="lekcja_niedostepna_zegar"><i class="fas fa-clock"></i></div>
                                    <div class="lekcja_niedostepna_time">
                                        <div class="drip_time_d"><?php echo bpmj_eddcm_seconds_to_time( $drip, 'a' ) ?></div>
                                        <div><?php _e( 'days', BPMJ_EDDCM_DOMAIN ) ?></div>
                                    </div>
                                    <div class="lekcja_niedostepna_time">:</div>
                                    <div class="lekcja_niedostepna_time">
                                        <div class="drip_time_h"><?php echo bpmj_eddcm_seconds_to_time( $drip, 'h' ) ?></div>
                                        <div><?php _e( 'hr', BPMJ_EDDCM_DOMAIN ) ?></div>
                                    </div>
                                    <div class="lekcja_niedostepna_time">:</div>
                                    <div class="lekcja_niedostepna_time">
                                        <div class="drip_time_m"><?php echo bpmj_eddcm_seconds_to_time( $drip, 'i' ) ?></div>
                                        <div><?php _e( 'min', BPMJ_EDDCM_DOMAIN ) ?></div>
                                    </div>
                                    <div class="lekcja_niedostepna_time">:</div>
                                    <div class="lekcja_niedostepna_time">
                                        <div class="drip_time_s"><?php echo bpmj_eddcm_seconds_to_time( $drip, 's' ) ?></div>
                                        <div><?php _e( 'sec', BPMJ_EDDCM_DOMAIN ) ?></div>
                                    </div>
                                </div>
                            </div>
                            <?php } ?>
                            <img src="<?php if ( $module->get_thumbnail() ) { echo $module->get_thumbnail(); } else { echo bpmj_eddcm_template_get_file( 'assets/img/box1.jpg' ); } ?>" />
                        </div>
                        <div class="modul_lekcja_tytul">
                            <?php echo $module->post_title; ?>
                        </div>
                        <?php if ( $module->get_subtitle() ) { ?>
                            <?php echo '<div class="modul_lekcja_opis">' . $module->get_subtitle() . '</div>'; ?><?php } ?>
                    <?php else: ?>
                        <a href="<?php echo get_permalink( $module->unwrap() ); ?>">
                            <div class="modul_lekcja_zdjecie">
                                <img src="<?php if ( $module->get_thumbnail() ) { echo $module->get_thumbnail(); } else { echo bpmj_eddcm_template_get_file( 'assets/img/box1.jpg' ); } ?>" />
                            </div>
                        </a>
                        <div class="modul_lekcja_tytul">
                            <a href="<?php echo get_permalink( $module->unwrap() ); ?>"><?php echo $module->post_title; ?></a>
                        </div>
                        <?php if ( $module->get_subtitle() ) { ?><?php echo '<div class="modul_lekcja_opis">' . $module->get_subtitle() . '</div>'; ?>
                            <?php } ?>
                    <?php endif; ?>
                </div>
            </div>
            <?php
        }
    endif; 
        ?>
<!--  END: Moduły kursu -->

<!--  BEGIN: Zawartość kursu -->
            <?php
        if($post->post_type == 'page') :
            $getCourseID = WPI()->courses->get_course_by_page((int)$post->ID);
            $modules = WPI()->courses->get_course_level1_modules_or_lessons( null );
            
            if(!empty($getCourseID->ID)) {
            $progress = new Course_Progress( $getCourseID->ID);

            $lessons  = WPI()->courses->get_course_structure_flat( null, false );
            $lessons_cnt = count( $lessons );
            $i = 0;
            $parent = 0;
            ?>
        <?php
            if ( ! empty( $lessons ) ) {
        ?>

        <?php
        foreach ( $lessons as $lesson ) {
            if( $i == 0 ) {
                echo '<div class="col-sm-6 etapy_kursu">
                    <div class="etap_kursu">';
            }
            else if( $lessons_cnt > 3 && $i == (int)($lessons_cnt / 2) ) {
                echo '</ul></div></div><div class="col-sm-6 etapy_kursu">
                    <div class="etap_kursu"><ul>';
            }
            if( $lesson->post_parent != $parent ) {
                if( $i != 0 ) echo '</ul>';
                if( $lesson->post_parent == $lesson_page_id ) {
                    echo '<p></p><ul>';
                }
                else {
                    echo '<p><i class="icon-module"></i> ' . get_the_title( $lesson->post_parent ) . '</p>
                        <ul>';
                }
                $parent = $lesson->post_parent;
            }
            
            $class_active = '';
            if( $lesson_page_id == $lesson->ID ) {
                $class_active = ' active';
            }
            
            if ( $lesson->should_be_grayed_out() ) {
                echo '<li' . ( $progress->is_lesson_finished( $lesson->ID ) ? ' class="zakonczony"' : '' ) . '><div class="etap_kursu_kreska fa"></div><span>' . $lesson->post_title . '</span></li>';
            } else {
                echo '<li' . ( $progress->is_lesson_finished( $lesson->ID ) ? ' class="zakonczony' . $class_active . '"' : ' class="' . $class_active . '"' ) . '><div class="etap_kursu_kreska fa"></div><a href="' . $lesson->get_permalink() . '">' . $lesson->post_title . '</a></li>';
            }
            $i ++;
        }
        ?>
    </ul>
<?php } ?>
<?php 
    }
endif; 
?>

<!--  END: Zawartość kursu -->

<br/>

        </div>

    </div>				


</div><!-- .entry-content -->


</article><!-- #post-## -->

<?php } ?>
