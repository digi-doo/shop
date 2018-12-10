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
        addressBook: function () {
            var element = $(this);
            var select = element.find('.address-book-select');

            select.on('change', function() {
                $.each(element.find('input'), function (key, input) {
                    $(input).val('');
                });
                // $.each(element.find('select:not(.custom-select)'), function (key, select) {
                //     $(select).val('');
                // });

                $.each($("option:selected").data(), function (property, value) {
                    var field = findByName(property);
                    field.val(value);
                });
            });

            var parseKey = function (key) {
                return key.replace(/(_\w)/g, function (words) {return words[1].toUpperCase()});
            };
            var findByName = function (name) {
                return element.find('[name*=' + parseKey(name) + ']');
            };
        }
    });
})( jQuery );
