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
 * Course completion target.
 *
 * @package   core_course
 * @copyright 2019 Victor Deniz <victor@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_course\analytics\target;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->dirroot . '/lib/completionlib.php');
require_once($CFG->dirroot . '/completion/completion_completion.php');

/**
 * Course completion target.
 *
 * @package   core_course
 * @copyright 2019 Victor Deniz <victor@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_completion extends course_enrolments {

    /**
     * Returns the name.
     *
     * If there is a corresponding '_help' string this will be shown as well.
     *
     * @return \lang_string
     */
    public static function get_name() : \lang_string {
        return new \lang_string('target:coursecompletion', 'course');
    }

    /**
     * Returns descriptions for each of the values the target calculation can return.
     *
     * @return string[]
     */
    protected static function classes_description() {
        return array(
            get_string('targetlabelstudentcompletionno', 'course'),
            get_string('targetlabelstudentcompletionyes', 'course')
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

        // Not a valid target if completion is not enabled or there are not completion criteria defined.
        $completion = new \completion_info($course->get_course_data());
        if (!$completion->is_enabled() || !$completion->has_criteria()) {
            return get_string('completionnotenabledforcourse', 'completion');
        }

        return true;
    }

    /**
     * Course completion sets the target value.
     *
     * @param int $sampleid
     * @param \core_analytics\analysable $course
     * @param int $starttime
     * @param int $endtime
     * @return float|null 0 -> course not completed, 1 -> course completed
     */
    protected function calculate_sample($sampleid, \core_analytics\analysable $course, $starttime = false, $endtime = false) {

        if (!$this->enrolment_active_during_analysis_time($sampleid, $starttime, $endtime)) {
            // We should not use this sample as the analysis results could be misleading.
            return null;
        }

        $userenrol = $this->retrieve('user_enrolments', $sampleid);

        // We use completion as a success metric.
        $ccompletion = new \completion_completion(array('userid' => $userenrol->userid, 'course' => $course->get_id()));
        if ($ccompletion->is_complete()) {
            return 0;
        } else {
            return 1;
        }
    }
}
