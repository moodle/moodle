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
require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(__FILE__).'/locallib.php');

// chart stuff
define('PCHART_SIZEX', 500);
define('PCHART_SIZEY', 500);

// Params.
$courseid = optional_param('courseid', 0, PARAM_INT);
$participant = optional_param('participant', 0, PARAM_INT);
$dodownload = optional_param('dodownload', 0, PARAM_CLEAN);
$firstname       = optional_param('firstname', 0, PARAM_CLEAN);
$lastname      = optional_param('lastname', '', PARAM_CLEAN);
$showsuspended = optional_param('showsuspended', 0, PARAM_INT);
$showhistoric = optional_param('showhistoric', 0, PARAM_BOOL);
$email  = optional_param('email', 0, PARAM_CLEAN);
$timecreated  = optional_param('timecreated', 0, PARAM_CLEAN);
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
$confirm = optional_param('confirm', false, PARAM_BOOL);

require_login($SITE);
$context = context_system::instance();
iomad::require_capability('local/report_completion:view', $context);

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
if ($showhistoric) {
    $params['showhistoric'] = $showhistoric;
}
if ($charttype) {
    $params['charttype'] = $charttype;
}
if ($completiontype) {
    $params['completiontype'] = $completiontype;
}

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
    if (!empty($compfrom)) {
        $compto = time();
        $params['compto'] = $compto;
    } else {
        $compto = 0;
    }
}

// Url stuff.
$url = new moodle_url('/local/report_completion/index.php');
$dashboardurl = new moodle_url('/my');

// Page stuff:.
$strcompletion = get_string('pluginname', 'local_report_completion');
$PAGE->set_url($url);
$PAGE->set_pagelayout('report');
$PAGE->set_title($strcompletion);
$PAGE->requires->css("/local/report_completion/styles.css");
$PAGE->requires->jquery();

// get output renderer                                                                                                                                                                                         
$output = $PAGE->get_renderer('block_iomad_company_admin');

// Javascript for fancy select.
// Parameter is name of proper select form element followed by 1=submit its form
$PAGE->requires->js_call_amd('block_iomad_company_admin/department_select', 'init', array('departmentid', 1, optional_param('departmentid', 0, PARAM_INT)));

// Set the page heading.
$PAGE->set_heading(get_string('pluginname', 'block_iomad_reports') . " - $strcompletion");

// Get the renderer.
$output = $PAGE->get_renderer('block_iomad_company_admin');

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

$url = new moodle_url('/local/report_completion/index.php', $params);

// Get the appropriate list of departments.
$userdepartment = $company->get_userlevel($USER);
$departmenttree = company::get_all_subdepartments_raw($userdepartment->id);
$treehtml = $output->department_tree($departmenttree, optional_param('departmentid', 0, PARAM_INT));
$selectparams = $params;
$selecturl = new moodle_url('/local/report_completion/index.php', $selectparams);
$subhierarchieslist = company::get_all_subdepartments($userhierarchylevel);
$select = new single_select($selecturl, 'departmentid', $subhierarchieslist, $departmentid);
$select->label = get_string('department', 'block_iomad_company_admin') . "&nbsp";
$select->formid = 'choosedepartment';

$departmenttree = company::get_all_subdepartments_raw($userhierarchylevel);
$treehtml = $output->department_tree($departmenttree, optional_param('departmentid', 0, PARAM_INT));
$fwselectoutput = html_writer::tag('div', $output->render($select), array('id' => 'iomad_department_selector', 'style' => 'display: none;'));

// Get the appropriate list of departments.
$selectparams = $params;
$selecturl = new moodle_url('/local/report_completion/index.php', $selectparams);
$completiontypelist = array('0' => get_string('all'),
                            '1' => get_string('notstartedusers', 'local_report_completion'),
                            '2' => get_string('inprogressusers', 'local_report_completion'),
                            '3' => get_string('completedusers', 'local_report_completion'));
$select = new single_select($selecturl, 'completiontype', $completiontypelist, $completiontype);
$select->label = get_string('choosecompletiontype', 'block_iomad_company_admin') . "&nbsp";
$select->formid = 'choosecompletiontype';
$completiontypeselectoutput = html_writer::tag('div', $output->render($select), array('id' => 'iomad_completiontype_selector'));

