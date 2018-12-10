(function ($) {
    'use strict';

    $('#bulk-check-rows').on('click', function(e) {
        var table= $(e.target).closest('table');
        $('td input.bulk-select-checkbox', table).prop('checked', this.checked);
    });
})(jQuery);