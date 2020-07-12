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
    protected $selectedcourses = 0;
    protected $company = null;
    protected $courses = array();


    public function __construct($actionurl, $context, $companyid, $departmentid, $selectedcourses, $parentlevel) {
        global $DB, $USER;
        $this->selectedcompany = $companyid;
        $this->company = new company($companyid);
        $this->context = $context;
        $this->departmentid = $departmentid;
        $this->selectedcourses = $selectedcourses;

        $options = array('context' => $this->context,
                         'multiselect' => false,
                         'companyid' => $this->selectedcompany,
                         'departmentid' => $departmentid,
                         'subdepartments' => $this->subhierarchieslist,
                         'parentdepartmentid' => $parentlevel,
                         'licenses' => false,
                         'shared' => false);
        $this->companycourses = $this->company->get_menu_courses(true, true);
        $this->companycourses[0] = get_string('all');

        parent::__construct($actionurl);
    }


    public function definition() {
        $this->_form->addElement('hidden', 'companyid', $this->selectedcompany);
        $this->_form->setType('companyid', PARAM_INT);
        $this->_form->addElement('hidden', 'deptid', $this->departmentid);
        $this->_form->setType('deptid', PARAM_INT);
    }


    public function definition_after_data() {
        $mform =& $this->_form;
        // Adding the elements in the definition_after_data function rather than in the definition
        // function so that when the currentcourses or potentialcourses get changed in the process
        // function, the changes get displayed, rather than the lists as they are before processing.

        //$courses = $this->currentcourses->find_courses('');
        if ($this->companycourses) {

        // We are going to cheat and be lazy here.
            $autooptions = array('multiple' => true,
                                 'noselectionstring' => get_string('none'),
                                 'onchange' => 'this.form.submit()');
            $mform->addElement('autocomplete', 'selectedcourses', get_string('selectenrolmentcourse', 'block_iomad_company_admin'), $this->companycourses, $autooptions);
        } else {
            $mform->addElement('html', '<div class="alert alert-warning">' . get_string('nocourses', 'block_iomad_company_admin') . '</div>');
        }

        // Disable the onchange popup.
        $mform->disable_form_change_checker();
    }
}

class company_course_users_form extends moodleform {
    protected $context = null;
    protected $selectedcompany = 0;
    protected $selectedcourses = 0;
    protected $potentialusers = null;
    protected $currentusers = null;
    protected $coursea = null;
    protected $departmentid = 0;
    protected $companydepartment = 0;
    protected $subhierarchieslist = null;
    protected $parentlevel = null;
    protected $groups = null;
    protected $company = null;

    public function __construct($actionurl, $context, $companyid, $departmentid, $courses) {
        global $USER, $DB;
        $this->selectedcompany = $companyid;
        $this->selectedcourses = $courses;
        $this->context = $context;
        $company = new company($this->selectedcompany);
        $this->company = $company;
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

        parent::__construct($actionurl);
    }

    public function set_course($courses) {
        global $DB;

        if (!in_array(0, $this->selectedcourses) && count($this->selectedcourses) == 1 && !$this->groups = $DB->get_records_sql_menu("SELECT g.id, g.description
                                                   FROM {groups} g
                                                   JOIN {company_course_groups} ccg
                                                   ON (g.id = ccg.groupid)
                                                   WHERE ccg.companyid = :companyid
                                                   AND ccg.courseid in (:courseids)",
                                                   array('companyid' => $this->selectedcompany,
                                                         'courseids' => join(',', array_values($this->selectedcourses))))) {
            $this->groups = array($this->company->get_name());
        }
    }

    public function create_user_selectors() {
        if (!empty ($this->selectedcourses)) {
            $options = array('context' => $this->context,
                             'companyid' => $this->selectedcompany,
                             'selectedcourses' => $this->selectedcourses,
                             'departmentid' => $this->departmentid,
                             'subdepartments' => $this->subhierarchieslist,
                             'parentdepartmentid' => $this->parentlevel,
                             'class' => 'potential_company_course_user_selector');
            if (empty($this->potentialusers)) {
                $this->potentialusers = new potential_company_course_user_selector('potentialcourseusers', $options);
            }
            $options['class'] = 'current_company_course_user_selector';
            if (empty($this->currentusers)) {
                $this->currentusers = new current_company_course_user_selector('currentlyenrolledusers', $options);
            }
        } else {
            return;
        }

    }

