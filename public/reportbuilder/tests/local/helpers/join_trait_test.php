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

/**
 * Unit tests for the join trait
 *
 * @package     core_reportbuilder
 * @copyright   2024 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace core_reportbuilder\local\helpers;

use advanced_testcase;

defined('MOODLE_INTERNAL') || die();

/**
 * Unit tests for the join trait
 *
 * @package     core_reportbuilder
 * @covers      \core_reportbuilder\local\helpers\join_trait
 * @copyright   2024 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class join_trait_test extends advanced_testcase {
    /**
     * Test adding single join
     */
    public function test_add_join(): void {
        $trait = new join_trait_mock();
        $trait->add_join('JOIN {test} t ON t.id = a.id');

        $this->assertEquals(['JOIN {test} t ON t.id = a.id'], $trait->get_joins());
    }

    /**
     * Test adding single join multiple times
     */
    public function test_add_join_multiple(): void {
        $trait = new join_trait_mock();

        // Add multiple joins, two of which are duplicates.
        $trait->add_join('JOIN {test} t1 ON t1.id = a.id')
            ->add_join('JOIN {test} t2 ON t2.id = b.id')
            ->add_join('JOIN {test} t1 ON t1.id = a.id');

        // The duplicated join is normalised away.
        $this->assertEquals([
            'JOIN {test} t1 ON t1.id = a.id',
            'JOIN {test} t2 ON t2.id = b.id',
        ], $trait->get_joins());
    }

    /**
     * Test adding multiple joins
     */
    public function test_add_joins(): void {
        $trait = new join_trait_mock();

        // Add multiple joins, two of which are duplicates.
        $trait->add_joins([
            'JOIN {test} t1 ON t1.id = a.id',
            'JOIN {test} t2 ON t2.id = b.id',
            'JOIN {test} t1 ON t1.id = a.id',
        ]);

        // The duplicated join is normalised away.
        $this->assertEquals([
            'JOIN {test} t1 ON t1.id = a.id',
            'JOIN {test} t2 ON t2.id = b.id',
        ], $trait->get_joins());
    }

    /**
     * Test prepending single join before existing joins
     */
    public function test_prepend_join(): void {
        $trait = new join_trait_mock();

        $trait->add_join('JOIN {test} t2 ON t2.id = b.id');
        $trait->prepend_join('JOIN {test} t1 ON t1.id = a.id');

        $this->assertEquals([
            'JOIN {test} t1 ON t1.id = a.id',
            'JOIN {test} t2 ON t2.id = b.id',
        ], $trait->get_joins());
    }

    /**
     * Test prepending joins before existing joins
     */
    public function test_prepend_joins(): void {
        $trait = new join_trait_mock();

        // First add some element-specific joins.
        $trait->add_join('JOIN {test} t3 ON t3.id = c.id');
        $trait->add_join('JOIN {test} t4 ON t4.id = d.id');

        // Prepend entity joins, which should appear before the existing ones.
        $trait->prepend_joins([
            'JOIN {test} t1 ON t1.id = a.id',
            'JOIN {test} t2 ON t2.id = b.id',
        ]);

        $this->assertEquals([
            'JOIN {test} t1 ON t1.id = a.id',
            'JOIN {test} t2 ON t2.id = b.id',
            'JOIN {test} t3 ON t3.id = c.id',
            'JOIN {test} t4 ON t4.id = d.id',
        ], $trait->get_joins());
    }

    /**
     * Test prepending joins with duplicates
     */
    public function test_prepend_joins_duplicates(): void {
        $trait = new join_trait_mock();

        // Add some existing joins.
        $trait->add_join('JOIN {test} t1 ON t1.id = a.id');
        $trait->add_join('JOIN {test} t2 ON t2.id = b.id');

        // Prepend joins including a duplicate.
        $trait->prepend_joins([
            'JOIN {test} t0 ON t0.id = z.id',
            'JOIN {test} t1 ON t1.id = a.id',
        ]);

        // Initial t0 is prepended, duplicate t1 is deduplicated (first occurrence wins).
        $this->assertEquals([
            'JOIN {test} t0 ON t0.id = z.id',
            'JOIN {test} t1 ON t1.id = a.id',
            'JOIN {test} t2 ON t2.id = b.id',
        ], $trait->get_joins());
    }
}

/**
 * Simple implementation of a class using the trait
 */
final class join_trait_mock {
    use join_trait;
}
