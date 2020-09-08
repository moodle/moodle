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

namespace block_iomad_company_admin\forms;

defined('MOODLE_INTERNAL') || die;

use \iomad;
use \company;
use \moodle_url;

class company_license_form extends \company_moodleform {
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
        global $DB, $USER;
        $this->selectedcompany = $companyid;
        $this->context = $context;
        $this->departmentid = $departmentid;
        $this->licenseid = $licenseid;
        $this->parentid = $parentid;
        $this->selectedcourses = $courses;
        if (!empty($this->parentid)) {
            $this->parentlicense = $DB->get_record('companylicense', array('id' => $parentid));
        } else {
            $this->parentlicense = null;
        }
        if (!$this->license = $DB->get_record('companylicense', array('id' => $licenseid))) {
            $this->license = new \stdclass();
        }

        $company = new \company($this->selectedcompany);
        $parentlevel = \company::get_company_parentnode($company->id);
        $this->companydepartment = $parentlevel->id;
        if(empty($parentid)) {
            $this->courses = $company->get_menu_courses(true, false, false, false);
        } else {
            $this->courses = $DB->get_records_sql_menu("SELECT c.id, c.fullname
                                                        FROM {course} c
                                                        JOIN {companylicense_courses} lic
                                                        on (c.id = lic.courseid)
                                                        WHERE lic.licenseid = :licenseid",
                                                        array('licenseid' => $parentid));
        }

        if (\iomad::has_capability('block/iomad_company_admin:edit_licenses', \context_system::instance())) {
            $userhierarchylevel = $parentlevel->id;
        } else {
            $userlevel = $company->get_userlevel($USER);
            $userhierarchylevel = $userlevel->id;
        }

        $this->subhierarchieslist = \company::get_all_subdepartments($userhierarchylevel);
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
        global $DB, $CFG;

        $mform =& $this->_form;

        // Adding the elements in the definition_after_data function rather than in the definition function
        // so that when the currentcourses or potentialcourses get changed in the process function, the
        // changes get displayed, rather than the lists as they are before processing.

        $company = new company($this->selectedcompany);
        if (empty($this->parentid)) {
            if (!empty($this->licenseid)) {
                $mform->addElement('header', 'header', get_string('edit_licenses', 'block_iomad_company_admin'));
            } else {
                $mform->addElement('header', 'header', get_string('createlicense', 'block_iomad_company_admin'));
            }
            $mform->addElement('hidden', 'designatedcompany', 0);
            $mform->setType('designatedcompany', PARAM_INT);
        } else {
            $licenseinfo = $DB->get_record('companylicense', array('id' => $this->parentid));

            // If this is a program, sort out the displayed used and allocated.
            if (!empty($licenseinfo->program)) {
                $used = $licenseinfo->used / count($this->courses);
                $free = ($licenseinfo->allocation - $licenseinfo->used) / count($this->courses);
            } else {
                $used = $licenseinfo->used;
                $free = $licenseinfo->allocation - $licenseinfo->used;
            }

            $company = new company($licenseinfo->companyid);
            $companylist = $company->get_child_companies_select(false);
            $mform->addElement('header', 'header', get_string('split_licenses', 'block_iomad_company_admin'));
            $this->free = $licenseinfo->allocation - $licenseinfo->used;
            $mform->addElement('static', 'parentlicensename', get_string('parentlicensename', 'block_iomad_company_admin') . ': ' . $licenseinfo->name);
            $mform->addElement('static', 'parentlicenseused', get_string('parentlicenseused', 'block_iomad_company_admin') . ': ' . $used);
            $mform->addElement('static', 'parentlicenseavailable', get_string('parentlicenseavailable', 'block_iomad_company_admin') . ': ' . $free);

            // Add in the selector for the company the license will be for.
            $designatedcompanyselect = $mform->addElement('select', 'designatedcompany', get_string('designatedcompany', 'block_iomad_company_admin'), $companylist);
            if (!empty($this->license->companyid)) {
                $designatedcompanyselect->setSelected($this->license->companyid);
            }
        }

        $mform->addElement('text',  'name', get_string('licensename', 'block_iomad_company_admin'),
                           'maxlength="254" size="50"');
        $mform->addHelpButton('name', 'licensename', 'block_iomad_company_admin');
        $mform->addRule('name', get_string('missinglicensename', 'block_iomad_company_admin'), 'required', null, 'client');
        $mform->setType('name', PARAM_ALPHANUMEXT);

