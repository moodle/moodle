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

namespace core_courseformat\external;

use core_external\external_api;

/**
 * Test class for log_view_overview_information external service.
 *
 * @package    core_courseformat
 * @category   test
 * @copyright  2025 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(log_view_overview_information::class)]
final class log_view_overview_information_test extends \core_external\tests\externallib_testcase {
    /**
     * Test for webservice log_view_overview_information base cases.
     *
     * @param string|null $role The role to assign to the user.
     * @param bool $expectexception Whether to expect an exception.
     * @param bool $expectstatus Whether to expect a successful status.
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('provider_log_view_overview_information')]
    public function test_view_page(
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

        $result = log_view_overview_information::execute($course->id);
        $result = external_api::clean_returnvalue(log_view_overview_information::execute_returns(), $result);

        $this->assertSame($expectstatus, $result['status']);
        // This webservice has no warnings.
        $this->assertSame([], $result['warnings']);

        $events = $sink->get_events();

        $this->assertCount(1, $events);
        $this->assertInstanceOf(\core\event\course_overview_viewed::class, $events[0]);
        $sink->close();
    }

    /**
     * Data provider for test_view_page.
     *
     * @return array
     */
    public static function provider_log_view_overview_information(): \Generator {
        yield 'student role' => [
            'role' => 'student',
            'expectexception' => false,
            'expectstatus' => true,
        ];
        yield 'teacher role' => [
            'role' => 'editingteacher',
            'expectexception' => false,
            'expectstatus' => true,
        ];
        yield 'no role' => [
            'role' => null,
            'expectexception' => true,
            'expectstatus' => false,
        ];
    }

    /**
     * Test for webservice log_view_overview_information when invalid course ID is provided.
     */
    public function test_view_page_invalid_course_id(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user->id, $course->id, 'student');

        $this->setUser($user);

        $this->expectException(\moodle_exception::class);

        $result = log_view_overview_information::execute(-1);
        $result = external_api::clean_returnvalue(log_view_overview_information::execute_returns(), $result);
    }

    /**
     * Test for webservice log_view_overview_information when site ID is provided.
     */
    public function test_view_page_invalid_site_id(): void {
        global $SITE;
        $this->resetAfterTest();

        // Using admin to ensure the exception is not depending on capabilities.
        $this->setAdminUser();

        $this->expectException(\moodle_exception::class);

        $result = log_view_overview_information::execute($SITE->id);
        $result = external_api::clean_returnvalue(log_view_overview_information::execute_returns(), $result);
    }
}
