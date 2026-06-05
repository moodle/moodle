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

declare(strict_types=1);

namespace customfield_number\task;

use core_customfield\api;
use core_customfield\external\toggle_shared_category;
use customfield_number\test_provider;

/**
 * Test the recalculate adhoc task.
 *
 * @package    customfield_number
 * @covers     \customfield_number\task\recalculate
 * @copyright  2026 Yerai Rodríguez <yerai.rodriguez@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class recalculate_test extends \advanced_testcase {
    /**
     * Test that schedule_for_fieldtype recalculates shared fields when scheduled with a specific component/area.

     * A shared custom field (handler core_customfield/shared) enabled for courses should be recalculated
     * when the adhoc task is scheduled with component='core_course' and area='course'.
     *
     * @return void
     */
    public function test_schedule_for_fieldtype_with_shared_field(): void {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        $this->load_fixture('customfield_number', 'test_provider.php');

        $category = $this->getDataGenerator()->create_custom_field_category([
            'component' => 'core_customfield',
            'area' => 'shared',
        ]);

        toggle_shared_category::execute($category->get('id'), 'core_course', 'course', 0, true);

        $sharedfield = $this->getDataGenerator()->create_custom_field([
            'categoryid' => $category->get('id'),
            'shortname' => 'seconds',
            'type' => 'number',
            'configdata' => [
                'fieldtype' => test_provider::class,
            ],
        ]);

        $clock = $this->mock_clock_with_frozen();
        $fieldid = $sharedfield->get('id');
        $fields = [$fieldid => $sharedfield];

        $course = $this->getDataGenerator()->create_course();
        $courseid = (int)$course->id;

        // Discard tasks and data created during setup to isolate the explicitly scheduled task below.
        $DB->delete_records('task_adhoc');
        $DB->delete_records('customfield_data');

        // Confirm current value is null.
        $data = api::get_instance_fields_data($fields, $courseid, true, 'core_course', 'course');
        $this->assertNull($data[$fieldid]->get_value());

        // Schedule recalculation with core_course/course component/area for a shared field.
        recalculate::schedule_for_fieldtype(
            fieldtype: test_provider::class,
            component: 'core_course',
            area: 'course',
        );
        $this->run_all_adhoc_tasks();

        // The shared field should have been recalculated for the course.
        $data = api::get_instance_fields_data($fields, $courseid, true, 'core_course', 'course');
        $this->assertEquals($clock->time() % 3600, $data[$fieldid]->get_value());

        // Data should not exist in the shared context.
        $data = api::get_instance_fields_data($fields, $courseid, true, 'core_customfield', 'shared');
        $this->assertNull($data[$fieldid]->get_value());
    }
}
