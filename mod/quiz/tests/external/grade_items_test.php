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

namespace mod_quiz\external;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../../../../webservice/tests/helpers.php');

use coding_exception;
use core_question_generator;
use externallib_advanced_testcase;
use mod_quiz\quiz_attempt;
use mod_quiz\quiz_settings;
use required_capability_exception;
use stdClass;

/**
 * Test for the grade_items CRUD service.
 *
 * @package   mod_quiz
 * @category  external
 * @copyright 2023 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \mod_quiz\external\create_grade_items
 * @covers \mod_quiz\external\delete_grade_items
 * @covers \mod_quiz\external\update_grade_items
 * @covers \mod_quiz\structure
 */
final class grade_items_test extends externallib_advanced_testcase {

    public function test_create_grade_items_service_works(): void {
        $quizobj = $this->create_quiz_with_two_grade_items();

        create_grade_items::execute($quizobj->get_quizid(), [
            ['name' => 'Speaking'],
            ['name' => 'Writing'],
        ]);

        $structure = $quizobj->get_structure();
        $items = array_values($structure->get_grade_items());

        $this->assertEquals('Listening', $items[0]->name);
        $this->assertEquals('Reading', $items[1]->name);
        $this->assertEquals('Speaking', $items[2]->name);
        $this->assertEquals('Writing', $items[3]->name);
    }

    public function test_create_grade_items_service_checks_permissions(): void {
        $quizobj = $this->create_quiz_with_two_grade_items();

        $unprivilegeduser = $this->getDataGenerator()->create_user();
        $this->setUser($unprivilegeduser);

        $this->expectException(required_capability_exception::class);
        create_grade_items::execute($quizobj->get_quizid(), []);
    }

    public function test_update_grade_items_service_works(): void {
        $quizobj = $this->create_quiz_with_two_grade_items();

        $structure = $quizobj->get_structure();
        $items = array_values($structure->get_grade_items());

        update_grade_items::execute($quizobj->get_quizid(), [
            ['id' => $items[0]->id, 'name' => 'Speaking'],
            ['id' => $items[1]->id, 'name' => null],
        ]);

        $structure = $quizobj->get_structure();
        $updateditems = $structure->get_grade_items();

        $this->assertEquals('Speaking', $updateditems[$items[0]->id]->name);
        $this->assertEquals($items[1]->name, $updateditems[$items[1]->id]->name);
    }

    public function test_update_grade_items_service_checks_permissions(): void {
        $quizobj = $this->create_quiz_with_two_grade_items();

        $unprivilegeduser = $this->getDataGenerator()->create_user();
        $this->setUser($unprivilegeduser);

        $this->expectException(required_capability_exception::class);
        update_grade_items::execute($quizobj->get_quizid(), []);
    }

    public function test_delete_grade_items_service_works(): void {
        $quizobj = $this->create_quiz_with_two_grade_items();

        $structure = $quizobj->get_structure();
        $items = array_values($structure->get_grade_items());
        $structure->update_slot_grade_item($structure->get_slot_by_number(1), null);
        $structure->update_slot_grade_item($structure->get_slot_by_number(2), null);

        delete_grade_items::execute($quizobj->get_quizid(), [['id' => $items[0]->id]]);

        $structure = $quizobj->get_structure();
        $updateditems = $structure->get_grade_items();

        $this->assertCount(1, $updateditems);
        $this->assertEquals('Reading', $updateditems[$items[1]->id]->name);
    }

    public function test_cant_delete_grade_item_that_is_used(): void {
        $quizobj = $this->create_quiz_with_two_grade_items();

        $structure = $quizobj->get_structure();
        $items = array_values($structure->get_grade_items());

        $this->expectException(coding_exception::class);
        delete_grade_items::execute($quizobj->get_quizid(), [['id' => $items[0]->id]]);
    }

    public function test_delete_grade_items_service_checks_permissions(): void {
        $quizobj = $this->create_quiz_with_two_grade_items();

        $unprivilegeduser = $this->getDataGenerator()->create_user();
        $this->setUser($unprivilegeduser);

        $structure = $quizobj->get_structure();
        $items = array_values($structure->get_grade_items());

        $this->expectException(required_capability_exception::class);
        delete_grade_items::execute($quizobj->get_quizid(), [['id' => $items[0]->id]]);
    }

    /**
     * Create a quiz of two shortanswer questions, each contributing to a different grade item.
     *
     * @return quiz_settings the newly created quiz.
     */
    protected function create_quiz_with_two_grade_items(): quiz_settings {
        global $SITE;
        $this->resetAfterTest();
        $this->setAdminUser();

        // Make a quiz.
        /** @var \mod_quiz_generator $quizgenerator */
        $quizgenerator = $this->getDataGenerator()->get_plugin_generator('mod_quiz');

        $quiz = $quizgenerator->create_instance(['course' => $SITE->id]);

        // Create two question.
        /** @var core_question_generator $questiongenerator */
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $questiongenerator->create_question_category();
        $saq1 = $questiongenerator->create_question('shortanswer', null, ['category' => $cat->id]);
        $saq2 = $questiongenerator->create_question('shortanswer', null, ['category' => $cat->id]);

        // Add them to the quiz.
        quiz_add_quiz_question($saq1->id, $quiz, 0, 1);
        quiz_add_quiz_question($saq2->id, $quiz, 0, 1);

        // Create two quiz grade items.
        $listeninggrade = $quizgenerator->create_grade_item(['quizid' => $quiz->id, 'name' => 'Listening']);
        $readinggrade = $quizgenerator->create_grade_item(['quizid' => $quiz->id, 'name' => 'Reading']);

        // Set the questions to use those grade items.
        $quizobj = quiz_settings::create($quiz->id);
        $structure = $quizobj->get_structure();
        $structure->update_slot_grade_item($structure->get_slot_by_number(1), $listeninggrade->id);
        $structure->update_slot_grade_item($structure->get_slot_by_number(2), $readinggrade->id);
        $quizobj->get_grade_calculator()->recompute_quiz_sumgrades();

        return $quizobj;
    }
}
