<?php
/**
 * Popup Manager utility functions
 *
 * @author WolfThemes
 * @category Core
 * @package WolfPopup/Frontend
 * @version 1.0.1
 */

defined( 'ABSPATH' ) || exit;

/**
 * Clean a list
 *
 * Remove first and last comma of a list and remove spaces before and after separator
 *
 * @param string $list
 * @return string $list
 */
function wolf_popup_clean_list( $list, $separator = ',' ) {
	$list = str_replace( array( $separator . ' ', ' ' . $separator ), $separator, $list );
	$list = ltrim( $list, $separator );
	$list = rtrim( $list, $separator );
	return $list;
}

/**
 * Remove all double spaces and line breaks
 *
 * This function is mainly used to clean up inline CSS
 *
 * @param string $css
 * @return string
 */
function wolf_popup_clean_spaces( $string, $hard = false ) {

	if ( $hard ) {
		return str_replace( ' ', '', $string );
	} else {
		return preg_replace( '/\s+/', ' ', $string );
	}
}

/**
 * Convert list to array
 *
 * @param string $list
 * @return array
 */
function wolf_popup_list_to_array( $list, $separator = ',' ) {
	return ( $list ) ? explode( ',', trim( wolf_popup_clean_spaces( wolf_popup_clean_list( $list ) ) ) ) : array();
}

/**
 * Convert array of ids to list
 *
 * @param string $list
 * @return array
 */
function wolf_popup_array_to_list( $array, $separator = ',' ) {
	$list = '';

	if ( is_array( $array ) ) {
		$list = rtrim( implode( $separator, array_unique( $array ) ), $separator );
	}

	return wolf_popup_clean_list( $list );
}

/**
 * Sanitize color input
 *
 * @link https://github.com/redelivre/wp-divi/blob/master/includes/functions/sanitization.php
 *
 * @param string $color
 * @return string $color
 */
function wolf_popup_sanitize_color( $color ) {

	// Trim unneeded whitespace
	$color = str_replace( ' ', '', $color );
	// If this is hex color, validate and return it
	if ( 1 === preg_match( '|^#([A-Fa-f0-9]{3}){1,2}$|', $color ) ) {
		return $color;
	}
	// If this is rgb, validate and return it
	elseif ( 'rgb(' === substr( $color, 0, 4 ) ) {
		sscanf( $color, 'rgb(%d,%d,%d)', $red, $green, $blue );
		if ( ( $red >= 0 && $red <= 255 ) &&
			 ( $green >= 0 && $green <= 255 ) &&
			 ( $blue >= 0 && $blue <= 255 )
			) {
			return "rgb({$red},{$green},{$blue})";
		}
	}
	// If this is rgba, validate and return it
	elseif ( 'rgba(' === substr( $color, 0, 5 ) ) {
		sscanf( $color, 'rgba(%d,%d,%d,%f)', $red, $green, $blue, $alpha );
		if ( ( $red >= 0 && $red <= 255 ) &&
			 ( $green >= 0 && $green <= 255 ) &&
			 ( $blue >= 0 && $blue <= 255 ) &&
			   $alpha >= 0 && $alpha <= 1
			) {
			return "rgba({$red},{$green},{$blue},{$alpha})";
		}
	} elseif ( 'transparent' === $color ) {
		return 'transparent';
	}
}

/**
 * Sanitize CSS from user intpu
 *
 * @param string $style
 * @return string
 */
function wolf_popup_sanitize_css_field( $style ) {

	if ( '' === $style ) {
		return;
	}

	if ( ';' !== substr( $style, -1) ) {
		$style = $style . ';'; // add end semicolon if missing
	}

	// remove double semicolon
	$style = str_replace( array( ';;', '; ;' ), '', $style );

	return esc_attr( trim( wolf_popup_clean_spaces( $style ) ) );
}

/**
 * Escape html style attribute
 *
 * @param string $style
 * @return string
 */
function wolf_popup_esc_style_attr( $style ) {

	if ( '' === $style ) {
		return;
	}

	if ( ';' !== substr( $style, -1) ) {
		$style = $style . ';'; // add end semicolon if missing
	}

	// remove double semicolon
	$style = str_replace( array( ';;', '; ;' ), '', $style );

	return esc_attr( trim( wolf_popup_clean_spaces( $style ) ) );
}

/**
 * Sanitize css value
 *
 * Be sure that the unit of a value ic correct (e.g: 100px)
 *
 * @param string $value
 * @param string $default_unit
 * @param string $default_value
 * @return string $value
 */
function wolf_popup_sanitize_css_value( $value, $default_unit = 'px', $default_value = '1' ) {

	$pattern = '/^(\d*(?:\.\d+)?)\s*(px|\%|in|cm|mm|em|rem|ex|pt|pc|vw|vh|vmin|vmax)?$/';
	// allowed metrics: http://www.w3schools.com/cssref/css_units.asp
	$regexr = preg_match( $pattern, $value, $matches );
	$value = isset( $matches[1] ) ? absint( $matches[1] ) : $default_value;
	$unit = isset( $matches[2] ) ? esc_attr( $matches[2] ) : $default_unit;
	$value = $value . $unit;

	return $value;
}

/**
 * sanitize_html_class works just fine for a single class
 * Some times le wild <span class="blue hedgehog"> appears, which is when you need this function,
 * to validate both blue and hedgehog,
 * Because sanitize_html_class doesn't allow spaces.
 *
 * @uses sanitize_html_class
 * @param (mixed: string/array) $class   "blue hedgehog goes shopping" or array("blue", "hedgehog", "goes", "shopping")
 * @param (mixed) $fallback Anything you want returned in case of a failure
 * @return (mixed: string / $fallback )
 */
function wolf_popup_sanitize_html_classes( $class, $fallback = null ) {

	// Explode it, if it's a string
	if ( is_string( $class ) ) {
		$class = explode( ' ', $class);
	}

	if ( is_array( $class ) && count( $class ) > 0 ) {
		$class = array_unique( array_map( 'sanitize_html_class', $class ) );
		return trim( implode( ' ', $class ) );
	}
	else {
		return trim( sanitize_html_class( $class, $fallback ) );
	}
}

/**
 * Straight from VC
 *
 * @param $content
 * @param bool $autop
 * @return string
 */
function wolf_popup_remove_wpautop( $content, $autop = false ) {

	if ( $autop ) { // Possible to use !preg_match('('.WPBMap::getTagsRegexp().')', $content)
		$content = wpautop( preg_replace( '/<\/?p\>/', "\n", $content ) . "\n" );
	}

	return do_shortcode( shortcode_unautop( $content ) );
}
