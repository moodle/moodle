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

require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->dirroot.'/user/filters/lib.php');
require_once($CFG->dirroot.'/blocks/iomad_company_admin/lib.php');
require_once(dirname(__FILE__).'/report_user_license_allocations_table.php');
require_once( dirname(__FILE__).'/lib.php');

$firstname       = optional_param('firstname', 0, PARAM_CLEAN);
$lastname      = optional_param('lastname', '', PARAM_CLEAN);
$showsuspended  = optional_param('showsuspended', 0, PARAM_INT);
$email  = optional_param('email', 0, PARAM_CLEAN);
$sort         = optional_param('sort', 'lastname', PARAM_ALPHA);
$dir          = optional_param('dir', 'ASC', PARAM_ALPHA);
$page         = optional_param('page', 0, PARAM_INT);
// How many per page.
$perpage      = optional_param('perpage', $CFG->iomad_max_list_users, PARAM_INT);
// Id of user to tweak mnet ACL (requires $access).
$acl          = optional_param('acl', '0', PARAM_INT);
$search      = optional_param('search', '', PARAM_CLEAN);// Search string.
$departmentid = optional_param('departmentid', 0, PARAM_INTEGER);
$licenseid    = optional_param('licenseid', 0, PARAM_INTEGER);
$download  = optional_param('download', '', PARAM_CLEAN);
$licenseallocatedfromraw = optional_param_array('licenseallocatedfrom', null, PARAM_INT);
$licenseallocatedtoraw = optional_param_array('licenseallocatedto', null, PARAM_INT);
$licenseunallocatedfromraw = optional_param_array('licenseunallocatedfrom', null, PARAM_INT);
$licenseunallocatedtoraw = optional_param_array('licenseunallocatedto', null, PARAM_INT);
$licenseusage = optional_param('licenseusage', 0, PARAM_INTEGER);

$params = array();

if ($firstname) {
    $params['firstname'] = $firstname;
}
if ($lastname) {
    $params['lastname'] = $lastname;
}
if ($email) {
    $params['email'] = $email;
}
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
    $params['departmentid'] = $departmentid;
}
if ($showsuspended) {
    $params['showsuspended'] = $showsuspended;
}
if ($licenseid) {
    $params['licenseid'] = $licenseid;
}
if ($licenseusage) {
    $params['licenseusage'] = $licenseusage;
}

if ($licenseallocatedfromraw) {
    if (is_array($licenseallocatedfromraw)) {
        $licenseallocatedfrom = mktime(0, 0, 0, $licenseallocatedfromraw['month'], $licenseallocatedfromraw['day'], $licenseallocatedfromraw['year']);
    } else {
        $licenseallocatedfrom = $licenseallocatedfromraw;
    }
    $params['licenseallocatedfrom'] = $licenseallocatedfrom;
} else {
    $licenseallocatedfrom = null;
}

if ($licenseallocatedtoraw) {
    if (is_array($licenseallocatedtoraw)) {
        $licenseallocatedto = mktime(0, 0, 0, $licenseallocatedtoraw['month'], $licenseallocatedtoraw['day'], $licenseallocatedtoraw['year']);
    } else {
        $licenseallocatedto = $licenseallocatedtoraw;
    }
    $params['licenseallocatedto'] = $licenseallocatedto;
} else {
    $licenseallocatedto = null;
}

if ($licenseunallocatedfromraw) {
    if (is_array($licenseunallocatedfromraw)) {
        $licenseunallocatedfrom = mktime(0, 0, 0, $licenseunallocatedfromraw['month'], $licenseunallocatedfromraw['day'], $licenseunallocatedfromraw['year']);
    } else {
        $licenseunallocatedfrom = $licenseunallocatedfromraw;
    }
    $params['licenseunallocatedfrom'] = $licenseunallocatedfrom;
} else {
    $licenseunallocatedfrom = null;
}

if ($licenseunallocatedtoraw) {
    if (is_array($licenseunallocatedtoraw)) {
        $licenseunallocatedto = mktime(0, 0, 0, $licenseunallocatedtoraw['month'], $licenseunallocatedtoraw['day'], $licenseunallocatedtoraw['year']);
    } else {
        $licenseunallocatedto = $licenseunallocatedtoraw;
    }
    $params['licenseunallocatedto'] = $licenseunallocatedto;
} else {
    $licenseunallocatedto = null;
}

$systemcontext = context_system::instance();
require_login(); // Adds to $PAGE, creates $output.
iomad::require_capability('local/report_user_license_allocations:view', $systemcontext);

// Set the companyid
$companyid = iomad::get_my_companyid($systemcontext);
$company = new company($companyid);
// Get the associated department id.
$parentlevel = company::get_company_parentnode($company->id);
$companydepartment = $parentlevel->id;

// all companies?
if ($parentslist = $company->get_parent_companies_recursive()) {
    $companysql = " AND u.id NOT IN (
                    SELECT userid FROM {company_users}
                    WHERE companyid IN (" . implode(',', array_keys($parentslist)) ."))";
} else {
    $companysql = "";
}

