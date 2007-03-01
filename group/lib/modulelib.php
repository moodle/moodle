<?php
/**
 * modulelib.php
 * 
 * This file contains functions to be used by modules to support groups. More
 * documentation is available on the Developer's Notes section of the Moodle 
 * wiki. 
 * 
 * For queries, suggestions for improvements etc. please post on the Groups 
 * forum on the moodle.org site.
 *
 * @copyright &copy; 2006 The Open University
 * @author J.White AT open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package groups
 */ 

/*
 * (OLD) Permission types
 * 
 * There are six types of permission that a user can hold for a particular
 * group - 'student view', 'student contribute', 'teacher view', 
 * 'teacher contribute', 'view members list' and 'view group existence'. 
 * 
 * A particular user need not to be a member of the group to have a specific 
 * permission and may have more than one permission type. The permission that a 
 * particular user has for a group used by an particular instance of the module 
 * depends on whether the student is a teacher or student for the course and on 
 * the settings for the set of groups (the 'grouping') being used by the 
 * instance of the module  
 * 
 * It is up to each module to decide how to interpret the different permission 
 * types. The only exception is with 'view members list' and 'view group 
 * existence'. The former means that the user can view the members of the group 
 * while the latter means that the user can view information such as the group 
 * name and description. It is possible that a user may have 'view members list' 
 * permission without 'view group existence' permission - the members would just 
 * appear as the other users on the course. 
 * 
 * Permission types can be combined with boolean expressions where they are used 
 * if necessary. 
 * 
 * @name GROUPS_STUDENT Either 'student view' or 'student contribute' permission
 * @name GROUPS_TEACHER Either 'teacher view' or 'teacher contribute' permission
 * @name GROUPS_VIEW Either 'teacher view' or 'student view' permission
 * @name GROUPS_CONTRIBUTE Either 'teacher contribute' or 'student contribute' 
 * permission
 * @name GROUPS_VIEW_GROUP_EXISTENCE 'view group existence' permission
 * @name GROUPS_VIEW_MEMBERS_LIST 'view members list' permission
 * @name GROUPS_STUDENT_VIEW 'student view' permission
 * @name GROUPS_STUDENT_CONTRIBUTE 'student contribute' permission
 * @name GROUPS_TEACHER_VIEW 'teacher view' permission
 * @name GROUPS_TEACHER_CONTRIBUTE 'teacher contribute' permission
 */
define('GROUPS_STUDENT', 1);
define('GROUPS_TEACHER', 2);
define('GROUPS_VIEW', 4);
define('GROUPS_CONTRIBUTE', 8);
define('GROUPS_VIEW_GROUP_EXISTENCE', 16);
define('GROUPS_VIEW_MEMBERS_LIST', 48);
define('GROUPS_STUDENT_VIEW', 5);
define('GROUPS_STUDENT_CONTRIBUTE', 9);
define('GROUPS_TEACHER_VIEW', 6);
define('GROUPS_TEACHER_CONTRIBUTE', 10);

/**
 * Indicates if the instance of the module has been set up by an editor of the 
 * course to use groups. This functionality can also be obtained using the 
 * groups_m_get_groups() function, however it is sufficiently commonly needed 
 * that this separate function has been provided and should be used instead. 
 * @param int $cmid The id of the module instance
 * @return boolean True if the instance is set up to use groups, false otherwise
 */
function groups_m_uses_groups($cmid) {
	$usesgroups = false;
	$groupingid = groups_db_get_groupingid($cmid);
	if (!$groupingid) {
		$usesgroups = true;
	}
	
	return $usesgroups;
}

/**
 * Prints a dropdown box to enable a user to select between the groups for the 
 * module instance of which they are a member. If a user belongs to 0 or 1 
 * groups, no form is printed. The dropdown box belongs to a form and when a 
 * user clicks on the box this form is automatically submitted so that the page 
 * knows about the change. 
 * @param int $cmid The id of the module instance
 * @param string $urlroot The url of the page - this is necessary so the form 
 * can submit to the correct page. 
 * @param int $permissiontype - see note on permissiontypes above. 
 * @return boolean True unless an error occurred or the module instance does not 
 * use groups in which case returns false. 
 */
