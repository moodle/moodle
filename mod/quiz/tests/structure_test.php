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

namespace mod_quiz;

use core\exception\coding_exception;
use question_bank;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/quiz/tests/quiz_question_helper_test_trait.php');

/**
 * Unit tests for quiz events.
 *
 * @package   mod_quiz
 * @category  test
 * @copyright 2013 Adrian Greeve
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \mod_quiz\structure
 */
class structure_test extends \advanced_testcase {

    use \quiz_question_helper_test_trait;

    /**
     * Create a course with an empty quiz.
     * @return array with three elements quiz, cm and course.
     */
    protected function prepare_quiz_data() {

        $this->resetAfterTest(true);

        // Create a course.
        $course = $this->getDataGenerator()->create_course();

        // Make a quiz.
        $quizgenerator = $this->getDataGenerator()->get_plugin_generator('mod_quiz');

        $quiz = $quizgenerator->create_instance(['course' => $course->id, 'questionsperpage' => 0,
            'grade' => 100.0, 'sumgrades' => 2, 'preferredbehaviour' => 'immediatefeedback']);

        $cm = get_coursemodule_from_instance('quiz', $quiz->id, $course->id);

        return [$quiz, $cm, $course];
    }

    /**
     * Creat a test quiz.
     *
     * $layout looks like this:
     * $layout = array(
     *     'Heading 1'
     *     array('TF1', 1, 'truefalse'),
     *     'Heading 2*'
     *     array('TF2', 2, 'truefalse'),
     * );
     * That is, either a string, which represents a section heading,
     * or an array that represents a question.
     *
     * If the section heading ends with *, that section is shuffled.
     *
     * The elements in the question array are name, page number, and question type.
     *
     * @param array $layout as above.
     * @return quiz_settings the created quiz.
     */
    protected function create_test_quiz($layout) {
        list($quiz, $cm, $course) = $this->prepare_quiz_data();
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $questiongenerator->create_question_category();

        $headings = [];
        $slot = 1;
        $lastpage = 0;
        foreach ($layout as $item) {
            if (is_string($item)) {
                if (isset($headings[$lastpage + 1])) {
                    throw new \coding_exception('Sections cannot be empty.');
                }
                $headings[$lastpage + 1] = $item;

            } else {
                list($name, $page, $qtype) = $item;
                if ($page < 1 || !($page == $lastpage + 1 ||
                        (!isset($headings[$lastpage + 1]) && $page == $lastpage))) {
                    throw new \coding_exception('Page numbers wrong.');
                }
                $q = $questiongenerator->create_question($qtype, null,
                        ['name' => $name, 'category' => $cat->id]);

                quiz_add_quiz_question($q->id, $quiz, $page);
                $lastpage = $page;
            }
        }

        $quizobj = new quiz_settings($quiz, $cm, $course);
        $structure = structure::create_for_quiz($quizobj);
        if (isset($headings[1])) {
            list($heading, $shuffle) = $this->parse_section_name($headings[1]);
            $sections = $structure->get_sections();
            $firstsection = reset($sections);
            $structure->set_section_heading($firstsection->id, $heading);
            $structure->set_section_shuffle($firstsection->id, $shuffle);
            unset($headings[1]);
        }

        foreach ($headings as $startpage => $heading) {
            list($heading, $shuffle) = $this->parse_section_name($heading);
            $id = $structure->add_section_heading($startpage, $heading);
            $structure->set_section_shuffle($id, $shuffle);
        }

        return $quizobj;
    }

    /**
     * Verify that the given layout matches that expected.
     * @param array $expectedlayout as for $layout in {@link create_test_quiz()}.
     * @param structure $structure the structure to test.
     */
    protected function assert_quiz_layout($expectedlayout, structure $structure) {
        $sections = $structure->get_sections();

        $slot = 1;
        foreach ($expectedlayout as $item) {
            if (is_string($item)) {
                list($heading, $shuffle) = $this->parse_section_name($item);
                $section = array_shift($sections);

                if ($slot > 1 && $section->heading == '' && $section->firstslot == 1) {
                    // The array $expectedlayout did not contain default first quiz section, so skip over it.
                    $section = array_shift($sections);
                }

                $this->assertEquals($slot, $section->firstslot);
                $this->assertEquals($heading, $section->heading);
                $this->assertEquals($shuffle, $section->shufflequestions);

            } else {
                list($name, $page, $qtype) = $item;
                $question = $structure->get_question_in_slot($slot);
                $this->assertEquals($name,  $question->name);
                $this->assertEquals($slot,  $question->slot,  'Slot number wrong for question ' . $name);
                $this->assertEquals($qtype, $question->qtype, 'Question type wrong for question ' . $name);
                $this->assertEquals($page,  $question->page,  'Page number wrong for question ' . $name);

                $slot += 1;
            }
        }

        if ($slot - 1 != count($structure->get_slots())) {
            $this->fail('The quiz contains more slots than expected.');
        }

        if (!empty($sections)) {
            $section = array_shift($sections);
            if ($section->heading != '' || $section->firstslot != 1) {
                $this->fail('Unexpected section (' . $section->heading .') found in the quiz.');
            }
        }
    }

