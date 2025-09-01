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

namespace core\navigation;

use core\tests\navigation\navigation_testcase;

/**
 * Tests for navigation_node_collection.
 *
 * @package    core
 * @category   test
 * @copyright  2025 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(navigation_node_collection::class)]
final class navigation_node_collection_test extends navigation_testcase {
    public function test_navigation_node_collection_remove_with_no_type(): void {
        $navigationnodecollection = new navigation_node_collection();
        $node = $this->setup_node();
        $node->key = 100;

        // Test it's empty.
        $this->assertEquals(0, count($navigationnodecollection->get_key_list()));

        // Add a node.
        $navigationnodecollection->add($node);

        // Test it's not empty.
        $this->assertEquals(1, count($navigationnodecollection->get_key_list()));

        // Remove a node - passing key only!
        $this->assertTrue($navigationnodecollection->remove(100));

        // Test it's empty again!
        $this->assertEquals(0, count($navigationnodecollection->get_key_list()));
    }

    public function test_navigation_node_collection_remove_with_type(): void {
        $navigationnodecollection = new navigation_node_collection();
        $node = $this->setup_node();
        $node->key = 100;

        // Test it's empty.
        $this->assertEquals(0, count($navigationnodecollection->get_key_list()));

        // Add a node.
        $navigationnodecollection->add($node);

        // Test it's not empty.
        $this->assertEquals(1, count($navigationnodecollection->get_key_list()));

        // Remove a node - passing type.
        $this->assertTrue($navigationnodecollection->remove(100, 1));

        // Test it's empty again!
        $this->assertEquals(0, count($navigationnodecollection->get_key_list()));
    }
}
