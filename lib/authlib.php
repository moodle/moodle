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
 * Multiple plugin authentication Support library
 *
 * 2006-08-28  File created, AUTH return values defined.
 *
 * @package    core
 * @subpackage auth
 * @copyright  1999 onwards Martin Dougiamas  http://dougiamas.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

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

/** Login attempt successful. */
define('AUTH_LOGIN_OK', 0);

/** Can not login because user does not exist. */
define('AUTH_LOGIN_NOUSER', 1);

/** Can not login because user is suspended. */
define('AUTH_LOGIN_SUSPENDED', 2);

/** Can not login, most probably password did not match. */
define('AUTH_LOGIN_FAILED', 3);

/** Can not login because user is locked out. */
define('AUTH_LOGIN_LOCKOUT', 4);

/** Can not login becauser user is not authorised. */
define('AUTH_LOGIN_UNAUTHORISED', 5);

/**
 * Abstract authentication plugin.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package moodlecore
 */
class auth_plugin_base {

    /**
     * The configuration details for the plugin.
     * @var object
     */
    var $config;

    /**
     * Authentication plugin type - the same as db field.
     * @var string
     */
    var $authtype;
    /*
     * The fields we can lock and update from/to external authentication backends
     * @var array
     */
    var $userfields = \core_user::AUTHSYNCFIELDS;

    /**
     * Moodle custom fields to sync with.
     * @var array()
     */
    var $customfields = null;

    /**
     * The tag we want to prepend to any error log messages.
     *
     * @var string
     */
    protected $errorlogtag = '';

    /**
     * This is the primary method that is used by the authenticate_user_login()
     * function in moodlelib.php.
     *
     * This method should return a boolean indicating
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
        print_error('mustbeoveride', 'debug', '', 'user_login()' );
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
     * URL can be used.
     *
     * This method is used if can_change_password() returns true.
     * This method is called only when user is logged in, it may use global $USER.
     * If you are using a plugin config variable in this method, please make sure it is set before using it,
     * as this method can be called even if the plugin is disabled, in which case the config values won't be set.
     *
     * @return moodle_url url of the profile page or null if standard used
     */
    function change_password_url() {
        //override if needed
        return null;
    }

    /**
     * Returns true if this authentication plugin can edit the users'
     * profile.
     *
     * @return bool
     */
    function can_edit_profile() {
        //override if needed
        return true;
    }

    /**
     * Returns the URL for editing the users' profile, or empty if the default
     * URL can be used.
     *
     * This method is used if can_edit_profile() returns true.
     * This method is called only when user is logged in, it may use global $USER.
     *
     * @return moodle_url url of the profile page or null if standard used
     */
    function edit_profile_url() {
        //override if needed
        return null;
    }

    /**
     * Returns true if this authentication plugin is "internal".
     *
     * Internal plugins use password hashes from Moodle user table for authentication.
     *
     * @return bool
     */
    function is_internal() {
        //override if needed
        return true;
    }

    /**
     * Returns false if this plugin is enabled but not configured.
     *
     * @return bool
     */
    public function is_configured() {
        return false;
    }

    /**
     * Indicates if password hashes should be stored in local moodle database.
     * @return bool true means md5 password hash stored in user table, false means flag 'not_cached' stored there instead
     */
    function prevent_local_passwords() {
        return !$this->is_internal();
    }

    /**
     * Indicates if moodle should automatically update internal user
     * records with data from external sources using the information
     * from get_userinfo() method.
     *
     * @return bool true means automatically copy data from ext to user table
     */
    function is_synchronised_with_external() {
        return !$this->is_internal();
    }

    /**
     * Updates the user's password.
     *
     * In previous versions of Moodle, the function
     * auth_user_update_password accepted a username as the first parameter. The
     * revised function expects a user object.
     *
     * @param  object  $user        User table object
     * @param  string  $newpassword Plaintext password
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
     * compares information saved modified information to external db.
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
     *
     * Do any action in external database.
     *
     * @param object $user       Userobject before delete    (without system magic quotes)
     * @return void
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
     * @param object $user new user object
     * @param boolean $notify print notice with link and terminate
     */
    function user_signup($user, $notify=true) {
        //override when can signup
        print_error('mustbeoveride', 'debug', '', 'user_signup()' );
    }

