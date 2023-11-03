<?php
define('AJAX_SCRIPT', true);
require_once('../../../../config.php');
global $CFG, $PAGE, $COURSE, $USER;

$context = context_system::instance();
require_login();
$PAGE->set_context($context);

$reportid = optional_param('reportid', 0, PARAM_INT);
$draw = optional_param('draw', 1, PARAM_INT);
$start = optional_param('start', 0, PARAM_INT);
$length = optional_param('length', 10, PARAM_INT);
$search = optional_param('search', '', PARAM_RAW);
$order = $_REQUEST['order'][0];
$ordercolumn = clean_param_array($order,PARAM_RAW,true);
$cmid = optional_param('cmid', 0, PARAM_INT);
$courseid = optional_param('courseid', SITEID, PARAM_INT);
$status = optional_param('status', '', PARAM_TEXT);
$courses = optional_param('filter_courses', $courseid, PARAM_TEXT);
$cmid = optional_param('cmid', 0, PARAM_INT);
$courseid = optional_param('courseid', SITEID, PARAM_INT);
$userid = optional_param('userid', $USER->id, PARAM_INT);
$ls_startdate = optional_param('ls_startdate', '', PARAM_RAW);
$ls_enddate = optional_param('ls_enddate', '', PARAM_RAW);
$filters = optional_param('filters', '', PARAM_RAW);
$filters = json_decode($filters, true);
$basicparams = optional_param('basicparams', '', PARAM_RAW);
$basicparams = json_decode($basicparams, true);

// $reportclass = new stdClass();

$reportclass = (new block_learnerscript\local\ls)->create_reportclass($reportid, $reportclass);

$reportclass->cmid = $cmid;
$reportclass->status = $status;
$reportclass->userid = $userid;
$reportclass->ls_startdate = 0;
$reportclass->ls_enddate = time();
$reportclass->start = $start;
$reportclass->length = $length;
$reportclass->search = $search['value'];
$reportclass->params = array_merge($filters,$basicparams);
$reportclass->courseid = $reportclass->params['courseid'] > SITEID ? $reportclass->params['courseid'] : $reportclass->params['filter_courses'];
$reportclass->currentcourseid = $reportclass->courseid;
$reportclass->currentuser = $DB->get_record('user', array('id' => $userid));
// $reportclass->params = $basicparams;

if (!empty($filters['ls_fstartdate'])) {
    $reportclass->ls_startdate = $filters['ls_fstartdate'];
} else {
    $reportclass->ls_startdate = 0;
}
if (!empty($filters['ls_fstartdate'])) {
    $reportclass->ls_enddate = $filters['ls_fenddate'];
} else {
    $reportclass->ls_enddate = time();
}
$reportclass->ordercolumn = $ordercolumn;
$reportclass->reporttype = 'table';
$reportclass->create_report(null);
$data = array();
foreach ($reportclass->finalreport->table->data as $key => $value) {
    $data[$key] = array_values($value);
}
echo json_encode(
    array(
        "draw" => $draw,
        "recordsTotal" => $reportclass->totalrecords,
        "recordsFiltered" => $reportclass->totalrecords,
        "data" => $data
    )
);
exit;