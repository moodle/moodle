<?php  // $Id$

// This script lists student attempts

    require_once("../../config.php");
    require_once("locallib.php");
    require_once($CFG->libdir.'/tablelib.php');

    optional_variable($id);    // Course Module ID, or
    optional_variable($q);     // quiz ID

    if ($id) {
        if (! $cm = get_record("course_modules", "id", $id)) {
            error("Course Module ID was incorrect");
        }

        if (! $course = get_record("course", "id", $cm->course)) {
            error("Course is misconfigured");
        }

        if (! $quiz = get_record("quiz", "id", $cm->instance)) {
            error("Course module is incorrect");
        }

    } else {
        if (! $quiz = get_record("quiz", "id", $q)) {
            error("Course module is incorrect");
        }
        if (! $course = get_record("course", "id", $quiz->course)) {
            error("Course is misconfigured");
        }
        if (! $cm = get_coursemodule_from_instance("quiz", $quiz->id, $course->id)) {
            error("Course Module ID was incorrect");
        }
    }

    require_login($course->id, false);

    if (!isteacher($course->id)) {
        error("You are not allowed to use this script");
    }

    // if no questions have been set up yet redirect to edit.php
    if (!$quiz->questions and isteacheredit($course->id)) {
        redirect('edit.php?quizid='.$quiz->id);
    }

    add_to_log($course->id, "quiz", "attempts", "attempts.php?id=$cm->id", "$quiz->id", "$cm->id");

/// Define some strings

    $strquizzes = get_string("modulenameplural", "quiz");
    $strquiz  = get_string("modulename", "quiz");
    $strreallydel  = addslashes(get_string('deleteattemptcheck','quiz'));
    $strnoattempts = get_string('noattempts','quiz');
    $strtimeformat = get_string('strftimedatetime');
    $strreviewquestion = get_string('reviewresponse', 'quiz');

/// Print the page header

    print_header_simple(format_string($quiz->name), "",
                 "<a href=\"index.php?id=$course->id\">$strquizzes</a>
                  -> ".format_string($quiz->name),
                 "", "", true, update_module_button($cm->id, $course->id, $strquiz), navmenu($course, $cm));

/// Print the tabs

    $currenttab = 'attempts';
    include('tabs.php');

/// Deal with actions

    $action = optional_param('action', '');

    switch($action) {
        case 'delete':  /// Some attempts need to be deleted
            // the following needs to be improved to delete all associated data as well

            $attemptids = isset($_POST['attemptid']) ? $_POST['attemptid'] : array();
            if(!is_array($attemptids) || empty($attemptids)) {
                break;
            }

            foreach($attemptids as $num => $attemptid) {
                if(empty($attemptid)) {
                    unset($attemptids[$num]);
                }
            }

            foreach($attemptids as $attemptid) {
                if ($todelete = get_record('quiz_attempts', 'id', $attemptid)) {

                    delete_records('quiz_attempts', 'id', $attemptid);
                    delete_records('quiz_states', 'attempt', $attemptid);

                    // Search quiz_attempts for other instances by this user.
                    // If none, then delete record for this quiz, this user from quiz_grades
                    // else recalculate best grade

                    $userid = $todelete->userid;
                    if (!record_exists('quiz_attempts', 'userid', $userid, 'quiz', $quiz->id)) {
                        delete_records('quiz_grades', 'userid', $userid,'quiz', $quiz->id);
                    } else {
                        quiz_save_best_grade($quiz, $userid);
                    }
                }
            }
        break;
    }

/// Check to see if groups are being used in this quiz
    if ($groupmode = groupmode($course, $cm)) {   // Groups are being used
        $currentgroup = setup_and_print_groups($course, $groupmode, "attempts.php?id=$cm->id&amp;mode=overview");
    } else {
        $currentgroup = false;
    }

/// Get all students
    if ($currentgroup) {
        $users = get_group_students($currentgroup);
    }
    else {
        $users = get_course_students($course->id);
    }

    if(empty($users)) {
        print_heading($strnoattempts);
        return true;
    }

/// Set table options
    if(!isset($SESSION->quiz_overview_table)) {
        $SESSION->quiz_overview_table = array('noattempts' => false, 'detailedmarks' => false);
    }

    foreach($SESSION->quiz_overview_table as $option => $value) {
        $urlparam = optional_param($option, NULL);
        if($urlparam === NULL) {
            $$option = $value;
        }
        else {
            $$option = $SESSION->quiz_overview_table[$option] = $urlparam;
        }
    }

