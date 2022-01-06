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

/**
 * File containing tests for the mform class.
 *
 * @package    tool_task
 * @copyright  2014 onwards Ankit Agarwal <ankit.agrr@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

/**
 * Mform test class.
 *
 * @package    tool_task
 * @copyright  2014 onwards Ankit Agarwal <ankit.agrr@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or late
 */
class tool_task_form_testcase extends advanced_testcase {

    /**
     * Test validations for minute field.
     */
    public function test_validate_fields_minute() {
        $valid = \tool_task_edit_scheduled_task_form::validate_fields('minute', '*');
        $this->assertTrue($valid);
        $valid = \tool_task_edit_scheduled_task_form::validate_fields('minute', '1');
        $this->assertTrue($valid);
        $valid = \tool_task_edit_scheduled_task_form::validate_fields('minute', '20');
        $this->assertTrue($valid);
        $valid = \tool_task_edit_scheduled_task_form::validate_fields('minute', '65');
        $this->assertFalse($valid);

        $valid = \tool_task_edit_scheduled_task_form::validate_fields('minute', '*/');
        $this->assertFalse($valid);
        $valid = \tool_task_edit_scheduled_task_form::validate_fields('minute', '*/1');
        $this->assertTrue($valid);
        $valid = \tool_task_edit_scheduled_task_form::validate_fields('minute', '*/20');
        $this->assertTrue($valid);
        $valid = \tool_task_edit_scheduled_task_form::validate_fields('minute', '*/65');
        $this->assertFalse($valid);

        $valid = \tool_task_edit_scheduled_task_form::validate_fields('minute', '1,2');
        $this->assertTrue($valid);
        $valid = \tool_task_edit_scheduled_task_form::validate_fields('minute', '2,20');
        $this->assertTrue($valid);
        $valid = \tool_task_edit_scheduled_task_form::validate_fields('minute', '20,30,45');
        $this->assertTrue($valid);
        $valid = \tool_task_edit_scheduled_task_form::validate_fields('minute', '65,20,30');
        $this->assertFalse($valid);
        $valid = \tool_task_edit_scheduled_task_form::validate_fields('minute', '25,75');
        $this->assertFalse($valid);

        $valid = \tool_task_edit_scheduled_task_form::validate_fields('minute', '1-2');
        $this->assertTrue($valid);
        $valid = \tool_task_edit_scheduled_task_form::validate_fields('minute', '2-20');
        $this->assertTrue($valid);
        $valid = \tool_task_edit_scheduled_task_form::validate_fields('minute', '20-30');
        $this->assertTrue($valid);
        $valid = \tool_task_edit_scheduled_task_form::validate_fields('minute', '65-20');
        $this->assertFalse($valid);
        $valid = \tool_task_edit_scheduled_task_form::validate_fields('minute', '25-75');
        $this->assertFalse($valid);
    }

    /**
     * Test validations for minute hour.
     */
    public function test_validate_fields_hour() {
        $valid = \tool_task_edit_scheduled_task_form::validate_fields('hour', '*');
        $this->assertTrue($valid);
        $valid = \tool_task_edit_scheduled_task_form::validate_fields('hour', '1');
        $this->assertTrue($valid);
        $valid = \tool_task_edit_scheduled_task_form::validate_fields('hour', '20');
        $this->assertTrue($valid);
        $valid = \tool_task_edit_scheduled_task_form::validate_fields('hour', '65');
        $this->assertFalse($valid);

        $valid = \tool_task_edit_scheduled_task_form::validate_fields('hour', '*/');
        $this->assertFalse($valid);
        $valid = \tool_task_edit_scheduled_task_form::validate_fields('hour', '*/1');
        $this->assertTrue($valid);
        $valid = \tool_task_edit_scheduled_task_form::validate_fields('hour', '*/20');
        $this->assertTrue($valid);
        $valid = \tool_task_edit_scheduled_task_form::validate_fields('hour', '*/65');
        $this->assertFalse($valid);

        $valid = \tool_task_edit_scheduled_task_form::validate_fields('hour', '1,2');
        $this->assertTrue($valid);
        $valid = \tool_task_edit_scheduled_task_form::validate_fields('hour', '2,20');
        $this->assertTrue($valid);
        $valid = \tool_task_edit_scheduled_task_form::validate_fields('hour', '20,30,45');
        $this->assertTrue($valid);
        $valid = \tool_task_edit_scheduled_task_form::validate_fields('hour', '65,20,30');
        $this->assertFalse($valid);
        $valid = \tool_task_edit_scheduled_task_form::validate_fields('hour', '25,75');
        $this->assertFalse($valid);

        $valid = \tool_task_edit_scheduled_task_form::validate_fields('hour', '1-2');
        $this->assertTrue($valid);
        $valid = \tool_task_edit_scheduled_task_form::validate_fields('hour', '2-20');
        $this->assertTrue($valid);
        $valid = \tool_task_edit_scheduled_task_form::validate_fields('hour', '20-30');
        $this->assertTrue($valid);
        $valid = \tool_task_edit_scheduled_task_form::validate_fields('hour', '65-20');
        $this->assertFalse($valid);
        $valid = \tool_task_edit_scheduled_task_form::validate_fields('hour', '25-75');
        $this->assertFalse($valid);
    }

