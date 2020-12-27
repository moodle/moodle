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
 * Allow something form.
 *
 * @package    core_role
 * @copyright  2009 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/formslib.php");

class core_role_permission_allow_form extends moodleform {

    /**
     * Define the form.
     */
    protected function definition() {
        global $CFG;

        $mform = $this->_form;
        list($context, $capability, $overridableroles) = $this->_customdata;

        list($needed, $forbidden) = get_roles_with_cap_in_context($context, $capability->name);
        foreach ($needed as $id => $unused) {
            unset($overridableroles[$id]);
        }
        foreach ($forbidden as $id => $unused) {
            unset($overridableroles[$id]);
        }

        $mform->addElement('header', 'allowheader', get_string('roleallowheader', 'core_role'));

        $mform->addElement('select', 'roleid', get_string('roleselect', 'core_role'), $overridableroles);

        $mform->addElement('hidden', 'capability');
        $mform->setType('capability', PARAM_CAPABILITY);
        $mform->setDefault('capability', $capability->name);

        $mform->addElement('hidden', 'contextid');
        $mform->setType('contextid', PARAM_INT);
        $mform->setDefault('contextid', $context->id);

        $mform->addElement('hidden', 'allow');
        $mform->setType('allow', PARAM_INT);
        $mform->setDefault('allow', 1);

        $this->add_action_buttons(true, get_string('allow', 'core_role'));
    }
}
