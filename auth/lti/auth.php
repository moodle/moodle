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

use auth_lti\local\ltiadvantage\entity\user_migration_claim;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/authlib.php');
require_once($CFG->libdir.'/accesslib.php');

/**
 * LTI Authentication plugin.
 *
 * @package auth_lti
 * @copyright 2016 Mark Nelson <markn@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class auth_plugin_lti extends \auth_plugin_base {

    /**
     * @var int constant representing the automatic account provisioning mode.
     * On first launch, for a previously unbound user, this mode dictates that a new Moodle account will be created automatically
     * for the user and bound to their platform credentials {iss, sub}.
     */
    public const PROVISIONING_MODE_AUTO_ONLY = 1;

    /**
     * @var int constant representing the prompt for new or existing provisioning mode.
     * On first launch, for a previously unbound user, the mode dictates that the launch user will be presented with an options
     * view, allowing them to select either 'link an existing account' or 'create a new account for me'.
     */
    public const PROVISIONING_MODE_PROMPT_NEW_EXISTING = 2;

    /**
     * @var int constant representing the prompt for existing only provisioning mode.
     * On first launch, for a previously unbound user, the mode dictates that the launch user will be presented with a view allowing
     * them to link an existing account only. This is useful for situations like deep linking, where an existing account is needed.
     */
    public const PROVISIONING_MODE_PROMPT_EXISTING_ONLY = 3;

    /**
     * Constructor.
     */
    public function __construct() {
        $this->authtype = 'lti';
    }

    /**
     * Users can not log in via the traditional login form.
     *
     * @param string $username The username
     * @param string $password The password
     * @return bool Authentication success or failure
     */
    public function user_login($username, $password) {
        return false;
    }

    /**
     * Authenticate the user based on the unique {iss, sub} tuple present in the OIDC JWT.
     *
     * This method ensures a Moodle user account has been found or is created, that the user is linked to the relevant
     * LTI Advantage credentials (iss, sub) and that the user account is logged in.
     *
     * Launch code can therefore rely on this method to get a session before doing things like calling require_login().
     *
     * This method supports two workflows:
     * 1. Automatic account provisioning - where the complete_login() call will ALWAYS create/find a user and return to
     * calling code directly. No user interaction is required.
     *
     * 2. Manual account provisioning - where the complete_login() call will redirect ONLY ONCE to a login page,
     * where the user can decide whether to use an automatically provisioned account, or bind an existing user account.
     * When the decision has been made, the relevant account is bound and the user is redirected back to $returnurl.
     * Once an account has been bound via this selection process, subsequent calls to complete_login() will return to
     * calling code directly. Any calling code must provide its $returnurl to support the return from the account
     * selection process and must also take care to cache any JWT data appropriately, since the return will not inlude
     * that information.
     *
     * Which workflow is chosen depends on the roles present in the JWT.
     * For teachers/admins, manual provisioning will take place. These user type are likely to have existing accounts.
     * For learners, automatic provisioning will take place.
     *
     * Migration of legacy users is supported, however, only for the Learner role (automatic provisioning). Admins and
     * teachers are likely to have existing accounts and we want them to be able to select and bind these, rather than
     * binding an automatically provisioned legacy account which doesn't represent their real user account.
     *
     * The JWT data must be verified elsewhere. The code here assumes its integrity/authenticity.
     *
     * @param array $launchdata the JWT data provided in the link launch.
     * @param moodle_url $returnurl the local URL to return to if authentication workflows are required.
     * @param int $provisioningmode the desired account provisioning mode, which controls the auth flow for unbound users.
     * @param array $legacyconsumersecrets an array of secrets used by the legacy consumer if a migration claim exists.
     * @throws coding_exception if the specified provisioning mode is invalid.
     */
    public function complete_login(array $launchdata, moodle_url $returnurl, int $provisioningmode,
            array $legacyconsumersecrets = []): void {

        // The platform user is already linked with a user account.
        if ($this->get_user_binding($launchdata['iss'], $launchdata['sub'])) {
            // Always sync the PII, regardless of whether we're already authenticated as this user or not.
            $user = $this->find_or_create_user_from_launch($launchdata, true);

            if (isloggedin()) {
                // If a different user is currently logged in, authenticate the linked user instead.
                global $USER;
                if ((int) $USER->id !== $user->id) {
                    complete_user_login($user);
                }
                // If the linked user is already logged in, skip the call to complete_user_login() because this affects deep linking
                // workflows on sites publishing and consuming resources on the same site, due to the regenerated sesskey.
                return;
            } else {
                complete_user_login($user);
                return;
            }
        }

        // The platform user is not bound to a user account, check provisioning mode now.
        if (!$this->is_valid_provisioning_mode($provisioningmode)) {
            throw new coding_exception('Invalid account provisioning mode provided to complete_login().');
        }

        switch ($provisioningmode) {
            case self::PROVISIONING_MODE_AUTO_ONLY:
                // Automatic provisioning - this will create/migrate a user account and log the user in.
                complete_user_login($this->find_or_create_user_from_launch($launchdata, true, $legacyconsumersecrets));
                break;
            case self::PROVISIONING_MODE_PROMPT_NEW_EXISTING:
            case self::PROVISIONING_MODE_PROMPT_EXISTING_ONLY:
            default:
                // Allow linking an existing account or creation of a new account via an intermediate account options page.
                // Cache the relevant data and take the user to the account options page.
                // Note: This mode also depends on the global auth config 'authpreventaccountcreation'. If set, only existing
                // accounts can be bound in this provisioning mode.
                global $SESSION;
                $SESSION->auth_lti = (object)[
                    'launchdata' => $launchdata,
                    'returnurl' => $returnurl,
                    'provisioningmode' => $provisioningmode
                ];
                redirect(new moodle_url('/auth/lti/login.php', [
                    'sesskey' => sesskey(),
                ]));
                break;
        }
    }

    /**
     * Get a Moodle user account for the LTI user based on the user details returned by a NRPS 2 membership call.
     *
     * This method expects a single member structure, in array format, as defined here:
     * See: https://www.imsglobal.org/spec/lti-nrps/v2p0#membership-container-media-type.
     *
     * This method supports migration of user accounts used in legacy launches, provided the legacy consumerkey corresponding to
     * to the legacy consumer is provided. Calling code will have verified the migration claim during initial launches and should
     * have the consumer key mapped to the deployment, ready to pass in.
     *
     * @param array $member the member data, in array format.
     * @param string $iss the issuer to which the member relates.
     * @param string $legacyconsumerkey optional consumer key mapped to the deployment to facilitate user migration.
     * @return stdClass a Moodle user record.
     */
    public function find_or_create_user_from_membership(array $member, string $iss,
            string $legacyconsumerkey = ''): stdClass {

        // Picture is not synced with membership-based auths because sync tasks may wish to perform slow operations like this a
        // different way.
        unset($member['picture']);

        if ($binduser = $this->get_user_binding($iss, $member['user_id'])) {
            $user = \core_user::get_user((int) $binduser);
            $this->update_user_account($user, $member, $iss);
            return \core_user::get_user($user->id);
        } else {
            if (!empty($legacyconsumerkey)) {
                // Consumer key is required to attempt user migration because legacy users were identified by a
                // username consisting of the consumer key and user id.
                // See the legacy \enrol_lti\helper::create_username() for details.
                $legacyuserid = $member['lti11_legacy_user_id'] ?? $member['user_id'];
                $username = 'enrol_lti' .
                    sha1($legacyconsumerkey . '::' . $legacyconsumerkey . ':' . $legacyuserid);
                if ($user = \core_user::get_user_by_username($username)) {
                    $this->create_user_binding($iss, $member['user_id'], $user->id);
                    $this->update_user_account($user, $member, $iss);
                    return \core_user::get_user($user->id);
                }
            }
            $user = $this->create_new_account($member, $iss);
            $this->update_user_account($user, $member, $iss);
            return \core_user::get_user($user->id);
        }
    }

    /**
     * Get a Moodle user account for the LTI user corresponding to the user defined in a link launch.
     *
     * This method supports migration of user accounts used in legacy launches, provided the legacy consumer secrets corresponding
     * to the legacy consumer are provided. If calling code wishes migration to be role-specific, it should check roles accordingly
     * itself and pass relevant data in - as auth_plugin_lti::complete_login() does.
     *
     * @param array $launchdata all data in the decoded JWT including iss and sub.
     * @param bool $syncpicture whether to sync the user's picture with the picture sent in the launch.
     * @param array $legacyconsumersecrets all secrets found for the legacy consumer, facilitating user migration.
     * @return stdClass the Moodle user who is mapped to the platform user identified in the JWT data.
     */
    public function find_or_create_user_from_launch(array $launchdata, bool $syncpicture = false,
            array $legacyconsumersecrets = []): stdClass {

        if (!$syncpicture) {
            unset($launchdata['picture']);
        }

        if ($binduser = $this->get_user_binding($launchdata['iss'], $launchdata['sub'])) {
            $user = \core_user::get_user((int) $binduser);
            $this->update_user_account($user, $launchdata, $launchdata['iss']);
            return \core_user::get_user($user->id);
        } else {
            // Is the intent to migrate a user account used in legacy launches?
            if (!empty($legacyconsumersecrets)) {
                try {
                    // Validate the migration claim and try to find a legacy user.
                    $usermigrationclaim = new user_migration_claim($launchdata, $legacyconsumersecrets);
                    $username = 'enrol_lti' .
                        sha1($usermigrationclaim->get_consumer_key() . '::' .
                        $usermigrationclaim->get_consumer_key() .':' .$usermigrationclaim->get_user_id());
                    if ($user = \core_user::get_user_by_username($username)) {
                        $this->create_user_binding($launchdata['iss'], $launchdata['sub'], $user->id);
                        $this->update_user_account($user, $launchdata, $launchdata['iss']);
                        return \core_user::get_user($user->id);
                    }
                } catch (Exception $e) {
                    // There was an issue validating the user migration claim. We don't want to fail auth entirely though.
                    // Rather, we want to fall back to new account creation and log the attempt.
                    debugging("There was a problem migrating the LTI user '{$launchdata['sub']}' on the platform " .
                        "'{$launchdata['iss']}'. The migration claim could not be validated. A new account will be created.");
                }
            }
            $user = $this->create_new_account($launchdata, $launchdata['iss']);
            $this->update_user_account($user, $launchdata, $launchdata['iss']);
            return \core_user::get_user($user->id);
        }
    }

    /**
     * Create a binding between the LTI user, as identified by {iss, sub} tuple and the user id.
     *
     * @param string $iss the issuer URL identifying the platform to which to user belongs.
     * @param string $sub the sub string identifying the user on the platform.
     * @param int $userid the id of the Moodle user account to bind.
     */
    public function create_user_binding(string $iss, string $sub, int $userid): void {
        global $DB;

        $timenow = time();
        $issuer256 = hash('sha256', $iss);
        $sub256 = hash('sha256', $sub);
        if ($DB->record_exists('auth_lti_linked_login', ['issuer256' => $issuer256, 'sub256' => $sub256])) {
            return;
        }
        $rec = [
            'userid' => $userid,
            'issuer' => $iss,
            'issuer256' => $issuer256,
            'sub' => $sub,
            'sub256' => $sub256,
            'timecreated' => $timenow,
            'timemodified' => $timenow
        ];
        $DB->insert_record('auth_lti_linked_login', $rec);
    }

    /**
     * Gets the id of the linked Moodle user account for an LTI user, or null if not found.
     *
     * @param string $issuer the issuer to which the user belongs.
     * @param string $sub the sub string identifying the user on the issuer.
     * @return int|null the id of the corresponding Moodle user record, or null if not found.
     */
    public function get_user_binding(string $issuer, string $sub): ?int {
        global $DB;
        $issuer256 = hash('sha256', $issuer);
        $sub256 = hash('sha256', $sub);
        try {
            $binduser = $DB->get_field('auth_lti_linked_login', 'userid',
                ['issuer256' => $issuer256, 'sub256' => $sub256], MUST_EXIST);
        } catch (\dml_exception $e) {
            $binduser = null;
        }
        return $binduser;
    }

    /**
     * Check whether a provisioning mode is valid or not.
     *
     * @param int $mode the mode
     * @return bool true if valid for use, false otherwise.
     */
    protected function is_valid_provisioning_mode(int $mode): bool {
        $validmodes = [
            self::PROVISIONING_MODE_AUTO_ONLY,
            self::PROVISIONING_MODE_PROMPT_NEW_EXISTING,
            self::PROVISIONING_MODE_PROMPT_EXISTING_ONLY
        ];
        return in_array($mode, $validmodes);
    }

    /**
     * Create a new user account based on the user data either in the launch JWT or from a membership call.
     *
     * @param array $userdata the user data coming from either a launch or membership service call.
     * @param string $iss the issuer to which the user belongs.
     * @return stdClass a complete Moodle user record.
     */
    protected function create_new_account(array $userdata, string $iss): stdClass {

        global $CFG;
        require_once($CFG->dirroot.'/user/lib.php');

        // Launches and membership calls handle the user id differently.
        // Launch uses 'sub', whereas member uses 'user_id'.
        $userid = !empty($userdata['sub']) ? $userdata['sub'] : $userdata['user_id'];

        $user = new stdClass();
        $user->username = 'enrol_lti_13_' . sha1($iss . '_' . $userid);
        // If the email was stripped/not set then fill it with a default one.
        // This stops the user from being redirected to edit their profile page.
        $email = !empty($userdata['email']) ? $userdata['email'] :
            'enrol_lti_13_' . sha1($iss . '_' . $userid) . "@example.com";
        $email = \core_user::clean_field($email, 'email');
        $user->email = $email;
        $user->auth = 'lti';
        $user->mnethostid = $CFG->mnet_localhost_id;
        $user->firstname = $userdata['given_name'] ?? $userid;
        $user->lastname = $userdata['family_name'] ?? $iss;
        $user->password = '';
        $user->confirmed = 1;
        $user->id = user_create_user($user, false);

        // Link this user with the LTI credentials for future use.
        $this->create_user_binding($iss, $userid, $user->id);

        return (object) get_complete_user_data('id', $user->id);
    }

    /**
     * Update the personal fields of the user account, based on data present in either a launch of member sync call.
     *
     * @param stdClass $user the Moodle user account to update.
     * @param array $userdata the user data coming from either a launch or membership service call.
     * @param string $iss the issuer to which the user belongs.
     */
    protected function update_user_account(stdClass $user, array $userdata, string $iss): void {
        global $CFG;
        require_once($CFG->dirroot.'/user/lib.php');
        if ($user->auth !== 'lti') {
            return;
        }

        // Launches and membership calls handle the user id differently.
        // Launch uses 'sub', whereas member uses 'user_id'.
        $platformuserid = !empty($userdata['sub']) ? $userdata['sub'] : $userdata['user_id'];
        $email = !empty($userdata['email']) ? $userdata['email'] :
            'enrol_lti_13_' . sha1($iss . '_' . $platformuserid) . "@example.com";
        $email = \core_user::clean_field($email, 'email');
        $update = [
            'id' => $user->id,
            'firstname' => $userdata['given_name'] ?? $platformuserid,
            'lastname' => $userdata['family_name'] ?? $iss,
            'email' => $email
        ];
        user_update_user($update);

        if (!empty($userdata['picture'])) {
            try {
                $this->update_user_picture($user->id, $userdata['picture']);
            } catch (Exception $e) {
                debugging("Error syncing the profile picture for user '$user->id' during LTI authentication.");
            }
        }
    }

    /**
     * Update the user's picture with the image stored at $url.
     *
     * @param int $userid the id of the user to update.
     * @param string $url the string URL where the new image can be found.
     * @throws moodle_exception if there were any problems updating the picture.
     */
    protected function update_user_picture(int $userid, string $url): void {
        global $CFG, $DB;

        require_once($CFG->libdir . '/filelib.php');
        require_once($CFG->libdir . '/gdlib.php');

        $fs = get_file_storage();

        $context = \context_user::instance($userid, MUST_EXIST);
        $fs->delete_area_files($context->id, 'user', 'newicon');

        $filerecord = array(
            'contextid' => $context->id,
            'component' => 'user',
            'filearea' => 'newicon',
            'itemid' => 0,
            'filepath' => '/'
        );

        $urlparams = array(
            'calctimeout' => false,
            'timeout' => 5,
            'skipcertverify' => true,
            'connecttimeout' => 5
        );

        try {
            $fs->create_file_from_url($filerecord, $url, $urlparams);
        } catch (\file_exception $e) {
            throw new moodle_exception(get_string($e->errorcode, $e->module, $e->a));
        }

        $iconfile = $fs->get_area_files($context->id, 'user', 'newicon', false, 'itemid', false);

        // There should only be one.
        $iconfile = reset($iconfile);

        // Something went wrong while creating temp file - remove the uploaded file.
        if (!$iconfile = $iconfile->copy_content_to_temp()) {
            $fs->delete_area_files($context->id, 'user', 'newicon');
            throw new moodle_exception('There was a problem copying the profile picture to temp.');
        }

        // Copy file to temporary location and the send it for processing icon.
        $newpicture = (int) process_new_icon($context, 'user', 'icon', 0, $iconfile);
        // Delete temporary file.
        @unlink($iconfile);
        // Remove uploaded file.
        $fs->delete_area_files($context->id, 'user', 'newicon');
        // Set the user's picture.
        $DB->set_field('user', 'picture', $newpicture, array('id' => $userid));
    }
}
