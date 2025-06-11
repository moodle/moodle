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

namespace core_blog\external;

use context_system;
use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_single_structure;
use core_external\external_value;
use core_external\external_warnings;


/**
 * External blog API implementation
 *
 * @package    core_blog
 * @copyright  2024 Juan Leyva <juan@moodle.com>
 * @category   external
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class get_access_information extends external_api {

    /**
     * Returns description of method parameters.
     *
     * @return external_function_parameters.
     * @since  Moodle 4.4
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([]);
    }

    /**
     * Convenience function to retrieve some permissions information for the blogs system.
     *
     * @return array The access information
     * @since  Moodle 4.4
     */
    public static function execute(): array {

        $context = context_system::instance();
        self::validate_context($context);

        return [
            'canview' => has_capability('moodle/blog:view', $context),
            'cansearch' => has_capability('moodle/blog:search', $context),
            'canviewdrafts' => has_capability('moodle/blog:viewdrafts', $context),
            'cancreate' => has_capability('moodle/blog:create', $context),
            'canmanageentries' => has_capability('moodle/blog:manageentries', $context),
            'canmanageexternal' => has_capability('moodle/blog:manageexternal', $context),
            'warnings' => [],
        ];
    }

    /**
     * Returns description of method result value.
     *
     * @return external_single_structure.
     * @since  Moodle 4.4
     */
    public static function execute_returns(): external_single_structure {

        return new external_single_structure(
            [
                'canview' => new external_value(PARAM_BOOL, 'Whether the user can view blogs'),
                'cansearch' => new external_value(PARAM_BOOL, 'Whether the user can search blogs'),
                'canviewdrafts' => new external_value(PARAM_BOOL, 'Whether the user can view drafts'),
                'cancreate' => new external_value(PARAM_BOOL, 'Whether the user can create blog entries'),
                'canmanageentries' => new external_value(PARAM_BOOL, 'Whether the user can manage blog entries'),
                'canmanageexternal' => new external_value(PARAM_BOOL, 'Whether the user can manage external blogs'),
                'warnings' => new external_warnings(),
            ]
        );
    }
}
