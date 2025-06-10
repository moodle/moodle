$(document).ready ->

    params = {}

    $('.cps_loading').ajaxError ->
        $('.cps_loading').html($('.network_failure').html())

    $('.passed').each (i, elem) ->
        params[$(elem).attr 'name'] = $(elem).val()

    $.post window.location.pathname, params, (html) ->
        $('.cps_loading').html(html)

