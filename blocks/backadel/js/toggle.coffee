$(document).ready ->
    $('.toggle_link').click ->
        which = !$(':checkbox').prop('checked')

        $(':checkbox').each -> $(this).prop('checked', which)
