<?PHP // $Id$

    require_once("../../config.php");
    require_once("lib.php");

// Check that all the parameters have been provided.
 
    require_variable($id);           // Course Module ID
    optional_variable($action, "");  // What to look at
    optional_variable($qid, "0");    // Question id

    if (! $cm = get_record("course_modules", "id", $id)) {
        error("Course Module ID was incorrect");
    }

    if (! $course = get_record("course", "id", $cm->course)) {
        error("Course is misconfigured");
    }

    require_login($course->id);

    if (!isteacher($course->id)) {
        error("Sorry, only teachers can see this.");
    }

    if (! $survey = get_record("survey", "id", $cm->instance)) {
        error("Survey ID was incorrect");
    }

    if (! $template = get_record("survey", "id", $survey->template)) {
        error("Template ID was incorrect");
    }

    $showscales = ($template->name != 'ciqname');


    $strreport = get_string("report", "survey");
    $strsurvey = get_string("modulename", "survey");
    $strsurveys = get_string("modulenameplural", "survey");
    $strsummary = get_string("summary", "survey");
    $strscales = get_string("scales", "survey");
    $strquestion = get_string("question", "survey");
    $strquestions = get_string("questions", "survey");
    $strdownload = get_string("download", "survey");
    $strallscales = get_string("allscales", "survey");
    $strallquestions = get_string("allquestions", "survey");
    $strselectedquestions = get_string("selectedquestions", "survey");
    $strseemoredetail = get_string("seemoredetail", "survey");
    $strnotes = get_string("notes", "survey");

    add_to_log($course->id, "survey", "view report", "report.php?id=$cm->id", "$survey->id", $cm->id);

    if ($course->category) {
        $navigation = "<a href=\"../../course/view.php?id=$course->id\">$course->shortname</a> ->
                       <a href=\"index.php?id=$course->id\">$strsurveys</a> ->
                       <a href=\"view.php?id=$cm->id\">$survey->name</a> -> ";
    } else {
        $navigation = "<a href=\"index.php?id=$course->id\">$strsurveys</a> ->
                       <a href=\"view.php?id=$cm->id\">$survey->name</a> -> ";
    }

    print_header("$course->shortname: $survey->name", "$course->fullname", "$navigation $strreport",
                 "", "", true,
                 update_module_button($cm->id, $course->id, $strsurvey), navmenu($course, $cm));

/// Check to see if groups are being used in this survey
    if ($groupmode = groupmode($course, $cm)) {   // Groups are being used
        $menuaction = $action == "student" ? "students" : $action;
        $currentgroup = setup_and_print_groups($course, $groupmode, 
                                       "report.php?id=$cm->id&action=$menuaction&qid=$qid");
    } else {
        $currentgroup = 0;
    }

    if ($currentgroup) {
        $users = get_group_users($currentgroup);
    } else {
        $users = get_course_users($course->id);
    }

    print_simple_box_start("center");
    if ($showscales) {
        echo "<a href=\"report.php?action=summary&id=$id\">$strsummary</a>";
        echo "&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"report.php?action=scales&id=$id\">$strscales</a>";
        echo "&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"report.php?action=questions&id=$id\">$strquestions</a>";
        echo "&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"report.php?action=students&id=$id\">$course->students</a>";
        echo "&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"report.php?action=download&id=$id\">$strdownload</a>";
        if (empty($action)) {
            $action = "summary";
        }
    } else {
        echo "<a href=\"report.php?action=questions&id=$id\">$strquestions</a>";
        echo "&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"report.php?action=students&id=$id\">$course->students</a>";
        echo "&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"report.php?action=download&id=$id\">$strdownload</a>";
        if (empty($action)) {
            $action = "questions";
        }
    }
    print_simple_box_end();

    print_spacer(30,30);