    /**
     * Parse the section name, optionally followed by a * to mean shuffle, as
     * used by create_test_quiz as assert_quiz_layout.
     * @param string $heading the heading.
     * @return array with two elements, the heading and the shuffle setting.
     */
    protected function parse_section_name($heading) {
        if (substr($heading, -1) == '*') {
            return [substr($heading, 0, -1), 1];
        } else {
            return [$heading, 0];
        }
    }

    public function test_get_quiz_slots(): void {
        $quizobj = $this->create_test_quiz([
                ['TF1', 1, 'truefalse'],
                ['TF2', 1, 'truefalse'],
        ]);
        $structure = structure::create_for_quiz($quizobj);

        // Are the correct slots returned?
        $slots = $structure->get_slots();
        $this->assertCount(2, $structure->get_slots());
    }

    public function test_quiz_has_one_section_by_default(): void {
        $quizobj = $this->create_test_quiz([
                ['TF1', 1, 'truefalse'],
        ]);
        $structure = structure::create_for_quiz($quizobj);

        $sections = $structure->get_sections();
        $this->assertCount(1, $sections);

        $section = array_shift($sections);
        $this->assertEquals(1, $section->firstslot);
        $this->assertEquals('', $section->heading);
        $this->assertEquals(0, $section->shufflequestions);
    }

    public function test_get_sections(): void {
        $quizobj = $this->create_test_quiz([
                'Heading 1*',
                ['TF1', 1, 'truefalse'],
                'Heading 2*',
                ['TF2', 2, 'truefalse'],
        ]);
        $structure = structure::create_for_quiz($quizobj);

        $sections = $structure->get_sections();
        $this->assertCount(2, $sections);

        $section = array_shift($sections);
        $this->assertEquals(1, $section->firstslot);
        $this->assertEquals('Heading 1', $section->heading);
        $this->assertEquals(1, $section->shufflequestions);

        $section = array_shift($sections);
        $this->assertEquals(2, $section->firstslot);
        $this->assertEquals('Heading 2', $section->heading);
        $this->assertEquals(1, $section->shufflequestions);
    }

    public function test_remove_section_heading(): void {
        $quizobj = $this->create_test_quiz([
                'Heading 1',
                ['TF1', 1, 'truefalse'],
                'Heading 2',
                ['TF2', 2, 'truefalse'],
        ]);
        $structure = structure::create_for_quiz($quizobj);

        $sections = $structure->get_sections();
        $section = end($sections);
        $structure->remove_section_heading($section->id);

        $structure = structure::create_for_quiz($quizobj);
        $this->assert_quiz_layout([
                'Heading 1',
                ['TF1', 1, 'truefalse'],
                ['TF2', 2, 'truefalse'],
        ], $structure);
    }

    public function test_cannot_remove_first_section(): void {
        $quizobj = $this->create_test_quiz([
                'Heading 1',
                ['TF1', 1, 'truefalse'],
        ]);
        $structure = structure::create_for_quiz($quizobj);

        $sections = $structure->get_sections();
        $section = reset($sections);

        $this->expectException(\coding_exception::class);
        $structure->remove_section_heading($section->id);
    }

    public function test_move_slot_to_the_same_place_does_nothing(): void {
        $quizobj = $this->create_test_quiz([
                ['TF1', 1, 'truefalse'],
                ['TF2', 1, 'truefalse'],
                ['TF3', 2, 'truefalse'],
        ]);
        $structure = structure::create_for_quiz($quizobj);

        $idtomove = $structure->get_question_in_slot(2)->slotid;
        $idmoveafter = $structure->get_question_in_slot(1)->slotid;
        $structure->move_slot($idtomove, $idmoveafter, '1');

        $structure = structure::create_for_quiz($quizobj);
        $this->assert_quiz_layout([
                ['TF1', 1, 'truefalse'],
                ['TF2', 1, 'truefalse'],
                ['TF3', 2, 'truefalse'],
        ], $structure);
    }

    public function test_move_slot_end_of_one_page_to_start_of_next(): void {
        $quizobj = $this->create_test_quiz([
                ['TF1', 1, 'truefalse'],
                ['TF2', 1, 'truefalse'],
                ['TF3', 2, 'truefalse'],
        ]);
        $structure = structure::create_for_quiz($quizobj);

        $idtomove = $structure->get_question_in_slot(2)->slotid;
        $idmoveafter = $structure->get_question_in_slot(2)->slotid;
        $structure->move_slot($idtomove, $idmoveafter, '2');

        $structure = structure::create_for_quiz($quizobj);
        $this->assert_quiz_layout([
                ['TF1', 1, 'truefalse'],
                ['TF2', 2, 'truefalse'],
                ['TF3', 2, 'truefalse'],
        ], $structure);
    }

