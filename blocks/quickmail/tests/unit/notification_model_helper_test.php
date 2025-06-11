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

use block_quickmail\notifier\models\notification_model_helper;

class block_quickmail_notification_model_helper_testcase extends advanced_testcase {

    use has_general_helpers;

    public function test_gets_available_reminder_model_keys_by_type() {
        $types = notification_model_helper::get_available_model_keys_by_type('reminder');

        $this->assertIsArray($types);
        $this->assertCount(count(block_quickmail_plugin::get_model_notification_types('reminder')), $types);
    }

    public function test_gets_available_event_model_keys_by_type() {
        $types = notification_model_helper::get_available_model_keys_by_type('event');

        $this->assertIsArray($types);
        $this->assertCount(count(block_quickmail_plugin::get_model_notification_types('event')), $types);
    }

}
