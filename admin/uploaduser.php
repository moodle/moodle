<?php

/// Bulk user registration script from a comma separated file
/// Returns list of users with their user ids

require('../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/csvlib.class.php');
require_once($CFG->dirroot.'/user/profile/lib.php');
require_once('uploaduser_form.php');

$iid         = optional_param('iid', '', PARAM_INT);
$previewrows = optional_param('previewrows', 10, PARAM_INT);
$readcount   = optional_param('readcount', 0, PARAM_INT);
$uploadtype  = optional_param('uutype', 0, PARAM_INT);

define('UU_ADDNEW', 0);
define('UU_ADDINC', 1);
define('UU_ADD_UPDATE', 2);
define('UU_UPDATE', 3);

$choices = array(UU_ADDNEW    => get_string('uuoptype_addnew', 'admin'),
                 UU_ADDINC    => get_string('uuoptype_addinc', 'admin'),
                 UU_ADD_UPDATE => get_string('uuoptype_addupdate', 'admin'),
                 UU_UPDATE     => get_string('uuoptype_update', 'admin'));

@set_time_limit(3600); // 1 hour should be enough
@raise_memory_limit('256M');
if (function_exists('apache_child_terminate')) {
    // if we are running from Apache, give httpd a hint that
    // it can recycle the process after it's done. Apache's
    // memory management is truly awful but we can help it.
    @apache_child_terminate();
}

require_login();
admin_externalpage_setup('uploadusers');
require_capability('moodle/site:uploadusers', get_context_instance(CONTEXT_SYSTEM));

$textlib = textlib_get_instance();
$systemcontext = get_context_instance(CONTEXT_SYSTEM);

$struserrenamed             = get_string('userrenamed', 'admin');
$strusernotrenamedexists    = get_string('usernotrenamedexists', 'error');
$strusernotrenamedmissing   = get_string('usernotrenamedmissing', 'error');
$strusernotrenamedoff       = get_string('usernotrenamedoff', 'error');
$strusernotrenamedadmin     = get_string('usernotrenamedadmin', 'error');

$struserupdated             = get_string('useraccountupdated', 'admin');
$strusernotupdated          = get_string('usernotupdatederror', 'error');
$strusernotupdatednotexists = get_string('usernotupdatednotexists', 'error');
$strusernotupdatedadmin     = get_string('usernotupdatedadmin', 'error');

$struseradded               = get_string('newuser');
$strusernotadded            = get_string('usernotaddedregistered', 'error');
$strusernotaddederror       = get_string('usernotaddederror', 'error');

$struserdeleted             = get_string('userdeleted', 'admin');
$strusernotdeletederror     = get_string('usernotdeletederror', 'error');
$strusernotdeletedmissing   = get_string('usernotdeletedmissing', 'error');
$strusernotdeletedoff       = get_string('usernotdeletedoff', 'error');
$strusernotdeletedadmin     = get_string('usernotdeletedadmin', 'error');

$strcannotassignrole        = get_string('cannotassignrole', 'error');
$strduplicateusername       = get_string('duplicateusername', 'error');

$struserauthunsupported     = get_string('userauthunsupported', 'error');
$stremailduplicate          = get_string('useremailduplicate', 'error');

$errorstr                   = get_string('error');

$returnurl = $CFG->wwwroot.'/'.$CFG->admin.'/uploaduser.php';
$bulknurl  = $CFG->wwwroot.'/'.$CFG->admin.'/user/user_bulk.php';

$today = time();
$today = make_timestamp(date('Y', $today), date('m', $today), date('d', $today), 0, 0, 0);

// array of all valid fields for validation
$STD_FIELDS = array('id', 'firstname', 'lastname', 'username', 'email',
        'city', 'country', 'lang', 'auth', 'timezone', 'mailformat',
        'maildisplay', 'maildigest', 'htmleditor', 'ajax', 'autosubscribe',
        'mnethostid', 'institution', 'department', 'idnumber', 'skype',
        'msn', 'aim', 'yahoo', 'icq', 'phone1', 'phone2', 'address',
        'url', 'description', 'descriptionformat', 'oldusername', 'emailstop', 'deleted',
        'password');

$PRF_FIELDS = array();

