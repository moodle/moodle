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
 * Cohort validation tests (no DB writes).
 *
 * @package    local_launchpad
 * @category   phpunit
 * @copyright  2026 IntelliVerse-X
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \local_launchpad\cohort
 * @covers     \local_launchpad\blueprint::cohort_slug
 */
final class cohort_test extends \basic_testcase {

    public function test_validate_accepts_pilot_and_returns_slug(): void {
        $this->assertSame('2026-pilot', cohort::validate('2026-Pilot'));
        $this->assertSame('pilot', cohort::validate('Pilot'));
    }

    public function test_validate_rejects_empty_and_non_slug_input(): void {
        $this->expectException(cohort_exception::class);
        cohort::validate('@@@');
    }

    public function test_validate_rejects_whitespace_only(): void {
        $this->expectException(cohort_exception::class);
        cohort::validate('   ');
    }

    public function test_validate_rejects_slug_over_moodle_idnumber_budget(): void {
        $long = str_repeat('a', cohort::MAX_SLUG_LENGTH + 1);
        $this->expectException(cohort_exception::class);
        cohort::validate($long);
    }

    public function test_generated_identifiers_fit_moodle_limits_for_valid_cohort(): void {
        cohort::assert_generated_identifiers('2026-Pilot');
        $courses = blueprint::courses('2026-Pilot');
        foreach ($courses as $course) {
            $this->assertLessThanOrEqual(cohort::COURSE_SHORTNAME_MAX, strlen($course['shortname']));
            $this->assertLessThanOrEqual(cohort::COURSE_IDNUMBER_MAX, strlen($course['idnumber']));
        }
    }
}
