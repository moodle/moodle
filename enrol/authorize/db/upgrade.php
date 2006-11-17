<?php  //$Id$

function xmldb_enrol_authorize_upgrade($oldversion=0) {

    global $CFG, $THEME, $db;

    $status = true;

    if ($status && $oldversion < 2006101701) {
        $table = new XMLDBTable('enrol_authorize');
        $field = new XMLDBField('cclastfour');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0', 'paymentmethod');
        $status = $status && rename_field($table, $field, 'refundinfo');
    }

    return $status;
}

?>
