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

defined('MOODLE_INTERNAL') || die;

use \iomad;
use \company;
use \moodle_url;

class company_license_users_form extends \moodleform {
    protected $context = null;
    protected $selectedcompany = 0;
    protected $potentialusers = null;
    protected $currentusers = null;
    protected $course = null;
    protected $departmentid = 0;
    protected $companydepartment = 0;
    protected $subhierarchieslist = null;
    protected $parentlevel = null;
    protected $license = array();
    protected $selectedcourses = array();
    protected $courseselect = array();
    protected $firstcourseid = 0;

    public function __construct($actionurl, $context, $companyid, $licenseid, $departmentid, $selectedcourses, $error, $output, $chosenid=0) {
        global $USER, $DB;
        $this->selectedcompany = $companyid;
        $this->context = $context;
        $company = new \company($this->selectedcompany);
        $this->parentlevel = \company::get_company_parentnode($company->id);
        $this->companydepartment = $this->parentlevel->id;
        $this->licenseid = $licenseid;
        $this->license = $DB->get_record('companylicense', array('id' => $licenseid));
        $this->selectedcourses = $selectedcourses;
        $this->error = $error;

        // Get the courses to send to if emails are configured.
        if (!empty($this->license)) {
            $courses = \company::get_courses_by_license($this->license->id);
        } else {
            $courses = array();
        }
        $courseselect = array();
        $first = true;
        foreach ($courses as $courseid => $course) {
            $courseselect[$course->id] = $course->fullname;
            if ($first) {
                $this->firstcourseid = $courseid;
                $first = false;
            }
        }
        natsort($courseselect);

        // If we only have one course in the license or this is a program license, select it by default.
        if (count($courseselect) == 1 || !empty($this->license->program)) {
            $this->selectedcourses = array_keys($courseselect);
        }

        // Add the all courses to the list.
        $courseselect = array(0 => get_string('all')) + $courseselect;
        $this->courseselect = $courseselect;

        if (\iomad::has_capability('block/iomad_company_admin:allocate_licenses', \context_system::instance())) {
            $userhierarchylevel = $this->parentlevel->id;
        } else {
            $userlevel = $company->get_userlevel($USER);
            $userhierarchylevel = key($userlevel);
        }

        if ($departmentid == 0) {
            $this->departmentid = $userhierarchylevel;
            $this->subhierarchieslist = \company::get_all_subdepartments($userhierarchylevel);
        } else {
            $this->departmentid = $departmentid;
            $this->subhierarchieslist = \company::get_all_subdepartments($departmentid);
        }

        $this->output = $output;
        $this->chosenid = $chosenid;
        parent::__construct($actionurl);
    }

    public function set_course($courses) {
        $keys = array_keys($courses);
        $this->course = $courses[$keys[0]];
    }

    public function create_user_selectors() {
        if (!empty ($this->licenseid)) {
            //if (count($this->courseselect) > 1) {
                $multiple = true;
            //}
            $options = array('context' => $this->context,
                             'companyid' => $this->selectedcompany,
                             'licenseid' => $this->licenseid,
                             'departmentid' => $this->departmentid,
                             'subdepartments' => $this->subhierarchieslist,
                             'parentdepartmentid' => $this->parentlevel,
                             'program' => $this->license->program,
                             'selectedcourses' => $this->selectedcourses,
                             'courses' => $this->courseselect,
                             'multiselect' => true);
            if (empty($this->potentialusers)) {
                $this->potentialusers = new \potential_license_user_selector('potentialcourseusers', $options);
            }
            if (empty($this->currentusers)) {
                $this->currentusers = new \current_license_user_selector('currentlyenrolledusers', $options);
            }
        } else {
            return;
        }

    }

    public function definition() {
        $this->_form->addElement('hidden', 'companyid', $this->selectedcompany);
        $this->_form->addElement('hidden', 'licenseid', $this->licenseid);
        $this->_form->setType('companyid', PARAM_INT);
        $this->_form->setType('licenseid', PARAM_INT);
    }

