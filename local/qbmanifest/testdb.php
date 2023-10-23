<?php

require_once('../../config.php');
require_once($CFG->dirroot . '/mod/quiz/lib.php');
require_once($CFG->dirroot . '/mod/quiz/accessmanager.php');
require_once($CFG->dirroot . '/mod/quiz/accessmanager_form.php');
require_once($CFG->dirroot . '/mod/quiz/renderer.php');
require_once($CFG->dirroot . '/mod/quiz/attemptlib.php');
require_once($CFG->libdir . '/completionlib.php');
require_once($CFG->libdir . '/filelib.php');
require_once($CFG->libdir . '/questionlib.php');


/* $old_file = array(
    "contenthash" => "21baa4b893fb6c2c8772f78e1dad1a83b703782b000qbs",
    "pathnamehash" => "5fc08591abae4bcd05f9074e14d07a958aa50cfb000qbs",
    "contextid" => 26076,
    "component" => "qbassignsubmission_file",
    "filearea" => "submission_files",
    "itemid" => 7557,
    "filepath" => "/",
    "filename" => "siuuu.PNG",
    "userid" => "",
    "filesize" => "22223",
    "mimetype" => "image/png",
    "status" => "0",
    "source" => "",
    "author" => "",
    "license" => "",
    "timecreated" => 1695631384,
    "timemodified" => 1695631384,
    "sortorder" => "0",
    "referencefileid" => ""
);

$old_fdata = json_decode(json_encode($old_file));
echo "<pre>";
print_r($old_fdata);
//$id = $DB->insert_record("files", $old_fdata);
$recs = $DB->get_records("files",
  [
    "contextid" => 10462,
    "component" => "qbassignsubmission_file",
    "filearea" => "submission_files",
    "itemid" => 3291,
  ]
  );
foreach($recs as $idata)
{
    $idata->itemid = 7557;
    $idata->contextid = 26076;
    print_r($idata);
    $DB->update_record("files", $idata);
}

print_r($recs); */

$nattempt = new stdClass;
//$nattempt->id = 127;
$nattempt->quiz = 224;
$nattempt->userid =  7640;
$nattempt->attempt =  1;
$nattempt->uniqueid =  13100;
$nattempt->layout =  "1,0,2,0,3,0,4,0,5,0,6,0,7,0,8,0,9,0,10,0,11,0,12,0,13,0,14,0,15,0,16,0,17,0,18,0,19,0";
$nattempt->currentpage =  0;
$nattempt->preview =  0;
$nattempt->state =  "inprogress";
$nattempt->timestart =  1695030615;
$nattempt->timefinish =  0;
$nattempt->timemodified =  1695030615;
$nattempt->timemodifiedoffline =  0;
$nattempt->timecheckstate =  "";
$nattempt->sumgrades =  "";
$nattempt->gradednotificationsenttime =  "";

$id = $DB->insert_record("quiz_attempts", $nattempt);
echo $id;
exit;