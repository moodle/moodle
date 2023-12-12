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
 * Tests for the Big Blue Button Instance.
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2021 Andrew Lyons <andrew@nicols.co.uk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_bigbluebuttonbn;

use advanced_testcase;
use moodle_exception;

/**
 * Tests for the Big Blue Button Instance.
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2021 Andrew Lyons <andrew@nicols.co.uk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \mod_bigbluebuttonbn\instance
 */
class instance_test extends advanced_testcase {

    /**
     * Test get from
     *
     * @param string $function
     * @param string $field
     * @dataProvider get_from_location_provider
     * @covers ::get_from_instanceid
     * @covers ::get_from_cmid
     */
    public function test_get_from(string $function, string $field): void {
        $this->resetAfterTest();

        [
            'record' => $record,
        ] = $this->get_test_instance();

        $instance = call_user_func("mod_bigbluebuttonbn\instance::{$function}", $record->{$field});

        $this->assertInstanceOf(instance::class, $instance);
        $this->assertEquals($record->id, $instance->get_instance_id());
        $this->assertEquals($record->cmid, $instance->get_cm_id());
        $this->assertEquals($record->cmid, $instance->get_cm()->id);
    }

    /**
     * Get from location provider
     *
     * @return string[][]
     */
    public function get_from_location_provider(): array {
        return [
            ['get_from_instanceid', 'id'],
            ['get_from_cmid', 'cmid'],
        ];
    }

    /**
     * Get an instance from a cmid.
     * @covers ::get_from_cmid
     */
    public function test_get_from_cmid(): void {
        $this->resetAfterTest();

        [
            'record' => $record,
            'cm' => $cm,
        ] = $this->get_test_instance();

        $instance = instance::get_from_cmid($cm->id);

        $this->assertInstanceOf(instance::class, $instance);
        $this->assertEquals($record->id, $instance->get_instance_id());
        $this->assertEquals($cm->id, $instance->get_cm()->id);
    }

    /**
     * If the instance was not found, and exception should be thrown.
     * @covers ::get_from_cmid
     */
    public function test_get_from_cmid_not_found(): void {
        $this->assertNull(instance::get_from_cmid(100));
    }

    /**
     * If the instance was not found, and exception should be thrown.
     * @covers ::get_from_instanceid
     */
    public function test_get_from_instance_not_found(): void {
        $this->assertNull(instance::get_from_instanceid(100));
    }

    /**
     * Get from meeting id
     *
     * @covers ::get_from_meetingid
     */
    public function test_get_from_meetingid(): void {
        $this->resetAfterTest();

        [
            'record' => $record,
        ] = $this->get_test_instance();

        // The meetingid is confusingly made up of a meetingid field, courseid, instanceid, and groupid.
        $instance = instance::get_from_meetingid(sprintf(
            "%s-%s-%s",
            $record->meetingid,
            $record->course,
            $record->id
        ));

        $this->assertInstanceOf(instance::class, $instance);
        $this->assertEquals($record->id, $instance->get_instance_id());
        $this->assertEquals($record->cmid, $instance->get_cm_id());
        $this->assertEquals($record->cmid, $instance->get_cm()->id);
    }

    /**
     * Get the get_from_meetingid() function where the meetingid includes a groupid.
     *
     * @covers ::get_from_meetingid
     */
    public function test_get_from_meetingid_group(): void {
        $this->resetAfterTest();

        [
            'record' => $record,
            'course' => $course,
            'cm' => $cm,
        ] = $this->get_test_instance();

        $group = $this->getDataGenerator()->create_group(['courseid' => $course->id]);

        $instance = instance::get_from_meetingid(
            sprintf("%s-%s-%s[0]", $record->meetingid, $record->course, $record->id)
        );

        $this->assertEquals($cm->instance, $instance->get_instance_id());
        $this->assertEquals($cm->id, $instance->get_cm_id());
    }

    /**
     * Ensure that invalid meetingids throw an appropriate exception.
     *
     * @dataProvider invalid_meetingid_provider
     * @param string $meetingid
     * @covers ::get_from_meetingid
     */
    public function test_get_from_meetingid_invalid(string $meetingid): void {
        $this->expectException(moodle_exception::class);
        instance::get_from_meetingid($meetingid);
    }

    /**
     * Provide invalid meeting examples
     *
     * @return \string[][]
     */
    public function invalid_meetingid_provider(): array {
        // Meeting IDs are in the formats:
        // - <meetingid[string]>-<courseid[number]>-<instanceid[number]>
        // - <meetingid[string]>-<courseid[number]>-<instanceid[number]>[<groupid[number]>]
        // Note: deducing the group from meeting id will soon be deprecated.
        return [
            'Non-numeric instanceid' => ['aaa-123-aaa'],
        ];
    }

