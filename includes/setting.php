<?php
/**
 * Setting screen.
 *
 * @return
 */

/**
 * Register settings.
 */
add_action( 'admin_init', function() {
	// Register section.
	add_settings_section( 'taroai_settings_section', __( 'OpenAI API Settings', 'taroai' ), function() {
		?>
		<p><?php _e( 'Please enter your API key and engine ID.', 'taroai' ); ?></p>
		<?php
	}, 'taroai_settings' );
	// Register API Key fields.
	add_settings_field( 'taroai_api_key', __( 'API Key', 'taroai' ), function() {
		?>
		<input type="text" name="taroai_api_key" value="<?php echo esc_attr( get_option( 'taroai_api_key', '' ) ); ?>" class="regular-text" />
		<?php
	}, 'taroai_settings', 'taroai_settings_section' );
	register_setting('taroai_settings', 'taroai_api_key');
	// Register Engine ID fields.
	add_settings_field( 'taroai_engine_id', __( 'Engine ID', 'taroai' ), function() {
		?>
		<input type="text" name="taroai_engine_id" value="<?php echo esc_attr( get_option( 'taroai_engine_id', '' ) ); ?>" class="regular-text"
			placeholder="<?php esc_attr_e( 'default', 'taroai' ); ?>"/>
		<?php
	}, 'taroai_settings', 'taroai_settings_section' );
	register_setting('taroai_settings', 'taroai_engine_id');
} );

/**
 * Add setting menu.
 */
add_action('admin_menu', function() {
	add_options_page(
		__('OpenAI API Settings', 'taroai'),
		__('OpenAI API Settings', 'taroai'),
		'manage_options',
		'taroai_settings',
		function () {
			?>
			<div class="wrap">
				<h1><?php _e('OpenAI API Settings', 'taroai'); ?></h1>
				<form method="post" action="<?php echo esc_url( admin_url( 'options.php' ) ); ?>">
					<?php
					settings_fields('taroai_settings');
					do_settings_sections('taroai_settings');
					submit_button();
					?>
				</form>
			</div>
			<?php
		}
	);
});
