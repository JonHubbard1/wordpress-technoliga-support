<?php

namespace Technoliga_Support;

/**
 * Check GitHub Releases for plugin updates and surface them
 * in the WordPress Plugins page.
 */
class Update_Checker {

	private const GITHUB_OWNER = 'JonHubbard1';
	private const GITHUB_REPO  = 'wordpress-technoliga-support';
	private const GITHUB_API   = 'https://api.github.com/repos/';
	private const CACHE_KEY    = 'technoliga_support_update_check';
	private const CACHE_TTL    = 300; // 5 minutes

	private ?string $plugin_basename = null;
	private string   $current_version;

	public function __construct() {
		$this->current_version = TECHNOLIGA_SUPPORT_VERSION;
	}

	public static function register(): void {
		$checker = new self();

		add_filter( 'site_transient_update_plugins', array( $checker, 'check_for_update' ) );
		add_filter( 'pre_set_site_transient_update_plugins', array( $checker, 'check_for_update' ) );
		add_filter( 'plugins_api', array( $checker, 'plugin_info' ), 10, 3 );
		add_action( 'admin_notices', array( $checker, 'update_notice' ) );
		add_action( 'upgrader_process_complete', array( $checker, 'clear_cache' ), 10, 2 );
	}

	/**
	 * After any plugin install/upgrade, clear our update cache.
	 */
	public function clear_cache( \WP_Upgrader $upgrader, array $hook_extra ): void {
		if ( 'plugin' === ( $hook_extra['type'] ?? '' ) ) {
			delete_transient( self::CACHE_KEY );
		}
	}

	/**
	 * Show an admin notice when an update is available.
	 * This ALWAYS fetches fresh from GitHub so users see updates immediately.
	 */
	public function update_notice(): void {
		$screen = get_current_screen();
		if ( ! $screen || strpos( $screen->id, 'technoliga' ) === false ) {
			return;
		}

		$latest = $this->fetch_latest_release( true );
		if ( ! $latest ) {
			return;
		}

		if ( version_compare( $latest['version'], $this->current_version, '<=' ) ) {
			return;
		}

		$plugin_file = $this->resolve_plugin_file();
		if ( ! $plugin_file ) {
			return;
		}

		$update_url = wp_nonce_url(
			self_admin_url( 'update.php?action=upgrade-plugin&plugin=' . rawurlencode( $plugin_file ) ),
			'upgrade-plugin_' . $plugin_file
		);

		printf(
			'<div class="notice notice-info is-dismissible"><p><strong>%s</strong> %s <a href="%s" class="button button-primary">%s</a> <a href="%s" target="_blank" class="button button-secondary">%s</a></p></div>',
			esc_html__( 'A new version of Technoliga Support is available!', 'technoliga-support' ),
			esc_html( sprintf( __( 'Version %s is now available.', 'technoliga-support' ), $latest['version'] ) ),
			esc_url( $update_url ),
			esc_html__( 'Update Now', 'technoliga-support' ),
			esc_url( $latest['url'] ),
			esc_html__( 'View Release', 'technoliga-support' )
		);
	}

	/**
	 * Compare local version with GitHub release and inject update if available.
	 *
	 * @param object $transient
	 * @return object
	 */
	public function check_for_update( $transient ): object {
		if ( ! is_object( $transient ) ) {
			$transient = new \stdClass();
		}

		if ( ! isset( $transient->response ) ) {
			$transient->response = array();
		}
		if ( ! isset( $transient->checked ) ) {
			$transient->checked = array();
		}

		$plugin_file = $this->resolve_plugin_file();
		if ( ! $plugin_file ) {
			return $transient;
		}

		$transient->checked[ $plugin_file ] = $this->current_version;

		$latest = $this->fetch_latest_release();
		if ( ! $latest ) {
			return $transient;
		}

		if ( version_compare( $latest['version'], $this->current_version, '>' ) ) {
			$transient->response[ $plugin_file ] = (object) array(
				'id'           => sprintf( '%s/%s', self::GITHUB_OWNER, self::GITHUB_REPO ),
				'slug'         => 'technoliga-support',
				'plugin'       => $plugin_file,
				'new_version'  => $latest['version'],
				'url'          => $latest['url'],
				'package'      => $latest['download_url'],
				'tested'       => get_bloginfo( 'version' ),
				'requires'     => '6.0',
				'requires_php' => '7.4',
			);
		} elseif ( isset( $transient->response[ $plugin_file ] ) ) {
			unset( $transient->response[ $plugin_file ] );
		}

		return $transient;
	}

