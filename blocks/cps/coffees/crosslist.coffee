$(document).ready () ->

    $("#id_save").click () ->
        value = true
        validated = []
        $("input[name^='shell_name_']").each (index, name) ->
            if $(name).attr 'type' is 'hidden'
                $(validated).each (i, n) ->
                    if n is $(name).val() then value = false
            validated.push $(name).val()

        if not value then $("#split_error").text "Each shell should have a unique name."
        value
