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

namespace mod_data;

use file_archive;
use stdClass;
use zip_archive;

/**
 * Preset tests class for mod_data.
 *
 * @package    mod_data
 * @category   test
 * @copyright  2022 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \mod_data\preset
 */
class preset_test extends \advanced_testcase {

    /**
     * Test for static create_from_plugin method.
     *
     * @covers ::create_from_plugin
     */
    public function test_create_from_plugin() {
        $this->resetAfterTest();
        $this->setAdminUser();

        // Check create_from_plugin is working as expected when an existing plugin is given.
        $pluginname = 'imagegallery';
        $result = preset::create_from_plugin(null, $pluginname);
        $this->assertTrue($result->isplugin);
        $this->assertEquals(get_string('modulename', "datapreset_$pluginname"), $result->name);
        $this->assertEquals($pluginname, $result->shortname);
        $this->assertEquals(get_string('modulename_help', "datapreset_$pluginname"), $result->description);
        $this->assertEmpty($result->get_userid());
        $this->assertEmpty($result->storedfile);
        $this->assertNull($result->get_path());

        // Check create_from_plugin is working as expected when an unexisting plugin is given.
        $pluginname = 'unexisting';
        $result = preset::create_from_plugin(null, $pluginname);
        $this->assertNull($result);
    }

    /**
     * Test for static create_from_storedfile method.
     *
     * @covers ::create_from_storedfile
     */
    public function test_create_from_storedfile() {
        global $USER;

        $this->resetAfterTest();
        $this->setAdminUser();

        // Create a course and a database activity.
        $course = $this->getDataGenerator()->create_course();
        $activity = $this->getDataGenerator()->create_module(manager::MODULE, ['course' => $course]);
        $manager = manager::create_from_instance($activity);

        // Create a saved preset.
        $plugingenerator = $this->getDataGenerator()->get_plugin_generator('mod_data');
        $record = (object) [
            'name' => 'Testing preset name',
            'description' => 'Testing preset description',
        ];
        $plugingenerator->create_preset($activity, $record);
        $savedpresets = $manager->get_available_saved_presets();
        $savedpreset = reset($savedpresets);

        // Check create_from_storedfile is working as expected with a valid preset file.
        $result = preset::create_from_storedfile($manager, $savedpreset->storedfile);
        $this->assertFalse($result->isplugin);
        $this->assertEquals($record->name, $result->name);
        $this->assertEquals($record->name, $result->shortname);
        $this->assertEquals($record->description, $result->description);
        $this->assertEquals($savedpreset->storedfile->get_userid(), $result->get_userid());
        $this->assertNotEmpty($result->storedfile);
        $this->assertEquals('/' . $record->name . '/', $result->get_path());

        // Check create_from_storedfile is not creating a preset object when an invalid file is given.
        $draftid = file_get_unused_draft_itemid();
        $filerecord = [
            'component' => 'user',
            'filearea' => 'draft',
            'contextid' => \context_user::instance($USER->id)->id,
            'itemid' => $draftid,
            'filename' => 'preset.xml',
            'filepath' => '/'
        ];
        $fs = get_file_storage();
        $file = $fs->create_file_from_string($filerecord, 'This is the file content');
        $result = preset::create_from_storedfile($manager, $file);
        $this->assertNull($result);
    }

    /**
     * Test for static create_from_instance method.
     *
     * @covers ::create_from_instance
     */
    public function test_create_from_instance() {
        $this->resetAfterTest();
        $this->setAdminUser();

        // Create a course and a database activity.
        $course = $this->getDataGenerator()->create_course();
        $activity = $this->getDataGenerator()->create_module(manager::MODULE, ['course' => $course]);
        $manager = manager::create_from_instance($activity);

        // Create a saved preset.
        $plugingenerator = $this->getDataGenerator()->get_plugin_generator('mod_data');
        $record = (object) [
            'name' => 'Testing preset name',
            'description' => 'Testing preset description',
        ];
        $plugingenerator->create_preset($activity, $record);
        $savedpresets = $manager->get_available_saved_presets();
        $savedpreset = reset($savedpresets);

        // Check create_from_instance is working as expected when a preset with this name exists.
        $result = preset::create_from_instance($manager, $record->name, $record->description);
        $this->assertFalse($result->isplugin);
        $this->assertEquals($record->name, $result->name);
        $this->assertEquals($record->name, $result->shortname);
        $this->assertEquals($record->description, $result->description);
        $this->assertEquals($savedpreset->storedfile->get_userid(), $result->get_userid());
        $this->assertNotEmpty($result->storedfile);
        $this->assertEquals('/' . $record->name . '/', $result->get_path());

        // Check create_from_instance is working as expected when there is no preset with the given name.
        $presetname = 'Unexisting preset';
        $presetdescription = 'This is the description for the unexisting preset';
        $result = preset::create_from_instance($manager, $presetname, $presetdescription);
        $this->assertFalse($result->isplugin);
        $this->assertEquals($presetname, $result->name);
        $this->assertEquals($presetname, $result->shortname);
        $this->assertEquals($presetdescription, $result->description);
        $this->assertEmpty($result->get_userid());
        $this->assertEmpty($result->storedfile);
        $this->assertEquals('/' . $presetname . '/', $result->get_path());
    }

