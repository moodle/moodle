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
 * Ensure that a variable is set
 *
 * Return $var if it is defined, otherwise return $default,
 * This function is very similar to {@link optional_variable()}
 *
 * @param    mixed $var the variable which may be unset
 * @param    mixed $default the value to return if $var is unset
 * @return   mixed
 */
function nvl(&$var, $default='') {
    global $CFG;

    if (!empty($CFG->disableglobalshack)) {
      error( "The nvl() function is deprecated ($var, $default)." );
    }
    return isset($var) ? $var : $default;
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

    $context = get_context_instance(CONTEXT_SYSTEM);

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

    // make sure it works on the site course
    $context = get_context_instance(CONTEXT_COURSE, $courseid);

    /// For the site course, old way was to check if $CFG->allusersaresitestudents was set to true.
    /// The closest comparible method using roles is if the $CFG->defaultuserroleid is set to the legacy
    /// student role. This function should be replaced where it is used with something more meaningful.
    if (($courseid == SITEID) && !empty($CFG->defaultuserroleid) && empty($CFG->nodefaultuserrolelists)) {
        if ($roles = get_roles_with_capability('moodle/legacy:student', CAP_ALLOW, $context)) {
            $hascap = false;
            foreach ($roles as $role) {
                if ($role->id == $CFG->defaultuserroleid) {
                    $hascap = true;
                    break;
                }
            }
            if ($hascap) {
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
        }
    }

    $LIKE      = sql_ilike();
    $fullname  = sql_fullname('u.firstname','u.lastname');

    $groupmembers = '';

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

    /// For the site course, if the $CFG->defaultuserroleid is set to the legacy teacher role, then all
    /// users are teachers. This function should be replaced where it is used with something more
    /// meaningful.
    if (($courseid == SITEID) && !empty($CFG->defaultuserroleid) && empty($CFG->nodefaultuserrolelists)) {
        if ($roles = get_roles_with_capability('moodle/legacy:teacher', CAP_ALLOW, $context)) {
            $hascap = false;
            foreach ($roles as $role) {
                if ($role->id == $CFG->defaultuserroleid) {
                    $hascap = true;
                    break;
                }
            }
            if ($hascap) {
                if (empty($fields)) {
                    $fields = '*';
                }
                return get_users(true, '', true, $exceptions, 'lastname ASC', '', '', '', '', $fields);
            }
        }
    }

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
    global $CFG;

    $context = get_context_instance(CONTEXT_COURSE, $courseid);

    /// If the course id is the SITEID, we need to return all the users if the "defaultuserroleid"
    /// has the capbility of accessing the site course. $CFG->nodefaultuserrolelists set to true can
    /// over-rule using this.
    if (($courseid == SITEID) && !empty($CFG->defaultuserroleid) && empty($CFG->nodefaultuserrolelists)) {
        if ($roles = get_roles_with_capability('moodle/course:view', CAP_ALLOW, $context)) {
            $hascap = false;
            foreach ($roles as $role) {
                if ($role->id == $CFG->defaultuserroleid) {
                    $hascap = true;
                    break;
                }
            }
            if ($hascap) {
                if (empty($fields)) {
                    $fields = '*';
                }
                return get_users(true, '', true, $exceptions, 'lastname ASC', '', '', '', '', $fields);
            }
        }
    }
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
        $group = groups_get_group(array_shift($groups));
    } else {
        $group = groups_get_group($groupids);
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
            if (($teacher->authority > 0) and groups_is_member($groupid, $teacher->id)) {  // Specific group teachers
                continue;
            }
            unset($teachers[$key]);
        }
    }
    return $teachers;
}



########### FROM weblib.php ##########################################################################


/**
 * Print a message in a standard themed box.
 * This old function used to implement boxes using tables.  Now it uses a DIV, but the old
 * parameters remain.  If possible, $align, $width and $color should not be defined at all.
 * Preferably just use print_box() in weblib.php
 *
 * @param string $align, alignment of the box, not the text (default center, left, right).
 * @param string $width, width of the box, including units %, for example '100%'.
 * @param string $color, background colour of the box, for example '#eee'.
 * @param int $padding, padding in pixels, specified without units.
 * @param string $class, space-separated class names.
 * @param string $id, space-separated id names.
 * @param boolean $return, return as string or just print it
 */
function print_simple_box($message, $align='', $width='', $color='', $padding=5, $class='generalbox', $id='', $return=false) {
    $output = '';
    $output .= print_simple_box_start($align, $width, $color, $padding, $class, $id, true);
    $output .= stripslashes_safe($message);
    $output .= print_simple_box_end(true);

    if ($return) {
        return $output;
    } else {
        echo $output;
    }
}



