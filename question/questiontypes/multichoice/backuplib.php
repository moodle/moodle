<?php
    function multichoice_backup($bf,$preferences,$question,$level=6,$include_answers=true) {

        global $CFG;

        $status = true;

        $multichoices = get_records("question_multichoice","question",$question,"id");
        //If there are multichoices
        if ($multichoices) {
            //Iterate over each multichoice
            foreach ($multichoices as $multichoice) {
                $status = fwrite ($bf,start_tag("MULTICHOICE",$level,true));
                //Print multichoice contents
                fwrite ($bf,full_tag("LAYOUT",$level+1,false,$multichoice->layout));
                fwrite ($bf,full_tag("ANSWERS",$level+1,false,$multichoice->answers));
                fwrite ($bf,full_tag("SINGLE",$level+1,false,$multichoice->single));
                fwrite ($bf,full_tag("SHUFFLEANSWERS",$level+1,false,$randomsamatch->shuffleanswers));
                $status = fwrite ($bf,end_tag("MULTICHOICE",$level,true));
            }
            //Now print question_answers
            if ($include_answers) {
                $status = question_backup_answers($bf,$preferences,$question);
            }
        }
        return $status;
    }
?>
