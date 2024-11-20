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
namespace mod_bigbluebuttonbn\local\helpers;

use mod_bigbluebuttonbn\instance;
use mod_bigbluebuttonbn\logger;
use mod_bigbluebuttonbn\test\testcase_helper_trait;

/**
 * User information printing test
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2022 - present, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Laurent David (laurent@call-learning.fr)
 * @covers \mod_bigbluebuttonbn\local\helpers\user_info
 * @coversDefaultClass \mod_bigbluebuttonbn\local\helpers\user_info
 */
class user_info_test extends \advanced_testcase {
    use testcase_helper_trait;

    /**
     * Test user info outline
     *
     * @return void
     */
    public function test_get_user_info_outline(): void {
        $this->initialise_mock_server();
        $this->resetAfterTest();

        $generator = $this->getDataGenerator();
        $user = $generator->create_and_enrol($this->get_course());
        list($bbactivitycontext, $bbactivitycm, $bbactivity) = $this->create_instance();
        $this->setUser($user);

        // Now create a couple of logs.
        $instance = instance::get_from_instanceid($bbactivity->id);
        $recordings = $this->create_recordings_for_instance($instance, [['name' => "Pre-Recording 1"]]);
        logger::log_meeting_joined_event($instance, 0);
        logger::log_recording_played_event($instance, $recordings[0]->id);
        [$logjoins, $logtimes] = user_info::get_user_info_outline($this->get_course(), $user, $bbactivitycm);
        $this->assertEquals([
            '1 meeting(s)',
            '1 recording(s) played'
        ], $logjoins);
        $this->assertCount(2, $logtimes);
    }

    /**
     * Test user info outline with several logs
     *
     * @return void
     */
    public function test_get_user_info_outline_several_logs(): void {
        $this->resetAfterTest();

        $generator = $this->getDataGenerator();
        $user = $generator->create_and_enrol($this->get_course());
        list($bbactivitycontext, $bbactivitycm, $bbactivity) = $this->create_instance();
        $this->setUser($user);

        // Now create a couple of logs.
        $instance = instance::get_from_instanceid($bbactivity->id);
        logger::log_meeting_joined_event($instance, 0);
        logger::log_meeting_joined_event($instance, 0);

        [$logjoins, $logtimes] = user_info::get_user_info_outline($this->get_course(), $user, $bbactivitycm);
        $this->assertEquals([
            '2 meeting(s)',
        ], $logjoins);
        $this->assertCount(1, $logtimes);
    }

    /**
     * Test user info outline for view events
     *
     * @return void
     */
    public function test_get_user_info_outline_view(): void {
        $this->initialise_mock_server();
        $this->resetAfterTest();

        $generator = $this->getDataGenerator();
        $user = $generator->create_and_enrol($this->get_course());
        list($bbactivitycontext, $bbactivitycm, $bbactivity) = $this->create_instance(
            null,
            ['completion' => 2, 'completionview' => 1]);
        $this->setUser($user);

        // Now create a couple of logs.
        $instance = instance::get_from_instanceid($bbactivity->id);
        // View it twice.
        bigbluebuttonbn_view($instance->get_instance_data(), $instance->get_course(), $instance->get_cm(),
            $instance->get_context());
        bigbluebuttonbn_view($instance->get_instance_data(), $instance->get_course(), $instance->get_cm(),
            $instance->get_context());
        [$logjoins, $logtimes] = user_info::get_user_info_outline($this->get_course(), $user, $bbactivitycm);
        $this->assertEquals([
            'viewed',
        ], $logjoins);
        $this->assertCount(1, $logtimes);
    }
}
