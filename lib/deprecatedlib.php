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

/// Enrol the student in any parent meta courses...
    if ($parents = get_records('course_meta', 'child_course', $courseid)) {
        foreach ($parents as $parent) {
            if ($metacontext = get_context_instance(CONTEXT_COURSE, $parent->parent_course)) {
                role_assign($role->id, $user->id, 0, $metacontext->id, $timestart, $timeend, 0, 'metacourse');
            }
        }
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

    if ($courseid) {
        /// First delete any crucial stuff that might still send mail
        if ($forums = get_records('forum', 'course', $courseid)) {
            foreach ($forums as $forum) {
                delete_records('forum_subscriptions', 'forum', $forum->id, 'userid', $userid);
            }
        }
        if ($groups = get_groups($courseid, $userid)) {
            foreach ($groups as $group) {
                delete_records('groups_members', 'groupid', $group->id, 'userid', $userid);
            }
        }
        // unenrol the student from any parent meta courses...
        if ($parents = get_records('course_meta','child_course',$courseid)) {
            foreach ($parents as $parent) {
                if (!record_exists_sql('SELECT us.id FROM '.$CFG->prefix.'user_students us, '
                                       .$CFG->prefix.'course_meta cm WHERE cm.child_course = us.course
                                        AND us.userid = '.$userid .' AND us.course != '.$courseid)) {
                    unenrol_student($userid, $parent->parent_course);
                }
            }
        }
        return delete_records('user_students', 'userid', $userid, 'course', $courseid);

    } else {
        delete_records('forum_subscriptions', 'userid', $userid);
        delete_records('groups_members', 'userid', $userid);
        return delete_records('user_students', 'userid', $userid);
    }
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

        /// Next if the teacher is not registered as a student, but is
        /// a member of a group, remove them from the group.
        if (!isstudent($courseid, $userid)) {
            if ($groups = get_groups($courseid, $userid)) {
                foreach ($groups as $group) {
                    delete_records('groups_members', 'groupid', $group->id, 'userid', $userid);
                }
            }
        }

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
 * Add a creator to the site
 *
 * @param int $userid The id of the user that is being tested against.
 * @return bool
 */
function add_creator($userid) {

    if (!record_exists('user_admins', 'userid', $userid)) {
        if (record_exists('user', 'id', $userid)) {
            $creator->userid = $userid;
            return insert_record('user_coursecreators', $creator);
        }
        return false;
    }
    return true;
}

/**
 * Remove a creator from a site
 *
 * @uses $db
 * @param int $userid The id of the user that is being tested against.
 * @return bool
 */
function remove_creator($userid) {
    global $db;

    return delete_records('user_coursecreators', 'userid', $userid);
}

/**
 * Add an admin to a site
 *
 * @uses SITEID
 * @param int $userid The id of the user that is being tested against.
 * @return bool
 */
function add_admin($userid) {

    if (!record_exists('user_admins', 'userid', $userid)) {
        if (record_exists('user', 'id', $userid)) {
            $admin->userid = $userid;

            // any admin is also a teacher on the site course
            if (!record_exists('user_teachers', 'course', SITEID, 'userid', $userid)) {
                if (!add_teacher($userid, SITEID)) {
                    return false;
                }
            }

            return insert_record('user_admins', $admin);
        }
        return false;
    }
    return true;
}

/**
 * Removes an admin from a site
 *
 * @uses $db
 * @uses SITEID
 * @param int $userid The id of the user that is being tested against.
 * @return bool
 */
function remove_admin($userid) {
    global $db;

    // remove also from the list of site teachers
    remove_teacher($userid, SITEID);

    return delete_records('user_admins', 'userid', $userid);
}



function get_user_info_from_db($field, $value) {  // For backward compatibility
    return get_complete_user_data($field, $value);
}

?>
