<?php  // $Id$

require_once($CFG->libdir.'/tablelib.php');
        
/// Overview report just displays a big table of all the attempts

class quiz_report extends quiz_default_report {

    function display($quiz, $cm, $course) {     /// This function just displays the report

        global $CFG, $SESSION, $db;

        $strreallydel  = addslashes(get_string('deleteattemptcheck','quiz'));
        $strnoattempts = get_string('noattempts','quiz');
        $strtimeformat = get_string('strftimedatetime');

        $action = optional_param('action', '');

        switch($action) {
            case 'delete':  /// Some attempts need to be deleted

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
                        delete_records('quiz_responses', 'attempt', $attemptid);

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

        if(!isset($SESSION->quiz_overview_table)) {
            $SESSION->quiz_overview_table = array('noattempts' => false, 'teacherattempts' => true, 'detailedmarks' => false);
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

        $tablecolumns = array('checkbox', 'picture', 'fullname', 'timefinish', 'duration', 'sumgrades');
        $tableheaders = array(NULL, '', get_string('fullname'), get_string('attemptedon', 'quiz'), get_string('attemptduration', 'quiz'), get_string('grade', 'quiz').'/'.$quiz->grade);

        if($detailedmarks) {
            $questions = explode(',', $quiz->questions);
            foreach($questions as $number => $questionid) {
                $tablecolumns[] = '$'.$questionid;
                $tableheaders[] = '#'.($number + 1);
            }
        }

        $table = new flexible_table('mod-quiz-report-overview-report');

        $table->define_columns($tablecolumns);
        $table->define_headers($tableheaders);
        $table->define_baseurl($CFG->wwwroot.'/mod/quiz/report.php?id='.$cm->id);

        $table->sortable(true);
        $table->collapsible(true);
        $table->initialbars(true);

        $table->column_suppress('picture');
        $table->column_suppress('fullname');

        $table->column_class('picture', 'picture');

        $table->set_attribute('cellspacing', '0');
        $table->set_attribute('id', 'attempts');
        $table->set_attribute('class', 'generaltable generalbox');
        
        // Start working -- this is necessary as soon as the niceties are over
        $table->setup();


    /// Check to see if groups are being used in this quiz
        if ($groupmode = groupmode($course, $cm)) {   // Groups are being used
            $currentgroup = setup_and_print_groups($course, $groupmode, "report.php?id=$cm->id&amp;mode=overview");
        } else {
            $currentgroup = false;
        }

    /// Get all teachers and students
        if ($currentgroup) {
            $users = get_group_users($currentgroup);
        }
        else {
            $users = get_course_users($course->id);
        }

        if(!$teacherattempts) {
            $teachers = get_course_teachers($course->id);
            if(!empty($teachers)) {
                $keys = array_keys($teachers);
            }
            foreach($keys as $key) {
                unset($users[$key]);
            }
        }

        if(empty($users)) {
            print_heading($strnoattempts);
            return true;
        }

        // Construct the SQL

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

        $select = 'SELECT '.$db->Concat('u.id', '\'#\'', $db->IfNull('qa.attempt', '0')).' AS uvsa, u.id AS userid, u.firstname, u.lastname, u.picture, qa.id AS attempt, qa.sumgrades, qa.timefinish, qa.timefinish - qa.timestart AS duration ';
        $group  = 'GROUP BY uvsa';
        $sql = 'FROM '.$CFG->prefix.'user u '.
               'LEFT JOIN '.$CFG->prefix.'quiz_attempts qa ON u.id = qa.userid '.
               'LEFT JOIN '.$CFG->prefix.'quiz_responses qr ON qr.attempt = qa.id '.
               'WHERE '.$where.'u.id IN ('.implode(',', array_keys($users)).') AND ('.($noattempts ? sql_isnull('qa.quiz').' OR ' : '') . 'qa.quiz = '.$quiz->id.') ';

        $total = count_records_sql('SELECT COUNT(DISTINCT('.$db->Concat('u.id', '\'#\'', $db->IfNull('qa.attempt', '0')).')) '.$sql);
        $table->pagesize(10, $total);

        if($table->get_page_start() !== '' && $table->get_page_size() !== '') {
            $limit = ' '.sql_paging_limit($table->get_page_start(), $table->get_page_size());
        }
        else {
            $limit = '';
        }

        $attempts = get_records_sql($select.$sql.$group.$sort.$limit);

        if(!empty($attempts)) {

            foreach ($attempts as $attempt) {
    
                $picture = print_user_picture($attempt->userid, $course->id, $attempt->picture, false, true);
    
                $row = array(
                          '<input type="checkbox" name="attemptid[]" value="'.$attempt->attempt.'" />',
                          $picture, 
                          '<a href="'.$CFG->wwwroot.'/user/view.php?id='.$attempt->userid.'&amp;course='.$course->id.'">'.fullname($attempt).'</a>',
                          empty($attempt->attempt) ? '-' : '<a href="review.php?q='.$quiz->id.'&amp;attempt='.$attempt->attempt.'">'.userdate($attempt->timefinish, $strtimeformat).'</a>',
                          empty($attempt->attempt) ? '-' : format_time($attempt->duration),
                          $attempt->sumgrades === NULL ? '-' : format_float($attempt->sumgrades / $quiz->sumgrades * $quiz->grade,$quiz->decimalpoints)
                       );
    
                if($detailedmarks) {
                    if(empty($attempt->attempt)) {
                        foreach($questions as $question) {
                            $row[] = '-';
                        }
                    }
                    else {
                        $responses = get_records('quiz_responses', 'attempt', $attempt->attempt, 'question');
                        foreach($responses as $response) {
                            $row[] = $response->grade;
                        }
                    }
                }
    
                $table->add_data($row);
            }

            echo '<div id="tablecontainer">';
            echo '<form id="attemptsform" method="post" action="report.php" onsubmit="var menu = document.getElementById(\'actionmenu\'); return confirm_if(menu.options[menu.selectedIndex].value == \'delete\', \''.$strreallydel.'\');">';
            echo '<input type="hidden" name="id" value="'.$cm->id.'" />';

        }

        $table->print_html();

        if(!empty($attempts)) {
            echo '<table id="commands">';
            echo '<tr><td>';
            echo '<a href="javascript:select_all_in(\'DIV\', null, \'tablecontainer\');">'.get_string('selectall', 'quiz').'</a> / ';
            echo '<a href="javascript:deselect_all_in(\'DIV\', null, \'tablecontainer\');">'.get_string('selectnone', 'quiz').'</a> ';
            echo '</td><td style="text-align: right;">';
            $options = array('dummy' => ' ', 'delete' => get_string('delete'));
            $menu = str_replace('<select', '<select id="actionmenu"', choose_from_menu($options, 'action', '', get_string('selectedattempts', 'quiz'), 'if(this.selectedIndex > 1) submitFormById(\'attemptsform\');', '', true));
            echo $menu;
            echo '<noscript id="noscriptactionmenu" style="display: inline;">';
            echo '<input type="submit" value="'.get_string('go').'" /></noscript>';
            echo '<script type="text/javascript">'."\n<!--\n".'document.getElementById("noscriptactionmenu").style.display = "none";'."\n-->\n".'</script>';
            echo '</td></tr></table>';
            echo '</form></div>';
        }
        else {
            print_heading(get_string('noattemptsmatchingfilter', 'quiz', strtolower($course->students)));
        }

        echo '<div class="controls">';
        echo '<form method="report.php">';
        echo '<p>'.get_string('displayoptions', 'quiz').': ';
        echo '<input type="hidden" name="id" value="'.$cm->id.'" />';
        echo '<input type="hidden" name="noattempts" value="0" />';
        echo '<input type="hidden" name="teacherattempts" value="0" />';
        echo '<input type="hidden" name="detailedmarks" value="0" />';
        echo '<input type="checkbox" id="checknoattempts" name="noattempts" '.($noattempts?'checked="checked" ':'').'value="1" /> <label for="checknoattempts">'.get_string('shownoattempts', 'quiz').'</label> ';
        echo '<input type="checkbox" id="checkteacherattempts" name="teacherattempts" '.($teacherattempts?'checked="checked" ':'').'value="1" /> <label for="checkteacherattempts">'.get_string('showteacherattempts', 'quiz').'</label> ';
        echo '<input type="checkbox" id="checkdetailedmarks" name="detailedmarks" '.($detailedmarks?'checked="checked" ':'').'value="1" /> <label for="checkdetailedmarks">'.get_string('showdetailedmarks', 'quiz').'</label> ';
        echo '<input type="submit" value="'.get_string('go').'" />';
        echo '</p>';
        echo '</form>';
        echo '</div>';

        return true;
    }

    function quiz_get_user_attempts_list($quiz, $attempts, $bestgrade, $timeformat) {
    /// Returns a little list of all attempts, one per line, 
    /// with each grade linked to the feedback report and with the best grade highlighted
    /// Each also has information about date and lapsed time
    
        $bestgrade = format_float($bestgrade,$quiz->decimalpoints);
    
        foreach ($attempts as $attempt) {
            $attemptgrade = format_float(($attempt->sumgrades / $quiz->sumgrades) * $quiz->grade,$quiz->decimalpoints);
            $attemptdate = userdate($attempt->timestart, $timeformat);
            if ($attempt->timefinish) {
                $attemptlapse = format_time($attempt->timefinish - $attempt->timestart);
            } else {
                $attemptlapse = "...";
            }
            $button = "<input type=\"checkbox\" name=\"box$attempt->id\" value=\"$attempt->id\" alt=\"box$attempt->id\" />";
            $revurl = "review.php?q=$quiz->id&amp;attempt=$attempt->id";
            if ($attemptgrade == $bestgrade) {
                $userattempts[] = "$button&nbsp;<span class=\"highlight\">$attemptgrade</span>&nbsp;<a href=\"$revurl\">$attemptdate</a>&nbsp;($attemptlapse)";
            } else {
                $userattempts[] = "$button&nbsp;$attemptgrade&nbsp;<a href=\"$revurl\">$attemptdate</a>&nbsp;($attemptlapse)";
            }
        }
        return implode("<br />\n", $userattempts);
    }
    
}

?>
