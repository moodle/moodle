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
 * This page contains navigation hooks for learning plans.
 *
 * @package    tool_lp
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * This function extends the course navigation
 *
 * @param navigation_node $navigation The navigation node to extend
 * @param stdClass $course The course to object for the tool
 * @param context $coursecontext The context of the course
 */
function tool_lp_extend_navigation_course($navigation, $course, $coursecontext) {
    // Just a link to course report.
    $title = get_string('coursecompetencies', 'tool_lp');
    $path = new moodle_url("/admin/tool/lp/coursecompetencies.php", array('courseid' => $course->id));
    $settingsnode = navigation_node::create($title,
                                            $path,
                                            navigation_node::TYPE_SETTING,
                                            null,
                                            null,
                                            new pix_icon('competency', '', 'tool_lp'));
    if (isset($settingsnode)) {
        $navigation->add_node($settingsnode);
    }
}

/**
 * Add nodes to myprofile page.
 *
 * @param \core_user\output\myprofile\tree $tree Tree object
 * @param stdClass $user user object
 * @param bool $iscurrentuser
 * @param stdClass $course Course object
 *
 * @return bool
 */
function tool_lp_myprofile_navigation(core_user\output\myprofile\tree $tree, $user, $iscurrentuser, $course) {
    global $USER;

    if (!\tool_lp\plan::can_read_user($user->id)) {
        return false;
    }

    $url = new moodle_url('/admin/tool/lp/plans.php');
    $node = new core_user\output\myprofile\node('miscellaneous', 'learningplans',
                                                get_string('learningplans', 'tool_lp'), null, $url);
    $tree->add_node($node);
}

/**
 * This function extends the category navigation to add learning plan links.
 *
 * @param navigation_node $navigation The navigation node to extend
 * @param context $coursecategorycontext The context of the course category
 */
function tool_lp_extend_navigation_category_settings($navigation, $coursecategorycontext) {
    // We check permissions before renderring the links.
    $templatemanagecapability = has_capability('tool/lp:templatemanage', $coursecategorycontext);
    $competencymanagecapability = has_capability('tool/lp:competencymanage', $coursecategorycontext);
    if (!$templatemanagecapability && !$competencymanagecapability) {
        return false;
    }

    // The link to the learning plan page.
    if ($templatemanagecapability) {
        $title = get_string('learningplans', 'tool_lp');
        $path = new moodle_url("/admin/tool/lp/learningplans.php", array('pagecontextid' => $coursecategorycontext->id));
        $settingsnode = navigation_node::create($title,
                                                $path,
                                                navigation_node::TYPE_SETTING,
                                                null,
                                                null,
                                                new pix_icon('competency', '', 'tool_lp'));
        if (isset($settingsnode)) {
            $navigation->add_node($settingsnode);
        }
    }

    // The link to the competency frameworks page.
    if ($competencymanagecapability) {
        $title = get_string('competencyframeworks', 'tool_lp');
        $path = new moodle_url("/admin/tool/lp/competencyframeworks.php", array('pagecontextid' => $coursecategorycontext->id));
        $settingsnode = navigation_node::create($title,
                                                $path,
                                                navigation_node::TYPE_SETTING,
                                                null,
                                                null,
                                                new pix_icon('competency', '', 'tool_lp'));
        if (isset($settingsnode)) {
            $navigation->add_node($settingsnode);
        }
    }
}