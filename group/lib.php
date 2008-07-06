<?php
/**
 * Extra library for groups and groupings.
 *
 * @copyright &copy; 2006 The Open University
 * @author J.White AT open.ac.uk, Petr Skoda (skodak)
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package groups
 */

/*
 * INTERNAL FUNCTIONS - to be used by moodle core only
 * require_once $CFG->dirroot.'/group/lib.php' must be used
 */

/**
 * Adds a specified user to a group
 * @param int $userid   The user id
 * @param int $groupid  The group id
 * @return boolean True if user added successfully or the user is already a
 * member of the group, false otherwise.
 */
function groups_add_member($groupid, $userid) {
    if (!groups_group_exists($groupid)) {
        return false;
    }

    if (groups_is_member($groupid, $userid)) {
        return true;
    }

    $member = new object();
    $member->groupid   = $groupid;
    $member->userid    = $userid;
    $member->timeadded = time();

    if (!insert_record('groups_members', $member)) {
        return false;
    }

    //update group info
    set_field('groups', 'timemodified', $member->timeadded, 'id', $groupid);

    //trigger groups events
    $eventdata = new object();
    $eventdata->groupid = $groupid;
    $eventdata->userid  = $userid;
    events_trigger('groups_member_added', $eventdata);

    return true;
}

/**
 * Deletes the link between the specified user and group.
 * @param int $groupid The group to delete the user from
 * @param int $userid The user to delete
 * @return boolean True if deletion was successful, false otherwise
 */
function groups_remove_member($groupid, $userid) {
    if (!groups_group_exists($groupid)) {
        return false;
    }

    if (!groups_is_member($groupid, $userid)) {
        return true;
    }

    if (!delete_records('groups_members', 'groupid', $groupid, 'userid', $userid)) {
        return false;
    }
    //update group info
    set_field('groups', 'timemodified', time(), 'id', $groupid);

    //trigger groups events
    $eventdata = new object();
    $eventdata->groupid = $groupid;
    $eventdata->userid  = $userid;
    events_trigger('groups_member_removed', $eventdata);

    return true;
}

/**
 * Add a new group
 * @param object $data group properties (with magic quotes)
 * @param object $um upload manager with group picture
 * @return id of group or false if error
 */
function groups_create_group($data, $um=false) {
    global $CFG;
    require_once("$CFG->libdir/gdlib.php");

    $data->timecreated = time();
    $data->timemodified = $data->timecreated;
    $data->name = trim($data->name);
    $id = insert_record('groups', $data);

    if ($id) {
        $data->id = $id;
        if ($um) {
            //update image
            if (save_profile_image($id, $um, 'groups')) {
                set_field('groups', 'picture', 1, 'id', $id);
            }
            $data->picture = 1;
        }

        //trigger groups events
        events_trigger('groups_group_created', stripslashes_recursive($data));
    }

    return $id;
}

/**
 * Add a new grouping
 * @param object $data grouping properties (with magic quotes)
 * @return id of grouping or false if error
 */
function groups_create_grouping($data) {
    global $CFG;

    $data->timecreated = time();
    $data->timemodified = $data->timecreated;
    $data->name = trim($data->name);

    $id = insert_record('groupings', $data);
    
    if ($id) {
        //trigger groups events
        $data->id = $id;
        events_trigger('groups_grouping_created', stripslashes_recursive($data));
    }

    return $id;
}

/**
 * Update group
 * @param object $data group properties (with magic quotes)
 * @param object $um upload manager with group picture
 * @return boolean success
 */
function groups_update_group($data, $um=false) {
    global $CFG;
    require_once("$CFG->libdir/gdlib.php");

    $data->timemodified = time();
    $data->name = trim($data->name);
    $result = update_record('groups', $data);

    if ($result) {
        if ($um) {
            //update image
            if (save_profile_image($data->id, $um, 'groups')) {
                set_field('groups', 'picture', 1, 'id', $data->id);
                $data->picture = 1;
            }
        }

        //trigger groups events
        events_trigger('groups_group_updated', stripslashes_recursive($data));
    }

    return $result;
}

/**
 * Update grouping
 * @param object $data grouping properties (with magic quotes)
 * @return boolean success
 */
function groups_update_grouping($data) {
    global $CFG;
    $data->timemodified = time();
    $data->name = trim($data->name);
    $result = update_record('groupings', $data);
    if ($result) {
        //trigger groups events
        events_trigger('groups_grouping_updated', stripslashes_recursive($data));
    }
    return $result;
}

