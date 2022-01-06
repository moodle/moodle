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
 * The mform for exporting calendar events
 *
 * @package core_calendar
 * @copyright 2014 Brian Barnes
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Always include formslib.
if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    // It must be included from a Moodle page.
}

require_once($CFG->dirroot.'/lib/formslib.php');

/**
 * The mform class for creating and editing a calendar
 *
 * @copyright 2014 Brian Barnes
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_calendar_export_form extends moodleform {

    /**
     * The export form definition
     * @throws coding_exception
     */
    public function definition() {
        global $CFG, $OUTPUT;
        $mform = $this->_form;

        $mform->addElement('html', $OUTPUT->doc_link('calendar/export', get_string('exporthelp', 'calendar'), true));

        $export = array();
        $export[] = $mform->createElement('radio', 'exportevents', '', get_string('eventsall', 'calendar'), 'all');
        $export[] = $mform->createElement('radio', 'exportevents', '', get_string('eventsrelatedtocategories', 'calendar'), 'categories');
        $export[] = $mform->createElement('radio', 'exportevents', '', get_string('eventsrelatedtocourses', 'calendar'), 'courses');
        $export[] = $mform->createElement('radio', 'exportevents', '', get_string('eventsrelatedtogroups', 'calendar'), 'groups');
        $export[] = $mform->createElement('radio', 'exportevents', '', get_string('eventspersonal', 'calendar'), 'user');

        $mform->addGroup($export, 'events', get_string('eventstoexport', 'calendar'), '<br/>');
        $mform->addGroupRule('events', get_string('required'), 'required');
        $mform->setDefault('events', 'all');

        $range = array();
        if ($this->_customdata['allowthisweek']) {
            $range[] = $mform->createElement('radio', 'timeperiod', '', get_string('weekthis', 'calendar'), 'weeknow');
        }
        if ($this->_customdata['allownextweek']) {
            $range[] = $mform->createElement('radio', 'timeperiod', '', get_string('weeknext', 'calendar'), 'weeknext');
        }
        $range[] = $mform->createElement('radio', 'timeperiod', '', get_string('monththis', 'calendar'), 'monthnow');
        if ($this->_customdata['allownextmonth']) {
            $range[] = $mform->createElement('radio', 'timeperiod', '', get_string('monthnext', 'calendar'), 'monthnext');
        }
        $range[] = $mform->createElement('radio', 'timeperiod', '', get_string('recentupcoming', 'calendar'), 'recentupcoming');

        if ($CFG->calendar_customexport) {
            $a = new stdClass();
            $now = time();
            $time = $now - $CFG->calendar_exportlookback * DAYSECS;
            $a->timestart = userdate($time, get_string('strftimedatefullshort', 'langconfig'));
            $time = $now + $CFG->calendar_exportlookahead * DAYSECS;
            $a->timeend = userdate($time, get_string('strftimedatefullshort', 'langconfig'));

            $range[] = $mform->createElement('radio', 'timeperiod', '', get_string('customexport', 'calendar', $a), 'custom');
        }

        $mform->addGroup($range, 'period', get_string('timeperiod', 'calendar'), '<br/>');
        $mform->addGroupRule('period', get_string('required'), 'required');
        $mform->setDefault('period', 'recentupcoming');

        $buttons = array();
        $buttons[] = $mform->createElement('submit', 'generateurl', get_string('generateurlbutton', 'calendar'));
        $buttons[] = $mform->createElement('submit', 'export', get_string('exportbutton', 'calendar'));
        $mform->addGroup($buttons);
    }
}
