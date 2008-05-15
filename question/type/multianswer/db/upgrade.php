<?php  // $Id$

// This file keeps track of upgrades to 
// the multianswer qtype plugin
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

function xmldb_qtype_multianswer_upgrade($oldversion=0) {

    global $CFG, $THEME, $db;

    $result = true;

/// And upgrade begins here. For each one, you'll need one 
/// block of code similar to the next one. Please, delete 
/// this comment lines once this file start handling proper
/// upgrade code.

/// if ($result && $oldversion < YYYYMMDD00) { //New version in version.php
///     $result = result of "/lib/ddllib.php" function calls
/// }

    if ($result && $oldversion < 2008050800) {
        question_multianswer_fix_subquestion_parents_and_categories();
    }

    return $result;
}

/**
 * Due to MDL-14750, subquestions of multianswer questions restored from backup will
 * have the wrong parent, and due to MDL-10899 subquestions of multianswer questions
 * that have been moved between categories will be in the wrong category, This code fixes these up.
 */
function question_multianswer_fix_subquestion_parents_and_categories() {
    global $CFG;

    $result = true;
    $rs = get_recordset_sql('SELECT q.id, q.category, qma.sequence FROM ' . $CFG->prefix .
            'question q JOIN ' . $CFG->prefix . 'question_multianswer qma ON q.id = qma.question');
    if ($rs) {
        while ($q = rs_fetch_next_record($rs)) {
            if (!empty($q->sequence)) {
                $result = $result && execute_sql('UPDATE ' . $CFG->prefix . 'question' .
                        ' SET parent = ' . $q->id . ', category = ' . $q->category .
                        ' WHERE id IN (' . $q->sequence . ') AND parent <> 0');
            }
        }
        rs_close($rs);
    } else {
        $result = false;
    }
    return $result;
}
?>
