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

require_once(__DIR__ . '/../../../../config.php');

defined('BEHAT_SITE_RUNNING') || die();

global $CFG, $PAGE, $OUTPUT;
require_once($CFG->libdir . '/formslib.php');
$PAGE->set_url('/lib/form/tests/fixtures/multiselect_hideif_disabledif_form.php');
$PAGE->add_body_class('limitedwidth');
require_login();
$PAGE->set_context(core\context\system::instance());

/**
 * Test class for hiding and disabling elements dependent on a multi-select element.
 *
 * @package   core_form
 * @copyright 2024 Lars Bonczek (@innoCampus, TU Berlin)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class test_multiselect_hideif_disabledif_form extends moodleform {

    /**
     * Form definition.
     */
    public function definition(): void {
        $mform = $this->_form;

        $mform->addElement('select', 'multiselect1', 'multiselect1', [
            1 => 'Option 1',
            2 => 'Option 2',
        ], ['multiple' => true]);

        $mform->addElement('checkbox', 'disabledIfEq_', "Disabled if selection 'eq' []");
        $mform->addElement('checkbox', 'disabledIfIn_', "Disabled if selection 'in' []");
        $mform->addElement('checkbox', 'disabledIfNeq_', "Disabled if selection 'neq' []");
        $mform->addElement('checkbox', 'disabledIfEq1', "Disabled if selection 'eq' ['1']");
        $mform->addElement('checkbox', 'disabledIfIn1', "Disabled if selection 'in' ['1']");
        $mform->addElement('checkbox', 'disabledIfNeq1', "Disabled if selection 'neq' ['1']");
        $mform->addElement('checkbox', 'disabledIfEq12', "Disabled if selection 'eq' ['1', '2']");
        $mform->addElement('checkbox', 'disabledIfIn12', "Disabled if selection 'in' ['1', '2']");
        $mform->addElement('checkbox', 'disabledIfNeq12', "Disabled if selection 'neq' ['1', '2']");

        $mform->disabledIf('disabledIfEq_', 'multiselect1[]', 'eq', []);
        $mform->disabledIf('disabledIfIn_', 'multiselect1[]', 'in', []);
        $mform->disabledIf('disabledIfNeq_', 'multiselect1[]', 'neq', []);
        $mform->disabledIf('disabledIfEq1', 'multiselect1[]', 'eq', ['1']);
        $mform->disabledIf('disabledIfIn1', 'multiselect1[]', 'in', ['1']);
        $mform->disabledIf('disabledIfNeq1', 'multiselect1[]', 'neq', ['1']);
        $mform->disabledIf('disabledIfEq12', 'multiselect1[]', 'eq', ['1', '2']);
        $mform->disabledIf('disabledIfIn12', 'multiselect1[]', 'in', ['1', '2']);
        $mform->disabledIf('disabledIfNeq12', 'multiselect1[]', 'neq', ['1', '2']);

        $mform->addElement('checkbox', 'hideIfEq_', "Hide if selection 'eq' []");
        $mform->addElement('checkbox', 'hideIfIn_', "Hide if selection 'in' []");
        $mform->addElement('checkbox', 'hideIfNeq_', "Hide if selection 'neq' []");
        $mform->addElement('checkbox', 'hideIfEq1', "Hide if selection 'eq' ['1']");
        $mform->addElement('checkbox', 'hideIfIn1', "Hide if selection 'in' ['1']");
        $mform->addElement('checkbox', 'hideIfNeq1', "Hide if selection 'neq' ['1']");
        $mform->addElement('checkbox', 'hideIfEq12', "Hide if selection 'eq' ['1', '2']");
        $mform->addElement('checkbox', 'hideIfIn12', "Hide if selection 'in' ['1', '2']");
        $mform->addElement('checkbox', 'hideIfNeq12', "Hide if selection 'neq' ['1', '2']");

        $mform->hideIf('hideIfEq_', 'multiselect1[]', 'eq', []);
        $mform->hideIf('hideIfIn_', 'multiselect1[]', 'in', []);
        $mform->hideIf('hideIfNeq_', 'multiselect1[]', 'neq', []);
        $mform->hideIf('hideIfEq1', 'multiselect1[]', 'eq', ['1']);
        $mform->hideIf('hideIfIn1', 'multiselect1[]', 'in', ['1']);
        $mform->hideIf('hideIfNeq1', 'multiselect1[]', 'neq', ['1']);
        $mform->hideIf('hideIfEq12', 'multiselect1[]', 'eq', ['1', '2']);
        $mform->hideIf('hideIfIn12', 'multiselect1[]', 'in', ['1', '2']);
        $mform->hideIf('hideIfNeq12', 'multiselect1[]', 'neq', ['1', '2']);

        $this->add_action_buttons();
    }
}

$form = new test_multiselect_hideif_disabledif_form();

echo $OUTPUT->header();
$form->display();
echo $OUTPUT->footer();
