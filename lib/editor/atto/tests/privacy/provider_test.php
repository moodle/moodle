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
 * Unit tests for the editor_atto implementation of the privacy API.
 *
 * @package    editor_atto
 * @category   test
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace editor_atto\privacy;

defined('MOODLE_INTERNAL') || die();

use core_privacy\local\request\writer;
use core_privacy\local\request\approved_contextlist;
use editor_atto\privacy\provider;
use core_privacy\local\request\approved_userlist;

/**
 * Unit tests for the editor_atto implementation of the privacy API.
 *
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider_test extends \core_privacy\tests\provider_testcase {
    /**
     * One test to check fetch and export of all drafts.
     */
    public function test_fetch_and_exports_drafts() {
        global $USER;
        $this->resetAfterTest();

        // Create editor drafts in:
        // - the system; and
        // - a course; and
        // - current user context; and
        // - another user.

        $systemcontext = \context_system::instance();
        $course = $this->getDataGenerator()->create_course();
        $coursecontext = \context_course::instance($course->id);

        $usercontextids = [];
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        $usercontext = \context_user::instance($user->id);
        $usercontextids[] = $usercontext->id;
        $usercontextids[] = $systemcontext->id;
        $usercontextids[] = $coursecontext->id;

        // Add a fake inline image to the original post.

        $userdraftintro = $this->create_editor_draft($usercontext, $user->id,
                'id_user_intro', 'text for test user at own context');
        $userdraftdescription = $this->create_editor_draft($usercontext, $user->id,
                'id_user_description', 'text for test user at own context');
        $systemuserdraftintro = $this->create_editor_draft($systemcontext, $user->id,
                'id_system_intro', 'text for test user at system context', 2);
        $systemuserdraftdescription = $this->create_editor_draft($systemcontext, $user->id,
                'id_system_description', 'text for test user at system context', 4);
        $coursedraftintro = $this->create_editor_draft($coursecontext, $user->id,
                'id_course_intro', 'text for test user at course context');
        $coursedraftdescription = $this->create_editor_draft($coursecontext, $user->id,
                'id_course_description', 'text for test user at course context');

        // Create some data as the other user too.
        $otherusercontextids = [];
        $otheruser = $this->getDataGenerator()->create_user();
        $this->setUser($otheruser);

        $otherusercontext = \context_user::instance($otheruser->id);
        $otherusercontextids[] = $otherusercontext->id;
        $otherusercontextids[] = $systemcontext->id;
        $otherusercontextids[] = $coursecontext->id;

        $otheruserdraftintro = $this->create_editor_draft($otherusercontext, $otheruser->id,
                'id_user_intro', 'text for other user at own context');
        $otheruserdraftdescription = $this->create_editor_draft($otherusercontext, $otheruser->id,
                'id_user_description', 'text for other user at own context');
        $systemotheruserdraftintro = $this->create_editor_draft($systemcontext, $otheruser->id,
                'id_system_intro', 'text for other user at system context');
        $systemotheruserdraftdescription = $this->create_editor_draft($systemcontext, $otheruser->id,
                'id_system_description', 'text for other user at system context');
        $courseotheruserdraftintro = $this->create_editor_draft($coursecontext, $otheruser->id,
                'id_course_intro', 'text for other user at course context');
        $courseotheruserdraftdescription = $this->create_editor_draft($coursecontext, $otheruser->id,
                'id_course_description', 'text for other user at course context');

        // Test as the original user.
        // Get all context data for the original user.
        $this->setUser($user);
        $contextlist = provider::get_contexts_for_userid($user->id);

        // There are three contexts in the list.
        $this->assertCount(3, $contextlist);

        // Check the list against the expected list of contexts.
        foreach ($contextlist as $context) {
            $this->assertContains($context->id, $usercontextids);
        }

        // Export the data for the system context.
        // There should be two.
        $this->export_context_data_for_user($user->id, $systemcontext, 'editor_atto');
        /** @var \core_privacy\tests\request\content_writer $writer */
        $writer = \core_privacy\local\request\writer::with_context($systemcontext);
        $this->assertTrue($writer->has_any_data());

        $subcontextbase = [get_string('autosaves', 'editor_atto')];

        // There should be an intro and description.
        $intro = $writer->get_data(array_merge($subcontextbase, [$systemuserdraftintro->id]));
        $fs = get_file_storage();
        $this->assertEquals(
                format_text($systemuserdraftintro->drafttext, FORMAT_HTML, provider::get_filter_options()),
                $intro->drafttext
            );
        $this->assertCount(2, $writer->get_files(array_merge($subcontextbase, [$systemuserdraftintro->id])));

        $description = $writer->get_data(array_merge($subcontextbase, [$systemuserdraftdescription->id]));
        $this->assertEquals(
                format_text($systemuserdraftdescription->drafttext, FORMAT_HTML, provider::get_filter_options()),
                $description->drafttext
            );
        $this->assertCount(4, $writer->get_files(array_merge($subcontextbase, [$systemuserdraftdescription->id])));
    }

    /**
     * Test delete_for_all_users_in_context.
     */
    public function test_delete_for_all_users_in_context() {
        global $USER, $DB;
        $this->resetAfterTest();

        // Create editor drafts in:
        // - the system; and
        // - a course; and
        // - current user context; and
        // - another user.

        $systemcontext = \context_system::instance();
        $course = $this->getDataGenerator()->create_course();
        $coursecontext = \context_course::instance($course->id);

        $usercontextids = [];
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        $usercontext = \context_user::instance($user->id);
        $usercontextids[] = $usercontext->id;
        $usercontextids[] = $systemcontext->id;
        $usercontextids[] = $coursecontext->id;

        // Add a fake inline image to the original post.

        $userdraftintro = $this->create_editor_draft($usercontext, $user->id,
                'id_user_intro', 'text for test user at own context');
        $userdraftdescription = $this->create_editor_draft($usercontext, $user->id,
                'id_user_description', 'text for test user at own context');
        $systemuserdraftintro = $this->create_editor_draft($systemcontext, $user->id,
                'id_system_intro', 'text for test user at system context', 2);
        $systemuserdraftdescription = $this->create_editor_draft($systemcontext, $user->id,
                'id_system_description', 'text for test user at system context', 4);
        $coursedraftintro = $this->create_editor_draft($coursecontext, $user->id,
                'id_course_intro', 'text for test user at course context');
        $coursedraftdescription = $this->create_editor_draft($coursecontext, $user->id,
                'id_course_description', 'text for test user at course context');

        // Create some data as the other user too.
        $otherusercontextids = [];
        $otheruser = $this->getDataGenerator()->create_user();
        $this->setUser($otheruser);

        $otherusercontext = \context_user::instance($otheruser->id);
        $otherusercontextids[] = $otherusercontext->id;
        $otherusercontextids[] = $systemcontext->id;
        $otherusercontextids[] = $coursecontext->id;

        $otheruserdraftintro = $this->create_editor_draft($otherusercontext, $otheruser->id,
                'id_user_intro', 'text for other user at own context');
        $otheruserdraftdescription = $this->create_editor_draft($otherusercontext, $otheruser->id,
                'id_user_description', 'text for other user at own context');
        $systemotheruserdraftintro = $this->create_editor_draft($systemcontext, $otheruser->id,
                'id_system_intro', 'text for other user at system context');
        $systemotheruserdraftdescription = $this->create_editor_draft($systemcontext, $otheruser->id,
                'id_system_description', 'text for other user at system context');
        $courseotheruserdraftintro = $this->create_editor_draft($coursecontext, $otheruser->id,
                'id_course_intro', 'text for other user at course context');
        $courseotheruserdraftdescription = $this->create_editor_draft($coursecontext, $otheruser->id,
                'id_course_description', 'text for other user at course context');

        // Test deletion of the user context.
        $this->assertCount(2, $DB->get_records('editor_atto_autosave', ['contextid' => $usercontext->id]));
        provider::delete_data_for_all_users_in_context($usercontext);
        $this->assertCount(0, $DB->get_records('editor_atto_autosave', ['contextid' => $usercontext->id]));

        // No other contexts should be removed.
        $this->assertCount(2, $DB->get_records('editor_atto_autosave', ['contextid' => $otherusercontext->id]));
        $this->assertCount(4, $DB->get_records('editor_atto_autosave', ['contextid' => $systemcontext->id]));
        $this->assertCount(4, $DB->get_records('editor_atto_autosave', ['contextid' => $coursecontext->id]));

        // Test deletion of the course contexts.
        provider::delete_data_for_all_users_in_context($coursecontext);
        $this->assertCount(0, $DB->get_records('editor_atto_autosave', ['contextid' => $coursecontext->id]));
        $this->assertCount(2, $DB->get_records('editor_atto_autosave', ['contextid' => $otherusercontext->id]));
        $this->assertCount(4, $DB->get_records('editor_atto_autosave', ['contextid' => $systemcontext->id]));

        // Test deletion of the system contexts.
        provider::delete_data_for_all_users_in_context($systemcontext);
        $this->assertCount(0, $DB->get_records('editor_atto_autosave', ['contextid' => $systemcontext->id]));
        $this->assertCount(2, $DB->get_records('editor_atto_autosave', ['contextid' => $otherusercontext->id]));
    }

    /**
     * Test delete_for_all_users_in_context.
     */
    public function test_delete_for_user_in_contexts() {
        global $USER, $DB;
        $this->resetAfterTest();

        // Create editor drafts in:
        // - the system; and
        // - a course; and
        // - current user context; and
        // - another user.

        $systemcontext = \context_system::instance();
        $course = $this->getDataGenerator()->create_course();
        $coursecontext = \context_course::instance($course->id);

        $usercontextids = [];
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        $usercontext = \context_user::instance($user->id);
        $usercontextids[] = $usercontext->id;
        $usercontextids[] = $systemcontext->id;
        $usercontextids[] = $coursecontext->id;

        // Add a fake inline image to the original post.

        $userdraftintro = $this->create_editor_draft($usercontext, $user->id,
                'id_user_intro', 'text for test user at own context');
        $userdraftdescription = $this->create_editor_draft($usercontext, $user->id,
                'id_user_description', 'text for test user at own context');
        $systemuserdraftintro = $this->create_editor_draft($systemcontext, $user->id,
                'id_system_intro', 'text for test user at system context', 2);
        $systemuserdraftdescription = $this->create_editor_draft($systemcontext, $user->id,
                'id_system_description', 'text for test user at system context', 4);
        $coursedraftintro = $this->create_editor_draft($coursecontext, $user->id,
                'id_course_intro', 'text for test user at course context');
        $coursedraftdescription = $this->create_editor_draft($coursecontext, $user->id,
                'id_course_description', 'text for test user at course context');

        // Create some data as the other user too.
        $otherusercontextids = [];
        $otheruser = $this->getDataGenerator()->create_user();
        $this->setUser($otheruser);

        $otherusercontext = \context_user::instance($otheruser->id);
        $otherusercontextids[] = $otherusercontext->id;
        $otherusercontextids[] = $systemcontext->id;
        $otherusercontextids[] = $coursecontext->id;

        $otheruserdraftintro = $this->create_editor_draft($otherusercontext, $otheruser->id,
                'id_user_intro', 'text for other user at own context');
        $otheruserdraftdescription = $this->create_editor_draft($otherusercontext, $otheruser->id,
                'id_user_description', 'text for other user at own context');
        $systemotheruserdraftintro = $this->create_editor_draft($systemcontext, $otheruser->id,
                'id_system_intro', 'text for other user at system context');
        $systemotheruserdraftdescription = $this->create_editor_draft($systemcontext, $otheruser->id,
                'id_system_description', 'text for other user at system context');
        $courseotheruserdraftintro = $this->create_editor_draft($coursecontext, $otheruser->id,
                'id_course_intro', 'text for other user at course context');
        $courseotheruserdraftdescription = $this->create_editor_draft($coursecontext, $otheruser->id,
                'id_course_description', 'text for other user at course context');

        // Test deletion of all data for user in usercontext only.
        $contextlist = new \core_privacy\tests\request\approved_contextlist(
            \core_user::get_user($user->id),
            'editor_atto',
            [$usercontext->id]
        );
        provider::delete_data_for_user($contextlist);
        $this->assertCount(0, $DB->get_records('editor_atto_autosave', ['contextid' => $usercontext->id]));

        // No other contexts should be removed.
        $this->assertCount(2, $DB->get_records('editor_atto_autosave', ['contextid' => $otherusercontext->id]));
        $this->assertCount(4, $DB->get_records('editor_atto_autosave', ['contextid' => $systemcontext->id]));
        $this->assertCount(4, $DB->get_records('editor_atto_autosave', ['contextid' => $coursecontext->id]));

        // Test deletion of all data for user in course and system.
        $contextlist = new \core_privacy\tests\request\approved_contextlist(
            \core_user::get_user($user->id),
            'editor_atto',
            [$coursecontext->id, $systemcontext->id]
        );
        provider::delete_data_for_user($contextlist);
        $this->assertCount(0, $DB->get_records('editor_atto_autosave', ['contextid' => $usercontext->id]));
        $this->assertCount(2, $DB->get_records('editor_atto_autosave', ['contextid' => $otherusercontext->id]));
        $this->assertCount(2, $DB->get_records('editor_atto_autosave', ['contextid' => $systemcontext->id]));
        $this->assertCount(2, $DB->get_records('editor_atto_autosave', ['contextid' => $coursecontext->id]));

        // Data for the other user should remain.
        $this->assertCount(2, $DB->get_records('editor_atto_autosave', [
            'contextid' => $coursecontext->id,
            'userid' => $otheruser->id,
        ]));

        $this->assertCount(2, $DB->get_records('editor_atto_autosave', [
            'contextid' => $systemcontext->id,
            'userid' => $otheruser->id,
        ]));
    }

    /**
     * Test that user data with different contexts is fetched.
     */
    public function test_get_users_in_context() {
        $this->resetAfterTest();

        $component = 'editor_atto';

        // Create editor drafts in:
        // - the system; and
        // - a course; and
        // - current user context; and
        // - another user.

        $systemcontext = \context_system::instance();
        // Create a course.
        $course = $this->getDataGenerator()->create_course();
        $coursecontext = \context_course::instance($course->id);

        // Create a user.
        $user = $this->getDataGenerator()->create_user();
        $usercontext = \context_user::instance($user->id);
        $this->setUser($user);

        // Add a fake inline image to the original post.
        $this->create_editor_draft($usercontext, $user->id,
                'id_user_intro', 'text for test user at own context');
        $this->create_editor_draft($systemcontext, $user->id,
            'id_system_intro', 'text for test user at system context', 2);
        $this->create_editor_draft($systemcontext, $user->id,
            'id_system_description', 'text for test user at system context', 4);
        $this->create_editor_draft($coursecontext, $user->id,
            'id_course_intro', 'text for test user at course context');

        // Create user2.
        $user2 = $this->getDataGenerator()->create_user();
        $this->setUser($user2);

        $this->create_editor_draft($coursecontext, $user2->id,
            'id_course_description', 'text for test user2 at course context');

        // The list of users in usercontext should return user.
        $userlist = new \core_privacy\local\request\userlist($usercontext, $component);
        provider::get_users_in_context($userlist);
        $this->assertCount(1, $userlist);
        $this->assertTrue(in_array($user->id, $userlist->get_userids()));

        // The list of users in systemcontext should return user.
        $userlist = new \core_privacy\local\request\userlist($systemcontext, $component);
        provider::get_users_in_context($userlist);
        $this->assertCount(1, $userlist);
        $this->assertTrue(in_array($user->id, $userlist->get_userids()));

        // The list of users in coursecontext should return user and user2.
        $userlist = new \core_privacy\local\request\userlist($coursecontext, $component);
        provider::get_users_in_context($userlist);
        $this->assertCount(2, $userlist);
        $this->assertTrue(in_array($user->id, $userlist->get_userids()));
        $this->assertTrue(in_array($user2->id, $userlist->get_userids()));
    }

    /**
     * Test that data for users in approved userlist is deleted.
     */
    public function test_delete_data_for_users() {
        $this->resetAfterTest();

        $component = 'editor_atto';

        // Create editor drafts in:
        // - the system; and
        // - a course; and
        // - current user context; and
        // - another user.

        $systemcontext = \context_system::instance();
        // Create a course.
        $course = $this->getDataGenerator()->create_course();
        $coursecontext = \context_course::instance($course->id);

        // Create a user.
        $user = $this->getDataGenerator()->create_user();
        $usercontext = \context_user::instance($user->id);
        $this->setUser($user);

        // Add a fake inline image to the original post.
        $this->create_editor_draft($usercontext, $user->id,
            'id_user_intro', 'text for test user at own context');
        $this->create_editor_draft($usercontext, $user->id,
            'id_user_description', 'text for test user at own context');
        $this->create_editor_draft($systemcontext, $user->id,
            'id_system_intro', 'text for test user at system context', 2);
        $this->create_editor_draft($systemcontext, $user->id,
            'id_system_description', 'text for test user at system context', 4);
        $this->create_editor_draft($coursecontext, $user->id,
            'id_course_intro', 'text for test user at course context');
        $this->create_editor_draft($coursecontext, $user->id,
            'id_course_description', 'text for test user at course context');

        // Create some data as the other user too.
        $otheruser = $this->getDataGenerator()->create_user();
        $otherusercontext = \context_user::instance($otheruser->id);
        $this->setUser($otheruser);

        $this->create_editor_draft($otherusercontext, $otheruser->id,
            'id_user_intro', 'text for other user at own context');
        $this->create_editor_draft($otherusercontext, $otheruser->id,
            'id_user_description', 'text for other user at own context');
        $this->create_editor_draft($systemcontext, $otheruser->id,
            'id_system_intro', 'text for other user at system context');
        $this->create_editor_draft($systemcontext, $otheruser->id,
            'id_system_description', 'text for other user at system context');
        $this->create_editor_draft($coursecontext, $otheruser->id,
            'id_course_intro', 'text for other user at course context');
        $this->create_editor_draft($coursecontext, $otheruser->id,
            'id_course_description', 'text for other user at course context');

        // The list of users for usercontext should return user.
        $userlist1 = new \core_privacy\local\request\userlist($usercontext, $component);
        provider::get_users_in_context($userlist1);
        $this->assertCount(1, $userlist1);
        $this->assertTrue(in_array($user->id, $userlist1->get_userids()));

        // The list of users for otherusercontext should return otheruser.
        $userlist2 = new \core_privacy\local\request\userlist($otherusercontext, $component);
        provider::get_users_in_context($userlist2);
        $this->assertCount(1, $userlist2);
        $this->assertTrue(in_array($otheruser->id, $userlist2->get_userids()));

        // Add userlist1 to the approved user list.
        $approvedlist = new approved_userlist($usercontext, $component, $userlist1->get_userids());
        // Delete user data using delete_data_for_user for usercontext.
        provider::delete_data_for_users($approvedlist);

        // Re-fetch users in usercontext - The user list should now be empty.
        $userlist1 = new \core_privacy\local\request\userlist($usercontext, $component);
        provider::get_users_in_context($userlist1);
        $this->assertCount(0, $userlist1);
        // Re-fetch users in otherusercontext - The user list should not be empty (otheruser).
        $userlist2 = new \core_privacy\local\request\userlist($otherusercontext, $component);
        provider::get_users_in_context($userlist2);
        $this->assertCount(1, $userlist2);
        $this->assertTrue(in_array($otheruser->id, $userlist2->get_userids()));

        // The list of users for systemcontext should return user and otheruser.
        $userlist3 = new \core_privacy\local\request\userlist($systemcontext, $component);
        provider::get_users_in_context($userlist3);
        $this->assertCount(2, $userlist3);
        $this->assertTrue(in_array($user->id, $userlist3->get_userids()));
        $this->assertTrue(in_array($otheruser->id, $userlist3->get_userids()));

        // Add $userlist3 to the approved user list in the system context.
        $approvedlist = new approved_userlist($systemcontext, $component, $userlist3->get_userids());
        // Delete user and otheruser data using delete_data_for_user.
        provider::delete_data_for_users($approvedlist);

        // Re-fetch users in systemcontext - The user list should be empty.
        $userlist3 = new \core_privacy\local\request\userlist($systemcontext, $component);
        provider::get_users_in_context($userlist3);
        $this->assertCount(0, $userlist3);

        // The list of users for coursecontext should return user and otheruser.
        $userlist4 = new \core_privacy\local\request\userlist($coursecontext, $component);
        provider::get_users_in_context($userlist4);
        $this->assertCount(2, $userlist4);
        $this->assertTrue(in_array($user->id, $userlist4->get_userids()));
        $this->assertTrue(in_array($otheruser->id, $userlist4->get_userids()));

        // Add user to the approved user list in the course context.
        $approvedlist = new approved_userlist($coursecontext, $component, [$user->id]);
        // Delete user data using delete_data_for_user.
        provider::delete_data_for_users($approvedlist);

        // Re-fetch users in coursecontext - The user list should return otheruser.
        $userlist4 = new \core_privacy\local\request\userlist($coursecontext, $component);
        provider::get_users_in_context($userlist4);
        $this->assertCount(1, $userlist4);
        $this->assertTrue(in_array($otheruser->id, $userlist4->get_userids()));
    }

    /**
     * Test fetch and delete when another user has editted a draft in your
     * user context. Edge case.
     */
    public function test_another_user_edits_you() {
        global $USER, $DB;
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        $usercontext = \context_user::instance($user->id);
        $otheruser = $this->getDataGenerator()->create_user();
        $otherusercontext = \context_user::instance($otheruser->id);
        $this->setUser($user);

        $userdraftintro = $this->create_editor_draft($usercontext, $otheruser->id,
                'id_user_intro', 'text for test user at other context');

        // Test as the owning user.
        $this->setUser($user);
        $contextlist = provider::get_contexts_for_userid($user->id);
        $contexts = $contextlist->get_contexts();
        $this->assertCount(1, $contexts);
        $firstcontext = reset($contexts);
        $this->assertEquals($usercontext, $firstcontext);

        // Should have the data.
        $this->export_context_data_for_user($user->id, $usercontext, 'editor_atto');
        /** @var \core_privacy\tests\request\content_writer $writer */
        $writer = \core_privacy\local\request\writer::with_context($usercontext);
        $this->assertTrue($writer->has_any_data());

        $subcontext = [
            get_string('autosaves', 'editor_atto'),
            $userdraftintro->id,
        ];
        $data = $writer->get_data($subcontext);
        $this->assertEquals(\core_privacy\local\request\transform::user($otheruser->id), $data->author);

        $contextlist = new \core_privacy\tests\request\approved_contextlist(
            \core_user::get_user($user->id),
            'editor_atto',
            [$usercontext->id]
        );


        // Deleting for this context should _not_ delete as the user does not own this draft (crazy edge case, remember).
        provider::delete_data_for_user($contextlist);
        $records = $DB->get_records('editor_atto_autosave');
        $this->assertNotEmpty($records);
        $this->assertCount(1, $records);
        $firstrecord = reset($records);
        $this->assertEquals($userdraftintro->id, $firstrecord->id);
    }

    /**
     * Test fetch and delete when you have edited another user's context.
     */
    public function test_another_you_edit_different_user() {
        global $USER, $DB;
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        $usercontext = \context_user::instance($user->id);
        $otheruser = $this->getDataGenerator()->create_user();
        $otherusercontext = \context_user::instance($otheruser->id);
        $this->setUser($user);

        $userdraftintro = $this->create_editor_draft($otherusercontext, $user->id,
                'id_user_intro', 'text for other user you just edited.');

        // Test as the context owner.
        $this->setUser($user);
        $contextlist = provider::get_contexts_for_userid($user->id);
        $contexts = $contextlist->get_contexts();
        $this->assertCount(1, $contexts);
        $firstcontext = reset($contexts);
        $this->assertEquals($otherusercontext, $firstcontext);

        // Should have the data.
        $this->export_context_data_for_user($user->id, $otherusercontext, 'editor_atto');
        /** @var \core_privacy\tests\request\content_writer $writer */
        $writer = \core_privacy\local\request\writer::with_context($otherusercontext);
        $this->assertTrue($writer->has_any_data());

        $subcontext = [
            get_string('autosaves', 'editor_atto'),
            $userdraftintro->id,
        ];
        $data = $writer->get_data($subcontext);
        $this->assertFalse(isset($data->author));

        $contextlist = new \core_privacy\tests\request\approved_contextlist(
            \core_user::get_user($user->id),
            'editor_atto',
            [$otherusercontext->id]
        );
        provider::delete_data_for_user($contextlist);
        $this->assertEmpty($DB->get_records('editor_atto_autosave'));
    }

   /**
     * Create an editor draft.
     *
     * @param   \context    $context The context to create the draft for.
     * @param   int         $userid The ID to create the draft for.
     * @param   string      $elementid The elementid for the editor.
     * @param   string      $text The text to write.
     * @param   int         $filecount The number of files to create.
     * @return  \stdClass   The editor draft.
     */
    protected function create_editor_draft(\context $context, $userid, $elementid, $text, $filecount = 0) {
        global $DB;

        $draftid = file_get_unused_draft_itemid();
        $fs = get_file_storage();

        for ($i = 0; $i < $filecount; $i++) {
            $fs->create_file_from_string([
                    'contextid' => $context->id,
                    'component' => 'user',
                    'filearea'  => 'draft',
                    'itemid'    => $draftid,
                    'filepath'  => '/',
                    'filename'  => "example_{$i}.txt",
                ],
            "Awesome example of a text file with id {$i} for {$context->id} and {$elementid}");
        }

        $id = $DB->insert_record('editor_atto_autosave', (object) [
                'elementid' => $elementid,
                'contextid' => $context->id,
                'userid' => $userid,
                'drafttext' => $text,
                'draftid' => $draftid,
                'pageinstance' => 'example_page_instance_' . rand(1, 1000),
                'timemodified' => time(),

                // Page hash doesn't matter for our purposes.
                'pagehash' => sha1("{$userid}/{$context->id}/{$elementid}/{$draftid}"),
            ]);

        return $DB->get_record('editor_atto_autosave', ['id' => $id]);
    }
}
