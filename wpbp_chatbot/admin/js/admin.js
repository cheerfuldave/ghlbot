
jQuery(document).ready(function($) {
    // Tab functionality
    $('.nav-tab').on('click', function(e) {
        e.preventDefault();
        $('.nav-tab').removeClass('nav-tab-active');
        $('.tab-content').removeClass('active');
        
        $(this).addClass('nav-tab-active');
        $($(this).data('tab')).addClass('active');
    });

    // Preview window functionality
    $('.preview-toggle').on('click', function() {
        $('.preview-window').toggleClass('open');
    });

    // AJAX save settings
    $('#ayd-settings-form').on('submit', function(e) {
        e.preventDefault();
        
        var formData = $(this).serialize();
        
        $.ajax({
            url: aydAjax.ajaxurl,
            type: 'POST',
            data: {
                action: 'save_ayd_settings',
                formData: formData
            },
            success: function(response) {
                alert('Settings saved successfully!');
            }
        });
    });
});
