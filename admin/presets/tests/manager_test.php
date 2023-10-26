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

use stdClass;

/**
 * Tests for the manager class.
 *
 * @package    core_adminpresets
 * @category   test
 * @copyright  2021 Sara Arjona (sara@moodle.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \core_adminpresets\manager
 */
class manager_test extends \advanced_testcase {
    /**
     * Test the behaviour of protected get_site_settings method.
     *
     * @covers ::get_site_settings
     * @covers ::get_settings
     */
    public function test_manager_get_site_settings(): void {
        global $DB;

        $this->resetAfterTest();

        // Login as admin, to access all the settings.
        $this->setAdminUser();

        $manager = new manager();
        $result = $manager->get_site_settings();

        // Check fullname is set into the none category.
        $this->assertInstanceOf(
                '\core_adminpresets\local\setting\adminpresets_admin_setting_sitesettext',
                $result['none']['fullname']
        );
        $this->assertEquals('PHPUnit test site', $result['none']['fullname']->get_value());

        // Check some of the config setting is present (they should be stored in the "none" category).
        $this->assertInstanceOf(
                '\core_adminpresets\local\setting\adminpresets_admin_setting_configcheckbox',
                $result['none']['enablecompletion']
        );
        $this->assertEquals(1, $result['none']['enablecompletion']->get_value());

        // Check some of the plugin config settings is present.
        $this->assertInstanceOf(
                '\core_adminpresets\local\setting\adminpresets_admin_setting_configtext',
                $result['folder']['maxsizetodownload']
        );
        $this->assertEquals(0, $result['folder']['maxsizetodownload']->get_value());

        // Set some of these values.
        $sitecourse = new stdClass();
        $sitecourse->id = 1;
        $sitecourse->fullname = 'New site fullname';
        $DB->update_record('course', $sitecourse);

        set_config('enablecompletion', 0);
        set_config('maxsizetodownload', 101, 'folder');

        // Check the new values are returned properly.
        $result = $manager->get_site_settings();
        // Site fullname.
        $this->assertInstanceOf(
                '\core_adminpresets\local\setting\adminpresets_admin_setting_sitesettext',
                $result['none']['fullname']
        );
        $this->assertEquals($sitecourse->fullname, $result['none']['fullname']->get_value());
        // Config setting.
        $this->assertInstanceOf(
                '\core_adminpresets\local\setting\adminpresets_admin_setting_configcheckbox',
                $result['none']['enablecompletion']
        );
        $this->assertEquals(0, $result['none']['enablecompletion']->get_value());
        // Plugin config settting.
        $this->assertInstanceOf(
                '\core_adminpresets\local\setting\adminpresets_admin_setting_configtext',
                $result['folder']['maxsizetodownload']
        );
        $this->assertEquals(101, $result['folder']['maxsizetodownload']->get_value());
    }

