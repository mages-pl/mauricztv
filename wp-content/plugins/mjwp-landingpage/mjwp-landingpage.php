<?php
/**
 * Plugin Name: Landing Page dla Mauricz
 * Description: Wtyczka dodająca landing page z darmowym szkoleniem dla Mauricz
 * Version: 1.0
 * Author: Michał Jendraszczyk
 */


 if (!defined('ABSPATH')) {
    exit; // Bezpośredni dostęp zabroniony
}

class MjwpLandingpage {
    private static $instance = null;
    private $custom_template = 'mjwp-landingpage-template.php'; //szablon
 
    public static $id_landing_page = 49895; // id strony z landingiem

    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_filter('template_include', [$this, 'override_template']);
        add_filter( 'admin_init' , array( &$this , 'register_fields' ) );

        if(!empty(get_option('MjwpLandingpage_id_page'))) {
                self::$id_landing_page = get_option('MjwpLandingpage_id_page');
        }
    }

    public function override_template($template) {
        global $post;
        
        // Sprawdzenie, czy to jest konkretna strona ID: 49895
        if (is_page(MjwpLandingpage::$id_landing_page)) {
           
            $custom_template_path = plugin_dir_path(__FILE__) .'views/'. $this->custom_template;
          
            if (file_exists($custom_template_path)) {
                return $custom_template_path;
            }
        }
        return $template;
    }
}

MjwpLandingpage::get_instance();


function register_fields() {
        /**
         * ID strony która ma być skojarzona jako ta z darmowym kursem
         */

        register_setting( 'general', 'mjwp_landingpage_free_course', 'esc_attr' );

        add_settings_field('mjwp_landingpage_free_course', '<label for="mjwp_landingpage_free_course">'.__('Podaj ID strony z darmowym szkoleniem' , 'mjwp_landingpage_free_course' ).'</label>' , array(&$this, 'mjwp_landingpage_free_course') , 'general' );

}
/**
 * Rejestracja zmiennych dla widoku
 * @param array $vars
 */
function add_custom_variables($vars) {
   
    if (is_page(MjwpLandingpage::$id_landing_page)) {
        
        $getProduct = get_post( (int)get_option('mauricz_product_free_course') );

        set_query_var('sale_price', number_format(get_post_meta((int)get_option('mauricz_product_free_course'), 'sale_price', true), 2, '.', ''));
    
        set_query_var('regular_price', number_format(get_post_meta((int)get_option('mauricz_product_free_course'), 'edd_price', true), 2, '.', ''));

       
        set_query_var('flash_messages', get_query_var('messages'));
        set_query_var('product', $getProduct);
    }
}

/**
 * Metoda weryfikująca recaptcha v2
 * @param array $request
 * @return bool $result
 */
function recaptchaVerify($request) {
    $gcaptcha = $request['g-recaptcha-response']; 
    $googleVerificationUrl = 'https://www.google.com/recaptcha/api/siteverify';
        $googleSec = '6LfInJcqAAAAAB7HG6Xkyjm849Yd_rCJL2pZqsXV';
       
        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => $googleVerificationUrl,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => [
                'secret' => $googleSec,
                'response' => $gcaptcha,
                'remoteip' => $_SERVER['REMOTE_ADDR']
            ],
            CURLOPT_RETURNTRANSFER => true
        ]);

        $output = curl_exec($ch);
        curl_close($ch);            
        
        $googleResponseObj = json_decode($output);
        if($googleResponseObj->success !== true)
        {
            return false;
        } else { 
            return true;
        }
}

/**
 * Stworzenie obiektu usera
 * @param string $name
 * @param string $password
 * @param string $email
 * @return object $user
 */
function createUser($name, $password, $email) { 
    $user = wp_insert_user( array(
        'user_login' => $email,
        'user_pass' => $password,
        'user_email' => $email,
        'first_name' => $name,
        'last_name' => '',
        'display_name' =>  $name,
        'role' => 'subscriber'
       ));


        if( is_numeric( $user ) ) {
            $getUser = get_userdata( $user );
        } elseif ( is_email( $user ) ) {
            $getUser = get_user_by( 'email', $user );
        } elseif ( is_string( $user ) ) {
            $getUser = get_user_by( 'login', $user );
        } else {
            return;
        }
        
        try {
            // echo "id".$getUser->ID;
            // exit();
            tml_send_new_user_notifications( $getUser->ID, $notify = 'user' );
            #wp_new_user_notification($getUser->ID, null, 'user');
            // add_action('user_register', function($user) {
            //     wp_new_user_notification($user->ID, null, 'user');
            // });
        } catch(\Exception $e) {

        }
        return $getUser;
}