function groups_m_print_group_selector($cmid, $urlroot, $permissiontype) {
	// Get the groups for the cmid
	// Produce an array to put into the $groupsmenu array. 
	// Add an all option if necessary. 
	$groupids = groups_module_get_groups_for_current_user($cmid, $permissiontype);
	
	// Need a line to check if current group selected. 
	if ($groupids) {
			$currentgroup = groups_module_get_current_group($cmid);
			if ($allgroupsoption) {
				$groupsmenu[0] = get_string('allparticipants');
			}
		
		foreach ($groupids as $groupid) {
			$groupsmenu[$groupid] = groups_get_group_name($groupid);
			popup_form($urlroot.'&amp;group=', $groupsmenu, 'selectgroup', 
			$currentgroup, '', '', '', false, 'self');
		}
	}	
}

/**
 * Gets the group that a student has selected from the drop-down menu printed
 * by groups_m_print_group_selector and checks that the student has the 
 * specified permission for the group and that the group is one of the groups
 * assigned for this module instance.
 * 
 * Groups selected are saved between page changes within the module instance but 
 * not necessarily if the user leaves the instance e.g. returns to the main 
 * course page. If the selector has not been printed anywhere during the user's 
 * 'visit' to the module instance, then the function returns false. This means 
 * that you need to be particularly careful about pages that might be 
 * bookmarked by the user.  
 * 
 * @uses $USER   
 * @param int $cmid The id of the module instance
 * @param int $permissiontype The permission type - see note on permission types 
 * above
 * @param int $userid The id of the user, defaults to the current user 
 * @return boolean True if no error occurred, false otherwise.
 * 
 * TO DO - make this and other functions default to current user  
 */
function groups_m_get_selected_group($cmid, $permissiontype, $userid) {
	$currentgroup = optional_param('group');
	if (!$currentgroup) {
		$groupids = groups_get_groups_for_user();
	}
	// Get it from the session variable, otherwise get it from the form, otherwise
	// Get it from the database as the first group. 
	// Then set the  group in the session variable to make it easier to get next time. 	
}
	 
/**
 * Gets an array of the group IDs of all groups for the user in this course module.
 * @uses $USER     
 * @param object $cm The course-module object.
 * @param int $userid The ID of the user.
 * @return array An array of group IDs, or false. 
 */
function groups_m_get_groups_for_user($cm, $userid) {
//echo 'User'; print_object($cm);
    $groupingid = groups_get_grouping_for_coursemodule($cm);
    if (!$groupingid || GROUP_NOT_IN_GROUPING == $groupingid) { //TODO: check.
        return false;
    }
    if (!isset($cm->course) || !groupmode($cm->course, $cm)) {
        return false;
    }
    elseif (GROUP_ANY_GROUPING == $groupingid) {
        return groups_get_groups_for_user($userid, $cm->course);
    }
    return groups_get_groups_for_user_in_grouping($userid, $groupingid);
} 


/**
 * Get the ID of the first group for the global $USER in the course module.
 * Replaces legacylib function 'mygroupid'.
 * @uses $USER
 * @param $cm A course module object.
 * @return int A single group ID for this user.
 */ 
function groups_m_get_my_group($cm) {
    global $USER;
    $groupids = groups_m_get_groups_for_user($cm, $USER->id);
    if (!$groupids || count($groupids) == 0) {
        return 0;
    }
    return array_shift($groupids);
}


