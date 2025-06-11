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
 * @package    block_quickmail
 * @copyright  2008 onwards Louisiana State University
 * @copyright  2008 onwards Chad Mazilly, Robert Russo, Jason Peak, Dave Elliott, Adam Zapletal, Philip Cali
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(dirname(__FILE__) . '/traits/unit_testcase_traits.php');

use block_quickmail\validators\edit_notification_form_validator;

class block_quickmail_edit_reminder_notification_form_validator_testcase extends advanced_testcase {

    use has_general_helpers,
        sets_up_courses,
        sets_up_notifications;

    public function test_validate_schedule_time_unit_is_valid_for_reminder_notifications() {
        // Reset all changes automatically after this test.
        $this->resetAfterTest(true);

        $input = $this->get_notification_input(['schedule_time_unit' => 'decade']);

        $validator = new edit_notification_form_validator($input, [
            'notification_type' => 'reminder',
        ]);
        $validator->validate();

        $this->assertTrue($validator->has_errors());
        $this->assertEquals('Invalid unit of time for schedule.', $validator->errors[0]);

        // Reset all changes automatically after this test.
        $this->resetAfterTest(true);

        $input = $this->get_notification_input(['schedule_time_unit' => 'day']);

        $validator = new edit_notification_form_validator($input, [
            'notification_type' => 'reminder',
        ]);
        $validator->validate();

        $this->assertFalse($validator->has_errors());
    }

    public function test_validate_schedule_time_amount_is_valid_for_reminder_notifications() {
        // Reset all changes automatically after this test.
        $this->resetAfterTest(true);

        $input = $this->get_notification_input(['schedule_time_amount' => '']);

        $validator = new edit_notification_form_validator($input, [
            'notification_type' => 'reminder',
        ]);
        $validator->validate();

        $this->assertTrue($validator->has_errors());
        $this->assertEquals('Invalid amount of time for schedule.', $validator->errors[0]);

        $input = $this->get_notification_input(['schedule_time_amount' => 'longtime']);

        $validator = new edit_notification_form_validator($input, [
            'notification_type' => 'reminder',
        ]);
        $validator->validate();

        $this->assertTrue($validator->has_errors());
        $this->assertEquals('Invalid amount of time for schedule.', $validator->errors[0]);

        $input = $this->get_notification_input(['schedule_time_amount' => '2']);

        $validator = new edit_notification_form_validator($input, [
            'notification_type' => 'reminder',
        ]);
        $validator->validate();

        $this->assertFalse($validator->has_errors());
    }

}