        $mform->addElement('text',  'reference', get_string('licensereference', 'block_iomad_company_admin'),
                           'maxlength="100" size="50"');
        $mform->addHelpButton('reference', 'licensereference', 'block_iomad_company_admin');
        $mform->setType('reference', PARAM_ALPHANUMEXT);

        if (empty($this->parentid)) {
            if ($CFG->iomad_autoenrol_managers) {
                $licensetypes = array(get_string('standard', 'block_iomad_company_admin'),
                                      get_string('reusable', 'block_iomad_company_admin'));
            } else {
                $licensetypes = array(get_string('standard', 'block_iomad_company_admin'),
                                      get_string('reusable', 'block_iomad_company_admin'),
                                      get_string('educator', 'block_iomad_company_admin'),
                                      get_string('educatorreusable', 'block_iomad_company_admin'));
            }
            $mform->addElement('select', 'type', get_string('licensetype', 'block_iomad_company_admin'), $licensetypes);
            $mform->addHelpButton('type', 'licensetype', 'block_iomad_company_admin');
            $mform->addElement('selectyesno', 'program', get_string('licenseprogram', 'block_iomad_company_admin'));
            $mform->addHelpButton('program', 'licenseprogram', 'block_iomad_company_admin');
            $mform->addElement('selectyesno', 'instant', get_string('licenseinstant', 'block_iomad_company_admin'));
            $mform->addHelpButton('instant', 'licenseinstant', 'block_iomad_company_admin');
            $mform->addElement('date_selector', 'startdate', get_string('licensestartdate', 'block_iomad_company_admin'));

            $mform->addHelpButton('startdate', 'licensestartdate', 'block_iomad_company_admin');
            $mform->addRule('startdate', get_string('missingstartdate', 'block_iomad_company_admin'),
                            'required', null, 'client');

            $mform->addElement('date_selector', 'expirydate', get_string('licenseexpires', 'block_iomad_company_admin'));
            $mform->addHelpButton('expirydate', 'licenseexpires', 'block_iomad_company_admin');
            $mform->addRule('expirydate', get_string('missinglicenseexpires', 'block_iomad_company_admin'),
                            'required', null, 'client');

            $mform->addElement('date_selector', 'cutoffdate', get_string('licensecutoffdate', 'block_iomad_company_admin'), array('optional' => true));
            $mform->addHelpButton('cutoffdate', 'licensecutoffdate', 'block_iomad_company_admin');
            $mform->disabledIf('cutoffdate', 'type', 'eq', 1);
            $mform->disabledIf('cutoffdate', 'type', 'eq', 3);

            $mform->addElement('advcheckbox', 'clearonexpire', get_string('clearonexpire', 'block_iomad_company_admin'));

            $mform->addHelpButton('clearonexpire', 'clearonexpire', 'block_iomad_company_admin');
            $mform->disabledIf('clearonexpire', 'type', 'eq', 1);
            $mform->disabledIf('clearonexpire', 'type', 'eq', 3);
            $mform->disabledIf('clearonexpire', 'cutoffdate[enabled]');

            $mform->addElement('text', 'validlength', get_string('licenseduration', 'block_iomad_company_admin'),
                               'maxlength="254" size="50"');
            $mform->addHelpButton('validlength', 'licenseduration', 'block_iomad_company_admin');
            $mform->setType('validlength', PARAM_INTEGER);
        } else {
            $mform->addElement('hidden', 'type', $this->parentlicense->type);
            $mform->setType('type', PARAM_INT);
            $mform->addElement('hidden', 'startdate', $licenseinfo->startdate);
            $mform->setType('expirydate', PARAM_INT);
            $mform->addElement('hidden', 'expirydate', $licenseinfo->expirydate);
            $mform->setType('expirydate', PARAM_INT);
            $mform->addElement('hidden', 'validlength', $licenseinfo->validlength);
            $mform->setType('validlength', PARAM_INTEGER);
            $mform->addElement('hidden', 'program', $this->parentlicense->program);
            $mform->setType('program', PARAM_INTEGER);
            $mform->addElement('hidden', 'parentid', $this->parentlicense->id);
            $mform->setType('parentid', PARAM_INTEGER);
        }

