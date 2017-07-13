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
 * PayPal enrolment plugin utility class.
 *
 * @package    core
 * @copyright  2016 Cameron Ball <cameron@cameron1729.xyz>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\upgrade;

defined('MOODLE_INTERNAL') || die();

/**
 * Core upgrade utility class.
 *
 * @package   core
 * @copyright 2016 Cameron Ball <cameron@cameron1729.xyz>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class util {

    /**
     * Gets the minimum version of a SSL/TLS library required for TLS 1.2 support.
     *
     * @param  string $sslflavour The SSL/TLS library
     * @return string|false The version string if it exists. False otherwise
     */
    private static function get_min_ssl_lib_version_for_tls12($sslflavour) {
        // Min versions for TLS 1.2.
        $versionmatrix = [
            'OpenSSL' => '1.0.1c',
            'GnuTLS' => '1.7.1',
            'NSS' => '3.15.1', // This number is usually followed by something like "Basic ECC".
            'CyaSSL' => '1.1.0',
            'wolfSSL' => '1.1.0',
            'PolarSSL' => '1.2.0',
            'WinSSL' => '*', // Does not specify a version but needs Windows >= 7.
            'SecureTransport' => '*' // Does not specify a version but needs iOS >= 5.0 or OS X >= 10.8.0.
        ];

        return isset($versionmatrix[$sslflavour]) ? $versionmatrix[$sslflavour] : false;
    }

    /**
     * Validates PHP/cURL extension for use with SSL/TLS.
     *
     * @param  array $curlinfo array of cURL information as returned by curl_version()
     * @param  int   $zts 0 or 1 as defined by PHP_ZTS
     * @return bool
     */
    public static function validate_php_curl_tls(array $curlinfo, $zts) {
        if (empty($curlinfo['ssl_version'])) {
            return false;
        }

        $flavour = explode('/', $curlinfo['ssl_version'])[0];
        // In threadsafe mode the only valid choices are OpenSSL and GnuTLS.
        if ($zts === 1 && $flavour != 'OpenSSL' && $flavour !== 'GnuTLS') {
            return false;
        }

        return true;
    }

    /**
     * Tests if the system is capable of using TLS 1.2 for requests.
     *
     * @param  array  $curlinfo array of cURL information as returned by curl_version()
     * @param  string $uname server uname
     * @return bool
     */
    public static function can_use_tls12(array $curlinfo, $uname) {
        // Do not compare the cURL version, e.g. $curlinfo['version_number'], with v7.34.0 (467456):
        // some Linux distros backport security issues and keep lower version numbers.
        if (!defined('CURL_SSLVERSION_TLSv1_2')) {
            return false;
        }

        $sslversion = explode('/', $curlinfo['ssl_version']);
        // NSS has a space in the version number 😦.
        $flavour = explode(' ', $sslversion[0])[0];
        $version = count($sslversion) == 2 ? $sslversion[1] : null;

        $minversion = self::get_min_ssl_lib_version_for_tls12($flavour);
        if (!$minversion) {
            return false;
        }

        // Special case (see $versionmatrix above).
        if ($flavour == 'WinSSL') {
            return $uname >= '6.1';
        }

        // Special case (see $versionmatrix above).
        if ($flavour == 'SecureTransport') {
            return $uname >= '10.8.0';
        }

        return $version >= $minversion;
    }
}
