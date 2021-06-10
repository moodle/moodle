$(document).ready(function () {

    $('[data-tooltip="tooltip"]').on('mouseleave', function () {
        $(this).tooltip('hide');
    });

});
