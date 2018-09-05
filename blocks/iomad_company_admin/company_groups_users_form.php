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
                                 'noselectionstring' => '',                                                                                                     
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

        
        // Then show the fields about where this block appears.
        $mform->addElement('header', 'header',
                            get_string('companygroupsusers', 'block_iomad_company_admin').
                           $company->get_name());

        if (empty($coursegroups)) {
            $mform->addElement('html', "<h3>" . get_string('nogroups', 'block_iomad_company_admin') . "</h3></br>");
        } else {
            $autooptions = array('setmultiple' => false,
                                 'noselectionstring' => '',                                                                                                     
                                 'onchange' => 'this.form.submit()');
            $mform->addElement('autocomplete', 'selectedgroup', get_string('selectgroup', 'block_iomad_company_admin'), $coursegroups, $autooptions);

        }
        
        $mform->addElement('hidden', 'selectedcourse', $this->courseid);
        $mform->setType('selectedcourse', PARAM_INT);

        // Disable the onchange popup.
        $mform->disable_form_change_checker();
    }

    public function create_user_selectors() {
        if (!empty ($this->course)) {
            $options = array('context' => $this->context,
                             'companyid' => $this->selectedcompany,
                             'courseid' => $this->course,
                             'departmentid' => $this->departmentid,
                             'subdepartments' => $this->subhierarchieslist,
                             'parentdepartmentid' => $this->parentlevel);
            if (empty($this->potentialusers)) {
                $this->potentialusers = new potential_company_group_user_selector('potentialgroupusers', $options);
            }
            if (empty($this->currentusers)) {
                $this->currentusers = new current_company_group_user_selector('currentgroupusers', $options);
            }
        } else {
            return;
        }

    }

    public function get_data() {
        $data = parent::get_data();
        return $data;
    }

}

class course_group_users_form extends moodleform {
    protected $context = null;
    protected $selectedcompany = 0;
    protected $potentialusers = null;
    protected $currentusers = null;
    protected $courseid = null;
    protected $departmentid = 0;
    protected $companydepartment = 0;
    protected $subhierarchieslist = null;
    protected $parentlevel = null;
    protected $groupid = null;
    protected $company = null;
    protected $selectedgroup = 0;
    protected $selectedcourse = 0;
    protected $isdefault = false;
    protected $defaultgroup = array();

    public function __construct($actionurl, $context, $companyid, $departmentid, $courseid, $groupid) {
        global $USER;

        $this->selectedcompany = $companyid;
        $this->context = $context;
        $company = new company($this->selectedcompany);
        $this->company = $company;
        $this->courseid = $courseid;
        $this->groupid = $groupid;
        $this->parentlevel = company::get_company_parentnode($company->id);
        $this->companydepartment = $this->parentlevel->id;
        $context = context_system::instance();

        if (iomad::has_capability('block/iomad_company_admin:edit_all_departments', $context)) {
            $userhierarchylevel = $this->parentlevel->id;
        } else {
            $userlevel = $company->get_userlevel($USER);
            $userhierarchylevel = $userlevel->id;
        }

        $this->subhierarchieslist = company::get_all_subdepartments($userhierarchylevel);
        if ($departmentid == 0 ) {
            $this->departmentid = $userhierarchylevel;
        } else {
            $this->departmentid = $departmentid;
        }
        $this->defaultgroup = company::get_company_group($companyid, $courseid);
        if ($this->defaultgroup->id == $groupid) {
            $this->isdefault = true;
        }

        parent::__construct($actionurl);
    }

    public function create_user_selectors() {
        if (!empty ($this->groupid)) {
            $options = array('context' => $this->context,
                             'companyid' => $this->selectedcompany,
                             'courseid' => $this->courseid,
                             'groupid' => $this->groupid,
                             'departmentid' => $this->departmentid,
                             'subdepartments' => $this->subhierarchieslist,
                             'parentdepartmentid' => $this->parentlevel);
            if (empty($this->potentialusers)) {
                 $this->potentialusers = new potential_company_group_user_selector('potentialgroupusers', $options);
            }
            if (empty($this->currentusers)) {
                $this->currentusers = new current_company_group_user_selector('currentgroupusers', $options);
            }
        } else {
            return;
        }

    }

    public function definition() {
        $this->_form->addElement('hidden', 'companyid', $this->selectedcompany);
        $this->_form->addElement('hidden', 'departmentid', $this->departmentid);
        $this->_form->addElement('hidden', 'courseid', $this->courseid);
        $this->_form->addElement('hidden', 'groupid', $this->groupid);
        $this->_form->addElement('hidden', 'selectedgroup', $this->groupid);
        $this->_form->addElement('hidden', 'selectedcourse', $this->courseid);
        $this->_form->setType('companyid', PARAM_INT);
        $this->_form->setType('departmentid', PARAM_INT);
        $this->_form->setType('courseid', PARAM_INT);
        $this->_form->setType('groupid', PARAM_INT);
        $this->_form->setType('selectedgroup', PARAM_INT);
        $this->_form->setType('selectedcourse', PARAM_INT);
   }

