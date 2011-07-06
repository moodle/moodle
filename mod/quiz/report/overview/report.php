<?php
/**
 * This script lists student attempts
 *
 * @author Martin Dougiamas, Tim Hunt and others.
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package quiz
 */

require_once($CFG->libdir.'/tablelib.php');
require_once($CFG->dirroot.'/mod/quiz/report/overview/overviewsettings_form.php');
require_once($CFG->dirroot.'/mod/quiz/report/overview/overview_table.php');

class quiz_overview_report extends quiz_default_report {

    /**
     * Display the report.
     */
    function display($quiz, $cm, $course) {
        global $CFG, $COURSE, $DB, $OUTPUT;

        $this->context = get_context_instance(CONTEXT_MODULE, $cm->id);

        // Work out some display options - whether there is feedback, and whether scores should be shown.
        $hasfeedback = quiz_has_feedback($quiz);
        $fakeattempt = new stdClass();
        $fakeattempt->preview = false;
        $fakeattempt->timefinish = $quiz->timeopen;
        $fakeattempt->userid = 0;
        $reviewoptions = quiz_get_reviewoptions($quiz, $fakeattempt, $this->context);
        $showgrades = quiz_has_grades($quiz) && $reviewoptions->scores;

        $download = optional_param('download', '', PARAM_ALPHA);

        /// find out current groups mode
        $currentgroup = groups_get_activity_group($cm, true);
        if (!$students = get_users_by_capability($this->context, array('mod/quiz:reviewmyattempts', 'mod/quiz:attempt'),'u.id,1','','','','','',false)) {
            $students = array();
        } else {
            $students = array_keys($students);
        }

        if (empty($currentgroup)) {
            // all users who can attempt quizzes
            $allowed = $students;
            $groupstudents = array();
        } else {
            // all users who can attempt quizzes and who are in the currently selected group
            if (!$groupstudents = get_users_by_capability($this->context, array('mod/quiz:reviewmyattempts', 'mod/quiz:attempt'),'u.id,1','','','',$currentgroup,'',false)) {
                $groupstudents = array();
            } else {
                $groupstudents = array_keys($groupstudents);
            }
            $allowed = $groupstudents;
        }

        $pageoptions = array();
        $pageoptions['id'] = $cm->id;
        $pageoptions['mode'] = 'overview';

        $reporturl = new moodle_url('/mod/quiz/report.php', $pageoptions);
        $qmsubselect = quiz_report_qm_filter_select($quiz);

        $mform = new mod_quiz_report_overview_settings($reporturl, array('qmsubselect'=> $qmsubselect, 'quiz'=>$quiz,
                                                             'currentgroup'=>$currentgroup, 'context'=>$this->context));
        if ($fromform = $mform->get_data()) {
            $regradeall = false;
            $regradealldry = false;
            $regradealldrydo = false;
            $attemptsmode = $fromform->attemptsmode;
            if ($qmsubselect) {
                //control is not on the form if
                //the grading method is not set
                //to grade one attempt per user eg. for average attempt grade.
                $qmfilter = $fromform->qmfilter;
            } else {
                $qmfilter = 0;
            }
            $regradefilter = $fromform->regradefilter;
            set_user_preference('quiz_report_overview_detailedmarks', $fromform->detailedmarks);
            set_user_preference('quiz_report_pagesize', $fromform->pagesize);
            $detailedmarks = $fromform->detailedmarks;
            $pagesize = $fromform->pagesize;
        } else {
            $regradeall  = optional_param('regradeall', 0, PARAM_BOOL);
            $regradealldry  = optional_param('regradealldry', 0, PARAM_BOOL);
            $regradealldrydo  = optional_param('regradealldrydo', 0, PARAM_BOOL);
            $attemptsmode = optional_param('attemptsmode', null, PARAM_INT);
            if ($qmsubselect) {
                $qmfilter = optional_param('qmfilter', 0, PARAM_INT);
            } else {
                $qmfilter = 0;
            }
            $regradefilter = optional_param('regradefilter', 0, PARAM_INT);

            $detailedmarks = get_user_preferences('quiz_report_overview_detailedmarks', 1);
            $pagesize = get_user_preferences('quiz_report_pagesize', 0);
        }
        if ($currentgroup) {
            //default for when a group is selected
            if ($attemptsmode === null  || $attemptsmode == QUIZ_REPORT_ATTEMPTS_ALL) {
                $attemptsmode = QUIZ_REPORT_ATTEMPTS_STUDENTS_WITH;
            }
        } else if (!$currentgroup && $course->id == SITEID) {
            //force report on front page to show all, unless a group is selected.
            $attemptsmode = QUIZ_REPORT_ATTEMPTS_ALL;
        } else if ($attemptsmode === null) {
            //default
            $attemptsmode = QUIZ_REPORT_ATTEMPTS_ALL;
        }
        if (!$reviewoptions->scores) {
            $detailedmarks = 0;
        }
        if ($pagesize < 1) {
            $pagesize = QUIZ_REPORT_DEFAULT_PAGE_SIZE;
        }
        // We only want to show the checkbox to delete attempts
        // if the user has permissions and if the report mode is showing attempts.
        $candelete = has_capability('mod/quiz:deleteattempts', $this->context)
                && ($attemptsmode != QUIZ_REPORT_ATTEMPTS_STUDENTS_WITH_NO);

        $displayoptions = array();
        $displayoptions['attemptsmode'] = $attemptsmode;
        $displayoptions['qmfilter'] = $qmfilter;
        $displayoptions['regradefilter'] = $regradefilter;

        if ($attemptsmode == QUIZ_REPORT_ATTEMPTS_ALL) {
            $allowed = array();
        }

        if (empty($currentgroup) || $groupstudents) {
            if (optional_param('delete', 0, PARAM_BOOL) && confirm_sesskey()) {
                if ($attemptids = optional_param('attemptid', array(), PARAM_INT)) {
                    require_capability('mod/quiz:deleteattempts', $this->context);
                    $this->delete_selected_attempts($quiz, $cm, $attemptids, $allowed, $groupstudents);
                    redirect($reporturl->out(false, $displayoptions));
                }
            } else if (optional_param('regrade', 0, PARAM_BOOL) && confirm_sesskey()) {
                if ($attemptids = optional_param('attemptid', array(), PARAM_INT)) {
                    $this->regrade_selected_attempts($quiz, $attemptids, $groupstudents);
                    redirect($reporturl->out(false, $displayoptions));
                }
            }
        }

        //work out the sql for this table.
        if ($detailedmarks) {
            $questions = quiz_report_load_questions($quiz);
        } else {
            $questions = array();
        }
        $table = new quiz_report_overview_table($quiz , $qmsubselect, $groupstudents,
                $students, $detailedmarks, $questions, $candelete, $reporturl,
                $displayoptions, $this->context);
        $table->is_downloading($download, get_string('reportoverview','quiz'),
                    "$COURSE->shortname ".format_string($quiz->name,true));
        if (!$table->is_downloading()) {
            // Only print headers if not asked to download data
            $this->print_header_and_tabs($cm, $course, $quiz, "overview");
        }

        if ($regradeall && confirm_sesskey()) {
            $this->regrade_all(false, $quiz, $groupstudents);
        } else if ($regradealldry && confirm_sesskey()) {
            $this->regrade_all(true, $quiz, $groupstudents);
        } else if ($regradealldrydo && confirm_sesskey()) {
            $this->regrade_all_needed($quiz, $groupstudents);
        }
        if ($regradeall || $regradealldry || $regradealldrydo) {
            redirect($reporturl->out(false, $displayoptions), '', 5);
        }

        if ($groupmode = groups_get_activity_groupmode($cm)) {   // Groups are being used
            if (!$table->is_downloading()) {
                groups_print_activity_menu($cm, $reporturl->out(true, $displayoptions));
            }
        }

        $nostudents = false;
        if (!$students) {
            if (!$table->is_downloading()) {
                echo $OUTPUT->notification(get_string('nostudentsyet'));
            }
            $nostudents = true;
        } else if ($currentgroup && !$groupstudents) {
            if (!$table->is_downloading()) {
                echo $OUTPUT->notification(get_string('nostudentsingroup'));
            }
            $nostudents = true;
        }
        if (!$table->is_downloading()) {
            // Print display options
            $mform->set_data($displayoptions +compact('detailedmarks', 'pagesize'));
            $mform->display();

            // Print information on the number of existing attempts
            if ($strattemptnum = quiz_num_attempt_summary($quiz, $cm, true, $currentgroup)) {
                echo '<div class="quizattemptcounts">' . $strattemptnum . '</div>';
            }
        }

        if (!$nostudents || ($attemptsmode == QUIZ_REPORT_ATTEMPTS_ALL)) {

            // Construct the SQL
            $fields = $DB->sql_concat('u.id', "'#'", 'COALESCE(qa.attempt, 0)') . ' AS uniqueid,';
            if ($qmsubselect) {
                $fields .= "\n(CASE WHEN $qmsubselect THEN 1 ELSE 0 END) AS gradedattempt,";
            }

            $fields .= '
                    qa.uniqueid AS attemptuniqueid,
                    qa.id AS attempt,
                    u.id AS userid,
                    u.idnumber,
                    u.firstname,
                    u.lastname,
                    u.picture,
                    u.imagealt,
                    u.email,
                    qa.sumgrades,
                    qa.timefinish,
                    qa.timestart,
                    CASE WHEN qa.timefinish = 0 THEN null
                         WHEN qa.timefinish > qa.timestart THEN qa.timefinish - qa.timestart
                         ELSE 0 END AS duration';
            // To explain that last bit, in MySQL, qa.timestart and qa.timefinish
            // are unsigned. Since MySQL 5.5.5, when they introduced strict mode,
            // subtracting a larger unsigned int from a smaller one gave an error.
            // Therefore, we avoid doing that. timefinish can be non-zero and less
            // than timestart when you have two load-balanced servers with very
            // badly synchronised clocks, and a student does a really quick attempt.

            // This part is the same for all cases - join users and quiz_attempts tables
            $from = '{user} u ';
            $from .= 'LEFT JOIN {quiz_attempts} qa ON qa.userid = u.id AND qa.quiz = :quizid';
            $params = array('quizid' => $quiz->id);

            if ($qmsubselect && $qmfilter) {
                $from .= ' AND '.$qmsubselect;
            }
            switch ($attemptsmode) {
                 case QUIZ_REPORT_ATTEMPTS_ALL:
                     // Show all attempts, including students who are no longer in the course
                    $where = 'qa.id IS NOT NULL AND qa.preview = 0';
                     break;
                 case QUIZ_REPORT_ATTEMPTS_STUDENTS_WITH:
                     // Show only students with attempts
                     list($allowed_usql, $allowed_params) = $DB->get_in_or_equal($allowed, SQL_PARAMS_NAMED, 'u');
                     $params += $allowed_params;
                    $where = "u.id $allowed_usql AND qa.preview = 0 AND qa.id IS NOT NULL";
                     break;
                 case QUIZ_REPORT_ATTEMPTS_STUDENTS_WITH_NO:
                     // Show only students without attempts
                     list($allowed_usql, $allowed_params) = $DB->get_in_or_equal($allowed, SQL_PARAMS_NAMED, 'u');
                     $params += $allowed_params;
                    $where = "u.id $allowed_usql AND qa.id IS NULL";
                     break;
                 case QUIZ_REPORT_ATTEMPTS_ALL_STUDENTS:
                     // Show all students with or without attempts
                     list($allowed_usql, $allowed_params) = $DB->get_in_or_equal($allowed, SQL_PARAMS_NAMED, 'u');
                     $params += $allowed_params;
                    $where = "u.id $allowed_usql AND (qa.preview = 0 OR qa.preview IS NULL)";
                     break;
             }

            $table->set_count_sql("SELECT COUNT(1) FROM $from WHERE $where", $params);

            $sqlobject = new stdClass();
            $sqlobject->from = $from;
            $sqlobject->where = $where;
            $sqlobject->params = $params;
            //test to see if there are any regraded attempts to be listed.
            if (quiz_get_regraded_qs($sqlobject, 0, 1)) {
                $regradedattempts = true;
            } else {
                $regradedattempts = false;
            }
            $fields .= ', COALESCE((SELECT MAX(qqr.regraded) FROM {quiz_question_regrade} qqr WHERE qqr.attemptid = qa.uniqueid),-1) AS regraded';
            if ($regradefilter) {
                $where .= ' AND COALESCE((SELECT MAX(qqr.regraded) FROM {quiz_question_regrade} qqr WHERE qqr.attemptid = qa.uniqueid),-1) !=\'-1\'';
            }
            $table->set_sql($fields, $from, $where, $params);

            // Define table columns
            $columns = array();
            $headers = array();
            if (!$table->is_downloading()) { //do not print notices when downloading
                //regrade buttons
                if (has_capability('mod/quiz:regrade', $this->context)) {
                    $countregradeneeded = $this->count_regrade_all_needed($quiz, $groupstudents);
                    if ($currentgroup) {
                        $a= new stdClass();
                        $a->groupname = groups_get_group_name($currentgroup);
                        $a->coursestudents = get_string('participants');
                        $a->countregradeneeded = $countregradeneeded;
                        $regradealldrydolabel = get_string('regradealldrydogroup', 'quiz_overview', $a);
                        $regradealldrylabel = get_string('regradealldrygroup', 'quiz_overview', $a);
                        $regradealllabel = get_string('regradeallgroup', 'quiz_overview', $a);
                    } else {
                        $regradealldrydolabel = get_string('regradealldrydo', 'quiz_overview', $countregradeneeded);
                        $regradealldrylabel = get_string('regradealldry', 'quiz_overview');
                        $regradealllabel = get_string('regradeall', 'quiz_overview');
                    }
                    $displayurl = new moodle_url($reporturl, $displayoptions);
                    echo '<div class="mdl-align">';
                    echo '<form action="'.$displayurl->out_omit_querystring().'">';
                    echo '<div>';
                    echo html_writer::input_hidden_params($displayurl);
                    echo html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey())) . "\n";
                    echo '<input type="submit" name="regradeall" value="'.$regradealllabel.'"/>';
                    echo '<input type="submit" name="regradealldry" value="'.$regradealldrylabel.'"/>';
                    if ($countregradeneeded) {
                        echo '<input type="submit" name="regradealldrydo" value="'.$regradealldrydolabel.'"/>';
                    }
                    echo '</div>';
                    echo '</form>';
                    echo '</div>';
                }
                // Print information on the grading method
                if ($strattempthighlight = quiz_report_highlighting_grading_method($quiz, $qmsubselect, $qmfilter)) {
                    echo '<div class="quizattemptcounts">' . $strattempthighlight . '</div>';
                }
            }

