<?php // $Id$

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.org                                            //
//                                                                       //
// Copyright (C) 1999-2999  Martin Dougiamas, Moodle  http://moodle.com  //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

/**
 * deprecatedlib.php - Old functions retained only for backward compatibility
 *
 * Old functions retained only for backward compatibility.  New code should not
 * use any of these functions.
 *
 * @author Martin Dougiamas
 * @version $Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package moodlecore
 */




/**
 * Ensure that a variable is set
 *
 * If $var is undefined throw an error, otherwise return $var.
 *
 * @param mixed $var the variable which may be unset
 * @param mixed $default the value to return if $var is unset
 */
function require_variable($var) {
    global $CFG;
    if (!empty($CFG->disableglobalshack)) {
        error( 'The require_variable() function is deprecated.' );
    }
    if (! isset($var)) {
        error('A required parameter was missing');
    }
}

/**
 * Ensure that a variable is set
 *
 * If $var is undefined set it (by reference), otherwise return $var.
 *
 * @param mixed $var the variable which may be unset
 * @param mixed $default the value to return if $var is unset
 */
function optional_variable(&$var, $default=0) {
    global $CFG;
    if (!empty($CFG->disableglobalshack)) {
        error( "The optional_variable() function is deprecated ($var, $default)." );
    }
    if (! isset($var)) {
        $var = $default;
    }
}

/**
 * Determines if a user an admin
 *
 * @uses $USER
 * @param int $userid The id of the user as is found in the 'user' table
 * @staticvar array $admins List of users who have been found to be admins by user id
 * @staticvar array $nonadmins List of users who have been found not to be admins by user id
 * @return bool
 */
function isadmin($userid=0) {
    global $USER, $CFG;

    if (empty($CFG->rolesactive)) {    // Then the user is likely to be upgrading NOW
        if (!$userid) {
            if (empty($USER->id)) {
                return false;
            }
            if (!empty($USER->admin)) {
                return true;
            }
            $userid = $USER->id;
        }

        return record_exists('user_admins', 'userid', $userid);
    }

    $context = get_context_instance(CONTEXT_SYSTEM, SITEID);  
    
    return has_capability('moodle/legacy:admin', $context, $userid, false);
}

/**
 * Determines if a user is a teacher (or better)
 *
 * @uses $CFG
 * @param int $courseid The id of the course that is being viewed, if any
 * @param int $userid The id of the user that is being tested against. Set this to 0 if you would just like to test against the currently logged in user.
 * @param bool $obsolete_includeadmin Not used any more
 * @return bool
 */

function isteacher($courseid=0, $userid=0, $obsolete_includeadmin=true) {
/// Is the user able to access this course as a teacher?
    global $CFG;

    if (empty($CFG->rolesactive)) {     // Teachers are locked out during an upgrade to 1.7
        return false;
    }

    if ($courseid) {
        $context = get_context_instance(CONTEXT_COURSE, $courseid);
    } else {
        $context = get_context_instance(CONTEXT_SYSTEM, SITEID);  
    }

    return (has_capability('moodle/legacy:teacher', $context, $userid, false)
         or has_capability('moodle/legacy:editingteacher', $context, $userid, false)
         or has_capability('moodle/legacy:admin', $context, $userid, false));
}

/**
 * Determines if a user is a teacher in any course, or an admin
 *
 * @uses $USER
 * @param int $userid The id of the user that is being tested against. Set this to 0 if you would just like to test against the currently logged in user.
 * @param bool $includeadmin Include anyone wo is an admin as well
 * @return bool
 */
