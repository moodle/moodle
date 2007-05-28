<?php //$Id$
/**
 * This php script contains all the stuff to restore lesson mods
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

    //This function executes all the restore procedure about this mod
    function lesson_restore_mods($mod,$restore) {

        global $CFG;

        $status = true;

        //Get record from backup_ids
        $data = backup_getid($restore->backup_unique_code,$mod->modtype,$mod->id);

        if ($data) {
            //Now get completed xmlized object
            $info = $data->info;
            //if necessary, write to restorelog and adjust date/time fields
            if ($restore->course_startdateoffset) {
                restore_log_date_changes('Lesson', $restore, $info['MOD']['#'], array('AVAILABLE', 'DEADLINE'));
            }
            //traverse_xmlize($info);                                                              //Debug
            //print_object ($GLOBALS['traverse_array']);                                           //Debug
            //$GLOBALS['traverse_array']="";                                                       //Debug

            //Now, build the lesson record structure
            $lesson->course = $restore->course_id;
            $lesson->name = backup_todb($info['MOD']['#']['NAME']['0']['#']);
            $lesson->practice = backup_todb($info['MOD']['#']['PRACTICE']['0']['#']);
            $lesson->modattempts = backup_todb($info['MOD']['#']['MODATTEMPTS']['0']['#']);
            $lesson->usepassword = backup_todb($info['MOD']['#']['USEPASSWORD']['0']['#']);
            $lesson->password = backup_todb($info['MOD']['#']['PASSWORD']['0']['#']);            
            $lesson->dependency = isset($info['MOD']['#']['DEPENDENCY']['0']['#'])?backup_todb($info['MOD']['#']['DEPENDENCY']['0']['#']):'';
            $lesson->conditions = isset($info['MOD']['#']['CONDITIONS']['0']['#'])?backup_todb($info['MOD']['#']['CONDITIONS']['0']['#']):'';
            $lesson->grade = backup_todb($info['MOD']['#']['GRADE']['0']['#']);
            $lesson->custom = backup_todb($info['MOD']['#']['CUSTOM']['0']['#']);
            $lesson->ongoing = backup_todb($info['MOD']['#']['ONGOING']['0']['#']);
            $lesson->usemaxgrade = backup_todb($info['MOD']['#']['USEMAXGRADE']['0']['#']);
            $lesson->maxanswers = backup_todb($info['MOD']['#']['MAXANSWERS']['0']['#']);
            $lesson->maxattempts = backup_todb($info['MOD']['#']['MAXATTEMPTS']['0']['#']);
            $lesson->review = backup_todb($info['MOD']['#']['REVIEW']['0']['#']);
            $lesson->nextpagedefault = backup_todb($info['MOD']['#']['NEXTPAGEDEFAULT']['0']['#']);
            $lesson->feedback = isset($info['MOD']['#']['FEEDBACK']['0']['#'])?backup_todb($info['MOD']['#']['FEEDBACK']['0']['#']):'';
            $lesson->minquestions = backup_todb($info['MOD']['#']['MINQUESTIONS']['0']['#']);
            $lesson->maxpages = backup_todb($info['MOD']['#']['MAXPAGES']['0']['#']);
            $lesson->timed = backup_todb($info['MOD']['#']['TIMED']['0']['#']);
            $lesson->maxtime = backup_todb($info['MOD']['#']['MAXTIME']['0']['#']);
            $lesson->retake = backup_todb($info['MOD']['#']['RETAKE']['0']['#']);
            $lesson->activitylink = isset($info['MOD']['#']['ACTIVITYLINK']['0']['#'])?backup_todb($info['MOD']['#']['ACTIVITYLINK']['0']['#']):'';
            $lesson->mediafile = isset($info['MOD']['#']['MEDIAFILE']['0']['#'])?backup_todb($info['MOD']['#']['MEDIAFILE']['0']['#']):'';
            $lesson->mediaheight = isset($info['MOD']['#']['MEDIAHEIGHT']['0']['#'])?backup_todb($info['MOD']['#']['MEDIAHEIGHT']['0']['#']):'';
            $lesson->mediawidth = isset($info['MOD']['#']['MEDIAWIDTH']['0']['#'])?backup_todb($info['MOD']['#']['MEDIAWIDTH']['0']['#']):'';
            $lesson->mediaclose = isset($info['MOD']['#']['MEDIACLOSE']['0']['#'])?backup_todb($info['MOD']['#']['MEDIACLOSE']['0']['#']):'';
            $lesson->slideshow = backup_todb($info['MOD']['#']['SLIDESHOW']['0']['#']);
            $lesson->width = backup_todb($info['MOD']['#']['WIDTH']['0']['#']);
            $lesson->height = backup_todb($info['MOD']['#']['HEIGHT']['0']['#']);
            $lesson->bgcolor = backup_todb($info['MOD']['#']['BGCOLOR']['0']['#']);
            $lesson->displayleft = isset($info['MOD']['#']['DISPLAYLEFT']['0']['#'])?backup_todb($info['MOD']['#']['DISPLAYLEFT']['0']['#']):'';
            $lesson->displayleftif = isset($info['MOD']['#']['DISPLAYLEFTIF']['0']['#'])?backup_todb($info['MOD']['#']['DISPLAYLEFTIF']['0']['#']):'';
            $lesson->progressbar = isset($info['MOD']['#']['PROGRESSBAR']['0']['#'])?backup_todb($info['MOD']['#']['PROGRESSBAR']['0']['#']):'';
            $lesson->highscores = backup_todb($info['MOD']['#']['SHOWHIGHSCORES']['0']['#']);
            $lesson->maxhighscores = backup_todb($info['MOD']['#']['MAXHIGHSCORES']['0']['#']);
            $lesson->available = backup_todb($info['MOD']['#']['AVAILABLE']['0']['#']);
            $lesson->deadline = backup_todb($info['MOD']['#']['DEADLINE']['0']['#']);
            $lesson->timemodified = backup_todb($info['MOD']['#']['TIMEMODIFIED']['0']['#']);

            //The structure is equal to the db, so insert the lesson
            $newid = insert_record("lesson", $lesson);

            //Do some output
            if (!defined('RESTORE_SILENTLY')) {
                echo "<li>".get_string("modulename","lesson")." \"".format_string(stripslashes($lesson->name),true)."\"</li>";
            }
            backup_flush(300);

            if ($newid) {
                //We have the newid, update backup_ids
                backup_putid($restore->backup_unique_code,$mod->modtype,
                             $mod->id, $newid);
                //We have to restore the lesson pages which are held in their logical order...
                $userdata = restore_userdata_selected($restore,"lesson",$mod->id);
                $status = lesson_pages_restore_mods($newid,$info,$restore,$userdata);
                //...and the user grades, high scores, and timer (if required)
                if ($status) {
                    if ($userdata) {
                        if(!lesson_grades_restore_mods($newid,$info,$restore)) {
                            return false;
                        }
                        if (!lesson_high_scores_restore_mods($newid,$info,$restore)) {
                            return false;
                        }
                        if (!lesson_timer_restore_mods($newid,$info,$restore)) {
                            return false;
                        }
                    }
                    // restore the default for the course.  Only do this once by checking for an id for lesson_default
                    $lessondefault = backup_getid($restore->backup_unique_code,'lesson_default',$restore->course_id);
                    if (!$lessondefault) {
                        $status = lesson_default_restore_mods($info,$restore);
                    }
                    
                }
            } else {
                $status = false;
            }
        } else {
            $status = false;
        }
        return $status;
    }

    //This function restores the lesson_pages
    function lesson_pages_restore_mods($lessonid,$info,$restore,$userdata=false) {

        global $CFG;

        $status = true;

        //Get the lesson_elements array
        $pages = $info['MOD']['#']['PAGES']['0']['#']['PAGE'];

        //Iterate over lesson pages (they are held in their logical order)
        $prevpageid = 0;
        for($i = 0; $i < sizeof($pages); $i++) {
            $page_info = $pages[$i];
            //traverse_xmlize($ele_info);                                                          //Debug
            //print_object ($GLOBALS['traverse_array']);                                           //Debug
            //$GLOBALS['traverse_array']="";                                                       //Debug

            //We'll need this later!!
            $oldid = backup_todb($page_info['#']['PAGEID']['0']['#']);

            //Now, build the lesson_pages record structure
            $page->lessonid = $lessonid;
            $page->prevpageid = $prevpageid;
            $page->qtype = backup_todb($page_info['#']['QTYPE']['0']['#']);
            $page->qoption = backup_todb($page_info['#']['QOPTION']['0']['#']);
            $page->layout = backup_todb($page_info['#']['LAYOUT']['0']['#']);
            $page->display = backup_todb($page_info['#']['DISPLAY']['0']['#']);
            $page->timecreated = backup_todb($page_info['#']['TIMECREATED']['0']['#']);
            $page->timemodified = backup_todb($page_info['#']['TIMEMODIFIED']['0']['#']);
            $page->title = backup_todb($page_info['#']['TITLE']['0']['#']);
            $page->contents = backup_todb($page_info['#']['CONTENTS']['0']['#']);

            //The structure is equal to the db, so insert the lesson_pages
            $newid = insert_record ("lesson_pages",$page);

            //Fix the forwards link of the previous page
            if ($prevpageid) {
                if (!set_field("lesson_pages", "nextpageid", $newid, "id", $prevpageid)) {
                    error("Lesson restorelib: unable to update link");
                }
            }
            $prevpageid = $newid;

            //Do some output
            if (($i+1) % 10 == 0) {
                if (!defined('RESTORE_SILENTLY')) {
                    echo ".";
                    if (($i+1) % 200 == 0) {
                        echo "<br/>";
                    }
                }
                backup_flush(300);
            }

            if ($newid) {
                //We have the newid, update backup_ids (restore logs will use it!!)
                backup_putid($restore->backup_unique_code,"lesson_pages", $oldid, $newid);
                //We have to restore the lesson_answers table now (a page level table)
                $status = lesson_answers_restore($lessonid,$newid,$page_info,$restore,$userdata);
                
                //Need to update useranswer field (which has answer id's in it)
                //for matching and multi-answer multi-choice questions
                if ($userdata) { // first check to see if we even have to do this
                    // if multi-answer multi-choice question or matching
                    if (($page->qtype == 3 && $page->qoption) ||
                         $page->qtype == 5) {
                        // get all the attempt records for this page
                        if ($attempts = get_records("lesson_attempts", "pageid", $newid)) {
                            foreach ($attempts as $attempt) {
                                unset($newuseranswer);
                                if ($attempt->useranswer != NULL) {
                                    // explode the user answer.  Each element in
                                    // $useranswer is an old answer id, so needs to be updated
                                    $useranswer = explode(",", $attempt->useranswer);
                                    foreach ($useranswer as $oldanswerid) {
                                         $backupdata = backup_getid($restore->backup_unique_code,"lesson_answers",$oldanswerid);
                                         $newuseranswer[] = $backupdata->new_id;
                                    }
                                    // get the useranswer in the right format
                                    $attempt->useranswer = implode(",", $newuseranswer);
                                    // update it
                                    update_record("lesson_attempts", $attempt);
                                }
                            }
                        }
                    }
                }        
                
                // backup branch table info for branch tables.
                if ($status && $userdata) {
                    if (!lesson_branch_restore($lessonid,$newid,$page_info,$restore)) {
                        return false;
                    }
                }
            } else {
                $status = false;
            }
        }

        //We've restored all the pages and answers, we now need to fix the jumps in the
        //answer records if they are absolute
        if ($answers = get_records("lesson_answers", "lessonid", $lessonid)) {
            foreach ($answers as $answer) {
                if ($answer->jumpto > 0) {
                    // change the absolute page id
                    $page = backup_getid($restore->backup_unique_code,"lesson_pages",$answer->jumpto);
                    if ($page) {
                        if (!set_field("lesson_answers", "jumpto", $page->new_id, "id", $answer->id)) {
                            error("Lesson restorelib: unable to reset jump");
                        }
                    }
                }
            }
        }
        return $status;
    }


    //This function restores the lesson_answers
    function lesson_answers_restore($lessonid,$pageid,$info,$restore,$userdata=false) {

        global $CFG;

        $status = true;

        //Get the lesson_answers array (optional)
        if (isset($info['#']['ANSWERS']['0']['#']['ANSWER'])) {
            // The following chunk of code is a fix for matching questions made
            // pre moodle 1.5.  Matching questions need two answer fields designated
            // for correct and wrong responses before the rest of the answer fields.
            if ($restore->backup_version <= 2004083124) {  // Backup version for 1.4.5+
                if ($ismatching = get_record('lesson_pages', 'id', $pageid)) {  // get the page we just inserted
                    if ($ismatching->qtype == 5) { // check to make sure it is a matching question
                        $time = time();  // this may need to be changed
                        // make our 2 response answers
                        $newanswer->lessonid = $lessonid;
                        $newanswer->pageid = $pageid;
                        $newanswer->timecreated = $time;
                        $newanswer->timemodified = 0;
                        insert_record('lesson_answers', $newanswer);
                        insert_record('lesson_answers', $newanswer);
                    }
                }
            }

            $answers = $info['#']['ANSWERS']['0']['#']['ANSWER'];

            //Iterate over lesson_answers
            for($i = 0; $i < sizeof($answers); $i++) {
                $answer_info = $answers[$i];
                //traverse_xmlize($rub_info);                                  //Debug
                //print_object ($GLOBALS['traverse_array']);                   //Debug
                //$GLOBALS['traverse_array']="";                               //Debug

                //We'll need this later!!
                $oldid = backup_todb($answer_info['#']['ID']['0']['#']);

                //Now, build the lesson_answers record structure
                $answer->lessonid = $lessonid;
                $answer->pageid = $pageid;
                // the absolute jumps will need fixing later
                $answer->jumpto = backup_todb($answer_info['#']['JUMPTO']['0']['#']);
                $answer->grade = backup_todb($answer_info['#']['GRADE']['0']['#']);
                $answer->score = backup_todb($answer_info['#']['SCORE']['0']['#']);
                $answer->flags = backup_todb($answer_info['#']['FLAGS']['0']['#']);
                $answer->timecreated = backup_todb($answer_info['#']['TIMECREATED']['0']['#']);
                $answer->timemodified = backup_todb($answer_info['#']['TIMEMODIFIED']['0']['#']);
                $answer->answer = backup_todb($answer_info['#']['ANSWERTEXT']['0']['#']);
                $answer->response = backup_todb($answer_info['#']['RESPONSE']['0']['#']);

                //The structure is equal to the db, so insert the lesson_answers
                $newid = insert_record ("lesson_answers",$answer);

                //Do some output
                if (($i+1) % 10 == 0) {
                    if (!defined('RESTORE_SILENTLY')) {
                        echo ".";
                        if (($i+1) % 200 == 0) {
                            echo "<br/>";
                        }
                    }
                    backup_flush(300);
                }

                if ($newid) {
                    // need to store the id so we can update the useranswer
                    // field in attempts.  This is done in the lesson_pages_restore_mods
                    backup_putid($restore->backup_unique_code,"lesson_answers", $oldid, $newid);                                 

                    if ($userdata) {
                        //We have to restore the lesson_attempts table now (a answers level table)
                        $status = lesson_attempts_restore($lessonid, $pageid, $newid, $answer_info, $restore);
                    }
                } else {
                    $status = false;
                }
            }
        }
        return $status;
    }


    //This function restores the attempts
    function lesson_attempts_restore($lessonid, $pageid, $answerid, $info, $restore) {

        global $CFG;

        $status = true;

        //Get the attempts array (optional)
        if (isset($info['#']['ATTEMPTS']['0']['#']['ATTEMPT'])) {
            $attempts = $info['#']['ATTEMPTS']['0']['#']['ATTEMPT'];
            //Iterate over attempts
            for($i = 0; $i < sizeof($attempts); $i++) {
                $attempt_info = $attempts[$i];
                //traverse_xmlize($sub_info);                                                         //Debug
                //print_object ($GLOBALS['traverse_array']);                                          //Debug
                //$GLOBALS['traverse_array']="";                                                      //Debug

                //We'll need this later!!
                $olduserid = backup_todb($attempt_info['#']['USERID']['0']['#']);

                //Now, build the lesson_attempts record structure
                $attempt->lessonid = $lessonid;
                $attempt->pageid = $pageid;
                $attempt->answerid = $answerid;
                $attempt->userid = backup_todb($attempt_info['#']['USERID']['0']['#']);
                $attempt->retry = backup_todb($attempt_info['#']['RETRY']['0']['#']);
                $attempt->correct = backup_todb($attempt_info['#']['CORRECT']['0']['#']);
                $attempt->useranswer = backup_todb($attempt_info['#']['USERANSWER']['0']['#']);
                $attempt->timeseen = backup_todb($attempt_info['#']['TIMESEEN']['0']['#']);

                //We have to recode the userid field
                $user = backup_getid($restore->backup_unique_code,"user",$olduserid);
                if ($user) {
                    $attempt->userid = $user->new_id;
                }

                //The structure is equal to the db, so insert the lesson_attempt
                $newid = insert_record ("lesson_attempts",$attempt);

                //Do some output
                if (($i+1) % 50 == 0) {
                    if (!defined('RESTORE_SILENTLY')) {
                        echo ".";
                        if (($i+1) % 1000 == 0) {
                            echo "<br/>";
                        }
                    }
                    backup_flush(300);
                }
            }
        }

    return $status;
    }

    //This function restores the lesson_grades
    function lesson_grades_restore_mods($lessonid, $info, $restore) {

        global $CFG;

        $status = true;

        //Get the grades array (optional)
        if (isset($info['MOD']['#']['GRADES']['0']['#']['GRADE'])) {
            $grades = $info['MOD']['#']['GRADES']['0']['#']['GRADE'];

            //Iterate over grades
            for($i = 0; $i < sizeof($grades); $i++) {
                $grade_info = $grades[$i];
                //traverse_xmlize($grade_info);                         //Debug
                //print_object ($GLOBALS['traverse_array']);            //Debug
                //$GLOBALS['traverse_array']="";                        //Debug

                //We'll need this later!!
                $olduserid = backup_todb($grade_info['#']['USERID']['0']['#']);

                //Now, build the lesson_GRADES record structure
                $grade->lessonid = $lessonid;
                $grade->userid = backup_todb($grade_info['#']['USERID']['0']['#']);
                $grade->grade = backup_todb($grade_info['#']['GRADE_VALUE']['0']['#']);
                $grade->late = backup_todb($grade_info['#']['LATE']['0']['#']);
                $grade->completed = backup_todb($grade_info['#']['COMPLETED']['0']['#']);

                //We have to recode the userid field
                $user = backup_getid($restore->backup_unique_code,"user",$olduserid);
                if ($user) {
                    $grade->userid = $user->new_id;
                }

                //The structure is equal to the db, so insert the lesson_grade
                $newid = insert_record ("lesson_grades",$grade);

                //Do some output
                if (($i+1) % 50 == 0) {
                    if (!defined('RESTORE_SILENTLY')) {
                        echo ".";
                        if (($i+1) % 1000 == 0) {
                            echo "<br/>";
                        }
                    }
                    backup_flush(300);
                }

                if (!$newid) {
                    $status = false;
                }
            }
        }

        return $status;
    }
    
    
    
    //This function restores the lesson_branch
    function lesson_branch_restore($lessonid, $pageid, $info, $restore) {

        global $CFG;

        $status = true;

        //Get the branch array (optional)
        if (isset($info['#']['BRANCHES']['0']['#']['BRANCH'])) {
            $branches = $info['#']['BRANCHES']['0']['#']['BRANCH'];
            //Iterate over branches
            for($i = 0; $i < sizeof($branches); $i++) {
                $branch_info = $branches[$i];
                //traverse_xmlize($branch_info);                                                         //Debug
                //print_object ($GLOBALS['traverse_array']);                                          //Debug
                //$GLOBALS['traverse_array']="";                                                      //Debug

                //We'll need this later!!
                $olduserid = backup_todb($branch_info['#']['USERID']['0']['#']);

                //Now, build the lesson_attempts record structure
                $branch->lessonid = $lessonid;
                $branch->userid = backup_todb($branch_info['#']['USERID']['0']['#']);
                $branch->pageid = $pageid;
                $branch->retry = backup_todb($branch_info['#']['RETRY']['0']['#']);
                $branch->flag = backup_todb($branch_info['#']['FLAG']['0']['#']);
                $branch->timeseen = backup_todb($branch_info['#']['TIMESEEN']['0']['#']);

                //We have to recode the userid field
                $user = backup_getid($restore->backup_unique_code,"user",$olduserid);
                if ($user) {
                    $branch->userid = $user->new_id;
                }

                //The structure is equal to the db, so insert the lesson_attempt
                $newid = insert_record ("lesson_branch",$branch);

                //Do some output
                if (($i+1) % 50 == 0) {
                    if (!defined('RESTORE_SILENTLY')) {
                        echo ".";
                        if (($i+1) % 1000 == 0) {
                            echo "<br/>";
                        }
                    }
                    backup_flush(300);
                }
            }
        }

    return $status;
    }

    //This function restores the lesson_timer
    function lesson_timer_restore_mods($lessonid, $info, $restore) {

        global $CFG;

        $status = true;
        //Get the timer array (optional)
        if (isset($info['MOD']['#']['TIMES']['0']['#']['TIME'])) {
            $times = $info['MOD']['#']['TIMES']['0']['#']['TIME'];
            //Iterate over times
            for($i = 0; $i < sizeof($times); $i++) {
                $time_info = $times[$i];
                //traverse_xmlize($time_info);                         //Debug
                //print_object ($GLOBALS['traverse_array']);            //Debug
                //$GLOBALS['traverse_array']="";                        //Debug

                //We'll need this later!!
                $olduserid = backup_todb($time_info['#']['USERID']['0']['#']);

                //Now, build the lesson_time record structure
                $time->lessonid = $lessonid;
                $time->userid = backup_todb($time_info['#']['USERID']['0']['#']);
                $time->starttime = backup_todb($time_info['#']['STARTTIME']['0']['#']);
                $time->lessontime = backup_todb($time_info['#']['LESSONTIME']['0']['#']);

                //We have to recode the userid field
                $user = backup_getid($restore->backup_unique_code,"user",$olduserid);
                if ($user) {
                    $time->userid = $user->new_id;
                }

                //The structure is equal to the db, so insert the lesson_grade
                $newid = insert_record ("lesson_timer",$time);

                //Do some output
                if (($i+1) % 50 == 0) {
                    if (!defined('RESTORE_SILENTLY')) {
                        echo ".";
                        if (($i+1) % 1000 == 0) {
                            echo "<br/>";
                        }
                    }
                    backup_flush(300);
                }

                if (!$newid) {
                    $status = false;
                }
            }
        }

        return $status;
    }

    //This function restores the lesson_high_scores
    function lesson_high_scores_restore_mods($lessonid, $info, $restore) {

        global $CFG;

        $status = true;

        //Get the highscores array (optional)
        if (isset($info['MOD']['#']['HIGHSCORES']['0']['#']['HIGHSCORE'])) {
            $highscores = $info['MOD']['#']['HIGHSCORES']['0']['#']['HIGHSCORE'];
            //Iterate over highscores
            for($i = 0; $i < sizeof($highscores); $i++) {
                $highscore_info = $highscores[$i];
                //traverse_xmlize($highscore_info);                     //Debug
                //print_object ($GLOBALS['traverse_array']);            //Debug
                //$GLOBALS['traverse_array']="";                        //Debug

                //We'll need this later!!
                $olduserid = backup_todb($highscore_info['#']['USERID']['0']['#']);

                //Now, build the lesson_highscores record structure
                $highscore->lessonid = $lessonid;
                $highscore->userid = backup_todb($highscore_info['#']['USERID']['0']['#']);
                $highscore->gradeid = backup_todb($highscore_info['#']['GRADEID']['0']['#']);
                $highscore->nickname = backup_todb($highscore_info['#']['NICKNAME']['0']['#']);

                //We have to recode the userid field
                $user = backup_getid($restore->backup_unique_code,"user",$olduserid);
                if ($user) {
                    $highscore->userid = $user->new_id;
                }
                
                //The structure is equal to the db, so insert the lesson_grade
                $newid = insert_record ("lesson_high_scores",$highscore);

                //Do some output
                if (($i+1) % 50 == 0) {
                    if (!defined('RESTORE_SILENTLY')) {
                        echo ".";
                        if (($i+1) % 1000 == 0) {
                            echo "<br/>";
                        }
                    }
                    backup_flush(300);
                }

                if (!$newid) {
                    $status = false;
                }
            }
        }

        return $status;
    }
    
    //This function restores the lesson_default
    function lesson_default_restore_mods($info, $restore) {

        global $CFG;

        $status = true;

        //Get the default array (optional)
        if (isset($info['MOD']['#']['DEFAULTS'])) {
            $defaults = $info['MOD']['#']['DEFAULTS'];

            //Iterate over defaults (should only be 1!)
            for($i = 0; $i < sizeof($defaults); $i++) {
                $default_info = $defaults[$i];
                //traverse_xmlize($default_info);                       //Debug
                //print_object ($GLOBALS['traverse_array']);            //Debug
                //$GLOBALS['traverse_array']="";                        //Debug

                //Now, build the lesson_default record structure
                $default->course = $restore->course_id;
                $default->practice = backup_todb($default_info['#']['PRACTICE']['0']['#']);
                $default->modattempts = backup_todb($default_info['#']['MODATTEMPTS']['0']['#']);
                $default->usepassword = backup_todb($default_info['#']['USEPASSWORD']['0']['#']);
                $default->password = backup_todb($default_info['#']['PASSWORD']['0']['#']);
                $default->conditions = backup_todb($default_info['#']['CONDITIONS']['0']['#']);
                $default->grade = backup_todb($default_info['#']['GRADE']['0']['#']);
                $default->custom = backup_todb($default_info['#']['CUSTOM']['0']['#']);
                $default->ongoing = backup_todb($default_info['#']['ONGOING']['0']['#']);
                $default->usemaxgrade = backup_todb($default_info['#']['USEMAXGRADE']['0']['#']);
                $default->maxanswers = backup_todb($default_info['#']['MAXANSWERS']['0']['#']);
                $default->maxattempts = backup_todb($default_info['#']['MAXATTEMPTS']['0']['#']);
                $default->review = backup_todb($default_info['#']['REVIEW']['0']['#']);
                $default->nextpagedefault = backup_todb($default_info['#']['NEXTPAGEDEFAULT']['0']['#']);
                $default->feedback = backup_todb($default_info['#']['FEEDBACK']['0']['#']);
                $default->minquestions = backup_todb($default_info['#']['MINQUESTIONS']['0']['#']);
                $default->maxpages = backup_todb($default_info['#']['MAXPAGES']['0']['#']);
                $default->timed = backup_todb($default_info['#']['TIMED']['0']['#']);
                $default->maxtime = backup_todb($default_info['#']['MAXTIME']['0']['#']);
                $default->retake = backup_todb($default_info['#']['RETAKE']['0']['#']);
                $default->mediaheight = backup_todb($default_info['#']['MEDIAHEIGHT']['0']['#']);
                $default->mediawidth = backup_todb($default_info['#']['MEDIAWIDTH']['0']['#']);
                $default->mediaclose = backup_todb($default_info['#']['MEDIACLOSE']['0']['#']);
                $default->slideshow = backup_todb($default_info['#']['SLIDESHOW']['0']['#']);
                $default->width = backup_todb($default_info['#']['WIDTH']['0']['#']);
                $default->height = backup_todb($default_info['#']['HEIGHT']['0']['#']);
                $default->bgcolor = backup_todb($default_info['#']['BGCOLOR']['0']['#']);
                $default->displayleft = backup_todb($default_info['#']['DISPLAYLEFT']['0']['#']);
                $default->displayleftif = backup_todb($default_info['#']['DISPLAYLEFTIF']['0']['#']);
                $default->progressbar = backup_todb($default_info['#']['PROGRESSBAR']['0']['#']);
                $default->highscores = backup_todb($default_info['#']['HIGHSCORES']['0']['#']);
                $default->maxhighscores = backup_todb($default_info['#']['MAXHIGHSCORES']['0']['#']);

                //The structure is equal to the db, so insert the lesson_grade
                $newid = insert_record ("lesson_default",$default);
                
                if ($newid) {
                    backup_putid($restore->backup_unique_code,'lesson_default',
                                 $restore->course_id, $newid);
                }
                
                //Do some output
                if (($i+1) % 50 == 0) {
                    if (!defined('RESTORE_SILENTLY')) {
                        echo ".";
                        if (($i+1) % 1000 == 0) {
                            echo "<br/>";
                        }
                    }
                    backup_flush(300);
                }

                if (!$newid) {
                    $status = false;
                }
            }
        }

        return $status;
    }

    //Return a content decoded to support interactivities linking. Every module
    //should have its own. They are called automatically from
    //lesson_decode_content_links_caller() function in each module
    //in the restore process
    function lesson_decode_content_links ($content,$restore) {
            
        global $CFG;
            
        $result = $content;
                
        //Link to the list of lessons
                
        $searchstring='/\$@(LESSONINDEX)\*([0-9]+)@\$/';
        //We look for it
        preg_match_all($searchstring,$content,$foundset);
        //If found, then we are going to look for its new id (in backup tables)
        if ($foundset[0]) {
            //print_object($foundset);                                     //Debug
            //Iterate over foundset[2]. They are the old_ids
            foreach($foundset[2] as $old_id) {
                //We get the needed variables here (course id)
                $rec = backup_getid($restore->backup_unique_code,"course",$old_id);
                //Personalize the searchstring
                $searchstring='/\$@(LESSONINDEX)\*('.$old_id.')@\$/';
                //If it is a link to this course, update the link to its new location
                if($rec->new_id) {
                    //Now replace it
                    $result= preg_replace($searchstring,$CFG->wwwroot.'/mod/lesson/index.php?id='.$rec->new_id,$result);
                } else { 
                    //It's a foreign link so leave it as original
                    $result= preg_replace($searchstring,$restore->original_wwwroot.'/mod/lesson/index.php?id='.$old_id,$result);
                }
            }
        }

        //Link to lesson view by moduleid

        $searchstring='/\$@(LESSONVIEWBYID)\*([0-9]+)@\$/';
        //We look for it
        preg_match_all($searchstring,$result,$foundset);
        //If found, then we are going to look for its new id (in backup tables)
        if ($foundset[0]) {
            //print_object($foundset);                                     //Debug
            //Iterate over foundset[2]. They are the old_ids
            foreach($foundset[2] as $old_id) {
                //We get the needed variables here (course_modules id)
                $rec = backup_getid($restore->backup_unique_code,"course_modules",$old_id);
                //Personalize the searchstring
                $searchstring='/\$@(LESSONVIEWBYID)\*('.$old_id.')@\$/';
                //If it is a link to this course, update the link to its new location
                if($rec->new_id) {
                    //Now replace it
                    $result= preg_replace($searchstring,$CFG->wwwroot.'/mod/lesson/view.php?id='.$rec->new_id,$result);
                } else {
                    //It's a foreign link so leave it as original
                    $result= preg_replace($searchstring,$restore->original_wwwroot.'/mod/lesson/view.php?id='.$old_id,$result);
                }
            }
        }

        return $result;
    }

    //This function makes all the necessary calls to xxxx_decode_content_links()
    //function in each module, passing them the desired contents to be decoded
    //from backup format to destination site/course in order to mantain inter-activities
    //working in the backup/restore process. It's called from restore_decode_content_links()
    //function in restore process
    function lesson_decode_content_links_caller($restore) {
        global $CFG;
        $status = true;
        
        //Process every lesson PAGE in the course
        if ($pages = get_records_sql ("SELECT p.id, p.contents
                                   FROM {$CFG->prefix}lesson_pages p,
                                        {$CFG->prefix}lesson l
                                   WHERE l.course = $restore->course_id AND
                                         p.lessonid = l.id")) {
            //Iterate over each page->message
            $i = 0;   //Counter to send some output to the browser to avoid timeouts
            foreach ($pages as $page) {
                //Increment counter
                $i++;
                $content = $page->contents;
                $result = restore_decode_content_links_worker($content,$restore);
                if ($result != $content) {
                    //Update record
                    $page->contents = addslashes($result);
                    $status = update_record("lesson_pages",$page);
                    if (debugging()) {
                        if (!defined('RESTORE_SILENTLY')) {
                            echo '<br /><hr />'.s($content).'<br />changed to<br />'.s($result).'<hr /><br />';
                        }
                    }
                }
                //Do some output
                if (($i+1) % 5 == 0) {
                    if (!defined('RESTORE_SILENTLY')) {
                        echo ".";
                        if (($i+1) % 100 == 0) {
                            echo "<br />";
                        }
                    }
                    backup_flush(300);
                }
            }
        }

        // Remap activity links
        if ($lessons = get_records_select('lesson', "activitylink != 0 AND course = $restore->course_id", '', 'id, activitylink')) {
            foreach ($lessons as $lesson) {
                if ($newcmid = backup_getid($restore->backup_unique_code, 'course_modules', $lesson->activitylink)) {
                    $status = $status and set_field('lesson', 'activitylink', $newcmid->new_id, 'id', $lesson->id);
                }
            }
        }

        return $status;
    }

    //This function returns a log record with all the necessay transformations
    //done. It's used by restore_log_module() to restore modules log.
    function lesson_restore_logs($restore,$log) {

        $status = false;

        //Depending of the action, we recode different things
        switch ($log->action) {
        case "add":
            if ($log->cmid) {
                //Get the new_id of the module (to recode the info field)
                $mod = backup_getid($restore->backup_unique_code,$log->module,$log->info);
                if ($mod) {
                    $log->url = "view.php?id=".$log->cmid;
                    $log->info = $mod->new_id;
                    $status = true;
                }
            }
            break;
        case "update":
            if ($log->cmid) {
                //Get the new_id of the module (to recode the info field)
                $mod = backup_getid($restore->backup_unique_code,$log->module,$log->info);
                if ($mod) {
                    $log->url = "view.php?id=".$log->cmid;
                    $log->info = $mod->new_id;
                    $status = true;
                }
            }
            break;
        case "view all":
            $log->url = "index.php?id=".$log->course;
            $status = true;
            break;
        case "start":
            if ($log->cmid) {
                //Get the new_id of the module (to recode the info field)
                $mod = backup_getid($restore->backup_unique_code,$log->module,$log->info);
                if ($mod) {
                    $log->url = "view.php?id=".$log->cmid;
                    $log->info = $mod->new_id;
                    $status = true;
                }
            }
            break;
        case "end":
            if ($log->cmid) {
                //Get the new_id of the module (to recode the info field)
                $mod = backup_getid($restore->backup_unique_code,$log->module,$log->info);
                if ($mod) {
                    $log->url = "view.php?id=".$log->cmid;
                    $log->info = $mod->new_id;
                    $status = true;
                }
            }
            break;
        case "view":
            if ($log->cmid) {
                //Get the new_id of the page (to recode the url field)
                $pag = backup_getid($restore->backup_unique_code,"lesson_pages",$log->info);
                if ($pag) {
                    $log->url = "view.php?id=".$log->cmid."&action=navigation&pageid=".$pag->new_id;
                    $log->info = $pag->new_id;
                    $status = true;
                }
            }
            break;
        default:
            if (!defined('RESTORE_SILENTLY')) {
                echo "action (".$log->module."-".$log->action.") unknown. Not restored<br/>";                 //Debug
            }
            break;
        }

        if ($status) {
            $status = $log;
        }
        return $status;
    }
?>
