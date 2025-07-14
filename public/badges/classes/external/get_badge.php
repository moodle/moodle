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

use core\exception\moodle_exception;
use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_single_structure;
use core_external\external_value;
use core_external\external_warnings;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/badgeslib.php');

/**
 * External service to get badge by id.
 *
 * @package   core_badges
 * @category  external
 * @copyright  2024 Daniel Ureña <durenadev@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 4.5
 */
class get_badge extends external_api {

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'id' => new external_value(PARAM_INT, 'Badge id', VALUE_REQUIRED),
        ]);
    }

    /**
     * Execute the get badge by id.
     *
     * @param int $id
     * @return array
     * @throws moodle_exception
     */
    public static function execute(int $id): array {
        global $CFG, $PAGE;

        // Initialize return variables.
        $warnings = [];

        // Validate the id.
        [
            'id' => $id,
        ] = self::validate_parameters(self::execute_parameters(), [
            'id' => $id,
        ]);

        // Validate badges is not disabled.
        if (empty($CFG->enablebadges)) {
            throw new moodle_exception('badgesdisabled', 'badges');
        }

        // Get the badge by id.
        $badgeclass = new \core_badges\output\badgeclass($id);
        if (empty($badgeclass->badge)) {
            throw new moodle_exception('error:relatedbadgedoesntexist', 'badges');
        }

        $PAGE->set_context($badgeclass->context);

        $result = badges_prepare_badgeclass_for_external($badgeclass);

        return [
            'badge'    => $result,
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
            'badge'  => badgeclass_exporter::get_read_structure(),
            'warnings' => new external_warnings(),
        ]);
    }
}
