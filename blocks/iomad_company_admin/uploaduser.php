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

// Bulk user registration script from a comma separated file.
// Returns list of users with their user ids.

/**
 * @package   block_iomad_company_admin
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/csvlib.class.php');
require_once($CFG->dirroot.'/user/profile/lib.php');
require_once('uploaduser_form.php');

$iid         = optional_param('iid', '', PARAM_INT);
$previewrows = optional_param('previewrows', 10, PARAM_INT);
$readcount   = optional_param('readcount', 0, PARAM_INT);
$uploadtype  = optional_param('uutype', 0, PARAM_INT);
$licenseid = optional_param('licenseid', 0, PARAM_INT);
$userdepartment = optional_param('userdepartment', 0, PARAM_INT);

if (!empty($licenseid)) {
    $SESSION->chosenlicenseid = $licenseid;
}

$montharray = array('jan' => '01',
                    'feb' => '02',
                    'mar' => 03,
                    'apr' => 04,
                    'may' => '05',
                    'jun' => '06',
                    'jul' => '07',
                    'aug' => '08',
                    'sep' => '09',
                    'oct' => '10',
                    'nov' => '11',
                    'dec' => '12');

$context = context_system::instance();
require_login();

define('UU_ADDNEW', 0);
define('UU_ADDINC', 1);
define('UU_ADD_UPDATE', 2);
define('UU_UPDATE', 3);

$choices = array(UU_ADDNEW    => get_string('uuoptype_addnew', 'tool_uploaduser'),
                 UU_ADDINC    => get_string('uuoptype_addinc', 'tool_uploaduser'),
                 UU_ADD_UPDATE => get_string('uuoptype_addupdate', 'tool_uploaduser'),
                 UU_UPDATE     => get_string('uuoptype_update', 'tool_uploaduser'));

@set_time_limit(3600); // 1 hour should be enough.
raise_memory_limit(MEMORY_EXTRA);

require_login();
iomad::require_capability('block/iomad_company_admin:user_upload', context_system::instance());

// Correct the navbar .
// Set the name for the page.
$linktext = get_string('uploadusers', 'tool_uploaduser');
// Set the url.
$linkurl = new moodle_url('/blocks/iomad_company_admin/uploaduser.php');

// Print the page header.
$PAGE->set_context($context);
$PAGE->set_url($linkurl);
$PAGE->set_pagelayout('base');
$PAGE->set_title($linktext);
// Set the page heading.
$PAGE->set_heading($linktext);

$PAGE->requires->jquery();

// Javascript for fancy select.
// Parameter is name of proper select form element.
$PAGE->requires->js_call_amd('block_iomad_company_admin/department_select', 'init', array('userdepartment', '', $userdepartment));

// get output renderer
$output = $PAGE->get_renderer('block_iomad_company_admin');

// Set the companyid
$companyid = iomad::get_my_companyid($context);

$companyshortname = '';
if ($companyid ) {
    $company = new company($companyid);
    $companyshortname = $company->get_shortname();
}
require_login(null, false); // Adds to $PAGE, creates $output.

$systemcontext = context_system::instance();

$struserrenamed             = get_string('userrenamed', 'tool_uploaduser');
$strusernotrenamedexists    = get_string('usernotrenamedexists', 'error');
$strusernotrenamedmissing   = get_string('usernotrenamedmissing', 'error');
$strusernotrenamedoff       = get_string('usernotrenamedoff', 'error');
$strusernotrenamedadmin     = get_string('usernotrenamedadmin', 'error');

$struserupdated             = get_string('useraccountupdated', 'tool_uploaduser');
$strusernotupdated          = get_string('usernotupdatederror', 'error');
$strusernotupdatednotexists = get_string('usernotupdatednotexists', 'error');
$strusernotupdatedadmin     = get_string('usernotupdatedadmin', 'error');

$struseradded               = get_string('newuser');
$strusernotadded            = get_string('usernotaddedregistered', 'error');
$strusernotaddederror       = get_string('usernotaddederror', 'error');

$struserdeleted             = get_string('userdeleted', 'tool_uploaduser');
$strusernotdeletederror     = get_string('usernotdeletederror', 'error');
$strusernotdeletedmissing   = get_string('usernotdeletedmissing', 'error');
$strusernotdeletedoff       = get_string('usernotdeletedoff', 'error');
$strusernotdeletedadmin     = get_string('usernotdeletedadmin', 'error');

$strcannotassignrole        = get_string('cannotassignrole', 'error');
$strduplicateusername       = get_string('duplicateusername', 'error');

$struserauthunsupported     = get_string('userauthunsupported', 'error');
$stremailduplicate          = get_string('useremailduplicate', 'error');

$strinvalidpasswordpolicy   = get_string('invalidpasswordpolicy', 'error');
$errorstr                   = get_string('error');
$strcantmanageuser          = get_string('invaliduser', 'block_iomad_company_admin');

$returnurl = $CFG->wwwroot."/blocks/iomad_company_admin/uploaduser.php";
$bulknurl  = $CFG->wwwroot.'/'.$CFG->admin.'/user/user_bulk.php';

$today = time();
$today = make_timestamp(date('Y', $today), date('m', $today), date('d', $today), 0, 0, 0);

// Array of all valid fields for validation.
$stdfields = array('id', 'firstname', 'lastname', 'username', 'email',
        'city', 'country', 'lang', 'auth', 'timezone', 'mailformat',
        'maildisplay', 'maildigest', 'htmleditor', 'ajax', 'autosubscribe',
        'mnethostid', 'institution', 'department', 'idnumber', 'skype',
        'msn', 'aim', 'yahoo', 'icq', 'phone1', 'phone2', 'address',
        'url', 'description', 'descriptionformat', 'oldusername', 'deleted',
        'password', 'temppassword', 'suspended');

$prffields = array();

if ($proffields = $DB->get_records('user_info_field')) {
    foreach ($proffields as $proffield) {
        $prffields[] = 'profile_field_'.$proffield->shortname;
    }
    unset($proffields);
}
if (empty($iid)) {
    $mform = new admin_uploaduser_form1();

    if ($formdata = $mform->get_data()) {
        $iid = csv_import_reader::get_new_iid('uploaduser');
        $cir = new csv_import_reader($iid, 'uploaduser');

        $content = $mform->get_file_content('userfile');
        $optype = $formdata->uutype;
        $readcount = $cir->load_csv_content($content,
                                            $formdata->encoding,
                                            $formdata->delimiter_name,
                                            'validate_user_upload_columns');
        if (!$columns = $cir->get_columns()) {
           print_error('cannotreadtmpfile', 'error', $returnurl);
        }

        unset($content);

        // Keep track of new users.
        $newusercount = 0;
        $cir->init();
        while ($line = $cir->next()) {
            $usercheck = new stdClass();

            // Add fields to user object.
            foreach ($line as $key => $value) {
                if ($value !== '') {
                    $key = $columns[$key];
                    $usercheck->$key = $value;
                } else {
                    $usercheck->{$columns[$key]} = '';
                }
            }
            if (!$DB->get_record('user', array('username' =>  $usercheck->username)) && $optype != 3) {
                $newusercount++;
            } else if (($optype == 2 || $optype ==3) && isset($usercheck->suspended)
                       && $usercheck->suspended == 0
                       && $DB->get_record('user', array('username' =>  $usercheck->username, 'suspended' => 1))) {
                $newusercount++;
            }
        }

        // Check if the company has gone over the user quota.
        if (!$company->check_usercount($newusercount)) {
            $maxusers = $company->get('maxusers');
            print_error('maxuserswarningplural', 'block_iomad_company_admin', $returnurl, $maxusers);
        }

        if ($readcount === false) {
            // TODO: need more detailed error info.
            print_error('csvloaderror', '', $returnurl);
        } else if ($readcount == 0) {
            print_error('csvemptyfile', 'error', $returnurl);
        }
        // Continue to form2.

    } else {
        $PAGE->set_heading($linktext);
        echo $output->header();

        $mform->display();
        echo $output->footer();
        die;
    }
} else {
    $cir = new csv_import_reader($iid, 'uploaduser');
}

if (!$columns = $cir->get_columns()) {
    print_error('cannotreadtmpfile', 'error', $returnurl);
}

$mform = new admin_uploaduser_form2(null, $columns);
// Get initial date from form1.
$mform->set_data(array('iid' => $iid,
                       'previewrows' => $previewrows,
                       'readcount' => $readcount,
                       'uutypelabel' => $choices[$uploadtype],
                       'uutype' => $uploadtype,
                       'companyid' => $companyid));

// If a file has been uploaded, then process it.
if ($mform->is_cancelled()) {
    $cir->cleanup(true);
    redirect($returnurl);

} else if ($formdata = $mform->get_data()) {
    if (!empty($formdata->submitbutton)) {
        // Another cancelled check.
        if (!empty($formdata->cancel) && $formdata->cancel == 'Cancel') {
            $cir->cleanup(true);
            redirect($returnurl);
        }

        // Deal with program license.
        if (!empty($formdata->licenseid)) {
            if ($DB->get_record('companylicense', array('id' => $formdata->licenseid, 'program' => 1))) {
                // This is a program of courses.  Set them!
                $formdata->licensecourses = $DB->get_records_sql_menu("SELECT c.id, clc.courseid FROM {companylicense_courses} clc
                                                                       JOIN {course} c ON (clc.courseid = c.id
                                                                       AND clc.licenseid = :licenseid)",
                                                                       array('licenseid' => $formdata->licenseid));
            } else {
                $formdata->licensecourses = optional_param_array('licensecourses', array(), PARAM_INT);
            }
        } else {
            $formdata->licensecourses = array();
        }

        // Print the header.
        $PAGE->set_heading(get_string('uploadusersresult', 'tool_uploaduser'));
        echo $output->header();

        $optype = $formdata->uutype;

        $createpasswords   = (!empty($formdata->uupasswordnew) and $optype != UU_UPDATE);
        $updatepasswords   = (!empty($formdata->uupasswordold)  and $optype != UU_ADDNEW and $optype != UU_ADDINC);
        $allowrenames      = (!empty($formdata->uuallowrenames) and $optype != UU_ADDNEW and $optype != UU_ADDINC);
        $allowdeletes      = (!empty($formdata->uuallowdeletes) and $optype != UU_ADDNEW and $optype != UU_ADDINC);
        $updatetype        = isset($formdata->uuupdatetype) ? $formdata->uuupdatetype : 0;
        $bulk              = $formdata->uubulk;
        $noemailduplicates = $formdata->uunoemailduplicates;

        // Verification moved to two places: after upload and into form2.
        $usersnew     = 0;
        $usersupdated = 0;
        $userserrors  = 0;
        $deletes      = 0;
        $deleteerrors = 0;
        $renames      = 0;
        $renameerrors = 0;
        $usersskipped = 0;
        $weakpasswords = 0;
        $numlicenses = 0;
        $numlicenseerrors = 0;
        $erroredusers = array();

        // Caches.
        $ccache       = array(); // Course cache - do not fetch all courses here, we  will not probably use them all anyway!
        $rolecache    = uu_allowed_roles_cache(); // Roles lookup cache.
        $manualcache  = array(); // Cache of used manual enrol plugins in each course.

        $allowedauths   = uu_allowed_auths();
        $allowedauths   = array_keys($allowedauths);
        $availableauths = get_plugin_list('auth');
        $availableauths = array_keys($availableauths);

        // We use only manual enrol plugin here, if it is disabled no enrol is done.
        if (enrol_is_enabled('manual')) {
            $manual = enrol_get_plugin('manual');
        } else {
            $manual = null;
        }

        // Clear bulk selection.
        if ($bulk) {
            $SESSION->bulk_users = array();
        }

        // Init csv import helper.
        $cir->init();
        $linenum = 1; // Column header is first line.

        // Init upload progress tracker.
        $upt = new uu_progress_tracker();
        $upt->init(); // Start table.

        while ($line = $cir->next()) {
            $upt->flush();
            $linenum++;
            $errornum = 1;
            $passeddepartment = false;
            $defaultdepartment = 0;

            $upt->track('line', $linenum);

            $forcechangepassword = false;

            $user = new stdClass();
            // By default, use the local mnet id (this may be changed in the file).
            $user->mnethostid = $CFG->mnet_localhost_id;
            // Add fields to user object.
            foreach ($line as $key => $value) {
                if ($value !== '') {
                    $key = $columns[$key];
                    $user->$key = $value;
                    // Did we get oassed a deparment value?
                    if (strpos($key, 'department') !== false) {
                        if (!empty($value)) {
                            $passeddepartment = true;
                        }
                    }
                    if (in_array($key, $upt->columns)) {
                        $upt->track($key, $value);
                    }
                } else {
                    $user->{$columns[$key]} = '';
                }
            }

            if (empty($user->username) && !empty($user->email)) {
                // No username given, try to find an existing user via the email address.
                if ($perfexistinguser = $DB->get_record('user', array('email' => $user->email, 'mnethostid' => $user->mnethostid))) {
                    $user->username = $perfexistinguser->username;
                } else {
                    // No existing user matches, generate a new username.
                    $user->username = company_user::generate_username($user->email);
                }
                $upt->track('username', $user->username);
            }

            // Get username, first/last name now - we need them in templates!!
            if ($optype == UU_UPDATE) {
                // When updating only username is required.
                if (!isset($user->username)) {
                    $upt->track('status', get_string('missingfield', 'error', 'username'), 'error');
                    $upt->track('username', $errorstr, 'error');
                    $line[] = get_string('missingfield', 'error', 'username');
                    $userserrors++;
                    $errornum++;
                    $erroredusers[] = $line;
                    continue;
                }

            } else {
                $error = false;
                // When all other ops need firstname and lastname.
                if (!isset($user->firstname) or $user->firstname === '') {
                    $upt->track('status', get_string('missingfield', 'error', 'firstname'), 'error');
                    $upt->track('firstname', $errorstr, 'error');
                    $line[] = get_string('missingfield', 'error', 'firstname');
                    $errornum++;
                    $userserrors++;
                    $error = true;
                }
                if (!isset($user->lastname) or $user->lastname === '') {
                    $upt->track('status', get_string('missingfield', 'error', 'lastname'), 'error');
                    $upt->track('lastname', $errorstr, 'error');
                    $line[] = get_string('missingfield', 'error', 'lastname');
                    $errornum++;
                    $userserrors++;
                    $error = true;
                }
                if ($error) {
                    $userserrors++;
                    $erroredusers[] = $line;
                    continue;
                }
                // We require username too - we might use template for it though.
                if (!isset($user->username)) {
                    if (!isset($formdata->username) or $formdata->username === '') {
                        $upt->track('status', get_string('missingfield', 'error', 'username'), 'error');
                        $upt->track('username', $errorstr, 'error');
                        $line[] = get_string('missingfield', 'error', 'username');
                        $errornum++;
                        $userserrors++;
                        $erroredusers[] = $line;
                        continue;
                    } else {
                        $user->username = process_template($formdata->username, $user);
                        $upt->track('username', $user->username);
                    }
                }
            }

            // Normalize username.
            $user->username = clean_param($user->username, PARAM_USERNAME);

            if (empty($user->username)) {
                $upt->track('status', get_string('missingfield', 'error', 'username'), 'error');
                $upt->track('username', $errorstr, 'error');
                $line[] = get_string('missingfield', 'error', 'username');
                $errornum++;
                $userserrors++;
                $erroredusers[] = $line;
                continue;
            }

            if ($existinguser = $DB->get_record('user', array('username' => $user->username, 'mnethostid' => $user->mnethostid))) {
                $upt->track('id', $existinguser->id, 'normal', false);
            }

            // Find out in username incrementing required.
            if ($existinguser and $optype == UU_ADDINC) {
                $oldusername = $user->username;
                $user->username = increment_username($user->username, $user->mnethostid);
                $upt->track('username', '', 'normal', false); // Clear previous.
                $upt->track('username', $oldusername.'-->'.$user->username, 'info');
                $existinguser = false;
            }

            // Add default values for remaining fields.
            foreach ($stdfields as $field) {
                if (isset($user->$field)) {
                    continue;
                }
                // All validation moved to form2.
                if (isset($formdata->$field)) {
                    // Process templates.
                    $user->$field = process_template($formdata->$field, $user);
                }
            }
            foreach ($prffields as $field) {
                if (isset($user->$field)) {
                    if (preg_match('/(?P<day>\d{2})-(?P<month>[a-zA-Z]{3})-(?P<year>\d{4})/', $user->$field, $datearray)) {
                        $month = $montharray[$datearray[2]];
                        $unixtime = mktime (0, 0, 0, $month, $datearray['day'], $datearray['year']);
                        $user->$field = $unixtime;
                    }
                    continue;
                }
                if (isset($formdata->$field)) {
                    // Process templates.
                    // Check if is in a dd-Mon-yyy format.
                    if (preg_match('/(?P<day>\d{2})-(?P<month>[a-zA-Z]{3})-(?P<year>\d{4})/', $formdata->$field, $datearray)) {
                        $month = $montharray[$datearray[2]];
                        $unixtime = mktime (0, 0, 0, $month, $datearray['day'], $datearray['year']);
                        $user->$field = $unixtime;
                    } else {
                        $user->$field = process_template($formdata->$field, $user);
                    }
                }
            }

            // Delete user.
            if (!empty($user->deleted)) {
                if (!$allowdeletes) {
                    $usersskipped++;
                    $upt->track('status', $strusernotdeletedoff, 'warning');
                    continue;
                }
                if ($existinguser) {
                    if (is_siteadmin($existinguser->id)) {
                        $upt->track('status', $strusernotdeletedadmin, 'error');
                        $deleteerrors++;
                        continue;
                    }
                    if (!company::check_can_manage($existinguser->id)) {
                        $upt->track('status', $strcantmanageuser, 'error');
                        $deleteerrors++;
                        continue;
                    }
                    if (delete_user($existinguser)) {
                        $upt->track('status', $struserdeleted);
                        $deletes++;
                    } else {
                        $upt->track('status', $strusernotdeletederror, 'error');
                        $deleteerrors++;
                    }
                } else {
                    $upt->track('status', $strusernotdeletedmissing, 'error');
                    $deleteerrors++;
                }
                continue;
            }
            // We do not need the deleted flag anymore.
            unset($user->deleted);

            // Renaming requested?
            if (!empty($user->oldusername) ) {
                $oldusername = core_text::strtolower($user->oldusername);
                if (!$allowrenames) {
                    $usersskipped++;
                    $upt->track('status', $strusernotrenamedoff, 'warning');
                    continue;
                }

                if ($existinguser) {
                    $upt->track('status', $strusernotrenamedexists, 'error');
                    $renameerrors++;
                    continue;
                }

                if ($olduser = $DB->get_record('user', array('username' => $oldusername, 'mnethostid' => $user->mnethostid))) {
                    $upt->track('id', $olduser->id, 'normal', false);
                    if (is_siteadmin($olduser->id)) {
                        $upt->track('status', $strusernotrenamedadmin, 'error');
                        $renameerrors++;
                        continue;
                    }
                    if (!company::check_can_manage($olduser->id)) {
                        $upt->track('status', $strcantmanageuser, 'error');
                        $renameerrors++;
                        continue;
                    }
                    $DB->set_field('user', 'username', $user->username, array('id' => $olduser->id));
                    $upt->track('username', '', 'normal', false); // Clear previous.
                    $upt->track('username', $oldusername.'-->'.$user->username, 'info');
                    $upt->track('status', $struserrenamed);
                    $renames++;
                } else {
                    $upt->track('status', $strusernotrenamedmissing, 'error');
                    $renameerrors++;
                    continue;
                }
                $existinguser = $olduser;
                $existinguser->username = $user->username;
            }

            // Can we process with update or insert?
            $skip = false;
            switch ($optype) {
                case UU_ADDNEW:
                    if ($existinguser) {
                        $usersskipped++;
                        $upt->track('status', $strusernotadded, 'warning');
                        $skip = true;
                    }
                    break;

                case UU_ADDINC:
                    if ($existinguser) {
                        // This should not happen!
                        $upt->track('status', $strusernotaddederror, 'error');
                        $userserrors++;
                        continue 2;
                    }
                    break;

                case UU_ADD_UPDATE:
                    break;

                case UU_UPDATE:
                    if (!$existinguser) {
                        $usersskipped++;
                        $upt->track('status', $strusernotupdatednotexists, 'warning');
                        $skip = true;
                    }
                    break;
            }

            if ($skip) {
                continue;
            }

            if ($existinguser) {
                $user->id = $existinguser->id;

                if (is_siteadmin($user->id)) {
                    $upt->track('status', $strusernotupdatedadmin, 'error');
                    $line[] = $strusernotupdatedadmin;
                    $errornum++;
                    $userserrors++;
                    $erroredusers[] = $line;
                    continue;
                }

                if (!company::check_can_manage($user->id)) {
                    $upt->track('status', $strcantmanageuser, 'error');
                    $line[] = $strcantmanageuser;
                    $errornum++;
                    $userserrors++;
                    $erroredusers[] = $line;
                    continue;
                }

                if (!empty($updatetype)) {
                    $existinguser->timemodified = time();
                    if (empty($existinguser->timecreated)) {
                        if (empty($existinguser->firstaccess)) {
                            $existinguser->timecreated = time();
                        } else {
                            $existinguser->timecreated = $existinguser->firstaccess;
                        }
                    }

                    // Load existing profile data.
                    profile_load_data($existinguser);

                    $allowed = array();
                    if ($updatetype == 1) {
                        $allowed = $columns;
                    } else if ($updatetype == 2 or $updatetype == 3) {
                        $allowed = array_merge($stdfields, $prffields);
                    }
                    foreach ($allowed as $column) {
                        $temppasswordhandler = '';
                        if ($column == 'username') {
                            continue;
                        }
                        if ((property_exists($existinguser, $column) and property_exists($user, $column))
                             or in_array($column, $prffields)) {
                            if ($updatetype == 3 && $existinguser->$column !== '') {
                                // Missing == non-empty only!
                                continue;
                            }
                            if ($existinguser->$column !== $user->$column) {
                                if ($column == 'email') {
                                    if ($DB->record_exists('user', array('email' => $user->email))) {
                                        if ($noemailduplicates) {
                                            $upt->track('email', $stremailduplicate, 'error');
                                            $upt->track('status', $strusernotupdated, 'error');
                                            $line[] = $stremailduplicate;
                                            $errornum++;
                                            $userserrors++;
                                            $erroredusers[] = $line;
                                            continue 2;
                                        } else {
                                            $upt->track('email', $stremailduplicate, 'warning');
                                        }
                                    }
                                }

                                if ($column == 'password') {
                                    $temppasswordhandler = $existinguser->password;
                                }

                                if ($column == 'auth') {
                                    if (isset($user->auth) && empty($user->auth)) {
                                        $user->auth = 'manual';
                                    }

                                    $existinguserauth = get_auth_plugin($existinguser->auth);
                                    $existingisinternalauth = $existinguserauth->is_internal();

                                    $userauth = get_auth_plugin($user->auth);
                                    $isinternalauth = $userauth->is_internal();

                                    if ($isinternalauth === $existingisinternalauth) {
                                        if ($updatepasswords) {
                                            if (empty($user->password)) {
                                                $forcechangepassword = true;
                                            }
                                        }
                                    } else if ($isinternalauth) {
                                        $existinguser->password = '';
                                        $forcechangepassword = true;
                                    }
                                }

                                if ($column != 'suspended') {
                                    $upt->track($column, '', 'normal', false); // Clear previous.
                                }
                                if ($column != 'password' && in_array($column, $upt->columns)) {
                                    $upt->track($column, $existinguser->$column.'-->'.$user->$column, 'info');
                                }
                                $existinguser->$column = $user->$column;

                                if (!isset($user->auth) && !$updatepasswords) {
                                    $existinguser->password = $temppasswordhandler;
                                }
                            }
                        }
                    }
                    // Do not update record if new auth plugin does not exist!
                    if (!in_array($existinguser->auth, $availableauths)) {
                        $upt->track('auth', get_string('userautherror', 'error', $existinguser->auth), 'error');
                        $upt->track('status', $strusernotupdated, 'error');
                        $line[] = get_string('userautherror', 'error', $existinguser->auth);
                        $errornum++;
                        $userserrors++;
                        $erroredusers[] = $line;
                        continue;
                    } else if (!in_array($existinguser->auth, $allowedauths)) {
                        $upt->track('auth', $struserauthunsupported, 'warning');
                    }

                    $auth = get_auth_plugin($existinguser->auth);
                    $isinternalauth = $auth->is_internal();

                    if ($isinternalauth && $updatepasswords && !check_password_policy($user->password, $errmsg)) {
                        $upt->track('password', get_string('internalauthpassworderror', 'error', $existinguser->password), 'error');
                        $upt->track('status', $strusernotupdated, 'error');
                        $line[] = get_string('internalauthpassworderror', 'error', $existinguser->password);
                        $errornum++;
                        $userserrors++;
                        $erroredusers[] = $line;
                        continue;
                    } else {
                        $forcechangepassword = true;
                    }

                    if (!$isinternalauth) {
                        $existinguser->password = 'not cached';
                        $upt->track('password', 'not cached');
                        $forcechangepassword = false;
                    } else if ($updatepasswords) {
                        $existinguser->password = hash_internal_user_password($existinguser->password);
                    } else {
                        $existinguser->password = $temppasswordhandler;
                    }

                    $DB->update_record('user', $existinguser);

                    // Remove user preference.

                    if (get_user_preferences('create_password', false, $existinguser)) {
                        unset_user_preference('create_password', $existinguser);
                    }
                    if (get_user_preferences('auth_forcepasswordchange', false, $existinguser)) {
                        unset_user_preference('auth_forcepasswordchange', $existinguser);
                    }

                    if ($isinternalauth && $updatepasswords) {
                        if (empty($existinguser->password)) {
                            set_user_preference('create_password', 1, $existinguser->id);
                            set_user_preference('auth_forcepasswordchange', 1, $existinguser->id);
                            $upt->track('password', get_string('new'));
                        } else if ($forcechangepassword) {
                            set_user_preference('auth_forcepasswordchange', 1, $existinguser->id);
                        }
                    }
                    $upt->track('status', $struserupdated);
                    $usersupdated++;
                    // Save custom profile fields data from csv file.
                    profile_save_data($existinguser);

                    \core\event\user_updated::create_from_userid($existinguser->id)->trigger();
     
                    // Is the company department valid?
                    if ($passeddepartment && !empty($existinguser->department)) {
                        if (!$department = $DB->get_record('department', array('company' => $company->id,
                                                                               'shortname' => $existinguser->department))) {
                            $upt->track('department', get_string('invaliddepartment', 'block_iomad_company_admin'), 'error');
                            $upt->track('status', $strusernotaddederror, 'error');
                            $line[] = get_string('invaliddepartment', 'block_iomad_company_admin');
                            $errornum++;
                            $userserrors++;
                            $erroredusers[] = $line;
                            continue;
                        }
                        // Make sure the user can manage this department.
                        if (!company::can_manage_department($department->id)) {
                            $upt->track('department', get_string('invaliddepartment', 'block_iomad_company_admin'), 'error');
                            $upt->track('status', $strusernotaddederror, 'error');
                            $line[] = get_string('invaliddepartment', 'block_iomad_company_admin');
                            $errornum++;
                            $userserrors++;
                            $erroredusers[] = $line;
                            continue;
                        }

                        if ($userdep = $DB->get_record('company_users', array('userid' => $existinguser->id, 'companyid' => $company->id))) {
                            $userdep->departmentid = $department->id;
                            $DB->update_record('company_users', $userdep);
                        } else {
                            // Add the user to the company
                            $company->assign_user_to_company($existinguser->id, $department->id);
                        }
                    }
                }

                if ($bulk == 2 or $bulk == 3) {
                    if (!in_array($user->id, $SESSION->bulk_users)) {
                        $SESSION->bulk_users[] = $user->id;
                    }
                }

            } else {
                // Save the user to the database.
                $user->confirmed = 1;
                $user->timemodified = time();
                $user->timecreated = time();

                if (isset($user->auth) && empty($user->auth)) {
                    $user->auth = 'manual';
                }
                $auth = get_auth_plugin($user->auth);
                $isinternalauth = $auth->is_internal();

                if (!$createpasswords && $isinternalauth) {
                    if (empty($user->password)) {
                        $upt->track('password', get_string('missingfield', 'error', 'password'), 'error');
                        $upt->track('status', $strusernotaddederror, 'error');
                        $line[] = get_string('missingfield', 'error', 'password');
                        $errornum++;
                        $userserrors++;
                        $erroredusers[] = $line;
                        continue;
                    } else if ($forcechangepassword) {
                        $upt->track('password', $strinvalidpasswordpolicy);
                        $upt->track('status', $strusernotaddederror, 'error');
                        $line[] = $strinvalidpasswordpolicy;
                        $errornum++;
                        $userserrors++;
                        $erroredusers[] = $line;
                        continue;
                    }
                }

                // Do not insert record if new auth plguin does not exist!
                if (isset($user->auth)) {
                    if (!in_array($user->auth, $availableauths)) {
                        $upt->track('auth', get_string('userautherror', 'error', $user->auth), 'error');
                        $upt->track('status', $strusernotaddederror, 'error');
                        $line[] = get_string('userautherror', 'error', $user->auth);
                        $errornum++;
                        $userserrors++;
                        $erroredusers[] = $line;
                        continue;
                    } else if (!in_array($user->auth, $allowedauths)) {
                        $upt->track('auth', $struserauthunsupported, 'warning');
                    }
                }

                if ($DB->record_exists('user', array('email' => $user->email))) {
                    if ($noemailduplicates) {
                        $upt->track('email', $stremailduplicate, 'error');
                        $upt->track('status', $strusernotaddederror, 'error');
                        $line[] = $stremailduplicate;
                        $errornum++;
                        $userserrors++;
                        $erroredusers[] = $line;
                        continue;
                    } else {
                        $upt->track('email', $stremailduplicate, 'warning');
                    }
                }
                if (!$isinternalauth) {
                    $user->password = 'not cached';
                    $upt->track('password', 'not cached');
                }

                // Merge user with company user defaults.
                if (!empty($companyid)) {
                    $company = new company($companyid);
                    $user->companyid = $companyid;
                }

                // Is the company department valid?
                if (!empty($user->department)) {
                    if (!$department = $DB->get_record('department', array('company' => $company->id,
                                                                           'shortname' => $user->department))) {
                        $upt->track('department', get_string('invaliddepartment', 'block_iomad_company_admin'), 'error');
                        $upt->track('status', $strusernotaddederror, 'error');
                        $line[] = get_string('invaliddepartment', 'block_iomad_company_admin');
                        $errornum++;
                        $userserrors++;
                        $erroredusers[] = $line;
                        continue;
                    }

                    // Make sure the user can manage this department.
                    if (!company::can_manage_department($department->id)) {
                        $upt->track('department', get_string('invaliddepartment', 'block_iomad_company_admin'), 'error');
                        $upt->track('status', $strusernotaddederror, 'error');
                        $line[] = get_string('invaliddepartment', 'block_iomad_company_admin');
                        $errornum++;
                        $userserrors++;
                        $erroredusers[] = $line;
                        continue;
                    }
                } else {
                    if (!$department = $DB->get_record('department', array('company' => $company->id,
                                                                           'id' => $formdata->deptid))) {
                        $upt->track('department', get_string('invaliddepartment', 'block_iomad_company_admin'), 'error');
                        $upt->track('status', $strusernotaddederror, 'error');
                        $line[] = get_string('invaliddepartment', 'block_iomad_company_admin');
                        $errornum++;
                        $userserrors++;
                        $erroredusers[] = $line;
                        continue;
                    }
                    // Make sure the user can manage this department.
                    if (!company::can_manage_department($department->id)) {
                        $upt->track('department', get_string('invaliddepartment', 'block_iomad_company_admin'), 'error');
                        $upt->track('status', $strusernotaddederror, 'error');
                        $line[] = get_string('invaliddepartment', 'block_iomad_company_admin');
                        $errornum++;
                        $userserrors++;
                        $erroredusers[] = $line;
                        continue;
                    }
                }
                $user->departmentid = $department->id;
                if (!empty($user->password)) {
                    $user->newpassword = $user->password;
                } else {
                    $user->newpassword = null;
                }

                unset($user->password);
                $user->sendnewpasswordemails = $formdata->sendnewpasswordemails;
                $user->due = $today;
                if (empty($user->newpassword) || $formdata->sendnewpasswordemails) {
                    $user->preference_auth_forcepasswordchange = true;
                } else {
                    $user->preference_auth_forcepasswordchange = false;
                }
                $user->id = company_user::create($user);

                // Are we being passed company departments?
                if ($passeddepartment) {
                    // Stash the default in case we need to remove them from it later.
                    $defaultdepartmentid = $department->id;
                }
                $info = ': ' . $user->username .' (ID = ' . $user->id . ')';
                $upt->track('status', $struseradded);
                $upt->track('id', $user->id, 'normal', false);
                $usersnew++;
            }

            // Find course enrolments, groups, roles/types and enrol periods.
            foreach ($columns as $column) {
                if (preg_match('/^course\d+$/', $column)) {
                    $i = substr($column, 6);

                    if (empty($user->{'course'.$i})) {
                        continue;
                    }
                    $shortname = $user->{'course'.$i};
                    if (!array_key_exists($shortname, $ccache)) {
                        if (!$course = $DB->get_record('course', array('shortname' => $shortname), 'id, shortname')) {
                            $upt->track('enrolments', get_string('unknowncourse', 'error', $shortname), 'error');
                            continue;
                        }
                        $ccache[$shortname] = $course;
                        $ccache[$shortname]->groups = null;
                    }

                    // find role
                    $roleid = false;
                    if (!empty($user->{'role'.$i})) {
                        $rolename = $user->{'role'.$i};
                        if (array_key_exists($rolename, $rolecache)) {
                            $roleid = $rolecache[$rolename]->id;
                        } else {
                            $upt->track('enrolments', get_string('unknownrole', 'error', s($rolename)), 'error');
                            continue;
                        }
                    }

                    company_user::enrol($user, [$ccache[$shortname]->id], $companyid , $roleid);
                    $coursecontext = context_course::instance($ccache[$shortname]->id);

                    // find group to add to
                    if (!empty($user->{'group'.$i})) {
                        // make sure user is enrolled into course before adding into groups
                        if (!is_enrolled($coursecontext, $user->id)) {
                            $upt->track('enrolments', get_string('addedtogroupnotenrolled', '', $user->{'group'.$i}), 'error');
                            continue;
                        }
                        //build group cache
                        if (is_null($ccache[$shortname]->groups)) {
                            $ccache[$shortname]->groups = array();
                            if ($groups = groups_get_all_groups($course->id)) {
                                foreach ($groups as $gid=>$group) {
                                    $ccache[$shortname]->groups[$gid] = new stdClass();
                                    $ccache[$shortname]->groups[$gid]->id   = $gid;
                                    $ccache[$shortname]->groups[$gid]->name = $group->name;
                                    if (!is_numeric($group->name)) { // only non-numeric names are supported!!!
                                        $ccache[$shortname]->groups[$group->name] = new stdClass();
                                        $ccache[$shortname]->groups[$group->name]->id   = $gid;
                                        $ccache[$shortname]->groups[$group->name]->name = $group->name;
                                    }
                                }
                            }
                        }
                        // group exists?
                        $addgroup = trim($user->{'group'.$i});
                         if (!array_key_exists($addgroup, $ccache[$shortname]->groups)) {
                            // if group doesn't exist,  create it
                            $newgroupdata = new stdClass();
                            $newgroupdata->name = $addgroup;
                            $newgroupdata->courseid = $ccache[$shortname]->id;
                            $newgroupdata->description = '';
                            $gid = groups_create_group($newgroupdata);
                            if ($gid){
                                $ccache[$shortname]->groups[$addgroup] = new stdClass();
                                $ccache[$shortname]->groups[$addgroup]->id   = $gid;
                                $ccache[$shortname]->groups[$addgroup]->name = $newgroupdata->name;
                            } else {
                                $upt->track('enrolments', get_string('unknowngroup', 'error', s($addgroup)), 'error');
                                continue;
                            }
                        }
                        $gid   = $ccache[$shortname]->groups[$addgroup]->id;
                        $gname = $ccache[$shortname]->groups[$addgroup]->name;

                        try {
                            if (groups_add_member($gid, $user->id)) {
                                $upt->track('enrolments', get_string('addedtogroup', '', s($gname)));
                            }  else {
                                $upt->track('enrolments', get_string('addedtogroupnot', '', s($gname)), 'error');
                            }
                        } catch (moodle_exception $e) {
                            $upt->track('enrolments', get_string('addedtogroupnot', '', s($gname)), 'error');
                            continue;
                        }
                    }
                }

                if (!empty($formdata->selectedcourses)) {
                    // add the user to the courses selected in the upload form.
                    $courseids = array();
                    foreach ($formdata->selectedcourses as $selectedcourse) {
                        $courseids[] = $selectedcourse->id;
                    }
                    company_user::enrol($user, $courseids, $companyid);
                }
                if (preg_match('/^department\d+$/', $column)) {
                    $i = substr($column, 10);

                    if (empty($user->{'department'.$i})) {
                        continue;
                    }
                    $shortname = $user->{'department'.$i};
                    if (!$department = $DB->get_record('department', array('company' => $company->id,
                                                                           'shortname' => $shortname))) {
                        $upt->track('department'.$i, get_string('invaliddepartment', 'block_iomad_company_admin'), 'error');
                        $upt->track('status', $strusernotaddederror, 'error');
                        $line[] = get_string('invaliddepartment', 'block_iomad_company_admin');
                        $errornum++;
                        $userserrors++;
                        $erroredusers[] = $line;
                        continue;
                    }
                    // Make sure the user can manage this department.
                    if (!company::can_manage_department($department->id)) {
                        $upt->track('department'.$i, get_string('invaliddepartment', 'block_iomad_company_admin'), 'error');
                        $upt->track('status', $strusernotaddederror, 'error');
                        $line[] = get_string('invaliddepartment', 'block_iomad_company_admin');
                        $errornum++;
                        $userserrors++;
                        $erroredusers[] = $line;
                        continue;
                    }

                    // Since we got a valid department, remove the user from any default one.  Typically top-level.
                    if ($department->id != $defaultdepartmentid &&
                        !empty($defaultdepartmentid)) {

                        // Remove the user from the default department.
                        $DB->delete_records('company_users', array('userid' => $user->id, 'companyid' => $company->id, 'departmentid' => $defaultdepartmentid));

                        // Only want to do this once.
                        $defaultdepartmentid = 0;
                    } else {
                        // Default is the first we were passed.  No longer required.
                        $defaultdepartmentid = 0;
                    }

                    // Add the user to this department.
                    $company->assign_user_to_company($user->id, $department->id);
                }
            }

            // Enrol user into courses that were selected on the form.
            if (isset($formdata->selectedcourses) ) {
                company_user::enrol($user, array_keys($formdata->selectedcourses) );
            }

            // Assign any licenses.
            if (!empty($formdata->licenseid)) {
                $timestamp = time();
                $licenserecord = (array) $DB->get_record('companylicense', array('id' => $formdata->licenseid));
                $count = $licenserecord['used'];
                $numberoflicenses = $licenserecord['allocation'];

                foreach ($formdata->licensecourses as $licensecourse) {
                    if ($count >= $numberoflicenses) {
                        // Set the used amount.
                        $licenserecord['used'] = $count;
                        $DB->update_record('companylicense', $licenserecord);
                        $numlicenseerrors++;
                        continue;
                    }
                    if ($DB->get_record_sql("SELECT id FROM {companylicense_users}
                                             WHERE licenseid = :licenseid
                                             AND licensecourseid = :licensecourseid
                                             AND userid = :userid
                                             AND (isusing = 0 OR timecompleted IS NULL)",
                                             array('userid' => $user->id, 'licenseid' => $formdata->licenseid,
                                                  'licensecourseid' => $licensecourse))) {
                        // Already assigned skip and error.
                        $numlicenseerrors++;
                        continue;
                    }
                    $allow = true;
                    $numlicenses++;

                    $count++;
                    $issuedate = time();
                    $userlicid = $DB->insert_record('companylicense_users',
                                        array('userid' => $user->id,
                                              'licenseid' => $formdata->licenseid,
                                              'licensecourseid' => $licensecourse,
                                              'issuedate' => $issuedate));

                    // Create an event.
                    $eventother = array('licenseid' => $formdata->licenseid,
                                        'issuedate' => $issuedate,
                                        'duedate' => $timestamp);
                    $event = \block_iomad_company_admin\event\user_license_assigned::create(array('context' => context_course::instance($licensecourse),
                                                                                                  'objectid' => $userlicid,
                                                                                                  'courseid' => $licensecourse,
                                                                                                  'userid' => $user->id,
                                                                                                  'other' => $eventother));
                    $event->trigger();
                }
            }


            // If user was set to have password generated, generate it now, so that it can be downloaded.
            company_user::generate_temporary_password($user, $formdata->sendnewpasswordemails);
        }

        if (!empty($licenserecord['program'])) {
            $numlicenses = $numlicenses / count($formdata->licensecourses);
            $numlicenseerrors = $numlicenseerrors / count($formdata->licensecourses);
        }

        $upt->flush();
        $upt->close(); // Close table.

        $cir->close();
        $cir->cleanup(true);

        // Deal with any erroring users.
        if (!empty($erroredusers)) {
            echo get_string('erroredusers', 'block_iomad_company_admin');
            $erroredtable = new html_table();
            foreach ($erroredusers as $erroreduser) {
                $erroredtable->data[] = $erroreduser;
            }
            echo html_writer::table($erroredtable);
        }

        echo $output->box_start('boxwidthnarrow boxaligncenter generalbox', 'uploadresults');
        echo '<p>';
        if ($optype != UU_UPDATE) {
            echo get_string('userscreated', 'tool_uploaduser').': '.$usersnew.'<br />';
        }
        if ($optype == UU_UPDATE or $optype == UU_ADD_UPDATE) {
            echo get_string('usersupdated', 'tool_uploaduser').': '.$usersupdated.'<br />';
        }
        if ($allowdeletes) {
            echo get_string('usersdeleted', 'tool_uploaduser').': '.$deletes.'<br />';
            echo get_string('deleteerrors', 'tool_uploaduser').': '.$deleteerrors.'<br />';
        }
        if ($allowrenames) {
            echo get_string('usersrenamed', 'tool_uploaduser').': '.$renames.'<br />';
            echo get_string('renameerrors', 'tool_uploaduser').': '.$renameerrors.'<br />';
        }
        if ($usersskipped) {
            echo get_string('usersskipped', 'tool_uploaduser').': '.$usersskipped.'<br />';
        }
        echo get_string('usersweakpassword', 'tool_uploaduser').': '.$weakpasswords.'<br />';
        echo get_string('errors', 'tool_uploaduser').': '.$userserrors.'</p>';
        echo get_string('licensecount', 'block_iomad_company_admin').': '.$numlicenses.'<br />';
        echo get_string('licenseerrors', 'block_iomad_company_admin').': '.$numlicenseerrors.'</p>';
        echo $output->box_end();

        if ($bulk) {
            echo $output->continue_button($bulknurl);
        } else {
            echo $output->continue_button($returnurl);
        }
        echo $output->footer();
        unset($SESSION->chosenlicenseid);
        die;
    }
}

// Print the header.
$PAGE->set_heading(get_string('uploaduserspreview', 'tool_uploaduser'));
echo $output->header();

// Print the form.
$cir->init();
$availableauths = get_plugin_list('auth');
$availableauths = array_keys($availableauths);
$contents = array();
while ($fields = $cir->next()) {
    $errormsg = array();
    $rowcols = array();
    foreach ($fields as $key => $field) {
        $rowcols[$columns[$key]] = $field;
    }

    if ((!isset($rowcols['profile_field_company']) || empty($rowcols['profile_field_company']))
        && !company_user::is_company_user() && $companyid == 0) {
        $errormsg['profile_field_company'] = get_string('profile_field_company_not_set', 'block_iomad_company_admin');
    }
    if (isset($rowcols['profile_field_company']) && !company_user::can_see_company($rowcols['profile_field_company'])) {
        $errormsg['profile_field_company'] = get_string('invalid_company', 'block_iomad_company_admin');
    }
    if ($companyid > 0 && isset($rowcols['profile_field_company']) && !empty($rowcols['profile_field_company'])
        && $rowcols['profile_field_company'] != $companyshortname ) {
        $errormsg['profile_field_company'] = get_string('profile_field_company_not_empty_does_not_match_selected',
                                                        'block_iomad_company_admin');
    }

    if ((!isset($rowcols['username']) || empty($rowcols['username'])) && isset($rowcols['email']) && !empty($rowcols['email'])) {
        // No username given, try to find an existing user via the email address.
        if ($perfexistinguser = $DB->get_record('user', array('email' => $rowcols['email']))) {
            $rowcols['username'] = $perfexistinguser->username;
        } else {
            // No existing user matches, generate a new username.
            $rowcols['username'] = company_user::generate_username($rowcols['email']);
        }
    }

    $usernameexist = $DB->record_exists('user', array('username' => $rowcols['username']));
    $emailexist    = $DB->record_exists('user', array('email' => $rowcols['email']));
    $cleanusername = clean_param($rowcols['username'], PARAM_USERNAME);
    $validusername = strcmp($rowcols['username'], $cleanusername);
    $validemail = validate_email($rowcols['email']);

    if ($validusername != 0 || !$validemail) {
        if ($validusername != 0) {
            $errormsg['username'] = get_string('invalidusernameupload');
        }
        if (!$validemail) {
            $errormsg['email'] = get_string('invalidemail');
        }
    }

    // Check password column.
    if (array_key_exists('auth', $rowcols)) {
        if (isset($rowcols['auth']) && empty($rowcols['auth'])) {
                $rowcols['auth'] = 'manual';
        }
        $rowauth = get_auth_plugin($rowcols['auth']);
        $rowisinternalauth = $rowauth->is_internal();
        if (!$rowisinternalauth) {
            if (array_key_exists('password', $rowcols) && !empty($rowcols['password'])) {
                $errormsg['password'] = get_string('externalauthpassworderror', 'error');
            }
        }

        if (!in_array($rowcols['auth'], $availableauths)) {
            $errormsg['auth'] = get_string('userautherror', 'error');
        }
    }

    if (empty($optype) ) {
        $optype = $uploadtype;
    }

    switch($optype) {
        case UU_ADDNEW:
            if ($usernameexist || $emailexist ) {
                $rowcols['action'] = 'skipped';
            } else {
                $rowcols['action'] = 'create';
            }
            break;

        case UU_ADDINC:
            if (!$usernameexist && !$emailexist) {
                $rowcols['action'] = 'create';
            } else if ($usernameexist && !$emailexist) {
                $rowcols['action'] = 'addcountertousername';
                $rowcols['username'] = increment_username($rowcols['username'], $CFG->mnet_localhost_id);
            } else {
                $rowcols['action'] = 'skipped';
            }
            break;

        case UU_ADD_UPDATE:
            $oldusernameexist = '';
            if (isset($rowcols['oldusername'])) {
                $oldusernameexist = $DB->record_exists('user', array('username' => $rowcols['oldusername']));
            }
            if ($usernameexist || $emailexist || $oldusernameexist ) {
                $rowcols['action'] = 'update';
            } else {
                $rowcols['action'] = 'create';
            }
            break;

        case UU_UPDATE:
             $oldusernameexist = '';
            if (isset($rowcols['oldusername'])) {
                $oldusernameexist = $DB->record_exists('user', array('username' => $rowcols['oldusername']));
            }

            if ($usernameexist || $emailexist || !empty($oldusernameexist)) {
                $rowcols['action'] = 'update';
            } else {
                $rowcols['action'] = "skipped";
            }
            break;
    }

    if (!empty($errormsg)) {
        $rowcols['error'] = array();
        $rowcols['error'] = $errormsg;
    }
    if ($rowcols['action'] != 'skipped') {
        $contents[] = $rowcols;
    }
}
$cir->close();

// Get heading.
$headings = array();
foreach ($contents as $content) {
    foreach ($content as $key => $value) {
        if (!in_array($key, $headings)) {
            $headings[] = $key;
        }
    }
}

$table = new html_table();
$table->id = "uupreview";
$table->attributes['class'] = 'generaltable';
$table->tablealign = 'center';
$table->summary = get_string('uploaduserspreview', 'tool_uploaduser');
$table->head = array();
$table->data = array();

// Print heading.
foreach ($headings as $heading) {
    $table->head[] = s($heading);
}

$haserror = false;
$countcontent = 0;
if (in_array('error', $headings)) {
    // Print error.
    $haserror = true;

    foreach ($contents as $content) {
        if (array_key_exists('error', $content)) {
            $rows = new html_table_row();
            foreach ($content as $key => $value) {
                $cells = new html_table_cell();
                $errclass = '';
                if (array_key_exists($key, $content['error'])) {
                    $errclass = 'uuerror';
                }
                if ($key == 'error') {
                    $value = join('<br />', $content['error']);
                }
                if ($key == 'action') {
                    $value = get_string($content[$key]);
                }
                $cells->text = $value;
                $cells->attributes['class'] = $errclass;
                $rows->cells[] = $cells;
            }
            $countcontent++;
            $table->data[] = $rows;
        }
    }
    $mform = new admin_uploaduser_form3();
    $mform->set_data(array('uutype' => $uploadtype));
} else if (empty($contents)) {
    $mform = new admin_uploaduser_form3();
    $mform->set_data(array('uutype' => $uploadtype));
} else {
    // Print content.
    foreach ($contents as $content) {
        $rows = new html_table_row();
        if ($countcontent >= $previewrows) {
            foreach ($content as $con) {
                $cells = new html_table_cell();
                $cells->text = '...';
            }
            $rows->cells[] = $cells;
            $table->data[] = $rows;
            break;
        }
        foreach ($headings as $heading) {
            $cells = new html_table_cell();
            if (array_key_exists($heading, $content)) {
                if ($heading == 'action') {
                    $content[$heading] = get_string($content[$heading]);
                }
                $cells->text = $content[$heading];
            } else {
                $cells->text = '';
            }
            $rows->cells[] = $cells;
        }
        $table->data[] = $rows;
        $countcontent++;
    }
}
echo html_writer::tag('div', html_writer::table($table), array('class' => 'flexible-wrap'));

if ($haserror) {
    echo $output->container(get_string('useruploadtype', 'moodle', $choices[$uploadtype]), 'block_iomad_company_admin');
    echo $output->container(get_string('uploadinvalidpreprocessedcount', 'moodle', $countcontent), 'block_iomad_company_admin');
    echo $output->container(get_string('invalidusername', 'moodle'), 'block_iomad_company_admin');
    echo $output->container(get_string('uploadfilecontainerror', 'block_iomad_company_admin'), 'block_iomad_company_admin');
} else if (empty($contents)) {
    echo $output->container(get_string('uupreprocessedcount', 'block_iomad_company_admin', $countcontent),
                            'block_iomad_company_admin');
    echo $output->container(get_string('uploadfilecontentsnovaliddata', 'block_iomad_company_admin'));
} else {
    echo $output->container(get_string('uupreprocessedcount', 'block_iomad_company_admin', $countcontent),
                            'block_iomad_company_admin');
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


$mform->display();
echo $output->footer();
die;

/*
* Utility functions and classes
*/

