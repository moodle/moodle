<?php
abstract class calendarsystem_plugin_base
{
	public abstract function calendar_days_in_month($m, $y);
	public abstract function usergetdate($time, $timezone=99);
	public abstract function checkdate($m, $d, $y);
	public abstract function make_timestamp($year, $month=1, $day=1, $hour=0, $minute=0, $second=0, $timezone=99, $applydst=true);
	public abstract function userdate($date, $format='', $timezone=99, $fixday = true, $fixhour = true);
	public abstract function today();
	public abstract function get_month_names();
	public abstract function get_min_year();
	public abstract function get_max_year();
	public abstract function gmmktime($hour=null, $minute=null, $second=null, $month=null, $day=null, $year=null);
	public abstract function mktime($hour=null, $minute=null, $second=null, $month=null, $day=null, $year=null);
	public abstract function dayofweek($day, $month, $year);
}

/**
* calendarsystem_plugin_factory is used to "manufacture" an instance of required calendar system.
*/

class calendarsystem_plugin_factory {
    static function factory($system = '') {
        global $CFG;
        if (!$system) {
            $system = current_calendarsystem_plugin();
            // empty($CFG->calendarsystem) ? 'gregorian' : $CFG->calendarsystem; // we might be in the installation process and $CFG->calendarststem might be undefined yet
        }
        if (file_exists("$CFG->dirroot/calendarsystem/$system/calendarsystem.php")) {
            require_once("$CFG->dirroot/calendarsystem/$system/calendarsystem.php");
            $class = "calendarsystem_plugin_$system";
            return new $class;
        } else {
            trigger_error("$CFG->dirroot/calendarsystem/$system/calendarsystem.php does not exist");
            notify("Calendar system file $system/calendarsystem.php does not exist");
        }
    }
}

function get_list_of_calendars() {
    $calendars = array();
    $calendardirs = get_list_of_plugins('calendarsystem');

    foreach ($calendardirs as $calendar) {
        $calendars[$calendar] = get_string('name', "calendarsystem_{$calendar}");
    }

    return $calendars;
}

function current_calendarsystem_plugin() {
    global $CFG, $USER, $SESSION, $COURSE;

    if (!empty($COURSE->id) and $COURSE->id != SITEID and !empty($COURSE->calendarsystem)) {    // Course calendarsystem can override all other settings for this page
        $return = $COURSE->calendarsystem;

    } else if (!empty($SESSION->calendarsystem)) {    // Session calendarsystem can override other settings
        $return = $SESSION->calendarsystem;

    } else if (!empty($USER->calendarsystem)) {
        $return = $USER->calendarsystem;

    } else {
        $return = $CFG->calendarsystem;
    }

    return $return;
}
?>