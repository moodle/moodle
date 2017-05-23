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
 * @copyright 2017 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_analytics\local\analyser;

defined('MOODLE_INTERNAL') || die();

/**
 *
 * @package   core_analytics
 * @copyright 2017 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class courses extends sitewide {

    public function get_samples_origin() {
        return 'course';
    }

    protected function provided_sample_data() {
        return array('course', 'context');
    }

    public function sample_access_context($sampleid) {
        return \context_system::instance();
    }

    protected function get_all_samples(\core_analytics\analysable $site) {
        global $DB;

        // Getting courses from DB instead of from the site as these samples
        // will be stored in memory and we just want the id.
        $select = 'id != 1';
        $courses = $DB->get_records_select('course', $select, null, '', '*');

        $courseids = array_keys($courses);
        $sampleids = array_combine($courseids, $courseids);

        $courses = array_map(function($course) {
            return array('course' => $course, 'context' => \context_course::instance($course->id));
        }, $courses);

        // No related data attached.
        return array($sampleids, $courses);
    }

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

    public function sample_description($sampleid, $contextid, $sampledata) {
        $description = format_string($sampledata['course'], true, array('context' => $contextid));
        return array($description, false);
    }
}
