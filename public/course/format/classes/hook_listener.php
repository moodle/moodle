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

namespace core_courseformat;

use core_courseformat\hook\after_course_content_updated;
use core_courseformat\local\linearnavigationsettings;
use core_course\hook\before_course_viewed;
use core\output\supplementary_sticky_footer;
use core_group\hook\after_group_membership_added;
use core_group\hook\after_group_membership_removed;

/**
 * Hook listener for course format
 *
 * @package    core_courseformat
 * @copyright  2024 Laurent David <laurent.david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class hook_listener {
    /**
     * Add members to group room when a new member is added to the group.
     *
     * @param after_group_membership_added $hook The group membership added hook.
     */
    public static function add_members_to_group(
        after_group_membership_added $hook,
    ): void {
        $group = $hook->groupinstance;
        $course = get_course($group->courseid);
        base::invalidate_all_session_caches_for_course($course);
    }

    /**
     * Remove members from the room when a member is removed from group room.
     *
     * @param after_group_membership_removed $hook The group membership removed hook.
     */
    public static function remove_members_from_group(
        after_group_membership_removed $hook,
    ): void {
        $group = $hook->groupinstance;
        $course = get_course($group->courseid);
        base::invalidate_all_session_caches_for_course($course);
    }

    /**
     * Redirect to external course url using the before_course_viewed hook.
     *
     * @param before_course_viewed $hook The hook object containing course data.
     * @deprecated since 5.0
     * @todo MDL-83839 This method will be removed in Moodle 6.0.
     */
    #[\core\attribute\deprecated('core_courseformat\hook\hook_listener::before_course_viewed', since: '5.0', mdl: 'MDL-83764')]
    public static function before_course_viewed(before_course_viewed $hook): void {
        global $CFG;
        if (file_exists($CFG->dirroot . '/course/externservercourse.php')) {
            \core\deprecation::emit_deprecation([self::class, __FUNCTION__]);
            include($CFG->dirroot . '/course/externservercourse.php');
            if (function_exists('extern_server_course')) {
                if ($externurl = extern_server_course($hook->course)) {
                    redirect($externurl);
                }
            }
        }
    }

    /**
     * Reset cache for the current user in a course when a course content is updated.
     *
     * @param after_course_content_updated $hook The course module created hook.
     */
    public static function course_content_updated(
        after_course_content_updated $hook,
    ): void {
        base::session_cache_reset($hook->course);
    }

    /**
     * Reset cache for the current user in a course when a role is switched.
     *
     * @param \core\hook\access\after_role_switched $hook The role switched hook.
     */
    public static function after_role_switched(
        \core\hook\access\after_role_switched $hook,
    ): void {
        if ($coursecontext = $hook->context->get_course_context()) {
            base::session_cache_reset(get_course($coursecontext->instanceid));
        }
    }

    /**
     * Reset cache for the current user when the course completion has been updated.
     *
     * @param \core_completion\hook\after_cm_completion_updated $hook The role switched hook.
     */
    public static function after_cm_completion_updated(
        \core_completion\hook\after_cm_completion_updated $hook,
    ): void {
        // The activity completion alters the course state cache for this particular user.
        $course = get_course($hook->cm->course);
        if ($course) {
            base::session_cache_reset($course);
        }
    }

    /**
     * Add a sticky footer with linear navigation content on activity pages when linear navigation
     * is enabled for the course format, unless there is already a sticky footer on the page.
     *
     * @param \core\hook\output\before_footer_html_generation $hook
     */
    public static function add_course_navigation_sticky_footer(
        \core\hook\output\before_footer_html_generation $hook,
    ): void {
        $page = $hook->renderer->get_page();
        if ($page->cm === null) {
            // Not on an activity page, do not add the sticky footer.
            return;
        }
        if ($page->has_sticky_footer()) {
            // If there is already a sticky footer, do not add another one.
            return;
        }
        if (!$page->should_show_navigation_footer()) {
            // If the page should not show the navigation footer, do not add the sticky footer.
            return;
        }

        $format = \course_get_format($page->course);
        $supplementarycontent = $page->get_supplementary_content();
        if (!$format->uses_linear_navigation() && $supplementarycontent === null) {
            // Only add the sticky footer for course formats using linear navigation or
            // if there is supplementary content to be added.
            return;
        }
        $formatoptions = $format->get_format_options();
        $linearnavigationenabled = ($formatoptions[linearnavigationsettings::SETTING_ENABLE_LINEAR_NAV] ?? false);
        if (!$linearnavigationenabled && $supplementarycontent === null) {
            // Linear navigation is not enabled and there is no supplementary content, do not add the sticky footer.
            return;
        }

        $stickyfootercontent = '';
        if ($linearnavigationenabled) {
            // Add the sticky footer with the linear navigation content.
            $linearnavigationcontent = new output\local\linearnavigation\footer_content($page->cm->id);
            $stickyfootercontent = $hook->renderer->render($linearnavigationcontent);
        }
        $footer = new supplementary_sticky_footer(
            $stickyfootercontent,
            'course-linear-navigation',
        );
        if ($supplementarycontent !== null) {
            $footer->add_supplementary_content($supplementarycontent);
        }
        $hook->add_html($hook->renderer->render($footer));
    }
}
