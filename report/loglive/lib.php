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
 * @param stdClass        $context    The context of the course
 */
function report_loglive_extend_navigation_course($navigation, $course, $context) {
    global $CFG, $OUTPUT;
    if (has_capability('report/loglive:view', $context)) {
        $url = new moodle_url('/report/loglive/index.php', array('id'=>$course->id, 'inpopup'=>1));
        $action = new action_link($url, get_string('pluginname', 'report_loglive'), new popup_action('click', $url));
        $navigation->add('', $action, navigation_node::TYPE_SETTING, null, null, new pix_icon('i/report', ''));
    }
}