class uu_progress_tracker {
    public $_row;
    public $columns = array('status',
                            'line',
                            'id',
                            'username',
                            'firstname',
                            'lastname',
                            'email',
                            'password',
                            'auth',
                            'enrolments',
                            'deleted',
                            'department');

    public function __construct() {
    }

    public function init() {
        $ci = 0;
        echo '<table id="uuresults" class="generaltable boxaligncenter flexible-wrap" summary="'.
               get_string('uploadusersresult', 'tool_uploaduser').'">';
        echo '<tr class="heading r0">';
        echo '<th class="header c'.$ci++.'" scope="col">'.get_string('status').'</th>';
        echo '<th class="header c'.$ci++.'" scope="col">'.get_string('uucsvline', 'tool_uploaduser').'</th>';
        echo '<th class="header c'.$ci++.'" scope="col">ID</th>';
        echo '<th class="header c'.$ci++.'" scope="col">'.get_string('username').'</th>';
        echo '<th class="header c'.$ci++.'" scope="col">'.get_string('firstname').'</th>';
        echo '<th class="header c'.$ci++.'" scope="col">'.get_string('lastname').'</th>';
        echo '<th class="header c'.$ci++.'" scope="col">'.get_string('email').'</th>';
        echo '<th class="header c'.$ci++.'" scope="col">'.get_string('password').'</th>';
        echo '<th class="header c'.$ci++.'" scope="col">'.get_string('authentication').'</th>';
        echo '<th class="header c'.$ci++.'" scope="col">'.get_string('enrolments', 'enrol').'</th>';
        echo '<th class="header c'.$ci++.'" scope="col">'.get_string('delete').'</th>';
        echo '<th class="header c'.$ci++.'" scope="col">'.get_string('department', 'block_iomad_company_admin').'</th>';
        echo '</tr>';
        $this->_row = null;
    }

