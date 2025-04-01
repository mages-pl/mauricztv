<?php
/*
  Plugin Name: Publigo
  Plugin URI: https://publigo.pl
  Description: Sell thousands of online courses with a few clicks
  Author: NEURE
  Author URI: https://upsell.pl
  Requires at least: 5.5
  Requires PHP: 7.4
  Version: 6.3
 */

use bpmj\wpidea\admin\Admin_Email_Notifications;
use bpmj\wpidea\admin\bar\Admin_Bar;
use bpmj\wpidea\admin\categories\Categories;
use bpmj\wpidea\admin\Creator;
use bpmj\wpidea\admin\Dashboard;
use bpmj\wpidea\admin\dashboard\Dashboard as Panel;
use bpmj\wpidea\admin\discounts\Discounts;
use bpmj\wpidea\admin\Edit_Bundle;
use bpmj\wpidea\admin\Edit_Course;
use bpmj\wpidea\admin\Edit_Product;
use bpmj\wpidea\admin\Edit_User;
use bpmj\wpidea\admin\Edit_User_Partner;
use bpmj\wpidea\admin\helpers\html\Admin_Popup_Initializer;
use bpmj\wpidea\admin\helpers\utils\Snackbar;
use bpmj\wpidea\admin\integrations\Tracker_Data_Collector;
use bpmj\wpidea\admin\media\Abstract_Limit_Checker;
use bpmj\wpidea\admin\media\Admin_Bar_Media_Info_Registrator;
use bpmj\wpidea\admin\media\Media_Limit_Checkers_Instantiator;
use bpmj\wpidea\admin\Menu;
use bpmj\wpidea\admin\menu\Admin_Menu_Reorderer;
use bpmj\wpidea\admin\menu\Site_Admin_Menu_Mode_Toggle;
use bpmj\wpidea\admin\Notices;
use bpmj\wpidea\admin\notices\Notice_Handlers_Initiator;
use bpmj\wpidea\admin\notices\Wp_Version_Notice;
use bpmj\wpidea\admin\pages\Admin_Pages_Redirector;
use bpmj\wpidea\admin\pages\bundle_editor\core\events\handlers\Bundle_Editor_Field_Update_Handler;
use bpmj\wpidea\admin\pages\common\Admin_Dynamic_Tables_Initiator;
use bpmj\wpidea\admin\pages\course_editor\core\events\handlers\Course_Editor_Field_Update_Handler;
use bpmj\wpidea\admin\pages\course_editor\core\filters\handlers\Course_Editor_Add_Body_Classes_Handler;
use bpmj\wpidea\admin\pages\customers\Customers;
use bpmj\wpidea\admin\pages\digital_product_editor\Digital_Product_Editor_Handler;
use bpmj\wpidea\admin\pages\payments_history\Payments_History;
use bpmj\wpidea\admin\pages\physical_product_editor\Physical_Product_Editor_Handler;
use bpmj\wpidea\admin\pages\quizzes\Redirect_After_Remove_Test;
use bpmj\wpidea\admin\pages\service_editor\Service_Editor_Handler;
use bpmj\wpidea\admin\Post_List;
use bpmj\wpidea\admin\redirects\LMS_Role_Redirector;
use bpmj\wpidea\admin\redirects\Media_New_Page_Redirect;
use bpmj\wpidea\admin\redirects\Old_Products_Page_To_Dashboard_Redirector;
use bpmj\wpidea\admin\redirects\Users_New_Page_Redirect;
use bpmj\wpidea\admin\Remote_Notifications as Admin_Remote_Notifications;
use bpmj\wpidea\admin\settings\core\services\Settings_Saved_Event_Handler;
use bpmj\wpidea\admin\settings\Core_Settings;
use bpmj\wpidea\admin\subscription\api\Subscription_API;
use bpmj\wpidea\admin\subscription\handlers\Subscription_Notifier_Initiator;
use bpmj\wpidea\admin\support\Support;
use bpmj\wpidea\admin\Tools;
use bpmj\wpidea\admin\updates\New_Version_Notification;
use bpmj\wpidea\admin\Upgrades;
use bpmj\wpidea\admin\User_List;
use bpmj\wpidea\admin\video\usage\Cached_Video_Space_Checker;
use bpmj\wpidea\admin\video\Videos;
use bpmj\wpidea\Analytics;
use bpmj\wpidea\API;
use bpmj\wpidea\api\WPI;
use bpmj\wpidea\API_V2;
use bpmj\wpidea\app\digital_products\Digital_Product_Download_Protector;
use bpmj\wpidea\assets\Assets_Dir;
use bpmj\wpidea\Caps;
use bpmj\wpidea\certificates\Certificate_Template_Actions;
use bpmj\wpidea\certificates\Certificates;
use bpmj\wpidea\commands\WPIdea_Commands;
use bpmj\wpidea\Cookie_Bar;
use bpmj\wpidea\Courses;
use bpmj\wpidea\Diagnostic;
use bpmj\wpidea\EDD_SL_Plugin_Updater;
use bpmj\wpidea\email_handler\Email_Plain_Text_Handler;
use bpmj\wpidea\events\Event_Handlers_Initiator;
use bpmj\wpidea\filters\emails\Email_Logo_Filter;
use bpmj\wpidea\filters\Invoice_Flat_Rate_Handler;
use bpmj\wpidea\headers\HSTS_Header;
use bpmj\wpidea\helpers\In_Memory_Cache_Static_Helper;
use bpmj\wpidea\helpers\Translator_Static_Helper;
use bpmj\wpidea\infrastructure\error\handler\Airbrake as Airbrake_Error_Handler;
use bpmj\wpidea\infrastructure\error\handler\Airbrake_Integration_Manual_Manager;
use bpmj\wpidea\infrastructure\error\Notifier as Error_Notifier;
use bpmj\wpidea\infrastructure\io\Disk_Space_Checker;
use bpmj\wpidea\instantiator\Instantiator;
use bpmj\wpidea\instantiator\Interface_Initiable;
use bpmj\wpidea\learning\quiz\api\Interface_Quiz_Api;
use bpmj\wpidea\learning\quiz\api\Quiz_Api_Static_Helper;
use bpmj\wpidea\ls_cache\Ls_Cache_Cleaner;
use bpmj\wpidea\ls_cache\Ls_Cache_Handler;
use bpmj\wpidea\mods\Categories_Posts_Count_Remover;
use bpmj\wpidea\mods\Change_Language_Button_Remover;
use bpmj\wpidea\mods\Custom_Editor_Font_Sizes;
use bpmj\wpidea\mods\Edit_Full_Screen_Mode_Redirect;
use bpmj\wpidea\mods\Front_Admin_Menu_Handler;
use bpmj\wpidea\mods\S3_File_Storage_Handler;
use bpmj\wpidea\modules\videos\Videos_Module;
use bpmj\wpidea\nonce\Nonce_Handler;
use bpmj\wpidea\nonce\Nonce_Html_Meta_Handler;
use bpmj\wpidea\notices\payments\Payment_Error_Notice;
use bpmj\wpidea\Packages;
use bpmj\wpidea\packages\Feature_Access_Manual_Manager;
use bpmj\wpidea\packages\Interface_Packages_API;
use bpmj\wpidea\packages\Packages_API_Static_Helper;
use bpmj\wpidea\pages\Page;
use bpmj\wpidea\physical_product\service\Physical_Product_Classes_Instantiator;
use bpmj\wpidea\Remote_Notifications_Checker;
use bpmj\wpidea\routing\Router;
use bpmj\wpidea\sales\product\api\Product_API;
use bpmj\wpidea\sales\product\legal\Information_About_Lowest_Price;
use bpmj\wpidea\sales\product\service\Information_About_Available_Quantities;
use bpmj\wpidea\scheduled_events\On_Load_Schedule_Handler;
use bpmj\wpidea\scopes\Admin_Custom_Post_Courses_List_Scope;
use bpmj\wpidea\scopes\Admin_Scope;
use bpmj\wpidea\scopes\Admin_Without_Ajax_Scope;
use bpmj\wpidea\scopes\Cron_Scope;
use bpmj\wpidea\scopes\Front_Without_Ajax_Scope;
use bpmj\wpidea\scopes\No_Scope;
use bpmj\wpidea\scopes\Ssl_Scope;
use bpmj\wpidea\shared\infrastructure\modules\Module_Registrar;
use bpmj\wpidea\SMTP_Activator;
use bpmj\wpidea\Software_Variant;
use bpmj\wpidea\telemetry\Telemetry;
use bpmj\wpidea\Templates;
use bpmj\wpidea\templates_system\Templates_System;
use bpmj\wpidea\templates_system\Wpi_Cart_Script_Params_For_Disabled_Template_Page;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\Trial;
use bpmj\wpidea\user\api\User_API;
use bpmj\wpidea\User_Security;
use bpmj\wpidea\View_Hooks_Data_Layer;
use bpmj\wpidea\wolverine\Main;
use bpmj\wpidea\wolverine\repository\RepositoryLocator;
use bpmj\wpidea\WP_Cleaner;
use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('BPMJ_WPI')) {
    class BPMJ_WPI
    {

        public const RECOMMENDED_PHP_VERSION = '8.0';
        public const MINIMUM_PHP_VERSION = '7.4.0';

        private static BPMJ_WPI $instance;
        public Core_Settings $settings;
        public Tools $tools;
        public Diagnostic $diagnostic;
        public string $version;
        public Templates $templates;
        public ?Snackbar $snackbar;
        public Dashboard $dashboard;
        public Support $support;
        public Packages $packages;
        public API $api;
        public API_V2 $api_v2;
        public RepositoryLocator $repo_locator;
        public Courses $courses;
        public Caps $caps;
        public Certificates $certificates;
        public Certificate_Template_Actions $certificates_template_actions;
        public ?Videos $videos;
        public Notices $notices;
        public Templates_System $templates_system;
        public Trial $trial;
        public Page $page;
        public ?Admin_Bar $admin_bar;
        public Telemetry $telemetry;
        public User_Security $security;
        public View_Hooks_Data_Layer $view_hooks_data_layer;
        public Analytics $analytics;
        public ContainerInterface $container;

        public static function instance()
        {
            global $wpidea_settings;

            $wpidea_settings = get_option('wp_idea');

            if (!isset(self::$instance)) {
                self::$instance = new BPMJ_WPI();
                $container = self::$instance->get_container();
                self::$instance->container = $container;

                self::should_be_initialized_before_modules();
                $container->get(Module_Registrar::class);

                In_Memory_Cache_Static_Helper::init();

                self::init_translator($container);

                $container->get(Subscription_API::class);

                $container->get(Product_API::class);

                $container->get(User_API::class);

                Packages_API_Static_Helper::init($container->get(Interface_Packages_API::class));
                Quiz_Api_Static_Helper::init($container->get(Interface_Quiz_API::class));

                self::$instance->auto_upgrade();

                /** @var Instantiator $instantiator */
                $instantiator = $container->get(Instantiator::class);

                $instantiator->create(Admin_Dynamic_Tables_Initiator::class, new Admin_Scope());

                $instantiator->create(HSTS_Header::class, new Ssl_Scope());

                $instantiator->create(\bpmj\wpidea\sales\product\acl\Product_Sale_Dates_ACL::class, new No_Scope());

                Error_Notifier::set_handler($container->get(Airbrake_Error_Handler::class));

                add_action('admin_enqueue_scripts', [self::$instance, 'admin_scripts_styles']);

                add_action('admin_notices', [self::$instance, 'php_version_notice']);

                new Cookie_Bar();

                self::$instance->diagnostic = $container->get(Diagnostic::class);
                self::$instance->settings = $container->get(Core_Settings::class);
                self::$instance->tools = $container->get(Tools::class);

                self::$instance->dependencies();

                self::$instance->notices = $container->get(Notices::class);

                $container->get(Remote_Notifications_Checker::class);

                new Edit_Product();
                $container->get(Admin_Email_Notifications::class);

                $instantiator->create(Email_Logo_Filter::class, new No_Scope());

                $instantiator->create(Digital_Product_Download_Protector::class, new Front_Without_Ajax_Scope());

                self::$instance->should_be_initialized_before__wp_get_upload_dir__is_used($instantiator);

                $instantiator->create(Cached_Video_Space_Checker::class, new Cron_Scope());
                $instantiator->create(Cached_Video_Space_Checker::class, new Admin_Scope());
                $instantiator->create(Information_About_Lowest_Price::class, new Front_Without_Ajax_Scope());
                $instantiator->create(Information_About_Available_Quantities::class, new Front_Without_Ajax_Scope());

                $instantiator->create(Physical_Product_Classes_Instantiator::class, new No_Scope());

                if (is_admin()) {
                    new Creator();
                    new User_List();
                    $container->get(Edit_Bundle::class);
                    $container->get(Edit_Course::class);

                    $instantiator->create(Edit_User::class, new Admin_Scope());
                    $instantiator->create(Edit_User_Partner::class, new Admin_Scope());


                    $instantiator->create(Course_Editor_Field_Update_Handler::class, new Admin_Scope());
                    $instantiator->create(Course_Editor_Add_Body_Classes_Handler::class, new Admin_Scope());
                    $instantiator->create(Bundle_Editor_Field_Update_Handler::class, new Admin_Scope());

                    $instantiator->create(Digital_Product_Editor_Handler::class, new Admin_Scope());
                    $instantiator->create(Service_Editor_Handler::class, new Admin_Scope());
                    $instantiator->create(Physical_Product_Editor_Handler::class, new Admin_Scope());
                    $instantiator->create(Old_Products_Page_To_Dashboard_Redirector::class, new Admin_Without_Ajax_Scope());

                    $instantiator->create( Feature_Access_Manual_Manager::class, new Admin_Without_Ajax_Scope());
                    $instantiator->create(Airbrake_Integration_Manual_Manager::class, new Admin_Without_Ajax_Scope());

                    $container->get(Menu::class);

                    self::$instance->dashboard = new Dashboard();

                    if (!Software_Variant::is_international()) {
                        self::$instance->support =  $instantiator->create(Support::class, new Admin_Scope());
                    }

                    self::$instance->admin_bar = $instantiator->create(
                        Admin_Bar::class,
                        new Admin_Without_Ajax_Scope()
                    );

                    $instantiator->create(Admin_Bar_Media_Info_Registrator::class, new Admin_Without_Ajax_Scope());
                    $instantiator->create(Disk_Space_Checker::class, new Admin_Scope());
                    $instantiator->create(Admin_Pages_Redirector::class, new Admin_Without_Ajax_Scope());

                    (new Panel())->init();

                    $container->get(Admin_Remote_Notifications::class);

                    $instantiator->create(Admin_Menu_Reorderer::class, new Admin_Without_Ajax_Scope());

                    new Payments_History();

                    new Categories();
                    $instantiator->create(Discounts::class, new Admin_Without_Ajax_Scope());
                    new Customers();

                }

                if (Software_Variant::is_saas()) {
                    self::$instance->videos = $instantiator->create(Videos::class, new No_Scope());

                    $instantiator->create(Media_Limit_Checkers_Instantiator::class, new Admin_Scope());

                    $instantiator->create(Media_New_Page_Redirect::class, new Admin_Scope());
                }


                $instantiator->create(Users_New_Page_Redirect::class, new Admin_Scope());

                $instantiator->create(Tracker_Data_Collector::class, new Admin_Scope());

                self::$instance->courses = $container->get(Courses::class);
                self::$instance->caps = $container->get(Caps::class);
                self::$instance->certificates = $container->get(Certificates::class);
                self::$instance->certificates_template_actions = $container->get(Certificate_Template_Actions::class);
                self::$instance->templates = $container->get(Templates::class);
                self::$instance->analytics = $container->get(Analytics::class);
                self::$instance->view_hooks_data_layer = new  View_Hooks_Data_Layer();
                self::$instance->api = new API();
                self::$instance->api_v2 = $container->get(API_V2::class);
                self::$instance->repo_locator = new RepositoryLocator();

                self::$instance->templates_system = $container->get(Templates_System::class);
                self::$instance->templates_system->init();

                $container->get(Ls_Cache_Handler::class);
                $container->get(Ls_Cache_Cleaner::class);

                $container->get(Nonce_Html_Meta_Handler::class);

                $container->get(On_Load_Schedule_Handler::class);
                $container->get(Event_Handlers_Initiator::class);

                new SMTP_Activator();
                $instantiator->create(User_Security::class, new No_Scope());

                if (!is_admin()) {
                    add_action('wp', function () use ($container) {
                        self::$instance->page = $container->get(Page::class);
                    });
                }

                new WP_Cleaner();

                if (is_admin()) {
                    $container->get(Subscription_Notifier_Initiator::class);
                    $container->get(Notice_Handlers_Initiator::class);
                }

                $instantiator->create(Router::class, new No_Scope());

                self::$instance->snackbar = $instantiator->create(Snackbar::class, new Admin_Without_Ajax_Scope());
                $instantiator->create(New_Version_Notification::class, new Admin_Without_Ajax_Scope());
                $instantiator->create(
                    Edit_Full_Screen_Mode_Redirect::class,
                    $container->get(Admin_Custom_Post_Courses_List_Scope::class)
                );
                $instantiator->create(Categories_Posts_Count_Remover::class, new Admin_Without_Ajax_Scope());
                $instantiator->create(Email_Plain_Text_Handler::class, new No_Scope());
                $instantiator->create(Custom_Editor_Font_Sizes::class, new Front_Without_Ajax_Scope());
                $instantiator->create(
                    Wpi_Cart_Script_Params_For_Disabled_Template_Page::class,
                    new Front_Without_Ajax_Scope()
                );
                $instantiator->create(Wp_Version_Notice::class, new Admin_Without_Ajax_Scope());
                $instantiator->create(Post_List::class, new Admin_Without_Ajax_Scope());
                $instantiator->create(Payment_Error_Notice::class, new Front_Without_Ajax_Scope());
                $instantiator->create(LMS_Role_Redirector::class, new Front_Without_Ajax_Scope());
                $instantiator->create(Site_Admin_Menu_Mode_Toggle::class, new Admin_Without_Ajax_Scope());
                $instantiator->create(Invoice_Flat_Rate_Handler::class, new No_Scope());
                $instantiator->create(Redirect_After_Remove_Test::class, new Admin_Scope());
                $instantiator->create(Settings_Saved_Event_Handler::class, new No_Scope());
                $instantiator->create(Admin_Popup_Initializer::class, new Admin_Scope());
                $instantiator->create(Front_Admin_Menu_Handler::class, new No_Scope());

                new Main();


                if (Software_Variant::is_saas()) {
                    $instantiator->create(Change_Language_Button_Remover::class, new No_Scope());
                }

            }
            return self::$instance;
        }

        private function should_be_initialized_before__wp_get_upload_dir__is_used(Instantiator $instantiator): void
        {
            if (Software_Variant::is_saas()) {
                $instantiator->create(S3_File_Storage_Handler::class, new No_Scope());
            }
        }

        public function __construct()
        {
            $this->autoload_vendors();
            $this->constants();

            $this->load_textdomain();
            $this->includes();

            $this->version = BPMJ_EDDCM_VERSION;
        }

        private function autoload_vendors()
        {
            require_once plugin_dir_path(__FILE__) . 'vendor/autoload.php';
            require_once plugin_dir_path(__FILE__) . 'autoload.php';
            $loader = new PSR4_Autoloader();
            $loader->addNamespace('bpmj\wpidea\api', plugin_dir_path(__FILE__) . 'api/');
            $loader->addNamespace('bpmj\wpidea', plugin_dir_path(__FILE__) . 'includes/');
            $loader->addNamespace('bpmj\wpidea\wolverine', plugin_dir_path(__FILE__) . 'wolverine/', false);
            $loader->register();
        }

        private function constants()
        {
            $dev_constants_file = dirname(__FILE__) . '/_dev-constants.php';
            $this->include_file_if_exists($dev_constants_file);

            $env_file = dirname(__FILE__) . '/../../' . Assets_Dir::EXTERNAL_DIR_NAME . '/env.php';
            if (!$this->include_file_if_exists($env_file)) {
                $env_file = dirname(__FILE__) . '/env.php';
                $this->include_file_if_exists($env_file);
            }

            $this->define('BPMJ_EDDCM_VERSION', '6.3');
            $this->define('BPMJ_EDDCM_NAME', 'WP Idea');
            $this->define('BPMJ_EDDCM_NAME_LOCALIZED', Software_Variant::get_name());
            $this->define('BPMJ_EDDCM_ID', 6245);
            /** @define "BPMJ_EDDCM_DIR" "./" */
            $this->define('BPMJ_EDDCM_URL', plugin_dir_url(__FILE__));   // Root plugin URL
            $this->define('BPMJ_EDDCM_DIR', plugin_dir_path(__FILE__));  // Root plugin path
            $this->define('BPMJ_EDDCM_FILE', __FILE__);   // General plugin FILE
            $this->define('BPMJ_EDDCM_DOMAIN', 'wp-idea');   // Text Domain
            $this->define('BPMJ_EDDCM_SETTINGS_SLUG', 'wp_idea');   // Settings slug
            $this->define('BPMJ_UPSELL_STORE_URL', 'https://upsell.pl');

            $this->define('BPMJ_EDDCM_TEMPLATES_DIR', BPMJ_EDDCM_DIR . 'templates/');
            $this->define('BPMJ_EDDCM_TEMPLATES_URL', BPMJ_EDDCM_URL . 'templates/');

            $this->define('EDD_SLUG', 'product');
            $this->define('EDD_DISABLE_ARCHIVE', true);
        }

        private function include_file_if_exists($path_to_file)
        {
            if (file_exists($path_to_file)) {
                include $path_to_file;
                return true;
            }
            return false;
        }

        /*
         * Define constant if not already set
         * @param  string $name
         * @param  string|bool $value
         */
        private function define($name, $value)
        {
            if (!defined($name)) {
                define($name, $value);
            }
        }

        private function includes()
        {
            require_once BPMJ_EDDCM_DIR . 'includes/functions.php';
            require_once BPMJ_EDDCM_DIR . 'includes/filters.php';
            require_once BPMJ_EDDCM_DIR . 'includes/actions.php';
            require_once BPMJ_EDDCM_DIR . 'includes/events.php';
            require_once BPMJ_EDDCM_DIR . 'includes/shortcode.php';
            require_once BPMJ_EDDCM_DIR . 'includes/pluggable.php';
            
            if (defined('WP_CLI') && WP_CLI) {
                new WPIdea_Commands();
            }

            if (is_admin()) {
                require_once BPMJ_EDDCM_DIR . 'includes/admin/menu.php';
                require_once BPMJ_EDDCM_DIR . 'includes/admin/filters.php';
                require_once BPMJ_EDDCM_DIR . 'includes/admin/actions.php';
            }
        }

        private function dependencies()
        {
            if (!file_exists(BPMJ_EDDCM_DIR . 'dependencies')) {
                return;
            }

            require_once BPMJ_EDDCM_DIR . 'vendor/upsell/wpi-cart/wpi-cart.php';
            require_once BPMJ_EDDCM_DIR . 'dependencies/edd-auto-register/edd-auto-register.php';
            require_once BPMJ_EDDCM_DIR . 'dependencies/edd-auto-register-custom/edd-auto-register-custom.php';

            $this->load_gates();
            $this->load_invoices();
            $this->load_mailers();
            $this->load_compatibility();

            require_once BPMJ_EDDCM_DIR . 'dependencies/edd-guest-buying-patch/edd-guest-buying-patch.php';
            require_once BPMJ_EDDCM_DIR . 'dependencies/edd-invoice-data/edd-invoice-data.php';
            require_once BPMJ_EDDCM_DIR . 'dependencies/edd-manual-purchases/edd-manual-purchases.php';

            require_once BPMJ_EDDCM_DIR . 'dependencies/edd-paid-content/edd-paid-content.php';

            require_once BPMJ_EDDCM_DIR . 'dependencies/edd-sale-price/edd-sale-price.php';

            require_once BPMJ_EDDCM_DIR . 'dependencies/edd-sell-discount/edd-sell-discount.php';

            if (WPI()->packages->has_access_to_feature(Packages::FEAT_DISCOUNT_CODE_GENERATOR)) {
                require_once BPMJ_EDDCM_DIR . 'dependencies/edd-discount-code-generator/edd-discount-code-generator.php';
            }

            require_once BPMJ_EDDCM_DIR . 'includes/install.php';
        }

        private static function init_translator(ContainerInterface $container): void
        {
            $translator = $container->get(Interface_Translator::class);
            if ($translator instanceof Interface_Initiable) {
                $translator->init();
            }

            Translator_Static_Helper::init($translator);
        }
        public function admin_scripts_styles()
        {
            wp_register_script('bpmj_eddmc_admin_script', BPMJ_EDDCM_URL . 'assets/js/edd-courses-admin.min.js', [
                'jquery',
                'jquery-ui-core',
                'jquery-ui-sortable',
                'wp-color-picker',
                'bpmj_eddmc_jquery_interdependencies_script',
            ], BPMJ_EDDCM_VERSION);

            $text = [
                'ajax' => admin_url('admin-ajax.php'),
                'creator' => [
                    'required' => __('Required', BPMJ_EDDCM_DOMAIN),
                    'bundle_requirement' => __(
                        'Select at least {0} products to create a bundle. Currently selected: <strong>{1}</strong>/{0}',
                        BPMJ_EDDCM_DOMAIN
                    ),
                    'button1' => __('Create modules and lessons', BPMJ_EDDCM_DOMAIN),
                    'button2' => __('Set the course product settings', BPMJ_EDDCM_DOMAIN),
                    'button2_bundle' => __('Set the bundle product settings', BPMJ_EDDCM_DOMAIN),
                    'button3' => __('Save and publish the course', BPMJ_EDDCM_DOMAIN),
                    'button3_bundle' => __('Save and publish the bundle', BPMJ_EDDCM_DOMAIN),
                    'button4' => __('Set invoice or mailers data', BPMJ_EDDCM_DOMAIN),
                    'add_module' => __('Add module', BPMJ_EDDCM_DOMAIN),
                    'add_question' => __('Add question', BPMJ_EDDCM_DOMAIN),
                    'add_lesson' => __('Add lesson', BPMJ_EDDCM_DOMAIN),
                    'add_test' => __('Add quiz', BPMJ_EDDCM_DOMAIN),
                    'search_pages' => __('Type two or more characters...', BPMJ_EDDCM_DOMAIN),
                    'placeholder_module' => __('Title of your module', BPMJ_EDDCM_DOMAIN),
                    'placeholder_lesson' => __('Title of your lesson', BPMJ_EDDCM_DOMAIN),
                    'placeholder_question' => __('Your question', BPMJ_EDDCM_DOMAIN),
                    'delete_confirm_course' => __(
                        'Are you sure that you want to delete this course? This operation cannot be undone.',
                        BPMJ_EDDCM_DOMAIN
                    ),
                    'delete_confirm_bundle' => __(
                        'Are you sure that you want to delete this bundle? This operation cannot be undone.',
                        BPMJ_EDDCM_DOMAIN
                    ),
                    'modal' => [
                        'heading' => __('Do you want to add subpages?', BPMJ_EDDCM_DOMAIN),
                        'content1' => __('The page that you want to add, has connected subpages.', BPMJ_EDDCM_DOMAIN),
                        'content2' => __(
                            '<h3>Do you want to add the following pages to this module?</h3>',
                            BPMJ_EDDCM_DOMAIN
                        ),
                        'btn1' => __('No thanks', BPMJ_EDDCM_DOMAIN),
                        'btn2' => __('Yes please', BPMJ_EDDCM_DOMAIN)
                    ],
                    'modal_save' => [
                        'heading' => __('Saving your course', BPMJ_EDDCM_DOMAIN),
                        'content1' => __("<h3>I'm saving your course right now.</h3>", BPMJ_EDDCM_DOMAIN),
                        'content2' => __('<h3>Your course is successfully saved!</h3>', BPMJ_EDDCM_DOMAIN),
                        'btn1' => [
                            'url' => admin_url('admin.php?page=wp-idea'),
                            'title' => __('Go to the dashboard', BPMJ_EDDCM_DOMAIN),
                        ],
                        'btn2' => [
                            'url' => admin_url(),
                            'title' => __('Edit course', BPMJ_EDDCM_DOMAIN),
                        ]
                    ],
                    'modal_save_bundle' => [
                        'heading' => __('Saving your bundle', BPMJ_EDDCM_DOMAIN),
                        'content1' => __("<h3>I'm saving your bundle right now.</h3>", BPMJ_EDDCM_DOMAIN),
                        'content2' => __('<h3>Your bundle is successfully saved!</h3>', BPMJ_EDDCM_DOMAIN),
                        'btn1' => [
                            'url' => admin_url('admin.php?page=wp-idea'),
                            'title' => __('Go to the dashboard', BPMJ_EDDCM_DOMAIN),
                        ],
                        'btn2' => [
                            'url' => admin_url(),
                            'title' => __('Edit bundle', BPMJ_EDDCM_DOMAIN),
                        ]
                    ]
                ],
                'courses' => [
                    'variable_prices_popup' => [
                        'title' => __('Edit variable prices', BPMJ_EDDCM_DOMAIN),
                        'placeholder' => '<div id="bpmj-eddcm-variable-prices-placeholder"><div style="text-align: center;"><div class="spinner is-active" style="float: none;"></div></div></div>',
                    ],
                ],
                'users_manager' => [
                    'set_access_time_popup' => [
                        'title' => __('Set access due date', BPMJ_EDDCM_DOMAIN),
                    ],
                    'set_total_time_popup' => [
                        'title' => __('Set total time', BPMJ_EDDCM_DOMAIN),
                    ],
                    'add_to_course' => [
                        'title' => __('Add a new course to this user', BPMJ_EDDCM_DOMAIN),
                    ],
                    'course_progress_popup' => [
                        'title' => __('Course progress', BPMJ_EDDCM_DOMAIN),
                    ],
                    'remove_user_from_course_confirm' => __(
                        'Are you sure you want to remove the user from the course?',
                        BPMJ_EDDCM_DOMAIN
                    ),
                    'cancel_subscription_confirm' => __(
                        'Are you sure you want to cancel this user\'s subscription for this course?',
                        BPMJ_EDDCM_DOMAIN
                    ),
                ],
                'add_tag' => __('Add tag', BPMJ_EDDCM_DOMAIN),
                'remove_certificate_template' => __(
                    'Are you sure you want to remove certificate template?',
                    BPMJ_EDDCM_DOMAIN
                ),
                'settings' => [
                    'show' => __('Show', BPMJ_EDDCM_DOMAIN),
                    'hide' => __('Hide', BPMJ_EDDCM_DOMAIN)
                ],
                'nonce_value' => Nonce_Handler::create(),
                'nonce_name' => Nonce_Handler::DEFAULT_REQUEST_VARIABLE_NAME,
                'media_limit_checker_popup_message' => self::$instance->container->get(
                    Interface_Translator::class
                )->translate(Abstract_Limit_Checker::MESSAGE_LIMIT_EXCEEDED),
                'media_video_format_blocker_error' => self::$instance->container->get(
                    Interface_Translator::class
                )->translate(Videos_Module::MESSAGE_NOT_ALLOWED_FORMAT),
            ];
            wp_localize_script('bpmj_eddmc_admin_script', 'bpmj_eddcm', $text);
            wp_enqueue_script('bpmj_eddmc_admin_script');


            wp_register_style(
                'bpmj_eddmc_admin_style',
                BPMJ_EDDCM_URL . 'assets/css/edd-courses-admin.css',
                [],
                BPMJ_EDDCM_VERSION
            );
            wp_enqueue_style('wp-color-picker');
            wp_enqueue_style('bpmj_eddmc_admin_style');

            // Select2

            if(!is_plugin_active('advanced-custom-fields')) {
            // wp_register_style(
            //     'bpmj_eddmc_select2_style',
            //     BPMJ_EDDCM_URL . 'assets/css/select2.min.css',
            //     [],
            //     BPMJ_EDDCM_VERSION
            // );
            // wp_enqueue_style('bpmj_eddmc_select2_style');
            // wp_register_script(
            //     'bpmj_eddmc_select2_script',
            //     BPMJ_EDDCM_URL . 'assets/js/select2.min.js',
            //     ['jquery'],
            //     BPMJ_EDDCM_VERSION
            // );
            // wp_enqueue_script('bpmj_eddmc_select2_script');
        }

            // JQuery Interdependencies
            wp_register_script(
                'bpmj_eddmc_jquery_interdependencies_script',
                BPMJ_EDDCM_URL . 'assets/js/jquery.interdependencies.js',
                ['jquery'],
                BPMJ_EDDCM_VERSION
            );
            wp_enqueue_script('bpmj_eddmc_jquery_interdependencies_script');
        }

        /**
         * Register text domain
         */
        private function load_textdomain(): void
        {
            $lang_dir = dirname(plugin_basename(BPMJ_EDDCM_FILE)) . '/languages/';
            load_plugin_textdomain(BPMJ_EDDCM_DOMAIN, false, $lang_dir);
        }


        public function auto_upgrade(bool $mark_only = false): void
        {
            if ($mark_only) {
                self::$instance->get_upgrades_class()->auto_upgrade(true);
            } else {
                add_action('admin_init', [self::$instance->get_upgrades_class(), 'auto_upgrade'], 100);
            }
        }

        public function get_upgrades_class(): Upgrades
        {
            return self::$instance->get_container()->get(Upgrades::class);
        }

        public function load_gates()
        {
            foreach (self::$instance->diagnostic->get_system_content('gates') as $gate) {
                if (self::$instance->diagnostic->is_payment_gate_enabled($gate)) {
                    require_once BPMJ_EDDCM_DIR . 'dependencies/' . $gate . '/' . $gate . '.php';
                    self::$instance->diagnostic->remove_unnecessary_hooks($gate);
                }
            }

            $this->load_paypal();
        }

        public function load_invoices()
        {
            foreach (self::$instance->diagnostic->get_system_content('invoices') as $invoice) {
                if (self::$instance->diagnostic->is_integration_enabled($invoice)) {
                    require_once BPMJ_EDDCM_DIR . 'dependencies/' . $invoice . '/' . $invoice . '.php';
                    self::$instance->diagnostic->remove_unnecessary_hooks($invoice);
                }
            }
        }

        private function load_paypal()
        {
            if (self::$instance->diagnostic->is_payment_gate_enabled('paypal')) {
                require_once BPMJ_EDDCM_DIR . 'dependencies/edd-paypal/includes/class_paypal.php';
            }
        }

        public function load_mailers()
        {
            foreach (self::$instance->diagnostic->get_system_content('mailers') as $mailer) {
                /*
                 * These mailers need to be loaded always because we need to get settings for them
                 */
                $always_include = [
                    'edd-activecampaign',
                    'edd-getresponse',
                    'edd-freshmail',
                    'edd-ipresso',
                    'edd-convertkit',
                ];
                if (self::$instance->diagnostic->is_integration_enabled($mailer) || in_array(
                        $mailer,
                        $always_include
                    )) {
                    require_once BPMJ_EDDCM_DIR . 'dependencies/' . $mailer . '/' . $mailer . '.php';
                    if (!self::$instance->diagnostic->is_integration_enabled($mailer)) {
                        /*
                         * It's necessary to remove mailer hooks because by default mailer plugins assume
                         * they are enabled if their files are loaded
                         */
                        self::$instance->diagnostic->remove_integration_hooks($mailer);
                    }
                    self::$instance->diagnostic->remove_unnecessary_hooks($mailer);
                }
            }
        }

        public function load_compatibility()
        {
            foreach (self::$instance->diagnostic->get_compatibility_fixes() as $plugin_or_theme_name) {
                if (self::$instance->diagnostic->is_plugin_or_theme_active($plugin_or_theme_name)) {
                    require_once BPMJ_EDDCM_DIR . 'includes/compatibility/' . $plugin_or_theme_name . '.php';
                }
            }
        }

        public function php_version_notice()
        {
            if (current_user_can(Caps::ROLE_SITE_ADMIN) && version_compare(
                    WPI_PHP_VERSION(),
                    self::RECOMMENDED_PHP_VERSION,
                    '<'
                )) :
                ?>
                <div class="notice notice-warning">

                    <p><?php
                        printf(
                            __(
                                'WP Idea: Warning! Your PHP version is quite old. Please update it immediately to guarantee your system works correctly all the time! You are currently using PHP version %s but recommended version is %s.',
                                BPMJ_EDDCM_DOMAIN
                            ),
                            WPI_PHP_VERSION(),
                            self::RECOMMENDED_PHP_VERSION
                        ); ?></p>
                </div>
            <?php
            endif;
        }

        private function get_container(): ContainerInterface
        {
            if(isset(self::$instance->container) && self::$instance->container instanceof ContainerInterface) {
                return self::$instance->container;
            }

            $container_builder = new ContainerBuilder();

            $container_builder->addDefinitions(BPMJ_EDDCM_DIR . '/config/container.php');
            $modules_dir = scandir(BPMJ_EDDCM_DIR . '/includes/modules');
            foreach ($modules_dir as $dir) {
                if (is_dir(BPMJ_EDDCM_DIR . '/includes/modules/' . $dir) &&
                    file_exists(BPMJ_EDDCM_DIR . '/includes/modules/' . $dir . '/container.php')) {
                    $container_builder->addDefinitions(BPMJ_EDDCM_DIR . '/includes/modules/' . $dir . '/container.php');
                }
            }

            return $container_builder->build();
        }

        private static function should_be_initialized_before_modules()
        {
            self::$instance->trial = new Trial();
            self::$instance->packages = new Packages();
        }
    }
}

