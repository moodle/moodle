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

namespace tool_admin_presets\local\action;

use core_adminpresets\manager;

/**
 * Tests for the import class.
 *
 * @package    tool_admin_presets
 * @category   test
 * @copyright  2021 Sara Arjona (sara@moodle.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \tool_admin_presets\local\action\import
 */
class import_test extends \advanced_testcase {

    /**
     * Test the behaviour of execute() method.
     *
     * @dataProvider import_execute_provider
     * @covers ::execute
     *
     * @param string $filecontents File content to import.
     * @param bool $expectedpreset Whether the preset should be created or not.
     * @param bool $expectedsettings Whether settings will be created or not.
     * @param bool $expectedplugins Whether plugins will be created or not.
     * @param bool $expecteddebugging Whether debugging message will be thrown or not.
     * @param string|null $expectedexception Expected exception class (if that's the case).
     * @param string|null $expectedpresetname Expected preset name.
     */
    public function test_import_execute(string $filecontents, bool $expectedpreset, bool $expectedsettings = false,
            bool $expectedplugins = false, bool $expecteddebugging = false, ?string $expectedexception = null,
            string $expectedpresetname = 'Imported preset'): void {
        global $DB, $USER;

        $this->resetAfterTest();
        $this->setAdminUser();

        $currentpresets = $DB->count_records('adminpresets');
        $currentitems = $DB->count_records('adminpresets_it');
        $currentadvitems = $DB->count_records('adminpresets_it_a');

        // Create draft file to import.
        $draftid = file_get_unused_draft_itemid();
        $filerecord = [
            'component' => 'user',
            'filearea' => 'draft',
            'contextid' => \context_user::instance($USER->id)->id, 'itemid' => $draftid,
            'filename' => 'export.xml', 'filepath' => '/'
        ];
        $fs = get_file_storage();
        $fs->create_file_from_string($filerecord, $filecontents);
        // Get the data we are submitting for the form and mock submitting it.
        $formdata = [
            'xmlfile' => $draftid,
            'name' => '',
            'admin_presets_submit' => 'Save changes',
            'sesskey' => sesskey(),
        ];
        \tool_admin_presets\form\import_form::mock_submit($formdata);

        // Initialise the parameters and create the import class.
        $_POST['action'] = 'import';
        $_POST['mode'] = 'execute';

        $action = new import();
        $sink = $this->redirectEvents();
        try {
            $action->execute();
        } catch (\exception $e) {
            // If import action was successfull, redirect should be called so we will encounter an
            // 'unsupported redirect error' moodle_exception.
            if ($expectedexception) {
                $this->assertInstanceOf($expectedexception, $e);
            } else {
                $this->assertInstanceOf(\moodle_exception::class, $e);
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
                $presetid = $generator->access_protected($action, 'id');
                $this->assertArrayHasKey($presetid, $presets);
                $preset = $presets[$presetid];
                $this->assertEquals($expectedpresetname, $preset->name);
                $this->assertEquals('http://demo.moodle', $preset->site);
                $this->assertEquals('Ada Lovelace', $preset->author);
                $this->assertEquals(manager::NONCORE_PRESET, $preset->iscore);

                if ($expectedsettings) {
                    // Check the items have been created.
                    $items = $DB->get_records('adminpresets_it', ['adminpresetid' => $presetid]);
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
                    $plugins = $DB->get_records('adminpresets_plug', ['adminpresetid' => $presetid]);
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

            // Check the export event has been raised.
            $events = $sink->get_events();
            $sink->close();
            $event = reset($events);
            if ($expectedpreset) {
                // If preset has been created, an event should be raised.
                $this->assertInstanceOf('\\tool_admin_presets\\event\\preset_imported', $event);
            } else {
                $this->assertFalse($event);
            }
        }
    }

    /**
     * Data provider for test_import_execute().
     *
     * @return array
     */
    public static function import_execute_provider(): array {
        $fixturesfolder = __DIR__ . '/../../../../../presets/tests/fixtures/';

        return [
            'Import settings from an empty file' => [
                'filecontents' => '',
                'expectedpreset' => false,
            ],
            'Import settings and plugins from a valid XML file' => [
                'filecontents' => file_get_contents($fixturesfolder . 'import_settings_plugins.xml'),
                'expectedpreset' => true,
                'expectedsettings' => true,
                'expectedplugins' => true,
            ],
            'Import only settings from a valid XML file' => [
                'filecontents' => file_get_contents($fixturesfolder . 'import_settings.xml'),
                'expectedpreset' => true,
                'expectedsettings' => true,
                'expectedplugins' => false,
            ],
            'Import settings and plugins from a valid XML file with Starter name, which will be marked as non-core' => [
                'filecontents' => file_get_contents($fixturesfolder . 'import_starter_name.xml'),
                'expectedpreset' => true,
                'expectedsettings' => true,
                'expectedplugins' => true,
                'expecteddebugging' => false,
                'expectedexception' => null,
                'expectedpresetname' => 'Starter',
            ],
            'Import settings from an invalid XML file' => [
                'filecontents' => file_get_contents($fixturesfolder . 'invalid_xml_file.xml'),
                'expectedpreset' => false,
                'expectedsettings' => false,
                'expectedplugins' => false,
                'expecteddebugging' => false,
                'expectedexception' => \Exception::class,
            ],
            'Import unexisting settings category' => [
                'filecontents' => file_get_contents($fixturesfolder . 'unexisting_category.xml'),
                'expectedpreset' => false,
                'expectedsettings' => false,
                'expectedplugins' => false,
            ],
            'Import unexisting setting' => [
                'filecontents' => file_get_contents($fixturesfolder . 'unexisting_setting.xml'),
                'expectedpreset' => false,
                'expectedsettings' => false,
                'expectedplugins' => false,
                'expecteddebugging' => true,
            ],
            'Import valid settings with one unexisting setting too' => [
                'filecontents' => file_get_contents($fixturesfolder . 'import_settings_with_unexisting_setting.xml'),
                'expectedpreset' => true,
                'expectedsettings' => false,
                'expectedplugins' => false,
                'expecteddebugging' => true,
            ],
        ];
    }
}
