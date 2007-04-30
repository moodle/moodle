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
function install_group_db() {
    global $CFG, $db;

    $group_version = '';  // Get code version
    require ("$CFG->dirroot/group/version.php");

    $status = true;

    print_heading('group');
    $db->debug=true;

    //Moodle is already installed - rename old tables, used during tansfer later
    if (table_exists($t_groups = new XMLDBTable('groups'))) {
        $status = $status && rename_table($t_groups, 'groups_temp');
        $status = $status && rename_table(new XMLDBTable('groups_members'), 'groups_members_temp');
    }

    // install new groups tables
    $status = $status && install_from_xmldb_file($CFG->dirroot . '/group/db/install.xml');
    // convert old groups to new ones
    $status = $status && groups_transfer_db();

    $db->debug = false;

    if (!$status or !set_config('group_version', $group_version)) {
        error("Upgrade of group system failed!");
    }

    notify(get_string('databasesuccess'), 'green');
    notify(get_string('databaseupgradegroups', '', $group_version), 'green');
}


function upgrade_group_db($continueto) {
/// This function upgrades the group tables, if necessary
/// It's called from admin/index.php.

    global $CFG, $db;

    $group_version = '';  // Get code versions
    require("$CFG->dirroot/group/version.php");

    if (empty($CFG->group_version)) {  // New 1.8 groups have never been installed...
        $strdatabaseupgrades = get_string('databaseupgrades');
        print_header($strdatabaseupgrades, $strdatabaseupgrades, $strdatabaseupgrades, '', 
                upgrade_get_javascript(), false, "&nbsp;", "&nbsp;");

        upgrade_log_start();
        //initialize default group settings now
        install_group_db();

        print_continue($continueto);
        print_footer('none');
        exit;
    }

/// Upgrading code starts here
    if ($group_version > $CFG->group_version) {       // Upgrade tables
        $strdatabaseupgrades = get_string('databaseupgrades');
        print_header($strdatabaseupgrades, $strdatabaseupgrades, $strdatabaseupgrades, '', upgrade_get_javascript());

        upgrade_log_start();
        print_heading('group');

        $db->debug = true;
        $status = xmldb_group_upgrade($CFG->group_version);
        $db->debug = false;

    /// Now analyze upgrade results
        if ($status) {    // No upgrading failed
            if (set_config('group_version', $group_version)) {
                notify(get_string('databasesuccess'), 'green');
                notify(get_string('databaseupgradegroups', '', $group_version), 'green');
                print_continue($continueto);
                print_footer('none');
                exit;
            } else {
                error("Error: Upgrade of group system failed! (Could not update version in config table)");
            }
        } else {
            error("Error: Upgrade failed! See group/upgrade.php");
        }

    } else if ($group_version < $CFG->group_version) {
        error("Error:  The code you are using is OLDER than the version that made these databases!");
    }
}

/**
 * Transfer data from old 1.7 to new 1.8 groups tables.
 */
function groups_transfer_db() {
    $status = true;
    
    if (table_exists($t_groups = new XMLDBTable('groups_temp'))) {
        $groups_r = get_records('groups_temp');
        $members_r = get_records('groups_members_temp');

        if (!$groups_r) {
            // No gropus to upgrade.
            return true;
        }
        foreach ($groups_r as $group) {
            if (debugging()) {
                print_object($group);
            }
            $group->enrolmentkey = $group->password;
            $status = $status && ($newgroupid = groups_db_upgrade_group($group->courseid, $group));
            if ($members_r) {
                foreach ($members_r as $member) {
                    if ($member->groupid == $group->id) {
                        $status = $status && groups_add_member($newgroupid, $member->userid);
                    }
                }
            }
        }
    } else {
        $status = true; //new install - it is ok!
    }
    return $status;
}

