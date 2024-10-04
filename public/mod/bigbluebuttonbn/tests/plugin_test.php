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
namespace mod_bigbluebuttonbn;

use advanced_testcase;
use moodle_exception;

/**
 * Tests for the Big Blue Button Plugin class.
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2021 Andrew Lyons <andrew@nicols.co.uk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \mod_bigbluebuttonbn\plugin
 */
final class plugin_test extends advanced_testcase {

    /**
     * Test html2text
     *
     * @covers ::html2text
     */
    public function test_html2text(): void {
        $this->assertEquals('My text is in HTML', plugin::html2text('<p>My text is&nbsp;in HTML</p>', 100));
        $this->assertEquals('My...', plugin::html2text('<p>My text is&nbsp;in HTML</p>', 2));
    }

    /**
     * Test random_password
     *
     * @covers ::random_password
     */
    public function test_random_password(): void {
        $password = plugin::random_password(10);
        $this->assertEquals(10, strlen($password));
        $this->assertMatchesRegularExpression(
            '/[abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789]+/', $password);
    }

    /**
     * Test generate_guest_meeting_credentials
     *
     * @covers ::generate_guest_meeting_credentials
     */
    public function test_generate_guest_meeting_credentials(): void {
        [$guestlinkuid, $password] = plugin::generate_guest_meeting_credentials();
        $this->assertEquals(40, strlen($guestlinkuid));
    }
}
