<?php

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

/**
 * moodlelib.php - Moodle main library
 *
 * Main library file of miscellaneous general-purpose Moodle functions.
 * Other main libraries:
 *  - weblib.php      - functions that produce web output
 *  - datalib.php     - functions that access the database
 * @author Martin Dougiamas
 * @version $Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package moodlecore
 */
/// CONSTANTS /////////////////////////////////////////////////////////////

/**
 * No groups used?
 */
define('NOGROUPS', 0);

/**
 * Groups used?
 */
define('SEPARATEGROUPS', 1);

/**
 * Groups visible?
 */
define('VISIBLEGROUPS', 2);


/// PARAMETER HANDLING ////////////////////////////////////////////////////

/**
 * Ensure that a variable is set or display error
 *
 * If $var is undefined display an error message using the {@link error()} function.
 *
 * @param mixed $var the variable which may not be set
 */
function require_variable($var) {
/// Variable must be present
    if (! isset($var)) {
        error('A required parameter was missing');
    }
}


/**
 * Ensure that a variable is set
 *
 * If $var is undefined set it (by reference), otherwise return $var.
 * This function is very similar to {@link nvl()}
 *
 * @param mixed $var the variable which may be unset
 * @param mixed $default the value to return if $var is unset
 */
function optional_variable(&$var, $default=0) {
/// Variable may be present, if not then set a default
    if (! isset($var)) {
        $var = $default;
    }
}

/**
 * Set a key in global configuration
 *
 * Set a key/value pair in both this session's {@link $CFG} global variable
 * and in the 'config' database table for future sessions.
 *
 * @param string $name the key to set
 * @param string $value the value to set
 * @uses $CFG
 * @return bool
 */
function set_config($name, $value) {
/// No need for get_config because they are usually always available in $CFG

    global $CFG;


    $CFG->$name = $value;  // So it's defined for this invocation at least

    if (get_field('config', 'name', 'name', $name)) {
        return set_field('config', 'value', $value, 'name', $name);
    } else {
        $config->name = $name;
        $config->value = $value;
        return insert_record('config', $config);
    }
}

/**
 * Refresh current $USER session global variable with all their current preferences.
 * @uses $USER
 */
function reload_user_preferences() {

    global $USER;

    unset($USER->preference);

    if ($preferences = get_records('user_preferences', 'userid', $USER->id)) {
        foreach ($preferences as $preference) {
            $USER->preference[$preference->name] = $preference->value;
        }
    } else {
            //return empty preference array to hold new values
            $USER->preference = array();
    }
}

/**
 * Sets a preference for the current user
 * Optionally, can set a preference for a different user object
 * @uses $USER
 * @todo Add a better description and include usage examples.
 * @param string $name The key to set as preference for the specified user
 * @param string $value The value to set forthe $name key in the specified user's record
 * @param int $userid A moodle user ID
 * @todo Add inline links to $USER and user functions in above line.
 * @return boolean
 */
function set_user_preference($name, $value, $userid=NULL) {

    global $USER;

    if (empty($userid)){ 
        $userid = $USER->id;
    }

    if (empty($name)) {
        return false;
    }

    if ($preference = get_record('user_preferences', 'userid', $userid, 'name', $name)) {
        if (set_field('user_preferences', 'value', $value, 'id', $preference->id)) {
            $user->preference[$name] = $value;
            return true;
        } else {
            return false;
        }

    } else {
        $preference->userid = $userid;
        $preference->name   = $name;
        $preference->value  = (string)$value;
        if (insert_record('user_preferences', $preference)) {
            $user->preference[$name] = $value;
            return true;
        } else {
            return false;
        }
    }
}

/**
 * Unsets a preference completely by deleting it from the database
 * Optionally, can set a preference for a different user id
 * @uses $USER
 * @param string  $name The key to unset as preference for the specified user
 * @param int $userid A moodle user ID
 * @return boolean
 */
function unset_user_preference($name, $userid=NULL) {

    global $USER;

    if (empty($userid)){ 
        $userid = $USER->id;
    }

    return delete_records('user_preferences', 'userid', $userid, 'name', $name);
}


/**
 * Sets a whole array of preferences for the current user
 * @param array $prefarray An array of key/value pairs to be set
 * @param int $userid A moodle user ID
 * @return boolean
 */
function set_user_preferences($prefarray, $userid=NULL) {

    global $USER;

    if (!is_array($prefarray) or empty($prefarray)) {
        return false;
    }

    if (empty($userid)){ 
        $userid = $USER->id;
    }

    $return = true;
    foreach ($prefarray as $name => $value) {
        // The order is important; if the test for return is done first,
        // then if one function call fails all the remaining ones will
        // be "optimized away"
        $return = set_user_preference($name, $value, $userid) and $return;
    }
    return $return;
}

/**
 * If no arguments are supplied this function will return
 * all of the current user preferences as an array.  
 * If a name is specified then this function
 * attempts to return that particular preference value.  If
 * none is found, then the optional value $default is returned,
 * otherwise NULL.
 * @param string $name Name of the key to use in finding a preference value
 * @param string $default Value to be returned if the $name key is not set in the user preferences
 * @param int $userid A moodle user ID
 * @uses $USER
 * @return string
 */
function get_user_preferences($name=NULL, $default=NULL, $userid=NULL) {

    global $USER;

    if (empty($userid)) {   // assume current user
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

    } else {
        $preference = get_records_menu('user_preferences', 'userid', $userid, 'name', 'name,value');

        if (empty($name)) {
            return $preference;
        }
        if (!isset($preference[$name])) {
            return $default;              // Default value (or NULL)
        }
        return $preference[$name];        // The single value
    }
}


/// FUNCTIONS FOR HANDLING TIME ////////////////////////////////////////////

/**
 * Given date parts in user time produce a GMT timestamp.
 *
 * @param int $year The year part to create timestamp of.
 * @param int $month The month part to create timestamp of.
 * @param int $day The day part to create timestamp of.
 * @param int $hour The hour part to create timestamp of.
 * @param int $minute The minute part to create timestamp of.
 * @param int $second The second part to create timestamp of.
 * @param int $timezone ?
 * @return ?
 * @todo Finish documenting this function
 */
function make_timestamp($year, $month=1, $day=1, $hour=0, $minute=0, $second=0, $timezone=99) {

    $timezone = get_user_timezone($timezone);

    if (abs($timezone) > 13) {
        return mktime((int)$hour,(int)$minute,(int)$second,(int)$month,(int)$day,(int)$year);
    } else {
        $time = gmmktime((int)$hour,(int)$minute,(int)$second,(int)$month,(int)$day,(int)$year);
        return usertime($time, $timezone);  // This is GMT
    }
}

