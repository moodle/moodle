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

global $CFG;

require_once("{$CFG->libdir}/filelib.php");
require_once("{$CFG->dirroot}/iplookup/lib.php");


/**
 * GeoIp data file parsing test.
 */
class core_iplookup_geoip_testcase extends advanced_testcase {
    public function setUp() {
        if (!PHPUNIT_LONGTEST) {
            // These tests are intensive and required downloads.
            $this->markTestSkipped('PHPUNIT_LONGTEST is not defined');
        }

        $this->resetAfterTest();
    }


    /**
     * Setup the GeoIP2File system.
     */
    public function setup_geoip2file() {
        global $CFG;

        // Store the file somewhere where it won't be wiped out..
        $gzfile = "$CFG->dataroot/phpunit/geoip/GeoLite2-City.mmdb.gz";
        check_dir_exists(dirname($gzfile));
        if (file_exists($gzfile) and (filemtime($gzfile) < time() - 60*60*24*30)) {
            // Delete file if older than 1 month.
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

    /**
     * Test the format of data returned in the iplookup_find_location function.
     *
     * @dataProvider ip_provider
     * @param   string  $ip The IP to test
     */
    public function test_ip($ip) {
        $this->setup_geoip2file();

        // Note: The results we get from the iplookup tests are beyond our control.
        // We used to check a specific IP to a known location, but these have become less reliable and change too
        // frequently to be used for testing.

        $result = iplookup_find_location($ip);

        $this->assertInternalType('array', $result);
        $this->assertInternalType('float', $result['latitude']);
        $this->assertInternalType('float', $result['longitude']);
        $this->assertInternalType('string', $result['city']);
        $this->assertInternalType('string', $result['country']);
        $this->assertInternalType('array', $result['title']);
        $this->assertInternalType('string', $result['title'][0]);
        $this->assertInternalType('string', $result['title'][1]);
        $this->assertNull($result['error']);
    }

    /**
     * Data provider for IP lookup test.
     *
     * @return array
     */
    public function ip_provider() {
        return [
            'IPv4: Sample suggested by maxmind themselves' => ['24.24.24.24'],
            'IPv4: github.com' => ['192.30.255.112'],
            'IPv6: UCLA' => ['2607:f010:3fe:fff1::ff:fe00:25'],
        ];
    }
}
