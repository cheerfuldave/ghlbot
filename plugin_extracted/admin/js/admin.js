
jQuery(document).ready(function($) {
    // Tab switching functionality
    $('.nav-tab').on('click', function(e) {
        e.preventDefault();
        var targetTab = $(this).data('tab');
        
        // Update active states
        $('.nav-tab').removeClass('nav-tab-active');
        $(this).addClass('nav-tab-active');
        
        // Show target content
        $('.tab-content').hide();
        $('#' + targetTab).fadeIn();
        
        // Save active tab to user preferences
        if (typeof localStorage !== 'undefined') {
            localStorage.setItem('activeSettingsTab', targetTab);
        }
    });

    // AJAX preview functionality
    $('.preview-button').on('click', function(e) {
        e.preventDefault();
        var $previewWindow = $('.preview-window');
        var $loading = $('.loading');
        
        $loading.show();
        $previewWindow.removeClass('active');

        // Simulate AJAX call
        setTimeout(function() {
            $previewWindow.addClass('active');
            $loading.hide();
        }, 500);
    });

    // Form submission handling
    $('#settings-form').on('submit', function(e) {
        e.preventDefault();
        
        var $form = $(this);
        var $submitButton = $form.find('input[type="submit"]');
        
        $submitButton.prop('disabled', true);
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: $form.serialize() + '&action=save_plugin_settings',
            success: function(response) {
                if (response.success) {
                    // Show success message
                    $('.notice').remove();
                    $form.before('<div class="notice notice-success"><p>Settings saved successfully!</p></div>');
                }
            },
            complete: function() {
                $submitButton.prop('disabled', false);
            }
        });
    });

    // Restore active tab on page load
    if (typeof localStorage !== 'undefined') {
        var activeTab = localStorage.getItem('activeSettingsTab');
        if (activeTab) {
            $('.nav-tab[data-tab="' + activeTab + '"]').trigger('click');
        }
    }
});
