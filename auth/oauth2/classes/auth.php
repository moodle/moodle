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

require_once($CFG->libdir.'/authlib.php');
require_once($CFG->dirroot.'/user/lib.php');
require_once($CFG->dirroot.'/user/profile/lib.php');

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
        $this->customfields = $this->get_custom_user_profile_fields();
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
        if (empty($wantsurl)) {
            $wantsurl = '/';
        }
        foreach ($providers as $idp) {
            if ($idp->is_available_for_login()) {
                $params = ['id' => $idp->get('id'), 'wantsurl' => $wantsurl, 'sesskey' => sesskey()];
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

        $allfields = array_merge($this->userfields, $this->customfields);

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

    // BEGIN LSU External Domains Fixes.
    public function normalize_external_oauth_email(string $email, string $domain = 'lsu.edu'): string {
        // If the email already ends with the specified domain, do nothing
        if (str_ends_with($email, '@' . $domain)) {
            return $email;
        }

        // Handle case-insensitive #EXT# using regex
        $local = preg_replace('/#ext#/i', '', $email);

        // Extract local part before the @
        $local = explode('@', $local)[0];

        // Find the last underscore (to separate username from domain)
        $lastunderscore = strrpos($local, '_');
        if ($lastunderscore !== false) {
            $username = substr($local, 0, $lastunderscore);
            $domainpart = substr($local, $lastunderscore + 1);
            return $username . '@' . $domainpart;
        }

        // Otherwise return the original email unmodified
        return $email;
    }
    // END LSU External Domains Fixes.

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

        // BEGIN LSU External Domains fixes.
        if (str_contains($CFG->allowedemaildomains, ',')) {

            // It's a list of domains.
            $alloweddomains = array_map('trim', explode(',', $CFG->allowedemaildomains));

        // Make sure it's not empty.
        } else if (trim($CFG->allowedemaildomains) != '') {

            // It's a single domain.
            $alloweddomains = [trim($CFG->allowedemaildomains)];

        // It's empty.
        } else {

            // Get the domain from the noreply.
            preg_match('/(?<=@)([a-zA-Z0-9.-]+)/', $CFG->noreplyaddress, $matches);

            // We found a domain.
            if (isset($matches[1])) {

                // Set it.
                $alloweddomains = [$matches[1]];

            // There's no domain present.
            } else {

                // Final fallback is hardcoded.
                $alloweddomains = ['lsu.edu'];
            }
        }

        // We've built ourselves an array where the 1st element is the one we want. Get it.
        $defaultdomain = reset($alloweddomains);

        // Normalize usernames. All the defaultdomain stuff is probably never used.
        $fixedusername = self::normalize_external_oauth_email($userinfo['username'], $defaultdomain);

        // Set the userinfo username appropriately.
        $userinfo['username'] = $fixedusername;
        $rawuserinfo->userPrincipalName = $fixedusername;

        // IDK if this is real phenomenon, but it happens to me.
        if (is_null($rawuserinfo->givenName) ||
            is_null($rawuserinfo->surname) ||
            !isset($userinfo['firstname']) ||
            !isset($userinfo['lastname'])
        ) {
            // If we have a displayName in the sso $rawuserinfo.
            if (!empty($rawuserinfo->displayName)) {

                // Split this into full words and make sure they're trimmed.
                $parts = preg_split('/\s+/', trim($rawuserinfo->displayName));

                // If we do not have a firstname in the userinfo array.
                if (!isset($userinfo['firstname'])) {
                    $userinfo['firstname'] = $parts[0] ?? null;
                }

                // If we do not have a lasttname in the userinfo array.
                if (!isset($userinfo['lastname'])) {
                    $userinfo['lastname'] = $parts[count($parts) - 1] ?? null;
                }

                // If the givenName is null.
                if (is_null($rawuserinfo->givenName)) {
                    $rawuserinfo->givenName = $parts[0] ?? null;
                }

                // If the surname is null.
                if (is_null($rawuserinfo->surname)) {
                    $rawuserinfo->surname = $parts[count($parts) - 1] ?? null;
                }
            }
        }
        // END LSU External Domains fixes.

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

        // BEGIN LSU userPrincipalName to email mapping.
        if (empty($userinfo['username'])) {
            $userinfo['username'] = $rawuserinfo->userPrincipalName;
        }
        if (empty($userinfo['email']) || $userinfo['email'] != $rawuserinfo->userPrincipalName) {
            $rawuserinfo->mail = $rawuserinfo->userPrincipalName;
            $userinfo['email'] = $rawuserinfo->userPrincipalName;
        }
        // END LSU userPrincipalName to email mapping.

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
                $failurereason = AUTH_LOGIN_SUSPENDED;
                $event = \core\event\user_login_failed::create([
                    'userid' => $mappeduser->id,
                    'other' => [
                        'username' => $userinfo['username'],
                        'reason' => $failurereason
                    ]
                ]);
                $event->trigger();
                $SESSION->loginerrormsg = get_string('invalidlogin');
                $client->log_out();
                redirect(new moodle_url('/login/index.php'));
            } else if ($mappeduser && ($mappeduser->confirmed || !$issuer->get('requireconfirmation'))) {
                // Update user fields.
                // BEGIN LSU oauth Fixes.
                $userinfo = self::update_userinfo($userinfo, $mappeduser);
                // END LSU oauth Fixes.
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
            // BEGIN LSU oauth fixes.
            $moodleuser = \core_user::get_user_by_username($userinfo['username']);
            if (empty($moodleuser)) {
                $moodleuser = \core_user::get_user_by_email($userinfo['email']);
            }
            // END LSU oauth fixes.

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
                    // BEGIN LSU oauth fixes.
                    $userinfo = self::update_userinfo($userinfo, $moodleuser);
                    // END LSU oauth fixes.
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
        redirect($redirecturl);
    }

    // BEGIN LSU oauth fixes.
    /**
     * Update user data according to data sent by authorization server.
     *
     * @param array $userinfo data from authorization server
     * @param stdClass $moodleuser Current data of the user to be updated
     * @return stdClass The updated user record, or the existing one if there's nothing to be updated.
     */
    public static function update_userinfo($userinfo, $moodleuser) {
        global $DB;
        $table = 'user';
        $userinfo["id"] = $moodleuser->id;
        // Unset the user's first name to keep whatever first name they have in Moodle.
        unset($userinfo['firstname']);

        // Make sure username and email are lowercase.
        if (isset($userinfo->username)) {
            $userinfo->username = trim(core_text::strtolower($userinfo->username));
        }
        if (isset($userinfo->email)) {
            $userinfo->email = trim(core_text::strtolower($userinfo->email));
        }

        // Update the record.
        $update = $DB->update_record($table, $userinfo, $bulk=null);

        // Get the complete record.
        $user = $DB->get_record($table, array("id" => $moodleuser->id));
        return $user;
    }
    // END LSU oauth fixes.

    /**
     * Returns information on how the specified user can change their password.
     * The password of the oauth2 accounts is not stored in Moodle.
     *
     * @param stdClass $user A user object
     * @return string[] An array of strings with keys subject and message
     */
    public function get_password_change_info(stdClass $user) : array {
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
