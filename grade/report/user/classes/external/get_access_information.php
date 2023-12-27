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

namespace gradereport_user\external;

use context_course;
use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_single_structure;
use core_external\external_value;
use core_external\external_warnings;
use stdClass;


/**
 * External grade report API implementation
 *
 * @package    gradereport_user
 * @copyright  2023 Juan Leyva <juan@moodle.com>
 * @category   external
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class get_access_information extends external_api {

    /**
     * Returns description of method parameters.
     *
     * @return external_function_parameters.
     * @since  Moodle 4.2
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters(
            [
                'courseid' => new external_value(PARAM_INT, 'Course to check.'),
            ]
        );
    }

    /**
     * Convenience function to retrieve some permissions information for the given course grade report.
     *
     * @param int $courseid Course to check, empty for site.
     * @return array The access information
     * @since  Moodle 4.2
     */
    public static function execute(int $courseid): array {

        $params = self::validate_parameters(self::execute_parameters(), ['courseid' => $courseid]);

        $context = context_course::instance($params['courseid']);

        self::validate_context($context);

        return [
            'canviewusergradereport' => has_capability('gradereport/user:view', $context),
            'canviewmygrades' => has_capability('moodle/grade:view', $context),
            'canviewallgrades' => has_capability('moodle/grade:viewall', $context),
            'warnings' => [],
        ];
    }

    /**
     * Returns description of method result value.
     *
     * @return external_single_structure.
     * @since  Moodle 4.2
     */
    public static function execute_returns(): external_single_structure {

        return new external_single_structure(
            [
                'canviewusergradereport' => new external_value(PARAM_BOOL, 'Whether the user can view the user grade report.'),
                'canviewmygrades' => new external_value(PARAM_BOOL, 'Whether the user can view his grades in the course.'),
                'canviewallgrades' => new external_value(PARAM_BOOL, 'Whether the user can view all users grades in the course.'),
                'warnings' => new external_warnings(),
            ]
        );
    }
}
