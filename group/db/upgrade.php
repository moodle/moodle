<?php  //$Id$

// This file keeps track of upgrades to 
// groups
//
// Sometimes, changes between versions involve
// alterations to database structures and other
// major things that may break installations.
//
// The upgrade function in this file will attempt
// to perform all the necessary actions to upgrade
// your older installtion to the current version.
//
// If there's something it cannot do itself, it
// will tell you what you need to do.
//
// The commands in here will all be database-neutral,
// using the functions defined in lib/ddllib.php

function upgrade_group_db($continueto) {
/// This function upgrades the group tables, if necessary
/// It's called from admin/index.php.

    global $CFG, $db;

    $group_version = '';  // Get code versions
    require_once ("$CFG->dirroot/group/version.php");

    if (empty($CFG->group_version)) {  // New groups have never been installed...
        $status = false;

        $strdatabaseupgrades = get_string('databaseupgrades');
        print_header($strdatabaseupgrades, $strdatabaseupgrades, $strdatabaseupgrades, '', 
                '<script type="text/javascript" src="' . $CFG->wwwroot . '/lib/scroll_to_errors.js"></script>',
                false, "&nbsp;", "&nbsp;");

        upgrade_log_start();
        print_heading('group');
        $db->debug=true;

        //TODO: for testing, revert to 'old' groups.
        if (! get_config('group_version')) {
            $status = revert_group_db();
        }

        //... But Moodle is already installed.
        if (table_exists($t_groups = new XMLDBTable('groups'))) {
            $status = rename_table($t_groups, 'groups_temp');
            $status = rename_table(new XMLDBTable('groups_members'), 'groups_members_temp');
        }

    /// Both old .sql files and new install.xml are supported
    /// but we prioritize install.xml (XMLDB) if present

        if (file_exists($CFG->dirroot . '/group/db/install.xml')) {
            $status = install_from_xmldb_file($CFG->dirroot . '/group/db/install.xml'); //New method
        } else if (file_exists($CFG->dirroot . '/group/db/' . $CFG->dbtype . '.sql')) {
            $status = modify_database($CFG->dirroot . '/group/db/' . $CFG->dbtype . '.sql'); //Old method
        }

        $status = transfer_group_db();

        $db->debug = false;
    
        if (set_config('group_version', $group_version)) { //and set_config('group_release', $group_release)) {
            //initialize default group settings now
            $adminroot = admin_get_root();
            apply_default_settings($adminroot->locate('groups'));
            notify(get_string('databasesuccess'), 'green');
            notify(get_string('databaseupgradegroups', '', $group_version), 'green');
            print_continue($continueto);
            print_footer('none');
            exit;
        } else {
            error("Upgrade of group system failed! (Could not update version in config table)");
        }
    }

/// Upgrading code starts here
    $oldupgrade = false;
    $newupgrade = false;
    if (is_readable($CFG->dirroot . '/group/db/' . $CFG->dbtype . '.php')) {
        include_once($CFG->dirroot . '/group/db/' . $CFG->dbtype . '.php');  // defines old upgrading function
        $oldupgrade = true;
    }
    if (is_readable($CFG->dirroot . '/group/db/upgrade.php')) {
        include_once($CFG->dirroot . '/group/db/upgrade.php');  // defines new upgrading function
        $newupgrade = true;
    }

    if ($group_version > $CFG->group_version) {       // Upgrade tables
        $strdatabaseupgrades = get_string('databaseupgrades');
        print_header($strdatabaseupgrades, $strdatabaseupgrades, $strdatabaseupgrades, '',
                 '<script type="text/javascript" src="' . $CFG->wwwroot . '/lib/scroll_to_errors.js"></script>');

        upgrade_log_start();
        print_heading('group');

    /// Run de old and new upgrade functions for the module
        $oldupgrade_function = 'group_upgrade';
        $newupgrade_function = 'xmldb_group_upgrade';

    /// First, the old function if exists
        $oldupgrade_status = true;
        if ($oldupgrade && function_exists($oldupgrade_function)) {
            $db->debug = true;
            $oldupgrade_status = $oldupgrade_function($CFG->group_version);
        } else if ($oldupgrade) {
            notify ('Upgrade function ' . $oldupgrade_function . ' was not available in ' .
                    '/group/db/' . $CFG->dbtype . '.php');
        }

    /// Then, the new function if exists and the old one was ok
        $newupgrade_status = true;
        if ($newupgrade && function_exists($newupgrade_function) && $oldupgrade_status) {
            $db->debug = true;
            $newupgrade_status = $newupgrade_function($CFG->group_version);
        } else if ($newupgrade) {
            notify ('Upgrade function ' . $newupgrade_function . ' was not available in ' .
                    '/group/db/upgrade.php');
        }

        $db->debug=false;
    /// Now analyze upgrade results
        if ($oldupgrade_status && $newupgrade_status) {    // No upgrading failed
            if (set_config('group_version', $group_version)) { //and set_config('group_release', $group_release))
                notify(get_string('databasesuccess'), 'green');
                notify(get_string('databaseupgradegroups', '', $group_version), 'green');
                print_continue($continueto);
                print_footer('none');
                exit;
            } else {
                error("Upgrade of group system failed! (Could not update version in config table)");
            }
        } else {
            error("Upgrade failed!  See group/version.php");
        }

        upgrade_log_finish();
        print_footer();

    } else if ($group_version < $CFG->group_version) {
        upgrade_log_start();
        notify("WARNING!!!  The code you are using is OLDER than the version that made these databases!");

        upgrade_log_finish();
        print_footer();
    }
}


