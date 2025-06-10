$(document).ready () ->

    available = $("select[name^='before']")

    $("#id_save").click () ->
        if available and $(available).children().length > 0
            $("#split_error").text "You must split all sections."
            false
        else if available
            value = true
            $("select[name^='shell_']").each (index, select) ->
                value = value and $(select).children().length >= 1

            if not value then $("#split_error").text "Each shell must have at least one section."
            value
        else
            true
