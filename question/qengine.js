// This script, and the YUI libraries that it needs, are inluded by
// the require_js calls in get_html_head_contributions in lib/questionlib.php.

question_flag_changer = {
    init_flag: function(checkboxid, postdata) {
        var checkbox = document.getElementById(checkboxid);
        checkbox.ajaxpostdata = postdata;
        checkbox.className += ' jsworking';
        question_flag_changer.update_image(checkbox);
        YAHOO.util.Event.addListener(checkbox, 'change', this.checkbox_state_change);
        YAHOO.util.Event.addListener(checkbox, 'focus', 'blur()');
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
    }
};
