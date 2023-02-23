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
 * @package    mod_lti
 * @copyright  2018 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_lti\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use mod_lti\privacy\provider;

defined('MOODLE_INTERNAL') || die();

/**
 * Privacy provider tests class.
 *
 * @package    mod_lti
 * @copyright  2018 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider_test extends \core_privacy\tests\provider_testcase {

    /**
     * Test for provider::get_metadata().
     */
    public function test_get_metadata() {
        $collection = new collection('mod_lti');
        $newcollection = provider::get_metadata($collection);
        $itemcollection = $newcollection->get_collection();
        $this->assertCount(4, $itemcollection);

        $ltiproviderexternal = array_shift($itemcollection);
        $this->assertEquals('lti_provider', $ltiproviderexternal->get_name());

        $ltisubmissiontable = array_shift($itemcollection);
        $this->assertEquals('lti_submission', $ltisubmissiontable->get_name());

        $ltitoolproxies = array_shift($itemcollection);
        $this->assertEquals('lti_tool_proxies', $ltitoolproxies->get_name());

        $ltitypestable = array_shift($itemcollection);
        $this->assertEquals('lti_types', $ltitypestable->get_name());

        $privacyfields = $ltisubmissiontable->get_privacy_fields();
        $this->assertArrayHasKey('userid', $privacyfields);
        $this->assertArrayHasKey('datesubmitted', $privacyfields);
        $this->assertArrayHasKey('dateupdated', $privacyfields);
        $this->assertArrayHasKey('gradepercent', $privacyfields);
        $this->assertArrayHasKey('originalgrade', $privacyfields);
        $this->assertEquals('privacy:metadata:lti_submission', $ltisubmissiontable->get_summary());

        $privacyfields = $ltitoolproxies->get_privacy_fields();
        $this->assertArrayHasKey('name', $privacyfields);
        $this->assertArrayHasKey('createdby', $privacyfields);
        $this->assertArrayHasKey('timecreated', $privacyfields);
        $this->assertArrayHasKey('timemodified', $privacyfields);
        $this->assertEquals('privacy:metadata:lti_tool_proxies', $ltitoolproxies->get_summary());

        $privacyfields = $ltitypestable->get_privacy_fields();
        $this->assertArrayHasKey('name', $privacyfields);
        $this->assertArrayHasKey('createdby', $privacyfields);
        $this->assertArrayHasKey('timecreated', $privacyfields);
        $this->assertArrayHasKey('timemodified', $privacyfields);
        $this->assertEquals('privacy:metadata:lti_types', $ltitypestable->get_summary());
    }

    /**
     * Test for provider::get_contexts_for_userid().
     */
    public function test_get_contexts_for_userid() {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();

        // The LTI activity the user will have submitted something for.
        $lti = $this->getDataGenerator()->create_module('lti', array('course' => $course->id));

        // Another LTI activity that has no user activity.
        $this->getDataGenerator()->create_module('lti', array('course' => $course->id));

        // Create a user which will make a submission.
        $user = $this->getDataGenerator()->create_user();

        $this->create_lti_submission($lti->id, $user->id);

        // Check the contexts supplied are correct.
        $contextlist = provider::get_contexts_for_userid($user->id);
        $this->assertCount(2, $contextlist);

        $contextformodule = $contextlist->current();
        $cmcontext = \context_module::instance($lti->cmid);
        $this->assertEquals($cmcontext->id, $contextformodule->id);

        $contextlist->next();
        $contextforsystem = $contextlist->current();
        $this->assertEquals(SYSCONTEXTID, $contextforsystem->id);
    }

    /**
     * Test for provider::test_get_users_in_context()
     */
    public function test_get_users_in_context() {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $component = 'mod_lti';

        // The LTI activity the user will have submitted something for.
        $lti1 = $this->getDataGenerator()->create_module('lti', array('course' => $course->id));

        // Another LTI activity that has no user activity.
        $lti2 = $this->getDataGenerator()->create_module('lti', array('course' => $course->id));

        // Create user which will make a submission each.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        $this->create_lti_submission($lti1->id, $user1->id);
        $this->create_lti_submission($lti1->id, $user2->id);

        $context = \context_module::instance($lti1->cmid);
        $userlist = new \core_privacy\local\request\userlist($context, $component);
        provider::get_users_in_context($userlist);

        $this->assertCount(2, $userlist);
        $expected = [$user1->id, $user2->id];
        $actual = $userlist->get_userids();
        sort($expected);
        sort($actual);

        $this->assertEquals($expected, $actual);

        $context = \context_module::instance($lti2->cmid);
        $userlist = new \core_privacy\local\request\userlist($context, $component);
        provider::get_users_in_context($userlist);

        $this->assertEmpty($userlist);
    }

    /**
     * Test for provider::export_user_data().
     */
    public function test_export_for_context_submissions() {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();

        $lti = $this->getDataGenerator()->create_module('lti', array('course' => $course->id));

        // Create users which will make submissions.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        $this->create_lti_submission($lti->id, $user1->id);
        $this->create_lti_submission($lti->id, $user1->id);
        $this->create_lti_submission($lti->id, $user2->id);

        // Export all of the data for the context for user 1.
        $cmcontext = \context_module::instance($lti->cmid);
        $this->export_context_data_for_user($user1->id, $cmcontext, 'mod_lti');
        $writer = \core_privacy\local\request\writer::with_context($cmcontext);

        $this->assertTrue($writer->has_any_data());

        $data = $writer->get_data();
        $this->assertCount(2, $data->submissions);
    }

    /**
     * Test for provider::export_user_data().
     */
    public function test_export_for_context_tool_types() {
        $this->resetAfterTest();

        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();

        // Create a user which will make a tool type.
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        // Create a user that will not make a tool type.
        $this->getDataGenerator()->create_user();

        $type = new \stdClass();
        $type->baseurl = 'http://moodle.org';
        $type->course = $course1->id;
        lti_add_type($type, new \stdClass());

        $type = new \stdClass();
        $type->baseurl = 'http://moodle.org';
        $type->course = $course1->id;
        lti_add_type($type, new \stdClass());

        $type = new \stdClass();
        $type->baseurl = 'http://moodle.org';
        $type->course = $course2->id;
        lti_add_type($type, new \stdClass());

        // Export all of the data for the context.
        $coursecontext = \context_course::instance($course1->id);
        $this->export_context_data_for_user($user->id, $coursecontext, 'mod_lti');
        $writer = \core_privacy\local\request\writer::with_context($coursecontext);

        $this->assertTrue($writer->has_any_data());

        $data = $writer->get_data();
        $this->assertCount(2, $data->lti_types);

        $coursecontext = \context_course::instance($course2->id);
        $this->export_context_data_for_user($user->id, $coursecontext, 'mod_lti');
        $writer = \core_privacy\local\request\writer::with_context($coursecontext);

        $this->assertTrue($writer->has_any_data());

        $data = $writer->get_data();
        $this->assertCount(1, $data->lti_types);
    }

    /**
     * Test for provider::export_user_data().
     */
    public function test_export_for_context_tool_proxies() {
        $this->resetAfterTest();

        // Create a user that will not make a tool proxy.
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        $toolproxy = new \stdClass();
        $toolproxy->createdby = $user;
        lti_add_tool_proxy($toolproxy);

        // Export all of the data for the context.
        $systemcontext = \context_system::instance();
        $this->export_context_data_for_user($user->id, $systemcontext, 'mod_lti');
        $writer = \core_privacy\local\request\writer::with_context($systemcontext);

        $this->assertTrue($writer->has_any_data());

        $data = $writer->get_data();
        $this->assertCount(1, $data->lti_tool_proxies);
    }

    /**
     * Test for provider::delete_data_for_all_users_in_context().
     */
    public function test_delete_data_for_all_users_in_context() {
        global $DB;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();

        $lti = $this->getDataGenerator()->create_module('lti', array('course' => $course->id));

        // Create users that will make submissions.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        $this->create_lti_submission($lti->id, $user1->id);
        $this->create_lti_submission($lti->id, $user2->id);

        // Before deletion, we should have 2 responses.
        $count = $DB->count_records('lti_submission', ['ltiid' => $lti->id]);
        $this->assertEquals(2, $count);

        // Delete data based on context.
        $cmcontext = \context_module::instance($lti->cmid);
        provider::delete_data_for_all_users_in_context($cmcontext);

        // After deletion, the lti submissions for that lti activity should have been deleted.
        $count = $DB->count_records('lti_submission', ['ltiid' => $lti->id]);
        $this->assertEquals(0, $count);
    }

    /**
     * Test for provider::delete_data_for_user().
     */
    public function test_delete_data_for_user() {
        global $DB;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();

        $lti = $this->getDataGenerator()->create_module('lti', array('course' => $course->id));

        // Create users that will make submissions.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        $this->create_lti_submission($lti->id, $user1->id);
        $this->create_lti_submission($lti->id, $user2->id);

        // Before deletion we should have 2 responses.
        $count = $DB->count_records('lti_submission', ['ltiid' => $lti->id]);
        $this->assertEquals(2, $count);

        $context = \context_module::instance($lti->cmid);
        $contextlist = new approved_contextlist($user1, 'lti',
            [\context_system::instance()->id, $context->id]);
        provider::delete_data_for_user($contextlist);

        // After deletion the lti submission for the first user should have been deleted.
        $count = $DB->count_records('lti_submission', ['ltiid' => $lti->id, 'userid' => $user1->id]);
        $this->assertEquals(0, $count);

        // Check the submission for the other user is still there.
        $ltisubmission = $DB->get_records('lti_submission');
        $this->assertCount(1, $ltisubmission);
        $lastsubmission = reset($ltisubmission);
        $this->assertEquals($user2->id, $lastsubmission->userid);
    }

    /**
     * Test for provider::delete_data_for_users().
     */
    public function test_delete_data_for_users() {
        global $DB;
        $component = 'mod_lti';

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();

        $lti = $this->getDataGenerator()->create_module('lti', array('course' => $course->id));

        // Create users that will make submissions.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();

        $this->create_lti_submission($lti->id, $user1->id);
        $this->create_lti_submission($lti->id, $user2->id);
        $this->create_lti_submission($lti->id, $user3->id);

        // Before deletion we should have 2 responses.
        $count = $DB->count_records('lti_submission', ['ltiid' => $lti->id]);
        $this->assertEquals(3, $count);

        $context = \context_module::instance($lti->cmid);
        $approveduserids = [$user1->id, $user2->id];
        $approvedlist = new approved_userlist($context, $component, $approveduserids);
        provider::delete_data_for_users($approvedlist);

        // After deletion the lti submission for the first two users should have been deleted.
        list($insql, $inparams) = $DB->get_in_or_equal($approveduserids, SQL_PARAMS_NAMED);
        $sql = "ltiid = :ltiid AND userid {$insql}";
        $params = array_merge($inparams, ['ltiid' => $lti->id]);
        $count = $DB->count_records_select('lti_submission', $sql, $params);
        $this->assertEquals(0, $count);

        // Check the submission for the third user is still there.
        $ltisubmission = $DB->get_records('lti_submission');
        $this->assertCount(1, $ltisubmission);
        $lastsubmission = reset($ltisubmission);
        $this->assertEquals($user3->id, $lastsubmission->userid);
    }

    /**
     * Mimicks the creation of an LTI submission.
     *
     * There is no API we can use to insert an LTI submission, so we
     * will simply insert directly into the database.
     *
     * @param int $ltiid
     * @param int $userid
     */
    protected function create_lti_submission(int $ltiid, int $userid) {
        global $DB;

        $ltisubmissiondata = [
            'ltiid' => $ltiid,
            'userid' => $userid,
            'datesubmitted' => time(),
            'dateupdated' => time(),
            'gradepercent' => 65,
            'originalgrade' => 70,
            'launchid' => 3,
            'state' => 1
        ];

        $DB->insert_record('lti_submission', $ltisubmissiondata);
    }
}
