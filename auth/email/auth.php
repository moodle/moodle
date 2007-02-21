<?php

/**
 * @author Martin Dougiamas
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package moodle multiauth
 *
 * Authentication Plugin: Email Authentication
 *
 * Standard authentication function.
 *
 * 2006-08-28  File created.
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

/**
 * Error codes for user confirm
 */
define('AUTH_CONFIRM_FAIL', 0);
define('AUTH_CONFIRM_OK', 1);
define('AUTH_CONFIRM_ALREADY', 2);
define('AUTH_CONFIRM_ERROR', 3);
// TODO: instead of integers these could be the language keys?


/**
 * Email authentication plugin.
 */
class auth_plugin_email {

    /**
     * The configuration details for the plugin.
     */
    var $config;

    /**
     * Constructor.
     */
    function auth_plugin_email() {
        $this->config = get_config('auth/email');
    }

    /**
     * Returns true if the username and password work and false if they are
     * wrong or don't exist.
     *
     * @param string $username The username
     * @param string $password The password
     * @return bool Authentication success or failure.
     */
    function user_login ($username, $password) {
        global $CFG;
        if ($user = get_record('user', 'username', $username, 'mnethostid', $CFG->mnet_localhost_id)) {
            return validate_internal_user_password($user, $password);
        }
        return false;
    }

    /**
     * Updates the user's password.
     *
     * called when the user password is updated.
     *
     * @param  object  $user        User
     * @param  string  $newpassword Plaintext password
     * @return boolean result
     *
     */
    function user_update_password($user, $newpassword) {
        $user = get_complete_user_data('id', $user->id);
        return update_internal_user_password($user, $newpassword);
    }

    /**
     * Sign up a new user ready for confirmation.
     */
    function user_signup($user, $notify = true) {
        if (! ($user->id = insert_record('user', $user)) ) {
            print_error('auth_emailnoinsert','auth');
        }
        if (! send_confirmation_email($user)) {
            print_error('auth_emailnoemail','auth');
        }

        if ($notify) {
            global $CFG;
            $emailconfirm = get_string('emailconfirm');
            print_header($emailconfirm, $emailconfirm, $emailconfirm);
            notice(get_string('emailconfirmsent', '', $user->email), "$CFG->wwwroot/index.php");
        }
    }

    /**
     * Confirm the new user as registered.
     */
    function user_confirm($username, $confirmsecret) {
        $user = get_complete_user_data('username', $username);

        if (!empty($user)) {
            if ($user->confirmed) {
                return AUTH_CONFIRM_ALREADY;
            }
            if ($user->secret == $confirmsecret) {   // They have provided the secret key to get in
                if (!set_field("user", "confirmed", 1, "id", $user->id)) {
                    return AUTH_CONFIRM_FAIL;
                }
                if (!set_field("user", "firstaccess", time(), "id", $user->id)) {
                    return AUTH_CONFIRM_FAIL;
                }
                return AUTH_CONFIRM_OK;
            }
        }
    }

    /**
     * Returns true if this authentication plugin is 'internal'.
     *
     * @return bool
     */
    function is_internal() {
        return true;
    }

    /**
     * Returns true if this authentication plugin can change the user's
     * password.
     *
     * @return bool
     */
    function can_change_password() {
        return true;
    }

    /**
     * Returns the URL for changing the user's pw, or empty if the default can
     * be used.
     *
     * @return mixed
     */
    function change_password_url() {
        return ''; // use dafult internal method
    }

    /**
     * Returns true if plugin allows resetting of internal password.
     *
     * @return bool
     */
    function can_reset_password() {
        return true;
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
