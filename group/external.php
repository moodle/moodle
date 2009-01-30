<?php
/**
 *
 * Group external api
 *
 * @author Jordi Piguillem
 * @author David Castro
 * @author Ferran Recio
 */

require_once(dirname(dirname(__FILE__)) . '/lib/moodleexternal.php');
require_once(dirname(dirname(__FILE__)) . '/group/lib.php');

/**
 * Group external api class
 *
 * WORK IN PROGRESS, DO NOT USE IT
 */
final class group_external extends moodle_external {

    /**
     * Constructor - We set the description of this API in order to be access by Web service
     */
    function __construct () {
          $this->descriptions = array();


          $this->descriptions['tmp_create_group']   = array( 'params' => array('groupname'=> PARAM_RAW, 'courseid'=> PARAM_INT),
                                                            'optionalparams' => array( ),
                                                            'return' => array('groupid' => PARAM_INT));

          $this->descriptions['tmp_add_groupmember']   = array( 'params' => array('groupid'=> PARAM_INT, 'userid'=> PARAM_INT),
                                                            'optionalparams' => array( ),
                                                            'return' => array('result' => PARAM_BOOL));

    }

    /**
     * Creates a group
     * @param array $params
     *  ->courseid int
     *  ->groupname string
     * @return int userid
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

	static function tmp_add_groupmember($params){

		if (has_capability('moodle/course:managegroups', get_context_instance(CONTEXT_SYSTEM))) {

			// @TODO groups_add_member() does not check userid
			return groups_add_member($params['groupid'], $params['userid']);
		}
		else {
            throw new moodle_exception('wscouldnotaddgroupmember');
        }
	}
}

?>