function isteacherinanycourse($userid=0, $includeadmin=true) {
    global $USER, $CFG;

    if (empty($CFG->rolesactive)) {     // Teachers are locked out during an upgrade to 1.7
        return false;
    }

    if (!$userid) {
        if (empty($USER->id)) {
            return false;
        }
        $userid = $USER->id;
    }

    if (!record_exists('role_assignments', 'userid', $userid)) {    // Has no roles anywhere
        return false;
    }

/// If this user is assigned as an editing teacher anywhere then return true
    if ($roles = get_roles_with_capability('moodle/legacy:editingteacher', CAP_ALLOW)) {
        foreach ($roles as $role) {
            if (record_exists('role_assignments', 'roleid', $role->id, 'userid', $userid)) {
                return true;
            }
        }
    }

/// If this user is assigned as a non-editing teacher anywhere then return true
    if ($roles = get_roles_with_capability('moodle/legacy:teacher', CAP_ALLOW)) {
        foreach ($roles as $role) {
            if (record_exists('role_assignments', 'roleid', $role->id, 'userid', $userid)) {
                return true;
            }
        }
    }

/// Include admins if required
    if ($includeadmin) {
        $context = get_context_instance(CONTEXT_SYSTEM, SITEID);  
        if (has_capability('moodle/legacy:admin', $context, $userid, false)) {
            return true;
        }
    }

    return false;
}

/**
 * Determines if a user is allowed to edit a given course
 *
 * @param int $courseid The id of the course that is being edited
 * @param int $userid The id of the user that is being tested against. Set this to 0 if you would just like to test against the currently logged in user.
 * @return bool
 */
function isteacheredit($courseid, $userid=0, $obsolete_ignorestudentview=false) {
    global $CFG;

    if (empty($CFG->rolesactive)) {
        return false;
    }

    if (empty($courseid)) {
        $context = get_context_instance(CONTEXT_SYSTEM, SITEID);  
    } else {
        $context = get_context_instance(CONTEXT_COURSE, $courseid);
    }

    return (has_capability('moodle/legacy:editingteacher', $context, $userid, false)
         or has_capability('moodle/legacy:admin', $context, $userid, false));
}

/**
 * Determines if a user can create new courses
 *
 * @param int $userid The user being tested. You can set this to 0 or leave it blank to test the currently logged in user.
 * @return bool
 */
function iscreator ($userid=0) {
    global $CFG;

    if (empty($CFG->rolesactive)) {
        return false;
    }

    $context = get_context_instance(CONTEXT_SYSTEM, SITEID);  

    return (has_capability('moodle/legacy:coursecreator', $context, $userid, false)
         or has_capability('moodle/legacy:admin', $context, $userid, false));
}

/**
 * Determines if a user is a student in the specified course
 *
 * If the course id specifies the site then this determines
 * if the user is a confirmed and valid user of this site.
 *
 * @uses $CFG
 * @uses SITEID
 * @param int $courseid The id of the course being tested
 * @param int $userid The user being tested. You can set this to 0 or leave it blank to test the currently logged in user.
 * @return bool
 */
function isstudent($courseid=0, $userid=0) {
    global $CFG;

    if (empty($CFG->rolesactive)) {
        return false;
    }

    if ($courseid == 0) {
        $context = get_context_instance(CONTEXT_SYSTEM, SITEID);  
    } else {
        $context = get_context_instance(CONTEXT_COURSE, $courseid);
    }

    return has_capability('moodle/legacy:student', $context, $userid, false);
}

/**
 * Determines if the specified user is logged in as guest.
 *
 * @param int $userid The user being tested. You can set this to 0 or leave it blank to test the currently logged in user.
 * @return bool
 */
function isguest($userid=0) {
    global $CFG;

    if (empty($CFG->rolesactive)) {
        return false;
    }

    $context = get_context_instance(CONTEXT_SYSTEM, SITEID);  

    return has_capability('moodle/legacy:guest', $context, $userid, false);
}

/**
 * Enrols (or re-enrols) a student in a given course
 *
 * NOTE: Defaults to 'manual' enrolment - enrolment plugins
 * must set it explicitly.
 *
 * @uses $CFG
 * @param int $userid The id of the user that is being tested against. Set this to 0 if you would just like to test against the currently logged in user.
 * @param int $courseid The id of the course that is being viewed
 * @param int $timestart ?
 * @param int $timeend ?
 * @param string $enrol ?
 * @return bool
 */
