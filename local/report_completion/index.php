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
require_once(dirname(__FILE__).'/report_course_completion_course_table.php');
require_once(dirname(__FILE__).'/report_course_completion_user_table.php');
require_once($CFG->dirroot.'/blocks/iomad_company_admin/lib.php');
require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(__FILE__).'/locallib.php');

// chart stuff
define('PCHART_SIZEX', 500);
define('PCHART_SIZEY', 500);

// Params.
$courseid = optional_param('courseid', 0, PARAM_INT);
$participant = optional_param('participant', 0, PARAM_INT);
$download = optional_param('download', 0, PARAM_CLEAN);
$firstname       = optional_param('firstname', 0, PARAM_CLEAN);
$lastname      = optional_param('lastname', '', PARAM_CLEAN);
$showsuspended = optional_param('showsuspended', 0, PARAM_INT);
$showhistoric = optional_param('showhistoric', 1, PARAM_BOOL);
$email  = optional_param('email', 0, PARAM_CLEAN);
$timecreated  = optional_param('timecreated', 0, PARAM_CLEAN);
$sort         = optional_param('sort', '', PARAM_ALPHA);
$dir          = optional_param('dir', 'ASC', PARAM_ALPHA);
$page         = optional_param('page', 0, PARAM_INT);
$perpage      = optional_param('perpage', $CFG->iomad_max_list_users, PARAM_INT);        // How many per page.
$acl          = optional_param('acl', '0', PARAM_INT);           // Id of user to tweak mnet ACL (requires $access).
$coursesearch = optional_param('coursesearch', '', PARAM_CLEAN);// Search string.
$departmentid = optional_param('departmentid', 0, PARAM_INTEGER);
$completiontype = optional_param('completiontype', 0, PARAM_INT);
$charttype = optional_param('charttype', '', PARAM_CLEAN);
$showchart = optional_param('showchart', false, PARAM_BOOL);
$confirm = optional_param('confirm', false, PARAM_BOOL);
$fromraw = optional_param_array('compfromraw', null, PARAM_INT);
$toraw = optional_param_array('comptoraw', null, PARAM_INT);
$yearfrom = optional_param_array('fromarray', null, PARAM_INT);
$yearto = optional_param_array('toarray', null, PARAM_INT);
$showpercentage = optional_param('showpercentage', 0, PARAM_INT);
$submitbutton = optional_param('submitbutton', '', PARAM_CLEAN);
$validonly = optional_param('validonly', 0, PARAM_BOOL);

require_login($SITE);
$context = context_system::instance();
iomad::require_capability('local/report_completion:view', $context);

