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
 * Checks current recorded rate limit.
 *
 * @package local_o365
 * @author James McQuillan <james.mcquillan@remote-learner.net>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2014 onwards Microsoft Open Technologies, Inc. (http://msopentech.com/)
 */

namespace local_o365\healthcheck;

defined('MOODLE_INTERNAL') || die();

/**
 * Checks current recorded rate limit
 */
class ratelimit implements \local_o365\healthcheck\healthcheckinterface {
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
        $ratelimitdisabled = get_config('local_o365', 'ratelimitdisabled');
        if (!empty($ratelimitdisabled)) {
            return [
                'result' => false,
                'severity' => static::SEVERITY_TRIVIAL,
                'message' => get_string('healthcheck_ratelimit_result_disabled', 'local_o365'),
            ];
        }

        $ratelimit = get_config('local_o365', 'ratelimit');
        $ratelimit = explode(':', $ratelimit, 2);

        if (!empty($ratelimit[0]) && $ratelimit[1] > (time() - (10 * MINSECS))) {
            $a = new \stdClass;
            $a->level = $ratelimit[0];
            $a->timestart = date('c', $ratelimit[1]);
            if ($ratelimit[0] < 4) {
                return [
                    'result' => false,
                    'severity' => static::SEVERITY_TRIVIAL,
                    'message' => get_string('healthcheck_ratelimit_result_notice', 'local_o365', $a),
                ];
            } else {
                return [
                    'result' => false,
                    'severity' => static::SEVERITY_TRIVIAL,
                    'message' => get_string('healthcheck_ratelimit_result_warning', 'local_o365', $a),
                ];
            }
        } else {
            return [
                'result' => true,
                'severity' => static::SEVERITY_OK,
                'message' => get_string('healthcheck_ratelimit_result_passed', 'local_o365'),
            ];
        }
    }

    /**
     * Get a human-readable name for the health check.
     *
     * @return string A name for the health check.
     */
    public function get_name() {
        return get_string('healthcheck_ratelimit_title', 'local_o365');
    }
}
