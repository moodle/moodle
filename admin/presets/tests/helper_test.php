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

namespace core_adminpresets;

/**
 * Tests for the helper class.
 *
 * @package    core_adminpresets
 * @category   test
 * @copyright  2021 Sara Arjona (sara@moodle.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \core_adminpresets\helper
 */
class helper_test extends \advanced_testcase {

    /**
     * Test the behaviour of create_preset() method.
     *
     * @covers ::create_preset
     * @dataProvider create_preset_provider
     *
     * @param string|null $name Preset name field.
     * @param string|null $comments Preset comments field.
     * @param int|null $iscore Preset iscore field.
     * @param int|null $iscoreresult Expected iscore value for the result preset.
     */
    public function test_create_preset(?string $name = null, ?string $comments = null, ?int $iscore = null,
           ?int $iscoreresult = null): void {
        global $CFG, $DB, $USER;

        $this->resetAfterTest();

        $data = [];
        if (isset($name)) {
            $data['name'] = $name;
        }
        if (isset($comments)) {
            $data['comments'] = $comments;
        }
        if (isset($iscore)) {
            $data['iscore'] = $iscore;
        }
        if (!isset($iscoreresult)) {
            $iscoreresult = manager::NONCORE_PRESET;
        }

        // Create a preset.
        $presetid = helper::create_preset($data);

        // Check the preset data.
        $preset = $DB->get_record('adminpresets', ['id' => $presetid]);

        $this->assertEquals($name, $preset->name);
        $this->assertEquals($comments, $preset->comments);
        $this->assertEquals(fullname($USER), $preset->author);
        $this->assertEquals($iscoreresult, $preset->iscore);
        $this->assertEquals($CFG->version, $preset->moodleversion);
        $this->assertEquals($CFG->release, $preset->moodlerelease);
        $this->assertEquals($CFG->wwwroot, $preset->site);

        // Check the preset is empty and hasn't settings or plugins.
        $settings = $DB->get_records('adminpresets_it', ['adminpresetid' => $presetid]);
        $this->assertCount(0, $settings);
        $plugins = $DB->get_records('adminpresets_plug', ['adminpresetid' => $presetid]);
        $this->assertCount(0, $plugins);
    }

    /**
     * Data provider for test_create_preset().
     *
     * @return array
     */
    public static function create_preset_provider(): array {
        return [
            'Default values' => [
            ],
            'Name not empty' => [
                'name' => 'Preset xaxi name',
            ],
            'Comments not empty' => [
                'name' => null,
                'comments' => 'This is a different comment',
            ],
            'Name and comments not empty' => [
                'name' => 'Preset with a super-nice name',
                'comments' => 'This is a comment different from the previous one',
            ],
            'Starter preset' => [
                'name' => 'Starter',
                'comments' => null,
                'iscore' => manager::STARTER_PRESET,
                'iscoreresult' => manager::STARTER_PRESET,
            ],
            'Full preset' => [
                'name' => 'Full',
                'comments' => null,
                'iscore' => manager::FULL_PRESET,
                'iscoreresult' => manager::FULL_PRESET,
            ],
            'Invalid iscore' => [
                'name' => 'Invalid iscore value',
                'comments' => null,
                'iscore' => -1,
                'iscoreresult' => manager::NONCORE_PRESET,
            ],
        ];
    }

