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

namespace tool_messageinbound;

use core_privacy\tests\provider_testcase;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\transform;
use core_privacy\local\request\writer;
use tool_messageinbound\privacy\provider;

/**
 * Manager testcase class.
 *
 * @package    tool_messageinbound
 * @category   test
 * @copyright  2018 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class manager_test extends provider_testcase {

    public function setUp(): void {
        global $CFG;
        parent::setUp();
        $this->resetAfterTest();

        // Pretend the system is enabled.
        $CFG->messageinbound_enabled = true;
        $CFG->messageinbound_mailbox = 'mailbox';
        $CFG->messageinbound_domain = 'example.com';
    }

    public function test_tidy_old_verification_failures(): void {
        global $DB;

        $now = time();
        $stale = $now - DAYSECS - 1;    // Make a second older because PHP Unit is too damn fast!!

        $this->create_messagelist(['timecreated' => $now]);
        $this->create_messagelist(['timecreated' => $now - HOURSECS]);
        $this->create_messagelist(['timecreated' => $stale]);
        $this->create_messagelist(['timecreated' => $stale - HOURSECS]);
        $this->create_messagelist(['timecreated' => $stale - YEARSECS]);

        $this->assertEquals(5, $DB->count_records('messageinbound_messagelist', []));
        $this->assertEquals(3, $DB->count_records_select('messageinbound_messagelist', 'timecreated < :t', ['t' => $stale + 1]));

        $manager = new \tool_messageinbound\manager();
        $manager->tidy_old_verification_failures();

        $this->assertEquals(2, $DB->count_records('messageinbound_messagelist', []));
        $this->assertEquals(0, $DB->count_records_select('messageinbound_messagelist', 'timecreated < :t', ['t' => $stale + 1]));
    }

    /**
     * Create a message to validate.
     *
     * @param array $params The params.
     * @return stdClass
     */
    protected function create_messagelist(array $params) {
        global $DB, $USER;
        $record = (object) array_merge([
            'messageid' => 'abc',
            'userid' => $USER->id,
            'address' => 'text@example.com',
            'timecreated' => time(),
        ], $params);
        $record->id = $DB->insert_record('messageinbound_messagelist', $record);
        return $record;
    }

}
