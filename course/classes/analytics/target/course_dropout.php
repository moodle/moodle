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
 * Drop out course target.
 *
 * @package   core_course
 * @copyright 2016 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_course\analytics\target;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->dirroot . '/lib/gradelib.php');
require_once($CFG->dirroot . '/lib/completionlib.php');
require_once($CFG->dirroot . '/completion/completion_completion.php');

/**
 * Drop out course target.
 *
 * @package   core_course
 * @copyright 2016 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_dropout extends course_enrolments {

    /**
     * Returns the name.
     *
     * If there is a corresponding '_help' string this will be shown as well.
     *
     * @return \lang_string
     */
    public static function get_name() : \lang_string {
        return new \lang_string('target:coursedropout', 'course');
    }

    /**
     * classes_description
     *
     * @return string[]
     */
    protected static function classes_description() {
        return array(
            get_string('targetlabelstudentdropoutno', 'course'),
            get_string('targetlabelstudentdropoutyes', 'course')
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
        global $DB;

        $isvalid = parent::is_valid_analysable($course, $fortraining);

        if (is_string($isvalid)) {
            return $isvalid;
        }

        if ($fortraining) {
            // Not a valid target for training if there are not enough course accesses between the course start and end dates.
            $params = array('courseid' => $course->get_id(), 'anonymous' => 0, 'start' => $course->get_start(),
                'end' => $course->get_end());
            list($studentssql, $studentparams) = $DB->get_in_or_equal($this->students, SQL_PARAMS_NAMED);
            // Using anonymous to use the db index, not filtering by timecreated to speed it up.
            $select = 'courseid = :courseid AND anonymous = :anonymous AND timecreated > :start AND timecreated < :end ' .
                'AND userid ' . $studentssql;

            if (!$logstore = \core_analytics\manager::get_analytics_logstore()) {
                throw new \coding_exception('No available log stores');
            }
            $nlogs = $logstore->get_events_select_count($select, array_merge($params, $studentparams));

            // At least a minimum of students activity.
            $nstudents = count($this->students);
            if ($nlogs / $nstudents < 10) {
                return get_string('nocourseactivity', 'course');
            }
        }

        return true;
    }

    /**
     * calculate_sample
     *
     * The meaning of a drop out changes depending on the settings enabled in the course. Following these priorities order:
     * 1.- Course completion
     * 2.- No logs during the last quarter of the course
     *
     * @param int $sampleid
     * @param \core_analytics\analysable $course
     * @param int $starttime
     * @param int $endtime
     * @return float 0 -> not at risk, 1 -> at risk
     */
    protected function calculate_sample($sampleid, \core_analytics\analysable $course, $starttime = false, $endtime = false) {

        $userenrol = $this->retrieve('user_enrolments', $sampleid);

        // We use completion as a success metric only when it is enabled.
        $completion = new \completion_info($course->get_course_data());
        if ($completion->is_enabled() && $completion->has_criteria()) {
            $ccompletion = new \completion_completion(array('userid' => $userenrol->userid, 'course' => $course->get_id()));
            if ($ccompletion->is_complete()) {
                return 0;
            } else {
                return 1;
            }
        }

        if (!$logstore = \core_analytics\manager::get_analytics_logstore()) {
            throw new \coding_exception('No available log stores');
        }

        // No logs during the last quarter of the course.
        $courseduration = $course->get_end() - $course->get_start();
        $limit = intval($course->get_end() - ($courseduration / 4));
        $select = "courseid = :courseid AND userid = :userid AND timecreated > :limit";
        $params = array('userid' => $userenrol->userid, 'courseid' => $course->get_id(), 'limit' => $limit);
        $nlogs = $logstore->get_events_select_count($select, $params);
        if ($nlogs == 0) {
            return 1;
        }
        return 0;
    }
}
