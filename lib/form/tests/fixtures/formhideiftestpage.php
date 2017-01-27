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
 * To support behat tests for hideif functionality (which is not yet used by any core forms).
 *
 * @package   core
 * @copyright 2016 Davo Smith, Synergy Learning
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__.'/../../../../config.php');
global $CFG, $PAGE, $OUTPUT;
require_once($CFG->libdir.'/formslib.php');

// Behat test fixture only.
defined('BEHAT_SITE_RUNNING') || die('Only available on Behat test server');

/**
 * Class hideif_form
 * @copyright 2016 Davo Smith, Synergy Learning
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class hideif_form extends moodleform {

    /**
     * Form definition.
     */
    protected function definition() {
        $mform = $this->_form;

        // Use 'selectyesno' to show/hide element.
        $mform->addElement('selectyesno', 'selectyesnoexample', 'Select yesno example');
        $mform->setDefault('selectyesnoexample', 0);

        $mform->addElement('text', 'testeqhideif', 'Test eq hideif');
        $mform->setType('testeqhideif', PARAM_TEXT);
        $mform->hideIf('testeqhideif', 'selectyesnoexample', 'eq', 0);

        // Use 'checkbox' to show/hide element.
        $mform->addElement('advcheckbox', 'checkboxexample', 'Checkbox example');
        $mform->setDefault('checkboxexample', 0);

        $mform->addElement('text', 'testcheckedhideif', 'Test checked hideif');
        $mform->setType('testcheckedhideif', PARAM_TEXT);
        $mform->hideIf('testcheckedhideif', 'checkboxexample', 'checked');

        $mform->addElement('text', 'testnotcheckedhideif', 'Test not checked hideif');
        $mform->setType('testnotcheckedhideif', PARAM_TEXT);
        $mform->hideIf('testnotcheckedhideif', 'checkboxexample', 'notchecked');

        // Use 'select' to show/hide element.
        $opts = [1, 2, 3, 4, 5];
        $opts = array_combine($opts, $opts);
        $mform->addElement('select', 'selectexample', 'Select example', $opts);
        $mform->setDefault('selectexample', 1);

        $mform->addElement('text', 'testinhideif', 'Test in hideif');
        $mform->setType('testinhideif', PARAM_TEXT);
        $mform->hideIf('testinhideif', 'selectexample', 'in', [1, 2, 5]);

        $mform->addElement('submit', 'submitform', 'Submit');
    }
}

$PAGE->set_url('/lib/form/tests/fixtures/formhideiftestpage.php');
$PAGE->set_context(context_system::instance());
$form = new hideif_form();

echo $OUTPUT->header();
if ($data = $form->get_data()) {
    echo "<p>Number of submitted form elements: " . count((array)$data) . "</p>";
    print_object($data);
}
$form->display();
echo $OUTPUT->footer();
