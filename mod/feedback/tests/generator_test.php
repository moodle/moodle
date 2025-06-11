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

namespace mod_feedback;

/**
 * Generator tests class.
 *
 * @package    mod_feedback
 * @copyright  2013 Ankit Agarwal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class generator_test extends \advanced_testcase {

    public function test_create_instance(): void {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();

        $this->assertFalse($DB->record_exists('feedback', array('course' => $course->id)));
        $feedback = $this->getDataGenerator()->create_module('feedback', array('course' => $course->id));
        $this->assertEquals(1, $DB->count_records('feedback', array('course' => $course->id)));
        $this->assertTrue($DB->record_exists('feedback', array('course' => $course->id)));
        $this->assertTrue($DB->record_exists('feedback', array('id' => $feedback->id)));

        $params = array('course' => $course->id, 'name' => 'One more feedback');
        $feedback = $this->getDataGenerator()->create_module('feedback', $params);
        $this->assertEquals(2, $DB->count_records('feedback', array('course' => $course->id)));
        $this->assertEquals('One more feedback', $DB->get_field_select('feedback', 'name', 'id = :id',
                array('id' => $feedback->id)));
    }

    public function test_create_item_info(): void {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $feedback = $this->getDataGenerator()->create_module('feedback', array('course' => $course));
        $feedbackgenerator = $this->getDataGenerator()->get_plugin_generator('mod_feedback');

        $item1 = $feedbackgenerator->create_item_info($feedback);
        $item2 = $feedbackgenerator->create_item_info($feedback, array('name' => 'Custom name'));
        $records = $DB->get_records('feedback_item', array('feedback' => $feedback->id), 'id');
        $this->assertCount(2, $records);
        $this->assertEquals($item1->id, $records[$item1->id]->id);
        $this->assertEquals($item2->id, $records[$item2->id]->id);
        $this->assertEquals('Custom name', $records[$item2->id]->name);
        $this->assertEquals('info', $records[$item1->id]->typ);
    }

    public function test_create_item_label(): void {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $feedback = $this->getDataGenerator()->create_module('feedback', array('course' => $course));
        $feedbackgenerator = $this->getDataGenerator()->get_plugin_generator('mod_feedback');

        $item1 = $feedbackgenerator->create_item_label($feedback);

        $editor = array(
            'text' => "Custom editor",
            'format' => FORMAT_HTML,
            'itemid' => 0
        );
        $item2 = $feedbackgenerator->create_item_label($feedback, array('presentation_editor' => $editor));
        $records = $DB->get_records('feedback_item', array('feedback' => $feedback->id), 'id');
        $this->assertCount(2, $records);
        $this->assertEquals($item1->id, $records[$item1->id]->id);
        $this->assertEquals($item2->id, $records[$item2->id]->id);
        $this->assertEquals($editor['text'], $records[$item2->id]->presentation);
        $this->assertEquals('label', $records[$item1->id]->typ);
    }

    public function test_create_item_multichoice(): void {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $feedback = $this->getDataGenerator()->create_module('feedback', array('course' => $course));
        $feedbackgenerator = $this->getDataGenerator()->get_plugin_generator('mod_feedback');

        $item1 = $feedbackgenerator->create_item_multichoice($feedback);
        $item2 = $feedbackgenerator->create_item_multichoice($feedback, array('values' => "1\n2\n3\n4\n5", 'horizontal' => 1));
        $records = $DB->get_records('feedback_item', array('feedback' => $feedback->id), 'id');
        $this->assertCount(2, $records);
        $this->assertEquals($item1->id, $records[$item1->id]->id);
        $this->assertEquals($item2->id, $records[$item2->id]->id);
        $this->assertEquals('r>>>>>a|b|c|d|e', $records[$item1->id]->presentation);
        $this->assertEquals('r>>>>>1|2|3|4|5<<<<<1', $records[$item2->id]->presentation);
        $this->assertEquals('multichoice', $records[$item1->id]->typ);
    }

    public function test_create_item_multichoicerated(): void {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $feedback = $this->getDataGenerator()->create_module('feedback', array('course' => $course));
        $feedbackgenerator = $this->getDataGenerator()->get_plugin_generator('mod_feedback');

        $item1 = $feedbackgenerator->create_item_multichoicerated($feedback);
        $item2 = $feedbackgenerator->create_item_multichoicerated($feedback, array(
                    'values' => "0/1\n1/2\n2/3\n3/4\n4/5", 'horizontal' => 1));
        $records = $DB->get_records('feedback_item', array('feedback' => $feedback->id), 'id');
        $this->assertCount(2, $records);
        $this->assertEquals($item1->id, $records[$item1->id]->id);
        $this->assertEquals($item2->id, $records[$item2->id]->id);
        $this->assertEquals('r>>>>>0####a|1####b|2####c|3####d|4####e', $records[$item1->id]->presentation);
        $this->assertEquals('r>>>>>0####1|1####2|2####3|3####4|4####5<<<<<1', $records[$item2->id]->presentation);
        $this->assertEquals('multichoicerated', $records[$item1->id]->typ);
    }

    public function test_create_item_numeric(): void {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $feedback = $this->getDataGenerator()->create_module('feedback', array('course' => $course));
        $feedbackgenerator = $this->getDataGenerator()->get_plugin_generator('mod_feedback');

        $item1 = $feedbackgenerator->create_item_numeric($feedback);
        $item2 = $feedbackgenerator->create_item_numeric($feedback, array('rangefrom' => '0', 'rangeto' => '10'));
        $records = $DB->get_records('feedback_item', array('feedback' => $feedback->id), 'id');
        $this->assertCount(2, $records);
        $this->assertEquals($item1->id, $records[$item1->id]->id);
        $this->assertEquals($item2->id, $records[$item2->id]->id);
        $this->assertEquals('-|-', $records[$item1->id]->presentation);
        $this->assertEquals('0|10', $records[$item2->id]->presentation);
        $this->assertEquals('numeric', $records[$item1->id]->typ);
    }

    public function test_create_item_textarea(): void {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $feedback = $this->getDataGenerator()->create_module('feedback', array('course' => $course));
        $feedbackgenerator = $this->getDataGenerator()->get_plugin_generator('mod_feedback');

        $item1 = $feedbackgenerator->create_item_textarea($feedback);
        $item2 = $feedbackgenerator->create_item_textarea($feedback, array('itemwidth' => '20', 'itemheight' => '10'));
        $records = $DB->get_records('feedback_item', array('feedback' => $feedback->id), 'id');
        $this->assertCount(2, $records);
        $this->assertEquals($item1->id, $records[$item1->id]->id);
        $this->assertEquals($item2->id, $records[$item2->id]->id);
        $this->assertEquals('40|20', $records[$item1->id]->presentation);
        $this->assertEquals('20|10', $records[$item2->id]->presentation);
        $this->assertEquals('textarea', $records[$item1->id]->typ);
    }

    public function test_create_item_textfield(): void {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $feedback = $this->getDataGenerator()->create_module('feedback', array('course' => $course));
        $feedbackgenerator = $this->getDataGenerator()->get_plugin_generator('mod_feedback');

        $item1 = $feedbackgenerator->create_item_textfield($feedback);
        $item2 = $feedbackgenerator->create_item_textfield($feedback, array('itemsize' => '20', 'itemmaxlength' => '10'));
        $records = $DB->get_records('feedback_item', array('feedback' => $feedback->id), 'id');
        $this->assertCount(2, $records);
        $this->assertEquals($item1->id, $records[$item1->id]->id);
        $this->assertEquals($item2->id, $records[$item2->id]->id);
        $this->assertEquals('20|30', $records[$item1->id]->presentation);
        $this->assertEquals('20|10', $records[$item2->id]->presentation);
        $this->assertEquals('textfield', $records[$item1->id]->typ);
    }

    public function test_create_item_pagebreak(): void {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $feedback = $this->getDataGenerator()->create_module('feedback', array('course' => $course));
        $feedbackgenerator = $this->getDataGenerator()->get_plugin_generator('mod_feedback');

        // Try to create a page break in an empty feedback (no items).
        $feedbackgenerator->create_item_pagebreak($feedback);
        $records = $DB->get_records('feedback_item', array('feedback' => $feedback->id), 'id');
        $this->assertCount(0, $records);

        // Create at least one item before the pagebreak.
        $feedbackgenerator->create_item_textfield($feedback);

        // Now, create one pagebreak.
        $item1 = $feedbackgenerator->create_item_pagebreak($feedback);
        $records = $DB->get_records('feedback_item', array('feedback' => $feedback->id), 'id');
        $this->assertCount(2, $records);

        // This pagebreak won't be created (there is already one in the last position).
        $item2 = $feedbackgenerator->create_item_pagebreak($feedback);
        $this->assertFalse($item2);
        $records = $DB->get_records('feedback_item', array('feedback' => $feedback->id), 'id');
        $this->assertCount(2, $records);
    }
}