    public function test_move_last_slot_to_previous_page_emptying_the_last_page(): void {
        $quizobj = $this->create_test_quiz([
                ['TF1', 1, 'truefalse'],
                ['TF2', 2, 'truefalse'],
        ]);
        $structure = structure::create_for_quiz($quizobj);

        $idtomove = $structure->get_question_in_slot(2)->slotid;
        $idmoveafter = $structure->get_question_in_slot(1)->slotid;
        $structure->move_slot($idtomove, $idmoveafter, '1');

        $structure = structure::create_for_quiz($quizobj);
        $this->assert_quiz_layout([
                ['TF1', 1, 'truefalse'],
                ['TF2', 1, 'truefalse'],
        ], $structure);
    }

    public function test_end_of_one_section_to_start_of_next(): void {
        $quizobj = $this->create_test_quiz([
                ['TF1', 1, 'truefalse'],
                ['TF2', 1, 'truefalse'],
                'Heading',
                ['TF3', 2, 'truefalse'],
        ]);
        $structure = structure::create_for_quiz($quizobj);

        $idtomove = $structure->get_question_in_slot(2)->slotid;
        $idmoveafter = $structure->get_question_in_slot(2)->slotid;
        $structure->move_slot($idtomove, $idmoveafter, '2');

        $structure = structure::create_for_quiz($quizobj);
        $this->assert_quiz_layout([
                ['TF1', 1, 'truefalse'],
                'Heading',
                ['TF2', 2, 'truefalse'],
                ['TF3', 2, 'truefalse'],
        ], $structure);
    }

    public function test_start_of_one_section_to_end_of_previous(): void {
        $quizobj = $this->create_test_quiz([
                ['TF1', 1, 'truefalse'],
                'Heading',
                ['TF2', 2, 'truefalse'],
                ['TF3', 2, 'truefalse'],
        ]);
        $structure = structure::create_for_quiz($quizobj);

        $idtomove = $structure->get_question_in_slot(2)->slotid;
        $idmoveafter = $structure->get_question_in_slot(1)->slotid;
        $structure->move_slot($idtomove, $idmoveafter, '1');

        $structure = structure::create_for_quiz($quizobj);
        $this->assert_quiz_layout([
                ['TF1', 1, 'truefalse'],
                ['TF2', 1, 'truefalse'],
                'Heading',
                ['TF3', 2, 'truefalse'],
        ], $structure);
    }
    public function test_move_slot_on_same_page(): void {
        $quizobj = $this->create_test_quiz([
                ['TF1', 1, 'truefalse'],
                ['TF2', 1, 'truefalse'],
                ['TF3', 1, 'truefalse'],
        ]);
        $structure = structure::create_for_quiz($quizobj);

        $idtomove = $structure->get_question_in_slot(2)->slotid;
        $idmoveafter = $structure->get_question_in_slot(3)->slotid;
        $structure->move_slot($idtomove, $idmoveafter, '1');

        $structure = structure::create_for_quiz($quizobj);
        $this->assert_quiz_layout([
                ['TF1', 1, 'truefalse'],
                ['TF3', 1, 'truefalse'],
                ['TF2', 1, 'truefalse'],
        ], $structure);
    }

    public function test_move_slot_up_onto_previous_page(): void {
        $quizobj = $this->create_test_quiz([
                ['TF1', 1, 'truefalse'],
                ['TF2', 2, 'truefalse'],
                ['TF3', 2, 'truefalse'],
        ]);
        $structure = structure::create_for_quiz($quizobj);

        $idtomove = $structure->get_question_in_slot(3)->slotid;
        $idmoveafter = $structure->get_question_in_slot(1)->slotid;
        $structure->move_slot($idtomove, $idmoveafter, '1');

        $structure = structure::create_for_quiz($quizobj);
        $this->assert_quiz_layout([
                ['TF1', 1, 'truefalse'],
                ['TF3', 1, 'truefalse'],
                ['TF2', 2, 'truefalse'],
        ], $structure);
    }

    public function test_move_slot_emptying_a_page_renumbers_pages(): void {
        $quizobj = $this->create_test_quiz([
                ['TF1', 1, 'truefalse'],
                ['TF2', 2, 'truefalse'],
                ['TF3', 3, 'truefalse'],
        ]);
        $structure = structure::create_for_quiz($quizobj);

        $idtomove = $structure->get_question_in_slot(2)->slotid;
        $idmoveafter = $structure->get_question_in_slot(3)->slotid;
        $structure->move_slot($idtomove, $idmoveafter, '3');

        $structure = structure::create_for_quiz($quizobj);
        $this->assert_quiz_layout([
                ['TF1', 1, 'truefalse'],
                ['TF3', 2, 'truefalse'],
                ['TF2', 2, 'truefalse'],
        ], $structure);
    }

