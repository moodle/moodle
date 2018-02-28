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

require_once(dirname(__FILE__).'/../../config.php');
require_once($CFG->libdir.'/completionlib.php');
require_once($CFG->libdir.'/excellib.class.php');
require_once(dirname(__FILE__).'/select_form.php');
require_once($CFG->dirroot.'/blocks/iomad_company_admin/lib.php');
require_once($CFG->dirroot."/local/email/lib.php");

// Params.
$participant  = optional_param('participant', 0, PARAM_INT);
$dodownload   = optional_param('dodownload', 0, PARAM_CLEAN);
$firstname    = optional_param('firstname', 0, PARAM_CLEAN);
$lastname     = optional_param('lastname', '', PARAM_CLEAN);
$showsuspended = optional_param('showsuspended', 0, PARAM_INT);
$email        = optional_param('email', 0, PARAM_CLEAN);
$sort         = optional_param('sort', 'name', PARAM_ALPHA);
$dir          = optional_param('dir', 'ASC', PARAM_ALPHA);
$page         = optional_param('page', 0, PARAM_INT);
$perpage      = optional_param('perpage', 30, PARAM_INT);        // How many per page.
$acl          = optional_param('acl', '0', PARAM_INT);           // Id of user to tweak mnet ACL (requires $access).
$search       = optional_param('search', '', PARAM_CLEAN);// Search string.
$departmentid = optional_param('departmentid', 0, PARAM_INTEGER);
$licensestatus = optional_param('licensestatus', 0, PARAM_INTEGER);
$licenseid    = optional_param('licenseid', 0, PARAM_INTEGER);

require_login($SITE);
$systemcontext = context_system::instance();
iomad::require_capability('local/report_user_licenses:view', $systemcontext);

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
if ($licenseid) {
    $params['licenseid'] = $licenseid;
}
if ($showsuspended) {
    $params['showsuspended'] = $showsuspended;
}
if ($licensestatus) {
    $params['licensestatus'] = $licensestatus;
}

// Set the companyid
$companyid = iomad::get_my_companyid($systemcontext);

