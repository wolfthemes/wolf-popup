<?php
/**
 * Popup Manager Plugin Settings
 *
 * @author WolfThemes
 * @category Core
 * @package WolfPopup/Admin
 * @version 1.0.1
 */

defined( 'ABSPATH' ) || exit;


$wolf_popup_options = array(
	array(
		'title' => esc_html__( 'Time Delayed', 'wolf-popup' ),
		'settings_id' => 'wolf-popup-time-delayed',
		'settings_slug' => 'time-delayed',
		'fields' => array(

			array(
				'type' => 'page',
				'field_id' => 'page_id',
				'label' => esc_html__( 'Content', 'wolf-popup' ),
				'description' => esc_html__( 'Choose the page you want to use as your pop-up content.', 'wolf-popup' ),
			),

			array(
				'type' => 'checkbox',
				'field_id' => 'dev_mode',
				'label' => esc_html__( 'Test mode', 'wolf-popup' ),
				'description' => esc_html__( 'It will only show the pop-up to logged-in admin users.', 'wolf-popup' ),
			),

			array(
				'type' => 'checkbox',
				'field_id' => 'disable_mobile',
				'label' => esc_html__( 'Disable on Mobile', 'wolf-popup' ),
				'description' => esc_html__( 'It is recommended as Google tends to penalize websites that uses pop-ups on mobile.', 'wolf-popup' ),
			),

			array(
				'type' => 'select',
				'field_id' => 'type',
				'label' => esc_html__( 'Type', 'wolf-popup' ),
				'choices' => array(
					'full' => esc_html__( 'Standard', 'wolf-popup' ),
					'non_intrusive' => esc_html__( 'Non-Intrusive (bottom left corner)', 'wolf-popup' ),
				),
			),

			array(
				'type' => 'checkbox',
				'field_id' => 'exclude_mc_subs',
				'label' => esc_html__( 'Exclude MailChimp subscribers', 'wolf-popup' ),
				'description' => sprintf( esc_html__( 'Works only if %s is installed and if the %s is used to collect subscribers.', 'wolf-popup' ), 'WPBakery Page Builder Extension by WolfThemes', 'MailChimp Page Builder Element' ),
			),

			array(
				'type' => 'text',
				'field_id' => 'delay',
				'label' => esc_html__( 'Delay', 'wolf-popup' ),
				'placeholder' => 3,
				'description' => wp_kses_post( __( 'The time before the modal window pops up in seconds.<br>You can set a very high number to prevent the window to pop-up automatically if you want it to be open via a link (see the "info" section at the bottom of this page).', 'wolf-popup' ) ),
			),

			array(
				'type' => 'text',
				'field_id' => 'show_count',
				'placeholder' => 2,
				'label' => esc_html__( 'Display Count', 'wolf-popup' ),
				'description' => esc_html__( 'How many time the pop-up should appear (if not opted out).', 'wolf-popup' ),
			),

			array(
				'type' => 'text',
				'field_id' => 'cookie_time',
				'label' => esc_html__( 'Cookie Persistency', 'wolf-popup' ),
				'placeholder' => 1,
				'description' => esc_html__( 'How long the browser will remember the user opt-out action (in days).', 'wolf-popup' ),
			),

			array(
				'type' => 'text',
				'field_id' => 'content_width',
				'label' => esc_html__( 'Modal Window Width', 'wolf-popup' ),
				'placeholder' => '960px',
			),

			array(
				'type' => 'text',
				'field_id' => 'exclude_post_types',
				'label' => esc_html__( 'Exclude Post Types', 'wolf-popup' ),
				'placeholder' => 'post,page',
				'description' => esc_html__( 'The modal window will NOT popup in these specific post types. Separate each post type by a comma.', 'wolf-popup' ),
			),

			array(
				'type' => 'text',
				'field_id' => 'include_post_types',
				'label' => esc_html__( 'Include Post Types', 'wolf-popup' ),
				'placeholder' => 'post,page',
				'description' => esc_html__( 'The modal window will popup ONLY in these specific post types. Separate each post type by a comma.', 'wolf-popup' ),
			),

			array(
				'type' => 'text',
				'field_id' => 'exclude_ids',
				'label' => esc_html__( 'Exclude Post IDs', 'wolf-popup' ),
				'placeholder' => '654,897,123',
				'description' => esc_html__( 'The modal window will NOT popup in these specific posts. Separate each ID by a comma.', 'wolf-popup' ),
			),

			array(
				'type' => 'text',
				'field_id' => 'include_ids',
				'label' => esc_html__( 'Include Post IDs', 'wolf-popup' ),
				'placeholder' => '654,897,123',
				'description' => esc_html__( 'The modal window will popup ONLY in these specific posts. Separate each ID by a comma.', 'wolf-popup' ),
			),

			array(
				'type' => 'colorpicker',
				'field_id' => 'close_button_color',
				'label' => esc_html__( 'Close Button Color', 'wolf-popup' ),
			),

			array(
				'type' => 'message',
				'field_id' => 'info',
				'label' => esc_html__( 'Info', 'wolf-popup' ),
				'description' =>  sprintf(
					wp_kses_post( __( 'You can add the <strong>"%s"</strong> class to any link to use it as pop-up window "close" button.<br>Also add the <strong>"%s"</strong> class to use it as opt-out button. ("Don\'t show this message again" link)<br>Additionally, if you need a link that open the modal window, you can use the <strong>"%s"</strong> class.', 'wolf-popup' ) ),
					'wolf-popup-close',
					'wolf-popup-opt-out',
					'wolf-popup-open'
				),
			),
		),
	),

	array(
		'title' => esc_html__( 'Exit Intent', 'wolf-popup' ),
		'settings_id' => 'wolf-popup-exit-intent',
		'settings_slug' => 'exit-intent',
		'fields' => array(

			array(
				'type' => 'page',
				'field_id' => 'page_id',
				'label' => esc_html__( 'Content', 'wolf-popup' ),
				'description' => esc_html__( 'Choose the page you want to use as your pop-up content.', 'wolf-popup' ),
			),

			array(
				'type' => 'checkbox',
				'field_id' => 'dev_mode',
				'label' => esc_html__( 'Test mode', 'wolf-popup' ),
				'description' => esc_html__( 'It will only show the pop-up to logged-in admin users.', 'wolf-popup' ),
			),

			array(
				'type' => 'checkbox',
				'field_id' => 'disable_mobile',
				'label' => esc_html__( 'Disable on Mobile', 'wolf-popup' ),
				'description' => esc_html__( 'It is recommended as Google tends to penalize websites that uses pop-ups on mobile.', 'wolf-popup' ),
			),

			array(
				'type' => 'select',
				'field_id' => 'type',
				'label' => esc_html__( 'Type', 'wolf-popup' ),
				'choices' => array(
					'full' => esc_html__( 'Standard', 'wolf-popup' ),
					'non_intrusive' => esc_html__( 'Non-Intrusive (bottom right corner)', 'wolf-popup' ),
				),
			),

			array(
				'type' => 'checkbox',
				'field_id' => 'exclude_mc_subs',
				'label' => esc_html__( 'Exclude MailChimp subscribers', 'wolf-popup' ),
				'description' => sprintf( esc_html__( 'Works only if %s is installed and if the %s is used to collect subscribers.', 'wolf-popup' ), 'WPBakery Page Builder Extension by WolfThemes', 'MailChimp Page Builder Element' ),
			),

			array(
				'type' => 'text',
				'field_id' => 'show_count',
				'placeholder' => 2,
				'label' => esc_html__( 'Display Count', 'wolf-popup' ),
				'description' => esc_html__( 'How many time the pop-up should appear (if not opted out).', 'wolf-popup' ),
			),

			array(
				'type' => 'text',
				'field_id' => 'delay',
				'label' => esc_html__( 'Delay', 'wolf-popup' ),
				'placeholder' => 5,
				'description' => esc_html__( 'The time before the modal window pops up in seconds.', 'wolf-popup' ),
			),

			array(
				'type' => 'text',
				'field_id' => 'cookie_time',
				'label' => esc_html__( 'Cookie Persistency', 'wolf-popup' ),
				'placeholder' => 1,
				'description' => esc_html__( 'How long the browser will remember the user opt-out action (in days).', 'wolf-popup' ),
			),

			array(
				'type' => 'text',
				'field_id' => 'content_width',
				'label' => esc_html__( 'Modal Window Width', 'wolf-popup' ),
				'placeholder' => '960px',
			),

			array(
				'type' => 'text',
				'field_id' => 'exclude_post_types',
				'label' => esc_html__( 'Exclude Post Types', 'wolf-popup' ),
				'placeholder' => 'post,page',
				'description' => esc_html__( 'The modal window will NOT popup in these specific post types. Separate each post type by a comma.', 'wolf-popup' ),
			),

			array(
				'type' => 'text',
				'field_id' => 'include_post_types',
				'label' => esc_html__( 'Include Post Types', 'wolf-popup' ),
				'placeholder' => 'post,page',
				'description' => esc_html__( 'The modal window will popup ONLY in these specific post types. Separate each post type by a comma.', 'wolf-popup' ),
			),

			array(
				'type' => 'text',
				'field_id' => 'exclude_ids',
				'label' => esc_html__( 'Exclude Post IDs', 'wolf-popup' ),
				'placeholder' => '654,897,123',
				'description' => esc_html__( 'The modal window will NOT popup in these specific posts. Separate each ID by a comma.', 'wolf-popup' ),
			),

			array(
				'type' => 'text',
				'field_id' => 'include_ids',
				'label' => esc_html__( 'Include Post IDs', 'wolf-popup' ),
				'placeholder' => '654,897,123',
				'description' => esc_html__( 'The modal window will popup ONLY in these specific posts. Separate each ID by a comma.', 'wolf-popup' ),
			),

			array(
				'type' => 'colorpicker',
				'field_id' => 'close_button_color',
				'label' => esc_html__( 'Close Button Color', 'wolf-popup' ),
			),

			array(
				'type' => 'message',
				'field_id' => 'info',
				'label' => esc_html__( 'Info', 'wolf-popup' ),
				'description' =>  sprintf(
					wp_kses_post( __( 'You can add the <strong>"%s"</strong> class to any link to use it as pop-up window "close" button.<br>Also add the <strong>"%s"</strong> class to use it as opt-out button. ("Don\'t show this message again" link)<br>Additionally, if you need a link that open the modal window, you can use the <strong>"%s"</strong> class.', 'wolf-popup' ) ),
					'wolf-popup-close',
					'wolf-popup-opt-out',
					'wolf-popup-open'
				),
			),
		),
	),
);

return new Wolf_Popup_Options( $wolf_popup_options );
