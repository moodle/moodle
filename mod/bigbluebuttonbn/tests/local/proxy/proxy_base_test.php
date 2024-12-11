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
 * Tests for BigBlueButton Proxy server (and checksum).
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2018 - present, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Laurent David  (laurent [at] call-learning [dt] fr)
 */

namespace mod_bigbluebuttonbn\local\proxy;
use mod_bigbluebuttonbn\test\testcase_helper_trait;

/**
 * Proxy base test
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2018 - present, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Laurent David  (laurent [at] call-learning [dt] fr)
 * @covers  \mod_bigbluebuttonbn\local\proxy\proxy_base
 * @coversDefaultClass \mod_bigbluebuttonbn\local\proxy\proxy_base
 */
final class proxy_base_test extends \advanced_testcase {
    use testcase_helper_trait;

    /**
     * Setup
     */
    public function setUp(): void {
        parent::setUp();
        $this->initialise_mock_server();
    }

    /**
     * Test that different checksum algorithm work
     *
     * @return void
     */
    public function test_get_checksum(): void {
        $this->resetAfterTest();
        foreach (['SHA1', 'SHA512', 'SHA256'] as $algo) {
            set_config('bigbluebuttonbn_checksum_algorithm', $algo);
            $xmlinfo = self::get_status();
            $this->assertNotEmpty($xmlinfo);
            $this->assertEquals('SUCCESS', $xmlinfo->returncode);
        }
    }

    /**
     * Test that we send a checksumError whenever the algorithm is not supported
     *
     * @return void
     */
    public function test_get_checksum_not_supported(): void {
        $this->resetAfterTest();
        $bbbgenerator = $this->getDataGenerator()->get_plugin_generator('mod_bigbluebuttonbn');
        $bbbgenerator->set_value('checksum_algorithms', ['SHA1', 'SHA256']);
        // This should not be supported.
        set_config('bigbluebuttonbn_checksum_algorithm', 'SHA512');
        $xmlinfo = self::get_status();
        $this->assertEquals($xmlinfo->messageKey, 'checksumError');
    }


    /**
     * Get the endpoint XML result.
     *
     * @return mixed
     */
    protected static function get_status() {
        $rc = new \ReflectionClass(proxy_base::class);
        $rcm = $rc->getMethod('fetch_endpoint_xml');
        return $rcm->invoke(null, '');
    }
}