if (!function_exists('WPI_PHP_VERSION')) {
    function WPI_PHP_VERSION()
    {
        $separator_position = strpos(PHP_VERSION, '-');
        if ($separator_position === false) {
            return PHP_VERSION;
        }

        return substr(PHP_VERSION, 0, $separator_position);
    }
}

if (!function_exists('WPI')) {
    /**
     *
     * @return BPMJ_WPI
     */
    function WPI()
    {
        return BPMJ_WPI::instance();
    }
}

if (!function_exists('WPI_API')) {
    /**
     *
     * @return WPI
     */
    function WPI_API()
    {
        return WPI::instance();
    }
}

if (version_compare(WPI_PHP_VERSION(), BPMJ_WPI::MINIMUM_PHP_VERSION, '<')) {
    /*
     * If PHP version is lower than MINIMUM_PHP_VERSION then we stop here
     */
    define('BPMJ_EDDCM_DIR', plugin_dir_path(__FILE__));
    define('BPMJ_EDDCM_DOMAIN', 'wp-idea');
    $lang_dir = basename(BPMJ_EDDCM_DIR) . '/languages';
    load_plugin_textdomain(BPMJ_EDDCM_DOMAIN, false, $lang_dir);

    function bpmj_eddcm_version_notice()
    {
        ?>
        <div class="error">

            <p>
                <?php
                printf(
                    __(
                        'WP Idea: You need at least PHP version %s to run this plugin. You are currently using PHP version %s.',
                        BPMJ_EDDCM_DOMAIN
                    ),
                    BPMJ_WPI::MINIMUM_PHP_VERSION,
                    WPI_PHP_VERSION()
                );
                ?>
            </p>
        </div>
        <?php
    }

    add_action('admin_notices', 'bpmj_eddcm_version_notice');
} else {
    // Get WPI Running
    WPI();

    if (!function_exists('bpmj_courses_updater')) {
        function bpmj_courses_updater()
        {
            global $wpidea_settings;

            $license_key = '';
            if (!empty($wpidea_settings['license_key'])) {
                $license_key = trim($wpidea_settings['license_key']);
            }

            new EDD_SL_Plugin_Updater(BPMJ_UPSELL_STORE_URL, __FILE__, [
                    'version' => BPMJ_EDDCM_VERSION,
                    'license' => $license_key,
                    'item_id' => BPMJ_EDDCM_ID,
                    'author' => 'upSell & Better Profits'
                ]
            );
        }

        $action = (defined('WP_CLI') && WP_CLI) ? 'init' : 'admin_init';
        add_action($action, 'bpmj_courses_updater');
    }
}


