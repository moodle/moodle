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
 * External user API
 *
 * @package    moodlecore
 * @subpackage user
 * @copyright  2009 Moodle Pty Ltd (http://moodle.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


/**
 * Creates a user
 *
 * @param object $user user to create
 * @return int id of the newly created user
 */
function user_create_user($user) {
    global $DB;

    // set the timecreate field to the current time
    if (!is_object($user)) {
            $user = (object)$user;
    }

    //check username
    if ($user->username !== textlib::strtolower($user->username)) {
        throw new moodle_exception('usernamelowercase');
    } else {
        if ($user->username !== clean_param($user->username, PARAM_USERNAME)) {
            throw new moodle_exception('invalidusername');
        }
    }

    // save the password in a temp value for later
    if (isset($user->password)) {

        //check password toward the password policy
        if (!check_password_policy($user->password, $errmsg)) {
            throw new moodle_exception($errmsg);
        }

        $userpassword = $user->password;
        unset($user->password);
    }

    $user->timecreated = time();
    $user->timemodified = $user->timecreated;

    // insert the user into the database
    $newuserid = $DB->insert_record('user', $user);

    // trigger user_created event on the full database user row
    $newuser = $DB->get_record('user', array('id' => $newuserid));

    // create USER context for this user
    context_user::instance($newuserid);

    // update user password if necessary
    if (isset($userpassword)) {
        $authplugin = get_auth_plugin($newuser->auth);
        $authplugin->user_update_password($newuser, $userpassword);
    }

    events_trigger('user_created', $newuser);

    add_to_log(SITEID, 'user', get_string('create'), '/view.php?id='.$newuser->id,
        fullname($newuser));

    return $newuserid;

}

/**
 * Update a user with a user object (will compare against the ID)
 *
 * @param object $user the user to update
 */
function user_update_user($user) {
    global $DB;

    // set the timecreate field to the current time
    if (!is_object($user)) {
            $user = (object)$user;
    }

    //check username
    if (isset($user->username)) {
        if ($user->username !== textlib::strtolower($user->username)) {
            throw new moodle_exception('usernamelowercase');
        } else {
            if ($user->username !== clean_param($user->username, PARAM_USERNAME)) {
                throw new moodle_exception('invalidusername');
            }
        }
    }

    // unset password here, for updating later
    if (isset($user->password)) {

        //check password toward the password policy
        if (!check_password_policy($user->password, $errmsg)) {
            throw new moodle_exception($errmsg);
        }

        $passwd = $user->password;
        unset($user->password);
    }

    $user->timemodified = time();
    $DB->update_record('user', $user);

    // trigger user_updated event on the full database user row
    $updateduser = $DB->get_record('user', array('id' => $user->id));

    // if password was set, then update its hash
    if (isset($passwd)) {
        $authplugin = get_auth_plugin($updateduser->auth);
        if ($authplugin->can_change_password()) {
            $authplugin->user_update_password($updateduser, $passwd);
        }
    }

    events_trigger('user_updated', $updateduser);

    add_to_log(SITEID, 'user', get_string('update'), '/view.php?id='.$updateduser->id,
        fullname($updateduser));

}

/**
 * Marks user deleted in internal user database and notifies the auth plugin.
 * Also unenrols user from all roles and does other cleanup.
 *
 * @todo Decide if this transaction is really needed (look for internal TODO:)
 * @param object $user Userobject before delete    (without system magic quotes)
 * @return boolean success
 */
function user_delete_user($user) {
    return delete_user($user);
}

/**
 * Get users by id
 * @param array $userids id of users to retrieve
 *
 */
function user_get_users_by_id($userids) {
    global $DB;
    return $DB->get_records_list('user', 'id', $userids);
}

/**
 * Returns the list of default 'displayable' fields
 *
 * Contains database field names but also names used to generate information, such as enrolledcourses
 *
 * @return array of user fields
 */
function user_get_default_fields() {
    return array( 'id', 'username', 'fullname', 'firstname', 'lastname', 'email',
        'address', 'phone1', 'phone2', 'icq', 'skype', 'yahoo', 'aim', 'msn', 'department',
        'institution', 'interests', 'firstaccess', 'lastaccess', 'auth', 'confirmed',
        'idnumber', 'lang', 'theme', 'timezone', 'mailformat', 'description', 'descriptionformat',
        'city', 'url', 'country', 'profileimageurlsmall', 'profileimageurl', 'customfields',
        'groups', 'roles', 'preferences', 'enrolledcourses'
    );
}

/**
 *
 * Give user record from mdl_user, build an array conntains
 * all user details
 *
 * Warning: description file urls are 'webservice/pluginfile.php' is use.
 *          it can be changed with $CFG->moodlewstextformatlinkstoimagesfile
 *
 * @param stdClass $user user record from mdl_user
 * @param stdClass $context context object
 * @param stdClass $course moodle course
 * @param array $userfields required fields
 * @return array
 */
function user_get_user_details($user, $course = null, array $userfields = array()) {
    global $USER, $DB, $CFG;
    require_once($CFG->dirroot . "/user/profile/lib.php"); //custom field library
    require_once($CFG->dirroot . "/lib/filelib.php");      // file handling on description and friends

    $defaultfields = user_get_default_fields();

    if (empty($userfields)) {
        $userfields = $defaultfields;
    }

    foreach ($userfields as $thefield) {
        if (!in_array($thefield, $defaultfields)) {
            throw new moodle_exception('invaliduserfield', 'error', '', $thefield);
        }
    }


    // Make sure id and fullname are included
    if (!in_array('id', $userfields)) {
        $userfields[] = 'id';
    }

    if (!in_array('fullname', $userfields)) {
        $userfields[] = 'fullname';
    }

    if (!empty($course)) {
        $context = context_course::instance($course->id);
        $usercontext = context_user::instance($user->id);
        $canviewdetailscap = (has_capability('moodle/user:viewdetails', $context) || has_capability('moodle/user:viewdetails', $usercontext));
    } else {
        $context = context_user::instance($user->id);
        $usercontext = $context;
        $canviewdetailscap = has_capability('moodle/user:viewdetails', $usercontext);
    }

    $currentuser = ($user->id == $USER->id);
    $isadmin = is_siteadmin($USER);

    $showuseridentityfields = get_extra_user_fields($context);

    if (!empty($course)) {
        $canviewhiddenuserfields = has_capability('moodle/course:viewhiddenuserfields', $context);
    } else {
        $canviewhiddenuserfields = has_capability('moodle/user:viewhiddendetails', $context);
    }
    $canviewfullnames        = has_capability('moodle/site:viewfullnames', $context);
    if (!empty($course)) {
        $canviewuseremail = has_capability('moodle/course:useremail', $context);
    } else {
        $canviewuseremail = false;
    }
    $cannotviewdescription   = !empty($CFG->profilesforenrolledusersonly) && !$currentuser && !$DB->record_exists('role_assignments', array('userid'=>$user->id));
    if (!empty($course)) {
        $canaccessallgroups = has_capability('moodle/site:accessallgroups', $context);
    } else {
        $canaccessallgroups = false;
    }

    if (!$currentuser && !$canviewdetailscap && !has_coursecontact_role($user->id)) {
        // skip this user details
        return null;
    }

    $userdetails = array();
    $userdetails['id'] = $user->id;

    if (($isadmin or $currentuser) and in_array('username', $userfields)) {
        $userdetails['username'] = $user->username;
    }
    if ($isadmin or $canviewfullnames) {
        if (in_array('firstname', $userfields)) {
            $userdetails['firstname'] = $user->firstname;
        }
        if (in_array('lastname', $userfields)) {
            $userdetails['lastname'] = $user->lastname;
        }
    }
    $userdetails['fullname'] = fullname($user);

    if (in_array('customfields', $userfields)) {
        $fields = $DB->get_recordset_sql("SELECT f.*
                                            FROM {user_info_field} f
                                            JOIN {user_info_category} c
                                                 ON f.categoryid=c.id
                                        ORDER BY c.sortorder ASC, f.sortorder ASC");
        $userdetails['customfields'] = array();
        foreach ($fields as $field) {
            require_once($CFG->dirroot.'/user/profile/field/'.$field->datatype.'/field.class.php');
            $newfield = 'profile_field_'.$field->datatype;
            $formfield = new $newfield($field->id, $user->id);
            if ($formfield->is_visible() and !$formfield->is_empty()) {
                $userdetails['customfields'][] =
                    array('name' => $formfield->field->name, 'value' => $formfield->data,
                        'type' => $field->datatype, 'shortname' => $formfield->field->shortname);
            }
        }
        $fields->close();
        // unset customfields if it's empty
        if (empty($userdetails['customfields'])) {
            unset($userdetails['customfields']);
        }
    }

    // profile image
    if (in_array('profileimageurl', $userfields)) {
        $profileimageurl = moodle_url::make_pluginfile_url($usercontext->id, 'user', 'icon', NULL, '/', 'f1');
        $userdetails['profileimageurl'] = $profileimageurl->out(false);
    }
    if (in_array('profileimageurlsmall', $userfields)) {
        $profileimageurlsmall = moodle_url::make_pluginfile_url($usercontext->id, 'user', 'icon', NULL, '/', 'f2');
        $userdetails['profileimageurlsmall'] = $profileimageurlsmall->out(false);
    }

    //hidden user field
    if ($canviewhiddenuserfields) {
        $hiddenfields = array();
        // address, phone1 and phone2 not appears in hidden fields list
        // but require viewhiddenfields capability
        // according to user/profile.php
        if ($user->address && in_array('address', $userfields)) {
            $userdetails['address'] = $user->address;
        }
    } else {
        $hiddenfields = array_flip(explode(',', $CFG->hiddenuserfields));
    }

    if ($user->phone1 && in_array('phone1', $userfields) &&
            (isset($showuseridentityfields['phone1']) or $canviewhiddenuserfields)) {
        $userdetails['phone1'] = $user->phone1;
    }
    if ($user->phone2 && in_array('phone2', $userfields) &&
            (isset($showuseridentityfields['phone2']) or $canviewhiddenuserfields)) {
        $userdetails['phone2'] = $user->phone2;
    }

    if (isset($user->description) &&
        ((!isset($hiddenfields['description']) && !$cannotviewdescription) or $isadmin)) {
        if (in_array('description', $userfields)) {
            // Always return the descriptionformat if description is requested.
            list($userdetails['description'], $userdetails['descriptionformat']) =
                    external_format_text($user->description, $user->descriptionformat,
                            $usercontext->id, 'user', 'profile', null);
        }
    }

    if (in_array('country', $userfields) && (!isset($hiddenfields['country']) or $isadmin) && $user->country) {
        $userdetails['country'] = $user->country;
    }

    if (in_array('city', $userfields) && (!isset($hiddenfields['city']) or $isadmin) && $user->city) {
        $userdetails['city'] = $user->city;
    }

    if (in_array('url', $userfields) && $user->url && (!isset($hiddenfields['webpage']) or $isadmin)) {
        $url = $user->url;
        if (strpos($user->url, '://') === false) {
            $url = 'http://'. $url;
        }
        $user->url = clean_param($user->url, PARAM_URL);
        $userdetails['url'] = $user->url;
    }

    if (in_array('icq', $userfields) && $user->icq && (!isset($hiddenfields['icqnumber']) or $isadmin)) {
        $userdetails['icq'] = $user->icq;
    }

    if (in_array('skype', $userfields) && $user->skype && (!isset($hiddenfields['skypeid']) or $isadmin)) {
        $userdetails['skype'] = $user->skype;
    }
    if (in_array('yahoo', $userfields) && $user->yahoo && (!isset($hiddenfields['yahooid']) or $isadmin)) {
        $userdetails['yahoo'] = $user->yahoo;
    }
    if (in_array('aim', $userfields) && $user->aim && (!isset($hiddenfields['aimid']) or $isadmin)) {
        $userdetails['aim'] = $user->aim;
    }
    if (in_array('msn', $userfields) && $user->msn && (!isset($hiddenfields['msnid']) or $isadmin)) {
        $userdetails['msn'] = $user->msn;
    }

    if (in_array('firstaccess', $userfields) && (!isset($hiddenfields['firstaccess']) or $isadmin)) {
        if ($user->firstaccess) {
            $userdetails['firstaccess'] = $user->firstaccess;
        } else {
            $userdetails['firstaccess'] = 0;
        }
    }
    if (in_array('lastaccess', $userfields) && (!isset($hiddenfields['lastaccess']) or $isadmin)) {
        if ($user->lastaccess) {
            $userdetails['lastaccess'] = $user->lastaccess;
        } else {
            $userdetails['lastaccess'] = 0;
        }
    }

    if (in_array('email', $userfields) && ($isadmin // The admin is allowed the users email
      or $currentuser // Of course the current user is as well
      or $canviewuseremail  // this is a capability in course context, it will be false in usercontext
      or isset($showuseridentityfields['email'])
      or $user->maildisplay == 1
      or ($user->maildisplay == 2 and enrol_sharing_course($user, $USER)))) {
        $userdetails['email'] = $user->email;
    }

    if (in_array('interests', $userfields) && !empty($CFG->usetags)) {
        require_once($CFG->dirroot . '/tag/lib.php');
        if ($interests = tag_get_tags_csv('user', $user->id, TAG_RETURN_TEXT) ) {
            $userdetails['interests'] = $interests;
        }
    }

    //Departement/Institution/Idnumber are not displayed on any profile, however you can get them from editing profile.
    if ($isadmin or $currentuser or isset($showuseridentityfields['idnumber'])) {
        if (in_array('idnumber', $userfields) && $user->idnumber) {
            $userdetails['idnumber'] = $user->idnumber;
        }
    }
    if ($isadmin or $currentuser or isset($showuseridentityfields['institution'])) {
        if (in_array('institution', $userfields) && $user->institution) {
            $userdetails['institution'] = $user->institution;
        }
    }
    if ($isadmin or $currentuser or isset($showuseridentityfields['department'])) {
        if (in_array('department', $userfields) && isset($user->department)) { //isset because it's ok to have department 0
            $userdetails['department'] = $user->department;
        }
    }

    if (in_array('roles', $userfields) && !empty($course)) {
        // not a big secret
        $roles = get_user_roles($context, $user->id, false);
        $userdetails['roles'] = array();
        foreach ($roles as $role) {
            $userdetails['roles'][] = array(
                'roleid'       => $role->roleid,
                'name'         => $role->name,
                'shortname'    => $role->shortname,
                'sortorder'    => $role->sortorder
            );
        }
    }

    // If groups are in use and enforced throughout the course, then make sure we can meet in at least one course level group
    if (in_array('groups', $userfields) && !empty($course) && $canaccessallgroups) {
        $usergroups = groups_get_all_groups($course->id, $user->id, $course->defaultgroupingid,
                'g.id, g.name,g.description,g.descriptionformat');
        $userdetails['groups'] = array();
        foreach ($usergroups as $group) {
            list($group->description, $group->descriptionformat) =
                external_format_text($group->description, $group->descriptionformat,
                        $context->id, 'group', 'description', $group->id);
            $userdetails['groups'][] = array('id'=>$group->id, 'name'=>$group->name,
                'description'=>$group->description, 'descriptionformat'=>$group->descriptionformat);
        }
    }
    //list of courses where the user is enrolled
    if (in_array('enrolledcourses', $userfields) && !isset($hiddenfields['mycourses'])) {
        $enrolledcourses = array();
        if ($mycourses = enrol_get_users_courses($user->id, true)) {
            foreach ($mycourses as $mycourse) {
                if ($mycourse->category) {
                    $coursecontext = context_course::instance($mycourse->id);
                    $enrolledcourse = array();
                    $enrolledcourse['id'] = $mycourse->id;
                    $enrolledcourse['fullname'] = format_string($mycourse->fullname, true, array('context' => $coursecontext));
                    $enrolledcourse['shortname'] = format_string($mycourse->shortname, true, array('context' => $coursecontext));
                    $enrolledcourses[] = $enrolledcourse;
                }
            }
            $userdetails['enrolledcourses'] = $enrolledcourses;
        }
    }

    //user preferences
    if (in_array('preferences', $userfields) && $currentuser) {
        $preferences = array();
        $userpreferences = get_user_preferences();
         foreach($userpreferences as $prefname => $prefvalue) {
            $preferences[] = array('name' => $prefname, 'value' => $prefvalue);
         }
         $userdetails['preferences'] = $preferences;
    }

    return $userdetails;
}

/**
 * Return a list of page types
 * @param string $pagetype current page type
 * @param stdClass $parentcontext Block's parent context
 * @param stdClass $currentcontext Current context of block
 */
function user_page_type_list($pagetype, $parentcontext, $currentcontext) {
    return array('user-profile'=>get_string('page-user-profile', 'pagetype'));
}
