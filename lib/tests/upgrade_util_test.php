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
 * Upgrade utility class  tests.
 *
 * @package    core
 * @copyright  2016 Cameron Ball <cameron@cameron1729.xyz>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Hack to let tests run on Travis CI.
defined('CURL_SSLVERSION_TLSv1_2') || define('CURL_SSLVERSION_TLSv1_2', 6);

/**
 * Upgrade utility class tests.
 *
 * @package   core
 * @copyright 2016 Cameron Ball <cameron@cameron1729.xyz>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class upgrade_util_testcase extends advanced_testcase {

    /**
     * The value of PHP_ZTS when thread safety is enabled.
     */
    const PHP_ZTS_ENABLED = 1;

    /**
     * The value of PHP_ZTS when thread safety is disabled.
     */
    const PHP_ZTS_DISABLED = 0;

    /**
     * Test PHP/cURL validation.
     *
     * @dataProvider validate_php_curl_tls_testcases()
     * @param array $curlinfo server curl_version array
     * @param int   $zts      0 or 1 as defined by PHP_ZTS
     * @param bool  $expected expected result
     */
    public function test_validate_php_curl_tls($curlinfo, $zts, $expected) {
        $this->assertSame($expected, \core\upgrade\util::validate_php_curl_tls($curlinfo, $zts));
    }

    /**
     * Test cases for validate_php_curl_tls test.
     */
    public function validate_php_curl_tls_testcases() {
        $base = curl_version();

        return [
            'Not threadsafe - Valid SSL (GnuTLS)' => [
                ['ssl_version' => 'GnuTLS/4.20'] + $base,
                self::PHP_ZTS_DISABLED,
                true
            ],
            'Not threadsafe - Valid SSL (OpenSSL)' => [
                ['ssl_version' => 'OpenSSL'] + $base,
                self::PHP_ZTS_DISABLED,
                true
            ],
            'Not threadsafe - Valid SSL (WinSSL)' => [
                ['ssl_version' => 'WinSSL'] + $base,
                self::PHP_ZTS_DISABLED,
                true
            ],
            'Not threadsafe - Invalid SSL' => [
                ['ssl_version' => ''] + $base,
                self::PHP_ZTS_DISABLED,
                false
            ],
            'Threadsafe - Valid SSL (OpenSSL)' => [
                ['ssl_version' => 'OpenSSL/1729'] + $base,
                self::PHP_ZTS_ENABLED,
                true
            ],
            'Threadsafe - Valid SSL (GnuTLS)' => [
                ['ssl_version' => 'GnuTLS/3.14'] + $base,
                self::PHP_ZTS_ENABLED,
                true
            ],
            'Threadsafe - Invalid SSL' => [
                ['ssl_version' => ''] + $base,
                self::PHP_ZTS_ENABLED,
                false
            ],
            'Threadsafe - Invalid SSL (but not empty)' => [
                ['ssl_version' => 'Not GnuTLS or OpenSSL'] + $base,
                self::PHP_ZTS_ENABLED,
                false
            ]
        ];
    }

    /**
     * Test various combinations of SSL/TLS libraries.
     *
     * @dataProvider can_use_tls12_testcases
     * @param string $sslversion the ssl_version string.
     * @param string|null $uname uname string (or null if not relevant)
     * @param bool $expected expected result
     */
    public function test_can_use_tls12($sslversion, $uname, $expected) {
        // Populate curlinfo with whats installed on this php install.
        $curlinfo = curl_version();

        // Set the curl values we are testing to the passed data.
        $curlinfo['ssl_version'] = $sslversion;

        // Set uname to system value if none passed in test case.
        $uname = !empty($uname) ? $uname : php_uname('r');

        $this->assertSame($expected, \core\upgrade\util::can_use_tls12($curlinfo, $uname));
    }

    /**
     * Test cases for the can_use_tls12 test.
     * The returned data format is:
     *  [(string) ssl_version, (string|null) uname (null if not relevant), (bool) expectation ]
     *
     * @return array of testcases
     */
    public function can_use_tls12_testcases() {
        return [
            // Bad versions.
            ['OpenSSL/0.9.8o', null, false],
            ['GnuTLS/1.5.0', null, false],
            ['NSS/3.14.15', null, false],
            ['CyaSSL/0.9.9', null, false],
            ['wolfSSL/1.0.0', null, false],
            ['WinSSL', '5.1', false],
            ['SecureTransport', '10.7.5', false],
            // Lowest good version.
            ['OpenSSL/1.0.1c', null, true],
            ['GnuTLS/1.7.1', null, true],
            ['NSS/3.15.1 Basic ECC', null, true],
            ['CyaSSL/1.1.0', null, true],
            ['wolfSSL/1.1.0', null, true],
            ['WinSSL', '6.1', true],
            ['SecureTransport', '10.8.0', true],
            // More higher good versions.
            ['OpenSSL/1.0.1t', null, true],
            ['GnuTLS/1.8.1', null, true],
            ['NSS/3.17.2 Basic ECC', null, true],
            ['CyaSSL/1.2.0', null, true],
            ['wolfSSL/1.2.0', null, true],
            ['WinSSL', '7.0', true],
            ['SecureTransport', '10.9.0', true],
        ];
    }
}
