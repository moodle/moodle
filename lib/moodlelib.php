<?PHP // $Id$

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// moodlelib.php                                                         //
//                                                                       //
// Main library file of miscellaneous general-purpose Moodle functions   //
//                                                                       //
// Other main libraries:                                                 //
//                                                                       //
//   weblib.php      - functions that produce web output                 //
//   datalib.php     - functions that access the database                //
//                                                                       //
///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.org                                            //
//                                                                       //
// Copyright (C) 1999-2004  Martin Dougiamas  http://dougiamas.com       //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

/// CONSTANTS /////////////////////////////////////////////////////////////

define('NOGROUPS', 0);
define('SEPARATEGROUPS', 1);
define('VISIBLEGROUPS', 2);


/// PARAMETER HANDLING ////////////////////////////////////////////////////

function require_variable($var) {
/// Variable must be present
    if (! isset($var)) {
        error("A required parameter was missing");
    }
}

function optional_variable(&$var, $default=0) {
/// Variable may be present, if not then set a default
    if (! isset($var)) {
        $var = $default;
    }
}


function set_config($name, $value) {
/// No need for get_config because they are usually always available in $CFG

    global $CFG;

    $CFG->$name = $value;  // So it's defined for this invocation at least

    if (get_field("config", "name", "name", $name)) {
        return set_field("config", "value", $value, "name", $name);
    } else {
        $config->name = $name;
        $config->value = $value;
        return insert_record("config", $config);
    }
}


function reload_user_preferences() {
/// Refresh current USER with all their current preferences

    global $USER;

    unset($USER->preference);

    if ($preferences = get_records('user_preferences', 'userid', $USER->id)) {
        foreach ($preferences as $preference) {
            $USER->preference[$preference->name] = $preference->value;
        }
    }
}

function set_user_preference($name, $value) {
/// Sets a preference for the current user

    global $USER;

    if (empty($name)) {
        return false;
    }

    if ($preference = get_record('user_preferences', 'userid', $USER->id, 'name', $name)) {
        if (set_field("user_preferences", "value", $value, "id", $preference->id)) {
            $USER->preference[$name] = $value;
            return true;
        } else {
            return false;
        }

    } else {
        $preference->userid = $USER->id;
        $preference->name   = $name;
        $preference->value  = (string)$value;
        if (insert_record('user_preferences', $preference)) {
            $USER->preference[$name] = $value;
            return true;
        } else {
            return false;
        }
    }
}

function set_user_preferences($prefarray) {
/// Sets a whole array of preferences for the current user

    if (!is_array($prefarray) or empty($prefarray)) {
        return false;
    }

    $return = true;
    foreach ($prefarray as $name => $value) {
        // The order is important; if the test for return is done first,
        // then if one function call fails all the remaining ones will
        // be "optimized away"
        $return = set_user_preference($name, $value) and $return;
    }
    return $return;
}

function get_user_preferences($name=NULL, $default=NULL) {
/// Without arguments, returns all the current user preferences
/// as an array.  If a name is specified, then this function
/// attempts to return that particular preference value.  If
/// none is found, then the optional value $default is returned,
/// otherwise NULL.

    global $USER;

    if (empty($USER->preference)) {
        return $default;              // Default value (or NULL)
    }
    if (empty($name)) {
        return $USER->preference;     // Whole array
    }
    if (!isset($USER->preference[$name])) {
        return $default;              // Default value (or NULL)
    }
    return $USER->preference[$name];  // The single value
}


/// FUNCTIONS FOR HANDLING TIME ////////////////////////////////////////////

function make_timestamp($year, $month=1, $day=1, $hour=0, $minute=0, $second=0, $timezone=99) {
/// Given date parts in user time, produce a GMT timestamp

    $timezone = get_user_timezone($timezone);

    if (abs($timezone) > 13) {
        return mktime((int)$hour,(int)$minute,(int)$second,(int)$month,(int)$day,(int)$year);
    } else {
        $time = gmmktime((int)$hour,(int)$minute,(int)$second,(int)$month,(int)$day,(int)$year);
        return usertime($time, $timezone);  // This is GMT
    }
}

function format_time($totalsecs, $str=NULL) {
/// Given an amount of time in seconds, returns string
/// formatted nicely as months, days, hours etc as needed

    $totalsecs = abs($totalsecs);

    if (!$str) {  // Create the str structure the slow way
        $str->day   = get_string("day");
        $str->days  = get_string("days");
        $str->hour  = get_string("hour");
        $str->hours = get_string("hours");
        $str->min   = get_string("min");
        $str->mins  = get_string("mins");
        $str->sec   = get_string("sec");
        $str->secs  = get_string("secs");
    }

    $days      = floor($totalsecs/86400);
    $remainder = $totalsecs - ($days*86400);
    $hours     = floor($remainder/3600);
    $remainder = $remainder - ($hours*3600);
    $mins      = floor($remainder/60);
    $secs      = $remainder - ($mins*60);

    $ss = ($secs == 1)  ? $str->sec  : $str->secs;
    $sm = ($mins == 1)  ? $str->min  : $str->mins;
    $sh = ($hours == 1) ? $str->hour : $str->hours;
    $sd = ($days == 1)  ? $str->day  : $str->days;

    $odays = "";
    $ohours = "";
    $omins = "";
    $osecs = "";

    if ($days)  $odays  = "$days $sd";
    if ($hours) $ohours = "$hours $sh";
    if ($mins)  $omins  = "$mins $sm";
    if ($secs)  $osecs  = "$secs $ss";

    if ($days)  return "$odays $ohours";
    if ($hours) return "$ohours $omins";
    if ($mins)  return "$omins $osecs";
    if ($secs)  return "$osecs";
    return get_string("now");
}

function userdate($date, $format="", $timezone=99, $fixday = true) {
/// Returns a formatted string that represents a date in user time
/// WARNING: note that the format is for strftime(), not date().
/// Because of a bug in most Windows time libraries, we can't use
/// the nicer %e, so we have to use %d which has leading zeroes.
/// A lot of the fuss below is just getting rid of these leading
/// zeroes as efficiently as possible.
///
/// If parammeter fixday = true (default), then take off leading
/// zero from %d, else mantain it.

    if ($format == "") {
        $format = get_string("strftimedaydatetime");
    }

    $formatnoday = str_replace("%d", "DD", $format);
    if ($fixday) {
        $fixday = ($formatnoday != $format);
    }

    $timezone = get_user_timezone($timezone);

    if (abs($timezone) > 13) {
        if ($fixday) {
            $datestring = strftime($formatnoday, $date);
            $daystring  = str_replace(" 0", "", strftime(" %d", $date));
            $datestring = str_replace("DD", $daystring, $datestring);
        } else {
            $datestring = strftime($format, $date);
        }
    } else {
        $date = $date + (int)($timezone * 3600);
        if ($fixday) {
            $datestring = gmstrftime($formatnoday, $date);
            $daystring  = str_replace(" 0", "", gmstrftime(" %d", $date));
            $datestring = str_replace("DD", $daystring, $datestring);
        } else {
            $datestring = gmstrftime($format, $date);
        }
    }

    return $datestring;
}

function usergetdate($date, $timezone=99) {
/// Given a $date timestamp in GMT, returns an array
/// that represents the date in user time

    $timezone = get_user_timezone($timezone);

    if (abs($timezone) > 13) {
        return getdate($date);
    }
    //There is no gmgetdate so I have to fake it...
    $date = $date + (int)($timezone * 3600);
    $getdate["seconds"] = gmstrftime("%S", $date);
    $getdate["minutes"] = gmstrftime("%M", $date);
    $getdate["hours"]   = gmstrftime("%H", $date);
    $getdate["mday"]    = gmstrftime("%d", $date);
    $getdate["wday"]    = gmstrftime("%u", $date);
    $getdate["mon"]     = gmstrftime("%m", $date);
    $getdate["year"]    = gmstrftime("%Y", $date);
    $getdate["yday"]    = gmstrftime("%j", $date);
    $getdate["weekday"] = gmstrftime("%A", $date);
    $getdate["month"]   = gmstrftime("%B", $date);
    return $getdate;
}

function usertime($date, $timezone=99) {
/// Given a GMT timestamp (seconds since epoch), offsets it by
/// the timezone.  eg 3pm in India is 3pm GMT - 7 * 3600 seconds

    $timezone = get_user_timezone($timezone);
    if (abs($timezone) > 13) {
        return $date;
    }
    return $date - (int)($timezone * 3600);
}

function usergetmidnight($date, $timezone=99) {
/// Given a time, return the GMT timestamp of the most recent midnight
/// for the current user.

    $timezone = get_user_timezone($timezone);
    $userdate = usergetdate($date, $timezone);

    if (abs($timezone) > 13) {
        return mktime(0, 0, 0, $userdate["mon"], $userdate["mday"], $userdate["year"]);
    }

    $timemidnight = gmmktime (0, 0, 0, $userdate["mon"], $userdate["mday"], $userdate["year"]);
    return usertime($timemidnight, $timezone); // Time of midnight of this user's day, in GMT

}