    public function flush() {
        if (empty($this->_row) or empty($this->_row['line']['normal'])) {
            $this->_row = array();
            foreach ($this->columns as $col) {
                $this->_row[$col] = array('normal' => '', 'info' => '', 'warning' => '', 'error' => '');
            }
            return;
        }
        $ci = 0;
        $ri = 1;
        echo '<tr class="r'.$ri.'">';
        foreach ($this->_row as $key => $field) {
            foreach ($field as $type => $content) {
                if ($field[$type] !== '') {
                    if ($key == 'username' && $type == 'normal') {
                        $field[$type] = clean_param($field[$type], PARAM_USERNAME);
                    }
                    $field[$type] = '<span class="uu'.$type.'">'.$field[$type].'</span>';
                } else {
                    unset($field[$type]);
                }
            }
            echo '<td class="cell c'.$ci++.'">';
            if (!empty($field)) {
                echo implode('<br />', $field);
            } else {
                echo '&nbsp;';
            }
            echo '</td>';
        }
        echo '</tr>';
        foreach ($this->columns as $col) {
            $this->_row[$col] = array('normal' => '', 'info' => '', 'warning' => '', 'error' => '');
        }
    }

    public function track($col, $msg, $level= 'normal', $merge=true) {
        if (empty($this->_row)) {
            $this->flush(); // Init arrays.
        }
        if (!in_array($col, $this->columns)) {
            debugging('Incorrect column:'.$col);
            return;
        }
        if ($merge) {
            if ($this->_row[$col][$level] != '') {
                $this->_row[$col][$level] .= '<br />';
            }
            $this->_row[$col][$level] .= s($msg);
        } else {
            $this->_row[$col][$level] = s($msg);
        }
    }