    public function test_move_slot_too_small_page_number_detected(): void {
        $quizobj = $this->create_test_quiz([
                ['TF1', 1, 'truefalse'],
                ['TF2', 2, 'truefalse'],
                ['TF3', 3, 'truefalse'],
        ]);
        $structure = structure::create_for_quiz($quizobj);

        $idtomove = $structure->get_question_in_slot(3)->slotid;
        $idmoveafter = $structure->get_question_in_slot(2)->slotid;
        $this->expectException(\coding_exception::class);
        $structure->move_slot($idtomove, $idmoveafter, '1');
    }

    public function test_move_slot_too_large_page_number_detected(): void {
        $quizobj = $this->create_test_quiz([
                ['TF1', 1, 'truefalse'],
                ['TF2', 2, 'truefalse'],
                ['TF3', 3, 'truefalse'],
        ]);
        $structure = structure::create_for_quiz($quizobj);

        $idtomove = $structure->get_question_in_slot(1)->slotid;
        $idmoveafter = $structure->get_question_in_slot(2)->slotid;
        $this->expectException(\coding_exception::class);
        $structure->move_slot($idtomove, $idmoveafter, '4');
    }

    public function test_move_slot_within_section(): void {
        $quizobj = $this->create_test_quiz([
                'Heading 1',
                ['TF1', 1, 'truefalse'],
                ['TF2', 1, 'truefalse'],
                'Heading 2',
                ['TF3', 2, 'truefalse'],
        ]);
        $structure = structure::create_for_quiz($quizobj);

        $idtomove = $structure->get_question_in_slot(1)->slotid;
        $idmoveafter = $structure->get_question_in_slot(2)->slotid;
        $structure->move_slot($idtomove, $idmoveafter, '1');

        $structure = structure::create_for_quiz($quizobj);
        $this->assert_quiz_layout([
                'Heading 1',
                ['TF2', 1, 'truefalse'],
                ['TF1', 1, 'truefalse'],
                'Heading 2',
                ['TF3', 2, 'truefalse'],
        ], $structure);
    }

    public function test_move_slot_to_new_section(): void {
        $quizobj = $this->create_test_quiz([
                'Heading 1',
                ['TF1', 1, 'truefalse'],
                ['TF2', 1, 'truefalse'],
                'Heading 2',
                ['TF3', 2, 'truefalse'],
        ]);
        $structure = structure::create_for_quiz($quizobj);

        $idtomove = $structure->get_question_in_slot(2)->slotid;
        $idmoveafter = $structure->get_question_in_slot(3)->slotid;
        $structure->move_slot($idtomove, $idmoveafter, '2');

        $structure = structure::create_for_quiz($quizobj);
        $this->assert_quiz_layout([
                'Heading 1',
                ['TF1', 1, 'truefalse'],
                'Heading 2',
                ['TF3', 2, 'truefalse'],
                ['TF2', 2, 'truefalse'],
        ], $structure);
    }

    public function test_move_slot_to_start(): void {
        $quizobj = $this->create_test_quiz([
                'Heading 1',
                ['TF1', 1, 'truefalse'],
                'Heading 2',
                ['TF2', 2, 'truefalse'],
                ['TF3', 2, 'truefalse'],
        ]);
        $structure = structure::create_for_quiz($quizobj);

        $idtomove = $structure->get_question_in_slot(3)->slotid;
        $structure->move_slot($idtomove, 0, '1');

        $structure = structure::create_for_quiz($quizobj);
        $this->assert_quiz_layout([
                'Heading 1',
                ['TF3', 1, 'truefalse'],
                ['TF1', 1, 'truefalse'],
                'Heading 2',
                ['TF2', 2, 'truefalse'],
        ], $structure);
    }

    public function test_move_slot_down_to_start_of_second_section(): void {
        $quizobj = $this->create_test_quiz([
                'Heading 1',
                ['TF1', 1, 'truefalse'],
                ['TF2', 1, 'truefalse'],
                'Heading 2',
                ['TF3', 2, 'truefalse'],
        ]);
        $structure = structure::create_for_quiz($quizobj);

        $idtomove = $structure->get_question_in_slot(2)->slotid;
        $idmoveafter = $structure->get_question_in_slot(2)->slotid;
        $structure->move_slot($idtomove, $idmoveafter, '2');

        $structure = structure::create_for_quiz($quizobj);
        $this->assert_quiz_layout([
                'Heading 1',
                ['TF1', 1, 'truefalse'],
                'Heading 2',
                ['TF2', 2, 'truefalse'],
                ['TF3', 2, 'truefalse'],
        ], $structure);
    }

