<?php  // $Id$
/**
 * @author Martin Dougiamas
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package moodle multiauth
 *
 * Multiple plugin authentication
 * Support library
 *
 * 2006-08-28  File created, AUTH return values defined.
 */

/**
 * Returned when the login was successful.
 */
define('AUTH_OK',     0);

/**
 * Returned when the login was unsuccessful.
 */
define('AUTH_FAIL',   1);

/**
 * Returned when the login was denied (a reason for AUTH_FAIL).
 */
define('AUTH_DENIED', 2);

/**
 * Returned when some error occurred (a reason for AUTH_FAIL).
 */
define('AUTH_ERROR',  4);

/**
 * Authentication - error codes for user confirm
 */
define('AUTH_CONFIRM_FAIL', 0);
define('AUTH_CONFIRM_OK', 1);
define('AUTH_CONFIRM_ALREADY', 2);
define('AUTH_CONFIRM_ERROR', 3);

# MDL-14055
define('AUTH_REMOVEUSER_KEEP', 0);
define('AUTH_REMOVEUSER_SUSPEND', 1);
define('AUTH_REMOVEUSER_FULLDELETE', 2);

/**
 * Abstract authentication plugin.
 */
class auth_plugin_base {

    /**
     * The configuration details for the plugin.
     */
    var $config;

    /**
     * Authentication plugin type - the same as db field.
     */
    var $authtype;
    /*
     * The fields we can lock and update from/to external authentication backends
     */
    var $userfields = array(
        'firstname',
        'lastname',
        'email',
        'city',
        'country',
        'lang',
        'description',
        'url',
        'idnumber',
        'institution',
        'department',
        'phone1',
        'phone2',
        'address'
    );

    /**

     * This is the primary method that is used by the authenticate_user_login()
     * function in moodlelib.php. This method should return a boolean indicating
     * whether or not the username and password authenticate successfully.
     *
     * Returns true if the username and password work and false if they are
     * wrong or don't exist.
     *
     * @param string $username The username (with system magic quotes)
     * @param string $password The password (with system magic quotes)
     *
     * @return bool Authentication success or failure.
     */
    function user_login($username, $password) {
        error('Abstract user_login() method must be overriden.');
    }

    /**
     * Returns true if this authentication plugin can change the users'
     * password.
     *
     * @return bool
     */
    function can_change_password() {
        //override if needed
        return false;
    }

    /**
     * Returns the URL for changing the users' passwords, or empty if the default
     * URL can be used. This method is used if can_change_password() returns true.
     * This method is called only when user is logged in, it may use global $USER.
     *
     * @return string
     */
    function change_password_url() {
        //override if needed
        return '';
    }

    /**
     * Returns true if this authentication plugin is "internal" (which means that
     * Moodle stores the users' passwords and other details in the local Moodle
     * database).
     *
     * @return bool
     */
    function is_internal() {
        //override if needed
        return true;
    }

    /**
     * Indicates if password hashes should be stored in local moodle database.
     * @return bool true means md5 password hash stored in user table, false means flag 'not_cached' stored there instead
     */
    function prevent_local_passwords() {
        // NOTE: this will be changed to true in 2.0
        return false;
    }

    /**
     * Updates the user's password. In previous versions of Moodle, the function
     * auth_user_update_password accepted a username as the first parameter. The
     * revised function expects a user object.
     *
     * @param  object  $user        User table object  (with system magic quotes)
     * @param  string  $newpassword Plaintext password (with system magic quotes)
     *
     * @return bool                  True on success
     */
    function user_update_password($user, $newpassword) {
        //override if needed
        return true;
    }

    /**
     * Called when the user record is updated.
     * Modifies user in external database. It takes olduser (before changes) and newuser (after changes)
     * conpares information saved modified information to external db.
     *
     * @param mixed $olduser     Userobject before modifications    (without system magic quotes)
     * @param mixed $newuser     Userobject new modified userobject (without system magic quotes)
     * @return boolean true if updated or update ignored; false if error
     *
     */
    function user_update($olduser, $newuser) {
        //override if needed
        return true;
    }

    /**
     * User delete requested - internal user record is mared as deleted already, username not present anymore.
     * Do any action in external database.
     * @param object $user       Userobject before delete    (without system magic quotes)
     */
    function user_delete($olduser) {
        //override if needed
        return;
    }

    /**
     * Returns true if plugin allows resetting of internal password.
     *
     * @return bool
     */
    function can_reset_password() {
        //override if needed
        return false;
    }

