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
                             'aggregationposition'    => array(get_string('left', 'grades'), get_string('right', 'grades')),
                             'aggregationview'        => array(get_string('full', 'grades'), get_string('compact', 'grades')),
                             'gradedisplaytype'       => array(get_string('raw', 'grades'), get_string('percentage', 'grades')),
                             'averagesdisplaytype'    => array(get_string('raw', 'grades'), get_string('percentage', 'grades')),
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
            $mform->setType($full_pref, PARAM_INT);
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
