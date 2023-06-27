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
 * Site courses analyser working at system level (insights for the site admin).
 *
 * @package   core
 * @copyright 2017 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\analytics\analyser;

defined('MOODLE_INTERNAL') || die();

/**
 * Site courses analyser working at system level (insights for the site admin).
 *
 * @package   core
 * @copyright 2017 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class site_courses extends \core_analytics\local\analyser\sitewide {

    /**
     * Samples origin is course table.
     *
     * @return string
     */
    public function get_samples_origin() {
        return 'course';
    }

    /**
     * Returns the sample analysable
     *
     * @param int $sampleid
     * @return \core_analytics\analysable
     */
    public function get_sample_analysable($sampleid) {
        return new \core_analytics\site();
    }

    /**
     * Data this analyer samples provide.
     *
     * @return string[]
     */
    protected function provided_sample_data() {
        return array('course', 'context');
    }

    /**
     * Returns the sample context.
     *
     * @param int $sampleid
     * @return \context
     */
    public function sample_access_context($sampleid) {
        return \context_system::instance();
    }

    /**
     * Returns all site courses.
     *
     * @param \core_analytics\analysable $site
     * @return array
     */
    public function get_all_samples(\core_analytics\analysable $site) {
        global $DB;

        // Getting courses from DB instead of from the site as these samples
        // will be stored in memory and we just want the id.
        $select = 'id != 1';
        $courses = get_courses('all', 'c.sortorder ASC');
        unset($courses[SITEID]);

        $courseids = array_keys($courses);
        $sampleids = array_combine($courseids, $courseids);

        $courses = array_map(function($course) {
            return array('course' => $course, 'context' => \context_course::instance($course->id));
        }, $courses);

        // No related data attached.
        return array($sampleids, $courses);
    }

    /**
     * Return all complete samples data from sample ids.
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
     * Returns the description of a sample.
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
