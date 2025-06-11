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

use advanced_testcase;
use ReflectionClass;

/**
 * This file contains unit tests for the mod_feedback items.
 *
 * @package    mod_feedback
 * @copyright  2020 Mikhail Golenkov <mikhailgolenkov@catalyst-au.net>
 * @author     2023 David Woloszyn <david.woloszyn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class item_test extends advanced_testcase {

    /**
     * Test that get_analysed() for textarea item returns correctly formatted data for exporting to Excel.
     *
     * @covers ::get_analysed
     */
    public function test_get_analysed_textarea_for_excel_export(): void {
        global $DB;
        $this->resetAfterTest();

        // Create a course, a feedback activity and an item.
        $course = $this->getDataGenerator()->create_course();
        $feedback = $this->getDataGenerator()->create_module('feedback', ['course' => $course]);
        $feedbackgenerator = $this->getDataGenerator()->get_plugin_generator('mod_feedback');
        $item = $feedbackgenerator->create_item_textfield($feedback);

        // Expected text.
        $valuetext = "First line\nSecond line";

        // Create a temporary response.
        $completedid = $DB->insert_record('feedback_completedtmp', (object)['feedback' => $feedback->id]);
        $completed = $DB->get_record('feedback_completedtmp', ['id' => $completedid], '*', MUST_EXIST);
        $value = (object)['course_id' => $course->id, 'item' => $item->id, 'completed' => $completedid, 'value' => $valuetext];
        $DB->insert_record('feedback_valuetmp', $value);
        feedback_save_tmp_values($completed);

        // Set get_analysed() method accessibility.
        $itemclass = feedback_get_item_class('textarea');
        $reflection = new ReflectionClass($itemclass);
        $method = $reflection->getMethod('get_analysed');

        // Call the method and indicate it is being used for Excel.
        $actual = $method->invoke(new $itemclass(), $item, false, $course->id, true);

        // Check returned data maintains the line break.
        $this->assertCount(1, $actual->data);
        $datum = reset($actual->data);
        $this->assertEquals($valuetext, $datum);
    }
}