    /**
     * Test the behaviour of protected get_setting method.
     *
     * @covers ::get_setting
     * @covers ::get_settings_class
     */
    public function test_manager_get_setting(): void {
        $this->resetAfterTest();

        // Login as admin, to access all the settings.
        $this->setAdminUser();

        $adminroot = admin_get_root();

        // Check the adminpresets_xxxxx class is created properly when it exists.
        $settingpage = $adminroot->locate('optionalsubsystems');
        $settingdata = $settingpage->settings->enablebadges;
        $manager = new manager();
        $result = $manager->get_setting($settingdata, '');
        $this->assertInstanceOf('\core_adminpresets\local\setting\adminpresets_admin_setting_configcheckbox', $result);
        $this->assertNotEquals('core_adminpresets\local\setting\adminpresets_setting', get_class($result));

        // Check the mapped class is returned when no specific class exists and it exists in the mappings array.
        $settingpage = $adminroot->locate('h5psettings');
        $settingdata = $settingpage->settings->h5plibraryhandler;;
        $result = $manager->get_setting($settingdata, '');
        $this->assertInstanceOf('\core_adminpresets\local\setting\adminpresets_admin_setting_configselect', $result);
        $this->assertNotEquals(
                'core_adminpresets\local\setting\adminpresets_admin_settings_h5plib_handler_select',
                get_class($result)
        );

        // Check the mapped class is returned when no specific class exists and it exists in the mappings array.
        $settingpage = $adminroot->locate('modsettingquiz');
        $settingdata = $settingpage->settings->quizbrowsersecurity;;
        $result = $manager->get_setting($settingdata, '');
        $this->assertInstanceOf('\mod_quiz\adminpresets\adminpresets_mod_quiz_admin_setting_browsersecurity', $result);
        $this->assertNotEquals('core_adminpresets\local\setting\adminpresets_setting', get_class($result));

        // Check the adminpresets_setting class is returned when no specific class exists.
        $settingpage = $adminroot->locate('managecustomfields');
        $settingdata = $settingpage->settings->customfieldsui;;
        $result = $manager->get_setting($settingdata, '');
        $this->assertInstanceOf('\core_adminpresets\local\setting\adminpresets_setting', $result);
        $this->assertEquals('core_adminpresets\local\setting\adminpresets_setting', get_class($result));
    }

    /**
     * Test the behaviour of apply_preset() method when the given presetid doesn't exist.
     *
     * @covers ::apply_preset
     */
    public function test_apply_preset_unexisting_preset(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        // Create some presets.
        $generator = $this->getDataGenerator()->get_plugin_generator('core_adminpresets');
        $presetid = $generator->create_preset();

        // Unexisting preset identifier.
        $unexistingid = $presetid * 2;

        $manager = new manager();
        $this->expectException(\moodle_exception::class);
        $manager->apply_preset($unexistingid);
    }

    /**
     * Test the behaviour of apply_preset() method.
     *
     * @covers ::apply_preset
     */
    public function test_apply_preset(): void {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        // Create a preset.
        $generator = $this->getDataGenerator()->get_plugin_generator('core_adminpresets');
        $presetid = $generator->create_preset();

        $currentpresets = $DB->count_records('adminpresets');
        $currentitems = $DB->count_records('adminpresets_it');
        $currentadvitems = $DB->count_records('adminpresets_it_a');
        $currentplugins = $DB->count_records('adminpresets_plug');
        $currentapppresets = $DB->count_records('adminpresets_app');
        $currentappitems = $DB->count_records('adminpresets_app_it');
        $currentappadvitems = $DB->count_records('adminpresets_app_it_a');
        $currentappplugins = $DB->count_records('adminpresets_app_plug');

        // Set the config values (to confirm they change after applying the preset).
        set_config('enablebadges', 1);
        set_config('allowemojipicker', 1);
        set_config('mediawidth', '640', 'mod_lesson');
        set_config('maxanswers', '5', 'mod_lesson');
        set_config('maxanswers_adv', '1', 'mod_lesson');
        set_config('enablecompletion', 1);
        set_config('usecomments', 0);

        // Call the apply_preset method.
        $manager = new manager();
        $manager->apply_preset($presetid);

        // Check the preset applied has been added to database.
        $this->assertCount($currentapppresets + 1, $DB->get_records('adminpresets_app'));
        // Applied items: enablebadges@none, mediawitdh@mod_lesson and maxanswers@@mod_lesson.
        $this->assertCount($currentappitems + 3, $DB->get_records('adminpresets_app_it'));
        // Applied advanced items: maxanswers_adv@mod_lesson.
        $this->assertCount($currentappadvitems + 1, $DB->get_records('adminpresets_app_it_a'));
        // Applied plugins: enrol_guest and mod_glossary.
        $this->assertCount($currentappplugins + 2, $DB->get_records('adminpresets_app_plug'));
        // Check no new preset has been created.
        $this->assertCount($currentpresets, $DB->get_records('adminpresets'));
        $this->assertCount($currentitems, $DB->get_records('adminpresets_it'));
        $this->assertCount($currentadvitems, $DB->get_records('adminpresets_it_a'));
        $this->assertCount($currentplugins, $DB->get_records('adminpresets_plug'));

        // Check the setting values have changed accordingly with the ones defined in the preset.
        $this->assertEquals(0, get_config('core', 'enablebadges'));
        $this->assertEquals(900, get_config('mod_lesson', 'mediawidth'));
        $this->assertEquals(2, get_config('mod_lesson', 'maxanswers'));
        $this->assertEquals(0, get_config('mod_lesson', 'maxanswers_adv'));

        // These settings will never change.
        $this->assertEquals(1, get_config('core', 'allowemojipicker'));
        $this->assertEquals(1, get_config('core', 'enablecompletion'));
        $this->assertEquals(0, get_config('core', 'usecomments'));

        // Check the plugins visibility have changed accordingly with the ones defined in the preset.
        $enabledplugins = \core\plugininfo\enrol::get_enabled_plugins();
        $this->assertArrayNotHasKey('guest', $enabledplugins);
        $this->assertArrayHasKey('manual', $enabledplugins);
        $enabledplugins = \core\plugininfo\mod::get_enabled_plugins();
        $this->assertArrayNotHasKey('glossary', $enabledplugins);
        $this->assertArrayHasKey('assign', $enabledplugins);
        $enabledplugins = \core\plugininfo\qtype::get_enabled_plugins();
        $this->assertArrayHasKey('truefalse', $enabledplugins);

        // Check the presetid has been also stored in the lastpresetapplied config setting.
        $this->assertEquals($presetid, get_config('adminpresets', 'lastpresetapplied'));

        // Call apply_preset as a simulation, so it shouldn't be applied and lastpresetapplied should still be $presetid.
        $presetid2 = $generator->create_preset();
        $manager->apply_preset($presetid2, true);
        $this->assertEquals($presetid, get_config('adminpresets', 'lastpresetapplied'));
    }


