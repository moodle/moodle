<?PHP // $Id$

// Graph size
$SURVEY_GHEIGHT = 500;
$SURVEY_GWIDTH  = 900;

$SURVEY_QTYPE = array (
        "-3" => "Virtual Actual and Preferred",
        "-2" => "Virtual Preferred",
        "-1" => "Virtual Actual",
         "0" => "Text",
         "1" => "Actual",
         "2" => "Preferred",
         "3" => "Actual and Preferred",
        );

// FUNCTIONS ////////////////////////////////////////////////////////

function survey_add_instance($survey) {
// Given an object containing all the necessary data, 
// (defined by the form in mod.html) this function 
// will create a new instance and return the id number 
// of the new instance.

    if (!$template = get_record("survey", "id", $survey->template)) {
        return 0;
    }

    $survey->questions    = $template->questions; 
    $survey->timecreated  = time();
    $survey->timemodified = $survey->timecreated;

    return insert_record("survey", $survey);

}


function survey_update_instance($survey) {
// Given an object containing all the necessary data, 
// (defined by the form in mod.html) this function 
// will update an existing instance with new data.

    if (!$template = get_record("survey", "id", $survey->template)) {
        return 0;
    }

    $survey->id           = $survey->instance; 
    $survey->questions    = $template->questions; 
    $survey->timemodified = time();

    return update_record("survey", $survey);
}

function survey_delete_instance($id) {
// Given an ID of an instance of this module, 
// this function will permanently delete the instance 
// and any data that depends on it.  

    if (! $survey = get_record("survey", "id", "$id")) {
        return false;
    }

    $result = true;

    if (! delete_records("survey_analysis", "survey", "$survey->id")) {
        $result = false;
    }

    if (! delete_records("survey_answers", "survey", "$survey->id")) {
        $result = false;
    }

    if (! delete_records("survey", "id", "$survey->id")) {
        $result = false;
    }

    return $result;
}