// Przechwytywanie i obsługa danych z formularza
function landingpage_form_post_request() {
    $messages = [];
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_form'])) {

        /**
         * Zweryfikuj recaptcha
         */
        if(recaptchaVerify($_POST)) {
            $name = $_POST['imie'];
            $email = $_POST['email'];

            // echo $name;
            // echo "<br/>";
            // echo $email;
            // exit();

            /**
             * Na podstawie tych danych utwórz klienta oraz zrealizuj zamówienie na darmowy produkt
             */
            try { 
                /**
                 * Jesli nie mozna odtworzyc obiektu usera na podstawie emaila, stwórz obiekt
                 */
                if(!get_user_by('email', $email)) {
                    $generatePassword =  wp_generate_password(8);//1;//wp_generate_password( 8, true, false ); // 1
                    /**
                     * Stworz obiekt uzytkownika
                     */
                    $user = createUser($name, $generatePassword, $email);

                    // Zainicjuj event do Klavyio
                    createKlavyioEventSubscription($email);

                    $createdUser = 1;
                } else {
                    $createdUser = 0;
                    $user = $email;
                }
               
                /**
                 * Pobierz obiekt usera
                 */
                if( is_numeric( $user ) ) {
                    $user = get_userdata( $user );
                } elseif ( is_email( $user ) ) {
                    $user = get_user_by( 'email', $user );
                } elseif ( is_string( $user ) ) {
                    $user = get_user_by( 'login', $user );
                } else {
                }

                /**
                 * Sprawdź czy ten user posiada darmowe szkolenie
                 */
                $product = get_post( (int)get_option('mauricz_product_free_course') );
                $course = WPI()->courses->get_course_by_product( $product->ID );
                $course_page_id = get_post_meta( $course->ID, 'course_id', true );
                $restricted_to  = array( array( 'download' => $product->ID ) );
                $access         = bpmj_eddpc_user_can_access( $user->ID, $restricted_to, $course_page_id );
               if  ( 'valid' === $access[ 'status' ] || 'waiting' === $access[ 'status' ] ) {
                    /**
                     * User posiada szkolenie zwróć komunikat
                     */
                    set_transient('mjwp_landingpage_flash_message_error', 'To konto posiada już te szkolenie.', 30); 
                    wp_redirect(wp_get_referer());
                } else {
                    /**
                     * User nie posiada szkolenia stwórz obiekt zamówienia, powiaz z userem i zwróć komunikat
                     */
                    /**
                    * Dodaj kurs dla usera
                    */
                    $user_id = $user ? $user->ID : 0;
                    
                    // Dodaj kurs dla danego usera
                    addCourseToUser(get_option('mauricz_product_free_course'), $user_id);

                    // Stworz obiekt zamówienia
                    #createOrder($user_id, $user);

               
                    if($createdUser == 1) { 
                        set_transient('mjwp_landingpage_flash_message_success', 'Dziękujemy za rejestrację na naszej platformie. Szkolenie zostało przypisane do Twojego konta. <a href="'.get_permalink(596).'">Zresetuj hasło</a>, zaloguj się do konta i ciesz się szkoleniem!', 30); 
                    } else {
                        $logged = get_current_user_id();
                        if($logged) {
                            // Przejdź do szkolenia
                            set_transient('mjwp_landingpage_flash_message_success', 'Szkolenie zostało przypisane do Twojego konta. <br/> <a href="'.get_permalink(get_option()).'">Przejdź do szkolenia</a>', 30); 

                        } else{
                            //Zaloguj się
                            set_transient('mjwp_landingpage_flash_message_success', 'Szkolenie zostało przypisane do Twojego konta. <br/> <a href="'.get_permalink(560).'">Zaloguj się</a>', 30); 
                        }
                    }
                    wp_redirect(wp_get_referer());
                }
            } catch(\Exception $e) { 
                #echo $e->getMessage();
            }
        } else {      
            set_transient('mjwp_landingpage_flash_message_error', 'Błąd recaptcha', 30); // Przechowa wiadomość na 30 sekund
            wp_redirect(wp_get_referer());
        }
    }
}
/**
 * Dodanie zamówienia dla usera
 * @param int $user_id
 * @param object $user
 */

 function createOrder($user_id, $user) { 

    // Adres dla uzytkownika 
    $address = array(
    'line1'    => '',
    'line2'    => '',
    'city'     => '',
    'state'    => '',
    'zip'      => '',
    'country'  => ''
    );

    $meta    = update_user_meta( $user_id, '_edd_user_address', $address );

    // Koniec adresu 

    $user_info = array(
        'id' 			=> $user_id,
        'email' 		=> $user->user_email,
        'first_name'	=> $user->first_name,
        'last_name'		=> $user->last_name,
        'discount'		=> 'none'
    );
    
    $price = false;
    
    $cart_details = array();

    $total = 0;

    $item_price = edd_get_download_price( get_option('mauricz_product_free_course') );

    $cart_details[] = array(
        'name'        => get_the_title( get_option('mauricz_product_free_course') ),
        'id'          => get_option('mauricz_product_free_course'),
        'item_number' => 0,
        'price'       => $price ? $price : $item_price,
        'subtotal'    => $price ? $price : $item_price,
        'quantity'    => 1,
        'tax'         => 0,
    );
    $total = $item_price;
  
    if( $price ) {
        $total = $price;
    }

    $date = date( 'Y-m-d H:i:s', current_time( 'timestamp' ) );

    if( strtotime( $date, time() ) > time() ) {
        $date = date( 'Y-m-d H:i:s', current_time( 'timestamp' ) );
    }

    // Status zamówienia
    $status = 'publish'; 
    // Podatek
    $tax    =  0;

    // Dane
    $purchase_data     = array(
        'price'        => edd_sanitize_amount( $total ),
        'tax'          => $tax,
        'post_date'    => $date,
        'purchase_key' => strtolower( md5( uniqid() ) ), // random key
        'user_email'   => $email,
        'user_info'    => $user_info,
        'currency'     => edd_get_currency(),
        'downloads'    => $data['downloads'],
        'cart_details' => $cart_details,
        'status'       => 'publish', // wczesniej pending
    );
    
    //
    // Dodawanie adresu do zamówienia
    //
    $payment_id = edd_insert_payment( $purchase_data );
 }

