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

    //This function executes all the restore procedure about this mod
    function lesson_restore_mods($mod,$restore) {

        global $CFG;

        $status = true;

        //Get record from backup_ids
        $data = backup_getid($restore->backup_unique_code,$mod->modtype,$mod->id);

        if ($data) {
            //Now get completed xmlized object   
            $info = $data->info;
            //traverse_xmlize($info);                                                              //Debug
            //print_object ($GLOBALS['traverse_array']);                                           //Debug
            //$GLOBALS['traverse_array']="";                                                       //Debug

            //Now, build the lesson record structure
            $lesson->course = $restore->course_id;
            $lesson->name = backup_todb($info['MOD']['#']['NAME']['0']['#']);
            $lesson->grade = backup_todb($info['MOD']['#']['GRADE']['0']['#']);
            $lesson->usemaxgrade = backup_todb($info['MOD']['#']['USEMAXGRADE']['0']['#']);
            $lesson->maxanswers = backup_todb($info['MOD']['#']['MAXANSWERS']['0']['#']);
            $lesson->maxattempts = backup_todb($info['MOD']['#']['MAXATTEMPTS']['0']['#']);
            $lesson->nextpagedefault = backup_todb($info['MOD']['#']['NEXTPAGEDEFAULT']['0']['#']);
            $lesson->minquestions = backup_todb($info['MOD']['#']['MINQUESTIONS']['0']['#']);
            $lesson->maxpages = backup_todb($info['MOD']['#']['MAXPAGES']['0']['#']);
            $lesson->retake = backup_todb($info['MOD']['#']['RETAKE']['0']['#']);
            $lesson->available = backup_todb($info['MOD']['#']['AVAILABLE']['0']['#']);
            $lesson->deadline = backup_todb($info['MOD']['#']['DEADLINE']['0']['#']);
            $lesson->timemodified = backup_todb($info['MOD']['#']['TIMEMODIFIED']['0']['#']);

            //The structure is equal to the db, so insert the lesson
            $newid = insert_record("lesson", $lesson);

            //Do some output     
            echo "<ul><li>".get_string("modulename","lesson")." \"".$lesson->name."\"<br>";
            backup_flush(300);

            if ($newid) {
                //We have the newid, update backup_ids
                backup_putid($restore->backup_unique_code,$mod->modtype,
                             $mod->id, $newid);
                //We have to restore the lesson pages which are held in their logical order...
                $status = lesson_pages_restore_mods($newid,$info,$restore);
                //...and the user grades (if required)
                if ($restore->mods['lesson']->userinfo) {
                    $status = lesson_grades_restore_mods($newid,$info,$restore);
                }
            } else {
                $status = false;
            }

            //Finalize ul        
            echo "</ul>";
        
        } else {
            $status = false;
        }

        return $status;
    }

    //This function restores the lesson_pages
    function lesson_pages_restore_mods($lessonid,$info,$restore) {

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
            $page->timecreated = backup_todb($page_info['#']['TIMECREATED']['0']['#']);
            $page->timemodified = backup_todb($page_info['#']['TIMEMODIFIED']['0']['#']);
            $page->title = backup_todb($page_info['#']['TITLE']['0']['#']);
            $page->contents = backup_todb($page_info['#']['CONTENTS']['0']['#']);

            //The structure is equal to the db, so insert the lesson_pages
            $newid = insert_record ("lesson_pages",$page);

            // save the new pageids (needed to fix the absolute jumps in the answers)
            $newpageid[backup_todb($page_info['#']['PAGEID']['0']['#'])] = $newid; 
            
            // fix the forwards link of the previous page
            if ($prevpageid) {
                if (!set_field("lesson_pages", "nextpageid", $newid, "id", $prevpageid)) {
                    error("Lesson restorelib: unable to update link");
                }
            }
            $prevpageid = $newid;
            
            //Do some output
            if (($i+1) % 10 == 0) {
                echo ".";
                if (($i+1) % 200 == 0) {
                    echo "<br>";
                }
                backup_flush(300);
            }

            if ($newid) {
                //We have the newid, update backup_ids (restore logs will use it!!)
                backup_putid($restore->backup_unique_code,"lesson_pages", $oldid, $newid);
                //We have to restore the lesson_answers table now (a page level table)
                $status = lesson_answers_restore($lessonid,$newid,$page_info,$restore);
            } else {
                $status = false;
            }
        }
        
        // we've restored all the pages and answers, we now need to fix the jumps in the
        // answer records if they are absolute
        if ($answers = get_records("lesson_answers", "lessonid", $lessonid)) {
            foreach ($answers as $answer) {
                if ($answer->jumpto > 0) {
                    // change the absolute page id
                    if (!set_field("lesson_answers", "jumpto", $newpageid[$answer->jumpto], "id", 
                                $answer->id)) {
                        error("Lesson restorelib: unable to reset jump");
                    }
                }
            }
        }

        return $status;
    }


    //This function restores the lesson_answers
    function lesson_answers_restore($lessonid,$pageid,$info,$restore) {

        global $CFG;

        $status = true;

        //Get the lesson_answers array (optional)
        if (isset($info['#']['ANSWERS']['0']['#']['ANSWER'])) {
            $answers = $info['#']['ANSWERS']['0']['#']['ANSWER'];

            //Iterate over lesson_answers
            for($i = 0; $i < sizeof($answers); $i++) {
                $answer_info = $answers[$i];
                //traverse_xmlize($rub_info);                                  //Debug
                //print_object ($GLOBALS['traverse_array']);                   //Debug
                //$GLOBALS['traverse_array']="";                               //Debug

                //Now, build the lesson_answers record structure
                $answer->lessonid = $lessonid;
                $answer->pageid = $pageid;
                // the absolute jumps will need fixing
                $answer->jumpto = backup_todb($answer_info['#']['JUMPTO']['0']['#']);
                $answer->grade = backup_todb($answer_info['#']['GRADE']['0']['#']);
                $answer->flags = backup_todb($answer_info['#']['FLAGS']['0']['#']);
                $answer->timecreated = backup_todb($answer_info['#']['TIMECREATED']['0']['#']);
                $answer->timemodified = backup_todb($answer_info['#']['TIMEMODIFIED']['0']['#']);
                $answer->answer = backup_todb($answer_info['#']['ANSWERTEXT']['0']['#']);
                $answer->response = backup_todb($answer_info['#']['RESPONSE']['0']['#']);

                //The structure is equal to the db, so insert the lesson_answers
                $newid = insert_record ("lesson_answers",$answer);

                //Do some output
                if (($i+1) % 10 == 0) {
                    echo ".";
                    if (($i+1) % 200 == 0) {
                        echo "<br>";
                    }
                    backup_flush(300);
                }

                if ($newid) {
                    if ($restore->mods['lesson']->userinfo) {
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
                    echo ".";
                    if (($i+1) % 1000 == 0) {
                        echo "<br>";
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
                    $attempt->userid = $user->new_id;
                }

                //The structure is equal to the db, so insert the lesson_grade
                $newid = insert_record ("lesson_grades",$grade);

                //Do some output
                if (($i+1) % 50 == 0) {
                    echo ".";
                    if (($i+1) % 1000 == 0) {
                        echo "<br>";
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
            echo "action (".$log->module."-".$log->action.") unknow. Not restored<br>";                 //Debug
            break;
        }

        if ($status) {
            $status = $log;
        }
        return $status;
    }
?>
