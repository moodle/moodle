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
require_once($CFG->dirroot . '/local/email/lib.php');

class company_license_users_form extends moodleform {
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
        $company = new company($this->selectedcompany);
        $this->parentlevel = company::get_company_parentnode($company->id);
        $this->companydepartment = $this->parentlevel->id;
        $this->licenseid = $licenseid;
        $this->license = $DB->get_record('companylicense', array('id' => $licenseid));
        $this->selectedcourses = $selectedcourses;
        $this->error = $error;

        // Get the courses to send to if emails are configured.
        if (!empty($this->license)) {
            $courses = company::get_courses_by_license($this->license->id);
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
        $this->courseselect = $courseselect;

        if (iomad::has_capability('block/iomad_company_admin:allocate_licenses', context_system::instance())) {
            $userhierarchylevel = $this->parentlevel->id;
        } else {
            $userlevel = $company->get_userlevel($USER);
            $userhierarchylevel = $userlevel->id;
        }

        if ($departmentid == 0) {
            $this->departmentid = $userhierarchylevel;
            $this->subhierarchieslist = company::get_all_subdepartments($userhierarchylevel);
        } else {
            $this->departmentid = $departmentid;
            $this->subhierarchieslist = company::get_all_subdepartments($departmentid);
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
            if (count($this->courseselect > 1)) {
                $multiple = true;
            } else {
                $multiple = false;
            }
            $options = array('context' => $this->context,
                             'companyid' => $this->selectedcompany,
                             'licenseid' => $this->licenseid,
                             'departmentid' => $this->departmentid,
                             'subdepartments' => $this->subhierarchieslist,
                             'parentdepartmentid' => $this->parentlevel,
                             'program' => $this->license->program,
                             'selectedcourses' => $this->selectedcourses,
                             'multiple' => $multiple);
            if (empty($this->potentialusers)) {
                $this->potentialusers = new potential_license_user_selector('potentialcourseusers', $options);
            }
            if (empty($this->currentusers)) {
                $this->currentusers = new current_license_user_selector('currentlyenrolledusers', $options);
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
        global $USER;

        $mform =& $this->_form;

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

        $company = new company($this->selectedcompany);

        $subdepartmenthtml = "";
        $userdepartment = $company->get_userlevel($USER);
        $departmentslist = company::get_all_subdepartments($userdepartment->id);
        $departmenttree = company::get_all_subdepartments_raw($userdepartment->id);
        $treehtml = $this->output->department_tree($departmenttree, optional_param('deptid', 0, PARAM_INT));

        $mform->addElement('html', '<p>' . get_string('updatedepartmentusersselection', 'block_iomad_company_admin') . '</p>');
        $mform->addElement('html', $treehtml);
        //$mform->addElement('html', $subdepartmenthtml);

        // This is getting hidden anyway, so no need for label
        $mform->addElement('html', '<div class="display:none;">');
        $mform->addElement('select', 'deptid', ' ',
                            $departmentslist, array('class' => 'iomad_department_select', 'onchange' => 'this.form.submit()'));
        $mform->disabledIf('deptid', 'action', 'eq', 1);
        $mform->addElement('html', '</div>');

        if ($this->license->expirydate > time()) {
            // Add in the courses selector.
            if (empty($this->license->program)) {
                $courseselector = $mform->addElement('select',
                                                     'courses',
                                                     get_string('courses', 'block_iomad_company_admin'),
                                                     $this->courseselect,
                                                     array('id' => 'courseselector'));
                $courseselector->setMultiple(true);
                $courseselector->setSelected($this->firstcourseid);
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
                $mform->addElement('html', '('.($this->license->allocation - $this->license->used) / count($this->courseselect) .' / '.
                $this->license->allocation / count($this->courseselect) . get_string('licensetotal', 'block_iomad_company_admin').')');
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
                                     class="companycourseuserstable addremovetable generaltable generalbox boxaligncenter"
                                     cellspacing="0">
            <tr>
              <td id="existingcell">');

        $mform->addElement('html', $this->currentusers->display(true));

        if ($this->license->expirydate > time()) {
            $mform->addElement('html', '
                  </td>
                  <td id="buttonscell">
                      <div id="addcontrols">
                          <input name="add" id="add" type="submit" value="&nbsp;' .
                       $this->output->larrow().'&nbsp;'. get_string('licenseallocate', 'block_iomad_company_admin') .
                          '" title="Enrol" />

                          <input name="addall" id="addall" type="submit" value="&nbsp;' .
                          $this->output->larrow().'&nbsp;'. get_string('licenseallocateall', 'block_iomad_company_admin') .
                          '" title="Enrolall" />

                      </div>

                      <div id="removecontrols"><input name="remove" id="remove" type="submit" value="' .
                       $this->output->rarrow().'&nbsp;'. get_string('licenseremove', 'block_iomad_company_admin') .
                          '" title="Unenrol" />
                          <input name="removeall" id="removeall" type="submit" value="' .
                          $this->output->rarrow().'&nbsp;'. get_string('licenseremoveall', 'block_iomad_company_admin') .
                          '" title="Unenrolall" />
                      </div>
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
            if (empty($this->selectedcourses)) {
                $this->selectedcourses = array_keys($this->courseselect);
            }
            if (!is_array($this->selectedcourses)) {
                $courses[] = $this->selectedcourses;
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
                        if (!company::check_valid_user($this->selectedcompany, $adduser->id, $this->departmentid)) {
                            print_error('invaliduserdepartment', 'block_iomad_company_management');
                        }
                        foreach ($courses as $courseid) {
                            $allow = true;
                            if ($allow) {
                                $recordarray = array('licensecourseid' => $courseid,
                                                     'userid' => $adduser->id,
                                                     'licenseid' => $this->licenseid,
                                                     'isusing' => 0);
    
                                // Check if we are not assigning multiple times.
                                if (!$DB->get_record('companylicense_users', $recordarray)) {
                                    $recordarray['issuedate'] = time();
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
                                                        'duedate' => $duedate);
                                    $event = \block_iomad_company_admin\event\user_license_assigned::create(array('context' => context_course::instance($courseid),
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
                $potentialusers = $this->currentusers->find_users($search, true);
                $licenserecords = array_pop($potentialusers);
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
                        if (!company::check_valid_user($this->selectedcompany, $licensedata->userid, $this->departmentid)) {
                            print_error('invaliduserdepartment', 'block_iomad_company_management');
                        }
    
                        if (!$licensedata->isusing || $this->license->type == 1 || $this->license->type == 3) {
                            $DB->delete_records('companylicense_users', array('id' => $unassignid));
    
                            // Create an event.
                            $eventother = array('licenseid' => $this->license->id,
                                                'duedate' => 0);
                            $event = \block_iomad_company_admin\event\user_license_unassigned::create(array('context' => context_course::instance($licensedata->licensecourseid),
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


$returnurl = optional_param('returnurl', '', PARAM_LOCALURL);
$companyid = optional_param('companyid', 0, PARAM_INTEGER);
$courseid = optional_param('courseid', 0, PARAM_INTEGER);
$departmentid = optional_param('deptid', 0, PARAM_INTEGER);
$licenseid = optional_param('licenseid', 0, PARAM_INTEGER);
$error = optional_param('error', 0, PARAM_INTEGER);
$selectedcourses = optional_param('courses', array(), PARAM_INT);
$chosenid = optional_param('chosenid', 0, PARAM_INT);

// if this is a single course then optional_param_array doesn't work.
if( empty($selectedcourses) ){
    $selectedcourses = optional_param('courses', array(), PARAM_INT);
}

$context = context_system::instance();
require_login();
iomad::require_capability('block/iomad_company_admin:allocate_licenses', $context);

// Correct the navbar.
// Set the name for the page.
$linktext = get_string('company_license_users_title', 'block_iomad_company_admin');
// Set the url.
$linkurl = new moodle_url('/blocks/iomad_company_admin/company_license_users_form.php');

// Print the page header.
$PAGE->set_context($context);
$PAGE->set_url($linkurl);
$PAGE->set_pagelayout('admin');
$PAGE->set_title($linktext);

// Set the page heading.
$PAGE->set_heading(get_string('name', 'local_iomad_dashboard') . " - $linktext");

// get output renderer
$output = $PAGE->get_renderer('block_iomad_company_admin');

// Javascript for fancy select.
// Parameter is name of proper select form element. 
$PAGE->requires->js_call_amd('block_iomad_company_admin/department_select', 'init', array('deptid', 'mform1', $departmentid));

// Build the nav bar.
company_admin_fix_breadcrumb($PAGE, $linktext, $linkurl);

// Set the companyid
$companyid = iomad::get_my_companyid($context);
$company = new company($companyid);

//  Check the license is valid for this company.
if (!empty($licenseid) && !company::check_valid_company_license($companyid, $licenseid)) {
    print_error('invalidcompanylicense', 'block_iomad_company_admin');
}

$urlparams = array('companyid' => $companyid);
if ($returnurl) {
    $urlparams['returnurl'] = $returnurl;
}
if ($courseid) {
    $urlparams['courseid'] = $courseid;
}

// Get the top level department.
$parentlevel = company::get_company_parentnode($companyid);

$availablewarning = '';
$licenselist = array();
if (iomad::has_capability('block/iomad_company_admin:unallocate_licenses', context_system::instance())) {
    $userhierarchylevel = $parentlevel->id;
    // Get all the licenses.
    $licenses = $DB->get_records('companylicense', array('companyid' => $companyid), 'expirydate DESC', 'id,name,startdate,expirydate');
    foreach ($licenses as $license) {
        if ($license->expirydate < time()) {
            $licenselist[$license->id] = $license->name . " (" . get_string('licenseexpired', 'block_iomad_company_admin', date($CFG->iomad_date_format, $license->expirydate)) . ")";
        } else if ($license->startdate > time()) {
            $licenselist[$license->id] = $license->name . " (" . get_string('licensevalidfrom', 'block_iomad_company_admin', date($CFG->iomad_date_format, $license->startdate)) . ")";
            if ($licenseid == $license->id) {
                $availablewarning = get_string('licensevalidfromwarning', 'block_iomad_company_admin', date($CFG->iomad_date_format, $license->startdate));
            }
        } else {
            $licenselist[$license->id] = $license->name;
        }
    }

} else {
    $userlevel = $company->get_userlevel($USER);
    $userhierarchylevel = $userlevel->id;
    if (iomad::has_capability('block/iomad_company_admin:edit_licenses', context_system::instance())) {
        $alllicenses = true;
    } else {
        $alllicenses = false;;
    }
    $licenses = $DB->get_records('companylicense', array('companyid' => $companyid), 'expirydate DESC', 'id,name,startdate,expirydate');

    if (!empty($licenses)) {
        foreach ($licenses as $license) {
            if ($alllicenses || $license->expirydate > time()) {
                if ($license->startdate > time()) {
                    $licenselist[$license->id] = $license->name . " (" . get_string('licensevalidfrom', 'block_iomad_company_admin', date($CFG->iomad_date_format, $license->startdate)) . ")";
                    if ($licenseid == $license->id) {
                        $availablewarning = get_string('licensevalidfromwarning', 'block_iomad_company_admin', date($CFG->iomad_date_format, $license->startdate));
                    }
                } else {
                    $licenselist[$license->id] = $license->name;
                }
            }
        }
    }
}

// If we haven't been passed a department level choose the users.
if (empty($departmentid)) {
    $departmentid = $userhierarchylevel;
}

$usersform = new company_license_users_form($PAGE->url, $context, $companyid, $licenseid, $departmentid, $selectedcourses, $error, $output);

echo $output->header();

// Check the department is valid.
if (!empty($departmentid) && !company::check_valid_department($companyid, $departmentid)) {
    print_error('invaliddepartment', 'block_iomad_company_admin');
}

//  Check the license is valid for this company.
if (!empty($licenseid) && !company::check_valid_company_license($companyid, $licenseid)) {
    print_error('invalidcompanylicense', 'block_iomad_company_admin');
}

// Display the license selector.
$select = new single_select($linkurl, 'licenseid', $licenselist, $licenseid);
$select->label = get_string('licenseselect', 'block_iomad_company_admin');
$select->formid = 'chooselicense';
echo html_writer::tag('div', $output->render($select), array('id' => 'iomad_license_selector'));
$fwselectoutput = html_writer::tag('div', $output->render($select), array('id' => 'iomad_license_selector'));

// Do we have any licenses?
if (empty($licenselist)) {
    echo get_string('nolicenses', 'block_iomad_company_admin');
    echo $output->footer();
    die;
}

if ($usersform->is_cancelled() || optional_param('cancel', false, PARAM_BOOL)) {
    if ($returnurl) {
        redirect($returnurl);
    } else {
        redirect(new moodle_url('/local/iomad_dashboard/index.php'));
    }
} else {
    if ($licenseid > 0) {
        //  Work out the courses that the license applies to, if any.
        $courses = company::get_courses_by_license($licenseid);
        $outputstring = "";
        if (!empty($courses)) {
            $outputstring = "<p>" .get_string('licenseassignedto', 'block_iomad_company_admin');
            $count = 1;
            foreach ($courses as $course) {
                if ($count > 1) {
                    $outputstring .= ", ".$course->fullname;
                } else {
                    $outputstring .= $course->fullname;
                }
                $count++;
            }
            $count ++;
        }
        echo $outputstring."</p>";
        $usersform->process();

        if (!empty($availablewarning)) {
            echo html_writer::start_tag('div', array('class' => "alert alert-success"));
            echo $availablewarning;
            echo "</div>";
        }

        // Reload the form.
        $usersform = new company_license_users_form($PAGE->url, $context, $companyid, $licenseid, $departmentid, $selectedcourses, $error, $output);
        $usersform->get_data();
        echo $usersform->display();
    }
}

echo $output->footer();
