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

    // save the password in a temp value for later
    if (isset($user->password)) {
        $userpassword = $user->password;
        unset($user->password);
    }

    $user->timecreated = time();
    $user->timemodified = $user->timecreated;

    // insert the user into the database
    $newuserid = $DB->insert_record('user', $user);

    // trigger user_created event on the full database user row
    $newuser = $DB->get_record('user', array('id' => $newuserid));
    events_trigger('user_created', $newuser);

    // create USER context for this user
    get_context_instance(CONTEXT_USER, $newuserid);

    // update user password if necessary
    if (isset($userpassword)) {
        update_internal_user_password($newuser, $userpassword);
    }

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

    // unset password here, for updating later
    if (isset($user->password)) {
        $passwd = $user->password;
        unset($user->password);
    }

    $user->timemodified = time();
    $DB->update_record('user', $user);

    // trigger user_updated event on the full database user row
    $updateduser = $DB->get_record('user', array('id' => $user->id));
    events_trigger('user_updated', $updateduser);

    // if password was set, then update its hash
    if (isset($passwd)) {
        update_internal_user_password($updateduser, $passwd);
    }

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
 *
 * Give user record from mdl_user, build an array conntains
 * all user details
 * @param stdClass $user user record from mdl_user
 * @param stdClass $context context object
 * @param stdClass $course moodle course
 * @return array
 */
