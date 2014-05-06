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
 * Grade edited event.
 *
 * @package    core
 * @copyright  2014 Petr Skoda
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\event;
defined('MOODLE_INTERNAL') || die();

/**
 * Event triggered after teacher edits manual grade or
 * overrides activity/aggregated grade.
 *
 * Note: use grade_grades_history table if you need to know
 *       the history of grades.
 *
 * @property-read array $other {
 *      Extra information about the event.
 *
 *      - int itemid: grade item id.
 *      - bool overridden: Is this grade override?
 *      - float finalgrade: the final grade value.
 * }
 *
 * @package    core
 * @since      Moodle 2.7
 * @copyright  2013 Petr Skoda
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_graded extends base {
    /** @var \grade_grade $grade */
    protected $grade;

    /**
     * Utility method to create new event.
     *
     * @param \grade_grade $grade
     * @return user_graded
     */
    public static function create_from_grade(\grade_grade $grade) {
        $event = self::create(array(
            'context'       => \context_course::instance($grade->grade_item->courseid),
            'objectid'      => $grade->id,
            'relateduserid' => $grade->userid,
            'other'         => array(
                'itemid'     => $grade->itemid,
                'overridden' => !empty($grade->overridden),
                'finalgrade' => $grade->finalgrade),
        ));
        $event->grade = $grade;
        return $event;
    }

    /**
     * Get grade object.
     *
     * @return \grade_grade
     */
    public function get_grade() {
        if ($this->is_restored()) {
            throw new \coding_exception('get_grade() is intended for event observers only');
        }
        return $this->grade;
    }

    /**
     * Init method.
     *
     * @return void
     */
    protected function init() {
        $this->data['crud'] = 'u';
        $this->data['edulevel'] = self::LEVEL_TEACHING;
        $this->data['objecttable'] = 'grade_grades';
    }

    /**
     * Return localised event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventusergraded', 'core_grades');
    }

    /**
     * Returns description of what happened.
     *
     * @return string
     */
    public function get_description() {
        return "The user with id '$this->userid' updated the grade with id '$this->objectid' for the user with " .
            "id '$this->relateduserid' for the grade item with id '{$this->other['itemid']}'.";
    }

    /**
     * Get URL related to the action
     *
     * @return \moodle_url
     */
    public function get_url() {
        return new \moodle_url('/grade/edit/tree/grade.php', array(
            'courseid' => $this->courseid,
            'itemid'   => $this->other['itemid'],
            'userid'   => $this->relateduserid,
        ));
    }

    /**
     * Return legacy log info.
     *
     * @return null|array of parameters to be passed to legacy add_to_log() function.
     */
    public function get_legacy_logdata() {
        $user = $this->get_record_snapshot('user', $this->relateduserid);
        $fullname = fullname($user);
        $info = $this->grade->grade_item->itemname . ': ' . $fullname;
        $url = '/report/grader/index.php?id=' . $this->courseid;

        return array($this->courseid, 'grade', 'update', $url, $info);
    }
}