/**
 * Given an amount of time in seconds, returns string
 * formatted nicely as months, days, hours etc as needed
 *
 * @param int $totalsecs ?
 * @param array $str ?
 * @return string
 * @todo Finish documenting this function
 */
 function format_time($totalsecs, $str=NULL) {

    $totalsecs = abs($totalsecs);

    if (!$str) {  // Create the str structure the slow way
        $str->day   = get_string('day');
        $str->days  = get_string('days');
        $str->hour  = get_string('hour');
        $str->hours = get_string('hours');
        $str->min   = get_string('min');
        $str->mins  = get_string('mins');
        $str->sec   = get_string('sec');
        $str->secs  = get_string('secs');
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

    $odays = '';
    $ohours = '';
    $omins = '';
    $osecs = '';

    if ($days)  $odays  = $days .' '. $sd;
    if ($hours) $ohours = $hours .' '. $sh;
    if ($mins)  $omins  = $mins .' '. $sm;
    if ($secs)  $osecs  = $secs .' '. $ss;

    if ($days)  return $odays .' '. $ohours;
    if ($hours) return $ohours .' '. $omins;
    if ($mins)  return $omins .' '. $osecs;
    if ($secs)  return $osecs;
    return get_string('now');
}

/**
 * Returns a formatted string that represents a date in user time
 * <b>WARNING: note that the format is for strftime(), not date().</b>
 * Because of a bug in most Windows time libraries, we can't use
 * the nicer %e, so we have to use %d which has leading zeroes.
 * A lot of the fuss in the function is just getting rid of these leading
 * zeroes as efficiently as possible.
 * 
 * If parameter fixday = true (default), then take off leading
 * zero from %d, else mantain it.
 *
 * @param  int $date ?
 * @param string $format ?
 * @param int $timezone ?
 * @param boolean $fixday If true (default) then the leading
 * zero from %d is removed. If false then the leading zero is mantained.
 * @return string
 * @todo Finish documenting this function
 */
function userdate($date, $format='', $timezone=99, $fixday = true) {

    if ($format == '') {
        $format = get_string('strftimedaydatetime');
    }

    $formatnoday = str_replace('%d', 'DD', $format);
    if ($fixday) {
        $fixday = ($formatnoday != $format);
    }

    $timezone = get_user_timezone($timezone);

    if (abs($timezone) > 13) {
        if ($fixday) {
            $datestring = strftime($formatnoday, $date);
            $daystring  = str_replace(' 0', '', strftime(" %d", $date));
            $datestring = str_replace('DD', $daystring, $datestring);
        } else {
            $datestring = strftime($format, $date);
        }
    } else {
        $date = $date + (int)($timezone * 3600);
        if ($fixday) {
            $datestring = gmstrftime($formatnoday, $date);
            $daystring  = str_replace(' 0', '', gmstrftime(" %d", $date));
            $datestring = str_replace('DD', $daystring, $datestring);
        } else {
            $datestring = gmstrftime($format, $date);
        }
    }

    return $datestring;
}

/**
 * Given a $date timestamp in GMT (seconds since epoch), 
 * returns an array that represents the date in user time
 *
 * @param  int $date Timestamp in GMT
 * @param int $timezone ?
 * @return array An array that represents the date in user time
 * @todo Finish documenting this function
 */
function usergetdate($date, $timezone=99) {

    $timezone = get_user_timezone($timezone);

    if (abs($timezone) > 13) {
        return getdate($date);
    }
    //There is no gmgetdate so I have to fake it...
    $date = $date + (int)($timezone * 3600);
    $getdate['seconds'] = gmstrftime("%S", $date);
    $getdate['minutes'] = gmstrftime("%M", $date);
    $getdate['hours']   = gmstrftime("%H", $date);
    $getdate['mday']    = gmstrftime("%d", $date);
    $getdate['wday']    = gmstrftime("%u", $date);
    $getdate['mon']     = gmstrftime("%m", $date);
    $getdate['year']    = gmstrftime("%Y", $date);
    $getdate['yday']    = gmstrftime("%j", $date);
    $getdate['weekday'] = gmstrftime("%A", $date);
    $getdate['month']   = gmstrftime("%B", $date);
    return $getdate;
}

/**
 * Given a GMT timestamp (seconds since epoch), offsets it by
 * the timezone.  eg 3pm in India is 3pm GMT - 7 * 3600 seconds
 *
 * @param  int $date Timestamp in GMT
 * @param int $timezone ?
 * @return int
 * @todo Finish documenting this function
 */
function usertime($date, $timezone=99) {

    $timezone = get_user_timezone($timezone);
    if (abs($timezone) > 13) {
        return $date;
    }
    return $date - (int)($timezone * 3600);
}

/**
 * Given a time, return the GMT timestamp of the most recent midnight
 * for the current user.
 *
 * @param  int $date Timestamp in GMT
 * @param int $timezone ?
 * @return ?
 * @todo Finish documenting this function. Is timezone an int or float?
 */
function usergetmidnight($date, $timezone=99) {

    $timezone = get_user_timezone($timezone);
    $userdate = usergetdate($date, $timezone);

    if (abs($timezone) > 13) {
        return mktime(0, 0, 0, $userdate['mon'], $userdate['mday'], $userdate['year']);
    }

    $timemidnight = gmmktime (0, 0, 0, $userdate['mon'], $userdate['mday'], $userdate['year']);
    return usertime($timemidnight, $timezone); // Time of midnight of this user's day, in GMT

}

/**
 * Returns a string that prints the user's timezone
 *
 * @param float $timezone The user's timezone
 * @return string
 * @todo is $timezone an int or a float?
 */
function usertimezone($timezone=99) {

    $timezone = get_user_timezone($timezone);

    if (abs($timezone) > 13) {
        return 'server time';
    }
    if (abs($timezone) < 0.5) {
        return 'GMT';
    }
    if ($timezone > 0) {
        return 'GMT+'. $timezone;
    } else {
        return 'GMT'. $timezone;
    }
}

/**
 * Returns a float which represents the user's timezone difference from GMT in hours
 * Checks various settings and picks the most dominant of those which have a value
 *
 * @uses $CFG
 * @uses $USER
 * @param int $tz The user's timezone
 * @return int
 * @todo is $tz an int or a float?
 */
function get_user_timezone($tz = 99) {

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

/**
 * This function checks that the current user is logged in, and optionally
 * whether they are "logged in" or allowed to be in a particular course.
 * If not, then it redirects them to the site login or course enrolment.
 * $autologinguest determines whether visitors should automatically be
 * logged in as guests provide {@link $CFG}->autologinguests is set to 1
 *
 * @uses $CFG
 * @uses $SESSION
 * @uses $USER
 * @uses $FULLME
 * @uses SITEID
 * @uses $MoodleSession
 * @param int $courseid The course in question
 * @param boolean $autologinguest ?
 * @todo Finish documenting this function
 */
function require_login($courseid=0, $autologinguest=true) {

    global $CFG, $SESSION, $USER, $FULLME, $MoodleSession;

    // First check that the user is logged in to the site.
    if (! (isset($USER->loggedin) and $USER->confirmed and ($USER->site == $CFG->wwwroot)) ) { // They're not
        $SESSION->wantsurl = $FULLME;
        if (!empty($_SERVER['HTTP_REFERER'])) {
            $SESSION->fromurl  = $_SERVER['HTTP_REFERER'];
        }
        $USER = NULL;
        if ($autologinguest and $CFG->autologinguests and $courseid and get_field('course','guest','id',$courseid)) {
            $loginguest = '?loginguest=true';
        } else {
            $loginguest = '';
        }
        if (empty($CFG->loginhttps)) {
            redirect($CFG->wwwroot .'/login/index.php'. $loginguest);
        } else {
            $wwwroot = str_replace('http','https', $CFG->wwwroot);
            redirect($wwwroot .'/login/index.php'. $loginguest);
        }
        die;
    }

    // check whether the user should be changing password
    reload_user_preferences();
    if (!empty($USER->preference['auth_forcepasswordchange'])){
        if (is_internal_auth() || $CFG->{'auth_'.$USER->auth.'_stdchangepassword'}){
            redirect($CFG->wwwroot .'/login/change_password.php');
        } elseif($CFG->changepassword) {
            redirect($CFG->changepassword);
        } else {
            error('You cannot proceed without changing your password. 
                   However there is no available page for changing it.
                   Please contact your Moodle Administrator.');
        }
    }

    // Check that the user account is properly set up
    if (user_not_fully_set_up($USER)) {
        redirect($CFG->wwwroot .'/user/edit.php?id='. $USER->id .'&amp;course='. SITEID);
        die;
    }

    // Next, check if the user can be in a particular course
    if ($courseid) {
        if ($courseid == SITEID) {   
            return;   // Anyone can be in the site course
        }
        if (!empty($USER->student[$courseid]) or !empty($USER->teacher[$courseid]) or !empty($USER->admin)) {
            if (isset($USER->realuser)) {   // Make sure the REAL person can also access this course
                if (!isteacher($courseid, $USER->realuser)) {
                    print_header();
                    notice(get_string('studentnotallowed', '', fullname($USER, true)), $CFG->wwwroot .'/');
                }
            }
            return;   // user is a member of this course.
        }
        if (! $course = get_record('course', 'id', $courseid)) {
            error('That course doesn\'t exist');
        }
        if (!$course->visible) {
            print_header();
            notice(get_string('studentnotallowed', '', fullname($USER, true)), $CFG->wwwroot .'/');
        }
        if ($USER->username == 'guest') {
            switch ($course->guest) {
                case 0: // Guests not allowed
                    print_header();
                    notice(get_string('guestsnotallowed', '', $course->fullname));
                    break;
                case 1: // Guests allowed
                    return;
                case 2: // Guests allowed with key (drop through)
                    break;
            }
        }

        // Currently not enrolled in the course, so see if they want to enrol
        $SESSION->wantsurl = $FULLME;
        redirect($CFG->wwwroot .'/course/enrol.php?id='. $courseid);
        die;
    }
}

/**
 * This is a weaker version of {@link require_login()} which only requires login
 * when called from within a course rather than the site page, unless
 * the forcelogin option is turned on.
 *
 * @uses $CFG
 * @param int $courseid The course in question
 * @param boolean $autologinguest ?
 * @todo Finish documenting this function
 */
function require_course_login($course, $autologinguest=true) {
    global $CFG;
    if ($CFG->forcelogin) {
      require_login();
    }
    if ($course->category) {
      require_login($course->id, $autologinguest);
    }
}

/**
 * Modify the user table by setting the currently logged in user's
 * last login to now.
 *
 * @uses $USER
 * @return boolean
 */
function update_user_login_times() {
    global $USER;

    $USER->lastlogin = $user->lastlogin = $USER->currentlogin;
    $USER->currentlogin = $user->lastaccess = $user->currentlogin = time();

    $user->id = $USER->id;

    return update_record('user', $user);
}

/**
 * Determines if a user has completed setting up their account.
 *
 * @param user $user A {@link $USER} object to test for the existance of a valid name and email
 * @return boolean
 */
function user_not_fully_set_up($user) {
    return ($user->username != 'guest' and (empty($user->firstname) or empty($user->lastname) or empty($user->email)));
}

/**
 * Keeps track of login attempts
 *
 * @uses $SESSION
 */
function update_login_count() {

    global $SESSION;

    $max_logins = 10;

    if (empty($SESSION->logincount)) {
        $SESSION->logincount = 1;
    } else {
        $SESSION->logincount++;
    }

    if ($SESSION->logincount > $max_logins) {
        unset($SESSION->wantsurl);
        error(get_string('errortoomanylogins'));
    }
}

/**
 * Resets login attempts
 *
 * @uses $SESSION
 */
function reset_login_count() {
    global $SESSION;

    $SESSION->logincount = 0;
}

/**
 * check_for_restricted_user
 *
 * @uses $CFG
 * @uses $USER
 * @param string $username ?
 * @param string $redirect ?
 * @todo Finish documenting this function
 */
function check_for_restricted_user($username=NULL, $redirect='') {
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
            error(get_string('restricteduser', 'error', fullname($USER)), $redirect);
        }
    }
}

/**
 * Determines if a user an admin
 *
 * @uses $USER
 * @param int $userid The id of the user as is found in the 'user' table
 * @staticvar array $admin ?
 * @staticvar array $nonadmins ?
 * @return boolean
 * @todo Complete documentation for this function
 */
function isadmin($userid=0) {
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
    } else if (record_exists('user_admins', 'userid', $userid)){
        $admins[] = $userid;
        return true;
    } else {
        $nonadmins[] = $userid;
        return false;
    }
}

/**
 * Determines if a user is a teacher or an admin
 *
  * @uses $USER
 * @param int $courseid The id of the course that is being viewed, if any
 * @param int $userid The id of the user that is being tested against. Set this to 0 if you would just like to test against the currently logged in user.
 * @param boolean $includeadmin If true this function will return true when it encounters an admin user.
 * @return boolean
 * @todo Finish documenting this function
 */
function isteacher($courseid=0, $userid=0, $includeadmin=true) {
    global $USER;

    if ($includeadmin and isadmin($userid)) {  // admins can do anything the teacher can
        return true;
    }

    if (!$userid) {
        if ($courseid) {
            return !empty($USER->teacher[$courseid]);
        }
        if (!isset($USER->id)) {
            return false;
        }
        $userid = $USER->id;
    }

    if (!$courseid) {
        return record_exists('user_teachers', 'userid', $userid);
    }

    return record_exists('user_teachers', 'userid', $userid, 'course', $courseid);
}

/**
 * Determines if a user is allowed to edit a given course
 *
 * @uses $USER
 * @param int $courseid The id of the course that is being edited
 * @param int $userid The id of the user that is being tested against. Set this to 0 if you would just like to test against the currently logged in user.
 * @return boolean
 */
function isteacheredit($courseid, $userid=0) {
    global $USER;

    if (isadmin($userid)) {  // admins can do anything
        return true;
    }

    if (!$userid) {
        return !empty($USER->teacheredit[$courseid]);
    }

    return get_field('user_teachers', 'editall', 'userid', $userid, 'course', $courseid);
}

/**
 * Determines if a user can create new courses
 *
 * @uses $USER
 * @param int $userid The user being tested. You can set this to 0 or leave it blank to test the currently logged in user. 
 * @return boolean
 */
function iscreator ($userid=0) {
    global $USER;
    if (empty($USER->id)) {
        return false;
    }
    if (isadmin($userid)) {  // admins can do anything
        return true;
    }
    if (empty($userid)) {
        return record_exists('user_coursecreators', 'userid', $USER->id);
    }

    return record_exists('user_coursecreators', 'userid', $userid);
}

/**
 * Determines if a user is a student in the specified course
 * 
 * If the course id specifies the site then the function determines
 * if the user is a confirmed and valid user of this site.
 *
 * @uses $USER
 * @uses $CFG
 * @uses SITEID
 * @param int $courseid The id of the course being tested
 * @param int $userid The user being tested. You can set this to 0 or leave it blank to test the currently logged in user. 
 * @return boolean
 */
function isstudent($courseid, $userid=0) {
    global $USER, $CFG;

    if (empty($USER->id) and !$userid) {
        return false;
    }

    if ($courseid == SITEID) {
        if (!$userid) {
            $userid = $USER->id;
        }
        if (isguest($userid)) {
            return false;
        }
        // a site teacher can never be a site student
        if (isteacher($courseid, $userid)) {
            return false;
        }
        if ($CFG->allusersaresitestudents) {
            return record_exists('user', 'id', $userid);
        } else {
            return (record_exists('user_students', 'userid', $userid)
                     or record_exists('user_teachers', 'userid', $userid));
        }
    }

    if (!$userid) {
        return !empty($USER->student[$courseid]);
    }

  //  $timenow = time();   // todo:  add time check below

    return record_exists('user_students', 'userid', $userid, 'course', $courseid);
}

/**
 * Determines if the specified user is logged in as guest.
 *
 * @uses $USER
 * @param int $userid The user being tested. You can set this to 0 or leave it blank to test the currently logged in user. 
 * @return boolean
 */
function isguest($userid=0) {
    global $USER;

    if (!$userid) {
        if (empty($USER->username)) {
            return false;
        }
        return ($USER->username == 'guest');
    }

    return record_exists('user', 'id', $userid, 'username', 'guest');
}

/**
 * Determines if the currently logged in user is in editing mode
 *
 * @uses $USER
 * @param int $courseid The id of the course being tested
 * @param user $user A {@link $USER} object. If null then the currently logged in user is used.
 * @return boolean
 */
function isediting($courseid, $user=NULL) {
    global $USER;
    if (!$user){
        $user = $USER;
    }
    if (empty($user->editing)) {
        return false;
    }
    return ($user->editing and isteacher($courseid, $user->id));
}

/**
 * Determines if the logged in user is currently moving an activity
 *
 * @uses $USER
 * @param int $courseid The id of the course being tested
 * @return boolean
 */
function ismoving($courseid) {
    global $USER;

    if (!empty($USER->activitycopy)) {
        return ($USER->activitycopycourse == $courseid);
    }
    return false;
}

/**
 * Given an object containing firstname and lastname
 * values, this function returns a string with the
 * full name of the person.
 * The result may depend on system settings
 * or language.  'override' will force both names
 * to be used even if system settings specify one. 
 * @uses $CFG
 * @uses $SESSION
 * @param    type description
 * @todo Finish documenting this function
 */
function fullname($user, $override=false) {

    global $CFG, $SESSION;

    if (!isset($user->firstname) and !isset($user->lastname)) {
        return '';
    }

    if (!empty($SESSION->fullnamedisplay)) {
        $CFG->fullnamedisplay = $SESSION->fullnamedisplay;
    }

    if ($CFG->fullnamedisplay == 'firstname lastname') {
        return $user->firstname .' '. $user->lastname;

    } else if ($CFG->fullnamedisplay == 'lastname firstname') {
        return $user->lastname .' '. $user->firstname;

    } else if ($CFG->fullnamedisplay == 'firstname') {
        if ($override) {
            return get_string('fullnamedisplay', '', $user);
        } else {
            return $user->firstname;
        }
    }

    return get_string('fullnamedisplay', '', $user);
}

