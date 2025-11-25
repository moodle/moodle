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

namespace core_communication;

use context_course;
use core\hook\access\after_role_assigned;
use core\hook\access\after_role_unassigned;
use core_enrol\hook\before_enrol_instance_deleted;
use core_enrol\hook\after_enrol_instance_status_updated;
use core_enrol\hook\after_user_enrolled;
use core_enrol\hook\before_user_enrolment_updated;
use core_enrol\hook\before_user_enrolment_removed;
use core_course\hook\after_course_created;
use core_course\hook\before_course_deleted;
use core_course\hook\after_course_updated;
use core_group\hook\after_group_created;
use core_group\hook\after_group_deleted;
use core_group\hook\after_group_membership_added;
use core_group\hook\after_group_membership_removed;
use core_group\hook\after_group_updated;
use core_user\hook\before_user_deleted;
use core_user\hook\before_user_updated;

/**
 * Hook listener for communication api.
 *
 * @package    core_communication
 * @copyright  2023 Safat Shahin <safat.shahin@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class hook_listener {

    /**
     * Get the course and group object for the group hook.
     *
     * @param mixed $hook The hook object.
     * @return array
     */
    protected static function get_group_and_course_data_for_group_hook(mixed $hook): array {
        $group = $hook->groupinstance;
        $course = helper::get_course(
            courseid: $group->courseid,
        );

        return [
            $group,
            $course,
        ];
    }

    /**
     * Communication api call to create room for a group if course has group mode enabled.
     *
     * @param after_group_created $hook The group created hook.
     */
    public static function create_group_communication(
        after_group_created $hook,
    ): void {
        [$group, $course] = self::get_group_and_course_data_for_group_hook(
            hook: $hook,
        );

        // Check if group mode enabled before handling the communication.
        if (!helper::is_group_mode_enabled_for_course(course: $course)) {
            return;
        }

        $coursecontext = \context_course::instance(courseid: $course->id);
        // Get the course communication instance to set the provider.
        $coursecommunication = helper::load_by_course(
            courseid: $course->id,
            context: $coursecontext,
        );

        // Check we have communication correctly set up before proceeding.
        if ($coursecommunication->get_processor() === null) {
            return;
        }

        $communication = api::load_by_instance(
            context: $coursecontext,
            component: constants::GROUP_COMMUNICATION_COMPONENT,
            instancetype: constants::GROUP_COMMUNICATION_INSTANCETYPE,
            instanceid: $group->id,
            provider: $coursecommunication->get_provider(),
        );

        $communicationroomname = helper::format_group_room_name(
            baseroomname: $coursecommunication->get_room_name(),
            groupname: $group->name,
        );

        $communication->create_and_configure_room(
            communicationroomname: $communicationroomname,
            instance: $course,
        );

        // As it's a new group, we need to add the users with all access group role to the room.
        $enrolledusers = helper::get_enrolled_users_for_course(course: $course);
        $userstoadd = helper::get_users_has_access_to_all_groups(
            userids: $enrolledusers,
            courseid: $course->id,
        );
        $communication->add_members_to_room(
            userids: $userstoadd,
            queue: false,
        );
    }

    /**
     * Communication api call to update room for a group if course has group mode enabled.
     *
     * @param after_group_updated $hook The group updated hook.
     */
    public static function update_group_communication(
        after_group_updated $hook,
    ): void {
        [$group, $course] = self::get_group_and_course_data_for_group_hook(
            hook: $hook,
        );

        // Check if group mode enabled before handling the communication.
        if (!helper::is_group_mode_enabled_for_course(course: $course)) {
            return;
        }

        $coursecontext = \context_course::instance(courseid: $course->id);
        $communication = helper::load_by_group(
            groupid: $group->id,
            context: $coursecontext,
        );

        // Get the course communication instance so we can extract the base room name.
        $coursecommunication = helper::load_by_course(
            courseid: $course->id,
            context: $coursecontext,
        );

        $communicationroomname = helper::format_group_room_name(
            baseroomname: $coursecommunication->get_room_name(),
            groupname: $group->name,
        );

        // If the name didn't change, then we don't need to update the room.
        if ($communicationroomname === $communication->get_room_name()) {
            return;
        }

        $communication->update_room(
            active: processor::PROVIDER_ACTIVE,
            communicationroomname: $communicationroomname,
            instance: $course,
        );
    }

    /**
     * Delete the communication room for a group if course has group mode enabled.
     *
     * @param after_group_deleted $hook The group deleted hook.
     */
    public static function delete_group_communication(
        after_group_deleted $hook
    ): void {
        [$group, $course] = self::get_group_and_course_data_for_group_hook(
            hook: $hook,
        );

        // Check if group mode enabled before handling the communication.
        if (!helper::is_group_mode_enabled_for_course(course: $course)) {
            return;
        }

        $context = context_course::instance($course->id);
        $communication = helper::load_by_group(
            groupid: $group->id,
            context: $context,
        );
        $communication->delete_room();
    }

    /**
     * Add members to group room when a new member is added to the group.
     *
     * @param after_group_membership_added $hook The group membership added hook.
     */
    public static function add_members_to_group_room(
        after_group_membership_added $hook,
    ): void {
        [$group, $course] = self::get_group_and_course_data_for_group_hook(
            hook: $hook,
        );

        // Check if group mode enabled before handling the communication.
        if (!helper::is_group_mode_enabled_for_course(course: $course)) {
            return;
        }

        $context = context_course::instance($course->id);
        $communication = helper::load_by_group(
            groupid: $group->id,
            context: $context,
        );

        // Filter out users who are not active in this course.
        $enrolledusers = helper::get_enrolled_users_for_course($course, true);
        $userids = array_intersect($hook->userids, $enrolledusers);

        $communication->add_members_to_room(
            userids: $userids,
        );
    }

    /**
     * Remove members from the room when a member is removed from group room.
     *
     * @param after_group_membership_removed $hook The group membership removed hook.
     */
    public static function remove_members_from_group_room(
        after_group_membership_removed $hook,
    ): void {
        [$group, $course] = self::get_group_and_course_data_for_group_hook(
            hook: $hook,
        );

        // Check if group mode enabled before handling the communication.
        if (!helper::is_group_mode_enabled_for_course(course: $course)) {
            return;
        }

        $context = context_course::instance($course->id);
        $communication = helper::load_by_group(
            groupid: $group->id,
            context: $context,
        );
        $communication->remove_members_from_room(
            userids: $hook->userids,
        );
    }

    /**
     * Create course communication instance.
     *
     * @param after_course_created $hook The course created hook.
     */
    public static function create_course_communication(
        after_course_created $hook,
    ): void {
        // If the communication subsystem is not enabled then just ignore.
        if (!api::is_available()) {
            return;
        }

        $course = $hook->course;

        // Check for default provider config setting.
        $defaultprovider = get_config(
            plugin: 'moodlecourse',
            name: 'coursecommunicationprovider',
        );
        $provider = $course->selectedcommunication ?? $defaultprovider;

        if (empty($provider) || $provider === processor::PROVIDER_NONE) {
            return;
        }

        // Check for group mode, we will have to get the course data again as the group info is not always in the object.
        $createcourseroom = true;
        $creategrouprooms = false;
        $coursedata = get_course(courseid: $course->id);
        $groupmode = $course->groupmode ?? $coursedata->groupmode;
        if ((int)$groupmode !== NOGROUPS) {
            $createcourseroom = false;
            $creategrouprooms = true;
        }

        // Prepare the communication api data.
        $courseimage = course_get_courseimage(course: $course);
        $communicationroomname = !empty($course->communicationroomname) ? $course->communicationroomname : $coursedata->fullname;
        $coursecontext = \context_course::instance(courseid: $course->id);
        // Communication api call for course communication.
        $communication = \core_communication\api::load_by_instance(
            context: $coursecontext,
            component: constants::COURSE_COMMUNICATION_COMPONENT,
            instancetype: constants::COURSE_COMMUNICATION_INSTANCETYPE,
            instanceid: $course->id,
            provider: $provider,
        );
        $communication->create_and_configure_room(
            communicationroomname: $communicationroomname,
            avatar: $courseimage,
            instance: $course,
            queue: $createcourseroom,
        );

        // Communication api call for group communication.
        if ($creategrouprooms) {
            helper::update_group_communication_instances_for_course(
                course: $course,
                provider: $provider,
            );
        } else {
            $enrolledusers = helper::get_enrolled_users_for_course(course: $course);
            $communication->add_members_to_room(
                userids: $enrolledusers,
                queue: false,
            );
        }
    }

    /**
     * Update the course communication instance.
     *
     * @param after_course_updated $hook The course updated hook.
     */
    public static function update_course_communication(
        after_course_updated $hook,
    ): void {
        // If the communication subsystem is not enabled then just ignore.
        if (!api::is_available()) {
            return;
        }
        $course = $hook->course;
        $oldcourse = $hook->oldcourse;
        $changeincoursecat = $hook->changeincoursecat;
        $groupmode = $course->groupmode ?? get_course($course->id)->groupmode;
        if ($changeincoursecat || $groupmode !== $oldcourse->groupmode) {
            helper::update_course_communication_instance(
                course: $course,
                changesincoursecat: $changeincoursecat,
            );
        }
    }

    /**
     * Delete course communication data and remove members.
     * Course can have communication data if it is a group or a course.
     * This action is important to perform even if the experimental feature is disabled.
     *
     * @param before_course_deleted $hook The course deleted hook.
     */
    public static function delete_course_communication(
        before_course_deleted $hook,
    ): void {
        // If the communication subsystem is not enabled then just ignore.
        if (!api::is_available()) {
            return;
        }

        $course = $hook->course;
        $groupmode = $course->groupmode ?? get_course(courseid: $course->id)->groupmode;
        $coursecontext = \context_course::instance(courseid: $course->id);

        // If group mode is not set then just handle the course communication room.
        if ((int)$groupmode === NOGROUPS) {
            $communication = helper::load_by_course(
                courseid: $course->id,
                context: $coursecontext,
            );
            $communication->delete_room();
        } else {
            // If group mode is set then handle the group communication rooms.
            $coursegroups = groups_get_all_groups(courseid: $course->id);
            foreach ($coursegroups as $coursegroup) {
                $communication = helper::load_by_group(
                    groupid: $coursegroup->id,
                    context: $coursecontext,
                );
                $communication->delete_room();
            }
        }
    }

    /**
     * Update the room membership for the user updates.
     *
     * @param before_user_updated $hook The user updated hook.
     */
    public static function update_user_room_memberships(
        before_user_updated $hook,
    ): void {
        // If the communication subsystem is not enabled then just ignore.
        if (!api::is_available()) {
            return;
        }

        $user = $hook->user;
        $currentuserrecord = $hook->currentuserdata;

        // Get the user courses.
        $usercourses = enrol_get_users_courses(userid: $user->id);

        // If the user is suspended then remove the user from all the rooms.
        // Otherwise add the user to all the rooms for the courses the user enrolled in.
        if (!empty($currentuserrecord) && isset($user->suspended) && $currentuserrecord->suspended !== $user->suspended) {
            // Decide the action for the communication api for the user.
            $memberaction = ($user->suspended === 0) ? 'add_members_to_room' : 'remove_members_from_room';
            foreach ($usercourses as $usercourse) {
                helper::update_course_communication_room_membership(
                    course: $usercourse,
                    userids: [$user->id],
                    memberaction: $memberaction,
                );
            }
        }
    }

    /**
     * Delete all room memberships for a user.
     *
     * @param before_user_deleted $hook The user deleted hook.
     */
    public static function delete_user_room_memberships(
        before_user_deleted $hook,
    ): void {
        // If the communication subsystem is not enabled then just ignore.
        if (!api::is_available()) {
            return;
        }

        $user = $hook->user;

        foreach (enrol_get_users_courses(userid: $user->id) as $course) {
            $groupmode = $course->groupmode ?? get_course(courseid: $course->id)->groupmode;
            $coursecontext = \context_course::instance(courseid: $course->id);

            if ((int)$groupmode === NOGROUPS) {
                $communication = helper::load_by_course(
                    courseid: $course->id,
                    context: $coursecontext,
                );
                $processor = $communication->get_processor();
                if ($processor?->supports_room_user_features()) {
                    $communication->get_room_user_provider()->remove_members_from_room(userids: [$user->id]);
                    $processor->delete_instance_user_mapping(userids: [$user->id]);
                }
            } else {
                // If group mode is set then handle the group communication rooms.
                $coursegroups = groups_get_all_groups(courseid: $course->id);
                foreach ($coursegroups as $coursegroup) {
                    $communication = helper::load_by_group(
                        groupid: $coursegroup->id,
                        context: $coursecontext,
                    );
                    $processor = $communication->get_processor();
                    if ($processor?->supports_room_user_features()) {
                        $communication->get_room_user_provider()->remove_members_from_room(userids: [$user->id]);
                        $processor->delete_instance_user_mapping(userids: [$user->id]);
                    }

                }
            }
        }
    }

    /**
     * Update the room membership of the user for role assigned in a course.
     *
     * @param after_role_assigned|after_role_unassigned $hook
     */
    public static function update_user_membership_for_role_changes(
        after_role_assigned|after_role_unassigned $hook,
    ): void {
        // If the communication subsystem is not enabled then just ignore.
        if (!api::is_available()) {
            return;
        }

        $context = $hook->context;
        if ($coursecontext = $context->get_course_context(strict: false)) {
            helper::update_course_communication_room_membership(
                course: get_course(courseid: $coursecontext->instanceid),
                userids: [$hook->userid],
                memberaction: 'update_room_membership',
            );
        }
    }

    /**
     * Update the communication memberships for enrol status change.
     *
     * @param after_enrol_instance_status_updated $hook The enrol status updated hook.
     */
    public static function update_communication_memberships_for_enrol_status_change(
        after_enrol_instance_status_updated $hook,
    ): void {
        // If the communication subsystem is not enabled then just ignore.
        if (!api::is_available()) {
            return;
        }

        $enrolinstance = $hook->enrolinstance;
        // No need to do anything for guest instances.
        if ($enrolinstance->enrol === 'guest') {
            return;
        }

        $newstatus = $hook->newstatus;
        // Check if a valid status is given.
        if (
            $newstatus !== ENROL_INSTANCE_ENABLED ||
            $newstatus !== ENROL_INSTANCE_DISABLED
        ) {
            return;
        }

        // Check if the status provided is valid.
        switch ($newstatus) {
            case ENROL_INSTANCE_ENABLED:
                $action = 'add_members_to_room';
                break;
            case ENROL_INSTANCE_DISABLED:
                $action = 'remove_members_from_room';
                break;
            default:
                return;
        }

        global $DB;
        $instanceusers = $DB->get_records(
            table: 'user_enrolments',
            conditions: ['enrolid' => $enrolinstance->id, 'status' => ENROL_USER_ACTIVE],
        );
        $enrolledusers = array_column($instanceusers, 'userid');
        helper::update_course_communication_room_membership(
            course: get_course(courseid: $enrolinstance->courseid),
            userids: $enrolledusers,
            memberaction: $action,
        );
    }

    /**
     * Remove the communication instance memberships when an enrolment instance is deleted.
     *
     * @param before_enrol_instance_deleted $hook The enrol instance deleted hook.
     */
    public static function remove_communication_memberships_for_enrol_instance_deletion(
        before_enrol_instance_deleted $hook,
    ): void {
        // If the communication subsystem is not enabled then just ignore.
        if (!api::is_available()) {
            return;
        }

        $enrolinstance = $hook->enrolinstance;
        // No need to do anything for guest instances.
        if ($enrolinstance->enrol === 'guest') {
            return;
        }

        global $DB;
        $instanceusers = $DB->get_records(
            table: 'user_enrolments',
            conditions: ['enrolid' => $enrolinstance->id, 'status' => ENROL_USER_ACTIVE],
        );
        $enrolledusers = array_column($instanceusers, 'userid');
        helper::update_course_communication_room_membership(
            course: get_course(courseid: $enrolinstance->courseid),
            userids: $enrolledusers,
            memberaction: 'remove_members_from_room',
        );
    }

    /**
     * Add communication instance membership for an enrolled user.
     *
     * @param after_user_enrolled $hook The user enrolled hook.
     */
    public static function add_communication_membership_for_enrolled_user(
        after_user_enrolled $hook,
    ): void {
        // If the communication subsystem is not enabled then just ignore.
        if (!api::is_available()) {
            return;
        }

        $enrolinstance = $hook->enrolinstance;
        // No need to do anything for guest instances.
        if ($enrolinstance->enrol === 'guest') {
            return;
        }

        helper::update_course_communication_room_membership(
            course: get_course($enrolinstance->courseid),
            userids: [$hook->get_userid()],
            memberaction: 'add_members_to_room',
        );
    }

    /**
     * Update the communication instance membership for the user enrolment updates.
     *
     * @param before_user_enrolment_updated $hook The user enrolment updated hook.
     */
    public static function update_communication_membership_for_updated_user_enrolment(
        before_user_enrolment_updated $hook,
    ): void {
        // If the communication subsystem is not enabled then just ignore.
        if (!api::is_available()) {
            return;
        }

        $enrolinstance = $hook->enrolinstance;
        // No need to do anything for guest instances.
        if ($enrolinstance->enrol === 'guest') {
            return;
        }

        $userenrolmentinstance = $hook->userenrolmentinstance;
        $statusmodified = $hook->statusmodified;
        $timeendmodified = $hook->timeendmodified;

        if (
            ($statusmodified && ((int) $userenrolmentinstance->status === 1)) ||
            ($timeendmodified && $userenrolmentinstance->timeend !== 0 && (time() > $userenrolmentinstance->timeend))
        ) {
            $action = 'remove_members_from_room';
        } else {
            $action = 'add_members_to_room';
        }

        helper::update_course_communication_room_membership(
            course: get_course($enrolinstance->courseid),
            userids: [$hook->get_userid()],
            memberaction: $action,
        );
    }

    /**
     * Remove communication instance membership for an enrolled user.
     *
     * @param before_user_enrolment_removed $hook The user unenrolled hook.
     */
    public static function remove_communication_membership_for_unenrolled_user(
        before_user_enrolment_removed $hook,
    ): void {
        // If the communication subsystem is not enabled then just ignore.
        if (!api::is_available()) {
            return;
        }

        $enrolinstance = $hook->enrolinstance;
        // No need to do anything for guest instances.
        if ($enrolinstance->enrol === 'guest') {
            return;
        }

        helper::update_course_communication_room_membership(
            course: get_course($enrolinstance->courseid),
            userids: [$hook->get_userid()],
            memberaction: 'remove_members_from_room',
        );
    }
}
