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
 * This file is responsible for producing the downloadable versions of a survey
 * module.
 *
 * @package   mod_survey
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once ("../../config.php");

// Check that all the parameters have been provided.

$id    = required_param('id', PARAM_INT);    // Course Module ID
$type  = optional_param('type', 'xls', PARAM_ALPHA);
$group = optional_param('group', 0, PARAM_INT);

if (! $cm = get_coursemodule_from_id('survey', $id)) {
    throw new \moodle_exception('invalidcoursemodule');
}

if (! $course = $DB->get_record("course", array("id"=>$cm->course))) {
    throw new \moodle_exception('coursemisconf');
}

$context = context_module::instance($cm->id);

$PAGE->set_url('/mod/survey/download.php', array('id'=>$id, 'type'=>$type, 'group'=>$group));

require_login($course, false, $cm);
require_capability('mod/survey:download', $context) ;

if (! $survey = $DB->get_record("survey", array("id"=>$cm->instance))) {
    throw new \moodle_exception('invalidsurveyid', 'survey');
}

$params = array(
    'objectid' => $survey->id,
    'context' => $context,
    'courseid' => $course->id,
    'other' => array('type' => $type, 'groupid' => $group)
);
$event = \mod_survey\event\report_downloaded::create($params);
$event->trigger();

/// Check to see if groups are being used in this survey

$groupmode = groups_get_activity_groupmode($cm);   // Groups are being used

if ($groupmode and $group) {
    $users = get_users_by_capability($context, 'mod/survey:participate', '', '', '', '', $group, null, false);
} else {
    $users = get_users_by_capability($context, 'mod/survey:participate', '', '', '', '', '', null, false);
    $group = false;
}

// The order of the questions
$order = explode(",", $survey->questions);

// Get the actual questions from the database
$questions = $DB->get_records_list("survey_questions", "id", $order);

// Get an ordered array of questions
$orderedquestions = array();

$virtualscales = false;
foreach ($order as $qid) {
    $orderedquestions[$qid] = $questions[$qid];
    // Check if this question is using virtual scales
    if (!$virtualscales && $questions[$qid]->type < 0) {
        $virtualscales = true;
    }
}
$nestedorder = array();//will contain the subquestions attached to the main questions
$preparray = array();

foreach ($orderedquestions as $qid=>$question) {
    //$orderedquestions[$qid]->text = get_string($question->text, "survey");
    if (!empty($question->multi)) {
        $actualqids = explode(",", $questions[$qid]->multi);
        foreach ($actualqids as $subqid) {
            if (!empty($orderedquestions[$subqid]->type)) {
                $orderedquestions[$subqid]->type = $questions[$qid]->type;
            }
        }
    } else {
        $actualqids = array($qid);
    }
    if ($virtualscales && $questions[$qid]->type < 0) {
        $nestedorder[$qid] = $actualqids;
    } else if (!$virtualscales && $question->type >= 0) {
        $nestedorder[$qid] = $actualqids;
    } else {
        //todo andrew this was added by me. Is it correct?
        $nestedorder[$qid] = array();
    }
}

$reversednestedorder = array();
foreach ($nestedorder as $qid=>$subqidarray) {
    foreach ($subqidarray as $subqui) {
        $reversednestedorder[$subqui] = $qid;
    }
}

//need to get info on the sub-questions from the db and merge the arrays of questions
$allquestions = array_merge($questions, $DB->get_records_list("survey_questions", "id", array_keys($reversednestedorder)));

//array_merge() messes up the keys so reinstate them
$questions = array();
foreach($allquestions as $question) {
    $questions[$question->id] = $question;

    //while were iterating over the questions get the question text
    $questions[$question->id]->text = get_string($questions[$question->id]->text, "survey");
}
unset($allquestions);

// Get and collate all the results in one big array
if (! $surveyanswers = $DB->get_records("survey_answers", array("survey"=>$survey->id), "time ASC")) {
    throw new \moodle_exception('cannotfindanswer', 'survey');
}

$results = array();

foreach ($surveyanswers as $surveyanswer) {
    if (!$group || isset($users[$surveyanswer->userid])) {
        //$questionid = $reversednestedorder[$surveyanswer->question];
        $questionid = $surveyanswer->question;
        if (!array_key_exists($surveyanswer->userid, $results)) {
            $results[$surveyanswer->userid] = array('time'=>$surveyanswer->time);
        }
        $results[$surveyanswer->userid][$questionid]['answer1'] = $surveyanswer->answer1;
        $results[$surveyanswer->userid][$questionid]['answer2'] = $surveyanswer->answer2;
    }
}