    /**
     * Return a form to capture user details for account creation.
     * This is used in /login/signup.php.
     * @return moodle_form A form which edits a record from the user table.
     */
    function signup_form() {
        global $CFG;

        require_once($CFG->dirroot.'/login/signup_form.php');
        return new login_signup_form(null, null, 'post', '', array('autocomplete'=>'on'));
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
     * @param string $username
     * @param string $confirmsecret
     */
    function user_confirm($username, $confirmsecret) {
        //override when can confirm
        print_error('mustbeoveride', 'debug', '', 'user_confirm()' );
    }

    /**
     * Checks if user exists in external db
     *
     * @param string $username (with system magic quotes)
     * @return bool
     */
    function user_exists($username) {
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
     * this information to moodle user-table you should honour synchronisation flags
     *
     * @param string $username username
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
     *
     * @param object $config
     * @param object $err
     * @param array $user_fields
     * @deprecated since Moodle 3.3
     */
    function config_form($config, $err, $user_fields) {
        debugging('Use of config.html files have been deprecated, please update your code to use the admin settings API.');
        //override if needed
    }

    /**
     * A chance to validate form data, and last chance to
     * do stuff before it is inserted in config_plugin
     * @param object object with submitted configuration settings (without system magic quotes)
     * @param array $err array of error messages
     * @deprecated since Moodle 3.3
     */
     function validate_form($form, &$err) {
        debugging('Use of config.html files have been deprecated, please update your code to use the admin settings API.');
        //override if needed
    }

    /**
     * Processes and stores configuration data for this authentication plugin.
     *
     * @param object object with submitted configuration settings (without system magic quotes)
     * @deprecated since Moodle 3.3
     */
    function process_config($config) {
        debugging('Use of config.html files have been deprecated, please update your code to use the admin settings API.');
        //override if needed
        return true;
    }

    /**
     * Hook for overriding behaviour of login page.
     * This method is called from login/index.php page for all enabled auth plugins.
     *
     * @global object
     * @global object
     */
    function loginpage_hook() {
        global $frm;  // can be used to override submitted login form
        global $user; // can be used to replace authenticate_user_login()

        //override if needed
    }

    /**
     * Hook for overriding behaviour before going to the login page.
     *
     * This method is called from require_login from potentially any page for
     * all enabled auth plugins and gives each plugin a chance to redirect
     * directly to an external login page, or to instantly login a user where
     * possible.
     *
     * If an auth plugin implements this hook, it must not rely on ONLY this
     * hook in order to work, as there are many ways a user can browse directly
     * to the standard login page. As a general rule in this case you should
     * also implement the loginpage_hook as well.
     *
     */
    function pre_loginpage_hook() {
        // override if needed, eg by redirecting to an external login page
        // or logging in a user:
        // complete_user_login($user);
    }

    /**
     * Pre user_login hook.
     * This method is called from authenticate_user_login() right after the user
     * object is generated. This gives the auth plugins an option to make adjustments
     * before the verification process starts.
     *
     * @param object $user user object, later used for $USER
     */
    public function pre_user_login_hook(&$user) {
        // Override if needed.
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
     *
     * @global object
     */
    function prelogout_hook() {
        global $USER; // use $USER->auth to find the plugin used for login

        //override if needed
    }

    /**
     * Hook for overriding behaviour of logout page.
     * This method is called from login/logout.php page for all enabled auth plugins.
     *
     * @global object
     * @global string
     */
    function logoutpage_hook() {
        global $USER;     // use $USER->auth to find the plugin used for login
        global $redirect; // can be used to override redirect after logout

        //override if needed
    }

    /**
     * Hook called before timing out of database session.
     * This is useful for SSO and MNET.
     *
     * @param object $user
     * @param string $sid session id
     * @param int $timecreated start of session
     * @param int $timemodified user last seen
     * @return bool true means do not timeout session yet
     */
    function ignore_timeout_hook($user, $sid, $timecreated, $timemodified) {
        return false;
    }

    /**
     * Return the properly translated human-friendly title of this auth plugin
     *
     * @todo Document this function
     */
    function get_title() {
        return get_string('pluginname', "auth_{$this->authtype}");
    }

    /**
     * Get the auth description (from core or own auth lang files)
     *
     * @return string The description
     */
    function get_description() {
        $authdescription = get_string("auth_{$this->authtype}description", "auth_{$this->authtype}");
        return $authdescription;
    }

    /**
     * Returns whether or not the captcha element is enabled.
     *
     * @abstract Implement in child classes
     * @return bool
     */
    function is_captcha_enabled() {
        return false;
    }

    /**
     * Returns whether or not this authentication plugin can be manually set
     * for users, for example, when bulk uploading users.
     *
     * This should be overriden by authentication plugins where setting the
     * authentication method manually is allowed.
     *
     * @return bool
     * @since Moodle 2.6
     */
    function can_be_manually_set() {
        // Override if needed.
        return false;
    }

    /**
     * Returns a list of potential IdPs that this authentication plugin supports.
     *
     * This is used to provide links on the login page and the login block.
     *
     * The parameter $wantsurl is typically used by the plugin to implement a
     * return-url feature.
     *
     * The returned value is expected to be a list of associative arrays with
     * string keys:
     *
     * - url => (moodle_url|string) URL of the page to send the user to for authentication
     * - name => (string) Human readable name of the IdP
     * - iconurl => (moodle_url|string) URL of the icon representing the IdP (since Moodle 3.3)
     *
     * For legacy reasons, pre-3.3 plugins can provide the icon via the key:
     *
     * - icon => (pix_icon) Icon representing the IdP
     *
     * @param string $wantsurl The relative url fragment the user wants to get to.
     * @return array List of associative arrays with keys url, name, iconurl|icon
     */
    function loginpage_idp_list($wantsurl) {
        return array();
    }

    /**
     * Return custom user profile fields.
     *
     * @return array list of custom fields.
     */
    public function get_custom_user_profile_fields() {
        global $DB;
        // If already retrieved then return.
        if (!is_null($this->customfields)) {
            return $this->customfields;
        }

        $this->customfields = array();
        if ($proffields = $DB->get_records('user_info_field')) {
            foreach ($proffields as $proffield) {
                $this->customfields[] = 'profile_field_'.$proffield->shortname;
            }
        }
        unset($proffields);

        return $this->customfields;
    }

    /**
     * Post logout hook.
     *
     * This method is used after moodle logout by auth classes to execute server logout.
     *
     * @param stdClass $user clone of USER object before the user session was terminated
     */
    public function postlogout_hook($user) {
    }

    /**
     * Update a local user record from an external source.
     * This is a lighter version of the one in moodlelib -- won't do
     * expensive ops such as enrolment.
     *
     * @param string $username username
     * @param array $updatekeys fields to update, false updates all fields.
     * @param bool $triggerevent set false if user_updated event should not be triggered.
     *             This will not affect user_password_updated event triggering.
     * @param bool $suspenduser Should the user be suspended?
     * @return stdClass|bool updated user record or false if there is no new info to update.
     */
    protected function update_user_record($username, $updatekeys = false, $triggerevent = false, $suspenduser = false) {
        global $CFG, $DB;

        require_once($CFG->dirroot.'/user/profile/lib.php');

        // Just in case check text case.
        $username = trim(core_text::strtolower($username));

        // Get the current user record.
        $user = $DB->get_record('user', array('username' => $username, 'mnethostid' => $CFG->mnet_localhost_id));
        if (empty($user)) { // Trouble.
            error_log($this->errorlogtag . get_string('auth_usernotexist', 'auth', $username));
            print_error('auth_usernotexist', 'auth', '', $username);
            die;
        }

        // Protect the userid from being overwritten.
        $userid = $user->id;

        $needsupdate = false;

        if ($newinfo = $this->get_userinfo($username)) {
            $newinfo = truncate_userinfo($newinfo);

            if (empty($updatekeys)) { // All keys? this does not support removing values.
                $updatekeys = array_keys($newinfo);
            }

            if (!empty($updatekeys)) {
                $newuser = new stdClass();
                $newuser->id = $userid;
                // The cast to int is a workaround for MDL-53959.
                $newuser->suspended = (int) $suspenduser;
                // Load all custom fields.
                $profilefields = (array) profile_user_record($user->id, false);
                $newprofilefields = [];

                foreach ($updatekeys as $key) {
                    if (isset($newinfo[$key])) {
                        $value = $newinfo[$key];
                    } else {
                        $value = '';
                    }

                    if (!empty($this->config->{'field_updatelocal_' . $key})) {
                        if (preg_match('/^profile_field_(.*)$/', $key, $match)) {
                            // Custom field.
                            $field = $match[1];
                            $currentvalue = isset($profilefields[$field]) ? $profilefields[$field] : null;
                            $newprofilefields[$field] = $value;
                        } else {
                            // Standard field.
                            $currentvalue = isset($user->$key) ? $user->$key : null;
                            $newuser->$key = $value;
                        }

                        // Only update if it's changed.
                        if ($currentvalue !== $value) {
                            $needsupdate = true;
                        }
                    }
                }
            }

            if ($needsupdate) {
                user_update_user($newuser, false, $triggerevent);
                profile_save_custom_fields($newuser->id, $newprofilefields);
                return $DB->get_record('user', array('id' => $userid, 'deleted' => 0));
            }
        }

        return false;
    }

    /**
     * Return the list of enabled identity providers.
     *
     * Each identity provider data contains the keys url, name and iconurl (or
     * icon). See the documentation of {@link auth_plugin_base::loginpage_idp_list()}
     * for detailed description of the returned structure.
     *
     * @param array $authsequence site's auth sequence (list of auth plugins ordered)
     * @return array List of arrays describing the identity providers
     */
    public static function get_identity_providers($authsequence) {
        global $SESSION;

        $identityproviders = [];
        foreach ($authsequence as $authname) {
            $authplugin = get_auth_plugin($authname);
            $wantsurl = (isset($SESSION->wantsurl)) ? $SESSION->wantsurl : '';
            $identityproviders = array_merge($identityproviders, $authplugin->loginpage_idp_list($wantsurl));
        }
        return $identityproviders;
    }

    /**
     * Prepare a list of identity providers for output.
     *
     * @param array $identityproviders as returned by {@link self::get_identity_providers()}
     * @param renderer_base $output
     * @return array the identity providers ready for output
     */
    public static function prepare_identity_providers_for_output($identityproviders, renderer_base $output) {
        $data = [];
        foreach ($identityproviders as $idp) {
            if (!empty($idp['icon'])) {
                // Pre-3.3 auth plugins provide icon as a pix_icon instance. New auth plugins (since 3.3) provide iconurl.
                $idp['iconurl'] = $output->image_url($idp['icon']->pix, $idp['icon']->component);
            }
            if ($idp['iconurl'] instanceof moodle_url) {
                $idp['iconurl'] = $idp['iconurl']->out(false);
            }
            unset($idp['icon']);
            if ($idp['url'] instanceof moodle_url) {
                $idp['url'] = $idp['url']->out(false);
            }
            $data[] = $idp;
        }
        return $data;
    }

    /**
     * Returns information on how the specified user can change their password.
     *
     * @param stdClass $user A user object
     * @return string[] An array of strings with keys subject and message
     */
    public function get_password_change_info(stdClass $user) : array {
        $site = get_site();
        $systemcontext = context_system::instance();

        $data = new stdClass();
        $data->firstname = $user->firstname;
        $data->lastname  = $user->lastname;
        $data->username  = $user->username;
        $data->sitename  = format_string($site->fullname);
        $data->admin     = generate_email_signoff();

        if ($this->can_change_password() and $this->change_password_url()) {
            // We have some external url for password changing.
            $data->link = $this->change_password_url();
        } else {
            // No way to change password, sorry.
            $data->link = '';
        }

        if (!empty($data->link) and has_capability('moodle/user:changeownpassword', $systemcontext, $user->id)) {
            $subject = get_string('emailpasswordchangeinfosubject', '', format_string($site->fullname));
            $message = get_string('emailpasswordchangeinfo', '', $data);
        } else {
            $subject = get_string('emailpasswordchangeinfosubject', '', format_string($site->fullname));
            $message = get_string('emailpasswordchangeinfofail', '', $data);
        }

        return [
            'subject' => $subject,
            'message' => $message
        ];
    }
}

/**
 * Verify if user is locked out.
 *
 * @param stdClass $user
 * @return bool true if user locked out
 */
function login_is_lockedout($user) {
    global $CFG;

    if ($user->mnethostid != $CFG->mnet_localhost_id) {
        return false;
    }
    if (isguestuser($user)) {
        return false;
    }

    if (empty($CFG->lockoutthreshold)) {
        // Lockout not enabled.
        return false;
    }

    if (get_user_preferences('login_lockout_ignored', 0, $user)) {
        // This preference may be used for accounts that must not be locked out.
        return false;
    }

    $locked = get_user_preferences('login_lockout', 0, $user);
    if (!$locked) {
        return false;
    }

    if (empty($CFG->lockoutduration)) {
        // Locked out forever.
        return true;
    }

    if (time() - $locked < $CFG->lockoutduration) {
        return true;
    }

    login_unlock_account($user);

    return false;
}

/**
 * To be called after valid user login.
 * @param stdClass $user
 */
function login_attempt_valid($user) {
    global $CFG;

    // Note: user_loggedin event is triggered in complete_user_login().

    if ($user->mnethostid != $CFG->mnet_localhost_id) {
        return;
    }
    if (isguestuser($user)) {
        return;
    }

    // Always unlock here, there might be some race conditions or leftovers when switching threshold.
    login_unlock_account($user);
}

/**
 * To be called after failed user login.
 * @param stdClass $user
 */
function login_attempt_failed($user) {
    global $CFG;

    if ($user->mnethostid != $CFG->mnet_localhost_id) {
        return;
    }
    if (isguestuser($user)) {
        return;
    }

    $count = get_user_preferences('login_failed_count', 0, $user);
    $last = get_user_preferences('login_failed_last', 0, $user);
    $sincescuccess = get_user_preferences('login_failed_count_since_success', $count, $user);
    $sincescuccess = $sincescuccess + 1;
    set_user_preference('login_failed_count_since_success', $sincescuccess, $user);

    if (empty($CFG->lockoutthreshold)) {
        // No threshold means no lockout.
        // Always unlock here, there might be some race conditions or leftovers when switching threshold.
        login_unlock_account($user);
        return;
    }

    if (!empty($CFG->lockoutwindow) and time() - $last > $CFG->lockoutwindow) {
        $count = 0;
    }

    $count = $count+1;

    set_user_preference('login_failed_count', $count, $user);
    set_user_preference('login_failed_last', time(), $user);

    if ($count >= $CFG->lockoutthreshold) {
        login_lock_account($user);
    }
}

/**
 * Lockout user and send notification email.
 *
 * @param stdClass $user
 */
function login_lock_account($user) {
    global $CFG;

    if ($user->mnethostid != $CFG->mnet_localhost_id) {
        return;
    }
    if (isguestuser($user)) {
        return;
    }

    if (get_user_preferences('login_lockout_ignored', 0, $user)) {
        // This user can not be locked out.
        return;
    }

    $alreadylockedout = get_user_preferences('login_lockout', 0, $user);

    set_user_preference('login_lockout', time(), $user);

    if ($alreadylockedout == 0) {
        $secret = random_string(15);
        set_user_preference('login_lockout_secret', $secret, $user);

        $oldforcelang = force_current_language($user->lang);

        $site = get_site();
        $supportuser = core_user::get_support_user();

        $data = new stdClass();
        $data->firstname = $user->firstname;
        $data->lastname  = $user->lastname;
        $data->username  = $user->username;
        $data->sitename  = format_string($site->fullname);
        $data->link      = $CFG->wwwroot.'/login/unlock_account.php?u='.$user->id.'&s='.$secret;
        $data->admin     = generate_email_signoff();

        $message = get_string('lockoutemailbody', 'admin', $data);
        $subject = get_string('lockoutemailsubject', 'admin', format_string($site->fullname));

        if ($message) {
            // Directly email rather than using the messaging system to ensure its not routed to a popup or jabber.
            email_to_user($user, $supportuser, $subject, $message);
        }

        force_current_language($oldforcelang);
    }
}

/**
 * Unlock user account and reset timers.
 *
 * @param stdClass $user
 */
function login_unlock_account($user) {
    unset_user_preference('login_lockout', $user);
    unset_user_preference('login_failed_count', $user);
    unset_user_preference('login_failed_last', $user);

    // Note: do not clear the lockout secret because user might click on the link repeatedly.
}

/**
 * Returns whether or not the captcha element is enabled, and the admin settings fulfil its requirements.
 * @return bool
 */
function signup_captcha_enabled() {
    global $CFG;
    $authplugin = get_auth_plugin($CFG->registerauth);
    return !empty($CFG->recaptchapublickey) && !empty($CFG->recaptchaprivatekey) && $authplugin->is_captcha_enabled();
}

/**
 * Validates the standard sign-up data (except recaptcha that is validated by the form element).
 *
 * @param  array $data  the sign-up data
 * @param  array $files files among the data
 * @return array list of errors, being the key the data element name and the value the error itself
 * @since Moodle 3.2
 */
function signup_validate_data($data, $files) {
    global $CFG, $DB;

    $errors = array();
    $authplugin = get_auth_plugin($CFG->registerauth);

    if ($DB->record_exists('user', array('username' => $data['username'], 'mnethostid' => $CFG->mnet_localhost_id))) {
        $errors['username'] = get_string('usernameexists');
    } else {
        // Check allowed characters.
        if ($data['username'] !== core_text::strtolower($data['username'])) {
            $errors['username'] = get_string('usernamelowercase');
        } else {
            if ($data['username'] !== core_user::clean_field($data['username'], 'username')) {
                $errors['username'] = get_string('invalidusername');
            }

        }
    }

    // Check if user exists in external db.
    // TODO: maybe we should check all enabled plugins instead.
    if ($authplugin->user_exists($data['username'])) {
        $errors['username'] = get_string('usernameexists');
    }

    if (! validate_email($data['email'])) {
        $errors['email'] = get_string('invalidemail');

    } else if (empty($CFG->allowaccountssameemail)) {
        // Make a case-insensitive query for the given email address.
        $select = $DB->sql_equal('email', ':email', false) . ' AND mnethostid = :mnethostid';
        $params = array(
            'email' => $data['email'],
            'mnethostid' => $CFG->mnet_localhost_id,
        );
        // If there are other user(s) that already have the same email, show an error.
        if ($DB->record_exists_select('user', $select, $params)) {
            $forgotpasswordurl = new moodle_url('/login/forgot_password.php');
            $forgotpasswordlink = html_writer::link($forgotpasswordurl, get_string('emailexistshintlink'));
            $errors['email'] = get_string('emailexists') . ' ' . get_string('emailexistssignuphint', 'moodle', $forgotpasswordlink);
        }
    }
    if (empty($data['email2'])) {
        $errors['email2'] = get_string('missingemail');

    } else if (core_text::strtolower($data['email2']) != core_text::strtolower($data['email'])) {
        $errors['email2'] = get_string('invalidemail');
    }
    if (!isset($errors['email'])) {
        if ($err = email_is_not_allowed($data['email'])) {
            $errors['email'] = $err;
        }
    }

    $errmsg = '';
    if (!check_password_policy($data['password'], $errmsg)) {
        $errors['password'] = $errmsg;
    }

    // Validate customisable profile fields. (profile_validation expects an object as the parameter with userid set).
    $dataobject = (object)$data;
    $dataobject->id = 0;
    $errors += profile_validation($dataobject, $files);

    return $errors;
}

/**
 * Add the missing fields to a user that is going to be created
 *
 * @param  stdClass $user the new user object
 * @return stdClass the user filled
 * @since Moodle 3.2
 */
function signup_setup_new_user($user) {
    global $CFG;

    $user->confirmed   = 0;
    $user->lang        = current_language();
    $user->firstaccess = 0;
    $user->timecreated = time();
    $user->mnethostid  = $CFG->mnet_localhost_id;
    $user->secret      = random_string(15);
    $user->auth        = $CFG->registerauth;
    // Initialize alternate name fields to empty strings.
    $namefields = array_diff(get_all_user_name_fields(), useredit_get_required_name_fields());
    foreach ($namefields as $namefield) {
        $user->$namefield = '';
    }
    return $user;
}

/**
 * Check if user confirmation is enabled on this site and return the auth plugin handling registration if enabled.
 *
 * @return stdClass the current auth plugin handling user registration or false if registration not enabled
 * @since Moodle 3.2
 */
function signup_get_user_confirmation_authplugin() {
    global $CFG;

    if (empty($CFG->registerauth)) {
        return false;
    }
    $authplugin = get_auth_plugin($CFG->registerauth);

    if (!$authplugin->can_confirm()) {
        return false;
    }
    return $authplugin;
}

/**
 * Check if sign-up is enabled in the site. If is enabled, the function will return the authplugin instance.
 *
 * @return mixed false if sign-up is not enabled, the authplugin instance otherwise.
 * @since  Moodle 3.2
 */
function signup_is_enabled() {
    global $CFG;

    if (!empty($CFG->registerauth)) {
        $authplugin = get_auth_plugin($CFG->registerauth);
        if ($authplugin->can_signup()) {
            return $authplugin;
        }
    }
    return false;
}

/**
 * Helper function used to print locking for auth plugins on admin pages.
 * @param stdclass $settings Moodle admin settings instance
 * @param string $auth authentication plugin shortname
 * @param array $userfields user profile fields
 * @param string $helptext help text to be displayed at top of form
 * @param boolean $mapremotefields Map fields or lock only.
 * @param boolean $updateremotefields Allow remote updates
 * @param array $customfields list of custom profile fields
 * @since Moodle 3.3
 */
function display_auth_lock_options($settings, $auth, $userfields, $helptext, $mapremotefields, $updateremotefields, $customfields = array()) {
    global $DB;

    // Introductory explanation and help text.
    if ($mapremotefields) {
        $settings->add(new admin_setting_heading($auth.'/data_mapping', new lang_string('auth_data_mapping', 'auth'), $helptext));
    } else {
        $settings->add(new admin_setting_heading($auth.'/auth_fieldlocks', new lang_string('auth_fieldlocks', 'auth'), $helptext));
    }

    // Generate the list of options.
    $lockoptions = array ('unlocked'        => get_string('unlocked', 'auth'),
                          'unlockedifempty' => get_string('unlockedifempty', 'auth'),
                          'locked'          => get_string('locked', 'auth'));
    $updatelocaloptions = array('oncreate'  => get_string('update_oncreate', 'auth'),
                                'onlogin'   => get_string('update_onlogin', 'auth'));
    $updateextoptions = array('0'  => get_string('update_never', 'auth'),
                              '1'  => get_string('update_onupdate', 'auth'));

    // Generate the list of profile fields to allow updates / lock.
    if (!empty($customfields)) {
        $userfields = array_merge($userfields, $customfields);
        $customfieldname = $DB->get_records('user_info_field', null, '', 'shortname, name');
    }

    foreach ($userfields as $field) {
        // Define the fieldname we display to the  user.
        // this includes special handling for some profile fields.
        $fieldname = $field;
        $fieldnametoolong = false;
        if ($fieldname === 'lang') {
            $fieldname = get_string('language');
        } else if (!empty($customfields) && in_array($field, $customfields)) {
            // If custom field then pick name from database.
            $fieldshortname = str_replace('profile_field_', '', $fieldname);
            $fieldname = $customfieldname[$fieldshortname]->name;
            if (core_text::strlen($fieldshortname) > 67) {
                // If custom profile field name is longer than 67 characters we will not be able to store the setting
                // such as 'field_updateremote_profile_field_NOTSOSHORTSHORTNAME' in the database because the character
                // limit for the setting name is 100.
                $fieldnametoolong = true;
            }
        } else if ($fieldname == 'url') {
            $fieldname = get_string('webpage');
        } else {
            $fieldname = get_string($fieldname);
        }

        // Generate the list of fields / mappings.
        if ($fieldnametoolong) {
            // Display a message that the field can not be mapped because it's too long.
            $url = new moodle_url('/user/profile/index.php');
            $a = (object)['fieldname' => s($fieldname), 'shortname' => s($field), 'charlimit' => 67, 'link' => $url->out()];
            $settings->add(new admin_setting_heading($auth.'/field_not_mapped_'.sha1($field), '',
                get_string('cannotmapfield', 'auth', $a)));
        } else if ($mapremotefields) {
            // We are mapping to a remote field here.
            // Mapping.
            $settings->add(new admin_setting_configtext("auth_{$auth}/field_map_{$field}",
                    get_string('auth_fieldmapping', 'auth', $fieldname), '', '', PARAM_RAW, 30));

            // Update local.
            $settings->add(new admin_setting_configselect("auth_{$auth}/field_updatelocal_{$field}",
                    get_string('auth_updatelocalfield', 'auth', $fieldname), '', 'oncreate', $updatelocaloptions));

            // Update remote.
            if ($updateremotefields) {
                    $settings->add(new admin_setting_configselect("auth_{$auth}/field_updateremote_{$field}",
                        get_string('auth_updateremotefield', 'auth', $fieldname), '', 0, $updateextoptions));
            }

            // Lock fields.
            $settings->add(new admin_setting_configselect("auth_{$auth}/field_lock_{$field}",
                    get_string('auth_fieldlockfield', 'auth', $fieldname), '', 'unlocked', $lockoptions));

        } else {
            // Lock fields Only.
            $settings->add(new admin_setting_configselect("auth_{$auth}/field_lock_{$field}",
                    get_string('auth_fieldlockfield', 'auth', $fieldname), '', 'unlocked', $lockoptions));
        }
    }
}
