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
    protected $courseselct = array();
    protected $firstcourseid = 0;

    public function __construct($actionurl, $context, $companyid, $licenseid, $departmentid, $selectedcourses) {
        global $USER, $DB;
        $this->selectedcompany = $companyid;
        $this->context = $context;
        $company = new company($this->selectedcompany);
        $this->parentlevel = company::get_company_parentnode($company->id);
        $this->companydepartment = $this->parentlevel->id;
        $this->licenseid = $licenseid;
        $this->license = $DB->get_record('companylicense', array('id' => $licenseid));
        $this->selectedcourses = $selectedcourses;

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

        $this->subhierarchieslist = company::get_all_subdepartments($userhierarchylevel);
        if ($departmentid == 0) {
            $this->departmentid = $userhierarchylevel;
        } else {
            $this->departmentid = $departmentid;
        }
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

        if ($this->license->expirydate > time()) {
            // Add in the courses selector.
            $courseselector = $mform->addElement('select', 'courses', get_string('courses', 'block_iomad_company_admin'), $this->courseselect, array('id' => 'courseselector'));
            $courseselector->setMultiple(true);
            $courseselector->setSelected($this->firstcourseid);

            $mform->addElement('header', 'header', get_string('license_users_for',
                                                              'block_iomad_company_admin',
                                                              $this->license->name));
            $mform->addElement('html', '('.($this->license->allocation - $this->license->used).' / '.
            $this->license->allocation.get_string('licensetotal', 'block_iomad_company_admin').')');
        } else {
            $mform->addElement('header', 'header', get_string('license_users_for',
                                                              'block_iomad_company_admin',
                                                              $this->license->name).' *Expired* ');
            $mform->addElement('html', '('.($this->license->allocation - $this->license->used).' / '.
            $this->license->allocation.get_string('licensetotal', 'block_iomad_company_admin').')');
        }

        $mform->addElement('date_time_selector', 'due', get_string('senddate', 'block_iomad_company_admin'));
        $mform->addHelpButton('due', 'senddate', 'block_iomad_company_admin');

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
                          get_string('licenseallocate', 'block_iomad_company_admin') .
                          '" title="Enrol" /><br />

                      </div>

                      <div id="removecontrols"><input name="remove" id="remove" type="submit" value="' .
                          get_string('licenseremove', 'block_iomad_company_admin') .
                          '&nbsp;" title="Unenrol" />
                      </div>
                  </td>
                  <td id="potentialcell">');

            $mform->addElement('html', $this->potentialusers->display(true));
        }

        $mform->addElement('html', '
              </td>
            </tr>
          </table>');
        $mform->addElement('html', get_string('licenseusedwarning', 'block_iomad_company_admin'));
    }

    public function process() {
        global $DB, $CFG;

        $this->create_user_selectors();
        $courses = array();
        if (!is_array($this->selectedcourses)) {
            $courses[] = $this->selectedcourses;
        } else {
            $courses = $this->selectedcourses;
        }
        // Process incoming allocations.
        if (optional_param('add', false, PARAM_BOOL) && confirm_sesskey()) {
            $userstoassign = $this->potentialusers->get_selected_users();
            $numberoflicenses = $this->license->allocation;
            $count = $this->license->used;
            $licenserecord = (array) $this->license;

            if (!empty($userstoassign) && !empty($courses)) {
                foreach ($userstoassign as $adduser) {

                    // Check the userid is valid.
                    if (!company::check_valid_user($this->selectedcompany, $adduser->id, $this->departmentid)) {
                        print_error('invaliduserdepartment', 'block_iomad_company_management');
                    }

                    foreach ($courses as $course) {
                        if ($count >= $numberoflicenses) {
                            // Set the used amount.
                            $licenserecord['used'] = $count;
                            $DB->update_record('companylicense', $licenserecord);
                            redirect(new moodle_url("/blocks/iomad_company_admin/company_license_users_form.php",
                                                     array('licenseid' => $this->licenseid, 'error' => 1)));
                        }
                        $allow = true;
                        if ($allow) {
                            $recordarray = array('licensecourseid' => $course,
                                                 'userid' => $adduser->id,
                                                 'licenseid' => $this->licenseid,
                                                 'issuedate' => time());
                            if (!$DB->get_record('companylicense_users', $recordarray)) {
                                $DB->insert_record('companylicense_users', $recordarray);
                                $count++;
                            }
                        }
                        $due = optional_param_array('due', array(), PARAM_INT);
                        if (!empty($due)) {
                            $duedate = strtotime($due['year'] . '-' . $due['month'] . '-' . $due['day'] . ' ' . $due['hour'] . ':' . $due['minute']);
                        } else {
                            $duedate = 0;
                        }
                        // Create an email event.
                        $license = new stdclass();
                        $license->length = $licenserecord['validlength'];
                        $license->valid = date($CFG->iomad_date_format, $licenserecord['expirydate']);
                        EmailTemplate::send('license_allocated', array('course' => $course,
                                                                       'user' => $adduser,
                                                                       'due' => $duedate,
                                                                       'license' => $license));
                    }
                }

                // Set the used amount for the license.
                $licenserecord['used'] = $DB->count_records('companylicense_users', array('licenseid' => $this->licenseid));
                $DB->update_record('companylicense', $licenserecord);
                $this->license->used = $licenserecord['used'];


                $this->potentialusers->invalidate_selected_users();
                $this->currentusers->invalidate_selected_users();
            }
        }

        // Process incoming unallocations.
        if (optional_param('remove', false, PARAM_BOOL) && confirm_sesskey()) {
            $licensestounassign = optional_param_array('currentlyenrolledusers', null, PARAM_INT);
            $count = $this->license->used;
            $licenserecord = (array) $this->license;

            if (!empty($licensestounassign)) {
                foreach ($licensestounassign as $unassignid) {
                    $licensedata = $DB->get_record('companylicense_users',array('id' => $unassignid, 'licenseid' => $this->licenseid), 'userid,isusing', MUST_EXIST);

                    // Check the userid is valid.
                    if (!company::check_valid_user($this->selectedcompany, $licensedata->userid, $this->departmentid)) {
                        print_error('invaliduserdepartment', 'block_iomad_company_management');
                    }

                    if (!$licensedata->isusing) {
                        $DB->delete_records('companylicense_users', array('id' => $unassignid));
                        $count--;
                        // Create an email event.
                        EmailTemplate::send('license_removed', array('course' => $licensedata->licensecourseid, 'user' => $licensedata->userid));
                    }
                }

                // Update the number of allocated records..
                if ($count < 0) {
                    // Cant have less than 0 licenses.
                    $count = 0;
                }
                $licenserecord['used'] = $DB->count_records('companylicense_users', array('licenseid' => $this->licenseid));
                $DB->update_record('companylicense', $licenserecord);
                $this->license->used = $licenserecord['used'];

                $this->potentialusers->invalidate_selected_users();
                $this->currentusers->invalidate_selected_users();
            }
        }
    }
}