    public function definition_after_data() {
        global $DB, $OUTPUT;

        $mform =& $this->_form;

        $this->create_user_selectors();

        // Adding the elements in the definition_after_data function rather than in the
        // definition function so that when the currentcourses or potentialcourses get
        // changed in the process function, the changes get displayed, rather than the
        // lists as they are before processing.

        if (!$this->groupid ) {
            die('No group selected.');
        }

        $course = $DB->get_record('course', array('id' => $this->courseid));
        $group = $DB->get_record('groups', array('id' => $this->groupid));

        $company = $this->company;
        $stringobj = new stdclass();
        $stringobj->group = $group->description;
        $stringobj->course = $course->fullname;
        $mform->addElement('header', 'header',
                            get_string('group_users_for', 'block_iomad_company_admin',
                            $stringobj));

        if ($this->isdefault) {
            $mform->addElement('html', '<p><strong>' . get_string('isdefaultgroupusers', 'block_iomad_company_admin') . '</strong></p>');
        }

        $mform->addElement('html', '<table summary="" class="companycourseuserstable'.
                                   ' addremovetable generaltable generalbox'.
                                   ' boxaligncenter" cellspacing="0">
            <tr>
              <td id="existingcell">');

        $mform->addElement('html', $this->currentusers->display(true));

        $mform->addElement('html', '
              </td>
              <td id="buttonscell">
                  <div id="addcontrols">
                      <input name="add" id="add" type="submit" value="' .
                       $OUTPUT->larrow().'&nbsp;'.get_string('add') .
                       '" title="Add" /><br />

                  </div>');

        if (!$this->isdefault) {

            $mform->addElement('html', '
                  <div id="removecontrols">
                      <input name="remove" id="remove" type="submit" value="' .
                       get_string('remove') . '&nbsp;' . $OUTPUT->rarrow() .
                       '" title="Remove" />
                  </div>');
        }

        $mform->addElement('html', '                  
              </td>
              <td id="potentialcell">');

        $mform->addElement('html', $this->potentialusers->display(true));

        $mform->addElement('html', '
              </td>
            </tr>
          </table>');

        // Disable the onchange popup.
        $mform->disable_form_change_checker();
    }

    public function process() {
        global $DB, $CFG;

        $this->create_user_selectors();

        // Process incoming enrolments.
        if (optional_param('add', false, PARAM_BOOL) && confirm_sesskey()) {
            $userstoassign = $this->potentialusers->get_selected_users();
            if (!empty($userstoassign)) {

                foreach ($userstoassign as $adduser) {
                    $allow = true;

                    // Check the userid is valid.
                    if (!company::check_valid_user($this->selectedcompany, $adduser->id, $this->departmentid)) {
                        print_error('invaliduserdepartment', 'block_iomad_company_management');
                    }

                    if ($allow) {
                        company_user::assign_group($adduser, $this->courseid, $this->groupid);
                    }
                }

                $this->potentialusers->invalidate_selected_users();
                $this->currentusers->invalidate_selected_users();
            }
        }

        // Process incoming unenrolments.
        if (optional_param('remove', false, PARAM_BOOL) && confirm_sesskey()) {
            $userstounassign = $this->currentusers->get_selected_users();
            if (!empty($userstounassign)) {

                foreach ($userstounassign as $removeuser) {
                    // Check the userid is valid.
                    if (!company::check_valid_user($this->selectedcompany, $removeuser->id, $this->departmentid)) {
                        print_error('invaliduserdepartment', 'block_iomad_company_management');
                    }

                    company_user::unassign_group($this->selectedcompany, $removeuser, $this->courseid, $this->groupid);
                }

                $this->potentialusers->invalidate_selected_users();
                $this->currentusers->invalidate_selected_users();
            }
        }
    }
}


$returnurl = optional_param('returnurl', '', PARAM_LOCALURL);
$courseid = optional_param('courseid', 0, PARAM_INT);
$deleteids = optional_param_array('courseids', null, PARAM_INT);
$createnew = optional_param('createnew', 0, PARAM_INT);
$selectedcourse = optional_param('selectedcourse', 0, PARAM_INTEGER);
$selectedgroup = optional_param('selectedgroup', 0, PARAM_INTEGER);
$groupids = optional_param_array('groupids', 0, PARAM_INTEGER);
$departmentid = optional_param_array('deparmentid', 0, PARAM_INTEGER);

if (!empty($groupids)) {
    $groupid = $groupids[0];
} else {
    $groupid = 0;
}

$context = context_system::instance();
require_login();

iomad::require_capability('block/iomad_company_admin:assign_groups', $context);

$urlparams = array();
if ($returnurl) {
    $urlparams['returnurl'] = $returnurl;
}
$companylist = new moodle_url('/my', $urlparams);

$linktext = get_string('managegroups', 'block_iomad_company_admin');

// Set the url.
$linkurl = new moodle_url('/blocks/iomad_company_admin/company_groups_users_form.php');

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
$mform = new course_group_display_form($PAGE->url, $companyid, $selectedcourse, $output);
if (!empty($selectedcourse) && !empty($selectedgroup)) {
    $groupform = new course_group_users_form($PAGE->url, $context, $companyid, $departmentid, $selectedcourse, $selectedgroup);
}
$courseform->set_data(array('selectedcourse' => $selectedcourse));
$mform->set_data(array('selectedgroup' => $selectedgroup));

if (!empty($groupform) && $groupform->is_cancelled()) {
    redirect($companylist);

} else {

    echo $output->header();

    // Check the department is valid.
    if (!empty($departmentid) && !company::check_valid_department($companyid, $departmentid)) {
        print_error('invaliddepartment', 'block_iomad_company_admin');
    }   

    $courseform->display();
    if (!empty($selectedcourse)) {
        $mform->display();
    }
    if (!empty($selectedgroup)) {
        $groupform->process();
        $groupform->set_data(array());
        $groupform->display();
    }

    echo $output->footer();
}

