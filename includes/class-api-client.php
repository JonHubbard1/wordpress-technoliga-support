<?php

namespace Technoliga_Support;

class API_Client {

	private string $api_key;
	private string $base_url;
	private int    $timeout;

	public function __construct() {
		$settings         = Settings::get_settings();
		$this->api_key    = $settings['api_key'] ?? '';
		$this->base_url   = rtrim( $settings['base_url'] ?? 'https://technoliga.co.uk', '/' );
		$this->timeout    = 30;
	}

	public function is_configured(): bool {
		return ! empty( $this->api_key ) && ! empty( $this->base_url );
	}

	/**
	 * List tickets with optional filters.
	 *
	 * @param array<string, mixed> $filters
	 * @return array<string, mixed>
	 * @throws \RuntimeException
	 */
	public function list_tickets( array $filters = array() ): array {
		$cache_key = 'technoliga_tickets_' . md5( serialize( $filters ) );
		$cached    = get_transient( $cache_key );

		if ( false !== $cached ) {
			return $cached;
		}

		$query = http_build_query( $filters );
		$data  = $this->request( 'GET', '/api/v1/tickets?' . $query );

		set_transient( $cache_key, $data, 300 );

		return $data;
	}

	/**
	 * Get a single ticket with comments.
	 *
	 * @throws \RuntimeException
	 */
	public function get_ticket( int $ticket_id ): array {
		$cache_key = 'technoliga_ticket_' . $ticket_id;
		$cached    = get_transient( $cache_key );

		if ( false !== $cached ) {
			return $cached;
		}

		$data = $this->request( 'GET', '/api/v1/tickets/' . $ticket_id );

		set_transient( $cache_key, $data, 120 );

		return $data;
	}

	/**
	 * Update a ticket.
	 *
	 * @param array<string, mixed> $data
	 * @throws \RuntimeException
	 */
	public function update_ticket( int $ticket_id, array $data ): array {
		$result = $this->request( 'PATCH', '/api/v1/tickets/' . $ticket_id, $data );
		$this->invalidate_ticket_cache( $ticket_id );
		return $result;
	}

	/**
	 * Add a comment to a ticket.
	 *
	 * @param array<string, mixed> $data
	 * @throws \RuntimeException
	 */
	public function add_comment( int $ticket_id, array $data ): array {
		$result = $this->request( 'POST', '/api/v1/tickets/' . $ticket_id . '/comments', $data );
		$this->invalidate_ticket_cache( $ticket_id );
		return $result;
	}

	/**
	 * Analyze an intake submission without creating a ticket.
	 *
	 * @param array<string, mixed> $data
	 * @throws \RuntimeException
	 */
	public function analyze( array $data ): array {
		return $this->request( 'POST', '/api/v1/intake/analyze', $data );
	}

	/**
	 * Create a new ticket via the intake endpoint.
	 *
	 * @param array<string, mixed> $data
	 * @throws \RuntimeException
	 */
	public function create_ticket( array $data ): array {
		$result = $this->request( 'POST', '/api/v1/intake/tickets', $data );
		$this->invalidate_list_cache();
		return $result;
	}

	/**
	 * Make an HTTP request to the BMS API.
	 *
	 * @param array<string, mixed>|null $body
	 * @return array<string, mixed>
	 * @throws \RuntimeException
	 */
	private function request( string $method, string $path, ?array $body = null ): array {
		$url = $this->base_url . $path;

		$args = array(
			'method'  => $method,
			'timeout' => $this->timeout,
			'headers' => array(
				'Authorization' => 'Bearer ' . $this->api_key,
				'Content-Type'    => 'application/json',
				'Accept'          => 'application/json',
			),
		);

		if ( null !== $body ) {
			$args['body'] = wp_json_encode( $body );
		}

		$response = wp_remote_request( $url, $args );

		if ( is_wp_error( $response ) ) {
			throw new \RuntimeException( $response->get_error_message() );
		}

		$status  = wp_remote_retrieve_response_code( $response );
		$body_raw = wp_remote_retrieve_body( $response );
		$data    = json_decode( $body_raw, true );

		if ( $status >= 400 ) {
			$message = is_array( $data ) && isset( $data['message'] ) ? $data['message'] : 'HTTP ' . $status;
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
				error_log( 'Technoliga API error: ' . $method . ' ' . $path . ' -> ' . $status . ' ' . $message . ' | Body: ' . substr( $body_raw, 0, 2000 ) );
			}
			throw new \RuntimeException( $message, $status );
		}

		if ( ! is_array( $data ) ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
				error_log( 'Technoliga API invalid JSON: ' . $method . ' ' . $path . ' | Body: ' . substr( $body_raw, 0, 2000 ) );
			}
			throw new \RuntimeException( 'Invalid JSON response from Technoliga API.' );
		}

		return $data;
	}

	private function invalidate_list_cache(): void {
		global $wpdb;
		$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '\_transient\_technoliga\_tickets\_%'" );
		$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '\_transient\_timeout\_technoliga\_tickets\_%'" );
	}

	private function invalidate_ticket_cache( int $ticket_id ): void {
		delete_transient( 'technoliga_ticket_' . $ticket_id );
		$this->invalidate_list_cache();
	}
}