$params['courseid'] = $courseid;
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
if ($coursesearch) {
    $params['coursesearch'] = $coursesearch;
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
if ($completiontype) {
    $params['completiontype'] = $completiontype;
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
$params['showpercentage'] = $showpercentage;
$params['validonly'] = $validonly;

// Url stuff.
$url = new moodle_url('/local/report_completion/index.php', array('validonly' => $validonly));
$dashboardurl = new moodle_url('/my');

// Page stuff:.
$strcompletion = get_string('pluginname', 'local_report_completion');
$PAGE->set_url($url);
$PAGE->set_pagelayout('report');
$PAGE->set_title($strcompletion);
$PAGE->requires->css("/local/report_completion/styles.css");
$PAGE->requires->jquery();
if (empty($CFG->defaulthomepage)) {
    $PAGE->navbar->add(get_string('dashboard', 'block_iomad_company_admin'), new moodle_url($CFG->wwwroot . '/my'));
}
$PAGE->navbar->add($strcompletion, $url);
if (!empty($courseid)) {
    if ($courseid == 1) {
        $PAGE->navbar->add(get_string("allusers", 'local_report_completion'));
    } else {
        $course = $DB->get_record('course', array('id' => $courseid));
            $PAGE->navbar->add(format_string($course->fullname, true, 1));
    }
}

// Javascript for fancy select.
// Parameter is name of proper select form element followed by 1=submit its form
$PAGE->requires->js_call_amd('block_iomad_company_admin/department_select', 'init', array('departmentid', 1, optional_param('departmentid', 0, PARAM_INT)));

// Set the page heading.
$PAGE->set_heading(get_string('pluginname', 'block_iomad_reports') . " - $strcompletion");

// get output renderer
$output = $PAGE->get_renderer('block_iomad_company_admin');

// Set the companyid
$companyid = iomad::get_my_companyid($context);

// Work out department level.
$company = new company($companyid);
$parentlevel = company::get_company_parentnode($company->id);
$companydepartment = $parentlevel->id;

// Work out where the user sits in the company department tree.
$userlevel = $company->get_userlevel($USER);
$userhierarchylevel = $userlevel->id;
if ($departmentid == 0 ) {
    $departmentid = $userhierarchylevel;
}

// Get the company additional optional user parameter names.
$foundobj = iomad::add_user_filter_params($params, $companyid);
$idlist = $foundobj->idlist;
$foundfields = $foundobj->foundfields;

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

// Set up the user search parameters.
if ($courseid == 1) {
    $searchinfo = iomad::get_user_sqlsearch($params, $idlist, $sort, $dir, $departmentid, true, true);
} else {
    $searchinfo = iomad::get_user_sqlsearch($params, $idlist, $sort, $dir, $departmentid, false, false);
}

// Create data for filter form.
$customdata = null;

// Check the department is valid.
if (!empty($departmentid) && !company::check_valid_department($companyid, $departmentid)) {
    print_error('invaliddepartment', 'block_iomad_company_admin');
}

// Are we showing the overview table?
if (empty($courseid)) {
    // Set up the course display table.
    $coursetable = new local_report_course_completion_course_table('local_report_completion_course_table');
    $coursetable->is_downloading($download, 'local_report_course_completion_course', 'local_report_coursecompletion_course123');

    if (!$coursetable->is_downloading()) {

        // Display the header.
        echo $output->header();

        // What heading are we displaying?
        if (empty($courseid)) {
            if (empty($to) && empty($from)) {
                echo "<h3>".get_string('coursesummary', 'local_report_completion')."</h3>";
            } else {
                $fromstring = get_string('beginningoftime', 'local_report_completion');
                $tostring = get_string('now');
                if (!empty($from)) {
                    $fromstring = date($CFG->iomad_date_format,$from);
                }
                if (!empty($to)) {
                    $tostring= date($CFG->iomad_date_format,$to);
                }
                echo "<h3>".get_string('coursesummarywithdate', 'local_report_completion', array('from' => $fromstring, 'to' => $tostring))."</h3>";
            }
        } else if ($courseid == 1) {
            echo "<h3>".get_string('reportallusers', 'local_report_completion')."</h3>";
        } else {
            echo "<h3>".get_string('courseusers', 'local_report_completion').format_string($courseinfo[$courseid]->coursename, true, 1)."</h3>";
        }

        // Display the department selector.
        echo html_writer::start_tag('div', array('class' => 'iomadclear'));
        echo html_writer::start_tag('div', array('class' => 'fitem'));
        echo $treehtml;
        echo html_writer::start_tag('div', array('style' => 'display:none'));
        echo $fwselectoutput;
        echo html_writer::end_tag('div');
        echo html_writer::end_tag('div');
        echo html_writer::end_tag('div');
        echo html_writer::start_tag('div', array('class' => 'reporttablecontrols'));
        $mform = new iomad_course_search_form($url, $params);
        $mform->set_data($params);

        // Set up the date filter form.
        $datemform = new iomad_date_filter_form($url, $params);
        $datemform->set_data(array('departmentid' => $departmentid));
        $options = $params;
        $options['compfromraw'] = $from;
        $options['comptoraw'] = $to;
        $datemform->set_data($options);
        $datemform->get_data();

        // Display the control buttons.
        $alluserslink = new moodle_url($url, array(
            'courseid' => 1,
            'departmentid' => $departmentid,
        ));
        echo html_writer::start_tag('div', array('class' => 'reporttablecontrolscontrol'));
        echo $output->single_button($alluserslink, get_string("allusers", 'local_report_completion'));
        echo html_writer::end_tag('div');

        // Also for suspended user controls.
        $showsuspendedparams = $params;
        if (!$showsuspended) {
            $showsuspendedparams['showsuspended'] = 1;
            $suspendeduserslink = new moodle_url($url, $showsuspendedparams);
            echo html_writer::start_tag('div', array('class' => 'reporttablecontrolscontrol'));
            echo $output->single_button($suspendeduserslink, get_string("showsuspendedusers", 'local_report_completion'));
            echo html_writer::end_tag('div');
        } else {
            $showsuspendedparams['showsuspended'] = 0;
            $suspendeduserslink = new moodle_url($url, $showsuspendedparams);
            echo html_writer::start_tag('div', array('class' => 'reporttablecontrolscontrol'));
            echo $output->single_button($suspendeduserslink, get_string("hidesuspendedusers", 'local_report_completion'));
            echo html_writer::end_tag('div');
        }

        // Also for percentage of user controls.
        $showpercentageparams = $params;
        if (!$showpercentage) {
            $showpercentageparams['showpercentage'] = 1;
            $percentageuserslink = new moodle_url($url, $showpercentageparams);
            echo html_writer::start_tag('div', array('class' => 'reporttablecontrolscontrol'));
            echo $output->single_button($percentageuserslink, get_string("showpercentageusers", 'local_report_completion'));
            echo html_writer::end_tag('div');
        } else {
            $showpercentageparams['showpercentage'] = 0;
            $percentageuserslink = new moodle_url($url, $showpercentageparams);
            echo html_writer::start_tag('div', array('class' => 'reporttablecontrolscontrol'));
            echo $output->single_button($percentageuserslink, get_string("hidepercentageusers", 'local_report_completion'));
            echo html_writer::end_tag('div');
        }

        // Also for validonly courses user controls.
        $validonlyparams = $params;
        $validonlyparams['validonly'] = !$validonly;
        if (!$validonly) {
            $validonlystring = get_string('hidevalidcourses', 'block_iomad_company_admin');
        } else {
            $validonlystring = get_string('showvalidcourses', 'block_iomad_company_admin');
        }
        $validonlylink = new moodle_url($url, $validonlyparams);
        echo html_writer::start_tag('div', array('class' => 'reporttablecontrolscontrol'));
        echo $output->single_button($validonlylink, $validonlystring);
        echo html_writer::end_tag('div');

        echo html_writer::start_tag('div', array('class' => 'reporttablecontrolscontrol'));
        $mform->display();
        echo html_writer::end_tag('div');
        echo html_writer::start_tag('div', array('class' => 'reporttablecontrolscontrol'));
        $datemform->display();
        echo html_writer::end_tag('div');
        echo html_writer::end_tag('div');
    }

    // Deal with any course searches.
    $searchparams = array();
    if (!empty($coursesearch)) {
        $coursesearchsql = " AND courseid IN (" . join(',', array_keys($company->get_menu_courses(true))) . ") AND " . $DB->sql_like('coursename', ':coursename', false, false);
        $searchparams['coursename'] = "%" . $coursesearch . "%";
    } else {
        $coursesearchsql = " AND courseid IN (" . join(',', array_keys($company->get_menu_courses(true))) . ") ";
    }

    // Set up the SQL for the table.
    $selectsql = "courseid as id, coursename, $departmentid AS departmentid, $showsuspended AS showsuspended, companyid";
    $fromsql = "{local_iomad_track}";
    $sqlparams = array('companyid' => $companyid) + $searchparams;

    $wheresql = "companyid = :companyid $coursesearchsql group by courseid, coursename, companyid";

    // Set up the headers for the table.
    $courseheaders = array(get_string('coursename', 'local_report_completion'),
                     get_string('licenseallocated', 'local_report_user_license_allocations'),
                     get_string('usersummary', 'local_report_completion'));
    $coursecolumns = array('coursename',
                     'licenseallocated',
                     'usersummary');

    $coursetable->set_sql($selectsql, $fromsql, $wheresql, $sqlparams);
    $coursetable->define_baseurl($url);
    $coursetable->define_columns($coursecolumns);
    $coursetable->define_headers($courseheaders);
    $coursetable->no_sorting('licenseallocated');
    $coursetable->no_sorting('usersummary');
    $coursetable->sort_default_column = 'coursename';
    $coursetable->out($CFG->iomad_max_list_users, true);

    if (!$coursetable->is_downloading()) {
        echo $output->footer();
    }
} else {
    // Do we have any additional reporting fields?
    $extrafields = array();
    if (!empty($CFG->iomad_report_fields)) {
        $companyrec = $DB->get_record('company', array('id' => $companyid));
        foreach (explode(',', $CFG->iomad_report_fields) as $extrafield) {
            $extrafields[$extrafield] = new stdclass();
            $extrafields[$extrafield]->name = $extrafield;
            if (strpos($extrafield, 'profile_field') !== false) {
                // Its an optional profile field.
                $profilefield = $DB->get_record('user_info_field', array('shortname' => str_replace('profile_field_', '', $extrafield)));
                if ($profilefield->categoryid == $companyrec->profileid ||
                    !$DB->get_record('company', array('profileid' => $profilefield->categoryid))) {
                    $extrafields[$extrafield]->title = $profilefield->name;
                    $extrafields[$extrafield]->fieldid = $profilefield->id;
                } else {
                    unset($extrafields[$extrafield]);
                }
            } else {
                $extrafields[$extrafield]->title = get_string($extrafield);
            }
        }
    }

    // Set up the display table.
    $table = new local_report_course_completion_user_table('local_report_course_completion_user_table');
    $table->is_downloading($download, 'local_report_course_completion_user', 'local_report_coursecompletion_user123');

    // Deal with sort by course for all courses if sort is empty.
    if (empty($sort) && $courseid == 1) {
        $table->sort_default_column = 'coursename';
    }

    if (!$table->is_downloading()) {
        echo $output->header();

        // Display the search form and department picker.
        if (!empty($companyid)) {
            if (empty($table->is_downloading())) {
                echo html_writer::start_tag('div', array('class' => 'iomadclear'));
                echo html_writer::start_tag('div', array('class' => 'fitem'));
                echo $treehtml;
                echo html_writer::start_tag('div', array('style' => 'display:none'));
                echo $fwselectoutput;
                echo html_writer::end_tag('div');
                echo html_writer::end_tag('div');
                echo html_writer::end_tag('div');

                // Set up the filter form.
                $options = $params;
                $options['companyid'] = $companyid;
                $options['addfrom'] = 'compfromraw';
                $options['addto'] = 'comptoraw';
                $options['adddodownload'] = false;
                $options['compfromraw'] = $from;
                $options['comptoraw'] = $to;
                $options['addvalidonly'] = true;
                $mform = new iomad_user_filter_form(null, $options);
                $mform->set_data(array('departmentid' => $departmentid, 'validonly' => $validonly));
                $mform->set_data($options);
                $mform->get_data();

                // Display the user filter form.
                $mform->display();
            }
        }
    }

    $sqlparams = array('companyid' => $companyid, 'courseid' => $courseid);

    // Deal with where we are on the department tree.
    $currentdepartment = company::get_departmentbyid($departmentid);
    $showdepartments = company::get_subdepartments_list($currentdepartment);
    $showdepartments[$departmentid] = $departmentid;
    $departmentsql = " AND d.id IN (" . implode(',', array_keys($showdepartments)) . ")";

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

    // Deal with any search dates.
    $datesql = "";
    if (!empty($params['from'])) {
        $datesql = " AND (lit.timeenrolled > :enrolledfrom OR lit.timecompleted > :completedfrom ) ";
        $sqlparams['enrolledfrom'] = $params['from'];
        $sqlparams['completedfrom'] = $params['from'];
    }
    if (!empty($params['to'])) {
        $datesql .= " AND (lit.timeenrolled < :enrolledto OR lit.timecompleted < :completedto) ";
        $sqlparams['enrolledto'] = $params['to'];
        $sqlparams['completedto'] = $params['to'];
    }

    // Just valid courses?
    if ($validonly) {
        $validsql = " AND (lit.timeexpires > :runtime or (lit.timecompleted IS NULL) or (lit.timecompleted > 0 AND lit.timeexpires IS NULL))";
        $sqlparams['runtime'] = time();
    } else {
        $validsql = "";
    }

    // Set up the initial SQL for the form.
    $selectsql = "lit.id,u.id as userid,u.firstname,u.lastname,d.name AS department,u.email,lit.id as certsource, lit.courseid,lit.coursename,lit.timecompleted,lit.timeenrolled,lit.timestarted,lit.timeexpires,lit.finalscore,lit.licenseid,lit.licensename, lit.licenseallocated";
    $fromsql = "{user} u JOIN {local_iomad_track} lit ON (u.id = lit.userid) JOIN {company_users} cu ON (u.id = cu.userid AND lit.userid = cu.userid AND lit.companyid = cu.companyid) JOIN {department} d ON (cu.departmentid = d.id)";
    $wheresql = $searchinfo->sqlsearch . " AND cu.companyid = :companyid $departmentsql $companysql $datesql $coursesql $validsql";
    $sqlparams = $sqlparams + $searchinfo->searchparams;

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
                $fromsql .= " LEFT JOIN {user_info_data} P" . $extrafield->fieldid . " ON (u.id = P" . $extrafield->fieldid . ".userid AND P".$extrafield->fieldid . ".fieldid = :p" . $extrafield->fieldid . "fieldid )";
                $sqlparams["p".$extrafield->fieldid."fieldid"] = $extrafield->fieldid;
            }
        }
    }

    // Are we showing all courses?
    if ($courseid == 1) {
        $headers[] = get_string('course');
        $columns[] = 'coursename';
    }

    // Status column.
    $headers[] =  get_string('status');
    $columns[] = 'status';

    // Is this licensed?
    if ($courseid == 1 ||
        $DB->get_record('iomad_courses', array('courseid' => $courseid, 'licensed' => 1)) ||
        $DB->count_records_sql("SELECT count(id) FROM {local_iomad_track}
                                WHERE courseid = :courseid
                                AND licensename IS NOT NULL",
                                array('courseid' => $courseid)) > 0) {
        // Need to add the license columns
        $headers[] = get_string('licensename', 'block_iomad_company_admin');
        $headers[] = get_string('licensedateallocated', 'block_iomad_company_admin');
        $columns[] = 'licensename';
        $columns[] = 'licenseallocated';
    }

    // And enrolment columns.
    $headers[] = get_string('timestarted', 'local_report_completion');
    $headers[] = get_string('timecompleted', 'local_report_completion');
    $columns[] = 'timeenrolled';
    $columns[] = 'timecompleted';

    // Does this course have an expiry time?
    if (($courseid == 1 && $DB->get_records_sql("SELECT id FROM {iomad_courses} WHERE courseid IN (SELECT courseid FROM {local_iomad_track} WHERE companyid = :companyid) AND expireafter != 0", array('companyid' => $company->id))) ||
        $DB->get_record_sql("SELECT id FROM {iomad_courses} WHERE courseid = :courseid AND validlength > 0", array('courseid' => $courseid))) {
        $columns[] = 'timeexpires';
        $headers[] = get_string('timeexpires', 'local_report_completion');
    }

    // Does this course have an visible grade?
    if (($courseid == 1 && $DB->get_records_sql("SELECT id FROM {iomad_courses} WHERE courseid IN (SELECT courseid FROM {local_iomad_track} WHERE companyid = :companyid) AND hasgrade = 1", array('companyid' => $company->id))) ||
        $DB->get_record_sql("SELECT id FROM {iomad_courses} WHERE courseid = :courseid AND hasgrade = 1", array('courseid' => $courseid))) {
        $columns[] = 'finalscore';
        $headers[] = get_string('grade');
    }

    // And finally the last of the columns.
    if (!$table->is_downloading()) {
        $headers[] = get_string('certificate', 'local_report_completion');
        $columns[] = 'certificate';
    }

    // Set up the table and display it.
    $table->set_sql($selectsql, $fromsql, $wheresql, $sqlparams);
    $table->define_baseurl($url);
    $table->define_columns($columns);
    $table->define_headers($headers);
    $table->no_sorting('status');
    $table->no_sorting('certificate');
    $table->sort_default_column = 'lastname';
    $table->out($CFG->iomad_max_list_users, true);

    // End the page if appropriate.
    if (!$table->is_downloading()) {
        echo $output->footer();
    }
}
