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
 * The mod_bigbluebuttonbn roles helper
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2021 onwards, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Laurent David  (laurent [at] call-learning [dt] fr)
 */

namespace mod_bigbluebuttonbn\local\helpers;

use cache;
use cache_store;
use context;
use context_course;
use mod_bigbluebuttonbn\instance;
use mod_bigbluebuttonbn\local\proxy\bigbluebutton_proxy;
use stdClass;

/**
 * Utility class for all roles routines helper
 *
 * @package mod_bigbluebuttonbn
 * @copyright 2021 onwards, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class roles {

    /** @var int The bigbluebutton viewer role */
    public const ROLE_VIEWER = 'viewer';

    /** @var string The bigbluebutton moderator role */
    public const ROLE_MODERATOR = 'moderator';

    /**
     * Returns user roles in a context.
     *
     * @param context $context
     * @param int $userid
     *
     * @return array $userroles
     */
    public static function get_user_roles(context $context, int $userid) {
        global $DB;
        $userroles = get_user_roles($context, $userid);
        if ($userroles) {
            $where = '';
            foreach ($userroles as $userrole) {
                $where .= (empty($where) ? ' WHERE' : ' OR') . ' id=' . $userrole->roleid;
            }
            $userroles = $DB->get_records_sql('SELECT * FROM {role}' . $where);
        }
        return $userroles;
    }

    /**
     * Returns guest role wrapped in an array.
     *
     * @return array
     */
    protected static function get_guest_role() {
        $guestrole = get_guest_role();
        return [$guestrole->id => $guestrole];
    }

    /**
     * Returns an array containing all the users in a context wrapped for html select element.
     *
     * @param context_course $context
     * @param null $bbactivity
     * @return array $users
     */
    public static function get_users_array(context_course $context, $bbactivity = null) {
        // CONTRIB-7972, check the group of current user and course group mode.
        $groups = null;
        $users = (array) get_enrolled_users($context, '', 0, 'u.*', null, 0, 0, true);
        $course = get_course($context->instanceid);
        $groupmode = groups_get_course_groupmode($course);
        if ($bbactivity) {
            list($bbcourse, $cm) = get_course_and_cm_from_instance($bbactivity->id, 'bigbluebuttonbn');
            $groupmode = groups_get_activity_groupmode($cm);

        }
        if ($groupmode == SEPARATEGROUPS && !has_capability('moodle/site:accessallgroups', $context)) {
            global $USER;
            $groups = groups_get_all_groups($course->id, $USER->id);
            $users = [];
            foreach ($groups as $g) {
                $users += (array) get_enrolled_users($context, '', $g->id, 'u.*', null, 0, 0, true);
            }
        }
        return array_map(
            function($u) {
                return ['id' => $u->id, 'name' => fullname($u)];
            },
            $users);
    }

    /**
     * Can do some administration in this course, likely manage recordings
     *
     * @param int $courseid
     * @param string $capability
     */
    public static function has_capability_in_course(int $courseid, string $capability) {
        global $DB;
        if (empty($courseid) || !$DB->record_exists('course', ['id' => $courseid])) {
            return has_capability('moodle/site:config', \context_system::instance());
        }

        $coursecontext = context_course::instance($courseid);
        return has_capability($capability, $coursecontext);
    }

    /**
     * Returns an array containing all the roles in a context.
     *
     * @param context|null $context $context
     * @param bool|null $onlyviewableroles
     *
     * @return array $roles
     */
    public static function get_roles(?context $context = null, ?bool $onlyviewableroles = true) {
        global $CFG;

        if ($onlyviewableroles == true && $CFG->branch >= 35) {
            $roles = (array) get_viewable_roles($context);
            foreach ($roles as $key => $value) {
                $roles[$key] = $value;
            }
        } else {
            $roles = (array) role_get_names($context);
            foreach ($roles as $key => $value) {
                $roles[$key] = $value->localname;
            }
        }

        return $roles;
    }

    /**
     * Returns an array containing all the roles in a context wrapped for html select element.
     *
     * @param context|null $context $context
     * @param bool $onlyviewableroles
     *
     * @return array $users
     */
    protected static function get_roles_select(context $context = null, bool $onlyviewableroles = true) {
        global $CFG;

        if ($onlyviewableroles == true && $CFG->branch >= 35) {
            $roles = (array) get_viewable_roles($context);
            foreach ($roles as $key => $value) {
                $roles[$key] = ['id' => $key, 'name' => $value];
            }
        } else {
            $roles = (array) role_get_names($context);
            foreach ($roles as $key => $value) {
                $roles[$key] = ['id' => $value->id, 'name' => $value->localname];
            }
        }

        return $roles;
    }

    /**
     * Returns role that corresponds to an id.
     *
     * @param string|integer $id
     *
     * @return stdClass|null $role
     */
    protected static function get_role($id): ?stdClass {
        $roles = (array) role_get_names();
        if (is_numeric($id) && isset($roles[$id])) {
            return (object) $roles[$id];
        }
        foreach ($roles as $role) {
            if ($role->shortname == $id) {
                return $role;
            }
        }
        return null;
    }

    /**
     * Returns an array to populate a list of participants used in mod_form.js.
     *
     * @param context $context
     * @param null|stdClass $bbactivity
     * @return array $data
     */
    public static function get_participant_data(context $context, ?stdClass $bbactivity = null) {
        $data = [
            'all' => [
                'name' => get_string('mod_form_field_participant_list_type_all', 'bigbluebuttonbn'),
                'children' => []
            ],
        ];
        $data['role'] = [
            'name' => get_string('mod_form_field_participant_list_type_role', 'bigbluebuttonbn'),
            'children' => self::get_roles_select($context, true)
        ];
        $data['user'] = [
            'name' => get_string('mod_form_field_participant_list_type_user', 'bigbluebuttonbn'),
            'children' => self::get_users_array($context, $bbactivity),
        ];
        return $data;
    }

    /**
     * Returns an array to populate a list of participants used in mod_form.php.
     *
     * @param stdClass|null $bigbluebuttonbn
     * @param context $context
     *
     * @return array
     */
    public static function get_participant_list(?stdClass $bigbluebuttonbn, context $context): array {
        global $USER;
        if ($bigbluebuttonbn == null) {
            return self::get_participant_rules_encoded(
                self::get_participant_list_default($context, $USER->id)
            );
        }
        if (empty($bigbluebuttonbn->participants)) {
            $bigbluebuttonbn->participants = "[]";
        }
        $rules = json_decode($bigbluebuttonbn->participants, true);
        if (empty($rules)) {
            $rules = self::get_participant_list_default($context,
                bigbluebutton_proxy::get_instance_ownerid($bigbluebuttonbn));
        }
        return self::get_participant_rules_encoded($rules);
    }

    /**
     * Returns an array to populate a list of participants used in mod_form.php with default values.
     *
     * @param context $context
     * @param int|null $ownerid
     *
     * @return array
     */
    protected static function get_participant_list_default(context $context, ?int $ownerid = null) {
        $participantlist = [];
        $participantlist[] = [
            'selectiontype' => 'all',
            'selectionid' => 'all',
            'role' => self::ROLE_VIEWER,
        ];
        $defaultrules = explode(',', \mod_bigbluebuttonbn\local\config::get('participant_moderator_default'));
        foreach ($defaultrules as $defaultrule) {
            if ($defaultrule == '0') {
                if (!empty($ownerid) && is_enrolled($context, $ownerid)) {
                    $participantlist[] = [
                        'selectiontype' => 'user',
                        'selectionid' => (string) $ownerid,
                        'role' => self::ROLE_MODERATOR];
                }
                continue;
            }
            $participantlist[] = [
                'selectiontype' => 'role',
                'selectionid' => $defaultrule,
                'role' => self::ROLE_MODERATOR];
        }
        return $participantlist;
    }

    /**
     * Returns an array to populate a list of participants used in mod_form.php with bigbluebuttonbn values.
     *
     * @param array $rules
     *
     * @return array
     */
    protected static function get_participant_rules_encoded(array $rules): array {
        foreach ($rules as $key => $rule) {
            if ($rule['selectiontype'] !== 'role' || is_numeric($rule['selectionid'])) {
                continue;
            }
            $role = self::get_role($rule['selectionid']);
            if ($role == null) {
                unset($rules[$key]);
                continue;
            }
            $rule['selectionid'] = $role->id;
            $rules[$key] = $rule;
        }
        return $rules;
    }

    /**
     * Returns an array to populate a list of participant_selection used in mod_form.php.
     *
     * @return array
     */
    public static function get_participant_selection_data(): array {
        return [
            'type_options' => [
                'all' => get_string('mod_form_field_participant_list_type_all', 'bigbluebuttonbn'),
                'role' => get_string('mod_form_field_participant_list_type_role', 'bigbluebuttonbn'),
                'user' => get_string('mod_form_field_participant_list_type_user', 'bigbluebuttonbn'),
            ],
            'type_selected' => 'all',
            'options' => ['all' => '---------------'],
            'selected' => 'all',
        ];
    }

    /**
     * Evaluate if a user in a context is moderator based on roles and participation rules.
     *
     * @param context $context
     * @param array $participantlist
     * @param int $userid
     *
     * @return bool
     */
    public static function is_moderator(context $context, array $participantlist, ?int $userid = null): bool {
        global $USER;
        // If an admin, then also a moderator.
        if (has_capability('moodle/site:config', $context)) {
            return true;
        }
        if (!is_array($participantlist)) {
            return false;
        }
        if (empty($userid)) {
            $userid = $USER->id;
        }
        $userroles = self::get_guest_role();
        if (!isguestuser()) {
            $userroles = self::get_user_roles($context, $userid);
        }
        return self::is_moderator_validator($participantlist, $userid, $userroles);
    }

    /**
     * Iterates participant list rules to evaluate if a user is moderator.
     *
     * @param array $participantlist
     * @param int $userid
     * @param array $userroles
     *
     * @return bool
     */
    protected static function is_moderator_validator(array $participantlist, int $userid, array $userroles): bool {
        // Iterate participant rules.
        foreach ($participantlist as $participant) {
            if (self::is_moderator_validate_rule($participant, $userid, $userroles)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Evaluate if a user is moderator based on roles and a particular participation rule.
     *
     * @param array $participant
     * @param int $userid
     * @param array $userroles
     *
     * @return bool
     */
    protected static function is_moderator_validate_rule(array $participant, int $userid, array $userroles): bool {
        if ($participant['role'] == self::ROLE_VIEWER) {
            return false;
        }
        // Validation for the 'all' rule.
        if ($participant['selectiontype'] == 'all') {
            return true;
        }
        // Validation for a 'user' rule.
        if ($participant['selectiontype'] == 'user') {
            if ($participant['selectionid'] == $userid) {
                return true;
            }
            return false;
        }
        // Validation for a 'role' rule.
        $role = self::get_role($participant['selectionid']);
        if ($role != null && array_key_exists($role->id, $userroles)) {
            return true;
        }
        return false;
    }

    /**
     * Updates the meeting info cached object when a participant has joined.
     *
     * @param string $meetingid
     * @param bool $ismoderator
     *
     * @return void
     */
    public static function participant_joined(string $meetingid, bool $ismoderator): void {
        $cache = cache::make_from_params(cache_store::MODE_APPLICATION, 'mod_bigbluebuttonbn', 'meetings_cache');
        $result = $cache->get($meetingid);
        $meetinginfo = json_decode($result['meeting_info']);
        $meetinginfo->participantCount += 1;
        if ($ismoderator) {
            $meetinginfo->moderatorCount += 1;
        }
        $cache->set($meetingid, ['creation_time' => $result['creation_time'],
            'meeting_info' => json_encode($meetinginfo)]);
    }

    /**
     * Helper function returns a list of courses a user has access to, wrapped in an array that can be used
     * by a html select.
     *
     * @param instance $instance
     * @return array
     */
    public static function import_get_courses_for_select(instance $instance): array {
        if ($instance->is_admin()) {
            $courses = get_courses('all', 'c.fullname ASC');
            // It includes the name of the site as a course (category 0), so remove the first one.
            unset($courses['1']);
        } else {
            $courses = enrol_get_users_courses($instance->get_user_id(), false, 'id,shortname,fullname');
        }
        $courses = array_filter($courses, function($course) {
            $modules = get_fast_modinfo($course->id);
            return !empty($modules->instances['bigbluebuttonbn']);
        });
        $coursesforselect = [];
        foreach ($courses as $course) {
            $coursesforselect[$course->id] = $course->fullname . " (" . $course->shortname . ")";
        }
        return $coursesforselect;
    }
}