    /**
     * Test validations for day field.
     */
    public function test_validate_fields_day() {
        $valid = \tool_task_edit_scheduled_task_form::validate_fields('day', '*');
        $this->assertTrue($valid);
        $valid = \tool_task_edit_scheduled_task_form::validate_fields('day', '1');
        $this->assertTrue($valid);
        $valid = \tool_task_edit_scheduled_task_form::validate_fields('day', '20');
        $this->assertTrue($valid);
        $valid = \tool_task_edit_scheduled_task_form::validate_fields('day', '65');
        $this->assertFalse($valid);
        $valid = \tool_task_edit_scheduled_task_form::validate_fields('day', '35');
        $this->assertFalse($valid);

        $valid = \tool_task_edit_scheduled_task_form::validate_fields('day', '*/');
        $this->assertFalse($valid);
        $valid = \tool_task_edit_scheduled_task_form::validate_fields('day', '*/1');
        $this->assertTrue($valid);
        $valid = \tool_task_edit_scheduled_task_form::validate_fields('day', '*/20');
        $this->assertTrue($valid);
        $valid = \tool_task_edit_scheduled_task_form::validate_fields('day', '*/65');
        $this->assertFalse($valid);
        $valid = \tool_task_edit_scheduled_task_form::validate_fields('day', '*/35');
        $this->assertFalse($valid);

        $valid = \tool_task_edit_scheduled_task_form::validate_fields('day', '1,2');
        $this->assertTrue($valid);
        $valid = \tool_task_edit_scheduled_task_form::validate_fields('day', '2,20');
        $this->assertTrue($valid);
        $valid = \tool_task_edit_scheduled_task_form::validate_fields('day', '20,30,25');
        $this->assertTrue($valid);
        $valid = \tool_task_edit_scheduled_task_form::validate_fields('day', '65,20,30');
        $this->assertFalse($valid);
        $valid = \tool_task_edit_scheduled_task_form::validate_fields('day', '25,35');
        $this->assertFalse($valid);

        $valid = \tool_task_edit_scheduled_task_form::validate_fields('day', '1-2');
        $this->assertTrue($valid);
        $valid = \tool_task_edit_scheduled_task_form::validate_fields('day', '2-20');
        $this->assertTrue($valid);
        $valid = \tool_task_edit_scheduled_task_form::validate_fields('day', '20-30');
        $this->assertTrue($valid);
        $valid = \tool_task_edit_scheduled_task_form::validate_fields('day', '65-20');
        $this->assertFalse($valid);
        $valid = \tool_task_edit_scheduled_task_form::validate_fields('day', '25-35');
        $this->assertFalse($valid);
    }

