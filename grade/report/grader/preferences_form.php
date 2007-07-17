<?php  //$Id$

require_once($CFG->libdir.'/formslib.php');

/**
 * First implementation of the preferences in the form of a moodleform.
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
        $preferences = array('bulkcheckboxes'         => 'advcheckbox',
                             'enableajax'             => 'advcheckbox',
                             'showcalculations'       => 'advcheckbox',
                             'showeyecons'            => 'advcheckbox',
                             'showaverages'           => 'advcheckbox',
                             'showgroups'             => 'advcheckbox',
                             'showlocks'              => 'advcheckbox',
                             'showranges'             => 'advcheckbox',
                             'quickgrading'           => 'advcheckbox',
                             'quickfeedback'          => 'advcheckbox',
                             'showuserimage'          => 'advcheckbox',
                             'meanselection'          => array(GRADE_AGGREGATE_MEAN_ALL => get_string('meanall', 'grades'),
                                                               GRADE_AGGREGATE_MEAN_GRADED => get_string('meangraded', 'grades')),
                             'aggregationposition'    => array(GRADE_REPORT_AGGREGATION_POSITION_LEFT => get_string('left', 'grades'),
                                                               GRADE_REPORT_AGGREGATION_POSITION_RIGHT => get_string('right', 'grades')),
                             'aggregationview'        => array(GRADE_REPORT_AGGREGATION_VIEW_FULL => get_string('full', 'grades'),
                                                               GRADE_REPORT_AGGREGATION_VIEW_COMPACT => get_string('compact', 'grades')),
                             'gradedisplaytype'       => array(GRADE_REPORT_GRADE_DISPLAY_TYPE_REAL => get_string('real', 'grades'),
                                                               GRADE_REPORT_GRADE_DISPLAY_TYPE_PERCENTAGE => get_string('percentage', 'grades')),
                             'averagesdisplaytype'    => array(GRADE_REPORT_PREFERENCE_INHERIT => get_string('inherit', 'grades'),
                                                               GRADE_REPORT_GRADE_DISPLAY_TYPE_REAL => get_string('real', 'grades'),
                                                               GRADE_REPORT_GRADE_DISPLAY_TYPE_PERCENTAGE => get_string('percentage', 'grades')),
                             'rangesdisplaytype'      => array(GRADE_REPORT_PREFERENCE_INHERIT => get_string('inherit', 'grades'),
                                                               GRADE_REPORT_GRADE_DISPLAY_TYPE_REAL => get_string('real', 'grades'),
                                                               GRADE_REPORT_GRADE_DISPLAY_TYPE_PERCENTAGE => get_string('percentage', 'grades')),
                             'averagesdecimalpoints'  => array(GRADE_REPORT_PREFERENCE_INHERIT => get_string('inherit', 'grades'),
                                                               0, 1, 2, 3, 4, 5),
                             'rangesdecimalpoints'    => array(GRADE_REPORT_PREFERENCE_INHERIT => get_string('inherit', 'grades'),
                                                               0, 1, 2, 3, 4, 5),
                             'decimalpoints'          => array(0, 1, 2, 3, 4, 5),
                             'studentsperpage'        => 'text');

        foreach ($preferences as $pref => $type) {
            $full_pref  = 'grade_report_' . $pref;
            $pref_value = get_user_preferences($full_pref, $CFG->$full_pref);

            $options = null;
            if (is_array($type)) {
                $options = $type;
                $type = 'select';
            }

            $mform->addElement($type, $full_pref, get_string($pref, 'grades'), $options);
            $mform->setHelpButton($full_pref, array(false, get_string($pref, 'grades'), false, true, false, get_string("config_$pref", 'grades')));
            $mform->setDefault($full_pref, $pref_value);
            $mform->setType($full_pref, PARAM_ALPHANUM);
        }

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
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
