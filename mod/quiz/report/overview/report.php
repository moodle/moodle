<?php  // $Id$

/// Overview report just displays a big table of all the attempts

class quiz_report extends quiz_default_report {

    function display($quiz, $cm, $course) {     /// This function just displays the report

        global $CFG, $QUIZ_GRADE_METHOD, $del;

        $strreallydel = addslashes(get_string('deleteattemptcheck','quiz'));
        $strdeleteselected = get_string('deleteselected');
        $strdeleteall = get_string('deleteall');
        $strname  = get_string("name");
        $strattempts  = get_string("attempts", "quiz");
        $strnoattempts = get_string('noattempts','quiz');
        $strbestgrade  = $QUIZ_GRADE_METHOD[$quiz->grademethod];
        $strtimeformat = get_string('strftimedatetime');
    

        if (!empty($del)) {   /// Some attempts need to be deleted

            if (record_exists('quiz_attempts', 'quiz', $quiz->id)) {

                if ($del == 'all'){     /// Delete all the attempts
                    $attempts = get_records('quiz_attempts','quiz',$quiz->id);
                    delete_records('quiz_attempts','quiz',$quiz->id);
                    delete_records('quiz_grades','quiz',$quiz->id);
                    if ($attempts) {
                        foreach ($attempts as $thisattempt){
                            delete_records('quiz_responses','attempt',$thisattempt->id);
                        }
                    }

                } else {                /// Delete selected attempts

                    $items = (array)data_submitted();

                    unset($items['del']);
                    unset($items['id']);

                    if ($items) {
                        foreach ($items as $attemptid) {
                            if ($todelete = get_record('quiz_attempts', 'id', $attemptid)) {
                                delete_records('quiz_attempts', 'id', $attemptid);
                                delete_records('quiz_responses', 'attempt', $attemptid);

                                // Search quiz_attempts for other instances by this user.  
                                // If none, then delete record for this quiz, this user from quiz_grades 
                                // else recalculate best grade

                                $userid = $todelete->userid;
                                if (!record_exists('quiz_attempts', 'userid', $userid, 'quiz',$quiz->id)) {
                                    delete_records('quiz_grades', 'userid', $userid,'quiz',$quiz->id);
                                } else {
                                    quiz_save_best_grade($quiz, $userid);
                                }
                            }
                        }
                    }
                }
            }
        }

        if (!$grades = quiz_get_grade_records($quiz)) {
            print_heading($strnoattempts);
            return true;
        }

    /// Check to see if groups are being used in this quiz
        if ($groupmode = groupmode($course, $cm)) {   // Groups are being used
            $currentgroup = setup_and_print_groups($course, $groupmode, "report.php?id=$cm->id&mode=overview");
        } else {
            $currentgroup = false;
        }

    /// Get all teachers and students
        if ($currentgroup) {
            $users = get_group_users($currentgroup);
        }


        $table->head = array("&nbsp;", $strname, $strattempts, "$strbestgrade /$quiz->grade");
        $table->align = array("center", "left", "left", "center");
        $table->wrap = array("nowrap", "nowrap", "nowrap", "nowrap");
        $table->width = 10;
        $table->size = array(10, "*", "80%", "*");

        foreach ($grades as $grade) {
            if ($currentgroup) {
                if (empty($users[$grade->userid])) {       /// Using groups, but this user not in group
                    continue;
                }
            }
            $picture = print_user_picture($grade->userid, $course->id, $grade->picture, false, true);

            if ($attempts = quiz_get_user_attempts($quiz->id, $grade->userid)) {
                $userattempts = $this->quiz_get_user_attempts_list($quiz, $attempts, $grade->grade, $strtimeformat);
            } else {
                $userattempts = "";
            }

            $table->data[] = array ($picture, 
                                    "<a href=\"$CFG->wwwroot/user/view.php?id=$grade->userid&course=$course->id\">".
                                    fullname($grade).'</a>', 
                                    "$userattempts", round($grade->grade,0));
        }

        //Embed script for warning
        echo "\n<script lang=javascript>\n<!--\nfunction delcheck(){\n ";
        echo "if (confirm('$strreallydel')) {\n";
        echo " document.delform.del.value='all';\n return true;\n";
        echo " } else {\n";
        echo " return false;\n }\n}\n";
        echo "//-->\n</script>\n";
            
        $onsub = "return confirm('$strreallydel')";

        echo "<form method=\"post\" action=\"report.php\" name=\"delform\" onsubmit=\"$onsub\">\n";
        echo "<input type=\"hidden\" name=\"del\" value=\"selection\">\n";
        echo "<input type=\"hidden\" name=\"id\" value=\"$cm->id\">\n";

        print_table($table);
    
        //There might be a more elegant way than using the <center> tag for this
        echo "<center><input type=\"submit\" value=\"$strdeleteselected\">&nbsp;";
        echo "<input type=button value=\"$strdeleteall\" onClick=\"if(delcheck()){document.delform.submit()}\">\n</center>\n";
        echo "</form>\n";

        return true;
    }

    function quiz_get_user_attempts_list($quiz, $attempts, $bestgrade, $timeformat) {
    /// Returns a little list of all attempts, one per line, 
    /// with each grade linked to the feedback report and with the best grade highlighted
    /// Each also has information about date and lapsed time
    
        $bestgrade = format_float($bestgrade);
    
        foreach ($attempts as $attempt) {
            $attemptgrade = format_float(($attempt->sumgrades / $quiz->sumgrades) * $quiz->grade);
            $attemptdate = userdate($attempt->timestart, $timeformat);
            if ($attempt->timefinish) {
                $attemptlapse = format_time($attempt->timefinish - $attempt->timestart);
            } else {
                $attemptlapse = "...";
            }
            $button = "<input type=checkbox name=\"box$attempt->id\" value=\"$attempt->id\">";
            $revurl = "review.php?q=$quiz->id&attempt=$attempt->id";
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
