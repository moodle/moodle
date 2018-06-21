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
    protected $licenseid = 0;
    protected $liccourses = array();
    protected $license = null;

    public function __construct($actionurl, $context, $companyid, $departmentid, $userid, $licenseid) {
        global $USER, $DB;
        $this->selectedcompany = $companyid;
        $this->context = $context;
        $company = new company($this->selectedcompany);
        $this->parentlevel = company::get_company_parentnode($company->id);
        $this->companydepartment = $this->parentlevel->id;
        $this->licenseid = $licenseid;
        $this->liccourses = $DB->get_records_sql("SELECT c.* FROM {course} c
                                                  JOIN {companylicense_courses} clc
                                                  ON (c.id = clc.courseid)
                                                  WHERE clc.licenseid = :licenseid",
                                                  array('licenseid' => $this->licenseid));

        if (iomad::has_capability('block/iomad_company_admin:edit_all_departments', context_system::instance())) {
            $userhierarchylevel = $this->parentlevel->id;
        } else {
            $userlevel = $company->get_userlevel($USER);
            $userhierarchylevel = $userlevel->id;
        }

        $this->subhierarchieslist = company::get_all_subdepartments($userhierarchylevel);
        if ($departmentid == 0) {
            $this->departmentid = $userhierarchylevel;
        } else {
            $this->departmentid = $departmentid;
        }
        $this->userid = $userid;
        $this->user = $DB->get_record('user', array('id' => $this->userid));
        $this->license = $DB->get_record('companylicense', array('id' => $this->licenseid));



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
                             'licenseid' => $this->licenseid,
                             'shared' => true);
            if (! $this->potentialcourses) {
                $this->potentialcourses = new potential_user_license_course_selector('potentialusercourses', $options);
            }
            if (! $this->currentcourses) {
                $this->currentcourses = new current_user_license_course_selector('currentcourses', $options);
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
        global $DB;
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
        $programstr = "";
        if (!empty($this->licenseid)) {

            // is this a program?
            if ($this->license->program) {
                // Get the courses.
                if (!empty($this->liccourses)) {
                    $coursecount = count($this->liccourses);
                    $programstr = get_string('licenseassignedto', 'block_iomad_company_admin');
                    $count = 1;
                    foreach ($this->liccourses as $course) {
                        if ($count > 1) {
                            $programstr .= ", ".$course->fullname;
                        } else {
                            $programstr .= $course->fullname;
                        }
                        $count++;
                    }
                    $this->license->allocation = $this->license->allocation / $coursecount;
                    $this->license->used = $this->license->used / $coursecount; 
                }
                $licenseleft2 = get_string('programleft2', 'block_iomad_company_admin');
            } else {
                $licenseleft2 = get_string('licenseleft2', 'block_iomad_company_admin');
            }
            $licensestring = get_string('licensedetails', 'block_iomad_company_admin', $this->license);
            $licensestring2 = get_string('licensedetails2', 'block_iomad_company_admin', $this->license);
            $licensestring3 = get_string('licensedetails3', 'block_iomad_company_admin', $this->license);
        } else {
            $licensestring = '';
            $licensestring2 = '';
            $licensestring3 = '';
        }

        if (!empty($this->licenseid)) {
            $mform->addElement('html', '<br /><p align="center"><b>' . get_string('licenseleft1', 'block_iomad_company_admin') .
                                        ((intval($licensestring3, 0)) - (intval($licensestring2, 0))) .
                                        "$licenseleft2</br>$programstr</b></p>");
    
            $mform->addElement('html', '<h4>' . get_string('user_courses_for', 'block_iomad_company_admin', fullname($this->user)) . '</h4>');
    
            $mform->addElement('date_time_selector', 'due', get_string('senddate', 'block_iomad_company_admin'));
            $mform->addHelpButton('due', 'senddate', 'block_iomad_company_admin');
            if ($this->license->startdate > time()) {
                $mform->setDefault('due', $this->license->startdate);
            }
    
            // Is this a license program?
            if ($this->license->program) {
                $programselect = $mform->addElement('selectyesno', 'allocate', get_string('programallocate', 'block_iomad_company_admin'));
                $mform->addHelpButton('allocate', 'programallocate', 'block_iomad_company_admin');
                // do we have any of these courses /license combo yet?
                if ($DB->get_records('companylicense_users', array('userid' => $this->userid, 'licenseid' => $this->licenseid))) {
                    $mform->addElement('hidden', 'inuse', true);
                    $mform->setType('inuse', PARAM_INT);
                    $programselect->setSelected(true);
                } else {
                    $mform->addElement('hidden', 'inuse', false);
                    $mform->setType('inuse', PARAM_INT);
                    $programselect->setSelected(false);
                }
                $this->add_action_buttons(false, get_string('updatelicense', 'block_iomad_company_admin'));
            } else {
                $mform->addElement('html', '<table summary=""
                                            class="companycourseuserstable addremovetable generaltable generalbox boxaligncenter"
                                            cellspacing="0"
                                            border="0">
                    <tr>
                      <td id="existingcell" style="text-align:center;">'); //maybe put this in the block CSS?
        
                $mform->addElement('html', $this->currentcourses->display(true));
        
                $mform->addElement('html', '
                      </td>
                      <td id="buttonscell" valign="middle">
                          <div id="addcontrols">
                              <input name="add" id="add" type="submit" value="&nbsp;' .
                              get_string('enrol', 'block_iomad_company_admin') .
                              '" title="Enrol" /><br />
        
                          </div>
        
                          <div id="removecontrols">
                              <input name="remove" id="remove" type="submit" value="' .
                              get_string('unenrol', 'block_iomad_company_admin') .
                              '&nbsp;" title="Unenrol" />
                          </div>
                      </td>
                      <td id="potentialcell" style="text-align:center;">'); //maybe put this in the block CSS?
        
                $mform->addElement('html', $this->potentialcourses->display(true));
        
                $mform->addElement('html', '
                      </td>
                    </tr>
                  </table>');
            }  
        } else {
            $mform->addElement('html', '<br /><p align="center"><b>' . get_string('selectlicenseblurb', 'block_iomad_company_admin') . '</b></p>');
        }
    }

    public function validation($data, $files) {

        $errors = array();

        // if we are removing we don't care about the date.
        if (optional_param('remove', false, PARAM_BOOL)) {
            $removing = true;
        } else {
            $removing = false;
        }

        // Is the due date valid
        if (!empty($data['due']) && $data['due'] > $this->license->expirydate && !$removing) {
            $errors['due'] = get_string('licensedueafterexpirywarning', 'block_iomad_company_admin');
        }
        if (!empty($data['due']) && $data['due'] < $this->license->startdate && !$removing) {
            $errors['due'] = get_string('licenseduebeforestartwarning', 'block_iomad_company_admin');
        }

        return $errors;
    }

    public function process() {
        global $DB, $CFG;

        if ($this->is_validated()) {
            $this->create_course_selectors();
    
            // Process program changes.
            if (optional_param('submitbutton', false, PARAM_BOOL) && confirm_sesskey()) {
                $inuse = optional_param('inuse', false, PARAM_BOOL);
                $allocate = optional_param('allocate', false, PARAM_BOOL);
                if ($inuse == $allocate && $allocate == 1) {
                    return;
                }
                if ($licenserecord = (array) $DB->get_record('companylicense', array('id' => $this->licenseid))) {
                    if ($allocate && ($licenserecord['used'] + count($this->liccourses) > $licenserecord['allocation'])) {
                        echo "<div class='mform'><span class='error'>" . get_string('triedtoallocatetoomanylicenses', 'block_iomad_company_admin') . "</span></div>";
                        return;
                    } else {
                        $due = optional_param_array('due', array(), PARAM_INT);
                        if (!empty($due)) {
                            $duedate = strtotime($due['year'] . '-' . $due['month'] . '-' . $due['day'] . ' ' . $due['hour'] . ':' . $due['minute']);
                        } else {
                            $duedate = 0;
                        }
                        // Is the user using any of the licenses and it's not a subscription?
                        if (!$allocate && $licenserecord['type'] == 0 && $DB->get_records('companylicense_users', array('userid' => $this->userid,
                                                                                                                        'licenseid' => $licenserecord['id'],
                                                                                                                        'isusing' => 1))) {
                            return;
                        }
                        // Deal with the course allocations/removals.
                        foreach ($this->liccourses as $course) {
                            if ($allocate) {
                                $assignrecord = array('userid' => $this->userid,
                                                      'licenseid' => $licenserecord['id'],
                                                      'isusing' => 0,
                                                      'licensecourseid' => $course->id);
    
                                // Check we are not adding multiple times.
                                if (!$DB->get_record('companylicense_users', $assignrecord)) {
                                    $assignrecord['issuedate'] = time();
                                    $assignrecord['id'] = $DB->insert_record('companylicense_users', $assignrecord);
        
                                    // Create an event.
                                    $eventother = array('licenseid' => $licenserecord['id'],
                                                        'duedate' => $duedate);
                                    $event = \block_iomad_company_admin\event\user_license_assigned::create(array('context' => context_course::instance($course->id),
                                                                                                                  'objectid' => $assignrecord['id'],
                                                                                                                  'courseid' => $course->id,
                                                                                                                  'userid' => $this->userid,
                                                                                                                  'other' => $eventother));
                                    $event->trigger();
                                }
                            } else {
                                $userlicenserecord = $DB->get_record('companylicense_users', array('userid' => $this->userid, 
                                                                                                   'licensecourseid' => $course->id,
                                                                                                   'licenseid' => $licenserecord['id']));
                                if (!empty($userlicenserecord->id)) {
                                    $DB->delete_records('companylicense_users', array('id' => $userlicenserecord->id));
            
                                    // Create an event.
                                    $eventother = array('licenseid' => $licenserecord['id'],
                                                        'duedate' => 0);
                                    $event = \block_iomad_company_admin\event\user_license_unassigned::create(array('context' => context_course::instance($course->id),
                                                                                                                    'objectid' => $licenserecord['id'],
                                                                                                                    'courseid' => $course->id,
                                                                                                                    'userid' => $this->userid,
                                                                                                                    'other' => $eventother));
                                    $event->trigger();
                                }
                            }
                        }
                    }
                    return;
                }
            }
    
    
            // Process incoming enrolments.
            if (optional_param('add', false, PARAM_BOOL) && confirm_sesskey()) {
                $coursestoassign = $this->potentialcourses->get_selected_courses();
                if (!empty($coursestoassign)) {
    
                    if ($licenserecord = (array) $DB->get_record('companylicense', array('id' => $this->licenseid))) {
                        if ($licenserecord['used'] + count($coursestoassign) > $licenserecord['allocation']) {
                            echo "<div class='mform'><span class='error'>" . get_string('triedtoallocatetoomanylicenses', 'block_iomad_company_admin') . "</span></div>";
                        } else {
                            $due = optional_param_array('due', array(), PARAM_INT);
                            if (!empty($due)) {
                                $duedate = strtotime($due['year'] . '-' . $due['month'] . '-' . $due['day'] . ' ' . $due['hour'] . ':' . $due['minute']);
                            } else {
                                $duedate = 0;
                            }
                            foreach ($coursestoassign as $addcourse) {
                                $assignrecord = array('userid' => $this->userid,
                                                      'licenseid' => $licenserecord['id'],
                                                      'isusing' => 0,
                                                      'licensecourseid' => $addcourse->id);
    
                                // Check we are not adding multiple times.
                                if (!$DB->get_record('companylicense_users', $assignrecord)) {
                                    $assignrecord['issuedate'] = time();
                                    $DB->insert_record('companylicense_users', $assignrecord);
    
                                    // Create an event.
                                    $eventother = array('licenseid' => $licenserecord['id'],
                                                        'duedate' => $duedate);
                                    $event = \block_iomad_company_admin\event\user_license_assigned::create(array('context' => context_course::instance($addcourse->id),
                                                                                                                  'objectid' => $licenserecord['id'],
                                                                                                                  'courseid' => $addcourse->id,
                                                                                                                  'userid' => $this->userid,
                                                                                                                  'other' => $eventother));
                                    $event->trigger();
                                }
                            }
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
                        if ($userlicenserecord = $DB->get_record('companylicense_users',
                                                                 array('userid' => $this->userid,
                                                                       'licensecourseid' => $removecourse->id))) {
                            $licenserecord = (array) $DB->get_record('companylicense', array('id' => $userlicenserecord->licenseid));
                            if ($userlicenserecord->isusing == 0 || $licenserecord['type'] != 0) {
                                $DB->delete_records('companylicense_users', array('id' => $userlicenserecord->id));
        
                                // Create an event.
                                $eventother = array('licenseid' => $licenserecord['id'],
                                                    'duedate' => 0);
                                $event = \block_iomad_company_admin\event\user_license_unassigned::create(array('context' => context_course::instance($removecourse->id),
                                                                                                                'objectid' => $licenserecord['id'],
                                                                                                                'courseid' => $removecourse->id,
                                                                                                                'userid' => $this->userid,
                                                                                                                'other' => $eventother));
                                $event->trigger();
                            }
                        }
                    }
    
                    $this->potentialcourses->invalidate_selected_courses();
                    $this->currentcourses->invalidate_selected_courses();
                }
            }
        }
    }
}

$returnurl = optional_param('returnurl', '', PARAM_LOCALURL);
$companyid = optional_param('companyid', 0, PARAM_INTEGER);
$courseid = optional_param('courseid', 0, PARAM_INTEGER);
$departmentid = optional_param('departmentid', 0, PARAM_INTEGER);
$userid = required_param('userid', PARAM_INTEGER);
$licenseid = optional_param('licenseid', 0, PARAM_INTEGER);

$context = context_system::instance();
require_login();
iomad::require_capability('block/iomad_company_admin:company_license_users', $context);

// Set the companyid
$companyid = iomad::get_my_companyid($context);
$company = new company($companyid);

$urlparams = array('companyid' => $companyid, 'licenseid' => $licenseid);
if ($returnurl) {
    $urlparams['returnurl'] = $returnurl;
}
if ($userid) {
    $urlparams['userid'] = $userid;
}

// Correct the navbar.
// Set the name for the page.
$linktext = get_string('edit_users_title', 'block_iomad_company_admin');
// Set the url.
$returnurl = new moodle_url('/blocks/iomad_company_admin/editusers.php');
$linkurl = new moodle_url('/blocks/iomad_company_admin/company_users_licenses_form.php');

// Print the page header.
$PAGE->set_context($context);
$PAGE->set_url($linkurl);
$PAGE->set_pagelayout('admin');
$PAGE->set_title($linktext);
$PAGE->set_heading(get_string('company_users_course_title', 'block_iomad_company_admin'));

// Build the nav bar.
company_admin_fix_breadcrumb($PAGE, $linktext, $returnurl);

$coursesform = new company_users_course_form($PAGE->url, $context, $companyid, $departmentid, $userid, $licenseid);

echo $OUTPUT->header();

// Check the department is valid.
if (!empty($departmentid) && !company::check_valid_department($companyid, $departmentid)) {
    print_error('invaliddepartment', 'block_iomad_company_admin');
}

// Check the userid is valid.
if (!company::check_valid_user($companyid, $userid, $departmentid)) {
    print_error('invaliduserdepartment', 'block_iomad_company_management');
}

//  Check the license is valid for this company.
if (!empty($licenseid) && !company::check_valid_company_license($companyid, $licenseid)) {
    print_error('invalidcompanylicense', 'block_iomad_company_admin');
}

if ($coursesform->is_cancelled() || optional_param('cancel', false, PARAM_BOOL)) {
    if ($returnurl) {
        redirect($returnurl);
    } else {
        redirect(new moodle_url('/local/iomad_dashboard/index.php'));
    }
} else {
    if ($companyid > 0) {
        $coursesform->process();
        $coursesform = new company_users_course_form($PAGE->url, $context, $companyid, $departmentid, $userid, $licenseid);
        // Display the license selector.
        $availablewarning = "";
        $licenselist = array();
        if (iomad::has_capability('block/iomad_company_admin:unallocate_licenses', context_system::instance())) {
            $parentlevel = company::get_company_parentnode($companyid);
            $userhierarchylevel = $parentlevel->id;
            // Get all the licenses.
            // Are we an educator?
            if (!empty($userid) && $DB->get_record('company_users', array('userid' => $userid, 'educator' => 1))) {
                $licenses = $DB->get_records('companylicense', array('companyid' => $companyid), 'expirydate DESC', 'id,type,name,startdate,expirydate');
            } else {
                $licenses = $DB->get_records_sql("SELECT id,type,name,startdate,expirydate FROM {companylicense}
                                                  WHERE companyid = :companyid
                                                  AND type < 2
                                                  ORDER BY expirydate DESC",
                                                  array('companyid' => $companyid));
            }
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
                if ($license->type > 1) {
                    $licenselist[$license->id] = $licenselist[$license->id] . " (" . get_string('educator', 'block_iomad_company_admin') .")";
                }
            }
        } else {
            $userlevel = $company->get_userlevel($USER);
            $userhierarchylevel = $userlevel->id;
            if (!empty($userid) && $DB->get_record('company_users', array('userid' => $userid, 'educator' => 1))) {
                $educator = true;
            } else {
                $educator = false;
            }
            $licenses = company::get_recursive_departments_licenses($userhierarchylevel);
            if (!empty($licenses)) {
                foreach ($licenses as $deptlicenseid) {
                    // Get the license record.
                    if ($license = $DB->get_records('companylicense',
                                                     array('id' => $deptlicenseid->licenseid, 'companyid' => $companyid),
                                                     null, 'id,name,startdate,expirydate')) {
                        if (!$educator && $license->type > 1) {
                            continue;
                        }
                        
                        if ($license[$deptlicenseid->licenseid]->expirydate > time()) {
                            if ($license->startdate > time()) {
                                $licenselist[$license->id] = $license->name . " (" . get_string('licensevalidfrom', 'block_iomad_company_admin', date($CFG->iomad_date_format, $license->startdate)) . ")";
                                if ($licenseid == $license->id) {
                                    $availablewarning = get_string('licensevalidfromwarning', 'block_iomad_company_admin', date($CFG->iomad_date_format, $license->startdate));
                                }
                            } else {
                                $licenselist[$license[$deptlicenseid->licenseid]->id]  = $license[$deptlicenseid->licenseid]->name;
                            }
                        }
                        if ($license->type > 1) {
                            $licenselist[$license->id] = $licenselist[$license->id] . " (" . get_string('educator', 'block_iomad_company_admin') . ")";
                        }
                    }
                }
            }
        }

        if (count($licenses) == 0) {
            echo '<h3>' . get_string('editlicensestitle', 'block_iomad_company_admin') . '</h3>';
            echo '<p>' . get_string('licensehelp', 'block_iomad_company_admin') . '</p>';
            echo '<b>' . get_string('nolicenses', 'block_iomad_company_admin') . '</b>';
        } else {
            echo '<h3>' . get_string('editlicensestitle', 'block_iomad_company_admin') . '</h3>';
            echo '<p>' . get_string('licensehelp', 'block_iomad_company_admin') . '</p>';
            echo '<div id="licenseSelector">';
            $selecturl = new moodle_url('/blocks/iomad_company_admin/company_users_licenses_form.php', $urlparams);
            $licenseselect = new single_select($selecturl, 'licenseid', $licenselist, $licenseid);
            $licenseselect->label = get_string('select_license', 'block_iomad_company_admin');
            $licenseselect->formid = 'chooselicense';
            echo html_writer::tag('div', $OUTPUT->render($licenseselect), array('id' => 'iomad_license_selector'));
            echo '</div>';

            if (!empty($availablewarning)) {
                echo html_writer::start_tag('div', array('class' => "alert alert-success"));
                echo $availablewarning;
                echo "</div>";
            }

            $coursesform->get_data();
            echo $coursesform->display();

        }
    }

    echo "<a class='btn btn-primary' href='$returnurl'>" . get_string('back') . "</a>";

    echo $OUTPUT->footer();
}