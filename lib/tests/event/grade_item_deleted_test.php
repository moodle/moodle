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
 * Grade item deleted event tests.
 *
 * @package    core
 * @category   test
 * @copyright  2020 Tom Dickman <tomdickman@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\event;

/**
 * Test for grade item deleted event.
 *
 * @package    core
 * @category   test
 * @copyright  2020 Tom Dickman <tomdickman@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \core\event\grade_item_deleted
 */
class grade_item_deleted_testcase extends \advanced_testcase {

    /**
     * Test the grade item deleted event.
     *
     * @covers ::create_from_grade_item
     */
    public function test_grade_item_deleted() {
        global $DB;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $gradeitemrecord = $this->getDataGenerator()->create_grade_item(['courseid' => $course->id]);
        $gradeitem = \grade_item::fetch(['id' => $gradeitemrecord->id, 'courseid' => $course->id]);

        $countgradeitems = $DB->count_records('grade_items');

        // Trigger and capture the event for deleting a grade item.
        $sink = $this->redirectEvents();
        $gradeitem->delete();
        $events = $sink->get_events();
        $sink->close();

        // Event should only be triggered once.
        $this->assertCount(1, $events);
        $event = reset($events);

        // Expect that the grade item was deleted and the event data is valid.
        $this->assertEquals($countgradeitems - 1, $DB->count_records('grade_items'));
        $this->assertInstanceOf('\core\event\grade_item_deleted', $event);
        $eventdata = $event->get_data();
        $this->assertEquals($gradeitem->id, $eventdata['objectid']);
        $this->assertEquals($gradeitem->courseid, $eventdata['courseid']);
        $this->assertEquals(\context_course::instance($gradeitem->courseid)->id, $eventdata['contextid']);
        $this->assertEquals($gradeitem->itemname, $eventdata['other']['itemname']);
        $this->assertEquals($gradeitem->itemtype, $eventdata['other']['itemtype']);
        $this->assertEquals($gradeitem->itemmodule, $eventdata['other']['itemmodule']);
    }
}
