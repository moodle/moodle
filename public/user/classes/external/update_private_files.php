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
use core_external\external_single_structure;
use core_external\external_value;
use core_external\external_warnings;
use context_user;

/**
 * Updates current user private files.
 *
 * @package   core_user
 * @category  external
 * @copyright 2024 Juan Leyva
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class update_private_files extends external_api {

    /**
     * Describes the external function parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters(
            [
                'draftitemid' => new external_value(PARAM_INT, 'The draft item id with the files.'),
            ]
        );
    }

    /**
     * Updates current user private files.
     *
     * @param int $draftitemid The draft item id with the files.
     * @throws \moodle_exception;
     * @return array
     */
    public static function execute(int $draftitemid): array {
        global $USER;

        $params = self::validate_parameters(self::execute_parameters(), [
            'draftitemid' => $draftitemid,
        ]);
        $warnings = [];

        $usercontext = context_user::instance($USER->id);
        self::validate_context($usercontext);

        $fs = get_file_storage();
        if (empty($fs->get_area_files($usercontext->id, 'user', 'draft', $params['draftitemid']))) {
            throw new \moodle_exception('Invalid draft item id.');
        }

        // Data structure for the draft item id.
        $data = ['files_filemanager' => $params['draftitemid']];
        // Use existing form for validation.
        $form = new \core_user\form\private_files();
        $form->check_access_for_dynamic_submission();
        $errors = $form->validation($data, []);

        if (!empty($errors)) {
            $status = false;
            foreach ($errors as $itemname => $message) {
                $warnings[] = [
                    'item' => $itemname,
                    'itemid' => 0,
                    'warningcode' => 'fielderror',
                    'message' => s($message),
                ];
            }
        } else {
            file_postupdate_standard_filemanager((object) $data, 'files',
                $form->get_options(), $usercontext, 'user', 'private', 0);
            $status = true;
        }

        return [
            'status' => $status,
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
            'status' => new external_value(PARAM_BOOL, 'The update result, true if everything went well.'),
            'warnings' => new external_warnings(),
        ]);
    }
}