    /**
     * Test for the save a preset method.
     *
     * @covers ::save
     */
    public function test_save() {
        $this->resetAfterTest();
        $this->setAdminUser();

        // Save should return false when trying to save a plugin preset.
        $preset = preset::create_from_plugin(null, 'imagegallery');
        $result = $preset->save();
        $this->assertFalse($result);

        // Create a course and a database activity.
        $course = $this->getDataGenerator()->create_course();
        $activity = $this->getDataGenerator()->create_module(manager::MODULE, ['course' => $course]);
        $manager = manager::create_from_instance($activity);

        // Add a field to the activity.
        $fieldrecord = new stdClass();
        $fieldrecord->name = 'field-1';
        $fieldrecord->type = 'text';
        $datagenerator = $this->getDataGenerator()->get_plugin_generator('mod_data');
        $datagenerator->create_field($fieldrecord, $activity);

        // Create a saved preset.
        $plugingenerator = $this->getDataGenerator()->get_plugin_generator('mod_data');
        $record = (object) [
            'name' => 'Testing preset name',
            'description' => 'Testing preset description',
        ];
        $plugingenerator->create_preset($activity, $record);

        // Save should return false when trying to save an existing saved preset.
        $preset = preset::create_from_instance($manager, $record->name, $record->description);
        $result = $preset->save();
        $this->assertFalse($result);

        // The preset should be saved when it's new and there is no any other having the same name.
        $savedpresets = $manager->get_available_saved_presets();
        $this->assertCount(1, $savedpresets);
        $presetname = 'New preset';
        $presetdescription = 'This is the description for the new preset';
        $preset = preset::create_from_instance($manager, $presetname, $presetdescription);
        $result = $preset->save();
        $this->assertTrue($result);
        // Check the preset has been created.
        $savedpresets = $manager->get_available_saved_presets();
        $this->assertCount(2, $savedpresets);
        $savedpresetsnames = array_map(function($preset) {
            return $preset->name;
        }, $savedpresets);
        $this->assertContains($presetname, $savedpresetsnames);
    }

    /**
     * Test for the export a preset method.
     *
     * @covers ::export
     */
    public function test_export() {
        $this->resetAfterTest();
        $this->setAdminUser();

        // Export should return empty string when trying to export a plugin preset.
        $preset = preset::create_from_plugin(null, 'imagegallery');
        $result = $preset->export();
        $this->assertEmpty($result);

        // Create a course and a database activity.
        $course = $this->getDataGenerator()->create_course();
        $activity = $this->getDataGenerator()->create_module(manager::MODULE, ['course' => $course]);
        $manager = manager::create_from_instance($activity);

        // Add a field to the activity.
        $fieldrecord = new stdClass();
        $fieldrecord->name = 'field-1';
        $fieldrecord->type = 'text';
        $datagenerator = $this->getDataGenerator()->get_plugin_generator('mod_data');
        $datagenerator->create_field($fieldrecord, $activity);

        // For now, default templates are not created automatically. This will be changed in MDL-75234.
        foreach (manager::TEMPLATES_LIST as $templatename => $notused) {
            data_generate_default_template($activity, $templatename);
        }

        $preset = preset::create_from_instance($manager, $activity->name);
        $result = $preset->export();
        $presetfilenames = array_merge(array_values(manager::TEMPLATES_LIST), ['preset.xml']);

        $ziparchive = new zip_archive();
        $ziparchive->open($result, file_archive::OPEN);
        $files = $ziparchive->list_files();
        foreach ($files as $file) {
            $this->assertContains($file->pathname, $presetfilenames);
            // Check the file is not empty (except CSS, JS and listtemplateheader/footer files which are empty by default).
            $ishtmlorxmlfile = str_ends_with($file->pathname, '.html') || str_ends_with($file->pathname, '.xml');
            $islistheader = $file->pathname != manager::TEMPLATES_LIST['listtemplateheader'];
            $islistfooter = $file->pathname != manager::TEMPLATES_LIST['listtemplatefooter'];
            if ($ishtmlorxmlfile && !$islistheader && !$islistfooter) {
                $this->assertGreaterThan(0, $file->size);
            }
        }
        $ziparchive->close();
    }

