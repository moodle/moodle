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
use core\lang_string;
use core_reportbuilder\local\report\filter;

/**
 * Unit tests for text report filter
 *
 * @package     core_reportbuilder
 * @covers      \core_reportbuilder\local\filters\base
 * @covers      \core_reportbuilder\local\filters\text
 * @copyright   2021 David Matamoros <davidmc@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class text_test extends advanced_testcase {
    /**
     * Data provider for {@see test_get_sql_filter_simple}
     *
     * @return array
     */
    public static function get_sql_filter_simple_provider(): array {
        return [
            [text::ANY_VALUE, 'Looking for', null, true],
            [text::IS_EQUAL_TO, 'Looking for', 'Looking for', true],
            [text::IS_EQUAL_TO, 'Looking for', 'Your eyes', false],
            [text::IS_NOT_EQUAL_TO, 'Looking for', 'Looking for', false],
            [text::IS_NOT_EQUAL_TO, 'Looking for', 'Your eyes', true],
            [text::STARTS_WITH, 'Looking for', 'Looking', true],
            [text::STARTS_WITH, 'Looking for', 'Your', false],
            [text::ENDS_WITH, 'Looking for', 'for', true],
            [text::ENDS_WITH, 'Looking for', 'eyes', false],

            // Contains content.
            [text::CONTAINS, 'Looking for', 'king', true],
            [text::CONTAINS, 'Looking for', 'sky', false],
            [text::CONTAINS, 'Looking for', 'L*king', true],
            [text::CONTAINS, 'L*oking for', 'L\\*oking', true],
            [text::CONTAINS, 'Looking for', 'L\\*king', false],
            [text::CONTAINS, 'Looking for', 'L??king', true],
            [text::CONTAINS, 'L?oking for', 'L\\?oking', true],
            [text::CONTAINS, 'Looking for', 'L\\?oking', false],
            [text::DOES_NOT_CONTAIN, 'Looking for', 'king', false],
            [text::DOES_NOT_CONTAIN, 'Looking for', 'sky', true],
            [text::DOES_NOT_CONTAIN, 'Looking for', 'L*king', false],
            [text::DOES_NOT_CONTAIN, 'L*oking for', 'L\\*oking', false],
            [text::DOES_NOT_CONTAIN, 'Looking for', 'L\\*king', true],
            [text::DOES_NOT_CONTAIN, 'Looking for', 'L??king', false],
            [text::DOES_NOT_CONTAIN, 'L?oking for', 'L\\?oking', false],
            [text::DOES_NOT_CONTAIN, 'Looking for', 'L\\?oking', true],

            // Empty content.
            [text::IS_EMPTY, null, null, true],
            [text::IS_EMPTY, '', null, true],
            [text::IS_EMPTY, 'Looking for', null, false],
            [text::IS_NOT_EMPTY, null, null, false],
            [text::IS_NOT_EMPTY, '', null, false],
            [text::IS_NOT_EMPTY, 'Looking for', null, true],

            // Ensure whitespace is trimmed.
            [text::CONTAINS, 'Looking for', '   Looking for   ', true],
            [text::IS_EQUAL_TO, 'Looking for', '  Looking for  ', true],
            [text::STARTS_WITH, 'Looking for', '  Looking  ', true],
            [text::ENDS_WITH, 'Looking for', '  for  ', true],
        ];
    }

    /**
     * Test getting filter SQL
     *
     * @param int $operator
     * @param string|null $fieldvalue
     * @param string|null $filtervalue
     * @param bool $expectmatch
     *
     * @dataProvider get_sql_filter_simple_provider
     */
    public function test_get_sql_filter_simple(
        int $operator,
        ?string $fieldvalue,
        ?string $filtervalue,
        bool $expectmatch,
    ): void {
        global $DB;

        $this->resetAfterTest();

        // We are using the pdfexportfont field because it is nullable.
        $course = $this->getDataGenerator()->create_course([
            'pdfexportfont' => $fieldvalue,
        ]);

        $filter = new filter(
            text::class,
            'test',
            new lang_string('course'),
            'testentity',
            'pdfexportfont',
        );

        // Create instance of our filter, passing given operator.
        [$select, $params] = text::create($filter)->get_sql_filter([
            $filter->get_unique_identifier() . '_operator' => $operator,
            $filter->get_unique_identifier() . '_value' => $filtervalue,
        ]);

        $fullnames = $DB->get_fieldset_select('course', 'fullname', $select, $params);
        if ($expectmatch) {
            $this->assertContains($course->fullname, $fullnames);
        } else {
            $this->assertNotContains($course->fullname, $fullnames);
        }
    }
}
