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


define("SURVEY_COLLES_ACTUAL",           "1");
define("SURVEY_COLLES_PREFERRED",        "2");
define("SURVEY_COLLES_PREFERRED_ACTUAL", "3");
define("SURVEY_ATTLS",                   "4");
define("SURVEY_CIQ",                     "5");


// STANDARD FUNCTIONS ////////////////////////////////////////////////////////

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

function survey_user_outline($course, $user, $mod, $survey) {
    if ($answers = get_records_select("survey_answers", "survey='$survey->id' AND userid='$user->id'")) {

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
        if ($survey->template == SURVEY_CIQ) { // print out answers for critical incidents
            $table = NULL;
            $table->align = array("left", "left");

            $questions = get_records_list("survey_questions", "id", $survey->questions);
            $questionorder = explode(",", $survey->questions);
            
            foreach ($questionorder as $key=>$val) {
                $question = $questions[$val];
                $questiontext = get_string($question->shorttext, "survey");
                
                if ($answer = survey_get_user_answer($survey->id, $question->id, $user->id)) {
                    $answertext = "$answer->answer1";
                } else {
                    $answertext = "No answer";
                }
                $table->data[] = array("<b>$questiontext</b>", $answertext);
            }
            print_table($table);
            
        } else {
        
            survey_print_graph("id=$mod->id&sid=$user->id&type=student.png");
        }
        
    } else {
        print_string("notdone", "survey");
    }
}

function survey_print_recent_activity($course, $isteacher, $timestart) {
    global $CFG;

    $content = false;
    $surveys = NULL;

    if (!$logs = get_records_select("log", "time > '$timestart' AND ".
                                           "course = '$course->id' AND ".
                                           "module = 'survey' AND ".
                                           "action = 'submit' ", "time ASC")) {
        return false;
    }

    foreach ($logs as $log) {
        //Create a temp valid module structure (course,id)
        $tempmod->course = $log->course;
        $tempmod->id = $log->info;
        //Obtain the visible property from the instance
        $modvisible = instance_is_visible($log->module,$tempmod);
   
        //Only if the mod is visible
        if ($modvisible) {
            $surveys[$log->id] = survey_log_info($log);
            $surveys[$log->id]->time = $log->time;
            $surveys[$log->id]->url = $log->url;
        }
    }

    if ($surveys) {
        $content = true;
        $strftimerecent = get_string("strftimerecent");
        print_headline(get_string("newsurveyresponses", "survey").":");
        foreach ($surveys as $survey) {
            $date = userdate($survey->time, $strftimerecent);
            echo "<p><font size=1>$date - ".fullname($survey)."<br />";
            echo "\"<a href=\"$CFG->wwwroot/mod/survey/$survey->url\">";
            echo "$survey->name";
            echo "</a>\"</font></p>";
        }
    }
 
    return $content;
}