function enrol_student($userid, $courseid, $timestart=0, $timeend=0, $enrol='manual') {

    global $CFG;

    if (!$user = get_record('user', 'id', $userid)) {        // Check user
        return false;
    }

    if (!$roles = get_roles_with_capability('moodle/legacy:student', CAP_ALLOW)) {
        return false;
    }

    $role = array_shift($roles);      // We can only use one, let's use the first one

    if (!$context = get_context_instance(CONTEXT_COURSE, $courseid)) {
        return false;
    }

    return role_assign($role->id, $user->id, 0, $context->id, $timestart, $timeend, 0, $enrol);
}

/**
 * Unenrols a student from a given course
 *
 * @param int $courseid The id of the course that is being viewed, if any
 * @param int $userid The id of the user that is being tested against.
 * @return bool
 */
function unenrol_student($userid, $courseid=0) {
    global $CFG;
    
    $status = true;

    if ($courseid) {
        /// First delete any crucial stuff that might still send mail
        if ($forums = get_records('forum', 'course', $courseid)) {
            foreach ($forums as $forum) {
                delete_records('forum_subscriptions', 'forum', $forum->id, 'userid', $userid);
            }
        }
        /// remove from all legacy student roles
        if ($courseid == SITEID) {
            $context = get_context_instance(CONTEXT_SYSTEM, SITEID);
        } else if (!$context = get_context_instance(CONTEXT_COURSE, $courseid)) {
            return false;
        }
        if (!$roles = get_roles_with_capability('moodle/legacy:student', CAP_ALLOW)) {
            return false;
        }
        foreach($roles as $role) {
            $status = role_unassign($role->id, $userid, 0, $context->id) and $status;
        }
    } else {
        // recursivelly unenroll student from all courses
        if ($courses = get_records('course')) {
            foreach($courses as $course) {
                $status = unenrol_student($userid, $course->id) and $status;
            }
        }
    }

    return $status;
}

/**
 * Add a teacher to a given course  
 *
 * @param int $userid The id of the user that is being tested against. Set this to 0 if you would just like to test against the currently logged in user.
 * @param int $courseid The id of the course that is being viewed, if any
 * @param int $editall Can edit the course
 * @param string $role Obsolete
 * @param int $timestart The time they start
 * @param int $timeend The time they end in this role
 * @param string $enrol The type of enrolment this is
 * @return bool
 */
function add_teacher($userid, $courseid, $editall=1, $role='', $timestart=0, $timeend=0, $enrol='manual') {
    global $CFG;

    if (!$user = get_record('user', 'id', $userid)) {        // Check user
        return false;
    }

    $capability = $editall ? 'moodle/legacy:editingteacher' : 'moodle/legacy:teacher';

    if (!$roles = get_roles_with_capability($capability, CAP_ALLOW)) {
        return false;
    }

    $role = array_shift($roles);      // We can only use one, let's use the first one

    if (!$context = get_context_instance(CONTEXT_COURSE, $courseid)) {
        return false;
    }

    return role_assign($role->id, $user->id, 0, $context->id, $timestart, $timeend, 0, $enrol);
}

/**
 * Removes a teacher from a given course (or ALL courses)
 * Does not delete the user account
 *
 * @param int $courseid The id of the course that is being viewed, if any
 * @param int $userid The id of the user that is being tested against.
 * @return bool
 */
