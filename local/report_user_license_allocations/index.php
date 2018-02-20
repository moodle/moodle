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

require_once('../../config.php');
require_once( dirname('__FILE__').'/lib.php');
require_once(dirname(__FILE__) . '/../../config.php'); // Creates $PAGE.
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->dirroot.'/user/filters/lib.php');
require_once($CFG->dirroot.'/blocks/iomad_company_admin/lib.php');

$firstname       = optional_param('firstname', 0, PARAM_CLEAN);
$lastname      = optional_param('lastname', '', PARAM_CLEAN);
$showsuspended  = optional_param('showsuspended', 0, PARAM_INT);
$email  = optional_param('email', 0, PARAM_CLEAN);
$sort         = optional_param('sort', 'name', PARAM_ALPHA);
$dir          = optional_param('dir', 'ASC', PARAM_ALPHA);
$page         = optional_param('page', 0, PARAM_INT);
// How many per page.
$perpage      = optional_param('perpage', 30, PARAM_INT);
// Id of user to tweak mnet ACL (requires $access).
$acl          = optional_param('acl', '0', PARAM_INT);
$search      = optional_param('search', '', PARAM_CLEAN);// Search string.
$departmentid = optional_param('departmentid', 0, PARAM_INTEGER);
$licenseid    = optional_param('licenseid', 0, PARAM_INTEGER);
$dodownload  = optional_param('dodownload', '', PARAM_CLEAN);
$licenseallocatedfromraw = optional_param_array('licenseallocatedfrom', null, PARAM_INT);
$licenseallocatedtoraw = optional_param_array('licenseallocatedto', null, PARAM_INT);
$licenseunallocatedfromraw = optional_param_array('licenseunallocatedfrom', null, PARAM_INT);
$licenseunallocatedtoraw = optional_param_array('licenseunallocatedto', null, PARAM_INT);
$licenseusage = optional_param('licenseusage', 0, PARAM_INTEGER);

$params = array();

if (!empty($dodownload)) {
    $page = 0;
    $perpage = 0;
}

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

// Correct the navbar.
// Set the name for the page.
$linktext = get_string('report_user_license_allocations_title', 'local_report_user_license_allocations');

// Set the url.
$linkurl = new moodle_url('/local/report_user_license_allocations/index.php');

// Print the page header.
$PAGE->set_context($systemcontext);
$PAGE->set_url($linkurl);
$PAGE->set_pagelayout('admin');
$PAGE->set_title($linktext);

// Set the page heading.
$PAGE->set_heading(get_string('pluginname', 'block_iomad_reports') . " - $linktext");

// Get the renderer.
$output = $PAGE->get_renderer('block_iomad_company_admin');

// Javascript for fancy select.
// Parameter is name of proper select form element followed by 1=submit its form
$PAGE->requires->js_call_amd('block_iomad_company_admin/department_select', 'init', array('departmentid', 1, optional_param('departmentid', 0, PARAM_INT)));

// Build the nav bar.
company_admin_fix_breadcrumb($PAGE, $linktext, $linkurl);

if (empty($dodownload)) {
    echo $output->header();
} else {
    // Set up the Excel workbook.
    header("Content-Type: application/download\n");
    header("Content-Disposition: attachment; filename=\"user_license_allocations.csv\"");
    header("Expires: 0");
    header("Cache-Control: must-revalidate,post-check=0,pre-check=0");
    header("Pragma: public");
}

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

// Set up the filter form.
$params['adddodownload'] = true;
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

if (empty($licenselist) && empty($dodownload)) {
    echo get_string('nolicenses', 'block_iomad_company_admin');
    echo $output->footer();
    die;
}

if (empty($dodownload)) {
    echo $licenseselectoutput;
}
if (empty($licenseid) && empty($dodownload)) {
    echo $output->footer();
    die;
}

