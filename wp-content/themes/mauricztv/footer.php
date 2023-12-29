<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package WP_Bootstrap_Starter
 */

?>
<?php if(!is_page_template( 'blank-page.php' ) && !is_page_template( 'blank-page-with-container.php' )): ?>

	<?php 
if(is_page(48)) { 
?>
	<?php 
	echo '<div class="container">';
	echo '<a href="'.get_permalink(56).'" class="btn btn-primary more center-btn">Kontynuj zakupy</a>'; 
	echo '</div>';
}
?>

			</div><!-- .row -->
		</div><!-- .container -->
	</div><!-- #content -->
	
    <?php get_template_part( 'footer-widget' ); ?>
	
	
	<div class="footer">
		<div class="container">
			<div class="row">
				<div class="col-lg-5">
				
					<?php if (is_front_page()){ ?>
						<img src="<?php echo get_template_directory_uri(); ?>/img/logo.svg" alt="Mauricz TV">
					<?php } else { ?>	
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home">
							<img src="<?php echo get_template_directory_uri(); ?>/img/logo.svg" alt="Mauricz TV">
						</a>
					<?php } ?>
					
					<h4>Zapisz się do newslettera</h4>
					
					<div class="nwsl">
						<?php echo do_shortcode("[contact-form-7 id='9' title='NWSL']"); ?>
					</div>
					
				</div>
				<div class="col-lg-7">
				
					<div class="row">
						<div class="col-sm-6 col-md-4">
							<h3>Menu</h3>
							
							<ul>
								<li><a href="/">Strona główna</a></li>
								<li><a href="/o-nas/">O Mauricz.tv</a></li>
								<li><a href="/lista-produktow/">Kursy online</a></li> 
								<li><a href="/faq/">FAQ</a></li> 
								<li><a href="/kontakt/">Kontakt</a></li>
							</ul>
							
						</div>
						<div class="col-sm-6 col-md-4">
							<h3>Informacje</h3>
							
							<ul>
								<li><a href="/regulamin/">Regulamin serwisu</a></li>
								<li><a href="/polityka-prywatnosci/">Polityka prywatności</a></li> 
								<li><a href="/cookies/">Cookies</a></li>
								<li><a href="/regulamin-newslettera/">Regulamin newslettera</a></li>
							</ul>
							
						</div>
						<div class="col-md-4">
							<h3>Twoje konto</h3>
							
							<ul>
							
								<?php if (is_user_logged_in()) : ?>
									<li><a href="/moje-konto/">Moje konto</a></li>
									<!--<li><a href="#">Moje kursy</a></li>-->
									<li><a href="/historia-transakcji/">Historia płatności i faktury</a></li>
									<li><a href="<?php echo wp_logout_url(get_permalink()); ?>">Wyloguj się</a></li>
								<?php else : ?>
									<li><a href="<?php echo wp_login_url(get_permalink()); ?>">Zaloguj się</a></li>
								<?php endif;?>

							</ul>
							
						</div>
					</div>
				
				</div>
			</div>
		</div>	
	</div>
	
	<div class="bottom-footer">
		<div class="container">
			<div class="row">
				<div class="col-md-7 first">
					&copy; <?php echo date('Y'); ?> Mauricz.tv. Wszelkie prawa zastrzeżone.
				</div>
				<div class="col-md-5 second">
					Projekt i wykonanie <a href="https://virtualpeople.pl">Virtual People</a>.
				</div>
			</div>
		</div>
	</div>
<?php endif; ?>
</div><!-- #page -->

<?php wp_footer(); ?>


	<script src="https://cdnjs.cloudflare.com/ajax/libs/Counter-Up/1.0.0/jquery.counterup.min.js"></script>
	<?php /* <script src="<?php echo get_template_directory_uri(); ?>/js/jquery.counterup.js"></script> */ ?>

		<script>

			if(!$) var $ = jQuery;
			jQuery(document).ready(function($) {
				$('.counter').counterUp({
					delay: 10,
					time: 3000
				});
			});
	 
			jQuery(document).ready(function($) {
				$( ".has-sub > .sub-menu, ul:not(.sub-menu) > .menu-item-has-children > .sub-menu" ).wrap( "<div class='sub-menu-scroll-wrapper'></div>" );
			});

		</script>

</body>
</html>