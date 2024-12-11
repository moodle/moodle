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
 * Unit tests for cohort report filter
 *
 * @package     core_reportbuilder
 * @covers      \core_reportbuilder\local\filters\cohort
 * @copyright   2024 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class cohort_test extends advanced_testcase {

    /**
     * Data provider for {@see test_get_sql_filter}
     *
     * @return array[]
     */
    public static function get_sql_filter_provider(): array {
        return [
            'Empty' => [
                [],
                ['C1', 'C2', 'C3'],
            ],
            'Non-existing' => [
                [-1],
                [],
            ],
            'Single cohort' => [
                ['C1'],
                ['C1'],
            ],
            'Multiple cohorts' => [
                ['C1', 'C2'],
                ['C1', 'C2'],
            ],
        ];
    }

    /**
     * Test getting filter SQL
     *
     * @param int[]|string[] $values
     * @param string[] $expectcohorts
     *
     * @dataProvider get_sql_filter_provider
     */
    public function test_get_sql_filter(array $values, array $expectcohorts): void {
        global $DB;

        $this->resetAfterTest();

        $this->getDataGenerator()->create_cohort(['name' => 'C1']);
        $this->getDataGenerator()->create_cohort(['name' => 'C2']);
        $this->getDataGenerator()->create_cohort(['name' => 'C3']);

        // Create cohort lookup for convenience, transform values that refer to cohorts by name, to their ID.
        $cohortmap = $DB->get_records_menu(table: 'cohort', fields: 'name, id');
        $values = array_map(static function(int|string $value) use ($cohortmap): int {
            if (is_numeric($value)) {
                return $value;
            }
            return (int) $cohortmap[$value];
        }, $values);

        $filter = new filter(
            cohort::class,
            'test',
            new lang_string('yes'),
            'testentity',
            'id'
        );

        // Create instance of our filter, passing given values.
        [$select, $params] = cohort::create($filter)->get_sql_filter([
            $filter->get_unique_identifier() . '_values' => $values,
        ]);

        $cohorts = $DB->get_fieldset_select('cohort', 'name', $select, $params);
        $this->assertEqualsCanonicalizing($expectcohorts, $cohorts);
    }
}
