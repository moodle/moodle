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
 * Base class for targets whose analysable is a course using user enrolments as samples.
 *
 * @package   core_course
 * @copyright 2019 Victor Deniz <victor@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_course\analytics\target;

defined('MOODLE_INTERNAL') || die();

/**
 * Base class for targets whose analysable is a course using user enrolments as samples.
 *
 * @package   core_course
 * @copyright 2019 Victor Deniz <victor@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class course_enrolments extends \core_analytics\local\target\binary {

    /**
     * @var string
     */
    const MESSAGE_ACTION_NAME = 'studentmessage';

    /**
     * @var float
     */
    const ENROL_ACTIVE_PERCENT_REQUIRED = 0.7;

    /**
     * Students in the course.
     * @var int[]
     */
    protected $students;

    /**
     * Returns the analyser class that should be used along with this target.
     *
     * @return string The full class name as a string
     */
    public function get_analyser_class() {
        return '\core\analytics\analyser\student_enrolments';
    }

    /**
     * Only past stuff.
     *
     * @param  \core_analytics\local\time_splitting\base $timesplitting
     * @return bool
     */
    public function can_use_timesplitting(\core_analytics\local\time_splitting\base $timesplitting): bool {
        return ($timesplitting instanceof \core_analytics\local\time_splitting\before_now);
    }

    /**
     * Overwritten to show a simpler language string.
     *
     * @param  int $modelid
     * @param  \context $context
     * @return string
     */
    public function get_insight_subject(int $modelid, \context $context) {
        return get_string('studentsatriskincourse', 'course', $context->get_context_name(false));
    }

    /**
     * Returns the body message for the insight.
     *
     * @param  \context     $context
     * @param  string       $contextname
     * @param  \stdClass    $user
     * @param  \moodle_url  $insighturl
     * @return string[]                     The plain text message and the HTML message
     */
    public function get_insight_body(\context $context, string $contextname, \stdClass $user, \moodle_url $insighturl): array {
        global $OUTPUT;

        $a = (object)['coursename' => $contextname, 'userfirstname' => $user->firstname];
        $fullmessage = get_string('studentsatriskinfomessage', 'course', $a) . PHP_EOL . PHP_EOL . $insighturl->out(false);
        $fullmessagehtml = $OUTPUT->render_from_template('core_analytics/insight_info_message',
            ['url' => $insighturl->out(false), 'insightinfomessage' => get_string('studentsatriskinfomessage', 'course', $a)]
        );

        return [$fullmessage, $fullmessagehtml];
    }

    /**
     * Discards courses that are not yet ready to be used for training or prediction.
     *
     * @param \core_analytics\analysable $course
     * @param bool $fortraining
     * @return true|string
     */
    public function is_valid_analysable(\core_analytics\analysable $course, $fortraining = true) {

        if (!$course->was_started()) {
            return get_string('coursenotyetstarted', 'course');
        }

        if (!$fortraining && !$course->get_course_data()->visible) {
            return get_string('hiddenfromstudents');
        }

        if (!$this->students = $course->get_students()) {
            return get_string('nocoursestudents', 'course');
        }

        if (!course_format_uses_sections($course->get_course_data()->format)) {
            // We can not split activities in time ranges.
            return get_string('nocoursesections', 'course');
        }

        if ($course->get_end() == 0) {
            // We require time end to be set.
            return get_string('nocourseendtime', 'course');
        }

        if ($course->get_end() < $course->get_start()) {
            return get_string('errorendbeforestart', 'course');
        }

        // A course that lasts longer than 1 year probably have wrong start or end dates.
        if ($course->get_end() - $course->get_start() > (YEARSECS + (WEEKSECS * 4))) {
            return get_string('coursetoolong', 'course');
        }

        // Finished courses can not be used to get predictions.
        if (!$fortraining && $course->is_finished()) {
            return get_string('coursealreadyfinished', 'course');
        }

        if ($fortraining) {
            // Ongoing courses data can not be used to train.
            if (!$course->is_finished()) {
                return get_string('coursenotyetfinished', 'course');
            }
        }

        return true;
    }

    /**
     * Discard student enrolments that are invalid.
     *
     * Note that this method assumes that the target is only interested in enrolments that are/were active
     * between the current course start and end times. Targets interested in predicting students at risk before
     * their enrolment start and targets interested in getting predictions for students whose enrolment already
     * finished should overwrite this method as these students are discarded by this method.
     *
     * @param int $sampleid
     * @param \core_analytics\analysable $course
     * @param bool $fortraining
     * @return bool
     */
    public function is_valid_sample($sampleid, \core_analytics\analysable $course, $fortraining = true) {

        $now = time();

        $userenrol = $this->retrieve('user_enrolments', $sampleid);
        if ($userenrol->timeend && $course->get_start() > $userenrol->timeend) {
            // Discard enrolments which time end is prior to the course start. This should get rid of
            // old user enrolments that remain on the course.
            return false;
        }

        $limit = $course->get_start() - (YEARSECS + (WEEKSECS * 4));
        if (($userenrol->timestart && $userenrol->timestart < $limit) ||
                (!$userenrol->timestart && $userenrol->timecreated < $limit)) {
            // Following what we do in is_valid_analysable, we will discard enrolments that last more than 1 academic year
            // because they have incorrect start and end dates or because they are reused along multiple years
            // without removing previous academic years students. This may not be very accurate because some courses
            // can last just some months, but it is better than nothing.
            return false;
        }

        if ($course->get_end()) {
            if (($userenrol->timestart && $userenrol->timestart > $course->get_end()) ||
                    (!$userenrol->timestart && $userenrol->timecreated > $course->get_end())) {
                // Discard user enrolments that start after the analysable official end.
                return false;
            }

        }

        if ($now < $userenrol->timestart && $userenrol->timestart) {
            // Discard enrolments whose start date is after now (no need to check timecreated > $now :P).
            return false;
        }

        if (!$fortraining && $userenrol->timeend && $userenrol->timeend < $now) {
            // We don't want to generate predictions for finished enrolments.
            return false;
        }

        return true;
    }

    /**
     * prediction_actions
     *
     * @param \core_analytics\prediction $prediction
     * @param bool $includedetailsaction
     * @param bool $isinsightuser
     * @return \core_analytics\prediction_action[]
     */
    public function prediction_actions(\core_analytics\prediction $prediction, $includedetailsaction = false,
            $isinsightuser = false) {

        $actions = array();

        $sampledata = $prediction->get_sample_data();
        $studentid = $sampledata['user']->id;

        // View outline report.
        $url = new \moodle_url('/report/outline/user.php', array('id' => $studentid, 'course' => $sampledata['course']->id,
                'mode' => 'outline'));
        $pix = new \pix_icon('i/report', get_string('outlinereport'));
        $actions[] = new \core_analytics\prediction_action('viewoutlinereport', $prediction, $url, $pix,
                get_string('outlinereport'), false, ['target' => '_blank']);

        return array_merge(parent::prediction_actions($prediction, $includedetailsaction, $isinsightuser), $actions);
    }

    /**
     * Suggested bulk actions for a user.
     *
     * @param  \core_analytics\prediction[]     $predictions List of predictions suitable for the bulk actions to use.
     * @return \core_analytics\bulk_action[]                 The list of bulk actions.
     */
    public function bulk_actions(array $predictions) {

        $actions = [];

        $userids = [];
        foreach ($predictions as $prediction) {
            $sampledata = $prediction->get_sample_data();
            $userid = $sampledata['user']->id;

            // Indexed by prediction id because we want the predictionid-userid
            // mapping later when sending the message.
            $userids[$prediction->get_prediction_data()->id] = $userid;
        }

        // Send a message for all the students.
        $attrs = array(
            'data-bulk-sendmessage' => '1',
            'data-prediction-to-user-id' => json_encode($userids)
        );
        $actions[] = new \core_analytics\bulk_action(self::MESSAGE_ACTION_NAME, new \moodle_url(''),
            new \pix_icon('t/message', get_string('sendmessage', 'message')),
            get_string('sendmessage', 'message'), true, $attrs);

        return array_merge($actions, parent::bulk_actions($predictions));
    }

    /**
     * Adds the JS required to run the bulk actions.
     */
    public function add_bulk_actions_js() {
        global $PAGE;

        $PAGE->requires->js_call_amd('report_insights/message_users', 'init',
            ['.insights-bulk-actions', self::MESSAGE_ACTION_NAME]);
        parent::add_bulk_actions_js();
    }

    /**
     * Is/was this user enrolment active during most of the analysis interval?
     *
     * This method discards enrolments that were not active during most of the analysis interval. It is
     * important to discard these enrolments because the indicator calculations can lead to misleading
     * results.
     *
     * Note that this method assumes that the target is interested in enrolments that are/were active
     * during the analysis interval. Targets interested in predicting students at risk before
     * their enrolment start should not call this method. Similarly, targets interested in getting
     * predictions for students whose enrolment already finished should not call this method either.
     *
     * @param  int    $sampleid     The id of the sample that is being calculated
     * @param  int    $starttime    The analysis interval start time
     * @param  int    $endtime      The analysis interval end time
     * @return bool
     */
    protected function enrolment_active_during_analysis_time(int $sampleid, int $starttime, int $endtime) {

        $userenrol = $this->retrieve('user_enrolments', $sampleid);

        if (!empty($userenrol->timestart)) {
            $enrolstart = $userenrol->timestart;
        } else {
            // This is always set.
            $enrolstart = $userenrol->timecreated;
        }

        if (!empty($userenrol->timeend)) {
            $enrolend = $userenrol->timeend;
        } else {
            // Default to tre end of the world.
            $enrolend = PHP_INT_MAX;
        }

        if ($endtime && $endtime < $enrolstart) {
            /* The enrolment starts/ed after the analysis end time.
             *   |=========|        |----------|
             * A start    A end   E start     E end
             */
            return false;
        }

        if ($starttime && $enrolend < $starttime) {
            /* The enrolment finishes/ed before the analysis start time.
             *    |---------|        |==========|
             * E start    E end   A start     A end
             */
            return false;
        }

        // Now we want to discard enrolments that were not active for most of the analysis interval. We
        // need both a $starttime and an $endtime to calculate this.

        if (!$starttime) {
            // Early return. Nothing to discard if there is no start.
            return true;
        }

        if (!$endtime) {
            // We can not calculate in relative terms (percent) how far from the analysis start time
            // this enrolment start is/was.
            return true;
        }

        if ($enrolstart < $starttime && $endtime < $enrolend) {
            /* The enrolment is active during all the analysis time.
             *    |-----------------------------|
             *               |========|
             * E start    A start   A end     E end
             */
            return true;
        }

        // If we reach this point is because the enrolment is only active for a portion of the analysis interval.
        // Therefore, we check that it was active for most of the analysis interval, a self::ENROL_ACTIVE_PERCENT_REQUIRED.

        if ($starttime <= $enrolstart && $enrolend <= $endtime) {
            /*    |=============================|
             *               |--------|
             * A start    E start   E end     A end
             */
            $activeenrolduration = $enrolend - $enrolstart;
        } else if ($enrolstart <= $starttime && $enrolend <= $endtime) {
            /*            |===================|
             *    |------------------|
             * E start  A start    E end    A end
             */
            $activeenrolduration = $enrolend - $starttime;
        } else if ($starttime <= $enrolstart && $endtime <= $enrolend) {
            /*   |===================|
             *               |------------------|
             * A start    E start  A end    E end
             */
            $activeenrolduration = $endtime - $enrolstart;
        }

        $analysisduration = $endtime - $starttime;

        if (floatval($activeenrolduration) / floatval($analysisduration) < self::ENROL_ACTIVE_PERCENT_REQUIRED) {
            // The student was not enroled in the course for most of the analysis interval.
            return false;
        }

        // We happily return true if the enrolment was active for more than self::ENROL_ACTIVE_PERCENT_REQUIRED of
        // the analysis interval.
        return true;
    }
}
