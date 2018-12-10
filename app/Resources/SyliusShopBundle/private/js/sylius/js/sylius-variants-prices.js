/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

(function ( $ ) {
    'use strict';

    $.fn.extend({
        variantPrices: function () {
            if ($('#sylius-variants-pricing').length > 0) {
                handleProductOptionsChange();
            } else if ($("#sylius-product-variants").length > 0) {
                handleProductVariantsChange();
            }
        }
    });
})( jQuery );

function handleProductOptionsChange() {
    $('[name*="sylius_add_to_cart[cartItem][variant]"]').on('change', function() {
        var $selector = '';

        $('#sylius-product-adding-to-cart select[data-option]').each(function() {
            var option = $(this).find('option:selected').val();
            $selector += '[data-' + $(this).attr('data-option') + '="' + option + '"]';
        });

        var $price = $('#sylius-variants-pricing').find($selector).attr('data-value');
        var $priceTax = $('#sylius-variants-pricing').find($selector).attr('data-valuetax');

        if ($price !== undefined) {
            $('#product-price').text($price);
            $('#product-price-tax').text($priceTax);
            $('#product-price-tax-complete').text($priceTax);
            $('button[type=submit]').removeAttr('disabled');
        } else {
            $('#product-price').text($('#sylius-variants-pricing').attr('data-unavailable-text'));
            $('#product-price-tax').text($('#sylius-variants-pricing').attr('data-unavailable-text'));
            $('#product-price-tax-complete').text($('#sylius-variants-pricing').attr('data-unavailable-text'));
            $('button[type=submit]').attr('disabled', 'disabled');
        }
    });
}

function handleProductVariantsChange() {
    $('[name="sylius_add_to_cart[cartItem][variant]"]').on('change', function() {
        var $price = $(this).parents('tr').find('.sylius-product-variant-price').text();
        $('#product-price').text($price);
    });
}
