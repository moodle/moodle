// This script, and the YUI libraries that it needs, are inluded by
// the $PAGE->requires->js calls in question_get_html_head_contributions in lib/questionlib.php.

M.core_question_flags = {
    flagattributes: null,
    actionurl: null,
    listeners: [],

    init: function(Y, actionurl, flagattributes) {
        M.core_question_flags.flagattributes = flagattributes;
        M.core_question_flags.actionurl = actionurl;

        Y.all('div.questionflag').each(function(flagdiv, i) {
            var checkbox = flagdiv.one('input.questionflagcheckbox');
            if (!checkbox) {
                return;
            }

            var input = Y.Node.create('<input type="hidden" />');
            input.set('id', checkbox.get('id'));
            input.set('name', checkbox.get('name'));
            input.set('value', checkbox.get('checked') ? 1 : 0);

            // Create an image input to replace the img tag.
            var image = Y.Node.create('<input type="image" class="questionflagimage" />');
            M.core_question_flags.update_flag(input, image);

            checkbox.remove();
            flagdiv.one('label').remove();
            flagdiv.append(input);
            flagdiv.append(image);
        });

        Y.delegate('click', function(e) {
            var input = this.previous('input');
            input.set('value', 1 - input.get('value'));
            M.core_question_flags.update_flag(input, this);
            var postdata = this.previous('input.questionflagpostdata').get('value') +
                    input.get('value')

            e.halt();
            Y.io(M.core_question_flags.actionurl , {method: 'POST', 'data': postdata});
            M.core_question_flags.fire_listeners(postdata);
        }, document.body, 'input.questionflagimage');

    },

    update_flag: function(input, image) {
        image.setAttrs(M.core_question_flags.flagattributes[input.get('value')]);
    },

    add_listener: function(listener) {
        M.core_question_flags.listeners.push(listener);
    },

    fire_listeners: function(postdata) {
        for (var i = 0; i < M.core_question_flags.listeners.length; i++) {
            M.core_question_flags.listeners[i](
                postdata.match(/\baid=(\d+)\b/)[1],
                postdata.match(/\bqid=(\d+)\b/)[1],
                postdata.match(/\bnewstate=(\d+)\b/)[1]
            );
        }
    }
};
