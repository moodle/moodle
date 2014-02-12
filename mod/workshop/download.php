<?php

/**
 * Download workshop marks
 *
 * @package    mod
 * @subpackage workshop
 * @copyright  2014 Morgan Harris <morgan.harris@unsw.edu.au>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/locallib.php');
require_once(dirname(dirname(dirname(__FILE__))).'/lib/csvlib.class.php');

//header('Content-type: text/plain');

$id         = required_param('id', PARAM_INT); // course_module ID
$sortby     = optional_param('sortby', 'lastname', PARAM_ALPHA);
$sorthow    = optional_param('sorthow', 'ASC', PARAM_ALPHA);

$cm         = get_coursemodule_from_id('workshop', $id, 0, false, MUST_EXIST);
$course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
$workshop   = $DB->get_record('workshop', array('id' => $cm->instance), '*', MUST_EXIST);

require_login($course, false, $cm);
if (isguestuser()) {
    print_error('guestsarenotallowed');
}
$workshop = new workshop($workshop, $cm, $course);

$groupid = groups_get_activity_group($cm, true);

// First we need to gather our initial data.

$teammode = $workshop->teammode;
if ($teammode) {
	$data = $workshop->prepare_grading_report_data_grouped($USER->id, $groupid, 0, PHP_INT_MAX, $sortby, $sorthow);
} else {
 	$data = $workshop->prepare_grading_report_data($USER->id, $groupid, 0, PHP_INT_MAX, $sortby, $sorthow);
}

// Our grading report is a good start, but unfortunately it's not quite enough.
// We also need some ancillary user information.

// For some stupid reason sometimes the keys aren't integers. Yes. I know. Ridiculous.
$userids = array();
foreach($data->userinfo as $k => $v) {
    if (is_int($k)) $userids[] = $k;
}

list($select, $params) = $DB->get_in_or_equal($userids);

$users = $DB->get_records_select('user',"id $select",$params);

foreach($users as $k => $v) {
    $fields = array("idnumber", "username");
    foreach($fields as $m) {
        $data->userinfo[$k]->$m = $v->$m;
    }
}

$dimensions = $workshop->grading_strategy_instance()->get_dimensions_info();

$maximumscore = 0;
foreach($dimensions as $d) {
    $maximumscore += $d->max;
}

$examples = $workshop->get_examples_for_manager();

// $assessments[reviewerid][submissionid]
$assessments = array();
$assessments_rs = $workshop->grading_strategy_instance()->get_assessments_recordset(null, true);
foreach($assessments_rs as $k => $record) {
    if(array_key_exists($k, $examples) && $record->assessmentweight == 1) {
        $examples[$k]->grades[$record->dimensionid] = $record;
    }
    $assessments[$record->reviewerid][$k][$record->dimensionid] = $record;
}

if($teammode) {
    $submissions = $workshop->get_submissions_grouped();
} else {
    $submissions = $workshop->get_submissions();
}

//we also need assessment totals for example submissions

if (count($examples)) {
    list($select, $params) = $DB->get_in_or_equal(array_keys($examples));
    $exampletotals = $DB->get_records_select("workshop_assessments", "submissionid $select", $params);

    $examplegrades = array();
    foreach($exampletotals as $record) {
        $examplegrades[$record->reviewerid][$record->submissionid] = $record;
    }
}

list($select, $params) = $DB->get_in_or_equal(array_keys($submissions));
$feedbackset = $DB->get_records_select("workshop_assessments", "submissionid $select", $params);

foreach($feedbackset as $record) {
    $submissions[$record->submissionid]->feedback[$record->reviewerid] = $record->feedbackauthor;
}

// Define some functions for later

$csv = new csv_export_writer();

function table_to_csv($headers, $table) {
    global $csv;
    
    $h = array();
    foreach($headers as $k => $v) {
        $h[] = $v;
    }
    $csv->add_data($h);
    
    foreach($table as $row) {
        $r = array();
        foreach($headers as $k => $v) {
            $r[] = @$row[$k];
        }
        $csv->add_data($r);
    }

}

//Yet more Excel bullshit - it won't read UTF-8 without a byte order mark
$csv->add_data(array("\xEF\xBB\xBF"));

// We include two tables in this report.

// The first is the grade summary.
// First we need an array of headers.

$headers = array();
$h = $teammode ? array("name", "submissiontitle", "submissiongrade") : array("idnumber", "name", "submissiontitle", "submissiongrade", "gradinggrade");
foreach($h as $i) {
    $headers[$i] = get_string($i, 'workshop');
}

// That gives us the direction for the data array.

$table1 = array();

// $table1 is an enumerated array of associative arrays; a list of dicts.

foreach($data->grades as $k => $grade) {
    $row = array();
    
    if ($teammode) {
        $row['name'] = $grade->name;
    } else {
        $user = $data->userinfo[$k];
        $row['idnumber'] = $user->username;
        $row['name'] = $user->firstname . ' ' . $user->lastname;
        $row['gradinggrade'] = $grade->gradinggrade;
    }

    $row['submissiontitle'] = $grade->submissiontitle;
    $row['submissiongrade'] = $grade->submissiongrade;
    
    $table1[] = $row;
}

$csv->add_data(array( get_string('overallmarks', 'workshop') ));

table_to_csv($headers, $table1);

$csv->add_data(array(""));

// Our second table is the individual marks. It's a bit more complicated!

// We build the headers after the table, since some of them depend
// on the content of the table.

// this is an array of comment fields that have values
// if a comment field is not in this array then it will not get a comments field
$needs_comments = array();
$needs_feedback = false;

$table2 = array();

foreach($examples as $ex) {
    $row = array("markername" => get_string('referencemarker','workshop'));

    $row['submittedby'] = get_string('example','workshop');
    $row['markedsubmission'] = $ex->title;
    
    $total = 0;
    foreach($dimensions as $dimid => $dim) {
        $row["dim$dimid"] = round($ex->grades[$dimid]->grade,2);
        $comment = trim(strip_tags($ex->grades[$dimid]->peercomment));
        $comment = str_replace("\n","\r",$comment);
        if (in_array(substr($comment,0,1), array("-","+","="))) {
            $comment = " $comment";
        }
        $row["comment$dimid"] = $comment;
        if (strlen($comment)) {
            $needs_comments[$dimid] = true;
        }
        $total += $ex->grades[$dimid]->grade;
    }
    
    $row['overallmark'] = $total;
    $row['scaledmark'] = round($ex->grade, 2);
    
    $table2[] = $row;
}

//insert a blank line
$table2[] = array();

foreach($assessments as $reviewerid => $a) {
    $user = $data->userinfo[$reviewerid];
    $reviewheader = array();
    
    foreach($examples as $exid => $ex) {
        if(!array_key_exists($exid, $a))
            continue;
        
        $row = array();
        $marks = $a[$exid];
        
        $row['markeridnumber'] = $user->username;
        $row['markername'] = $user->firstname . ' ' . $user->lastname;
        
        $row['submittedby'] = get_string('example','workshop');
        $row['markedsubmission'] = $ex->title;
        
        $total = 0;
        foreach($dimensions as $dimid => $dim) {
            $row["dim$dimid"] = round($marks[$dimid]->grade,2);
            $comment = trim(strip_tags($marks[$dimid]->peercomment));
            //Excel does not like line feeds inside fields
            $comment = str_replace("\n","\r",$comment);
            //It also doesn't like fields that start with -, + or =
            if (in_array(substr($comment,0,1), array("-","+","="))) {
                $comment = " $comment";
            }
            $row["comment$dimid"] = $comment;
            if (strlen($comment)) {
                $needs_comments[$dimid] = true;
            }
            $total += $marks[$dimid]->grade;
        }
        
        $row['overallmark'] = $total;
        $row['scaledmark'] = round($examplegrades[$reviewerid][$exid]->grade, 2);

        if($teammode) {
           $row['gradinggrade'] = $data->userinfo[$user->id]->gradinggrade;
        }
        
        $table2[] = $row;
    }
    
    foreach($a as $submissionid => $marks) {
        
        if (!array_key_exists($submissionid, $submissions))
            continue;
        
        $submission = $submissions[$submissionid];
        $row = array();
        $row['markeridnumber'] = $user->username;
        $row['markername'] = $user->firstname . ' ' . $user->lastname;
  
        if($teammode) {
           $row['submittedby'] = $submission->group->name;
           $row['gradinggrade'] = $data->userinfo[$user->id]->gradinggrade;
        } else {
            $subuser = $data->userinfo[$submission->authorid];
            $row['submitteridnumber'] = $subuser->username;
            $row['submittedby'] = $subuser->firstname . ' ' . $subuser->lastname;
        }
        
        $row['markedsubmission'] = $submission->title;
        
        $total = 0;
        foreach($dimensions as $dimid => $dim) {
            $row["dim$dimid"] = round($marks[$dimid]->grade,2);
            $comment = trim(strip_tags($marks[$dimid]->peercomment));
            $comment = str_replace("\n","\r",$comment);
            if (in_array(substr($comment,0,1), array("-","+","="))) {
                $comment = " $comment";
            }
            $row["comment$dimid"] = $comment;
            if (strlen($row["comment$dimid"])) {
                $needs_comments[$dimid] = true;
            }
            $total += $marks[$dimid]->grade;
        }
        
        if(!empty($submissions[$submissionid]->feedback)) {
            $feedback = trim(strip_tags($submissions[$submissionid]->feedback[$reviewerid]));
            $feedback = str_replace("\n", "\r", $feedback);
            if (in_array(substr($feedback,0,1), array("-","+","="))) {
                $feedback = " $comment";
            }
            $row['feedback'] = $feedback;
            if (strlen($feedback) > 1) {
                $needs_feedback = true;
            }
        }
        
        $row['overallmark'] = $total;
        $row['scaledmark'] = empty($submission->gradeover) ? round($submission->grade, 2) : round($submission->gradeover, 2);
        
        $table2[] = $row;
    }
    
    //add a blank line
    $table2[] = array();
} 

// Build our headers

$headers = array();
foreach(array("markeridnumber", "markername", "markedsubmission", "submittedby") as $i) {
    $headers[$i] = get_string($i, 'workshop');
}

if (!$teammode) {
    $headers["submitteridnumber"] = get_string("submitteridnumber", 'workshop');
}

foreach($dimensions as $dim) {
    $title = strip_tags($dim->title);
    $title = strtok($title, "\n"); //up to the first newline
    $title = substr($title, 0, 100);
    $headers["dim$dim->id"] = $title . ' / ' . round($dim->max, 2);
    if (! empty($needs_comments[$dim->id])) {
        $headers["comment$dim->id"] = get_string('comments', 'workshop');
    }
}

if ($needs_feedback == true) {
    $headers["feedback"] = get_string('feedback', 'workshop');
}
$headers["overallmark"] = get_string('overallmark', 'workshop') . ' / ' . $maximumscore;
$headers["scaledmark"] = get_string('scaledmark', 'workshop') . ' / 100';

if ($teammode) {
    $headers["gradinggrade"] = get_string("gradinggrade", "workshop");
}

$csv->add_data(array( get_string('individualmarks', 'workshop') ));
table_to_csv($headers, $table2);

$csv->download_file();

// $csv->print_csv_data();
