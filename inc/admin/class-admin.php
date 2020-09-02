<?php
/**
 * Popup Manager Admin.
 *
 * @class Wolf_Popup_Admin
 * @author WolfThemes
 * @category Admin
 * @package WolfPopup/Admin
 * @version 1.0.1
 */

defined( 'ABSPATH' ) || exit;

/**
 * Wolf_Popup_Admin class.
 */
class Wolf_Popup_Admin {
	/**
	 * Constructor
	 */
	public function __construct() {

		// Includes files
		$this->includes();

		// Admin init hooks
		$this->admin_init_hooks();
	}

	/**
	 * Include any classes we need within admin.
	 */
	public function includes() {
		include_once( 'class-options.php' );
		include_once( 'admin-options.php' );
	}

	/**
	 * Admin init
	 */
	public function admin_init_hooks() {

		// Plugin settings link
		add_filter( 'plugin_action_links_' . plugin_basename( WPOPUP_PATH ), array( $this, 'settings_action_links' ) );
	}

	/**
	 * Add settings link in plugin page
	 */
	public function settings_action_links( $links ) {
		$setting_link = array(
			'<a href="' . admin_url( 'themes.php?page=wolf-popup-settings' ) . '">' . esc_html__( 'Settings', 'wolf-popup' ) . '</a>',
		);
		return array_merge( $links, $setting_link );
	}
}

return new Wolf_Popup_Admin();
