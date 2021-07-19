<?php
/**
 * Plugin Name: Popup Manager
 * Plugin URI: http://wolfthemes.com/plugin/wolf-popup
 * Description: A WordPress plugin to manage popups.
 * Version: 1.0.6
 * Author: WolfThemes
 * Author URI: http://wolfthemes.com
 * Requires at least: 5.0
 * Tested up to: 5.5
 *
 * Text Domain: wolf-popup
 * Domain Path: /languages/
 *
 * @package WolfPopup
 * @category Core
 * @author WolfThemes
 *
 * Help:
 * https://wlfthm.es/help
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Wolf_Popup' ) ) {
	/**
	 * Main Wolf_Popup Class
	 *
	 * Contains the main functions for Wolf_Popup
	 *
	 * @class Wolf_Popup
	 * @version 1.0.6
	 * @since 1.0.0
	 */
	class Wolf_Popup {

		/**
		 * @var string
		 */
		public $version = '1.0.6';

		/**
		 * @var Popup Manager The single instance of the class
		 */
		protected static $_instance = null;

		/**
		 * @var string
		 */
		private $update_url = 'https://plugins.wolfthemes.com/update';

		/**
		 * Main Popup Manager Instance
		 *
		 * Ensures only one instance of Popup Manager is loaded or can be loaded.
		 *
		 * @static
		 * @see WSHARE()
		 * @return Popup Manager - Main instance
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Popup Manager Constructor.
		 */
		public function __construct() {

			$this->define_constants();
			$this->includes();
			$this->init_hooks();

			do_action( 'wolf_popup_loaded' );
		}

		/**
		 * Hook into actions and filters
		 */
		private function init_hooks() {
			register_activation_hook( __FILE__, array( $this, 'activate' ) );
			add_action( 'init', array( $this, 'init' ), 0 );

			add_action( 'admin_init', array( $this, 'plugin_update' ) );
		}

		/**
		 * Activation function
		 */
		public function activate() {

			do_action( 'wolf_popup_activated' );
		}

		/**
		 * Define WPB Constants
		 */
		private function define_constants() {

			$constants = array(
				'WPOPUP_DEV' => false,
				'WPOPUP_DIR' => $this->plugin_path(),
				'WPOPUP_URI' => $this->plugin_url(),
				'WPOPUP_CSS' => $this->plugin_url() . '/assets/css',
				'WPOPUP_JS' => $this->plugin_url() . '/assets/js',
				'WPOPUP_IMG' => $this->plugin_url() . '/assets/img',
				'WPOPUP_SLUG' => plugin_basename( dirname( __FILE__ ) ),
				'WPOPUP_PATH' => plugin_basename( __FILE__ ),
				'WPOPUP_VERSION' => $this->version,
				'WPOPUP_UPDATE_URL' => $this->update_url,
				'WPOPUP_DOC_URI' => 'https://docs.wolfthemes.com/documentation/plugins/' . plugin_basename( dirname( __FILE__ ) ),
				'WPOPUP_WOLF_DOMAIN' => 'wolfthemes.com',
			);

			foreach ( $constants as $name => $value ) {
				$this->define( $name, $value );
			}

			// var_dump( WPB_UPLOAD_URI );
		}

		/**
		 * Define constant if not already set
		 * @param  string $name
		 * @param  string|bool $value
		 */
		private function define( $name, $value ) {
			if ( ! defined( $name ) ) {
				define( $name, $value );
			}
		}

		/**
		 * What type of request is this?
		 * string $type ajax, frontend or admin
		 * @return bool
		 */
		private function is_request( $type ) {
			switch ( $type ) {
				case 'admin' :
					return is_admin();
				case 'ajax' :
					return defined( 'DOING_AJAX' );
				case 'cron' :
					return defined( 'DOING_CRON' );
				case 'frontend' :
					return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
			}
		}

		/**
		 * Include required core files used in admin and on the frontend.
		 */
		public function includes() {

			include_once( 'inc/core-functions.php' );
			include_once( 'inc/frontend/utility-functions.php' );

			if ( $this->is_request( 'admin' ) ) {
				include_once( 'inc/admin/class-admin.php' );
			}

			if ( $this->is_request( 'ajax' ) ) {

			}

			if ( $this->is_request( 'frontend' ) ) {
				include_once( 'inc/frontend/conditional-functions.php' );
				include_once( 'inc/frontend/frontend-functions.php' );
			}
		}

		/**
		 * Init Popup Manager when WordPress Initialises.
		 */
		public function init() {
			// Before init action
			do_action( 'before_wolf_popup_init' );

			// Set up localisation
			$this->load_plugin_textdomain();

			// Init action
			do_action( 'wolf_popup_init' );
		}

		/**
		 * Loads the plugin text domain for translation
		 */
		public function load_plugin_textdomain() {

			$domain = 'wolf-popup';
			$locale = apply_filters( 'wolf-popup', get_locale(), $domain );
			load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
			load_plugin_textdomain( $domain, FALSE, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		}

		/**
		 * Get the plugin url.
		 * @return string
		 */
		public function plugin_url() {
			return untrailingslashit( plugins_url( '/', __FILE__ ) );
		}

		/**
		 * Get the plugin path.
		 * @return string
		 */
		public function plugin_path() {
			return untrailingslashit( plugin_dir_path( __FILE__ ) );
		}

		/**
		 * Plugin update
		 */
		public function plugin_update() {

			if ( ! class_exists( 'WP_GitHub_Updater' ) ) {
				include_once 'inc/admin/updater.php';
			}

			$repo = 'wolfthemes/wolf-popup';

			$config = array(
				'slug' => plugin_basename( __FILE__ ),
				'proper_folder_name' => 'wolf-popup',
				'api_url' => 'https://api.github.com/repos/' . $repo . '',
				'raw_url' => 'https://raw.github.com/' . $repo . '/master/',
				'github_url' => 'https://github.com/' . $repo . '',
				'zip_url' => 'https://github.com/' . $repo . '/archive/master.zip',
				'sslverify' => true,
				'requires' => '5.0',
				'tested' => '5.5',
				'readme' => 'README.md',
				'access_token' => '',
			);

			new WP_GitHub_Updater( $config );
		}
	}
}
/**
 * Returns the main instance of WOLFPOPUP to prevent the need to use globals.
 *
 * @return Wolf_Popup
 */
function WOLFPOPUP() {
	return Wolf_Popup::instance();
}

WOLFPOPUP(); // Go

