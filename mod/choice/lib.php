<?PHP // $Id$

function choice_user_summary($course, $user, $mod, $choice) {
    global $CFG;
}


function choice_user_outline($course, $user, $mod, $choice) {
    if ($current = get_record_sql("SELECT * FROM choice_answers
                                   WHERE choice='$choice->id' AND user='$user->id'")) {
        if ($current->answer == "1") {
            $result->info = "'$choice->answer1'";
        } else if ($current->answer == "2") {
            $result->info = "'$choice->answer2'";
        }
        $result->time = $current->timemodified;
        return $result;
    }
    return NULL;
}


function choice_user_complete($course, $user, $mod, $choice) {
    if ($current = get_record_sql("SELECT * FROM choice_answers
                                   WHERE choice='$choice->id' AND user='$user->id'")) {
        if ($current->answer == "1") {
            $result->info = "'$choice->answer1'";
        } else if ($current->answer == "2") {
            $result->info = "'$choice->answer2'";
        }
        $result->time = $current->timemodified;
        echo "Answered: $result->info , last updated ".userdate($result->time);
    } else {
        echo "Not answered yet";
    }
}

?>