    public function test_move_first_slot_down_to_start_of_page_2(): void {
        $quizobj = $this->create_test_quiz([
                'Heading 1',
                ['TF1', 1, 'truefalse'],
                ['TF2', 2, 'truefalse'],
        ]);
        $structure = structure::create_for_quiz($quizobj);

        $idtomove = $structure->get_question_in_slot(1)->slotid;
        $structure->move_slot($idtomove, 0, '2');

        $structure = structure::create_for_quiz($quizobj);
        $this->assert_quiz_layout([
                'Heading 1',
                ['TF1', 1, 'truefalse'],
                ['TF2', 1, 'truefalse'],
        ], $structure);
    }

    public function test_move_first_slot_to_same_place_on_page_1(): void {
        $quizobj = $this->create_test_quiz([
                'Heading 1',
                ['TF1', 1, 'truefalse'],
                ['TF2', 2, 'truefalse'],
        ]);
        $structure = structure::create_for_quiz($quizobj);

        $idtomove = $structure->get_question_in_slot(1)->slotid;
        $structure->move_slot($idtomove, 0, '1');

        $structure = structure::create_for_quiz($quizobj);
        $this->assert_quiz_layout([
                'Heading 1',
                ['TF1', 1, 'truefalse'],
                ['TF2', 2, 'truefalse'],
        ], $structure);
    }

    public function test_move_first_slot_to_before_page_1(): void {
        $quizobj = $this->create_test_quiz([
                'Heading 1',
                ['TF1', 1, 'truefalse'],
                ['TF2', 2, 'truefalse'],
        ]);
        $structure = structure::create_for_quiz($quizobj);

        $idtomove = $structure->get_question_in_slot(1)->slotid;
        $structure->move_slot($idtomove, 0, '');

        $structure = structure::create_for_quiz($quizobj);
        $this->assert_quiz_layout([
                'Heading 1',
                ['TF1', 1, 'truefalse'],
                ['TF2', 2, 'truefalse'],
        ], $structure);
    }

    public function test_move_slot_up_to_start_of_second_section(): void {
        $quizobj = $this->create_test_quiz([
                'Heading 1',
                ['TF1', 1, 'truefalse'],
                'Heading 2',
                ['TF2', 2, 'truefalse'],
                'Heading 3',
                ['TF3', 3, 'truefalse'],
                ['TF4', 3, 'truefalse'],
        ]);
        $structure = structure::create_for_quiz($quizobj);

        $idtomove = $structure->get_question_in_slot(3)->slotid;
        $idmoveafter = $structure->get_question_in_slot(1)->slotid;
        $structure->move_slot($idtomove, $idmoveafter, '2');

        $structure = structure::create_for_quiz($quizobj);
        $this->assert_quiz_layout([
                'Heading 1',
                ['TF1', 1, 'truefalse'],
                'Heading 2',
                ['TF3', 2, 'truefalse'],
                ['TF2', 2, 'truefalse'],
                'Heading 3',
                ['TF4', 3, 'truefalse'],
        ], $structure);
    }

    public function test_move_slot_does_not_violate_heading_unique_key(): void {
        $quizobj = $this->create_test_quiz([
                'Heading 1',
                ['TF1', 1, 'truefalse'],
                'Heading 2',
                ['TF2', 2, 'truefalse'],
                'Heading 3',
                ['TF3', 3, 'truefalse'],
                ['TF4', 3, 'truefalse'],
        ]);
        $structure = structure::create_for_quiz($quizobj);

        $idtomove = $structure->get_question_in_slot(4)->slotid;
        $idmoveafter = $structure->get_question_in_slot(1)->slotid;
        $structure->move_slot($idtomove, $idmoveafter, 1);

        $structure = structure::create_for_quiz($quizobj);
        $this->assert_quiz_layout([
                'Heading 1',
                ['TF1', 1, 'truefalse'],
                ['TF4', 1, 'truefalse'],
                'Heading 2',
                ['TF2', 2, 'truefalse'],
                'Heading 3',
                ['TF3', 3, 'truefalse'],
        ], $structure);
    }

    public function test_quiz_remove_slot(): void {
        $quizobj = $this->create_test_quiz([
                ['TF1', 1, 'truefalse'],
                ['TF2', 1, 'truefalse'],
                'Heading 2',
                ['TF3', 2, 'truefalse'],
        ]);
        $structure = structure::create_for_quiz($quizobj);

        $structure->remove_slot(2);

        $structure = structure::create_for_quiz($quizobj);
        $this->assert_quiz_layout([
                ['TF1', 1, 'truefalse'],
                'Heading 2',
                ['TF3', 2, 'truefalse'],
        ], $structure);
    }

