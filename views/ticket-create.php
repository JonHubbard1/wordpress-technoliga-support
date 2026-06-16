<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Question definitions for the intake wizard.
 * Mirrors App\Services\TicketIntakeService::questionsForCategory()
 */
$intake_categories = array(
	'support_request' => array(
		'label' => __( 'Support Request', 'technoliga-support' ),
		'description' => __( 'Get help with something that is not working as expected.', 'technoliga-support' ),
	),
	'bug_report' => array(
		'label' => __( 'Bug Report', 'technoliga-support' ),
		'description' => __( 'Report a defect or unexpected behaviour.', 'technoliga-support' ),
	),
	'feature_request' => array(
		'label' => __( 'Feature Request', 'technoliga-support' ),
		'description' => __( 'Suggest a new feature or improvement.', 'technoliga-support' ),
	),
	'billing_enquiry' => array(
		'label' => __( 'Billing Enquiry', 'technoliga-support' ),
		'description' => __( 'Questions about invoices, payments or subscriptions.', 'technoliga-support' ),
	),
	'general_enquiry' => array(
		'label' => __( 'General Enquiry', 'technoliga-support' ),
		'description' => __( 'Anything else that does not fit the other categories.', 'technoliga-support' ),
	),
	'access_issue' => array(
		'label' => __( 'Access Issue', 'technoliga-support' ),
		'description' => __( 'Login, permissions, account or access problems.', 'technoliga-support' ),
	),
	'training_request' => array(
		'label' => __( 'Training Request', 'technoliga-support' ),
		'description' => __( 'Ask for training, documentation or a walkthrough.', 'technoliga-support' ),
	),
	'security_issue' => array(
		'label' => __( 'Security Issue', 'technoliga-support' ),
		'description' => __( 'Report a security concern or vulnerability.', 'technoliga-support' ),
	),
);

