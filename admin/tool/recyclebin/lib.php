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
 * @param stdClass $course The course to object for the tool
 * @param context $context The context of the course
 * @return void|null return null if we don't want to display the node.
 */
function tool_recyclebin_extend_navigation_course($navigation, $course, $context) {
    global $PAGE;

    // Only add this settings item on non-site course pages.
    if (!$PAGE->course || $PAGE->course->id == SITEID || !\tool_recyclebin\course_bin::is_enabled()) {
        return null;
    }

    $coursebin = new \tool_recyclebin\course_bin($context->instanceid);

    // Check we can view the recycle bin.
    if (!$coursebin->can_view()) {
        return null;
    }

    $url = null;
    $settingnode = null;

    $url = new moodle_url('/admin/tool/recyclebin/index.php', array(
        'contextid' => $context->id
    ));

    // If we are set to auto-hide, check the number of items.
    $autohide = get_config('tool_recyclebin', 'autohide');
    if ($autohide) {
        $items = $coursebin->get_items();
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
        new pix_icon('trash', $pluginname, 'tool_recyclebin')
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
 * @param context $context The context of the course
 * @return void|null return null if we don't want to display the node.
 */
function tool_recyclebin_extend_navigation_category_settings($navigation, $context) {
    global $PAGE;

    // Check if it is enabled.
    if (!\tool_recyclebin\category_bin::is_enabled()) {
        return null;
    }

    $categorybin = new \tool_recyclebin\category_bin($context->instanceid);

    // Check we can view the recycle bin.
    if (!$categorybin->can_view()) {
        return null;
    }

    $url = null;
    $settingnode = null;

    // Add a link to the category recyclebin.
    $url = new moodle_url('/admin/tool/recyclebin/index.php', array(
        'contextid' => $context->id
    ));

    // If we are set to auto-hide, check the number of items.
    $autohide = get_config('tool_recyclebin', 'autohide');
    if ($autohide) {
        $items = $categorybin->get_items();
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
        new pix_icon('trash', $pluginname, 'tool_recyclebin')
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
    if (\tool_recyclebin\course_bin::is_enabled()) {
        $coursebin = new \tool_recyclebin\course_bin($cm->course);
        $coursebin->store_item($cm);
    }
}

/**
 * Hook called before we delete a course.
 *
 * @param \stdClass $course The course record.
 */
function tool_recyclebin_pre_course_delete($course) {
    // Delete all the items in the course recycle bin, regardless if it enabled or not.
    // It may have been enabled, then disabled later on, so may still have content.
    $coursebin = new \tool_recyclebin\course_bin($course->id);
    $coursebin->delete_all_items();

    if (\tool_recyclebin\category_bin::is_enabled()) {
        $categorybin = new \tool_recyclebin\category_bin($course->category);
        $categorybin->store_item($course);
    }
}

/**
 * Hook called before we delete a category.
 *
 * @param \stdClass $category The category record.
 */
function tool_recyclebin_pre_course_category_delete($category) {
    // Delete all the items in the category recycle bin, regardless if it enabled or not.
    // It may have been enabled, then disabled later on, so may still have content.
    $categorybin = new \tool_recyclebin\category_bin($category->id);
    $categorybin->delete_all_items();
}
