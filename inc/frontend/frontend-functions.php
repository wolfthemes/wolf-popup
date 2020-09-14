<?php
/**
 * Popup Manager Frontend Functions
 *
 * @author WolfThemes
 * @category Core
 * @package WolfPopup/Functions
 * @since 10.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqeue styles and scripts
 */
function wolf_popup_enqueue_scripts() {

	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
	$version = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? time() : WPOPUP_VERSION;

	/* Don't serve minified JS files if Autoptimize plugin is activated */
	if ( defined( 'AUTOPTIMIZE_PLUGIN_DIR' ) ) {
		$suffix = '';
	}

	// Styles
	wp_enqueue_style( 'wolf-popup', WPOPUP_CSS . '/popup' . $suffix . '.css', array(), $version, 'all' );

	// Scripts
	wp_enqueue_script( 'wolf-popup', WPOPUP_JS . '/popup' . $suffix . '.js', array( 'jquery' ), $version, true );

	// Add JS global variables
	wp_localize_script(
		'wolf-popup', 'WolfPopupParams', array(
			'themeSlug' => wolf_popup_get_theme_slug(),
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'isMobile' => wp_is_mobile(),
			'themeSlug' => wolf_popup_get_theme_slug(),
		)
	);
}
add_action( 'wp_enqueue_scripts',  'wolf_popup_enqueue_scripts' );

/**
 * Output time delayed popup
 */
function wolf_popup_output_time_delayed_popup( $atts = array() ) {

	$atts = apply_filters( 'wolf_popup_time_delayed_atts', wp_parse_args( $atts, array(
		'type' => wolf_popup_get_option( 'time-delayed', 'type', 'full' ),
		'position' => wolf_popup_get_option( 'time-delayed', 'position', 'bottom-left' ),
		'page_id' => wolf_popup_get_option( 'time-delayed', 'page_id' ),
		'content_width' => wolf_popup_get_option( 'time-delayed', 'content_width', 500 ),
		'delay' => wolf_popup_get_option( 'time-delayed', 'delay', 15 ),
		'show_count' => wolf_popup_get_option( 'time-delayed', 'show_count', 2 ),
		'cookie_time' => wolf_popup_get_option( 'time-delayed', 'cookie_time', 1 ),
		'include_post_types' => wolf_popup_get_option( 'time-delayed', 'include_post_types' ),
		'exclude_post_types' => wolf_popup_get_option( 'time-delayed', 'exclude_post_types' ),
		'include_ids' => wolf_popup_get_option( 'time-delayed', 'include_ids' ),
		'exclude_ids' => wolf_popup_get_option( 'time-delayed', 'exclude_ids' ),
		'exclude_mc_subs' => wolf_popup_get_option( 'time-delayed', 'exclude_mc_subs' ),
		'close_button_color' => wolf_popup_get_option( 'time-delayed', 'close_button_color' ),
		'dev_mode' => wolf_popup_get_option( 'time-delayed', 'dev_mode', false ),
		'disable_mobile' => wolf_popup_get_option( 'time-delayed', 'disable_mobile', false ),
	) ) );

	extract( $atts );

	/* filter to force disabling popup */
	$enabled = apply_filters( 'wolf_popup_time_delayed_enabled', true );

	if ( ! $enabled ) {
		return;
	}

	if ( $dev_mode && ! current_user_can( 'manage_options' ) ) {
		return;
	}

	if ( $disable_mobile && wp_is_mobile() ) {
		return;
	}

	$current_post_id = wolf_popup_get_the_ID();
	$post_type = get_post_type( $current_post_id );
	$show_count = ( $show_count ) ? absint( $show_count ) : 2;

	if ( apply_filters( 'wolf_popup_hide_time_delayed', false ) ) {
		return;
	}

	if ( wolf_popup_is_user_mc_sub() && $exclude_mc_subs && ! wolf_popup_is_user_admin() ) {
		return;
	}

	if ( is_404() || wolf_popup_is_maintenance_page() ) {
		return;
	}

	if ( wolf_popup_is_woocommerce_page() ) {
		if (
			is_checkout() ||
			is_account_page() ) {
			return;
		}
	}

	if ( ! $page_id ) {
		return;
	}

	if ( $current_post_id == $page_id ) {
		return;
	}

	if ( $include_ids ) {
		$include_ids = wolf_popup_list_to_array( $include_ids );

		if ( ! in_array( $current_post_id, $include_ids ) ) {
			return;
		}
	}

	if ( $include_post_types ) {
		$include_post_types = wolf_popup_list_to_array( $include_post_types );

		if ( ! in_array( $post_type, $include_post_types ) ) {
			return;
		}
	}

	if ( $exclude_post_types ) {
		$exclude_post_types = wolf_popup_list_to_array( $exclude_post_types );

		if ( in_array( $post_type, $exclude_post_types ) ) {
			return;
		}
	}

	if ( $exclude_ids ) {
		$exclude_ids = wolf_popup_list_to_array( $exclude_ids );

		if ( in_array( $current_post_id, $exclude_ids ) ) {
			return;
		}
	}

	$inline_style = $close_inline_style = $container_inline_style = '';

	$content_width = wolf_popup_sanitize_css_value( $content_width );

	$inline_style .= "max-width:$content_width;";

	if ( $close_button_color ) {
		$close_inline_style .= 'color:' . wolf_popup_sanitize_color( $close_button_color ) . ';';
	}

	if ( 'non_intrusive' === $type ) {
		$container_inline_style .= "max-width:$content_width;";
		$inline_style = '';
	}

	ob_start();
	?>
	<div id="wolf-popup-overlay-time-delayed" data-wolf-popup-type="time-delayed" data-wolf-popup-cookie-time="<?php echo absint( $cookie_time ); ?>" data-wolf-popup-delay="<?php echo absint( $delay ); ?>"  data-wolf-popup-count="<?php echo absint( $show_count ); ?>" style="<?php echo wolf_popup_esc_style_attr( $container_inline_style ); ?>" class="wolf-popup-overlay  <?php echo wolf_popup_sanitize_html_classes( array( 'wolf-popup-type-' . $type, 'wolf-popup-position-'  . $position ) ); ?>">

		<?php if ( 'full' === $type ) : ?>
			<div class="wolf-popup-mask wolf-popup-close wolf-popup-close-button"></div>
		<?php endif; ?>

		<div class="wolf-popup-container" style="<?php echo wolf_popup_esc_style_attr( $inline_style ); ?>">
			<div class="wolf-popup-content">
				<a style="<?php echo wolf_popup_esc_style_attr( $close_inline_style ); ?>" href="#" id="wolf-popup-close" class="wolf-popup-close wolf-popup-close-button <?php echo ( 1 === $show_count ) ? 'wolf-popup-close-opt-out' : ''; ?>">X</a>
				<div class="wolf-popup-inner">
					<div id="wolf-popup-time-delayed" class="wolf-popup">
						<?php
							/**
							 * Page Content
							 */
							echo wolf_popup_remove_wpautop( get_post_field( 'post_content', $page_id ) );
						?>
					</div>
				</div>
			</div>
			<?php if ( 1 < $show_count ) : ?>
				<span class="wolf-popup-close wolf-popup-close-opt-out wolf-popup-bottom-close"><?php esc_html_e( 'Don\'t show this message again', 'wolf-popup' ); ?></span>
			<?php endif; ?>
		</div>
	</div>
	<?php
	echo apply_filters( 'wolf_popup_time_delayed_output', ob_get_clean(), $atts );

}
add_action( 'wolf_body_start', 'wolf_popup_output_time_delayed_popup' );

