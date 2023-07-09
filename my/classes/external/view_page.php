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

namespace core_my\external;

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_single_structure;
use core_external\external_value;
use core_external\external_warnings;

/**
 * External service to log viewed Dashboard and My pages.
 *
 * This is mainly used by the mobile application.
 *
 * @package   core_my
 * @category  external
 * @copyright 2023 Rodrigo Mady <rodrigo.mady@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 4.3
 */
class view_page extends external_api {
    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'page' => new external_value(PARAM_TEXT, 'My page to trigger a view event'),
        ]);
    }

    /**
     * Execute the My or Dashboard view event.
     *
     * @param string $page the page for trigger the event.
     * @return array
     */
    public static function execute(string $page): array {
        $warnings = [];
        $status   = true;
        // Validate the cmid ID.
        ['page'  => $page] = self::validate_parameters(
            self::execute_parameters(), ['page' => $page]
        );

        if ($page === 'my') {
            $eventname = '\core\event\mycourses_viewed';
        } else if ($page === 'dashboard') {
            $eventname = '\core\event\dashboard_viewed';
        } else {
            $status     = false;
            $warnings[] = [
                'item'        => $page,
                'warningcode' => 'invalidmypage',
                'message'     => 'The value for the page request is invalid!'
            ];
        }

        // Trigger my/dashboard view event.
        $context = \context_system::instance();
        self::validate_context($context);
        if ($status) {
            $event = $eventname::create(['context' => $context]);
            $event->trigger();
        }
        $result = [
            'status'   => $status,
            'warnings' => $warnings
        ];
        return $result;
    }

    /**
     * Describe the return structure of the external service.
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'status'   => new external_value(PARAM_BOOL, 'status: true if success'),
            'warnings' => new external_warnings(),
        ]);
    }
}