    public function close() {
        echo '</table>';
    }
}

/**
 * Validation callback function - verified the column line of csv file.
 * Converts column names to lowercase too.
 */
function validate_user_upload_columns(&$columns) {
    global $stdfields, $prffields;

    if (count($columns) < 2) {
        return get_string('csvfewcolumns', 'error');
    }
    // Test columns.
    $processed = array();
    foreach ($columns as $key => $unused) {
        $field = $columns[$key];
        if (!in_array($field, $stdfields) && !in_array($field, $prffields) &&
            !preg_match('/^course\d+$/', $field) && !preg_match('/^group\d+$/', $field) &&
            !preg_match('/^department\d+$/', $field) &&
            !preg_match('/^type\d+$/', $field) && !preg_match('/^role\d+$/', $field) &&
            !preg_match('/^enrolperiod\d+$/', $field)) {
            // If not a standard field and not an enrolment field, then we have an error!
            return get_string('invalidfieldname', 'error', $field);
        }
        if (in_array($field, $processed)) {
            return get_string('csvcolumnduplicates', 'error');
        }
        $processed[] = $field;
    }
    return true;
}

/**
 * Increments username - increments trailing number or adds it if not present.
 * Varifies that the new username does not exist yet
 * @param string $username
 * @return incremented username which does not exist yet
 */
