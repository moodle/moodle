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
 * @package    mod_choice
 * @copyright  2018 Jun Pataleta
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core_privacy\local\metadata\collection;
use core_privacy\local\request\deletion_criteria;
use mod_choice\privacy\provider;

defined('MOODLE_INTERNAL') || die();

/**
 * Privacy provider tests class.
 *
 * @package    mod_choice
 * @copyright  2018 Jun Pataleta
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_choice_privacy_provider_testcase extends \core_privacy\tests\provider_testcase {
    /** @var stdClass The student object. */
    protected $student;

    /** @var stdClass The choice object. */
    protected $choice;

    /** @var stdClass The course object. */
    protected $course;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void {
        $this->resetAfterTest();

        global $DB;
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $options = ['fried rice', 'spring rolls', 'sweet and sour pork', 'satay beef', 'gyouza'];
        $params = [
            'course' => $course->id,
            'option' => $options,
            'name' => 'First Choice Activity',
            'showpreview' => 0
        ];

        $plugingenerator = $generator->get_plugin_generator('mod_choice');
        // The choice activity the user will answer.
        $choice = $plugingenerator->create_instance($params);
        // Create another choice activity.
        $plugingenerator->create_instance($params);
        $cm = get_coursemodule_from_instance('choice', $choice->id);

        // Create a student which will make a choice.
        $student = $generator->create_user();
        $studentrole = $DB->get_record('role', ['shortname' => 'student']);
        $generator->enrol_user($student->id,  $course->id, $studentrole->id);

        $choicewithoptions = choice_get_choice($choice->id);
        $optionids = array_keys($choicewithoptions->option);

        choice_user_submit_response($optionids[2], $choice, $student->id, $course, $cm);
        $this->student = $student;
        $this->choice = $choice;
        $this->course = $course;
    }

    /**
     * Test for provider::get_metadata().
     */
    public function test_get_metadata() {
        $collection = new collection('mod_choice');
        $newcollection = provider::get_metadata($collection);
        $itemcollection = $newcollection->get_collection();
        $this->assertCount(1, $itemcollection);

        $table = reset($itemcollection);
        $this->assertEquals('choice_answers', $table->get_name());

        $privacyfields = $table->get_privacy_fields();
        $this->assertArrayHasKey('choiceid', $privacyfields);
        $this->assertArrayHasKey('optionid', $privacyfields);
        $this->assertArrayHasKey('userid', $privacyfields);
        $this->assertArrayHasKey('timemodified', $privacyfields);

        $this->assertEquals('privacy:metadata:choice_answers', $table->get_summary());
    }

    /**
     * Test for provider::get_contexts_for_userid().
     */
    public function test_get_contexts_for_userid() {
        $cm = get_coursemodule_from_instance('choice', $this->choice->id);

        $contextlist = provider::get_contexts_for_userid($this->student->id);
        $this->assertCount(1, $contextlist);
        $contextforuser = $contextlist->current();
        $cmcontext = context_module::instance($cm->id);
        $this->assertEquals($cmcontext->id, $contextforuser->id);
    }

    /**
     * Test for provider::export_user_data().
     */
    public function test_export_for_context() {
        $cm = get_coursemodule_from_instance('choice', $this->choice->id);
        $cmcontext = context_module::instance($cm->id);

        // Export all of the data for the context.
        $this->export_context_data_for_user($this->student->id, $cmcontext, 'mod_choice');
        $writer = \core_privacy\local\request\writer::with_context($cmcontext);
        $this->assertTrue($writer->has_any_data());
    }

    /**
     * Test for provider::delete_data_for_all_users_in_context().
     */
    public function test_delete_data_for_all_users_in_context() {
        global $DB;

        $choice = $this->choice;
        $generator = $this->getDataGenerator();
        $cm = get_coursemodule_from_instance('choice', $this->choice->id);

        // Create another student who will answer the choice activity.
        $student = $generator->create_user();
        $studentrole = $DB->get_record('role', ['shortname' => 'student']);
        $generator->enrol_user($student->id, $this->course->id, $studentrole->id);

        $choicewithoptions = choice_get_choice($choice->id);
        $optionids = array_keys($choicewithoptions->option);

        choice_user_submit_response($optionids[1], $choice, $student->id, $this->course, $cm);

        // Before deletion, we should have 2 responses.
        $count = $DB->count_records('choice_answers', ['choiceid' => $choice->id]);
        $this->assertEquals(2, $count);

        // Delete data based on context.
        $cmcontext = context_module::instance($cm->id);
        provider::delete_data_for_all_users_in_context($cmcontext);

        // After deletion, the choice answers for that choice activity should have been deleted.
        $count = $DB->count_records('choice_answers', ['choiceid' => $choice->id]);
        $this->assertEquals(0, $count);
    }

    /**
     * Test for provider::delete_data_for_user().
     */
    public function test_delete_data_for_user_() {
        global $DB;

        $choice = $this->choice;
        $generator = $this->getDataGenerator();
        $cm1 = get_coursemodule_from_instance('choice', $this->choice->id);

        // Create a second choice activity.
        $options = ['Boracay', 'Camiguin', 'Bohol', 'Cebu', 'Coron'];
        $params = [
            'course' => $this->course->id,
            'option' => $options,
            'name' => 'Which do you think is the best island in the Philippines?',
            'showpreview' => 0
        ];
        $plugingenerator = $generator->get_plugin_generator('mod_choice');
        $choice2 = $plugingenerator->create_instance($params);
        $plugingenerator->create_instance($params);
        $cm2 = get_coursemodule_from_instance('choice', $choice2->id);

        // Make a selection for the first student for the 2nd choice activity.
        $choicewithoptions = choice_get_choice($choice2->id);
        $optionids = array_keys($choicewithoptions->option);
        choice_user_submit_response($optionids[2], $choice2, $this->student->id, $this->course, $cm2);

        // Create another student who will answer the first choice activity.
        $otherstudent = $generator->create_user();
        $studentrole = $DB->get_record('role', ['shortname' => 'student']);
        $generator->enrol_user($otherstudent->id, $this->course->id, $studentrole->id);

        $choicewithoptions = choice_get_choice($choice->id);
        $optionids = array_keys($choicewithoptions->option);

        choice_user_submit_response($optionids[1], $choice, $otherstudent->id, $this->course, $cm1);

        // Before deletion, we should have 2 responses.
        $count = $DB->count_records('choice_answers', ['choiceid' => $choice->id]);
        $this->assertEquals(2, $count);

        $context1 = context_module::instance($cm1->id);
        $context2 = context_module::instance($cm2->id);
        $contextlist = new \core_privacy\local\request\approved_contextlist($this->student, 'choice',
            [context_system::instance()->id, $context1->id, $context2->id]);
        provider::delete_data_for_user($contextlist);

        // After deletion, the choice answers for the first student should have been deleted.
        $count = $DB->count_records('choice_answers', ['choiceid' => $choice->id, 'userid' => $this->student->id]);
        $this->assertEquals(0, $count);

        // Confirm that we only have one choice answer available.
        $choiceanswers = $DB->get_records('choice_answers');
        $this->assertCount(1, $choiceanswers);
        $lastresponse = reset($choiceanswers);
        // And that it's the other student's response.
        $this->assertEquals($otherstudent->id, $lastresponse->userid);
    }

    /**
     * Test for provider::get_users_in_context().
     */
    public function test_get_users_in_context() {
        $cm = get_coursemodule_from_instance('choice', $this->choice->id);
        $cmcontext = context_module::instance($cm->id);

        $userlist = new \core_privacy\local\request\userlist($cmcontext, 'mod_choice');
        \mod_choice\privacy\provider::get_users_in_context($userlist);

        $this->assertEquals(
                [$this->student->id],
                $userlist->get_userids()
        );
    }

    /**
     * Test for provider::get_users_in_context() with invalid context type.
     */
    public function test_get_users_in_context_invalid_context_type() {
        $systemcontext = context_system::instance();

        $userlist = new \core_privacy\local\request\userlist($systemcontext, 'mod_choice');
        \mod_choice\privacy\provider::get_users_in_context($userlist);

        $this->assertCount(0, $userlist->get_userids());
    }

    /**
     * Test for provider::delete_data_for_users().
     */
    public function test_delete_data_for_users() {
        global $DB;

        $choice = $this->choice;
        $generator = $this->getDataGenerator();
        $cm1 = get_coursemodule_from_instance('choice', $this->choice->id);

        // Create a second choice activity.
        $options = ['Boracay', 'Camiguin', 'Bohol', 'Cebu', 'Coron'];
        $params = [
            'course' => $this->course->id,
            'option' => $options,
            'name' => 'Which do you think is the best island in the Philippines?',
            'showpreview' => 0
        ];
        $plugingenerator = $generator->get_plugin_generator('mod_choice');
        $choice2 = $plugingenerator->create_instance($params);
        $plugingenerator->create_instance($params);
        $cm2 = get_coursemodule_from_instance('choice', $choice2->id);

        // Make a selection for the first student for the 2nd choice activity.
        $choicewithoptions = choice_get_choice($choice2->id);
        $optionids = array_keys($choicewithoptions->option);
        choice_user_submit_response($optionids[2], $choice2, $this->student->id, $this->course, $cm2);

        // Create 2 other students who will answer the first choice activity.
        $otherstudent = $generator->create_and_enrol($this->course, 'student');
        $anotherstudent = $generator->create_and_enrol($this->course, 'student');

        $choicewithoptions = choice_get_choice($choice->id);
        $optionids = array_keys($choicewithoptions->option);

        choice_user_submit_response($optionids[1], $choice, $otherstudent->id, $this->course, $cm1);
        choice_user_submit_response($optionids[1], $choice, $anotherstudent->id, $this->course, $cm1);

        // Before deletion, we should have 3 responses in the first choice activity.
        $count = $DB->count_records('choice_answers', ['choiceid' => $choice->id]);
        $this->assertEquals(3, $count);

        $context1 = context_module::instance($cm1->id);
        $approveduserlist = new \core_privacy\local\request\approved_userlist($context1, 'choice',
                [$this->student->id, $otherstudent->id]);
        provider::delete_data_for_users($approveduserlist);

        // After deletion, the choice answers of the 2 students provided above should have been deleted
        // from the first choice activity. So there should only remain 1 answer which is for $anotherstudent.
        $choiceanswers = $DB->get_records('choice_answers', ['choiceid' => $choice->id]);
        $this->assertCount(1, $choiceanswers);
        $lastresponse = reset($choiceanswers);
        $this->assertEquals($anotherstudent->id, $lastresponse->userid);

        // Confirm that the answer that was submitted in the other choice activity is intact.
        $choiceanswers = $DB->get_records_select('choice_answers', 'choiceid <> ?', [$choice->id]);
        $this->assertCount(1, $choiceanswers);
        $lastresponse = reset($choiceanswers);
        // And that it's for the choice2 activity.
        $this->assertEquals($choice2->id, $lastresponse->choiceid);
    }
}
