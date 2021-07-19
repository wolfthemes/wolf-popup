<?php
/**
 * Popup Manager core functions
 *
 * General core functions available on admin and frontend
 *
 * @author WolfThemes
 * @category Core
 * @package WolfPopup/Core
 * @version 1.0.1
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'wolf_popup_get_theme_slug' ) ) {
	/**
	 * Get the theme slug
	 *
	 * @return string
	 */
	function wolf_popup_get_theme_slug() {

		return apply_filters( 'wolftheme_theme_slug', esc_attr( sanitize_title_with_dashes( get_template() ) ) );
	}
}

/**
 * Gets the ID of the post, even if it's not inside the loop.
 *
 * @uses WP_Query
 * @uses get_queried_object()
 * @extends get_the_ID()
 * @see get_the_ID()
 *
 * @return int
 */
function wolf_popup_get_the_ID() {
	global $wp_query;

	$post_id = null;

	if ( function_exists( 'is_shop' ) && is_shop() ) {

		$post_id = get_option( 'woocommerce_shop_page_id' );

		// Get post ID outside the loop
	} elseif ( is_object( $wp_query ) && isset( $wp_query->queried_object ) && isset( $wp_query->queried_object->ID ) ) {

		$post_id = $wp_query->queried_object->ID;

	} else {
		$post_id = get_the_ID();
	}

	return $post_id;
}

/**
 * Get option
 *
 * Retrieve an option value from the plugin settings
 *
 * @param string $value
 * @param string $default
 * @return string
 */
function wolf_popup_get_option( $index, $name, $default = null ) {

	global $options;

	$wolf_popup_settings = ( get_option( 'wolf_popup_settings' ) && is_array( get_option( 'wolf_popup_settings' ) ) ) ? get_option( 'wolf_popup_settings' ) : array();

	if ( isset( $wolf_popup_settings[ $index ] ) && is_array( $wolf_popup_settings[ $index ] ) ) {

		if ( isset( $wolf_popup_settings[ $index ][ $name ] ) && '' != $wolf_popup_settings[ $index ][ $name ] ) {

			return $wolf_popup_settings[ $index ][ $name ];

		} elseif ( $default ) {

			return $default;
		}
	} elseif ( $default ) {

		return $default;
	}
}

/**
 * Update option index
 *
 * @param string $value
 * @param string $default
 * @return string
 */
function wolf_popup_update_option_index( $index, $options_array ) {

	$wolf_popup_settings = ( get_option( 'wolf_popup_settings' ) && is_array( get_option( 'wolf_popup_settings' ) ) ) ? get_option( 'wolf_popup_settings' ) : array();

	$wolf_popup_settings[ $index ] = $options_array;

	update_option( 'wolf_popup_settings', $wolf_popup_settings );
}
