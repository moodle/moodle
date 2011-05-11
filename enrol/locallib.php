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
 * This file contains the course_enrolment_manager class which is used to interface
 * with the functions that exist in enrollib.php in relation to a single course.
 *
 * @package    core
 * @subpackage enrol
 * @copyright  2010 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * This class provides a targeted tied together means of interfacing the enrolment
 * tasks together with a course.
 *
 * It is provided as a convenience more than anything else.
 *
 * @copyright 2010 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_enrolment_manager {

    /**
     * The course context
     * @var stdClass
     */
    protected $context;
    /**
     * The course we are managing enrolments for
     * @var stdClass
     */
    protected $course = null;
    /**
     * Limits the focus of the manager to one enrolment plugin instance
     * @var string
     */
    protected $instancefilter = null;

    /**
     * The total number of users enrolled in the course
     * Populated by course_enrolment_manager::get_total_users
     * @var int
     */
    protected $totalusers = null;
    /**
     * An array of users currently enrolled in the course
     * Populated by course_enrolment_manager::get_users
     * @var array
     */
    protected $users = array();

    /**
     * An array of users who have roles within this course but who have not
     * been enrolled in the course
     * @var array
     */
    protected $otherusers = array();

    /**
     * The total number of users who hold a role within the course but who
     * arn't enrolled.
     * @var int
     */
    protected $totalotherusers = null;

    /**
     * The current moodle_page object
     * @var moodle_page
     */
    protected $moodlepage = null;

    /**#@+
     * These variables are used to cache the information this class uses
     * please never use these directly instead use their get_ counterparts.
     * @access private
     * @var array
     */
    private $_instancessql = null;
    private $_instances = null;
    private $_inames = null;
    private $_plugins = null;
    private $_roles = null;
    private $_assignableroles = null;
    private $_assignablerolesothers = null;
    private $_groups = null;
    /**#@-*/

    /**
     * Constructs the course enrolment manager
     *
     * @param moodle_page $moodlepage
     * @param stdClass $course
     * @param string $instancefilter
     */
    public function __construct(moodle_page $moodlepage, $course, $instancefilter = null) {
        $this->moodlepage = $moodlepage;
        $this->context = get_context_instance(CONTEXT_COURSE, $course->id);
        $this->course = $course;
        $this->instancefilter = $instancefilter;
    }

    /**
     * Returns the current moodle page
     * @return moodle_page 
     */
    public function get_moodlepage() {
        return $this->moodlepage;
    }

    /**
     * Returns the total number of enrolled users in the course.
     *
     * If a filter was specificed this will be the total number of users enrolled
     * in this course by means of that instance.
     *
     * @global moodle_database $DB
     * @return int
     */
    public function get_total_users() {
        global $DB;
        if ($this->totalusers === null) {
            list($instancessql, $params, $filter) = $this->get_instance_sql();
            $sqltotal = "SELECT COUNT(DISTINCT u.id)
                           FROM {user} u
                           JOIN {user_enrolments} ue ON (ue.userid = u.id  AND ue.enrolid $instancessql)
                           JOIN {enrol} e ON (e.id = ue.enrolid)";
            $this->totalusers = (int)$DB->count_records_sql($sqltotal, $params);
        }
        return $this->totalusers;
    }

    /**
     * Returns the total number of enrolled users in the course.
     *
     * If a filter was specificed this will be the total number of users enrolled
     * in this course by means of that instance.
     *
     * @global moodle_database $DB
     * @return int
     */
    public function get_total_other_users() {
        global $DB;
        if ($this->totalotherusers === null) {
            list($ctxcondition, $params) = $DB->get_in_or_equal(get_parent_contexts($this->context, true), SQL_PARAMS_NAMED, 'ctx');
            $params['courseid'] = $this->course->id;
            $sql = "SELECT COUNT(DISTINCT u.id)
                      FROM {role_assignments} ra
                      JOIN {user} u ON u.id = ra.userid
                      JOIN {context} ctx ON ra.contextid = ctx.id
                 LEFT JOIN (
                           SELECT ue.id, ue.userid
                             FROM {user_enrolments} ue
                        LEFT JOIN {enrol} e ON e.id=ue.enrolid
                            WHERE e.courseid = :courseid
                         ) ue ON ue.userid=u.id
                     WHERE ctx.id $ctxcondition AND
                           ue.id IS NULL";
            $this->totalotherusers = (int)$DB->count_records_sql($sql, $params);
        }
        return $this->totalotherusers;
    }

    /**
     * Gets all of the users enrolled in this course.
     *
     * If a filter was specified this will be the users who were enrolled
     * in this course by means of that instance.
     *
     * @global moodle_database $DB
     * @param string $sort
     * @param string $direction ASC or DESC
     * @param int $page First page should be 0
     * @param int $perpage Defaults to 25
     * @return array
     */
    public function get_users($sort, $direction='ASC', $page=0, $perpage=25) {
        global $DB;
        if ($direction !== 'ASC') {
            $direction = 'DESC';
        }
        $key = md5("$sort-$direction-$page-$perpage");
        if (!array_key_exists($key, $this->users)) {
            list($instancessql, $params, $filter) = $this->get_instance_sql();
            $ufields = user_picture::fields('u', array('lastaccess', 'email'));
            $sql = "SELECT DISTINCT $ufields, ul.timeaccess AS lastseen
                      FROM {user} u
                      JOIN {user_enrolments} ue ON (ue.userid = u.id  AND ue.enrolid $instancessql)
                      JOIN {enrol} e ON (e.id = ue.enrolid)
                 LEFT JOIN {user_lastaccess} ul ON (ul.courseid = e.courseid AND ul.userid = u.id)";
            if ($sort === 'firstname') {
                $sql .= " ORDER BY u.firstname $direction, u.lastname $direction";
            } else if ($sort === 'lastname') {
                $sql .= " ORDER BY u.lastname $direction, u.firstname $direction";
            } else if ($sort === 'email') {
                $sql .= " ORDER BY u.email $direction, u.lastname $direction, u.firstname $direction";
            } else if ($sort === 'lastseen') {
                $sql .= " ORDER BY ul.timeaccess $direction, u.lastname $direction, u.firstname $direction";
            }
            $this->users[$key] = $DB->get_records_sql($sql, $params, $page*$perpage, $perpage);
        }
        return $this->users[$key];
    }

    /**
     * Gets and array of other users.
     *
     * Other users are users who have been assigned roles or inherited roles
     * within this course but who have not been enrolled in the course
     *
     * @global moodle_database $DB
     * @param string $sort
     * @param string $direction
     * @param int $page
     * @param int $perpage
     * @return array
     */
    public function get_other_users($sort, $direction='ASC', $page=0, $perpage=25) {
        global $DB;
        if ($direction !== 'ASC') {
            $direction = 'DESC';
        }
        $key = md5("$sort-$direction-$page-$perpage");
        if (!array_key_exists($key, $this->otherusers)) {
            list($ctxcondition, $params) = $DB->get_in_or_equal(get_parent_contexts($this->context, true), SQL_PARAMS_NAMED, 'ctx');
            $params['courseid'] = $this->course->id;
            $params['cid'] = $this->course->id;
            $sql = "SELECT ra.id as raid, ra.contextid, ra.component, ctx.contextlevel, ra.roleid, u.*, ue.lastseen
                    FROM {role_assignments} ra
                    JOIN {user} u ON u.id = ra.userid
                    JOIN {context} ctx ON ra.contextid = ctx.id
               LEFT JOIN (
                       SELECT ue.id, ue.userid, ul.timeaccess AS lastseen
                         FROM {user_enrolments} ue
                    LEFT JOIN {enrol} e ON e.id=ue.enrolid
                    LEFT JOIN {user_lastaccess} ul ON (ul.courseid = e.courseid AND ul.userid = ue.userid)
                        WHERE e.courseid = :courseid
                       ) ue ON ue.userid=u.id
                   WHERE ctx.id $ctxcondition AND
                         ue.id IS NULL
                ORDER BY u.$sort $direction, ctx.depth DESC";
            $this->otherusers[$key] = $DB->get_records_sql($sql, $params, $page*$perpage, $perpage);
        }
        return $this->otherusers[$key];
    }

    /**
     * Gets an array of the users that can be enrolled in this course.
     *
     * @global moodle_database $DB
     * @param int $enrolid
     * @param string $search
     * @param bool $searchanywhere
     * @param int $page Defaults to 0
     * @param int $perpage Defaults to 25
     * @return array Array(totalusers => int, users => array)
     */
    public function get_potential_users($enrolid, $search='', $searchanywhere=false, $page=0, $perpage=25) {
        global $DB, $CFG;

        // Add some additional sensible conditions
        $tests = array("id <> :guestid", 'u.deleted = 0', 'u.confirmed = 1');
        $params = array('guestid' => $CFG->siteguest);
        if (!empty($search)) {
            $conditions = array(
                $DB->sql_concat('u.firstname', "' '", 'u.lastname'),
                'u.email'
            );
            if ($searchanywhere) {
                $searchparam = '%' . $search . '%';
            } else {
                $searchparam = $search . '%';
            }
            $i = 0;
            foreach ($conditions as $key=>$condition) {
                $conditions[$key] = $DB->sql_like($condition,":con{$i}00", false);
                $params["con{$i}00"] = $searchparam;
                $i++;
            }
            $tests[] = '(' . implode(' OR ', $conditions) . ')';
        }
        $wherecondition = implode(' AND ', $tests);

        $ufields = user_picture::fields('u', array('username', 'lastaccess'));

        $fields      = 'SELECT '.$ufields;
        $countfields = 'SELECT COUNT(1)';
        $sql = " FROM {user} u
                WHERE $wherecondition
                      AND u.id NOT IN (SELECT ue.userid
                                         FROM {user_enrolments} ue
                                         JOIN {enrol} e ON (e.id = ue.enrolid AND e.id = :enrolid))";
        $order = ' ORDER BY u.lastname ASC, u.firstname ASC';
        $params['enrolid'] = $enrolid;
        $totalusers = $DB->count_records_sql($countfields . $sql, $params);
        $availableusers = $DB->get_records_sql($fields . $sql . $order, $params, $page*$perpage, $perpage);
        return array('totalusers'=>$totalusers, 'users'=>$availableusers);
    }

    /**
     * Searches other users and returns paginated results
     *
     * @global moodle_database $DB
     * @param string $search
     * @param bool $searchanywhere
     * @param int $page Starting at 0
     * @param int $perpage
     * @return array
     */
    public function search_other_users($search='', $searchanywhere=false, $page=0, $perpage=25) {
        global $DB, $CFG;

        // Add some additional sensible conditions
        $tests = array("u.id <> :guestid", 'u.deleted = 0', 'u.confirmed = 1');
        $params = array('guestid'=>$CFG->siteguest);
        if (!empty($search)) {
            $conditions = array('u.firstname','u.lastname');
            if ($searchanywhere) {
                $searchparam = '%' . $search . '%';
            } else {
                $searchparam = $search . '%';
            }
            $i = 0;
            foreach ($conditions as $key=>$condition) {
                $conditions[$key] = $DB->sql_like($condition, ":con{$i}00", false);
                $params["con{$i}00"] = $searchparam;
                $i++;
            }
            $tests[] = '(' . implode(' OR ', $conditions) . ')';
        }
        $wherecondition = implode(' AND ', $tests);

        $fields      = 'SELECT '.user_picture::fields('u', array('username','lastaccess'));
        $countfields = 'SELECT COUNT(u.id)';
        $sql   = " FROM {user} u
                  WHERE $wherecondition
                    AND u.id NOT IN (
                           SELECT u.id
                             FROM {role_assignments} r, {user} u
                            WHERE r.contextid = :contextid AND
                                  u.id = r.userid)";
        $order = ' ORDER BY lastname ASC, firstname ASC';

        $params['contextid'] = $this->context->id;
        $totalusers = $DB->count_records_sql($countfields . $sql, $params);
        $availableusers = $DB->get_records_sql($fields . $sql . $order, $params, $page*$perpage, $perpage);
        return array('totalusers'=>$totalusers, 'users'=>$availableusers);
    }

    /**
     * Gets an array containing some SQL to user for when selecting, params for
     * that SQL, and the filter that was used in constructing the sql.
     *
     * @global moodle_database $DB
     * @return string
     */
    protected function get_instance_sql() {
        global $DB;
        if ($this->_instancessql === null) {
            $instances = $this->get_enrolment_instances();
            $filter = $this->get_enrolment_filter();
            if ($filter && array_key_exists($filter, $instances)) {
                $sql = " = :ifilter";
                $params = array('ifilter'=>$filter);
            } else {
                $filter = 0;
                if ($instances) {
                    list($sql, $params) = $DB->get_in_or_equal(array_keys($this->get_enrolment_instances()), SQL_PARAMS_NAMED);
                } else {
                    // no enabled instances, oops, we should probably say something
                    $sql = "= :never";
                    $params = array('never'=>-1);
                }
            }
            $this->instancefilter = $filter;
            $this->_instancessql = array($sql, $params, $filter);
        }
        return $this->_instancessql;
    }

    /**
     * Returns all of the enrolment instances for this course.
     *
     * @return array
     */
    public function get_enrolment_instances() {
        if ($this->_instances === null) {
            $this->_instances = enrol_get_instances($this->course->id, true);
        }
        return $this->_instances;
    }

    /**
     * Returns the names for all of the enrolment instances for this course.
     *
     * @return array
     */
    public function get_enrolment_instance_names() {
        if ($this->_inames === null) {
            $instances = $this->get_enrolment_instances();
            $plugins = $this->get_enrolment_plugins();
            foreach ($instances as $key=>$instance) {
                if (!isset($plugins[$instance->enrol])) {
                    // weird, some broken stuff in plugin
                    unset($instances[$key]);
                    continue;
                }
                $this->_inames[$key] = $plugins[$instance->enrol]->get_instance_name($instance);
            }
        }
        return $this->_inames;
    }

    /**
     * Gets all of the enrolment plugins that are active for this course.
     *
     * @return array
     */
    public function get_enrolment_plugins() {
        if ($this->_plugins === null) {
            $this->_plugins = enrol_get_plugins(true);
        }
        return $this->_plugins;
    }

    /**
     * Gets all of the roles this course can contain.
     *
     * @return array
     */
    public function get_all_roles() {
        if ($this->_roles === null) {
            $this->_roles = role_fix_names(get_all_roles(), $this->context);
        }
        return $this->_roles;
    }

    /**
     * Gets all of the assignable roles for this course.
     *
     * @return array
     */
    public function get_assignable_roles($otherusers = false) {
        if ($this->_assignableroles === null) {
            $this->_assignableroles = get_assignable_roles($this->context, ROLENAME_ALIAS, false); // verifies unassign access control too
        }

        if ($otherusers) {
            if (!is_array($this->_assignablerolesothers)) {
                $this->_assignablerolesothers = array();
                list($courseviewroles, $ignored) = get_roles_with_cap_in_context($this->context, 'moodle/course:view');
                foreach ($this->_assignableroles as $roleid=>$role) {
                    if (isset($courseviewroles[$roleid])) {
                        $this->_assignablerolesothers[$roleid] = $role;
                    }
                }
            }
            return $this->_assignablerolesothers;
        } else {
            return $this->_assignableroles;
        }
    }

    /**
     * Gets all of the groups for this course.
     *
     * @return array
     */
    public function get_all_groups() {
        if ($this->_groups === null) {
            $this->_groups = groups_get_all_groups($this->course->id);
            foreach ($this->_groups as $gid=>$group) {
                $this->_groups[$gid]->name = format_string($group->name);
            }
        }
        return $this->_groups;
    }

    /**
     * Unenrols a user from the course given the users ue entry
     *
     * @global moodle_database $DB
     * @param stdClass $ue
     * @return bool
     */
    public function unenrol_user($ue) {
        global $DB;
        list ($instance, $plugin) = $this->get_user_enrolment_components($ue);
        if ($instance && $plugin && $plugin->allow_unenrol($instance) && has_capability("enrol/$instance->enrol:unenrol", $this->context)) {
            $plugin->unenrol_user($instance, $ue->userid);
            return true;
        }
        return false;
    }

    /**
     * Given a user enrolment record this method returns the plugin and enrolment
     * instance that relate to it.
     *
     * @param stdClass|int $userenrolment
     * @return array array($instance, $plugin)
     */
    public function get_user_enrolment_components($userenrolment) {
        global $DB;
        if (is_numeric($userenrolment)) {
            $userenrolment = $DB->get_record('user_enrolments', array('id'=>(int)$userenrolment));
        }
        $instances = $this->get_enrolment_instances();
        $plugins = $this->get_enrolment_plugins();
        if (!$userenrolment || !isset($instances[$userenrolment->enrolid])) {
            return array(false, false);
        }
        $instance = $instances[$userenrolment->enrolid];
        $plugin = $plugins[$instance->enrol];
        return array($instance, $plugin);
    }

    /**
     * Removes an assigned role from a user.
     *
     * @global moodle_database $DB
     * @param int $userid
     * @param int $roleid
     * @return bool
     */
    public function unassign_role_from_user($userid, $roleid) {
        global $DB;
        require_capability('moodle/role:assign', $this->context);
        $user = $DB->get_record('user', array('id'=>$userid), '*', MUST_EXIST);
        try {
            role_unassign($roleid, $user->id, $this->context->id, '', NULL);
        } catch (Exception $e) {
            if (defined('AJAX_SCRIPT')) {
                throw $e;
            }
            return false;
        }
        return true;
    }

    /**
     * Assigns a role to a user.
     *
     * @param int $roleid
     * @param int $userid
     * @return int|false
     */
    public function assign_role_to_user($roleid, $userid) {
        require_capability('moodle/role:assign', $this->context);
        if (!array_key_exists($roleid, $this->get_assignable_roles())) {
            if (defined('AJAX_SCRIPT')) {
                throw new moodle_exception('invalidrole');
            }
            return false;
        }
        return role_assign($roleid, $userid, $this->context->id, '', NULL);
    }

    /**
     * Adds a user to a group
     *
     * @param stdClass $user
     * @param int $groupid
     * @return bool
     */
    public function add_user_to_group($user, $groupid) {
        require_capability('moodle/course:managegroups', $this->context);
        $group = $this->get_group($groupid);
        if (!$group) {
            return false;
        }
        return groups_add_member($group->id, $user->id);
    }

    /**
     * Removes a user from a group
     *
     * @global moodle_database $DB
     * @param StdClass $user
     * @param int $groupid
     * @return bool
     */
    public function remove_user_from_group($user, $groupid) {
        global $DB;
        require_capability('moodle/course:managegroups', $this->context);
        $group = $this->get_group($groupid);
        if (!$group) {
            return false;
        }
        return groups_remove_member($group, $user);
    }

    /**
     * Gets the requested group
     *
     * @param int $groupid
     * @return stdClass|int
     */
    public function get_group($groupid) {
        $groups = $this->get_all_groups();
        if (!array_key_exists($groupid, $groups)) {
            return false;
        }
        return $groups[$groupid];
    }

    /**
     * Edits an enrolment
     *
     * @param stdClass $userenrolment
     * @param stdClass $data
     * @return bool
     */
    public function edit_enrolment($userenrolment, $data) {
        //Only allow editing if the user has the appropriate capability
        //Already checked in /enrol/users.php but checking again in case this function is called from elsewhere
        list($instance, $plugin) = $this->get_user_enrolment_components($userenrolment);
        if ($instance && $plugin && $plugin->allow_manage($instance) && has_capability("enrol/$instance->enrol:manage", $this->context)) {
            if (!isset($data->status)) {
                $data->status = $userenrolment->status;
            }
            $plugin->update_user_enrol($instance, $userenrolment->userid, $data->status, $data->timestart, $data->timeend);
            return true;
        }
        return false;
    }

    /**
     * Returns the current enrolment filter that is being applied by this class
     * @return string
     */
    public function get_enrolment_filter() {
        return $this->instancefilter;
    }

    /**
     * Gets the roles assigned to this user that are applicable for this course.
     *
     * @param int $userid
     * @return array
     */
    public function get_user_roles($userid) {
        $roles = array();
        $ras = get_user_roles($this->context, $userid, true, 'c.contextlevel DESC, r.sortorder ASC');
        foreach ($ras as $ra) {
            if ($ra->contextid != $this->context->id) {
                if (!array_key_exists($ra->roleid, $roles)) {
                    $roles[$ra->roleid] = null;
                }
                // higher ras, course always takes precedence
                continue;
            }
            if (array_key_exists($ra->roleid, $roles) && $roles[$ra->roleid] === false) {
                continue;
            }
            $roles[$ra->roleid] = ($ra->itemid == 0 and $ra->component === '');
        }
        return $roles;
    }

    /**
     * Gets the enrolments this user has in the course
     *
     * @global moodle_database $DB
     * @param int $userid
     * @return array
     */
    public function get_user_enrolments($userid) {
        global $DB;
        list($instancessql, $params, $filter) = $this->get_instance_sql();
        $params['userid'] = $userid;
        $userenrolments = $DB->get_records_select('user_enrolments', "enrolid $instancessql AND userid = :userid", $params);
        $instances = $this->get_enrolment_instances();
        $plugins = $this->get_enrolment_plugins();
        $inames = $this->get_enrolment_instance_names();
        foreach ($userenrolments as &$ue) {
            $ue->enrolmentinstance     = $instances[$ue->enrolid];
            $ue->enrolmentplugin       = $plugins[$ue->enrolmentinstance->enrol];
            $ue->enrolmentinstancename = $inames[$ue->enrolmentinstance->id];
        }
        return $userenrolments;
    }

    /**
     * Gets the groups this user belongs to
     *
     * @param int $userid
     * @return array
     */
    public function get_user_groups($userid) {
        return groups_get_all_groups($this->course->id, $userid, 0, 'g.id');
    }

    /**
     * Retursn an array of params that would go into the URL to return to this
     * exact page.
     *
     * @return array
     */
    public function get_url_params() {
        $args = array(
            'id' => $this->course->id
        );
        if (!empty($this->instancefilter)) {
            $args['ifilter'] = $this->instancefilter;
        }
        return $args;
    }

    /**
     * Returns the course this object is managing enrolments for
     *
     * @return stdClass
     */
    public function get_course() {
        return $this->course;
    }

    /**
     * Returns the course context
     *
     * @return stdClass
     */
    public function get_context() {
        return $this->context;
    }

    /**
     * Gets an array of other users in this course ready for display.
     *
     * Other users are users who have been assigned or inherited roles within this
     * course but have not been enrolled.
     *
     * @param core_enrol_renderer $renderer
     * @param moodle_url $pageurl
     * @param string $sort
     * @param string $direction ASC | DESC
     * @param int $page Starting from 0
     * @param int $perpage
     * @return array
     */
    public function get_other_users_for_display(core_enrol_renderer $renderer, moodle_url $pageurl, $sort, $direction, $page, $perpage) {

        $userroles = $this->get_other_users($sort, $direction, $page, $perpage);
        $roles = $this->get_all_roles();

        $courseid   = $this->get_course()->id;
        $context    = $this->get_context();

        $users = array();
        foreach ($userroles as $userrole) {
            if (!array_key_exists($userrole->id, $users)) {
                $users[$userrole->id] = array(
                    'userid'     => $userrole->id,
                    'courseid'   => $courseid,
                    'picture'    => new user_picture($userrole),
                    'firstname'  => fullname($userrole, true),
                    'email'      => $userrole->email,
                    'roles'      => array()
                );
            }
            $a = new stdClass;
            $a->role = $roles[$userrole->roleid]->localname;
            $changeable = ($userrole->component == '');
            if ($userrole->contextid == $this->context->id) {
                $roletext = get_string('rolefromthiscourse', 'enrol', $a);
            } else {
                $changeable = false;
                switch ($userrole->contextlevel) {
                    case CONTEXT_COURSE :
                        // Meta course
                        $roletext = get_string('rolefrommetacourse', 'enrol', $a);
                        break;
                    case CONTEXT_COURSECAT :
                        $roletext = get_string('rolefromcategory', 'enrol', $a);
                        break;
                    case CONTEXT_SYSTEM:
                    default:
                        $roletext = get_string('rolefromsystem', 'enrol', $a);
                        break;
                }
            }
            $users[$userrole->id]['roles'][$userrole->roleid] = array(
                'text' => $roletext,
                'unchangeable' => !$changeable
            );
        }
        return $users;
    }

    /**
     * Gets an array of users for display, this includes minimal user information
     * as well as minimal information on the users roles, groups, and enrolments.
     *
     * @param core_enrol_renderer $renderer
     * @param moodle_url $pageurl
     * @param int $sort
     * @param string $direction ASC or DESC
     * @param int $page
     * @param int $perpage
     * @return array
     */
    public function get_users_for_display(course_enrolment_manager $manager, $sort, $direction, $page, $perpage) {
        $pageurl = $manager->get_moodlepage()->url;
        $users = $this->get_users($sort, $direction, $page, $perpage);

        $now = time();
        $strnever = get_string('never');
        $straddgroup = get_string('addgroup', 'group');
        $strunenrol = get_string('unenrol', 'enrol');
        $stredit = get_string('edit');

        $allroles   = $this->get_all_roles();
        $assignable = $this->get_assignable_roles();
        $allgroups  = $this->get_all_groups();
        $courseid   = $this->get_course()->id;
        $context    = $this->get_context();
        $canmanagegroups = has_capability('moodle/course:managegroups', $context);

        $url = new moodle_url($pageurl, $this->get_url_params());

        $userdetails = array();
        foreach ($users as $user) {
            $details = array(
                'userid'     => $user->id,
                'courseid'   => $courseid,
                'picture'    => new user_picture($user),
                'firstname'  => fullname($user, true),
                'email'      => $user->email,
                'lastseen'   => $strnever,
                'roles'      => array(),
                'groups'     => array(),
                'enrolments' => array()
            );

            if ($user->lastaccess) {
                $details['lastseen'] = format_time($now - $user->lastaccess);
            }

            // Roles
            foreach ($this->get_user_roles($user->id) as $rid=>$rassignable) {
                $details['roles'][$rid] = array('text'=>$allroles[$rid]->localname, 'unchangeable'=>(!$rassignable || !isset($assignable[$rid])));
            }

            // Users
            $usergroups = $this->get_user_groups($user->id);
            foreach($usergroups as $gid=>$unused) {
                $details['groups'][$gid] = $allgroups[$gid]->name;
            }

            // Enrolments
            foreach ($this->get_user_enrolments($user->id) as $ue) {
                if ($ue->timestart and $ue->timeend) {
                    $period = get_string('periodstartend', 'enrol', array('start'=>userdate($ue->timestart), 'end'=>userdate($ue->timeend)));
                    $periodoutside = ($ue->timestart && $ue->timeend && $now < $ue->timestart && $now > $ue->timeend);
                } else if ($ue->timestart) {
                    $period = get_string('periodstart', 'enrol', userdate($ue->timestart));
                    $periodoutside = ($ue->timestart && $now < $ue->timestart);
                } else if ($ue->timeend) {
                    $period = get_string('periodend', 'enrol', userdate($ue->timeend));
                    $periodoutside = ($ue->timeend && $now > $ue->timeend);
                } else {
                    $period = '';
                    $periodoutside = false;
                }
                $details['enrolments'][$ue->id] = array(
                    'text' => $ue->enrolmentinstancename,
                    'period' => $period,
                    'dimmed' =>  ($periodoutside || $ue->status != ENROL_USER_ACTIVE),
                    'actions' => $ue->enrolmentplugin->get_user_enrolment_actions($manager, $ue)
                );
            }
            $userdetails[$user->id] = $details;
        }
        return $userdetails;
    }

    public function get_manual_enrol_buttons() {
        $plugins = $this->get_enrolment_plugins();
        $buttons = array();
        foreach ($plugins as $plugin) {
            $newbutton = $plugin->get_manual_enrol_button($this);
            if (is_array($newbutton)) {
                $buttons += $newbutton;
            } else if ($newbutton instanceof enrol_user_button) {
                $buttons[] = $newbutton;
            }
        }
        return $buttons;
    }

    public function has_instance($enrolpluginname) {
        // Make sure manual enrolments instance exists
        foreach ($this->get_enrolment_instances() as $instance) {
            if ($instance->enrol == $enrolpluginname) {
                return true;
            }
        }
        return false;
    }

    /**
     * Returns the enrolment plugin that the course manager was being filtered to.
     *
     * If no filter was being applied then this function returns false.
     *
     * @return enrol_plugin
     */
    public function get_filtered_enrolment_plugin() {
        $instances = $this->get_enrolment_instances();
        $plugins = $this->get_enrolment_plugins();

        if (empty($this->instancefilter) || !array_key_exists($this->instancefilter, $instances)) {
            return false;
        }

        $instance = $instances[$this->instancefilter];
        return $plugins[$instance->enrol];
    }

    /**
     * Returns and array of users + enrolment details.
     *
     * Given an array of user id's this function returns and array of user enrolments for those users
     * as well as enough user information to display the users name and picture for each enrolment.
     *
     * @global moodle_database $DB
     * @param array $userids
     * @return array
     */
    public function get_users_enrolments(array $userids) {
        global $DB;

        $instances = $this->get_enrolment_instances();
        $plugins = $this->get_enrolment_plugins();

        if  (!empty($this->instancefilter)) {
            $instancesql = ' = :instanceid';
            $instanceparams = array('instanceid' => $this->instancefilter);
        } else {
            list($instancesql, $instanceparams) = $DB->get_in_or_equal(array_keys($instances), SQL_PARAMS_NAMED, 'instanceid0000');
        }

        $userfields = user_picture::fields('u');
        list($idsql, $idparams) = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED, 'userid0000');

        $sql = "SELECT ue.id AS ueid, ue.status, ue.enrolid, ue.userid, ue.timestart, ue.timeend, ue.modifierid, ue.timecreated, ue.timemodified, $userfields
                  FROM {user_enrolments} ue
             LEFT JOIN {user} u ON u.id = ue.userid
                 WHERE ue.enrolid $instancesql AND
                       u.id $idsql
              ORDER BY u.firstname ASC, u.lastname ASC";

        $rs = $DB->get_recordset_sql($sql, $idparams + $instanceparams);
        $users = array();
        foreach ($rs as $ue) {
            $user = user_picture::unalias($ue);
            $ue->id = $ue->ueid;
            unset($ue->ueid);
            if (!array_key_exists($user->id, $users)) {
                $user->enrolments = array();
                $users[$user->id] = $user;
            }
            $ue->enrolmentinstance = $instances[$ue->enrolid];
            $ue->enrolmentplugin = $plugins[$ue->enrolmentinstance->enrol];
            $users[$user->id]->enrolments[$ue->id] = $ue;
        }
        $rs->close();
        return $users;
    }
}

