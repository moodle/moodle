<?php  //$Id$

// This file keeps track of upgrades to
// the authorize enrol plugin
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

function xmldb_enrol_authorize_upgrade($oldversion=0) {

    global $CFG, $THEME, $db;

    $result = true;

    if ($result && $oldversion < 2006111700) {
        $table = new XMLDBTable('enrol_authorize');
        if (!field_exists($table, new XMLDBField('refundinfo'))) {
            $field = new XMLDBField('cclastfour');
            $field->setAttributes(XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0', 'paymentmethod');
            $result = $result && rename_field($table, $field, 'refundinfo');
        }
    }

    if ($result && $oldversion < 2006112900) {
        if (isset($CFG->an_login)) {
            if (empty($CFG->an_login)) {
                unset_config('an_login');
            }
            else {
                $result = $result && set_config('an_login', rc4encrypt($CFG->an_login), 'enrol/authorize') && unset_config('an_login');
            }
        }
        if (isset($CFG->an_tran_key)) {
            if (empty($CFG->an_tran_key)) {
                unset_config('an_tran_key');
            }
            else {
                $result = $result && set_config('an_tran_key', rc4encrypt($CFG->an_tran_key), 'enrol/authorize') && unset_config('an_tran_key');
            }
        }
        if (isset($CFG->an_password)) {
            if (empty($CFG->an_password)) {
                unset_config('an_password');
            }
            else {
                $result = $result && set_config('an_password', rc4encrypt($CFG->an_password), 'enrol/authorize') && unset_config('an_password');
            }
        }
    }


    if ($result && $oldversion < 2006112903) {
        /// enrol_authorize.transid
        /// Define index transid (not unique) to be dropped form enrol_authorize
        $table = new XMLDBTable('enrol_authorize');
        $index = new XMLDBIndex('transid');
        $index->setAttributes(XMLDB_INDEX_NOTUNIQUE, array('transid'));
        drop_index($table, $index);

        /// Changing precision of field transid on table enrol_authorize to (20)
        $table = new XMLDBTable('enrol_authorize');
        $field = new XMLDBField('transid');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '20', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0', 'userid');
        change_field_precision($table, $field);

        /// Launch add index transid again
        $table = new XMLDBTable('enrol_authorize');
        $index = new XMLDBIndex('transid');
        $index->setAttributes(XMLDB_INDEX_NOTUNIQUE, array('transid'));
        add_index($table, $index);

        /// enrol_authorize_refunds.transid
        /// Define index transid (not unique) to be dropped form enrol_authorize_refunds
        $table = new XMLDBTable('enrol_authorize_refunds');
        $index = new XMLDBIndex('transid');
        $index->setAttributes(XMLDB_INDEX_NOTUNIQUE, array('transid'));
        drop_index($table, $index);

        /// Changing precision of field transid on table enrol_authorize_refunds to (20)
        $table = new XMLDBTable('enrol_authorize_refunds');
        $field = new XMLDBField('transid');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '20', XMLDB_UNSIGNED, null, null, null, null, '0', 'amount');
        change_field_precision($table, $field);

        /// Launch add index transid again
        $table = new XMLDBTable('enrol_authorize_refunds');
        $index = new XMLDBIndex('transid');
        $index->setAttributes(XMLDB_INDEX_NOTUNIQUE, array('transid'));
        add_index($table, $index);
    }

    return $result;
}

?>
