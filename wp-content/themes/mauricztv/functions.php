<?php
/**
 * WP Bootstrap Starter functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package WP_Bootstrap_Starter
 */

if ( ! function_exists( 'wp_bootstrap_starter_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function wp_bootstrap_starter_setup() {

wp_enqueue_script(array( 'jquery', 'jquery-ui', 'jquery-ui-dialog', 'jquery-ui-slider'));
// wp_deregister_script('jquery');
// wp_enqueue_script('wp-bootstrap-starter-jquery', get_template_directory_uri() . '/inc/assets/js/jquery.js', array(), '', true );

	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on WP Bootstrap Starter, use a find and replace
	 * to change 'wp-bootstrap-starter' to the name of your theme in all the template files.
	 */
	load_theme_textdomain( 'wp-bootstrap-starter', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support( 'title-tag' );

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
	 */
	add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'primary' => esc_html__( 'Primary', 'wp-bootstrap-starter' ),
		'secondary' => esc_html__( 'Secondary', 'wp-bootstrap-starter' ),
	) );

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'comment-form',
		'comment-list',
		'caption',
	) );

	// Set up the WordPress core custom background feature.
	add_theme_support( 'custom-background', apply_filters( 'wp_bootstrap_starter_custom_background_args', array(
		'default-color' => 'ffffff',
		'default-image' => '',
	) ) );

	// Add theme support for selective refresh for widgets.
	add_theme_support( 'customize-selective-refresh-widgets' );

    function wp_boostrap_starter_add_editor_styles() {
        add_editor_style( 'custom-editor-style.css' );
    }
    add_action( 'admin_init', 'wp_boostrap_starter_add_editor_styles' );

}
endif;
add_action( 'after_setup_theme', 'wp_bootstrap_starter_setup' );


/**
 * Add Welcome message to dashboard
 */
function wp_bootstrap_starter_reminder(){
        $theme_page_url = 'https://afterimagedesigns.com/wp-bootstrap-starter/?dashboard=1';

            if(!get_option( 'triggered_welcomet')){
                $message = sprintf(__( 'Welcome to WP Bootstrap Starter Theme! Before diving in to your new theme, please visit the <a style="color: #fff; font-weight: bold;" href="%1$s" target="_blank">theme\'s</a> page for access to dozens of tips and in-depth tutorials.', 'wp-bootstrap-starter' ),
                    esc_url( $theme_page_url )
                );

                printf(
                    '<div class="notice is-dismissible" style="background-color: #6C2EB9; color: #fff; border-left: none;">
                        <p>%1$s</p>
                    </div>',
                    $message
                );
                add_option( 'triggered_welcomet', '1', '', 'yes' );
            }

}
add_action( 'admin_notices', 'wp_bootstrap_starter_reminder' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function wp_bootstrap_starter_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'wp_bootstrap_starter_content_width', 1170 );
}
add_action( 'after_setup_theme', 'wp_bootstrap_starter_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function wp_bootstrap_starter_widgets_init() {
    register_sidebar( array(
        'name'          => esc_html__( 'Sidebar', 'wp-bootstrap-starter' ),
        'id'            => 'sidebar-1',
        'description'   => esc_html__( 'Add widgets here.', 'wp-bootstrap-starter' ),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ) );
    register_sidebar( array(
        'name'          => esc_html__( 'Footer 1', 'wp-bootstrap-starter' ),
        'id'            => 'footer-1',
        'description'   => esc_html__( 'Add widgets here.', 'wp-bootstrap-starter' ),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ) );
    register_sidebar( array(
        'name'          => esc_html__( 'Footer 2', 'wp-bootstrap-starter' ),
        'id'            => 'footer-2',
        'description'   => esc_html__( 'Add widgets here.', 'wp-bootstrap-starter' ),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ) );
    register_sidebar( array(
        'name'          => esc_html__( 'Footer 3', 'wp-bootstrap-starter' ),
        'id'            => 'footer-3',
        'description'   => esc_html__( 'Add widgets here.', 'wp-bootstrap-starter' ),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ) );
}
add_action( 'widgets_init', 'wp_bootstrap_starter_widgets_init' );


/**
 * Enqueue scripts and styles.
 */
