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
 * External groups API
 *
 * @package    moodlecore
 * @subpackage webservice
 * @copyright  2009 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("$CFG->libdir/externallib.php");

class moodle_group_external extends external_api {

   /**
     * Create groups
     * @param array $params array of group description arrays (with keys groupname and courseid)
     * @return array of newly created group ids
     */
    public static function create_groups($params) {
        global $CFG;
        require_once("$CFG->dirroot/group/lib.php");

        $groupids = array();

        foreach ($params as $groupparam) {
            $group = new object();
            // clean params
            $group->courseid  = clean_param($groupparam['courseid'], PARAM_INTEGER);
            $group->name      = clean_param($groupparam['groupname'], PARAM_MULTILANG);
            if (array_key_exists('enrolmentkey', $groupparam)) {
                $group->enrolmentkey = $groupparam['enrolmentkey'];
            } else {
                $group->enrolmentkey = '';
            }
            // now security checks
            $context = get_context_instance(CONTEXT_COURSE, $group->courseid);
            self::validate_context($context);
            require_capability('moodle/course:managegroups', $context);

            $id = groups_create_group($group, false);
            $group->id = $id;
            $groupids[$id] = $group;
        }

        return $groupids;
    }

    /**
     * Get groups definition
     * @param array $params arrays of group ids
     * @return array of group objects (id, courseid, name, enrolmentkey)
     */
    public static function get_groups($params) {
        $groups = array();

        //TODO: we do need to search for groups in courses too,
        //      fetching by id is not enough!

        foreach ($params as $groupid) {
            $groupid = clean_param($groupid, PARAM_INTEGER);
            $group = groups_get_group($groupid, 'id, courseid, name, enrolmentkey', MUST_EXIST);
            // now security checks
            $context = get_context_instance(CONTEXT_COURSE, $group->courseid);
            self::validate_context($context);
            require_capability('moodle/course:managegroups', $context);

            $groups[$group->id] = $group;
        }

        return $groups;
    }

    /**
     * Delete groups
     * @param array $params array of group ids
     * @return void
     */
    public static function delete_groups($params) {
        global $CFG;
        require_once("$CFG->dirroot/group/lib.php");

        $groups = array();

        foreach ($params as $groupid) {
            $groupid = clean_param($groupid, PARAM_INTEGER);
            if (!$group = groups_get_group($groupid, 'id, courseid', IGNORE_MISSING)) {
                // silently ignore attempts to delete nonexisting groups
                continue;
            }
            // now security checks
            $context = get_context_instance(CONTEXT_COURSE, $group->courseid);
            self::validate_context($context);
            require_capability('moodle/course:managegroups', $context);

            groups_delete_group($group);
        }
    }


    /**
     * Return all members for a group
     * @param array $params array of group ids
     * @return array with  group id keys containing arrays of user ids
     */
    public static function get_groupmembers($params) {
        $groups = array();

        foreach ($params as $groupid) {
            $groupid = clean_param($groupid, PARAM_INTEGER);
            $group = groups_get_group($groupid, 'id, courseid, name, enrolmentkey', MUST_EXIST);
            // now security checks
            $context = get_context_instance(CONTEXT_COURSE, $group->courseid);
            self::validate_context($context);
            require_capability('moodle/course:managegroups', $context);

            $groupmembers = groups_get_members($group->id, 'u.id', 'lastname ASC, firstname ASC');

            $groups[$group->id] = array_keys($groupmembers);
        }

        return $groups;
    }


    /**
     * Add group members
     * @param array of arrays with keys userid, groupid
     * @return void
     */
    public static function add_groupmembers($params) {
        global $CFG;
        require_once("$CFG->dirroot/group/lib.php");

        $groups = array();

        foreach ($params as $member) {
            $groupid = clean_param($member['groupid'], PARAM_INTEGER);
            $userid = clean_param($member['userid'], PARAM_INTEGER);
            $group = groups_get_group($groupid, 'id, courseid', MUST_EXIST);
            $user = $DB->get_record('user', array('id'=>$userid, 'deleted'=>0, 'mnethostid'=>$CFG->mnet_localhost_id));

            // now security checks
            $context = get_context_instance(CONTEXT_COURSE, $group->courseid);
            self::validate_context($context);
            require_capability('moodle/course:managegroups', $context);
            require_capability('moodle/course:view', $context, $user->id, false); // only enrolled users may be members of group!!!

            groups_add_member($group, $user);
        }
    }


    /**
     * Delete group members
     * @param array of arrays with keys userid, groupid
     * @return void
     */
    public static function delete_groupmembers($params){
        global $CFG;
        require_once("$CFG->dirroot/group/lib.php");

        $groups = array();

        foreach ($params as $member) {
            $groupid = clean_param($member['groupid'], PARAM_INTEGER);
            $userid = clean_param($member['userid'], PARAM_INTEGER);
            $group = groups_get_group($groupid, 'id, courseid');
            $user = $DB->get_record('user', array('id'=>$userid, 'deleted'=>0, 'mnethostid'=>$CFG->mnet_localhost_id));

            // now security checks
            $context = get_context_instance(CONTEXT_COURSE, $group->courseid);
            self::validate_context($context);
            require_capability('moodle/course:managegroups', $context);

            groups_remove_member($group, $user);
        }
    }

}