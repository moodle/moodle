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
final class preset_test extends \advanced_testcase {

    /**
     * Test for static create_from_plugin method.
     *
     * @covers ::create_from_plugin
     */
    public function test_create_from_plugin(): void {
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
    public function test_create_from_storedfile(): void {
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
    public function test_create_from_instance(): void {
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
     * Test for static create_from_fullname method.
     *
     * @covers ::create_from_fullname
     */
    public function test_create_from_fullname(): void {
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
        $savedpreset = $plugingenerator->create_preset($activity, $record);

        // Check instantiate from plugin.
        $pluginname = 'imagegallery';
        $fullname = '0/imagegallery';
        $result = preset::create_from_fullname($manager, $fullname);
        $this->assertTrue($result->isplugin);
        $this->assertEquals(get_string('modulename', "datapreset_$pluginname"), $result->name);
        $this->assertEquals($pluginname, $result->shortname);
        $this->assertEquals(get_string('modulename_help', "datapreset_$pluginname"), $result->description);
        $this->assertEmpty($result->get_userid());
        $this->assertEmpty($result->storedfile);
        $this->assertNull($result->get_path());

        // Check instantiate from user preset
        // Check create_from_instance is working as expected when a preset with this name exists.
        $fullname = $savedpreset->get_userid() . '/' . $savedpreset->name;
        $result = preset::create_from_fullname($manager, $fullname);
        $this->assertFalse($result->isplugin);
        $this->assertEquals($savedpreset->name, $result->name);
        $this->assertEquals($savedpreset->shortname, $result->shortname);
        $this->assertEquals($savedpreset->description, $savedpreset->description);
        $this->assertEquals($savedpreset->storedfile->get_userid(), $result->get_userid());
        $this->assertNotEmpty($result->storedfile);
        $this->assertEquals('/' . $savedpreset->name . '/', $result->get_path());
    }

    /**
     * Test for the save a preset method when the preset hasn't been saved before.
     *
     * @covers ::save
     */
    public function test_save_new_preset(): void {
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
     * Test for the save a preset method when is an existing preset that has been saved before.
     *
     * @covers ::save
     */
    public function test_save_existing_preset(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

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
        $oldpresetname = $record->name;
        $plugingenerator->create_preset($activity, $record);

        // Save should return false when trying to save an existing preset.
        $preset = preset::create_from_instance($manager, $record->name, $record->description);
        $result = $preset->save();
        $this->assertFalse($result);
        // Check no new preset has been created.
        $this->assertCount(1, $manager->get_available_saved_presets());

        // Save should overwrite existing preset if name or description have changed.
        $preset->name = 'New preset name';
        $preset->description = 'New preset description';
        $result = $preset->save();
        $this->assertTrue($result);
        // Check the preset files have been renamed.
        $presetfiles = array_merge(array_values(manager::TEMPLATES_LIST), ['preset.xml', '.']);
        foreach ($presetfiles as $templatefile) {
            $file = preset::get_file($preset->get_path(), $templatefile);
            $this->assertNotNull($file);
        }
        // Check old preset files have been removed.
        $oldpath = "{$oldpresetname}";
        foreach ($presetfiles as $templatefile) {
            $file = preset::get_file($oldpath, $templatefile);
            $this->assertNull($file);
        }

        // Check no new preset has been created.
        $savedpresets = $manager->get_available_saved_presets();
        $this->assertCount(1, $savedpresets);
        // Check the preset has the expected values.
        $savedpreset = reset($savedpresets);
        $this->assertEquals($preset->name, $savedpreset->name);
        $this->assertEquals($preset->description, $savedpreset->description);
        $this->assertNotEmpty($preset->storedfile);
        // Check the storedfile has been updated properly.
        $this->assertEquals($preset->name, trim($savedpreset->storedfile->get_filepath(), '/'));

        // Create another saved preset with empty description.
        $plugingenerator = $this->getDataGenerator()->get_plugin_generator('mod_data');
        $record = (object) [
            'name' => 'Testing preset 2',
        ];
        $plugingenerator->create_preset($activity, $record);
        $this->assertCount(2, $manager->get_available_saved_presets());
        // Description should be saved too when it was empty in the original preset and a new value is assigned to it.
        $preset = preset::create_from_instance($manager, $record->name);
        $preset->description = 'New preset description';
        $result = $preset->save();
        $this->assertTrue($result);
        $savedpresets = $manager->get_available_saved_presets();
        $this->assertCount(2, $savedpresets);
        foreach ($savedpresets as $savedpreset) {
            if ($savedpreset->name == $record->name) {
                $this->assertEquals($preset->description, $savedpreset->description);
            }
        }
    }

    /**
     * Test for the export a preset method.
     *
     * @covers ::export
     */
    public function test_export(): void {
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
            $extension = pathinfo($file->pathname, PATHINFO_EXTENSION);
            $ishtmlorxmlfile = in_array($extension, ['html', 'xml']);

            $expectedemptyfiles = array_intersect_key(manager::TEMPLATES_LIST, array_flip([
                'listtemplateheader',
                'listtemplatefooter',
                'rsstitletemplate',
            ]));

            if ($ishtmlorxmlfile && !in_array($file->pathname, $expectedemptyfiles)) {
                $this->assertGreaterThan(0, $file->size);
            } else {
                $this->assertEquals(0, $file->size);
            }
        }
        $ziparchive->close();
    }

    /**
     * Test for get_userid().
     *
     * @covers ::get_userid
     */
    public function test_get_userid(): void {
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
    public function test_get_path(): void {
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
    public static function is_directory_a_preset_provider(): array {
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
    public function test_get_name_from_plugin(): void {
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
    public function test_get_description_from_plugin(): void {
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
    public function test_generate_preset_xml(array $params, ?string $description): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        // Make accessible the method.
        $reflection = new \ReflectionClass(preset::class);
        $method = $reflection->getMethod('generate_preset_xml');

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
    public static function generate_preset_xml_provider(): array {
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
    public function test_get_file(): void {
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

    /**
     * Test for can_manage().
     *
     * @covers ::can_manage
     */
    public function test_can_manage(): void {
        $this->resetAfterTest();

        // Create course, database activity and users.
        $course = $this->getDataGenerator()->create_course();
        $data = $this->getDataGenerator()->create_module('data', ['course' => $course->id]);
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $manager = manager::create_from_instance($data);

        $preset1name = 'Admin preset';
        $preset2name = 'Teacher preset';

        // Create a saved preset by admin.
        $this->setAdminUser();
        $plugingenerator = $this->getDataGenerator()->get_plugin_generator('mod_data');
        $record = (object) [
            'name' => $preset1name,
            'description' => 'Testing preset description',
        ];
        $adminpreset = $plugingenerator->create_preset($data, $record);

        // Create a saved preset by teacher.
        $this->setUser($teacher);
        $record = (object) [
            'name' => $preset2name,
            'description' => 'Testing preset description',
        ];
        $teacherpreset = $plugingenerator->create_preset($data, $record);

        // Plugins can't be deleted.
        $pluginpresets = manager::get_available_plugin_presets();
        $pluginpreset = reset($pluginpresets);
        $this->assertFalse($pluginpreset->can_manage());

        // Admin can delete all saved presets.
        $this->setAdminUser();
        $this->assertTrue($adminpreset->can_manage());
        $this->assertTrue($teacherpreset->can_manage());

        // Teacher can delete their own preset only.
        $this->setUser($teacher);
        $this->assertFalse($adminpreset->can_manage());
        $this->assertTrue($teacherpreset->can_manage());

        // Student can't delete any of the presets.
        $this->setUser($student);
        $this->assertFalse($adminpreset->can_manage());
        $this->assertFalse($teacherpreset->can_manage());
    }

    /**
     * Test for delete().
     *
     * @covers ::delete
     */
    public function test_delete(): void {
        $this->resetAfterTest();

        // Create course, database activity and users.
        $course = $this->getDataGenerator()->create_course();
        $data = $this->getDataGenerator()->create_module('data', ['course' => $course->id]);
        $manager = manager::create_from_instance($data);
        $presetname = 'Admin preset';

        // Create a saved preset by admin.
        $this->setAdminUser();
        $plugingenerator = $this->getDataGenerator()->get_plugin_generator('mod_data');
        $record = (object) [
            'name' => $presetname,
            'description' => 'Testing preset description',
        ];
        $adminpreset = $plugingenerator->create_preset($data, $record);
        $initialpresets = $manager->get_available_presets();

        // Plugins can't be deleted.
        $pluginpresets = manager::get_available_plugin_presets();
        $pluginpreset = reset($pluginpresets);
        $result = $pluginpreset->delete();
        $currentpluginpresets = manager::get_available_plugin_presets();
        $this->assertEquals(count($pluginpresets), count($currentpluginpresets));

        $result = $adminpreset->delete();
        $this->assertTrue($result);

        // After deleting the preset, there is no file linked.
        $adminpreset = preset::create_from_instance($manager, $presetname);
        $this->assertEmpty($adminpreset->storedfile);

        // Check the preset has been deleted.
        $currentpresets = $manager->get_available_presets();
        $this->assertEquals(count($initialpresets) - 1, count($currentpresets));

        // The behavior of trying to delete a preset twice.
        $result = $adminpreset->delete();
        $this->assertFalse($result);

        // Check the preset has not been deleted.
        $currentpresets = $manager->get_available_presets();
        $this->assertEquals(count($initialpresets) - 1, count($currentpresets));

        $emptypreset = preset::create_from_instance($manager, $presetname);
        // The behavior of deleting an empty preset.
        $result = $emptypreset->delete();
        $this->assertFalse($result);

        // Check the preset has not been deleted.
        $currentpresets = $manager->get_available_presets();
        $this->assertEquals(count($initialpresets) - 1, count($currentpresets));
    }

    /**
     * Test for the get_fields method.
     *
     * @covers ::get_fields
     */
    public function test_get_fields(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

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
        $preset = $plugingenerator->create_preset($activity, $record);

        // Check regular fields.
        $fields = $preset->get_fields();
        $this->assertCount(1, $fields);
        $this->assertArrayHasKey('field-1', $fields);
        $field = $fields['field-1'];
        $this->assertEquals('text', $field->type);
        $this->assertEquals('field-1', $field->get_name());
        $this->assertEquals(false, $field->get_preview());

        // Check preview fields.
        $savedpresets = $manager->get_available_saved_presets();
        $preset = reset($savedpresets);
        $fields = $preset->get_fields(true);
        $this->assertCount(1, $fields);
        $this->assertArrayHasKey('field-1', $fields);
        $field = $fields['field-1'];
        $this->assertEquals('text', $field->type);
        $this->assertEquals('field-1', $field->get_name());
        $this->assertEquals(true, $field->get_preview());
    }

    /**
     * Test for the get_sample_entries method.
     *
     * @covers ::get_sample_entries
     */
    public function test_get_sample_entries(): void {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

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
        $preset = $plugingenerator->create_preset($activity, $record);

        $entries = $preset->get_sample_entries(3);
        $this->assertCount(3, $entries);
        foreach ($entries as $entry) {
            $this->assertEquals($user->id, $entry->userid);
            $this->assertEquals($user->email, $entry->email);
            $this->assertEquals($user->firstname, $entry->firstname);
            $this->assertEquals($user->lastname, $entry->lastname);
            $this->assertEquals($activity->id, $entry->dataid);
            $this->assertEquals(0, $entry->groupid);
            $this->assertEquals(1, $entry->approved);
        }
    }

    /**
     * Test for the get_template_content method.
     *
     * @covers ::get_template_content
     */
    public function test_get_template_content(): void {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);
        $course = $this->getDataGenerator()->create_course();

        // Module data with templates.
        $templates = [
            'singletemplate' => 'Single template content',
            'listtemplate' => 'List template content',
            'listtemplateheader' => 'List template content header',
            'listtemplatefooter' => 'List template content footer',
            'addtemplate' => 'Add template content',
            'rsstemplate' => 'RSS template content',
            'rsstitletemplate' => 'RSS title template content',
            'csstemplate' => 'CSS template content',
            'jstemplate' => 'JS template content',
            'asearchtemplate' => 'Advanced search template content',
        ];
        $params = array_merge(['course' => $course], $templates);

        // Create a database activity.
        $activity = $this->getDataGenerator()->create_module(manager::MODULE, $params);
        $manager = manager::create_from_instance($activity);

        // Create a saved preset.
        $plugingenerator = $this->getDataGenerator()->get_plugin_generator('mod_data');
        $record = (object) [
            'name' => 'Testing preset name',
            'description' => 'Testing preset description',
        ];
        $preset = $plugingenerator->create_preset($activity, $record);

        // Test user preset templates.
        foreach ($templates as $templatename => $templatecontent) {
            $content = $preset->get_template_content($templatename);
            $this->assertEquals($templatecontent, $content);
        }

        // Test plugin preset content.
        $pluginname = 'imagegallery';
        $preset = preset::create_from_plugin($manager, $pluginname);
        foreach (manager::TEMPLATES_LIST as $templatename => $templatefile) {
            // Get real file contents.
            $path = $manager->path . '/preset/' . $pluginname . '/' . $templatefile;
            $templatecontent = file_get_contents($path);
            $content = $preset->get_template_content($templatename);
            $this->assertEquals($templatecontent, $content);
        }
    }

    /**
     * Test for the get_fullname method.
     *
     * @covers ::get_fullname
     */
    public function test_get_fullname(): void {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);
        $course = $this->getDataGenerator()->create_course();

        // Create a database activity.
        $activity = $this->getDataGenerator()->create_module(manager::MODULE, ['course' => $course]);
        $manager = manager::create_from_instance($activity);

        // Create a saved preset.
        $plugingenerator = $this->getDataGenerator()->get_plugin_generator('mod_data');
        $record = (object) [
            'name' => 'Testing preset name',
            'description' => 'Testing preset description',
        ];
        $preset = $plugingenerator->create_preset($activity, $record);

        // Test user preset templates.
        $this->assertEquals("{$user->id}/Testing preset name", $preset->get_fullname());

        // Test plugin preset content.
        $pluginname = 'imagegallery';
        $preset = preset::create_from_plugin($manager, $pluginname);
        $this->assertEquals("0/imagegallery", $preset->get_fullname());
    }
}
