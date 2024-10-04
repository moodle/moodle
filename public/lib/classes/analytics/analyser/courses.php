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
 * Courses analyser working at course level (insights for the course teachers).
 *
 * @package   core
 * @copyright 2017 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\analytics\analyser;

defined('MOODLE_INTERNAL') || die();

/**
 * Courses analyser working at course level (insights for the course teachers).
 *
 * @package   core
 * @copyright 2017 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class courses extends \core_analytics\local\analyser\by_course {

    /**
     * Samples origin is course table.
     *
     * @return string
     */
    public function get_samples_origin() {
        return 'course';
    }

    /**
     * Just one sample per analysable.
     *
     * @return bool
     */
    public static function one_sample_per_analysable() {
        return true;
    }

    /**
     * Returns the analysable of a sample
     *
     * @param int $sampleid
     * @return \core_analytics\analysable
     */
    public function get_sample_analysable($sampleid) {
        return \core_analytics\course::instance($sampleid);
    }

    /**
     * This provides samples' course and context.
     *
     * @return string[]
     */
    protected function provided_sample_data() {
        return array('course', 'context');
    }

    /**
     * Returns the context of a sample.
     *
     * @param int $sampleid
     * @return \context
     */
    public function sample_access_context($sampleid) {
        return \context_course::instance($sampleid);
    }

    /**
     * This will return just one course as we analyse 'by_course'.
     *
     * @param \core_analytics\analysable $course
     * @return array
     */
    public function get_all_samples(\core_analytics\analysable $course) {

        $context = \context_course::instance($course->get_id());

        // Just 1 sample per analysable.
        return array(
            array($course->get_id() => $course->get_id()),
            array($course->get_id() => array('course' => $course->get_course_data(), 'context' => $context))
        );
    }

    /**
     * Returns samples data from sample ids.
     *
     * @param int[] $sampleids
     * @return array
     */
    public function get_samples($sampleids) {
        global $DB;

        list($sql, $params) = $DB->get_in_or_equal($sampleids, SQL_PARAMS_NAMED);
        $courses = $DB->get_records_select('course', "id $sql", $params);

        $courseids = array_keys($courses);
        $sampleids = array_combine($courseids, $courseids);

        $courses = array_map(function($course) {
            return array('course' => $course, 'context' => \context_course::instance($course->id));
        }, $courses);

        // No related data attached.
        return array($sampleids, $courses);
    }

    /**
     * Returns the sample description
     *
     * @param int $sampleid
     * @param int $contextid
     * @param array $sampledata
     * @return array array(string, \renderable)
     */
    public function sample_description($sampleid, $contextid, $sampledata) {
        $description = format_string(
            get_course_display_name_for_list($sampledata['course']), true, array('context' => $sampledata['context']));
        $courseimage = new \pix_icon('i/course', get_string('course'));
        return array($description, $courseimage);
    }
}
