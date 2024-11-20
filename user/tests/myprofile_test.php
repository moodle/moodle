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

namespace core_user;

defined('MOODLE_INTERNAL') || die();
global $CFG;
require_once($CFG->dirroot . "/user/tests/fixtures/myprofile_fixtures.php");

/**
 * Unit tests for core_user\output\myprofile
 *
 * @package   core_user
 * @category  test
 * @copyright 2015 onwards Ankit Agarwal
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later (5)
 */
class myprofile_test extends \advanced_testcase {
    /**
     * Test node::__construct().
     */
    public function test_node__construct(): void {
        $node = new \core_user\output\myprofile\node('parentcat', 'nodename',
                'nodetitle', 'after', 'www.google.com', 'description', new \pix_icon('i/course', ''), 'class1 class2');
        $this->assertSame('parentcat', $node->parentcat);
        $this->assertSame('nodename', $node->name);
        $this->assertSame('nodetitle', $node->title);
        $this->assertSame('after', $node->after);
        $url = new \moodle_url('www.google.com');
        $this->assertEquals($url, $node->url);
        $this->assertEquals(new \pix_icon('i/course', ''), $node->icon);
        $this->assertSame('class1 class2', $node->classes);
    }

    /**
     * Test category::node_add().
     */
    public function test_add_node(): void {
        $tree = new \core_user\output\myprofile\tree();
        $category = new \core_user\output\myprofile\category('category', 'categorytitle');

        $node = new \core_user\output\myprofile\node('category', 'nodename',
                'nodetitle', null, 'www.iAmaZombie.com', 'description');
        $category->add_node($node);
        $this->assertCount(1, $category->nodes);
        $node = new \core_user\output\myprofile\node('category', 'nodename2',
                'nodetitle', null, 'www.WorldisGonnaEnd.com', 'description');
        $category->add_node($node);
        $this->assertCount(2, $category->nodes);

        $node = new \core_user\output\myprofile\node('category', 'nodename3',
                'nodetitle', null, 'www.TeamBeardsFTW.com', 'description');
        $tree->add_node($node);
        $tree->add_category($category);
        $tree->sort_categories();
        $category = $tree->categories['category'];
        $this->assertCount(3, $category->nodes);
    }

    /**
     * Test category::__construct().
     */
    public function test_category__construct(): void {
        $category = new \core_user\output\myprofile\category('categoryname', 'title', 'after', 'class1 class2');
        $this->assertSame('categoryname', $category->name);
        $this->assertSame('title', $category->title);
        $this->assertSame('after', $category->after);
        $this->assertSame('class1 class2', $category->classes);
    }

    public function test_validate_after_order1(): void {
        $category = new \phpunit_fixture_myprofile_category('category', 'title', null);

        // Create nodes.
        $node1 = new \core_user\output\myprofile\node('category', 'node1', 'nodetitle', null, null, 'content');
        $node2 = new \core_user\output\myprofile\node('category', 'node2', 'nodetitle', 'node1', null, 'content');
        $node3 = new \core_user\output\myprofile\node('category', 'node3', 'nodetitle', 'node2', null, null);

        $category->add_node($node3);
        $category->add_node($node2);
        $category->add_node($node1);

        $this->expectException(\coding_exception::class);
        $category->validate_after_order();

    }

    public function test_validate_after_order2(): void {
        $category = new \phpunit_fixture_myprofile_category('category', 'title', null);

        // Create nodes.
        $node1 = new \core_user\output\myprofile\node('category', 'node1', 'nodetitle', null, null, null);
        $node2 = new \core_user\output\myprofile\node('category', 'node2', 'nodetitle', 'node1', null, 'content');
        $node3 = new \core_user\output\myprofile\node('category', 'node3', 'nodetitle', 'node2', null, null);

        $category->add_node($node3);
        $category->add_node($node2);
        $category->add_node($node1);

        $this->expectException(\coding_exception::class);
        $category->validate_after_order();

    }