/**
 * This old function used to implement boxes using tables.  Now it uses a DIV, but the old
 * parameters remain.  If possible, $align, $width and $color should not be defined at all.
 * Even better, please use print_box_start() in weblib.php
 *
 * @param string $align, alignment of the box, not the text (default center, left, right).   DEPRECATED
 * @param string $width, width of the box, including % units, for example '100%'.            DEPRECATED
 * @param string $color, background colour of the box, for example '#eee'.                   DEPRECATED
 * @param int $padding, padding in pixels, specified without units.                          OBSOLETE
 * @param string $class, space-separated class names.
 * @param string $id, space-separated id names.
 * @param boolean $return, return as string or just print it
 */
function print_simple_box_start($align='', $width='', $color='', $padding=5, $class='generalbox', $id='', $return=false) {

    $output = '';

    $divclasses = 'box '.$class.' '.$class.'content';
    $divstyles  = '';

    if ($align) {
        $divclasses .= ' boxalign'.$align;    // Implement alignment using a class
    }
    if ($width) {    // Hopefully we can eliminate these in calls to this function (inline styles are bad)
        if (substr($width, -1, 1) == '%') {    // Width is a % value
            $width = (int) substr($width, 0, -1);    // Extract just the number
            if ($width < 40) {
                $divclasses .= ' boxwidthnarrow';    // Approx 30% depending on theme
            } else if ($width > 60) {
                $divclasses .= ' boxwidthwide';      // Approx 80% depending on theme
            } else {
                $divclasses .= ' boxwidthnormal';    // Approx 50% depending on theme
            }
        } else {
            $divstyles  .= ' width:'.$width.';';     // Last resort
        }
    }
    if ($color) {    // Hopefully we can eliminate these in calls to this function (inline styles are bad)
        $divstyles  .= ' background:'.$color.';';
    }
    if ($divstyles) {
        $divstyles = ' style="'.$divstyles.'"';
    }

    if ($id) {
        $id = ' id="'.$id.'"';
    }

    $output .= '<div'.$id.$divstyles.' class="'.$divclasses.'">';

    if ($return) {
        return $output;
    } else {
        echo $output;
    }
}


/**
 * Print the end portion of a standard themed box.
 * Preferably just use print_box_end() in weblib.php
 */
function print_simple_box_end($return=false) {
    $output = '</div>';
    if ($return) {
        return $output;
    } else {
        echo $output;
    }
}

/**
 * deprecated - use clean_param($string, PARAM_FILE); instead
 * Check for bad characters ?
 *
 * @param string $string ?
 * @param int $allowdots ?
 * @todo Finish documenting this function - more detail needed in description as well as details on arguments
 */
function detect_munged_arguments($string, $allowdots=1) {
    if (substr_count($string, '..') > $allowdots) {   // Sometimes we allow dots in references
        return true;
    }
    if (ereg('[\|\`]', $string)) {  // check for other bad characters
        return true;
    }
    if (empty($string) or $string == '/') {
        return true;
    }

    return false;
}

/** Deprecated function - returns the code of the current charset - originally depended on the selected language pack.
 *
 * @param $ignorecache not used anymore
 * @return string always returns 'UTF-8'
 */
function current_charset($ignorecache = false) {
    return 'UTF-8';
}


/////////////////////////////////////////////////////////////
/// Old functions not used anymore - candidates for removal
/////////////////////////////////////////////////////////////

/**
 * Load a template from file - this function dates back to Moodle 1 :-) not used anymore
 *
 * Returns a (big) string containing the contents of a template file with all
 * the variables interpolated.  all the variables must be in the $var[] array or
 * object (whatever you decide to use).
 *
 * <b>WARNING: do not use this on big files!!</b>
 *
 * @param string $filename Location on the server's filesystem where template can be found.
 * @param mixed $var Passed in by reference. An array or object which will be loaded with data from the template file.
 *
 */
function read_template($filename, &$var) {

    $temp = str_replace("\\", "\\\\", implode(file($filename), ''));
    $temp = str_replace('"', '\"', $temp);
    eval("\$template = \"$temp\";");
    return $template;
}


/**
 * deprecated - relies on register globals; use new formslib instead
 *
 * Set a variable's value depending on whether or not it already has a value.
 *
 * If variable is set, set it to the set_value otherwise set it to the
 * unset_value.  used to handle checkboxes when you are expecting them from
 * a form
 *
 * @param mixed $var Passed in by reference. The variable to check.
 * @param mixed $set_value The value to set $var to if $var already has a value.
 * @param mixed $unset_value The value to set $var to if $var does not already have a value.
 */
