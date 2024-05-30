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
 * Tests for tool_licensemanager helper class.
 *
 * @package    tool_licensemanager
 * @copyright  2020 Tom Dickman <tom.dickman@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Tests for tool_licensemanager helper class.
 *
 * @package    tool_licensemanager
 * @copyright  2020 Tom Dickman <tom.dickman@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @group      tool_licensemanager
 */
class helper_test extends advanced_testcase {

    public function test_convert_version_to_epoch(): void {

        $version = '2020010100';
        $expected = strtotime(20200101);

        $this->assertEquals($expected, \tool_licensemanager\helper::convert_version_to_epoch($version));
    }
}
