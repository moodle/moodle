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
 * GeoIP tests
 *
 * @package    core_iplookup
 * @category   phpunit
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


/**
 * GeoIp data file parsing test.
 */
class core_iplookup_geoip_testcase extends advanced_testcase {

    public function setUp() {
        global $CFG;
        require_once("$CFG->libdir/filelib.php");
        require_once("$CFG->dirroot/iplookup/lib.php");

        if (!PHPUNIT_LONGTEST) {
            // this may take a long time
            $this->markTestSkipped('PHPUNIT_LONGTEST is not defined');
        }

        $this->resetAfterTest();

        // let's store the file somewhere
        $gzfile = "$CFG->dataroot/phpunit/geoip/GeoLite2-City.mmdb.gz";
        check_dir_exists(dirname($gzfile));
        if (file_exists($gzfile) and (filemtime($gzfile) < time() - 60*60*24*30)) {
            // delete file if older than 1 month
            unlink($gzfile);
        }

        if (!file_exists($gzfile)) {
            download_file_content('http://geolite.maxmind.com/download/geoip/database/GeoLite2-City.mmdb.gz',
                null, null, false, 300, 20, false, $gzfile);
        }

        $this->assertTrue(file_exists($gzfile));

        $geoipfile = str_replace('.gz', '', $gzfile);

        // Open our files (in binary mode).
        $file = gzopen($gzfile, 'rb');
        $geoipfilebuf = fopen($geoipfile, 'wb');

        // Keep repeating until the end of the input file.
        while (!gzeof($file)) {
            // Read buffer-size bytes.
            // Both fwrite and gzread and binary-safe.
            fwrite($geoipfilebuf, gzread($file, 4096));
        }

        // Files are done, close files.
        fclose($geoipfilebuf);
        gzclose($file);

        $this->assertTrue(file_exists($geoipfile));

        $CFG->geoip2file = $geoipfile;
    }

    public function test_ipv4() {

        $result = iplookup_find_location('131.111.150.25');

        $this->assertEquals('array', gettype($result));
        $this->assertEquals('Cambridge', $result['city']);
        $this->assertEquals(0.1167, $result['longitude'], 'Coordinates are out of accepted tolerance', 0.01);
        $this->assertEquals(52.2, $result['latitude'], 'Coordinates are out of accepted tolerance', 0.01);
        $this->assertNull($result['error']);
        $this->assertEquals('array', gettype($result['title']));
        $this->assertEquals('Cambridge', $result['title'][0]);
        $this->assertEquals('United Kingdom', $result['title'][1]);
    }

    public function test_ipv6() {
        // NOTE: these tests can be altered by the geoip dataset, there has been an attempt to get
        // a 'reliable' result.

        $result = iplookup_find_location('2607:f010:3fe:fff1::ff:fe00:25');

        $this->assertEquals('array', gettype($result));
        $this->assertEquals('Los Angeles', $result['city']);
        $this->assertEquals(-118.2987, $result['longitude'], 'Coordinates are out of accepted tolerance', 0.01);
        $this->assertEquals(33.7866, $result['latitude'], 'Coordinates are out of accepted tolerance', 0.01);
        $this->assertNull($result['error']);
        $this->assertEquals('array', gettype($result['title']));
        $this->assertEquals('Los Angeles', $result['title'][0]);
        $this->assertEquals('United States', $result['title'][1]);
    }
}

