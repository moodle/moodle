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
 * File containing the base import form.
 *
 * @package    tool_bulkenrol
 * @copyright  2013 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once($CFG->libdir.'/formslib.php');

/**
 * Base import form.
 *
 * @package    tool_bulkenrol
 * @copyright  2013 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_bulkenrol_base_form extends moodleform {

    /**
     * Empty definition.
     *
     * @return void
     */
    public function definition() {
    }

    /**
     * Adds the import settings part.
     *
     * @return void
     */
    public function add_import_options() {
        $mform = $this->_form;

        // Upload settings and file.
        $mform->addElement('header', 'importoptionshdr', get_string('importoptions', 'tool_bulkenrol'));
        $mform->setExpanded('importoptionshdr', true);

        $choices = array(
            'id' => 'ID',
            'email' => 'Email',
            'idnumber' => 'ID Number',
            'username' => 'Username',
        );
        $mform->addElement('select', 'options[resolveuser]', get_string('resolveuser', 'tool_bulkenrol'), $choices);
        $mform->setType('options[resolveuser]', PARAM_STRINGID);
        $mform->addHelpButton('options[resolveuser]', 'resolveuser', 'tool_bulkenrol');

        $choices = array(
            'id' => 'ID',
            'shortname' => 'Short Name',
            'idnumber' => 'ID Number',
        );
        $mform->addElement('select', 'options[resolvecourse]', get_string('resolvecourse', 'tool_bulkenrol'), $choices);
        $mform->setType('options[resolvecourse]', PARAM_STRINGID);
        $mform->addHelpButton('options[resolvecourse]', 'resolvecourse', 'tool_bulkenrol');

        $choices = array(
            'id' => 'ID',
            'shortname' => 'Short Name',
        );
        $mform->addElement('select', 'options[resolverole]', get_string('resolverole', 'tool_bulkenrol'), $choices);
        $mform->setType('options[resolverole]', PARAM_STRINGID);
        $mform->addHelpButton('options[resolverole]', 'resolverole', 'tool_bulkenrol');

    }

}