/**
 * Sets a moodle cookie with an encrypted string
 *
 * @uses $CFG
 * @param string $thing The string to encrypt and place in a cookie
 */
function set_moodle_cookie($thing) {
    global $CFG;

    $cookiename = 'MOODLEID_'.$CFG->sessioncookie;

    $days = 60;
    $seconds = 60*60*24*$days;

    setCookie($cookiename, '', time() - 3600, '/');
    setCookie($cookiename, rc4encrypt($thing), time()+$seconds, '/');
}

/**
 * Gets a moodle cookie with an encrypted string
 *
 * @uses $CFG
 * @return string
 */
function get_moodle_cookie() {
    global $CFG;

    $cookiename = 'MOODLEID_'.$CFG->sessioncookie;

    if (empty($_COOKIE[$cookiename])) {
        return '';
    } else {
        return rc4decrypt($_COOKIE[$cookiename]);
    }
}

/**
 * Returns true if an internal authentication method is being used.
 * if method not specified then, global default is assumed
 *
 * @uses $CFG
 * @param string $auth Form of authentication required
 * @return boolean
 * @todo Outline auth types and provide code example
 */
function is_internal_auth($auth='') {
/// Returns true if an internal authentication method is being used.
/// If auth not specified then global default is assumed

    global $CFG;

    if (empty($auth)) {
        $auth = $CFG->auth;
    }

    return ($auth == "email" || $auth == "none" || $auth == "manual");
}

/**
 * Returns an array of user fields
 *
 * @uses $CFG
 * @uses $db
 * @return array User field/column names
 * @todo Finish documenting this function
 */
function get_user_fieldnames() {

    global $CFG, $db;

    $fieldarray = $db->MetaColumnNames($CFG->prefix.'user');
    unset($fieldarray['ID']);

    return $fieldarray;
}

/**
 * Creates a bare-bones user record
 *
 * @uses $CFG
 * @uses $REMOTE_ADDR
 * @param string $username New user's username to add to record
 * @param string $password New user's password to add to record
 * @param string $auth Form of authentication required
 * @return user A {@link $USER} object
 * @todo Outline auth types and provide code example
 */
function create_user_record($username, $password, $auth='') {
    global $REMOTE_ADDR, $CFG;

    //just in case check text case
    $username = trim(moodle_strtolower($username));

    if (function_exists('auth_get_userinfo')) {
        if ($newinfo = auth_get_userinfo($username)) {
            foreach ($newinfo as $key => $value){
                $newuser->$key = addslashes(stripslashes($value)); // Just in case
            }
        }
    }

    if (!empty($newuser->email)) {
        if (email_is_not_allowed($newuser->email)) {
            unset($newuser->email);
        }
    }

    $newuser->auth = (empty($auth)) ? $CFG->auth : $auth;
    $newuser->username = $username;
    $newuser->password = md5($password);
    $newuser->lang = $CFG->lang;
    $newuser->confirmed = 1;
    $newuser->lastIP = getremoteaddr();
    $newuser->timemodified = time();

    if (insert_record('user', $newuser)) {
         $user = get_user_info_from_db('username', $newuser->username);
         if($CFG->{'auth_'.$newuser->auth.'_forcechangepassword'}){
             set_user_preference('auth_forcepasswordchange', 1, $user);
         }
         return $user;
    }
    return false;
}

/**
 * Will update a local user record from an external source
 *
 * @uses $CFG
 * @param string $username New user's username to add to record
 * @return user A {@link $USER} object
 */
function update_user_record($username) {
    global $CFG;

    if (function_exists('auth_get_userinfo')) {
        $username = trim(moodle_strtolower($username)); /// just in case check text case

        if ($newinfo = auth_get_userinfo($username)) {
            foreach ($newinfo as $key => $value){
                if (!empty($CFG->{'auth_user_' . $key. '_updatelocal'})) {
                    $value = addslashes(stripslashes($value));   // Just in case
                    set_field('user', $key, $value, 'username', $username);
                }
            }
        }
    }
    return get_user_info_from_db('username', $username);
}

/**
 * Retrieve the guest user object
 *
 * @uses $CFG
 * @return user A {@link $USER} object
 */
function guest_user() {
    global $CFG;

    if ($newuser = get_record('user', 'username', 'guest')) {
        $newuser->loggedin = true;
        $newuser->confirmed = 1;
        $newuser->site = $CFG->wwwroot;
        $newuser->lang = $CFG->lang;
    }

    return $newuser;
}

/**
 * Given a username and password, this function looks them
 * up using the currently selected authentication mechanism,
 * and if the authentication is successful, it returns a
 * valid $user object from the 'user' table.
 * 
 * Uses auth_ functions from the currently active auth module
 *
 * @uses $CFG
 * @param string $username  User's username 
 * @param string $password  User's password 
 * @return user|flase A {@link $USER} object or false if error
 */
function authenticate_user_login($username, $password) {

    global $CFG;

    $md5password = md5($password);

    // First try to find the user in the database

    $user = get_user_info_from_db('username', $username);

    // Sort out the authentication method we are using.

    if (empty($CFG->auth)) {
        $CFG->auth = 'manual';     // Default authentication module
    }

    if (empty($user->auth)) {      // For some reason it isn't set yet
        if (isadmin($user->id) or isguest($user->id)) {
            $auth = 'manual';    // Always assume these guys are internal
        } else {
            $auth = $CFG->auth;  // Normal users default to site method
        }
        // update user record from external DB
        if ($user->auth != 'manual' && $user->auth != 'email') {
            $user = update_user_record($username);
        }
    } else {
        $auth = $user->auth;
    }

    if (detect_munged_arguments($auth, 0)) {   // For safety on the next require
        return false;
    }

    if (!file_exists($CFG->dirroot .'/auth/'. $auth .'/lib.php')) {
        $auth = 'manual';    // Can't find auth module, default to internal
    }

    require_once($CFG->dirroot .'/auth/'. $auth .'/lib.php');

    if (auth_user_login($username, $password)) {  // Successful authentication
        if ($user) {                              // User already exists in database
            if (empty($user->auth)) {             // For some reason auth isn't set yet
                set_field('user', 'auth', $auth, 'username', $username);
            }
            if ($md5password <> $user->password) {   // Update local copy of password for reference
                set_field('user', 'password', $md5password, 'username', $username);
            }
            // update user record from external DB
            if ($user->auth != 'manual' && $user->auth != 'email'){
                $user = update_user_record($username);
            }
        } else {
            $user = create_user_record($username, $password, $auth);
        }

        if (function_exists('auth_iscreator')) {    // Check if the user is a creator
            if (auth_iscreator($username)) {
                if (! record_exists('user_coursecreators', 'userid', $user->id)) {
                    $cdata->userid = $user->id;
                    if (! insert_record('user_coursecreators', $cdata)) {
                        error('Cannot add user to course creators.');
                    }
                }
            } else {
                if ( record_exists('user_coursecreators', 'userid', $user->id)) {
                    if (! delete_records('user_coursecreators', 'userid', $user->id)) {
                        error('Cannot remove user from course creators.');
                    }
                }
            }
        }
        return $user;

    } else {
        add_to_log(0, 'login', 'error', $_SERVER['HTTP_REFERER'], $username);
        $date = date('Y-m-d H:i:s');
        error_log($date ."\tfailed login\t". getremoteaddr() ."\t". $_SERVER['HTTP_USER_AGENT'] ."\t". $username);
        return false;
    }
}

/**
 * Enrols (or re-enrols) a student in a given course
 *
 * @param int $courseid The id of the course that is being viewed
 * @param int $userid The id of the user that is being tested against. Set this to 0 if you would just like to test against the currently logged in user.
 * @param int $timestart ?
 * @param int $timeend ?
 * @return boolean
 * @todo Finish documenting this function
 */
function enrol_student($userid, $courseid, $timestart=0, $timeend=0) {

    if (!$course = get_record('course', 'id', $courseid)) {  // Check course
        return false;
    }
    if (!$user = get_record('user', 'id', $userid)) {        // Check user
        return false;
    }
    if ($student = get_record('user_students', 'userid', $userid, 'course', $courseid)) {
        $student->timestart = $timestart;
        $student->timeend = $timeend;
        $student->time = time();
        return update_record('user_students', $student);

    } else {
        $student->userid = $userid;
        $student->course = $courseid;
        $student->timestart = $timestart;
        $student->timeend = $timeend;
        $student->time = time();
        return insert_record('user_students', $student);
    }
}

/**
 * Unenrols a student from a given course
 *
 * @param int $courseid The id of the course that is being viewed, if any
 * @param int $userid The id of the user that is being tested against.
 * @return boolean
 */
function unenrol_student($userid, $courseid=0) {

    if ($courseid) {
        /// First delete any crucial stuff that might still send mail
        if ($forums = get_records('forum', 'course', $courseid)) {
            foreach ($forums as $forum) {
                delete_records('forum_subscriptions', 'forum', $forum->id, 'userid', $userid);
            }
        }
        if ($groups = get_groups($courseid, $userid)) {
            foreach ($groups as $group) {
                delete_records('groups_members', 'groupid', $group->id, 'userid', $userid);
            }
        }
        return delete_records('user_students', 'userid', $userid, 'course', $courseid);

    } else {
        delete_records('forum_subscriptions', 'userid', $userid);
        delete_records('groups_members', 'userid', $userid);
        return delete_records('user_students', 'userid', $userid);
    }
}

/**
 * Add a teacher to a given course
 *
  * @uses $USER
 * @param int $courseid The id of the course that is being viewed, if any
 * @param int $userid The id of the user that is being tested against. Set this to 0 if you would just like to test against the currently logged in user.
 * @param int $editall ?
 * @param string $role ?
 * @param int $timestart ?
 * @param int $timeend ?
 * @return boolean
 * @todo Finish documenting this function
 */
function add_teacher($userid, $courseid, $editall=1, $role='', $timestart=0, $timeend=0) {
    global $CFG;

    if ($teacher = get_record('user_teachers', 'userid', $userid, 'course', $courseid)) {
        $newteacher = NULL;
        $newteacher->id = $teacher->id;
        $newteacher->editall = $editall;
        if ($role) {
            $newteacher->role = $role;
        }
        if ($timestart) {
            $newteacher->timestart = $timestart;
        }
        if ($timeend) {
            $newteacher->timeend = $timeend;
        }
        return update_record('user_teachers', $newteacher);
    }

    if (!record_exists('user', 'id', $userid)) {
        return false;   // no such user
    }

    if (!record_exists('course', 'id', $courseid)) {
        return false;   // no such course
    }

    $teacher = NULL;
    $teacher->userid  = $userid;
    $teacher->course  = $courseid;
    $teacher->editall = $editall;
    $teacher->role    = $role;
    $teacher->timemodified = time();
    $newteacher->timestart = $timestart;
    $newteacher->timeend = $timeend;
    if ($student = get_record('user_students', 'userid', $userid, 'course', $courseid)) {
        $teacher->timestart = $student->timestart;
        $teacher->timeend = $student->timeend;
        $teacher->timeaccess = $student->timeaccess;
    }

    if (record_exists('user_teachers', 'course', $courseid)) {
        $teacher->authority = 2;
    } else {
        $teacher->authority = 1;
    }
    delete_records('user_students', 'userid', $userid, 'course', $courseid); // Unenrol as student

    /// Add forum subscriptions for new users
    require_once('../mod/forum/lib.php');
    forum_add_user($userid, $courseid);

    return insert_record('user_teachers', $teacher);

}

/**
 * Removes a teacher from a given course (or ALL courses)
 * Does not delete the user account
 *
 * @param int $courseid The id of the course that is being viewed, if any
 * @param int $userid The id of the user that is being tested against. 
 * @return boolean
 */
function remove_teacher($userid, $courseid=0) {
    if ($courseid) {
        /// First delete any crucial stuff that might still send mail
        if ($forums = get_records('forum', 'course', $courseid)) {
            foreach ($forums as $forum) {
                delete_records('forum_subscriptions', 'forum', $forum->id, 'userid', $userid);
            }
        }

        /// Next if the teacher is not registered as a student, but is
        /// a member of a group, remove them from the group.
        if (!isstudent($courseid, $userid)) {
            if ($groups = get_groups($courseid, $userid)) {
                foreach ($groups as $group) {
                    delete_records('groups_members', 'groupid', $group->id, 'userid', $userid);
                }
            }
        }

        return delete_records('user_teachers', 'userid', $userid, 'course', $courseid);
    } else {
        delete_records('forum_subscriptions', 'userid', $userid);
        return delete_records('user_teachers', 'userid', $userid);
    }
}

