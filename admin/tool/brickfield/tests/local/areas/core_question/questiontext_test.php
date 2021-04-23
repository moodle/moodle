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

namespace tool_brickfield\local\areas\core_question;

/**
 * Tests for questiontext.
 *
 * @package     tool_brickfield
 * @copyright   2020 onward: Brickfield Education Labs, https://www.brickfield.ie
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class questiontext_test extends \advanced_testcase {

    /**
     * Set up before class.
     */
    public static function setUpBeforeClass(): void {
        global $CFG;
        require_once($CFG->dirroot . '/mod/quiz/locallib.php');
    }

    /**
     * Test find course areas.
     */
    public function test_find_course_areas() {
        $this->resetAfterTest();
        $this->setAdminUser();

        $category = $this->getDataGenerator()->create_category();
        $course = $this->getDataGenerator()->create_course(['category' => $category->id]);
        $coursecontext = \context_course::instance($course->id);
        $catcontext = \context_coursecat::instance($category->id);
        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat1 = $generator->create_question_category(['contextid' => $coursecontext->id]);
        $question1 = $generator->create_question('multichoice', null, ['category' => $cat1->id]);
        $question2 = $generator->create_question('multichoice', null, ['category' => $cat1->id]);
        $questiontext = new questiontext();
        $rs = $questiontext->find_course_areas($course->id);
        $this->assertNotNull($rs);

        $count = 0;
        foreach ($rs as $rec) {
            $count++;
            $this->assertEquals($coursecontext->id, $rec->contextid);
            $this->assertEquals($course->id, $rec->courseid);
            if ($count <= 1) {
                $this->assertEquals($question1->id, $rec->itemid);
            } else {
                $this->assertEquals($question2->id, $rec->itemid);
            }
        }
        $rs->close();
        $this->assertEquals(2, $count);

        // Add a question to a quiz in the course.
        $quiz = $this->getDataGenerator()->create_module('quiz', ['course' => $course->id, 'name' => 'Quiz1']);
        $quizmodule = get_coursemodule_from_instance('quiz', $quiz->id, $course->id);
        $quizcontext = \context_module::instance($quizmodule->id);

        // Add a question to the quiz context.
        $cat2 = $generator->create_question_category(['contextid' => $quizcontext->id]);
        $question3 = $generator->create_question('multichoice', null, ['category' => $cat2->id]);
        $rs2 = $questiontext->find_course_areas($course->id);
        $this->assertNotNull($rs2);

        $count = 0;
        foreach ($rs2 as $rec) {
            $count++;
            if ($count <= 1) {
                $this->assertEquals($coursecontext->id, $rec->contextid);
                $this->assertEquals($course->id, $rec->courseid);
                $this->assertEquals($question1->id, $rec->itemid);
            } else if ($count <= 2) {
                $this->assertEquals($coursecontext->id, $rec->contextid);
                $this->assertEquals($course->id, $rec->courseid);
                $this->assertEquals($question2->id, $rec->itemid);
            } else {
                $this->assertEquals($quizcontext->id, $rec->contextid);
                $this->assertEquals($course->id, $rec->courseid);
                $this->assertEquals($question3->id, $rec->itemid);
            }
        }
        $rs2->close();
        $this->assertEquals(3, $count);

        // Add a question to the category context.
        $cat3 = $generator->create_question_category(['contextid' => $catcontext->id]);
        $question4 = $generator->create_question('multichoice', null, ['category' => $cat3->id]);
        $rs3 = $questiontext->find_course_areas($course->id);
        $this->assertNotNull($rs3);

        // The category level questions should not be found.
        $count = 0;
        foreach ($rs3 as $rec) {
            $count++;
            if ($count > 2) {
                $this->assertEquals($quizcontext->id, $rec->contextid);
                $this->assertEquals($course->id, $rec->courseid);
                $this->assertEquals($question3->id, $rec->itemid);
            }
        }
        $rs2->close();
        $this->assertEquals(3, $count);
    }

    /**
     * Test find relevant areas.
     */
    public function test_find_relevant_areas() {
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $coursecontext = \context_course::instance($course->id);
        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat1 = $generator->create_question_category(['contextid' => $coursecontext->id]);
        $question1 = $generator->create_question('multichoice', null, ['category' => $cat1->id]);
        $question2 = $generator->create_question('multichoice', null, ['category' => $cat1->id]);
        $questiontext = new questiontext();
        $event = \core\event\question_updated::create_from_question_instance($question1,
            \context_course::instance($course->id));
        $rs = $questiontext->find_relevant_areas($event);
        $this->assertNotNull($rs);

        $count = 0;
        foreach ($rs as $rec) {
            $count++;
            $this->assertEquals($coursecontext->id, $rec->contextid);
            $this->assertEquals($course->id, $rec->courseid);
            $this->assertEquals($question1->id, $rec->itemid);
        }
        $rs->close();
        $this->assertEquals(1, $count);
    }

    /**
     * Test find system areas.
     */
    public function test_find_system_areas() {
        $this->resetAfterTest();
        $this->setAdminUser();

        $category = $this->getDataGenerator()->create_category();
        $catcontext = \context_coursecat::instance($category->id);
        $systemcontext = \context_system::instance();
        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $generator->create_question_category(['contextid' => $catcontext->id]);
        $cat2 = $generator->create_question_category(['contextid' => $systemcontext->id]);
        $question = $generator->create_question('multichoice', null, ['category' => $cat2->id]);
        $question2 = $generator->create_question('multichoice', null, ['category' => $cat->id]);
        $questiontext = new questiontext();
        $rs = $questiontext->find_system_areas();
        $this->assertNotNull($rs);

        $count = 0;
        foreach ($rs as $rec) {
            $count++;
            if ($count <= 1) {
                $this->assertEquals($systemcontext->id, $rec->contextid);
                $this->assertEquals(1, $rec->courseid);
                $this->assertEquals(0, $rec->categoryid);
                $this->assertEquals($question->id, $rec->itemid);
            } else {
                $this->assertEquals($catcontext->id, $rec->contextid);
                $this->assertEquals(1, $rec->courseid);
                $this->assertEquals($category->id, $rec->categoryid);
                $this->assertEquals($question2->id, $rec->itemid);
            }
        }
        $rs->close();
        $this->assertEquals(2, $count);
    }
}