    /**
     * Test the behaviour of export_preset() method.
     *
     * @covers ::export_preset
     * @dataProvider export_preset_provider
     *
     * @param bool $includesensible Whether the sensible settings should be exported too or not.
     * @param string $presetname Preset name.
     */
    public function test_export_preset(bool $includesensible = false, string $presetname = 'Export 1'): void {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        // Get current presets and items.
        $currentpresets = $DB->count_records('adminpresets');
        $currentadvitems = $DB->count_records('adminpresets_it_a');

        // Initialise some settings (to compare their values have been exported as expected).
        set_config('recaptchapublickey', 'abcde');
        set_config('enablebadges', '0');
        set_config('mediawidth', '900', 'mod_lesson');
        set_config('maxanswers', '2', 'mod_lesson');
        set_config('maxanswers_adv', '0', 'mod_lesson');
        set_config('defaultfeedback', '0', 'mod_lesson');
        set_config('defaultfeedback_adv', '1', 'mod_lesson');

        // Prepare the data to export preset.
        $data = [
            'name' => $presetname,
            'comments' => ['text' => 'This is a presets for testing export'],
            'author' => 'Super-Girl',
            'includesensiblesettings' => $includesensible,
        ];

        // Call the method to be tested.
        $manager = new manager();
        list($presetid, $settingsfound, $pluginsfound) = $manager->export_preset((object) $data);

        // Check the preset record has been created.
        $presets = $DB->get_records('adminpresets');
        $this->assertCount($currentpresets + 1, $presets);
        $this->assertArrayHasKey($presetid, $presets);
        $preset = $presets[$presetid];
        $this->assertEquals($presetname, $preset->name);
        $this->assertEquals(manager::NONCORE_PRESET, $preset->iscore);

        // Check the preset includes settings and plugins.
        $this->assertTrue($settingsfound);
        $this->assertTrue($pluginsfound);

        // Check the items, advanced attributes and plugins have been created.
        $this->assertGreaterThan(0, $DB->count_records('adminpresets_it', ['adminpresetid' => $presetid]));
        $this->assertGreaterThan($currentadvitems, $DB->count_records('adminpresets_it_a'));
        $this->assertGreaterThan(0, $DB->count_records('adminpresets_plug', ['adminpresetid' => $presetid]));

        // Check settings have been created with the expected values.
        $params = ['adminpresetid' => $presetid, 'plugin' => 'none', 'name' => 'enablebadges'];
        $setting = $DB->get_record('adminpresets_it', $params);
        $this->assertEquals('0', $setting->value);

        $params = ['adminpresetid' => $presetid, 'plugin' => 'mod_lesson', 'name' => 'mediawidth'];
        $setting = $DB->get_record('adminpresets_it', $params);
        $this->assertEquals('900', $setting->value);

        $params = ['adminpresetid' => $presetid, 'plugin' => 'mod_lesson', 'name' => 'maxanswers'];
        $setting = $DB->get_record('adminpresets_it', $params);
        $this->assertEquals('2', $setting->value);
        $params = ['itemid' => $setting->id, 'name' => 'maxanswers_adv'];
        $setting = $DB->get_record('adminpresets_it_a', $params);
        $this->assertEquals('0', $setting->value);

        $params = ['adminpresetid' => $presetid, 'plugin' => 'mod_lesson', 'name' => 'defaultfeedback'];
        $setting = $DB->get_record('adminpresets_it', $params);
        $this->assertEquals('0', $setting->value);
        $params = ['itemid' => $setting->id, 'name' => 'defaultfeedback_adv'];
        $setting = $DB->get_record('adminpresets_it_a', $params);
        $this->assertEquals('1', $setting->value);

        // Check plugins have been created with the expected values.
        $manager = \core_plugin_manager::instance();
        $plugintype = 'enrol';
        $plugins = $manager->get_present_plugins($plugintype);
        $enabledplugins = $manager->get_enabled_plugins($plugintype);
        foreach ($plugins as $pluginname => $unused) {
            $params = ['adminpresetid' => $presetid, 'plugin' => $plugintype, 'name' => $pluginname];
            $plugin = $DB->get_record('adminpresets_plug', $params);
            $enabled = (!empty($enabledplugins) && array_key_exists($pluginname, $enabledplugins));
            $this->assertEquals($enabled, (bool) $plugin->enabled);
        }

        // Check whether sensible settings have been exported or not.
        $params = ['adminpresetid' => $presetid, 'plugin' => 'none', 'name' => 'recaptchapublickey'];
        $recaptchasetting = $DB->get_record('adminpresets_it', $params);
        $params = ['adminpresetid' => $presetid, 'plugin' => 'none', 'name' => 'cronremotepassword'];
        $cronsetting = $DB->get_record('adminpresets_it', $params);
        if ($includesensible) {
            $this->assertEquals('abcde', $recaptchasetting->value);
            $this->assertNotFalse($cronsetting);
        } else {
            $this->assertFalse($recaptchasetting);
            $this->assertFalse($cronsetting);
        }
    }