            if (!$table->is_downloading() && $candelete) {
                $columns[]= 'checkbox';
                $headers[]= NULL;
            }

            if (!$table->is_downloading() && $CFG->grade_report_showuserimage) {
                $columns[]= 'picture';
                $headers[]= '';
            }
            if (!$table->is_downloading()) {
                $columns[]= 'fullname';
                $headers[]= get_string('name');
             } else {
                $columns[]= 'lastname';
                $headers[]= get_string('lastname');
                $columns[]= 'firstname';
                $headers[]= get_string('firstname');
             }

            if ($CFG->grade_report_showuseridnumber) {
                $columns[]= 'idnumber';
                $headers[]= get_string('idnumber');
            }

            $columns[]= 'timestart';
            $headers[]= get_string('startedon', 'quiz');

            $columns[]= 'timefinish';
            $headers[]= get_string('timecompleted','quiz');

            $columns[]= 'duration';
            $headers[]= get_string('attemptduration', 'quiz');

            if ($detailedmarks) {
                foreach ($questions as $id => $question) {
                    // Ignore questions of zero length
                    $columns[] = 'qsgrade'.$id;
                    $header = '#'.$question->number;
                    if (!$table->is_downloading()) {
                        $header .='<br />';
                    } else {
                        $header .=' ';
                    }
                    $header .='--/'.quiz_rescale_grade($question->maxgrade, $quiz, 'question');
                    $headers[] = $header;
                    $question->formattedname = strip_tags(format_string($question->name));
                }
            }
            if (!$table->is_downloading() && has_capability('mod/quiz:regrade', $this->context) && $regradedattempts) {
                $columns[] = 'regraded';
                $headers[] = get_string('regrade', 'quiz_overview');
            }
            if ($showgrades) {
                $columns[] = 'sumgrades';
                $headers[] = get_string('grade', 'quiz').'/'.quiz_format_grade($quiz, $quiz->grade);
             }

