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
 *
 * @package   core_analytics
 * @copyright 2016 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_analytics\local\analyser;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/lib/enrollib.php');

/**
 *
 * @package   core_analytics
 * @copyright 2016 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class student_enrolments extends by_course {

    /**
     * @var array Cache for user_enrolment id - course id relation.
     */
    protected $samplecourses = array();

    protected function get_samples_origin() {
        return 'user_enrolments';
    }

    public function sample_access_context($sampleid) {
        return \context_course::instance($this->get_sample_courseid($sampleid));
    }

    public function get_sample_analysable($sampleid) {
        $course = enrol_get_course_by_user_enrolment_id($ueid);
        return \core_analytics\course($course);
    }

    protected function provided_sample_data() {
        return array('user_enrolments', 'context', 'course', 'user');
    }

    /**
     * All course enrolments.
     *
     * @param \core_analytics\analysable $course
     * @return void
     */
    protected function get_all_samples(\core_analytics\analysable $course) {

        $enrolments = enrol_get_course_users($course->get_id());

        // We fetch all enrolments, but we are only interested in students.
        $studentids = $course->get_students();

        $samplesdata = array();
        foreach ($enrolments as $user) {

            if (empty($studentids[$user->id])) {
                // Not a student.
                continue;
            }

            $sampleid = $user->enrolmentid;
            unset($user->enrolmentid);

            $samplesdata[$sampleid]['course'] = $course->get_course_data();
            $samplesdata[$sampleid]['context'] = $course->get_context();
            $samplesdata[$sampleid]['user'] = $user;

            // Fill the cache.
            $this->samplecourses[$sampleid] = $course->get_id();
        }

        $enrolids = array_keys($samplesdata);
        return array(array_combine($enrolids, $enrolids), $samplesdata);
    }

    public function get_samples($sampleids) {
        global $DB;

        // Some course enrolments.
        list($enrolsql, $params) = $DB->get_in_or_equal($sampleids, SQL_PARAMS_NAMED);

        // Although we load all the course users data in memory anyway, using recordsets we will
        // not use the double of the memory required by the end of the iteration.
        $sql = "SELECT ue.id AS enrolmentid, u.* FROM {user_enrolments} ue
                  JOIN {user} u on ue.userid = u.id
                  WHERE ue.id $enrolsql";
        $enrolments = $DB->get_recordset_sql($sql, $params);

        $samplesdata = array();
        foreach ($enrolments as $user) {

            $sampleid = $user->enrolmentid;
            unset($user->enrolmentid);

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
        $enrolments->close();

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
     * @return array
     */
    public function sample_description($sampleid, $contextid, $sampledata) {
        $description = fullname($sampledata['user'], true, array('context' => $contextid));
        return array($description, new \user_picture($sampledata['user']));
    }

}
