<?PHP // $Id$

    include("../../config.php");
    include("lib.php");

// Check that all the parameters have been provided.
 
    require_variable($id);    // Course Module ID

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



    $ME = qualified_me()."?id=$id";

    if (!$action) {
        $display = "summary";
    }

    if ($display)  { // Display the frame containing something.

        add_to_log($course->id, "survey", "view report", "report.php?id=$cm->id", "$survey->id");
        echo "<HEAD><TITLE>Report: $survey->name</TITLE>\n";
        echo "<FRAMESET COLS=150,* BORDER=1> ";
        echo "  <FRAME NAME=reportmenu SRC=\"report.php?action=menu&id=$id\"> \n";
        echo "  <FRAME NAME=reportmain SRC=\"report.php?action=$display&id=$id\"> \n";
        echo "</FRAMESET>\n";
        exit;
    }

    switch ($action) {
      case "menu":
        print_header("Survey Report", "Survey Report");
        //echo "<FONT FACE=\"Verdana,Arial,Helvetica,sans-serif\">";
        //echo "<P><B>Survey Report</B></P>"; 
        echo "<P><FONT SIZE=2><A TARGET=reportmain HREF=\"report.php?action=summary&id=$id\">Summary</A></FONT></P>";
        echo "<P><FONT SIZE=2><A TARGET=reportmain HREF=\"report.php?action=scales&id=$id\">Scales</A></FONT></P>";
        echo "<P><FONT SIZE=2><A TARGET=reportmain HREF=\"report.php?action=questions&id=$id\">Questions</A></FONT></P>";
        echo "<P><FONT SIZE=2><A TARGET=reportmain HREF=\"report.php?action=students&id=$id\">Students</A></FONT></P>";
        if ($users = get_survey_responses($survey->id)) {
            foreach ($users as $user) {
                echo "<LI><FONT SIZE=1>";
                echo "<A TARGET=reportmain HREF=\"report.php?action=student&student=$user->id&id=$id\">";
                echo "$user->firstname $user->lastname";
                echo "</A></FONT></LI>";
            }
        }
        echo "<P><FONT SIZE=2><A TARGET=reportmain HREF=\"report.php?action=download&id=$id\">Download</A></FONT></P>";
        echo "<HR SIZE=1 NOSHADE>";
        echo "<P align=center><FONT SIZE=2><A TARGET=_top HREF=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</A></FONT></P>";
        break;

      case "summary":
        print_header("Overall Summary", "$survey->name: Overall Summary", "", "");

        print_heading("All scales, all students");

        echo "<P ALIGN=CENTER><A HREF=\"report.php?action=scales&id=$id\"><IMG HEIGHT=$GHEIGHT WIDTH=$GWIDTH ALT=\"Click here to see the scales in more detail\" BORDER=0 SRC=\"graph.php?id=$id&type=overall.png\"></A>";
        print_footer($course);
        break;

      case "scales":
        print_header("Scales", "$survey->name: Scales", "", "");

        print_heading("All scales, all students");

        $questions = get_records_sql("SELECT * FROM survey_questions WHERE id in ($survey->questions)");
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
                echo "<P ALIGN=center><A HREF=report.php?action=questions&id=$id&qid=$question->multi>";
                echo "<IMG HEIGHT=$GHEIGHT WIDTH=$GWIDTH ALT=\"Click here to see subquestions\" BORDER=0
                       SRC=\"graph.php?id=$id&qid=$question->id&type=multiquestion.png\">";
                echo "</A></P><BR>";
            } 
        }

        print_footer($course);
        break;

      case "questions":
        print_header("Analysis by Question", "$survey->name: Questions", "", "");

        if ($qid) {     // just get one multi-question
            $questions = get_records_sql("SELECT * FROM survey_questions WHERE id in ($qid)");
            $questionorder = explode(",", $qid);

            print_heading("Selected questions from a scale, all students");

        } else {        // get all top-level questions
            $questions = get_records_sql("SELECT * FROM survey_questions WHERE id in ($survey->questions)");
            $questionorder = explode(",", $survey->questions);

            print_heading("All questions in order, all students");
        }

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

            if ($question->multi) {
                echo "<H3>$question->text :</H3>";

                $subquestions = get_records_sql("SELECT * FROM survey_questions WHERE id in ($question->multi)");
                $subquestionorder = explode(",", $question->multi);
                foreach ($subquestionorder as $key => $val) {
                    $subquestion = $subquestions[$val];
                    if ($subquestion->type > 0) {
                        echo "<P ALIGN=CENTER><A HREF=\"report.php?action=question&id=$id&qid=$subquestion->id\">
                              <IMG HEIGHT=$GHEIGHT WIDTH=$GWIDTH ALT=\"Click here to see all responses\" 
                                   BORDER=0 SRC=\"graph.php?id=$id&qid=$subquestion->id&type=question.png\"></A></P>";
                    }
                }
            } else if ($question->type > 0 ) {
                echo "<P ALIGN=CENTER><A HREF=\"report.php?action=question&id=$id&qid=$question->id\">
                      <IMG HEIGHT=$GHEIGHT WIDTH=$GWIDTH ALT=\"Click here to see all responses\"
                           BORDER=0 SRC=\"graph.php?id=$id&qid=$question->id&type=question.png\"></A></P>";
            } else {
                echo "<H3>$question->text</H3>";
                if ($aaa = get_records_sql("SELECT sa.*, u.firstname,u.lastname FROM survey_answers sa, user u WHERE survey = '$survey->id' AND question = $question->id and sa.user = u.id")) {
                    echo "<UL>";
                    foreach ($aaa as $a) {
                        echo "<LI>$a->firstname $a->lastname: $a->answer1";
                    }
                    echo "</UL>";
                }
            }
        }

        print_footer($course);
        break;

      case "question":
        if (!$question = get_record("survey_questions", "id", $qid)) {
            error("Question doesn't exist");
        }

        $answers =  explode(",", $question->options);

        print_header("All answers for a particular question", "$survey->name: Question Answers", "", "");

        print_heading("$question->text");

        $aaa = get_records_sql("SELECT sa.*,u.firstname,u.lastname,u.picture FROM survey_answers sa, user u WHERE sa.survey = '$survey->id' AND sa.question = $question->id AND u.id = sa.user ORDER by sa.answer1,sa.answer2 ASC");

        echo "<TABLE ALIGN=center CELLPADDING=0 CELLSPACING=10><TR><TD>&nbsp;<TH align=left>Name<TH align=left>Time<TH align=left>Actual<TH align=left>Preferred</TR>";
        foreach ($aaa as $a) {
            echo "<TR>";
            echo "<TD WIDTH=35>";
            print_user_picture($a->user, $course->id, $a->picture, false);
            echo "</TD>";
            echo "<TD><P><A HREF=\"report.php?id=$id&action=student&student=$a->user\">$a->firstname $a->lastname</A></TD>";
            echo "<TD><P>".userdate($a->time, "j M Y h:i A")."</TD>";
            echo "<TD BGCOLOR=\"$THEME->cellcontent\"><P>";
            if ($a->answer1) {
                echo "$a->answer1 - ".$answers[$a->answer1 - 1];
            } else {
                echo "&nbsp;";
            }
            echo "</TD><TD BGCOLOR=\"$THEME->cellcontent\"><P>";
            if ($a->answer2) {
                echo "$a->answer2 - ".$answers[$a->answer2 - 1];
            } else {
                echo "&nbsp;";
            }
            echo "</TD></TR>";

        }
        echo "</TABLE>";


        print_footer($course);
        break;

      case "students":

         print_header("Analysis by Student", "$survey->name: Students", "", "");
        
         if (! $results = get_survey_responses($survey->id) ) {
             notify("There are no responses for this survey.");
         } else {
             print_all_responses($cm->id, $results);
         }

        print_footer($course);
        break;

      case "student":
         if (!$user = get_record("user", "id", $student)) {
             error("Student doesn't exist");
         }


         print_header("Analysis of $user->firstname $user->lastname", "$survey->name: Analysis of a student", "", "");
         if (isset($notes)) {
             if (record_exists_sql("SELECT * FROM survey_analysis WHERE survey='$survey->id' and user='$user->id'")) {
                 if (! update_survey_analysis($survey->id, $user->id, $notes)) {
                     notify("An error occurred while saving your notes.  Sorry.");
                 }
             } else {
                 if (!add_survey_analysis($survey->id, $user->id, $notes)) {
                     notify("An error occurred while saving your notes.  Sorry.");
                 }
             }
         }

         print_heading("$user->firstname $user->lastname");

         echo "<P ALIGN=CENTER>";
         print_user_picture($user->id, $course->id, $user->picture, true);
         echo "</P>";

         // Print overall summary
         echo "<P ALIGN=CENTER><IMG HEIGHT=$GHEIGHT WIDTH=$GWIDTH ALIGN=CENTER SRC=\"graph.php?id=$id&sid=$student&type=student.png\"></P>";
         
         // Print scales
         $questions = get_records_sql("SELECT * FROM survey_questions WHERE id in ($survey->questions)");
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
                 echo "<P ALIGN=center><A HREF=report.php?action=questions&id=$id&qid=$question->multi>";
                 echo "<IMG HEIGHT=$GHEIGHT WIDTH=$GWIDTH ALT=\"Click here to see subquestions\" BORDER=0
                        SRC=\"graph.php?id=$id&qid=$question->id&sid=$student&type=studentmultiquestion.png\">";
                 echo "</A></P><BR>";
             } 
         }

         if ($rs = get_record_sql("SELECT notes from survey_analysis WHERE survey='$survey->id' and user='$user->id'")) {
            $notes = $rs->notes;
         } else {
            $notes = "";
         }
         echo "<HR NOSHADE SIZE=1>";
         echo "<CENTER>";
         echo "<FORM ACTION=report.php METHOD=post NAME=form>";
         echo "<H3>Your private analysis/notes:</H3>";
         echo "<BLOCKQUOTE>";
         echo "<TEXTAREA NAME=notes ROWS=10 COLS=60>";
         p($notes);
         echo "</TEXTAREA><BR>";
         echo "<INPUT TYPE=hidden NAME=action VALUE=student>";
         echo "<INPUT TYPE=hidden NAME=student VALUE=$student>";
         echo "<INPUT TYPE=hidden NAME=id VALUE=$cm->id>";
         echo "<INPUT TYPE=submit VALUE=\"Save these notes\">";
         echo "</BLOCKQUOTE>";
         echo "</FORM>";
         echo "</CENTER>";
 

         print_footer($course);
         break;

      case "download":
        print_header("Download Data", "$survey->name: Download Data", "", "");

        echo "<P>You can download the complete raw data for this survey in a form suitable
                    for analysis in Excel, SPSS or other package.</P>";

        echo "<H2 ALIGN=CENTER><A HREF=\"download.php?id=$id&type=xls\">Download data as Excel spreadsheet</A></H2>";
        echo "<H2 ALIGN=CENTER><A HREF=\"download.php?id=$id&type=text\">Download data as a plain text file</A></H2>";

        print_footer($course);
        break;

    }

/// FUNCTIONS //////////////////////////////////////////////////////////////

function print_all_responses($survey, $results) {

    global $THEME;

    echo "<TABLE CELLPADDING=5 CELLSPACING=2 ALIGN=CENTER>";
    echo "<TR><TD>Name<TD>Time<TD>Answered</TR>";

    foreach ($results as $a) {
                 
        echo "<TR>";
        echo "<TD><A HREF=\"report.php?action=student&student=$a->id&id=$survey\">$a->firstname $a->lastname</A></TD>";
        echo "<TD>".userdate($a->time, "j M Y, h:i A")."</TD>";
        echo "<TD align=right>$a->numanswers</TD>";
        echo "</TR>";
    }
    echo "</TABLE>";
}

          
function get_survey_responses($survey) {
    return get_records_sql("SELECT a.time as time, count(*) as numanswers, u.*
                            FROM survey_answers AS a, user AS u
                            WHERE a.answer1 <> '0' AND a.answer2 <> '0'
                                  AND a.survey = $survey 
                                  AND a.user = u.id
                            GROUP BY a.user ORDER BY a.time ASC");
}