//if (!(iomad::has_capability('block/iomad_company_admin:editusers', $context) or
//      iomad::has_capability('block/iomad_company_admin:editallusers', $context))) {
//    print_error('nopermissions', 'error', '', 'report on users');
//}

if ($courseid == 1) {
	$searchinfo = iomad::get_user_sqlsearch($params, $idlist, $sort, $dir, $departmentid, true, true);
} else {
	$searchinfo = iomad::get_user_sqlsearch($params, $idlist, $sort, $dir, $departmentid, false, false);
}

// Create data for form.
$customdata = null;
$options = $params;
$options['dodownload'] = 1;

// Only print the header if we are not downloading.
if (empty($dodownload) && empty($showchart)) {
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

$courseinfo = report_completion::get_course_summary_info ($departmentid, 0, $showsuspended);
if (empty($dodownload) && empty($showchart)) {
    if (empty($courseid)) {
        echo "<h3>".get_string('coursesummary', 'local_report_completion')."</h3>";
    } else if ($courseid == 1) {
        echo "<h3>".get_string('reportallusers', 'local_report_completion')."</h3>";
    } else {
        echo "<h3>".get_string('courseusers', 'local_report_completion').$courseinfo[$courseid]->coursename."</h3>";
    }

    if (!empty($companyid) && !empty($courseid)) {
        echo html_writer::start_tag('div', array('class' => 'iomadclear'));
        echo html_writer::start_tag('div', array('class' => 'fitem'));
        echo $treehtml;
        echo html_writer::start_tag('div', array('style' => 'display:none'));
        echo $fwselectoutput;
        echo html_writer::end_tag('div');
        echo html_writer::end_tag('div');
        echo html_writer::end_tag('div');
        echo html_writer::start_tag('div', array('class' => 'iomadclear', 'style' => 'padding-top: 5px;'));
        echo html_writer::start_tag('div', array('style' => 'float:left;'));
        echo $completiontypeselectoutput;
        echo html_writer::end_tag('div');
        echo html_writer::end_tag('div');
    }
    if (!empty($courseid)) {
        $options['charttype'] = 'summary';
        $options['dodownload'] = false;
    } else {
        $options['charttype'] = 'summary';
        $options['dodownload'] = false;
        $alluserslink = new moodle_url($url, array(
            'courseid' => 1,
            'departmentid' => $departmentid,
            'showchart' => 0,
            'charttype' => '',
        ));
        echo $output->single_button($alluserslink, get_string("allusers", 'local_report_completion'));
        if (!$showhistoric) {
            $historicuserslink = new moodle_url($url, array('departmentid' => $departmentid,
                                                            'showchart' => 0,
                                                            'charttype' => '',
                                                            'showhistoric' => 1,
                                                            'showsuspended' => $showsuspended
                                                            ));
            echo $output->single_button($historicuserslink, get_string("historicusers", 'local_report_completion'));
        } else {
            $historicuserslink = new moodle_url($url, array('departmentid' => $departmentid,
                                                            'showchart' => 0,
                                                            'charttype' => '',
                                                            'showhistoric' => 0,
                                                            'showsuspended' => $showsuspended
                                                            ));
            echo $output->single_button($historicuserslink, get_string("hidehistoricusers", 'local_report_completion'));
        }
        if (!$showsuspended) {
            $suspendeduserslink = new moodle_url($url, array('departmentid' => $departmentid,
                                                             'showchart' => 0,
                                                             'charttype' => '',
                                                             'showhistoric' => $showhistoric,
                                                             'showsuspended' => 1
                                                            ));
            echo $output->single_button($suspendeduserslink, get_string("showsuspendedusers", 'local_report_completion'));
        } else {
            $suspendeduserslink = new moodle_url($url, array('departmentid' => $departmentid,
                                                             'showchart' => 0,
                                                             'charttype' => '',
                                                             'showhistoric' => $showhistoric,
                                                             'showsuspended' => 0
                                                            ));
            echo $output->single_button($suspendeduserslink, get_string("hidesuspendedusers", 'local_report_completion'));
        }
    }

}

// Set up the course overview table.
$coursecomptable = new html_table();
$coursecomptable->id = 'ReportTable';
if (!$showhistoric) {
    $coursecomptable->head = array(
        get_string('coursename', 'local_report_completion'),
        get_string('numusers', 'local_report_completion'),
        get_string('notstartedusers', 'local_report_completion'),
        get_string('inprogressusers', 'local_report_completion'),
        get_string('completedusers', 'local_report_completion'),
        ' ',
    );
    $coursecomptable->align = array('left', 'center', 'center', 'center', 'center', 'center');
} else {
    $coursecomptable->head = array(
        get_string('coursename', 'local_report_completion'),
        get_string('numusers', 'local_report_completion'),
        get_string('notstartedusers', 'local_report_completion'),
        get_string('inprogressusers', 'local_report_completion'),
        get_string('completedusers', 'local_report_completion'),
        get_string('historiccompletedusers', 'local_report_completion'),
        ' ',
    );
    $coursecomptable->align = array('left', 'center', 'center', 'center', 'center', 'center', 'center');
}
//$coursecomptable->width = '95%';
$chartdata = array();

if (!empty($dodownload)) {
    // Set up the Excel workbook.

    header("Content-Type: application/download\n");
    header("Content-Disposition: attachment; filename=\"coursereport.csv\"");
    header("Expires: 0");
    header("Cache-Control: must-revalidate,post-check=0,pre-check=0");
    header("Pragma: public");

}
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
    if (!$showhistoric) {
        $coursecomptable->data[] = array(
            $coursedata->coursename,
            $coursedata->numenrolled,
            $coursedata->numnotstarted,
            $coursedata->numstarted - $coursedata->numcompleted,
            $coursedata->numcompleted,
            '<a class="btn" style="margin:2px" href="' . $courseuserslink . '">' . get_string('usersummary', 'local_report_completion') . '</a>&nbsp;',
            //'<a class="btn" style="margin:2px" href="' . $coursechartlink . '">' . get_string('cchart', 'local_report_completion') . '</a>',
        );
    } else {
        $coursecomptable->data[] = array(
            $coursedata->coursename,
            $coursedata->numenrolled,
            $coursedata->numnotstarted,
            $coursedata->numstarted - $coursedata->numcompleted,
            $coursedata->numcompleted,
            $coursedata->historic,
            '<a class="btn" style="margin:2px" href="' . $courseuserslink . '">' . get_string('usersummary', 'local_report_completion') . '</a>&nbsp;',
            //'<a class="btn" style="margin:2px" href="' . $coursechartlink . '">' . get_string('cchart', 'local_report_completion') . '</a>',
        );
    }
    if ($charttype == 'summary') {
        $chartname[] = $coursedata->coursename;
        $chartnumusers[] = $coursedata->numenrolled;
        $chartnotstarted[] = $coursedata->numnotstarted;
        $chartinprogress[] = $coursedata->numstarted - $coursedata->numcompleted;
        $chartcompleted[] = $coursedata->numcompleted;
        if ($showhistoric) {
            $charthistoric = $coursedata->historic;
        }
    } else if ($charttype == 'course' && $courseid == $coursedata->id ) {
        $seriesdata = array($coursedata->numnotstarted,
                            $coursedata->numstarted - $coursedata->numcompleted,
                            $coursedata->numcompleted);
        if ($showhistoric) {
            $seriesdata = $seriesdata + array($coursedata->historic);
        }
    }
}