// Output the file as a valid ODS spreadsheet if required
$coursecontext = context_course::instance($course->id);
$courseshortname = format_string($course->shortname, true, array('context' => $coursecontext));

if ($type == "ods") {
    require_once("$CFG->libdir/odslib.class.php");

/// Calculate file name
    $downloadfilename = clean_filename(strip_tags($courseshortname.' '.format_string($survey->name, true))).'.ods';
/// Creating a workbook
    $workbook = new MoodleODSWorkbook("-");
/// Sending HTTP headers
    $workbook->send($downloadfilename);
/// Creating the first worksheet
    $myxls = $workbook->add_worksheet(core_text::substr(strip_tags(format_string($survey->name,true)), 0, 31));

    $header = array("surveyid","surveyname","userid","firstname","lastname","email","idnumber","time", "notes");
    $col=0;
    foreach ($header as $item) {
        $myxls->write_string(0,$col++,$item);
    }

    foreach ($nestedorder as $key => $nestedquestions) {
        foreach ($nestedquestions as $key2 => $qid) {
            $question = $questions[$qid];
            if ($question->type == "0" || $question->type == "1" || $question->type == "3" || $question->type == "-1")  {
                $myxls->write_string(0,$col++,"$question->text");
            }
            if ($question->type == "2" || $question->type == "3")  {
                $myxls->write_string(0,$col++,"$question->text (preferred)");
            }
        }
    }

//      $date = $workbook->addformat();
//      $date->set_num_format('mmmm-d-yyyy h:mm:ss AM/PM'); // ?? adjust the settings to reflect the PHP format below

    $row = 0;
    foreach ($results as $user => $rest) {
        $col = 0;
        $row++;
        if (! $u = $DB->get_record("user", array("id"=>$user))) {
            throw new \moodle_exception('invaliduserid');
        }
        if ($n = $DB->get_record("survey_analysis", array("survey"=>$survey->id, "userid"=>$user))) {
            $notes = $n->notes;
        } else {
            $notes = "No notes made";
        }
        $myxls->write_string($row,$col++,$survey->id);
        $myxls->write_string($row,$col++,strip_tags(format_text($survey->name,true)));
        $myxls->write_string($row,$col++,$user);
        $myxls->write_string($row,$col++,$u->firstname);
        $myxls->write_string($row,$col++,$u->lastname);
        $myxls->write_string($row,$col++,$u->email);
        $myxls->write_string($row,$col++,$u->idnumber);
        $myxls->write_string($row,$col++, userdate($results[$user]["time"], "%d-%b-%Y %I:%M:%S %p") );
//          $myxls->write_number($row,$col++,$results[$user]["time"],$date);
        $myxls->write_string($row,$col++,$notes);

        foreach ($nestedorder as $key => $nestedquestions) {
            foreach ($nestedquestions as $key2 => $qid) {
                $question = $questions[$qid];
                if ($question->type == "0" || $question->type == "1" || $question->type == "3" || $question->type == "-1")  {
                    $myxls->write_string($row,$col++, $results[$user][$qid]["answer1"] );
                }
                if ($question->type == "2" || $question->type == "3")  {
                    $myxls->write_string($row, $col++, $results[$user][$qid]["answer2"] );
                }
            }
        }
    }
    $workbook->close();

    exit;
}

// Output the file as a valid Excel spreadsheet if required

