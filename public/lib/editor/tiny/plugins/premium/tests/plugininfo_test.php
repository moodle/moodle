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
        $generator = $this->getDataGenerator();
        $user = $generator->create_user();
        $context = \context_system::instance();
        $this->setUser($user);

        $configs = plugininfo::get_plugin_configuration_for_external($context);
        $this->assertArrayHasKey('premiumplugins', $configs);
        $this->assertArrayHasKey('serviceurls', $configs);
        $this->assertEquals(implode(',', \tiny_premium\manager::get_plugins()), $configs['premiumplugins']);
    }

    /**
     * Test that capability filtering removes prohibited plugins from the external configuration.
     *
     * advtable is used as the "still present" anchor because it has no per-plugin capability
     * restriction and is alphabetically first in the plugin list, making it a stable reference
     * for asserting that non-prohibited plugins are unaffected by the filtering.
     *
     * @return void
     */
    public function test_get_plugin_configuration_filters_by_user_capability(): void {
        $generator = $this->getDataGenerator();
        $user = $generator->create_user();
        $roleid = $generator->create_role();
        assign_capability('tiny/premium:usemarkdown', CAP_PROHIBIT, $roleid, \context_system::instance()->id);
        role_assign($roleid, $user->id, \context_system::instance()->id);

        $context = \context_system::instance();
        $this->setUser($user);

        $configs = plugininfo::get_plugin_configuration_for_external($context);
        $this->assertContains('advtable', explode(',', $configs['premiumplugins']));
        $this->assertNotContains('markdown', explode(',', $configs['premiumplugins']));
    }
}
