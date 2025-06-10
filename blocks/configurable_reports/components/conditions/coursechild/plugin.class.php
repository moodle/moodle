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
 * Configurable Reports a Moodle block for creating customizable reports
 *
 * @copyright  2020 Juan Leyva <juan@moodle.com>
 * @package    block_configurable_reports
 * @author     Juan leyva <http://www.twitter.com/jleyvadelgado>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;
require_once($CFG->dirroot . '/blocks/configurable_reports/plugin.class.php');

/**
 * Class plugin_coursechild
 *
 * @package   block_configurable_reports
 * @author    Juan leyva <http://www.twitter.com/jleyvadelgado>
 */
class plugin_coursechild extends plugin_base {

    /**
     * Init
     *
     * @return void
     */
    public function init(): void {
        $this->fullname = get_string('coursechild', 'block_configurable_reports');
        $this->form = true;
        $this->reporttypes = ['courses'];
    }

    /**
     * Summary
     *
     * @param object $data
     * @return string
     */
    public function summary(object $data): string {
        global $DB;
        $course = $DB->get_record('course', ['id' => $data->courseid]);
        if ($course) {
            return get_string('coursechild', 'block_configurable_reports') . ' ' . (format_string($course->fullname));
        }

        return '';
    }

    /**
     * Execute
     *
     * @param object $data
     * @return array|int[]|string[]
     */
    public function execute($data) {
        global $DB;
        // Data -> Plugin configuration data.
        $finalcourses = [];
        if ($courses = $DB->get_records('course_meta', ['child_course' => $data->courseid])) {
            foreach ($courses as $c) {
                $finalcourses[] = $c->parent_course;
            }
        }

        return $finalcourses;
    }

}

