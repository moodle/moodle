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
 * @copyright  2011 onwards E-Learn Design Limited
 * @author    Derick Turner
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 *
 * @param int $oldversion
 * @param object $block
 */
function xmldb_block_iomad_company_admin_upgrade($oldversion) {
    global $CFG, $DB;

    $result = true;
    $dbman = $DB->get_manager();

    if ($oldversion < 2014041200) {

        // Check if there is an instance of the company select block on the dashboard.
        if ($DB->get_record('block_instances', array('blockname' => 'iomad_company_selector',
                                                    'pagetypepattern' => 'local-iomad-dashboard-index'))) {
            $DB->delete_records('block_instances', array('blockname' => 'iomad_company_selector',
                                                        'pagetypepattern' => 'local-iomad-dashboard-index'));
        }

        // Iomad_company_admin savepoint reached.
        upgrade_block_savepoint(true, 2014041200, 'iomad_company_admin');
    }

    // add new role capability
    if ($oldversion < 2014041201) {
        $systemcontext = context_system::instance();
        $clientadministrator = $DB->get_record('role', array('shortname' => 'clientadministrator'), '*', MUST_EXIST);
        assign_capability(
            'block/iomad_company_admin:restrict_capabilities',
            CAP_ALLOW,
            $clientadministrator->id,
            $systemcontext->id
        );
    }

    // add new role capability
    if ($oldversion < 2015011900) {
        $systemcontext = context_system::instance();
        foreach (array('clientadministrator', 'companymanager', 'companydepartmentmanager') as $rolename) {
            $role = $DB->get_record('role', array('shortname' => $rolename), '*', MUST_EXIST);
            assign_capability(
                'block/iomad_company_admin:block/iomad_commerce:companymanagement_view',
                CAP_ALLOW,
                $role->id,
                $systemcontext->id
            );
            assign_capability(
                'block/iomad_company_admin:block/iomad_commerce:usermanagement_view',
                CAP_ALLOW,
                $role->id,
                $systemcontext->id
            );
            assign_capability(
                'block/iomad_company_admin:block/iomad_commerce:coursemanagement_view',
                CAP_ALLOW,
                $role->id,
                $systemcontext->id
            );
            assign_capability(
                'block/iomad_company_admin:block/iomad_commerce:licensemanagement_view',
                CAP_ALLOW,
                $role->id,
                $systemcontext->id
            );
        }
    }

    // add new role capability
    if ($oldversion < 2015012100) {
        $systemcontext = context_system::instance();
        foreach (array('clientadministrator', 'companymanager', 'companydepartmentmanager') as $rolename) {
            $role = $DB->get_record('role', array('shortname' => $rolename), '*', MUST_EXIST);
            assign_capability(
                'block/iomad_company_admin:block/iomad_company_admin:companymanagement_view',
                CAP_ALLOW,
                $role->id,
                $systemcontext->id
            );
            assign_capability(
                'block/iomad_company_admin:block/iomad_company_admin:usermanagement_view',
                CAP_ALLOW,
                $role->id,
                $systemcontext->id
            );
            assign_capability(
                'block/iomad_company_admin:block/iomad_company_admin:coursemanagement_view',
                CAP_ALLOW,
                $role->id,
                $systemcontext->id
            );
            assign_capability(
                'block/iomad_company_admin:block/iomad_company_admin:licensemanagement_view',
                CAP_ALLOW,
                $role->id,
                $systemcontext->id
            );
        }
    }

    // add new role capability
    if ($oldversion < 2017090308) {
        $systemcontext = context_system::instance();
        foreach (array('clientadministrator', 'companymanager', 'companydepartmentmanager') as $rolename) {
            if ($role = $DB->get_record('role', array('shortname' => $rolename), '*')) {
                assign_capability(
                    'block/iomad_company_admin:block/iomad_company_admin:edituserpassword',
                    CAP_ALLOW,
                    $role->id,
                    $systemcontext->id
                );
                assign_capability(
                    'block/iomad_company_admin:block/iomad_company_admin:deleteuser',
                    CAP_ALLOW,
                    $role->id,
                    $systemcontext->id
                );
                assign_capability(
                    'block/iomad_company_admin:block/iomad_company_admin:suspenduser',
                    CAP_ALLOW,
                    $role->id,
                    $systemcontext->id
                );
            }
        }

        // Iomad savepoint reached.
        upgrade_plugin_savepoint(true, 2017090308, 'block', 'iomad_company_admin');
    }


    // Fix company profile categories.
    if ($oldversion < 2017090309) {
        if ($companies = $DB->get_records('company')) {
            foreach ($companies as $company) {
                if ($compcat = $DB->get_record('user_info_category', array('name' => $company->shortname))) {
                    $company_profileid = $compcat->id;
                    $DB->update_record('company', $company);
                }
            }
        }

        // Iomad savepoint reached.
        upgrade_plugin_savepoint(true, 2017090309, 'block', 'iomad_company_admin');
    }

    // Remove Iomad Dashboard (stuff moves to core dashboard)
    if ($oldversion < 2018090600) {

        // Update default block instance of iomad_company_admin
        if ($instance = $DB->get_record('block_instances', ['blockname' => 'iomad_company_admin', 'pagetypepattern' => 'local-iomad-dashboard-index'])) {
            $instance->pagetypepattern = 'my-index';
            $DB->update_record('block_instances', $instance);
        }

        // Remove any remaining iomad dashboard instances
        $instances = $DB->get_records('block_instances', ['pagetypepattern' => 'local-iomad-dashboard-index']);
        foreach ($instances as $instance) {
            blocks_delete_instance($instance);
        }

        // Iomad savepoint reached.
        upgrade_plugin_savepoint(true, 2018090600, 'block', 'iomad_company_admin');
    }

    if ($oldversion < 2019032103) {

        // Convert all profile shortnames to remove spaces.
        if ($userprofilefields = $DB->get_records('user_info_field')) {
            foreach ($userprofilefields as $userprofilefield) {
                $userprofilefield->shortname = str_replace(" ", "", $userprofilefield->shortname);
                $DB->update_record('user_info_field', $userprofilefield);
            }
        }

        // Iomad savepoint reached.
        upgrade_plugin_savepoint(true, 2019032103, 'block', 'iomad_company_admin');
    }

    return true;
}
