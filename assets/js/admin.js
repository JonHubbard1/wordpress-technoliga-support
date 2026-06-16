(function ($) {
	'use strict';

	$(function () {
		// --- Intake Wizard ----------------------------------------------------
		var $wizard = $('#ts-intake-wizard');
		if (!$wizard.length) return;

		var categories = window.tsIntakeQuestions || {};
		var catLabels = window.tsIntakeCategories || {};
		var currentStep = 1;

		function showStep(n) {
			$('.ts-step').removeClass('ts-step-active');
			$('.ts-step[data-step="' + n + '"]').addClass('ts-step-active');
			$('.ts-step-indicator .ts-step-dot').removeClass('active');
			$('.ts-step-indicator .ts-step-dot[data-step="' + n + '"]').addClass('active');
			currentStep = n;
			window.scrollTo(0, 0);
		}

		function renderQuestions(category) {
			var questions = categories[category] || [];
			var html = '';
			questions.forEach(function (q) {
				var required = q.required ? 'required' : '';
				var reqMark = q.required ? ' <span class="ts-required">*</span>' : '';
				var helpText = q.placeholder ? ' placeholder="' + escapeHtml(q.placeholder) + '"' : '';

				html += '<div class="ts-field" data-question="' + escapeHtml(q.name) + '">\n';
				html += '<label for="ts-q-' + escapeHtml(q.name) + '">' + escapeHtml(q.question) + reqMark + '</label>\n';

				if (q.type === 'select') {
					html += '<select id="ts-q-' + escapeHtml(q.name) + '" name="answers[' + escapeHtml(q.name) + ']" ' + required + '>\n';
					html += '<option value="">' + tsAdmin.selectOption + '</option>\n';
					for (var optVal in q.options) {
						if (q.options.hasOwnProperty(optVal)) {
							html += '<option value="' + escapeHtml(optVal) + '">' + escapeHtml(q.options[optVal]) + '</option>\n';
						}
					}
					html += '</select>\n';
				} else if (q.type === 'textarea') {
					html += '<textarea id="ts-q-' + escapeHtml(q.name) + '" name="answers[' + escapeHtml(q.name) + ']" rows="4" class="large-text" ' + required + helpText + '></textarea>\n';
				} else {
					html += '<input type="text" id="ts-q-' + escapeHtml(q.name) + '" name="answers[' + escapeHtml(q.name) + ']" class="regular-text" ' + required + helpText + '>\n';
				}

				html += '</div>\n';
			});
			$('#ts-questions-container').html(html);
		}

		function renderReview() {
			var cat = $('input[name="intake_category"]:checked').val() || '';
			$('#ts-review-category').text(catLabels[cat] ? catLabels[cat].label : cat);

			var answersHtml = '';
			var questions = categories[cat] || [];
			questions.forEach(function (q) {
				var val = $('*[name="answers[' + q.name + ']"]').val() || '';
				if (!val) return;
				if (q.type === 'select' && q.options && q.options[val]) {
					val = q.options[val];
				}
				answersHtml += '<div class="ts-review-box"><h4>' + escapeHtml(q.question) + '</h4><p>' + escapeHtml(val).replace(/\n/g, '<br>') + '</p></div>';
			});
			$('#ts-review-answers').html(answersHtml);
		}

		function escapeHtml(text) {
			if (!text) return '';
			var div = document.createElement('div');
			div.appendChild(document.createTextNode(text));
			return div.innerHTML;
		}

		function validateStep(step) {
			var valid = true;
			$('.ts-step[data-step="' + step + '"] [required]').each(function () {
				if (!$(this).val().trim()) {
					$(this).closest('.ts-field').addClass('ts-field-error');
					valid = false;
				} else {
					$(this).closest('.ts-field').removeClass('ts-field-error');
				}
			});
			return valid;
		}

		// Step 1 -> Step 2
		$('#ts-btn-step-2').on('click', function () {
			var cat = $('input[name="intake_category"]:checked').val();
			if (!cat) {
				$('#ts-category-error').show();
				return;
			}
			$('#ts-category-error').hide();
			renderQuestions(cat);
			showStep(2);
		});

		// Step 2 -> Step 3
		$('#ts-btn-step-3').on('click', function () {
			if (!validateStep(2)) return;
			renderReview();
			showStep(3);
		});

		// Back buttons
		$('#ts-back-step-1').on('click', function () { showStep(1); });
		$('#ts-back-step-2').on('click', function () { showStep(2); });

		// Category change -> clear questions
		$('input[name="intake_category"]').on('change', function () {
			$('#ts-category-error').hide();
		});

		// Remove error styling on input
		$(document).on('input change', '.ts-field-error input, .ts-field-error textarea, .ts-field-error select', function () {
			$(this).closest('.ts-field').removeClass('ts-field-error');
		});

		// --- Status change confirm --------------------------------------------
		$('form:has(select[name="status"])').on('submit', function () {
			if (typeof tsAdmin !== 'undefined' && tsAdmin.confirmStatus) {
				return confirm(tsAdmin.confirmStatus);
			}
			return true;
		});

		// --- Description character counter ------------------------------------
		var $desc = $('#description');
		if ($desc.length) {
			var max = 10000;
			var $counter = $('<p class="description" style="margin-top:4px;"><span id="ts-char-count">0</span>/' + max + '</p>');
			$desc.after($counter);

			function updateCount() {
				var len = $desc.val().length;
				$('#ts-char-count').text(len);
				if (len > max) {
					$('#ts-char-count').css('color', '#b32d2e');
				} else {
					$('#ts-char-count').css('color', '');
				}
			}

			$desc.on('input', updateCount);
			updateCount();
		}
	});
})(jQuery);
