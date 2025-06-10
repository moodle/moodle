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
 * Privacy test for the mod questionnaire.
 *
 * @package    mod_questionnaire
 * @copyright  2019, onwards Poet
 * @author     Mike Churchward
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_questionnaire;

use \mod_questionnaire\privacy\provider;

/**
 * Privacy test for the mod questionnaire.
 *
 * @package    mod_questionnaire
 * @copyright  2019, onwards Poet
 * @author     Mike Churchward
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @group      mod_questionnaire
 */
class privacy_provider_test extends \core_privacy\tests\provider_testcase {
    /**
     * Tests set up.
     */
    public function setUp(): void {
        $this->resetAfterTest();
        $this->setAdminUser();
    }

    /**
     * Check that the expected context is returned if there is any user data for this module.
     *
     * @covers \mod_questionnaire\privacy\provider::get_contexts_for_userid
     */
    public function test_get_contexts_for_userid() {
        global $DB;

        $this->resetAfterTest();
        $dg = $this->getDataGenerator();
        $qdg = $dg->get_plugin_generator('mod_questionnaire');
        $qdg->create_and_fully_populate(1, 1, 1, 1);
        $user = $DB->get_record('user', ['firstname' => 'Testy']);
        $questionnaires = $qdg->questionnaires();
        $questionnaire = current($questionnaires);
        list ($course, $cm) = get_course_and_cm_from_instance($questionnaire->id, 'questionnaire', $questionnaire->course);

        $contextlist = provider::get_contexts_for_userid($user->id);
        // Check that we only get back one context.
        $this->assertCount(1, $contextlist);

        // Check that a context is returned and is the expected context.
        $cmcontext = \context_module::instance($cm->id);
        $this->assertEquals($cmcontext->id, $contextlist->get_contextids()[0]);
    }

    /**
     * Test that only users with a questionnaire context are fetched.
     *
     * @covers \mod_questionnaire\privacy\provider::get_users_in_context
     */
    public function test_get_users_in_context() {
        global $DB;

        $this->resetAfterTest();
        $dg = $this->getDataGenerator();
        $qdg = $dg->get_plugin_generator('mod_questionnaire');
        $qdg->create_and_fully_populate(1, 1, 1, 1);
        $user = $DB->get_record('user', ['firstname' => 'Testy']);
        $questionnaires = $qdg->questionnaires();
        $questionnaire = current($questionnaires);
        list ($course, $cm) = get_course_and_cm_from_instance($questionnaire->id, 'questionnaire', $questionnaire->course);
        $cmcontext = \context_module::instance($cm->id);

        $userlist = new \core_privacy\local\request\userlist($cmcontext, 'mod_questionnaire');

        // The list of users for this context should return the user.
        provider::get_users_in_context($userlist);
        $this->assertCount(1, $userlist);
        $expected = [$user->id];
        $actual = $userlist->get_userids();
        $this->assertEquals($expected, $actual);

        // The list of users for other contexts should not return any users.
        $userlist = new \core_privacy\local\request\userlist(\context_system::instance(), 'mod_questionnaire');
        provider::get_users_in_context($userlist);
        $this->assertCount(0, $userlist);
    }

    /**
     * Test that user data is exported correctly.
     *
     * @covers \mod_questionnaire\privacy\provider::export_user_data
     */
    public function test_export_user_data() {
        global $DB;

        $this->resetAfterTest();
        $dg = $this->getDataGenerator();
        $qdg = $dg->get_plugin_generator('mod_questionnaire');
        $qdg->create_and_fully_populate(1, 1, 1, 1);
        $user = $DB->get_record('user', ['firstname' => 'Testy']);
        $questionnaires = $qdg->questionnaires();
        $questionnaire = current($questionnaires);
        list ($course, $cm) = get_course_and_cm_from_instance($questionnaire->id, 'questionnaire', $questionnaire->course);
        $cmcontext = \context_module::instance($cm->id);

        $writer = \core_privacy\local\request\writer::with_context($cmcontext);
        $this->assertFalse($writer->has_any_data());

        $approvedlist = new \core_privacy\local\request\approved_contextlist($user, 'mod_questionnaire', [$cmcontext->id]);
        provider::export_user_data($approvedlist);
        $data = $writer->get_data([]);

        $this->assertStringContainsString($questionnaire->name, strip_tags($data->name));
        $this->assertEquals($questionnaire->intro, strip_tags($data->intro));
        $this->assertNotEmpty($data->responses[0]['questions']);
        $this->assertEquals('1. Text Box 1000', $data->responses[0]['questions'][1]->questionname);
        $this->assertEquals('Test answer', $data->responses[0]['questions'][1]->answers[0]);
        $this->assertEquals('7. Numeric 1004', $data->responses[0]['questions'][7]->questionname);
        $this->assertEquals(83, $data->responses[0]['questions'][7]->answers[0]);
        $this->assertEquals('22. Rate Scale 1014', $data->responses[0]['questions'][22]->questionname);
        $this->assertEquals('fourteen = 1', $data->responses[0]['questions'][22]->answers[0]);
        $this->assertEquals('happy = 3', $data->responses[0]['questions'][22]->answers[7]);
    }

