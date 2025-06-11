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

namespace theme_snap;
use cache_helper;
use core\event\course_updated;
use core\event\course_deleted;
use core\event\course_completion_updated;
use core\event\course_module_created;
use core\event\course_module_updated;
use core\event\course_module_deleted;
use core\event\course_module_completion_updated;
use core\event\user_deleted;
use core\event\user_updated;
use core\event\base;
use core\event\role_assigned;
use core\event\role_unassigned;
use core\event\user_enrolment_deleted;
use core\event\group_member_added;
use core\event\group_member_removed;

/**
 * Event handlers.
 *
 * This class contains all the event handlers used by Snap.
 *
 * @package   theme_snap
 * @copyright Copyright (c) 2015 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class event_handlers {

    /**
     * The course update event.
     *
     * process cover image.
     *
     * @param course_updated $event
     * @return void
     */
    public static function course_updated(course_updated $event) {
        $context = \context_course::instance($event->objectid);

        local::process_coverimage($context);
        local::clean_course_card_bg_image_cache($event->contextid);
        $cache = \cache::make('theme_snap', 'course_users_assign_ungraded');
        $cache->delete($context->instanceid);
        $cache = \cache::make('theme_snap', 'course_users_quiz_ungraded');
        $cache->delete($context->instanceid);
    }

    /**
     * The course delete event.
     *
     * Delete course favorite records when course is deleted.
     *
     * @param course_deleted $event
     */
    public static function course_deleted(course_deleted $event) {
        global $DB;

        $select = ['itemid' => $event->objectid, 'component' => 'core_course'];
        $DB->delete_records('favourite', $select);

        local::clean_course_card_bg_image_cache($event->contextid);
        local::clean_course_card_teacher_avatar_cache($event->contextid);
        $cache = \cache::make('theme_snap', 'course_users_assign_ungraded');
        $cache->delete($event->objectid);
        $cache = \cache::make('theme_snap', 'course_users_quiz_ungraded');
        $cache->delete($event->objectid);
    }

    /**
     * The user delete event.
     *
     * Delete course favorite records when an user is deleted.
     *
     * @param user_deleted $event
     */
    public static function user_deleted($event) {
        global $DB;

        $select = ['userid' => $event->objectid, 'component' => 'core_course'];
        $DB->delete_records('favourite', $select);

        local::clean_course_card_teacher_avatar_cache(null, $event->objectid);
    }

    /**
     * Update course grading / completion time stamp for course affected by event.
     * @param course_completion_updated $event
     */
    public static function course_completion_updated(course_completion_updated $event) {
        // Force an update of affected cache stamps.
        local::course_completion_cachestamp($event->courseid, true);
    }

    /**
     * Update course grading / completion time stamp for course affected by event.
     * @param course_module_created $event
     */
    public static function course_module_created(course_module_created $event) {
        // Force an update of affected cache stamps.
        local::course_completion_cachestamp($event->courseid, true);
    }

    /**
     * Update course grading / completion time stamp for course affected by event.
     * @param course_module_updated $event
     */
    public static function course_module_updated(course_module_updated $event) {
        // Force an update of affected cache stamps.
        local::course_completion_cachestamp($event->courseid, true);
    }

    /**
     * Update course grading / completion time stamp for course affected by event.
     * @param course_module_deleted $event
     */
    public static function course_module_deleted(course_module_deleted $event) {
        // Force an update of affected cache stamps.
        local::course_completion_cachestamp($event->courseid, true);
    }

    /**
     * Purge session level cache for affected course.
     * @param course_module_completion_updated $event
     */
    public static function course_module_completion_updated(course_module_completion_updated $event) {
        // Force an update for the specific course and user effected by this completion event.
        local::course_user_completion_cachestamp($event->courseid, $event->relateduserid, true);
    }

    /**
     * Record calendar change for affected course.
     * @param base $event
     */
    public static function calendar_change(base $event) {
        local::add_calendar_change_stamp($event->courseid);
    }

    /**
     * The user update event.
     *
     * Removes cache value for this user Profile based branding CSS class.
     *
     * @param user_updated $event
     */
    public static function user_updated($event) {
        $cache = \cache::make('theme_snap', 'profile_based_branding');
        $cache->delete('pbb_class');

        local::clean_course_card_teacher_avatar_cache(null, $event->userid);
    }

    /**
     * Handles this kind of event.
     * @param role_assigned $event
     */
    public static function role_assigned(role_assigned $event) {
        $context = \context::instance_by_id($event->contextid, MUST_EXIST);
        if ($context->contextlevel != CONTEXT_COURSE) {
            return;
        }

        // Too many checks need to be done for determining if the new user is a
        // course contact. Purging all avatars just in case.
        local::clean_course_card_teacher_avatar_cache($context->id);
        $cache = \cache::make('theme_snap', 'course_users_assign_ungraded');
        $cache->delete($context->instanceid);
        $cache = \cache::make('theme_snap', 'course_users_quiz_ungraded');
        $cache->delete($context->instanceid);
    }

    /**
     * Handles this kind of event.
     * @param role_unassigned $event
     */
    public static function role_unassigned(role_unassigned $event) {
        $context = \context::instance_by_id($event->contextid, MUST_EXIST);
        if ($context->contextlevel != CONTEXT_COURSE) {
            return;
        }

        // Too many checks need to be done for determining if the user continues being a
        // course contact. Purging all avatars if user is in course avatar index just in case.
        local::clean_course_card_teacher_avatar_cache(
            $context->id,
            $event->relateduserid
        );
        $cache = \cache::make('theme_snap', 'course_users_assign_ungraded');
        $cache->delete($context->instanceid);
        $cache = \cache::make('theme_snap', 'course_users_quiz_ungraded');
        $cache->delete($context->instanceid);
    }

    /**
     * Triggered via user_enrolment_deleted event.
     *
     * @param user_enrolment_deleted $event
     */
    public static function user_enrolment_deleted(user_enrolment_deleted $event) {
        $context = \context::instance_by_id($event->contextid, MUST_EXIST);
        if ($context->contextlevel != CONTEXT_COURSE) {
            return;
        }

        // Too many checks need to be done for determining if the user continues being a
        // course contact. Purging all avatars if user is in course avatar index just in case.
        local::clean_course_card_teacher_avatar_cache(
            $context->id,
            $event->relateduserid
        );
        $cache = \cache::make('theme_snap', 'course_users_assign_ungraded');
        $cache->delete($context->instanceid);
        $cache = \cache::make('theme_snap', 'course_users_quiz_ungraded');
        $cache->delete($context->instanceid);
    }

    /**
     * This group member event may make activity_deadlines cache invalid.
     * @param group_member_added $event
     */
    public static function group_member_added(group_member_added $event) {
        $context = \context::instance_by_id($event->contextid, MUST_EXIST);
        if ($context->contextlevel != CONTEXT_COURSE) {
            return;
        }

        cache_helper::purge_by_event('groupmemberschanged');
    }

    /**
     * This group member event may make activity_deadlines cache invalid.
     * @param group_member_removed $event
     */
    public static function group_member_removed(group_member_removed $event) {
        $context = \context::instance_by_id($event->contextid, MUST_EXIST);
        if ($context->contextlevel != CONTEXT_COURSE) {
            return;
        }

        cache_helper::purge_by_event('groupmemberschanged');
    }
}
