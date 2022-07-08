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
 * @package   local_report_completion_monthly
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once(dirname(__FILE__) . '/../../config.php'); // Creates $PAGE.
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->dirroot.'/user/filters/lib.php');
require_once($CFG->dirroot.'/blocks/iomad_company_admin/lib.php');

$sort         = optional_param('sort', 'lastname', PARAM_ALPHA);
$dir          = optional_param('dir', 'ASC', PARAM_ALPHA);
$page         = optional_param('page', 0, PARAM_INT);
// How many per page.
$perpage      = optional_param('perpage', 30, PARAM_INT);
$search      = optional_param('search', '', PARAM_CLEAN);// Search string.
$departmentid = optional_param('deptid', 0, PARAM_INTEGER);
$courseid    = optional_param('courseid', 1, PARAM_INTEGER);
$fromraw = optional_param_array('compfromraw', null, PARAM_INT);
$toraw = optional_param_array('comptoraw', null, PARAM_INT);
$yearfrom = optional_param_array('fromarray', null, PARAM_INT);
$yearto = optional_param_array('toarray', null, PARAM_INT);

$params = array();

if ($sort) {
    $params['sort'] = $sort;
}
if ($dir) {
    $params['dir'] = $dir;
}
if ($page) {
    $params['page'] = $page;
}
if ($perpage) {
    $params['perpage'] = $perpage;
}
if ($search) {
    $params['search'] = $search;
}
if ($departmentid) {
    $params['deptid'] = $departmentid;
}
if ($courseid) {
    $params['courseid'] = $courseid;
}
if ($fromraw) {
    if (is_array($fromraw)) {
        $from = mktime(0, 0, 0, $fromraw['month'], $fromraw['day'], $fromraw['year']);
    } else {
        $from = $fromraw;
    }
    $params['from'] = $from;
    $params['fromraw[day]'] = $fromraw['day'];
    $params['fromraw[month]'] = $fromraw['month'];
    $params['fromraw[year]'] = $fromraw['year'];
    $params['fromraw[enabled]'] = $fromraw['enabled'];
} else {
    $from = null;
}

if ($toraw) {
    if (is_array($toraw)) {
        $to = mktime(0, 0, 0, $toraw['month'], $toraw['day'], $toraw['year']);
    } else {
        $to = $toraw;
    }
    $params['to'] = $to;
    $params['toraw[day]'] = $toraw['day'];
    $params['toraw[month]'] = $toraw['month'];
    $params['toraw[year]'] = $toraw['year'];
    $params['toraw[enabled]'] = $toraw['enabled'];
} else {
    if (!empty($from)) {
        $to = time();
        $params['to'] = $to;
    } else {
        $to = null;
    }
}

// Deal with any passed year parameters.
if (!empty($yearto)) {
    $compto = strtotime($yearto['yearto'] . "/01/01 + 1 year");
    $params['yearto'] = $yearto['yearto'];
    $params['yeartooptional'] = true;
} else if ($yearto = optional_param('yearto', null, PARAM_INT)) {
    $compto = strtotime($yearto . "/01/01 + 1 year");
    $params['yearto'] = $yearto;
    $params['yeartooptional'] = optional_param('yeartooptional', true, PARAM_BOOL);
}
if (!empty($yearfrom)) {
    $compfrom = strtotime($yearfrom['yearfrom'] . "/01/01");
    $params['yearfrom'] = $yearfrom['yearfrom'];
    $params['yearfromoptional'] = true;
} else if ($yearfrom = optional_param('yearfrom', null, PARAM_INT)) {
    $compfrom = strtotime($yearfrom . "/01/01 + 1 year");
    $params['yearfrom'] = $yearfrom;
    $params['yearfromoptional'] = optional_param('yearfromoptional', true, PARAM_BOOL);
}

$systemcontext = context_system::instance();
require_login(); // Adds to $PAGE, creates $output.
iomad::require_capability('local/report_completion_monthly:view', $systemcontext);

// Set the companyid
$companyid = iomad::get_my_companyid($systemcontext);

// Correct the navbar.
// Set the name for the page.
$linktext = get_string('pluginname', 'local_report_completion_monthly');

// Set the url.
$linkurl = new moodle_url('/local/report_completion_monthly/index.php');

// Print the page header.
$PAGE->set_context($systemcontext);
$PAGE->set_url($linkurl);
$PAGE->set_pagelayout('report');
$PAGE->set_title($linktext);

