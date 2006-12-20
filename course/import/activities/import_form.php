<?php  // $Id$

require_once($CFG->libdir.'/formslib.php');

class activities_import_form_1 extends moodleform {

	function definition() {

		global $CFG;
		$mform    =& $this->_form;
		$text = $this->_customdata['text'];
		$options = $this->_customdata['options'];
        $courseid = $this->_customdata['courseid'];
        $mform->addElement('header', 'general', '');//fill in the data depending on page params
                                                    //later using set_defaults
		$mform->addElement('select', 'fromcourse', $text, $options);
         
        // buttons
        $submit_string = get_string('usethiscourse');
        $this->add_action_buttons(false, true, $submit_string);
        
        $mform->addElement('hidden', 'id');
		$mform->setType('id', PARAM_INT);
		$mform->setConstants(array('id'=> $courseid));

	}

	function validation($data) {
        return true;
	}

}

class activities_import_form_2 extends moodleform {

	function definition() {

		global $CFG;
		$mform    =& $this->_form;
		$courseid = $this->_customdata['courseid'];        

        $mform->addElement('header', 'general', '');//fill in the data depending on page params
                                                    //later using set_defaults
		$mform->addElement('text', 'fromcoursesearch', get_string('searchcourses'));
         
        // buttons
        $this->add_action_buttons(false, true, get_string('searchcourses'));
        
        $mform->addElement('hidden', 'id');
		$mform->setType('id', PARAM_INT);
		$mform->setConstants(array('id'=> $courseid));

	}

	function validation($data) {
        return true;
	}

}
?>
