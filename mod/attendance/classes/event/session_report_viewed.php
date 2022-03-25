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
 * This file contains an event for when a student's attendance report is viewed.
 *
 * @package    mod_attendance
 * @copyright  2019 Nick Phillips
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_attendance\event;
defined('MOODLE_INTERNAL') || die();

/**
 * Event for when a student's attendance report is viewed.
 *
 * @property-read array $other {
 *      Extra information about event properties.
 *
 *      string studentid Id of student whose attendances were viewed.
 *      string mode Mode of the report viewed.
 * }
 * @package    mod_attendance
 * @copyright  2019 Nick Phillips
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class session_report_viewed extends \core\event\base {

    /**
     * Init method.
     */
    protected function init() {
        $this->data['crud'] = 'r';
        $this->data['edulevel'] = self::LEVEL_TEACHING;
        // Objecttable and objectid can't be meaningfully specified.
    }

    /**
     * Returns non-localised description of what happened.
     *
     * @return string
     */
    public function get_description() {
        return 'User with id ' . $this->userid . ' ' . $this->action . ' attendance sessions for student with id ' .
            $this->relateduserid;
    }

    /**
     * Returns localised general event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventstudentattendancesessionsviewed', 'mod_attendance');
    }

    /**
     * Get URL related to the action
     *
     * @return \moodle_url
     */
    public function get_url() {
        // Mode, groupby, sesscourses are optional.
        $mode = empty($this->other['mode']) ? "" : $this->other['mode'];
        $groupby = empty($this->other['groupby']) ? "" : $this->other['groupby'];
        $sesscourses = empty($this->other['sesscourses']) ? "" : $this->other['sesscourses'];
        return new \moodle_url('/mod/attendance/view.php', array('id' => $this->contextinstanceid,
                                                                 'studentid' => $this->relateduserid,
                                                                 'mode' => $mode,
                                                                 'view' => $this->other['view'],
                                                                 'groupby' => $groupby,
                                                                 'sesscourses' => $sesscourses,
                                                                 'curdate' => $this->other['curdate']));
    }

    /**
     * Replace add_to_log() statement.
     *
     * @return array of parameters to be passed to legacy add_to_log() function.
     */
    protected function get_legacy_logdata() {
        return array($this->courseid, 'attendance', 'student sessions ' . $this->action, $this->get_url(),
            'student id ' . $this->relateduserid, $this->contextinstanceid);
    }

    /**
     * Get objectid mapping
     *
     * @return array of parameters for object mapping.
     */
    public static function get_objectid_mapping() {
        return array();
    }

    /**
     * Get other mapping
     *
     * @return array of parameters for object mapping for objects referenced in 'other' property.
     */
    public static function get_other_mapping() {
        return array();
    }

    /**
     * Custom validation.
     *
     * @throws \coding_exception
     * @return void
     */
    protected function validate_data() {
        if (!isset($this->relateduserid)) {
            throw new \coding_exception('The event ' . $this->eventname . ' must specify relateduserid.');
        }
        // View params can be left out as defaults will be the same when log event is viewed as when
        // it was stored.
        // filter params are important, but stored in session so default effectively unknown,
        // hence required here.
        if (!isset($this->other['view'])) {
            throw new \coding_exception('The event ' . $this->eventname . ' must specify view.');
        }
        if (!isset($this->other['curdate'])) {
            throw new \coding_exception('The event ' . $this->eventname . ' must specify curdate.');
        }
        parent::validate_data();
    }
}
