<?php // $Id$

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.com                                            //
//                                                                       //
// Copyright (C) 1999 onwards Martin Dougiamas     http://dougiamas.com  //
//           (C) 2001-3001 Eloy Lafuente (stronk7) http://contiento.com  //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

// This library includes all the required functions used to handle the DB
// structure (DDL) independently of the underlying RDBMS in use. All the functions
// rely on the XMLDBDriver classes to be able to generate the correct SQL
// syntax needed by each DB.
//
// To define any structure to be created we'll use the schema defined
// by the XMLDB classes, for tables, fields, indexes, keys and other
// statements instead of direct handling of SQL sentences.
//
// This library should be used, exclusively, by the installation and
// upgrade process of Moodle.
//
// For further documentation, visit http://docs.moodle.org/en/DDL_functions

/// Add required XMLDB constants
require_once($CFG->libdir.'/xmldb/XMLDBConstants.php');

/// Add required XMLDB DB classes
require_once($CFG->libdir.'/xmldb/XMLDBObject.class.php');
require_once($CFG->libdir.'/xmldb/XMLDBFile.class.php');
require_once($CFG->libdir.'/xmldb/XMLDBStructure.class.php');
require_once($CFG->libdir.'/xmldb/XMLDBTable.class.php');
require_once($CFG->libdir.'/xmldb/XMLDBField.class.php');
require_once($CFG->libdir.'/xmldb/XMLDBKey.class.php');
require_once($CFG->libdir.'/xmldb/XMLDBIndex.class.php');
require_once($CFG->libdir.'/xmldb/XMLDBStatement.class.php');

/// Add other libraries
require_once($CFG->libdir.'/xmlize.php');

/**
 * Delete all plugin tables
 * @name string name of plugin, used as table prefix
 * @file string path to install.xml file
 * @feedback boolean
 */
function drop_plugin_tables($name, $file, $feedback=true) {
    global $CFG, $DB;

    // first try normal delete
    if ($DB->get_manager()->delete_tables_from_xmldb_file($file, $feedback)) {
        return true;
    }

    // then try to find all tables that start with name and are not in any xml file
    $used_tables = get_used_table_names();

    $tables = $DB->get_tables();

    /// Iterate over, fixing id fields as necessary
    foreach ($tables as $table) {
        if (in_array($table, $used_tables)) {
            continue;
        }

        // found orphan table --> delete it
        if ($DB->get_manager()->table_exists($table)) {
            $xmldb_table = new XMLDBTable($table);
            $DB->get_manager()->drop_table($xmldb_table, true, $feedback);
        }
    }

    return true;
}

/**
 * Returns names of all known tables == tables that moodle knowns about.
 * @return array of lowercase table names
 */
function get_used_table_names() {
    $table_names = array();
    $dbdirs = get_db_directories();

    foreach ($dbdirs as $dbdir) {
        $file = $dbdir.'/install.xml';

        $xmldb_file = new XMLDBFile($file);

        if (!$xmldb_file->fileExists()) {
            continue;
        }

        $loaded    = $xmldb_file->loadXMLStructure();
        $structure =& $xmldb_file->getStructure();

        if ($loaded and $tables = $structure->getTables()) {
            foreach($tables as $table) {
                $table_names[] = strtolower($table->name);
            }
        }
    }

    return $table_names;
}

/**
 * Returns list of all directories where we expect install.xml files
 * @return array of paths
 */
