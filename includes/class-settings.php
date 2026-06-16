<?php

namespace Technoliga_Support;

class Settings {

	private const OPTION_KEY = 'technoliga_support_settings';

	/**
	 * Retrieve plugin settings with defaults.
	 *
	 * @return array<string, string>
	 */
	public static function get_settings(): array {
		$defaults = array(
			'api_key'   => '',
			'base_url'  => 'https://technoliga.co.uk',
		);
		$stored = get_option( self::OPTION_KEY, array() );
		return wp_parse_args( $stored, $defaults );
	}

	public static function render_page(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to access this page.', 'technoliga-support' ) );
		}

		$saved = false;
		$error = '';

		if ( isset( $_POST['technoliga_support_save'] ) && check_admin_referer( 'technoliga_support_settings' ) ) {
			$settings = array(
				'api_key'  => isset( $_POST['api_key'] ) ? sanitize_text_field( wp_unslash( $_POST['api_key'] ) ) : '',
				'base_url' => isset( $_POST['base_url'] ) ? esc_url_raw( wp_unslash( $_POST['base_url'] ) ) : '',
			);

			$settings['base_url'] = rtrim( $settings['base_url'], '/' );

			if ( empty( $settings['api_key'] ) || empty( $settings['base_url'] ) ) {
				$error = __( 'Both API Key and Base URL are required.', 'technoliga-support' );
			} elseif ( 0 !== strpos( $settings['api_key'], 'tk_' ) ) {
				$error = __( 'API Key must start with "tk_".', 'technoliga-support' );
			} else {
				update_option( self::OPTION_KEY, $settings );
				$saved = true;
			}
		}

		$settings = self::get_settings();
		require TECHNOLIGA_SUPPORT_PATH . 'views/settings.php';
	}
}
