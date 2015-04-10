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
 * This page lists public api for tool_monitor plugin.
 *
 * @package    tool_monitor
 * @copyright  2014 onwards Ankit Agarwal <ankit.agrr@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * This function extends the navigation with the tool items
 *
 * @param navigation_node $navigation The navigation node to extend
 * @param stdClass        $course     The course to object for the tool
 * @param context         $context    The context of the course
 */
function tool_monitor_extend_navigation_course($navigation, $course, $context) {
    if (has_capability('tool/monitor:managerules', $context) && get_config('tool_monitor', 'enablemonitor')) {
        $url = new moodle_url('/admin/tool/monitor/managerules.php', array('courseid' => $course->id));
        $settingsnode = navigation_node::create(get_string('managerules', 'tool_monitor'), $url, navigation_node::TYPE_SETTING,
                null, null, new pix_icon('i/settings', ''));
        $reportnode = $navigation->get('coursereports');

        if (isset($settingsnode) && !empty($reportnode)) {
            $reportnode->add_node($settingsnode);
        }
    }
}

/**
 * This function extends the navigation with the tool items
 *
 * @param navigation_node $navigation The navigation node to extend
 * @param stdClass        $course     The course to object for the tool
 * @param context         $context    The context of the course
 */
function tool_monitor_extend_navigation_frontpage($navigation, $course, $context) {

    if (has_capability('tool/monitor:managerules', $context)) {
        $url = new moodle_url('/admin/tool/monitor/managerules.php', array('courseid' => $course->id));
        $settingsnode = navigation_node::create(get_string('managerules', 'tool_monitor'), $url, navigation_node::TYPE_SETTING,
                null, null, new pix_icon('i/settings', ''));
        $reportnode = $navigation->get('frontpagereports');

        if (isset($settingsnode) && !empty($reportnode)) {
            $reportnode->add_node($settingsnode);
        }
    }
}

/**
 * This function extends the navigation with the tool items for user settings node.
 *
 * @param navigation_node $navigation  The navigation node to extend
 * @param stdClass        $user        The user object
 * @param context         $usercontext The context of the user
 * @param stdClass        $course      The course to object for the tool
 * @param context         $coursecontext     The context of the course
 */
function tool_monitor_extend_navigation_user_settings($navigation, $user, $usercontext, $course, $coursecontext) {
    global $USER, $SITE;

    // Don't show the setting if the event monitor isn't turned on. No access to other peoples subscriptions.
    if (get_config('tool_monitor', 'enablemonitor') && $USER->id == $user->id) {
        // The $course->id will always be the course that corresponds to the current context.
        $courseid = $course->id;
        // A $course->id of $SITE->id might either be the frontpage or the site. So if we get the site ID back, check the...
        // ...courseid parameter passed to the page so we can know if we are looking at the frontpage rules or site level rules.
        if ($course->id == $SITE->id && optional_param('courseid', $course->id, PARAM_INT) == 0) {
            $courseid = 0;
        }
        $url = new moodle_url('/admin/tool/monitor/index.php', array('courseid' => $courseid));
        $subsnode = navigation_node::create(get_string('managesubscriptions', 'tool_monitor'), $url,
                navigation_node::TYPE_SETTING, null, 'monitor', new pix_icon('i/settings', ''));

        if (isset($subsnode) && !empty($navigation)) {
            $navigation->add_node($subsnode);
        }
    }
}
