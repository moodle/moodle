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
use lang_string;
use core_reportbuilder\local\report\filter;

/**
 * Unit tests for select report filter
 *
 * @package     core_reportbuilder
 * @covers      \core_reportbuilder\local\filters\base
 * @covers      \core_reportbuilder\local\filters\select
 * @copyright   2021 David Matamoros <davidmc@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class select_test extends advanced_testcase {

    /**
     * Data provider for {@see test_get_sql_filter_simple}
     *
     * @return array
     */
    public static function get_sql_filter_simple_provider(): array {
        return [
            [select::ANY_VALUE, null, true],
            [select::EQUAL_TO, 'starwars', true],
            [select::EQUAL_TO, 'mandalorian', false],
            [select::NOT_EQUAL_TO, 'starwars', false],
            [select::NOT_EQUAL_TO, 'mandalorian', true],
        ];
    }

    /**
     * Test getting filter SQL
     *
     * @param int $operator
     * @param string|null $value
     * @param bool $expectmatch
     *
     * @dataProvider get_sql_filter_simple_provider
     */
    public function test_get_sql_filter_simple(int $operator, ?string $value, bool $expectmatch): void {
        global $DB;

        $this->resetAfterTest();

        $course1 = $this->getDataGenerator()->create_course([
            'fullname' => "May the course be with you",
            'shortname' => 'starwars',
        ]);
        $course2 = $this->getDataGenerator()->create_course([
            'fullname' => "This is the course",
            'shortname' => 'mandalorian',
        ]);

        $filter = (new filter(
            select::class,
            'test',
            new lang_string('course'),
            'testentity',
            'shortname'
        ))->set_options([
            $course1->shortname => $course1->fullname,
            $course2->shortname => $course2->fullname,
        ]);

        // Create instance of our filter, passing given operator.
        [$select, $params] = select::create($filter)->get_sql_filter([
            $filter->get_unique_identifier() . '_operator' => $operator,
            $filter->get_unique_identifier() . '_value' => $value,
        ]);

        $fullnames = $DB->get_fieldset_select('course', 'fullname', $select, $params);
        if ($expectmatch) {
            $this->assertContains($course1->fullname, $fullnames);
        } else {
            $this->assertNotContains($course1->fullname, $fullnames);
        }
    }
}
