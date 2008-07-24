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

function survey_print_recent_activity($course, $viewfullnames, $timestart) {
    global $CFG;

    $modinfo = get_fast_modinfo($course);
    $ids = array();
    foreach ($modinfo->cms as $cm) {
        if ($cm->modname != 'survey') {
            continue;
        }
        if (!$cm->uservisible) {
            continue;
        }
        $ids[$cm->instance] = $cm->instance;
    }

    if (!$ids) {
        return false;
    }

    $slist = implode(',', $ids); // there should not be hundreds of glossaries in one course, right?

    if (!$rs = get_recordset_sql("SELECT sa.userid, sa.survey, MAX(sa.time) AS time,
                                         u.firstname, u.lastname, u.email, u.picture
                                    FROM {$CFG->prefix}survey_answers sa
                                         JOIN {$CFG->prefix}user u ON u.id = sa.userid
                                   WHERE sa.survey IN ($slist) AND sa.time > $timestart
                                   GROUP BY sa.userid, sa.survey, u.firstname, u.lastname, u.email, u.picture
                                   ORDER BY time ASC")) {
        return false;
    }

    $surveys = array();

    while ($survey = rs_fetch_next_record($rs)) {
        $cm = $modinfo->instances['survey'][$survey->survey];
        $survey->name = $cm->name;
        $survey->cmid = $cm->id;
        $surveys[] = $survey;
    } 
    rs_close($rs);

    if (!$surveys) {
        return false;
    }

    print_headline(get_string('newsurveyresponses', 'survey').':');
    foreach ($surveys as $survey) {
        $url = $CFG->wwwroot.'/mod/survey/view.php?id='.$survey->cmid;
        print_recent_activity_note($survey->time, $survey, $survey->name, $url, false, $viewfullnames);
    }
 
    return true;
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

function survey_get_responses($surveyid, $groupid, $groupingid) {
    global $CFG;

    if ($groupid) {
        $groupsjoin = "INNER JOIN {$CFG->prefix}groups_members gm ON u.id = gm.userid AND gm.groupid = '$groupid' ";

    } else if ($groupingid) {
        $groupsjoin = "INNER JOIN {$CFG->prefix}groups_members gm ON u.id = gm.userid
                       INNER JOIN {$CFG->prefix}groupings_groups gg ON gm.groupid = gg.groupid AND gg.groupingid = $groupingid ";
    } else {
        $groupsjoin = "";
    }

    return get_records_sql("SELECT u.id, u.firstname, u.lastname, u.picture, MAX(a.time) as time
                            FROM {$CFG->prefix}survey_answers a
                                INNER JOIN {$CFG->prefix}user u ON a.userid = u.id
                                $groupsjoin
                            WHERE a.survey = $surveyid
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

function survey_count_responses($surveyid, $groupid, $groupingid) {
    if ($responses = survey_get_responses($surveyid, $groupid, $groupingid)) {
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
    global $USER, $db, $qnum, $checklist;

    $stripreferthat = get_string("ipreferthat", "survey");
    $strifoundthat = get_string("ifoundthat", "survey");
    $strdefault    = get_string('default');
    $strresponses  = get_string('responses', 'survey');

    print_heading($question->text, null, 3, 'questiontext');
    echo "\n<table width=\"90%\" cellpadding=\"4\" cellspacing=\"1\" border=\"0\">";

    $options = explode( ",", $question->options);
    $numoptions = count($options);

    $oneanswer = ($question->type == 1 || $question->type == 2) ? true : false;
    if ($question->type == 2) {
        $P = "P";
    } else {
        $P = "";
    }

    echo "<tr class=\"smalltext\"><th scope=\"row\">$strresponses</th>";
    while (list ($key, $val) = each ($options)) {
        echo "<th scope=\"col\" class=\"hresponse\">$val</th>\n";
    }
    echo "<th>&nbsp;</th></tr>\n";

    if ($oneanswer) {
        echo "<tr><th scope=\"col\" colspan=\"6\">$question->intro</th></tr>\n";
    } else {
        echo "<tr><th scope=\"col\" colspan=\"7\">$question->intro</th></tr>\n"; 
    }

    $subquestions = get_records_list("survey_questions", "id", $question->multi);

    foreach ($subquestions as $q) {
        $qnum++;
        $rowclass = survey_question_rowclass($qnum);
        if ($q->text) {
            $q->text = get_string($q->text, "survey");
        }

        echo "<tr class=\"$rowclass rblock\">";
        if ($oneanswer) {

            echo "<th scope=\"row\" class=\"optioncell\">";
            echo "<b class=\"qnumtopcell\">$qnum</b> &nbsp; ";
            echo $q->text ."</th>\n";
            for ($i=1;$i<=$numoptions;$i++) {
                $hiddentext = get_accesshide($options[$i-1]);
                $id = "q$P" . $q->id . "_$i";
                echo "<td><label for=\"$id\"><input type=\"radio\" name=\"q$P$q->id\" id=\"$id\" value=\"$i\" />$hiddentext</label></td>";
            }
            $default = get_accesshide($strdefault, 'label', '', "for=\"q$P$q->id\"");
            echo "<td class=\"whitecell\"><input type=\"radio\" name=\"q$P$q->id\" id=\"q$P" . $q->id . "_D\" value=\"0\" checked=\"checked\" />$default</td>";
            $checklist["q$P$q->id"] = $numoptions;
        
        } else { 
            // yu : fix for MDL-7501, possibly need to use user flag as this is quite ugly.
            echo "<th scope=\"row\" class=\"optioncell\">";
            echo "<b class=\"qnumtopcell\">$qnum</b> &nbsp; ";
            $qnum++;
            echo "<span class=\"preferthat smalltext\">$stripreferthat</span> &nbsp; ";
            echo "<span class=\"option\">$q->text</span></th>\n";
            for ($i=1;$i<=$numoptions;$i++) {
                $hiddentext = get_accesshide($options[$i-1]);
                $id = "qP" . $q->id . "_$i";
                echo "<td><label for=\"$id\"><input type=\"radio\" name=\"qP$q->id\" id=\"$id\" value=\"$i\" />$hiddentext</label></td>";
            }
            $default = get_accesshide($strdefault, 'label', '', "for=\"qP$q->id\"");
            echo "<td><input type=\"radio\" name=\"qP$q->id\" id=\"qP$q->id\" value=\"0\" checked=\"checked\" />$default</td>";
            echo "</tr>";

            echo "<tr class=\"$rowclass rblock\">";
            echo "<th scope=\"row\" class=\"optioncell\">";
            echo "<b class=\"qnumtopcell\">$qnum</b> &nbsp; ";
            echo "<span class=\"foundthat smalltext\">$strifoundthat</span> &nbsp; ";
            echo "<span class=\"option\">$q->text</span></th>\n";
            for ($i=1;$i<=$numoptions;$i++) {
                $hiddentext = get_accesshide($options[$i-1]);
                $id = "q" . $q->id . "_$i";
                echo "<td><label for=\"$id\"><input type=\"radio\" name=\"q$q->id\" id=\"$id\" value=\"$i\" />$hiddentext</label></td>";
            }
            $default = get_accesshide($strdefault, 'label', '', "for=\"q$q->id\"");
            echo "<td class=\"buttoncell\"><input type=\"radio\" name=\"q$q->id\" id=\"q$q->id\" value=\"0\" checked=\"checked\" />$default</td>";
            $checklist["qP$q->id"] = $numoptions;
            $checklist["q$q->id"] = $numoptions;            
        }
        echo "</tr>\n";
    }
    echo "</table>";
}



function survey_print_single($question) {
    global $db, $qnum;

    $rowclass = survey_question_rowclass(0);

    $qnum++;

    echo "<br />\n";
    echo "<table width=\"90%\" cellpadding=\"4\" cellspacing=\"0\">\n";
    echo "<tr class=\"$rowclass\">";
    echo "<th scope=\"row\" class=\"optioncell\"><label for=\"q$question->id\"><b class=\"qnumtopcell\">$qnum</b> &nbsp; ";
    echo "<span class=\"questioncell\">$question->text</span></label></th>\n";
    echo "<td class=\"questioncell smalltext\">\n";


    if ($question->type == 0) {           // Plain text field
        echo "<textarea rows=\"3\" cols=\"30\" name=\"q$question->id\" id=\"q$question->id\">$question->options</textarea>";

    } else if ($question->type > 0) {     // Choose one of a number
        $strchoose = get_string("choose");
        echo "<select name=\"q$question->id\" id=\"q$question->id\">";
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

    echo "</td></tr></table>";

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


/**
 * Implementation of the function for printing the form elements that control
 * whether the course reset functionality affects the survey.
 * @param $mform form passed by reference
 */
function survey_reset_course_form_definition(&$mform) {
    $mform->addElement('header', 'surveyheader', get_string('modulenameplural', 'survey'));
    $mform->addElement('checkbox', 'reset_survey_answers', get_string('deleteallanswers','survey'));
    $mform->addElement('checkbox', 'reset_survey_analysis', get_string('deleteanalysis','survey'));
    $mform->disabledIf('reset_survey_analysis', 'reset_survey_answers', 'checked');
}

/**
 * Course reset form defaults.
 */
function survey_reset_course_form_defaults($course) {
    return array('reset_survey_answers'=>1, 'reset_survey_analysis'=>1);
}

/**
 * Actual implementation of the rest coures functionality, delete all the
 * survey responses for course $data->courseid.
 * @param $data the data submitted from the reset course.
 * @return array status array
 */
function survey_reset_userdata($data) {
    global $CFG;

    $componentstr = get_string('modulenameplural', 'survey');
    $status = array();

    $surveyssql = "SELECT ch.id
                     FROM {$CFG->prefix}survey ch
                    WHERE ch.course={$data->courseid}";

    if (!empty($data->reset_survey_answers)) {
        delete_records_select('survey_answers', "survey IN ($surveyssql)");
        delete_records_select('survey_analysis', "survey IN ($surveyssql)");
        $status[] = array('component'=>$componentstr, 'item'=>get_string('deleteallanswers', 'survey'), 'error'=>false);
    }

    if (!empty($data->reset_survey_analysis)) {
        delete_records_select('survey_analysis', "survey IN ($surveyssql)");
        $status[] = array('component'=>$componentstr, 'item'=>get_string('deleteallanswers', 'survey'), 'error'=>false);
    }

    // no date shifting
    return $status;
}

/**
 * Returns all other caps used in module
 */
function survey_get_extra_capabilities() {
    return array('moodle/site:accessallgroups');
}

?>
