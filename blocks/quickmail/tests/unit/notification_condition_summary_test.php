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
use block_quickmail\notifier\notification_condition_summary;
class block_quickmail_notification_condition_summary_testcase extends advanced_testcase {
    use has_general_helpers;
    public function test_gets_summary_for_reminder_course_non_participation_notification() {
        $params = [
            'time_unit' => 'day',
            'time_amount' => '3',
        ];

        $summary = notification_condition_summary::get_model_condition_summary('reminder', 'course_non_participation', $params);
        $this->assertIsString($summary);
        $this->assertEquals('All who have not accessed the course in 3 days', $summary);

        $params = [
            'time_unit' => 'week',
            'time_amount' => '1',
        ];

        $summary = notification_condition_summary::get_model_condition_summary('reminder', 'course_non_participation', $params);
        $this->assertIsString($summary);
        $this->assertEquals('All who have not accessed the course in 1 week', $summary);
    }

}
