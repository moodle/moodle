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
require_once($CFG->dirroot.'/local/iomad/pchart2/class/pData.class.php');
require_once($CFG->dirroot.'/local/iomad/pchart2/class/pDraw.class.php');
require_once($CFG->dirroot.'/local/iomad/pchart2/class/pImage.class.php');
require_once($CFG->dirroot.'/local/iomad/pchart2/class/pPie.class.php');
require_once($CFG->dirroot."/local/email/lib.php");

// chart stuff
define('PCHART_SIZEX', 500);
define('PCHART_SIZEY', 500);

// Params.
$courseid = optional_param('courseid', 0, PARAM_INT);
$participant = optional_param('participant', 0, PARAM_INT);
$dodownload = optional_param('dodownload', 0, PARAM_INT);
$sendemail = optional_param('sendemail', false, PARAM_BOOL);
$firstname       = optional_param('firstname', 0, PARAM_CLEAN);
$lastname      = optional_param('lastname', '', PARAM_CLEAN);
$showsuspended = optional_param('showsuspended', 0, PARAM_INT);
$email  = optional_param('email', 0, PARAM_CLEAN);
$sort         = optional_param('sort', 'name', PARAM_ALPHA);
$dir          = optional_param('dir', 'ASC', PARAM_ALPHA);
$page         = optional_param('page', 0, PARAM_INT);
$perpage      = optional_param('perpage', 30, PARAM_INT);        // How many per page.
$acl          = optional_param('acl', '0', PARAM_INT);           // Id of user to tweak mnet ACL (requires $access).
$search      = optional_param('search', '', PARAM_CLEAN);// Search string.
$departmentid = optional_param('departmentid', 0, PARAM_INTEGER);
$compfromraw = optional_param_array('compfrom', null, PARAM_INT);
$comptoraw = optional_param_array('compto', null, PARAM_INT);
$completiontype = optional_param('completiontype', 0, PARAM_INT);
$charttype = optional_param('charttype', '', PARAM_CLEAN);
$showchart = optional_param('showchart', false, PARAM_BOOL);
$confirm = optional_param('confirm', '', PARAM_ALPHANUM);
$emailsent = optional_param('emailsent', false, PARAM_BOOL);
$showused = optional_param('showused', true, PARAM_BOOL);

require_login($SITE);
$context = context_system::instance();
iomad::require_capability('local/report_license:view', $context);

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
if ($courseid) {
    $params['courseid'] = $courseid;
}
if ($departmentid) {
    $params['departmentid'] = $departmentid;
}
if ($departmentid) {
    $params['departmentid'] = $departmentid;
}
if ($showsuspended) {
    $params['showsuspended'] = $showsuspended;
}
if ($charttype) {
    $params['charttype'] = $charttype;
}
if ($sendemail) {
    $params['sendemail'] = $sendemail;
}
$params['showused'] = $showused;

if ($compfromraw) {
    if (is_array($compfromraw)) {
        $compfrom = mktime(0, 0, 0, $compfromraw['month'], $compfromraw['day'], $compfromraw['year']);
    } else {
        $compfrom = $compfromraw;
    }
    $params['compfrom'] = $compfrom;
} else {
    $compfrom = 0;
}

if ($comptoraw) {
    if (is_array($comptoraw)) {
        $compto = mktime(0, 0, 0, $comptoraw['month'], $comptoraw['day'], $comptoraw['year']);
    } else {
        $compto = $comptoraw;
    }
    $params['compto'] = $compto;
} else {
    $compto = 0;
}

// Url stuff.
$url = new moodle_url('/local/report_license/index.php');
$dashboardurl = new moodle_url('/my');

// Page stuff:.
$strcompletion = get_string('pluginname', 'local_report_license');
$PAGE->set_url($url);
$PAGE->set_pagelayout('report');
$PAGE->set_title($strcompletion);
$PAGE->requires->css("/local/report_license/styles.css");
$PAGE->requires->jquery();

// get output renderer                                                                                                                                                                                         
$output = $PAGE->get_renderer('block_iomad_company_admin');

// Javascript for fancy select.
// Parameter is name of proper select form element followed by 1=submit its form
$PAGE->requires->js_call_amd('block_iomad_company_admin/department_select', 'init', array('departmentid', 1, optional_param('departmentid', 0, PARAM_INT)));

