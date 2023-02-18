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
 * @package   block_iomad_company_admin
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Capability definitions for the iomad_company_admin block.
//
// The capabilities are loaded into the database table when the block is
// installed or updated. Whenever the capability definitions are updated,
// the module version number should be bumped up.
//
// The system has four possible values for a capability:
// CAP_ALLOW, CAP_PREVENT, CAP_PROHIBIT, and inherit (not set).
//
//
// CAPABILITY NAMING CONVENTION
//
// It is important that capability names are unique. The naming convention
// for capabilities that are specific to modules and blocks is as follows:
//   [mod/block]/<plugin_name>:<capabilityname>
//
// component_name should be the same as the directory name of the mod or block.
//
// Core moodle capabilities are defined thus:
//    moodle/<capabilityclass>:<capabilityname>
//
// Examples: mod/forum:viewpost
//           block/recent_activity:view
//           moodle/site:deleteuser
//
// The variable name for the capability definitions array is $capabilities.


$capabilities = array(

    'block/iomad_company_admin:addinstance' => array(

        'captype' => 'read',
        'contextlevel' => CONTEXT_BLOCK
    ),

    'block/iomad_company_admin:myaddinstance' => array(

        'captype' => 'read',
        'contextlevel' => CONTEXT_BLOCK
    ),

    'block/iomad_company_admin:companymanagement_view' => array(

        'captype' => 'read',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'companymanager' => CAP_ALLOW,
            'companydepartmentmanager' => CAP_ALLOW,
            'clientadministrator' => CAP_ALLOW
        ),
    ),

    'block/iomad_company_admin:usermanagement_view' => array(

        'captype' => 'read',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'companymanager' => CAP_ALLOW,
            'companydepartmentmanager' => CAP_ALLOW,
            'clientadministrator' => CAP_ALLOW
        ),
    ),

    'block/iomad_company_admin:coursemanagement_view' => array(

        'captype' => 'read',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'companymanager' => CAP_ALLOW,
            'companydepartmentmanager' => CAP_ALLOW,
            'clientadministrator' => CAP_ALLOW
        ),
    ),

    'block/iomad_company_admin:licensemanagement_view' => array(

        'captype' => 'read',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'companymanager' => CAP_ALLOW,
            'companydepartmentmanager' => CAP_ALLOW,
            'clientadministrator' => CAP_ALLOW
        ),
    ),

    'block/iomad_company_admin:competencymanagement_view' => array(

        'captype' => 'read',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'companymanager' => CAP_ALLOW,
            'clientadministrator' => CAP_ALLOW
        ),
    ),

    'block/iomad_company_admin:assign_company_manager' => array(

        'captype' => 'read',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'companymanager' => CAP_ALLOW,
            'clientadministrator' => CAP_ALLOW
        ),
    ),

    'block/iomad_company_admin:assign_department_manager' => array(

        'captype' => 'read',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'companymanager' => CAP_ALLOW,
            'companydepartmentmanager' => CAP_ALLOW,
            'clientadministrator' => CAP_ALLOW
        ),
    ),

    'block/iomad_company_admin:assign_educator' => array(

        'captype' => 'read',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'companymanager' => CAP_ALLOW,
            'clientadministrator' => CAP_ALLOW
        ),
    ),

    'block/iomad_company_admin:assign_company_reporter' => array(

        'captype' => 'read',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'companymanager' => CAP_ALLOW,
            'clientadministrator' => CAP_ALLOW
        ),
    ),

    'block/iomad_company_admin:view_my_company_email' => array(

        'captype' => 'read',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'companymanager' => CAP_ALLOW,
            'clientadministrator' => CAP_ALLOW
        ),
    ),

    'block/iomad_company_admin:company_add' => array(

        'captype' => 'write',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'clientadministrator' => CAP_ALLOW
        ),
    ),

    'block/iomad_company_admin:company_add_child' => array(

        'captype' => 'write',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'clientadministrator' => CAP_ALLOW
        ),
    ),

    'block/iomad_company_admin:company_edit' => array(

        'captype' => 'write',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'companymanager' => CAP_ALLOW,
            'clientadministrator' => CAP_ALLOW
        ),
    ),

    'block/iomad_company_admin:company_edit_appearance' => array(

        'captype' => 'write',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'companymanager' => CAP_ALLOW,
            'clientadministrator' => CAP_ALLOW
        ),
    ),

    'block/iomad_company_admin:company_delete' => array(

        'captype' => 'write',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'clientadministrator' => CAP_ALLOW
        ),
    ),

    'block/iomad_company_admin:company_view' => array(

        'captype' => 'write',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'companymanager' => CAP_ALLOW,
            'clientadministrator' => CAP_ALLOW
        ),
    ),

    'block/iomad_company_admin:company_view_all' => array(

        'captype' => 'write',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'clientadministrator' => CAP_ALLOW,
            'clientreporter' => CAP_ALLOW
        ),
    ),

    'block/iomad_company_admin:company_user' => array(

        'captype' => 'write',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'companymanager' => CAP_ALLOW,
            'companydepartmentmanager' => CAP_ALLOW,
            'clientadministrator' => CAP_ALLOW
        ),
    ),

    'block/iomad_company_admin:company_manager' => array(

        'captype' => 'write',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'companymanager' => CAP_ALLOW,
            'companydepartmentmanager' => CAP_ALLOW,
            'clientadministrator' => CAP_ALLOW
        ),
    ),

    'block/iomad_company_admin:company_course' => array(

        'captype' => 'write',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'clientadministrator' => CAP_ALLOW
        ),
    ),

    'block/iomad_company_admin:company_course_unenrol' => array(

        'captype' => 'write',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'clientadministrator' => CAP_ALLOW
        ),
    ),

    'block/iomad_company_admin:createcourse' => array(

        'captype' => 'write',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'clientadministrator' => CAP_ALLOW
        ),
    ),

    'block/iomad_company_admin:deletecourses' => array(

        'captype' => 'write',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'clientadministrator' => CAP_ALLOW
        ),
    ),

    'block/iomad_company_admin:deleteallcourses' => array(

        'captype' => 'write',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'clientadministrator' => CAP_ALLOW
        ),
    ),

    'block/iomad_company_admin:destroycourses' => array(

        'captype' => 'write',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'clientadministrator' => CAP_ALLOW
        ),
    ),

    'block/iomad_company_admin:managecourses' => array(

        'captype' => 'write',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'clientadministrator' => CAP_ALLOW
        ),
    ),

    'block/iomad_company_admin:manageallcourses' => array(

        'captype' => 'write',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'clientadministrator' => CAP_ALLOW
        ),
    ),

    'block/iomad_company_admin:viewcourses' => array(

        'captype' => 'write',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'clientadministrator' => CAP_ALLOW
        ),
    ),

    'block/iomad_company_admin:viewallsharedcourses' => array(

        'captype' => 'write',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'clientadministrator' => CAP_ALLOW
        ),
    ),

    'block/iomad_company_admin:user_create' => array(

        'captype' => 'write',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'companymanager' => CAP_ALLOW,
            'companydepartmentmanager' => CAP_ALLOW,
            'clientadministrator' => CAP_ALLOW
        ),
    ),

    'block/iomad_company_admin:user_upload' => array(

        'captype' => 'write',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'companymanager' => CAP_ALLOW,
            'companydepartmentmanager' => CAP_ALLOW,
            'clientadministrator' => CAP_ALLOW
        ),
    ),

    'block/iomad_company_admin:company_course_users' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'companymanager' => CAP_ALLOW,
            'companydepartmentmanager' => CAP_ALLOW,
            'clientadministrator' => CAP_ALLOW
        ),
    ),

    'block/iomad_company_admin:company_license_users' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'companymanager' => CAP_ALLOW,
            'companydepartmentmanager' => CAP_ALLOW,
            'clientadministrator' => CAP_ALLOW
        ),
    ),

    'block/iomad_company_admin:editusers' => array(

        'captype' => 'write',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'companymanager' => CAP_ALLOW,
            'companydepartmentmanager' => CAP_ALLOW,
            'clientadministrator' => CAP_ALLOW
        ),
    ),

    'block/iomad_company_admin:edituserpassword' => array(

        'captype' => 'write',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'companymanager' => CAP_ALLOW,
            'companydepartmentmanager' => CAP_ALLOW,
            'clientadministrator' => CAP_ALLOW
        ),
    ),

    'block/iomad_company_admin:deleteuser' => array(

        'captype' => 'write',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'companymanager' => CAP_ALLOW,
            'companydepartmentmanager' => CAP_ALLOW,
            'clientadministrator' => CAP_ALLOW
        ),
    ),

    'block/iomad_company_admin:suspenduser' => array(

        'captype' => 'write',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'companymanager' => CAP_ALLOW,
            'companydepartmentmanager' => CAP_ALLOW,
            'clientadministrator' => CAP_ALLOW
        ),
    ),

    'block/iomad_company_admin:editmanagers' => array(

        'captype' => 'write',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'companymanager' => CAP_ALLOW,
            'clientadministrator' => CAP_ALLOW
        ),
    ),

    'block/iomad_company_admin:editallusers' => array(

        'captype' => 'write',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'clientadministrator' => CAP_ALLOW
        ),
    ),

    'block/iomad_company_admin:company_user_profiles' => array(

        'captype' => 'write',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'companymanager' => CAP_ALLOW,
            'clientadministrator' => CAP_ALLOW
        ),
    ),

    'block/iomad_company_admin:allcompany_user_profiles' => array(

        'captype' => 'write',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'clientadministrator' => CAP_ALLOW
        ),
    ),

    'block/iomad_company_admin:export_departments' => array(

        'captype' => 'write',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'clientadministrator' => CAP_ALLOW
        ),
    ),

    'block/iomad_company_admin:import_departments' => array(

        'captype' => 'write',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'clientadministrator' => CAP_ALLOW
        ),
    ),

    'block/iomad_company_admin:edit_all_departments' => array(

        'captype' => 'write',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'clientadministrator' => CAP_ALLOW
        ),
    ),

    'block/iomad_company_admin:edit_departments' => array(

        'captype' => 'write',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'companymanager' => CAP_ALLOW,
            'clientadministrator' => CAP_ALLOW
        ),
    ),

    'block/iomad_company_admin:assign_groups' => array(

        'captype' => 'write',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'companymanager' => CAP_ALLOW,
            'clientadministrator' => CAP_ALLOW
        ),
    ),

    'block/iomad_company_admin:edit_groups' => array(

        'captype' => 'write',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'companymanager' => CAP_ALLOW,
            'clientadministrator' => CAP_ALLOW
        ),
    ),

    'block/iomad_company_admin:edit_licenses' => array(

        'captype' => 'write',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'clientadministrator' => CAP_ALLOW
        ),
    ),

    'block/iomad_company_admin:split_my_licenses' => array(

        'captype' => 'write',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'clientadministrator' => CAP_ALLOW
        ),
    ),

    'block/iomad_company_admin:edit_my_licenses' => array(

        'captype' => 'write',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'clientadministrator' => CAP_ALLOW
        ),
    ),

    'block/iomad_company_admin:view_licenses' => array(

        'captype' => 'write',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'companymanager' => CAP_ALLOW,
            'companydepartmentmanager' => CAP_ALLOW,
            'clientadministrator' => CAP_ALLOW
        ),
    ),

    'block/iomad_company_admin:allocate_licenses' => array(

        'captype' => 'write',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'companymanager' => CAP_ALLOW,
            'companydepartmentmanager' => CAP_ALLOW,
            'clientadministrator' => CAP_ALLOW
        ),
    ),

    'block/iomad_company_admin:unallocate_licenses' => array(

        'captype' => 'write',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'companymanager' => CAP_ALLOW,
            'companydepartmentmanager' => CAP_ALLOW,
            'clientadministrator' => CAP_ALLOW
        ),
    ),

    'block/iomad_company_admin:classrooms' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'clientadministrator' => CAP_ALLOW
        ),
    ),
    'block/iomad_company_admin:classrooms_add' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'clientadministrator' => CAP_ALLOW
        ),
    ),
    'block/iomad_company_admin:classrooms_edit' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'clientadministrator' => CAP_ALLOW
        ),
    ),
    'block/iomad_company_admin:classrooms_delete' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'clientadministrator' => CAP_ALLOW
        ),
    ),
    'block/iomad_company_admin:viewsuspendedusers' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'companymanager' => CAP_ALLOW,
            'companydepartmentmanager' => CAP_ALLOW,
            'clientadministrator' => CAP_ALLOW
        ),
    ),
    'block/iomad_company_admin:suspendcompanies' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'clientadministrator' => CAP_ALLOW
        ),
    ),
    'block/iomad_company_admin:restrict_capabilities' => array(
                    'captype' => 'write',
                    'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'clientadministrator' => CAP_ALLOW
        ),
    ),
    'block/iomad_company_admin:competencyview' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'companymanager' => CAP_ALLOW,
            'clientadministrator' => CAP_ALLOW
        ),
    ),
    'block/iomad_company_admin:manageframeworks' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'companymanager' => CAP_ALLOW,
            'clientadministrator' => CAP_ALLOW
        ),
    ),
    'block/iomad_company_admin:templateview' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'companymanager' => CAP_ALLOW,
            'clientadministrator' => CAP_ALLOW
        ),
    ),
    'block/iomad_company_admin:company_framework' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'companymanager' => CAP_ALLOW,
            'clientadministrator' => CAP_ALLOW
        ),
    ),
    'block/iomad_company_admin:company_template' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'companymanager' => CAP_ALLOW,
            'clientadministrator' => CAP_ALLOW
        ),
    ),
    'block/iomad_company_admin:managetemplates' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'clientadministrator' => CAP_ALLOW
        ),
    ),
    'block/iomad_company_admin:company_edit_certificateinfo' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'companymanager' => CAP_ALLOW,
            'clientadministrator' => CAP_ALLOW
        ),
    ),
    'block/iomad_company_admin:canviewchildren' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'clientadministrator' => CAP_ALLOW
        ),
    ),
);