    public function test_quiz_removing_a_random_question_deletes_the_question(): void {
        global $DB;

        $this->resetAfterTest(true);
        $this->setAdminUser();

        $quizobj = $this->create_test_quiz([
                ['TF1', 1, 'truefalse'],
        ]);

        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $questiongenerator->create_question_category();
        $this->add_random_questions($quizobj->get_quizid(), 1, $cat->id, 1);
        $structure = structure::create_for_quiz($quizobj);
        $sql = 'SELECT qsr.*
                 FROM {question_set_references} qsr
                 JOIN {quiz_slots} qs ON qs.id = qsr.itemid
                 WHERE qs.quizid = ?
                   AND qsr.component = ?
                   AND qsr.questionarea = ?';
        $randomq = $DB->get_record_sql($sql, [$quizobj->get_quizid(), 'mod_quiz', 'slot']);

        $structure->remove_slot(2);

        $structure = structure::create_for_quiz($quizobj);
        $this->assert_quiz_layout([
                ['TF1', 1, 'truefalse'],
        ], $structure);
        $this->assertFalse($DB->record_exists('question_set_references',
            ['id' => $randomq->id, 'component' => 'mod_quiz', 'questionarea' => 'slot']));
    }

    /**
     * Unit test to make sue it is not possible to remove all slots in a section at once.
     */
    public function test_cannot_remove_all_slots_in_a_section(): void {
        $quizobj = $this->create_test_quiz([
            ['TF1', 1, 'truefalse'],
            ['TF2', 1, 'truefalse'],
            'Heading 2',
            ['TF3', 2, 'truefalse'],
        ]);
        $structure = structure::create_for_quiz($quizobj);

        $structure->remove_slot(1);
        $this->expectException(\coding_exception::class);
        $structure->remove_slot(2);
    }

    public function test_cannot_remove_last_slot_in_a_section(): void {
        $quizobj = $this->create_test_quiz([
                ['TF1', 1, 'truefalse'],
                ['TF2', 1, 'truefalse'],
                'Heading 2',
                ['TF3', 2, 'truefalse'],
        ]);
        $structure = structure::create_for_quiz($quizobj);

        $this->expectException(\coding_exception::class);
        $structure->remove_slot(3);
    }

    public function test_can_remove_last_question_in_a_quiz(): void {
        $quizobj = $this->create_test_quiz([
                'Heading 1',
                ['TF1', 1, 'truefalse'],
        ]);
        $structure = structure::create_for_quiz($quizobj);

        $structure->remove_slot(1);

        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $questiongenerator->create_question_category();
        $q = $questiongenerator->create_question('truefalse', null,
                ['name' => 'TF2', 'category' => $cat->id]);

        quiz_add_quiz_question($q->id, $quizobj->get_quiz(), 0);
        $structure = structure::create_for_quiz($quizobj);

        $this->assert_quiz_layout([
                'Heading 1',
                ['TF2', 1, 'truefalse'],
        ], $structure);
    }

    public function test_add_question_updates_headings(): void {
        $quizobj = $this->create_test_quiz([
                ['TF1', 1, 'truefalse'],
                'Heading 2',
                ['TF2', 2, 'truefalse'],
        ]);

        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $questiongenerator->create_question_category();
        $q = $questiongenerator->create_question('truefalse', null,
                ['name' => 'TF3', 'category' => $cat->id]);

        quiz_add_quiz_question($q->id, $quizobj->get_quiz(), 1);

        $structure = structure::create_for_quiz($quizobj);
        $this->assert_quiz_layout([
                ['TF1', 1, 'truefalse'],
                ['TF3', 1, 'truefalse'],
                'Heading 2',
                ['TF2', 2, 'truefalse'],
        ], $structure);
    }

    public function test_add_question_updates_headings_even_with_one_question_sections(): void {
        $quizobj = $this->create_test_quiz([
                'Heading 1',
                ['TF1', 1, 'truefalse'],
                'Heading 2',
                ['TF2', 2, 'truefalse'],
                'Heading 3',
                ['TF3', 3, 'truefalse'],
        ]);

        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $questiongenerator->create_question_category();
        $q = $questiongenerator->create_question('truefalse', null,
                ['name' => 'TF4', 'category' => $cat->id]);

        quiz_add_quiz_question($q->id, $quizobj->get_quiz(), 1);

        $structure = structure::create_for_quiz($quizobj);
        $this->assert_quiz_layout([
                'Heading 1',
                ['TF1', 1, 'truefalse'],
                ['TF4', 1, 'truefalse'],
                'Heading 2',
                ['TF2', 2, 'truefalse'],
                'Heading 3',
                ['TF3', 3, 'truefalse'],
        ], $structure);
    }

