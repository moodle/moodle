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

function  quiz_print_question($number, $questionid, $grade, $courseid) {
    
    if (!$question = get_record("quiz_questions", "id", $questionid)) {
        notify("Error: Question not found!");
    }

    $stranswer = get_string("answer", "quiz");
    $strmarks  = get_string("marks", "quiz");

    echo "<TABLE WIDTH=100% CELLSPACING=10><TR><TD NOWRAP WIDTH=100 VALIGN=top>";
    echo "<P ALIGN=CENTER><B>$number</B><BR><FONT SIZE=1>$grade $strmarks</FONT></P>";
    print_spacer(1,100);
    echo "</TD><TD VALIGN=TOP>";

    switch ($question->type) {
       case 1: // shortanswer
           if (!$options = get_record("quiz_shortanswer", "question", $question->id)) {
               notify("Error: Missing question options!");
           }
           if (!$answer = get_record("quiz_answers", "id", $options->answer)) {
               notify("Error: Missing question answers!");
           }
           echo "<P>$question->question</P>";
           if ($question->image) {
               print_file_picture($question->image, $courseid, 200);
           }
           echo "<P ALIGN=RIGHT>$stranswer: <INPUT TYPE=TEXT NAME=q$question->id SIZE=20></P>";
           break;

       case 2: // true-false
           if (!$options = get_record("quiz_truefalse", "question", $question->id)) {
               notify("Error: Missing question options!");
           }
           if (!$true = get_record("quiz_answers", "id", $options->true)) {
               notify("Error: Missing question answers!");
           }
           if (!$false = get_record("quiz_answers", "id", $options->false)) {
               notify("Error: Missing question answers!");
           }
           if (!$true->answer) {
               $true->answer = get_string("true", "quiz");
           }
           if (!$false->answer) {
               $false->answer = get_string("false", "quiz");
           }
           echo "<P>$question->question</P>";
           if ($question->image) {
               print_file_picture($question->image, $courseid, 200);
           }
           echo "<P ALIGN=RIGHT>$stranswer:&nbsp;&nbsp;";
           echo "<INPUT TYPE=RADIO NAME=\"q$question->id\" VALUE=\"$true->id\">$true->answer";
           echo "&nbsp;&nbsp;&nbsp;";
           echo "<INPUT TYPE=RADIO NAME=\"q$question->id\" VALUE=\"$false->id\">$false->answer</P>";
           break;

       case 3: // multiple-choice
           if (!$options = get_record("quiz_multichoice", "question", $question->id)) {
               notify("Error: Missing question options!");
           }
           if (!$answers = get_records_sql("SELECT * from quiz_answers WHERE id in ($options->answers)")) {
               notify("Error: Missing question answers!");
           }
           echo "<P>$question->question</P>";
           if ($question->image) {
               print_file_picture($question->image, $courseid, 200);
           }
           echo "<TABLE ALIGN=right>";
           echo "<TR><TD valign=top>$stranswer:&nbsp;&nbsp;</TD><TD>";
           echo "<TABLE ALIGN=right>";
           $answerids = explode(",", $options->answers);
           foreach ($answerids as $key => $answerid) {
               $answer = $answers[$answerid];
               $qnum = $key + 1;
               echo "<TR><TD valign=top>";
               if (!$options->single) {
                   echo "<INPUT TYPE=RADIO NAME=q$question->id VALUE=\"$answer->id\">";
               } else {
                   echo "<INPUT TYPE=CHECKBOX NAME=q$question->id VALUE=\"$answer->id\">";
               }
               echo "</TD>";
               echo "<TD valign=top>$qnum. $answer->answer</TD>";
               echo "</TR>";
           }
           echo "</TABLE>";
           echo "</TABLE>";
           break;

       default: 
           notify("Error: Unknown question type!");
    }

    echo "</TD></TR></TABLE>";
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