/// Print display options

    echo '<div class="controls">';
    echo '<form method="post" action="attempts.php">';
    echo '<p>'.get_string('displayoptions', 'quiz').': ';
    echo '<input type="hidden" name="id" value="'.$cm->id.'" />';
    echo '<input type="hidden" name="noattempts" value="0" />';
    echo '<input type="hidden" name="detailedmarks" value="0" />';
    echo '<input type="checkbox" id="checknoattempts" name="noattempts" '.($noattempts?'checked="checked" ':'').'value="1" /> <label for="checknoattempts">'.get_string('shownoattempts', 'quiz').'</label> ';
    echo '<input type="checkbox" id="checkdetailedmarks" name="detailedmarks" '.($detailedmarks?'checked="checked" ':'').'value="1" /> <label for="checkdetailedmarks">'.get_string('showdetailedmarks', 'quiz').'</label> ';
    echo '<input type="submit" value="'.get_string('go').'" />';
    echo '</p>';
    echo '</form>';
    echo '</div>';

/// Define table columns
    $tablecolumns = array('checkbox', 'picture', 'fullname', 'timestart', 'duration');
    $tableheaders = array(NULL, '', get_string('fullname'), get_string('startedon', 'quiz'), get_string('attemptduration', 'quiz'));

    if ($quiz->grade) {
        $tablecolumns[] = 'sumgrades';
        $tableheaders[] = get_string('grade', 'quiz').'/'.$quiz->grade;
    }

    if($detailedmarks) {
        // we want to display marks for all questions
        // Start by getting all questions
        $questionlist = quiz_questions_in_quiz($quiz->questions);
        $questionids = explode(',', $questionlist);
        $sql = "SELECT q.*, i.grade AS maxgrade, i.id AS instance".
           "  FROM {$CFG->prefix}quiz_questions q,".
           "       {$CFG->prefix}quiz_question_instances i".
           " WHERE i.quiz = '$quiz->id' AND q.id = i.question".
           "   AND q.id IN ($questionlist)";
        if (!$questions = get_records_sql($sql)) {
            error('No questions found');
        }
        $number = 1;
        foreach($questionids as $key => $id) {
            if ($questions[$id]->length) {
                // Only print questions of non-zero length
                $tablecolumns[] = '$'.$id;
                $tableheaders[] = '#'.$number;
                $questions[$id]->number = $number;
                $number += $questions[$id]->length;
            } else {
                // get rid of zero length questions
                unset($questions[$id]);
                unset($questionids[$key]);
            }
        }
    }

/// Set up the table

    $table = new flexible_table('mod-quiz-report-overview-report');

    $table->define_columns($tablecolumns);
    $table->define_headers($tableheaders);
    $table->define_baseurl($CFG->wwwroot.'/mod/quiz/attempts.php?id='.$cm->id);

    $table->sortable(true);
    $table->collapsible(true);
    $table->initialbars(count($users)>20);

    $table->column_suppress('picture');
    $table->column_suppress('fullname');

    $table->column_class('picture', 'picture');

    $table->set_attribute('cellspacing', '0');
    $table->set_attribute('id', 'attempts');
    $table->set_attribute('class', 'generaltable generalbox');

    // Start working -- this is necessary as soon as the niceties are over
    $table->setup();


/// Construct the SQL

    if($where = $table->get_sql_where()) {
        $where .= ' AND ';
    }

    if($sort = $table->get_sql_sort()) {
        $sortparts = explode(',', $sort);
        $newsort   = array();
        $firsttime = true;
        foreach($sortparts as $sortpart) {
            $sortpart = trim($sortpart);
            if(substr($sortpart, 0, 1) == '$') {
                if($firsttime) {
                    $qnum      = intval(substr($sortpart, 1));
                    $where    .= '('.sql_isnull('qr.question').' OR qr.question = '.$qnum.') AND ';
                    $newsort[] = 'grade '.(strpos($sortpart, 'ASC')? 'ASC' : 'DESC');
                    $firsttime = false;
                }
            }
            else {
                $newsort[] = $sortpart;
            }
        }
        $sort = ' ORDER BY '.implode(', ', $newsort);
    }

    $select = 'SELECT '.$db->Concat('u.id', '\'#\'', $db->IfNull('qa.attempt', '0')).' AS uvsa, u.id AS userid, u.firstname, u.lastname, u.picture, qa.id AS attempt, qa.sumgrades, qa.timefinish, qa.timestart, qa.timefinish - qa.timestart AS duration ';
    $group  = 'GROUP BY uvsa';
    $sql = 'FROM '.$CFG->prefix.'user u '.
           'LEFT JOIN '.$CFG->prefix.'quiz_attempts qa ON u.id = qa.userid '.
           'LEFT JOIN '.$CFG->prefix.'quiz_states qr ON qr.attempt = qa.id '.
           'WHERE '.$where.'u.id IN ('.implode(',', array_keys($users)).') AND ('.($noattempts ? sql_isnull('qa.quiz').' OR ' : '') . 'qa.quiz = '.$quiz->id.') ';


    $total = count_records_sql('SELECT COUNT(DISTINCT('.$db->Concat('u.id', '\'#\'', $db->IfNull('qa.attempt', '0')).')) '.$sql);
    $table->pagesize(10, $total);

    if($table->get_page_start() !== '' && $table->get_page_size() !== '') {
        $limit = ' '.sql_paging_limit($table->get_page_start(), $table->get_page_size());
    }
    else {
        $limit = '';
    }

