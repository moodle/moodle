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
 * Library functions for Course Analytics Report.
 *
 * @package    report_course_analytics
 * @copyright  2026 Antigravity
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * This function extends the course navigation with the report items.
 *
 * @param navigation_node $navigation The navigation node to extend
 * @param stdClass $course The course object
 * @param context $context The context object
 */
function report_course_analytics_extend_navigation_course($navigation, $course, $context) {
    if (has_capability('report/course_analytics:view', $context)) {
        $url = new moodle_url('/report/course_analytics/index.php', array('course' => $course->id));
        $navigation->add(
            get_string('pluginname', 'report_course_analytics'),
            $url,
            navigation_node::TYPE_SETTING,
            null,
            'report_course_analytics',
            new pix_icon('i/report', '')
        );
    }
}
