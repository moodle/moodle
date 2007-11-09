<?php  //$Id$

// This file keeps track of upgrades to 
// the rqp qtype plugin
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

function xmldb_qtype_rqp_upgrade($oldversion=0) {

    global $CFG, $THEME, $db;

    $result = true;

    if ($result && $oldversion < 2006032201) {
        $table = new XMLDBTable('question_rqp_servers');

        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, 10, XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE);
        $table->addFieldInfo('typeid', XMLDB_TYPE_INTEGER, 10, XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, 0);
        $table->addFieldInfo('url', XMLDB_TYPE_CHAR, 255);
        $table->addFieldInfo('can_render', XMLDB_TYPE_INTEGER, 2, XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, 0);
        $table->addFieldInfo('can_author', XMLDB_TYPE_INTEGER, 2, XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, 0);

        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->addKeyInfo('typeid', XMLDB_KEY_FOREIGN, array('typeid'), 'rqp_types', array('id'));

        $result = $result && create_table($table);
    }

    return $result;
}

?>
