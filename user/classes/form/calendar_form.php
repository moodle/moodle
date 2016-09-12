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
 * Form to edit a users preferred language
 *
 * @copyright 2015 Shamim Rezaie  http://foodle.org
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package core_user
 */

namespace core_user\form;

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    // It must be included from a Moodle page.
}

require_once($CFG->dirroot.'/lib/formslib.php');

/**
 * Class user_edit_calendar_form.
 *
 * @copyright 2015 Shamim Rezaie  http://foodle.org
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class calendar_form extends \moodleform {

    /**
     * Define the form.
     */
    public function definition () {
        global $CFG, $USER;

        $mform = $this->_form;
        $userid = $USER->id;

        if (is_array($this->_customdata)) {
            if (array_key_exists('userid', $this->_customdata)) {
                $userid = $this->_customdata['userid'];
            }
        }

        // Add some extra hidden fields.
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        // We do not want to show this option unless there is more than one calendar type to display.
        if (count(\core_calendar\type_factory::get_list_of_calendar_types()) > 1) {
            $calendartypes = \core_calendar\type_factory::get_list_of_calendar_types();
            $mform->addElement('select', 'calendartype', get_string('preferredcalendar', 'calendar'), $calendartypes);
            $mform->setType('calendartype', PARAM_ALPHANUM);
            $mform->setDefault('calendartype', $CFG->calendartype);
        } else {
            $mform->addElement('hidden', 'calendartype', $CFG->calendartype);
            $mform->setType('calendartype', PARAM_ALPHANUM);

        }

        // Date / Time settings.
        $options = array(
            '0'  => get_string('default', 'calendar'),
            CALENDAR_TF_12 => get_string('timeformat_12', 'calendar'),
            CALENDAR_TF_24 => get_string('timeformat_24', 'calendar')
        );
        $mform->addElement('select', 'timeformat', get_string('pref_timeformat', 'calendar'), $options);
        $mform->addHelpButton('timeformat', 'pref_timeformat', 'calendar');

        // First day of week.
        $options = array(
            0 => get_string('sunday', 'calendar'),
            1 => get_string('monday', 'calendar'),
            2 => get_string('tuesday', 'calendar'),
            3 => get_string('wednesday', 'calendar'),
            4 => get_string('thursday', 'calendar'),
            5 => get_string('friday', 'calendar'),
            6 => get_string('saturday', 'calendar')
        );
        $mform->addElement('select', 'startwday', get_string('pref_startwday', 'calendar'), $options);
        $mform->addHelpButton('startwday', 'pref_startwday', 'calendar');

        // Maximum events to display.
        $options = array();
        for ($i = 1; $i <= 20; $i++) {
            $options[$i] = $i;
        }
        $mform->addElement('select', 'maxevents', get_string('pref_maxevents', 'calendar'), $options);
        $mform->addHelpButton('maxevents', 'pref_maxevents', 'calendar');

        // Calendar lookahead.
        $options = array(365 => new \lang_string('numyear', '', 1),
                270 => get_string('nummonths', '', 9),
                180 => get_string('nummonths', '', 6),
                150 => get_string('nummonths', '', 5),
                120 => get_string('nummonths', '', 4),
                90  => get_string('nummonths', '', 3),
                60  => get_string('nummonths', '', 2),
                30  => get_string('nummonth', '', 1),
                21  => get_string('numweeks', '', 3),
                14  => get_string('numweeks', '', 2),
                7  => get_string('numweek', '', 1),
                6  => get_string('numdays', '', 6),
                5  => get_string('numdays', '', 5),
                4  => get_string('numdays', '', 4),
                3  => get_string('numdays', '', 3),
                2  => get_string('numdays', '', 2),
                1  => get_string('numday', '', 1));
        $mform->addElement('select', 'lookahead', get_string('pref_lookahead', 'calendar'), $options);
        $mform->addHelpButton('lookahead', 'pref_lookahead', 'calendar');

        // Remember event filtering.
        $options = array(
            0 => get_string('no'),
            1 => get_string('yes')
        );
        $mform->addElement('select', 'persistflt', get_string('pref_persistflt', 'calendar'), $options);
        $mform->addHelpButton('persistflt', 'pref_persistflt', 'calendar');
        $this->add_action_buttons(true, get_string('savechanges'));
    }

    /**
     * Extend the form definition after the data has been parsed.
     */
    public function definition_after_data() {
        global $CFG;

        $mform = $this->_form;

        // If calendar type does not exist, use site default calendar type.
        if ($calendarselected = $mform->getElementValue('calendartype')) {
            if (is_array($calendarselected)) {
                // There are multiple calendar types available.
                $calendar = reset($calendarselected);
            } else {
                // There is only one calendar type available.
                $calendar = $calendarselected;
            }
            // Check calendar type exists.
            if (!array_key_exists($calendar, \core_calendar\type_factory::get_list_of_calendar_types())) {
                $calendartypeel = $mform->getElement('calendartype');
                $calendartypeel->setValue($CFG->calendartype);
            }
        }
    }
}