    /**
     * Test category::find_nodes_after().
     */
    public function test_find_nodes_after(): void {
        $category = new \phpunit_fixture_myprofile_category('category', 'title', null);

        // Create nodes.
        $node1 = new \core_user\output\myprofile\node('category', 'node1', 'nodetitle', null);
        $node2 = new \core_user\output\myprofile\node('category', 'node2', 'nodetitle', 'node1');
        $node3 = new \core_user\output\myprofile\node('category', 'node3', 'nodetitle', 'node2');
        $node4 = new \core_user\output\myprofile\node('category', 'node4', 'nodetitle', 'node3');
        $node5 = new \core_user\output\myprofile\node('category', 'node5', 'nodetitle', 'node3');
        $node6 = new \core_user\output\myprofile\node('category', 'node6', 'nodetitle', 'node1');

        // Add the nodes in random order.
        $category->add_node($node3);
        $category->add_node($node2);
        $category->add_node($node4);
        $category->add_node($node1);
        $category->add_node($node5);
        $category->add_node($node6);

        // After node 1 we should have node2 - node3 - node4 - node5 - node6.
        $return = $category->find_nodes_after($node1);
        $this->assertCount(5, $return);
        $node = array_shift($return);
        $this->assertEquals($node2, $node);
        $node = array_shift($return);
        $this->assertEquals($node3, $node);
        $node = array_shift($return);
        $this->assertEquals($node4, $node);
        $node = array_shift($return);
        $this->assertEquals($node5, $node);
        $node = array_shift($return);
        $this->assertEquals($node6, $node);

        // Last check also verifies calls for all subsequent nodes, still do some random checking.
        $return = $category->find_nodes_after($node6);
        $this->assertCount(0, $return);
        $return = $category->find_nodes_after($node3);
        $this->assertCount(2, $return);
    }

    /**
     * Test category::sort_nodes().
     */
    public function test_sort_nodes1(): void {
        $category = new \phpunit_fixture_myprofile_category('category', 'title', null);

        // Create nodes.
        $node1 = new \core_user\output\myprofile\node('category', 'node1', 'nodetitle', null);
        $node2 = new \core_user\output\myprofile\node('category', 'node2', 'nodetitle', 'node1');
        $node3 = new \core_user\output\myprofile\node('category', 'node3', 'nodetitle', 'node2');
        $node4 = new \core_user\output\myprofile\node('category', 'node4', 'nodetitle', 'node3');
        $node5 = new \core_user\output\myprofile\node('category', 'node5', 'nodetitle', 'node3');
        $node6 = new \core_user\output\myprofile\node('category', 'node6', 'nodetitle', 'node1');

        // Add the nodes in random order.
        $category->add_node($node3);
        $category->add_node($node2);
        $category->add_node($node4);
        $category->add_node($node1);
        $category->add_node($node5);
        $category->add_node($node6);

        // After node 1 we should have node2 - node3 - node4 - node5 - node6.
        $category->sort_nodes();
        $nodes = $category->nodes;
        $this->assertCount(6, $nodes);
        $node = array_shift($nodes);
        $this->assertEquals($node1, $node);
        $node = array_shift($nodes);
        $this->assertEquals($node2, $node);
        $node = array_shift($nodes);
        $this->assertEquals($node3, $node);
        $node = array_shift($nodes);
        $this->assertEquals($node4, $node);
        $node = array_shift($nodes);
        $this->assertEquals($node5, $node);
        $node = array_shift($nodes);
        $this->assertEquals($node6, $node);

        // Last check also verifies calls for all subsequent nodes, still do some random checking.
        $return = $category->find_nodes_after($node6);
        $this->assertCount(0, $return);
        $return = $category->find_nodes_after($node3);
        $this->assertCount(2, $return);

        // Add a node with invalid 'after' and make sure an exception is thrown.
        $node7 = new \core_user\output\myprofile\node('category', 'node7', 'nodetitle', 'noderandom');
        $category->add_node($node7);
        $this->expectException(\coding_exception::class);
        $category->sort_nodes();
    }

    /**
     * Test category::sort_nodes() with a mix of content and non content nodes.
     */
    public function test_sort_nodes2(): void {
        $category = new \phpunit_fixture_myprofile_category('category', 'title', null);

        // Create nodes.
        $node1 = new \core_user\output\myprofile\node('category', 'node1', 'nodetitle', null, null, 'content');
        $node2 = new \core_user\output\myprofile\node('category', 'node2', 'nodetitle', 'node1', null, 'content');
        $node3 = new \core_user\output\myprofile\node('category', 'node3', 'nodetitle', null);
        $node4 = new \core_user\output\myprofile\node('category', 'node4', 'nodetitle', 'node3');
        $node5 = new \core_user\output\myprofile\node('category', 'node5', 'nodetitle', 'node3');
        $node6 = new \core_user\output\myprofile\node('category', 'node6', 'nodetitle', 'node1', null, 'content');

        // Add the nodes in random order.
        $category->add_node($node3);
        $category->add_node($node2);
        $category->add_node($node4);
        $category->add_node($node1);
        $category->add_node($node5);
        $category->add_node($node6);

        // After node 1 we should have node2 - node6 - node3 - node4 - node5.
        $category->sort_nodes();
        $nodes = $category->nodes;
        $this->assertCount(6, $nodes);
        $node = array_shift($nodes);
        $this->assertEquals($node1, $node);
        $node = array_shift($nodes);
        $this->assertEquals($node2, $node);
        $node = array_shift($nodes);
        $this->assertEquals($node6, $node);
        $node = array_shift($nodes);
        $this->assertEquals($node3, $node);
        $node = array_shift($nodes);
        $this->assertEquals($node4, $node);
        $node = array_shift($nodes);
        $this->assertEquals($node5, $node);
    }

