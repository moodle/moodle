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

    public function setUp(): void {
        global $CFG;
        require_once("$CFG->libdir/filelib.php");
        require_once("$CFG->dirroot/iplookup/lib.php");

        if (!PHPUNIT_LONGTEST) {
            // we do not want to DDOS their server, right?
            $this->markTestSkipped('PHPUNIT_LONGTEST is not defined');
        }

        $this->resetAfterTest();

        $CFG->geoipfile = '';
    }

    public function test_ipv4() {
        $result = iplookup_find_location('50.0.184.0');

        $this->assertEquals('array', gettype($result));
        $this->assertEquals('San Francisco', $result['city']);
        $this->assertEqualsWithDelta(-122.3933, $result['longitude'], 0.1, 'Coordinates are out of accepted tolerance');
        $this->assertEqualsWithDelta(37.7697, $result['latitude'], 0.1, 'Coordinates are out of accepted tolerance');
        $this->assertNull($result['error']);
        $this->assertEquals('array', gettype($result['title']));
        $this->assertEquals('San Francisco', $result['title'][0]);
        $this->assertEquals('United States', $result['title'][1]);
    }

    public function test_geoip_ipv6() {
        $result = iplookup_find_location('2a01:8900:2:3:8c6c:c0db:3d33:9ce6');

        $this->assertNotNull($result['error']);
        $this->assertEquals($result['error'], get_string('invalidipformat', 'error'));
    }
}

