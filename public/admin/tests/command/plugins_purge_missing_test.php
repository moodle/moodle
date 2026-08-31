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

namespace core_admin\command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Tests for the admin:plugins:purge-missing command.
 *
 * @package    core_admin
 * @category   test
 * @copyright  2026 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(plugins_purge_missing::class)]
final class plugins_purge_missing_test extends \advanced_testcase {
    /**
     * Build a mock plugin manager whose get_plugins() returns a controlled map.
     *
     * Each entry in $plugindefs is:
     *   ['component' => ..., 'name' => ..., 'status' => ..., 'canuninstall' => ...]
     *
     * @param array $plugindefs
     * @param int $uninstallcalls Reference counter for uninstall_plugin() calls.
     * @return \core\plugin_manager
     */
    private function build_mock_manager(array $plugindefs, int &$uninstallcalls = 0): \core\plugin_manager {
        $groups = [];
        $canuninstallmap = [];

        foreach ($plugindefs as $def) {
            [$type] = explode('_', $def['component'], 2);
            $canuninstallmap[$def['component']] = $def['canuninstall'] ?? true;

            $info = new class (
                $def['component'],
                $def['name'],
                $def['status'],
            ) extends \core\plugininfo\base {
                /** @var string The plugin status value. */
                private string $statusval;

                /**
                 * Constructor.
                 *
                 * @param string $component The plugin component.
                 * @param string $displayname The display name.
                 * @param string $statusval The plugin status value.
                 */
                public function __construct(
                    string $component,
                    string $displayname,
                    string $statusval,
                ) {
                    // Assign inherited untyped properties directly.
                    $this->component = $component;
                    $this->displayname = $displayname;
                    $this->statusval = $statusval;
                }

                #[\Override]
                public function get_status(): string {
                    return $this->statusval;
                }

                #[\Override]
                public function is_standard(): bool {
                    return false;
                }
            };

            $groups[$type][$def['component']] = $info;
        }

        return new class ($groups, $canuninstallmap, $uninstallcalls) extends \core\plugin_manager {
            /**
             * Constructor.
             *
             * @param array $groups Plugin groups.
             * @param array $canuninstallmap Map of component to canuninstall flag.
             * @param int $callcounter Reference counter for uninstall calls.
             */
            public function __construct(
                /** @var array Plugin groups. */
                private array $groups,
                /** @var array Map of component to canuninstall flag. */
                private array $canuninstallmap,
                /** @var int Reference counter for uninstall calls. */
                private int &$callcounter,
            ) {
                // Skip parent constructor.
            }

            #[\Override]
            public function get_plugins(bool $includeindeprecation = false): array {
                return $this->groups;
            }

            #[\Override]
            public function can_uninstall_plugin($component): bool {
                return $this->canuninstallmap[$component] ?? false;
            }

            #[\Override]
            public function uninstall_plugin($component, \progress_trace $progress): bool {
                $this->callcounter++;
                return true;
            }
        };
    }

    /**
     * Wrap the command with a custom plugin manager.
     *
     * @param \core\plugin_manager $manager
     * @return plugins_purge_missing
     */
    private function build_command(\core\plugin_manager $manager): plugins_purge_missing {
        \core\di::set(\core\plugin_manager::class, $manager);

        return \core\di::get(plugins_purge_missing::class);
    }

    /**
     * Ensure the command exits cleanly when there are no missing plugins.
     */
    public function test_no_missing_plugins(): void {
        $manager = $this->build_mock_manager([
            ['component' => 'mod_forum', 'name' => 'Forum', 'status' => 'uptodate', 'canuninstall' => false],
        ]);

        $tester = new CommandTester($this->build_command($manager));
        $tester->execute([]);

        $tester->assertCommandIsSuccessful();
        $this->assertStringContainsString('No missing plugins found.', $tester->getDisplay());
    }

    /**
     * Ensure missing plugins are listed before the purge is performed.
     */
    public function test_purge_missing_displays_plugins_before_purging(): void {
        $uninstallcalls = 0;
        $manager = $this->build_mock_manager([
            ['component' => 'local_gone', 'name' => 'Gone plugin', 'status' => 'missing', 'canuninstall' => true],
            ['component' => 'mod_forum', 'name' => 'Forum', 'status' => 'uptodate', 'canuninstall' => false],
        ], $uninstallcalls);

        $tester = new CommandTester($this->build_command($manager));
        $tester->execute([]);

        $tester->assertCommandIsSuccessful();
        $this->assertSame(1, $uninstallcalls, 'Only the missing plugin should be uninstalled');
        $output = $tester->getDisplay();
        $this->assertStringContainsString('The following missing plugins will be purged:', $output);
        $this->assertStringContainsString('local_gone (Gone plugin)', $output);
        $this->assertStringContainsString('Uninstalling: local_gone', $output);
    }

    /**
     * Ensure missing plugins that cannot be uninstalled return failure.
     */
    public function test_cannot_uninstall_missing_returns_failure(): void {
        $manager = $this->build_mock_manager([
            ['component' => 'local_locked', 'name' => 'Locked missing', 'status' => 'missing', 'canuninstall' => false],
        ]);

        $tester = new CommandTester($this->build_command($manager));
        $tester->execute([]);

        $this->assertSame(Command::FAILURE, $tester->getStatusCode());
        $this->assertStringContainsString('Cannot uninstall: local_locked', $tester->getDisplay());
    }

    /**
     * Ensure plugins that are present on disk are ignored during purge.
     */
    public function test_skips_non_missing_plugins(): void {
        $uninstallcalls = 0;
        $manager = $this->build_mock_manager([
            ['component' => 'local_gone', 'name' => 'Gone', 'status' => 'missing', 'canuninstall' => true],
            ['component' => 'local_alive', 'name' => 'Alive', 'status' => 'uptodate', 'canuninstall' => true],
        ], $uninstallcalls);

        $tester = new CommandTester($this->build_command($manager));
        $tester->execute([]);

        $tester->assertCommandIsSuccessful();
        $this->assertSame(1, $uninstallcalls);
        $this->assertStringContainsString('local_gone', $tester->getDisplay());
        $this->assertStringNotContainsString('local_alive', $tester->getDisplay());
    }
}