/**
 * Add a creator to the site
 *
 * @param int $userid The id of the user that is being tested against. 
 * @return boolean
 */
function add_creator($userid) {

    if (!record_exists('user_admins', 'userid', $userid)) {
        if (record_exists('user', 'id', $userid)) {
            $creator->userid = $userid;
            return insert_record('user_coursecreators', $creator);
        }
        return false;
    }
    return true;
}

/**
 * Remove a creator from a site
 *
  * @uses $db
 * @param int $userid The id of the user that is being tested against.
 * @return boolean
 */
function remove_creator($userid) {
    global $db;

    return delete_records('user_coursecreators', 'userid', $userid);
}

/**
 * Add an admin to a site
 *
 * @uses SITEID
 * @param int $userid The id of the user that is being tested against.
 * @return boolean
 */
function add_admin($userid) {

    if (!record_exists('user_admins', 'userid', $userid)) {
        if (record_exists('user', 'id', $userid)) {
            $admin->userid = $userid;

            // any admin is also a teacher on the site course
            if (!record_exists('user_teachers', 'course', SITEID, 'userid', $userid)) {
                if (!add_teacher($userid, SITEID)) {
                    return false;
                }
            }

            return insert_record('user_admins', $admin);
        }
        return false;
    }
    return true;
}

/**
 * Removes an admin from a site
 *
  * @uses $db
  * @uses SITEID
 * @param int $userid The id of the user that is being tested against.
 * @return boolean
 */
function remove_admin($userid) {
    global $db;

    // remove also from the list of site teachers
    remove_teacher($userid, SITEID);

    return delete_records('user_admins', 'userid', $userid);
}

/**
 * Clear a course out completely, deleting all content
 * but don't delete the course itself
 *
 * @uses $USER
 * @uses $SESSION
 * @uses $CFG
 * @param int $courseid The id of the course that is being viewed
 * @param boolean $showfeedback Set this to false to suppress notifications from being printed as the functions performs its steps.
 * @return boolean
 */
function remove_course_contents($courseid, $showfeedback=true) {

    global $CFG, $THEME, $USER, $SESSION;

    $result = true;

    if (! $course = get_record('course', 'id', $courseid)) {
        error('Course ID was incorrect (can\'t find it)');
    }

    $strdeleted = get_string('deleted');

    // First delete every instance of every module

    if ($allmods = get_records('modules') ) {
        foreach ($allmods as $mod) {
            $modname = $mod->name;
            $modfile = $CFG->dirroot .'/mod/'. $modname .'/lib.php';
            $moddelete = $modname .'_delete_instance';       // Delete everything connected to an instance
            $moddeletecourse = $modname .'_delete_course';   // Delete other stray stuff (uncommon)
            $count=0;
            if (file_exists($modfile)) {
                include_once($modfile);
                if (function_exists($moddelete)) {
                    if ($instances = get_records($modname, 'course', $course->id)) {
                        foreach ($instances as $instance) {
                            if ($moddelete($instance->id)) {
                                $count++;
                            } else {
                                notify('Could not delete '. $modname .' instance '. $instance->id .' ('. $instance->name .')');
                                $result = false;
                            }
                        }
                    }
                } else {
                    notify('Function '. $moddelete() .'doesn\'t exist!');
                    $result = false;
                }

                if (function_exists($moddeletecourse)) {
                    $moddeletecourse($course);
                }
            }
            if ($showfeedback) {
                notify($strdeleted .' '. $count .' x '. $modname);
            }
        }
    } else {
        error('No modules are installed!');
    }

    // Delete any user stuff

    if (delete_records('user_students', 'course', $course->id)) {
        if ($showfeedback) {
            notify($strdeleted .' user_students');
        }
    } else {
        $result = false;
    }

    if (delete_records('user_teachers', 'course', $course->id)) {
        if ($showfeedback) {
            notify($strdeleted .' user_teachers');
        }
    } else {
        $result = false;
    }

    // Delete any groups

    if ($groups = get_records('groups', 'courseid', $course->id)) {
        foreach ($groups as $group) {
            if (delete_records('groups_members', 'groupid', $group->id)) {
                if ($showfeedback) {
                    notify($strdeleted .' groups_members');
                }
            } else {
                $result = false;
            }
            if (delete_records('groups', 'id', $group->id)) {
                if ($showfeedback) {
                    notify($strdeleted .' groups');
                }
            } else {
                $result = false;
            }
        }
    }

    // Delete events

    if (delete_records('event', 'courseid', $course->id)) {
        if ($showfeedback) {
            notify($strdeleted .' event');
        }
    } else {
        $result = false;
    }

    // Delete logs

    if (delete_records('log', 'course', $course->id)) {
        if ($showfeedback) {
            notify($strdeleted .' log');
        }
    } else {
        $result = false;
    }

    // Delete any course stuff

    if (delete_records('course_sections', 'course', $course->id)) {
        if ($showfeedback) {
            notify($strdeleted .' course_sections');
        }
    } else {
        $result = false;
    }

    if (delete_records('course_modules', 'course', $course->id)) {
        if ($showfeedback) {
            notify($strdeleted .' course_modules');
        }
    } else {
        $result = false;
    }

    return $result;

}

/**
 * This function will empty a course of USER data as much as
/// possible. It will retain the activities and the structure
/// of the course.
 *
 * @uses $USER
 * @uses $THEME
 * @uses $SESSION
 * @uses $CFG
 * @param int $courseid The id of the course that is being viewed
 * @param boolean $showfeedback Set this to false to suppress notifications from being printed as the functions performs its steps.
 * @param boolean $removestudents ?
 * @param boolean $removeteachers ?
 * @param boolean $removegroups ?
 * @param boolean $removeevents ?
 * @param boolean $removelogs ?
 * @return boolean
 * @todo Finish documenting this function
 */
function remove_course_userdata($courseid, $showfeedback=true,
                                $removestudents=true, $removeteachers=false, $removegroups=true,
                                $removeevents=true, $removelogs=false) {

    global $CFG, $THEME, $USER, $SESSION;

    $result = true;

    if (! $course = get_record('course', 'id', $courseid)) {
        error('Course ID was incorrect (can\'t find it)');
    }

    $strdeleted = get_string('deleted');

    // Look in every instance of every module for data to delete

    if ($allmods = get_records('modules') ) {
        foreach ($allmods as $mod) {
            $modname = $mod->name;
            $modfile = $CFG->dirroot .'/mod/'. $modname .'/lib.php';
            $moddeleteuserdata = $modname .'_delete_userdata';   // Function to delete user data
            $count=0;
            if (file_exists($modfile)) {
                @include_once($modfile);
                if (function_exists($moddeleteuserdata)) {
                    $moddeleteuserdata($course, $showfeedback);
                }
            }
        }
    } else {
        error('No modules are installed!');
    }

    // Delete other stuff

    if ($removestudents) {
        /// Delete student enrolments
        if (delete_records('user_students', 'course', $course->id)) {
            if ($showfeedback) {
                notify($strdeleted .' user_students');
            }
        } else {
            $result = false;
        }
        /// Delete group members (but keep the groups)
        if ($groups = get_records('groups', 'courseid', $course->id)) {
            foreach ($groups as $group) {
                if (delete_records('groups_members', 'groupid', $group->id)) {
                    if ($showfeedback) {
                        notify($strdeleted .' groups_members');
                    }
                } else {
                    $result = false;
                }
            }
        }
    }

    if ($removeteachers) {
        if (delete_records('user_teachers', 'course', $course->id)) {
            if ($showfeedback) {
                notify($strdeleted .' user_teachers');
            }
        } else {
            $result = false;
        }
    }

    if ($removegroups) {
        if ($groups = get_records('groups', 'courseid', $course->id)) {
            foreach ($groups as $group) {
                if (delete_records('groups', 'id', $group->id)) {
                    if ($showfeedback) {
                        notify($strdeleted .' groups');
                    }
                } else {
                    $result = false;
                }
            }
        }
    }

    if ($removeevents) {
        if (delete_records('event', 'courseid', $course->id)) {
            if ($showfeedback) {
                notify($strdeleted .' event');
            }
        } else {
            $result = false;
        }
    }

    if ($removelogs) {
        if (delete_records('log', 'course', $course->id)) {
            if ($showfeedback) {
                notify($strdeleted .' log');
            }
        } else {
            $result = false;
        }
    }

    return $result;

}



/// GROUPS /////////////////////////////////////////////////////////


/**
* Returns a boolean: is the user a member of the given group?
*
* @param    type description
 * @todo Finish documenting this function
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

    return record_exists('groups_members', 'groupid', $groupid, 'userid', $userid);
}

/**
 * Returns the group ID of the current user in the given course
 *
 * @uses $USER
 * @param int $courseid The course being examined - relates to id field in 'course' table.
 * @todo Finish documenting this function
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
 * @param course $course A {@link $COURSE} object
 * @param array? $cm A course module object
 * @return int A group mode (NOGROUPS, SEPARATEGROUPS or VISIBLEGROUPS)
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
 * @uses $SESSION
 * @param int $courseid The course being examined - relates to id field in 'course' table.
 * @param int $groupid The group being examined.
 * @return int Current group id which was set by this function
 * @todo Finish documenting this function
 */
function set_current_group($courseid, $groupid) {
    global $SESSION;

    return $SESSION->currentgroup[$courseid] = $groupid;
}


/**
 * Gets the current group for the current user as an id or an object
 *
 * @uses $CFG
 * @uses $SESSION
 * @param int $courseid The course being examined - relates to id field in 'course' table.
 * @param boolean $full ?
 * @todo Finish documenting this function
 */
function get_current_group($courseid, $full=false) {
    global $SESSION, $USER;

    if (!isset($SESSION->currentgroup[$courseid])) {
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
 * @uses VISIBLEGROUPS
 * @param course $course A {@link $COURSE} object
 * @param int $groupmode Either NOGROUPS, SEPARATEGROUPS or VISIBLEGROUPS
 * @param int $groupid Will try to use this optional parameter to
 *            reset the current group for the user
 * @return int|false Returns the current group id or false if error.
 * @todo Finish documenting this function
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
 * @uses SEPARATEGROUPS
 * @uses VISIBLEGROUPS
 * @param course $course A {@link $COURSE} object
 * @param int $groupmode Either NOGROUPS, SEPARATEGROUPS or VISIBLEGROUPS
 * @param string $urlroot ?
 * @todo Finish documenting this function
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
        if ($groups = get_records_menu('groups', 'courseid', $course->id, 'name ASC', 'id,name')) {
            echo '<div align="center">';
            print_group_menu($groups, $groupmode, $currentgroup, $urlroot);
            echo '</div>';
        }
    }

    return $currentgroup;
}



/// CORRESPONDENCE  ////////////////////////////////////////////////

/**
 * Send an email to a specified user
 *
 * @uses $CFG
 * @uses $_SERVER
 * @uses SITEID
 * @param user $user  A {@link $USER} object
 * @param user $from A {@link $USER} object
 * @param string $subject plain text subject line of the email
 * @param string $messagetext plain text version of the message
 * @param string $messagehtml complete html version of the message (optional)
 * @param string $attachment a file on the filesystem, relative to $CFG->dataroot
 * @param string $attachname the name of the file (extension indicates MIME)
 * @param boolean $usetrueaddress determines whether $from email address should 
 *          be sent out. Will be overruled by user profile setting for maildisplay
 * @return boolean|string Returns "true" if mail was sent OK, "emailstop" if email 
 *          was blocked by user and "false" if there was another sort of error.
 */