function checked(&$var, $set_value = 1, $unset_value = 0) {

    if (empty($var)) {
        $var = $unset_value;
    } else {
        $var = $set_value;
    }
}

/**
 * deprecated - use new formslib instead
 *
 * Prints the word "checked" if a variable is true, otherwise prints nothing,
 * used for printing the word "checked" in a checkbox form element.
 *
 * @param boolean $var Variable to be checked for true value
 * @param string $true_value Value to be printed if $var is true
 * @param string $false_value Value to be printed if $var is false
 */
function frmchecked(&$var, $true_value = 'checked', $false_value = '') {

    if ($var) {
        echo $true_value;
    } else {
        echo $false_value;
    }
}

/**
 * Legacy function, provided for backward compatability.
 * This method now simply calls {@link use_html_editor()}
 *
 * @deprecated Use {@link use_html_editor()} instead.
 * @param string $name Form element to replace with HTMl editor by name
 * @todo Finish documenting this function
 */
function print_richedit_javascript($form, $name, $source='no') {
    use_html_editor($name);
}

/** various deprecated groups function **/


/**
 * Returns the table in which group members are stored, with a prefix 'gm'.
 * @return SQL string.
 */
function groups_members_from_sql() {
    global $CFG;
    return " {$CFG->prefix}groups_members gm ";
}

/**
 * Returns a join testing user.id against member's user ID.
 * Relies on 'user' table being included as 'user u'.
 * Used in Quiz module reports.
 * @param group ID, optional to include a test for this in the SQL.
 * @return SQL string.
 */
function groups_members_join_sql($groupid=false) {
    $sql = ' JOIN '.groups_members_from_sql().' ON u.id = gm.userid ';
    if ($groupid) {
        $sql = "AND gm.groupid = '$groupid' ";
    }
    return $sql;
    //return ' INNER JOIN '.$CFG->prefix.'role_assignments ra ON u.id=ra.userid'.
    //       ' INNER JOIN '.$CFG->prefix.'context c ON ra.contextid=c.id AND c.contextlevel='.CONTEXT_GROUP.' AND c.instanceid='.$groupid;
}

/**
 * Returns SQL for a WHERE clause testing the group ID.
 * Optionally test the member's ID against another table's user ID column.
 * @param groupid
 * @param userid_sql Optional user ID column selector, example "mdl_user.id", or false.
 * @return SQL string.
 */
function groups_members_where_sql($groupid, $userid_sql=false) {
    $sql = " gm.groupid = '$groupid' ";
    if ($userid_sql) {
        $sql .= "AND $userid_sql = gm.userid ";
    }
    return $sql;
}


/**
 * Returns an array of group objects that the user is a member of
 * in the given course.  If userid isn't specified, then return a
 * list of all groups in the course.
 *
 * @uses $CFG
 * @param int $courseid The id of the course in question.
 * @param int $userid The id of the user in question as found in the 'user' table 'id' field.
 * @return object
 */
function get_groups($courseid, $userid=0) {
    return groups_get_all_groups($courseid, $userid);
}

/**
 * Returns the user's groups in a particular course
 * note: this function originally returned only one group
 *
 * @uses $CFG
 * @param int $courseid The course in question.
 * @param int $userid The id of the user as found in the 'user' table.
 * @param int $groupid The id of the group the user is in.
 * @return aray of groups
 */
function user_group($courseid, $userid) {
    return groups_get_all_groups($courseid, $userid);
}


/**
 * Determines if the user is a member of the given group.
 *
 * @param int $groupid The group to check for membership.
 * @param int $userid The user to check against the group.
 * @return boolean True if the user is a member, false otherwise.
 */
function ismember($groupid, $userid = null) {
    return groups_is_member($groupid, $userid);
}

/**
 * Get the IDs for the user's groups in the given course.
 *
 * @uses $USER
 * @param int $courseid The course being examined - the 'course' table id field.
 * @return array An _array_ of groupids.
 * (Was return $groupids[0] - consequences!)
 */
function mygroupid($courseid) {
    global $USER;
    if ($groups = groups_get_all_groups($courseid, $USER->id)) {
        return array_keys($groups);
    } else {
        return false;
    }
}

/**
 * Add a user to a group, return true upon success or if user already a group
 * member
 *
 * @param int $groupid  The group id to add user to
 * @param int $userid   The user id to add to the group
 * @return bool
 */
function add_user_to_group($groupid, $userid) {
    global $CFG;
    require_once($CFG->dirroot.'/group/lib.php');

    return groups_add_member($groupid, $userid);
}


