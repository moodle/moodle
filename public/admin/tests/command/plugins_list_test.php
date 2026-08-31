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
 * Tests for the admin:plugins:list command.
 *
 * @package    core_admin
 * @category   test
 * @copyright  2026 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(plugins_list::class)]
final class plugins_list_test extends \advanced_testcase {
    /**
     * Build a simple mock plugin manager with a fixed set of plugins.
     *
     * @param array $plugindefs Array of ['component' => string, 'name' => string, 'status' => string, 'isstandard' => bool]
     * @return \core\plugin_manager
     */
    private function build_mock_manager(array $plugindefs): \core\plugin_manager {
        // Build mock plugin info objects.
        $groups = [];
        foreach ($plugindefs as $def) {
            [$type] = explode('_', $def['component'], 2);
            $info = new class (
                $def['component'],
                $def['name'],
                $def['status'],
                $def['isstandard'] ?? false,
            ) extends \core\plugininfo\base {
                /** @var string The plugin status value. */
                private string $statusval;
                /** @var bool Whether the plugin is a standard plugin. */
                private bool $standard;

                /**
                 * Constructor.
                 *
                 * @param string $component The plugin component.
                 * @param string $displayname The display name.
                 * @param string $statusval The plugin status value.
                 * @param bool $standard Whether the plugin is standard.
                 */
                public function __construct(
                    string $component,
                    string $displayname,
                    string $statusval,
                    bool $standard,
                ) {
                    // Assign inherited untyped properties directly.
                    $this->component = $component;
                    $this->displayname = $displayname;
                    $this->statusval = $statusval;
                    $this->standard = $standard;
                }

                #[\Override]
                public function get_status(): string {
                    return $this->statusval;
                }

                #[\Override]
                public function is_standard(): bool {
                    return $this->standard;
                }
            };
            $groups[$type][$def['component']] = $info;
        }

        return new class ($groups) extends \core\plugin_manager {
            /**
             * Constructor.
             *
             * @param array $groups Plugin groups.
             */
            public function __construct(
                /** @var array Plugin groups. */
                private array $groups,
            ) {
                // Skip parent constructor.
            }

            #[\Override]
            public function get_plugins(bool $includeindeprecation = false): array {
                return $this->groups;
            }
        };
    }

    /**
     * Build the command with a given mock manager.
     *
     * @param \core\plugin_manager $manager
     * @return plugins_list
     */
    private function build_command(\core\plugin_manager $manager): plugins_list {
        \core\di::set(\core\plugin_manager::class, $manager);

        return \core\di::get(plugins_list::class);
    }

    /**
     * Ensure all installed plugins are listed when no filters are supplied.
     */
    public function test_list_all_plugins(): void {
        $manager = $this->build_mock_manager([
            ['component' => 'mod_forum', 'name' => 'Forum', 'status' => 'uptodate', 'isstandard' => true],
            ['component' => 'local_test', 'name' => 'Test plugin', 'status' => 'uptodate', 'isstandard' => false],
        ]);

        $tester = new CommandTester($this->build_command($manager));
        $tester->execute([]);

        $tester->assertCommandIsSuccessful();
        $output = $tester->getDisplay();
        $this->assertStringContainsString('mod_forum', $output);
        $this->assertStringContainsString('local_test', $output);
        $this->assertStringContainsString('2 plugin(s) listed.', $output);
    }

    /**
     * Ensure only contrib (non-standard) plugins are listed when --contrib is supplied.
     */
    public function test_list_contrib_only(): void {
        $manager = $this->build_mock_manager([
            ['component' => 'mod_forum', 'name' => 'Forum', 'status' => 'uptodate', 'isstandard' => true],
            ['component' => 'local_test', 'name' => 'Test plugin', 'status' => 'uptodate', 'isstandard' => false],
        ]);

        $tester = new CommandTester($this->build_command($manager));
        $tester->execute(['--contrib' => true]);

        $tester->assertCommandIsSuccessful();
        $output = $tester->getDisplay();
        $this->assertStringNotContainsString('mod_forum', $output);
        $this->assertStringContainsString('local_test', $output);
        $this->assertStringContainsString('1 plugin(s) listed.', $output);
    }

    /**
     * Ensure only missing plugins are listed when --missing is supplied.
     */
    public function test_list_missing_only(): void {
        $manager = $this->build_mock_manager([
            ['component' => 'mod_forum', 'name' => 'Forum', 'status' => 'uptodate', 'isstandard' => true],
            ['component' => 'local_gone', 'name' => 'Gone plugin', 'status' => 'missing', 'isstandard' => false],
        ]);

        $tester = new CommandTester($this->build_command($manager));
        $tester->execute(['--missing' => true]);

        $tester->assertCommandIsSuccessful();
        $output = $tester->getDisplay();
        $this->assertStringNotContainsString('mod_forum', $output);
        $this->assertStringContainsString('local_gone', $output);
        $this->assertStringContainsString('1 plugin(s) listed.', $output);
    }

    /**
     * Ensure combining --contrib and --missing lists only plugins matching both criteria.
     */
    public function test_list_contrib_and_missing_combined(): void {
        $manager = $this->build_mock_manager([
            ['component' => 'mod_forum', 'name' => 'Forum', 'status' => 'uptodate', 'isstandard' => true],
            ['component' => 'local_gone', 'name' => 'Gone contrib', 'status' => 'missing', 'isstandard' => false],
            ['component' => 'local_alive', 'name' => 'Live contrib', 'status' => 'uptodate', 'isstandard' => false],
        ]);

        $tester = new CommandTester($this->build_command($manager));
        $tester->execute(['--contrib' => true, '--missing' => true]);

        $tester->assertCommandIsSuccessful();
        $output = $tester->getDisplay();
        // Only the contrib + missing plugin should appear.
        $this->assertStringContainsString('local_gone', $output);
        $this->assertStringNotContainsString('mod_forum', $output);
        $this->assertStringNotContainsString('local_alive', $output);
        $this->assertStringContainsString('1 plugin(s) listed.', $output);
    }

    /**
     * Ensure a clear message is shown when no plugins match the given filter criteria.
     */
    public function test_no_plugins_match_criteria(): void {
        $manager = $this->build_mock_manager([
            ['component' => 'mod_forum', 'name' => 'Forum', 'status' => 'uptodate', 'isstandard' => true],
        ]);

        $tester = new CommandTester($this->build_command($manager));
        $tester->execute(['--missing' => true]);

        $tester->assertCommandIsSuccessful();
        $this->assertStringContainsString(
            'No plugins match the given criteria.',
            $tester->getDisplay(),
        );
    }
}
