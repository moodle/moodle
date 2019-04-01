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
 * @package   core
 * @copyright 2019 Victor Deniz <victor@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\analytics\target;

defined('MOODLE_INTERNAL') || die();

/**
 * Base class for targets whose analysable is a course using user enrolments as samples.
 *
 * @package   core
 * @copyright 2019 Victor Deniz <victor@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class course_enrolments extends \core_analytics\local\target\binary {

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
     * Discards courses that are not yet ready to be used for training or prediction.
     *
     * @param \core_analytics\analysable $course
     * @param bool $fortraining
     * @return true|string
     */
    public function is_valid_analysable(\core_analytics\analysable $course, $fortraining = true) {

        if (!$course->was_started()) {
            return get_string('coursenotyetstarted');
        }

        if (!$this->students = $course->get_students()) {
            return get_string('nocoursestudents');
        }

        if (!course_format_uses_sections($course->get_course_data()->format)) {
            // We can not split activities in time ranges.
            return get_string('nocoursesections');
        }

        if ($course->get_end() == 0) {
            // We require time end to be set.
            return get_string('nocourseendtime');
        }

        if ($course->get_end() < $course->get_start()) {
            return get_string('errorendbeforestart', 'analytics');
        }

        // A course that lasts longer than 1 year probably have wrong start or end dates.
        if ($course->get_end() - $course->get_start() > (YEARSECS + (WEEKSECS * 4))) {
            return get_string('coursetoolong', 'analytics');
        }

        // Finished courses can not be used to get predictions.
        if (!$fortraining && $course->is_finished()) {
            return get_string('coursealreadyfinished');
        }

        if ($fortraining) {
            // Ongoing courses data can not be used to train.
            if (!$course->is_finished()) {
                return get_string('coursenotyetfinished');
            }
        }

        return true;
    }

    /**
     * Discard student enrolments that are invalid.
     *
     * @param int $sampleid
     * @param \core_analytics\analysable $course
     * @param bool $fortraining
     * @return bool
     */
    public function is_valid_sample($sampleid, \core_analytics\analysable $course, $fortraining = true) {

        $userenrol = $this->retrieve('user_enrolments', $sampleid);
        if ($userenrol->timeend && $course->get_start() > $userenrol->timeend) {
            // Discard enrolments which time end is prior to the course start. This should get rid of
            // old user enrolments that remain on the course.
            return false;
        }

        $limit = $course->get_start() - (YEARSECS + (WEEKSECS * 4));
        if (($userenrol->timestart && $userenrol->timestart < $limit) ||
                (!$userenrol->timestart && $userenrol->timecreated < $limit)) {
            // Following what we do in is_valid_sample, we will discard enrolments that last more than 1 academic year
            // because they have incorrect start and end dates or because they are reused along multiple years
            // without removing previous academic years students. This may not be very accurate because some courses
            // can last just some months, but it is better than nothing.
            return false;
        }

        if (($userenrol->timestart && $userenrol->timestart > $course->get_end()) ||
                (!$userenrol->timestart && $userenrol->timecreated > $course->get_end())) {
            // Discard user enrolments that starts after the analysable official end.
            return false;
        }

        return true;
    }

    /**
     * prediction_actions
     *
     * @param \core_analytics\prediction $prediction
     * @param bool $includedetailsaction
     * @return \core_analytics\prediction_action[]
     */
    public function prediction_actions(\core_analytics\prediction $prediction, $includedetailsaction = false) {
        global $USER;

        $actions = array();

        $sampledata = $prediction->get_sample_data();
        $studentid = $sampledata['user']->id;

        $attrs = array('target' => '_blank');

        // Send a message.
        $url = new \moodle_url('/message/index.php', array('user' => $USER->id, 'id' => $studentid));
        $pix = new \pix_icon('t/message', get_string('sendmessage', 'message'));
        $actions[] = new \core_analytics\prediction_action('studentmessage', $prediction, $url, $pix,
                get_string('sendmessage', 'message'), false, $attrs);

        // View outline report.
        $url = new \moodle_url('/report/outline/user.php', array('id' => $studentid, 'course' => $sampledata['course']->id,
                'mode' => 'outline'));
        $pix = new \pix_icon('i/report', get_string('outlinereport'));
        $actions[] = new \core_analytics\prediction_action('viewoutlinereport', $prediction, $url, $pix,
                get_string('outlinereport'), false, $attrs);

        return array_merge($actions, parent::prediction_actions($prediction, $includedetailsaction));
    }
}
