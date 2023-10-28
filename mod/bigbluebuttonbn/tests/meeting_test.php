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
 * Meeting test.
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2018 - present, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Jesus Federico  (jesus [at] blindsidenetworks [dt] com)
 */

namespace mod_bigbluebuttonbn;

use mod_bigbluebuttonbn\test\testcase_helper_trait;

/**
 * Meeting tests class.
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2018 - present, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Jesus Federico  (jesus [at] blindsidenetworks [dt] com)
 * @covers \mod_bigbluebuttonbn\meeting
 * @coversDefaultClass \mod_bigbluebuttonbn\meeting
 */
class meeting_test extends \advanced_testcase {
    use testcase_helper_trait;

    /**
     * Setup Test
     */
    public function setUp(): void {
        parent::setUp();
        $this->initialise_mock_server();
        // We do not force the group mode so we can change the activity group mode during test.
        $this->course = $this->getDataGenerator()->create_course(['groupmode' => SEPARATEGROUPS]);
        $this->getDataGenerator()->create_group(['name' => 'G1', 'courseid' => $this->course->id]);
        $this->getDataGenerator()->create_group(['name' => 'G2', 'courseid' => $this->course->id]);
    }

    /**
     * Get a list of possible test (dataprovider)
     *
     * @return array[]
     */
    public function get_instance_types_meeting_info(): array {
        return [
            'Instance Type ALL - No Group' => [
                'type' => instance::TYPE_ALL,
                'groupname' => null,
                'groupmode' => NOGROUPS,
                'canjoin' => ['useringroup' => true, 'usernotingroup' => true],
            ],
            'Instance Type ALL - Group 1 - Visible groups' => [
                'type' => instance::TYPE_ALL,
                'groupname' => 'G1',
                'groupmode' => VISIBLEGROUPS,
                'canjoin' => ['useringroup' => true, 'usernotingroup' => true],
            ],
            'Instance Type ALL - Group 1 - Separate groups' => [
                'type' => instance::TYPE_ALL,
                'groupname' => 'G1',
                'groupmode' => SEPARATEGROUPS,
                'canjoin' => ['useringroup' => true, 'usernotingroup' => false],
            ],
            'Instance Type ROOM Only - No Group' => [
                'type' => instance::TYPE_ROOM_ONLY,
                'groupname' => null,
                'groupmode' => NOGROUPS,
                'canjoin' => ['useringroup' => true, 'usernotingroup' => true],
            ],
            'Instance Type ROOM Only - Group 1 - Visible groups' => [
                'type' => instance::TYPE_ROOM_ONLY,
                'groupname' => 'G1',
                'groupmode' => VISIBLEGROUPS,
                'canjoin' => ['useringroup' => true, 'usernotingroup' => true],
            ],
            'Instance Type ROOM Only - Group 1 - Separate groups' => [
                'type' => instance::TYPE_ROOM_ONLY,
                'groupname' => 'G1',
                'groupmode' => SEPARATEGROUPS,
                'canjoin' => ['useringroup' => true, 'usernotingroup' => false],
            ],
            'Instance Type Recording Only - No Group' => [
                'type' => instance::TYPE_RECORDING_ONLY,
                'groupname' => null,
                'groupmode' => NOGROUPS,
                'canjoin' => ['useringroup' => false, 'usernotingroup' => false]
            ],
            'Instance Type Recording Only - Group 1' => [
                'type' => instance::TYPE_RECORDING_ONLY,
                'groupname' => 'G1',
                'groupmode' => VISIBLEGROUPS,
                'canjoin' => ['useringroup' => false, 'usernotingroup' => false]
            ]
        ];
    }

    /**
     * Test that create meeing is working for all types.
     *
     * @dataProvider get_instance_types_meeting_info
     * @param int $type
     * @param string|null $groupname
     * @covers ::create_meeting
     * @covers ::create_meeting_data
     * @covers ::create_meeting_metadata
     */
    public function test_create_meeting(int $type, ?string $groupname) {
        $this->resetAfterTest();
        [$meeting, $useringroup, $usernotingroup, $groupid, $activity] =
            $this->prepare_meeting($type, $groupname, SEPARATEGROUPS, false);
        $meeting->create_meeting();
        $meetinginfo = $meeting->get_meeting_info();
        $this->assertNotNull($meetinginfo);
        $this->assertEquals($activity->id, $meetinginfo->bigbluebuttonbnid);
        $this->assertFalse($meetinginfo->statusrunning);
        $this->assertStringContainsString("is ready", $meetinginfo->statusmessage);
        $this->assertEquals($groupid, $meetinginfo->groupid);
    }

