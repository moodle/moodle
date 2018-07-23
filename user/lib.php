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
 * @package   core_user
 * @copyright 2009 Moodle Pty Ltd (http://moodle.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('USER_FILTER_ENROLMENT', 1);
define('USER_FILTER_GROUP', 2);
define('USER_FILTER_LAST_ACCESS', 3);
define('USER_FILTER_ROLE', 4);
define('USER_FILTER_STATUS', 5);
define('USER_FILTER_STRING', 6);

/**
 * Creates a user
 *
 * @throws moodle_exception
 * @param stdClass $user user to create
 * @param bool $updatepassword if true, authentication plugin will update password.
 * @param bool $triggerevent set false if user_created event should not be triggred.
 *             This will not affect user_password_updated event triggering.
 * @return int id of the newly created user
 */
function user_create_user($user, $updatepassword = true, $triggerevent = true) {
    global $DB;

    // Set the timecreate field to the current time.
    if (!is_object($user)) {
        $user = (object) $user;
    }

    // Check username.
    if ($user->username !== core_text::strtolower($user->username)) {
        throw new moodle_exception('usernamelowercase');
    } else {
        if ($user->username !== core_user::clean_field($user->username, 'username')) {
            throw new moodle_exception('invalidusername');
        }
    }

    // Save the password in a temp value for later.
    if ($updatepassword && isset($user->password)) {

        // Check password toward the password policy.
        if (!check_password_policy($user->password, $errmsg)) {
            throw new moodle_exception($errmsg);
        }

        $userpassword = $user->password;
        unset($user->password);
    }

    // Apply default values for user preferences that are stored in users table.
    if (!isset($user->calendartype)) {
        $user->calendartype = core_user::get_property_default('calendartype');
    }
    if (!isset($user->maildisplay)) {
        $user->maildisplay = core_user::get_property_default('maildisplay');
    }
    if (!isset($user->mailformat)) {
        $user->mailformat = core_user::get_property_default('mailformat');
    }
    if (!isset($user->maildigest)) {
        $user->maildigest = core_user::get_property_default('maildigest');
    }
    if (!isset($user->autosubscribe)) {
        $user->autosubscribe = core_user::get_property_default('autosubscribe');
    }
    if (!isset($user->trackforums)) {
        $user->trackforums = core_user::get_property_default('trackforums');
    }
    if (!isset($user->lang)) {
        $user->lang = core_user::get_property_default('lang');
    }

    $user->timecreated = time();
    $user->timemodified = $user->timecreated;

    // Validate user data object.
    $uservalidation = core_user::validate($user);
    if ($uservalidation !== true) {
        foreach ($uservalidation as $field => $message) {
            debugging("The property '$field' has invalid data and has been cleaned.", DEBUG_DEVELOPER);
            $user->$field = core_user::clean_field($user->$field, $field);
        }
    }

    // Insert the user into the database.
    $newuserid = $DB->insert_record('user', $user);

    // Create USER context for this user.
    $usercontext = context_user::instance($newuserid);

    // Update user password if necessary.
    if (isset($userpassword)) {
        // Get full database user row, in case auth is default.
        $newuser = $DB->get_record('user', array('id' => $newuserid));
        $authplugin = get_auth_plugin($newuser->auth);
        $authplugin->user_update_password($newuser, $userpassword);
    }

    // Trigger event If required.
    if ($triggerevent) {
        \core\event\user_created::create_from_userid($newuserid)->trigger();
    }

    // Purge the associated caches.
    cache_helper::purge_by_event('createduser');

    return $newuserid;
}

/**
 * Update a user with a user object (will compare against the ID)
 *
 * @throws moodle_exception
 * @param stdClass $user the user to update
 * @param bool $updatepassword if true, authentication plugin will update password.
 * @param bool $triggerevent set false if user_updated event should not be triggred.
 *             This will not affect user_password_updated event triggering.
 */
function user_update_user($user, $updatepassword = true, $triggerevent = true) {
    global $DB;

    // Set the timecreate field to the current time.
    if (!is_object($user)) {
        $user = (object) $user;
    }

    // Check username.
    if (isset($user->username)) {
        if ($user->username !== core_text::strtolower($user->username)) {
            throw new moodle_exception('usernamelowercase');
        } else {
            if ($user->username !== core_user::clean_field($user->username, 'username')) {
                throw new moodle_exception('invalidusername');
            }
        }
    }

    // Unset password here, for updating later, if password update is required.
    if ($updatepassword && isset($user->password)) {

        // Check password toward the password policy.
        if (!check_password_policy($user->password, $errmsg)) {
            throw new moodle_exception($errmsg);
        }

        $passwd = $user->password;
        unset($user->password);
    }

    // Make sure calendartype, if set, is valid.
    if (empty($user->calendartype)) {
        // Unset this variable, must be an empty string, which we do not want to update the calendartype to.
        unset($user->calendartype);
    }

    $user->timemodified = time();

    // Validate user data object.
    $uservalidation = core_user::validate($user);
    if ($uservalidation !== true) {
        foreach ($uservalidation as $field => $message) {
            debugging("The property '$field' has invalid data and has been cleaned.", DEBUG_DEVELOPER);
            $user->$field = core_user::clean_field($user->$field, $field);
        }
    }

    $DB->update_record('user', $user);

    if ($updatepassword) {
        // Get full user record.
        $updateduser = $DB->get_record('user', array('id' => $user->id));

        // If password was set, then update its hash.
        if (isset($passwd)) {
            $authplugin = get_auth_plugin($updateduser->auth);
            if ($authplugin->can_change_password()) {
                $authplugin->user_update_password($updateduser, $passwd);
            }
        }
    }
    // Trigger event if required.
    if ($triggerevent) {
        \core\event\user_updated::create_from_userid($user->id)->trigger();
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
 *
 * @param array $userids id of users to retrieve
 * @return array
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
        'groups', 'roles', 'preferences', 'enrolledcourses', 'suspended'
    );
}

/**
 *
 * Give user record from mdl_user, build an array contains all user details.
 *
 * Warning: description file urls are 'webservice/pluginfile.php' is use.
 *          it can be changed with $CFG->moodlewstextformatlinkstoimagesfile
 *
 * @throws moodle_exception
 * @param stdClass $user user record from mdl_user
 * @param stdClass $course moodle course
 * @param array $userfields required fields
 * @return array|null
 */
function user_get_user_details($user, $course = null, array $userfields = array()) {
    global $USER, $DB, $CFG, $PAGE;
    require_once($CFG->dirroot . "/user/profile/lib.php"); // Custom field library.
    require_once($CFG->dirroot . "/lib/filelib.php");      // File handling on description and friends.

    $defaultfields = user_get_default_fields();

    if (empty($userfields)) {
        $userfields = $defaultfields;
    }

    foreach ($userfields as $thefield) {
        if (!in_array($thefield, $defaultfields)) {
            throw new moodle_exception('invaliduserfield', 'error', '', $thefield);
        }
    }

    // Make sure id and fullname are included.
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
    $canviewfullnames = has_capability('moodle/site:viewfullnames', $context);
    if (!empty($course)) {
        $canviewuseremail = has_capability('moodle/course:useremail', $context);
    } else {
        $canviewuseremail = false;
    }
    $cannotviewdescription   = !empty($CFG->profilesforenrolledusersonly) && !$currentuser && !$DB->record_exists('role_assignments', array('userid' => $user->id));
    if (!empty($course)) {
        $canaccessallgroups = has_capability('moodle/site:accessallgroups', $context);
    } else {
        $canaccessallgroups = false;
    }

    if (!$currentuser && !$canviewdetailscap && !has_coursecontact_role($user->id)) {
        // Skip this user details.
        return null;
    }

    $userdetails = array();
    $userdetails['id'] = $user->id;

    if (in_array('username', $userfields)) {
        if ($currentuser or has_capability('moodle/user:viewalldetails', $context)) {
            $userdetails['username'] = $user->username;
        }
    }
    if ($isadmin or $canviewfullnames) {
        if (in_array('firstname', $userfields)) {
            $userdetails['firstname'] = $user->firstname;
        }
        if (in_array('lastname', $userfields)) {
            $userdetails['lastname'] = $user->lastname;
        }
    }
    $userdetails['fullname'] = fullname($user, $canviewfullnames);

    if (in_array('customfields', $userfields)) {
        $categories = profile_get_user_fields_with_data_by_category($user->id);
        $userdetails['customfields'] = array();
        foreach ($categories as $categoryid => $fields) {
            foreach ($fields as $formfield) {
                if ($formfield->is_visible() and !$formfield->is_empty()) {

                    // TODO: Part of MDL-50728, this conditional coding must be moved to
                    // proper profile fields API so they are self-contained.
                    // We only use display_data in fields that require text formatting.
                    if ($formfield->field->datatype == 'text' or $formfield->field->datatype == 'textarea') {
                        $fieldvalue = $formfield->display_data();
                    } else {
                        // Cases: datetime, checkbox and menu.
                        $fieldvalue = $formfield->data;
                    }

                    $userdetails['customfields'][] =
                        array('name' => $formfield->field->name, 'value' => $fieldvalue,
                            'type' => $formfield->field->datatype, 'shortname' => $formfield->field->shortname);
                }
            }
        }
        // Unset customfields if it's empty.
        if (empty($userdetails['customfields'])) {
            unset($userdetails['customfields']);
        }
    }

    // Profile image.
    if (in_array('profileimageurl', $userfields)) {
        $userpicture = new user_picture($user);
        $userpicture->size = 1; // Size f1.
        $userdetails['profileimageurl'] = $userpicture->get_url($PAGE)->out(false);
    }
    if (in_array('profileimageurlsmall', $userfields)) {
        if (!isset($userpicture)) {
            $userpicture = new user_picture($user);
        }
        $userpicture->size = 0; // Size f2.
        $userdetails['profileimageurlsmall'] = $userpicture->get_url($PAGE)->out(false);
    }

    // Hidden user field.
    if ($canviewhiddenuserfields) {
        $hiddenfields = array();
        // Address, phone1 and phone2 not appears in hidden fields list but require viewhiddenfields capability
        // according to user/profile.php.
        if (!empty($user->address) && in_array('address', $userfields)) {
            $userdetails['address'] = $user->address;
        }
    } else {
        $hiddenfields = array_flip(explode(',', $CFG->hiddenuserfields));
    }

    if (!empty($user->phone1) && in_array('phone1', $userfields) &&
            (in_array('phone1', $showuseridentityfields) or $canviewhiddenuserfields)) {
        $userdetails['phone1'] = $user->phone1;
    }
    if (!empty($user->phone2) && in_array('phone2', $userfields) &&
            (in_array('phone2', $showuseridentityfields) or $canviewhiddenuserfields)) {
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
    if (in_array('suspended', $userfields) && (!isset($hiddenfields['suspended']) or $isadmin)) {
        $userdetails['suspended'] = (bool)$user->suspended;
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

    if (in_array('email', $userfields) && ($isadmin // The admin is allowed the users email.
      or $currentuser // Of course the current user is as well.
      or $canviewuseremail  // This is a capability in course context, it will be false in usercontext.
      or in_array('email', $showuseridentityfields)
      or $user->maildisplay == 1
      or ($user->maildisplay == 2 and enrol_sharing_course($user, $USER)))) {
        $userdetails['email'] = $user->email;
    }

    if (in_array('interests', $userfields)) {
        $interests = core_tag_tag::get_item_tags_array('core', 'user', $user->id, core_tag_tag::BOTH_STANDARD_AND_NOT, 0, false);
        if ($interests) {
            $userdetails['interests'] = join(', ', $interests);
        }
    }

    // Departement/Institution/Idnumber are not displayed on any profile, however you can get them from editing profile.
    if (in_array('idnumber', $userfields) && $user->idnumber) {
        if (in_array('idnumber', $showuseridentityfields) or $currentuser or
                has_capability('moodle/user:viewalldetails', $context)) {
            $userdetails['idnumber'] = $user->idnumber;
        }
    }
    if (in_array('institution', $userfields) && $user->institution) {
        if (in_array('institution', $showuseridentityfields) or $currentuser or
                has_capability('moodle/user:viewalldetails', $context)) {
            $userdetails['institution'] = $user->institution;
        }
    }
    // Isset because it's ok to have department 0.
    if (in_array('department', $userfields) && isset($user->department)) {
        if (in_array('department', $showuseridentityfields) or $currentuser or
                has_capability('moodle/user:viewalldetails', $context)) {
            $userdetails['department'] = $user->department;
        }
    }

    if (in_array('roles', $userfields) && !empty($course)) {
        // Not a big secret.
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

    // If groups are in use and enforced throughout the course, then make sure we can meet in at least one course level group.
    if (in_array('groups', $userfields) && !empty($course) && $canaccessallgroups) {
        $usergroups = groups_get_all_groups($course->id, $user->id, $course->defaultgroupingid,
                'g.id, g.name,g.description,g.descriptionformat');
        $userdetails['groups'] = array();
        foreach ($usergroups as $group) {
            list($group->description, $group->descriptionformat) =
                external_format_text($group->description, $group->descriptionformat,
                        $context->id, 'group', 'description', $group->id);
            $userdetails['groups'][] = array('id' => $group->id, 'name' => $group->name,
                'description' => $group->description, 'descriptionformat' => $group->descriptionformat);
        }
    }
    // List of courses where the user is enrolled.
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

    // User preferences.
    if (in_array('preferences', $userfields) && $currentuser) {
        $preferences = array();
        $userpreferences = get_user_preferences();
        foreach ($userpreferences as $prefname => $prefvalue) {
            $preferences[] = array('name' => $prefname, 'value' => $prefvalue);
        }
        $userdetails['preferences'] = $preferences;
    }

    if ($currentuser or has_capability('moodle/user:viewalldetails', $context)) {
        $extrafields = ['auth', 'confirmed', 'lang', 'theme', 'timezone', 'mailformat'];
        foreach ($extrafields as $extrafield) {
            if (in_array($extrafield, $userfields) && isset($user->$extrafield)) {
                $userdetails[$extrafield] = $user->$extrafield;
            }
        }
    }

    // Clean lang and auth fields for external functions (it may content uninstalled themes or language packs).
    if (isset($userdetails['lang'])) {
        $userdetails['lang'] = clean_param($userdetails['lang'], PARAM_LANG);
    }
    if (isset($userdetails['theme'])) {
        $userdetails['theme'] = clean_param($userdetails['theme'], PARAM_THEME);
    }

    return $userdetails;
}

/**
 * Tries to obtain user details, either recurring directly to the user's system profile
 * or through one of the user's course enrollments (course profile).
 *
 * @param stdClass $user The user.
 * @return array if unsuccessful or the allowed user details.
 */
function user_get_user_details_courses($user) {
    global $USER;
    $userdetails = null;

    // Get the courses that the user is enrolled in (only active).
    $courses = enrol_get_users_courses($user->id, true);

    $systemprofile = false;
    if (can_view_user_details_cap($user) || ($user->id == $USER->id) || has_coursecontact_role($user->id)) {
        $systemprofile = true;
    }

    // Try using system profile.
    if ($systemprofile) {
        $userdetails = user_get_user_details($user, null);
    } else {
        // Try through course profile.
        foreach ($courses as $course) {
            if (can_view_user_details_cap($user, $course) || ($user->id == $USER->id) || has_coursecontact_role($user->id)) {
                $userdetails = user_get_user_details($user, $course);
            }
        }
    }

    return $userdetails;
}

/**
 * Check if $USER have the necessary capabilities to obtain user details.
 *
 * @param stdClass $user
 * @param stdClass $course if null then only consider system profile otherwise also consider the course's profile.
 * @return bool true if $USER can view user details.
 */
function can_view_user_details_cap($user, $course = null) {
    // Check $USER has the capability to view the user details at user context.
    $usercontext = context_user::instance($user->id);
    $result = has_capability('moodle/user:viewdetails', $usercontext);
    // Otherwise can $USER see them at course context.
    if (!$result && !empty($course)) {
        $context = context_course::instance($course->id);
        $result = has_capability('moodle/user:viewdetails', $context);
    }
    return $result;
}

/**
 * Return a list of page types
 * @param string $pagetype current page type
 * @param stdClass $parentcontext Block's parent context
 * @param stdClass $currentcontext Current context of block
 * @return array
 */
function user_page_type_list($pagetype, $parentcontext, $currentcontext) {
    return array('user-profile' => get_string('page-user-profile', 'pagetype'));
}

/**
 * Count the number of failed login attempts for the given user, since last successful login.
 *
 * @param int|stdclass $user user id or object.
 * @param bool $reset Resets failed login count, if set to true.
 *
 * @return int number of failed login attempts since the last successful login.
 */
function user_count_login_failures($user, $reset = true) {
    global $DB;

    if (!is_object($user)) {
        $user = $DB->get_record('user', array('id' => $user), '*', MUST_EXIST);
    }
    if ($user->deleted) {
        // Deleted user, nothing to do.
        return 0;
    }
    $count = get_user_preferences('login_failed_count_since_success', 0, $user);
    if ($reset) {
        set_user_preference('login_failed_count_since_success', 0, $user);
    }
    return $count;
}

/**
 * Converts a string into a flat array of menu items, where each menu items is a
 * stdClass with fields type, url, title, pix, and imgsrc.
 *
 * @param string $text the menu items definition
 * @param moodle_page $page the current page
 * @return array
 */
function user_convert_text_to_menu_items($text, $page) {
    global $OUTPUT, $CFG;

    $lines = explode("\n", $text);
    $items = array();
    $lastchild = null;
    $lastdepth = null;
    $lastsort = 0;
    $children = array();
    foreach ($lines as $line) {
        $line = trim($line);
        $bits = explode('|', $line, 3);
        $itemtype = 'link';
        if (preg_match("/^#+$/", $line)) {
            $itemtype = 'divider';
        } else if (!array_key_exists(0, $bits) or empty($bits[0])) {
            // Every item must have a name to be valid.
            continue;
        } else {
            $bits[0] = ltrim($bits[0], '-');
        }

        // Create the child.
        $child = new stdClass();
        $child->itemtype = $itemtype;
        if ($itemtype === 'divider') {
            // Add the divider to the list of children and skip link
            // processing.
            $children[] = $child;
            continue;
        }

        // Name processing.
        $namebits = explode(',', $bits[0], 2);
        if (count($namebits) == 2) {
            // Check the validity of the identifier part of the string.
            if (clean_param($namebits[0], PARAM_STRINGID) !== '') {
                // Treat this as a language string.
                $child->title = get_string($namebits[0], $namebits[1]);
                $child->titleidentifier = implode(',', $namebits);
            }
        }
        if (empty($child->title)) {
            // Use it as is, don't even clean it.
            $child->title = $bits[0];
            $child->titleidentifier = str_replace(" ", "-", $bits[0]);
        }

        // URL processing.
        if (!array_key_exists(1, $bits) or empty($bits[1])) {
            // Set the url to null, and set the itemtype to invalid.
            $bits[1] = null;
            $child->itemtype = "invalid";
        } else {
            // Nasty hack to replace the grades with the direct url.
            if (strpos($bits[1], '/grade/report/mygrades.php') !== false) {
                $bits[1] = user_mygrades_url();
            }

            // Make sure the url is a moodle url.
            $bits[1] = new moodle_url(trim($bits[1]));
        }
        $child->url = $bits[1];

        // PIX processing.
        $pixpath = "t/edit";
        if (!array_key_exists(2, $bits) or empty($bits[2])) {
            // Use the default.
            $child->pix = $pixpath;
        } else {
            // Check for the specified image existing.
            $pixpath = "t/" . $bits[2];
            if ($page->theme->resolve_image_location($pixpath, 'moodle', true)) {
                // Use the image.
                $child->pix = $pixpath;
            } else {
                // Treat it like a URL.
                $child->pix = null;
                $child->imgsrc = $bits[2];
            }
        }

        // Add this child to the list of children.
        $children[] = $child;
    }
    return $children;
}

/**
 * Get a list of essential user navigation items.
 *
 * @param stdclass $user user object.
 * @param moodle_page $page page object.
 * @param array $options associative array.
 *     options are:
 *     - avatarsize=35 (size of avatar image)
 * @return stdClass $returnobj navigation information object, where:
 *
 *      $returnobj->navitems    array    array of links where each link is a
 *                                       stdClass with fields url, title, and
 *                                       pix
 *      $returnobj->metadata    array    array of useful user metadata to be
 *                                       used when constructing navigation;
 *                                       fields include:
 *
 *          ROLE FIELDS
 *          asotherrole    bool    whether viewing as another role
 *          rolename       string  name of the role
 *
 *          USER FIELDS
 *          These fields are for the currently-logged in user, or for
 *          the user that the real user is currently logged in as.
 *
 *          userid         int        the id of the user in question
 *          userfullname   string     the user's full name
 *          userprofileurl moodle_url the url of the user's profile
 *          useravatar     string     a HTML fragment - the rendered
 *                                    user_picture for this user
 *          userloginfail  string     an error string denoting the number
 *                                    of login failures since last login
 *
 *          "REAL USER" FIELDS
 *          These fields are for when asotheruser is true, and
 *          correspond to the underlying "real user".
 *
 *          asotheruser        bool    whether viewing as another user
 *          realuserid         int        the id of the user in question
 *          realuserfullname   string     the user's full name
 *          realuserprofileurl moodle_url the url of the user's profile
 *          realuseravatar     string     a HTML fragment - the rendered
 *                                        user_picture for this user
 *
 *          MNET PROVIDER FIELDS
 *          asmnetuser            bool   whether viewing as a user from an
 *                                       MNet provider
 *          mnetidprovidername    string name of the MNet provider
 *          mnetidproviderwwwroot string URL of the MNet provider
 */
function user_get_user_navigation_info($user, $page, $options = array()) {
    global $OUTPUT, $DB, $SESSION, $CFG;

    $returnobject = new stdClass();
    $returnobject->navitems = array();
    $returnobject->metadata = array();

    $course = $page->course;

    // Query the environment.
    $context = context_course::instance($course->id);

    // Get basic user metadata.
    $returnobject->metadata['userid'] = $user->id;
    $returnobject->metadata['userfullname'] = fullname($user, true);
    $returnobject->metadata['userprofileurl'] = new moodle_url('/user/profile.php', array(
        'id' => $user->id
    ));

    $avataroptions = array('link' => false, 'visibletoscreenreaders' => false);
    if (!empty($options['avatarsize'])) {
        $avataroptions['size'] = $options['avatarsize'];
    }
    $returnobject->metadata['useravatar'] = $OUTPUT->user_picture (
        $user, $avataroptions
    );
    // Build a list of items for a regular user.

    // Query MNet status.
    if ($returnobject->metadata['asmnetuser'] = is_mnet_remote_user($user)) {
        $mnetidprovider = $DB->get_record('mnet_host', array('id' => $user->mnethostid));
        $returnobject->metadata['mnetidprovidername'] = $mnetidprovider->name;
        $returnobject->metadata['mnetidproviderwwwroot'] = $mnetidprovider->wwwroot;
    }

    // Did the user just log in?
    if (isset($SESSION->justloggedin)) {
        // Don't unset this flag as login_info still needs it.
        if (!empty($CFG->displayloginfailures)) {
            // Don't reset the count either, as login_info() still needs it too.
            if ($count = user_count_login_failures($user, false)) {

                // Get login failures string.
                $a = new stdClass();
                $a->attempts = html_writer::tag('span', $count, array('class' => 'value'));
                $returnobject->metadata['userloginfail'] =
                    get_string('failedloginattempts', '', $a);

            }
        }
    }

    // Links: Dashboard.
    $myhome = new stdClass();
    $myhome->itemtype = 'link';
    $myhome->url = new moodle_url('/my/');
    $myhome->title = get_string('mymoodle', 'admin');
    $myhome->titleidentifier = 'mymoodle,admin';
    $myhome->pix = "i/dashboard";
    $returnobject->navitems[] = $myhome;

    // Links: My Profile.
    $myprofile = new stdClass();
    $myprofile->itemtype = 'link';
    $myprofile->url = new moodle_url('/user/profile.php', array('id' => $user->id));
    $myprofile->title = get_string('profile');
    $myprofile->titleidentifier = 'profile,moodle';
    $myprofile->pix = "i/user";
    $returnobject->navitems[] = $myprofile;

    $returnobject->metadata['asotherrole'] = false;

    // Before we add the last items (usually a logout + switch role link), add any
    // custom-defined items.
    $customitems = user_convert_text_to_menu_items($CFG->customusermenuitems, $page);
    foreach ($customitems as $item) {
        $returnobject->navitems[] = $item;
    }


    if ($returnobject->metadata['asotheruser'] = \core\session\manager::is_loggedinas()) {
        $realuser = \core\session\manager::get_realuser();

        // Save values for the real user, as $user will be full of data for the
        // user the user is disguised as.
        $returnobject->metadata['realuserid'] = $realuser->id;
        $returnobject->metadata['realuserfullname'] = fullname($realuser, true);
        $returnobject->metadata['realuserprofileurl'] = new moodle_url('/user/profile.php', array(
            'id' => $realuser->id
        ));
        $returnobject->metadata['realuseravatar'] = $OUTPUT->user_picture($realuser, $avataroptions);

        // Build a user-revert link.
        $userrevert = new stdClass();
        $userrevert->itemtype = 'link';
        $userrevert->url = new moodle_url('/course/loginas.php', array(
            'id' => $course->id,
            'sesskey' => sesskey()
        ));
        $userrevert->pix = "a/logout";
        $userrevert->title = get_string('logout');
        $userrevert->titleidentifier = 'logout,moodle';
        $returnobject->navitems[] = $userrevert;

    } else {

        // Build a logout link.
        $logout = new stdClass();
        $logout->itemtype = 'link';
        $logout->url = new moodle_url('/login/logout.php', array('sesskey' => sesskey()));
        $logout->pix = "a/logout";
        $logout->title = get_string('logout');
        $logout->titleidentifier = 'logout,moodle';
        $returnobject->navitems[] = $logout;
    }

    if (is_role_switched($course->id)) {
        if ($role = $DB->get_record('role', array('id' => $user->access['rsw'][$context->path]))) {
            // Build role-return link instead of logout link.
            $rolereturn = new stdClass();
            $rolereturn->itemtype = 'link';
            $rolereturn->url = new moodle_url('/course/switchrole.php', array(
                'id' => $course->id,
                'sesskey' => sesskey(),
                'switchrole' => 0,
                'returnurl' => $page->url->out_as_local_url(false)
            ));
            $rolereturn->pix = "a/logout";
            $rolereturn->title = get_string('switchrolereturn');
            $rolereturn->titleidentifier = 'switchrolereturn,moodle';
            $returnobject->navitems[] = $rolereturn;

            $returnobject->metadata['asotherrole'] = true;
            $returnobject->metadata['rolename'] = role_get_name($role, $context);

        }
    } else {
        // Build switch role link.
        $roles = get_switchable_roles($context);
        if (is_array($roles) && (count($roles) > 0)) {
            $switchrole = new stdClass();
            $switchrole->itemtype = 'link';
            $switchrole->url = new moodle_url('/course/switchrole.php', array(
                'id' => $course->id,
                'switchrole' => -1,
                'returnurl' => $page->url->out_as_local_url(false)
            ));
            $switchrole->pix = "i/switchrole";
            $switchrole->title = get_string('switchroleto');
            $switchrole->titleidentifier = 'switchroleto,moodle';
            $returnobject->navitems[] = $switchrole;
        }
    }

    return $returnobject;
}

/**
 * Add password to the list of used hashes for this user.
 *
 * This is supposed to be used from:
 *  1/ change own password form
 *  2/ password reset process
 *  3/ user signup in auth plugins if password changing supported
 *
 * @param int $userid user id
 * @param string $password plaintext password
 * @return void
 */
function user_add_password_history($userid, $password) {
    global $CFG, $DB;

    if (empty($CFG->passwordreuselimit) or $CFG->passwordreuselimit < 0) {
        return;
    }

    // Note: this is using separate code form normal password hashing because
    //       we need to have this under control in the future. Also the auth
    //       plugin might not store the passwords locally at all.

    $record = new stdClass();
    $record->userid = $userid;
    $record->hash = password_hash($password, PASSWORD_DEFAULT);
    $record->timecreated = time();
    $DB->insert_record('user_password_history', $record);

    $i = 0;
    $records = $DB->get_records('user_password_history', array('userid' => $userid), 'timecreated DESC, id DESC');
    foreach ($records as $record) {
        $i++;
        if ($i > $CFG->passwordreuselimit) {
            $DB->delete_records('user_password_history', array('id' => $record->id));
        }
    }
}

/**
 * Was this password used before on change or reset password page?
 *
 * The $CFG->passwordreuselimit setting determines
 * how many times different password needs to be used
 * before allowing previously used password again.
 *
 * @param int $userid user id
 * @param string $password plaintext password
 * @return bool true if password reused
 */
function user_is_previously_used_password($userid, $password) {
    global $CFG, $DB;

    if (empty($CFG->passwordreuselimit) or $CFG->passwordreuselimit < 0) {
        return false;
    }

    $reused = false;

    $i = 0;
    $records = $DB->get_records('user_password_history', array('userid' => $userid), 'timecreated DESC, id DESC');
    foreach ($records as $record) {
        $i++;
        if ($i > $CFG->passwordreuselimit) {
            $DB->delete_records('user_password_history', array('id' => $record->id));
            continue;
        }
        // NOTE: this is slow but we cannot compare the hashes directly any more.
        if (password_verify($password, $record->hash)) {
            $reused = true;
        }
    }

    return $reused;
}

/**
 * Remove a user device from the Moodle database (for PUSH notifications usually).
 *
 * @param string $uuid The device UUID.
 * @param string $appid The app id. If empty all the devices matching the UUID for the user will be removed.
 * @return bool true if removed, false if the device didn't exists in the database
 * @since Moodle 2.9
 */
function user_remove_user_device($uuid, $appid = "") {
    global $DB, $USER;

    $conditions = array('uuid' => $uuid, 'userid' => $USER->id);
    if (!empty($appid)) {
        $conditions['appid'] = $appid;
    }

    if (!$DB->count_records('user_devices', $conditions)) {
        return false;
    }

    $DB->delete_records('user_devices', $conditions);

    return true;
}

/**
 * Trigger user_list_viewed event.
 *
 * @param stdClass  $course course  object
 * @param stdClass  $context course context object
 * @since Moodle 2.9
 */
function user_list_view($course, $context) {

    $event = \core\event\user_list_viewed::create(array(
        'objectid' => $course->id,
        'courseid' => $course->id,
        'context' => $context,
        'other' => array(
            'courseshortname' => $course->shortname,
            'coursefullname' => $course->fullname
        )
    ));
    $event->trigger();
}

/**
 * Returns the url to use for the "Grades" link in the user navigation.
 *
 * @param int $userid The user's ID.
 * @param int $courseid The course ID if available.
 * @return mixed A URL to be directed to for "Grades".
 */
function user_mygrades_url($userid = null, $courseid = SITEID) {
    global $CFG, $USER;
    $url = null;
    if (isset($CFG->grade_mygrades_report) && $CFG->grade_mygrades_report != 'external') {
        if (isset($userid) && $USER->id != $userid) {
            // Send to the gradebook report.
            $url = new moodle_url('/grade/report/' . $CFG->grade_mygrades_report . '/index.php',
                    array('id' => $courseid, 'userid' => $userid));
        } else {
            $url = new moodle_url('/grade/report/' . $CFG->grade_mygrades_report . '/index.php');
        }
    } else if (isset($CFG->grade_mygrades_report) && $CFG->grade_mygrades_report == 'external'
            && !empty($CFG->gradereport_mygradeurl)) {
        $url = $CFG->gradereport_mygradeurl;
    } else {
        $url = $CFG->wwwroot;
    }
    return $url;
}

/**
 * Check if the current user has permission to view details of the supplied user.
 *
 * This function supports two modes:
 * If the optional $course param is omitted, then this function finds all shared courses and checks whether the current user has
 * permission in any of them, returning true if so.
 * If the $course param is provided, then this function checks permissions in ONLY that course.
 *
 * @param object $user The other user's details.
 * @param object $course if provided, only check permissions in this course.
 * @param context $usercontext The user context if available.
 * @return bool true for ability to view this user, else false.
 */
function user_can_view_profile($user, $course = null, $usercontext = null) {
    global $USER, $CFG;

    if ($user->deleted) {
        return false;
    }

    // Do we need to be logged in?
    if (empty($CFG->forceloginforprofiles)) {
        return true;
    } else {
       if (!isloggedin() || isguestuser()) {
            // User is not logged in and forceloginforprofile is set, we need to return now.
            return false;
        }
    }

    // Current user can always view their profile.
    if ($USER->id == $user->id) {
        return true;
    }

    // Course contacts have visible profiles always.
    if (has_coursecontact_role($user->id)) {
        return true;
    }

    // If we're only checking the capabilities in the single provided course.
    if (isset($course)) {
        // Confirm that $user is enrolled in the $course we're checking.
        if (is_enrolled(context_course::instance($course->id), $user)) {
            $userscourses = array($course);
        }
    } else {
        // Else we're checking whether the current user can view $user's profile anywhere, so check user context first.
        if (empty($usercontext)) {
            $usercontext = context_user::instance($user->id);
        }
        if (has_capability('moodle/user:viewdetails', $usercontext) || has_capability('moodle/user:viewalldetails', $usercontext)) {
            return true;
        }
        // This returns context information, so we can preload below.
        $userscourses = enrol_get_all_users_courses($user->id);
    }

    if (empty($userscourses)) {
        return false;
    }

    foreach ($userscourses as $userscourse) {
        context_helper::preload_from_record($userscourse);
        $coursecontext = context_course::instance($userscourse->id);
        if (has_capability('moodle/user:viewdetails', $coursecontext) ||
            has_capability('moodle/user:viewalldetails', $coursecontext)) {
            if (!groups_user_groups_visible($userscourse, $user->id)) {
                // Not a member of the same group.
                continue;
            }
            return true;
        }
    }
    return false;
}

/**
 * Returns users tagged with a specified tag.
 *
 * @param core_tag_tag $tag
 * @param bool $exclusivemode if set to true it means that no other entities tagged with this tag
 *             are displayed on the page and the per-page limit may be bigger
 * @param int $fromctx context id where the link was displayed, may be used by callbacks
 *            to display items in the same context first
 * @param int $ctx context id where to search for records
 * @param bool $rec search in subcontexts as well
 * @param int $page 0-based number of page being displayed
 * @return \core_tag\output\tagindex
 */
function user_get_tagged_users($tag, $exclusivemode = false, $fromctx = 0, $ctx = 0, $rec = 1, $page = 0) {
    global $PAGE;

    if ($ctx && $ctx != context_system::instance()->id) {
        $usercount = 0;
    } else {
        // Users can only be displayed in system context.
        $usercount = $tag->count_tagged_items('core', 'user',
                'it.deleted=:notdeleted', array('notdeleted' => 0));
    }
    $perpage = $exclusivemode ? 24 : 5;
    $content = '';
    $totalpages = ceil($usercount / $perpage);

    if ($usercount) {
        $userlist = $tag->get_tagged_items('core', 'user', $page * $perpage, $perpage,
                'it.deleted=:notdeleted', array('notdeleted' => 0));
        $renderer = $PAGE->get_renderer('core', 'user');
        $content .= $renderer->user_list($userlist, $exclusivemode);
    }

    return new core_tag\output\tagindex($tag, 'core', 'user', $content,
            $exclusivemode, $fromctx, $ctx, $rec, $page, $totalpages);
}

/**
 * Returns the SQL used by the participants table.
 *
 * @param int $courseid The course id
 * @param int $groupid The groupid, 0 means all groups
 * @param int $accesssince The time since last access, 0 means any time
 * @param int $roleid The role id, 0 means all roles
 * @param int $enrolid The enrolment id, 0 means all enrolment methods will be returned.
 * @param int $statusid The user enrolment status, -1 means all enrolments regardless of the status will be returned, if allowed.
 * @param string|array $search The search that was performed, empty means perform no search
 * @param string $additionalwhere Any additional SQL to add to where
 * @param array $additionalparams The additional params
 * @return array
 */
function user_get_participants_sql($courseid, $groupid = 0, $accesssince = 0, $roleid = 0, $enrolid = 0, $statusid = -1,
                                   $search = '', $additionalwhere = '', $additionalparams = array()) {
    global $DB, $USER;

    // Get the context.
    $context = \context_course::instance($courseid, MUST_EXIST);

    $isfrontpage = ($courseid == SITEID);

    // Default filter settings. We only show active by default, especially if the user has no capability to review enrolments.
    $onlyactive = true;
    $onlysuspended = false;
    if (has_capability('moodle/course:enrolreview', $context)) {
        switch ($statusid) {
            case ENROL_USER_ACTIVE:
                // Nothing to do here.
                break;
            case ENROL_USER_SUSPENDED:
                $onlyactive = false;
                $onlysuspended = true;
                break;
            default:
                // If the user has capability to review user enrolments, but statusid is set to -1, set $onlyactive to false.
                $onlyactive = false;
                break;
        }
    }

    list($esql, $params) = get_enrolled_sql($context, null, $groupid, $onlyactive, $onlysuspended, $enrolid);

    $joins = array('FROM {user} u');
    $wheres = array();

    $userfields = get_extra_user_fields($context);
    $userfieldssql = user_picture::fields('u', $userfields);

    if ($isfrontpage) {
        $select = "SELECT $userfieldssql, u.lastaccess";
        $joins[] = "JOIN ($esql) e ON e.id = u.id"; // Everybody on the frontpage usually.
        if ($accesssince) {
            $wheres[] = user_get_user_lastaccess_sql($accesssince);
        }
    } else {
        $select = "SELECT $userfieldssql, COALESCE(ul.timeaccess, 0) AS lastaccess";
        $joins[] = "JOIN ($esql) e ON e.id = u.id"; // Course enrolled users only.
        // Not everybody has accessed the course yet.
        $joins[] = 'LEFT JOIN {user_lastaccess} ul ON (ul.userid = u.id AND ul.courseid = :courseid)';
        $params['courseid'] = $courseid;
        if ($accesssince) {
            $wheres[] = user_get_course_lastaccess_sql($accesssince);
        }
    }

    // Performance hacks - we preload user contexts together with accounts.
    $ccselect = ', ' . context_helper::get_preload_record_columns_sql('ctx');
    $ccjoin = 'LEFT JOIN {context} ctx ON (ctx.instanceid = u.id AND ctx.contextlevel = :contextlevel)';
    $params['contextlevel'] = CONTEXT_USER;
    $select .= $ccselect;
    $joins[] = $ccjoin;

    // Limit list to users with some role only.
    if ($roleid) {
        // We want to query both the current context and parent contexts.
        list($relatedctxsql, $relatedctxparams) = $DB->get_in_or_equal($context->get_parent_context_ids(true),
            SQL_PARAMS_NAMED, 'relatedctx');

        $wheres[] = "u.id IN (SELECT userid FROM {role_assignments} WHERE roleid = :roleid AND contextid $relatedctxsql)";
        $params = array_merge($params, array('roleid' => $roleid), $relatedctxparams);
    }

    if (!empty($search)) {
        if (!is_array($search)) {
            $search = [$search];
        }
        foreach ($search as $index => $keyword) {
            $searchkey1 = 'search' . $index . '1';
            $searchkey2 = 'search' . $index . '2';
            $searchkey3 = 'search' . $index . '3';
            $searchkey4 = 'search' . $index . '4';
            $searchkey5 = 'search' . $index . '5';
            $searchkey6 = 'search' . $index . '6';
            $searchkey7 = 'search' . $index . '7';

            $conditions = array();
            // Search by fullname.
            $fullname = $DB->sql_fullname('u.firstname', 'u.lastname');
            $conditions[] = $DB->sql_like($fullname, ':' . $searchkey1, false, false);

            // Search by email.
            $email = $DB->sql_like('email', ':' . $searchkey2, false, false);
            if (!in_array('email', $userfields)) {
                $maildisplay = 'maildisplay' . $index;
                $userid1 = 'userid' . $index . '1';
                // Prevent users who hide their email address from being found by others
                // who aren't allowed to see hidden email addresses.
                $email = "(". $email ." AND (" .
                        "u.maildisplay <> :$maildisplay " .
                        "OR u.id = :$userid1". // User can always find himself.
                        "))";
                $params[$maildisplay] = core_user::MAILDISPLAY_HIDE;
                $params[$userid1] = $USER->id;
            }
            $conditions[] = $email;

            // Search by idnumber.
            $idnumber = $DB->sql_like('idnumber', ':' . $searchkey3, false, false);
            if (!in_array('idnumber', $userfields)) {
                $userid2 = 'userid' . $index . '2';
                // Users who aren't allowed to see idnumbers should at most find themselves
                // when searching for an idnumber.
                $idnumber = "(". $idnumber . " AND u.id = :$userid2)";
                $params[$userid2] = $USER->id;
            }
            $conditions[] = $idnumber;

            // Search by middlename.
            $middlename = $DB->sql_like('middlename', ':' . $searchkey4, false, false);
            $conditions[] = $middlename;

            // Search by alternatename.
            $alternatename = $DB->sql_like('alternatename', ':' . $searchkey5, false, false);
            $conditions[] = $alternatename;

            // Search by firstnamephonetic.
            $firstnamephonetic = $DB->sql_like('firstnamephonetic', ':' . $searchkey6, false, false);
            $conditions[] = $firstnamephonetic;

            // Search by lastnamephonetic.
            $lastnamephonetic = $DB->sql_like('lastnamephonetic', ':' . $searchkey7, false, false);
            $conditions[] = $lastnamephonetic;

            $wheres[] = "(". implode(" OR ", $conditions) .") ";
            $params[$searchkey1] = "%$keyword%";
            $params[$searchkey2] = "%$keyword%";
            $params[$searchkey3] = "%$keyword%";
            $params[$searchkey4] = "%$keyword%";
            $params[$searchkey5] = "%$keyword%";
            $params[$searchkey6] = "%$keyword%";
            $params[$searchkey7] = "%$keyword%";
        }
    }

    if (!empty($additionalwhere)) {
        $wheres[] = $additionalwhere;
        $params = array_merge($params, $additionalparams);
    }

    $from = implode("\n", $joins);
    if ($wheres) {
        $where = 'WHERE ' . implode(' AND ', $wheres);
    } else {
        $where = '';
    }

    return array($select, $from, $where, $params);
}

/**
 * Returns the total number of participants for a given course.
 *
 * @param int $courseid The course id
 * @param int $groupid The groupid, 0 means all groups
 * @param int $accesssince The time since last access, 0 means any time
 * @param int $roleid The role id, 0 means all roles
 * @param int $enrolid The applied filter for the user enrolment ID.
 * @param int $status The applied filter for the user's enrolment status.
 * @param string|array $search The search that was performed, empty means perform no search
 * @param string $additionalwhere Any additional SQL to add to where
 * @param array $additionalparams The additional params
 * @return int
 */
function user_get_total_participants($courseid, $groupid = 0, $accesssince = 0, $roleid = 0, $enrolid = 0, $statusid = -1,
                                     $search = '', $additionalwhere = '', $additionalparams = array()) {
    global $DB;

    list($select, $from, $where, $params) = user_get_participants_sql($courseid, $groupid, $accesssince, $roleid, $enrolid,
        $statusid, $search, $additionalwhere, $additionalparams);

    return $DB->count_records_sql("SELECT COUNT(u.id) $from $where", $params);
}

/**
 * Returns the participants for a given course.
 *
 * @param int $courseid The course id
 * @param int $groupid The group id
 * @param int $accesssince The time since last access
 * @param int $roleid The role id
 * @param int $enrolid The applied filter for the user enrolment ID.
 * @param int $status The applied filter for the user's enrolment status.
 * @param string $search The search that was performed
 * @param string $additionalwhere Any additional SQL to add to where
 * @param array $additionalparams The additional params
 * @param string $sort The SQL sort
 * @param int $limitfrom return a subset of records, starting at this point (optional).
 * @param int $limitnum return a subset comprising this many records (optional, required if $limitfrom is set).
 * @return moodle_recordset
 */
function user_get_participants($courseid, $groupid = 0, $accesssince, $roleid, $enrolid = 0, $statusid, $search,
                               $additionalwhere = '', $additionalparams = array(), $sort = '', $limitfrom = 0, $limitnum = 0) {
    global $DB;

    list($select, $from, $where, $params) = user_get_participants_sql($courseid, $groupid, $accesssince, $roleid, $enrolid,
        $statusid, $search, $additionalwhere, $additionalparams);

    return $DB->get_recordset_sql("$select $from $where $sort", $params, $limitfrom, $limitnum);
}

/**
 * Returns SQL that can be used to limit a query to a period where the user last accessed a course.
 *
 * @param int $accesssince The time since last access
 * @param string $tableprefix
 * @return string
 */
function user_get_course_lastaccess_sql($accesssince = null, $tableprefix = 'ul') {
    if (empty($accesssince)) {
        return '';
    }

    if ($accesssince == -1) { // Never.
        return $tableprefix . '.timeaccess = 0';
    } else {
        return $tableprefix . '.timeaccess != 0 AND ul.timeaccess < ' . $accesssince;
    }
}

/**
 * Returns SQL that can be used to limit a query to a period where the user last accessed the system.
 *
 * @param int $accesssince The time since last access
 * @param string $tableprefix
 * @return string
 */
function user_get_user_lastaccess_sql($accesssince = null, $tableprefix = 'u') {
    if (empty($accesssince)) {
        return '';
    }

    if ($accesssince == -1) { // Never.
        return $tableprefix . '.lastaccess = 0';
    } else {
        return $tableprefix . '.lastaccess != 0 AND u.lastaccess < ' . $accesssince;
    }
}

/**
 * Callback for inplace editable API.
 *
 * @param string $itemtype - Only user_roles is supported.
 * @param string $itemid - Courseid and userid separated by a :
 * @param string $newvalue - json encoded list of roleids.
 * @return \core\output\inplace_editable
 */
function core_user_inplace_editable($itemtype, $itemid, $newvalue) {
    if ($itemtype === 'user_roles') {
        return \core_user\output\user_roles_editable::update($itemid, $newvalue);
    }
}
