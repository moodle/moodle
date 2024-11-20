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

use core_question\local\bank\question_version_status;
use qubaid_list;
use question_bank;
use question_engine;
use question_finder;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once(__DIR__ . '/../lib.php');

/**
 * Unit tests for the {@see question_bank} class.
 *
 * @package    core_question
 * @category   test
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class questionbank_test extends \advanced_testcase {

    public function test_sort_qtype_array(): void {
        $config = new \stdClass();
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

    public function test_fraction_options(): void {
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

    public function test_fraction_options_full(): void {
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

    public function test_load_many_for_cache(): void {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $generator->create_question_category();
        $q1 = $generator->create_question('shortanswer', null, ['category' => $cat->id]);

        $qs = question_finder::get_instance()->load_many_for_cache([$q1->id]);
        $this->assertArrayHasKey($q1->id, $qs);
    }

    public function test_load_many_for_cache_missing_id(): void {
        // Try to load a non-existent question.
        $this->expectException(\dml_missing_record_exception::class);
        question_finder::get_instance()->load_many_for_cache([-1]);
    }

    /**
     * Test get_questions_from_categories.
     *
     * @covers \question_finder::get_questions_from_categories
     *
     * @return void
     */
    public function test_get_questions_from_categories(): void {
        $this->resetAfterTest();

        /** @var core_question_generator $questiongenerator */
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');

        // Create three questions in a question bank category, each with three versions.
        // The first question has all three versions in status ready.
        $cat = $questiongenerator->create_question_category();
        $q1v1 = $questiongenerator->create_question('truefalse', null, ['name' => 'Q1V1', 'category' => $cat->id]);
        $q1v2 = $questiongenerator->update_question($q1v1, null, ['name' => 'Q1V2']);
        $q1v3 = $questiongenerator->update_question($q1v2, null, ['name' => 'Q1V3']);
        // The second question has the first version in status draft, the second version in status ready,
        // and third version in status draft.
        $q2v1 = $questiongenerator->create_question('numerical', null, ['name' => 'Q2V2', 'category' => $cat->id,
            'status' => question_version_status::QUESTION_STATUS_DRAFT, ]);
        $q2v2 = $questiongenerator->update_question($q2v1, null, ['name' => 'Q2V2',
            'status' => question_version_status::QUESTION_STATUS_READY, ]);
        $q2v3 = $questiongenerator->update_question($q2v2, null,
            ['name' => 'Q2V3', 'status' => question_version_status::QUESTION_STATUS_DRAFT]);
        // The third question has all three version in status draft.
        $q3v1 = $questiongenerator->create_question('shortanswer', null, ['name' => 'Q3V1', 'category' => $cat->id,
            'status' => question_version_status::QUESTION_STATUS_DRAFT, ]);
        $q3v2 = $questiongenerator->update_question($q3v1, null, ['name' => 'Q3V2',
            'status' => question_version_status::QUESTION_STATUS_DRAFT, ]);
        $q3v3 = $questiongenerator->update_question($q3v2, null, ['name' => 'Q3V3',
            'status' => question_version_status::QUESTION_STATUS_DRAFT]);

        // Test the returned array of questions in that category is the desired one with version three of the first
        // question, version two of the second question, and the third question omitted completely since there are
        // only draft versions.
        $this->assertEquals([$q1v3->id => $q1v3->id, $q2v2->id => $q2v2->id],
            question_bank::get_finder()->get_questions_from_categories([$cat->id], ""));
    }
}
