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


class company_ccu_courses_form extends company_moodleform {
    protected $context = null;
    protected $selectedcompany = 0;
    protected $potentialcourses = null;
    protected $currentcourses = null;
    protected $departmentid = 0;
    protected $subhierarchieslist = null;
    protected $companydepartment = 0;
    protected $selectedcourse = 0;
    protected $company = null;
    protected $courses = array();


    public function __construct($actionurl, $context, $companyid, $selectedcourse) {
        global $DB, $USER;
        $this->selectedcompany = $companyid;
        $this->company = new company($companyid);
        $this->context = $context;
        $this->selectedcourse = $selectedcourse;

        $this->courses = $this->company->get_menu_courses(true, false, true);
        parent::__construct($actionurl);
    }


    public function definition() {
        $this->_form->addElement('hidden', 'companyid', $this->selectedcompany);
        $this->_form->setType('companyid', PARAM_INT);
    }


    public function definition_after_data() {
        $mform =& $this->_form;
        // Adding the elements in the definition_after_data function rather than in the definition
        // function so that when the currentcourses or potentialcourses get changed in the process
        // function, the changes get displayed, rather than the lists as they are before processing.

        if ($this->courses) {
            $autooptions = array('setmultiple' => false,
                                 'onchange' => 'this.form.submit()');
            $mform->addElement('autocomplete', 'selectedcourse', get_string('selectcourse', 'block_iomad_company_admin'), $this->courses, $autooptions);

        } else {
            $mform->addElement('html', '<div class="alert alert-warning">' . get_string('nocourses', 'block_iomad_company_admin') . '</div>');
        }

        // Disable the onchange popup.
        $mform->disable_form_change_checker();
    }
}


class course_group_display_form extends company_moodleform {
    protected $courseid = 0;
    protected $context = null;
    protected $company = null;

    public function __construct($actionurl, $companyid, $courseid, $output, $chosenid=0, $action=0) {
        global $CFG, $USER;

        $this->selectedcompany = $companyid;
        $this->context = context_coursecat::instance($CFG->defaultrequestcategory);
        $syscontext = context_system::instance();

        $this->company = new company($this->selectedcompany);
        $this->courseid = $courseid;
        $this->output = $output;
        parent::__construct($actionurl);
    }

    public function definition() {
        global $CFG,$DB;

        $mform =& $this->_form;
        $company = $this->company;
        if (!empty($this->courseid)) {
            $coursegroups = $company->get_course_groups_menu($this->courseid);
        } else {
            $coursegroups = array();
        }

        // Create the course group checkboxes html.
        $coursegrouphtml = "";
        unset($coursegroups[0]);
        if (!empty($coursegroups)) {
            $coursegrouphtml = "<p>".get_string('group').
                               "</p>";
            foreach ($coursegroups as $key => $value) {

                $coursegrouphtml .= '<input type = "radio" name = "groupids[]" value="'.
                                       $key.'" /> '.$value.'</br>';
            }
        }
        // Then show the fields about where this block appears.
        $mform->addElement('header', 'header',
                            get_string('companygroups', 'block_iomad_company_admin').
                           $company->get_name());

        if (empty($coursegroups)) {
            $mform->addElement('html', "<h3>" . get_string('nogroups', 'block_iomad_company_admin') . "</h3></br>");
        }
        $mform->addElement('html', $coursegrouphtml);
        $mform->addElement('hidden', 'selectedcourse', $this->courseid);
        $mform->setType('selectedcourse', PARAM_INT);

        $buttonarray = array();
        $buttonarray[] = $mform->createElement('submit', 'create',
                                get_string('creategroup', 'block_iomad_company_admin'));
        if (!empty($coursegroups)) {
            $buttonarray[] = $mform->createElement('submit', 'edit',
                                get_string('editgroup', 'block_iomad_company_admin'));
            $buttonarray[] = $mform->createElement('submit', 'delete',
                                get_string('deletegroup', 'block_iomad_company_admin'));
        }
        $mform->addGroup($buttonarray, 'buttonarray', '', array(' '), false);

        // Disable the onchange popup.
        $mform->disable_form_change_checker();
    }