function usertimezone($timezone=99) {
/// Returns a string that prints the user's timezone

    $timezone = get_user_timezone($timezone);

    if (abs($timezone) > 13) {
        return "server time";
    }
    if (abs($timezone) < 0.5) {
        return "GMT";
    }
    if ($timezone > 0) {
        return "GMT+$timezone";
    } else {
        return "GMT$timezone";
    }
}

function get_user_timezone($tz = 99) {
// Returns a float which represents the user's timezone difference from GMT in hours
// Checks various settings and picks the most dominant of those which have a value

    // Variables declared explicitly global here so that if we add
    // something later we won't forget to global it...
    $timezones = array(
        isset($GLOBALS['USER']->timezone) ? $GLOBALS['USER']->timezone : 99,
        isset($GLOBALS['CFG']->timezone) ? $GLOBALS['CFG']->timezone : 99,
        );
    while($tz == 99 && $next = each($timezones)) {
        $tz = (float)$next['value'];
    }

    return $tz;
}

/// USER AUTHENTICATION AND LOGIN ////////////////////////////////////////

function require_login($courseid=0) {
/// This function checks that the current user is logged in, and optionally
/// whether they are "logged in" or allowed to be in a particular course.
/// If not, then it redirects them to the site login or course enrolment.

    global $CFG, $SESSION, $USER, $FULLME, $MoodleSession;

    // First check that the user is logged in to the site.
    if (! (isset($USER->loggedin) and $USER->confirmed and ($USER->site == $CFG->wwwroot)) ) { // They're not
        $SESSION->wantsurl = $FULLME;
        if (!empty($_SERVER["HTTP_REFERER"])) {
            $SESSION->fromurl  = $_SERVER["HTTP_REFERER"];
        }
        $USER = NULL;
        redirect("$CFG->wwwroot/login/index.php");
        die;
    }

    // Check that the user account is properly set up
    if (user_not_fully_set_up($USER)) {
        $site = get_site();
        redirect("$CFG->wwwroot/user/edit.php?id=$USER->id&course=$site->id");
        die;
    }

    // Next, check if the user can be in a particular course
    if ($courseid) {
        if (!empty($USER->student[$courseid]) or !empty($USER->teacher[$courseid]) or !empty($USER->admin)) {
            if (isset($USER->realuser)) {   // Make sure the REAL person can also access this course
                if (!isteacher($courseid, $USER->realuser)) {
                    print_header();
                    notice(get_string("studentnotallowed", "", fullname($USER, true)), "$CFG->wwwroot/");
                }

            } else {  // just update their last login time
                update_user_in_db();
            }
            return;   // user is a member of this course.
        }
        if (! $course = get_record("course", "id", $courseid)) {
            error("That course doesn't exist");
        }
        if (!$course->visible) {
            print_header();
            notice(get_string("studentnotallowed", "", fullname($USER, true)), "$CFG->wwwroot/");
        }
        if ($USER->username == "guest") {
            switch ($course->guest) {
                case 0: // Guests not allowed
                    print_header();
                    notice(get_string("guestsnotallowed", "", $course->fullname));
                    break;
                case 1: // Guests allowed
                    update_user_in_db();
                    return;
                case 2: // Guests allowed with key (drop through)
                    break;
            }
        }

        // Currently not enrolled in the course, so see if they want to enrol
        $SESSION->wantsurl = $FULLME;
        redirect("$CFG->wwwroot/course/enrol.php?id=$courseid");
        die;
    }
}

function require_course_login($course, $autologinguest=true) {
// This is a weaker version of require_login which only requires login
// when called from within a course rather than the site page, unless
// the forcelogin option is turned on.
    global $CFG;
    if ($CFG->forcelogin) {
      require_login();
    }
    if ($course->category) {
      require_login($course->id, $autologinguest);
    }
}

function update_user_login_times() {
    global $USER;

    $USER->lastlogin = $user->lastlogin = $USER->currentlogin;
    $USER->currentlogin = $user->currentlogin = time();

    $user->id = $USER->id;

    return update_record("user", $user);
}

function user_not_fully_set_up($user) {
    return ($user->username != "guest" and (empty($user->firstname) or empty($user->lastname) or empty($user->email)));
}

function update_login_count() {
/// Keeps track of login attempts

    global $SESSION;

    $max_logins = 10;

    if (empty($SESSION->logincount)) {
        $SESSION->logincount = 1;
    } else {
        $SESSION->logincount++;
    }

    if ($SESSION->logincount > $max_logins) {
        unset($SESSION->wantsurl);
        error(get_string("errortoomanylogins"));
    }
}

function reset_login_count() {
/// Resets login attempts
    global $SESSION;

    $SESSION->logincount = 0;
}

function check_for_restricted_user($username=NULL, $redirect="") {
    global $CFG, $USER;

    if (!$username) {
        if (!empty($USER->username)) {
            $username = $USER->username;
        } else {
            return false;
        }
    }

    if (!empty($CFG->restrictusers)) {
        $names = explode(',', $CFG->restrictusers);
        if (in_array($username, $names)) {
            error(get_string("restricteduser", "error", fullname($USER)), $redirect);
        }
    }
}

function isadmin($userid=0) {
/// Is the user an admin?
    global $USER;
    static $admins = array();
    static $nonadmins = array();

    if (!$userid){
        if (empty($USER->id)) {
            return false;
        }
        $userid = $USER->id;
    }

    if (in_array($userid, $admins)) {
        return true;
    } else if (in_array($userid, $nonadmins)) {
        return false;
    } else if (record_exists("user_admins", "userid", $userid)){
        $admins[] = $userid;
        return true;
    } else {
        $nonadmins[] = $userid;
        return false;
    }
}

function isteacher($courseid, $userid=0, $includeadmin=true) {
/// Is the user a teacher or admin?
    global $USER;

    if ($includeadmin and isadmin($userid)) {  // admins can do anything the teacher can
        return true;
    }

    if (!$userid) {
        return !empty($USER->teacher[$courseid]);
    }

    return record_exists("user_teachers", "userid", $userid, "course", $courseid);
}

function isteacheredit($courseid, $userid=0) {
/// Is the user allowed to edit this course?
    global $USER;

    if (isadmin($userid)) {  // admins can do anything
        return true;
    }

    if (!$userid) {
        return !empty($USER->teacheredit[$courseid]);
    }

    return get_field("user_teachers", "editall", "userid", $userid, "course", $courseid);
}

function iscreator ($userid=0) {
/// Can user create new courses?
    global $USER;
    if (empty($USER->id)) {
        return false;
    }
    if (isadmin($userid)) {  // admins can do anything
        return true;
    }
    if (empty($userid)) {
        return record_exists("user_coursecreators", "userid", $USER->id);
    }

    return record_exists("user_coursecreators", "userid", $userid);
}

function isstudent($courseid, $userid=0) {
/// Is the user a student in this course?
    global $USER;

    if (!$userid) {
        return !empty($USER->student[$courseid]);
    }

  //  $timenow = time();   // todo:  add time check below

    return record_exists("user_students", "userid", $userid, "course", $courseid);
}

function isguest($userid=0) {
/// Is the user a guest?
    global $USER;

    if (!$userid) {
        if (empty($USER->username)) {
            return false;
        }
        return ($USER->username == "guest");
    }

    return record_exists("user", "id", $userid, "username", "guest");
}


function isediting($courseid, $user=NULL) {
/// Is the current user in editing mode?
    global $USER;
    if (!$user){
        $user = $USER;
    }
    if (empty($user->editing)) {
        return false;
    }
    return ($user->editing and isteacher($courseid, $user->id));
}

function ismoving($courseid) {
/// Is the current user currently moving an activity?
    global $USER;

    if (!empty($USER->activitycopy)) {
        return ($USER->activitycopycourse == $courseid);
    }
    return false;
}

function fullname($user, $override=false) {
/// Given an object containing firstname and lastname
/// values, this function returns a string with the
/// full name of the person.
/// The result may depend on system settings
/// or language.  'override' will force both names
/// to be used even if system settings specify one.

    global $CFG, $SESSION;

    if (!empty($SESSION->fullnamedisplay)) {
        $CFG->fullnamedisplay = $SESSION->fullnamedisplay;
    }

    if ($CFG->fullnamedisplay == 'firstname lastname') {
        return "$user->firstname $user->lastname";

    } else if ($CFG->fullnamedisplay == 'lastname firstname') {
        return "$user->lastname $user->firstname";

    } else if ($CFG->fullnamedisplay == 'firstname') {
        if ($override) {
            return get_string('fullnamedisplay', '', $user);
        } else {
            return $user->firstname;
        }
    }

    return get_string('fullnamedisplay', '', $user);
}


