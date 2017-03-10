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

require_once(dirname(__FILE__) . '/../../config.php'); // Creates $PAGE.
require_once('lib.php');
require_once($CFG->libdir . '/formslib.php');

class company_license_form extends company_moodleform {
    protected $context = null;
    protected $selectedcompany = 0;
    protected $potentialcourses = null;
    protected $subhierarchieslist = null;
    protected $currentcourses = null;
    protected $departmentid = 0;
    protected $companydepartment = 0;

    public function __construct($actionurl, $context, $companyid, $departmentid, $licenseid, $courses=array()) {
        global $USER;
        $this->selectedcompany = $companyid;
        $this->context = $context;
        $this->departmentid = $departmentid;
        $this->licenseid = $licenseid;
        $this->selectedcourses = $courses;

        $company = new company($this->selectedcompany);
        $parentlevel = company::get_company_parentnode($company->id);
        $this->companydepartment = $parentlevel->id;

        if (iomad::has_capability('block/iomad_company_admin:edit_licenses', context_system::instance())) {
            $userhierarchylevel = $parentlevel->id;
        } else {
            $userlevel = $company->get_userlevel($USER);
            $userhierarchylevel = $userlevel->id;
        }

        $this->subhierarchieslist = company::get_all_subdepartments($userhierarchylevel);
        if ($this->departmentid == 0 ) {
            $departmentid = $userhierarchylevel;
        } else {
            $departmentid = $this->departmentid;
        }

        $options = array('context' => $this->context,
                         'multiselect' => true,
                         'companyid' => $this->selectedcompany,
                         'departmentid' => $departmentid,
                         'subdepartments' => $this->subhierarchieslist,
                         'parentdepartmentid' => $parentlevel,
                         'selected' => $this->selectedcourses,
                         'license' => true);
        $this->currentcourses = new all_department_course_selector('currentcourselicense', $options);

        parent::__construct($actionurl);
    }


    public function definition() {
        $this->_form->addElement('hidden', 'companyid', $this->selectedcompany);
        $this->_form->addElement('hidden', 'departmentid', $this->departmentid);
        $this->_form->addElement('hidden', 'licenseid', $this->licenseid);
        $this->_form->setType('companyid', PARAM_INT);
        $this->_form->setType('departmentid', PARAM_INT);
        $this->_form->setType('licenseid', PARAM_INT);
    }


    public function definition_after_data() {
        $mform =& $this->_form;

        // Adding the elements in the definition_after_data function rather than in the definition function
        // so that when the currentcourses or potentialcourses get changed in the process function, the
        // changes get displayed, rather than the lists as they are before processing.

        $company = new company($this->selectedcompany);
        $mform->addElement('header', 'header', get_string('edit_licenses', 'block_iomad_company_admin'));

        $mform->addElement('text',  'licensename', get_string('licensename', 'block_iomad_company_admin'),
                           'maxlength="254" size="50"');
        $mform->addHelpButton('licensename', 'licensename', 'block_iomad_company_admin');
        $mform->addRule('licensename', get_string('missinglicensename', 'block_iomad_company_admin'), 'required', null, 'client');
        $mform->setType('licensename', PARAM_MULTILANG);

        $mform->addElement('date_selector', 'licenseexpires', get_string('licenseexpires', 'block_iomad_company_admin'));
        $mform->addHelpButton('licenseexpires', 'licenseexpires', 'block_iomad_company_admin');
        $mform->addRule('licenseexpires', get_string('missinglicenseexpires', 'block_iomad_company_admin'),
                        'required', null, 'client');

        $mform->addElement('text', 'licenseduration', get_string('licenseduration', 'block_iomad_company_admin'),
                           'maxlength="254" size="50"');
        $mform->addHelpButton('licenseduration', 'licenseduration', 'block_iomad_company_admin');
        $mform->addRule('licenseduration', get_string('missinglicenseduration', 'block_iomad_company_admin'),
                        'required', null, 'client');
        $mform->setType('licenseduration', PARAM_INTEGER);

        $mform->addElement('text', 'licenseallocation', get_string('licenseallocation', 'block_iomad_company_admin'),
                           'maxlength="254" size="50"');
        $mform->addHelpButton('licenseallocation', 'licenseallocation', 'block_iomad_company_admin');
        $mform->addRule('licenseallocation', get_string('missinglicenseallocation', 'block_iomad_company_admin'),
                        'required', null, 'client');
        $mform->setType('licenseallocation', PARAM_MULTILANG);

        $mform->addElement('html', "<div class='fitem'><div class='fitemtitle'>" .
                           get_string('selectlicensecourse', 'block_iomad_company_admin').
                           "</div><div class='felement'>");
        $mform->addElement('html', $this->currentcourses->display(true));
        $mform->addElement('html', "</div></div>");

        if ( $this->currentcourses ) {
            $this->add_action_buttons(true, get_string('updatelicense', 'block_iomad_company_admin'));
        } else {
            $mform->addElement('html', get_string('nocourses', 'block_iomad_company_admin'));
        }
    }

