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

namespace tool_brickfield;

/**
 * Unit tests for {@scheduler tool_brickfield\scheduler.php}.
 *
 * @package   tool_brickfield
 * @copyright  2020 onward: Brickfield Education Labs, www.brickfield.ie
 * @author     Jay Churchward (jay@brickfieldlabs.ie)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class scheduler_test extends \advanced_testcase {

    public function test_request_analysis(): void {
        $this->resetAfterTest();

        // I believe there is a bug where the code won't work with the default constructor values.
        // Can't find data record in database table course.
        // (SELECT id,category FROM {course} WHERE id = ?[array (0 => 0,)])
        // There is no course with an id of 0 so it throws an error, however it is never used like this in the current code.

        $object = new scheduler(1);
        $output = $object->request_analysis();
        $this->assertTrue($output);

        $object = new scheduler(1, 1);
        $output = $object->request_analysis();
        $this->assertTrue($output);

        $object = new scheduler(0, 2);
        $output = $object->request_analysis();
        $this->assertTrue($output);

    }

    public function test_mark_analyzed(): void {
        $this->resetAfterTest();
        $object = new scheduler();
        $output = $object->mark_analyzed();
        $this->assertTrue($output);

        $object = new scheduler(1, 1);
        $output = $object->mark_analyzed();
        $this->assertTrue($output);
    }

    public function test_create_schedule(): void {
        global $DB;

        $this->resetAfterTest();
        $object = new scheduler();
        $output = $object->create_schedule();
        $record = $DB->get_record($object::DATA_TABLE, ['contextlevel' => 50]);
        $this->assertTrue($output);
        $this->assertEquals($record->instanceid, 0);

        $object = new scheduler(1, 1);
        $output = $object->mark_analyzed();
        $record = $DB->get_record($object::DATA_TABLE, ['contextlevel' => 1]);
        $this->assertTrue($output);
        $this->assertEquals($record->instanceid, 1);
    }

    public function test_delete_schedule(): void {
        global $DB;

        // Call create_record() to insert a record into the table.
        $this->resetAfterTest();
        $object = new scheduler();
        $object->create_schedule();
        $record = $DB->get_record($object::DATA_TABLE, ['contextlevel' => 50]);

        // Assert that the record is in the table.
        $this->assertEquals($record->instanceid, 0);

        // Assert that the record is deleted after calling delete_schedule().
        $output = $object->delete_schedule();
        $record = $DB->get_record($object::DATA_TABLE, ['contextlevel' => 50]);
        $this->assertTrue($output);
        $this->assertFalse($record);
    }

    public function test_is_in_schedule(): void {
        $this->resetAfterTest();

        // This should assert to false as no record has been inserted.
        $object = new scheduler();
        $output = $object->is_in_schedule();
        $this->assertFalse($output);

        // This should assert to true because create_schedule inserts a record to the table.
        $object->create_schedule();
        $output = $object->is_in_schedule();
        $this->assertTrue($output);
    }

    public function test_is_scheduled(): void {
        $this->resetAfterTest();

        // This should assert to false as no record has been inserted.
        $object = new scheduler(1, 1);
        $output = $object->is_scheduled();
        $this->assertFalse($output);

        // This should assert to false because the record has been created but not requested.
        $object->create_schedule();
        $output = $object->is_scheduled();
        $this->assertFalse($output);

        // This should assert to true because the record has been created and requested.
        $object->create_schedule();
        $object->request_analysis();
        $output = $object->is_scheduled();
        $this->assertTrue($output);
    }

    public function test_is_submitted(): void {
        $this->resetAfterTest();

        // This should assert to false as no record has been inserted.
        $object = new scheduler(1, 1);
        $output = $object->is_submitted();
        $this->assertFalse($output);

        // This should assert to false because the record has been created but not submitted.
        $object->create_schedule();
        $output = $object->is_submitted();
        $this->assertFalse($output);

        // This should assert to true because the record has been created and submitted.
        $object->create_schedule();
        $object->mark_analyzed();
        $output = $object->is_submitted();
        $this->assertTrue($output);
    }

    public function test_is_analyzed(): void {
        $this->resetAfterTest();

        // This should assert to false as no record has been inserted.
        $object = new scheduler(1, 1);
        $output = $object->is_analyzed();
        $this->assertFalse($output);

        // This should assert to false because the record has been created but not submitted.
        $object->create_schedule();
        $output = $object->is_analyzed();
        $this->assertFalse($output);

        // This should assert to true because the record has been created and submitted.
        $object->create_schedule();
        $object->mark_analyzed();
        $output = $object->is_analyzed();
        $this->assertTrue($output);
    }

    // Can't test because it's a protected function.
    public function test_standard_search_params(): void {
    }

    // Can't test because it's a protected function.
    public function test_get_contextid(): void {
    }

    public function test_get_datarecord(): void {
        $this->resetAfterTest();

        $object = new scheduler();
        $output = $object->get_datarecord();
        $this->assertEquals($output->contextlevel, 50);
        $this->assertEquals($output->instanceid, 0);
        $this->assertEquals($output->status, 0);

        $object = new scheduler(1, 1);
        $output = $object->get_datarecord(2);
        $this->assertEquals($output->contextlevel, 1);
        $this->assertEquals($output->instanceid, 1);
        $this->assertEquals($output->status, 2);

        $object = new scheduler(10, 143);
        $output = $object->get_datarecord(5);
        $this->assertEquals($output->contextlevel, 143);
        $this->assertEquals($output->instanceid, 10);
        $this->assertEquals($output->status, 5);
    }

    // No return statement.
    public function test_process_scheduled_requests(): void {

    }

    public function test_initialize_schedule(): void {
        global $DB;
        $this->resetAfterTest();

        $output = scheduler::initialize_schedule();
        $record = $DB->get_record(scheduler::DATA_TABLE, ['contextlevel' => 50]);
        $this->assertTrue($output);
        $this->assertEquals($record->contextlevel, 50);

        $output = scheduler::initialize_schedule(20);
        $record = $DB->get_record(scheduler::DATA_TABLE, ['contextlevel' => 20]);
        $this->assertTrue($output);
        $this->assertEquals($record->contextlevel, 20);
    }

    public function test_request_course_analysis(): void {
        $this->resetAfterTest();

        $output = scheduler::request_course_analysis(1);
        $this->assertTrue($output);
    }

    public function test_create_course_schedule(): void {
        global $DB;
        $this->resetAfterTest();

        $output = scheduler::create_course_schedule(1);
        $record = $DB->get_record(scheduler::DATA_TABLE, ['contextlevel' => 50]);
        $this->assertTrue($output);
        $this->assertEquals($record->instanceid, 1);

    }

    public function test_delete_course_schedule(): void {
        global $DB;
        $this->resetAfterTest();

        scheduler::create_course_schedule(1);
        $record = $DB->get_record(scheduler::DATA_TABLE, ['contextlevel' => 50]);
        $this->assertEquals($record->instanceid, 1);

        $output = scheduler::delete_course_schedule(1);
        $record = $DB->get_record(scheduler::DATA_TABLE, ['contextlevel' => 50]);
        $this->assertTrue($output);
        $this->assertFalse($record);
    }

    public function test_is_course_in_schedule(): void {
        $this->resetAfterTest();

        // This should assert to false as no record has been inserted.
        $output = scheduler::is_course_in_schedule(1);
        $this->assertFalse($output);

        // This should assert to true because create_schedule inserts a record to the table.
        scheduler::create_course_schedule(1);
        $output = scheduler::is_course_in_schedule(1);
        $this->assertTrue($output);
    }

    public function test_is_course_scheduled(): void {
        $this->resetAfterTest();

        // This should assert to false as no record has been inserted.
        $output = scheduler::is_course_scheduled(1);
        $this->assertFalse($output);

        // This should assert to false because the record has been created but not requested.
        scheduler::create_course_schedule(1);
        $output = scheduler::is_course_scheduled(1);
        $this->assertFalse($output);

        // This should assert to true because the record has been created and requested.
        scheduler::create_course_schedule(1);
        scheduler::request_course_analysis(1);
        $output = scheduler::is_course_scheduled(1);
        $this->assertTrue($output);
    }

    public function test_is_course_analyzed(): void {
        $this->resetAfterTest();
        $object = new scheduler(10, 1);

        // This should assert to false as no record has been inserted.
        $output = scheduler::is_course_analyzed(10);
        $this->assertFalse($output);

        // This should assert to false because the record has been created but not submitted.
        scheduler::create_course_schedule(10);
        $output = scheduler::is_course_analyzed(10);
        $this->assertFalse($output);

        // This should assert to true because the record has been created and submitted.
        $object->create_schedule();
        $object->mark_analyzed();
        $output = $object->is_analyzed();
        $this->assertTrue($output);
    }
}
