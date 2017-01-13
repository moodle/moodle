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

class company_managers_form extends moodleform {
    protected $context = null;
    protected $selectedcompany = 0;
    protected $potentialusers = null;
    protected $currentusers = null;
    protected $departmentid = 0;
    protected $roletype = 0;
    protected $subhierarchieslist = null;
    protected $companydepartment = 0;

    public function __construct($actionurl, $context, $companyid, $deptid, $roleid, $showothermanagers) {
        global $USER;
        $this->selectedcompany = $companyid;
        $this->context = $context;
        $this->departmentid = $deptid;
        $this->roletype = $roleid;
        if (!iomad::has_capability('block/iomad_company_admin:company_add', context_system::instance())) {
            $this->showothermanagers = false;
        } else {
            $this->showothermanagers = $showothermanagers;
        }

        $company = new company($this->selectedcompany);
        $parentlevel = company::get_company_parentnode($company->id);
        $this->companydepartment = $parentlevel->id;
        if (iomad::has_capability('block/iomad_company_admin:edit_all_departments', context_system::instance())) {
            $userhierarchylevel = $parentlevel->id;
        } else {
            $userlevel = company::get_userlevel($USER);
            $userhierarchylevel = $userlevel->id;
        }

        $this->subhierarchieslist = company::get_all_subdepartments($userhierarchylevel);
        if ($this->departmentid == 0) {
            $departmentid = $userhierarchylevel;
        } else {
            $departmentid = $this->departmentid;
        }
        $options = array('context' => $this->context,
                         'companyid' => $this->selectedcompany,
                         'departmentid' => $departmentid,
                         'roletype' => $this->roletype,
                         'subdepartments' => $this->subhierarchieslist,
                         'parentdepartmentid' => $parentlevel,
                         'showothermanagers' => $this->showothermanagers);
        $this->potentialusers = new potential_department_user_selector('potentialmanagers', $options);
        $this->currentusers = new current_department_user_selector('currentmanagers', $options);

        parent::__construct($actionurl);
    }

    public function definition() {
        $this->_form->addElement('hidden', 'companyid', $this->selectedcompany);
        $this->_form->setType('companyid', PARAM_INT);
    }

