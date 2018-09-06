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
 * Script to let a user create a department for a particular company.
 */

require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->libdir . '/formslib.php');
require_once('lib.php');
require_once(dirname(__FILE__) . '/../../course/lib.php');

class department_edit_form extends company_moodleform {
    protected $selectedcompany = 0;
    protected $context = null;
    protected $company = null;
    protected $deptid = 0;
    protected $output = null;

    public function __construct($actionurl, $companyid, $departmentid, $output, $chosenid=0, $action=0) {
        global $CFG;

        $this->selectedcompany = $companyid;
        $this->context = context_coursecat::instance($CFG->defaultrequestcategory);
        $this->departmentid = $departmentid;
        $this->output = $output;
        $this->chosenid = $chosenid;
        $this->action = $action;
        parent::__construct($actionurl);
    }

    public function definition() {
        global $CFG, $USER;

        $mform =& $this->_form;
        $company = new company($this->selectedcompany);

        if (!empty($this->departmentid)) {
            $ignorecurrentbranch = $this->departmentid;
        } else {
            $ignorecurrentbranch = false;
        }
        $userdepartment = $company->get_userlevel($USER);
        $departmentslist = company::get_all_subdepartments($userdepartment->id);
        $departmenttree = company::get_all_subdepartments_raw($userdepartment->id, $ignorecurrentbranch);
        $department = company::get_departmentbyid($this->departmentid);
        $treehtml = $this->output->department_tree($departmenttree, optional_param('deptid', 0, PARAM_INT));

        // Then show the fields about where this block appears.
        if ($this->action == 0) {
            $mform->addElement('header', 'header',
                                get_string('createdepartment', 'block_iomad_company_admin'));
        } else {
            $mform->addElement('header', 'header',
                                get_string('editdepartments', 'block_iomad_company_admin'));
        }
        $mform->addElement('hidden', 'departmentid', $this->departmentid);
        $mform->setType('departmentid', PARAM_INT);
        $mform->addElement('hidden', 'action', $this->action);
        $mform->setType('action', PARAM_INT);

        // Display department select html (create only)
        //if ($this->action == 0) {
            $mform->addElement('html', '<p>' . get_string('parentdepartment', 'block_iomad_company_admin') . '</p>');
            $mform->addElement('html', $treehtml);
        //}

        // This is getting hidden anyway, so no need for label
        $mform->addElement('html', "<div style='display:none;'>");
        $mform->addElement('select', 'deptid', ' ',
                            $departmentslist, array('class' => 'iomad_department_select'));
        $mform->addElement('html', "</div></br>");

        $mform->addElement('text', 'fullname',
                            get_string('fullnamedepartment', 'block_iomad_company_admin'),
                            'maxlength = "254" size = "50"');
        $mform->addHelpButton('fullname', 'fullnamedepartment', 'block_iomad_company_admin');
        $mform->addRule('fullname',
                        get_string('missingfullnamedepartment', 'block_iomad_company_admin'),
                        'required', null, 'client');
        $mform->setType('fullname', PARAM_MULTILANG);

        $mform->addElement('text', 'shortname',
                            get_string('shortnamedepartment', 'block_iomad_company_admin'),
                            'maxlength = "100" size = "20"');
        $mform->addHelpButton('shortname', 'shortnamedepartment', 'block_iomad_company_admin');
        $mform->addRule('shortname',
                         get_string('missingshortnamedepartment', 'block_iomad_company_admin'),
                         'required', null, 'client');
        $mform->setType('shortname', PARAM_MULTILANG);

        if (!$this->departmentid) {
            $mform->addElement('hidden', 'chosenid', $this->chosenid);
        } else {
            $mform->addElement('hidden', 'chosenid', $this->departmentid);
        }
        $mform->setType('chosenid', PARAM_INT);

        $this->add_action_buttons();
    }

    public function validation($data, $files) {
        global $DB;

        $errors = array();

        if ($departmentbyname = $DB->get_record('department', array('company' => $this->selectedcompany, 'shortname' => trim($data['shortname'])))) {
            if ($departmentbyname->id != $this->departmentid) {
                $errors['shortname'] = get_string('departmentnameinuse', 'block_iomad_company_admin');
            }
        }
        return $errors;
    }
}

$returnurl = optional_param('returnurl', '', PARAM_LOCALURL);
$departmentid = optional_param('departmentid', 0, PARAM_INT);
$deptid = optional_param('deptid', 0, PARAM_INT);
$confirm = optional_param('confirm', null, PARAM_ALPHANUM);
$moveid = optional_param('moveid', 0, PARAM_INT);

