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

namespace core\fixtures;

/**
 * Test to ensure that fixtures are excluded from phpunit configuration.
 *
 * @package    core
 * @category   phpunit
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class fixtures_not_tested_test extends \advanced_testcase {
    /**
     * Ensure that test fixtures are not tested.
     *
     * This test deliberately fails, but it should never be included in a test run.
     *
     * If this test is failing, then something has broken the PHPUnit configuration.
     */
    public function test_fixture_are_not_included(): void {
        $this->assertFalse(true);
    }
}
