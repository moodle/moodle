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

/**
 * Privacy tests for relative date availability.
 *
 * @package   availability_relativedate
 * @copyright eWallah (www.eWallah.net)
 * @author    Renaat Debleu <info@eWallah.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace availability_relativedate\privacy;

use core_privacy\tests\provider_testcase;
use core_privacy\local\metadata\collection;

/**
 * Privacy tests for relative date availability.
 *
 * @package   availability_relativedate
 * @copyright eWallah (www.eWallah.net)
 * @author    Renaat Debleu <info@eWallah.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \availability_relativedate\privacy\provider
 */
final class privacy_test extends provider_testcase {
    /**
     * Test returning metadata.
     * @covers \availability_relativedate\privacy\provider
     */
    public function test_get_metadata(): void {
        $collection = new collection('availability_relativedate');
        $reason = provider::get_reason($collection);
        $this->assertEquals($reason, 'privacy:metadata');
        $this->assertStringContainsString('does not store private user data.', get_string($reason, 'availability_relativedate'));
    }
}
