<?php  // $Id$

// THIS FILE IS DEPRECATED!  PLEASE DO NOT MAKE CHANGES TO IT!
//
// IT IS USED ONLY FOR UPGRADES FROM BEFORE MOODLE 1.7, ALL 
// LATER CHANGES SHOULD USE upgrade.php IN THIS DIRECTORY.

// MySQL commands for upgrading this question type

function qtype_multichoice_upgrade($oldversion=0) {
    global $CFG;
    $success = true;
    
    if ($success && $oldversion < 2006081900) {
        $success = $success && table_column('question_multichoice', '', 'correctfeedback', 'text', '', '', '');
        $success = $success && table_column('question_multichoice', '', 'partiallycorrectfeedback', 'text', '', '', '');
        $success = $success && table_column('question_multichoice', '', 'incorrectfeedback', 'text', '', '', '');
    }

    //////  DO NOT ADD NEW THINGS HERE!!  USE upgrade.php and the lib/ddllib.php functions.

    return $success;
}

?>