function wp_bootstrap_starter_scripts() {
	// load bootstrap css
    if ( get_theme_mod( 'cdn_assets_setting' ) === 'yes' ) {
        wp_enqueue_style( 'wp-bootstrap-starter-bootstrap-css', 'https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css' );
        wp_enqueue_style( 'wp-bootstrap-starter-fontawesome-cdn', 'https://use.fontawesome.com/releases/v5.15.1/css/all.css' );
    } else {
        wp_enqueue_style( 'wp-bootstrap-starter-bootstrap-css', get_template_directory_uri() . '/inc/assets/css/bootstrap.min.css' );
        wp_enqueue_style( 'wp-bootstrap-starter-fontawesome-cdn', get_template_directory_uri() . '/inc/assets/css/fontawesome.min.css' );
    }
	// load bootstrap css
	// load AItheme styles
	// load WP Bootstrap Starter styles
	wp_enqueue_style( 'wp-bootstrap-starter-style', get_stylesheet_uri() );
    if(get_theme_mod( 'theme_option_setting' ) && get_theme_mod( 'theme_option_setting' ) !== 'default') {
        wp_enqueue_style( 'wp-bootstrap-starter-'.get_theme_mod( 'theme_option_setting' ), get_template_directory_uri() . '/inc/assets/css/presets/theme-option/'.get_theme_mod( 'theme_option_setting' ).'.css', false, '' );
    }
    if(get_theme_mod( 'preset_style_setting' ) === 'poppins-lora') {
        wp_enqueue_style( 'wp-bootstrap-starter-poppins-lora-font', 'https://fonts.googleapis.com/css?family=Lora:400,400i,700,700i|Poppins:300,400,500,600,700' );
    }
    if(get_theme_mod( 'preset_style_setting' ) === 'montserrat-merriweather') {
        wp_enqueue_style( 'wp-bootstrap-starter-montserrat-merriweather-font', 'https://fonts.googleapis.com/css?family=Merriweather:300,400,400i,700,900|Montserrat:300,400,400i,500,700,800' );
    }
    if(get_theme_mod( 'preset_style_setting' ) === 'poppins-poppins') {
        wp_enqueue_style( 'wp-bootstrap-starter-poppins-font', 'https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700' );
    }
    if(get_theme_mod( 'preset_style_setting' ) === 'roboto-roboto') {
        wp_enqueue_style( 'wp-bootstrap-starter-roboto-font', 'https://fonts.googleapis.com/css?family=Roboto:300,300i,400,400i,500,500i,700,700i,900,900i' );
    }
    if(get_theme_mod( 'preset_style_setting' ) === 'arbutusslab-opensans') {
        wp_enqueue_style( 'wp-bootstrap-starter-arbutusslab-opensans-font', 'https://fonts.googleapis.com/css?family=Arbutus+Slab|Open+Sans:300,300i,400,400i,600,600i,700,800' );
    }
    if(get_theme_mod( 'preset_style_setting' ) === 'oswald-muli') {
        wp_enqueue_style( 'wp-bootstrap-starter-oswald-muli-font', 'https://fonts.googleapis.com/css?family=Muli:300,400,600,700,800|Oswald:300,400,500,600,700' );
    }
    if(get_theme_mod( 'preset_style_setting' ) === 'montserrat-opensans') {
        wp_enqueue_style( 'wp-bootstrap-starter-montserrat-opensans-font', 'https://fonts.googleapis.com/css?family=Montserrat|Open+Sans:300,300i,400,400i,600,600i,700,800' );
    }
    if(get_theme_mod( 'preset_style_setting' ) === 'robotoslab-roboto') {
        wp_enqueue_style( 'wp-bootstrap-starter-robotoslab-roboto', 'https://fonts.googleapis.com/css?family=Roboto+Slab:100,300,400,700|Roboto:300,300i,400,400i,500,700,700i' );
    }
    if(get_theme_mod( 'preset_style_setting' ) && get_theme_mod( 'preset_style_setting' ) !== 'default') {
        wp_enqueue_style( 'wp-bootstrap-starter-'.get_theme_mod( 'preset_style_setting' ), get_template_directory_uri() . '/inc/assets/css/presets/typography/'.get_theme_mod( 'preset_style_setting' ).'.css', false, '' );
    }
    //Color Scheme
    /*if(get_theme_mod( 'preset_color_scheme_setting' ) && get_theme_mod( 'preset_color_scheme_setting' ) !== 'default') {
        wp_enqueue_style( 'wp-bootstrap-starter-'.get_theme_mod( 'preset_color_scheme_setting' ), get_template_directory_uri() . '/inc/assets/css/presets/color-scheme/'.get_theme_mod( 'preset_color_scheme_setting' ).'.css', false, '' );
    }else {
        wp_enqueue_style( 'wp-bootstrap-starter-default', get_template_directory_uri() . '/inc/assets/css/presets/color-scheme/blue.css', false, '' );
    }*/


    // Internet Explorer HTML5 support
    wp_enqueue_script( 'html5hiv',get_template_directory_uri().'/inc/assets/js/html5.js', array(), '3.7.0', false );
    wp_script_add_data( 'html5hiv', 'conditional', 'lt IE 9' );

	// load bootstrap js
    if ( get_theme_mod( 'cdn_assets_setting' ) === 'yes' ) {
        wp_enqueue_script('wp-bootstrap-starter-popper', 'https://cdn.jsdelivr.net/npm/popper.js@1/dist/umd/popper.min.js', array(), '', true );
    	wp_enqueue_script('wp-bootstrap-starter-bootstrapjs', 'https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.min.js', array(), '', true );
    } else {
        wp_enqueue_script('wp-bootstrap-starter-popper', get_template_directory_uri() . '/inc/assets/js/popper.min.js', array(), '', true );
        wp_enqueue_script('wp-bootstrap-starter-bootstrapjs', get_template_directory_uri() . '/inc/assets/js/bootstrap.min.js', array(), '', true );
    }
    wp_enqueue_script('wp-bootstrap-starter-popper', get_template_directory_uri() . '/inc/assets/js/popper.min.js', array(), '', true );
    wp_enqueue_script('wp-bootstrap-starter-bootstrapjs', get_template_directory_uri() . '/inc/assets/js/bootstrap.min.js', array(), '', true );
    wp_enqueue_script('wp-bootstrap-starter-themejs', get_template_directory_uri() . '/inc/assets/js/theme-script.min.js', array(), '', true );
	wp_enqueue_script( 'wp-bootstrap-starter-skip-link-focus-fix', get_template_directory_uri() . '/inc/assets/js/skip-link-focus-fix.min.js', array(), '20151215', true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'wp_bootstrap_starter_scripts' );


/**
 * Add Preload for CDN scripts and stylesheet
 */
function wp_bootstrap_starter_preload( $hints, $relation_type ){
    if ( 'preconnect' === $relation_type && get_theme_mod( 'cdn_assets_setting' ) === 'yes' ) {
        $hints[] = [
            'href'        => 'https://cdn.jsdelivr.net/',
            'crossorigin' => 'anonymous',
        ];
        $hints[] = [
            'href'        => 'https://use.fontawesome.com/',
            'crossorigin' => 'anonymous',
        ];
    }
    return $hints;
} 

add_filter( 'wp_resource_hints', 'wp_bootstrap_starter_preload', 10, 2 );



function wp_bootstrap_starter_password_form() {
    global $post;
    $label = 'pwbox-'.( empty( $post->ID ) ? rand() : $post->ID );
    $o = '<form action="' . esc_url( home_url( 'wp-login.php?action=postpass', 'login_post' ) ) . '" method="post">
    <div class="d-block mb-3">' . __( "To view this protected post, enter the password below:", "wp-bootstrap-starter" ) . '</div>
    <div class="form-group form-inline"><label for="' . $label . '" class="mr-2">' . __( "Password:", "wp-bootstrap-starter" ) . ' </label><input name="post_password" id="' . $label . '" type="password" size="20" maxlength="20" class="form-control mr-2" /> <input type="submit" name="Submit" value="' . esc_attr__( "Submit", "wp-bootstrap-starter" ) . '" class="btn btn-primary"/></div>
    </form>';
    return $o;
}
add_filter( 'the_password_form', 'wp_bootstrap_starter_password_form' );



/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/extras.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load plugin compatibility file.
 */
require get_template_directory() . '/inc/plugin-compatibility/plugin-compatibility.php';

/**
 * Load custom WordPress nav walker.
 */
if ( ! class_exists( 'wp_bootstrap_navwalker' )) {
    require_once(get_template_directory() . '/inc/wp_bootstrap_navwalker.php');
}



add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_styles' );
function my_theme_enqueue_styles() {
	wp_enqueue_style('main-styles', get_template_directory_uri() . '/style.css', array(), filemtime(get_template_directory() . '/style.css'), false);	

}

add_shortcode('mjtest_shortcode', 'mjtest_shortcode');

function mjtest_shortcode() { 
     return "test mjshortcode: ". get_post_type( get_the_ID());
}

add_shortcode('mjloginpage', 'mjloginpage');
function mjloginpage() {
    $output = '<div class="container">';
    
    $output .= '<div class="row login-section">';

        $output .= '<div class="col-md-6">';
        
        $output .= wp_login_form( ['echo' =>false] );
        $output .= "</div>";

        $output .= '<div class="col-md-6">';
            $output .= '<div>';
                $output .=  '<h1>Zarejestruj się</h1>';
                $output .=  '<h3>Otrzymasz liczne dodatkowe korzyści</h3>';
                $output .=  '<ul>';

                    $output .=  '<li>';
                        $output .=  'dostęp do Twoich szkoleń';
                    $output .=  '</li>';
                    $output .=  '<li>';
                        $output .=  'podgląd historii zakupów';
                    $output .=  '</li>';
                    $output .=  '<li>';
                        $output .=  'brak konieczności wprowadzania swoich danych w kolejnych zakupach';
                    $output .=  '</li>';
                    $output .=  '<li>';
                        $output .=  'mozliwość otrzymania rabatów i kuponów promocyjnych';
                    $output .=  '</li>';

                $output .=  '</ul>';

                $output .= "<a href='wp-login.php?action=register' class='more btn btn-secondary register'>Zarejestruj się</a>";

            $output .= '</div>';

        $output .= "</div>";
    $output .= "</div>";
    $output .= "</div>";
    return $output;
}

/**
 * Przekierowanie do uzytkownikow
 */
function redirectFromStudentsToUsers() { 
    //redirect_login_page
    $url = basename($_SERVER['REQUEST_URI']);
    // echo $url;
    $users_url = admin_url( 'admin.php?page=wp-idea-users', '' );
    ;
    // exit();

    if(strpos($url ,'admin.php?page=wp-idea-students') !== false) {
        wp_redirect($users_url);
    }
}

// add_action('init','redirectFromStudentsToUsers');

function redirect_login_page() {
    $login_url  = home_url( '/logowanie' );
    $url = basename($_SERVER['REQUEST_URI']); // get requested URL
    isset( $_REQUEST['redirect_to'] ) ? ( $url   = "wp-login.php" ): 0; // if users ssend request to wp-admin
    //if((strpos($url, "wp-login.php") !== false) && $_SERVER['REQUEST_METHOD'] == 'GET')  {
    if((($url == "wp-login.php")) && $_SERVER['REQUEST_METHOD'] == 'GET')  {
        wp_redirect( $login_url );
        exit;
    }
    if((($url == "wp-login.php?loggedout=true&wp_lang=pl_PL")) && $_SERVER['REQUEST_METHOD'] == 'GET')  {
        wp_redirect( $login_url );
        exit;
    }
}
add_action('init','redirect_login_page');
function redirectToLoginIfGuest() { 
    $backToLogin = home_url( '/logowanie' );
 
    $url = basename($_SERVER['REQUEST_URI']);
    // echo $url;
    // exit();
    if(((($url == "zamowienie")) && $_SERVER['REQUEST_METHOD'] == 'GET') && !is_user_logged_in())  {
        wp_redirect( $backToLogin );
        exit;
    }
}
#add_action('init','redirectToLoginIfGuest');


add_action( 'post_updated', 'updateAuthorCourse' );
/**
 * Zapis autora opisu z kursu zapisuje równiez autora samego kursu
 */

function updateAuthorCourse() {
    $id_post = get_the_ID();
    
    if(isset($_POST['post_author_override'])) {
        if(!empty($_POST['post_author_override'])) {

            try {
            global $wpdb;


            $getCourse = $wpdb->get_row("select post_id,meta_key, meta_value from $wpdb->postmeta where meta_key = 'product_id' AND meta_value  = '".$id_post."'");


            $getCourseID = $getCourse->post_id;
            
            if($getCourseID) { 
            
                $wpdb->query( $wpdb->prepare(
                    "
                    UPDATE $wpdb->posts
                    SET post_author = %d
                    WHERE ID = %s
                    ",
                    $_POST['post_author_override'], $getCourseID
                    ) );
                }
          
            } catch(\Exception $e) {
                return false;
            }
            //     exit();
        }
    }
}

/**
 * Weryfikacja czy konto jest aktywne
 */

//remove wordpress authentication
remove_filter('authenticate', 'wp_authenticate_username_password', 20);

 
 add_filter('authenticate',  function($user, $username, $password) {
    // $email = sanitize_user($email);
    // $password = trim($password);
    
    $_SESSION['user_confirm'] = '';
    $_SESSION['inacvite_user'] = '';
    $_SESSION['authentication_failed'] = '';

    $userLogin = get_user_by('login', $username);
    $userEmail = get_user_by('email', $username);

    if(($userLogin) || ($userEmail)) { 

        if($userLogin) { 
            // echo "obiekt usera poprzez login";
            $user = $userLogin;
        } else {
            // echo "obiekt usera poprzez email";
            $user = $userEmail;
        }
        
        // print_r($user);
        // echo "znaleziono uzytkownika";
        /**
         * Sprawdź czy uzytkownik ma aktywowane konto 
         */

          if ( get_user_meta( $user->ID, 'has_to_be_activated', true ) != false ) {
     
                $_SESSION['inacvite_user'] = '1';
                $user = new WP_Error('inacvite_user', __('<strong>ERROR</strong>: User is not activated.'));
                return $user;
            } else { 
                if(!wp_check_password($password, $user->user_pass, $user->ID)){ //bad password
                    $_SESSION['authentication_failed'] = '1';
                    $user = new WP_Error('authentication_failed', __('<strong>ERROR</strong>: Invalid username or incorrect password.'));
                    return $user;
                } else{
                    return $user; //passed
                }
            }
        
    } else {
        $_SESSION['authentication_failed'] = '1';
        $user = new WP_Error('authentication_failed', __('<strong>ERROR</strong>: Invalid username or incorrect password.'));
    }

    return $user;
     
    }, 20, 3);
 

/**
 * Aktywacja konta uzytkownika
 */

add_action( 'template_redirect', 'wpse8170_activate_user' );
function wpse8170_activate_user() {
    if ( is_page() && get_the_ID() ==  (int)get_option('mauricz_activation_page') ) {

        $user_id = filter_input( INPUT_GET, 'user', FILTER_VALIDATE_INT, array( 'options' => array( 'min_range' => 1 ) ) );
        if ( $user_id ) {
            // echo "OKKKK ". $user_id;
            // get user meta activation hash field
            $code = get_user_meta( $user_id, 'has_to_be_activated', true );
            // echo "blaa ". print_r(get_user_meta( $user_id, 'user_email'));
            // echo " code ".$code . ' vs '.filter_input( INPUT_GET, 'key' );


            if ( $code == filter_input( INPUT_GET, 'key' ) ) {
                delete_user_meta( $user_id, 'has_to_be_activated' );
                // echo "odblokowano";
                update_user_meta($user_id, "role", "subscriber");
            } else {
                // Token w requescie nie zgadza sie z tokenem w meta_value
                wp_redirect(get_permalink((int)get_option('mauricz_error_activation_page'))); 
            }
        }
    } else {
        // echo "Nieprawidłowy url". (int)get_option('mauricz_activation_page');
    }
}


// Przekierowanie użytkownika po zalogowaniu z parametrem w URL
add_action('wp_login', 'redirect_after_login', 10, 2);
function redirect_after_login($user_login, $user) {
    if (edd_get_cart_quantity() > 0) {
        // Przekieruj użytkownika na stronę główną z parametrem w URL
        $redirect_to = home_url('/?show_cart_popup=1');
        wp_safe_redirect($redirect_to);
        exit();
    }
}

add_action('wp_footer', 'show_popup_on_login_redirect');
function show_popup_on_login_redirect() {
    if (isset($_GET['show_cart_popup']) && $_GET['show_cart_popup'] == 1) {
        ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Wyświetl popup i backdrop
                document.getElementById('cartPopup').style.display = 'block';
                document.getElementById('backdrop').style.display = 'block';

                // Funkcja usuwająca parametr show_cart_popup=1 z URL
                function removePopupParamFromURL() {
                    const url = new URL(window.location.href);
                    url.searchParams.delete('show_cart_popup');
                    window.history.replaceState({}, document.title, url);
                }

                // Zamknięcie popupu i usunięcie backdropu
                function closePopup() {
                    document.getElementById('cartPopup').style.display = 'none';
                    document.getElementById('backdrop').style.display = 'none';
                    removePopupParamFromURL();
                }

                // Nasłuchuj kliknięcia przycisku "Zamknij" oraz kliknięcia w backdrop
                document.getElementById('closePopupButton').addEventListener('click', closePopup);
                document.getElementById('backdrop').addEventListener('click', closePopup);
            });
        </script>
        <?php
    }
}

