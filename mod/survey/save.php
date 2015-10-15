<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * This file is responsible for saving the results of a users survey and displaying
 * the final message.
 *
 * @package   mod_survey
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

    require_once('../../config.php');
    require_once('lib.php');


// Make sure this is a legitimate posting

    if (!$formdata = data_submitted() or !confirm_sesskey()) {
        print_error('cannotcallscript');
    }

    $id = required_param('id', PARAM_INT);    // Course Module ID

    if (! $cm = get_coursemodule_from_id('survey', $id)) {
        print_error('invalidcoursemodule');
    }

    if (! $course = $DB->get_record("course", array("id"=>$cm->course))) {
        print_error('coursemisconf');
    }

    $PAGE->set_url('/mod/survey/save.php', array('id'=>$id));
    require_login($course, false, $cm);

    $context = context_module::instance($cm->id);
    require_capability('mod/survey:participate', $context);

    if (! $survey = $DB->get_record("survey", array("id"=>$cm->instance))) {
        print_error('invalidsurveyid', 'survey');
    }

    $strsurveysaved = get_string('surveysaved', 'survey');

    $PAGE->set_title($strsurveysaved);
    $PAGE->set_heading($course->fullname);
    echo $OUTPUT->header();
    echo $OUTPUT->heading($survey->name);

    if (survey_already_done($survey->id, $USER->id)) {
        notice(get_string("alreadysubmitted", "survey"), get_local_referer(false));
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
        if ($key != 'sesskey') {
            $newdata = new stdClass();
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

            $DB->insert_record("survey_answers", $newdata);
        }
    }

    $params = array(
        'context' => $context,
        'courseid' => $course->id,
        'other' => array('surveyid' => $survey->id)
    );
    $event = \mod_survey\event\response_submitted::create($params);
    $event->trigger();

// Print the page and finish up.

    notice(get_string("thanksforanswers","survey", $USER->firstname), "$CFG->wwwroot/course/view.php?id=$course->id");

    exit;