function set_moodle_cookie($thing) {
/// Sets a moodle cookie with an encrypted string
    global $CFG;

    $cookiename = 'MOODLEID_'.$CFG->sessioncookie;

    $days = 60;
    $seconds = 60*60*24*$days;

    setCookie($cookiename, "", time() - 3600, "/");
    setCookie($cookiename, rc4encrypt($thing), time()+$seconds, "/");
}


function get_moodle_cookie() {
/// Gets a moodle cookie with an encrypted string
    global $CFG;

    $cookiename = 'MOODLEID_'.$CFG->sessioncookie;

    if (empty($_COOKIE[$cookiename])) {
        return "";
    } else {
        return rc4decrypt($_COOKIE[$cookiename]);
    }
}

function is_internal_auth() {
/// Returns true if an internal authentication method is being used.

    global $CFG;

    return ($CFG->auth == "email" || $CFG->auth == "none" || $CFG->auth == "manual");
}

function create_user_record($username, $password) {
/// Creates a bare-bones user record
    global $REMOTE_ADDR, $CFG;
    //just in case check text case
    $username = trim(moodle_strtolower($username));
    if (function_exists(auth_get_userinfo)) {
        if ($newinfo = auth_get_userinfo($username)) {
            foreach ($newinfo as $key => $value){
                $newuser->$key = addslashes(stripslashes($value)); // Just in case
            }
        }
    }

    $newuser->username = $username;
    $newuser->password = md5($password);
    $newuser->lang = $CFG->lang;
    $newuser->confirmed = 1;
    $newuser->lastIP = $REMOTE_ADDR;
    $newuser->timemodified = time();

    if (insert_record("user", $newuser)) {
        return get_user_info_from_db("username", $username);
    }
    return false;
}


function guest_user() {
    global $CFG;

    if ($newuser = get_record("user", "username", "guest")) {
        $newuser->loggedin = true;
        $newuser->confirmed = 1;
        $newuser->site = $CFG->wwwroot;
        $newuser->lang = $CFG->lang;
    }

    return $newuser;
}

function authenticate_user_login($username, $password) {
/// Given a username and password, this function looks them
/// up using the currently selected authentication mechanism,
/// and if the authentication is successful, it returns a
/// valid $user object from the 'user' table.
///
/// Uses auth_ functions from the currently active auth module

    global $CFG;

    $md5password = md5($password);

    if (empty($CFG->auth)) {
        $CFG->auth = "email";    // Default authentication module
    }

    if ($username == "guest") {
        $CFG->auth = "none";     // Guest account always internal
    }

    // If this is the admin, then just use internal methods
    // Doing this first (even though it's less efficient) because
    // the chosen authentication method might hang and lock the
    // admin out.
    if (adminlogin($username, $md5password)) {
        return get_user_info_from_db("username", $username);
    }

    // OK, the user is a normal user, so try and authenticate them
    require_once("$CFG->dirroot/auth/$CFG->auth/lib.php");

    if (auth_user_login($username, $password)) {  // Successful authentication
        if ($user = get_user_info_from_db("username", $username)) {
            if ($md5password <> $user->password) {   // Update local copy of password for reference
                set_field("user", "password", $md5password, "username", $username);
            }
        } else {
            $user = create_user_record($username, $password);
        }

        if (function_exists('auth_iscreator')) {    // Check if the user is a creator
            if (auth_iscreator($username)) {
                 if (! record_exists("user_coursecreators", "userid", $user->id)) {
                      $cdata['userid']=$user->id;
                      $creator = insert_record("user_coursecreators",$cdata);
                      if (! $creator) {
                          error("Cannot add user to course creators.");
                      }
                  }
            } else {
                 if ( record_exists("user_coursecreators", "userid", $user->id)) {
                      $creator = delete_records("user_coursecreators", "userid", $user->id);
                      if (! $creator) {
                          error("Cannot remove user from course creators.");
                      }
                 }
            }
         }

        return $user;
    } else {
        return false;
    }
}

function enrol_student($userid, $courseid) {
/// Enrols a student in a given course

    if (!record_exists("user_students", "userid", $userid, "course", $courseid)) {
        if (record_exists("user", "id", $userid)) {
            $student->userid = $userid;
            $student->course = $courseid;
            $student->start = 0;
            $student->end = 0;
            $student->time = time();
            return insert_record("user_students", $student);
        }
        return false;
    }
    return true;
}

function unenrol_student($userid, $courseid=0) {
/// Unenrols a student from a given course

    if ($courseid) {
        /// First delete any crucial stuff that might still send mail
        if ($forums = get_records("forum", "course", $courseid)) {
            foreach ($forums as $forum) {
                delete_records("forum_subscriptions", "forum", $forum->id, "userid", $userid);
            }
        }
        if ($groups = get_groups($courseid, $userid)) {
            foreach ($groups as $group) {
                delete_records("groups_members", "groupid", $group->id, "userid", $userid);
            }
        }
        return delete_records("user_students", "userid", $userid, "course", $courseid);

    } else {
        delete_records("forum_subscriptions", "userid", $userid);
        delete_records("groups_members", "userid", $userid);
        return delete_records("user_students", "userid", $userid);
    }
}

function add_teacher($userid, $courseid) {
/// Add a teacher to a given course

    if (!record_exists("user_teachers", "userid", $userid, "course", $courseid)) {
        if (record_exists("user", "id", $userid)) {
            $teacher->userid = $userid;
            $teacher->course = $courseid;
            $teacher->editall = 1;
            $teacher->role = "";
            if (record_exists("user_teachers", "course", $courseid)) {
                $teacher->authority = 2;
            } else {
                $teacher->authority = 1;
            }
            delete_records("user_students", "userid", $userid, "course", $courseid); // Unenrol as student

            return insert_record("user_teachers", $teacher);
        }
        return false;
    }
    return true;
}

function remove_teacher($userid, $courseid=0) {
/// Removes a teacher from a given course (or ALL courses)
/// Does not delete the user account
    if ($courseid) {
        /// First delete any crucial stuff that might still send mail
        if ($forums = get_records("forum", "course", $courseid)) {
            foreach ($forums as $forum) {
                delete_records("forum_subscriptions", "forum", $forum->id, "userid", $userid);
            }
        }
        return delete_records("user_teachers", "userid", $userid, "course", $courseid);
    } else {
        delete_records("forum_subscriptions", "userid", $userid);
        return delete_records("user_teachers", "userid", $userid);
    }
}


function add_creator($userid) {
/// Add a creator to the site

    if (!record_exists("user_admins", "userid", $userid)) {
        if (record_exists("user", "id", $userid)) {
            $creator->userid = $userid;
            return insert_record("user_coursecreators", $creator);
        }
        return false;
    }
    return true;
}

function remove_creator($userid) {
/// Removes a creator from a site
    global $db;

    return delete_records("user_coursecreators", "userid", $userid);
}

function add_admin($userid) {
/// Add an admin to the site

    if (!record_exists("user_admins", "userid", $userid)) {
        if (record_exists("user", "id", $userid)) {
            $admin->userid = $userid;
            return insert_record("user_admins", $admin);
        }
        return false;
    }
    return true;
}

function remove_admin($userid) {
/// Removes an admin from a site
    global $db;

    return delete_records("user_admins", "userid", $userid);
}


function remove_course_contents($courseid, $showfeedback=true) {
/// Clear a course out completely, deleting all content
/// but don't delete the course itself

    global $CFG, $THEME, $USER, $SESSION;

    $result = true;

    if (! $course = get_record("course", "id", $courseid)) {
        error("Course ID was incorrect (can't find it)");
    }

    $strdeleted = get_string("deleted");

    // First delete every instance of every module

    if ($allmods = get_records("modules") ) {
        foreach ($allmods as $mod) {
            $modname = $mod->name;
            $modfile = "$CFG->dirroot/mod/$modname/lib.php";
            $moddelete = $modname."_delete_instance";       // Delete everything connected to an instance
            $moddeletecourse = $modname."_delete_course";   // Delete other stray stuff (uncommon)
            $count=0;
            if (file_exists($modfile)) {
                include_once($modfile);
                if (function_exists($moddelete)) {
                    if ($instances = get_records($modname, "course", $course->id)) {
                        foreach ($instances as $instance) {
                            if ($moddelete($instance->id)) {
                                $count++;
                            } else {
                                notify("Could not delete $modname instance $instance->id ($instance->name)");
                                $result = false;
                            }
                        }
                    }
                } else {
                    notify("Function $moddelete() doesn't exist!");
                    $result = false;
                }

                if (function_exists($moddeletecourse)) {
                    $moddeletecourse($course);
                }
            }
            if ($showfeedback) {
                notify("$strdeleted $count x $modname");
            }
        }
    } else {
        error("No modules are installed!");
    }

    // Delete any user stuff

    if (delete_records("user_students", "course", $course->id)) {
        if ($showfeedback) {
            notify("$strdeleted user_students");
        }
    } else {
        $result = false;
    }

    if (delete_records("user_teachers", "course", $course->id)) {
        if ($showfeedback) {
            notify("$strdeleted user_teachers");
        }
    } else {
        $result = false;
    }

    // Delete any groups

    if ($groups = get_records("groups", "courseid", $course->id)) {
        foreach ($groups as $group) {
            if (delete_records("groups_members", "groupid", $group->id)) {
                if ($showfeedback) {
                    notify("$strdeleted groups_members");
                }
            } else {
                $result = false;
            }
            if (delete_records("groups", "id", $group->id)) {
                if ($showfeedback) {
                    notify("$strdeleted groups");
                }
            } else {
                $result = false;
            }
        }
    }

    // Delete events

    if (delete_records("event", "courseid", $course->id)) {
        if ($showfeedback) {
            notify("$strdeleted event");
        }
    } else {
        $result = false;
    }

    // Delete logs

    if (delete_records("log", "course", $course->id)) {
        if ($showfeedback) {
            notify("$strdeleted log");
        }
    } else {
        $result = false;
    }

    // Delete any course stuff

    if (delete_records("course_sections", "course", $course->id)) {
        if ($showfeedback) {
            notify("$strdeleted course_sections");
        }
    } else {
        $result = false;
    }

    if (delete_records("course_modules", "course", $course->id)) {
        if ($showfeedback) {
            notify("$strdeleted course_modules");
        }
    } else {
        $result = false;
    }

    return $result;

}