    public function test_add_question_at_end_does_not_update_headings(): void {
        $quizobj = $this->create_test_quiz([
                ['TF1', 1, 'truefalse'],
                'Heading 2',
                ['TF2', 2, 'truefalse'],
        ]);

        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $questiongenerator->create_question_category();
        $q = $questiongenerator->create_question('truefalse', null,
                ['name' => 'TF3', 'category' => $cat->id]);

        quiz_add_quiz_question($q->id, $quizobj->get_quiz(), 0);

        $structure = structure::create_for_quiz($quizobj);
        $this->assert_quiz_layout([
                ['TF1', 1, 'truefalse'],
                'Heading 2',
                ['TF2', 2, 'truefalse'],
                ['TF3', 2, 'truefalse'],
        ], $structure);
    }

    public function test_remove_page_break(): void {
        $quizobj = $this->create_test_quiz([
                ['TF1', 1, 'truefalse'],
                ['TF2', 2, 'truefalse'],
        ]);
        $structure = structure::create_for_quiz($quizobj);

        $slotid = $structure->get_question_in_slot(2)->slotid;
        $slots = $structure->update_page_break($slotid, repaginate::LINK);

        $structure = structure::create_for_quiz($quizobj);
        $this->assert_quiz_layout([
                ['TF1', 1, 'truefalse'],
                ['TF2', 1, 'truefalse'],
        ], $structure);
    }

    public function test_add_page_break(): void {
        $quizobj = $this->create_test_quiz([
                ['TF1', 1, 'truefalse'],
                ['TF2', 1, 'truefalse'],
        ]);
        $structure = structure::create_for_quiz($quizobj);

        $slotid = $structure->get_question_in_slot(2)->slotid;
        $slots = $structure->update_page_break($slotid, repaginate::UNLINK);

        $structure = structure::create_for_quiz($quizobj);
        $this->assert_quiz_layout([
                ['TF1', 1, 'truefalse'],
                ['TF2', 2, 'truefalse'],
        ], $structure);
    }

    public function test_update_question_dependency(): void {
        $quizobj = $this->create_test_quiz([
                ['TF1', 1, 'truefalse'],
                ['TF2', 1, 'truefalse'],
        ]);
        $structure = structure::create_for_quiz($quizobj);

        // Test adding a dependency.
        $slotid = $structure->get_slot_id_for_slot(2);
        $structure->update_question_dependency($slotid, true);

        // Having done an update, we need to reload $structure.
        $structure = structure::create_for_quiz($quizobj);
        $this->assertEquals(1, $structure->is_question_dependent_on_previous_slot(2));

        // Test removing a dependency.
        $structure->update_question_dependency($slotid, false);

        // Having done an update, we need to reload $structure.
        $structure = structure::create_for_quiz($quizobj);
        $this->assertEquals(0, $structure->is_question_dependent_on_previous_slot(2));
    }

    public function test_update_slot_version(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $quizgenerator = $this->getDataGenerator()->get_plugin_generator('mod_quiz');
        $quiz = $quizgenerator->create_instance(['course' => $course->id, 'questionsperpage' => 0,
                'grade' => 100.0, 'sumgrades' => 2]);

        get_coursemodule_from_instance('quiz', $quiz->id, $course->id);

        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $questiongenerator->create_question_category();
        $numq = $questiongenerator->create_question('numerical', null, ['category' => $cat->id]);
        $questiongenerator->update_question($numq, null, ['name' => 'Second version of numq']);
        quiz_add_quiz_question($numq->id, $quiz);

        $quizobj = quiz_settings::create($quiz->id);
        $quizobj->preload_questions();
        [$question] = array_values($quizobj->get_questions(null, false));
        $structure = $quizobj->get_structure();

        // Updating to a version which exists, should succeed.
        $this->assertTrue($structure->update_slot_version($question->slotid, 2));

        // Updating to the same version as the current version should return false.
        $this->assertFalse($structure->update_slot_version($question->slotid, 2));

        // Updating to a version which does not exists, should throw exception.
        $this->expectException(coding_exception::class);
        $this->expectExceptionMessage('Version: 3 does not exist for question bank entry: ' . $question->questionbankentryid);
        $structure->update_slot_version($question->slotid, 3);

    }

    public function test_update_slot_grade_item(): void {
        $quizobj = $this->create_test_quiz([
                ['TF1', 1, 'truefalse'],
                ['TF2', 1, 'truefalse'],
        ]);
        /** @var \mod_quiz_generator $quizgenerator */
        $quizgenerator = $this->getDataGenerator()->get_plugin_generator('mod_quiz');
        $gradeitem = $quizgenerator->create_grade_item(
            ['quizid' => $quizobj->get_quizid(), 'name' => 'Awesomeness!']);
         $structure = structure::create_for_quiz($quizobj);

        // Test setting the grade item for a slot.
        $slot = $structure->get_slot_by_number(1);
        $this->assertTrue($structure->update_slot_grade_item($slot, $gradeitem->id));

        // Having done an update, we need to reload $structure.
        $structure = structure::create_for_quiz($quizobj);
        $slot = $structure->get_slot_by_number(1);
        $this->assertEquals($gradeitem->id, $slot->quizgradeitemid);

        // Test returns false if no change.
        $this->assertFalse($structure->update_slot_grade_item($slot, $gradeitem->id));

        // Test unsetting grade item.
        $this->assertTrue($structure->update_slot_grade_item($slot, 0));

        // Having done an update, we need to reload $structure.
        $structure = structure::create_for_quiz($quizobj);
        $slot = $structure->get_slot_by_number(1);
        $this->assertEquals(null, $slot->quizgradeitemid);

        // Test returns false if no change.
        $this->assertFalse($structure->update_slot_grade_item($slot, null));
    }

