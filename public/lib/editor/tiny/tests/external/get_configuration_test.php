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

declare(strict_types=1);

namespace editor_tiny\external;

use advanced_testcase;
use core_external\external_api;
use editor_tiny\plugininfo\tiny;

/**
 * Unit tests for the editor_tiny\external get_configuration class.
 *
 * @package     editor_tiny
 * @covers      \editor_tiny\external\get_configuration
 * @covers      \editor_tiny\manager::get_plugin_configuration_for_external
 * @covers      \editor_tiny\plugin::is_enabled_for_external
 * @copyright   2025 Moodle Pty Ltd
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class get_configuration_test extends advanced_testcase {

    /**
     * Basic setup for tests.
     */
    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest(true);

        // Global editor settings.
        set_config('branding', false, 'editor_tiny');
        set_config('extended_valid_elements', 'script[*]', 'editor_tiny');

        // Editor plugins.
        foreach (\editor_tiny\plugininfo\tiny::get_enabled_plugins() as $plugin) {
            tiny::enable_plugin($plugin, 0);
        }
        tiny::enable_plugin('h5p', 1); // Plugin with get_plugin_configuration_for_external.
        tiny::enable_plugin('media', 1); // Plugin with is_enabled_for_external.
        tiny::enable_plugin('html', 1); // Plugin with no overriden methods.
    }

    /**
     * Test the external function.
     *
     * @dataProvider execute_provider
     * @param string $contextlevel Context level: system, course or module.
     * @param ?string $role Role name to assign to use. If null, no role is assigned.
     * @return void
     */
    public function test_execute(string $contextlevel, ?string $role): void {
        global $CFG;

        // Setup user and context.
        $generator = $this->getDataGenerator();
        $user = $generator->create_user();

        if ($contextlevel == 'system') {
            $context = \core\context\system::instance();
            if ($role) {
                $generator->role_assign($role, $user->id, $context);
            }
        } else if ($contextlevel == 'course' || $contextlevel == 'module') {
            $course = $generator->create_course();
            if ($contextlevel == 'module') {
                $module = $generator->create_module('forum', ['course' => $course->id]);
                $context = \core\context\module::instance($module->cmid);
            } else {
                $context = \core\context\course::instance($course->id);
            }
            if ($role) {
                $generator->enrol_user($user->id, $course->id, $role);
            } else {
                $this->expectException(\core\exception\require_login_exception::class);
            }
        } else {
            throw new \coding_exception("Invalid context level: {$contextlevel}");
        }

        $this->setUser($user);

        // Execute function.
        $result = get_configuration::execute($contextlevel, (int) $context->instanceid);
        $result = external_api::clean_returnvalue(get_configuration::execute_returns(), $result);

        // Check context id.
        self::assertEquals($context->id, $result['contextid']);

        // Check global settings.
        self::assertEquals(get_config('editor_tiny', 'branding'), $result['branding']);
        self::assertEquals(get_config('editor_tiny', 'extended_valid_elements'), $result['extendedvalidelements']);
        self::assertEquals(self::get_installed_languages(), $result['installedlanguages']);

        // Check plugin settings.
        $plugins = [];
        if (\tiny_h5p\plugininfo::is_enabled($context, ['pluginname' => 'h5p'], [])) {
            $settings = [];
            foreach (\tiny_h5p\plugininfo::get_plugin_configuration_for_external($context) as $name => $value) {
                $settings[] = ['name' => $name, 'value' => $value];
            }
            $plugins[] = ['name' => 'h5p', 'settings' => $settings];
        }
        $plugins[] = ['name' => 'html', 'settings' => []];
        if (\tiny_media\plugininfo::is_enabled_for_external($context, ['plugginname' => 'media'])) {
            $plugins[] = ['name' => 'media', 'settings' => []];
        }
        self::assertEquals($plugins, $result['plugins']);
    }

    /**
     * Data provider for test_execute.
     *
     * @return array
     */
    public static function execute_provider(): array {
        return [
            ['contextlevel' => 'system', 'role' => null],
            ['contextlevel' => 'system', 'role' => 'guest'],
            ['contextlevel' => 'system', 'role' => 'manager'],
            ['contextlevel' => 'course', 'role' => null],
            ['contextlevel' => 'course', 'role' => 'guest'],
            ['contextlevel' => 'course', 'role' => 'student'],
            ['contextlevel' => 'course', 'role' => 'teacher'],
            ['contextlevel' => 'course', 'role' => 'editingteacher'],
            ['contextlevel' => 'module', 'role' => null],
            ['contextlevel' => 'module', 'role' => 'guest'],
            ['contextlevel' => 'module', 'role' => 'student'],
            ['contextlevel' => 'module', 'role' => 'teacher'],
            ['contextlevel' => 'module', 'role' => 'editingteacher'],
        ];
    }

    /**
     * Test the external function with an invalid context level.
     */
    public function test_execute_invalid_context_level(): void {
        $this->expectException(\invalid_parameter_exception::class);

        get_configuration::execute('invalid', (int) SITEID);
    }

    /**
     * Test the external function with an invalid instance ID.
     */
    public function test_execute_invalid_instance_id(): void {
        $this->expectException(\invalid_parameter_exception::class);

        get_configuration::execute('course', -1);
    }

    /**
     * Returns the expected list of installed languages returned by the external function.
     *
     * @return array
     */
    private static function get_installed_languages(): array {
        $installedlanguages = [];
        foreach (get_string_manager()->get_list_of_translations(true) as $lang => $name) {
            $installedlanguages[] = ['lang' => $lang, 'name' => $name];
        }
        return $installedlanguages;
    }
}