            if ($hasfeedback) {
                $columns[] = 'feedbacktext';
                $headers[] = get_string('feedback', 'quiz');
             }

            $table->define_columns($columns);
            $table->define_headers($headers);
            $table->sortable(true, 'uniqueid');

            // Set up the table
            $table->define_baseurl($reporturl->out(true, $displayoptions));

            $table->collapsible(true);

            $table->no_sorting('feedbacktext');

            $table->column_class('picture', 'picture');
            $table->column_class('lastname', 'bold');
            $table->column_class('firstname', 'bold');
            $table->column_class('fullname', 'bold');
            $table->column_class('sumgrades', 'bold');

            $table->set_attribute('id', 'attempts');

            $table->out($pagesize, true);
        }
        if (!$table->is_downloading() && $showgrades) {
            if ($currentgroup && $groupstudents) {
                list($usql, $params) = $DB->get_in_or_equal($groupstudents);
                $params[] = $quiz->id;
                if ($DB->record_exists_select('quiz_grades', "userid $usql AND quiz = ?", $params)) {
                     $imageurl = "{$CFG->wwwroot}/mod/quiz/report/overview/overviewgraph.php?id={$quiz->id}&amp;groupid=$currentgroup";
                     $graphname = get_string('overviewreportgraphgroup', 'quiz_overview', groups_get_group_name($currentgroup));
                     echo $OUTPUT->heading($graphname);
                     echo '<div class="graph"><img src="'.$imageurl.'" alt="'.$graphname.'" /></div>';
                }
            }
            if ($DB->record_exists('quiz_grades', array('quiz'=> $quiz->id))) {
                 $graphname = get_string('overviewreportgraph', 'quiz_overview');
                 $imageurl = $CFG->wwwroot.'/mod/quiz/report/overview/overviewgraph.php?id='.$quiz->id;
                 echo $OUTPUT->heading($graphname);
                 echo '<div class="graph"><img src="'.$imageurl.'" alt="'.$graphname.'" /></div>';
            }
        }
        return true;
    }
    /**
     * @param bool changedb whether to change contents of state and grades
     * tables.
     */
    function regrade_all($dry, $quiz, $groupstudents) {
        global $DB, $OUTPUT;
        if (!has_capability('mod/quiz:regrade', $this->context)) {
            echo $OUTPUT->notification(get_string('regradenotallowed', 'quiz'));
            return true;
        }
        // Fetch all attempts
        if ($groupstudents) {
            list($usql, $params) = $DB->get_in_or_equal($groupstudents);
            $select = "userid $usql AND ";
        } else {
            $select = '';
            $params = array();
        }
        $select .= "quiz = ? AND preview = 0";
        $params[] = $quiz->id;
        if (!$attempts = $DB->get_records_select('quiz_attempts', $select, $params)) {
            echo $OUTPUT->heading(get_string('noattempts', 'quiz'));
            return true;
        }

        $this->clear_regrade_table($quiz, $groupstudents);

        // Fetch all questions
        $questions = question_load_questions(explode(',',quiz_questions_in_quiz($quiz->questions)), 'qqi.grade AS maxgrade, qqi.id AS instance',
            '{quiz_question_instances} qqi ON qqi.quiz = ' . $quiz->id . ' AND q.id = qqi.question');

        // Print heading
        echo $OUTPUT->heading(get_string('regradingquiz', 'quiz', format_string($quiz->name)));
        $qstodo = count($questions);
        $qsdone = 0;
        if ($qstodo > 1) {
            $qpb = new progress_bar('qregradingbar', 500, true);
            $qpb->update($qsdone, $qstodo, "Question $qsdone of $qstodo");
        }
        $apb = new progress_bar('aregradingbar', 500, true);

        // Loop through all questions and all attempts and regrade while printing progress info
        $attemptstodo = count($attempts);
        foreach ($questions as $question) {
            $attemptsdone = 0;
            $apb->restart();
            echo '<p class="mdl-align"><strong>'.get_string('regradingquestion', 'quiz', $question->name).'</strong></p>';
            @flush();@ob_flush();
            foreach ($attempts as $attempt) {
                set_time_limit(30);
                $changed = regrade_question_in_attempt($question, $attempt, $quiz, true, $dry);

                $attemptsdone++;
                $a = new stdClass();
                $a->done = $attemptsdone;
                $a->todo = $attemptstodo;
                $apb->update($attemptsdone, $attemptstodo, get_string('attemptprogress', 'quiz_overview', $a));
            }
            $qsdone++;
            if (isset($qpb)) {
                $a = new stdClass();
                $a->done = $qsdone;
                $a->todo = $qstodo;
                $qpb->update($qsdone, $qstodo, get_string('qprogress', 'quiz_overview', $a));
            }
            // the following makes sure that the output is sent immediately.
            @flush();@ob_flush();
        }

        if (!$dry) {
            $this->check_overall_grades($quiz, $groupstudents);
        }
    }
    function count_regrade_all_needed($quiz, $groupstudents) {
        global $DB;
        // Fetch all attempts that need regrading
        if ($groupstudents) {
            list($usql, $params) = $DB->get_in_or_equal($groupstudents);
            $where = "qa.userid $usql AND ";
        } else {
            $where = '';
            $params = array();
        }
        $where .= "qa.quiz = ? AND qa.preview = 0 AND qa.uniqueid = qqr.attemptid AND qqr.regraded = 0";
        $params[] = $quiz->id;
        return $DB->get_field_sql('SELECT COUNT(1) FROM {quiz_attempts} qa, {quiz_question_regrade} qqr WHERE '. $where, $params);
    }
    function regrade_all_needed($quiz, $groupstudents) {
        global $DB, $OUTPUT;
        if (!has_capability('mod/quiz:regrade', $this->context)) {
            echo $OUTPUT->notification(get_string('regradenotallowed', 'quiz'));
            return;
        }
        // Fetch all attempts that need regrading
        if ($groupstudents) {
            list($usql, $params) = $DB->get_in_or_equal($groupstudents);
            $where = "qa.userid $usql AND ";
        } else {
            $where = '';
            $params = array();
        }
        $where .= "qa.quiz = ? AND qa.preview = 0 AND qa.uniqueid = qqr.attemptid AND qqr.regraded = 0";
        $params[] = $quiz->id;
        if (!$attempts = $DB->get_records_sql('SELECT qa.*, qqr.questionid FROM {quiz_attempts} qa, {quiz_question_regrade} qqr WHERE '. $where, $params)) {
            echo $OUTPUT->heading(get_string('noattemptstoregrade', 'quiz_overview'));
            return true;
        }
        $this->clear_regrade_table($quiz, $groupstudents);
        // Fetch all questions
        $questions = question_load_questions(explode(',',quiz_questions_in_quiz($quiz->questions)), 'qqi.grade AS maxgrade, qqi.id AS instance',
            '{quiz_question_instances} qqi ON qqi.quiz = ' . $quiz->id . ' AND q.id = qqi.question');

        // Print heading
        echo $OUTPUT->heading(get_string('regradingquiz', 'quiz', format_string($quiz->name)));

        $apb = new progress_bar('aregradingbar', 500, true);

        // Loop through all questions and all attempts and regrade while printing progress info
        $attemptstodo = count($attempts);
        $attemptsdone = 0;
        @flush();@ob_flush();
        $attemptschanged = array();
        foreach ($attempts as $attempt) {
            $question = $questions[$attempt->questionid];
            $changed = regrade_question_in_attempt($question, $attempt, $quiz, true);
            if ($changed) {
                $attemptschanged[] = $attempt->uniqueid;
                $usersschanged[] = $attempt->userid;
            }
            if (!empty($apb)) {
                $attemptsdone++;
                $a = new stdClass();
                $a->done = $attemptsdone;
                $a->todo = $attemptstodo;
                $apb->update($attemptsdone, $attemptstodo, get_string('attemptprogress', 'quiz_overview', $a));
            }
        }
        $this->check_overall_grades($quiz, array(), $attemptschanged);
    }

    function clear_regrade_table($quiz, $groupstudents) {
        global $DB;
        // Fetch all attempts that need regrading
        if ($groupstudents) {
            list($usql, $params) = $DB->get_in_or_equal($groupstudents);
            $where = "userid $usql AND ";
        } else {
            $usql = '';
            $where = '';
            $params = array();
        }
        $params[] = $quiz->id;
        $delsql = 'DELETE FROM {quiz_question_regrade} WHERE attemptid IN
                (SELECT uniqueid FROM {quiz_attempts} WHERE ' . $where . ' quiz = ?)';
        if (!$DB->execute($delsql, $params)) {
            print_error('err_failedtodeleteregrades', 'quiz_overview');
        }
    }

    function check_overall_grades($quiz, $userids=array(), $attemptids=array()) {
        global $DB;
        //recalculate $attempt->sumgrade
        //already updated in regrade_question_in_attempt
        $sql = "UPDATE {quiz_attempts} SET sumgrades= " .
                    "COALESCE((SELECT SUM(qs.grade) FROM {question_sessions} qns, {question_states} qs " .
                        "WHERE qns.newgraded = qs.id AND qns.attemptid = {quiz_attempts}.uniqueid ), 0) WHERE ";
        $attemptsql='';
        if (!$attemptids) {
            if ($userids) {
                list($usql, $params) = $DB->get_in_or_equal($userids);
                $attemptsql .= "{quiz_attempts}.userid $usql AND ";
            } else {
                $params = array();
            }
            $attemptsql .= "{quiz_attempts}.quiz =? AND preview = 0";
            $params[] = $quiz->id;
        } else {
            list($asql, $params) = $DB->get_in_or_equal($attemptids);
            $attemptsql .= "{quiz_attempts}.uniqueid $asql";
        }
        $sql .= $attemptsql;
        if (!$DB->execute($sql, $params)) {
            print_error('err_failedtorecalculateattemptgrades', 'quiz_overview');
        }

        // Update the overall quiz grades
        if ($attemptids) {
            //make sure we fetch all attempts for users to calculate grade.
            //not just those that have changed.
            $sql = "SELECT qa2.* FROM {quiz_attempts} qa2 WHERE " .
                    "qa2.userid IN (SELECT DISTINCT userid FROM {quiz_attempts} WHERE $attemptsql) " .
                    "AND qa2.timefinish > 0 AND qa2.quiz = ?";
            $params[] = $quiz->id;
        } else {
            $sql = "SELECT * FROM {quiz_attempts} WHERE $attemptsql AND timefinish > 0";
        }
        if ($attempts = $DB->get_records_sql($sql, $params)) {
            $attemptsbyuser = quiz_report_index_by_keys($attempts, array('userid', 'id'));
            foreach($attemptsbyuser as $userid => $attemptsforuser) {
                quiz_save_best_grade($quiz, $userid, $attemptsforuser);
            }
        }
    }

    function delete_selected_attempts($quiz, $cm, $attemptids, $allowed, $groupstudents) {
        global $DB, $COURSE;
        foreach($attemptids as $attemptid) {
            $attempt = $DB->get_record('quiz_attempts', array('id' => $attemptid));
            if (!$attempt || $attempt->quiz != $quiz->id || $attempt->preview != 0) {
                // Ensure the attempt exists, and belongs to this quiz. If not skip.
                continue;
            }
            if ($allowed && !array_key_exists($attempt->userid, $allowed)) {
                // Ensure the attempt belongs to a student included in the report. If not skip.
                continue;
            }
            if ($groupstudents && !array_key_exists($attempt->userid, $groupstudents)) {
                // Additional check in groups mode.
                continue;
            }
            add_to_log($COURSE->id, 'quiz', 'delete attempt', 'report.php?id=' . $cm->id,
                    $attemptid, $cm->id);
            quiz_delete_attempt($attempt, $quiz);
        }
    }

    function regrade_selected_attempts($quiz, $attemptids, $groupstudents) {
        global $DB;
        require_capability('mod/quiz:regrade', $this->context);
        if ($groupstudents) {
            list($usql, $params) = $DB->get_in_or_equal($groupstudents);
            $where = "qa.userid $usql AND ";
        } else {
            $params = array();
            $where = '';
        }
        list($asql, $aparams) = $DB->get_in_or_equal($attemptids);
        $where = "qa.id $asql AND ";
        $params = array_merge($params, $aparams);

        $where .= "qa.quiz = ? AND qa.preview = 0";
        $params[] = $quiz->id;
        if (!$attempts = $DB->get_records_sql('SELECT qa.* FROM {quiz_attempts} qa WHERE '. $where, $params)) {
            print_error('noattemptstoregrade', 'quiz_overview');
        }

        // Fetch all questions
        $questions = question_load_questions(explode(',',quiz_questions_in_quiz($quiz->questions)), 'qqi.grade AS maxgrade, qqi.id AS instance',
            '{quiz_question_instances} qqi ON qqi.quiz = ' . $quiz->id . ' AND q.id = qqi.question');
        $updateoverallgrades = array();
        foreach($attempts as $attempt) {
            foreach ($questions as $question) {
                $changed = regrade_question_in_attempt($question, $attempt, $quiz, true);
            }
            $updateoverallgrades[] = $attempt->uniqueid;
        }
        $this->check_overall_grades($quiz, array(), $updateoverallgrades);
    }
}
