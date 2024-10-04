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

namespace core_reportbuilder\local\filters;

use advanced_testcase;
use core_reportbuilder\local\report\filter;

/**
 * Unit tests for course selector filter
 *
 * @package     core_reportbuilder
 * @covers      \core_reportbuilder\local\filters\base
 * @covers      \core_reportbuilder\local\filters\autocomplete
 * @copyright   2022 Nathan Nguyen <nathannguyen@catalyst-au.net>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class autocomplete_test extends advanced_testcase {

    /**
     * Data provider for {@see test_get_sql_filter}
     *
     * @return array
     */
    public static function get_sql_filter_provider(): array {
        return [
            [[], ["Course 1 full name", "Course 2 full name", "Course 3 full name", "PHPUnit test site"]],
            [["course1", "course3"], ["Course 1 full name", "Course 3 full name"]],
            [["course1"], ["Course 1 full name"]],
        ];
    }

    /**
     * Test getting filter SQL
     *
     * @param array $shortnames list of course short name
     * @param array $expected list of course full name
     *
     * @dataProvider get_sql_filter_provider
     */
    public function test_get_sql_filter(array $shortnames, array $expected): void {
        global $DB;

        $this->resetAfterTest();

        // Create courses as values for autocompletion.
        $course1 = $this->getDataGenerator()->create_course([
            'fullname' => "Course 1 full name",
            'shortname' => 'course1',
        ]);

        $course2 = $this->getDataGenerator()->create_course([
            'fullname' => "Course 2 full name",
            'shortname' => 'course2',
        ]);

        $course3 = $this->getDataGenerator()->create_course([
            'fullname' => "Course 3 full name",
            'shortname' => 'course3',
        ]);

        $filter = (new filter(
            autocomplete::class,
            'test',
            new \lang_string('course'),
            'testentity',
            'shortname'
        ))->set_options([
            $course1->shortname => $course1->fullname,
            $course2->shortname => $course2->fullname,
            $course3->shortname => $course3->fullname,
        ]);

        [$select, $params] = text::create($filter)->get_sql_filter([
            $filter->get_unique_identifier() . '_values' => $shortnames,
        ]);
        $fullnames = $DB->get_fieldset_select('course', 'fullname', $select, $params);
        $this->assertEqualsCanonicalizing($expected, $fullnames);

    }
}
