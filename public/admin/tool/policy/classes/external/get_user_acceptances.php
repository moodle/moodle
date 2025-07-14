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
use core_external\external_format_value;
use core_external\external_value;
use core_external\external_warnings;
use tool_policy\api;
use context_user;
use core_user;
use core_external\util;

/**
 * External function for retrieving user policies acceptances.
 *
 * @package    tool_policy
 * @copyright  2023 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 4.4
 */
class get_user_acceptances extends external_api {

    /**
     * Webservice parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters(
            [
                'userid' => new external_value(PARAM_INT, 'The user id we want to retrieve the acceptances.',
                    VALUE_DEFAULT, 0),
            ]
        );
    }

    /**
     * Returns the acceptance status for all the policies the given user can see.
     *
     * @param int $userid the user id we want to retrieve the acceptances
     * @throws \required_capability_exception
     * @return array policies and acceptance status
     */
    public static function execute(int $userid = 0): array {
        global $USER;

        $params = self::validate_parameters(self::execute_parameters(),
            [
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
            $usercontext = context_user::instance($user->id);
            // Check capability to view acceptances. No capability is needed to view your own acceptances.
            if (!has_capability('tool/policy:acceptbehalf', $usercontext)) {
                require_capability('tool/policy:viewacceptances', $usercontext);
            }
        }

        $canviewfullnames = has_capability('moodle/site:viewfullnames', $systemcontext);
        $userpolicies = api::get_policies_with_acceptances($user->id);

        $policies = [];
        foreach ($userpolicies as $userpolicy) {
            foreach ($userpolicy->versions as $version) {

                $policy = (array) clone $version;
                unset($policy['acceptance']); // This might return NULL and break the WS response.
                $policy['versionid'] = $version->id;
                $policy['name'] = util::format_string($version->name, $systemcontext);
                $policy['revision'] = util::format_string($version->revision, $systemcontext);
                [$policy['summary'], $policy['summaryformat']] = util::format_text($version->summary,
                    $version->summaryformat, $systemcontext);
                [$policy['content'], $policy['contentformat']] = util::format_text($version->content,
                    $version->contentformat, $systemcontext);

                if (!empty($version->acceptance)) {
                    $policy['acceptance'] = (array) $version->acceptance;
                    if ($version->acceptance->usermodified && $version->acceptance->usermodified != $user->id) {
                        // Get the full name of who accepted on behalf.
                        $usermodified = (object)['id' => $version->acceptance->usermodified];
                        username_load_fields_from_object($usermodified, $version->acceptance, 'mod');
                        $override = $canviewfullnames || has_capability('moodle/site:viewfullnames', context_user::instance($version->acceptance->usermodified));
                        $policy['acceptance']['modfullname'] = fullname($usermodified, $override);
                    }
                    if (!empty($version->acceptance->note)) {
                        [$policy['acceptance']['note']] = util::format_text($version->acceptance->note, FORMAT_MOODLE, $systemcontext);
                    }
                }
                // Return permission for actions for the current policy and user.
                $policy['canaccept'] = api::can_accept_policies([$version->id], $user->id);
                $policy['candecline'] = api::can_decline_policies([$version->id], $user->id);
                $policy['canrevoke'] = api::can_revoke_policies([$version->id], $user->id);

                $policies[] = $policy;
            }
        }

        $return = [
            'policies' => $policies,
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
            'policies' => new external_multiple_structure(
                new external_single_structure([
                    'policyid' => new external_value(PARAM_INT, 'The policy id.'),
                    'versionid' => new external_value(PARAM_INT, 'The policy version id.'),
                    'agreementstyle' => new external_value(PARAM_INT, 'The policy agreement style. 0: consent page, 1: own page.'),
                    'optional' => new external_value(PARAM_INT, 'Whether the policy is optional. 0: compulsory, 1: optional'),
                    'revision' => new external_value(PARAM_TEXT, 'The policy revision.'),
                    'status' => new external_value(PARAM_INT, 'The policy status. 0: draft, 1: active, 2: archived.'),
                    'name' => new external_value(PARAM_TEXT, 'The policy name'),
                    'summary' => new external_value(PARAM_RAW, 'The policy summary.', VALUE_OPTIONAL),
                    'summaryformat' => new external_format_value('summary'),
                    'content' => new external_value(PARAM_RAW, 'The policy content.', VALUE_OPTIONAL),
                    'contentformat' => new external_format_value('content'),
                    'acceptance' => new external_single_structure([
                        'status' => new external_value(PARAM_INT, 'The acceptance status. 0: declined, 1: accepted.'),
                        'lang' => new external_value(PARAM_LANG, 'The policy lang.'),
                        'timemodified' => new external_value(PARAM_INT, 'The time the acceptance was set.'),
                        'usermodified' => new external_value(PARAM_INT, 'The user who accepted.'),
                        'note' => new external_value(PARAM_TEXT, 'The policy note/remarks.', VALUE_OPTIONAL),
                        'modfullname' => new external_value(PARAM_NOTAGS, 'The fullname who accepted on behalf.', VALUE_OPTIONAL),
                    ], 'Acceptance status for the given user.', VALUE_OPTIONAL),
                    'canaccept' => new external_value(PARAM_BOOL, 'Whether the policy can be accepted.'),
                    'candecline' => new external_value(PARAM_BOOL, 'Whether the policy can be declined.'),
                    'canrevoke' => new external_value(PARAM_BOOL, 'Whether the policy can be revoked.'),
                ]), 'Policies and acceptance status for the given user.', VALUE_OPTIONAL
            ),
            'warnings'  => new external_warnings(),
        ]);
    }
}
