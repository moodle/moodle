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
use core_external\external_single_structure;
use core_external\external_value;
use core_external\external_warnings;
use mod_data\local\importer\preset_importer;
use mod_data\manager;

/**
 * This is the external method for deleting a saved preset.
 *
 * @package    mod_data
 * @since      Moodle 4.1
 * @copyright  2022 Amaia Anabitarte <amaia@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class get_mapping_information extends external_api {
    /**
     * Parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'cmid' => new external_value(PARAM_INT, 'Id of the data activity', VALUE_REQUIRED),
            'importedpreset' => new external_value(PARAM_TEXT, 'Preset to be imported'),
        ]);
    }

    /**
     * Get importing information for the given database course module.
     *
     * @param  int $cmid Id of the course module where to import the preset.
     * @param  string $importedpreset Plugin or saved preset to be imported.
     * @return array Information needed to decide whether to show the dialogue or not.
     */
    public static function execute(int $cmid, string $importedpreset): array {

        $params = self::validate_parameters(
            self::execute_parameters(),
            ['cmid' => $cmid, 'importedpreset' => $importedpreset]
        );

        try {
            // Let's get the manager.
            list($course, $cm) = get_course_and_cm_from_cmid($params['cmid'], manager::MODULE);
            $manager = manager::create_from_coursemodule($cm);

            $importer = preset_importer::create_from_plugin_or_directory($manager, $params['importedpreset']);
            $result['data'] = $importer->get_mapping_information();
        } catch (\moodle_exception $e) {
            $result['warnings'][] = [
                'item' => $importedpreset,
                'warningcode' => 'exception',
                'message' => $e->getMessage()
            ];
            notification::error($e->getMessage());
        }
        return $result;
    }

    /**
     * Return.
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'data' => new external_single_structure([
                'needsmapping' => new external_value(PARAM_BOOL, 'Whether the importing needs mapping or not'),
                'presetname' => new external_value(PARAM_TEXT, 'Name of the applied preset'),
                'fieldstocreate' => new external_value(PARAM_TEXT, 'List of field names to create'),
                'fieldstoremove' => new external_value(PARAM_TEXT, 'List of field names to remove'),
            ], 'Information to import if everything went fine', VALUE_OPTIONAL),
            'warnings' => new external_warnings(),
        ]);
    }
}
