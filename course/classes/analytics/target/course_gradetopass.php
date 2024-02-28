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
 * Getting the minimum grade to pass target.
 *
 * @package   core_course
 * @copyright 2019 Victor Deniz <victor@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_course\analytics\target;

defined('MOODLE_INTERNAL') || die();


/**
 * Getting the minimum grade to pass target.
 *
 * @package   core_course
 * @copyright 2019 Victor Deniz <victor@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_gradetopass extends course_enrolments {

    /**
     * Courses grades to pass.
     * @var mixed[]
     */
    protected $coursesgradetopass = array();

    /**
     * Courses grades.
     * @var mixed[]
     */
    protected $coursesgrades = array();

    /**
     * Returns the grade to pass a course.
     *
     * Save the value in $coursesgradetopass array to prevent new accesses to the database.
     *
     * @param int $courseid The course id.
     * @return array The courseitem id and the required grade to pass the course.
     */
    protected function get_course_gradetopass($courseid) {
        if (!isset($this->coursesgradetopass[$courseid])) {
            // Get course grade_item.
            $courseitem = \grade_item::fetch_course_item($courseid);

            $ci = array();
            $ci['courseitemid'] = $courseitem->id;

            if ($courseitem->gradetype == GRADE_TYPE_VALUE && grade_floats_different($courseitem->gradepass, 0.0)) {
                $ci['gradetopass'] = $courseitem->gradepass;
            } else {
                $ci['gradetopass'] = null;
            }
            $this->coursesgradetopass[$courseid] = $ci;
        }

        return $this->coursesgradetopass[$courseid];
    }

    /**
     * Returns the grade of a user in a course.
     *
     * Saves the grades of all course users in $coursesgrades array to prevent new accesses to the database.
     *
     * @param  int $courseitemid The course item id.
     * @param  int $userid the user whose grade is requested.
     * @return array The courseitem id and the required grade to pass the course.
     */
    protected function get_user_grade($courseitemid, $userid) {
        // If the user grade for this course is not available, get all the grades for the course.
        if (!isset($this->coursesgrades[$courseitemid])) {
            // Ony a course is cached to avoid high memory usage.
            unset($this->coursesgrades);
            $gg = new \grade_grade(null, false);
            $usersgrades = $gg->fetch_all(array('itemid' => $courseitemid));

            if ($usersgrades) {
                foreach ($usersgrades as $ug) {
                    $this->coursesgrades[$courseitemid][$ug->userid] = $ug->finalgrade;
                }
            }
        }

        if (!isset($this->coursesgrades[$courseitemid][$userid])) {
            $this->coursesgrades[$courseitemid][$userid] = null;
        }

        return $this->coursesgrades[$courseitemid][$userid];
    }

    /**
     * Returns the name.
     *
     * If there is a corresponding '_help' string this will be shown as well.
     *
     * @return \lang_string
     */
    public static function get_name(): \lang_string {
        return new \lang_string('target:coursegradetopass', 'course');
    }

    /**
     * Returns descriptions for each of the values the target calculation can return.
     *
     * @return string[]
     */
    protected static function classes_description() {
        return array(
            get_string('targetlabelstudentgradetopassno', 'course'),
            get_string('targetlabelstudentgradetopassyes', 'course')
        );
    }

    /**
     * Discards courses that are not yet ready to be used for training or prediction.
     *
     * Only courses with "value" grade type and grade to pass set are valid.
     *
     * @param \core_analytics\analysable $course
     * @param bool $fortraining
     * @return true|string
     */
    public function is_valid_analysable(\core_analytics\analysable $course, $fortraining = true) {
        $isvalid = parent::is_valid_analysable($course, $fortraining);

        if (is_string($isvalid)) {
            return $isvalid;
        }

        $courseitem = $this->get_course_gradetopass ($course->get_id());
        if (is_null($courseitem['gradetopass'])) {
            return get_string('gradetopassnotset', 'course');
        }

        return true;
    }

    /**
     * The user's grade in the course sets the target value.
     *
     * @param int $sampleid
     * @param \core_analytics\analysable $course
     * @param int $starttime
     * @param int $endtime
     * @return float|null 0 -> course grade to pass achieved, 1 -> course grade to pass not achieved
     */
    protected function calculate_sample($sampleid, \core_analytics\analysable $course, $starttime = false, $endtime = false) {

        if (!$this->enrolment_active_during_analysis_time($sampleid, $starttime, $endtime)) {
            // We should not use this sample as the analysis results could be misleading.
            return null;
        }

        $userenrol = $this->retrieve('user_enrolments', $sampleid);

        // Get course grade to pass.
        $courseitem = $this->get_course_gradetopass($course->get_id());

        // Get the user grade.
        $usergrade = $this->get_user_grade($courseitem['courseitemid'], $userenrol->userid);

        if ($usergrade >= $courseitem['gradetopass']) {
            return 0;
        }

        return 1;
    }
}
