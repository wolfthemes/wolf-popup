<?php
/**
 * Popup Manager Settings.
 *
 * @class Wolf_Popup_Admin
 * @author WolfThemes
 * @category Admin
 * @package WolfPopup/Admin
 * @version 1.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Wolf_Popup_Options class.
 */
class Wolf_Popup_Options {

	/**
	 * @var settings id
	 */
	private $settings_id = 'wolf-popup-settings';

	/**
	 * @var settings slug
	 */
	private $settings_slug = 'settings';

	/**
	 * @var array
	 */
	public $settings = array();

	/**
	 * Constructor
	 */
	public function __construct( $settings = array() ) {

		$this->settings = $settings + $this->settings;

		// Add menu
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );

		// Add settings form
		add_action( 'admin_init', array( $this, 'settings' ) );

		// set default options
		add_action( 'admin_init', array( $this, 'default_options' ) );

		// Add settings scripts
		add_action( 'admin_enqueue_scripts', array( $this, 'scripts' ) );
	}

	/**
  	 * Enqueue scripts
	 */
	public function scripts() {
		wp_enqueue_script( 'wp-color-picker' ); // colorpicker
	}

	/**
	 * Add the Theme menu to the WP admin menu
	 */
	public function admin_menu() {

		add_menu_page( 
			esc_html__( 'Pop-Up', 'wolf-popup' ),
			esc_html__( 'Pop-Up', 'wolf-popup' ),
			'manage_options',
			'wolf-popup',
			'wolf-popup',
			'dashicons-admin-comments',
			6
		); 

		foreach ( $this->settings as $section ) {
			$this->settings_id = $section['settings_id'];
			$parent_slug = ( isset( $section['parent_slug'] ) ) ? $section['parent_slug'] : 'wolf-popup';
			//$parent_slug = 'options-general.php';
			add_submenu_page( $parent_slug, $section['title'], $section['title'], 'activate_plugins', $section['settings_id'], array( $this, 'settings_form' ) );
		}
		remove_submenu_page( 'wolf-popup','wolf-popup' );
	}

	/**
	 * Init Settings
	 */
	public function settings() {

		foreach ( $this->settings as $setting ) {
			
			$this->settings_id = $setting['settings_id'];
			$this->settings_slug = $setting['settings_slug'];

			register_setting( $this->settings_id, $this->settings_slug, array( $this, 'settings_validate' ) );
			add_settings_section( $this->settings_id, '', array( $this, 'section_intro' ), $this->settings_id );

			foreach ( $setting['fields'] as $key => $field ) {
				$type = ( isset( $field['type'] ) ) ? $field['type'] : 'text';
				$label = ( isset( $field['label'] ) ) ? $field['label'] : '';
				$description = ( isset( $field['description'] ) ) ? $field['description'] : '';
				$placeholder = ( isset( $field['placeholder'] ) ) ? $field['placeholder'] : '';
				$value = ( isset( $field['value'] ) ) ? $field['value'] : '';
				$choices = ( isset( $field['choices'] ) && 'select' == $type  ) ? $field['choices'] : array();
				add_settings_field(
					$field['field_id'],
					$label,
					array( $this, 'setting_field' ),
					$this->settings_id,
					$this->settings_id,
					array(
						'field_id' => $field['field_id'],
						'type' => $type,
						'settings_slug' => $this->settings_slug,
						'description' => $description,
						'placeholder' => $placeholder,
						'value' => $value,
						'choices' => $choices,
					)
				);
			}

			add_settings_field( 'settings_index', '', array( $this, 'section_slug' ), $this->settings_id, $this->settings_id, array( 'settings_slug' => $this->settings_slug ) );
		}
	}

	/**
	 * Intro section
	 */
	public function section_slug( $args ) {
		$settings_slug = $args['settings_slug'];
		?>
		<input type="hidden" name="<?php echo esc_attr( $settings_slug . '[settings_slug]' ); ?>" value="<?php echo esc_attr( $settings_slug ); ?>">
		<?php
	}

	/**
	 * Validate settings
	 */
	public function settings_validate( $input ) {

		if ( isset( $_POST['wolf_popup_settings_nonce'] ) && wp_verify_nonce( $_POST['wolf_popup_settings_nonce'], 'wolf_popup_save_settings_nonce' ) ) {

			// process form data
			do_action( 'wolf_popup_before_options_save', $input );

			$setting_index = esc_attr( $input['settings_slug'] );
			wolf_popup_update_option_index( $setting_index, $input );

			do_action( 'wolf_popup_after_options_save', $input );
		}

		return $input;
	}

	/**
	 * Intro section
	 */
	public function section_intro() {
		//var_dump( get_option( 'wolf_popup_settings' ) );
		//var_dump( wolf_popup_get_option( 'mailchimp', 'mailchimp_api_key' ) );
		// add instructions
	}

	/**
	 * Create field using passed arguments
	 *
	 * @param array $args
	 * @return string
	 */
	public function setting_field( $args ) {
		$type = $args['type'];
		$field_id = $args['field_id'];
		$settings_slug = $args['settings_slug'];
		$placeholder = $args['placeholder'];
		$value = ( wolf_popup_get_option( $settings_slug, $field_id ) ) ? wolf_popup_get_option( $settings_slug, $field_id ) : $args['value'];
		$choices = $args['choices'];
		$description = $args['description'];

		if ( 'text' == $type || 'url' == $type ) {
			?>
			<input placeholder="<?php echo esc_attr( $placeholder ); ?>" value="<?php echo esc_attr( wolf_popup_get_option( $settings_slug, $field_id ) ); ?>" name="<?php echo esc_attr( $settings_slug . '[' . $field_id . ']' ); ?>" class="regular-text">
			<?php
		} elseif ( 'textarea' == $type ) {
			?>
			<textarea class="large-text" name="<?php echo esc_attr( $settings_slug . '[' . $field_id . ']' ); ?>" rows="5"><?php echo sanitize_text_field( wolf_popup_get_option( $settings_slug, $field_id ) ); ?></textarea>
			<?php
		} elseif ( 'editor' === $type ) {
			$content = ( wolf_popup_get_option( $settings_slug, $field_id ) ) ? stripslashes( wolf_popup_get_option( $settings_slug, $field_id ) ) : '';
			$editor_id = esc_attr( $settings_slug . '[' . $field_id . ']' );
			wp_editor( $content, $field_id, $settings = array() );
		} elseif ( 'checkbox' == $type ) {
			?>
			<input type="hidden" name="<?php echo esc_attr( $settings_slug . '[' . $field_id . ']' ); ?>" value="0">
			<label>
				<input type="checkbox" name="<?php echo esc_attr( $settings_slug . '[' . $field_id . ']' ); ?>" value="1" <?php checked( wolf_popup_get_option( $settings_slug, $field_id ), 1 ); ?>>
			</label>
			<?php
		} elseif ( 'page' == $type ) {
			$page_options = array( '' => esc_html__( '- Disabled -', 'wolf-popup' ) );
			$pages = get_pages();

			foreach ( $pages as $page ) {

				if ( get_post_field( 'post_parent', $page->ID ) ) {
					$page_options[ absint( $page->ID ) ] = '&nbsp;&nbsp;&nbsp; ' . sanitize_text_field( $page->post_title );
				} else {
					$page_options[ absint( $page->ID ) ] = sanitize_text_field( $page->post_title );
				}
			}
			?>
			<select name="<?php echo esc_attr( $settings_slug . '[' . $field_id . ']' ); ?>">
				<?php foreach ( $page_options as $id => $title ) : ?>
					<option value="<?php echo absint( $id ); ?>" <?php selected( $value, $id ); ?>><?php echo sanitize_text_field( $title ); ?></option>
				<?php endforeach; ?>
			</select>
			<?php
		

		} elseif ( 'select' == $type ) {
			?>
			<select name="<?php echo esc_attr( $settings_slug . '[' . $field_id . ']' ); ?>">
				<?php if ( array_keys( $choices ) != array_keys( array_keys( $choices ) ) ) : ?>
					<?php foreach ( $choices as $key => $name) : ?>
						<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $value, $key ); ?>><?php echo sanitize_text_field( $name ); ?></option>
					<?php endforeach; ?>
				<?php else : ?>
					<?php foreach ( $choices as $choice ) : ?>
						<option value="<?php echo esc_attr( $choice ); ?>" <?php selected( $value, $choice ); ?>><?php echo sanitize_text_field( $choice ); ?></option>
					<?php endforeach; ?>
				<?php endif; ?>
			</select>
			<?php

		} elseif ( 'colorpicker' == $type ) {
			$colorpicker_id = uniqid( 'wvc-settings-colorpicker-' );
			?>
			<script>
				jQuery( document ).ready( function() {
					jQuery( '#<?php echo esc_js( $colorpicker_id ); ?>' ).wpColorPicker();
				} );
			</script>
			<input id="<?php echo esc_attr( $colorpicker_id ); ?>" value="<?php echo wolf_popup_sanitize_color( wolf_popup_get_option( $settings_slug, $field_id ) ); ?>" name="<?php echo esc_attr( $settings_slug . '[' . $field_id . ']' ); ?>" class="wvc-settings-colorpicker">
			<?php
		} elseif ( 'image' == $type ) {
			/**
			 * Bg image
			 */
			wp_enqueue_media();
			$image_id = absint( $value );
			$image_url = wolf_popup_get_url_from_attachment_id( $image_id );
			?>
			<input type="hidden" name="<?php echo esc_attr( $settings_slug . '[' . $field_id . ']' ); ?>" value="<?php echo esc_attr( $image_id); ?>">
			<img style="max-width:150px;<?php if ( ! $image_id ) echo 'display:none;'; ?>" class="wvc-img-preview" src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $field_id ); ?>">
			<br>
			<a href="#" class="button wvc-reset-img"><?php esc_html_e( 'Clear', 'wolf-popup' ); ?></a>
			<a href="#" class="button wvc-set-img"><?php esc_html_e( 'Choose an image', 'wolf-popup' ); ?></a>
			<?php
		} elseif ( 'background' == $type ) {
			$bg_meta = wolf_popup_get_bg_meta( $settings_slug, $field_id  );
			extract( $bg_meta );
			$image_url = wolf_popup_get_url_from_attachment_id( $image_id );
			/**
			 * Bg color
			 */
			?>
			<p>
				<label for="<?php echo esc_attr( $settings_slug . '[' . $field_id . '][color]' ); ?>">
					<?php esc_html_e( 'Color', 'wolf-popup' ); ?>
				</label><br>
				<input value="<?php echo wolf_popup_sanitize_color( $color ); ?>" name="<?php echo esc_attr( $settings_slug . '[' . $field_id . '][color]' ); ?>" class="wvc-settings-colorpicker">
			</p>
			<?php
			/**
			 * Bg image
			 */
			wp_enqueue_media();
			?>
			<p>
				<label for="<?php echo esc_attr( $settings_slug . '[' . $field_id . '][image_id]' ); ?>">
					<?php esc_html_e( 'Image', 'wolf-popup' ); ?>
				</label><br>
				<input type="hidden" name="<?php echo esc_attr( $settings_slug . '[' . $field_id . '][image_id]' ); ?>" value="<?php echo esc_attr( $image_id); ?>">
				<img style="max-width:150px;<?php if ( ! $image_id ) echo 'display:none;'; ?>" class="wvc-img-preview" src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $field_id ); ?>">
				<br>
				<a href="#" class="button wvc-reset-img"><?php esc_html_e( 'Clear', 'wolf-popup' ); ?></a>
				<a href="#" class="button wvc-set-img"><?php esc_html_e( 'Choose an image', 'wolf-popup' ); ?></a>
			</p>
			<?php

			/**
			 * Bg repeat
			 */
			$options = array( 'no-repeat', 'repeat', 'repeat-x', 'repeat-y' );
			?>
			<p>
				<label for="<?php echo esc_attr( $settings_slug . '[' . $field_id . '][repeat]' ); ?>">
					<?php esc_html_e( 'Repeat', 'wolf-popup' ); ?>
				</label><br>
				<select name="<?php echo esc_attr( $settings_slug . '[' . $field_id . '][repeat]' ); ?>">
					<?php foreach ( $options as $option ) : ?>
						<option <?php selected( $repeat, $option ); ?>><?php echo sanitize_text_field( $option ); ?></option>
					<?php endforeach; ?>
				</select>
			</p>
			<?php
			/**
			 * Bg position
			 */
			$options = array(
				'center center',
				'center top',
				'left top' ,
				'right top' ,
				'center bottom',
				'left bottom' ,
				'right bottom' ,
				'left center' ,
				'right center',
			);
			 ?>
			 <p>
				 <label for="<?php echo esc_attr( $settings_slug . '[' . $field_id . '][position]' ); ?>">
					<?php esc_html_e( 'Position', 'wolf-popup' ); ?>
				</label><br>
				 <select name="<?php echo esc_attr( $settings_slug . '[' . $field_id . '][position]' ); ?>">
				 	<?php foreach ( $options as $option ) : ?>
						<option <?php selected( $position, $option ); ?>><?php echo sanitize_text_field( $option ); ?></option>
					<?php endforeach; ?>
				 </select>
			</p>
			 <?php

			/**
			 * Bg size
			 */
			$options = array(
				'cover' => esc_html__( 'cover (resize)', 'wolf-popup' ),
				'normal' => esc_html__( 'normal', 'wolf-popup' ),
				'resize' => esc_html__( 'responsive (hard resize)', 'wolf-popup' ),
			);
			?>
			<p>
				<label for="<?php echo esc_attr( $settings_slug . '[' . $field_id . '][size]' ); ?>">
					<?php esc_html_e( 'Size', 'wolf-popup' ); ?>
				</label><br>
				<select name="<?php echo esc_attr( $settings_slug . '[' . $field_id . '][size]' ); ?>">
					<?php foreach ( $options as $option => $display ) : ?>
						<option value="<?php echo esc_attr( $option ); ?>" <?php selected( $size, $option ); ?>><?php echo sanitize_text_field( $display ); ?></option>
					<?php endforeach; ?>
				</select>
			</p>
			<?php

			/**
			 * Bg attachment
			 */
			$options = array(
				'scroll',
				'fixed',
			);
			?>
			<p>
				<label for="<?php echo esc_attr( $settings_slug . '[' . $field_id . '][attachment]' ); ?>">
					<?php esc_html_e( 'Attachment', 'wolf-popup' ); ?>
				</label><br>
				<select name="<?php echo esc_attr( $settings_slug . '[' . $field_id . '][attachment]' ); ?>">
					<?php foreach ( $options as $option ) : ?>
						<option <?php selected( $attachment, $option ); ?>><?php echo sanitize_text_field( $option ); ?></option>
					<?php endforeach; ?>
				</select>
			</p>
			<?php
		} elseif ( 'message' === $type ) {
			
		}

		if ( $description ) {
			echo '<p class="description">' . wp_kses_post( $description ) . '</p>';
		}
	}

	/**
	 * Plugin Settings
	 */
	public function settings_form() {
		$this->settings_id = ( isset( $_GET['page'] ) ) ? esc_attr( $_GET['page'] ) : '';
		?>
		<div class="wrap">
			<h2><?php esc_html_e( 'Pop-Up Settings', 'wolf-popup' ) ?></h2>
			<?php settings_errors(); ?>
			<form action="options.php" method="post">
				<?php wp_nonce_field( 'wolf_popup_save_settings_nonce', 'wolf_popup_settings_nonce' ); ?>
				<?php settings_fields( $this->settings_id ); ?>
				<?php do_settings_sections( $this->settings_id ); ?>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}

	/**
	 * Set default options
	 */
	public function default_options() {

		global $options;

		//delete_option( 'wolf_popup_settings' );

		if ( ! get_option( 'wolf_popup_settings' )  ) {

			$default = apply_filters( 'wolf_popup_default_settings',
				array(

					'time_delayed' => array(
						
					),
					'exit-intent' => array(
						
					),
				)
			);

			add_option( 'wolf_popup_settings', $default );
		}

		//var_dump( get_option( 'wolf_popup_settings' ) );
	}
} // end class