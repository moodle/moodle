M.block_ues_reprocess = {}

M.block_ues_reprocess.init = (Y) ->
    pull = (value) -> Y.one('input[name=' + value + ']').get 'value'

    sections = ->
        ret = []
        Y.all('input').each (node) ->
            name = node.get 'name'
            if name.match /^section_/
                ret.push node
        ret

    courses = ->
        ret = []
        Y.all('input').each (node) ->
            name = node.get 'name'
            if name.match /^course_/
                ret.push node
        ret

    Y.one('form[method=POST]').on 'submit', (e) ->
        e.preventDefault();

        params = {
            type: pull 'type',
            id: pull 'id'
        }

        set = (section) ->
            name = section.get 'name'
            params[name] = pull name

        set elem for elem in courses()
        set elem for elem in sections()

        Y.one('.buttons').getDOMNode().innerHTML = Y.one('#loading').getDOMNode().innerHTML

        Y.io 'rpc.php', {
            method: 'POST',
            data: params,
            "on": {
                success: (id, result) ->
                    Y.one('#notice').getDOMNode().innerHTML = result.responseText
            }
        }
        false
