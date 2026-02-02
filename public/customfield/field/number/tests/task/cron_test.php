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
use core_customfield\field_controller;
use customfield_number\test_provider;

/**
 * Test the cron task.
 *
 * @package    customfield_number
 * @covers     \customfield_number\task\cron
 * @copyright  2026 Sebastian Gundersen <sebastian.gundersen@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class cron_test extends \advanced_testcase {
    /** @var field_controller Field */
    private field_controller $field;

    /**
     * Set up.
     *
     * @return void
     */
    protected function setUp(): void {
        parent::setUp();

        $this->resetAfterTest();
        $this->setAdminUser();

        require_once(__DIR__ . '/../fixtures/test_provider.php');

        $category = $this->getDataGenerator()->create_custom_field_category([
            'component' => 'core_customfield',
            'area' => 'shared',
        ]);

        toggle_shared_category::execute($category->get('id'), 'core_course', 'course', 0, true);

        $this->field = $this->getDataGenerator()->create_custom_field([
            'categoryid' => $category->get('id'),
            'shortname' => 'seconds',
            'type' => 'number',
            'configdata' => [
                'fieldtype' => test_provider::class,
            ],
        ]);
    }

    /**
     * Test running the cron task to recalculate number custom field values when data is created by the provider.
     *
     * @return void
     */
    public function test_execute_without_data(): void {
        $clock = $this->mock_clock_with_frozen();
        $fieldid = $this->field->get('id');
        $fields = [$fieldid => $this->field];
        $course = $this->getDataGenerator()->create_course();
        $courseid = (int)$course->id;

        // Confirm current value.
        $data = api::get_instance_fields_data($fields, $courseid, true, 'core_course', 'course');
        $this->assertNull($data[$fieldid]->get_value());
        $data = api::get_instance_fields_data($fields, $courseid, true, 'core_customfield', 'shared');
        $this->assertNull($data[$fieldid]->get_value());

        // Run the cron task and confirm the value is updated.
        (new cron())->execute();
        $data = api::get_instance_fields_data($fields, $courseid, true, 'core_course', 'course');
        $this->assertEquals($clock->time() % 3600, $data[$fieldid]->get_value());
        $data = api::get_instance_fields_data($fields, $courseid, true, 'core_customfield', 'shared');
        $this->assertNull($data[$fieldid]->get_value());
    }

    /**
     * Test running the cron task to recalculate number custom field values when there is already data.
     *
     * @return void
     */
    public function test_execute_with_existing_data(): void {
        $clock = $this->mock_clock_with_frozen();
        $fieldid = $this->field->get('id');
        $fields = [$fieldid => $this->field];
        $course = $this->getDataGenerator()->create_course([
            'customfield_seconds' => 10,
        ]);
        $courseid = (int)$course->id;

        // Confirm current value.
        $data = api::get_instance_fields_data($fields, $courseid, true, 'core_course', 'course');
        $this->assertEquals(10, $data[$fieldid]->get_value());
        $data = api::get_instance_fields_data($fields, $courseid, true, 'core_customfield', 'shared');
        $this->assertNull($data[$fieldid]->get_value());

        // Run the cron task and confirm the value is updated.
        (new cron())->execute();
        $data = api::get_instance_fields_data($fields, $courseid, true, 'core_course', 'course');
        $this->assertEquals($clock->time() % 3600, $data[$fieldid]->get_value());
        $data = api::get_instance_fields_data($fields, $courseid, true, 'core_customfield', 'shared');
        $this->assertNull($data[$fieldid]->get_value());
    }
}
