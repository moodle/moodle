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
 * Course module completion event.
 *
 * @package    core
 * @copyright  2013 Rajesh Taneja <rajesh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\event;

defined('MOODLE_INTERNAL') || die();

/**
 * Course module completion event class.
 *
 * @property-read array $other {
 *      Extra information about event.
 *
 *      - int relateduserid: (optional) the related user id.
 * }
 *
 * @package    core
 * @since      Moodle 2.6
 * @copyright  2013 Rajesh Taneja <rajesh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_module_completion_updated extends base {

    /**
     * Initialise required event data properties.
     */
    protected function init() {
        $this->data['objecttable'] = 'course_modules_completion';
        $this->data['crud'] = 'u';
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
    }

    /**
     * Returns localised event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventcoursemodulecompletionupdated', 'core_completion');
    }

    /**
     * Returns non-localised event description with id's for admin use only.
     *
     * @return string
     */
    public function get_description() {
        if (isset($this->other['overrideby']) && $this->other['overrideby']) {
            return "The user with id '{$this->userid}' overrode the completion state to '{$this->other['completionstate']}' ".
                "for the course module with id '{$this->contextinstanceid}' for the user with id '{$this->relateduserid}'.";
        } else {
            return "The user with id '{$this->userid}' updated the completion state for the course module with id " .
                "'{$this->contextinstanceid}' for the user with id '{$this->relateduserid}'.";
        }
    }

    /**
     * Returns relevant URL.
     *
     * @return \moodle_url
     */
    public function get_url() {
        return new \moodle_url('/report/completion/index.php', array('course' => $this->courseid));
    }

    /**
     * Return name of the legacy event, which is replaced by this event.
     *
     * @return string legacy event name
     */
    public static function get_legacy_eventname() {
        return 'activity_completion_changed';
    }

    /**
     * Return course module completion legacy event data.
     *
     * @return \stdClass completion data.
     */
    protected function get_legacy_eventdata() {
        return $this->get_record_snapshot('course_modules_completion', $this->objectid);
    }

    /**
     * Custom validation.
     *
     * @throws \coding_exception in case of a problem.
     */
    protected function validate_data() {
        parent::validate_data();
        // Make sure the context level is set to module.
        if ($this->contextlevel !== CONTEXT_MODULE) {
            throw new \coding_exception('Context level must be CONTEXT_MODULE.');
        }

        if (!isset($this->relateduserid)) {
            throw new \coding_exception('The \'relateduserid\' must be set.');
        }
    }

    public static function get_objectid_mapping() {
        // Sorry mapping info is not available for course modules completion records.
        return array('db' => 'course_modules_completion', 'restore' => base::NOT_MAPPED);
    }

    public static function get_other_mapping() {
        $othermapped = array();
        $othermapped['relateduserid'] = array('db' => 'user', 'restore' => 'user');
        $othermapped['overrideby'] = array('db' => 'user', 'restore' => 'user');

        return $othermapped;
    }
}
