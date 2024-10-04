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
 * Unit tests for core_table\external\dynamic\get;
 *
 * @package   core_table
 * @category  test
 * @copyright  2020 Simey Lameze <simey@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

declare(strict_types = 1);

namespace core_table\external\dynamic;

use core_table\local\filter\filter;
use advanced_testcase;

/**
 * Unit tests for core_table\external\dynamic\get;
 *
 * @package   core_table
 * @category  test
 * @copyright  2020 Simey Lameze <simey@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class get_test extends advanced_testcase {
    /**
     * Test execute invalid component format.
     */
    public function test_execute_invalid_component_format(): void {
        $this->resetAfterTest();

        $this->expectException(\invalid_parameter_exception::class);
        get::execute(
            "core-user",
            "participants",
            "",
            $this->get_sort_array(['email' => SORT_ASC]),
            [],
            (string) filter::JOINTYPE_ANY,
            null,
            null,
            null,
            null,
            [],
            null

        );
    }

    /**
     * Test execute invalid component.
     */
    public function test_execute_invalid_component(): void {
        $this->resetAfterTest();

        $this->expectException(\UnexpectedValueException::class);
        get::execute(
            "core_users",
            "participants",
            "",
            $this->get_sort_array(['email' => SORT_ASC]),
            [],
            (string) filter::JOINTYPE_ANY,
            null,
            null,
            null,
            null,
            [],
            null
        );
    }

    /**
     * Test execute invalid handler.
     */
    public function test_execute_invalid_handler(): void {
        $this->resetAfterTest();

        $this->expectException('UnexpectedValueException');
        $handler = "\\core_user\\table\\users_participants_table";
        $this->expectExceptionMessage("Table handler class {$handler} not found. Please make sure that your table handler class is under the \\core_user\\table namespace.");

        // Tests that invalid users_participants_table class gets an exception.
        get::execute(
            "core_user",
            "users_participants_table",
            "",
            $this->get_sort_array(['email' => SORT_ASC]),
            [],
            (string) filter::JOINTYPE_ANY,
            null,
            null,
            null,
            null,
            [],
            null

        );
    }

    /**
     * Test execute invalid filter.
     */
    public function test_execute_invalid_filter(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();

        // Filter with an invalid name.
        $filter = [
            [
                'fullname' => 'courseid',
                'jointype' => filter::JOINTYPE_ANY,
                'values' => [(int) $course->id]
            ]
        ];
        $this->expectException(\invalid_parameter_exception::class);
        $this->expectExceptionMessage("Invalid parameter value detected (filters => Invalid parameter value detected " .
        "(Missing required key in single structure: name): Missing required key in single structure: name");

        get::execute(
            "core_user",
            "participants", "user-index-participants-{$course->id}",
            $this->get_sort_array(['firstname' => SORT_ASC]),
            $filter,
            (string) filter::JOINTYPE_ANY
        );
    }

    /**
     * Test execute method.
     */
    public function test_table_get_execute(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $user1 = $this->getDataGenerator()->create_and_enrol($course, 'student', ['email' => 's1@moodle.com']);
        $user2 = $this->getDataGenerator()->create_and_enrol($course, 'student', ['email' => 's2@moodle.com']);
        $user3 = $this->getDataGenerator()->create_user(['email' => 's3@moodle.com']);
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher', ['email' => 't1@moodle.com']);

        $this->setUser($teacher);

        $this->get_sort_array(['email' => SORT_ASC]);

        $filter = [
            [
                'name' => 'courseid',
                'jointype' => filter::JOINTYPE_ANY,
                'values' => [(int) $course->id]
            ]
        ];

        $participantstable = get::execute(
            "core_user",
            "participants",
            "user-index-participants-{$course->id}",
            $this->get_sort_array(['firstname' => SORT_ASC]),
            $filter,
            (string) filter::JOINTYPE_ANY,
            null,
            null,
            null,
            null,
            [],
            null
        );
        $html = $participantstable['html'];

        $this->assertStringContainsString($user1->email, $html);
        $this->assertStringContainsString($user2->email, $html);
        $this->assertStringContainsString($teacher->email, $html);
        $this->assertStringNotContainsString($user3->email, $html);
    }


    /**
     * Convert a traditional sort order into a sortorder for the web service.
     *
     * @param array $sortdata
     * @return array
     */
    protected function get_sort_array(array $sortdata): array {
        $newsortorder = [];
        foreach ($sortdata as $sortby => $sortorder) {
            $newsortorder[] = [
                'sortby' => $sortby,
                'sortorder' => $sortorder,
            ];
        }

        return $newsortorder;
    }
}