// Set the page heading.
$PAGE->set_heading($linktext);
if (iomad::has_capability('local/report_completion:view', $systemcontext)) {
    $buttoncaption = get_string('pluginname', 'local_report_completion');
    $buttonlink = new moodle_url($CFG->wwwroot . "/local/report_completion/index.php");
    $buttons = $OUTPUT->single_button($buttonlink, $buttoncaption, 'get');
    $PAGE->set_button($buttons);
}
$PAGE->navbar->add($linktext, $linkurl);

// Get the renderer.
$output = $PAGE->get_renderer('block_iomad_company_admin');

// Javascript for fancy select.
// Parameter is name of proper select form element followed by 1=submit its form
$PAGE->requires->js_call_amd('block_iomad_company_admin/department_select', 'init', array('deptid', 1, optional_param('deptid', 0, PARAM_INT)));

echo $output->header();

// Check the department is valid.
if (!empty($departmentid) && !company::check_valid_department($companyid, $departmentid)) {
    print_error('invaliddepartment', 'block_iomad_company_admin');
}

// Get the associated department id.
$company = new company($companyid);
$parentlevel = company::get_company_parentnode($company->id);
$companydepartment = $parentlevel->id;

// Get the company additional optional user parameter names.
$fieldnames = array();
if ($category = company::get_category($companyid)) {
    // Get field names from company category.
    if ($fields = $DB->get_records('user_info_field', array('categoryid' => $category->id))) {
        foreach ($fields as $field) {
            $fieldnames[$field->id] = 'profile_field_'.$field->shortname;
            ${'profile_field_'.$field->shortname} = optional_param('profile_field_'.
                                                      $field->shortname, null, PARAM_RAW);
        }
    }
}
if ($categories = $DB->get_records_sql("SELECT id FROM {user_info_category}
                                                WHERE id NOT IN (
                                                 SELECT profileid FROM {company})")) {
    foreach ($categories as $category) {
        if ($fields = $DB->get_records('user_info_field', array('categoryid' => $category->id))) {
            foreach ($fields as $field) {
                $fieldnames[$field->id] = 'profile_field_'.$field->shortname;
                ${'profile_field_'.$field->shortname} = optional_param('profile_field_'.
                                                          $field->shortname, null, PARAM_RAW);
            }
        }
    }
}

// Deal with the user optional profile search.
$urlparams = $params;
$baseurl = new moodle_url(basename(__FILE__), $urlparams);
$returnurl = $baseurl;

// Work out where the user sits in the company department tree.
if (\iomad::has_capability('block/iomad_company_admin:edit_all_departments', \context_system::instance())) {
    $userlevels = array($parentlevel->id => $parentlevel->id);
} else {
    $userlevels = $company->get_userlevel($USER);
}
$userhierarchylevel = key($userlevels);
if ($departmentid == 0 ) {
    $departmentid = $userhierarchylevel;
}

// Get the company additional optional user parameter names.
$foundobj = iomad::add_user_filter_params($params, $companyid);
$idlist = $foundobj->idlist;
$foundfields = $foundobj->foundfields;

// Set up the user search parameters.
if ($courseid == 1) {
    $searchinfo = iomad::get_user_sqlsearch($params, $idlist, $sort, $dir, $departmentid, true, true);
} else {
    $searchinfo = iomad::get_user_sqlsearch($params, $idlist, $sort, $dir, $departmentid, false, false);
}

$companycourselist = $company->get_menu_courses(true, false, false, false);
$courselist = array(1 => get_string('all')) + $companycourselist;

$selectparams = $params;
$selecturl = new moodle_url('/local/report_completion_monthly/index.php', $selectparams);
$select = new single_select($selecturl, 'courseid', $courselist, $courseid);
$select->label = get_string('course');
$select->formid = 'shoosecourse';
$courseselectoutput = html_writer::tag('div', $output->render($select), array('id' => 'iomad_course_selector'));

// Set up the filter form.
$params['yearonly'] = true;
$mform = new iomad_date_filter_form($baseurl, $params);
$mform->set_data(array('departmentid' => $departmentid));
$options = $params;
$options['compfromraw'] = $from;
$options['comptoraw'] = $to;
$mform->set_data($options);
$mform->get_data();

// Display the tree selector thing.
echo $output->display_tree_selector($company, $parentlevel, $linkurl, $params, $departmentid);
echo html_writer::start_tag('div', array('class' => 'iomadclear', 'style' => 'padding-top: 5px;'));

echo html_writer::start_tag('div', array('class' => 'iomadclear controlitems'));

if (empty($courselist)) {
echo html_writer::end_tag('div');
    echo get_string('nocourses', 'block_iomad_company_admin');
    echo $output->footer();
    die;
}

// Display the course selector.
echo $courseselectoutput;

// Display the user filter form.
$mform->display();
echo html_writer::end_tag('div');
echo html_writer::end_tag('div');

if (empty($CFG->loginhttps)) {
    $securewwwroot = $CFG->wwwroot;
} else {
    $securewwwroot = str_replace('http:', 'https:', $CFG->wwwroot);
}

$returnurl = $CFG->wwwroot."/local/report_completion_monthly/index.php";

// Deal with where we are on the department tree.
$currentdepartment = company::get_departmentbyid($departmentid);
$showdepartments = company::get_subdepartments_list($currentdepartment);
$showdepartments[$departmentid] = $departmentid;
$departmentsql = " AND cu.departmentid IN (" . implode(',', array_keys($showdepartments)) . ")";

// all companies?
if ($parentslist = $company->get_parent_companies_recursive()) {
    $companysql = " AND u.id NOT IN (
                    SELECT userid FROM {company_users}
                    WHERE companyid IN (" . implode(',', array_keys($parentslist)) ."))";
} else {
    $companysql = "";
}

