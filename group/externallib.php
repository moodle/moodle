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
 * @copyright  2009 Moodle Pty Ltd (http://moodle.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("$CFG->libdir/externallib.php");

class moodle_group_external extends external_api {

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function create_groups_parameters() {
        return new external_function_parameters(
            array(
                'groups' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'courseid' => new external_value(PARAM_INT, 'id of course'),
                            'name' => new external_value(PARAM_TEXT, 'multilang compatible name, course unique'),
                            'description' => new external_value(PARAM_RAW, 'group description text'),
                            'enrolmentkey' => new external_value(PARAM_RAW, 'group enrol secret phrase'),
                        )
                    )
                )
            )
        );
    }

    /**
     * Create groups
     * @param array $groups array of group description arrays (with keys groupname and courseid)
     * @return array of newly created groups
     */
    public static function create_groups($groups) {
        global $CFG, $DB;
        require_once("$CFG->dirroot/group/lib.php");

        $params = self::validate_parameters(self::create_groups_parameters(), array('groups'=>$groups));

        // ideally create all groups or none at all, unfortunately myisam engine does not support transactions :-(
        $DB->begin_sql();
        try {
            $groups = array();

            foreach ($params['groups'] as $group) {
                $group = (object)$group;

                if (trim($group->name) == '') {
                    throw new invalid_parameter_exception('Invalid group name');
                }
                if ($DB->get_record('groups', array('courseid'=>$group->courseid, 'name'=>$group->name))) {
                    throw new invalid_parameter_exception('Group with the same name already exists in the course');
                }

                // now security checks
                $context = get_context_instance(CONTEXT_COURSE, $group->courseid);
                self::validate_context($context);
                require_capability('moodle/course:managegroups', $context);

                // finally create the group
                $group->id = groups_create_group($group, false);
                $groups[] = (array)$group;
            }
        } catch (Exception $ex) {
            $DB->rollback_sql();
            throw $ex;
        }
        $DB->commit_sql();

        return $groups;
    }

   /**
     * Returns description of method result value
     * @return external_description
     */
    public static function create_groups_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'id' => new external_value(PARAM_INT, 'group record id'),
                    'courseid' => new external_value(PARAM_INT, 'id of course'),
                    'name' => new external_value(PARAM_TEXT, 'multilang compatible name, course unique'),
                    'description' => new external_value(PARAM_RAW, 'group description text'),
                    'enrolmentkey' => new external_value(PARAM_RAW, 'group enrol secret phrase'),
                )
            )
        );
    }

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function get_groups_parameters() {
        return new external_function_parameters(
            array(
                'groupids' => new external_multiple_structure(new external_value(PARAM_INT, 'Group ID')),
            )
        );
    }

    /**
     * Get groups definition
     * @param array $groupids arrays of group ids
     * @return array of group objects (id, courseid, name, enrolmentkey)
     */
    public static function get_groups($groupids) {
        $groups = array();

        $params = self::validate_parameters(self::get_groups_parameters(), array('groupids'=>$groupids));

        foreach ($params['groupids'] as $groupid) {
            // validate params
            $group = groups_get_group($groupid, 'id, courseid, name, description, enrolmentkey', MUST_EXIST);

            // now security checks
            $context = get_context_instance(CONTEXT_COURSE, $group->courseid);
            self::validate_context($context);
            require_capability('moodle/course:managegroups', $context);

            $groups[] = (array)$group;
        }

        return $groups;
    }

   /**
     * Returns description of method result value
     * @return external_description
     */
    public static function get_groups_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'id' => new external_value(PARAM_INT, 'group record id'),
                    'courseid' => new external_value(PARAM_INT, 'id of course'),
                    'name' => new external_value(PARAM_TEXT, 'multilang compatible name, course unique'),
                    'description' => new external_value(PARAM_RAW, 'group description text'),
                    'enrolmentkey' => new external_value(PARAM_RAW, 'group enrol secret phrase'),
                )
            )
        );
    }

    public static function delete_groups_parameters() {
        //TODO
    }

    /**
     * Delete groups
     * @param array $groupids array of group ids
     * @return void
     */
    public static function delete_groups($groupids) {
        global $CFG;
        require_once("$CFG->dirroot/group/lib.php");

        $params = self::validate_parameters(self::delete_groups_parameters(), array('groupids'=>$groupids));

        $groups = array();

        foreach ($params['groupids'] as $groupid) {
            // validate params
            $groupid = validate_param($groupid, PARAM_INTEGER);
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

    public static function delete_groups_returns() {
        //TODO
    }


    public static function get_groupmembers_parameters() {
        //TODO
    }

    /**
     * Return all members for a group
     * @param array $groupids array of group ids
     * @return array with  group id keys containing arrays of user ids
     */
    public static function get_groupmembers($groupids) {
        $groups = array();

        $params = self::validate_parameters(self::get_groupmembers_parameters(), array('groupids'=>$groupids));

        foreach ($params['groupids'] as $groupid) {
            // validate params
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

    public static function get_groupmembers_returns() {
        //TODO
    }


    public static function add_groupmembers_parameters() {
        //TODO
    }

    /**
     * Add group members
     * @param array $members of arrays with keys userid, groupid
     * @return void
     */
    public static function add_groupmembers($members) {
        global $CFG;
        require_once("$CFG->dirroot/group/lib.php");

        $params = self::validate_parameters(self::add_groupmembers_parameters(), array('members'=>$members));

        foreach ($params['members'] as $member) {
            // validate params
            $groupid = $member['groupid'];
            $userid = $member['userid'];

            $group = groups_get_group($groupid, 'id, courseid', MUST_EXIST);
            $user = $DB->get_record('user', array('id'=>$userid, 'deleted'=>0, 'mnethostid'=>$CFG->mnet_localhost_id), '*', MUST_EXIST);

            // now security checks
            $context = get_context_instance(CONTEXT_COURSE, $group->courseid);
            self::validate_context($context);
            require_capability('moodle/course:managegroups', $context);
            require_capability('moodle/course:view', $context, $user->id, false); // only enrolled users may be members of group!!!

            groups_add_member($group, $user);
        }
    }

    public static function add_groupmembers_returns() {
        //TODO
    }


    public static function delete_groupmembers_parameters() {
        //TODO
    }

    /**
     * Delete group members
     * @param array $members of arrays with keys userid, groupid
     * @return void
     */
    public static function delete_groupmembers($members) {
        global $CFG;
        require_once("$CFG->dirroot/group/lib.php");

        $params = self::validate_parameters(self::delete_groupmembers_parameters(), array($members=>'members'));

        foreach ($params['members'] as $member) {
            // validate params
            $groupid = $member['groupid'];
            $userid = $member['userid'];

            $group = groups_get_group($groupid, 'id, courseid', MUST_EXIST);
            $user = $DB->get_record('user', array('id'=>$userid, 'deleted'=>0, 'mnethostid'=>$CFG->mnet_localhost_id), '*', MUST_EXIST);

            // now security checks
            $context = get_context_instance(CONTEXT_COURSE, $group->courseid);
            self::validate_context($context);
            require_capability('moodle/course:managegroups', $context);

            groups_remove_member($group, $user);
        }
    }

    public static function delete_groupmembers_returns() {
        //TODO
    }

}