(function($) {
	$(document).ready(function() {
		function generateCode() {
			var text = "";
			var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

			for (var i = 0; i < 10; i++)
				text += possible.charAt(Math.floor(Math.random() * possible.length));

			return text;
		}

		$('#product_code_generator').on('click', function() {
			var input = $('[data-generator-input]');

			$(input).val(generateCode());
		});

		$('[data-variant-generator-trigger]').on('click', function() {
			var productCode = $('[data-product-code').val();
			var input = $(this).prev('[data-generator-input]');

			$(input).val(productCode + '-' + generateCode());
		});
	});
})(jQuery);