/**
 * Output time delayed popup
 */
function wolf_popup_output_exit_intent_popup( $atts = array() ) {

	$atts = apply_filters( 'wolf_popup_exit_intent_atts', wp_parse_args( $atts, array(
		'type' => wolf_popup_get_option( 'exit-intent', 'type', 'full' ),
		'page_id' => wolf_popup_get_option( 'exit-intent', 'page_id' ),
		'content_width' => wolf_popup_get_option( 'exit-intent', 'content_width', 650 ),
		'show_count' => wolf_popup_get_option( 'exit-intent', 'show_count', 2 ),
		'delay' => wolf_popup_get_option( 'exit-intent', 'delay', 5 ),
		'cookie_time' => wolf_popup_get_option( 'exit-intent', 'cookie_time', 1 ),
		'include_post_types' => wolf_popup_get_option( 'exit-intent', 'include_post_types' ),
		'exclude_post_types' => wolf_popup_get_option( 'exit-intent', 'exclude_post_types' ),
		'include_ids' => wolf_popup_get_option( 'exit-intent', 'include_ids' ),
		'exclude_ids' => wolf_popup_get_option( 'exit-intent', 'exclude_ids' ),
		'exclude_mc_subs' => wolf_popup_get_option( 'exit-intent', 'exclude_mc_subs' ),
		'close_button_color' => wolf_popup_get_option( 'exit-intent', 'close_button_color' ),
		'dev_mode' => wolf_popup_get_option( 'exit-intent', 'dev_mode', false ),
		'disable_mobile' => wolf_popup_get_option( 'exit-intent', 'disable_mobile', false ),
	) ) );

	extract( $atts );

	/* filter to force disabling popup */
	$enabled = apply_filters( 'wolf_popup_exit_intent_enabled', true );

	if ( ! $enabled ) {
		return;
	}

	if ( $dev_mode && ! current_user_can( 'manage_options' ) ) {
		return;
	}

	if ( $disable_mobile && wp_is_mobile() ) {
		return;
	}

	$current_post_id = wolf_popup_get_the_ID();
	$post_type = get_post_type( $current_post_id );
	$show_count = ( $show_count ) ? absint( $show_count ) : 2;

	if ( apply_filters( 'wolf_popup_hide_exit_intent', false ) ) {
		return;
	}

	if ( wolf_popup_is_user_mc_sub() && $exclude_mc_subs && ! wolf_popup_is_user_admin() ) {
		return;
	}

	if ( is_404() || wolf_popup_is_maintenance_page() ) {
		return;
	}

	if ( wolf_popup_is_woocommerce_page() ) {
		if (
			is_checkout() ||
			is_account_page() ) {
			return;
		}
	}

	if ( ! $page_id ) {
		return;
	}

	if ( $current_post_id == $page_id ) {
		return;
	}

	if ( $include_ids ) {
		$include_ids = wolf_popup_list_to_array( $include_ids );

		if ( ! in_array( $current_post_id, $include_ids ) ) {
			return;
		}
	}

	if ( $include_post_types ) {
		$include_post_types = wolf_popup_list_to_array( $include_post_types );

		if ( ! in_array( $post_type, $include_post_types ) ) {
			return;
		}
	}

	if ( $exclude_post_types ) {
		$exclude_post_types = wolf_popup_list_to_array( $exclude_post_types );

		if ( in_array( $post_type, $exclude_post_types ) ) {
			return;
		}
	}

	if ( $exclude_ids ) {
		$exclude_ids = wolf_popup_list_to_array( $exclude_ids );

		if ( in_array( $current_post_id, $exclude_ids ) ) {
			return;
		}
	}

	$inline_style = $close_inline_style = $container_inline_style = '';

	$content_width = wolf_popup_sanitize_css_value( $content_width );

	$inline_style .= "max-width:$content_width;";

	if ( $close_button_color ) {
		$close_inline_style .= 'color:' . wolf_popup_sanitize_color( $close_button_color ) . ';';
	}

	if ( 'non_intrusive' === $type ) {
		$container_inline_style .= "max-width:$content_width;";
		$inline_style = '';
	}

	ob_start();
	?>
	<div id="wolf-popup-overlay-exit-intent" data-wolf-popup-type="exit-intent" data-wolf-popup-cookie-time="<?php echo absint( $cookie_time ); ?>" data-wolf-popup-delay="<?php echo absint( $delay ); ?>" data-wolf-popup-count="<?php echo absint( $show_count ); ?>" style="<?php echo wolf_popup_esc_style_attr( $container_inline_style ); ?>" class="wolf-popup-overlay  <?php echo wolf_popup_sanitize_html_classes( 'wolf-popup-type-' . $type ); ?>">

		<?php if ( 'full' === $type ) : ?>
			<div class="wolf-popup-mask wolf-popup-close wolf-popup-close-button"></div>
		<?php endif; ?>

		<div class="wolf-popup-container" style="<?php echo wolf_popup_esc_style_attr( $inline_style ); ?>">
			<div class="wolf-popup-content">
				<a style="<?php echo wolf_popup_esc_style_attr( $close_inline_style ); ?>" href="#" id="wolf-popup-close" class="wolf-popup-close wolf-popup-close-button <?php echo ( 1 === $show_count ) ? 'wolf-popup-close-opt-out' : ''; ?>">X</a>
				<div class="wolf-popup-inner">
					<div id="wolf-popup-exit-intent" class="wolf-popup">
						<?php
							/**
							 * Page Content
							 */
							echo wolf_popup_remove_wpautop( get_post_field( 'post_content', $page_id ) );
						?>
					</div>
				</div>
			</div>
			<?php if ( 1 < $show_count ) : ?>
				<span class="wolf-popup-close wolf-popup-close-opt-out wolf-popup-bottom-close"><?php esc_html_e( 'Don\'t show this message again', 'wolf-popup' ); ?></span>
			<?php endif; ?>
		</div>
	</div>
	<?php
	echo apply_filters( 'wolf_popup_exit_intent_output', ob_get_clean(), $atts );

}
add_action( 'wolf_body_start', 'wolf_popup_output_exit_intent_popup' );
