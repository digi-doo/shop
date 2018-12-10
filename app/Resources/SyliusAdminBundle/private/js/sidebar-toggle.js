$(document).ready(function() {
    // Active sidebar handling
    $('#sidebar-toggle').on('click', function() {
        if ($('#sidebar').hasClass('visible')) {
            localStorage.setItem('sidebarVisible', 'no');    
        }
        if (!$('#sidebar').hasClass('visible')) {
            localStorage.setItem('sidebarVisible', 'yes');    
        }
    });

    var visibleSidebar = localStorage.getItem('sidebarVisible');
    if (visibleSidebar === 'no') {
        $('#sidebar').removeClass('visible');
    }
});