function email_to_user($user, $from, $subject, $messagetext, $messagehtml='', $attachment='', $attachname='', $usetrueaddress=true) {

    global $CFG, $_SERVER;

    global $course;                // This is a bit of an ugly hack to be gotten rid of later
    if (!empty($course->lang)) {   // Course language is defined
        $CFG->courselang = $course->lang;
    }

    include_once($CFG->libdir .'/phpmailer/class.phpmailer.php');

    if (empty($user)) {
        return false;
    }

    if (!empty($user->emailstop)) {
        return 'emailstop';
    }

    $mail = new phpmailer;

    $mail->Version = 'Moodle '. $CFG->version;           // mailer version
    $mail->PluginDir = $CFG->libdir .'/phpmailer/';      // plugin directory (eg smtp plugin)


    if (current_language() != 'en') {
        $mail->CharSet = get_string('thischarset');
    }

    if ($CFG->smtphosts == 'qmail') {
        $mail->IsQmail();                              // use Qmail system

    } else if (empty($CFG->smtphosts)) {
        $mail->IsMail();                               // use PHP mail() = sendmail

    } else {
        $mail->IsSMTP();                               // use SMTP directly
        if ($CFG->debug > 7) {
            echo '<pre>' . "\n";
            $mail->SMTPDebug = true;
        }
        $mail->Host = $CFG->smtphosts;               // specify main and backup servers

        if ($CFG->smtpuser) {                          // Use SMTP authentication
            $mail->SMTPAuth = true;
            $mail->Username = $CFG->smtpuser;
            $mail->Password = $CFG->smtppass;
        }
    }

    $adminuser = get_admin();

    $mail->Sender   = $adminuser->email;

    if (is_string($from)) { // So we can pass whatever we want if there is need
        $mail->From     = $CFG->noreplyaddress;
        $mail->FromName = $from;
    } else if ($usetrueaddress and $from->maildisplay) {
        $mail->From     = $from->email;
        $mail->FromName = fullname($from);
    } else {
        $mail->From     = $CFG->noreplyaddress;
        $mail->FromName = fullname($from);
    }
    $mail->Subject  =  stripslashes($subject);

    $mail->AddAddress($user->email, fullname($user) );

    $mail->WordWrap = 79;                               // set word wrap

    if (!empty($from->customheaders)) {                 // Add custom headers
        if (is_array($from->customheaders)) {
            foreach ($from->customheaders as $customheader) {
                $mail->AddCustomHeader($customheader);
            }
        } else {
            $mail->AddCustomHeader($from->customheaders);
        }
    }

    if ($messagehtml) {
        $mail->IsHTML(true);
        $mail->Encoding = 'quoted-printable';           // Encoding to use
        $mail->Body    =  $messagehtml;
        $mail->AltBody =  "\n$messagetext\n";
    } else {
        $mail->IsHTML(false);
        $mail->Body =  "\n$messagetext\n";
    }

    if ($attachment && $attachname) {
        if (ereg( "\\.\\." ,$attachment )) {    // Security check for ".." in dir path
            $mail->AddAddress($adminuser->email, fullname($adminuser) );
            $mail->AddStringAttachment('Error in attachment.  User attempted to attach a filename with a unsafe name.', 'error.txt', '8bit', 'text/plain');
        } else {
            include_once($CFG->dirroot .'/files/mimetypes.php');
            $mimetype = mimeinfo('type', $attachname);
            $mail->AddAttachment($CFG->dataroot .'/'. $attachment, $attachname, 'base64', $mimetype);
        }
    }

    if ($mail->Send()) {
        return true;
    } else {
        mtrace('ERROR: '. $mail->ErrorInfo);
        add_to_log(SITEID, 'library', 'mailer', $_SERVER['REQUEST_URI'], 'ERROR: '. $mail->ErrorInfo);
        return false;
    }
}

/**
 * Resets specified user's password and send the new password to the user via email.
 *
 * @uses $CFG
 * @param user $user A {@link $USER} object
 * @return boolean|string Returns "true" if mail was sent OK, "emailstop" if email 
 *          was blocked by user and "false" if there was another sort of error.
 */
function reset_password_and_mail($user) {

    global $CFG;

    $site  = get_site();
    $from = get_admin();

    $newpassword = generate_password();

    if (! set_field('user', 'password', md5($newpassword), 'id', $user->id) ) {
        error('Could not set user password!');
    }

    $a->firstname = $user->firstname;
    $a->sitename = $site->fullname;
    $a->username = $user->username;
    $a->newpassword = $newpassword;
    $a->link = $CFG->wwwroot .'/login/change_password.php';
    $a->signoff = fullname($from, true).' ('. $from->email .')';

    $message = get_string('newpasswordtext', '', $a);

    $subject  = $site->fullname .': '. get_string('changedpassword');

    return email_to_user($user, $from, $subject, $message);

}

/**
 * Send email to specified user with confirmation text and activation link.
 *
 * @uses $CFG
 * @param user $user A {@link $USER} object
 * @return boolean|string Returns "true" if mail was sent OK, "emailstop" if email 
 *          was blocked by user and "false" if there was another sort of error.
 */
 function send_confirmation_email($user) {

    global $CFG;

    $site = get_site();
    $from = get_admin();

    $data->firstname = $user->firstname;
    $data->sitename = $site->fullname;
    $data->link = $CFG->wwwroot .'/login/confirm.php?p='. $user->secret .'&amp;s='. $user->username;
    $data->admin = fullname($from) .' ('. $from->email .')';

    $message = get_string('emailconfirmation', '', $data);
    $subject = get_string('emailconfirmationsubject', '', $site->fullname);

    $messagehtml = text_to_html($message, false, false, true);

    return email_to_user($user, $from, $subject, $message, $messagehtml);

}

/**
 * send_password_change_confirmation_email.
 *
 * @uses $CFG
 * @param user $user A {@link $USER} object
 * @return boolean|string Returns "true" if mail was sent OK, "emailstop" if email 
 *          was blocked by user and "false" if there was another sort of error.
 * @todo Finish documenting this function
 */
function send_password_change_confirmation_email($user) {

    global $CFG;

    $site = get_site();
    $from = get_admin();

    $data->firstname = $user->firstname;
    $data->sitename = $site->fullname;
    $data->link = $CFG->wwwroot .'/login/forgot_password.php?p='. $user->secret .'&amp;s='. $user->username;
    $data->admin = fullname($from).' ('. $from->email .')';

    $message = get_string('emailpasswordconfirmation', '', $data);
    $subject = get_string('emailpasswordconfirmationsubject', '', $site->fullname);

    return email_to_user($user, $from, $subject, $message);

}

/**
 * Check that an email is allowed.  It returns an error message if there
 * was a problem.
 *
 * @param    type description
 * @todo Finish documenting this function
 */
function email_is_not_allowed($email) {

    global $CFG;

    if (!empty($CFG->allowemailaddresses)) {
        $allowed = explode(' ', $CFG->allowemailaddresses);
        foreach ($allowed as $allowedpattern) {
            $allowedpattern = trim($allowedpattern);
            if (!$allowedpattern) {
                continue;
            }
            if (strpos($email, $allowedpattern) !== false) {  // Match!
                return false;
            }
        }
        return get_string('emailonlyallowed', '', $CFG->allowemailaddresses);

    } else if (!empty($CFG->denyemailaddresses)) {
        $denied = explode(' ', $CFG->denyemailaddresses);
        foreach ($denied as $deniedpattern) {
            $deniedpattern = trim($deniedpattern);
            if (!$deniedpattern) {
                continue;
            }
            if (strpos($email, $deniedpattern) !== false) {   // Match!
                return get_string('emailnotallowed', '', $CFG->denyemailaddresses);
            }
        }
    }

    return false;
}


/// FILE HANDLING  /////////////////////////////////////////////

/**
 * Create a directory.
 *
 * @uses $CFG
 * @param string $directory  a string of directory names under $CFG->dataroot eg  stuff/assignment/1
 * param boolean $shownotices If true then notification messages will be printed out on error.
 * @return string|false Returns full path to directory if successful, false if not
 */
function make_upload_directory($directory, $shownotices=true) {

    global $CFG;

    $currdir = $CFG->dataroot;

    umask(0000);

    if (!file_exists($currdir)) {
        if (! mkdir($currdir, $CFG->directorypermissions)) {
            if ($shownotices) {
                notify('ERROR: You need to create the directory '. $currdir .' with web server write access');
            }
            return false;
        }
    }

    $dirarray = explode('/', $directory);

    foreach ($dirarray as $dir) {
        $currdir = $currdir .'/'. $dir;
        if (! file_exists($currdir)) {
            if (! mkdir($currdir, $CFG->directorypermissions)) {
                if ($shownotices) {
                    notify('ERROR: Could not find or create a directory ('. $currdir .')');
                }
                return false;
            }
            @chmod($currdir, $CFG->directorypermissions);  // Just in case mkdir didn't do it
        }
    }

    return $currdir;
}

/**
 * Makes an upload directory for a particular module.
 *
 * @uses $CFG
 * @param int $courseid The id of the course in question - maps to id field of 'course' table.
 * @return string|false Returns full path to directory if successful, false if not
 * @todo Finish documenting this function
 */
function make_mod_upload_directory($courseid) {
    global $CFG;

    if (! $moddata = make_upload_directory($courseid .'/'. $CFG->moddata)) {
        return false;
    }

    $strreadme = get_string('readme');

    if (file_exists($CFG->dirroot .'/lang/'. $CFG->lang .'/docs/module_files.txt')) {
        copy($CFG->dirroot .'/lang/'. $CFG->lang .'/docs/module_files.txt', $moddata .'/'. $strreadme .'.txt');
    } else {
        copy($CFG->dirroot .'/lang/en/docs/module_files.txt', $moddata .'/'. $strreadme .'.txt');
    }
    return $moddata;
}

/**
 * Returns current name of file on disk if it exists.
 *
 * @param string $newfile File to be verified
 * @return string Current name of file on disk if true
 * @todo Finish documenting this function
 */
function valid_uploaded_file($newfile) {
    if (empty($newfile)) {
        return '';
    }
    if (is_uploaded_file($newfile['tmp_name']) and $newfile['size'] > 0) {
        return $newfile['tmp_name'];
    } else {
        return '';
    }
}

/**
 * Returns the maximum size for uploading files.
 *
 * There are seven possible upload limits:
 * 1. in Apache using LimitRequestBody (no way of checking or changing this)
 * 2. in php.ini for 'upload_max_filesize' (can not be changed inside PHP)
 * 3. in .htaccess for 'upload_max_filesize' (can not be changed inside PHP)
 * 4. in php.ini for 'post_max_size' (can not be changed inside PHP)
 * 5. by the Moodle admin in $CFG->maxbytes
 * 6. by the teacher in the current course $course->maxbytes
 * 7. by the teacher for the current module, eg $assignment->maxbytes
 *
 * These last two are passed to this function as arguments (in bytes).
 * Anything defined as 0 is ignored.
 * The smallest of all the non-zero numbers is returned.
 *
 * @param int $sizebytes ?
 * @param int $coursebytes Current course $course->maxbytes (in bytes)
 * @param int $modulebytes Current module ->maxbytes (in bytes)
 * @return int The maximum size for uploading files.
 * @todo Finish documenting this function
 */