    /**
     * Test for get_userid().
     *
     * @covers ::get_userid
     */
    public function test_get_userid() {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $activity = $this->getDataGenerator()->create_module(manager::MODULE, ['course' => $course]);
        $user = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $this->setUser($user);

        // Check userid is null for plugin preset.
        $manager = manager::create_from_instance($activity);
        $pluginpresets = $manager->get_available_plugin_presets();
        $pluginpreset = reset($pluginpresets);
        $this->assertNull($pluginpreset->get_userid());

        // Check userid meets the user that has created the preset when it's a saved preset.
        $plugingenerator = $this->getDataGenerator()->get_plugin_generator('mod_data');
        $savedpreset = (object) [
            'name' => 'Preset created by teacher',
        ];
        $plugingenerator->create_preset($activity, $savedpreset);
        $savedpresets = $manager->get_available_saved_presets();
        $savedpreset = reset($savedpresets);
        $this->assertEquals($user->id, $savedpreset->get_userid());

        // Check userid is null when preset hasn't any file associated.
        $preset = preset::create_from_instance($manager, 'Unexisting preset');
        $this->assertNull($preset->get_userid());
    }

    /**
     * Test for get_path().
     *
     * @covers ::get_path
     */
    public function test_get_path() {
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $activity = $this->getDataGenerator()->create_module(manager::MODULE, ['course' => $course]);

        // Check path is null for plugin preset.
        $manager = manager::create_from_instance($activity);
        $pluginpresets = $manager->get_available_plugin_presets();
        $pluginpreset = reset($pluginpresets);
        $this->assertNull($pluginpreset->get_path());

        // Check path meets expected value when it's a saved preset.
        $plugingenerator = $this->getDataGenerator()->get_plugin_generator('mod_data');
        $savedpreset = (object) [
            'name' => 'Saved preset',
        ];
        $plugingenerator->create_preset($activity, $savedpreset);
        $savedpresets = $manager->get_available_saved_presets();
        $savedpreset = reset($savedpresets);
        $this->assertEquals("/{$savedpreset->name}/", $savedpreset->get_path());

        // Check path is /presetname/ when preset hasn't any file associated.
        $presetname = 'Unexisting preset';
        $preset = preset::create_from_instance($manager, $presetname);
        $this->assertEquals("/{$presetname}/", $preset->get_path());
    }

