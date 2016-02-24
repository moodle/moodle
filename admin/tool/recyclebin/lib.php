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
 * Local lib code
 *
 * @package    tool_recyclebin
 * @copyright  2015 Skylar Kelty <S.Kelty@kent.ac.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * Adds a recycle bin link to the course admin menu.
 *
 * @param navigation_node $navigation The navigation node to extend
 * @param stdClass        $course     The course to object for the tool
 * @param context         $context    The context of the course
 */
function tool_recyclebin_extend_navigation_course($navigation, $course, $context) {
    global $PAGE;

    $url = null;
    $bin = null;
    $settingnode = null;

    // Only add this settings item on non-site course pages.
    if (!$PAGE->course || $PAGE->course->id == SITEID || !\tool_recyclebin\course::is_enabled()) {
        return null;
    }

    // Check we can view the recycle bin.
    if (!has_capability('tool/recyclebin:view_item', $context)) {
        return null;
    }

    $bin = new \tool_recyclebin\course($context->instanceid);
    $url = new moodle_url('/admin/tool/recyclebin/index.php', array(
        'contextid' => $context->id
    ));

    // If we are set to auto-hide, check the number of items.
    $autohide = get_config('tool_recyclebin', 'autohide');
    if ($autohide) {
        $items = $bin->get_items();
        if (empty($items)) {
            return null;
        }
    }

    // Add the recyclebin link.
    $pluginname = get_string('pluginname', 'tool_recyclebin');

    $node = navigation_node::create(
        $pluginname,
        $url,
        navigation_node::NODETYPE_LEAF,
        'tool_recyclebin',
        'tool_recyclebin',
        new pix_icon('e/cleanup_messy_code', $pluginname)
    );

    if ($PAGE->url->compare($url, URL_MATCH_BASE)) {
        $node->make_active();
    }

    $navigation->add_node($node);
}

/**
 * Adds a recycle bin link to the course admin menu.
 *
 * @param navigation_node $navigation The navigation node to extend
 * @param context         $context    The context of the course
 */
function tool_recyclebin_extend_navigation_category_settings($navigation, $context) {
    global $PAGE;

    $url = null;
    $bin = null;
    $settingnode = null;

    // Check we can view the recycle bin.
    if (!has_capability('tool/recyclebin:view_course', $context) || !\tool_recyclebin\category::is_enabled()) {
        return null;
    }

    // Add a link to the category recyclebin.
    $bin = new \tool_recyclebin\category($context->instanceid);
    $url = new moodle_url('/admin/tool/recyclebin/index.php', array(
        'contextid' => $context->id
    ));

    // If we are set to auto-hide, check the number of items.
    $autohide = get_config('tool_recyclebin', 'autohide');
    if ($autohide) {
        $items = $bin->get_items();
        if (empty($items)) {
            return null;
        }
    }

    // Add the recyclebin link.
    $pluginname = get_string('pluginname', 'tool_recyclebin');

    $node = navigation_node::create(
        $pluginname,
        $url,
        navigation_node::NODETYPE_LEAF,
        'tool_recyclebin',
        'tool_recyclebin',
        new pix_icon('e/cleanup_messy_code', $pluginname)
    );

    if ($PAGE->url->compare($url, URL_MATCH_BASE)) {
        $node->make_active();
    }

    $navigation->add_node($node);
}

/**
 * Hook called before we delete a course module.
 *
 * @param \stdClass $cm The course module record.
 */
function tool_recyclebin_pre_course_module_delete($cm) {
    if (\tool_recyclebin\course::is_enabled()) {
        $recyclebin = new \tool_recyclebin\course($cm->course);
        $recyclebin->store_item($cm);
    }
}

/**
 * Hook called before we delete a course.
 *
 * @param \stdClass $course The course record.
 */
function tool_recyclebin_pre_course_delete($course) {
    if (\tool_recyclebin\category::is_enabled()) {
        $recyclebin = new \tool_recyclebin\category($course->category);
        $recyclebin->store_item($course);
    }
}