/// Fetch the attempts

    $attempts = get_records_sql($select.$sql.$group.$sort.$limit);

/// Build table rows

    if(!empty($attempts)) {

        foreach ($attempts as $attempt) {

            $picture = print_user_picture($attempt->userid, $course->id, $attempt->picture, false, true);

            $row = array(
                      '<input type="checkbox" name="attemptid[]" value="'.$attempt->attempt.'" />',
                      $picture,
                      '<a href="'.$CFG->wwwroot.'/user/view.php?id='.$attempt->userid.'&amp;course='.$course->id.'">'.fullname($attempt).'</a>',
                      empty($attempt->attempt) ? '-' : '<a href="review.php?q='.$quiz->id.'&amp;attempt='.$attempt->attempt.'">'.userdate($attempt->timestart, $strtimeformat).'</a>',
                      empty($attempt->attempt) ? '-' :
                       (empty($attempt->timefinish) ? get_string('unfinished', 'quiz') :
                        format_time($attempt->duration))
                   );

            if ($quiz->grade) {
                $row[] = $attempt->sumgrades === NULL ? '-' : '<a href="review.php?q='.$quiz->id.'&amp;attempt='.$attempt->attempt.'">'.round($attempt->sumgrades / $quiz->sumgrades * $quiz->grade,$quiz->decimalpoints).'</a>';
            }

            if($detailedmarks) {
                if(empty($attempt->attempt)) {
                    foreach($questionids as $questionid) {
                        $row[] = '-';
                    }
                }
                else {
                    foreach($questionids as $questionid) {
                        if ($gradedstateid = get_field('quiz_newest_states', 'newgraded', 'attemptid', $attempt->attempt, 'questionid', $questionid)) {
                            $grade = round(get_field('quiz_states', 'grade', 'id', $gradedstateid), $quiz->decimalpoints);
                        } else { 
                            // This is an old-style attempt
                            $grade = round(get_field('quiz_states', 'grade', 'attempt', $attempt->attempt, 'question', $questionid), $quiz->decimalpoints);
                        }
                        $row[] = link_to_popup_window ('/mod/quiz/reviewquestion.php?state='.$gradedstateid.'&amp;number='.$questions[$questionid]->number, 'reviewquestion', $grade, 450, 650, $strreviewquestion, 'none', true);
                    }
                }
            }

            $table->add_data($row);
        }

/// Start form

        echo '<div id="tablecontainer">';
        echo '<form id="attemptsform" method="post" action="attempts.php" onsubmit="var menu = document.getElementById(\'actionmenu\'); return (menu.options[menu.selectedIndex].value == \'delete\' ? \''.$strreallydel.'\' : true);">';
        echo '<input type="hidden" name="id" value="'.$cm->id.'" />';

/// Print table

        $table->print_html();

/// Print "Select all" etc.

        echo '<table id="commands">';
        echo '<tr><td>';
        echo '<a href="javascript:select_all_in(\'DIV\', null, \'tablecontainer\');">'.get_string('selectall', 'quiz').'</a> / ';
        echo '<a href="javascript:deselect_all_in(\'DIV\', null, \'tablecontainer\');">'.get_string('selectnone', 'quiz').'</a> ';
        echo '&nbsp;&nbsp;';
        $options = array('delete' => get_string('delete'));
        $menu = str_replace('<select', '<select id="actionmenu"', choose_from_menu($options, 'action', '', get_string('withselected', 'quiz'), 'if(this.selectedIndex > 0) submitFormById(\'attemptsform\');', '', true));
        echo $menu;
        echo '<noscript id="noscriptactionmenu" style="display: inline;">';
        echo '<input type="submit" value="'.get_string('go').'" /></noscript>';
        echo '<script type="text/javascript">'."\n<!--\n".'document.getElementById("noscriptactionmenu").style.display = "none";'."\n-->\n".'</script>';
        echo '</td></tr></table>';

/// Close form
        echo '</form></div>';
    }
    else {
        print_heading(get_string('noattemptstoshow', 'quiz'));
    }


/// Print footer

    print_footer($course);

?>
