<?php
/**
 * Plugin name: Innovative Solutions
 * Description: This is a custom plugin to display insight posts of the website for services, sub-services, industry page along with categorization of the post type
 * Version: 1.0
 * Requires at least: 5.6
 * Author: Kanchan Agarwal
 * Text Domain: innovative-solutions
 */

 /*
This is a custom WordPress plugin to display insight posts for services, sub-services, and industry pages, with categorization by post type. The plugin displays each post type in a grid layout with two rows — the first column spans the full width and includes a metabox for adding a video to the first post of each type. A total of four post types can be displayed.

On mobile devices, the grid automatically converts into a carousel for a better user experience.

Through the plugin’s settings page, users can generate filtered shortcodes based on industry, service, or sub-service. For each category, users can select up to three posts per post type to display.
*/

 if(! defined('ABSPATH')){
    die('You are not allowed');
 }
if(! class_exists('Innovative_Solutions')){
    class Innovative_Solutions{
        function __construct(){
            $this->define_constants();

            $this->load_textdomain();

            add_action('admin_menu',array($this,'add_menu'));

            require_once(INNOVATIVE_SOLUTIONS_PATH.'class.innovative-solutions-metabox.php');
            $Innovative_Solutions_Post_Metabox = new Innovative_Solutions_Post_Metabox();

            require_once(INNOVATIVE_SOLUTIONS_PATH.'shortcodes/class.innovative-solutions-shortcode.php');
            $Innovative_Solutions_Shortcode = new Innovative_Solutions_Shortcode();

            add_action('wp_enqueue_scripts',array($this,'register_scripts'),999);
        }
        public function define_constants(){
            define('INNOVATIVE_SOLUTIONS_PATH',plugin_dir_path(__FILE__));
            define('INNOVATIVE_SOLUTIONS_URL',plugin_dir_url(__FILE__));
            define('INNOVATIVE_SOLUTIONS_VERSION','1.0.0');
        }
        public static function activate(){
            update_option('rewrite rules','');
        }
        public static function deactivate(){
            flush_rewrite_rules();
        }
        public static function uninstall(){
            delete_option('innovative_solutions_options');
        }
        public function load_textdomain(){
            load_plugin_textdomain(
                'innovative-solutions',
                false,
                dirname(plugin_basename(__FILE__)).'/languages'
            );
        }
        public function add_menu(){
            add_menu_page(
                esc_html__('Innovative Solutions Options','innovative-solutions'),
                'Innovative Solutions',
                'manage_options',
                'innovative_solutions_admin',
                array($this, 'innovative_solutions_settings_page'),
                'dashicons-images-alt2',
            );
        }
        public function innovative_solutions_settings_page(){
            if(!current_user_can('manage_options')){
                return ;
            }            
            if(isset($_GET['settings-updated'])){
                add_settings_error('innovative_solutions_options','innovative_solutions_message',esc_html__('Settings saved','innovative-solutions'),'success');
            }
            settings_errors('innovative_solutions_options');
            require(INNOVATIVE_SOLUTIONS_PATH.'views/settings-page.php');
        }
        public function register_scripts(){
            wp_register_style('innovative-solutions-main-css',INNOVATIVE_SOLUTIONS_URL.'vendor/owlslider/owlSlider.css',array(),INNOVATIVE_SOLUTIONS_VERSION,'all');
            wp_register_script('innovative-solutions-main-js',INNOVATIVE_SOLUTIONS_URL.'vendor/owlslider/owlslider.js',array('jquery'),INNOVATIVE_SOLUTIONS_VERSION,true);

        }
        
    }
}
if(class_exists('Innovative_Solutions')){
    register_activation_hook(__FILE__,array('Innovative_Solutions','activate'));
    register_deactivation_hook(__FILE__,array('Innovative_Solutions','deactivate'));
    register_uninstall_hook(__FILE__,array('Innovative_Solutions','uninstall'));
   $innovative_solutions = new Innovative_Solutions();
}