// Wyświetl popup dla zalogowanego użytkownika przy ponownym wejściu na stronę (z ciasteczkiem)
add_action('wp_loaded', 'check_cart_on_page_load');
function check_cart_on_page_load() {
    if (is_user_logged_in() && edd_get_cart_quantity() > 0 && empty($_COOKIE['cartPopupShown'])) {
        setcookie('cartPopupShown', 'true', time() + DAY_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN);

        // Dodaj skrypt wyświetlający popup
        add_action('wp_footer', 'show_popup_script');
    }
}

// Funkcja generująca skrypt do pokazania popupu przy ponownym wejściu na stronę
function show_popup_script() {
    ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('cartPopup').style.display = 'block';
        });
    </script>
    <?php
}

// Ustaw ciasteczko przy dodaniu przedmiotu do koszyka, aby blokować popup
add_action('edd_post_add_to_cart', 'set_cart_popup_cookie');
function set_cart_popup_cookie() {
    if (is_user_logged_in()) {
        setcookie('cartPopupShown', 'true', time() + DAY_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN);
    }
}

/**
 * Sprawdzenie czy event istnieje
 */

 function klavyioGetEvent($id_event) { 
    // curl --request GET \
    //  --url https://a.klaviyo.com/api/events/id \
    //  --header 'Authorization: Klaviyo-API-Key your-private-api-key' \
    //  --header 'accept: application/vnd.api+json' \
    //  --header 'revision: 2025-01-15'
    try { 
       
            $c = curl_init();
            curl_setopt($c, CURLOPT_URL, 'https://a.klaviyo.com/api/events/a3998200-efaa-11ef-8001-502d6aca6fca');//.trim(($id_event)));

            // date revision
            $revision = get_option('mauricz_klavyio_api_revision');//'2025-01-15';

            $KlaviyoPrivateKey = get_option('mauricz_klavyio_api_private_key'); //'pk_788d358870622e5f3ba8afcea7d675dd02';
            $head[] ='Authorization: Klaviyo-API-Key '.$KlaviyoPrivateKey.'';
            $head[] ='accept: application/json';
            $head[] ='revision: '.$revision;
            curl_setopt($c, CURLOPT_HTTPHEADER, $head);
            curl_setopt($c, CURLOPT_POST, false);
            curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
                
            //json_request
            $result =  @json_decode(curl_exec($c), 1);

            print_r($result);
    } catch(\Exception $e) {
        #return false;
    }
 }