    public function definition_after_data() {
        global $USER;
        $mform =& $this->_form;

        // Adding the elements in the definition_after_data function rather than in the definition function
        // so that when the currentmanagers or potentialmanagers get changed in the process function, the
        // changes get displayed, rather than the lists as they are before processing.

        $company = new company($this->selectedcompany);
        $mform->addElement('hidden', 'showothermanagers', $this->showothermanagers);
        $mform->setType('showothermanagers', PARAM_INT);
        $mform->addElement('hidden', 'deptid', $this->departmentid);
        $mform->setType('deptid', PARAM_INT);
        $mform->addElement('hidden', 'managertype', $this->roletype);
        $mform->setType('managertype', PARAM_INT);

        if (count($this->potentialusers->find_users('')) || count($this->currentusers->find_users(''))) {

            $mform->addElement('html', '<table summary=""
                                        class="companymanagertable addremovetable generaltable generalbox boxaligncenter"
                                        cellspacing="0">
                <tr>
                  <td id="existingcell">');

            $mform->addElement('html', $this->currentusers->display(true));

            $mform->addElement('html', '
                  </td>
                  <td id="buttonscell">
                      <div id="addcontrols">
                          <input name="add" id="add" type="submit" value="&#x25C4;&nbsp;' .
                          get_string('add') . '" title="Add" /><br />

                      </div>

                      <div id="removecontrols">
                          <input name="remove" id="remove" type="submit" value="' .
                          get_string('remove') . '&nbsp;&#x25BA;" title="Remove" />
                      </div>
                  </td>
                  <td id="potentialcell">');

            $mform->addElement('html', $this->potentialusers->display(true));

            $mform->addElement('html', '
                  </td>
                </tr>
              </table>');
        } else {
            $mform->addElement('html', get_string('nousers', 'block_iomad_company_admin').
            ' <a href="'. new moodle_url('/blocks/iomad_company_admin/company_user_create_form.php?companyid='.
            $this->selectedcompany).
            '">Create one now</a>');
        }
    }

    public function process($departmentid, $roletype) {
        global $DB, $USER;

        $companymanagerrole = $DB->get_record('role', array('shortname' => 'companymanager'));
        $departmentmanagerrole = $DB->get_record('role', array('shortname' => 'companydepartmentmanager'));
        $companycoursenoneditorrole = $DB->get_record('role', array('shortname' => 'companycoursenoneditor'));
        $companycourseeditorrole = $DB->get_record('role', array('shortname' => 'companycourseeditor'));

        // Process incoming assignments.
        if (optional_param('add', false, PARAM_BOOL) && confirm_sesskey()) {
            $userstoassign = $this->potentialusers->get_selected_users();
            if (!empty($userstoassign)) {

                foreach ($userstoassign as $adduser) {
                    $allow = true;

                // Check the userid is valid.
                if (!company::check_valid_user($this->selectedcompany, $adduser->id, $this->departmentid)) {
                    // The userid may still be valid, but only if we are assigning an external company manager
                    // require permissions, check roletype is manager & the userid is actually a manager in another company
                    if (!iomad::has_capability('block/iomad_company_admin:company_add', context_system::instance()) && $roletype == 1 &&
                        $DB->get_record_sql('SELECT id FROM {company_users}
                                             WHERE
                                             userid = :userid
                                             AND managertype = :roletype
                                             AND companyid != :companyid', array('userid' => $adduser->id,
                                                                                 'roletype' => 1,
                                                                                 'companyid' => $this->selectedcompany))) {
                        // We are not assigning an external company manager AND the userid is not valid for this company
                        print_error('invaliduserdepartment', 'block_iomad_company_management');
                    }
                }

                if ($allow) {
                        if ($roletype != 0) {
                            // Adding a manager type.
                            // Add user to the company manager table.
                            if ($userrecord = $DB->get_record('company_users',
                                                                   array('companyid' => $this->selectedcompany,
                                                                         'userid' => $adduser->id))) {

                                if ($departmentid != $userrecord->departmentid) {
                                    $userrecord->departmentid = $departmentid;
                                }

                                if ($roletype == 1 && $userrecord->managertype == 0) {
                                    // Give them the company manager role.
                                    role_assign($companymanagerrole->id, $adduser->id, $this->context->id);
                                    // Deal with company courses.
                                    if ($companycourses = $DB->get_records('company_course',
                                                                            array('companyid' => $this->selectedcompany))) {
                                        foreach ($companycourses as $companycourse) {
                                            if ($DB->record_exists('course', array('id' => $companycourse->courseid))) {
                                                if ($DB->record_exists('company_created_courses',
                                                                        array('companyid' => $companycourse->companyid,
                                                                              'courseid' => $companycourse->courseid))) {
                                                    company_user::enrol($adduser,
                                                                        array($companycourse->courseid),
                                                                        $companycourse->companyid,
                                                                        $companycourseeditorrole->id);
                                                } else {
                                                    company_user::enrol($adduser,
                                                                        array($companycourse->courseid),
                                                                        $companycourse->companyid,
                                                                        $companycoursenoneditorrole->id);
                                                }
                                            }
                                        }
                                    }
                                } else if ($roletype == 2 && $userrecord->managertype == 0) {
                                    // Give them the department manager role.
                                    role_assign($departmentmanagerrole->id, $adduser->id, $this->context->id);

                                    // Deal with company courses.
                                    if ($companycourses = $DB->get_records('company_course',
                                                                            array('companyid' => $this->selectedcompany))) {
                                        foreach ($companycourses as $companycourse) {
                                            if ($DB->record_exists('course', array('id' => $companycourse->courseid))) {
                                                company_user::enrol($adduser, array($companycourse->courseid),
                                                                    $companycourse->companyid,
                                                                    $companycoursenoneditorrole->id);
                                            }
                                        }
                                    }
                                } else if ($roletype == 1 && $userrecord->managertype = 2) {
                                    // Give them the department manager role.
                                    role_unassign($departmentmanagerrole->id, $adduser->id, $this->context->id);
                                    role_assign($companymanagerrole->id, $adduser->id, $this->context->id);

                                    // Deal with course permissions.
                                    if ($companycourses = $DB->get_records('company_course',
                                                                            array('companyid' => $this->selectedcompany))) {
                                        foreach ($companycourses as $companycourse) {
                                            if ($DB->record_exists('course', array('id' => $companycourse->courseid))) {
                                                // If its a company created course then assign the editor role to the user.
                                                if ($DB->record_exists('company_created_courses',
                                                                        array ('companyid' => $this->selectedcompany,
                                                                               'courseid' => $companycourse->courseid))) {
                                                    company_user::unenrol($adduser,
                                                                          array($companycourse->courseid),
                                                                                $companycourse->companyid);
                                                    company_user::enrol($adduser, array($companycourse->courseid),
                                                                        $companycourse->companyid,
                                                                        $companycourseeditorrole->id);

                                                } else {
                                                     company_user::enrol($adduser, array($companycourse->courseid),
                                                                         $companycourse->companyid,
                                                                         $companycoursenoneditorrole->id);
                                                }
                                            }
                                        }
                                    }
                                } else if ($roletype == 2 && $userrecord->managertype = 1) {
                                    // Give them the department manager role.
                                    role_unassign($companymanagerrole->id, $adduser->id, $this->context->id);
                                    role_assign($departmentmanagerrole->id, $adduser->id, $this->context->id);
                                    // Deal with company course roles.
                                    if ($companycourses = $DB->get_records('company_course',
                                         array('companyid' => $this->selectedcompany))) {
                                        foreach ($companycourses as $companycourse) {
                                            if ($DB->record_exists('course', array('id' => $companycourse->courseid))) {
                                                company_user::unenrol($adduser, array($companycourse->courseid),
                                                                      $companycourse->companyid);
                                                company_user::enrol($adduser, array($companycourse->courseid),
                                                                    $companycourse->companyid,
                                                                    $companycoursenoneditorrole->id);
                                            }
                                        }
                                    }
                                }
                                $userrecord->managertype = $roletype;
                                $DB->update_record('company_users', $userrecord);
                            } else if ($roletype == 1 &&
                                       $DB->get_records_sql('SELECT id FROM {company_users}
                                                            WHERE
                                                            userid = :userid
                                                            AND managertype = :roletype
                                                            AND companyid != :companyid', array('userid' => $adduser->id,
                                                                              'roletype' => 1,
                                                                              'companyid' => $this->selectedcompany))) {
                                // We have a company manager from another company.
                                // We need to add another record.
                                $company = new company($this->selectedcompany);
                                $company->assign_user_to_company($adduser->id);
                                $DB->set_field('company_users', 'managertype', 1, array('userid' => $adduser->id, 'companyid' => $this->selectedcompany));
                                // Deal with company courses.
                                if ($companycourses = $DB->get_records('company_course',
                                                                        array('companyid' => $this->selectedcompany))) {
                                    foreach ($companycourses as $companycourse) {
                                        if ($DB->record_exists('course', array('id' => $companycourse->courseid))) {
                                            if ($DB->record_exists('company_created_courses',
                                                                    array('companyid' => $companycourse->companyid,
                                                                          'courseid' => $companycourse->courseid))) {
                                                company_user::enrol($adduser,
                                                                    array($companycourse->courseid),
                                                                    $companycourse->companyid,
                                                                    $companycourseeditorrole->id);
                                            } else {
                                                company_user::enrol($adduser,
                                                                    array($companycourse->courseid),
                                                                    $companycourse->companyid,
                                                                    $companycoursenoneditorrole->id);
                                            }
                                        }
                                    }
                                }
                            } else {                                 
                                // Assign the user to department as staff.
                                company::assign_user_to_department($departmentid, $adduser->id);
                            }
                        } else {
                            // Assign the user to department as staff.
                            company::assign_user_to_department($departmentid, $adduser->id);
                        }
                    }
                }

                $this->potentialusers->invalidate_selected_users();
                $this->currentusers->invalidate_selected_users();
            }
        }

        // Process incoming unassignments.
        if (optional_param('remove', false, PARAM_BOOL) && confirm_sesskey()) {
            $userstounassign = $this->currentusers->get_selected_users();
            if (!empty($userstounassign)) {

                // Check if we are mearly removing the manager role.
                if ($roletype != 0) {
                    foreach ($userstounassign as $removeuser) {

                        // Check the userid is valid.
                        if (!company::check_valid_user($this->selectedcompany, $removeuser->id, $this->departmentid)) {
                            print_error('invaliduserdepartment', 'block_iomad_company_management');
                        }

                        $userrecord = $DB->get_record('company_users', array('companyid' => $this->selectedcompany,
                                                                    'userid' => $removeuser->id));
                        // Is this a manager from another company?
                        if ($DB->get_records_sql("SELECT id FROM {company_users}
                                                  WHERE userid = :userid
                                                  AND companyid != :companyid
                                                  AND managertype = 1",
                                                  array('userid' => $removeuser->id,
                                                        'companyid' => $this->selectedcompany))) {
                            // Remove the user from this company.
                            $DB->delete_records('company_users', (array) $userrecord);
                        } else {
                            // Remove the manager status from the user.
                            $userrecord->managertype = 0;
                            $DB->update_record('company_users', $userrecord);
                            role_unassign($companymanagerrole->id, $removeuser->id, $this->context->id);
                            role_unassign($departmentmanagerrole->id, $removeuser->id, $this->context->id);
                        }
                        // Remove their capabilities from the company courses.
                        if ($companycourses = $DB->get_records('company_course', array('companyid' => $this->selectedcompany))) {
                            foreach ($companycourses as $companycourse) {
                                if ($DB->record_exists('course', array('id' => $companycourse->courseid))) {
                                    company_user::unenrol($removeuser, array($companycourse->courseid), $companycourse->companyid);
                                }
                            }
                        }
                    }
                } else {
                    foreach ($userstounassign as $removeuser) {
                        // Check the userid is valid.
                        if (!company::check_valid_user($this->selectedcompany, $removeuser->id, $this->departmentid)) {
                            print_error('invaliduserdepartment', 'block_iomad_company_management');
                        }

                        // Assign the user to parent department as staff.
                        company::assign_user_to_department($this->companydepartment, $removeuser->id);
                    }
                }

                $this->potentialusers->invalidate_selected_users();
                $this->currentusers->invalidate_selected_users();
            }
        }
    }
}


$returnurl = optional_param('returnurl', '', PARAM_LOCALURL);
$departmentid = optional_param('deptid', 0, PARAM_INTEGER);
$roleid = optional_param('managertype', 0, PARAM_INTEGER);
$showothermanagers = optional_param('showothermanagers', 0, PARAM_BOOL);

// If we are not handling company manager role types we are not picking other company managers.
if ($roleid != 1) {
    $showothermanagers = false;
}

$context = context_system::instance();
require_login();
iomad::require_capability('block/iomad_company_admin:company_manager', $context);

// Correct the navbar.
// Set the name for the page.
$linktext = get_string('assignmanagers', 'block_iomad_company_admin');
// Set the url.
$linkurl = new moodle_url('/blocks/iomad_company_admin/company_managers_form.php');
// Build the nav bar.
company_admin_fix_breadcrumb($PAGE, $linktext, $linkurl);

$blockpage = new blockpage($PAGE, $OUTPUT, 'iomad_company_admin', 'block', 'company_manager_form_title');
$blockpage->setup();

// Set the companyid
$companyid = iomad::get_my_companyid($context);

$PAGE->set_context($context);

$urlparams = array('departmentid' => $departmentid,
                   'managertype' => $roleid,
                   'showothermanagers' => $showothermanagers);
if ($returnurl) {
    $urlparams['returnurl'] = $returnurl;
}

// Set up the departments stuffs.
$company = new company($companyid);
$parentlevel = company::get_company_parentnode($company->id);
if (iomad::has_capability('block/iomad_company_admin:edit_all_departments', context_system::instance())) {
    $userhierarchylevel = $parentlevel->id;
} else {
    $userlevel = company::get_userlevel($USER);
    $userhierarchylevel = $userlevel->id;
}

$subhierarchieslist = company::get_all_subdepartments($userhierarchylevel);
if (empty($departmentid)) {
    $departmentid = $userhierarchylevel;
}

$departmentselect = new single_select(new moodle_url($linkurl, $urlparams), 'deptid', $subhierarchieslist, $departmentid);
$departmentselect->label = get_string('department', 'block_iomad_company_admin') .
                           $OUTPUT->help_icon('department', 'block_iomad_company_admin') . '&nbsp';

$managertypes = $company->get_managertypes();
$managerselect = new single_select(new moodle_url($linkurl, $urlparams), 'managertype', $managertypes, $roleid);
$managerselect->label = get_string('managertype', 'block_iomad_company_admin') .
                        $OUTPUT->help_icon('managertype', 'block_iomad_company_admin') . '&nbsp';

$othersselect = new single_select(new moodle_url($linkurl, $urlparams), 'showothermanagers',
                array(get_string('no'), get_string('yes')), $showothermanagers);
$othersselect->label = get_string('showothermanagers', 'block_iomad_company_admin') .
                       $OUTPUT->help_icon('showothermanagers', 'block_iomad_company_admin') . '&nbsp';

// Set up the allocation form.
$managersform = new company_managers_form($PAGE->url, $context, $companyid, $departmentid, $roleid, $showothermanagers);

// Change the department for the form.
if ($departmentid != 0) {
    $managersform->set_data(array('deptid' => $departmentid));
}
// Change the user type of the form.
if ($roleid != 0) {
    $managersform->set_data(array('managertype' => $roleid));
}


if ($managersform->is_cancelled()) {
    if ($returnurl) {
        redirect($returnurl);
    } else {
        redirect(new moodle_url('company_list/local/iomad_dashboard/index.php'));
    }
} else {
    $managersform->process($departmentid, $roleid);

    $blockpage->display_header();

    // Check the department is valid.
    if (!empty($departmentid) && !company::check_valid_department($companyid, $departmentid)) {
        print_error('invaliddepartment', 'block_iomad_company_admin');
    }

    echo html_writer::tag('h3', get_string('company_managers_for', 'block_iomad_company_admin', $company->get_name()));
    echo html_writer::start_tag('div', array('class' => 'iomadclear'));
    echo html_writer::start_tag('div', array('class' => 'fitem'));
    echo $OUTPUT->render($departmentselect);
    echo html_writer::end_tag('div');
    echo html_writer::end_tag('div');

    echo html_writer::start_tag('div', array('class' => 'iomadclear'));
    echo html_writer::start_tag('div', array('class' => 'fitem'));
    echo $OUTPUT->render($managerselect);
    echo html_writer::end_tag('div');
    echo html_writer::end_tag('div');

    if (iomad::has_capability('block/iomad_company_admin:company_add', context_system::instance()) &&
        $roleid == 1) {
        echo html_writer::start_tag('div', array('class' => 'iomadclear'));
        echo html_writer::start_tag('div', array('class' => 'fitem'));
        echo $OUTPUT->render($othersselect);
        echo html_writer::end_tag('div');
        echo html_writer::end_tag('div');
    }

    echo $managersform->display();

    echo $OUTPUT->footer();
}
