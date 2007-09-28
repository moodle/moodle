<?php  //$Id$

require_once $CFG->libdir.'/formslib.php';

class edit_letter_form extends moodleform {

    function definition() {
        $mform =& $this->_form;
        $num   = $this->_customdata['num'];
        $admin = $this->_customdata['admin'];

        $mform->addElement('header', 'gradeletters', get_string('gradeletters', 'grades'));

        $mform->addElement('checkbox', 'override', get_string('overridesitedefaultgradedisplaytype', 'grades'));
        $mform->setHelpButton('override', array(false, get_string('overridesitedefaultgradedisplaytype', 'grades'),
                false, true, false, get_string('overridesitedefaultgradedisplaytypehelp', 'grades')));

        $gradeletterhelp   = get_string('configgradeletter', 'grades');
        $gradeboundaryhelp = get_string('configgradeboundary', 'grades');
        $gradeletter       = get_string('gradeletter', 'grades');
        $gradeboundary     = get_string('gradeboundary', 'grades');

        $percentages = array(-1 => get_string('unused', 'grades'));
        for ($i=100; $i > -1; $i--) {
            $percentages[$i] = "$i %";
        }

        for($i=1; $i<$num+1; $i++) {
            $gradelettername = 'gradeletter'.$i;
            $gradeboundaryname = 'gradeboundary'.$i;

            $mform->addElement('text', $gradelettername, $gradeletter." $i");
            $mform->setHelpButton($gradelettername, array(false, $gradeletter." $i", false, true, false, $gradeletterhelp));
            $mform->setType($gradelettername, PARAM_TEXT);
            $mform->disabledIf($gradelettername, 'override', 'notchecked');
            $mform->disabledIf($gradelettername, $gradeboundaryname, 'eq', -1);

            $mform->addElement('select', $gradeboundaryname, $gradeboundary." $i", $percentages);
            $mform->setHelpButton($gradeboundaryname, array(false, $gradeboundary." $i", false, true, false, $gradeboundaryhelp));
            $mform->setDefault($gradeboundaryname, -1);
            $mform->setType($gradeboundaryname, PARAM_INT);
            $mform->disabledIf($gradeboundaryname, 'override', 'notchecked');
        }

        // hidden params
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

//-------------------------------------------------------------------------------
        // buttons
        $this->add_action_buttons(!$admin);
    }

}

?>