function get_db_directories() {
    global $CFG;

    $dbdirs = array();

/// First, the main one (lib/db)
    $dbdirs[] = $CFG->libdir.'/db';

/// Now, activity modules (mod/xxx/db)
    if ($plugins = get_list_of_plugins('mod')) {
        foreach ($plugins as $plugin) {
            $dbdirs[] = $CFG->dirroot.'/mod/'.$plugin.'/db';
        }
    }

/// Now, assignment submodules (mod/assignment/type/xxx/db)
    if ($plugins = get_list_of_plugins('mod/assignment/type')) {
        foreach ($plugins as $plugin) {
            $dbdirs[] = $CFG->dirroot.'/mod/assignment/type/'.$plugin.'/db';
        }
    }

/// Now, question types (question/type/xxx/db)
    if ($plugins = get_list_of_plugins('question/type')) {
        foreach ($plugins as $plugin) {
            $dbdirs[] = $CFG->dirroot.'/question/type/'.$plugin.'/db';
        }
    }

/// Now, backup/restore stuff (backup/db)
    $dbdirs[] = $CFG->dirroot.'/backup/db';

/// Now, block system stuff (blocks/db)
    $dbdirs[] = $CFG->dirroot.'/blocks/db';

/// Now, blocks (blocks/xxx/db)
    if ($plugins = get_list_of_plugins('blocks', 'db')) {
        foreach ($plugins as $plugin) {
            $dbdirs[] = $CFG->dirroot.'/blocks/'.$plugin.'/db';
        }
    }

/// Now, course formats (course/format/xxx/db)
    if ($plugins = get_list_of_plugins('course/format', 'db')) {
        foreach ($plugins as $plugin) {
            $dbdirs[] = $CFG->dirroot.'/course/format/'.$plugin.'/db';
        }
    }

/// Now, enrolment plugins (enrol/xxx/db)
    if ($plugins = get_list_of_plugins('enrol', 'db')) {
        foreach ($plugins as $plugin) {
            $dbdirs[] = $CFG->dirroot.'/enrol/'.$plugin.'/db';
        }
    }

/// Now admin report plugins (admin/report/xxx/db)
    if ($plugins = get_list_of_plugins($CFG->admin.'/report', 'db')) {
        foreach ($plugins as $plugin) {
            $dbdirs[] = $CFG->dirroot.'/'.$CFG->admin.'/report/'.$plugin.'/db';
        }
    }

/// Local database changes, if the local folder exists.
    if (file_exists($CFG->dirroot . '/local')) {
        $dbdirs[] = $CFG->dirroot.'/local/db';
    }

    return $dbdirs;
}


// DEPRECATED - to be removed soon

function table_exists($table) {
    global $DB;
    debugging('Deprecated ddllib function used!');
    return $DB->get_manager()->table_exists($table);
}

function field_exists($table, $field) {
    global $DB;
    debugging('Deprecated ddllib function used!');
    return $DB->get_manager()->field_exists($table, $field);
}

function find_index_name($table, $index) {
    global $DB;
    debugging('Deprecated ddllib function used!');
    return $DB->get_manager()->find_index_name($table, $index);
}

function index_exists($table, $index) {
    global $DB;
    debugging('Deprecated ddllib function used!');
    return $DB->get_manager()->index_exists($table, $index);
}

function find_check_constraint_name($table, $field) {
    global $DB;
    debugging('Deprecated ddllib function used!');
    return $DB->get_manager()->find_check_constraint_name($table, $field);
}

function check_constraint_exists($table, $field) {
    global $DB;
    debugging('Deprecated ddllib function used!');
    return $DB->get_manager()->check_constraint_exists($table, $field);
}

function find_key_name($table, $xmldb_key) {
    global $DB;
    debugging('Deprecated ddllib function used!');
    return $DB->get_manager()->find_key_name($table, $xmldb_key);
}

function find_sequence_name($table) {
    global $DB;
    debugging('Deprecated ddllib function used!');
    return $DB->get_manager()->find_sequence_name($table);
}

function drop_table($table, $continue=true, $feedback=true) {
    global $DB;
    debugging('Deprecated ddllib function used!');
    return $DB->get_manager()->drop_table($table, $continue, $feedback);
}

function install_from_xmldb_file($file) {
    global $DB;
    debugging('Deprecated ddllib function used!');
    return $DB->get_manager()->install_from_xmldb_file($file);
}

function create_table($table, $continue=true, $feedback=true) {
    global $DB;
    debugging('Deprecated ddllib function used!');
    return $DB->get_manager()->create_table($table, $continue, $feedback);
}

