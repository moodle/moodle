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
 * The mform for settings user preferences
 *
 * @copyright 2010 Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package calendar
 */

 /**
  * Always include formslib
  */
if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->dirroot.'/lib/formslib.php');

/**
 * The mform class for setting user preferences
 *
 * @copyright 2010 Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class calendar_preferences_form extends moodleform {

    function definition() {
        $mform = $this->_form;

        $options = array(
            '0'  =>             get_string('default', 'calendar'),
            CALENDAR_TF_12 =>   get_string('timeformat_12', 'calendar'),
            CALENDAR_TF_24 =>   get_string('timeformat_24', 'calendar')
        );
        $mform->addElement('select', 'timeformat', get_string('pref_timeformat', 'calendar'), $options);
        $mform->addHelpButton('timeformat', 'pref_timeformat', 'calendar');

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

        $options = array();
        for ($i=1; $i<=20; $i++) {
            $options[$i] = $i;
        }
        $mform->addElement('select', 'maxevents', get_string('pref_maxevents', 'calendar'), $options);
        $mform->addHelpButton('maxevents', 'pref_maxevents', 'calendar');

        $options = array();
        for ($i=1; $i<=20; $i++) {
            $options[$i] = $i;
        }
        $mform->addElement('select', 'lookahead', get_string('pref_lookahead', 'calendar'), $options);
        $mform->addHelpButton('lookahead', 'pref_lookahead', 'calendar');

        $options = array(
            0 => get_string('no'),
            1 => get_string('yes')
        );
        $mform->addElement('select', 'persistflt', get_string('pref_persistflt', 'calendar'), $options);
        $mform->addHelpButton('persistflt', 'pref_persistflt', 'calendar');

        $this->add_action_buttons(false, get_string('savechanges'));
    }

}