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

    $strreport = get_string("report", "survey");
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

    if (!$action) {
        $display = "summary";
    }

    if ($display)  { // Display the frame containing something.
        add_to_log($course->id, "survey", "view report", "report.php?id=$cm->id", "$survey->id");
        echo "<HEAD><TITLE>$course->shortname: $strreport: $survey->name</TITLE>\n";
        echo "<FRAMESET ROWS=70,* BORDER=1> ";
        echo "  <FRAME NAME=reporttop SRC=\"report.php?action=top&id=$id\"> \n";
        echo "  <FRAMESET COLS=150,* BORDER=1> ";
        echo "    <FRAME NAME=reportmenu SRC=\"report.php?action=menu&id=$id\"> \n";
        echo "    <FRAME NAME=reportmain SRC=\"report.php?action=$display&id=$id\"> \n";
        echo "  </FRAMESET>\n";
        echo "</FRAMESET>\n";
        exit;
    }

    switch ($action) {
      case "top":
        if ($course->category) {
            $navigation = "<A TARGET=_top HREF=\"../../course/view.php?id=$course->id\">$course->shortname</A> ->
                           <A TARGET=_top HREF=\"index.php?id=$course->id\">$strsurveys</A> ->
                           <A TARGET=_top HREF=\"view.php?id=$cm->id\">$survey->name</A> -> ";
        } else {
            $navigation = "<A TARGET=_top HREF=\"index.php?id=$course->id\">$strsurveys</A> ->
                           <A TARGET=_top HREF=\"view.php?id=$cm->id\">$survey->name</A> -> ";
        }
        print_header("$course->shortname: $survey->name", "$course->fullname", "$navigation $strreport");
        break;

      case "menu":
        print_header();
        echo "<P><FONT SIZE=2><A TARGET=reportmain HREF=\"report.php?action=summary&id=$id\">$strsummary</A></FONT></P>";
        echo "<P><FONT SIZE=2><A TARGET=reportmain HREF=\"report.php?action=scales&id=$id\">$strscales</A></FONT></P>";
        echo "<P><FONT SIZE=2><A TARGET=reportmain HREF=\"report.php?action=questions&id=$id\">$strquestions</A></FONT></P>";
        echo "<P><FONT SIZE=2><A TARGET=reportmain HREF=\"report.php?action=students&id=$id\">$course->student:</A></FONT></P>";
        if ($users = survey_get_responses($survey->id)) {
            foreach ($users as $user) {
                echo "<LI><FONT SIZE=1>";
                echo "<A TARGET=reportmain HREF=\"report.php?action=student&student=$user->id&id=$id\">";
                echo "$user->firstname $user->lastname";
                echo "</A></FONT></LI>";
            }
        }
        echo "<P><FONT SIZE=2><A TARGET=reportmain HREF=\"report.php?action=download&id=$id\">$strdownload</A></FONT></P>";
        break;

      case "summary":
        print_header("$survey->name: $strsummary", "$strsummary - $strallscales");

        if (survey_count_responses($survey->id)) {
            echo "<P ALIGN=CENTER><A HREF=\"report.php?action=scales&id=$id\"><IMG HEIGHT=$SURVEY_GHEIGHT WIDTH=$SURVEY_GWIDTH tail\" BORDER=1 SRC=\"graph.php?id=$id&type=overall.png\"></A>";
        } else {
            echo "<P ALIGN=CENTER>".get_string("nobodyyet","survey")."</P>";
        }
        print_footer($course);
        break;

      case "scales":
        print_header("$survey->name: $strscales", "$strallscales");

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
                echo "<P ALIGN=center><A TITLE=\"$strseemoredetail\" HREF=report.php?action=questions&id=$id&qid=$question->multi>";
                echo "<IMG HEIGHT=$SURVEY_GHEIGHT WIDTH=$SURVEY_GWIDTH BORDER=1
                       SRC=\"graph.php?id=$id&qid=$question->id&type=multiquestion.png\">";
                echo "</A></P><BR>";
            } 
        }

        print_footer($course);
        break;

      case "questions":

        if ($qid) {     // just get one multi-question
            $questions = get_records_sql("SELECT * FROM survey_questions WHERE id in ($qid)");
            $questionorder = explode(",", $qid);

            if ($scale = get_records("survey_questions", "multi", "$qid")) {
                $scale = array_pop($scale);
                print_header("$survey->name: $strquestions", "$scale->text - $strselectedquestions");
            } else {
                print_header("$survey->name: $strquestions", "$strselectedquestions");
            }

        } else {        // get all top-level questions
            $questions = get_records_sql("SELECT * FROM survey_questions WHERE id in ($survey->questions)");
            $questionorder = explode(",", $survey->questions);

            print_header("$survey->name: $strquestions", "$strallquestions");
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
                        echo "<P ALIGN=CENTER><A TITLE=\"$strseemoredetail\" HREF=\"report.php?action=question&id=$id&qid=$subquestion->id\">
                              <IMG HEIGHT=$SURVEY_GHEIGHT WIDTH=$SURVEY_GWIDTH  
                                   BORDER=1 SRC=\"graph.php?id=$id&qid=$subquestion->id&type=question.png\"></A></P>";
                    }
                }
            } else if ($question->type > 0 ) {
                echo "<P ALIGN=CENTER><A TITLE=\"$strseemoredetail\" HREF=\"report.php?action=question&id=$id&qid=$question->id\">
                      <IMG HEIGHT=$SURVEY_GHEIGHT WIDTH=$SURVEY_GWIDTH 
                           BORDER=1 SRC=\"graph.php?id=$id&qid=$question->id&type=question.png\"></A></P>";
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

        $answers =  explode(",", get_string($question->options, "survey"));

        print_header("$survey->name: $strquestion", "$strquestion: $question->text");

        $aaa = get_records_sql("SELECT sa.*,u.firstname,u.lastname,u.picture FROM survey_answers sa, user u WHERE sa.survey = '$survey->id' AND sa.question = $question->id AND u.id = sa.user ORDER by sa.answer1,sa.answer2 ASC");

        $strname = get_string("name", "survey");
        $strtime = get_string("time", "survey");
        $stractual = get_string("actual", "survey");
        $strpreferred = get_string("preferred", "survey");

        echo "<TABLE ALIGN=center CELLPADDING=0 CELLSPACING=10><TR><TD>&nbsp;<TH align=left>$strname<TH align=left>$strtime<TH align=left>$stractual<TH align=left>$strpreferred</TR>";
        foreach ($aaa as $a) {
            echo "<TR>";
            echo "<TD WIDTH=35>";
            print_user_picture($a->user, $course->id, $a->picture, false);
            echo "</TD>";
            echo "<TD><P><A HREF=\"report.php?id=$id&action=student&student=$a->user\">$a->firstname $a->lastname</A></TD>";
            echo "<TD><P>".userdate($a->time, "%d %B %Y, %I:%M %p")."</TD>";
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

         print_header("$survey->name: $course->student", get_string("analysisof", "survey", "$course->student"));
        
         if (! $results = survey_get_responses($survey->id) ) {
             notify(get_string("nobodyyet","survey"));
         } else {
             survey_print_all_responses($cm->id, $results);
         }

        print_footer($course);
        break;

      case "student":
         if (!$user = get_record("user", "id", $student)) {
             error("Student doesn't exist");
         }

         print_header("$survey->name: $$user->firstname $user->lastname", 
                       get_string("analysisof", "survey", "$user->firstname $user->lastname"));

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

         print_heading("$user->firstname $user->lastname");

         echo "<P ALIGN=CENTER>";
         print_user_picture($user->id, $course->id, $user->picture, true);
         echo "</P>";

         // Print overall summary
         echo "<P ALIGN=CENTER><IMG HEIGHT=$SURVEY_GHEIGHT WIDTH=$SURVEY_GWIDTH ALIGN=CENTER SRC=\"graph.php?id=$id&sid=$student&type=student.png\"></P>";
         
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
                 echo "<P ALIGN=center><A TITLE=\"$strseemoredetail\" HREF=report.php?action=questions&id=$id&qid=$question->multi>";
                 echo "<IMG HEIGHT=$SURVEY_GHEIGHT WIDTH=$SURVEY_GWIDTH BORDER=1
                        SRC=\"graph.php?id=$id&qid=$question->id&sid=$student&type=studentmultiquestion.png\">";
                 echo "</A></P><BR>";
             } 
         }

         if ($rs = survey_get_analysis($survey->id, $user->id)) {
            $notes = $rs->notes;
         } else {
            $notes = "";
         }
         echo "<HR NOSHADE SIZE=1>";
         echo "<CENTER>";
         echo "<FORM ACTION=report.php METHOD=post NAME=form>";
         echo "<H3>$strnotes:</H3>";
         echo "<BLOCKQUOTE>";
         echo "<TEXTAREA NAME=notes ROWS=10 COLS=60>";
         p($notes);
         echo "</TEXTAREA><BR>";
         echo "<INPUT TYPE=hidden NAME=action VALUE=student>";
         echo "<INPUT TYPE=hidden NAME=student VALUE=$student>";
         echo "<INPUT TYPE=hidden NAME=id VALUE=$cm->id>";
         echo "<INPUT TYPE=submit VALUE=\"".get_string("savechanges")."\">";
         echo "</BLOCKQUOTE>";
         echo "</FORM>";
         echo "</CENTER>";
 

         print_footer($course);
         break;

      case "download":
        print_header("$survey->name: $strdownload", "$strdownload");

        $strdownloadinfo = get_string("downloadinfo", "survey");
        $strdownloadexcel = get_string("downloadexcel", "survey");
        $strdownloadtext = get_string("downloadtext", "survey");

        echo "<P>$strdownloadinfo</P>";

        echo "<H2 ALIGN=CENTER><A HREF=\"download.php?id=$id&type=xls\">$strdownloadexcel</A></H2>";
        echo "<H2 ALIGN=CENTER><A HREF=\"download.php?id=$id&type=text\">$strdownloadtext</A></H2>";

        print_footer($course);
        break;

    }
?>
