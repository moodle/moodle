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
 * Course viewed event.
 *
 * @package    core
 * @copyright  2014 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\event;
defined('MOODLE_INTERNAL') || die();

/**
 * Course viewed event class.
 *
 * Class for event to be triggered when a course is viewed.
 *
 * @property-read array $other {
 *      Extra information about the event.
 *
 *      - int coursesectionid: (optional) The course section ID.
 * }
 *
 * @package    core
 * @since      Moodle 2.7
 * @copyright  2014 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_viewed extends base {

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

        $sectionstr = '';
        if (!empty($this->other['coursesectionid'])) {
            $sectionstr = "section number '{$this->other['coursesectionid']}' of the ";
        }
        $description = "The user with id '$this->userid' viewed the " . $sectionstr . "course with id '$this->courseid'.";
        return $description;
    }

    /**
     * Return localised event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventcourseviewed', 'core');
    }

    /**
     * Get URL related to the action.
     *
     * @return \moodle_url|null
     */
    public function get_url() {
        global $CFG;
        $sectionid = null;
        if (isset($this->other['coursesectionid'])) {
            $sectionid = $this->other['coursesectionid'];
        }
        require_once($CFG->dirroot . '/course/lib.php');
        try {
            return course_get_url($this->courseid, $sectionid);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Return the legacy event log data.
     *
     * @return array|null
     */
    protected function get_legacy_logdata() {
        if ($this->courseid == SITEID and !isloggedin()) {
            // We did not log frontpage access in older Moodle versions.
            return null;
        }

        if (isset($this->other['coursesectionid'])) {
            return array($this->courseid, 'course', 'view section', 'view.php?id=' . $this->courseid . '&amp;section='
                    . $this->other['coursesectionid'], $this->other['coursesectionid']);
        }
        return array($this->courseid, 'course', 'view', 'view.php?id=' . $this->courseid, $this->courseid);
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
            throw new \coding_exception('Context level must be CONTEXT_COURSE.');
        }
    }
}
