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
 * Tests for questionanswer.
 *
 * @package     tool_brickfield
 * @copyright   2020 onward: Brickfield Education Labs, https://www.brickfield.ie
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class questionanswers_test extends \advanced_testcase {

    /**
     * Set up before class.
     */
    public static function setUpBeforeClass(): void {
        global $CFG;
        require_once($CFG->dirroot . '/mod/quiz/locallib.php');
        parent::setUpBeforeClass();
    }

    /**
     * Test find relevant areas.
     */
    public function test_find_relevant_areas(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $qbank = $this->getDataGenerator()->create_module('qbank', ['course' => $course->id]);
        $qbankcontext = \context_module::instance($qbank->cmid);
        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat1 = $generator->create_question_category(['contextid' => $qbankcontext->id]);
        $question1 = $generator->create_question('multichoice', null, ['category' => $cat1->id]);
        $question2 = $generator->create_question('multichoice', null, ['category' => $cat1->id]);
        $questionanswers = new questionanswers();
        $event = \core\event\question_updated::create_from_question_instance($question1,
            \context_course::instance($course->id));
        $rs = $questionanswers->find_relevant_areas($event);
        $this->assertNotNull($rs);

        // Each multichoice question generated has four answers.
        $count = 0;
        foreach ($rs as $rec) {
            $count++;
            $this->assertEquals($qbankcontext->id, $rec->contextid);
            $this->assertEquals($course->id, $rec->courseid);
            $this->assertEquals($question1->id, $rec->refid);
        }
        $rs->close();
        $this->assertEquals(4, $count);
    }
}