    public function definition() {
        $this->_form->addElement('hidden', 'companyid', $this->selectedcompany);
        $this->_form->addElement('hidden', 'deptid', $this->departmentid);
        // Deal with the selected courses array.
        foreach ($this->selectedcourses as $a => $b) {
            $this->_form->addElement('hidden', "selectedcourses[$a]", $b);
            $this->_form->setType("selectedcourses[$a]", PARAM_INT);
        }
        $this->_form->setType('companyid', PARAM_INT);
        $this->_form->setType('deptid', PARAM_INT);
    }

    public function definition_after_data() {
        global $DB, $output;

        $mform =& $this->_form;

        if (!empty($this->selectedcourses)) {
            foreach ($this->selectedcourses as $a => $b) {
                $this->_form->addElement('hidden', "courses[$a]", $b);
                $this->_form->setType("courses[$a]", PARAM_INT);
            }
        }
        $this->create_user_selectors();

        // Adding the elements in the definition_after_data function rather than in the
        // definition function so that when the currentcourses or potentialcourses get
        // changed in the process function, the changes get displayed, rather than the
        // lists as they are before processing.

        if (empty($this->selectedcourses)) {
            die('No course selected.');
        }

        $company = new company($this->selectedcompany);

        if (count($this->selectedcourses) == 1 && !in_array(0, $this->selectedcourses)) {
            foreach ($this->selectedcourses as $courseid) {
                $course = $DB->get_record('course', array('id' => $courseid));
            }
        } else {
            $course = new stdclass();
            $namestring = $company->get('name');
            $course->fullname = $namestring;
            $course->id = 0;
        }
        $mform->addElement('header', 'header',
                            get_string('company_users_for', 'block_iomad_company_admin',
                            format_string($course->fullname, true, 1) ));

        $mform->addElement('date_time_selector', 'due', get_string('senddate', 'block_iomad_company_admin'));
        $mform->addHelpButton('due', 'senddate', 'block_iomad_company_admin');

        if (in_array(0, $this->selectedcourses) || count($this->selectedcourses) != 1) {
            $mform->addElement('hidden', 'groupid', 0);
            $mform->setType('groupid', PARAM_INT);
        } else {
            if ($DB->get_record('iomad_courses', array('courseid' => $course->id, 'shared' => 0))) {
                $mform->addElement('hidden', 'groupid', 0);
                $mform->setType('groupid', PARAM_INT);
            } else {
                $mform->addElement('autocomplete', 'groupid', get_string('group'),
                                   $this->groups,
                                   array('setmultiple' => false,
                                         'onchange' => 'this.form.submit()'));
            }
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
                      <input name="add" id="add" type="submit" value="&nbsp;' .
                      $output->larrow().'&nbsp;'. get_string('enrol', 'block_iomad_company_admin') .
                       '" title="Enrol" /></br>
                      <input name="addall" id="addall" type="submit" value="&nbsp;' .
                      $output->larrow().'&nbsp;'. get_string('enrolall', 'block_iomad_company_admin') .
                      '" title="Enrolall" /></br>

                      <input name="remove" id="remove" type="submit" value="' .
                       $output->rarrow().'&nbsp;'. get_string('unenrol', 'block_iomad_company_admin') .
                       '&nbsp;" title="Unenrol" /></br>
                      <input name="removeall" id="removeall" type="submit" value="&nbsp;' .
                      $output->rarrow().'&nbsp;'. get_string('unenrolall', 'block_iomad_company_admin') .
                      '" title="Enrolall" /></br>
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
        $data = $this->get_data();

        $addall = false;
        $add = false;
        if (optional_param('addall', false, PARAM_BOOL) && confirm_sesskey()) {
            $search = optional_param('potentialcourseusers_searchtext', '', PARAM_RAW);
            // Process incoming allocations.
            $potentialusers = $this->potentialusers->find_users($search, true);
            $userstoassign = array_pop($potentialusers);
            $addall = true;
        }
        if (optional_param('add', false, PARAM_BOOL) && confirm_sesskey()) {
            $userstoassign = $this->potentialusers->get_selected_users();
            $add = true;
        }

        // Sort out which courses it's going to be for.
        if (in_array(0, $this->selectedcourses)) {
            $courses = array_keys($this->company->get_menu_courses(true, true));
            unset($courses[0]);
        } else {
            $courses = array_values($this->selectedcourses);
        }

        if ($add || $addall) {
            // Process incoming enrolments.
            if (!empty($userstoassign)) {
                foreach ($userstoassign as $adduser) {
                    $allow = true;

                    // Check the userid is valid.
                    if (!company::check_valid_user($this->selectedcompany, $adduser->id, $this->departmentid)) {
                        print_error('invaliduserdepartment', 'block_iomad_company_management');
                    }

                    if ($allow) {
                        $due = optional_param_array('due', array(), PARAM_INT);
                        if (!empty($due)) {
                            $duedate = strtotime($due['year'] . '-' . $due['month'] . '-' . $due['day'] . ' ' . $due['hour'] . ':' . $due['minute']);
                        } else {
                            $duedate = 0;
                        }

                        // Enrol the user on the courses.
                        foreach ($courses as $courseid) {
                            $course = $DB->get_record('course', array('id' => $courseid));
                            company_user::enrol($adduser,
                                                array($courseid),
                                                $this->selectedcompany,
                                                0,
                                                $data->groupid);
                            EmailTemplate::send('user_added_to_course',
                                                 array('course' => $course,
                                                       'user' => $adduser,
                                                       'due' => $duedate));
                        }
                    }
                }

                $this->potentialusers->invalidate_selected_users();
                $this->currentusers->invalidate_selected_users();
            }
        }
        $removeall = false;;
        $remove = false;
        $userstounassign = array();

        if (optional_param('removeall', false, PARAM_BOOL) && confirm_sesskey()) {
            $search = optional_param('currentlyenrolledusers_searchtext', '', PARAM_RAW);
            // Process incoming allocations.
            $potentialusers = $this->currentusers->find_users($search, true);
            $userstounassign = array_pop($potentialusers);
            $removeall = true;
        }
        if (optional_param('remove', false, PARAM_BOOL) && confirm_sesskey()) {
            $userstounassign = $this->currentusers->get_selected_users();
            $remove = true;
        }
        // Process incoming unallocations.
        if ($remove || $removeall) {
            if (!empty($userstounassign)) {

                foreach ($userstounassign as $removeuser) {
                    if ($removeuser->id != $removeuser->userid) {
                        $removeuser->id = $removeuser->userid;
                    }
                    // Check the userid is valid.
                    if (!company::check_valid_user($this->selectedcompany, $removeuser->userid, $this->departmentid)) {
                        print_error('invaliduserdepartment', 'block_iomad_company_management');
                    }

                    // Unenrol the user on the courses.
                    foreach ($courses as $courseid) {
                        company_user::unenrol($removeuser, array($courseid),
                                                                 $this->selectedcompany);
                    }
                }

                $this->potentialusers->invalidate_selected_users();
                $this->currentusers->invalidate_selected_users();
            }
        }
    }
}

$returnurl = optional_param('returnurl', '', PARAM_LOCALURL);
$companyid = optional_param('companyid', 0, PARAM_INTEGER);
$courses = optional_param_array('courses', array(), PARAM_INTEGER);
$departmentid = optional_param('deptid', 0, PARAM_INTEGER);
$selectedcourses = optional_param_array('selectedcourses', array('-1'), PARAM_INTEGER);
$groupid = optional_param('groupid', 0, PARAM_INTEGER);

$context = context_system::instance();
require_login();

$params = array('companyid' => $companyid,
                'courses' => $courses,
                'deptid' => $departmentid,
                'selectedcourses' => $selectedcourses,
                'groupid' => $groupid);

$urlparams = array('companyid' => $companyid);
if ($returnurl) {
    $urlparams['returnurl'] = $returnurl;
}
if (!empty($courses)) {
    foreach ($courses as $a => $b)
    $urlparams["courses[$a]"] = $b;
}
if (!empty($selectedcourses)) {
    foreach ($selectedcourses as $a => $b)
    $urlparams["selectedcourses[$a]"] = $b;
}
// Correct the navbar.
// Set the name for the page.
$linktext = get_string('company_course_users_title', 'block_iomad_company_admin');
// Set the url.
$linkurl = new moodle_url('/blocks/iomad_company_admin/company_course_users_form.php');

// Print the page header.
$PAGE->set_context($context);
$PAGE->set_url($linkurl);
$PAGE->set_pagelayout('admin');
$PAGE->set_title($linktext);
// Set the page heading.
$PAGE->set_heading(get_string('myhome') . " - $linktext");
if (empty($CFG->defaulthomepage)) {
    $PAGE->navbar->add(get_string('dashboard', 'block_iomad_company_admin'), new moodle_url($CFG->wwwroot . '/my'));
}

// get output renderer
$output = $PAGE->get_renderer('block_iomad_company_admin');

// Javascript for fancy select.
// Parameter is name of proper select form element followed by 1=submit its form
$PAGE->requires->js_call_amd('block_iomad_company_admin/department_select', 'init', array('deptid', 1, optional_param('deptid', 0, PARAM_INT)));
$PAGE->navbar->add($linktext, $linkurl);

require_login(null, false); // Adds to $PAGE, creates $output.
iomad::require_capability('block/iomad_company_admin:company_course_users', $context);
// Set the companyid
$companyid = iomad::get_my_companyid($context);
$parentlevel = company::get_company_parentnode($companyid);
$companydepartment = $parentlevel->id;
$syscontext = context_system::instance();
$company = new company($companyid);

if (iomad::has_capability('block/iomad_company_admin:edit_all_departments', $syscontext)) {
    $userhierarchylevel = $parentlevel->id;
} else {
    $userlevel = $company->get_userlevel($USER);
    $userhierarchylevel = $userlevel->id;
}

$subhierarchieslist = company::get_all_subdepartments($userhierarchylevel);
if (empty($departmentid)) {
    $departmentid = $userhierarchylevel;
}

$userdepartment = $company->get_userlevel($USER);
$departmenttree = company::get_all_subdepartments_raw($userdepartment->id);
$treehtml = $output->department_tree($departmenttree, optional_param('deptid', 0, PARAM_INT));

$departmentselect = new single_select(new moodle_url($linkurl, $urlparams), 'deptid', $subhierarchieslist, $departmentid);
$departmentselect->label = get_string('department', 'block_iomad_company_admin') .
                           $output->help_icon('department', 'block_iomad_company_admin') . '&nbsp';

$coursesform = new company_ccu_courses_form($PAGE->url, $context, $companyid, $departmentid, $selectedcourses, $parentlevel);
$coursesform->set_data(array('selectedcourses' => $selectedcourses, 'courses' => $courses));
$usersform = new company_course_users_form($PAGE->url, $context, $companyid, $departmentid, $selectedcourses);
echo $output->header();

// Check the department is valid.
if (!empty($departmentid) && !company::check_valid_department($companyid, $departmentid)) {
    print_error('invaliddepartment', 'block_iomad_company_admin');
}

if ($coursesform->is_cancelled() || $usersform->is_cancelled() ||
     optional_param('cancel', false, PARAM_BOOL) ) {
    if ($returnurl) {
        redirect($returnurl);
    } else {
        redirect(new moodle_url('/my'));
    }
} else {
    echo html_writer::tag('h3', get_string('company_courses_for', 'block_iomad_company_admin', $company->get_name()));
    echo html_writer::start_tag('div', array('class' => 'fitem'));
    echo $treehtml;
    echo html_writer::start_tag('div', array('style' => 'display:none'));
    echo $output->render($departmentselect);
    echo html_writer::end_tag('div');
    echo html_writer::end_tag('div');
    echo html_writer::start_tag('div', array('class' => 'iomadclear'));
    if ($companyid > 0) {
        $coursesform->set_data($params);
        echo $coursesform->display();
        if (!in_array('-1', $selectedcourses, true)) {
            if ($data = $coursesform->get_data() || empty($selectedcourses)) {
                 if (count($courses) > 0) {
                    $usersform->set_course(array($courses));
                    $usersform->process();
                    $usersform = new company_course_users_form($PAGE->url, $context, $companyid, $departmentid, $selectedcourses);
                    $usersform->set_course(array($courses));
                    $usersform->set_data(array('groupid' => $groupid));
                } else if (!empty($selectedcourses)) {
                    $usersform->set_course($selectedcourses);
                }
                echo $usersform->display();
            } else if (count($courses) > 0) {
                $usersform->set_course(array($courses));
                $usersform->process();
                $usersform = new company_course_users_form($PAGE->url, $context, $companyid, $departmentid, $selectedcourses);
                $usersform->set_course(array($courses));
                $usersform->set_data(array('groupid' => $groupid));
                echo $usersform->display();
            }
        }
    }
    echo html_writer::end_tag('div');

    echo $output->footer();
}
