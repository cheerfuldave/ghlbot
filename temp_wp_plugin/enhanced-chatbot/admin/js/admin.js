
jQuery(document).ready(function($) {
    // Tab functionality
    $('.juliusai-tab').on('click', function() {
        $('.juliusai-tab').removeClass('active');
        $('.juliusai-tab-content').removeClass('active');
        
        $(this).addClass('active');
        $($(this).data('tab')).addClass('active');
    });

    // Preview window functionality
    $('.preview-toggle').on('click', function() {
        $('.preview-window').toggleClass('open');
    });

    // Save settings via AJAX
    $('#juliusai-settings-form').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: juliusAjax.ajaxurl,
            type: 'POST',
            data: {
                action: 'save_juliusai_settings',
                settings: $(this).serialize()
            },
            success: function(response) {
                alert('Settings saved successfully!');
            }
        });
    });
});
