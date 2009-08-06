<?php // $Id$

    require_once("../../config.php");
    require_once("lib.php");

// Check that all the parameters have been provided.

    $id      = required_param('id', PARAM_INT);           // Course Module ID
    $action  = optional_param('action', '', PARAM_ALPHA); // What to look at
    $qid     = optional_param('qid', 0, PARAM_RAW);       // Question IDs comma-separated list
    $student = optional_param('student', 0, PARAM_INT);   // Student ID
    $notes   = optional_param('notes', '', PARAM_RAW);    // Save teachers notes

    $qids = explode(',', $qid);
    $qids = clean_param($qids, PARAM_INT);
    $qid = implode (',', $qids);

    if (! $cm = get_coursemodule_from_id('survey', $id)) {
        print_error('invalidcoursemodule');
    }

    if (! $course = $DB->get_record("course", array("id"=>$cm->course))) {
        print_error('coursemisconf');
    }

    require_login($course->id, false, $cm);
    
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);

    require_capability('mod/survey:readresponses', $context);

    if (! $survey = $DB->get_record("survey", array("id"=>$cm->instance))) {
        print_error('invalidsurveyid', 'survey');
    }

    if (! $template = $DB->get_record("survey", array("id"=>$survey->template))) {
        print_error('invalidtmptid', 'survey');
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

    if ($course->id != SITEID) {
        $navigation = "<a href=\"../../course/view.php?id=$course->id\">$course->shortname</a> ->
                       <a href=\"index.php?id=$course->id\">$strsurveys</a> ->
                       <a href=\"view.php?id=$cm->id\">".format_string($survey->name,true)."</a> -> ";
    } else {
        $navigation = "<a href=\"index.php?id=$course->id\">$strsurveys</a> ->
                       <a href=\"view.php?id=$cm->id\">".format_string($survey->name,true)."</a> -> ";
    }
    
    $navigation = build_navigation($strreport, $cm);
    print_header("$course->shortname: ".format_string($survey->name), $course->fullname, $navigation,
                 "", "", true,
                 update_module_button($cm->id, $course->id, $strsurvey), navmenu($course, $cm));

/// Check to see if groups are being used in this survey
    if ($groupmode = groups_get_activity_groupmode($cm)) {   // Groups are being used
        $menuaction = $action == "student" ? "students" : $action;
        $currentgroup = groups_get_activity_group($cm, true);
        groups_print_activity_menu($cm, "report.php?id=$cm->id&amp;action=$menuaction&amp;qid=$qid");
    } else {
        $currentgroup = 0;
    }

    if ($currentgroup) {
        $users = get_users_by_capability($context, 'mod/survey:participate', '', '', '', '', $currentgroup, null, false);
    } else if (!empty($CFG->enablegroupings) && !empty($cm->groupingid)) { 
        $groups = groups_get_all_groups($courseid, 0, $cm->groupingid);
        $groups = array_keys($groups);
        $users = get_users_by_capability($context, 'mod/survey:participate', '', '', '', '', $groups, null, false);
    } else {
        $users = get_users_by_capability($context, 'mod/survey:participate', '', '', '', '', '', null, false);
        $group = false;
    }

    $groupingid = $cm->groupingid;
    
    print_simple_box_start("center");
    if ($showscales) {
        echo "<a href=\"report.php?action=summary&amp;id=$id\">$strsummary</a>";
        echo "&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"report.php?action=scales&amp;id=$id\">$strscales</a>";
        echo "&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"report.php?action=questions&amp;id=$id\">$strquestions</a>";
        echo "&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"report.php?action=students&amp;id=$id\">".get_string('participants')."</a>";
        if (has_capability('mod/survey:download', $context)) {
            echo "&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"report.php?action=download&amp;id=$id\">$strdownload</a>";
        }
        if (empty($action)) {
            $action = "summary";
        }
    } else {
        echo "<a href=\"report.php?action=questions&amp;id=$id\">$strquestions</a>";
        echo "&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"report.php?action=students&amp;id=$id\">".get_string('participants')."</a>";
        if (has_capability('mod/survey:download', $context)) {
            echo "&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"report.php?action=download&amp;id=$id\">$strdownload</a>";
        }
        if (empty($action)) {
            $action = "questions";
        }
    }
    print_simple_box_end();

    $spacer = new html_image();
    $spacer->height = 30;
    $spacer->width = 30;
    echo $OUTPUT->spacer($spacer) . '<br />';


/// Print the menu across the top

    $virtualscales = false;

    switch ($action) {

      case "summary":
        echo $OUTPUT->heading($strsummary);

        if (survey_count_responses($survey->id, $currentgroup, $groupingid)) {
            echo "<div class='reportsummary'><a href=\"report.php?action=scales&amp;id=$id\">";
            survey_print_graph("id=$id&amp;group=$currentgroup&amp;type=overall.png");
            echo "</a></div>";
        } else {
            notify(get_string("nobodyyet","survey"));
        }
        break;

      case "scales":
        echo $OUTPUT->heading($strscales);

        if (! $results = survey_get_responses($survey->id, $currentgroup, $groupingid) ) {
            notify(get_string("nobodyyet","survey"));

        } else {

            $questions = $DB->get_records_list("survey_questions", "id", explode(',', $survey->questions));
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
                    if (!empty($virtualscales) && $question->type > 0) {  // Don't show non-virtual scales if virtual
                        continue;
                    }
                    echo "<p class=\"centerpara\"><a title=\"$strseemoredetail\" href=\"report.php?action=questions&amp;id=$id&amp;qid=$question->multi\">";
                    survey_print_graph("id=$id&amp;qid=$question->id&amp;group=$currentgroup&amp;type=multiquestion.png");
                    echo "</a></p><br />";
                }
            }
        }

        break;

      case "questions":

        if ($qid) {     // just get one multi-question
            $questions = $DB->get_record("survey_questions", "id", $qid);
            $questionorder = explode(",", $qid);

            if ($scale = $DB->get_records("survey_questions", array("multi"=>$qid))) {
                $scale = array_pop($scale);
                echo $OUTPUT->heading("$scale->text - $strselectedquestions");
            } else {
                echo $OUTPUT->heading($strselectedquestions);
            }

        } else {        // get all top-level questions
            $questions = $DB->get_records_list("survey_questions", "id", explode(',',$survey->questions));
            $questionorder = explode(",", $survey->questions);

            echo $OUTPUT->heading($strallquestions);
        }

        if (! $results = survey_get_responses($survey->id, $currentgroup, $groupingid) ) {
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

                    $subquestions = $DB->get_records_list("survey_questions", "id", explode(',', $question->multi));
                    $subquestionorder = explode(",", $question->multi);
                    foreach ($subquestionorder as $key => $val) {
                        $subquestion = $subquestions[$val];
                        if ($subquestion->type > 0) {
                            echo "<p class=\"centerpara\">";
                            echo "<a title=\"$strseemoredetail\" href=\"report.php?action=question&amp;id=$id&amp;qid=$subquestion->id\">";
                            survey_print_graph("id=$id&amp;qid=$subquestion->id&amp;group=$currentgroup&amp;type=question.png");
                            echo "</a></p>";
                        }
                    }
                } else if ($question->type > 0 ) {
                    echo "<p class=\"centerpara\">";
                    echo "<a title=\"$strseemoredetail\" href=\"report.php?action=question&amp;id=$id&amp;qid=$question->id\">";
                    survey_print_graph("id=$id&amp;qid=$question->id&amp;group=$currentgroup&amp;type=question.png");
                    echo "</a></p>";

                } else {
                    $table = NULL;
                    $table->head = array($question->text);
                    $table->align = array ("left");

                    $contents = '<table cellpadding="15" width="100%">';

                    if ($aaa = survey_get_user_answers($survey->id, $question->id, $currentgroup, "sa.time ASC")) {
                        foreach ($aaa as $a) {
                            $contents .= "<tr>";
                            $contents .= '<td class="fullnamecell">'.fullname($a).'</td>';
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
        if (!$question = $DB->get_record("survey_questions", array("id"=>$qid))) {
            print_error('cannotfindquestion', 'survey');
        }
        $question->text = get_string($question->text, "survey");

        $answers =  explode(",", get_string($question->options, "survey"));

        echo $OUTPUT->heading("$strquestion: $question->text");


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
                       "<a href=\"report.php?id=$id&amp;action=student&amp;student=$a->userid\">".fullname($a)."</a>",
                       userdate($a->time),
                       $answer1, $answer2);

            }
        }

        print_table($table);

        break;

      case "students":

         echo $OUTPUT->heading(get_string("analysisof", "survey", get_string('participants')));

         if (! $results = survey_get_responses($survey->id, $currentgroup, $groupingid) ) {
             notify(get_string("nobodyyet","survey"));
         } else {
             survey_print_all_responses($cm->id, $results, $course->id);
         }

        break;

      case "student":
         if (!$user = $DB->get_record("user", array("id"=>$student))) {
             print_error('invaliduserid');
         }

         echo $OUTPUT->heading(get_string("analysisof", "survey", fullname($user)));

         if ($notes != '' and confirm_sesskey()) {
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

         echo "<p <p class=\"centerpara\">";
         print_user_picture($user->id, $course->id, $user->picture, true);
         echo "</p>";

         $questions = $DB->get_records_list("survey_questions", "id", explode(',', $survey->questions));
         $questionorder = explode(",", $survey->questions);

         if ($showscales) {
             // Print overall summary
             echo "<p <p class=\"centerpara\">>";
             survey_print_graph("id=$id&amp;sid=$student&amp;type=student.png");
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
                     echo "<p class=\"centerpara\">";
                     echo "<a title=\"$strseemoredetail\" href=\"report.php?action=questions&amp;id=$id&amp;qid=$question->multi\">";
                     survey_print_graph("id=$id&amp;qid=$question->id&amp;sid=$student&amp;type=studentmultiquestion.png");
                     echo "</a></p><br />";
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
                     $table->data[] = array(s($answer->answer1)); // no html here, just plain text
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
         echo "<hr noshade=\"noshade\" size=\"1\" />";
         echo "<div class='studentreport'>";
         echo "<form action=\"report.php\" method=\"post\">";
         echo "<h3>$strnotes:</h3>";
         echo "<blockquote>";
         echo "<textarea name=\"notes\" rows=\"10\" cols=\"60\">";
         p($notes);
         echo "</textarea><br />";
         echo "<input type=\"hidden\" name=\"action\" value=\"student\" />";
         echo "<input type=\"hidden\" name=\"sesskey\" value=\"".sesskey()."\" />";
         echo "<input type=\"hidden\" name=\"student\" value=\"$student\" />";
         echo "<input type=\"hidden\" name=\"id\" value=\"$cm->id\" />";
         echo "<input type=\"submit\" value=\"".get_string("savechanges")."\" />";
         echo "</blockquote>";
         echo "</form>";
         echo "</div>";


         break;

      case "download":
        echo $OUTPUT->heading($strdownload);

        require_capability('mod/survey:download', $context);

        echo '<p class="centerpara">'.get_string("downloadinfo", "survey").'</p>';

        echo '<div class="reportbuttons">';
        $optons = array();
        $options["id"] = "$cm->id";
        $options["group"] = $currentgroup;

        $options["type"] = "ods";
        print_single_button("download.php", $options, get_string("downloadods"));

        $options["type"] = "xls";
        print_single_button("download.php", $options, get_string("downloadexcel"));

        $options["type"] = "txt";
        print_single_button("download.php", $options, get_string("downloadtext"));
        echo '</div>';

        break;

    }
    print_footer($course);
?>
