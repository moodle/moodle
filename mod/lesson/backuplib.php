<?PHP //$Id$
    //This php script contains all the stuff to backup/restore
    //lesson mods

    //This is the "graphical" structure of the lesson mod: 
    //
    //                                          lesson ----------------------------|
    //                                       (CL,pk->id)                           | 
    //                                             |                               |
    //                                             |                         lesson_grades
    //                                             |                  (UL, pk->id,fk->lessonid)
    //                                      lesson_pages
    //                                  (CL,pk->id,fk->lessonid)
    //                                             |
    //                                             |
    //                                             |
    //                                       lesson_answers
    //                                    (CL,pk->id,fk->pageid)
    //                                             |
    //                                             |
    //                                             |
    //                                       lesson_attempts
    //                                  (UL,pk->id,fk->answerid)
    //
    // Meaning: pk->primary key field of the table
    //          fk->foreign key to link with parent
    //          nt->nested field (recursive data)
    //          CL->course level info
    //          UL->user level info
    //          files->table may have files)
    //
    //-----------------------------------------------------------

    //This function executes all the backup procedure about this mod
    function lesson_backup_mods($bf, $preferences) {

        global $CFG;

        $status = true;

        //Iterate over lesson table
        $lessons = get_records("lesson", "course", $preferences->backup_course, "id");
        if ($lessons) {
            foreach ($lessons as $lesson) {
                //Start mod
                fwrite ($bf,start_tag("MOD",3,true));
                //Print lesson data
                fwrite ($bf,full_tag("ID",4,false,$lesson->id));
                fwrite ($bf,full_tag("MODTYPE",4,false,"lesson"));
                fwrite ($bf,full_tag("NAME",4,false,$lesson->name));
                fwrite ($bf,full_tag("GRADE",4,false,$lesson->grade));
                fwrite ($bf,full_tag("USEMAXGRADE",4,false,$lesson->usemaxgrade));
                fwrite ($bf,full_tag("MAXANSWERS",4,false,$lesson->maxanswers));
                fwrite ($bf,full_tag("MAXATTEMPTS",4,false,$lesson->maxattempts));
                fwrite ($bf,full_tag("NEXTPAGEDEFAULT",4,false,$lesson->nextpagedefault));
                fwrite ($bf,full_tag("MINQUESTIONS",4,false,$lesson->minquestions));
                fwrite ($bf,full_tag("MAXPAGES",4,false,$lesson->maxpages));
                fwrite ($bf,full_tag("RETAKE",4,false,$lesson->retake));
                fwrite ($bf,full_tag("AVAILABLE",4,false,$lesson->available));
                fwrite ($bf,full_tag("DEADLINE",4,false,$lesson->deadline));
                fwrite ($bf,full_tag("TIMEMODIFIED",4,false,$lesson->timemodified));
                //Now we backup lesson pages
                $status = backup_lesson_pages($bf,$preferences,$lesson->id);
                //if we've selected to backup users info, then backup grades
                if ($status) {
                    if ($preferences->mods["lesson"]->userinfo) {
                        $status = backup_lesson_grades($bf, $preferences, $lesson->id);
                    }
                //End mod
                $status =fwrite ($bf,end_tag("MOD",3,true));
                }
            }
        }
        return $status;  
    }

    //Backup lesson_pages contents (executed from lesson_backup_mods)
    function backup_lesson_pages ($bf, $preferences, $lessonid) {

        global $CFG;

        $status = true;

        // run through the pages in their logical order, get the first page
        if ($page = get_record_select("lesson_pages", "lessonid = $lessonid AND prevpageid = 0")) {
            //Write start tag
            $status =fwrite ($bf,start_tag("PAGES",4,true));
            //Iterate over each page
            while (true) {
                //Start of page
                $status =fwrite ($bf,start_tag("PAGE",5,true));
                //Print page contents (prevpageid and nextpageid not needed)
                fwrite ($bf,full_tag("PAGEID",6,false,$page->id)); // needed to fix (absolute) jumps
                fwrite ($bf,full_tag("QTYPE",6,false,$page->qtype));
                fwrite ($bf,full_tag("QOPTION",6,false,$page->qoption));
                fwrite ($bf,full_tag("TIMECREATED",6,false,$page->timecreated));
                fwrite ($bf,full_tag("TIMEMODIFIED",6,false,$page->timemodified));
                fwrite ($bf,full_tag("TITLE",6,false,$page->title));
                fwrite ($bf,full_tag("CONTENTS",6,false,$page->contents));
                //Now we backup lesson answers for this page
                $status = backup_lesson_answers($bf, $preferences, $page->id);
                //End of page
                $status =fwrite ($bf,end_tag("PAGE",5,true));
                // move to the next (logical) page
                if ($page->nextpageid) {
                    if (!$page = get_record("lesson_pages", "id", $page->nextpageid)) {
                        error("Lesson Backup: Next page not found!");
                    }
                } else {
                    // last page reached
                    break;
                }

            }
            //Write end tag
            $status =fwrite ($bf,end_tag("PAGES",4,true));
        }
        return $status;
    }

    //Backup lesson_answers contents (executed from backup_lesson_pages)
    function backup_lesson_answers($bf,$preferences,$pageno) {

        global $CFG;

        $status = true;

        // get the answers in a set order, the id order
        $lesson_answers = get_records("lesson_answers", "pageid", $pageno, "id");

        //If there is lesson_answers
        if ($lesson_answers) {
            //Write start tag
            $status =fwrite ($bf,start_tag("ANSWERS",6,true));
            //Iterate over each element
            foreach ($lesson_answers as $answer) {
                //Start answer
                $status =fwrite ($bf,start_tag("ANSWER",7,true));
                //Print answer contents
                fwrite ($bf,full_tag("JUMPTO",8,false,$answer->jumpto));
                fwrite ($bf,full_tag("GRADE",8,false,$answer->grade));
                fwrite ($bf,full_tag("FLAGS",8,false,$answer->flags));
                fwrite ($bf,full_tag("TIMECREATED",8,false,$answer->timecreated));
                fwrite ($bf,full_tag("TIMEMODIFIED",8,false,$answer->timemodified));
                fwrite ($bf,full_tag("ANSWERTEXT",8,false,$answer->answer));
                fwrite ($bf,full_tag("RESPONSE",8,false,$answer->response));
                //Now we backup any lesson attempts (if student data required)
                if ($preferences->mods["lesson"]->userinfo) {
                    $status = backup_lesson_attempts($bf,$preferences,$answer->id);
                }
                //End rubric
                $status =fwrite ($bf,end_tag("ANSWER",7,true));
            }
            //Write end tag
            $status =fwrite ($bf,end_tag("ANSWERS",6,true));
        }
        return $status;
    }

    //Backup lesson_attempts contents (executed from lesson_backup_answers)
    function backup_lesson_attempts ($bf,$preferences,$answerid) {

        global $CFG;

        $status = true;

        $lesson_attempts = get_records("lesson_attempts","answerid", $answerid);
        //If there are attempts
        if ($lesson_attempts) {
            //Write start tag
            $status =fwrite ($bf,start_tag("ATTEMPTS",4,true));
            //Iterate over each attempt
            foreach ($lesson_attempts as $attempt) {
                //Start Attempt
                $status =fwrite ($bf,start_tag("ATTEMPT",5,true));
                //Print attempt contents
                fwrite ($bf,full_tag("USERID",6,false,$attempt->userid));       
                fwrite ($bf,full_tag("RETRY",6,false,$attempt->retry));       
                fwrite ($bf,full_tag("CORRECT",6,false,$attempt->correct));       
                fwrite ($bf,full_tag("TIMESEEN",6,false,$attempt->timeseen));       
                //End attempt
                $status =fwrite ($bf,end_tag("ATTEMPT",5,true));
            }
            //Write end tag
            $status =fwrite ($bf,end_tag("ATTEMPTS",4,true));
        }
        return $status;
    }


   //Backup lesson_grades contents (executed from backup_lesson_mods)
    function backup_lesson_grades ($bf,$preferences,$lessonid) {

        global $CFG;

        $status = true;

        $grades = get_records("lesson_grades", "lessonid", $lessonid);

        //If there is grades
        if ($grades) {
            //Write start tag
            $status =fwrite ($bf,start_tag("GRADES",8,true));
            //Iterate over each grade
            foreach ($grades as $grade) {
                //Start grade
                $status =fwrite ($bf,start_tag("GRADE",9,true));
                //Print grade contents
                fwrite ($bf,full_tag("USERID",10,false,$grade->userid));
                fwrite ($bf,full_tag("GRADE_VALUE",10,false,$grade->grade));
                fwrite ($bf,full_tag("LATE",10,false,$grade->late));
                fwrite ($bf,full_tag("COMPLETED",10,false,$grade->completed));
                //End comment
                $status =fwrite ($bf,end_tag("GRADE",9,true));
            }
            //Write end tag
            $status =fwrite ($bf,end_tag("GRADES",8,true));
        }
        return $status;
    }
    //Return an array of info (name,value)
    function lesson_check_backup_mods($course,$user_data=false,$backup_unique_code) {
        //First the course data
        $info[0][0] = get_string("modulenameplural","lesson");
        if ($ids = lesson_ids($course)) {
            $info[0][1] = count($ids);
        } else {
            $info[0][1] = 0;
        }

        //Now, if requested, the user_data
        if ($user_data) {
            $info[1][0] = get_string("attempts","lesson");
            if ($ids = lesson_attempts_ids_by_course ($course)) { 
                $info[1][1] = count($ids);
            } else {
                $info[1][1] = 0;
            }
        }
        return $info;
    }



    // INTERNAL FUNCTIONS. BASED IN THE MOD STRUCTURE

    //Returns an array of lesson id 
    function lesson_ids ($course) {

        global $CFG;

        return get_records_sql ("SELECT l.id, l.course
                                 FROM {$CFG->prefix}lesson l
                                 WHERE l.course = '$course'");
    }
    
    //Returns an array of lesson_submissions id
    function lesson_attempts_ids_by_course ($course) {

        global $CFG;

        return get_records_sql ("SELECT a.id , a.lessonid
                                 FROM {$CFG->prefix}lesson_attempts a,
                                      {$CFG->prefix}lesson l
                                 WHERE l.course = '$course' AND
                                       a.lessonid = l.id");
    }
?>