function groups_drop_keys_indexes_db() {
    $result = true;
    /// Define index groupid-courseid (unique) to be added to groups_members
        $table = new XMLDBTable('groups_members');
        $index = new XMLDBIndex('groupid-courseid');
        $index->setAttributes(XMLDB_INDEX_UNIQUE, array('groupid', 'userid'));

    /// Launch add index groupid-courseid
        $result = $result && drop_index($table, $index);

    /// Define key courseid (foreign) to be added to groups_courses_groups
        $table = new XMLDBTable('groups_courses_groups');
        $key = new XMLDBKey('courseid');
        $key->setAttributes(XMLDB_KEY_FOREIGN, array('courseid'), 'course', array('id'));

    /// Launch add key courseid
        $result = $result && drop_key($table, $key);

    /// Define key groupid (foreign) to be added to groups_courses_groups
        $key = new XMLDBKey('groupid');
        $key->setAttributes(XMLDB_KEY_FOREIGN, array('groupid'), 'groups', array('id'));

    /// Launch add key groupid
        $result = $result && drop_key($table, $key);

    /// Define index courseid-groupid (unique) to be added to groups_courses_groups
        $index = new XMLDBIndex('courseid-groupid');
        $index->setAttributes(XMLDB_INDEX_UNIQUE, array('courseid', 'groupid'));

    /// Launch add index courseid-groupid
        $result = $result && drop_index($table, $index);

    /// Define key courseid (foreign) to be added to groups_courses_groupings
        $table = new XMLDBTable('groups_courses_groupings');
        $key = new XMLDBKey('courseid');
        $key->setAttributes(XMLDB_KEY_FOREIGN, array('courseid'), 'course', array('id'));

    /// Launch add key courseid
        $result = $result && drop_key($table, $key);

    /// Define key groupingid (foreign) to be added to groups_courses_groupings
        $key = new XMLDBKey('groupingid');
        $key->setAttributes(XMLDB_KEY_FOREIGN, array('groupingid'), 'groups_groupings', array('id'));

    /// Launch add key groupingid
        $result = $result && drop_key($table, $key);

    /// Define index courseid-groupingid (unique) to be added to groups_courses_groupings
        $index = new XMLDBIndex('courseid-groupingid');
        $index->setAttributes(XMLDB_INDEX_UNIQUE, array('courseid', 'groupingid'));

    /// Launch add index courseid-groupingid
        $result = $result && drop_index($table, $index);

    /// Define key groupingid (foreign) to be added to groups_groupings_groups
        $table = new XMLDBTable('groups_groupings_groups');
        $key = new XMLDBKey('groupingid');
        $key->setAttributes(XMLDB_KEY_FOREIGN, array('groupingid'), 'groups_groupings', array('id'));

    /// Launch add key groupingid
        $result = $result && drop_key($table, $key);

    /// Define key groupid (foreign) to be added to groups_groupings_groups
        $key = new XMLDBKey('groupid');
        $key->setAttributes(XMLDB_KEY_FOREIGN, array('groupid'), 'groups', array('id'));

    /// Launch add key groupid
        $result = $result && drop_key($table, $key);

    /// Define index groupingid-groupid (unique) to be added to groups_groupings_groups
        $index = new XMLDBIndex('groupingid-groupid');
        $index->setAttributes(XMLDB_INDEX_UNIQUE, array('groupingid', 'groupid'));

    /// Launch add index groupingid-groupid
        $result = $result && drop_index($table, $index);

    return $result;
}

/**
 * Drop 'new' 1.8 groups tables for 200701240 upgrade below.
 * (Also, for testing it's useful to be able to revert to 'old' groups.)
 */
function groups_revert_db($renametemp=true) {
    $status = true;
    ///$status = (bool)$rs = delete_records('config', 'name', 'group_version');
    if (table_exists(new XMLDBTable('groups_groupings'))) {

        $tables = array('', '_members', '_groupings', '_courses_groups', '_courses_groupings', '_groupings_groups');
        foreach ($tables as $t_name) {
            $status = $status && drop_table(new XMLDBTable('groups'.$t_name));
        }
        $status = $status && (bool)delete_records('log_display', 'module', 'group');

        if ($renametemp) {
            $status = $status && rename_table(new XMLDBTable('groups_temp'), 'groups');
            $status = $status && rename_table(new XMLDBTable('groups_members_temp'), 'groups_members');
        }
    }
    return $status;
}


