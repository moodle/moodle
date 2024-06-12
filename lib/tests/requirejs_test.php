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

namespace core;

use core_requirejs;

/**
 * Unit tests for requirejs.
 *
 * @package   core
 * @author    Damyon Wiese <damyon@moodle.com>
 * @copyright 2016 Damyon Wiese
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class requirejs_test extends \advanced_testcase {

    /**
     * Test requirejs loader
     */
    public function test_requirejs(): void {
        global $CFG;

        // Find a core module.
        $result = core_requirejs::find_one_amd_module('core', 'templates');
        $expected = ['core/templates' => $CFG->dirroot . '/lib/amd/build/templates.min.js'];
        $this->assertEquals($expected, $result);

        // Find a subsystem module (none exist yet).
        $result = core_requirejs::find_one_amd_module('core_group', 'doesnotexist');
        $expected = [];
        $this->assertEquals($expected, $result);

        // Find all modules.
        $result = core_requirejs::find_all_amd_modules();
        foreach ($result as $key => $path) {
            // Lets verify the first part of the key is a valid component name and the second part correctly contains "min" or not.
            list($component, $template) = explode('/', $key, 2);
            $dir = \core_component::get_component_directory($component);
            $this->assertNotEmpty($dir);
            // Only "core" is allowed to have no _ in component names.
            if (strpos($component, '_') === false) {
                $this->assertEquals('core', $component);
            }

            $this->assertStringContainsString('.min', $path);
        }

    }
}
