(function($) {
	$(document).ready(function() {
		/**
		 * Disable delete button of already defined variant
		 * in product variant generator due to bug in Sylius.
		 * If variant is already used in order.
		 */
		var inputs = $('form[name="sylius_product_generate_variants"] input[id$="_code"]');

		inputs.each(function() {
			if ($(this).attr('disabled') === 'disabled') {
				var parent = $(this).closest('.segment');
				var button = parent.next('a[data-form-collection="delete"]');

				button.addClass('disabled');		
			}
		});
	});
})(jQuery);