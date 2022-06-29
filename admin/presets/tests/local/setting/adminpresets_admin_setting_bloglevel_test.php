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
 * Tests for the adminpresets_admin_setting_bloglevel class.
 *
 * @package    core_adminpresets
 * @category   test
 * @copyright  2021 Sara Arjona (sara@moodle.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \core_adminpresets\local\setting\adminpresets_admin_setting_bloglevel
 */
class adminpresets_admin_setting_bloglevel_test extends \advanced_testcase {

    /**
     * Test the behaviour of save_value() method.
     *
     * @covers ::save_value
     * @dataProvider save_value_provider
     *
     * @param int $settingvalue Setting value to be saved.
     * @param bool $expectedsaved Whether the setting will be saved or not.
     */
    public function test_save_value(int $settingvalue, bool $expectedsaved): void {
        global $DB;

        $this->resetAfterTest();

        // Login as admin, to access all the settings.
        $this->setAdminUser();

        // Set the config values (to confirm they change after applying the preset).
        set_config('bloglevel', BLOG_SITE_LEVEL); // All site users can see all blog entries.

        // Get the setting and save the value.
        $generator = $this->getDataGenerator()->get_plugin_generator('core_adminpresets');
        $setting = $generator->get_admin_preset_setting('blog', 'bloglevel');
        $result = $setting->save_value(false, $settingvalue);

        // Check the result is the expected (saved when it has a different value and ignored when the value is the same).
        if ($expectedsaved) {
            $this->assertCount(1, $DB->get_records('config_log', ['id' => $result]));
            // Specific from the save_value in adminpresets_admin_setting_bloglevel.
            if ($settingvalue != 0) {
                $this->assertTrue((bool) $DB->get_field('block', 'visible', ['name' => 'blog_menu']));
            } else {
                $this->assertFalse((bool) $DB->get_field('block', 'visible', ['name' => 'blog_menu']));
            }
        } else {
            $this->assertFalse($result);
        }
        $this->assertEquals($settingvalue, get_config('core', 'bloglevel'));
    }

    /**
     * Data provider for test_save_value().
     *
     * @return array
     */
    public function save_value_provider(): array {
        return [
            'Save the bloglevel and set blog_menu block visibility to true' => [
                'setttingvalue' => BLOG_USER_LEVEL,
                'expectedsaved' => true,
            ],
            'Same value to bloglevel, so it will not be saved' => [
                'setttingvalue' => BLOG_SITE_LEVEL,
                'expectedsaved' => false,
            ],
            'Save the bloglevel and set blog_menu block visibility to false' => [
                'setttingvalue' => 0,
                'expectedsaved' => true,
            ],
        ];
    }

}
