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

use context_module;
use moodle_url;
use core_component;

/**
 * Manager tests class for mod_data.
 *
 * @package    mod_data
 * @category   test
 * @copyright  2022 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \mod_data\manager
 */
class manager_test extends \advanced_testcase {

    /**
     * Test for static create methods.
     *
     * @covers ::create_from_instance
     * @covers ::create_from_coursemodule
     * @covers ::create_from_data_record
     */
    public function test_create() {

        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $activity = $this->getDataGenerator()->create_module(manager::MODULE, ['course' => $course]);
        $cm = get_coursemodule_from_id(manager::MODULE, $activity->cmid, 0, false, MUST_EXIST);
        $context = context_module::instance($cm->id);

        $manager = manager::create_from_instance($activity);
        $manageractivity = $manager->get_instance();
        $this->assertEquals($activity->id, $manageractivity->id);
        $managercm = $manager->get_coursemodule();
        $this->assertEquals($cm->id, $managercm->id);
        $managercontext = $manager->get_context();
        $this->assertEquals($context->id, $managercontext->id);

        $manager = manager::create_from_coursemodule($cm);
        $manageractivity = $manager->get_instance();
        $this->assertEquals($activity->id, $manageractivity->id);
        $managercm = $manager->get_coursemodule();
        $this->assertEquals($cm->id, $managercm->id);
        $managercontext = $manager->get_context();
        $this->assertEquals($context->id, $managercontext->id);

        $datarecord = (object)[
            'dataid' => $activity->id,
            'id' => 0,
            'userid' => 0,
            'groupid' => 0,
            'timecreated' => 0,
            'timemodified' => 0,
            'approved' => 0,
        ];
        $manager = manager::create_from_data_record($datarecord);
        $manageractivity = $manager->get_instance();
        $this->assertEquals($activity->id, $manageractivity->id);
        $managercm = $manager->get_coursemodule();
        $this->assertEquals($cm->id, $managercm->id);
        $managercontext = $manager->get_context();
        $this->assertEquals($context->id, $managercontext->id);
    }

