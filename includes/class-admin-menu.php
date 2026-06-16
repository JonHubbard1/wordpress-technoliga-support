<?php

namespace Technoliga_Support;

class Admin_Menu {

	public static function register(): void {
		add_action( 'admin_menu', array( __CLASS__, 'add_menu_pages' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );
		add_action( 'admin_notices', array( __CLASS__, 'api_key_notice' ) );
		add_filter( 'plugin_action_links_' . plugin_basename( TECHNOLIGA_SUPPORT_PATH . 'technoliga-support.php' ), array( __CLASS__, 'action_links' ) );
	}

	public static function add_menu_pages(): void {
		$icon = 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgc3Ryb2tlPSJjdXJyZW50Q29sb3IiIHN0cm9rZS13aWR0aD0iMiIgc3Ryb2tlLWxpbmVjYXA9InJvdW5kIiBzdHJva2UtbGluZWpvaW49InJvdW5kIj48cGF0aCBkPSJNMTMgMmgLTNsMiA3TDggMThoMTJsLTItN2gtM2wyLTdaIi8+PHBhdGggZD0iTTEyIDE5djIiLz48cGF0aCBkPSJNOCAxOWg4Ii8+PC9zdmc+';

		add_menu_page(
			__( 'Technoliga Support', 'technoliga-support' ),
			__( 'Technoliga', 'technoliga-support' ),
			'manage_options',
			TECHNOLIGA_SUPPORT_SLUG,
			array( __CLASS__, 'render_main_page' ),
			$icon,
			80
		);

		add_submenu_page(
			TECHNOLIGA_SUPPORT_SLUG,
			__( 'Tickets', 'technoliga-support' ),
			__( 'Tickets', 'technoliga-support' ),
			'manage_options',
			TECHNOLIGA_SUPPORT_SLUG,
			array( __CLASS__, 'render_main_page' )
		);

		add_submenu_page(
			TECHNOLIGA_SUPPORT_SLUG,
			__( 'New Ticket', 'technoliga-support' ),
			__( 'New Ticket', 'technoliga-support' ),
			'manage_options',
			TECHNOLIGA_SUPPORT_SLUG . '-create',
			array( __CLASS__, 'render_create_page' )
		);

		add_submenu_page(
			TECHNOLIGA_SUPPORT_SLUG,
			__( 'Settings', 'technoliga-support' ),
			__( 'Settings', 'technoliga-support' ),
			'manage_options',
			TECHNOLIGA_SUPPORT_SLUG . '-settings',
			array( __CLASS__, 'render_settings_page' )
		);
	}

	public static function render_main_page(): void {
		$action = isset( $_GET['action'] ) ? sanitize_text_field( wp_unslash( $_GET['action'] ) ) : 'list';

		switch ( $action ) {
			case 'view':
				Ticket_Views::render_detail();
				break;
			case 'create':
				Ticket_Views::render_create();
				break;
			default:
				Ticket_Views::render_list();
				break;
		}
	}

	public static function render_create_page(): void {
		Ticket_Views::render_create();
	}

	public static function render_settings_page(): void {
		Settings::render_page();
	}

	public static function enqueue_assets( string $hook ): void {
		if ( strpos( $hook, TECHNOLIGA_SUPPORT_SLUG ) === false ) {
			return;
		}

		wp_enqueue_style(
			'technoliga-support-admin',
			TECHNOLIGA_SUPPORT_URL . 'assets/css/admin.css',
			array(),
			TECHNOLIGA_SUPPORT_VERSION
		);

		wp_enqueue_script(
			'technoliga-support-admin',
			TECHNOLIGA_SUPPORT_URL . 'assets/js/admin.js',
			array( 'jquery' ),
			TECHNOLIGA_SUPPORT_VERSION,
			true
		);

		$settings = Settings::get_settings();
		wp_localize_script(
			'technoliga-support-admin',
			'tsAdmin',
			array(
				'confirmStatus' => __( 'Are you sure you want to change the ticket status?', 'technoliga-support' ),
				'selectOption'  => __( '-- Select --', 'technoliga-support' ),
				'analyzing'     => __( 'Analyzing your answers...', 'technoliga-support' ),
				'addDetails'    => __( 'Add anything else?', 'technoliga-support' ),
				'hideDetails'   => __( 'Hide', 'technoliga-support' ),
				'apiKey'        => $settings['api_key'] ?? '',
				'apiUrl'        => rtrim( $settings['base_url'] ?? 'https://technoliga.co.uk', '/' ),
			)
		);
	}

	public static function api_key_notice(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( ! $screen || strpos( $screen->id, TECHNOLIGA_SUPPORT_SLUG ) === false ) {
			return;
		}

		$settings = Settings::get_settings();
		if ( empty( $settings['api_key'] ) ) {
			echo '<div class="notice notice-warning"><p>';
			echo esc_html__( 'Technoliga Support: Please configure your API key in the Settings page.', 'technoliga-support' );
			echo ' <a href="' . esc_url( admin_url( 'admin.php?page=' . TECHNOLIGA_SUPPORT_SLUG . '-settings' ) ) . '">';
			echo esc_html__( 'Go to Settings', 'technoliga-support' );
			echo '</a></p></div>';
		}
	}

	public static function action_links( array $links ): array {
		$settings_link = '<a href="' . esc_url( admin_url( 'admin.php?page=' . TECHNOLIGA_SUPPORT_SLUG . '-settings' ) ) . '">' . __( 'Settings', 'technoliga-support' ) . '</a>';
		array_unshift( $links, $settings_link );
		return $links;
	}
}
