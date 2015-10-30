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
 * Tests for base course module viewed event.
 *
 * @package    core
 * @category   phpunit
 * @copyright  2014 Petr Skoda
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Class core_event_user_graded_testcase
 *
 * Tests for event \core\event\user_graded
 *
 * @package    core
 * @category   phpunit
 * @copyright  2014 Petr Skoda
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_event_user_graded_testcase extends advanced_testcase {
    /**
     * Test the event.
     */
    public function test_event() {
        global $CFG;
        require_once("$CFG->libdir/gradelib.php");

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user->id, $course->id);

        $grade_category = grade_category::fetch_course_category($course->id);
        $grade_category->load_grade_item();
        $grade_item = $grade_category->grade_item;

        $grade_item->update_final_grade($user->id, 10, 'gradebook');

        $grade_grade = new grade_grade(array('userid' => $user->id, 'itemid' => $grade_item->id), true);
        $grade_grade->grade_item = $grade_item;

        $event = \core\event\user_graded::create_from_grade($grade_grade);

        $this->assertEventLegacyLogData(
            array($course->id, 'grade', 'update', '/report/grader/index.php?id=' . $course->id, $grade_item->itemname . ': ' . fullname($user)),
            $event
        );
        $this->assertEquals(context_course::instance($course->id), $event->get_context());
        $this->assertSame($event->objecttable, 'grade_grades');
        $this->assertEquals($event->objectid, $grade_grade->id);
        $this->assertEquals($event->other['itemid'], $grade_item->id);
        $this->assertTrue($event->other['overridden']);
        $this->assertEquals(10, $event->other['finalgrade']);

        // Trigger the events.
        $sink = $this->redirectEvents();
        $event->trigger();
        $result = $sink->get_events();
        $sink->close();

        $this->assertCount(1, $result);

        $event = reset($result);
        $this->assertEventContextNotUsed($event);

        $grade = $event->get_grade();
        $this->assertInstanceOf('grade_grade', $grade);
        $this->assertEquals($grade_grade->id, $grade->id);
    }
}
