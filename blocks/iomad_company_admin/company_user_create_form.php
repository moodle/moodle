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
 * Script to let a user create a user for a particular company.
 */

require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->libdir . '/formslib.php');
require_once($CFG->dirroot . '/user/editlib.php');
require_once('lib.php');

class user_edit_form extends company_moodleform {
    protected $title = '';
    protected $description = '';
    protected $context = null;
    protected $courseselector = null;
    protected $departmentid = 0;
    protected $licenseid = 0;
    protected $licensecourses = array();

    public function __construct($actionurl, $companyid, $departmentid, $licenseid=0) {
        global $CFG, $USER;

        $this->selectedcompany = $companyid;
        $this->departmentid = $departmentid;
        $this->licenseid = $licenseid;
        $company = new company($this->selectedcompany);
        $parentlevel = company::get_company_parentnode($company->id);
        $this->companydepartment = $parentlevel->id;
        $systemcontext = context_system::instance();

        if (iomad::has_capability('block/iomad_company_admin:edit_all_departments', $systemcontext)) {
            $userhierarchylevel = $parentlevel->id;
        } else {
            $userlevel = $company->get_userlevel($USER);
            $userhierarchylevel = $userlevel->id;
        }

        $this->subhierarchieslist = company::get_all_subdepartments($userhierarchylevel);
        if ($this->departmentid == 0) {
            $departmentid = $userhierarchylevel;
        } else {
            $departmentid = $this->departmentid;
        }
        $this->userdepartment = $userhierarchylevel;

        $options = array('context' => $this->context,
                         'multiselect' => true,
                         'companyid' => $this->selectedcompany,
                         'departmentid' => $departmentid,
                         'subdepartments' => $this->subhierarchieslist,
                         'parentdepartmentid' => $parentlevel,
                         'showopenshared' => true,
                         'license' => false);
        
        $this->currentcourses = new potential_subdepartment_course_selector('currentcourses', $options);
        $this->currentcourses->set_rows(20);
        $this->context = context_coursecat::instance($CFG->defaultrequestcategory);
        parent::__construct($actionurl);
    }

