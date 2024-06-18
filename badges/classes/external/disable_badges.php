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

use core_badges\badge;
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
 * External service to disable badges.
 *
 * @package   core_badges
 * @category  external
 * @copyright 2024 Sara Arjona <sara@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 4.5
 */
class disable_badges extends external_api {

    /**
     * Describes the parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'badgeids' => new external_multiple_structure(
                new external_value(PARAM_TEXT, 'The badge identifiers to update', VALUE_REQUIRED),
            ),
        ]);
    }

    /**
     * Disable the given badges.
     *
     * @param array $badgeids List of badge identifiers to disable.
     * @return array List of results and warnings.
     */
    public static function execute(array $badgeids): array {
        global $CFG, $DB;

        $warnings = [];

        [
            'badgeids' => $badgeids,
        ] = self::validate_parameters(self::execute_parameters(), [
            'badgeids' => $badgeids,
        ]);

        // Check if badges are enabled.
        if (empty($CFG->enablebadges)) {
            throw new moodle_exception('badgesdisabled', 'badges');
        }

        foreach ($badgeids as $badgeid) {
            $badge = new badge($badgeid);

            // Check capabilities.
            $context = $badge->get_context();
            self::validate_context($context);
            if (!has_capability('moodle/badges:configurecriteria', $context)) {
                $warnings[] = [
                    'item'        => $badgeid,
                    'warningcode' => 'nopermissions',
                    'message'     => get_string('nopermissions', 'error'),
                ];
                continue;
            }

            // Check if course badges are enabled.
            if (empty($CFG->badges_allowcoursebadges) && ($badge->type == BADGE_TYPE_COURSE)) {
                $warnings[] = [
                    'item'        => $badgeid,
                    'warningcode' => 'coursebadgesdisabled',
                    'message'     => get_string('coursebadgesdisabled', 'badges'),
                ];
                continue;
            }

            $status = ($badge->status == BADGE_STATUS_ACTIVE) ? BADGE_STATUS_INACTIVE : BADGE_STATUS_INACTIVE_LOCKED;
            // Deactivate the badge.
            $badge->set_status($status);
        }

        return [
            'result' => empty($warnings),
            'warnings' => $warnings,
        ];
    }

    /**
     * Describe the return structure of the external service.
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'result' => new external_value(PARAM_BOOL, 'The processing result'),
            'warnings' => new external_warnings(),
        ]);
    }
}
