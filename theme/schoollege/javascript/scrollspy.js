$(document).ready(function () {

    $('.edit-btn').click(function () {

        window.sessionStorage.setItem('edit_toggled', true);

        var viewport_top = $(window).scrollTop();
        var closest = null;
        var closest_offset = null;

        $('.section.main').each(function (e, f) {
            var this_offset = $(f).offset().top;

            if ($(closest).offset()) {
                closest_offset = $(closest).offset().top;
            }
            if (closest == null || Math.abs(this_offset - viewport_top) < Math.abs(closest_offset - viewport_top)) {
                closest = f;
            }
        });

        window.sessionStorage.setItem('closest_id', closest.id);
        window.sessionStorage.setItem('closest_delta', viewport_top - $(closest).offset().top);

    });

    var edit_toggled = window.sessionStorage.getItem('edit_toggled');

    if (edit_toggled) {

        var closest_id = window.sessionStorage.getItem('closest_id');
        var closest_delta = window.sessionStorage.getItem('closest_delta');

        if (closest_id && closest_delta) {
            var closest = $('#' + closest_id);
            $(window).scrollTop(closest.offset().top + parseInt(closest_delta));
        }

        window.sessionStorage.removeItem('edit_toggled');
        window.sessionStorage.removeItem('closest_id');
        window.sessionStorage.removeItem('closest_delta');

    }

});
