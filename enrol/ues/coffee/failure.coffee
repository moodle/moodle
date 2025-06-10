M.ues = {}

M.ues.failures = (Y) ->
    grab = (buttonName) -> Y.one "input[name='" + buttonName + "']"

    buttonCheck = ->
        selected = []
        Y.all(".ids").each (node, i, nl) ->
            if (node.get 'checked')
                selected.push node

        disabled = selected.length == 0

        grab('reprocess').set 'disabled', disabled
        grab('delete').set 'disabled', disabled

    Y.all(".ids").on 'change', buttonCheck

    Y.one('input[name=select_all]').on 'change', ->
        selected = this.get 'checked'

        Y.all(".ids").set 'checked', selected
        buttonCheck()
