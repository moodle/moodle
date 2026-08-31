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
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Tests for the admin:plugins:uninstall command.
 *
 * @package    core_admin
 * @category   test
 * @copyright  2026 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(plugins_uninstall::class)]
final class plugins_uninstall_test extends \advanced_testcase {
    /**
     * Build a mock plugin manager.
     *
     * @param array $plugins Keyed by component. Value has 'name' and 'canuninstall'.
     * @param int $uninstallcalls Reference counter for uninstall_plugin() calls.
     * @return \core\plugin_manager
     */
    private function build_mock_manager(array $plugins, int &$uninstallcalls = 0): \core\plugin_manager {
        $infos = [];
        foreach ($plugins as $component => $spec) {
            $infos[$component] = new class (
                $component,
                $spec['name'],
                $spec['status'] ?? 'uptodate',
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
        }

        return new class ($infos, $plugins, $uninstallcalls) extends \core\plugin_manager {
            /**
             * Constructor.
             *
             * @param array $infos Plugin info objects keyed by component.
             * @param array $specs Plugin specs keyed by component.
             * @param int $callcounter Reference counter for uninstall calls.
             */
            public function __construct(
                /** @var array Plugin info objects keyed by component. */
                private array $infos,
                /** @var array Plugin specs keyed by component. */
                private array $specs,
                /** @var int Reference counter for uninstall calls. */
                private int &$callcounter,
            ) {
                // Skip parent constructor.
            }

            #[\Override]
            public function get_plugin_info($component): mixed {
                return $this->infos[$component] ?? null;
            }

            #[\Override]
            public function can_uninstall_plugin($component): bool {
                return $this->specs[$component]['canuninstall'] ?? false;
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
     * @return plugins_uninstall
     */
    private function build_command(\core\plugin_manager $manager): plugins_uninstall {
        \core\di::set(\core\plugin_manager::class, $manager);

        $command = \core\di::get(plugins_uninstall::class);

        // Normally the Application provides the HelperSet (and its QuestionHelper) when the
        // command is run. These tests construct the command directly, bypassing the
        // Application, so the HelperSet must be supplied here instead.
        $command->setHelperSet(new HelperSet([new QuestionHelper()]));

        return $command;
    }

    /**
     * Ensure a single plugin is displayed and uninstalled after confirmation is accepted.
     */
    public function test_single_plugin_confirmation_accepted(): void {
        $uninstallcalls = 0;
        $manager = $this->build_mock_manager([
            'local_myplugin' => ['name' => 'My plugin', 'canuninstall' => true],
        ], $uninstallcalls);

        $tester = new CommandTester($this->build_command($manager));
        $tester->setInputs(['yes']);
        $tester->execute(['plugin' => ['local_myplugin']]);

        $tester->assertCommandIsSuccessful();
        $this->assertSame(1, $uninstallcalls);
        $output = $tester->getDisplay();
        $this->assertStringContainsString('The following plugins will be uninstalled:', $output);
        $this->assertStringContainsString('local_myplugin (My plugin)', $output);
        $this->assertStringContainsString('Uninstalling: local_myplugin', $output);
    }

    /**
     * Ensure a single plugin is not uninstalled when confirmation is declined.
     */
    public function test_single_plugin_confirmation_declined(): void {
        $uninstallcalls = 0;
        $manager = $this->build_mock_manager([
            'local_myplugin' => ['name' => 'My plugin', 'canuninstall' => true],
        ], $uninstallcalls);

        $tester = new CommandTester($this->build_command($manager));
        $tester->setInputs(['no']);
        $tester->execute(['plugin' => ['local_myplugin']]);

        $tester->assertCommandIsSuccessful();
        $this->assertSame(0, $uninstallcalls);
        $this->assertStringContainsString('Aborted. No plugins were uninstalled.', $tester->getDisplay());
    }

    /**
     * Ensure --assume-yes bypasses confirmation for a single plugin.
     */
    public function test_single_plugin_with_assume_yes_uninstalls_non_interactive(): void {
        $uninstallcalls = 0;
        $manager = $this->build_mock_manager([
            'local_myplugin' => ['name' => 'My plugin', 'canuninstall' => true],
        ], $uninstallcalls);

        $tester = new CommandTester($this->build_command($manager));
        $tester->execute(['plugin' => ['local_myplugin'], '--assume-yes' => true], ['interactive' => false]);

        $tester->assertCommandIsSuccessful();
        $this->assertSame(1, $uninstallcalls);
    }

    /**
     * Ensure non-interactive mode fails cleanly when single-plugin confirmation cannot be asked.
     */
    public function test_single_plugin_non_interactive_requires_assume_yes(): void {
        $uninstallcalls = 0;
        $manager = $this->build_mock_manager([
            'local_myplugin' => ['name' => 'My plugin', 'canuninstall' => true],
        ], $uninstallcalls);

        $tester = new CommandTester($this->build_command($manager));
        $tester->execute(['plugin' => ['local_myplugin']], ['interactive' => false]);

        $this->assertSame(Command::FAILURE, $tester->getStatusCode());
        $this->assertSame(0, $uninstallcalls);
        $this->assertStringContainsString('Re-run with --assume-yes.', $tester->getDisplay());
    }

    /**
     * Ensure multiple plugins uninstall after the confirmation prompt is accepted.
     */
    public function test_multiple_plugins_confirmation_accepted(): void {
        $uninstallcalls = 0;
        $manager = $this->build_mock_manager([
            'local_alpha' => ['name' => 'Alpha', 'canuninstall' => true],
            'local_beta'  => ['name' => 'Beta', 'canuninstall' => true],
        ], $uninstallcalls);

        $tester = new CommandTester($this->build_command($manager));
        $tester->setInputs(['yes']);
        $tester->execute(['plugin' => ['local_alpha', 'local_beta']]);

        $tester->assertCommandIsSuccessful();
        $this->assertSame(2, $uninstallcalls);
        $output = $tester->getDisplay();
        $this->assertStringContainsString('The following plugins will be uninstalled:', $output);
        $this->assertStringContainsString('local_alpha (Alpha)', $output);
        $this->assertStringContainsString('local_beta (Beta)', $output);
    }

    /**
     * Ensure declining the confirmation prompt leaves all plugins installed.
     */
    public function test_multiple_plugins_confirmation_declined(): void {
        $uninstallcalls = 0;
        $manager = $this->build_mock_manager([
            'local_alpha' => ['name' => 'Alpha', 'canuninstall' => true],
            'local_beta'  => ['name' => 'Beta', 'canuninstall' => true],
        ], $uninstallcalls);

        $tester = new CommandTester($this->build_command($manager));
        $tester->setInputs(['no']);
        $tester->execute(['plugin' => ['local_alpha', 'local_beta']]);

        $tester->assertCommandIsSuccessful();
        $this->assertSame(0, $uninstallcalls);
        $this->assertStringContainsString('Aborted. No plugins were uninstalled.', $tester->getDisplay());
    }

    /**
     * Ensure non-interactive mode fails cleanly when multi-plugin confirmation cannot be asked.
     */
    public function test_multiple_plugins_non_interactive_requires_assume_yes(): void {
        $uninstallcalls = 0;
        $manager = $this->build_mock_manager([
            'local_alpha' => ['name' => 'Alpha', 'canuninstall' => true],
            'local_beta'  => ['name' => 'Beta', 'canuninstall' => true],
        ], $uninstallcalls);

        $tester = new CommandTester($this->build_command($manager));
        $tester->execute(['plugin' => ['local_alpha', 'local_beta']], ['interactive' => false]);

        $this->assertSame(Command::FAILURE, $tester->getStatusCode());
        $this->assertSame(0, $uninstallcalls);
        $this->assertStringContainsString('Re-run with --assume-yes.', $tester->getDisplay());
    }

    /**
     * Ensure non-interactive mode proceeds when confirmation is pre-approved.
     */
    public function test_multiple_plugins_with_assume_yes_uninstalls_non_interactive(): void {
        $uninstallcalls = 0;
        $manager = $this->build_mock_manager([
            'local_alpha' => ['name' => 'Alpha', 'canuninstall' => true],
            'local_beta'  => ['name' => 'Beta', 'canuninstall' => true],
        ], $uninstallcalls);

        $tester = new CommandTester($this->build_command($manager));
        $tester->execute(['plugin' => ['local_alpha', 'local_beta'], '--assume-yes' => true], ['interactive' => false]);

        $tester->assertCommandIsSuccessful();
        $this->assertSame(2, $uninstallcalls);
    }

    /**
     * Ensure unknown plugin components fail validation before any uninstall is attempted.
     */
    public function test_unknown_plugin_returns_failure(): void {
        $uninstallcalls = 0;
        $manager = $this->build_mock_manager([], $uninstallcalls);

        $tester = new CommandTester($this->build_command($manager));
        $tester->execute(['plugin' => ['local_doesnotexist']]);

        $this->assertSame(Command::FAILURE, $tester->getStatusCode());
        $this->assertSame(0, $uninstallcalls);
        $this->assertStringContainsString('Unknown plugin: local_doesnotexist', $tester->getDisplay());
    }

    /**
     * Ensure non-uninstallable plugins fail validation before any uninstall is attempted.
     */
    public function test_cannot_uninstall_returns_failure(): void {
        $uninstallcalls = 0;
        $manager = $this->build_mock_manager([
            'local_locked' => ['name' => 'Locked plugin', 'canuninstall' => false],
        ], $uninstallcalls);

        $tester = new CommandTester($this->build_command($manager));
        $tester->execute(['plugin' => ['local_locked']]);

        $this->assertSame(Command::FAILURE, $tester->getStatusCode());
        $this->assertSame(0, $uninstallcalls);
        $this->assertStringContainsString('Cannot uninstall: local_locked', $tester->getDisplay());
    }

    /**
     * Ensure batch validation completes before any uninstall side effects begin.
     */
    public function test_multiple_plugins_validate_before_uninstalling(): void {
        $uninstallcalls = 0;
        $manager = $this->build_mock_manager([
            'local_ok'     => ['name' => 'OK plugin', 'canuninstall' => true],
            'local_locked' => ['name' => 'Locked plugin', 'canuninstall' => false],
        ], $uninstallcalls);

        $tester = new CommandTester($this->build_command($manager));
        $tester->execute(['plugin' => ['local_ok', 'local_locked']], ['interactive' => false]);

        $this->assertStringContainsString('Cannot uninstall: local_locked', $tester->getDisplay());
        $this->assertSame(Command::FAILURE, $tester->getStatusCode());
        $this->assertSame(0, $uninstallcalls);
        $this->assertStringNotContainsString('Uninstalling: local_ok', $tester->getDisplay());
    }
}
