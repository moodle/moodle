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

/// MDL-11062 migrating to 1.8 groups system from versions before 1.8
function install_group_db() {
    global $CFG, $db;

    $group_version = '';  // Get code version
    require ("$CFG->dirroot/group/version.php");

    print_heading('group');
    $db->debug = true;
    $result = true;

    /// 1) Set groups->description to NULLable

    /// Changing nullability of field description on table groups to null

    $table = new XMLDBTable('groups');
    $field = new XMLDBField('description');
    $field->setAttributes(XMLDB_TYPE_TEXT, 'small', null, null, null, null, null, null, 'name');
        
    /// Launch change of nullability for field description
    $result = $result && change_field_notnull($table, $field);

    /// 2) Rename the groups->password field to enrolmentkey

    /// Rename field password on table groups to enrolmentkey.

    $field = new XMLDBField('password');
    $field->setAttributes(XMLDB_TYPE_CHAR, '50', null, XMLDB_NOTNULL, null, null, null, null, 'description');

    /// Launch rename field password
    $result = $result && rename_field($table, $field, 'enrolmentkey');

    /// 3) Change the groups->lang from 10cc to 30cc

    /// Changing precision of field lang on table groups to (30)
    $field = new XMLDBField('lang');
    $field->setAttributes(XMLDB_TYPE_CHAR, '30', null, XMLDB_NOTNULL, null, null, null, 'en', 'enrolmentkey');

    /// Launch change of precision for field lang
    $result = $result && change_field_precision($table, $field);

    /// 4) Change the groups->hidepicture from int(2) to int(1)

    /// Changing precision of field hidepicture on table groups to (1)
    $field = new XMLDBField('hidepicture');
    $field->setAttributes(XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0', 'picture');

    /// Launch change of precision for field hidepicture
    $result = $result && change_field_precision($table, $field);

    /// 5) Add one UNIQUE index on groups_members (groupid, userid)

    /// Define index groupid-courseid (unique) to be added to groups_members
    $table = new XMLDBTable('groups_members');
    $index = new XMLDBIndex('groupid-courseid');
    $index->setAttributes(XMLDB_INDEX_UNIQUE, array('groupid', 'userid'));

    /// Launch add index groupid-courseid
    $result = $result && add_index($table, $index);

    /// 6) Add the whole groups_groupings table (as is in 1.8.2+)

    /// Define table groups_groupings to be created
    $table = new XMLDBTable('groups_groupings');

    /// Adding fields to table groups_groupings
    $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
    $table->addFieldInfo('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, null, null);
    $table->addFieldInfo('description', XMLDB_TYPE_TEXT, 'small', null, null, null, null, null, null);
    $table->addFieldInfo('timecreated', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
    $table->addFieldInfo('viewowngroup', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '1');
    $table->addFieldInfo('viewallgroupsmembers', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
    $table->addFieldInfo('viewallgroupsactivities', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
    $table->addFieldInfo('teachersgroupmark', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
    $table->addFieldInfo('teachersgroupview', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
    $table->addFieldInfo('teachersoverride', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
    $table->addFieldInfo('teacherdeletable', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');

    /// Adding keys to table groups_groupings
    $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));

    /// Launch create table for groups_groupings
    $result = $result && create_table($table);

    /// 7) Add the whole groups_courses_groups table (as is in 1.8.2+)

    /// Define table groups_courses_groups to be created
    $table = new XMLDBTable('groups_courses_groups');

    /// Adding fields to table groups_courses_groups
    $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
    $table->addFieldInfo('courseid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
    $table->addFieldInfo('groupid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);

    /// Adding keys to table groups_courses_groups
    $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
    $table->addKeyInfo('courseid', XMLDB_KEY_FOREIGN, array('courseid'), 'course', array('id'));
    $table->addKeyInfo('groupid', XMLDB_KEY_FOREIGN, array('groupid'), 'groups', array('id'));

    /// Adding indexes to table groups_courses_groups
    $table->addIndexInfo('courseid-groupid', XMLDB_INDEX_UNIQUE, array('courseid', 'groupid'));

    /// Launch create table for groups_courses_groups
    $result = $result && create_table($table);

    /// 8) Add the whole groups_courses_groupings table (as is in 1.8.2+)

    /// Define table groups_courses_groupings to be created
    $table = new XMLDBTable('groups_courses_groupings');

    /// Adding fields to table groups_courses_groupings
    $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
    $table->addFieldInfo('courseid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
    $table->addFieldInfo('groupingid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);

    /// Adding keys to table groups_courses_groupings
    $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
    $table->addKeyInfo('courseid', XMLDB_KEY_FOREIGN, array('courseid'), 'course', array('id'));
    $table->addKeyInfo('groupingid', XMLDB_KEY_FOREIGN, array('groupingid'), 'groups_groupings', array('id'));

    /// Adding indexes to table groups_courses_groupings
    $table->addIndexInfo('courseid-groupingid', XMLDB_INDEX_UNIQUE, array('courseid', 'groupingid'));

    /// Launch create table for groups_courses_groupings
    $result = $result && create_table($table);

    /// 9) Add the whole groups_groupings_groups table (as is in 1.8.2+)

    /// Define table groups_groupings_groups to be created
    $table = new XMLDBTable('groups_groupings_groups');

    /// Adding fields to table groups_groupings_groups
    $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
    $table->addFieldInfo('groupingid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
    $table->addFieldInfo('groupid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
    $table->addFieldInfo('timeadded', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');

    /// Adding keys to table groups_groupings_groups
    $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
    $table->addKeyInfo('groupingid', XMLDB_KEY_FOREIGN, array('groupingid'), 'groups_groupings', array('id'));
    $table->addKeyInfo('groupid', XMLDB_KEY_FOREIGN, array('groupid'), 'groups', array('id'));

    /// Adding indexes to table groups_groupings_groups
    $table->addIndexInfo('groupingid-groupid', XMLDB_INDEX_UNIQUE, array('groupingid', 'groupid'));

    /// Launch create table for groups_groupings_groups
    $result = $result && create_table($table);

    /// 10) Insert one record in log_display (module, action, mtable, field) 
    ///     VALUES ('group', 'view', 'groups', 'name') IF it doesn't exist.

    if (!record_exists('log_display', 'module', 'group', 'action', 'view')) {
        $rec = new object();
        $rec->module = 'group';
        $rec->action = 'view';
        $rec->mtable = 'groups';
        $rec->field  = 'name';
        $result = insert_record('log_display', $rec);
    }

    /// 11) PERFORM ALL THE NEEDED MOVEMENTS OF DATA
    
    $db->debug = false; // suppressing because there can be too many
    /// a) get the current groups, foreach one add an entry in groups_courses_groups
    if ($oldgroups = get_records('groups')) {
        foreach ($oldgroups as $oldgroup) {
            $rec = new Object();
            $rec->courseid = $oldgroup->courseid;
            $rec->groupid = $oldgroup->id;
            $rec->timeadded = $oldgroup->timemodified; // I think this is not needed since the field is gone?
            insert_record('groups_courses_groups', $rec);
        }
    }
    $db->debug = true;

    /// TODO, TODO, TODO. At this point is where all the data must be populated to new tables!!

    /// 12) Drop the groups->courseid index

    /// Define index courseid (not unique) to be dropped form groups
    $table = new XMLDBTable('groups');
    $index = new XMLDBIndex('courseid');
    $index->setAttributes(XMLDB_INDEX_NOTUNIQUE, array('courseid'));

    /// Launch drop index courseid
    $result = $result && drop_index($table, $index);

    /// 13) Drop the groups->courseid field

    /// Define field courseid to be dropped from groups
    $field = new XMLDBField('courseid');

    /// Launch drop field courseid
    $result = $result && drop_field($table, $field);

    $db->debug = false;

    if (!$result or !set_config('group_version', $group_version)) {
        error("Upgrade of group system failed!");
    }

    notify(get_string('databasesuccess'), 'green');
    notify(get_string('databaseupgradegroups', '', $group_version), 'green');
}

function undo_groupings() {
    global $CFG;

    if (!$rs = get_recordset_sql("
                    SELECT gpgs.courseid, ggs.groupid 
                    FROM {$CFG->prefix}groups_courses_groupings gpgs,
                         {$CFG->prefix}groups_groupings_groups ggs
                    WHERE gpgs.groupingid = ggs.groupingid")) {
        //strange - did we already remove the tables?
        return;
    }

    $db->debug = false;
    if ($rs->RecordCount() > 0) {
        while ($group = rs_fetch_next_record($rs)) {
            if (!record_exists('groups_courses_groups', 'courseid', $group->courseid, 'groupid', $group->groupid)) {
                insert_record('groups_courses_groups', $group);
            }
        }
    }
    rs_close($rs);
    $db->debug = true;

    delete_records('groups_courses_groupings');
    delete_records('groups_groupings_groups');
    delete_records('groups_groupings');
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

        $adminroot = admin_get_root();
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
