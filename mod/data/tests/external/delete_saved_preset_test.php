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

use externallib_advanced_testcase;
use core_external\external_api;
use mod_data\manager;

/**
 * External function test for delete_saved_preset.
 *
 * @package    mod_data
 * @category   external
 * @since      Moodle 4.1
 * @copyright  2022 Amaia Anabitarte <amaia@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \mod_data\external\delete_saved_preset
 */
class delete_saved_preset_test extends externallib_advanced_testcase {

    /**
     * Test the behaviour of delete_saved_preset().
     *
     * @covers ::execute
     */
    public function test_delete_saved_preset() {
        $this->resetAfterTest();

        // Create course, database activity and users.
        $course = $this->getDataGenerator()->create_course();
        $data = $this->getDataGenerator()->create_module('data', ['course' => $course->id]);
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $manager = manager::create_from_instance($data);
        $initialpresets = $manager->get_available_presets();

        $preset1name = 'Admin preset';
        $preset2name = 'Teacher preset';

        // Trying to delete a preset when there is no saved preset created.
        $result = delete_saved_preset::execute($data->id, [$preset1name, $preset2name]);
        $result = external_api::clean_returnvalue(delete_saved_preset::execute_returns(), $result);
        $this->assertFalse($result['result']);
        $this->assertCount(2, $result['warnings']);
        // Check no preset has been deleted.
        $currentpresets = $manager->get_available_presets();
        $this->assertEquals(count($initialpresets), count($currentpresets));

        // Create a saved preset.
        $plugingenerator = $this->getDataGenerator()->get_plugin_generator('mod_data');
        $record = (object)[
            'name' => $preset1name,
            'description' => 'Testing preset description',
        ];
        $adminpreset = $plugingenerator->create_preset($data, $record);
        // Update initial preset list.
        $initialpresets = $manager->get_available_presets();

        // There is a warning for non-existing preset.
        $result = delete_saved_preset::execute($data->id, ['Another preset']);
        $result = external_api::clean_returnvalue(delete_saved_preset::execute_returns(), $result);
        $this->assertFalse($result['result']);
        $this->assertCount(1, $result['warnings']);
        // Check no preset has been deleted.
        $currentpresets = $manager->get_available_presets();
        $this->assertEquals(count($initialpresets), count($currentpresets));

        // Create a saved preset by teacher.
        $this->setUser($teacher);
        $plugingenerator = $this->getDataGenerator()->get_plugin_generator('mod_data');
        $record = (object)[
            'name' => $preset2name,
            'description' => 'Testing preset description',
        ];
        $teacherpreset = $plugingenerator->create_preset($data, $record);
        // Update initial preset list.
        $this->setAdminUser();
        $initialpresets = $manager->get_available_presets();

        // Student can't delete presets.
        $this->setUser($student);
        $result = delete_saved_preset::execute($data->id, [$preset1name, $preset2name]);
        $result = external_api::clean_returnvalue(delete_saved_preset::execute_returns(), $result);
        $this->assertFalse($result['result']);
        $this->assertCount(2, $result['warnings']);
        // Check no preset has been deleted.
        $this->setAdminUser();
        $currentpresets = $manager->get_available_presets();
        $this->assertEquals(count($initialpresets), count($currentpresets));

        // Teacher can delete their preset.
        $this->setUser($teacher);
        $result = delete_saved_preset::execute($data->id, [$preset2name]);
        $result = external_api::clean_returnvalue(delete_saved_preset::execute_returns(), $result);
        $this->assertTrue($result['result']);
        $this->assertCount(0, $result['warnings']);
        // Check the preset has been deleted.
        $this->setAdminUser();
        $currentpresets = $manager->get_available_presets();
        $this->assertEquals(count($initialpresets) - 1, count($currentpresets));
        foreach ($currentpresets as $currentpreset) {
            $this->assertNotEquals($currentpreset->name, $preset2name);
        }

        // Teacher can't delete other users' preset.
        $this->setUser($teacher);
        $result = delete_saved_preset::execute($data->id, [$preset1name]);
        $result = external_api::clean_returnvalue(delete_saved_preset::execute_returns(), $result);
        $this->assertFalse($result['result']);
        $this->assertCount(1, $result['warnings']);
        // Check no preset has been deleted.
        $this->setAdminUser();
        $currentpresets = $manager->get_available_presets();
        $this->assertEquals(count($initialpresets) - 1, count($currentpresets));
    }
}
