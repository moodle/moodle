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
 * Checks that the system API user can communicate with Microsoft 365.
 *
 * @package local_o365
 * @author James McQuillan <james.mcquillan@remote-learner.net>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2014 onwards Microsoft, Inc. (http://microsoft.com/)
 */

namespace local_o365\healthcheck;

defined('MOODLE_INTERNAL') || die();

/**
 * Checks that the system API user can communicate with Microsoft 365.
 */
class systemapiuser implements \local_o365\healthcheck\healthcheckinterface {
    /**
     * Run the health check.
     *
     * @return array Array of result data. Must include:
     *               bool result Whether the health check passed or not.
     *               int severity If the health check failed, how bad a problem is it? This is one of the SEVERITY_* constants.
     *               string message A message to show the user.
     *               string fixlink If the healthcheck failed, a link to help resolve the problem.
     */
    public function run() {
        // Check that the system API user has a graph resource.
        $tokens = get_config('local_o365', 'systemtokens');
        $tokens = unserialize($tokens);
        $graphresource = \local_o365\rest\unified::get_tokenresource();
        if (!isset($tokens[$graphresource])) {
            return [
                'result' => false,
                'severity' => static::SEVERITY_WARNING,
                'message' => get_string('healthcheck_systemtoken_result_notoken', 'local_o365'),
                'fixlink' => new \moodle_url('/local/o365/acp.php', ['mode' => 'setsystemuser']),
            ];
        }

        // Try to refresh the token as an indicator for successful communication.
        $oidcconfig = get_config('auth_oidc');
        if (empty($oidcconfig)) {
            return [
                'result' => false,
                'severity' => static::SEVERITY_FATAL,
                'message' => get_string('healthcheck_systemtoken_result_noclientcreds', 'local_o365'),
                'fixlink' => new \moodle_url('/admin/auth_config.php', ['auth' => 'oidc']),
            ];
        }
        $httpclient = new \local_o365\httpclient();
        $clientdata = new \local_o365\oauth2\clientdata($oidcconfig->clientid, $oidcconfig->clientsecret, $oidcconfig->authendpoint,
                $oidcconfig->tokenendpoint);

        $tokenresource = \local_o365\rest\unified::get_tokenresource();
        $systemtoken = \local_o365\oauth2\systemapiusertoken::get_for_new_resource(null, $tokenresource, $clientdata, $httpclient);
        if (empty($systemtoken)) {
            return [
                'result' => false,
                'severity' => static::SEVERITY_WARNING,
                'message' => get_string('healthcheck_systemtoken_result_badtoken', 'local_o365'),
                'fixlink' => new \moodle_url('/local/o365/acp.php', ['mode' => 'setsystemuser']),
            ];
        } else {
            return [
                'result' => true,
                'severity' => static::SEVERITY_OK,
                'message' => get_string('healthcheck_systemtoken_result_passed', 'local_o365'),
            ];
        }
    }

    /**
     * Get a human-readable name for the health check.
     *
     * @return string A name for the health check.
     */
    public function get_name() {
        return get_string('healthcheck_systemapiuser_title', 'local_o365');
    }
}
