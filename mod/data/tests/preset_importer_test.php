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

use mod_data\local\importer\preset_existing_importer;
use mod_data\local\importer\preset_importer;

/**
 * Preset importer tests class for mod_data.
 *
 * @package    mod_data
 * @category   test
 * @copyright  2022 Amaia Anabitarte <amaia@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \mod_data\local\importer\preset_importer
 */
final class preset_importer_test extends \advanced_testcase {
    /**
     * Data provider for build providers for test_needs_mapping and test_set_affected_fields.
     *
     * @return array[]
     */
    public static function preset_importer_provider(): array {
        // Image gallery preset is: ['title' => 'text', 'description' => 'textarea', 'image' => 'picture'];

        $titlefield = new \stdClass();
        $titlefield->name = 'title';
        $titlefield->type = 'text';

        $descfield = new \stdClass();
        $descfield->name = 'description';
        $descfield->type = 'textarea';

        $imagefield = new \stdClass();
        $imagefield->name = 'image';
        $imagefield->type = 'picture';

        $difffield = new \stdClass();
        $difffield->name = 'title';
        $difffield->type = 'textarea';

        $newfield = new \stdClass();
        $newfield->name = 'number';
        $newfield->type = 'number';

        return [
            'Empty database / Empty importer' => [
                'currentfields' => [],
                'newfields' => [],
                'pluginname' => '',
            ],
            'Empty database / Importer with fields' => [
                'currentfields' => [],
                'newfields' => [$titlefield, $descfield, $imagefield],
                'pluginname' => 'imagegallery',
            ],
            'Database with fields / Empty importer' => [
                'currentfields' => [$titlefield, $descfield, $imagefield],
                'newfields' => [],
                'pluginname' => '',
            ],
            'Same fields' => [
                'currentfields' => [$titlefield, $descfield, $imagefield],
                'newfields' => [$titlefield, $descfield, $imagefield],
                'pluginname' => 'imagegallery',
            ],
            'Fields to create' => [
                'currentfields' => [$titlefield, $descfield],
                'newfields' => [$titlefield, $descfield, $imagefield],
                'pluginname' => 'imagegallery',
            ],
            'Fields to remove' => [
                'currentfields' => [$titlefield, $descfield, $imagefield, $difffield],
                'newfields' => [$titlefield, $descfield, $imagefield],
                'pluginname' => 'imagegallery',
            ],
            'Fields to update' => [
                'currentfields' => [$difffield, $descfield, $imagefield],
                'newfields' => [$titlefield, $descfield, $imagefield],
                'pluginname' => 'imagegallery',
            ],
            'Fields to create, remove and update' => [
                'currentfields' => [$titlefield, $descfield, $imagefield, $difffield],
                'newfields' => [$titlefield, $descfield, $newfield],
                'pluginname' => '',
            ],
        ];
    }

    /**
     * Data provider for needs_mapping().
     *
     * @return array[]
     */
    public static function needs_mapping_provider(): array {
        $basedprovider = static::preset_importer_provider();

        $basedprovider['Empty database / Empty importer']['needsmapping'] = false;
        $basedprovider['Empty database / Importer with fields']['needsmapping'] = false;
        $basedprovider['Database with fields / Empty importer']['needsmapping'] = true;
        $basedprovider['Same fields']['needsmapping'] = false;
        $basedprovider['Fields to create']['needsmapping'] = true;
        $basedprovider['Fields to remove']['needsmapping'] = true;
        $basedprovider['Fields to update']['needsmapping'] = true;
        $basedprovider['Fields to create, remove and update']['needsmapping'] = true;

        return $basedprovider;
    }

