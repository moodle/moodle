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

namespace tool_task;

/**
 * Test for the task mform class.
 *
 * @package    tool_task
 * @copyright  2014 onwards Ankit Agarwal <ankit.agrr@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or late
 */
final class form_test extends \advanced_testcase {

    /**
     * Test validations for minute field.
     */
    public function test_validate_fields_minute(): void {
        $checker = new \tool_task\scheduled_checker_task();
        $checker->set_minute('*');
        $this->assertTrue($checker->is_valid($checker::FIELD_MINUTE));
        $checker->set_minute('1');
        $this->assertTrue($checker->is_valid($checker::FIELD_MINUTE));
        $checker->set_minute('20');
        $this->assertTrue($checker->is_valid($checker::FIELD_MINUTE));
        $checker->set_minute('65');
        $this->assertFalse($checker->is_valid($checker::FIELD_MINUTE));

        $checker->set_minute('*/');
        $this->assertFalse($checker->is_valid($checker::FIELD_MINUTE));
        $checker->set_minute('*/1');
        $this->assertTrue($checker->is_valid($checker::FIELD_MINUTE));
        $checker->set_minute('*/20');
        $this->assertTrue($checker->is_valid($checker::FIELD_MINUTE));
        $checker->set_minute('*/60');
        $this->assertFalse($checker->is_valid($checker::FIELD_MINUTE));

        $checker->set_minute('1,2');
        $this->assertTrue($checker->is_valid($checker::FIELD_MINUTE));
        $checker->set_minute('2,20');
        $this->assertTrue($checker->is_valid($checker::FIELD_MINUTE));
        $checker->set_minute('20,30,45');
        $this->assertTrue($checker->is_valid($checker::FIELD_MINUTE));
        $checker->set_minute('65,20,30');
        $this->assertFalse($checker->is_valid($checker::FIELD_MINUTE));
        $checker->set_minute('25,75');
        $this->assertFalse($checker->is_valid($checker::FIELD_MINUTE));

        $checker->set_minute('1-2');
        $this->assertTrue($checker->is_valid($checker::FIELD_MINUTE));
        $checker->set_minute('2-20');
        $this->assertTrue($checker->is_valid($checker::FIELD_MINUTE));
        $checker->set_minute('20-30');
        $this->assertTrue($checker->is_valid($checker::FIELD_MINUTE));
        $checker->set_minute('65-20');
        $this->assertFalse($checker->is_valid($checker::FIELD_MINUTE));
        $checker->set_minute('25-75');
        $this->assertFalse($checker->is_valid($checker::FIELD_MINUTE));
    }

    /**
     * Test validations for minute hour.
     */
    public function test_validate_fields_hour(): void {
        $checker = new \tool_task\scheduled_checker_task();
        $checker->set_hour('*');
        $this->assertTrue($checker->is_valid($checker::FIELD_HOUR));
        $checker->set_hour('1');
        $this->assertTrue($checker->is_valid($checker::FIELD_HOUR));
        $checker->set_hour('20');
        $this->assertTrue($checker->is_valid($checker::FIELD_HOUR));
        $checker->set_hour('60');
        $this->assertFalse($checker->is_valid($checker::FIELD_HOUR));
        $checker->set_hour('65');
        $this->assertFalse($checker->is_valid($checker::FIELD_HOUR));

        $checker->set_hour('*/');
        $this->assertFalse($checker->is_valid($checker::FIELD_HOUR));
        $checker->set_hour('*/1');
        $this->assertTrue($checker->is_valid($checker::FIELD_HOUR));
        $checker->set_hour('*/20');
        $this->assertTrue($checker->is_valid($checker::FIELD_HOUR));
        $checker->set_hour('*/60');
        $this->assertFalse($checker->is_valid($checker::FIELD_HOUR));
        $checker->set_hour('*/65');
        $this->assertFalse($checker->is_valid($checker::FIELD_HOUR));

        $checker->set_hour('1,2');
        $this->assertTrue($checker->is_valid($checker::FIELD_HOUR));
        $checker->set_hour('2,20');
        $this->assertTrue($checker->is_valid($checker::FIELD_HOUR));
        $checker->set_hour('20,30,45');
        $this->assertFalse($checker->is_valid($checker::FIELD_HOUR));
        $checker->set_hour('65,20,30');
        $this->assertFalse($checker->is_valid($checker::FIELD_HOUR));
        $checker->set_hour('25,75');
        $this->assertFalse($checker->is_valid($checker::FIELD_HOUR));

        $checker->set_hour('1-2');
        $this->assertTrue($checker->is_valid($checker::FIELD_HOUR));
        $checker->set_hour('2-20');
        $this->assertTrue($checker->is_valid($checker::FIELD_HOUR));
        $checker->set_hour('20-30');
        $this->assertFalse($checker->is_valid($checker::FIELD_HOUR));
        $checker->set_hour('65-20');
        $this->assertFalse($checker->is_valid($checker::FIELD_HOUR));
        $checker->set_hour('24-75');
        $this->assertFalse($checker->is_valid($checker::FIELD_HOUR));
    }

