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
 *
 * @package core_calendar
 * @copyright 2008 onwards Foodle Group {@link http://foodle.org}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class type_factory {

    /**
     * Returns an instance of the currently used calendar type.
     *
     * @param string|null $type the calendar type to use, if none provided use logic to determine
     * @return \core_calendar\type_base the created calendar_type class
     * @throws \coding_exception if the calendar type file could not be loaded
     */
    public static function get_calendar_instance($type = null) {
        if (is_null($type)) {
            $type = self::get_calendar_type();
        }

        $class = "\\calendartype_$type\\structure";

        // Ensure the calendar type exists. It may occur that a user has selected a calendar type, which was then
        // deleted. If this happens we want to fall back on the Gregorian calendar type.
        if (!class_exists($class)) {
            $class = "\\calendartype_gregorian\\structure";
        }

        return new $class();
    }

    /**
     * Returns a list of calendar typess available for use.
     *
     * @return array the list of calendar types
     */
    public static function get_list_of_calendar_types() {
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
    public static function get_calendar_type() {
        global $CFG, $USER, $SESSION, $COURSE;

        // Course calendartype can override all other settings for this page.
        if (!empty($COURSE->id) and $COURSE->id != SITEID and !empty($COURSE->calendartype)) {
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