    /**
     * Test the behaviour of add_item() method.
     *
     * @covers ::add_item
     * @dataProvider add_item_provider
     *
     * @param string $name Item name.
     * @param string $value Item value.
     * @param string|null $plugin Item plugin.
     * @param string|null $advname If the item is an advanced setting, the name of the advanced setting should be specified here.
     * @param string|null $advvalue If the item is an advanced setting, the value of the advanced setting should be specified here.
     */
    public function test_add_item(string $name, string $value, ?string $plugin = 'none', ?string $advname = null,
            ?string $advvalue = null): void {
        global $DB;

        $this->resetAfterTest();

        // Create a preset.
        $presetid = helper::create_preset([]);
        $this->assertEquals(1, $DB->count_records('adminpresets', ['id' => $presetid]));

        // Add items.
        $itemid = helper::add_item($presetid, $name, $value, $plugin, $advname, $advvalue);

        // Check settings have been created.
        $settings = $DB->get_records('adminpresets_it', ['adminpresetid' => $presetid]);
        $this->assertCount(1, $settings);

        $setting = reset($settings);
        $this->assertEquals($itemid, $setting->id);
        $this->assertEquals($name, $setting->name);
        $this->assertEquals($value, $setting->value);
        $this->assertEquals($plugin, $setting->plugin);

        if ($advname) {
            // Check settings have been created.
            $advsettings = $DB->get_records('adminpresets_it_a', ['itemid' => $itemid]);
            $this->assertCount(1, $advsettings);

            $advsetting = reset($advsettings);
            $this->assertEquals($advname, $advsetting->name);
            $this->assertEquals($advvalue, $advsetting->value);
        } else {
            // Check no advanced items have been created.
            $this->assertEquals(0, $DB->count_records('adminpresets_it_a', ['itemid' => $itemid]));
        }

        // Check no plugins have been created.
        $this->assertEquals(0, $DB->count_records('adminpresets_plug', ['adminpresetid' => $presetid]));
    }

    /**
     * Data provider for test_add_item().
     *
     * @return array
     */
    public static function add_item_provider(): array {
        return [
            'Setting without plugin' => [
                'name' => 'settingname',
                'value' => 'thisisthevalue',
            ],
            'Setting with plugin' => [
                'name' => 'settingname',
                'value' => 'thisisthevalue',
                'plugin' => 'pluginname',
            ],
            'Setting with advanced item' => [
                'name' => 'settingname',
                'value' => 'thevalue',
                'plugin' => 'pluginname',
                'advname' => 'advsettingname',
                'advvalue' => 'advsettingvalue',
            ],
        ];
    }

    /**
     * Test the behaviour of add_plugin() method.
     *
     * @covers ::add_plugin
     * @dataProvider add_plugin_provider
     *
     * @param string $type Plugin type.
     * @param string $name Plugin name.
     * @param mixed $enabled Whether the plugin will be enabled or not.
     */
    public function test_add_plugin(string $type, string $name, $enabled = 0): void {
        global $DB;

        $this->resetAfterTest();

        // Create a preset.
        $presetid = helper::create_preset([]);
        $this->assertEquals(1, $DB->count_records('adminpresets', ['id' => $presetid]));

        // Add plugin.
        $pluginid = helper::add_plugin($presetid, $type, $name, $enabled);

        // Check plugin has been created.
        $pluggins = $DB->get_records('adminpresets_plug', ['adminpresetid' => $presetid]);
        $this->assertCount(1, $pluggins);

        $plugin = reset($pluggins);
        $this->assertEquals($pluginid, $plugin->id);
        $this->assertEquals($type, $plugin->plugin);
        $this->assertEquals($name, $plugin->name);
        $this->assertEquals((int) $enabled, $plugin->enabled);

        // Check no settings have been created.
        $this->assertEquals(0, $DB->count_records('adminpresets_it', ['adminpresetid' => $presetid]));
    }

    /**
     * Data provider for test_add_plugin().
     *
     * @return array
     */
    public static function add_plugin_provider(): array {
        return [
            'Plugin: enabled (using int)' => [
                'type' => 'plugintype',
                'name' => 'pluginname',
                'enabled' => 1,
            ],
            'Plugin: enabled (using bool)' => [
                'type' => 'plugintype',
                'name' => 'pluginname',
                'enabled' => true,
            ],
            'Plugin: disabled (using int)' => [
                'type' => 'plugintype',
                'name' => 'pluginname',
                'enabled' => 0,
            ],
            'Plugin: disabled (using bool)' => [
                'type' => 'plugintype',
                'name' => 'pluginname',
                'enabled' => false,
            ],
            'Plugin: negative int value' => [
                'type' => 'plugintype',
                'name' => 'pluginname',
                'enabled' => -9999,
            ],
        ];
    }

