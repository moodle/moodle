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

namespace editor_tiny\plugininfo;

use advanced_testcase;

/**
 * Unit tests for the editor_tiny\tiny plugininfo class.
 *
 * @package     editor_tiny
 * @covers      \editor_tiny\plugininfo\tiny
 * @copyright   2023 Andrew Lyons <andrew@nicols.co.uk>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tiny_test extends advanced_testcase {
    /**
     * Uninstall is allowed of TinyMCE plugins.
     *
     * @covers ::is_uninstall_allowed
     */
    public function test_is_uninstall_allowed(): void {
        $instance = new tiny();
        $this->assertTrue($instance->is_uninstall_allowed());
    }

    /**
     * Check the manage URL.
     *
     * @covers ::get_manage_url
     */
    public function test_get_manage_url(): void {
        $this->assertInstanceOf(\moodle_url::class, tiny::get_manage_url());
    }

    /**
     * Test the get_enabled_plugins method.
     *
     * @covers ::get_enabled_plugin
     */
    public function test_get_enabled_plugins(): void {
        $this->resetAfterTest();

        $plugins = tiny::get_enabled_plugins();
        $this->assertArrayHasKey('recordrtc', $plugins);
        $this->assertArrayHasKey('media', $plugins);
        $this->assertArrayHasKey('autosave', $plugins);
        $this->assertArrayHasKey('h5p', $plugins);

        // Disable a plugin.
        tiny::enable_plugin('h5p', 0);

        $plugins = tiny::get_enabled_plugins();
        $this->assertArrayHasKey('recordrtc', $plugins);
        $this->assertArrayHasKey('media', $plugins);
        $this->assertArrayHasKey('autosave', $plugins);
        $this->assertArrayNotHasKey('h5p', $plugins);
    }

    /**
     * Ensure that the base implementation is used for plugins not supporting ordering.
     */
    public function test_sorting_not_supported(): void {
        $this->assertFalse(tiny::plugintype_supports_ordering());

        $this->assertNull(tiny::get_sorted_plugins());
        $this->assertNull(tiny::get_sorted_plugins(true));
        $this->assertNull(tiny::get_sorted_plugins(false));

        $this->assertFalse(tiny::change_plugin_order('tiny_h5p', \core\plugininfo\base::MOVE_UP));
        $this->assertFalse(tiny::change_plugin_order('tiny_h5p', \core\plugininfo\base::MOVE_DOWN));
    }
}