if ($prof_fields = $DB->get_records('user_info_field')) {
    foreach ($prof_fields as $prof_field) {
        $PRF_FIELDS[] = 'profile_field_'.$prof_field->shortname;
    }
    unset($prof_fields);
}

if (empty($iid)) {
    $mform = new admin_uploaduser_form1();

    if ($formdata = $mform->get_data()) {
        $iid = csv_import_reader::get_new_iid('uploaduser');
        $cir = new csv_import_reader($iid, 'uploaduser');

        $content = $mform->get_file_content('userfile');
        $optype = $formdata->uutype;
        $readcount = $cir->load_csv_content($content, $formdata->encoding, $formdata->delimiter_name, 'validate_user_upload_columns');
        unset($content);

        if ($readcount === false) {
            //TODO: need more detailed error info
            print_error('csvloaderror', '', $returnurl);
        } else if ($readcount == 0) {
            print_error('csvemptyfile', 'error', $returnurl);
        }
        // continue to form2

    } else {
        echo $OUTPUT->header();

        echo $OUTPUT->heading_with_help(get_string('uploadusers', 'admin'), 'uploadusers', 'admin');

        $mform->display();
        echo $OUTPUT->footer();
        die;
    }
} else {
    $cir = new csv_import_reader($iid, 'uploaduser');
}

if (!$columns = $cir->get_columns()) {
    print_error('cannotreadtmpfile', 'error', $returnurl);
}
$mform = new admin_uploaduser_form2(null, $columns);
// get initial date from form1
$mform->set_data(array('iid'=>$iid, 'previewrows'=>$previewrows, 'readcount'=>$readcount, 'uutypelabel'=>$choices[$uploadtype], 'uutype'=>$uploadtype));

