<?PHP // $Id$

// Graph size
$GHEIGHT = 500;
$GWIDTH  = 900;

$QTYPE = array (
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

function get_survey_status($survey) {

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

function get_responses_for_survey($surveyid) {
        global $db;

        if ($aa = $db->Execute("SELECT user FROM survey_answers WHERE survey = $surveyid GROUP BY user")) {
                if ($aa) {
                        return $aa->RowCount();
                } else {
                        return -1;
                }
        } else {
                return -1;
        }
}


function get_template_name($templateid) {
    global $db;

    if ($templateid) {
        if ($ss = $db->Execute("SELECT name FROM surveys WHERE id = $templateid")) {
            return $ss->fields["name"];
        }
    } else {
        return "";
    }
}

function update_survey_analysis($survey, $user, $notes) {
    global $db;

    return $db->Execute("UPDATE survey_analysis SET notes='$notes' WHERE survey='$survey' and user='$user'");
}

function add_survey_analysis($survey, $user, $notes) {
    global $db;

    return $db->Execute("INSERT INTO survey_analysis SET notes='$notes', survey='$survey', user='$user'");
}



?>
