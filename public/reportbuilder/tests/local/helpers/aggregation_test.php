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

namespace core_reportbuilder\local\helpers;

use advanced_testcase;
use core_reportbuilder\local\aggregation\{base, groupconcat, max};
use core_reportbuilder\local\report\column;
use core\url;

/**
 * Unit tests for aggregation helper
 *
 * @package     core_reportbuilder
 * @covers      \core_reportbuilder\local\helpers\aggregation
 * @copyright   2025 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class aggregation_test extends advanced_testcase {
    /**
     * Test converting aggregation type name to full classpath
     */
    public function test_get_full_classpath(): void {
        $classpath = aggregation::get_full_classpath(max::get_class_name());
        $this->assertEquals('\\' . max::class, $classpath);
    }

    /**
     * Data provider for {@see test_valid}
     *
     * @return array[]
     */
    public static function valid_provider(): array {
        return [
            [max::class, true],
            [base::class, false],
            [url::class, false],
            ['invalid', false],
        ];
    }

    /**
     * Test validity of aggregation type classes
     *
     * @param string $classpath
     * @param bool $expected
     *
     * @dataProvider valid_provider
     */
    public function test_valid(string $classpath, bool $expected): void {
        $this->assertEquals($expected, aggregation::valid($classpath));
    }

    /**
     * Test retrieving all aggregation types
     */
    public function test_get_aggregations(): void {
        $aggregations = aggregation::get_aggregations();
        $this->assertCount(10, $aggregations);

        // Just assert single item from returned structure.
        $this->assertContains(max::class, $aggregations);
        $this->assertNotContains(base::class, $aggregations);
    }

    /**
     * Test retrieving aggregation types compatible with column
     */
    public function test_get_column_aggregations(): void {
        $aggregations = aggregation::get_column_aggregations(column::TYPE_TIMESTAMP);
        $this->assertCount(5, $aggregations);

        // Just assert single item from returned structure.
        $this->assertArrayHasKey(max::get_class_name(), $aggregations);
        $this->assertEquals(max::get_name(), $aggregations[max::get_class_name()]);

        // Group concatenation isn't compatible with a timestamp column.
        $this->assertArrayNotHasKey(groupconcat::get_class_name(), $aggregations);

        // Now exclude some aggregation types.
        $aggregations = aggregation::get_column_aggregations(column::TYPE_TIMESTAMP, [max::get_class_name()]);
        $this->assertCount(4, $aggregations);
        $this->assertArrayNotHasKey(max::get_class_name(), $aggregations);
    }
}
