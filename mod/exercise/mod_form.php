<?php
require_once ($CFG->dirroot.'/course/moodleform_mod.php');

class mod_exercise_mod_form extends moodleform_mod {

	function definition() {

		global $CFG, $COURSE, $EXERCISE_TYPE, $EXERCISE_ASSESSMENT_COMPS;
		$mform    =& $this->_form;

//-------------------------------------------------------------------------------
        $mform->addElement('header', 'general', get_string('general', 'form'));

        $mform->addElement('text', 'name', get_string('title', 'exercise'), array('size'=>'64'));
		$mform->setType('name', PARAM_TEXT);
		$mform->addRule('name', null, 'required', null, 'client');

		$mform->addElement('static', 'description', get_string('description', 'exercise'), get_string('descriptionofexercise', 'exercise', $COURSE->students));

        $filesize = array();
        $sizelist = array('10Kb', '50Kb', '100Kb', '500Kb', '1Mb', '2Mb', '5Mb', '10Mb', '20Mb', '50Mb');
        $maxsize = get_max_upload_file_size();
        foreach ($sizelist as $size) {
            $sizebytes = get_real_size($size);
            if ($sizebytes < $maxsize) {
                $filesize[$sizebytes] = $size;
            }
        }
        $filesize[$maxsize] = display_size($maxsize);
        ksort($filesize, SORT_NUMERIC);
        $mform->addElement('select', 'maxbytes', get_string('maximumsize', 'exercise'), $filesize);
        $mform->setHelpButton('maxbytes', array('comparisonofassessments', get_string('comparisonofassessments', 'exercise'), 'exercise'));
        $mform->setDefault('maxbytes', get_real_size('500K'));

        $mform->addElement('date_time_selector', 'deadline', get_string('deadline', 'exercise'));

        $numbers = array();
        $numbers[22] = 'All';
        $numbers[21] = 50;
        for ($i=20; $i>=0; $i--) {
            $numbers[$i] = $i;
        }
//-------------------------------------------------------------------------------
        $mform->addElement('header', 'leaguetablehdr', get_string('leaguetable', 'exercise'));
        $mform->addElement('select', 'showleaguetable', get_string('numberofentriesinleaguetable', 'exercise'), $numbers);
        $mform->setHelpButton('showleaguetable', array('leaguetable', get_string('numberofentriesinleaguetable', 'exercise'), 'exercise'));

        $mform->addElement('selectyesno', 'anonymous', get_string('hidenamesfromstudents', 'exercise', $COURSE->students), $numbers);
        $mform->setHelpButton('anonymous', array('leaguetablenames', get_string('hidenamesfromstudents', 'exercise', $COURSE->students), 'exercise'));

//-------------------------------------------------------------------------------
        $mform->addElement('header', 'gradeshdr', get_string('grades'));
        $grades = array();
        for ($i=100; $i>=0; $i--) {
            $grades[$i] = $i;
        }
        $mform->addElement('select', 'gradinggrade', get_string('gradeforstudentsassessment', 'exercise', $COURSE->student), $grades);
        $mform->setHelpButton('gradinggrade', array('gradinggrade', get_string('gradinggrade', 'exercise'), 'exercise'));
        $mform->setDefault('gradinggrade', 100);

        $mform->addElement('select', 'grade', get_string('gradeforsubmission', 'exercise'), $grades);
        $mform->setHelpButton('grade', array('grade', get_string('gradeforsubmission', 'exercise'), 'exercise'));
        $mform->setDefault('grade', 100);

        $mform->addElement('select', 'gradingstrategy', get_string('gradingstrategy', 'exercise'), $EXERCISE_TYPE);
        $mform->setHelpButton('gradingstrategy', array('gradingstrategy', get_string('gradingstrategy', 'exercise'), 'exercise'));
        $mform->setDefault('gradingstrategy', 1);

        $options= array(get_string('usemean', 'exercise'), get_string('usemaximum', 'exercise'));
        $mform->addElement('select', 'usemaximum', get_string('handlingofmultiplesubmissions', 'exercise'), $options);
        $mform->setHelpButton('usemaximum', array('usemax', get_string('handlingofmultiplesubmissions', 'exercise'), 'exercise'));
        $mform->setDefault('usemaximum', 0);

        $options= array();
        for ($i=20; $i>=0; $i--) {
            $options[$i] = $i;
        }
        $mform->addElement('select', 'nelements', get_string('numberofassessmentelements', 'exercise'), $options);
        $mform->setHelpButton('nelements', array('nelements', get_string('numberofassessmentelements', 'exercise'), 'exercise'));
        $mform->setDefault('nelements', 1);

        $COMPARISONS=array();
        foreach ($EXERCISE_ASSESSMENT_COMPS as $KEY => $COMPARISON) {
            $COMPARISONS[] = $COMPARISON['name'];
        }
        $mform->addElement('select', 'assessmentcomps', get_string('comparisonofassessments', 'exercise'), $options);
        $mform->setHelpButton('assessmentcomps', array('comparisonofassessments', get_string('comparisonofassessments', 'exercise'), 'exercise'));
        $mform->setDefault('assessmentcomps', 2);

//-------------------------------------------------------------------------------
        $mform->addElement('header', 'passwordhdr', get_string('password'));
        $mform->addElement('selectyesno', 'usepassword', get_string('usepassword', 'exercise'));
        $mform->setHelpButton('usepassword', array('usepassword', get_string('usepassword', 'exercise'), 'exercise'));
        $mform->setDefault('usepassword', 0);

        $mform->addElement('text', 'password', get_string('password'));
        $mform->setHelpButton('password', array('usepassword', get_string('usepassword', 'exercise'), 'exercise'));
        $mform->setType('password', PARAM_RAW);

//-------------------------------------------------------------------------------
        $this->standard_coursemodule_elements();
//-------------------------------------------------------------------------------
        // buttons
        $this->add_action_buttons();
	}



}
?>