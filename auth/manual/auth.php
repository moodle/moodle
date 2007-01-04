<?php
/**
 * @author Martin Dougiamas
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package moodle multiauth
 *
 * Authentication Plugin: Manual Authentication
 *
 * Just does a simple check against the moodle database.
 *
 * 2006-08-28  File created.
 */

// This page cannot be called directly
if (!isset($CFG)) exit;

/**
 * Manual authentication plugin.
 */
class auth_plugin_manual
{
    /**
     * The configuration details for the plugin.
     */
    var $config;

    var $canchangepassword = true;
    var $isinternal = true;

    /**
     * Constructor.
     */
    function auth_plugin_manual() {
        $this->config = get_config('auth/manual');
    }

    /**
     * Returns true if the username and password work and false if they are
     * wrong or don't exist.
     *
     * @param string $username The username
     * @param string $password The password
     * @returns bool Authentication success or failure.
     */
    function user_login ($username, $password) {
        if ($user = get_record('user', 'username', $username)) {
            if (validate_internal_user_password($user, $password)) {
                return true;
                // return AUTH_OK;
            }
        }
        return false;
        // return AUTH_FAIL;
    }

    /*
     * Updates the user's password.
     *
     * called when the user password is updated.
     *
     * @param mixed $username    Username
     * @param mixed $newpassword Plaintext password
     * @return boolean result
     *
     */
    function user_update_password($username, $newpassword) {
        $user = get_complete_user_data('username', $username);
        return update_internal_user_password($user, $newpassword);
    }

    /**
     * Returns true if this authentication plugin is 'internal'.
     *
     * @returns bool
     */
    function is_internal() {
        return true;
    }
    
    /**
     * Returns true if this authentication plugin can change the user's
     * password.
     *
     * @returns bool
     */
    function can_change_password() {
        return true;
    }
    
    /**
     * Returns the URL for changing the user's pw, or false if the default can
     * be used.
     *
     * @returns bool
     */
    function change_password_url() {
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
    function config_form($config, $err) {
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
