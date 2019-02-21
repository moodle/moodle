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
require_once(dirname(__FILE__).'/report_emails_table.php');
require_once($CFG->dirroot.'/blocks/iomad_company_admin/lib.php');
require_once($CFG->dirroot."/lib/tablelib.php");

// Params.
$participant = optional_param('participant', 0, PARAM_INT);
$download = optional_param('download', 0, PARAM_CLEAN);
$firstname       = optional_param('firstname', 0, PARAM_CLEAN);
$lastname      = optional_param('lastname', '', PARAM_CLEAN);
$showsuspended = optional_param('showsuspended', 0, PARAM_INT);
$email  = optional_param('email', 0, PARAM_CLEAN);
$sort         = optional_param('sort', 'lastname', PARAM_ALPHA);
$dir          = optional_param('dir', 'ASC', PARAM_ALPHA);
$page         = optional_param('page', 0, PARAM_INT);
$perpage      = optional_param('perpage', $CFG->iomad_max_list_users, PARAM_INT);        // How many per page.
$acl          = optional_param('acl', '0', PARAM_INT);           // Id of user to tweak mnet ACL (requires $access).
$search      = optional_param('search', '', PARAM_CLEAN);// Search string.
$departmentid = optional_param('departmentid', 0, PARAM_INTEGER);
$emailfromraw = optional_param_array('emailfrom', null, PARAM_INT);
$emailtoraw = optional_param_array('emailto', null, PARAM_INT);
$confirm = optional_param('confirm', '', PARAM_CLEAN);
$emailid = optional_param('emailid', 0, PARAM_INT);

require_login($SITE);
$systemcontext = context_system::instance();
iomad::require_capability('local/report_emails:view', $systemcontext);

if (!empty($download)) {
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

if ($emailfromraw) {
    if (is_array($emailfromraw)) {
        $emailfrom = mktime(0, 0, 0, $emailfromraw['month'], $emailfromraw['day'], $emailfromraw['year']);
    } else {
        $emailfrom = $emailfromraw;
    }
    $params['emailfrom'] = $emailfrom;
} else {
    $emailfrom = null;
}

if ($emailtoraw) {
    if (is_array($emailtoraw)) {
        $emailto = mktime(0, 0, 0, $emailtoraw['month'], $emailtoraw['day'], $emailtoraw['year']);
    } else {
        $emailto = $emailtoraw;
    }
    $params['emailto'] = $emailto;
} else {
    if (!empty($comptfrom)) {
        $emailto = time();
        $params['emailto'] = $emailto;
    } else {
        $emailto = null;
    }
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
$url = new moodle_url('/local/report_emails/index.php');
$dashboardurl = new moodle_url('/my');

// Page stuff:.
$strcompletion = get_string('pluginname', 'local_report_emails');
$PAGE->set_url($url);
$PAGE->set_pagelayout('report');
$PAGE->set_title($strcompletion);
$PAGE->requires->css("/local/report_emails/styles.css");
$PAGE->requires->jquery();

// Set the page heading.
$PAGE->set_heading(get_string('pluginname', 'block_iomad_reports') . " - $strcompletion");

// Get the renderer.
$output = $PAGE->get_renderer('block_iomad_company_admin');

// Javascript for fancy select.
// Parameter is name of proper select form element followed by 1=submit its form
$PAGE->requires->js_call_amd('block_iomad_company_admin/department_select', 'init', array('departmentid', 1, optional_param('departmentid', 0, PARAM_INT)));

// Work out department level.
$company = new company($companyid);
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

$PAGE->navbar->add(get_string('dashboard', 'block_iomad_company_admin'));
$PAGE->navbar->add($strcompletion, $url);

$url = new moodle_url('/local/report_emails/index.php', $params);

// Deal with resend check.
if ($emailid and confirm_sesskey()) {

    // resend email, after confirmation.
    $email = $DB->get_record('email', ['id' => $emailid], '*', MUST_EXIST);
    if ($confirm != md5($emailid)) {
        echo $OUTPUT->header();
        echo $OUTPUT->heading(get_string('resendemail', 'local_report_emails'));
        $optionsyes = array('emailid' => $emailid,
                            'confirm' => md5($emailid),
                            'sesskey' => sesskey());

        echo $OUTPUT->confirm(get_string('resendemailfull', 'local_report_emails'),
                              new moodle_url('/local/report_emails/index.php', $optionsyes), '/local/report_emails/index.php');
        echo $OUTPUT->footer();
        die;
    } else {
        $DB->set_field('email', 'sent', null, array('id' => $emailid));
        redirect($url);
        die;
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
            $extrafields[$extrafield]->fieldid = $profilefield->id;
        } else {
            $extrafields[$extrafield]->title = get_string($extrafield);
        }
    }
}

// Get the appropriate list of departments.
$selectparams = $params;
$selectparams['courseid'] = 0;
$selecturl = new moodle_url('/local/report_emails/index.php', $selectparams);
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

// Set up the table.
$table = new local_report_emails_table('user_report_logins');
$table->is_downloading($download, 'user_report_logins', 'user_report_logins123');

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
            $params['companyid'] = $companyid;
            $params['addfrom'] = 'emailfrom';
            $params['addto'] = 'emailto';
            $params['adddodownload'] = false;
            $mform = new iomad_user_filter_form(null, $params);
            $mform->set_data(array('departmentid' => $departmentid));
            $mform->set_data($params);
            $mform->get_data();

            // Display the user filter form.
            $mform->display();
        }
    }
}

