<?php // $Id$
require_once ('moodleform_mod.php');

class label_mod_form extends moodleform_mod {

	function definition() {

		$mform    =& $this->_form;
		$renderer =& $mform->defaultRenderer();

		$mform->addElement('htmleditor', 'content', get_string('labeltext', 'label'));
		$mform->setType('content', PARAM_RAW);
		$mform->addRule('content', get_string('required'), 'required', null, 'client');

		$this->standard_hidden_coursemodule_elements();

        $mform->addElement('modvisible', 'visible', get_string('visible'));

        $buttonarray=array();
        $buttonarray[] = &MoodleQuickForm::createElement('submit', 'submit', get_string('savechanges'));
        $buttonarray[] = &MoodleQuickForm::createElement('submit', 'cancel', get_string('cancel'));
        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
		$renderer->addStopFieldsetElements('buttonar');
	}

}
?>