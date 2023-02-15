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
 * @package   local_iomad
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * As of the implementation of this block and the general navigation code
 * in Moodle 2.0 the body of immediate upgrade work for this block and
 * settings is done in core upgrade {@see lib/db/upgrade.php}
 *
 * There were several reasons that they were put there and not here, both becuase
 * the process for the two blocks was very similar and because the upgrade process
 * was complex due to us wanting to remvoe the outmoded blocks that this
 * block was going to replace.
 *
 * @global moodle_database $DB
 * @param int $oldversion
 * @param object $block
 */

defined('MOODLE_INTERNAL') || die();

function xmldb_local_iomad_upgrade($oldversion) {
    global $CFG, $DB;

    $result = true;
    $dbman = $DB->get_manager();

    if ($oldversion < 2011090600) {

        // Define table department to be created.
        $table = new xmldb_table('department');

        // Adding fields to table department.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL,
                           XMLDB_SEQUENCE, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('shortname', XMLDB_TYPE_CHAR, '32', null, XMLDB_NOTNULL, null, null);
        $table->add_field('company', XMLDB_TYPE_INTEGER, '20', XMLDB_UNSIGNED, XMLDB_NOTNULL,
                           null, '0');
        $table->add_field('parent', XMLDB_TYPE_INTEGER, '20', XMLDB_UNSIGNED, null, null, null);

        // Adding keys to table department.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for department.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }
        // Define table department_users to be created.
        $table = new xmldb_table('department_users');

        // Adding fields to table department_users.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED,
                           XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '20', XMLDB_UNSIGNED,
                           XMLDB_NOTNULL, null, null);
        $table->add_field('departmentid', XMLDB_TYPE_INTEGER, '20', XMLDB_UNSIGNED,
                           XMLDB_NOTNULL, null, null);

        // Adding keys to table department_users.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for department_users.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table department_courses to be created.
        $table = new xmldb_table('department_courses');

        // Adding fields to table department_courses.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL,
                           XMLDB_SEQUENCE, null);
        $table->add_field('courseid', XMLDB_TYPE_INTEGER, '20', XMLDB_UNSIGNED,
                           XMLDB_NOTNULL, null, null);
        $table->add_field('departmentid', XMLDB_TYPE_INTEGER, '20', XMLDB_UNSIGNED,
                           XMLDB_NOTNULL, null, null);

        // Adding keys to table department_courses.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for department_courses.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Iomad savepoint reached.
        upgrade_plugin_savepoint(true, 2011090600, 'local', 'iomad');
    }

    // Licensing added.
    if ($oldversion < 2011091500) {

        // Define table companylicense to be created.
        $table = new xmldb_table('companylicense');

        // Adding fields to table companylicense.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL,
                           XMLDB_SEQUENCE, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('allocation', XMLDB_TYPE_INTEGER, '20', XMLDB_UNSIGNED,
                           XMLDB_NOTNULL, null, '0');
        $table->add_field('validlength', XMLDB_TYPE_INTEGER, '20', XMLDB_UNSIGNED,
                           XMLDB_NOTNULL, null, '0');
        $table->add_field('expirydate', XMLDB_TYPE_INTEGER, '20', XMLDB_UNSIGNED,
                           XMLDB_NOTNULL, null, '0');
        $table->add_field('used', XMLDB_TYPE_INTEGER, '20', XMLDB_UNSIGNED,
                           XMLDB_NOTNULL, null, '0');
        $table->add_field('companyid', XMLDB_TYPE_INTEGER, '20', XMLDB_UNSIGNED, null, null, null);

        // Adding keys to table companylicense.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for companylicense.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table companylicense_users to be created.
        $table = new xmldb_table('companylicense_users');

        // Adding fields to table companylicense_users.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED,
                           XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('licenseid', XMLDB_TYPE_INTEGER, '20', XMLDB_UNSIGNED,
                           XMLDB_NOTNULL, null, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '20', XMLDB_UNSIGNED,
                           XMLDB_NOTNULL, null, null);

        // Adding keys to table companylicense_users.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for companylicense_users.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table companylicense_courses to be created.
        $table = new xmldb_table('companylicense_courses');

        // Adding fields to table companylicense_courses.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL,
                           XMLDB_SEQUENCE, null);
        $table->add_field('licenseid', XMLDB_TYPE_INTEGER, '20', XMLDB_UNSIGNED,
                           XMLDB_NOTNULL, null, null);
        $table->add_field('courseid', XMLDB_TYPE_INTEGER, '20', XMLDB_UNSIGNED,
                           XMLDB_NOTNULL, null, null);

        // Adding keys to table companylicense_courses.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for companylicense_courses.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Iomad savepoint reached.
        upgrade_plugin_savepoint(true, 2011091500, 'local', 'iomad');
    }

    if ($oldversion < 2011092300) {

        // Define field id to be added to companylicense_users.
        $table = new xmldb_table('companylicense_users');
        $field = new xmldb_field('isusing', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED,
                                  XMLDB_NOTNULL, null, '0', 'userid');

        // Conditionally launch add field id.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Iomad savepoint reached.
        upgrade_plugin_savepoint(true, 2011092300, 'local', 'iomad');
    }

    if ($oldversion < 2011092600) {

        // Define field timecompleted to be added to companylicense_users.
        $table = new xmldb_table('companylicense_users');
        $field = new xmldb_field('timecompleted', XMLDB_TYPE_INTEGER, '20', XMLDB_UNSIGNED,
                                  null, null, null, 'isusing');

        // Conditionally launch add field timecompleted.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field score to be added to companylicense_users.
        $table = new xmldb_table('companylicense_users');
        $field = new xmldb_field('score', XMLDB_TYPE_NUMBER, '10, 5', null, null, null,
                                  null, 'timecompleted');

        // Conditionally launch add field score.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field result to be added to companylicense_users.
        $table = new xmldb_table('companylicense_users');
        $field = new xmldb_field('result', XMLDB_TYPE_TEXT, 'small', null, null, null,
                                  null, 'score');

        // Conditionally launch add field result.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Iomad savepoint reached.
        upgrade_plugin_savepoint(true, 2011092600, 'local', 'iomad');

    }

    if ($oldversion < 2011103000) {

        // Define table company_course_groups to be created.
        $table = new xmldb_table('company_course_groups');

        // Adding fields to table company_course_groups.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL,
                           XMLDB_SEQUENCE, null);
        $table->add_field('companyid', XMLDB_TYPE_INTEGER, '20', XMLDB_UNSIGNED,
                           XMLDB_NOTNULL, null, null);
        $table->add_field('groupid', XMLDB_TYPE_INTEGER, '20', XMLDB_UNSIGNED,
                           XMLDB_NOTNULL, null, null);
        $table->add_field('courseid', XMLDB_TYPE_INTEGER, '20', XMLDB_UNSIGNED,
                           XMLDB_NOTNULL, null, null);

        // Adding keys to table company_course_groups.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for company_course_groups.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table iomad_courses to be created.
        $table = new xmldb_table('iomad_courses');

        // Adding fields to table iomad_courses.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL,
                           XMLDB_SEQUENCE, null);
        $table->add_field('courseid', XMLDB_TYPE_INTEGER, '20', XMLDB_UNSIGNED, XMLDB_NOTNULL,
                           null, null);
        $table->add_field('licensed', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL,
                           null, '0');
        $table->add_field('shared', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL,
                           null, '0');

        // Adding keys to table iomad_courses.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for iomad_courses.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Iomad savepoint reached.
        upgrade_plugin_savepoint(true, 2011103000, 'local', 'iomad');
    }

    if ($oldversion < 2011111000) {

        // Define table iomad_courses to be created.
        $table = new xmldb_table('iomad_courses');

        // Adding fields to table iomad_courses.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL,
                           XMLDB_SEQUENCE, null);
        $table->add_field('courseid', XMLDB_TYPE_INTEGER, '20', XMLDB_UNSIGNED,
                           XMLDB_NOTNULL, null, null);
        $table->add_field('licensed', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, null, null, '0');
        $table->add_field('shared', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, null, null, '0');

        // Adding keys to table iomad_courses.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for iomad_courses.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Iomad savepoint reached.
        upgrade_plugin_savepoint(true, 2011111000, 'local', 'iomad');
    }

    if ($oldversion < 2011111401) {

        // Define table classroom to be created.
        $table = new xmldb_table('classroom');

        // Adding fields to table classroom.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL,
                           XMLDB_SEQUENCE, null);
        $table->add_field('companyid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED,
                           XMLDB_NOTNULL, null, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->add_field('address', XMLDB_TYPE_CHAR, '70', null, null, null, null);
        $table->add_field('city', XMLDB_TYPE_CHAR, '120', null, null, null, null);
        $table->add_field('country', XMLDB_TYPE_CHAR, '2', null, null, null, null);
        $table->add_field('postcode', XMLDB_TYPE_CHAR, '20', null, null, null, null);
        $table->add_field('capacity', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED,
                           XMLDB_NOTNULL, null, null);

        // Adding keys to table classroom.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for classroom.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }
        upgrade_plugin_savepoint(true, 2011111401, 'local', 'iomad');
    }

    if ($oldversion < 2011111800) {

        // Define field validlength to be added to iomad_courses.
        $table = new xmldb_table('iomad_courses');
        $field = new xmldb_field('validlength', XMLDB_TYPE_INTEGER, '20', XMLDB_UNSIGNED,
                                  null, null, '0', 'shared');

        // Conditionally launch add field validlength.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Iomad savepoint reached.
        upgrade_plugin_savepoint(true, 2011111800, 'local', 'iomad');
    }

    if ($oldversion < 2011111801) {

        // Define field warnexpire to be added to iomad_courses.
        $table = new xmldb_table('iomad_courses');
        $field = new xmldb_field('warnexpire', XMLDB_TYPE_INTEGER, '20', XMLDB_UNSIGNED,
                                  XMLDB_NOTNULL, null, '0', 'validlength');

        // Conditionally launch add field warnexpire.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field warncompletion to be added to iomad_courses.
        $table = new xmldb_table('iomad_courses');
        $field = new xmldb_field('warncompletion', XMLDB_TYPE_INTEGER, '20', XMLDB_UNSIGNED,
                                  XMLDB_NOTNULL, null, '0', 'warnexpire');

        // Conditionally launch add field warncompletion.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Iomad savepoint reached.
        upgrade_plugin_savepoint(true, 2011111801, 'local', 'iomad');

    }

    if ($oldversion < 2011112000) {

        // Define field category to be added to company.
        $table = new xmldb_table('company');
        $field = new xmldb_field('category', XMLDB_TYPE_INTEGER, '20',
                                  XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'theme');

        // Conditionally launch add field category.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Iomad savepoint reached.
        upgrade_plugin_savepoint(true, 2011112000, 'local', 'iomad');
    }

    if ($oldversion < 2012012500) {

        // Define table company_course_groups to be created.
        // ADDED AGAIN DUE TO git branching timelines.
        $table = new xmldb_table('company_course_groups');

        // Adding fields to table company_course_groups.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED,
                           XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('companyid', XMLDB_TYPE_INTEGER, '20',
                           XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('groupid', XMLDB_TYPE_INTEGER, '20',
                           XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('courseid', XMLDB_TYPE_INTEGER, '20',
                           XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);

        // Adding keys to table company_course_groups.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for company_course_groups.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table company_shared_courses to be created.
        $table = new xmldb_table('company_shared_courses');

        // Adding fields to table company_shared_courses.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED,
                          XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('companyid', XMLDB_TYPE_INTEGER, '20',
                          XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('courseid', XMLDB_TYPE_INTEGER, '20',
                          XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);

        // Adding keys to table company_shared_courses.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for company_shared_courses.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Iomad savepoint reached.
        upgrade_plugin_savepoint(true, 2012012500, 'local', 'iomad');
    }

    if ($oldversion < 2012051500) {

        // Change the role permissions for company and create the department manager role.
            $systemcontext = context_system::instance();

        // Create the Company Manager role.
        if (!$companymanager = $DB->get_record('role', array('shortname' => 'companymanager'))) {
            $companymanagerid = create_role('Company Manager', 'companymanager',
            '(Iomad) Manages individual companies - can upload users etc.');
        } else {
            $companymanagerid = $companymanager->id;
        }

        // If not done already, allow assignment at system context.
        $levels = get_role_contextlevels( $companymanagerid );
        if (empty($levels)) {
            $level = null;
            $level->roleid = $companymanagerid;
            $level->contextlevel = CONTEXT_SYSTEM;
            $DB->insert_record( 'role_context_levels', $level );
        }

        // Create new Company Department Manager role.
        if (!$companydepartmentmanager = $DB->get_record('role',
                                         array('shortname' => 'companydepartmentmanager'))) {
            $companydepartmentmanagerid = create_role('Company Department Manager',
            'companydepartmentmanager',
            '(Iomad) Manages departments within companies - can upload users etc.' );
        } else {
            $companydepartmentmanagerid = $companydepartmentmanager->id;
        }

        // If not done already, allow assignment at system context.
        $levels = get_role_contextlevels( $companydepartmentmanagerid );
        if (empty($levels)) {
            $level = null;
            $level->roleid = $companydepartmentmanagerid;
            $level->contextlevel = CONTEXT_SYSTEM;
            $DB->insert_record( 'role_context_levels', $level );
        }

        $companydepartmentmanagercaps = array('block/iomad_reports:view',
            'block/iomad_onlineusers:viewlist',
            'block/iomad_link:view',
            'block/iomad_company_admin:view_licenses',
            'block/iomad_company_admin:view',
            'block/iomad_company_admin:user_upload',
            'block/iomad_company_admin:user_create',
            'block/iomad_company_admin:editusers',
            'block/iomad_company_admin:edit_departments',
            'block/iomad_company_admin:company_view',
            'block/iomad_company_admin:company_course_users',
            'block/iomad_company_admin:assign_department_manager',
            'block/iomad_company_admin:company_manager',
            'block/iomad_company_admin:allocate_licenses',
        );

        if ($DB->get_records('role_capabilities', array('roleid' => $companydepartmentmanagerid))) {
            $DB->delete_records('role_capabilities', array('roleid' => $companydepartmentmanagerid));
        }
        foreach ($companydepartmentmanagercaps as $cap) {
            assign_capability( $cap, CAP_ALLOW, $companydepartmentmanagerid, $systemcontext->id );
        }

        $companymanagercaps = array(
            'block/iomad_company_admin:assign_company_manager',
            'block/iomad_company_admin:assign_department_manager',
            'block/iomad_onlineusers:viewlist',
            'block/iomad_link:view',
            'block/iomad_company_admin:view_licenses',
            'block/iomad_company_admin:view',
            'block/iomad_company_admin:user_upload',
            'block/iomad_company_admin:user_create',
            'block/iomad_company_admin:editusers',
            'block/iomad_company_admin:edit_departments',
            'block/iomad_company_admin:company_view',
            'block/iomad_company_admin:company_course_users',
            'block/iomad_company_admin:assign_department_manager',
            'block/iomad_company_admin:allocate_licenses',
            'block/iomad_company_admin:assign_company_manager',
            'block/iomad_company_admin:classrooms',
            'block/iomad_company_admin:classrooms_delete',
            'block/iomad_company_admin:classrooms_edit',
            'block/iomad_company_admin:company_edit',
            'block/iomad_company_admin:company_course_unenrol',
            'block/iomad_company_admin:company_manager',
            'block/iomad_company_admin:company_user_profiles',
            'block/iomad_company_admin:createcourse',

        );

        if ($DB->get_records('role_capabilities', array('roleid' => $companymanagerid))) {
            $DB->delete_records('role_capabilities', array('roleid' => $companymanagerid));
        }

        foreach ($companymanagercaps as $cap) {
            assign_capability( $cap, CAP_ALLOW, $companymanagerid, $systemcontext->id );
        }

        //  Deal with the database.
                // Define field id to be added to companymanager.
        $table = new xmldb_table('companymanager');
        $field = new xmldb_field('departmentmanager', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED,
                                  XMLDB_NOTNULL, null, '0', 'userid');

        // Conditionally launch add field departmentmanager.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Conditionally launch add field id.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $DB->set_field('companymanager', 'departmentmanager', 0);

        // Iomad savepoint reached.
        upgrade_plugin_savepoint(true, 2012051500, 'local', 'iomad');
    }

    if ($oldversion < 2012052200) {

        // Define table company_created_courses to be created.
        $table = new xmldb_table('company_created_courses');

        // Adding fields to table company_created_courses.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL,
                          XMLDB_SEQUENCE, null);
        $table->add_field('companyid', XMLDB_TYPE_INTEGER, '20', XMLDB_UNSIGNED,
                          XMLDB_NOTNULL, null, null);
        $table->add_field('courseid', XMLDB_TYPE_INTEGER, '20',
                          XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);

        // Adding keys to table company_created_courses.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for company_created_courses.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Change the role permissions for company and create the department manager role..
            $systemcontext = context_system::instance();

        // Create the Company Course Editor.
        if (!$companycourseeditor = $DB->get_record('role',
                                                    array('shortname' => 'companycourseeditor'))) {
            $companycourseeditorid = create_role('Company Course Editor', 'companycourseeditor',
            '(Iomad) Teacher style role for Company manager provided to them when they create their own course.');
        } else {
            $companycourseeditorid = $companycourseeditor->id;
        }

        // If not done already, allow assignment at system context.
        $levels = get_role_contextlevels( $companycourseeditorid );
        if (empty($levels)) {
            $level = null;
            $level->roleid = $companycourseeditorid;
            $level->contextlevel = CONTEXT_COURSE;
            $DB->insert_record( 'role_context_levels', $level );
        }

        // Create new Company Course Non Editor role.
        if (!$companycoursenoneditor = $DB->get_record('role',
                                            array('shortname' => 'companycoursenoneditor'))) {
            $companycoursenoneditorid = create_role('Company Course Non Editor',
             'companycoursenoneditor',
            '(Iomad) Non editing teacher style role form Company and department managers' );
        } else {
            $companycoursenoneditorid = $companycoursenoneditor->id;
        }

        // If not done already, allow assignment at system context.
        $levels = get_role_contextlevels( $companycoursenoneditorid );
        if (empty($levels)) {
            $level = null;
            $level->roleid = $companycoursenoneditorid;
            $level->contextlevel = CONTEXT_COURSE;
            $DB->insert_record( 'role_context_levels', $level );
        }

        if ($DB->get_records('role_capabilities', array('roleid' => $companycourseeditorid))) {
            $DB->delete_records('role_capabilities', array('roleid' => $companycourseeditorid));
        }

        $companycourseeditorcaps = array('block/side_bar_block:editblock',
            'block/side_bar_block:viewblock',
            'booktool/importhtml:import',
            'booktool/print:print',
            'enrol/authorize:manage',
            'enrol/license:manage',
            'enrol/license:unenrol',
            'enrol/manual:enrol',
            'enrol/manual:unenrol',
            'gradereport/grader:view',
            'gradereport/overview:view',
            'gradereport/user:view',
            'mod/assignment:exportownsubmission',
            'mod/assignment:grade',
            'mod/assignment:view',
            'mod/book:edit',
            'mod/book:read',
            'mod/book:viewhiddenchapters',
            'mod/certificate:manage',
            'mod/certificate:view',
            'mod/choice:choose',
            'mod/choice:deleteresponses',
            'mod/choice:downloadresponses',
            'mod/choice:readresponses',
            'mod/courseclassroom:grade',
            'mod/courseclassroom:invite',
            'mod/courseclassroom:viewattendees',
            'mod/forum:addnews',
            'mod/forum:addquestion',
            'mod/forum:createattachment',
            'mod/forum:deleteanypost',
            'mod/forum:deleteownpost',
            'mod/forum:editanypost',
            'mod/forum:exportdiscussion',
            'mod/forum:exportownpost',
            'mod/forum:exportpost',
            'mod/forum:managesubscriptions',
            'mod/forum:movediscussions',
            'mod/forum:postwithoutthrottling',
            'mod/forum:rate',
            'mod/forum:replynews',
            'mod/forum:replypost',
            'mod/forum:splitdiscussions',
            'mod/forum:startdiscussion',
            'mod/forum:viewallratings',
            'mod/forum:viewanyrating',
            'mod/forum:viewdiscussion',
            'mod/forum:viewhiddentimedposts',
            'mod/forum:viewqandawithoutposting',
            'mod/forum:viewrating',
            'mod/forum:viewsubscribers',
            'mod/page:view',
            'mod/resource:view',
            'mod/scorm:deleteresponses',
            'mod/scorm:savetrack',
            'mod/scorm:viewreport',
            'mod/scorm:viewscores',
            'mod/url:view',
            'moodle/block:edit',
            'moodle/block:view',
            'moodle/calendar:manageentries',
            'moodle/calendar:managegroupentries',
            'moodle/calendar:manageownentries',
            'moodle/course:activityvisibility',
            'moodle/course:changefullname',
            'moodle/course:changesummary',
            'moodle/course:manageactivities',
            'moodle/course:managefiles',
            'moodle/course:managegroups',
            'moodle/course:markcomplete',
            'moodle/course:reset',
            'moodle/course:sectionvisibility',
            'moodle/course:setcurrentsection',
            'moodle/course:update',
            'moodle/course:viewhiddenactivities',
            'moodle/course:viewhiddensections',
            'moodle/course:viewparticipants',
            'moodle/grade:edit',
            'moodle/grade:hide',
            'moodle/grade:lock',
            'moodle/grade:manage',
            'moodle/grade:managegradingforms',
            'moodle/grade:manageletters',
            'moodle/grade:manageoutcomes',
            'moodle/grade:unlock',
            'moodle/grade:viewall',
            'moodle/grade:viewhidden',
            'moodle/notes:manage',
            'moodle/notes:view',
            'moodle/rating:rate',
            'moodle/rating:view',
            'moodle/rating:viewall',
            'moodle/rating:viewany',
            'moodle/role:switchroles',
            'moodle/site:accessallgroups',
            'moodle/site:manageblocks',
            'moodle/site:trustcontent',
            'moodle/site:viewfullnames',
            'moodle/site:viewreports',
            'moodle/site:viewuseridentity',
            'moodle/user:viewdetails',
            'report/courseoverview:view',
            'report/log:view',
            'report/log:viewtoday',
            'report/loglive:view',
            'report/outline:view',
            'report/participation:view',
            'report/progress:view');

        foreach ($companycourseeditorcaps as $rolecapability) {
            // Assign_capability will update rather than insert if capability exists.
            assign_capability($rolecapability, CAP_ALLOW, $companycourseeditorid,
                              $systemcontext->id);
        }

        if ($DB->get_records('role_capabilities', array('roleid' => $companycoursenoneditorid))) {
            $DB->delete_records('role_capabilities', array('roleid' => $companycoursenoneditorid));
        }

        $companycoursenoneditorcaps = array('block/side_bar_block:viewblock',
            'gradereport/grader:view',
            'gradereport/user:view',
            'mod/assignment:view',
            'mod/book:read',
            'mod/certificate:manage',
            'mod/certificate:view',
            'mod/choice:readresponses',
            'mod/feedback:view',
            'mod/forum:addquestion',
            'mod/forum:createattachment',
            'mod/forum:deleteownpost',
            'mod/forum:replypost',
            'mod/forum:startdiscussion',
            'mod/forum:viewdiscussion',
            'mod/forum:viewqandawithoutposting',
            'mod/page:view',
            'mod/quiz:attempt',
            'mod/quiz:view',
            'mod/resource:view',
            'mod/survey:participate',
            'moodle/block:view',
            'moodle/grade:viewall',
            'moodle/site:viewfullnames',
            'moodle/site:viewuseridentity');

        foreach ($companycoursenoneditorcaps as $rolecapability) {
            // Assign_capability will update rather than insert if capability exists.
            assign_capability($rolecapability, CAP_ALLOW, $companycoursenoneditorid,
                              $systemcontext->id);
        }

        // Deal with role assignments.
        // Get the list of company courses.
        $companycourses = $DB->get_records('companycourse');
        // Get the managers.
        foreach ($companycourses as $companycourse) {
            $companymanagers = $DB->get_records('companymanager',
                                                 array('companyid' => $companycourse->companyid));
            if ($DB->record_exists('course', array('id' => $companycourse->courseid))) {
                if ($DB->record_exists('scorm', array('course' => $companycourse->courseid))) {
                    // This is a scorm course so only noneditor role to be applied.
                    foreach ($companymanagers as $companymanager) {
                        if ($user = $DB->get_record('user', array('id' => $companymanager->userid,
                                                                  'deleted' => 0))) {
                            company_user::enrol($user, array($companycourse->courseid),
                                                             $companycourse->companyid,
                                                             $companycoursenoneditorid);
                        }
                    }
                } else {
                    // Add it to the company created courses.
                    $DB->insert_record('company_created_courses',
                                       array('companyid' => $companycourse->companyid,
                                       'courseid' => $companycourse->courseid));
                    // Set up the manager roles.
                    foreach ($companymanagers as $companymanager) {
                        if ($user = $DB->get_record('user',
                            array('id' => $companymanager->userid, 'deleted' => 0))) {
                            if ($companymanager->departmentmanager) {
                                // Lowly department manager, no more than that.
                                company_user::enrol($user, array($companycourse->courseid),
                                $companycourse->companyid, $companycoursenoneditorid);
                            } else {
                                company_user::enrol($user, array($companycourse->courseid),
                                $companycourse->companyid, $companycourseeditorid);
                            }
                        }
                    }
                }
            }
        }

        // Restrict the default modules.
        $allowedmodules = '1,3,5,7,10,12,14,15,17,20,21,22';
        set_config('restrictbydefault', 1);
        set_config('restrictmodulesfor', 'all');
        set_config('defaultallowedmodules', $allowedmodules);
        // Restrict the modules for every course.
        $sitecourses = $DB->get_records_select('course', "id != ".SITEID);
        foreach ($sitecourses as $sitecourse) {
            foreach (explode(',', $allowedmodules) as $module) {
                $DB->insert_record('course_allowed_modules', array('course' => $sitecourse->id,
                'module' => $module));
                $DB->set_field('course', 'restrictmodules', '1', array('id' => $sitecourse->id));
            }
        }

        // Iomad savepoint reached.
        upgrade_plugin_savepoint(true, 2012052200, 'local', 'iomad');
    }

    if ($oldversion < 2013050100) {

        // Define table companyusers to be created.
        $table = new xmldb_table('companyusers');

        // Adding fields to table companyusers.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('companyid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('usserid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table companyusers.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for companyusers.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Need to deal with current company allocations.
        /*if ($companyfield = $DB->get_record('user_info_field', array('shortname' => 'company'))) {
            if ($companyusers = $DB->get_records('user_info_data', array('fieldid' => $companyfield->id))) {
                foreach($companyusers as $companyuser) {
          */

        // Iomad savepoint reached.
        upgrade_plugin_savepoint(true, 2013050100, 'local', 'iomad');
    }

    if ($oldversion < 2014012400) {

        $systemcontext = context_system::instance();

        // Get the Company Manager role.
        if ($companymanager = $DB->get_record( 'role', array( 'shortname' => 'companymanager') )) {
            $companymanagerid = $companymanager->id;
            $companymanagercaps = array(
                'block/iomad_reports:view',
                'local_report_attendance:view',
                'local_report_completion:view',
                'local_report_users:view',
                'local_report_scorm_overview:view',
            );

            foreach ($companymanagercaps as $cap) {
                assign_capability( $cap, CAP_ALLOW, $companymanagerid, $systemcontext->id );
            }
        }

        // Get the Company Department Manager role.
        if ($companydepartmentmanager = $DB->get_record('role',
                                         array( 'shortname' => 'companydepartmentmanager'))) {
            $companydepartmentmanagerid = $companydepartmentmanager->id;
            $companydepartmentmanagercaps = array(
                'block/iomad_reports:view',
                'local_report_attendance:view',
                'local_report_completion:view',
                'local_report_users:view',
                'local_report_scorm_overview:view',
            );

            foreach ($companydepartmentmanagercaps as $cap) {
                assign_capability( $cap, CAP_ALLOW, $companydepartmentmanagerid, $systemcontext->id );
            }
        }

        // Get the Client Administrator role.
        if ($clientadministrator = $DB->get_record('role',
                                                     array('shortname' => 'clientadministrator'))) {
            $clientadministratorid = $clientadministrator->id;
            $clientadministratorcaps = array(
                'block/iomad_reports:view',
                'local_report_attendance:view',
                'local_report_completion:view',
                'local_report_users:view',
                'local_report_scorm_overview:view',
            );

            foreach ($clientadministratorcaps as $cap) {
                assign_capability( $cap, CAP_ALLOW, $clientadministratorid, $systemcontext->id );
            }
        }
    }

    if ($oldversion < 2014022600) {

        // Change the site to force user allowed themes.
        set_config('allowuserthemes', 1);
    }

    if ($oldversion < 2014052700) {

        // Define field suspended to be added to company_users.
        $table = new xmldb_table('company_users');
        $field = new xmldb_field('suspended', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'departmentid');

        // Conditionally launch add field suspended.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field suspended to be added to company.
        $table = new xmldb_table('company');
        $field = new xmldb_field('suspended', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'profileid');

        // Conditionally launch add field suspended.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Iomad savepoint reached.
        upgrade_plugin_savepoint(true, 2014052700, 'local', 'iomad');
    }

    if ($oldversion < 2014052702) {

        // Define new table company_role_restriction
        $table = new xmldb_table('company_role_restriction');

        // Adding fields to table department.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL,
                           XMLDB_SEQUENCE, null);
        $table->add_field('roleid', XMLDB_TYPE_INTEGER, '11', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('companyid', XMLDB_TYPE_INTEGER, '11', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('capability',  XMLDB_TYPE_CHAR, '255', null, null, null, null);

        // Adding keys to table department.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('company_roleidcompanyid', XMLDB_KEY_UNIQUE, array('roleid', 'companyid', 'capability'));

        // Conditionally launch create table for company_role_restriction.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }
    }

    if ($oldversion < 2014120400) {

        // Define field licensecourseid to be added to companylicense_users.
        $table = new xmldb_table('companylicense_users');
        $field = new xmldb_field('licensecourseid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'userid');

        // Conditionally launch add field licensecourseid.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Iomad savepoint reached.
        upgrade_plugin_savepoint(true, 2014120400, 'local', 'iomad');
    }

    if ($oldversion < 2014121900) {

        // Deal with licenseses which have already been allocated.
        $licenseusers = $DB->get_records('companylicense_users', array('licensecourseid' => 0));
        foreach ($licenseusers as $licenseuser) {
            if ($licenseuser->timecompleted != null) {
                continue;
            }
            // Are they using the license?
            if ($licenseuser->isusing == 1) {
                // Get the course.
                $enrolrecords = $DB->get_records_sql("SELECT e.courseid,ue.userid
                                                    FROM {enrol} e JOIN {user_enrolments} ue
                                                    ON e.id=ue.enrolid
                                                    WHERE userid = :userid
                                                    AND courseid IN
                                                     (SELECT courseid FROM {companylicense_courses}
                                                      WHERE licenseid = :licenseid)",
                                                    array('userid' => $licenseuser->userid,
                                                          'licenseid' => $licenseuser->licenseid));
                // Do we have more than one record?
                if (count($enrolrecords > 1 )) {
                    foreach ($enrolrecords as $enrolrecord) {
                        // Check if we already have a record for this course.
                        if ($DB->get_record('companylicense_users', array('userid' => $licenseuser->userid,
                                                                          'licenseid' => $licenseuser->licenseid,
                                                                          'licensecourseid' => $enrolrecord->courseid))) {
                            continue;
                        } else {
                            $licenseuser->licensecourseid = $enrolrecord->courseid;
                            $DB->update_record('companylicense_users', $licenseuser);
                        }
                    }
                } else {
                    list($enrolcourseid, $enroluserid) = current($enrolrecords);
                    $licenseuser->licensecourseid = $enrolcourseid;
                    $DB->update_record('companylicense_users', $licenseuser);
                }
            } else {
                // Get the courses.
                $licensecourses = $DB->get_records('companylicense_courses', array('licenseid' => $licenseuser->licenseid));
                if (count($licensecourses) == 1) {
                    // Only one course so add it.
                    $licensecourse = array_pop($licensecourses);
                    $licenseuser->licensecourseid = $licensecourse->id;
                    $DB->update_record('companylicense_users', $licenseuser);
                } else {
                    //  Dont know which course to assign so we are going to remove the record as its not being used.
                    $DB->delete_records('companylicense_users', array('id' => $licenseuser->id));
                }
            }
        }
        //  Update the used counts for each license.
        $licenses = $DB->get_records('companylicense');
        foreach ($licenses as $license) {
            $licensecount = $DB->count_records('companylicense_users', array('licenseid' => $license->id));
            $license->used = $licensecount;
            $DB->update_record('companylicense', $license);
        }

        // Iomad savepoint reached.
        upgrade_plugin_savepoint(true, 2014121900, 'local', 'iomad');
    }

    if ($oldversion < 2015020800) {

        // Define table company_domains to be created.
        $table = new xmldb_table('company_domains');

        // Adding fields to table company_domains.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('companyid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('domain', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);

        // Adding keys to table company_domains.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for company_domains.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Iomad savepoint reached.
        upgrade_plugin_savepoint(true, 2015020800, 'local', 'iomad');
    }

    if ($oldversion < 2015040700) {

        // Define field issuedate to be added to companylicense_users.
        $table = new xmldb_table('companylicense_users');
        $field = new xmldb_field('issuedate', XMLDB_TYPE_INTEGER, '20', null, null, null, '0', 'licensecourseid');

        // Conditionally launch add field issuedate.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Iomad savepoint reached.
        upgrade_plugin_savepoint(true, 2015040700, 'local', 'iomad');
    }

    if ($oldversion < 2015083100) {

        // Define field customcss to be added to company.
        $table = new xmldb_table('company');
        $field = new xmldb_field('customcss', XMLDB_TYPE_TEXT, null, null, null, null, null, 'suspended');

        // Conditionally launch add field customcss.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Iomad savepoint reached.
        upgrade_plugin_savepoint(true, 2015083100, 'local', 'iomad');
    }

    if ($oldversion < 2015083101) {

        // Define field notifyperiod to be added to iomad_courses.
        $table = new xmldb_table('iomad_courses');
        $field = new xmldb_field('notifyperiod', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'warncompletion');

        // Conditionally launch add field notifyperiod.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Iomad savepoint reached.
        upgrade_plugin_savepoint(true, 2015083101, 'local', 'iomad');
    }

    if ($oldversion < 2016083100) {

        // Define table company_comp_frameworks to be created.
        $table = new xmldb_table('company_comp_frameworks');

        // Adding fields to table company_comp_frameworks.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('companyid', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table company_comp_frameworks.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for company_comp_frameworks.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table company_comp_templates to be created.
        $table = new xmldb_table('company_comp_templates');

        // Adding fields to table company_comp_templates.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('companyid', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
        $table->add_field('templateid', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table company_comp_templates.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for company_comp_templates.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table iomad_templates to be created.
        $table = new xmldb_table('iomad_templates');

        // Adding fields to table iomad_templates.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('templateid', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
        $table->add_field('shared', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0');

        // Adding keys to table iomad_templates.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for iomad_templates.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table iomad_frameworks to be created.
        $table = new xmldb_table('iomad_frameworks');

        // Adding fields to table iomad_frameworks.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('frameworkid', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
        $table->add_field('shared', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0');

        // Adding keys to table iomad_frameworks.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for iomad_frameworks.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table company_shared_templates to be created.
        $table = new xmldb_table('company_shared_templates');

        // Adding fields to table company_shared_templates.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('companyid', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
        $table->add_field('templateid', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table company_shared_templates.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for company_shared_templates.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table company_shared_frameworks to be created.
        $table = new xmldb_table('company_shared_frameworks');

        // Adding fields to table company_shared_frameworks.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('companyid', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
        $table->add_field('frameworkid', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table company_shared_frameworks.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for company_shared_frameworks.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Iomad savepoint reached.
        upgrade_plugin_savepoint(true, 2016083100, 'local', 'iomad');
    }

    if ($oldversion < 2016090502) {

        // Define field frameworkid to be added to company_comp_frameworks.
        $table = new xmldb_table('company_comp_frameworks');
        $field = new xmldb_field('frameworkid', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null, 'companyid');

        // Conditionally launch add field frameworkid.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Iomad savepoint reached.
        upgrade_plugin_savepoint(true, 2016090502, 'local', 'iomad');
    }

    if ($oldversion < 2016090503) {

        // Deal with new competencies based capabilities.
        $companydepartmentmanagercaps = array(
            'moodle/competency:plancomment',
            'moodle/competency:planmanage',
            'moodle/competency:planmanageowndraft',
            'moodle/competency:planreview',
            'moodle/competency:planview',
            'moodle/competency:usercompetencycomment',
            'moodle/competency:usercompetencyreview',
            'moodle/competency:usercompetencyview',
            'moodle/competency:userevidencemanage',
            'moodle/competency:userevidenceview',
            'moodle/competency:competencymanage',
            'moodle/competency:competencyview',
            'moodle/competency:templatemanage',
            'moodle/competency:templateview',
            'block/iomad_company_admin:competencymanagement_view',
            'block/iomad_company_admin:templateview',
        );

        $companymanagercaps = array(
            'moodle/competency:plancomment',
            'moodle/competency:planmanage',
            'moodle/competency:planmanageowndraft',
            'moodle/competency:planreview',
            'moodle/competency:planview',
            'moodle/competency:usercompetencycomment',
            'moodle/competency:usercompetencyreview',
            'moodle/competency:usercompetencyview',
            'moodle/competency:userevidencemanage',
            'moodle/competency:userevidenceview',
            'moodle/competency:competencymanage',
            'moodle/competency:competencyview',
            'moodle/competency:templatemanage',
            'moodle/competency:templateview',
            'block/iomad_company_admin:competencymanagement_view',
            'block/iomad_company_admin:competencyview',
            'block/iomad_company_admin:templateview',
        );

        $companycoursenoneditorcaps = array(
            'moodle/competency:coursecompetencyview',
        );

        $companycourseeditorcaps = array(
            'moodle/competency:competencygrade',
            'moodle/competency:coursecompetencymanage',
            'moodle/competency:coursecompetencyview',
            'moodle/competency:coursecompetencyconfigure',
        );

        $systemcontext = context_system::instance();
        if ($companymanager = $DB->get_record( 'role', array( 'shortname' => 'companymanager') )) {
            foreach ($companymanagercaps as $cap) {
                assign_capability( $cap, CAP_ALLOW, $companymanager->id, $systemcontext->id );
            }
        }

        if ($companydepartmentmanager = $DB->get_record( 'role', array( 'shortname' => 'companydepartmentmanager') )) {
            foreach ($companydepartmentmanagercaps as $cap) {
                assign_capability( $cap, CAP_ALLOW, $companydepartmentmanager->id, $systemcontext->id );
            }
        }

        if ($companycourseeditor = $DB->get_record( 'role', array( 'shortname' => 'companycourseeditor') )) {
            foreach ($companycourseeditorcaps as $cap) {
                assign_capability( $cap, CAP_ALLOW, $companycourseeditor->id, $systemcontext->id );
            }
        }

        if ($companycoursenoneditor = $DB->get_record( 'role', array( 'shortname' => 'companycoursenoneditor') )) {
            foreach ($companycoursenoneditorcaps as $cap) {
                assign_capability( $cap, CAP_ALLOW, $companycoursenoneditor->id, $systemcontext->id );
            }
        }

        // Remove moodle/my:manageblocks capability from authenticated user
        if ($authenticateduser = $DB->get_record('role', array('shortname' => 'user'))) {
            assign_capability('moodle/my:manageblocks', CAP_PREVENT, $authenticateduser->id, $systemcontext->id, true);
        }

        upgrade_plugin_savepoint(true, 2016090503, 'local', 'iomad');
    }

    if ($oldversion < 2017041700) {

        // Define field emailprofileid to be added to company.
        $table = new xmldb_table('company');
        $field = new xmldb_field('emailprofileid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'suspended');

        // Conditionally launch add field emailprofileid.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field managernotify to be added to company.
        $table = new xmldb_table('company');
        $field = new xmldb_field('managernotify', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'emailprofileid');

        // Conditionally launch add field managernotify.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Iomad savepoint reached.
        upgrade_plugin_savepoint(true, 2017041700, 'local', 'iomad');
    }

    if ($oldversion < 2017041701) {

        // Define field parentid to be added to company.
        $table = new xmldb_table('company');
        $field = new xmldb_field('parentid', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, '0', 'managernotify');

        // Conditionally launch add field parentid.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Iomad savepoint reached.
        upgrade_plugin_savepoint(true, 2017041701, 'local', 'iomad');

        // Define field ecommerce to be added to company.
        $table = new xmldb_table('company');
        $field = new xmldb_field('ecommerce', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'parentid');

        // Conditionally launch add field ecommerce.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
    }

    if ($oldversion < 2017041702) {

        // Define field parentid to be added to company.
        $table = new xmldb_table('company');
        $field = new xmldb_field('parentid', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, '0', 'managernotify');

        // Conditionally launch add field parentid.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Iomad savepoint reached.
        upgrade_plugin_savepoint(true, 2017041702, 'local', 'iomad');
    }

    if ($oldversion < 2017041705) {

        // Define field parentid to be added to companylicense.
        $table = new xmldb_table('companylicense');
        $field = new xmldb_field('parentid', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, '0', 'companyid');

        // Conditionally launch add field parentid.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Iomad savepoint reached.
        upgrade_plugin_savepoint(true, 2017041705, 'local', 'iomad');
    }

    if ($oldversion < 2017041707) {

        $systemcontext = context_system::instance();

        // Add the edit my licenses capability.
        $companymanagercaps = array(
            'block/iomad_company_admin:edit_my_licenses',
        );

        if ($companymanager = $DB->get_record('role', array('shortname' => 'companymanager'))) {
            foreach ($companymanagercaps as $cap) {
                assign_capability( $cap, CAP_ALLOW, $companymanager->id, $systemcontext->id );
            }
        }

        upgrade_plugin_savepoint(true, 2017041707, 'local', 'iomad');
    }

    if ($oldversion < 2017041708) {

        // Define field maincolor to be added to company.
        $table = new xmldb_table('company');
        $field = new xmldb_field('maincolor', XMLDB_TYPE_CHAR, '20', null, null, null, 'null', 'customcss');

        // Conditionally launch add field maincolor.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field headingcolor to be added to company.
        $table = new xmldb_table('company');
        $field = new xmldb_field('headingcolor', XMLDB_TYPE_CHAR, '20', null, null, null, 'null', 'maincolor');

        // Conditionally launch add field headingcolor.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field linkcolor to be added to company.
        $table = new xmldb_table('company');
        $field = new xmldb_field('linkcolor', XMLDB_TYPE_CHAR, '20', null, null, null, 'null', 'headingcolor');

        // Conditionally launch add field linkcolor.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Iomad savepoint reached.
        upgrade_plugin_savepoint(true, 2017041708, 'local', 'iomad');
    }

    if ($oldversion < 2017041711) {

        // Define field type to be added to companylicense.
        $table = new xmldb_table('companylicense');
        $field = new xmldb_field('type', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'parentid');

        // Conditionally launch add field type.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Iomad savepoint reached.
        upgrade_plugin_savepoint(true, 2017041711, 'local', 'iomad');
    }

    if ($oldversion < 2017041713) {

        // Define field program to be added to companylicense.
        $table = new xmldb_table('companylicense');
        $field = new xmldb_field('program', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'type');

        // Conditionally launch add field program.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Iomad savepoint reached.
        upgrade_plugin_savepoint(true, 2017041713, 'local', 'iomad');
    }

    if ($oldversion < 2017041714) {

        // Define field groupid to be added to companylicense_users.
        $table = new xmldb_table('companylicense_users');
        $field = new xmldb_field('groupid', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, '0', 'issuedate');

        // Conditionally launch add field groupid.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Iomad savepoint reached.
        upgrade_plugin_savepoint(true, 2017041714, 'local', 'iomad');
    }

    if ($oldversion < 2017041715) {

        // Define field program to be added to companylicense.
        $table = new xmldb_table('companylicense');
        $field = new xmldb_field('program', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'type');

        // Conditionally launch add field program.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Iomad savepoint reached.
        upgrade_plugin_savepoint(true, 2017041715, 'local', 'iomad');
    }

    if ($oldversion < 2017041717) {

        // Define table company_role_templates to be created.
        $table = new xmldb_table('company_role_templates');

        // Adding fields to table company_role_templates.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '50', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table company_role_templates.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for company_role_templates.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table company_role_templates_caps to be created.
        $table = new xmldb_table('company_role_templates_caps');

        // Adding fields to table company_role_templates_caps.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('templateid', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
        $table->add_field('roleid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('capability', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table company_role_templates_caps.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for company_role_templates_caps.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Iomad savepoint reached.
        upgrade_plugin_savepoint(true, 2017041717, 'local', 'iomad');
    }

    if ($oldversion < 2017041719) {

        // Define field custommenuitems to be added to company.
        $table = new xmldb_table('company');
        $field = new xmldb_field('custommenuitems', XMLDB_TYPE_TEXT, null, null, null, null, null, 'ecommerce');

        // Conditionally launch add field custommenuitems.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Iomad savepoint reached.
        upgrade_plugin_savepoint(true, 2017041719, 'local', 'iomad');
    }

    if ($oldversion < 2017041720) {

        // Define field autoenrol to be added to company_course.
        $table = new xmldb_table('company_course');
        $field = new xmldb_field('autoenrol', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'departmentid');

        // Conditionally launch add field autoenrol.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Iomad savepoint reached.
        upgrade_plugin_savepoint(true, 2017041720, 'local', 'iomad');
    }

    if ($oldversion < 2017041722) {

        // Define table company_role_templates_ass to be created.
        $table = new xmldb_table('company_role_templates_ass');

        // Adding fields to table company_role_templates_ass.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('companyid', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
        $table->add_field('templateid', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table company_role_templates_ass.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for company_role_templates_ass.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Iomad savepoint reached.
        upgrade_plugin_savepoint(true, 2017041722, 'local', 'iomad');
    }

    if ($oldversion < 2017090303) {

        // Define field managerdigestday to be added to company.
        $table = new xmldb_table('company');
        $field = new xmldb_field('managerdigestday', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'custommenuitems');

        // Conditionally launch add field managerdigestday.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Iomad savepoint reached.
        upgrade_plugin_savepoint(true, 2017090303, 'local', 'iomad');
    }

    if ($oldversion < 2017090304) {

        // Define field startdate to be added to companylicense.
        $table = new xmldb_table('companylicense');
        $field = new xmldb_field('startdate', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, '0', 'validlength');

        // Conditionally launch add field startdate.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Set the default start date to be in the past.
        $startdate = strtotime('01/01/2004');
        $DB->execute("UPDATE {companylicense} SET startdate = :startdate", array('startdate' => $startdate));

        // Iomad savepoint reached.
        upgrade_plugin_savepoint(true, 2017090304, 'local', 'iomad');
    }

    if ($oldversion < 2017090305) {

        // Define table companycertificate to be created.
        $table = new xmldb_table('companycertificate');

        // Adding fields to table companycertificate.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('companyid', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
        $table->add_field('uselogo', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '1');
        $table->add_field('usewatermark', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '1');
        $table->add_field('usesignature', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '1');
        $table->add_field('useborder', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '1');
        $table->add_field('showgrade', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '1');

        // Adding keys to table companycertificate.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for companycertificate.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        if ($companies = $DB->get_records('company')) {
            foreach ($companies as $company) {
                $companycertrecord = array('companyid' => $company->id,
                                           'uselogo' => 1,
                                           'usewatermark' => 1,
                                           'usesignature' => 1,
                                           'useborder' => 1,
                                           'showgrade' => 1);
                $DB->insert_record('companycertificate', $companycertrecord);
            }
        }

        // Iomad savepoint reached.
        upgrade_plugin_savepoint(true, 2017090305, 'local', 'iomad');
    }

    if ($oldversion < 2017090307) {

        $systemcontext = context_system::instance();

        // Remove capabilities incorrectly added.
        // They do not exist.
        $roles = $DB->get_records('role');
        foreach ($roles as $role) {
            unassign_capability('block/iomad_company_admin:view', $role->id);
            unassign_capability('block/side_bar_block:editblock', $role->id);
            unassign_capability('block/side_bar_block:viewblock', $role->id);
            unassign_capability('enrol/authorize:manage', $role->id);
            unassign_capability('mod/certificate:manage', $role->id);
            unassign_capability('mod/certificate:view', $role->id);
        }

        // Fix capability typo in company department manager role
        $companydepartmentmanager = $DB->get_record('role', array('shortname' => 'companydepartmentmanager'), '*', MUST_EXIST);
        unassign_capability('block/iomad_report:view', $companydepartmentmanager->id);
        assign_capability('block/iomad_reports:view', CAP_ALLOW, $companydepartmentmanager->id, $systemcontext->id);

        // Iomad savepoint reached.
        upgrade_plugin_savepoint(true, 2017090307, 'local', 'iomad');
    }

    if ($oldversion < 2017090308) {

        // Define field previousroletemplateid to be added to company.
        $table = new xmldb_table('company');
        $field = new xmldb_field('previousroletemplateid', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, '0', 'managerdigestday');

        // Conditionally launch add field previousroletemplateid.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field reference to be added to companylicense.
        $table = new xmldb_table('companylicense');
        $field = new xmldb_field('reference', XMLDB_TYPE_CHAR, '100', null, null, null, null, 'program');

        // Conditionally launch add field reference.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Iomad savepoint reached.
        upgrade_plugin_savepoint(true, 2017090308, 'local', 'iomad');
    }

    if ($oldversion < 2017090309) {

        // Define field hostname to be added to company.
        $table = new xmldb_table('company');
        $field = new xmldb_field('hostname', XMLDB_TYPE_CHAR, '200', null, null, null, null, 'previousroletemplateid');

        // Conditionally launch add field hostname.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Iomad savepoint reached.
        upgrade_plugin_savepoint(true, 2017090309, 'local', 'iomad');
    }

    if ($oldversion < 2017090310) {

        // Define field instant to be added to companylicense.
        $table = new xmldb_table('companylicense');
        $field = new xmldb_field('instant', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'reference');

        // Conditionally launch add field instant.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Iomad savepoint reached.
        upgrade_plugin_savepoint(true, 2017090310, 'local', 'iomad');
    }

    if ($oldversion < 2017090311) {

        // Define field educator to be added to company_users.
        $table = new xmldb_table('company_users');
        $field = new xmldb_field('educator', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'suspended');

        // Conditionally launch add field educator.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // If we were automatically enrolling managers using the old method, mark them as an educator.
        if ($CFG->iomad_autoenrol_managers) {
            $DB->set_field_select('company_users', 'educator', 1, 'managertype != 0');
        }

        // Iomad savepoint reached.
        upgrade_plugin_savepoint(true, 2017090311, 'local', 'iomad');
    }

    if ($oldversion < 2017090313) {

        // Define table company_transient_tokens to be created.
        $table = new xmldb_table('company_transient_tokens');

        // Adding fields to table company_transient_tokens.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
        $table->add_field('expires', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
        $table->add_field('token', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);

        // Adding keys to table company_transient_tokens.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for company_transient_tokens.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Iomad savepoint reached.
        upgrade_plugin_savepoint(true, 2017090313, 'local', 'iomad');
    }

    if ($oldversion < 2018122700) {

        // Define field notifyperiod to be added to iomad_courses.
        $table = new xmldb_table('iomad_courses');
        $field = new xmldb_field('notifyperiod', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'warncompletion');

        // Conditionally launch add field notifyperiod.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field expireafter to be added to iomad_courses.
        $table = new xmldb_table('iomad_courses');
        $field = new xmldb_field('expireafter', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'notifyperiod');

        // Conditionally launch add field expireafter.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Perform clear up on courses data which may have become out of step.

        $sql = 'SELECT ic.courseid FROM {iomad_courses} ic
                LEFT OUTER JOIN {course} c ON c.id = ic.courseid
                WHERE c.id IS NULL';
        $deletedcourses = $DB->get_fieldset_sql($sql);

        foreach ($deletedcourses as $deletedcourse) {

            // Clear everything from the iomad_courses table.
            $DB->delete_records('iomad_courses', array('courseid' => $deletedcourse));

            // Remove the course from company allocation tables.
            $DB->delete_records('company_course', array('courseid' => $deletedcourse));

            // Remove the course from company created course tables.
            $DB->delete_records('company_created_courses', array('courseid' => $deletedcourse));

            // Remove the course from company shared courses tables.
            $DB->delete_records('company_shared_courses', array('courseid' => $deletedcourse));

            // Deal with licenses allocations.
            $DB->delete_records('companylicense_users', array('licensecourseid' => $deletedcourse));
            $courselicenses = $DB->get_records('companylicense_courses', array('courseid' => $deletedcourse));
            foreach ($courselicenses as $courselicense) {
                // Delete the course from the license.
                $DB->delete_records('companylicense_courses', array('id' => $courselicense->id));
                // Does the license have any courses left?
                if ($DB->get_records('companylicense_courses', array('licenseid' => $courselicense->licenseid))) {
                    company::update_license_usage($courselicense->licenseid);
                } else {
                    // Delete the license.  It no longer is valid.
                    $DB->delete_records('companylicense', array('id' => $courselicense->licenseid));
                }
            }
        }

        // Iomad savepoint reached.
        upgrade_plugin_savepoint(true, 2018122700, 'local', 'iomad');
    }

    if ($oldversion < 2019012900) {

        // Define field maxusers to be added to company.
        $table = new xmldb_table('company');
        $field = new xmldb_field('maxusers', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, '0', 'hostname');

        // Conditionally launch add field maxusers.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Iomad savepoint reached.
        upgrade_plugin_savepoint(true, 2019012900, 'local', 'iomad');
    }

    if ($oldversion < 2019012901) {

        // Define field warnnotstarted to be added to iomad_courses.
        $table = new xmldb_table('iomad_courses');
        $field = new xmldb_field('warnnotstarted', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, '0', 'expireafter');

        // Conditionally launch add field warnnotstarted.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Iomad savepoint reached.
        upgrade_plugin_savepoint(true, 2019012901, 'local', 'iomad');
    }

    if ($oldversion < 2019030100) {

        // Define field hasgrade to be added to iomad_courses.
        $table = new xmldb_table('iomad_courses');
        $field = new xmldb_field('hasgrade', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '1', 'warnnotstarted');

        // Conditionally launch add field hasgrade.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Iomad savepoint reached.
        upgrade_plugin_savepoint(true, 2019030100, 'local', 'iomad');
    }

    if ($oldversion < 2019030101) {

        $validcourseids = $DB->get_fieldset_select('course', 'id', true);
        if ($validcourseids) {
            list($sql, $params) = $DB->get_in_or_equal($validcourseids);

            // Clear any non existant references from the iomad tables.
            $DB->delete_records_select('iomad_courses', "NOT courseid $sql", $params);
            $DB->delete_records_select('company_course', "NOT courseid $sql", $params);
            $DB->delete_records_select('company_created_courses', "NOT courseid $sql", $params);
            $DB->delete_records_select('company_shared_courses', "NOT courseid $sql", $params);
            $DB->delete_records_select('companylicense_users', "NOT licensecourseid $sql", $params);

            // Find broken licenses and delete missing courses.
            $licenses = $DB->get_fieldset_select('companylicense_courses', 'licenseid', "NOT courseid $sql", $params);
            $DB->delete_records_select('companylicense_courses', "NOT courseid $sql", $params);

            // Delete any licenses that are left empty.
            $sql = 'SELECT l.id
                    FROM {companylicense} l
                    LEFT OUTER JOIN {companylicense_courses} c ON l.id = c.licenseid
                    WHERE c.id IS NULL';
            $deletedlicenses = $DB->get_fieldset_sql($sql);
            if ($deletedlicenses) {
                list($sql, $params) = $DB->get_in_or_equal($deletedlicenses);
                $DB->delete_records_select('companylicense', "id $sql", $params);
            }

            // Remove deleted licenses fromm affected list.
            $licenses = array_diff(array_values($licenses), $deletedlicenses);

            // Update usage of affected licenses.
            foreach ($licenses as $license) {
                company::update_license_usage($license);
            }
        }

        // Define key licenseid (foreign) to be added to companylicense_users.
        $table = new xmldb_table('companylicense_users');
        $key = new xmldb_key('licenseid', XMLDB_KEY_FOREIGN, array('licenseid'), 'companylicense', array('id'));

        // Launch add key licenseid.
        $dbman->add_key($table, $key);

        // Define key licensecourseid (foreign) to be added to companylicense_users.
        $table = new xmldb_table('companylicense_users');
        $key = new xmldb_key('licensecourseid', XMLDB_KEY_FOREIGN, array('licensecourseid'), 'course', array('id'));

        // Iomad savepoint reached.
        upgrade_plugin_savepoint(true, 2019030101, 'local', 'iomad');
    }

    if ($oldversion < 2019030102) {

        // Define field humanallocation to be added to companylicense.
        $table = new xmldb_table('companylicense');
        $field = new xmldb_field('humanallocation', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, '0', 'allocation');

        // Conditionally launch add field humanallocation.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Add the right value to the new human allocation.
        if ($licenses = $DB->get_records('companylicense')) {
            foreach ($licenses as $license) {
                if (empty($license->program)) {
                    $DB->set_field('companylicense', 'humanallocation', $license->allocation, array('id' => $license->id));
                } else {
                    // Get the number of courses.
                    $coursecount = $DB->count_records('companylicense_courses', array('licenseid' => $license->id));
                    $DB->set_field('companylicense', 'humanallocation', $license->allocation / $coursecount, array('id' => $license->id));
                }
            }
        }

        // Iomad savepoint reached.
        upgrade_plugin_savepoint(true, 2019030102, 'local', 'iomad');
    }

    if ($oldversion < 2019030103) {

        // Update the existing roles to have the correct archetypes.
        $rolesarray = array('clientadministrator', 'companymanager', 'companydepartmentmanager', 'companycourseeditor', 'companycoursenoneditor');
        foreach ($rolesarray as $role) {
            if ($rolerec = $DB->get_record('role', array('shortname' => $role))) {
                $DB->set_field('role', 'archetype', $role, array('id' => $rolerec->id));
                if (!$DB->record_exists('role_context_levels', array('roleid' => $rolerec->id, 'contextlevel' => CONTEXT_SYSTEM))) {
                    $DB->insert_record('role_context_levels', array('roleid' => $rolerec->id, 'contextlevel' => CONTEXT_SYSTEM));
                }
            }
        }


        // Iomad savepoint reached.
        upgrade_plugin_savepoint(true, 2019030103, 'local', 'iomad');
    }

    if ($oldversion < 2019030104) {

       // Create new Company reporter role.
        if (!$companyreporter = $DB->get_record( 'role', array( 'shortname' => 'companyreporter') )) {
            $companyreporterid = create_role( 'Company Report Only', 'companyreporter',
            '(Iomad) Access to company reports only..', 'companyreporter');
        } else {
            $companyreporterid = $companyreporter->id;
        }

        // If not done already, allow assignment at system context.
        $levels = get_role_contextlevels( $companyreporterid );
        if (empty($levels)) {
            $level = new stdClass;
            $level->roleid = $companyreporterid;
            $level->contextlevel = CONTEXT_SYSTEM;
            $DB->insert_record( 'role_context_levels', $level );
        }

        update_capabilities('companyreporter');

       // Create new Client reporter role.
        if (!$clientreporter = $DB->get_record( 'role', array( 'shortname' => 'clientreporter') )) {
            $clientreporterid = create_role( 'Client Report Only', 'clientreporter',
            '(Iomad) CLient access to all company reports only..', 'clientreporter');
        } else {
            $clientreporterid = $clientreporter->id;
        }

        // If not done already, allow assignment at system context.
        $levels = get_role_contextlevels( $clientreporterid );
        if (empty($levels)) {
            $level = new stdClass;
            $level->roleid = $clientreporterid;
            $level->contextlevel = CONTEXT_SYSTEM;
            $DB->insert_record( 'role_context_levels', $level );
        }

        update_capabilities('clientreporter');

        // Iomad savepoint reached.
        upgrade_plugin_savepoint(true, 2019030104, 'local', 'iomad');
    }

    if ($oldversion < 2019030106) {

        // Define field cutoffdate to be added to companylicense.
        $table = new xmldb_table('companylicense');
        $field = new xmldb_field('cutoffdate', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'instant');

        // Conditionally launch add field cutoffdate.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field validto to be added to company.
        $table = new xmldb_table('company');
        $field = new xmldb_field('validto', XMLDB_TYPE_INTEGER, '20', null, null, null, null, 'maxusers');

        // Conditionally launch add field validto.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field suspendafter to be added to company.
        $table = new xmldb_table('company');
        $field = new xmldb_field('suspendafter', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'validto');

        // Conditionally launch add field suspendafter.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field terminated to be added to company.
        $table = new xmldb_table('company');
        $field = new xmldb_field('terminated', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'suspendafter');

        // Conditionally launch add field terminated.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Iomad savepoint reached.
        upgrade_plugin_savepoint(true, 2019030106, 'local', 'iomad');
    }

    if ($oldversion < 2019030107) {

        // Rename field terminated on table company to companyterminated.
        $table = new xmldb_table('company');
        $field = new xmldb_field('terminated', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'suspendafter');

        // Launch rename field companyterminated.
        $dbman->rename_field($table, $field, 'companyterminated');

        // Iomad savepoint reached.
        upgrade_plugin_savepoint(true, 2019030107, 'local', 'iomad');
    }

    if ($oldversion < 2019030108) {

        // Define field previousemailtemplateid to be added to company.
        $table = new xmldb_table('company');
        $field = new xmldb_field('previousemailtemplateid', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, '0', 'previousroletemplateid');

        // Conditionally launch add field previousemailtemplateid.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Iomad savepoint reached.
        upgrade_plugin_savepoint(true, 2019030108, 'local', 'iomad');
    }

    if ($oldversion < 2019030109) {

        // Security issue that users may be tagged as educators when they shouldn't have been.
        if ($CFG->iomad_autoenrol_managers) {
            // Remove any company user educator entry where not a manager.
            $affected = $DB->get_records('company_users', array('managertype' => 0, 'educator' => 1));
            foreach ($affected as $companyuser) {
                $DB->set_field('company_users', 'educator', 0, array('id' => $companyuser->id));
            }
        } else {
            // Get the potentials.
            $affectedusers = array();
            $companyusers = $DB->get_records('company_users', array('managertype' => 0, 'educator' => 1));
            foreach ($companyusers as $companyuser) {
                if ($DB->get_records_sql("SELECT id FROM {logstore_standard_log}
                                          WHERE userid = :userid
                                          AND " . $DB->sql_like('eventname', ':eventname') . "
                                          AND " . $DB->sql_like('other', ':other'),
                                          array('userid' => $companyuser->userid,
                                                'eventname' => '%company_user_assigned',
                                                'other' => '%Course educator%'))) {
                    continue;
                } else {
                    $affectedusers[$companyuser->id] = $companyuser;
                }
                // Deal with the erroneous users.
                foreach ($affectedusers as $affecteduser) {
                    company::upsert_company_user($affecteduser->userid, $affecteduser->companyid, $affecteduser->departmentid, $affecteduser->managertype, false);
                }
            } 
        }
        // Iomad savepoint reached.
        upgrade_plugin_savepoint(true, 2019030109, 'local', 'iomad');
    }

    if ($oldversion < 2019030111) {

        // Define field code to be added to company.
        $table = new xmldb_table('company');
        $field = new xmldb_field('code', XMLDB_TYPE_CHAR, '25', null, null, null, null, 'shortname');

        // Conditionally launch add field code.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Iomad savepoint reached.
        upgrade_plugin_savepoint(true, 2019030111, 'local', 'iomad');
    }

    if ($oldversion < 2019030113) {

        // Define field clearonexpire to be added to companylicense.
        $table = new xmldb_table('companylicense');
        $field = new xmldb_field('clearonexpire', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'cutoffdate');

        // Conditionally launch add field clearonexpire.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Iomad savepoint reached.
        upgrade_plugin_savepoint(true, 2019030113, 'local', 'iomad');
    }

    if ($oldversion < 2021012500) {

        // Define index departmentusers (unique) to be dropped form company_users.
        $table = new xmldb_table('company_users');
        $index = new xmldb_index('departmentusers', XMLDB_INDEX_UNIQUE, ['userid', 'departmentid']);

        // Conditionally launch drop index departmentusers.
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }

        $index = new xmldb_index('companyusers', XMLDB_INDEX_UNIQUE, ['companyid', 'userid']);

        // Conditionally launch drop index departmentusers.
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }

        $index = new xmldb_index('departmentusers', XMLDB_INDEX_UNIQUE, ['companyid', 'userid', 'departmentid']);

        // Conditionally launch add index departmentusers.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Iomad savepoint reached.
        upgrade_plugin_savepoint(true, 2021012500, 'local', 'iomad');
    }

    if ($oldversion < 2021042500) {

        // Define field address to be added to company.
        $table = new xmldb_table('company');
        $field = new xmldb_field('address', XMLDB_TYPE_TEXT, null, null, null, null, null, 'code');

        // Conditionally launch add field address.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field postcode to be added to company.
        $table = new xmldb_table('company');
        $field = new xmldb_field('postcode', XMLDB_TYPE_CHAR, '20', null, null, null, null, 'city');

        // Conditionally launch add field postcode.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Iomad savepoint reached.
        upgrade_plugin_savepoint(true, 2021042500, 'local', 'iomad');
    }

    if ($oldversion < 2022032400) {

        // Define field isvirtual to be added to classroom.
        $table = new xmldb_table('classroom');
        $field = new xmldb_field('isvirtual', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'capacity');

        // Conditionally launch add field isvirtual.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Iomad savepoint reached.
        upgrade_plugin_savepoint(true, 2022032400, 'local', 'iomad');
    }

    if ($oldversion < 2022100700) {

        // Define field region to be added to company.
        $table = new xmldb_table('company');
        $field = new xmldb_field('region', XMLDB_TYPE_CHAR, '50', null, null, null, null, 'city');

        // Conditionally launch add field region.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field custom1 to be added to company.
        $table = new xmldb_table('company');
        $field = new xmldb_field('custom1', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'companyterminated');

        // Conditionally launch add field custom1.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field custom2 to be added to company.
        $table = new xmldb_table('company');
        $field = new xmldb_field('custom2', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'custom1');

        // Conditionally launch add field custom2.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field custom3 to be added to company.
        $table = new xmldb_table('company');
        $field = new xmldb_field('custom3', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'custom2');

        // Conditionally launch add field custom3.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        // Changing precision of field code on table company to (255).
        $table = new xmldb_table('company');
        $field = new xmldb_field('code', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'shortname');

        // Launch change of precision for field code.
        $dbman->change_field_precision($table, $field);

        // Iomad savepoint reached.
        upgrade_plugin_savepoint(true, 2022100700, 'local', 'iomad');
    }

    if ($oldversion < 2023012500) {

        // Define index comp_par (not unique) to be added to company.
        $table = new xmldb_table('company');
        $index = new xmldb_index('comp_par', XMLDB_INDEX_NOTUNIQUE, ['parentid']);

        // Conditionally launch add index comp_par.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }
        // Define index comp_shortname (not unique) to be added to company.
        $table = new xmldb_table('company');
        $index = new xmldb_index('comp_shortname', XMLDB_INDEX_NOTUNIQUE, ['shortname']);

        // Conditionally launch add index comp_shortname.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Define index comp_name (not unique) to be added to company.
        $table = new xmldb_table('company');
        $index = new xmldb_index('comp_name', XMLDB_INDEX_NOTUNIQUE, ['name']);

        // Conditionally launch add index comp_name.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Define index depa_idcom (not unique) to be added to department.
        $table = new xmldb_table('department');
        $index = new xmldb_index('depa_idcom', XMLDB_INDEX_NOTUNIQUE, ['id', 'company']);

        // Conditionally launch add index depa_idcom.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Define index depa_idcompar (not unique) to be added to department.
        $table = new xmldb_table('department');
        $index = new xmldb_index('depa_idcompar', XMLDB_INDEX_NOTUNIQUE, ['id', 'company', 'parent']);

        // Conditionally launch add index depa_idcompar.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Iomad savepoint reached.
        upgrade_plugin_savepoint(true, 2023012500, 'local', 'iomad');
    }

    if ($oldversion < 2023021500) {

        // Define field paymentaccount to be added to company.
        $table = new xmldb_table('company');
        $field = new xmldb_field('paymentaccount', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'custom3');

        // Conditionally launch add field paymentaccount.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Iomad savepoint reached.
        upgrade_plugin_savepoint(true, 2023021500, 'local', 'iomad');
    }

    return $result;
}
