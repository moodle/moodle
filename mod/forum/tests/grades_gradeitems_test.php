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
 * Unit tests for mod_forum\grades\gradeitems.
 *
 * @package   mod_forum
 * @category  test
 * @copyright 2019 Andrew Nicols <andrew@nicols.co.uk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

declare(strict_types = 1);

namespace tests\mod_forum\grades;

use advanced_testcase;
use core_grades\component_gradeitems;
use coding_exception;

/**
 * Unit tests for mod_forum\grades\gradeitems.
 *
 * @package   mod_forum
 * @category  test
 * @copyright 2019 Andrew Nicols <andrew@nicols.co.uk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class gradeitems_test extends advanced_testcase {

    /**
     * Ensure that the mappings are present and correct.
     */
    public function test_get_itemname_mapping_for_component(): void {
        $mappings = component_gradeitems::get_itemname_mapping_for_component('mod_forum');
        $this->assertIsArray($mappings);
        $this->assertCount(2, $mappings);
        $this->assertArraySubset([0 => 'rating'], $mappings);
        $this->assertArraySubset([1 => 'forum'], $mappings);
    }

    /**
     * Ensure that the advanced grading only applies to the relevant items.
     */
    public function test_get_advancedgrading_itemnames_for_component(): void {
        $mappings = component_gradeitems::get_advancedgrading_itemnames_for_component('mod_forum');
        $this->assertIsArray($mappings);
        $this->assertCount(1, $mappings);
        $this->assertContains('forum', $mappings);
        $this->assertNotContains('rating', $mappings);
    }

    /**
     * Ensure that the correct items are identified by is_advancedgrading_itemname.
     *
     * @dataProvider is_advancedgrading_itemname_provider
     * @param string $itemname
     * @param bool $expected
     */
    public function test_is_advancedgrading_itemname(string $itemname, bool $expected): void {
        $this->assertEquals(
            $expected,
            component_gradeitems::is_advancedgrading_itemname('mod_forum', $itemname)
        );
    }

    /**
     * Data provider for tests of is_advancedgrading_itemname.
     *
     * @return array
     */
    public function is_advancedgrading_itemname_provider(): array {
        return [
            'rating is not advanced' => [
                'rating',
                false,
            ],
            'Whole forum grading is advanced' => [
                'forum',
                true,
            ],
        ];
    }
}
