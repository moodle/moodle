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
defined('MOODLE_INTERNAL') || die();
use mnetservice_enrol\privacy\provider;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\writer;
use core_privacy\local\request\transform;
use core_privacy\tests\provider_testcase;
/**
 * Privacy test for the mnetservice_enrol.
 *
 * @copyright  2018 Carlos Escobedo <carlos@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mnetservice_enrol_privacy_testcase extends provider_testcase {
    /** @var stdClass the mnet host we are using to test. */
    protected $mnethost;
    /** @var stdClass the mnet service enrolment to test. */
    protected $enrolment;

    /**
     * Test set up.
     *
     * This is executed before running any test in this file.
     */
    public function setUp() {
        global $DB;

        // Add a mnet host.
        $this->mnethost = new stdClass();
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
        $data = $writer->get_data($subcontexts);
        $this->assertCount(1, (array)$data);
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
     * Help function to create a simulation of MNet service enrol.
     * Create a Dummy Enrol into mnetservice_enrol_enrolments.
     *
     * @param  int $userid  Userid.
     * @param  int $remotecourseid  Remotecourseid.
     */
    protected function insert_mnetservice_enrol_enrolments($userid, $remotecourseid) {
        global $DB;

        // Create a test MNet service enrol enrolments.
        $this->enrolment                  = new stdclass();
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
        $course                 = new stdclass();
        $course->hostid         = $this->mnethost->id;
        $course->remoteid       = $remoteid;
        $course->categoryid     = 1;
        $course->categoryname   = 'Miscellaneous';
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