if (empty($dodownload)) {
    // Display the tree selector thing.
    echo html_writer::start_tag('div', array('class' => 'iomadclear'));
    echo html_writer::start_tag('div', array('class' => 'fitem'));
    echo $treehtml;
    echo html_writer::start_tag('div', array('style' => 'display:none'));
    echo $fwselectoutput;
    echo html_writer::end_tag('div');
    echo html_writer::end_tag('div');
    echo html_writer::end_tag('div');
    echo html_writer::start_tag('div', array('class' => 'iomadclear', 'style' => 'padding-top: 5px;'));
    
    if (empty($licenseid)) {
        echo $output->footer();
        die;
    }
    
    // Display the user filter form.
    $mform->display();
    echo html_writer::end_tag('div');
}

$stredit   = get_string('edit');
$strdelete = get_string('delete');
$strdeletecheck = get_string('deletecheck');
$strshowallusers = get_string('showallusers');

if (empty($CFG->loginhttps)) {
    $securewwwroot = $CFG->wwwroot;
} else {
    $securewwwroot = str_replace('http:', 'https:', $CFG->wwwroot);
}

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
        } else {
            $extrafields[$extrafield]->title = get_string($extrafield);
        }
    }
}

// Get the license information.
$license = $DB->get_record('companylicense', array('id' => $licenseid));

// Carry on with the user listing.
$columns = array("firstname", "lastname", "email", "department", "licenseallocated", "dateallocated", "dateunallocated", "totalallocate", "totalunallocate");

foreach ($columns as $column) {
    if ($column == 'licenseallocated' ||$column == 'dateallocated' || $column == 'dateunallocated' ||  $column == 'totalallocate' ||  $column == 'totalunallocate') {
        $string[$column] = get_string("$column", 'local_report_user_license_allocations');
    } else {
        $string[$column] = get_string("$column");
    }
    if ($sort != $column) {
        $columnicon = "";
        $columndir = "ASC";
    } else {
        $columndir = $dir == "ASC" ? "DESC":"ASC";
        if ($column == "lastaccess") {
            $columnicon = $dir == "ASC" ? "up":"down";
        } else {
            $columnicon = $dir == "ASC" ? "down":"up";
        }
        $columnicon = " <img src=\"" . $output->pix_url('t/' . $columnicon) . "\" alt=\"\" />";

    }
    $$column = $string[$column].$columnicon;
}

if ($sort == "name") {
    $sort = "firstname";
}

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

// Check we havent looked and discounted everyone.
if ((empty($idlist) && !$foundfields) || (!empty($idlist) && $foundfields)) {
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

    // Deal with search strings..
    $searchparams = array();
    if (!empty($idlist)) {
        $sqlsearch .= " AND id in (".implode(',', array_keys($idlist)).") ";
    }
    if (!empty($params['firstname'])) {
        $sqlsearch .= " AND firstname like :firstname ";
        $searchparams['firstname'] = '%'.$params['firstname'].'%';
    }

    if (!empty($params['lastname'])) {
        $sqlsearch .= " AND lastname like :lastname ";
        $searchparams['lastname'] = '%'.$params['lastname'].'%';
    }

    if (!empty($params['email'])) {
        $sqlsearch .= " AND email like :email ";
        $searchparams['email'] = '%'.$params['email'].'%';
    }

    switch($sort) {
        case "firstname":
            $sqlsearch .= " order by firstname $dir ";
        break;
        case "lastname":
            $sqlsearch .= " order by lastname $dir ";
        break;
        case "email":
            $sqlsearch .= " order by email $dir ";
        break;
    }

    // Get the user records.
    $userrecords = $DB->get_fieldset_select('user', 'id', $sqlsearch, $searchparams);
} else {
    $userrecords = array();
}

