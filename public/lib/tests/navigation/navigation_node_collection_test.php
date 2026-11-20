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

    public function test_type_and_remove_with_normalized_index(): void {
        $navigationnodecollection = new navigation_node_collection();
        $node = new navigation_node('Test node');
        $navigationnodecollection->add($node);
        $this->assertDebuggingCalled('Navigation node add: Node key should not be null');

        $this->assertSame($node, $navigationnodecollection->get('', ''));
        $this->assertSame($node, $navigationnodecollection->get(null, null));
        $this->assertSame($node, $navigationnodecollection->find('', ''));
        $this->assertArrayHasKey('', $navigationnodecollection->type(''));
        $this->assertSame($node, $navigationnodecollection->type('')['']);

        $this->assertTrue($navigationnodecollection->remove('', ''));
        $this->assertCount(0, $navigationnodecollection);
        $this->assertSame([], $navigationnodecollection->get_key_list());
        $this->assertFalse($navigationnodecollection->get('', ''));
        $this->assertSame([], $navigationnodecollection->type(''));

        $node = new navigation_node(['text' => 'Another test node', 'key' => '']);
        $navigationnodecollection->add($node);
        $this->assertTrue($navigationnodecollection->remove(''));
        $this->assertCount(0, $navigationnodecollection);
    }

    public function test_get_and_find_with_empty_string_key(): void {
        $navigationnodecollection = new navigation_node_collection();
        $node = new navigation_node(['text' => 'Test node', 'key' => '', 'type' => navigation_node::TYPE_CUSTOM]);
        $navigationnodecollection->add($node);

        $this->assertSame($node, $navigationnodecollection->get('', navigation_node::TYPE_CUSTOM));
        $this->assertSame($node, $navigationnodecollection->find('', navigation_node::TYPE_CUSTOM));
    }

    public function test_remove_missing_node_does_not_decrement_count(): void {
        $navigationnodecollection = new navigation_node_collection();
        $node = new navigation_node(['text' => 'Test node', 'key' => 'demo', 'type' => navigation_node::TYPE_CUSTOM]);
        $navigationnodecollection->add($node);

        $this->assertFalse($navigationnodecollection->remove('missing', navigation_node::TYPE_CUSTOM));
        $this->assertCount(1, $navigationnodecollection);
    }
}
