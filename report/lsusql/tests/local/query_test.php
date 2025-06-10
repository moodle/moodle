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

namespace report_lsusql\local;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/report/lsusql/locallib.php');

/**
 * Tests for the report_lsusql\local\query.
 *
 * @package   report_lsusql
 * @copyright 2021 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class query_test extends \advanced_testcase {
    /**
     * Test create query.
     */
    public function test_create_query() {
        $this->resetAfterTest();
        $this->setAdminUser();

        $fakerecord = (object) [
            'id' => 1,
            'displayname' => 'Query 1',
            'runable' => 'daily',
            'capability' => 'moodle/site:config',
            'lastrun' => 0
        ];

        $query = new query($fakerecord);

        $this->assertEquals(1, $query->get_id());
        $this->assertEquals('Query 1', $query->get_displayname());
        $this->assertStringContainsString('view.php?id=1', $query->get_url());
        $this->assertStringContainsString('edit.php?id=1', $query->get_edit_url());
        $this->assertStringContainsString('delete.php?id=1', $query->get_delete_url());
        $this->assertEquals('<span class="admin_note">This query has not yet been run.</span>',
              $query->get_time_note());
        $this->assertEquals('Only administrators (moodle/site:config)', $query->get_capability_string());
        // Admin user should have capability to edit and view queries.
        $this->assertEquals(true, $query->can_edit(\context_system::instance()));
        $this->assertEquals(true, $query->can_view(\context_system::instance()));
    }
}
