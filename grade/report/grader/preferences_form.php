<?php  //$Id$

require_once($CFG->libdir.'/formslib.php');

/**
 * First implementation of the preferences in the form of a moodleform.
 * TODO add "reset to site defaults" button
 */
class grader_report_preferences_form extends moodleform {

    function definition() {
        global $USER, $CFG;

        $mform    =& $this->_form;
        $course   = $this->_customdata['course'];

        $context = get_context_instance(CONTEXT_COURSE, $course->id);
        $systemcontext = get_context_instance(CONTEXT_SYSTEM);

        $strgradeboundary       = get_string('gradeboundary', 'grades');
        $strconfiggradeboundary = get_string('configgradeboundary', 'grades');
        $strgradeletter         = get_string('gradeletter', 'grades');
        $strconfiggradeletter   = get_string('configgradeletter', 'grades');
        $strinherit             = get_string('inherit', 'grades');
        $stryes                 = get_string('yes');
        $strno                  = get_string('no');


        $checkbox_default = array(GRADE_REPORT_PREFERENCE_DEFAULT => 'default', 0 => $strno, 1 => $stryes);

/// form definition with preferences defaults
//--------------------------------------------------------------------------------
        $preferences = array();

        // Initialise the preferences arrays with grade:manage capabilities
        if (has_capability('moodle/grade:manage', $context)) {
            $preferences['prefgeneral'] = array(
                          'decimalpoints'       => array(GRADE_REPORT_PREFERENCE_DEFAULT => 'default', 0, 1, 2, 3, 4, 5),
                          'aggregationview'     => array(GRADE_REPORT_PREFERENCE_DEFAULT => 'default',
                                                         GRADE_REPORT_AGGREGATION_VIEW_FULL => get_string('fullmode', 'grades'),
                                                         GRADE_REPORT_AGGREGATION_VIEW_AGGREGATES_ONLY => get_string('aggregatesonly', 'grades'),
                                                         GRADE_REPORT_AGGREGATION_VIEW_GRADES_ONLY => get_string('gradesonly', 'grades')),
                          'meanselection'       => array(GRADE_REPORT_PREFERENCE_DEFAULT => 'default',
                                                         GRADE_REPORT_MEAN_ALL => get_string('meanall', 'grades'),
                                                         GRADE_REPORT_MEAN_GRADED => get_string('meangraded', 'grades')));


            $preferences['prefshow'] = array('showcalculations'  => $checkbox_default,
                                             'showeyecons'       => $checkbox_default,
                                             'showaverages'      => $checkbox_default,
                                             'showgroups'        => $checkbox_default,
                                             'showlocks'         => $checkbox_default);

            $preferences['prefrows'] = array(
                        'averagesdisplaytype'    => array(GRADE_DISPLAY_TYPE_DEFAULT => 'default',
                                                          GRADE_DISPLAY_TYPE_REAL => get_string('real', 'grades'),
                                                          GRADE_DISPLAY_TYPE_PERCENTAGE => get_string('percentage', 'grades'),
                                                          GRADE_DISPLAY_TYPE_LETTER => get_string('letter', 'grades')),
                        'rangesdisplaytype'      => array(GRADE_DISPLAY_TYPE_DEFAULT => 'default',
                                                          GRADE_DISPLAY_TYPE_REAL => get_string('real', 'grades'),
                                                          GRADE_DISPLAY_TYPE_PERCENTAGE => get_string('percentage', 'grades'),
                                                          GRADE_DISPLAY_TYPE_LETTER => get_string('letter', 'grades')),
                        'averagesdecimalpoints'  => array(GRADE_REPORT_PREFERENCE_DEFAULT => 'default',
                                                          GRADE_REPORT_PREFERENCE_INHERIT => $strinherit, 0, 1, 2, 3, 4, 5),
                        'rangesdecimalpoints'    => array(GRADE_REPORT_PREFERENCE_DEFAULT => 'default',
                                                          GRADE_REPORT_PREFERENCE_INHERIT => $strinherit, 0, 1, 2, 3, 4, 5));

        }

        // quickgrading and quickfeedback are conditional on grade:edit capability
        if (has_capability('moodle/grade:edit', $context)) {
            $preferences['prefgeneral']['quickgrading'] = $checkbox_default;
            $preferences['prefgeneral']['quickfeedback'] = $checkbox_default;
        }

        // View capability is the lowest permission. Users with grade:manage or grade:edit must also have grader:view
        if (has_capability('gradereport/grader:view', $context)) {
            $preferences['prefgeneral']['studentsperpage'] = 'text';
            $preferences['prefgeneral']['aggregationposition'] = array(GRADE_REPORT_PREFERENCE_DEFAULT => 'default',
                                                                     GRADE_REPORT_AGGREGATION_POSITION_LEFT => get_string('left', 'grades'),
                                                                     GRADE_REPORT_AGGREGATION_POSITION_RIGHT => get_string('right', 'grades'));
            $preferences['prefgeneral']['enableajax'] = $checkbox_default;

            $preferences['prefshow']['showuserimage'] = $checkbox_default;
            $preferences['prefshow']['showactivityicons'] = $checkbox_default;
            $preferences['prefshow']['showranges'] = $checkbox_default;

            $preferences['prefrows']['shownumberofgrades'] = $checkbox_default;
        }


        foreach ($preferences as $group => $prefs) {
            $mform->addElement('header', $group, get_string($group, 'grades'));

            foreach ($prefs as $pref => $type) {
                // Detect and process dynamically numbered preferences
                if (preg_match('/([^[0-9]+)([0-9]+)/', $pref, $matches)) {
                    $lang_string = $matches[1];
                    $number = ' ' . $matches[2];
                } else {
                    $lang_string = $pref;
                    $number = null;
                }

                $full_pref  = 'grade_report_' . $pref;

                $pref_value = get_user_preferences($full_pref);

                $options = null;
                if (is_array($type)) {
                    $options = $type;
                    $type = 'select';
                    $default = $options[$CFG->$full_pref];
                } else {
                    $default = $CFG->$full_pref;
                }

                $help_string = get_string("config$lang_string", 'grades');

                // Replace the 'default' value with the site default language string
                if (!is_null($options) AND $options[GRADE_REPORT_PREFERENCE_DEFAULT] == 'default') {
                    $options[GRADE_REPORT_PREFERENCE_DEFAULT] = get_string('sitedefault', 'grades', $default);
                } elseif ($type == 'text') {
                    $help_string = get_string("config{$lang_string}default", 'grades', $default);
                }

                $label = get_string($lang_string, 'grades') . $number;

                $mform->addElement($type, $full_pref, $label, $options);
                $mform->setHelpButton($full_pref, array(false, get_string($lang_string, 'grades'), false, true, false, $help_string));
                $mform->setDefault($full_pref, $pref_value);
                $mform->setType($full_pref, PARAM_ALPHANUM);
            }
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