/// GROUPS /////////////////////////////////////////////////////////


/**
* Returns a boolean: is the user a member of the given group?
*
* @param    type description
*/
function ismember($groupid, $userid=0) {
    global $USER;

    if (!$groupid) {   // No point doing further checks
        return false;
    }

    if (!$userid) {
        if (empty($USER->groupmember)) {
            return false;
        }
        foreach ($USER->groupmember as $courseid => $mgroupid) {
            if ($mgroupid == $groupid) {
                return true;
            }
        }
        return false;
    }

    return record_exists("groups_members", "groupid", $groupid, "userid", $userid);
}

/**
* Returns the group ID of the current user in the given course
*
* @param    type description
*/
function mygroupid($courseid) {
    global $USER;

    if (empty($USER->groupmember[$courseid])) {
        return 0;
    } else {
        return $USER->groupmember[$courseid];
    }
}

/**
* For a given course, and possibly course module, determine
* what the current default groupmode is:
* NOGROUPS, SEPARATEGROUPS or VISIBLEGROUPS
*
* @param    type description
*/
function groupmode($course, $cm=null) {

    if ($cm and !$course->groupmodeforce) {
        return $cm->groupmode;
    }
    return $course->groupmode;
}


/**
* Sets the current group in the session variable
*
* @param    type description
*/
function set_current_group($courseid, $groupid) {
    global $SESSION;

    return $SESSION->currentgroup[$courseid] = $groupid;
}


/**
* Gets the current group for the current user as an id or an object
*
* @param    type description
*/
function get_current_group($courseid, $full=false) {
    global $SESSION, $USER;

    if (empty($SESSION->currentgroup[$courseid])) {
        if (empty($USER->groupmember[$courseid])) {
            return 0;
        } else {
            $SESSION->currentgroup[$courseid] = $USER->groupmember[$courseid];
        }
    }

    if ($full) {
        return get_record('groups', 'id', $SESSION->currentgroup[$courseid]);
    } else {
        return $SESSION->currentgroup[$courseid];
    }
}

/**
* A combination function to make it easier for modules
* to set up groups.
*
* It will use a given "groupid" parameter and try to use
* that to reset the current group for the user.
*
* @param    type description
*/
function get_and_set_current_group($course, $groupmode, $groupid=-1) {

    if (!$groupmode) {   // Groups don't even apply
        return false;
    }

    $currentgroupid = get_current_group($course->id);

    if ($groupid < 0) {  // No change was specified
        return $currentgroupid;
    }

    if ($groupid) {      // Try to change the current group to this groupid
        if ($group = get_record('groups', 'id', $groupid, 'courseid', $course->id)) { // Exists
            if (isteacheredit($course->id)) {          // Sets current default group
                $currentgroupid = set_current_group($course->id, $group->id);

            } else if ($groupmode == VISIBLEGROUPS) {  // All groups are visible
                $currentgroupid = $group->id;
            }
        }
    } else {             // When groupid = 0 it means show ALL groups
        if (isteacheredit($course->id)) {          // Sets current default group
            $currentgroupid = set_current_group($course->id, 0);

        } else if ($groupmode == VISIBLEGROUPS) {  // All groups are visible
            $currentgroupid = 0;
        }
    }

    return $currentgroupid;
}


/**
* A big combination function to make it easier for modules
* to set up groups.
*
* Terminates if the current user shouldn't be looking at this group
* Otherwise returns the current group if there is one
* Otherwise returns false if groups aren't relevant
*
* @param    type description
*/
function setup_and_print_groups($course, $groupmode, $urlroot) {

    if (isset($_GET['group'])) {
        $changegroup = $_GET['group'];  /// 0 or higher
    } else {
        $changegroup = -1;              /// This means no group change was specified
    }

    $currentgroup = get_and_set_current_group($course, $groupmode, $changegroup);

    if ($currentgroup === false) {
        return false;
    }

    if ($groupmode == SEPARATEGROUPS and !isteacheredit($course->id) and !$currentgroup) {
        print_heading(get_string('notingroup'));
        print_footer($course);
        exit;
    }

    if ($groupmode == VISIBLEGROUPS or ($groupmode and isteacheredit($course->id))) {
        if ($groups = get_records_menu("groups", "courseid", $course->id, "name ASC", "id,name")) {
            echo '<div align="center">';
            print_group_menu($groups, $groupmode, $currentgroup, $urlroot);
            echo '</div>';
        }
    }

    return $currentgroup;
}



/// CORRESPONDENCE  ////////////////////////////////////////////////

function email_to_user($user, $from, $subject, $messagetext, $messagehtml="", $attachment="", $attachname="") {
///  user        - a user record as an object
///  from        - a user record as an object
///  subject     - plain text subject line of the email
///  messagetext - plain text version of the message
///  messagehtml - complete html version of the message (optional)
///  attachment  - a file on the filesystem, relative to $CFG->dataroot
///  attachname  - the name of the file (extension indicates MIME)

    global $CFG, $_SERVER;

    global $course;                // This is a bit of an ugly hack to be gotten rid of later
    if (!empty($course->lang)) {   // Course language is defined
        $CFG->courselang = $course->lang;
    }

    include_once("$CFG->libdir/phpmailer/class.phpmailer.php");

    if (empty($user)) {
        return false;
    }

    if (!empty($user->emailstop)) {
        return false;
    }

    $mail = new phpmailer;

    $mail->Version = "Moodle $CFG->version";           // mailer version
    $mail->PluginDir = "$CFG->libdir/phpmailer/";      // plugin directory (eg smtp plugin)


    if (current_language() != "en") {
        $mail->CharSet = get_string("thischarset");
    }

    if ($CFG->smtphosts == "qmail") {
        $mail->IsQmail();                              // use Qmail system

    } else if (empty($CFG->smtphosts)) {
        $mail->IsMail();                               // use PHP mail() = sendmail

    } else {
        $mail->IsSMTP();                               // use SMTP directly
        if ($CFG->debug > 7) {
            echo "<pre>\n";
            $mail->SMTPDebug = true;
        }
        $mail->Host = "$CFG->smtphosts";               // specify main and backup servers

        if ($CFG->smtpuser) {                          // Use SMTP authentication
            $mail->SMTPAuth = true;
            $mail->Username = $CFG->smtpuser;
            $mail->Password = $CFG->smtppass;
        }
    }

    $adminuser = get_admin();

    $mail->Sender   = "$adminuser->email";

    $mail->From     = "$from->email";
    $mail->FromName = fullname($from);
    $mail->Subject  =  stripslashes($subject);

    $mail->AddAddress("$user->email", fullname($user) );

    $mail->WordWrap = 79;                               // set word wrap

    if (!empty($from->precedence)) {
        $mail->Precedence = $from->precedence;          // set precedence level eg "bulk" "list" or "junk"
    }

    if ($messagehtml) {
        $mail->IsHTML(true);
        $mail->Encoding = "quoted-printable";           // Encoding to use
        $mail->Body    =  $messagehtml;
        $mail->AltBody =  "\n$messagetext\n";
    } else {
        $mail->IsHTML(false);
        $mail->Body =  "\n$messagetext\n";
    }

    if ($attachment && $attachname) {
        if (ereg( "\\.\\." ,$attachment )) {    // Security check for ".." in dir path
            $mail->AddAddress("$adminuser->email", fullname($adminuser) );
            $mail->AddStringAttachment("Error in attachment.  User attempted to attach a filename with a unsafe name.", "error.txt", "8bit", "text/plain");
        } else {
            include_once("$CFG->dirroot/files/mimetypes.php");
            $mimetype = mimeinfo("type", $attachname);
            $mail->AddAttachment("$CFG->dataroot/$attachment", "$attachname", "base64", "$mimetype");
        }
    }

    if ($mail->Send()) {
        return true;
    } else {
        echo "ERROR: $mail->ErrorInfo\n";
        $site = get_site();
        add_to_log($site->id, "library", "mailer", $_SERVER["REQUEST_URI"], "ERROR: $mail->ErrorInfo");
        return false;
    }
}

