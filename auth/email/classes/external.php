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
     * @return external_external_function_parameters
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
        if (!empty($CFG->sitepolicy)) {
            $result['sitepolicy'] = $CFG->sitepolicy;
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
            require_once($CFG->libdir . '/recaptchalib.php');
            // We return the public key, maybe we want to use the javascript api to get the image.
            $result['recaptchapublickey'] = $CFG->recaptchapublickey;
            list($result['recaptchachallengehash'], $result['recaptchachallengeimage'], $result['recaptchachallengejs']) =
                recaptcha_get_challenge_hash_and_urls(RECAPTCHA_API_SECURE_SERVER, $CFG->recaptchapublickey);
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
                'sitepolicy' => new external_value(PARAM_URL, 'Site policy url', VALUE_OPTIONAL),
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
                'recaptchachallengeimage' => new external_value(PARAM_URL, 'Recaptcha challenge <noscript> image', VALUE_OPTIONAL),
                'recaptchachallengejs' => new external_value(PARAM_URL, 'Recaptcha challenge js url', VALUE_OPTIONAL),
                'warnings'  => new external_warnings(),
            )
        );
    }
}
