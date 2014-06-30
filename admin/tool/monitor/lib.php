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
    $node = navigation_node::create(get_string('pluginname', 'tool_monitor'), null, navigation_node::TYPE_CONTAINER, null,
        'eventmonitor', new pix_icon('i/tool', ''));

    if (has_capability('tool/monitor:managerules', $context)) {
        $url = new moodle_url('/admin/tool/monitor/managerules.php', array('courseid' => $course->id));
        $settingsnode = navigation_node::create(get_string('managerules', 'tool_monitor'), $url, navigation_node::TYPE_SETTING,
            null, null, new pix_icon('i/settings', ''));
    }

    if (has_capability('tool/monitor:subscribe', $context)) {
        $url = new moodle_url('/admin/tool/monitor/index.php', array('courseid' => $course->id));
        $subsnode = navigation_node::create(get_string('managesubscriptions', 'tool_monitor'), $url,
            navigation_node::TYPE_SETTING, null, null, new pix_icon('i/settings', ''));
    }

    $reportnode = $navigation->get('coursereports');

    if ((isset($subsnode) || isset($settingsnode)) && !empty($reportnode)) {
        // Add the node only if there are sub pages.
        $node = $reportnode->add_node($node);

        // Our navigation lib can not handle nodes that have active child, so we need to always add parent first without
        // children. Refer MDL-45872 .

        if (isset($settingsnode)) {
            $node->add_node($settingsnode);
        }

        if (isset($subsnode)) {
            $node->add_node($subsnode);
        }
    }
}
