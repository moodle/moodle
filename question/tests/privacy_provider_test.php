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
 * @package    core_question
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core_privacy\local\metadata\collection;
use core_privacy\local\request\deletion_criteria;
use core_privacy\local\request\writer;
use core_question\privacy\provider;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/xmlize.php');
require_once(__DIR__ . '/privacy_helper.php');
require_once(__DIR__ . '/../engine/tests/helpers.php');

/**
 * Privacy provider tests class.
 *
 * @package    core_question
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_question_privacy_provider_testcase extends \core_privacy\tests\provider_testcase {

    // Include the privacy helper which has assertions on it.
    use core_question_privacy_helper;

    /**
     * Prepare a question attempt.
     *
     * @return  question_usage_by_activity
     */
    protected function prepare_question_attempt() {
        // Create a question with a usage from the current user.
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $questiongenerator->create_question_category();
        $quba = question_engine::make_questions_usage_by_activity('core_question_preview', context_system::instance());
        $quba->set_preferred_behaviour('deferredfeedback');
        $questiondata = $questiongenerator->create_question('numerical', null, ['category' => $cat->id]);
        $question = question_bank::load_question($questiondata->id);
        $quba->add_question($question);
        $quba->start_all_questions();

        question_engine::save_questions_usage_by_activity($quba);

        return $quba;
    }

    /**
     * Test that calling export_question_usage on a usage belonging to a
     * different user does not export any data.
     */
    public function test_export_question_usage_no_usage() {
        $this->resetAfterTest();

        $quba = $this->prepare_question_attempt();

        // Create a question with a usage from the current user.
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $questiongenerator->create_question_category();
        $quba = question_engine::make_questions_usage_by_activity('core_question_preview', context_system::instance());
        $quba->set_preferred_behaviour('deferredfeedback');
        $questiondata = $questiongenerator->create_question('numerical', null, ['category' => $cat->id]);
        $question = question_bank::load_question($questiondata->id);
        $quba->add_question($question);
        $quba->start_all_questions();

        question_engine::save_questions_usage_by_activity($quba);

        // Set the user.
        $testuser = $this->getDataGenerator()->create_user();
        $this->setUser($testuser);
        $context = $quba->get_owning_context();
        $options = new \question_display_options();

        provider::export_question_usage($testuser->id, $context, [], $quba->get_id(), $options, false);
        $writer = writer::with_context($context);

        $this->assertFalse($writer->has_any_data_in_any_context());
    }

    /**
     * Test that calling export_question_usage on a usage belonging to a
     * different user but ignoring the user match
     */
    public function test_export_question_usage_with_usage() {
        $this->resetAfterTest();

        $quba = $this->prepare_question_attempt();

        // Create a question with a usage from the current user.
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $questiongenerator->create_question_category();
        $quba = question_engine::make_questions_usage_by_activity('core_question_preview', context_system::instance());
        $quba->set_preferred_behaviour('deferredfeedback');

        $questiondata = $questiongenerator->create_question('truefalse', 'true', ['category' => $cat->id]);
        $quba->add_question(question_bank::load_question($questiondata->id));
        $questiondata = $questiongenerator->create_question('shortanswer', null, ['category' => $cat->id]);
        $quba->add_question(question_bank::load_question($questiondata->id));

        // Set the user and answer the questions.
        $testuser = $this->getDataGenerator()->create_user();
        $this->setUser($testuser);

        $quba->start_all_questions();
        $quba->process_action(1, ['answer' => 1]);
        $quba->process_action(2, ['answer' => 'cat']);
        $quba->finish_all_questions();

        question_engine::save_questions_usage_by_activity($quba);

        $context = $quba->get_owning_context();

        // Export all questions for this attempt.
        $options = new \question_display_options();
        provider::export_question_usage($testuser->id, $context, [], $quba->get_id(), $options, true);
        $writer = writer::with_context($context);

        $this->assertTrue($writer->has_any_data_in_any_context());
        $this->assertTrue($writer->has_any_data());

        $slots = $quba->get_slots();
        $this->assertCount(2, $slots);

        foreach ($slots as $slotno) {
            $data = $writer->get_data([get_string('questions', 'core_question'), $slotno]);
            $this->assertNotNull($data);
            $this->assert_question_slot_equals($quba, $slotno, $options, $data);
        }

        $this->assertEmpty($writer->get_data([get_string('questions', 'core_question'), $quba->next_slot_number()]));

        // Disable some options and re-export.
        writer::reset();
        $options = new \question_display_options();
        $options->hide_all_feedback();
        $options->flags = \question_display_options::HIDDEN;
        $options->marks = \question_display_options::HIDDEN;

        provider::export_question_usage($testuser->id, $context, [], $quba->get_id(), $options, true);
        $writer = writer::with_context($context);

        $this->assertTrue($writer->has_any_data_in_any_context());
        $this->assertTrue($writer->has_any_data());

        $slots = $quba->get_slots();
        $this->assertCount(2, $slots);

        foreach ($slots as $slotno) {
            $data = $writer->get_data([get_string('questions', 'core_question'), $slotno]);
            $this->assertNotNull($data);
            $this->assert_question_slot_equals($quba, $slotno, $options, $data);
        }

        $this->assertEmpty($writer->get_data([get_string('questions', 'core_question'), $quba->next_slot_number()]));
    }

    /**
     * Test that questions owned by a user are exported and never deleted.
     */
    public function test_question_owned_is_handled() {
        global $DB;
        $this->resetAfterTest();

        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');

        // Create the two test users.
        $user = $this->getDataGenerator()->create_user();
        $otheruser = $this->getDataGenerator()->create_user();

        // Create one question as each user in diferent contexts.
        $this->setUser($user);
        $userdata = $questiongenerator->setup_course_and_questions();
        $expectedcontext = \context_course::instance($userdata[1]->id);

        $this->setUser($otheruser);
        $otheruserdata = $questiongenerator->setup_course_and_questions();
        $unexpectedcontext = \context_course::instance($otheruserdata[1]->id);

        // And create another one where we'll update a question as the test user.
        $moreotheruserdata = $questiongenerator->setup_course_and_questions();
        $otherexpectedcontext = \context_course::instance($moreotheruserdata[1]->id);
        $morequestions = $moreotheruserdata[3];

        // Update the third set of questions.
        $this->setUser($user);

        foreach ($morequestions as $question) {
            $questiongenerator->update_question($question);
        }

        // Run the get_contexts_for_userid as default user.
        $this->setUser();

        // There should be two contexts returned - the first course, and the third.
        $contextlist = provider::get_contexts_for_userid($user->id);
        $this->assertCount(2, $contextlist);

        $expectedcontexts = [
                $expectedcontext->id,
                $otherexpectedcontext->id,
            ];
        $this->assertEquals($expectedcontexts, $contextlist->get_contextids(), 'Contexts not equal', 0.0, 10, true);

        // Run the export_user_Data as the test user.
        $this->setUser($user);

        $approvedcontextlist = new \core_privacy\tests\request\approved_contextlist(
            \core_user::get_user($user->id),
            'core_question',
            $expectedcontexts
        );
        provider::export_user_data($approvedcontextlist);

        // There should be data for the user's question context.
        $writer = writer::with_context($expectedcontext);
        $this->assertTrue($writer->has_any_data());

        // And for the course we updated.
        $otherwriter = writer::with_context($otherexpectedcontext);
        $this->assertTrue($otherwriter->has_any_data());

        // But not for the other user's course.
        $otherwriter = writer::with_context($unexpectedcontext);
        $this->assertFalse($otherwriter->has_any_data());

        // The question data is exported as an XML export in custom files.
        $writer = writer::with_context($expectedcontext);
        $subcontext = [get_string('questionbank', 'core_question')];

        $exportfile = $writer->get_custom_file($subcontext, 'questions.xml');
        $this->assertNotEmpty($exportfile);

        $xmlized = xmlize($exportfile);
        $xmlquestions = $xmlized['quiz']['#']['question'];

        $this->assertCount(2, $xmlquestions);

        // Run the delete functions as default user.
        $this->setUser();

        // The delete functions should do nothing here.
        $this->assertCount(6, $DB->get_records('question'));

        // Delete for all users in context.
        provider::delete_data_for_all_users_in_context($expectedcontext);
        $this->assertCount(6, $DB->get_records('question'));

        provider::delete_data_for_user($approvedcontextlist);
        $this->assertCount(6, $DB->get_records('question'));
    }

    /**
     * Deleting questions should only unset their created and modified user.
     */
    public function test_question_delete_data_for_user_anonymised() {
        global $DB;
        $this->resetAfterTest(true);

        $user = \core_user::get_user_by_username('admin');
        $otheruser = $this->getDataGenerator()->create_user();

        $course = $this->getDataGenerator()->create_course();
        $context = \context_course::instance($course->id);
        $othercourse = $this->getDataGenerator()->create_course();
        $othercontext = \context_course::instance($othercourse->id);

        // Create a couple of questions.
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $questiongenerator->create_question_category([
            'contextid' => $context->id,
        ]);
        $othercat = $questiongenerator->create_question_category([
            'contextid' => $othercontext->id,
        ]);

        // Create questions:
        // Q1 - Created by the UUT, Modified by UUT.
        // Q2 - Created by the UUT, Modified by the other user.
        // Q3 - Created by the other user, Modified by UUT
        // Q4 - Created by the other user, Modified by the other user.
        // Q5 - Created by the UUT, Modified by the UUT, but in a different context.
        $this->setUser($user);
        $q1 = $questiongenerator->create_question('shortanswer', null, array('category' => $cat->id));
        $q2 = $questiongenerator->create_question('shortanswer', null, array('category' => $cat->id));

        $this->setUser($otheruser);
        $questiongenerator->update_question($q2);
        $q3 = $questiongenerator->create_question('shortanswer', null, array('category' => $cat->id));
        $q4 = $questiongenerator->create_question('shortanswer', null, array('category' => $cat->id));

        $this->setUser($user);
        $questiongenerator->update_question($q3);
        $q5 = $questiongenerator->create_question('shortanswer', null, array('category' => $othercat->id));

        $approvedcontextlist = new \core_privacy\tests\request\approved_contextlist(
            $user,
            'core_question',
            [$context->id]
        );

        // Delete the data and check it is removed.
        $this->setUser();
        provider::delete_data_for_user($approvedcontextlist);

        $this->assertCount(5, $DB->get_records('question'));

        $qrecord = $DB->get_record('question', ['id' => $q1->id]);
        $this->assertEquals(0, $qrecord->createdby);
        $this->assertEquals(0, $qrecord->modifiedby);

        $qrecord = $DB->get_record('question', ['id' => $q2->id]);
        $this->assertEquals(0, $qrecord->createdby);
        $this->assertEquals($otheruser->id, $qrecord->modifiedby);

        $qrecord = $DB->get_record('question', ['id' => $q3->id]);
        $this->assertEquals($otheruser->id, $qrecord->createdby);
        $this->assertEquals(0, $qrecord->modifiedby);

        $qrecord = $DB->get_record('question', ['id' => $q4->id]);
        $this->assertEquals($otheruser->id, $qrecord->createdby);
        $this->assertEquals($otheruser->id, $qrecord->modifiedby);

        $qrecord = $DB->get_record('question', ['id' => $q5->id]);
        $this->assertEquals($user->id, $qrecord->createdby);
        $this->assertEquals($user->id, $qrecord->modifiedby);
    }

    /**
     * Deleting questions should only unset their created and modified user for all questions in a context.
     */
    public function test_question_delete_data_for_all_users_in_context_anonymised() {
        global $DB;
        $this->resetAfterTest(true);

        $user = \core_user::get_user_by_username('admin');
        $otheruser = $this->getDataGenerator()->create_user();

        $course = $this->getDataGenerator()->create_course();
        $context = \context_course::instance($course->id);
        $othercourse = $this->getDataGenerator()->create_course();
        $othercontext = \context_course::instance($othercourse->id);

        // Create a couple of questions.
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $questiongenerator->create_question_category([
            'contextid' => $context->id,
        ]);
        $othercat = $questiongenerator->create_question_category([
            'contextid' => $othercontext->id,
        ]);

        // Create questions:
        // Q1 - Created by the UUT, Modified by UUT.
        // Q2 - Created by the UUT, Modified by the other user.
        // Q3 - Created by the other user, Modified by UUT
        // Q4 - Created by the other user, Modified by the other user.
        // Q5 - Created by the UUT, Modified by the UUT, but in a different context.
        $this->setUser($user);
        $q1 = $questiongenerator->create_question('shortanswer', null, array('category' => $cat->id));
        $q2 = $questiongenerator->create_question('shortanswer', null, array('category' => $cat->id));

        $this->setUser($otheruser);
        $questiongenerator->update_question($q2);
        $q3 = $questiongenerator->create_question('shortanswer', null, array('category' => $cat->id));
        $q4 = $questiongenerator->create_question('shortanswer', null, array('category' => $cat->id));

        $this->setUser($user);
        $questiongenerator->update_question($q3);
        $q5 = $questiongenerator->create_question('shortanswer', null, array('category' => $othercat->id));

        // Delete the data and check it is removed.
        $this->setUser();
        provider::delete_data_for_all_users_in_context($context);

        $this->assertCount(5, $DB->get_records('question'));

        $qrecord = $DB->get_record('question', ['id' => $q1->id]);
        $this->assertEquals(0, $qrecord->createdby);
        $this->assertEquals(0, $qrecord->modifiedby);

        $qrecord = $DB->get_record('question', ['id' => $q2->id]);
        $this->assertEquals(0, $qrecord->createdby);
        $this->assertEquals(0, $qrecord->modifiedby);

        $qrecord = $DB->get_record('question', ['id' => $q3->id]);
        $this->assertEquals(0, $qrecord->createdby);
        $this->assertEquals(0, $qrecord->modifiedby);

        $qrecord = $DB->get_record('question', ['id' => $q4->id]);
        $this->assertEquals(0, $qrecord->createdby);
        $this->assertEquals(0, $qrecord->modifiedby);

        $qrecord = $DB->get_record('question', ['id' => $q5->id]);
        $this->assertEquals($user->id, $qrecord->createdby);
        $this->assertEquals($user->id, $qrecord->modifiedby);
    }
}
