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
 * Enrol plan tests (no DB writes).
 *
 * @package    local_launchpad
 * @category   phpunit
 * @copyright  2026 IntelliVerse-X
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \local_launchpad\enrol_plan
 */
final class enrol_plan_test extends \basic_testcase {

    public function test_missing_manual_requests_create(): void {
        $plan = enrol_plan::analyse([
            ['enrol' => 'self', 'status' => enrol_plan::STATUS_ENABLED],
        ]);
        $this->assertTrue($plan['create_manual']);
        $this->assertFalse($plan['enable_manual']);
        $this->assertSame(['self'], $plan['other_enabled']);
        $this->assertStringContainsString('will create manual', $plan['lines'][0]);
        $this->assertStringContainsString('left unchanged: self', $plan['lines'][1]);
    }

    public function test_disabled_manual_requests_enable_without_disabling_others(): void {
        $plan = enrol_plan::analyse([
            ['enrol' => 'manual', 'status' => 1],
            ['enrol' => 'guest', 'status' => enrol_plan::STATUS_ENABLED],
        ]);
        $this->assertFalse($plan['create_manual']);
        $this->assertTrue($plan['enable_manual']);
        $this->assertSame(['guest'], $plan['other_enabled']);
    }

    public function test_enabled_manual_only_is_noop(): void {
        $plan = enrol_plan::analyse([
            ['enrol' => 'manual', 'status' => enrol_plan::STATUS_ENABLED],
        ]);
        $this->assertFalse($plan['create_manual']);
        $this->assertFalse($plan['enable_manual']);
        $this->assertSame([], $plan['other_enabled']);
        $this->assertSame(['manual already enabled'], $plan['lines']);
    }
}
