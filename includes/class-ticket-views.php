<?php

namespace Technoliga_Support;

class Ticket_Views {

	private static ?API_Client $client = null;

	private static function client(): API_Client {
		if ( null === self::$client ) {
			self::$client = new API_Client();
		}
		return self::$client;
	}

	public static function render_list(): void {
		if ( ! self::client()->is_configured() ) {
			self::render_not_configured();
			return;
		}

		$table = new Tickets_Table();
		$table->prepare_items();

		require TECHNOLIGA_SUPPORT_PATH . 'views/ticket-list.php';
	}

	public static function render_detail(): void {
		if ( ! self::client()->is_configured() ) {
			self::render_not_configured();
			return;
		}

		$ticket_id = isset( $_GET['id'] ) ? absint( $_GET['id'] ) : 0;
		if ( ! $ticket_id ) {
			wp_die( esc_html__( 'Invalid ticket ID.', 'technoliga-support' ) );
		}

		$success = '';
		$error   = '';

		// Handle comment submission
		if ( isset( $_POST['technoliga_add_comment'] ) && check_admin_referer( 'technoliga_add_comment_' . $ticket_id ) ) {
			$comment = isset( $_POST['comment_text'] ) ? sanitize_textarea_field( wp_unslash( $_POST['comment_text'] ) ) : '';
			if ( empty( $comment ) ) {
				$error = __( 'Comment cannot be empty.', 'technoliga-support' );
			} else {
				try {
					self::client()->add_comment( $ticket_id, array( 'comment' => $comment ) );
					$success = __( 'Comment added successfully.', 'technoliga-support' );
				} catch ( \RuntimeException $e ) {
					$error = $e->getMessage();
				}
			}
		}

		// Handle status update
		if ( isset( $_POST['technoliga_update_status'] ) && check_admin_referer( 'technoliga_update_status_' . $ticket_id ) ) {
			$new_status = isset( $_POST['status'] ) ? sanitize_text_field( wp_unslash( $_POST['status'] ) ) : '';
			$valid      = array( 'open', 'in_progress', 'waiting_customer', 'resolved', 'closed' );
			if ( in_array( $new_status, $valid, true ) ) {
				try {
					self::client()->update_ticket( $ticket_id, array( 'status' => $new_status, 'suppress_webhook' => true ) );
					$success = __( 'Status updated successfully.', 'technoliga-support' );
				} catch ( \RuntimeException $e ) {
					$error = $e->getMessage();
				}
			} else {
				$error = __( 'Invalid status selected.', 'technoliga-support' );
			}
		}

		try {
			$response = self::client()->get_ticket( $ticket_id );
			$ticket   = $response['data'] ?? array();
		} catch ( \RuntimeException $e ) {
			wp_die( esc_html( $e->getMessage() ) );
		}

		require TECHNOLIGA_SUPPORT_PATH . 'views/ticket-detail.php';
	}

	public static function render_create(): void {
		if ( ! self::client()->is_configured() ) {
			self::render_not_configured();
			return;
		}

		$success = '';
		$error   = '';
		$prefill = array(
			'subject'          => '',
			'intake_category'  => 'support_request',
			'priority'         => 'medium',
			'description'      => '',
			'answers'          => array(),
			'clarification'    => array(),
		);

		if ( isset( $_POST['technoliga_create_ticket'] ) && check_admin_referer( 'technoliga_create_ticket' ) ) {
			$subject       = isset( $_POST['subject'] ) ? sanitize_text_field( wp_unslash( $_POST['subject'] ) ) : '';
			$cat           = isset( $_POST['intake_category'] ) ? sanitize_text_field( wp_unslash( $_POST['intake_category'] ) ) : 'support_request';
			$pri           = isset( $_POST['priority'] ) ? sanitize_text_field( wp_unslash( $_POST['priority'] ) ) : 'medium';
			$desc          = isset( $_POST['description'] ) ? sanitize_textarea_field( wp_unslash( $_POST['description'] ) ) : '';
			$answers       = isset( $_POST['answers'] ) && is_array( $_POST['answers'] ) ? array_map( 'sanitize_textarea_field', wp_unslash( $_POST['answers'] ) ) : array();
			$clarification = isset( $_POST['clarification'] ) && is_array( $_POST['clarification'] ) ? array_map( 'sanitize_textarea_field', wp_unslash( $_POST['clarification'] ) ) : array();

			// Merge clarification answers into main answers so the API receives everything
			$answers = array_merge( $answers, $clarification );

			$prefill = compact( 'subject', 'intake_category', 'priority', 'description', 'answers', 'clarification' );

			if ( empty( $subject ) ) {
				$error = __( 'Subject is required.', 'technoliga-support' );
			} elseif ( empty( $answers['expected_outcome'] ) || empty( $answers['business_impact'] ) ) {
				$error = __( 'Please answer all required questions.', 'technoliga-support' );
			} else {
				try {
					$data = array(
						'subject'         => $subject,
						'intake_category' => $cat,
						'priority'        => $pri,
						'description'     => $desc,
						'answers'         => $answers,
						'source'          => 'wordpress_plugin',
					);

					$result   = self::client()->create_ticket( $data );
					$new_id   = $result['data']['id'] ?? 0;
					$success  = __( 'Ticket created successfully.', 'technoliga-support' );

					if ( $new_id ) {
						wp_safe_redirect(
							admin_url( 'admin.php?page=' . TECHNOLIGA_SUPPORT_SLUG . '&action=view&id=' . $new_id . '&created=1' )
						);
						exit;
					}
				} catch ( \RuntimeException $e ) {
					$error = $e->getMessage();
				}
			}
		}

		require TECHNOLIGA_SUPPORT_PATH . 'views/ticket-create.php';
	}

	private static function render_not_configured(): void {
		echo '<div class="wrap">';
		echo '<h1>' . esc_html__( 'Technoliga Support', 'technoliga-support' ) . '</h1>';
		echo '<div class="notice notice-warning"><p>';
		echo esc_html__( 'Please configure your API key and base URL in the Settings page.', 'technoliga-support' );
		echo ' <a href="' . esc_url( admin_url( 'admin.php?page=' . TECHNOLIGA_SUPPORT_SLUG . '-settings' ) ) . '" class="button button-primary">';
		echo esc_html__( 'Configure Now', 'technoliga-support' );
		echo '</a></p></div>';
		echo '</div>';
	}
}
