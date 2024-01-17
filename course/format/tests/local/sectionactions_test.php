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

namespace core_courseformat\local;

use stdClass;

/**
 * Section format actions class tests.
 *
 * @package    core_courseformat
 * @copyright  2023 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \core_courseformat\sectionactions
 */
class sectionactions_test extends \advanced_testcase {
    /**
     * Setup to ensure that fixtures are loaded.
     */
    public static function setUpBeforeClass(): void {
        global $CFG;
        require_once($CFG->dirroot . '/course/lib.php');
    }

    /**
     * Test for create_delegated method.
     * @covers ::create_delegated
     * @dataProvider create_delegated_provider
     * @param string $component the name of the plugin
     * @param int|null $itemid the id of the delegated section
     * @param stdClass|null $fields the fields to set on the section
     */
    public function test_create_delegated(string $component, ?int $itemid, ?stdClass $fields): void {
        global $DB;
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course(['format' => 'topics', 'numsections' => 1]);

        $sectionactions = new sectionactions($course);
        $section = $sectionactions->create_delegated($component, $itemid, $fields);

        $this->assertEquals($component, $section->component);
        $this->assertEquals($itemid, $section->itemid);
        if (!empty($fields)) {
            foreach ($fields as $field => $value) {
                $this->assertEquals($value, $section->$field);
            }
        }
    }

    /**
     * Data provider for test_create_delegated.
     * @return array
     */
    public static function create_delegated_provider(): array {
        return [
            'component with no itemid or fields' => [
                'mod_assign',
                null,
                null,
            ],
            'component with itemid but no fields' => [
                'mod_assign',
                1,
                null,
            ],
            'component with itemid and empty fields' => [
                'mod_assign',
                1,
                new stdClass(),
            ],
            'component with itemid and name field' => [
                'mod_assign',
                1,
                (object) ['name' => 'new name'],
            ],
            'component with no itemid but name field' => [
                'mod_assign',
                null,
                (object) ['name' => 'new name'],
            ],
            'component with itemid and summary' => [
                'mod_assign',
                1,
                (object) ['summary' => 'summary'],
            ],
            'component with itemid and summary, summaryformat ' => [
                'mod_assign',
                1,
                (object) ['summary' => 'summary', 'summaryformat' => 1],
            ],
            'component with itemid and section number' => [
                'mod_assign',
                1,
                (object) ['section' => 2],
            ],
            'component with itemid and visible 1' => [
                'mod_assign',
                1,
                (object) ['visible' => 1],
            ],
            'component with itemid and visible 0' => [
                'mod_assign',
                1,
                (object) ['visible' => 0],
            ],
        ];
    }

    /**
     * Test for create method.
     * @covers ::create
     * @dataProvider create_provider
     * @param int $sectionnum the name of the plugin
     * @param bool $skip if the validation should be skipped
     * @param bool $expectexception if the method should throw an exception
     * @param int $expected the expected section number
     */
    public function test_create(int $sectionnum, bool $skip, bool $expectexception, int $expected): void {
        global $DB;
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course(['format' => 'topics', 'numsections' => 1]);

        $sectionactions = new sectionactions($course);

        if ($expectexception) {
            $this->expectException(\dml_write_exception::class);
        }
        $section = $sectionactions->create($sectionnum, $skip);

        $this->assertEquals($expected, $section->section);
    }

    /**
     * Data provider for test_create_delegated.
     * @return array
     */
    public static function create_provider(): array {
        return [
            'section 1' => [
                'sectionnum' => 1,
                'skip' => false,
                'expectexception' => false,
                'expected' => 1,
            ],
            'section 2' => [
                'sectionnum' => 2,
                'skip' => false,
                'expectexception' => false,
                'expected' => 2,
            ],
            'section 3' => [
                'sectionnum' => 3,
                'skip' => false,
                'expectexception' => false,
                'expected' => 2,
            ],
            'section 4' => [
                'sectionnum' => 4,
                'skip' => false,
                'expectexception' => false,
                'expected' => 2,
            ],
            'section 1 with exception' => [
                'sectionnum' => 1,
                'skip' => true,
                'expectexception' => true,
                'expected' => 0,
            ],
            'section 2 with skip validation' => [
                'sectionnum' => 2,
                'skip' => true,
                'expectexception' => false,
                'expected' => 2,
            ],
            'section 5 with skip validation' => [
                'sectionnum' => 5,
                'skip' => true,
                'expectexception' => false,
                'expected' => 5,
            ],
        ];
    }