function increment_username($username, $mnethostid) {
    global $DB;

    if (!preg_match_all('/(.*?)([0-9]+)$/', $username, $matches)) {
        $username = $username.'2';
    } else {
        $username = $matches[1][0].($matches[2][0] + 1);
    }

    if ($DB->record_exists('user', array('username' => $username, 'mnethostid' => $mnethostid))) {
        return increment_username($username, $mnethostid);
    } else {
        return $username;
    }
}

/**
 * Check if default field contains templates and apply them.
 * @param string template - potential tempalte string
 * @param object user object- we need username, firstname and lastname
 * @return string field value
 */
function process_template($template, $user) {
    if (strpos($template, '%') === false) {
        return $template;
    }

    // Very very ugly hack!
    global $template_globals;
    $template_globals = new stdClass();
    $template_globals->username  = isset($user->username)  ? $user->username  : '';
    $template_globals->firstname = isset($user->firstname) ? $user->firstname : '';
    $template_globals->lastname  = isset($user->lastname)  ? $user->lastname  : '';

    $result = preg_replace_callback('/(?<!%)%([+-~])?(\d)*([flu])/', 'process_template_callback', $template);

    $template_globals = null;

    if (is_null($result)) {
        return $template; // Error during regex processing??
    } else {
        return $result;
    }
}

