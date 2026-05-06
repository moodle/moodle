<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

namespace core_admin\setting\setting;

use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Unit tests for the admin_setting_configduration class.
 *
 * @package    core_admin
 * @category   test
 * @copyright  2013 David Mudrak <david@moodle.com>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[CoversClass(configduration::class)]
final class configduration_test extends \advanced_testcase {
    /**
     * Test setting an empty duration displays the correct validation message.
     */
    public function test_emptydurationvalue(): void {
        $this->resetAfterTest();
        $adminsetting = new configduration('abc_cde/duration', 'some desc', '', '');

        // A value that isn't a number is treated as a zero, so we expect to see no error message.
        $this->assertEmpty($adminsetting->write_setting(['u' => '3600', 'v' => 'abc']));
    }
}
