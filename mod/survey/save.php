<?php // $Id$

    require_once('../../config.php');
    require_once('lib.php');


// Make sure this is a legitimate posting

    if (!$formdata = data_submitted("$CFG->wwwroot/mod/survey/view.php") or !confirm_sesskey()) {
        error("You are not supposed to use this script like that.");
    }

    $id = required_param('id', PARAM_INT);    // Course Module ID

    if (! $cm = get_coursemodule_from_id('survey', $id)) {
        error("Course Module ID was incorrect");
    }

    if (! $course = get_record("course", "id", $cm->course)) {
        error("Course is misconfigured");
    }

    require_login($course->id, false, $cm);
    
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);
    require_capability('mod/survey:participate', $context);
    
    if (! $survey = get_record("survey", "id", $cm->instance)) {
        error("Survey ID was incorrect");
    }

    add_to_log($course->id, "survey", "submit", "view.php?id=$cm->id", "$survey->id", "$cm->id");

    $strsurveysaved = get_string('surveysaved', 'survey');

    $navigation = build_navigation('', $cm);
    print_header_simple("$strsurveysaved", "", $navigation, "");


    if (survey_already_done($survey->id, $USER->id)) {
        notice(get_string("alreadysubmitted", "survey"), $_SERVER["HTTP_REFERER"]);
        exit;
    }


// Sort through the data and arrange it
// This is necessary because some of the questions
// may have two answers, eg Question 1 -> 1 and P1

    $answers = array();

    foreach ($formdata as $key => $val) {
        if ($key <> "userid" && $key <> "id") {
            if ( substr($key,0,1) == "q") {
                $key = clean_param(substr($key,1), PARAM_ALPHANUM);   // keep everything but the 'q', number or Pnumber
            }
            if ( substr($key,0,1) == "P") {
                $realkey = (int) substr($key,1);
                $answers[$realkey][1] = $val;
            } else {
                $answers[$key][0] = $val;
            }
        }
    }


// Now store the data.

    $timenow = time();
    foreach ($answers as $key => $val) {

        $newdata->time = $timenow;
        $newdata->userid = $USER->id;
        $newdata->survey = $survey->id;
        $newdata->question = $key;
        if (!empty($val[0])) {
            $newdata->answer1 = $val[0];
        } else {
            $newdata->answer1 = "";
        }
        if (!empty($val[1])) {
            $newdata->answer2 = $val[1];
        } else {
            $newdata->answer2 = "";
        }

        if (! insert_record("survey_answers", $newdata)) {
            error("Encountered a problem trying to store your results. Sorry.");
        }
    }

// Print the page and finish up.

    notice(get_string("thanksforanswers","survey", $USER->firstname), "$CFG->wwwroot/course/view.php?id=$course->id");

    exit;


?>