    public function definition_after_data() {
        global $USER, $output;

        $mform =& $this->_form;

        // Disable on change notifications.
        $mform->disable_form_change_checker();

        if (!empty($this->course->id)) {
            $this->_form->addElement('hidden', 'courseid', $this->course->id);
        }
        $this->create_user_selectors();

        // Adding the elements in the definition_after_data function rather than in the definition function
        // so that when the currentcourses or potentialcourses get changed in the process function, the
        // changes get displayed, rather than the lists as they are before processing.

        if (!$this->licenseid) {
            die('No license selected.');
        }

        $company = new \company($this->selectedcompany);

        $output->display_tree_selector_form($company, $mform);

        if ($this->license->expirydate > time()) {
            // Add in the courses selector.
            if (empty($this->license->program)) {
                $courseselector = $mform->addElement('autocomplete',
                                                     'courses',
                                                     get_string('courses', 'block_iomad_company_admin'),
                                                     $this->courseselect,
                                                     array('id' => 'courseselector',
                                                           'multiple' => false,
                                                           'onchange' => 'this.form.submit()'));
                $courseselector->setMultiple(true);
                $courseselector->setSelected($this->selectedcourses);
            } else {
                $mform->addElement('hidden', 'courses');
                $mform->setType('courses', PARAM_INT);
            }

            $mform->addElement('header', 'header', get_string('license_users_for',
                                                              'block_iomad_company_admin',
                                                              $this->license->name));
            if (!$this->license->program) {
                $mform->addElement('html', '('.($this->license->allocation - $this->license->used).' / '.
                $this->license->allocation.get_string('licensetotal', 'block_iomad_company_admin').')');
            } else {
                $mform->addElement('html', '('.($this->license->allocation - $this->license->used) / (count($this->courseselect) - 1) .' / '.
                $this->license->allocation / (count($this->courseselect) - 1) . get_string('licensetotal', 'block_iomad_company_admin').')');
            }
        } else {
            $mform->addElement('header', 'header', get_string('license_users_for',
                                                              'block_iomad_company_admin',
                                                              $this->license->name).' *Expired* ');
            $mform->addElement('html', '('.($this->license->used).' / '.
            $this->license->allocation . get_string('licensetotal', 'block_iomad_company_admin').')');
        }

        $mform->addElement('date_time_selector', 'due', get_string('senddate', 'block_iomad_company_admin'));
        $mform->addHelpButton('due', 'senddate', 'block_iomad_company_admin');
        if ($this->license->startdate > time()) {
            $mform->setDefault('due', $this->license->startdate);
        }

        if ($this->error == 1) {
            $mform->addElement('html', "<div class='form-group row has-danger fitem'>
                                        <div class='form-inline felement' data-fieldtype='text'>
                                        <div class='form-control-feedback'>".
                                        get_string('licensetoomanyusers', 'block_iomad_company_admin').
                                        "</div></div>");
        }

        $mform->addElement('html', '<table summary=""
                                     class="companylicenseuserstable addremovetable generaltable generalbox boxaligncenter"
                                     cellspacing="0">
            <tr>
              <td id="existingcell">');

        $mform->addElement('html', $this->currentusers->display(true));

        if ($this->license->expirydate > time()) {
            $mform->addElement('html', '
                  </td>
                  <td id="buttonscell">
                      <p class="arrow_button">
                        <input name="add" id="add" type="submit" value="' . $output->larrow().'&nbsp;'.get_string('licenseallocate', 'block_iomad_company_admin') . '"
                               title="' . get_string('licenseallocate', 'block_iomad_company_admin') .'" class="btn btn-secondary"/><br />
                        <input name="addall" id="addall" type="submit" value="' . $output->larrow().'&nbsp;'.get_string('licenseallocateall', 'block_iomad_company_admin') . '"
                               title="' . get_string('licenseallocateall', 'block_iomad_company_admin') .'" class="btn btn-secondary"/><br />
                        <input name="remove" id="remove" type="submit" value="'. get_string('licenseremove', 'block_iomad_company_admin').'&nbsp;'.$output->rarrow(). '"
                               title="'. get_string('licenseremove', 'block_iomad_company_admin') .'" class="btn btn-secondary"/><br />
                        <input name="removeall" id="removeall" type="submit" value="'. get_string('licenseremoveall', 'block_iomad_company_admin').'&nbsp;'.$output->rarrow(). '"
                               title="'. get_string('licenseremoveall', 'block_iomad_company_admin') .'" class="btn btn-secondary"/><br />
                     </p>
                  </td>
                  <td id="potentialcell">');

            $mform->addElement('html', $this->potentialusers->display(true));
        }

        $mform->addElement('html', '
              </td>
            </tr>
          </table>');
        if ($this->error == 1) {
            $mform->addElement('html', '</div>');
        }
        $mform->addElement('html', get_string('licenseusedwarning', 'block_iomad_company_admin'));
    }

    public function validation($data, $files) {

        $errors = array();

        // if we are removing we don't care about the date.
        if (optional_param('removeall', false, PARAM_BOOL) || optional_param('remove', false, PARAM_BOOL)) {
            $removing = true;
        } else {
            $removing = false;
        }

        // Is the due date valid
        if ($data['due'] > $this->license->expirydate && !$removing) {
            $errors['due'] = get_string('licensedueafterexpirywarning', 'block_iomad_company_admin');
        }
        if ($data['due'] < $this->license->startdate && !$removing) {
            $errors['due'] = get_string('licenseduebeforestartwarning', 'block_iomad_company_admin');
        }

        return $errors;
    }

    public function process() {
        global $DB, $CFG;

        if ($this->is_validated()) {
            $this->create_user_selectors();
            $courses = array();
            if (in_array(0, $this->selectedcourses)) {
                $temp = $this->courseselect;
                unset($temp[0]);
                $courses = array_keys($temp);
            } else {
                $courses = $this->selectedcourses;
            }
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
            if ($add || $addall) {
                $numberoflicenses = $this->license->allocation;
                $count = $this->license->used;
                $licenserecord = (array) $this->license;

                if (!empty($userstoassign) && !empty($courses)) {
                    $required = count($userstoassign) * count($courses);
                    if ($count + $required > $numberoflicenses) {
                        redirect(new moodle_url("/blocks/iomad_company_admin/company_license_users_form.php",
                                                 array('licenseid' => $this->licenseid, 'error' => 1)));

                    }
                    foreach ($userstoassign as $adduser) {

                        // Check the userid is valid.
                        if (!\company::check_valid_user($this->selectedcompany, $adduser->id, $this->departmentid)) {
                            print_error('invaliduserdepartment', 'block_iomad_company_management');
                        }
                        foreach ($courses as $courseid) {
                            $allow = true;
                            if ($allow) {
                                $recordarray = array('licensecourseid' => $courseid,
                                                     'userid' => $adduser->id,
                                                     'timecompleted' => null);

                                // Check if we are not assigning multiple times.
                                if (!$DB->get_record('companylicense_users', $recordarray)) {
                                    $recordarray['licenseid'] = $this->licenseid;
                                    $recordarray['issuedate'] = time();
                                    $recordarray['isusing'] = 0;
                                    $recordarray['id'] = $DB->insert_record('companylicense_users', $recordarray);
                                    $count++;
                                    $due = optional_param_array('due', array(), PARAM_INT);
                                    if (!empty($due)) {
                                        $duedate = strtotime($due['year'] . '-' . $due['month'] . '-' . $due['day'] . ' ' . $due['hour'] . ':' . $due['minute']);
                                    } else {
                                        $duedate = 0;
                                    }

                                    // Create an event.
                                    $eventother = array('licenseid' => $this->license->id,
                                                        'issuedate' => $recordarray['issuedate'],
                                                        'duedate' => $duedate);
                                    $event = \block_iomad_company_admin\event\user_license_assigned::create(array('context' => \context_course::instance($courseid),
                                                                                                                  'objectid' => $recordarray['id'],
                                                                                                                  'courseid' => $courseid,
                                                                                                                  'userid' => $adduser->id,
                                                                                                                  'other' => $eventother));
                                    $event->trigger();
                                }
                            }
                        }
                    }

                    $this->potentialusers->invalidate_selected_users();
                    $this->currentusers->invalidate_selected_users();
                }
            }

            $removeall = false;;
            $remove = false;
            $licensestounassign = array();
            $licenserecords = array();

            if (optional_param('removeall', false, PARAM_BOOL) && confirm_sesskey()) {
                $search = optional_param('currentlyenrolledusers_searchtext', '', PARAM_RAW);
                // Process incoming allocations.
                $currentusers = $this->currentusers->find_users($search, true);

                $licenserecords = array_pop($currentusers);
                $removeall = true;
            }
            if (optional_param('remove', false, PARAM_BOOL) && confirm_sesskey()) {
                $licenserecords = $this->currentusers->get_selected_users();
                $remove = true;
            }
            foreach($licenserecords as $licenserecord) {
                $licensestounassign[$licenserecord->licenseid] = $licenserecord->licenseid;
            }

            // Process incoming unallocations.
            if ($remove || $removeall) {
                $licenserecord = (array) $this->license;

                if (!empty($licenserecord['program'])) {
                    $userrecords = array();
                    foreach ($licensestounassign as $licenserecid) {

                        // Get the user from the initial license ID passed.
                        $userlic = $DB->get_record('companylicense_users',array('id' => $licenserecid), '*', MUST_EXIST);
                        $userrecords = $userrecords + array_keys($DB->get_records_sql("SELECT id FROM {companylicense_users}
                                                                                       WHERE licenseid = :licenseid
                                                                                       AND userid IN (
                                                                                           SELECT userid FROM {companylicense_users}
                                                                                           WHERE id IN
                                                                                       (" . implode(',', $licensestounassign) . "))",
                                                                                       array('licenseid' => $this->license->id)));
                    }
                    $licensestounassign = $userrecords;
                    if ($licenserecord['type'] == 1 || $licenserecord['type'] == 3) {
                        $canremove = true;
                    } else {
                        $canremove = true;
                        foreach ($licensestounassign as $unassignid) {
                            if ($DB->get_record('companylicense_users' ,array('id' => $unassignid, 'isusing' => 1))) {
                                $canremove = false;
                            }
                        }
                    }
                    if (!$canremove) {
                        $licensestounassign = array();
                    }
                }

                if (!empty($licensestounassign)) {
                    foreach ($licensestounassign as $unassignid) {
                        $licensedata = $DB->get_record('companylicense_users' ,array('id' => $unassignid), '*', MUST_EXIST);

                        // Check the userid is valid.
                        if (!\company::check_valid_user($this->selectedcompany, $licensedata->userid, $this->departmentid)) {
                            print_error('invaliduserdepartment', 'block_iomad_company_management');
                        }

                        if (!$licensedata->isusing || $this->license->type == 1 || $this->license->type == 3) {
                            $DB->delete_records('companylicense_users', array('id' => $unassignid));

                            // Remove the report data if license hasn't been used.
                            if (!$licensedata->isusing) {
                                $DB->delete_records('local_iomad_track', array('userid' => $licensedata->userid,
                                                                               'licenseid' => $licensedata->id,
                                                                               'courseid' => $licensedata->licensecourseid,
                                                                               'timeenrolled' => null));
                            }

                            // Create an event.
                            $eventother = array('licenseid' => $this->license->id,
                                                'duedate' => 0);
                            $event = \block_iomad_company_admin\event\user_license_unassigned::create(array('context' => \context_course::instance($licensedata->licensecourseid),
                                                                                                            'objectid' => $this->license->id,
                                                                                                            'courseid' => $licensedata->licensecourseid,
                                                                                                            'userid' => $licensedata->userid,
                                                                                                            'other' => $eventother));
                            $event->trigger();
                        }
                    }

                    $this->potentialusers->invalidate_selected_users();
                    $this->currentusers->invalidate_selected_users();
                }
            }
        }
    }
}
