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
require_once($CFG->dirroot.'/local/email/lib.php');

class company_ccu_courses_form extends company_moodleform {
    protected $context = null;
    protected $selectedcompany = 0;
    protected $potentialcourses = null;
    protected $currentcourses = null;
    protected $departmentid = 0;
    protected $subhierarchieslist = null;
    protected $companydepartment = 0;
    protected $selectedcourse = 0;


    public function __construct($actionurl, $context, $companyid, $departmentid, $selectedcourse) {
        global $USER;
        $this->selectedcompany = $companyid;
        $this->context = $context;
        $this->departmentid = $departmentid;
        $this->selectedcourse = $selectedcourse;

        $company = new company($this->selectedcompany);
        $parentlevel = company::get_company_parentnode($company->id);
        $this->companydepartment = $parentlevel->id;
        $context = context_system::instance();

        if (iomad::has_capability('block/iomad_company_admin:edit_all_departments', $context)) {
            $userhierarchylevel = $parentlevel->id;
        } else {
            $userlevel = company::get_userlevel($USER);
            $userhierarchylevel = $userlevel->id;
        }

        $this->subhierarchieslist = company::get_all_subdepartments($userhierarchylevel);
        if ($this->departmentid == 0 ) {
            $departmentid = $userhierarchylevel;
        } else {
            $departmentid = $this->departmentid;
        }

        $options = array('context' => $this->context,
                         'multiselect' => false,
                         'companyid' => $this->selectedcompany,
                         'departmentid' => $departmentid,
                         'subdepartments' => $this->subhierarchieslist,
                         'parentdepartmentid' => $parentlevel,
                         'licenses' => false, 'shared' => false);
        $this->currentcourses = new current_company_course_selector('currentcourses', $options);
        $this->currentcourses->set_rows(1);

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

        $company = new company($this->selectedcompany);

        $mform->addElement('header', 'header',
                            get_string('company_courses_for',
                                      'block_iomad_company_admin',
                                      $company->get_name() ));
        $mform->addElement('select', 'departmentid',
                            get_string('department', 'block_iomad_company_admin'),
                            $this->subhierarchieslist);
        $mform->addElement('submit', 'updateselection',
                            get_string('updatedepartmentusersselection',
                                       'block_iomad_company_admin'));

        $mform->addElement('html', "<div class='fitem'><div class='fitemtitle'>" .
                                get_string('selectenrolmentcourse', 'block_iomad_company_admin') .
                                "</div><div class='felement'>");
        $mform->addElement('html', $this->currentcourses->display(true));
        $mform->addElement('html', "</div></div>");

        if ($this->currentcourses) {
            $this->add_action_buttons(true,
                              get_string('enrolunenrollusers', 'block_iomad_company_admin'));
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

class company_course_users_form extends moodleform {
    protected $context = null;
    protected $selectedcompany = 0;
    protected $potentialusers = null;
    protected $currentusers = null;
    protected $course = null;
    protected $departmentid = 0;
    protected $companydepartment = 0;
    protected $subhierarchieslist = null;
    protected $parentlevel = null;

    public function __construct($actionurl, $context, $companyid, $departmentid) {
        global $USER;
        $this->selectedcompany = $companyid;
        $this->context = $context;
        $company = new company($this->selectedcompany);
        $this->parentlevel = company::get_company_parentnode($company->id);
        $this->companydepartment = $this->parentlevel->id;
        $context = context_system::instance();

        if (iomad::has_capability('block/iomad_company_admin:edit_all_departments', $context)) {
            $userhierarchylevel = $this->parentlevel->id;
        } else {
            $userlevel = company::get_userlevel($USER);
            $userhierarchylevel = $userlevel->id;
        }

        $this->subhierarchieslist = company::get_all_subdepartments($userhierarchylevel);
        if ($departmentid == 0 ) {
            $this->departmentid = $userhierarchylevel;
        } else {
            $this->departmentid = $departmentid;
        }

        parent::__construct($actionurl);
    }

    public function set_course($courses) {
        $keys = array_keys($courses);
        $this->course = $courses[$keys[0]];
    }

    public function create_user_selectors() {
        if (!empty ($this->course->id)) {
            $options = array('context' => $this->context,
                             'companyid' => $this->selectedcompany,
                             'courseid' => $this->course->id,
                             'departmentid' => $this->departmentid,
                             'subdepartments' => $this->subhierarchieslist,
                             'parentdepartmentid' => $this->parentlevel);
            if (! $this->potentialusers) {
                $this->potentialusers = new potential_company_course_user_selector('potentialcourseusers', $options);
            }
            if (! $this->currentusers) {
                $this->currentusers = new current_company_course_user_selector('currentlyenrolledusers', $options);
            }
        } else {
            return;
        }

    }

    public function definition() {
        $this->_form->addElement('hidden', 'companyid', $this->selectedcompany);
        $this->_form->addElement('hidden', 'departmentid', $this->departmentid);
        $this->_form->setType('companyid', PARAM_INT);
        $this->_form->setType('departmentid', PARAM_INT);
    }

    public function definition_after_data() {
        $mform =& $this->_form;

        if (!empty($this->course->id)) {
            $this->_form->addElement('hidden', 'courseid', $this->course->id);
        }
        $this->create_user_selectors();

        // Adding the elements in the definition_after_data function rather than in the
        // definition function so that when the currentcourses or potentialcourses get
        // changed in the process function, the changes get displayed, rather than the
        // lists as they are before processing.

        if (!$this->course ) {
            die('No course selected.');
        }

        $company = new company($this->selectedcompany);
        $mform->addElement('header', 'header',
                            get_string('company_users_for', 'block_iomad_company_admin',
                            $this->course->fullname ));

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
                      <input name="add" id="add" type="submit" value="&#x25C4;&nbsp;' .
                       get_string('enrol', 'block_iomad_company_admin') .
                       '" title="Enrol" /><br />

                  </div>

                  <div id="removecontrols">
                      <input name="remove" id="remove" type="submit" value="' .
                       get_string('unenrol', 'block_iomad_company_admin') .
                       '&nbsp;&#x25BA;" title="Unenrol" />
                  </div>
              </td>
              <td id="potentialcell">');

        $mform->addElement('html', $this->potentialusers->display(true));

        $mform->addElement('html', '
              </td>
            </tr>
          </table>');
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
                        company_user::enrol($adduser, array($this->course->id),
                                                            $this->selectedcompany);
                        EmailTemplate::send('user_added_to_course',
                                             array('course' => $this->course,
                                                   'user' => $adduser));
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
                    if (!company::check_valid_user($this->selectedcompany, $removeuser  ->id, $this->departmentid)) {
                        print_error('invaliduserdepartment', 'block_iomad_company_management');
                    }

                    company_user::unenrol($removeuser, array($this->course->id),
                                                             $this->selectedcompany);
                }

                $this->potentialusers->invalidate_selected_users();
                $this->currentusers->invalidate_selected_users();
            }
        }
    }
}


$returnurl = optional_param('returnurl', '', PARAM_LOCALURL);
$companyid = optional_param('companyid', 0, PARAM_INTEGER);
$courseid = optional_param('courseid', 0, PARAM_INTEGER);
$departmentid = optional_param('departmentid', 0, PARAM_INTEGER);
$selectedcourse = optional_param('currentcourses', 0, PARAM_INTEGER);

$context = context_system::instance();
require_login();
$PAGE->set_context($context);

$urlparams = array('companyid' => $companyid);
if ($returnurl) {
    $urlparams['returnurl'] = $returnurl;
}
if ($courseid) {
    $urlparams['courseid'] = $courseid;
}

// Correct the navbar.
// Set the name for the page.
$linktext = get_string('enroluser', 'block_iomad_company_admin');
// Set the url.
$linkurl = new moodle_url('/blocks/iomad_company_admin/company_course_users_form.php');
// Build the nav bar.
company_admin_fix_breadcrumb($PAGE, $linktext, $linkurl);

$blockpage = new blockpage($PAGE, $OUTPUT, 'iomad_company_admin', 'block',
                           'company_course_users_title');
$blockpage->setup();

require_login(null, false); // Adds to $PAGE, creates $OUTPUT.
iomad::require_capability('block/iomad_company_admin:company_course_users', $context);

// Set the companyid
$companyid = iomad::get_my_companyid($context);

$coursesform = new company_ccu_courses_form($PAGE->url, $context, $companyid, $departmentid, $selectedcourse);
$usersform = new company_course_users_form($PAGE->url, $context, $companyid, $departmentid);
$blockpage->display_header();

// Check the department is valid.
if (!empty($departmentid) && !company::check_valid_department($companyid, $departmentid)) {
    print_error('invaliddepartment', 'block_iomad_company_admin');
}   

$ccuparamarray = array();
if (!empty($departmentid)) {

    $ccuparamaray['departmentid'] = $departmentid;
}

if (!empty($courseid)) {
    $ccuparamarray['currentcourses'] = $courseid;
}

if ($coursesform->is_cancelled() || $usersform->is_cancelled() ||
     optional_param('cancel', false, PARAM_BOOL) ) {
    if ($returnurl) {
        redirect($returnurl);
    } else {
        redirect(new moodle_url('/local/iomad_dashboard/index.php'));
    }
} else {
    if ($companyid > 0) {
        $coursesform->set_data($ccuparamarray);
        echo $coursesform->display();
        if ($data = $coursesform->get_data()) {
            if (!empty($data->selectedcourses)) {
                $usersform->set_course($data->selectedcourses);
            }
            echo $usersform->display();
        } else if ($courseid > 0) {
            global $DB;
            $course = $DB->get_record('course', array('id' => $courseid));
            $usersform->set_course(array($course));
            $usersform->process();
            echo $usersform->display();
        }
    }

    echo $OUTPUT->footer();
}
