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

namespace core\event;

/**
 * Course ended event class.
 *
 * @property-read array $other {
 *      Extra information about event.
 *
 *      - string fullname: fullname of course.
 *      - string shortname: (optional) shortname of course.
 * }
 *
 * @package    core
 * @copyright  2023 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_ended extends base {

    /**
     * Initialise the event data.
     */
    protected function init() {
        $this->data['objecttable'] = 'course';
        $this->data['crud'] = 'u';
        $this->data['edulevel'] = self::LEVEL_TEACHING;
    }

    /**
     * Returns localised general event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventcourseended');
    }

    /**
     * Returns non-localised description of what happened.
     *
     * @return string
     */
    public function get_description() {
        return "The course with id '$this->courseid' has ended.";
    }

    /**
     * Returns relevant URL.
     *
     * @return \moodle_url
     */
    public function get_url() {
        return new \moodle_url('/course/view.php', ['id' => $this->objectid]);
    }

    /**
     * Custom validation.
     *
     * @throws \coding_exception
     * @return void
     */
    protected function validate_data() {
        parent::validate_data();

        if (!isset($this->other['fullname'])) {
            throw new \coding_exception('The \'fullname\' value must be set in other.');
        }
    }

    public static function get_objectid_mapping() {
        return [
            'db' => 'course',
            'restore' => 'course',
        ];
    }

    public static function get_other_mapping() {
        // Nothing to map.
        return [];
    }
}
