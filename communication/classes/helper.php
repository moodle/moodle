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

use context;
use stdClass;

/**
 * Helper method for communication.
 *
 * @package    core_communication
 * @copyright  2023 Safat Shahin <safat.shahin@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class helper {

    /**
     * Load the communication instance for group id.
     *
     * @param int $groupid The group id
     * @param context $context The context, to make sure any instance using group can load the communication instance
     * @return api The communication instance.
     */
    public static function load_by_group(int $groupid, context $context): api {
        return \core_communication\api::load_by_instance(
            context: $context,
            component: constants::GROUP_COMMUNICATION_COMPONENT,
            instancetype: constants::GROUP_COMMUNICATION_INSTANCETYPE,
            instanceid: $groupid,
        );
    }

    /**
     * Load the communication instance for course id.
     *
     * @param int $courseid The course id
     * @param \context $context The context
     * @param string|null $provider The provider name
     * @return api The communication instance
     */
    public static function load_by_course(
        int $courseid,
        \context $context,
        ?string $provider = null,
    ): api {
        return \core_communication\api::load_by_instance(
            context: $context,
            component: constants::COURSE_COMMUNICATION_COMPONENT,
            instancetype: constants::COURSE_COMMUNICATION_INSTANCETYPE,
            instanceid: $courseid,
            provider: $provider,
        );
    }

    /**
     * Communication api call to create room for a group if course has group mode enabled.
     *
     * @param int $courseid The course id.
     * @return stdClass
     */
    public static function get_course(int $courseid): stdClass {
        global $DB;
        return $DB->get_record(
            table: 'course',
            conditions: ['id' => $courseid],
            strictness: MUST_EXIST,
        );
    }

    /**
     * Is group mode enabled for the course.
     *
     * @param stdClass $course The course object
     */
    public static function is_group_mode_enabled_for_course(stdClass $course): bool {
        // If the communication subsystem is not enabled then just ignore.
        if (!api::is_available()) {
            return false;
        }

        $groupmode = $course->groupmode ?? get_course(courseid: $course->id)->groupmode;
        return (int)$groupmode !== NOGROUPS;
    }

    /**
     * Helper to update room membership according to action passed.
     * This method will help reduce a large amount of duplications of code in different places in core.
     *
     * @param \stdClass $course The course object.
     * @param array $userids The user ids to add to the communication room.
     * @param string $memberaction The action to perform on the communication room.
     */
    public static function update_course_communication_room_membership(
        \stdClass $course,
        array $userids,
        string $memberaction,
    ): void {
        // If the communication subsystem is not enabled then just ignore.
        if (!api::is_available()) {
            return;
        }

        // Validate communication api action.
        $roomuserprovider = new \ReflectionClass(room_user_provider::class);
        if (!$roomuserprovider->hasMethod($memberaction)) {
            throw new \coding_exception('Invalid action provided.');
        }

        $coursecontext = \context_course::instance(courseid: $course->id);

        $communication = self::load_by_course(
            courseid: $course->id,
            context: $coursecontext,
        );

        // Check we have communication correctly set up before proceeding.
        if ($communication->get_processor() === null) {
            return;
        }

        // Get the group mode for this course.
        $groupmode = $course->groupmode ?? get_course(courseid: $course->id)->groupmode;

        if ((int)$groupmode === NOGROUPS) {
            // If group mode is not set, then just handle the update normally for these users.
            $communication->$memberaction($userids);
        } else {
            // If group mode is set, then handle the update for these users with repect to the group they are in.
            $coursegroups = groups_get_all_groups(courseid: $course->id);

            $usershandled = [];

            // Filter all the users that have the capability to access all groups.
            $allaccessgroupusers = self::get_users_has_access_to_all_groups(
                userids: $userids,
                courseid: $course->id,
            );

            foreach ($coursegroups as $coursegroup) {

                // Get all group members.
                $groupmembers = groups_get_members(groupid: $coursegroup->id, fields: 'u.id');
                $groupmembers = array_column($groupmembers, 'id');

                // Find the common user ids between the group members and incoming userids.
                $groupuserstohandle = array_intersect(
                    $groupmembers,
                    $userids,
                );

                // Add users who have the capability to access this group (and haven't been added already).
                foreach ($allaccessgroupusers as $allaccessgroupuser) {
                    if (!in_array($allaccessgroupuser, $groupuserstohandle, true)) {
                        $groupuserstohandle[] = $allaccessgroupuser;
                    }
                }

                // Keep track of the users we have handled already.
                $usershandled = array_merge($usershandled, $groupuserstohandle);

                // Let's check if we need to add/remove members from room because of a role change.
                // First, get all the instance users for this group.
                $communication = self::load_by_group(
                    groupid: $coursegroup->id,
                    context: $coursecontext,
                );
                $instanceusers = $communication->get_processor()->get_all_userids_for_instance();

                // The difference between the instance users and the group members are the ones we want to check.
                $roomuserstocheck = array_diff(
                    $instanceusers,
                    $groupmembers
                );

                if (!empty($roomuserstocheck)) {
                    // Check if they still have the capability to keep their access in the room.
                    $userslostcaps = array_diff(
                        $roomuserstocheck,
                        self::get_users_has_access_to_all_groups(
                            userids: $roomuserstocheck,
                            courseid: $course->id,
                        ),
                    );
                    // Remove users who no longer have the capability.
                    if (!empty($userslostcaps)) {
                        $communication->remove_members_from_room(userids: $userslostcaps);
                    }
                }

                // Check if we have to add any room members who have gained the capability.
                $usersgainedcaps = array_diff(
                    $allaccessgroupusers,
                    $instanceusers,
                );

                // If we have users, add them to the room.
                if (!empty($usersgainedcaps)) {
                    $communication->add_members_to_room(userids: $usersgainedcaps);
                }

                // Finally, trigger the update task for the users who need to be handled.
                $communication->$memberaction($groupuserstohandle);
            }

            // If the user was not in any group, but an update/remove action was requested for the user,
            // then the user must have had a role with the capablity, but made a regular user.
            $usersnothandled = array_diff($userids, $usershandled);

            // These users are not handled and not in any group, so logically these users lost their permission to stay in the room.
            foreach ($coursegroups as $coursegroup) {
                $communication = self::load_by_group(
                    groupid: $coursegroup->id,
                    context: $coursecontext,
                );
                $communication->remove_members_from_room(userids: $usersnothandled);
            }
        }
    }

    /**
     * Get users with the capability to access all groups.
     *
     * @param array $userids user ids to check the permission
     * @param int $courseid course id
     * @return array of userids
     */
    public static function get_users_has_access_to_all_groups(
        array $userids,
        int $courseid
    ): array {
        $allgroupsusers = [];
        $context = \context_course::instance(courseid: $courseid);

        foreach ($userids as $userid) {
            if (
                has_capability(
                    capability: 'moodle/site:accessallgroups',
                    context: $context,
                    user: $userid,
                )
            ) {
                $allgroupsusers[] = $userid;
            }
        }

        return $allgroupsusers;
    }

    /**
     * Get the course communication url according to course setup.
     *
     * @param stdClass $course The course object.
     * @return string The communication room url.
     */
    public static function get_course_communication_url(stdClass $course): string {
        // If it's called from site context, then just return.
        if ($course->id === SITEID) {
            return '';
        }

        // If the communication subsystem is not enabled then just ignore.
        if (!api::is_available()) {
            return '';
        }

        $url = '';
        // Get the group mode for this course.
        $groupmode = $course->groupmode ?? get_course(courseid: $course->id)->groupmode;
        $coursecontext = \context_course::instance(courseid: $course->id);

        // If group mode is not set then just handle the course communication for these users.
        if ((int)$groupmode === NOGROUPS) {
            $communication = self::load_by_course(
                courseid: $course->id,
                context: $coursecontext,
            );
            $url = $communication->get_communication_room_url();
        } else {
            // If group mode is set then handle the group communication rooms for these users.
            $coursegroups = groups_get_all_groups(courseid: $course->id);
            $numberofgroups = count($coursegroups);

            // If no groups available, nothing to show.
            if ($numberofgroups === 0) {
                return '';
            }

            $readygroups = [];

            foreach ($coursegroups as $coursegroup) {
                $communication = self::load_by_group(
                    groupid: $coursegroup->id,
                    context: $coursecontext,
                );
                $roomstatus = $communication->get_communication_room_url() ? 'ready' : 'pending';
                if ($roomstatus === 'ready') {
                    $readygroups[$communication->get_processor()->get_id()] = $communication->get_communication_room_url();
                }
            }
            if (!empty($readygroups)) {
                $highestkey = max(array_keys($readygroups));
                $url = $readygroups[$highestkey];
            }
        }

        return empty($url) ? '' : $url;
    }

    /**
     * Get the enrolled users for course.
     *
     * @param stdClass $course The course object.
     * @return array
     */
    public static function get_enrolled_users_for_course(stdClass $course): array {
        global $CFG;
        require_once($CFG->libdir . '/enrollib.php');
        return array_column(
            enrol_get_course_users(courseid: $course->id),
            'id',
        );
    }

    /**
     * Get the course communication status notification for course.
     *
     * @param \stdClass $course The course object.
     */
    public static function get_course_communication_status_notification(\stdClass $course): void {
        // If the communication subsystem is not enabled then just ignore.
        if (!api::is_available()) {
            return;
        }

        // Get the group mode for this course.
        $groupmode = $course->groupmode ?? get_course(courseid: $course->id)->groupmode;
        $coursecontext = \context_course::instance(courseid: $course->id);

        // If group mode is not set then just handle the course communication for these users.
        if ((int)$groupmode === NOGROUPS) {
            $communication = self::load_by_course(
                courseid: $course->id,
                context: $coursecontext,
            );
            $communication->show_communication_room_status_notification();
        } else {
            // If group mode is set then handle the group communication rooms for these users.
            $coursegroups = groups_get_all_groups(courseid: $course->id);
            $numberofgroups = count($coursegroups);

            // If no groups available, nothing to show.
            if ($numberofgroups === 0) {
                return;
            }

            $numberofreadygroups = 0;

            foreach ($coursegroups as $coursegroup) {
                $communication = self::load_by_group(
                    groupid: $coursegroup->id,
                    context: $coursecontext,
                );
                $roomstatus = $communication->get_communication_room_url() ? 'ready' : 'pending';
                switch ($roomstatus) {
                    case 'ready':
                        $numberofreadygroups ++;
                        break;
                    case 'pending':
                        $pendincommunicationobject = $communication;
                        break;
                }
            }

            if ($numberofgroups === $numberofreadygroups) {
                $communication->show_communication_room_status_notification();
            } else {
                $pendincommunicationobject->show_communication_room_status_notification();
            }
        }
    }

    /**
     * Update course communication according to course data.
     * Course can have course or group rooms. Group mode enabling will create rooms for groups.
     *
     * @param stdClass $course The course data
     * @param bool $changesincoursecat Whether the course moved to a different category
     */
    public static function update_course_communication_instance(
        stdClass $course,
        bool $changesincoursecat
    ): void {
        // If the communication subsystem is not enabled then just ignore.
        if (!api::is_available()) {
            return;
        }

        // Check if provider is selected.
        $provider = $course->selectedcommunication ?? null;
        // If the course moved to hidden category, set provider to none.
        if ($changesincoursecat && empty($course->visible)) {
            $provider = processor::PROVIDER_NONE;
        }

        // Get the course context.
        $coursecontext = \context_course::instance(courseid: $course->id);
        // Get the course image.
        $courseimage = course_get_courseimage(course: $course);
        // Get the course communication instance.
        $coursecommunication = self::load_by_course(
            courseid: $course->id,
            context: $coursecontext,
        );

        // Attempt to get the communication provider if it wasn't provided in the data.
        if (empty($provider)) {
            $provider = $coursecommunication->get_provider();
        }

        // Determine the communication room name if none was provided and add it to the course data.
        if (empty($course->communicationroomname)) {
            $course->communicationroomname = $course->fullname ?? get_course($course->id)->fullname;
        }

        // List of enrolled users for course communication.
        $enrolledusers = self::get_enrolled_users_for_course(course: $course);

        // Check for group mode, we will have to get the course data again as the group info is not always in the object.
        $groupmode = $course->groupmode ?? get_course(courseid: $course->id)->groupmode;

        // If group mode is disabled, get the communication information for creating room for a course.
        if ((int)$groupmode === NOGROUPS) {
            // Remove all the members from active group rooms if there is any.
            $coursegroups = groups_get_all_groups(courseid: $course->id);
            foreach ($coursegroups as $coursegroup) {
                $communication = self::load_by_group(
                    groupid: $coursegroup->id,
                    context: $coursecontext,
                );
                // Remove the members from the group room.
                $communication->remove_all_members_from_room();
                // Now delete the group room.
                $communication->update_room(active: processor::PROVIDER_INACTIVE);
            }

            // Now create/update the course room.
            $communication = self::load_by_course(
                courseid: $course->id,
                context: $coursecontext,
            );
            $communication->configure_room_and_membership_by_provider(
                provider: $provider,
                instance: $course,
                communicationroomname: $course->communicationroomname,
                users: $enrolledusers,
                instanceimage: $courseimage,
            );
        } else {
            // Update the group communication instances.
            self::update_group_communication_instances_for_course(
                course: $course,
                provider: $provider,
            );

            // Remove all the members for the course room if instance available.
            $communication = self::load_by_course(
                courseid: $course->id,
                context: $coursecontext,
            );

            // Now handle the course communication according to the provider.
            $communication->configure_room_and_membership_by_provider(
                provider: $provider,
                instance: $course,
                communicationroomname: $course->communicationroomname,
                users: $enrolledusers,
                instanceimage: $courseimage,
                queue: false,
            );

            // As the course is in group mode, make sure no users are in the course room.
            $communication->reload();
            $communication->remove_all_members_from_room();
        }
    }

    /**
     * Update the group communication instances.
     *
     * @param stdClass $course The course object.
     * @param string $provider The provider name.
     */
    public static function update_group_communication_instances_for_course(
        stdClass $course,
        string $provider,
    ): void {
        $coursegroups = groups_get_all_groups(courseid: $course->id);
        $coursecontext = \context_course::instance(courseid: $course->id);
        $allaccessgroupusers = self::get_users_has_access_to_all_groups(
            userids: self::get_enrolled_users_for_course(course: $course),
            courseid: $course->id,
        );

        foreach ($coursegroups as $coursegroup) {
            $groupuserstoadd = array_column(
                groups_get_members(groupid: $coursegroup->id),
                'id',
            );

            foreach ($allaccessgroupusers as $allaccessgroupuser) {
                if (!in_array($allaccessgroupuser, $groupuserstoadd, true)) {
                    $groupuserstoadd[] = $allaccessgroupuser;
                }
            }

            // Now create/update the group room.
            $communication = self::load_by_group(
                groupid: $coursegroup->id,
                context: $coursecontext,
            );

            $communicationroomname = self::format_group_room_name(
                baseroomname: $course->communicationroomname,
                groupname: $coursegroup->name,
            );

            $communication->configure_room_and_membership_by_provider(
                provider: $provider,
                instance: $course,
                communicationroomname: $communicationroomname,
                users: $groupuserstoadd,
            );
        }
    }

    /**
     * Format a group communication room name with the following syntax: 'Group A (Course 1)'.
     *
     * @param string $baseroomname The base room name.
     * @param string $groupname The group name.
     */
    public static function format_group_room_name(
        string $baseroomname,
        string $groupname
    ): string {
        return get_string('communicationgrouproomnameformat', 'core_communication', [
            'groupname' => $groupname,
            'baseroomname' => $baseroomname,
        ]);
    }
}