function reset_password_and_mail($user) {

    global $CFG;

    $site  = get_site();
    $from = get_admin();

    $newpassword = generate_password();

    if (! set_field("user", "password", md5($newpassword), "id", $user->id) ) {
        error("Could not set user password!");
    }

    $a->firstname = $user->firstname;
    $a->sitename = $site->fullname;
    $a->username = $user->username;
    $a->newpassword = $newpassword;
    $a->link = "$CFG->wwwroot/login/change_password.php";
    $a->signoff = fullname($from, true)." ($from->email)";

    $message = get_string("newpasswordtext", "", $a);

    $subject  = "$site->fullname: ".get_string("changedpassword");

    return email_to_user($user, $from, $subject, $message);

}

function send_confirmation_email($user) {

    global $CFG;

    $site = get_site();
    $from = get_admin();

    $data->firstname = $user->firstname;
    $data->sitename = $site->fullname;
    $data->link = "$CFG->wwwroot/login/confirm.php?p=$user->secret&s=$user->username";
    $data->admin = fullname($from)." ($from->email)";

    $message = get_string("emailconfirmation", "", $data);
    $subject = get_string("emailconfirmationsubject", "", $site->fullname);

    $messagehtml = text_to_html($message, false, false, true);

    return email_to_user($user, $from, $subject, $message, $messagehtml);

}

function send_password_change_confirmation_email($user) {

    global $CFG;

    $site = get_site();
    $from = get_admin();

    $data->firstname = $user->firstname;
    $data->sitename = $site->fullname;
    $data->link = "$CFG->wwwroot/login/forgot_password.php?p=$user->secret&s=$user->username";
    $data->admin = fullname($from)." ($from->email)";

    $message = get_string("emailpasswordconfirmation", "", $data);
    $subject = get_string("emailpasswordconfirmationsubject", "", $site->fullname);

    return email_to_user($user, $from, $subject, $message);

}




/// FILE HANDLING  /////////////////////////////////////////////


function make_upload_directory($directory) {
/// $directory = a string of directory names under $CFG->dataroot
/// eg  stuff/assignment/1
/// Returns full directory if successful, false if not

    global $CFG;

    $currdir = $CFG->dataroot;

    umask(0000);

    if (!file_exists($currdir)) {
        if (! mkdir($currdir, $CFG->directorypermissions)) {
            notify("ERROR: You need to create the directory $currdir with web server write access");
            return false;
        }
    }

    $dirarray = explode("/", $directory);

    foreach ($dirarray as $dir) {
        $currdir = "$currdir/$dir";
        if (! file_exists($currdir)) {
            if (! mkdir($currdir, $CFG->directorypermissions)) {
                notify("ERROR: Could not find or create a directory ($currdir)");
                return false;
            }
            @chmod($currdir, $CFG->directorypermissions);  // Just in case mkdir didn't do it
        }
    }

    return $currdir;
}


function make_mod_upload_directory($courseid) {
/// Makes an upload directory for a particular module
    global $CFG;

    if (! $moddata = make_upload_directory("$courseid/$CFG->moddata")) {
        return false;
    }

    $strreadme = get_string("readme");

    if (file_exists("$CFG->dirroot/lang/$CFG->lang/docs/module_files.txt")) {
        copy("$CFG->dirroot/lang/$CFG->lang/docs/module_files.txt", "$moddata/$strreadme.txt");
    } else {
        copy("$CFG->dirroot/lang/en/docs/module_files.txt", "$moddata/$strreadme.txt");
    }
    return $moddata;
}


function valid_uploaded_file($newfile) {
/// Returns current name of file on disk if true
    if (empty($newfile)) {
        return "";
    }
    if (is_uploaded_file($newfile['tmp_name']) and $newfile['size'] > 0) {
        return $newfile['tmp_name'];
    } else {
        return "";
    }
}

function get_max_upload_file_size($sitebytes=0, $coursebytes=0, $modulebytes=0) {
/// Returns the maximum size for uploading files
/// There are seven possible upload limits:
///
/// 1) in Apache using LimitRequestBody (no way of checking or changing this)
/// 2) in php.ini for 'upload_max_filesize' (can not be changed inside PHP)
/// 3) in .htaccess for 'upload_max_filesize' (can not be changed inside PHP)
/// 4) in php.ini for 'post_max_size' (can not be changed inside PHP)
/// 5) by the Moodle admin in $CFG->maxbytes
/// 6) by the teacher in the current course $course->maxbytes
/// 7) by the teacher for the current module, eg $assignment->maxbytes
///
/// These last two are passed to this function as arguments (in bytes).
/// Anything defined as 0 is ignored.
/// The smallest of all the non-zero numbers is returned.

    if (! $filesize = ini_get("upload_max_filesize")) {
        $filesize = "5M";
    }
    $minimumsize = get_real_size($filesize);

    if ($postsize = ini_get("post_max_size")) {
        $postsize = get_real_size($postsize);
        if ($postsize < $minimumsize) {
            $minimumsize = $postsize;
        }
    }

    if ($sitebytes and $sitebytes < $minimumsize) {
        $minimumsize = $sitebytes;
    }

    if ($coursebytes and $coursebytes < $minimumsize) {
        $minimumsize = $coursebytes;
    }

    if ($modulebytes and $modulebytes < $minimumsize) {
        $minimumsize = $modulebytes;
    }

    return $minimumsize;
}

function get_max_upload_sizes($sitebytes=0, $coursebytes=0, $modulebytes=0) {
/// Related to the above function - this function returns an
/// array of possible sizes in an array, translated to the
/// local language.

    if (!$maxsize = get_max_upload_file_size($sitebytes, $coursebytes, $modulebytes)) {
        return array();
    }

    $filesize[$maxsize] = display_size($maxsize);

    $sizelist = array(10240, 51200, 102400, 512000, 1048576, 2097152,
                      5242880, 10485760, 20971520, 52428800, 104857600);

    foreach ($sizelist as $sizebytes) {
       if ($sizebytes < $maxsize) {
           $filesize[$sizebytes] = display_size($sizebytes);
       }
    }

    krsort($filesize, SORT_NUMERIC);

    return $filesize;
}

function get_directory_list($rootdir, $excludefile="", $descend=true, $getdirs=false, $getfiles=true) {
/// Returns an array with all the filenames in
/// all subdirectories, relative to the given rootdir.
/// If excludefile is defined, then that file/directory is ignored
/// If getdirs is true, then (sub)directories are included in the output
/// If getfiles is true, then files are included in the output
/// (at least one of these must be true!)

    $dirs = array();

    if (!$getdirs and !$getfiles) {   // Nothing to show
        return $dirs;
    }

    if (!is_dir($rootdir)) {          // Must be a directory
        return $dirs;
    }

    if (!$dir = opendir($rootdir)) {  // Can't open it for some reason
        return $dirs;
    }

    while (false !== ($file = readdir($dir))) {
        $firstchar = substr($file, 0, 1);
        if ($firstchar == "." or $file == "CVS" or $file == $excludefile) {
            continue;
        }
        $fullfile = "$rootdir/$file";
        if (filetype($fullfile) == "dir") {
            if ($getdirs) {
                $dirs[] = $file;
            }
            if ($descend) {
                $subdirs = get_directory_list($fullfile, $excludefile, $descend, $getdirs, $getfiles);
                foreach ($subdirs as $subdir) {
                    $dirs[] = "$file/$subdir";
                }
            }
        } else if ($getfiles) {
            $dirs[] = $file;
        }
    }
    closedir($dir);

    asort($dirs);

    return $dirs;
}

function get_directory_size($rootdir, $excludefile="") {
/// Adds up all the files in a directory and works out the size

    $size = 0;

    if (!is_dir($rootdir)) {          // Must be a directory
        return $dirs;
    }

    if (!$dir = @opendir($rootdir)) {  // Can't open it for some reason
        return $dirs;
    }

    while (false !== ($file = readdir($dir))) {
        $firstchar = substr($file, 0, 1);
        if ($firstchar == "." or $file == "CVS" or $file == $excludefile) {
            continue;
        }
        $fullfile = "$rootdir/$file";
        if (filetype($fullfile) == "dir") {
            $size += get_directory_size($fullfile, $excludefile);
        } else {
            $size += filesize($fullfile);
        }
    }
    closedir($dir);

    return $size;
}