/**
 * A button that is used to enrol users in a course
 *
 * @copyright 2010 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class enrol_user_button extends single_button {

    /**
     * An array containing JS YUI modules required by this button
     * @var array
     */
    protected $jsyuimodules = array();

    /**
     * An array containing JS initialisation calls required by this button
     * @var array
     */
    protected $jsinitcalls = array();

    /**
     * An array strings required by JS for this button
     * @var array
     */
    protected $jsstrings = array();

    /**
     * Initialises the new enrol_user_button
     *
     * @staticvar int $count The number of enrol user buttons already created
     * @param moodle_url $url
     * @param string $label The text to display in the button
     * @param string $method Either post or get
     */
    public function __construct(moodle_url $url, $label, $method = 'post') {
        static $count = 0;
        $count ++;
        parent::__construct($url, $label, $method);
        $this->class = 'singlebutton enrolusersbutton';
        $this->formid = 'enrolusersbutton-'.$count;
    }

    /**
     * Adds a YUI module call that will be added to the page when the button is used.
     *
     * @param string|array $modules One or more modules to require
     * @param string $function The JS function to call
     * @param array $arguments An array of arguments to pass to the function
     * @param string $galleryversion The YUI gallery version of any modules required
     * @param bool $ondomready If true the call is postponed until the DOM is finished loading
     */
    public function require_yui_module($modules, $function, array $arguments = null, $galleryversion = '2010.04.08-12-35', $ondomready = false) {
        $js = new stdClass;
        $js->modules = (array)$modules;
        $js->function = $function;
        $js->arguments = $arguments;
        $js->galleryversion = $galleryversion;
        $js->ondomready = $ondomready;
        $this->jsyuimodules[] = $js;
    }

    /**
     * Adds a JS initialisation call to the page when the button is used.
     *
     * @param string $function The function to call
     * @param array $extraarguments An array of arguments to pass to the function
     * @param bool $ondomready If true the call is postponed until the DOM is finished loading
     * @param array $module A module definition
     */
    public function require_js_init_call($function, array $extraarguments = null, $ondomready = false, array $module = null) {
        $js = new stdClass;
        $js->function = $function;
        $js->extraarguments = $extraarguments;
        $js->ondomready = $ondomready;
        $js->module = $module;
        $this->jsinitcalls[] = $js;
    }

    /**
     * Requires strings for JS that will be loaded when the button is used.
     *
     * @param type $identifiers
     * @param string $component
     * @param mixed $a
     */
    public function strings_for_js($identifiers, $component = 'moodle', $a = null) {
        $string = new stdClass;
        $string->identifiers = (array)$identifiers;
        $string->component = $component;
        $string->a = $a;
        $this->jsstrings[] = $string;
    }

    /**
     * Initialises the JS that is required by this button
     *
     * @param moodle_page $page
     */
    public function initialise_js(moodle_page $page) {
        foreach ($this->jsyuimodules as $js) {
            $page->requires->yui_module($js->modules, $js->function, $js->arguments, $js->galleryversion, $js->ondomready);
        }
        foreach ($this->jsinitcalls as $js) {
            $page->requires->js_init_call($js->function, $js->extraarguments, $js->ondomready, $js->module);
        }
        foreach ($this->jsstrings as $string) {
            $page->requires->strings_for_js($string->identifiers, $string->component, $string->a);
        }
    }
}

