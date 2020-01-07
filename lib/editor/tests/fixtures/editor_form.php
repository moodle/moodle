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
 * Provides {@link lib/editor/tests/fixtures/editor_form} class.
 *
 * @package core_editor
 * @copyright 2018 Jake Hau <phuchau1509@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir.'/formslib.php');

/**
 * Class editor_form
 *
 * Demonstrates use of editor with disabledIf function.
 * This fixture is only used by the Behat test.
 *
 * @package core_editor
 * @copyright 2018 Jake Hau <phuchau1509@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class editor_form extends moodleform {

    /**
     * Form definition. Abstract method - always override!
     */
    protected function definition() {
        $mform = $this->_form;
        $editoroptions = $this->_customdata['editoroptions'] ?? null;

        // Add header.
        $mform->addElement('header', 'myheader', 'Editor in Moodle form');

        // Add element control.
        $mform->addElement('select', 'mycontrol', 'My control', ['Enable', 'Disable']);

        // Add editor.
        $mform->addElement('editor', 'myeditor', 'My Editor', null, $editoroptions);
        $mform->setType('myeditor', PARAM_RAW);

        // Add control.
        $mform->disabledIf('myeditor', 'mycontrol', 'eq', 1);
    }
}
