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
 * Test page for choice dropdown field type.
 *
 * @copyright 2023 Ferran Recio <ferran@moodle.com>
 * @package   core_form
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core\output\choicelist;

require_once(__DIR__ . '/../../../../../config.php');

defined('BEHAT_SITE_RUNNING') || die();

global $CFG, $PAGE, $OUTPUT;
require_once($CFG->libdir . '/formslib.php');
$PAGE->set_url('/lib/form/tests/behat/fixtures/field_choicedropdown_testpage.php');
$PAGE->add_body_class('limitedwidth');
require_login();
$PAGE->set_context(core\context\system::instance());

/**
 * Class test_choice_dropdown
 * @package core_form
 */
class test_choice_dropdown extends moodleform {
    /**
     * Define the export form.
     */
    public function definition() {
        $mform = $this->_form;

        $options = new choicelist();
        $options->set_allow_empty(false);
        $options->add_option('option1', "Text option 1", [
            'description' => 'Option 1 description',
            'icon' => new pix_icon('t/hide', 'Eye icon 1'),
        ]);
        $options->add_option('option2', "Text option 2", [
            'description' => 'Option 2 description',
            'icon' => new pix_icon('t/stealth', 'Eye icon 2'),
        ]);
        $options->add_option('option3', "Text option 3", [
            'description' => 'Option 3 description',
            'icon' => new pix_icon('t/show', 'Eye icon 3'),
        ]);

        $mform->addElement('header', 'database', "Basic example");
        $mform->addElement('choicedropdown', 'example0', "Basic choice dropdown", $options);

        $mform->addElement('header', 'database', "Disable choice dropdown");
        $mform->addElement('checkbox', 'disableme', 'Check to disable the first choice dropdown field.');
        $mform->addElement('choicedropdown', 'example1', "Disable if example", $options);
        $mform->disabledIf('example1', 'disableme', 'checked');

        $mform->addElement('header', 'database', "Hide choice dropdown");
        $mform->addElement('checkbox', 'hideme', 'Check to hide the first choice dropdown field.');
        $mform->addElement('choicedropdown', 'example2', "Hide if example", $options);
        $mform->hideIf('example2', 'hideme', 'checked');

        $options = new choicelist();
        $options->set_allow_empty(false);
        $options->add_option('hide', 'Hide or disable subelements');
        $options->add_option('show', 'Show or enable subelements');

        $mform->addElement('header', 'database', "Use choice dropdown to hide or disable other fields");
        $mform->addElement('choicedropdown', 'example3', "Control choice dropdown", $options);

        $mform->addElement('text', 'hideinput', 'Hide if element', ['maxlength' => 80, 'size' => 50]);
        $mform->hideIf('hideinput', 'example3', 'eq', 'hide');
        $mform->setDefault('hideinput', 'Is this visible?');
        $mform->setType('hideinput', PARAM_TEXT);

        $mform->addElement('text', 'disabledinput', 'Disabled if element', ['maxlength' => 80, 'size' => 50]);
        $mform->disabledIf('disabledinput', 'example3', 'eq', 'hide');
        $mform->setDefault('disabledinput', 'Is this enabled?');
        $mform->setType('disabledinput', PARAM_TEXT);

        $this->add_action_buttons(false, 'Send form');
    }
}

echo $OUTPUT->header();

echo "<h2>Quickform integration test</h2>";

$form = new test_choice_dropdown();

$data = $form->get_data();
if ($data) {
    echo "<h3>Submitted data</h3>";
    echo '<div id="submitted_data"><ul>';
    $data = (array) $data;
    foreach ($data as $field => $value) {
        echo "<li id=\"sumbmitted_{$field}\">$field: $value</li>";
    }
    echo '</ul></div>';
}
$form->display();

echo $OUTPUT->footer();
