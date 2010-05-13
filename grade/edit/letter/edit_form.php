<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.


if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once $CFG->libdir.'/formslib.php';

class edit_letter_form extends moodleform {

    function definition() {
        $mform =& $this->_form;
        $num   = $this->_customdata['num'];
        $admin = $this->_customdata['admin'];

        $mform->addElement('header', 'gradeletters', get_string('gradeletters', 'grades'));
        
        // Only show "override site defaults" checkbox if editing the course grade letters
        if (!$admin) {
            $mform->addElement('checkbox', 'override', get_string('overridesitedefaultgradedisplaytype', 'grades'));
            $mform->setHelpButton('override', array('overridesitedefaultgradedisplaytype', get_string('overridesitedefaultgradedisplaytype', 'grades'), 'grade'));
        }

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
            if ($i == 1) {
                $mform->setHelpButton($gradelettername, array('gradeletter', get_string('gradeletter', 'grades'), 'grade'));
            }
            $mform->setType($gradelettername, PARAM_TEXT);
            
            if (!$admin) {
                $mform->disabledIf($gradelettername, 'override', 'notchecked');
                $mform->disabledIf($gradelettername, $gradeboundaryname, 'eq', -1);
            }

            $mform->addElement('select', $gradeboundaryname, $gradeboundary." $i", $percentages);
            if ($i == 1) {
                $mform->setHelpButton($gradeboundaryname, array('gradeboundary', get_string('gradeboundary', 'grades'), 'grade'));
            }
            $mform->setDefault($gradeboundaryname, -1);
            $mform->setType($gradeboundaryname, PARAM_INT);
            
            if (!$admin) {
                $mform->disabledIf($gradeboundaryname, 'override', 'notchecked');
            }
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
