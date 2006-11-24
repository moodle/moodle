<?php
require_once ('moodleform_mod.php');

class chat_mod_form extends moodleform_mod {

	function definition() {

		global $CFG;
		$mform    =& $this->_form;
		$renderer =& $mform->defaultRenderer();

//-------------------------------------------------------------------------------
        $mform->addElement('header', 'general', get_string('general', 'form'));

        $mform->addElement('text', 'name', get_string('chatname', 'chat'));
		$mform->setType('name', PARAM_TEXT);
		$mform->addRule('name', null, 'required', null, 'client');

		$mform->addElement('htmleditor', 'intro', get_string('chatintro', 'chat'));
		$mform->setType('intro', PARAM_RAW);
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

        $options=array();
        $options[0]    = get_string('no');
        $options[1]    = get_string('yes');
        $mform->addElement('select', 'studentlogs', get_string('studentseereports', 'chat'), $options);

        $this->standard_coursemodule_elements();

        $buttonarray=array();
        $buttonarray[] = &MoodleQuickForm::createElement('submit', 'submit', get_string('savechanges'));
        $buttonarray[] = &MoodleQuickForm::createElement('submit', 'cancel', get_string('cancel'));
        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
		$renderer->addStopFieldsetElements('buttonar');
	}



}
?>