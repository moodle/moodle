<?php // $Id$

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
        
            survey_print_graph("id=$mod->id&amp;sid=$user->id&amp;type=student.png");
        }
        
    } else {
        print_string("notdone", "survey");
    }
}

function survey_print_recent_activity($course, $isteacher, $timestart) {
    global $CFG;

    $content = false;
    $surveys = NULL;

    if (!$logs = get_records_select('log', 'time > \''.$timestart.'\' AND '.
                                           'course = \''.$course->id.'\' AND '.
                                           'module = \'survey\' AND '.
                                           'action = \'submit\' ', 'time ASC')) {
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
            $surveys[$log->id]->url = str_replace('&', '&amp;', $log->url);
        }
    }

    if ($surveys) {
        $content = true;
        print_headline(get_string('newsurveyresponses', 'survey').':');
        foreach ($surveys as $survey) {
            print_recent_activity_note($survey->time, $survey, $survey->name,
                                       $CFG->wwwroot.'/mod/survey/'.$survey->url);
        }
    }
 
    return $content;
}

function survey_get_participants($surveyid) {
//Returns the users with data in one survey
//(users with records in survey_analysis and survey_answers, students)

    global $CFG;

    //Get students from survey_analysis
    $st_analysis = get_records_sql("SELECT DISTINCT u.id, u.id
                                    FROM {$CFG->prefix}user u,
                                         {$CFG->prefix}survey_analysis a
                                    WHERE a.survey = '$surveyid' and
                                          u.id = a.userid");
    //Get students from survey_answers
    $st_answers = get_records_sql("SELECT DISTINCT u.id, u.id
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
        $groupsdb = ", {$CFG->prefix}groups_members gm ";
        $groupsql = "AND gm.groupid = '$groupid' AND u.id = gm.userid";

    } else {
        $groupsdb = "";
        $groupsql = "";
    }

    return get_records_sql("SELECT u.id, u.firstname, u.lastname, u.picture, MAX(a.time) as time
                            FROM {$CFG->prefix}survey_answers a,
                                 {$CFG->prefix}user u   $groupsdb
                            WHERE a.survey = $surveyid
                              AND a.userid = u.id $groupsql
                            GROUP BY u.id, u.firstname, u.lastname, u.picture
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

    $table->head  = array ("", get_string("name"),  get_string("time"));
    $table->align = array ("", "left", "left");
    $table->size = array (35, "", "" );

    foreach ($results as $a) {
        $table->data[] = array(print_user_picture($a->id, $courseid, $a->picture, false, true, false),
               "<a href=\"report.php?action=student&amp;student=$a->id&amp;id=$cmid\">".fullname($a)."</a>", 
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
    GLOBAL $USER, $db, $qnum, $checklist;

    $stripreferthat = get_string("ipreferthat", "survey");
    $strifoundthat = get_string("ifoundthat", "survey");
    echo "<br />\n";
    echo "<span class='questiontext'><b>$question->text</b></span><br />";
    echo "<div style=\"text-align:center\">";
    echo "<table width=\"90%\" cellpadding=\"4\" cellspacing=\"1\" border=\"0\">";

    $options = explode( ",", $question->options);
    $numoptions = count($options);

    $oneanswer = ($question->type == 1 || $question->type == 2) ? true : false;
    if ($question->type == 2) {
        $P = "P";
    } else {
        $P = "";
    }
   
    if ($oneanswer) { 
        echo "<tr><td colspan=\"2\">$question->intro</td>";
    } else {
        echo "<tr><td colspan=\"3\">$question->intro</td>"; 
    }

    while (list ($key, $val) = each ($options)) {
        echo "<td class=\"smalltextcell\"><span class='smalltext'>$val</span></td>\n";
    }
    echo "<td align=\"center\">&nbsp;</td></tr>\n";

    $subquestions = get_records_list("survey_questions", "id", $question->multi);
    
    foreach ($subquestions as $q) {
        $qnum++;
        $rowclass = survey_question_rowclass($qnum);
        if ($q->text) {
            $q->text = get_string($q->text, "survey");
        }

        echo "<tr class=\"$rowclass\">";
        if ($oneanswer) {

            echo "<td class=\"qnumtopcell\"><b>$qnum</b></td>";
            echo "<td valign=\"top\">$q->text</td>";
            for ($i=1;$i<=$numoptions;$i++) {            
                $screenreader = !empty($USER->screenreader)?"<label for=\"q$P" . $q->id . "_$i\">".$options[$i-1]."</label><br/>":'';
                echo "<td class=\"screenreadertext\">".$screenreader."<input type=\"radio\" name=\"q$P$q->id\" id=\"q$P" . $q->id . "_$i\" value=\"$i\" alt=\"$i\" /></td>";
            }
            echo "<td class=\"whitecell\"><input type=\"radio\" name=\"q$P$q->id\" value=\"0\" checked=\"checked\" alt=\"0\" /></td>";
            $checklist["q$P$q->id"] = $numoptions;
        
        } else { 
            // yu : fix for MDL-7501, possibly need to use user flag as this is quite ugly.
            echo "<td class=\"qnummiddlecell\"><b>$qnum</b></td>";
            $qnum++;
            echo "<td class=\"preferthat\"><span class='smalltext'>$stripreferthat&nbsp;</span></td>";
            echo "<td class=\"optioncell\">$q->text</td>";
            for ($i=1;$i<=$numoptions;$i++) {
                $screenreader = !empty($USER->screenreader)?"<label for=\"qP" . $q->id . "_$i\">".$options[$i-1]."</label><br/>":'';
                echo "<td class=\"screenreadertext\">".$screenreader."<input type=\"radio\" name=\"qP$q->id\" id=\"qP" . $q->id . "_$i\" value=\"$i\" alt=\"$i\"/></td>";
            }
            echo "<td><input type=\"radio\" name=\"qP$q->id\" value=\"0\" checked=\"checked\" alt=\"0\" /></td>";
            echo "</tr>";

            echo "<tr class=\"$rowclass\">";
            echo "<td class=\"qnumtopcell\"><b>$qnum</b></td>";
            echo "<td class=\"foundthat\"><span class='smalltext'>$strifoundthat&nbsp;</span></td>";
            echo "<td class=\"optioncell\">$q->text</td>";
            for ($i=1;$i<=$numoptions;$i++) {
                $screenreader = !empty($USER->screenreader)?"<label for=\"q" . $q->id . "_$i\">".$options[$i-1]."</label><br/>":'';
                echo "<td class=\"screenreadertext\">".$screenreader."<input type=\"radio\" name=\"q$q->id\" id=\"q" . $q->id . "_$i\" value=\"$i\" alt=\"$i\" /></td>";
            }
            echo "<td class=\"buttoncell\"><input type=\"radio\" name=\"q$q->id\" value=\"0\" checked=\"checked\" alt=\"0\" /></td>";
            $checklist["qP$q->id"] = $numoptions;
            $checklist["q$q->id"] = $numoptions;            
        }
        echo "</tr>\n";
        
    }
    echo "</table>";
    echo "</div>";
}



function survey_print_single($question) {
    GLOBAL $db, $qnum;

    $rowclass = survey_question_rowclass(0);

    $qnum++;

    echo "<br />\n";
    echo "<table width=\"90%\" cellpadding=\"4\" cellspacing=\"0\">\n";
    echo "<tr class=\"$rowclass\">";
    echo "<td valign=\"top\"><b>$qnum</b></td>";
    echo "<td class=\"questioncell\">$question->text</td>\n";
    echo "<td class=\"questioncell\"><span class='smalltext'>\n";


    if ($question->type == 0) {           // Plain text field
        echo "<textarea rows=\"3\" cols=\"30\" name=\"q$question->id\">$question->options</textarea>";

    } else if ($question->type > 0) {     // Choose one of a number
        $strchoose = get_string("choose");
        echo "<select name=\"q$question->id\">";
        echo "<option value=\"0\" selected=\"selected\">$strchoose...</option>";
        $options = explode( ",", $question->options);
        foreach ($options as $key => $val) {
            $key++;
            echo "<option value=\"$key\">$val</option>";
        }
        echo "</select>";

    } else if ($question->type < 0) {     // Choose several of a number
        $options = explode( ",", $question->options);
        notify("This question type not supported yet");
    }

    echo "</span></td></tr></table>";

}

function survey_question_rowclass($qnum) {

    if ($qnum) {
        return $qnum % 2 ? 'r0' : 'r1';
    } else {
        return 'r0';
    }
}

function survey_print_graph($url) {
    global $CFG, $SURVEY_GHEIGHT, $SURVEY_GWIDTH;

    if (empty($CFG->gdversion)) {
        echo "(".get_string("gdneed").")";

    } else {
        echo "<img class='resultgraph' height=\"$SURVEY_GHEIGHT\" width=\"$SURVEY_GWIDTH\"".
             " src=\"$CFG->wwwroot/mod/survey/graph.php?$url\" alt=\"".get_string("surveygraph", "survey")."\" />";
    }
}

function survey_get_view_actions() {
    return array('download','view all','view form','view graph','view report');
}

function survey_get_post_actions() {
    return array('submit');
}

?>