    /**
     * Returns true if plugin allows resetting of internal password.
     *
     * @return bool
     */
    function can_signup() {
        //override if needed
        return false;
    }

    /**
     * Sign up a new user ready for confirmation.
     * Password is passed in plaintext.
     *
     * @param object $user new user object (with system magic quotes)
     * @param boolean $notify print notice with link and terminate
     */
    function user_signup($user, $notify=true) {
        //override when can signup
        error('user_signup method must be overriden if signup enabled');
    }

    /**
     * Returns true if plugin allows confirming of new users.
     *
     * @return bool
     */
    function can_confirm() {
        //override if needed
        return false;
    }

    /**
     * Confirm the new user as registered.
     *
     * @param string $username (with system magic quotes)
     * @param string $confirmsecret (with system magic quotes)
     */
    function user_confirm($username, $confirmsecret) {
        //override when can confirm
        error('user_confirm method must be overriden if confirm enabled');
    }

    /**
     * Checks if user exists in external db
     *
     * @param string $username (with system magic quotes)
     * @return bool
     */
    function user_exists() {
        //override if needed
        return false;
    }

    /**
     * return number of days to user password expires
     *
     * If userpassword does not expire it should return 0. If password is already expired
     * it should return negative value.
     *
     * @param mixed $username username (with system magic quotes)
     * @return integer
     */
    function password_expire($username) {
        return 0;
    }
    /**
     * Sync roles for this user - usually creator
     *
     * @param $user object user object (without system magic quotes)
     */
    function sync_roles($user) {
        //override if needed
    }

    /**
     * Read user information from external database and returns it as array().
     * Function should return all information available. If you are saving
     * this information to moodle user-table you should honor syncronization flags
     *
     * @param string $username username (with system magic quotes)
     *
     * @return mixed array with no magic quotes or false on error
     */
    function get_userinfo($username) {
        //override if needed
        return array();
    }

    /**
     * Prints a form for configuring this authentication plugin.
     *
     * This function is called from admin/auth.php, and outputs a full page with
     * a form for configuring this plugin.
     */
    function config_form($config, $err, $user_fields) {
        //override if needed
    }

    /**
     * A chance to validate form data, and last chance to
     * do stuff before it is inserted in config_plugin
     * @param object object with submitted configuration settings (without system magic quotes)
     * @param array $err array of error messages
     */
     function validate_form(&$form, &$err) {
        //override if needed
    }

    /**
     * Processes and stores configuration data for this authentication plugin.
     *
     * @param object object with submitted configuration settings (without system magic quotes)
     */
    function process_config($config) {
        //override if needed
        return true;
    }

    /**
     * Hook for overriding behavior of login page.
     * This method is called from login/index.php page for all enabled auth plugins.
     */
    function loginpage_hook() {
        global $frm;  // can be used to override submitted login form
        global $user; // can be used to replace authenticate_user_login()

        //override if needed
    }

    /**
     * Post authentication hook.
     * This method is called from authenticate_user_login() for all enabled auth plugins.
     *
     * @param object $user user object, later used for $USER
     * @param string $username (with system magic quotes)
     * @param string $password plain text password (with system magic quotes)
     */
    function user_authenticated_hook(&$user, $username, $password) {
        //override if needed
    }

    /**
     * Pre logout hook.
     * This method is called from require_logout() for all enabled auth plugins,
     */
    function prelogout_hook() {
        global $USER; // use $USER->auth to find the plugin used for login

        //override if needed
    }

    /**
     * Hook for overriding behavior of logout page.
     * This method is called from login/logout.php page for all enabled auth plugins.
     */
    function logoutpage_hook() {
        global $USER;     // use $USER->auth to find the plugin used for login
        global $redirect; // can be used to override redirect after logout

        //override if needed
    }

    /**
     * Return the properly translated human-friendly title of this auth plugin
     */
    function get_title() {
        return auth_get_plugin_title($this->authtype);
    }

    /**
     *  Get the auth description (from core or own auth lang files)
     */
    function get_description() {
        $authdescription = get_string("auth_{$this->authtype}description", "auth");
        if ($authdescription == "[[auth_{$this->authtype}description]]") {
            $authdescription = get_string("auth_{$this->authtype}description", "auth_{$this->authtype}");
        }
        return $authdescription;
    }
    
    /**
     * Returns whether or not the captcha element is enabled, and the admin settings fulfil its requirements.
     * @abstract Implement in child classes
     * @return bool
     */
    function is_captcha_enabled() {
        return false;
    }


}

?>
