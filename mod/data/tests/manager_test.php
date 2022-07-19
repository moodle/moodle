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
}
