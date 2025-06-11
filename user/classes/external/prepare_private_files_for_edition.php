<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

namespace core_user\external;

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;
use core_external\external_warnings;
use context_user;

/**
 * Prepares the draft area for user private files.
 *
 * @package   core_user
 * @category  external
 * @copyright 2024 Juan Leyva
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class prepare_private_files_for_edition extends external_api {

    /**
     * Describes the external function parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([]);
    }

    /**
     * Prepare a draft area for private files.
     *
     * @throws \moodle_exception;
     * @return array
     */
    public static function execute(): array {
        global $USER;

        $usercontext = context_user::instance($USER->id);
        self::validate_context($usercontext);

        $form = new \core_user\form\private_files();
        // Permission checks.
        $form->check_access_for_dynamic_submission();

        $areaoptions = $form->get_options();
        $draftitemid = 0;
        file_prepare_draft_area($draftitemid, $usercontext->id, 'user', 'private', 0, $areaoptions);

        // Just get a structure compatible with external API.
        array_walk($areaoptions, function(&$item, $key) {
            $item = ['name' => $key, 'value' => $item];
        });

        return [
            'draftitemid' => $draftitemid,
            'areaoptions' => $areaoptions,
            'warnings' => [],
        ];
    }

    /**
     * Describe the return structure of the external service.
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure(
            [
                'draftitemid' => new external_value(PARAM_INT, 'Draft item id for the file area.'),
                'areaoptions' => new external_multiple_structure(
                    new external_single_structure(
                        [
                            'name' => new external_value(PARAM_RAW, 'Name of option.'),
                            'value' => new external_value(PARAM_RAW, 'Value of option.'),
                        ]
                    ), 'Draft file area options.'
                ),
                'warnings' => new external_warnings(),
            ]
        );
    }
}