function transfer_group_db() {
    $status = true;
    
    if (table_exists($t_groups = new XMLDBTable('groups_temp'))) {
        $status = (bool)$groups_r = get_records('groups_temp');
        $status = (bool)$members_r= get_records('groups_members_temp');

        if (! $groups_r) {
            return $status;
        }
        foreach ($groups_r as $group) {
            if (debugging()) {
                print_object($group);
            }
            $group->enrolmentkey = $group->password;
            ///unset($group->password);
            ///unset($group->courseid);
            $status = (bool)$newgroupid = groups_create_group($group->courseid, $group);
            debugging('Create group status: '.$status); //TODO: ?
            if ($members_r) {
                foreach ($members_r as $member) {
                    if ($member->groupid == $group->id) {
                        $status = (bool)$memberid = groups_add_member($newgroupid, $member->userid);
                    }
                }
            }
            debugging('Add member status: '.$status);
        }
        ///$status = drop_table($t_groups);
        ///$status = drop_table(new XMLDBTable('groups_members_temp'));
    }
    return $status;
}


/**
 * For testing, it's useful to be able to revert to 'old' groups.
 */
function revert_group_db() {
    $status = false;
    //$status = (bool)$rs = delete_records('config', 'name', 'group_version');
    if (!get_config('group_version') && table_exists(new XMLDBTable('groups_groupings'))) { //debugging()
        $status = drop_table(new XMLDBTable('groups'));
        $status = drop_table(new XMLDBTable('groups_members'));
        $status = drop_table(new XMLDBTable('groups_groupings'));
        $status = drop_table(new XMLDBTable('groups_courses_groups'));
        $status = drop_table(new XMLDBTable('groups_courses_groupings'));
        $status = drop_table(new XMLDBTable('groups_groupings_groups'));

        $status = rename_table(new XMLDBTable('groups_temp'), 'groups');
        $status = rename_table(new XMLDBTable('groups_members_temp'), 'groups_members');
    }
    return $status;
}


function xmldb_group_upgrade($oldversion=0) {

    //global $CFG, $THEME, $db;

    $result = true;

    if ($result && $oldversion < 2007012000) {

    /// Changing nullability of field description on table groups to null
        $table = new XMLDBTable('groups');
        $field = new XMLDBField('description');
        $field->setAttributes(XMLDB_TYPE_TEXT, 'small', null, null, null, null, null, null, 'name');

    /// Launch change of nullability for field description
        $result = $result && change_field_notnull($table, $field);

    /// Changing nullability of field description on table groups_groupings to null
        $table = new XMLDBTable('groups_groupings');
        $field = new XMLDBField('description');
        $field->setAttributes(XMLDB_TYPE_TEXT, 'small', null, null, null, null, null, null, 'name');

    /// Launch change of nullability for field description
        $result = $result && change_field_notnull($table, $field);
    }

    if ($result && $oldversion < 2007012100) {

    /// Changing precision of field lang on table groups to (30)
        $table = new XMLDBTable('groups');
        $field = new XMLDBField('lang');
        $field->setAttributes(XMLDB_TYPE_CHAR, '30', null, XMLDB_NOTNULL, null, null, null, 'en', 'enrolmentkey');

    /// Launch change of precision for field lang
        $result = $result && change_field_precision($table, $field);
    }

    return $result;
}

?>