//  function klavyioStart
/**
 * Wywołanie akcji przekaznia zadarzenia do Klaviyo z zdarzeniem dodania zamówienia
 * @param string $type ['Placed Order', ]
 * @return bool $result
 */

 //edd_purchase
 //edd_checkout_before_gateway
 //edd_update_payment_details //$data['edd_payment_id']
 //edd_checkout_before_gateway 
 add_action( 'edd_payment_receipt_after', 'klavyioPostEvents', 10, 2 );
// edd_update_payment_status
 //$type = 'Placed Order', $id_order = 0
 function klavyioPostEvents( $payment) {
    try  {
        //    print_r($payment);
        //    exit();
        /**
         * Pobierz obiekt zamówienia
         */ 
        $args = [
			'number' => '1',
			// 'status' => 'publish',
            'post__in' => [$payment->ID], // 49841 49841 49863
            // 'orderby'    => 'payment_id',
			// 'order'      => 'DESC',
            // 'user'       => $payment['edd-user-id'],
			'output'     => 'payments',
			'orderby'    => 'date',
			'order'      => 'DESC',
			/*'date_query' => array(
				array(  
                    'after'     => '-3 months',
					// 'before'    => '+5 years',
				// 	'after'     => $_POST['raport_sprzedazy_option_name']['raport_data_od'],
				// 	'before'    => $_POST['raport_sprzedazy_option_name']['raport_data_do'],
				),
			),
            */
			
		];
        $getPayments = edd_get_payments($args);

        // print_r($getPayments[0]);
        // exit();
        $orders = getOrders($getPayments);
        // echo "A";
        //     print_r($orders);
        // exit();
        $json=[];

        /**
         * Ziteruj zamówienia w tablicy
         */
        foreach($orders as $key => $order)  {

            try {
            $json['data']['type'] = "event";
            $json['data']['attributes']['properties']['OrderId'] = $order['id'];

            $productNames = [];
            $productCategories = [];
            
            $productItems = [];
            foreach($order['products'] as $position => $item) {
                $productNames[$position] = $item['name'];

                /**
                 * Categories
                 */
                $categories_terms = get_the_terms($item['id'],  'download_category', true);
                
                $productCategoriesItem = [];
                foreach($categories_terms as $term) {
                    if(!in_array($term->name, $productCategories)) {
                        $productCategories[] = $term->name;
                    }
                    $productCategoriesItem[] = $term->name;
                }

                /**
                 * Thumbnail
                 */
                if(!empty(get_the_post_thumbnail_url($item['id']))) { 
                    $thumbnail = get_the_post_thumbnail_url($item['id']);
                } else {
                    $thumbnail =  get_template_directory_uri()."/img/logo.svg";
                }

                $productItems[$position]['ProductID'] = $item['id'];
                $productItems[$position]['SKU'] = $item['id'];
                $productItems[$position]['ProductName'] = $item['name'];
                $productItems[$position]['Quantity'] = 1;
                $productItems[$position]['ItemPrice'] = $item['amount'];
                $productItems[$position]['RowTotal'] = $item['amount'];
                $productItems[$position]['ImageURL'] = $thumbnail;
                $productItems[$position]['ProductURL'] = get_the_permalink($item['id']);
                $productItems[$position]['Categories'] = (array)$productCategoriesItem;
                $productItems[$position]['Brand'] = "Mauricz";
            }
        
           
            // Properties
            $json['data']['attributes']['properties']['Categories'] = (array)$productCategories;

            $json['data']['attributes']['properties']['ItemNames'] = (array)$productNames;

            $json['data']['attributes']['properties']['Brands'] = ["Mauricz"];

            //bpmj_eddcm_phone_no
            if(!empty($order['phone'])) {
                $phone = $order['phone'];
                if (strpos($phone, "+48") === 0) {
                } else {
                    $phone = "+48".$phone;
                }
            } else {
                $phone = '+48000000000';
            }
           
            //Items
            $json['data']['attributes']['properties']['Items'] = (array)$productItems;

            
            //BillingAddress
            $json['data']['attributes']['properties']['BillingAddress'] = [
                "FirstName" => $order['first'],
                "LastName" =>  $order['last'],
                "Address1" => $order['address1'],
                "City" => $order['city'],
                "RegionCode" => "",
                "CountryCode" => "",
                "Zip" =>  $order['zip'],
                "Phone" => $phone
            ];

            //ShippingAddress
            $json['data']['attributes']['properties']['ShippingAddress'] = [
                "FirstName" =>  $order['first'],
                "LastName" =>  $order['last'],
                "Address1" => $order['address1'],
                "City" => $order['city'],
                "RegionCode" => "",
                "CountryCode" => "",
                "Zip" => $order['zip'],
                "Phone" => $phone
            ];

            // tim / value / value_currency / unique_id
            $json['data']['attributes']['time'] = date('c', strtotime($order['date']));//date('c', strtotime($order['date']));//'2022-11-08T00:00:00';
            $json['data']['attributes']['value'] =  $order['amount'];
            $json['data']['attributes']['value_currency'] = 'PLN';
            $json['data']['attributes']['unique_id'] = md5($order['id']);//'d47aeda5-1751-4483-a81e-6fcc8ad48711';

            // if(!empty($order['discount'])) {
            //     $json['data']['attributes']['properties']['DiscountCode'] = [$order['discount']];
            //     $json['data']['attributes']['properties']['DiscountValue'] = 0;
            // } else {
            //     $json['data']['attributes']['properties']['DiscountCode'] = [];
            //     $json['data']['attributes']['properties']['DiscountValue'] = 0;
            // }
           
            // Mertic
            $json['data']['attributes']['metric']['data']['type'] = 'metric';
            $json['data']['attributes']['metric']['data']['attributes']['name'] = 'Placed Order';

            // Profile
            $json['data']['attributes']['profile']['data']['type'] = 'profile';
            $json['data']['attributes']['profile']['data']['attributes']['email'] = $order['email'];
            $json['data']['attributes']['profile']['data']['attributes']['phone_number'] = $phone;
        
        
                    

            $json_request = json_encode($json);

//             $json_request = '{
//     "data": {
//         "type": "event",
//         "attributes": {
//             "properties": {
//                 "OrderId": "22",
//                 "Categories": [
//                     "Fiction",
//                     "Classics",
//                     "Children"
//                 ],
//                 "ItemNames": [
//                     "Winnie the Pooh",
//                     "A Tale of Two Cities"
//                 ],
//                 "DiscountCode": "Free Shipping",
//                 "DiscountValue": 5,
//                 "Brands": [
//                     "Kids Books",
//                     "Harcourt Classics"
//                 ],
//                 "Items": [
//                     {
//                         "ProductID": "1111",
//                         "SKU": "WINNIEPOOH",
//                         "ProductName": "Winnie the Pooh",
//                         "Quantity": 1,
//                         "ItemPrice": 9.99,
//                         "RowTotal": 9.99,
//                         "ProductURL": "http://www.example.com/path/to/product",
//                         "ImageURL": "http://www.example.com/path/to/product/image.png",
//                         "Categories": [
//                             "Fiction",
//                             "Children"
//                         ],
//                         "Brand": "Kids Books"
//                     },
//                     {
//                         "ProductID": "1112",
//                         "SKU": "TALEOFTWO",
//                         "ProductName": "A Tale of Two Cities",
//                         "Quantity": 1,
//                         "ItemPrice": 19.99,
//                         "RowTotal": 19.99,
//                         "ProductURL": "http://www.example.com/path/to/product2",
//                         "ImageURL": "http://www.example.com/path/to/product/image2.png",
//                         "Categories": [
//                             "Fiction",
//                             "Classics"
//                         ],
//                         "Brand": "Harcourt Classics"
//                     }
//                 ],
//                 "BillingAddress": {
//                     "FirstName": "John",
//                     "LastName": "Smith",
//                     "Address1": "123 Abc St",
//                     "City": "Boston",
//                     "RegionCode": "MA",
//                     "CountryCode": "US",
//                     "Zip": "02110",
//                     "Phone": "+15551234567"
//                 },
//                 "ShippingAddress": {
//                     "Address1": "123 Abc St"
//                 }
//             },
//             "time": "2025-02-18T00:00:00",
//             "value": 29.98,
//             "value_currency": "USD",
//             "unique_id": "d47aeda5-1751-4483-a81e-6fcc8ad48711",
//             "metric": {
//                 "data": {
//                     "type": "metric",
//                     "attributes": {
//                         "name": "Placed Order"
//                     }
//                 }
//             },
//             "profile": {
//                 "data": {
//                     "type": "profile",
//                     "attributes": {
//                         "email": "sarah.mason@klaviyo-demo.com",
//                         "phone_number": "+15005550006"
//                     }
//                 }
//             }
//         }
//     }
// }';

            $c = curl_init();
            curl_setopt($c, CURLOPT_URL, 'https://a.klaviyo.com/api/events/');

            // date revision
            $revision = get_option('mauricz_klavyio_api_revision');//'2025-01-15';

            $KlaviyoPrivateKey = get_option('mauricz_klavyio_api_private_key');//'pk_788d358870622e5f3ba8afcea7d675dd02';
            $head[] ='Authorization: Klaviyo-API-Key '.$KlaviyoPrivateKey.'';
            $head[] ='accept: application/json';
            $head[] ='content-Type: application/json';
            $head[] ='revision: '.$revision;
            curl_setopt($c, CURLOPT_HTTPHEADER, $head);
            curl_setopt($c, CURLOPT_POST, true);
            curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($c, CURLOPT_POSTFIELDS, $json_request);
                
            //json_request
            $result =  @json_decode(curl_exec($c),1);

            // echo $json_request2;
            // echo "<br/>";
            // echo "<br/>";
            // echo $json_request;
            // echo "<br/>";
            // echo "<br/>";
            // print_r($result);
            // echo "<br/>";
            // echo $phone;
            // exit();
            return true;
        
            } catch(\Exception $e) {
                continue;
            }
        
        }

    } catch(\Exception $e) {

    }
    
 }


 /**
  * Pobranie eventów klavyio
  * @return array|bool $result
  */
 function klavyioGetEvents() {
    try {

        /**
         * Zwróć odpowiedź  po api z klavyio
         */
            try {
                $c = curl_init();
                // curl_setopt($c, CURLOPT_URL, 'https://a.klaviyo.com/api/events/1234');
                curl_setopt($c, CURLOPT_URL, 'https://a.klaviyo.com/api/events');

                $revision = get_option('mauricz_klavyio_api_revision');//'2025-01-15';
                $KlaviyoPrivateKey = get_option('mauricz_klavyio_api_private_key');
                $head[] ='Authorization: Klaviyo-API-Key '.$KlaviyoPrivateKey.'';
                $head[] ='accept: application/json';
                $head[] ='revision: '.$revision;//.date('Y-m-d');
                curl_setopt($c, CURLOPT_HTTPHEADER, $head);
                curl_setopt($c, CURLOPT_POST, false);
                curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
                
                $result =  @json_decode(curl_exec($c),1);

                print_r($result);
            } catch(\Exception $e) {
            }
            echo "======";
            echo "GET";
        exit();

    } catch(\Exception $e) {

    }
 }

 /**
  * Pobranie danych zamówienia
  * @param array $payments
  * @return array $data
  */
 function getOrders($payments) {
     
    $data = [];
    if( $payments ) {

        foreach ( $payments as $payment ) {
            $payment_meta   = edd_get_payment_meta( $payment->ID );
            $user_info      = edd_get_payment_meta_user_info( $payment->ID );
            $downloads      = edd_get_payment_meta_cart_details( $payment->ID );
            $total          = edd_get_payment_amount( $payment->ID );
            $user_id        = isset( $user_info['id'] ) && $user_info['id'] != -1 ? $user_info['id'] : $user_info['email'];
            $products       = [];
            $skus           = '';

            if ( $downloads ) {
                foreach ( $downloads as $key => $download ) {

                    // Download ID
                    $id  = isset( $payment_meta['cart_details'] ) ? $download['id'] : $download;
                    $qty = isset( $download['quantity'] ) ? $download['quantity'] : 1;

                    if ( isset( $download['price'] ) ) {
                        $price = $download['price'];
                    } else {
                        // If the download has variable prices, override the default price
                        $price_override = isset( $payment_meta['cart_details'] ) ? $download['price'] : null;
                        $price = edd_get_download_final_price( $id, $user_info, $price_override );
                    }

                    // Display the Downoad Name
                    $products[$key]['id'] = $id ;

                    $products[$key]['name'] = html_entity_decode( get_the_title( $id ) );

                    $products[$key]['qty'] = $qty;

                    $products[$key]['amount'] = $price;
                    
                    $products[$key]['price'] = html_entity_decode( edd_currency_filter( edd_format_amount( $price ) ) );

                }
            }

            if ( is_numeric( $user_id ) ) {
                $user = get_userdata( $user_id );
            } else {
                $user = false;
            }

            $eddcm_purchase_data = edd_get_payment_meta( $payment->ID, 'bpmj_eddcm_purchase_data' );

            // print_r($eddcm_purchase_data);
            // echo "<br/><br>===<br/><br>";
            // print_r(edd_get_payment_meta_user_info( $payment->ID ));
            $customerName = @explode(" ",@$payment_meta['bpmj_edd_invoice_person_name']);

            $data[] = array(
                'id'       => $payment->ID,
                'id_customer' => $payment->post_author,
                'seq_id'   => edd_get_payment_number( $payment->ID ),
                'email'    => $payment_meta['email'],
                'first'    => @$customerName[0],
                'last'     => @$customerName[1],
                'address1' => @$payment_meta['bpmj_edd_invoice_street']. ' '.@$payment_meta['bpmj_edd_invoice_building_number'],
                'address2' => @$payment_meta['bpmj_edd_invoice_apartment_number'], //isset( $user_info['address']['line2'] )   ? $user_info['address']['line2']   : '',
                'city'     => @$payment_meta['bpmj_edd_invoice_city'], //isset( $payment->bpmj_edd_invoice_city )    ?  $payment->bpmj_edd_invoice_city     : '',
                'state'    => isset( $user_info['address']['state'] )   ? $user_info['address']['state']   : '',
                'country'  => @$payment_meta['bpmj_edd_invoice_country'], //isset( $user_info['address']['country'] ) ? $user_info['address']['country'] : '',
                'zip'      => @$payment_meta['bpmj_edd_invoice_postcode'], //isset( $user_info['address']['zip'] )     ? $user_info['address']['zip']     : '',
                'phone' => (string)$eddcm_purchase_data['bpmj_eddcm_phone_no'],
                'products' => $products,
                'amount'   => html_entity_decode( edd_format_amount( $total ) ), // The non-discounted item price
                'tax'      => html_entity_decode( edd_format_amount( edd_get_payment_tax( $payment->ID, $payment_meta ) ) ),
                'discount' => isset( $user_info['discount'] ) && $user_info['discount'] != 'none' ? $user_info['discount'] : '',
                'gateway'  => edd_get_gateway_admin_label( get_post_meta( $payment->ID, '_edd_payment_gateway', true ) ),
                'trans_id' => edd_get_payment_transaction_id( $payment->ID ),
                'key'      => $payment_meta['key'],
                'date'     => @$payment_meta['date'],//$payment->post_date,
                'user'     => $user ? $user->display_name : __( 'guest', 'easy-digital-downloads' ),
                'status'   => edd_get_payment_status( $payment, true )
            );

        }

    }
        return (array)$data;
 }

 /**
  * Zwróć cenę regularną/po promocji dla kursu/pakietu
  * @param int $product_id
  * @param bool $html
  */
  function getPricesFromCourses($product_id, $html = true) {

    $response = [];
    if((date('Y-m-d') >= $sale_price_from_date) && (date('Y-m-d') < $sale_price_to_date)) { 
        if(!is_numeric(get_post_meta($product_id,  'sale_price', true))) {
            if($html == true) {
                ?>
                    <h4 class="product-price"><?php echo number_format(get_post_meta($product_id,  'edd_price', true),2,'.',''); ?> PLN</h4>    
                <?php
            } else {
                $response['price'] = number_format(get_post_meta($product_id,  'edd_price', true),2,'.',''); 
            }
        } else {
        ?>
        <?php 
        if($html == true) {
            echo "<h4 class='product-price sale'>";
            echo number_format(get_post_meta($product_id,  'sale_price', true),2,'.','');
            echo " PLN</h4>";
        } else {
            $response['price'] = number_format(get_post_meta($product_id,  'sale_price', true),2,'.','');
        }
    ?>
    
    <h4 class="crossed"><?php echo number_format(get_post_meta($product_id,  'edd_price', true),2,'.',''); ?> PLN</h4>
    
    <?php 
     if(!$html) {
            $response['regular_price'] = number_format(get_post_meta($product_id,  'edd_price', true),2,'.','');
        }
    }
    
    } else { 
    if((@get_post_meta($product_id,  'edd_sale_price', true)  > 0) && (get_post_meta($product_id,  'edd_sale_price', true) != @get_post_meta($product_id,  'edd_price', true))) {
        if($html == true) {
        ?>
        
            <h4 class="product-price sale"><?php echo number_format(get_post_meta($product_id,  'edd_sale_price', true),2,'.',''); ?> PLN</h4>
            <h4 class="crossed"><?php echo get_post_meta($product_id,  'edd_price', true); ?> PLN</h4>
        
        <?php 
        } else {
            $response['price'] = number_format(get_post_meta($product_id,  'edd_sale_price', true),2,'.','');
            $response['regular_price'] = number_format(get_post_meta($product_id,  'edd_price', true),2,'.','');
        }
    } else { 
            if($html == true) {
                echo "<h4 class='product-price'>";
                echo number_format(get_post_meta($product_id,  'edd_price', true),2,'.','');
                echo " PLN</h4>";
            } else {
                $response['price'] = number_format(get_post_meta($product_id,  'edd_price', true),2,'.','');
            }
    } 	 
    }
    return json_encode($response);
  }

  function add_gdpr_checkbox_to_tml_register_form() {
	tml_add_form_field( 'register', 'accept_terms', array(
		'type' => 'checkbox',
		'label' => 'Zapoznałem/-łam się z <a href="/regulamin">Regulaminem</a> serwisu i akceptuję jego treść.<span class="text-red">*</span>',
		'value' => '1',
		'checked' => tml_get_request_value( 'accept_terms', 'post' ),
		'priority' => 30,
	) );
}
add_action( 'init', 'add_gdpr_checkbox_to_tml_register_form' );