function survey_get_participants($surveyid) {
//Returns the users with data in one survey
//(users with records in survey_analysis and survey_answers, students)

    global $CFG;

    //Get students from survey_analysis
    $st_analysis = get_records_sql("SELECT DISTINCT u.*
                                    FROM {$CFG->prefix}user u,
                                         {$CFG->prefix}survey_analysis a
                                    WHERE a.survey = '$surveyid' and
                                          u.id = a.userid");
    //Get students from survey_answers
    $st_answers = get_records_sql("SELECT DISTINCT u.*
                                   FROM {$CFG->prefix}user u,
                                        {$CFG->prefix}survey_answers a
                                   WHERE a.survey = '$surveyid' and
                                         u.id = a.userid");

    //Add st_answers to st_analysis
    if ($st_answers) {
        foreach ($st_answers as $st_answer) {
            $st_analysis[$st_answer->id] = $st_answer;
        }
    }
    //Return st_analysis array (it contains an array of unique users)
    return ($st_analysis);
}

// SQL FUNCTIONS ////////////////////////////////////////////////////////


function survey_log_info($log) {
    global $CFG;
    return get_record_sql("SELECT s.name, u.firstname, u.lastname, u.picture
                             FROM {$CFG->prefix}survey s, 
                                  {$CFG->prefix}user u
                            WHERE s.id = '$log->info' 
                              AND u.id = '$log->userid'");
}

function survey_get_responses($surveyid, $groupid) {
    global $CFG;

    if ($groupid) {
        $groupsdb = ", {$CFG->prefix}groups_members AS gm";
        $groupsql = "AND gm.groupid = $groupid AND u.id = gm.userid";
    } else {
        $groupsdb = "";
        $groupsql = "";
    }

    return get_records_sql("SELECT MAX(a.time) as time, 
                                   u.id, u.firstname, u.lastname, u.picture
                              FROM {$CFG->prefix}survey_answers AS a, 
                                   {$CFG->prefix}user AS u   $groupsdb
                             WHERE a.survey = $surveyid 
                                   AND a.userid = u.id $groupsql
                          GROUP BY u.id, u.firstname, u.lastname
                          ORDER BY time ASC");
}

function survey_get_analysis($survey, $user) {
    global $CFG;

    return get_record_sql("SELECT notes 
                             FROM {$CFG->prefix}survey_analysis 
                            WHERE survey='$survey' 
                              AND userid='$user'");
}

function survey_update_analysis($survey, $user, $notes) {
    global $CFG;

    return execute_sql("UPDATE {$CFG->prefix}survey_analysis 
                            SET notes='$notes' 
                          WHERE survey='$survey' 
                            AND userid='$user'");
}


function survey_get_user_answers($surveyid, $questionid, $groupid, $sort="sa.answer1,sa.answer2 ASC") {
    global $CFG;

    if ($groupid) {
        $groupsql = "AND gm.groupid = $groupid AND u.id = gm.userid";
    } else {
        $groupsql = "";
    }

    return get_records_sql("SELECT sa.*,u.firstname,u.lastname,u.picture 
                              FROM {$CFG->prefix}survey_answers sa, 
                                   {$CFG->prefix}user u,
                                   {$CFG->prefix}groups_members gm 
                             WHERE sa.survey = '$surveyid' 
                               AND sa.question = $questionid 
                               AND u.id = sa.userid $groupsql
                          ORDER BY $sort");
}

function survey_get_user_answer($surveyid, $questionid, $userid) {
    global $CFG;

    return get_record_sql("SELECT sa.* 
                              FROM {$CFG->prefix}survey_answers sa
                             WHERE sa.survey = '$surveyid' 
                               AND sa.question = '$questionid' 
                               AND sa.userid = '$userid'");
}

// MODULE FUNCTIONS ////////////////////////////////////////////////////////

function survey_add_analysis($survey, $user, $notes) {
    global $CFG;

    $record->survey = $survey;
    $record->userid = $user;
    $record->notes = $notes;

    return insert_record("survey_analysis", $record, false);
}

function survey_already_done($survey, $user) {
   return record_exists("survey_answers", "survey", $survey, "userid", $user);
}

function survey_count_responses($surveyid, $groupid) {
    if ($responses = survey_get_responses($surveyid, $groupid)) {
        return count($responses);
    } else {
        return 0;
    }
}


function survey_print_all_responses($cmid, $results, $courseid) {
    global $THEME;

    $table->head  = array ("", get_string("name"),  get_string("time"));
    $table->align = array ("", "left", "left");
    $table->size = array (35, "", "" );

    foreach ($results as $a) {
        $table->data[] = array(print_user_picture($a->id, $courseid, $a->picture, false, true, false),
               "<a href=\"report.php?action=student&student=$a->id&id=$cmid\">".fullname($a)."</a>", 
               userdate($a->time));
    }

    print_table($table);
}


function survey_get_template_name($templateid) {
    global $db;

    if ($templateid) {
        if ($ss = get_record("surveys", "id", $templateid)) {
            return $ss->name;
        }
    } else {
        return "";
    }
}



function survey_shorten_name ($name, $numwords) {
    $words = explode(" ", $name);
    for ($i=0; $i < $numwords; $i++) {
        $output .= $words[$i]." ";
    }
    return $output;
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

    $subquestions = get_records_list("survey_questions", "id", $question->multi);

    foreach ($subquestions as $q) {
        $qnum++;
        $bgcolor = survey_question_color($qnum);

        if ($q->text) {
            $q->text = get_string($q->text, "survey");
        }

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
        notify("This question type not supported yet");
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

function survey_print_graph($url) {
    global $CFG, $SURVEY_GHEIGHT, $SURVEY_GWIDTH;

    if (empty($CFG->gdversion)) {
        echo "(".get_string("gdneed").")";

    } else {
        echo "<img height=\"$SURVEY_GHEIGHT\" width=\"$SURVEY_GWIDTH\" border=\"1\"".
             " src=\"$CFG->wwwroot/mod/survey/graph.php?$url\">";
    }
}

?>
