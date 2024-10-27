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
 * @package   local_iomad
 * @copyright 2024 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_iomad\forms;

defined('MOODLE_INTERNAL') || die;

use \iomad;
use \company;
use \moodle_url;
use \moodleform;
use \context_system;

/**
 * User search form used on the IOMAD pages.
 *
 */
class user_search_form extends moodleform {
    protected $companyid;
    protected $useshowall;
    protected $showhistoric;
    protected $addfrom;
    protected $addto;
    protected $addlicensestatus;
    protected $fromname;
    protected $toname;
    protected $useusertype;
    protected $validonly;

    public function definition() {
        global $CFG, $DB, $USER, $SESSION, $companycontext;

        if (!empty($this->_customdata['useshowall'])) {
            $useshowall = true;
        } else {
            $useshowall = false;
        }

        if (!empty($this->_customdata['showhistoric'])) {
            $showhistoric = true;
        } else {
            $showhistoric = false;
        }

        if (!empty($this->_customdata['addfrom'])) {
            $this->addfrom = true;
            $this->fromname = $this->_customdata['addfrom'];
        } else {
            $this->addfrom = false;
        }

        if (!empty($this->_customdata['addto'])) {
            $this->addto = true;
            $this->toname = $this->_customdata['addto'];
        } else {
            $this->addto = false;
        }

        if (!empty($this->_customdata['addfromb'])) {
            $this->addfromb = true;
            $this->fromnameb = $this->_customdata['addfromb'];
        } else {
            $this->addfromb = false;
        }

        if (!empty($this->_customdata['addtob'])) {
            $this->addtob = true;
            $this->tonameb = $this->_customdata['addtob'];
        } else {
            $this->addtob = false;
        }

        if (!empty($this->_customdata['addlicensestatus'])) {
            $addlicensestatus = true;
        } else {
            $addlicensestatus = false;
        }

        if (!empty($this->_customdata['addlicenseusage'])) {
            $addlicenseusage = true;
        } else {
            $addlicenseusage = false;
        }

        if (!empty($this->_customdata['addusertype'])) {
            $useusertype = true;
        } else {
            $useusertype = false;
        }

        if (!empty($this->_customdata['addvalidonly'])) {
            $this->validonly = true;
        } else {
            $this->validonly = false;
        }

        $mform =& $this->_form;
        $filtergroup = array();
        $mform->addElement('header', 'usersearchfields', get_string('usersearchfields', 'local_iomad'));
        $mform->addElement('text', 'firstname', get_string('firstnamefilter', 'local_iomad'), 'size="20"');
        $mform->addElement('text', 'lastname', get_string('lastnamefilter', 'local_iomad'), 'size="20"');
        $mform->addElement('text', 'email', get_string('emailfilter', 'local_iomad'), 'size="20"');
        $mform->addElement('hidden', 'departmentid');
        $mform->addElement('hidden', 'completiontype');
        $mform->addElement('hidden', 'eventid');
        $mform->addElement('hidden', 'courseid');
        $mform->addElement('hidden', 'licenseid');
        $mform->addElement('hidden', 'templateid');
        $mform->addElement('hidden', 'sort');
        $mform->setType('firstname', PARAM_CLEAN);
        $mform->setType('lastname', PARAM_CLEAN);
        $mform->setType('email', PARAM_EMAIL);
        $mform->setType('departmentid', PARAM_INT);
        $mform->setType('completiontype', PARAM_INT);
        $mform->setType('eventid', PARAM_INT);
        $mform->setType('courseid', PARAM_INT);
        $mform->setType('licenseid', PARAM_INT);
        $mform->setType('templateid', PARAM_INT);
        $mform->setType('sort', PARAM_ALPHA);
        $mform->setExpanded('usersearchfields', false);

        // Get company category.
        if ($category = $DB->get_record_sql('SELECT uic.id, uic.name
                                             FROM {user_info_category} uic, {company} c
                                             WHERE c.id = '.$this->_customdata['companyid'].'
                                             AND c.profileid=uic.id')) {
            // Get fields from company category.
            if ($fields = $DB->get_records('user_info_field', array('categoryid' => $category->id))) {
                // Display the header and the fields.
                foreach ($fields as $field) {
                    require_once($CFG->dirroot.'/user/profile/field/'.$field->datatype.'/field.class.php');
                    $newfield = 'profile_field_'.$field->datatype;
                    $formfield = new $newfield($field->id);
                    if ($field->datatype == 'datetime') {
                        $formfield->field->required = false;
                    }
                    $formfield->edit_field($mform);
                }
            }
        }

        // Deal with non company categories.
        if ($categories = $DB->get_records_sql("SELECT id FROM {user_info_category}
                                                WHERE id NOT IN (
                                                 SELECT profileid FROM {company})")) {
            foreach ($categories as $category) {
                // Get fields from company category.
                if ($fields = $DB->get_records('user_info_field', array('categoryid' => $category->id))) {
                    // Display the header and the fields.
                    foreach ($fields as $field) {
                        require_once($CFG->dirroot.'/user/profile/field/'.$field->datatype.'/field.class.php');
                        $newfield = 'profile_field_'.$field->datatype;
                        $formfield = new $newfield($field->id);
                        if ($field->datatype == 'datetime') {
                            $formfield->field->required = false;
                        } else if ($field->datatype == 'menu') {
                            $formfield->options = [-1 => null] + $formfield->options;
                        }
                        $formfield->edit_field($mform);
                    }
                }
            }
        }

        if ($useusertype) {
            $usertypearray = array ('a' => get_string('any'),
                                    '0' => get_string('user', 'block_iomad_company_admin'),
                                    '1' => get_string('companymanager', 'block_iomad_company_admin'),
                                    '2' => get_string('departmentmanager', 'block_iomad_company_admin'));
            $mform->addElement('select', 'usertype', get_string('usertype', 'block_iomad_company_admin'), $usertypearray);
        }

        if (iomad::has_capability('block/iomad_company_admin:viewsuspendedusers', $companycontext)) {
            $mform->addElement('checkbox', 'showsuspended', get_string('show_suspended_users', 'local_iomad'));
        } else {
            $mform->addElement('hidden', 'showsuspended');
        }
        $mform->setType('showsuspended', PARAM_INT);

        if ($this->validonly) {
            $mform->addElement('checkbox', 'validonly', get_string('hidevalidcourses', 'block_iomad_company_admin'));
        }

        if (!$useshowall) {
            $mform->addElement('hidden', 'showall');
            $mform->setType('showall', PARAM_BOOL);
        } else {
            $mform->addElement('checkbox', 'showall', get_string('show_all_company_users', 'block_iomad_company_admin'));
        }

        if (!$showhistoric) {
            $mform->addElement('hidden', 'showhistoric');
            $mform->setType('showhistoric', PARAM_BOOL);
        } else {
            $mform->addElement('checkbox', 'showhistoric', get_string('showhistoricusers', 'block_iomad_company_admin'));
        }

        if ($this->addfrom) {
            $mform->addElement('date_selector', $this->fromname, get_string($this->fromname, 'block_iomad_company_admin'), array('optional' => 'yes'));
        }

        if ($this->addto) {
            $mform->addElement('date_selector', $this->toname, get_string($this->toname, 'block_iomad_company_admin'), array('optional' => 'yes'));
        }

        if ($this->addfromb) {
            $mform->addElement('date_selector', $this->fromnameb, get_string($this->fromnameb, 'block_iomad_company_admin'), array('optional' => 'yes'));
        }

        if ($this->addtob) {
            $mform->addElement('date_selector', $this->tonameb, get_string($this->tonameb, 'block_iomad_company_admin'), array('optional' => 'yes'));
        }

        if ($addlicensestatus) {
            $licensestatusarray = array ('0' => get_string('any'),
                                      '1' => get_string('notinuse', 'block_iomad_company_admin'),
                                      '2' => get_string('inuse', 'block_iomad_company_admin'));
            $mform->addElement('select', 'licensestatus', get_string('licensestatus', 'block_iomad_company_admin'), $licensestatusarray);
        }

        if ($addlicenseusage) {
            $licenseusagearray = array ('0' => get_string('any'),
                                        '1' => get_string('notallocated', 'block_iomad_company_admin'),
                                        '2' => get_string('allocated', 'block_iomad_company_admin'));
            $mform->addElement('select', 'licenseusage', get_string('licenseuseage', 'block_iomad_company_admin'), $licenseusagearray);
        }

        // Add the button(s).
        $buttonarray=[];
        $buttonarray[] = $mform->createElement('submit', 'submitbutton', get_string('userfilter', 'local_iomad'));
        if (!empty($this->_customdata['adddodownload'])) {
            $buttonarray[] = $mform->createElement('submit', 'dodownload', get_string("downloadcsv", 'local_report_completion'));
        }
        $mform->addGroup($buttonarray, 'buttonar', '', ' ', false);
    }

    public function validation($data, $files) {

        $errors = array();
        if (!empty($this->fromname) && !empty($this->toname)) {
            if (!empty($data[$this->fromname]) && !empty($data[$this->toname])) {
                if ($data[$this->fromname] > $data[$this->toname]) {
                    $errors[$this->fromname] = get_string('errorinvaliddate', 'calendar');
                }
            }
        }
        if (!empty($this->fromnameb) && !empty($this->tonameb)) {
            if (!empty($data[$this->fromnameb]) && !empty($data[$this->tonameb])) {
                if ($data[$this->fromnameb] > $data[$this->tonameb]) {
                    $errors[$this->fromnameb] = get_string('errorinvaliddate', 'calendar');
                }
            }
        }
        return $errors;
    }
}
