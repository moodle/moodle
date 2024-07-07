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

require_once(__DIR__ . '/../../../../../config.php');

defined('BEHAT_SITE_RUNNING') || die();

global $CFG, $PAGE, $OUTPUT;
require_once($CFG->libdir . '/formslib.php');
$PAGE->set_url('/lib/form/tests/behat/fixtures/static_hideif_disabledif_form.php');
$PAGE->add_body_class('limitedwidth');
require_login();
$PAGE->set_context(core\context\system::instance());

/**
 * Test class for hiding and disabling static elements.
 *
 * @package   core_form
 * @copyright Meirza <meirza.arson@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class test_static_hideif_disabledif_form extends moodleform {

    /**
     * Form definition.
     */
    public function definition(): void {
        $mform = $this->_form;

        // Radio buttons.
        $radiogroup = [
            $mform->createElement('radio', 'some_radios', '', 'Enable', '1'),
            $mform->createElement('radio', 'some_radios', '', 'Disable', '2'),
            $mform->createElement('radio', 'some_radios', '', 'Hide', '3'),
        ];

        $mform->addGroup($radiogroup, 'some_radios_group', 'Enable/Disable/Hide', ' ', false);
        $mform->setDefault('some_radios', 1);

        // Static element with conditions.
        $mform->addElement(
            'static',
            'some_static',
            'Static element',
            'Static with <a href="#">form elements</a>
            <input id="id_some_static_username" type="text" class="form-control mb-2" placeholder="Type username...">
            <button type="submit" class="btn btn-primary mb-2">Check</button>',
        );
        $mform->disabledIf('some_static', 'some_radios', 'eq', '2');
        $mform->hideIf('some_static', 'some_radios', 'eq', '3');

        $this->add_action_buttons();
    }
}

$form = new test_static_hideif_disabledif_form();

echo $OUTPUT->header();
$form->display();
echo $OUTPUT->footer();
