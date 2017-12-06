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
 * Libs, public API.
 *
 * NOTE: page type not included because there can not be any blocks in popups
 *
 * @package    report_loglive
 * @copyright  2011 Petr Skoda
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * This function extends the navigation with the report items
 *
 * @global stdClass $CFG
 * @global core_renderer $OUTPUT
 * @param navigation_node $navigation The navigation node to extend
 * @param stdClass        $course     The course to object for the report
 * @param context         $context    The context of the course
 */
function report_loglive_extend_navigation_course($navigation, $course, $context) {
    if (has_capability('report/loglive:view', $context)) {
        $url = new moodle_url('/report/loglive/index.php', array('id' => $course->id));
        $navigation->add(get_string('pluginname', 'report_loglive'), $url, navigation_node::TYPE_SETTING, null, null,
                new pix_icon('i/report', ''));
    }
}

/**
 * Callback to verify if the given instance of store is supported by this report or not.
 *
 * @param string $instance store instance.
 *
 * @return bool returns true if the store is supported by the report, false otherwise.
 */
function report_loglive_supports_logstore($instance) {
    if ($instance instanceof \core\log\sql_reader) {
        return true;
    }
    return false;
}
