<?php
/**
 * Popup Manager frontend functions
 *
 * General core functions available on admin.and frontend
 *
 * @author WolfThemes
 * @category Core
 * @package WolfPopup/Frontend
 * @version 1.0.1
 */

defined( 'ABSPATH' ) || exit;

/**
 * Maintenance
 */
function wolf_popup_is_maintenance_page() {
	
	$wolf_maintenance_settings = get_option( 'wolf_maintenance_settings' );
	$maintenance_page_id = ( isset( $wolf_maintenance_settings[ 'page_id' ] ) ) ? $wolf_maintenance_settings[ 'page_id' ] : null;

	if ( $maintenance_page_id && is_page( $maintenance_page_id ) ) {
		return true;
	}
}

/**
 * Check if we are on a woocommerce page
 *
 * @return bool
 */
function wolf_popup_is_woocommerce_page() {

	if ( class_exists( 'WooCommerce' ) ) {

		if ( is_woocommerce() ) {
			return true;
		}

		if ( is_shop() ) {
			return true;
		}

		if ( is_checkout() || is_order_received_page() ) {
			return true;
		}

		if ( is_cart() ) {
			return true;
		}

		if ( is_account_page() ) {
			return true;
		}

		if ( function_exists( 'wolf_wishlist_get_page_id' ) && is_page( wolf_wishlist_get_page_id() ) ) {
			return true;
		}
	}
}

/**
 * Check if use is MC sub
 */
function wolf_popup_is_user_mc_sub() {
	if ( get_user_meta( get_current_user_id(), 'user_mc_subscriber_status', true ) === 'yes' ) {
		return true;
	}
}

/**
 * User is admin
 */
function wolf_popup_is_user_admin() {
	return current_user_can( 'manage_options' );
}