if (!empty($charttype)) {
    $chartdata = new pData();
    if ($charttype == 'summary') {
        $chartdata->addPoints($chartnotstarted, 's_notstarted' );
        $chartdata->addPoints($chartinprogress, 's_inprogress' );
        $chartdata->addPoints($chartcompleted, 's_completed' );
        if ($showhistoric) {
            $chartdata->addPoints($charthistoric, 's_completed' );
        }
    } else if ($charttype == 'course') {
        $chartdata->addPoints($seriesdata, 'Value');
    }
    if (!showhistoric) {
        $chartdata->addPoints(array(
            get_string('notstartedusers', 'local_report_completion'),
            get_string('inprogressusers', 'local_report_completion'),
            get_string('completedusers', 'local_report_completion'),
        ), 'Legend');
    } else {
        $chartdata->addPoints(array(
            get_string('notstartedusers', 'local_report_completion'),
            get_string('inprogressusers', 'local_report_completion'),
            get_string('completedusers', 'local_report_completion'),
            get_string('historicusers', 'local_report_completion'),
        ), 'Legend');
    }
    $chartdata->setAbscissa('Legend');
}

if (empty($dodownload) && empty($showchart)) {
    if (empty($courseid)) {
        echo html_writer::table($coursecomptable);
    }
}

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