// If a file has been uploaded, then process it
if ($formdata = $mform->is_cancelled()) {
    $cir->cleanup(true);
    redirect($returnurl);

} else if ($formdata = $mform->get_data()) {
    // Print the header
    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('uploadusersresult', 'admin'));

    $optype = $formdata->uutype;

    $createpasswords   = (!empty($formdata->uupasswordnew) and $optype != UU_UPDATE);
    $updatepasswords   = (!empty($formdata->uupasswordold)  and $optype != UU_ADDNEW and $optype != UU_ADDINC);
    $allowrenames      = (!empty($formdata->uuallowrenames) and $optype != UU_ADDNEW and $optype != UU_ADDINC);
    $allowdeletes      = (!empty($formdata->uuallowdeletes) and $optype != UU_ADDNEW and $optype != UU_ADDINC);
    $updatetype        = isset($formdata->uuupdatetype) ? $formdata->uuupdatetype : 0;
    $bulk              = $formdata->uubulk;
    $noemailduplicates = $formdata->uunoemailduplicates;

    // verification moved to two places: after upload and into form2
    $usersnew     = 0;
    $usersupdated = 0;
    $userserrors  = 0;
    $deletes      = 0;
    $deleteerrors = 0;
    $renames      = 0;
    $renameerrors = 0;
    $usersskipped = 0;
    $weakpasswords = 0;

    // caches
    $ccache       = array(); // course cache - do not fetch all courses here, we  will not probably use them all anyway!
    $rolecache    = uu_allowed_roles_cache(); // roles lookup cache
    $manualcacche = array(); // cache of used manual enrol plugins in each course

    $allowedauths   = uu_allowed_auths();
    $allowedauths   = array_keys($allowedauths);
    $availableauths = get_plugin_list('auth');
    $availableauths = array_keys($availableauths);

    // we use only manual enrol plugin here, if it is disabled no enrol is done
    if (enrol_is_enabled('manual')) {
        $manual = enrol_get_plugin('manual');
    } else {
        $manual = NULL;
    }

    // clear bulk selection
    if ($bulk) {
        $SESSION->bulk_users = array();
    }

    // init csv import helper
    $cir->init();
    $linenum = 1; //column header is first line

    // init upload progress tracker
    $upt = new uu_progress_tracker();
    $upt->init(); // start table

    while ($line = $cir->next()) {
        $upt->flush();
        $linenum++;

        $upt->track('line', $linenum);

        $forcechangepassword = false;

        $user = new object();
        // by default, use the local mnet id (this may be changed in the file)
        $user->mnethostid = $CFG->mnet_localhost_id;
        // add fields to user object
        foreach ($line as $key => $value) {
            if ($value !== '') {
                $key = $columns[$key];
                // password is special field
                if ($key == 'password') {
                    if ($value !== '') {
                        $user->password = hash_internal_user_password($value);
                        if (!empty($CFG->passwordpolicy) and !check_password_policy($value, $errmsg)) {
                            $forcechangepassword = true;
                            $weakpasswords++;
                        }
                    }
                } else {
                    $user->$key = $value;
                    if (in_array($key, $upt->columns)) {
                        $upt->track($key, $value);
                    }
                }
            }
        }

        // get username, first/last name now - we need them in templates!!
        if ($optype == UU_UPDATE) {
            // when updating only username is required
            if (!isset($user->username)) {
                $upt->track('status', get_string('missingfield', 'error', 'username'), 'error');
                $upt->track('username', $errorstr, 'error');
                $userserrors++;
                continue;
            }

        } else {
            $error = false;
            // when all other ops need firstname and lastname
            if (!isset($user->firstname) or $user->firstname === '') {
                $upt->track('status', get_string('missingfield', 'error', 'firstname'), 'error');
                $upt->track('firstname', $errorstr, 'error');
                $error = true;
            }
            if (!isset($user->lastname) or $user->lastname === '') {
                $upt->track('status', get_string('missingfield', 'error', 'lastname'), 'error');
                $upt->track('lastname', $errorstr, 'error');
                $error = true;
            }
            if ($error) {
                $userserrors++;
                continue;
            }
            // we require username too - we might use template for it though
            if (!isset($user->username)) {
                if (!isset($formdata->username) or $formdata->username === '') {
                    $upt->track('status', get_string('missingfield', 'error', 'username'), 'error');
                    $upt->track('username', $errorstr, 'error');
                    $userserrors++;
                    continue;
                } else {
                    $user->username = process_template($formdata->username, $user);
                    $upt->track('username', $user->username);
                }
            }
        }

        // normalize username
        $user->username = clean_param($user->username, PARAM_USERNAME);

        if (empty($user->username)) {
            $upt->track('status', get_string('missingfield', 'error', 'username'), 'error');
            $upt->track('username', $errorstr, 'error');
            $userserrors++;
            continue;
        }

        if ($existinguser = $DB->get_record('user', array('username'=>$user->username, 'mnethostid'=>$user->mnethostid))) {
            $upt->track('id', $existinguser->id, 'normal', false);
        }

        // find out in username incrementing required
        if ($existinguser and $optype == UU_ADDINC) {
            $oldusername = $user->username;
            $user->username = increment_username($user->username, $user->mnethostid);
            $upt->track('username', '', 'normal', false); // clear previous
            $upt->track('username', $oldusername.'-->'.$user->username, 'info');
            $existinguser = false;
        }

        // add default values for remaining fields
        foreach ($STD_FIELDS as $field) {
            if (isset($user->$field)) {
                continue;
            }
            // all validation moved to form2
            if (isset($formdata->$field)) {
                // process templates
                $user->$field = process_template($formdata->$field, $user);
            }
        }
        foreach ($PRF_FIELDS as $field) {
            if (isset($user->$field)) {
                continue;
            }
            if (isset($formdata->$field)) {
                // process templates
                $user->$field = process_template($formdata->$field, $user);
            }
        }

        // delete user
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
        // we do not need the deleted flag anymore
        unset($user->deleted);

        // renaming requested?
        if (!empty($user->oldusername) ) {
            $oldusername = $textlib->strtolower($user->oldusername);
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

            if ($olduser = $DB->get_record('user', array('username'=>$oldusername, 'mnethostid'=>$user->mnethostid))) {
                $upt->track('id', $olduser->id, 'normal', false);
                if (is_siteadmin($olduser->id)) {
                    $upt->track('status', $strusernotrenamedadmin, 'error');
                    $renameerrors++;
                    continue;
                }
                $DB->set_field('user', 'username', $user->username, array('id'=>$olduser->id));
                $upt->track('username', '', 'normal', false); // clear previous
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

        // can we process with update or insert?
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
                    //this should not happen!
                    $upt->track('status', $strusernotaddederror, 'error');
                    $userserrors++;
                    continue;
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
                $userserrors++;
                continue;
            }

            if (!$updatetype) {
                // no updates of existing data at all
            } else {
                $existinguser->timemodified = time();
                if (empty($existinguser->timecreated)) {
                    if (empty($existinguser->firstaccess)) {
                        $existinguser->timecreated = time();
                    } else {
                        $existinguser->timecreated = $existinguser->firstaccess;
                    }
                }

                //load existing profile data
                profile_load_data($existinguser);

                $allowed = array();
                if ($updatetype == 1) {
                    $allowed = $columns;
                } else if ($updatetype == 2 or $updatetype == 3) {
                    $allowed = array_merge($STD_FIELDS, $PRF_FIELDS);
                }
                foreach ($allowed as $column) {
                    if ($column == 'username') {
                        continue;
                    }
                    if ($column == 'password') {
                        if (!$updatepasswords or $updatetype == 3) {
                            continue;
                        } else if (!empty($user->password)) {
                            $upt->track('password', get_string('updated'));
                            if ($forcechangepassword) {
                                set_user_preference('auth_forcepasswordchange', 1, $existinguser->id);
                            }
                        }
                    }
                    if ((array_key_exists($column, $existinguser) and array_key_exists($column, $user)) or in_array($column, $PRF_FIELDS)) {
                        if ($updatetype == 3 and $existinguser->$column !== '') {
                            //missing == non-empty only
                            continue;
                        }
                        if ($existinguser->$column !== $user->$column) {
                            if ($column == 'email') {
                                if ($DB->record_exists('user', array('email'=>$user->email))) {
                                    if ($noemailduplicates) {
                                        $upt->track('email', $stremailduplicate, 'error');
                                        $upt->track('status', $strusernotupdated, 'error');
                                        $userserrors++;
                                        continue 2;
                                    } else {
                                        $upt->track('email', $stremailduplicate, 'warning');
                                    }
                                }
                            }
                            if ($column != 'password' and in_array($column, $upt->columns)) {
                                $upt->track($column, '', 'normal', false); // clear previous
                                $upt->track($column, $existinguser->$column.'-->'.$user->$column, 'info');
                            }
                            $existinguser->$column = $user->$column;
                        }
                    }
                }

                // do not update record if new auth plguin does not exist!
                if (!in_array($existinguser->auth, $availableauths)) {
                    $upt->track('auth', get_string('userautherror', 'error', $existinguser->auth), 'error');
                    $upt->track('status', $strusernotupdated, 'error');
                    $userserrors++;
                    continue;
                } else if (!in_array($existinguser->auth, $allowedauths)) {
                    $upt->track('auth', $struserauthunsupported, 'warning');
                }

                $DB->update_record('user', $existinguser);
                $upt->track('status', $struserupdated);
                $usersupdated++;
                // save custom profile fields data from csv file
                profile_save_data($existinguser);
            }

            if ($bulk == 2 or $bulk == 3) {
                if (!in_array($user->id, $SESSION->bulk_users)) {
                    $SESSION->bulk_users[] = $user->id;
                }
            }

        } else {
            // save the user to the database
            $user->confirmed = 1;
            $user->timemodified = time();
            $user->timecreated = time();

            if (!$createpasswords and empty($user->password)) {
                $upt->track('password', get_string('missingfield', 'error', 'password'), 'error');
                $upt->track('status', $strusernotaddederror, 'error');
                $userserrors++;
                continue;
            }

            // do not insert record if new auth plguin does not exist!
            if (isset($user->auth)) {
                if (!in_array($user->auth, $availableauths)) {
                    $upt->track('auth', get_string('userautherror', 'error', $user->auth), 'error');
                    $upt->track('status', $strusernotaddederror, 'error');
                    $userserrors++;
                    continue;
                } else if (!in_array($user->auth, $allowedauths)) {
                    $upt->track('auth', $struserauthunsupported, 'warning');
                }
            }

            if ($DB->record_exists('user', array('email'=>$user->email))) {
                if ($noemailduplicates) {
                    $upt->track('email', $stremailduplicate, 'error');
                    $upt->track('status', $strusernotaddederror, 'error');
                    $userserrors++;
                    continue;
                } else {
                    $upt->track('email', $stremailduplicate, 'warning');
                }
            }

            $user->id = $DB->insert_record('user', $user);
            $info = ': ' . $user->username .' (ID = ' . $user->id . ')';
            $upt->track('status', $struseradded);
            $upt->track('id', $user->id, 'normal', false);
            $usersnew++;
            if ($createpasswords and empty($user->password)) {
                // passwords will be created and sent out on cron
                set_user_preference('create_password', 1, $user->id);
                set_user_preference('auth_forcepasswordchange', 1, $user->id);
                $upt->track('password', get_string('new'));
            }
            if ($forcechangepassword) {
                set_user_preference('auth_forcepasswordchange', 1, $user->id);
            }

            // save custom profile fields data
            profile_save_data($user);

            // make sure user context exists
            get_context_instance(CONTEXT_USER, $user->id);

            if ($bulk == 1 or $bulk == 3) {
                if (!in_array($user->id, $SESSION->bulk_users)) {
                    $SESSION->bulk_users[] = $user->id;
                }
            }
        }

        // find course enrolments, groups, roles/types and enrol periods
        foreach ($columns as $column) {
            if (!preg_match('/^course\d+$/', $column)) {
                continue;
            }
            $i = substr($column, 6);

            if (empty($user->{'course'.$i})) {
                continue;
            }
            $shortname = $user->{'course'.$i};
            if (!array_key_exists($shortname, $ccache)) {
                if (!$course = $DB->get_record('course', array('shortname'=>$shortname), 'id, shortname')) {
                    $upt->track('enrolments', get_string('unknowncourse', 'error', $shortname), 'error');
                    continue;
                }
                $ccache[$shortname] = $course;
                $ccache[$shortname]->groups = null;
            }
            $courseid      = $ccache[$shortname]->id;
            $coursecontext = get_context_instance(CONTEXT_COURSE, $courseid);
            if (!isset($manualcache[$courseid])) {
                if ($instances = enrol_get_instances($courseid, false)) {
                    $manualcache[$courseid] = reset($instances);
                } else {
                    $manualcache[$courseid] = false;
                }
            }

            if ($manual and $manualcache[$courseid]) {

                // find role
                $rid = false;
                if (!empty($user->{'role'.$i})) {
                    $addrole = $user->{'role'.$i};
                    if (array_key_exists($addrole, $rolecache)) {
                        $rid = $rolecache[$addrole]->id;
                    } else {
                        $upt->track('enrolments', get_string('unknownrole', 'error', $addrole), 'error');
                        continue;
                    }

                } else if (!empty($user->{'type'.$i})) {
                    // if no role, then find "old" enrolment type
                    $addtype = $user->{'type'.$i};
                    if ($addtype < 1 or $addtype > 3) {
                        $upt->track('enrolments', $strerror.': typeN = 1|2|3', 'error');
                        continue;
                    } else if (empty($formdata->{'uulegacy'.$addtype})) {
                        continue;
                    } else {
                        $rid = $formdata->{'uulegacy'.$addtype};
                    }
                } else {
                    // no role specified, use the default from manual enrol plugin
                    $rid = $manualcache[$courseid]->roleid;
                }

                if ($rid) {
                    // find duration
                    $timeend   = 0;
                    if (!empty($user->{'enrolperiod'.$i})) {
                        $duration = (int)$user->{'enrolperiod'.$i} * 86400; // convert days to seconds
                        if ($duration > 0) { // sanity check
                            $timeend   = $today + $duration;
                        }
                    }

                    $manual->enrol_user($manualcache[$courseid], $user->id, $rid, $today, $timeend, true);

                    $a = new object();
                    $a->course = $shortname;
                    $a->role   = $rolecache[$rid]->name;
                    $upt->track('enrolments', get_string('enrolledincourserole', 'enrol_manual', $a));
                }
            }

            // find group to add to
            if (!empty($user->{'group'.$i})) {
                // make sure user is enrolled into course before adding into groups
                if (!is_enrolled($coursecontext, $user->id)) {
                    $upt->track('enrolments', get_string('addedtogroupnotenrolled', '', $gname), 'error');
                    continue;
                }
                //build group cache
                if (is_null($ccache[$shortname]->groups)) {
                    $ccache[$shortname]->groups = array();
                    if ($groups = groups_get_all_groups($courseid)) {
                        foreach ($groups as $gid=>$group) {
                            $ccache[$shortname]->groups[$gid] = new object();
                            $ccache[$shortname]->groups[$gid]->id   = $gid;
                            $ccache[$shortname]->groups[$gid]->name = $group->name;
                            if (!is_numeric($group->name)) { // only non-numeric names are supported!!!
                            $ccache[$shortname]->groups[$group->name] = new object();
                            $ccache[$shortname]->groups[$group->name]->id   = $gid;
                            $ccache[$shortname]->groups[$group->name]->name = $group->name;
                            }
                        }
                    }
                }
                // group exists?
                $addgroup = $user->{'group'.$i};
                if (!array_key_exists($addgroup, $ccache[$shortname]->groups)) {
                    // if group doesn't exist,  create it
                    $newgroupdata = new object();
                    $newgroupdata->name = $addgroup;
                    $newgroupdata->courseid = $ccache[$shortname]->id;
                    if ($ccache[$shortname]->groups[$addgroup]->id = groups_create_group($newgroupdata)){
                        $ccache[$shortname]->groups[$addgroup]->name = $newgroupdata->name;
                    } else {
                        $upt->track('enrolments', get_string('unknowngroup', 'error', $addgroup), 'error');
                        continue;
                    }
                }
                $gid   = $ccache[$shortname]->groups[$addgroup]->id;
                $gname = $ccache[$shortname]->groups[$addgroup]->name;

                try {
                    if (groups_add_member($gid, $user->id)) {
                        $upt->track('enrolments', get_string('addedtogroup', '', $gname));
                    }  else {
                        $upt->track('enrolments', get_string('addedtogroupnot', '', $gname), 'error');
                    }
                } catch (moodle_exception $e) {
                    $upt->track('enrolments', get_string('addedtogroupnot', '', $gname), 'error');
                    continue;
                }
            }
        }
    }
    $upt->flush();
    $upt->close(); // close table

    $cir->close();
    $cir->cleanup(true);

    echo $OUTPUT->box_start('boxwidthnarrow boxaligncenter generalbox', 'uploadresults');
    echo '<p>';
    if ($optype != UU_UPDATE) {
        echo get_string('userscreated', 'admin').': '.$usersnew.'<br />';
    }
    if ($optype == UU_UPDATE or $optype == UU_ADD_UPDATE) {
        echo get_string('usersupdated', 'admin').': '.$usersupdated.'<br />';
    }
    if ($allowdeletes) {
        echo get_string('usersdeleted', 'admin').': '.$deletes.'<br />';
        echo get_string('deleteerrors', 'admin').': '.$deleteerrors.'<br />';
    }
    if ($allowrenames) {
        echo get_string('usersrenamed', 'admin').': '.$renames.'<br />';
        echo get_string('renameerrors', 'admin').': '.$renameerrors.'<br />';
    }
    if ($usersskipped) {
        echo get_string('usersskipped', 'admin').': '.$usersskipped.'<br />';
    }
    echo get_string('usersweakpassword', 'admin').': '.$weakpasswords.'<br />';
    echo get_string('errors', 'admin').': '.$userserrors.'</p>';
    echo $OUTPUT->box_end();

    if ($bulk) {
        echo $OUTPUT->continue_button($bulknurl);
    } else {
        echo $OUTPUT->continue_button($returnurl);
    }
    echo $OUTPUT->footer();
    die;
}

