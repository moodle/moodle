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

namespace mod_data\external;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/webservice/tests/helpers.php');

use external_api;
use mod_data\manager;

/**
 * External function tests class for get_mapping_information.
 *
 * @package    mod_data
 * @category   external
 * @copyright  2022 Amaia Anabitarte <amaia@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \mod_data\external\get_mapping_information
 */
class get_mapping_information_test extends \advanced_testcase {

    /**
     * Data provider for test_get_mapping_information().
     *
     * @return array[]
     */
    public function get_mapping_information_provider(): array {
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
                'fieldstocreate' => '',
                'fieldstoremove' => '',
            ],
            'Empty database / Importer with fields' => [
                'currentfields' => [],
                'newfields' => [$imagefield, $titlefield, $descfield],
                'pluginname' => 'imagegallery',
                'fieldstocreate' => 'image, title, description',
                'fieldstoremove' => '',
            ],
            'Database with fields / Empty importer' => [
                'currentfields' => [$imagefield, $titlefield, $descfield],
                'newfields' => [],
                'pluginname' => '',
                'fieldstocreate' => '',
                'fieldstoremove' => 'image, title, description',
            ],
            'Same fields' => [
                'currentfields' => [$imagefield, $titlefield, $descfield],
                'newfields' => [$imagefield, $titlefield, $descfield],
                'pluginname' => 'imagegallery',
                'fieldstocreate' => '',
                'fieldstoremove' => '',
            ],
            'Fields to create' => [
                'currentfields' => [$titlefield, $descfield],
                'newfields' => [$imagefield, $titlefield, $descfield],
                'pluginname' => 'imagegallery',
                'fieldstocreate' => 'image',
                'fieldstoremove' => '',
            ],
            'Fields to remove' => [
                'currentfields' => [$imagefield, $titlefield, $descfield, $difffield],
                'newfields' => [$imagefield, $titlefield, $descfield],
                'pluginname' => 'imagegallery',
                'fieldstocreate' => '',
                'fieldstoremove' => 'title',
            ],
            'Fields to update' => [
                'currentfields' => [$imagefield, $difffield, $descfield],
                'newfields' => [$imagefield, $titlefield, $descfield],
                'pluginname' => 'imagegallery',
                'fieldstocreate' => 'title',
                'fieldstoremove' => 'title',
            ],
            'Fields to create, remove and update' => [
                'currentfields' => [$titlefield, $descfield, $imagefield, $difffield],
                'newfields' => [$titlefield, $descfield, $newfield],
                'pluginname' => '',
                'fieldstocreate' => 'number',
                'fieldstoremove' => 'image, title',
            ],
        ];
    }

    /**
     * Test for get_mapping_information method.
     *
     * @dataProvider get_mapping_information_provider
     * @covers ::execute
     *
     * @param array $currentfields Fields of the current activity.
     * @param array $newfields Fields to be imported.
     * @param string $pluginname The plugin preset to be imported.
     * @param string $fieldstocreate Expected fields on $fieldstocreate.
     * @param string $fieldstoremove Expected fields on $fieldstoremove.
     */
    public function test_get_mapping_information(
        array $currentfields,
        array $newfields,
        string $pluginname,
        string $fieldstocreate,
        string $fieldstoremove
    ) {
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
        $module = $manager->get_coursemodule();

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

        $result = get_mapping_information::execute($module->id, $USER->id . '/' . $saved->name);
        $result = external_api::clean_returnvalue(get_mapping_information::execute_returns(), $result);

        $this->assertEquals($result['data']['fieldstocreate'], $fieldstocreate);
        $this->assertEquals($result['data']['fieldstoremove'], $fieldstoremove);

        // Create presets and importers.
        if ($pluginname) {
            $result = get_mapping_information::execute($module->id, '/' . $pluginname);;
            $result = external_api::clean_returnvalue(get_mapping_information::execute_returns(), $result);
            $this->assertEquals($result['data']['fieldstoremove'], $fieldstoremove);
            $this->assertEquals($result['data']['fieldstocreate'], $fieldstocreate);
        }
    }

    /**
     * Test for get_mapping_information method for wrong presets.
     *
     * @covers ::execute
     *
     */
    public function test_get_mapping_information_for_wrong_preset() {
        global $USER;

        $this->resetAfterTest();
        $this->setAdminUser();
        $plugingenerator = $this->getDataGenerator()->get_plugin_generator('mod_data');

        // Create a course and a database activity.
        $course = $this->getDataGenerator()->create_course();
        $activity = $this->getDataGenerator()->create_module(manager::MODULE, ['course' => $course]);

        $manager = manager::create_from_instance($activity);
        $module = $manager->get_coursemodule();

        // We get warnings with empty preset name.
        $result = get_mapping_information::execute($module->id, '');
        $result = external_api::clean_returnvalue(get_mapping_information::execute_returns(), $result);

        $this->assertFalse(array_key_exists('data', $result));
        $this->assertTrue(array_key_exists('warnings', $result));

        // We get warnings with non-existing preset name.
        $result = get_mapping_information::execute($module->id, $USER->id . '/Non-existing');
        $result = external_api::clean_returnvalue(get_mapping_information::execute_returns(), $result);

        $this->assertFalse(array_key_exists('data', $result));
        $this->assertTrue(array_key_exists('warnings', $result));

        $record = (object) [
            'name' => 'Testing preset name',
            'description' => 'Testing preset description',
        ];
        $saved = $plugingenerator->create_preset($activity, $record);

        // We get no warning with the right preset.
        $result = get_mapping_information::execute($module->id, $USER->id . '/' . $saved->name);
        $result = external_api::clean_returnvalue(get_mapping_information::execute_returns(), $result);

        $this->assertTrue(array_key_exists('data', $result));
        $this->assertFalse(array_key_exists('warnings', $result));
    }
}
