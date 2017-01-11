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
}
