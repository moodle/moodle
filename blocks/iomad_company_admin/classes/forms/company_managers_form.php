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
use \moodleform;
use \context_system;
use \potential_department_user_selector;
use \current_department_user_selector;

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
            $userlevels = $company->get_userlevel($USER);
            $userhierarchylevel = key($userlevels);
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
        global $USER, $output;
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

            $mform->addElement('html', "(" . get_string('companymanagersforminfo', 'block_iomad_company_admin') . ")");
            $mform->addElement('html', '<table summary=""
                                        class="companymanagertable addremovetable generaltable generalbox boxaligncenter"
                                        cellspacing="0">
                <tr>
                  <td id="existingcell">');

            $mform->addElement('html', $this->currentusers->display(true));

            $mform->addElement('html', '
                  </td>
                  <td id="buttonscell">
                      <p class="arrow_button">
                        <input name="add" id="add" type="submit" value="' . $output->larrow().'&nbsp;'.get_string('add') . '"
                               title="' . print_string('add') .'" class="btn btn-secondary"/><br />
                        <input name="remove" id="remove" type="submit" value="'. get_string('remove').'&nbsp;'.$output->rarrow(). '"
                               title="'. print_string('remove') .'" class="btn btn-secondary"/><br />
                     </p>
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
        global $DB, $USER, $CFG;

        // Process incoming assignments.
        if (optional_param('add', false, PARAM_BOOL) && confirm_sesskey()) {
            $userstoassign = $this->potentialusers->get_selected_users();
            if (!empty($userstoassign)) {
                foreach ($userstoassign as $adduser) {
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

                    if (!$CFG->iomad_autoenrol_managers && $roletype != 3) {
                        // We have to be mindful of educator types here.
                        if ($userrec = $DB->get_record('company_users', array('userid' => $adduser->id, 'companyid' => $this->selectedcompany, 'departmentid' => $departmentid))) {
                            $educator = $userrec->educator;
                        } else {
                            $educator = false;
                        }
                    } else if (!$CFG->iomad_autoenrol_managers && $roletype == 3) {
                        $educator = true;
                    } else if ($CFG->iomad_autoenrol_managers && ($roletype == 2 || $roletype == 1)) {
                        $educator = true;
                    } else {
                        $educator = false;
                    }
                    // Do the actual work.
                    company::upsert_company_user($adduser->id, $this->selectedcompany, $departmentid, $roletype, $educator);
                }

                $this->potentialusers->invalidate_selected_users();
                $this->currentusers->invalidate_selected_users();
            }
        }

        // Process incoming unassignments.
        if (optional_param('remove', false, PARAM_BOOL) && confirm_sesskey()) {
            $userstounassign = $this->currentusers->get_selected_users();
            if (!empty($userstounassign)) {
                foreach ($userstounassign as $removeuser) {

                        // Check the userid is valid.
                    if (!company::check_valid_user($this->selectedcompany, $removeuser->id, $this->departmentid)) {
                        print_error('invaliduserdepartment', 'block_iomad_company_management');
                    }

                    // Get the current company_users record.
                    $userrec = $DB->get_record('company_users', array('userid' => $removeuser->id, 'companyid' => $this->selectedcompany, 'departmentid' => $departmentid));
                    if (!$CFG->iomad_autoenrol_managers && $roletype != 3) {
                        // We have to be mindful of educator types here.
                        $userrec = $DB->get_record('company_users', array('userid' => $removeuser->id, 'companyid' => $this->selectedcompany, 'departmentid' => $departmentid));
                        $educator = $userrec->educator;
                    } else {
                        $educator = false;
                    }

                    // Do the bulk of the work.
                    company::upsert_company_user($removeuser->id, $this->selectedcompany, $departmentid, 0, $educator);

                    // Remove the current record.
                    $DB->delete_records('company_users', array('id' => $userrec->id));

                    // Does the user exist in the company still?
                    if (!$DB->get_records('company_users', array('userid' => $removeuser->id, 'companyid' => $this->selectedcompany))) {
                        // if not add them to the company top level.
                        $companydepartment = company::get_company_parentnode($this->selectedcompany);
                        company::upsert_company_user($removeuser->id, $this->selectedcompany, $companydepartment->id, 0, $educator);
                    }
                }

                $this->potentialusers->invalidate_selected_users();
                $this->currentusers->invalidate_selected_users();
            }
        }
    }
}
