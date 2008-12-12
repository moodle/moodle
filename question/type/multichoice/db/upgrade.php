<?php  // $Id$

// This file keeps track of upgrades to 
// the multichoice qtype plugin
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

function xmldb_qtype_multichoice_upgrade($oldversion=0) {

    global $CFG, $THEME, $db;

    $result = true;

    // This upgrade actually belongs to the description question type,
    // but that does not have a DB upgrade script. Therefore, multichoice
    // is doing it.
    // The need for this is that for a while, descriptions were being created
    // with a defaultgrade of 1, when it shoud be 0. We need to reset them all to 0.
    // See MDL-7925. 
    if ($result && $oldversion < 2006121500) {
        $result = $result && set_field('question', 'defaultgrade', 0,
                'qtype', DESCRIPTION, 'defaultgrade', 1);
    }

    // Add a field so that question authors can choose whether and how the Choices are numbered.
    // Subsequently changed to not create an enum constraint, because it was causing problems -
    // See 2007081700 update.
    if ($result && $oldversion < 2007041300) {
        $table = new XMLDBTable('question_multichoice');
        $field = new XMLDBField('answernumbering');
        $field->setAttributes(XMLDB_TYPE_CHAR, '10', null, XMLDB_NOTNULL, null, null, null, 'abc', 'incorrectfeedback');
        $result = $result && add_field($table, $field);
    }

    // This upgrade actually belongs to the description question type,
    // but that does not have a DB upgrade script. Therefore, multichoice
    // is doing it.
    // The need for this is that for a while, descriptions were being created
    // with a defaultgrade of 1, when it shoud be 0. We need to reset them all to 0.
    // This is re-occurrence of MDL-7925, so we need to do it again. 
    if ($result && $oldversion < 2007072000) {
        require_once($CFG->libdir . '/questionlib.php');
        $result = $result && set_field('question', 'defaultgrade', 0,
                'qtype', DESCRIPTION, 'defaultgrade', 1);
    }

    // Drop enum constraint on 'answernumbering' column, and change ABC to ABCD becuase MySQL
    // sometimes can't cope with things that differ only by case.
    if ($result && $oldversion < 2007081700) {
        if ($result && $oldversion >= 2007041300) {
            $table = new XMLDBTable('question_multichoice');
            $field = new XMLDBField('answernumbering');
            $field->setAttributes(XMLDB_TYPE_CHAR, '10', null, XMLDB_NOTNULL, null, null, null, 'abc', 'incorrectfeedback');
            $result = $result && change_field_enum($table, $field);
        }
        $result = $result && set_field('question_multichoice', 'answernumbering', 'ABCD', 'answernumbering', 'ABC');
    }

    return $result;
}

?>
