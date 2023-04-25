<?php
require_once('../../config.php');
require_once($CFG->libdir.'/gdlib.php');
require_once($CFG->dirroot.'/local/qubitscourse/locallib.php');
require_once($CFG->dirroot . '/local/qubitscourse/renderer.php');
require_once($CFG->dirroot . '/cohort/externallib.php');
require_once($CFG->dirroot.'/local/qubitsuser/externallib.php');

$acohortmembers = core_cohort_external::get_cohort_members(array(4));
$cohortmembers = reset($acohortmembers);
$cohortusers = $cohortmembers["userids"];

$courseid = 41;
$enrolid = 118;
$search = "";
$searchanywhere = true;
$page = 0;
$perpage = 100 + 1;
$siteid = 3;

$ausers = local_qubitsuser_external::get_potential_users($courseid, $enrolid, $search, $searchanywhere, $page, $perpage, $siteid);
echo "<pre>";
print_r($acohortmembers);
print_r($cohortusers);
print_r($ausers);
echo "</pre>";
exit;