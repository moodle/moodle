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

namespace mod_bigbluebuttonbn\completion;

use completion_info;
use context_module;
use mod_bigbluebuttonbn\instance;
use mod_bigbluebuttonbn\logger;
use mod_bigbluebuttonbn\meeting;
use mod_bigbluebuttonbn\test\testcase_helper_trait;

/**
 * Tests for Big Blue Button Completion.
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2021 - present, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Laurent David (laurent@call-learning.fr)
 * @covers \mod_bigbluebuttonbn\completion\custom_completion
 */
class completion_test extends \advanced_testcase {
    use testcase_helper_trait;

    /**
     * Setup basic
     */
    public function setUp(): void {
        parent::setUp();
        $this->initialise_mock_server();
        set_config('enablecompletion', true); // Enable completion for all tests.
    }

    /**
     * Completion with no rules: the completion is completed as soons as we view the course.
     */
    public function test_get_completion_state_no_rules() {
        $this->resetAfterTest();
        list($bbactivitycontext, $bbactivitycm, $bbactivity) = $this->create_instance();

        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        $completion = new custom_completion($bbactivitycm, $user->id);
        $result = $completion->get_overall_completion_state();
        // No custom rules so complete by default.
        $this->assertEquals(COMPLETION_COMPLETE, $result);
    }

    /**
     * Completion with no rules and join meeting
     */
    public function test_get_completion_state_no_rules_and_join_meeting() {
        $this->resetAfterTest();
        list($bbactivitycontext, $bbactivitycm, $bbactivity) = $this->create_instance();

        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        // Now create a couple of logs.
        $instance = instance::get_from_instanceid($bbactivity->id);
        logger::log_meeting_joined_event($instance, 0);
        // No custom rules and we joined once so complete.
        $completion = new custom_completion($bbactivitycm, $user->id);
        $result = $completion->get_overall_completion_state();
        $this->assertEquals(COMPLETION_COMPLETE, $result);
    }

    /**
     * With state incomplete
     */
    public function test_get_completion_state_incomplete() {
        $this->resetAfterTest();

        list($bbactivitycontext, $bbactivitycm, $bbactivity) = $this->create_instance();

        $bbactivitycm->override_customdata('customcompletionrules', [
            'completionengagementchats' => '1',
            'completionattendance' => '1'
        ]);

        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        $completion = new custom_completion($bbactivitycm, $user->id);
        $result = $completion->get_overall_completion_state();
        $this->assertEquals(COMPLETION_INCOMPLETE, $result);
    }

    /**
     * With state complete
     */
    public function test_get_completion_state_complete() {
        $this->resetAfterTest();

        list($bbactivitycontext, $bbactivitycm, $bbactivity) = $this->create_instance(
            $this->get_course(),
            [
                'completion' => '2',
                'completionengagementtalks' => 2,
                'completionengagementchats' => 2,
                'completionattendance' => 15
            ]
        );
        $instance = instance::get_from_instanceid($bbactivity->id);

        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        // Add a couple of fake logs.
        $overrides = ['meetingid' => $bbactivity->meetingid];
        $meta = [
            'origin' => 0,
            'data' => [
                'duration' => 300, // 300 seconds, i.e 5 mins.
                'engagement' => [
                    'chats' => 2,
                    'talks' => 2,
                ],
            ],
        ];

        // We setup a couple of logs as per engagement and duration.
        logger::log_event_summary($instance, $overrides, $meta);
        logger::log_event_summary($instance, $overrides, $meta);
        $completion = new custom_completion($bbactivitycm, $user->id);
        $result = $completion->get_overall_completion_state();
        $this->assertEquals(COMPLETION_INCOMPLETE, $result);

        // Now we have 15 mins.
        logger::log_event_summary($instance, $overrides, $meta);
        // Now that the meeting was joined, it should be complete.
        $completion = new custom_completion($bbactivitycm, $user->id);
        $result = $completion->get_overall_completion_state();
        $this->assertEquals(COMPLETION_COMPLETE, $result);
    }

    /**
     * No rule description but active
     */
    public function test_mod_bigbluebuttonbn_get_completion_active_rule_descriptions() {
        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);
        // Two activities, both with automatic completion. One has the 'completionsubmit' rule, one doesn't.
        // Inspired from the same test in forum.
        list($bbactivitycontext, $cm1, $bbactivity) = $this->create_instance($this->get_course(),
            ['completion' => '2']);
        $cm1->override_customdata('customcompletionrules', [
            'completionattendance' => '1'
        ]);
        list($bbactivitycontext, $cm2, $bbactivity) = $this->create_instance($this->get_course(),
            ['completion' => '2']);
        $cm2->override_customdata('customcompletionrules', [
            'completionattendance' => '0'
        ]);

