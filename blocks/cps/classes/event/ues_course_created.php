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
 * The ues_course_created event.
 *
 * @package    block_cps
 * @copyright  2019 Louisiana State University
 * @author     Troy Kammerdiener
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace blocks_cps\event;

defined('MOODLE_INTERNAL') || die();

/**
 * The UES course created event class.
 *
 * @property-read array $other {
 *      Extra information about event.
 *
 *      - string fullname: fullname of course.
 *      - string shortname: (optional) shortname of course.
 * }
 *
 * @since     Moodle 3.7
 * @copyright 2019 Louisiana State University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 **/

class ues_course_created extends \core\event\base {
    protected function init() {
        $this->data['crud'] = 'c'; // c(reate), r(ead), u(pdate), d(elete)
        $this->data['edulevel'] = self::LEVEL_TEACHING;
        $this->data['objecttable'] = 'course';
    }
    /**
     * Returns localised general event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventues_course_created', 'block_cps');
    }

     /**
      * Returns non-localised description of what happened.
      *
      * @return string
      */
    public function get_description() {
        return "The user with id '$this->userid' created the UES course with id '$this->courseid'.";
    }

    /**
     * Returns relevant URL.
     *
     * @return \moodle_url
     */
    public function get_url() {
        return new \moodle_url('/course/view.php', array('id' => $this->objectid));
    }

    /**
     * Returns the name of the legacy event.
     *
     * @return string legacy event name
     */
    public static function get_legacy_eventname() {
        // Override ONLY if you are migrating events_trigger() call.
        return 'ues_course_created';
    }

    /**
     * Returns the legacy event data.
     *
     * @return \stdClass the course that was created
     */
    protected function get_legacy_eventdata() {
        // Override if you migrating events_trigger() call.
        $data = null;
        return $data;
    }
}