$context = context_system::instance();
require_login();

iomad::require_capability('block/iomad_company_admin:edit_departments', $context);

$departmentlist = new moodle_url('/blocks/iomad_company_admin/company_departments.php', array('deptid' => $departmentid));

$linktext = get_string('editdepartment', 'block_iomad_company_admin');

// Set the url.
$linkurl = new moodle_url('/blocks/iomad_company_admin/company_department_create_form.php');

$PAGE->set_context($context);
$PAGE->set_url($linkurl);
$PAGE->set_pagelayout('admin');
$PAGE->set_title($linktext);

// get output renderer                                                                                                                                                                                         
$output = $PAGE->get_renderer('block_iomad_company_admin');

// Set the page heading.
$PAGE->set_heading(get_string('myhome') . " - $linktext");

// Build the nav bar.
company_admin_fix_breadcrumb($PAGE, $linktext, $departmentlist);

// Set the companyid
$companyid = iomad::get_my_companyid($context);

// Did we get a move request?
// Delete any valid departments.
if ($moveid && confirm_sesskey() && $confirm == md5($moveid)) {
    $movefullname = required_param('movefullname', PARAM_MULTILANG);
    $moveshortname = required_param('movefullname', PARAM_MULTILANG);
    $moveparent = required_param('moveparent', PARAM_INT);
    company::create_department($moveid,
                               $companyid,
                               $movefullname,
                               $moveshortname,
                               $moveparent);
    redirect($departmentlist);
    die;
}

// Set up the initial forms.
if (!empty($departmentid)) {
    $department = $DB->get_record('department', array('id' => $departmentid));
    $department->fullname = $department->name;
    $department->deptid = $department->parent;
    $editform = new department_edit_form($PAGE->url, $companyid, $departmentid, $output);
    $editform->set_data($department);
} else {
    $editform = new department_edit_form($PAGE->url, $companyid, $departmentid, $output);
    $editform->set_data(array('deptid' => $deptid));
}

if ($editform->is_cancelled()) {
    redirect($departmentlist);
    die;
} else if ($createdata = $editform->get_data()) {

    // Deal with leading/trailing spaces.
    $createdata->fullname = trim($createdata->fullname);
    $createdata->shortname = trim($createdata->shortname);

    // Create or update the department.
    if ($createdata->action != 0 ) {
        // We are creating a new department.
        company::create_department($createdata->departmentid,
                                   $companyid,
                                   $createdata->fullname,
                                   $createdata->shortname,
                                   $createdata->deptid);
    } else {
        // We are editing a current department.
        // Check if we are moving this department.
        $current = $DB->get_record('department', array('id' => $createdata->departmentid));
        if (empty($current) || $current->parent == $createdata->deptid) {
            // Not moving.  Save it.
            company::create_department($createdata->departmentid,
                                       $companyid,
                                       $createdata->fullname,
                                       $createdata->shortname,
                                       $createdata->deptid);
        } else {
            $parentdept = $DB->get_record('department', array('id' => $createdata->deptid));
            echo $output->header();
            echo $output->heading(get_string('movedepartment', 'block_iomad_company_admin'));
            $optionsyes = array('moveid' => $departmentid,
                                'confirm' => md5($departmentid),
                                'companyid' => $companyid,
                                'movefullname' => $createdata->fullname,
                                'moveshortname' => $createdata->shortname,
                                'moveparent' => $createdata->deptid,
                                'sesskey' => sesskey());
            $deptstring = (object) array('current' => $createdata->fullname, 'newparent' => $parentdept->name);
            echo $output->confirm(get_string('movedepartmentcheckfull', 'block_iomad_company_admin', $deptstring),
                                  new moodle_url('company_department_create_form.php', $optionsyes), 'company_departments.php');
            die;
        }
    }

    redirect($departmentlist);
    die;
} else {
    // Javascript for fancy select.
    // Parameter is name of proper select form element. 
    $PAGE->requires->js_call_amd('block_iomad_company_admin/department_select', 'init', array('deptid', '', $departmentid));

    echo $output->header();
    // Check the department is valid.
    if (!empty($departmentid) && !company::check_valid_department($companyid, $departmentid)) {
        print_error('invaliddepartment', 'block_iomad_company_admin');
    }   

    $editform->display();

    echo $output->footer();
}