/**
 * User enrolment action
 *
 * This class is used to manage a renderable ue action such as editing an user enrolment or deleting
 * a user enrolment.
 *
 * @copyright  2011 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_enrolment_action implements renderable {

    /**
     * The icon to display for the action
     * @var pix_icon
     */
    protected $icon;

    /**
     * The title for the action
     * @var string
     */
    protected $title;

    /**
     * The URL to the action
     * @var moodle_url
     */
    protected $url;

    /**
     * An array of HTML attributes
     * @var array
     */
    protected $attributes = array();

    /**
     * Constructor
     * @param pix_icon $icon
     * @param string $title
     * @param moodle_url $url
     * @param array $attributes
     */
    public function __construct(pix_icon $icon, $title, $url, array $attributes = null) {
        $this->icon = $icon;
        $this->title = $title;
        $this->url = new moodle_url($url);
        if (!empty($attributes)) {
            $this->attributes = $attributes;
        }
        $this->attributes['title'] = $title;
    }

    /**
     * Returns the icon for this action
     * @return pix_icon
     */
    public function get_icon() {
        return $this->icon;
    }

    /**
     * Returns the title for this action
     * @return string
     */
    public function get_title() {
        return $this->title;
    }

    /**
     * Returns the URL for this action
     * @return moodle_url
     */
    public function get_url() {
        return $this->url;
    }

    /**
     * Returns the attributes to use for this action
     * @return array
     */
    public function get_attributes() {
        return $this->attributes;
    }
}