/**
 * Dodanie kursu dla usera
 * @param int $product_id
 * @param int $user_id
 * @return bool
 */
function addCourseToUser($product_id,$user_id) { 
    
    $user_id = (int)$user_id;
    $product_id = (int)$product_id;
    $days = (int)365;
    $hours = (int)0;
    $minutes = (int)0;
    $seconds = (int)0;
    $sign = '';

    $total_time = $days * 86400 + $hours * 3600 + $minutes * 60 + $seconds;

    $price_id = (int)0;


    if ($user_id && $product_id) {
        bpmj_eddpc_add_time($user_id, $product_id, $price_id, $total_time);

        $course_id = WPI()->courses->get_course_by_product($product_id);

        if($course_id){

            $access_time_data = get_user_meta($user_id, "_bpmj_eddpc_access", true);


            $timestamp = time()+$total_time;
            $due_date = gmdate("Y-m-d", $timestamp);


            $access_time_str = $due_date . ' ' .'00:00';
            $access_time = bpmj_eddpc_adjust_timestamp(strtotime($access_time_str), false);
            
            $access_time_data[$product_id]['access_time'] = $access_time;
    
            update_user_meta($user_id, '_bpmj_eddpc_access', $access_time_data);
        }
    }
}

/**
 * Tworzy zdarzenie subskrypcji w klavyio
 * @param string $email
 * @return bool
 */
function createKlavyioEventSubscription($email) {
    try { 
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
                                    "email" => $email,
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
        #return false;
    }
}


/**
 * Dodanie tabsa z ustawieniami
 */
function MjwpLandingpage_add_settings_page() {
    add_options_page(
        'Ustawienia Mauricz Landingpage', // Tytuł strony
        'Ustawienia Mauricz Landingpage', // Nazwa w menu
        'manage_options',  // Uprawnienia
        'mjwpLandingpage-settings', // Unikalny slug
        'MjwpLandingpage_render_settings_page' // Funkcja renderująca
    );
}

/**
 * Konfiguracja sekcji dla wtyczki
 */