/**
 * Delete a group best effort, first removing members and links with courses and groupings.
 * Removes group avatar too.
 * @param mixed $grouporid The id of group to delete or full group object
 * @return boolean True if deletion was successful, false otherwise
 */
function groups_delete_group($grouporid) {
    global $CFG;
    require_once($CFG->libdir.'/gdlib.php');

    if (is_object($grouporid)) {
        $groupid = $grouporid->id;
        $group   = $grouporid;
    } else {
        $groupid = $grouporid;
        if (!$group = get_record('groups', 'id', $groupid)) {
            return false;
        }
    }

    // delete group calendar events
    delete_records('event', 'groupid', $groupid);
    //first delete usage in groupings_groups
    delete_records('groupings_groups', 'groupid', $groupid);
    //delete members
    delete_records('groups_members', 'groupid', $groupid);
    //then imge
    delete_profile_image($groupid, 'groups');
    //group itself last
    $result = delete_records('groups', 'id', $groupid);

    if ($result) {
        //trigger groups events
        events_trigger('groups_group_deleted', $group);
    }

    return $result;
}

/**
 * Delete grouping
 * @param int $groupingid
 * @return bool success
 */
function groups_delete_grouping($groupingorid) {
    if (is_object($groupingorid)) {
        $groupingid = $groupingorid->id;
        $grouping   = $groupingorid;
    } else {
        $groupingid = $groupingorid;
        if (!$grouping = get_record('groupings', 'id', $groupingorid)) {
            return false;
        }
    }

    //first delete usage in groupings_groups
    delete_records('groupings_groups', 'groupingid', $groupingid);
    // remove the default groupingid from course
    set_field('course', 'defaultgroupingid', 0, 'defaultgroupingid', $groupingid);
    // remove the groupingid from all course modules
    set_field('course_modules', 'groupingid', 0, 'groupingid', $groupingid);
    //group itself last
    $result = delete_records('groupings', 'id', $groupingid);

    if ($result) {
        //trigger groups events
        events_trigger('groups_grouping_deleted', $grouping);
    }

    return $result;
}

/**
 * Remove all users (or one user) from all groups in course
 * @param int $courseid
 * @param int $userid 0 means all users
 * @param bool $showfeedback
 * @return bool success
 */
function groups_delete_group_members($courseid, $userid=0, $showfeedback=false) {
    global $CFG;

    if (is_bool($userid)) {
        debugging('Incorrect userid function parameter');
        return false;
    }

    if ($userid) {
        $usersql = "AND userid = $userid";
    } else {
        $usersql = "";
    }

    $groupssql = "SELECT id FROM {$CFG->prefix}groups g WHERE g.courseid = $courseid";
    delete_records_select('groups_members', "groupid IN ($groupssql) $usersql");

    //trigger groups events
    $eventdata = new object();
    $eventdata->courseid = $courseid;
    $eventdata->userid   = $userid;
    events_trigger('groups_members_removed', $eventdata);

    if ($showfeedback) {
        notify(get_string('deleted').' groups_members');
    }

    return true;
}

/**
 * Remove all groups from all groupings in course
 * @param int $courseid
 * @param bool $showfeedback
 * @return bool success
 */
function groups_delete_groupings_groups($courseid, $showfeedback=false) {
    global $CFG;

    $groupssql = "SELECT id FROM {$CFG->prefix}groups g WHERE g.courseid = $courseid";
    delete_records_select('groupings_groups', "groupid IN ($groupssql)");

    //trigger groups events
    events_trigger('groups_groupings_groups_removed', $courseid);

    if ($showfeedback) {
        notify(get_string('deleted').' groupings_groups');
    }

    return true;
}

/**
 * Delete all groups from course
 * @param int $courseid
 * @param bool $showfeedback
 * @return bool success
 */
function groups_delete_groups($courseid, $showfeedback=false) {
    global $CFG;
    require_once($CFG->libdir.'/gdlib.php');

    $groupssql = "SELECT id FROM {$CFG->prefix}groups g WHERE g.courseid = $courseid";

    // delete any uses of groups
    groups_delete_groupings_groups($courseid, $showfeedback);
    groups_delete_group_members($courseid, 0, $showfeedback);

    // delete group pictures
    if ($groups = get_records('groups', 'courseid', $courseid)) {
        foreach($groups as $group) {
            delete_profile_image($group->id, 'groups');
        }
    }

    // delete group calendar events
    delete_records_select('event', "groupid IN ($groupssql)");

    delete_records('groups', 'courseid', $courseid);

    //trigger groups events
    events_trigger('groups_groups_deleted', $courseid);

    if ($showfeedback) {
        notify(get_string('deleted').' groups');
    }

    return true;
}

