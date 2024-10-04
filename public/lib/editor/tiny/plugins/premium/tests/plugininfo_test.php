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

namespace tiny_premium;

use advanced_testcase;

/**
 * Unit tests for the \tiny_premium\plugininfo class.
 *
 * @package     tiny_premium
 * @covers      \tiny_premium\plugininfo::get_plugin_configuration_for_external
 * @copyright   2025 Moodle Pty Ltd
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class plugininfo_test extends advanced_testcase {

    /**
     * Basic setup for tests.
     */
    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest(true);

        foreach (\tiny_premium\manager::get_plugins() as $plugin) {
            \tiny_premium\manager::set_plugin_config(['enabled' => 1], $plugin);
        }
    }

    /**
     * Test the get_plugin_configuration_for_external method.
     *
     * @return void
     */
    public function test_get_plugin_configuration_for_external(): void {
        global $CFG;

        $generator = $this->getDataGenerator();
        $user = $generator->create_user();
        $context = \context_system::instance();
        $this->setUser($user);

        $this->assertEquals(
            ['premiumplugins' => implode(',', \tiny_premium\manager::get_plugins())],
            plugininfo::get_plugin_configuration_for_external($context)
        );
    }
}
