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
 * @covers      \core_reportbuilder\local\filters\course_selector
 * @copyright   2021 David Matamoros <davidmc@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_selector_test extends advanced_testcase {

    /**
     * Test getting filter SQL
     */
    public function test_get_sql_filter(): void {
        global $DB;

        $this->resetAfterTest();

        $course1 = $this->getDataGenerator()->create_course([
            'fullname' => "Time travel",
        ]);

        $course2 = $this->getDataGenerator()->create_course([
            'fullname' => "Quantum computing",
        ]);

        $course3 = $this->getDataGenerator()->create_course([
            'fullname' => "Space travel",
        ]);

        $filter = new filter(
            course_selector::class,
            'test',
            new \lang_string('course'),
            'testentity',
            'id'
        );

        // Create instance of our filter, passing given courses ids.
        [$select, $params] = text::create($filter)->get_sql_filter([
            $filter->get_unique_identifier() . '_values' => [$course1->id, $course3->id],
        ]);
        $fullnames = $DB->get_fieldset_select('course', 'fullname', $select, $params);
        $this->assertEqualsCanonicalizing(['Time travel', 'Space travel'], $fullnames);

        // Test without passing any course id.
        [$select, $params] = text::create($filter)->get_sql_filter([
            $filter->get_unique_identifier() . '_values' => [],
        ]);
        $fullnames = $DB->get_fieldset_select('course', 'fullname', $select, $params);
        $this->assertEqualsCanonicalizing(['Time travel', 'Quantum computing', 'Space travel', 'PHPUnit test site'], $fullnames);
    }
}