// Correct the navbar.
// Set the name for the page.
$linktext = get_string('report_user_license_allocations_title', 'local_report_user_license_allocations');

// Set the url.
$linkurl = new moodle_url('/local/report_user_license_allocations/index.php');

// Print the page header.
$PAGE->set_context($systemcontext);
$PAGE->set_url($linkurl);
$PAGE->set_pagelayout('report');
$PAGE->set_title($linktext);

// Set the page heading.
$PAGE->set_heading(get_string('pluginname', 'block_iomad_reports') . " - $linktext");
$PAGE->navbar->add(get_string('dashboard', 'block_iomad_company_admin'));
$PAGE->navbar->add($linktext, $linkurl);

// Get the renderer.
$output = $PAGE->get_renderer('block_iomad_company_admin');

// Javascript for fancy select.
// Parameter is name of proper select form element followed by 1=submit its form
$PAGE->requires->js_call_amd('block_iomad_company_admin/department_select', 'init', array('departmentid', 1, optional_param('departmentid', 0, PARAM_INT)));

// Check the department is valid.
if (!empty($departmentid) && !company::check_valid_department($companyid, $departmentid)) {
    print_error('invaliddepartment', 'block_iomad_company_admin');
}

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
$idlist = array();
$foundfields = false;
if (!empty($fieldnames)) {
    $fieldids = array();
    foreach ($fieldnames as $id => $fieldname) {
        $paramarray = array();
        if ($fields[$id]->datatype == "menu" ) {
            $paramarray = explode("\n", $fields[$id]->param1);
            if (!empty($paramarray[${$fieldname}])) {
                ${$fieldname} = $paramarray[${$fieldname}];
            }
        }
        if (!empty(${$fieldname}) ) {
            $idlist[0] = "We found no one";
            $fieldsql = $DB->sql_compare_text('data')." LIKE '%".${$fieldname}."%'
                                                        AND fieldid = $id";
            if ($idfields = $DB->get_records_sql("SELECT userid FROM {user_info_data}
                                                  WHERE $fieldsql")) {
                $fieldids[] = $idfields;
            }
            if (!empty($paramarray)) {
                $params[$fieldname] = array_search(${$fieldname}, $paramarray);
                $urlparams[$fieldname] = array_search(${$fieldname}, $paramarray);
            } else {
                if (!is_array(${$fieldname})) {
                    $params[$fieldname] = ${$fieldname};
                    $urlparams[$fieldname] = ${$fieldname};
                } else {
                    $params[$fieldname] = ${$fieldname};
                    $urlparams[$fieldname] = serialize(${$fieldname});
                }
            }
        }
    }
    if (!empty($fieldids)) {
        $foundfields = true;
        $idlist = array_pop($fieldids);
        if (!empty($fieldids)) {
            foreach ($fieldids as $fieldid) {
                $idlist = array_intersect_key($idlist, $fieldid);
                if (empty($idlist)) {
                    break;
                }
            }
        }
    }
}

$baseurl = new moodle_url(basename(__FILE__), $urlparams);
$returnurl = $baseurl;

if (iomad::has_capability('block/iomad_company_admin:edit_all_departments', $systemcontext) ||
    !empty($SESSION->currenteditingcompany)) {
    $userhierarchylevel = $parentlevel->id;
} else {
    $userlevel = $company->get_userlevel($USER);
    $userhierarchylevel = $userlevel->id;
}
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
$selecturl = new moodle_url('/local/report_user_license_allocations/index.php', $selectparams);
$select = new single_select($selecturl, 'licenseid', $licenselist, $licenseid);
$select->label = get_string('licenseselect', 'block_iomad_company_admin');
$select->formid = 'chooselicense';
$licenseselectoutput = html_writer::tag('div', $output->render($select), array('id' => 'iomad_department_selector'));

// Get the appropriate list of departments.
$subhierarchieslist = company::get_all_subdepartments($userhierarchylevel);
$select = new single_select($baseurl, 'departmentid', $subhierarchieslist, $departmentid);
$select->label = get_string('department', 'block_iomad_company_admin');
$select->formid = 'choosedepartment';
$fwselectoutput = html_writer::tag('div', $output->render($select), array('id' => 'iomad_department_selector'));

$departmenttree = company::get_all_subdepartments_raw($userhierarchylevel);
$treehtml = $output->department_tree($departmenttree, optional_param('departmentid', 0, PARAM_INT));

$searchinfo = iomad::get_user_sqlsearch($params, $idlist, $sort, $dir, $departmentid, true, true);

// Set up the table.
$table = new local_report_user_license_allocations_table('user_report_license_allocations');
$table->is_downloading($download, 'user_report_license_allocations', 'user_report_license_allocations123');

if (!$table->is_downloading()) {
    echo $output->header();
    // Display the search form and department picker.

    // Throw an error if we don't have any licenses.
    if (empty($licenselist)) {
        echo get_string('nolicenses', 'block_iomad_company_admin');
        echo $output->footer();
        die;
    }
    // Display the license selector and other control forms.
    if (!empty($companyid)) {
        if (empty($table->is_downloading())) {
            echo $licenseselectoutput;
            echo html_writer::start_tag('div', array('class' => 'iomadclear'));
            echo html_writer::start_tag('div', array('class' => 'fitem'));
            echo $treehtml;
            echo html_writer::start_tag('div', array('style' => 'display:none'));
            echo $fwselectoutput;
            echo html_writer::end_tag('div');
            echo html_writer::end_tag('div');
            echo html_writer::end_tag('div');

            // Set up the filter form.
            $params['companyid'] = $companyid;
            $params['addlicenseusage'] = true;
            $params['addfrom'] = 'licenseallocatedfrom';
            $params['addto'] = 'licenseallocatedto';
            $params['addfromb'] = 'licenseunallocatedfrom';
            $params['addtob'] = 'licenseunallocatedto';
            $mform = new iomad_user_filter_form(null, $params);
            $mform->set_data(array('departmentid' => $departmentid));
            $mform->set_data($params);
            $mform->get_data();

            // Display the user filter form.
            $mform->display();
        }
    }
}

$stredit   = get_string('edit');
$returnurl = $CFG->wwwroot."/local/report_user_license_allocations/index.php";

// Do we have any additional reporting fields?
$extrafields = array();
if (!empty($CFG->iomad_report_fields)) {
    foreach (explode(',', $CFG->iomad_report_fields) as $extrafield) {
        $extrafields[$extrafield] = new stdclass();
        $extrafields[$extrafield]->name = $extrafield;
        if (strpos($extrafield, 'profile_field') !== false) {
            // Its an optional profile field.
            $profilefield = $DB->get_record('user_info_field', array('shortname' => str_replace('profile_field_', '', $extrafield)));
            $extrafields[$extrafield]->title = $profilefield->name;
            $extrafields[$extrafield]->fieldid = $profilefield->id;
        } else {
            $extrafields[$extrafield]->title = get_string($extrafield);
        }
    }
}

// Get the license information.
$license = $DB->get_record('companylicense', array('id' => $licenseid));

// Deal with where we are on the department tree.
$currentdepartment = company::get_departmentbyid($departmentid);
$showdepartments = company::get_subdepartments_list($currentdepartment);
$showdepartments[$departmentid] = $departmentid;
$departmentsql = " AND d.id IN (" . implode(',', array_keys($showdepartments)) . ")";

// Set up the initial SQL for the form.
$selectsql = "DISTINCT u.id,u.firstname,u.lastname,d.name AS department,u.email,urla.action AS action, urla.licenseid";
$fromsql = "{user} u JOIN {company_users} cu ON (u.id = cu.userid) JOIN {department} d ON (cu.departmentid = d.id) LEFT JOIN {local_report_user_lic_allocs} urla ON (u.id = urla.userid) ";
$wheresql = $searchinfo->sqlsearch . " AND cu.companyid = :companyid $departmentsql $companysql AND urla.action = 1";
$sqlparams = array('companyid' => $companyid) + $searchinfo->searchparams;

// Set up the headers for the form.
$headers = array(get_string('firstname'),
                 get_string('lastname'),
                 get_string('department', 'block_iomad_company_admin'),
                 get_string('email'));

$columns = array('firstname',
                    'lastname',
                    'department',
                    'email');

// Deal with optional report fields.
if (!empty($extrafields)) {
    foreach ($extrafields as $extrafield) {
        $headers[] = $extrafield->title;
        $columns[] = $extrafield->name;
        if (!empty($extrafield->fieldid)) {
            // Its a profile field.
            // Skip it this time as these may not have data.
        } else {
            $selectsql .= ", u." . $extrafield->name;
        }
    }
    foreach ($extrafields as $extrafield) {
        if (!empty($extrafield->fieldid)) {
            // Its a profile field.
            $selectsql .= ", P" . $extrafield->fieldid . ".data AS " . $extrafield->name;
            $fromsql .= " LEFT JOIN {user_info_data} P" . $extrafield->fieldid . " ON (u.id = P" . $extrafield->fieldid . ".userid )";
        }
    }
}

// And final the rest of the form headers.
$headers[] = get_string('licenseallocated', 'local_report_user_license_allocations');
$headers[] = get_string('dateallocated', 'local_report_user_license_allocations');
$headers[] = get_string('dateunallocated', 'local_report_user_license_allocations');
$headers[] = get_string('totalallocate', 'local_report_user_license_allocations');
$headers[] = get_string('totalunallocate', 'local_report_user_license_allocations');

$columns[] = 'licenseallocated';
$columns[] = 'dateallocated';
$columns[] = 'dateunallocated';
$columns[] = 'numallocations';
$columns[] = 'numunallocations';
$table->no_sorting('licenseallocated');
$table->no_sorting('dateallocated');
$table->no_sorting('dateunallocated');
$table->no_sorting('numallocations');
$table->no_sorting('numunallocations');

$table->set_sql($selectsql, $fromsql, $wheresql, $sqlparams);
$table->define_baseurl($linkurl);
$table->define_columns($columns);
$table->define_headers($headers);
$table->out($CFG->iomad_max_list_users, true);

if (!$table->is_downloading()) {
    echo $output->footer();
}