    /**
     * Test create sections when there are sections with comonent (delegated sections) in the course.
     * @covers ::create
     * @covers ::create_delegated
     */
    public function test_create_with_delegated_sections(): void {
        global $DB;
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course(
            ['format' => 'topics', 'numsections' => 1],
            ['createsections' => true],
        );

        $sectionactions = new sectionactions($course);
        $section = $sectionactions->create_delegated('mod_forum', 1);
        $this->assertEquals(2, $section->section);
        $delegateid = $section->id;

        // Regular sections are created before delegated ones.
        $section = $sectionactions->create(2);
        $this->assertEquals(2, $section->section);
        $regularid = $section->id;

        $modinfo = get_fast_modinfo($course);

        $section2 = $modinfo->get_section_info(2);
        $this->assertEquals($regularid, $section2->id);
        $this->assertEquals(2, $section2->section);

        $sectiondelegated = $modinfo->get_section_info_by_component('mod_forum', 1);
        $this->assertEquals($delegateid, $sectiondelegated->id);
        $this->assertEquals(3, $sectiondelegated->section);

        // New delegates should be after the current delegate sections.
        $section = $sectionactions->create_delegated('mod_forum', 2);
        $this->assertEquals(4, $section->section);
    }

    /**
     * Test for create_if_missing method.
     * @covers ::create_if_missing
     * @dataProvider create_if_missing_provider
     * @param array $sectionnums the section numbers to create
     * @param bool $expected the expected result
     */
    public function test_create_if_missing(array $sectionnums, bool $expected): void {
        global $DB;
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course(['format' => 'topics', 'numsections' => 2]);

        $sectionactions = new sectionactions($course);
        $result = $sectionactions->create_if_missing($sectionnums);

        $this->assertEquals($expected, $result);

        $modinfo = get_fast_modinfo($course);
        foreach ($sectionnums as $sectionnum) {
            $section = $modinfo->get_section_info($sectionnum);
            $this->assertEquals($sectionnum, $section->section);
        }
    }

    /**
     * Data provider for test_create_delegated.
     * @return array
     */
    public static function create_if_missing_provider(): array {
        return [
            'existing section' => [
                'sectionnum' => [1],
                'expected' => false,
            ],
            'unexisting section' => [
                'sectionnum' => [3],
                'expected' => true,
            ],
            'several existing sections' => [
                'sectionnum' => [1, 2],
                'expected' => false,
            ],
            'several unexisting sections' => [
                'sectionnum' => [3, 4],
                'expected' => true,
            ],
            'empty array' => [
                'sectionnum' => [],
                'expected' => false,
            ],
            'existent and unexistent sections' => [
                'sectionnum' => [1, 2, 3, 4],
                'expected' => true,
            ],
        ];
    }

    /**
     * Test create if missing when the course has delegated sections.
     * @covers ::create_if_missing
     * @covers ::create_delegated
     */
    public function test_create_if_missing_with_delegated_sections(): void {
        global $DB;
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course(
            ['format' => 'topics', 'numsections' => 1],
            ['createsections' => true],
        );

        $sectionactions = new sectionactions($course);
        $section = $sectionactions->create_delegated('mod_forum', 1);
        $delegateid = $section->id;

        $result = $sectionactions->create_if_missing([1, 2]);
        $this->assertTrue($result);

        $modinfo = get_fast_modinfo($course);
        $section = $modinfo->get_section_info(2);
        $this->assertEquals(2, $section->section);
        $this->assertNotEquals($delegateid, $section->id);
        $delegatedsection = $modinfo->get_section_info_by_id($delegateid);
        $this->assertEquals(3, $delegatedsection->section);

        $result = $sectionactions->create_if_missing([1, 2]);
        $this->assertFalse($result);

        $result = $sectionactions->create_if_missing([1, 2, 3]);
        $this->assertTrue($result);

        $modinfo = get_fast_modinfo($course);
        $section = $modinfo->get_section_info(3);
        $this->assertEquals(3, $section->section);
        $this->assertNotEquals($delegateid, $section->id);
        $delegatedsection = $modinfo->get_section_info_by_id($delegateid);
        $this->assertEquals(4, $delegatedsection->section);

        $result = $sectionactions->create_if_missing([1, 2, 3]);
        $this->assertFalse($result);
    }

