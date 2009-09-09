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
require_once(dirname(dirname(__FILE__)) . '/lib/moodleexternal.php');
require_once(dirname(dirname(__FILE__)) . '/group/lib.php');
require_once(dirname(dirname(__FILE__)) . '/lib/grouplib.php');


/**
 * Group external api class
 *
 */
final class group_external extends moodle_external {

    /**
     * Create some groups
     * @param array|struct $params
     * @subparam string $params:group->groupname
     * @subparam integer $params:group->courseid
     * @return array $return
     * @subparam integer $return:groupid
     */
    static function create_groups($params) {
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
    static function get_groups($params){

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
     * Delete some groups
     * @param array|struct $params
     * @subparam integer $params:groupid
     * @return boolean result
     */
    static function delete_groups($params){

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
     * Return all internal members for a group id (do not return remotely registered user)
     * @param array|struct $params
     * @subparam integer $params:groupid
     * @return array $return
     * $subparam string $return:username
     */
    static function get_groupmembers($params){
        if (has_capability('moodle/course:managegroups', get_context_instance(CONTEXT_SYSTEM))) {
            $members = array();
            foreach ($params as $groupid) {

                $groupmembers = groups_get_members(clean_param($groupid, PARAM_INTEGER));
                $custommembers = array();
                foreach ($groupmembers as $member) {
                    $custommember = new stdClass();
                    $custommember->username =  $member->username;
                    $custommember->auth =  $member->auth;
                    $custommember->confirmed =  $member->confirmed;
                    $custommember->idnumber =  $member->idnumber;
                    $custommember->firstname =  $member->firstname;
                    $custommember->lastname =  $member->lastname;
                    $custommember->email =  $member->email;
                    $custommember->emailstop =  $member->emailstop;
                    $custommember->lang =  $member->lang;
                    $custommember->id =  $member->id;
                    $custommember->theme =  $member->theme;
                    $custommember->timezone =  $member->timezone;
                    $custommember->mailformat =  $member->mailformat;
                    $custommembers[] = $custommember;
                }
                 
                $members[] = array("groupid" => $groupid, "members" => $custommembers);
            }
            return $members;
        }
        else {
            throw new moodle_exception('wscouldnotgetgroupnopermission');
        }
    }

     /**
     * Add some members to some groups
     * @param array|struct $params
     * @subparam integer $params:member->groupid
     * @subparam integer $params:member->userid
     * @return boolean result
     */
    static function add_groupmembers($params){

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
     * Delete some members from some groups
     * @param array|struct $params
     * @subparam integer $params:member->groupid
     * @subparam integer $params:member->userid
     * @return boolean result
     */
    static function delete_groupmembers($params){
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

