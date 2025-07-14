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
 * Test for setting date fields in behat
 *
 * @package    core_form
 * @copyright  Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../../../config.php');

defined('BEHAT_SITE_RUNNING') || die();

global $CFG, $PAGE, $OUTPUT;
require_once($CFG->libdir . '/formslib.php');
$PAGE->set_url('/lib/form/tests/behat/fixtures/dates_form.php');
$PAGE->add_body_class('limitedwidth');
require_login();
$PAGE->set_context(context_system::instance());

/**
 * Test form class adding all types of date elements
 *
 * @package core_form
 */
class test_dates_form extends moodleform {
    /**
     * Define the form.
     */
    public function definition() {
        $mform = $this->_form;

        $mform->addElement('date_selector', 'simpledateonly', 'Simple only date');
        $mform->addElement('date_selector', 'simpleoptionaldateonly', 'Simple optional only date', ['optional' => true]);
        $mform->addElement('date_time_selector', 'simpledatetime', 'Simple date and time');
        $mform->addElement('date_time_selector', 'simpleoptionaldatetime', 'Simple optional date and time', ['optional' => true]);

        $group = [];
        $group[] = $mform->createElement('date_selector', 'group1dateonly', 'Group1 only date');
        $group[] = $mform->createElement('date_selector', 'group1optionaldateonly', 'Group1 optional only date',
            ['optional' => true]);
        $group[] = $mform->createElement('date_time_selector', 'group1datetime', 'Group1 date and time');
        $group[] = $mform->createElement('date_time_selector', 'group1optionaldatetime', 'Group1 optional date and time',
            ['optional' => true]);
        $mform->addGroup($group, 'dategroup1', 'Date group1', '', false);

        $group = [];
        $group[] = $mform->createElement('date_selector', 'group2dateonly', 'Group2 only date');
        $group[] = $mform->createElement('date_selector', 'group2optionaldateonly', 'Group2 optional only date',
            ['optional' => true]);
        $group[] = $mform->createElement('date_time_selector', 'group2datetime', 'Group2 date and time');
        $group[] = $mform->createElement('date_time_selector', 'group2optionaldatetime', 'Group2 optional date and time',
            ['optional' => true]);
        $mform->addGroup($group, 'dategroup2', 'Date group2', '', true);

        $this->add_action_buttons(false, 'Send form');
    }
}

echo $OUTPUT->header();

echo "<h2>Quickform integration test</h2>";

$form = new test_dates_form();

$data = $form->get_data();
if ($data) {
    echo "<h3>Submitted data</h3>";
    echo '<div id="submitted_data"><ul>';
    $data = (array) $data;
    foreach ($data as $field => $value) {
        if (is_array($value)) {
            foreach ($value as $key => $v) {
                echo "<li id=\"sumbmitted_{$field}_$key\">{$field}[$key]: $v</li>";
            }
        } else {
            echo "<li id=\"sumbmitted_{$field}\">$field: $value</li>";
        }
    }
    echo '</ul></div>';
}
$form->display();

echo $OUTPUT->footer();