    /**
     * Test for get meeting info for all types
     *
     * @param int $type
     * @param string|null $groupname
     * @dataProvider get_instance_types_meeting_info
     * @covers ::get_meeting_info
     * @covers ::do_get_meeting_info
     */
    public function test_get_meeting_info(int $type, ?string $groupname) {
        $this->resetAfterTest();
        [$meeting, $useringroup, $usernotingroup, $groupid, $activity] = $this->prepare_meeting($type, $groupname);
        $meetinginfo = $meeting->get_meeting_info();
        $this->assertNotNull($meetinginfo);
        $this->assertEquals($activity->id, $meetinginfo->bigbluebuttonbnid);
        $this->assertTrue($meetinginfo->statusrunning);
        $this->assertStringContainsString("in progress", $meetinginfo->statusmessage);
        $this->assertEquals($groupid, $meetinginfo->groupid);
        $meeting->end_meeting();
        $meeting->update_cache();
        $meetinginfo = $meeting->get_meeting_info();
        $this->assertFalse($meetinginfo->statusrunning);

        if ($type == instance::TYPE_ALL) {
            $this->assertTrue($meetinginfo->features['showroom']);
            $this->assertTrue($meetinginfo->features['showrecordings']);
        } else if ($type == instance::TYPE_ROOM_ONLY) {
            $this->assertTrue($meetinginfo->features['showroom']);
            $this->assertFalse($meetinginfo->features['showrecordings']);
        } else if ($type == instance::TYPE_RECORDING_ONLY) {
            $this->assertFalse($meetinginfo->features['showroom']);
            $this->assertTrue($meetinginfo->features['showrecordings']);
        }
    }

    /**
     * Test can join is working for all types
     *
     * @param int $type
     * @param string|null $groupname
     * @param int $groupmode
     * @param array $canjoin
     * @dataProvider get_instance_types_meeting_info
     * @covers ::can_join
     */
    public function test_can_join(int $type, ?string $groupname, int $groupmode, array $canjoin) {
        $this->resetAfterTest();
        [$meeting, $useringroup, $usernotingroup, $groupid, $activity] = $this->prepare_meeting($type, $groupname, $groupmode);
        $this->setUser($useringroup);
        $meeting->update_cache();
        $this->assertEquals($canjoin['useringroup'], $meeting->can_join());
        if ($meeting->can_join()) {
            $meetinginfo = $meeting->get_meeting_info();
            $this->assertStringContainsString("The session is in progress.", $meetinginfo->statusmessage);
        }
        if ($groupname) {
            $this->setUser($usernotingroup);
            $meeting->update_cache();
            $this->assertEquals($canjoin['usernotingroup'], $meeting->can_join());
        }
    }

    /**
     * Test can join is working if opening/closing time are set
     *
     * @param int $type
     * @param string|null $groupname
     * @param int $groupmode
     * @param array $canjoin
     * @param array $dates
     * @dataProvider get_data_can_join_with_dates
     * @covers ::can_join
     */
    public function test_can_join_with_dates(int $type, ?string $groupname, int $groupmode, array $canjoin, array $dates) {
        // Apply the data provider relative values to now.
        array_walk($dates, function(&$val) {
            $val = time() + $val;
        });
        $this->resetAfterTest();
        [$meeting, $useringroup, $usernotingroup, $groupid, $activity] =
            $this->prepare_meeting($type, $groupname, $groupmode, true, $dates);
        $this->setUser($useringroup);
        $meeting->update_cache();
        $this->assertEquals($canjoin['useringroup'], $meeting->can_join());
        // We check that admin can not join outside opening/closing times either.
        $this->setAdminUser();
        $this->assertEquals(false, $meeting->can_join());
        if ($groupname) {
            $this->setUser($usernotingroup);
            $meeting->update_cache();
            $this->assertEquals($canjoin['usernotingroup'], $meeting->can_join());
            $this->setAdminUser();
            $this->assertEquals(false, $meeting->can_join());
        }
    }

    /**
     * Test can join is working if the "Wait for moderator to join" setting is set and a moderator has not yet joined.
     *
     * @covers ::join
     * @covers ::join_meeting
     */
    public function test_join_wait_for_moderator_not_joined() {
        $this->resetAfterTest();

        $this->setAdminUser();
        $bbbgenerator = $this->getDataGenerator()->get_plugin_generator('mod_bigbluebuttonbn');
        $student = $this->getDataGenerator()->create_and_enrol($this->get_course());
        $meetinginfo = [
            'course' => $this->get_course()->id,
            'type' => instance::TYPE_ALL,
            'wait' => 1,
        ];
        $activity = $bbbgenerator->create_instance($meetinginfo, [
            'wait' => 1,
        ]);
        $instance = instance::get_from_instanceid($activity->id);
        $meeting = new meeting($instance);

        // The moderator has not joined.
        $this->setUser($student);
        $meeting->update_cache();
        $this->expectException(\mod_bigbluebuttonbn\local\exceptions\meeting_join_exception::class);
        meeting::join_meeting($instance);
    }

