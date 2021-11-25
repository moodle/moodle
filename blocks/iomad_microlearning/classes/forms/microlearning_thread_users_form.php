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
 * @package   block_iomad_microlearning
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_iomad_microlearning\forms;

defined('MOODLE_INTERNAL') || die;

use \company_moodleform;
use \company;
use \microlearning;
use \iomad;

class microlearning_thread_users_form extends \company_moodleform {
    protected $context = null;
    protected $selectedcompany = 0;
    protected $selectedthread = 0;
    protected $potentialusers = null;
    protected $currentusers = null;
    protected $thread = null;
    protected $departmentid = 0;
    protected $companydepartment = 0;
    protected $subhierarchieslist = null;
    protected $parentlevel = null;
    protected $groupid = 0;
    protected $groups = null;
    protected $company = null;
    protected $scheduletypes = null;

    public function __construct($actionurl, $context, $companyid, $departmentid, $threadid, $groupid) {
        global $USER, $DB;
        $this->selectedcompany = $companyid;
        $this->selectedthread = $threadid;
        $this->context = $context;
        $company = new \company($this->selectedcompany);
        $this->company = $company;
        $this->parentlevel = \company::get_company_parentnode($company->id);
        $this->companydepartment = $this->parentlevel->id;
        $context = \context_system::instance();

        if (\iomad::has_capability('block/iomad_company_admin:edit_all_departments', $context)) {
            $userhierarchylevel = $this->parentlevel->id;
        } else {
            $userlevel = $company->get_userlevel($USER);
            $userhierarchylevel = key($userlevel);
        }

        $this->subhierarchieslist = \company::get_all_subdepartments($userhierarchylevel);
        if ($departmentid == 0 ) {
            $this->departmentid = $userhierarchylevel;
        } else {
            $this->departmentid = $departmentid;
        }
        $this->thread = $DB->get_record('microlearning_thread', array('id' => $threadid));
        $this->groups = $DB->get_records_menu('microlearning_thread_group', ['threadid' => $threadid], 'name', 'id,name');
        $this->groups = [0 => get_string('none'), '-1' => get_string('all')] + $this->groups;
        $this->scheduletypes = [get_string('standard', 'block_iomad_microlearning'),
                                get_string('starttoday', 'block_iomad_microlearning'),
                                get_string('startnextscheduled', 'block_iomad_microlearning')];
        $this->groupid = $groupid;

        parent::__construct($actionurl);
    }

    public function create_user_selectors() {
        if (!empty ($this->thread)) {
            $options = array('context' => $this->context,
                             'companyid' => $this->selectedcompany,
                             'threadid' => $this->thread->id,
                             'groupid' => $this->groupid,
                             'departmentid' => $this->departmentid,
                             'subdepartments' => $this->subhierarchieslist,
                             'parentdepartmentid' => $this->parentlevel,
                             'class' => 'potential_company_thread_user_selector');
            if (empty($this->potentialusers)) {
                $this->potentialusers = new \potential_company_thread_user_selector('potentialthreadusers', $options);
            }
            $options['class'] = 'current_company_thread_user_selector';
            if (empty($this->currentusers)) {
                $this->currentusers = new \current_company_thread_user_selector('currentlyenrolledusers', $options);
            }
        } else {
            return;
        }

    }

    public function definition() {
        $this->_form->addElement('hidden', 'companyid', $this->selectedcompany);
        $this->_form->addElement('hidden', 'deptid', $this->departmentid);
        $this->_form->addElement('hidden', 'selectedthread', $this->selectedthread);
        $this->_form->setType('companyid', PARAM_INT);
        $this->_form->setType('deptid', PARAM_INT);
        $this->_form->setType('selectedthread', PARAM_INT);
    }

    public function definition_after_data() {
        global $DB, $output;

        $mform =& $this->_form;

        if (!empty($this->thread)) {
            $this->_form->addElement('hidden', 'threadid', $this->thread->id);
        }

        // Add the group selector.
        $mform->addElement('select', 'groupid', get_string('group', 'block_iomad_microlearning'), $this->groups, ['onchange' => 'this.form.submit()']);
        $mform->addHelpButton('groupid', 'group', 'block_iomad_microlearning');
        $mform->setDefault('groupid', $this->groupid);

        // Add the group selector.
        $mform->addElement('select', 'scheduletype', get_string('scheduletype', 'block_iomad_microlearning'), $this->scheduletypes);
        $mform->addHelpButton('scheduletype','scheduletype', 'block_iomad_microlearning');

        // Add the user selectors.
        $this->create_user_selectors();

        // Adding the elements in the definition_after_data function rather than in the
        // definition function so that when the currentthreads or potentialthreads get
        // changed in the process function, the changes get displayed, rather than the
        // lists as they are before processing.

        if (!$this->thread->id ) {
            die('No thread selected.');
        }

        $thread = $DB->get_record('microlearning_thread', array('id' => $this->thread->id));
        $company = new \company($this->selectedcompany);
        $mform->addElement('header', 'header',
                            get_string('company_users_for', 'block_iomad_microlearning',
                            format_string($thread->name, true, 1) ));

        $mform->addElement('html', '<table summary="" class="companythreaduserstable'.
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
            $search = optional_param('potentialthreadusers_searchtext', '', PARAM_RAW);
            // Process incoming allocations.
            $potentialusers = $this->potentialusers->find_users($search, true);
            $userstoassign = array_pop($potentialusers);
            $addall = true;
        }
        if (optional_param('add', false, PARAM_BOOL) && confirm_sesskey()) {
            $userstoassign = $this->potentialusers->get_selected_users();
            $add = true;
        }

        if ($add || $addall) {
            // Process incoming enrolments.
            if (!empty($userstoassign)) {
                foreach ($userstoassign as $adduser) {
                    $allow = true;

                    // Check the userid is valid.
                    if (!\company::check_valid_user($this->selectedcompany, $adduser->id, $this->departmentid)) {
                        print_error('invaliduserdepartment', 'block_iomad_company_management');
                    }

                    if ($allow) {
                        $due = optional_param_array('due', array(), PARAM_INT);
                        if (!empty($due)) {
                            $duedate = strtotime($due['year'] . '-' . $due['month'] . '-' . $due['day'] . ' ' . $due['hour'] . ':' . $due['minute']);
                        } else {
                            $duedate = 0;
                        }
                        \microlearning::assign_thread_to_user($adduser, $this->thread->id, $this->selectedcompany, $data->groupid, $data->scheduletype);
                    }
                }

                $this->potentialusers->invalidate_selected_users();
                $this->currentusers->invalidate_selected_users();
            }
        }
        $removeall = false;
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
                    // Check the userid is valid.
                    if (!\company::check_valid_user($this->selectedcompany, $removeuser->id, $this->departmentid)) {
                        print_error('invaliduserdepartment', 'block_iomad_company_management');
                    }

                    \microlearning::remove_thread_from_user($removeuser, $this->thread->id, $this->selectedcompany);
                }

                $this->potentialusers->invalidate_selected_users();
                $this->currentusers->invalidate_selected_users();
            }
        }
    }
}
