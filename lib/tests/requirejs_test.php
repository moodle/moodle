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
 * Unit tests for requirejs loader.
 *
 * @package   core
 * @author    Damyon Wiese <damyon@moodle.com>
 * @copyright 2016 Damyon Wiese
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Unit tests for requirejs.
 *
 * @package   core
 * @author    Damyon Wiese <damyon@moodle.com>
 * @copyright 2016 Damyon Wiese
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_requirejs_testcase extends advanced_testcase {

    /**
     * Test requirejs loader
     */
    public function test_requirejs() {
        global $CFG;

        // Find a core module.
        $result = core_requirejs::find_one_amd_module('core', 'templates', false);
        $expected = ['core/templates' => $CFG->dirroot . '/lib/amd/build/templates.min.js'];
        $this->assertEquals($expected, $result);

        $result = core_requirejs::find_one_amd_module('core', 'templates', true);
        $expected = ['core/templates' => $CFG->dirroot . '/lib/amd/src/templates.js'];
        $this->assertEquals($expected, $result);

        // Find a subsystem module (none exist yet).
        $result = core_requirejs::find_one_amd_module('core_group', 'doesnotexist', false);
        $expected = [];
        $this->assertEquals($expected, $result);

        // Find a plugin module.
        $result = core_requirejs::find_one_amd_module('tool_templatelibrary', 'search', true);
        $expected = ['tool_templatelibrary/search' => $CFG->dirroot . '/admin/tool/templatelibrary/amd/src/search.js'];
        $this->assertEquals($expected, $result);

        // Find all modules - no debugging.
        $result = core_requirejs::find_all_amd_modules(true);
        foreach ($result as $key => $path) {
            // Lets verify the first part of the key is a valid component name and the second part correctly contains "min" or not.
            list($component, $template) = explode('/', $key, 2);
            // Can we resolve it to a valid dir?
            $dir = core_component::get_component_directory($component);
            $this->assertNotEmpty($dir);

            // Only "core" is allowed to have no _ in component names.
            if (strpos($component, '_') === false) {
                $this->assertEquals('core', $component);
            }
            $this->assertNotContains('.min', $path);
        }

        // Find all modules - debugging.
        $result = core_requirejs::find_all_amd_modules(false);
        foreach ($result as $key => $path) {
            // Lets verify the first part of the key is a valid component name and the second part correctly contains "min" or not.
            list($component, $template) = explode('/', $key, 2);
            $dir = core_component::get_component_directory($component);
            $this->assertNotEmpty($dir);
            // Only "core" is allowed to have no _ in component names.
            if (strpos($component, '_') === false) {
                $this->assertEquals('core', $component);
            }

            $this->assertContains('.min', $path);
        }

    }
}
