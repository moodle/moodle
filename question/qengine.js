// This script, and the YUI libraries that it needs, are inluded by
// the $PAGE->requires->js calls in get_html_head_contributions in lib/questionlib.php.

question_flag_changer = {
    flag_state_listeners: new Object(),

    init_flag: function(checkboxid, postdata) {
        // Create a hidden input - you can't just repurpose the old checkbox, IE
        // does not cope - and put it in place of the checkbox.
        var checkbox = document.getElementById(checkboxid);
        var input = document.createElement('input');
        input.type = 'hidden';
        checkbox.parentNode.appendChild(input);
        checkbox.parentNode.removeChild(checkbox);
        input.id = checkbox.id;
        input.name = checkbox.name;
        input.value = checkbox.checked ? 1 : 0;
        input.ajaxpostdata = postdata;

        // Create an image input to replace the img tag.
        var image = document.createElement('input');
        image.type = 'image';
        image.statestore = input;
        question_flag_changer.update_image(image);
        input.parentNode.appendChild(image);

        // Remove the label.
        var label = document.getElementById(checkboxid + 'label');
        label.parentNode.removeChild(label);

        // Add the event handler.
        YAHOO.util.Event.addListener(image, 'click', this.flag_state_change);
    },

    init_flag_save_form: function(submitbuttonid) {
        // With JS on, we just want to remove all visible traces of the form.
        var button = document.getElementById(submitbuttonid);
        button.parentNode.removeChild(button);
    },

    flag_state_change: function(e) {
        var image = e.target ? e.target : e.srcElement;
        var input = image.statestore;
        input.value = 1 - input.value;
        question_flag_changer.update_image(image);
        var postdata = input.ajaxpostdata
        if (input.value == 1) {
            postdata += '&newstate=1'
        } else {
            postdata += '&newstate=0'
        }
        YAHOO.util.Event.preventDefault(e);
        question_flag_changer.fire_state_changed(input);
        YAHOO.util.Connect.asyncRequest('POST', qengine_config.actionurl, null, postdata);
    },

    update_image: function(image) {
        if (image.statestore.value == 1) {
            image.src = qengine_config.flagicon;
            image.alt = qengine_config.flaggedalt;
            image.title = qengine_config.unflagtooltip;
        } else {
            image.src = qengine_config.unflagicon;
            image.alt = qengine_config.unflaggedalt;
            image.title = qengine_config.flagtooltip;
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
        var newstate = input.value == 1;
        for (var i = 0; i < question_flag_changer.flag_state_listeners[key].length; i++) {
            question_flag_changer.flag_state_listeners[key][i].flag_state_changed(newstate);
        }
    }
};