    /**
     * Test for needs_mapping method.
     *
     * @dataProvider needs_mapping_provider
     * @covers ::needs_mapping
     *
     * @param array $currentfields Fields of the current activity.
     * @param array $newfields Fields to be imported.
     * @param string $pluginname The plugin preset to be imported.
     * @param bool $expectedresult Expected exception.
     */
    public function test_needs_mapping(
        array $currentfields,
        array $newfields,
        string $pluginname,
        bool $expectedresult
    ): void {

        global $USER;

        $this->resetAfterTest();
        $this->setAdminUser();
        $plugingenerator = $this->getDataGenerator()->get_plugin_generator('mod_data');

        // Create a course and a database activity.
        $course = $this->getDataGenerator()->create_course();
        $activity = $this->getDataGenerator()->create_module(manager::MODULE, ['course' => $course]);
        // Add current fields to the activity.
        foreach ($currentfields as $field) {
            $plugingenerator->create_field($field, $activity);
        }
        $manager = manager::create_from_instance($activity);

        $presetactivity = $this->getDataGenerator()->create_module(manager::MODULE, ['course' => $course]);
        // Add current fields to the activity.
        foreach ($newfields as $field) {
            $plugingenerator->create_field($field, $presetactivity);
        }

        $record = (object) [
            'name' => 'Testing preset name',
            'description' => 'Testing preset description',
        ];
        $saved = $plugingenerator->create_preset($presetactivity, $record);
        $savedimporter = new preset_existing_importer($manager, $USER->id . '/Testing preset name');
        $this->assertEquals($savedimporter->needs_mapping(), $expectedresult);

        // Create presets and importers.
        if ($pluginname) {
            $plugin = preset::create_from_plugin(null, $pluginname);
            $pluginimporter = new preset_existing_importer($manager, '/' . $pluginname);
            $this->assertEquals($pluginimporter->needs_mapping(), $expectedresult);
        }
    }

    /**
     * Data provider for test_set_affected_fields().
     *
     * @return array[]
     */
    public static function set_affected_provider(): array {
        $basedprovider = static::preset_importer_provider();

        $basedprovider['Empty database / Empty importer']['fieldstocreate'] = 0;
        $basedprovider['Empty database / Empty importer']['fieldstoremove'] = 0;
        $basedprovider['Empty database / Empty importer']['fieldstoupdate'] = 0;

        $basedprovider['Empty database / Importer with fields']['fieldstocreate'] = 3;
        $basedprovider['Empty database / Importer with fields']['fieldstoremove'] = 0;
        $basedprovider['Empty database / Importer with fields']['fieldstoupdate'] = 0;

        $basedprovider['Database with fields / Empty importer']['fieldstocreate'] = 0;
        $basedprovider['Database with fields / Empty importer']['fieldstoremove'] = 3;
        $basedprovider['Database with fields / Empty importer']['fieldstoupdate'] = 0;

        $basedprovider['Same fields']['fieldstocreate'] = 0;
        $basedprovider['Same fields']['fieldstoremove'] = 0;
        $basedprovider['Same fields']['fieldstoupdate'] = 3;

        $basedprovider['Fields to create']['fieldstocreate'] = 1;
        $basedprovider['Fields to create']['fieldstoremove'] = 0;
        $basedprovider['Fields to create']['fieldstoupdate'] = 2;

        $basedprovider['Fields to remove']['fieldstocreate'] = 0;
        $basedprovider['Fields to remove']['fieldstoremove'] = 1;
        $basedprovider['Fields to remove']['fieldstoupdate'] = 3;

        $basedprovider['Fields to update']['fieldstocreate'] = 1;
        $basedprovider['Fields to update']['fieldstoremove'] = 1;
        $basedprovider['Fields to update']['fieldstoupdate'] = 2;

        $basedprovider['Fields to create, remove and update']['fieldstocreate'] = 1;
        $basedprovider['Fields to create, remove and update']['fieldstoremove'] = 2;
        $basedprovider['Fields to create, remove and update']['fieldstoupdate'] = 2;

        return $basedprovider;
    }