/**
 * Delete all groupings from course
 * @param int $courseid
 * @param bool $showfeedback
 * @return bool success
 */
function groups_delete_groupings($courseid, $showfeedback=false) {
    global $CFG;

    // delete any uses of groupings
    $sql = "DELETE FROM {$CFG->prefix}groupings_groups
             WHERE groupingid in (SELECT id FROM {$CFG->prefix}groupings g WHERE g.courseid = $courseid)";
    execute_sql($sql, false);

    // remove the default groupingid from course
    set_field('course', 'defaultgroupingid', 0, 'id', $courseid);
    // remove the groupingid from all course modules
    set_field('course_modules', 'groupingid', 0, 'course', $courseid);

    delete_records('groupings', 'courseid', $courseid);

    //trigger groups events
    events_trigger('groups_groupings_deleted', $courseid);

    if ($showfeedback) {
        notify(get_string('deleted').' groupings');
    }

    return true;
}

/* =================================== */
/* various functions used by groups UI */
/* =================================== */

/**
 * Gets the users for a course who are not in a specified group, and returns
 * them in an array organised by role. For the array format, see 
 * groups_get_members_by_role.
 * @param int $groupid The id of the group
 * @param string searchtext similar to searchtext in role assign, search
 * @return array An array of role id or '*' => information about that role 
 *   including a list of users
 */
function groups_get_users_not_in_group_by_role($courseid, $groupid, $searchtext='', $sort = 'u.lastname ASC') {

    global $CFG;
    $context = get_context_instance(CONTEXT_COURSE, $courseid);
    
    if ($searchtext !== '') {   // Search for a subset of remaining users
        $LIKE      = sql_ilike();
        $FULLNAME  = sql_fullname();
        $wheresearch = " AND u.id IN (SELECT id FROM {$CFG->prefix}user WHERE $FULLNAME $LIKE '%$searchtext%' OR email $LIKE '%$searchtext%' )";
    } else {
        $wheresearch = '';
    }

/// Get list of allowed roles     
    if(!($validroleids=groups_get_possible_roles($context))) {
        return;
    }
    $roleids = '('.implode(',', $validroleids).')';

/// Construct the main SQL
    $select = " SELECT r.id AS roleid,r.shortname AS roleshortname,r.name AS rolename,
                       u.id AS userid, u.firstname, u.lastname";
    $from   = " FROM {$CFG->prefix}user u
                INNER JOIN {$CFG->prefix}role_assignments ra ON ra.userid = u.id
                INNER JOIN {$CFG->prefix}role r ON r.id = ra.roleid";

    $where  = " WHERE ra.contextid ".get_related_contexts_string($context)."
                  AND u.deleted = 0
                  AND ra.roleid in $roleids
                  AND u.id NOT IN (SELECT userid
                                   FROM {$CFG->prefix}groups_members
                                   WHERE groupid = $groupid)
                  $wheresearch";
    $orderby = " ORDER BY $sort";

    return groups_calculate_role_people(get_recordset_sql(
        $select.$from.$where.$orderby),$context);
}


/**
 * Obtains a list of the possible roles that group members might come from,
 * on a course. Generally this includes all the roles who would have 
 * course:view on that course, except the doanything roles.
 * @param object $context Context of course
 * @return Array of role ID integers, or false if error/none.
 */
function groups_get_possible_roles($context) {
    $capability = 'moodle/course:view';
    $doanything = false;

    // find all possible "student" roles
    if ($possibleroles = get_roles_with_capability($capability, CAP_ALLOW, $context)) {
        if (!$doanything) {
            if (!$sitecontext = get_context_instance(CONTEXT_SYSTEM)) {
                return false;    // Something is seriously wrong
            }
            $doanythingroles = get_roles_with_capability('moodle/site:doanything', CAP_ALLOW, $sitecontext);
        }

        $validroleids = array();
        foreach ($possibleroles as $possiblerole) {
            if (!$doanything) {
                if (isset($doanythingroles[$possiblerole->id])) {  // We don't want these included
                    continue;
                }
            }
            if ($caps = role_context_capabilities($possiblerole->id, $context, $capability)) { // resolved list
                if (isset($caps[$capability]) && $caps[$capability] > 0) { // resolved capability > 0
                    $validroleids[] = $possiblerole->id;
                }
            }
        }
        if (empty($validroleids)) {
            return false;
        }
        return $validroleids;
    } else {
        return false;  // No need to continue, since no roles have this capability set
    }    
}