    public function get_data() {
        $data = parent::get_data();

        if ($data !== null && $this->currentcourses) {
            $data->selectedcourses = $this->currentcourses->get_selected_courses();
        }

        return $data;
    }
}

$returnurl = optional_param('returnurl', '', PARAM_LOCALURL);
$companyid = optional_param('companyid', 0, PARAM_INTEGER);
$courseid = optional_param('courseid', 0, PARAM_INTEGER);
$departmentid = optional_param('departmentid', 0, PARAM_INTEGER);
$licenseid = optional_param('licenseid', 0, PARAM_INTEGER);

$context = context_system::instance();
require_login();
iomad::require_capability('block/iomad_company_admin:edit_licenses', $context);

// Set the companyid
$companyid = iomad::get_my_companyid($context);

$PAGE->set_context($context);

$urlparams = array('companyid' => $companyid);
if ($returnurl) {
    $urlparams['returnurl'] = $returnurl;
}
if ($courseid) {
    $urlparams['courseid'] = $courseid;
}

// Correct the navbar .
// Set the name for the page.
$linktext = get_string('managelicenses', 'block_iomad_company_admin');
// Set the url.
$linkurl = new moodle_url('/blocks/iomad_company_admin/company_license_edit_form.php');

// Print the page header.
$PAGE->set_context($context);
$PAGE->set_url($linkurl);
$PAGE->set_pagelayout('admin');
$PAGE->set_title($linktext);
$PAGE->set_heading(get_string('edit_licenses_title', 'block_iomad_company_admin'));

// Build the nav bar.
company_admin_fix_breadcrumb($PAGE, $linktext, $linkurl);

$mform = new company_license_form($PAGE->url, $context, $companyid, $departmentid, $licenseid);


if ( $mform->is_cancelled() || optional_param('cancel', false, PARAM_BOOL) ) {
    if ( $returnurl ) {
        redirect($returnurl);
    } else {
        redirect(new moodle_url('/blocks/iomad_company_admin/company_license_list.php'));
    }
} else {
    if ( $data = $mform->get_data() ) {
        global $DB;
        $licensedata = array();
        $licensedata['name'] = $data->licensename;
        $licensedata['allocation'] = $data->licenseallocation;
        $licensedata['expirydate'] = $data->licenseexpires;
        $licensedata['companyid'] = $data->companyid;
        $licensedata['validlength'] = $data->licenseduration;
        if ($currlicensedata = $DB->get_record('companylicense', array('id' => $licenseid))) {
            $new = false;
            // Already in the table update it.
            $licensedata['id'] = $currlicensedata->id;
            $licensedata['used'] = $currlicensedata->used;
            $licenseid = $licensedata['id'];
            $DB->update_record('companylicense', $licensedata);
        } else {
            $new = true;
            // New license being created.
            $licensedata['used'] = 0;
            $licenseid = $DB->insert_record('companylicense', $licensedata);
        }
        // Deal with course allocations if there are any.
        // Clear down all of them initially.
        $DB->delete_records('companylicense_courses', array('licenseid' => $licenseid));
        if (!empty($data->selectedcourses)) {
            // Add the course license allocations.
            foreach ($data->selectedcourses as $selectedcourse) {
                $DB->insert_record('companylicense_courses', array('licenseid' => $licenseid, 'courseid' => $selectedcourse->id));
            }
        }
        redirect(new moodle_url('/blocks/iomad_company_admin/company_license_list.php'));

    }
    // Check if we are editing a current license.
    if (!empty($licenseid)) {
        $license = $DB->get_record('companylicense', array('id' => $licenseid));
        $formlicense = array();
        $formlicense['licensename'] = $license->name;
        $formlicense['licenseallocation'] = $license->allocation;
        $formlicense['licenseexpires'] = $license->expirydate;
        $formlicense['companyid'] = $license->companyid;
        $formlicense['licenseduration'] = $license->validlength;
        // Get courses license is applied to.
        $courselicense = $DB->get_records('companylicense_courses', array('licenseid' => $licenseid), null, 'courseid');
        $formlicense['currentcourselicense'] = $courselicense;
        $mform = new company_license_form($PAGE->url, $context, $companyid, $departmentid, $licenseid, $courselicense);
        $mform->set_data($formlicense);
    }

    echo $OUTPUT->header();
    // Check the department is valid.
    if (!empty($departmentid) && !company::check_valid_department($companyid, $departmentid)) {
        print_error('invaliddepartment', 'block_iomad_company_admin');
    }   

    // Check the license is valid.
    if (!empty($licenseid) && !company::check_valid_company_license($companyid, $licenseid)) {
        print_error('invalidlicense', 'block_iomad_company_admin');
    }   

    $company = new company($companyid);
    echo "<h3>".$company->get_name()."</h3>";
    $mform->display();
    echo $OUTPUT->footer();
}