    /**
     * Data provider for test_export_preset().
     *
     * @return array
     */
    public function export_preset_provider(): array {
        return [
            'Export settings and plugins, excluding sensible' => [
                'includesensible' => false,
            ],
            'Export settings and plugins, including sensible' => [
                'includesensible' => true,
            ],
            'Export settings and plugins, with Starter name (it should not be marked as core)' => [
                'includesensible' => false,
                'presetname' => 'Starter',
            ],
            'Export settings and plugins, with Full name (it should not be marked as core)' => [
                'includesensible' => false,
                'presetname' => 'Full',
            ],
        ];
    }

    /**
     * Test the behaviour of download_preset() method, when the given presetid doesn't exist.
     *
     * @covers ::download_preset
     */
    public function test_download_unexisting_preset(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        // Create some presets.
        $generator = $this->getDataGenerator()->get_plugin_generator('core_adminpresets');
        $presetid = $generator->create_preset();

        // Unexisting preset identifier.
        $unexistingid = $presetid * 2;

        $manager = new manager();
        $this->expectException(\moodle_exception::class);
        $manager->download_preset($unexistingid);
    }


    /**
     * Test the behaviour of import_preset() method.
     *
     * @dataProvider import_preset_provider
     * @covers ::import_preset
     *
     * @param string $filecontents File content to import.
     * @param bool $expectedpreset Whether the preset should be created or not.
     * @param bool $expectedsettings Whether settings will be created or not.
     * @param bool $expectedplugins Whether plugins will be created or not.
     * @param bool $expecteddebugging Whether debugging message will be thrown or not.
     * @param string|null $expectedexception Expected exception class (if that's the case).
     * @param string|null $expectedpresetname Expected preset name.
     */
    public function test_import_preset(string $filecontents, bool $expectedpreset, bool $expectedsettings = false,
            bool $expectedplugins = false, bool $expecteddebugging = false, string $expectedexception = null,
            string $expectedpresetname = 'Imported preset'): void {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        $currentpresets = $DB->count_records('adminpresets');
        $currentitems = $DB->count_records('adminpresets_it');
        $currentadvitems = $DB->count_records('adminpresets_it_a');

        // Call the method to be tested.
        $manager = new manager();
        try {
            list($xml, $preset, $settingsfound, $pluginsfound) = $manager->import_preset($filecontents);
        } catch (\exception $e) {
            if ($expectedexception) {
                $this->assertInstanceOf($expectedexception, $e);
            }
        } finally {
            if ($expecteddebugging) {
                $this->assertDebuggingCalled();
            }

            if ($expectedpreset) {
                // Check the preset record has been created.
                $presets = $DB->get_records('adminpresets');
                $this->assertCount($currentpresets + 1, $presets);
                $generator = $this->getDataGenerator()->get_plugin_generator('core_adminpresets');
                $this->assertArrayHasKey($preset->id, $presets);
                $preset = $presets[$preset->id];
                $this->assertEquals($expectedpresetname, $preset->name);
                $this->assertEquals('http://demo.moodle', $preset->site);
                $this->assertEquals('Ada Lovelace', $preset->author);
                $this->assertEquals(manager::NONCORE_PRESET, $preset->iscore);

                if ($expectedsettings) {
                    // Check the items have been created.
                    $items = $DB->get_records('adminpresets_it', ['adminpresetid' => $preset->id]);
                    $this->assertCount(4, $items);
                    $presetitems = [
                        'none' => [
                            'enablebadges' => 0,
                            'enableportfolios' => 1,
                            'allowemojipicker' => 1,
                        ],
                        'mod_lesson' => [
                            'mediawidth' => 900,
                            'maxanswers' => 2,
                        ],
                    ];
                    foreach ($items as $item) {
                        $this->assertArrayHasKey($item->name, $presetitems[$item->plugin]);
                        $this->assertEquals($presetitems[$item->plugin][$item->name], $item->value);
                    }

                    // Check the advanced attributes have been created.
                    $advitems = $DB->get_records('adminpresets_it_a');
                    $this->assertCount($currentadvitems + 1, $advitems);
                    $advitemfound = false;
                    foreach ($advitems as $advitem) {
                        if ($advitem->name == 'maxanswers_adv') {
                            $this->assertEmpty($advitem->value);
                            $advitemfound = true;
                        }
                    }
                    $this->assertTrue($advitemfound);
                }

                if ($expectedplugins) {
                    // Check the plugins have been created.
                    $plugins = $DB->get_records('adminpresets_plug', ['adminpresetid' => $preset->id]);
                    $this->assertCount(6, $plugins);
                    $presetplugins = [
                        'atto' => [
                            'html' => 1,
                        ],
                        'block' => [
                            'html' => 0,
                            'activity_modules' => 1,
                        ],
                        'mod' => [
                            'chat' => 0,
                            'data' => 0,
                            'lesson' => 1,
                        ],
                    ];
                    foreach ($plugins as $plugin) {
                        $this->assertArrayHasKey($plugin->name, $presetplugins[$plugin->plugin]);
                        $this->assertEquals($presetplugins[$plugin->plugin][$plugin->name], $plugin->enabled);
                    }

                }
            } else {
                // Check the preset nor the items are not created.
                $this->assertCount($currentpresets, $DB->get_records('adminpresets'));
                $this->assertCount($currentitems, $DB->get_records('adminpresets_it'));
                $this->assertCount($currentadvitems, $DB->get_records('adminpresets_it_a'));
            }
        }
    }