    /**
     * Test set_module_viewed
     * @covers ::set_module_viewed
     */
    public function test_set_module_viewed() {
        global $CFG;

        $CFG->enablecompletion = 1;
        $this->resetAfterTest();
        $this->setAdminUser();

        // Setup test data.
        $course = $this->getDataGenerator()->create_course(['enablecompletion' => 1]);
        $instance = $this->getDataGenerator()->create_module(
            'data',
            ['course' => $course->id],
            ['completion' => 2, 'completionview' => 1]
        );
        $manager = manager::create_from_instance($instance);
        $context = $manager->get_context();
        $cm = $manager->get_coursemodule();

        // Trigger and capture the event.
        $sink = $this->redirectEvents();

        $manager->set_module_viewed($course);

        $events = $sink->get_events();
        // 2 additional events thanks to completion.
        $this->assertCount(3, $events);
        $event = array_shift($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_data\event\course_module_viewed', $event);
        $this->assertEquals($context, $event->get_context());
        $moodleurl = new moodle_url('/mod/data/view.php', ['id' => $cm->id]);
        $this->assertEquals($moodleurl, $event->get_url());
        $this->assertEventContextNotUsed($event);
        $this->assertNotEmpty($event->get_name());

        // Check completion status.
        $completion = new \completion_info($course);
        $completiondata = $completion->get_data($cm);
        $this->assertEquals(1, $completiondata->completionstate);
    }

    /**
     * Test set_template_viewed
     * @covers ::set_template_viewed
     */
    public function test_set_template_viewed() {
        $this->resetAfterTest();
        $this->setAdminUser();

        // Setup test data.
        $course = $this->getDataGenerator()->create_course();
        $instance = $this->getDataGenerator()->create_module(
            'data',
            ['course' => $course->id]
        );
        $manager = manager::create_from_instance($instance);
        $context = $manager->get_context();

        // Trigger and capture the event.
        $sink = $this->redirectEvents();

        $manager->set_template_viewed();

        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('mod_data\event\template_viewed', $event);
        $this->assertEquals($context, $event->get_context());
        $moodleurl = new moodle_url('/mod/data/templates.php', ['d' => $instance->id]);
        $this->assertEquals($moodleurl, $event->get_url());
        $this->assertEventContextNotUsed($event);
        $this->assertNotEmpty($event->get_name());
    }

    /**
     * Test for has_fields().
     *
     * @covers ::has_fields
     */
    public function test_has_fields() {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $activity = $this->getDataGenerator()->create_module(manager::MODULE, ['course' => $course]);
        $manager = manager::create_from_instance($activity);

        // Empty database should return false.
        $this->assertFalse($manager->has_fields());

        // Add a field to the activity.
        $datagenerator = $this->getDataGenerator()->get_plugin_generator('mod_data');
        $fieldrecord = new \stdClass();
        $fieldrecord->name = 'field1';
        $fieldrecord->type = 'text';
        $datagenerator->create_field($fieldrecord, $activity);

        // Database with fields should return true.
        $this->assertTrue($manager->has_fields());
    }

    /**
     * Test for get_available_presets().
     *
     * @covers ::get_available_presets
     */
    public function test_get_available_presets() {
        global $DB;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $this->setUser($user);

        $activity = $this->getDataGenerator()->create_module(manager::MODULE, ['course' => $course]);
        $cm = get_coursemodule_from_id(manager::MODULE, $activity->cmid, 0, false, MUST_EXIST);

        // Check available presets meet the datapreset plugins when there are no any preset saved by users.
        $datapresetplugins = core_component::get_plugin_list('datapreset');
        $manager = manager::create_from_coursemodule($cm);
        $presets = $manager->get_available_presets();
        $this->assertCount(count($datapresetplugins), $presets);
        // Confirm that, at least, the "Image gallery" is one of them.
        $namepresets = array_map(function($preset) {
            return $preset->name;
        }, $presets);
        $this->assertContains('Image gallery', $namepresets);

        // Login as admin and create some presets saved manually by users.
        $this->setAdminUser();
        $plugingenerator = $this->getDataGenerator()->get_plugin_generator('mod_data');
        $savedpresets = [];
        for ($i = 1; $i <= 3; $i++) {
            $preset = (object) [
                'name' => 'Preset name ' . $i,
            ];
            $plugingenerator->create_preset($activity, $preset);
            $savedpresets[] = $preset;
        }
        $savedpresetsnames = array_map(function($preset) {
            return $preset->name;
        }, $savedpresets);
        $this->setUser($user);

        // Check available presets meet the datapreset plugins + presets saved manually by users.
        $presets = $manager->get_available_presets();
        $this->assertCount(count($datapresetplugins) + count($savedpresets), $presets);
        // Confirm that, apart from the "Image gallery" preset, the ones created manually have been also returned.
        $namepresets = array_map(function($preset) {
            return $preset->name;
        }, $presets);
        $this->assertContains('Image gallery', $namepresets);
        foreach ($savedpresets as $savedpreset) {
            $this->assertContains($savedpreset->name, $namepresets);
        }
        // Check all the presets have the proper value for the isplugin attribute.
        foreach ($presets as $preset) {
            if (in_array($preset->name, $savedpresetsnames)) {
                $this->assertFalse($preset->isplugin);
            } else {
                $this->assertTrue($preset->isplugin);
            }
        }

        // Unassign the capability to the teacher role and check that only plugin presets are returned (because the saved presets
        // have been created by admin).
        $teacherrole = $DB->get_record('role', ['shortname' => 'teacher']);
        unassign_capability('mod/data:viewalluserpresets', $teacherrole->id);
        $presets = $manager->get_available_presets();
        $this->assertCount(count($datapresetplugins), $presets);
        // Confirm that, at least, the "Image gallery" is one of them.
        $namepresets = array_map(function($preset) {
            return $preset->name;
        }, $presets);
        $this->assertContains('Image gallery', $namepresets);
        foreach ($savedpresets as $savedpreset) {
            $this->assertNotContains($savedpreset->name, $namepresets);
        }

        // Create a preset with the current user and check that, although the viewalluserpresets is not assigned to the teacher
        // role, the preset is returned because the teacher is the owner.
        $savedpreset = (object) [
            'name' => 'Preset created by teacher',
        ];
        $plugingenerator->create_preset($activity, $savedpreset);
        $presets = $manager->get_available_presets();
        // The presets total is all the plugin presets plus the preset created by the teacher.
        $this->assertCount(count($datapresetplugins) + 1, $presets);
        // Confirm that, at least, the "Image gallery" is one of them.
        $namepresets = array_map(function($preset) {
            return $preset->name;
        }, $presets);
        $this->assertContains('Image gallery', $namepresets);
        // Confirm that savedpresets are still not returned.
        foreach ($savedpresets as $savedpreset) {
            $this->assertNotContains($savedpreset->name, $namepresets);
        }
        // Confirm the new preset created by the teacher is returned too.
        $this->assertContains('Preset created by teacher', $namepresets);
    }

    /**
     * Test for get_available_plugin_presets().
     *
     * @covers ::get_available_plugin_presets
     */
    public function test_get_available_plugin_presets() {
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $activity = $this->getDataGenerator()->create_module(manager::MODULE, ['course' => $course]);

        // Check available plugin presets meet the datapreset plugins.
        $datapresetplugins = core_component::get_plugin_list('datapreset');
        $manager = manager::create_from_instance($activity);
        $presets = $manager->get_available_plugin_presets();
        $this->assertCount(count($datapresetplugins), $presets);
        // Confirm that, at least, the "Image gallery" is one of them.
        $namepresets = array_map(function($preset) {
            return $preset->name;
        }, $presets);
        $this->assertContains('Image gallery', $namepresets);

        // Create a preset saved manually by users.
        $savedpreset = (object) [
            'name' => 'Preset name 1',
        ];
        $plugingenerator = $this->getDataGenerator()->get_plugin_generator('mod_data');
        $plugingenerator->create_preset($activity, $savedpreset);

        // Check available plugin presets don't contain the preset saved manually.
        $presets = $manager->get_available_plugin_presets();
        $this->assertCount(count($datapresetplugins), $presets);
        // Confirm that, at least, the "Image gallery" is one of them.
        $namepresets = array_map(function($preset) {
            return $preset->name;
        }, $presets);
        $this->assertContains('Image gallery', $namepresets);
        // Confirm that the preset saved manually hasn't been returned.
        $this->assertNotContains($savedpreset->name, $namepresets);
        // Check all the presets have the proper value for the isplugin attribute.
        foreach ($presets as $preset) {
            $this->assertTrue($preset->isplugin);
        }
    }

    /**
     * Test for get_available_saved_presets().
     *
     * @covers ::get_available_saved_presets
     */
    public function test_get_available_saved_presets() {
        global $DB;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $this->setUser($user);

        $activity = $this->getDataGenerator()->create_module(manager::MODULE, ['course' => $course]);
        $cm = get_coursemodule_from_id(manager::MODULE, $activity->cmid, 0, false, MUST_EXIST);

        // Check available saved presets is empty (because, for now, no user preset has been created).
        $manager = manager::create_from_coursemodule($cm);
        $presets = $manager->get_available_saved_presets();
        $this->assertCount(0, $presets);

        // Create some presets saved manually by the admin user.
        $this->setAdminUser();
        $plugingenerator = $this->getDataGenerator()->get_plugin_generator('mod_data');
        $savedpresets = [];
        for ($i = 1; $i <= 3; $i++) {
            $preset = (object) [
                'name' => 'Preset name ' . $i,
            ];
            $plugingenerator->create_preset($activity, $preset);
            $savedpresets[] = $preset;
        }
        // Create one more preset saved manually by the teacher user.
        $this->setUser($user);
        $teacherpreset = (object) [
            'name' => 'Preset created by teacher',
        ];
        $plugingenerator->create_preset($activity, $teacherpreset);
        $savedpresets[] = $teacherpreset;

        $savedpresetsnames = array_map(function($preset) {
            return $preset->name;
        }, $savedpresets);

        // Check available saved presets only contain presets saved manually by users.
        $presets = $manager->get_available_saved_presets();
        $this->assertCount(count($savedpresets), $presets);
        // Confirm that it contains only the presets created manually.
        foreach ($presets as $preset) {
            $this->assertContains($preset->name, $savedpresetsnames);
            $this->assertFalse($preset->isplugin);
        }

        // Unassign the mod/data:viewalluserpresets capability to the teacher role and check that saved presets are not returned.
        $teacherrole = $DB->get_record('role', ['shortname' => 'teacher']);
        unassign_capability('mod/data:viewalluserpresets', $teacherrole->id);

        $presets = $manager->get_available_saved_presets();
        $this->assertCount(1, $presets);
        $preset = reset($presets);
        $this->assertEquals($teacherpreset->name, $preset->name);
    }
}