$intake_questions = array(
	'support_request' => array(
		array(
			'name' => 'expected_outcome',
			'question' => __( 'What outcome are you expecting?', 'technoliga-support' ),
			'type' => 'textarea',
			'required' => true,
			'placeholder' => __( 'Describe what you want to happen or what you need to achieve...', 'technoliga-support' ),
		),
		array(
			'name' => 'business_impact',
			'question' => __( 'What is the business impact?', 'technoliga-support' ),
			'type' => 'select',
			'required' => true,
			'options' => array(
				'no_impact' => __( 'No real impact', 'technoliga-support' ),
				'minor' => __( 'Minor inconvenience', 'technoliga-support' ),
				'workaround' => __( 'We have a workaround but it is slowing us down', 'technoliga-support' ),
				'blocking' => __( 'Blocking important work', 'technoliga-support' ),
				'critical' => __( 'Critical / revenue-impacting', 'technoliga-support' ),
			),
		),
		array(
			'name' => 'what_happened',
			'question' => __( 'What is happening vs. what you expected?', 'technoliga-support' ),
			'type' => 'textarea',
			'required' => true,
			'placeholder' => __( 'e.g. I expected the export to include all rows, but only the first 10 are downloaded.', 'technoliga-support' ),
		),
		array(
			'name' => 'affected_url',
			'question' => __( 'Which page or area of the project is affected?', 'technoliga-support' ),
			'type' => 'text',
			'required' => false,
			'placeholder' => __( 'e.g. https://example.com/admin/reports', 'technoliga-support' ),
		),
		array(
			'name' => 'tried_already',
			'question' => __( 'What have you already tried?', 'technoliga-support' ),
			'type' => 'textarea',
			'required' => false,
			'placeholder' => __( 'e.g. Cleared cache, tried a different browser, logged out and back in.', 'technoliga-support' ),
		),
	),
	'bug_report' => array(
		array(
			'name' => 'expected_outcome',
			'question' => __( 'What outcome are you expecting?', 'technoliga-support' ),
			'type' => 'textarea',
			'required' => true,
			'placeholder' => __( 'Describe what you want to happen or what you need to achieve...', 'technoliga-support' ),
		),
		array(
			'name' => 'business_impact',
			'question' => __( 'What is the business impact?', 'technoliga-support' ),
			'type' => 'select',
			'required' => true,
			'options' => array(
				'no_impact' => __( 'No real impact', 'technoliga-support' ),
				'minor' => __( 'Minor inconvenience', 'technoliga-support' ),
				'workaround' => __( 'We have a workaround but it is slowing us down', 'technoliga-support' ),
				'blocking' => __( 'Blocking important work', 'technoliga-support' ),
				'critical' => __( 'Critical / revenue-impacting', 'technoliga-support' ),
			),
		),
		array(
			'name' => 'reproduction_steps',
			'question' => __( 'Can you provide exact steps to reproduce the bug?', 'technoliga-support' ),
			'type' => 'textarea',
			'required' => true,
			'placeholder' => __( "1. Go to ...\n2. Click ...\n3. Enter ...\n4. Observe ...", 'technoliga-support' ),
		),
		array(
			'name' => 'expected_vs_actual',
			'question' => __( 'What did you expect to happen, and what actually happened?', 'technoliga-support' ),
			'type' => 'textarea',
			'required' => true,
			'placeholder' => __( 'Expected: the form submits. Actual: a red error banner appears.', 'technoliga-support' ),
		),
		array(
			'name' => 'environment',
			'question' => __( 'Where are you seeing this?', 'technoliga-support' ),
			'type' => 'text',
			'required' => false,
			'placeholder' => __( 'e.g. Chrome 120 on Windows 11, iPhone Safari, Android app', 'technoliga-support' ),
		),
		array(
			'name' => 'error_message',
			'question' => __( 'Is there an error message, screenshot or reference number?', 'technoliga-support' ),
			'type' => 'textarea',
			'required' => false,
			'placeholder' => __( 'Paste the exact error text or describe what you see.', 'technoliga-support' ),
		),
		array(
			'name' => 'affected_url',
			'question' => __( 'Which URL or area is affected?', 'technoliga-support' ),
			'type' => 'text',
			'required' => false,
			'placeholder' => __( 'e.g. https://example.com/checkout', 'technoliga-support' ),
		),
	),
	'feature_request' => array(
		array(
			'name' => 'expected_outcome',
			'question' => __( 'What outcome are you expecting?', 'technoliga-support' ),
			'type' => 'textarea',
			'required' => true,
			'placeholder' => __( 'Describe what you want to happen or what you need to achieve...', 'technoliga-support' ),
		),
		array(
			'name' => 'business_impact',
			'question' => __( 'What is the business impact?', 'technoliga-support' ),
			'type' => 'select',
			'required' => true,
			'options' => array(
				'no_impact' => __( 'No real impact', 'technoliga-support' ),
				'minor' => __( 'Minor inconvenience', 'technoliga-support' ),
				'workaround' => __( 'We have a workaround but it is slowing us down', 'technoliga-support' ),
				'blocking' => __( 'Blocking important work', 'technoliga-support' ),
				'critical' => __( 'Critical / revenue-impacting', 'technoliga-support' ),
			),
		),
		array(
			'name' => 'problem_solved',
			'question' => __( 'What problem should this feature solve?', 'technoliga-support' ),
			'type' => 'textarea',
			'required' => true,
			'placeholder' => __( 'e.g. Our team spends 30 minutes a day manually exporting data.', 'technoliga-support' ),
		),
		array(
			'name' => 'who_will_use',
			'question' => __( 'Who will use this feature and how?', 'technoliga-support' ),
			'type' => 'textarea',
			'required' => true,
			'placeholder' => __( 'e.g. Office managers will click a button to generate the daily report.', 'technoliga-support' ),
		),
		array(
			'name' => 'must_haves',
			'question' => __( 'What must the first version include?', 'technoliga-support' ),
			'type' => 'textarea',
			'required' => true,
			'placeholder' => __( 'List the minimum requirements. Avoid nice-to-haves for now.', 'technoliga-support' ),
		),
		array(
			'name' => 'nice_to_haves',
			'question' => __( 'Any nice-to-haves or future ideas?', 'technoliga-support' ),
			'type' => 'textarea',
			'required' => false,
			'placeholder' => __( 'Things that would be useful but are not essential.', 'technoliga-support' ),
		),
	),
	'billing_enquiry' => array(
		array(
			'name' => 'expected_outcome',
			'question' => __( 'What outcome are you expecting?', 'technoliga-support' ),
			'type' => 'textarea',
			'required' => true,
			'placeholder' => __( 'Describe what you want to happen or what you need to achieve...', 'technoliga-support' ),
		),
		array(
			'name' => 'business_impact',
			'question' => __( 'What is the business impact?', 'technoliga-support' ),
			'type' => 'select',
			'required' => true,
			'options' => array(
				'no_impact' => __( 'No real impact', 'technoliga-support' ),
				'minor' => __( 'Minor inconvenience', 'technoliga-support' ),
				'workaround' => __( 'We have a workaround but it is slowing us down', 'technoliga-support' ),
				'blocking' => __( 'Blocking important work', 'technoliga-support' ),
				'critical' => __( 'Critical / revenue-impacting', 'technoliga-support' ),
			),
		),
		array(
			'name' => 'billing_topic',
			'question' => __( 'What is your billing question about?', 'technoliga-support' ),
			'type' => 'select',
			'required' => true,
			'options' => array(
				'invoice' => __( 'Invoice', 'technoliga-support' ),
				'payment' => __( 'Payment', 'technoliga-support' ),
				'subscription' => __( 'Subscription', 'technoliga-support' ),
				'refund' => __( 'Refund', 'technoliga-support' ),
				'contract' => __( 'Contract or pricing', 'technoliga-support' ),
				'other' => __( 'Other', 'technoliga-support' ),
			),
		),
		array(
			'name' => 'invoice_reference',
			'question' => __( 'Do you have an invoice number, date or reference?', 'technoliga-support' ),
			'type' => 'text',
			'required' => false,
			'placeholder' => __( 'e.g. INV-2026-00123', 'technoliga-support' ),
		),
		array(
			'name' => 'details',
			'question' => __( 'Please describe your question or concern in detail.', 'technoliga-support' ),
			'type' => 'textarea',
			'required' => true,
			'placeholder' => __( 'Include amounts, dates, and anything else that will help us answer quickly.', 'technoliga-support' ),
		),
	),
	'general_enquiry' => array(
		array(
			'name' => 'expected_outcome',
			'question' => __( 'What outcome are you expecting?', 'technoliga-support' ),
			'type' => 'textarea',
			'required' => true,
			'placeholder' => __( 'Describe what you want to happen or what you need to achieve...', 'technoliga-support' ),
		),
		array(
			'name' => 'business_impact',
			'question' => __( 'What is the business impact?', 'technoliga-support' ),
			'type' => 'select',
			'required' => true,
			'options' => array(
				'no_impact' => __( 'No real impact', 'technoliga-support' ),
				'minor' => __( 'Minor inconvenience', 'technoliga-support' ),
				'workaround' => __( 'We have a workaround but it is slowing us down', 'technoliga-support' ),
				'blocking' => __( 'Blocking important work', 'technoliga-support' ),
				'critical' => __( 'Critical / revenue-impacting', 'technoliga-support' ),
			),
		),
		array(
			'name' => 'topic',
			'question' => __( 'What is your enquiry about?', 'technoliga-support' ),
			'type' => 'text',
			'required' => true,
			'placeholder' => __( 'e.g. Partnership opportunity, service question, feedback', 'technoliga-support' ),
		),
		array(
			'name' => 'details',
			'question' => __( 'Please provide the details.', 'technoliga-support' ),
			'type' => 'textarea',
			'required' => true,
			'placeholder' => __( 'Give us enough context to route your enquiry to the right person.', 'technoliga-support' ),
		),
	),
	'access_issue' => array(
		array(
			'name' => 'expected_outcome',
			'question' => __( 'What outcome are you expecting?', 'technoliga-support' ),
			'type' => 'textarea',
			'required' => true,
			'placeholder' => __( 'Describe what you want to happen or what you need to achieve...', 'technoliga-support' ),
		),
		array(
			'name' => 'business_impact',
			'question' => __( 'What is the business impact?', 'technoliga-support' ),
			'type' => 'select',
			'required' => true,
			'options' => array(
				'no_impact' => __( 'No real impact', 'technoliga-support' ),
				'minor' => __( 'Minor inconvenience', 'technoliga-support' ),
				'workaround' => __( 'We have a workaround but it is slowing us down', 'technoliga-support' ),
				'blocking' => __( 'Blocking important work', 'technoliga-support' ),
				'critical' => __( 'Critical / revenue-impacting', 'technoliga-support' ),
			),
		),
		array(
			'name' => 'issue_type',
			'question' => __( 'What kind of access problem are you having?', 'technoliga-support' ),
			'type' => 'select',
			'required' => true,
			'options' => array(
				'cannot_login' => __( 'Cannot log in', 'technoliga-support' ),
				'forgot_password' => __( 'Forgotten password', 'technoliga-support' ),
				'no_permission' => __( 'Missing permissions', 'technoliga-support' ),
				'account_locked' => __( 'Account locked', 'technoliga-support' ),
				'invite_needed' => __( 'Need an invite for a colleague', 'technoliga-support' ),
				'other' => __( 'Other', 'technoliga-support' ),
			),
		),
		array(
			'name' => 'username_or_email',
			'question' => __( 'Which email address or username is affected?', 'technoliga-support' ),
			'type' => 'text',
			'required' => false,
			'placeholder' => __( 'e.g. name@company.com', 'technoliga-support' ),
		),
		array(
			'name' => 'error_message',
			'question' => __( 'What error or message do you see?', 'technoliga-support' ),
			'type' => 'textarea',
			'required' => false,
			'placeholder' => __( 'Paste the exact message if there is one.', 'technoliga-support' ),
		),
	),
	'training_request' => array(
		array(
			'name' => 'expected_outcome',
			'question' => __( 'What outcome are you expecting?', 'technoliga-support' ),
			'type' => 'textarea',
			'required' => true,
			'placeholder' => __( 'Describe what you want to happen or what you need to achieve...', 'technoliga-support' ),
		),
		array(
			'name' => 'business_impact',
			'question' => __( 'What is the business impact?', 'technoliga-support' ),
			'type' => 'select',
			'required' => true,
			'options' => array(
				'no_impact' => __( 'No real impact', 'technoliga-support' ),
				'minor' => __( 'Minor inconvenience', 'technoliga-support' ),
				'workaround' => __( 'We have a workaround but it is slowing us down', 'technoliga-support' ),
				'blocking' => __( 'Blocking important work', 'technoliga-support' ),
				'critical' => __( 'Critical / revenue-impacting', 'technoliga-support' ),
			),
		),
		array(
			'name' => 'topic',
			'question' => __( 'What do you need training or documentation on?', 'technoliga-support' ),
			'type' => 'textarea',
			'required' => true,
			'placeholder' => __( 'e.g. How to run the monthly reports, how to manage user permissions.', 'technoliga-support' ),
		),
		array(
			'name' => 'audience',
			'question' => __( 'Who needs the training?', 'technoliga-support' ),
			'type' => 'text',
			'required' => false,
			'placeholder' => __( 'e.g. New office manager, whole team, just me', 'technoliga-support' ),
		),
		array(
			'name' => 'format',
			'question' => __( 'What format would work best?', 'technoliga-support' ),
			'type' => 'select',
			'required' => false,
			'options' => array(
				'video' => __( 'Recorded video walkthrough', 'technoliga-support' ),
				'call' => __( 'Live video call', 'technoliga-support' ),
				'guide' => __( 'Written guide', 'technoliga-support' ),
				'in_person' => __( 'In person', 'technoliga-support' ),
				'unsure' => __( 'Not sure', 'technoliga-support' ),
			),
		),
	),
	'security_issue' => array(
		array(
			'name' => 'expected_outcome',
			'question' => __( 'What outcome are you expecting?', 'technoliga-support' ),
			'type' => 'textarea',
			'required' => true,
			'placeholder' => __( 'Describe what you want to happen or what you need to achieve...', 'technoliga-support' ),
		),
		array(
			'name' => 'business_impact',
			'question' => __( 'What is the business impact?', 'technoliga-support' ),
			'type' => 'select',
			'required' => true,
			'options' => array(
				'no_impact' => __( 'No real impact', 'technoliga-support' ),
				'minor' => __( 'Minor inconvenience', 'technoliga-support' ),
				'workaround' => __( 'We have a workaround but it is slowing us down', 'technoliga-support' ),
				'blocking' => __( 'Blocking important work', 'technoliga-support' ),
				'critical' => __( 'Critical / revenue-impacting', 'technoliga-support' ),
			),
		),
		array(
			'name' => 'severity',
			'question' => __( 'How severe is this issue?', 'technoliga-support' ),
			'type' => 'select',
			'required' => true,
			'options' => array(
				'suspicious' => __( 'Suspicious but not confirmed', 'technoliga-support' ),
				'confirmed' => __( 'Confirmed vulnerability', 'technoliga-support' ),
				'active_exploit' => __( 'Actively being exploited', 'technoliga-support' ),
				'data_exposure' => __( 'Potential data exposure', 'technoliga-support' ),
			),
		),
		array(
			'name' => 'summary',
			'question' => __( 'Please describe the security concern.', 'technoliga-support' ),
			'type' => 'textarea',
			'required' => true,
			'placeholder' => __( 'Include what you observed, where, and when.', 'technoliga-support' ),
		),
		array(
			'name' => 'affected_area',
			'question' => __( 'Which project area or URL is affected?', 'technoliga-support' ),
			'type' => 'text',
			'required' => false,
			'placeholder' => __( 'e.g. https://example.com/login', 'technoliga-support' ),
		),
		array(
			'name' => 'reproduction',
			'question' => __( 'Can the issue be reproduced? If so, how?', 'technoliga-support' ),
			'type' => 'textarea',
			'required' => false,
			'placeholder' => __( 'Steps or conditions that trigger the issue.', 'technoliga-support' ),
		),
	),
);
?>
<div class="wrap technoliga-wrap">
	<h1><?php echo esc_html__( 'New Support Ticket', 'technoliga-support' ); ?></h1>

	<?php if ( ! empty( $error ) ) { ?>
		<div class="notice notice-error"><p><?php echo esc_html( $error ); ?></p></div>
	<?php } ?>

	<!-- Step Indicator -->
	<ul class="ts-step-indicator">
		<li class="ts-step-dot active" data-step="1">1</li>
		<li class="ts-step-line"></li>
		<li class="ts-step-dot" data-step="2">2</li>
		<li class="ts-step-line"></li>
		<li class="ts-step-dot" data-step="3">3</li>
	</ul>

	<form method="post" action="" id="ts-intake-wizard">
		<?php wp_nonce_field( 'technoliga_create_ticket' ); ?>

		<!-- Step 1: Category -->
		<div class="ts-step ts-step-active" data-step="1">
			<h2><?php echo esc_html__( 'Step 1 of 3 — Choose a category', 'technoliga-support' ); ?></h2>
			<p class="description"><?php echo esc_html__( 'What kind of support do you need?', 'technoliga-support' ); ?></p>

			<div class="ts-category-grid">
				<?php foreach ( $intake_categories as $key => $cat ) { ?>
					<div class="ts-category-option">
						<input type="radio" name="intake_category" id="ts-cat-<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( $key ); ?>" <?php checked( $prefill['intake_category'], $key ); ?>>
						<label for="ts-cat-<?php echo esc_attr( $key ); ?>">
							<span class="ts-cat-title"><?php echo esc_html( $cat['label'] ); ?></span>
							<span class="ts-cat-desc"><?php echo esc_html( $cat['description'] ); ?></span>
						</label>
					</div>
				<?php } ?>
			</div>

			<p id="ts-category-error" class="notice notice-error" style="display:none;"><?php echo esc_html__( 'Please select a category to continue.', 'technoliga-support' ); ?></p>

			<div class="ts-wizard-actions">
				<button type="button" class="button button-primary" id="ts-btn-step-2"><?php echo esc_html__( 'Continue →', 'technoliga-support' ); ?></button>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=' . TECHNOLIGA_SUPPORT_SLUG ) ); ?>" class="button button-secondary"><?php echo esc_html__( 'Cancel', 'technoliga-support' ); ?></a>
			</div>
		</div>

		<!-- Step 2: Questions -->
		<div class="ts-step" data-step="2">
			<h2><?php echo esc_html__( 'Step 2 of 3 — Answer a few questions', 'technoliga-support' ); ?></h2>
			<p class="description"><?php echo esc_html__( 'These details help us route and prioritise your ticket correctly.', 'technoliga-support' ); ?></p>

			<div id="ts-questions-container"></div>

			<div class="ts-wizard-actions">
				<button type="button" class="button button-primary" id="ts-btn-step-3"><?php echo esc_html__( 'Continue →', 'technoliga-support' ); ?></button>
				<button type="button" class="button button-secondary" id="ts-back-step-1">← <?php echo esc_html__( 'Back', 'technoliga-support' ); ?></button>
			</div>
		</div>

		<!-- Step 3: Details -->
		<div class="ts-step" data-step="3">
			<h2><?php echo esc_html__( 'Step 3 of 3 — Final details', 'technoliga-support' ); ?></h2>
			<p class="description"><?php echo esc_html__( 'Add a subject and any extra context. Review your answers before submitting.', 'technoliga-support' ); ?></p>

			<div class="ts-card">
				<h3><?php echo esc_html__( 'Ticket Summary', 'technoliga-support' ); ?></h3>
				<div class="ts-review-box">
					<h4><?php echo esc_html__( 'Category', 'technoliga-support' ); ?></h4>
					<p id="ts-review-category"></p>
				</div>
				<div id="ts-review-answers"></div>
			</div>

			<div class="ts-field">
				<label for="subject"><?php echo esc_html__( 'Subject', 'technoliga-support' ); ?> <span class="ts-required">*</span></label>
				<input type="text" name="subject" id="subject" value="<?php echo esc_attr( $prefill['subject'] ); ?>" class="regular-text" required maxlength="255">
			</div>

			<div class="ts-field">
				<label for="description"><?php echo esc_html__( 'Additional description (optional)', 'technoliga-support' ); ?></label>
				<textarea name="description" id="description" rows="4" class="large-text" maxlength="10000"><?php echo esc_textarea( $prefill['description'] ); ?></textarea>
			</div>

			<div class="ts-field">
				<label for="priority"><?php echo esc_html__( 'Priority', 'technoliga-support' ); ?> <span class="ts-required">*</span></label>
				<select name="priority" id="priority" required>
					<option value="low" <?php selected( $prefill['priority'], 'low' ); ?><?php echo esc_html__( 'Low', 'technoliga-support' ); ?></option>
					<option value="medium" <?php selected( $prefill['priority'], 'medium' ); ?><?php echo esc_html__( 'Medium', 'technoliga-support' ); ?></option>
					<option value="high" <?php selected( $prefill['priority'], 'high' ); ?><?php echo esc_html__( 'High', 'technoliga-support' ); ?></option>
					<option value="urgent" <?php selected( $prefill['priority'], 'urgent' ); ?><?php echo esc_html__( 'Urgent', 'technoliga-support' ); ?></option>
				</select>
			</div>

			<div class="ts-wizard-actions">
				<?php submit_button( __( 'Submit Ticket', 'technoliga-support' ), 'primary', 'technoliga_create_ticket' ); ?>
				<button type="button" class="button button-secondary" id="ts-back-step-2">← <?php echo esc_html__( 'Back', 'technoliga-support' ); ?></button>
			</div>
		</div>
	</form>
</div>

<script>
/* <![CDATA[ */
window.tsIntakeQuestions = <?php echo wp_json_encode( $intake_questions ); ?>;
window.tsIntakeCategories = <?php echo wp_json_encode( $intake_categories ); ?>;
/* ]] >*/
</script>