    /**
     * Test validations for day field.
     */
    public function test_validate_fields_day(): void {
        $checker = new \tool_task\scheduled_checker_task();
        $checker->set_day('*');
        $this->assertTrue($checker->is_valid($checker::FIELD_DAY));
        $checker->set_day('1');
        $this->assertTrue($checker->is_valid($checker::FIELD_DAY));
        $checker->set_day('20');
        $this->assertTrue($checker->is_valid($checker::FIELD_DAY));
        $checker->set_day('65');
        $this->assertFalse($checker->is_valid($checker::FIELD_DAY));
        $checker->set_day('35');
        $this->assertFalse($checker->is_valid($checker::FIELD_DAY));

        $checker->set_day('*/');
        $this->assertFalse($checker->is_valid($checker::FIELD_DAY));
        $checker->set_day('*/1');
        $this->assertTrue($checker->is_valid($checker::FIELD_DAY));
        $checker->set_day('*/20');
        $this->assertTrue($checker->is_valid($checker::FIELD_DAY));
        $checker->set_day('*/65');
        $this->assertFalse($checker->is_valid($checker::FIELD_DAY));
        $checker->set_day('*/35');
        $this->assertFalse($checker->is_valid($checker::FIELD_DAY));


        $checker->set_day('1,2');
        $this->assertTrue($checker->is_valid($checker::FIELD_DAY));
        $checker->set_day('2,20');
        $this->assertTrue($checker->is_valid($checker::FIELD_DAY));
        $checker->set_day('20,30,25');
        $this->assertTrue($checker->is_valid($checker::FIELD_DAY));
        $checker->set_day('65,20,30');
        $this->assertFalse($checker->is_valid($checker::FIELD_DAY));
        $checker->set_day('25,35');
        $this->assertFalse($checker->is_valid($checker::FIELD_DAY));

        $checker->set_day('1-2');
        $this->assertTrue($checker->is_valid($checker::FIELD_DAY));
        $checker->set_day('2-20');
        $this->assertTrue($checker->is_valid($checker::FIELD_DAY));
        $checker->set_day('20-30');
        $this->assertTrue($checker->is_valid($checker::FIELD_DAY));
        $checker->set_day('65-20');
        $this->assertFalse($checker->is_valid($checker::FIELD_DAY));
        $checker->set_day('25-35');
        $this->assertFalse($checker->is_valid($checker::FIELD_DAY));
    }

    /**
     * Test validations for month field.
     */
    public function test_validate_fields_month(): void {
        $checker = new \tool_task\scheduled_checker_task();
        $checker->set_month('*');
        $this->assertTrue($checker->is_valid($checker::FIELD_MONTH));
        $checker->set_month('1');
        $this->assertTrue($checker->is_valid($checker::FIELD_MONTH));
        $checker->set_month('10');
        $this->assertTrue($checker->is_valid($checker::FIELD_MONTH));
        $checker->set_month('13');
        $this->assertFalse($checker->is_valid($checker::FIELD_MONTH));
        $checker->set_month('35');
        $this->assertFalse($checker->is_valid($checker::FIELD_MONTH));

        $checker->set_month('*/');
        $this->assertFalse($checker->is_valid($checker::FIELD_MONTH));
        $checker->set_month('*/1');
        $this->assertTrue($checker->is_valid($checker::FIELD_MONTH));
        $checker->set_month('*/12');
        $this->assertTrue($checker->is_valid($checker::FIELD_MONTH));
        $checker->set_month('*/13');
        $this->assertFalse($checker->is_valid($checker::FIELD_MONTH));
        $checker->set_month('*/35');
        $this->assertFalse($checker->is_valid($checker::FIELD_MONTH));

        $checker->set_month('1,2');
        $this->assertTrue($checker->is_valid($checker::FIELD_MONTH));
        $checker->set_month('2,11');
        $this->assertTrue($checker->is_valid($checker::FIELD_MONTH));
        $checker->set_month('2,10,12');
        $this->assertTrue($checker->is_valid($checker::FIELD_MONTH));
        $checker->set_month('65,2,13');
        $this->assertFalse($checker->is_valid($checker::FIELD_MONTH));
        $checker->set_month('25,35');
        $this->assertFalse($checker->is_valid($checker::FIELD_MONTH));

        $checker->set_month('1-2');
        $this->assertTrue($checker->is_valid($checker::FIELD_MONTH));
        $checker->set_month('2-12');
        $this->assertTrue($checker->is_valid($checker::FIELD_MONTH));
        $checker->set_month('3-6');
        $this->assertTrue($checker->is_valid($checker::FIELD_MONTH));
        $checker->set_month('65-2');
        $this->assertFalse($checker->is_valid($checker::FIELD_MONTH));
        $checker->set_month('25-26');
        $this->assertFalse($checker->is_valid($checker::FIELD_MONTH));
    }

