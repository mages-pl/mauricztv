<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="profile" href="http://gmpg.org/xfn/11">
	
	<meta name="theme-color" content="#1AD779" />
	
	<link rel="shortcut icon" href="<?php echo get_template_directory_uri(); ?>/img/favicon.ico" type="image/x-icon">
	<link rel="icon" href="<?php echo get_template_directory_uri(); ?>/img/favicon.ico" type="image/x-icon">
	
	<?php if (is_front_page('moje-konto')){ ?>
	<style>
		.footer{margin-top:50px;}
	</style>
	<?php } ?>
	
	<?php if (in_category('dieta') || in_category('suplementacja') || in_category('trening')){ ?>
	
		<style>
		
			.site{background:url('<?php echo get_template_directory_uri(); ?>/img/single-background.jpg')no-repeat top center;}

		</style>

	<?php } ?>

	
<?php wp_head(); ?>

		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
		

</head>

<body <?php body_class(); ?>>

<?php 

    // WordPress 5.2 wp_body_open implementation
    if ( function_exists( 'wp_body_open' ) ) {
        wp_body_open();
    } else {
        do_action( 'wp_body_open' );
    }

?>

<?php if(!is_page_template( 'blank-page.php' ) && !is_page_template( 'blank-page-with-container.php' )): ?>
<?php 
if(is_plugin_active('wp-idea/wp-idea.php')) { 
    WPI()->templates->header();
    ?>
<?php
 } else { 
?>
	<header id="masthead" class="site-header navbar-static-top <?php echo wp_bootstrap_starter_bg_class(); ?>" role="banner">
        <div class="container">
         
                <div class="navbar-brand">
				
					<?php if (is_front_page()){ ?>
						<img src="<?php echo get_template_directory_uri(); ?>/img/logo.svg" alt="Mauricz TV">
					<?php } else { ?>	
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home">
							<img src="<?php echo get_template_directory_uri(); ?>/img/logo.svg" alt="Mauricz TV">
						</a>
					<?php } ?>

                </div>
				
				<div class="top-menu">
                <?php
                wp_nav_menu(array(
                'theme_location'    => 'secondary',
                'container'       => 'div',
                'container_id'    => 'top-nav',
                'container_class' => 'collapseA navbar-collapse justify-content-end',
                'menu_id'         => false,
                'menu_class'      => 'top-nav',
                'depth'           => 3,
                'fallback_cb'     => 'wp_bootstrap_navwalker::fallback',
                'walker'          => new wp_bootstrap_navwalker()
                ));
                ?>
				</div>
				
                <?php
                wp_nav_menu(array(
                'theme_location'    => 'primary',
                'container'       => 'div',
                'container_id'    => 'main-nav',
                'container_class' => 'collapse navbar-collapse justify-content-end',
                'menu_id'         => false,
                'menu_class'      => 'navbar-nav',
                'depth'           => 3,
                'fallback_cb'     => 'wp_bootstrap_navwalker::fallback',
                'walker'          => new wp_bootstrap_navwalker()
                ));
                ?>

         
        </div>
	</header><!-- #masthead -->

<?php
 }
?>

<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'wp-bootstrap-starter' ); ?></a>
	
	<div id="content" class="site-content">
		<div class="container">
			<div class="row">
                <?php endif; ?>