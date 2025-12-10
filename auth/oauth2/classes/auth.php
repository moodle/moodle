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
 * Anobody can login with any password.
 *
 * @package auth_oauth2
 * @copyright 2017 Damyon Wiese
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

namespace auth_oauth2;

defined('MOODLE_INTERNAL') || die();

use pix_icon;
use moodle_url;
use core_text;
use context_system;
use stdClass;
use core\oauth2\issuer;
use core\oauth2\client;

global $CFG;
require_once($CFG->libdir.'/authlib.php');
require_once($CFG->dirroot.'/user/lib.php');
require_once($CFG->dirroot.'/user/profile/lib.php');
require_once($CFG->dirroot.'/login/lib.php');

/**
 * Plugin for oauth2 authentication.
 *
 * @package auth_oauth2
 * @copyright 2017 Damyon Wiese
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */
class auth extends \auth_plugin_base {

    /**
     * @var stdClass $userinfo The set of user info returned from the oauth handshake
     */
    private static $userinfo;

    /**
     * @var stdClass $userpicture The url to a picture.
     */
    private static $userpicture;

    /**
     * Constructor.
     */
    public function __construct() {
        $this->authtype = 'oauth2';
        $this->config = get_config('auth_oauth2');
    }

    /**
     * Returns true if the username and password work or don't exist and false
     * if the user exists and the password is wrong.
     *
     * @param string $username The username
     * @param string $password The password
     * @return bool Authentication success or failure.
     */
    public function user_login($username, $password) {
        $cached = $this->get_static_user_info();
        if (empty($cached)) {
            // This means we were called as part of a normal login flow - without using oauth.
            return false;
        }
        $verifyusername = $cached['username'];
        if ($verifyusername == $username) {
            return true;
        }
        return false;
    }

    /**
     * We don't want to allow users setting an internal password.
     *
     * @return bool
     */
    public function prevent_local_passwords() {
        return true;
    }

    /**
     * Returns true if this authentication plugin is 'internal'.
     *
     * @return bool
     */
    public function is_internal() {
        return false;
    }

    /**
     * Indicates if moodle should automatically update internal user
     * records with data from external sources using the information
     * from auth_plugin_base::get_userinfo().
     *
     * @return bool true means automatically copy data from ext to user table
     */
    public function is_synchronised_with_external() {
        return true;
    }

    /**
     * Returns true if this authentication plugin can change the user's
     * password.
     *
     * @return bool
     */
    public function can_change_password() {
        return false;
    }

    /**
     * Returns the URL for changing the user's pw, or empty if the default can
     * be used.
     *
     * @return moodle_url
     */
    public function change_password_url() {
        return null;
    }

    /**
     * Returns true if plugin allows resetting of internal password.
     *
     * @return bool
     */
    public function can_reset_password() {
        return false;
    }

    /**
     * Returns true if plugin can be manually set.
     *
     * @return bool
     */
    public function can_be_manually_set() {
        return true;
    }

    /**
     * Return the userinfo from the oauth handshake. Will only be valid
     * for the logged in user.
     * @param string $username
     */
    public function get_userinfo($username) {
        $cached = $this->get_static_user_info();
        if (!empty($cached) && $cached['username'] == $username) {
            return $cached;
        }
        return false;
    }

    /**
     * Return a list of identity providers to display on the login page.
     *
     * @param string|moodle_url $wantsurl The requested URL.
     * @return array List of arrays with keys url, iconurl and name.
     */
    public function loginpage_idp_list($wantsurl) {
        $providers = \core\oauth2\api::get_all_issuers(true);
        $result = [];
        foreach ($providers as $idp) {
            if ($idp->is_available_for_login()) {
                $params = ['id' => $idp->get('id'), 'sesskey' => sesskey()];
                if (!empty($wantsurl)) {
                    $params['wantsurl'] = $wantsurl;
                }
                $url = new moodle_url('/auth/oauth2/login.php', $params);
                $icon = $idp->get('image');
                $result[] = ['url' => $url, 'iconurl' => $icon, 'name' => $idp->get_display_name()];
            }
        }
        return $result;
    }

    /**
     * Statically cache the user info from the oauth handshake
     * @param stdClass $userinfo
     */
    private function set_static_user_info($userinfo) {
        self::$userinfo = $userinfo;
    }

    /**
     * Get the static cached user info
     * @return stdClass
     */
    private function get_static_user_info() {
        return self::$userinfo;
    }

