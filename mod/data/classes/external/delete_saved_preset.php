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

namespace mod_data\external;

use core\notification;
use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;
use core_external\external_warnings;
use mod_data\manager;
use mod_data\preset;

/**
 * This is the external method for deleting a saved preset.
 *
 * @package    mod_data
 * @since      Moodle 4.1
 * @copyright  2022 Amaia Anabitarte <amaia@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class delete_saved_preset extends external_api {
    /**
     * Parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'dataid' => new external_value(PARAM_INT, 'Id of the data activity', VALUE_REQUIRED),
            'presetnames' => new external_multiple_structure(
                new external_value(PARAM_TEXT, 'The preset name to delete', VALUE_REQUIRED)
            )
        ]);
    }

    /**
     * Delete saved preset from the file system.
     *
     * @param  int $dataid Id of the data activity to check context and permissions.
     * @param  array $presetnames List of saved preset names to delete.
     * @return array True if the content has been deleted; false and the warning, otherwise.
     */
    public static function execute(int $dataid, array $presetnames): array {
        global $DB;

        $result = false;
        $warnings = [];

        $params = self::validate_parameters(self::execute_parameters(), ['dataid' => $dataid, 'presetnames' => $presetnames]);

        $instance = $DB->get_record('data', ['id' => $params['dataid']], '*', MUST_EXIST);
        $manager = manager::create_from_instance($instance);

        foreach ($params['presetnames'] as $presetname) {
            try {
                $preset = preset::create_from_instance($manager, $presetname);
                if ($preset->can_manage()) {
                    if ($preset->delete()) {
                        notification::success(get_string('presetdeleted', 'mod_data'));
                        $result = true;
                    } else {
                        // An error ocurred while deleting the preset.
                        $warnings[] = [
                            'item' => $presetname,
                            'warningcode' => 'failedpresetdelete',
                            'message' => get_string('failedpresetdelete', 'mod_data')
                        ];
                        notification::error(get_string('failedpresetdelete', 'mod_data'));
                    }
                } else {
                    // The user has no permission to delete the preset.
                    $warnings[] = [
                        'item' => $presetname,
                        'warningcode' => 'cannotdeletepreset',
                        'message' => get_string('cannotdeletepreset', 'mod_data')
                    ];
                    notification::error(get_string('cannotdeletepreset', 'mod_data'));
                }
            } catch (\moodle_exception $e) {
                // The saved preset has not been deleted.
                $warnings[] = [
                    'item' => $presetname,
                    'warningcode' => 'exception',
                    'message' => $e->getMessage()
                ];
                notification::error($e->getMessage());
            }
        }

        return [
            'result' => $result,
            'warnings' => $warnings
        ];
    }

    /**
     * Return.
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'result' => new external_value(PARAM_BOOL, 'The processing result'),
            'warnings' => new external_warnings()
        ]);
    }
}
