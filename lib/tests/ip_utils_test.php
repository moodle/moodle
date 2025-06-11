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
 * This tests the static helper functions contained in the class '\core\ip_utils'.
 *
 * @package    core
 * @covers     \core\ip_utils
 * @copyright  2016 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class ip_utils_test extends \basic_testcase {
    /**
     * Test for \core\ip_utils::is_domain_name().
     *
     * @param string $domainname the domain name to validate.
     * @param bool $expected the expected result.
     * @dataProvider domain_name_data_provider
     */
    public function test_is_domain_name($domainname, $expected): void {
        $this->assertEquals($expected, \core\ip_utils::is_domain_name($domainname));
    }

    /**
     * Data provider for test_is_domain_name().
     *
     * @return array
     */
    public static function domain_name_data_provider(): array {
        return [
            ["com", true],
            ["i.net", true], // Single char, alpha tertiary domain.
            ["0.org", true], // Single char, non-alpha tertiary domain.
            ["0.a", true], // Single char, alpha top level domain.
            ["0.1", false], // Single char, non-alpha top level domain.
            ["example.com", true],
            ["sub.example.com", true],
            ["sub-domain.example-domain.net", true],
            ["123.com", true],
            ["123.a11", true],
            [str_repeat('sub.', 60) . "1-example.com", true], // Max length without null label is 253 octets = 253 ascii chars.
            [str_repeat('example', 9) . ".com", true], // Max number of octets per label is 63  = 63 ascii chars.
            ["localhost", true],
            [" example.com", false],
            ["example.com ", false],
            ["example.com/", false],
            ["*.example.com", false],
            ["*example.com", false],
            ["example.123", false],
            ["-example.com", false],
            ["example-.com", false],
            [".example.com", false],
            ["127.0.0.1", false],
            [str_repeat('sub.', 60) . "11-example.com", false], // Name length is 254 chars, which exceeds the max allowed.
            [str_repeat('example', 9) . "1.com", false], // Label length is 64 chars, which exceed the max allowed.
            ["example.com.", true], // Null label explicitly provided - this is valid.
            [".example.com.", false],
            ["見.香港", false], // IDNs are invalid.
            [null, false], // Non-strings are invalid.
        ];
    }

    /**
     * Test for \core\ip_utils::is_domain_matching_pattern().
     *
     * @param string $str the string to evaluate.
     * @param bool $expected the expected result.
     * @dataProvider domain_matching_patterns_data_provider
     */
    public function test_is_domain_matching_pattern($str, $expected): void {
        $this->assertEquals($expected, \core\ip_utils::is_domain_matching_pattern($str));
    }

    /**
     * Data provider for test_is_domain_matching_pattern().
     *
     * @return array
     */
    public static function domain_matching_patterns_data_provider(): array {
        return [
            ["*.com", true],
            ["*.example.com", true],
            ["*.example.com", true],
            ["*.sub.example.com", true],
            ["*.sub-domain.example-domain.com", true],
            ["*." . str_repeat('sub.', 60) . "example.com", true], // Max number of domain name chars = 253.
            ["*." . str_repeat('example', 9) . ".com", true], // Max number of domain name label chars = 63.
            ["*com", false],
            ["*example.com", false],
            [" *.example.com", false],
            ["*.example.com ", false],
            ["*-example.com", false],
            ["*.-example.com", false],
            ["*.example.com/", false],
            ["sub.*.example.com", false],
            ["sub.*example.com", false],
            ["*.*.example.com", false],
            ["example.com", false],
            ["*." . str_repeat('sub.', 60) . "1example.com", false], // Name length is 254 chars, which exceeds the max allowed.
            ["*." . str_repeat('example', 9) . "1.com", false], // Label length is 64 chars, which exceed the max allowed.
            ["*.example.com.", true], // Null label explicitly provided - this is valid.
            [".*.example.com.", false],
            ["*.香港", false], // IDNs are invalid.
            [null, false], // None-strings are invalid.
        ];
    }

    /**
     * Test for \core\ip_utils::is_ip_address().
     *
     * @param string $address the address to validate.
     * @param bool $expected the expected result.
     * @dataProvider ip_address_data_provider
     */
    public function test_is_ip_address($address, $expected): void {
        $this->assertEquals($expected, \core\ip_utils::is_ip_address($address));
    }

    /**
     * Data provider for test_is_ip_address().
     *
     * @return array
     */
    public static function ip_address_data_provider(): array {
        return [
            ["127.0.0.1", true],
            ["10.1", false],
            ["0.0.0.0", true],
            ["255.255.255.255", true],
            ["256.0.0.1", false],
            ["256.0.0.1", false],
            ["127.0.0.0/24", false],
            ["127.0.0.0-255", false],
            ["::", true],
            ["::0", true],
            ["0::", true],
            ["0::0", true],
            ["fe80:fe80:fe80:fe80:fe80:fe80:fe80:fe80", true],
            ["fe80::ffff", true],
            ["fe80::f", true],
            ["fe80::", true],
            ["0", false],
            ["127.0.0.0/24", false],
            ["fe80::fe80/128", false],
            ["fe80:fe80:fe80:fe80:fe80:fe80:fe80:fe80/128", false],
            ["fe80:", false],
            ["fe80:: ", false],
            [" fe80::", false],
            ["fe80::ddddd", false],
            ["fe80::gggg", false],
            ["fe80:fe80:fe80:fe80:fe80:fe80:fe80:fe80:fe80", false],
        ];
    }

    /**
     * Test for \core\ip_utils::is_ipv4_address().
     *
     * @param string $address the address to validate.
     * @param bool $expected the expected result.
     * @dataProvider ipv4_address_data_provider
     */
    public function test_is_ipv4_address($address, $expected): void {
        $this->assertEquals($expected, \core\ip_utils::is_ipv4_address($address));
    }

    /**
     * Data provider for test_is_ipv4_address().
     *
     * @return array
     */
    public static function ipv4_address_data_provider(): array {
        return [
            ["127.0.0.1", true],
            ["0.0.0.0", true],
            ["255.255.255.255", true],
            [" 127.0.0.1", false],
            ["127.0.0.1 ", false],
            ["-127.0.0.1", false],
            ["127.0.1", false],
            ["127.0.0.0.1", false],
            ["a.b.c.d", false],
            ["localhost", false],
            ["fe80::1", false],
            ["256.0.0.1", false],
            ["256.0.0.1", false],
            ["127.0.0.0/24", false],
            ["127.0.0.0-255", false],
        ];
    }

    /**
     * Test for \core\ip_utils::is_ipv4_range().
     *
     * @param string $addressrange the address range to validate.
     * @param bool $expected the expected result.
     * @dataProvider ipv4_range_data_provider
     */
    public function test_is_ipv4_range($addressrange, $expected): void {
        $this->assertEquals($expected, \core\ip_utils::is_ipv4_range($addressrange));
    }

    /**
     * Data provider for test_is_ipv4_range().
     *
     * @return array
     */
    public static function ipv4_range_data_provider(): array {
        return [
            ["127.0.0.1/24", true],
            ["127.0.0.20-20", true],
            ["127.0.0.20-50", true],
            ["127.0.0.0-255", true],
            ["127.0.0.1-1", true],
            ["255.255.255.0-255", true],
            ["127.0.0.1", false],
            ["127.0", false],
            [" 127.0.0.0/24", false],
            ["127.0.0.0/24 ", false],
            ["a.b.c.d/24", false],
            ["256.0.0.0-80", false],
            ["127.0.0.0/a", false],
            ["256.0.0.0/24", false],
            ["127.0.0.0/-1", false],
            ["127.0.0.0/33", false],
            ["127.0.0.0-127.0.0.10", false],
            ["127.0.0.30-20", false],
            ["127.0.0.0-256", false],
            ["fe80::fe80/64", false],
        ];
    }

    /**
     * Test for \core\ip_utils::is_ipv6_address().
     *
     * @param string $address the address to validate.
     * @param bool $expected the expected result.
     * @dataProvider ipv6_address_data_provider
     */
    public function test_is_ipv6_address($address, $expected): void {
        $this->assertEquals($expected, \core\ip_utils::is_ipv6_address($address));
    }

    /**
     * Data provider for test_is_ipv6_address().
     *
     * @return array
     */
    public static function ipv6_address_data_provider(): array {
        return [
            ["::", true],
            ["::0", true],
            ["0::", true],
            ["0::0", true],
            ["fe80:fe80:fe80:fe80:fe80:fe80:fe80:fe80", true],
            ["fe80::ffff", true],
            ["fe80::f", true],
            ["fe80::", true],
            ["0", false],
            ["127.0.0.0", false],
            ["127.0.0.0/24", false],
            ["fe80::fe80/128", false],
            ["fe80:fe80:fe80:fe80:fe80:fe80:fe80:fe80/128", false],
            ["fe80:", false],
            ["fe80:: ", false],
            [" fe80::", false],
            ["fe80::ddddd", false],
            ["fe80::gggg", false],
            ["fe80:fe80:fe80:fe80:fe80:fe80:fe80:fe80:fe80", false],
        ];
    }

    /**
     * Test for \core\ip_utils::is_ipv6_range().
     *
     * @param string $addressrange the address range to validate.
     * @param bool $expected the expected result.
     * @dataProvider ipv6_range_data_provider
     */
    public function test_is_ipv6_range($addressrange, $expected): void {
        $this->assertEquals($expected, \core\ip_utils::is_ipv6_range($addressrange));
    }

    /**
     * Data provider for test_is_ipv6_range().
     *
     * @return array
     */
    public static function ipv6_range_data_provider(): array {
        return [
            ["::/128", true],
            ["::1/128", true],
            ["fe80:fe80:fe80:fe80:fe80:fe80:fe80:fe80/128", true],
            ["fe80::dddd/128", true],
            ["fe80::/64", true],
            ["fe80::dddd-ffff", true],
            ["::0-ffff", true],
            ["::a-ffff", true],
            ["0", false],
            ["::1", false],
            ["fe80::fe80", false],
            ["::/128 ", false],
            [" ::/128", false],
            ["::/a", false],
            ["::/-1", false],
            ["fe80::fe80/129", false],
            ["fe80:fe80:fe80:fe80:fe80:fe80:fe80:fe80", false],
            ["fe80::bbbb-aaaa", false],
            ["fe80::0-fffg", false],
            ["fe80::0-fffff", false],
            ["fe80::0 - ffff", false],
            [" fe80::0-ffff", false],
            ["fe80::0-ffff ", false],
            ["192.0.0.0/24", false],
            ["fe80:::fe80/128", false],
            ["fe80:::aaaa-dddd", false],
        ];
    }

    /**
     * Test checking domains against a list of allowed domains.
     *
     * @param  bool $expected Expected result
     * @param  string $domain domain address
     * @dataProvider data_domain_addresses
     */
    public function test_check_domain_against_allowed_domains($expected, $domain): void {
        $alloweddomains = ['example.com',
                           '*.moodle.com',
                           '*.per.this.penny-arcade.com',
                           'bad.*.url.com',
                           ' trouble.com.au'];
        $this->assertEquals($expected, \core\ip_utils::is_domain_in_allowed_list($domain, $alloweddomains));
    }

    /**
     * Data provider for test_check_domain_against_allowed_domains.
     *
     * @return array
     */
    public static function data_domain_addresses(): array {
        return [
            [true, 'example.com'],
            [true, 'ExAmPle.com'],
            [false, 'sub.example.com'],
            [false, 'example.com.au'],
            [false, ' example.com'], // A space at the front of the domain is invalid.
            [false, 'example.123'], // Numbers at the end is invalid.
            [false, 'test.example.com'],
            [false, 'moodle.com'],
            [true, 'test.moodle.com'],
            [true, 'TeSt.moodle.com'],
            [true, 'test.MoOdLe.com'],
            [false, 'test.moodle.com.au'],
            [true, 'nice.address.per.this.penny-arcade.com'],
            [false, 'normal.per.this.penny-arcade.com.au'],
            [false, 'bad.thing.url.com'], // The allowed domain (above) has a bad wildcard and so this address will return false.
            [false, 'trouble.com.au'] // The allowed domain (above) has a space at the front and so will return false.
        ];
    }

    /**
     * Data provider for test_is_ip_in_subnet_list.
     *
     * @return array
     */
    public static function data_is_ip_in_subnet_list(): array {
        return [
            [true, '1.1.1.1', '1.1.1.1', "\n"],
            [false, '1.1.1.1', '2.2.2.2', "\n"],
            [true, '1.1.1.1', "1.1.1.5\n1.1.1.1", "\n"],
            [true, '1.1.1.1', "1.1.1.5,1.1.1.1", ","],
        ];
    }

    /**
     * Test checking ips against a list of allowed domains.
     *
     * @param  bool $expected Expected result
     * @param  string $ip IP address
     * @param  string $list list of  IP subnets
     * @param  string $delim delimiter of list
     * @dataProvider data_is_ip_in_subnet_list
     */
    public function test_is_ip_in_subnet_list($expected, $ip, $list, $delim): void {
        $this->assertEquals($expected, \core\ip_utils::is_ip_in_subnet_list($ip, $list, $delim));
    }

    /**
     * Data provider for test_normalize_internet_address.
     *
     * @return array
     */
    public static function normalize_internet_address_provider(): array {
        return [
            'Strip all white spaces on IP address' => [
                '   192.168.5.5  ',
                '192.168.5.5',
            ],
            'Strip all white spaces on domain name' => [
                ' www.moodle.org   ',
                'www.moodle.org',
            ],
            'Preserve IPv4 address' => [
                '127.0.0.1',
                '127.0.0.1',
            ],
            'Preserve IPv4 address range' => [
                '192.168.0.0/16',
                '192.168.0.0/16',
            ],
            'Preserve IPv6 address' => [
                'fe80:fe80:fe80:fe80:fe80:fe80:fe80:fe80',
                'fe80:fe80:fe80:fe80:fe80:fe80:fe80:fe80',
            ],
            'Preserve IPv6 address range' => [
                'fe80::ffff',
                'fe80::ffff',
            ],
            'Preserve valid domain' => [
                'localhost',
                'localhost',
            ],
            'Preserve valid FQDN' => [
                'www.moodle.org',
                'www.moodle.org',
            ],
            'Preserve valid FQDN with trailing dot' => [
                'www.moodle.com.',
                'www.moodle.com',
            ],
            'Preserve valid domain with wildcard' => [
                '*.moodledev.io',
                '*.moodledev.io',
            ],
            'Convert previous allowed "127." format to CIDR format (127.0.0.0/8)' => [
                '127.',
                '127.0.0.0/8',
            ],
            'Convert previous allowed "169.8." format to CIDR format (169.8.0.0/16)' => [
                '169.8.',
                '169.8.0.0/16',
            ],
            'Convert previous allowed "192.168.10." format to CIDR format (192.168.10.0/24)' => [
                '192.168.10.',
                '192.168.10.0/24',
            ],
            'Convert previous allowed ".moodle.org" subdomain format to new format (*.moodle.org)' => [
                '.moodle.org',
                '*.moodle.org',
            ],
            'Ignore invalid IPv4' => [
                '327.0.0.1',
                '',
            ],
            'Ignore invalid IPv4 range' => [
                '192.168',
                '',
            ],
            'Ignore invalid IPv6' => [
                'fe80::ddddd',
                '',
            ],
            'Ignore invalid IPv6 range' => [
                'fe80:',
                '',
            ],
            'Ignore invalid domain' => [
                '-example.com',
                '',
            ],
        ];
    }

    /**
     * Test if input address value is correctly normalized.
     *
     * @covers ::normalize_internet_address
     *
     * @dataProvider normalize_internet_address_provider
     *
     * @param string $input    Raw input value.
     * @param string $expected Expected value after normalization.
     */
    public function test_normalize_internet_address(string $input, string $expected): void {
        $this->assertEquals($expected, \core\ip_utils::normalize_internet_address($input));
    }

    /**
     * Data provider for test_normalize_internet_address_list.
     *
     * @return array
     */
    public static function normalize_internet_address_list_provider(): array {
        return [
            'Strip all white spaces' => [
                '   192.168.5.5, 127.0.0.1,    www.moodle.org   ',
                '192.168.5.5,127.0.0.1,www.moodle.org',
            ],
            'Trim input' => [
                '    192.168.5.5,127.0.0.1,www.moodle.org   ',
                '192.168.5.5,127.0.0.1,www.moodle.org',
            ],
            'Preserve valid full and partial IP' => [
                '127.0.0.1,192.168.0.0/16,fe80:fe80:fe80:fe80:fe80:fe80:fe80:fe80,fe80::ffff',
                '127.0.0.1,192.168.0.0/16,fe80:fe80:fe80:fe80:fe80:fe80:fe80:fe80,fe80::ffff',
            ],
            'Convert previous allowed format to new allowed format' => [
                '127.,169.8.,192.168.10.,.moodle.org',
                '127.0.0.0/8,169.8.0.0/16,192.168.10.0/24,*.moodle.org',
            ],
            'Preserve valid domain and pattern domain' => [
                'localhost,www.moodle.org,.moodle.com,*.moodledev.io',
                'localhost,www.moodle.org,*.moodle.com,*.moodledev.io',
            ],
            'Remove all invalid IP and domains' => [
                '327.0.0.1,192.168,fe80::ddddd,fe80:,-example.com',
                '',
            ],
            'Remove duplicate values' => [
                '.moodle.org,*.moodle.org,*.moodle.org,.moodle.org',
                '*.moodle.org',
            ],
        ];
    }

    /**
     * Test if input address list is correctly normalized.
     *
     * @covers ::normalize_internet_address_list
     *
     * @dataProvider normalize_internet_address_list_provider
     *
     * @param string $input    Raw input value.
     * @param string $expected Expected value after normalization.
     */
    public function test_normalize_internet_address_list(string $input, string $expected): void {
        $this->assertEquals($expected, \core\ip_utils::normalize_internet_address_list($input));
    }
}
