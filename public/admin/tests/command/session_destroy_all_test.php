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

/**
 * Tests for the cron command.
 *
 * @package    core_admin
 * @category   test
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(\core_admin\command\session_destroy_all::class)]
final class session_destroy_all_test extends \advanced_testcase {
    /**
     * When the session manager is able to successfully destroy all sessions, the command should return success.
     */
    public function test_success(): void {
        $mock = new class () extends \core\session\manager {
            #[\Override]
            public static function destroy_all(): bool {
                return true;
            }
        };

        \core\di::set(\core\session\manager::class, $mock);

        $tester = $this->get_testable_command('admin:session:destroy:all');
        $tester->execute([]);
        $tester->assertCommandIsSuccessful();
    }

    /**
     * When the session manager fails to destroy all sessions, the command should return failure.
     */
    public function test_failure(): void {
        $mock = new class () extends \core\session\manager {
            #[\Override]
            public static function destroy_all(): bool {
                return false;
            }
        };

        \core\di::set(\core\session\manager::class, $mock);

        $tester = $this->get_testable_command('admin:session:destroy:all');
        $tester->execute([]);
        $this->assertEquals(Command::FAILURE, $tester->getStatusCode());
        $this->assertStringContainsString(
            'Destroying all user sessions...',
            $tester->getDisplay(),
        );
        $this->assertStringContainsString(
            'Failed to destroy all user sessions.',
            $tester->getDisplay(),
        );
    }
}
