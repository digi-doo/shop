(function ( $ ) {
    'use strict';

    $.fn.extend({
        variantsAvailability: function () {
            if ($('#sylius-variants-availability').length > 0) {
                handleVariantAvailabilityChange();
            }
        }
    });
})( jQuery );

function handleVariantAvailabilityChange() {
    $('[name*="sylius_add_to_cart[cartItem][variant]"]').on('change', function() {
        var $selector = '';

        $('#sylius-product-adding-to-cart select[data-option]').each(function() {
            var option = $(this).find('option:selected').val();
            $selector += '[data-' + $(this).attr('data-option') + '="' + option + '"]';
        });

        var $availability = $('#sylius-variants-availability').find($selector).attr('data-availability');
        var $availabilityClass = $('#sylius-variants-availability').find($selector).attr('data-availability-class');

        $('#variant-stock-info').text($availability);
        $('#variant-stock-info').removeClass().addClass($availabilityClass);
    });
}