    /**
     * Test validations for month field.
     */
    public function test_validate_fields_month() {
        $valid = \tool_task_edit_scheduled_task_form::validate_fields('month', '*');
        $this->assertTrue($valid);
        $valid = \tool_task_edit_scheduled_task_form::validate_fields('month', '1');
        $this->assertTrue($valid);
        $valid = \tool_task_edit_scheduled_task_form::validate_fields('month', '10');
        $this->assertTrue($valid);
        $valid = \tool_task_edit_scheduled_task_form::validate_fields('month', '13');
        $this->assertFalse($valid);
        $valid = \tool_task_edit_scheduled_task_form::validate_fields('month', '35');
        $this->assertFalse($valid);

        $valid = \tool_task_edit_scheduled_task_form::validate_fields('month', '*/');
        $this->assertFalse($valid);
        $valid = \tool_task_edit_scheduled_task_form::validate_fields('month', '*/1');
        $this->assertTrue($valid);
        $valid = \tool_task_edit_scheduled_task_form::validate_fields('month', '*/12');
        $this->assertTrue($valid);
        $valid = \tool_task_edit_scheduled_task_form::validate_fields('month', '*/13');
        $this->assertFalse($valid);
        $valid = \tool_task_edit_scheduled_task_form::validate_fields('month', '*/35');
        $this->assertFalse($valid);

        $valid = \tool_task_edit_scheduled_task_form::validate_fields('month', '1,2');
        $this->assertTrue($valid);
        $valid = \tool_task_edit_scheduled_task_form::validate_fields('month', '2,11');
        $this->assertTrue($valid);
        $valid = \tool_task_edit_scheduled_task_form::validate_fields('month', '2,10,12');
        $this->assertTrue($valid);
        $valid = \tool_task_edit_scheduled_task_form::validate_fields('month', '65,2,13');
        $this->assertFalse($valid);
        $valid = \tool_task_edit_scheduled_task_form::validate_fields('month', '25,35');
        $this->assertFalse($valid);

        $valid = \tool_task_edit_scheduled_task_form::validate_fields('month', '1-2');
        $this->assertTrue($valid);
        $valid = \tool_task_edit_scheduled_task_form::validate_fields('month', '2-12');
        $this->assertTrue($valid);
        $valid = \tool_task_edit_scheduled_task_form::validate_fields('month', '3-6');
        $this->assertTrue($valid);
        $valid = \tool_task_edit_scheduled_task_form::validate_fields('month', '65-2');
        $this->assertFalse($valid);
        $valid = \tool_task_edit_scheduled_task_form::validate_fields('month', '25-26');
        $this->assertFalse($valid);
    }

    /**
     * Test validations for dayofweek field.
     */
    public function test_validate_fields_dayofweek() {
        $valid = \tool_task_edit_scheduled_task_form::validate_fields('dayofweek', '*');
        $this->assertTrue($valid);
        $valid = \tool_task_edit_scheduled_task_form::validate_fields('dayofweek', '0');
        $this->assertTrue($valid);
        $valid = \tool_task_edit_scheduled_task_form::validate_fields('dayofweek', '1');
        $this->assertTrue($valid);
        $valid = \tool_task_edit_scheduled_task_form::validate_fields('dayofweek', '6');
        $this->assertTrue($valid);
        $valid = \tool_task_edit_scheduled_task_form::validate_fields('dayofweek', '7');
        $this->assertFalse($valid);
        $valid = \tool_task_edit_scheduled_task_form::validate_fields('dayofweek', '20');
        $this->assertFalse($valid);

        $valid = \tool_task_edit_scheduled_task_form::validate_fields('dayofweek', '*/');
        $this->assertFalse($valid);
        $valid = \tool_task_edit_scheduled_task_form::validate_fields('dayofweek', '*/1');
        $this->assertTrue($valid);
        $valid = \tool_task_edit_scheduled_task_form::validate_fields('dayofweek', '*/6');
        $this->assertTrue($valid);
        $valid = \tool_task_edit_scheduled_task_form::validate_fields('dayofweek', '*/13');
        $this->assertFalse($valid);
        $valid = \tool_task_edit_scheduled_task_form::validate_fields('dayofweek', '*/35');
        $this->assertFalse($valid);

        $valid = \tool_task_edit_scheduled_task_form::validate_fields('dayofweek', '1,2');
        $this->assertTrue($valid);
        $valid = \tool_task_edit_scheduled_task_form::validate_fields('dayofweek', '2,6');
        $this->assertTrue($valid);
        $valid = \tool_task_edit_scheduled_task_form::validate_fields('dayofweek', '2,6,3');
        $this->assertTrue($valid);
        $valid = \tool_task_edit_scheduled_task_form::validate_fields('dayofweek', '65,2,13');
        $this->assertFalse($valid);
        $valid = \tool_task_edit_scheduled_task_form::validate_fields('dayofweek', '25,35');
        $this->assertFalse($valid);

        $valid = \tool_task_edit_scheduled_task_form::validate_fields('dayofweek', '1-2');
        $this->assertTrue($valid);
        $valid = \tool_task_edit_scheduled_task_form::validate_fields('dayofweek', '2-6');
        $this->assertTrue($valid);
        $valid = \tool_task_edit_scheduled_task_form::validate_fields('dayofweek', '65-2');
        $this->assertFalse($valid);
        $valid = \tool_task_edit_scheduled_task_form::validate_fields('dayofweek', '3-7');
        $this->assertFalse($valid);
    }
}

