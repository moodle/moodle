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
 *      - bool overridden: (optional) Is this grade override?
 *      - float finalgrade: (optional) the final grade value.
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
     * @param null|int $userid Id of user responsible for this event.
     *
     * @return user_graded
     */
    public static function create_from_grade(\grade_grade $grade, $userid = null) {
        $gradedata = array(
            'context'       => \context_course::instance($grade->grade_item->courseid),
            'objectid'      => $grade->id,
            'relateduserid' => $grade->userid,
            'other'         => array(
                'itemid'     => $grade->itemid,
                'overridden' => !empty($grade->overridden),
                'finalgrade' => $grade->finalgrade),
        );
        if ($userid !== null) {
            $gradedata["userid"] = $userid;
        }
        $event = self::create($gradedata);
        $event->grade = $grade;
        return $event;
    }

    /**
     * Get grade object.
     *
     * @throws \coding_exception
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
     * Custom validation.
     *
     * @throws \coding_exception when validation does not pass.
     * @return void
     */
    protected function validate_data() {
        parent::validate_data();

        if (!isset($this->relateduserid)) {
            throw new \coding_exception('The \'relateduserid\' must be set.');
        }

        if (!isset($this->other['itemid'])) {
            throw new \coding_exception('The \'itemid\' value must be set in other.');
        }
    }

    public static function get_objectid_mapping() {
        return array('db' => 'grade_grades', 'restore' => 'grade_grades');
    }

    public static function get_other_mapping() {
        $othermapped = array();
        $othermapped['itemid'] = array('db' => 'grade_items', 'restore' => 'grade_item');

        return $othermapped;
    }
}