    /**
     * Data provider for test_import_preset().
     *
     * @return array
     */
    public function import_preset_provider(): array {
        return [
            'Import settings from an empty file' => [
                'filecontents' => '',
                'expectedpreset' => false,
            ],
            'Import settings and plugins from a valid XML file' => [
                'filecontents' => file_get_contents(__DIR__ . '/fixtures/import_settings_plugins.xml'),
                'expectedpreset' => true,
                'expectedsettings' => true,
                'expectedplugins' => true,
            ],
            'Import only settings from a valid XML file' => [
                'filecontents' => file_get_contents(__DIR__ . '/fixtures/import_settings.xml'),
                'expectedpreset' => true,
                'expectedsettings' => true,
                'expectedplugins' => false,
            ],
            'Import settings and plugins from a valid XML file with Starter name, which will be marked as non-core' => [
                'filecontents' => file_get_contents(__DIR__ . '/fixtures/import_starter_name.xml'),
                'expectedpreset' => true,
                'expectedsettings' => true,
                'expectedplugins' => true,
                'expecteddebugging' => false,
                'expectedexception' => null,
                'expectedpresetname' => 'Starter',
            ],
            'Import settings from an invalid XML file' => [
                'filecontents' => file_get_contents(__DIR__ . '/fixtures/invalid_xml_file.xml'),
                'expectedpreset' => false,
                'expectedsettings' => false,
                'expectedplugins' => false,
                'expecteddebugging' => false,
                'expectedexception' => \Exception::class,
            ],
            'Import unexisting settings category' => [
                'filecontents' => file_get_contents(__DIR__ . '/fixtures/unexisting_category.xml'),
                'expectedpreset' => false,
                'expectedsettings' => false,
                'expectedplugins' => false,
            ],
            'Import unexisting setting' => [
                'filecontents' => file_get_contents(__DIR__ . '/fixtures/unexisting_setting.xml'),
                'expectedpreset' => false,
                'expectedsettings' => false,
                'expectedplugins' => false,
                'expecteddebugging' => true,
            ],
            'Import valid settings with one unexisting setting too' => [
                'filecontents' => file_get_contents(__DIR__ . '/fixtures/import_settings_with_unexisting_setting.xml'),
                'expectedpreset' => true,
                'expectedsettings' => false,
                'expectedplugins' => false,
                'expecteddebugging' => true,
            ],
        ];
    }


