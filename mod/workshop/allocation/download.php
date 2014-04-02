<?php

// Download all allocations in the same format that it can be uploaded

require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/config.php');
require_once(dirname(dirname(__FILE__)).'/locallib.php');
require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/lib/csvlib.class.php');

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

require_capability('mod/workshop:viewallassessments', $PAGE->context);

$workshop = new workshop($workshop, $cm, $course);

$allocations = $workshop->get_allocations();

header("Content-type: application/csv");
header('Content-Disposition: attachment; filename="allocations.csv"');

$organised_allocations = array();

//reorganise allocations to be reviewerid => array(authorid => allocation)

$allusers = array();

foreach ($allocations as $allocation) {
  $organised_allocations[$allocation->reviewerid][$allocation->authorid] = $allocation;
  $allusers[$allocation->reviewerid] = $allocation->reviewerid;
  $allusers[$allocation->authorid] = $allocation->authorid;
}

$users = $DB->get_records_list('user', 'id', array_keys($allusers), 'id, username');

$rows = array();

foreach ($organised_allocations as $reviewer => $allocations) {
  $reviewername = $users[$reviewer]->username;
  $row = array($reviewername);
  foreach ($allocations as $author => $allocation) {
    $row[] = $users[$author]->username;
  }
  $rows[$reviewername] = $row;
}

ksort($rows, SORT_NATURAL);

foreach ($rows as $row) {
  echo implode(",", $row);
  echo "\n";
}