        $mform->addElement('text', 'allocation', get_string('licenseallocation', 'block_iomad_company_admin'),
                           'maxlength="254" size="50"');
        $mform->addHelpButton('allocation', 'licenseallocation', 'block_iomad_company_admin');
        $mform->addRule('allocation', get_string('missinglicenseallocation', 'block_iomad_company_admin'),
                        'required', null, 'client');
        $mform->setType('allocation', PARAM_MULTILANG);

        $mform->addElement('hidden', 'courseselector', 0);
        $mform->setType('expirydate', PARAM_INT);

        if (!empty($this->parentlicense->program)) {
            $mform->addElement('html', "<div style='display:none'>");
        }
        $autooptions = array('multiple' => true);
        $mform->addElement('autocomplete', 'licensecourses', get_string('selectlicensecourse', 'block_iomad_company_admin'), $this->courses, $autooptions);
        $mform->addRule('licensecourses', get_string('missinglicensecourses', 'block_iomad_company_admin'),
                        'required', null, 'client');

        // If we are not a child of a program license then show all of the courses.
        if (!empty($this->parentlicense->program)) {
            $mform->addElement('html', "</div>");
        }
        if ( $this->courses ) {
            $this->add_action_buttons(true, get_string('updatelicense', 'block_iomad_company_admin'));
        } else {
            $mform->addElement('html', get_string('nocourses', 'block_iomad_company_admin'));
        }
    }

    public function validation($data, $files) {
        global $CFG, $DB;

        $errors = array();

        $name = optional_param('name', '', PARAM_ALPHANUMEXT);

        if (empty($name)) {
            $errors['name'] = get_string('invalidlicensename', 'block_iomad_company_admin');
        }

        if (!empty($data['licenseid'])) {
            // check that the amount of free licenses slots is more than the amount being allocated.
            $currentlicense = $DB->get_record('companylicense', array('id' => $data['licenseid']));
            if (!empty($currentlicense->program)) {
                // Used count comes from the number of currently allocated courses.  Not those being passed.
                $coursecount = $DB->count_records('companylicense_courses', array('licenseid' => $currentlicense->id));
                $used = $currentlicense->used / $coursecount;
            } else {
                $used = $currentlicense->used;
            }
            if ($used > $data['allocation']) {
                $errors['allocation'] = get_string('licensenotenough', 'block_iomad_company_admin');
            }
        }

        if ($data['startdate'] > $data['expirydate']) {
            $errors['startdate'] = get_string('invalidstartdate', 'block_iomad_company_admin');
        }

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

            // How manay license do we actually need?
            if (!empty($data['program'])) {
                $required = $data['allocation'] * count($data['licensecourses']);
            } else {
                $required = $data['allocation'];
            }

            // Check if we have enough.
            if ($required > $free) {
                $errors['allocation'] = get_string('licensenotenough', 'block_iomad_company_admin');
            }

            // Check if we have a designated company.
            if (empty($data['designatedcompany'])) {
                $errors['designatedcompany'] = get_string('invalid_company', 'block_iomad_company_admin');
            }
        }

        // Allocation needs to be an integer.
        if (!preg_match('/^\d+$/', $data['allocation'])) {
            $errors['allocation'] = get_string('notawholenumber', 'block_iomad_company_admin');
        }

        // Did we get passed any courses?
        if (empty($data['licensecourses'])) {
            $errors['licensecourses'] = get_string('select_license_courses', 'block_iomad_company_admin');
        }

        if (($data['type'] == 1 || $data['type'] == 3) && empty($data['validlength']) && empty($data['cutoffdate'])) {
            $errors['validlength'] = get_string('missinglicenseduration', 'block_iomad_company_admin');
        }

        // Is the value for length appropriate?
        if (empty($data['type']) && $data['validlength'] < 1 ) {
            if (empty($data['validlength'])) {
                $errors['validlength'] = get_string('missingvalidlength', 'block_iomad_company_admin');
            } else {
                $errors['validlength'] = get_string('invalidnumber', 'block_iomad_company_admin');
            }
        }

        // Did we get passed any courses?
        if ($data['allocation'] < 1 ) {
            $errors['allocation'] = get_string('invalidnumber', 'block_iomad_company_admin');
        }

        // Is expiry date valid?
        if ($data['expirydate'] < time()) {
            $errors['expirydate'] = get_string('errorinvaliddate', 'calendar');
        }

        if ($CFG->iomad_autoenrol_managers && $data['type'] > 1) {
            $errors['type'] = get_string('invalid');
        }

        return $errors;
    }
}