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
$PAGE->set_url('/lib/form/tests/fixtures/filemanager_hideif_disabledif_form.php');
$PAGE->add_body_class('limitedwidth');
require_login();
$PAGE->set_context(core\context\system::instance());

/**
 * Test class for disabling and hiding a filemanager element.
 *
 * @copyright 2024 David Woloszyn <david.woloszyn@moodle.com>
 * @package   core_form
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class test_filemanager_hideif_disabledif_form extends moodleform {

    /**
     * Form definition.
     */
    public function definition(): void {
        $mform = $this->_form;

        $attributes = [];
        $attributes['maxbytes'] = '1024';
        $attributes['accepted_types'] = array_map('trim', explode(',', '.odt, .pdf'));
        $attributes['subdirs'] = false;
        $attributes['maxfiles'] = 3;

        // Radio buttons.
        $radiogroup = [
            $mform->createElement('radio', 'some_radios', '', 'Enable', '1'),
            $mform->createElement('radio', 'some_radios', '', 'Disable', '2'),
            $mform->createElement('radio', 'some_radios', '', 'Hide', '3'),
        ];

        $mform->addGroup($radiogroup, 'some_radios_group', 'Enable/Disable/Hide', ' ', false);
        $mform->setDefault('some_radios', 1);

        // Standard file manager.
        $mform->addElement('filemanager', 'some_filemanager', 'Standard filemanager', '', $attributes);
        $mform->disabledIf('some_filemanager', 'some_radios', 'eq', '2');
        $mform->hideIf('some_filemanager', 'some_radios', 'eq', '3');

        // File manager nested in group.
        $filemanagergroup = [];
        $filemanagergroup[] = $mform->createElement('filemanager', 'some_filemanager_group', '', null, $attributes);
        $mform->addGroup($filemanagergroup, 'filemanager_group', 'Group filemanager');
        $mform->disabledIf('filemanager_group', 'some_radios', 'eq', '2');

        $this->add_action_buttons();
    }
}

$form = new test_filemanager_hideif_disabledif_form();

echo $OUTPUT->header();
$form->display();
echo $OUTPUT->footer();
