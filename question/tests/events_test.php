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
 * Events tests.
 *
 * @package core_question
 * @copyright 2014 Mark Nelson <markn@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/question/editlib.php');
require_once($CFG->dirroot . '/question/category_class.php');

class core_question_events_testcase extends advanced_testcase {

    /**
     * Tests set up.
     */
    public function setUp() {
        $this->resetAfterTest();
    }

    /**
     * Test the question category created event.
     */
    public function test_question_category_created() {
        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();
        $quiz = $this->getDataGenerator()->create_module('quiz', array('course' => $course->id));

        $contexts = new question_edit_contexts(context_module::instance($quiz->cmid));

        $defaultcategoryobj = question_make_default_categories(array($contexts->lowest()));
        $defaultcategory = $defaultcategoryobj->id . ',' . $defaultcategoryobj->contextid;

        $qcobject = new question_category_object(
            1,
            new moodle_url('/mod/quiz/edit.php', array('cmid' => $quiz->cmid)),
            $contexts->having_one_edit_tab_cap('categories'),
            $defaultcategoryobj->id,
            $defaultcategory,
            null,
            $contexts->having_cap('moodle/question:add'));

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $categoryid = $qcobject->add_category($defaultcategory, 'newcategory', '', true);
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\core\event\question_category_created', $event);
        $this->assertEquals(context_module::instance($quiz->cmid), $event->get_context());
        $expected = array($course->id, 'quiz', 'addcategory', 'view.php?id=' . $quiz->cmid , $categoryid, $quiz->cmid);
        $this->assertEventLegacyLogData($expected, $event);
        $this->assertEventContextNotUsed($event);
    }
}
