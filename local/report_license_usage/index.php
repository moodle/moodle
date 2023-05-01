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
 * @package   local_report_license_usage
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
$licenseid    = optional_param('licenseid', 0, PARAM_INTEGER);
$fromraw = optional_param_array('compfromraw', null, PARAM_INT);
$toraw = optional_param_array('comptoraw', null, PARAM_INT);

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
if ($licenseid) {
    $params['licenseid'] = $licenseid;
}
if ($fromraw) {
    if (is_array($fromraw)) {
        $from = mktime(0, 0, 0, $fromraw['month'], $fromraw['day'], $fromraw['year']);
    } else {
        $from = $fromraw;
    }
    $params['from'] = $from;
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
} else {
    if (!empty($from)) {
        $to = time();
        $params['to'] = $to;
    } else {
        $to = null;
    }
}

$systemcontext = context_system::instance();
require_login(); // Adds to $PAGE, creates $output.
iomad::require_capability('local/report_license_usage:view', $systemcontext);

// Set the companyid
$companyid = iomad::get_my_companyid($systemcontext);

// Correct the navbar.
// Set the name for the page.
$linktext = get_string('report_license_usage_title', 'local_report_license_usage');

// Set the url.
$linkurl = new moodle_url('/local/report_license_usage/index.php');

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

// Get the appropriate list of licenses.
$licenselist = array();
$licenses = $DB->get_records('companylicense', array('companyid' => $companyid), 'expirydate DESC', 'id,name,startdate,expirydate');
foreach ($licenses as $license) {
    if ($license->expirydate < time()) {
        $licenselist[$license->id] = $license->name . " (" . get_string('licenseexpired', 'block_iomad_company_admin', date($CFG->iomad_date_format, $license->expirydate)) . ")";
    } else if ($license->startdate > time()) {
        $licenselist[$license->id] = $license->name . " (" . get_string('licensevalidfrom', 'block_iomad_company_admin', date($CFG->iomad_date_format, $license->startdate)) . ")";
    } else {
        $licenselist[$license->id] = $license->name;
    }
}

$selectparams = $params;
$selecturl = new moodle_url('/local/report_license_usage/index.php', $selectparams);
$select = new single_select($selecturl, 'licenseid', $licenselist, $licenseid);
$select->label = get_string('licenseselect', 'block_iomad_company_admin');
$select->formid = 'chooselicense';
$licenseselectoutput = html_writer::tag('div', $output->render($select), array('id' => 'iomad_license_selector'));

// Set up the filter form.
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
echo html_writer::start_tag('div', array('class' => 'reportform', 'style' => 'display: inline-flex;'));

if (empty($licenselist)) {
    echo html_writer::end_tag('div');
    echo get_string('nolicenses', 'block_iomad_company_admin');
    echo $output->footer();
    die;
}

echo $licenseselectoutput;
if (empty($licenseid)) {
    echo html_writer::end_tag('div');
    echo html_writer::end_tag('div');
    echo $output->footer();
    die;
}

// Display the user filter form.
$mform->display();
echo html_writer::end_tag('div');
echo html_writer::end_tag('div');

if (empty($CFG->loginhttps)) {
    $securewwwroot = $CFG->wwwroot;
} else {
    $securewwwroot = str_replace('http:', 'https:', $CFG->wwwroot);
}

$returnurl = $CFG->wwwroot."/local/report_license_usage/index.php";

// Get the license information.
$license = $DB->get_record('companylicense', array('id' => $licenseid));

// Get the full company tree as we may need it.
$topcompanyid = $company->get_topcompanyid();
$topcompany = new company($topcompanyid);
$companytree = $topcompany->get_child_companies_recursive();
$parentcompanies = $company->get_parent_companies_recursive();

// Deal with parent company managers
if (!empty($parentcompanies)) {
    $userfilter = " AND id NOT IN (
                     SELECT userid FROM {company_users}
                     WHERE companyid IN (" . implode(',', array_keys($parentcompanies)) . "))";
    $userfilterwithu = " AND u.id NOT IN (
                         SELECT userid FROM {company_users}
                         WHERE companyid IN (" . implode(',', array_keys($parentcompanies)) . "))";
} else {
    $userfilter = "";
    $userfilterwithu = "";
}

