<?php

namespace Technoliga_Support;

/**
 * Check GitHub Releases for plugin updates and surface them
 * in the WordPress Plugins page.
 */
class Update_Checker {

	private const GITHUB_OWNER = 'JonHubbard1';
	private const GITHUB_REPO  = 'wordpress-technoliga-support';
	private const GITHUB_API = 'https://api.github.com/repos/';
	private const CACHE_KEY  = 'technoliga_support_update_check';
	private const CACHE_TTL  = 3600; // 1 hour

	private string $plugin_file;
	private string $current_version;
	private string $plugin_basename;

	public function __construct() {
		$this->plugin_file     = TECHNOLIGA_SUPPORT_PATH . 'technoliga-support.php';
		$this->plugin_basename = plugin_basename( $this->plugin_file );
		$this->current_version = TECHNOLIGA_SUPPORT_VERSION;
	}

	public static function register(): void {
		$checker = new self();
		// Fires when WordPress writes the update transient (Plugins / Updates page)
		add_filter( 'pre_set_site_transient_update_plugins', array( $checker, 'check_for_update' ) );
		// Fires when WordPress reads the existing transient (any admin page)
		add_filter( 'site_transient_update_plugins', array( $checker, 'check_for_update' ) );
		add_filter( 'plugins_api', array( $checker, 'plugin_info' ), 10, 3 );
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

		if ( empty( $transient->checked ) ) {
			return $transient;
		}

		$latest = $this->fetch_latest_release();

		if ( ! $latest ) {
			return $transient;
		}

		if ( version_compare( $latest['version'], $this->current_version, '>' ) ) {
			$transient->response[ $this->plugin_basename ] = (object) array(
				'id'            => sprintf( '%s/%s', self::GITHUB_OWNER, self::GITHUB_REPO ),
				'slug'          => 'technoliga-support',
				'plugin'        => $this->plugin_basename,
				'new_version'   => $latest['version'],
				'url'           => $latest['url'],
				'package'       => $latest['download_url'],
				'tested'        => get_bloginfo( 'version' ),
				'requires'      => '6.0',
				'requires_php'  => '7.4',
			);
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

		$latest = $this->fetch_latest_release();

		if ( ! $latest ) {
			return $result;
		}

		return (object) array(
			'name'          => 'Technoliga Support',
			'slug'          => 'technoliga-support',
			'author'        => 'Technoliga',
			'author_profile'=> 'https://technoliga.co.uk',
			'version'       => $latest['version'],
			'downloaded'    => 0,
			'tested'        => get_bloginfo( 'version' ),
			'requires'      => '6.0',
			'requires_php'  => '7.4',
			'last_updated'  => $latest['published_at'],
			'homepage'      => $latest['url'],
			'package'       => $latest['download_url'],
			'changelog'     => nl2br( $latest['body'] ),
			'sections'      => array(
				'Description' => 'Manage support tickets for your Technoliga BMS products directly from WordPress admin.',
				'Changelog'   => nl2br( $latest['body'] ),
			),
		);
	}

	/**
	 * Fetch the latest release from GitHub (cached).
	 *
	 * @return array<string, mixed>|null
	 */
	private function fetch_latest_release(): ?array {
		$cached = get_transient( self::CACHE_KEY );
		if ( false !== $cached ) {
			return $cached;
		}

		$api_url = sprintf(
			'%s%s/%s/releases/latest',
			self::GITHUB_API,
			self::GITHUB_OWNER,
			self::GITHUB_REPO
		);

		$response = wp_remote_get( $api_url, array( 'timeout' => 10 ) );

		if ( is_wp_error( $response ) ) {
			return null;
		}

		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body, true );

		if ( ! is_array( $data ) || empty( $data['tag_name'] ) ) {
			return null;
		}

		$version = ltrim( $data['tag_name'], 'v' );
		$asset   = null;

		// Find the first .zip asset (GitHub may report content_type as
		// application/octet-stream rather than application/zip)
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

		$result = array(
			'version'      => $version,
			'url'          => $data['html_url'] ?? '',
			'download_url' => $download_url,
			'published_at' => $data['published_at'] ?? '',
			'body'         => $data['body'] ?? '',
		);

		set_transient( self::CACHE_KEY, $result, self::CACHE_TTL );

		return $result;
	}
}
