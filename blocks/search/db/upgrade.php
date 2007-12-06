<?php  //$Id$

// This file keeps track of upgrades to 
// the search block
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

function xmldb_block_search_upgrade($oldversion=0) {

    global $CFG, $THEME, $db;

    $result = true;

/// And upgrade begins here. For each one, you'll need one 
/// block of code similar to the next one. Please, delete 
/// this comment lines once this file start handling proper
/// upgrade code.

/// if ($result && $oldversion < YYYYMMDD00) { //New version in version.php
///     $result = result of "/lib/ddllib.php" function calls
/// }
    if ($result && $oldversion < 2007071302) {

    /// Define table search_documents to be created
        $table = new XMLDBTable('search_documents');

    /// Drop it if it existed before

        drop_table($table, true, false);

    /// Adding fields to table search_documents
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->addFieldInfo('docid', XMLDB_TYPE_CHAR, '32', null, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('doctype', XMLDB_TYPE_CHAR, '32', null, XMLDB_NOTNULL, null, null, null, 'none');
        $table->addFieldInfo('itemtype', XMLDB_TYPE_CHAR, '32', null, XMLDB_NOTNULL, null, null, null, 'standard');
        $table->addFieldInfo('title', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, null);
        $table->addFieldInfo('url', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, null);
        $table->addFieldInfo('docdate', XMLDB_TYPE_DATETIME, null, null, XMLDB_NOTNULL, null, null, null);
        $table->addFieldInfo('updated', XMLDB_TYPE_DATETIME, null, null, XMLDB_NOTNULL, null, null, null);
        $table->addFieldInfo('courseid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('groupid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');

    /// Adding keys to table search_documents
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));

    /// Adding indexes to table search_documents
        $table->addIndexInfo('mdl_search_docid', XMLDB_INDEX_NOTUNIQUE, array('docid'));
        $table->addIndexInfo('mdl_search_doctype', XMLDB_INDEX_NOTUNIQUE, array('doctype'));
        $table->addIndexInfo('mdl_search_itemtype', XMLDB_INDEX_NOTUNIQUE, array('itemtype'));

    /// Launch create table for search_documents
        $result = $result && create_table($table);
    }

/// Rename table search_documents to block_search_documents and 
/// fix some defaults (MDL-10572)
    if ($result && $oldversion < 2007081100) {

    /// Define table search_documents to be renamed to block_search_documents
        $table = new XMLDBTable('search_documents');

    /// Launch rename table for block_search_documents
        $result = $result && rename_table($table, 'block_search_documents');

    /// Changing the default of field doctype on table block_search_documents to none
        $table = new XMLDBTable('block_search_documents');
        $field = new XMLDBField('doctype');
        $field->setAttributes(XMLDB_TYPE_CHAR, '32', null, XMLDB_NOTNULL, null, null, null, 'none', 'docid');

    /// Launch change of default for field doctype
        $result = $result && change_field_default($table, $field);

   /// Changing the default of field itemtype on table block_search_documents to standard
        $table = new XMLDBTable('block_search_documents');
        $field = new XMLDBField('itemtype');
        $field->setAttributes(XMLDB_TYPE_CHAR, '32', null, XMLDB_NOTNULL, null, null, null, 'standard', 'doctype');

    /// Launch change of default for field itemtype
        $result = $result && change_field_default($table, $field);

    /// Changing the default of field title on table block_search_documents to drop it
        $table = new XMLDBTable('block_search_documents');
        $field = new XMLDBField('title');
        $field->setAttributes(XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, null, null, 'itemtype');

    /// Launch change of default for field title
        $result = $result && change_field_default($table, $field);

    /// Changing the default of field url on table block_search_documents to drop it
        $table = new XMLDBTable('block_search_documents');
        $field = new XMLDBField('url');
        $field->setAttributes(XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, null, null, 'title');

    /// Launch change of default for field url
        $result = $result && change_field_default($table, $field);
    }

    if ($result && $oldversion < 2007112700) {
    
    /*
    /// Truncate the block_search_documents table
        execute_sql("TRUNCATE TABLE {$CFG->prefix}block_search_documents", true);
    
    /// Changing type of field docdate on table block_search_documents to int
        $table = new XMLDBTable('block_search_documents');
        $field = new XMLDBField('docdate');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0', 'url');

    /// Launch change of type for field docdate
        $result = $result && change_field_type($table, $field);
        
    /// Changing type of field updated on table block_search_documents to int
        $table = new XMLDBTable('block_search_documents');
        $field = new XMLDBField('updated');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0', 'docdate');

    /// Launch change of type for field updated
        $result = $result && change_field_type($table, $field);
    */    
        
        
    /// MDL-12352, postgres can not cope with change_field_type(), so dropping the fields and adding again
    
    /// Define field docdate to be dropped from block_search_documents
        $table = new XMLDBTable('block_search_documents');
        $field = new XMLDBField('docdate');

    /// Launch drop field docdate
        $result = $result && drop_field($table, $field);
        
    /// Define field updated to be dropped from block_search_documents
        $field = new XMLDBField('updated');

    /// Launch drop field updated
        $result = $result && drop_field($table, $field);

    /// Define field docdate to be added to block_search_documents
        $field = new XMLDBField('docdate');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0', 'url');

    /// Launch add field docdate
        $result = $result && add_field($table, $field);      
        
    /// Define field updated to be added to block_search_documents
        $field = new XMLDBField('updated');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0', 'docdate');

    /// Launch add field updated
        $result = $result && add_field($table, $field);  
    }
    
    return $result;
}

?>