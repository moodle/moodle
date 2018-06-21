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
 * @package    Block IOMAD Company Admin
 * @copyright  2017 onwards E-Learn Design Limited
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Define an Iomad service
$services = array(
    'iomadservice' => array(
        'functions' => array(
            'block_iomad_company_admin_create_companies',
            'block_iomad_company_admin_get_companies',
            'block_iomad_company_admin_edit_companies',
            'block_iomad_company_admin_get_departments',
            'block_iomad_company_admin_assign_users',
            'block_iomad_company_admin_move_users',
            'block_iomad_company_admin_unassign_users',
            'block_iomad_company_admin_assign_courses',
            'block_iomad_company_admin_unassign_courses',
            'block_iomad_company_admin_update_courses',
            'block_iomad_company_admin_get_course_info',
            'block_iomad_company_admin_get_license_info',
            'block_iomad_company_admin_create_licenses',
            'block_iomad_company_admin_edit_licenses',
            'block_iomad_company_admin_delete_licenses',
            'block_iomad_company_admin_allocate_licenses',
            'block_iomad_company_admin_unallocate_liceses',
            'block_iomad_company_admin_enrol_users',
        ),
        'requiredcapability' => '',
        'restrictusers' => 1,
        'enabled' => 1,
    )
);

// Define the web service funtions
$functions = array(
    'block_iomad_company_admin_create_companies' => array(
        'classname' => 'block_iomad_company_admin_external',
        'methodname' => 'create_companies',
        'classpath' => 'blocks/iomad_company_admin/externallib.php',
        'description' => 'Create new Iomad companies',
        'type' => 'write',
    ),
    'block_iomad_company_admin_get_companies' => array(
        'classname' => 'block_iomad_company_admin_external',
        'methodname' => 'get_companies',
        'classpath' => 'blocks/iomad_company_admin/externallib.php',
        'description' => 'Get all Iomad companies',
        'type' => 'read',
    ),
    'block_iomad_company_admin_edit_companies' => array(
        'classname' => 'block_iomad_company_admin_external',
        'methodname' => 'edit_companies',
        'classpath' => 'blocks/iomad_company_admin/externallib.php',
        'description' => 'Edit Iomad companies',
        'type' => 'write',
    ),
    'block_iomad_company_admin_get_departments' => array(
        'classname' => 'block_iomad_company_admin_external',
        'methodname' => 'get_departments',
        'classpath' => 'blocks/iomad_company_admin/externallib.php',
        'description' => 'Get all company departments',
        'type' => 'read',
    ),
    'block_iomad_company_admin_assign_users' => array(
        'classname' => 'block_iomad_company_admin_external',
        'methodname' => 'assign_users',
        'classpath' => 'blocks/iomad_company_admin/externallib.php',
        'description' => 'Assign users to a company',
        'type' => 'write',
    ),
    'block_iomad_company_admin_move_users' => array(
        'classname' => 'block_iomad_company_admin_external',
        'methodname' => 'move_users',
        'classpath' => 'blocks/iomad_company_admin/externallib.php',
        'description' => 'Move users between departments',
        'type' => 'write',
    ),
    'block_iomad_company_admin_unassign_users' => array(
        'classname' => 'block_iomad_company_admin_external',
        'methodname' => 'unassign_users',
        'classpath' => 'blocks/iomad_company_admin/externallib.php',
        'description' => 'Unassign users from a company',
        'type' => 'write',
    ),
    'block_iomad_company_admin_assign_courses' => array(
        'classname' => 'block_iomad_company_admin_external',
        'methodname' => 'assign_courses',
        'classpath' => 'blocks/iomad_company_admin/externallib.php',
        'description' => 'Assign a course to a company',
        'type' => 'write',
    ),
    'block_iomad_company_admin_unassign_courses' => array(
        'classname' => 'block_iomad_company_admin_external',
        'methodname' => 'unassign_courses',
        'classpath' => 'blocks/iomad_company_admin/externallib.php',
        'description' => 'Unassign a course from a company',
        'type' => 'write',
    ),
    'block_iomad_company_admin_update_courses' => array(
        'classname' => 'block_iomad_company_admin_external',
        'methodname' => 'update_courses',
        'classpath' => 'blocks/iomad_company_admin/externallib.php',
        'description' => 'Update Iomad course settings',
        'type' => 'write',
    ),
    'block_iomad_company_admin_get_course_info' => array(
        'classname' => 'block_iomad_company_admin_external',
        'methodname' => 'get_course_info',
        'classpath' => 'blocks/iomad_company_admin/externallib.php',
        'description' => 'Get Iomad course settings',
        'type' => 'write',
    ),
    'block_iomad_company_admin_get_license_info' => array(
        'classname' => 'block_iomad_company_admin_external',
        'methodname' => 'get_license_info',
        'classpath' => 'blocks/iomad_company_admin/externallib.php',
        'description' => 'Get company license information',
        'type' => 'write',
    ),
    'block_iomad_company_admin_create_licenses' => array(
        'classname' => 'block_iomad_company_admin_external',
        'methodname' => 'create_licenses',
        'classpath' => 'blocks/iomad_company_admin/externallib.php',
        'description' => 'Create company licenses',
        'type' => 'write',
    ),
    'block_iomad_company_admin_edit_licenses' => array(
        'classname' => 'block_iomad_company_admin_external',
        'methodname' => 'edit_licenses',
        'classpath' => 'blocks/iomad_company_admin/externallib.php',
        'description' => 'Edit company license settings',
        'type' => 'write',
    ),
    'block_iomad_company_admin_delete_licenses' => array(
        'classname' => 'block_iomad_company_admin_external',
        'methodname' => 'delete_licenses',
        'classpath' => 'blocks/iomad_company_admin/externallib.php',
        'description' => 'Delete company licenses',
        'type' => 'write',
    ),
    'block_iomad_company_admin_allocate_licenses' => array(
        'classname' => 'block_iomad_company_admin_external',
        'methodname' => 'allocate_licenses',
        'classpath' => 'blocks/iomad_company_admin/externallib.php',
        'description' => 'Allocate course licenses to a user',
        'type' => 'write',
    ),
    'block_iomad_company_admin_unallocate_liceses' => array(
        'classname' => 'block_iomad_company_admin_external',
        'methodname' => 'unallocate_licenses',
        'classpath' => 'blocks/iomad_company_admin/externallib.php',
        'description' => 'Remove course licenses from users',
        'type' => 'write',
    ),
    'block_iomad_company_admin_enrol_users' => array(
        'classname' => 'block_iomad_company_admin_external',
        'methodname' => 'enrol_users',
        'classpath' => 'blocks/iomad_company_admin/externallib.php',
        'description' => 'Assign users onto courses',
        'type' => 'write',
    ),
);
