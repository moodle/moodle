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

function survey_already_done($survey, $user) {
   return record_exists_sql("SELECT * FROM survey_answers WHERE survey='$survey' AND user='$user'");
}

function survey_get_status($survey) {

    $timenow = time();
    if ($survey->locked) {
        if (($survey->timeopen <= $timenow) && ($timenow <= $survey->timeclose)) {
            return "released";
        } else if ($survey->timenow >= $survey->timeclose) {
            return "finished";
        } else {
            return "error";
        }
    } else {
        return "editing";
    }

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


function survey_user_summary($course, $user, $mod, $survey) {
    global $CFG;
}


function survey_user_outline($course, $user, $mod, $survey) {
    if ($answers = get_records_sql("SELECT * FROM survey_answers WHERE survey='$survey->id' AND user='$user->id'")) {

        $lastanswer = array_pop($answers);

        $result->info = "Done";
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
        echo "Not done yet";
    }
}

?>
