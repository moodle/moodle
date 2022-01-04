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

namespace core_adminpresets\local\setting;

/**
 * Tests for the adminpresets_admin_setting_sitesettext class.
 *
 * @package    core_adminpresets
 * @category   test
 * @copyright  2021 Sara Arjona (sara@moodle.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \core_adminpresets\local\setting\adminpresets_admin_setting_sitesettext
 */
class adminpresets_admin_setting_sitesettext_test extends \advanced_testcase {

    /**
     * Test the behaviour of save_value() method.
     *
     * @covers ::save_value
     * @dataProvider save_value_provider
     *
     * @param string $settingname Setting name to save.
     * @param string $settingvalue Setting value to be saved.
     * @param bool $expectedsaved Whether the setting will be saved or not.
     */
    public function test_save_value(string $settingname, string $settingvalue, bool $expectedsaved): void {
        global $DB;

        $this->resetAfterTest();

        // Login as admin, to access all the settings.
        $this->setAdminUser();

        // Get the setting and save the value.
        $generator = $this->getDataGenerator()->get_plugin_generator('core_adminpresets');
        $setting = $generator->get_admin_preset_setting('frontpagesettings', $settingname);
        $result = $setting->save_value(false, $settingvalue);

        // Check the result is the expected (saved when it has a different value and ignored when the value is the same).
        if ($expectedsaved) {
            $this->assertCount(1, $DB->get_records('config_log', ['id' => $result]));
            // Specific from the save_value in adminpresets_admin_setting_sitesettext.
            $sitecourse = $DB->get_record('course', ['id' => 1]);
            $this->assertEquals($settingvalue, $sitecourse->{$settingname});
        } else {
            $this->assertFalse($result);
        }
    }

    /**
     * Data provider for test_save_value().
     *
     * @return array
     */
    public function save_value_provider(): array {
        return [
            'Fullname: different value' => [
                'settingname' => 'fullname',
                'setttingvalue' => 'New site fullname',
                'expectedsaved' => true,
            ],
            'Fullname: same value' => [
                'settingname' => 'fullname',
                'setttingvalue' => 'PHPUnit test site',
                'expectedsaved' => false,
            ],
            'Summary: different value' => [
                'settingname' => 'summary',
                'setttingvalue' => 'This is a new site summary.',
                'expectedsaved' => true,
            ],
            'Summary: same value' => [
                'settingname' => 'summary',
                'setttingvalue' => '',
                'expectedsaved' => false,
            ],
        ];
    }

}
