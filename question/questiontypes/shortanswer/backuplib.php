<?php
    function shortanswer_backup($bf,$preferences,$question,$level=6,$include_answers=true) {

        global $CFG;

        $status = true;

        $shortanswers = get_records("question_shortanswer","question",$question,"id");
        //If there are shortanswers
        if ($shortanswers) {
            //Iterate over each shortanswer
            foreach ($shortanswers as $shortanswer) {
                $status = fwrite ($bf,start_tag("SHORTANSWER",$level,true));
                //Print shortanswer contents
                fwrite ($bf,full_tag("ANSWERS",$level+1,false,$shortanswer->answers));
                fwrite ($bf,full_tag("USECASE",$level+1,false,$shortanswer->usecase));
                $status = fwrite ($bf,end_tag("SHORTANSWER",$level,true));
            }
            //Now print question_answers
            if ($include_answers) {
                $status = quiz_backup_answers($bf,$preferences,$question);
            }
        }
        return $status;
    }
?>