function survey_print_recent_activity(&$logs, $isteacher=false) {
    global $CFG, $COURSE_TEACHER_COLOR;

    $content = false;
    $surveys = NULL;

    foreach ($logs as $log) {
        if ($log->module == "survey" and $log->action == "submit") {
            $surveys[$log->id] = get_record_sql("SELECT s.name, u.firstname, u.lastname
                                                 FROM survey s, user u
                                                 WHERE s.id = '$log->info' AND u.id = '$log->user'");
            $surveys[$log->id]->time = $log->time;
            $surveys[$log->id]->url = $log->url;
        }
    }

    if ($surveys) {
        $content = true;
        print_headline(get_string("newsurveyresponses", "survey").":");
        foreach ($surveys as $survey) {
            $date = userdate($survey->time, "%e %b, %H:%M");
            echo "<P><FONT SIZE=1>$date - $survey->firstname $survey->lastname<BR>";
            echo "\"<A HREF=\"$CFG->wwwroot/mod/survey/$survey->url\">";
            echo "$survey->name";
            echo "</A>\"</FONT></P>";
        }
    }
 
    return $content;
}

function survey_already_done($survey, $user) {
   return record_exists_sql("SELECT * FROM survey_answers WHERE survey='$survey' AND user='$user'");
}


function survey_get_responses($survey) {
    return get_records_sql("SELECT a.time as time, count(*) as numanswers, u.*
                            FROM survey_answers AS a, user AS u
                            WHERE a.answer1 <> '0' AND a.answer2 <> '0'
                                  AND a.survey = $survey 
                                  AND a.user = u.id
                            GROUP BY a.user ORDER BY a.time ASC");
}

function survey_count_responses($survey) {
    if ($responses = survey_get_responses($survey)) {
        return count($responses);
    } else {
        return 0;
    }
}


function survey_print_all_responses($survey, $results) {
    global $THEME;

    echo "<TABLE CELLPADDING=5 CELLSPACING=2 ALIGN=CENTER>";
    echo "<TR><TD>Name<TD>Time<TD>Answered</TR>";

    foreach ($results as $a) {
                 
        echo "<TR>";
        echo "<TD><A HREF=\"report.php?action=student&student=$a->id&id=$survey\">$a->firstname $a->lastname</A></TD>";
        echo "<TD>".userdate($a->time, "%e %B %Y, %I:%M %p")."</TD>";
        echo "<TD align=right>$a->numanswers</TD>";
        echo "</TR>";
    }
    echo "</TABLE>";
}


function survey_get_template_name($templateid) {
    global $db;

    if ($templateid) {
        if ($ss = $db->Execute("SELECT name FROM surveys WHERE id = $templateid")) {
            return $ss->fields["name"];
        }
    } else {
        return "";
    }
}


function survey_get_analysis($survey, $user) {
    global $db;

    return get_record_sql("SELECT notes from survey_analysis WHERE survey='$survey' and user='$user'");
}

function survey_update_analysis($survey, $user, $notes) {
    global $db;

    return $db->Execute("UPDATE survey_analysis SET notes='$notes' WHERE survey='$survey' and user='$user'");
}


function survey_add_analysis($survey, $user, $notes) {
    global $db;

    return $db->Execute("INSERT INTO survey_analysis SET notes='$notes', survey='$survey', user='$user'");
}

function survey_shorten_name ($name, $numwords) {
    $words = explode(" ", $name);
    for ($i=0; $i < $numwords; $i++) {
        $output .= $words[$i]." ";
    }
    return $output;
}

function survey_user_summary($course, $user, $mod, $survey) {
    global $CFG;
}


function survey_user_outline($course, $user, $mod, $survey) {
    if ($answers = get_records_sql("SELECT * FROM survey_answers WHERE survey='$survey->id' AND user='$user->id'")) {

        $lastanswer = array_pop($answers);

        $result->info = get_string("done", "survey");
        $result->time = $lastanswer->time;
        return $result;
    }
    return NULL;
}


function survey_user_complete($course, $user, $mod, $survey) {
    global $CFG;

    if (survey_already_done($survey->id, $user->id)) {
        echo "<IMG SRC=\"$CFG->wwwroot/mod/survey/graph.php?id=$mod->id&sid=$user->id&type=student.png\">";
    } else {
        print_string("notdone", "survey");
    }
}


function survey_print_multi($question) {
    GLOBAL $db, $qnum, $checklist, $THEME;


    $stripreferthat = get_string("ipreferthat", "survey");
    $strifoundthat = get_string("ifoundthat", "survey");
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
        $bgcolor = survey_question_color($qnum);

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
            echo "<TD WIDTH=10% NOWRAP><P><FONT SIZE=1>$stripreferthat&nbsp;</FONT></P></TD>";
            echo "<TD WIDTH=40% VALIGN=middle rowspan=2><P>$q->text</P></TD>";
            for ($i=1;$i<=$numoptions;$i++) {
                echo "<TD WIDTH=10% ALIGN=CENTER><INPUT TYPE=radio NAME=qP$q->id VALUE=$i></TD>";
            }
            echo "<TD BGCOLOR=\"$THEME->body\"><INPUT TYPE=radio NAME=qP$q->id VALUE=0 checked></TD>";
            echo "</TR>";

            echo "<TR BGCOLOR=$bgcolor>";
            echo "<TD WIDTH=10% NOWRAP><P><FONT SIZE=1>$strifoundthat&nbsp;</P></TD>";
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



function survey_print_single($question) {
    GLOBAL $db, $qnum;

    $bgcolor = survey_question_color(0);

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
        $strchoose = get_string("choose");
        echo "<SELECT NAME=$question->id>";
        echo "<OPTION VALUE=0 SELECTED>$strchoose...</OPTION>";
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

function survey_question_color($qnum) {
    global $THEME;

    if ($qnum) {
        return $qnum % 2 ? $THEME->cellcontent : $THEME->cellcontent2;
        //return $qnum % 2 ? "#CCFFCC" : "#CCFFFF";
    } else {
        return $THEME->cellcontent;
    }
}

?>
