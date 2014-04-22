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
 * The functions and services defined on this file are
 * processed and registered into the Moodle DB after any
 * install or upgrade operation. All plugins support this.
 *
 * For more information, take a look to the documentation available:
 *     - Webservices API: {@link http://docs.moodle.org/dev/Web_services_API}
 *     - External API: {@link http://docs.moodle.org/dev/External_functions_API}
 *     - Upgrade API: {@link http://docs.moodle.org/dev/Upgrade_API}
 *
 * @package    core_webservice
 * @category   webservice
 * @copyright  2009 Petr Skodak
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$functions = array(

    // Cohort related functions.

    'core_cohort_create_cohorts' => array(
        'classname'   => 'core_cohort_external',
        'methodname'  => 'create_cohorts',
        'classpath'   => 'cohort/externallib.php',
        'description' => 'Creates new cohorts.',
        'type'        => 'write',
        'capabilities'=> 'moodle/cohort:manage',
    ),

    'core_cohort_delete_cohorts' => array(
        'classname'   => 'core_cohort_external',
        'methodname'  => 'delete_cohorts',
        'classpath'   => 'cohort/externallib.php',
        'description' => 'Deletes all specified cohorts.',
        'type'        => 'delete',
        'capabilities'=> 'moodle/cohort:manage',
    ),

    'core_cohort_get_cohorts' => array(
        'classname'   => 'core_cohort_external',
        'methodname'  => 'get_cohorts',
        'classpath'   => 'cohort/externallib.php',
        'description' => 'Returns cohort details.',
        'type'        => 'read',
        'capabilities'=> 'moodle/cohort:view',
    ),

    'core_cohort_update_cohorts' => array(
        'classname'   => 'core_cohort_external',
        'methodname'  => 'update_cohorts',
        'classpath'   => 'cohort/externallib.php',
        'description' => 'Updates existing cohorts.',
        'type'        => 'write',
        'capabilities'=> 'moodle/cohort:manage',
    ),

    'core_cohort_add_cohort_members' => array(
        'classname'   => 'core_cohort_external',
        'methodname'  => 'add_cohort_members',
        'classpath'   => 'cohort/externallib.php',
        'description' => 'Adds cohort members.',
        'type'        => 'write',
        'capabilities'=> 'moodle/cohort:assign',
    ),

    'core_cohort_delete_cohort_members' => array(
        'classname'   => 'core_cohort_external',
        'methodname'  => 'delete_cohort_members',
        'classpath'   => 'cohort/externallib.php',
        'description' => 'Deletes cohort members.',
        'type'        => 'delete',
        'capabilities'=> 'moodle/cohort:assign',
    ),

    'core_cohort_get_cohort_members' => array(
        'classname'   => 'core_cohort_external',
        'methodname'  => 'get_cohort_members',
        'classpath'   => 'cohort/externallib.php',
        'description' => 'Returns cohort members.',
        'type'        => 'read',
        'capabilities'=> 'moodle/cohort:view',
    ),
    // Grade related functions.

    'core_grades_get_grades' => array(
        'classname'     => 'core_grades_external',
        'methodname'    => 'get_grades',
        'description'   => 'Returns grade item details and optionally student grades.',
        'type'          => 'read',
        'capabilities'  => 'moodle/grade:view, moodle/grade:viewall',
    ),

    'core_grades_update_grades' => array(
        'classname'     => 'core_grades_external',
        'methodname'    => 'update_grades',
        'description'   => 'Update a grade item and associated student grades.',
        'type'          => 'write',
        'capabilities'  => '',
    ),

    // === group related functions ===

    'moodle_group_create_groups' => array(
        'classname'   => 'core_group_external',
        'methodname'  => 'create_groups',
        'classpath'   => 'group/externallib.php',
        'description' => 'DEPRECATED: this deprecated function will be removed in a future version. This function has be renamed as core_group_create_groups(). ',
        'type'        => 'write',
        'capabilities'=> 'moodle/course:managegroups',
    ),

    'core_group_create_groups' => array(
        'classname'   => 'core_group_external',
        'methodname'  => 'create_groups',
        'classpath'   => 'group/externallib.php',
        'description' => 'Creates new groups.',
        'type'        => 'write',
        'capabilities'=> 'moodle/course:managegroups',
    ),

    'moodle_group_get_groups' => array(
        'classname'   => 'core_group_external',
        'methodname'  => 'get_groups',
        'classpath'   => 'group/externallib.php',
        'description' => 'DEPRECATED: this deprecated function will be removed in a future version. This function has be renamed as core_group_get_groups()',
        'type'        => 'read',
        'capabilities'=> 'moodle/course:managegroups',
    ),

    'core_group_get_groups' => array(
        'classname'   => 'core_group_external',
        'methodname'  => 'get_groups',
        'classpath'   => 'group/externallib.php',
        'description' => 'Returns group details.',
        'type'        => 'read',
        'capabilities'=> 'moodle/course:managegroups',
    ),

    'moodle_group_get_course_groups' => array(
        'classname'   => 'core_group_external',
        'methodname'  => 'get_course_groups',
        'classpath'   => 'group/externallib.php',
        'description' => 'DEPRECATED: this deprecated function will be removed in a future version. This function has be renamed as core_group_get_course_groups()',
        'type'        => 'read',
        'capabilities'=> 'moodle/course:managegroups',
    ),

    'core_group_get_course_groups' => array(
        'classname'   => 'core_group_external',
        'methodname'  => 'get_course_groups',
        'classpath'   => 'group/externallib.php',
        'description' => 'Returns all groups in specified course.',
        'type'        => 'read',
        'capabilities'=> 'moodle/course:managegroups',
    ),

    'moodle_group_delete_groups' => array(
        'classname'   => 'core_group_external',
        'methodname'  => 'delete_groups',
        'classpath'   => 'group/externallib.php',
        'description' => 'DEPRECATED: this deprecated function will be removed in a future version. This function has be renamed as core_group_delete_groups()',
        'type'        => 'delete',
        'capabilities'=> 'moodle/course:managegroups',
    ),

    'core_group_delete_groups' => array(
        'classname'   => 'core_group_external',
        'methodname'  => 'delete_groups',
        'classpath'   => 'group/externallib.php',
        'description' => 'Deletes all specified groups.',
        'type'        => 'delete',
        'capabilities'=> 'moodle/course:managegroups',
    ),

    'moodle_group_get_groupmembers' => array(
        'classname'   => 'core_group_external',
        'methodname'  => 'get_group_members',
        'classpath'   => 'group/externallib.php',
        'description' => 'DEPRECATED: this deprecated function will be removed in a future version. This function has be renamed as core_group_get_group_members()',
        'type'        => 'read',
        'capabilities'=> 'moodle/course:managegroups',
    ),

    'core_group_get_group_members' => array(
        'classname'   => 'core_group_external',
        'methodname'  => 'get_group_members',
        'classpath'   => 'group/externallib.php',
        'description' => 'Returns group members.',
        'type'        => 'read',
        'capabilities'=> 'moodle/course:managegroups',
    ),

    'moodle_group_add_groupmembers' => array(
        'classname'   => 'core_group_external',
        'methodname'  => 'add_group_members',
        'classpath'   => 'group/externallib.php',
        'description' => 'DEPRECATED: this deprecated function will be removed in a future version. This function has be renamed as core_group_add_group_members()',
        'type'        => 'write',
        'capabilities'=> 'moodle/course:managegroups',
    ),

    'core_group_add_group_members' => array(
        'classname'   => 'core_group_external',
        'methodname'  => 'add_group_members',
        'classpath'   => 'group/externallib.php',
        'description' => 'Adds group members.',
        'type'        => 'write',
        'capabilities'=> 'moodle/course:managegroups',
    ),

    'moodle_group_delete_groupmembers' => array(
        'classname'   => 'core_group_external',
        'methodname'  => 'delete_group_members',
        'classpath'   => 'group/externallib.php',
        'description' => 'DEPRECATED: this deprecated function will be removed in a future version. This function has be renamed as core_group_delete_group_members()',
        'type'        => 'delete',
        'capabilities'=> 'moodle/course:managegroups',
    ),

    'core_group_delete_group_members' => array(
        'classname'   => 'core_group_external',
        'methodname'  => 'delete_group_members',
        'classpath'   => 'group/externallib.php',
        'description' => 'Deletes group members.',
        'type'        => 'delete',
        'capabilities'=> 'moodle/course:managegroups',
    ),

    'core_group_create_groupings' => array(
        'classname'   => 'core_group_external',
        'methodname'  => 'create_groupings',
        'classpath'   => 'group/externallib.php',
        'description' => 'Creates new groupings',
        'type'        => 'write',
    ),

    'core_group_update_groupings' => array(
        'classname'   => 'core_group_external',
        'methodname'  => 'update_groupings',
        'classpath'   => 'group/externallib.php',
        'description' => 'Updates existing groupings',
        'type'        => 'write',
    ),

    'core_group_get_groupings' => array(
        'classname'   => 'core_group_external',
        'methodname'  => 'get_groupings',
        'classpath'   => 'group/externallib.php',
        'description' => 'Returns groupings details.',
        'type'        => 'read',
    ),

    'core_group_get_course_groupings' => array(
        'classname'   => 'core_group_external',
        'methodname'  => 'get_course_groupings',
        'classpath'   => 'group/externallib.php',
        'description' => 'Returns all groupings in specified course.',
        'type'        => 'read',
    ),

    'core_group_delete_groupings' => array(
        'classname'   => 'core_group_external',
        'methodname'  => 'delete_groupings',
        'classpath'   => 'group/externallib.php',
        'description' => 'Deletes all specified groupings.',
        'type'        => 'write',
    ),

    'core_group_assign_grouping' => array(
        'classname'   => 'core_group_external',
        'methodname'  => 'assign_grouping',
        'classpath'   => 'group/externallib.php',
        'description' => 'Assing groups from groupings',
        'type'        => 'write',
    ),

    'core_group_unassign_grouping' => array(
        'classname'   => 'core_group_external',
        'methodname'  => 'unassign_grouping',
        'classpath'   => 'group/externallib.php',
        'description' => 'Unassing groups from groupings',
        'type'        => 'write',
    ),

    // === file related functions ===

    'moodle_file_get_files' => array(
        'classname'   => 'core_files_external',
        'methodname'  => 'get_files',
        'description' => 'DEPRECATED: this deprecated function will be removed in a future version. This function has be renamed as core_files_get_files()',
        'type'        => 'read',
        'classpath'   => 'files/externallib.php',
    ),

    'core_files_get_files' => array(
        'classname'   => 'core_files_external',
        'methodname'  => 'get_files',
        'description' => 'browse moodle files',
        'type'        => 'read',
        'classpath'   => 'files/externallib.php',
    ),

    'moodle_file_upload' => array(
        'classname'   => 'core_files_external',
        'methodname'  => 'upload',
        'description' => 'DEPRECATED: this deprecated function will be removed in a future version. This function has be renamed as core_files_upload()',
        'type'        => 'write',
        'classpath'   => 'files/externallib.php',
    ),

    'core_files_upload' => array(
        'classname'   => 'core_files_external',
        'methodname'  => 'upload',
        'description' => 'upload a file to moodle',
        'type'        => 'write',
        'classpath'   => 'files/externallib.php',
    ),

    // === user related functions ===

    'moodle_user_create_users' => array(
        'classname'   => 'core_user_external',
        'methodname'  => 'create_users',
        'classpath'   => 'user/externallib.php',
        'description' => 'DEPRECATED: this deprecated function will be removed in a future version. This function has be renamed as core_user_create_users()',
        'type'        => 'write',
        'capabilities'=> 'moodle/user:create',
    ),

    'core_user_create_users' => array(
        'classname'   => 'core_user_external',
        'methodname'  => 'create_users',
        'classpath'   => 'user/externallib.php',
        'description' => 'Create users.',
        'type'        => 'write',
        'capabilities'=> 'moodle/user:create',
    ),

    'core_user_get_users' => array(
        'classname'   => 'core_user_external',
        'methodname'  => 'get_users',
        'classpath'   => 'user/externallib.php',
        'description' => 'search for users matching the parameters',
        'type'        => 'read',
        'capabilities'=> 'moodle/user:viewdetails, moodle/user:viewhiddendetails, moodle/course:useremail, moodle/user:update',
    ),

    'moodle_user_get_users_by_id' => array(
        'classname'   => 'core_user_external',
        'methodname'  => 'get_users_by_id',
        'classpath'   => 'user/externallib.php',
        'description' => 'DEPRECATED: this deprecated function will be removed in a future version. This function has be renamed as core_user_get_users_by_id()',
        'type'        => 'read',
        'capabilities'=> 'moodle/user:viewdetails, moodle/user:viewhiddendetails, moodle/course:useremail, moodle/user:update',
    ),

    'core_user_get_users_by_field' => array(
        'classname'   => 'core_user_external',
        'methodname'  => 'get_users_by_field',
        'classpath'   => 'user/externallib.php',
        'description' => 'Retrieve users information for a specified unique field - If you want to do a user search, use core_user_get_users()',
        'type'        => 'read',
        'capabilities'=> 'moodle/user:viewdetails, moodle/user:viewhiddendetails, moodle/course:useremail, moodle/user:update',
    ),

    'core_user_get_users_by_id' => array(
        'classname'   => 'core_user_external',
        'methodname'  => 'get_users_by_id',
        'classpath'   => 'user/externallib.php',
        'description' => 'DEPRECATED: this deprecated function will be removed in a future version. This function has been replaced by core_user_get_users_by_field()',
        'type'        => 'read',
        'capabilities'=> 'moodle/user:viewdetails, moodle/user:viewhiddendetails, moodle/course:useremail, moodle/user:update',
    ),

    'moodle_user_get_users_by_courseid' => array(
        'classname'   => 'core_enrol_external',
        'methodname'  => 'get_enrolled_users',
        'classpath'   => 'enrol/externallib.php',
        'description' => 'DEPRECATED: this deprecated function will be removed in a future version. This function has be renamed as core_enrol_get_enrolled_users()',
        'type'        => 'read',
        'capabilities'=> 'moodle/user:viewdetails, moodle/user:viewhiddendetails, moodle/course:useremail, moodle/user:update, moodle/site:accessallgroups',
    ),

    'moodle_user_get_course_participants_by_id' => array(
        'classname'   => 'core_user_external',
        'methodname'  => 'get_course_user_profiles',
        'classpath'   => 'user/externallib.php',
        'description' => 'DEPRECATED: this deprecated function will be removed in a future version. This function has be renamed as core_user_get_course_user_profiles()',
        'type'        => 'read',
        'capabilities'=> 'moodle/user:viewdetails, moodle/user:viewhiddendetails, moodle/course:useremail, moodle/user:update, moodle/site:accessallgroups',
    ),

    'core_user_get_course_user_profiles' => array(
        'classname'   => 'core_user_external',
        'methodname'  => 'get_course_user_profiles',
        'classpath'   => 'user/externallib.php',
        'description' => 'Get course user profiles (each of the profils matching a course id and a user id).',
        'type'        => 'read',
        'capabilities'=> 'moodle/user:viewdetails, moodle/user:viewhiddendetails, moodle/course:useremail, moodle/user:update, moodle/site:accessallgroups',
    ),

    'moodle_user_delete_users' => array(
        'classname'   => 'core_user_external',
        'methodname'  => 'delete_users',
        'classpath'   => 'user/externallib.php',
        'description' => 'DEPRECATED: this deprecated function will be removed in a future version. This function has be renamed as core_user_delete_users()',
        'type'        => 'write',
        'capabilities'=> 'moodle/user:delete',
    ),

    'core_user_delete_users' => array(
        'classname'   => 'core_user_external',
        'methodname'  => 'delete_users',
        'classpath'   => 'user/externallib.php',
        'description' => 'Delete users.',
        'type'        => 'write',
        'capabilities'=> 'moodle/user:delete',
    ),

    'moodle_user_update_users' => array(
        'classname'   => 'core_user_external',
        'methodname'  => 'update_users',
        'classpath'   => 'user/externallib.php',
        'description' => 'DEPRECATED: this deprecated function will be removed in a future version. This function has be renamed as core_user_update_users()',
        'type'        => 'write',
        'capabilities'=> 'moodle/user:update',
    ),

    'core_user_update_users' => array(
        'classname'   => 'core_user_external',
        'methodname'  => 'update_users',
        'classpath'   => 'user/externallib.php',
        'description' => 'Update users.',
        'type'        => 'write',
        'capabilities'=> 'moodle/user:update',
    ),

    'core_user_add_user_device' => array(
        'classname'   => 'core_user_external',
        'methodname'  => 'add_user_device',
        'classpath'   => 'user/externallib.php',
        'description' => 'Store mobile user devices information for PUSH Notifications.',
        'type'        => 'write',
        'capabilities'=> '',
    ),

    // === enrol related functions ===

    'core_enrol_get_enrolled_users_with_capability' => array(
        'classname'   => 'core_enrol_external',
        'methodname'  => 'get_enrolled_users_with_capability',
        'classpath'   => 'enrol/externallib.php',
        'description' => 'For each course and capability specified, return a list of the users that are enrolled in the course
                          and have that capability',
        'type'        => 'read',
    ),

    'moodle_enrol_get_enrolled_users' => array(
        'classname'   => 'moodle_enrol_external',
        'methodname'  => 'get_enrolled_users',
        'classpath'   => 'enrol/externallib.php',
        'description' => 'DEPRECATED: this deprecated function will be removed in a future version. Please use core_enrol_get_enrolled_users() (previously known as moodle_user_get_users_by_courseid).',
        'type'        => 'read',
        'capabilities'=> 'moodle/site:viewparticipants, moodle/course:viewparticipants,
            moodle/role:review, moodle/site:accessallgroups, moodle/course:enrolreview',
    ),

    'core_enrol_get_enrolled_users' => array(
        'classname'   => 'core_enrol_external',
        'methodname'  => 'get_enrolled_users',
        'classpath'   => 'enrol/externallib.php',
        'description' => 'Get enrolled users by course id.',
        'type'        => 'read',
        'capabilities'=> 'moodle/user:viewdetails, moodle/user:viewhiddendetails, moodle/course:useremail, moodle/user:update, moodle/site:accessallgroups',
    ),

    'moodle_enrol_get_users_courses' => array(
        'classname'   => 'core_enrol_external',
        'methodname'  => 'get_users_courses',
        'classpath'   => 'enrol/externallib.php',
        'description' => 'DEPRECATED: this deprecated function will be removed in a future version. This function has be renamed as core_enrol_get_users_courses()',
        'type'        => 'read',
        'capabilities'=> 'moodle/course:viewparticipants',
    ),

    'core_enrol_get_users_courses' => array(
        'classname'   => 'core_enrol_external',
        'methodname'  => 'get_users_courses',
        'classpath'   => 'enrol/externallib.php',
        'description' => 'Get the list of courses where a user is enrolled in',
        'type'        => 'read',
        'capabilities'=> 'moodle/course:viewparticipants',
    ),

    'core_enrol_get_course_enrolment_methods' => array(
        'classname'   => 'core_enrol_external',
        'methodname'  => 'get_course_enrolment_methods',
        'classpath'   => 'enrol/externallib.php',
        'description' => 'Get the list of course enrolment methods',
        'type'        => 'read',
    ),

    // === Role related functions ===

    'moodle_role_assign' => array(
        'classname'   => 'core_role_external',
        'methodname'  => 'assign_roles',
        'classpath'   => 'enrol/externallib.php',
        'description' => 'DEPRECATED: this deprecated function will be removed in a future version. This function has be renamed as core_role_assign_role()',
        'type'        => 'write',
        'capabilities'=> 'moodle/role:assign',
    ),

    'core_role_assign_roles' => array(
        'classname'   => 'core_role_external',
        'methodname'  => 'assign_roles',
        'classpath'   => 'enrol/externallib.php',
        'description' => 'Manual role assignments.',
        'type'        => 'write',
        'capabilities'=> 'moodle/role:assign',
    ),

    'moodle_role_unassign' => array(
        'classname'   => 'core_role_external',
        'methodname'  => 'unassign_roles',
        'classpath'   => 'enrol/externallib.php',
        'description' => 'DEPRECATED: this deprecated function will be removed in a future version. This function has be renamed as core_role_unassign_role()',
        'type'        => 'write',
        'capabilities'=> 'moodle/role:assign',
    ),

    'core_role_unassign_roles' => array(
        'classname'   => 'core_role_external',
        'methodname'  => 'unassign_roles',
        'classpath'   => 'enrol/externallib.php',
        'description' => 'Manual role unassignments.',
        'type'        => 'write',
        'capabilities'=> 'moodle/role:assign',
    ),

    // === course related functions ===

    'core_course_get_contents' => array(
        'classname'   => 'core_course_external',
        'methodname'  => 'get_course_contents',
        'classpath'   => 'course/externallib.php',
        'description' => 'Get course contents',
        'type'        => 'read',
        'capabilities'=> 'moodle/course:update,moodle/course:viewhiddencourses',
    ),

    'moodle_course_get_courses' => array(
        'classname'   => 'core_course_external',
        'methodname'  => 'get_courses',
        'classpath'   => 'course/externallib.php',
        'description' => 'DEPRECATED: this deprecated function will be removed in a future version. This function has be renamed as core_course_get_courses()',
        'type'        => 'read',
        'capabilities'=> 'moodle/course:view,moodle/course:update,moodle/course:viewhiddencourses',
    ),

    'core_course_get_courses' => array(
        'classname'   => 'core_course_external',
        'methodname'  => 'get_courses',
        'classpath'   => 'course/externallib.php',
        'description' => 'Return course details',
        'type'        => 'read',
        'capabilities'=> 'moodle/course:view,moodle/course:update,moodle/course:viewhiddencourses',
    ),

    'moodle_course_create_courses' => array(
        'classname'   => 'core_course_external',
        'methodname'  => 'create_courses',
        'classpath'   => 'course/externallib.php',
        'description' => 'DEPRECATED: this deprecated function will be removed in a future version. This function has be renamed as core_course_create_courses()',
        'type'        => 'write',
        'capabilities'=> 'moodle/course:create,moodle/course:visibility',
    ),

    'core_course_create_courses' => array(
        'classname'   => 'core_course_external',
        'methodname'  => 'create_courses',
        'classpath'   => 'course/externallib.php',
        'description' => 'Create new courses',
        'type'        => 'write',
        'capabilities'=> 'moodle/course:create,moodle/course:visibility',
    ),

    'core_course_delete_courses' => array(
        'classname'   => 'core_course_external',
        'methodname'  => 'delete_courses',
        'classpath'   => 'course/externallib.php',
        'description' => 'Deletes all specified courses',
        'type'        => 'write',
        'capabilities'=> 'moodle/course:delete',
    ),

    'core_course_delete_modules' => array(
        'classname' => 'core_course_external',
        'methodname' => 'delete_modules',
        'classpath' => 'course/externallib.php',
        'description' => 'Deletes all specified module instances',
        'type' => 'write',
        'capabilities' => 'moodle/course:manageactivities'
    ),

    'core_course_duplicate_course' => array(
        'classname'   => 'core_course_external',
        'methodname'  => 'duplicate_course',
        'classpath'   => 'course/externallib.php',
        'description' => 'Duplicate an existing course (creating a new one) without user data',
        'type'        => 'write',
        'capabilities'=> 'moodle/backup:backupcourse,moodle/restore:restorecourse,moodle/course:create',
    ),

    'core_course_update_courses' => array(
        'classname'   => 'core_course_external',
        'methodname'  => 'update_courses',
        'classpath'   => 'course/externallib.php',
        'description' => 'Update courses',
        'type'        => 'write',
        'capabilities'=> 'moodle/course:update,moodle/course:changecategory,moodle/course:changefullname,moodle/course:changeshortname,moodle/course:changeidnumber,moodle/course:changesummary,moodle/course:visibility',
    ),

    // === course category related functions ===

    'core_course_get_categories' => array(
        'classname'   => 'core_course_external',
        'methodname'  => 'get_categories',
        'classpath'   => 'course/externallib.php',
        'description' => 'Return category details',
        'type'        => 'read',
        'capabilities'=> 'moodle/category:viewhiddencategories',
    ),

    'core_course_create_categories' => array(
        'classname'   => 'core_course_external',
        'methodname'  => 'create_categories',
        'classpath'   => 'course/externallib.php',
        'description' => 'Create course categories',
        'type'        => 'write',
        'capabilities'=> 'moodle/category:manage',
    ),

    'core_course_update_categories' => array(
        'classname'   => 'core_course_external',
        'methodname'  => 'update_categories',
        'classpath'   => 'course/externallib.php',
        'description' => 'Update categories',
        'type'        => 'write',
        'capabilities'=> 'moodle/category:manage',
    ),

    'core_course_delete_categories' => array(
        'classname'   => 'core_course_external',
        'methodname'  => 'delete_categories',
        'classpath'   => 'course/externallib.php',
        'description' => 'Delete course categories',
        'type'        => 'write',
        'capabilities'=> 'moodle/category:manage',
    ),

    'core_course_import_course' => array(
        'classname'   => 'core_course_external',
        'methodname'  => 'import_course',
        'classpath'   => 'course/externallib.php',
        'description' => 'Import course data from a course into another course. Does not include any user data.',
        'type'        => 'write',
        'capabilities'=> 'moodle/backup:backuptargetimport, moodle/restore:restoretargetimport',
    ),

    // === message related functions ===

    'moodle_message_send_instantmessages' => array(
        'classname'   => 'core_message_external',
        'methodname'  => 'send_instant_messages',
        'classpath'   => 'message/externallib.php',
        'description' => 'DEPRECATED: this deprecated function will be removed in a future version. This function has be renamed as core_message_send_instant_messages()',
        'type'        => 'write',
        'capabilities'=> 'moodle/site:sendmessage',
    ),

    'core_message_send_instant_messages' => array(
        'classname'   => 'core_message_external',
        'methodname'  => 'send_instant_messages',
        'classpath'   => 'message/externallib.php',
        'description' => 'Send instant messages',
        'type'        => 'write',
        'capabilities'=> 'moodle/site:sendmessage',
    ),

    'core_message_create_contacts' => array(
        'classname'   => 'core_message_external',
        'methodname'  => 'create_contacts',
        'classpath'   => 'message/externallib.php',
        'description' => 'Add contacts to the contact list',
        'type'        => 'write',
        'capabilities'=> '',
    ),

    'core_message_delete_contacts' => array(
        'classname'   => 'core_message_external',
        'methodname'  => 'delete_contacts',
        'classpath'   => 'message/externallib.php',
        'description' => 'Remove contacts from the contact list',
        'type'        => 'write',
        'capabilities'=> '',
    ),

    'core_message_block_contacts' => array(
        'classname'   => 'core_message_external',
        'methodname'  => 'block_contacts',
        'classpath'   => 'message/externallib.php',
        'description' => 'Block contacts',
        'type'        => 'write',
        'capabilities'=> '',
    ),

    'core_message_unblock_contacts' => array(
        'classname'   => 'core_message_external',
        'methodname'  => 'unblock_contacts',
        'classpath'   => 'message/externallib.php',
        'description' => 'Unblock contacts',
        'type'        => 'write',
        'capabilities'=> '',
    ),

    'core_message_get_contacts' => array(
        'classname'   => 'core_message_external',
        'methodname'  => 'get_contacts',
        'classpath'   => 'message/externallib.php',
        'description' => 'Retrieve the contact list',
        'type'        => 'read',
        'capabilities'=> '',
    ),

    'core_message_search_contacts' => array(
        'classname'   => 'core_message_external',
        'methodname'  => 'search_contacts',
        'classpath'   => 'message/externallib.php',
        'description' => 'Search for contacts',
        'type'        => 'read',
        'capabilities'=> '',
    ),

    // === notes related functions ===

    'moodle_notes_create_notes' => array(
        'classname'   => 'core_notes_external',
        'methodname'  => 'create_notes',
        'classpath'   => 'notes/externallib.php',
        'description' => 'DEPRECATED: this deprecated function will be removed in a future version. This function has be renamed as core_notes_create_notes()',
        'type'        => 'write',
        'capabilities'=> 'moodle/notes:manage',
    ),

    'core_notes_create_notes' => array(
        'classname'   => 'core_notes_external',
        'methodname'  => 'create_notes',
        'classpath'   => 'notes/externallib.php',
        'description' => 'Create notes',
        'type'        => 'write',
        'capabilities'=> 'moodle/notes:manage',
    ),

    'core_notes_delete_notes' => array(
        'classname'   => 'core_notes_external',
        'methodname'  => 'delete_notes',
        'classpath'   => 'notes/externallib.php',
        'description' => 'Delete notes',
        'type'        => 'write',
        'capabilities'=> 'moodle/notes:manage',
    ),

    'core_notes_get_notes' => array(
        'classname'   => 'core_notes_external',
        'methodname'  => 'get_notes',
        'classpath'   => 'notes/externallib.php',
        'description' => 'Get notes',
        'type'        => 'read',
        'capabilities'=> 'moodle/notes:view',
    ),

    'core_notes_update_notes' => array(
        'classname'   => 'core_notes_external',
        'methodname'  => 'update_notes',
        'classpath'   => 'notes/externallib.php',
        'description' => 'Update notes',
        'type'        => 'write',
        'capabilities'=> 'moodle/notes:manage',
    ),

    // === grading related functions ===

    'core_grading_get_definitions' => array(
        'classname'   => 'core_grading_external',
        'methodname'  => 'get_definitions',
        'description' => 'Get grading definitions',
        'type'        => 'read'
    ),

    'core_grade_get_definitions' => array(
        'classname'   => 'core_grade_external',
        'methodname'  => 'get_definitions',
        'classpath'   => 'grade/externallib.php',
        'description' => 'DEPRECATED: this deprecated function will be removed in a future version. This function has been renamed as core_grading_get_definitions()',
        'type'        => 'read'
    ),

    'core_grading_get_gradingform_instances' => array(
        'classname'   => 'core_grading_external',
        'methodname'  => 'get_gradingform_instances',
        'description' => 'Get grading form instances',
        'type'        => 'read'
    ),

    // === webservice related functions ===

    'moodle_webservice_get_siteinfo' => array(
        'classname'   => 'core_webservice_external',
        'methodname'  => 'get_site_info',
        'classpath'   => 'webservice/externallib.php',
        'description' => 'DEPRECATED: this deprecated function will be removed in a future version. This function has be renamed as core_webservice_get_site_info()',
        'type'        => 'read',
    ),

    'core_webservice_get_site_info' => array(
        'classname'   => 'core_webservice_external',
        'methodname'  => 'get_site_info',
        'classpath'   => 'webservice/externallib.php',
        'description' => 'Return some site info / user info / list web service functions',
        'type'        => 'read',
    ),

    'core_get_string' => array(
        'classname'   => 'core_external',
        'methodname'  => 'get_string',
        'classpath'   => 'lib/external/externallib.php',
        'description' => 'Return a translated string - similar to core get_string() call',
        'type'        => 'read',
    ),

    'core_get_strings' => array(
        'classname'   => 'core_external',
        'methodname'  => 'get_strings',
        'classpath'   => 'lib/external/externallib.php',
        'description' => 'Return some translated strings - like several core get_string() calls',
        'type'        => 'read',
    ),

    'core_get_component_strings' => array(
        'classname'   => 'core_external',
        'methodname'  => 'get_component_strings',
        'classpath'   => 'lib/external/externallib.php',
        'description' => 'Return all raw strings (with {$a->xxx}) for a specific component
            - similar to core get_component_strings() call',
        'type'        => 'read',
    ),


    // === Calendar related functions ===

    'core_calendar_delete_calendar_events' => array(
        'classname'   => 'core_calendar_external',
        'methodname'  => 'delete_calendar_events',
        'description' => 'Delete calendar events',
        'classpath'   => 'calendar/externallib.php',
        'type'        => 'write',
        'capabilities'=> 'moodle/calendar:manageentries', 'moodle/calendar:manageownentries', 'moodle/calendar:managegroupentries'
    ),


    'core_calendar_get_calendar_events' => array(
        'classname'   => 'core_calendar_external',
        'methodname'  => 'get_calendar_events',
        'description' => 'Get calendar events',
        'classpath'   => 'calendar/externallib.php',
        'type'        => 'read',
        'capabilities'=> 'moodle/calendar:manageentries', 'moodle/calendar:manageownentries', 'moodle/calendar:managegroupentries'
    ),

    'core_calendar_create_calendar_events' => array(
        'classname'   => 'core_calendar_external',
        'methodname'  => 'create_calendar_events',
        'description' => 'Create calendar events',
        'classpath'   => 'calendar/externallib.php',
        'type'        => 'write',
        'capabilities'=> 'moodle/calendar:manageentries', 'moodle/calendar:manageownentries', 'moodle/calendar:managegroupentries'
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
            'moodle_message_send_instantmessages',
            'core_course_get_contents',
            'core_get_component_strings',
            'core_user_add_user_device',
            'core_calendar_get_calendar_events',
            'core_enrol_get_users_courses',
            'core_enrol_get_enrolled_users',
            'core_user_get_users_by_id',
            'core_webservice_get_site_info',
            'core_notes_create_notes',
            'core_user_get_course_user_profiles',
            'core_enrol_get_enrolled_users',
            'core_message_send_instant_messages',
            'mod_assign_get_grades',
            'mod_assign_get_assignments',
            'mod_assign_get_submissions',
            'mod_assign_get_user_flags',
            'mod_assign_set_user_flags',
            'mod_assign_get_user_mappings',
            'mod_assign_revert_submissions_to_draft',
            'mod_assign_lock_submissions',
            'mod_assign_unlock_submissions',
            'mod_assign_save_submission',
            'mod_assign_submit_for_grading',
            'mod_assign_save_grade',
            'mod_assign_save_user_extensions',
            'mod_assign_reveal_identities',
            'message_airnotifier_is_system_configured',
            'message_airnotifier_are_notification_preferences_configured',
            'core_grades_get_grades',
            'core_grades_update_grades'),
        'enabled' => 0,
        'restrictedusers' => 0,
        'shortname' => MOODLE_OFFICIAL_MOBILE_SERVICE,
        'downloadfiles' => 1,
        'uploadfiles' => 1
    ),
);
