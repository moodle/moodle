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

namespace core_admin\external;

/**
 * Unit tests to configure the plugin order.
 *
 * Note: Not all plugins can be ordered, so this test is limited to those which support it.
 *
 * @package     core
 * @covers      \core_admin\external\set_plugin_state
 * @copyright   2023 Andrew Lyons <andrew@nicols.co.uk>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class set_plugin_order_test extends \core_external\tests\externallib_testcase {
    /**
     * Text execute method for editor plugins, which support ordering.
     *
     * @dataProvider execute_editor_provider
     * @param string $initialstate The initial state of the plugintype
     * @param string $plugin The name of the plugin
     * @param int $direction
     * @param array $neworder
     * @param string $newstate
     */
    public function test_execute_editors(
        string $initialstate,
        string $plugin,
        int $direction,
        array $neworder,
        string $newstate,
    ): void {
        global $CFG;

        $this->resetAfterTest();
        $this->setAdminUser();

        $CFG->texteditors = $initialstate;

        set_plugin_order::execute($plugin, $direction);

        $this->assertSame(
            $neworder,
            array_keys(\core\plugininfo\editor::get_sorted_plugins()),
        );
        $this->assertSame($newstate, $CFG->texteditors);
    }

    /**
     * Data provider for base tests of the execute method.
     *
     * @return array
     */
    public static function execute_editor_provider(): array {
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
                'initialstate' => 'textarea,tiny',
                'plugin' => 'editor_textarea',
                'direction' => 1, // DOWN.
                'neworder' => $getorder([
                    'tiny',
                    'textarea',
                ]),
                'newstate' => 'tiny,textarea',
            ],
            [
                'initialstate' => 'textarea,tiny',
                'plugin' => 'editor_textarea',
                'direction' => -1, // UP.
                'neworder' => $getorder([
                    'textarea',
                    'tiny',
                ]),
                'newstate' => 'textarea,tiny',
            ],
            [
                'initialstate' => 'textarea,tiny',
                'plugin' => 'editor_tiny',
                'direction' => 1, // DOWN.
                // Tiny is already at the bottom of the list of enabled plugins.
                'neworder' => $getorder([
                    'textarea',
                    'tiny',
                ]),
                'newstate' => 'textarea,tiny',
            ],
            [
                'initialstate' => 'textarea,tiny',
                'plugin' => 'editor_atto',
                'direction' => 1, // DOWN.
                // Atto is not enabled. Disabled editors are listed lexically after enabled editors.
                'neworder' => $getorder([
                    'textarea',
                    'tiny',
                ]),
                'newstate' => 'textarea,tiny',
            ],
        ];
    }

    /**
     * Test re-ordering plugins where one plugin is not enabled.
     *
     *  Media plugins are ordered by rank, with enabled plugins first.
     * This is similar to the editors test but covers a scenario that cannot be covered by the editors test due to
     * not having enough plugins.
     */
    public function test_execute_media_including_disabled(): void {
        global $CFG;

        $this->resetAfterTest();
        $this->setAdminUser();

        $CFG->media_plugins_sortorder = 'videojs,vimeo,html5video';

        set_plugin_order::execute('youtube', -1);

        $this->assertSame('videojs,vimeo,html5video', $CFG->media_plugins_sortorder);
    }

    /**
     * Text execute method for plugins which do not support ordering.
     *
     * @dataProvider execute_non_orderable_provider
     * @param string $plugin
     */
    public function test_execute_editors_non_orderable(string $plugin): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $this->assertIsArray(set_plugin_order::execute($plugin, 1));
    }

    public static function execute_non_orderable_provider(): array {
        return [
            // Activities do not support ordering.
            ['mod_assign'],
            // Nor to blocks.
            ['block_login'],
        ];
    }

    /**
     * Test execute method with no login.
     */
    public function test_execute_no_login(): void {
        $this->expectException(\require_login_exception::class);
        set_plugin_order::execute('editor_tiny', 1);
    }

    /**
     * Test execute method with no login.
     */
    public function test_execute_no_capability(): void {
        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);
        $this->expectException(\required_capability_exception::class);
        set_plugin_order::execute('editor_tiny', 1);
    }
}