function get_real_size($size=0) {
/// Converts numbers like 10M into bytes
    if (!$size) {
        return 0;
    }
    $scan['MB'] = 1048576;
    $scan['Mb'] = 1048576;
    $scan['M'] = 1048576;
    $scan['m'] = 1048576;
    $scan['KB'] = 1024;
    $scan['Kb'] = 1024;
    $scan['K'] = 1024;
    $scan['k'] = 1024;

    while (list($key) = each($scan)) {
        if ((strlen($size)>strlen($key))&&(substr($size, strlen($size) - strlen($key))==$key)) {
            $size = substr($size, 0, strlen($size) - strlen($key)) * $scan[$key];
            break;
        }
    }
    return $size;
}

function display_size($size) {
/// Converts bytes into display form

    static $gb,$mb,$kb,$b;

    if (empty($gb)) {
        $gb = get_string('sizegb');
        $mb = get_string('sizemb');
        $kb = get_string('sizekb');
        $b  = get_string('sizeb');
    }

    if ($size >= 1073741824) {
        $size = round($size / 1073741824 * 10) / 10 . $gb;
    } else if ($size >= 1048576) {
        $size = round($size / 1048576 * 10) / 10 . $mb;
    } else if ($size >= 1024) {
        $size = round($size / 1024 * 10) / 10 . $kb;
    } else {
        $size = $size ." $b";
    }
    return $size;
}

function clean_filename($string) {
/// Cleans a given filename by removing suspicious or troublesome characters
/// Only these are allowed:
///    alphanumeric _ - . 

    $string = eregi_replace("\.\.+", "", $string);
    $string = preg_replace('/[^\.a-zA-Z\d\_-]/','_', $string ); // only allowed chars
    $string = eregi_replace("_+", "_", $string);
    return    $string;
}


/// STRING TRANSLATION  ////////////////////////////////////////

function current_language() {
/// Returns the code for the current language
    global $CFG, $USER, $SESSION;

    if (!empty($CFG->courselang)) {    // Course language can override all other settings for this page
        return $CFG->courselang;

    } else if (!empty($SESSION->lang)) {    // Session language can override other settings
        return $SESSION->lang;

    } else if (!empty($USER->lang)) {    // User language can override site language
        return $USER->lang;

    } else {
        return $CFG->lang;
    }
}

function print_string($identifier, $module="", $a=NULL) {
/// Given a string to translate - prints it out.
    echo get_string($identifier, $module, $a);
}

function get_string($identifier, $module="", $a=NULL) {
/// Return the translated string specified by $identifier as
/// for $module.  Uses the same format files as STphp.
/// $a is an object, string or number that can be used
/// within translation strings
///
/// eg "hello \$a->firstname \$a->lastname"
/// or "hello \$a"

    global $CFG;

    global $course;     /// Not a nice hack, but quick
    if (empty($CFG->courselang)) {
        if (!empty($course->lang)) {
            $CFG->courselang = $course->lang;
        }
    }

    $lang = current_language();

    if ($module == "") {
        $module = "moodle";
    }

    $langpath = "$CFG->dirroot/lang";
    $langfile = "$langpath/$lang/$module.php";

    // Look for the string - if found then return it

    if (file_exists($langfile)) {
        if ($result = get_string_from_file($identifier, $langfile, "\$resultstring")) {
            eval($result);
            return $resultstring;
        }
    }

    // If it's a module, then look within the module pack itself mod/xxxx/lang/en/module.php

    if ($module != "moodle") {
        $modlangpath = "$CFG->dirroot/mod/$module/lang";
        $langfile = "$modlangpath/$lang/$module.php";
        if (file_exists($langfile)) {
            if ($result = get_string_from_file($identifier, $langfile, "\$resultstring")) {
                eval($result);
                return $resultstring;
            }
        }
    }

    // If the preferred language was English we can abort now
    if ($lang == "en") {
        return "[[$identifier]]";
    }

    // Is a parent language defined?  If so, try it.

    if ($result = get_string_from_file("parentlanguage", "$langpath/$lang/moodle.php", "\$parentlang")) {
        eval($result);
        if (!empty($parentlang)) {
            $langfile = "$langpath/$parentlang/$module.php";
            if (file_exists($langfile)) {
                if ($result = get_string_from_file($identifier, $langfile, "\$resultstring")) {
                    eval($result);
                    return $resultstring;
                }
            }
        }
    }

    // Our only remaining option is to try English

    $langfile = "$langpath/en/$module.php";
    if (!file_exists($langfile)) {
        return "ERROR: No lang file ($langpath/en/$module.php)!";
    }
    if ($result = get_string_from_file($identifier, $langfile, "\$resultstring")) {
        eval($result);
        return $resultstring;
    }

    // If it's a module, then look within the module pack itself mod/xxxx/lang/en/module.php

    if ($module != "moodle") {
        $langfile = "$modlangpath/en/$module.php";
        if (file_exists($langfile)) {
            if ($result = get_string_from_file($identifier, $langfile, "\$resultstring")) {
                eval($result);
                return $resultstring;
            }
        }
    }

    return "[[$identifier]]";  // Last resort
}


function get_string_from_file($identifier, $langfile, $destination) {
/// This function is only used from get_string().

    static $strings;    // Keep the strings cached in memory.

    if (empty($strings[$langfile])) {
        $string = array();
        include ($langfile);
        $strings[$langfile] = $string;
    } else {
        $string = &$strings[$langfile];
    }

    if (!isset ($string[$identifier])) {
        return false;
    }

    return "$destination = sprintf(\"".$string[$identifier]."\");";
}


function get_list_of_languages() {
/// Returns a list of language codes and their full names
    global $CFG;

    $languages = array();

    if (!empty($CFG->langlist)) {       // use admin's list of languages
        $langlist = explode(',', $CFG->langlist);
        foreach ($langlist as $lang) {
            if (file_exists("$CFG->dirroot/lang/$lang/moodle.php")) {
                include("$CFG->dirroot/lang/$lang/moodle.php");
                $languages[$lang] = $string["thislanguage"]." ($lang)";
                unset($string);
            }
        }
    } else {
        if (!$langdirs = get_list_of_plugins("lang")) {
            return false;
        }
        foreach ($langdirs as $lang) {
            include("$CFG->dirroot/lang/$lang/moodle.php");
            $languages[$lang] = $string["thislanguage"]." ($lang)";
            unset($string);
        }
    }

    return $languages;
}

function get_list_of_countries() {
/// Returns a list of country names in the current language
    global $CFG, $USER;

    $lang = current_language();

    if (!file_exists("$CFG->dirroot/lang/$lang/countries.php")) {
        if ($parentlang = get_string("parentlanguage")) {
            if (file_exists("$CFG->dirroot/lang/$parentlang/countries.php")) {
                $lang = $parentlang;
            } else {
                $lang = "en";  // countries.php must exist in this pack
            }
        } else {
            $lang = "en";  // countries.php must exist in this pack
        }
    }

    include("$CFG->dirroot/lang/$lang/countries.php");

    if (!empty($string)) {
        asort($string);
    }

    return $string;
}

function get_list_of_pixnames() {
/// Returns a list of picture names in the current language
    global $CFG;

    $lang = current_language();

    if (!file_exists("$CFG->dirroot/lang/$lang/pix.php")) {
        if ($parentlang = get_string("parentlanguage")) {
            if (file_exists("$CFG->dirroot/lang/$parentlang/pix.php")) {
                $lang = $parentlang;
            } else {
                $lang = "en";  // countries.php must exist in this pack
            }
        } else {
            $lang = "en";  // countries.php must exist in this pack
        }
    }

    include_once("$CFG->dirroot/lang/$lang/pix.php");

    return $string;
}

function document_file($file, $include=true) {
/// Can include a given document file (depends on second
/// parameter) or just return info about it

    global $CFG;

    $file = clean_filename($file);

    if (empty($file)) {
        return false;
    }

    $langs = array(current_language(), get_string("parentlanguage"), "en");

    foreach ($langs as $lang) {
        $info->filepath = "$CFG->dirroot/lang/$lang/docs/$file";
        $info->urlpath  = "$CFG->wwwroot/lang/$lang/docs/$file";

        if (file_exists($info->filepath)) {
            if ($include) {
                include($info->filepath);
            }
            return $info;
        }
    }

    return false;
}


/// ENCRYPTION  ////////////////////////////////////////////////

function rc4encrypt($data) {
    $password = "nfgjeingjk";
    return endecrypt($password, $data, "");
}

function rc4decrypt($data) {
    $password = "nfgjeingjk";
    return endecrypt($password, $data, "de");
}