// Set the page heading.
$PAGE->set_heading(get_string('pluginname', 'block_iomad_reports') . " - $strcompletion");

// Set the page heading.
$PAGE->set_heading(get_string('pluginname', 'block_iomad_reports') . " - $strcompletion");

// Set the companyid
$companyid = iomad::get_my_companyid($context);

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

$url = new moodle_url('/local/report_license/index.php', $params);

// Get the appropriate list of departments.
$userdepartment = $company->get_userlevel($USER);
$departmenttree = company::get_all_subdepartments_raw($userdepartment->id);
$treehtml = $output->department_tree($departmenttree, optional_param('departmentid', 0, PARAM_INT));
$selectparams = $params;
$selecturl = new moodle_url('/local/report_license/index.php', $selectparams);
$subhierarchieslist = company::get_all_subdepartments($userhierarchylevel);
$select = new single_select($selecturl, 'departmentid', $subhierarchieslist, $departmentid);
$select->label = get_string('department', 'block_iomad_company_admin');
$select->formid = 'choosedepartment';
$fwselectoutput = html_writer::tag('div', $output->render($select), array('id' => 'iomad_company_selector', 'style' => 'display: none;'));

if (!(iomad::has_capability('block/iomad_company_admin:editusers', $context) or
      iomad::has_capability('block/iomad_company_admin:editallusers', $context))) {
    print_error('nopermissions', 'error', '', 'report on users');
}

$searchinfo = iomad::get_user_license_sqlsearch($params, $idlist, $sort, $dir, $departmentid, true);

// Create data for form.
$customdata = null;
$options = $params;
$options['dodownload'] = 1;