    public function get_data() {
        $data = parent::get_data();
        return $data;
    }

}

class group_edit_form extends company_moodleform {
    protected $selectedcompany = 0;
    protected $context = null;
    protected $company = null;
    protected $courseid = 0;
    protected $groupid = 0;
    protected $output = null;

    public function __construct($actionurl, $companyid, $courseid, $groupid, $output, $action = 0) {
        global $CFG;

        $this->selectedcompany = $companyid;
        $this->context = context_coursecat::instance($CFG->defaultrequestcategory);
        $this->courseid = $courseid;
        $this->output = $output;
        $this->groupid = $groupid;
        $this->action = $action;
        $this->company = new company($companyid);

        parent::__construct($actionurl);
    }

    public function definition() {
        global $CFG;
        $mform =& $this->_form;

        // Then show the fields about where this block appears.
        if ($this->action == 0) {
            $mform->addElement('header', 'header',
                                get_string('creategroup', 'block_iomad_company_admin'));
        } else {
            $mform->addElement('header', 'header',
                                get_string('editgroup', 'block_iomad_company_admin'));
        }
        $mform->addElement('hidden', 'courseid', $this->courseid);
        $mform->setType('courseid', PARAM_INT);
        $mform->addElement('hidden', 'groupid', $this->groupid);
        $mform->setType('groupid', PARAM_INT);
        $mform->addElement('hidden', 'action', $this->action);
        $mform->setType('action', PARAM_INT);
        $mform->addElement('hidden', 'name');
        $mform->setType('name', PARAM_CLEAN);

        $mform->addElement('hidden', 'selectedcourse', $this->courseid);
        $mform->setType('selectedcourse', PARAM_INT);

        $mform->addElement('text', 'description',
                            get_string('groupdescription', 'block_iomad_company_admin'),
                            'maxlength = "200" size = "50"');
        $mform->addHelpButton('description', 'fullnamegroup', 'block_iomad_company_admin');
        $mform->addRule('description',
                        get_string('missinggroupdescription', 'block_iomad_company_admin'),
                        'required', null, 'client');
        $mform->setType('description', PARAM_MULTILANG);

        $this->add_action_buttons();
    }

}

$returnurl = optional_param('returnurl', '', PARAM_LOCALURL);
$courseid = optional_param('courseid', 0, PARAM_INT);
$deleteids = optional_param_array('courseids', null, PARAM_INT);
$createnew = optional_param('createnew', 0, PARAM_INT);
$selectedcourse = optional_param('selectedcourse', 0, PARAM_INTEGER);
$groupids = optional_param_array('groupids', 0, PARAM_INTEGER);

if (!empty($groupids)) {
    $groupid = $groupids[0];
} else {
    $groupid = 0;
}

$context = context_system::instance();
require_login();

iomad::require_capability('block/iomad_company_admin:edit_groups', $context);

$urlparams = array();
if ($returnurl) {
    $urlparams['returnurl'] = $returnurl;
}
$companylist = new moodle_url('/my', $urlparams);

$linktext = get_string('managegroups', 'block_iomad_company_admin');

// Set the url.
$linkurl = new moodle_url('/blocks/iomad_company_admin/company_groups_create_form.php');

$PAGE->set_context($context);
$PAGE->set_url($linkurl);
$PAGE->set_pagelayout('admin');
$PAGE->set_title($linktext);

// get output renderer                                                                                                                                                                                         
$output = $PAGE->get_renderer('block_iomad_company_admin');

// Set the page heading.
$PAGE->set_heading(get_string('myhome') . " - $linktext");

// Build the nav bar.
company_admin_fix_breadcrumb($PAGE, $linktext, $linkurl);

// Set the companyid
$companyid = iomad::get_my_companyid($context);

// Javascript for fancy select.
// Parameter is name of proper select form element. 
$PAGE->requires->js_call_amd('block_iomad_company_admin/department_select', 'init', array('deptid'));

