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
 * locallib tests.
 *
 * @package    mod_lesson
 * @category   test
 * @copyright  2016 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot.'/mod/lesson/locallib.php');

/**
 * locallib testcase.
 *
 * @package    mod_lesson
 * @category   test
 * @copyright  2016 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_lesson_locallib_testcase extends advanced_testcase {

    /**
     * Test duplicating a lesson page element.
     */
    public function test_duplicate_page() {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();
        $lessonmodule = $this->getDataGenerator()->create_module('lesson', array('course' => $course->id));
        // Convert to a lesson object.
        $lesson = new lesson($lessonmodule);

        // Set up a generator to create content.
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_lesson');
        $tfrecord = $generator->create_question_truefalse($lesson);
        $lesson->duplicate_page($tfrecord->id);

        // Lesson pages.
        $records = $DB->get_records('lesson_pages', array('qtype' => 2));
        $sameelements = array('lessonid', 'qtype', 'qoption', 'layout', 'display', 'title', 'contents', 'contentsformat');
        $baserecord = array_shift($records);
        $secondrecord = array_shift($records);
        foreach ($sameelements as $element) {
            $this->assertEquals($baserecord->$element, $secondrecord->$element);
        }
        // Need lesson answers as well.
        $baserecordanswers = array_values($DB->get_records('lesson_answers', array('pageid' => $baserecord->id)));
        $secondrecordanswers = array_values($DB->get_records('lesson_answers', array('pageid' => $secondrecord->id)));
        $sameanswerelements = array('lessonid', 'jumpto', 'grade', 'score', 'flags', 'answer', 'answerformat', 'response',
                'responseformat');
        foreach ($baserecordanswers as $key => $baseanswer) {
            foreach ($sameanswerelements as $element) {
                $this->assertEquals($baseanswer->$element, $secondrecordanswers[$key]->$element);
            }
        }
    }
}
