<?php  //$Id$

// This file keeps track of upgrades to 
// the scorm module
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

function xmldb_scorm_upgrade($oldversion=0) {

    global $CFG, $THEME, $db;

    $result = true;

    if ($result && $oldversion < 2006103100) {
        /// Create the new sco optionals data table

        /// Define table scorm_scoes_data to be created
        $table = new XMLDBTable('scorm_scoes_data');

        /// Adding fields to table scorm_scoes_data
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->addFieldInfo('scoid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('value', XMLDB_TYPE_TEXT, 'small', null, XMLDB_NOTNULL, null, null, null, null);

        /// Adding keys to table scorm_scoes_data
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));

        /// Adding indexes to table scorm_scoes_data
        $table->addIndexInfo('scoid', XMLDB_INDEX_NOTUNIQUE, array('scoid'));

        /// Launch create table for scorm_scoes_data
        $result = $result && create_table($table);

        /// The old fields used in scorm_scoes
        $fields = array('parameters' => '',
                        'prerequisites' => '',
                        'maxtimeallowed' => '',
                        'timelimitaction' => '',
                        'datafromlms' => '',
                        'masteryscore' => '',
                        'next' => '0',
                        'previous' => '0');

        /// Retrieve old datas
        if ($olddatas = get_records('scorm_scoes')) {
            foreach ($olddatas as $olddata) {
                $newdata = new stdClass();
                $newdata->scoid = $olddata->id;
                foreach ($fields as $field => $value) {
                    if ($olddata->$field != $value) {
                        $newdata->name = addslashes($field);
                        $newdata->value = addslashes($olddata->$field);
                        $id = insert_record('scorm_scoes_data', $newdata);
                        $result = $result && ($id != 0);
                    }
                }
            }
        }

        /// Remove no more used fields
        $table = new XMLDBTable('scorm_scoes');

        foreach ($fields as $field => $value) {
            $field = new XMLDBField($field);
            $result = $result && drop_field($table, $field);
        }
    }

    return $result;
}

?>