/**
 * Indicates if a specified user has a particular type of permission for a 
 * particular group for this module instance.
 * @uses $USER      
 * @param int $cmid The id of the module instance. This is necessary because the 
 * same group can be used in different module instances with different 
 * permission setups. 
 * @param int $groupid The id of the group
 * @param int $permissiontype The permission type - see note on permission types 
 * above
 * @userid int $userid The id of the user, defaults to the current user
 * @return boolean True if the user has the specified permission type, false 
 * otherwise or if an error occurred. 
 */
 function groups_m_has_permission($cm, $groupid, $permissiontype, $userid = null) {
    if (!$userid) {
        global $USER;
        $userid = $USER->id;
    }
	$groupingid = groups_get_grouping_for_coursemodule($cm);
	if (!$groupingid || !is_object($cm) || !isset($cm->course)) {
        return false;
    }
    $courseid = $cm->course;
    $isstudent = isstudent($courseid, $userid);
	$isteacher = isteacher($courseid, $userid);
	$groupmember = groups_is_member($groupid, $userid);
	$memberofsomegroup = groups_is_member_of_some_group_in_grouping($userid, $groupingid);
	
	$groupingsettings = groups_get_grouping_settings($groupingid);
	$viewowngroup = $groupingsettings->viewowngroup;
	$viewallgroupsmembers = $groupingsettings->viewallgroupmembers;
	$viewallgroupsactivities = $groupingsettings->viewallgroupsactivities;
	$teachersgroupsmark = $groupingsettings->teachersgroupsmark;
	$teachersgroupsview = $groupingsettings->teachersgroupsview;
	$teachersgroupmark = $groupingsettings->teachersgroupmark;
	$teachersgroupview = $groupingsettings->teachersgroupview;
	$teachersoverride = $groupingsettings->teachersoverride;
		
	$permission = false;
	
	switch ($permissiontype) {
		case 'view':
			if (($isstudent and $groupmember) or 
			    ($isteacher and $groupmember) or 
			    ($isstudent and $viewallgroupsactivities) or 
			    ($isteacher and !$teachersgroupview) or 
			    ($isteacher and !$memberofsomegroup and $teachersoverride)) {
				$permission = true;
			} 
			break;
			
		case 'studentcontribute':
			if (($isstudent and $groupmember) or 
			    ($isteacher and $groupmember) or 
			    ($isteacher and !$memberofsomegroup and $teachersoverride)) {
				$permission = true;
			} 
			break;
		case 'teachermark':
			if (($isteacher and $groupmember) or 
				($isteacher and !$teachersgroupmark) or
			    ($isteacher and !$memberofsomegroup and $teachersoverride)) {
				$permission = true;
			}  
			break;
		
		case 'viewmembers':	
			if (($isstudent and $groupmember and $viewowngroup) or 
			    ($isstudent and $viewallgroupsmembers) or 
				($isteacher and $groupmember) or 
			    ($isteacher and !$teachersgroupview) or 
			    ($isteacher and !$memberofsomegroup and $teachersoverride) or 
			    $isteacheredit) {
				$permission = true;
			}  
			break;
	}
	return $permission;	
}

/**
 * Gets an array of members of a group that have a particular permission type 
 * for this instance of the module and that are enrolled on the course that
 * the module instance belongs to. 
 * 
 * @param int $cmid The id of the module instance. This is necessary because the 
 * same group can be used in different module instances with different 
 * permission setups. 
 * @param int $groupid The id of the group
 * @param int $permissiontype The permission type - see note on permission types 
 * above
 * @return array An array containing the ids of the users with the specified 
 * permission. 
 */
function groups_m_get_members_with_permission($cmid, $groupid, 
                                              $permissiontype) {
	// Get all the users as $userid
	$validuserids = array();	
	foreach($validuserids as $userid) {
		$haspermission = groups_m_has_permission($cmid, $groupid, 
										$permissiontype, $userid);
		if ($haspermission) {
			array_push($validuserids, $userid);
		}
	}
	return $validuserids;
}

/**
 * Gets the group object associated with a group id. This group object can be 
 * used to get information such as the name of the group and the file for the 
 * group icon if it exists. (Look at the groups table in the database to see
 * the fields). 
 * @param int $groupid The id of the group
 * @return group The group object 
 */
function groups_m_get_group($groupid) {
	return groups_db_m_get_group($groupid);
}

