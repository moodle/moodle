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

namespace core_xapi\privacy;

use core_privacy\tests\provider_testcase;
use core_privacy\local\request\transform;
use core_xapi\privacy\provider;
use core_xapi\local\statement\item_activity;
use core_xapi\test_helper;

/**
 * Privacy tests for core_xapi.
 *
 * @package    core_xapi
 * @category   test
 * @copyright  2023 Sara Arjona (sara@moodle.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \core_xapi\privacy\provider
 */
class provider_test extends provider_testcase {

    /**
     * Setup to ensure that fixtures are loaded.
     */
    public static function setUpBeforeClass(): void {
        global $CFG;
        require_once($CFG->dirroot.'/lib/xapi/tests/helper.php');
        parent::setUpBeforeClass();
    }

    /**
     * Helper to set up some sample data.
     *
     * @return array Array with the users that have been created.
     */
    protected function set_up_data(): array {
        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();
        $user3 = self::getDataGenerator()->create_user();

        // Add a few xAPI state records to database.
        $context = \context_system::instance();
        $cid = $context->id;
        $this->setUser($user1);
        test_helper::create_state(['activity' => item_activity::create_from_id($context->id)], true);
        test_helper::create_state(['activity' => item_activity::create_from_id('2')], true);
        test_helper::create_state(['activity' => item_activity::create_from_id('3'), 'component' => 'mod_h5pactivity'], true);
        $this->setUser($user2);
        test_helper::create_state(['activity' => item_activity::create_from_id($context->id)], true);
        test_helper::create_state(['activity' => item_activity::create_from_id('2')], true);
        test_helper::create_state(['activity' => item_activity::create_from_id('4')], true);
        test_helper::create_state(['activity' => item_activity::create_from_id('5')], true);
        $this->setUser($user3);
        test_helper::create_state(['activity' => item_activity::create_from_id($cid), 'component' => 'mod_h5pactivity'], true);

        return [$user1, $user2, $user3];
    }

    /**
     * Test confirming that contexts of xapi items can be added to the contextlist.
     */
    public function test_add_contexts_for_userid(): void {
        $this->resetAfterTest();

        // Scenario.
        list($user1, $user2) = $this->set_up_data();

        // Ask the xapi privacy api to export contexts for xapi of the type we just created, for user1.
        $contextlist = new \core_privacy\local\request\contextlist();
        provider::add_contexts_for_userid($contextlist, $user1->id, 'fake_component');
        $this->assertCount(2, $contextlist->get_contextids());

        $contextlist = new \core_privacy\local\request\contextlist();
        provider::add_contexts_for_userid($contextlist, $user1->id, 'mod_h5pactivity');
        $this->assertCount(1, $contextlist->get_contextids());

        // Ask the xapi privacy api to export contexts for xapi of the type we just created, for user2.
        $contextlist = new \core_privacy\local\request\contextlist();
        provider::add_contexts_for_userid($contextlist, $user2->id, 'fake_component');
        $this->assertCount(4, $contextlist->get_contextids());

        $contextlist = new \core_privacy\local\request\contextlist();
        provider::add_contexts_for_userid($contextlist, $user2->id, 'mod_h5pactivity');
        $this->assertCount(0, $contextlist->get_contextids());
    }

