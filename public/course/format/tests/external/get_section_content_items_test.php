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
use core_external\tests\externallib_testcase;
use section_info;
use stdClass;

/**
 * Tests for courseformat get_section_content_items web service.
 *
 * @package    core_courseformat
 * @copyright  2025 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(get_section_content_items::class)]
final class get_section_content_items_test extends externallib_testcase {
    /**
     * Test the web service returning course content items for inclusion in activity choosers, etc.
     */
    public function test_execute(): void {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course(['numsections' => 1], ['createsections' => true]);
        $user = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');

        $section = get_fast_modinfo($course)->get_section_info(1);

        // Fetch available content items as the editing teacher.
        $this->setUser($user);
        $result = get_section_content_items::execute($course->id, $section->id);
        $result = external_api::clean_returnvalue(get_section_content_items::execute_returns(), $result);

        $expecteditems = $this->get_current_content_items($user, $course, $section);

        $this->assertEquals($expecteditems, $result['content_items']);
    }

    /**
     * Test the web service returning course content items, specifically in case where the user can't manage activities.
     */
    public function test_execute_no_permission_to_manage(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course(['numsections' => 1], ['createsections' => true]);
        $user = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $section = get_fast_modinfo($course)->get_section_info(1);

        // Fetch available content items as a student, who won't have the permission to manage activities.
        $this->setUser($user);
        $result = get_section_content_items::execute($course->id, $section->id);
        $result = external_api::clean_returnvalue(get_section_content_items::execute_returns(), $result);

        $this->assertEmpty($result['content_items']);
    }

    /**
     * Get the current content items for a user in a course section.
     *
     * @param stdClass $user
     * @param stdClass $course
     * @param section_info $section
     * @return stdClass[]
     */
    private function get_current_content_items(
        stdClass $user,
        stdClass $course,
        section_info $section
    ): array {
        $contentitemservice = new \core_course\local\service\content_item_service(
            new \core_course\local\repository\content_item_readonly_repository()
        );
        return array_map(
            function ($item) use ($section) {
                return (array) $item;
            },
            $contentitemservice->get_content_items_for_user_in_course($user, $course, [], $section),
        );
    }
}
