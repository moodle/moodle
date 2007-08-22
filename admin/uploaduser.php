<?php

/// Bulk user registration script from a comma separated file
/// Returns list of users with their user ids

require_once('../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/textlib.class.php');
require_once('uploaduser_form.php');
define('LINE_MAX_SIZE', 1024);

//Note: commas within a field should be encoded as &#44 (for comma separated csv files)
//Note: semicolon within a field should be encoded as &#59 (for semicolon separated csv files)
$csv_delimiter = isset($CFG->CSV_DELIMITER) ? $CFG->CSV_DELIMITER : ',';
$csv_encode = '&#' . (isset($CFG->CSV_ENCODE) ? $CFG->CSV_ENCODE : ord($csv_delimiter));

@set_time_limit(0);
@raise_memory_limit('192M');

admin_externalpage_setup('uploadusers');
require_capability('moodle/site:uploadusers', get_context_instance(CONTEXT_SYSTEM));

if (! $site = get_site()) {
    error('Could not find site-level course');
}

$textlib =& textlib_get_instance();

$struserrenamed = get_string('userrenamed', 'admin');
$strusernotrenamedexists = get_string('usernotrenamedexists', 'error');
$strusernotrenamedmissing = get_string('usernotrenamedmissing', 'error');

$struserupdated = get_string('useraccountupdated', 'admin');
$strusernotupdated = get_string('usernotupdatederror', 'error');

$struseradded = get_string('newuser');
$strusernotadded = get_string('usernotaddedregistered', 'error');
$strusernotaddederror = get_string('usernotaddederror', 'error');

$struserdeleted = get_string('userdeleted', 'admin');
$strusernotdeletederror = get_string('usernotdeletederror', 'error');
$strusernotdeletedmissing = get_string('usernotdeletedmissing', 'error');

$strcannotassignrole = get_string('cannotassignrole', 'error');
$strduplicateusername = get_string('duplicateusername', 'error');
$strindent = '-->';

$mform = new admin_uploaduser_form();

// Print the header

admin_externalpage_print_header();