function endecrypt ($pwd, $data, $case) {
/// Based on a class by Mukul Sabharwal [mukulsabharwal@yahoo.com]

    if ($case == 'de') {
        $data = urldecode($data);
    }

    $key[] = "";
    $box[] = "";
    $temp_swap = "";
    $pwd_length = 0;

    $pwd_length = strlen($pwd);

    for ($i = 0; $i <= 255; $i++) {
        $key[$i] = ord(substr($pwd, ($i % $pwd_length), 1));
        $box[$i] = $i;
    }

    $x = 0;

    for ($i = 0; $i <= 255; $i++) {
        $x = ($x + $box[$i] + $key[$i]) % 256;
        $temp_swap = $box[$i];
        $box[$i] = $box[$x];
        $box[$x] = $temp_swap;
    }

    $temp = "";
    $k = "";

    $cipherby = "";
    $cipher = "";

    $a = 0;
    $j = 0;

    for ($i = 0; $i < strlen($data); $i++) {
        $a = ($a + 1) % 256;
        $j = ($j + $box[$a]) % 256;
        $temp = $box[$a];
        $box[$a] = $box[$j];
        $box[$j] = $temp;
        $k = $box[(($box[$a] + $box[$j]) % 256)];
        $cipherby = ord(substr($data, $i, 1)) ^ $k;
        $cipher .= chr($cipherby);
    }

    if ($case == 'de') {
        $cipher = urldecode(urlencode($cipher));
    } else {
        $cipher = urlencode($cipher);
    }

    return $cipher;
}


/// CALENDAR MANAGEMENT  ////////////////////////////////////////////////////////////////


function add_event($event) {
/// call this function to add an event to the calendar table
///  and to call any calendar plugins
/// The function returns the id number of the resulting record
/// The object event should include the following:
///     $event->name         Name for the event
///     $event->description  Description of the event (defaults to '')
///     $event->courseid     The id of the course this event belongs to (0 = all courses)
///     $event->groupid      The id of the group this event belongs to (0 = no group)
///     $event->userid       The id of the user this event belongs to (0 = no user)
///     $event->modulename   Name of the module that creates this event
///     $event->instance     Instance of the module that owns this event
///     $event->eventtype    The type info together with the module info could
///                          be used by calendar plugins to decide how to display event
///     $event->timestart    Timestamp for start of event
///     $event->timeduration Duration (defaults to zero)

    global $CFG;

    $event->timemodified = time();

    if (!$event->id = insert_record("event", $event)) {
        return false;
    }

    if (!empty($CFG->calendar)) { // call the add_event function of the selected calendar
        if (file_exists("$CFG->dirroot/calendar/$CFG->calendar/lib.php")) {
            include_once("$CFG->dirroot/calendar/$CFG->calendar/lib.php");
            $calendar_add_event = $CFG->calendar.'_add_event';
            if (function_exists($calendar_add_event)) {
                $calendar_add_event($event);
            }
        }
    }

    return $event->id;
}


function update_event($event) {
/// call this function to update an event in the calendar table
/// the event will be identified by the id field of the $event object

    global $CFG;

    $event->timemodified = time();

    if (!empty($CFG->calendar)) { // call the update_event function of the selected calendar
        if (file_exists("$CFG->dirroot/calendar/$CFG->calendar/lib.php")) {
            include_once("$CFG->dirroot/calendar/$CFG->calendar/lib.php");
            $calendar_update_event = $CFG->calendar.'_update_event';
            if (function_exists($calendar_update_event)) {
                $calendar_update_event($event);
            }
        }
    }
    return update_record("event", $event);
}


function delete_event($id) {
/// call this function to delete the event with id $id from calendar table

    global $CFG;

    if (!empty($CFG->calendar)) { // call the delete_event function of the selected calendar
        if (file_exists("$CFG->dirroot/calendar/$CFG->calendar/lib.php")) {
            include_once("$CFG->dirroot/calendar/$CFG->calendar/lib.php");
            $calendar_delete_event = $CFG->calendar.'_delete_event';
            if (function_exists($calendar_delete_event)) {
                $calendar_delete_event($id);
            }
        }
    }
    return delete_records("event", 'id', $id);
}


function hide_event($event) {
/// call this function to hide an event in the calendar table
/// the event will be identified by the id field of the $event object

    global $CFG;

    if (!empty($CFG->calendar)) { // call the update_event function of the selected calendar
        if (file_exists("$CFG->dirroot/calendar/$CFG->calendar/lib.php")) {
            include_once("$CFG->dirroot/calendar/$CFG->calendar/lib.php");
            $calendar_hide_event = $CFG->calendar.'_hide_event';
            if (function_exists($calendar_hide_event)) {
                $calendar_hide_event($event);
            }
        }
    }
    return set_field('event', 'visible', 0, 'id', $event->id);
}


function show_event($event) {
/// call this function to unhide an event in the calendar table
/// the event will be identified by the id field of the $event object

    global $CFG;

    if (!empty($CFG->calendar)) { // call the update_event function of the selected calendar
        if (file_exists("$CFG->dirroot/calendar/$CFG->calendar/lib.php")) {
            include_once("$CFG->dirroot/calendar/$CFG->calendar/lib.php");
            $calendar_show_event = $CFG->calendar.'_show_event';
            if (function_exists($calendar_show_event)) {
                $calendar_show_event($event);
            }
        }
    }
    return set_field('event', 'visible', 1, 'id', $event->id);
}


/// ENVIRONMENT CHECKING  ////////////////////////////////////////////////////////////

function get_list_of_plugins($plugin="mod", $exclude="") {
/// Lists plugin directories within some directory

    global $CFG;

    $basedir = opendir("$CFG->dirroot/$plugin");
    while ($dir = readdir($basedir)) {
        $firstchar = substr($dir, 0, 1);
        if ($firstchar == "." or $dir == "CVS" or $dir == "_vti_cnf" or $dir == $exclude) {
            continue;
        }
        if (filetype("$CFG->dirroot/$plugin/$dir") != "dir") {
            continue;
        }
        $plugins[] = $dir;
    }
    if ($plugins) {
        asort($plugins);
    }
    return $plugins;
}

function check_php_version($version="4.1.0") {
/// Returns true is the current version of PHP is greater that the specified one
    $minversion = intval(str_replace(".", "", $version));
    $curversion = intval(str_replace(".", "", phpversion()));
    return ($curversion >= $minversion);
}

function check_browser_version($brand="MSIE", $version=5.5) {
/// Checks to see if is a browser matches the specified
/// brand and is equal or better version.

    $agent = $_SERVER["HTTP_USER_AGENT"];

    if (empty($agent)) {
        return false;
    }

    switch ($brand) {

      case "Gecko":   /// Gecko based browsers

          if (substr_count($agent, "Camino")) {     // MacOS X Camino not supported.
              return false;
          }

          // the proper string - Gecko/CCYYMMDD Vendor/Version
          if (ereg("^([a-zA-Z]+)/([0-9]+\.[0-9]+) \((.*)\) (.*)$", $agent, $match)) {
              if (ereg("^([Gecko]+)/([0-9]+)",$match[4], $reldate)) {
                  if ($reldate[2] > $version) {
                      return true;
                  }
              }
          }
          break;


      case "MSIE":   /// Internet Explorer

          if (strpos($agent, 'Opera')) {     // Reject Opera
              return false;
          }
          $string = explode(";", $agent);
          if (!isset($string[1])) {
              return false;
          }
          $string = explode(" ", trim($string[1]));
          if (!isset($string[0]) and !isset($string[1])) {
              return false;
          }
          if ($string[0] == $brand and (float)$string[1] >= $version ) {
              return true;
          }
          break;

    }

    return false;
}

function ini_get_bool($ini_get_arg) {
/// This function makes the return value of ini_get consistent if you are
/// setting server directives through the .htaccess file in apache.
/// Current behavior for value set from php.ini On = 1, Off = [blank]
/// Current behavior for value set from .htaccess On = On, Off = Off
/// Contributed by jdell@unr.edu

    $temp = ini_get($ini_get_arg);

    if ($temp == "1" or strtolower($temp) == "on") {
        return true;
    }
    return false;
}

function can_use_richtext_editor() {
/// Compatibility stub to provide backward compatibility
    return can_use_html_editor();
}

function can_use_html_editor() {
/// Is the HTML editor enabled?  This depends on site and user
/// settings, as well as the current browser being used.
/// Returns false is editor is not being used, otherwise
/// returns "MSIE" or "Gecko"

    global $USER, $CFG;

    if (!empty($USER->htmleditor) and !empty($CFG->htmleditor)) {
        if (check_browser_version("MSIE", 5.5)) {
            return "MSIE";
        } else if (check_browser_version("Gecko", 20030516)) {
            return "Gecko";
        }
    }
    return false;
}


function check_gd_version() {
/// Hack to find out the GD version by parsing phpinfo output
    $gdversion = 0;

    if (function_exists('gd_info')){
        $gd_info = gd_info();
        if (substr_count($gd_info['GD Version'], "2.")) {
            $gdversion = 2;
        } else if (substr_count($gd_info['GD Version'], "1.")) {
            $gdversion = 1;
        }

    } else {
        ob_start();
        phpinfo(8);
        $phpinfo = ob_get_contents();
        ob_end_clean();

        $phpinfo = explode("\n",$phpinfo);


        foreach ($phpinfo as $text) {
            $parts = explode('</td>',$text);
            foreach ($parts as $key => $val) {
                $parts[$key] = trim(strip_tags($val));
            }
            if ($parts[0] == "GD Version") {
                if (substr_count($parts[1], "2.0")) {
                    $parts[1] = "2.0";
                }
                $gdversion = intval($parts[1]);
            }
        }
    }

    return $gdversion;   // 1, 2 or 0
}


