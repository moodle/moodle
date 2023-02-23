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
 * Privacy tests for enrol_flatfile.
 *
 * @package    enrol_flatfile
 * @category   test
 * @copyright  2018 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace enrol_flatfile\privacy;

defined('MOODLE_INTERNAL') || die();

use core_privacy\local\metadata\collection;
use core_privacy\tests\provider_testcase;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\writer;
use enrol_flatfile\privacy\provider;

/**
 * Privacy tests for enrol_flatfile.
 *
 * @copyright  2018 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider_test extends provider_testcase {

    /** @var \stdClass $user1 a test user.*/
    protected $user1;

    /** @var \stdClass $user2 a test user.*/
    protected $user2;

    /** @var \stdClass $user3 a test user.*/
    protected $user3;

    /** @var \stdClass $user4 a test user.*/
    protected $user4;

    /** @var \context $coursecontext1 a course context.*/
    protected $coursecontext1;

    /** @var \context $coursecontext2 a course context.*/
    protected $coursecontext2;

    /** @var \context $coursecontext3 a course context.*/
    protected $coursecontext3;

    /**
     * Called before every test.
     */
    public function setUp(): void {
        $this->resetAfterTest(true);
    }

    /**
     * Verify that get_metadata returns the database table mapping.
     */
    public function test_get_metadata() {
        $collection = new collection('enrol_flatfile');
        $collection = provider::get_metadata($collection);
        $collectiondata = $collection->get_collection();
        $this->assertNotEmpty($collectiondata);
        $this->assertInstanceOf(\core_privacy\local\metadata\types\database_table::class, $collectiondata[0]);
    }

    /**
     * Verify that the relevant course contexts are returned for users with pending enrolment records.
     */
    public function test_get_contexts_for_user() {
        global $DB;
        // Create, via flatfile syncing, the future enrolments entries in the enrol_flatfile table.
        $this->create_future_enrolments();

        $this->assertEquals(5, $DB->count_records('enrol_flatfile'));

        // We expect to see 2 entries for user1, in course1 and course3.
        $contextlist = provider::get_contexts_for_userid($this->user1->id);
        $this->assertEquals(2, $contextlist->count());
        $contextids = $contextlist->get_contextids();
        $this->assertContainsEquals($this->coursecontext1->id, $contextids);
        $this->assertContainsEquals($this->coursecontext3->id, $contextids);

        // And 1 for user2 on course2.
        $contextlist = provider::get_contexts_for_userid($this->user2->id);
        $this->assertEquals(1, $contextlist->count());
        $contextids = $contextlist->get_contextids();
        $this->assertContainsEquals($this->coursecontext2->id, $contextids);
    }

    /**
     * Verify the export includes any future enrolment records for the user.
     */
    public function test_export_user_data() {
        // Create, via flatfile syncing, the future enrolments entries in the enrol_flatfile table.
        $this->create_future_enrolments();

        // Get contexts containing user data.
        $contextlist = provider::get_contexts_for_userid($this->user1->id);
        $this->assertEquals(2, $contextlist->count());

        $approvedcontextlist = new approved_contextlist(
            $this->user1,
            'enrol_flatfile',
            $contextlist->get_contextids()
        );

        // Export for the approved contexts.
        provider::export_user_data($approvedcontextlist);

        // Verify we see one future course enrolment in course1, and one in course3.
        $subcontext = \core_enrol\privacy\provider::get_subcontext([get_string('pluginname', 'enrol_flatfile')]);

        $writer = writer::with_context($this->coursecontext1);
        $this->assertNotEmpty($writer->get_data($subcontext));

        $writer = writer::with_context($this->coursecontext3);
        $this->assertNotEmpty($writer->get_data($subcontext));

        // Verify we have nothing in course 2 for this user.
        $writer = writer::with_context($this->coursecontext2);
        $this->assertEmpty($writer->get_data($subcontext));
    }

    /**
     * Verify export will limit any future enrolment records to only those contextids provided.
     */
    public function test_export_user_data_restricted_context_subset() {
        // Create, via flatfile syncing, the future enrolments entries in the enrol_flatfile table.
        $this->create_future_enrolments();

        // Now, limit the export scope to just course1's context and verify only that data is seen in any export.
        $subsetapprovedcontextlist = new approved_contextlist(
            $this->user1,
            'enrol_flatfile',
            [$this->coursecontext1->id]
        );

        // Export for the approved contexts.
        provider::export_user_data($subsetapprovedcontextlist);

        // Verify we see one future course enrolment in course1 only.
        $subcontext = \core_enrol\privacy\provider::get_subcontext([get_string('pluginname', 'enrol_flatfile')]);

        $writer = writer::with_context($this->coursecontext1);
        $this->assertNotEmpty($writer->get_data($subcontext));

        // And nothing in the course3 context.
        $writer = writer::with_context($this->coursecontext3);
        $this->assertEmpty($writer->get_data($subcontext));
    }

    /**
     * Verify that records can be deleted by context.
     */
    public function test_delete_data_for_all_users_in_context() {
        global $DB;
        // Create, via flatfile syncing, the future enrolments entries in the enrol_flatfile table.
        $this->create_future_enrolments();

        // Verify we have 3 future enrolments for course 1.
        $this->assertEquals(3, $DB->count_records('enrol_flatfile', ['courseid' => $this->coursecontext1->instanceid]));

        // Now, run delete by context and confirm that all records are removed.
        provider::delete_data_for_all_users_in_context($this->coursecontext1);
        $this->assertEquals(0, $DB->count_records('enrol_flatfile', ['courseid' => $this->coursecontext1->instanceid]));
    }

    public function test_delete_data_for_user() {
        global $DB;
        // Create, via flatfile syncing, the future enrolments entries in the enrol_flatfile table.
        $this->create_future_enrolments();

        // Verify we have 2 future enrolments for course 1 and course 3.
        $contextlist = provider::get_contexts_for_userid($this->user1->id);
        $this->assertEquals(2, $contextlist->count());
        $contextids = $contextlist->get_contextids();
        $this->assertContainsEquals($this->coursecontext1->id, $contextids);
        $this->assertContainsEquals($this->coursecontext3->id, $contextids);

        $approvedcontextlist = new approved_contextlist(
            $this->user1,
            'enrol_flatfile',
            $contextids
        );

        // Now, run delete for user and confirm that both records are removed.
        provider::delete_data_for_user($approvedcontextlist);
        $contextlist = provider::get_contexts_for_userid($this->user1->id);
        $this->assertEquals(0, $contextlist->count());
        $this->assertEquals(0, $DB->count_records('enrol_flatfile', ['userid' => $this->user1->id]));
    }

    /**
     * Test for provider::get_users_in_context().
     */
    public function test_get_users_in_context() {
        global $DB;
        // Create, via flatfile syncing, the future enrolments entries in the enrol_flatfile table.
        $this->create_future_enrolments();

        $this->assertEquals(5, $DB->count_records('enrol_flatfile'));

        // We expect to see 3 entries for course1, and that's user1, user3 and user4.
        $userlist = new \core_privacy\local\request\userlist($this->coursecontext1, 'enrol_flatfile');
        provider::get_users_in_context($userlist);
        $this->assertEqualsCanonicalizing(
                [$this->user1->id, $this->user3->id, $this->user4->id],
                $userlist->get_userids());

        // And 1 for course2 which is for user2.
        $userlist = new \core_privacy\local\request\userlist($this->coursecontext2, 'enrol_flatfile');
        provider::get_users_in_context($userlist);
        $this->assertEquals([$this->user2->id], $userlist->get_userids());

        // And 1 for course3 which is for user1 again.
        $userlist = new \core_privacy\local\request\userlist($this->coursecontext3, 'enrol_flatfile');
        provider::get_users_in_context($userlist);
        $this->assertEquals([$this->user1->id], $userlist->get_userids());
    }

    /**
     * Test for provider::delete_data_for_users().
     */
    public function test_delete_data_for_users() {
        global $DB;

        // Create, via flatfile syncing, the future enrolments entries in the enrol_flatfile table.
        $this->create_future_enrolments();

        // Verify we have 3 future enrolment for user 1, user 3 and user 4.
        $userlist = new \core_privacy\local\request\userlist($this->coursecontext1, 'enrol_flatfile');
        provider::get_users_in_context($userlist);
        $this->assertEqualsCanonicalizing(
                [$this->user1->id, $this->user3->id, $this->user4->id],
                $userlist->get_userids());

        $approveduserlist = new \core_privacy\local\request\approved_userlist($this->coursecontext1, 'enrol_flatfile',
                [$this->user1->id, $this->user3->id]);

        // Now, run delete for user and confirm that the record is removed.
        provider::delete_data_for_users($approveduserlist);
        $userlist = new \core_privacy\local\request\userlist($this->coursecontext1, 'enrol_flatfile');
        provider::get_users_in_context($userlist);
        $this->assertEquals([$this->user4->id], $userlist->get_userids());
        $this->assertEquals(
                [$this->user4->id],
                $DB->get_fieldset_select('enrol_flatfile', 'userid', 'courseid = ?', [$this->coursecontext1->instanceid])
        );
    }

    /**
     * Helper to sync a file and create the enrol_flatfile DB entries, for use with the get, export and delete tests.
     */
    protected function create_future_enrolments() {
        global $CFG;
        $this->user1 = $this->getDataGenerator()->create_user(['idnumber' => 'u1']);
        $this->user2 = $this->getDataGenerator()->create_user(['idnumber' => 'u2']);
        $this->user3 = $this->getDataGenerator()->create_user(['idnumber' => 'u3']);
        $this->user4 = $this->getDataGenerator()->create_user(['idnumber' => 'u4']);

        $course1 = $this->getDataGenerator()->create_course(['idnumber' => 'c1']);
        $course2 = $this->getDataGenerator()->create_course(['idnumber' => 'c2']);
        $course3 = $this->getDataGenerator()->create_course(['idnumber' => 'c3']);
        $this->coursecontext1 = \context_course::instance($course1->id);
        $this->coursecontext2 = \context_course::instance($course2->id);
        $this->coursecontext3 = \context_course::instance($course3->id);

        $now = time();
        $future = $now + 60 * 60 * 5;
        $farfuture = $now + 60 * 60 * 24 * 5;

        $file = "$CFG->dataroot/enrol.txt";
        $data = "add,student,u1,c1,$future,0
                 add,student,u2,c2,$future,0
                 add,student,u3,c1,$future,0
                 add,student,u4,c1,$future,0
                 add,student,u1,c3,$future,$farfuture";
        file_put_contents($file, $data);

        $trace = new \null_progress_trace();
        $this->enable_plugin();
        $flatfileplugin = enrol_get_plugin('flatfile');
        $flatfileplugin->set_config('location', $file);
        $flatfileplugin->sync($trace);
    }

    /**
     * Enables the flatfile plugin for testing.
     */
    protected function enable_plugin() {
        $enabled = enrol_get_plugins(true);
        $enabled['flatfile'] = true;
        $enabled = array_keys($enabled);
        set_config('enrol_plugins_enabled', implode(',', $enabled));
    }
}
