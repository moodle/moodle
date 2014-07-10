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
 * The mform for creating and editing a rule.
 *
 * @copyright 2014 onwards Simey Lameze <lameze@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package   tool_monitor
 */

namespace tool_monitor;

require_once($CFG->dirroot.'/lib/formslib.php');

/**
 * The mform for creating and editing a rule.
 *
 * @since     Moodle 2.8
 * @copyright 2014 onwards Simey Lameze <lameze@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package   tool_monitor
 */
class rule_form extends \moodleform {

    /**
     * Mform class definition
     *
     */
    public function definition () {
        $mform = $this->_form;
        $eventlist = $this->_customdata['eventlist'];
        $pluginlist = $this->_customdata['pluginlist'];
        $rule = $this->_customdata['rule'];
        $courseid = $this->_customdata['courseid'];
        $eventlist = array_merge(array('' => get_string('choosedots')), $eventlist);
        $pluginlist = array_merge(array('' => get_string('choosedots')), $pluginlist);

        // General section header.
        $mform->addElement('header', 'general', get_string('general'));

        // Hidden course ID.
        $mform->addElement('hidden', 'courseid');
        $mform->setType('courseid', PARAM_INT);

        // We are editing a existing rule.
        if (!empty($rule->id)) {
            // Hidden rule id.
            $mform->addElement('hidden', 'ruleid');
            $mform->setType('ruleid', PARAM_INT);
            $mform->setConstant('ruleid', $rule->id);

            // Force course id.
            $courseid = $rule->courseid;
        }

        // Make course id a constant.
        $mform->setConstant('courseid', $courseid);

        if (empty($courseid)) {
            $context = \context_system::instance();
        } else {
            $context = \context_course::instance($courseid);
        }

        $editoroptions = array(
            'subdirs' => 0,
            'maxbytes' => 0,
            'maxfiles' => 0,
            'changeformat' => 0,
            'context' => $context,
            'noclean' => 0,
            'trusttext' => 0
        );

        // Name field.
        $mform->addElement('text', 'name', get_string('name', 'tool_monitor'), 'size="50"');
        $mform->addRule('name', get_string('required'), 'required');
        $mform->setType('name', PARAM_TEXT);
        $mform->addHelpButton('name', 'name', 'tool_monitor');

        // Plugin field.
        $mform->addElement('select', 'plugin', get_string('selectplugin', 'tool_monitor'), $pluginlist);
        $mform->addRule('plugin', get_string('required'), 'required');
        $mform->addHelpButton('plugin', 'selectplugin', 'tool_monitor');

        // Event field.
        $mform->addElement('select', 'eventname', get_string('selectevent', 'tool_monitor'), $eventlist);
        $mform->addRule('eventname', get_string('required'), 'required');
        $mform->addHelpButton('eventname', 'selectevent', 'tool_monitor');

        // Description field.
        $mform->addElement('editor', 'description', get_string('description', 'tool_monitor'), $editoroptions);
        $mform->addHelpButton('description', 'description', 'tool_monitor');

        // Filters.
        $mform->addElement('header', 'customizefilters', get_string('customizefilters', 'tool_monitor'));
        $freq = array(1 => 1, 5 => 5, 10 => 10, 20 => 20, 30 => 30, 40 => 40, 50 => 50, 60 => 60, 70 => 70, 80 => 80, 90 => 90,
                100 => 100, 1000 => 1000);
        $mform->addElement('select', 'frequency', get_string('selectfrequency', 'tool_monitor'), $freq);
        $mform->addRule('frequency', get_string('required'), 'required');
        $mform->addHelpButton('frequency', 'selectfrequency', 'tool_monitor');

        $mins = array(1 => 1, 5 => 5, 10 => 10, 15 => 15, 20 => 20, 25 => 25, 30 => 30, 35 => 35, 40 => 40, 45 => 45, 50 => 50,
                55 => 55,  60 => 60);
        $mform->addElement('select', 'minutes', get_string('selectminutes', 'tool_monitor'), $mins);
        $mform->addRule('minutes', get_string('required'), 'required');

        // Message template.
        $mform->addElement('header', 'customizemessage', get_string('customizemessage', 'tool_monitor'));
        $mform->addElement('editor', 'template', get_string('messagetemplate', 'tool_monitor'), $editoroptions);
        $mform->setDefault('template', get_string('defaultmessagetpl', 'tool_monitor'));
        $mform->addRule('template', get_string('required'), 'required');
        $mform->addHelpButton('template', 'messagetemplate', 'tool_monitor');

        // Action buttons.
        $this->add_action_buttons(false, get_string('savechanges'));
    }

    /**
     * Form validation
     *
     * @param array $data data from the form.
     * @param array $files files uploaded.
     *
     * @return array of errors.
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        if (!eventlist::validate_event_plugin($data['plugin'], $data['eventname'])) {
            $errors['eventname'] = get_string('errorincorrectevent', 'tool_monitor');
        }

        return $errors;
    }
}