<?php  //$Id$

// PostgreSQL commands for upgrading this question type

function qtype_multichoice_upgrade($oldversion=0) {
    global $CFG;
    $success = true;
    
    if ($success && $oldversion < 2006081900) {
        $success = $success && table_column('question_multichoice', '', 'correctfeedback', 'text', '', '', '');
        $success = $success && table_column('question_multichoice', '', 'partiallycorrectfeedback', 'text', '', '', '');
        $success = $success && table_column('question_multichoice', '', 'incorrectfeedback', 'text', '', '', '');
    }
    
    return $success;
}

?>