    /**
     * Test the get_all_instances_in_course function.
     *
     * @covers ::get_all_instances_in_course
     */
    public function test_get_all_instances_in_course(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $records = [];
        for ($i = 0; $i < 5; $i++) {
            $this->getDataGenerator()->create_module('bigbluebuttonbn', [
                'course' => $course->id,
            ]);
        }

        $instances = instance::get_all_instances_in_course($course->id);
        $this->assertCount(5, $instances);
        foreach ($instances as $instance) {
            $this->assertInstanceOf(instance::class, $instance);
        }
    }

    /**
     * Get test instance from data
     *
     * @param array $data
     * @return array
     */
    protected function get_test_instance(array $data = []): array {
        $course = $this->getDataGenerator()->create_course();
        $record = $this->getDataGenerator()->create_module('bigbluebuttonbn', array_merge([
            'course' => $course->id,
        ], $data));
        $cm = get_fast_modinfo($course)->instances['bigbluebuttonbn'][$record->id];

        return [
            'course' => $course,
            'record' => $record,
            'cm' => $cm,
        ];
    }

    /**
     * Test the get_meeting_id function for a meeting configured for a group.
     *
     * @covers ::get_meeting_id
     */
    public function test_get_meeting_id_with_groups(): void {
        $this->resetAfterTest();

        [
            'record' => $record,
            'course' => $course,
        ] = $this->get_test_instance();

        $group = $this->getDataGenerator()->create_group(['courseid' => $course->id]);

        $instance = instance::get_from_instanceid($record->id);

        // No group.
        $this->assertEquals(
            sprintf("%s-%s-%s[0]", $record->meetingid, $record->course, $record->id),
            $instance->get_meeting_id(0)
        );

        // Specified group.
        $this->assertEquals(
            sprintf("%s-%s-%s[%d]", $record->meetingid, $record->course, $record->id, $group->id),
            $instance->get_meeting_id($group->id)
        );
    }

    /**
     * Test the get_meeting_id function for a meeting configured for a group.
     *
     * @covers ::get_meeting_id
     */
    public function test_get_meeting_id_without_groups(): void {
        $this->resetAfterTest();

        [
            'record' => $record,
            'course' => $course,
        ] = $this->get_test_instance();

        $instance = instance::get_from_instanceid($record->id);

        // No group.
        $this->assertEquals(
            sprintf("%s-%s-%s[0]", $record->meetingid, $record->course, $record->id),
            $instance->get_meeting_id(null)
        );
    }

    /**
     * Data provider to check the various room_available scenarios'
     *
     * @return array
     */
    public function is_currently_open_provider(): array {
        return [
            'No opening or closing time set: Is open' => [null, null, true],
            'Opening time set in the past, no closing: Is open' => [-DAYSECS, null, true],
            'Opening time set in the future, no closing: Is closed' => [+DAYSECS, null, false],
            'Closing time set in the past, no opening: Is closed' => [null, -DAYSECS, false],
            'Closing time set in the future, no opening: Is open' => [null, +DAYSECS, true],
            'Opening and closing in the past: Is closed' => [-WEEKSECS, -DAYSECS, false],
            'Opening and closing in the future: Is closed' => [+DAYSECS, +WEEKSECS, false],
            'Opening in the past, Closing in the future: Is open' => [-DAYSECS, +DAYSECS, true],
        ];
    }

    /**
     * Check instance currently open
     *
     * @dataProvider is_currently_open_provider
     * @param null|int $openingtime
     * @param null|int $closingtime
     * @param bool $expected
     * @covers ::is_currently_open
     */
    public function test_is_currently_open(?int $openingtime, ?int $closingtime, bool $expected): void {
        $stub = $this->getMockBuilder(instance::class)
            ->onlyMethods(['get_instance_var'])
            ->disableOriginalConstructor()
            ->getMock();

        if ($openingtime) {
            $openingtime = $openingtime + time();
        }

        if ($closingtime) {
            $closingtime = $closingtime + time();
        }

        $stub->method('get_instance_var')
            ->willReturnCallback(function($var) use ($openingtime, $closingtime) {
                if ($var === 'openingtime') {
                    return $openingtime;
                }

                return $closingtime;
            });
        $this->assertEquals($expected, $stub->is_currently_open());
    }