function remove_teacher($userid, $courseid=0) {
    global $CFG;

    $roles = get_roles_with_capability('moodle/legacy:editingteacher', CAP_ALLOW);

    if ($roles) {
        $roles += get_roles_with_capability('moodle/legacy:teacher', CAP_ALLOW);
    } else {
        $roles = get_roles_with_capability('moodle/legacy:teacher', CAP_ALLOW);
    }

    if (empty($roles)) {
        return true;
    }

    $return = true;

    if ($courseid) {

        if (!$context = get_context_instance(CONTEXT_COURSE, $courseid)) {
            return false;
        }

        /// First delete any crucial stuff that might still send mail
        if ($forums = get_records('forum', 'course', $courseid)) {
            foreach ($forums as $forum) {
                delete_records('forum_subscriptions', 'forum', $forum->id, 'userid', $userid);
            }
        }

        /// No need to remove from groups now

        foreach ($roles as $role) {    // Unassign them from all the teacher roles
            $newreturn = role_unassign($role->id, $userid, 0, $context->id);
            if (empty($newreturn)) {
                $return = false;
            }
        }

    } else {
        delete_records('forum_subscriptions', 'userid', $userid);
        $return = true;
        foreach ($roles as $role) {    // Unassign them from all the teacher roles
            $newreturn = role_unassign($role->id, $userid, 0, 0);
            if (empty($newreturn)) {
                $return = false;
            }
        }
    }

    return $return;
}

/**
 * Add an admin to a site
 *
 * @uses SITEID
 * @param int $userid The id of the user that is being tested against.
 * @return bool
 * @TODO: remove from cvs
 */
function add_admin($userid) {
    return true;
}

function get_user_info_from_db($field, $value) {  // For backward compatibility
    return get_complete_user_data($field, $value);
}


/**
 * Get the guest user information from the database
 *
 * @return object(user) An associative array with the details of the guest user account.
 * @todo Is object(user) a correct return type? Or is array the proper return type with a note that the contents include all details for a user.
 */
function get_guest() {
    return get_complete_user_data('username', 'guest');
}

/**
 * Returns $user object of the main teacher for a course
 *
 * @uses $CFG
 * @param int $courseid The course in question.
 * @return user|false  A {@link $USER} record of the main teacher for the specified course or false if error.
 * @todo Finish documenting this function
 */
function get_teacher($courseid) {

    global $CFG;

    $context = get_context_instance(CONTEXT_COURSE, $courseid);

    if ($users = get_users_by_capability($context, 'moodle/course:update', 'u.*,ra.hidden', 'r.sortorder ASC',
                                         '', '', '', '', false)) {
        foreach ($users as $user) {
            if (!$user->hidden || has_capability('moodle/role:viewhiddenassigns', $context)) {
                return $user;
            }
        }
    }

    return false;
}

/**
 * Searches logs to find all enrolments since a certain date
 *
 * used to print recent activity
 *
 * @uses $CFG
 * @param int $courseid The course in question.
 * @return object|false  {@link $USER} records or false if error.
 * @todo Finish documenting this function
 */
