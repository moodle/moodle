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
 * Course completed event.
 *
 * @package    core
 * @copyright  2013 Rajesh Taneja <rajesh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\event;

defined('MOODLE_INTERNAL') || die();

/**
 * Course completed event class.
 *
 * @property-read int $relateduserid user who completed the course
 * @property-read array $other {
 *      Extra information about event.
 *
 *      - int relateduserid: deprecated since 2.7, please use property relateduserid
 * }
 *
 * @package    core
 * @since      Moodle 2.6
 * @copyright  2013 Rajesh Taneja <rajesh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_completed extends base {
    /**
     * Create event from course_completion record.
     * @param \stdClass $completion
     * @return course_completed
     */
    public static function create_from_completion(\stdClass $completion) {
        $event = self::create(
            array(
                'objectid' => $completion->id,
                'relateduserid' => $completion->userid,
                'context' => \context_course::instance($completion->course),
                'courseid' => $completion->course,
                'other' => array('relateduserid' => $completion->userid), // Deprecated since 2.7, please use property relateduserid.
            )
        );
        $event->add_record_snapshot('course_completions', $completion);
        return $event;
    }

    /**
     * Initialise required event data properties.
     */
    protected function init() {
        $this->data['objecttable'] = 'course_completions';
        $this->data['crud'] = 'u';
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
    }

    /**
     * Returns localised event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventcoursecompleted', 'core_completion');
    }

    /**
     * Returns non-localised event description with id's for admin use only.
     *
     * @return string
     */
    public function get_description() {
        return "The user with id '$this->relateduserid' completed the course with id '$this->courseid'.";
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
        return 'course_completed';
    }

    /**
     * Return course_completed legacy event data.
     *
     * @return \stdClass completion data.
     */
    protected function get_legacy_eventdata() {
        return $this->get_record_snapshot('course_completions', $this->objectid);
    }

    /**
     * Custom validation.
     *
     * @throws \coding_exception
     * @return void
     */
    protected function validate_data() {
        parent::validate_data();

        if (!isset($this->relateduserid)) {
            throw new \coding_exception('The \'relateduserid\' must be set.');
        }

        // Check that the 'relateduserid' value is set in other as well. This is because we introduced this in 2.6
        // and some observers may be relying on this value to be present.
        if (!isset($this->other['relateduserid'])) {
            throw new \coding_exception('The \'relateduserid\' value must be set in other.');
        }
    }

    public static function get_objectid_mapping() {
        // Sorry - there is no mapping available for completion records.
        return array('db' => 'course_completions', 'restore' => base::NOT_MAPPED);
    }

    public static function get_other_mapping() {
        $othermapped = array();
        $othermapped['relateduserid'] = array('db' => 'user', 'restore' => 'user');
        return $othermapped;
    }
}