// Check we havent looked and discounted everyone.
if ((empty($idlist) && !$foundfields) || (!empty($idlist) && $foundfields)) {
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
    $searchparams = array();
    if (!empty($idlist)) {
        $sqlsearch .= " AND id in (".implode(',', array_keys($idlist)).") ";
    }
    if (!empty($params['firstname'])) {
        $sqlsearch .= " AND firstname like :firstname ";
        $searchparams['firstname'] = '%'.$params['firstname'].'%';
    }

    if (!empty($params['lastname'])) {
        $sqlsearch .= " AND lastname like :lastname ";
        $searchparams['lastname'] = '%'.$params['lastname'].'%';
    }

    if (!empty($params['email'])) {
        $sqlsearch .= " AND email like :email ";
        $searchparams['email'] = '%'.$params['email'].'%';
    }

    switch($sort) {
        case "name":
            $sqlsearch .= " order by firstname $dir ";
            $dbsort = " order by u.firstname $dir ";
        break;
        case "lastname":
            $sqlsearch .= " order by lastname $dir ";
            $dbsort = " order by u.lastname $dir ";
        break;
        case "email":
            $sqlsearch .= " order by email $dir ";
            $dbsort = " order by u.email $dir ";
        break;
        case "department":
            $dbsort = " order by d.name $dir ";
        break;
    }

    $userrecords = $DB->get_fieldset_select('user', 'id', $sqlsearch . $userfilter, $searchparams);
} else {
    $userrecords = array();
}

$userlist = "";
if (!empty($userrecords)) {
    $userlist = " u.id in (". implode(',', array_values($userrecords)).") ";
}