function get_max_upload_file_size($sitebytes=0, $coursebytes=0, $modulebytes=0) {

    if (! $filesize = ini_get('upload_max_filesize')) {
        $filesize = '5M';
    }
    $minimumsize = get_real_size($filesize);

    if ($postsize = ini_get('post_max_size')) {
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

/**
 * Related to the above function - this function returns an
 * array of possible sizes in an array, translated to the
 * local language.
 *
 * @uses SORT_NUMERIC
 * @param int $sizebytes ?
 * @param int $coursebytes Current course $course->maxbytes (in bytes)
 * @param int $modulebytes Current module ->maxbytes (in bytes)
 * @return int
 * @todo Finish documenting this function
 */
function get_max_upload_sizes($sitebytes=0, $coursebytes=0, $modulebytes=0) {

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

/**
 * If there has been an error uploading a file, print the appropriate error message
 * Numerical constants used as constant definitions not added until PHP version 4.2.0
 *
 * $filearray is a 1-dimensional sub-array of the $_FILES array
 * eg $filearray = $_FILES['userfile1']
 * If left empty then the first element of the $_FILES array will be used 
 *
 * @uses $_FILES
 * @param array $filearray  A 1-dimensional sub-array of the $_FILES array
 * @param boolean $returnerror ?
 * @return boolean
 * @todo Finish documenting this function
 */
function print_file_upload_error($filearray = '', $returnerror = false) {

    if ($filearray == '' or !isset($filearray['error'])) {

        if (empty($_FILES)) return false;

        $files = $_FILES; /// so we don't mess up the _FILES array for subsequent code
        $filearray = array_shift($files); /// use first element of array
    }

    switch ($filearray['error']) {

        case 0: // UPLOAD_ERR_OK
            if ($filearray['size'] > 0) {
                $errmessage = get_string('uploadproblem', $filearray['name']);
            } else {
                $errmessage = get_string('uploadnofilefound'); /// probably a dud file name
            }
            break;

        case 1: // UPLOAD_ERR_INI_SIZE
            $errmessage = get_string('uploadserverlimit');
            break;

        case 2: // UPLOAD_ERR_FORM_SIZE
            $errmessage = get_string('uploadformlimit');
            break;

        case 3: // UPLOAD_ERR_PARTIAL
            $errmessage = get_string('uploadpartialfile');
            break;

        case 4: // UPLOAD_ERR_NO_FILE
            $errmessage = get_string('uploadnofilefound');
            break;

        default:
            $errmessage = get_string('uploadproblem', $filearray['name']);
    }

    if ($returnerror) {
        return $errmessage;
    } else {
        notify($errmessage);
        return true;
    }

}

/**
 * Returns an array with all the filenames in
 * all subdirectories, relative to the given rootdir.
 * If excludefile is defined, then that file/directory is ignored
 * If getdirs is true, then (sub)directories are included in the output
 * If getfiles is true, then files are included in the output
 * (at least one of these must be true!)
 *
 * @param string $rootdir  ?
 * @param string $excludefile  If defined then the specified file/directory is ignored
 * @param boolean $descend  ?
 * @param boolean $getdirs  If true then (sub)directories are included in the output
 * @param boolean $getfiles  If true then files are included in the output
 * @return array An array with all the filenames in
 * all subdirectories, relative to the given rootdir
 * @todo Finish documenting this function. Add examples of $excludefile usage.
 */
function get_directory_list($rootdir, $excludefile='', $descend=true, $getdirs=false, $getfiles=true) {

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
        if ($firstchar == '.' or $file == 'CVS' or $file == $excludefile) {
            continue;
        }
        $fullfile = $rootdir .'/'. $file;
        if (filetype($fullfile) == 'dir') {
            if ($getdirs) {
                $dirs[] = $file;
            }
            if ($descend) {
                $subdirs = get_directory_list($fullfile, $excludefile, $descend, $getdirs, $getfiles);
                foreach ($subdirs as $subdir) {
                    $dirs[] = $file .'/'. $subdir;
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

/**
 * Adds up all the files in a directory and works out the size.
 *
 * @param string $rootdir  ?
 * @param string $excludefile  ?
 * @return array
 * @todo Finish documenting this function
 */
function get_directory_size($rootdir, $excludefile='') {

    $size = 0;

    if (!is_dir($rootdir)) {          // Must be a directory
        return $dirs;
    }

    if (!$dir = @opendir($rootdir)) {  // Can't open it for some reason
        return $dirs;
    }

    while (false !== ($file = readdir($dir))) {
        $firstchar = substr($file, 0, 1);
        if ($firstchar == '.' or $file == 'CVS' or $file == $excludefile) {
            continue;
        }
        $fullfile = $rootdir .'/'. $file;
        if (filetype($fullfile) == 'dir') {
            $size += get_directory_size($fullfile, $excludefile);
        } else {
            $size += filesize($fullfile);
        }
    }
    closedir($dir);

    return $size;
}

/**
 * Converts numbers like 10M into bytes.
 *
 * @param mixed $size The size to be converted
 * @return mixed
 */
function get_real_size($size=0) {
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

/**
 * Converts bytes into display form
 *
 * @param string $size  ?
 * @return string
 * @staticvar string $gb Localized string for size in gigabytes
 * @staticvar string $mb Localized string for size in megabytes
 * @staticvar string $kb Localized string for size in kilobytes
 * @staticvar string $b Localized string for size in bytes
 * @todo Finish documenting this function. Verify return type.
 */
function display_size($size) {

    static $gb, $mb, $kb, $b;

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
        $size = $size .' '. $b;
    }
    return $size;
}

/**
 * Cleans a given filename by removing suspicious or troublesome characters
 * Only these are allowed:
 *    alphanumeric _ - .
 *
 * @param string $string  ?
 * @return string
 * @todo Finish documenting this function
 */
function clean_filename($string) {
    $string = eregi_replace("\.\.+", '', $string);
    $string = preg_replace('/[^\.a-zA-Z\d\_-]/','_', $string ); // only allowed chars
    $string = eregi_replace("_+", '_', $string);
    return $string;
}


/// STRING TRANSLATION  ////////////////////////////////////////

/**
 * Returns the code for the current language
 *
 * @uses $CFG
 * @param $USER
 * @param $SESSION
 * @return string
 */
function current_language() {
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

/**
 * Given a string to translate - prints it out.
 *
 * @param string $identifier ?
 * @param string $module ?
 * @param mixed $a ?
 */
function print_string($identifier, $module='', $a=NULL) {
    echo get_string($identifier, $module, $a);
}

/**
 * Return the translated string specified by $identifier as
 * for $module.  Uses the same format files as STphp.
 * $a is an object, string or number that can be used
 * within translation strings
 *
 * eg "hello \$a->firstname \$a->lastname"
 * or "hello \$a"
 *
 * @uses $CFG
 * @param string $identifier ?
 * @param string $module ?
 * @param mixed $a ?
 * @return string
 * @todo Finish documenting this function
 */
function get_string($identifier, $module='', $a=NULL) { 

    global $CFG;

    global $course;     /// Not a nice hack, but quick
    if (empty($CFG->courselang)) {
        if (!empty($course->lang)) {
            $CFG->courselang = $course->lang;
        }
    }

    $lang = current_language();

    if ($module == '') {
        $module = 'moodle';
    }

    $langpath = $CFG->dirroot .'/lang';
    $langfile = $langpath .'/'. $lang .'/'. $module .'.php';

    // Look for the string - if found then return it

    if (file_exists($langfile)) {
        if ($result = get_string_from_file($identifier, $langfile, "\$resultstring")) {
            eval($result);
            return $resultstring;
        }
    }

    // If it's a module, then look within the module pack itself mod/xxxx/lang/en/module.php

    if ($module != 'moodle') {
        $modlangpath = $CFG->dirroot .'/mod/'. $module .'/lang';
        $langfile = $modlangpath .'/'. $lang .'/'. $module .'.php';
        if (file_exists($langfile)) {
            if ($result = get_string_from_file($identifier, $langfile, "\$resultstring")) {
                eval($result);
                return $resultstring;
            }
        }
    }

    // If the preferred language was English we can abort now
    if ($lang == 'en') {
        return '[['. $identifier .']]';
    }

    // Is a parent language defined?  If so, try it.

    if ($result = get_string_from_file('parentlanguage', $langpath .'/'. $lang .'/moodle.php', "\$parentlang")) {
        eval($result);
        if (!empty($parentlang)) {
            $langfile = $langpath .'/'. $parentlang .'/'. $module .'.php';
            if (file_exists($langfile)) {
                if ($result = get_string_from_file($identifier, $langfile, "\$resultstring")) {
                    eval($result);
                    return $resultstring;
                }
            }
        }
    }

    // Our only remaining option is to try English

    $langfile = $langpath .'/en/'. $module .'.php';
    if (!file_exists($langfile)) {
        return 'ERROR: No lang file ('. $langpath .'/en/'. $module .'.php)!';
    }
    if ($result = get_string_from_file($identifier, $langfile, "\$resultstring")) {
        eval($result);
        return $resultstring;
    }

    // If it's a module, then look within the module pack itself mod/xxxx/lang/en/module.php

    if ($module != 'moodle') {
        $langfile = $modlangpath .'/en/'. $module .'.php';
        if (file_exists($langfile)) {
            if ($result = get_string_from_file($identifier, $langfile, "\$resultstring")) {
                eval($result);
                return $resultstring;
            }
        }
    }

    return '[['. $identifier .']]';  // Last resort
}

/**
 * This function is only used from {@link get_string()}.
 *
 * @internal Only used from get_string, not meant to be public api
 * @param string $identifier ?
 * @param string $langfile ?
 * @param string $destination ?
 * @return string|false ?
 * @staticvar array $strings Localized strings
 * @todo Finish documenting this function.
 */
function get_string_from_file($identifier, $langfile, $destination) {

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

    return $destination .'= sprintf("'. $string[$identifier] .'");';
}

/**
 * Converts an array of strings to their localized value.
 *
 * @param array $array An array of strings
 * @param string $module The language module that these strings can be found in.
 * @return string
 */
function get_strings($array, $module='') {

   $string = NULL;
   foreach ($array as $item) {
       $string->$item = get_string($item, $module);
   }
   return $string;
}

/**
 * Returns a list of language codes and their full names
 *
 * @uses $CFG
 * @return array An associative array with contents in the form of LanguageCode => LanguageName
 * @todo Finish documenting this function
 */
function get_list_of_languages() {
    global $CFG;

    $languages = array();

    if (!empty($CFG->langlist)) {       // use admin's list of languages
        $langlist = explode(',', $CFG->langlist);
        foreach ($langlist as $lang) {
            if (file_exists($CFG->dirroot .'/lang/'. $lang .'/moodle.php')) {
                include($CFG->dirroot .'/lang/'. $lang .'/moodle.php');
                $languages[$lang] = $string['thislanguage'].' ('. $lang .')';
                unset($string);
            }
        }
    } else {
        if (!$langdirs = get_list_of_plugins('lang')) {
            return false;
        }
        foreach ($langdirs as $lang) {
            include($CFG->dirroot .'/lang/'. $lang .'/moodle.php');
            $languages[$lang] = $string['thislanguage'] .' ('. $lang .')';
            unset($string);
        }
    }

    return $languages;
}

/**
 * Returns a list of country names in the current language
 *
 * @uses $CFG
 * @uses $USER
 * @return string?
 * @todo Finish documenting this function. 
 */
function get_list_of_countries() {
    global $CFG, $USER;

    $lang = current_language();

    if (!file_exists($CFG->dirroot .'/lang/'. $lang .'/countries.php')) {
        if ($parentlang = get_string('parentlanguage')) {
            if (file_exists($CFG->dirroot .'/lang/'. $parentlang .'/countries.php')) {
                $lang = $parentlang;
            } else {
                $lang = 'en';  // countries.php must exist in this pack
            }
        } else {
            $lang = 'en';  // countries.php must exist in this pack
        }
    }

    include($CFG->dirroot .'/lang/'. $lang .'/countries.php');

    if (!empty($string)) {
        asort($string);
    }

    return $string;
}

/**
 * Returns a list of picture names in the current language
 *
 * @uses $CFG
 * @return string?
 * @todo Finish documenting this function.
 */
function get_list_of_pixnames() { 
    global $CFG;

    $lang = current_language();

    if (!file_exists($CFG->dirroot .'/lang/'. $lang .'/pix.php')) {
        if ($parentlang = get_string('parentlanguage')) {
            if (file_exists($CFG->dirroot .'/lang/'. $parentlang .'/pix.php')) {
                $lang = $parentlang;
            } else {
                $lang = 'en';  // countries.php must exist in this pack
            }
        } else {
            $lang = 'en';  // countries.php must exist in this pack
        }
    }

    include_once($CFG->dirroot .'/lang/'. $lang .'/pix.php');

    return $string;
}

/**
 * Can include a given document file (depends on second
 * parameter) or just return info about it.
 *
 * @uses $CFG
 * @param string $file ?
 * @param boolean $include ?
 * @return ?
 * @todo Finish documenting this function
 */
function document_file($file, $include=true) { 
    global $CFG;

    $file = clean_filename($file);

    if (empty($file)) {
        return false;
    }

    $langs = array(current_language(), get_string('parentlanguage'), 'en');

    foreach ($langs as $lang) {
        $info->filepath = $CFG->dirroot .'/lang/'. $lang .'/docs/'. $file;
        $info->urlpath  = $CFG->wwwroot .'/lang/'. $lang .'/docs/'. $file;

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

/**
 * rc4encrypt
 *
 * @param string $data ?
 * @return string
 * @todo Finish documenting this function
 */
function rc4encrypt($data) {
    $password = 'nfgjeingjk';
    return endecrypt($password, $data, '');
}

/**
 * rc4decrypt
 *
 * @param string $data ?
 * @return string
 * @todo Finish documenting this function
 */
function rc4decrypt($data) {
    $password = 'nfgjeingjk';
    return endecrypt($password, $data, 'de');
}

/**
 * Based on a class by Mukul Sabharwal [mukulsabharwal @ yahoo.com]
 *
 * @param string $pwd ?
 * @param string $data ?
 * @param string $case ?
 * @return string
 * @todo Finish documenting this function
 */
function endecrypt ($pwd, $data, $case) { 

    if ($case == 'de') {
        $data = urldecode($data);
    }

    $key[] = '';
    $box[] = '';
    $temp_swap = '';
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

    $temp = '';
    $k = '';

    $cipherby = '';
    $cipher = '';

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


/**
 * Call this function to add an event to the calendar table
 *  and to call any calendar plugins
 *
 * @uses $CFG
 * @param array $event An associative array representing an event from the calendar table. The event will be identified by the id field. The object event should include the following:
 *  <ul>
 *    <li><b>$event->name</b> - Name for the event
 *    <li><b>$event->description</b> - Description of the event (defaults to '')
 *    <li><b>$event->format</b> - Format for the description (using formatting types defined at the top of weblib.php)
 *    <li><b>$event->courseid</b> - The id of the course this event belongs to (0 = all courses)
 *    <li><b>$event->groupid</b> - The id of the group this event belongs to (0 = no group)
 *    <li><b>$event->userid</b> - The id of the user this event belongs to (0 = no user)
 *    <li><b>$event->modulename</b> - Name of the module that creates this event
 *    <li><b>$event->instance</b> - Instance of the module that owns this event
 *    <li><b>$event->eventtype</b> - The type info together with the module info could
 *             be used by calendar plugins to decide how to display event
 *    <li><b>$event->timestart</b>- Timestamp for start of event
 *    <li><b>$event->timeduration</b> - Duration (defaults to zero)
 *    <li><b>$event->>visible</b> - 0 if the event should be hidden (e.g. because the activity that created it is hidden)
 *  </ul>
 * @return int The id number of the resulting record
 * @todo Finish documenting this function
 */
 function add_event($event) {

    global $CFG;

    $event->timemodified = time();

    if (!$event->id = insert_record('event', $event)) {
        return false;
    }

    if (!empty($CFG->calendar)) { // call the add_event function of the selected calendar
        if (file_exists($CFG->dirroot .'/calendar/'. $CFG->calendar .'/lib.php')) {
            include_once($CFG->dirroot .'/calendar/'. $CFG->calendar .'/lib.php');
            $calendar_add_event = $CFG->calendar.'_add_event';
            if (function_exists($calendar_add_event)) {
                $calendar_add_event($event);
            }
        }
    }

    return $event->id;
}

/**
 * Call this function to update an event in the calendar table
 * the event will be identified by the id field of the $event object.
 *
 * @uses $CFG
 * @param array $event An associative array representing an event from the calendar table. The event will be identified by the id field.
 * @return boolean
 * @todo Finish documenting this function
 */
function update_event($event) {

    global $CFG;

    $event->timemodified = time();

    if (!empty($CFG->calendar)) { // call the update_event function of the selected calendar
        if (file_exists($CFG->dirroot .'/calendar/'. $CFG->calendar .'/lib.php')) {
            include_once($CFG->dirroot .'/calendar/'. $CFG->calendar .'/lib.php');
            $calendar_update_event = $CFG->calendar.'_update_event';
            if (function_exists($calendar_update_event)) {
                $calendar_update_event($event);
            }
        }
    }
    return update_record('event', $event);
}

/**
 * Call this function to delete the event with id $id from calendar table.
 *
  * @uses $CFG
 * @param int $id The id of an event from the 'calendar' table.
 * @return array An associative array with the results from the SQL call.
 * @todo Verify return type
 */
function delete_event($id) {

    global $CFG;

    if (!empty($CFG->calendar)) { // call the delete_event function of the selected calendar
        if (file_exists($CFG->dirroot .'/calendar/'. $CFG->calendar .'/lib.php')) {
            include_once($CFG->dirroot .'/calendar/'. $CFG->calendar .'/lib.php');
            $calendar_delete_event = $CFG->calendar.'_delete_event';
            if (function_exists($calendar_delete_event)) {
                $calendar_delete_event($id);
            }
        }
    }
    return delete_records('event', 'id', $id);
}

/**
 * Call this function to hide an event in the calendar table
 * the event will be identified by the id field of the $event object.
 *
 * @uses $CFG
 * @param array $event An associative array representing an event from the calendar table. The event will be identified by the id field.
 * @return array An associative array with the results from the SQL call.
 * @todo Verify return type
 */
function hide_event($event) {
    global $CFG;

    if (!empty($CFG->calendar)) { // call the update_event function of the selected calendar
        if (file_exists($CFG->dirroot .'/calendar/'. $CFG->calendar .'/lib.php')) {
            include_once($CFG->dirroot .'/calendar/'. $CFG->calendar .'/lib.php');
            $calendar_hide_event = $CFG->calendar.'_hide_event';
            if (function_exists($calendar_hide_event)) {
                $calendar_hide_event($event);
            }
        }
    }
    return set_field('event', 'visible', 0, 'id', $event->id);
}

/**
 * Call this function to unhide an event in the calendar table
 * the event will be identified by the id field of the $event object.
 *
 * @uses $CFG
 * @param array $event An associative array representing an event from the calendar table. The event will be identified by the id field.
 * @return array An associative array with the results from the SQL call.
 * @todo Verify return type
 */
function show_event($event) {
    global $CFG;

    if (!empty($CFG->calendar)) { // call the update_event function of the selected calendar
        if (file_exists($CFG->dirroot .'/calendar/'. $CFG->calendar .'/lib.php')) {
            include_once($CFG->dirroot .'/calendar/'. $CFG->calendar .'/lib.php');
            $calendar_show_event = $CFG->calendar.'_show_event';
            if (function_exists($calendar_show_event)) {
                $calendar_show_event($event);
            }
        }
    }
    return set_field('event', 'visible', 1, 'id', $event->id);
}


/// ENVIRONMENT CHECKING  ////////////////////////////////////////////////////////////

/**
 * Lists plugin directories within some directory
 *
 * @uses $CFG
 * @param string $plugin ?
 * @param string $exclude ?
 * @return array
 * @todo Finish documenting this function
 */
function get_list_of_plugins($plugin='mod', $exclude='') {

    global $CFG;

    $basedir = opendir($CFG->dirroot .'/'. $plugin);
    while ($dir = readdir($basedir)) {
        $firstchar = substr($dir, 0, 1);
        if ($firstchar == '.' or $dir == 'CVS' or $dir == '_vti_cnf' or $dir == $exclude) {
            continue;
        }
        if (filetype($CFG->dirroot .'/'. $plugin .'/'. $dir) != 'dir') {
            continue;
        }
        $plugins[] = $dir;
    }
    if ($plugins) {
        asort($plugins);
    }
    return $plugins;
}

/**
 * Returns true if the current version of PHP is greater that the specified one.
 *
 * @param string $version The version of php being tested.
 * @return boolean
 * @todo Finish documenting this function
 */
function check_php_version($version='4.1.0') {
    $minversion = intval(str_replace('.', '', $version));
    $curversion = intval(str_replace('.', '', phpversion()));
    return ($curversion >= $minversion);
}


/**
 * Checks to see if is a browser matches the specified
 * brand and is equal or better version.
 *
 * @uses $_SERVER
 * @param string $brand The browser identifier being tested
 * @param int $version The version of the browser
 * @return boolean
 * @todo Finish documenting this function
 */
 function check_browser_version($brand='MSIE', $version=5.5) { 
    $agent = $_SERVER['HTTP_USER_AGENT'];

    if (empty($agent)) {
        return false;
    }

    switch ($brand) {

      case 'Gecko':   /// Gecko based browsers

          if (substr_count($agent, 'Camino')) {     // MacOS X Camino not supported.
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


      case 'MSIE':   /// Internet Explorer

          if (strpos($agent, 'Opera')) {     // Reject Opera
              return false;
          }
          $string = explode(';', $agent);
          if (!isset($string[1])) {
              return false;
          }
          $string = explode(' ', trim($string[1]));
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

/**
 * This function makes the return value of ini_get consistent if you are
 * setting server directives through the .htaccess file in apache.
 * Current behavior for value set from php.ini On = 1, Off = [blank]
 * Current behavior for value set from .htaccess On = On, Off = Off
 * Contributed by jdell @ unr.edu
 *
 * @param string $ini_get_arg ?
 * @return boolean
 * @todo Finish documenting this function
 */
function ini_get_bool($ini_get_arg) { 
    $temp = ini_get($ini_get_arg);

    if ($temp == '1' or strtolower($temp) == 'on') {
        return true;
    }
    return false;
}

/**
 * Compatibility stub to provide backward compatibility
 *
 * Determines if the HTML editor is enabled.
 * @deprecated Use {@link can_use_html_editor()} instead.
 */
 function can_use_richtext_editor() {
    return can_use_html_editor();
}

/**
 * Determines if the HTML editor is enabled. 
 *
 * This depends on site and user
 * settings, as well as the current browser being used.
 *
 * @return string|false Returns false if editor is not being used, otherwise
 * returns 'MSIE' or 'Gecko'.
 * @todo Finish documenting this function
 */
 function can_use_html_editor() { 
    global $USER, $CFG;

    if (!empty($USER->htmleditor) and !empty($CFG->htmleditor)) {
        if (check_browser_version('MSIE', 5.5)) {
            return 'MSIE';
        } else if (check_browser_version('Gecko', 20030516)) {
            return 'Gecko';
        }
    }
    return false;
}

/**
 * Hack to find out the GD version by parsing phpinfo output
 *
 * @return int GD version (1, 2, or 0)
 */
function check_gd_version() {
    $gdversion = 0;

    if (function_exists('gd_info')){
        $gd_info = gd_info();
        if (substr_count($gd_info['GD Version'], '2.')) {
            $gdversion = 2;
        } else if (substr_count($gd_info['GD Version'], '1.')) {
            $gdversion = 1;
        }

    } else {
        ob_start();
        phpinfo(8);
        $phpinfo = ob_get_contents();
        ob_end_clean();

        $phpinfo = explode("\n", $phpinfo);


        foreach ($phpinfo as $text) {
            $parts = explode('</td>', $text);
            foreach ($parts as $key => $val) {
                $parts[$key] = trim(strip_tags($val));
            }
            if ($parts[0] == 'GD Version') {
                if (substr_count($parts[1], '2.0')) {
                    $parts[1] = '2.0';
                }
                $gdversion = intval($parts[1]);
            }
        }
    }

    return $gdversion;   // 1, 2 or 0
}

/**
 * Determine if moodle installation requires update
 *
 * Checks version numbers of main code and all modules to see
 * if there are any mismatches
 *
 * @uses $CFG
 * @return boolean
 * @todo Finish documenting this function
 */
function moodle_needs_upgrading() { 
    global $CFG;

    include_once($CFG->dirroot .'/version.php');  # defines $version and upgrades
    if ($CFG->version) {
        if ($version > $CFG->version) {
            return true;
        }
        if ($mods = get_list_of_plugins('mod')) {
            foreach ($mods as $mod) {
                $fullmod = $CFG->dirroot .'/mod/'. $mod;
                unset($module);
                if (!is_readable($fullmod .'/version.php')) {
                    notify('Module "'. $mod .'" is not readable - check permissions');
                    continue;
                }
                include_once($fullmod .'/version.php');  # defines $module with version etc
                if ($currmodule = get_record('modules', 'name', $mod)) {
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

/**
 * Notify admin users or admin user of any failed logins (since last notification).
 *
 * @uses $CFG
 * @uses $db
 * @todo Finish documenting this function. Add long description with more detail on what it does.
 */
function notify_login_failures() {
    global $CFG, $db;

    switch ($CFG->notifyloginfailures) {
        case 'mainadmin' :
            $recip = array(get_admin());
            break;
        case 'alladmins':
            $recip = get_admins();
            break;
    }

    if (empty($CFG->lastnotifyfailure)) {
        $CFG->lastnotifyfailure=0;
    }

    // we need to deal with the threshold stuff first.
    if (empty($CFG->notifyloginthreshold)) {
        $CFG->notifyloginthreshold = 10; // default to something sensible.
    }

    $notifyipsrs = $db->Execute('SELECT ip FROM '. $CFG->prefix .'log WHERE time > '. $CFG->lastnotifyfailure .'
                          AND module=\'login\' AND action=\'error\' GROUP BY ip HAVING count(*) > '. $CFG->notifyloginthreshold);

    $notifyusersrs = $db->Execute('SELECT info FROM '. $CFG->prefix .'log WHERE time > '. $CFG->lastnotifyfailure .'
                          AND module=\'login\' AND action=\'error\' GROUP BY info HAVING count(*) > '. $CFG->notifyloginthreshold);

    if ($notifyipsrs) {
        $ipstr = '';
        while ($row = $notifyipsrs->FetchRow()) {
            $ipstr .= "'". $row['ip'] ."',";
        }
        $ipstr = substr($ipstr,0,strlen($ipstr)-1);
    }
    if ($notifyusersrs) {
        $userstr = '';
        while ($row = $notifyusersrs->FetchRow()) {
            $userstr .= "'". $row['info'] ."',";
        }
        $userstr = substr($userstr,0,strlen($userstr)-1);
    }

    if (strlen($userstr) > 0 || strlen($ipstr) > 0) {
        $count = 0;
        $logs = get_logs('time > '. $CFG->lastnotifyfailure .' AND module=\'login\' AND action=\'error\' '
                 .((strlen($ipstr) > 0 && strlen($userstr) > 0) ? ' AND ( ip IN ('. $ipstr .') OR info IN ('. $userstr .') ) '
                 : ((strlen($ipstr) != 0) ? ' AND ip IN ('. $ipstr .') ' : ' AND info IN ('. $userstr .') ')), 'l.time DESC', '', '', $count);

        // if we haven't run in the last hour and we have something useful to report and we are actually supposed to be reporting to somebody
        if (is_array($recip) and count($recip) > 0 and ((time() - (60 * 60)) > $CFG->lastnotifyfailure)
            and is_array($logs) and count($logs) > 0) {

            $message = '';
            $site = get_site();
            $subject = get_string('notifyloginfailuressubject', '', $site->fullname);
            $message .= get_string('notifyloginfailuresmessagestart', '', $CFG->wwwroot)
                 .(($CFG->lastnotifyfailure != 0) ? '('.userdate($CFG->lastnotifyfailure).')' : '')."\n\n";
            foreach ($logs as $log) {
                $log->time = userdate($log->time);
                $message .= get_string('notifyloginfailuresmessage','',$log)."\n";
            }
            $message .= "\n\n".get_string('notifyloginfailuresmessageend','',$CFG->wwwroot)."\n\n";
            foreach ($recip as $admin) {
                mtrace('Emailing '. $admin->username .' about '. count($logs) .' failed login attempts');
                email_to_user($admin,get_admin(),$subject,$message);
            }
            $conf->name = 'lastnotifyfailure';
            $conf->value = time();
            if ($current = get_record('config', 'name', 'lastnotifyfailure')) {
                $conf->id = $current->id;
                if (! update_record('config', $conf)) {
                    mtrace('Could not update last notify time');
                }

            } else if (! insert_record('config', $conf)) {
                mtrace('Could not set last notify time');
            }
        }
    }
}

/**
 * moodle_setlocale
 *
 * @uses $CFG
 * @uses $USER
 * @uses $SESSION
 * @param string $locale ?
 * @todo Finish documenting this function
 */
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

/**
 * Converts string to lowercase using most compatible function available.
 *
 * @param string $string The string to convert to all lowercase characters.
 * @param string $encoding The encoding on the string.
 * @return string
 * @todo Add examples of calling this function with/without encoding types
 */
function moodle_strtolower ($string, $encoding='') {
    if (function_exists('mb_strtolower')) {
        if($encoding===''){
           return mb_strtolower($string);          //use multibyte support with default encoding
        } else {
           return mb_strtolower($string, $encoding); //use given encoding
        }
    } else {
        return strtolower($string);                // use common function what rely on current locale setting
    }
}

/**
 * Count words in a string.
 *
 * Words are defined as things between whitespace.
 *
 * @param string $string The text to be searched for words.
 * @return int The count of words in the specified string
 */
function count_words($string) { 
    $string = strip_tags($string);
    return count(preg_split("/\w\b/", $string)) - 1;
}

/**
 * Generate and return a random string of the specified length.
 *
 * @param int $length The length of the string to be created.
 * @return string
 */
function random_string ($length=15) {
    $pool  = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $pool .= 'abcdefghijklmnopqrstuvwxyz';
    $pool .= '0123456789';
    $poollen = strlen($pool);
    mt_srand ((double) microtime() * 1000000);
    $string = '';
    for ($i = 0; $i < $length; $i++) {
        $string .= substr($pool, (mt_rand()%($poollen)), 1);
    }
    return $string;
}

/**
 * Given dates in seconds, how many weeks is the date from startdate
 * The first week is 1, the second 2 etc ...
 *
 * @param ? $startdate ?
 * @param ? $thedate ?
 * @return string
 * @todo Finish documenting this function
 */
function getweek ($startdate, $thedate) { 
    if ($thedate < $startdate) {   // error
        return 0;
    }

    return floor(($thedate - $startdate) / 604800.0) + 1;
}

/**
 * returns a randomly generated password of length $maxlen.  inspired by
 * {@link http://www.phpbuilder.com/columns/jesus19990502.php3}
 *
 * @param int $maxlength  The maximum size of the password being generated.
 * @return string
 * @todo Finish documenting this function
 */
function generate_password($maxlen=10) {
    global $CFG;

    $fillers = '1234567890!$-+';
    $wordlist = file($CFG->wordlist);

    srand((double) microtime() * 1000000);
    $word1 = trim($wordlist[rand(0, count($wordlist) - 1)]);
    $word2 = trim($wordlist[rand(0, count($wordlist) - 1)]);
    $filler1 = $fillers[rand(0, strlen($fillers) - 1)];

    return substr($word1 . $filler1 . $word2, 0, $maxlen);
}

/**
 * Given a float, prints it nicely
 *
 * @param float $num The float to print
 * @param int $places The number of decimal places to print.
 * @return string
 */
function format_float($num, $places=1) {
    return sprintf("%.$places"."f", $num);
}

/**
 * Given a simple array, this shuffles it up just like shuffle()
 * Unlike PHP's shuffle() ihis function works on any machine.
 *
 * @param array $array The array to be rearranged
 * @return array
 */
function swapshuffle($array) { 

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

/**
 * Like {@link swapshuffle()}, but works on associative arrays
 *
 * @param array $array The associative array to be rearranged
 * @return array
 */
function swapshuffle_assoc($array) {
/// 

    $newkeys = swapshuffle(array_keys($array));
    foreach ($newkeys as $newkey) {
        $newarray[$newkey] = $array[$newkey];
    }
    return $newarray;
}

/**
 * Given an arbitrary array, and a number of draws,
 * this function returns an array with that amount
 * of items.  The indexes are retained.
 *
 * @param array $array ?
 * @param ? $draws ?
 * @return ?
 * @todo Finish documenting this function
 */
function draw_rand_array($array, $draws) {
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

/**
 * microtime_diff
 *
 * @param string $a ?
 * @param string $b ?
 * @return string
 * @todo Finish documenting this function
 */
function microtime_diff($a, $b) {
    list($a_dec, $a_sec) = explode(' ', $a);
    list($b_dec, $b_sec) = explode(' ', $b);
    return $b_sec - $a_sec + $b_dec - $a_dec;
}

/**
 * Given a list (eg a,b,c,d,e) this function returns
 * an array of 1->a, 2->b, 3->c etc
 *
 * @param array $list ?
 * @param string $separator ?
 * @todo Finish documenting this function
 */
function make_menu_from_list($list, $separator=',') { 

    $array = array_reverse(explode($separator, $list), true);
    foreach ($array as $key => $item) {
        $outarray[$key+1] = trim($item);
    }
    return $outarray;
}

/**
 * Creates an array that represents all the current grades that
 * can be chosen using the given grading type.  Negative numbers
 * are scales, zero is no grade, and positive numbers are maximum
 * grades.
 *
 * @param int $gradingtype ?
 * return int
 * @todo Finish documenting this function
 */
function make_grades_menu($gradingtype) {
    $grades = array();
    if ($gradingtype < 0) {
        if ($scale = get_record('scale', 'id', - $gradingtype)) {
            return make_menu_from_list($scale->scale);
        }
    } else if ($gradingtype > 0) {
        for ($i=$gradingtype; $i>=0; $i--) {
            $grades[$i] = $i .' / '. $gradingtype;
        }
        return $grades;
    }
    return $grades;
}

/**
 * This function returns the nummber of activities
 * using scaleid in a courseid
 *
 * @param int $courseid ?
 * @param int $scaleid ?
 * @return int
 * @todo Finish documenting this function
 */
function course_scale_used($courseid, $scaleid) {

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

/**
 * This function returns the nummber of activities
 * using scaleid in the entire site
 *
 * @param int $scaleid ?
 * @return int
 * @todo Finish documenting this function. Is return type correct?
 */
function site_scale_used($scaleid) {

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

/**
 * make_unique_id_code
 *
 * @param string $extra ?
 * @return string
 * @todo Finish documenting this function
 */
function make_unique_id_code($extra='') {

    $hostname = 'unknownhost';
    if (!empty($_SERVER['HTTP_HOST'])) {
        $hostname = $_SERVER['HTTP_HOST'];
    } else if (!empty($_ENV['HTTP_HOST'])) {
        $hostname = $_ENV['HTTP_HOST'];
    } else if (!empty($_SERVER['SERVER_NAME'])) {
        $hostname = $_SERVER['SERVER_NAME'];
    } else if (!empty($_ENV['SERVER_NAME'])) {
        $hostname = $_ENV['SERVER_NAME'];
    }

    $date = gmdate("ymdHis");

    $random =  random_string(6);

    if ($extra) {
        return $hostname .'+'. $date .'+'. $random .'+'. $extra;
    } else {
        return $hostname .'+'. $date .'+'. $random;
    }
}


/**
 * Function to check the passed address is within the passed subnet
 *
 * The parameter is a comma separated string of subnet definitions.
 * Subnet strings can be in one of two formats:
 *   1: xxx.xxx.xxx.xxx/xx
 *   2: xxx.xxx
 * Code for type 1 modified from user posted comments by mediator at
 * {@link http://au.php.net/manual/en/function.ip2long.php}
 *
 * @param string $addr    The address you are checking
 * @param string $subnetstr    The string of subnet addresses
 * @return boolean
 */
function address_in_subnet($addr, $subnetstr) {

    $subnets = explode(',', $subnetstr);
    $found = false;
    $addr = trim($addr);

    foreach ($subnets as $subnet) {
        $subnet = trim($subnet);
        if (strpos($subnet, '/') !== false) { /// type 1

            list($ip, $mask) = explode('/', $subnet);
            $mask = 0xffffffff << (32 - $mask);
            $found = ((ip2long($addr) & $mask) == (ip2long($ip) & $mask));

        } else { /// type 2
            $found = (strpos($addr, $subnet) === 0);
        }

        if ($found) {
            continue;
        }
    }

    return $found;
}

/**
 * For outputting debugging info
 *
 * @uses STDOUT
 * @param string $string ?
 * @param string $eol ?
 * @todo Finish documenting this function
 */
function mtrace($string, $eol="\n") {

    if (defined('STDOUT')) {
        fwrite(STDOUT, $string.$eol);
    } else {
        echo $string . $eol;
    }

    flush();
}


/**
 * Returns most reliable client address
 *
 * @return string The remote IP address
 */
 function getremoteaddr() {
    if (getenv('HTTP_CLIENT_IP')) $ip = getenv('HTTP_CLIENT_IP');
    else if(getenv('HTTP_X_FORWARDED_FOR')) $ip = getenv('HTTP_X_FORWARDED_FOR');
    else if(getenv('REMOTE_ADDR')) $ip = getenv('REMOTE_ADDR');
    else $ip = false; //just in case
    return $ip;
}

/**
 * html_entity_decode is only supported by php 4.3.0 and higher
 * so if it is not predefined, define it here
 *
 * @param string $string ?
 * @return string
 * @todo Finish documenting this function
 */
if(!function_exists('html_entity_decode')) {
     function html_entity_decode($string) {
        $trans_tbl = get_html_translation_table(HTML_ENTITIES);
        $trans_tbl = array_flip($trans_tbl);
        return strtr($string, $trans_tbl);
    }
}

// vim:autoindent:expandtab:shiftwidth=4:tabstop=4:tw=140:
?>
