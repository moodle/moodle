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

namespace tool_policy\external;

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_single_structure;
use core_external\external_multiple_structure;
use core_external\external_value;
use core_external\external_warnings;
use tool_policy\api;
use tool_policy\policy_version;
use core_user;

/**
 * External function for setting user policies acceptances.
 *
 * @package    tool_policy
 * @copyright  2023 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 4.4
 */
class set_acceptances_status extends external_api {

    /**
     * Webservice parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters(
            [
                'policies' => new external_multiple_structure(
                    new external_single_structure([
                        'versionid' => new external_value(PARAM_INT, 'The policy version id.'),
                        'status' => new external_value(PARAM_INT, 'The policy acceptance status. 0: decline, 1: accept.'),
                    ]), 'Policies acceptances for the given user.'
                ),
                'userid' => new external_value(PARAM_INT,
                    'The user id we want to set the acceptances. Default is the current user.', VALUE_DEFAULT, 0
                ),
            ]
        );
    }

    /**
     * Set the acceptance status (accept or decline only) for the indicated policies for the given user.
     *
     * @param array $policies the policies to set the acceptance status
     * @param int $userid the user id we want to retrieve the acceptances
     * @throws \moodle_exception
     * @return array policies and acceptance status
     */
    public static function execute(array $policies, int $userid = 0): array {
        global $USER;

        $params = self::validate_parameters(self::execute_parameters(),
            [
                'policies' => $policies,
                'userid' => $userid,
            ]
        );

        // Do not check for the site policies in validate_context() to avoid the redirect loop.
        if (!defined('NO_SITEPOLICY_CHECK')) {
            define('NO_SITEPOLICY_CHECK', true);
        }

        $systemcontext = \context_system::instance();
        external_api::validate_context($systemcontext);

        if (empty($params['userid']) || $params['userid'] == $USER->id) {
            $user = $USER;
        } else {
            $user = core_user::get_user($params['userid'], '*', MUST_EXIST);
            core_user::require_active_user($user);
        }

        // Split acceptances.
        $allcurrentpolicies = api::list_current_versions(policy_version::AUDIENCE_LOGGEDIN);
        $requestedpolicies = $agreepolicies = $declinepolicies = [];
        foreach ($params['policies'] as $policy) {
            $requestedpolicies[$policy['versionid']] = $policy['status'];
        }

        foreach ($allcurrentpolicies as $policy) {
            if (isset($requestedpolicies[$policy->id])) {
                if ($requestedpolicies[$policy->id] === 1) {
                    $agreepolicies[] = $policy->id;
                } else if ($requestedpolicies[$policy->id] === 0) {
                    $declinepolicies[] = $policy->id;
                }
            }
        }

        // Permissions check.
        api::can_accept_policies($agreepolicies, $user->id, true);
        api::can_decline_policies($declinepolicies, $user->id, true);

        // Good to go.
        api::accept_policies($agreepolicies, $user->id, null);
        api::decline_policies($declinepolicies, $user->id, null);

        $return = [
            'policyagreed' => (int) $user->policyagreed,  // Final policy agreement status for $user.
            'warnings' => [],
        ];
        return $return;
    }

    /**
     * Webservice returns.
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'policyagreed' => new external_value(PARAM_INT,
                'Whether the user has provided acceptance to all current site policies. 1 if yes, 0 if not'),
            'warnings'  => new external_warnings(),
        ]);
    }
}