    /**
     * Test for is_directory_a_preset().
     *
     * @dataProvider is_directory_a_preset_provider
     * @covers ::is_directory_a_preset
     * @param string $directory
     * @param bool $expected
     */
    public function test_is_directory_a_preset(string $directory, bool $expected): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $result = preset::is_directory_a_preset($directory);
        $this->assertEquals($expected, $result);
    }

    /**
     * Data provider for test_is_directory_a_preset().
     *
     * @return array
     */
    public function is_directory_a_preset_provider(): array {
        global $CFG;

        return [
            'Valid preset directory' => [
                'directory' => $CFG->dirroot . '/mod/data/preset/imagegallery',
                'expected' => true,
            ],
            'Invalid preset directory' => [
                'directory' => $CFG->dirroot . '/mod/data/field/checkbox',
                'expected' => false,
            ],
            'Unexisting preset directory' => [
                'directory' => $CFG->dirroot . 'unexistingdirectory',
                'expected' => false,
            ],
        ];
    }

    /**
     * Test for get_name_from_plugin().
     *
     * @covers ::get_name_from_plugin
     */
    public function test_get_name_from_plugin() {
        $this->resetAfterTest();
        $this->setAdminUser();

        // The expected name for plugins with modulename in lang is this value.
        $name = preset::get_name_from_plugin('imagegallery');
        $this->assertEquals('Image gallery', $name);

        // However, if the plugin doesn't exist or the modulename is not defined, the preset shortname will be returned.
        $presetshortname = 'nonexistingpreset';
        $name = preset::get_name_from_plugin($presetshortname);
        $this->assertEquals($presetshortname, $name);
    }

    /**
     * Test for get_description_from_plugin().
     *
     * @covers ::get_description_from_plugin
     */
    public function test_get_description_from_plugin() {
        $this->resetAfterTest();
        $this->setAdminUser();

        // The expected name for plugins with modulename in lang is this value.
        $description = preset::get_description_from_plugin('imagegallery');
        $this->assertEquals('Use this preset to collect images.', $description);

        // However, if the plugin doesn't exist or the modulename is not defined, empty string will be returned.
        $presetshortname = 'nonexistingpreset';
        $description = preset::get_description_from_plugin($presetshortname);
        $this->assertEmpty($description);
    }

    /**
     * Test for generate_preset_xml().
     *
     * @covers ::generate_preset_xml
     * @dataProvider generate_preset_xml_provider
     * @param array $params activity config settings
     * @param string|null $description preset description
     */
    public function test_generate_preset_xml(array $params, ?string $description) {
        $this->resetAfterTest();
        $this->setAdminUser();

        // Make accessible the method.
        $reflection = new \ReflectionClass(preset::class);
        $method = $reflection->getMethod('generate_preset_xml');
        $method->setAccessible(true);

        // The method should return empty string when trying to generate preset.xml for a plugin preset.
        $preset = preset::create_from_plugin(null, 'imagegallery');
        $result = $method->invokeArgs($preset, []);
        $this->assertEmpty($result);

        // Create a course and a database activity.
        $course = $this->getDataGenerator()->create_course();
        $activity = $this->getDataGenerator()->create_module(manager::MODULE, array_merge(['course' => $course], $params));

        // Add a field to the activity.
        $fieldrecord = new stdClass();
        $fieldrecord->name = 'field-1';
        $fieldrecord->type = 'text';
        $datagenerator = $this->getDataGenerator()->get_plugin_generator('mod_data');
        $datagenerator->create_field($fieldrecord, $activity);

        $manager = manager::create_from_instance($activity);
        $preset = preset::create_from_instance($manager, $activity->name, $description);

        // Call the generate_preset_xml method.
        $result = $method->invokeArgs($preset, []);
        // Check is a valid XML.
        $parsedxml = simplexml_load_string($result);
        // Check the description has the expected value.
        $this->assertEquals($description, strval($parsedxml->description));
        // Check settings have the expected values.
        foreach ($params as $paramname => $paramvalue) {
            $this->assertEquals($paramvalue, strval($parsedxml->settings->{$paramname}));
        }
        // Check field have the expected values.
        $this->assertEquals($fieldrecord->name, strval($parsedxml->field->name));
        $this->assertEquals($fieldrecord->type, strval($parsedxml->field->type));
    }

    /**
     * Data provider for generate_preset_xml().
     *
     * @return array
     */
    public function generate_preset_xml_provider(): array {
        return [
            'Generate preset.xml with the default params and empty description' => [
                'params' => [],
                'description' => null,
            ],
            'Generate preset.xml with a description but the default params' => [
                'params' => [],
                'description' => 'This is a description',
            ],
            'Generate preset.xml with empty description but changing some params' => [
                'params' => [
                    'requiredentries' => 2,
                    'approval' => 1,
                ],
                'description' => null,
            ],
            'Generate preset.xml with a description and changing some params' => [
                'params' => [
                    'maxentries' => 5,
                    'manageapproved' => 0,
                ],
                'description' => 'This is a description',
            ],
        ];
    }

    /**
     * Test for get_file().
     *
     * @covers ::get_file
     */
    public function test_get_file() {
        $this->resetAfterTest();
        $this->setAdminUser();

        // Create a course and a database activity.
        $course = $this->getDataGenerator()->create_course();
        $activity = $this->getDataGenerator()->create_module(manager::MODULE, ['course' => $course]);
        $manager = manager::create_from_instance($activity);

        $presetname = 'Saved preset';
        // Check file doesn't exist if the preset hasn't been saved yet.
        $preset = preset::create_from_instance($manager, $presetname);
        $file = preset::get_file($preset->get_path(), 'preset.xml');
        $this->assertNull($file);

        // Check file is not empty when there is a saved preset with this name.
        $preset->save();
        $file = preset::get_file($preset->get_path(), 'preset.xml');
        $this->assertNotNull($file);
        $this->assertStringContainsString($presetname, $file->get_filepath());
        $this->assertEquals('preset.xml', $file->get_filename());

        // Check invalid preset file name doesn't exist.
        $file = preset::get_file($preset->get_path(), 'unexistingpreset.xml');
        $this->assertNull($file);
    }
}
