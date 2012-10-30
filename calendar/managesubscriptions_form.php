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
 * Allows the user to manage calendar subscriptions.
 *
 * @copyright 2012 Jonathan Harker
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package calendar
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->libdir.'/formslib.php');

/**
 * Form for adding a subscription to a Moodle course calendar.
 * @copyright 2012 Jonathan Harker
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class calendar_addsubscription_form extends moodleform {

    /**
     * Defines the form used to add calendar subscriptions.
     */
    public function definition() {
        $mform = $this->_form;
        $courseid = optional_param('course', 0, PARAM_INT);

        $mform->addElement('header', 'addsubscriptionform', get_string('importcalendarheading', 'calendar'));

        // Name.
        $mform->addElement('text', 'name', get_string('subscriptionname', 'calendar'), array('maxsize' => '255', 'size' => '40'));
        $mform->addRule('name', get_string('required'), 'required');
        $mform->setType('name', PARAM_TEXT);

        // Import from (url | importfile).
        $mform->addElement('html', get_string('importfrominstructions', 'calendar'));
        $choices = array(CALENDAR_IMPORT_FROM_FILE => get_string('importfromfile', 'calendar'),
            CALENDAR_IMPORT_FROM_URL  => get_string('importfromurl',  'calendar'));
        $mform->addElement('select', 'importfrom', get_string('importcalendarfrom', 'calendar'), $choices);
        $mform->setDefault('importfrom', CALENDAR_IMPORT_FROM_URL);

        // URL.
        $mform->addElement('text', 'url', get_string('importfromurl', 'calendar'), array('maxsize' => '255', 'size' => '50'));
        $mform->setType('url', PARAM_URL);

        // Import file
        $mform->addElement('filepicker', 'importfile', get_string('importfromfile', 'calendar'));

        $mform->disabledIf('url',  'importfrom', 'eq', CALENDAR_IMPORT_FROM_FILE);
        $mform->disabledIf('importfile', 'importfrom', 'eq', CALENDAR_IMPORT_FROM_URL);

        // Poll interval
        $choices = calendar_get_pollinterval_choices();
        $mform->addElement('select', 'pollinterval', get_string('pollinterval', 'calendar'), $choices);
        $mform->setDefault('pollinterval', 604800);
        $mform->addHelpButton('pollinterval', 'pollinterval', 'calendar');
        $mform->setType('pollinterval', PARAM_INT);

        // Eventtype: 0 = user, 1 = global, anything else = course ID.
        list($choices, $groups) = calendar_get_eventtype_choices($courseid);
        $mform->addElement('select', 'eventtype', get_string('eventkind', 'calendar'), $choices);
        $mform->addRule('eventtype', get_string('required'), 'required');
        $mform->setType('eventtype', PARAM_INT);

        if (!empty($groups) and is_array($groups)) {
            $groupoptions = array();
            foreach ($groups as $group) {
                $groupoptions[$group->id] = $group->name;
            }
            $mform->addElement('select', 'groupid', get_string('typegroup', 'calendar'), $groupoptions);
            $mform->setType('groupid', PARAM_INT);
            $mform->disabledIf('groupid', 'eventtype', 'noteq', 'group');
        }

        $mform->addElement('hidden', 'course');
        $mform->addElement('hidden', 'sesskey', sesskey());
        $mform->addElement('submit', 'add', get_string('add'));
    }

    /**
     * Validates the returned data.
     *
     * @param array $data
     * @param array $files
     * @return array
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);
        if (empty($data['url']) && empty($data['importfile'])) {
            if (!empty($data['importfrom']) && $data['importfrom'] == CALENDAR_IMPORT_FROM_FILE) {
                $errors['importfile'] = get_string('errorrequiredurlorfile', 'calendar');
            } else {
                $errors['url'] = get_string('errorrequiredurlorfile', 'calendar');
            }
        }
        return $errors;
    }
}
