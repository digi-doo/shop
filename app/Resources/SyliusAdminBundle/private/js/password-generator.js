(function($) {
	$(document).ready(function() {
		function generatePassword() {
			var text = "";
			var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

			for (var i = 0; i < 10; i++)
				text += possible.charAt(Math.floor(Math.random() * possible.length));

			return text;
		}

		$('#password_code_generator').on('click', function() {
			var input = $('[data-generator-input]');

			$(input).val(generatePassword());
		});
	});
})(jQuery);