    /**
     * Test for set_affected_fields method.
     *
     * @dataProvider set_affected_provider
     * @covers ::set_affected_fields
     *
     * @param array $currentfields Fields of the current activity.
     * @param array $newfields Fields to be imported.
     * @param string $pluginname The plugin preset to be imported.
     * @param int $fieldstocreate Expected number of fields on $fieldstocreate.
     * @param int $fieldstoremove Expected number of fields on $fieldstoremove.
     * @param int $fieldstoupdate Expected number of fields on $fieldstoupdate.
     */
    public function test_set_affected_fields(
        array $currentfields,
        array $newfields,
        string $pluginname,
        int $fieldstocreate,
        int $fieldstoremove,
        int $fieldstoupdate
    ): void {
        global $USER;

        $this->resetAfterTest();
        $this->setAdminUser();
        $plugingenerator = $this->getDataGenerator()->get_plugin_generator('mod_data');

        // Create a course and a database activity.
        $course = $this->getDataGenerator()->create_course();
        $activity = $this->getDataGenerator()->create_module(manager::MODULE, ['course' => $course]);
        // Add current fields to the activity.
        foreach ($currentfields as $field) {
            $plugingenerator->create_field($field, $activity);
        }
        $manager = manager::create_from_instance($activity);

        $presetactivity = $this->getDataGenerator()->create_module(manager::MODULE, ['course' => $course]);
        // Add current fields to the activity.
        foreach ($newfields as $field) {
            $plugingenerator->create_field($field, $presetactivity);
        }

        $record = (object) [
            'name' => 'Testing preset name',
            'description' => 'Testing preset description',
        ];
        $saved = $plugingenerator->create_preset($presetactivity, $record);
        $savedimporter = new preset_existing_importer($manager, $USER->id . '/Testing preset name');
        $this->assertEquals(count($savedimporter->fieldstoremove), $fieldstoremove);
        $this->assertEquals(count($savedimporter->fieldstocreate), $fieldstocreate);
        $this->assertEquals(count($savedimporter->fieldstoupdate), $fieldstoupdate);

        // Create presets and importers.
        if ($pluginname) {
            $plugin = preset::create_from_plugin(null, $pluginname);
            $pluginimporter = new preset_existing_importer($manager, '/' . $pluginname);
            $this->assertEquals(count($pluginimporter->fieldstoremove), $fieldstoremove);
            $this->assertEquals(count($pluginimporter->fieldstocreate), $fieldstocreate);
            $this->assertEquals(count($pluginimporter->fieldstoupdate), $fieldstoupdate);
        }
    }

    /**
     * Test for get_mapping_information method.
     *
     * @dataProvider set_affected_provider
     * @covers ::get_mapping_information
     *
     * @param array $currentfields Fields of the current activity.
     * @param array $newfields Fields to be imported.
     * @param string $pluginname The plugin preset to be imported.
     * @param int $fieldstocreate Expected number of fields on $fieldstocreate.
     * @param int $fieldstoremove Expected number of fields on $fieldstoremove.
     * @param int $fieldstoupdate Expected number of fields on $fieldstoupdate.
     */
    public function test_get_mapping_information(
        array $currentfields,
        array $newfields,
        string $pluginname,
        int $fieldstocreate,
        int $fieldstoremove,
        int $fieldstoupdate
    ): void {
        global $USER;

        $this->resetAfterTest();
        $this->setAdminUser();
        $plugingenerator = $this->getDataGenerator()->get_plugin_generator('mod_data');

        // Create a course and a database activity.
        $course = $this->getDataGenerator()->create_course();
        $activity = $this->getDataGenerator()->create_module(manager::MODULE, ['course' => $course]);
        // Add current fields to the activity.
        foreach ($currentfields as $field) {
            $plugingenerator->create_field($field, $activity);
        }
        $manager = manager::create_from_instance($activity);

        $presetactivity = $this->getDataGenerator()->create_module(manager::MODULE, ['course' => $course]);
        // Add current fields to the activity.
        foreach ($newfields as $field) {
            $plugingenerator->create_field($field, $presetactivity);
        }

        $record = (object) [
            'name' => 'Testing preset name',
            'description' => 'Testing preset description',
        ];
        $saved = $plugingenerator->create_preset($presetactivity, $record);
        $savedimporter = new preset_existing_importer($manager, $USER->id . '/Testing preset name');
        $information = $savedimporter->get_mapping_information();
        $this->assertEquals($savedimporter->needs_mapping(), $information['needsmapping']);
        $this->assertEquals(count($savedimporter->fieldstoremove), $fieldstoremove);
        $this->assertEquals(count($savedimporter->fieldstocreate), $fieldstocreate);
        $this->assertEquals(count($savedimporter->fieldstoupdate), $fieldstoupdate);

        // Create presets and importers.
        if ($pluginname) {
            $plugin = preset::create_from_plugin(null, $pluginname);
            $pluginimporter = new preset_existing_importer($manager, '/' . $pluginname);
            $information = $pluginimporter->get_mapping_information();
            $this->assertEquals($pluginimporter->needs_mapping(), $information['needsmapping']);
            $this->assertEquals(count($pluginimporter->fieldstoremove), $fieldstoremove);
            $this->assertEquals(count($pluginimporter->fieldstocreate), $fieldstocreate);
            $this->assertEquals(count($pluginimporter->fieldstoupdate), $fieldstoupdate);
        }
    }

