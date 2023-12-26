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

declare(strict_types=1);

namespace core_admin\reportbuilder\local\filters;

use advanced_testcase;
use lang_string;
use core_reportbuilder\local\report\filter;

/**
 * Unit tests for course role report filter
 *
 * @package     core_admin
 * @covers      \core_admin\reportbuilder\local\filters\courserole
 * @copyright   2023 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class courserole_test extends advanced_testcase {

    /**
     * Data provider for {@see test_get_sql_filter}
     *
     * @return array[]
     */
    public static function get_sql_filter_provider(): array {
        return [
            'Empty filter' => ['', '', '', ['admin', 'guest', 'user1', 'user2', 'user3', 'user4']],
            'Filter by role' => ['editingteacher', '', '', ['user1', 'user3', 'user4']],
            'Filter by role in category' => ['editingteacher', 'cat2', '', ['user1', 'user3']],
            'Filter by role in category and course' => ['editingteacher', 'cat2', 'course2', ['user1']],
            'Filter by role in course' => ['student', '', 'course2', ['user2']],
            'Filter by category' => ['', 'cat2', '', ['user1', 'user2', 'user3']],
            'Filter by category and course' => ['', 'cat2', 'course2', ['user1', 'user2']],
            'Filter by course' => ['', '', 'course3', ['user3']],
            'Filter by course (ensure whitespace is trimmed)' => ['', '', '  course3  ', ['user3']],
        ];
    }

    /**
     * Test getting filter SQL
     *
     * @param string $rolename
     * @param string $categoryname
     * @param string $course
     * @param string[] $expectedusers
     *
     * @dataProvider get_sql_filter_provider
     */
    public function test_get_sql_filter(
        string $rolename,
        string $categoryname,
        string $course,
        array $expectedusers,
    ): void {
        global $DB;

        $this->resetAfterTest();

        // Create categories/course structure (categoryone: courseone; categorytwo: coursetwo/coursethree).
        $categoryone = $this->getDataGenerator()->create_category(['name' => 'cat1']);
        $courseone = $this->getDataGenerator()->create_course(['category' => $categoryone->id, 'shortname' => 'course1']);

        $categorytwo = $this->getDataGenerator()->create_category(['name' => 'cat2']);
        $coursetwo = $this->getDataGenerator()->create_course(['category' => $categorytwo->id, 'shortname' => 'course2']);
        $coursethree = $this->getDataGenerator()->create_course(['category' => $categorytwo->id, 'shortname' => 'course3']);

        // User one is a teacher in courseone/coursetwo.
        $userone = $this->getDataGenerator()->create_and_enrol($courseone, 'editingteacher', ['username' => 'user1']);
        $this->getDataGenerator()->enrol_user($userone->id, $coursetwo->id, 'editingteacher');

        // User two is a student in coursetwo.
        $usertwo = $this->getDataGenerator()->create_and_enrol($coursetwo, 'student', ['username' => 'user2']);

        // User three is a teacher in courseone/coursethree.
        $userthree = $this->getDataGenerator()->create_and_enrol($courseone, 'editingteacher', ['username' => 'user3']);
        $this->getDataGenerator()->enrol_user($userthree->id, $coursethree->id, 'editingteacher');

        // User four is a teacher in courseone.
        $userfour = $this->getDataGenerator()->create_and_enrol($courseone, 'editingteacher', ['username' => 'user4']);

        $filter = new filter(
            courserole::class,
            'test',
            new lang_string('yes'),
            'testentity',
            'id',
        );

        // Create instance of our filter, passing given operators (with lookups for role/category).
        [$select, $params] = courserole::create($filter)->get_sql_filter([
            $filter->get_unique_identifier() . '_role' => $DB->get_field('role', 'id', ['shortname' => $rolename]),
            $filter->get_unique_identifier() . '_category' => $DB->get_field('course_categories', 'id', ['name' => $categoryname]),
            $filter->get_unique_identifier() . '_course' => $course,
        ]);

        $users = $DB->get_fieldset_select('user', 'username', $select, $params);
        $this->assertEqualsCanonicalizing($expectedusers, $users);
    }
}