function user_get_user_details($user, $course = null) {
    global $USER, $DB, $CFG;
    require_once($CFG->dirroot . "/user/profile/lib.php"); //custom field library
    require_once($CFG->dirroot . "/lib/filelib.php");      // file handling on description and friends

    if (!empty($course)) {
        $context = get_context_instance(CONTEXT_COURSE, $course->id);
        $usercontext = get_context_instance(CONTEXT_USER, $user->id);
        $canviewdetailscap = (has_capability('moodle/user:viewdetails', $context) || has_capability('moodle/user:viewdetails', $usercontext));
    } else {
        $context = get_context_instance(CONTEXT_USER, $user->id);
        $usercontext = $context;
        $canviewdetailscap = has_capability('moodle/user:viewdetails', $usercontext);
    }

    $currentuser = ($user->id == $USER->id);
    $isadmin = is_siteadmin($USER);

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

    if ($isadmin or $currentuser) {
        $userdetails['username'] = $user->username;
    }
    if ($isadmin or $canviewfullnames) {
        $userdetails['firstname'] = $user->firstname;
        $userdetails['lastname'] = $user->lastname;
    }
    $userdetails['fullname'] = fullname($user);

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

    // profile image
    $profileimageurl = moodle_url::make_pluginfile_url($usercontext->id, 'user', 'icon', NULL, '/', 'f1');
    $userdetails['profileimageurl'] = $profileimageurl->out(false);
    $profileimageurlsmall = moodle_url::make_pluginfile_url($usercontext->id, 'user', 'icon', NULL, '/', 'f2');
    $userdetails['profileimageurlsmall'] = $profileimageurlsmall->out(false);

    //hidden user field
    if ($canviewhiddenuserfields) {
        $hiddenfields = array();
        // address, phone1 and phone2 not appears in hidden fields list
        // but require viewhiddenfields capability
        // according to user/profile.php
        if ($user->address) {
            $userdetails['address'] = $user->address;
        }
        if ($user->phone1) {
            $userdetails['phone1'] = $user->phone1;
        }
        if ($user->phone2) {
            $userdetails['phone2'] = $user->phone2;
        }
    } else {
        $hiddenfields = array_flip(explode(',', $CFG->hiddenuserfields));
    }

    if (isset($user->description) && (!isset($hiddenfields['description']) or $isadmin)) {
        if (!$cannotviewdescription) {
            $user->description = file_rewrite_pluginfile_urls($user->description, 'pluginfile.php', $usercontext->id, 'user', 'profile', null);
            $userdetails['description'] = $user->description;
            $userdetails['descriptionformat'] = $user->descriptionformat;
        }
    }

    if ((!isset($hiddenfields['country']) or $isadmin) && $user->country) {
        $userdetails['country'] = $user->country;
    }

    if ((!isset($hiddenfields['city']) or $isadmin) && $user->city) {
        $userdetails['city'] = $user->city;
    }

    if ($user->url && (!isset($hiddenfields['webpage']) or $isadmin)) {
        $url = $user->url;
        if (strpos($user->url, '://') === false) {
            $url = 'http://'. $url;
        }
        $user->url = clean_param($user->url, PARAM_URL);
        $userdetails['url'] = $user->url;
    }

    if ($user->icq && (!isset($hiddenfields['icqnumber']) or $isadmin)) {
        $userdetails['icq'] = $user->icq;
    }

    if ($user->skype && (!isset($hiddenfields['skypeid']) or $isadmin)) {
        $userdetails['skype'] = $user->skype;
    }
    if ($user->yahoo && (!isset($hiddenfields['yahooid']) or $isadmin)) {
        $userdetails['yahoo'] = $user->yahoo;
    }
    if ($user->aim && (!isset($hiddenfields['aimid']) or $isadmin)) {
        $userdetails['aim'] = $user->aim;
    }
    if ($user->msn && (!isset($hiddenfields['msnid']) or $isadmin)) {
        $userdetails['msn'] = $user->msn;
    }

    if (!isset($hiddenfields['firstaccess']) or $isadmin) {
        if ($user->firstaccess) {
            $userdetails['firstaccess'] = $user->firstaccess;
        } else {
            $userdetails['firstaccess'] = 0;
        }
    }
    if (!isset($hiddenfields['lastaccess']) or $isadmin) {
        if ($user->lastaccess) {
            $userdetails['lastaccess'] = $user->lastaccess;
        } else {
            $userdetails['lastaccess'] = 0;
        }
    }

    if ($currentuser
      or $canviewuseremail  // this is a capability in course context, it will be false in usercontext
      or $user->maildisplay == 1
      or ($user->maildisplay == 2 and enrol_sharing_course($user, $USER))) {
        $userdetails['email'] = $user->email;;
    }

    if (!empty($CFG->usetags)) {
        require_once($CFG->dirroot . '/tag/lib.php');
        if ($interests = tag_get_tags_csv('user', $user->id, TAG_RETURN_TEXT) ) {
            $userdetails['interests'] = $interests;
        }
    }

    //Departement/Institution are not displayed on any profile, however you can get them from editing profile.
    if ($isadmin or $currentuser) {
        if ($user->institution) {
            $userdetails['institution'] = $user->institution;
        }
        if (isset($user->department)) { //isset because it's ok to have department 0
            $userdetails['department'] = $user->department;
        }
    }

    if (!empty($course)) {
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
    if (!empty($course) && $canaccessallgroups) {
        $usergroups = groups_get_all_groups($course->id, $user->id, $course->defaultgroupingid, 'g.id, g.name,g.description');
        $userdetails['groups'] = array();
        foreach ($usergroups as $group) {
            $group->description = file_rewrite_pluginfile_urls($group->description, 'pluginfile.php', $context->id, 'group', 'description', $group->id);
            $userdetails['groups'][] = array('id'=>$group->id, 'name'=>$group->name, 'description'=>$group->description);
        }
    }
    //list of courses where the user is enrolled
    if (!isset($hiddenfields['mycourses'])) {
        $enrolledcourses = array();
        if ($mycourses = enrol_get_users_courses($user->id, true)) {
            foreach ($mycourses as $mycourse) {
                if ($mycourse->category) {
                    $coursecontext = get_context_instance(CONTEXT_COURSE, $mycourse->id);
                    $enrolledcourse = array();
                    $enrolledcourse['id'] = $mycourse->id;
                    $enrolledcourse['fullname'] = format_string($mycourse->fullname, true, array('context' => get_context_instance(CONTEXT_COURSE, $mycourse->id)));
                    $enrolledcourse['shortname'] = format_string($mycourse->shortname, true, array('context' => $coursecontext));
                    $enrolledcourses[] = $enrolledcourse;
                }
            }
            $userdetails['enrolledcourses'] = $enrolledcourses;
        }
    }

    //user preferences
    if ($currentuser) {
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