    /**
     * Test can join is working if the "Wait for moderator to join" setting is set and a moderator has already joined.
     *
     * @covers ::join
     * @covers ::join_meeting
     */
    public function test_join_wait_for_moderator_is_joined() {
        $this->resetAfterTest();

        $this->setAdminUser();
        $bbbgenerator = $this->getDataGenerator()->get_plugin_generator('mod_bigbluebuttonbn');
        $moderator = $this->getDataGenerator()->create_and_enrol($this->get_course(), 'editingteacher');
        $student = $this->getDataGenerator()->create_and_enrol($this->get_course());
        $meetinginfo = [
            'course' => $this->get_course()->id,
            'type' => instance::TYPE_ALL,
            'wait' => 1,
            'moderators' => 'role:editingteacher',
        ];
        $activity = $bbbgenerator->create_instance($meetinginfo, [
            'wait' => 1,
        ]);
        $instance = instance::get_from_instanceid($activity->id);
        $meeting = new meeting($instance);
        $bbbgenerator->create_meeting([
            'instanceid' => $instance->get_instance_id(),
        ]);

        $this->setUser($moderator);
        $meeting->update_cache();
        $joinurl = $meeting->join(logger::ORIGIN_BASE);
        $this->assertIsString($joinurl);
        $this->join_meeting($joinurl);
        $meeting->update_cache();
        $this->assertCount(1, $meeting->get_attendees());

        // The student can now join the meeting as a moderator is present.
        $this->setUser($student);
        $joinurl = $meeting->join(logger::ORIGIN_BASE);
        $this->assertIsString($joinurl);
    }

    /**
     * Test can join is working if the "user limit" setting is set and reached.
     *
     * @covers ::join
     * @covers ::join_meeting
     */
    public function test_join_user_limit_reached() {
        $this->resetAfterTest();
        set_config('bigbluebuttonbn_userlimit_editable', true);
        $this->setAdminUser();
        $bbbgenerator = $this->getDataGenerator()->get_plugin_generator('mod_bigbluebuttonbn');
        $moderator = $this->getDataGenerator()->create_and_enrol($this->get_course(), 'editingteacher');
        $student1 = $this->getDataGenerator()->create_and_enrol($this->get_course());
        $student2 = $this->getDataGenerator()->create_and_enrol($this->get_course());
        $meetinginfo = [
            'course' => $this->get_course()->id,
            'type' => instance::TYPE_ALL,
            'userlimit' => 2,
        ];
        $activity = $bbbgenerator->create_instance($meetinginfo, [
            'userlimit' => 2,
        ]);
        $instance = instance::get_from_instanceid($activity->id);
        $meeting = new meeting($instance);
        $bbbgenerator->create_meeting([
            'instanceid' => $instance->get_instance_id(),
        ]);
        // Moderator joins the meeting.
        $this->setUser($moderator);
        $this->join_meeting($meeting->join(logger::ORIGIN_BASE));
        $meeting->update_cache();
        $this->assertEquals(1, $meeting->get_participant_count());

        // Student1 joins the meeting.
        $this->setUser($student1);
        $this->join_meeting($meeting->join(logger::ORIGIN_BASE));
        $meeting->update_cache();
        $this->assertEquals(2, $meeting->get_participant_count());
        $this->assertTrue($instance->has_user_limit_been_reached($meeting->get_participant_count()));

        // Student2 tries to join but the limit has been reached.
        $this->setUser($student2);
        $meeting->update_cache();
        $this->assertFalse($meeting->can_join());
        $this->expectException(\mod_bigbluebuttonbn\local\exceptions\meeting_join_exception::class);
        meeting::join_meeting($instance);
    }

    /**
     * Test that attendees returns the right list of attendees
     *
     * @covers ::get_attendees
     */
    public function test_get_attendees() {
        $this->resetAfterTest();
        [$meeting, $useringroup, $usernotingroup, $groupid, $activity] =
            $this->prepare_meeting(instance::TYPE_ALL, null, NOGROUPS, true);
        $this->setUser($useringroup);
        $this->join_meeting($meeting->join(logger::ORIGIN_BASE));
        $meeting->update_cache();
        $this->assertCount(1, $meeting->get_attendees());
        $otheruser = $this->getDataGenerator()->create_and_enrol($this->get_course());
        $this->setUser($otheruser);
        $meeting->update_cache();
        $this->join_meeting($meeting->join(logger::ORIGIN_BASE));
        $meeting->update_cache();
        $this->assertCount(2, $meeting->get_attendees());
    }

