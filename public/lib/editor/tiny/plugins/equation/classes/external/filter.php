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

namespace tiny_equation\external;

use context;
use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_single_structure;
use core_external\external_value;
use filter_manager;

/**
 * TinyMCE Equation external API for filtering the equation.
 *
 * @package    tiny_equation
 * @copyright  2022 Huong Nguyen <huongnv13@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class filter extends external_api {

    /**
     * Describes the parameters for filtering the equation.
     *
     * @return external_function_parameters
     * @since Moodle 4.1
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'contextid' => new external_value(PARAM_INT, 'The context ID', VALUE_REQUIRED),
            'content' => new external_value(PARAM_RAW, 'The equation content', VALUE_REQUIRED)
        ]);
    }

    /**
     * External function to filter the equation.
     *
     * @param int $contextid Context ID.
     * @param string $content Equation content.
     * @return array
     * @since Moodle 4.1
     */
    public static function execute(int $contextid, string $content): array {
        [
            'contextid' => $contextid,
            'content' => $content
        ] = self::validate_parameters(self::execute_parameters(), [
            'contextid' => $contextid,
            'content' => $content
        ]);

        $context = context::instance_by_id($contextid);
        self::validate_context($context);
        $result = filter_manager::instance()->filter_text($content, $context);

        return [
            'content' => $result,
        ];
    }

    /**
     * Describes the data returned from the external function.
     *
     * @return external_single_structure
     * @since Moodle 4.1
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'content' => new external_value(PARAM_RAW, 'Filtered content'),
        ]);
    }
}