/**
 * Gets the groups for the module instance. In general, you should use 
 * groups_m_get_groups_for_user, however this function is provided for 
 * circumstances where this function isn't sufficient for some reason. 
 * @param int $cmid The id of the module instance. 
 * @return array An array of the ids of the groups for the module instance 
 */
function groups_m_get_groups($cmid) {
	$groupingid = groups_db_get_groupingid($cmid);
	$groupids = groups_get_groups_in_grouping($groupingid);
	return $groupids;	
}

/**
 * Gets the members of group that are enrolled on the course that the specified
 * module instance belongs to. 
 * @param int $cmid The id of the module instance
 * @param int $groupid The id of the group
 * @return array An array of the userids of the members. 
 */
function groups_m_get_members($cmid, $groupid) {
	$userids = groups_get_members($groupid, $membertype);
	if (!$userids) {
		$memberids = false;
	} else {
		// Check if each user is enrolled on the course @@@ TO DO 
	}
    return $memberids;
}

/**
 * Stores a current group in the user's session, if not already present.
 * 
 * Current group applies to all modules in the current course that share 
 * a grouping (or use no grouping).
 * 
 * This function allows the user to change group if they want, but it
 * checks they have permissions to access the new group and calls error()
 * otherwise.
 * @param object $cm Course-module object
 * @param int $groupmode Group mode
 * @param int $changegroup If specified, user wants to change to this group
 * @return Group ID
 */
function groups_m_get_and_set_current($cm, $groupmode, $changegroup=-1) {
    // Check group mode is turned on
    if (!$groupmode) {
        return false;
    }

    // Get current group and return it if no change requested
    $currentgroupid = groups_m_get_current($cm);
    if ($changegroup<0) {
        return $currentgroupid;
    }

    // Check 'all groups' access
    $context = get_context_instance(CONTEXT_COURSE, $cm->course);
    $allgroups = has_capability('moodle/site:accessallgroups', $context);

    // 0 is a special case for 'all groups'.
    if ($changegroup==0) {
        if ($groupmode!=VISIBLEGROUPS && !$allgroups) {
            error('You do not have access to view all groups');
        }
    } else { // Normal group specified
        // Check group is in the course...
        if (!groups_group_belongs_to_course($changegroup, $cm->course)) {
            error('Requested group is not in this course.');
        }
        // ...AND in the right grouping if required...
        if ($cm->groupingid && !groups_belongs_to_grouping($changegroup,$cm->groupingid)) {
            print_object($cm);
            print_object(groups_get_group($changegroup));
            error('Requested group is not in this grouping.');
        }
        // ...AND user has access to all groups, or it's in visible groups mode, or 
        // user is a member.
        if (!$allgroups &&
          $groupmode != VISIBLEGROUPS && !groups_is_member($changegroup)) {
        }
    }
    // OK, now remember this group in session
    global $SESSION;
    $SESSION->currentgroupinggroup[$cm->course][$cm->groupingid] = $changegroup;
    return $changegroup;
}

/**
 * Obtains the current group (see groups_m_get_and_set_current) either as an ID or object.
 * @param object $cm Course-module object
 * @param bool $full If true, returns group object rather than ID
 * @return mixed Group ID (default) or object
 */
function groups_m_get_current($cm, $full=false) {
    global $SESSION;
    if (isset($SESSION->currentgroupinggroup[$cm->course][$cm->groupingid])) {
        $currentgroup = $SESSION->currentgroupinggroup[$cm->course][$cm->groupingid];
    } else {
        global $USER;
        if ($cm->groupingid) {
            $mygroupids = groups_get_groups_for_user_in_grouping($USER->id, $cm->groupingid);
        } else {
            $mygroupids = groups_get_groups_for_user($USER->id, $cm->course);
        }
        if (!$mygroupids) {
            return false;
        }
        $currentgroup = array_shift($mygroupids);
        $SESSION->currentgroupinggroup[$cm->course][$cm->groupingid] = $currentgroup;
    }
    if ($full) {
        return groups_groupid_to_group($currentgroup);
    } else {
        return $currentgroup;
    }
}

?>