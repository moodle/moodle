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
 * Auth e-mail external API
 *
 * @package    auth_email
 * @category   external
 * @copyright  2016 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.2
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir . '/externallib.php');
require_once($CFG->libdir . '/authlib.php');
require_once($CFG->dirroot . '/user/editlib.php');
require_once($CFG->dirroot . '/user/profile/lib.php');

/**
 * Auth e-mail external functions
 *
 * @package    auth_email
 * @category   external
 * @copyright  2016 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.2
 */
class auth_email_external extends external_api {

    /**
     * Check if registration is enabled in this site.
     *
     * @throws moodle_exception
     * @since Moodle 3.2
     */
    protected static function check_signup_enabled() {
        global $CFG;

        if (empty($CFG->registerauth) or $CFG->registerauth != 'email') {
            throw new moodle_exception('registrationdisabled', 'error');
        }
    }

    /**
     * Describes the parameters for get_signup_settings.
     *
     * @return external_function_parameters
     * @since Moodle 3.2
     */
    public static function get_signup_settings_parameters() {
        return new external_function_parameters(array());
    }

    /**
     * Get the signup required settings and profile fields.
     *
     * @return array settings and possible warnings
     * @since Moodle 3.2
     * @throws moodle_exception
     */
    public static function get_signup_settings() {
        global $CFG, $PAGE;

        $context = context_system::instance();
        // We need this to make work the format text functions.
        $PAGE->set_context($context);

        self::check_signup_enabled();

        $result = array();
        $result['namefields'] = useredit_get_required_name_fields();

        if (!empty($CFG->passwordpolicy)) {
            $result['passwordpolicy'] = print_password_policy();
        }
        $manager = new \core_privacy\local\sitepolicy\manager();
        if ($sitepolicy = $manager->get_embed_url()) {
            $result['sitepolicy'] = $sitepolicy->out(false);
        }
        if (!empty($CFG->sitepolicyhandler)) {
            $result['sitepolicyhandler'] = $CFG->sitepolicyhandler;
        }
        if (!empty($CFG->defaultcity)) {
            $result['defaultcity'] = $CFG->defaultcity;
        }
        if (!empty($CFG->country)) {
            $result['country'] = $CFG->country;
        }

        if ($fields = profile_get_signup_fields()) {
            $result['profilefields'] = array();
            foreach ($fields as $field) {
                $fielddata = $field->object->get_field_config_for_external();
                $fielddata['categoryname'] = external_format_string($field->categoryname, $context->id);
                $fielddata['name'] = external_format_string($fielddata['name'], $context->id);
                list($fielddata['defaultdata'], $fielddata['defaultdataformat']) =
                    external_format_text($fielddata['defaultdata'], $fielddata['defaultdataformat'], $context->id);

                $result['profilefields'][] = $fielddata;
            }
        }

        if (signup_captcha_enabled()) {
            // With reCAPTCHA v2 the captcha will be rendered by the mobile client using just the publickey.
            // For now include placeholders for the v1 paramaters to support older mobile app versions.
            $result['recaptchapublickey'] = $CFG->recaptchapublickey;
            list($result['recaptchachallengehash'], $result['recaptchachallengeimage'], $result['recaptchachallengejs']) =
                array('', '', '');
        }

        $result['warnings'] = array();
        return $result;
    }