$returnurl = optional_param('returnurl', '', PARAM_LOCALURL);
$companyid = optional_param('companyid', 0, PARAM_INTEGER);
$courseid = optional_param('courseid', 0, PARAM_INTEGER);
$departmentid = optional_param('departmentid', 0, PARAM_INTEGER);
$licenseid = optional_param('licenseid', 0, PARAM_INTEGER);
$error = optional_param('error', 0, PARAM_INTEGER);
$selectedcourses = optional_param_array('courses', array(), PARAM_INT);
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

$licenselist = array();
if (iomad::has_capability('block/iomad_company_admin:unallocate_licenses', context_system::instance())) {
    $userhierarchylevel = $parentlevel->id;
    // Get all the licenses.
    $licenses = $DB->get_records('companylicense', array('companyid' => $companyid), null, 'id,name,expirydate');
    foreach ($licenses as $license) {
        if ($license->expirydate > time()) {
            $licenselist[$license->id] = $license->name;
        } else {
            $licenselist[$license->id] = $license->name." (expired)";
        }

    }

} else {
    $userlevel = $company->get_userlevel($USER);
    $userhierarchylevel = $userlevel->id;
    if (iomad::has_capability('block/iomad_company_admin:edit_licenses', context_system::instance())) {
        $alllicenses = true;
    } else {
        $allliceses = false;
    }
    $licenses = company::get_recursive_departments_licenses($userhierarchylevel);
    if (!empty($licenses)) {
        foreach ($licenses as $deptlicenseid) {
            // Get the license record.
            if ($license = $DB->get_records('companylicense',
                                             array('id' => $deptlicenseid->licenseid, 'companyid' => $companyid),
                                             null, 'id,name,expirydate')) {
                if ($alllicenses || $license[$deptlicenseid->licenseid]->expirydate > time()) {
                    $licenselist[$license[$deptlicenseid->licenseid]->id]  = $license[$deptlicenseid->licenseid]->name;
                }
            }
        }
    }
}

$usersform = new company_license_users_form($PAGE->url, $context, $companyid, $licenseid, $userhierarchylevel, $selectedcourses);

echo $OUTPUT->header();

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
echo html_writer::tag('div', $OUTPUT->render($select), array('id' => 'iomad_license_selector'));
$fwselectoutput = html_writer::tag('div', $OUTPUT->render($select), array('id' => 'iomad_license_selector'));

// Do we have any licenses?
if (empty($licenselist)) {
    echo get_string('nolicenses', 'block_iomad_company_admin');
    echo $OUTPUT->footer();
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
            $outputstring = get_string('licenseassignedto', 'block_iomad_company_admin');
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
        echo $outputstring."</br>";
        $usersform->process();
        if ($error == 1) {
            echo "<h3>".get_string('licensetoomanyusers', 'block_iomad_company_admin')."</h3>";
        }
        echo $usersform->display();
    }
}
/*<script type="text/javascript">
Y.on('change', submit_form, '#courseselector');
 function submit_form() {
var form = Y.one('#mform1'); // The id for the moodle form is automatically set.
    form.submit();
 }
</script>
*/
echo $OUTPUT->footer();
