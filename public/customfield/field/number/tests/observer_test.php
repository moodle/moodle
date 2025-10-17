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

namespace customfield_number;

use context_module;
use customfield_number\local\numberproviders\nofactivities;
use customfield_number\task\recalculate;

/**
 * Testing event observers
 *
 * @covers     \customfield_number\observer
 * @covers     \customfield_number\task\recalculate
 * @package    customfield_number
 * @category   test
 * @copyright  Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class observer_test extends \advanced_testcase {

    /**
     * Create a number custom field
     *
     * @param array $configdata
     * @return \customfield_number\field_controller
     */
    protected function create_number_custom_field(array $configdata): field_controller {
        /** @var \core_customfield_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_customfield');

        // Create a category and field.
        $category = $generator->create_category();
        $field = $generator->create_field([
            'categoryid' => $category->get('id'),
            'type' => 'number',
            'configdata' => $configdata,
        ]);
        return $field;
    }

    /**
     * Helper function that checks if the recalculate ad-hoc task is scheduled
     *
     * @param bool $mustbescheduled - when false checks that the adhoc task is NOT scheduled
     * @return void
     */
    protected function ensure_number_adhoc_task_is_scheduled(bool $mustbescheduled): void {
        $tasks = array_filter(
            \core\task\manager::get_candidate_adhoc_tasks(time(), 1200, null),
            fn($task) => $task->classname === '\\' . recalculate::class
        );
        if ($mustbescheduled && empty($tasks)) {
            $this->fail('Recalculate ad-hoc task is not scheduled.');
        } else if (!$mustbescheduled && !empty($tasks)) {
            $this->fail('Recalculate ad-hoc task is scheduled when it is not expected.');
        }
    }

    /**
     * Test for observer for field_created event
     */
    public function test_field_created(): void {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $assigngenerator = $this->getDataGenerator()->get_plugin_generator('mod_assign');
        $assign1 = $assigngenerator->create_instance(['course' => $course1->id, 'visible' => 1]);
        $assigngenerator->create_instance(['course' => $course1->id, 'visible' => 1]);

        // Create a number field with a provider.
        $field = $this->create_number_custom_field(['fieldtype' => nofactivities::class, 'activitytypes' => ['assign', 'forum']]);

        // Execute scheduled ad-hoc tasks and it will populate the data for the course.
        $this->ensure_number_adhoc_task_is_scheduled(true);
        $this->run_all_adhoc_tasks();

        $alldata = $DB->get_records_menu('customfield_data',
            ['fieldid' => $field->get('id')], 'instanceid', 'instanceid, decvalue');
        $this->assertEquals([$course1->id => 2], $alldata);

        // Creating another field type does not schedule tasks.
        $this->ensure_number_adhoc_task_is_scheduled(false);
        $this->getDataGenerator()->get_plugin_generator('core_customfield')->create_field((object)[
            'categoryid' => $field->get_category()->get('id'),
            'type' => 'textarea',
        ]);
        $this->ensure_number_adhoc_task_is_scheduled(false);
    }

    /**
     * Test for observer for field_updated event
     */
    public function test_field_updated(): void {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        /** @var \mod_assign_generator $assigngenerator */
        $assigngenerator = $this->getDataGenerator()->get_plugin_generator('mod_assign');
        $assign1 = $assigngenerator->create_instance(['course' => $course1->id, 'visible' => 1]);
        $assigngenerator->create_instance(['course' => $course1->id, 'visible' => 1]);

        // Create a simple number field.
        $field = $this->create_number_custom_field([]);

        // There is no data for this field yet.
        $this->ensure_number_adhoc_task_is_scheduled(true);
        $this->run_all_adhoc_tasks();
        $this->assertEmpty($DB->get_records('customfield_data', ['fieldid' => $field->get('id')]));

        // Update this field to use nofactivities as provider.
        $params = ['fieldtype' => nofactivities::class, 'activitytypes' => ['assign', 'forum']];
        \core_customfield\api::save_field_configuration($field, (object)['configdata' => $params]);

        // Now an ad-hoc task is scheduled and the data is populated.
        $this->ensure_number_adhoc_task_is_scheduled(true);
        $this->run_all_adhoc_tasks();

        $alldata = $DB->get_records_menu(
            'customfield_data',
            ['fieldid' => $field->get('id')],
            'instanceid',
            'instanceid, decvalue'
        );
        $this->assertEquals([$course1->id => 2], $alldata);
    }

    /**
     * Test for observer for course_module_created, course_module_updated and course_module_deleted events
     */
    public function test_course_module_events(): void {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();

        // Create a number field with a provider.
        $field = $this->create_number_custom_field(['fieldtype' => nofactivities::class, 'activitytypes' => ['assign', 'forum']]);

        // There is no data for this field yet.
        $this->ensure_number_adhoc_task_is_scheduled(true);
        $this->run_all_adhoc_tasks();
        $this->assertEmpty($DB->get_records('customfield_data', ['fieldid' => $field->get('id')]));

        // Create modules.
        $assigngenerator = $this->getDataGenerator()->get_plugin_generator('mod_assign');
        $assign1 = $assigngenerator->create_instance(['course' => $course1->id, 'visible' => 1]);
        $assign2 = $assigngenerator->create_instance(['course' => $course1->id, 'visible' => 0]);
        $assigngenerator->create_instance(['course' => $course1->id, 'visible' => 1]);

        // Execute scheduled ad-hoc tasks and it will populate the data for the course.
        $this->ensure_number_adhoc_task_is_scheduled(true);
        $this->run_all_adhoc_tasks();

        $alldata = $DB->get_records_menu('customfield_data',
            ['fieldid' => $field->get('id')], 'instanceid', 'instanceid, decvalue');
        $this->assertEquals([$course1->id => 2], $alldata);

        // Update visibility of one module.
        set_coursemodule_visible($assign2->cmid, 1);
        [$course, $cm] = get_course_and_cm_from_cmid($assign2->cmid);
        \core\event\course_module_updated::create_from_cm($cm, context_module::instance($assign2->cmid))->trigger();
        $this->ensure_number_adhoc_task_is_scheduled(true);
        $this->run_all_adhoc_tasks();

        $alldata = $DB->get_records_menu('customfield_data',
            ['fieldid' => $field->get('id')], 'instanceid', 'instanceid, decvalue');
        $this->assertEquals([$course1->id => 3], $alldata);

        // Delete one module.
        \core_courseformat\formatactions::cm($course1->id)->delete($assign1->cmid);

        // Execute scheduled ad-hoc tasks and it will update the data for the course.
        $this->ensure_number_adhoc_task_is_scheduled(true);
        $this->run_all_adhoc_tasks();

        $alldata = $DB->get_records_menu('customfield_data',
            ['fieldid' => $field->get('id')], 'instanceid', 'instanceid, decvalue');
        $this->assertEquals([$course1->id => 2], $alldata);
    }

    /**
     * Creating, updating and deleting modules when there are no 'nofactivities' custom fields does not schedule the ad-hoc task
     *
     * @return void
     */
    public function test_course_module_events_without_custom_fields(): void {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();

        // Create a number field without a provider.
        $field = $this->create_number_custom_field([]);

        // Initial ad-hoc task was scheduled.
        $this->ensure_number_adhoc_task_is_scheduled(true);
        $this->run_all_adhoc_tasks();

        // Create modules.
        $assigngenerator = $this->getDataGenerator()->get_plugin_generator('mod_assign');
        $assign1 = $assigngenerator->create_instance(['course' => $course1->id, 'visible' => 1]);
        $assign2 = $assigngenerator->create_instance(['course' => $course1->id, 'visible' => 0]);
        $assigngenerator->create_instance(['course' => $course1->id, 'visible' => 1]);

        $this->ensure_number_adhoc_task_is_scheduled(false);

        // Update visibility of one module.
        set_coursemodule_visible($assign2->cmid, 1);
        [$course, $cm] = get_course_and_cm_from_cmid($assign2->cmid);
        \core\event\course_module_updated::create_from_cm($cm, context_module::instance($assign2->cmid))->trigger();
        $this->ensure_number_adhoc_task_is_scheduled(false);

        // Delete one module.
        \core_courseformat\formatactions::cm($course1->id)->delete($assign1->cmid);
        $this->ensure_number_adhoc_task_is_scheduled(false);
    }
}
