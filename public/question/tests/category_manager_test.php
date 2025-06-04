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

namespace core_question;

use context_course;
use context_module;
use moodle_url;
use core_question\local\bank\question_edit_contexts;

/**
 * Unit tests for category_manager
 *
 * @package   core_question
 * @copyright 2024 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author    Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \core_question\category_manager
 */
final class category_manager_test extends \advanced_testcase {

    /**
     * Create a course and qbank module and return the module context for use in tests.
     *
     * @return \core\context\module|false
     */
    private function create_course_and_get_qbank_context() {
        $course = self::getDataGenerator()->create_course();
        $qbank = self::getDataGenerator()->create_module('qbank', ['course' => $course->id]);
        return context_module::instance($qbank->cmid);
    }
    /**
     * Test creating a category.
     */
    public function test_add_category_no_idnumber(): void {
        global $DB;

        $this->setAdminUser();
        $this->resetAfterTest();
        $context = $this->create_course_and_get_qbank_context();
        $topcat = question_get_top_category($context->id, true);
        $manager = new category_manager();
        $id = $manager->add_category(
            $topcat->id . ',' . $topcat->contextid,
            'New category',
            '',
            FORMAT_HTML,
            '', // No idnumber passed as '' to match form data.
        );

        $newcat = $DB->get_record('question_categories', ['id' => $id], '*', MUST_EXIST);
        $this->assertSame('New category', $newcat->name);
        $this->assertNull($newcat->idnumber);
    }

    /**
     * Test creating a category with a tricky idnumber.
     */
    public function test_add_category_set_idnumber_0(): void {
        global $DB;

        $this->setAdminUser();
        $this->resetAfterTest();
        $context = $this->create_course_and_get_qbank_context();
        $topcat = question_get_top_category($context->id, true);
        $manager = new category_manager();
        $id = $manager->add_category(
            $topcat->id . ',' . $topcat->contextid,
            'New category',
            '',
            FORMAT_HTML,
            '0',
        );

        $newcat = $DB->get_record('question_categories', ['id' => $id], '*', MUST_EXIST);
        $this->assertSame('New category', $newcat->name);
        $this->assertSame('0', $newcat->idnumber);
    }

    /**
     * Trying to add a category with duplicate idnumber throws an exception.
     */
    public function test_add_category_try_to_set_duplicate_idnumber(): void {
        global $DB;

        $this->setAdminUser();
        $this->resetAfterTest();
        $context = $this->create_course_and_get_qbank_context();
        $topcat = question_get_top_category($context->id, true);
        $manager = new category_manager();
        $manager->add_category(
            $topcat->id . ',' . $topcat->contextid,
            'Existing category',
            '',
            FORMAT_HTML,
            'frog',
        );
        $this->expectExceptionMessage(get_string('idnumbertaken', 'error'));
        $manager->add_category(
            $topcat->id . ',' . $topcat->contextid,
            'New category',
            '',
            FORMAT_HTML,
            'frog',
        );
        $this->assertFalse($DB->record_exists('question_categories', ['name' => 'New category']));
    }

    /**
     * Test updating a category.
     */
    public function test_update_category(): void {
        global $DB;

        $this->setAdminUser();
        $this->resetAfterTest();
        $context = $this->create_course_and_get_qbank_context();
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $topcat = question_get_top_category($context->id, true);
        $category = $questiongenerator->create_question_category([
            'contextid' => $context->id,
            'name' => 'Old name',
            'info' => 'Description',
            'idnumber' => 'frog',
        ]);
        $existingcat = $DB->get_record('question_categories', ['id' => $category->id], '*', MUST_EXIST);
        $this->assertSame('Old name', $existingcat->name);
        $this->assertSame('Description', $existingcat->info);
        $this->assertSame('frog', $existingcat->idnumber);

        $manager = new category_manager(new moodle_url('/'));
        $manager->update_category(
            $category->id,
            $topcat->id . ',' . $topcat->contextid,
            'New name',
            'New description',
            FORMAT_HTML,
            '0'
        );

        $updatedcat = $DB->get_record('question_categories', ['id' => $category->id], '*', MUST_EXIST);
        $this->assertSame('New name', $updatedcat->name);
        $this->assertSame('New description', $updatedcat->info);
        $this->assertSame('0', $updatedcat->idnumber);
    }