// All courses or just the one?
if ($courseid != 1) {
    $coursesql = " AND lit.courseid = :courseid AND lit.courseid IN (" . join(',', array_keys($company->get_menu_courses(true))) . ") ";
} else {
    $coursesql = " AND lit.courseid IN (" . join(',', array_keys($company->get_menu_courses(true))) . ") ";
}

// Deal with completion times.
$timesql = "";
if (!empty($compfrom)) {
    $timesql .= " AND lit.timecompleted > :compfrom ";
    $searchinfo->searchparams['compfrom'] = $compfrom;
}
if (!empty($compto)) {
    $timesql .= " AND lit.timecompleted < :compto ";
    $searchinfo->searchparams['compto'] = $compto;
}

// Set up the initial SQL for the form.
$selectsql = "DISTINCT lit.id,lit.timecompleted";
$fromsql = "{user} u JOIN {local_iomad_track} lit ON (u.id = lit.userid) JOIN {company_users} cu ON (u.id = cu.userid AND lit.userid = cu.userid AND lit.companyid = cu.companyid)";
$wheresql = $searchinfo->sqlsearch . " AND cu.companyid = :companyid AND lit.timecompleted IS NOT NULL $departmentsql $companysql $coursesql $timesql";
$sqlparams = array('companyid' => $companyid, 'courseid' => $courseid) + $searchinfo->searchparams;

// Get the full list of completions.
$results = $DB->get_records_sql("SELECT $selectsql FROM $fromsql WHERE $wheresql", $sqlparams);

// Set up some defaults.
$seriesarray = array();
// Get the calendar type used - see MDL-18375.
$calendartype = \core_calendar\type_factory::get_calendar_instance();
$dateformat = $calendartype->get_date_order();
$montharray = array("01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12");
$monthstringarray = array_values($dateformat['month']);

// Work through the results.
foreach ($results as $result) {
    $year = date('Y', $result->timecompleted);
    $month = date('m', $result->timecompleted);
    $both = date('Y-m', $result->timecompleted);

    // Do we have something recorded for this month?
    if (empty($seriesarray[$year])) {
        //if not set it up.
        $seriesarray[$year] = array();
    }
    // If there isn't already an entry it becomes == 1.
    if (empty($seriesarray[$year][$month])) {
        $seriesarray[$year][$month] = 1;
    } else {
        // Or we add one to it.
        $seriesarray[$year][$month]++;
    }
}
// sort this by date.
ksort($seriesarray);

// Create any missing months as the chart needs them..
foreach (array_keys($seriesarray) as $year) {
    foreach($montharray as $month) {
        if (empty($seriesarray[$year][$month])) {
            $seriesarray[$year][$month] = 0;
        }
    }
    ksort($seriesarray[$year]);
}

// Create the chart and all the series data for it.
$chart = new core\chart_bar();
foreach ($seriesarray as $year => $values) {
        $series = new core\chart_series("$year (".array_sum($values) . ")", array_values($values));
        $chart->add_series($series);
}
$chart->set_labels($monthstringarray);

// Display the chart.
echo $output->render($chart);
echo $output->footer();
