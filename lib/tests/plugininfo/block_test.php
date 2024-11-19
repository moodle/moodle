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

namespace core\plugininfo;

use advanced_testcase;

/**
 * Unit tests for the mod plugininfo class
 *
 * @package     core
 * @covers      \core\plugininfo\block
 * @copyright   2023 Andrew Lyons <andrew@nicols.co.uk>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_test extends advanced_testcase {

    /**
     * Test the get_enabled_plugins method.
     */
    public function test_get_enabled_plugins(): void {
        $this->resetAfterTest();

        // The bigbluebuttonbn plugin is disabled by default.
        // Check all default formats.
        $plugins = block::get_enabled_plugins();
        $this->assertArrayHasKey('badges', $plugins);
        $this->assertArrayHasKey('timeline', $plugins);
        $this->assertArrayHasKey('admin_bookmarks', $plugins);

        // Disable a plugin.
        block::enable_plugin('timeline', 0);

        $plugins = block::get_enabled_plugins();
        $this->assertArrayHasKey('badges', $plugins);
        $this->assertArrayNotHasKey('timeline', $plugins);
        $this->assertArrayHasKey('admin_bookmarks', $plugins);
    }

    /**
     * Test the is_uninstall_allowed method.
     *
     * @dataProvider is_uninstall_allowed_provider
     * @param string $plugin
     * @param bool $expected
     */
    public function test_is_uninstall_allowed(
        string $plugin,
        bool $expected,
    ): void {
        $pluginmanager = \core_plugin_manager::instance();
        $plugininfo = $pluginmanager->get_plugin_info("block_{$plugin}");
        $this->assertEquals($expected, $plugininfo->is_uninstall_allowed());
    }

    public static function is_uninstall_allowed_provider(): array {
        $plugins = block::get_enabled_plugins();
        return array_map(function ($plugin) {
            $expected = true;
            if ($plugin === 'settings' || $plugin === 'navigation') {
                $expected = false;
            }
            return [
                'plugin' => $plugin,
                'expected' => $expected,
            ];
        }, array_keys($plugins));
    }
}
