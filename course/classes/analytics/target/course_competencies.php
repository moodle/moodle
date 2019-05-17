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
 * Course competencies achievement target.
 *
 * @package   core_course
 * @copyright 2019 Victor Deniz <victor@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_course\analytics\target;

defined('MOODLE_INTERNAL') || die();

/**
 * Course competencies achievement target.
 *
 * @package   core_course
 * @copyright 2019 Victor Deniz <victor@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_competencies extends course_enrolments {

    /**
     * Number of competencies assigned per course.
     * @var int[]
     */
    protected $coursecompetencies = array();

    /**
     * Count the competencies in a course.
     *
     * Save the value in $coursecompetencies array to prevent new accesses to the database.
     *
     * @param int $courseid The course id.
     * @return int Number of competencies assigned to the course.
     */
    protected function get_num_competencies_in_course ($courseid) {

        if (!isset($this->coursecompetencies[$courseid])) {
            $ccs = \core_competency\api::count_competencies_in_course($courseid);
            // Save the number of competencies per course to avoid another database access in calculate_sample().
            $this->coursecompetencies[$courseid] = $ccs;
        } else {
            $ccs = $this->coursecompetencies[$courseid];
        }
        return $ccs;
    }

    /**
     * Returns the name.
     *
     * If there is a corresponding '_help' string this will be shown as well.
     *
     * @return \lang_string
     */
    public static function get_name() : \lang_string {
        return new \lang_string('target:coursecompetencies', 'course');
    }

    /**
     * Returns descriptions for each of the values the target calculation can return.
     *
     * @return string[]
     */
    protected static function classes_description() {
        return array(
            get_string('targetlabelstudentcompetenciesno', 'course'),
            get_string('targetlabelstudentcompetenciesyes', 'course'),
        );
    }

    /**
     * Discards courses that are not yet ready to be used for training or prediction.
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

        $ccs = $this->get_num_competencies_in_course($course->get_id());

        if (!$ccs) {
            return get_string('nocompetenciesincourse', 'tool_lp');
        }

        return true;
    }

    /**
     * To have the proficiency or not in each of the competencies assigned to the course sets the target value.
     *
     * @param int $sampleid
     * @param \core_analytics\analysable $course
     * @param int $starttime
     * @param int $endtime
     * @return float 0 -> competencies achieved, 1 -> competencies not achieved
     */
    protected function calculate_sample($sampleid, \core_analytics\analysable $course, $starttime = false, $endtime = false) {

        if ($this->enrolment_starts_after_calculation_start($sampleid, $starttime)) {
            // Discard user enrolments whose start date is after $starttime.
            return null;
        }

        $userenrol = $this->retrieve('user_enrolments', $sampleid);

        $key = $course->get_id();
        // Number of competencies in the course.
        $ccs = $this->get_num_competencies_in_course($key);
        // Number of proficient competencies in the same course for the user.
        $ucs = \core_competency\api::count_proficient_competencies_in_course_for_user($key, $userenrol->userid);

        // If they are the equals, the user achieved all the competencies assigned to the course.
        if ($ccs == $ucs) {
            return 0;
        }

        return 1;
    }
}
