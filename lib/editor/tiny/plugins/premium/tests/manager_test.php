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

namespace tiny_premium;

/**
 * Manager tests class for tiny_premium.
 *
 * @package    tiny_premium
 * @category   test
 * @copyright  2024 David Woloszyn <david.woloszyn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class manager_test extends \advanced_testcase {

    /**
     * Test the getting of all available Tiny Premium plugins.
     *
     * @covers \tiny_premium\manager
     */
    public function test_get_plugins(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        // Check all Tiny Premium plugins are returned.
        $premiumplugins = manager::get_plugins();
        $this->assertCount(15, $premiumplugins);
    }

    /**
     * Test the getting and setting of enabled Tiny Premium plugins.
     *
     * @covers \tiny_premium\manager
     */
    public function test_get_and_set_enabled_plugins(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        // Check all enabled Tiny Premium plugins are returned (all disabled by default).
        $enabledpremiumplugins = manager::get_enabled_plugins();
        $this->assertCount(0, $enabledpremiumplugins);

        // Enable a couple premium plugins.
        manager::set_plugin_config(['enabled' => 1], 'advtable');
        manager::set_plugin_config(['enabled' => 1], 'formatpainter');
        // Check the premium plugins are enabled.
        $enabledpremiumplugins = manager::get_enabled_plugins();
        $this->assertCount(2, $enabledpremiumplugins);
        $this->assertTrue(manager::is_plugin_enabled('advtable'));
        $this->assertTrue(manager::is_plugin_enabled('formatpainter'));

        // Disable a premium plugin.
        manager::set_plugin_config(['enabled' => 0], 'advtable');
        // Check the correct premium plugins are enabled.
        $enabledpremiumplugins = manager::get_enabled_plugins();
        $this->assertCount(1, $enabledpremiumplugins);
        $this->assertFalse(manager::is_plugin_enabled('advtable'));
        $this->assertTrue(manager::is_plugin_enabled('formatpainter'));
    }
}
