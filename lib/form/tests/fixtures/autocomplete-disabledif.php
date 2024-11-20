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
 * Test form for testing autocomplete behaviour.
 *
 * @copyright 2020 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../../config.php');
require_once($CFG->libdir . '/formslib.php');

if (!defined('BEHAT_SITE_RUNNING')) {
    throw new coding_exception('This fixture can only be used in Behat tests.');
}
require_login();
require_capability('moodle/site:config', context_system::instance());


/**
 * The form class for our test.
 */
class test_form extends moodleform {

    protected function definition() {
        $mform = $this->_form;

        $mform->addElement('course', 'x', 'Controls the rest');

        $mform->addElement('text', 'enabledifblank', 'Single select will be enabled if the control is blank');
        $mform->disabledIf('enabledifblank', 'x', 'neq', '');
        $mform->setType('enabledifblank', PARAM_RAW);

        $mform->addElement('text', 'disabledifblank', 'Single select will be disabled if the control is blank');
        $mform->disabledIf('disabledifblank', 'x', 'eq', '');
        $mform->setType('disabledifblank', PARAM_RAW);

        $this->add_action_buttons();
    }
}

$PAGE->set_context(context_system::instance());
$PAGE->set_url('/lib/form/tests/fixtures/autocomplete-disabledif.php');
echo $OUTPUT->header();

$form = new test_form();
if ($data = $form->get_data()) {
    echo $OUTPUT->notification("Data was submitted (but still re-showing form).", 'success');
}

$form->display();
echo $OUTPUT->footer();