/**
 * Returns an array of user objects
 *
 * @uses $CFG
 * @param int $groupid The group in question.
 * @param string $sort ?
 * @param string $exceptions ?
 * @return object
 * @todo Finish documenting this function
 */
function get_group_users($groupid, $sort='u.lastaccess DESC', $exceptions='',
                         $fields='u.*') {
    global $CFG;
    if (!empty($exceptions)) {
        $except = ' AND u.id NOT IN ('. $exceptions .') ';
    } else {
        $except = '';
    }
    // in postgres, you can't have things in sort that aren't in the select, so...
    $extrafield = str_replace('ASC','',$sort);
    $extrafield = str_replace('DESC','',$extrafield);
    $extrafield = trim($extrafield);
    if (!empty($extrafield)) {
        $extrafield = ','.$extrafield;
    }
    return get_records_sql("SELECT DISTINCT $fields $extrafield
                              FROM {$CFG->prefix}user u,
                                   {$CFG->prefix}groups_members m
                             WHERE m.groupid = '$groupid'
                               AND m.userid = u.id $except
                          ORDER BY $sort");
}

/**
 * Returns the current group mode for a given course or activity module
 *
 * Could be false, SEPARATEGROUPS or VISIBLEGROUPS    (<-- Martin)
 */
function groupmode($course, $cm=null) {

    if (isset($cm->groupmode) && empty($course->groupmodeforce)) {
        return $cm->groupmode;
    }
    return $course->groupmode;
}


/**
 * Sets the current group in the session variable
 * When $SESSION->currentgroup[$courseid] is set to 0 it means, show all groups.
 * Sets currentgroup[$courseid] in the session variable appropriately.
 * Does not do any permission checking.
 * @uses $SESSION
 * @param int $courseid The course being examined - relates to id field in
 * 'course' table.
 * @param int $groupid The group being examined.
 * @return int Current group id which was set by this function
 */
function set_current_group($courseid, $groupid) {
    global $SESSION;
    return $SESSION->currentgroup[$courseid] = $groupid;
}


/**
 * Gets the current group - either from the session variable or from the database.
 *
 * @uses $USER
 * @uses $SESSION
 * @param int $courseid The course being examined - relates to id field in
 * 'course' table.
 * @param bool $full If true, the return value is a full record object.
 * If false, just the id of the record.
 */
function get_current_group($courseid, $full = false) {
    global $SESSION;

    if (isset($SESSION->currentgroup[$courseid])) {
        if ($full) {
            return groups_get_group($SESSION->currentgroup[$courseid]);
        } else {
            return $SESSION->currentgroup[$courseid];
        }
    }

    $mygroupid = mygroupid($courseid);
    if (is_array($mygroupid)) {
        $mygroupid = array_shift($mygroupid);
        set_current_group($courseid, $mygroupid);
        if ($full) {
            return groups_get_group($mygroupid);
        } else {
            return $mygroupid;
        }
    }

    if ($full) {
        return false;
    } else {
        return 0;
    }
}


/**
 * A combination function to make it easier for modules
 * to set up groups.
 *
 * It will use a given "groupid" parameter and try to use
 * that to reset the current group for the user.
 *
 * @uses VISIBLEGROUPS
 * @param course $course A {@link $COURSE} object
 * @param int $groupmode Either NOGROUPS, SEPARATEGROUPS or VISIBLEGROUPS
 * @param int $groupid Will try to use this optional parameter to
 *            reset the current group for the user
 * @return int|false Returns the current group id or false if error.
 */
function get_and_set_current_group($course, $groupmode, $groupid=-1) {

    // Sets to the specified group, provided the current user has view permission
    if (!$groupmode) {   // Groups don't even apply
        return false;
    }

    $currentgroupid = get_current_group($course->id);

    if ($groupid < 0) {  // No change was specified
        return $currentgroupid;
    }

    $context = get_context_instance(CONTEXT_COURSE, $course->id);
    if ($groupid and $group = get_record('groups', 'id', $groupid)) {      // Try to change the current group to this groupid
        if ($group->courseid == $course->id) {
            if (has_capability('moodle/site:accessallgroups', $context)) {  // Sets current default group
                $currentgroupid = set_current_group($course->id, $groupid);

            } elseif ($groupmode == VISIBLEGROUPS) {
                  // All groups are visible
                //if (groups_is_member($group->id)){
                    $currentgroupid = set_current_group($course->id, $groupid); //set this since he might post
                /*)}else {
                    $currentgroupid = $group->id;*/
            } elseif ($groupmode == SEPARATEGROUPS) { // student in separate groups switching
                if (groups_is_member($groupid)) { //check if is a member
                    $currentgroupid = set_current_group($course->id, $groupid); //might need to set_current_group?
                }
                else {
                    notify('You do not belong to this group! ('.$groupid.')', 'error');
                }
            }
        }
    } else { // When groupid = 0 it means show ALL groups
        // this is changed, non editting teacher needs access to group 0 as well,
        // for viewing work in visible groups (need to set current group for multiple pages)
        if (has_capability('moodle/site:accessallgroups', $context)) { // Sets current default group
            $currentgroupid = set_current_group($course->id, 0);

        } else if ($groupmode == VISIBLEGROUPS) {  // All groups are visible
            $currentgroupid = set_current_group($course->id, 0);
        }
    }

    return $currentgroupid;
}


/**
 * A big combination function to make it easier for modules
 * to set up groups.
 *
 * Terminates if the current user shouldn't be looking at this group
 * Otherwise returns the current group if there is one
 * Otherwise returns false if groups aren't relevant
 *
 * @uses SEPARATEGROUPS
 * @uses VISIBLEGROUPS
 * @param course $course A {@link $COURSE} object
 * @param int $groupmode Either NOGROUPS, SEPARATEGROUPS or VISIBLEGROUPS
 * @param string $urlroot ?
 * @return int|false
 */
function setup_and_print_groups($course, $groupmode, $urlroot) {

    global $USER, $SESSION; //needs his id, need to hack his groups in session

    $changegroup = optional_param('group', -1, PARAM_INT);

    $currentgroup = get_and_set_current_group($course, $groupmode, $changegroup);
    if ($currentgroup === false) {
        return false;
    }

    $context = get_context_instance(CONTEXT_COURSE, $course->id);

    if ($groupmode == SEPARATEGROUPS and !$currentgroup and !has_capability('moodle/site:accessallgroups', $context)) {
        //we are in separate groups and the current group is group 0, as last set.
        //this can mean that either, this guy has no group
        //or, this guy just came from a visible all forum, and he left when he set his current group to 0 (show all)

        if ($usergroups = groups_get_all_groups($course->id, $USER->id)){
            //for the second situation, we need to perform the trick and get him a group.
            $first = reset($usergroups);
            $currentgroup = get_and_set_current_group($course, $groupmode, $first->id);

        } else {
            //else he has no group in this course
            print_heading(get_string('notingroup'));
            print_footer($course);
            exit;
        }
    }

    if ($groupmode == VISIBLEGROUPS or ($groupmode and has_capability('moodle/site:accessallgroups', $context))) {

        if ($groups = groups_get_all_groups($course->id)) {

            echo '<div class="groupselector">';
            print_group_menu($groups, $groupmode, $currentgroup, $urlroot, 1);
            echo '</div>';
        }

    } else if ($groupmode == SEPARATEGROUPS and has_capability('moodle/course:view', $context)) {
        //get all the groups this guy is in in this course
        if ($usergroups = groups_get_all_groups($course->id, $USER->id)){
            echo '<div class="groupselector">';
            //print them in the menu
            print_group_menu($usergroups, $groupmode, $currentgroup, $urlroot, 0);
            echo '</div>';
        }
    }

    return $currentgroup;
}

/**
 * Prints an appropriate group selection menu
 *
 * @uses VISIBLEGROUPS
 * @param array $groups ?
 * @param int $groupmode ?
 * @param string $currentgroup ?
 * @param string $urlroot ?
 * @param boolean $showall: if set to 0, it is a student in separate groups, do not display all participants
 * @todo Finish documenting this function
 */
function print_group_menu($groups, $groupmode, $currentgroup, $urlroot, $showall=1, $return=false) {

    $output = '';
    $groupsmenu = array();

/// Add an "All groups" to the start of the menu
    if ($showall){
        $groupsmenu[0] = get_string('allparticipants');
    }
    foreach ($groups as $key => $group) {
        $groupsmenu[$key] = format_string($group->name);
    }

    if ($groupmode == VISIBLEGROUPS) {
        $grouplabel = get_string('groupsvisible');
    } else {
        $grouplabel = get_string('groupsseparate');
    }

    if (count($groupsmenu) == 1) {
        $groupname = reset($groupsmenu);
        $output .= $grouplabel.': '.$groupname;
    } else {
        $output .= popup_form($urlroot.'&amp;group=', $groupsmenu, 'selectgroup', $currentgroup, '', '', '', true, 'self', $grouplabel);
    }

    if ($return) {
        return $output;
    } else {
        echo $output;
    }

}

?>