    /**
     * Test the behaviour of change_default_preset() method.
     *
     * @covers ::change_default_preset
     * @dataProvider change_default_preset_provider
     *
     * @param string $preset The preset name to apply or the path to the XML to be imported and applied.
     * @param array|null $settings A few settings to check (with their expected values).
     * @param array|null $plugins A few module plugins to check (with their expected values for the visibility).
     */
    public function test_change_default_preset(string $preset, ?array $settings = null, ?array $plugins = null): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        // We need to change some of the default values; otherwise, the full preset won't be applied, because all the settings
        // and plugins are the same.
        set_config('enableanalytics', '0');

        $generator = $this->getDataGenerator()->get_plugin_generator('core_adminpresets');
        $generator->create_preset(['name' => 'Preset 1']);

        $presetid = helper::change_default_preset($preset);

        if (empty($settings) && empty($plugins)) {
            // The preset hasn't been applied.
            $this->assertNull($presetid);
        } else {
            // The preset has been applied. Check the settings and plugins are the expected.
            $this->assertNotEmpty($presetid);

            // Check the setting values have changed accordingly with the ones defined in the preset.
            foreach ($settings as $settingname => $settingvalue) {
                $this->assertEquals($settingvalue, get_config('core', $settingname));
            }

            // Check the plugins visibility have changed accordingly with the ones defined in the preset.
            $enabledplugins = \core\plugininfo\mod::get_enabled_plugins();
            foreach ($plugins as $pluginname => $pluginvalue) {
                if ($pluginvalue) {
                    $this->assertArrayHasKey($pluginname, $enabledplugins);
                } else {
                    $this->assertArrayNotHasKey($pluginname, $enabledplugins);
                }
            }
        }
    }

    /**
     * Data provider for test_change_default_preset().
     *
     * @return array
     */
    public static function change_default_preset_provider(): array {
        return [
            'Starter preset' => [
                'preset' => 'starter',
                'settings' => [
                    'enablebadges' => 0,
                    'enableportfolios' => 0,
                ],
                'plugins' => [
                    'assign' => 1,
                    'chat' => 0,
                    'data' => 0,
                    'lesson' => 0,
                ],
            ],
            'Full preset' => [
                'preset' => 'full',
                'settings' => [
                    'enablebadges' => 1,
                    'enableportfolios' => 0,
                ],
                'plugins' => [
                    'assign' => 1,
                    'book' => 1,
                    'data' => 1,
                    'lesson' => 1,
                ],
            ],
            'Preset 1, created manually' => [
                'preset' => 'Preset 1',
                'settings' => [
                    'enablebadges' => 0,
                    'allowemojipicker' => 1,
                ],
                'plugins' => [
                    'assign' => 1,
                    'glossary' => 0,
                ],
            ],
            'Unexisting preset name' => [
                'preset' => 'unexisting',
            ],
            'Valid XML file' => [
                'preset' => self::get_fixture_path(__NAMESPACE__, 'import_settings_plugins.xml'),
                'settings' => [
                    'allowemojipicker' => 1,
                    'enableportfolios' => 1,
                ],
                'plugins' => [
                    'assign' => 1,
                    'chat' => 0,
                    'data' => 0,
                    'lesson' => 1,
                ],
            ],
            'Invalid XML file' => [
                'preset' => self::get_fixture_path(__NAMESPACE__, 'invalid_xml_file.xml'),
            ],
            'Unexisting XML file' => [
                'preset' => __DIR__ . '/fixtures/unexisting.xml',
            ],
        ];
    }
}
