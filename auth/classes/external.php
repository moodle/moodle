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
 * Auth external API
 *
 * @package    core_auth
 * @category   external
 * @copyright  2016 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.2
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir . '/externallib.php');
require_once($CFG->libdir . '/authlib.php');

/**
 * Auth external functions
 *
 * @package    core_auth
 * @category   external
 * @copyright  2016 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.2
 */
class core_auth_external extends external_api {

    /**
     * Describes the parameters for confirm_user.
     *
     * @return external_function_parameters
     * @since Moodle 3.2
     */
    public static function confirm_user_parameters() {
        return new external_function_parameters(
            array(
                'username' => new external_value(core_user::get_property_type('username'), 'User name'),
                'secret' => new external_value(core_user::get_property_type('secret'), 'Confirmation secret'),
            )
        );
    }

    /**
     * Confirm a user account.
     *
     * @param  string $username user name
     * @param  string $secret   confirmation secret (random string) used for validating the confirm request
     * @return array warnings and success status (true if the user was confirmed, false if he was already confirmed)
     * @since Moodle 3.2
     * @throws moodle_exception
     */
    public static function confirm_user($username, $secret) {
        global $PAGE;

        $warnings = array();
        $params = self::validate_parameters(
            self::confirm_user_parameters(),
            array(
                'username' => $username,
                'secret' => $secret,
            )
        );

        $context = context_system::instance();
        $PAGE->set_context($context);

        if (!$authplugin = signup_get_user_confirmation_authplugin()) {
            throw new moodle_exception('confirmationnotenabled');
        }

        $confirmed = $authplugin->user_confirm($username, $secret);

        if ($confirmed == AUTH_CONFIRM_ALREADY) {
            $success = false;
            $warnings[] = array(
                'item' => 'user',
                'itemid' => 0,
                'warningcode' => 'alreadyconfirmed',
                'message' => s(get_string('alreadyconfirmed'))
            );
        } else if ($confirmed == AUTH_CONFIRM_OK) {
            $success = true;
        } else {
            throw new moodle_exception('invalidconfirmdata');
        }

        $result = array(
            'success' => $success,
            'warnings' => $warnings,
        );
        return $result;
    }

    /**
     * Describes the confirm_user return value.
     *
     * @return external_single_structure
     * @since Moodle 3.2
     */
    public static function confirm_user_returns() {

        return new external_single_structure(
            array(
                'success' => new external_value(PARAM_BOOL, 'True if the user was confirmed, false if he was already confirmed'),
                'warnings'  => new external_warnings(),
            )
        );
    }

    /**
     * Describes the parameters for request_password_reset.
     *
     * @return external_function_parameters
     * @since Moodle 3.4
     */
    public static function request_password_reset_parameters() {
        return new external_function_parameters(
            array(
                'username' => new external_value(core_user::get_property_type('username'), 'User name', VALUE_DEFAULT, ''),
                'email' => new external_value(core_user::get_property_type('email'), 'User email', VALUE_DEFAULT, ''),
            )
        );
    }

    /**
     * Requests a password reset.
     *
     * @param  string $username user name
     * @param  string $email    user email
     * @return array warnings and success status (including notices and errors while processing)
     * @since Moodle 3.4
     * @throws moodle_exception
     */
    public static function request_password_reset($username = '', $email = '') {
        global $CFG, $PAGE;
        require_once($CFG->dirroot . '/login/lib.php');

        $warnings = array();
        $params = self::validate_parameters(
            self::request_password_reset_parameters(),
            array(
                'username' => $username,
                'email' => $email,
            )
        );

        $context = context_system::instance();
        $PAGE->set_context($context);   // Needed by format_string calls.

        // Check if an alternate forgotten password method is set.
        if (!empty($CFG->forgottenpasswordurl)) {
            throw new moodle_exception('cannotmailconfirm');
        }

        $errors = core_login_validate_forgot_password_data($params);
        if (!empty($errors)) {
            $status = 'dataerror';
            $notice = '';

            foreach ($errors as $itemname => $message) {
                $warnings[] = array(
                    'item' => $itemname,
                    'itemid' => 0,
                    'warningcode' => 'fielderror',
                    'message' => s($message)
                );
            }
        } else {
            list($status, $notice, $url) = core_login_process_password_reset($params['username'], $params['email']);
        }

        return array(
            'status' => $status,
            'notice' => $notice,
            'warnings' => $warnings,
        );
    }

