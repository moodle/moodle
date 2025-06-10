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
 * Test cases for observer functions.
 *
 * @package local_o365
 * @author Remote-Learner.net Inc
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2016 onwards Microsoft, Inc. (http://microsoft.com/)
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Tests event observers.
 *
 * @group local_o365
 * @group office365
 * @codeCoverageIgnore
 */
class local_o365_observers_testcase extends \advanced_testcase {
    /**
     * Perform setup before every test. This tells Moodle's phpunit to reset the database after every test.
     */
    protected function setUp() : void {
        parent::setUp();
        $this->resetAfterTest(true);
    }

    /**
     * Test users disconnect method.
     */
    public function test_user_disconnected() {
        global $DB;
        $user = $this->getDataGenerator()->create_user(['auth' => 'oidc']);
        $this->create_member_entities($user->id);
        $this->assertTrue($this->has_member_entities($user->id));
        $eventdata = ['objectid' => $user->id, 'userid' => $user->id];
        $event = \auth_oidc\event\user_disconnected::create($eventdata);
        $event->trigger();
        $this->assertFalse($this->has_member_entities($user->id));
    }

    /**
     * Test users deleted method.
     */
    public function test_user_deleted() {
        global $DB;
        $user = $this->getDataGenerator()->create_user(['auth' => 'oidc']);
        $this->create_member_entities($user->id);
        $this->assertTrue($this->has_member_entities($user->id));
        delete_user($user);
        $this->assertFalse($this->has_member_entities($user->id));
    }

    /**
     * Create Microsoft 365 entities.
     *
     * @param int $userid
     */
    public function create_member_entities($userid) {
        global $DB;
        $token = (object)[
            'user_id' => $userid,
            'scope' => 'scope',
            'tokenresource' => 'resource',
            'token' => rand() * 1000,
            'expiry' => time() + 1000000,
            'refreshtoken' => time() + 100000,
        ];
        $DB->insert_record('local_o365_token', $token);
        $aaduserdata = (object)[
            'type' => 'user',
            'subtype' => '',
            'objectid' => '',
            'moodleid' => $userid,
            'o365name' => 'test@example.onmicrosoft.com',
            'timecreated' => time(),
            'timemodified' => time(),
        ];
        $DB->insert_record('local_o365_objects', $aaduserdata);
        $DB->insert_record('local_o365_connections', ['muserid' => $userid]);
        $object = (object)[
            'muserid' => $userid,
            'assigned' => 1,
            'photoid' => 'abc',
            'photoupdated' => 1,
        ];
        $DB->insert_record('local_o365_appassign', $object);
    }

    /**
     * Test if user has any entities.
     *
     * @param int $userid User id to check for Microsoft 365 entities.
     * @return boolean Returns true if any entities exist for user.
     */
    public function has_member_entities($userid) {
        global $DB;
        $result = $DB->count_records('local_o365_token', ['user_id' => $userid]);
        $result = $result || $DB->count_records('local_o365_objects', ['type' => 'user', 'moodleid' => $userid]);
        $result = $result || $DB->count_records('local_o365_connections', ['muserid' => $userid]);
        return $result || $DB->count_records('local_o365_appassign', ['muserid' => $userid]);
    }
}
