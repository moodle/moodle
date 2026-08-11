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

namespace editor_tiny;

use advanced_testcase;

/**
 * Unit tests for the editor_tiny manager class.
 *
 * @package     editor_tiny
 * @covers      \editor_tiny\manager
 * @copyright   2026 Matt Porritt <matt.porritt@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class manager_test extends advanced_testcase {
    /**
     * Test that the accordion and advlist plugins are present in the enabled plugin configuration.
     *
     * @dataProvider bundled_plugins_provider
     * @param string $plugin Plugin name expected to be enabled.
     */
    public function test_bundled_plugins_are_enabled(string $plugin): void {
        $this->resetAfterTest();

        $manager = new manager();
        $context = \context_system::instance();
        $config = $manager->get_plugin_configuration($context);

        $this->assertArrayHasKey($plugin, $config, "Plugin '{$plugin}' should be present in the TinyMCE plugin configuration.");
    }

    /**
     * Data provider for test_bundled_plugins_are_enabled.
     *
     * @return array
     */
    public static function bundled_plugins_provider(): array {
        return [
            'accordion' => ['accordion'],
            'advlist'   => ['advlist'],
        ];
    }

    /**
     * Test that the accordion plugin registers the expected toolbar buttons.
     */
    public function test_accordion_registers_buttons(): void {
        $this->resetAfterTest();

        $manager = new manager();
        $context = \context_system::instance();
        $config = $manager->get_plugin_configuration($context);

        $this->assertArrayHasKey('accordion', $config);
        $this->assertArrayHasKey('buttons', $config['accordion']);
        $this->assertContains('accordion', $config['accordion']['buttons']);
        $this->assertContains('accordiontoggle', $config['accordion']['buttons']);
        $this->assertContains('accordionremove', $config['accordion']['buttons']);
    }

    /**
     * Test that the accordion plugin registers the expected menu item in the insert menu.
     */
    public function test_accordion_registers_menu_item(): void {
        $this->resetAfterTest();

        $manager = new manager();
        $context = \context_system::instance();
        $config = $manager->get_plugin_configuration($context);

        $this->assertArrayHasKey('accordion', $config);
        $this->assertArrayHasKey('menuitems', $config['accordion']);
        $this->assertArrayHasKey('accordion', $config['accordion']['menuitems']);
        $this->assertEquals('insert', $config['accordion']['menuitems']['accordion']);
    }

    /**
     * Test that the lists plugin (advlist dependency) is also present in the configuration.
     */
    public function test_lists_plugin_is_present_for_advlist(): void {
        $this->resetAfterTest();

        $manager = new manager();
        $context = \context_system::instance();
        $config = $manager->get_plugin_configuration($context);

        $this->assertArrayHasKey('lists', $config, 'The lists plugin must be enabled as advlist depends on it.');
    }
}
