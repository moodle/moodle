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
 * Public API of the AI usage course report.
 *
 * @package    report_aiusage
 * @copyright  2026 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Extend the course navigation with a link to the AI usage report.
 *
 * This is reached via course administration > Reports, which core gates behind the
 * moodle/site:viewreports capability (teacher/manager archetypes by default), so this entry point
 * only ever benefits users who can already view all students' usage.
 * Students reach their own usage report via {@see report_aiusage_myprofile_navigation()} instead.
 *
 * @param navigation_node $navigation The navigation node to extend.
 * @param stdClass $course The course to add the report link for.
 * @param stdClass $context The context of the course.
 */
function report_aiusage_extend_navigation_course($navigation, $course, $context) {
    if (has_capability('report/aiusage:view', $context) || has_capability('report/aiusage:viewown', $context)) {
        $url = new moodle_url('/report/aiusage/index.php', ['id' => $course->id]);
        $navigation->add(
            get_string('pluginname', 'report_aiusage'),
            $url,
            navigation_node::TYPE_SETTING,
            null,
            null,
            new pix_icon('i/report', ''),
        );
    }
}

/**
 * Add a link to the AI usage report on a user's profile page, under the given course.
 *
 * The course-level "Reports" navigation added by {@see report_aiusage_extend_navigation_course()}
 * is gated behind moodle/site:viewreports, which students do not hold by default, so this is the
 * entry point students actually use to reach their own usage report.
 *
 * @param \core_user\output\myprofile\tree $tree Tree object.
 * @param stdClass $user The user whose profile is being viewed.
 * @param bool $iscurrentuser Whether the viewer is looking at their own profile.
 * @param stdClass|null $course The course the profile is being viewed within, if any.
 */
function report_aiusage_myprofile_navigation(core_user\output\myprofile\tree $tree, $user, $iscurrentuser, $course) {
    if (empty($course) || !$iscurrentuser) {
        // Only the "view own" self-service entry point is added here; full course-wide usage
        // (including other users') is available to staff via the course "Reports" link instead.
        return;
    }

    $context = context_course::instance($course->id);
    if (has_capability('report/aiusage:viewown', $context) || has_capability('report/aiusage:view', $context)) {
        $url = new moodle_url('/report/aiusage/index.php', ['id' => $course->id]);
        $node = new core_user\output\myprofile\node(
            'reports',
            'aiusage',
            get_string('pluginname', 'report_aiusage'),
            null,
            $url,
        );
        $tree->add_node($node);
    }
}
