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
 * Data generators tests
 *
 * @package    core_question
 * @subpackage questionengine
 * @copyright  2013 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_question;

/**
 * Test data generator
 *
 * @copyright  2013 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class generator_test extends \advanced_testcase {
    public function test_create() {
        global $DB;

        $this->resetAfterTest();
        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');

        $count = $DB->count_records('question_categories');

        $cat = $generator->create_question_category();
        $count += $count ? 1 : 2; // Calling $generator->create_question_category() for the first time
                                  // creates a Top category as well.
        $this->assertEquals($count, $DB->count_records('question_categories'));

        $cat = $generator->create_question_category(['name' => 'My category', 'sortorder' => 1]);
        $this->assertSame('My category', $cat->name);
        $this->assertSame(1, $cat->sortorder);
    }

    public function test_idnumbers_in_categories_and_questions() {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');
        list($category, $course, $qcat, $questions) = $generator->setup_course_and_questions();
        // Check default idnumbers in question_category and questions.
        $this->assertNull($qcat->idnumber);
        $this->assertNull($questions[0]->idnumber);
        $this->assertNull($questions[1]->idnumber);
        // Check created idnumbers.
        $qcat1 = $generator->create_question_category([
                'name' => 'My category', 'sortorder' => 1, 'idnumber' => 'myqcat']);
        $this->assertSame('myqcat', $qcat1->idnumber);
        $quest1 = $generator->update_question($questions[0], null, ['idnumber' => 'myquest']);
        $this->assertSame('myquest', $quest1->idnumber);
        $quest3 = $generator->create_question('shortanswer', null,
                ['name' => 'sa1', 'category' => $qcat1->id, 'idnumber' => 'myquest_3']);
        $this->assertSame('myquest_3', $quest3->idnumber);
        // Check idnumbers of questions moved. Note have to use load_question_data or we only get to see old cached data.
        question_move_questions_to_category([$quest1->id], $qcat1->id);
        $this->assertSame('myquest', \question_bank::load_question_data($quest1->id)->idnumber);
        // Can only change idnumber of quest2 once quest1 has been moved to another category.
        $quest2 = $generator->update_question($questions[1], null, ['idnumber' => 'myquest_4']);
        question_move_questions_to_category([$quest2->id], $qcat1->id);
        $this->assertSame('myquest_4', \question_bank::load_question_data($quest2->id)->idnumber);
        // Check can add an idnumber of 0.
        $quest4 = $generator->create_question('shortanswer', null, ['name' => 'sa1', 'category' => $qcat1->id, 'idnumber' => '0']);
        $this->assertSame('0', $quest4->idnumber);
    }
}