/// Print the menu across the top

    switch ($action) {

      case "summary":
        print_heading($strsummary);

        if (survey_count_responses($survey->id, $currentgroup)) {
            echo "<p align=center><a href=\"report.php?action=scales&id=$id\">";
            survey_print_graph("id=$id&group=$currentgroup&type=overall.png");
            echo "</a>";
        } else {
            notify(get_string("nobodyyet","survey"));
        }
        break;

      case "scales":
        print_heading($strscales);

        if (! $results = survey_get_responses($survey->id, $currentgroup) ) {
            notify(get_string("nobodyyet","survey"));

        } else {

            $questions = get_records_list("survey_questions", "id", $survey->questions);
            $questionorder = explode(",", $survey->questions);

            foreach ($questionorder as $key => $val) {
                $question = $questions[$val];
                if ($question->type < 0) {  // We have some virtual scales.  Just show them.
                    $virtualscales = true;
                    break;
                }
            }

            foreach ($questionorder as $key => $val) {
                $question = $questions[$val];
                if ($question->multi) {
                    if ($virtualscales && $question->type > 0) {  // Don't show non-virtual scales if virtual
                        continue;
                    }
                    echo "<p align=center><a title=\"$strseemoredetail\" href=report.php?action=questions&id=$id&qid=$question->multi>";
                    survey_print_graph("id=$id&qid=$question->id&group=$currentgroup&type=multiquestion.png");
                    echo "</a></p><br>";
                } 
            }
        }

        break;

      case "questions":

        if ($qid) {     // just get one multi-question
            $questions = get_records_list("survey_questions", "id", $qid);
            $questionorder = explode(",", $qid);

            if ($scale = get_records("survey_questions", "multi", "$qid")) {
                $scale = array_pop($scale);
                print_heading("$scale->text - $strselectedquestions");
            } else {
                print_heading($strselectedquestions);
            }

        } else {        // get all top-level questions
            $questions = get_records_list("survey_questions", "id", $survey->questions);
            $questionorder = explode(",", $survey->questions);

            print_heading($strallquestions);
        }

        if (! $results = survey_get_responses($survey->id, $currentgroup) ) {
            notify(get_string("nobodyyet","survey"));

        } else {

            foreach ($questionorder as $key => $val) {
                $question = $questions[$val];
                if ($question->type < 0) {  // We have some virtual scales.  DON'T show them.
                    $virtualscales = true;
                    break;
                }
            }

            foreach ($questionorder as $key => $val) {
                $question = $questions[$val];

                if ($question->type < 0) {  // We have some virtual scales.  DON'T show them.
                    continue;
                }
                $question->text = get_string($question->text, "survey");

                if ($question->multi) {
                    echo "<h3>$question->text:</h3>";

                    $subquestions = get_records_list("survey_questions", "id", $question->multi);
                    $subquestionorder = explode(",", $question->multi);
                    foreach ($subquestionorder as $key => $val) {
                        $subquestion = $subquestions[$val];
                        if ($subquestion->type > 0) {
                            echo "<p align=center>";
                            echo "<a title=\"$strseemoredetail\" href=\"report.php?action=question&id=$id&qid=$subquestion->id\">";
                            survey_print_graph("id=$id&qid=$subquestion->id&group=$currentgroup&type=question.png");
                            echo "</a></p>";
                        }
                    }
                } else if ($question->type > 0 ) {
                    echo "<p align=center>";
                    echo "<a title=\"$strseemoredetail\" href=\"report.php?action=question&id=$id&qid=$question->id\">";
                    survey_print_graph("id=$id&qid=$question->id&group=$currentgroup&type=question.png");
                    echo "</a></p>";

                } else {
                    $table = NULL;
                    $table->head = array($question->text);
                    $table->align = array ("left");

                    $contents = '<table cellpadding="15" width="100%">';

                    if ($aaa = survey_get_user_answers($survey->id, $question->id, $currentgroup, "sa.time ASC")) {
                        foreach ($aaa as $a) {
                            $contents .= "<tr>";
                            $contents .= '<td nowrap="nowrap" width="10%" valign="top">'.fullname($a).'</td>';
                            $contents .= '<td valign="top">'.$a->answer1.'</td>';
                            $contents .= "</tr>";
                        }
                    }
                    $contents .= "</table>";

                    $table->data[] = array($contents);

                    print_table($table);
                    print_spacer(30);
                }
            }
        }

        break;

      case "question":
        if (!$question = get_record("survey_questions", "id", $qid)) {
            error("Question doesn't exist");
        }
        $question->text = get_string($question->text, "survey");

        $answers =  explode(",", get_string($question->options, "survey"));

        print_heading("$strquestion: $question->text");


        $strname = get_string("name", "survey");
        $strtime = get_string("time", "survey");
        $stractual = get_string("actual", "survey");
        $strpreferred = get_string("preferred", "survey");
        $strdateformat = get_string("strftimedatetime");

        $table = NULL;
        $table->head = array("", $strname, $strtime, $stractual, $strpreferred);
        $table->align = array ("left", "left", "left", "left", "right");
        $table->size = array (35, "", "", "", "");

        if ($aaa = survey_get_user_answers($survey->id, $question->id, $currentgroup)) {
            foreach ($aaa as $a) {
                if ($a->answer1) {
                    $answer1 =  "$a->answer1 - ".$answers[$a->answer1 - 1];
                } else {
                    $answer1 =  "&nbsp;";
                }
                if ($a->answer2) {
                    $answer2 = "$a->answer2 - ".$answers[$a->answer2 - 1];
                } else {
                    $answer2 = "&nbsp;";
                }

                $table->data[] = array(
                       print_user_picture($a->userid, $course->id, $a->picture, false, true, true),
                       "<a href=\"report.php?id=$id&action=student&student=$a->userid\">".fullname($a)."</a>",
                       userdate($a->time), 
                       $answer1, $answer2);
    
            }
        }

        print_table($table);

        break;

      case "students":

         print_heading(get_string("analysisof", "survey", "$course->students"));
        
         if (! $results = survey_get_responses($survey->id, $currentgroup) ) {
             notify(get_string("nobodyyet","survey"));
         } else {
             survey_print_all_responses($cm->id, $results, $course->id);
         }

        break;

      case "student":
         if (!$user = get_record("user", "id", $student)) {
             error("Student doesn't exist");
         }

         print_heading(get_string("analysisof", "survey", fullname($user)));

         if (isset($notes)) {
             if (survey_get_analysis($survey->id, $user->id)) {
                 if (! survey_update_analysis($survey->id, $user->id, $notes)) {
                     notify("An error occurred while saving your notes.  Sorry.");
                 } else {
                     notify(get_string("savednotes", "survey"));
                 }
             } else {
                 if (! survey_add_analysis($survey->id, $user->id, $notes)) {
                     notify("An error occurred while saving your notes.  Sorry.");
                 } else {
                     notify(get_string("savednotes", "survey"));
                 }
             }
         }

         echo "<p align=center>";
         print_user_picture($user->id, $course->id, $user->picture, true);
         echo "</p>";

         $questions = get_records_list("survey_questions", "id", $survey->questions);
         $questionorder = explode(",", $survey->questions);

         if ($showscales) {
             // Print overall summary
             echo "<p align=center>";
             survey_print_graph("id=$id&sid=$student&type=student.png");
             echo "</p>";
         
             // Print scales
     
             foreach ($questionorder as $key => $val) {
                 $question = $questions[$val];
                 if ($question->type < 0) {  // We have some virtual scales.  Just show them.
                     $virtualscales = true;
                     break;
                 }
             }
     
             foreach ($questionorder as $key => $val) {
                 $question = $questions[$val];
                 if ($question->multi) {
                     if ($virtualscales && $question->type > 0) {  // Don't show non-virtual scales if virtual
                         continue;
                     }
                     echo "<p align=center>";
                     echo "<a title=\"$strseemoredetail\" href=report.php?action=questions&id=$id&qid=$question->multi>";
                     survey_print_graph("id=$id&qid=$question->id&sid=$student&type=studentmultiquestion.png");
                     echo "</a></p><br>";
                 } 
             }
         }        

         // Print non-scale questions

         foreach ($questionorder as $key => $val) {
             $question = $questions[$val];
             if ($question->type == 0 or $question->type == 1) {
                 if ($answer = survey_get_user_answer($survey->id, $question->id, $user->id)) {
                     $table = NULL;
                     $table->head = array(get_string($question->text, "survey"));
                     $table->align = array ("left");
                     $table->data[] = array("$answer->answer1");
                     print_table($table);
                     print_spacer(30);
                 }
             }
         }

         if ($rs = survey_get_analysis($survey->id, $user->id)) {
            $notes = $rs->notes;
         } else {
            $notes = "";
         }
         echo "<hr noshade size=1>";
         echo "<center>";
         echo "<form action=report.php method=post name=form>";
         echo "<h3>$strnotes:</h3>";
         echo "<blockquote>";
         echo "<textarea name=notes rows=10 cols=60>";
         p($notes);
         echo "</textarea><br>";
         echo "<input type=hidden name=action value=student>";
         echo "<input type=hidden name=student value=$student>";
         echo "<input type=hidden name=id value=$cm->id>";
         echo "<input type=submit value=\"".get_string("savechanges")."\">";
         echo "</blockquote>";
         echo "</form>";
         echo "</center>";
 

         break;

      case "download":
        print_heading($strdownload);

        echo '<p align="center">'.get_string("downloadinfo", "survey").'</p>';

        echo '<center>';
        $options["id"] = "$cm->id";
        $options["type"] = "xls";
        $options["group"] = $currentgroup;
        print_single_button("download.php", $options, get_string("downloadexcel", "survey"));

        $options["type"] = "txt";
        print_single_button("download.php", $options, get_string("downloadtext", "survey"));
        echo '</center>';
    
        break;

    }
    print_footer($course);
?>
