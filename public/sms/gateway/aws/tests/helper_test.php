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

namespace smsgateway_aws;

/**
 * AWS SMS gateway helper tests.
 *
 * @package    smsgateway_aws
 * @category   test
 * @copyright  2024 Safat Shahin <safat.shahin@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \smsgateway_aws\helper
 */
final class helper_test extends \advanced_testcase {

    /**
     * Data provider for test_format_number().
     *
     * @return array of different country codes and phone numbers.
     */
    public static function format_number_provider(): array {

        return [
            'Phone number with local format' => [
                'phonenumber' => '0123456789',
                'expected' => '+34123456789',
                'countrycode' => '34',
            ],
            'Phone number with international format' => [
                'phonenumber' => '+39123456789',
                'expected' => '+39123456789',
            ],
            'Phone number with spaces using international format' => [
                'phonenumber' => '+34 123 456 789',
                'expected' => '+34123456789',
            ],
            'Phone number with spaces using local format with country code' => [
                'phonenumber' => '0 123 456 789',
                'expected' => '+34123456789',
                'countrycode' => '34',
            ],
            'Phone number with spaces using local format without country code' => [
                'phonenumber' => '0 123 456 789',
                'expected' => '123456789',
            ],
        ];
    }

    /**
     * Test format number with different phones and different country codes.
     *
     * @dataProvider format_number_provider
     * @param string $phonenumber Phone number.
     * @param string $expected Expected value.
     * @param string|null $countrycode Country code.
     */
    public function test_format_number(
        string $phonenumber,
        string $expected,
        ?string $countrycode = null,
    ): void {
        $this->resetAfterTest();
        $this->assertEquals($expected, \core_sms\manager::format_number($phonenumber, $countrycode));
    }

    /**
     * Test the proxy strings.
     */
    public function test_get_proxy_string(): void {
        global $CFG;
        $this->resetAfterTest();
        // Confirm with no config an empty string is returned.
        $CFG->proxyhost = '';
        $this->assertEquals('', helper::get_proxy_string());

        // Now set some configs.
        $CFG->proxyhost = '127.0.0.1';
        $CFG->proxyuser = 'user';
        $CFG->proxypassword = 'password';
        $CFG->proxyport = '1337';
        $this->assertEquals('user:password@127.0.0.1:1337', helper::get_proxy_string());

        // Now change to SOCKS proxy.
        $CFG->proxytype = 'SOCKS5';
        $this->assertEquals('socks5://user:password@127.0.0.1:1337', helper::get_proxy_string());
    }
}