    /**
     * Describes the request_password_reset return value.
     *
     * @return external_single_structure
     * @since Moodle 3.4
     */
    public static function request_password_reset_returns() {

        return new external_single_structure(
            array(
                'status' => new external_value(PARAM_ALPHANUMEXT, 'The returned status of the process:
                    dataerror: Error in the sent data (username or email). More information in warnings field.
                    emailpasswordconfirmmaybesent: Email sent or not (depends on user found in database).
                    emailpasswordconfirmnotsent: Failure, user not found.
                    emailpasswordconfirmnoemail: Failure, email not found.
                    emailalreadysent: Email already sent.
                    emailpasswordconfirmsent: User pending confirmation.
                    emailresetconfirmsent: Email sent.
                '),
                'notice' => new external_value(PARAM_RAW, 'Important information for the user about the process.'),
                'warnings'  => new external_warnings(),
            )
        );
    }

    /**
     * Describes the parameters for the digital minor check.
     *
     * @return external_function_parameters
     * @since Moodle 3.4
     */
    public static function is_minor_parameters() {
        return new external_function_parameters(
            array(
                'age' => new external_value(PARAM_INT, 'Age'),
                'country' => new external_value(PARAM_ALPHA, 'Country of residence'),
            )
        );
    }

    /**
     * Requests a check if a user is digital minor.
     *
     * @param  int $age User age
     * @param  string $country Country of residence
     * @return array status (true if the user is a minor, false otherwise)
     * @since Moodle 3.4
     * @throws moodle_exception
     */
    public static function is_minor($age, $country) {
        global $CFG, $PAGE;
        require_once($CFG->dirroot . '/login/lib.php');

        $params = self::validate_parameters(
            self::is_minor_parameters(),
            array(
                'age' => $age,
                'country' => $country,
            )
        );

        if (!array_key_exists($params['country'], get_string_manager()->get_list_of_countries())) {
            throw new invalid_parameter_exception('Invalid value for country parameter (value: '.
                $params['country'] .')');
        }

        $context = context_system::instance();
        $PAGE->set_context($context);

        // Check if verification of age and location (minor check) is enabled.
        if (!\core_auth\digital_consent::is_age_digital_consent_verification_enabled()) {
            throw new moodle_exception('nopermissions', 'error', '',
                get_string('agelocationverificationdisabled', 'error'));
        }

        $status = \core_auth\digital_consent::is_minor($params['age'], $params['country']);

        return array(
            'status' => $status
        );
    }

    /**
     * Describes the is_minor return value.
     *
     * @return external_single_structure
     * @since Moodle 3.4
     */
    public static function is_minor_returns() {
        return new external_single_structure(
            array(
                'status' => new external_value(PARAM_BOOL, 'True if the user is considered to be a digital minor,
                    false if not')
            )
        );
    }

    /**
     * Describes the parameters for is_age_digital_consent_verification_enabled.
     *
     * @return external_function_parameters
     * @since Moodle 3.3
     */
    public static function is_age_digital_consent_verification_enabled_parameters() {
        return new external_function_parameters(array());
    }

    /**
     * Checks if age digital consent verification is enabled.
     *
     * @return array status (true if digital consent verification is enabled, false otherwise.)
     * @since Moodle 3.3
     * @throws moodle_exception
     */
    public static function is_age_digital_consent_verification_enabled() {
        global $PAGE;

        $context = context_system::instance();
        $PAGE->set_context($context);

        $status = false;
        // Check if verification is enabled.
        if (\core_auth\digital_consent::is_age_digital_consent_verification_enabled()) {
            $status = true;
        }

        return array(
            'status' => $status
        );
    }

    /**
     * Describes the is_age_digital_consent_verification_enabled return value.
     *
     * @return external_single_structure
     * @since Moodle 3.3
     */
    public static function is_age_digital_consent_verification_enabled_returns() {
        return new external_single_structure(
            array(
                'status' => new external_value(PARAM_BOOL, 'True if digital consent verification is enabled,
                    false otherwise.')
            )
        );
    }

    /**
     * Describes the parameters for resend_confirmation_email.
     *
     * @return external_function_parameters
     * @since Moodle 3.6
     */
    public static function resend_confirmation_email_parameters() {
        return new external_function_parameters(
            array(
                'username' => new external_value(core_user::get_property_type('username'), 'Username.'),
                'password' => new external_value(core_user::get_property_type('password'), 'Plain text password.'),
                'redirect' => new external_value(PARAM_LOCALURL, 'Redirect the user to this site url after confirmation.',
                    VALUE_DEFAULT, ''),
            )
        );
    }

    /**
     * Requests resend the confirmation email.
     *
     * @param  string $username user name
     * @param  string $password plain text password
     * @param  string $redirect redirect the user to this site url after confirmation
     * @return array warnings and success status
     * @since Moodle 3.6
     * @throws moodle_exception
     */
    public static function resend_confirmation_email($username, $password, $redirect = '') {
        global $PAGE;

        $warnings = array();
        $params = self::validate_parameters(
            self::resend_confirmation_email_parameters(),
            array(
                'username' => $username,
                'password' => $password,
                'redirect' => $redirect,
            )
        );

        $context = context_system::instance();
        $PAGE->set_context($context);   // Need by internal APIs.
        $username = trim(core_text::strtolower($params['username']));
        $password = $params['password'];

        if (is_restored_user($username)) {
            throw new moodle_exception('restoredaccountresetpassword', 'webservice');
        }

        $user = authenticate_user_login($username, $password);

        if (empty($user)) {
            throw new moodle_exception('invalidlogin');
        }

        if ($user->confirmed) {
            throw new moodle_exception('alreadyconfirmed');
        }

        // Check if we should redirect the user once the user is confirmed.
        $confirmationurl = null;
        if (!empty($params['redirect'])) {
            // Pass via moodle_url to fix thinks like admin links.
            $redirect = new moodle_url($params['redirect']);

            $confirmationurl = new moodle_url('/login/confirm.php', array('redirect' => $redirect->out()));
        }
        $status = send_confirmation_email($user, $confirmationurl);

        return array(
            'status' => $status,
            'warnings' => $warnings,
        );
    }

    /**
     * Describes the resend_confirmation_email return value.
     *
     * @return external_single_structure
     * @since Moodle 3.6
     */
    public static function resend_confirmation_email_returns() {

        return new external_single_structure(
            array(
                'status' => new external_value(PARAM_BOOL, 'True if the confirmation email was sent, false otherwise.'),
                'warnings'  => new external_warnings(),
            )
        );
    }
}
