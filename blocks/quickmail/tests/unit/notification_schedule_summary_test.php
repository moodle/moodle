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

use block_quickmail\notifier\notification_schedule_summary;

class block_quickmail_notification_schedule_summary_testcase extends advanced_testcase {

    use has_general_helpers;

    public function test_gets_schedule_summary_from_params() {
        $params = [];

        $summary = notification_schedule_summary::get_from_params($params);

        $this->assertIsString($summary);
        $this->assertEquals('', $summary);

        $begin = $this->get_timestamp_for_date('jun 26 2018 08:30:00');
        $end = $this->get_timestamp_for_date('nov 30 2018 08:30:00');

        $params = [
            'time_amount' => '3',
            'time_unit' => 'day',
            'begin_at' => $begin,
            'end_at' => $end,
        ];

        $summary = notification_schedule_summary::get_from_params($params);

        $this->assertIsString($summary);
        $this->assertEquals('Every 3 Days, Beginning Jun 26 2018, 12:30am, Ending Nov 30 2018, 12:30am', $summary);

        $begin = $this->get_timestamp_for_date('jun 26 2018 08:30:00');

        $params = [
            'time_amount' => '1',
            'time_unit' => 'week',
            'begin_at' => $begin,
        ];

        $summary = notification_schedule_summary::get_from_params($params);

        $this->assertIsString($summary);
        $this->assertEquals('Once a Week, Beginning Jun 26 2018, 12:30am', $summary);
    }

}