// Print the header
echo $OUTPUT->header();

/// Print the form

echo $OUTPUT->heading(get_string('uploaduserspreview', 'admin'));

$cir->init();

$contents = array();
while ($fields = $cir->next()) {
    $errormsg = array();
    $rowcols = array();
    foreach($fields as $key =>$field) {
        $rowcols[$columns[$key]] = $field;
    }

    $usernameexist = $DB->record_exists('user', array('username'=>$rowcols['username']));
    $emailexist    = $DB->record_exists('user', array('email'=>$rowcols['email']));
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
                $oldusernameexist = $DB->record_exists('user', array('username'=>$rowcols['oldusername']));
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
                $oldusernameexist = $DB->record_exists('user', array('username'=>$rowcols['oldusername']));
            }

            if ($usernameexist || $emailexist || !empty($oldusernameexist)) {
                $rowcols['action'] = 'update';
            } else {
                $rowcols['action'] = "skipped";
            }
            break;
    }

    if (!empty($errormsg)){
        $rowcols['error'] = array();
        $rowcols['error'] = $errormsg;
    }
    if ($rowcols['action'] != 'skipped') {
        $contents[] = $rowcols;
    }
}
$cir->close();

//get heading
$headings = array();
foreach ($contents as $content) {
    foreach($content as $key => $value) {
        if (!in_array($key, $headings)) {
            $headings[] = $key;
        }
    }
}