    /**
     * Test deleting all user data for a specific context.
     *
     * @covers \mod_questionnaire\privacy\provider::delete_data_for_all_users_in_context
     */
    public function test_delete_data_for_all_users_in_context() {
        global $DB;

        $this->resetAfterTest();
        $dg = $this->getDataGenerator();
        $qdg = $dg->get_plugin_generator('mod_questionnaire');
        $qdg->create_and_fully_populate(1, 2, 1, 1);
        $user = $DB->get_record('user', ['username' => 'username1']);
        $questionnaires = $qdg->questionnaires();
        $questionnaire = current($questionnaires);
        list ($course, $cm) = get_course_and_cm_from_instance($questionnaire->id, 'questionnaire', $questionnaire->course);
        $cmcontext = \context_module::instance($cm->id);

        // Get all accounts. There should be two.
        $this->assertCount(2, $DB->get_records('questionnaire_response', []));

        // Delete everything for the context.
        provider::delete_data_for_all_users_in_context($cmcontext);
        $this->assertCount(0, $DB->get_records('questionnaire_response', []));
    }

    /**
     * This should work identical to the above test.
     *
     * @covers \mod_questionnaire\privacy\provider::delete_data_for_user
     */
    public function test_delete_data_for_user() {
        global $DB;

        $this->resetAfterTest();
        $dg = $this->getDataGenerator();
        $qdg = $dg->get_plugin_generator('mod_questionnaire');
        $qdg->create_and_fully_populate(1, 2, 1, 1);
        $user = $DB->get_record('user', ['username' => 'username1']);
        $questionnaires = $qdg->questionnaires();
        $questionnaire = current($questionnaires);
        list ($course, $cm) = get_course_and_cm_from_instance($questionnaire->id, 'questionnaire', $questionnaire->course);
        $cmcontext = \context_module::instance($cm->id);

        // Get all accounts. There should be two.
        $this->assertCount(2, $DB->get_records('questionnaire_response', []));

        // Delete everything for the first user.
        $approvedlist = new \core_privacy\local\request\approved_contextlist($user, 'questionnaire_response', [$cmcontext->id]);
        provider::delete_data_for_user($approvedlist);

        $this->assertCount(0, $DB->get_records('questionnaire_response', ['userid' => $user->id]));

        // Get all accounts. There should be one.
        $this->assertCount(1, $DB->get_records('questionnaire_response', []));
    }

    /**
     * Test that data for users in approved userlist is deleted.
     *
     * @covers \mod_questionnaire\privacy\provider::delete_data_for_users
     */
    public function test_delete_data_for_users() {
        global $DB;

        $this->resetAfterTest();
        $dg = $this->getDataGenerator();
        $qdg = $dg->get_plugin_generator('mod_questionnaire');
        $qdg->create_and_fully_populate(1, 3, 1, 1);
        $user = $DB->get_record('user', ['username' => 'username1']);
        $user3 = $DB->get_record('user', ['username' => 'username3']);
        $questionnaires = $qdg->questionnaires();
        $questionnaire = current($questionnaires);
        list ($course, $cm) = get_course_and_cm_from_instance($questionnaire->id, 'questionnaire', $questionnaire->course);
        $cmcontext = \context_module::instance($cm->id);

        $approveduserlist = new \core_privacy\local\request\approved_userlist($cmcontext, 'questionnaire', [$user->id, $user3->id]);

        // Get all accounts. There should be three.
        $this->assertCount(3, $DB->get_records('questionnaire_response', []));

        provider::delete_data_for_users($approveduserlist);

        // Get all accounts. There should be one now.
        $this->assertCount(1, $DB->get_records('questionnaire_response', []));
    }
}
