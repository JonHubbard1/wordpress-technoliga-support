(function ($) {
	'use strict';

	$(function () {
		// --- Intake Wizard ----------------------------------------------------
		var $wizard = $('#ts-intake-wizard');
		if (!$wizard.length) return;

		var categories = window.tsIntakeQuestions || {};
		var catLabels = window.tsIntakeCategories || {};
		var currentStep = 1;
		var analyzeResponse = null;
		var clarificationQuestions = [];

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

		function renderClarificationQuestions() {
			if (!clarificationQuestions.length) return;
			var html = '';
			clarificationQuestions.forEach(function (q, i) {
				html += '<div class="ts-field" data-question="' + escapeHtml(q.name) + '">\n';
				html += '<label for="ts-clarify-' + i + '">' + escapeHtml(q.question) + ' <span class="ts-required">*</span></label>\n';
				html += '<p class="description">' + escapeHtml(q.reason) + '</p>\n';
				html += '<textarea id="ts-clarify-' + i + '" name="clarification[' + escapeHtml(q.name) + ']" rows="3" class="large-text" required></textarea>\n';
				html += '</div>\n';
			});
			$('#ts-clarification-container').html(html);
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

			// Also show clarification answers in review
			if (clarificationQuestions.length) {
				clarificationQuestions.forEach(function (q) {
					var val = $('*[name="clarification[' + q.name + ']"]').val() || '';
					if (val) {
						answersHtml += '<div class="ts-review-box"><h4>' + escapeHtml(q.question) + '</h4><p>' + escapeHtml(val).replace(/\n/g, '<br>') + '</p></div>';
					}
				});
			}

			$('#ts-review-answers').html(answersHtml);
		}

		function setSuggestedSubject(subject) {
			if (!subject) return;
			$('#ts-subject-hidden').val(subject);
			$('#ts-review-subject').text(subject);
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

		function collectAnswers() {
			var answers = {};
			$('input[name^="answers["], textarea[name^="answers["], select[name^="answers["]').each(function () {
				var name = $(this).attr('name').match(/answers\[(.+?)\]/);
				if (name) {
					answers[name[1]] = $(this).val();
				}
			});
			return answers;
		}

		function showLoading(msg) {
			$('#ts-loading-overlay').show().find('.ts-loading-text').text(msg || tsAdmin.analyzing);
		}

		function hideLoading() {
			$('#ts-loading-overlay').hide();
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

		// Step 2 -> Analyze (AJAX)
		$('#ts-btn-step-3').on('click', function () {
			if (!validateStep(2)) return;

			var cat = $('input[name="intake_category"]:checked').val();
			var answers = collectAnswers();

			showLoading(tsAdmin.analyzing || 'Analyzing your answers...');

			$.ajax({
				url: tsAdmin.apiUrl + '/api/v1/intake/analyze',
				method: 'POST',
				contentType: 'application/json',
				beforeSend: function (xhr) {
					xhr.setRequestHeader('Authorization', 'Bearer ' + tsAdmin.apiKey);
				},
				data: JSON.stringify({
					intake_category: cat,
					answers: answers,
					subject: '',
					description: '',
					priority: 'medium'
				}),
				success: function (response) {
					hideLoading();
					if (response.success && response.data) {
						analyzeResponse = response.data;
						if (response.data.clarification_needed && response.data.clarification_questions.length) {
							clarificationQuestions = response.data.clarification_questions;
							renderClarificationQuestions();
							showStep(2.5); // clarification step
						} else {
							// Pre-fill subject and priority
							var suggestedSubject = response.data.suggested_subject || '';
							var suggestedPriority = response.data.suggested_priority || 'medium';
							setSuggestedSubject(suggestedSubject);
							$('#priority').val(suggestedPriority);
							renderReview();
							showStep(3);
						}
					} else {
						// Fallback: go to step 3 with fallback subject if none provided
						var fallbackSubject = response.data.suggested_subject || ((catLabels[cat] ? catLabels[cat].label : 'Support') + ' request');
						setSuggestedSubject(fallbackSubject);
						$('#priority').val(response.data.suggested_priority || 'medium');
						renderReview();
						showStep(3);
					}
				},
				error: function () {
					hideLoading();
					// On error, generate a fallback subject and proceed
					var cat = $('input[name="intake_category"]:checked').val() || '';
					var fallbackSubject = (catLabels[cat] ? catLabels[cat].label : 'Support') + ' request';
					setSuggestedSubject(fallbackSubject);
					renderReview();
					showStep(3);
				}
			});
		});

		// Clarification -> Step 3
		$('#ts-btn-clarify-next').on('click', function () {
			if (!validateStep(2.5)) return;

			// Merge clarification answers into main answers
			clarificationQuestions.forEach(function (q) {
				var val = $('textarea[name="clarification[' + q.name + ']"]').val() || '';
				var $hidden = $('<input type="hidden" name="answers[' + q.name + ']" value="' + escapeHtml(val) + '">');
				$wizard.append($hidden);
			});

			// Pre-fill subject/priority if available from earlier analyze
			if (analyzeResponse) {
				setSuggestedSubject(analyzeResponse.suggested_subject);
				if (analyzeResponse.suggested_priority) {
					$('#priority').val(analyzeResponse.suggested_priority);
				}
			}

			renderReview();
			showStep(3);
		});

		// Back buttons
		$('#ts-back-step-1').on('click', function () { showStep(1); });
		$('#ts-back-step-2').on('click', function () { showStep(2); });
		$('#ts-back-step-2b').on('click', function () { showStep(2); });

		// Category change -> clear questions
		$('input[name="intake_category"]').on('change', function () {
			$('#ts-category-error').hide();
		});

		// Remove error styling on input
		$(document).on('input change', '.ts-field-error input, .ts-field-error textarea, .ts-field-error select', function () {
			$(this).closest('.ts-field').removeClass('ts-field-error');
		});

		// Toggle additional description
		$('#ts-toggle-description').on('click', function () {
			var $wrap = $('#ts-description-wrap');
			if ($wrap.is(':visible')) {
				$wrap.hide();
				$(this).text('+ ' + tsAdmin.addDetails || '+ Add anything else?');
			} else {
				$wrap.show();
				$(this).text('- ' + tsAdmin.hideDetails || '- Hide');
				$('#description').focus();
			}
		});

		// --- Restore state on validation-error reload --------------------------
		var prefillAnswers = {};
		$('input[data-ts-prefill-answer]').each(function () {
			var name = $(this).data('ts-prefill-answer');
			prefillAnswers[name] = $(this).val();
		});

		var prefillClarifications = {};
		$('input[data-ts-prefill-clarification]').each(function () {
			var name = $(this).data('ts-prefill-clarification');
			prefillClarifications[name] = $(this).val();
		});

		if (Object.keys(prefillAnswers).length > 0) {
			var cat = $('input[name="intake_category"]:checked').val();
			if (cat && categories[cat]) {
				renderQuestions(cat);
				for (var key in prefillAnswers) {
					if (prefillAnswers.hasOwnProperty(key)) {
						$('[name="answers[' + key + ']"]').val(prefillAnswers[key]);
					}
				}
				for (var cKey in prefillClarifications) {
					if (prefillClarifications.hasOwnProperty(cKey)) {
						$('[name="answers[' + cKey + ']"]').val(prefillClarifications[cKey]);
					}
				}
				// Also restore subject text
				var hiddenSubject = $('#ts-subject-hidden').val();
				if (hiddenSubject) {
					$('#ts-review-subject').text(hiddenSubject);
				}
				renderReview();
				showStep(3);
			}
		}

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
