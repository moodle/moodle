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
 * Tests for the adminpresets_setting class.
 *
 * @package    core_adminpresets
 * @category   test
 * @copyright  2021 Sara Arjona (sara@moodle.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \core_adminpresets\local\setting\adminpresets_setting
 */
class adminpresets_setting_test extends \advanced_testcase {

    /**
     * Test the behaviour of save_value() method.
     *
     * @covers ::save_value
     * @dataProvider save_value_provider
     *
     * @param string $category Admin tree where the setting belongs.
     * @param string $settingplugin Plugin where the setting belongs.
     * @param string $settingname Setting name.
     * @param string $settingvalue Setting value to be saved.
     * @param bool $expectedsaved Whether the setting will be saved or not.
     */
    public function test_save_value(string $category, string $settingplugin, string $settingname, string $settingvalue,
            bool $expectedsaved): void {
        global $DB;

        $this->resetAfterTest();

        // Login as admin, to access all the settings.
        $this->setAdminUser();

        // Set the config values (to confirm they change after applying the preset).
        set_config('enablebadges', 1);
        set_config('mediawidth', '640', 'mod_lesson');

        // The expected setting name in the admin tree is $plugin.$name when plugin is not core.
        if ($settingplugin !== 'core') {
            $name = $settingplugin . $settingname;
        } else {
            $name = $settingname;
        }
        // Get the setting and save the value.
        $generator = $this->getDataGenerator()->get_plugin_generator('core_adminpresets');
        $setting = $generator->get_admin_preset_setting($category, $name);
        $result = $setting->save_value(false, $settingvalue);

        // Check the result is the expected (saved when it has a different value and ignored when the value is the same).
        if ($expectedsaved) {
            $this->assertCount(1, $DB->get_records('config_log', ['id' => $result]));
        } else {
            $this->assertFalse($result);
        }
        $this->assertEquals($settingvalue, get_config($settingplugin, $settingname));
    }

    /**
     * Data provider for test_save_value().
     *
     * @return array
     */
    public static function save_value_provider(): array {
        return [
            'Core setting with the same value is not saved' => [
                'category' => 'optionalsubsystems',
                'settingplugin' => 'core',
                'settingname' => 'enablebadges',
                'setttingvalue' => '1',
                'expectedsaved' => false,
            ],
            'Core setting with a different value is saved' => [
                'category' => 'optionalsubsystems',
                'settingplugin' => 'core',
                'settingname' => 'enablebadges',
                'setttingvalue' => '0',
                'expectedsaved' => true,
            ],
            'Plugin setting with the same value is not saved' => [
                'category' => 'modsettinglesson',
                'settingplugin' => 'mod_lesson',
                'settingname' => 'mediawidth',
                'setttingvalue' => '640',
                'expectedsaved' => false,
            ],
            'Plugin setting with different value is saved' => [
                'category' => 'modsettinglesson',
                'settingplugin' => 'mod_lesson',
                'settingname' => 'mediawidth',
                'setttingvalue' => '900',
                'expectedsaved' => true,
            ],
        ];
    }

    /**
     * Test the behaviour of save_attributes_values() method.
     *
     * @covers ::save_attributes_values
     * @dataProvider save_attributes_values_provider
     *
     * @param string $category Admin tree where the setting belongs.
     * @param string $settingplugin Plugin where the setting belongs.
     * @param string $settingname Setting name.
     * @param string|null $advsettingname Advanced setting name.
     * @param string $advsettingvalue Advanced setting value to be saved.
     * @param bool $expectedsaved Whether the setting will be saved or not.
     */
    public function test_save_attributes_values(string $category, string $settingplugin, string $settingname,
            ?string $advsettingname, string $advsettingvalue, bool $expectedsaved): void {
        global $DB;

        $this->resetAfterTest();

        // Login as admin, to access all the settings.
        $this->setAdminUser();

        // Set the config values (to confirm they change after applying the preset).
        set_config('maxanswers_adv', '1', 'mod_lesson');

        // The expected setting name in the admin tree is $plugin.$name when plugin is not core.
        if ($settingplugin !== 'core') {
            $name = $settingplugin . $settingname;
        } else {
            $name = $settingname;
        }
        // Get the setting and save the value.
        $generator = $this->getDataGenerator()->get_plugin_generator('core_adminpresets');
        $setting = $generator->get_admin_preset_setting($category, $name);
        if ($advsettingname) {
            $setting->set_attribute_value($advsettingname, $advsettingvalue);
        }
        $result = $setting->save_attributes_values();

        // Check the result is the expected (saved when it has a different value and ignored when the value is the same).
        if ($expectedsaved) {
            $this->assertCount(1, $result);
            $configlog = reset($result);
            $this->assertCount(1, $DB->get_records('config_log', ['id' => $configlog]));
        } else {
            $this->assertFalse($result);
        }
        if ($advsettingname) {
            $this->assertEquals($advsettingvalue, get_config($settingplugin, $advsettingname));
        }
    }

    /**
     * Data provider for test_save_attributes_values().
     *
     * @return array
     */
    public static function save_attributes_values_provider(): array {
        return [
            'Plugin setting with the same value is not saved' => [
                'category' => 'modsettinglesson',
                'settingplugin' => 'mod_lesson',
                'settingname' => 'maxanswers',
                'advsettingname' => 'maxanswers_adv',
                'advsetttingvalue' => '1',
                'expectedsaved' => false,
            ],
            'Plugin setting with different value is saved' => [
                'category' => 'modsettinglesson',
                'settingplugin' => 'mod_lesson',
                'settingname' => 'maxanswers',
                'advsettingname' => 'maxanswers_adv',
                'advsetttingvalue' => '0',
                'expectedsaved' => true,
            ],
            'Plugin setting without advanced attributes are not saved' => [
                'category' => 'modsettinglesson',
                'settingplugin' => 'mod_lesson',
                'settingname' => 'maxanswers',
                'advsettingname' => null,
                'advsetttingvalue' => '0',
                'expectedsaved' => false,
            ],
        ];
    }
}
