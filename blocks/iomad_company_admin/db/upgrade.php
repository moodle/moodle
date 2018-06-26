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
            $role = $DB->get_record('role', array('shortname' => $rolename), '*', MUST_EXIST);
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

        // Iomad savepoint reached.
        upgrade_plugin_savepoint(true, 2017090308, 'block', 'iomad_company_admin');
    }


    // Fix company profile categories.
    if ($oldversion < 2017090305.2) {
        $DB->execute("update mdl_company c join mdl_user_info_category uic on c.shortname=uic.name set c.profileid = uic.id");

        // Iomad savepoint reached.
        upgrade_plugin_savepoint(true, 2017090305.2, 'block', 'iomad_company_admin');
    }

    return true;
}
