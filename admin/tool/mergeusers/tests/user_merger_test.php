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

namespace tool_mergeusers;

use advanced_testcase;
use dml_exception;
use moodle_exception;
use tool_mergeusers\local\user_merger;

/**
 * Merge tool testing.
 *
 * @package   tool_mergeusers
 * @author    Jordi Pujol Ahulló <jordi.pujol@urv.cat>
 * @copyright Universitat Rovira i Virgili (https://www.urv.cat)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class user_merger_test extends advanced_testcase {
    public function setUp(): void {
        global $CFG;
        parent::setUp();
        $this->resetAfterTest(true);
    }

    /**
     * Testing merge for two existing users.
     *
     * @group tool_mergeusers
     * @group tool_mergeusers_tool
     * @throws dml_exception
     * @throws moodle_exception
     */
    public function test_merge_existing_users_with_success(): void {
        global $DB;

        // Setup two users to merge.
        $usertoremove = $this->getDataGenerator()->create_user();
        $usertokeep = $this->getDataGenerator()->create_user();

        $mut = new user_merger();
        [$success, $log, $logid] = $mut->merge($usertokeep->id, $usertoremove->id);

        // Be sure merge was ok.
        $this->assertTrue($success);

        // Check $usertoremove is suspended.
        $suspendedremoveduser = $DB->get_field('user', 'suspended', ['id' => $usertoremove->id]);
        $this->assertEquals(1, $suspendedremoveduser);

        // Check $usertokeep is still active.
        $suspendedkeptuser = $DB->get_field('user', 'suspended', ['id' => $usertokeep->id]);
        $this->assertEquals(0, $suspendedkeptuser);
    }

    /**
     * Testing merge for one deleted user.
     *
     * @group tool_mergeusers
     * @group tool_mergeusers_tool
     * @throws dml_exception
     * @throws moodle_exception
     */
    public function test_failed_merge_with_one_deleted_user() {
        global $DB;

        // Setup two users to merge.
        $usertoremove = $this->getDataGenerator()->create_user();
        delete_user($usertoremove);
        $usertokeep = $this->getDataGenerator()->create_user();

        $mut = new user_merger();
        [$success, $log, $logid] = $mut->merge($usertokeep->id, $usertoremove->id);

        // Be sure merge failed.
        $this->assertFalse($success);

        // Unaltered record for deleted user.
        $suspendedremoveduser = $DB->get_field('user', 'suspended', ['id' => $usertoremove->id, 'deleted' => 1]);
        $this->assertEquals(0, $suspendedremoveduser);

        // Unaltered record for user keep.
        $suspendedkeptuser = $DB->get_field('user', 'suspended', ['id' => $usertokeep->id, 'deleted' => 0]);
        $this->assertEquals(0, $suspendedkeptuser);

        // Be sure only one user id appears on the logs.
        $idtofindonlogs = "\"{$usertoremove->id}\"";
        $matchingerror = array_filter(
            $log,
            function ($line) use ($idtofindonlogs) {
                return strstr($line, $idtofindonlogs);
            },
        );

        $this->assertCount(1, $matchingerror);

        $idnottofindonlogs = "\"{$usertokeep->id}\"";
        $mustnotmatchonerrors = array_filter(
            $log,
            function ($line) use ($idnottofindonlogs) {
                return strstr($line, $idnottofindonlogs);
            },
        );
        $this->assertCount(0, $mustnotmatchonerrors);
    }

    /**
     * Testing merge for two deleted users.
     *
     * @group tool_mergeusers
     * @group tool_mergeusers_tool
     * @throws dml_exception
     * @throws moodle_exception
     */
    public function test_failed_merge_with_two_deleted_users() {
        global $DB;

        // Setup two users to merge.
        $usertoremove = $this->getDataGenerator()->create_user();
        delete_user($usertoremove);
        $usertokeep = $this->getDataGenerator()->create_user();
        delete_user($usertokeep);

        $mut = new user_merger();
        [$success, $log, $logid] = $mut->merge($usertokeep->id, $usertoremove->id);

        // Be sure merge failed.
        $this->assertFalse($success);

        // Unaltered record for deleted user.
        $suspendedremoveduser = $DB->get_field('user', 'suspended', ['id' => $usertoremove->id, 'deleted' => 1]);
        $this->assertEquals(0, $suspendedremoveduser);

        // Unaltered record for deleted user.
        $suspendedkeptuser = $DB->get_field('user', 'suspended', ['id' => $usertokeep->id, 'deleted' => 1]);
        $this->assertEquals(0, $suspendedkeptuser);

        // Be sure both user ids appear on the logs.
        $idtofindonlogsfrom = "\"{$usertoremove->id}\"";
        $idtofindonlogsto = "\"{$usertokeep->id}\"";
        $matchingerror = array_filter(
            $log,
            function ($line) use ($idtofindonlogsfrom, $idtofindonlogsto) {
                return strstr($line, $idtofindonlogsfrom) || strstr($line, $idtofindonlogsto);
            },
        );

        $this->assertCount(2, $matchingerror);
    }
}
