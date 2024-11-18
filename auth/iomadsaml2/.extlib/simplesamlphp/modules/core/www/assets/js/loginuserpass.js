$(document).ready(function () {
    $('#submit_button').on('click', function () {
        $(this).attr('disabled', 'disabled');
        $(this).html($(this).data('processing'));
        $(this).parents('form').submit();
    });
});