    /**
     * Test updating a category to remove the idnumber.
     */
    public function test_update_category_removing_idnumber(): void {
        global $DB;

        $this->setAdminUser();
        $this->resetAfterTest();
        $context = $this->create_course_and_get_qbank_context();
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $topcat = question_get_top_category($context->id, true);
        $category = $questiongenerator->create_question_category([
            'contextid' => $context->id,
            'name' => 'Old name',
            'info' => 'Description',
            'idnumber' => 'frog',
        ]);

        $existingcat = $DB->get_record('question_categories', ['id' => $category->id], '*', MUST_EXIST);
        $this->assertSame('Old name', $existingcat->name);
        $this->assertSame('Description', $existingcat->info);
        $this->assertSame('frog', $existingcat->idnumber);

        $manager = new category_manager(new moodle_url('/'));
        $manager->update_category(
            $category->id,
            $topcat->id . ',' . $topcat->contextid,
            'New name',
            'New description',
            FORMAT_HTML,
            ''
        );

        $updatedcat = $DB->get_record('question_categories', ['id' => $category->id], '*', MUST_EXIST);
        $this->assertSame('New name', $updatedcat->name);
        $this->assertSame('New description', $updatedcat->info);
        $this->assertNull($updatedcat->idnumber);
    }

    /**
     * Test updating a category without changing the idnumber.
     */
    public function test_update_category_dont_change_idnumber(): void {
        global $DB;

        $this->setAdminUser();
        $this->resetAfterTest();
        $context = $this->create_course_and_get_qbank_context();
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $topcat = question_get_top_category($context->id, true);
        $category = $questiongenerator->create_question_category([
            'contextid' => $context->id,
            'name' => 'Old name',
            'info' => 'Description',
            'idnumber' => 'frog',
        ]);
        $existingcat = $DB->get_record('question_categories', ['id' => $category->id], '*', MUST_EXIST);
        $this->assertSame('Old name', $existingcat->name);
        $this->assertSame('Description', $existingcat->info);
        $this->assertSame('frog', $existingcat->idnumber);

        $manager = new category_manager(new moodle_url('/'));
        $manager->update_category(
            $category->id,
            $topcat->id . ',' . $topcat->contextid,
            'New name',
            'New description',
            FORMAT_HTML,
            'frog'
        );

        $updatedcat = $DB->get_record('question_categories', ['id' => $category->id], '*', MUST_EXIST);
        $this->assertSame('New name', $updatedcat->name);
        $this->assertSame('New description', $updatedcat->info);
        $this->assertSame('frog', $updatedcat->idnumber);
    }

    /**
     * Trying to update a category so its idnumber is a duplicate throws an exception and does not update.
     */
    public function test_update_category_try_to_set_duplicate_idnumber(): void {
        global $DB;

        $this->setAdminUser();
        $this->resetAfterTest();
        $context = $this->create_course_and_get_qbank_context();
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $topcat = question_get_top_category($context->id, true);
        $questiongenerator->create_question_category([
            'contextid' => $context->id,
            'name' => 'Toad category',
            'idnumber' => 'toad',
        ]);
        $category = $questiongenerator->create_question_category([
            'contextid' => $context->id,
            'name' => 'Frog category',
            'idnumber' => 'frog',
        ]);
        $existingcat = $DB->get_record('question_categories', ['id' => $category->id], '*', MUST_EXIST);
        $this->assertSame('Frog category', $existingcat->name);
        $this->assertSame('frog', $existingcat->idnumber);

        $manager = new category_manager();
        $this->expectExceptionMessage(get_string('idnumbertaken', 'error'));
        $manager->update_category(
            $category->id,
            $topcat->id . ',' . $topcat->contextid,
            'New name',
            '',
            FORMAT_HTML,
            'toad'
        );

        $updatedcat = $DB->get_record('question_categories', ['id' => $category->id], '*', MUST_EXIST);
        $this->assertEquals('Frog category', $updatedcat->name);
        $this->assertEquals('frog', $updatedcat->idnumber);
    }