    /**
     * Test confirming that user ID's of xapi states can be added to the userlist.
     */
    public function test_add_userids_for_context(): void {
        global $DB;

        $this->resetAfterTest();

        // Scenario.
        list($user1, $user2, $user3) = $this->set_up_data();
        $this->assertEquals(3, $DB->count_records('xapi_states', ['userid' => $user1->id]));
        $this->assertEquals(4, $DB->count_records('xapi_states', ['userid' => $user2->id]));
        $this->assertEquals(1, $DB->count_records('xapi_states', ['userid' => $user3->id]));
        $systemcontext = \context_system::instance();

        // Ask the xapi privacy api to export userids for xapi states of the type we just created, in the system context.
        $userlist = new \core_privacy\local\request\userlist($systemcontext, 'fake_component');
        provider::add_userids_for_context($userlist, 'fake_component');
        // Only user1 and user2 should be returned, because user3 has a different component for the system context.
        $this->assertCount(2, $userlist->get_userids());
        $expected = [
            $user1->id,
            $user2->id,
        ];
        $this->assertEqualsCanonicalizing($expected, $userlist->get_userids());

        // Ask the xapi privacy api to export userids for xapi states of the type we just created for a different component.
        $userlist = new \core_privacy\local\request\userlist($systemcontext, 'mod_h5pactivity');
        provider::add_userids_for_context($userlist, 'mod_h5pactivity');
        // Only user3 should be returned, because the others have a different component for the system context.
        $this->assertCount(1, $userlist->get_userids());
        $expected = [$user3->id];
        $this->assertEqualsCanonicalizing($expected, $userlist->get_userids());

        // Ask the xapi privacy api to export userids xapi states for an empty component.
        $userlist = new \core_privacy\local\request\userlist($systemcontext, 'empty_component');
        provider::add_userids_for_context($userlist, 'empty_component');
        $this->assertCount(0, $userlist->get_userids());
    }

    /**
     * Test fetching the xapi state data for a specified user in a specified component and itemid.
     */
    public function test_get_xapi_states_for_user(): void {
        global $DB;

        $this->resetAfterTest();

        // Scenario.
        list($user1, $user2, $user3) = $this->set_up_data();
        $this->assertEquals(3, $DB->count_records('xapi_states', ['userid' => $user1->id]));
        $this->assertEquals(4, $DB->count_records('xapi_states', ['userid' => $user2->id]));
        $this->assertEquals(1, $DB->count_records('xapi_states', ['userid' => $user3->id]));
        $systemcontext = \context_system::instance();

        // Get the states info for user1 in the system context.
        $result = provider::get_xapi_states_for_user($user1->id, 'fake_component', $systemcontext->id);
        $info = (object) reset($result);
        // Ensure the correct data has been returned.
        $this->assertNotEmpty($info->statedata);

        $this->assertNotEmpty($info->timecreated);
        $this->assertNotEmpty($info->timemodified);

        // Get the states info for user2 in the system context.
        $result = provider::get_xapi_states_for_user($user2->id, 'fake_component', $systemcontext->id);
        $info = (object) reset($result);
        // Ensure the correct data has been returned.
        $this->assertNotEmpty($info->statedata);
        $this->assertNotEmpty($info->timecreated);
        $this->assertNotEmpty($info->timemodified);

        // Get the states info for user3 in the system context (it should be empty).
        $info = provider::get_xapi_states_for_user($user3->id, 'fake_component', $systemcontext->id);
        // Ensure the correct data has been returned.
        $this->assertEmpty($info);
    }

    /**
     * Test deletion of user xapi states based on an approved_contextlist and component area.
     */
    public function test_delete_states_for_user(): void {
        global $DB;

        $this->resetAfterTest();

        // Scenario.
        list($user1, $user2, $user3) = $this->set_up_data();
        $this->assertEquals(3, $DB->count_records('xapi_states', ['userid' => $user1->id]));
        $this->assertEquals(4, $DB->count_records('xapi_states', ['userid' => $user2->id]));
        $this->assertEquals(1, $DB->count_records('xapi_states', ['userid' => $user3->id]));

        // Now, delete the xapistates for user1 only.
        $user1context = \context_user::instance($user1->id);
        $approvedcontextlist = new \core_privacy\local\request\approved_contextlist($user1, 'fake_component', [$user1context->id]);
        provider::delete_states_for_user($approvedcontextlist, 'fake_component');

        // Verify that we have no xapi states for user1 for the fake_component but that the rest of records are intact.
        $this->assertEquals(0, $DB->count_records('xapi_states', ['userid' => $user1->id, 'component' => 'fake_component']));
        $this->assertEquals(1, $DB->count_records('xapi_states', ['userid' => $user1->id, 'component' => 'mod_h5pactivity']));
        $this->assertEquals(4, $DB->count_records('xapi_states', ['userid' => $user2->id]));
        $this->assertEquals(1, $DB->count_records('xapi_states', ['userid' => $user3->id]));
    }

