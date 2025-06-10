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
 * Privacy provider tests.
 *
 * @package    mod_customcert
 * @copyright  2018 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_customcert;

use stdClass;
use context_module;
use context_system;
use mod_customcert\privacy\provider;

/**
 * Privacy provider tests class.
 *
 * @package    mod_customcert
 * @copyright  2018 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class privacy_provider_test extends \core_privacy\tests\provider_testcase {

    /**
     * Test for provider::get_contexts_for_userid().
     *
     * @covers \provider::get_contexts_for_userid
     */
    public function test_get_contexts_for_userid() {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();

        // The customcert activity the user will have an issue from.
        $customcert = $this->getDataGenerator()->create_module('customcert', ['course' => $course->id]);

        // Another customcert activity that has no issued certificates.
        $this->getDataGenerator()->create_module('customcert', ['course' => $course->id]);

        // Create a user who will be issued a certificate.
        $user = $this->getDataGenerator()->create_user();

        // Issue the certificate.
        $this->create_certificate_issue($customcert->id, $user->id);

        // Check the context supplied is correct.
        $contextlist = provider::get_contexts_for_userid($user->id);
        $this->assertCount(1, $contextlist);

        $contextformodule = $contextlist->current();
        $cmcontext = context_module::instance($customcert->cmid);
        $this->assertEquals($cmcontext->id, $contextformodule->id);
    }

    /**
     * Test for provider::get_users_in_context().
     *
     * @covers \provider::get_users_in_context()
     */
    public function test_get_users_in_context() {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();

        // The customcert activity the user will have an issue from.
        $customcert1 = $this->getDataGenerator()->create_module('customcert', ['course' => $course->id]);
        $customcert2 = $this->getDataGenerator()->create_module('customcert', ['course' => $course->id]);

        // Call get_users_in_context() when the customcert hasn't any user.
        $cm = get_coursemodule_from_instance('customcert', $customcert1->id);
        $cmcontext = context_module::instance($cm->id);
        $userlist = new \core_privacy\local\request\userlist($cmcontext, 'mod_customcert');
        provider::get_users_in_context($userlist);

        // Check no user has been returned.
        $this->assertCount(0, $userlist->get_userids());

        // Create some users who will be issued a certificate.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();
        $this->create_certificate_issue($customcert1->id, $user1->id);
        $this->create_certificate_issue($customcert1->id, $user2->id);
        $this->create_certificate_issue($customcert2->id, $user3->id);

        // Call get_users_in_context() again.
        provider::get_users_in_context($userlist);

        // Check this time there are 2 users.
        $this->assertCount(2, $userlist->get_userids());
        $this->assertContains((int) $user1->id, $userlist->get_userids());
        $this->assertContains((int) $user2->id, $userlist->get_userids());
        $this->assertNotContains((int) $user3->id, $userlist->get_userids());
    }

    /**
     * Test for provider::get_users_in_context() with invalid context type.
     *
     * @covers \provider::get_users_in_context()
     */
    public function test_get_users_in_context_invalid_context_type() {
        $systemcontext = context_system::instance();

        $userlist = new \core_privacy\local\request\userlist($systemcontext, 'mod_customcert');
        \mod_customcert\privacy\provider::get_users_in_context($userlist);

        $this->assertCount(0, $userlist->get_userids());
    }

    /**
     * Test for provider::export_user_data().
     *
     * @covers \provider::export_user_data()
     */
    public function test_export_for_context() {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();

        $customcert = $this->getDataGenerator()->create_module('customcert', ['course' => $course->id]);

        // Create users who will be issued a certificate.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        $this->create_certificate_issue($customcert->id, $user1->id);
        $this->create_certificate_issue($customcert->id, $user2->id);

        // Export all of the data for the context for user 1.
        $cmcontext = context_module::instance($customcert->cmid);
        $this->export_context_data_for_user($user1->id, $cmcontext, 'mod_customcert');
        $writer = \core_privacy\local\request\writer::with_context($cmcontext);

        $this->assertTrue($writer->has_any_data());

        $data = $writer->get_data();
        $this->assertCount(1, $data->issues);

        $issues = $data->issues;
        foreach ($issues as $issue) {
            $this->assertArrayHasKey('code', $issue);
            $this->assertArrayHasKey('emailed', $issue);
            $this->assertArrayHasKey('timecreated', $issue);
        }
    }

    /**
     * Test for provider::delete_data_for_all_users_in_context().
     *
     * @covers \provider::delete_data_for_all_users_in_context()
     */
    public function test_delete_data_for_all_users_in_context() {
        global $DB;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();

        $customcert = $this->getDataGenerator()->create_module('customcert', ['course' => $course->id]);
        $customcert2 = $this->getDataGenerator()->create_module('customcert', ['course' => $course->id]);

        // Create users who will be issued a certificate.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        $this->create_certificate_issue($customcert->id, $user1->id);
        $this->create_certificate_issue($customcert->id, $user2->id);

        $this->create_certificate_issue($customcert2->id, $user1->id);
        $this->create_certificate_issue($customcert2->id, $user2->id);

        // Before deletion, we should have 2 issued certificates for the first certificate.
        $count = $DB->count_records('customcert_issues', ['customcertid' => $customcert->id]);
        $this->assertEquals(2, $count);

        // Delete data based on context.
        $cmcontext = context_module::instance($customcert->cmid);
        provider::delete_data_for_all_users_in_context($cmcontext);

        // After deletion, the issued certificates for the activity should have been deleted.
        $count = $DB->count_records('customcert_issues', ['customcertid' => $customcert->id]);
        $this->assertEquals(0, $count);

        // We should still have the issues for the second certificate.
        $count = $DB->count_records('customcert_issues', ['customcertid' => $customcert2->id]);
        $this->assertEquals(2, $count);
    }

    /**
     * Test for provider::delete_data_for_user().
     *
     * @covers \provider::delete_data_for_user()
     */
    public function test_delete_data_for_user() {
        global $DB;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();

        $customcert = $this->getDataGenerator()->create_module('customcert', ['course' => $course->id]);

        // Create users who will be issued a certificate.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        $this->create_certificate_issue($customcert->id, $user1->id);
        $this->create_certificate_issue($customcert->id, $user2->id);

        // Before deletion we should have 2 issued certificates.
        $count = $DB->count_records('customcert_issues', ['customcertid' => $customcert->id]);
        $this->assertEquals(2, $count);

        $context = \context_module::instance($customcert->cmid);
        $contextlist = new \core_privacy\local\request\approved_contextlist($user1, 'customcert',
            [$context->id]);
        provider::delete_data_for_user($contextlist);

        // After deletion, the issued certificates for the first user should have been deleted.
        $count = $DB->count_records('customcert_issues', ['customcertid' => $customcert->id, 'userid' => $user1->id]);
        $this->assertEquals(0, $count);

        // Check the issue for the other user is still there.
        $customcertissue = $DB->get_records('customcert_issues');
        $this->assertCount(1, $customcertissue);
        $lastissue = reset($customcertissue);
        $this->assertEquals($user2->id, $lastissue->userid);
    }

    /**
     * Test for provider::delete_data_for_users().
     *
     * @covers \provider::delete_data_for_users()
     */
    public function test_delete_data_for_users() {
        global $DB;

        $this->resetAfterTest();

        // Create course, customcert and users who will be issued a certificate.
        $course = $this->getDataGenerator()->create_course();
        $customcert1 = $this->getDataGenerator()->create_module('customcert', ['course' => $course->id]);
        $customcert2 = $this->getDataGenerator()->create_module('customcert', ['course' => $course->id]);
        $cm1 = get_coursemodule_from_instance('customcert', $customcert1->id);
        $cm2 = get_coursemodule_from_instance('customcert', $customcert2->id);
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();
        $this->create_certificate_issue($customcert1->id, $user1->id);
        $this->create_certificate_issue($customcert1->id, $user2->id);
        $this->create_certificate_issue($customcert1->id, $user3->id);
        $this->create_certificate_issue($customcert2->id, $user1->id);
        $this->create_certificate_issue($customcert2->id, $user2->id);

        // Before deletion we should have 3 + 2 issued certificates.
        $count = $DB->count_records('customcert_issues', ['customcertid' => $customcert1->id]);
        $this->assertEquals(3, $count);
        $count = $DB->count_records('customcert_issues', ['customcertid' => $customcert2->id]);
        $this->assertEquals(2, $count);

        $context1 = context_module::instance($cm1->id);
        $approveduserlist = new \core_privacy\local\request\approved_userlist($context1, 'customcert',
                [$user1->id, $user2->id]);
        provider::delete_data_for_users($approveduserlist);

        // After deletion, the customcert of the 2 students provided above should have been deleted
        // from the activity. So there should only remain 1 certificate which is for $user3.
        $customcertissues1 = $DB->get_records('customcert_issues', ['customcertid' => $customcert1->id]);
        $this->assertCount(1, $customcertissues1);
        $lastissue = reset($customcertissues1);
        $this->assertEquals($user3->id, $lastissue->userid);

        // Confirm that the certificates issues in the other activity are intact.
        $customcertissues1 = $DB->get_records('customcert_issues', ['customcertid' => $customcert2->id]);
        $this->assertCount(2, $customcertissues1);
    }

    /**
     * Mimicks the creation of a customcert issue.
     *
     * There is no API we can use to insert an customcert issue, so we
     * will simply insert directly into the database.
     *
     * @param int $customcertid
     * @param int $userid
     */
    protected function create_certificate_issue(int $customcertid, int $userid) {
        global $DB;

        static $i = 1;

        $customcertissue = new stdClass();
        $customcertissue->customcertid = $customcertid;
        $customcertissue->userid = $userid;
        $customcertissue->code = certificate::generate_code();
        $customcertissue->timecreated = time() + $i;

        // Insert the record into the database.
        $DB->insert_record('customcert_issues', $customcertissue);

        $i++;
    }
}
