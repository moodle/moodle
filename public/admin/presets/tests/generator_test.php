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
 * Tests for the data generator.
 *
 * @package    core_adminpresets
 * @category   test
 * @copyright  2021 Sara Arjona (sara@moodle.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass core_adminpresets_generator
 */
final class generator_test extends \advanced_testcase {

    /**
     * Test the behaviour of create_preset() method.
     *
     * @covers ::create_preset
     * @dataProvider create_preset_provider
     *
     * @param string|null $name Preset name field.
     * @param string|null $comments Preset comments field.
     * @param string|null $author Preset author field.
     * @param bool $applypreset Whether the preset should be applied or not.
     * @param int|null $iscore Whether the preset is a core preset or not.
     * @param int|null $iscoreresult Expected iscore value for the result preset.
     */
    public function test_create_preset(?string $name = null, ?string $comments = null, ?string $author = null,
            bool $applypreset = false, ?int $iscore = null, ?int $iscoreresult = null): void {
        global $CFG, $DB;

        $this->resetAfterTest();

        $data = [];
        if (isset($name)) {
            $data['name'] = $name;
        } else {
            // Set the default value used in the generator.
            $name = 'Preset default name';
        }
        if (isset($comments)) {
            // Set the default value used in the generator.
            $data['comments'] = $comments;
        } else {
            // Set the default value used in the generator.
            $comments = 'Preset default comment';
        }
        if (isset($author)) {
            $data['author'] = $author;
        } else {
            $author = 'Default author';
        }
        if ($applypreset) {
            $data['applypreset'] = $applypreset;
        }
        if (isset($iscore)) {
            $data['iscore'] = $iscore;
        }
        if (!isset($iscoreresult)) {
            $iscoreresult = manager::NONCORE_PRESET;
        }

        // Create a preset.
        $presetid = $this->getDataGenerator()->get_plugin_generator('core_adminpresets')->create_preset($data);

        // Check the preset data.
        $preset = $DB->get_record('adminpresets', ['id' => $presetid]);

        $this->assertEquals($name, $preset->name);
        $this->assertEquals($comments, $preset->comments);
        $this->assertEquals($author, $preset->author);
        $this->assertEquals($iscoreresult, $preset->iscore);
        $this->assertEquals($CFG->version, $preset->moodleversion);
        $this->assertEquals($CFG->release, $preset->moodlerelease);
        $this->assertEquals($CFG->wwwroot, $preset->site);

        // Check the settings.
        $settings = $DB->get_records('adminpresets_it', ['adminpresetid' => $presetid]);
        $this->assertCount(4, $settings);
        // These are the settings created in the generator. Check the results match them.
        $expectedsettings = [
            'enablebadges' => 0,
            'allowemojipicker' => 1,
            'mediawidth' => 900,
            'maxanswers' => 2,
        ];
        foreach ($settings as $setting) {
            $this->assertArrayHasKey($setting->name, $expectedsettings);
            $this->assertEquals($expectedsettings[$setting->name], $setting->value);
        }

        // Check the advanced settings (should be only one).
        $settingsid = array_keys($settings);
        list($insql, $inparams) = $DB->get_in_or_equal($settingsid);
        $advsettings = $DB->get_records_select('adminpresets_it_a', 'itemid ' . $insql, $inparams);
        $this->assertCount(1, $advsettings);
        $advsetting = reset($advsettings);
        $this->assertEquals('maxanswers_adv', $advsetting->name);
        $this->assertEquals(0, $advsetting->value);

        // Check the plugins.
        $plugins = $DB->get_records('adminpresets_plug', ['adminpresetid' => $presetid]);
        $this->assertCount(3, $plugins);
        // These are the plugins created in the generator. Check the results match them.
        $expectedplugins = [
            'enrol' => [
                'guest' => 0,
            ],
            'mod' => [
                'glossary' => 0,
            ],
            'qtype' => [
                'truefalse' => 1,
            ],
        ];
        foreach ($plugins as $plugin) {
            $this->assertArrayHasKey($plugin->plugin, $expectedplugins);
            $this->assertArrayHasKey($plugin->name, $expectedplugins[$plugin->plugin]);
            $this->assertEquals($expectedplugins[$plugin->plugin][$plugin->name], $plugin->enabled);
        }

        if ($applypreset) {
            // Verify that the preset has been applied.
            $apps = $DB->get_records('adminpresets_app', ['adminpresetid' => $presetid]);
            $this->assertCount(1, $apps);
            $app = reset($apps);

            // Check the applied settings.
            $appsettings = $DB->get_records('adminpresets_app_it', ['adminpresetapplyid' => $app->id]);
            $this->assertCount(3, $appsettings);
            // These are the settings created in the generator (all but the allowemojipicker because it hasn't changed).
            $expectedappsettings = $expectedsettings;
            unset($expectedappsettings['allowemojipicker']);
            // Check the results match the expected settings applied.
            foreach ($appsettings as $appsetting) {
                $configlog = $DB->get_record('config_log', ['id' => $appsetting->configlogid]);
                $this->assertArrayHasKey($configlog->name, $expectedappsettings);
                $this->assertEquals($expectedappsettings[$configlog->name], $configlog->value);
            }

            $appsettings = $DB->get_records('adminpresets_app_it_a', ['adminpresetapplyid' => $app->id]);
            $this->assertCount(1, $appsettings);
            $appsetting = reset($appsettings);
            $configlog = $DB->get_record('config_log', ['id' => $appsetting->configlogid]);
            $this->assertEquals('maxanswers_adv', $configlog->name);
            $this->assertEquals(0, $configlog->value);

            // Check the applied plugins.
            $appplugins = $DB->get_records('adminpresets_app_plug', ['adminpresetapplyid' => $app->id]);
            $this->assertCount(2, $appplugins);
            // These are the plugins created in the generator (all but the qtype_truefalse because it hasn't changed).
            $expectedappplugins = $expectedplugins;
            unset($expectedappplugins['qtype']);
            // Check the results match the expected plugins applied.
            foreach ($appplugins as $appplugin) {
                $this->assertArrayHasKey($appplugin->plugin, $expectedappplugins);
                $this->assertArrayHasKey($appplugin->name, $expectedappplugins[$appplugin->plugin]);
                $this->assertEquals($expectedappplugins[$appplugin->plugin][$appplugin->name], $appplugin->value);
            }
        }
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
            'Comment not empty' => [
                'name' => null,
                'comments' => 'This is a different comment',
            ],
            'Author not empty' => [
                'name' => null,
                'comments' => null,
                'author' => 'Ada Lovelace',
            ],
            'No default values for all the fields' => [
                'name' => 'Preset with a super-nice name',
                'comments' => 'This is a comment different from the previous one',
                'author' => 'Alejandro Sanz',
            ],
            'Apply preset' => [
                'name' => null,
                'comments' => null,
                'author' => null,
                'applypreset' => true,
            ],
            'Starter preset' => [
                'name' => 'Starter',
                'comments' => null,
                'author' => null,
                'applypreset' => false,
                'iscore' => manager::STARTER_PRESET,
                'iscoreresult' => manager::STARTER_PRESET,
            ],
            'Full preset' => [
                'name' => 'Full',
                'comments' => null,
                'author' => null,
                'applypreset' => false,
                'iscore' => manager::FULL_PRESET,
                'iscoreresult' => manager::FULL_PRESET,
            ],
            'Invalid iscore' => [
                'name' => 'Invalid iscore value',
                'comments' => null,
                'author' => null,
                'applypreset' => false,
                'iscore' => -1,
                'iscoreresult' => manager::NONCORE_PRESET,
            ],
        ];
    }
}