    /**
     * Test the behaviour of delete_preset() method when the preset id doesn't exist.
     *
     * @covers ::delete_preset
     */
    public function test_delete_preset_unexisting_preset(): void {

        $this->resetAfterTest();
        $this->setAdminUser();

        // Create some presets.
        $generator = $this->getDataGenerator()->get_plugin_generator('core_adminpresets');
        $presetid = $generator->create_preset(['name' => 'Preset 1']);

        // Unexisting preset identifier.
        $unexistingid = $presetid * 2;

        $manager = new manager();

        $this->expectException(\moodle_exception::class);
        $manager->delete_preset($unexistingid);
    }

    /**
     * Test the behaviour of delete_preset() method.
     *
     * @covers ::delete_preset
     */
    public function test_delete_preset(): void {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        // Create some presets.
        $generator = $this->getDataGenerator()->get_plugin_generator('core_adminpresets');
        $presetid1 = $generator->create_preset(['name' => 'Preset 1', 'applypreset' => true]);
        $presetid2 = $generator->create_preset(['name' => 'Preset 2']);

        $currentpresets = $DB->count_records('adminpresets');
        $currentitems = $DB->count_records('adminpresets_it');
        $currentadvitems = $DB->count_records('adminpresets_it_a');
        $currentplugins = $DB->count_records('adminpresets_plug');

        // Only preset1 has been applied.
        $this->assertCount(1, $DB->get_records('adminpresets_app'));
        // Only the preset1 settings that have changed: enablebadges, mediawidth and maxanswers.
        $this->assertCount(3, $DB->get_records('adminpresets_app_it'));
        // Only the preset1 advanced settings that have changed: maxanswers_adv.
        $this->assertCount(1, $DB->get_records('adminpresets_app_it_a'));
        // Only the preset1 plugins that have changed: enrol_guest and mod_glossary.
        $this->assertCount(2, $DB->get_records('adminpresets_app_plug'));

        // Call the method to be tested.
        $manager = new manager();
        $manager->delete_preset($presetid1);

        // Check the preset data has been removed.
        $presets = $DB->get_records('adminpresets');
        $this->assertCount($currentpresets - 1, $presets);
        $preset = reset($presets);
        $this->assertArrayHasKey($presetid2, $presets);
        // Check preset items.
        $this->assertCount($currentitems - 4, $DB->get_records('adminpresets_it'));
        $this->assertCount(0, $DB->get_records('adminpresets_it', ['adminpresetid' => $presetid1]));
        // Check preset advanced items.
        $this->assertCount($currentadvitems - 1, $DB->get_records('adminpresets_it_a'));
        // Check preset plugins.
        $this->assertCount($currentplugins - 3, $DB->get_records('adminpresets_plug'));
        $this->assertCount(0, $DB->get_records('adminpresets_plug', ['adminpresetid' => $presetid1]));
        // Check preset applied tables are empty now.
        $this->assertCount(0, $DB->get_records('adminpresets_app'));
        $this->assertCount(0, $DB->get_records('adminpresets_app_it'));
        $this->assertCount(0, $DB->get_records('adminpresets_app_it_a'));
        $this->assertCount(0, $DB->get_records('adminpresets_app_plug'));
    }

