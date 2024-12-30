
jQuery(document).ready(function($) {
    // Add tab functionality
    $('.tab').on('click', function() {
        $('.tab').removeClass('active');
        $('.tab-content').removeClass('active');
        
        $(this).addClass('active');
        $($(this).data('tab')).addClass('active');
    });

    // AJAX save functionality
    $('#settings-form').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                alert('Settings saved successfully!');
            }
        });
    });
});