// Only print the header if we are not downloading.
if (empty($dodownload) && empty($showchart) && !$sendemail) {
    echo $output->header();
    // Check the department is valid.
    if (!empty($departmentid) && !company::check_valid_department($companyid, $departmentid)) {
        print_error('invaliddepartment', 'block_iomad_company_admin');
    }   

    if ($emailsent) {
        echo "<h2>".get_string('licenseemailsent', 'local_report_license')."</h2>";
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
    if (empty($dodownload) && empty($showchart) && !$sendemail) {
        echo $treehtml;
        echo $fwselectoutput;
    }
}

if (empty($dodownload) && empty($showchart) && !$sendemail) {
    echo "<h3>".get_string('coursesummary', 'local_report_license')."</h3>";
    if (!empty($courseid)) {
        // Navigation and header.
        echo $output->single_button(new moodle_url('index.php', $options), get_string("downloadcsv", 'local_report_license'));
        $options['charttype'] = 'summary';
        $options['dodownload'] = false;
        echo $output->single_button(new moodle_url('index.php', $options), get_string("summarychart", 'local_report_license'));
        $options['charttype'] = '';
        $options['sendemail'] = true;
        echo $output->single_button(new moodle_url('index.php', $options), get_string("sendreminderemail", 'local_report_license'));
    } else {
        $options['charttype'] = 'summary';
        $options['dodownload'] = false;
        echo $output->single_button(new moodle_url('index.php', $options), get_string("summarychart", 'local_report_license'));
        $alluserslink = new moodle_url($url, array(
            'courseid' => 1,
            'departmentid' => $departmentid,
            'showchart' => 0,
            'charttype' => '',
        ));
        echo $output->single_button($alluserslink, get_string("allusers", 'local_report_license'));
    }

}

// Set up the course overview table.
$coursecomptable = new html_table();
$coursecomptable->id = 'ReportTable';
$coursecomptable->head = array(
    get_string('coursename', 'local_report_license'),
    get_string('numallocated', 'local_report_license'),
    get_string('unused', 'local_report_license'),
    ' ',
);
$coursecomptable->align = array('left', 'center', 'center', 'center');
//$coursecomptable->width = '95%';
$chartdata = array();

if (!empty($dodownload)) {
    // Set up the Excel workbook.

    header("Content-Type: application/download\n");
    header("Content-Disposition: attachment; filename=\"license_report.csv\"");
    header("Expires: 0");
    header("Cache-Control: must-revalidate,post-check=0,pre-check=0");
    header("Pragma: public");

}
$courseinfo = iomad::get_course_license_summary_info ($departmentid, 0, $showsuspended);
$chartnumusers = array();
$chartnotstarted = array();
$chartinprogress = array();
$chartcompleted = array();
$chartname = array();

// Iterate over courses.
foreach ($courseinfo as $id => $coursedata) {
    $courseuserslink = new moodle_url($url, array(
        'courseid' => $coursedata->id,
        'departmentid' => $departmentid,
        'showchart' => 0,
        'charttype' => '',
    ));
    $coursechartlink = new moodle_url('index.php', array(
        'courseid' => $coursedata->id,
        'departmentid' => $departmentid,
        'showchart' => 0,
        'charttype' => 'course',
    ));
    $coursecomptable->data[] = array(
        $coursedata->coursename,
        $coursedata->numlicenses,
        $coursedata->numunused,
        '<a class="btn" style="margin:2px" href="' . $courseuserslink . '">' . get_string('usersummary', 'local_report_license') . '</a>&nbsp;' .
        '<a class="btn" style="margin:2px" href="' . $coursechartlink . '">' . get_string('cchart', 'local_report_license') . '</a>',
    );
    if ($charttype == 'summary') {
        $chartname[] = $coursedata->coursename;
        $chartnumusers[] = $coursedata->numlicenses;
        $chartinprogress[] = $coursedata->numunused;
    } else if ($charttype == 'course' && $courseid == $coursedata->id ) {
        $seriesdata = array($coursedata->numunused);
    }
}

if (!empty($charttype)) {
    $chartdata = new pData();
    if ($charttype == 'summary') {
        $chartdata->addPoints($chartnotstarted, 's_notstarted' );
        $chartdata->addPoints($chartinprogress, 's_inprogress' );
    } else if ($charttype == 'course') {
        $chartdata->addPoints($seriesdata, 'Value');
    }
    $chartdata->addPoints(array(
        get_string('notstartedusers', 'local_report_license'),
        get_string('inprogressusers', 'local_report_license'),
    ), 'Legend');
    $chartdata->setAbscissa('Legend');
}

if (empty($dodownload) && empty($showchart) && !$sendemail) {
    if (empty($courseid)) {
        echo html_writer::table($coursecomptable);
    }
}

if (empty($charttype)) {
    if (!empty($courseid)) {
        // Get the course license information.
        if ($dodownload) {
            if (empty($idlist['0'])) {
                if ($courseid == 1) {
                    $coursedataobj = iomad::get_all_user_course_license_data($searchinfo);
                } else {
                    $coursedataobj = iomad::get_user_course_license_data($searchinfo, $courseid, 0, 0, $showsuspended, $showused);
                }
                $coursedata = $coursedataobj->users;
                $totalcount = $coursedataobj->totalcount;
            }
        } else if ($sendemail) {
            if ($confirm && confirm_sesskey()) {
                if ($courseid == 1) {
                    $coursedataobj = iomad::get_all_user_course_license_data($searchinfo);
                } else {
                    $coursedataobj = iomad::get_user_course_license_data($searchinfo, $courseid, 0, 0, $showsuspended, $showused);
                }
                $coursedata = $coursedataobj->users;
                $totalcount = $coursedataobj->totalcount;
                // Send everyone a reminder email.
                foreach ($coursedata as $user) {
                    if ($userdata = $DB->get_record('user', array('id' => $user->uid))) {
                        $license = $DB->get_record('companylicense', array('id' => $user->licenseid));
                        $licensedata = new stdclass();
                        $licensedata->length = $license->validlength;
                        $licensedata->valid = date($CFG->iomad_date_format, $license->expirydate);
                        EmailTemplate::send('license_reminder', array('course' => $user->courseid,
                                                                      'user' => $userdata,
                                                                      'license' => $licensedata));
                    }
                }
                $params['sendemail'] = false;
                $params['emailsent'] = true;
                redirect(new moodle_url('/local/report_license/index.php', $params));
            } else {
                echo $output->header();
                $params['confirm'] = true;
                $params['sesskey'] = sesskey();
                $buttons = $output->single_button(
                    new moodle_url('/local/report_license/index.php', $params),
                    get_string('send_button', 'local_email')
                );
                $params['confirm'] = false;
                $params['sendemail'] = false;
                $buttons .= $output->single_button(
                    new moodle_url('/local/report_license/index.php', $params),
                    get_string('cancel')
                );
                echo $output->box_start('generalbox', 'notice');
                echo $output->box(get_string('licensesendreminder', 'local_report_license'));
                echo $output->box($buttons, 'buttons');
                echo $output->box_end();
                echo $output->footer();
            }
            die;
        } else {
            if (empty($idlist['0'])) {
                // Only want the data for the page we are on.
                // courseid==1 is ALL users.
                if ($courseid == 1) {
                    $coursedataobj = iomad::get_all_user_course_license_data($searchinfo, $page, $perpage);
                } else {
                    $coursedataobj = iomad::get_user_course_license_data($searchinfo, $courseid, $page, $perpage, $showsuspended, $showused);
                }
                $coursedata = $coursedataobj->users;
                $totalcount = $coursedataobj->totalcount;
            }
        }

        if (empty($dodownload) && !$sendemail) {
            if ($courseid == 1) {
                echo "<h3>".get_string('reportallusers', 'local_report_license')."</h3>";
            } else {
                echo "<h3>".get_string('courseusers', 'local_report_license').$courseinfo[$courseid]->coursename."</h3>";
            }
        }
        $compusertable = new html_table();
        $compusertable->id = 'ReportTable';
    
        // Deal with table columns.
        if ($showused) {
            $columns = array('firstname',
                             'lastname',
                             'department',
                             'email',
                             'lastaccess',
                             'licensename',
                             'issuedate',
                             'coursename',
                             'isusing');
        } else {
            $columns = array('firstname',
                             'lastname',
                             'department',
                             'email',
                             'lastaccess',
                             'licensename',
                             'issuedate',
                             'coursename');
        }
    
        foreach ($columns as $column) {
            if ($column == 'lastaccess') {
                $string[$column] = get_string('lastaccess');
            } else if ($column == 'firstname') {
                $string[$column] = get_string('firstname');
            } else if ($column == 'lastname') {
                $string[$column] = get_string('lastname');
            } else {
                $string[$column] = get_string($column, 'local_report_license');
            }
            if ($sort != $column) {
                $columnicon = "";
                $columndir = "ASC";
            } else {
                $columndir = $dir == "ASC" ? "DESC":"ASC";
                $columnicon = $dir == "ASC" ? "down":"up";
                $columnicon = " <img src=\"" . $OUTPUT->image_url('t/' . $columnicon) . "\" alt=\"\" />";
    
            }
            $$column = $string[$column].$columnicon;
        }
    
        // Set up the course worksheet.
        if (!empty($dodownload)) {
   
            if ($courseid == 1) {
                echo get_string('allusers', 'local_report_license')."\n";
            } else {
                echo $courseinfo[$courseid]->coursename."\n";
            }
            if ($showused) {
                echo '"'.get_string('name', 'local_report_license').'","'
                     .get_string('email', 'local_report_license').'","'
                     .get_string('course').'","'
                     .get_string('department', 'block_iomad_company_admin').'","'
                     .get_string('lastaccess').'","'
                     .get_string('licensename', 'local_report_license').'","'
                     .get_string('issuedate', 'local_report_license').'","'
                     .get_string('isusing', 'local_report_license')."\"\n";
            } else {
                echo '"'.get_string('name', 'local_report_license').'","'
                     .get_string('email', 'local_report_license').'","'
                     .get_string('course').'","'
                     .get_string('department', 'block_iomad_company_admin').'","'
                     .get_string('lastaccess').'","'
                     .get_string('licensename', 'local_report_license').'","'
                     .get_string('issuedate', 'local_report_license')."\"\n";
            }
            $xlsrow = 1;
        }
        // Set the initial parameters for the table header links.
        $linkparams = $params;
    
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
            $linkparams['sort'] = 'lastaccess';
            $lastaccessurl = new moodle_url('index.php', $linkparams);
            $linkparams['sort'] = 'licensename';
            $licensenameurl = new moodle_url('index.php', $linkparams);
            $linkparams['sort'] = 'issuedate';
            $issuedateurl = new moodle_url('index.php', $linkparams);
            $linkparams['sort'] = 'coursename';
            $coursenameurl = new moodle_url('index.php', $linkparams);
            $linkparams['sort'] = 'isusing';
            $isusingurl = new moodle_url('index.php', $linkparams);

            // Set the options if there is already a sort defined.
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
                        $departmenturl = new moodle_url('index.php', $linkparams);
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
                        $lastaccessurl = new moodle_url('index.php', $linkparams);
                    } else {
                        $linkparams['dir'] = 'ASC';
                        $lastaccessurl = new moodle_url('index.php', $linkparams);
                    }
                } else if ($params['sort'] == 'licensename') {
                    $linkparams['sort'] = 'licensename';
                    if ($params['dir'] == 'ASC') {
                        $linkparams['dir'] = 'DESC';
                        $licensenameurl = new moodle_url('index.php', $linkparams);
                    } else {
                        $linkparams['dir'] = 'ASC';
                        $licensenameurl = new moodle_url('index.php', $linkparams);
                    }
                } else if ($params['sort'] == 'coursename') {
                    $linkparams['sort'] = 'coursename';
                    if ($params['dir'] == 'ASC') {
                        $linkparams['dir'] = 'DESC';
                        $coursenameurl = new moodle_url('index.php', $linkparams);
                    } else {
                        $linkparams['dir'] = 'ASC';
                        $coursenameurl = new moodle_url('index.php', $linkparams);
                    }
                } else if ($params['sort'] == 'issuedate') {
                    $linkparams['sort'] = 'issuedate';
                    if ($params['dir'] == 'ASC') {
                        $linkparams['dir'] = 'DESC';
                        $issuedateurl = new moodle_url('index.php', $linkparams);
                    } else {
                        $linkparams['dir'] = 'ASC';
                        $issuedateurl = new moodle_url('index.php', $linkparams);
                    }
                } else if ($params['sort'] == 'isusing') {
                    $linkparams['sort'] = 'isusing';
                    if ($params['dir'] == 'ASC') {
                        $linkparams['dir'] = 'DESC';
                        $isusingurl = new moodle_url('index.php', $linkparams);
                    } else {
                        $linkparams['dir'] = 'ASC';
                        $isusingurl = new moodle_url('index.php', $linkparams);
                    }
                }
            }
        }
        $fullnamedisplay = $output->action_link($firstnameurl, $firstname) ." / ". $output->action_link($lastnameurl, $lastname);
        if ($showused) {
            $compusertable->head = array ($fullnamedisplay,
                                          $output->action_link($emailurl, $email),
                                          get_string('course'),
                                          $output->action_link($departmenturl, $department),
                                          $output->action_link($lastaccessurl, $lastaccess),
                                          $output->action_link($licensenameurl, $licensename),
                                          $output->action_link($issuedateurl, $issuedate),
                                          $output->action_link($isusingurl, $isusing));
            $compusertable->align = array('center', 'center', 'center', 'center', 'center', 'center', 'center', 'center');
        } else {
            $compusertable->head = array ($fullnamedisplay,
                                          $output->action_link($emailurl, $email),
                                          get_string('course'),
                                          $output->action_link($departmenturl, $department),
                                          $output->action_link($lastaccessurl, $lastaccess),
                                          $output->action_link($licensenameurl, $licensename),
                                          $output->action_link($issuedateurl, $issuedate));
            $compusertable->align = array('center', 'center', 'center', 'center', 'center', 'center', 'center');
        }
        $compusertable->width = '95%';
    
        $userurl = '/local/report_users/userdisplay.php';
    
        // Paginate up the results.
    
        if (empty($idlist['0'])) {
            foreach ($coursedata as $user) {
                if ($user->isusing) {
                    $user->isusing = get_string('yes');
                } else {
                    $user->isusing = get_string('no');
                }
                if ($user->lastaccess == 0) {
                    $datestring = get_string('never');
                } else {
                    $datestring = date($CFG->iomad_date_format, $user->lastaccess);
                }
                $user->fullname = $user->firstname . ' ' . $user->lastname;
                if (empty($dodownload)) {
                    if ($showused) {
                        $compusertable->data[] = array("<a href='".new moodle_url($userurl,
                                                                                  array('userid' => $user->uid,
                                                                                        'courseid' => $courseid)).
                                                       "'>$user->fullname</a>",
                                                        $user->email,
                                                        $user->coursename,
                                                        $user->department,
                                                        $datestring,
                                                        $user->licensename,
                                                        date($CFG->iomad_date_format, $user->issuedate)."&nbsp; (".format_time(time() - $user->issuedate).")",
                                                        $user->isusing);
                    } else {
                        $compusertable->data[] = array("<a href='".new moodle_url($userurl,
                                                                                  array('userid' => $user->uid,
                                                                                        'courseid' => $courseid)).
                                                       "'>$user->fullname</a>",
                                                        $user->email,
                                                        $user->coursename,
                                                        $user->department,
                                                        $datestring,
                                                        $user->licensename,
                                                        date($CFG->iomad_date_format, $user->issuedate)."&nbsp; (".format_time(time() - $user->issuedate).")");
                    }
                } else {
                    if ($showused) {
                        echo '"'.$user->fullname.
                             '","'.$user->email.
                             '","'.$user->coursename.
                             '","'.$user->department.
                             '","'.$datestring.
                             '","'.$user->licensename.
                             '","'.date($CFG->iomad_date_format, $user->issuedate)." (".format_time(time() - $user->issuedate).")".
                             '","'.$user->isusing.
                             "\"\n";
                    } else {
                        echo '"'.$user->fullname.
                             '","'.$user->email.
                             '","'.$user->coursename.
                             '","'.$user->department.
                             '","'.$datestring.
                             '","'.$user->licensename.
                             '","'.date($CFG->iomad_date_format, $user->issuedate)." (".format_time(time() - $user->issuedate).")".
                             "\"\n";
                    }
                }
            }
        }
        if (empty($dodownload)) {
            // Set up the filter form.
            $mform = new iomad_user_filter_form(null, array('companyid' => $companyid));
            $mform->set_data(array('departmentid' => $departmentid));
            $mform->set_data($params);
            $mform->get_data();

            // Display the user filter form.
            $mform->display();

            // Display the paging bar.
            if (empty($idlist['0'])) {
                echo $output->paging_bar($totalcount, $page, $perpage, new moodle_url('/local/report_license/index.php', $params));
                echo "<br />";
            }
    
            // Display the user table.
            echo html_writer::table($compusertable);
            if (!empty($idlist['0'])) {
                echo "<h2>".$idlist['0']."</h2>";
            }
        }
    }
}
if (!empty($showchart)) {

    // Initialise the graph
    $pi = new pImage(PCHART_SIZEX, PCHART_SIZEY, $chartdata);
    $pi->drawRectangle(0, 0, PCHART_SIZEX-1, PCHART_SIZEY-1, array('R' => 0, 'G' => 0, 'B' => 0));

    if ($charttype == "summary") {

        // Bar chart
        $pi->setFontProperties(array(
            'FontName' => $CFG->dirroot . '/local/iomad/pchart2/fonts/verdana.ttf',
            'FontSize' => 10,
            'R' => 0, 'G' => 0, 'B' => 0,
        ));
        $pi->setGraphArea(50, 50, PCHART_SIZEX-50, PCHART_SIZEY-50);
        $pi->setShadow(false);
        $pi->drawScale(array('DrawSubTicks' => true));
        $pi->drawBarChart();
        $pi->autoOutput();
        exit;
    } else if ($charttype == "course") {

        // Pie chart
        $pp = new pPie($pi, $chartdata);
        $pi->setShadow(false);
        $pi->setFontProperties(array(
            'FontName' => $CFG->dirroot . '/local/iomad/pchart2/fonts/verdana.ttf',
            'FontSize' => 10,
            'R' => 0, 'G' => 0, 'B' => 0,
        ));
        $pp->draw3DPie(PCHART_SIZEX * 0.5, PCHART_SIZEY * 0.5, array(
            'Radius' => PCHART_SIZEX * 0.4,
            'DrawLabels' => true,
            'DataGapAngle' => 10,
            'DataGapRadius' => 6,
            'Border' => true,
        )); 
        $pp->drawPieLegend(10,PCHART_SIZEY-20, array(
            'Style' => LEGEND_BOX,
            'Mode' => LEGEND_HORIZONTAL,
        ));
        $pi->drawText(PCHART_SIZEX * 0.5, 10, 'Course completion', array(
            'Align' => TEXT_ALIGN_TOPMIDDLE,
        ));
        $pi->autoOutput();
        exit;
    }
}

if (empty($dodownload) && !empty($charttype)) {
    $params['showchart'] = true;
    echo "<center><img src='".new moodle_url('/local/report_license/index.php', $params)."'></center>";
}

if (!empty($dodownload)) {
    exit;
}
echo $output->footer();