    /**
     * Test for delete method.
     * @covers ::delete
     */
    public function test_delete(): void {
        global $DB;
        $this->resetAfterTest(true);

        $generator = $this->getDataGenerator();

        $course = $generator->create_course(
            ['numsections' => 6, 'format' => 'topics'],
            ['createsections' => true]
        );
        $assign0 = $generator->create_module('assign', ['course' => $course, 'section' => 0]);
        $assign1 = $generator->create_module('assign', ['course' => $course, 'section' => 1]);
        $assign21 = $generator->create_module('assign', ['course' => $course, 'section' => 2]);
        $assign22 = $generator->create_module('assign', ['course' => $course, 'section' => 2]);
        $assign3 = $generator->create_module('assign', ['course' => $course, 'section' => 3]);
        $assign5 = $generator->create_module('assign', ['course' => $course, 'section' => 5]);
        $assign6 = $generator->create_module('assign', ['course' => $course, 'section' => 6]);

        $this->setAdminUser();

        $sectionactions = new sectionactions($course);
        $sections = get_fast_modinfo($course)->get_section_info_all();

        // Attempt to delete 0-section.
        $this->assertFalse($sectionactions->delete($sections[0], true));
        $this->assertTrue($DB->record_exists('course_modules', ['id' => $assign0->cmid]));
        $this->assertEquals(6, course_get_format($course)->get_last_section_number());

        // Delete last section.
        $this->assertTrue($sectionactions->delete($sections[6], true));
        $this->assertFalse($DB->record_exists('course_modules', ['id' => $assign6->cmid]));
        $this->assertEquals(5, course_get_format($course)->get_last_section_number());

        // Delete empty section.
        $this->assertTrue($sectionactions->delete($sections[4], false));
        $this->assertEquals(4, course_get_format($course)->get_last_section_number());

        // Delete section in the middle (2).
        $this->assertFalse($sectionactions->delete($sections[2], false));
        $this->assertEquals(4, course_get_format($course)->get_last_section_number());
        $sections = get_fast_modinfo($course)->get_section_info_all();
        $this->assertTrue($sectionactions->delete($sections[2], true));
        $this->assertFalse($DB->record_exists('course_modules', ['id' => $assign21->cmid]));
        $this->assertFalse($DB->record_exists('course_modules', ['id' => $assign22->cmid]));
        $this->assertEquals(3, course_get_format($course)->get_last_section_number());
        $this->assertEquals(
            [
                0 => [$assign0->cmid],
                1 => [$assign1->cmid],
                2 => [$assign3->cmid],
                3 => [$assign5->cmid],
            ],
            get_fast_modinfo($course)->sections
        );

        // Remove marked section.
        course_set_marker($course->id, 1);
        $this->assertTrue(course_get_format($course)->is_section_current(1));
        $this->assertTrue($sectionactions->delete(
            get_fast_modinfo($course)->get_section_info(1),
            true
        ));
        $this->assertFalse(course_get_format($course)->is_section_current(1));
    }