    public function test_cannot_set_nonnull_slot_grade_item_for_description(): void {
        $quizobj = $this->create_test_quiz([
                ['Info', 1, 'description'],
        ]);
        /** @var \mod_quiz_generator $quizgenerator */
        $quizgenerator = $this->getDataGenerator()->get_plugin_generator('mod_quiz');
        $gradeitem = $quizgenerator->create_grade_item(
            ['quizid' => $quizobj->get_quizid(), 'name' => 'Awesomeness!']);
        $structure = structure::create_for_quiz($quizobj);

        $this->expectException(coding_exception::class);
        $structure->update_slot_grade_item($structure->get_slot_by_number(1), $gradeitem->id);
    }

    /**
     * Test for can_add_random_questions.
     */
    public function test_can_add_random_questions(): void {
        $this->resetAfterTest();

        $quiz = $this->create_test_quiz([]);
        $course = $quiz->get_course();

        $generator = $this->getDataGenerator();
        $teacher = $generator->create_and_enrol($course, 'editingteacher');
        $noneditingteacher = $generator->create_and_enrol($course, 'teacher');

        $this->setUser($teacher);
        $structure = structure::create_for_quiz($quiz);
        $this->assertTrue($structure->can_add_random_questions());

        $this->setUser($noneditingteacher);
        $structure = structure::create_for_quiz($quiz);
        $this->assertFalse($structure->can_add_random_questions());
    }

    /**
     * Test to get the version information for a question to show in the version selection dropdown.
     *
     * @covers ::get_question_version_info
     */
    public function test_get_version_choices_for_slot(): void {
        $this->resetAfterTest();

        $quizobj = $this->create_test_quiz([]);

        // Create a question with two versions.
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $questiongenerator->create_question_category(['contextid' => $quizobj->get_context()->id]);
        $q = $questiongenerator->create_question('essay', null,
                ['category' => $cat->id, 'name' => 'This is the first version']);
        $questiongenerator->update_question($q, null, ['name' => 'This is the second version']);
        $questiongenerator->update_question($q, null, ['name' => 'This is the third version']);
        quiz_add_quiz_question($q->id, $quizobj->get_quiz());

        // Create the quiz object.
        $structure = structure::create_for_quiz($quizobj);
        $versiondata = $structure->get_version_choices_for_slot(1);
        $this->assertEquals(4, count($versiondata));
        $this->assertEquals('Always latest', $versiondata[0]->versionvalue);
        $this->assertEquals('v3 (latest)', $versiondata[1]->versionvalue);
        $this->assertEquals('v2', $versiondata[2]->versionvalue);
        $this->assertEquals('v1', $versiondata[3]->versionvalue);
        $this->assertTrue($versiondata[0]->selected);
        $this->assertFalse($versiondata[1]->selected);
        $this->assertFalse($versiondata[2]->selected);
        $this->assertFalse($versiondata[3]->selected);
    }

    /**
     * Test the current user have '...use' capability over the question(s) in a given slot.
     *
     * @covers ::has_use_capability
     */
    public function test_has_use_capability(): void {
        $this->resetAfterTest();

        // Create a quiz with question.
        $quizobj = $this->create_test_quiz([]);
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $questiongenerator->create_question_category(['contextid' => $quizobj->get_context()->id]);
        $q = $questiongenerator->create_question('essay', null,
            ['category' => $cat->id, 'name' => 'This is essay question']);
        quiz_add_quiz_question($q->id, $quizobj->get_quiz());

        // Create the quiz object.
        $structure = structure::create_for_quiz($quizobj);
        $slots = $structure->get_slots();

        // Get slot.
        $slotid = array_pop($slots)->slot;

        $course = $quizobj->get_course();
        $generator = $this->getDataGenerator();
        $teacher = $generator->create_and_enrol($course, 'editingteacher');
        $student = $generator->create_and_enrol($course);

        $this->setUser($teacher);
        $this->assertTrue($structure->has_use_capability($slotid));

        $this->setUser($student);
        $this->assertFalse($structure->has_use_capability($slotid));
    }
}
