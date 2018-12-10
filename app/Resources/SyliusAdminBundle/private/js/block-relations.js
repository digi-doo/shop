$(document).ready(function() {
    $('#bitbag_sylius_cms_plugin_block_tabType_0').on('change', function() {
        if($('#block-tag-select').hasClass('hidden')) {
            $('#block-tag-select').removeClass('hidden');
            $('#block-taxon-select').addClass('hidden');
        } else {
            $('#block-tag-select').addClass('hidden');
            $('#bitbag_sylius_cms_plugin_block_tabType_1').trigger('change');
        }
    });
    $('#bitbag_sylius_cms_plugin_block_tabType_1').on('change', function() {
        if($('#block-taxon-select').hasClass('hidden')) {
            $('#block-taxon-select').removeClass('hidden');
            $('#block-tag-select').addClass('hidden');
        } else {
            $('#block-taxon-select').addClass('hidden');
            $('#bitbag_sylius_cms_plugin_block_tabType_0').trigger('change');
        }
    });
});