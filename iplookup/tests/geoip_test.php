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

        $result = iplookup_find_location('147.230.16.1');

        $this->assertEquals('array', gettype($result));
        $this->assertEquals('Liberec', $result['city']);
        $this->assertEquals(15.0653, $result['longitude'], '', 0.001);
        $this->assertEquals(50.7639, $result['latitude'], '', 0.001);
        $this->assertNull($result['error']);
        $this->assertEquals('array', gettype($result['title']));
        $this->assertEquals('Liberec', $result['title'][0]);
        $this->assertEquals('Czech Republic', $result['title'][1]);
    }

    public function test_ipv6() {

        $result = iplookup_find_location('2a01:8900:2:3:8c6c:c0db:3d33:9ce6');

        $this->assertEquals('array', gettype($result));
        $this->assertEquals('Lancaster', $result['city']);
        $this->assertEquals(-2.79970, $result['longitude'], '', 0.001);
        $this->assertEquals(54.04650, $result['latitude'], '', 0.001);
        $this->assertNull($result['error']);
        $this->assertEquals('array', gettype($result['title']));
        $this->assertEquals('Lancaster', $result['title'][0]);
        $this->assertEquals('United Kingdom', $result['title'][1]);
    }
}