// Get all or company users depending on capability.
$dbsort = "";

// Make sure we dont display site admins.
// Set default search to something which cant happen.
$sqlsearch = "id!='-1' AND id NOT IN (" . $CFG->siteadmins . ") $userfilter";

// Get department users.
$departmentusers = company::get_recursive_department_users($departmentid);
if ( count($departmentusers) > 0 ) {
    $departmentids = "";
    foreach ($departmentusers as $departmentuser) {
        if (!empty($departmentids)) {
            $departmentids .= ",".$departmentuser->userid;
        } else {
            $departmentids .= $departmentuser->userid;
        }
    }
    if (!empty($showsuspended)) {
        $sqlsearch .= " AND deleted <> 1 AND id in ($departmentids) ";
    } else {
        $sqlsearch .= " AND deleted <> 1 AND suspended = 0 AND id in ($departmentids) ";
    }
} else {
    $sqlsearch = "1 = 0";
}

// Get the user records.
$userrecords = $DB->get_fieldset_select('user', 'id', $sqlsearch);

// Check we havent looked and discounted everyone.
if (!empty($userrecords)) {
    // Get users company association.
    $departmentusers = company::get_recursive_department_users($departmentid);
    $sqlsearch = "id!='-1' $userfilter";
    if ( count($departmentusers) > 0 ) {
        $departmentids = "";
        foreach ($departmentusers as $departmentuser) {
            if (!empty($departmentids)) {
                $departmentids .= ",".$departmentuser->userid;
            } else {
                $departmentids .= $departmentuser->userid;
            }
        }
        if (!empty($showsuspended)) {
            $sqlsearch .= " AND deleted <> 1 AND id in ($departmentids) ";
        } else {
            $sqlsearch .= " AND deleted <> 1 AND suspended = 0 AND id in ($departmentids) ";
        }
    } else {
        $sqlsearch = "1 = 0";
    }
    // Deal with search strings.
    $userrecords = $DB->get_fieldset_select('user', 'id', $sqlsearch . $userfilter);
} else {
    $userrecords = array();
}

