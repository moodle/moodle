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
 * Unit tests for the editor plugininfo class
 *
 * @package     core
 * @covers      \core\plugininfo\editor
 * @copyright   2023 Andrew Lyons <andrew@nicols.co.uk>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class editor_test extends advanced_testcase {

    /**
     * Test that editor::get_enabled_plugins() returns the correct list of enabled plugins.
     */
    public function test_get_enabled_plugins(): void {
        $this->resetAfterTest();

        // All plugins are enabled by default.
        $plugins = editor::get_enabled_plugins();
        $this->assertArrayHasKey('tiny', $plugins);
        $this->assertArrayHasKey('textarea', $plugins);

        // Disable tiny.
        editor::enable_plugin('textarea', 0);

        $plugins = editor::get_enabled_plugins();
        $this->assertArrayHasKey('tiny', $plugins);
        $this->assertArrayNotHasKey('textarea', $plugins);
    }

    /**
     * Test that editor::enable_plugin set to disable all plugins will leave the textarea enabled.
     */
    public function test_enable_plugin_all(): void {
        $this->resetAfterTest();

        // All plugins are enabled by default.
        $plugins = editor::get_enabled_plugins();
        foreach ($plugins as $plugin) {
            editor::enable_plugin($plugin, 0);
        }

        $plugins = editor::get_enabled_plugins();
        $this->assertCount(1, $plugins);
        $this->assertArrayHasKey('textarea', $plugins);
    }
}
