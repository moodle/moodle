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
 * Tests for local library.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2016 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_ally;

use advanced_testcase;
use tool_ally\local;
use tool_ally\auto_config;

/**
 * Tests for local library.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2016 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @group     tool_ally
 * @group     ally
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @runTestsInSeparateProcesses
 */
class local_test extends advanced_testcase {
    /**
     * Test get role IDs.
     */
    public function test_get_roleids() {
        global $DB;

        $this->resetAfterTest();

        $roles   = $DB->get_records_list('role', 'shortname', ['manager', 'coursecreator', 'editingteacher']);
        $roleids = local::get_roleids();
        foreach ($roles as $role) {
            $this->assertContains($role->id, $roleids);
            $this->assertArrayHasKey($role->id, $roleids);
        }

        set_config('roles', '', 'tool_ally');
        $this->assertEmpty(local::get_roleids());

        unset_config('roles', 'tool_ally');
        $this->assertEmpty(local::get_roleids());
    }

    /**
     * Test get admin IDs.
     */
    public function test_get_adminids() {
        $admins  = get_admins();
        $userids = local::get_roleids();
        foreach ($admins as $admin) {
            $this->assertContains($admin->id, $userids);
            $this->assertArrayHasKey($admin->id, $userids);
        }
    }

    public function test_get_ws_token_invalid_config() {
        // Test failure without ally_webuser / valid configuration.
        $expectedmsg = 'Access control exception (Ally web user (ally_webuser) does not exist.';
        $expectedmsg .= ' Has auto configure been run?)';
        $this->expectExceptionMessage($expectedmsg);
        local::get_ws_token();
    }

    public function test_get_ws_token() {
        $this->resetAfterTest();
        // Test token generated successfully when configured.
        $ac = new auto_config();
        $ac->configure();
        $token = local::get_ws_token();
        $this->assertnotEmpty($token);
        $this->assertNotEmpty($token->token);
    }
}
