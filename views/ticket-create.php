<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wrap technoliga-wrap">
	<h1><?php echo esc_html__( 'New Support Ticket', 'technoliga-support' ); ?></h1>

	<?php if ( ! empty( $error ) ) { ?>
		<div class="notice notice-error"><p><?php echo esc_html( $error ); ?></p></div>
	<?php } ?>

	<form method="post" action="">
		<?php wp_nonce_field( 'technoliga_create_ticket' ); ?>

		<table class="form-table" role="presentation">
			<tr>
				<th scope="row"><label for="subject"><?php echo esc_html__( 'Subject', 'technoliga-support' ); ?></label></th>
				<td>
					<input type="text" name="subject" id="subject" value="<?php echo esc_attr( $prefill['subject'] ); ?>" class="regular-text" required maxlength="255">
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="intake_category"><?php echo esc_html__( 'Category', 'technoliga-support' ); ?></label></th>
				<td>
					<select name="intake_category" id="intake_category">
						<option value="support_request" <?php selected( $prefill['intake_category'], 'support_request' ); ?><?php echo esc_html__( 'Support Request', 'technoliga-support' ); ?></option>
						<option value="bug_report" <?php selected( $prefill['intake_category'], 'bug_report' ); ?><?php echo esc_html__( 'Bug Report', 'technoliga-support' ); ?></option>
						<option value="feature_request" <?php selected( $prefill['intake_category'], 'feature_request' ); ?><?php echo esc_html__( 'Feature Request', 'technoliga-support' ); ?></option>
						<option value="question" <?php selected( $prefill['intake_category'], 'question' ); ?><?php echo esc_html__( 'Question', 'technoliga-support' ); ?></option>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="priority"><?php echo esc_html__( 'Priority', 'technoliga-support' ); ?></label></th>
				<td>
					<select name="priority" id="priority">
						<option value="low" <?php selected( $prefill['priority'], 'low' ); ?><?php echo esc_html__( 'Low', 'technoliga-support' ); ?></option>
						<option value="medium" <?php selected( $prefill['priority'], 'medium' ); ?><?php echo esc_html__( 'Medium', 'technoliga-support' ); ?></option>
						<option value="high" <?php selected( $prefill['priority'], 'high' ); ?><?php echo esc_html__( 'High', 'technoliga-support' ); ?></option>
						<option value="urgent" <?php selected( $prefill['priority'], 'urgent' ); ?><?php echo esc_html__( 'Urgent', 'technoliga-support' ); ?></option>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="description"><?php echo esc_html__( 'Description', 'technoliga-support' ); ?></label></th>
				<td>
					<textarea name="description" id="description" rows="8" class="large-text" required maxlength="10000"><?php echo esc_textarea( $prefill['description'] ); ?></textarea>
				</td>
			</tr>
		</table>

		<?php submit_button( __( 'Submit Ticket', 'technoliga-support' ), 'primary', 'technoliga_create_ticket' ); ?>
		<a href="<?php echo esc_url( admin_url( 'admin.php?page=' . TECHNOLIGA_SUPPORT_SLUG ) ); ?>" class="button button-secondary"><?php echo esc_html__( 'Cancel', 'technoliga-support' ); ?></a>
	</form>
</div>
