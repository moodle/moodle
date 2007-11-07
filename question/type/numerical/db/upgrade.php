<?php  // $Id$

// This file keeps track of upgrades to 
// the numerical qtype plugin
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

function xmldb_qtype_numerical_upgrade($oldversion=0) {

    global $CFG, $THEME, $db;

    $result = true;

    // In numerical questions, we are changing the 'match anything' answer
    // from the empty string to *, to be like short answer questions.
    if ($result && $oldversion < 2006121500) {
        $result = set_field_select('question_answers', 'answer', '*',
            sql_compare_text('answer') . " = '" . sql_empty() . "' AND question IN (SELECT id FROM {$CFG->prefix}question WHERE qtype = '" . NUMERICAL . "')");
    }

    return $result;
}

?>