    /**
     * Statically cache the user picture from the oauth handshake
     * @param string $userpicture
     */
    private function set_static_user_picture($userpicture) {
        self::$userpicture = $userpicture;
    }

    /**
     * Get the static cached user picture
     * @return string
     */
    private function get_static_user_picture() {
        return self::$userpicture;
    }

    /**
     * If this user has no picture - but we got one from oauth - set it.
     * @param stdClass $user
     * @return boolean True if the image was updated.
     */
    private function update_picture($user) {
        global $CFG, $DB, $USER;

        require_once($CFG->libdir . '/filelib.php');
        require_once($CFG->libdir . '/gdlib.php');
        require_once($CFG->dirroot . '/user/lib.php');

        $fs = get_file_storage();
        $userid = $user->id;
        if (!empty($user->picture)) {
            return false;
        }
        if (!empty($CFG->enablegravatar)) {
            return false;
        }

        $picture = $this->get_static_user_picture();
        if (empty($picture)) {
            return false;
        }

        $context = \context_user::instance($userid, MUST_EXIST);
        $fs->delete_area_files($context->id, 'user', 'newicon');

        $filerecord = array(
            'contextid' => $context->id,
            'component' => 'user',
            'filearea' => 'newicon',
            'itemid' => 0,
            'filepath' => '/',
            'filename' => 'image'
        );

        try {
            $fs->create_file_from_string($filerecord, $picture);
        } catch (\file_exception $e) {
            return get_string($e->errorcode, $e->module, $e->a);
        }

        $iconfile = $fs->get_area_files($context->id, 'user', 'newicon', false, 'itemid', false);

        // There should only be one.
        $iconfile = reset($iconfile);

        // Something went wrong while creating temp file - remove the uploaded file.
        if (!$iconfile = $iconfile->copy_content_to_temp()) {
            $fs->delete_area_files($context->id, 'user', 'newicon');
            return false;
        }

        // Copy file to temporary location and the send it for processing icon.
        $newpicture = (int) process_new_icon($context, 'user', 'icon', 0, $iconfile);
        // Delete temporary file.
        @unlink($iconfile);
        // Remove uploaded file.
        $fs->delete_area_files($context->id, 'user', 'newicon');
        // Set the user's picture.
        $updateuser = new stdClass();
        $updateuser->id = $userid;
        $updateuser->picture = $newpicture;
        $USER->picture = $newpicture;
        user_update_user($updateuser);
        return true;
    }

    /**
     * Update user data according to data sent by authorization server.
     *
     * @param array $externaldata data from authorization server
     * @param stdClass $userdata Current data of the user to be updated
     * @return stdClass The updated user record, or the existing one if there's nothing to be updated.
     */
    private function update_user(array $externaldata, $userdata) {
        $user = (object) [
            'id' => $userdata->id,
        ];

        // We can only update if the default authentication type of the user is set to OAuth2 as well. Otherwise, we might mess
        // up the user data of other users that use different authentication mechanisms (e.g. linked logins).
        if ($userdata->auth !== $this->authtype) {
            return $userdata;
        }

        $allfields = array_merge($this->userfields, $this->get_custom_user_profile_fields());

        // Go through each field from the external data.
        foreach ($externaldata as $fieldname => $value) {
            if (!in_array($fieldname, $allfields)) {
                // Skip if this field doesn't belong to the list of fields that can be synced with the OAuth2 issuer.
                continue;
            }

            $userhasfield = property_exists($userdata, $fieldname);
            // Find out if it is a profile field.
            $isprofilefield = strpos($fieldname, 'profile_field_') === 0;
            $profilefieldname = str_replace('profile_field_', '', $fieldname);
            $userhasprofilefield = $isprofilefield && array_key_exists($profilefieldname, $userdata->profile);

            // Just in case this field is on the list, but not part of the user data. This shouldn't happen though.
            if (!($userhasfield || $userhasprofilefield)) {
                continue;
            }

            // Get the old value.
            $oldvalue = $isprofilefield ? (string) $userdata->profile[$profilefieldname] : (string) $userdata->$fieldname;

            // Get the lock configuration of the field.
            if (!empty($this->config->{'field_lock_' . $fieldname})) {
                $lockvalue = $this->config->{'field_lock_' . $fieldname};
            } else {
                $lockvalue = 'unlocked';
            }

            // We should update fields that meet the following criteria:
            // - Lock value set to 'unlocked'; or 'unlockedifempty', given the current value is empty.
            // - The value has changed.
            if ($lockvalue === 'unlocked' || ($lockvalue === 'unlockedifempty' && empty($oldvalue))) {
                $value = (string)$value;
                if ($oldvalue !== $value) {
                    $user->$fieldname = $value;
                }
            }
        }
        // Update the user data.
        user_update_user($user, false);

        // Save user profile data.
        profile_save_data($user);

        // Refresh user for $USER variable.
        return get_complete_user_data('id', $user->id);
    }