if ($category = $DB->get_record_sql("SELECT uic.id, uic.name FROM {user_info_category} uic, {company} c
                                     WHERE c.id = :companyid
                                     AND c.profileid=uic.id", array('companyid' => $companyid))) {
    // Get field names from company category.
    if ($fields = $DB->get_records('user_info_field', array('categoryid' => $category->id))) {
        foreach ($fields as $field) {
            $fieldnames[$field->id] = 'profile_field_'.$field->shortname;
            ${'profile_field_'.$field->shortname} = optional_param('profile_field_'.$field->shortname, null, PARAM_RAW);
        }
    }
}

// Deal with the user optional profile search.
$idlist = array();
if (!empty($fieldnames)) {
    $fieldids = array();
    foreach ($fieldnames as $id => $fieldname) {
        if ($fields[$id]->datatype == "menu") {
            $paramarray = explode("\n", $fields[$id]->param1);
            if (!empty($paramarray[${$fieldname}])) {
                ${$fieldname} = $paramarray[${$fieldname}];
            }
        }
        if (!empty(${$fieldname})) {
            $idlist[0] = "We found no one";
            $fieldsql = $DB->sql_compare_text('data')." LIKE '%".${$fieldname}."%' AND fieldid = $id";
            if ($idfields = $DB->get_records_sql("SELECT userid from {user_info_data} WHERE $fieldsql")) {
                $fieldids[] = $idfields;
            }
        }
    }

    if (!empty($fieldids)) {
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

// Url stuff.
$url = new moodle_url('/local/report_user_licenses/index.php');
$dashboardurl = new moodle_url('/local/iomad_dashboard/index.php');

// Page stuff:.
$strcompletion = get_string('pluginname', 'local_report_user_licenses');
$PAGE->set_url($url);
$PAGE->set_pagelayout('admin');
$PAGE->set_title($strcompletion);
$PAGE->requires->css("/local/report_user_licenses/styles.css");
$PAGE->requires->jquery();

// Set the page heading.
$PAGE->set_heading(get_string('pluginname', 'block_iomad_reports') . " - $strcompletion");

// Work out department level.
$company = new company($companyid);
$parentlevel = company::get_company_parentnode($company->id);
$companydepartment = $parentlevel->id;

if (iomad::has_capability('block/iomad_company_admin:edit_all_departments', context_system::instance()) ||
    !empty($SESSION->currenteditingcompany)) {
    $userhierarchylevel = $parentlevel->id;
} else {
    $userlevel = $company->get_userlevel($USER);
    $userhierarchylevel = $userlevel->id;
}
if ($departmentid == 0 ) {
    $departmentid = $userhierarchylevel;
}

// Get the company additional optional user parameter names.
$foundobj = iomad::add_user_filter_params($params, $companyid);
$idlist = $foundobj->idlist;
$foundfields = $foundobj->foundfields;

// Set the url.
company_admin_fix_breadcrumb($PAGE, $strcompletion, $url);
$url = new moodle_url('/local/report_user_licenses/index.php', $params);

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

// Get the renderer.
$output = $PAGE->get_renderer('block_iomad_company_admin');

// Javascript for fancy select.
// Parameter is name of proper select form element followed by 1=submit its form
$PAGE->requires->js_call_amd('block_iomad_company_admin/department_select', 'init', array('departmentid', 1, optional_param('departmentid', 0, PARAM_INT)));

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

// Get the appropriate list of departments.
$selectparams = $params;
$selecturl = new moodle_url('/local/report_user_licenses/index.php', $selectparams);
$select = new single_select($selecturl, 'licenseid', $licenselist, $licenseid);
$select->label = get_string('licenseselect', 'block_iomad_company_admin');
$select->formid = 'chooselicense';
$licenseselectoutput = html_writer::tag('div', $output->render($select), array('id' => 'iomad_department_selector'));

// Get the appropriate list of departments.
$selectparams = $params;
$selectparams['courseid'] = 0;
$selecturl = new moodle_url('/local/report_user_licenses/index.php', $selectparams);
$subhierarchieslist = company::get_all_subdepartments($userhierarchylevel);
$select = new single_select($selecturl, 'departmentid', $subhierarchieslist, $departmentid);
$select->label = get_string('department', 'block_iomad_company_admin');
$select->formid = 'choosedepartment';
$fwselectoutput = html_writer::tag('div', $output->render($select), array('id' => 'iomad_department_selector'));

$departmenttree = company::get_all_subdepartments_raw($userhierarchylevel);
$treehtml = $output->department_tree($departmenttree, optional_param('departmentid', 0, PARAM_INT));

if (!(iomad::has_capability('block/iomad_company_admin:editusers', $systemcontext) or
      iomad::has_capability('block/iomad_company_admin:editallusers', $systemcontext))) {
    print_error('nopermissions', 'error', '', 'report on users');
}

$searchinfo = iomad::get_user_sqlsearch($params, $idlist, $sort, $dir, $departmentid, true, true);

// Create data for form.
$customdata = null;
$options = $params;
$options['dodownload'] = 1;

// Only print the header if we are not downloading.
if (empty($dodownload)) {
    echo $output->header();
    // Check the department is valid.
    if (!empty($departmentid) && !company::check_valid_department($companyid, $departmentid)) {
        print_error('invaliddepartment', 'block_iomad_company_admin');
    }

} else {
    // Check the department is valid.
    if (!empty($departmentid) && !company::check_valid_department($companyid, $departmentid)) {
        print_error('invaliddepartment', 'block_iomad_company_admin');
        die;
    }
}

// Get the data.
if (!empty($companyid)) {
    if (empty($dodownload)) {
        // Do we have any licenses?

        if (empty($licenselist) && empty($dodownload)) {
            echo get_string('nolicenses', 'block_iomad_company_admin');
            echo $output->footer();
            die;
        }

        if (empty($dodownload)) {
            echo $licenseselectoutput;
            if (empty($licenseid)) {
                echo $output->footer();
                die;
            }

            echo html_writer::start_tag('div', array('class' => 'iomadclear'));
            echo html_writer::start_tag('div', array('class' => 'fitem'));
            echo $treehtml;
            echo html_writer::start_tag('div', array('style' => 'display:none'));
            echo $fwselectoutput;
            echo html_writer::end_tag('div');
            echo html_writer::end_tag('div');
            echo html_writer::end_tag('div');

            if (empty($licenseid)) {
                echo $output->footer();
                die;
            }

            // Set up the filter form.
            $params['companyid'] = $companyid;
            $params['addlicensestatus'] = true;
            $params['adddodownload'] = true;
            $mform = new iomad_user_filter_form(null, $params);
            $mform->set_data($params);
            $mform->set_data(array('departmentid' => $departmentid));
            $mform->set_data(array('licensestatus' => $licensestatus));
            $mform->get_data();

            // Display the user filter form.
            $mform->display();
        }
    }
}

if (!empty($dodownload)) {
    // Set up the Excel workbook.

    header("Content-Type: application/download\n");
    header("Content-Disposition: attachment; filename=\"user_license_report.csv\"");
    header("Expires: 0");
    header("Cache-Control: must-revalidate,post-check=0,pre-check=0");
    header("Pragma: public");
}

$license = $DB->get_record('companylicense', array('id' => $licenseid));
if (empty($license->program)) {
    $columns = array("firstname", "lastname", "email", "department", "course", "licenseallocated", "licenseinuse", "licenseused", "lastaccess");
} else {
    $columns = array("firstname", "lastname", "email", "department", "licenseallocated", "licenseinuse", "licenseused", "lastaccess");
}

foreach ($columns as $column) {
    if ($column == 'company') {
        $string[$column] = get_string('company', 'block_iomad_company_admin');
    } else if ($column == 'licenseallocated') {
        $string[$column] = get_string('licensedateallocated', 'block_iomad_company_admin');
    } else if ($column == 'licenseinuse') {
        $string[$column] = get_string('licenseuserinuse', 'block_iomad_company_admin');
    } else if ($column == 'licenseused') {
        $string[$column] = get_string('licensedateused', 'block_iomad_company_admin');
    } else {
        $string[$column] = get_string("$column");
    }
    if ($sort != $column) {
        $columnicon = "";
        if ($column == "lastaccess") {
            $columndir = "DESC";
        } else {
            $columndir = "ASC";
        }
    } else {
        $columndir = $dir == "ASC" ? "DESC":"ASC";
        if ($column == "lastaccess") {
            $columnicon = $dir == "ASC" ? "up":"down";
        } else {
            $columnicon = $dir == "ASC" ? "down":"up";
        }
        $columnicon = " <img src=\"" . $output->image_url('t/' . $columnicon) . "\" alt=\"\" />";

    }
    $params['sort'] = $column;
    $params['dir'] = $columndir;
    if ($column == 'course' || $column == "licenseused" || $column == 'licenseinuse' || !empty($dodownload)) {
        $$column = $string[$column];
    } else {
        $$column = "<a href= ". new moodle_url('index.php', $params).">".$string[$column]."</a>$columnicon";
    }
}

if ($sort == "name") {
    $sort = "firstname";
}

// Get all or company users depending on capability.
//  Check if has capability edit all users.
if (iomad::has_capability('block/iomad_company_admin:editallusers', $systemcontext)) {
    // Make sure we dont display site admins.
    // Set default search to something which cant happen.
    $sqlsearch = "id!='-1'";
    $sqlsearch = "id!='-1' AND id NOT IN (" . $CFG->siteadmins . ")";

    // Get department users.
    $departmentusers = company::get_recursive_department_users($departmentid);
    if (count($departmentusers) > 0) {
        $departmentids = "";
        foreach ($departmentusers as $departmentuser) {
            if (!empty($departmentids)) {
                $departmentids .= ",".$departmentuser->userid;
            } else {
                $departmentids .= $departmentuser->userid;
            }
        }
        if (!empty($showsuspended)) {
            $sqlsearch .= " AND deleted <> 1 ";
        } else {
            $sqlsearch .= " AND deleted <> 1 AND suspended = 0 ";
        }
        $sqlsearch .= " AND id in ($departmentids) ";
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
    $userrecords = $DB->get_fieldset_select('user', 'id', $sqlsearch, $searchparams);

} else if (iomad::has_capability('block/iomad_company_admin:editusers', $systemcontext)) {   // Check if has role edit company users.

    // Get users company association.
    $departmentusers = company::get_recursive_department_users($departmentid);
    if (count($departmentusers) > 0) {
        $departmentids = "";
        foreach ($departmentusers as $departmentuser) {
            if (!empty($departmentids)) {
                $departmentids .= ",".$departmentuser->userid;
            } else {
                $departmentids .= $departmentuser->userid;
            }
        }
        if (!empty($showsuspended)) {
            $sqlsearch = " deleted <> 1 AND id in ($departmentids) ";
        } else {
            $sqlsearch = " deleted <> 1 AND suspended = 0 AND id in ($departmentids) ";
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

    $userrecords = $DB->get_fieldset_select('user', 'id', $sqlsearch, $searchparams);
}

$userlist = "";

if (!empty($userrecords)) {
    $userlist = "u.id in (". implode(',', array_values($userrecords)).")";
} else {
    $userlist = "1=2";
}

if (!empty($userlist)) {
    $users = iomad_get_users_listing($sort, $dir, $page * $perpage, $perpage, '', '', '', $userlist, array('companyid' => $companyid, 'licenseid' => $licenseid, 'licensestatus' => $licensestatus));
    $totalusers = iomad_get_users_listing($sort, $dir, 0, 0, '', '', '', $userlist, array('companyid' => $companyid, 'licenseid' => $licenseid, 'licensestatus' => $licensestatus));

} else {
    $users = array();
}
$usercount = count($totalusers);

if (empty($dodownload)) {
    echo $output->heading("$usercount ".get_string('users'));
}

$alphabet = explode(',', get_string('alphabet', 'block_iomad_company_admin'));
$strall = get_string('all');

// Fix sort for paging.
$params['sort'] = $sort;
$params['dir'] = $dir;

// We don't want to be deleting when we are using the paging bar.
$params['delete'] = '';
$params['confirm'] = '';

$baseurl = new moodle_url('index.php', $params);

flush();


if (!$users && empty($dodownload)) {
    $match = array();
    echo $output->heading(get_string('nousersfound'));

    $table = null;

} else {

    if (empty($dodownload)) {
        echo $output->paging_bar($usercount, $page, $perpage, $baseurl);
    }
    $mainadmin = get_admin();

    $override = new stdclass();
    $override->firstname = 'firstname';
    $override->lastname = 'lastname';
    $fullnamelanguage = get_string('fullnamedisplay', '', $override);
    if (($CFG->fullnamedisplay == 'firstname lastname') or
        ($CFG->fullnamedisplay == 'firstname') or
        ($CFG->fullnamedisplay == 'language' and $fullnamelanguage == 'firstname lastname')) {
        $fullnamedisplay = "$firstname / $lastname";
    } else {
        $fullnamedisplay = "$lastname / $firstname";
    }

    if (empty($dodownload)) {
        // set up the table.
        $table = new html_table();
        $table->id = 'ReportTable';
        $headstart = array('fullnamedisplay' => $fullnamedisplay,
                           'email' => $email,
                           'department' => $department);

        $headmid = array();
        if (!empty($extrafields)) {
            foreach ($extrafields as $extrafield) {
                $headmid[$extrafield->name] = $extrafield->title;
            }
        }

        if (empty($license->program)) {
            $headend = array('course' => $course,
                             'licenseallocated' => $licenseallocated,
                             'licenseinuse' => $licenseinuse,
                             'licenseused' => $licenseused,
                             'lastaccess' => $lastaccess);
            $table->align = array ("left", "center", "center", "center", "center", "center", "center", "center");
        } else {
            $headend = array('licenseallocated' => $licenseallocated,
                             'licenseinuse' => $licenseinuse,
                             'licenseused' => $licenseused,
                             'lastaccess' => $lastaccess);
            $table->align = array ("left", "center", "center", "center", "center", "center", "center");
        }
    } else {
        $fullnamedisplay = "\"$firstname\",\"$lastname\"";
        $headstart = $fullnamedisplay .",\"$email\",\"$department\"";
        $headmid = "";
        if (!empty($extrafields)) {
            foreach ($extrafields as $extrafield) {
                $headmid .= ",\"$extrafield->title\"";
            }
        }

        if (empty($license->program)) {
            $headend = ",\"$course\",\"$licenseallocated\",\"$licenseinuse\",\"$licenseused\",\"$lastaccess\"\n";
        } else {
            $headend = ",\"$licenseallocated\",\"$licenseinuse\",\"$licenseused\",\"$lastaccess\"\n";
        }
        echo $headstart . $headmid . $headend;
    }
    $table->head = $headstart + $headmid + $headend;

    $stringyesno = array(0 => get_string('no'), 1 => get_string('yes'));
    $stringnever = get_string('never');

    foreach ($users as $user) {

        // User actions
        $actions = array();

        profile_load_data($user);

        if ($user->username == 'guest') {
            continue; // Do not dispaly dummy new user and guest here.
        }

        if ($user->lastaccess) {
            $strlastaccess = date($CFG->iomad_date_format, $user->lastaccess);
        } else {
            $strlastaccess = $stringnever;
        }

        if (empty($license->program)) {
            if ($user->issuedate) {
                $strissuedate = date($CFG->iomad_date_format, $user->issuedate);
            } else {
                $strissuedate = $stringnever;
            }

            $strisusing = $stringyesno[$user->isusing];

            // Get the date the user accessed the course.
            if ($user->isusing) {
                $enrolinfo = $DB->get_record_sql("SELECT ue.* FROM {user_enrolments} ue
                                                  JOIN {enrol} e ON (ue.enrolid = e.id AND e.enrol = :license)
                                                  WHERE ue.userid = :userid
                                                  AND e.courseid = :courseid",
                                                  array('userid' => $user->id,
                                                        'courseid' => $user->licensecourseid,
                                                        'license' => 'license'));
                $struseddate = date($CFG->iomad_date_format, $enrolinfo->timestart);
            } else {
                $struseddate =  $stringnever;
            }
        } else {
            if ($userlicenseinfo = $DB->get_records_sql("SELECT * FROM {companylicense_users}
                                                         WHERE userid = :userid
                                                         AND licenseid = :licenseid
                                                         ORDER BY issuedate",
                                                         array('userid' => $user->id, 'licenseid' => $licenseid), 0,1)) {
                $strisusing = $stringyesno[1];
                $userlicense = array_pop($userlicenseinfo);
                $strissuedate = date($CFG->iomad_date_format, $userlicense->issuedate);
                if ($enrolinfo = $DB->get_record_sql("SELECT ue.* FROM {user_enrolments} ue
                                                      JOIN {enrol} e ON (ue.enrolid = e.id AND e.enrol = :license)
                                                      WHERE ue.userid = :userid
                                                      AND e.courseid = :courseid",
                                                      array('userid' => $user->id,
                                                            'courseid' => $userlicense->licensecourseid,
                                                            'license' => 'license'))) {
                    $struseddate = date($CFG->iomad_date_format, $enrolinfo->timestart);
                } else {
                    $struseddate =  $stringnever;
                }
            } else {
                $strisusing = $stringyesno[0];
                $strissuedate = $stringnever;
                $struseddate =  $stringnever;
            }
        }

        $fullname = fullname($user, true);

        // Is this a suspended user?
        if (!empty($user->suspended)) {
            $fullname .= " (S)";
        }

        $numlogins = $DB->count_records('logstore_standard_log', array('userid' => $user->id, 'eventname' => '\core\event\user_loggedin'));

        $user->department = $user->departmentname;

        if (empty($dodownload)) {
            $rowstart = array('fullname' => $fullname,
                              'email' => $user->email,
                              'department' => $user->departmentname);
            $rowmid = array();
            if (!empty($extrafields)) {
                foreach($extrafields as $extrafield) {
                    $fieldname = $extrafield->name;
                    $rowmid[$extrafield->name] = $user->$fieldname;
                }
            }

            if (empty($license->program)) {
                $rowend = array('coursename' => $user->coursename,
                                'strissuedate' => $strissuedate,
                                'strisusing' => $strisusing,
                                'struseddate' => $struseddate,
                                'strlastaccess' => $strlastaccess);
            } else {
                $rowend = array('strissuedate' => $strissuedate,
                                'strisusing' => $strisusing,
                                'struseddate' => $struseddate,
                                'strlastaccess' => $strlastaccess);
            }
            $table->data[] = $rowstart + $rowmid + $rowend;
        } else {
            $rowstart =  "\"$user->firstname\",\"$user->lastname\",\"$user->email\",\"$user->department\"";
            $rowmid = "";
            if (!empty($extrafields)) {
                foreach($extrafields as $extrafield) {
                    $fieldname = $extrafield->name;
                    $rowmid .= ",\"$user->$fieldname\"";
                }
            }
            if (empty($license->program)) {
                $rowend =  ",\"$user->coursename\",\"$strissuedate\",\"$strisusing\",\"$struseddate\",\"$strlastaccess\"\n";
            } else {
                $rowend =  ",\"$strissuedate\",\"$strisusing\",\"$struseddate\",\"$strlastaccess\"\n";
            }
            echo $rowstart . $rowmid . $rowend;
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

function iomad_get_users_listing($sort='lastaccess', $dir='ASC', $page=0, $recordsperpage=0,
                       $search='', $firstinitial='', $lastinitial='', $extraselect='', array $extraparams = null) {
    global $DB, $USER;

    $fullname  = $DB->sql_fullname();

    $select = "u.deleted <> 1";
    $params = array();

    if (!empty($search)) {
        $search = trim($search);
        $select .= " AND (". $DB->sql_like("u.$fullname", ':search1', false, false).
                   " OR ". $DB->sql_like('u.email', ':search2', false, false).
                   " OR u.username = :search3)";
        $params['search1'] = "%$search%";
        $params['search2'] = "%$search%";
        $params['search3'] = "$search";
    }

    if ($firstinitial) {
        $select .= " AND ". $DB->sql_like('u.firstname', ':fni', false, false);
        $params['fni'] = "$firstinitial%";
    }
    if ($lastinitial) {
        $select .= " AND ". $DB->sql_like('u.lastname', ':lni', false, false);
        $params['lni'] = "$lastinitial%";
    }

    if ($extraselect) {
        $select .= " AND $extraselect";
        $params = $params + (array)$extraparams;
    }

    if ($sort) {
        if ($sort == "department") {
            $sort = " ORDER by d.name $dir";
        } else if ($sort == "company") {
            $sort = " ORDER by c.name $dir";
        } else if ($sort == "course") {
            $sort = " ORDER by co.fullname $dir";
        } else if ($sort == "licenseused") {
            $sort = " ORDER by clu.isusing $dir";
        } else if ($sort == "licenseallocated") {
            $sort = " ORDER by clu.issuedate $dir";
        } else {
            $sort = " ORDER BY u.$sort $dir";
        }
    }

    // all companies?
    $company = new company($extraparams['companyid']);

    if ($parentslist = $company->get_parent_companies_recursive()) {
        $companysql = " AND c.id = :companyid AND u.id NOT IN (
                        SELECT userid FROM {company_users}
                        WHERE companyid IN (" . implode(',', array_keys($parentslist)) ."))";
    } else {
        $companysql = " AND c.id = :companyid";
    }
    $params['companyid'] = $extraparams['companyid'];
    $params['licenseid'] = $extraparams['licenseid'];

    // What type of license is it?
    $license = $DB->get_record('companylicense', array('id' => $extraparams['licenseid']));
    if (empty($license->program)) {
        if (!empty($extraparams['licensestatus'])) {
            if ($extraparams['licensestatus'] == 1) {
                $statussql = " AND clu.isusing = 0 ";
            } else {
                $statussql = " AND clu.isusing = 1 ";
            }
        } else {
            $statussql = "";
        }
        return $DB->get_records_sql("SELECT concat(c.id, '-', u.id), u.*, d.name AS departmentname, c.name AS companyname,
                                     clu.isusing, clu.issuedate, co.fullname AS coursename, clu.licensecourseid
                                     FROM {user} u, {department} d, {company_users} cu, {company} c, {companylicense_users} clu, {course} co
                                     WHERE $select AND cu.userid = u.id AND d.id = cu.departmentid AND c.id = cu.companyid
                                     AND clu.userid = u.id AND clu.licenseid = :licenseid
                                     AND clu.licensecourseid = co.id
                                     $companysql
                                     $statussql
                                     $sort", $params, $page, $recordsperpage);
    } else {
        if (!empty($extraparams['licensestatus'])) {
            $params['licenseidtoo'] = $extraparams['licenseid'];
            if ($extraparams['licensestatus'] == 1) {
                $statussql = " AND u.id NOT IN (SELECT userid FROM {companylicense_users} WHERE isusing = 1 AND licenseid = :licenseidtoo) ";
            } else {
                $statussql = " AND u.id IN (SELECT userid FROM {companylicense_users} WHERE isusing = 1 AND licenseid = :licenseidtoo) ";
            }
        } else {
            $statussql = "";
        }
        return $DB->get_records_sql("SELECT DISTINCT concat(c.id, '-', u.id), u.*, d.name AS departmentname, c.name AS companyname, clu.issuedate
                                     FROM {user} u, {department} d, {company_users} cu, {company} c, {companylicense_users} clu
                                     WHERE $select and cu.userid = u.id AND d.id = cu.departmentid AND c.id = cu.companyid
                                     AND clu.userid = u.id AND clu.licenseid = :licenseid
                                     $statussql
                                     $companysql
                                     $sort", $params, $page, $recordsperpage);
    }
}
