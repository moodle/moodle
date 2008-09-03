// This script, and the YUI libraries that it needs, are inluded by
// the require_js calls in get_html_head_contributions in lib/questionlib.php.

question_flag_changer = {
    flag_state_listeners: new Object(),

    init_flag: function(checkboxid, postdata) {
        // Convert the checkbox to an image input.
        var input = document.getElementById(checkboxid);
        var state = input.checked;
        input.ajaxpostdata = postdata;
        input.flaggedstate = state;
        input.type = 'image';

        // Set up the correct image, alt and title.
        question_flag_changer.update_image(input);

        // Remove the label element.
        var label = document.getElementById(checkboxid + 'label');
        label.parentNode.removeChild(label);

        // Add the event handler.
        YAHOO.util.Event.addListener(input, 'click', this.flag_state_change);
    },

    flag_state_change: function(e) {
        var input = e.target ? e.target : e.srcElement;
        input.flaggedstate = !input.flaggedstate;
        question_flag_changer.update_image(input);
        var postdata = input.ajaxpostdata
        if (input.flaggedstate) {
            postdata += '&newstate=1'
        } else {
            postdata += '&newstate=0'
        }
        YAHOO.util.Connect.asyncRequest('POST', qengine_config.wwwroot + '/question/toggleflag.php', null, postdata);
        question_flag_changer.fire_state_changed(input);
        YAHOO.util.Event.preventDefault(e);
    },

    update_image: function(input) {
        if (input.flaggedstate) {
            input.src = qengine_config.pixpath + '/i/flagged.png';
            input.alt = qengine_config.flaggedalt;
            input.title = qengine_config.unflagtooltip;
        } else {
            input.src = qengine_config.pixpath + '/i/unflagged.png';
            input.alt = qengine_config.unflaggedalt;
            input.title = qengine_config.flagtooltip;
        }
    },

    add_flag_state_listener: function(questionid, listener) {
        var key = 'q' + questionid;
        if (!question_flag_changer.flag_state_listeners.hasOwnProperty(key)) {
            question_flag_changer.flag_state_listeners[key] = [];
        }
        question_flag_changer.flag_state_listeners[key].push(listener);
    },

    questionid_from_inputid: function(inputid) {
        return inputid.replace(/resp(\d+)__flagged/, '$1');
    },

    fire_state_changed: function(input) {
        var questionid = question_flag_changer.questionid_from_inputid(input.id);
        var key = 'q' + questionid;
        if (!question_flag_changer.flag_state_listeners.hasOwnProperty(key)) {
            return;
        }
        var newstate = input.flaggedstate;
        for (var i = 0; i < question_flag_changer.flag_state_listeners[key].length; i++) {
            question_flag_changer.flag_state_listeners[key][i].flag_state_changed(newstate);
        }
    }
};
