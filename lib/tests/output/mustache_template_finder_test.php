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
 * Unit tests for lib/classes/output/mustache_template_finder.php
 *
 * Unit tests for the Mustache template finder class (contains logic about
 * resolving mustache template locations.
 *
 * @package   core
 * @category  test
 * @copyright 2015 Damyon Wiese
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class mustache_template_finder_test extends \advanced_testcase {

    /**
     * Data provider which reutrns a set of valid template directories to be used when testing
     * get_template_directories_for_component.
     *
     * @return array
     */
    public static function valid_template_directories_provider(): array {
        return [
            'plugin: mod_assign' => [
                'component' => 'mod_assign',
                'theme' => '',
                'paths' => [
                    'theme/boost/templates/mod_assign/',
                    'mod/assign/templates/'
                ],
            ],
            'plugin: mod_assign with classic' => [
                'component' => 'mod_assign',
                'theme' => 'classic',
                'paths' => [
                    'theme/classic/templates/mod_assign/',
                    'theme/boost/templates/mod_assign/',
                    'mod/assign/templates/'
                ],
            ],
            'subsystem: core_user' => [
                'component' => 'core_user',
                'theme' => 'classic',
                'paths' => [
                    'theme/classic/templates/core_user/',
                    'theme/boost/templates/core_user/',
                    'user/templates/'
                ],
            ],
            'core' => [
                'component' => 'core',
                'theme' => 'classic',
                'paths' => [
                    'theme/classic/templates/core/',
                    'theme/boost/templates/core/',
                    'lib/templates/'
                ],
            ],
        ];
    }

    /**
     * Tests for get_template_directories_for_component.
     *
     * @dataProvider valid_template_directories_provider
     * @param   string $component
     * @param   string $theme
     * @param   array $paths
     */
    public function test_get_template_directories_for_component(string $component, string $theme, array $paths): void {
        global $CFG;

        // Test a plugin.
        $dirs = mustache_template_finder::get_template_directories_for_component($component, $theme, $paths);

        $correct = array_map(function($path) use ($CFG) {
            return implode('/', [$CFG->dirroot, $path]);
        }, $paths);

        $this->assertEquals($correct, $dirs);
    }

    /**
     * Tests for get_template_directories_for_component when dealing with an invalid component.
     */
    public function test_invalid_component_get_template_directories_for_component(): void {
        // Test something invalid.
        $this->expectException(\coding_exception::class);
        mustache_template_finder::get_template_directories_for_component('octopus', 'classic');
    }

    /**
     * Data provider which reutrns a set of valid template directories to be used when testing
     * get_template_directories_for_component.
     *
     * @return array
     */
    public static function valid_template_filepath_provider(): array {
        return [
            'Standard core template' => [
                'template' => 'core/modal',
                'theme' => '',
                'location' => 'lib/templates/modal.mustache',
            ],
            'Template overridden by theme' => [
                'template' => 'core_form/element-float-inline',
                'theme' => '',
                'location' => 'theme/boost/templates/core_form/element-float-inline.mustache',
            ],
            'Template overridden by theme but child theme selected' => [
                'template' => 'core_form/element-float-inline',
                'theme' => 'classic',
                'location' => 'theme/boost/templates/core_form/element-float-inline.mustache',
            ],
            'Template overridden by child theme' => [
                'template' => 'core/full_header',
                'theme' => 'classic',
                'location' => 'theme/classic/templates/core/full_header.mustache',
            ],
            'Template overridden by child theme but tested against defualt theme' => [
                'template' => 'core/full_header',
                'theme' => '',
                'location' => 'lib/templates/full_header.mustache',
            ],
            'Standard plugin template' => [
                'template' => 'mod_assign/grading_panel',
                'theme' => '',
                'location' => 'mod/assign/templates/grading_panel.mustache',
            ],
            'Subsystem template' => [
                'template' => 'core_user/status_details',
                'theme' => '',
                'location' => 'user/templates/status_details.mustache',
            ],
            'Theme own template' => [
                'template' => 'theme_classic/columns',
                'theme' => '',
                'location' => 'theme/classic/templates/columns.mustache',
            ],
            'Theme overridden template against that theme' => [
                'template' => 'theme_classic/navbar',
                'theme' => 'classic',
                'location' => 'theme/classic/templates/navbar.mustache',
            ],
            // Note: This one looks strange but is correct. It is legitimate to request theme's component template in
            // the context of another theme. For example, this is used by child themes making use of parent theme
            // templates.
            'Theme overridden template against the default theme' => [
                'template' => 'theme_classic/navbar',
                'theme' => '',
                'location' => 'theme/classic/templates/navbar.mustache',
            ],
        ];
    }

    /**
     * Tests for get_template_filepath.
     *
     * @dataProvider valid_template_filepath_provider
     * @param   string $template
     * @param   string $theme
     * @param   string $location
     */
    public function test_get_template_filepath(string $template, string $theme, string $location): void {
        global $CFG;

        $filename = mustache_template_finder::get_template_filepath($template, $theme);
        $this->assertEquals("{$CFG->dirroot}/{$location}", $filename);
    }

    /**
     * Tests for get_template_filepath when dealing with an invalid component.
     */
    public function test_invalid_component_get_template_filepath(): void {
        $this->expectException(\moodle_exception::class);
        mustache_template_finder::get_template_filepath('core/octopus', 'classic');
    }
}
