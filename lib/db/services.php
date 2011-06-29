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
 * Core external functions and service definitions.
 *
 * @package    core
 * @subpackage webservice
 * @copyright  2009 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$functions = array(

    // === group related functions ===

    'moodle_group_create_groups' => array(
        'classname'   => 'moodle_group_external',
        'methodname'  => 'create_groups',
        'classpath'   => 'group/externallib.php',
        'description' => 'Creates new groups.',
        'type'        => 'write',
        'capabilities'=> 'moodle/course:managegroups',
    ),

    'moodle_group_get_groups' => array(
        'classname'   => 'moodle_group_external',
        'methodname'  => 'get_groups',
        'classpath'   => 'group/externallib.php',
        'description' => 'Returns group details.',
        'type'        => 'read',
        'capabilities'=> 'moodle/course:managegroups',
    ),

    'moodle_group_get_course_groups' => array(
        'classname'   => 'moodle_group_external',
        'methodname'  => 'get_course_groups',
        'classpath'   => 'group/externallib.php',
        'description' => 'Returns all groups in specified course.',
        'type'        => 'read',
        'capabilities'=> 'moodle/course:managegroups',
    ),

    'moodle_group_delete_groups' => array(
        'classname'   => 'moodle_group_external',
        'methodname'  => 'delete_groups',
        'classpath'   => 'group/externallib.php',
        'description' => 'Deletes all specified groups.',
        'type'        => 'delete',
        'capabilities'=> 'moodle/course:managegroups',
    ),

    'moodle_group_get_groupmembers' => array(
        'classname'   => 'moodle_group_external',
        'methodname'  => 'get_groupmembers',
        'classpath'   => 'group/externallib.php',
        'description' => 'Returns group members.',
        'type'        => 'read',
        'capabilities'=> 'moodle/course:managegroups',
    ),

    'moodle_group_add_groupmembers' => array(
        'classname'   => 'moodle_group_external',
        'methodname'  => 'add_groupmembers',
        'classpath'   => 'group/externallib.php',
        'description' => 'Adds group members.',
        'type'        => 'write',
        'capabilities'=> 'moodle/course:managegroups',
    ),

    'moodle_group_delete_groupmembers' => array(
        'classname'   => 'moodle_group_external',
        'methodname'  => 'delete_groupmembers',
        'classpath'   => 'group/externallib.php',
        'description' => 'Deletes group members.',
        'type'        => 'delete',
        'capabilities'=> 'moodle/course:managegroups',
    ),


    // === file related functions ===

    'moodle_file_get_files' => array(
        'classname'   => 'moodle_file_external',
        'methodname'  => 'get_files',
        'description' => 'browse moodle files',
        'type'        => 'read',
        'classpath'   => 'files/externallib.php',
    ),
    'moodle_file_upload' => array(
        'classname'   => 'moodle_file_external',
        'methodname'  => 'upload',
        'description' => 'upload a file to moodle',
        'type'        => 'write',
        'classpath'   => 'files/externallib.php',
    ),

    // === user related functions ===

    'moodle_user_create_users' => array(
        'classname'   => 'moodle_user_external',
        'methodname'  => 'create_users',
        'classpath'   => 'user/externallib.php',
        'description' => 'Create users.',
        'type'        => 'write',
        'capabilities'=> 'moodle/user:create',
    ),

    'moodle_user_get_users_by_id' => array(
        'classname'   => 'moodle_user_external',
        'methodname'  => 'get_users_by_id',
        'classpath'   => 'user/externallib.php',
        'description' => 'Get users by id.',
        'type'        => 'read',
        'capabilities'=> 'moodle/user:viewdetails, moodle/user:viewhiddendetails, moodle/course:useremail, moodle/user:update',
    ),

    'moodle_user_get_users_by_courseid' => array(
        'classname'   => 'moodle_user_external',
        'methodname'  => 'get_users_by_courseid',
        'classpath'   => 'user/externallib.php',
        'description' => 'Get enrolled users by course id.',
        'type'        => 'read',
        'capabilities'=> 'moodle/user:viewdetails, moodle/user:viewhiddendetails, moodle/course:useremail, moodle/user:update, moodle/site:accessallgroups',
    ),

    'moodle_user_get_course_participants_by_id' => array(
        'classname'   => 'moodle_user_external',
        'methodname'  => 'get_course_participants_by_id',
        'classpath'   => 'user/externallib.php',
        'description' => 'Get course user profiles by id.',
        'type'        => 'read',
        'capabilities'=> 'moodle/user:viewdetails, moodle/user:viewhiddendetails, moodle/course:useremail, moodle/user:update, moodle/site:accessallgroups',
    ),

    'moodle_user_delete_users' => array(
        'classname'   => 'moodle_user_external',
        'methodname'  => 'delete_users',
        'classpath'   => 'user/externallib.php',
        'description' => 'Delete users.',
        'type'        => 'write',
        'capabilities'=> 'moodle/user:delete',
    ),

    'moodle_user_update_users' => array(
        'classname'   => 'moodle_user_external',
        'methodname'  => 'update_users',
        'classpath'   => 'user/externallib.php',
        'description' => 'Update users.',
        'type'        => 'write',
        'capabilities'=> 'moodle/user:update',
    ),

    // === enrol related functions ===

    'moodle_enrol_get_enrolled_users' => array(
        'classname'   => 'moodle_enrol_external',
        'methodname'  => 'get_enrolled_users',
        'classpath'   => 'enrol/externallib.php',
        'description' => 'Get list of course participants',
        'type'        => 'read',
        'capabilities'=> 'moodle/site:viewparticipants, moodle/course:viewparticipants,
            moodle/role:review, moodle/site:accessallgroups, moodle/course:enrolreview',
    ),

    'moodle_enrol_get_users_courses' => array(
        'classname'   => 'moodle_enrol_external',
        'methodname'  => 'get_users_courses',
        'classpath'   => 'enrol/externallib.php',
        'description' => 'Get list of courses user is enrolled in',
        'type'        => 'read',
        'capabilities'=> 'moodle/course:viewparticipants',
    ),

    'moodle_role_assign' => array(
        'classname'   => 'moodle_enrol_external',
        'methodname'  => 'role_assign',
        'classpath'   => 'enrol/externallib.php',
        'description' => 'Manual role assignments.',
        'type'        => 'write',
        'capabilities'=> 'moodle/role:assign',
    ),

    'moodle_role_unassign' => array(
        'classname'   => 'moodle_enrol_external',
        'methodname'  => 'role_unassign',
        'classpath'   => 'enrol/externallib.php',
        'description' => 'Manual role unassignments.',
        'type'        => 'write',
        'capabilities'=> 'moodle/role:assign',
    ),

    // === course related functions ===

    'moodle_course_get_courses' => array(
        'classname'   => 'moodle_course_external',
        'methodname'  => 'get_courses',
        'classpath'   => 'course/externallib.php',
        'description' => 'Return course details',
        'type'        => 'read',
        'capabilities'=> 'moodle/course:view,moodle/course:update,moodle/course:viewhiddencourses',
    ),

    'moodle_course_create_courses' => array(
        'classname'   => 'moodle_course_external',
        'methodname'  => 'create_courses',
        'classpath'   => 'course/externallib.php',
        'description' => 'Create new courses',
        'type'        => 'write',
        'capabilities'=> 'moodle/course:create,moodle/course:visibility',
    ),

    // === message related functions ===

    'moodle_message_send_instantmessages' => array(
        'classname'   => 'moodle_message_external',
        'methodname'  => 'send_instantmessages',
        'classpath'   => 'message/externallib.php',
        'description' => 'Send instant messages',
        'type'        => 'write',
        'capabilities'=> 'moodle/site:sendmessage',
    ),

    // === notes related functions ===

    'moodle_notes_create_notes' => array(
        'classname'   => 'moodle_notes_external',
        'methodname'  => 'create_notes',
        'classpath'   => 'notes/externallib.php',
        'description' => 'Create notes',
        'type'        => 'write',
        'capabilities'=> 'moodle/notes:manage',
    ),

    // === webservice related functions ===

    'moodle_webservice_get_siteinfo' => array(
        'classname'   => 'moodle_webservice_external',
        'methodname'  => 'get_siteinfo',
        'classpath'   => 'webservice/externallib.php',
        'description' => 'Return some site info / user info / list web service functions',
        'type'        => 'read',
    ),

);

$services = array(
   'Moodle mobile web service'  => array(
        'functions' => array (
            'moodle_enrol_get_users_courses',
            'moodle_enrol_get_enrolled_users',
            'moodle_user_get_users_by_id',
            'moodle_webservice_get_siteinfo',
            'moodle_notes_create_notes',
            'moodle_user_get_course_participants_by_id',
            'moodle_user_get_users_by_courseid',
            'moodle_message_send_instantmessages'),
        'enabled' => 0,
        'restrictedusers' => 0,
        'shortname' => MOODLE_OFFICIAL_MOBILE_SERVICE
    ),
);