$courseform = new company_ccu_courses_form($PAGE->url, $context, $companyid, $selectedcourse);
if (!empty($selectedcourse)) {
    $defaultgroup = company::get_company_group($companyid, $selectedcourse);
    $mform = new course_group_display_form($PAGE->url, $companyid, $selectedcourse, $output);
    $editform = new group_edit_form($PAGE->url, $companyid, $selectedcourse, $groupid, $output);
}
$courseform->set_data(array('selectedcourse' => $selectedcourse));

if (!empty($selectedcourse)) {
    if ($mform->is_cancelled()) {
        redirect($companylist);
    
    } else if ($data = $mform->get_data()) {
        if (isset($data->create)) {
            if (!empty($deleteids)) {
                $chosenid = $deleteids['0'];
            } else {
                $chosenid = 0;
            }
            $editform = new group_edit_form($PAGE->url, $companyid, $selectedcourse, $groupid, $output);
            echo $output->header();
    
            $editform->display();
            echo $output->footer();
            die;
        } else if (isset($data->delete)) {
            $shownotice = false;
            if (empty($groupid)) {
                $shownotice = true;
                $noticestring = get_string('groupnoselect', 'block_iomad_company_admin');
            } else {
                if ($groupid != $defaultgroup->id) {
                    $course = $DB->get_record('course', array('id' => $selectedcourse));
                    company::delete_company_course_group($companyid, $course, false, $groupid);
                } else {
                    $shownotice = true;
                    $noticestring = get_string('isdefaultgroupdelete', 'block_iomad_company_admin');
                }
            }
            $mform = new course_group_display_form($PAGE->url, $companyid, $selectedcourse, $output);
            // Redisplay the form.
            echo $output->header();
            $courseform->display();
            if ($shownotice) {
                notice($noticestring, new moodle_url($PAGE->url, array('selectedcourse' => $selectedcourse)));
            }
            $mform->display();
            echo $output->footer();
            die;
    
        } else if (isset($data->edit)) {
            // Editing an existing group..
            if (!empty($groupid)) {
                $grouprecord = $DB->get_record('groups', array('id' => $groupid));
                $editform = new group_edit_form($PAGE->url, $companyid, $selectedcourse, $groupid, $output);
                $editform->set_data(array('groupid' => $grouprecord->id,
                                          'name' => $grouprecord->name,
                                          'description' => $grouprecord->description));
                echo $output->header();
    
                // Check the department is valid.
                if (!empty($departmentid) && !company::check_valid_department($companyid, $departmentid)) {
                    print_error('invaliddepartment', 'block_iomad_company_admin');
                }   
    
                $editform->display();
    
                echo $output->footer();
                die;
            } else {
                echo $output->header();
                // Check the department is valid.
                if (!empty($departmentid) && !company::check_valid_department($companyid, $departmentid)) {
                    print_error('invaliddepartment', 'block_iomad_company_admin');
                }   
    
                echo get_string('departmentnoselect', 'block_iomad_company_admin');
                $mform->display();
                echo $output->footer();
                die;
            }
    
        }
    } else if ($createdata = $editform->get_data()) {
    
        // Create or update the department.
        company::create_company_course_group($companyid,
                                             $selectedcourse,
                                             $createdata);
        
        $mform = new course_group_display_form($PAGE->url, $companyid, $selectedcourse, $output);
        // Redisplay the form.
        echo $output->header();
    
        // Check the department is valid.
        if (!empty($departmentid) && !company::check_valid_department($companyid, $departmentid)) {
            print_error('invaliddepartment', 'block_iomad_company_admin');
        }   
    
        $courseform->display();
        $mform->display();
    
        echo $output->footer();
        die;
    }
}
echo $output->header();

// Check the department is valid.
if (!empty($departmentid) && !company::check_valid_department($companyid, $departmentid)) {
    print_error('invaliddepartment', 'block_iomad_company_admin');
}   

$courseform->display();
if (!empty($selectedcourse)) {
    $mform->display();
}

echo $output->footer();
