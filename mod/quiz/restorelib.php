<?php // $Id$
    //This php script contains all the stuff to restore quiz mods

// Todo:

    // whereever it says "/// We have to recode the .... field" we should put in a check
    // to see if the recoding was successful and throw an appropriate error otherwise

//This is the "graphical" structure of the quiz mod:
    //To see, put your terminal to 160cc

    //
    //                           quiz
    //                        (CL,pk->id)
    //                            |
    //           -------------------------------------------------------------------
    //           |                    |                        |                    |
    //           |               quiz_grades                   |        quiz_question_versions
    //           |           (UL,pk->id,fk->quiz)              |         (CL,pk->id,fk->quiz)
    //           |                                             |
    //      quiz_attempts                          quiz_question_instances
    //  (UL,pk->id,fk->quiz)                    (CL,pk->id,fk->quiz,question)
    //
    // Meaning: pk->primary key field of the table
    //          fk->foreign key to link with parent
    //          nt->nested field (recursive data)
    //          SL->site level info
    //          CL->course level info
    //          UL->user level info
    //          files->table may have files
    //
    //-----------------------------------------------------------

    // When we restore a quiz we also need to restore the questions and possibly
    // the data about student interaction with the questions. The functions to do
    // that are included with the following library
    include_once("$CFG->dirroot/question/restorelib.php");

    function quiz_restore_mods($mod,$restore) {

        global $CFG;

        $status = true;

        //Hook to call Moodle < 1.5 Quiz Restore
        if ($restore->backup_version < 2005043000) {
            include_once("restorelibpre15.php");
            return quiz_restore_pre15_mods($mod,$restore);
        }

        //Get record from backup_ids
        $data = backup_getid($restore->backup_unique_code,$mod->modtype,$mod->id);

        if ($data) {
            //Now get completed xmlized object
            $info = $data->info;
            //if necessary, write to restorelog and adjust date/time fields
            if ($restore->course_startdateoffset) {
                restore_log_date_changes('Quiz', $restore, $info['MOD']['#'], array('TIMEOPEN', 'TIMECLOSE'));
            }            
            //traverse_xmlize($info);                                                                     //Debug
            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
            //$GLOBALS['traverse_array']="";                                                              //Debug

            //Now, build the QUIZ record structure
            $quiz = new stdClass;
            $quiz->course = $restore->course_id;
            $quiz->name = backup_todb($info['MOD']['#']['NAME']['0']['#']);
            $quiz->intro = backup_todb($info['MOD']['#']['INTRO']['0']['#']); 
            $quiz->timeopen = backup_todb($info['MOD']['#']['TIMEOPEN']['0']['#']);
            $quiz->timeclose = backup_todb($info['MOD']['#']['TIMECLOSE']['0']['#']);
            $quiz->optionflags = backup_todb($info['MOD']['#']['OPTIONFLAGS']['0']['#']);
            $quiz->penaltyscheme = backup_todb($info['MOD']['#']['PENALTYSCHEME']['0']['#']);
            $quiz->attempts = backup_todb($info['MOD']['#']['ATTEMPTS_NUMBER']['0']['#']);
            $quiz->attemptonlast = backup_todb($info['MOD']['#']['ATTEMPTONLAST']['0']['#']);
            $quiz->grademethod = backup_todb($info['MOD']['#']['GRADEMETHOD']['0']['#']);
            $quiz->decimalpoints = backup_todb($info['MOD']['#']['DECIMALPOINTS']['0']['#']);
            $quiz->review = backup_todb($info['MOD']['#']['REVIEW']['0']['#']);
            $quiz->questionsperpage = backup_todb($info['MOD']['#']['QUESTIONSPERPAGE']['0']['#']);
            $quiz->shufflequestions = backup_todb($info['MOD']['#']['SHUFFLEQUESTIONS']['0']['#']);
            $quiz->shuffleanswers = backup_todb($info['MOD']['#']['SHUFFLEANSWERS']['0']['#']);
            $quiz->questions = backup_todb($info['MOD']['#']['QUESTIONS']['0']['#']);
            $quiz->sumgrades = backup_todb($info['MOD']['#']['SUMGRADES']['0']['#']);
            $quiz->grade = backup_todb($info['MOD']['#']['GRADE']['0']['#']);
            $quiz->timecreated = backup_todb($info['MOD']['#']['TIMECREATED']['0']['#']);
            $quiz->timemodified = backup_todb($info['MOD']['#']['TIMEMODIFIED']['0']['#']);
            $quiz->timelimit = backup_todb($info['MOD']['#']['TIMELIMIT']['0']['#']);
            $quiz->password = backup_todb($info['MOD']['#']['PASSWORD']['0']['#']);
            $quiz->subnet = backup_todb($info['MOD']['#']['SUBNET']['0']['#']);
            $quiz->popup = backup_todb($info['MOD']['#']['POPUP']['0']['#']);
            $quiz->delay1 = isset($info['MOD']['#']['DELAY1']['0']['#'])?backup_todb($info['MOD']['#']['DELAY1']['0']['#']):'';
            $quiz->delay2 = isset($info['MOD']['#']['DELAY2']['0']['#'])?backup_todb($info['MOD']['#']['DELAY2']['0']['#']):'';
            //We have to recode the questions field (a list of questions id and pagebreaks)
            $quiz->questions = quiz_recode_layout($quiz->questions, $restore);

            //The structure is equal to the db, so insert the quiz
            $newid = insert_record ("quiz",$quiz);

            //Do some output
            if (!defined('RESTORE_SILENTLY')) {
                echo "<li>".get_string("modulename","quiz")." \"".format_string(stripslashes($quiz->name),true)."\"</li>";
            }
            backup_flush(300);

            if ($newid) {
                //We have the newid, update backup_ids
                backup_putid($restore->backup_unique_code,$mod->modtype,
                             $mod->id, $newid);
                //We have to restore the question_instances now (course level table)
                $status = quiz_question_instances_restore_mods($newid,$info,$restore);
                //We have to restore the feedback now (course level table)
                $status = quiz_feedback_restore_mods($newid, $info, $restore, $quiz);
                //We have to restore the question_versions now (course level table)
                $status = quiz_question_versions_restore_mods($newid,$info,$restore);
                //Now check if want to restore user data and do it.
                if (restore_userdata_selected($restore,'quiz',$mod->id)) {
                    //Restore quiz_attempts
                    $status = quiz_attempts_restore_mods ($newid,$info,$restore);
                    if ($status) {
                        //Restore quiz_grades
                        $status = quiz_grades_restore_mods ($newid,$info,$restore);
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

    //This function restores the quiz_question_instances
    function quiz_question_instances_restore_mods($quiz_id,$info,$restore) {

        global $CFG;

        $status = true;

        //Get the quiz_question_instances array
        if (array_key_exists('QUESTION_INSTANCES', $info['MOD']['#'])) {
            $instances = $info['MOD']['#']['QUESTION_INSTANCES']['0']['#']['QUESTION_INSTANCE'];
        } else {
            $instances = array();
        }

        //Iterate over question_instances
        for($i = 0; $i < sizeof($instances); $i++) {
            $gra_info = $instances[$i];
            //traverse_xmlize($gra_info);                                                                 //Debug
            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
            //$GLOBALS['traverse_array']="";                                                              //Debug

            //We'll need this later!!
            $oldid = backup_todb($gra_info['#']['ID']['0']['#']);

            //Now, build the QUESTION_INSTANCES record structure
            $instance = new stdClass;
            $instance->quiz = $quiz_id;
            $instance->question = backup_todb($gra_info['#']['QUESTION']['0']['#']);
            $instance->grade = backup_todb($gra_info['#']['GRADE']['0']['#']);

            //We have to recode the question field
            $question = backup_getid($restore->backup_unique_code,"question",$instance->question);
            if ($question) {
                $instance->question = $question->new_id;
            }

            //The structure is equal to the db, so insert the quiz_question_instances
            $newid = insert_record ("quiz_question_instances",$instance);

            //Do some output
            if (($i+1) % 10 == 0) {
                if (!defined('RESTORE_SILENTLY')) {
                    echo ".";
                    if (($i+1) % 200 == 0) {
                        echo "<br />";
                    }
                }
                backup_flush(300);
            }

            if ($newid) {
                //We have the newid, update backup_ids
                backup_putid($restore->backup_unique_code,"quiz_question_instances",$oldid,
                             $newid);
            } else {
                $status = false;
            }
        }

        return $status;
    }

    //This function restores the quiz_question_instances
    function quiz_feedback_restore_mods($quiz_id, $info, $restore, $quiz) {
        $status = true;

        //Get the quiz_feedback array
        if (array_key_exists('FEEDBACKS', $info['MOD']['#'])) {
            $feedbacks = $info['MOD']['#']['FEEDBACKS']['0']['#']['FEEDBACK'];

            //Iterate over the feedbacks
            foreach ($feedbacks as $feedback_info) {
                //traverse_xmlize($feedback_info);                                                            //Debug
                //print_object ($GLOBALS['traverse_array']);                                                  //Debug
                //$GLOBALS['traverse_array']="";                                                              //Debug
    
                //We'll need this later!!
                $oldid = backup_todb($feedback_info['#']['ID']['0']['#']);
    
                //Now, build the quiz_feedback record structure
                $feedback = new stdClass();
                $feedback->quizid = $quiz_id;
                $feedback->feedbacktext = backup_todb($feedback_info['#']['FEEDBACKTEXT']['0']['#']);
                $feedback->mingrade = backup_todb($feedback_info['#']['MINGRADE']['0']['#']);
                $feedback->maxgrade = backup_todb($feedback_info['#']['MAXGRADE']['0']['#']);
    
                //The structure is equal to the db, so insert the quiz_question_instances
                $newid = insert_record('quiz_feedback', $feedback);
    
                if ($newid) {
                    //We have the newid, update backup_ids
                    backup_putid($restore->backup_unique_code, 'quiz_feedback', $oldid, $newid);
                } else {
                    $status = false;
                }
            }
        } else {
            $feedback = new stdClass();
            $feedback->quizid = $quiz_id;
            $feedback->feedbacktext = '';
            $feedback->mingrade = 0;
            $feedback->maxgrade = $quiz->grade + 1;
            insert_record('quiz_feedback', $feedback);
        }

        return $status;
    }

    //This function restores the quiz_question_versions
    function quiz_question_versions_restore_mods($quiz_id,$info,$restore) {

        global $CFG, $USER;

        $status = true;

        //Get the quiz_question_versions array
        if (!empty($info['MOD']['#']['QUESTION_VERSIONS'])) {
            $versions = $info['MOD']['#']['QUESTION_VERSIONS']['0']['#']['QUESTION_VERSION'];
        } else {
            $versions = array();
        }
        
        //Iterate over question_versions
        for($i = 0; $i < sizeof($versions); $i++) {
            $ver_info = $versions[$i];
            //traverse_xmlize($ver_info);                                                                 //Debug
            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
            //$GLOBALS['traverse_array']="";                                                              //Debug

            //We'll need this later!!
            $oldid = backup_todb($ver_info['#']['ID']['0']['#']);

            //Now, build the QUESTION_VERSIONS record structure
            $version = new stdClass;
            $version->quiz = $quiz_id;
            $version->oldquestion = backup_todb($ver_info['#']['OLDQUESTION']['0']['#']);
            $version->newquestion = backup_todb($ver_info['#']['NEWQUESTION']['0']['#']);
            $version->originalquestion = backup_todb($ver_info['#']['ORIGINALQUESTION']['0']['#']);
            $version->userid = backup_todb($ver_info['#']['USERID']['0']['#']);
            $version->timestamp = backup_todb($ver_info['#']['TIMESTAMP']['0']['#']);

            //We have to recode the oldquestion field
            $question = backup_getid($restore->backup_unique_code,"question",$version->oldquestion);
            if ($question) {
                $version->oldquestion = $question->new_id;
            }

            //We have to recode the newquestion field
            $question = backup_getid($restore->backup_unique_code,"question",$version->newquestion);
            if ($question) {
                $version->newquestion = $question->new_id;
            }

            //We have to recode the originalquestion field
            $question = backup_getid($restore->backup_unique_code,"question",$version->originalquestion);
            if ($question) {
                $version->newquestion = $question->new_id;
            }

            //We have to recode the userid field
            $user = backup_getid($restore->backup_unique_code,"user",$version->userid);
            if ($user) {
                $version->userid = $user->new_id;
            } else {  //Assign to current user
                $version->userid = $USER->id;
            }

            //The structure is equal to the db, so insert the quiz_question_versions
            $newid = insert_record ("quiz_question_versions",$version);

            //Do some output
            if (($i+1) % 10 == 0) {
                if (!defined('RESTORE_SILENTLY')) {
                    echo ".";
                    if (($i+1) % 200 == 0) {
                        echo "<br />";
                    }
                }
                backup_flush(300);
            }

            if ($newid) {
                //We have the newid, update backup_ids
                backup_putid($restore->backup_unique_code,"quiz_question_versions",$oldid,
                             $newid);
            } else {
                $status = false;
            }
        }

        return $status;
    }

    //This function restores the quiz_attempts
    function quiz_attempts_restore_mods($quiz_id,$info,$restore) {

        global $CFG;

        $status = true;

        //Get the quiz_attempts array
        if (array_key_exists('ATTEMPTS', $info['MOD']['#'])) {
            $attempts = $info['MOD']['#']['ATTEMPTS']['0']['#']['ATTEMPT'];
        } else {
            $attempts = array();
        }

        //Iterate over attempts
        for($i = 0; $i < sizeof($attempts); $i++) {
            $att_info = $attempts[$i];
            //traverse_xmlize($att_info);                                                                 //Debug
            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
            //$GLOBALS['traverse_array']="";                                                              //Debug

            //We'll need this later!!
            $oldid = backup_todb($att_info['#']['ID']['0']['#']);
            $olduserid = backup_todb($att_info['#']['USERID']['0']['#']);

            //Now, build the ATTEMPTS record structure
            $attempt = new stdClass;
            $attempt->quiz = $quiz_id;
            $attempt->userid = backup_todb($att_info['#']['USERID']['0']['#']);
            $attempt->attempt = backup_todb($att_info['#']['ATTEMPTNUM']['0']['#']);
            $attempt->sumgrades = backup_todb($att_info['#']['SUMGRADES']['0']['#']);
            $attempt->timestart = backup_todb($att_info['#']['TIMESTART']['0']['#']);
            $attempt->timefinish = backup_todb($att_info['#']['TIMEFINISH']['0']['#']);
            $attempt->timemodified = backup_todb($att_info['#']['TIMEMODIFIED']['0']['#']);
            $attempt->layout = backup_todb($att_info['#']['LAYOUT']['0']['#']);
            $attempt->preview = backup_todb($att_info['#']['PREVIEW']['0']['#']);

            //We have to recode the userid field
            $user = backup_getid($restore->backup_unique_code,"user",$attempt->userid);
            if ($user) {
                $attempt->userid = $user->new_id;
            }

            //Set the uniqueid field
            $attempt->uniqueid = question_new_attempt_uniqueid();

            //We have to recode the layout field (a list of questions id and pagebreaks)
            $attempt->layout = quiz_recode_layout($attempt->layout, $restore);

            //The structure is equal to the db, so insert the quiz_attempts
            $newid = insert_record ("quiz_attempts",$attempt);

            //Do some output
            if (($i+1) % 10 == 0) {
                if (!defined('RESTORE_SILENTLY')) {
                    echo ".";
                    if (($i+1) % 200 == 0) {
                        echo "<br />";
                    }
                }
                backup_flush(300);
            }

            if ($newid) {
                //We have the newid, update backup_ids
                backup_putid($restore->backup_unique_code,"quiz_attempts",$oldid,
                             $newid);
                //Now process question_states
                // This function is defined in question/restorelib.php
                $status = question_states_restore_mods($attempt->uniqueid,$att_info,$restore);
            } else {
                $status = false;
            }
        }

        return $status;
    }

    //This function restores the quiz_grades
    function quiz_grades_restore_mods($quiz_id,$info,$restore) {

        global $CFG;

        $status = true;

        //Get the quiz_grades array
        if (array_key_exists('GRADES', $info['MOD']['#'])) {
            $grades = $info['MOD']['#']['GRADES']['0']['#']['GRADE'];
        } else {
            $grades = array();
        }

        //Iterate over grades
        for($i = 0; $i < sizeof($grades); $i++) {
            $gra_info = $grades[$i];
            //traverse_xmlize($gra_info);                                                                 //Debug
            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
            //$GLOBALS['traverse_array']="";                                                              //Debug

            //We'll need this later!!
            $oldid = backup_todb($gra_info['#']['ID']['0']['#']);
            $olduserid = backup_todb($gra_info['#']['USERID']['0']['#']);

            //Now, build the GRADES record structure
            $grade = new stdClass;
            $grade->quiz = $quiz_id;
            $grade->userid = backup_todb($gra_info['#']['USERID']['0']['#']);
            $grade->grade = backup_todb($gra_info['#']['GRADEVAL']['0']['#']);
            $grade->timemodified = backup_todb($gra_info['#']['TIMEMODIFIED']['0']['#']);

            //We have to recode the userid field
            $user = backup_getid($restore->backup_unique_code,"user",$grade->userid);
            if ($user) {
                $grade->userid = $user->new_id;
            }

            //The structure is equal to the db, so insert the quiz_grades
            $newid = insert_record ("quiz_grades",$grade);

            //Do some output
            if (($i+1) % 10 == 0) {
                if (!defined('RESTORE_SILENTLY')) {
                    echo ".";
                    if (($i+1) % 200 == 0) {
                        echo "<br />";
                    }
                }
                backup_flush(300);
            }

            if ($newid) {
                //We have the newid, update backup_ids
                backup_putid($restore->backup_unique_code,"quiz_grades",$oldid,
                             $newid);
            } else {
                $status = false;
            }
        }

        return $status;
    }

    //Return a content decoded to support interactivities linking. Every module
    //should have its own. They are called automatically from
    //quiz_decode_content_links_caller() function in each module
    //in the restore process
    function quiz_decode_content_links ($content,$restore) {
            
        global $CFG;
            
        $result = $content;
                
        //Link to the list of quizs
                
        $searchstring='/\$@(QUIZINDEX)\*([0-9]+)@\$/';
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
                $searchstring='/\$@(QUIZINDEX)\*('.$old_id.')@\$/';
                //If it is a link to this course, update the link to its new location
                if($rec->new_id) {
                    //Now replace it
                    $result= preg_replace($searchstring,$CFG->wwwroot.'/mod/quiz/index.php?id='.$rec->new_id,$result);
                } else { 
                    //It's a foreign link so leave it as original
                    $result= preg_replace($searchstring,$restore->original_wwwroot.'/mod/quiz/index.php?id='.$old_id,$result);
                }
            }
        }

        //Link to quiz view by moduleid
        $searchstring='/\$@(QUIZVIEWBYID)\*([0-9]+)@\$/';
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
                $searchstring='/\$@(QUIZVIEWBYID)\*('.$old_id.')@\$/';
                //If it is a link to this course, update the link to its new location
                if($rec->new_id) {
                    //Now replace it
                    $result= preg_replace($searchstring,$CFG->wwwroot.'/mod/quiz/view.php?id='.$rec->new_id,$result);
                } else {
                    //It's a foreign link so leave it as original
                    $result= preg_replace($searchstring,$restore->original_wwwroot.'/mod/quiz/view.php?id='.$old_id,$result);
                }
            }
        }

        //Link to quiz view by quizid
        $searchstring='/\$@(QUIZVIEWBYQ)\*([0-9]+)@\$/';
        //We look for it
        preg_match_all($searchstring,$result,$foundset);
        //If found, then we are going to look for its new id (in backup tables)
        if ($foundset[0]) {
            //print_object($foundset);                                     //Debug
            //Iterate over foundset[2]. They are the old_ids
            foreach($foundset[2] as $old_id) {
                //We get the needed variables here (course_modules id)
                $rec = backup_getid($restore->backup_unique_code,'quiz',$old_id);
                //Personalize the searchstring
                $searchstring='/\$@(QUIZVIEWBYQ)\*('.$old_id.')@\$/';
                //If it is a link to this course, update the link to its new location
                if($rec->new_id) {
                    //Now replace it
                    $result= preg_replace($searchstring,$CFG->wwwroot.'/mod/quiz/view.php?q='.$rec->new_id,$result);
                } else {
                    //It's a foreign link so leave it as original
                    $result= preg_replace($searchstring,$restore->original_wwwroot.'/mod/quiz/view.php?q='.$old_id,$result);
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
    function quiz_decode_content_links_caller($restore) {
        global $CFG;
        $status = true;
        
        if ($quizs = get_records_sql ("SELECT q.id, q.intro
                                   FROM {$CFG->prefix}quiz q
                                   WHERE q.course = $restore->course_id")) {
                                               //Iterate over each quiz->intro
            $i = 0;   //Counter to send some output to the browser to avoid timeouts
            foreach ($quizs as $quiz) {
                //Increment counter
                $i++;
                $content = $quiz->intro;
                $result = restore_decode_content_links_worker($content,$restore);
                if ($result != $content) {
                    //Update record
                    $quiz->intro = addslashes($result);
                    $status = update_record("quiz",$quiz);
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

        return $status;
    }

    //This function converts texts in FORMAT_WIKI to FORMAT_MARKDOWN for
    //some texts in the module
    function quiz_restore_wiki2markdown ($restore) {

        global $CFG;

        $status = true;

        //Convert question->questiontext
        if ($records = get_records_sql ("SELECT q.id, q.questiontext, q.questiontextformat
                                         FROM {$CFG->prefix}question q,
                                              {$CFG->prefix}backup_ids b
                                         WHERE b.backup_code = $restore->backup_unique_code AND
                                               b.table_name = 'question' AND
                                               q.id = b.new_id AND
                                               q.questiontextformat = ".FORMAT_WIKI)) {
            $i = 0;
            foreach ($records as $record) {
                //Rebuild wiki links
                $record->questiontext = restore_decode_wiki_content($record->questiontext, $restore);
                //Convert to Markdown
                $wtm = new WikiToMarkdown();
                $record->questiontext = $wtm->convert($record->questiontext, $restore->course_id);
                $record->questiontextformat = FORMAT_MARKDOWN;
                $status = update_record('question', addslashes_object($record));
                //Do some output
                $i++;
                if (($i+1) % 1 == 0) {
                    if (!defined('RESTORE_SILENTLY')) {
                        echo ".";
                        if (($i+1) % 20 == 0) {
                            echo "<br />";
                        }
                    }
                    backup_flush(300);
                }
            }
        }
        return $status;
    }

    //This function returns a log record with all the necessay transformations
    //done. It's used by restore_log_module() to restore modules log.
    function quiz_restore_logs($restore,$log) {

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
        case "view":
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
        case "report":
            if ($log->cmid) {
                //Get the new_id of the module (to recode the info field)
                $mod = backup_getid($restore->backup_unique_code,$log->module,$log->info);
                if ($mod) {
                    $log->url = "report.php?id=".$log->cmid;
                    $log->info = $mod->new_id;
                    $status = true;
                }
            }
            break;
        case "attempt":
            if ($log->cmid) {
                //Get the new_id of the module (to recode the info field)
                $mod = backup_getid($restore->backup_unique_code,$log->module,$log->info);
                if ($mod) {
                    //Extract the attempt id from the url field
                    $attid = substr(strrchr($log->url,"="),1);
                    //Get the new_id of the attempt (to recode the url field)
                    $att = backup_getid($restore->backup_unique_code,"quiz_attempts",$attid);
                    if ($att) {
                        $log->url = "review.php?id=".$log->cmid."&attempt=".$att->new_id;
                        $log->info = $mod->new_id;
                        $status = true;
                    }
                }
            }
            break;
        case "submit":
            if ($log->cmid) {
                //Get the new_id of the module (to recode the info field)
                $mod = backup_getid($restore->backup_unique_code,$log->module,$log->info);
                if ($mod) {
                    //Extract the attempt id from the url field
                    $attid = substr(strrchr($log->url,"="),1);
                    //Get the new_id of the attempt (to recode the url field)
                    $att = backup_getid($restore->backup_unique_code,"quiz_attempts",$attid);
                    if ($att) {
                        $log->url = "review.php?id=".$log->cmid."&attempt=".$att->new_id;
                        $log->info = $mod->new_id;
                        $status = true;
                    }
                }
            }
            break;
        case "review":
            if ($log->cmid) {
                //Get the new_id of the module (to recode the info field)
                $mod = backup_getid($restore->backup_unique_code,$log->module,$log->info);
                if ($mod) {
                    //Extract the attempt id from the url field
                    $attid = substr(strrchr($log->url,"="),1);
                    //Get the new_id of the attempt (to recode the url field)
                    $att = backup_getid($restore->backup_unique_code,"quiz_attempts",$attid);
                    if ($att) {
                        $log->url = "review.php?id=".$log->cmid."&attempt=".$att->new_id;
                        $log->info = $mod->new_id;
                        $status = true;
                    }
                }
            }
            break;
        case "editquestions":
            if ($log->cmid) {
                //Get the new_id of the module (to recode the url field)
                $mod = backup_getid($restore->backup_unique_code,$log->module,$log->info);
                if ($mod) {
                    $log->url = "view.php?id=".$log->cmid;
                    $log->info = $mod->new_id;
                    $status = true;
                }
            }
            break;
        case "preview":
            if ($log->cmid) {
                //Get the new_id of the module (to recode the url field)
                $mod = backup_getid($restore->backup_unique_code,$log->module,$log->info);
                if ($mod) {
                    $log->url = "attempt.php?id=".$log->cmid;
                    $log->info = $mod->new_id;
                    $status = true;
                }
            }
            break;
        case "start attempt":
            if ($log->cmid) {
                //Get the new_id of the module (to recode the info field)
                $mod = backup_getid($restore->backup_unique_code,$log->module,$log->info);
                if ($mod) {
                    //Extract the attempt id from the url field
                    $attid = substr(strrchr($log->url,"="),1);
                    //Get the new_id of the attempt (to recode the url field)
                    $att = backup_getid($restore->backup_unique_code,"quiz_attempts",$attid);
                    if ($att) {
                        $log->url = "review.php?id=".$log->cmid."&attempt=".$att->new_id;
                        $log->info = $mod->new_id;
                        $status = true;
                    }
                }
            }
            break;
        case "close attempt":
            if ($log->cmid) {
                //Get the new_id of the module (to recode the info field)
                $mod = backup_getid($restore->backup_unique_code,$log->module,$log->info);
                if ($mod) {
                    //Extract the attempt id from the url field
                    $attid = substr(strrchr($log->url,"="),1);
                    //Get the new_id of the attempt (to recode the url field)
                    $att = backup_getid($restore->backup_unique_code,"quiz_attempts",$attid);
                    if ($att) {
                        $log->url = "review.php?id=".$log->cmid."&attempt=".$att->new_id;
                        $log->info = $mod->new_id;
                        $status = true;
                    }
                }
            }
            break;
        case "continue attempt":
            if ($log->cmid) {
                //Get the new_id of the module (to recode the info field)
                $mod = backup_getid($restore->backup_unique_code,$log->module,$log->info);
                if ($mod) {
                    //Extract the attempt id from the url field
                    $attid = substr(strrchr($log->url,"="),1);
                    //Get the new_id of the attempt (to recode the url field)
                    $att = backup_getid($restore->backup_unique_code,"quiz_attempts",$attid);
                    if ($att) {
                        $log->url = "review.php?id=".$log->cmid."&attempt=".$att->new_id;
                        $log->info = $mod->new_id;
                        $status = true;
                    }
                }
            }
            break;
        case "continue attemp":
            if ($log->cmid) {
                //Get the new_id of the module (to recode the info field)
                $mod = backup_getid($restore->backup_unique_code,$log->module,$log->info);
                if ($mod) {
                    //Extract the attempt id from the url field
                    $attid = substr(strrchr($log->url,"="),1);
                    //Get the new_id of the attempt (to recode the url field)
                    $att = backup_getid($restore->backup_unique_code,"quiz_attempts",$attid);
                    if ($att) {
                        $log->url = "review.php?id=".$log->cmid."&attempt=".$att->new_id;
                        $log->info = $mod->new_id;
                        $log->action = "continue attempt";  //To recover some bad actions
                        $status = true;
                    }
                }
            }
            break;
        default:
            if (!defined('RESTORE_SILENTLY')) {
                echo "action (".$log->module."-".$log->action.") unknown. Not restored<br />";                 //Debug
            }
            break;
        }

        if ($status) {
            $status = $log;
        }
        return $status;
    }

    function quiz_recode_layout($layout, $restore) {
        //Recodes the quiz layout (a list of questions id and pagebreaks)

        //Extracts question id from sequence
        if ($questionids = explode(',', $layout)) {
            foreach ($questionids as $id => $questionid) {
                if ($questionid) { // If it is zero then this is a pagebreak, don't translate
                    $newq = backup_getid($restore->backup_unique_code,"question",$questionid);
                    $questionids[$id] = $newq->new_id;
                }
            }
        }
        return implode(',', $questionids);
    }

?>