    /**
     * Test tree::add_node().
     */
    public function test_tree_add_node(): void {
        $tree = new \phpunit_fixture_myprofile_tree();
        $node1 = new \core_user\output\myprofile\node('category', 'node1', 'nodetitle');
        $tree->add_node($node1);
        $nodes = $tree->nodes;
        $node = array_shift($nodes);
        $this->assertEquals($node1, $node);

        // Can't add node with same name.
        $this->expectException(\coding_exception::class);
        $tree->add_node($node1);
    }

    /**
     * Test tree::add_category().
     */
    public function test_tree_add_category(): void {
        $tree = new \phpunit_fixture_myprofile_tree();
        $category1 = new \core_user\output\myprofile\category('category', 'title');
        $tree->add_category($category1);
        $categories = $tree->categories;
        $category = array_shift($categories);
        $this->assertEquals($category1, $category);

        // Can't add node with same name.
        $this->expectException(\coding_exception::class);
        $tree->add_category($category1);
    }

    /**
     * Test tree::find_categories_after().
     */
    public function test_find_categories_after(): void {
        $tree = new \phpunit_fixture_myprofile_tree('category', 'title', null);

        // Create categories.
        $category1 = new \core_user\output\myprofile\category('category1', 'category1', null);
        $category2 = new \core_user\output\myprofile\category('category2', 'category2', 'category1');
        $category3 = new \core_user\output\myprofile\category('category3', 'category3', 'category2');
        $category4 = new \core_user\output\myprofile\category('category4', 'category4', 'category3');
        $category5 = new \core_user\output\myprofile\category('category5', 'category5', 'category3');
        $category6 = new \core_user\output\myprofile\category('category6', 'category6', 'category1');

        // Add the categories in random order.
        $tree->add_category($category3);
        $tree->add_category($category2);
        $tree->add_category($category4);
        $tree->add_category($category1);
        $tree->add_category($category5);
        $tree->add_category($category6);

        // After category 1 we should have category2 - category3 - category4 - category5 - category6.
        $return = $tree->find_categories_after($category1);
        $this->assertCount(5, $return);
        $category = array_shift($return);
        $this->assertEquals($category2, $category);
        $category = array_shift($return);
        $this->assertEquals($category3, $category);
        $category = array_shift($return);
        $this->assertEquals($category4, $category);
        $category = array_shift($return);
        $this->assertEquals($category5, $category);
        $category = array_shift($return);
        $this->assertEquals($category6, $category);

        // Last check also verifies calls for all subsequent categories, still do some random checking.
        $return = $tree->find_categories_after($category6);
        $this->assertCount(0, $return);
        $return = $tree->find_categories_after($category3);
        $this->assertCount(2, $return);
    }

    /**
     * Test tree::sort_categories().
     */
    public function test_sort_categories(): void {
        $tree = new \phpunit_fixture_myprofile_tree('category', 'title', null);

        // Create categories.
        $category1 = new \core_user\output\myprofile\category('category1', 'category1', null);
        $category2 = new \core_user\output\myprofile\category('category2', 'category2', 'category1');
        $category3 = new \core_user\output\myprofile\category('category3', 'category3', 'category2');
        $category4 = new \core_user\output\myprofile\category('category4', 'category4', 'category3');
        $category5 = new \core_user\output\myprofile\category('category5', 'category5', 'category3');
        $category6 = new \core_user\output\myprofile\category('category6', 'category6', 'category1');

        // Add the categories in random order.
        $tree->add_category($category3);
        $tree->add_category($category2);
        $tree->add_category($category4);
        $tree->add_category($category1);
        $tree->add_category($category5);
        $tree->add_category($category6);

        // After category 1 we should have category2 - category3 - category4 - category5 - category6.
        $tree->sort_categories();
        $categories = $tree->categories;
        $this->assertCount(6, $categories);
        $category = array_shift($categories);
        $this->assertEquals($category1, $category);
        $category = array_shift($categories);
        $this->assertEquals($category2, $category);
        $category = array_shift($categories);
        $this->assertEquals($category3, $category);
        $category = array_shift($categories);
        $this->assertEquals($category4, $category);
        $category = array_shift($categories);
        $this->assertEquals($category5, $category);
        $category = array_shift($categories);
        $this->assertEquals($category6, $category);

        // Can't add category with same name.
        $this->expectException(\coding_exception::class);
        $tree->add_category($category1);
    }
}
