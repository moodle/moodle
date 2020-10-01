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
 * Unit tests for /lib/classes/curl/curl_security_helper.php.
 *
 * @package   core
 * @copyright 2016 Jake Dallimore <jrhdallimore@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * cURL security test suite.
 *
 * Note: The curl_security_helper class performs forward and reverse DNS look-ups in some cases. This class will not attempt to test
 * this functionality as look-ups can vary from machine to machine. Instead, human testing with known inputs/outputs is recommended.
 *
 * @package    core
 * @copyright  2016 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_curl_security_helper_testcase extends advanced_testcase {
    /**
     * Test for \core\files\curl_security_helper::url_is_blocked().
     *
     * @param array $dns a mapping between hosts and IPs to be used instead of a real DNS lookup. The values must be arrays.
     * @param string $url the url to validate.
     * @param string $blockedhosts the list of blocked hosts.
     * @param string $allowedports the list of allowed ports.
     * @param bool $expected the expected result.
     * @dataProvider curl_security_url_data_provider
     */
    public function test_curl_security_helper_url_is_blocked($dns, $url, $blockedhosts, $allowedports, $expected) {
        $this->resetAfterTest(true);
        $helper = $this->getMockBuilder('\core\files\curl_security_helper')
                        ->setMethods(['get_host_list_by_name'])
                        ->getMock();

        // Override the get host list method to return hard coded values based on a mapping provided by $dns.
        $helper->method('get_host_list_by_name')
               ->will(
                   $this->returnCallback(
                       function($host) use ($dns) {
                           return isset($dns[$host]) ? $dns[$host] : [];
                       }
                   )
               );

        set_config('curlsecurityblockedhosts', $blockedhosts);
        set_config('curlsecurityallowedport', $allowedports);
        $this->assertEquals($expected, $helper->url_is_blocked($url));
    }

    /**
     * Data provider for test_curl_security_helper_url_is_blocked().
     *
     * @return array
     */
    public function curl_security_url_data_provider() {
        $simpledns = ['localhost' => ['127.0.0.1']];
        $multiplerecorddns = [
            'sub.example.com' => ['1.2.3.4', '5.6.7.8']
        ];
        // Format: url, blocked hosts, allowed ports, expected result.
        return [
            // Base set without the blocklist enabled - no checking takes place.
            [$simpledns, "http://localhost/x.png", "", "", false],       // IP=127.0.0.1, Port=80 (port inferred from http).
            [$simpledns, "http://localhost:80/x.png", "", "", false],    // IP=127.0.0.1, Port=80 (specific port overrides http scheme).
            [$simpledns, "https://localhost/x.png", "", "", false],      // IP=127.0.0.1, Port=443 (port inferred from https).
            [$simpledns, "http://localhost:443/x.png", "", "", false],   // IP=127.0.0.1, Port=443 (specific port overrides http scheme).
            [$simpledns, "localhost/x.png", "", "", false],              // IP=127.0.0.1, Port=80 (port inferred from http fallback).
            [$simpledns, "localhost:443/x.png", "", "", false],          // IP=127.0.0.1, Port=443 (port hard specified, despite http fallback).
            [$simpledns, "http://127.0.0.1/x.png", "", "", false],       // IP=127.0.0.1, Port=80 (port inferred from http).
            [$simpledns, "127.0.0.1/x.png", "", "", false],              // IP=127.0.0.1, Port=80 (port inferred from http fallback).
            [$simpledns, "http://localhost:8080/x.png", "", "", false],  // IP=127.0.0.1, Port=8080 (port hard specified).
            [$simpledns, "http://192.168.1.10/x.png", "", "", false],    // IP=192.168.1.10, Port=80 (port inferred from http).
            [$simpledns, "https://192.168.1.10/x.png", "", "", false],   // IP=192.168.1.10, Port=443 (port inferred from https).
            [$simpledns, "http://sub.example.com/x.png", "", "", false], // IP=::1, Port = 80 (port inferred from http).
            [$simpledns, "http://s-1.d-1.com/x.png", "", "", false],     // IP=::1, Port = 80 (port inferred from http).

            // Test set using domain name filters but with all ports allowed (empty).
            [$simpledns, "http://localhost/x.png", "localhost", "", true],
            [$simpledns, "localhost/x.png", "localhost", "", true],
            [$simpledns, "localhost:0/x.png", "localhost", "", true],
            [$simpledns, "ftp://localhost/x.png", "localhost", "", true],
            [$simpledns, "http://sub.example.com/x.png", "localhost", "", false],
            [$simpledns, "http://example.com/x.png", "example.com", "", true],
            [$simpledns, "http://sub.example.com/x.png", "example.com", "", false],

            // Test set using wildcard domain name filters but with all ports allowed (empty).
            [$simpledns, "http://sub.example.com/x.png", "*.com", "", true],
            [$simpledns, "http://example.com/x.png", "*.example.com", "", false],
            [$simpledns, "http://sub.example.com/x.png", "*.example.com", "", true],
            [$simpledns, "http://sub.example.com/x.png", "*.sub.example.com", "", false],
            [$simpledns, "http://sub.example.com/x.png", "*.example", "", false],

            // Test set using IP address filters but with all ports allowed (empty).
            [$simpledns, "http://localhost/x.png", "127.0.0.1", "", true],
            [$simpledns, "http://127.0.0.1/x.png", "127.0.0.1", "", true],

            // Test set using CIDR IP range filters but with all ports allowed (empty).
            [$simpledns, "http://localhost/x.png", "127.0.0.0/24", "", true],
            [$simpledns, "http://127.0.0.1/x.png", "127.0.0.0/24", "", true],

            // Test set using last-group range filters but with all ports allowed (empty).
            [$simpledns, "http://localhost/x.png", "127.0.0.0-30", "", true],
            [$simpledns, "http://127.0.0.1/x.png", "127.0.0.0-30", "", true],

            // Test set using port filters but with all hosts allowed (empty).
            [$simpledns, "http://localhost/x.png", "", "80\n443", false],
            [$simpledns, "http://localhost:80/x.png", "", "80\n443", false],
            [$simpledns, "https://localhost/x.png", "", "80\n443", false],
            [$simpledns, "http://localhost:443/x.png", "", "80\n443", false],
            [$simpledns, "http://sub.example.com:8080/x.png", "", "80\n443", true],
            [$simpledns, "http://sub.example.com:-80/x.png", "", "80\n443", true],
            [$simpledns, "http://sub.example.com:aaa/x.png", "", "80\n443", true],

            // Test set using port filters and hosts filters.
            [$simpledns, "http://localhost/x.png", "127.0.0.1", "80\n443", true],
            [$simpledns, "http://127.0.0.1/x.png", "127.0.0.1", "80\n443", true],

            // Test using multiple A records.
            // Multiple record DNS gives two IPs for the same host, we want to make
            // sure that if we block one of those (doesn't matter which one)
            // the request is blocked.
            [$multiplerecorddns, "http://sub.example.com", '1.2.3.4', "", true],
            [$multiplerecorddns, "http://sub.example.com", '5.6.7.8', "", true],

            // Test when DNS resolution fails.
            [[], "http://example.com", "127.0.0.1", "", true],

            // Test some freaky deaky Unicode domains. Should be blocked always.
            [$simpledns, "http://169。254。169。254/", "127.0.0.1", "", true],
            [$simpledns, "http://169。254。169。254/", "1.2.3.4", "", true],
            [$simpledns, "http://169。254。169。254/", "127.0.0.1", "80\n443", true]

            // Note on testing URLs using IPv6 notation:
            // At present, the curl_security_helper class doesn't support IPv6 url notation.
            // E.g.  http://[ad34::dddd]:port/resource
            // This is because it uses clean_param(x, PARAM_URL) as part of parsing, which won't validate urls having IPv6 notation.
            // The underlying IPv6 address and range support is in place, however, so if clean_param is changed in future,
            // please add the following test sets.
            // 1. ["http://[::1]/x.png", "", "", false]
            // 2. ["http://[::1]/x.png", "::1", "", true]
            // 3. ["http://[::1]/x.png", "::1/64", "", true]
            // 4. ["http://[fe80::dddd]/x.png", "fe80::cccc-eeee", "", true]
            // 5. ["http://[fe80::dddd]/x.png", "fe80::dddd/128", "", true].
        ];
    }

    /**
     * Test for \core\files\curl_security_helper->is_enabled().
     *
     * @param string $blockedhosts the list of blocked hosts.
     * @param string $allowedports the list of allowed ports.
     * @param bool $expected the expected result.
     * @dataProvider curl_security_settings_data_provider
     */
    public function test_curl_security_helper_is_enabled($blockedhosts, $allowedports, $expected) {
        $this->resetAfterTest(true);
        $helper = new \core\files\curl_security_helper();
        set_config('curlsecurityblockedhosts', $blockedhosts);
        set_config('curlsecurityallowedport', $allowedports);
        $this->assertEquals($expected, $helper->is_enabled());
    }

    /**
     * Data provider for test_curl_security_helper_is_enabled().
     *
     * @return array
     */
    public function curl_security_settings_data_provider() {
        // Format: blocked hosts, allowed ports, expected result.
        return [
            ["", "", false],
            ["127.0.0.1", "", true],
            ["localhost", "", true],
            ["127.0.0.0/24\n192.0.0.0/24", "", true],
            ["", "80\n443", true],
        ];
    }

    /**
     * Test for \core\files\curl_security_helper::host_is_blocked().
     *
     * @param string $host the host to validate.
     * @param string $blockedhosts the list of blocked hosts.
     * @param bool $expected the expected result.
     * @dataProvider curl_security_host_data_provider
     */
    public function test_curl_security_helper_host_is_blocked($host, $blockedhosts, $expected) {
        $this->resetAfterTest(true);
        $helper = new \core\files\curl_security_helper();
        set_config('curlsecurityblockedhosts', $blockedhosts);
        $this->assertEquals($expected, phpunit_util::call_internal_method($helper, 'host_is_blocked', [$host],
                                                                          '\core\files\curl_security_helper'));
    }

    /**
     * Data provider for test_curl_security_helper_host_is_blocked().
     *
     * @return array
     */
    public function curl_security_host_data_provider() {
        return [
            // IPv4 hosts.
            ["127.0.0.1", "127.0.0.1", true],
            ["127.0.0.1", "127.0.0.0/24", true],
            ["127.0.0.1", "127.0.0.0-40", true],
            ["", "127.0.0.0/24", false],

            // IPv6 hosts.
            // Note: ["::", "::", true], - should match but 'address_in_subnet()' has trouble with fully collapsed IPv6 addresses.
            ["::1", "::1", true],
            ["::1", "::0-cccc", true],
            ["::1", "::0/64", true],
            ["FE80:0000:0000:0000:0000:0000:0000:0000", "fe80::/128", true],
            ["fe80::eeee", "fe80::ddde/64", true],
            ["fe80::dddd", "fe80::cccc-eeee", true],
            ["fe80::dddd", "fe80::ddde-eeee", false],

            // Domain name hosts.
            ["example.com", "example.com", true],
            ["sub.example.com", "example.com", false],
            ["example.com", "*.com", true],
            ["example.com", "*.example.com", false],
            ["sub.example.com", "*.example.com", true],
            ["sub.sub.example.com", "*.example.com", true],
            ["sub.example.com", "*example.com", false],
            ["sub.example.com", "*.example", false],

            // International domain name hosts.
            ["xn--nw2a.xn--j6w193g", "xn--nw2a.xn--j6w193g", true], // The domain 見.香港 is ace-encoded to xn--nw2a.xn--j6w193g.
        ];
    }

    /**
     * Test for \core\files\curl_security_helper->port_is_blocked().
     *
     * @param int|string $port the port to validate.
     * @param string $allowedports the list of allowed ports.
     * @param bool $expected the expected result.
     * @dataProvider curl_security_port_data_provider
     */
    public function test_curl_security_helper_port_is_blocked($port, $allowedports, $expected) {
        $this->resetAfterTest(true);
        $helper = new \core\files\curl_security_helper();
        set_config('curlsecurityallowedport', $allowedports);
        $this->assertEquals($expected, phpunit_util::call_internal_method($helper, 'port_is_blocked', [$port],
                                                                          '\core\files\curl_security_helper'));
    }

    /**
     * Data provider for test_curl_security_helper_port_is_blocked().
     *
     * @return array
     */
    public function curl_security_port_data_provider() {
        return [
            ["", "80\n443", true],
            [" ", "80\n443", true],
            ["-1", "80\n443", true],
            [-1, "80\n443", true],
            ["n", "80\n443", true],
            [0, "80\n443", true],
            ["0", "80\n443", true],
            [8080, "80\n443", true],
            ["8080", "80\n443", true],
            ["80", "80\n443", false],
            [80, "80\n443", false],
            [443, "80\n443", false],
            [0, "", true], // Port 0 and below are always invalid, even when the admin hasn't set allowed entries.
            [-1, "", true], // Port 0 and below are always invalid, even when the admin hasn't set allowed entries.
            [null, "", true], // Non-string, non-int values are invalid.
        ];
    }

    /**
     * Test for \core\files\curl_security_helper::get_blocked_url_string().
     */
    public function test_curl_security_helper_get_blocked_url_string() {
        $helper = new \core\files\curl_security_helper();
        $this->assertEquals(get_string('curlsecurityurlblocked', 'admin'), $helper->get_blocked_url_string());
    }
}
