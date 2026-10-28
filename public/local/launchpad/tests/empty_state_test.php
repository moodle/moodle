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
 * Empty-state notice shape tests (no DB writes).
 *
 * @package    local_launchpad
 * @category   phpunit
 * @copyright  2026 IntelliVerse-X
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \local_launchpad\empty_state
 */
final class empty_state_test extends \basic_testcase {

    public function test_notice_html_is_honest_and_marked(): void {
        $html = empty_state::notice_html();
        $this->assertStringContainsString(empty_state::DATA_REGION, $html);
        $this->assertStringContainsString('role="status"', $html);
        $this->assertStringContainsString('programme admin will enrol you', $html);
        $this->assertStringContainsString('margin-top:4.75rem', $html);
        $this->assertStringNotContainsString('@', $html);
        $this->assertStringNotContainsString('http://', $html);
        $this->assertStringNotContainsString('₹', $html);
    }
}
