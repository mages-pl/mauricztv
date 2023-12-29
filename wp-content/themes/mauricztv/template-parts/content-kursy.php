<?php
/**
 * Template part for displaying posts
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package WP_Bootstrap_Starter
 */

?>






<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<div class="entry-content">
	


<div class="breadcrumbs" typeof="BreadcrumbList" vocab="https://schema.org/">
	<?php if(function_exists('bcn_display'))
	{
		bcn_display();
	}?>
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
							<?php endif; ?>
							
							<?php if ( get_field( 'imienny_certyfikat' ) ): ?>
								<p class="inner02"><b>Imienny certyfikat</b></p>
							<?php endif; ?>
							
							<?php if ( get_field( 'materialy_dydaktyczne' ) ): ?>
								<p class="inner03">Materiały dydaktyczne w formie PDF </p>
							<?php endif; ?>
							
							<p class="inner04">Liczba lekcji: <?php the_field('liczba_lekcji'); ?></p>
							
							<p class="inner05">Czas kursu: <?php the_field('czas_kursu'); ?>h</p>
							
							<p class="inner06">Szkolenie kupiło aż <?php the_field('ilosc_kursantow'); ?> kursantów!</p>
							
							<p class="inner07">Prowadzący: <?php the_field('prowadzacy'); ?></p>
							
						</div>
						
						<h6 class="price">Cena:</h6>
						
						<?php if ( get_field( 'cena_przekreslona' ) ): ?>
							<h4 class="crossed"><?php the_field('cena_przekreslona'); ?> PLN</h4>
						<?php endif; ?>
						
						<h4><?php the_field('cena'); ?> PLN</h4>
						
						<?php if ( get_field( 'cena_przed_obnizka' ) ): ?>
							<small>Najniższa cena z 30 dni: <?php the_field('cena_przed_obnizka'); ?> PLN</small>
						<?php endif; ?>
						
						<div class="links">
							<a href="<?php the_field('link_do_kursu_w_publigo'); ?>" class="more">Kup teraz</a>
							<a href="#kursy-why" class="more-empty">Więcej o kursie</a>
						</div>

					</div>
					<div class="col-md-6">
						
						<div class="movie">
							<?php the_field('filmik'); ?>
						</div>	
					
					</div>
					
					<div class="col-md-12 top-kursy-links">
					
						<a href="#kursy-why" class="little-mouse"><img src="<?php echo get_template_directory_uri(); ?>/img/little-mouse.png" alt="Mauricz TV"/></a>
						<a href="#kursy-why"><img src="<?php echo get_template_directory_uri(); ?>/img/little-triangle.png" alt="Mauricz TV"/></a>
					
					</div>
					
				</div>
			</div>

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
							<a href="<?php the_field('link_do_kursu_w_publigo'); ?>" class="more">Kup teraz</a>
						</div>	
						
					</div>
				</div>
			</div>
			
		</div>	

		<div class="kursy-who row-full">
		
			<div class="container">
				<div class="row">
				
					<div class="col-md-12">
						<h3>Szkolenie opracował</h3>
					</div>
					
					<div class="col-md-6">
						<h4><?php the_field('imie_i_nazwisko'); ?></h4>
						
						<?php the_field('kto_opracowal_tresc'); ?>
						
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
					
					<div class="col-xs-6 col-md-3">
						<div class="inner">
							<img src="<?php echo get_template_directory_uri(); ?>/img/icon-what01.png" alt="Mauricz TV">
							<p><?php the_field('pierwszy_tekst'); ?></p>
						</div>
					</div>
					<div class="col-xs-6 col-md-3">
						<div class="inner">
							<img src="<?php echo get_template_directory_uri(); ?>/img/icon-what02.png" alt="Mauricz TV">
							<p><?php the_field('drugi_tekst'); ?></p>
						</div>
					</div>
					<div class="col-xs-6 col-md-3">
						<div class="inner">
							<img src="<?php echo get_template_directory_uri(); ?>/img/icon-what03.png" alt="Mauricz TV">
							<p><?php the_field('trzeci_tekst'); ?></p>
						</div>
					</div>
					<div class="col-xs-6 col-md-3">
						<div class="inner">
							<img src="<?php echo get_template_directory_uri(); ?>/img/icon-what04.png" alt="Mauricz TV">
							<p><?php the_field('czwarty_tekst'); ?></p>
						</div>
					</div>
					
					<div class="col-md-12">
						<a href="<?php the_field('link_do_kursu_w_publigo'); ?>" class="more">Kup teraz</a>
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
				
				  
            <!-- cena -->
            <?php
                    if((date('Y-m-d') >= $sale_price_from_date) && (date('Y-m-d') < $sale_price_to_date)) { 
                        ?>
            <h4 class="crossed"><?php echo $product_price; ?> PLN</h4>
        <?php
    }
    ?>              		
					<small class="omniprice">
                    <!-- Najniższa cena z 30 dni: -->
                    <?= bpmj_render_lowest_price_information($product_id); ?>
                     <!-- PLN -->
                    </small>
				
				<div class="row">
				
					<div class="col-md-6 text-right">
						Liczba modułów:
					</div>
					<div class="col-md-6">
						<?php the_field('liczba_lekcji'); ?>
					</div>	
					<div class="col-md-6 text-right">		
						Czas trwania:
					</div>	
					<div class="col-md-6">
						<?php the_field('czas_kursu'); ?>min
					</div>	
				</div>
				
				<a href="<?php the_field('link_do_kursu_w_publigo'); ?>" class="more-green"><span>Kup teraz</span></a>
				
			</div>

		</div>				
					
					
					
					
					
					
	


	</div><!-- .entry-content -->

	<footer class="entry-footer">
		<?php wp_bootstrap_starter_entry_footer(); ?>
	</footer><!-- .entry-footer -->
</article><!-- #post-## -->
