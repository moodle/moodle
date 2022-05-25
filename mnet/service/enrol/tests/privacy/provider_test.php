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
 * Privacy test for the mnetservice_enrol implementation of the privacy API.
 *
 * @package    mnetservice_enrol
 * @category   test
 * @copyright  2018 Carlos Escobedo <carlos@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mnetservice_enrol\privacy;

defined('MOODLE_INTERNAL') || die();

use mnetservice_enrol\privacy\provider;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\writer;
use core_privacy\local\request\transform;
use core_privacy\tests\provider_testcase;
use core_privacy\local\request\approved_userlist;

/**
 * Privacy test for the mnetservice_enrol.
 *
 * @copyright  2018 Carlos Escobedo <carlos@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider_test extends provider_testcase {

    /** @var stdClass the mnet host we are using to test. */
    protected $mnethost;

    /** @var stdClass the mnet service enrolment to test. */
    protected $enrolment;

    /**
     * Test set up.
     *
     * This is executed before running any test in this file.
     */
    public function setUp(): void {
        global $DB;

        // Add a mnet host.
        $this->mnethost = new \stdClass();
        $this->mnethost->name = 'A mnet host';
        $this->mnethost->public_key = 'A random public key!';
        $this->mnethost->id = $DB->insert_record('mnet_host', $this->mnethost);
    }

    /**
     * Check that a user context is returned if there is any user data for this user.
     */
    public function test_get_contexts_for_userid() {
        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();
        $this->assertEmpty(provider::get_contexts_for_userid($user->id));

        // Create a test MNet service enrol enrolments.
        $remotecourseid = 101;
        $this->insert_mnetservice_enrol_courses($remotecourseid);
        $this->insert_mnetservice_enrol_enrolments($user->id, $remotecourseid);

        $contextlist = provider::get_contexts_for_userid($user->id);
        // Check that we only get back two context.
        $this->assertCount(1, $contextlist);
        // Check that the contexts are returned are the expected.
        $usercontext = \context_user::instance($user->id);
        $this->assertEquals($usercontext->id, $contextlist->get_contextids()[0]);
    }

    /**
     * Test that user data is exported correctly.
     */
    public function test_export_user_data() {
        global $DB;

        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();
        $this->assertEmpty(provider::get_contexts_for_userid($user->id));

        // Create a test MNet service enrol enrolments.
        $remotecourseid = 101;
        $this->insert_mnetservice_enrol_courses($remotecourseid);
        $this->insert_mnetservice_enrol_enrolments($user->id, $remotecourseid);

        $subcontexts = [
            get_string('privacy:metadata:mnetservice_enrol_enrolments', 'mnetservice_enrol')
        ];
        $usercontext = \context_user::instance($user->id);
        $writer = writer::with_context($usercontext);
        $this->assertFalse($writer->has_any_data());
        $approvedlist = new approved_contextlist($user, 'mnetservice_enrol', [$usercontext->id]);
        provider::export_user_data($approvedlist);
        $data = (array)$writer->get_data($subcontexts);
        $this->assertCount(1, $data);
        $this->assertEquals($this->mnethost->name, reset($data)->host);
        $remotecoursename = $DB->get_field('mnetservice_enrol_courses', 'fullname',
            array('remoteid' => $this->enrolment->remotecourseid));
        $this->assertEquals($remotecoursename, reset($data)->remotecourseid);
        $this->assertEquals($this->enrolment->rolename, reset($data)->rolename);
        $this->assertEquals($this->enrolment->enroltype, reset($data)->enroltype);
        $this->assertEquals(transform::datetime($this->enrolment->enroltime), reset($data)->enroltime);
    }

    /**
     * Test deleting all user data for a specific context.
     */
    public function test_delete_data_for_all_users_in_context() {
        global $DB;

        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();
        $this->assertEmpty(provider::get_contexts_for_userid($user->id));

        // Create a test MNet service enrol enrolments.
        $remotecourseid = 101;
        $this->insert_mnetservice_enrol_courses($remotecourseid);
        $this->insert_mnetservice_enrol_enrolments($user->id, $remotecourseid);
        $this->insert_mnetservice_enrol_enrolments($user2->id, $remotecourseid);
        $this->insert_mnetservice_enrol_enrolments($user3->id, $remotecourseid);
        $usercontext = \context_user::instance($user->id);
        // Get all user enrolments.
        $userenrolments = $DB->get_records('mnetservice_enrol_enrolments', array());
        $this->assertCount(3, $userenrolments);
        // Get all user enrolments match with user.
        $userenrolments = $DB->get_records('mnetservice_enrol_enrolments', array('userid' => $user->id));
        $this->assertCount(1, $userenrolments);
        // Delete everything for the first user context.
        provider::delete_data_for_all_users_in_context($usercontext);
        // Get all user enrolments match with user.
        $userenrolments = $DB->get_records('mnetservice_enrol_enrolments', ['userid' => $user->id]);
        $this->assertCount(0, $userenrolments);
        // Get all user enrolments.
        $userenrolments = $DB->get_records('mnetservice_enrol_enrolments', array());
        $this->assertCount(2, $userenrolments);
    }

    /**
     * This should work identical to the above test.
     */
    public function test_delete_data_for_user() {
        global $DB;

        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();
        $this->assertEmpty(provider::get_contexts_for_userid($user->id));

        $remotecourseid = 101;
        $this->insert_mnetservice_enrol_courses($remotecourseid);
        $this->insert_mnetservice_enrol_enrolments($user->id, $remotecourseid);
        $this->insert_mnetservice_enrol_enrolments($user2->id, $remotecourseid);
        $this->insert_mnetservice_enrol_enrolments($user3->id, $remotecourseid);
        $remotecourseid2 = 102;
        $this->insert_mnetservice_enrol_courses($remotecourseid2);
        $this->insert_mnetservice_enrol_enrolments($user->id, $remotecourseid2);

        $usercontext = \context_user::instance($user->id);
        // Get all user enrolments.
        $userenrolments = $DB->get_records('mnetservice_enrol_enrolments', array());
        $this->assertCount(4, $userenrolments);
        // Get all user enrolments match with user.
        $userenrolments = $DB->get_records('mnetservice_enrol_enrolments', array('userid' => $user->id));
        $this->assertCount(2, $userenrolments);
        // Delete everything for the first user.
        $approvedlist = new approved_contextlist($user, 'mnetservice_enrol', [$usercontext->id]);
        provider::delete_data_for_user($approvedlist);
        // Get all user enrolments match with user.
        $userenrolments = $DB->get_records('mnetservice_enrol_enrolments', ['userid' => $user->id]);
        $this->assertCount(0, $userenrolments);
        // Get all user enrolments accounts.
        $userenrolments = $DB->get_records('mnetservice_enrol_enrolments', array());
        $this->assertCount(2, $userenrolments);
    }

    /**
     * Test that only users within a course context are fetched.
     */
    public function test_get_users_in_context() {
        $this->resetAfterTest();

        $component = 'mnetservice_enrol';

        // Create a user.
        $user = $this->getDataGenerator()->create_user();
        $usercontext = \context_user::instance($user->id);

        // Create user2.
        $user2 = $this->getDataGenerator()->create_user();

        $userlist = new \core_privacy\local\request\userlist($usercontext, $component);
        provider::get_users_in_context($userlist);
        $this->assertCount(0, $userlist);

        // Create a test MNet service enrol enrolments.
        $remotecourseid = 101;
        $this->insert_mnetservice_enrol_courses($remotecourseid);
        $this->insert_mnetservice_enrol_enrolments($user->id, $remotecourseid);
        $this->insert_mnetservice_enrol_enrolments($user2->id, $remotecourseid);

        // The list of users within the user context should contain only user.
        provider::get_users_in_context($userlist);
        $this->assertCount(1, $userlist);
        $this->assertFalse(in_array($user2->id, $userlist->get_userids()));
        $this->assertTrue(in_array($user->id, $userlist->get_userids()));

        // The list of users within the system context should be empty.
        $systemcontext = \context_system::instance();
        $userlist2 = new \core_privacy\local\request\userlist($systemcontext, $component);
        provider::get_users_in_context($userlist2);
        $this->assertCount(0, $userlist2);
    }

    /**
     * Test that data for users in approved userlist is deleted.
     */
    public function test_delete_data_for_users() {
        $this->resetAfterTest();

        $component = 'mnetservice_enrol';

        // Create user1.
        $user1 = $this->getDataGenerator()->create_user();
        $usercontext1 = \context_user::instance($user1->id);
        // Create user2.
        $user2 = $this->getDataGenerator()->create_user();
        $usercontext2 = \context_user::instance($user2->id);

        // Create a test MNet service enrol enrolments.
        $remotecourseid = 101;
        $this->insert_mnetservice_enrol_courses($remotecourseid);
        $this->insert_mnetservice_enrol_enrolments($user1->id, $remotecourseid);
        $this->insert_mnetservice_enrol_enrolments($user2->id, $remotecourseid);

        $userlist1 = new \core_privacy\local\request\userlist($usercontext1, $component);
        provider::get_users_in_context($userlist1);
        $this->assertCount(1, $userlist1);
        $expected = [$user1->id];
        $actual = $userlist1->get_userids();
        $this->assertEquals($expected, $actual);

        $userlist2 = new \core_privacy\local\request\userlist($usercontext2, $component);
        provider::get_users_in_context($userlist2);
        $this->assertCount(1, $userlist2);
        $expected = [$user2->id];
        $actual = $userlist2->get_userids();
        $this->assertEquals($expected, $actual);

        // Convert $userlist1 into an approved_contextlist.
        $approvedlist1 = new approved_userlist($usercontext1, $component, $userlist1->get_userids());
        // Delete using delete_data_for_user.
        provider::delete_data_for_users($approvedlist1);

        // Re-fetch users in usercontext1.
        $userlist1 = new \core_privacy\local\request\userlist($usercontext1, $component);
        provider::get_users_in_context($userlist1);
        // The user data in usercontext1 should be deleted.
        $this->assertCount(0, $userlist1);

        // Re-fetch users in usercontext2.
        $userlist2 = new \core_privacy\local\request\userlist($usercontext2, $component);
        provider::get_users_in_context($userlist2);
        // The user data in usercontext2 should be still present.
        $this->assertCount(1, $userlist2);

        // Convert $userlist2 into an approved_contextlist in the system context.
        $systemcontext = \context_system::instance();
        $approvedlist3 = new approved_userlist($systemcontext, $component, $userlist2->get_userids());
        // Delete using delete_data_for_user.
        provider::delete_data_for_users($approvedlist3);
        // Re-fetch users in usercontext2.
        $userlist2 = new \core_privacy\local\request\userlist($usercontext2, $component);
        provider::get_users_in_context($userlist2);
        // The user data in systemcontext should not be deleted.
        $this->assertCount(1, $userlist2);
    }

    /**
     * Help function to create a simulation of MNet service enrol.
     * Create a Dummy Enrol into mnetservice_enrol_enrolments.
     *
     * @param  int $userid  Userid.
     * @param  int $remotecourseid  Remotecourseid.
     */
    protected function insert_mnetservice_enrol_enrolments($userid, $remotecourseid) {
        global $DB;

        // Create a test MNet service enrol enrolments.
        $this->enrolment                  = new \stdClass();
        $this->enrolment->hostid          = $this->mnethost->id;
        $this->enrolment->userid          = $userid;
        $this->enrolment->remotecourseid  = $remotecourseid;
        $this->enrolment->rolename        = 'student';
        $this->enrolment->enroltime       = time();
        $this->enrolment->enroltype       = 'mnet';
        $DB->insert_record('mnetservice_enrol_enrolments', $this->enrolment);
    }

    /**
     * Help function to create a simualtion of MNet service enrol.
     * Create a Dummy Course into mnetservice_enrol_courses.
     * Important: The real course is on the host.
     *
     * @param  int    $remoteid  Remote courseid.
     */
    protected function insert_mnetservice_enrol_courses($remoteid) {
        global $DB;

        // Create a Dummy Remote Course to test.
        $course                 = new \stdClass();
        $course->hostid         = $this->mnethost->id;
        $course->remoteid       = $remoteid;
        $course->categoryid     = 1;
        $course->categoryname   = get_string('defaultcategoryname');
        $course->sortorder      = 10001;
        $course->fullname       = 'Test Remote Course '.$remoteid;
        $course->shortname      = 'testremotecourse '.$remoteid;
        $course->idnumber       = 'IdnumberRemote '.$remoteid;
        $course->summary        = 'TestSummaryRemote '.$remoteid;
        $course->summaryformat  = FORMAT_MOODLE;
        $course->startdate      = time();
        $course->roleid         = 5;
        $course->rolename       = 'student';
        $DB->insert_record('mnetservice_enrol_courses', $course);
    }
}
