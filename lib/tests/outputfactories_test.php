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

use test_output_factory;

/**
 * Unit tests for lib/outputfactories.php.
 *
 * @package   core
 * @category  test
 * @copyright 2014 Damyon Wiese
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class outputfactories_test extends \advanced_testcase {
    #[\Override]
    public static function setUpBeforeClass(): void {
        global $CFG;

        require_once($CFG->libdir . '/tests/fixtures/test_renderer_factory.php');

        parent::setUpBeforeClass();
    }

    public function test_nonautoloaded_classnames(): void {
        global $PAGE;
        $renderer = $PAGE->get_renderer('mod_assign');
    }

    public function test_autoloaded_classnames(): void {
        $testfactory = new test_output_factory();
        $component = 'mod_assign';
        $subtype = 'custom';
        $target = RENDERER_TARGET_AJAX;

        $paths = $testfactory->get_standard_renderer_factory_search_paths('');
        $this->assertSame($paths, array (
            '\\core\\output\\renderer_cli',
            'core_renderer_cli',
            '\\core\\output\\renderer',
            'core_renderer',
        ));
        $paths = $testfactory->get_standard_renderer_factory_search_paths($component);
        $this->assertSame($paths, array (
            '\\mod_assign\\output\\renderer_cli',
            'mod_assign_renderer_cli',
            '\\mod_assign\\output\\renderer',
            'mod_assign_renderer',
        ));
        $paths = $testfactory->get_standard_renderer_factory_search_paths($component, $subtype);
        $this->assertSame($paths, array (
            '\\mod_assign\\output\\custom_renderer_cli',
            '\\mod_assign\\output\\custom\\renderer_cli',
            'mod_assign_custom_renderer_cli',
            '\\mod_assign\\output\\custom_renderer',
            '\\mod_assign\\output\\custom\\renderer',
            'mod_assign_custom_renderer',
        ));
        $paths = $testfactory->get_standard_renderer_factory_search_paths($component, $subtype, $target);
        $this->assertSame($paths, array (
            '\\mod_assign\\output\\custom_renderer_ajax',
            '\\mod_assign\\output\\custom\\renderer_ajax',
            'mod_assign_custom_renderer_ajax',
            '\\mod_assign\\output\\custom_renderer',
            '\\mod_assign\\output\\custom\\renderer',
            'mod_assign_custom_renderer',
        ));
        $paths = $testfactory->get_theme_overridden_renderer_factory_search_paths('');
        $this->assertSame($paths, array (
            'theme_child\\output\\core_renderer_cli',
            'theme_child_core_renderer_cli',
            'theme_parent\\output\\core_renderer_cli',
            'theme_parent_core_renderer_cli',
            '\\core\\output\\renderer_cli',
            'core_renderer_cli',
            'theme_child\\output\\core_renderer',
            'theme_child_core_renderer',
            'theme_parent\\output\\core_renderer',
            'theme_parent_core_renderer',
            '\\core\\output\\renderer',
            'core_renderer',
        ));
        $paths = $testfactory->get_theme_overridden_renderer_factory_search_paths($component);
        $this->assertSame($paths, array (
            'theme_child\\output\\mod_assign_renderer_cli',
            'theme_child_mod_assign_renderer_cli',
            'theme_parent\\output\\mod_assign_renderer_cli',
            'theme_parent_mod_assign_renderer_cli',
            '\\mod_assign\\output\\renderer_cli',
            'mod_assign_renderer_cli',
            'theme_child\\output\\mod_assign_renderer',
            'theme_child_mod_assign_renderer',
            'theme_parent\\output\\mod_assign_renderer',
            'theme_parent_mod_assign_renderer',
            '\\mod_assign\\output\\renderer',
            'mod_assign_renderer',
        ));
        $paths = $testfactory->get_theme_overridden_renderer_factory_search_paths($component, $subtype);
        $this->assertSame($paths, array (
            'theme_child\\output\\mod_assign\\custom_renderer_cli',
            'theme_child\\output\\mod_assign\\custom\\renderer_cli',
            'theme_child_mod_assign_custom_renderer_cli',
            'theme_parent\\output\\mod_assign\\custom_renderer_cli',
            'theme_parent\\output\\mod_assign\\custom\\renderer_cli',
            'theme_parent_mod_assign_custom_renderer_cli',
            '\\mod_assign\\output\\custom_renderer_cli',
            '\\mod_assign\\output\\custom\\renderer_cli',
            'mod_assign_custom_renderer_cli',
            'theme_child\\output\\mod_assign\\custom_renderer',
            'theme_child\\output\\mod_assign\\custom\\renderer',
            'theme_child_mod_assign_custom_renderer',
            'theme_parent\\output\\mod_assign\\custom_renderer',
            'theme_parent\\output\\mod_assign\\custom\\renderer',
            'theme_parent_mod_assign_custom_renderer',
            '\\mod_assign\\output\\custom_renderer',
            '\\mod_assign\\output\\custom\\renderer',
            'mod_assign_custom_renderer',
        ));
        $paths = $testfactory->get_theme_overridden_renderer_factory_search_paths($component, $subtype, $target);
        $this->assertSame($paths, array (
            'theme_child\\output\\mod_assign\\custom_renderer_ajax',
            'theme_child\\output\\mod_assign\\custom\\renderer_ajax',
            'theme_child_mod_assign_custom_renderer_ajax',
            'theme_parent\\output\\mod_assign\\custom_renderer_ajax',
            'theme_parent\\output\\mod_assign\\custom\\renderer_ajax',
            'theme_parent_mod_assign_custom_renderer_ajax',
            '\\mod_assign\\output\\custom_renderer_ajax',
            '\\mod_assign\\output\\custom\\renderer_ajax',
            'mod_assign_custom_renderer_ajax',
            'theme_child\\output\\mod_assign\\custom_renderer',
            'theme_child\\output\\mod_assign\\custom\\renderer',
            'theme_child_mod_assign_custom_renderer',
            'theme_parent\\output\\mod_assign\\custom_renderer',
            'theme_parent\\output\\mod_assign\\custom\\renderer',
            'theme_parent_mod_assign_custom_renderer',
            '\\mod_assign\\output\\custom_renderer',
            '\\mod_assign\\output\\custom\\renderer',
            'mod_assign_custom_renderer',
        ));
    }
}
