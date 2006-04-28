<?php  //$Id$

// PostgreSQL commands for upgrading this question type

function qtype_randomsamatch_upgrade($oldversion=0) {
    global $CFG;

    if ($oldversion < 2006042800) {
        // This is a random questiontype and therefore answers are always shuffled, no need for this field
        modify_database('', 'ALTER TABLE prefix_question_randomsamatch DROP shuffleanswers');
    }

    return true;
}

?>
