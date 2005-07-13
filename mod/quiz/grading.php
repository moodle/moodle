<?php  

// This file allows a teacher to grade essay questions.  
// Could be later expanded to change user responses for all question types

# Flow of the file:
#     Get variables, run essential queries, print header and print tabs
#     Check for post data submitted.  If exists, then process data (the data is the grades and comments for essay questions)
#     Check for userid, attemptid, or gradeall and for questionid.  If found, print out the appropriate essay question attempts
#     Switch:
#         first case: print out all essay questions in quiz and the number of ungraded attempts
#         second case: print out all users and their attempts for a specific essay question


    require_once("../../config.php");
    require_once("lib.php");
    require_once("editlib.php");
    require_once($CFG->libdir.'/tablelib.php');

    $quizid = required_param('quizid', PARAM_INT);    // Course Module ID, or
    $action = optional_param('action', 'viewquestions', PARAM_ALPHA);
    $questionid = optional_param('questionid', 0, PARAM_INT);
    $attemptid = optional_param('attemptid', 0, PARAM_INT);
    $gradeall = optional_param('gradeall', 0, PARAM_INT);
    $userid = optional_param('userid', 0, PARAM_INT);


    if (!empty($questionid)) {
        if (! $question = get_record('quiz_questions', 'id', $questionid)) {
            error("Question with id $questionid not found");
        }
        ///$number = optional_param('number', 0, PARAM_INT);
    }

    if (! $quiz = get_record("quiz", "id", $quizid)) {
        error("Quiz with id $quizid not found");
    }
    if (! $course = get_record("course", "id", $quiz->course)) {
        error("Course is misconfigured");
    }
    if (! $cm = get_coursemodule_from_instance("quiz", $quiz->id, $course->id)) {
        error("Course Module ID was incorrect");
    }

    require_login($course->id);

    if (!$isteacher = isteacheredit($course->id)) {
        error("Only teachers authorized to edit the course '{$course->fullname}' can use this page!");
    }

    add_to_log($course->id, "quiz", "manualgrading", "grading.php?quizid=$quiz->id", "$quiz->id", "$cm->id");

/// GROUP CODE FROM ATTEMPTS.PHP no sure how to use just yet... need to update later perhaps
/// Check to see if groups are being used in this quiz
    # if ($groupmode = groupmode($course, $cm)) {   // Groups are being used
    #     $currentgroup = setup_and_print_groups($course, $groupmode, "attempts.php?id=$cm->id&amp;mode=overview");
    # } else {
    #     $currentgroup = false;
    # }

