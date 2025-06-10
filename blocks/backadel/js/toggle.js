(function() {
    $(document).ready(function() {
        return $('.toggle_link').click(function() {
            var which;
            which = !$(':checkbox').prop('checked');
            return $(':checkbox').each(function() {
                return $(this).prop('checked', which);
            });
        });
    });
}).call(this);
