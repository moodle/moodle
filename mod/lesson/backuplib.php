<?php //$Id$
/**
 * Lesson's backup routine
 *
 * @version $Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package lesson
 **/
    //This is the "graphical" structure of the lesson mod: 
    //
    //          lesson_default                  lesson ----------------------------|--------------------------|--------------------------|
    //     (UL, pk->id,fk->courseid)         (CL,pk->id)                           |                          |                          | 
    //                                             |                               |                          |                          |
    //                                             |                         lesson_grades              lesson_high_scores         lesson_timer
    //                                             |                  (UL, pk->id,fk->lessonid)    (UL, pk->id,fk->lessonid)   (UL, pk->id,fk->lessonid)
    //                                             |
    //                                             |
    //                                      lesson_pages---------------------------|
    //                                  (CL,pk->id,fk->lessonid)                   |
    //                                             |                               |
    //                                             |                         lesson_branch
    //                                             |                   (UL, pk->id,fk->pageid)
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
                if (backup_mod_selected($preferences,'lesson',$lesson->id)) {
                    $status = lesson_backup_one_mod($bf,$preferences,$lesson);
                }
            }
        }
        return $status;  
    }

    function lesson_backup_one_mod($bf,$preferences,$lesson) {

        global $CFG;
    
        if (is_numeric($lesson)) {
            $lesson = get_record('lesson','id',$lesson);
        }
    
        $status = true;

        //Start mod
        fwrite ($bf,start_tag("MOD",3,true));
        //Print lesson data
        fwrite ($bf,full_tag("ID",4,false,$lesson->id));
        fwrite ($bf,full_tag("MODTYPE",4,false,"lesson"));
        fwrite ($bf,full_tag("NAME",4,false,$lesson->name));
        fwrite ($bf,full_tag("PRACTICE",4,false,$lesson->practice));
        fwrite ($bf,full_tag("MODATTEMPTS",4,false,$lesson->modattempts));
        fwrite ($bf,full_tag("USEPASSWORD",4,false,$lesson->usepassword));
        fwrite ($bf,full_tag("PASSWORD",4,false,$lesson->password));
        fwrite ($bf,full_tag("DEPENDENCY",4,false,$lesson->dependency));
        fwrite ($bf,full_tag("CONDITIONS",4,false,$lesson->conditions));
        fwrite ($bf,full_tag("GRADE",4,false,$lesson->grade));
        fwrite ($bf,full_tag("CUSTOM",4,false,$lesson->custom));
        fwrite ($bf,full_tag("ONGOING",4,false,$lesson->ongoing));
        fwrite ($bf,full_tag("USEMAXGRADE",4,false,$lesson->usemaxgrade));
        fwrite ($bf,full_tag("MAXANSWERS",4,false,$lesson->maxanswers));
        fwrite ($bf,full_tag("MAXATTEMPTS",4,false,$lesson->maxattempts));
        fwrite ($bf,full_tag("REVIEW",4,false,$lesson->review));
        fwrite ($bf,full_tag("NEXTPAGEDEFAULT",4,false,$lesson->nextpagedefault));
        fwrite ($bf,full_tag("FEEDBACK",4,false,$lesson->feedback));
        fwrite ($bf,full_tag("MINQUESTIONS",4,false,$lesson->minquestions));
        fwrite ($bf,full_tag("MAXPAGES",4,false,$lesson->maxpages));
        fwrite ($bf,full_tag("TIMED",4,false,$lesson->timed));
        fwrite ($bf,full_tag("MAXTIME",4,false,$lesson->maxtime));
        fwrite ($bf,full_tag("RETAKE",4,false,$lesson->retake));
        fwrite ($bf,full_tag("ACTIVITYLINK",4,false,$lesson->activitylink));
        fwrite ($bf,full_tag("MEDIAFILE",4,false,$lesson->mediafile));
        fwrite ($bf,full_tag("MEDIAHEIGHT",4,false,$lesson->mediaheight));
        fwrite ($bf,full_tag("MEDIAWIDTH",4,false,$lesson->mediawidth));
        fwrite ($bf,full_tag("MEDIACLOSE",4,false,$lesson->mediaclose));
        fwrite ($bf,full_tag("SLIDESHOW",4,false,$lesson->slideshow));
        fwrite ($bf,full_tag("WIDTH",4,false,$lesson->width));
        fwrite ($bf,full_tag("HEIGHT",4,false,$lesson->height));
        fwrite ($bf,full_tag("BGCOLOR",4,false,$lesson->bgcolor));
        fwrite ($bf,full_tag("DISPLAYLEFT",4,false,$lesson->displayleft));
        fwrite ($bf,full_tag("DISPLAYLEFTIF",4,false,$lesson->displayleftif));
        fwrite ($bf,full_tag("PROGRESSBAR",4,false,$lesson->progressbar));
        fwrite ($bf,full_tag("SHOWHIGHSCORES",4,false,$lesson->highscores));
        fwrite ($bf,full_tag("MAXHIGHSCORES",4,false,$lesson->maxhighscores));
        fwrite ($bf,full_tag("AVAILABLE",4,false,$lesson->available));
        fwrite ($bf,full_tag("DEADLINE",4,false,$lesson->deadline));
        fwrite ($bf,full_tag("TIMEMODIFIED",4,false,$lesson->timemodified));

        //Now we backup lesson pages
        $status = backup_lesson_pages($bf,$preferences,$lesson->id);
        //if we've selected to backup users info, then backup grades, high scores, and timer info
        if ($status) {
            if (backup_userdata_selected($preferences,'lesson',$lesson->id)) {
                if(!backup_lesson_grades($bf, $preferences, $lesson->id)) {
                    return false;
                }
                if (!backup_lesson_high_scores($bf, $preferences, $lesson->id)) {
                    return false;
                }
                if (!backup_lesson_timer($bf, $preferences, $lesson->id)) {
                    return false;
                }
            }
            // back up the default for the course.  There might not be one, but if there
            //  is, there will only be one.
            $status = backup_lesson_default($bf,$preferences);
            //End mod
            if ($status) {
                $status =fwrite ($bf,end_tag("MOD",3,true));
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
                fwrite ($bf,full_tag("LAYOUT",6,false,$page->layout));
                fwrite ($bf,full_tag("DISPLAY",6,false,$page->display));
                fwrite ($bf,full_tag("TIMECREATED",6,false,$page->timecreated));
                fwrite ($bf,full_tag("TIMEMODIFIED",6,false,$page->timemodified));
                fwrite ($bf,full_tag("TITLE",6,false,$page->title));
                fwrite ($bf,full_tag("CONTENTS",6,false,$page->contents));
                //Now we backup lesson answers for this page
                $status = backup_lesson_answers($bf, $preferences, $page->id);
                // backup branch table info for branch tables.
                if ($status && backup_userdata_selected($preferences,'lesson',$lessonid)) {
                    if (!backup_lesson_branch($bf, $preferences, $page->id)) {
                        return false;
                    }
                }
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
                fwrite ($bf,full_tag("ID",8,false,$answer->id));
                fwrite ($bf,full_tag("JUMPTO",8,false,$answer->jumpto));
                fwrite ($bf,full_tag("GRADE",8,false,$answer->grade));
                fwrite ($bf,full_tag("SCORE",8,false,$answer->score));
                fwrite ($bf,full_tag("FLAGS",8,false,$answer->flags));
                fwrite ($bf,full_tag("TIMECREATED",8,false,$answer->timecreated));
                fwrite ($bf,full_tag("TIMEMODIFIED",8,false,$answer->timemodified));
                fwrite ($bf,full_tag("ANSWERTEXT",8,false,$answer->answer));
                fwrite ($bf,full_tag("RESPONSE",8,false,$answer->response));
                //Now we backup any lesson attempts (if student data required)
                if (backup_userdata_selected($preferences,'lesson',$answer->lessonid)) {
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
            $status =fwrite ($bf,start_tag("ATTEMPTS",8,true));
            //Iterate over each attempt
            foreach ($lesson_attempts as $attempt) {
                //Start Attempt
                $status =fwrite ($bf,start_tag("ATTEMPT",9,true));
                //Print attempt contents
                fwrite ($bf,full_tag("USERID",10,false,$attempt->userid));       
                fwrite ($bf,full_tag("RETRY",10,false,$attempt->retry));       
                fwrite ($bf,full_tag("CORRECT",10,false,$attempt->correct));     
                fwrite ($bf,full_tag("USERANSWER",10,false,$attempt->useranswer));
                fwrite ($bf,full_tag("TIMESEEN",10,false,$attempt->timeseen));       
                //End attempt
                $status =fwrite ($bf,end_tag("ATTEMPT",9,true));
            }
            //Write end tag
            $status =fwrite ($bf,end_tag("ATTEMPTS",8,true));
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
            $status =fwrite ($bf,start_tag("GRADES",4,true));
            //Iterate over each grade
            foreach ($grades as $grade) {
                //Start grade
                $status =fwrite ($bf,start_tag("GRADE",5,true));
                //Print grade contents
                fwrite ($bf,full_tag("USERID",6,false,$grade->userid));
                fwrite ($bf,full_tag("GRADE_VALUE",6,false,$grade->grade));
                fwrite ($bf,full_tag("LATE",6,false,$grade->late));
                fwrite ($bf,full_tag("COMPLETED",6,false,$grade->completed));
                //End grade
                $status =fwrite ($bf,end_tag("GRADE",5,true));
            }
            //Write end tag
            $status =fwrite ($bf,end_tag("GRADES",4,true));
        }
        return $status;
    }

    //Backup lesson_branch contents (executed from backup_lesson_pages)
    function backup_lesson_branch($bf,$preferences,$pageno) {

        global $CFG;

        $status = true;

        // get the branches in a set order, the id order
        $lesson_branch = get_records("lesson_branch", "pageid", $pageno, "id");

        //If there is lesson_branch
        if ($lesson_branch) {
            //Write start tag
            $status =fwrite ($bf,start_tag("BRANCHES",6,true));
            //Iterate over each element
            foreach ($lesson_branch as $branch) {
                //Start branch
                $status =fwrite ($bf,start_tag("BRANCH",7,true));
                //Print branch contents
                fwrite ($bf,full_tag("USERID",8,false,$branch->userid));
                fwrite ($bf,full_tag("RETRY",8,false,$branch->retry));
                fwrite ($bf,full_tag("FLAG",8,false,$branch->flag));
                fwrite ($bf,full_tag("TIMESEEN",8,false,$branch->timeseen));
                // END BRANCH
                $status =fwrite ($bf,end_tag("BRANCH",7,true));
            }
            //Write end tag
            $status =fwrite ($bf,end_tag("BRANCHES",6,true));
        }
        return $status;
    }

   //Backup lesson_timer contents (executed from backup_lesson_mods)
    function backup_lesson_timer ($bf,$preferences,$lessonid) {

        global $CFG;

        $status = true;

        $times = get_records("lesson_timer", "lessonid", $lessonid);

        //If there is times
        if ($times) {
            //Write start tag
            $status =fwrite ($bf,start_tag("TIMES",4,true));
            //Iterate over each time
            foreach ($times as $time) {
                //Start time
                $status =fwrite ($bf,start_tag("TIME",5,true));
                //Print time contents
                fwrite ($bf,full_tag("USERID",6,false,$time->userid));
                fwrite ($bf,full_tag("STARTTIME",6,false,$time->starttime));
                fwrite ($bf,full_tag("LESSONTIME",6,false,$time->lessontime));
                //End time
                $status =fwrite ($bf,end_tag("TIME",5,true));
            }
            //Write end tag
            $status =fwrite ($bf,end_tag("TIMES",4,true));
        }
        return $status;
    }
    
    // backup lesson_high_score contents (executed from backup_lesson_mods)
    function backup_lesson_high_scores($bf, $preferences, $lessonid) {
        global $CFG;

        $status = true;

        $highscores = get_records("lesson_high_scores", "lessonid", $lessonid);

        //If there is highscores
        if ($highscores) {
            //Write start tag
            $status =fwrite ($bf,start_tag("HIGHSCORES",4,true));
            //Iterate over each highscore
            foreach ($highscores as $highscore) {
                //Start highscore
                $status =fwrite ($bf,start_tag("HIGHSCORE",5,true));
                //Print highscore contents
                fwrite ($bf,full_tag("USERID",6,false,$highscore->userid));
                fwrite ($bf,full_tag("GRADEID",6,false,$highscore->gradeid));
                fwrite ($bf,full_tag("NICKNAME",6,false,$highscore->nickname));
                //End highscore
                $status =fwrite ($bf,end_tag("HIGHSCORE",5,true));
            }
            //Write end tag
            $status =fwrite ($bf,end_tag("HIGHSCORES",4,true));
        }
        return $status;
    }
    
    // backup lesson_default contents (executed from backup_lesson_mods)
    function backup_lesson_default ($bf,$preferences) {
        global $CFG;

        $status = true;

        //only one default record per course
        $default = get_record("lesson_default", "course", $preferences->backup_course);
        if ($default) {
            //Start mod
            $status =fwrite ($bf,start_tag("DEFAULTS",4,true));            
            //Print default data
            fwrite ($bf,full_tag("PRACTICE",5,false,$default->practice));
            fwrite ($bf,full_tag("MODATTEMPTS",5,false,$default->modattempts));
            fwrite ($bf,full_tag("USEPASSWORD",5,false,$default->usepassword));
            fwrite ($bf,full_tag("PASSWORD",5,false,$default->password));
            fwrite ($bf,full_tag("CONDITIONS",5,false,$default->conditions));
            fwrite ($bf,full_tag("GRADE",5,false,$default->grade));
            fwrite ($bf,full_tag("CUSTOM",5,false,$default->custom));
            fwrite ($bf,full_tag("ONGOING",5,false,$default->ongoing));
            fwrite ($bf,full_tag("USEMAXGRADE",5,false,$default->usemaxgrade));
            fwrite ($bf,full_tag("MAXANSWERS",5,false,$default->maxanswers));
            fwrite ($bf,full_tag("MAXATTEMPTS",5,false,$default->maxattempts));
            fwrite ($bf,full_tag("REVIEW",5,false,$default->review));
            fwrite ($bf,full_tag("NEXTPAGEDEFAULT",5,false,$default->nextpagedefault));
            fwrite ($bf,full_tag("FEEDBACK",5,false,$default->feedback));
            fwrite ($bf,full_tag("MINQUESTIONS",5,false,$default->minquestions));
            fwrite ($bf,full_tag("MAXPAGES",5,false,$default->maxpages));
            fwrite ($bf,full_tag("TIMED",5,false,$default->timed));
            fwrite ($bf,full_tag("MAXTIME",5,false,$default->maxtime));
            fwrite ($bf,full_tag("RETAKE",5,false,$default->retake));
            fwrite ($bf,full_tag("MEDIAHEIGHT",5,false,$default->mediaheight));
            fwrite ($bf,full_tag("MEDIAWIDTH",5,false,$default->mediawidth));
            fwrite ($bf,full_tag("MEDIACLOSE",5,false,$default->mediaclose));
            fwrite ($bf,full_tag("SLIDESHOW",5,false,$default->slideshow));
            fwrite ($bf,full_tag("WIDTH",5,false,$default->width));
            fwrite ($bf,full_tag("HEIGHT",5,false,$default->height));
            fwrite ($bf,full_tag("BGCOLOR",5,false,$default->bgcolor));
            fwrite ($bf,full_tag("DISPLAYLEFT",5,false,$default->displayleft));
            fwrite ($bf,full_tag("DISPLAYLEFTIF",5,false,$default->displayleftif));
            fwrite ($bf,full_tag("PROGRESSBAR",5,false,$default->progressbar));
            fwrite ($bf,full_tag("HIGHSCORES",5,false,$default->highscores));
            fwrite ($bf,full_tag("MAXHIGHSCORES",5,false,$default->maxhighscores));
            $status =fwrite ($bf,end_tag("DEFAULTS",4,true));
        }
        return $status;  
    }
    
    //Return an array of info (name,value)
    function lesson_check_backup_mods($course,$user_data=false,$backup_unique_code,$instances=null) {
        if (!empty($instances) && is_array($instances) && count($instances)) {
            $info = array();
            foreach ($instances as $id => $instance) {
                $info += lesson_check_backup_mods_instances($instance,$backup_unique_code);
            }
            return $info;
        }
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

    //Return an array of info (name,value)
    function lesson_check_backup_mods_instances($instance,$backup_unique_code) {
        //First the course data
        $info[$instance->id.'0'][0] = '<b>'.$instance->name.'</b>';
        $info[$instance->id.'0'][1] = '';

        //Now, if requested, the user_data
        if (!empty($instance->userdata)) {
            $info[$instance->id.'1'][0] = get_string("attempts","lesson");
            if ($ids = lesson_attempts_ids_by_instance ($instance->id)) { 
                $info[$instance->id.'1'][1] = count($ids);
            } else {
                $info[$instance->id.'1'][1] = 0;
            }
        }
        return $info;
    }

    //Return a content encoded to support interactivities linking. Every module
    //should have its own. They are called automatically from the backup procedure.
    function lesson_encode_content_links ($content,$preferences) {

        global $CFG;

        $base = preg_quote($CFG->wwwroot,"/");

        //Link to the list of lessons
        $buscar="/(".$base."\/mod\/lesson\/index.php\?id\=)([0-9]+)/";
        $result= preg_replace($buscar,'$@LESSONINDEX*$2@$',$content);

        //Link to lesson view by moduleid
        $buscar="/(".$base."\/mod\/lesson\/view.php\?id\=)([0-9]+)/";
        $result= preg_replace($buscar,'$@LESSONVIEWBYID*$2@$',$result);

        return $result;
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

    //Returns an array of lesson_submissions id
    function lesson_attempts_ids_by_instance ($instanceid) {

        global $CFG;

        return get_records_sql ("SELECT a.id , a.lessonid
                                 FROM {$CFG->prefix}lesson_attempts a
                                 WHERE a.lessonid = $instanceid");
    }
?>
