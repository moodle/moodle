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
 * @package    core
 * @subpackage session
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @copyright  2008, 2009 Petr Skoda  {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


/**
 * Makes sure that $USER->sesskey exists, if $USER itself exists. It sets a new sesskey
 * if one does not already exist, but does not overwrite existing sesskeys. Returns the
 * sesskey string if $USER exists, or boolean false if not.
 *
 * @uses $USER
 * @return string
 */
function sesskey() {
    // note: do not use $USER because it may not be initialised yet
    if (empty($_SESSION['USER']->sesskey)) {
        if (!isset($_SESSION['USER'])) {
            $_SESSION['USER'] = new stdClass;
        }
        $_SESSION['USER']->sesskey = random_string(10);
    }

    return $_SESSION['USER']->sesskey;
}


/**
 * Check the sesskey and return true of false for whether it is valid.
 * (You might like to imagine this function is called sesskey_is_valid().)
 *
 * Every script that lets the user perform a significant action (that is,
 * changes data in the database) should check the sesskey before doing the action.
 * Depending on your code flow, you may want to use the {@link require_sesskey()}
 * helper function.
 *
 * @param string $sesskey The sesskey value to check (optional). Normally leave this blank
 *      and this function will do required_param('sesskey', ...).
 * @return bool whether the sesskey sent in the request matches the one stored in the session.
 */
function confirm_sesskey($sesskey=NULL) {
    global $USER;

    if (!empty($USER->ignoresesskey)) {
        return true;
    }

    if (empty($sesskey)) {
        $sesskey = required_param('sesskey', PARAM_RAW);  // Check script parameters
    }

    return (sesskey() === $sesskey);
}

/**
 * Check the session key using {@link confirm_sesskey()},
 * and cause a fatal error if it does not match.
 */
function require_sesskey() {
    if (!confirm_sesskey()) {
        print_error('invalidsesskey');
    }
}

/**
 * Sets a moodle cookie with a weakly encrypted username
 *
 * @param string $username to encrypt and place in a cookie, '' means delete current cookie
 * @return void
 */
function set_moodle_cookie($username) {
    global $CFG;

    if (NO_MOODLE_COOKIES) {
        return;
    }

    if (empty($CFG->rememberusername)) {
        // erase current and do not store permanent cookies
        $username = '';
    }

    if ($username === 'guest') {
        // keep previous cookie in case of guest account login
        return;
    }

    $cookiename = 'MOODLEID1_'.$CFG->sessioncookie;

    // delete old cookie
    setcookie($cookiename, '', time() - HOURSECS, $CFG->sessioncookiepath, $CFG->sessioncookiedomain, $CFG->cookiesecure, $CFG->cookiehttponly);

    if ($username !== '') {
        // set username cookie for 60 days
        setcookie($cookiename, rc4encrypt($username), time()+(DAYSECS*60), $CFG->sessioncookiepath, $CFG->sessioncookiedomain, $CFG->cookiesecure, $CFG->cookiehttponly);
    }
}

/**
 * Gets a moodle cookie with a weakly encrypted username
 *
 * @return string username
 */
function get_moodle_cookie() {
    global $CFG;

    if (NO_MOODLE_COOKIES) {
        return '';
    }

    if (empty($CFG->rememberusername)) {
        return '';
    }

    $cookiename = 'MOODLEID1_'.$CFG->sessioncookie;

    if (empty($_COOKIE[$cookiename])) {
        return '';
    } else {
        $username = rc4decrypt($_COOKIE[$cookiename]);
        if ($username === 'guest' or $username === 'nobody') {
            // backwards compatibility - we do not set these cookies any more
            $username = '';
        }
        return $username;
    }
}

/**
 * Sets up current user and course environment (lang, etc.) in cron.
 * Do not use outside of cron script!
 *
 * @param stdClass $user full user object, null means default cron user (admin)
 * @param $course full course record, null means $SITE
 * @return void
 */
function cron_setup_user($user = NULL, $course = NULL) {
    global $CFG, $SITE, $PAGE;

    static $cronuser    = NULL;
    static $cronsession = NULL;

    if (empty($cronuser)) {
        /// ignore admins timezone, language and locale - use site default instead!
        $cronuser = get_admin();
        $cronuser->timezone = $CFG->timezone;
        $cronuser->lang     = '';
        $cronuser->theme    = '';
        unset($cronuser->description);

        $cronsession = new stdClass();
    }

    if (!$user) {
        // cached default cron user (==modified admin for now)
        \core\session\manager::set_user($cronuser);
        $_SESSION['SESSION'] = $cronsession;

    } else {
        // emulate real user session - needed for caps in cron
        if ($_SESSION['USER']->id != $user->id) {
            \core\session\manager::set_user($user);
            $_SESSION['SESSION'] = new stdClass();
        }
    }

    // TODO MDL-19774 relying on global $PAGE in cron is a bad idea.
    // Temporary hack so that cron does not give fatal errors.
    $PAGE = new moodle_page();
    if ($course) {
        $PAGE->set_course($course);
    } else {
        $PAGE->set_course($SITE);
    }

    // TODO: it should be possible to improve perf by caching some limited number of users here ;-)

}
