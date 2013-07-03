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

namespace core_calendar;

/**
 * Class \core_calendar\type_factory.
 *
 * Factory class producing required subclasses of {@link \core_calendar\type_base}.
 */
class type_factory {

    /**
     * Returns an instance of the currently used calendar type.
     *
     * @param string|null $type the calendar type to use, if none provided use logic to determine
     * @return calendar_type_plugin_* the created calendar_type class
     * @throws coding_exception if the calendar type file could not be loaded
     */
    static function factory($type = null) {
        if (is_null($type)) {
            $type = self::get_calendar_type();
        }

        $class = "\\calendartype_$type\\structure";
        return new $class();
    }

    /**
     * Returns a list of calendar typess available for use.
     *
     * @return array the list of calendar types
     */
    static function get_list_of_calendar_types() {
        $calendars = array();
        $calendardirs = \core_component::get_plugin_list('calendartype');

        foreach ($calendardirs as $name => $location) {
            $calendars[$name] = get_string('name', "calendartype_{$name}");
        }

        return $calendars;
    }

    /**
     * Returns the current calendar type in use.
     *
     * @return string the current calendar type being used
     */
    static function get_calendar_type() {
        global $CFG, $USER, $SESSION, $COURSE;

        if (!empty($COURSE->id) and $COURSE->id != SITEID and !empty($COURSE->calendartype)) { // Course calendartype can override all other settings for this page.
            $return = $COURSE->calendartype;
        } else if (!empty($SESSION->calendartype)) { // Session calendartype can override other settings.
            $return = $SESSION->calendartype;
        } else if (!empty($USER->calendartype)) {
            $return = $USER->calendartype;
        } else if (!empty($CFG->calendartype)) {
            $return = $CFG->calendartype;
        } else {
            $return = 'gregorian';
        }

        return $return;
    }
}