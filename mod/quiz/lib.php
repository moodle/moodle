<?PHP  // $Id$

// Library of function for module quiz

$QUIZ_GRADE_METHOD = array ( "1" => get_string("gradehighest", "quiz"),
                             "2" => get_string("gradeaverage", "quiz"),
                             "3" => get_string("attemptfirst", "quiz"),
                             "4" => get_string("attemptlast", "quiz")
                           );


function quiz_add_instance($quiz) {
// Given an object containing all the necessary data, 
// (defined by the form in mod.html) this function 
// will create a new instance and return the id number 
// of the new instance.

    $quiz->timemodified = time();

    # May have to add extra stuff in here #
    
    return insert_record("quiz", $quiz);
}


function quiz_update_instance($quiz) {
// Given an object containing all the necessary data, 
// (defined by the form in mod.html) this function 
// will update an existing instance with new data.

    $quiz->timemodified = time();
    $quiz->id = $quiz->instance;

    # May have to add extra stuff in here #

    return update_record("quiz", $quiz);
}


function quiz_delete_instance($id) {
// Given an ID of an instance of this module, 
// this function will permanently delete the instance 
// and any data that depends on it.  

    if (! $quiz = get_record("quiz", "id", "$id")) {
        return false;
    }

    $result = true;

    # Delete any dependent records here #

    if (! delete_records("quiz", "id", "$quiz->id")) {
        $result = false;
    }

    return $result;
}

function quiz_user_outline($course, $user, $mod, $quiz) {
// Return a small object with summary information about what a 
// user has done with a given particular instance of this module
// Used for user activity reports.
// $return->time = the time they did it
// $return->info = a short text description

    return $return;
}

function quiz_user_complete($course, $user, $mod, $quiz) {
// Print a detailed representation of what a  user has done with 
// a given particular instance of this module, for user activity reports.

    return true;
}

function quiz_print_recent_activity(&$logs, $isteacher=false) {
// Given a list of logs, assumed to be those since the last login 
// this function prints a short list of changes related to this module
// If isteacher is true then perhaps additional information is printed.
// This function is called from course/lib.php: print_recent_activity()

    global $CFG, $COURSE_TEACHER_COLOR;

    return $content;  // True if anything was printed, otherwise false
}

function quiz_cron () {
// Function to be run periodically according to the moodle cron
// This function searches for things that need to be done, such 
// as sending out mail, toggling flags etc ... 

    global $CFG;

    return true;
}


//////////////////////////////////////////////////////////////////////////////////////
// Any other quiz functions go here.  Each of them must have a name that 
// starts with quiz_

function  quiz_print_question($number, $questionid) {
    echo "<P><B>$number</B></P>";
    echo "<UL>";
    echo "<P>XXXXXX</P>";
    echo "</UL>";
    echo "<HR>";
}


function quiz_get_user_attempts($quizid, $userid) {
    return get_records_sql("SELECT * FROM quiz_attempts WHERE quiz = '$quizid' and user = '$userid' ORDER by attempt ASC");
}

function quiz_get_grade($quizid, $userid) {
    if (!$grade = get_record_sql("SELECT * FROM quiz_grades WHERE quiz = '$quizid' and user = '$userid'")) {
        return 0;
    }

    return $grade->grade;
}

function quiz_calculate_best_grade($quiz, $attempts) {
// Calculate the best grade for a quiz given a number of attempts by a particular user.

    switch ($quiz->grademethod) {
        case "1": // Use highest score
            $max = 0;
            foreach ($attempts as $attempt) {
                if ($attempt->grade > $max) {
                    $max = $attempt->grade;
                }
            }
            return $max;

        case "2": // Use average score
            $sum = 0;
            $count = 0;
            foreach ($attempts as $attempt) {
                $sum += $attempt->grade;
                $count++;
            }
            return (float)$sum/$count;

        case "3": // Use first attempt
            foreach ($attempts as $attempt) {
                return $attempt->attempt;
            }
            break;

        default:
        case "4": // Use last attempt
            foreach ($attempts as $attempt) {
                $final = $attempt->attempt;
            }
            return $final;
    }
}

?>