function MjwpLandingpage_register_settings() {
    register_setting('MjwpLandingpage_options_group', 'MjwpLandingpage_id_page');

    add_settings_section(
        'MjwpLandingpage_main_section',
        'Ustwienia Landingpage Mauricz.tv',
        null,
        'MjwpLandingpage-settings'
    );

    add_settings_field(
        'MjwpLandingpage_id_page',
        'Podaj ID strony z którą ma być powiązany landing page',
        'MjwpLandingpage_id_page_callback',
        'MjwpLandingpage-settings',
        'MjwpLandingpage_main_section'
    );
}
add_action('admin_init', 'MjwpLandingpage_register_settings');

/**
 * Zwrócenie ustawień
 */
function MjwpLandingpage_id_page_callback() {
    $value = get_option('MjwpLandingpage_id_page', '');
    echo '<input type="text" name="MjwpLandingpage_id_page" value="' . esc_attr($value) . '" />';
}

/**
 * Renderowanie sekcji ustawień
 */
function MjwpLandingpage_render_settings_page() {
    ?>
    <div class="wrap">
        <h1>Ustwienia Landingpage Mauricz.tv</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('MjwpLandingpage_options_group');
            do_settings_sections('MjwpLandingpage-settings');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

/**
 * Stworzenie eventu w klavyio
 * @param string $eventKey
 * @param string $eventValue
 * @param string $email
 * @return bool
 */

 function createEvent($eventKey, $evetValue, $email) { 
    try {

        $apiKey = get_option('mauricz_klavyio_api_private_key');
        $revision = get_option('mauricz_klavyio_api_revision');//'2025-01-15';

 
$url = 'https://a.klaviyo.com/api/events';

$data = [
    "data" => [
        "type" => "event",
        "attributes" => [
            "metric" => [
                "data" => [
                    "type" => "metric",
                    "attributes" => [
                        "name" => $eventKey
                    ]
                ]
            ],
            "properties" => [
                "status" => $evetValue
            ],
            "profile" => [
                "data" => [
                    "type" => "profile",
                    "attributes" => [
                        "email" => $email
                    ]
                ]
            ]
        ]
    ]
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Klaviyo-API-Key $apiKey",
    "Accept: application/vnd.api+json",
    "Content-Type: application/vnd.api+json",
    "Revision: ".$revision
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

$response = curl_exec($ch);

$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

print_r($httpCode);
        // $c = curl_init();
        // curl_setopt($c, CURLOPT_URL, 'https://a.klaviyo.com/api/events');//.trim(($id_event)));

        // // date revision
        // $revision = get_option('mauricz_klavyio_api_revision');//'2025-01-15';

        // $KlaviyoPrivateKey = get_option('mauricz_klavyio_api_private_key'); //'pk_788d358870622e5f3ba8afcea7d675dd02';
        // $head[] ='Authorization: Klaviyo-API-Key '.$KlaviyoPrivateKey.'';
        // $head[] ='Content-Type:application/json';
        // $head[] ='accept: application/json';
        // $head[] ='revision: '.$revision;
        // curl_setopt($c, CURLOPT_HTTPHEADER, $head);
        // curl_setopt($c, CURLOPT_POST, true);
        // curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);

        // $json_request = [
        //     "data" => [
        //         "type" => "event",
        //         "attributes" => [
        //             "properties" => [
        //                 "Klienci" => "Zarejestrowani"
        //             ],
        //             "metric" => [
        //                 "data" => [
        //                     "type" => "metric"
        //                 ]
        //             ],
        //             "profile" => [
        //                 "data" => [
        //                     "type" => "profile",
        //                     "attributes" => [
        //                         "email" => "biuro@mages.pl"
        //                     ]
        //                 ]
        //             ]
        //         ]
        //     ]
        // ];


            
        // curl_setopt($c, CURLOPT_POSTFIELDS, json_encode($json_request));

        // $result =  @json_decode(curl_exec($c), 1);

        // echo "+====";   
        // print_r($result);

    } catch(\Exception $e) {
        return $e->getMessage();
    }
 }

add_action('admin_menu', 'MjwpLandingpage_add_settings_page');

add_action('init', 'landingpage_form_post_request');

add_action('template_redirect', 'add_custom_variables');

add_action('template_redirect', 'add_custom_variables');
?>
