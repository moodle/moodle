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
 * mod/hotpot/classes/event/attempt_started.php
 *
 * @package    mod_hotpot
 * @copyright  2014 Gordon Bateson (gordon.bateson@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 2.6
 */

namespace mod_hotpot\event;

/** prevent direct access to this script */
defined('MOODLE_INTERNAL') || die();

/**
 * The attempt_started event class.
 *
 * @package    mod_hotpot
 * @copyright  2014 Gordon Bateson (gordon.bateson@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 2.6
 */
abstract class base extends \core\event\base {

    /**
     * Pattern to match names of legacy functions.
     * This is used to hide legacy functions and so prevent error
     * messages in Moodle >= 4.2 which complains about the presence of
     * [get|set]_legacy_logdata and get_legacy_[event|data].
     */
    const LEGACY_FUNCTIONS = '/(get|set)_legacy_(logdata|eventdata|eventname|records)/';

    /**
     * Magic call function which hides the legacy functions.
     */
    public function __call($name, $args) {
        if (preg_match(self::LEGACY_FUNCTIONS, $name)) {;
            $name = 'my_'.$name;
        }
        return call_user_func_array([$this, $name], $args);
    }

    /**
     * Magic callStatic function to hide legacy functions.
     */
    public static function __callStatic($name, $args) {
        if (preg_match(self::LEGACY_FUNCTIONS, $name)) {;
            $name = 'my_'.$name;
        }
        return call_user_func_array([__CLASS__, $name], $args);
    }

    /**
     * Returns the name of an language string for this event
     *
     * @param string $suffix (optional, default="")
     * @return string
     */
    public static function get_event_string_name($suffix='') {
        $class = get_called_class();
        $class = substr($class, strlen(__NAMESPACE__) + 1);
        return 'event_'.$class.$suffix;
    }

    /**
     * Returns localised event name
     *
     * @return string
     */
    public static function get_name() {
        return get_string(self::get_event_string_name(), 'mod_hotpot');
    }

    /**
     * Returns non-localised event description with id's for admin use only.
     *
     * @return string
     */
    public function get_description() {
        if ($this->contextlevel==CONTEXT_MODULE) {
            $cmid = $this->contextinstanceid;
        } else {
            $cmid = 0; // shouldn't happen !!
        }
        $a = (object)array('courseid'      => $this->courseid,
                           'cmid'          => $cmid,
                           'objectid'      => $this->objectid,
                           'objecttable'   => $this->objecttable,
                           'userid'        => $this->userid,
                           'relateduserid' => $this->relateduserid,
                           'other'         => $this->other);
        return get_string(self::get_event_string_name('_description'), 'mod_hotpot', $a);
    }

    /**
     * Get an explanation of what the class does.
     *
     * @return string An explanation of the event formatted in markdown style.
     */
    public static function get_explanation() {
        return get_string(self::get_event_string_name('_explanation'), 'mod_hotpot');
    }

    /**
     * Returns relevant URL
     *
     * @return \moodle_url
     */
    public function get_url() {
        $params = array();
        foreach ($this->my_get_legacy_records() as $record) {
            list($name, $table, $id) = $record;
            if ($name=='course' || $name=='hotpot') {
                continue; // not required in the URL
            }
            if ($name=='cm') {
                $params['id'] = $id;
            } else {
                $params[$name.'id'] = $id;
            }
        }
        return new \moodle_url('/mod/hotpot/view.php', $params);
    }

    /**
     * Return the legacy event log data
     *
     * @return array
     */
    protected function my_get_legacy_logdata() {
        $name = $this->my_get_legacy_eventname();
        $url = preg_replace('/^.*\/mod\/hotpot\//', '', $this->get_url());
        return array($this->courseid, 'hotpot', $name, $url, $this->objectid, $this->contextinstanceid);
    }

    /**
     * Legacy event data if my_get_legacy_eventname() is not empty.
     *
     * @return mixed
     */
    protected function my_get_legacy_eventdata() {
        global $USER;
        $eventdata = (object)array('user' => $USER);
        foreach ($this->my_get_legacy_records() as $record) {
            list($name, $table, $id) = $record;
            $eventdata->$name = $this->get_record_snapshot($table, $id);
        }
        return $eventdata;
    }

   /**
     * Records required by my_get_legacy_eventdata
     *
     * @return array(array($name, $table, $id), ...)
     */
    protected function my_get_legacy_records() {
        return array(array('course', 'course',         $this->courseid),
                     array('cm',     'course_modules', $this->contextinstanceid),
                     array('hotpot', 'hotpot',         $this->objectid));
    }

    /**
     * Custom validation
     *
     * @throws \coding_exception
     * @return void
     */
    protected function validate_data() {
        parent::validate_data();
    }
}
