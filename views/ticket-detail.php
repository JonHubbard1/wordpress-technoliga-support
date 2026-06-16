<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$ticket_id = absint( $ticket['id'] ?? 0 );
$comments  = $ticket['comments']['data'] ?? $ticket['comments'] ?? array();
?>
<div class="wrap technoliga-wrap">
	<h1>
		<?php echo esc_html__( 'Ticket', 'technoliga-support' ); ?> #<?php echo esc_html( $ticket_id ); ?>
		<span class="ts-badge <?php echo esc_attr( 'status-' . ( $ticket['status'] ?? '' ) ); ?>"><?php echo esc_html( ucwords( str_replace( '_', ' ', $ticket['status'] ?? '' ) ) ); ?></span>
	</h1>

	<?php if ( ! empty( $success ) ) { ?>
		<div class="notice notice-success"><p><?php echo esc_html( $success ); ?></p></div>
	<?php } ?>

	<?php if ( ! empty( $error ) ) { ?>
		<div class="notice notice-error"><p><?php echo esc_html( $error ); ?></p></div>
	<?php } ?>

	<div class="ts-card">
		<h2><?php echo esc_html( $ticket['subject'] ?? '' ); ?></h2>
		<p class="ts-meta">
			<strong><?php echo esc_html__( 'Type:', 'technoliga-support' ); ?></strong> <?php echo esc_html( ucwords( str_replace( '_', ' ', $ticket['type'] ?? '' ) ) ); ?> <br>
			<strong><?php echo esc_html__( 'Priority:', 'technoliga-support' ); ?></strong> <?php echo Tickets_Table::priority_badge( $ticket['priority'] ?? '' ); ?> <br>
			<strong><?php echo esc_html__( 'Assigned To:', 'technoliga-support' ); ?></strong> <?php echo esc_html( $ticket['assigned_user']['name'] ?? __( 'Unassigned', 'technoliga-support' ) ); ?> <br>
		<strong><?php echo esc_html__( 'Project:', 'technoliga-support' ); ?></strong> <?php echo esc_html( $ticket['project']['title'] ?? __( 'No project', 'technoliga-support' ) ); ?> <br>
			<strong><?php echo esc_html__( 'Created:', 'technoliga-support' ); ?></strong> <?php echo esc_html( $ticket['created_at'] ?? '' ); ?> <br>
			<strong><?php echo esc_html__( 'Updated:', 'technoliga-support' ); ?></strong> <?php echo esc_html( $ticket['updated_at'] ?? '' ); ?>
		</p>
		<hr>
		<div class="ts-description"><?php echo wp_kses_post( nl2br( esc_html( $ticket['description'] ?? '' ) ) ); ?></div>
	</div>

	<div class="ts-card">
		<h3><?php echo esc_html__( 'Change Status', 'technoliga-support' ); ?></h3>
		<form method="post" action="">
			<?php wp_nonce_field( 'technoliga_update_status_' . $ticket_id ); ?>
			<select name="status">
				<option value="open" <?php selected( $ticket['status'] ?? '', 'open' ); ?>><?php echo esc_html__( 'Open', 'technoliga-support' ); ?></option>
				<option value="in_progress" <?php selected( $ticket['status'] ?? '', 'in_progress' ); ?>><?php echo esc_html__( 'In Progress', 'technoliga-support' ); ?></option>
				<option value="waiting_customer" <?php selected( $ticket['status'] ?? '', 'waiting_customer' ); ?>><?php echo esc_html__( 'Waiting Customer', 'technoliga-support' ); ?></option>
				<option value="resolved" <?php selected( $ticket['status'] ?? '', 'resolved' ); ?>><?php echo esc_html__( 'Resolved', 'technoliga-support' ); ?></option>
				<option value="closed" <?php selected( $ticket['status'] ?? '', 'closed' ); ?>><?php echo esc_html__( 'Closed', 'technoliga-support' ); ?></option>
			</select>
			<?php submit_button( __( 'Update Status', 'technoliga-support' ), 'secondary', 'technoliga_update_status' ); ?>
		</form>
	</div>

	<div class="ts-card">
		<h3><?php echo esc_html__( 'Comments', 'technoliga-support' ); ?></h3>
		<?php if ( ! empty( $comments ) ) { ?>
			<div class="ts-comments">
				<?php foreach ( $comments as $comment ) { ?>
					<div class="ts-comment">
						<div class="ts-comment-header">
							<strong><?php echo esc_html( $comment['user']['name'] ?? __( 'System', 'technoliga-support' ) ); ?></strong>
							<span class="ts-comment-date"><?php echo esc_html( $comment['created_at'] ?? '' ); ?></span>
						</div>
						<div class="ts-comment-body"><?php echo wp_kses_post( nl2br( esc_html( $comment['comment'] ?? '' ) ) ); ?></div>
					</div>
				<?php } ?>
			</div>
		<?php } else { ?>
			<p><?php echo esc_html__( 'No comments yet.', 'technoliga-support' ); ?></p>
		<?php } ?>

		<h4><?php echo esc_html__( 'Add Comment', 'technoliga-support' ); ?></h4>
		<form method="post" action="">
			<?php wp_nonce_field( 'technoliga_add_comment_' . $ticket_id ); ?>
			<textarea name="comment_text" rows="5" class="widefat"></textarea>
			<?php submit_button( __( 'Submit Comment', 'technoliga-support' ), 'primary', 'technoliga_add_comment' ); ?>
		</form>
	</div>

	<p><a href="<?php echo esc_url( admin_url( 'admin.php?page=' . TECHNOLIGA_SUPPORT_SLUG ) ); ?>" class="button">← <?php echo esc_html__( 'Back to Tickets', 'technoliga-support' ); ?></a></p>
</div>
