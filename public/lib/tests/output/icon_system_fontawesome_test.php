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
final class icon_system_fontawesome_test extends \advanced_testcase {

    /**
     * Test that the specified icon has an SVG fallback.
     */
    public function test_svg_fallback(): void {
        // This can't be tested using data provider because it initializes the theme system when running filtered tests.
        $instance = icon_system::instance(icon_system::FONTAWESOME);
        $icons = array_map(function($key) {
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
                'key' => $key,
                'path' => $componentdir,
                'filename' => $file,
            ];
        }, array_keys($instance->get_icon_name_map()));

        foreach ($icons as $icon) {
            $this->assertTrue(
                file_exists("{$icon['path']}/pix/{$icon['filename']}.svg"), "No SVG equivalent found for '{$icon['key']}'",
            );
        }
    }
}
