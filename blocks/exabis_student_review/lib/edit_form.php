<?php

require_once($CFG->dirroot.'/lib/formslib.php');

class class_edit_form extends moodleform {
	function definition() {
		global $CFG, $USER;
		$mform =& $this->_form;
		
        $mform->addElement('text', 'class', get_string('class','block_exabis_student_review').':', array('size' => 50));
        $mform->setType('class', PARAM_TEXT);
        $mform->addRule('class', null, 'required', null, 'client');
		
		$mform->addElement('hidden', 'id');
		$mform->setType('id', PARAM_INT);
		$mform->setDefault('id', 0);
		
        $this->add_action_buttons(false);
	}
	
	function validation($data) {
        return true;
	}
}

class period_edit_form extends moodleform {
	function definition() {
		global $CFG, $USER;
		$mform =& $this->_form;
		
        $mform->addElement('text', 'description', 'Beschreibung der Periode:', array('size' => 50));
        $mform->setType('description', PARAM_TEXT);
        $mform->addRule('description', null, 'required', null, 'client');
        
        $timeoptions = array(
						        'language'         => 'en',
						        'format'           => 'd. M. Y - H:i',
						        'minYear'          => 2001,
						        'maxYear'          => 2010,
						        'addEmptyOption'   => false,
						        'emptyOptionValue' => '',
						        'emptyOptionText'  => '&nbsp;',
						        'optionIncrement'  => array('i' => 1, 's' => 1),
								'optional'		   => false,
						    );
        						    
        $mform->addElement('date_time_selector', 'starttime', 'Startdatum:', $timeoptions);
        $mform->setType('starttime', PARAM_INT);
        $mform->addRule('starttime', null, 'required', null, 'client');
        
        $mform->addElement('date_time_selector', 'endtime', 'Enddatum:', $timeoptions);
        $mform->setType('endtime', PARAM_INT);
        $mform->addRule('endtime', null, 'required', null, 'client');
        
		$mform->addElement('hidden', 'id');
		$mform->setType('id', PARAM_INT);
		$mform->setDefault('id', 0);
		
		$mform->addElement('hidden', 'action');
		$mform->setType('action', PARAM_TEXT);
		$mform->setDefault('action', 0);
		
        $this->add_action_buttons(false);
	}
}

class student_edit_form extends moodleform {
	function definition() {
		global $CFG, $USER;
		$mform =& $this->_form;
		
		$mform->addElement('hidden', 'courseid');
		$mform->setType('courseid', PARAM_INT);
		$mform->setDefault('courseid', 0);
		
		$mform->addElement('hidden', 'classid');
		$mform->setType('classid', PARAM_INT);
		$mform->setDefault('classid', 0);
		
		$mform->addElement('hidden', 'studentid');
		$mform->setType('studentid', PARAM_INT);
		$mform->setDefault('studentid', 0);
		
		//$mform->addElement('hidden', 'teacher_id');
		//$mform->setType('teacher_id', PARAM_INT);
		//$mform->setDefault('teacher_id', 0);
		
		//$mform->addElement('hidden', 'period_id');
		//$mform->setType('period_id', PARAM_INT);
		//$mform->setDefault('period_id', 0);
		
		//$mform->addElement('hidden', 'student_id');
		//$mform->setType('student_id', PARAM_INT);
		//$mform->setDefault('student_id', 0);
		
		//$mform->addElement('hidden', 'action');
		//$mform->setType('action', PARAM_TEXT);
		//$mform->setDefault('action', 0);
		
		
        $selectoptions = array(
						        1  =>  get_string('evaluation1', 'block_exabis_student_review'),
						        2  =>  get_string('evaluation2', 'block_exabis_student_review'),
						        3  =>  get_string('evaluation3', 'block_exabis_student_review'),
						        4  =>  get_string('evaluation4', 'block_exabis_student_review'),
						        5  =>  get_string('evaluation5', 'block_exabis_student_review'),
						        6  =>  get_string('evaluation6', 'block_exabis_student_review'),
						        7  =>  get_string('evaluation7', 'block_exabis_student_review'),
						        8  =>  get_string('evaluation8', 'block_exabis_student_review'),
						        9  =>  get_string('evaluation9', 'block_exabis_student_review'),
						        10 =>  get_string('evaluation10', 'block_exabis_student_review'),
						    );
		
		
		
						    
		$mform->addElement('select', 'team', get_string('teamplayer', 'block_exabis_student_review'), $selectoptions);
		$mform->setType('team', PARAM_INT);
		$mform->setDefault('team', 1);
		
		$mform->addElement('select', 'resp', get_string('responsibility', 'block_exabis_student_review'), $selectoptions);
		$mform->setType('resp', PARAM_INT);
		$mform->setDefault('resp', 1);
		
		$mform->addElement('select', 'inde', get_string('selfreliance', 'block_exabis_student_review'), $selectoptions);
		$mform->setType('inde', PARAM_INT);
		$mform->setDefault('inde', 1);
		
		$mform->addElement('htmleditor', 'review', get_string('review', 'block_exabis_student_review'), array('cols'=>50, 'rows'=>30));
		$mform->setType('review', PARAM_RAW);
		//$mform->addRule('review', get_string('required'), 'required', null, 'client');
        $mform->setHelpButton('review', array('reading', 'writing', 'richtext'), false, 'editorhelpbutton');
        
        $this->add_action_buttons(false);
	}
}
?>