// Dodanie ustawień wyboru rabatu crosselingowego do bloku

global $post;

/**
 * Dodanie customowego pola do WP settings
 */

$MauriczCrosselingFileds = new MauriczCrosselingFileds();

class MauriczCrosselingFileds {
    function MauriczCrosselingFileds( ) {
        add_filter( 'admin_init' , array( &$this , 'register_fields' ) );
    }

    function register_fields() {
        register_setting( 'general', 'mauricz_crosseling_discount', 'esc_attr' );
        add_settings_field('mauricz_crosseling_discount', '<label for="mauricz_crosseling_discount">'.__('Wybierz rabat uwzględniany w crosselingu' , 'mauricz_crosseling_discount' ).'</label>' , array(&$this, 'fields_html') , 'general' );


        register_setting( 'general', 'mauricz_crosseling_category', 'esc_attr' );
         add_settings_field('mauricz_crosseling_category', '<label for="mauricz_crosseling_category">'.__('Wybierz kategorię z której mają być pokazywane produkty w sekcji crosseling' , 'mauricz_crosseling_category' ).'</label>' , array(&$this, 'fields_mauricz_categoryID') , 'general' );

         /**
          * Podstrona aktywacji uzytkownika
          */
          register_setting( 'general', 'mauricz_activation_page', 'esc_attr' );

          add_settings_field('mauricz_activation_page', '<label for="mauricz_activation_page">'.__('Wybierz stronę z informacją o aktywacji konta' , 'mauricz_activation_page' ).'</label>' , array(&$this, 'fields_mauricz_activation_pages') , 'general' );

          /**
           * Podstrona błędnej aktywacji uzytkownika
           */

          register_setting( 'general', 'mauricz_error_activation_page', 'esc_attr' );

          add_settings_field('mauricz_error_activation_page', '<label for="mauricz_error_activation_page">'.__('Wybierz stronę z informacją niepomyślnej aktywacji konta' , 'mauricz_error_activation_page' ).'</label>' , array(&$this, 'fields_mauricz_error_activation_pages') , 'general' );

           /**
            * Podstrona moje konto klienta
            */

            register_setting( 'general', 'mauricz_myaccount', 'esc_attr' );

            add_settings_field('mauricz_myaccount', '<label for="mauricz_myaccount">'.__('Wybierz stronę z informacją o koncie klienta (Moje konto)' , 'mauricz_myaccount' ).'</label>' , array(&$this, 'fields_mauricz_myaccount_pages') , 'general' );

            /**
             * Integracja klavyio - private api key
             */

             register_setting( 'general', 'mauricz_klavyio_api_private_key', 'esc_attr' );

             add_settings_field('mauricz_klavyio_api_private_key', '<label for="mauricz_klavyio_api_private_key">'.__('Podaj klucz prywatny API Klavyio' , 'mauricz_klavyio_api_private_key' ).'</label>' , array(&$this, 'mauricz_klavyio_api_private_key') , 'general' );
 
            /**
             * Integracja klavyio - public api key
             */

             register_setting( 'general', 'mauricz_klavyio_api_public_key', 'esc_attr' );

             add_settings_field('mauricz_klavyio_api_public_key', '<label for="mauricz_klavyio_api_public_key">'.__('Podaj klucz publiczny API Klavyio' , 'mauricz_klavyio_api_public_key' ).'</label>' , array(&$this, 'mauricz_klavyio_api_public_key') , 'general' );
 
            /**
             * Integracja klavyio - revision
             */

             register_setting( 'general', 'mauricz_klavyio_api_revision', 'esc_attr' );

             add_settings_field('mauricz_klavyio_api_revision', '<label for="mauricz_klavyio_api_revision">'.__('Podaj revision dla API Klavyio' , 'mauricz_klavyio_api_revision' ).'</label>' , array(&$this, 'mauricz_klavyio_api_revision') , 'general' );
             
            /**
             * Mauricz - darmowy kurs
             */

             register_setting( 'general', 'mauricz_product_free_course', 'esc_attr' );

             add_settings_field('mauricz_product_free_course', '<label for="mauricz_product_free_course">'.__('Wybierz darmowy kurs który ma być oferowany klientom' , 'mauricz_product_free_course' ).'</label>' , array(&$this, 'mauricz_product_free_course') , 'general' );


    }
    function fields_html() {
        $getDiscounts = edd_get_discounts(); // opcjonalnie dodaj  array( 'posts_per_page' => $per_page, 'paged' => $paged
        

        $value = get_option( 'mauricz_crosseling_discount', '' );

        $optionsDiscounts  = '';
        foreach ($getDiscounts as $key => $discount) {
            if($value == $discount->ID) { 
                $optionsDiscounts .= '<option selected="selected" value="'.$discount->ID.'">'.$discount->post_title.'</option>';  
            } else { 
                $optionsDiscounts .= '<option value="'.$discount->ID.'">'.$discount->post_title.'</option>';   
            }
        }

        echo '<select id="mauricz_crosseling_discount" name="mauricz_crosseling_discount">';
            echo $optionsDiscounts;
        echo '</select>';
        echo ' <p class="description">Mauricz - wybierz rabat do bloku crosseling</p>';
    }