/// Get all students
    # if ($currentgroup) {
    #     $users = get_group_students($currentgroup);
    # }
    # else {
        $users = get_course_students($course->id);
    # }

    if(empty($users)) {
        print_heading(get_string("noattempts", "quiz"));
        return true;
    } else {
        // for sql queries
        $userids = implode(', ', array_keys($users)); 
    }

    $strquizzes = get_string("modulenameplural", "quiz");
    $strmanualgrading  = get_string("manualgrading", "quiz");

    print_header_simple("$quiz->name", "",
                 "<a href=\"index.php?id=$course->id\">$strquizzes</a> 
                  -> <a href=\"view.php?id=$cm->id\">$quiz->name</a> -> $strmanualgrading", 
                 "", "", true);


    $currenttab = 'manualgrading';
    include('tabs.php');
    echo '<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>'; // for overlib

    if ($data = data_submitted()) {  // post data submitted, process it
        confirm_sesskey();

        $question->maxgrade = get_field('quiz_question_instances', 'grade', 'quiz', $quiz->id, 'question', $question->id);
        $QUIZ_QTYPES[$question->qtype]->get_question_options($question);

        // first, process all the data to extract the teacher's new responses for the question(s)
        foreach ($data as $key => $response) {
            $keyparts = explode('_', $key); // valid keys are in this format: attemptid_stateid_fieldname
            if (count($keyparts) == 3) { // must have 3 parts to the key
                // re-assign to nice variable names for readability
                $attemptid = $keyparts[0];
                $stateid = $keyparts[1];
                $fieldname = $keyparts[2];
                
                $responses[$attemptid.'_'.$stateid][$fieldname] = $response;
            }
        }
        // now go through all of the responses to grade them and save them.
        // not totally sure if this process is correct or fully optimized.  I need help here!
        foreach($responses as $ids => $response) {
            // get our necessary ids
            $ids = explode('_', $ids);
            $attemptid = $ids[0];
            $stateid = $ids[1];
            
            // get our attempt
            if (! $attempt = get_record('quiz_attempts', 'id', $attemptid)) {
                error('No such attempt ID exists');
            }            

            // get the state
            $statefields = 'n.questionid as question, s.*, n.sumpenalty';
            $sql = "SELECT $statefields".
               "  FROM {$CFG->prefix}quiz_states s,".
               "       {$CFG->prefix}quiz_newest_states n".
               " WHERE s.id = n.newest".
               "   AND n.attemptid = '$attempt->uniqueid'".
               "   AND n.questionid = $question->id";
            $state = get_record_sql($sql);

            // restore the state of the question
            quiz_restore_state($question, $state);

            // this is the new response from the teacher
            $state->responses = $response;
            
            // grade the question with the new state made by the teacher
            $QUIZ_QTYPES[$question->qtype]->grade_responses($question, $state, $quiz);

            // finalize the grade
            $state->last_graded->grade = 0; // we dont want the next function to care about the last grade
            quiz_apply_penalty_and_timelimit($question, $state, $attempt, $quiz);

            // want to update session.  Also set changed to 1 to trick quiz_save_question_session to save our session
            $state->update = 1;
            $state->changed = 1;
            quiz_save_question_session($question, $state);
            
            // method for changing sumgrades from report type regrade.  Thanks!
            $sumgrades = 0;
            $questionids = explode(',', quiz_questions_in_quiz($attempt->layout));
            foreach($questionids as $questionid) {
                $lastgradedid = get_field('quiz_newest_states', 'newgraded', 'attemptid', $attempt->uniqueid, 'questionid', $questionid);
                $sumgrades += get_field('quiz_states', 'grade', 'id', $lastgradedid);
            }            

            if ($attempt->sumgrades != $sumgrades) {
                set_field('quiz_attempts', 'sumgrades', $sumgrades, 'id', $attempt->id);
            }

            // update user's grade
            quiz_save_best_grade($quiz, $attempt->userid);
        }
        print_string('changessaved', 'quiz');
    } else if ( ( !empty($attemptid) or !empty($gradeall) or !empty($userid)) and !empty($questionid) ) {  // need attemptid and questionid or gradeall and a questionid
        // this sql joins the attempts table and the user table
        $select = 'SELECT '.$db->Concat('u.id', '\'#\'', $db->IfNull('qa.attempt', '0')).' AS userattemptid, 
                    qa.id AS attemptid, qa.uniqueid, qa.attempt, qa.timefinish, qa.preview, 
                    u.id AS userid, u.firstname, u.lastname, u.picture ';
        $from   = 'FROM '.$CFG->prefix.'user u LEFT JOIN '.$CFG->prefix.'quiz_attempts qa ON (u.id = qa.userid AND qa.quiz = '.$quiz->id.') ';
        
        if ($gradeall) { // get all user attempts
            $where  = 'WHERE u.id IN ('.implode(',', array_keys($users)).') ';
        } else if ($userid) { // get all the attempts for a specific user
            $where = 'WHERE u.id='.$userid.' ';
        } else { // get a specific attempt
            $where = 'WHERE qa.id='.$attemptid.' ';
        }
        
        $where .= 'AND '.$db->IfNull('qa.attempt', '0').' != 0 ';
        $where .= 'AND '.$db->IfNull('qa.timefinish', '0').' != 0 '; 
        $sort = 'ORDER BY u.firstname, u.lastname, qa.attempt ASC';
        $attempts = get_records_sql($select.$from.$where.$sort);
        
        echo '<form method="post" action="grading.php">'.
            '<input type="hidden" name="quizid" value="'.$quiz->id.'">'.
            '<input type="hidden" name="sesskey" value="'.$USER->sesskey.'">'.
            '<input type="hidden" name="action" value="viewquestion">'.
            '<input type="hidden" name="questionid" value="'.$questionid.'">';        
        
        foreach ($attempts as $attempt) {
            // retrieve the state
            if (!$neweststate = get_record('quiz_newest_states', 'attemptid', $attempt->uniqueid, 'questionid', $questionid)) {
                error("Can not find newest states for attempt $attempt->uniqueid for question $questionid");
            }
            if (! $state = get_record('quiz_states', 'id', $neweststate->newest)) {
                error('Invalid state id');
            }

            // get everything ready for the question to be printed
            $instance = get_record('quiz_question_instances', 'quiz', $quiz->id, 'question', $question->id);
            $question->instance = $instance->id;
            $question->maxgrade = $instance->grade;
            $question->name_prefix = $attempt->attemptid.'_'.$state->id.'_';
            $QUIZ_QTYPES[$question->qtype]->get_question_options($question);

            quiz_restore_state($question, $state);
            $state->last_graded = $state;

            $options = quiz_get_reviewoptions($quiz, $attempt, $isteacher);
            $options->validation = ($state->event == QUIZ_EVENTVALIDATE);  // not sure what this is
            //$options->history = 'all';  // had this on, but seemed confusing for this
            
            // IF this code is expanded to manually regrade any question type, then 
            //   readonly would be set to 0 and the essay question would have to be
            //   updated.  Also, regrade would most likly be tossed.
            $options->readonly = 1;
            $options->regrade = 1;

            // print the user name, attempt count, the question, and some more hidden fields
            echo '<div align="center" width="80%" style="padding:15px;">'.
                '<p>'."$attempt->firstname $attempt->lastname: ".
                get_string('attempt', 'quiz')." $attempt->attempt".
                '</p>';
            
            quiz_print_quiz_question($question, $state, '', $quiz, $options);
            echo '<input type="hidden" name="attemptids[]" value="'.$attempt->attemptid.'">'.
                '<input type="hidden" name="stateids[]" value="'.$state->id.'">';
            echo '</div>';
        }
        echo '<div align="center"><input type="submit" value="'.get_string('save', 'quiz').'"></div>'.
            '</form>';
        print_footer($course);
        exit();
    }
    
    // our 2 different views
    // the first one displays all of the essay questions in the quiz 
    // with the number of ungraded attempts for each essay question
    
    // the second view displays the users who have answered the essay question 
    // and all of their attempts at answering the question
    switch($action) {
        case 'viewquestions':
            notify(get_string('essayonly', 'quiz'));
            // just a basic table for this...
            $table = new stdClass;
            $table->head = array(get_string("essayquestions", "quiz"), get_string("ungraded", "quiz"));
            $table->align = array("left", "left");
            $table->wrap = array("wrap", "wrap");
            $table->width = "20%";
            $table->size = array("*", "*");  
            $table->data = array();
            
            // get the essay questions
            $questionlist = quiz_questions_in_quiz($quiz->questions);
            $sql = "SELECT q.*, i.grade AS maxgrade, i.id AS instance".
                   "  FROM {$CFG->prefix}quiz_questions q,".
                   "       {$CFG->prefix}quiz_question_instances i".
                   " WHERE i.quiz = '$quiz->id' AND q.id = i.question".
                   "   AND q.id IN ($questionlist)".
                   "   AND q.qtype = '".ESSAY."'".
                   "   ORDER BY q.name";
            if (!$questions = get_records_sql($sql)) {
                print_heading(get_string('noessayquestionsfound', 'quiz'));
                print_footer($course);
                exit();
            }
            // get all the finished attempts by the users
            if ($attempts = get_records_select('quiz_attempts', "quiz = $quiz->id and timefinish > 0 and userid IN ($userids)", 'userid, attempt')) {
                foreach($questions as $question) {
                    
                    $link = "<a href=\"grading.php?quizid=$quiz->id&amp;action=viewquestion&amp;questionid=$question->id\">".
                            $question->name."</a>";
                    // determine the number of ungraded attempts (essay question thing only)
                    $ungraded = 0;
                    foreach ($attempts as $attempt) {
                        // grab the state then check if it is graded
                        if (!$neweststate = get_record('quiz_newest_states', 'attemptid', $attempt->uniqueid, 'questionid', $question->id)) {
                            error("Can not find newest states for attempt $attempt->uniqueid for question $questionid");
                        }
                        if (!$questionstate = get_record('quiz_essay_states', 'stateid', $neweststate->newest)) {
                            error('Could not find question state');
                        }
                        if (!$questionstate->graded) {
                            $ungraded++;
                        }
                    }

                    $table->data[] = array($link, $ungraded);
                }
                print_table($table);
            } else {
                print_heading(get_string('noattempts', 'quiz'));
            }
            break;
        case 'viewquestion':
            // gonna use flexible_table (first time!)
            $tablecolumns = array('picture', 'fullname', 'attempt');
            $tableheaders = array('', get_string('fullname'), get_string("attempts", "quiz"));

            $table = new flexible_table('mod-quiz-report-grading');

            $table->define_columns($tablecolumns);
            $table->define_headers($tableheaders);
            $table->define_baseurl($CFG->wwwroot.'/mod/quiz/grading.php?quizid='.$quiz->id.'&amp;action=viewquestion&amp;questionid='.$question->id);

            $table->sortable(true);
            $table->initialbars(count($users)>20);  // will show initialbars if there are more than 20 users
            $table->pageable(true);

            $table->column_suppress('fullname');
            $table->column_suppress('picture');

            $table->column_class('picture', 'picture');
            
            // attributes in the table tag
            $table->set_attribute('cellspacing', '0');
            $table->set_attribute('id', 'grading');
            $table->set_attribute('class', 'generaltable generalbox');
            $table->set_attribute('align', 'center');
            $table->set_attribute('width', '50%');
    
            // get it ready!
            $table->setup();                    
            
            // this sql is a join of the attempts table and the user table.  I do this so I can sort by user name and attempt number (not id)
            $select = 'SELECT '.$db->Concat('u.id', '\'#\'', $db->IfNull('qa.attempt', '0')).' AS userattemptid, qa.id AS attemptid, qa.uniqueid, qa.attempt, u.id AS userid, u.firstname, u.lastname, u.picture ';
            $from   = 'FROM '.$CFG->prefix.'user u LEFT JOIN '.$CFG->prefix.'quiz_attempts qa ON (u.id = qa.userid AND qa.quiz = '.$quiz->id.') ';
            $where  = 'WHERE u.id IN ('.implode(',', array_keys($users)).') ';
            $where .= 'AND '.$db->IfNull('qa.attempt', '0').' != 0 ';
            $where .= 'AND '.$db->IfNull('qa.timefinish', '0').' != 0 '; 
     
            if($table->get_sql_where()) { // forgot what this does
                $where .= 'AND '.$table->get_sql_where();
            }
            
            // sorting of the table
            if($sort = $table->get_sql_sort()) {
                $sort = 'ORDER BY '.$sort;  // seems like I would need to have u. or qa. infront of the ORDER BY attribues... but seems to work..
            } else {
                // my default sort rule
                $sort = 'ORDER BY u.firstname, u.lastname, qa.attempt ASC';
            }
            
            // set up the pagesize
            $total  = count_records_sql('SELECT COUNT(DISTINCT('.$db->Concat('u.id', '\'#\'', $db->IfNull('qa.attempt', '0')).')) '.$from.$where);
            $table->pagesize(10, $total);

            // this is for getting the correct records for a given page
            if($table->get_page_start() !== '' && $table->get_page_size() !== '') {
                $limit = ' '.sql_paging_limit($table->get_page_start(), $table->get_page_size());
            } else {
                $limit = '';
            }
            //$number = 1;
            // get the attempts and process them
            if ($attempts = get_records_sql($select.$from.$where.$sort.$limit)) {
                foreach($attempts as $attempt) {

                    $picture = print_user_picture($attempt->userid, $course->id, $attempt->picture, false, true);

                    // link here... grades all for this student                        
                    $userlink = "<a href=\"grading.php?quizid=$quiz->id&amp;questionid=$question->id&amp;userid=$attempt->userid\">".
                                $attempt->firstname.' '.$attempt->lastname.'</a>';
                    
                    // nab the state of the attempt to see if it is graded or not
                    if (!$neweststate = get_record('quiz_newest_states', 'attemptid', $attempt->uniqueid, 'questionid', $question->id)) {
                        error("Can not find newest states for attempt $attempt->uniqueid for question $questionid");
                    }

                    if (!$questionstate = get_record('quiz_essay_states', 'stateid', $neweststate->newest)) {
                        error('Could not find question state');
                    }
                    // change the color of the link based on being graded or not
                    if (!$questionstate->graded) {
                        $style = 'style="color:#FF0000"';  // red
                    } else {
                        $style = 'style="color:#008000"';  // green
                    }
                    
                    // link for the attempt
                    $attemptlink = "<a $style href=\"grading.php?quizid=$quiz->id&amp;questionid=$question->id&amp;attemptid=$attempt->attemptid\">".  // &amp;number=$number
                            $question->name." attempt $attempt->attempt</a>";
                    
                    $table->add_data( array($picture, $userlink, $attemptlink) );
                }
                //$number += $question->length;
            }
            
            // grade all and "back" links
            $links = "<center><a href=\"grading.php?quizid=$quiz->id&amp;questionid=$questionid&amp;gradeall=1\">".get_string('gradeall', 'quiz').'</a> | '.
                    "<a href=\"grading.php?quizid=$quiz->id&amp;action=viewquestions\">".get_string('backtoquestionlist', 'quiz').'</a></center>'.
        
            // print everything here
            print_heading($question->name);
            echo $links;
            echo '<div id="tablecontainer">';
            $table->print_html();
            echo '</div>';
            echo $links;            
            break;
        default:
            error("Invalid Action");
    }
    
    print_footer($course);

?>
