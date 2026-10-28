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

namespace local_launchpad;

/**
 * Operation failure message shape (no DB writes).
 *
 * @package    local_launchpad
 * @category   phpunit
 * @copyright  2026 IntelliVerse-X
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \local_launchpad\operation_failure
 */
final class operation_failure_test extends \basic_testcase {

    public function test_message_includes_operation_target_and_cause(): void {
        $prev = new \RuntimeException('quiz table missing column');
        $e = new operation_failure('create_activity', 'lp-ai-pilot / quiz / lp-ai-pilot-act-f-quiz', $prev);
        $this->assertSame('create_activity', $e->operation);
        $this->assertStringContainsString('lp-ai-pilot / quiz', $e->target);
        $this->assertStringContainsString('Failed during create_activity', $e->getMessage());
        $this->assertStringContainsString('quiz table missing column', $e->getMessage());
        $this->assertSame($prev, $e->getPrevious());
    }
}
