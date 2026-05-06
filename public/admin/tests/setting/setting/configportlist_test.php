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
 * Unit tests for the admin_setting_configportlist class.
 *
 * @package    core_admin
 * @category   test
 * @copyright  2013 David Mudrak <david@moodle.com>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[CoversClass(configportlist::class)]
final class configportlist_test extends \advanced_testcase {
    public function test_portlist(): void {
        $this->resetAfterTest();

        $adminsetting = new configportlist('abc_cde/portlist', 'some desc', '', '');

        // Test valid settings.
        $validsimplesettings = [
            '443',
            "80\n443",
        ];

        foreach ($validsimplesettings as $setting) {
            $errormessage = $adminsetting->write_setting($setting);
            $this->assertEmpty($errormessage, $errormessage);
            $this->assertSame($setting, get_config('abc_cde', 'portlist'));
            $this->assertSame($setting, $adminsetting->get_setting());
        }

        // Invalid settings.
        $this->assertEquals('These entries are invalid: cat, dog', $adminsetting->write_setting("cat\ndog"));
        $this->assertEquals('Empty lines are not valid', $adminsetting->write_setting("80\n"));
    }
}
