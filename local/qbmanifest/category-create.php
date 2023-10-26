<?php
require_once('../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->dirroot.'/course/lib.php');
global $CFG, $DB, $USER, $OUTPUT;

$url = "http://qubits.localhost.com/local/qbmanifest/category-create.php?cohortid=efalcon";
$cohort_idnumber = required_param('cohortid', PARAM_ALPHANUMEXT);

$carr = array(
    "efalcon" => array(
        "name" => "Emirates Falcon",
        "idnumber" => "efalcon"
    )
);

if(isset($carr[$cohort_idnumber])){
    $cdata = $carr[$cohort_idnumber];
    $existing = $DB->get_record('course_categories', array('idnumber' => $cdata['idnumber']));
    if(empty($existing)){
        $cdata = json_decode(json_encode($cdata));
        $categoryid = core_course_category::create($cdata);
    }
}
