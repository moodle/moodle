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
 * Course user report viewed event.
 *
 * @package    core
 * @copyright  2014 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\event;
defined('MOODLE_INTERNAL') || die();

/**
 * Course user report viewed event class.
 *
 * Class for event to be triggered when a course user report is viewed.
 *
 * @property-read array $other {
 *      Extra information about the event.
 *
 *      - string mode: Mode is used to show the user different data.
 * }
 *
 * @package    core
 * @since      Moodle 2.7
 * @copyright  2014 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_user_report_viewed extends \core\event\base {

    /**
     * Init method.
     *
     * @return void
     */
    protected function init() {
        $this->data['crud'] = 'r';
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
    }

    /**
     * Returns description of what happened.
     *
     * @return string
     */
    public function get_description() {
        return "The user with the id '$this->userid' viewed the user report for the course with the id '$this->courseid' " .
            "for user with the id '$this->relateduserid'.";
    }

    /**
     * Return localised event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventcourseuserreportviewed', 'core');
    }

    /**
     * Get URL related to the action.
     *
     * @return \moodle_url
     */
    public function get_url() {
        return new \moodle_url("/course/user.php", array('id' => $this->courseid, 'user' => $this->relateduserid,
                'mode' => $this->other['mode']));
    }

    /**
     * Return the legacy event log data.
     *
     * @return array|null
     */
    protected function get_legacy_logdata() {
        return array($this->courseid, 'course', 'user report', 'user.php?id=' . $this->courseid . '&amp;user='
                . $this->relateduserid . '&amp;mode=' . $this->other['mode'], $this->relateduserid);
    }

    /**
     * Custom validation.
     *
     * @throws \coding_exception
     * @return void
     */
    protected function validate_data() {
        parent::validate_data();

        if ($this->contextlevel != CONTEXT_COURSE) {
            throw new \coding_exception('Context passed must be course context.');
        }

        if (empty($this->relateduserid)) {
            throw new \coding_exception('relateduserid needs to be set.');
        }

        // Make sure this class is never used without proper object details.
        if (!isset($this->other['mode'])) {
            throw new \coding_exception('mode needs to be set in $other.');
        }
    }
}
