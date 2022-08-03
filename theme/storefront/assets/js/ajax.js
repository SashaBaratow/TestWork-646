jQuery( document ).ready(function() {
    jQuery("form#add_product").submit(function(e) {
        e.preventDefault();
        jQuery.ajax({
            type: "post",
            url: jQuery(this).attr("action"),
            data: jQuery(this).serialize(),
            success: function(response) {
                jQuery("#result").html(response);
            }
        });
        jQuery("input[type=text], input[type=number]").val("");
        // document.location.reload();
    });

});