(function ($) {
	'use strict';

	$(function () {
		// Confirm status changes
		$('form:has(select[name="status"])').on('submit', function () {
			if (typeof tsAdmin !== 'undefined' && tsAdmin.confirmStatus) {
				return confirm(tsAdmin.confirmStatus);
			}
			return true;
		});

		// Simple character counter for description
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
