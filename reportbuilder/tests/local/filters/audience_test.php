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
use core_reportbuilder_generator;
use core_reportbuilder\local\report\filter;
use core_reportbuilder\reportbuilder\audience\manual;
use core_user\reportbuilder\datasource\users;

/**
 * Unit tests for report audience filter
 *
 * @package     core_reportbuilder
 * @covers      \core_reportbuilder\local\filters\audience
 * @copyright   2024 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class audience_test extends advanced_testcase {

    /**
     * Data provider for {@see test_get_sql_filter}
     *
     * @return array
     */
    public static function get_sql_filter_provider(): array {
        return [
            [select::ANY_VALUE, null, ['user1', 'user2', 'admin', 'guest']],
            [select::EQUAL_TO, 'audience1', ['user1']],
            [select::EQUAL_TO, 'audience2', ['user2']],
            [select::NOT_EQUAL_TO, 'audience1', ['user2', 'admin', 'guest']],
            [select::NOT_EQUAL_TO, 'audience2', ['user1', 'admin', 'guest']],
        ];
    }

    /**
     * Test getting filter SQL
     *
     * @param int $operator
     * @param string|null $audiencename
     * @param string[] $expected
     *
     * @dataProvider get_sql_filter_provider
     */
    public function test_get_sql_filter(int $operator, ?string $audiencename, array $expected): void {
        global $DB;

        $this->resetAfterTest();

        $userone = $this->getDataGenerator()->create_user(['username' => 'user1']);
        $usertwo = $this->getDataGenerator()->create_user(['username' => 'user2']);

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'My report', 'source' => users::class]);

        // Store created audiences in a lookup table so they can be referenced by name.
        $audiences = [
            'audience1' => $generator->create_audience([
                'reportid' => $report->get('id'),
                'classname' => manual::class,
                'configdata' => [
                    'users' => [$userone->id],
                ],
            ]),
            'audience2' => $generator->create_audience([
                'reportid' => $report->get('id'),
                'classname' => manual::class,
                'configdata' => [
                    'users' => [$usertwo->id],
                ],
            ]),
        ];

        $audienceid = 0;
        if (array_key_exists($audiencename, $audiences)) {
            $audienceid = $audiences[$audiencename]->get_persistent()->get('id');
        }

        $filter = (new filter(
            audience::class,
            'test',
            new lang_string('yes'),
            'testentity',
            'id'
        ))->set_options([
            'reportid' => $report->get('id'),
        ]);

        // Create instance of our filter, passing given operator.
        [$select, $params] = audience::create($filter)->get_sql_filter([
            $filter->get_unique_identifier() . '_operator' => $operator,
            $filter->get_unique_identifier() . '_value' => $audienceid,
        ]);

        $usernames = $DB->get_fieldset_select('user', 'username', $select, $params);
        $this->assertEqualsCanonicalizing($expected, $usernames);
    }
}
