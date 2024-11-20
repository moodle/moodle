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

namespace core;

/**
 * GeoIp data file parsing test.
 *
 * @package    core
 * @category   test
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class geoplugin_test extends \advanced_testcase {

    /**
     * Load required test libraries
     */
    public static function setUpBeforeClass(): void {
        global $CFG;
        require_once("{$CFG->dirroot}/iplookup/lib.php");
        parent::setUpBeforeClass();
    }

    /**
     * In order to execute this test PHPUNIT_LONGTEST should be defined as true in phpunit.xml or directly in config.php
     */
    public function setUp(): void {
        parent::setUp();
        if (!PHPUNIT_LONGTEST) {
            $this->markTestSkipped('PHPUNIT_LONGTEST is not defined');
        }
    }

    /**
     * Test IPv4 address
     *
     * @covers ::iplookup_find_location
     */
    public function test_ipv4(): void {
        $result = iplookup_find_location('50.0.184.0');

        $this->assertIsArray($result);
        $this->assertIsFloat($result['latitude']);
        $this->assertIsFloat($result['longitude']);
        $this->assertIsString($result['city']);
        $this->assertIsString($result['country']);
        $this->assertIsArray($result['title']);
        $this->assertIsString($result['title'][0]);
        $this->assertIsString($result['title'][1]);
        $this->assertNull($result['error']);
    }

    /**
     * Test IPv6 address (unsupported by Geoplugin)
     *
     * @covers ::iplookup_find_location
     */
    public function test_ipv6(): void {
        $result = iplookup_find_location('2a01:8900:2:3:8c6c:c0db:3d33:9ce6');
        $this->assertEquals($result['error'], get_string('invalidipformat', 'error'));
    }
}
