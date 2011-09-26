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
 * Settings form for cronsetup.php.
 *
 * @package    tool
 * @subpackage qeupgradehelper
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');


/**
 * Cron setup form.
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_qeupgradehelper_cron_setup_form extends moodleform {
    public function definition() {
        $mform = $this->_form;

        $mform->addElement('selectyesno', 'cronenabled',
                get_string('cronenabled', 'tool_qeupgradehelper'));

        $mform->addElement('select', 'starthour',
                get_string('cronstarthour', 'tool_qeupgradehelper'), range(0, 23));

        $mform->addElement('select', 'stophour',
                get_string('cronstophour', 'tool_qeupgradehelper'),
                array_combine(range(1, 24), range(1, 24)));
        $mform->setDefault('stophour', 24);

        $mform->addElement('duration', 'procesingtime',
                get_string('cronprocesingtime', 'tool_qeupgradehelper'));
        $mform->setDefault('procesingtime', 60);

        $mform->disabledIf('starthour', 'cronenabled', 'eq', 0);
        $mform->disabledIf('stophour', 'cronenabled', 'eq', 0);
        $mform->disabledIf('procesingtime', 'cronenabled', 'eq', 0);

        $this->add_action_buttons();
    }
}