    /**
     * Test deletion of all user xapi states.
     */
    public function test_delete_states_for_all_users(): void {
        global $DB;

        $this->resetAfterTest();

        // Scenario.
        list($user1, $user2, $user3) = $this->set_up_data();
        $this->assertEquals(3, $DB->count_records('xapi_states', ['userid' => $user1->id]));
        $this->assertEquals(4, $DB->count_records('xapi_states', ['userid' => $user2->id]));
        $this->assertEquals(1, $DB->count_records('xapi_states', ['userid' => $user3->id]));

        // Now, delete all course module xapi states in the 'fake_component' context only.
        provider::delete_states_for_all_users(\context_system::instance(), 'fake_component');

        // Verify that only content with the context_system for the fake_component have been removed.
        $this->assertEquals(2, $DB->count_records('xapi_states', ['userid' => $user1->id]));
        $this->assertEquals(3, $DB->count_records('xapi_states', ['userid' => $user2->id]));
        $this->assertEquals(1, $DB->count_records('xapi_states', ['userid' => $user3->id]));
    }

    /**
     * Test deletion of user xapi states based on an approved_userlist and component area.
     */
    public function test_delete_states_for_userlist(): void {
        global $DB;

        $this->resetAfterTest();

        // Scenario.
        list($user1, $user2, $user3) = $this->set_up_data();
        $this->assertEquals(3, $DB->count_records('xapi_states', ['userid' => $user1->id]));
        $this->assertEquals(4, $DB->count_records('xapi_states', ['userid' => $user2->id]));
        $this->assertEquals(1, $DB->count_records('xapi_states', ['userid' => $user3->id]));
        $systemcontext = \context_system::instance();

        // Ask the xapi privacy api to export userids for states of the type we just created, in the system context.
        $userlist1 = new \core_privacy\local\request\userlist($systemcontext, 'fake_component');
        provider::add_userids_for_context($userlist1);
        // Verify we have two userids in the list for system context.
        $this->assertCount(2, $userlist1->get_userids());

        // Now, delete the states for user1 only in the system context.
        $approveduserlist = new \core_privacy\local\request\approved_userlist($systemcontext, 'fake_component', [$user1->id]);
        provider::delete_states_for_userlist($approveduserlist);
        // Ensure user1's data was deleted and user2 is still returned for system context.
        $userlist1 = new \core_privacy\local\request\userlist($systemcontext, 'fake_component');
        provider::add_userids_for_context($userlist1);
        $this->assertCount(1, $userlist1->get_userids());
        // Verify that user2 is still in the list for system context.
        $expected = [$user2->id];
        $this->assertEquals($expected, $userlist1->get_userids());
        // Verify that the data of user1 in other contexts was not deleted.
        $this->assertEquals(1, $DB->count_records('xapi_states', ['userid' => $user3->id]));
        $this->assertEquals(1, $DB->count_records('xapi_states', ['userid' => $user1->id]));
        $this->assertEquals(2, $DB->count_records('xapi_states', ['itemid' => $systemcontext->id]));

        // Verify that no data is removed if the component is empty.
        $userlist3 = new \core_privacy\local\request\userlist($systemcontext, 'empty_component');
        provider::add_userids_for_context($userlist3);
        $this->assertCount(0, $userlist3->get_userids());
        $this->assertEquals(2, $DB->count_records('xapi_states', ['itemid' => $systemcontext->id]));
    }
}