    /**
     * Ensure that the user_must_wait_to_join function works as expectd.
     *
     * @dataProvider user_must_wait_to_join_provider
     * @param bool $isadmin
     * @param bool $ismoderator
     * @param bool $haswaitingroom
     * @param bool $expected
     * @covers ::user_must_wait_to_join
     */
    public function test_user_must_wait_to_join(bool $isadmin, bool $ismoderator, bool $haswaitingroom, bool $expected): void {
        $stub = $this->getMockBuilder(instance::class)
            ->setMethods([
                'get_instance_var',
                'is_admin',
                'is_moderator',
            ])
            ->disableOriginalConstructor()
            ->getMock();

        $stub->method('is_admin')->willReturn($isadmin);
        $stub->method('is_moderator')->willReturn($ismoderator);
        $stub->method('get_instance_var')->willReturn($haswaitingroom);

        $this->assertEquals($expected, $stub->user_must_wait_to_join());
    }

    /**
     * Data provider for the user_must_wait_to_join function.
     *
     * @return array
     */
    public function user_must_wait_to_join_provider(): array {
        return [
            'Admins must never wait to join (waiting disabled)' => [true, false, false, false],
            'Admins must never wait to join (waiting enabled)' => [true, false, true, false],
            'Moderators must never wait to join (waiting disabled)' => [false, true, false, false],
            'Moderators must never wait to join (waiting enabled)' => [false, true, true, false],
            'Other users must wait to join if waiting enabled' => [false, false, true, true],
            'Other users cannot wait to join if waiting disabled' => [false, false, false, false],
        ];
    }

    /**
     * Ensure that the does_current_user_count_towards_user_limit function works as expectd.
     *
     * @dataProvider does_current_user_count_towards_user_limit_provider
     * @param bool $isadmin
     * @param bool $ismoderator
     * @param bool $expected
     * @covers ::does_current_user_count_towards_user_limit
     */
    public function test_does_current_user_count_towards_user_limit(
        bool $isadmin,
        bool $ismoderator,
        bool $expected
    ): void {
        $stub = $this->getMockBuilder(instance::class)
            ->setMethods([
                'is_admin',
                'is_moderator',
            ])
            ->disableOriginalConstructor()
            ->getMock();

        $stub->method('is_admin')->willReturn($isadmin);
        $stub->method('is_moderator')->willReturn($ismoderator);

        $this->assertEquals($expected, $stub->does_current_user_count_towards_user_limit());
    }

    /**
     * Data provider for the does_current_user_count_towards_user_limit function.
     *
     * @return array
     */
    public function does_current_user_count_towards_user_limit_provider(): array {
        return [
            'Admin does not count' => [true, false, false],
            'Moderator does not count' => [false, true, false],
            'Other users do count' => [false, false, true],
        ];
    }

    /**
     * Ensure that the does_current_user_count_towards_user_limit function works as expectd.
     *
     * @dataProvider get_current_user_password_provider
     * @param bool $isadmin
     * @param bool $ismoderator
     * @param bool $expectedmodpassword
     * @covers ::get_current_user_password
     */
    public function test_get_current_user_password(bool $isadmin, bool $ismoderator, bool $expectedmodpassword): void {
        $stub = $this->getMockBuilder(instance::class)
            ->setMethods([
                'is_admin',
                'is_moderator',
                'get_moderator_password',
                'get_viewer_password',
            ])
            ->disableOriginalConstructor()
            ->getMock();

        $stub->method('is_admin')->willReturn($isadmin);
        $stub->method('is_moderator')->willReturn($ismoderator);
        $stub->method('get_moderator_password')->willReturn('Moderator Password');
        $stub->method('get_viewer_password')->willReturn('Viewer Password');

        if ($expectedmodpassword) {
            $this->assertEquals('Moderator Password', $stub->get_current_user_password());
        } else {
            $this->assertEquals('Viewer Password', $stub->get_current_user_password());
        }
    }

    /**
     * Data provider for the get_current_user_password function.
     *
     * @return array
     */
    public function get_current_user_password_provider(): array {
        return [
            'Admin is a moderator' => [true, false, true],
            'Moderator is a moderator' => [false, true, true],
            'Others are a viewer' => [false, false, false],
        ];
    }

    /**
     * Ensure that the get_current_user_role function works as expected.
     *
     * @dataProvider get_current_user_role_provider
     * @param bool $isadmin
     * @param bool $ismoderator
     * @param bool $expectedmodrole
     * @covers ::get_current_user_role
     */
    public function test_get_current_user_role(bool $isadmin, bool $ismoderator, bool $expectedmodrole): void {
        $stub = $this->getMockBuilder(instance::class)
            ->setMethods([
                'is_admin',
                'is_moderator',
            ])
            ->disableOriginalConstructor()
            ->getMock();

        $stub->method('is_admin')->willReturn($isadmin);
        $stub->method('is_moderator')->willReturn($ismoderator);

        if ($expectedmodrole) {
            $this->assertEquals('MODERATOR', $stub->get_current_user_role());
        } else {
            $this->assertEquals('VIEWER', $stub->get_current_user_role());
        }
    }

