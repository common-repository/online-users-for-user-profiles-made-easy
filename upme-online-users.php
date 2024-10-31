<?php
/*
  Plugin Name: Online Users for User Profiles Made Easy
  Plugin URI: http://upmeaddons.innovativephp.com/upme-online-users
  Description: Show online status of WordPress users
  Version: 1.0
  Author: Rakhitha Nimesh
  Author URI: http://www.wpexpertdeveloper.com
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

function uoua_get_plugin_version() {
    $default_headers = array('Version' => 'Version');
    $plugin_data = get_file_data(__FILE__, $default_headers, 'plugin');
    return $plugin_data['Version'];
}

/* Validating existence of required plugins */
add_action( 'plugins_loaded', 'uoua_plugin_init' );

function uoua_plugin_init(){
    if(!class_exists('UPME')){
        add_action( 'admin_notices', 'uoua_plugin_admin_notice' );
    }else{
        
    }
}

function uoua_plugin_admin_notice() {
   $message = __('<strong>UPME Online Users</strong> requires <strong>User Profiles Made Easy</strong> plugin to function properly','upmeinc');
   echo '<div class="error"><p>'.$message.'</p></div>';
}

if( !class_exists( 'UPME_Online_Users' ) ) {
    
    class UPME_Online_Users{
    
        private static $instance;

        public static function instance() {
            
            if ( ! isset( self::$instance ) && ! ( self::$instance instanceof UPME_Online_Users ) ) {
                self::$instance = new UPME_Online_Users();
                self::$instance->setup_constants();

                add_action( 'plugins_loaded', array( self::$instance, 'load_textdomain' ) );
                self::$instance->includes();
                
                add_action('admin_enqueue_scripts',array(self::$instance,'load_admin_scripts'),9);
                add_action('wp_enqueue_scripts',array(self::$instance,'load_scripts'),9);
                 
                self::$instance->template_loader    = new UOUA_Template_Loader();
                self::$instance->settings           = new UOUA_Settings();
                self::$instance->online_users       = new UOUA_Online_Users();
            }
            return self::$instance;
        }

        public function setup_constants() { }
        
        public function load_scripts(){ 
            wp_register_style('uoua-front-css', UOUA_PLUGIN_URL . 'css/uoua-custom.css');
            wp_enqueue_style('uoua-front-css');
        }
        
        public function load_admin_scripts(){
            
        }
        
        private function includes() {
            
            require_once UOUA_PLUGIN_DIR . 'functions.php';
            require_once UOUA_PLUGIN_DIR . 'classes/class-uoua-template-loader.php';      
            require_once UOUA_PLUGIN_DIR . 'classes/class-uoua-settings.php'; 
            require_once UOUA_PLUGIN_DIR . 'classes/class-uoua-online-users.php'; 

            if ( is_admin() ) {
            }
        }

        public function load_textdomain() {
            
        }
        
    }
}

// Plugin version
if ( ! defined( 'UOUA_VERSION' ) ) {
    define( 'UOUA_VERSION', '1.0' );
}

// Plugin Folder Path
if ( ! defined( 'UOUA_PLUGIN_DIR' ) ) {
    define( 'UOUA_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}

// Plugin Folder URL
if ( ! defined( 'UOUA_PLUGIN_URL' ) ) {
    define( 'UOUA_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}




function UPME_Online_Users() {
    global $uoua;
    $uoua = UPME_Online_Users::instance();
}

UPME_Online_Users();





