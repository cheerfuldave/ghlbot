
jQuery(document).ready(function($) {
    // Show first tab by default
    $("#settings").show();
    $(".nav-tab:first").addClass("nav-tab-active");
    
    // Handle tab clicks
    $(".nav-tab").click(function(e) {
        e.preventDefault();
        $(".nav-tab").removeClass("nav-tab-active");
        $(this).addClass("nav-tab-active");
        $(".tab-content").hide();
        $($(this).attr("href")).show();
    });
});