    /**
     * Confirm the new user as registered.
     *
     * @param string $username
     * @param string $confirmsecret
     */
    public function user_confirm($username, $confirmsecret) {
        global $DB;
        $user = get_complete_user_data('username', $username);

        if (!empty($user)) {
            if ($user->auth != $this->authtype) {
                return AUTH_CONFIRM_ERROR;

            } else if ($user->secret === $confirmsecret && $user->confirmed) {
                return AUTH_CONFIRM_ALREADY;

            } else if ($user->secret === $confirmsecret) {   // They have provided the secret key to get in.
                $DB->set_field("user", "confirmed", 1, array("id" => $user->id));
                return AUTH_CONFIRM_OK;
            }
        } else {
            return AUTH_CONFIRM_ERROR;
        }
    }

    /**
     * Print a page showing that a confirm email was sent with instructions.
     *
     * @param string $title
     * @param string $message
     */
    public function print_confirm_required($title, $message) {
        global $PAGE, $OUTPUT, $CFG;

        $PAGE->navbar->add($title);
        $PAGE->set_title($title);
        $PAGE->set_heading($PAGE->course->fullname);
        echo $OUTPUT->header();
        notice($message, "$CFG->wwwroot/index.php");
    }

    /**
     * Complete the login process after oauth handshake is complete.
     * @param \core\oauth2\client $client
     * @param string $redirecturl
     * @return void Either redirects or throws an exception
     */
    public function complete_login(client $client, $redirecturl) {
        global $CFG, $SESSION, $PAGE;

        $rawuserinfo = $client->get_raw_userinfo();
        $userinfo = $client->get_userinfo();

        if (!$userinfo) {
            // Trigger login failed event.
            $failurereason = AUTH_LOGIN_NOUSER;
            $event = \core\event\user_login_failed::create(['other' => ['username' => 'unknown',
                                                                        'reason' => $failurereason]]);
            $event->trigger();

            $errormsg = get_string('loginerror_nouserinfo', 'auth_oauth2');
            $SESSION->loginerrormsg = $errormsg;
            $client->log_out();
            redirect(new moodle_url('/login/index.php'));
        }
        if (empty($userinfo['username']) || empty($userinfo['email'])) {
            // Trigger login failed event.
            $failurereason = AUTH_LOGIN_NOUSER;
            $event = \core\event\user_login_failed::create(['other' => ['username' => 'unknown',
                                                                        'reason' => $failurereason]]);
            $event->trigger();

            $errormsg = get_string('loginerror_userincomplete', 'auth_oauth2');
            $SESSION->loginerrormsg = $errormsg;
            $client->log_out();
            redirect(new moodle_url('/login/index.php'));
        }

        $userinfo['username'] = trim(core_text::strtolower($userinfo['username']));
        $oauthemail = $userinfo['email'];

        // Once we get here we have the user info from oauth.
        $userwasmapped = false;

        // Clean and remember the picture / lang.
        if (!empty($userinfo['picture'])) {
            $this->set_static_user_picture($userinfo['picture']);
            unset($userinfo['picture']);
        }

        if (!empty($userinfo['lang'])) {
            $userinfo['lang'] = str_replace('-', '_', trim(core_text::strtolower($userinfo['lang'])));
            if (!get_string_manager()->translation_exists($userinfo['lang'], false)) {
                unset($userinfo['lang']);
            }
        }

        $issuer = $client->get_issuer();
        // First we try and find a defined mapping.
        $linkedlogin = api::match_username_to_user($userinfo['username'], $issuer);

        if (!empty($linkedlogin) && empty($linkedlogin->get('confirmtoken'))) {
            $mappeduser = get_complete_user_data('id', $linkedlogin->get('userid'));

            if ($mappeduser && $mappeduser->suspended) {
                // Check if there's another user with the same email that is not suspended.
                $moodleuser = \core_user::get_user_by_email($userinfo['email'], '*', null, IGNORE_MULTIPLE);
                if ($moodleuser->id == $mappeduser->id) {
                    $failurereason = AUTH_LOGIN_SUSPENDED;
                    $event = \core\event\user_login_failed::create([
                        'userid' => $mappeduser->id,
                        'other' => [
                            'username' => $userinfo['username'],
                            'reason' => $failurereason,
                        ],
                    ]);
                    $event->trigger();
                    $SESSION->loginerrormsg = get_string('invalidlogin');
                    $client->log_out();
                    redirect(new moodle_url('/login/index.php'));
                } else if ($moodleuser && !$moodleuser->suspended) {
                    // Update the OAuth2 linked login to point to the active user account.
                    $linkedlogin->set('userid', $moodleuser->id);
                    $linkedlogin->set('timemodified', time());
                    $linkedlogin->update();

                    // Update user fields and continue with login.
                    $userinfo = $this->update_user($userinfo, $moodleuser);
                    $userwasmapped = true;
                }
            } else if ($mappeduser && ($mappeduser->confirmed || !$issuer->get('requireconfirmation'))) {
                // Update user fields.
                $userinfo = $this->update_user($userinfo, $mappeduser);
                $userwasmapped = true;
            } else {
                // Trigger login failed event.
                $failurereason = AUTH_LOGIN_UNAUTHORISED;
                $event = \core\event\user_login_failed::create(['other' => ['username' => $userinfo['username'],
                                                                            'reason' => $failurereason]]);
                $event->trigger();

                $errormsg = get_string('confirmationpending', 'auth_oauth2');
                $SESSION->loginerrormsg = $errormsg;
                $client->log_out();
                redirect(new moodle_url('/login/index.php'));
            }
        } else if (!empty($linkedlogin)) {
            // Trigger login failed event.
            $failurereason = AUTH_LOGIN_UNAUTHORISED;
            $event = \core\event\user_login_failed::create(['other' => ['username' => $userinfo['username'],
                                                                        'reason' => $failurereason]]);
            $event->trigger();

            $errormsg = get_string('confirmationpending', 'auth_oauth2');
            $SESSION->loginerrormsg = $errormsg;
            $client->log_out();
            redirect(new moodle_url('/login/index.php'));
        }

        if (!$issuer->is_valid_login_domain($oauthemail)) {
            // Trigger login failed event.
            $failurereason = AUTH_LOGIN_UNAUTHORISED;
            $event = \core\event\user_login_failed::create(['other' => ['username' => $userinfo['username'],
                                                                        'reason' => $failurereason]]);
            $event->trigger();

            $errormsg = get_string('notloggedindebug', 'auth_oauth2', get_string('loginerror_invaliddomain', 'auth_oauth2'));
            $SESSION->loginerrormsg = $errormsg;
            $client->log_out();
            redirect(new moodle_url('/login/index.php'));
        }

        if (!$userwasmapped) {
            // No defined mapping - we need to see if there is an existing account with the same email.
            $moodleuser = \core_user::get_user_by_email($userinfo['email'], '*', null, IGNORE_MULTIPLE);

            // Ensure we don't link a login for a suspended user.
            if (!empty($moodleuser) && $moodleuser->suspended) {
                $failurereason = AUTH_LOGIN_SUSPENDED;
                $event = \core\event\user_login_failed::create([
                    'userid' => $moodleuser->id,
                    'other' => [
                        'username' => $userinfo['email'],
                        'reason' => $failurereason,
                    ],
                ]);
                $event->trigger();
                $SESSION->loginerrormsg = get_string('invalidlogin');
                $client->log_out();
                redirect(new moodle_url('/login/index.php'));
            }

            if (!empty($moodleuser)) {
                if ($issuer->get('requireconfirmation')) {
                    $PAGE->set_url('/auth/oauth2/confirm-link-login.php');
                    $PAGE->set_context(context_system::instance());

                    \auth_oauth2\api::send_confirm_link_login_email($userinfo, $issuer, $moodleuser->id);
                    // Request to link to existing account.
                    $emailconfirm = get_string('emailconfirmlink', 'auth_oauth2');
                    $message = get_string('emailconfirmlinksent', 'auth_oauth2', $moodleuser->email);
                    $this->print_confirm_required($emailconfirm, $message);
                    exit();
                } else {
                    \auth_oauth2\api::link_login($userinfo, $issuer, $moodleuser->id, true);
                    // We dont have profile loaded on $moodleuser, so load it.
                    require_once($CFG->dirroot.'/user/profile/lib.php');
                    profile_load_custom_fields($moodleuser);
                    $userinfo = $this->update_user($userinfo, $moodleuser);
                    // No redirect, we will complete this login.
                }

            } else {
                // This is a new account.
                $exists = \core_user::get_user_by_username($userinfo['username']);
                // Creating a new user?
                if ($exists) {
                    // Trigger login failed event.
                    $failurereason = AUTH_LOGIN_FAILED;
                    $event = \core\event\user_login_failed::create(['other' => ['username' => $userinfo['username'],
                                                                                'reason' => $failurereason]]);
                    $event->trigger();

                    // The username exists but the emails don't match. Refuse to continue.
                    $errormsg = get_string('accountexists', 'auth_oauth2');
                    $SESSION->loginerrormsg = $errormsg;
                    $client->log_out();
                    redirect(new moodle_url('/login/index.php'));
                }

                if (email_is_not_allowed($userinfo['email'])) {
                    // Trigger login failed event.
                    $failurereason = AUTH_LOGIN_FAILED;
                    $event = \core\event\user_login_failed::create(['other' => ['username' => $userinfo['username'],
                                                                                'reason' => $failurereason]]);
                    $event->trigger();
                    // The username exists but the emails don't match. Refuse to continue.
                    $reason = get_string('loginerror_invaliddomain', 'auth_oauth2');
                    $errormsg = get_string('notloggedindebug', 'auth_oauth2', $reason);
                    $SESSION->loginerrormsg = $errormsg;
                    $client->log_out();
                    redirect(new moodle_url('/login/index.php'));
                }

                if (!empty($CFG->authpreventaccountcreation)) {
                    // Trigger login failed event.
                    $failurereason = AUTH_LOGIN_UNAUTHORISED;
                    $event = \core\event\user_login_failed::create(['other' => ['username' => $userinfo['username'],
                                                                                'reason' => $failurereason]]);
                    $event->trigger();
                    // The username does not exist and settings prevent creating new accounts.
                    $reason = get_string('loginerror_cannotcreateaccounts', 'auth_oauth2');
                    $errormsg = get_string('notloggedindebug', 'auth_oauth2', $reason);
                    $SESSION->loginerrormsg = $errormsg;
                    $client->log_out();
                    redirect(new moodle_url('/login/index.php'));
                }

                if ($issuer->get('requireconfirmation')) {
                    $PAGE->set_url('/auth/oauth2/confirm-account.php');
                    $PAGE->set_context(context_system::instance());

                    // Create a new (unconfirmed account) and send an email to confirm it.
                    $user = \auth_oauth2\api::send_confirm_account_email($userinfo, $issuer);

                    $this->update_picture($user);
                    $emailconfirm = get_string('emailconfirm');
                    $message = get_string('emailconfirmsent', '', $userinfo['email']);
                    $this->print_confirm_required($emailconfirm, $message);
                    exit();
                } else {
                    // Create a new confirmed account.
                    $newuser = \auth_oauth2\api::create_new_confirmed_account($userinfo, $issuer);
                    $userinfo = get_complete_user_data('id', $newuser->id);
                    // No redirect, we will complete this login.
                }
            }
        }

        // We used to call authenticate_user - but that won't work if the current user has a different default authentication
        // method. Since we now ALWAYS link a login - if we get to here we can directly allow the user in.
        $user = (object) $userinfo;

        // Add extra loggedin info.
        $this->set_extrauserinfo((array)$rawuserinfo);

        complete_user_login($user, $this->get_extrauserinfo());
        $this->update_picture($user);

        if (empty($redirecturl)) {
            $redirecturl = core_login_get_return_url();
        }
        redirect(new moodle_url($redirecturl));
    }

    /**
     * Returns information on how the specified user can change their password.
     * The password of the oauth2 accounts is not stored in Moodle.
     *
     * @param stdClass $user A user object
     * @return string[] An array of strings with keys subject and message
     */
    public function get_password_change_info(stdClass $user): array {
        $site = get_site();

        $data = new stdClass();
        $data->firstname = $user->firstname;
        $data->lastname  = $user->lastname;
        $data->username  = $user->username;
        $data->sitename  = format_string($site->fullname);
        $data->admin     = generate_email_signoff();

        $message = get_string('emailpasswordchangeinfo', 'auth_oauth2', $data);
        $subject = get_string('emailpasswordchangeinfosubject', 'auth_oauth2', format_string($site->fullname));

        return [
            'subject' => $subject,
            'message' => $message
        ];
    }
}
