$(function() {
    $(document).click(function(e) {
        if ($(e.target).hasClass("btn-delete-notification")) {
            if ( ! confirm('Delete this notification?')) {
                e.preventDefault();
            }
        }
    });
});