function xmldb_group_upgrade($oldversion=0) {
    global $CFG;

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

    /// Adding all the missing FK + Unique indexes (XMLDB will create the underlying indexes)
    if ($result && $oldversion < 2007012200) {

    /// Define index groupid-courseid (unique) to be added to groups_members
        $table = new XMLDBTable('groups_members');
        $index = new XMLDBIndex('groupid-courseid');
        $index->setAttributes(XMLDB_INDEX_UNIQUE, array('groupid', 'userid'));

    /// Launch add index groupid-courseid
        $result = $result && add_index($table, $index);

    /// Define key courseid (foreign) to be added to groups_courses_groups
        $table = new XMLDBTable('groups_courses_groups');
        $key = new XMLDBKey('courseid');
        $key->setAttributes(XMLDB_KEY_FOREIGN, array('courseid'), 'course', array('id'));

    /// Launch add key courseid
        $result = $result && add_key($table, $key);

    /// Define key groupid (foreign) to be added to groups_courses_groups
        $table = new XMLDBTable('groups_courses_groups');
        $key = new XMLDBKey('groupid');
        $key->setAttributes(XMLDB_KEY_FOREIGN, array('groupid'), 'groups', array('id'));

    /// Launch add key groupid
        $result = $result && add_key($table, $key);

    /// Define index courseid-groupid (unique) to be added to groups_courses_groups
        $table = new XMLDBTable('groups_courses_groups');
        $index = new XMLDBIndex('courseid-groupid');
        $index->setAttributes(XMLDB_INDEX_UNIQUE, array('courseid', 'groupid'));

    /// Launch add index courseid-groupid
        $result = $result && add_index($table, $index);

    /// Define key courseid (foreign) to be added to groups_courses_groupings
        $table = new XMLDBTable('groups_courses_groupings');
        $key = new XMLDBKey('courseid');
        $key->setAttributes(XMLDB_KEY_FOREIGN, array('courseid'), 'course', array('id'));

    /// Launch add key courseid
        $result = $result && add_key($table, $key);

    /// Define key groupingid (foreign) to be added to groups_courses_groupings
        $table = new XMLDBTable('groups_courses_groupings');
        $key = new XMLDBKey('groupingid');
        $key->setAttributes(XMLDB_KEY_FOREIGN, array('groupingid'), 'groups_groupings', array('id'));

    /// Launch add key groupingid
        $result = $result && add_key($table, $key);

    /// Define index courseid-groupingid (unique) to be added to groups_courses_groupings
        $table = new XMLDBTable('groups_courses_groupings');
        $index = new XMLDBIndex('courseid-groupingid');
        $index->setAttributes(XMLDB_INDEX_UNIQUE, array('courseid', 'groupingid'));

    /// Launch add index courseid-groupingid
        $result = $result && add_index($table, $index);

    /// Define key groupingid (foreign) to be added to groups_groupings_groups
        $table = new XMLDBTable('groups_groupings_groups');
        $key = new XMLDBKey('groupingid');
        $key->setAttributes(XMLDB_KEY_FOREIGN, array('groupingid'), 'groups_groupings', array('id'));

    /// Launch add key groupingid
        $result = $result && add_key($table, $key);

    /// Define key groupid (foreign) to be added to groups_groupings_groups
        $table = new XMLDBTable('groups_groupings_groups');
        $key = new XMLDBKey('groupid');
        $key->setAttributes(XMLDB_KEY_FOREIGN, array('groupid'), 'groups', array('id'));

    /// Launch add key groupid
        $result = $result && add_key($table, $key);

    /// Define index groupingid-groupid (unique) to be added to groups_groupings_groups
        $table = new XMLDBTable('groups_groupings_groups');
        $index = new XMLDBIndex('groupingid-groupid');
        $index->setAttributes(XMLDB_INDEX_UNIQUE, array('groupingid', 'groupid'));

    /// Launch add index groupingid-groupid
        $result = $result && add_index($table, $index);
    }

    if ($result && $oldversion < 2007012400) {
        if (table_exists(new XMLDBTable('groups_temp')) && file_exists($CFG->dirroot.'/group/db/install.xml')) {
            /// Need to drop foreign keys/indexes added in last upgrade, drop 'new' tables, then start again!!
            $result = $result && groups_drop_keys_indexes_db();
            $result = $result && groups_revert_db($renametemp=false);
            $result = $result && install_from_xmldb_file($CFG->dirroot.'/group/db/install.xml');
            $result = $result && groups_transfer_db();
        }
    }

    return $result;
}

?>
