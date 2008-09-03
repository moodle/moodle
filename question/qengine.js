// This script, and the YUI libraries that it needs, are inluded by
// the require_js calls in get_html_head_contributions in lib/questionlib.php.

question_flag_changer = {
    flag_state_listeners: new Object(),

    init_flag: function(checkboxid, postdata) {
        var checkbox = document.getElementById(checkboxid);
        checkbox.ajaxpostdata = postdata;
        checkbox.className += ' jsworking';
        question_flag_changer.update_image(checkbox);
        YAHOO.util.Event.addListener(checkbox, 'change', this.checkbox_state_change);
    },

    checkbox_state_change: function(e) {
        var checkbox = e.target ? e.target : e.srcElement;
        question_flag_changer.update_image(checkbox);
        var postdata = checkbox.ajaxpostdata
        if (checkbox.checked) {
            postdata += '&newstate=1'
        } else {
            postdata += '&newstate=0'
        }
        YAHOO.util.Connect.asyncRequest('POST', qengine_config.wwwroot + '/question/toggleflag.php', null, postdata);
        question_flag_changer.fire_state_changed(checkbox);
    },

    update_image: function(checkbox) {
        var img = document.getElementById(checkbox.id + 'img');
        if (checkbox.checked) {
            img.src = qengine_config.pixpath + '/i/flagged.png';
            img.alt = qengine_config.flaggedalt;
            img.title = qengine_config.unflagtooltip;
        } else {
            img.src = qengine_config.pixpath + '/i/unflagged.png';
            img.alt = qengine_config.unflaggedalt;
            img.title = qengine_config.flagtooltip;
        }
    },

    add_flag_state_listener: function(questionid, listener) {
        var key = 'q' + questionid;
        if (!question_flag_changer.flag_state_listeners.hasOwnProperty(key)) {
            question_flag_changer.flag_state_listeners[key] = [];
        }
        question_flag_changer.flag_state_listeners[key].push(listener);
    },

    questionid_from_cbid: function(cbid) {
        return cbid.replace(/resp(\d+)__flagged/, '$1');
    },

    fire_state_changed: function(checkbox) {
        var questionid = question_flag_changer.questionid_from_cbid(checkbox.id);
        var key = 'q' + questionid;
        if (!question_flag_changer.flag_state_listeners.hasOwnProperty(key)) {
            return;
        }
        var newstate = checkbox.checked;
        for (var i = 0; i < question_flag_changer.flag_state_listeners[key].length; i++) {
            question_flag_changer.flag_state_listeners[key][i].flag_state_changed(newstate);
        }
    }
};
