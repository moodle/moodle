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
 * Fixture for testing the functionality of read-only forms.
 *
 * @package core
 * @copyright 2018 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/formslib.php');

$sections = optional_param('sections', 2, PARAM_INT);
require_login();


/**
 * The form used for testing.
 */
class test_read_only_form extends moodleform {
    protected function definition() {
        $mform = $this->_form;

        $sections = $this->_customdata;

        $mform->addElement('header', 'sectionheader', 'First section');

        $mform->addElement('text', 'name', 'Name');
        $mform->setDefault('name', 'Important information');
        $mform->setType('name', PARAM_RAW);

        $mform->setExpanded('sectionheader', false);

        if ($sections > 1) {
            $mform->addElement('header', 'secondsection', 'Other section header');

            $mform->addElement('text', 'other', 'Other');
            $mform->setDefault('other', 'Other information');
            $mform->setType('other', PARAM_RAW);

            $mform->setExpanded('secondsection', false);
        }

        $this->add_action_buttons();
    }
}

$PAGE->set_context(context_system::instance());
$PAGE->set_url('/lib/tests/fixtures/readonlyform.php');

$form = new test_read_only_form(null, $sections, 'post', '', null, false); // The false here is $editable.

echo $OUTPUT->header();
echo $form->render();
echo $OUTPUT->footer();