function get_recent_enrolments($courseid, $timestart) {

    global $CFG;
    
    $context = get_context_instance(CONTEXT_COURSE, $courseid);

    return get_records_sql("SELECT DISTINCT u.id, u.firstname, u.lastname, l.time
                            FROM {$CFG->prefix}user u,
                                 {$CFG->prefix}role_assignments ra,
                                 {$CFG->prefix}log l
                            WHERE l.time > '$timestart'
                              AND l.course = '$courseid'
                              AND l.module = 'course'
                              AND l.action = 'enrol'
                              AND l.info = u.id
                              AND u.id = ra.userid
                              AND ra.contextid ".get_related_contexts_string($context)."
                              ORDER BY l.time ASC");
}

/**
 * Returns array of userinfo of all students in this course
 * or on this site if courseid is id of site
 *
 * @uses $CFG
 * @uses SITEID
 * @param int $courseid The course in question.
 * @param string $sort ?
 * @param string $dir ?
 * @param int $page ?
 * @param int $recordsperpage ?
 * @param string $firstinitial ?
 * @param string $lastinitial ?
 * @param ? $group ?
 * @param string $search ?
 * @param string $fields A comma separated list of fields to be returned from the chosen table.
 * @param string $exceptions ?
 * @return object
 * @todo Finish documenting this function
 */
function get_course_students($courseid, $sort='ul.timeaccess', $dir='', $page='', $recordsperpage='',
                             $firstinitial='', $lastinitial='', $group=NULL, $search='', $fields='', $exceptions='') {

    global $CFG;

    if ($courseid == SITEID and $CFG->allusersaresitestudents) {
        // return users with confirmed, undeleted accounts who are not site teachers
        // the following is a mess because of different conventions in the different user functions
        $sort = str_replace('s.timeaccess', 'lastaccess', $sort); // site users can't be sorted by timeaccess
        $sort = str_replace('timeaccess', 'lastaccess', $sort); // site users can't be sorted by timeaccess
        $sort = str_replace('u.', '', $sort); // the get_user function doesn't use the u. prefix to fields
        $fields = str_replace('u.', '', $fields);
        if ($sort) {
            $sort = $sort .' '. $dir;
        }
        // Now we have to make sure site teachers are excluded

        if ($teachers = get_course_teachers(SITEID)) {
            foreach ($teachers as $teacher) {
                $exceptions .= ','. $teacher->userid;
            }
            $exceptions = ltrim($exceptions, ','); 
          
        }        

        return get_users(true, $search, true, $exceptions, $sort, $firstinitial, $lastinitial,
                          $page, $recordsperpage, $fields ? $fields : '*');
    }

    $LIKE      = sql_ilike();
    $fullname  = sql_fullname('u.firstname','u.lastname');

    $groupmembers = '';

    // make sure it works on the site course
    $context = get_context_instance(CONTEXT_COURSE, $courseid);

    $select = "c.contextlevel=".CONTEXT_COURSE." AND "; // Must be on a course
    if ($courseid != SITEID) {
        // If not site, require specific course
        $select.= "c.instanceid=$courseid AND ";
    }
    $select.="rc.capability='moodle/legacy:student' AND rc.permission=".CAP_ALLOW." AND "; 

    $select .= ' u.deleted = \'0\' ';

    if (!$fields) {
        $fields = 'u.id, u.confirmed, u.username, u.firstname, u.lastname, '.
                  'u.maildisplay, u.mailformat, u.maildigest, u.email, u.city, '.
                  'u.country, u.picture, u.idnumber, u.department, u.institution, '.
                  'u.emailstop, u.lang, u.timezone, ul.timeaccess as lastaccess';
    }

    if ($search) {
        $search = ' AND ('. $fullname .' '. $LIKE .'\'%'. $search .'%\' OR email '. $LIKE .'\'%'. $search .'%\') ';
    }

    if ($firstinitial) {
        $select .= ' AND u.firstname '. $LIKE .'\''. $firstinitial .'%\' ';
    }

    if ($lastinitial) {
        $select .= ' AND u.lastname '. $LIKE .'\''. $lastinitial .'%\' ';
    }

    if ($group === 0) {   /// Need something here to get all students not in a group
        return array();

    } else if ($group !== NULL) {
        $groupmembers = "INNER JOIN {$CFG->prefix}groups_members gm on u.id=gm.userid";
        $select .= ' AND gm.groupid = \''. $group .'\'';
    }

    if (!empty($exceptions)) {
        $select .= ' AND u.id NOT IN ('. $exceptions .')';
    }

    if ($sort) {
        $sort = ' ORDER BY '. $sort .' ';
    }

    $students = get_records_sql("SELECT $fields
                                FROM {$CFG->prefix}user u INNER JOIN
                                     {$CFG->prefix}role_assignments ra on u.id=ra.userid INNER JOIN
                                     {$CFG->prefix}role_capabilities rc ON ra.roleid=rc.roleid INNER JOIN
                                     {$CFG->prefix}context c ON c.id=ra.contextid LEFT OUTER JOIN
                                     {$CFG->prefix}user_lastaccess ul on ul.userid=ra.userid
                                     $groupmembers
                                WHERE $select $search $sort $dir", $page, $recordsperpage); 

    return $students;
}

/**
 * Counts the students in a given course (or site), or a subset of them
 *
 * @param object $course The course in question as a course object.
 * @param string $search ?
 * @param string $firstinitial ? 
 * @param string $lastinitial ?
 * @param ? $group ?
 * @param string $exceptions ? 
 * @return int
 * @todo Finish documenting this function
 */
function count_course_students($course, $search='', $firstinitial='', $lastinitial='', $group=NULL, $exceptions='') {

    if ($students = get_course_students($course->id, '', '', 0, 999999, $firstinitial, $lastinitial, $group, $search, '', $exceptions)) {
        return count($students);
    }
    return 0;
}

/**
 * Returns list of all teachers in this course
 *
 * If $courseid matches the site id then this function
 * returns a list of all teachers for the site.
 *
 * @uses $CFG
 * @param int $courseid The course in question.
 * @param string $sort ?
 * @param string $exceptions ? 
 * @return object
 * @todo Finish documenting this function
 */
function get_course_teachers($courseid, $sort='t.authority ASC', $exceptions='') {

    global $CFG;
    
    $sort = 'ul.timeaccess DESC';
    
    $context = get_context_instance(CONTEXT_COURSE, $courseid);
    return get_users_by_capability($context, 'moodle/course:update', 'u.*, ul.timeaccess as lastaccess, ra.hidden', $sort, '','','',$exceptions, false);
    /// some fields will be missing, like authority, editall
    /*
    return get_records_sql("SELECT u.id, u.username, u.firstname, u.lastname, u.maildisplay, u.mailformat, u.maildigest,
                                   u.email, u.city, u.country, u.lastlogin, u.picture, u.lang, u.timezone,
                                   u.emailstop, t.authority,t.role,t.editall,t.timeaccess as lastaccess
                            FROM {$CFG->prefix}user u,
                                 {$CFG->prefix}user_teachers t
                            WHERE t.course = '$courseid' AND t.userid = u.id
                              AND u.deleted = '0' AND u.confirmed = '1' $exceptions $sort");
    */
}

/**
 * Returns all the users of a course: students and teachers
 *
 * @param int $courseid The course in question.
 * @param string $sort ?
 * @param string $exceptions ?
 * @param string $fields A comma separated list of fields to be returned from the chosen table.
 * @return object
 * @todo Finish documenting this function
 */
function get_course_users($courseid, $sort='ul.timeaccess DESC', $exceptions='', $fields='') {

    $context = get_context_instance(CONTEXT_COURSE, $courseid);
    return get_users_by_capability($context, 'moodle/course:view', 'u.*, ul.timeaccess as lastaccess', $sort, '','','',$exceptions, false);

}

/**
 * Returns an array of user objects
 *
 * @uses $CFG
 * @param int $groupid The group(s) in question.
 * @param string $sort How to sort the results
 * @return object (changed to groupids)
 */
function get_group_students($groupids, $sort='ul.timeaccess DESC') {
    
    if (is_array($groupids)){
        $groups = $groupids;
        // all groups must be from one course anyway...
        $group = get_record('groups', 'id', array_shift($groups));
    } else {
        $group = get_record('groups', 'id', $groupids);
    }
    if (!$group) {
        return NULL;
    }

    $context = get_context_instance(CONTEXT_COURSE, $group->courseid);
    return get_users_by_capability($context, 'moodle/legacy:student', 'u.*, ul.timeaccess as lastaccess', $sort, '','',$groupids, '', false);
}

/**
 * Returns list of all the teachers who can access a group
 *
 * @uses $CFG
 * @param int $courseid The course in question.
 * @param int $groupid The group in question.
 * @return object
 */
function get_group_teachers($courseid, $groupid) {
/// Returns a list of all the teachers who can access a group
    if ($teachers = get_course_teachers($courseid)) {
        foreach ($teachers as $key => $teacher) {
            if ($teacher->editall) {             // These can access anything
                continue;
            }
            if (($teacher->authority > 0) and ismember($groupid, $teacher->id)) {  // Specific group teachers
                continue;
            }
            unset($teachers[$key]);
        }
    }
    return $teachers;
}


?>
