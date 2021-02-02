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
 * Capability tool settings form.
 *
 * Do no include this file, it is automatically loaded by the class loader!
 *
 * @package    tool_capability
 * @copyright  2013 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->libdir.'/formslib.php');

/**
 * Class tool_capability_settings_form
 *
 * The settings form for the comparison of roles/capabilities.
 *
 * @copyright  2013 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_capability_settings_form extends moodleform {

    /**
     * The form definition.
     */
    public function definition() {
        $form = $this->_form;
        $capabilities = $this->_customdata['capabilities'];
        $roles = $this->_customdata['roles'];
        // Set the form ID.
        $form->setAttributes(array('id' => 'capability-overview-form') + $form->getAttributes());

        $form->addElement('header', 'reportsettings', get_string('reportsettings', 'tool_capability'));
        $form->addElement('html', html_writer::tag('p', get_string('intro', 'tool_capability'), array('id' => 'intro')));

        $form->addElement('hidden', 'search');
        $form->setType('search', PARAM_TEXT);

        $attributes = array('multiple' => 'multiple', 'size' => 10, 'data-search' => 'capability');
        $form->addElement('select', 'capability', get_string('capabilitylabel', 'tool_capability'), $capabilities, $attributes);
        $form->setType('capability', PARAM_CAPABILITY);

        $attributes = array('multiple' => 'multiple', 'size' => 10);
        $form->addElement('select', 'roles', get_string('roleslabel', 'tool_capability'), $roles, $attributes);
        $form->setType('roles', PARAM_TEXT);

        $filters = [];
        $filters[] = $form->createElement('checkbox', 'onlydiff',  get_string('onlydiff', 'tool_capability'));
        $form->setType('onlydiff', PARAM_BOOL);
        $form->addGroup($filters, 'filters', get_string('filters', 'tool_capability'), array('<br>'), false);

        $form->addElement('submit', 'submitbutton', get_string('getreport', 'tool_capability'));
    }

}
