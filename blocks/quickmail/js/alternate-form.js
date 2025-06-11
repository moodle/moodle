$(function() {
    $(document).click(function(e) {
        if ($(e.target).hasClass("btn-delete-alt")) {
            if ( ! confirm('Delete this alternate email?')) {
                e.preventDefault();
            }
        } else if ($(e.target).hasClass("btn-resend-alt")) {
            if ( ! confirm('Duplicate this alternate email?')) {
                e.preventDefault();
            }
        }
    });
});