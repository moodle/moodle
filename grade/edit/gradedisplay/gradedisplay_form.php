<?php  //$Id$

require_once $CFG->libdir.'/formslib.php';

class edit_grade_display_form extends moodleform {

    function definition() {
        global $CFG, $COURSE;

        $mform =& $this->_form;
        $context = get_context_instance(CONTEXT_COURSE, $COURSE->id);
        $course_has_letters = $this->_customdata['course_has_letters'];
        $coursegradedisplaytype = get_field('grade_items', 'display', 'courseid', $COURSE->id, 'itemtype', 'course');
        $coursegradedecimals    = get_field('grade_items', 'decimals', 'courseid', $COURSE->id, 'itemtype', 'course');

        $mform->addElement('header', 'coursesettings', get_string('coursesettings', 'grades'));

        $gradedisplaytypes = array(GRADE_DISPLAY_TYPE_DEFAULT => get_string('default'),
                                   GRADE_DISPLAY_TYPE_REAL => get_string('real', 'grades'),
                                   GRADE_DISPLAY_TYPE_PERCENTAGE => get_string('percentage', 'grades'),
                                   GRADE_DISPLAY_TYPE_LETTER => get_string('letter', 'grades'));
        $label = get_string('coursegradedisplaytype', 'grades') . ' (' . get_string('default', 'grades') . ': '
                . $gradedisplaytypes[$CFG->grade_report_gradedisplaytype] . ')';
        $mform->addElement('select', 'display', $label, $gradedisplaytypes);
        $mform->setHelpButton('display', array(false, get_string('coursegradedisplaytype', 'grades'),
                false, true, false, get_string('configcoursegradedisplaytype', 'grades')));
        $mform->setDefault('display', $coursegradedisplaytype);
        $mform->setType($coursegradedisplaytype, PARAM_INT);

        $options = array(GRADE_DECIMALS_DEFAULT => get_string('default', 'grades'), 0, 1, 2, 3, 4, 5);
        $label = get_string('decimalpoints', 'grades') . ' (' . get_string('default', 'grades') . ': ' . $options[$CFG->grade_report_decimalpoints] . ')';
        $mform->addElement('select', 'decimals', $label, $options);
        $mform->setHelpButton('decimals', array(false, get_string('decimalpoints', 'grades'), false, true, false, get_string("configdecimalpoints", 'grades')));
        $mform->setDefault('decimals', $coursegradedecimals);

        // Disable decimals if displaytype is not REAL or PERCENTAGE
        $mform->disabledIf('decimals', 'display', "eq", GRADE_DISPLAY_TYPE_LETTER);

        $course_set_to_letters  = $coursegradedisplaytype == GRADE_DISPLAY_TYPE_LETTER;
        $course_set_to_default  = $coursegradedisplaytype == GRADE_DISPLAY_TYPE_DEFAULT;
        $site_set_to_letters = $CFG->grade_report_gradedisplaytype == GRADE_DISPLAY_TYPE_LETTER;

        // Disable decimals if course displaytype is DEFAULT and site displaytype is LETTER
        if ($site_set_to_letters) {
            $mform->disabledIf('decimals', 'display', "eq", GRADE_DISPLAY_TYPE_DEFAULT);
        }

        if ($course_set_to_letters || ($course_set_to_default && $site_set_to_letters)) {

            $mform->addElement('header', 'gradeletters', get_string('gradeletters', 'grades'));
            $percentages = array(null => get_string('unused', 'grades'));

            $mform->addElement('checkbox', 'override', get_string('overridesitedefaultgradedisplaytype', 'grades'));
            $mform->setHelpButton('override', array(false, get_string('overridesitedefaultgradedisplaytype', 'grades'),
                    false, true, false, get_string('overridesitedefaultgradedisplaytypehelp', 'grades')));
            $mform->setDefault('override', $course_has_letters);

            for ($i=100; $i > -1; $i--) {
                $percentages[$i] = "$i%";
            }

            $elementsarray = array();

            // Get course letters if they exist
            if ($letters = get_records('grade_letters', 'contextid', $context->id, 'lowerboundary DESC')) {
                $i = 1;
                foreach ($letters as $letter) {
                    $elementsarray[$i]['letter'] = $letter->letter;
                    $elementsarray[$i]['boundary'] = $letter->lowerboundary;
                    $i++;
                }
            } else { // Get site default for each letter
                for ($i = 1; $i <= 10; $i++) {
                    $elementsarray[$i]['letter'] = $CFG->{'grade_report_gradeletter'.$i};
                    $elementsarray[$i]['boundary'] = $CFG->{'grade_report_gradeboundary'.$i};
                }
            }

            foreach ($elementsarray as $i => $element) {
                $letter = $element['letter'];
                $boundary = $element['boundary'];

                $gradelettername = 'gradeletter' . $i;
                $gradeletterstring = get_string('gradeletter', 'grades') . " $i";
                $gradeletterhelp = get_string('configgradeletter', 'grades');

                $gradeboundaryname = 'gradeboundary' . $i;
                $gradeboundarystring = get_string('gradeboundary', 'grades') . " $i";
                $gradeboundaryhelp = get_string('configgradeboundary', 'grades');

                $mform->addElement('text', $gradelettername, $gradeletterstring);
                $mform->setHelpButton($gradelettername, array(false, $gradeletterstring, false, true, false, $gradeletterhelp));
                $mform->setDefault($gradelettername, $letter);
                $mform->setType($gradelettername, PARAM_RAW);
                $mform->disabledIf($gradelettername, 'override');

                $mform->addElement('select', $gradeboundaryname, $gradeboundarystring, $percentages);
                $mform->setHelpButton($gradeboundaryname, array(false, $gradeboundarystring, false, true, false, $gradeboundaryhelp));
                $mform->setDefault($gradeboundaryname, $boundary);
                $mform->setType($gradeboundaryname, PARAM_ALPHANUM);
                $mform->disabledIf($gradeboundaryname, 'override');
            }

            $mform->addElement('submit', 'addgradeletter', get_string('addgradeletter', 'grades'));
            $mform->disabledIf('addgradeletter', 'override');
        }

        // hidden params
        $mform->addElement('hidden', 'id', $COURSE->id);
        $mform->setType('id', PARAM_INT);

/// add return tracking info
        $gpr = $this->_customdata['gpr'];
        $gpr->add_mform_elements($mform);

//-------------------------------------------------------------------------------
        // buttons
        $this->add_action_buttons();
    }

    function definition_after_data() {
        global $CFG, $COURSE;

        $context = get_context_instance(CONTEXT_COURSE, $COURSE->id);

    }
}

?>