function moodle_needs_upgrading() {
/// Checks version numbers of Main code and all modules to see
/// if there are any mismatches ... returns true or false
    global $CFG;

    include_once("$CFG->dirroot/version.php");  # defines $version and upgrades
    if ($CFG->version) {
        if ($version > $CFG->version) {
            return true;
        }
        if ($mods = get_list_of_plugins("mod")) {
            foreach ($mods as $mod) {
                $fullmod = "$CFG->dirroot/mod/$mod";
                unset($module);
                if (!is_readable("$fullmod/version.php")) {
                    notify("Module '$mod' is not readable - check permissions");
                    continue;
                }
                include_once("$fullmod/version.php");  # defines $module with version etc
                if ($currmodule = get_record("modules", "name", $mod)) {
                    if ($module->version > $currmodule->version) {
                        return true;
                    }
                }
            }
        }
    } else {
        return true;
    }
    return false;
}


/// MISCELLANEOUS ////////////////////////////////////////////////////////////////////

function moodle_setlocale($locale='') {

    global $SESSION, $USER, $CFG;

    if ($locale) {
        $CFG->locale = $locale;
    } else if (!empty($CFG->courselang) and ($CFG->courselang != $CFG->lang) ) {
        $CFG->locale = get_string('locale');
    } else if (!empty($SESSION->lang) and ($SESSION->lang != $CFG->lang) ) {
        $CFG->locale = get_string('locale');
    } else if (!empty($USER->lang) and ($USER->lang != $CFG->lang) ) {
        $CFG->locale = get_string('locale');
    } else if (empty($CFG->locale)) {
        $CFG->locale = get_string('locale');
        set_config('locale', $CFG->locale);   // cache it to save lookups in future
    }
    setlocale (LC_TIME, $CFG->locale);
    setlocale (LC_COLLATE, $CFG->locale);

    if ($CFG->locale != 'tr_TR') {            // To workaround a well-known PHP bug with Turkish
        setlocale (LC_CTYPE, $CFG->locale);
    }
}

function moodle_strtolower ($string, $encoding='') {
/// Converts string to lowercase using most compatible  function available
    if (function_exists('mb_strtolower')) {
        if($encoding===''){
           return mb_strtolower($string);          //use multibyte support with default encoding
        } else {
           return mb_strtolower($string,$encoding); //use given encoding
        }
    } else {
        return strtolower($string);                // use common function what rely on current locale setting
    }
}

function count_words($string) {
/// Words are defined as things between whitespace
    $string = strip_tags($string);
    return count(preg_split("/\w\b/", $string)) - 1;
}

function random_string ($length=15) {
    $pool  = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $pool .= "abcdefghijklmnopqrstuvwxyz";
    $pool .= "0123456789";
    $poollen = strlen($pool);
    mt_srand ((double) microtime() * 1000000);
    $string = "";
    for ($i = 0; $i < $length; $i++) {
        $string .= substr($pool, (mt_rand()%($poollen)), 1);
    }
    return $string;
}


function getweek ($startdate, $thedate) {
/// Given dates in seconds, how many weeks is the date from startdate
/// The first week is 1, the second 2 etc ...

    if ($thedate < $startdate) {   // error
        return 0;
    }

    return floor(($thedate - $startdate) / 604800.0) + 1;
}

function generate_password($maxlen=10) {
/// returns a randomly generated password of length $maxlen.  inspired by
/// http://www.phpbuilder.com/columns/jesus19990502.php3

    global $CFG;

    $fillers = "1234567890!$-+";
    $wordlist = file($CFG->wordlist);

    srand((double) microtime() * 1000000);
    $word1 = trim($wordlist[rand(0, count($wordlist) - 1)]);
    $word2 = trim($wordlist[rand(0, count($wordlist) - 1)]);
    $filler1 = $fillers[rand(0, strlen($fillers) - 1)];

    return substr($word1 . $filler1 . $word2, 0, $maxlen);
}

function format_float($num, $places=1) {
/// Given a float, prints it nicely
    return sprintf("%.$places"."f", $num);
}

function swapshuffle($array) {
/// Given a simple array, this shuffles it up just like shuffle()
/// Unlike PHP's shuffle() ihis function works on any machine.

    srand ((double) microtime() * 10000000);
    $last = count($array) - 1;
    for ($i=0;$i<=$last;$i++) {
        $from = rand(0,$last);
        $curr = $array[$i];
        $array[$i] = $array[$from];
        $array[$from] = $curr;
    }
    return $array;
}

function swapshuffle_assoc($array) {
/// Like swapshuffle, but works on associative arrays

    $newkeys = swapshuffle(array_keys($array));
    foreach ($newkeys as $newkey) {
        $newarray[$newkey] = $array[$newkey];
    }
    return $newarray;
}

function draw_rand_array($array, $draws) {
/// Given an arbitrary array, and a number of draws,
/// this function returns an array with that amount
/// of items.  The indexes are retained.

    srand ((double) microtime() * 10000000);

    $return = array();

    $last = count($array);

    if ($draws > $last) {
        $draws = $last;
    }

    while ($draws > 0) {
        $last--;

        $keys = array_keys($array);
        $rand = rand(0, $last);

        $return[$keys[$rand]] = $array[$keys[$rand]];
        unset($array[$keys[$rand]]);

        $draws--;
    }

    return $return;
}

function microtime_diff($a, $b) {
    list($a_dec, $a_sec) = explode(" ", $a);
    list($b_dec, $b_sec) = explode(" ", $b);
    return $b_sec - $a_sec + $b_dec - $a_dec;
}

function make_menu_from_list($list, $separator=",") {
/// Given a list (eg a,b,c,d,e) this function returns
/// an array of 1->a, 2->b, 3->c etc

    $array = array_reverse(explode($separator, $list), true);
    foreach ($array as $key => $item) {
        $outarray[$key+1] = trim($item);
    }
    return $outarray;
}

function make_grades_menu($gradingtype) {
/// Creates an array that represents all the current grades that
/// can be chosen using the given grading type.  Negative numbers
/// are scales, zero is no grade, and positive numbers are maximum
/// grades.

    $grades = array();
    if ($gradingtype < 0) {
        if ($scale = get_record("scale", "id", - $gradingtype)) {
            return make_menu_from_list($scale->scale);
        }
    } else if ($gradingtype > 0) {
        for ($i=$gradingtype; $i>=0; $i--) {
            $grades[$i] = "$i / $gradingtype";
        }
        return $grades;
    }
    return $grades;
}

function course_scale_used($courseid,$scaleid) {
////This function returns the nummber of activities
////using scaleid in a courseid

    global $CFG;
    
    $return = 0;

    if (!empty($scaleid)) {
        if ($cms = get_course_mods($courseid)) {
            foreach ($cms as $cm) {
                //Check cm->name/lib.php exists
                if (file_exists($CFG->dirroot.'/mod/'.$cm->modname.'/lib.php')) {
                    include_once($CFG->dirroot.'/mod/'.$cm->modname.'/lib.php');
                    $function_name = $cm->modname.'_scale_used';
                    if (function_exists($function_name)) {
                        if ($function_name($cm->instance,$scaleid)) {
                            $return++;
                        }
                    }
                }
            }
        }
    }
    return $return;
}

function site_scale_used($scaleid) {
////This function returns the nummber of activities 
////using scaleid in the entire site

    global $CFG;

    $return = 0;

    if (!empty($scaleid)) {
        if ($courses = get_courses()) {
            foreach ($courses as $course) {
                $return += course_scale_used($course->id,$scaleid);
            }
        }
    }
    return $return;
}

function make_unique_id_code($extra="") {

    $hostname = "unknownhost";
    if (!empty($_SERVER["HTTP_HOST"])) {
        $hostname = $_SERVER["HTTP_HOST"];
    } else if (!empty($_ENV["HTTP_HOST"])) {
        $hostname = $_ENV["HTTP_HOST"];
    } else if (!empty($_SERVER["SERVER_NAME"])) {
        $hostname = $_SERVER["SERVER_NAME"];
    } else if (!empty($_ENV["SERVER_NAME"])) {
        $hostname = $_ENV["SERVER_NAME"];
    }

    $date = gmdate("ymdHis");

    $random =  random_string(6);

    if ($extra) {
        return "$hostname+$date+$random+$extra";
    } else {
        return "$hostname+$date+$random";
    }
}


// vim:autoindent:expandtab:shiftwidth=4:tabstop=4:tw=140:
?>
