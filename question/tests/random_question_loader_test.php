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
 * Tests for the {@link core_question\bank\random_question_loader} class.
 *
 * @package   core_question
 * @copyright 2015 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


/**
 * Tests for the {@link core_question\bank\random_question_loader} class.
 *
 * @copyright  2015 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class random_question_loader_testcase extends advanced_testcase {

    public function test_empty_category_gives_null() {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');

        $cat = $generator->create_question_category();
        $loader = new \core_question\bank\random_question_loader(new qubaid_list(array()));

        $this->assertNull($loader->get_next_question_id($cat->id, 0));
        $this->assertNull($loader->get_next_question_id($cat->id, 1));
    }

    public function test_unknown_category_behaves_like_empty() {
        // It is up the caller to make sure the category id is valid.
        $loader = new \core_question\bank\random_question_loader(new qubaid_list(array()));
        $this->assertNull($loader->get_next_question_id(-1, 1));
    }

    public function test_descriptions_not_returned() {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');

        $cat = $generator->create_question_category();
        $info = $generator->create_question('description', null, array('category' => $cat->id));
        $loader = new \core_question\bank\random_question_loader(new qubaid_list(array()));

        $this->assertNull($loader->get_next_question_id($cat->id, 0));
    }

    public function test_one_question_category_returns_that_q_then_null() {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');

        $cat = $generator->create_question_category();
        $question1 = $generator->create_question('shortanswer', null, array('category' => $cat->id));
        $loader = new \core_question\bank\random_question_loader(new qubaid_list(array()));

        $this->assertEquals($question1->id, $loader->get_next_question_id($cat->id, 1));
        $this->assertNull($loader->get_next_question_id($cat->id, 0));
    }

    public function test_two_question_category_returns_both_then_null() {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');

        $cat = $generator->create_question_category();
        $question1 = $generator->create_question('shortanswer', null, array('category' => $cat->id));
        $question2 = $generator->create_question('shortanswer', null, array('category' => $cat->id));
        $loader = new \core_question\bank\random_question_loader(new qubaid_list(array()));

        $questionids = array();
        $questionids[] = $loader->get_next_question_id($cat->id, 0);
        $questionids[] = $loader->get_next_question_id($cat->id, 0);
        sort($questionids);
        $this->assertEquals(array($question1->id, $question2->id), $questionids);

        $this->assertNull($loader->get_next_question_id($cat->id, 1));
    }

    public function test_nested_categories() {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');

        $cat1 = $generator->create_question_category();
        $cat2 = $generator->create_question_category(array('parent' => $cat1->id));
        $question1 = $generator->create_question('shortanswer', null, array('category' => $cat1->id));
        $question2 = $generator->create_question('shortanswer', null, array('category' => $cat2->id));
        $loader = new \core_question\bank\random_question_loader(new qubaid_list(array()));

        $this->assertEquals($question2->id, $loader->get_next_question_id($cat2->id, 1));
        $this->assertEquals($question1->id, $loader->get_next_question_id($cat1->id, 1));

        $this->assertNull($loader->get_next_question_id($cat1->id, 0));
    }

    public function test_used_question_not_returned_until_later() {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');

        $cat = $generator->create_question_category();
        $question1 = $generator->create_question('shortanswer', null, array('category' => $cat->id));
        $question2 = $generator->create_question('shortanswer', null, array('category' => $cat->id));
        $loader = new \core_question\bank\random_question_loader(new qubaid_list(array()),
                array($question2->id => 2));

        $this->assertEquals($question1->id, $loader->get_next_question_id($cat->id, 0));
        $this->assertNull($loader->get_next_question_id($cat->id, 0));
    }

    public function test_previously_used_question_not_returned_until_later() {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');

        $cat = $generator->create_question_category();
        $question1 = $generator->create_question('shortanswer', null, array('category' => $cat->id));
        $question2 = $generator->create_question('shortanswer', null, array('category' => $cat->id));
        $quba = question_engine::make_questions_usage_by_activity('test', context_system::instance());
        $quba->set_preferred_behaviour('deferredfeedback');
        $question = question_bank::load_question($question2->id);
        $quba->add_question($question);
        $quba->add_question($question);
        $quba->start_all_questions();
        question_engine::save_questions_usage_by_activity($quba);

        $loader = new \core_question\bank\random_question_loader(new qubaid_list(array($quba->get_id())));

        $this->assertEquals($question1->id, $loader->get_next_question_id($cat->id, 0));
        $this->assertEquals($question2->id, $loader->get_next_question_id($cat->id, 0));
        $this->assertNull($loader->get_next_question_id($cat->id, 0));
    }

    public function test_empty_category_does_not_have_question_available() {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');

        $cat = $generator->create_question_category();
        $loader = new \core_question\bank\random_question_loader(new qubaid_list(array()));

        $this->assertFalse($loader->is_question_available($cat->id, 0, 1));
        $this->assertFalse($loader->is_question_available($cat->id, 1, 1));
    }

    public function test_descriptions_not_available() {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');

        $cat = $generator->create_question_category();
        $info = $generator->create_question('description', null, array('category' => $cat->id));
        $loader = new \core_question\bank\random_question_loader(new qubaid_list(array()));

        $this->assertFalse($loader->is_question_available($cat->id, 0, $info->id));
        $this->assertFalse($loader->is_question_available($cat->id, 1, $info->id));
    }

    public function test_existing_question_is_available_but_then_marked_used() {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');

        $cat = $generator->create_question_category();
        $question1 = $generator->create_question('shortanswer', null, array('category' => $cat->id));
        $loader = new \core_question\bank\random_question_loader(new qubaid_list(array()));

        $this->assertTrue($loader->is_question_available($cat->id, 0, $question1->id));
        $this->assertFalse($loader->is_question_available($cat->id, 0, $question1->id));

        $this->assertFalse($loader->is_question_available($cat->id, 0, -1));
    }
}