function validate_gdpr_checkbox_on_registration( $errors ) {
	if ( ! tml_get_request_value( 'accept_terms', 'post' ) ) {
		$errors->add( 'accept_terms', '<strong>Błąd</strong>: Musisz zaakceptować Regulamin serwisu' );
	}
	return $errors;
}
add_filter( 'registration_errors', 'validate_gdpr_checkbox_on_registration' );

function save_gdpr_checkbox_on_registration( $user_id ) {
	if ( tml_get_request_value( 'accept_terms', 'post' ) ) {
		update_user_meta( $user_id, 'accepted_terms', 1 );
	}
}
add_action( 'user_register', 'save_gdpr_checkbox_on_registration' );

/**
 * Dodanie customowego pola do checkoutu mauricz.tv
 */

 function addCustomChecboxToCheckoutMauricz() { 
//  <fieldset id="edd_agree_marketing_klavyio">
    $output = '';
    $output .= '
  
            <div class="edd_agree_marketing_klavyio">
                <input name="edd_agree_marketing_klavyio"  type="checkbox" id="edd_agree_marketing_klavyio" value="1">
                <label for="edd_agree_marketing_klavyio"> Wyrażam zgodę na otrzymywanie drogą elektroniczną na wskazany przeze mnie adres email informacji handlowej w rozumieniu art. 10 ust. 1 ustawy z dnia 18 lipca 2002 roku o świadczeniu usług drogą elektroniczną od Mauricz.tv</label>
            </div>
    ';
    //</fieldset>'

    echo $output;
   
 }

