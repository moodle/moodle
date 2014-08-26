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
 * Tests the \core\event\user_graded event.
 *
 * @package    core
 * @category   phpunit
 * @copyright  2014 Petr Skoda
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->libdir . '/mathslib.php');

/**
 * Class core_event_user_graded_testcase
 *
 * Tests for event \core\event\user_graded
 *
 * @package    core
 * @category   test
 * @copyright  2014 Petr Skoda
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_event_user_graded_testcase extends advanced_testcase {

    /**
     * Tests set up.
     */
    public function setUp() {
        $this->resetAfterTest();
    }

    /**
     * Tests the event details.
     */
    public function test_event() {
        global $CFG;
        require_once("$CFG->libdir/gradelib.php");

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

    /**
     * Tests that the event is fired in the correct locations in core.
     */
    public function test_event_is_triggered() {
        global $DB;

        // Create the items we need to test with.
        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user->id, $course->id);
        $quiz = $this->getDataGenerator()->create_module('quiz', array('course' => $course->id));

        // Now mark the quiz using grade_update as this is the function that modules use.
        $grade = array();
        $grade['userid'] = $user->id;
        $grade['rawgrade'] = 50;

        $sink = $this->redirectEvents();
        grade_update('mod/quiz', $course->id, 'mod', 'quiz', $quiz->id, 0, $grade);
        $events = $sink->get_events();
        $event = reset($events);
        $sink->close();

        // Ensure we have a user_graded event.
        $this->assertEquals(1, count($events));
        $this->assertInstanceOf('\core\event\user_graded', $event);

        // Get the grade item.
        $gradeitem = grade_item::fetch(array('itemtype' => 'mod', 'itemmodule' => 'quiz', 'iteminstance' => $quiz->id,
            'courseid' => $course->id));

        // Let's alter the grade in the DB so when we call regrade_final_grades() it is changed and an event is called.
        $sql = "UPDATE {grade_grades}
                   SET finalgrade = '2'
                 WHERE itemid = :itemid
                   AND userid = :userid";
        $DB->execute($sql, array('itemid' => $gradeitem->id, 'userid' => $user->id));

        // Now check when we regrade this that there is a user graded event.
        $sink = $this->redirectEvents();
        $gradeitem->regrade_final_grades();
        $events = $sink->get_events();
        $event = reset($events);
        $sink->close();

        // Ensure we have a user_graded event.
        $this->assertEquals(1, count($events));
        $this->assertInstanceOf('\core\event\user_graded', $event);

        // Remove the grades.
        $gradeitem->delete_all_grades();

        // Now, create a grade using update_raw_grade().
        $sink = $this->redirectEvents();
        $gradeitem->update_raw_grade($user->id, 50);
        $events = $sink->get_events();
        $event = reset($events);
        $sink->close();

        // Ensure we have a user_graded event.
        $this->assertEquals(1, count($events));
        $this->assertInstanceOf('\core\event\user_graded', $event);

        // Now, update this grade using update_raw_grade().
        $sink = $this->redirectEvents();
        $gradeitem->update_raw_grade($user->id, 100);
        $events = $sink->get_events();
        $event = reset($events);
        $sink->close();

        $this->assertEquals(1, count($events));
        $this->assertInstanceOf('\core\event\user_graded', $event);

        // Remove the grades.
        $gradeitem->delete_all_grades();

        // Now, create a grade using update_final_grade().
        $sink = $this->redirectEvents();
        $gradeitem->update_final_grade($user->id, 50);
        $events = $sink->get_events();
        $event = reset($events);
        $sink->close();

        // Ensure we have a user_graded event.
        $this->assertEquals(1, count($events));
        $this->assertInstanceOf('\core\event\user_graded', $event);

        // Now, update this grade using update_final_grade().
        $sink = $this->redirectEvents();
        $gradeitem->update_final_grade($user->id, 100);
        $events = $sink->get_events();
        $event = reset($events);
        $sink->close();

        $this->assertEquals(1, count($events));
        $this->assertInstanceOf('\core\event\user_graded', $event);

        // Let's change the calculation to anything that won't cause an error.
        $calculation = calc_formula::unlocalize("=3");
        $gradeitem->set_calculation($calculation);

        // Let's alter the grade in the DB so when we call compute() it is changed and an event is called.
        $sql = "UPDATE {grade_grades}
                   SET finalgrade = 2, overridden = 0
                 WHERE itemid = :itemid
                   AND userid = :userid";
        $DB->execute($sql, array('itemid' => $gradeitem->id, 'userid' => $user->id));

        // Now check when we compute that there is a user graded event.
        $sink = $this->redirectEvents();
        $gradeitem->compute();
        $events = $sink->get_events();
        $event = reset($events);
        $sink->close();

        $this->assertEquals(1, count($events));
        $this->assertInstanceOf('\core\event\user_graded', $event);
    }
}
