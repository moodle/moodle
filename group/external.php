<?php
/**
 *
 * Group external api
 *
 * @author Jordi Piguillem
 * @author David Castro
 * @author Ferran Recio
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
     * @param array $params
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
     * Get a group member
     * @param array $params
     * @subparam integer $params->groupid
     * @return array $return
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
     * @param array $params
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
     *
     * @param array $params
     * @subparam integer $params->groupid
     * @subparam integer $params->userid
     * @return boolean result
     */
	static function tmp_get_groupmember($params){
	}

    /**
     * Add a member to a group
     * @param array $params
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
     *
     * @param array $params
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

}

?>

