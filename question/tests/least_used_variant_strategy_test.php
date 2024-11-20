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

use core_question\engine\variants\least_used_strategy;
use qubaid_list;
use question_bank;
use question_engine;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');

/**
 * Tests for the {@link core_question\engine\variants\least_used_strategy} class.
 *
 * @package    core_question
 * @copyright  2015 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class least_used_variant_strategy_test extends \advanced_testcase {

    public function test_question_with_one_variant_always_picks_that(): void {
        $question = \test_question_maker::make_question('shortanswer');
        $quba = question_engine::make_questions_usage_by_activity('test', \context_system::instance());
        $quba->set_preferred_behaviour('deferredfeedback');
        $slot = $quba->add_question($question);
        $quba->start_all_questions(new least_used_strategy(
                $quba, new qubaid_list([])));
        $this->assertEquals(1, $quba->get_variant($slot));
    }

    public function test_synchronised_question_should_use_the_same_dataset(): void {
        // Actually, we cheat here. We use the same question twice, not two different synchronised questions.
        $question = \test_question_maker::make_question('calculated');
        $quba = question_engine::make_questions_usage_by_activity('test', \context_system::instance());
        $quba->set_preferred_behaviour('deferredfeedback');
        $slot1 = $quba->add_question($question);
        $slot2 = $quba->add_question($question);
        $quba->start_all_questions(new least_used_strategy(
                $quba, new qubaid_list([])));
        $this->assertEquals($quba->get_variant($slot1), $quba->get_variant($slot2));
    }

    public function test_second_attempt_uses_other_dataset(): void {
        global $DB;
        $this->resetAfterTest();
        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');

        $cat = $generator->create_question_category();
        $questiondata = $generator->create_question('calculated', null, ['category' => $cat->id]);

        // Create two dataset items.
        $adefinitionid = $DB->get_field_sql("
                    SELECT qdd.id
                      FROM {question_dataset_definitions} qdd
                      JOIN {question_datasets} qd ON qd.datasetdefinition = qdd.id
                     WHERE qd.question = ?
                       AND qdd.name = ?", [$questiondata->id, 'a']);
        $bdefinitionid = $DB->get_field_sql("
                    SELECT qdd.id
                      FROM {question_dataset_definitions} qdd
                      JOIN {question_datasets} qd ON qd.datasetdefinition = qdd.id
                     WHERE qd.question = ?
                       AND qdd.name = ?", [$questiondata->id, 'b']);
        $DB->set_field('question_dataset_definitions', 'itemcount', 2, ['id' => $adefinitionid]);
        $DB->set_field('question_dataset_definitions', 'itemcount', 2, ['id' => $bdefinitionid]);
        $DB->insert_record('question_dataset_items', ['definition' => $adefinitionid,
                'itemnumber' => 1, 'value' => 3]);
        $DB->insert_record('question_dataset_items', ['definition' => $bdefinitionid,
                'itemnumber' => 1, 'value' => 7]);
        $DB->insert_record('question_dataset_items', ['definition' => $adefinitionid,
                'itemnumber' => 2, 'value' => 6]);
        $DB->insert_record('question_dataset_items', ['definition' => $bdefinitionid,
                'itemnumber' => 2, 'value' => 4]);

        $question = question_bank::load_question($questiondata->id);

        $quba1 = question_engine::make_questions_usage_by_activity('test', \context_system::instance());
        $quba1->set_preferred_behaviour('deferredfeedback');
        $slot1 = $quba1->add_question($question);
        $quba1->start_all_questions(new least_used_strategy(
                $quba1, new qubaid_list([])));
        question_engine::save_questions_usage_by_activity($quba1);
        $variant1 = $quba1->get_variant($slot1);

        // Second attempt should use the other variant.
        $quba2 = question_engine::make_questions_usage_by_activity('test', \context_system::instance());
        $quba2->set_preferred_behaviour('deferredfeedback');
        $slot2 = $quba2->add_question($question);
        $quba2->start_all_questions(new least_used_strategy(
                $quba1, new qubaid_list([$quba1->get_id()])));
        question_engine::save_questions_usage_by_activity($quba2);
        $variant2 = $quba2->get_variant($slot2);

        $this->assertNotEquals($variant1, $variant2);

        // Third attempt uses either variant at random.
        $quba3 = question_engine::make_questions_usage_by_activity('test', \context_system::instance());
        $quba3->set_preferred_behaviour('deferredfeedback');
        $slot3 = $quba3->add_question($question);
        $quba3->start_all_questions(new least_used_strategy(
                $quba1, new qubaid_list([$quba1->get_id(), $quba2->get_id()])));
        $variant3 = $quba3->get_variant($slot3);

        $this->assertTrue($variant3 == $variant1 || $variant3 == $variant2);
    }
}