    /**
     * Test the behaviour of revert_preset() method when the preset applied id doesn't exist.
     *
     * @covers ::revert_preset
     */
    public function test_revert_preset_unexisting_presetapp(): void {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        // Create a preset and apply it.
        $generator = $this->getDataGenerator()->get_plugin_generator('core_adminpresets');
        $presetid = $generator->create_preset(['applypreset' => true]);
        $presetappid = $DB->get_field('adminpresets_app', 'id', ['adminpresetid' => $presetid]);

        // Unexisting applied preset identifier.
        $unexistingid = $presetappid * 2;

        $manager = new manager();
        $this->expectException(\moodle_exception::class);
        $manager->revert_preset($unexistingid);
    }

    /**
     * Test the behaviour of revert_preset() method.
     *
     * @covers ::revert_preset
     */
    public function test_revert_preset(): void {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        // Set the config values (to confirm they change after applying the preset).
        set_config('enablebadges', 1);
        set_config('allowemojipicker', 1);
        set_config('mediawidth', '640', 'mod_lesson');
        set_config('maxanswers', '5', 'mod_lesson');
        set_config('maxanswers_adv', '1', 'mod_lesson');
        set_config('enablecompletion', 1);
        set_config('usecomments', 0);

        // Create a preset and apply it.
        $generator = $this->getDataGenerator()->get_plugin_generator('core_adminpresets');
        $presetid = $generator->create_preset(['applypreset' => true]);
        $presetappid = $DB->get_field('adminpresets_app', 'id', ['adminpresetid' => $presetid]);

        $currentpresets = $DB->count_records('adminpresets');
        $currentitems = $DB->count_records('adminpresets_it');
        $currentadvitems = $DB->count_records('adminpresets_it_a');
        $currentplugins = $DB->count_records('adminpresets_plug');
        $this->assertCount(1, $DB->get_records('adminpresets_app'));
        $this->assertCount(3, $DB->get_records('adminpresets_app_it'));
        $this->assertCount(1, $DB->get_records('adminpresets_app_it_a'));
        $this->assertCount(2, $DB->get_records('adminpresets_app_plug'));

        // Check the setttings have changed accordingly after applying the preset.
        $this->assertEquals(0, get_config('core', 'enablebadges'));
        $this->assertEquals(900, get_config('mod_lesson', 'mediawidth'));
        $this->assertEquals(2, get_config('mod_lesson', 'maxanswers'));
        $this->assertEquals(1, get_config('core', 'allowemojipicker'));
        $this->assertEquals(1, get_config('core', 'enablecompletion'));
        $this->assertEquals(0, get_config('core', 'usecomments'));

        // Check the plugins visibility have changed accordingly with the ones defined in the preset.
        $enabledplugins = \core\plugininfo\enrol::get_enabled_plugins();
        $this->assertArrayNotHasKey('guest', $enabledplugins);
        $enabledplugins = \core\plugininfo\mod::get_enabled_plugins();
        $this->assertArrayNotHasKey('glossary', $enabledplugins);
        $enabledplugins = \core\plugininfo\qtype::get_enabled_plugins();
        $this->assertArrayHasKey('truefalse', $enabledplugins);

        // Call the method to be tested.
        $manager = new manager();
        list($presetapp, $rollback, $failures) = $manager->revert_preset($presetappid);

        // Check the preset applied has been reverted (so the records in _appXX tables have been removed).
        $this->assertNotEmpty($presetapp);
        $this->assertNotEmpty($rollback);
        $this->assertEmpty($failures);
        $this->assertCount(0, $DB->get_records('adminpresets_app'));
        $this->assertCount(0, $DB->get_records('adminpresets_app_it'));
        $this->assertCount(0, $DB->get_records('adminpresets_app_it_a'));
        $this->assertCount(0, $DB->get_records('adminpresets_app_plug'));
        // Check the preset data hasn't changed.
        $this->assertCount($currentpresets, $DB->get_records('adminpresets'));
        $this->assertCount($currentitems, $DB->get_records('adminpresets_it'));
        $this->assertCount($currentadvitems, $DB->get_records('adminpresets_it_a'));
        $this->assertCount($currentplugins, $DB->get_records('adminpresets_plug'));

        // Check the setting values have been reverted accordingly.
        $this->assertEquals(1, get_config('core', 'enablebadges'));
        $this->assertEquals(640, get_config('mod_lesson', 'mediawidth'));
        $this->assertEquals(5, get_config('mod_lesson', 'maxanswers'));
        $this->assertEquals(1, get_config('mod_lesson', 'maxanswers_adv'));
        // These settings won't change, regardless if they are posted to the form.
        $this->assertEquals(1, get_config('core', 'allowemojipicker'));
        $this->assertEquals(1, get_config('core', 'enablecompletion'));
        $this->assertEquals(0, get_config('core', 'usecomments'));

        // Check the plugins visibility have been reverted accordingly.
        $enabledplugins = \core\plugininfo\enrol::get_enabled_plugins();
        $this->assertArrayHasKey('guest', $enabledplugins);
        $enabledplugins = \core\plugininfo\mod::get_enabled_plugins();
        $this->assertArrayHasKey('glossary', $enabledplugins);
        // This plugin won't change (because it had the same value than before the preset was applied).
        $enabledplugins = \core\plugininfo\qtype::get_enabled_plugins();
        $this->assertArrayHasKey('truefalse', $enabledplugins);
    }
}
