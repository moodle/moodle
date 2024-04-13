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
 * Unit tests for boolean report filter
 *
 * @package     core_reportbuilder
 * @covers      \core_reportbuilder\local\filters\base
 * @covers      \core_reportbuilder\local\filters\boolean_select
 * @copyright   2021 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class boolean_select_test extends advanced_testcase {

    /**
     * Data provider for {@see test_get_sql_filter_simple}
     *
     * @return array
     */
    public static function get_sql_filter_simple_provider(): array {
        return [
            [boolean_select::ANY_VALUE, true],
            [boolean_select::CHECKED, true],
            [boolean_select::NOT_CHECKED, false],
        ];
    }

    /**
     * Test getting filter SQL
     *
     * @param int $operator
     * @param bool $expectuser
     *
     * @dataProvider get_sql_filter_simple_provider
     */
    public function test_get_sql_filter_simple(int $operator, bool $expectuser): void {
        global $DB;

        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user([
            'suspended' => 1,
        ]);

        $filter = new filter(
            boolean_select::class,
            'test',
            new lang_string('yes'),
            'testentity',
            'suspended'
        );

        // Create instance of our filter, passing given operator.
        [$select, $params] = boolean_select::create($filter)->get_sql_filter([
            $filter->get_unique_identifier() . '_operator' => $operator,
        ]);

        $usernames = $DB->get_fieldset_select('user', 'username', $select, $params);
        if ($expectuser) {
            $this->assertContains($user->username, $usernames);
        } else {
            $this->assertNotContains($user->username, $usernames);
        }
    }
}
