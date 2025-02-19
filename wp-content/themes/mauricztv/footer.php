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
	
	<?php if (is_front_page()){ ?>
	<div class="container">
		<div id="nwsl" class="row hp-contact">
		<div class="col-md-5"><img class="alignnone wp-image-2187 size-medium" src="<?php echo get_bloginfo('url');?>/wp-content/uploads/2023/12/0034-482x500.jpeg" alt="" width="482" height="500" /></div>
		<div class="col-md-7">
		<h6>Bądź na bieżąco!</h6>
		<h3>Zapisz się do newslettera!</h3>

<!-- <script>var fmFCF4k4bqyzpvc=function(e){if(e.data.type==='resize'){document.getElementById('fm-fc-f-4k4bqyzpvc').style.minHeight=e.data.size+'px'}};window.addEventListener?addEventListener('message',fmFCF4k4bqyzpvc,!1):attachEvent('onmessage',fmFCF4k4bqyzpvc);
</script><iframe id="fm-fc-f-4k4bqyzpvc" data-height="255" src="https://forms.freshmail.io/f/duaaswiwmi/4k4bqyzpvc/index.html" frameborder="0" marginheight="0" marginwidth="0" width="100%" style="min-height: 285px"></iframe> -->
    <div class="klaviyo-form-Wixv96"></div>
		</div>
		</div>
	</div>	
	<?php } ?>
	

	
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
<!-- 					
<script>var fmFCF4k4bqyzpvc=function(e){if(e.data.type==='resize'){document.getElementById('fm-fc-f-4k4bqyzpvc').style.minHeight=e.data.size+'px'}};window.addEventListener?addEventListener('message',fmFCF4k4bqyzpvc,!1):attachEvent('onmessage',fmFCF4k4bqyzpvc);
</script><iframe id="fm-fc-f-4k4bqyzpvc" data-height="255" src="https://forms.freshmail.io/f/duaaswiwmi/4k4bqyzpvc/index.html" frameborder="0" marginheight="0" marginwidth="0" width="100%" style="min-height: 285px"></iframe> -->
    <div class="klaviyo-form-Wixv96"></div>

						<?php //echo do_shortcode("[contact-form-7 id='9' title='NWSL']"); ?>
						
						
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
									<li><a href="/moje-certyfikaty/">Moje certyfikaty</a></li>
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
					&copy; 2020 - <?php echo date('Y'); ?> Mauricz.tv. Wszelkie prawa zastrzeżone.
				</div>
				<div class="col-md-5 second">
					Projekt i wykonanie <a href="https://virtualpeople.pl">Virtual People</a>.
				</div>
			</div>
		</div>
	</div>
<?php endif; ?>
</div><!-- #page -->
<div id="backdrop" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 999;"></div>
<div id="cartPopup" style="display: none;">
    <h2>Nie dokończyłeś/-łaś swoich ostatnich zakupów?</h2>
	<h3>Zajrzyj do koszyka, nasze szkolenia czekają na Ciebie!</h3>
	<div class="buttons">
		<a href="/zamowienie" style="" class="more">Przejdź do koszyka</a>
		<button id="closePopupButton" class="more">Zamknij</button>
		</div>
</div>

<!-- <div id="customPopup" style="display: none;">
    <h2>🎉 BLACK WEEK WIELKA PROMOCJA! 🎉</h2>
	<p>Tylko teraz zyskaj aż <strong>55% rabatu</strong> na wszystkie szkolenia! (nie dotyczy pakietów)</p>
	<p>Skorzystaj z kodu: <strong>CZARNY55</strong></p>
	<div class="countdown-row"><p>📅 Oferta ważna jeszcze przez:</p> <?= do_shortcode( '[ycd_countdown id=44]' ) ?></div>
	<p>👉 Zrób zakupy już teraz i oszczędzaj!</p>
	<div class="buttons">
		<a href="/lista-produktow/" style="" class="more">Szkolenia</a>
		<button id="customClosePopupButton" class="more">Zamknij</button>
		</div>
</div> -->
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



			(function( $ ) {
    $.fn.generatePdfAndClose = function() {
        var $this = $(this),
            element = document.getElementById('generate-pdf-and-close'),
            orientation = $this.find('#pb-page').data('orientation'),
            opt_orientation = (orientation == 'horizontal') ? 'landscape' : 'portrait',
            opt = {
            margin:0,
            filename:     'certificate.pdf',
            image:        { type: 'jpeg', quality: 1 },
            html2canvas:  { scale: 2, useCORS: true },
            jsPDF:        {format : 'a4', orientation  : opt_orientation}
        };

        $('.ui-icon-gripsmall-diagonal-se').hide()

        html2pdf(element, opt)
            .set({ html2canvas: { scale: 2, useCORS: true } })
            .then(function(pdf) {
            setTimeout(function () {
                window.close()
            }, 1000);
        });
    };
}( jQuery ));


jQuery( document ).ready(function() {
    if ( jQuery( "#generate-pdf-and-close" ).length > 0) {
        // setTimeout added because generatePdfAndClose will not have time to load
        setTimeout(function(){
            jQuery( "#generate-pdf-and-close" ).generatePdfAndClose();
        }, 750);
    }
});

		</script>
<script async type='text/javascript' src='https://static.klaviyo.com/onsite/js/klaviyo.js?company_id=RAAZX9'></script>
</body>
</html>