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
 * Unit tests for text report filter
 *
 * @package     core_reportbuilder
 * @covers      \core_reportbuilder\local\filters\base
 * @covers      \core_reportbuilder\local\filters\text
 * @copyright   2021 David Matamoros <davidmc@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class text_test extends advanced_testcase {

    /**
     * Data provider for {@see test_get_sql_filter_simple}
     *
     * @return array
     */
    public function get_sql_filter_simple_provider(): array {
        return [
            [text::ANY_VALUE, null, true],
            [text::CONTAINS, 'looking', true],
            [text::CONTAINS, 'sky', false],
            [text::DOES_NOT_CONTAIN, 'sky', true],
            [text::DOES_NOT_CONTAIN, 'looking', false],
            [text::IS_EQUAL_TO, "Hello, is it me you're looking for?", true],
            [text::IS_EQUAL_TO, 'I can see it in your eyes', false],
            [text::IS_NOT_EQUAL_TO, "Hello, is it me you're looking for?", false],
            [text::IS_NOT_EQUAL_TO, 'I can see it in your eyes', true],
            [text::STARTS_WITH, 'Hello', true],
            [text::STARTS_WITH, 'sunlight', false],
            [text::ENDS_WITH, 'looking for?', true],
            [text::ENDS_WITH, 'your heart', false],
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

        $course = $this->getDataGenerator()->create_course([
            'fullname' => "Hello, is it me you're looking for?",
        ]);

        $filter = new filter(
            text::class,
            'test',
            new lang_string('course'),
            'testentity',
            'fullname'
        );

        // Create instance of our filter, passing given operator.
        [$select, $params] = text::create($filter)->get_sql_filter([
            $filter->get_unique_identifier() . '_operator' => $operator,
            $filter->get_unique_identifier() . '_value' => $value,
        ]);

        $fullnames = $DB->get_fieldset_select('course', 'fullname', $select, $params);
        if ($expectmatch) {
            $this->assertContains($course->fullname, $fullnames);
        } else {
            $this->assertNotContains($course->fullname, $fullnames);
        }
    }

    /**
     * Data provider for {@see test_get_sql_filter_empty}
     *
     * @return array
     */
    public function get_sql_filter_empty_provider(): array {
        return [
            [text::IS_EMPTY, null, true],
            [text::IS_EMPTY, '', true],
            [text::IS_EMPTY, 'hola', false],
            [text::IS_NOT_EMPTY, null, false],
            [text::IS_NOT_EMPTY, '', false],
            [text::IS_NOT_EMPTY, 'hola', true],
        ];
    }

    /**
     * Test getting filter SQL using the {@see text::IS_EMPTY} and {@see text::IS_NOT_EMPTY} operators
     *
     * @param int $operator
     * @param string|null $profilefieldvalue
     * @param bool $expectmatch
     *
     * @dataProvider get_sql_filter_empty_provider
     */
    public function test_get_sql_filter_empty(int $operator, ?string $profilefieldvalue, bool $expectmatch): void {
        global $DB;

        $this->resetAfterTest();

        // We are using the user.moodlenetprofile field because it is nullable.
        $user = $this->getDataGenerator()->create_user([
            'moodlenetprofile' => $profilefieldvalue,
        ]);

        $filter = new filter(
            text::class,
            'test',
            new lang_string('user'),
            'testentity',
            'moodlenetprofile'
        );

        // Create instance of our filter, passing given operator.
        [$select, $params] = text::create($filter)->get_sql_filter([
            $filter->get_unique_identifier() . '_operator' => $operator,
        ]);

        $usernames = $DB->get_fieldset_select('user', 'username', $select, $params);
        if ($expectmatch) {
            $this->assertContains($user->username, $usernames);
        } else {
            $this->assertNotContains($user->username, $usernames);
        }
    }
}
