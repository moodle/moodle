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
 * Custom sort order upgrade script.
 *
 * @package   qbank_columnsortorder
 * @copyright 2024 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core_question\local\bank\column_base;

/**
 * Upgrade the plugin.
 *
 * @param int $oldversion the version of this plugin we are upgrading from.
 * @return bool success/failure.
 */
function xmldb_qbank_columnsortorder_upgrade(int $oldversion): bool {
    global $DB;

    if ($oldversion < 2024042201) {
        // Before Moodle 4.3, config_plugins settings for qbank_columnsortorder (disabledcol, enabledcol) had a value like
        // qbank_statistics\columns\facility_index,qbank_statistics\columns\discriminative_efficiency, ...
        // In Moodle 4.3, the values are stored as qbank_statistics\columns\discriminative_efficiency-discriminative_efficiency.
        // So updating the old values to match the new format.
        // Update the columns records for qbank_columnsortorder plugin.
        $pluginconfigs = $DB->get_records('config_plugins', ['plugin' => 'qbank_columnsortorder'], 'name, value');

        foreach ($pluginconfigs as $config) {
            if ($config->name == 'version') {
                continue;
            }
            $fields = explode(',', $config->value);
            $updatedcols = [];
            foreach ($fields as $columnclass) {
                // Columns config that are already in the correct format, could be ignored.
                if (str_contains($columnclass, column_base::ID_SEPARATOR)) {
                    continue;
                }

                $classbits = explode('\\', $columnclass);
                $columnname = end($classbits);

                // The custom fields are to be in the format e.g., qbank_customfields\custom_field_column-test.
                if (str_contains($columnclass, 'custom_field_column')) {
                    array_pop($classbits);
                }

                $updatedcols[] = implode('\\', $classbits) . column_base::ID_SEPARATOR . $columnname;
            }
            $updatedconfig = implode(',', $updatedcols);
            set_config($config->name, $updatedconfig, 'qbank_columnsortorder');
        }

        // Custom sort order savepoint reached.
        upgrade_plugin_savepoint(true, 2024042201, 'qbank', 'columnsortorder');
    }

    if ($oldversion < 2024042202) {
        // Remove plugin entry created by previously incorrect 2024042201 savepoint.
        $DB->delete_records('config_plugins', ['plugin' => 'qbank_qbank_columnsortorder']);
        upgrade_plugin_savepoint(true, 2024042202, 'qbank', 'columnsortorder');
    }

    return true;
}