	/**
	 * Populate the "View details" modal on the Plugins page.
	 *
	 * @param false|object|array $result
	 * @param string             $action
	 * @param object             $args
	 * @return false|object|array
	 */
	public function plugin_info( $result, string $action, object $args ) {
		if ( 'plugin_information' !== $action ) {
			return $result;
		}

		if ( 'technoliga-support' !== ( $args->slug ?? '' ) ) {
			return $result;
		}

		$latest = $this->fetch_latest_release( true );
		if ( ! $latest ) {
			return $result;
		}

		return (object) array(
			'name'           => 'Technoliga Support',
			'slug'           => 'technoliga-support',
			'author'         => 'Technoliga',
			'author_profile' => 'https://technoliga.co.uk',
			'version'        => $latest['version'],
			'downloaded'     => 0,
			'tested'         => get_bloginfo( 'version' ),
			'requires'       => '6.0',
			'requires_php'   => '7.4',
			'last_updated'   => $latest['published_at'],
			'homepage'       => $latest['url'],
			'package'        => $latest['download_url'],
			'changelog'      => nl2br( $latest['body'] ),
			'sections'       => array(
				'Description' => 'Manage support tickets for your Technoliga BMS products directly from WordPress admin.',
				'Changelog'   => nl2br( $latest['body'] ),
			),
		);
	}

	/**
	 * Find the actual plugin basename by scanning active plugins.
	 *
	 * @return string|null
	 */
	private function resolve_plugin_file(): ?string {
		if ( null !== $this->plugin_basename ) {
			return $this->plugin_basename;
		}

		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$all_plugins = get_plugins();
		foreach ( $all_plugins as $basename => $data ) {
			if ( ( $data['Name'] ?? '' ) === 'Technoliga Support' ) {
				$this->plugin_basename = $basename;
				return $basename;
			}
		}

		$fallback = plugin_basename( TECHNOLIGA_SUPPORT_PATH . 'technoliga-support.php' );
		$this->plugin_basename = $fallback;
		return $fallback;
	}

	/**
	 * Fetch the latest release from GitHub.
	 *
	 * @param bool $skip_cache When true, always fetch fresh from GitHub.
	 * @return array<string, mixed>|null
	 */
	private function fetch_latest_release( bool $skip_cache = false ): ?array {
		if ( ! $skip_cache ) {
			$cached = get_transient( self::CACHE_KEY );
			if ( false !== $cached ) {
				return $cached;
			}
		}

		$api_url = sprintf(
			'%s%s/%s/releases/latest',
			self::GITHUB_API,
			self::GITHUB_OWNER,
			self::GITHUB_REPO
		);

		$response = wp_remote_get(
			$api_url,
			array(
				'timeout' => 10,
				'headers' => array(
					'Accept' => 'application/vnd.github+json',
				),
			)
		);

		if ( is_wp_error( $response ) ) {
			$this->log( 'Update check failed: ' . $response->get_error_message() );
			return null;
		}

		$status = wp_remote_retrieve_response_code( $response );
		if ( 200 !== $status ) {
			$this->log( 'Update check HTTP error: ' . $status );
			return null;
		}

		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body, true );

		if ( ! is_array( $data ) || empty( $data['tag_name'] ) ) {
			$this->log( 'Update check: invalid JSON or missing tag_name' );
			return null;
		}

		$version = ltrim( $data['tag_name'], 'v' );
		$asset   = null;

		if ( ! empty( $data['assets'] ) && is_array( $data['assets'] ) ) {
			foreach ( $data['assets'] as $a ) {
				$name = $a['name'] ?? '';
				$type = $a['content_type'] ?? '';
				if ( str_ends_with( strtolower( $name ), '.zip' ) || 'application/zip' === $type || 'application/octet-stream' === $type ) {
					$asset = $a['browser_download_url'] ?? null;
					break;
				}
			}
		}

		$download_url = $asset ?? ( $data['zipball_url'] ?? '' );

		if ( ! $download_url ) {
			$this->log( 'Update check: no download URL found' );
			return null;
		}

		$result = array(
			'version'      => $version,
			'url'          => $data['html_url'] ?? '',
			'download_url' => $download_url,
			'published_at' => $data['published_at'] ?? '',
			'body'         => $data['body'] ?? '',
		);

		set_transient( self::CACHE_KEY, $result, self::CACHE_TTL );

		$this->log( 'Update check: found version ' . $version . ' | download: ' . $download_url );

		return $result;
	}

	private function log( string $message ): void {
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			error_log( 'Technoliga Support: ' . $message );
		}
	}
}