    /**
     * Test the question category created event.
     */
    public function test_question_category_created(): void {
        $this->setAdminUser();
        $this->resetAfterTest();
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $quiz = $generator->get_plugin_generator('mod_quiz')->create_instance(['course' => $course->id]);
        $context = context_module::instance($quiz->cmid);
        $topcat = question_get_top_category($context->id, true);
        $manager = new category_manager(new moodle_url('/'));
        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $manager->add_category(
            $topcat->id . ',' . $topcat->contextid,
            'New category',
            'Description',
            FORMAT_HTML,
            'frog',
        );
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\core\event\question_category_created', $event);
        $this->assertEquals($context, $event->get_context());
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test the question category deleted event.
     */
    public function test_question_category_deleted(): void {
        $this->setAdminUser();
        $this->resetAfterTest();
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $quiz = $generator->get_plugin_generator('mod_quiz')->create_instance(['course' => $course->id]);
        $contexts = new question_edit_contexts(context_module::instance($quiz->cmid));
        $defaultcat = question_get_default_category($contexts->lowest()->id, true);
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        // Create the category.
        $category = $questiongenerator->create_question_category([
            'contextid' => $contexts->lowest()->id,
            'name' => 'New category',
            'info' => 'Description',
            'idnumber' => 'newcategory',
            'parent' => $defaultcat->id,
        ]);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $manager = new category_manager(new moodle_url('/'));
        $manager->delete_category($category->id);
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\core\event\question_category_deleted', $event);
        $this->assertEquals($contexts->lowest(), $event->get_context());
        $this->assertEquals($category->id, $event->objectid);
        $this->assertDebuggingNotCalled();
    }

    /**
     * Test the question category updated event.
     */
    public function test_question_category_updated(): void {
        $this->setAdminUser();
        $this->resetAfterTest();
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $quiz = $generator->get_plugin_generator('mod_quiz')->create_instance(['course' => $course->id]);
        $context = context_module::instance($quiz->cmid);
        $topcat = question_get_top_category($context->id, true);
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        // Create the category.
        $category = $questiongenerator->create_question_category([
            'contextid' => $context->id,
            'name' => 'New category',
            'info' => 'Description',
            'idnumber' => 'newcategory',
        ]);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $manager = new category_manager(new moodle_url('/'));
        $manager->update_category(
            $category->id,
            $topcat->id . ',' . $topcat->contextid,
            'Updated category',
            '',
            true,
            FORMAT_HTML,
        );
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\core\event\question_category_updated', $event);
        $this->assertEquals($context, $event->get_context());
        $this->assertEquals($category->id, $event->objectid);
        $this->assertDebuggingNotCalled();
    }

    /**
     * Test that get_real_question_ids_in_category() returns question id
     * of a shortanswer question in a category.
     */
    public function test_get_real_question_ids_in_category_shortanswer(): void {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $quiz = $generator->create_module('quiz', ['course' => $course->id]);
        $contexts = new question_edit_contexts(context_module::instance($quiz->cmid));

        $defaultcategory = question_get_default_category($contexts->lowest()->id, true);
        $questiongerator = $generator->get_plugin_generator('core_question');

        // Short answer question is made of one question.
        $shortanswer = $questiongerator->create_question('shortanswer', null, ['category' => $defaultcategory->id]);
        $manager = new category_manager();
        $questionids = $manager->get_real_question_ids_in_category($defaultcategory->id);
        $this->assertCount(1, $questionids);
        $this->assertContains($shortanswer->id, $questionids);
    }

    /**
     * Test that get_real_question_ids_in_category() returns question id
     * of a multianswer question in a category.
     */
    public function test_get_real_question_ids_in_category_multianswer(): void {
        global $DB;
        $this->resetAfterTest();
        $countq = $DB->count_records('question');
        $countqbe = $DB->count_records('question_bank_entries');
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $quiz = $generator->create_module('quiz', ['course' => $course->id]);
        $contexts = new question_edit_contexts(context_module::instance($quiz->cmid));

        $defaultcategory = question_get_default_category($contexts->lowest()->id, true);
        $questiongerator = $generator->get_plugin_generator('core_question');

        // Multi answer question is made of one parent and two child questions.
        $multianswer = $questiongerator->create_question('multianswer', null, ['category' => $defaultcategory->id]);
        $manager = new category_manager();
        $questionids = $manager->get_real_question_ids_in_category($defaultcategory->id);
        $this->assertCount(1, $questionids);
        $this->assertContains($multianswer->id, $questionids);
        $this->assertEquals(3, $DB->count_records('question') - $countq);
        $this->assertEquals(3, $DB->count_records('question_bank_entries') - $countqbe);
    }

    /**
     * Test that get_real_question_ids_in_category() returns question ids
     * of two versions of a multianswer question in a category.
     */
    public function test_get_real_question_ids_in_category_multianswer_two_versions(): void {
        global $DB;
        $this->resetAfterTest();
        $countq = $DB->count_records('question');
        $countqv = $DB->count_records('question_versions');
        $countqbe = $DB->count_records('question_bank_entries');

        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $quiz = $generator->create_module('quiz', ['course' => $course->id]);
        $contexts = new question_edit_contexts(context_module::instance($quiz->cmid));

        $defaultcategory = question_get_default_category($contexts->lowest()->id, true);
        $questiongerator = $generator->get_plugin_generator('core_question');

        // Create two versions of a multianswer question which will lead to
        // 2 parents and 4 child questions in the question bank.
        $multianswer = $questiongerator->create_question('multianswer', null, ['category' => $defaultcategory->id]);
        $multianswernew = $questiongerator->update_question($multianswer, null, ['name' => 'This is a new version']);
        $manager = new category_manager();
        $questionids = $manager->get_real_question_ids_in_category($defaultcategory->id);
        $this->assertCount(2, $questionids);
        $this->assertContains($multianswer->id, $questionids);
        $this->assertContains($multianswernew->id, $questionids);
        $this->assertEquals(6, $DB->count_records('question') - $countq);
        $this->assertEquals(6, $DB->count_records('question_versions') - $countqv);
        $this->assertEquals(3, $DB->count_records('question_bank_entries') - $countqbe);
    }

    /**
     * Test that get_real_question_ids_in_category() returns question id
     * of a multianswer question in a category even if their child questions are
     * linked to a category that doesn't exist.
     */
    public function test_get_real_question_ids_in_category_multianswer_bad_data(): void {
        global $DB;
        $this->resetAfterTest();
        $countqbe = $DB->count_records('question_bank_entries');

        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $quiz = $generator->create_module('quiz', ['course' => $course->id]);
        $contexts = new question_edit_contexts(context_module::instance($quiz->cmid));

        $defaultcategory = question_get_default_category($contexts->lowest()->id, true);
        $questiongerator = $generator->get_plugin_generator('core_question');

        // Multi answer question is made of one parent and two child questions.
        $multianswer = $questiongerator->create_question('multianswer', null, ['category' => $defaultcategory->id]);
        $qversion = $DB->get_record('question_versions', ['questionid' => $multianswer->id]);

        // Update category id for child questions to a category that doesn't exist.
        $DB->set_field_select(
            'question_bank_entries',
            'questioncategoryid',
            123456,
            'id <> :id',
            ['id' => $qversion->questionbankentryid]
        );

        $manager = new category_manager();
        $questionids = $manager->get_real_question_ids_in_category($defaultcategory->id);
        $this->assertCount(1, $questionids);
        $this->assertContains($multianswer->id, $questionids);
        $this->assertEquals(3, $DB->count_records('question_bank_entries') - $countqbe);
    }

    /**
     * Test delete top category in function question_can_delete_cat.
     */
    public function test_question_can_delete_cat_top_category(): void {
        $this->setAdminUser();
        $this->resetAfterTest();

        $manager = new category_manager();

        // Create a category.
        $course = $this->getDataGenerator()->create_course();
        $quiz = $this->getDataGenerator()->create_module('quiz', ['course' => $course->id]);
        $context = \context_module::instance($quiz->cmid);
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $qcategory1 = $questiongenerator->create_question_category(['contextid' => $context->id]);

        // Try to delete a top category.
        $categorytop = question_get_top_category($qcategory1->contextid, true)->id;
        $this->expectException('moodle_exception');
        $this->expectExceptionMessage(get_string('cannotdeletetopcat', 'question'));
        $manager->require_can_delete_category($categorytop);
    }

    /**
     * Test delete only child category in function question_can_delete_cat.
     */
    public function test_question_can_delete_cat_child_category(): void {
        $this->setAdminUser();
        $this->resetAfterTest();

        $manager = new category_manager();

        // Create a category.
        $course = $this->getDataGenerator()->create_course();
        $quiz = $this->getDataGenerator()->create_module('quiz', ['course' => $course->id]);
        $context = \context_module::instance($quiz->cmid);
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $qcategory1 = question_get_default_category($context->id);

        // Try to delete an only child of top category having also at least one child.
        $this->expectException('moodle_exception');
        $this->expectExceptionMessage(get_string('cannotdeletecate', 'question'));
        $manager->require_can_delete_category($qcategory1->id);
    }

    /**
     * Test delete category in function question_can_delete_cat without capabilities.
     */
    public function test_can_delete_category_capability(): void {
        $this->setAdminUser();
        $this->resetAfterTest();

        $manager = new category_manager();

        // Create 2 categories.
        $course = $this->getDataGenerator()->create_course();
        $quiz = $this->getDataGenerator()->create_module('quiz', ['course' => $course->id]);
        $context = \context_module::instance($quiz->cmid);
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $qcategory1 = $questiongenerator->create_question_category(['contextid' => $context->id]);
        $qcategory2 = $questiongenerator->create_question_category(['contextid' => $context->id, 'parent' => $qcategory1->id]);

        // This call should not throw an exception as admin user has the capabilities moodle/question:managecategory.
        $manager->require_can_delete_category($qcategory2->id);

        // Try to delete a category with and user without the capability.
        $manager = new category_manager();
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        $this->expectException(\required_capability_exception::class);
        $this->expectExceptionMessage(get_string('nopermissions', 'error', get_string('question:managecategory', 'role')));
        $manager->require_can_delete_category($qcategory2->id);
    }

    /**
     * Test get max sortorder
     */
    public function test_get_max_sortorder(): void {
        $this->setAdminUser();
        $this->resetAfterTest();

        $manager = new category_manager();

        // Create question categories for a course.
        $course = $this->getDataGenerator()->create_course();
        $quiz = $this->getDataGenerator()->create_module('quiz', ['course' => $course->id]);
        $context = \context_module::instance($quiz->cmid);
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $topcategory = question_get_top_category($context->id, true);
        $qcategory1 = question_get_default_category($context->id);
        $this->assertEquals(999, $manager->get_max_sortorder($topcategory->id));

        $qcategory2 = $questiongenerator->create_question_category(['contextid' => $context->id, 'parent' => $qcategory1->id]);

        $this->assertEquals(1, $manager->get_max_sortorder($qcategory1->id));

        $questiongenerator->create_question_category(['contextid' => $context->id]);
        $this->assertEquals(1000, $manager->get_max_sortorder($topcategory->id));

        $this->assertEquals(0, $manager->get_max_sortorder($qcategory2->id));
        $questiongenerator->create_question_category(['contextid' => $context->id, 'parent' => $qcategory2->id]);
        $this->assertEquals(1, $manager->get_max_sortorder($qcategory2->id));
    }
}
