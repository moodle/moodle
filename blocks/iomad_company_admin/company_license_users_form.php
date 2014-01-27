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

    public function __construct($actionurl, $context, $companyid, $licenseid, $departmentid) {
        global $USER, $DB;
        $this->selectedcompany = $companyid;
        $this->context = $context;
        $company = new company($this->selectedcompany);
        $this->parentlevel = company::get_company_parentnode($company->id);
        $this->companydepartment = $this->parentlevel->id;
        $this->licenseid = $licenseid;
        $this->license = $DB->get_record('companylicense', array('id' => $licenseid));

        if (has_capability('block/iomad_company_admin:allocate_licenses', context_system::instance())) {
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
        parent::__construct($actionurl);
    }

    public function set_course($courses) {
        $keys = array_keys($courses);
        $this->course = $courses[$keys[0]];
    }

    public function create_user_selectors() {
        if (!empty ($this->licenseid)) {
            $options = array('context' => $this->context,
                             'companyid' => $this->selectedcompany,
                             'licenseid' => $this->licenseid,
                             'departmentid' => $this->departmentid,
                             'subdepartments' => $this->subhierarchieslist,
                             'parentdepartmentid' => $this->parentlevel);
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
                          <input name="add" id="add" type="submit" value="&#x25C4;&nbsp;' .
                          get_string('licenseallocate', 'block_iomad_company_admin') .
                          '" title="Enrol" /><br />

                      </div>

                      <div id="removecontrols">
                          <input name="remove" id="remove" type="submit" value="' .
                          get_string('licenseremove', 'block_iomad_company_admin') .
                          '&nbsp;&#x25BA;" title="Unenrol" />
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
        // Get the courses to send to if emails are configured.
        $courses = company::get_courses_by_license($this->license->id);

        // Process incoming allocations.
        if (optional_param('add', false, PARAM_BOOL) && confirm_sesskey()) {
            $userstoassign = $this->potentialusers->get_selected_users();
            $numberoflicenses = $this->license->allocation;
            $count = $this->license->used;
            $licenserecord = (array) $this->license;

            if (!empty($userstoassign)) {

                foreach ($userstoassign as $adduser) {
                    if ($count >= $numberoflicenses) {
                        // Set the used amount.
                        $licenserecord['used'] = $count;
                        $DB->update_record('companylicense', $licenserecord);
                        redirect(new moodle_url("/blocks/iomad_company_admin/company_license_users_form.php",
                                                 array('licenseid' => $this->licenseid, 'error' => 1)));
                    }
                    $allow = true;

                    if ($allow) {
                        $count++;
                        $DB->insert_record('companylicense_users',
                                            array('userid' => $adduser->id, 'licenseid' => $this->licenseid));
                    }

                    // Create an email event.
                    foreach ($courses as $course) {
                        $license = new stdclass();
                        $license->length = $licenserecord['validlength'];
                        $license->valid = date('d M Y', $licenserecord['expirydate']);
                        EmailTemplate::send('license_allocated', array('course' => $course,
                                                                       'user' => $adduser,
                                                                       'license' => $license));
                    }
                }

                // Set the used amount for the license.
                $licenserecord['used'] = $count;
                $DB->update_record('companylicense', $licenserecord);

                $this->potentialusers->invalidate_selected_users();
                $this->currentusers->invalidate_selected_users();
            }
        }

        // Process incoming unallocations.
        if (optional_param('remove', false, PARAM_BOOL) && confirm_sesskey()) {
            $userstounassign = $this->currentusers->get_selected_users();
            $count = $this->license->used;
            $licenserecord = (array) $this->license;

            if (!empty($userstounassign)) {
                foreach ($userstounassign as $removeuser) {
                    if ($licensedata = $DB->get_record('companylicense_users',
                                                        array('userid' => $removeuser->id, 'licenseid' => $this->licenseid))) {
                        if (!$licensedata->isusing) {
                            $DB->delete_records('companylicense_users', array('id' => $licensedata->id));
                            $count--;
                        }
                    }
                    // Create an email event.
                    foreach ($courses as $course) {
                        EmailTemplate::send('license_removed', array('course' => $course, 'user' => $removeuser));
                    }
                }

                // Update the number of allocated records..
                if ($count < 0) {
                    // Cant have less than 0 licenses.
                    $count = 0;
                }
                $licenserecord['used'] = $count;
                $DB->update_record('companylicense', $licenserecord);

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

$context = context_system::instance();
require_login();
require_capability('block/iomad_company_admin:allocate_licenses', $context);

$PAGE->set_context($context);

// Correct the navbar.
// Set the name for the page.
$linktext = get_string('licenseusers', 'block_iomad_company_admin');
// Set the url.
$linkurl = new moodle_url('/blocks/iomad_company_admin/company_license_users_form.php');
// Build the nav bar.
company_admin_fix_breadcrumb($PAGE, $linktext, $linkurl);

$blockpage = new blockpage($PAGE, $OUTPUT, 'iomad_company_admin', 'block', 'company_license_users_title');
$blockpage->setup();

// Set the companyid
$companyid = iomad::get_my_companyid($context);

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
if (has_capability('block/iomad_company_admin:unallocate_licenses', context_system::instance())) {
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
                                             null, 'id,name')) {
                $licenselist[$license[$deptlicenseid->licenseid]->id]  = $license[$deptlicenseid->licenseid]->name;
            }
        }
    }
}

$usersform = new company_license_users_form($PAGE->url, $context, $companyid, $licenseid, $userhierarchylevel);

$blockpage->display_header();

// Display the license selector.
$select = new single_select($linkurl, 'licenseid', $licenselist, $licenseid);
$select->label = get_string('licenseselect', 'block_iomad_company_admin');
$select->formid = 'chooselicense';
echo html_writer::tag('div', $OUTPUT->render($select), array('id' => 'iomad_license_selector'));
$fwselectoutput = html_writer::tag('div', $OUTPUT->render($select), array('id' => 'iomad_license_selector'));

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
echo $OUTPUT->footer();
