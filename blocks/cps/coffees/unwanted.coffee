$(document).ready ->
    make_selected = (courseid, checked) ->
        ->
            $("input[id*='course"+ courseid + "_']").attr 'checked', checked
            false

    apply_event = (checked) ->
        (index, elem) ->
            id = $(elem).attr('id').split('_')[1]
            $(elem).click make_selected id, checked

    $("a[id^='all_']").each apply_event true

    $("a[id^='none_']").each apply_event false
