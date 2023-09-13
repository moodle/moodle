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

namespace core\output;

/**
 * Unit tests for the FontAwesome icon system.
 *
 * @package     core
 * @copyright   2023 Andrew Nicols <andrew@nicols.co.uk>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \core\output\icon_system_fontawesome
 */
class icon_system_fontawesome_test extends \advanced_testcase {
    /**
     * Returns a list of all mapped icons along with the component dir, and file name (without any suffix/extension).
     *
     * @return array
     */
    public function get_icon_name_map(): array {
        $instance = icon_system::instance(icon_system::FONTAWESOME);
        return array_map(function($key) {
            global $CFG;
            [$component, $file] = explode(':', $key);

            if ($component === 'core') {
                $componentdir = $CFG->dirroot;
            } else if ($component === 'theme') {
                $componentdir = \core_component::get_component_directory('theme_' . \theme_config::DEFAULT_THEME);
            } else {
                $componentdir = \core_component::get_component_directory($component);
            }

            return [
                $key,
                $componentdir,
                $file,
            ];
        }, array_keys($instance->get_icon_name_map()));
    }

    /**
     * Test that the specified icon has an SVG fallback.
     *
     * @param string $key Icon key.
     * @param string $path Path to the component directory.
     * @param string $filename Icon filename.
     * @dataProvider get_icon_name_map
     */
    public function test_svg_fallback(string $key, string $path, string $filename): void {
        $this->assertTrue(file_exists("{$path}/pix/{$filename}.svg"), "No SVG equivalent found for '{$key}'");
    }
}
