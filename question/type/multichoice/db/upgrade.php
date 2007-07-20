<?php  //$Id$

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
        $result = set_field('question', 'defaultgrade', 0,
                'qtype', DESCRIPTION, 'defaultgrade', 1);
    }

    // This upgrade actually belongs to the description question type,
    // but that does not have a DB upgrade script. Therefore, multichoice
    // is doing it.
    // The need for this is that for a while, descriptions were being created
    // with a defaultgrade of 1, when it shoud be 0. We need to reset them all to 0.
    // This is re-occurrence of MDL-7925, so we need to do it again. 
    if ($result && $oldversion < 2006121501) {
        $result = set_field('question', 'defaultgrade', 0,
                'qtype', DESCRIPTION, 'defaultgrade', 1);
    }

    return $result;
}

?>
