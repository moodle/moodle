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

namespace mod_data;

/**
 * Class preset for database activity.
 *
 * @package    mod_data
 * @copyright  2022 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class preset {

    /**
     * Checks if a directory contains all the required files to define a preset.
     *
     * @param string $directory The patch to check if it contains the preset files or not.
     * @return bool True if the directory contains all the preset files; false otherwise.
     */
    public static function is_directory_a_preset(string $directory): bool {
        $status = true;
        $directory = rtrim($directory, '/\\') . '/';
        $presetfilenames = array_merge(array_values(manager::TEMPLATES_LIST), ['preset.xml']);
        foreach ($presetfilenames as $filename) {
            $status &= file_exists($directory.$filename);
        }

        return $status;
    }

    /**
     * Returns the best name to show for a datapreset plugin.
     *
     * @param string $pluginname The datapreset plugin name.
     * @return string The plugin preset name to display.
     */
    public static function get_name_from_plugin(string $pluginname): string {
        if (get_string_manager()->string_exists('modulename', 'datapreset_'.$pluginname)) {
            return get_string('modulename', 'datapreset_'.$pluginname);
        } else {
            return $pluginname;
        }
    }
}
