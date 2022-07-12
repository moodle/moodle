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
 * @package   block_iomad_company_admin
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_iomad_company_admin\forms;

use \moodleform;
use \company;
use \company_user;
use \iomad;
use \potential_company_group_user_selector;
use \current_company_group_user_selector;
use \context_coursecat;
use \context_system;
use \stdclass;

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
            $userhierarchylevel = key($userlevel);
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
        global $DB, $output;

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
        $mform->addElement('static', 'departmenttitle', get_string('department', 'block_iomad_company_admin'));
        $output->display_tree_selector_form($this->company, $mform, $this->departmentid);
        $stringobj = new stdclass();
        $stringobj->group = $group->description;
        $stringobj->course = $course->fullname;
        $mform->addElement('header', 'header',
                            get_string('group_users_for', 'block_iomad_company_admin',
                            $stringobj));

        if ($this->isdefault) {
            $mform->addElement('html', '<p><strong>' . get_string('isdefaultgroupusers', 'block_iomad_company_admin') . '</strong></p>');
        }

        $mform->addElement('html', '<table summary="" class="companygroupuserstable'.
                                   ' addremovetable generaltable generalbox'.
                                   ' boxaligncenter" cellspacing="0">
            <tr>
              <td id="existingcell">');

        $mform->addElement('html', $this->currentusers->display(true));

        $mform->addElement('html', '
              </td>
              <td id="buttonscell">
                      <input name="add" id="add" type="submit" value="' .
                       $output->larrow().'&nbsp;'.get_string('add') .
                       '" title="'.get_string('add') .'" /><br />');

        if (!$this->isdefault) {

            $mform->addElement('html', '
                      <input name="remove" id="remove" type="submit" value="' .
                       get_string('remove') . '&nbsp;' . $output->rarrow() .
                       '" title="'.get_string('remove') .'" /></br>');
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
