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

        $strgradeboundary       = get_string('gradeboundary', 'grades');
        $strconfiggradeboundary = get_string('configgradeboundary', 'grades');
        $strgradeletter         = get_string('gradeletter', 'grades');
        $strconfiggradeletter   = get_string('configgradeletter', 'grades');
        $strinherit             = get_string('inherit', 'grades');
        $stryes                 = get_string('yes');
        $strno                  = get_string('no');

        $percentages = array(GRADE_REPORT_PREFERENCE_DEFAULT => 'default',
                             GRADE_REPORT_PREFERENCE_UNUSED => get_string('unused', 'grades'));
        for ($i=100; $i > -1; $i--) {
            $percentages[$i] = "$i%";
        }

        $checkbox_default = array(GRADE_REPORT_PREFERENCE_DEFAULT => 'default', 0 => $strno, 1 => $stryes);

/// form definition with preferences defaults
//--------------------------------------------------------------------------------
        $preferences = array();
        $preferences['prefgeneral'] = array(
                      'studentsperpage'        => 'text',
                      'quickgrading'           => $checkbox_default,
                      'quickfeedback'          => $checkbox_default,
                      'decimalpoints'          => array(GRADE_REPORT_PREFERENCE_DEFAULT => 'default', 0, 1, 2, 3, 4, 5),
                      'aggregationposition'    => array(GRADE_REPORT_PREFERENCE_DEFAULT => 'default',
                                                        GRADE_REPORT_AGGREGATION_POSITION_LEFT => get_string('left', 'grades'),
                                                        GRADE_REPORT_AGGREGATION_POSITION_RIGHT => get_string('right', 'grades')),
                      'aggregationview'        => array(GRADE_REPORT_PREFERENCE_DEFAULT => 'default',
                                                        GRADE_REPORT_AGGREGATION_VIEW_FULL => get_string('full', 'grades'),
                                                        GRADE_REPORT_AGGREGATION_VIEW_COMPACT => get_string('compact', 'grades')),
                      'gradedisplaytype'       => array(GRADE_REPORT_PREFERENCE_DEFAULT => 'default',
                                                        GRADE_REPORT_GRADE_DISPLAY_TYPE_REAL => get_string('real', 'grades'),
                                                        GRADE_REPORT_GRADE_DISPLAY_TYPE_PERCENTAGE => get_string('percentage', 'grades'),
                                                        GRADE_REPORT_GRADE_DISPLAY_TYPE_LETTER => get_string('letter', 'grades')),
                      'meanselection'          => array(GRADE_REPORT_PREFERENCE_DEFAULT => 'default',
                                                        GRADE_AGGREGATE_MEAN_ALL => get_string('meanall', 'grades'),
                                                        GRADE_AGGREGATE_MEAN_GRADED => get_string('meangraded', 'grades')),
                      'enableajax'             => $checkbox_default);

        $preferences['prefshow'] = array('showcalculations'       => $checkbox_default,
                                     'showeyecons'            => $checkbox_default,
                                     'showaverages'           => $checkbox_default,
                                     'showgroups'             => $checkbox_default,
                                     'showlocks'              => $checkbox_default,
                                     'showranges'             => $checkbox_default,
                                     'showuserimage'          => $checkbox_default,);

        $preferences['prefrows'] = array(
                    'averagesdisplaytype'    => array(GRADE_REPORT_PREFERENCE_DEFAULT => 'default',
                                                      GRADE_REPORT_PREFERENCE_INHERIT => $strinherit,
                                                      GRADE_REPORT_GRADE_DISPLAY_TYPE_REAL => get_string('real', 'grades'),
                                                      GRADE_REPORT_GRADE_DISPLAY_TYPE_PERCENTAGE => get_string('percentage', 'grades'),
                                                      GRADE_REPORT_GRADE_DISPLAY_TYPE_LETTER => get_string('letter', 'grades')),
                    'rangesdisplaytype'      => array(GRADE_REPORT_PREFERENCE_DEFAULT => 'default',
                                                      GRADE_REPORT_PREFERENCE_INHERIT => $strinherit,
                                                      GRADE_REPORT_GRADE_DISPLAY_TYPE_REAL => get_string('real', 'grades'),
                                                      GRADE_REPORT_GRADE_DISPLAY_TYPE_PERCENTAGE => get_string('percentage', 'grades'),
                                                      GRADE_REPORT_GRADE_DISPLAY_TYPE_LETTER => get_string('letter', 'grades')),
                    'averagesdecimalpoints'  => array(GRADE_REPORT_PREFERENCE_DEFAULT => 'default',
                                                      GRADE_REPORT_PREFERENCE_INHERIT => $strinherit, 0, 1, 2, 3, 4, 5),
                    'rangesdecimalpoints'    => array(GRADE_REPORT_PREFERENCE_DEFAULT => 'default',
                                                      GRADE_REPORT_PREFERENCE_INHERIT => $strinherit, 0, 1, 2, 3, 4, 5));


        for ($i = 1; $i <= 10; $i++) {
            $preferences['prefletters']['gradeboundary' . $i] = $percentages;
            $preferences['prefletters']['gradeletter' . $i] = 'text';
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
                    $help_string = get_string("config$lang_string", 'grades', $default);
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