/**
 * Gets potential group members for grouping
 * @param int $courseid The id of the course
 * @param int $roleid The role to select users from
 * @param string $orderby The colum to sort users by
 * @return array An array of the users
 */
function groups_get_potential_members($courseid, $roleid = null, $orderby = 'lastname,firstname') {
	global $CFG;

    $context = get_context_instance(CONTEXT_COURSE, $courseid);
    $sitecontext = get_context_instance(CONTEXT_SYSTEM);
    $rolenames = array();
    $avoidroles = array();

    if ($roles = get_roles_used_in_context($context, true)) {

        $canviewroles    = get_roles_with_capability('moodle/course:view', CAP_ALLOW, $context);
        $doanythingroles = get_roles_with_capability('moodle/site:doanything', CAP_ALLOW, $sitecontext);

        foreach ($roles as $role) {
            if (!isset($canviewroles[$role->id])) {   // Avoid this role (eg course creator)
                $avoidroles[] = $role->id;
                unset($roles[$role->id]);
                continue;
            }
            if (isset($doanythingroles[$role->id])) {   // Avoid this role (ie admin)
                $avoidroles[] = $role->id;
                unset($roles[$role->id]);
                continue;
            }
            $rolenames[$role->id] = strip_tags(role_get_name($role, $context));   // Used in menus etc later on
        }
    }

    $select = 'SELECT u.id, u.username, u.firstname, u.lastname, u.idnumber ';
    $from   = "FROM {$CFG->prefix}user u INNER JOIN
               {$CFG->prefix}role_assignments r on u.id=r.userid ";

    if ($avoidroles) {
        $adminroles = 'AND r.roleid NOT IN (';
        $adminroles .= implode(',', $avoidroles);
        $adminroles .= ')';
    } else {
        $adminroles = '';
    }

    // we are looking for all users with this role assigned in this context or higher
    if ($usercontexts = get_parent_contexts($context)) {
        $listofcontexts = '('.implode(',', $usercontexts).')';
    } else {
        $listofcontexts = '('.$sitecontext->id.')'; // must be site
    }

    if ($roleid) {
        $selectrole = " AND r.roleid = $roleid ";
    } else {
        $selectrole = " ";
    }

    $where  = "WHERE (r.contextid = $context->id OR r.contextid in $listofcontexts)
                     AND u.deleted = 0 $selectrole
                     AND u.username != 'guest'
                     $adminroles ";
    $order = "ORDER BY $orderby ";

    return(get_records_sql($select.$from.$where.$order));

}

/**
 * Parse a group name for characters to replace
 * @param string $format The format a group name will follow
 * @param int $groupnumber The number of the group to be used in the parsed format string
 * @return string the parsed format string
 */
function groups_parse_name($format, $groupnumber) {
    if (strstr($format, '@') !== false) { // Convert $groupnumber to a character series
        $letter = 'A';
        for($i=0; $i<$groupnumber; $i++) {
            $letter++;
        }
        $str = str_replace('@', $letter, $format);
    } else {
    	$str = str_replace('#', $groupnumber+1, $format);
    }
    return($str);
}

/**
 * Assigns group into grouping
 * @param int groupingid
 * @param int groupid
 * @return bool success
 */
function groups_assign_grouping($groupingid, $groupid) {
    if (record_exists('groupings_groups', 'groupingid', $groupingid, 'groupid', $groupid)) {
        return true;
    }
    $assign = new object();
    $assign->groupingid = $groupingid;
    $assign->groupid = $groupid;
    $assign->timeadded = time();
    return (bool)insert_record('groupings_groups', $assign);
}

/**
 * Unassigns group grom grouping
 * @param int groupingid
 * @param int groupid
 * @return bool success
 */
function groups_unassign_grouping($groupingid, $groupid) {
    return delete_records('groupings_groups', 'groupingid', $groupingid, 'groupid', $groupid);
}