    function fields_mauricz_activation_pages() { 
        $instance = 'mauricz_activation_page';
        $value = get_option($instance);
        $getPages = get_pages();
        // print_r($getPages);

        $optionsPages = '';
        
        foreach ($getPages as $key => $page) {
            if($value == $page->ID) { 
                $optionsPages .= '<option selected="selected" value="'.$page->ID.'">'.$page->post_title.'</option>';  
            } else { 
                $optionsPages .= '<option value="'.$page->ID.'">'.$page->post_title.'</option>';   
            }
        }

        echo '<select id="'.$instance.'" name="'.$instance.'">';
            echo $optionsPages;
        echo '</select>';
    }
    function fields_mauricz_error_activation_pages() { 
        $instance = 'mauricz_error_activation_page';
        $value = get_option($instance);
        $getPages = get_pages();
        // print_r($getPages);

        $optionsPages = '';
        
        foreach ($getPages as $key => $page) {
            if($value == $page->ID) { 
                $optionsPages .= '<option selected="selected" value="'.$page->ID.'">'.$page->post_title.'</option>';  
            } else { 
                $optionsPages .= '<option value="'.$page->ID.'">'.$page->post_title.'</option>';   
            }
        }

        echo '<select id="'.$instance.'" name="'.$instance.'">';
            echo $optionsPages;
        echo '</select>';
    }
    function fields_mauricz_myaccount_pages() { 
        $instance = 'mauricz_myaccount';

        $getPages = get_pages();
        // print_r($getPages);
        $value = get_option($instance);

        $optionsPages = '';
        
        foreach ($getPages as $key => $page) {
            if($value == $page->ID) { 
                $optionsPages .= '<option selected="selected" value="'.$page->ID.'">'.$page->post_title.'</option>';  
            } else { 
                $optionsPages .= '<option value="'.$page->ID.'">'.$page->post_title.'</option>';   
            }
        }

        echo '<select id="'.$instance.'" name="'.$instance.'">';
            echo $optionsPages;
        echo '</select>';
    }

