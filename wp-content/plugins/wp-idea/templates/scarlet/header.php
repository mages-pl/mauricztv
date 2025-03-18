<?php

use bpmj\wpidea\templates_system\admin\modules\settings_handlers\New_Templates_System_Settings_Handler;
use bpmj\wpidea\templates_system\templates\scarlet\Search_Page_Template;
use bpmj\wpidea\modules\app_view\api\App_View_API_Static_Helper;

?>

<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="<?php bloginfo('description'); ?>">
	<?php if ( is_singular() && pings_open( get_queried_object() ) ) : ?>
        <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	<?php endif; ?>
    <link rel="stylesheet" type="text/css" href="<?php bloginfo('url'); ?>/wp-content/lms-data/assets/scarlet/css/dynamic-wp-idea.min.css?v=1689846542&ver=6.2.2"/>
    

    <link rel="stylesheet" type="text/css" href="<?php bloginfo('url'); ?>/wp-content/plugins/wp-idea/templates/scarlet/assets/css/wp-idea.min.css?ver=6.2.2"/>

    <link rel="stylesheet" type="text/css" href="<?php bloginfo('url'); ?>/wp-content/plugins/easy-accordion-free/public/assets/css/font-awesome.min.css?ver=2.2.3"/>

    <link rel="stylesheet" type="text/css" href="<?php bloginfo('url'); ?>/wp-content/themes/mauricztv/style.css?v=<?php echo time(); ?>"/>


    <link rel="stylesheet" type="text/css" href="<?php echo get_template_directory_uri(); ?>/inc/assets/css/jquery-ui.css" />
   
    <!-- integracja klaviyo -->
    <!-- public key RAAZX9 -->
    <?php 
    // private key pk_788d358870622e5f3ba8afcea7d675dd02 
    ?>
    <script type="text/javascript" async=""
    src="https://static.klaviyo.com/onsite/js/RAAZX9/klaviyo.js"></script>
    <!-- end integracja klaviyo -->

	<?php wp_head(); ?>

</head>
<body <?php body_class( array( WPI()->templates->get_body_class() ) ); ?>>
<?php do_action( 'bpmj_eddc_after_body_open_tag' ); ?>

<?= App_View_API_Static_Helper::render_top_bar_navigation() ?>

<div id="page">
    <div id="header">
        <div class="contenter">
            <div class="row">
                <div class="col-sm-1" id="logo-cell">
					
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home">
                        <img src="<?php echo get_template_directory_uri(); ?>/img/logo.svg" alt="Mauricz TV">
                    </a>
                </div>
                <div class="col-sm-11">

                    
                    
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


                <div id="menu_mobile"><i class="fas fa-bars"></i></div>
                <div class="menu_glowne">
                    <?php echo WPI()->templates->get_main_menu( array(
                        'id' => 'menu',
                    ) ); ?>
                </div>
                <?php if ( $this->override_all && $this->templates_settings_handler instanceof New_Templates_System_Settings_Handler ): ?>
                    <div class="search navbar-search">
                        <a href="<?= get_home_url() . '?' . Search_Page_Template::SEARCH_PHRASE_QUERY_PARAM_NAME . '=' . Search_Page_Template::SEARCH_PHRASE_PLACEHOLDER ?>"> <i class="icon-search"></i></a>
                    </div>
                <?php endif; ?>
                <div class="koszyk">
                    <a href="<?php echo edd_get_checkout_uri(); ?>"><i class="icon-cart"></i></a>
                    <span class="dymek edd-cart-quantity"><?php echo edd_get_cart_quantity(); ?></span>
                </div>
            </div>
        </div>
    </div>
</div>