// If a file has been uploaded, then process it
if ( $formdata = $mform->get_data() ) {
    $createpassword = $formdata->createpassword;
    $updateaccounts = $formdata->updateaccounts;
    $allowrenames   = $formdata->allowrenames;
    $skipduplicates = $formdata->duplicatehandling;
    
    // make arrays of valid fields for error checking 
    // the value associated to each field is: 0 = optional field, 1 = field required either in default values or in data file
    $fields = array(
        'firstname' => 1,
        'lastname' => 1,
        'username' => 1,
        'email' => 1,
        'city' => 1,
        'country' => 1,
        'lang' => 1,
        'auth' => 1,
        'timezone' => 1,
        'mailformat' => 1,
        'maildisplay' => 1,
        'htmleditor' => 1,
        'autosubscribe' => 1,
        'mnethostid' => 0, 
        'institution' => 0,
        'department' => 0,
        'idnumber' => 0,
        'icq' => 0,
        'phone1' => 0,
        'phone2' => 0,
        'address' => 0,
        'url' => 0,
        'description' => 0,
        'icq' => 0,
        'oldusername' => 0,
        'emailstop' => 1,
        'deleted' => 0,
        'password' => !$createpassword,
    );

    $fp = fopen($mform->get_userfile_name(), 'r');
    $linenum = 1; // since header is line 1
    // get header (field names) and remove Unicode BOM from first line, if any
    $line = explode($csv_delimiter, $textlib->trim_utf8_bom(fgets($fp,LINE_MAX_SIZE)));
    // check for valid field names
    $headers = array();
    foreach ($line as $key => $value) {
        $value = trim($value); // remove whitespace
        if (!in_array($value, $fields) && // if not a standard field and not an enrolment field, then we have an error 
            !preg_match('/^course\d+$/', $value) && !preg_match('/^group\d+$/', $value) && 
            !preg_match('/^type\d+$/', $value) && !preg_match('/^role\d+$/', $value)) {
            error(get_string('invalidfieldname', 'error', $value), 'uploaduser.php?sesskey='.$USER->sesskey);
        }
        $headers[$key] = $value; 
    }

    // check that required fields are present or a default value for them exists
    $headersOk = true;
    // disable the check if we also have deleting information (ie. deleted column)
    if (!in_array('deleted', $headers)) {
        foreach ($fields as $key => $required) {
            if($required && !in_array($key, $headers) && (!isset($formdata->$key) || $formdata->$key==='')) { 
                notify(get_string('missingfield', 'error', $key));
                $headersOk = false;
            }
        }
    }
    if($headersOk) {
        $usersnew     = 0;
        $usersupdated = 0;
        $userserrors  = 0;
        $usersdeleted = 0;
        $renames      = 0;
        $renameerrors = 0;
        $deleteerrors = 0;
        $newusernames = array();
        // We'll need courses a lot, so fetch it early and keep it in memory, indexed by their shortname
        $tmp =& get_courses('all','','id,shortname,visible');
        $courses = array();
        foreach ($tmp as $c) {
            $courses[$c->shortname] = $c;
        }
        unset($tmp);

        echo '<p id="results">';
        while (!feof ($fp)) {
            $errors = '';
            $user = new object();
            // by default, use the local mnet id (this may be changed in the file)
            $user->mnethostid = $CFG->mnet_localhost_id;
            $line = explode($csv_delimiter, fgets($fp,LINE_MAX_SIZE));
            ++$linenum;
            // add fields to user object
            foreach ($line as $key => $value) {
                if($value !== '') {
                    $key = $headers[$key];
                    //decode encoded commas
                    $value = str_replace($csv_encode,$csv_delimiter,trim($value));
                    // special fields: password and username
                    if ($key == 'password' && !empty($value)) {
                        $user->$key = hash_internal_user_password($value);
                    } else if($key == 'username') {
                        $value = $textlib->strtolower(addslashes($value));
                        if(empty($CFG->extendedusernamechars)) {
                            $value = eregi_replace('[^(-\.[:alnum:])]', '', $value);
                        }
                        @$newusernames[$value]++;
                        $user->$key = $value;
                    } else {
                        $user->$key = addslashes($value);
                    }
                }
            }
            
            // add default values for remaining fields
            foreach ($fields as $key => $required) {
                if(isset($user->$key)) {
                    continue;
                }
                if(!isset($formdata->$key) || $formdata->$key==='') { // no default value was submited
                    // if the field is required, give an error only if we are adding the user or deleting a user with unkown username
                    if($required && (empty($user->deleted) || $key == 'username')) { 
                        $errors .= get_string('missingfield', 'error', $key) . ' ';
                    }
                    continue;
                }
                // process templates
                $template = $formdata->$key;
                $templatelen = strlen($template);
                $value = '';
                for ($i = 0 ; $i < $templatelen; ++$i) {
                    if($template[$i] == '%') {
                        $case = 0; // 1=lowercase, 2=uppercase
                        $len = 0; // number of characters to keep
                        $info = null; // data to process
                        for($j = $i + 1; is_null($info) && $j < $templatelen; ++$j) {
                            $car = $template[$j];
                            if ($car >= '0' && $car <= '9') {
                                $len = $len * 10 + (int)$car;
                            } else if($car == '-') {
                                $case = 1;
                            } else if($car == '+') { 
                                $case = 2;
                            } else if($car == 'f') { // first name
                                $info = @$user->firstname;
                            } else if($car == 'l') { // last name
                                $info = @$user->lastname;
                            } else if($car == 'u') { // username
                                $info = @$user->username;
                            } else if($car == '%' && $j == $i+1) {
                                $info = '%';
                            } else { // invalid character
                                $info = '';
                            }
                        }
                        if($info==='' || is_null($info)) { // invalid template
                            continue;
                        }
                        $i = $j - 1;
                        // change case
                        if($case == 1) { 
                            $info = $textlib->strtolower($info);
                        } else if($case == 2) {
                            $info = $textlib->strtoupper($info);
                        }
                        if($len) { // truncate data
                            $info = $textlib->substr($info, 0, $len);
                        }
                        $value .= $info;
                    } else {
                        $value .= $template[$i];
                    }
                }
                
                if($key == 'username') {
                    $value = $textlib->strtolower($value);
                    if(empty($CFG->extendedusernamechars)) {
                        $value = eregi_replace('[^(-\.[:alnum:])]', '', $value);
                    }
                    @$newusernames[$value]++;
                    // check for new username duplicates
                    if($newusernames[$value] > 1) {
                        if($skipduplicates) {
                            $errors .= $strduplicateusername . ' (' . stripslashes($value) . '). ';
                            continue;
                        } else {
                            $value .= $newusernames[$value];
                        }
                    }
                }
                $user->$key = $value;
            }
            if($errors) {
                notify(get_string('erroronline', 'error', $linenum). ': ' . $errors);
                ++$userserrors;
                continue;
            }

            // delete user 
            if(@$user->deleted) {
                $info = ': ' . stripslashes($user->username) . '. ';
                if($user =& get_record('user', 'username', $user->username, 'mnethostid', $user->mnethostid)) {
                    $user->timemodified = time();
                    $user->username     = addslashes($user->email . $user->timemodified);  // Remember it just in case
                    $user->deleted      = 1;
                    $user->email        = '';    // Clear this field to free it up
                    $user->idnumber     = '';    // Clear this field to free it up
                    if (update_record('user', $user)) {
                        // not sure if this is needed. unenrol_student($user->id);  // From all courses
                        delete_records('role_assignments', 'userid', $user->id); // unassign all roles
                        // remove all context assigned on this user?
                        echo $struserdeleted . $info . '<br />';
                        ++$usersdeleted;
                    } else {
                        notify(get_string('erroronline', 'error', $linenum). ': ' . $strusernotdeletederror . $info);
                        ++$deleteerrors;
                    }
                } else {
                    notify(get_string('erroronline', 'error', $linenum). ': ' . $strusernotdeletedmissing . $info);
                    ++$deleteerrors;
                }            
                continue;
            }
            
            // save the user to the database
            $user->confirmed = 1;
            $user->timemodified = time();

            // before insert/update, check whether we should be updating an old record instead
            if ($allowrenames && !empty($user->oldusername) ) {
                $user->oldusername = $textlib->strtolower($user->oldusername);
                $info = ': ' . stripslashes($user->oldusername) . '-->' . stripslashes($user->username) . '. ';
                if ($olduser =& get_record('user', 'username', $user->oldusername, 'mnethostid', $user->mnethostid)) {
                    if (set_field('user', 'username', $user->username, 'id', $olduser->id)) {
                        echo $struserrenamed . $info;
                        $renames++;
                    } else {
                        notify(get_string('erroronline', 'error', $linenum). ': ' . $strusernotrenamedexists . $info);
                        $renameerrors++;
                        continue;
                    }
                } else {
                    notify(get_string('erroronline', 'error', $linenum). ': ' . $strusernotrenamedmissing . $info);
                    $renameerrors++;
                    continue;
                }
            }

            // save the information
            if ($olduser =& get_record('user', 'username', $user->username, 'mnethostid', $user->mnethostid)) {
                $user->id = $olduser->id;
                $info = ': ' . stripslashes($user->username) .' (ID = ' . $user->id . ')';
                if ($updateaccounts) {
                    // Record is being updated
                    if (update_record('user', $user)) {
                        echo $struserupdated . $info . '<br />';
                        $usersupdated++;
                    } else {
                        notify(get_string('erroronline', 'error', $linenum). ': ' . $strusernotupdated . $info);
                        $userserrors++;
                        continue;
                    }
                } else {
                    //Record not added - user is already registered
                    //In this case, output userid from previous registration
                    //This can be used to obtain a list of userids for existing users
                    echo $strusernotadded . $info . '<br />';
                    $userserrors++;
                }
            } else { // new user
                if ($user->id = insert_record('user', $user)) {
                    $info = ': ' . stripslashes($user->username) .' (ID = ' . $user->id . ')';
                    echo $struseradded . $info . '<br />';
                    $usersnew++;
                    if (empty($user->password) && $createpassword) {
                        // passwords will be created and sent out on cron
                        insert_record('user_preferences', array( 'userid' => $user->id, 'name'   => 'create_password', 'value'  => 1));
                        insert_record('user_preferences', array( 'userid' => $user->id, 'name'   => 'auth_forcepasswordchange', 'value'  => 1));
                    }
                } else {
                    // Record not added -- possibly some other error
                    notify(get_string('erroronline', 'error', $linenum). ': ' . $strusernotaddederror . ': ' . stripslashes($user->username));
                    $userserrors++;
                    continue;
                }
            }

            // find course enrolments, groups and roles/types
            for($ncourses = 1; $addcourse = @$user->{'course' . $ncourses}; ++$ncourses) {
                // find course
                if(!$course = @$courses[$addcourse]) {
                    notify(get_string('erroronline', 'error', $linenum). ': ' . get_string('unknowncourse', 'error', $addcourse));
                    continue;
                }
                // find role
                if ($addrole = @$user->{'role' . $ncourses}) {
                    $coursecontext =& get_context_instance(CONTEXT_COURSE, $course->id);
                    if (!$ok = role_assign($addrole, $user->id, 0, $coursecontext->id)) {
                        echo $strindent . $strcannotassignrole . '<br >';
                    }
                } else {
                    // if no role, then find "old" enrolment type
                    switch ($addtype = @$user->{'type' . $ncourses}) {
                        case 2:   // teacher
                            $ok = add_teacher($user->id, $course->id, 1);
                            break;
                        case 3:   // non-editing teacher
                            $ok = add_teacher($user->id, $course->id, 0);
                            break;
                        case 1:   // student
                        default:  
                            $ok = enrol_student($user->id, $course->id);
                            break;
                    }
                }
                if ($ok) {   // OK
                    echo $strindent . get_string('enrolledincourse', '', $addcourse) . '<br />';
                } else {
                    notify(get_string('erroronline', 'error', $linenum). ': ' . get_string('enrolledincoursenot', '', $addcourse));
                }

                // find group to add to
                if ($addgroup = @$user->{'group' . $ncourses}) {
                    if ($gid =& groups_get_group_by_name($course->id, $addgroup)) {
                        $coursecontext =& get_context_instance(CONTEXT_COURSE, $course->id);
                        if (count(get_user_roles($coursecontext, $user->id))) {
                            if (groups_add_member($gid, $user->id)) {
                                echo $strindent . get_string('addedtogroup','',$addgroup) . '<br />';
                            } else {
                                notify(get_string('erroronline', 'error', $linenum). ': ' . get_string('addedtogroupnot','',$addgroup));
                            }
                        } else {
                            notify(get_string('erroronline', 'error', $linenum). ': ' . get_string('addedtogroupnotenrolled','',$addgroup));
                        }
                    } else {
                        notify(get_string('erroronline', 'error', $linenum). ': ' . get_string('groupunknown','error',$addgroup));
                    }
                }
            }
        }
        echo '</p>';
        notify(get_string('userscreated', 'admin') . ': ' . $usersnew);
        notify(get_string('usersupdated', 'admin') . ': ' . $usersupdated);
        notify(get_string('usersdeleted', 'admin') . ': ' . $usersdeleted);
        notify(get_string('deleteerrors', 'admin') . ': ' . $deleteerrors);
        if ($allowrenames) {
            notify(get_string('usersrenamed', 'admin') . ': ' . $renames);
            notify(get_string('renameerrors', 'admin') . ': ' . $renameerrors);
        }
        notify(get_string('errors', 'admin') . ': ' . $userserrors);
    }
    fclose($fp);
    echo '<hr />';
}

/// Print the form
print_heading_with_help(get_string('uploadusers'), 'uploadusers');
$mform->display();
admin_externalpage_print_footer();
?>
