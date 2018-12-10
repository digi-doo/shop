$(document).ready(function() {
    // Product detail toggles
    $('#sylius_product_variant_channelPricings_default_discountLimitType_0').on('change', function() {
        if ($(this).is(':checked')) {
            $('.discount__limit--stock').addClass('hidden');
            $('.discount__limit--datetime').addClass('hidden');
        }
    });
    $('#sylius_product_variant_channelPricings_default_discountLimitType_1').on('change', function() {
        if ($(this).is(':checked')) {
            $('.discount__limit--stock').removeClass('hidden');
            $('.discount__limit--datetime').addClass('hidden');
        }
    });
    $('#sylius_product_variant_channelPricings_default_discountLimitType_2').on('change', function() {
       if ($(this).is(':checked')) {
            $('.discount__limit--datetime').removeClass('hidden');
            $('.discount__limit--stock').addClass('hidden');
        } 
    });

    // Product bulk update discount toggles
    $('#discount_limit_type_no_change').on('change', function() {
        if ($(this).is(':checked')) {
            $('.discount__limit--none').addClass('hidden');
            $('.discount__limit--stock').addClass('hidden');
            $('.discount__limit--datetime').addClass('hidden');
            $('.discount__limit--no_change').removeClass('hidden');
        }
    });

    $('#discount_limit_type_none').on('change', function() {
        if ($(this).is(':checked')) {
            $('.discount__limit--no_change').addClass('hidden');
            $('.discount__limit--stock').addClass('hidden');
            $('.discount__limit--datetime').addClass('hidden');
            $('.discount__limit--none').removeClass('hidden');
        }
    });

    $('#discount_limit_type_stock').on('change', function() {
        if ($(this).is(':checked')) {
            $('.discount__limit--none').addClass('hidden');
            $('.discount__limit--no_change').addClass('hidden');
            $('.discount__limit--datetime').addClass('hidden');
            $('.discount__limit--stock').removeClass('hidden');
        }
    });

    $('#discount_limit_type_datetime').on('change', function() {
        if ($(this).is(':checked')) {
            $('.discount__limit--none').addClass('hidden');
            $('.discount__limit--stock').addClass('hidden');
            $('.discount__limit--no_change').addClass('hidden');
            $('.discount__limit--datetime').removeClass('hidden');
        }
    });
});