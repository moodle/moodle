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
    protected $parentid = 0;
    protected $free = 0;

    public function __construct($actionurl,
                                $context,
                                $companyid,
                                $departmentid = 0,
                                $licenseid,
                                $parentid = 0,
                                $courses=array()) {
        global $USER;
        $this->selectedcompany = $companyid;
        $this->context = $context;
        $this->departmentid = $departmentid;
        $this->licenseid = $licenseid;
        $this->parentid = $parentid;
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
                         'parentid' => $this->parentid,
                         'license' => true);
        $this->currentcourses = new all_department_course_selector('currentcourselicense', $options);

        parent::__construct($actionurl);
    }


    public function definition() {
        $this->_form->addElement('hidden', 'companyid', $this->selectedcompany);
        $this->_form->addElement('hidden', 'departmentid', $this->departmentid);
        $this->_form->addElement('hidden', 'licenseid', $this->licenseid);
        $this->_form->addElement('hidden', 'parentid', $this->parentid);
        $this->_form->setType('companyid', PARAM_INT);
        $this->_form->setType('departmentid', PARAM_INT);
        $this->_form->setType('licenseid', PARAM_INT);
        $this->_form->setType('parentid', PARAM_INT);
    }


    public function definition_after_data() {
        global $DB;

        $mform =& $this->_form;

        // Adding the elements in the definition_after_data function rather than in the definition function
        // so that when the currentcourses or potentialcourses get changed in the process function, the
        // changes get displayed, rather than the lists as they are before processing.

        $company = new company($this->selectedcompany);
        if (empty($this->parentid)) {
            $mform->addElement('header', 'header', get_string('edit_licenses', 'block_iomad_company_admin'));
            $mform->addElement('hidden', 'designatedcompany', 0);
            $mform->setType('designatedcompany', PARAM_INT);
        } else {
            $company = new company($this->selectedcompany);
            $companylist = $company->get_child_companies_select(false);
            $mform->addElement('header', 'header', get_string('split_licenses', 'block_iomad_company_admin'));
            $licenseinfo = $DB->get_record('companylicense', array('id' => $this->parentid));
            $this->free = $licenseinfo->allocation - $licenseinfo->used;
            $mform->addElement('static', 'parentlicensename', get_string('parentlicensename', 'block_iomad_company_admin') . ': ' . $licenseinfo->name);
            $mform->addElement('static', 'parentlicenseused', get_string('parentlicenseused', 'block_iomad_company_admin') . ': ' . $licenseinfo->used);
            $mform->addElement('static', 'parentlicenseavailable', get_string('parentlicenseavailable', 'block_iomad_company_admin') . ': ' . $this->free);

            // Add in the selector for the company the license will be for.
            $mform->addElement('select', 'designatedcompany', get_string('designatedcompany', 'block_iomad_company_admin'), $companylist);
        }

        $mform->addElement('text',  'name', get_string('licensename', 'block_iomad_company_admin'),
                           'maxlength="254" size="50"');
        $mform->addHelpButton('name', 'licensename', 'block_iomad_company_admin');
        $mform->addRule('name', get_string('missinglicensename', 'block_iomad_company_admin'), 'required', null, 'client');
        $mform->setType('name', PARAM_MULTILANG);

        if (empty($this->parentid)) {
            $mform->addElement('date_selector', 'expirydate', get_string('licenseexpires', 'block_iomad_company_admin'));
            $mform->addHelpButton('expirydate', 'licenseexpires', 'block_iomad_company_admin');
            $mform->addRule('expirydate', get_string('missinglicenseexpires', 'block_iomad_company_admin'),
                            'required', null, 'client');

            $mform->addElement('text', 'validlength', get_string('licenseduration', 'block_iomad_company_admin'),
                               'maxlength="254" size="50"');
            $mform->addHelpButton('validlength', 'licenseduration', 'block_iomad_company_admin');
            $mform->addRule('validlength', get_string('missinglicenseduration', 'block_iomad_company_admin'),
                            'required', null, 'client');
            $mform->setType('validlength', PARAM_INTEGER);
        } else {
            $mform->addElement('hidden', 'expirydate', $licenseinfo->expirydate);
            $mform->setType('expirydate', PARAM_INT);
            $mform->addElement('hidden', 'validlength', $licenseinfo->validlength);
            $mform->setType('validlength', PARAM_INTEGER);
        }

        $mform->addElement('text', 'allocation', get_string('licenseallocation', 'block_iomad_company_admin'),
                           'maxlength="254" size="50"');
        $mform->addHelpButton('allocation', 'licenseallocation', 'block_iomad_company_admin');
        $mform->addRule('allocation', get_string('missinglicenseallocation', 'block_iomad_company_admin'),
                        'required', null, 'client');
        $mform->setType('allocation', PARAM_MULTILANG);

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

    public function validation($data, $files) {
        global $DB;

        $errors = array();

        if (!empty($data['parentid'])) {
            // check that the amount of free licenses slots is more than the amount being allocated.
            $parentlicense = $DB->get_record('companylicense', array('id' => $data['parentid']));

            // Check if this is a new license or we are updating it.
            if (!empty($data['licenseid'])) {
                $currlicenseinfo = $DB->get_record('companylicense', array('id' => $data['licenseid']));
                $weighting = $currlicenseinfo->allocation;
            } else {
                $weighting = 0;
            }
            $free = $parentlicense->allocation - $parentlicense->used + $weighting;
            if ($data['allocation'] > $free) {
                $errors['allocation'] = get_string('licensenotenough', 'block_iomad_company_admin');
            }  
        }
        return $errors;
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
$parentid = optional_param('parentid', 0, PARAM_INTEGER);

$context = context_system::instance();
require_login();

// Set the companyid
$companyid = iomad::get_my_companyid($context);
$company = new company($companyid);

echo "licenseid = $licenseid , companyid = $companyid </br>";

if (empty($parentid)) {
    if (!empty($licenseid) && $company->is_child_license($licenseid)) {
        iomad::require_capability('block/iomad_company_admin:edit_my_licenses', $context);
    } else {
        iomad::require_capability('block/iomad_company_admin:edit_licenses', $context);
    }
} else {
    iomad::require_capability('block/iomad_company_admin:edit_my_licenses', $context);
}

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

// If we are editing a license, check that the parent id is set.
if (!empty($licenseid)) {
    $licenseinfo = $DB->get_record('companylicense', array('id' => $licenseid));
    $parentid = $licenseinfo->parentid;
}

// Set up the form.
$mform = new company_license_form($PAGE->url, $context, $companyid, $departmentid, $licenseid, $parentid);
if ($licenseinfo = $DB->get_record('companylicense', array('id' => $licenseid))) {
    if ($currentcourses = $DB->get_records('companylicense_courses', array('licenseid' => $licenseid), null, 'courseid')) {
        foreach ($currentcourses as $currentcourse) {
            $licenseinfo->currentcourselicense[] = $currentcourse->courseid;
        }
    }
    $mform->set_data($licenseinfo);
}

if ( $mform->is_cancelled() || optional_param('cancel', false, PARAM_BOOL) ) {
    if ( $returnurl ) {
        redirect($returnurl);
    } else {
        redirect(new moodle_url('/blocks/iomad_company_admin/company_license_list.php'));
    }
} else {
    if ( $data = $mform->get_data() ) {
        global $DB, $USER;

        $new = false;
        $licensedata = array();
        $licensedata['name'] = $data->name;
        $licensedata['allocation'] = $data->allocation;
        $licensedata['expirydate'] = $data->expirydate;
        if (empty($data->parentid)) { 
            $licensedata['companyid'] = $data->companyid;
        } else {
            $licensedata['companyid'] = $data->designatedcompany;
            $licensedata['parentid'] = $data->parentid;
        }
        $licensedata['validlength'] = $data->validlength;
        if ( !empty($licenseid) && $currlicensedata = $DB->get_record('companylicense', array('id' => $licenseid))) {
            $new = false;
            // Already in the table update it.
            $licensedata['id'] = $currlicensedata->id;
            $licensedata['used'] = $currlicensedata->used;
            $DB->update_record('companylicense', $licensedata);
        } else {
            $new = true;
            // New license being created.
            $licensedata['used'] = 0;
            $licenseid = $DB->insert_record('companylicense', $licensedata);
        }

        // Create an event to deal with an parent license allocations.
        $eventother = array('licenseid' => $licenseid,
                            'parentid' => $data->parentid);

        if ($new) {
            $event = \block_iomad_company_admin\event\company_license_created::create(array('context' => context_system::instance(),
                                                                                            'userid' => $USER->id,
                                                                                            'objectid' => $licenseid,
                                                                                            'other' => $eventother));
        } else {
            $event = \block_iomad_company_admin\event\company_license_updated::create(array('context' => context_system::instance(),
                                                                                            'userid' => $USER->id,
                                                                                            'objectid' => $licenseid,
                                                                                            'other' => $eventother));
        }
        $event->trigger();

        // Deal with course allocations if there are any.
        // Clear down all of them initially.
        $DB->delete_records('companylicense_courses', array('licenseid' => $licenseid));
        if (!empty($data->selectedcourses)) {
            // Add the course license allocations.
            foreach ($data->selectedcourses as $selectedcourse) {
                $DB->insert_record('companylicense_courses', array('licenseid' => $licenseid, 'courseid' => $selectedcourse->id));
            }
        }
        if (empty($data->selectedcourses) && !empty($data->parentid)) {
            // Allocate all of the parent courses to this license by default.
            $parentcourses = $DB->get_records('companylicense_courses', array('licenseid' => $data->parentid));
            foreach ($parentcourses as $parentcourse) {
                $courserec = array('licenseid' => $licenseid, 'courseid' => $parentcourse->courseid);
                $DB->insert_record('companylicense_courses', $courserec);
            }
        }
        redirect(new moodle_url('/blocks/iomad_company_admin/company_license_list.php'));
    }

    // Display the form.
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