function create_temp_table($table, $continue=true, $feedback=true) {
    global $DB;
    debugging('Deprecated ddllib function used!');
    return $DB->get_manager()->create_temp_table($table, $continue, $feedback);
}

function rename_table($table, $newname, $continue=true, $feedback=true) {
    global $DB;
    debugging('Deprecated ddllib function used!');
    return $DB->get_manager()->rename_table($table, $newname, $continue, $feedback);
}

function add_field($table, $field, $continue=true, $feedback=true) {
    global $DB;
    debugging('Deprecated ddllib function used!');
    return $DB->get_manager()->add_field($table, $field, $continue, $feedback);
}

function drop_field($table, $field, $continue=true, $feedback=true) {
    global $DB;
    debugging('Deprecated ddllib function used!');
    return $DB->get_manager()->drop_field($table, $field, $continue, $feedback);
}

function change_field_type($table, $field, $continue=true, $feedback=true) {
    global $DB;
    debugging('Deprecated ddllib function used!');
    return $DB->get_manager()->change_field_type($table, $field, $continue, $feedback);
}

function change_field_precision($table, $field, $continue=true, $feedback=true) {
    global $DB;
    debugging('Deprecated ddllib function used!');
    return $DB->get_manager()->change_field_precision($table, $field, $continue, $feedback);
}

function change_field_unsigned($table, $field, $continue=true, $feedback=true) {
    global $DB;
    debugging('Deprecated ddllib function used!');
    return $DB->get_manager()->change_field_unsigned($table, $field, $continue, $feedback);
}

function change_field_notnull($table, $field, $continue=true, $feedback=true) {
    global $DB;
    debugging('Deprecated ddllib function used!');
    return $DB->get_manager()->change_field_notnull($table, $field, $continue, $feedback);
}

function change_field_enum($table, $field, $continue=true, $feedback=true) {
    global $DB;
    debugging('Deprecated ddllib function used!');
    return $DB->get_manager()->change_field_enum($table, $field, $continue, $feedback);
}

function change_field_default($table, $field, $continue=true, $feedback=true) {
    global $DB;
    debugging('Deprecated ddllib function used!');
    return $DB->get_manager()->change_field_default($table, $field, $continue, $feedback);
}

function rename_field($table, $field, $newname, $continue=true, $feedback=true) {
    global $DB;
    debugging('Deprecated ddllib function used!');
    return $DB->get_manager()->rename_field($table, $field, $continue, $feedback);
}

function add_key($table, $key, $continue=true, $feedback=true) {
    global $DB;
    debugging('Deprecated ddllib function used!');
    return $DB->get_manager()->add_key($table, $key, $continue, $feedback);
}

function drop_key($table, $key, $continue=true, $feedback=true) {
    global $DB;
    debugging('Deprecated ddllib function used!');
    return $DB->get_manager()->drop_key($table, $key, $continue, $feedback);
}

function rename_key($table, $key, $newname, $continue=true, $feedback=true) {
    global $DB;
    debugging('Deprecated ddllib function used!');
    return $DB->get_manager()->rename_key($table, $key, $newname, $continue, $feedback);
}

function add_index($table, $index, $continue=true, $feedback=true) {
    global $DB;
    debugging('Deprecated ddllib function used!');
    return $DB->get_manager()->add_index($table, $index, $continue, $feedback);
}

function drop_index($table, $index, $continue=true, $feedback=true) {
    global $DB;
    debugging('Deprecated ddllib function used!');
    return $DB->get_manager()->drop_index($table, $index, $continue, $feedback);
}

function rename_index($table, $index, $newname, $continue=true, $feedback=true) {
    global $DB;
    debugging('Deprecated ddllib function used!');
    return $DB->get_manager()->rename_index($table, $index, $newname, $continue, $feedback);
}

/// DELETED !!

function table_column($table, $oldfield, $field, $type='integer', $size='10',
                      $signed='unsigned', $default='0', $null='not null', $after='') {
    error('table_column() was removed, please use new ddl functions');
}


?>