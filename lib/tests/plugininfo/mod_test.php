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
 * @covers      \core\plugininfo\mod
 * @copyright   2023 Andrew Lyons <andrew@nicols.co.uk>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_test extends advanced_testcase {
    public function test_get_enabled_plugins(): void {
        $this->resetAfterTest();

        // The bigbluebuttonbn and chat plugins are disabled by default.
        // Check all default formats.
        $plugins = mod::get_enabled_plugins();
        $this->assertArrayHasKey('assign', $plugins);
        $this->assertArrayHasKey('forum', $plugins);
        $this->assertArrayNotHasKey('chat', $plugins);
        $this->assertArrayNotHasKey('bigbluebuttonbn', $plugins);

        // Disable assignment.
        mod::enable_plugin('assign', 0);

        $plugins = mod::get_enabled_plugins();
        $this->assertArrayHasKey('forum', $plugins);
        $this->assertArrayNotHasKey('assign', $plugins);
        $this->assertArrayNotHasKey('chat', $plugins);
        $this->assertArrayNotHasKey('bigbluebuttonbn', $plugins);
    }
}
