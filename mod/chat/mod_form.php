<?php
require_once ($CFG->dirroot.'/course/moodleform_mod.php');

class mod_chat_mod_form extends moodleform_mod {

    function definition() {

        global $CFG;
        $mform    =& $this->_form;

//-------------------------------------------------------------------------------
        $mform->addElement('header', 'general', get_string('general', 'form'));

        $mform->addElement('text', 'name', get_string('chatname', 'chat'), array('size'=>'64'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEAN);
        }
        $mform->addRule('name', null, 'required', null, 'client');

        $mform->addElement('htmleditor', 'intro', get_string('chatintro', 'chat'));
        $mform->setType('intro', PARAM_RAW);
        $mform->setHelpButton('intro', array('writing', 'questions', 'richtext'), false, 'editorhelpbutton');
        $mform->addRule('intro', get_string('required'), 'required', null, 'client');

        $mform->addElement('date_time_selector', 'chattime', get_string('chattime', 'chat'));

        $options=array();
        $options[0]  = get_string('donotusechattime', 'chat');
        $options[1]  = get_string('repeatnone', 'chat');
        $options[2]  = get_string('repeatdaily', 'chat');
        $options[3]  = get_string('repeatweekly', 'chat');
        $mform->addElement('select', 'schedule', get_string('repeattimes', 'chat'), $options);


        $options=array();
        $options[0]    = get_string('neverdeletemessages', 'chat');
        $options[365]  = get_string('numdays', '', 365);
        $options[180]  = get_string('numdays', '', 180);
        $options[150]  = get_string('numdays', '', 150);
        $options[120]  = get_string('numdays', '', 120);
        $options[90]   = get_string('numdays', '', 90);
        $options[60]   = get_string('numdays', '', 60);
        $options[30]   = get_string('numdays', '', 30);
        $options[21]   = get_string('numdays', '', 21);
        $options[14]   = get_string('numdays', '', 14);
        $options[7]    = get_string('numdays', '', 7);
        $options[2]    = get_string('numdays', '', 2);
        $mform->addElement('select', 'keepdays', get_string('savemessages', 'chat'), $options);

        $mform->addElement('selectyesno', 'studentlogs', get_string('studentseereports', 'chat'));

        $features = new stdClass;
        $features->groups = true;
        $features->groupings = true;
        $features->groupmembersonly = true;
        $this->standard_coursemodule_elements($features);

        $this->add_action_buttons();
    }



}
?>