/**
 * Internal callback function.
 */
function process_template_callback($block) {
    global $template_globals;
    $textlib = core_text::get_instance();
    $repl = $block[0];

    switch ($block[3]) {
        case 'u':
            $repl = $template_globals->username;
        break;
        case 'f':
            $repl = $template_globals->firstname;
        break;
        case 'l':
            $repl = $template_globals->lastname;
        break;
    }
    switch ($block[1]) {
        case '+':
            $repl = core_text::strtoupper($repl);
        break;
        case '-':
            $repl = core_text::strtolower($repl);
        break;
        case '~':
            $repl = core_text::strtotitle($repl);
        break;
    }
    if (!empty($block[2])) {
        $repl = core_text::substr($repl, 0 , $block[2]);
    }

    return $repl;
}

/**
 * Returns list of auth plugins that are enabled and known to work.
 */
function uu_allowed_auths() {
    global $CFG;

    // Only following plugins are guaranteed to work properly.
    // TODO: add support for more plugins in 2.0!
    $whitelist = array('manual', 'nologin', 'none', 'email');
    $plugins = get_enabled_auth_plugins();
    $choices = array();
    foreach ($plugins as $plugin) {
        $choices[$plugin] = get_string('pluginname', "auth_{$plugin}");
    }

    return $choices;
}

/**
 * Returns list of roles that are assignable in courses
 */
function uu_allowed_roles() {
    // Let's cheat a bit, frontpage is guaranteed to exist and has the same list of roles ;-).
    $roles = get_assignable_roles(context_course::instance(SITEID), ROLENAME_ORIGINALANDSHORT);
    return array_reverse($roles, true);
}

function uu_allowed_roles_cache() {
    $allowedroles = get_assignable_roles(context_course::instance(SITEID), ROLENAME_SHORT);
    foreach ($allowedroles as $rid => $rname) {
        $rolecache[$rid] = new stdClass();
        $rolecache[$rid]->id   = $rid;
        $rolecache[$rid]->name = $rname;
        if (!is_numeric($rname)) { // Only non-numeric shortnames are supported!!!
            $rolecache[$rname] = new stdClass();
            $rolecache[$rname]->id   = $rid;
            $rolecache[$rname]->name = $rname;
        }
    }
    if (!empty($rolecache)) {
        return $rolecache;
    } else {
        return array();
    }
}