    public function definition() {
        global $CFG, $DB, $output;

        // Get the system context.
        $systemcontext = context_system::instance();

        $mform =& $this->_form;

        // Then show the fields about where this block appears.
        $mform->addElement('header', 'header', get_string('companyuser', 'block_iomad_company_admin'));

        $mform->addElement('hidden', 'companyid', $this->selectedcompany);
        $mform->setType('companyid', PARAM_INT);

        /* copied from /user/editlib.php */
        $strrequired = get_string('required');

        // Deal with the name order sorting and required fields.
        $necessarynames = useredit_get_required_name_fields();
        foreach ($necessarynames as $necessaryname) {
            $mform->addElement('text', $necessaryname, get_string($necessaryname), 'maxlength="100" size="30"');
            $mform->addRule($necessaryname, $strrequired, 'required', null, 'client');
            $mform->setType($necessaryname, PARAM_NOTAGS);
        } 

        // Do not show email field if change confirmation is pending.
        if (!empty($CFG->emailchangeconfirmation) and !empty($user->preference_newemail)) {
            $notice = get_string('auth_emailchangepending', 'auth_email', $user);
            $notice .= '<br /><a href="edit.php?cancelemailchange=1&amp;id='.$user->id.'">'
                    . get_string('auth_emailchangecancel', 'auth_email') . '</a>';
            $mform->addElement('static', 'emailpending', get_string('email'), $notice);
        } else {
            $mform->addElement('text', 'email', get_string('email'), 'maxlength="100" size="30"');
            $mform->addRule('email', $strrequired, 'required', null, 'client');
            $mform->setType('email', PARAM_EMAIL);
        }
        if (!empty($CFG->iomad_allow_username)) {
            $mform->addElement('text', 'username', get_string('username'), 'size="20"');
            $mform->addHelpButton('username', 'username', 'auth');
            $mform->setType('username', PARAM_RAW);
            $mform->disabledif('username', 'use_email_as_username', 'eq', 1);
        }
        $mform->addElement('advcheckbox', 'use_email_as_username', get_string('iomad_use_email_as_username', 'local_iomad_settings'));
        if (!empty($CFG->iomad_use_email_as_username)) {
            $mform->setDefault('use_email_as_username', 1);
        } else {
            $mform->setDefault('use_email_as_username', 0);
        }


        /* /copied from /user/editlib.php */

        $mform->addElement('static', 'blankline', '', '');
        if (!empty($CFG->passwordpolicy)) {
            $mform->addElement('static', 'passwordpolicyinfo', '', print_password_policy());
        }
        $mform->addElement('passwordunmask', 'newpassword', get_string('newpassword'), 'size="20"');
        $mform->addHelpButton('newpassword', 'newpassword');
        $mform->setType('newpassword', PARAM_RAW);
        $mform->addElement('static', 'generatepassword', '',
                            get_string('leavepasswordemptytogenerate', 'block_iomad_company_admin'));

        $mform->addElement('advcheckbox', 'preference_auth_forcepasswordchange', get_string('forcepasswordchange'));
        $mform->addHelpButton('preference_auth_forcepasswordchange', 'forcepasswordchange');
        $mform->setDefault('preference_auth_forcepasswordchange', 1);

        $mform->addElement('selectyesno', 'sendnewpasswordemails',
                            get_string('sendnewpasswordemails', 'block_iomad_company_admin'));
        $mform->setDefault('sendnewpasswordemails', 1);
        $mform->disabledIf('sendnewpasswordemails', 'newpassword', 'eq', '');

        $mform->addElement('date_time_selector', 'due', get_string('senddate', 'block_iomad_company_admin'));
        $mform->disabledIf('due', 'sendnewpasswordemails', 'eq', '0');
        $mform->addHelpButton('due', 'senddate', 'block_iomad_company_admin');


        // Deal with company optional fields.
        $mform->addElement('header', 'category_id', get_string('advanced'));
        
        $departmentslist = company::get_all_subdepartments($this->userdepartment);
        $departmenttree = company::get_all_subdepartments_raw($this->userdepartment);
        $treehtml = $output->department_tree($departmenttree, optional_param('userdepartment', 0, PARAM_INT));

        $mform->addElement('html', $treehtml);
        $mform->addElement('html', "<div style='display: none;'>");
        // Department drop down.
        $mform->addElement('select', 'userdepartment', get_string('department', 'block_iomad_company_admin'),
                            $this->subhierarchieslist, $this->userdepartment);

        $mform->addElement('html', "</div>");

        // Add in company/department manager checkboxes.
        $managerarray = array();
        if (iomad::has_capability('block/iomad_company_admin:assign_department_manager', $systemcontext)) {
            $managerarray['0'] = get_string('user', 'block_iomad_company_admin');
            $managerarray['2'] = get_string('departmentmanager', 'block_iomad_company_admin');
        }
        if (iomad::has_capability('block/iomad_company_admin:assign_company_manager', $systemcontext)) {
            if (empty($managearray)) {
                $managerarray['0'] = get_string('user', 'block_iomad_company_admin');
            }
            $managerarray['1'] = get_string('companymanager', 'block_iomad_company_admin');
        }
        if (!empty($managerarray)) {
            $mform->addElement('select', 'managertype', get_string('managertype', 'block_iomad_company_admin'), $managerarray, 0);
        } else {
            $mform->addElement('hidden', 'managertype', 0);
        }
        // Deal with the educator role.
        if (!$CFG->iomad_autoenrol_managers) {
            $mform->addElement('selectyesno', 'educator', get_string('assigneducator', 'block_iomad_company_admin'));
            $mform->addHelpButton('educator', 'educator', 'block_iomad_company_admin');
        } else {
            $mform->addElement('hidden', 'educator', 0);
            $mform->setType('educator', PARAM_BOOL);
        }

        // Get global fields.
        if ($fields = $DB->get_records_sql("SELECT * FROM {user_info_field}
                                            WHERE categoryid NOT IN (
                                             SELECT profileid FROM {company})")) {
            // Display the header and the fields.
            foreach ($fields as $field) {
                require_once($CFG->dirroot.'/user/profile/field/'.$field->datatype.'/field.class.php');
                $newfield = 'profile_field_'.$field->datatype;
                $formfield = new $newfield($field->id);
                $formfield->edit_field($mform);
                $mform->setDefault($formfield->inputname, $formfield->field->defaultdata);
            }
        }
        // Get company category.
        if ($companyinfo = $DB->get_record('company', array('id' => $this->selectedcompany))) {

            // Get fields from company category.
            if ($fields = $DB->get_records('user_info_field', array('categoryid' => $companyinfo->profileid))) {
                // Display the header and the fields.
                foreach ($fields as $field) {
                    require_once($CFG->dirroot.'/user/profile/field/'.$field->datatype.'/field.class.php');
                    $newfield = 'profile_field_'.$field->datatype;
                    $formfield = new $newfield($field->id);
                    $formfield->edit_field($mform);
                    $mform->setDefault($formfield->inputname, $formfield->field->defaultdata);
                }
            }
        }

        // Deal with licenses.
        if (iomad::has_capability('block/iomad_company_admin:allocate_licenses', $systemcontext)) {
            $mform->addElement('header', 'licenses', get_string('assignlicenses', 'block_iomad_company_admin'));
            $foundlicenses = $DB->get_records_sql_menu("SELECT id, name FROM {companylicense}
                                                   WHERE expirydate >= :timestamp
                                                   AND companyid = :companyid
                                                   AND used < allocation",
                                                   array('timestamp' => time(),
                                                         'companyid' => $this->selectedcompany));
            $licenses = array('0' => get_string('nolicense', 'block_iomad_company_admin')) + $foundlicenses;
            $licensecourses = array();
            if (count($foundlicenses) == 0) {
                // No valid licenses.
                $mform->addElement('html', '<div id="licensedetails"><b>' . get_string('nolicenses', 'block_iomad_company_admin') . '</b></div>');
            } else {
                $mform->addElement('html', "<div class='fitem'><div class='fitemtitle'>" .
                                            get_string('selectlicensecourse', 'block_iomad_company_admin') .
                                            "</div><div class='felement'>");
                $mform->addElement('select', 'licenseid', get_string('select_license', 'block_iomad_company_admin'), $licenses, array('id' => 'licenseidselector'));
                $mylicenseid = $this->licenseid;
                if (empty($this->licenseid)) {
                    $mform->addElement('html', '<div id="licensedetails"></div>');
                } else {
                    $mylicensedetails = $DB->get_record('companylicense', array('id' => $this->licenseid));
                    $licensestring = get_string('licensedetails', 'block_iomad_company_admin', $mylicensedetails);
                    $licensestring2 = get_string('licensedetails2', 'block_iomad_company_admin', $mylicensedetails);
                    $licensestring3 = get_string('licensedetails3', 'block_iomad_company_admin', $mylicensedetails);
                    $mform->addElement('html', '<div id="    "><b>You have ' . ((intval($licensestring3, 0)) - (intval($licensestring2, 0))) . ' courses left to allocate on this license </b></div>');
                }

                // Is this a program of courses?
                if (!empty($mylicensedetails->program)) {
                     $mform->addElement('html', "<div style='display:none'>");
                }
                if (!$licensecourses = $DB->get_records_sql_menu("SELECT c.id, c.fullname FROM {companylicense_courses} clc
                                                             JOIN {course} c ON (clc.courseid = c.id
                                                             AND clc.licenseid = :licenseid)
                                                             ORDER BY c.fullname",
                                                             array('licenseid' => $mylicenseid))) {
                    $licensecourses = array();
                }
            }

            $mform->addElement('html', '<div id="licensecoursescontainer" style="display:none;">');
            $licensecourseselect = $mform->addElement('select', 'licensecourses',
                                                      get_string('select_license_courses', 'block_iomad_company_admin'),
                                                      $licensecourses, array('id' => 'licensecourseselector'));
            $licensecourseselect->setMultiple(true);
            $mform->addElement('html', '</div>');
            if (!empty($mylicensedetails->program)) {
                $licensecourseselect->setSelected($licensecourses);
            } else {
                $licensecourseselect->setSelected(array());
            }
        }

        if (iomad::has_capability('block/iomad_company_admin:company_course_users', $systemcontext)) {
            $mform->addElement('header', 'courses', get_string('assigncourses', 'block_iomad_company_admin'));
            $mform->addElement('html', "<div class='fitem'><div class='fitemtitle'>" .
                                        get_string('selectenrolmentcourse', 'block_iomad_company_admin') .
                                        "</div><div class='felement'>");
            $mform->addElement('html', $this->currentcourses->display(true));
            $mform->addElement('html', "</div></div>");
        }

        // add action buttons
        $buttonarray = array();
        $buttonarray[] = &$mform->createElement('submit', 'submitbutton',
                            get_string('createuseragain', 'block_iomad_company_admin'));
        $buttonarray[] = &$mform->createElement('submit', 'submitandback',
                            get_string('createuserandback', 'block_iomad_company_admin'));
        $buttonarray[] = &$mform->createElement('cancel');
        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
        $mform->closeHeaderBefore('buttonar');

    }

    public function get_data() {
        $data = parent::get_data();

        if ($data) {
            $data->title = '';
            $data->description = '';

            if ($this->title) {
                $data->title = $this->title;
            }

            if ($this->description) {
                $data->description = $this->description;
            }

            if ($this->courseselector) {
                $data->selectedcourses = $this->courseselector->get_selected_courses();
            }
        }
        return $data;
    }

    // Perform some extra moodle validation.
    /* copied from /user/edit_form.php */
    public function validation($usernew, $files) {
        global $CFG, $DB;

        $errors = parent::validation($usernew, $files);

        $usernew = (object)$usernew;

        // Validate email.
        if ($DB->record_exists('user', array('email' => $usernew->email, 'mnethostid' => $CFG->mnet_localhost_id))) {
            $errors['email'] = get_string('emailexists');
        }

        if (!empty($usernew->newpassword)) {
            $errmsg = ''; // Prevent eclipse warning.
            if (!check_password_policy($usernew->newpassword, $errmsg)) {
                $errors['newpassword'] = $errmsg;
            }
        }

        // It is insecure to send passwords by email without forcing them to be changed on first login.
        if (!$usernew->preference_auth_forcepasswordchange && $usernew->sendnewpasswordemails) {
            $errors['preference_auth_forcepasswordchange'] = get_string('sendemailsforcepasswordchange',
                                                                        'block_iomad_company_admin',
                                                             array('forcechange' => get_string('forcepasswordchange'),
                                                                   'sendemail' => get_string('sendnewpasswordemails',
                                                                   'block_iomad_company_admin')));
        }

        //  Check numbers of licensed courses against license.
        if (!empty($usernew->licenseid)) {
            $license = $DB->get_record('companylicense', array('id' => $usernew->licenseid));

            // Are we dealing with a program license?
            if (!empty($license->program)) {
                // If so the courses are not passed automatically.
                $usernew->licensecourses =  $DB->get_records_sql_menu("SELECT c.id, c.fullname FROM {companylicense_courses} clc
                                                                       JOIN {course} c ON (clc.courseid = c.id
                                                                       AND clc.licenseid = :licenseid)",
                                                                       array('licenseid' => $license->id));
            }
        
            if (!empty($usernew->licensecourses)) {
                if ($license = $DB->get_record('companylicense', array('id' => $usernew->licenseid))) {
                    if (count($usernew->licensecourses) + $license->used > $license->allocation) {
                        $errors['licensecourses'] = get_string('triedtoallocatetoomanylicenses', 'block_iomad_company_admin');
                    }
                } else {
                    $errors['licenseid'] = get_string('invalidlicense', 'block_iomad_company_admin');
                }
            }
        }
        return $errors;
    }

}

$returnurl = optional_param('returnurl', '', PARAM_LOCALURL);
$companyid = optional_param('companyid', company_user::companyid(), PARAM_INTEGER);
$departmentid = optional_param('departmentid', 0, PARAM_INTEGER);
$createdok = optional_param('createdok', 0, PARAM_INTEGER);
$createcourses = optional_param_array('currentcourses', null, PARAM_INT);
$licenseid = optional_param('licenseid', 0, PARAM_INTEGER);

$context = context_system::instance();
require_login();
iomad::require_capability('block/iomad_company_admin:user_create', $context);

$urlparams = array('companyid' => $companyid);
if ($returnurl) {
    $urlparams['returnurl'] = $returnurl;
}
$companylist = new moodle_url('/local/iomad_dashboard/index.php', $urlparams);

// Correct the navbar.
// Set the name for the page.
$linktext = get_string('createuser', 'block_iomad_company_admin');
// Set the url.
$linkurl = new moodle_url('/blocks/iomad_company_admin/company_user_create_form.php');
$dashboardurl = new moodle_url('/local/iomad_dashboard/index.php');

// Print the page header.
$PAGE->set_context($context);
$PAGE->set_url($linkurl);
$PAGE->set_pagelayout('admin');
$PAGE->set_title($linktext);

// Set the page heading.
$PAGE->set_heading(get_string('name', 'local_iomad_dashboard') . " - $linktext");

$PAGE->requires->jquery();

$output = $PAGE->get_renderer('block_iomad_company_admin');

// Javascript for fancy select.
// Parameter is name of proper select form element. 
$PAGE->requires->js_call_amd('block_iomad_company_admin/department_select', 'init', array('userdepartment', '', $departmentid));


// Build the nav bar.
company_admin_fix_breadcrumb($PAGE, $linktext, $linkurl);

// Set the companyid
$companyid = iomad::get_my_companyid($context);
$company = new company($companyid);

$mform = new user_edit_form($PAGE->url, $companyid, $departmentid, $licenseid);
if ($mform->is_cancelled()) {
    redirect($dashboardurl);
    die;
} else if ($data = $mform->get_data()) {
    // Trim first and lastnames
    $data->firstname = trim($data->firstname);
    $data->lastname = trim($data->lastname);

    $data->userid = $USER->id;
    if ($companyid > 0) {
        $data->companyid = $companyid;
    }

    if (!$userid = company_user::create($data)) {
        $this->verbose("Error inserting a new user in the database!");
        if (!$this->get('ignore_errors')) {
            die();
        }
    }
    $user = new stdclass();
    $user->id = $userid;
    $data->id = $userid;

    // Save custom profile fields data.
    profile_save_data($data);

    $systemcontext = context_system::instance();

    $companycourseeditorrole = $DB->get_record('role', array('shortname' => 'companycourseeditor'));
    $companycoursenoneditorrole = $DB->get_record('role', array('shortname' => 'companycoursenoneditor'));

    // Check if we are assigning a different role to the user.
    if (!empty($data->managertype)) {
        if ($CFG->iomad_autoenrol_managers) {
            $data->educator = 1;
        }
        if ($data->managertype == 2) {
            // Assign the department manager role.
            $departmentmanagerrole = $DB->get_record('role', array('shortname' => 'companydepartmentmanager'));
            role_assign($departmentmanagerrole->id, $userid, $systemcontext->id);
        } else if ($data->managertype == 1) {
            // Assign the user as a company manager.
            $companymanagerrole = $DB->get_record('role', array('shortname' => 'companymanager'));
            // Give them the manager role.
            role_assign($companymanagerrole->id, $userid, $systemcontext->id);
        }
    }

    if (!empty($data->educator)) {
        if ($companycourses = $DB->get_records('company_course', array('companyid' => $companyid))) {
            foreach ($companycourses as $companycourse) {
                if ($DB->record_exists('course', array('id' => $companycourse->courseid))) {
                    //if its a licensed course - skip it.
                    if ($DB->record_exists('iomad_courses', array ('licensed' => 1,
                                                                   'courseid' => $companycourse->courseid))) {
                        continue;
                    }
                    // If it's not a shared course - assign the editor type role.
                    if ($DB->record_exists('iomad_courses', array ('shared' => 0,
                                                                   'courseid' => $companycourse->courseid))) {
                        company_user::enrol($user,
                                             array($companycourse->courseid),
                                             $companycourse->companyid,
                                             $companycourseeditorrole->id);
                    } else {
                         company_user::enrol($user,
                                             array($companycourse->courseid),
                                             $companycourse->companyid,
                                             $companycoursenoneditorrole->id);
                    }
                }
            }
        }
    }

    // Create an event for this.
    $managertypes = $company->get_managertypes();
    $eventother = array('companyname' => $company->get_name(),
                        'companyid' => $company->id,
                        'usertype' => $data->managertype,
                        'usertypename' => $managertypes[$data->managertype]);
    $event = \block_iomad_company_admin\event\company_user_assigned::create(array('context' => context_system::instance(),
                                                                                  'objectid' => $company->id,
                                                                                  'userid' => $user->id,
                                                                                  'other' => $eventother));
    $event->trigger();

    // Assign the user to the default company department.
    $parentnode = company::get_company_parentnode($companyid);
    if (iomad::has_capability('block/iomad_company_admin:edit_all_departments', $systemcontext)) {
        $userhierarchylevel = $parentnode->id;
    } else {
        $userlevel = $company->get_userlevel($USER);
        $userhierarchylevel = $userlevel->id;
    }
    company::assign_user_to_department($data->userdepartment, $userid);

    // Enrol the user on the courses.
    if (!empty($createcourses)) {
        $userdata = $DB->get_record('user', array('id' => $userid));
        company_user::enrol($userdata, $createcourses, $companyid);
    }
    // Assign and licenses.
    if (!empty($licenseid)) {
        $licenserecord = (array) $DB->get_record('companylicense', array('id' => $licenseid));
        if (!empty($licenserecord['program'])) {
            // If so the courses are not passed automatically.
            $data->licensecourses =  $DB->get_records_sql_menu("SELECT c.id, clc.courseid FROM {companylicense_courses} clc
                                                                   JOIN {course} c ON (clc.courseid = c.id
                                                                   AND clc.licenseid = :licenseid)",
                                                                   array('licenseid' => $licenserecord['id']));
        }
        
        if (!empty($data->licensecourses)) {
            $userdata = $DB->get_record('user', array('id' => $userid));
            $count = $licenserecord['used'];
            $numberoflicenses = $licenserecord['allocation'];
            foreach ($data->licensecourses as $licensecourse) {
                if ($count >= $numberoflicenses) {
                    // Set the used amount.
                    $licenserecord['used'] = $count;
                    $DB->update_record('companylicense', $licenserecord);
                    redirect(new moodle_url("/blocks/iomad_company_admin/company_license_users_form.php",
                                             array('licenseid' => $licenseid, 'error' => 1)));
                }
                $allow = true;
    
                if ($allow) {
                    $count++;
                    $DB->insert_record('companylicense_users',
                                        array('userid' => $userdata->id,
                                              'licenseid' => $licenseid,
                                              'issuedate' => time(),
                                              'licensecourseid' => $licensecourse));
                }

                // Create an event.
                $eventother = array('licenseid' => $licenseid,
                                    'duedate' => $data->due);
                $event = \block_iomad_company_admin\event\user_license_assigned::create(array('context' => context_course::instance($licensecourse),
                                                                                              'objectid' => $licenseid,
                                                                                              'courseid' => $licensecourse,
                                                                                              'userid' => $userdata->id,
                                                                                              'other' => $eventother));
                $event->trigger();
            }
        }
    }

    if (isset($data->submitandback)) {
        $dashboardurl->param('noticeok', get_string('usercreated', 'block_iomad_company_admin'));
        redirect($dashboardurl);
    } else {
        redirect($linkurl."?createdok=1");
    }
}
echo $output->header();

// Check the department is valid.
if (!empty($departmentid) && !company::check_valid_department($companyid, $departmentid)) {
    print_error('invaliddepartment', 'block_iomad_company_admin');
}   

// Check the userid is valid.
if (!empty($userid) && !company::check_valid_user($companyid, $userid, $departmentid)) {
    print_error('invaliduserdepartment', 'block_iomad_company_management');
}

?>
<script type="text/javascript">
Y.on('change', submit_form, '#licenseidselector');
 function submit_form() {
     var nValue = Y.one('#licenseidselector').get('value');
    $.ajax({
        type: "GET",
        url: "<?php echo $CFG->wwwroot; ?>/blocks/iomad_company_admin/js/company_user_create_form.ajax.php?licenseid="+nValue,
        datatype: "HTML",
        success: function(response){
            $("#licensecourseselector").html(response);
        }
    });
    $.ajax({
        type: "GET",
        url: "<?php echo $CFG->wwwroot; ?>/blocks/iomad_company_admin/js/company_user_create_form-license.ajax.php?licenseid="+nValue,
        datatype: "HTML",
        success: function(response){
            $("#licensedetails").html(response);
        }
    });
    $.ajax({
        type: "GET",
        url: "<?php echo $CFG->wwwroot; ?>/blocks/iomad_company_admin/js/company_user_create_form-license-courses.ajax.php?licenseid="+nValue,
        datatype: "HTML",
        success: function(response){
            $("#licensecoursescontainer")[0].style.display = response;
        }
    });
 }
</script>
<?php

// Display a message if user is created..
if ($createdok) {
    echo html_writer::start_tag('div', array('class' => "alert alert-success"));
    echo get_string('usercreated', 'block_iomad_company_admin');
    echo "</div>";
}
// Display the form.
$mform->display();

echo $output->footer();

