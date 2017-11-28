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
 * This file contains tests for the question_bank class.
 *
 * @package    moodlecore
 * @subpackage questionbank
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once(__DIR__ . '/../lib.php');


/**
 *Unit tests for the {@link question_bank} class.
 *
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_bank_test extends advanced_testcase {

    public function test_sort_qtype_array() {
        $config = new stdClass();
        $config->multichoice_sortorder = '1';
        $config->calculated_sortorder = '2';
        $qtypes = array(
            'frog' => 'toad',
            'calculated' => 'newt',
            'multichoice' => 'eft',
        );
        $this->assertEquals(question_bank::sort_qtype_array($qtypes, $config), array(
            'multichoice' => 'eft',
            'calculated' => 'newt',
            'frog' => 'toad',
        ));
    }

    public function test_fraction_options() {
        $fractions = question_bank::fraction_options();
        $this->assertSame(get_string('none'), reset($fractions));
        $this->assertSame('0.0', key($fractions));
        $this->assertSame('5%', end($fractions));
        $this->assertSame('0.05', key($fractions));
        array_shift($fractions);
        array_pop($fractions);
        array_pop($fractions);
        $this->assertSame('100%', reset($fractions));
        $this->assertSame('1.0', key($fractions));
        $this->assertSame('11.11111%', end($fractions));
        $this->assertSame('0.1111111', key($fractions));
    }

    public function test_fraction_options_full() {
        $fractions = question_bank::fraction_options_full();
        $this->assertSame(get_string('none'), reset($fractions));
        $this->assertSame('0.0', key($fractions));
        $this->assertSame('-100%', end($fractions));
        $this->assertSame('-1.0', key($fractions));
        array_shift($fractions);
        array_pop($fractions);
        array_pop($fractions);
        $this->assertSame('100%', reset($fractions));
        $this->assertSame('1.0', key($fractions));
        $this->assertSame('-83.33333%', end($fractions));
        $this->assertSame('-0.8333333', key($fractions));
    }

    public function test_get_questions_from_categories_with_usage_counts() {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');

        $cat = $generator->create_question_category();
        $questiondata1 = $generator->create_question('shortanswer', null, array('category' => $cat->id));
        $questiondata2 = $generator->create_question('shortanswer', null, array('category' => $cat->id));
        $questiondata3 = $generator->create_question('shortanswer', null, array('category' => $cat->id));

        $quba = question_engine::make_questions_usage_by_activity('test', context_system::instance());
        $quba->set_preferred_behaviour('deferredfeedback');
        $question1 = question_bank::load_question($questiondata1->id);
        $question3 = question_bank::load_question($questiondata3->id);
        $quba->add_question($question1);
        $quba->add_question($question1);
        $quba->add_question($question3);
        $quba->start_all_questions();
        question_engine::save_questions_usage_by_activity($quba);

        $this->assertEquals(array(
                $questiondata2->id => 0,
                $questiondata3->id => 1,
                $questiondata1->id => 2,
        ), question_bank::get_finder()->get_questions_from_categories_with_usage_counts(
                array($cat->id), new qubaid_list(array($quba->get_id()))));
    }
}
