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
 * Unit tests to configure the enabled/disabled state of a plugin.
 *
 * @package     core
 * @covers      \core_admin\external\set_plugin_state
 * @copyright   2023 Andrew Lyons <andrew@nicols.co.uk>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class set_plugin_state_test extends \core_external\tests\externallib_testcase {
    /**
     * Text execute method.
     *
     * @dataProvider execute_standard_provider
     * @param string $plugin The name of the plugin
     * @param int|null $initialstate The initial state of the plugin
     * @param int $newstate The target state
     * @param int $notificationcount The number of notifications expected
     */
    public function test_execute(
        string $plugin,
        ?int $initialstate,
        int $newstate,
        int $notificationcount,
    ): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        if ($initialstate !== null) {
            [$plugintype, $pluginname] = \core_component::normalize_component($plugin);
            $manager = \core_plugin_manager::resolve_plugininfo_class($plugintype);
            $manager::enable_plugin($pluginname, $initialstate);
            \core\notification::fetch();
        }

        set_plugin_state::execute($plugin, $newstate);
        $this->assertCount($notificationcount, \core\notification::fetch());
    }

    /**
     * Data provider for base tests of the execute method.
     *
     * @return array
     */
    public static function execute_standard_provider(): array {
        $generatetestsfor = function (string $plugin): array {
            return [
                [
                    'plugin' => $plugin,
                    'initialstate' => null,
                    'newstate' => 0,
                    'notificationcount' => 1,
                ],
                [
                    'plugin' => $plugin,
                    'initialstate' => 1,
                    'newstate' => 0,
                    'notificationcount' => 1,
                ],
                [
                    'plugin' => $plugin,
                    'initialstate' => 1,
                    'newstate' => 1,
                    'notificationcount' => 0,
                ],
                [
                    'plugin' => $plugin,
                    'initialstate' => 0,
                    'newstate' => 0,
                    'notificationcount' => 0,
                ],
                [
                    'plugin' => $plugin,
                    'initialstate' => 0,
                    'newstate' => 1,
                    'notificationcount' => 1,
                ],
            ];
        };

        return array_merge(
            $generatetestsfor('mod_assign'),
            $generatetestsfor('editor_tiny'),
            $generatetestsfor('tiny_h5p'),
            $generatetestsfor('block_badges'),
        );
    }

    /**
     * Test execute method with no login.
     */
    public function test_execute_no_login(): void {
        $this->expectException(\require_login_exception::class);
        set_plugin_state::execute('mod_assign', 1);
    }

    /**
     * Test execute method with no login.
     */
    public function test_execute_no_capability(): void {
        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);
        $this->expectException(\required_capability_exception::class);
        set_plugin_state::execute('mod_assign', 1);
    }
}