    /**
     * Test that attendees returns the right list of attendees
     *
     * @covers ::get_attendees
     */
    public function test_participant_count() {
        $this->resetAfterTest();
        [$meeting, $useringroup, $usernotingroup, $groupid, $activity] =
            $this->prepare_meeting(instance::TYPE_ALL, null, NOGROUPS, true);
        $this->setUser($useringroup);
        $this->join_meeting($meeting->join(logger::ORIGIN_BASE));
        $meeting->update_cache();
        $meetinginfo = $meeting->get_meeting_info();
        $this->assertEquals(1, $meetinginfo->participantcount);
        $this->assertEquals(1, $meetinginfo->totalusercount);
        $this->assertEquals(0, $meetinginfo->moderatorcount);
        $this->setUser($usernotingroup);
        $this->join_meeting($meeting->join(logger::ORIGIN_BASE));
        $meeting->update_cache();
        $meetinginfo = $meeting->get_meeting_info();
        $this->assertEquals(2, $meetinginfo->participantcount);
        $this->assertEquals(2, $meetinginfo->totalusercount);
        $this->assertEquals(0, $meetinginfo->moderatorcount);
        $this->setAdminUser();
        $this->join_meeting($meeting->join(logger::ORIGIN_BASE));
        $meeting->update_cache();
        $meetinginfo = $meeting->get_meeting_info();
        $this->assertEquals(2, $meetinginfo->participantcount);
        $this->assertEquals(3, $meetinginfo->totalusercount);
        $this->assertEquals(1, $meetinginfo->moderatorcount);
    }
    /**
     * Send a join meeting API CALL
     *
     * @param string $url
     */
    protected function join_meeting(string $url) {
        $curl = new \curl();
        $url = new \moodle_url($url);
        $curl->get($url->out_omit_querystring(), $url->params());
    }

    /**
     * Get a list of possible test (dataprovider)
     *
     * @return array[]
     */
    public function get_data_can_join_with_dates(): array {
        return [
            'Instance Type ALL - No Group - Closed in past' => [
                'type' => instance::TYPE_ALL,
                'groupname' => null,
                'groupmode' => NOGROUPS,
                'canjoin' => ['useringroup' => false, 'usernotingroup' => false],
                'dates' => ['openingtime' => -7200, 'closingtime' => -3600]
            ],
            'Instance Type ALL - No Group - Open in future' => [
                'type' => instance::TYPE_ALL,
                'groupname' => null,
                'groupmode' => NOGROUPS,
                'canjoin' => ['useringroup' => false, 'usernotingroup' => false],
                'dates' => ['openingtime' => 3600, 'closingtime' => 7200]
            ],
        ];
    }

    /**
     * Helper to prepare for a meeting
     *
     * @param int $type
     * @param string|null $groupname
     * @param int $groupmode
     * @param bool $createmeeting
     * @param array $dates
     * @return array
     */
    protected function prepare_meeting(int $type, ?string $groupname, int $groupmode = SEPARATEGROUPS, bool $createmeeting = true,
        array $dates = []) {
        $this->setAdminUser();
        $bbbgenerator = $this->getDataGenerator()->get_plugin_generator('mod_bigbluebuttonbn');
        $groupid = 0;
        $useringroup = $this->getDataGenerator()->create_and_enrol($this->get_course());
        $usernotingroup = $this->getDataGenerator()->create_and_enrol($this->get_course());
        if (!empty($groupname)) {
            $groupid = groups_get_group_by_name($this->get_course()->id, $groupname);
            $this->getDataGenerator()->create_group_member(['groupid' => $groupid, 'userid' => $useringroup->id]);
        }
        $meetinginfo = [
            'course' => $this->get_course()->id,
            'type' => $type
        ];
        if ($dates) {
            $meetinginfo = array_merge($meetinginfo, $dates);
        };
        $activity = $bbbgenerator->create_instance($meetinginfo, ['groupmode' => $groupmode]);
        $instance = instance::get_from_instanceid($activity->id);
        if ($groupid) {
            $instance->set_group_id($groupid);
        }
        if ($createmeeting) {
            // Create the meetings on the mock server, so we can join it as a simple user.
            $bbbgenerator->create_meeting([
                'instanceid' => $instance->get_instance_id(),
                'groupid' => $instance->get_group_id()
            ]);
        }
        $meeting = new meeting($instance);
        return [$meeting, $useringroup, $usernotingroup, $groupid, $activity];
    }
}
