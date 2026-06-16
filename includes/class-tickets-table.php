<?php

namespace Technoliga_Support;

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class Tickets_Table extends \WP_List_Table {

	private API_Client $client;

	public function __construct() {
		parent::__construct(
			array(
				'singular' => __( 'Ticket', 'technoliga-support' ),
				'plural'   => __( 'Tickets', 'technoliga-support' ),
				'ajax'     => false,
			)
		);
		$this->client = new API_Client();
	}

	public function get_columns(): array {
		return array(
			'cb'        => '<input type="checkbox" />',
			'id'        => __( 'ID', 'technoliga-support' ),
			'subject'   => __( 'Subject', 'technoliga-support' ),
			'project'   => __( 'Project', 'technoliga-support' ),
			'status'    => __( 'Status', 'technoliga-support' ),
			'priority'  => __( 'Priority', 'technoliga-support' ),
			'type'      => __( 'Type', 'technoliga-support' ),
			'created'   => __( 'Created', 'technoliga-support' ),
		);
	}

	public function prepare_items(): void {
		$columns  = $this->get_columns();
		$hidden   = array();
		$sortable = array();

		$per_page = $this->get_items_per_page( 'tickets_per_page', 15 );

		$filters = array(
			'page'     => $this->get_pagenum(),
			'per_page' => $per_page,
		);

		if ( ! empty( $_GET['status'] ) ) {
			$filters['status'] = sanitize_text_field( wp_unslash( $_GET['status'] ) );
		}
		if ( ! empty( $_GET['priority'] ) ) {
			$filters['priority'] = sanitize_text_field( wp_unslash( $_GET['priority'] ) );
		}

		try {
			$response = $this->client->list_tickets( $filters );
			$data     = $response['data'] ?? array();

			// Handle paginated collection vs plain array
			if ( isset( $data['data'] ) && is_array( $data['data'] ) ) {
				$tickets = $data['data'];
				$total   = $data['total'] ?? count( $tickets );
			} elseif ( is_array( $data ) ) {
				$tickets = $data;
				$total   = count( $tickets );
			} else {
				$tickets = array();
				$total   = 0;
			}

			$this->items = $tickets;
			$this->set_pagination_args(
				array(
					'total_items' => $total,
					'per_page'    => $per_page,
					'total_pages' => ceil( $total / $per_page ),
				)
			);
		} catch ( \RuntimeException $e ) {
			$this->items = array();
			add_settings_error(
				'technoliga_support',
				'api_error',
				esc_html( $e->getMessage() ),
				'error'
			);
		}

		$this->_column_headers = array( $columns, $hidden, $sortable );
	}

	public function column_cb( $item ): string {
		return sprintf(
			'<input type="checkbox" name="ticket_ids[]" value="%s" />',
			esc_attr( $item['id'] )
		);
	}

	public function column_default( $item, $column_name ): string {
		return esc_html( $item[ $column_name ] ?? '-' );
	}

	public function no_items(): void {
		echo esc_html__( 'No tickets found.', 'technoliga-support' );
	}

	public function column_id( $item ): string {
		return sprintf(
			'<a href="%s">#%s</a>',
			esc_url( admin_url( 'admin.php?page=' . TECHNOLIGA_SUPPORT_SLUG . '&action=view&id=' . $item['id'] ) ),
			esc_html( $item['id'] )
		);
	}

	public function column_subject( $item ): string {
		return sprintf(
			'<strong><a href="%s" class="row-title">%s</a></strong>',
			esc_url( admin_url( 'admin.php?page=' . TECHNOLIGA_SUPPORT_SLUG . '&action=view&id=' . $item['id'] ) ),
			esc_html( $item['subject'] )
		);
	}

	public function column_status( $item ): string {
		return self::status_badge( $item['status'] );
	}

	public function column_priority( $item ): string {
		return self::priority_badge( $item['priority'] );
	}

	public function column_project( $item ): string {
		$project = $item['project'] ?? null;
		if ( is_array( $project ) && ! empty( $project['title'] ) ) {
			return esc_html( $project['title'] );
		}
		return '-';
	}

	public function column_created( $item ): string {
		$date = $item['created_at'] ?? $item['created'] ?? '';
		if ( empty( $date ) ) {
			return '-';
		}
		return esc_html( human_time_diff( strtotime( $date ), time() ) . ' ' . __( 'ago', 'technoliga-support' ) );
	}

	public function get_views(): array {
		$statuses = array(
			'all'              => __( 'All', 'technoliga-support' ),
			'open'             => __( 'Open', 'technoliga-support' ),
			'in_progress'      => __( 'In Progress', 'technoliga-support' ),
			'waiting_customer' => __( 'Waiting Customer', 'technoliga-support' ),
			'resolved'         => __( 'Resolved', 'technoliga-support' ),
			'closed'           => __( 'Closed', 'technoliga-support' ),
		);

		$current = isset( $_GET['status'] ) ? sanitize_text_field( wp_unslash( $_GET['status'] ) ) : 'all';
		$views   = array();

		foreach ( $statuses as $status => $label ) {
			$url = admin_url( 'admin.php?page=' . TECHNOLIGA_SUPPORT_SLUG );
			if ( 'all' !== $status ) {
				$url = add_query_arg( 'status', $status, $url );
			}
			$class = ( $status === $current ) ? 'current' : '';
			$views[ $status ] = sprintf( '<a href="%s" class="%s">%s</a>', esc_url( $url ), esc_attr( $class ), esc_html( $label ) );
		}

		return $views;
	}

	public static function status_badge( string $status ): string {
		$map = array(
			'open'             => array( 'label' => __( 'Open', 'technoliga-support' ), 'class' => 'status-open' ),
			'in_progress'      => array( 'label' => __( 'In Progress', 'technoliga-support' ), 'class' => 'status-progress' ),
			'waiting_customer' => array( 'label' => __( 'Waiting Customer', 'technoliga-support' ), 'class' => 'status-waiting' ),
			'resolved'         => array( 'label' => __( 'Resolved', 'technoliga-support' ), 'class' => 'status-resolved' ),
			'closed'           => array( 'label' => __( 'Closed', 'technoliga-support' ), 'class' => 'status-closed' ),
		);

		$info = $map[ $status ] ?? array( 'label' => $status, 'class' => '' );
		return sprintf( '<span class="ts-badge %s">%s</span>', esc_attr( $info['class'] ), esc_html( $info['label'] ) );
	}

	public static function priority_badge( string $priority ): string {
		$map = array(
			'low'    => array( 'label' => __( 'Low', 'technoliga-support' ), 'class' => 'priority-low' ),
			'medium' => array( 'label' => __( 'Medium', 'technoliga-support' ), 'class' => 'priority-medium' ),
			'high'   => array( 'label' => __( 'High', 'technoliga-support' ), 'class' => 'priority-high' ),
			'urgent' => array( 'label' => __( 'Urgent', 'technoliga-support' ), 'class' => 'priority-urgent' ),
		);

		$info = $map[ $priority ] ?? array( 'label' => $priority, 'class' => '' );
		return sprintf( '<span class="ts-badge %s">%s</span>', esc_attr( $info['class'] ), esc_html( $info['label'] ) ) ;
	}
}
