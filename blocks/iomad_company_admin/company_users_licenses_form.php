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

    public function __construct($actionurl, $context, $companyid, $departmentid, $userid, $licenseid) {
        global $USER, $DB;
        $this->selectedcompany = $companyid;
        $this->context = $context;
        $company = new company($this->selectedcompany);
        $this->parentlevel = company::get_company_parentnode($company->id);
        $this->companydepartment = $this->parentlevel->id;
        $this->licenseid = $licenseid;

        if (iomad::has_capability('block/iomad_company_admin:edit_all_departments', context_system::instance())) {
            $userhierarchylevel = $this->parentlevel->id;
        } else {
            $userlevel = company::get_userlevel($USER);
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
        if (!empty($this->licenseid)) {

            $license = $DB->get_record('companylicense', array('id' => $this->licenseid));
            $licensestring = get_string('licensedetails', 'block_iomad_company_admin', $license);
            $licensestring2 = get_string('licensedetails2', 'block_iomad_company_admin', $license);
            $licensestring3 = get_string('licensedetails3', 'block_iomad_company_admin', $license);
        } else {
            $licensestring = '';
            $licensestring2 = '';
            $licensestring3 = '';
        }

        if (!empty($this->licenseid)) {
        $mform->addElement('html', '<br /><p align="center"><b>' . get_string('licenseleft1', 'block_iomad_company_admin') .
                                    ((intval($licensestring3, 0)) - (intval($licensestring2, 0))) .
                                    get_string('licenseleft2', 'block_iomad_company_admin') . '</b>');

        $mform->addElement('html', '<h4>' . get_string('user_courses_for', 'block_iomad_company_admin', fullname($this->user)) . '</h4>');

        $mform->addElement('date_time_selector', 'due', get_string('senddate', 'block_iomad_company_admin'));
        $mform->addHelpButton('due', 'senddate', 'block_iomad_company_admin');

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

        } else {
        $mform->addElement('html', '<br /><p align="center"><b>' . get_string('selectlicenseblurb', 'block_iomad_company_admin') . '</b></p>');
        }
    }

    public function process() {
        global $DB, $CFG;

        $this->create_course_selectors();


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
                            $DB->insert_record('companylicense_users',
                                               array('userid' => $this->userid,
                                                     'licenseid' => $licenserecord['id'],
                                                     'licensecourseid' => $addcourse->id));

                            // Create an email event.
                            $license = new stdclass();
                            $license->length = $licenserecord['validlength'];
                            $license->valid = date($CFG->iomad_date_format, $licenserecord['expirydate']);
                            EmailTemplate::send('license_allocated', array('course' => $addcourse,
                                                                           'user' => $this->user,
                                                                           'due' => $duedate,
                                                                           'license' => $license));
                            $licenserecord['used'] = $DB->count_records('companylicense_users', array('licenseid' => $licenserecord['id']));
                            $DB->update_record('companylicense', $licenserecord);
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
                                                                   'licensecourseid' => $removecourse->id,
                                                                   'isusing' => 0))) {
                        $licenserecord = (array) $DB->get_record('companylicense', array('id' => $userlicenserecord->licenseid));
                        $DB->delete_records('companylicense_users', array('id' => $userlicenserecord->id));
                        $licenserecord['used'] = $DB->count_records('companylicense_users', array('licenseid' => $licenserecord['id']));
                        $DB->update_record('companylicense', $licenserecord);
                    }
                }

                $this->potentialcourses->invalidate_selected_courses();
                $this->currentcourses->invalidate_selected_courses();
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
$linkurl = new moodle_url('/blocks/iomad_company_admin/editusers.php');

// Print the page header.
$PAGE->set_context($context);
$PAGE->set_url($linkurl);
$PAGE->set_pagelayout('admin');
$PAGE->set_title($linktext);
$PAGE->set_heading(get_string('company_users_course_title', 'block_iomad_company_admin'));

// Build the nav bar.
company_admin_fix_breadcrumb($PAGE, $linktext, $linkurl);

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
        // Display the license selctor.
        $licenselist = array();
        if (iomad::has_capability('block/iomad_company_admin:unallocate_licenses', context_system::instance())) {
            $parentlevel = company::get_company_parentnode($companyid);
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
            $userlevel = company::get_userlevel($USER);
            $userhierarchylevel = $userlevel->id;
            $licenses = company::get_recursive_departments_licenses($userhierarchylevel);
            if (!empty($licenses)) {
                foreach ($licenses as $deptlicenseid) {
                    // Get the license record.
                    if ($license = $DB->get_records('companylicense',
                                                     array('id' => $deptlicenseid->licenseid, 'companyid' => $companyid),
                                                     null, 'id,name,expirydate')) {
                        if ($license[$deptlicenseid->licenseid]->expirydate > time()) {
                            $licenselist[$license[$deptlicenseid->licenseid]->id]  = $license[$deptlicenseid->licenseid]->name;
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

            echo $coursesform->display();

        }
    }

    echo $OUTPUT->footer();
}
