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

namespace tiny_autosave\external;

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_single_structure;
use core_external\external_value;

/**
 * Web Service to reset the autosave session.
 *
 * @package   tiny_autosave
 * @category  external
 * @copyright 2022 Andrew Lyons <andrew@nicols.co.uk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class reset_autosave_session extends external_api {
    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'contextid' => new external_value(PARAM_INT, 'The context id that owns the editor', VALUE_REQUIRED),
            'pagehash' => new external_value(PARAM_ALPHANUMEXT, 'The page hash', VALUE_REQUIRED),
            'pageinstance' => new external_value(PARAM_ALPHANUMEXT, 'The page instance', VALUE_REQUIRED),
            'elementid' => new external_value(PARAM_RAW, 'The ID of the element', VALUE_REQUIRED),
        ]);
    }

    /**
     * Reset the autosave entry for this autosave instance.
     *
     * If not matching autosave area could be found, the function will
     * silently return and this is not treated as an error condition.
     *
     * @param int $contextid The context id of the owner
     * @param string $pagehash The hash of the page
     * @param string $pageinstance The instance id of the page
     * @param string $elementid The id of the element
     * @return null
     */
    public static function execute(
        int $contextid,
        string $pagehash,
        string $pageinstance,
        string $elementid
    ): array {
        global $DB, $USER;

        [
            'contextid' => $contextid,
            'pagehash' => $pagehash,
            'elementid' => $elementid,
            'pageinstance' => $pageinstance,
        ] = self::validate_parameters(self::execute_parameters(), [
            'contextid' => $contextid,
            'pagehash' => $pagehash,
            'elementid' => $elementid,
            'pageinstance' => $pageinstance,

        ]);

        $manager = new \tiny_autosave\autosave_manager($contextid, $pagehash, $pageinstance, $elementid);
        $manager->remove_autosave_record();

        return [];
    }

    /**
     * Describe the return structure of the external service.
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([]);
    }
}