$table = new html_table();
$table->id = "uupreview";
$table->attributes['class'] = 'generaltable';
$table->tablealign = 'center';
$table->summary = get_string('uploaduserspreview', 'admin');
$table->head = array();
$table->data = array();

//print heading
foreach ($headings as $heading) {
    $table->head[] = s($heading);
}

$haserror = false;
$countcontent = 0;
if (in_array('error', $headings)) {
    //print error
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
    $mform->set_data(array('uutype'=>$uploadtype));
} else if (empty($contents)) {
    $mform = new admin_uploaduser_form3();
    $mform->set_data(array('uutype'=>$uploadtype));
} else {
    //print content
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
            if(array_key_exists($heading, $content)) {
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
echo html_writer::table($table);

if ($haserror) {

    echo $OUTPUT->container(get_string('useruploadtype', 'moodle', $choices[$uploadtype]), 'centerpara');
    echo $OUTPUT->container(get_string('uploadinvalidpreprocessedcount', 'moodle', $countcontent), 'centerpara');
    echo $OUTPUT->container(get_string('invalidusername', 'moodle'), 'centerpara');
    echo $OUTPUT->container(get_string('uploadfilecontainerror', 'moodle'), 'centerpara');
} else if (empty($contents)) {
    echo $OUTPUT->container(get_string('uupreprocessedcount', 'admin', $countcontent), 'centerpara');
    echo $OUTPUT->container(get_string('uploadfilecontentsnovaliddata'), 'centerpara');
} else {
    echo $OUTPUT->container(get_string('uupreprocessedcount', 'admin', $countcontent), 'centerpara');
}

$mform->display();
echo $OUTPUT->footer();
die;

/////////////////////////////////////
/// Utility functions and classes ///
/////////////////////////////////////

class uu_progress_tracker {
    var $_row;
    var $columns = array('status', 'line', 'id', 'username', 'firstname', 'lastname', 'email', 'password', 'auth', 'enrolments', 'deleted');

    function uu_progress_tracker() {
    }

    function init() {
        $ci = 0;
        echo '<table id="uuresults" class="generaltable boxaligncenter" summary="'.get_string('uploadusersresult', 'admin').'">';
        echo '<tr class="heading r0">';
        echo '<th class="header c'.$ci++.'" scope="col">'.get_string('status').'</th>';
        echo '<th class="header c'.$ci++.'" scope="col">'.get_string('uucsvline', 'admin').'</th>';
        echo '<th class="header c'.$ci++.'" scope="col">ID</th>';
        echo '<th class="header c'.$ci++.'" scope="col">'.get_string('username').'</th>';
        echo '<th class="header c'.$ci++.'" scope="col">'.get_string('firstname').'</th>';
        echo '<th class="header c'.$ci++.'" scope="col">'.get_string('lastname').'</th>';
        echo '<th class="header c'.$ci++.'" scope="col">'.get_string('email').'</th>';
        echo '<th class="header c'.$ci++.'" scope="col">'.get_string('password').'</th>';
        echo '<th class="header c'.$ci++.'" scope="col">'.get_string('authentication').'</th>';
        echo '<th class="header c'.$ci++.'" scope="col">'.get_string('enrolments', 'enrol').'</th>';
        echo '<th class="header c'.$ci++.'" scope="col">'.get_string('delete').'</th>';
        echo '</tr>';
        $this->_row = null;
    }

    function flush() {
        if (empty($this->_row) or empty($this->_row['line']['normal'])) {
            $this->_row = array();
            foreach ($this->columns as $col) {
                $this->_row[$col] = array('normal'=>'', 'info'=>'', 'warning'=>'', 'error'=>'');
            }
            return;
        }
        $ci = 0;
        $ri = 1;
        echo '<tr class="r'.$ri.'">';
        foreach ($this->_row as $key=>$field) {
            foreach ($field as $type=>$content) {
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
            $this->_row[$col] = array('normal'=>'', 'info'=>'', 'warning'=>'', 'error'=>'');
        }
    }

    function track($col, $msg, $level='normal', $merge=true) {
        if (empty($this->_row)) {
            $this->flush(); //init arrays
        }
        if (!in_array($col, $this->columns)) {
            debugging('Incorrect column:'.$col);
            return;
        }
        if ($merge) {
            if ($this->_row[$col][$level] != '') {
                $this->_row[$col][$level] .='<br />';
            }
            $this->_row[$col][$level] .= s($msg);
        } else {
            $this->_row[$col][$level] = s($msg);
        }
    }

    function close() {
        echo '</table>';
    }
}

/**
 * Validation callback function - verified the column line of csv file.
 * Converts column names to lowercase too.
 */
function validate_user_upload_columns(&$columns) {
    global $STD_FIELDS, $PRF_FIELDS;

    if (count($columns) < 2) {
        return get_string('csvfewcolumns', 'error');
    }

    // test columns
    $processed = array();
    foreach ($columns as $key=>$unused) {
        $columns[$key] = strtolower($columns[$key]); // no unicode expected here, ignore case
        $field = $columns[$key];
        if (!in_array($field, $STD_FIELDS) && !in_array($field, $PRF_FIELDS) &&// if not a standard field and not an enrolment field, then we have an error
            !preg_match('/^course\d+$/', $field) && !preg_match('/^group\d+$/', $field) &&
            !preg_match('/^type\d+$/', $field) && !preg_match('/^role\d+$/', $field) &&
            !preg_match('/^enrolperiod\d+$/', $field)) {
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
        $username = $matches[1][0].($matches[2][0]+1);
    }

    if ($DB->record_exists('user', array('username'=>$username, 'mnethostid'=>$mnethostid))) {
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

    // very very ugly hack!
    global $template_globals;
    $template_globals = new object();
    $template_globals->username  = isset($user->username)  ? $user->username  : '';
    $template_globals->firstname = isset($user->firstname) ? $user->firstname : '';
    $template_globals->lastname  = isset($user->lastname)  ? $user->lastname  : '';

    $result = preg_replace_callback('/(?<!%)%([+-~])?(\d)*([flu])/', 'process_template_callback', $template);

    $template_globals = null;

    if (is_null($result)) {
        return $template; //error during regex processing??
    } else {
        return $result;
    }
}

/**
 * Internal callback function.
 */
function process_template_callback($block) {
    global $template_globals;
    $textlib = textlib_get_instance();
    $repl = $block[0];

    switch ($block[3]) {
        case 'u': $repl = $template_globals->username; break;
        case 'f': $repl = $template_globals->firstname; break;
        case 'l': $repl = $template_globals->lastname; break;
    }
    switch ($block[1]) {
        case '+': $repl = $textlib->strtoupper($repl); break;
        case '-': $repl = $textlib->strtolower($repl); break;
        case '~': $repl = $textlib->strtotitle($repl); break;
    }
    if (!empty($block[2])) {
        $repl = $textlib->substr($repl, 0 , $block[2]);
    }

    return $repl;
}

/**
 * Returns list of auth plugins that are enabled and known to work.
 */
function uu_allowed_auths() {
    global $CFG;

    // only following plugins are guaranteed to work properly
    // TODO: add support for more plugins in 2.0
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
    // let's cheat a bit, frontpage is guaranteed to exist and has the same list of roles ;-)
    $roles = get_assignable_roles(get_context_instance(CONTEXT_COURSE, SITEID), ROLENAME_ORIGINALANDSHORT);
    return array_reverse($roles, true);
}

function uu_allowed_roles_cache() {
    $allowedroles = get_assignable_roles(get_context_instance(CONTEXT_COURSE, SITEID), ROLENAME_SHORT);
    foreach ($allowedroles as $rid=>$rname) {
        $rolecache[$rid] = new object();
        $rolecache[$rid]->id   = $rid;
        $rolecache[$rid]->name = $rname;
        if (!is_numeric($rname)) { // only non-numeric shortnames are supported!!!
            $rolecache[$rname] = new object();
            $rolecache[$rname]->id   = $rid;
            $rolecache[$rname]->name = $rname;
        }
    }
    return $rolecache;
}
