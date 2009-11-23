<?php

/**
 * @author Petr Skoda
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package moodle multiauth
 *
 * Authentication Plugin: No Authentication
 *
 * No authentication at all. This method approves everything!
 *
 * 2007-02-18  File created.
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->libdir.'/authlib.php');

/**
 * Plugin for no authentication - disabled user.
 */
class auth_plugin_nologin extends auth_plugin_base {


    /**
     * Constructor.
     */
    function auth_plugin_nologin() {
        $this->authtype = 'nologin';
    }

    /**
     * Do not allow any login.
     *
     */
    function user_login($username, $password) {
        return false;
    }

    /**
     * No password updates.
     */
    function user_update_password($user, $newpassword) {
        return false;
    }

    function prevent_local_passwords() {
        // just in case, we do not want to loose the passwords
        return false;
    }

    /**
     * No external data sync.
     *
     * @return bool
     */
    function is_internal() {
        //we do not know if it was internal or external originally
        return true;
    }

    /**
     * No changing of password.
     *
     * @return bool
     */
    function can_change_password() {
        return false;
    }

    /**
     * No password resetting.
     */
    function can_reset_password() {
        return false;
    }

}

?>
