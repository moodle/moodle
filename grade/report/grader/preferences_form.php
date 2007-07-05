<?php  //$Id$

require_once($CFG->libdir.'/formslib.php');

/**
 * First implementation of the preferences in the form of a moodleform.
 * TODO add submit button
 * TODO add "reset to site defaults" button
 * TODO show site defaults near each setting
 */
class grader_report_preferences_form extends moodleform {

    function definition() {
        global $USER, $CFG;

        $mform    =& $this->_form;
        $course   = $this->_customdata['course'];

        $systemcontext = get_context_instance(CONTEXT_SYSTEM);

/// form definition with preferences defaults
//--------------------------------------------------------------------------------
        $prefs = new stdClass();
        $prefs->aggregationposition = get_user_preferences('grade_report_aggregationposition',  $CFG->grade_report_aggregationposition);
        $prefs->aggregationview     = get_user_preferences('grade_report_aggregationview',      $CFG->grade_report_aggregationview);
        $prefs->bulkcheckboxes      = get_user_preferences('grade_report_bulkcheckboxes',       $CFG->grade_report_bulkcheckboxes);
        $prefs->enableajax          = get_user_preferences('grade_report_enableajax',           $CFG->grade_report_enableajax);
        $prefs->gradedisplaytype    = get_user_preferences('grade_report_gradedisplaytype',     $CFG->grade_report_gradedisplaytype);
        $prefs->showeyecons         = get_user_preferences('grade_report_showeyecons',          $CFG->grade_report_showeyecons);
        $prefs->showgroups          = get_user_preferences('grade_report_showgroups',           $CFG->grade_report_showgroups);
        $prefs->showlocks           = get_user_preferences('grade_report_showlocks',            $CFG->grade_report_showlocks);
        $prefs->shownotes           = get_user_preferences('grade_report_shownotes',            $CFG->grade_report_shownotes);
        $prefs->showscales          = get_user_preferences('grade_report_showscales',           $CFG->grade_report_showscales);
        $prefs->studentsperpage     = get_user_preferences('grade_report_studentsperpage',      $CFG->grade_report_studentsperpage);
        $prefs->feedbackformat      = get_user_preferences('grade_report_feedbackformat',       $CFG->grade_report_feedbackformat);
        $prefs->decimalpoints       = get_user_preferences('grade_report_decimalpoints',        $CFG->grade_report_decimalpoints);

        $mform->addElement('text','grade_report_studentsperpage', get_string('studentsperpage', 'grades'));
        $mform->setHelpButton('grade_report_studentsperpage', array(false, get_string('studentsperpage', 'grades'), false, true,
                                                           false, get_string('configstudentsperpage', 'grades')));
        $mform->setDefault('grade_report_studentsperpage', $prefs->studentsperpage);
        $mform->setType('grade_report_studentsperpage', PARAM_INT);

        $mform->addElement('select','grade_report_aggregationposition', get_string('aggregationposition', 'grades'),
                array(get_string('left', 'grades'), get_string('right', 'grades')));
        $mform->setHelpButton('grade_report_aggregationposition', array(false, get_string('aggregationposition', 'grades'), false, true,
                                                           false, get_string('configaggregationposition', 'grades')));
        $mform->setDefault('grade_report_aggregationposition', $prefs->aggregationposition);
        $mform->setType('grade_report_aggregationposition', PARAM_INT);

        $mform->addElement('select','grade_report_aggregationview', get_string('aggregationview', 'grades'),
                array(get_string('full', 'grades'), get_string('compact', 'grades')));
        $mform->setHelpButton('grade_report_aggregationview', array(false, get_string('aggregationview', 'grades'), false, true,
                                                           false, get_string('configaggregationview', 'grades')));
        $mform->setDefault('grade_report_aggregationview', $prefs->aggregationview);
        $mform->setType('grade_report_aggregationview', PARAM_INT);

        $mform->addElement('select','grade_report_gradedisplaytype', get_string('gradedisplaytype', 'grades'),
                array(get_string('raw', 'grades'), get_string('percentage', 'grades')));
        $mform->setHelpButton('grade_report_gradedisplaytype', array(false, get_string('gradedisplaytype', 'grades'), false, true,
                                                           false, get_string('configgradedisplaytype', 'grades')));
        $mform->setDefault('grade_report_gradedisplaytype', $prefs->gradedisplaytype);
        $mform->setType('grade_report_gradedisplaytype', PARAM_INT);

        $mform->addElement('select','grade_report_feedbackformat', get_string('feedbackformat', 'grades'),
                array(get_string('text', 'grades'), get_string('html', 'grades')));
        $mform->setHelpButton('grade_report_feedbackformat', array(false, get_string('feedbackformat', 'grades'), false, true,
                                                           false, get_string('configfeedbackformat', 'grades')));
        $mform->setDefault('grade_report_feedbackformat', $prefs->feedbackformat);
        $mform->setType('grade_report_feedbackformat', PARAM_INT);

        $mform->addElement('select','grade_report_decimalpoints', get_string('decimalpoints', 'grades'),
                array(0, 1, 2, 3, 4, 5));
        $mform->setHelpButton('grade_report_decimalpoints', array(false, get_string('decimalpoints', 'grades'), false, true,
                                                           false, get_string('configdecimalpoints', 'grades')));
        $mform->setDefault('grade_report_decimalpoints', $prefs->decimalpoints);
        $mform->setType('grade_report_decimalpoints', PARAM_INT);

        $mform->addElement('checkbox', 'grade_report_bulkcheckboxes', get_string('bulkcheckboxes', 'grades'));
        $mform->setHelpButton('grade_report_bulkcheckboxes', array(false, get_string('bulkcheckboxes', 'grades'), false, true,
                                                           false, get_string('configbulkcheckboxes', 'grades')));
        $mform->setDefault('grade_report_bulkcheckboxes', $prefs->bulkcheckboxes);
        $mform->setType('grade_report_bulkcheckboxes', PARAM_INT);

        $mform->addElement('checkbox', 'grade_report_enableajax', get_string('enableajax', 'grades'));
        $mform->setHelpButton('grade_report_enableajax', array(false, get_string('enableajax', 'grades'), false, true,
                                                           false, get_string('configenableajax', 'grades')));
        $mform->setDefault('grade_report_enableajax', $prefs->enableajax);
        $mform->setType('grade_report_enableajax', PARAM_INT);

        $mform->addElement('checkbox', 'grade_report_showeyecons', get_string('showeyecons', 'grades'));
        $mform->setHelpButton('grade_report_showeyecons', array(false, get_string('showeyecons', 'grades'), false, true,
                                                           false, get_string('configshoweyecons', 'grades')));
        $mform->setDefault('grade_report_showeyecons', $prefs->showeyecons);
        $mform->setType('grade_report_showeyecons', PARAM_INT);

        $mform->addElement('checkbox', 'grade_report_showgroups', get_string('showgroups', 'grades'));
        $mform->setHelpButton('grade_report_showgroups', array(false, get_string('showgroups', 'grades'), false, true,
                                                           false, get_string('configshowgroups', 'grades')));
        $mform->setDefault('grade_report_showgroups', $prefs->showgroups);
        $mform->setType('grade_report_showgroups', PARAM_INT);

        $mform->addElement('checkbox', 'grade_report_showlocks', get_string('showlocks', 'grades'));
        $mform->setHelpButton('grade_report_showlocks', array(false, get_string('showlocks', 'grades'), false, true,
                                                           false, get_string('configshowlocks', 'grades')));
        $mform->setDefault('grade_report_showlocks', $prefs->showlocks);
        $mform->setType('grade_report_showlocks', PARAM_INT);

        $mform->addElement('checkbox', 'grade_report_shownotes', get_string('shownotes', 'grades'));
        $mform->setHelpButton('grade_report_shownotes', array(false, get_string('shownotes', 'grades'), false, true,
                                                           false, get_string('configshownotes', 'grades')));
        $mform->setDefault('grade_report_shownotes', $prefs->shownotes);
        $mform->setType('grade_report_shownotes', PARAM_INT);

        $mform->addElement('checkbox', 'grade_report_showscales', get_string('showscales', 'grades'));
        $mform->setHelpButton('grade_report_showscales', array(false, get_string('showscales', 'grades'), false, true,
                                                           false, get_string('configshowscales', 'grades')));
        $mform->setDefault('grade_report_showscales', $prefs->showscales);
        $mform->setType('grade_report_showscales', PARAM_INT);

        $mform->addElement('hidden', 'id');
        $mform->setDefault('id', $course->id);

        $this->add_action_buttons();
    }


/// perform some extra moodle validation
    function validation($data){
        $errors= array();
        if (0 == count($errors)){
            return true;
        } else {
            return $errors;
        }
    }
}
?>