    /**
     * Data provider for the get_current_user_role function.
     *
     * @return array
     */
    public function get_current_user_role_provider(): array {
        return [
            'Admin is a moderator' => [true, false, true],
            'Moderator is a moderator' => [false, true, true],
            'Others are a viewer' => [false, false, false],
        ];
    }

    /**
     * Tests for the allow_recording_start_stop function.
     *
     * @dataProvider allow_recording_start_stop_provider
     * @param bool $isrecorded
     * @param bool $showbuttons
     * @param bool $expected
     * @covers ::allow_recording_start_stop
     */
    public function test_allow_recording_start_stop(
        bool $isrecorded,
        bool $showbuttons,
        bool $expected
    ): void {
        $stub = $this->getMockBuilder(instance::class)
            ->setMethods([
                'is_recorded',
                'should_show_recording_button',
            ])
            ->disableOriginalConstructor()
            ->getMock();

        $stub->method('is_recorded')->willReturn($isrecorded);
        $stub->method('should_show_recording_button')->willReturn($showbuttons);

        $this->assertEquals($expected, $stub->allow_recording_start_stop());
    }

    /**
     * Data provider for the allow_recording_start_stop function.
     *
     * @return array
     */
    public function allow_recording_start_stop_provider(): array {
        return [
            'Meeting is not recorded: No start/stop' => [false, false, false],
            'Meeting recorded, Buttons shown: Allow' => [true, true, true],
            'Meeting recorded, Buttons not shown: Deny' => [true, false, false],
        ];
    }


    /**
     * Test get user id (guest or current user)
     * @covers \mod_bigbluebuttonbn\instance::get_user_id
     */
    public function test_get_user_id(): void {
        $this->resetAfterTest();
        $this->setUser(null);
        ['record' => $record ] = $this->get_test_instance();
        $instance = instance::get_from_instanceid($record->id);
        $this->assertEquals(0, $instance->get_user_id());
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);
        $this->assertEquals($user->id, $instance->get_user_id());
    }

    /**
     * Test guest access URL
     *
     * @covers ::get_guest_access_url
     */
    public function test_get_guest_access_url() {
        global $CFG;
        $this->resetAfterTest();
        ['record' => $record ] = $this->get_test_instance(['guestallowed' => true]);
        $CFG->bigbluebuttonbn['guestaccess_enabled'] = 1;
        $instance = instance::get_from_instanceid($record->id);
        $this->assertNotEmpty($instance->get_guest_access_url());
    }

    /**
     * Test guest allowed flag
     *
     * @covers ::is_guest_allowed
     */
    public function test_is_guest_allowed() {
        global $CFG;
        $this->resetAfterTest();
        ['record' => $record ] = $this->get_test_instance(['guestallowed' => true]);
        $CFG->bigbluebuttonbn['guestaccess_enabled'] = 1;
        $instance = instance::get_from_instanceid($record->id);
        $this->assertTrue($instance->is_guest_allowed());
        $CFG->bigbluebuttonbn['guestaccess_enabled'] = 0;
        $this->assertFalse($instance->is_guest_allowed());
    }

    /**
     * Test private method get_instance_info_retriever
     *
     * @covers ::get_instance_info_retriever
     */
    public function test_get_instance_info_retriever() {
        $this->resetAfterTest();
        [
            'record' => $record,
            'cm' => $cm,
        ] = $this->get_test_instance();
        $instance = instance::get_from_instanceid($record->id);
        $instancereflection = new \ReflectionClass($instance);
        $getinstanceinforetriever = $instancereflection->getMethod('get_instance_info_retriever');
        $getinstanceinforetriever->setAccessible(true);
        $this->assertInstanceOf('\mod_bigbluebuttonbn\instance',
            $getinstanceinforetriever->invoke($instance, $record->id, instance::IDTYPE_INSTANCEID));
        $this->assertEquals($cm->id, $instance->get_cm_id());
    }

    /**
     * Test guest access password
     *
     * @covers ::get_guest_access_password
     */
    public function get_guest_access_password() {
        global $CFG;
        $this->resetAfterTest();
        ['record' => $record ] = $this->get_test_instance(['guestallowed' => true]);
        $CFG->bigbluebuttonbn['guestaccess_enabled'] = 1;
        $instance = instance::get_from_instanceid($record->id);
        $this->assertNotEmpty($instance->get_guest_access_password());
    }
}