if ($type == "xls") {
    require_once("$CFG->libdir/excellib.class.php");

/// Calculate file name
    $downloadfilename = clean_filename(strip_tags($courseshortname.' '.format_string($survey->name,true))).'.xls';
/// Creating a workbook
    $workbook = new MoodleExcelWorkbook("-");
/// Sending HTTP headers
    $workbook->send($downloadfilename);
/// Creating the first worksheet
    $myxls = $workbook->add_worksheet(core_text::substr(strip_tags(format_string($survey->name,true)), 0, 31));

    $header = array("surveyid","surveyname","userid","firstname","lastname","email","idnumber","time", "notes");
    $col=0;
    foreach ($header as $item) {
        $myxls->write_string(0,$col++,$item);
    }

    foreach ($nestedorder as $key => $nestedquestions) {
        foreach ($nestedquestions as $key2 => $qid) {
            $question = $questions[$qid];

            if ($question->type == "0" || $question->type == "1" || $question->type == "3" || $question->type == "-1")  {
                $myxls->write_string(0,$col++,"$question->text");
            }
            if ($question->type == "2" || $question->type == "3")  {
                $myxls->write_string(0,$col++,"$question->text (preferred)");
            }
        }
    }

//      $date = $workbook->addformat();
//      $date->set_num_format('mmmm-d-yyyy h:mm:ss AM/PM'); // ?? adjust the settings to reflect the PHP format below

    $row = 0;
    foreach ($results as $user => $rest) {
        $col = 0;
        $row++;
        if (! $u = $DB->get_record("user", array("id"=>$user))) {
            throw new \moodle_exception('invaliduserid');
        }
        if ($n = $DB->get_record("survey_analysis", array("survey"=>$survey->id, "userid"=>$user))) {
            $notes = $n->notes;
        } else {
            $notes = "No notes made";
        }
        $myxls->write_string($row,$col++,$survey->id);
        $myxls->write_string($row,$col++,strip_tags(format_text($survey->name,true)));
        $myxls->write_string($row,$col++,$user);
        $myxls->write_string($row,$col++,$u->firstname);
        $myxls->write_string($row,$col++,$u->lastname);
        $myxls->write_string($row,$col++,$u->email);
        $myxls->write_string($row,$col++,$u->idnumber);
        $myxls->write_string($row,$col++, userdate($results[$user]["time"], "%d-%b-%Y %I:%M:%S %p") );
//          $myxls->write_number($row,$col++,$results[$user]["time"],$date);
        $myxls->write_string($row,$col++,$notes);

        foreach ($nestedorder as $key => $nestedquestions) {
            foreach ($nestedquestions as $key2 => $qid) {
                $question = $questions[$qid];
                if (($question->type == "0" || $question->type == "1" || $question->type == "3" || $question->type == "-1")
                    && array_key_exists($qid, $results[$user]) ){
                $myxls->write_string($row,$col++, $results[$user][$qid]["answer1"] );
            }
                if (($question->type == "2" || $question->type == "3")
                    && array_key_exists($qid, $results[$user]) ){
                $myxls->write_string($row, $col++, $results[$user][$qid]["answer2"] );
            }
        }
    }
    }
    $workbook->close();

    exit;
}

// Otherwise, return the text file.

// Print header to force download

header("Content-Type: application/download\n");

$downloadfilename = clean_filename(strip_tags($courseshortname.' '.format_string($survey->name,true)));
header("Content-Disposition: attachment; filename=\"$downloadfilename.txt\"");

// Print names of all the fields

echo "surveyid    surveyname    userid    firstname    lastname    email    idnumber    time    ";

foreach ($nestedorder as $key => $nestedquestions) {
    foreach ($nestedquestions as $key2 => $qid) {
        $question = $questions[$qid];
    if ($question->type == "0" || $question->type == "1" || $question->type == "3" || $question->type == "-1")  {
        echo "$question->text    ";
    }
    if ($question->type == "2" || $question->type == "3")  {
         echo "$question->text (preferred)    ";
    }
}
}
echo "\n";

// Print all the lines of data.
foreach ($results as $user => $rest) {
    if (! $u = $DB->get_record("user", array("id"=>$user))) {
        throw new \moodle_exception('invaliduserid');
    }
    echo $survey->id."\t";
    echo strip_tags(format_string($survey->name,true))."\t";
    echo $user."\t";
    echo $u->firstname."\t";
    echo $u->lastname."\t";
    echo $u->email."\t";
    echo $u->idnumber."\t";
    echo userdate($results[$user]["time"], "%d-%b-%Y %I:%M:%S %p")."\t";

    foreach ($nestedorder as $key => $nestedquestions) {
        foreach ($nestedquestions as $key2 => $qid) {
            $question = $questions[$qid];

            if ($question->type == "0" || $question->type == "1" || $question->type == "3" || $question->type == "-1")  {
                echo $results[$user][$qid]["answer1"]."    ";
            }
            if ($question->type == "2" || $question->type == "3")  {
                echo $results[$user][$qid]["answer2"]."    ";
            }
        }
    }
    echo "\n";
}

exit;
