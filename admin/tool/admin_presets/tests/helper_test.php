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

namespace tool_admin_presets;

/**
 * Tests for the helper class.
 *
 * @package    tool_admin_presets
 * @category   test
 * @copyright  2021 Sara Arjona (sara@moodle.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass helper
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
     */
    public function test_create_preset(?string $name = null, ?string $comments = null): void {
        global $CFG, $DB, $USER;

        $this->resetAfterTest();

        $data = [];
        if (isset($name)) {
            $data['name'] = $name;
        }
        if (isset($comments)) {
            $data['comments'] = $comments;
        }

        // Create a preset.
        $presetid = helper::create_preset($data);

        // Check the preset data.
        $preset = $DB->get_record('tool_admin_presets', ['id' => $presetid]);

        $this->assertEquals($name, $preset->name);
        $this->assertEquals($comments, $preset->comments);
        $this->assertEquals(fullname($USER), $preset->author);
        $this->assertEquals($CFG->version, $preset->moodleversion);
        $this->assertEquals($CFG->release, $preset->moodlerelease);
        $this->assertEquals($CFG->wwwroot, $preset->site);

        // Check the preset is empty and hasn't settings or plugins.
        $settings = $DB->get_records('tool_admin_presets_it', ['adminpresetid' => $presetid]);
        $this->assertCount(0, $settings);
        $plugins = $DB->get_records('tool_admin_presets_plug', ['adminpresetid' => $presetid]);
        $this->assertCount(0, $plugins);
    }

    /**
     * Data provider for test_create_preset().
     *
     * @return array
     */
    public function create_preset_provider(): array {
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
        $this->assertEquals(1, $DB->count_records('tool_admin_presets', ['id' => $presetid]));

        // Add items.
        $itemid = helper::add_item($presetid, $name, $value, $plugin, $advname, $advvalue);

        // Check settings have been created.
        $settings = $DB->get_records('tool_admin_presets_it', ['adminpresetid' => $presetid]);
        $this->assertCount(1, $settings);

        $setting = reset($settings);
        $this->assertEquals($itemid, $setting->id);
        $this->assertEquals($name, $setting->name);
        $this->assertEquals($value, $setting->value);
        $this->assertEquals($plugin, $setting->plugin);

        if ($advname) {
            // Check settings have been created.
            $advsettings = $DB->get_records('tool_admin_presets_it_a', ['itemid' => $itemid]);
            $this->assertCount(1, $advsettings);

            $advsetting = reset($advsettings);
            $this->assertEquals($advname, $advsetting->name);
            $this->assertEquals($advvalue, $advsetting->value);
        } else {
            // Check no advanced items have been created.
            $this->assertEquals(0, $DB->count_records('tool_admin_presets_it_a', ['itemid' => $itemid]));
        }

        // Check no plugins have been created.
        $this->assertEquals(0, $DB->count_records('tool_admin_presets_plug', ['adminpresetid' => $presetid]));
    }

    /**
     * Data provider for test_add_item().
     *
     * @return array
     */
    public function add_item_provider(): array {
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
        $this->assertEquals(1, $DB->count_records('tool_admin_presets', ['id' => $presetid]));

        // Add plugin.
        $pluginid = helper::add_plugin($presetid, $type, $name, $enabled);

        // Check plugin has been created.
        $pluggins = $DB->get_records('tool_admin_presets_plug', ['adminpresetid' => $presetid]);
        $this->assertCount(1, $pluggins);

        $plugin = reset($pluggins);
        $this->assertEquals($pluginid, $plugin->id);
        $this->assertEquals($type, $plugin->plugin);
        $this->assertEquals($name, $plugin->name);
        $this->assertEquals((int) $enabled, $plugin->enabled);

        // Check no settings have been created.
        $this->assertEquals(0, $DB->count_records('tool_admin_presets_it', ['adminpresetid' => $presetid]));
    }

    /**
     * Data provider for test_add_plugin().
     *
     * @return array
     */
    public function add_plugin_provider(): array {
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
}
