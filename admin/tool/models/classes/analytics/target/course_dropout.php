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
 * @package   tool_models
 * @copyright 2016 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_models\analytics\target;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->dirroot . '/lib/gradelib.php');
require_once($CFG->dirroot . '/lib/completionlib.php');
require_once($CFG->dirroot . '/completion/completion_completion.php');

/**
 * Drop out course target.
 *
 * @package   tool_models
 * @copyright 2016 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_dropout extends \core_analytics\local\target\binary {

    protected $coursegradeitem = null;

    public static function get_name() {
        return get_string('target:coursedropout', 'tool_models');
    }

    public function prediction_actions(\core_analytics\prediction $prediction, $includedetailsaction = false) {
        global $USER;

        $actions = parent::prediction_actions($prediction, $includedetailsaction);

        $sampledata = $prediction->get_sample_data();
        $studentid = $sampledata['user']->id;

        // Send a message.
        $url = new \moodle_url('/message/index.php', array('user' => $USER->id, 'id' => $studentid));
        $pix = new \pix_icon('t/message', get_string('sendmessage', 'message'));
        $actions['studentmessage'] = new \core_analytics\prediction_action('studentmessage', $prediction, $url, $pix, get_string('sendmessage', 'message'));

        // View outline report.
        $url = new \moodle_url('/report/outline/user.php', array('id' => $studentid, 'course' => $sampledata['course']->id,
            'mode' => 'outline'));
        $pix = new \pix_icon('i/report', get_string('outlinereport'));
        $actions['viewoutlinereport'] = new \core_analytics\prediction_action('viewoutlinereport', $prediction, $url, $pix, get_string('outlinereport'));

        return $actions;
    }

    protected static function classes_description() {
        return array(
            get_string('labelstudentdropoutno', 'tool_models'),
            get_string('labelstudentdropoutyes', 'tool_models')
        );
    }

    /**
     * Returns the predicted classes that will be ignored.
     *
     * Overwriten because we are also interested in knowing when the student is far from the risk of dropping out.
     *
     * @return array
     */
    protected function ignored_predicted_classes() {
        return array();
    }

    public function get_analyser_class() {
        return '\\core_analytics\\local\\analyser\\student_enrolments';
    }

    public function is_valid_analysable(\core_analytics\analysable $course, $fortraining = true) {
        global $DB;

        if (!$course->was_started()) {
            return get_string('coursenotyetstarted', 'tool_models');
        }

        if (!$students = $course->get_students()) {
            return get_string('nocoursestudents', 'tool_models');
        }

        if (!course_format_uses_sections($course->get_course_data()->format)) {
            // We can not split activities in time ranges.
            return get_string('nocoursesections', 'tool_models');
        }

        if ($course->get_end() == 0) {
            // We require time end to be set.
            return get_string('nocourseendtime', 'tool_models');
        }

        // Ongoing courses data can not be used to train.
        if ($fortraining && !$course->is_finished()) {
            return get_string('coursenotyetfinished', 'tool_models');
        }

        if ($fortraining) {
            // Not a valid target for training if there are not enough course accesses.

            $params = array('courseid' => $course->get_id(), 'anonymous' => 0, 'start' => $course->get_start(),
                'end' => $course->get_end());
            list($studentssql, $studentparams) = $DB->get_in_or_equal($students, SQL_PARAMS_NAMED);
            // Using anonymous to use the db index, not filtering by timecreated to speed it up.
            $select = 'courseid = :courseid AND anonymous = :anonymous AND timecreated > :start AND timecreated < :end ' .
                'AND userid ' . $studentssql;

            $logstore = \core_analytics\manager::get_analytics_logstore();
            $nlogs = $logstore->get_events_select_count($select, array_merge($params, $studentparams));

            // At least a minimum of students activity.
            $nstudents = count($students);
            if ($nlogs / $nstudents < 10) {
                return get_string('nocourseactivity', 'tool_models');
            }
        }

        return true;
    }

    /**
     * All student enrolments are valid.
     *
     * @param int $sampleid
     * @param \core_analytics\analysable $course
     * @param bool $fortraining
     * @return bool
     */
    public function is_valid_sample($sampleid, \core_analytics\analysable $course, $fortraining = true) {
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
     * @return void
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

        // No logs during the last quarter of the course.
        $courseduration = $course->get_end() - $course->get_start();
        $limit = intval($course->get_end() - ($courseduration / 4));
        $select = "courseid = :courseid AND userid = :userid AND timecreated > :limit";
        $params = array('userid' => $userenrol->userid, 'courseid' => $course->get_id(), 'limit' => $limit);
        $logstore = \core_analytics\manager::get_analytics_logstore();
        $nlogs = $logstore->get_events_select_count($select, $params);
        if ($nlogs == 0) {
            return 0;
        }
        return 1;
    }
}