    /**
     * Test validations for dayofweek field.
     */
    public function test_validate_fields_dayofweek(): void {
        $checker = new \tool_task\scheduled_checker_task();
        $checker->set_day_of_week('*');
        $this->assertTrue($checker->is_valid($checker::FIELD_DAYOFWEEK));
        $checker->set_day_of_week('0');
        $this->assertTrue($checker->is_valid($checker::FIELD_DAYOFWEEK));
        $checker->set_day_of_week('1');
        $this->assertTrue($checker->is_valid($checker::FIELD_DAYOFWEEK));
        $checker->set_day_of_week('6');
        $this->assertTrue($checker->is_valid($checker::FIELD_DAYOFWEEK));
        $checker->set_day_of_week('7');
        $this->assertTrue($checker->is_valid($checker::FIELD_DAYOFWEEK));
        $checker->set_day_of_week('8');
        $this->assertFalse($checker->is_valid($checker::FIELD_DAYOFWEEK));
        $checker->set_day_of_week('20');
        $this->assertFalse($checker->is_valid($checker::FIELD_DAYOFWEEK));

        $checker->set_day_of_week('*/');
        $this->assertFalse($checker->is_valid($checker::FIELD_DAYOFWEEK));
        $checker->set_day_of_week('*/1');
        $this->assertTrue($checker->is_valid($checker::FIELD_DAYOFWEEK));
        $checker->set_day_of_week('*/6');
        $this->assertTrue($checker->is_valid($checker::FIELD_DAYOFWEEK));
        $checker->set_day_of_week('*/7');
        $this->assertTrue($checker->is_valid($checker::FIELD_DAYOFWEEK));
        $checker->set_day_of_week('*/13');
        $this->assertFalse($checker->is_valid($checker::FIELD_DAYOFWEEK));
        $checker->set_day_of_week('*/35');
        $this->assertFalse($checker->is_valid($checker::FIELD_DAYOFWEEK));

        $checker->set_day_of_week('1,2');
        $this->assertTrue($checker->is_valid($checker::FIELD_DAYOFWEEK));
        $checker->set_day_of_week('2,6');
        $this->assertTrue($checker->is_valid($checker::FIELD_DAYOFWEEK));
        $checker->set_day_of_week('2,6,3');
        $this->assertTrue($checker->is_valid($checker::FIELD_DAYOFWEEK));
        $checker->set_day_of_week('65,2,13');
        $this->assertFalse($checker->is_valid($checker::FIELD_DAYOFWEEK));
        $checker->set_day_of_week('25,35');
        $this->assertFalse($checker->is_valid($checker::FIELD_DAYOFWEEK));

        $checker->set_day_of_week('1-2');
        $this->assertTrue($checker->is_valid($checker::FIELD_DAYOFWEEK));
        $checker->set_day_of_week('2-6');
        $this->assertTrue($checker->is_valid($checker::FIELD_DAYOFWEEK));
        $checker->set_day_of_week('65-2');
        $this->assertFalse($checker->is_valid($checker::FIELD_DAYOFWEEK));
        $checker->set_day_of_week('3-7');
        $this->assertTrue($checker->is_valid($checker::FIELD_DAYOFWEEK));
        $checker->set_day_of_week('3-8');
        $this->assertFalse($checker->is_valid($checker::FIELD_DAYOFWEEK));
    }
}
