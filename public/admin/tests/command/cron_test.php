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

/**
 * Tests for the cron command.
 *
 * @package    core_admin
 * @category   test
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(\core_admin\command\cron::class)]
final class cron_test extends \advanced_testcase {
    public function test_disable(): void {
        $this->resetAfterTest();

        $this->assertTrue(\core\task\manager::is_cron_enabled());

        $tester = $this->get_testable_command('admin:cron');
        $tester->execute([
            '--disable' => true,
        ]);

        $tester->assertCommandIsSuccessful();
        $this->assertFalse(\core\task\manager::is_cron_enabled());
    }

    public function test_enable(): void {
        $this->resetAfterTest();

        $this->assertTrue(\core\task\manager::is_cron_enabled());
        \core\task\manager::disable_cron();
        $this->assertFalse(\core\task\manager::is_cron_enabled());

        $tester = $this->get_testable_command('admin:cron');
        $tester->execute([
            '--enable' => true,
        ]);

        $tester->assertCommandIsSuccessful();
        $this->assertTrue(\core\task\manager::is_cron_enabled());
    }

    public function test_list(): void {
        $tester = $this->get_testable_command('admin:cron');
        $tester->execute([
            '--list' => true,
        ]);

        $tester->assertCommandIsSuccessful();
        $output = $tester->getDisplay();
        $this->assertStringContainsString('No tasks are currently running.', $output);
    }

    public function test_list_running(): void {
        $clock = $this->mock_clock_with_frozen(1234567890);
        $taskmanager = new class () extends \core\task\manager {
            #[\Override]
            public static function get_running_tasks($sort = ''): array {
                $clock = \core\di::get(\core\clock::class);
                return [
                    (object) [
                        'uniqueid' => 'Example ID',
                        'pid' => 12345,
                        'hostname' => 'cron1.example.com',
                        'type' => 'scheduled',
                        'timestarted' => $clock->time() - (2 * DAYSECS) - HOURSECS - (3 * MINSECS) - 6,
                        'classname' => '\Moodle\task\example_task',
                    ],
                ];
            }
        };

        \core\di::set(\core\task\manager::class, $taskmanager);

        $tester = $this->get_testable_command('admin:cron');
        $tester->execute([
            '--list' => true,
        ]);

        $tester->assertCommandIsSuccessful();
        $output = $tester->getDisplay();
        $this->assertStringNotContainsString('No tasks are currently running.', $output);
        $this->assertStringContainsString('1 tasks are currently running.', $output);
        $this->assertStringContainsString('cron1.example.com', $output);
        $this->assertStringContainsString('2 d, 1 h, 3 min, 6 s', $output);
    }

    public function test_run(): void {
        $this->resetAfterTest();

        $cronrunner = new class () extends \core\cron {
            #[\Override]
            public static function run_main_process(?int $keepalive = null): void {
                // Simulate running cron tasks.
            }
        };

        \core\di::set(\core\cron::class, $cronrunner);

        $tester = $this->get_testable_command('admin:cron');
        $tester->execute([
            '--force' => true,
        ]);

        $tester->assertCommandIsSuccessful();
    }
}