    /**
     * Data provider for get_field_names().
     *
     * @return array[]
     */
    public static function get_field_names_provider(): array {
        return [
            'Empty list' => [
                'fields' => [],
                'expected' => '',
            ],
            'List with one field' => [
                'fields' => ['fieldname' => 'text'],
                'expected' => 'fieldname',
            ],
            'List of fields with same type' => [
                'fields' => ['textfield' => 'text', 'other' => 'text'],
                'expected' => 'textfield, other',
            ],
            'List of fields with different type' => [
                'fields' => ['textfield' => 'text', 'number' => 'number'],
                'expected' => 'textfield, number',
            ],
        ];
    }

    /**
     * Test for get_field_names method.
     *
     * @dataProvider get_field_names_provider
     * @covers ::get_field_names
     *
     * @param array $fields List of fields to get the names from.
     * @param string $expected The list of field names expected.
     */
    public function test_get_field_names(array $fields, string $expected): void {
        global $USER;

        $this->resetAfterTest();
        $this->setAdminUser();
        $plugingenerator = $this->getDataGenerator()->get_plugin_generator('mod_data');

        // Create a course and a database activity.
        $course = $this->getDataGenerator()->create_course();
        $presetactivity = $this->getDataGenerator()->create_module(manager::MODULE, ['course' => $course]);
        foreach ($fields as $fieldname => $fieldtype) {
            $newfield = new \stdClass();
            $newfield->name = $fieldname;
            $newfield->type = $fieldtype;

            $createdfield = $plugingenerator->create_field($newfield, $presetactivity);
        }
        $manager = manager::create_from_instance($presetactivity);

        $record = (object) [
            'name' => 'Testing preset name',
            'description' => 'Testing preset description',
        ];
        $saved = $plugingenerator->create_preset($presetactivity, $record);
        $savedimporter = new preset_existing_importer($manager, $USER->id . '/Testing preset name');
        $this->assertEquals($expected, $savedimporter->get_field_names($manager->get_field_records()));
    }

    /**
     * Test for create_from_plugin_or_directory creation static method.
     *
     * @covers ::create_from_plugin_or_directory
     *
     */
    public function test_create_from_plugin_or_directory(): void {

        global $USER;

        $this->resetAfterTest();
        $this->setAdminUser();
        $plugingenerator = $this->getDataGenerator()->get_plugin_generator('mod_data');

        // Create a course and a database activity.
        $course = $this->getDataGenerator()->create_course();
        $activity = $this->getDataGenerator()->create_module(manager::MODULE, ['course' => $course]);
        $manager = manager::create_from_instance($activity);

        $presetactivity = $this->getDataGenerator()->create_module(manager::MODULE, ['course' => $course]);

        $record = (object) [
            'name' => 'Testing preset name',
            'description' => 'Testing preset description',
        ];
        $saved = $plugingenerator->create_preset($presetactivity, $record);

        // A plugin preset returns an instance of preset_existing_importer.
        $preset = preset_importer::create_from_plugin_or_directory($manager, '/imagegallery');
        $this->assertInstanceOf('\mod_data\local\importer\preset_existing_importer', $preset);

        // A saved preset returns an instance of preset_existing_importer.
        $preset = preset_importer::create_from_plugin_or_directory($manager, $USER->id . '/Testing preset name');
        $this->assertInstanceOf('\mod_data\local\importer\preset_existing_importer', $preset);

        // An empty preset name throws an exception.
        $this->expectException('moodle_exception');
        try {
            preset_importer::create_from_plugin_or_directory($manager, '');
        } finally {
            // A non-existing preset name throws an exception.
            $this->expectException('moodle_exception');
            preset_importer::create_from_plugin_or_directory($manager, $USER->id . '/Non-existing');
        }
    }
}
