<?php

/**
 * @author Martin Dougiamas
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package moodle multiauth
 *
 * Authentication Plugin: PAM Authentication
 *
 * PAM (Pluggable Authentication Modules) for Moodle
 *
 * Description:
 * Authentication by using the PHP4 PAM module:
 * http://www.math.ohio-state.edu/~ccunning/pam_auth/
 *
 * Version 0.3  2006/09/07 by Jonathan Harker (plugin class)
 * Version 0.2: 2004/09/01 by Martin V�geli (stable version)
 * Version 0.1: 2004/08/30 by Martin V�geli (first draft)
 *
 * Contact: martinvoegeli@gmx.ch
 * Website 1: http://elearning.zhwin.ch/
 * Website 2: http://birdy1976.com/
 *
 * License:  GPL License v2
 *
 * 2006-08-31  File created.
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->libdir.'/authlib.php');

/**
 * PAM authentication plugin.
 */
class auth_plugin_pam extends auth_plugin_base {

    /**
     * Store error messages from pam authentication attempts.
     */
    var $lasterror;

    /**
     * Constructor.
     */
    function auth_plugin_pam() {
        $this->authtype = 'pam';
        $this->config = get_config('auth/pam');
        $this->errormessage = '';
    }

    /**
     * Returns true if the username and password work and false if they are
     * wrong or don't exist.
     *
     * @param string $username The username (with system magic quotes)
     * @param string $password The password (with system magic quotes)
     * @return bool Authentication success or failure.
     */
    function user_login ($username, $password) {
        // variable to store possible errors during authentication
        $errormessage = str_repeat(' ', 2048);

        // just for testing and debugging
        // error_reporting(E_ALL);

        // call_time_pass_reference of errormessage is deprecated - throws warnings in multiauth
        //if (pam_auth($username, $password, &$errormessage)) {
        if (pam_auth(stripslashes($username), stripslashes($password))) {
            return true;
        }
        else {
            $this->lasterror = $errormessage;
            return false;
        }
    }

    function prevent_local_passwords() {
        return true;
    }

    /**
     * Returns true if this authentication plugin is 'internal'.
     *
     * @return bool
     */
    function is_internal() {
        return false;
    }

    /**
     * Returns true if this authentication plugin can change the user's
     * password.
     *
     * @return bool
     */
    function can_change_password() {
        return false;
    }

    /**
     * Prints a form for configuring this authentication plugin.
     *
     * This function is called from admin/auth.php, and outputs a full page with
     * a form for configuring this plugin.
     *
     * @param array $page An object containing all the data for this page.
     */
    function config_form($config, $err, $user_fields) {
        include "config.html";
    }

    /**
     * Processes and stores configuration data for this authentication plugin.
     */
    function process_config($config) {
        return true;
    }

}

?>
