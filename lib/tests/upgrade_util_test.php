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

/**
 * Upgrade utility class tests.
 *
 * @package   core
 * @copyright 2016 Cameron Ball <cameron@cameron1729.xyz>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class upgrade_util_testcase extends advanced_testcase {

    /**
     * A cURL version that supports TLS 1.2.
     */
    const VALID_CURL_VERSION = 467456;

    /**
     * A cURL version that does not support TLS 1.2.
     */
    const INVALID_CURL_VERSION = 467455;

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
        $expected === true && $this->assertTrue(\core\upgrade\util::validate_php_curl_tls($curlinfo, $zts));
        $expected === false && $this->assertFalse(\core\upgrade\util::validate_php_curl_tls($curlinfo, $zts));
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
     * @param array $environment the server environment
     * @param bool  $expected    expected result
     */
    public function test_can_use_tls12($environment, $expected) {
        $curlinfo = $environment['curl_version'] + curl_version();

        if ($curlinfo['version_number'] >= self::VALID_CURL_VERSION && !defined('CURL_SSLVERSION_TLSv1_2')) {
            define('CURL_SSLVERSION_TLSv1_2', 6);
        }

        $expected === true && $this->assertTrue(\core\upgrade\util::can_use_tls12($curlinfo, $environment['uname']));
        $expected === false && $this->assertFalse(\core\upgrade\util::can_use_tls12($curlinfo, $environment['uname']));
    }

    /**
     * Test cases for the can_use_tls test.
     *
     * @return array of testcases
     */
    public function can_use_tls12_testcases() {
        $versionmatrix = [
            'OpenSSL'         => ['Older' => '0.9.8o',  'Min required' => '1.0.1c',           'Newer' => '1.0.1t'],
            'GnuTLS'          => ['Older' => '1.5.0',   'Min requires' => '1.7.1',            'Newer' => '1.8.1'],
            'NSS'             => ['Older' => '3.14.15', 'Min required' => '3.15.1 Basic ECC', 'Newer' => '3.17.2 Basic ECC'],
            'CyaSSL'          => ['Older' => '0.9.9',   'Min required' => '1.1.0',            'Newer' => '1.2.0'],
            'wolfSSL'         => ['Older' => '1.0.0',   'Min required' => '1.1.0',            'Newer' => '1.2.0'],
            'WinSSL'          => ['Older' => '5.1',     'Min required' => '6.1',              'Newer' => '7.0'],
            'SecureTransport' => ['Older' => '10.7.5',  'Min required' => '10.8.0',           'Newer' => '10.9.0']
        ];

        // This will generate an array of testcases from the matrix above.
        // It generates one testcase for every version. If the version is too
        // old or the cURL version (passed as an argument) is too old, the
        // expected result of the testcase is false. Otherwise it is true.
        //
        // Each testcase is given a name like WinSSL/Valid env/Min required.
        // The first part is the SSL/TLS library, the second part is whether
        // or not the environment is valid (i.e., we are using a valid/invalid
        // cURL version. The final part says which version of the SSL/TLS library
        // is being used (i.e., Older, Min required or Newer).
        $generatetestcases = function($curlversion) use ($versionmatrix) {
            return array_reduce(array_keys($versionmatrix), function($carry, $sslflavour) use ($versionmatrix, $curlversion) {
                return $carry + array_reduce(array_keys($versionmatrix[$sslflavour]), function($carry, $sslversion)
                        use ($versionmatrix, $curlversion, $sslflavour) {
                    $env = $curlversion == self::VALID_CURL_VERSION ? 'Valid' : 'Invalid';
                    $exceptions = ['WinSSL', 'SecureTransport'];
                    $versionsuffix = in_array($sslflavour, $exceptions) ? '' : '/' . $versionmatrix[$sslflavour][$sslversion];
                    return $carry + [$sslflavour . '/' . $env. ' env/' . $sslversion => [[
                        'curl_version' => [
                            'ssl_version' => $sslflavour . $versionsuffix,
                            'version_number' => $curlversion
                        ],
                        'uname' => in_array($sslflavour, $exceptions) ? $versionmatrix[$sslflavour][$sslversion] : php_uname('r')
                    ], $sslversion != 'Older' && $curlversion != self::INVALID_CURL_VERSION]];
                }, []);
            }, []);
        };

        return $generatetestcases(self::VALID_CURL_VERSION) + $generatetestcases(self::INVALID_CURL_VERSION);
    }
}
