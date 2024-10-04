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
 * Unit tests for the media plugininfo class.
 *
 * @package     core
 * @covers      \core\plugininfo\media
 * @copyright   2023 Andrew Lyons <andrew@nicols.co.uk>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class media_test extends advanced_testcase {

    /**
     * Test the get_enabled_plugins method.
     */
    public function test_get_enabled_plugins(): void {
        $this->resetAfterTest();

        $plugins = media::get_enabled_plugins();
        $this->assertArrayHasKey('videojs', $plugins);
        $this->assertArrayHasKey('youtube', $plugins);

        // Disable a plugin.
        media::enable_plugin('youtube', 0);

        $plugins = media::get_enabled_plugins();
        $this->assertArrayNotHasKey('youtube', $plugins);
        $this->assertArrayHasKey('videojs', $plugins);
    }

    /**
     * Test the is_uninstall_allowed method.
     *
     * @dataProvider is_uninstall_allowed_provider
     * @param string $plugin
     */
    public function test_is_uninstall_allowed(
        string $plugin,
    ): void {
        $pluginmanager = \core_plugin_manager::instance();
        $plugininfo = $pluginmanager->get_plugin_info("media_{$plugin}");
        $this->assertTrue($plugininfo->is_uninstall_allowed());
    }

    /**
     * Data provider for the is_uninstall_allowed tests.
     *
     * @return array
     */
    public static function is_uninstall_allowed_provider(): array {
        $plugins = media::get_enabled_plugins();
        return array_map(function ($plugin) {
            return [
                'plugin' => $plugin,
            ];
        }, array_keys($plugins));
    }

    /**
     * Ensure that change_plugin_order() changes the order of the plugins.
     *
     * @dataProvider change_plugin_order_provider
     * @param string $initialorder
     * @param string $pluginname
     * @param int $direction
     * @param array $expected
     */
    public function test_change_plugin_order(
        array $initialorder,
        string $pluginname,
        int $direction,
        array $expected,
    ): void {
        $this->resetAfterTest(true);

        media::set_enabled_plugins($initialorder);
        media::change_plugin_order($pluginname, $direction);

        $this->assertSame(
            $expected,
            array_keys(media::get_sorted_plugins()),
        );
    }

    public static function change_plugin_order_provider(): array {
        $pluginmanager = \core_plugin_manager::instance();
        $allplugins = $pluginmanager->get_plugins_of_type('media');
        \core_collator::asort_objects_by_method($allplugins, 'get_rank', \core_collator::SORT_NUMERIC);
        $getorder = function (array $plugins) use ($allplugins) {
            return array_merge(
                $plugins,
                array_diff(array_reverse(array_keys($allplugins)), array_values($plugins)),
            );
        };

        return [
            'Top item down one' => [
                'initialorder' => [
                    'videojs',
                    'html5audio',
                    'html5video',
                    'youtube',
                ],
                'pluginname' => 'videojs',
                'direction' => base::MOVE_DOWN,
                'expected' => $getorder([
                    'html5audio',
                    'videojs',
                    'html5video',
                    'youtube',
                ]),
            ],
            'Bottom item down one' => [
                'initialorder' => [
                    'videojs',
                    'html5audio',
                    'html5video',
                    'youtube',
                ],
                'pluginname' => 'youtube',
                'direction' => base::MOVE_DOWN,
                'expected' => $getorder([
                    'videojs',
                    'html5audio',
                    'html5video',
                    'youtube',
                ]),
            ],
            'Top item up' => [
                'initialorder' => [
                    'videojs',
                    'html5audio',
                    'html5video',
                    'youtube',
                ],
                'pluginname' => 'videojs',
                'direction' => base::MOVE_UP,
                'expected' => $getorder([
                    'videojs',
                    'html5audio',
                    'html5video',
                    'youtube',
                ]),
            ],
            'Disabled plugin' => [
                'initialorder' => [
                    'videojs',
                    'html5audio',
                    'html5video',
                ],
                'pluginname' => 'youtube',
                'direction' => base::MOVE_UP,
                'expected' => $getorder([
                    'videojs',
                    'html5audio',
                    'html5video',
                ]),
            ],
            'Non-existent plugin' => [
                'initialorder' => [
                    'videojs',
                    'html5audio',
                    'html5video',
                    'youtube',
                ],
                'pluginname' => 'moodletube',
                'direction' => base::MOVE_UP,
                'expected' => $getorder([
                    'videojs',
                    'html5audio',
                    'html5video',
                    'youtube',
                ]),
            ],
        ];
    }
}
