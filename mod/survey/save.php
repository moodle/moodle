<?PHP // $Id$

	require('../../config.php');
	require('lib.php');


// Make sure this is a legitimate posting

    if (!isset($HTTP_POST_VARS)) {
        error("You are not supposed to use this script like that.");
    }

    require_variable($id);    // Course Module ID

    if (! $cm = get_record("course_modules", "id", $id)) {
        error("Course Module ID was incorrect");
    }

    if (! $course = get_record("course", "id", $cm->course)) {
        error("Course is misconfigured");
    }

    require_login($course->id);

    if (! $survey = get_record("survey", "id", $cm->instance)) {
        error("Survey ID was incorrect");
    }

    add_to_log($course->id, "survey", "submit", "view.php?id=$cm->id", "$survey->id");

    if (survey_already_done($survey->id, $USER->id)) {
        notice("You've already submitted this survey!", $HTTP_REFERER);
        exit;
    }


// Sort through the data and arrange it
// This is necessary because some of the questions 
// may have two answers, eg Question 1 -> 1 and P1

    $answers = array(); 

    foreach ($HTTP_POST_VARS as $key => $val) {
        if ($key <> "userid" && $key <> "id") {
            if ( substr($key,0,1) == "q") {  
                $key = substr($key,1);   // keep everything but the 'q'
            }
            if ( substr($key,0,1) == "P") {
                $realkey = substr($key,1);
                $answers[$realkey][1] = $val;
            } else {
                $answers[$key][0] = $val;
            }
        }
    }
 

// Now store the data.

    $timenow = time();
    foreach ($answers as $key => $val) {
        $val1 = $val[0]; $val2 = $val[1];
        if (! $result = $db->Execute("INSERT INTO survey_answers 
                         (time, user, survey, question, answer1, answer2) 
                         VALUES ('$timenow', '$USER->id', '$survey->id', '$key', '$val1', '$val2')") ) {
            error("Encountered a problem trying to store your results. Sorry.");
        }
    }

// Print the page and finish up.

	print_header("$course->shortname: Survey sent", "$course->fullname", 
        "<A HREF=/course/view.php?id=$course->id>$course->shortname</A> ->
         <A HREF=index.php?id=$course->id>Surveys</A> -> $survey->name -> Survey sent", "");


    notice("Thanks for your answers, $USER->firstname.", "$CFG->wwwroot/course/view.php?id=$course->id");
   
    exit;
    

?>