/**
 * Lists users in a group based on their role on the course.
 * Returns false if there's an error or there are no users in the group. 
 * Otherwise returns an array of role ID => role data, where role data includes:
 * (role) $id, $shortname, $name
 * $users: array of objects for each user which include the specified fields
 * Users who do not have a role are stored in the returned array with key '-'
 * and pseudo-role details (including a name, 'No role'). Users with multiple
 * roles, same deal with key '*' and name 'Multiple roles'. You can find out
 * which roles each has by looking in the $roles array of the user object.
 * @param int $groupid
 * @param int $courseid Course ID (should match the group's course)
 * @param string $fields List of fields from user table prefixed with u, default 'u.*'
 * @param string $sort SQL ORDER BY clause, default 'u.lastname ASC'
 * @return array Complex array as described above
 */
function groups_get_members_by_role($groupid, $courseid, $fields='u.*', $sort='u.lastname ASC') {
    global $CFG;

    // Retrieve information about all users and their roles on the course or
    // parent ('related') contexts 
    $context=get_context_instance(CONTEXT_COURSE,$courseid);
    $rs=get_recordset_sql($crap="SELECT r.id AS roleid,r.shortname AS roleshortname,r.name AS rolename,
                                        u.id AS userid,$fields
                                  FROM {$CFG->prefix}groups_members gm
                            INNER JOIN {$CFG->prefix}user u ON u.id = gm.userid
                            INNER JOIN {$CFG->prefix}role_assignments ra 
                                       ON ra.userid = u.id 
                            INNER JOIN {$CFG->prefix}role r ON r.id = ra.roleid
                                 WHERE gm.groupid='$groupid'
                                   AND ra.contextid ".get_related_contexts_string($context)."
                              ORDER BY r.sortorder,$sort");

    return groups_calculate_role_people($rs,$context);
}

/**
 * Internal function used by groups_get_members_by_role to handle the
 * results of a database query that includes a list of users and possible
 * roles on a course.
 *
 * @param object $rs The record set (may be false)
 * @param object $context of course
 * @return array As described in groups_get_members_by_role 
 */
function groups_calculate_role_people($rs,$context) {
    global $CFG;
    if(!$rs) {
        return false;
    }
    
    $roles = get_records_menu('role', null, 'name', 'id, name');
    $aliasnames = role_fix_names($roles, $context);

    // Array of all involved roles
    $roles=array();
    // Array of all retrieved users
    $users=array();
    // Fill arrays
    while($rec=rs_fetch_next_record($rs)) {
        // Create information about user if this is a new one
        if(!array_key_exists($rec->userid,$users)) {
            // User data includes all the optional fields, but not any of the
            // stuff we added to get the role details
            $userdata=clone($rec);
            unset($userdata->roleid);
            unset($userdata->roleshortname);
            unset($userdata->rolename);
            unset($userdata->userid);
            $userdata->id=$rec->userid;

            // Make an array to hold the list of roles for this user
            $userdata->roles=array();
            $users[$rec->userid]=$userdata;
        }
        // If user has a role...
        if(!is_null($rec->roleid)) {
            // Create information about role if this is a new one
            if(!array_key_exists($rec->roleid,$roles)) {
                $roledata=new StdClass;
                $roledata->id=$rec->roleid;
                $roledata->shortname=$rec->roleshortname;
                if(array_key_exists($rec->roleid,$aliasnames)) {
                    $roledata->name=$aliasnames[$rec->roleid];
                } else {
                    $roledata->name=$rec->rolename;
                }
                $roledata->users=array();
                $roles[$roledata->id]=$roledata;
            }
            // Record that user has role
            $users[$rec->userid]->roles[] = $roles[$rec->roleid];
        }
    }
    rs_close($rs);

    // Return false if there weren't any users
    if(count($users)==0) {
        return false;
    }

    // Add pseudo-role for multiple roles
    $roledata=new StdClass;
    $roledata->name=get_string('multipleroles','role');
    $roledata->users=array();
    $roles['*']=$roledata;

    // Now we rearrange the data to store users by role
    foreach($users as $userid=>$userdata) {
        $rolecount=count($userdata->roles);
        if($rolecount==0) {
            debugging("Unexpected: user $userid is missing roles");
        } else if($rolecount>1) {
            $roleid='*';
        } else {
            $roleid=$userdata->roles[0]->id;
        }
        $roles[$roleid]->users[$userid]=$userdata;
    }

    // Delete roles not used
    foreach($roles as $key=>$roledata) {
        if(count($roledata->users)===0) {
            unset($roles[$key]);
        }
    }

    // Return list of roles containing their users
    return $roles;
}

?>