if (!empty($userlist)) {

    // set up the sql parameter array.
    $sqlparams = array('companyid' => $company->id);

    // Check if we have anything for license user allocations.
    if ($licenseusage == 1) {
        $usagesql = " AND u.id NOT IN (
                      SELECT userid FROM {companylicense_users}
                      WHERE licenseid = :licenseid) ";
        $sqlparams['licenseid'] = $licenseid;
    } else if ($licenseusage == 2) {
        $usagesql = " AND u.id IN (
                      SELECT userid FROM {companylicense_users}
                      WHERE licenseid = :licenseid) ";
        $sqlparams['licenseid'] = $licenseid;
    } else {
        $usagesql = "";
    }

    // Check if we have anything for license allocation from date.
    $userallocationsql = "";
    if ($licenseallocatedfrom) {
echo "Licenseallocatedfrom = $licenseallocatedfrom </br>";
echo "Licenseallocatedfrom = $licenseallocatedfrom </br>";
        $userallocationsql .= " AND u.id IN (
                                SELECT userid FROM {logstore_standard_log}
                                WHERE eventname = :afeventname
                                AND objectid = :aflicenseid
                                AND timecreated > :afdate) ";
        $sqlparams['afeventname'] = '\block_iomad_company_admin\event\user_license_assigned';
        $sqlparams['aflicenseid'] = $licenseid;
        $sqlparams['afdate'] = $licenseallocatedfrom;
    }

    if ($licenseallocatedto) {
        $userallocationsql .= " AND u.id IN (
                                SELECT userid FROM {logstore_standard_log}
                                WHERE eventname = :ateventname
                                AND objectid = :atlicenseid
                                AND timecreated < :atdate) ";
        $sqlparams['ateventname'] = '\block_iomad_company_admin\event\user_license_assigned';
        $sqlparams['atlicenseid'] = $licenseid;
        $sqlparams['atdate'] = $licenseallocatedto;
    }

    if ($licenseunallocatedfrom) {
        $userallocationsql .= " AND u.id IN (
                                SELECT userid FROM {logstore_standard_log}
                                WHERE eventname = :ufeventname
                                AND objectid = :uflicenseid
                                AND timecreated > :ufdate) ";
        $sqlparams['ufeventname'] = '\block_iomad_company_admin\event\user_license_unassigned';
        $sqlparams['uflicenseid'] = $licenseid;
        $sqlparams['ufdate'] = $licenseunallocatedfrom;
    }

    if ($licenseunallocatedto) {
        $userallocationsql .= " AND u.id IN (
                                SELECT userid FROM {logstore_standard_log}
                                WHERE eventname = :uteventname
                                AND objectid = :utlicenseid
                                AND timecreated < :utdate) ";
        $sqlparams['uteventname'] = '\block_iomad_company_admin\event\user_license_unassigned';
        $sqlparams['utlicenseid'] = $licenseid;
        $sqlparams['utdate'] = $licenseunallocatedto;
    }

    $users = $DB->get_records_sql("SELECT u.id as id,
                                          u.username as username,
                                          u.email as email,
                                          u.firstname as firstname,
                                          u.lastname as lastname,
                                          u.alternatename as alternatename,
                                          u.firstnamephonetic as firstnamephonetic,
                                          u.lastnamephonetic as lastnamephonetic,
                                          u.middlename as middlename,
                                          u.city as city,
                                          u.country as country,
                                          u.suspended as suspended,
                                          d.name as departmentname
                                   FROM {user} u, {department} d, {company_users} cu
                                   WHERE u.deleted <> 1 AND $userlist
                                   $usagesql
                                   $userallocationsql
                                   AND cu.userid = u.id AND cu.departmentid = d.id
                                   AND cu.companyid = :companyid
                                   $userfilterwithu
                                   GROUP BY u.id, d.name $dbsort ", $sqlparams, $page * $perpage, $perpage);
    $allusers = $DB->get_records_sql("SELECT u.id as id
                                   FROM {user} u, {department} d, {company_users} cu
                                   WHERE u.deleted <> 1 AND $userlist
                                   $usagesql
                                   $userallocationsql
                                   AND cu.userid = u.id AND cu.departmentid = d.id
                                   AND cu.companyid = :companyid
                                   $userfilterwithu
                                   GROUP BY u.id, d.name $dbsort ", $sqlparams);
} else {
    $users = array();
    $allusers = array();
}

$usercount = count($allusers);

if (empty($dodownload)) {
    echo $output->heading("$usercount ".get_string('users'));
}

$alphabet = explode(',', get_string('alphabet', 'block_iomad_company_admin'));
$strall = get_string('all');

if (empty($dodownload)) {
    echo $output->paging_bar($usercount, $page, $perpage, $baseurl);
}

flush();

if (!$users && empty($dodownload)) {
    $match = array();
    echo $output->heading(get_string('nousersfound'));

    echo "<p><a class='btn' href='" . new moodle_url('/blocks/iomad_company_admin/company_user_create_form.php') . "'>" .
         get_string('createuser', 'block_iomad_company_admin') . "</a></p>";

    $table = null;

} else {

    $countries = get_string_manager()->get_list_of_countries();

    foreach ($users as $key => $user) {
        if (!empty($user->country)) {
            $users[$key]->country = $countries[$user->country];
        }
    }

    $mainadmin = get_admin();
    // Set the initial parameters for the table header links.
    $linkparams = $urlparams;

    $override = new stdclass();
    $override->firstname = 'firstname';
    $override->lastname = 'lastname';
    $fullnamelanguage = get_string('fullnamedisplay', '', $override);
    if (($CFG->fullnamedisplay == 'firstname lastname') or
        ($CFG->fullnamedisplay == 'firstname') or
        ($CFG->fullnamedisplay == 'language' and $fullnamelanguage == 'firstname lastname' )) {
        // Work out for name sorting/direction and links.
        // Set the defaults.
        $linkparams['dir'] = 'ASC';
        $linkparams['sort'] = 'firstname';
        $firstnameurl = new moodle_url('index.php', $linkparams);
        $linkparams['sort'] = 'lastname';
        $lastnameurl = new moodle_url('index.php', $linkparams);
        $linkparams['sort'] = 'department';
        $departmenturl = new moodle_url('index.php', $linkparams);
        $linkparams['sort'] = 'email';
        $emailurl = new moodle_url('index.php', $linkparams);
        $linkparams['sort'] = 'timecreated';
        $timecreatedurl = new moodle_url('index.php', $linkparams);
        $linkparams['sort'] = 'lastaccess';
        $accessurl = new moodle_url('index.php', $linkparams);

        // Set the options if there is alread a sort.
        if (!empty($params['sort'])) {
            if ($params['sort'] == 'firstname') {
                $linkparams['sort'] = 'firstname';
                if ($params['dir'] == 'ASC') {
                    $linkparams['dir'] = 'DESC';
                    $firstnameurl = new moodle_url('index.php', $linkparams);
                } else {
                    $linkparams['dir'] = 'ASC';
                    $firstnameurl = new moodle_url('index.php', $linkparams);
                }
            } else if ($params['sort'] == 'lastname') {
                $linkparams['sort'] = 'lastname';
                if ($params['dir'] == 'ASC') {
                    $linkparams['dir'] = 'DESC';
                    $lastnameurl = new moodle_url('index.php', $linkparams);
                } else {
                    $linkparams['dir'] = 'ASC';
                    $lastnameurl = new moodle_url('index.php', $linkparams);
                }
            } else if ($params['sort'] == 'department') {
                $linkparams['sort'] = 'department';
                if ($params['dir'] == 'ASC') {
                    $linkparams['dir'] = 'DESC';
                    $departmenturl = new moodle_url('index.php', $linkparams);
                } else {
                    $linkparams['dir'] = 'ASC';
                    $emailurl = new moodle_url('index.php', $linkparams);
                }
            } else if ($params['sort'] == 'email') {
                $linkparams['sort'] = 'email';
                if ($params['dir'] == 'ASC') {
                    $linkparams['dir'] = 'DESC';
                    $emailurl = new moodle_url('index.php', $linkparams);
                } else {
                    $linkparams['dir'] = 'ASC';
                    $emailurl = new moodle_url('index.php', $linkparams);
                }
            } else if ($params['sort'] == 'lastaccess') {
                $linkparams['sort'] = 'lastaccess';
                if ($params['dir'] == 'ASC') {
                    $linkparams['dir'] = 'DESC';
                    $accessurl = new moodle_url('index.php', $linkparams);
                } else {
                    $linkparams['dir'] = 'ASC';
                    $accessurl = new moodle_url('index.php', $linkparams);
                }
            } else if ($params['sort'] == 'timecreated') {
                $linkparams['sort'] = 'timecreated';
                if ($params['dir'] == 'ASC') {
                    $linkparams['dir'] = 'DESC';
                    $timecreatedurl = new moodle_url('index.php', $linkparams);
                } else {
                    $linkparams['dir'] = 'ASC';
                    $timecreatedurl = new moodle_url('index.php', $linkparams);
                }
            }
        }
    }
    if (empty($dodownload)) {
        $fullnamedisplay = $output->action_link($firstnameurl, $firstname)." / ".
                                   $output->action_link($lastnameurl, $lastname);
    
        $table = new html_table();
        $headstart = array($fullnamedisplay => $fullnamedisplay,
                           $email => $output->action_link($emailurl, $email),
                           $department => $output->action_link($departmenturl, $department));
        $headmid = array();
        if (!empty($extrafields)) {
            foreach ($extrafields as $extrafield) {
                $headmid[$extrafield->name] = $extrafield->title;
            }
        }
    
        $headend = array ($licenseallocated => $licenseallocated,
                          $dateallocated => $dateallocated,
                          $dateunallocated => $dateunallocated,
                          $totalallocate => $totalallocate,
                          $totalunallocate => $totalunallocate);
        $table->head = $headstart + $headmid + $headend;
        $table->align = array ("left", "left", "left", "left", "left", "left", "center", "center", "center");
        $table->width = "95%";
    } else {
        $headstart = "\"$firstname\",\"$lastname\",\"$email\",\"$department\"";
        $headmid = "";
        if (!empty($extrafields)) {
            foreach ($extrafields as $extrafield) {
                $headmid .= ",\"$extrafield->title\"";
            }
        }
        $detail = get_string('detail', 'local_report_user_license_allocations');
        $headend = ",\"$licenseallocated\",\"$dateallocated\",\"$dateunallocated\",\"$totalallocate\",\"$detail\",\"$totalallocate\",\"$detail\"";
        echo $headstart . $headmid . $headend . "\n";
    }

    foreach ($users as $user) {
        if ($user->username == 'guest') {
            continue; // Do not display dummy new user and guest here.
        }

        // load the full user profile.
        profile_load_data($user);

        if (empty($dodownload)) {
            $fullname = fullname($user, true);
            // Is this a suspended user?
            if (!empty($user->suspended)) {
                $fullname .= " (S)";
            }
        } else {
            $fullname = "\"$user->firstname\",\"$user->lastname\"";
        }

        // Get the license information.
        // Is the license currently allocated?
        if ($DB->get_records('companylicense_users', array('licenseid' => $licenseid, 'userid' => $user->id))) {
            $licenseallocated = get_string('yes');
        } else {
            $licenseallocated = get_string('no');
        }

        // Get allocation info.
        if ($allocations = $DB->get_records('logstore_standard_log', array('eventname' => '\block_iomad_company_admin\event\user_license_assigned',
                                                                           'userid' => $user->id,
                                                                           'objectid' => $licenseid))) {
            if (!empty($license->program)) {
                // We need to go through the allocations and lump them together.
                $temp = array();
                foreach ($allocations as $allocation) {
                    $temp[$allocation->other. '-' . round($allocation->timecreated, -2)] = $allocation;
                }
                $allocations = $temp;
            }
            $allocationtip = "";
            foreach ($allocations as $allocation) {
                $allocationtip .= date($CFG->iomad_date_format, $allocation->timecreated) . "\n"; 
            }
            $totalallocated = count($allocations);
            $latest = array_pop($allocations);
            $dateallocated = date($CFG->iomad_date_format, $latest->timecreated);
        } else {
            $totalallocated = 0;
            $dateallocated = get_string('never');
            $allocationtip = "";
        }

        // Get unallocation info.
        if ($unallocations = $DB->get_records('logstore_standard_log', array('eventname' => '\block_iomad_company_admin\event\user_license_unassigned',
                                                                            'userid' => $user->id,
                                                                            'objectid' => $licenseid))) {
            if (!empty($license->program)) {
                // We need to go through the allocations and lump them together.
                $temp = array();
                foreach ($unallocations as $unallocation) {
                    $temp[$unallocation->other. '-' . round($unallocation->timecreated, -2)] = $unallocation;
                }
                $unallocations = $temp;
            }
            $unallocationtip = "";
            foreach ($unallocations as $unallocation) {
                $unallocationtip .= date($CFG->iomad_date_format, $unallocation->timecreated) . "\n"; 
            }
            $totalunallocated = count($unallocations);
            $unallocation = array_pop($unallocations);
            $dateunallocated = date($CFG->iomad_date_format, $unallocation->timecreated);
        } else {
            $dateunallocated = get_string('never');
            $totalunallocated = 0;
            $unallocationtip = "";
        }

        if (empty($dodownload)) {
        $userurl = new moodle_url('/local/report_user_license_allocations/userdisplay.php', array('userid' => $user->id));
            $rowstart = array('fullname' => "<a href = '$userurl'>$fullname</a>",
                              'email' => $user->email,
                              'department' => $user->departmentname);
            $rowmid = array();
            if (!empty($extrafields)) {
                foreach($extrafields as $extrafield) {
                    $fieldname = $extrafield->name;
                    $rowmid[$extrafield->name] = $user->$fieldname;
                }
            }
    
            $rowend = array('licenseallocated' => $licenseallocated,
                          'dateallocated' => $dateallocated,
                          'dateunallocated' => $dateunallocated,
                          'totalallocate' => "<a data-toggle='tooltip' data-placement='right' title='$allocationtip'>" . $totalallocated ."</a>",
                          'totalunallocate' => "<a data-toggle='tooltip' data-placement='right' title='$unallocationtip'>" . $totalunallocated . "</a>");
            $table->data[] = $rowstart + $rowmid + $rowend;
        } else {
            $rowstart = $fullname. ",\"$user->email\",\"$user->departmentname\"";
            $rowmid = "";
            if (!empty($extrafields)) {
                foreach($extrafields as $extrafield) {
                    $fieldname = $extrafield->name;
                    $rowmid .= ",\"$user->$fieldname\"";
                }
            }
    
            $rowend = ",\"$licenseallocated\",\"$dateallocated\",\"$dateunallocated\",\"$totalallocated\",\"$allocationtip\",\"$totalunallocated\",\"$unallocationtip\"";
            echo $rowstart . $rowmid . $rowend . "\n";
        }
                            
    }
}

if (!empty($dodownload)) {
    die;
}

if (!empty($table)) {
    echo html_writer::table($table);
    echo $output->paging_bar($usercount, $page, $perpage, $baseurl);
}

echo $output->footer();
