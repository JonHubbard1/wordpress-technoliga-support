<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wrap">
	<h1><?php echo esc_html__( 'Technoliga Support Settings', 'technoliga-support' ); ?></h1>

	<?php if ( $saved ) { ?>
		<div class="notice notice-success"><p><?php echo esc_html__( 'Settings saved successfully.', 'technoliga-support' ); ?></p></div>
	<?php } ?>

	<?php if ( $error ) { ?>
		<div class="notice notice-error"><p><?php echo esc_html( $error ); ?></p></div>
	<?php } ?>

	<form method="post" action="">
		<?php wp_nonce_field( 'technoliga_support_settings' ); ?>

		<table class="form-table" role="presentation">
			<tr>
				<th scope="row"><label for="api_key"><?php echo esc_html__( 'API Key', 'technoliga-support' ); ?></label></th>
				<td>
					<input type="text" name="api_key" id="api_key" value="<?php echo esc_attr( $settings['api_key'] ); ?>" class="regular-text" autocomplete="off">
					<p class="description"><?php echo esc_html__( 'Your Technoliga BMS API key. Must start with "tk_".', 'technoliga-support' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="base_url"><?php echo esc_html__( 'Base URL', 'technoliga-support' ); ?></label></th>
				<td>
					<input type="url" name="base_url" id="base_url" value="<?php echo esc_attr( $settings['base_url'] ); ?>" class="regular-text">
					<p class="description"><?php echo esc_html__( 'Technoliga BMS base URL, e.g. https://technoliga.co.uk', 'technoliga-support' ); ?></p>
				</td>
			</tr>
		</table>

		<?php submit_button( __( 'Save Settings', 'technoliga-support' ), 'primary', 'technoliga_support_save' ); ?>
	</form>
</div>
