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
 * Provides the {@link core_form\external} class.
 *
 * @package     core_form
 * @category    external
 * @copyright   2017 David Mudrák <david@moodle.com>
 * @copyright   2016 Jonathon Fowler <fowlerj@usq.edu.au>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_form;

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;

/**
 * Implements the external functions provided by the core_form subsystem.
 *
 * @copyright 2017 David Mudrak <david@moodle.com>
 * @copyright 2016 Jonathon Fowler <fowlerj@usq.edu.au>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class external extends external_api {

    /**
     * Describes the input paramaters of the get_filetypes_browser_data external function.
     *
     * @return \core_external\external_description
     */
    public static function get_filetypes_browser_data_parameters() {
        return new external_function_parameters([
            'onlytypes' => new external_value(PARAM_RAW, 'Limit the browser to the given groups and extensions', VALUE_DEFAULT, ''),
            'allowall' => new external_value(PARAM_BOOL, 'Allows to select All file types, does not apply with onlytypes are set.',
                VALUE_DEFAULT, true),
            'current' => new external_value(PARAM_RAW, 'Current types that should be selected.', VALUE_DEFAULT, ''),
        ]);
    }

    /**
     * Implements the get_filetypes_browser_data external function.
     *
     * @param string $onlytypes Allow selection from these file types only; for example 'web_image'.
     * @param bool $allowall Allow to select 'All file types'. Does not apply if onlytypes is set.
     * @param string $current Current values that should be selected.
     * @return object
     */
    public static function get_filetypes_browser_data($onlytypes, $allowall, $current) {

        $params = self::validate_parameters(self::get_filetypes_browser_data_parameters(),
            compact('onlytypes', 'allowall', 'current'));

        $util = new filetypes_util();

        return ['groups' => $util->data_for_browser($params['onlytypes'], $params['allowall'], $params['current'])];
    }

    /**
     * Describes the output of the get_filetypes_browser_data external function.
     *
     * @return \core_external\external_description
     */
    public static function get_filetypes_browser_data_returns() {

        $type = new external_single_structure([
            'key' => new external_value(PARAM_RAW, 'The file type identifier'),
            'name' => new external_value(PARAM_RAW, 'The file type name'),
            'selected' => new external_value(PARAM_BOOL, 'Should it be marked as selected'),
            'ext' => new external_value(PARAM_RAW, 'The file extension associated with the file type'),
        ]);

        $group = new external_single_structure([
            'key' => new external_value(PARAM_RAW, 'The file type group identifier'),
            'name' => new external_value(PARAM_RAW, 'The file type group name'),
            'selectable' => new external_value(PARAM_BOOL, 'Can it be marked as selected'),
            'selected' => new external_value(PARAM_BOOL, 'Should it be marked as selected'),
            'ext' => new external_value(PARAM_RAW, 'The list of file extensions associated with the group'),
            'expanded' => new external_value(PARAM_BOOL, 'Should the group start as expanded or collapsed'),
            'types' => new external_multiple_structure($type, 'List of file types in the group'),
        ]);

        return new external_single_structure([
            'groups' => new external_multiple_structure($group, 'List of file type groups'),
        ]);
    }
}
