<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wrap technoliga-wrap">
	<h1><?php echo esc_html__( 'Support Tickets', 'technoliga-support' ); ?></h1>

	<?php settings_errors( 'technoliga_support' ); ?>

	<div class="tablenav top">
		<div class="alignleft actions">
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=' . TECHNOLIGA_SUPPORT_SLUG . '-create' ) ); ?>" class="page-title-action"><?php echo esc_html__( 'New Ticket', 'technoliga-support' ); ?></a>
		</div>
	</div>

	<?php $table->display(); ?>
</div>