    /**
     * Test that triggering a course_section_deleted event works as expected.
     * @covers ::delete
     */
    public function test_section_deleted_event(): void {
        global $USER, $DB;
        $this->resetAfterTest();
        $sink = $this->redirectEvents();

        // Create the course with sections.
        $course = $this->getDataGenerator()->create_course(['numsections' => 10], ['createsections' => true]);
        $coursecontext = \context_course::instance($course->id);

        $section = get_fast_modinfo($course)->get_section_info(10);
        $sectionrecord = $DB->get_record('course_sections', ['id' => $section->id]);

        $sectionactions = new sectionactions($course);
        $sectionactions->delete($section);

        $events = $sink->get_events();
        $event = array_pop($events); // Delete section event.
        $sink->close();

        // Validate event data.
        $this->assertInstanceOf('\core\event\course_section_deleted', $event);
        $this->assertEquals('course_sections', $event->objecttable);
        $this->assertEquals($section->id, $event->objectid);
        $this->assertEquals($course->id, $event->courseid);
        $this->assertEquals($coursecontext->id, $event->contextid);
        $this->assertEquals($section->section, $event->other['sectionnum']);
        $expecteddesc = "The user with id '{$event->userid}' deleted section number '{$event->other['sectionnum']}' " .
            "(section name '{$event->other['sectionname']}') for the course with id '{$event->courseid}'";
        $this->assertEquals($expecteddesc, $event->get_description());
        $this->assertEquals($sectionrecord, $event->get_record_snapshot('course_sections', $event->objectid));
        $this->assertNull($event->get_url());
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test async section deletion hook.
     * @covers ::delete
     */
    public function test_async_section_deletion_hook_implemented(): void {
        // Async section deletion (provided section contains modules), depends on the 'true' being returned by at least one plugin
        // implementing the 'course_module_adhoc_deletion_recommended' hook. In core, is implemented by the course recyclebin,
        // which will only return true if the plugin is enabled. To make sure async deletion occurs, this test enables recyclebin.
        global $DB, $USER;
        $this->resetAfterTest(true);
        $this->setAdminUser();

        // Ensure recyclebin is enabled.
        set_config('coursebinenable', true, 'tool_recyclebin');

        // Create course, module and context.
        $generator = $this->getDataGenerator();
        $course = $generator->create_course(['numsections' => 4, 'format' => 'topics'], ['createsections' => true]);
        $assign0 = $generator->create_module('assign', ['course' => $course, 'section' => 2]);
        $assign1 = $generator->create_module('assign', ['course' => $course, 'section' => 2]);
        $assign2 = $generator->create_module('assign', ['course' => $course, 'section' => 2]);
        $assign3 = $generator->create_module('assign', ['course' => $course, 'section' => 0]);

        $sectionactions = new sectionactions($course);

        // Delete empty section. No difference from normal, synchronous behaviour.
        $this->assertTrue($sectionactions->delete(get_fast_modinfo($course)->get_section_info(4), false, true));
        $this->assertEquals(3, course_get_format($course)->get_last_section_number());

        // Delete a module in section 2 (using async). Need to verify this doesn't generate two tasks when we delete
        // the section in the next step.
        course_delete_module($assign2->cmid, true);

        // Confirm that the module is pending deletion in its current section.
        $section = $DB->get_record('course_sections', ['course' => $course->id, 'section' => '2']); // For event comparison.
        $this->assertEquals(true, $DB->record_exists('course_modules', ['id' => $assign2->cmid, 'deletioninprogress' => 1,
            'section' => $section->id]));

        // Non-empty section, no forcedelete, so no change.
        $this->assertFalse($sectionactions->delete(get_fast_modinfo($course)->get_section_info(2), false, true));

        $sink = $this->redirectEvents();
        $this->assertTrue($sectionactions->delete(get_fast_modinfo($course)->get_section_info(2), true, true));

        // Now, confirm that:
        // a) the section's modules have been flagged for deletion and moved to section 0 and;
        // b) the section has been deleted and;
        // c) course_section_deleted event has been fired. The course_module_deleted events will only fire once they have been
        // removed from section 0 via the adhoc task.

        // Modules should have been flagged for deletion and moved to section 0.
        $sectionid = $DB->get_field('course_sections', 'id', ['course' => $course->id, 'section' => 0]);
        $this->assertEquals(
            3,
            $DB->count_records('course_modules', ['section' => $sectionid, 'deletioninprogress' => 1])
        );

        // Confirm the section has been deleted.
        $this->assertEquals(2, course_get_format($course)->get_last_section_number());

        // Check event fired.
        $events = $sink->get_events();
        $event = array_pop($events);
        $sink->close();
        $this->assertInstanceOf('\core\event\course_section_deleted', $event);
        $this->assertEquals($section->id, $event->objectid);
        $this->assertEquals($USER->id, $event->userid);
        $this->assertEquals('course_sections', $event->objecttable);
        $this->assertEquals(null, $event->get_url());
        $this->assertEquals($section, $event->get_record_snapshot('course_sections', $section->id));

        // Now, run the adhoc task to delete the modules from section 0.
        $sink = $this->redirectEvents(); // To capture the events.
        \phpunit_util::run_all_adhoc_tasks();

        // Confirm the modules have been deleted.
        list($insql, $assignids) = $DB->get_in_or_equal([$assign0->cmid, $assign1->cmid, $assign2->cmid]);
        $cmcount = $DB->count_records_select('course_modules', 'id ' . $insql, $assignids);
        $this->assertEmpty($cmcount);

        // Confirm other modules in section 0 still remain.
        $this->assertEquals(1, $DB->count_records('course_modules', ['id' => $assign3->cmid]));

        // Confirm that events were generated for all 3 of the modules.
        $events = $sink->get_events();
        $sink->close();
        $count = 0;
        while (!empty($events)) {
            $event = array_pop($events);
            if ($event instanceof \core\event\course_module_deleted &&
                in_array($event->objectid, [$assign0->cmid, $assign1->cmid, $assign2->cmid])) {
                $count++;
            }
        }
        $this->assertEquals(3, $count);
    }
}
