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

    /**
     * Ensure that plugintype_supports_ordering() returns true.
     */
    public function test_plugintype_supports_ordering(): void {
        $this->assertTrue(editor::plugintype_supports_ordering());
    }

    /**
     * Ensure that get_sorted_plugins() returns the correct list of plugins.
     *
     * @dataProvider get_sorted_plugins_provider
     * @param string $texteditors The $CFG->texteditors value to use as a base
     * @param bool $enabledonly
     * @param array $expected The expected order
     */
    public function test_get_sorted_plugins(
        string $texteditors,
        bool $enabledonly,
        array $expected,
    ): void {
        global $CFG;
        $this->resetAfterTest(true);

        $CFG->texteditors = $texteditors;
        $this->assertSame(
            $expected,
            array_keys(editor::get_sorted_plugins($enabledonly)),
        );
    }

    /**
     * Data provider for the get_sorted_plugins tests.
     *
     * @return array
     */
    public function get_sorted_plugins_provider(): array {
        $pluginmanager = \core_plugin_manager::instance();
        $allplugins = array_keys($pluginmanager->get_plugins_of_type('editor'));

        // Disabled editors are listed alphabetically at the end.
        $getorder = function (array $plugins) use ($allplugins) {
            return array_merge(
                $plugins,
                array_diff($allplugins, array_values($plugins)),
            );
        };
        return [
            [
                'texteditors' => 'textarea,tiny',
                'enabledonly' => true,
                'expected' => [
                    'textarea',
                    'tiny',
                ],
            ],
            [
                'texteditors' => 'tiny,textarea',
                'enabledonly' => true,
                'expected' => [
                    'tiny',
                    'textarea',
                ],
            ],
            [
                'texteditors' => 'tiny',
                'enabledonly' => true,
                'expected' => [
                    'tiny',
                ],
            ],
            'Phantom values are removed from the list' => [
                'texteditors' => 'fakeeditor',
                'enabledonly' => true,
                'expected' => [
                ],
            ],
            [
                'texteditors' => 'textarea,tiny',
                'enabledonly' => false,
                'expected' => $getorder([
                    'textarea',
                    'tiny',
                ]),
            ],
            [
                'texteditors' => 'tiny',
                'enabledonly' => false,
                'expected' => $getorder([
                    'tiny',
                ]),
            ],
        ];
    }

    /**
     * Ensure that change_plugin_order() changes the order of the plugins.
     *
     * @dataProvider change_plugin_order_provider
     * @param string $texteditors
     * @param string $pluginname
     * @param int $direction
     * @param array $neworder
     * @param string $newtexteditors
     */
    public function test_change_plugin_order(
        string $texteditors,
        string $pluginname,
        int $direction,
        array $neworder,
        string $newtexteditors,
    ): void {
        global $CFG;
        $this->resetAfterTest(true);

        $CFG->texteditors = $texteditors;
        editor::change_plugin_order($pluginname, $direction);

        $this->assertSame(
            $neworder,
            array_keys(editor::get_sorted_plugins()),
        );
        $this->assertSame($newtexteditors, $CFG->texteditors);
    }

    /**
     * Data provider fro the change_plugin_order() tests.
     *
     * @return array
     */
    public function change_plugin_order_provider(): array {
        $pluginmanager = \core_plugin_manager::instance();
        $allplugins = array_keys($pluginmanager->get_plugins_of_type('editor'));

        // Disabled editors are listed alphabetically at the end.
        $getorder = function (array $plugins) use ($allplugins) {
            return array_merge(
                $plugins,
                array_diff($allplugins, array_values($plugins)),
            );
        };
        return [
            [
                'texteditors' => 'textarea,tiny',
                'pluginname' => 'textarea',
                'direction' => base::MOVE_DOWN,
                'expected' => $getorder([
                    'tiny',
                    'textarea',
                ]),
                'newtexteditors' => 'tiny,textarea',
            ],
            [
                'texteditors' => 'textarea,tiny',
                'pluginname' => 'tiny',
                'direction' => base::MOVE_DOWN,
                // Tiny is already at the bottom of the enabled plugins.
                'expected' => $getorder([
                    'textarea',
                    'tiny',
                ]),
                'newtexteditors' => 'textarea,tiny',
            ],
            [
                'texteditors' => 'textarea,tiny',
                'pluginname' => 'atto',
                'direction' => base::MOVE_DOWN,
                // Atto is not enabled. No change expected.
                'expected' => $getorder([
                    'textarea',
                    'tiny',
                ]),
                'newtexteditors' => 'textarea,tiny',
            ],
            [
                'texteditors' => 'textarea,tiny',
                'pluginname' => 'tiny',
                'direction' => base::MOVE_UP,
                'expected' => $getorder([
                    'tiny',
                    'textarea',
                ]),
                'newtexteditors' => 'tiny,textarea',
            ],
            [
                'texteditors' => 'textarea,tiny',
                'pluginname' => 'tiny',
                'direction' => base::MOVE_UP,
                // Tiny is already at the top of the enabled plugins.
                'expected' => $getorder([
                    'tiny',
                    'textarea',
                ]),
                'newtexteditors' => 'tiny,textarea',
            ],
            [
                'texteditors' => 'textarea,tiny',
                'pluginname' => 'atto',
                'direction' => base::MOVE_UP,
                // Atto is not enabled. No change expected.
                'expected' => $getorder([
                    'textarea',
                    'tiny',
                ]),
                'newtexteditors' => 'textarea,tiny',
            ],
            [
                'texteditors' => 'textarea,tiny',
                'pluginname' => 'atto',
                'direction' => base::MOVE_UP,
                // Atto is not enabled. No change expected.
                'expected' => $getorder([
                    'textarea',
                    'tiny',
                ]),
                'newtexteditors' => 'textarea,tiny',
            ],
            [
                'texteditors' => 'textarea,tiny',
                'pluginname' => 'fakeeditor',
                'direction' => base::MOVE_UP,
                // The fakeeditor plugin does not exist. No change expected.
                'expected' => $getorder([
                    'textarea',
                    'tiny',
                ]),
                'newtexteditors' => 'textarea,tiny',
            ],
        ];
    }
}
