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

namespace qbank_customfields\event;

/**
 * Tests for question_deleted_observer
 *
 * @package   qbank_customfields
 * @copyright 2023 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author    Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers    \qbank_customfields\event\question_deleted_observer
 */
class question_deleted_observer_test extends \advanced_testcase {

    /**
     * Deleting a question with customfield data should also delete the data.
     *
     * @return void
     */
    public function test_delete_question_with_customfields(): void {
        $this->resetAfterTest();
        $generator = self::getDataGenerator();
        $data = [
            'component' => 'qbank_customfields',
            'area' => 'question'
        ];

        $categoryid = $generator->create_custom_field_category($data)->get('id');
        $generator->create_custom_field(['categoryid' => $categoryid, 'type' => 'text', 'shortname' => 'f1']);

        $questiongenerator = $generator->get_plugin_generator('core_question');
        [, , , $questions] = $questiongenerator->setup_course_and_questions();
        $question = reset($questions);

        $customfieldhandler = \qbank_customfields\customfield\question_handler::create();
        $questiondata = (object)[
            'id' => $question->id,
            'customfield_f1' => random_string()
        ];

        $customfieldhandler->instance_form_save($questiondata);

        $customdata = $customfieldhandler->get_instance_data($question->id);
        $this->assertCount(1, $customdata);
        $this->assertEquals($questiondata->customfield_f1, reset($customdata)->get_value());

        question_delete_question($question->id);

        $customdata = $customfieldhandler->get_instance_data($question->id);
        $this->assertCount(1, $customdata);
        $this->assertEmpty(reset($customdata)->get_value());
    }
}
