<?PHP // $Id$

    include("../../config.php");
    include("lib.php");

    require_variable($id);    // Course Module ID

    if (! $cm = get_record("course_modules", "id", $id)) {
        error("Course Module ID was incorrect");
    }

    if (! $course = get_record("course", "id", $cm->course)) {
        error("Course is misconfigured");
    }

    require_login($course->id);

    if (! $survey = get_record("survey", "id", $cm->instance)) {
        error("Survey ID was incorrect");
    }

    add_to_log("View survey $survey->name", $course->id);

    print_header("$course->shortname: $survey->name", "$course->fullname",
                 "<A HREF=../../course/view.php?id=$course->id>$course->shortname</A> ->
                  <A HREF=index.php?id=$course->id>Surveys</A> -> $survey->name", "");


    if ($USER->editing) {
        print_update_module_icon($cm->id);
    }

    if (isteacher($course->id)) {
        echo "<P align=right><A HREF=\"report.php?id=$cm->id\">View all responses</A></P>";
    }


//  Check the survey hasn't already been filled out.

    if (survey_already_done($survey->id, $USER->id)) {
        print_heading("You've completed this survey.  The graph below shows a summary of your results compared to the class averages.");
        echo "<CENTER>";
        echo "<IMG SRC=\"$CFG->wwwroot/mod/survey/graph.php?id=$cm->id&sid=$USER->id&type=student.png\">";
        echo "</CENTER>";
        print_footer($course);
        exit;
    }

//  Start the survey form

    echo "<FORM NAME=form METHOD=post ACTION=save.php>";
    echo "<INPUT TYPE=hidden NAME=id VALUE=$id>";

    print_simple_box(text_to_html($survey->intro), "center", "80%");

// Get all the major questions and their proper order
    if (! $questions = get_records_sql("SELECT * FROM survey_questions WHERE id in ($survey->questions)")) {
        error("Couldn't find any questions in this survey!!");
    }
    $questionorder = explode( ",", $survey->questions);

// Cycle through all the questions in order and print them

    $qnum = 0;
    foreach ($questionorder as $key => $val) {
        $question = $questions["$val"];
        $question->id = $val;
        
        if ($question->type > 0) {
            if ($question->multi) {
                print_multi($question);
            } else {
                print_single($question);
            }
        }
    }


// End the survey page
   echo "<CENTER><P>&nbsp;</P><P>";
   if ($ownerpreview) {
       echo "(Because this is only a preview, the button below will not send data)<BR>\n";
       echo "<FONT SIZE=+1><INPUT TYPE=submit VALUE=\"Click here to go back\"></FONT>";
   } else {
       echo "\n";
?>

<SCRIPT>
<!-- // BEGIN
function checkform() {

    var error=false;

    with (document.form) {
    <? foreach ($checklist as $question => $default) {
           echo "  if (".$question."[".$default."].checked) error=true;\n";
    }?>
    }

    if (error) {
        alert("Some of the multiple choice questions have not been answered.");
    } else {
        document.form.submit();
    }
}

document.write('<INPUT TYPE="button" VALUE="Click here to check and continue" onClick="checkform()">');

// END -->
</SCRIPT>

<NOSCRIPT>
    <!-- Without Javascript, no checking is done -->
    <INPUT TYPE="submit" VALUE="Click here to continue">
</NOSCRIPT>
<?

   }
   echo "</FORM>";

   print_footer($course);

   exit;


//////////////////////////////////////////////////////////////////////////////////////


function print_multi($question) {
    GLOBAL $db, $qnum, $checklist, $THEME;


    echo "<P>&nbsp</P>\n";
	echo "<P><FONT SIZE=4><B>$question->text</B></FONT></P>";

	echo "<TABLE ALIGN=CENTER WIDTH=90% CELLPADDING=4 CELLSPACING=1 BORDER=0>";

    $options = explode( ",", $question->options);
    $numoptions = count($options);

    $oneanswer = ($question->type == 1 || $question->type == 2) ? true : false;
	if ($question->type == 2) {
		$P = "P";
	} else {
		$P = "";
	}
   
    if ($oneanswer) { 
        echo "<TR WIDTH=100% ><TD COLSPAN=2><P>$question->intro</P></TD>";
    } else {
        echo "<TR WIDTH=100% ><TD COLSPAN=3><P>$question->intro</P></TD>"; 
    }

    while (list ($key, $val) = each ($options)) {
        echo "<TD width=10% ALIGN=CENTER><FONT SIZE=1><P>$val</P></FONT></TD>\n";
    }
    echo "<TD ALIGN=CENTER BGCOLOR=\"$THEME->body\">&nbsp</TD></TR>\n";

    $subquestions = get_records_sql("SELECT * FROM survey_questions WHERE id in ($question->multi) ");

    foreach ($subquestions as $q) {
        $qnum++;
        $bgcolor = question_color($qnum);

        echo "<TR BGCOLOR=$bgcolor>";
        if ($oneanswer) {
            echo "<TD WIDTH=10 VALIGN=top><P><B>$qnum</B></P></TD>";
            echo "<TD VALIGN=top><P>$q->text</P></TD>";
            for ($i=1;$i<=$numoptions;$i++) {
                echo "<TD WIDTH=10% ALIGN=CENTER><INPUT TYPE=radio NAME=q$P$q->id VALUE=$i></TD>";
            }
            echo "<TD BGCOLOR=white><INPUT TYPE=radio NAME=q$P$q->id VALUE=0 checked></TD>";
            $checklist["q$P$q->id"] = $numoptions;
        
        } else {
            echo "<TD WIDTH=10 VALIGN=middle rowspan=2><P><B>$qnum</B></P></TD>";
            echo "<TD WIDTH=10% NOWRAP><P><FONT SIZE=1>I prefer that&nbsp;</FONT></P></TD>";
            echo "<TD WIDTH=40% VALIGN=middle rowspan=2><P>$q->text</P></TD>";
            for ($i=1;$i<=$numoptions;$i++) {
                echo "<TD WIDTH=10% ALIGN=CENTER><INPUT TYPE=radio NAME=qP$q->id VALUE=$i></TD>";
            }
            echo "<TD BGCOLOR=\"$THEME->body\"><INPUT TYPE=radio NAME=qP$q->id VALUE=0 checked></TD>";
            echo "</TR>";

            echo "<TR BGCOLOR=$bgcolor>";
            echo "<TD WIDTH=10% NOWRAP><P><FONT SIZE=1>I found that&nbsp;</P></TD>";
            for ($i=1;$i<=$numoptions;$i++) {
                echo "<TD WIDTH=10% ALIGN=CENTER><INPUT TYPE=radio NAME=q$q->id VALUE=$i></TD>";
            }
            echo "<TD WIDTH=5% BGCOLOR=\"$THEME->body\"><INPUT TYPE=radio NAME=q$q->id VALUE=0 checked></TD>";
            $checklist["qP$q->id"] = $numoptions;
            $checklist["q$q->id"] = $numoptions;
        }
        echo "</TR>\n";
    }
    echo "</TABLE>";
}



function print_single($question) {
    GLOBAL $db, $qnum;

    $bgcolor = question_color(0);

    $qnum++;

    echo "<P>&nbsp</P>\n";
    echo "<TABLE ALIGN=CENTER WIDTH=90% CELLPADDING=4 CELLSPACING=0>\n";
    echo "<TR BGCOLOR=$bgcolor>";
    echo "<TD VALIGN=top><B>$qnum</B></TD>";
    echo "<TD WIDTH=50% VALIGN=top><P>$question->text</P></TD>\n";
    echo "<TD WIDTH=50% VALIGN=top><P><FONT SIZE=+1>\n";


    if ($question->type == 0) {           // Plain text field
        echo "<TEXTAREA ROWS=3 COLS=30 WRAP=virtual NAME=\"$question->id\">$question->options</TEXTAREA>";

    } else if ($question->type > 0) {     // Choose one of a number
        echo "<SELECT NAME=$question->id>";
        echo "<OPTION VALUE=0 SELECTED>Choose...</OPTION>";
        $options = explode( ",", $question->options);
        foreach ($options as $key => $val) {
            $key++;
            echo "<OPTION VALUE=\"$key\">$val</OPTION>";
        }
        echo "</SELECT>";

    } else if ($question->type < 0) {     // Choose several of a number
        $options = explode( ",", $question->options);
        echo "<P>THIS TYPE OF QUESTION NOT SUPPORTED YET</P>";
    }

    echo "</FONT></TD></TR></TABLE>";

}

function question_color($qnum) {
    global $THEME;

    if ($qnum) {
        return $qnum % 2 ? $THEME->cellcontent : $THEME->cellcontent2;
        //return $qnum % 2 ? "#CCFFCC" : "#CCFFFF";
    } else {
        return $THEME->cellcontent;
    }
}

?>
