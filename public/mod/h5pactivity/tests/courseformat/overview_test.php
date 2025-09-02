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

namespace mod_h5pactivity\courseformat;

use core_courseformat\local\overview\overviewfactory;

/**
 * Tests for H5P activity overview
 *
 * @package    mod_h5pactivity
 * @category   test
 * @copyright  2025 Amaia Anabitarte <amaia@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(overview::class)]
final class overview_test extends \advanced_testcase {
    /**
     * Test get_actions_overview.
     */
    public function test_get_actions_overview(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $activity = $this->getDataGenerator()->create_module(
            'h5pactivity',
            ['course' => $course, 'enabletracking' => 1],
        );
        $cm = get_fast_modinfo($course)->get_cm($activity->cmid);

        // Prepare users: 1 teacher, 2 students, 1 unenroled user.
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        // Students have no action column.
        $this->setUser($student);
        $this->assertNull(overviewfactory::create($cm)->get_actions_overview());

        // Teachers have a 'View results' button.
        $this->setUser($teacher);
        $items = overviewfactory::create($cm)->get_actions_overview();
        $this->assertNotNull($items);
        $this->assertEquals(get_string('actions'), $items->get_name());
    }

    /**
     * Test get_extra_h5ptype_overview.
     *
     * @param string $h5pfile
     * @param bool $iscorrect
     * @param string $expected
     * @return void
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('provider_test_get_extra_h5type_overview')]
    public function test_get_extra_h5ptype_overview(
        string $h5pfile,
        bool $iscorrect,
        string $expected
    ): void {
        global $CFG;

        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $params = [
            'course' => $course->id,
            'packagefilepath' => "{$CFG->dirroot}/h5p/tests/fixtures/{$h5pfile}",
            'introformat' => 1,
        ];
        $activity = $this->getDataGenerator()->create_module('h5pactivity', $params);
        // Add filename and contextid to make easier the asserts.
        $activity->filename = $h5pfile;
        $context = \context_module::instance($activity->cmid);
        $activity->contextid = $context->id;

        // Create a fake deploy H5P file.

        /** @var \core_h5p_generator $h5pgenerator */
        $h5pgenerator = $this->getDataGenerator()->get_plugin_generator('core_h5p');

        if (!$iscorrect) {
            $this->expectException(\TypeError::class);
        }
        $h5pgenerator->create_export_file($activity->filename, $context->id, 'mod_h5pactivity', 'package');

        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $this->setUser($teacher);

        $cm = get_fast_modinfo($course)->get_cm($activity->cmid);
        $items = overviewfactory::create($cm)->get_extra_overview_items();

        $this->assertEquals($expected, $items['h5ptype']->get_value());

        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $this->setUser($student);

        $cm = get_fast_modinfo($course)->get_cm($activity->cmid);
        $items = overviewfactory::create($cm)->get_extra_overview_items();

        $this->assertNull($items['h5ptype']);
    }

    /**
     * Data provider for test h5p type overview extra.
     *
     * @return \Generator
     */
    public static function provider_test_get_extra_h5type_overview(): \Generator {
        yield 'Basic package' => [
            'h5pfile' => 'basic_essay.h5p',
            'iscorrect' => true,
            'expected' => 'Essay',
        ];
        yield 'No json file' => [
            'h5pfile' => 'no-json-file.h5p',
            'iscorrect' => false,
            'expected' => get_string('unknowntype', 'mod_h5pactivity'),
        ];
        yield 'Unzippable package' => [
            'h5pfile' => 'unzippable.h5p',
            'iscorrect' => false,
            'expected' => get_string('unknowntype', 'mod_h5pactivity'),
        ];
    }

    /**
     * Test get_extra_overview_items.
     */
    public function test_get_extra_attempts_overview(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $activity = $this->getDataGenerator()->create_module(
            'h5pactivity',
            ['course' => $course, 'enabletracking' => 1],
        );
        $cm = get_fast_modinfo($course)->get_cm($activity->cmid);

        // Prepare users: 1 teacher, 2 students, 1 unenroled user.
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $other = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $generator = $this->getDataGenerator()->get_plugin_generator('mod_h5pactivity');

        // No attempts yet.
        $this->setUser($teacher);
        $items = overviewfactory::create($cm)->get_extra_overview_items();
        $this->assertEquals(0, $items['totalattempts']->get_value());
        $this->assertNull($items['myattempts']);

        $this->setUser($student);
        $items = overviewfactory::create($cm)->get_extra_overview_items();
        $this->assertEquals(0, $items['myattempts']->get_value());
        $this->assertNull($items['totalattempts']);

        // Attempts done by other student.
        $params = ['cmid' => $cm->id, 'userid' => $other->id];
        $generator->create_content($activity, $params);
        $generator->create_content($activity, $params);

        $this->setUser($teacher);
        $items = overviewfactory::create($cm)->get_extra_overview_items();
        $this->assertEquals(2, $items['totalattempts']->get_value());
        $this->assertNull($items['myattempts']);

        $this->setUser($student);
        $items = overviewfactory::create($cm)->get_extra_overview_items();
        $this->assertEquals(0, $items['myattempts']->get_value());
        $this->assertNull($items['totalattempts']);

        // Attempts done by the student.
        $params = ['cmid' => $cm->id, 'userid' => $student->id];
        $generator->create_content($activity, $params);
        $generator->create_content($activity, $params);

        $this->setUser($teacher);
        $items = overviewfactory::create($cm)->get_extra_overview_items();
        $this->assertEquals(4, $items['totalattempts']->get_value());
        $this->assertNull($items['myattempts']);

        $this->setUser($student);
        $items = overviewfactory::create($cm)->get_extra_overview_items();
        $this->assertEquals(2, $items['myattempts']->get_value());
        $this->assertNull($items['totalattempts']);
    }

    /**
     * Test get_extra_studentsattempted_overview.
     */
    public function test_get_extra_studentsattempted_overview(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $activity = $this->getDataGenerator()->create_module(
            'h5pactivity',
            ['course' => $course, 'enabletracking' => 1],
        );
        $cm = get_fast_modinfo($course)->get_cm($activity->cmid);

        // Prepare users: 1 teacher, 2 students, 1 unenroled user.
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $this->getDataGenerator()->create_and_enrol($course, 'student');

        $generator = $this->getDataGenerator()->get_plugin_generator('mod_h5pactivity');

        // No attempts yet.
        $this->setUser($teacher);
        $items = overviewfactory::create($cm)->get_extra_overview_items();
        $this->assertEquals(0, $items['attempted']->get_value());
        $this->assertEquals('<strong>0</strong> of 2', $items['attempted']->get_content());

        // With attempts.
        $params = ['cmid' => $cm->id, 'userid' => $student->id];
        $generator->create_content($activity, $params);
        $generator->create_content($activity, $params);

        $items = overviewfactory::create($cm)->get_extra_overview_items();
        $this->assertEquals(1, $items['attempted']->get_value());
        $this->assertEquals('<strong>1</strong> of 2', $items['attempted']->get_content());

        // Student.
        $this->setUser($student);
        $items = overviewfactory::create($cm)->get_extra_overview_items();
        $this->assertNull($items['attempted']);
    }

    /**
     * Test get_extra_studentsattempted_overview filtering by groups.
     */
    public function test_get_extra_studentsattempted_overview_by_groups(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $g1 = $this->getDataGenerator()->create_group(['courseid' => $course->id]);
        $g2 = $this->getDataGenerator()->create_group(['courseid' => $course->id]);
        $activity = $this->getDataGenerator()->create_module(
            'h5pactivity',
            ['course' => $course, 'enabletracking' => 1, 'groupmode' => SEPARATEGROUPS],
        );
        $cm = get_fast_modinfo($course)->get_cm($activity->cmid);

        // Prepare users: 1 editing teacher, 1 non-editing teacher, 2 students, 1 unenroled user.
        $editing = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $nonediting = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $studentingroups = $this->getDataGenerator()->create_and_enrol($course, 'student');
        groups_add_member($g1, $editing->id);
        groups_add_member($g1, $nonediting->id);
        groups_add_member($g1, $studentingroups->id);
        groups_add_member($g2, $student->id);

        $generator = $this->getDataGenerator()->get_plugin_generator('mod_h5pactivity');

        // Attempts by student not in the non-editing teacher's group.
        $params = ['cmid' => $cm->id, 'userid' => $student->id];
        $generator->create_content($activity, $params);
        $generator->create_content($activity, $params);

        // No visible attempts for the non-editing teacher yet.
        $this->setUser($nonediting);
        $items = overviewfactory::create($cm)->get_extra_overview_items();
        $this->assertEquals(0, $items['attempted']->get_value());
        $this->assertEquals('<strong>0</strong> of 1', $items['attempted']->get_content());

        // Editing teacher can see all the attempts and users, whatever the group is.
        $this->setUser($editing);
        $items = overviewfactory::create($cm)->get_extra_overview_items();
        $this->assertEquals(1, $items['attempted']->get_value());
        $this->assertEquals('<strong>1</strong> of 2', $items['attempted']->get_content());

        // Attempts by student in non-editing teacher's group.
        $params = ['cmid' => $cm->id, 'userid' => $studentingroups->id];
        $generator->create_content($activity, $params);
        $generator->create_content($activity, $params);

        // Editing teacher can see all the attempts and users, whatever the group is.
        $items = overviewfactory::create($cm)->get_extra_overview_items();
        $this->assertEquals(2, $items['attempted']->get_value());
        $this->assertEquals('<strong>2</strong> of 2', $items['attempted']->get_content());

        // Non-editing teacher can see their group activity and users.
        $this->setUser($nonediting);
        $items = overviewfactory::create($cm)->get_extra_overview_items();
        $this->assertEquals(1, $items['attempted']->get_value());
        $this->assertEquals('<strong>1</strong> of 1', $items['attempted']->get_content());
    }

    /**
     * Test get_extra_totalattempts_overview filtering by groups.
     */
    public function test_get_extra_totalattempts_overview_by_groups(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $g1 = $this->getDataGenerator()->create_group(['courseid' => $course->id]);
        $g2 = $this->getDataGenerator()->create_group(['courseid' => $course->id]);
        $activity = $this->getDataGenerator()->create_module(
            'h5pactivity',
            ['course' => $course, 'enabletracking' => 1, 'groupmode' => SEPARATEGROUPS],
        );
        $cm = get_fast_modinfo($course)->get_cm($activity->cmid);

        // Prepare users: 1 editing teacher, 1 non-editing teacher, 2 students, 1 unenroled user.
        $editing = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $nonediting = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $studentingroups = $this->getDataGenerator()->create_and_enrol($course, 'student');
        groups_add_member($g1, $editing->id);
        groups_add_member($g1, $nonediting->id);
        groups_add_member($g1, $studentingroups->id);
        groups_add_member($g2, $student->id);

        $generator = $this->getDataGenerator()->get_plugin_generator('mod_h5pactivity');

        // Attempts by student not in the non-editing teacher's group.
        $params = ['cmid' => $cm->id, 'userid' => $student->id];
        $generator->create_content($activity, $params);
        $generator->create_content($activity, $params);

        // No visible attempts for the non-editing teacher yet.
        $this->setUser($nonediting);
        $items = overviewfactory::create($cm)->get_extra_overview_items();
        $this->assertEquals(0, $items['totalattempts']->get_value());
        $this->assertEquals(0, $items['totalattempts']->get_content()->get_items()[1]->value); // Average attempts (0).

        // Editing teacher can see all the attempts, whatever the group is.
        $this->setUser($editing);
        $items = overviewfactory::create($cm)->get_extra_overview_items();
        $this->assertEquals(2, $items['totalattempts']->get_value());
        $this->assertEquals(2, $items['totalattempts']->get_content()->get_items()[1]->value); // Average attempts (2/1).

        // Attempts by student in non-editing teacher's group.
        $params = ['cmid' => $cm->id, 'userid' => $studentingroups->id];
        $generator->create_content($activity, $params);
        $generator->create_content($activity, $params);
        $generator->create_content($activity, $params);

        // Editing teacher can see all the attempts, whatever the group is.
        $items = overviewfactory::create($cm)->get_extra_overview_items();
        $this->assertEquals(5, $items['totalattempts']->get_value());
        $this->assertEquals(2.5, $items['totalattempts']->get_content()->get_items()[1]->value); // Average attempts (5/2).

        // Non-editing teacher can see their group activity.
        $this->setUser($nonediting);
        $items = overviewfactory::create($cm)->get_extra_overview_items();
        $this->assertEquals(3, $items['totalattempts']->get_value());
        $this->assertEquals(3, $items['totalattempts']->get_content()->get_items()[1]->value); // Average attempts (3/1).
    }
}