add_action('edd_purchase_form_before_submit', 'addCustomChecboxToCheckoutMauricz', 1);

function addCustomFooterInfoToCheckoutMauricz() {
    echo '<div class="tml-field-wrap tml-info_required_field-wrap"><p id="info_required_field"><span class="text-red">*</span> Pole wymagane</p></div>';

}
 add_action('edd_purchase_form_before_submit', 'addCustomFooterInfoToCheckoutMauricz', 9992);



/**
 * Hook action processCheckboxKlavyioCheckout
 * @return bool
 */
function processCheckboxKlavyioCheckout() {
    try {   
        if(@$_POST['edd_agree_marketing_klavyio'] == '1') { 
            /**
             * Zasubskrybuj w klavyio
             */
            try { 
                // 		curl --request POST \
                //      --url https://a.klaviyo.com/api/profile-subscription-bulk-create-jobs/ \

                $c = curl_init();
                curl_setopt($c, CURLOPT_URL, 'https://a.klaviyo.com/api/profile-subscription-bulk-create-jobs/');//.trim(($id_event)));
    
                // date revision
                $revision = get_option('mauricz_klavyio_api_revision');//'2025-01-15';
    
                $KlaviyoPrivateKey = get_option('mauricz_klavyio_api_private_key'); //'pk_788d358870622e5f3ba8afcea7d675dd02';
                $head[] ='Authorization: Klaviyo-API-Key '.$KlaviyoPrivateKey.'';
                $head[] ='Content-Type:application/json';
                $head[] ='accept: application/json';
                $head[] ='revision: '.$revision;
                curl_setopt($c, CURLOPT_HTTPHEADER, $head);
                curl_setopt($c, CURLOPT_POST, true);
                curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
                
                $json_request = [
                    "data" => [
                        "type" => "profile-subscription-bulk-create-job",
                        "attributes" => [
                            "profiles" => [
                                "data" => [
                                    [
                                        "type" => "profile",
                                        "attributes" => [
                                            "email" => $_POST['edd_email'],
                                            "subscriptions" => [
                                                "email" => [
                                                    "marketing" => [
                                                        "consent" => "SUBSCRIBED"
                                                    ]
                                                ],
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ];

                    
                curl_setopt($c, CURLOPT_POSTFIELDS, json_encode($json_request));

                $result =  @json_decode(curl_exec($c), 1);
    
            } catch(\Exception $e) {
            }
        } else { 
            /**
             * Nic nie rób
             */
        }
            // print_r($_POST);
            // exit();
    } catch(\Exception $e) {

    }
}
add_action('edd_checkout_before_gateway', 'processCheckboxKlavyioCheckout');
 //edd_checkout_before_gateway
require_once('slwn_functions.php');
