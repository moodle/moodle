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

class company_courses_form extends moodleform {
    protected $context = null;
    protected $selectedcompany = 0;
    protected $potentialcourses = null;
    protected $currentcourses = null;
    protected $departmentid = 0;
    protected $subhierarchieslist = null;
    protected $companydepartment = 0;

    public function __construct($actionurl, $context, $companyid, $departmentid, $parentlevel) {
        global $USER;
        $this->selectedcompany = $companyid;
        $this->context = $context;
        $this->departmentid = $departmentid;

        $options = array('context' => $this->context,
                         'companyid' => $this->selectedcompany,
                         'departmentid' => $departmentid,
                         'subdepartments' => $this->subhierarchieslist,
                         'parentdepartmentid' => $parentlevel,
                         'shared' => false,
                         'licenses' => true,
                         'partialshared' => true);
        $this->potentialcourses = new potential_company_course_selector('potentialcourses',
                                                                         $options);
        $this->currentcourses = new current_company_course_selector('currentcourses', $options);

        parent::__construct($actionurl);
    }

    public function definition() {
        $this->_form->addElement('hidden', 'companyid', $this->selectedcompany);
        $this->_form->setType('companyid', PARAM_INT);
    }

    public function definition_after_data() {
        $mform =& $this->_form;

        // Adding the elements in the definition_after_data function rather than in the
        // definition function  so that when the currentcourses or potentialcourses get changed
        // in the process function, the changes get displayed, rather than the lists as they
        // are before processing.

        $context = context_system::instance();
        $company = new company($this->selectedcompany);
        $mform->addElement('hidden', 'deptid', $this->departmentid);
        $mform->setType('deptid', PARAM_INT);
        
        $mform->addElement('html', '<table summary="" class="companycoursetable addremovetable'.
                                   ' generaltable generalbox boxaligncenter" cellspacing="0">
            <tr>
              <td id="existingcell">');

        $mform->addElement('html', $this->currentcourses->display(true));

        $mform->addElement('html', '
              </td>
              <td id="buttonscell">
                  <div id="addcontrols">
                      <input name="add" id="add" type="submit" value="&#x25C4;&nbsp;'.
                       get_string('add') . '" title="Add" /><br />

                  </div>

                  <div id="removecontrols">
                      <input name="remove" id="remove" type="submit" value="'.
                       get_string('remove') . '&nbsp;&#x25BA;" title="Remove" />
                  </div>
              </td>
              <td id="potentialcell">');

        $mform->addElement('html', $this->potentialcourses->display(true));

        $mform->addElement('html', '
              </td>
            </tr>
          </table>');

        // Can this user move courses with existing enrollments
        // (which unenrolls those users as a result)?
        if (iomad::has_capability('block/iomad_company_admin:company_course_unenrol', $context)) {
            $mform->addElement('html', get_string('unenrollwarning',
                                                  'block_iomad_company_admin'));
            $mform->addElement('checkbox', 'oktounenroll',
                                get_string('oktounenroll', 'block_iomad_company_admin'));
        } else {
            $mform->addElement('html', get_string('unenrollincapable',
                                                  'block_iomad_company_admin'));
        }
    }

    public function process() {
        global $DB;

        $context = context_system::instance();

        // Get process ok to unenroll confirmation.
        $oktounenroll = optional_param('oktounenroll', false, PARAM_BOOL);

        // Process incoming assignments.
        if (optional_param('add', false, PARAM_BOOL) && confirm_sesskey()) {
            $coursestoassign = $this->potentialcourses->get_selected_courses();
            if (!empty($coursestoassign)) {

                $company = new company($this->selectedcompany);

                foreach ($coursestoassign as $addcourse) {
                    // Check if its a shared course.
                    if ($DB->get_record_sql("SELECT id FROM {iomad_courses}
                                             WHERE courseid=$addcourse->id
                                             AND shared != 0")) {
                        if ($companycourserecord = $DB->get_record('company_course', array('companyid' => $this->selectedcompany,
                                                                                           'courseid' => $addcourse->id))) {
                            // Already assigned to the company so we are just moving it within it.
                            $companycourserecord->departmentid = $this->departmentid;
                            $DB->update_record('company_course', $companycourserecord);
                        } else {
                            $sharingrecord = new stdclass();
                            $sharingrecord->courseid = $addcourse->id;
                            $sharingrecord->companyid = $company->id;
                            $DB->insert_record('company_shared_courses', $sharingrecord);
                            if ($this->departmentid != $this->companydepartment ) {
                                $company->add_course($addcourse, $this->departmentid);
                            } else {
                                $company->add_course($addcourse, $this->companydepartment);
                            }
                        }
                    } else {
                        // If company has enrollment then we must have BOTH
                        // oktounenroll true and the company_course_unenrol capability.
                        if (!empty($addcourse->has_enrollments)) {
                            if (iomad::has_capability('block/iomad_company_admin:company_course_unenrol',
                                                $context) and $oktounenroll) {
                                $this->unenroll_all($addcourse->id);
                                $company->add_course($addcourse);
                            }
                        } else if ($companycourserecord = $DB->get_record('company_course', array('companyid' => $this->selectedcompany,
                                                                                                  'courseid' => $addcourse->id))) {
                            $companycourserecord->departmentid = $this->departmentid;
                            $DB->update_record('company_course', $companycourserecord);
                        } else {
                            if ($this->departmentid != $this->companydepartment ) {
                                $company->add_course($addcourse, $this->departmentid);
                            } else {
                                $company->add_course($addcourse, $this->companydepartment);
                            }
                        }
                    }
                }

                $this->potentialcourses->invalidate_selected_courses();
                $this->currentcourses->invalidate_selected_courses();
            }
        }

        // Process incoming unassignments.
        if (optional_param('remove', false, PARAM_BOOL) && confirm_sesskey()) {
            $coursestounassign = $this->currentcourses->get_selected_courses();
            if (!empty($coursestounassign)) {

                $company = new company($this->selectedcompany);

                foreach ($coursestounassign as $removecourse) {

                    // Check if its a shared course.
                    if ($DB->get_record_sql("SELECT id FROM {iomad_courses}
                                             WHERE courseid=:removecourse
                                             AND shared != 0",
                                             array('removecourse' => $removecourse->id))) {
                        $DB->delete_records('company_shared_courses',
                                             array('companyid' => $company->id,
                                                   'courseid' => $removecourse->id));
                        $DB->delete_records('company_course',
                                             array('companyid' => $company->id,
                                                   'courseid' => $removecourse->id));
                        company::delete_company_course_group($company->id,
                                                             $removecourse,
                                                             $oktounenroll);
                    } else {
                        // If company has enrollment then we must have BOTH
                        // oktounenroll true and the company_course_unenrol capability.
                        if (!empty($removecourse->has_enrollments)) {
                            if (iomad::has_capability('block/iomad_company_admin:company_course_unenrol',
                                                $context) and $oktounenroll) {
                                $this->unenroll_all($removecourse->id);
                                if ($this->departmentid != $this->companydepartment) {
                                    // Dump it into the default company department.
                                    $company->remove_course($removecourse,
                                                            $company->id,
                                                            $this->companydepartment);
                                } else {
                                    // Remove it from the company.
                                    $company->remove_course($removecourse, $company->id);
                                }
                            }
                        } else {
                            if ($this->departmentid != $this->companydepartment) {
                                // Move the course to the company default department.
                                $company->remove_course($removecourse, $company->id,
                                                        $this->companydepartment);
                            } else {
                                $company->remove_course($removecourse, $company->id);
                            }
                        }
                    }
                }

                $this->potentialcourses->invalidate_selected_courses();
                $this->currentcourses->invalidate_selected_courses();
            }
        }
    }

    private function unenroll_all($id) {
        global $DB, $PAGE;
        // Unenroll everybody from given course.

        // Get list of enrollments.
        $course = $DB->get_record('course', array('id' => $id));
        $courseenrolment = new course_enrolment_manager($PAGE, $course);
        $userlist = $courseenrolment->get_users('', 'ASC', 0, 0);
        foreach ($userlist as $user) {
            $ues = $courseenrolment->get_user_enrolments($user->id);
            foreach ($ues as $ue) {
                $courseenrolment->unenrol_user($ue);
            }
        }
    }
}

$returnurl = optional_param('returnurl', '', PARAM_LOCALURL);
$companyid = optional_param('companyid', 0, PARAM_INTEGER);
$departmentid = optional_param('deptid', 0, PARAM_INTEGER);

$context = context_system::instance();
require_login();
iomad::require_capability('block/iomad_company_admin:company_course', $context);

$urlparams = array('companyid' => $companyid);
if ($returnurl) {
    $urlparams['returnurl'] = $returnurl;
}

// Correct the navbar.
// Set the name for the page.
$linktext = get_string('assigncourses', 'block_iomad_company_admin');
// Set the url.
$linkurl = new moodle_url('/blocks/iomad_company_admin/company_courses_form.php');

// Print the page header.
$PAGE->set_context($context);
$PAGE->set_url($linkurl);
$PAGE->set_pagelayout('admin');
$PAGE->set_title($linktext);
// Set the page heading.
$PAGE->set_heading(get_string('myhome') . " - $linktext");

// Build the nav bar.
company_admin_fix_breadcrumb($PAGE, $linktext, $linkurl);

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
$departmentselect = new single_select(new moodle_url($linkurl, $urlparams), 'deptid', $subhierarchieslist, $departmentid);
$departmentselect->label = get_string('department', 'block_iomad_company_admin') .
                           $OUTPUT->help_icon('department', 'block_iomad_company_admin') . '&nbsp';

$mform = new company_courses_form($PAGE->url, $context, $companyid, $departmentid, $parentlevel);

if ($mform->is_cancelled()) {
    if ($returnurl) {
        redirect($returnurl);
    } else {
        redirect(new moodle_url('/my'));
    }
} else {
    $mform->process();

    echo $OUTPUT->header();

    // Check the department is valid.
    if (!empty($departmentid) && !company::check_valid_department($companyid, $departmentid)) {
        print_error('invaliddepartment', 'block_iomad_company_admin');
    }

    echo html_writer::tag('h3', get_string('company_courses_for',
                                                          'block_iomad_company_admin',
                                                          $company->get_name()));

    echo $OUTPUT->render($departmentselect);

    $mform->display();

    echo $OUTPUT->footer();
}
