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


function choice_add_instance($choice) {
// Given an object containing all the necessary data, 
// (defined by the form in mod.html) this function 
// will create a new instance and return the id number 
// of the new instance.

    $choice->timemodified = time();

    return insert_record("choice", $choice);
}


function choice_update_instance($choice) {
// Given an object containing all the necessary data, 
// (defined by the form in mod.html) this function 
// will update an existing instance with new data.

    $choice->id = $choice->instance;
    $choice->timemodified = time();

    return update_record("choice", $choice);
}


function choice_delete_instance($id) {
// Given an ID of an instance of this module, 
// this function will permanently delete the instance 
// and any data that depends on it.  

    if (! $choice = get_record("choice", "id", "$id")) {
        return false;
    }

    $result = true;

    if (! delete_records("choice_answers", "choice", "$choice->id")) {
        $result = false;
    }

    if (! delete_records("choice", "id", "$choice->id")) {
        $result = false;
    }

    return $result;
}


?>

