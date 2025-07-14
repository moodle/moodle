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

/**
 * A moodleform for editing grade letters
 *
 * @package   core_grades
 * @copyright 2007 Petr Skoda
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once $CFG->libdir.'/formslib.php';

class edit_letter_form extends moodleform {

    public function definition() {
        $mform =& $this->_form;

        [
            'lettercount' => $lettercount,
            'admin' => $admin,
        ] = $this->_customdata;

        $mform->addElement('header', 'gradeletters', get_string('gradeletters', 'grades'));

        // Only show "override site defaults" checkbox if editing the course grade letters
        if (!$admin) {
            $mform->addElement('checkbox', 'override', get_string('overridesitedefaultgradedisplaytype', 'grades'));
            $mform->addHelpButton('override', 'overridesitedefaultgradedisplaytype', 'grades');
        }

        $gradeletter       = get_string('gradeletter', 'grades');
        $gradeboundary     = get_string('gradeboundary', 'grades');

        // The fields to create the grade letter/boundary.
        $elements = [];
        $elements[] = $mform->createElement('text', 'gradeletter', "{$gradeletter} {no}");
        $elements[] = $mform->createElement('static', '', '', '&ge;');
        $elements[] = $mform->createElement('float', 'gradeboundary', "{$gradeboundary} {no}");
        $elements[] = $mform->createElement('static', '', '', '%');

        // Element options/rules, fields should be disabled unless "Override" is checked for course grade letters.
        $options = [];
        $options['gradeletter']['type'] = PARAM_TEXT;

        if (!$admin) {
            $options['gradeletter']['disabledif'] = ['override', 'notchecked'];
            $options['gradeboundary']['disabledif'] = ['override', 'notchecked'];
        }

        // Create our repeatable elements, each one a group comprised of the fields defined previously.
        $this->repeat_elements([
            $mform->createElement('group', 'gradeentry', "{$gradeletter} {no}", $elements, [' '], false)
        ], $lettercount, $options, 'gradeentrycount', 'gradeentryadd', 3);

        // Add a help icon to first element group, if it exists.
        if ($mform->elementExists('gradeentry[0]')) {
            $mform->addHelpButton('gradeentry[0]', 'gradeletter', 'grades');
        }

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $this->add_action_buttons();
    }
}
