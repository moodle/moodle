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
 * Unit tests for user report filter
 *
 * @package     core_reportbuilder
 * @covers      \core_reportbuilder\local\filters\base
 * @covers      \core_reportbuilder\local\filters\user
 * @copyright   2021 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class user_test extends advanced_testcase {

    /**
     * Data provider for {@see test_get_sql_filter}
     *
     * @return array
     */
    public static function get_sql_filter_simple(): array {
        return [
            [user::USER_ANY, ['admin', 'guest', 'user01', 'user02']],
            [user::USER_CURRENT, ['user01']],
        ];
    }

    /**
     * Test getting filter SQL
     *
     * @param int $operator
     * @param string[] $expectedusernames
     *
     * @dataProvider get_sql_filter_simple
     */
    public function test_get_sql_filter(int $operator, array $expectedusernames): void {
        global $DB;

        $this->resetAfterTest();

        $user01 = $this->getDataGenerator()->create_user(['username' => 'user01']);
        $user02 = $this->getDataGenerator()->create_user(['username' => 'user02']);

        $this->setUser($user01);

        $filter = new filter(
            user::class,
            'test',
            new lang_string('yes'),
            'testentity',
            'id'
        );

        // Create instance of our filter, passing given operator.
        [$select, $params] = user::create($filter)->get_sql_filter([
            $filter->get_unique_identifier() . '_operator' => $operator,
        ]);

        $usernames = $DB->get_fieldset_select('user', 'username', $select, $params);
        $this->assertEqualsCanonicalizing($expectedusernames, $usernames);
    }

    /**
     * Test getting filter SQL using specific user selection operator/value
     */
    public function test_get_sql_filter_select_user(): void {
        global $DB;

        $this->resetAfterTest();

        $user01 = $this->getDataGenerator()->create_user(['username' => 'user01']);
        $user02 = $this->getDataGenerator()->create_user(['username' => 'user02']);

        $filter = new filter(
            user::class,
            'test',
            new lang_string('yes'),
            'testentity',
            'id'
        );

        // Create instance of our filter, passing given operator/value matching second user.
        [$select, $params] = user::create($filter)->get_sql_filter([
            $filter->get_unique_identifier() . '_operator' => user::USER_SELECT,
            $filter->get_unique_identifier() . '_value' => [$user02->id],
        ]);

        $usernames = $DB->get_fieldset_select('user', 'username', $select, $params);
        $this->assertEquals([$user02->username], $usernames);
    }
}
