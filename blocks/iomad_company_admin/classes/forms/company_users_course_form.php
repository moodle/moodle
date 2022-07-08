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
use \EmailTemplate;
use \potential_user_course_selector;
use \current_user_course_selector;
use \context_system;
use \stdclass;

class company_users_course_form extends moodleform {
    protected $context = null;
    protected $selectedcompany = 0;
    protected $potentialcourses = null;
    protected $currentcourses = null;
    protected $course = null;
    protected $departmentid = 0;
    protected $companydepartment = 0;
    protected $subhierarchieslist = null;
    protected $parentlevel = null;
    protected $userid = null;
    protected $user = null;

    public function __construct($actionurl, $context, $companyid, $departmentid, $userid) {
        global $USER, $DB;
        $this->selectedcompany = $companyid;
        $this->context = $context;
        $company = new company($this->selectedcompany);
        $this->parentlevel = company::get_company_parentnode($company->id);
        $this->companydepartment = $this->parentlevel->id;

        if (iomad::has_capability('block/iomad_company_admin:edit_all_departments', context_system::instance())) {
            $userhierarchylevel = $this->parentlevel->id;
        } else {
            $userlevel = $company->get_userlevel($USER);
            $userhierarchylevel = key($userlevel);
        }

        $this->subhierarchieslist = company::get_all_subdepartments($userhierarchylevel);
        if ($departmentid == 0) {
            $this->departmentid = $userhierarchylevel;
        } else {
            $this->departmentid = $departmentid;
        }
        $this->userid = $userid;
        $this->user = $DB->get_record('user', array('id' => $this->userid));

        parent::__construct($actionurl);
    }

    public function set_course($courses) {
        $keys = array_keys($courses);
        $this->course = $courses[$keys[0]];
    }

    public function create_course_selectors() {
        if (!empty ($this->userid)) {
            $options = array('context' => $this->context,
                             'companyid' => $this->selectedcompany,
                             'user' => $this->user,
                             'departmentid' => $this->departmentid,
                             'subdepartments' => $this->subhierarchieslist,
                             'parentdepartmentid' => $this->parentlevel,
                             'shared' => true);
            if (! $this->potentialcourses) {
                $this->potentialcourses = new potential_user_course_selector('potentialusercourses', $options);
            }
            if (! $this->currentcourses) {
                $this->currentcourses = new current_user_course_selector('currentcourses', $options);
            }
        } else {
            return;
        }

    }

    public function definition() {
        $this->_form->addElement('hidden', 'companyid', $this->selectedcompany);
        $this->_form->setType('companyid', PARAM_INT);
    }

    public function definition_after_data() {
        global $OUTPUT;

        $mform =& $this->_form;

        if (!empty($this->userid)) {
            $this->_form->addElement('hidden', 'userid', $this->userid);
        }
        $this->create_course_selectors();
        // Adding the elements in the definition_after_data function rather than in the definition function
        // so that when the currentcourses or potentialcourses get changed in the process function, the
        // changes get displayed, rather than the lists as they are before processing.

        if (!$this->userid) {
            die('No user selected.');
        }

        $company = new company($this->selectedcompany);
        $mform->addElement('date_time_selector', 'due', get_string('senddate', 'block_iomad_company_admin'));
        $mform->addHelpButton('due', 'senddate', 'block_iomad_company_admin');

        $mform->addElement('html', '<table summary=""
                                    class="companycourseuserstable addremovetable generaltable generalbox boxaligncenter"
                                    cellspacing="0">
            <tr>
              <td id="existingcell">');

        $mform->addElement('html', $this->currentcourses->display(true));

        $mform->addElement('html', '
              </td>
              <td id="buttonscell">
                  <p class="arrow_button">
                    <input name="add" id="add" type="submit" value="' . $OUTPUT->larrow().'&nbsp;'.get_string('enrol', 'block_iomad_company_admin') . '"
                           title="' . get_string('enrol', 'block_iomad_company_admin') .'" class="btn btn-secondary"/><br />
                    <input name="remove" id="remove" type="submit" value="'. get_string('unenrol', 'block_iomad_company_admin').'&nbsp;'.$OUTPUT->rarrow(). '"
                           title="'. get_string('unenrol', 'block_iomad_company_admin') .'" class="btn btn-secondary"/><br />
                 </p>
              </td>
              <td id="potentialcell">');

        $mform->addElement('html', $this->potentialcourses->display(true));

        $mform->addElement('html', '
              </td>
            </tr>
          </table>');
    }

    public function process() {
        global $DB, $CFG;

        $this->create_course_selectors();

        // Process incoming enrolments.
        if (optional_param('add', false, PARAM_BOOL) && confirm_sesskey()) {
            $coursestoassign = $this->potentialcourses->get_selected_courses();
            if (!empty($coursestoassign)) {

                foreach ($coursestoassign as $addcourse) {
                    $allow = true;

                    if ($allow) {
                        $due = optional_param_array('due', array(), PARAM_INT);
                        if (!empty($due)) {
                            $duedate = strtotime($due['year'] . '-' . $due['month'] . '-' . $due['day'] . ' ' . $due['hour'] . ':' . $due['minute']);
                        } else {
                            $duedate = 0;
                        }
                        company_user::enrol($this->user, array($addcourse->id), $this->selectedcompany);
                        EmailTemplate::send('user_added_to_course', array('course' => $addcourse, 'user' => $this->user, 'due' => $duedate));
                    }
                }

                $this->potentialcourses->invalidate_selected_courses();
                $this->currentcourses->invalidate_selected_courses();
            }
        }

        // Process incoming unenrolments.
        if (optional_param('remove', false, PARAM_BOOL) && confirm_sesskey()) {
            $coursestounassign = $this->currentcourses->get_selected_courses();
            if (!empty($coursestounassign)) {

                foreach ($coursestounassign as $removecourse) {
                    company_user::unenrol($this->user, array($removecourse->id));
                }

                $this->potentialcourses->invalidate_selected_courses();
                $this->currentcourses->invalidate_selected_courses();
            }
        }
    }
}