    /**
     * Private api key klavyio
     */
    function mauricz_klavyio_api_private_key() { 
        $value = get_option('mauricz_klavyio_api_private_key');

        echo "<input type='text' id='mauricz_klavyio_api_private_key' name='mauricz_klavyio_api_private_key' class='regular-text' value='".$value."'/>";
    }

    /**
     * Public api key klavyio
     */
    function mauricz_klavyio_api_public_key() { 
        $value = get_option('mauricz_klavyio_api_public_key');

        echo "<input type='text' id='mauricz_klavyio_api_public_key' name='mauricz_klavyio_api_public_key' class='regular-text' value='".$value."'/>";
    }

    /**
     * Revision dla api klavyio
     */
    function mauricz_klavyio_api_revision() { 
        $value = get_option('mauricz_klavyio_api_revision');

        echo "<input type='text' id='mauricz_klavyio_api_revision' name='mauricz_klavyio_api_revision' class='regular-text' value='".$value."'/>";
    }

    /**
     * Produkt darmowe kursy do mapowania
     */
    function mauricz_product_free_course() { 
        $instance = 'mauricz_product_free_course';

        $args = array(
            'post_type'      => 'download',
            'posts_per_page' => 9999,
            'orderby'          => 'date',
            'order'            => 'DESC',
        );
        
        $getProducts = get_posts( $args );

        $value = get_option($instance);

        $optionsProducts = '';
        
        foreach ($getProducts as $key => $product) {
            if($value == $product->ID) { 
                $optionsProducts .= '<option selected="selected" value="'.$product->ID.'">'.$product->post_title.'</option>';  
            } else { 
                $optionsProducts .= '<option value="'.$product->ID.'">'.$product->post_title.'</option>';   
            }
        }

        echo '<select id="'.$instance.'" name="'.$instance.'">';
            echo $optionsProducts;
        echo '</select>';
    }

    function fields_mauricz_categoryID() { 

        $value = get_option( 'mauricz_crosseling_category', '' );

        $getPubligoCategories = get_terms( array(
            'taxonomy' => 'download_category',
            'hide_empty' => true
            ) );

        // print_r($getPubligoCategories);

        $optionsCategories = '';
        
        foreach ($getPubligoCategories as $key => $category) {
            if($value == $category->term_id) { 
                $optionsCategories .= '<option selected="selected" value="'.$category->term_id.'">'.$category->name.'</option>';  
            } else { 
                $optionsCategories .= '<option value="'.$category->term_id.'">'.$category->name.'</option>';   
            }
        }

        echo '<select id="mauricz_crosseling_category" name="mauricz_crosseling_category">';
            echo $optionsCategories;
        echo '</select>';

        echo '<p class="description">Wybierz kategorię z Publigo wg której mają pokazywać się produkty w bloku crosseling (widoczne są te które zawierają przynajmniej jeden produkt)</p>
        ';
    }
}