        $completioncm1 = new custom_completion($cm1, $user->id);
        // TODO: check the return value here as there might be an issue with the function compared to the forum for example.
        $this->assertEquals(
            [
                'completionengagementchats' => get_string('completionengagementchats_desc', 'mod_bigbluebuttonbn', 1),
                'completionengagementtalks' => get_string('completionengagementtalks_desc', 'mod_bigbluebuttonbn', 1),
                'completionattendance' => get_string('completionattendance_desc', 'mod_bigbluebuttonbn', 1),
                'completionengagementraisehand' => get_string('completionengagementraisehand_desc', 'mod_bigbluebuttonbn', 1),
                'completionengagementpollvotes' => get_string('completionengagementpollvotes_desc', 'mod_bigbluebuttonbn', 1),
                'completionengagementemojis' => get_string('completionengagementemojis_desc', 'mod_bigbluebuttonbn', 1)
            ],
            $completioncm1->get_custom_rule_descriptions());
        $completioncm2 = new custom_completion($cm2, $user->id);
        $this->assertEquals(
            [
                'completionengagementchats' => get_string('completionengagementchats_desc', 'mod_bigbluebuttonbn', 1),
                'completionengagementtalks' => get_string('completionengagementtalks_desc', 'mod_bigbluebuttonbn', 1),
                'completionattendance' => get_string('completionattendance_desc', 'mod_bigbluebuttonbn', 0),
                'completionengagementraisehand' => get_string('completionengagementraisehand_desc', 'mod_bigbluebuttonbn', 1),
                'completionengagementpollvotes' => get_string('completionengagementpollvotes_desc', 'mod_bigbluebuttonbn', 1),
                'completionengagementemojis' => get_string('completionengagementemojis_desc', 'mod_bigbluebuttonbn', 1)
            ], $completioncm2->get_custom_rule_descriptions());
    }

    /**
     * Completion View
     */
    public function test_view() {
        $this->resetAfterTest();
        $this->setAdminUser();
        list($bbactivitycontext, $bbactivitycm, $bbactivity) = $this->create_instance(
            null, ['completion' => 2, 'completionview' => 1]
        );

        // Trigger and capture the event.
        $sink = $this->redirectEvents();

        // Check completion before viewing.
        $completion = new completion_info($this->get_course());
        $completiondata = $completion->get_data($bbactivitycm);
        $this->assertEquals(0, $completiondata->viewed);
        $this->assertEquals(COMPLETION_NOT_VIEWED, $completiondata->completionstate);

        bigbluebuttonbn_view($bbactivity, $this->get_course(), $bbactivitycm, context_module::instance($bbactivitycm->id));

        $events = $sink->get_events();
        $this->assertTrue(count($events) > 1); // TODO : Here we have the module completion event triggered twice.
        // this might be a bug from 4.0 core and will need some further investigation.
        $event = reset($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_bigbluebuttonbn\event\course_module_viewed', $event);
        $this->assertEquals($bbactivitycontext, $event->get_context());
        $url = new \moodle_url('/mod/bigbluebuttonbn/view.php', ['id' => $bbactivitycontext->instanceid]);
        $this->assertEquals($url, $event->get_url());
        $this->assertEventContextNotUsed($event);
        $this->assertNotEmpty($event->get_name());

        // Check completion status.
        $completion = new completion_info($this->get_course());
        $completiondata = $completion->get_data($bbactivitycm);
        $this->assertEquals(1, $completiondata->viewed);
        $this->assertEquals(COMPLETION_COMPLETE, $completiondata->completionstate);
    }

    /**
     * Completion with no rules and join meeting
     *
     * @param array $customcompletionrules
     * @param array $events
     * @param int $expectedstate
     * @dataProvider custom_completion_data_provider
     */
    public function test_get_completion_with_events(array $customcompletionrules, array $events, int $expectedstate) {
        $this->resetAfterTest();
        list($bbactivitycontext, $bbactivitycm, $bbactivity) = $this->create_instance(
            $this->get_course(),
            [
                'completion' => '2',
            ]
        );
        $bbactivitycm->override_customdata('customcompletionrules', $customcompletionrules);
        $plugingenerator = $this->getDataGenerator()->get_plugin_generator('mod_bigbluebuttonbn');

        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        // Now create a couple of events.
        $instance = instance::get_from_instanceid($bbactivity->id);
        set_config('bigbluebuttonbn_meetingevents_enabled', true);
        $meeting = $plugingenerator->create_meeting([
            'instanceid' => $instance->get_instance_id(),
            'groupid' => $instance->get_group_id(),
            'participants' => json_encode([$user->id])
        ]);
        foreach ($events as $edesc) {
            $plugingenerator->add_meeting_event($user, $instance, $edesc->name, $edesc->data ?? '');
        }
        $result = $plugingenerator->send_all_events($instance);
        $this->assertNotEmpty($result->data);
        $data = json_decode(json_encode($result->data));
        meeting::meeting_events($instance, $data);
        $completion = new custom_completion($bbactivitycm, $user->id);
        $result = $completion->get_overall_completion_state();
        $this->assertEquals($expectedstate, $result);
    }

    /**
     * Data generator
     *
     * @return array[]
     */
    public function custom_completion_data_provider() {
        return [
            'simple' => [
                'customcompletionrules' => [
                    'completionengagementtalks' => 1,
                    'completionengagementchats' => 1,
                ],
                'events' => [
                    (object) ['name' => 'talks'],
                    (object) ['name' => 'chats']
                ],
                'expectedstate' => COMPLETION_COMPLETE
            ],
            'not right events' => [
                'customcompletionrules' => [
                    'completionengagementchats' => 1,
                ],
                'events' => [
                    (object) ['name' => 'talks']
                ],
                'expectedstate' => COMPLETION_INCOMPLETE
            ],
            'attendance' => [
                'customcompletionrules' => [
                    'completionattendance' => 1,
                ],
                'events' => [
                    (object) ['name' => 'talks'],
                    (object) ['name' => 'attendance', 'data' => '70']
                ],
                'expectedstate' => COMPLETION_COMPLETE
            ]
        ];
    }
}