// Deal with where we are on the department tree.
$currentdepartment = company::get_departmentbyid($departmentid);
$showdepartments = company::get_subdepartments_list($currentdepartment);
$showdepartments[$departmentid] = $departmentid;
$departmentsql = " AND d.id IN (" . implode(',', array_keys($showdepartments)) . ")";

// Set up the initial SQL for the form.
$selectsql = " e.id AS emailid, u.id,u.firstname,u.lastname,d.name as department,u.email,e.templatename, e.modifiedtime AS created, e.sent, c.fullname AS coursename, e.senderid, e.due, e.subject";
$fromsql = "{user} u JOIN {email} e ON (u.id = e.userid) JOIN {company_users} cu ON (u.id = cu.userid AND e.userid = cu.userid) JOIN {department} d ON (cu.departmentid = d.id) JOIN {course} c on (e.courseid = c.id)";
$wheresql = $searchinfo->sqlsearch . " AND cu.companyid = :companyid $departmentsql $companysql";
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
            $wheresql .= " AND P".$extrafield->fieldid . ".fieldid = :p" . $extrafield->fieldid . "fieldid ";
            $sqlparams["p".$extrafield->fieldid."fieldid"] = $extrafield->fieldid;
        }
    }
}

// And final the rest of the form headers.
$headers[] = get_string('emailtemplatename', 'local_email');
$headers[] = get_string('subject', 'local_email');
$headers[] = get_string('course');
$headers[] = get_string('sender', 'local_report_emails');
$headers[] = get_string('created', 'local_report_emails');
$headers[] = get_string('due', 'local_report_emails');
$headers[] = get_string('sent', 'local_report_emails');
$headers[] = get_string('controls', 'local_report_emails');

$columns[] = 'templatename';
$columns[] = 'subject';
$columns[] = 'coursename';
$columns[] = 'sender';
$columns[] = 'created';
$columns[] = 'due';
$columns[] = 'sent';
$columns[] = 'controls';

$table->set_sql($selectsql, $fromsql, $wheresql, $sqlparams);
$table->define_baseurl($url);
$table->define_columns($columns);
$table->define_headers($headers);
$table->no_sorting('controls');
$table->sort_default_column = 'sent';
$table->out($CFG->iomad_max_list_users, true);

if (!$table->is_downloading()) {
    echo $output->footer();
}