if (empty($charttype)) {
    if (!empty($courseid)) {
        // Get the course completion information.
        $showexpiry = true;
        if ($iomadcourseinfo = $DB->get_record('iomad_courses', array('courseid' => $courseid))) {
            if (!empty($iomadcourseinfo->validlength)) {
                $showexpiry = true;
            }
        }
        if (empty($dodownload)) {
            if (empty($idlist['0'])) {
                // Only want the data for the page we are on.
                // courseid==1 is ALL users.
                if ($courseid == 1) {
                    $coursedataobj = report_completion::get_all_user_course_completion_data($searchinfo, $page, $perpage, $completiontype, $showhistoric);
                } else {
                    $coursedataobj = report_completion::get_user_course_completion_data($searchinfo, $courseid, $page, $perpage, $completiontype, $showhistoric);
                }
                $coursedata = $coursedataobj->users;
                $totalcount = $coursedataobj->totalcount;
            }
        } else {
            if (empty($idlist['0'])) {
                if ($courseid == 1) {
                    $coursedataobj = report_completion::get_all_user_course_completion_data($searchinfo, 0, 0, 0, $showhistoric);
                } else {
                    $coursedataobj = report_completion::get_user_course_completion_data($searchinfo, $courseid, 0, 0, 0, $showhistoric);
                }
                $coursedata = $coursedataobj->users;
                $totalcount = $coursedataobj->totalcount;
            }
        }
    
        // Check if there is a certificate module.
        $hascertificate = false;
        if (empty($dodownload) && $certmodule = $DB->get_record('modules', array('name' => 'iomadcertificate'))) {
            require_once($CFG->dirroot.'/mod/iomadcertificate/lib.php');
            if ($certificateinfo = $DB->get_record('iomadcertificate', array('course' => $courseid))) {
                if ($certificatemodinstance = $DB->get_record('course_modules', array('course' => $courseid,
                                                                                      'module' => $certmodule->id,
                                                                                      'instance' => $certificateinfo->id))) {
                    $certificatecontext = context_module::instance($certificatemodinstance->id);
                    $hascertificate = true;
                }
            }
        }
        $compusertable = new html_table();
    
        // Deal with table columns.
        $startcolumns = array('firstname' => 'firstname',
                              'lastname' => 'lastname',
                              'department' => 'department',
                              'email' => 'email');

        if (!$showexpiry) {
            $endcolumns = array('status' => 'status',
                                'timeenrolled' => 'timeenrolled',
                                'timestarted' => 'timestarted',
                                'timecompleted' => 'timecompleted',
                                'finalscore' => 'finalscore');
        } else {
            $endcolumns = array('status' => 'status',
                                'timeenrolled' => 'timeenrolled',
                                'timestarted' => 'timestarted',
                                'timecompleted' => 'timecompleted',
                                'timeexpires' => 'timeexpires',
                                'finalscore' => 'finalscore');
        }

        $columns = $startcolumns + $endcolumns;
        foreach ($columns as $column) {
            if ($column != 'timeexpires') {
                $string[$column] = get_string($column, 'local_report_completion');
                if ($sort != $column) {
                    $columnicon = "";
                    $columndir = "ASC";
                } else {
                    $columndir = $dir == "ASC" ? "DESC":"ASC";
                    $columnicon = $dir == "ASC" ? "down":"up";
                    $columnicon = " <img src=\"" . $output->image_url('t/' . $columnicon) . "\" alt=\"\" />";

                }
                $$column = $string[$column].$columnicon;
            } else {
                $$column = get_string($column, 'local_report_completion');
            }
        }
    
        // Set up the course worksheet.
        if (!empty($dodownload)) {
   
            if ($courseid == 1) {
                echo get_string('allusers', 'local_report_completion')."\n";
            } else {
                echo $courseinfo[$courseid]->coursename."\n";
            }
            $startcolumns = '"'.get_string('name', 'local_report_completion').'","'
                            .get_string('email', 'local_report_completion').'","'
                            .get_string('course').'","'
                            .get_string('department', 'block_iomad_company_admin').'",';
            if (!$showexpiry) {
                $endcolumns = '"' . get_string('status', 'local_report_completion').'","'
                              .get_string('timeenrolled', 'local_report_completion').'","'
                              .get_string('timestarted', 'local_report_completion').'","'
                              .get_string('timecompleted', 'local_report_completion').'","'
                              .get_string('finalscore', 'local_report_completion')."\"\n";
            } else {
                $endcolumns = '"' . get_string('status', 'local_report_completion').'","'
                              .get_string('timeenrolled', 'local_report_completion').'","'
                              .get_string('timestarted', 'local_report_completion').'","'
                              .get_string('timecompleted', 'local_report_completion').'","'
                              .get_string('timeexpires', 'local_report_completion').'","'
                              .get_string('finalscore', 'local_report_completion')."\"\n";
            }
            $midcolumns = "";
            if (!empty($extrafields)) {
                foreach ($extrafields as $extrafield) {
                    $midcolumns .= '"' . $extrafield->title . '",';
                }
            }

            echo $startcolumns . $midcolumns . $endcolumns;
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
            $linkparams['sort'] = 'status';
            $statusurl = new moodle_url('index.php', $linkparams);
            $linkparams['sort'] = 'timestarted';
            $timestartedurl = new moodle_url('index.php', $linkparams);
            $linkparams['sort'] = 'timecompleted';
            $timecompletedurl = new moodle_url('index.php', $linkparams);
            $linkparams['sort'] = 'finalscore';
            $finalscoreurl = new moodle_url('index.php', $linkparams);
            $linkparams['sort'] = 'timeenrolled';
            $timeenrolledurl = new moodle_url('index.php', $linkparams);
    
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
                } else if ($params['sort'] == 'status') {
                    $linkparams['sort'] = 'status';
                    if ($params['dir'] == 'ASC') {
                        $linkparams['dir'] = 'DESC';
                        $statusurl = new moodle_url('index.php', $linkparams);
                    } else {
                        $linkparams['dir'] = 'ASC';
                        $statusurl = new moodle_url('index.php', $linkparams);
                    }
                } else if ($params['sort'] == 'timestarted') {
                    $linkparams['sort'] = 'timestarted';
                    if ($params['dir'] == 'ASC') {
                        $linkparams['dir'] = 'DESC';
                        $datestartedurl = new moodle_url('index.php', $linkparams);
                    } else {
                        $linkparams['dir'] = 'ASC';
                        $datestartedurl = new moodle_url('index.php', $linkparams);
                    }
                } else if ($params['sort'] == 'timeenrolled') {
                    $linkparams['sort'] = 'timeenrolled';
                    if ($params['dir'] == 'ASC') {
                        $linkparams['dir'] = 'DESC';
                        $dateenrolledurl = new moodle_url('index.php', $linkparams);
                    } else {
                        $linkparams['dir'] = 'ASC';
                        $dateenrolledurl = new moodle_url('index.php', $linkparams);
                    }
                } else if ($params['sort'] == 'timecompleted') {
                    $linkparams['sort'] = 'timecompleted';
                    if ($params['dir'] == 'ASC') {
                        $linkparams['dir'] = 'DESC';
                        $datecompletedurl = new moodle_url('index.php', $linkparams);
                    } else {
                        $linkparams['dir'] = 'ASC';
                        $datecompletedurl = new moodle_url('index.php', $linkparams);
                    }
                } else if ($params['sort'] == 'finalscore') {
                    $linkparams['sort'] = 'finalscore';
                    if ($params['dir'] == 'ASC') {
                        $linkparams['dir'] = 'DESC';
                        $finalscoreurl = new moodle_url('index.php', $linkparams);
                    } else {
                        $linkparams['dir'] = 'ASC';
                        $finalscoreurl = new moodle_url('index.php', $linkparams);
                    }
                }
            }
        }
        $fullnamedisplay = $output->action_link($firstnameurl, get_string('name')); //." / ". $OUTPUT->action_link($lastnameurl, $lastname);
    
        $headstart = array($fullnamedisplay => $fullnamedisplay,
                           $email => $output->action_link($emailurl, $email),
                           'course' => get_string('course'),
                           $department => $output->action_link($departmenturl, $department));
        $headmid = array();
        if (!empty($extrafields)) {
            foreach ($extrafields as $extrafield) {
                $headmid[$extrafield->name] = $extrafield->title;
            }
        }
        if (!$showexpiry) {
            $headend = array ($timeenrolled => $output->action_link($timeenrolledurl, $timeenrolled),
                              $status => $output->action_link($statusurl, $status),
                              $timestarted => $output->action_link($timestartedurl, $timestarted),
                              $timecompleted => $output->action_link($timecompletedurl, $timecompleted),
                              $finalscore =>$finalscore);
            $compusertable->align = array('center', 'center', 'center', 'center', 'center', 'center', 'center', 'center', 'center');
        } else {
            $headend = array ($timeenrolled => $output->action_link($timeenrolledurl, $timeenrolled),
                              $status => $output->action_link($statusurl, $status),
                              $timestarted => $output->action_link($timestartedurl, $timestarted),
                              $timecompleted => $output->action_link($timecompletedurl, $timecompleted),
                              $timeexpires => $timeexpires,
                              $finalscore =>$finalscore);
            $compusertable->align = array('center', 'center', 'center', 'center', 'center', 'center', 'center', 'center', 'center', 'center');
        }
        $compusertable->head = $headstart + $headmid + $headend;
		$compusertable->id = 'ReportTable';
        if ($hascertificate) {
            $compusertable->head[] = get_string('certificate', 'local_report_completion');
            $compusertable->align[] = 'center';
        }
    
        $userurl = '/local/report_users/userdisplay.php';
    
        // Paginate up the results.
    
        if (empty($idlist['0'])) {
            foreach ($coursedata as $userid => $user) {

                // Get the course info if it's all of them.
                if ($courseid == 1) {
                    if (!$iomadcourseinfo = $DB->get_record('iomad_courses', array('courseid' => $user->courseid))) {
                        $iomadcourseinfo = new stdclass();
                    }
                }

                if (empty($user->timestarted)) {
                    $statusstring = get_string('notstarted', 'local_report_completion');
                } else {
                    $statusstring = get_string('started', 'local_report_completion');
                }
                if (!empty($user->timecompleted)) {
                    $statusstring = get_string('completed', 'local_report_completion');
                }

                // Get the completion date information.
                if (!empty($user->timestarted)) {
                    $starttime = date($CFG->iomad_date_format, $user->timestarted);
                } else {
                    $starttime = "-";
                }
                if (!empty($user->timeenrolled)) {
                    $enrolledtime = date($CFG->iomad_date_format, $user->timeenrolled);
                } else {
                    $enrolledtime = "-";
                }
                if (!empty($user->timecompleted)) {
                    $completetime = date($CFG->iomad_date_format, $user->timecompleted);
                } else {
                    $completetime = "-";
                }
    
                if ($showexpiry && !empty($user->timecompleted) && !empty($iomadcourseinfo->validlength)) {
                    $expirytime = date($CFG->iomad_date_format, $user->timecompleted + ($iomadcourseinfo->validlength * 24 * 60 * 60) );
                } else {
                    $expirytime = "-";
                }

                // Score information.
                if (!empty($user->result)) {
                    $scorestring = round($user->result, 0)."%";
                } else {
                    $scorestring = "-";
                }

                // load the full user profile.
                $fulluser = $DB->get_record('user', array('id' => $user->uid));
                profile_load_data($fulluser);
                $user->fullname = fullname($fulluser);

                // Deal with the certificate.
                if ($hascertificate) {
                    // Check if user has completed the course - if so, show the certificate.
                    if (!empty($user->timecompleted) ) {
                        // Get the course module.
                        if (empty($user->certsource)) {
                            $certtabledata = "<a class=\"btn\" href='".new moodle_url('/mod/iomadcertificate/view.php',
                                                                         array('id' => $certificatemodinstance->id,
                                                                               'action' => 'get',
                                                                               'userid' => $user->uid,
                                                                               'sesskey' => sesskey()))."'>".
                                              get_string('downloadcert', 'local_report_users')."</a>";
                        } else {
                            // Get the certificate from the download files thing.
                            if ($traccertrec = $DB->get_record('local_iomad_track_certs', array('trackid' => $user->certsource))) {
                                // create the file download link.
                                $coursecontext = context_course::instance($courseid);
                                $certtabledata = "<a class=\"btn btn-info\" href='".
                                               moodle_url::make_file_url('/pluginfile.php', '/'.$coursecontext->id.'/local_iomad_track/issue/'.$traccertrec->trackid.'/'.$traccertrec->filename) .
                                              "'>" . get_string('downloadcert', 'local_report_users').
                                              "</a>";
                            }
                        }
                    } else {
                        $certtabledata = get_string('nocerttodownload', 'local_report_users');
                    }

                    $rowstart = array('fullname' => "<a href='".new moodle_url($userurl, array('userid' => $user->uid,
                                                                                 'courseid' => $courseid)).
                                                    "'>$user->fullname</a>",
                                      'email' => $user->email,
                                      'coursename' => $user->coursename,
                                      'department' => $user->department);
                    $rowmid = array();
                    if (!empty($extrafields)) {
                        foreach($extrafields as $extrafield) {
                            $fieldname = $extrafield->name;
                            $rowmid[$extrafield->name] = $fulluser->$fieldname;
                        }
                    }
                    if (!$showexpiry) {
                        $rowend = array('enrolledtime' => $enrolledtime,
                                        'statusstring' => $statusstring,
                                        'starttime' => $starttime,
                                        'completetime' => $completetime,
                                        'scorestring' => $scorestring,
                                        'certtabledata' => $certtabledata);
                    } else {
                        $rowend = array('enrolledtime' => $enrolledtime,
                                        'statusstring' => $statusstring,
                                        'starttime' => $starttime,
                                        'completetime' => $completetime,
                                        'expirytime' => $expirytime,
                                        'scorestring' => $scorestring,
                                        'certtabledata' => $certtabledata);
                    }
                    $compusertable->data[] = $rowstart + $rowmid + $rowend;
                } else {
                    $rowstart = array('fullname' => "<a href='".new moodle_url($userurl, array('userid' => $user->uid,
                                                                                 'courseid' => $courseid)).
                                                    "'>$user->fullname</a>",
                                      'email' => $user->email,
                                      'coursename' => $user->coursename,
                                      'department' => $user->department);
                    $rowmid = array();
                    if (!empty($extrafields)) {
                        foreach($extrafields as $extrafield) {
                            $fieldname = $extrafield->name;
                            $rowmid[$extrafield->name] = $fulluser->$fieldname;
                        }
                    }
                    if (!$showexpiry) {
                        $rowend = array('enrolledtime' => $enrolledtime,
                                        'statusstring' => $statusstring,
                                        'starttime' => $starttime,
                                        'completetime' => $completetime,
                                        'scorestring' => $scorestring);
                    } else {
                        $rowend = array('enrolledtime' => $enrolledtime,
                                        'statusstring' => $statusstring,
                                        'starttime' => $starttime,
                                        'completetime' => $completetime,
                                        'expirytime' => $expirytime,
                                        'scorestring' => $scorestring);
                    }
                    $compusertable->data[] = $rowstart + $rowmid + $rowend;
                }
                if (!empty($dodownload)) {
                    $rowstart = '"'.$user->fullname.
                                '","'.$user->email.
                                '","'.$user->coursename.
                                '","'.$user->department;
                              
                    $rowmid = '';
                    if (!empty($extrafields)) {
                        foreach($extrafields as $extrafield) {
                            $fieldname = $extrafield->name;
                            $rowmid .= '","'.$fulluser->$fieldname;
                        }
                    }
                    if (!$showexpiry) {
                        $rowend = '","'.$statusstring.
                                  '","'.$enrolledtime.
                                  '","'.$starttime.
                                  '","'.$completetime.
                                  '","'.$scorestring.
                                  "\"\n";
                    } else {
                        $rowend = '","'.$statusstring.
                                  '","'.$enrolledtime.
                                  '","'.$starttime.
                                  '","'.$completetime.
                                  '","'.$expirytime.
                                  '","'.$scorestring.
                                  "\"\n";
                    }
                    echo $rowstart . $rowmid . $rowend;
                }
            }
        }
        if (empty($dodownload)) {
            // Set up the filter form.
            $mform = new iomad_user_filter_form(null, array('companyid' => $companyid, 'showhistoric' => true, 'addfrom' => 'compfrom', 'addto' => 'compto', 'adddodownload' => true));

            $mform->set_data(array('departmentid' => $departmentid));
            $mform->set_data($params);
            $mform->get_data();
    
            // Display the user filter form.
            echo html_writer::start_tag('div', array('class' => 'iomadclear'));
            $mform->display();
            echo html_writer::end_tag('div');
    
            // Display the paging bar.
            if (empty($idlist['0'])) {
                echo $output->paging_bar($totalcount, $page, $perpage, new moodle_url('/local/report_completion/index.php', $params));
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
    echo "<center><img src='".new moodle_url('/local/report_completion/index.php', $params)."'></center>";
}

if (!empty($dodownload)) {
    exit;
}
echo $output->footer();