$userlist = "";
if (!empty($userrecords)) {
    $userlist = " u.id in (". implode(',', array_values($userrecords)).") ";
}
// Set the chart defaults
$numstart = 0;
$net = 0;
$numunallocations = 0;
$numallocations = 0;
$total = 0;
if (!empty($userlist)) {
    if (!empty($from)) {
        // We need to get the total allocated up to that date.
        if (empty($license->program)) {
            $numallocations = $DB->count_records_sql("SELECT COUNT(id) FROM {local_report_user_lic_allocs}
                                                      WHERE action = 1
                                                      AND licenseid = :licenseid
                                                      AND issuedate < :fromtime
                                                      AND userid IN (" . $departmentids . ")",
                                                      array('licenseid' => $licenseid,
                                                            'fromtime' => $from));
            $numunallocations = $DB->count_records_sql("SELECT COUNT(id) FROM {local_report_user_lic_allocs}
                                                        WHERE action = 0
                                                        AND licenseid = :licenseid
                                                        AND issuedate < :fromtime
                                                        AND userid IN (" . $departmentids . ")",
                                                        array('licenseid' => $licenseid,
                                                              'fromtime' => $from));
            $numstart = $numallocations - $numunallocations;
        } else {
            $coursecount = $DB->count_records('companylicense_courses', array('licenseid' => $licenseid));
            $allocations = $DB->get_records_sql("SELECT * FROM {local_report_user_lic_allocs}
                                                 WHERE action = 1
                                                 AND license = :licenseid
                                                 AND issuedate < :fromtime
                                                 AND userid IN (" . $departmentids . ")",
                                                 array('licenseid' => $licenseid,
                                                       'fromtime' => $from));

            $numallocations = $allocations / $coursecount;
            $unallocations = $DB->get_records_sql("SELECT * FROM {local_report_user_lic_allocs}
                                                   WHERE action = 0
                                                   AND licenseid = :licenseid
                                                   AND issuedate < :fromtime
                                                   AND userid IN (" . $departmentids . ")",
                                                   array('licenseid' => $licenseid,
                                                         'fromtime' => $from));

            $numunallocations = $unallocations / $coursecount;
            $numstart = $numallocations - $numunallocations;
        }
    } else {
        $numstart = 0;
    }
    $sqlparams = array('licenseid' => $licenseid);
    $timesql = "";
    if (!empty($from)) {
        $timesql = " AND issuedate > :from ";
        $sqlparams['from'] = $from;
    }
    if (!empty($to)) {
        $timesql .= " AND issuedate < :to ";
        $sqlparams['to'] = $to;
    }
    // Get the number of allocations.
    if (empty($license->program)) {
        $numallocations = $DB->count_records_sql("SELECT COUNT(id) FROM {local_report_user_lic_allocs}
                                                  WHERE action = 1
                                                  AND licenseid = :licenseid
                                                  $timesql
                                                  AND userid IN (" . $departmentids . ")",
                                                  $sqlparams);
        $numunallocations = $DB->count_records_sql("SELECT COUNT(id) FROM {local_report_user_lic_allocs}
                                                    WHERE action = 0
                                                    AND licenseid = :licenseid
                                                    $timesql
                                                    AND userid IN (" . $departmentids . ")",
                                                    $sqlparams);
    } else {
        $coursecount = $DB->count_records('companylicense_courses', array('licenseid' => $licenseid));
        $allocations = $DB->count_records_sql("SELECT count(id) FROM {local_report_user_lic_allocs}
                                             WHERE action = 1
                                             AND licenseid = :licenseid
                                             $timesql
                                             AND userid IN (" . $departmentids . ")",
                                             $sqlparams);
        $unallocations = $DB->count_records_sql("SELECT count(id) FROM {local_report_user_lic_allocs}
                                               WHERE action = 0
                                               AND licenseid = :licenseid
                                               $timesql
                                               AND userid IN (" . $departmentids . ")",
                                               $sqlparams);
        $numallocations = $allocations / $coursecount;
        $numunallocations = $unallocations / $coursecount;
    }
    $net = $numallocations - $numunallocations;
    $total = $numstart + $net;
}

// Display the current license overview.
$table = new html_table();
$table->id = 'LicenseOverviewTable';
$table->head = array (get_string('licensename', 'block_iomad_company_admin'),
                      get_string('licenseallocated', 'block_iomad_company_admin'),
                      get_string('licenses', 'block_iomad_company_admin'),
                      get_string('userlicenseused', 'block_iomad_company_admin'));
$table->align = array ("left", "center", "center", "center");
$licenseused = $DB->count_records('companylicense_users', array('licenseid' => $license->id, 'isusing' => 1));
if (!empty($license->program)) {
    $weighting = $DB->count_records('companylicense_courses', array('licenseid' => $licenseid));
} else {
    $weighting = 1;
}

$table->data[] = array('name' => $license->name, 'allocated' => $license->allocation / $weighting, 'remaining' => ($license->allocation - $license->used) / $weighting, 'used' => $licenseused / $weighting);

echo html_writer::table($table);

// Display the chart.
$startseries = new core\chart_series(get_string('numstart', 'local_report_license_usage'), [$numstart]);
$allocatedseries = new core\chart_series(get_string('totalallocate', 'local_report_license_usage'), [$numallocations]);
$unallocatedseries = new core\chart_series(get_string('totalunallocate', 'local_report_license_usage'), [$numunallocations]);
$netseries = new core\chart_series(get_string('numnet', 'local_report_license_usage'), [$net]);
$totalseries = new core\chart_series(get_string('numtotal', 'local_report_license_usage'), [$total]);
$chart = new core\chart_bar();
$chart->add_series($startseries);
$chart->add_series($allocatedseries);
$chart->add_series($unallocatedseries);
$chart->add_series($netseries);
$chart->add_series($totalseries);
$chart->set_labels([get_string('licenseuseage', 'block_iomad_company_admin')]);
echo $output->render($chart);

echo $output->footer();