class enrol_ajax_exception extends moodle_exception {
    /**
     * Constructor
     * @param string $errorcode The name of the string from error.php to print
     * @param string $module name of module
     * @param string $link The url where the user will be prompted to continue. If no url is provided the user will be directed to the site index page.
     * @param object $a Extra words and phrases that might be required in the error string
     * @param string $debuginfo optional debugging information
     */
    public function __construct($errorcode, $link = '', $a = NULL, $debuginfo = null) {
        parent::__construct($errorcode, 'enrol', $link, $a, $debuginfo);
    }
}

/**
 * This class is used to manage a bulk operations for enrolment plugins.
 *
 * @copyright 2011 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class enrol_bulk_enrolment_operation {

    /**
     * The course enrolment manager
     * @var course_enrolment_manager
     */
    protected $manager;

    /**
     * The enrolment plugin to which this operation belongs
     * @var enrol_plugin
     */
    protected $plugin;

    /**
     * Contructor
     * @param course_enrolment_manager $manager
     * @param stdClass $plugin
     */
    public function __construct(course_enrolment_manager $manager, enrol_plugin $plugin = null) {
        $this->manager = $manager;
        $this->plugin = $plugin;
    }

    /**
     * Returns a moodleform used for this operation, or false if no form is required and the action
     * should be immediatly processed.
     *
     * @param moodle_url|string $defaultaction
     * @param mixed $defaultcustomdata
     * @return enrol_bulk_enrolment_change_form|moodleform|false
     */
    public function get_form($defaultaction = null, $defaultcustomdata = null) {
        return false;
    }

    /**
     * Returns the title to use for this bulk operation
     *
     * @return string
     */
    abstract public function get_title();

    /**
     * Returns the identifier for this bulk operation.
     * This should be the same identifier used by the plugins function when returning
     * all of its bulk operations.
     *
     * @return string
     */
    abstract public function get_identifier();

    /**
     * Processes the bulk operation on the given users
     *
     * @param course_enrolment_manager $manager
     * @param array $users
     * @param stdClass $properties
     */
    abstract public function process(course_enrolment_manager $manager, array $users, stdClass $properties);
}