<?PHP // $Id$

$CHOICE_MAX_NUMBER = 6;

$COLUMN_HEIGHT = 300;

define("CHOICE_PUBLISH_NOT",       "0");
define("CHOICE_PUBLISH_ANONYMOUS", "1");
define("CHOICE_PUBLISH_NAMES",     "2");

$CHOICE_PUBLISH = array (CHOICE_PUBLISH_NOT        => get_string("publishnot", "choice"),
                         CHOICE_PUBLISH_ANONYMOUS  => get_string("publishanonymous", "choice"),
                         CHOICE_PUBLISH_NAMES      => get_string("publishnames", "choice"));


/// Standard functions /////////////////////////////////////////////////////////

function choice_user_outline($course, $user, $mod, $choice) {
    if ($current = get_record("choice_answers", "choice", $choice->id, "userid", $user->id)) {
        $result->info = "'".choice_get_answer($choice, $current->answer)."'";
        $result->time = $current->timemodified;
        return $result;
    }
    return NULL;
}


function choice_user_complete($course, $user, $mod, $choice) {
    if ($current = get_record("choice_answers", "choice", $choice->id, "userid", $user->id)) {
        $result->info = "'".choice_get_answer($choice, $current->answer)."'";
        $result->time = $current->timemodified;
        echo get_string("answered", "choice").": $result->info , last updated ".userdate($result->time);
    } else {
        print_string("notanswered", "choice");
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

function choice_get_participants($choiceid) {
//Returns the users with data in one choice
//(users with records in choice_answers, students)

    global $CFG;

    //Get students
    $students = get_records_sql("SELECT DISTINCT u.*
                                 FROM {$CFG->prefix}user u,
                                      {$CFG->prefix}choice_answers c
                                 WHERE c.choice = '$choiceid' and
                                       u.id = c.userid");

    //Return students array (it contains an array of unique users)
    return ($students);
}


function choice_get_answer($choice, $code) {
// Returns text string which is the answer that matches the code
    switch ($code) {
        case 1:
            return "$choice->answer1";
        case 2:
            return "$choice->answer2";
        case 3:
            return "$choice->answer3";
        case 4:
            return "$choice->answer4";
        case 5:
            return "$choice->answer5";
        case 6:
            return "$choice->answer6";
        default:
            return get_string("notanswered", "choice");
    }
}

function choice_get_choice($choiceid) {
// Gets a full choice record

    if ($choice = get_record("choice", "id", $choiceid)) {
        $choice->answer[1] = $choice->answer1;
        $choice->answer[2] = $choice->answer2;
        $choice->answer[3] = $choice->answer3;
        $choice->answer[4] = $choice->answer4;
        $choice->answer[5] = $choice->answer5;
        $choice->answer[6] = $choice->answer6;
        return $choice;
    } else {
        return false;
    }
}

?>
