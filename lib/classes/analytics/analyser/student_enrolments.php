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
 * Student enrolments analyser.
 *
 * @package   core
 * @copyright 2016 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\analytics\analyser;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/lib/enrollib.php');

/**
 * Student enrolments analyser.
 *
 * It does return all student enrolments including the suspended ones.
 *
 * @package   core
 * @copyright 2016 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class student_enrolments extends \core_analytics\local\analyser\by_course {

    /**
     * @var array Cache for user_enrolment id - course id relation.
     */
    protected $samplecourses = array();

    /**
     * Defines the origin of the samples in the database.
     *
     * @return string
     */
    public function get_samples_origin() {
        return 'user_enrolments';
    }

    /**
     * Returns the student enrolment course context.
     *
     * @param int $sampleid
     * @return \context
     */
    public function sample_access_context($sampleid) {
        return \context_course::instance($this->get_sample_courseid($sampleid));
    }

    /**
     * Returns the student enrolment course.
     *
     * @param int $sampleid
     * @return \core_analytics\analysable
     */
    public function get_sample_analysable($sampleid) {
        $course = enrol_get_course_by_user_enrolment_id($sampleid);
        return \core_analytics\course::instance($course);
    }

    /**
     * Data provided by get_all_samples & get_samples.
     *
     * @return string[]
     */
    protected function provided_sample_data() {
        return array('user_enrolments', 'context', 'course', 'user');
    }

    /**
     * We need to delete associated data if a user requests his data to be deleted.
     *
     * @return bool
     */
    public function processes_user_data() {
        return true;
    }

    /**
     * Join the samples origin table with the user id table.
     *
     * @param string $sampletablealias
     * @return string
     */
    public function join_sample_user($sampletablealias) {
        return "JOIN {user_enrolments} ue ON {$sampletablealias}.sampleid = ue.id " .
               "JOIN {user} u ON u.id = ue.userid";
    }

    /**
     * All course student enrolments.
     *
     * It does return all student enrolments including the suspended ones.
     *
     * @param \core_analytics\analysable $course
     * @return array
     */
    public function get_all_samples(\core_analytics\analysable $course) {

        $enrolments = enrol_get_course_users($course->get_id());

        // We fetch all enrolments, but we are only interested in students.
        $studentids = $course->get_students();

        $samplesdata = array();
        foreach ($enrolments as $userenrolmentid => $user) {

            if (empty($studentids[$user->id])) {
                // Not a student.
                continue;
            }

            $sampleid = $userenrolmentid;
            $samplesdata[$sampleid]['user_enrolments'] = (object)array(
                'id' => $user->ueid,
                'status' => $user->uestatus,
                'enrolid' => $user->ueenrolid,
                'userid' => $user->id,
                'timestart' => $user->uetimestart,
                'timeend' => $user->uetimeend,
                'modifierid' => $user->uemodifierid,
                'timecreated' => $user->uetimecreated,
                'timemodified' => $user->uetimemodified
            );
            unset($user->ueid);
            unset($user->uestatus);
            unset($user->ueenrolid);
            unset($user->uetimestart);
            unset($user->uetimeend);
            unset($user->uemodifierid);
            unset($user->uetimecreated);
            unset($user->uetimemodified);

            $samplesdata[$sampleid]['course'] = $course->get_course_data();
            $samplesdata[$sampleid]['context'] = $course->get_context();
            $samplesdata[$sampleid]['user'] = $user;

            // Fill the cache.
            $this->samplecourses[$sampleid] = $course->get_id();
        }

        $enrolids = array_keys($samplesdata);
        return array(array_combine($enrolids, $enrolids), $samplesdata);
    }

    /**
     * Returns all samples from the samples ids.
     *
     * @param int[] $sampleids
     * @return array
     */
    public function get_samples($sampleids) {
        global $DB;

        $enrolments = enrol_get_course_users(false, false, array(), $sampleids);

        // Some course enrolments.
        list($enrolsql, $params) = $DB->get_in_or_equal($sampleids, SQL_PARAMS_NAMED);

        $samplesdata = array();
        foreach ($enrolments as $userenrolmentid => $user) {

            $sampleid = $userenrolmentid;
            $samplesdata[$sampleid]['user_enrolments'] = (object)array(
                'id' => $user->ueid,
                'status' => $user->uestatus,
                'enrolid' => $user->ueenrolid,
                'userid' => $user->id,
                'timestart' => $user->uetimestart,
                'timeend' => $user->uetimeend,
                'modifierid' => $user->uemodifierid,
                'timecreated' => $user->uetimecreated,
                'timemodified' => $user->uetimemodified
            );
            unset($user->ueid);
            unset($user->uestatus);
            unset($user->ueenrolid);
            unset($user->uetimestart);
            unset($user->uetimeend);
            unset($user->uemodifierid);
            unset($user->uetimecreated);
            unset($user->uetimemodified);

            // Enrolment samples are grouped by the course they belong to, so all $sampleids belong to the same
            // course, $courseid and $coursemodinfo will only query the DB once and cache the course data in memory.
            $courseid = $this->get_sample_courseid($sampleid);
            $coursemodinfo = get_fast_modinfo($courseid);
            $coursecontext = \context_course::instance($courseid);

            $samplesdata[$sampleid]['course'] = $coursemodinfo->get_course();
            $samplesdata[$sampleid]['context'] = $coursecontext;
            $samplesdata[$sampleid]['user'] = $user;

            // Fill the cache.
            $this->samplecourses[$sampleid] = $coursemodinfo->get_course()->id;
        }

        $enrolids = array_keys($samplesdata);
        return array(array_combine($enrolids, $enrolids), $samplesdata);
    }

    /**
     * Returns the student enrolment course id.
     *
     * @param int $sampleid
     * @return int
     */
    protected function get_sample_courseid($sampleid) {
        global $DB;

        if (empty($this->samplecourses[$sampleid])) {
            $course = enrol_get_course_by_user_enrolment_id($sampleid);
            $this->samplecourses[$sampleid] = $course->id;
        }

        return $this->samplecourses[$sampleid];
    }

    /**
     * Returns the visible name of a sample + a renderable to display as sample picture.
     *
     * @param int $sampleid
     * @param int $contextid
     * @param array $sampledata
     * @return array array(string, \renderable)
     */
    public function sample_description($sampleid, $contextid, $sampledata) {
        $description = fullname($sampledata['user'], true, array('context' => $contextid));
        return array($description, new \user_picture($sampledata['user']));
    }

}
