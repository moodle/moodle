<?php
    function question_random_recode_answer($state, $restore) {
        $answer = backup_getid($restore->backup_unique_code,"question_answers",$state->answer);
        if ($answer) {
            return $answer->new_id;
        }
        return '';
    }
?>
