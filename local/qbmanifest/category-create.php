<?php

require_once('../../config.php');
require_once($CFG->dirroot.'/course/lib.php');


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

$catrecords = $DB->get_records("cohort", [
  "contextid" => 1
]);

foreach($catrecords as $catrecord){
    $mdesc = str_replace("Cohort - ", "", $catrecord->description);
    $mdesc = trim($mdesc);
    $mdesc = strip_tags($mdesc);
    $catrecord->description = ucfirst($mdesc);
    $DB->update_record("cohort", $catrecord);

    $coursecategory = $DB->get_record("course_categories", [
        "idnumber" => $catrecord->idnumber
    ], '*');

    if(empty($coursecategory)){
      $coursecategory = new stdClass;
      $coursecategory->name = $catrecord->description;
      $coursecategory->idnumber = $catrecord->idnumber;
      $coursecategory->parent = 0;
      $rcat = core_course_category::create($coursecategory, '');
    }
}

exit;