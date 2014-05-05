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
 * External functions
 *
 * @package    message_airnotifier
 * @category   external
 * @copyright  2012 Jerome Mouneyrac <jerome@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.7
 */

defined('MOODLE_INTERNAL') || die;

require_once("$CFG->libdir/externallib.php");

/**
 * External API for airnotifier web services
 *
 * @package    message_airnotifier
 * @category   external
 * @copyright  2012 Jerome Mouneyrac <jerome@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.7
 */
class message_airnotifier_external extends external_api {

    /**
     * Returns description of method parameters
     *
     * @since Moodle 2.7
     */
    public static function is_system_configured_parameters() {
        return new external_function_parameters(
                array()
        );
    }

    /**
     * Tests whether the airnotifier settings have been configured
     *
     * @since Moodle 2.7
     */
    public static function is_system_configured() {
        global $DB;

        // First, check if the plugin is disabled.
        $processor = $DB->get_record('message_processors', array('name' => 'airnotifier'), '*', MUST_EXIST);
        if (!$processor->enabled) {
            return 0;
        }

        // Then, check if the plugin is completly configured.
        $manager = new message_airnotifier_manager();
        return (int) $manager->is_system_configured();
    }

    /**
     * Returns description of method result value
     *
     * @return external_single_structure
     * @since Moodle 2.7
     */
    public static function is_system_configured_returns() {
        return new external_value( PARAM_INT, '0 if the system is not configured, 1 otherwise');
    }

    /**
     * Returns description of method parameters
     *
     * @since Moodle 2.7
     */
    public static function are_notification_preferences_configured_parameters() {
        return new external_function_parameters(
                array(
                    'userids' => new external_multiple_structure(new external_value(PARAM_INT, 'user ID')),
                )
        );
    }

    /**
     * Check if the users have notification preferences configured for the airnotifier plugin
     *
     * @param array $userids Array of user ids
     * @since Moodle 2.7
     */
    public static function are_notification_preferences_configured($userids) {
        global $CFG, $USER, $DB;

        require_once($CFG->dirroot . '/message/lib.php');
        $params = self::validate_parameters(self::are_notification_preferences_configured_parameters(),
                array('userids' => $userids));

        list($sqluserids, $params) = $DB->get_in_or_equal($params['userids'], SQL_PARAMS_NAMED);
        $uselect = ', ' . context_helper::get_preload_record_columns_sql('ctx');
        $ujoin = "LEFT JOIN {context} ctx ON (ctx.instanceid = u.id AND ctx.contextlevel = :contextlevel)";
        $params['contextlevel'] = CONTEXT_USER;
        $usersql = "SELECT u.* $uselect
                      FROM {user} u $ujoin
                     WHERE u.id $sqluserids";
        $users = $DB->get_recordset_sql($usersql, $params);

        $result = array(
            'users' => array(),
            'warnings' => array()
        );
        $hasuserupdatecap = has_capability('moodle/user:update', context_system::instance());
        foreach ($users as $user) {

            $currentuser = ($user->id == $USER->id);

            if ($currentuser or $hasuserupdatecap) {

                if (!empty($user->deleted)) {
                    $warning = array();
                    $warning['item'] = 'user';
                    $warning['itemid'] = $user->id;
                    $warning['warningcode'] = '1';
                    $warning['message'] = "User $user->id was deleted";
                    $result['warnings'][] = $warning;
                    continue;
                }

                $preferences = array();
                $preferences['userid'] = $user->id;
                $preferences['configured'] = 0;

                // Now we get for all the providers and all the states
                // the user preferences to check if at least one is enabled for airnotifier plugin.
                $providers = message_get_providers_for_user($user->id);
                $configured = false;

                foreach ($providers as $provider) {
                    if ($configured) {
                        break;
                    }

                    foreach (array('loggedin', 'loggedoff') as $state) {

                        $prefstocheck = array();
                        $prefname = 'message_provider_'.$provider->component.'_'.$provider->name.'_'.$state;

                        // First get forced settings.
                        if ($forcedpref = get_config('message', $prefname)) {
                            $prefstocheck = array_merge($prefstocheck, explode(',', $forcedpref));
                        }

                        // Then get user settings.
                        if ($userpref = get_user_preferences($prefname, '', $user->id)) {
                            $prefstocheck = array_merge($prefstocheck, explode(',', $userpref));
                        }

                        if (in_array('airnotifier', $prefstocheck)) {
                            $preferences['configured'] = 1;
                            $configured = true;
                            break;
                        }

                    }
                }

                $result['users'][] = $preferences;
            } else if (!$hasuserupdatecap) {
                $warning = array();
                $warning['item'] = 'user';
                $warning['itemid'] = $user->id;
                $warning['warningcode'] = '2';
                $warning['message'] = "You don't have permissions for view user $user->id preferences";
                $result['warnings'][] = $warning;
            }

        }
        $users->close();

        return $result;
    }

    /**
     * Returns description of method result value
     *
     * @return external_single_structure
     * @since Moodle 2.7
     */
    public static function are_notification_preferences_configured_returns() {
        return new external_single_structure(
            array(
                'users' => new external_multiple_structure(
                    new external_single_structure(
                        array (
                            'userid'     => new external_value(PARAM_INT, 'userid id'),
                            'configured' => new external_value(PARAM_INT,
                                '1 if the user preferences have been configured and 0 if not')
                        )
                    ),
                    'list of preferences by user'),
                'warnings' => new external_warnings()
            )
        );
    }

}
