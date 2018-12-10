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
        requireConfirmation: function() {
            return this.each(function() {
                return $(this).on('click', function(event) {
                    event.preventDefault();

                    $('#confirmation-message').remove();

                    var actionButton = $(this);
                    var confirmMessage = $(this).data('confirmation-message');

                    if (actionButton.is('a')) {
                        $('#confirmation-button').attr('href', actionButton.attr('href'));
                    }
                    
                    if (actionButton.is('button')) {
                        $('#confirmation-button').on('click', function(event) {
                            event.preventDefault();

                            return actionButton.closest('form').submit();
                        });
                    }

                    if (typeof(confirmMessage) !== 'undefined') {
                        $('#confirmation-modal .content').prepend('<h3 id="confirmation-message" class="ui dividing header red">' + confirmMessage + '</h3>');
                    }

                    return $('#confirmation-modal').modal('show');
                });
            });
        }
    });
})( jQuery );