    /**
     * Describes the get_signup_settings return value.
     *
     * @return external_single_structure
     * @since Moodle 3.2
     */
    public static function get_signup_settings_returns() {

        return new external_single_structure(
            array(
                'namefields' => new external_multiple_structure(
                     new external_value(PARAM_NOTAGS, 'The order of the name fields')
                ),
                'passwordpolicy' => new external_value(PARAM_RAW, 'Password policy', VALUE_OPTIONAL),
                'sitepolicy' => new external_value(PARAM_RAW, 'Site policy', VALUE_OPTIONAL),
                'sitepolicyhandler' => new external_value(PARAM_PLUGIN, 'Site policy handler', VALUE_OPTIONAL),
                'defaultcity' => new external_value(PARAM_NOTAGS, 'Default city', VALUE_OPTIONAL),
                'country' => new external_value(PARAM_ALPHA, 'Default country', VALUE_OPTIONAL),
                'profilefields' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'id' => new external_value(PARAM_INT, 'Profile field id', VALUE_OPTIONAL),
                            'shortname' => new external_value(PARAM_ALPHANUM, 'Password policy', VALUE_OPTIONAL),
                            'name' => new external_value(PARAM_TEXT, 'Profield field name', VALUE_OPTIONAL),
                            'datatype' => new external_value(PARAM_ALPHANUMEXT, 'Profield field datatype', VALUE_OPTIONAL),
                            'description' => new external_value(PARAM_RAW, 'Profield field description', VALUE_OPTIONAL),
                            'descriptionformat' => new external_format_value('description'),
                            'categoryid' => new external_value(PARAM_INT, 'Profield field category id', VALUE_OPTIONAL),
                            'categoryname' => new external_value(PARAM_TEXT, 'Profield field category name', VALUE_OPTIONAL),
                            'sortorder' => new external_value(PARAM_INT, 'Profield field sort order', VALUE_OPTIONAL),
                            'required' => new external_value(PARAM_INT, 'Profield field required', VALUE_OPTIONAL),
                            'locked' => new external_value(PARAM_INT, 'Profield field locked', VALUE_OPTIONAL),
                            'visible' => new external_value(PARAM_INT, 'Profield field visible', VALUE_OPTIONAL),
                            'forceunique' => new external_value(PARAM_INT, 'Profield field unique', VALUE_OPTIONAL),
                            'signup' => new external_value(PARAM_INT, 'Profield field in signup form', VALUE_OPTIONAL),
                            'defaultdata' => new external_value(PARAM_RAW, 'Profield field default data', VALUE_OPTIONAL),
                            'defaultdataformat' => new external_format_value('defaultdata'),
                            'param1' => new external_value(PARAM_RAW, 'Profield field settings', VALUE_OPTIONAL),
                            'param2' => new external_value(PARAM_RAW, 'Profield field settings', VALUE_OPTIONAL),
                            'param3' => new external_value(PARAM_RAW, 'Profield field settings', VALUE_OPTIONAL),
                            'param4' => new external_value(PARAM_RAW, 'Profield field settings', VALUE_OPTIONAL),
                            'param5' => new external_value(PARAM_RAW, 'Profield field settings', VALUE_OPTIONAL),
                        )
                    ), 'Required profile fields', VALUE_OPTIONAL
                ),
                'recaptchapublickey' => new external_value(PARAM_RAW, 'Recaptcha public key', VALUE_OPTIONAL),
                'recaptchachallengehash' => new external_value(PARAM_RAW, 'Recaptcha challenge hash', VALUE_OPTIONAL),
                'recaptchachallengeimage' => new external_value(PARAM_URL, 'Recaptcha challenge noscript image', VALUE_OPTIONAL),
                'recaptchachallengejs' => new external_value(PARAM_URL, 'Recaptcha challenge js url', VALUE_OPTIONAL),
                'warnings'  => new external_warnings(),
            )
        );
    }

    /**
     * Describes the parameters for signup_user.
     *
     * @return external_function_parameters
     * @since Moodle 3.2
     */
    public static function signup_user_parameters() {
        return new external_function_parameters(
            array(
                'username' => new external_value(core_user::get_property_type('username'), 'Username'),
                'password' => new external_value(core_user::get_property_type('password'), 'Plain text password'),
                'firstname' => new external_value(core_user::get_property_type('firstname'), 'The first name(s) of the user'),
                'lastname' => new external_value(core_user::get_property_type('lastname'), 'The family name of the user'),
                'email' => new external_value(core_user::get_property_type('email'), 'A valid and unique email address'),
                'city' => new external_value(core_user::get_property_type('city'), 'Home city of the user', VALUE_DEFAULT, ''),
                'country' => new external_value(core_user::get_property_type('country'), 'Home country code', VALUE_DEFAULT, ''),
                'recaptchachallengehash' => new external_value(PARAM_RAW, 'Recaptcha challenge hash', VALUE_DEFAULT, ''),
                'recaptcharesponse' => new external_value(PARAM_NOTAGS, 'Recaptcha response', VALUE_DEFAULT, ''),
                'customprofilefields' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'type'  => new external_value(PARAM_ALPHANUMEXT, 'The type of the custom field'),
                            'name'  => new external_value(PARAM_ALPHANUMEXT, 'The name of the custom field'),
                            'value' => new external_value(PARAM_RAW, 'Custom field value, can be an encoded json if required')
                        )
                    ), 'User custom fields (also known as user profile fields)', VALUE_DEFAULT, array()
                ),
                'redirect' => new external_value(PARAM_LOCALURL, 'Redirect the user to this site url after confirmation.',
                                                    VALUE_DEFAULT, ''),
            )
        );
    }

    /**
     * Get the signup required settings and profile fields.
     *
     * @param  string $username               username
     * @param  string $password               plain text password
     * @param  string $firstname              the first name(s) of the user
     * @param  string $lastname               the family name of the user
     * @param  string $email                  a valid and unique email address
     * @param  string $city                   home city of the user
     * @param  string $country                home country code
     * @param  string $recaptchachallengehash recaptcha challenge hash
     * @param  string $recaptcharesponse      recaptcha response
     * @param  array  $customprofilefields    user custom fields (also known as user profile fields)
     * @param  string $redirect               Site url to redirect the user after confirmation
     * @return array settings and possible warnings
     * @since Moodle 3.2
     * @throws moodle_exception
     * @throws invalid_parameter_exception
     */
    public static function signup_user($username, $password, $firstname, $lastname, $email, $city = '', $country = '',
                                        $recaptchachallengehash = '', $recaptcharesponse = '', $customprofilefields = array(),
                                        $redirect = '') {
        global $CFG, $PAGE;

        $warnings = array();
        $params = self::validate_parameters(
            self::signup_user_parameters(),
            array(
                'username' => $username,
                'password' => $password,
                'firstname' => $firstname,
                'lastname' => $lastname,
                'email' => $email,
                'city' => $city,
                'country' => $country,
                'recaptchachallengehash' => $recaptchachallengehash,
                'recaptcharesponse' => $recaptcharesponse,
                'customprofilefields' => $customprofilefields,
                'redirect' => $redirect,
            )
        );

        // We need this to make work the format text functions.
        $context = context_system::instance();
        $PAGE->set_context($context);

        self::check_signup_enabled();

        // Validate profile fields param types.
        $allowedfields = profile_get_signup_fields();
        $fieldproperties = array();
        $fieldsrequired = array();
        foreach ($allowedfields as $field) {
            $fieldproperties[$field->object->inputname] = $field->object->get_field_properties();
            if ($field->object->is_required()) {
                $fieldsrequired[$field->object->inputname] = true;
            }
        }

        foreach ($params['customprofilefields'] as $profilefield) {
            if (!array_key_exists($profilefield['name'], $fieldproperties)) {
                throw new invalid_parameter_exception('Invalid field' . $profilefield['name']);
            }
            list($type, $allownull) = $fieldproperties[$profilefield['name']];
            validate_param($profilefield['value'], $type, $allownull);
            // Remove from the potential required list.
            if (isset($fieldsrequired[$profilefield['name']])) {
                unset($fieldsrequired[$profilefield['name']]);
            }
        }
        if (!empty($fieldsrequired)) {
            throw new invalid_parameter_exception('Missing required parameters: ' . implode(',', array_keys($fieldsrequired)));
        }

        // Validate the data sent.
        $data = $params;
        $data['email2'] = $data['email'];
        // Force policy agreed if a site policy is set. The client is responsible of implementing the interface check.
        $manager = new \core_privacy\local\sitepolicy\manager();
        if ($manager->is_defined()) {
            $data['policyagreed'] = 1;
        }
        unset($data['recaptcharesponse']);
        unset($data['customprofilefields']);
        // Add profile fields data.
        foreach ($params['customprofilefields'] as $profilefield) {
            // First, check if the value is a json (some profile fields like text area uses an array for sending data).
            $datadecoded = json_decode($profilefield['value'], true);
            if (is_array($datadecoded) && (json_last_error() == JSON_ERROR_NONE)) {
                $data[$profilefield['name']] = $datadecoded;
            } else {
                $data[$profilefield['name']] = $profilefield['value'];
            }
        }

        $errors = signup_validate_data($data, array());

        // Validate recaptcha.
        if (signup_captcha_enabled()) {
            require_once($CFG->libdir . '/recaptchalib_v2.php');
            $response = recaptcha_check_response(RECAPTCHA_VERIFY_URL, $CFG->recaptchaprivatekey,
                                                 getremoteaddr(), $params['recaptcharesponse']);
            if (!$response['isvalid']) {
                $errors['recaptcharesponse'] = $response['error'];
            }
        }

        if (!empty($errors)) {
            foreach ($errors as $itemname => $message) {
                $warnings[] = array(
                    'item' => $itemname,
                    'itemid' => 0,
                    'warningcode' => 'fielderror',
                    'message' => s($message)
                );
            }
            $result = array(
                'success' => false,
                'warnings' => $warnings,
            );
        } else {
            // Save the user.
            $user = signup_setup_new_user((object) $data);

            $authplugin = get_auth_plugin('email');

            // Check if we should redirect the user once the user is confirmed.
            $confirmationurl = null;
            if (!empty($params['redirect'])) {
                // Pass via moodle_url to fix thinks like admin links.
                $redirect = new moodle_url($params['redirect']);

                $confirmationurl = new moodle_url('/login/confirm.php', array('redirect' => $redirect->out()));
            }
            $authplugin->user_signup_with_confirmation($user, false, $confirmationurl);

            $result = array(
                'success' => true,
                'warnings' => array(),
            );
        }
        return $result;
    }

    /**
     * Describes the signup_user return value.
     *
     * @return external_single_structure
     * @since Moodle 3.2
     */
    public static function signup_user_returns() {

        return new external_single_structure(
            array(
                'success' => new external_value(PARAM_BOOL, 'True if the user was created false otherwise'),
                'warnings'  => new external_warnings(),
            )
        );
    }
}
