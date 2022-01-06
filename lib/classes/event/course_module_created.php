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
 * Event to be triggered when a new course module is created.
 *
 * @package    core
 * @copyright  2013 Ankit Agarwal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

namespace core\event;
defined('MOODLE_INTERNAL') || die();

/**
 * Class course_module_created
 *
 * Class for event to be triggered when a new course module is created.
 *
 * @property-read array $other {
 *      Extra information about event.
 *
 *      - string modulename: name of module created.
 *      - string name: title of module.
 *      - string instanceid: id of module instance.
 * }
 *
 * @package    core
 * @since      Moodle 2.6
 * @copyright  2013 Ankit Agarwal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */
class course_module_created extends base {

    /**
     * Set basic properties for the event.
     */
    protected function init() {
        $this->data['objecttable'] = 'course_modules';
        $this->data['crud'] = 'c';
        $this->data['edulevel'] = self::LEVEL_TEACHING;
    }

    /**
     * Api to Create new event from course module.
     *
     * @since Moodle 2.6.4, 2.7.1
     * @param \cm_info|\stdClass $cm course module instance, as returned by {@link get_coursemodule_from_id}
     *                               or {@link get_coursemodule_from_instance}.
     * @param \context_module $modcontext module context instance
     *
     * @return \core\event\base returns instance of new event
     */
    public static final function create_from_cm($cm, $modcontext = null) {
        // If not set, get the module context.
        if (empty($modcontext)) {
            $modcontext = \context_module::instance($cm->id);
        }

        // Create event object for course module update action.
        $event = static::create(array(
            'context'  => $modcontext,
            'objectid' => $cm->id,
            'other'    => array(
                'modulename' => $cm->modname,
                'instanceid' => $cm->instance,
                'name'       => $cm->name,
            )
        ));
        return $event;
    }

    /**
     * Returns localised general event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventcoursemodulecreated', 'core');
    }

    /**
     * Returns non-localised event description with id's for admin use only.
     *
     * @return string
     */
    public function get_description() {
        return "The user with id '$this->userid' created the '{$this->other['modulename']}' activity with " .
            "course module id '$this->contextinstanceid'.";
    }

    /**
     * Returns relevant URL.
     * @return \moodle_url
     */
    public function get_url() {
        return new \moodle_url('/mod/' . $this->other['modulename'] . '/view.php', array('id' => $this->objectid));
    }

    /**
     * Legacy event name.
     *
     * @return string legacy event name
     */
    public static function get_legacy_eventname() {
        return 'mod_created';
    }

    /**
     * Legacy event data.
     *
     * @return \stdClass
     */
    protected function get_legacy_eventdata() {
        $eventdata = new \stdClass();
        $eventdata->modulename = $this->other['modulename'];
        $eventdata->name       = $this->other['name'];
        $eventdata->cmid       = $this->objectid;
        $eventdata->courseid   = $this->courseid;
        $eventdata->userid     = $this->userid;
        return $eventdata;
    }

    /**
     * replace add_to_log() statement.
     *
     * @return array of parameters to be passed to legacy add_to_log() function.
     */
    protected function get_legacy_logdata() {
        $log1 = array($this->courseid, "course", "add mod", "../mod/" . $this->other['modulename'] . "/view.php?id=" .
                $this->objectid, $this->other['modulename'] . " " . $this->other['instanceid']);
        $log2 = array($this->courseid, $this->other['modulename'], "add",
            "view.php?id={$this->objectid}",
                "{$this->other['instanceid']}", $this->objectid);
        return array($log1, $log2);
    }

    /**
     * Custom validation.
     *
     * @throw \coding_exception
     */
    protected function validate_data() {
        parent::validate_data();
        if (!isset($this->other['modulename'])) {
            throw new \coding_exception('The \'modulename\' value must be set in other.');
        }
        if (!isset($this->other['instanceid'])) {
            throw new \coding_exception('The \'instanceid\' value must be set in other.');
        }
        if (!isset($this->other['name'])) {
            throw new \coding_exception('The \'name\' value must be set in other.');
        }
    }

    public static function get_objectid_mapping() {
        return array('db' => 'course_modules', 'restore' => 'course_module');
    }

    public static function get_other_mapping() {
        $othermapped = array();
        $othermapped['instanceid'] = base::NOT_MAPPED;

        return $othermapped;
    }
}

