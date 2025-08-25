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

namespace core_course\external;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/webservice/tests/helpers.php');

use core_external\external_api;

/**
 * Test class for view_module_instance_list external service.
 *
 * @package    core_course
 * @category   test
 * @copyright  2025 Dani Palou <dani@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(view_module_instance_list::class)]
final class view_module_instance_list_test extends \externallib_advanced_testcase {
    /**
     * Test for webservice view_module_instance_list base cases.
     *
     * @param string $modname The module name.
     * @param string|null $role The role to assign to the user.
     * @param bool $expectexception Whether to expect an exception.
     * @param bool $expectstatus Whether to expect a successful status.
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('provider_view_module_instance_list')]
    public function test_view_list(
        string $modname,
        ?string $role,
        bool $expectexception = false,
        bool $expectstatus = true,
    ): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_user();

        if ($role !== null) {
            $this->getDataGenerator()->enrol_user($user->id, $course->id, $role);
        }

        $this->setUser($user);

        $sink = $this->redirectEvents();

        if ($expectexception) {
            $this->expectException(\moodle_exception::class);
        }

        $result = view_module_instance_list::execute($course->id, $modname);
        $result = external_api::clean_returnvalue(view_module_instance_list::execute_returns(), $result);

        $this->assertSame($expectstatus, $result['status']);
        // This webservice has no warnings.
        $this->assertSame([], $result['warnings']);

        $events = $sink->get_events();

        $this->assertCount(1, $events);
        if ($modname === 'resource') {
            $this->assertInstanceOf(\core\event\course_resources_list_viewed::class, $events[0]);
        } else {
            $this->assertInstanceOf('mod_' . $modname . '\\event\\course_module_instance_list_viewed', $events[0]);
        }

        $sink->close();
    }

    /**
     * Data provider for test_view_list.
     *
     * @return array
     */
    public static function provider_view_module_instance_list(): \Generator {
        yield 'student role' => [
            'modname' => 'assign',
            'role' => 'student',
            'expectexception' => false,
            'expectstatus' => true,
        ];
        yield 'teacher role' => [
            'modname' => 'assign',
            'role' => 'editingteacher',
            'expectexception' => false,
            'expectstatus' => true,
        ];
        yield 'no role' => [
            'modname' => 'assign',
            'role' => null,
            'expectexception' => true,
            'expectstatus' => false,
        ];
        yield 'view resources' => [
            'modname' => 'resource',
            'role' => 'student',
            'expectexception' => false,
            'expectstatus' => true,
        ];
        yield 'invalid modname' => [
            'modname' => 'fake_mod_name',
            'role' => 'student',
            'expectexception' => true,
            'expectstatus' => false,
        ];
    }

    /**
     * Test for webservice view_module_instance_list when invalid course ID is provided.
     */
    public function test_view_list_invalid_course_id(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user->id, $course->id, 'student');

        $this->setUser($user);

        $this->expectException(\moodle_exception::class);

        $result = view_module_instance_list::execute(-1, 'assign');
        $result = external_api::clean_returnvalue(view_module_instance_list::execute_returns(), $result);
    }

    /**
     * Test for webservice view_module_instance_list when course is hidden.
     */
    public function test_view_list_hidden_course(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course(['visible' => 0]);
        $student = $this->getDataGenerator()->create_user();
        $teacher = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($student->id, $course->id, 'student');
        $this->getDataGenerator()->enrol_user($teacher->id, $course->id, 'editingteacher');

        // Teacher can view the course.
        $this->setUser($teacher);

        $sink = $this->redirectEvents();

        $result = view_module_instance_list::execute($course->id, 'assign');
        $result = external_api::clean_returnvalue(view_module_instance_list::execute_returns(), $result);

        $this->assertSame(true, $result['status']);
        $this->assertSame([], $result['warnings']);

        $events = $sink->get_events();

        $this->assertCount(1, $events);
        $this->assertInstanceOf(\core\event\course_module_instance_list_viewed::class, $events[0]);
        $sink->close();

        // Student cannot view the course.
        $this->setUser($student);

        $this->expectException(\moodle_exception::class);

        $result = view_module_instance_list::execute($course->id, 'assign');
        $result = external_api::clean_returnvalue(view_module_instance_list::execute_returns(), $result);
    }
}
