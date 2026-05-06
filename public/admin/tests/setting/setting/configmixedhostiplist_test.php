<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

namespace core_admin\setting\setting;

use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Unit tests for the admin_setting_configmixedhostiplist class.
 *
 * @package    core_admin
 * @category   test
 * @copyright  2013 David Mudrak <david@moodle.com>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[CoversClass(configmixedhostiplist::class)]
final class configmixedhostiplist_test extends \advanced_testcase {
    /**
     * Test setting for blocked hosts.
     *
     * For testing the admin settings element only. Test for blocked hosts functionality can be found
     * in lib/tests/curl_security_helper_test.php
     */
    public function test_mixedhostiplist(): void {
        $this->resetAfterTest();

        $adminsetting = new configmixedhostiplist('abc_cde/hostiplist', 'some desc', '', '');

        // Test valid settings.
        $validsimplesettings = [
            'localhost',
            "localhost\n127.0.0.1",
            '192.168.10.1',
            '0:0:0:0:0:0:0:1',
            '::1',
            'fe80::',
            '231.54.211.0/20',
            'fe80::/64',
            '231.3.56.10-20',
            'fe80::1111-bbbb',
            '*.example.com',
            '*.sub.example.com',
        ];

        foreach ($validsimplesettings as $setting) {
            $errormessage = $adminsetting->write_setting($setting);
            $this->assertEmpty($errormessage, $errormessage);
            $this->assertSame($setting, get_config('abc_cde', 'hostiplist'));
            $this->assertSame($setting, $adminsetting->get_setting());
        }

        // Test valid international site names.
        $valididnsettings = [
            'правительство.рф' => 'xn--80aealotwbjpid2k.xn--p1ai',
            'faß.de' => 'xn--fa-hia.de',
            'ß.ß' => 'xn--zca.xn--zca',
            '*.tharkûn.com' => '*.xn--tharkn-0ya.com',
        ];

        foreach ($valididnsettings as $setting => $encodedsetting) {
            $errormessage = $adminsetting->write_setting($setting);
            $this->assertEmpty($errormessage, $errormessage);
            $this->assertSame($encodedsetting, get_config('abc_cde', 'hostiplist'));
            $this->assertSame($setting, $adminsetting->get_setting());
        }

        // Invalid settings.
        $this->assertEquals('These entries are invalid: nonvalid site name', $adminsetting->write_setting('nonvalid site name'));
        $this->assertEquals('Empty lines are not valid', $adminsetting->write_setting("localhost\n"));
    }
}
