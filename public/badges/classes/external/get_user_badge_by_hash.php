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

namespace core_badges\external;

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_single_structure;
use core_external\external_multiple_structure;
use core_external\external_value;
use core_external\external_warnings;
use moodle_exception;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/badgeslib.php');

/**
 * External service to get user badge.
 *
 * This is mainly used by the mobile application.
 *
 * @package   core_badges
 * @category  external
 * @copyright 2023 Rodrigo Mady <rodrigo.mady@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 4.3
 */
class get_user_badge_by_hash extends external_api {
    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'hash' => new external_value(PARAM_ALPHANUM, 'Badge issued hash', VALUE_REQUIRED),
        ]);
    }

    /**
     * Execute the get user badge.
     *
     * @param string $hash
     * @return array
     * @throws \restricted_context_exception
     */
    public static function execute(string $hash): array {
        global $CFG;

        // Initialize return variables.
        $warnings = [];
        $result   = [];

        // Validate the hash.
        [
            'hash' => $hash,
        ] = self::validate_parameters(self::execute_parameters(), [
            'hash' => $hash,
        ]);

        if (empty($CFG->enablebadges)) {
            throw new moodle_exception('badgesdisabled', 'badges');
        }

        // Get the badge by hash.
        $badge = badges_get_badge_by_hash($hash);

        if (!empty($badge)) {
            // Get the user that issued the badge.
            $user     = \core_user::get_user($badge->userid, '*', MUST_EXIST);
            $result[] = badges_prepare_badge_for_external($badge, $user);
        } else {
            $warnings[] = [
                'item'        => $hash,
                'warningcode' => 'badgeawardnotfound',
                'message'     => get_string('error:badgeawardnotfound', 'badges')
            ];
        }

        return [
            'badge'    => $result,
            'warnings' => $warnings
        ];
    }

    /**
     * Describe the return structure of the external service.
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'badge'  => new external_multiple_structure(
                user_badge_exporter::get_read_structure()
            ),
            'warnings' => new external_warnings()
        ]);
    }
}
