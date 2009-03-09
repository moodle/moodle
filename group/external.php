<?php
/**
 *
 * Group external api
 *
 * @author Jordi Piguillem
 * @author David Castro
 * @author Ferran Recio
 * @author Jerome Mouneyrac
 */

require_once(dirname(dirname(__FILE__)) . '/group/lib.php');
require_once(dirname(dirname(__FILE__)) . '/lib/grouplib.php');


/**
 * Group external api class
 *
 * WORK IN PROGRESS, DO NOT USE IT
 */
final class group_external {

    /**
     * Creates a group
     * @param array|struct $params
     * @subparam string $params->groupname
     * @subparam integer $params->courseid
     * @return integer groupid
     */
    static function tmp_create_group($params) {
        global $USER;

        if (has_capability('moodle/course:managegroups', get_context_instance(CONTEXT_SYSTEM))) {
            $group = new stdClass;
            $group->courseid = $params['courseid'];
            $group->name = $params['groupname'];

            // @TODO: groups_create_group() does not check courseid
            return groups_create_group($group, false);
        }
        else {
            throw new moodle_exception('wscouldnotcreategroup');
        }
    }

    /**
     * Create some groups
     * @param array|struct $params
     * @subparam string $params:group->groupname
     * @subparam integer $params:group->courseid
     * @return array $return
     * @subparam integer $return:groupid
     */
    static function tmp_create_groups($params) {
        global $USER;
        $groupids = array();

        if (has_capability('moodle/course:managegroups', get_context_instance(CONTEXT_SYSTEM))) {

            foreach ($params as $groupparam) {
                $group = new stdClass;
                $group->courseid  = clean_param($groupparam['courseid'], PARAM_INTEGER);
                $group->name  = clean_param($groupparam['groupname'], PARAM_ALPHANUMEXT);
                $groupids[] = groups_create_group($group, false);
            }

            return $groupids;
        }
        else {
            throw new moodle_exception('wscouldnotcreategroupnopermission');
        }
    }

    /**
     * Get some groups
     * @param array|struct $params
     * @subparam integer $params:groupid
     * @return object $return
     * @subreturn integer $return:group->id
     * @subreturn integer $return:group->courseid
     * @subreturn string $return:group->name
     * @subreturn string $return:group->enrolmentkey
     */
    static function tmp_get_groups($params){

        if (has_capability('moodle/course:managegroups', get_context_instance(CONTEXT_SYSTEM))) {

            foreach ($params as $groupid) {
                $group = groups_get_group(clean_param($groupid, PARAM_INTEGER));

                $ret = new StdClass();
                $ret->id = $group->id;
                $ret->courseid = $group->courseid;
                $ret->name = $group->name;
                $ret->enrolmentkey = $group->enrolmentkey;

                $groups[] = $ret;
            }
            return $groups;
        }
        else {
            throw new moodle_exception('wscouldnotgetgroupnopermission');
        }

    }

    /**
     * Get a group
     * @param array|struct $params
     * @subparam integer $params->groupid
     * @return object $return
     * @subreturn integer $return->group->id
     * @subreturn integer $return->group->courseid
     * @subreturn string $return->group->name
     * @subreturn string $return->group->enrolmentkey
     */
    static function tmp_get_group($params){

        // @TODO: any capability to check?
        $group = groups_get_group($params['groupid']);

        $ret = new StdClass();
        $ret->id = $group->id;
        $ret->courseid = $group->courseid;
        $ret->name = $group->name;
        $ret->enrolmentkey = $group->enrolmentkey;

        return $ret;

    }


    /**
     *
     * @param array|struct $params
     * @subparam integer $params->groupid
     * @return boolean result
     */
    static function tmp_delete_group($params){

        if (has_capability('moodle/course:managegroups', get_context_instance(CONTEXT_SYSTEM))) {

            // @TODO groups_add_member() does not check userid
            return groups_delete_group($params['groupid']);
        }
        else {
            throw new moodle_exception('wscouldnotdeletegroup');
        }
    }

    /**
     * Delete some groups
     * @param array|struct $params
     * @subparam integer $params:groupid
     * @return boolean result
     */
    static function tmp_delete_groups($params){

        if (has_capability('moodle/course:managegroups', get_context_instance(CONTEXT_SYSTEM))) {
            $deletionsuccessfull = true;
            foreach ($params as $groupid) {
                if (!groups_delete_group(clean_param($groupid, PARAM_INTEGER))) {
                    $deletionsuccessfull = false;
                }
            }
            return  $deletionsuccessfull;
        }
        else {
            throw new moodle_exception('wscouldnotdeletegroupnopermission');
        }
    }

    /**
     *
     * @param array|struct $params
     * @subparam integer $params->groupid
     * @subparam integer $params->userid
     * @return boolean result
     */
    static function tmp_get_groupmember($params){
    }

    /**
     * Add a member to a group
     * @param array|struct $params
     * @subparam integer $params->groupid
     * @subparam integer $params->userid
     * @return boolean result
     */
    static function tmp_add_groupmember($params){

        if (has_capability('moodle/course:managegroups', get_context_instance(CONTEXT_SYSTEM))) {

            // @TODO groups_add_member() does not check userid
            return groups_add_member($params['groupid'], $params['userid']);
        }
        else {
            throw new moodle_exception('wscouldnotaddgroupmember');
        }
    }

     /**
     * Add some members to some groups
     * @param array|struct $params
     * @subparam integer $params:member->groupid
     * @subparam integer $params:member->userid
     * @return boolean result
     */
    static function tmp_add_groupmembers($params){

        if (has_capability('moodle/course:managegroups', get_context_instance(CONTEXT_SYSTEM))) {
            $addmembersuccessfull = true;
            foreach($params as $member) {
                $groupid = clean_param($member['groupid'], PARAM_INTEGER);
                $userid = clean_param($member['userid'], PARAM_INTEGER);
                if (!groups_add_member($groupid, $userid)) {
                    $addmembersuccessfull = false;
                }
            }
            return $addmembersuccessfull;
        }
        else {
            throw new moodle_exception('wscouldnotaddgroupmembernopermission');
        }
    }

    /**
     *
     * @param array|struct $params
     * @subparam integer $params->groupid
     * @subparam integer $params->userid
     * @return boolean result
     */
    static function tmp_delete_groupmember($params){
        if (has_capability('moodle/course:managegroups', get_context_instance(CONTEXT_SYSTEM))) {

            return groups_remove_member($params['groupid'], $params['userid']);
        } else {
            throw new moodle_exception('wscouldnotremovegroupmember');
        }
    }

     /**
     * Delete some members from some groups
     * @param array|struct $params
     * @subparam integer $params:member->groupid
     * @subparam integer $params:member->userid
     * @return boolean result
     */
    static function tmp_delete_groupmembers($params){
        if (has_capability('moodle/course:managegroups', get_context_instance(CONTEXT_SYSTEM))) {
             $addmembersuccessfull = true;
            foreach($params as $member) {
                $groupid = clean_param($member['groupid'], PARAM_INTEGER);
                $userid = clean_param($member['userid'], PARAM_INTEGER);
                if (!groups_remove_member($groupid, $userid)) {
                    $addmembersuccessfull = false;
                }
            }
            return $addmembersuccessfull;
        } else {
            throw new moodle_exception('wscouldnotremovegroupmembernopermission');
        }
    }

}

?>

