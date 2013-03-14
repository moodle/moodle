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
 * Unit tests for those parts of adminlib.php that implement the admin tree
 * functionality.
 *
 * @package     core
 * @category    test
 * @copyright   2013 David Mudrak <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir.'/adminlib.php');

/**
 * Provides the unit tests for admin tree functionality.
 */
class admintree_testcase extends advanced_testcase {

    /**
     * Adding nodes into the admin tree
     */
    public function test_add_nodes() {

        $tree = new admin_root(true);
        $tree->add('root', $one = new admin_category('one', 'One'));
        $tree->add('root', new admin_category('three', 'Three'));
        $tree->add('one', new admin_category('one-one', 'One-one'));
        $tree->add('one', new admin_category('one-three', 'One-three'));

        // Check the order of nodes in the root.
        $map = array();
        foreach ($tree->children as $child) {
            $map[] = $child->name;
        }
        $this->assertEquals(array('one', 'three'), $map);

        // Insert a node into the middle.
        $tree->add('root', new admin_category('two', 'Two'), 'three');
        $map = array();
        foreach ($tree->children as $child) {
            $map[] = $child->name;
        }
        $this->assertEquals(array('one', 'two', 'three'), $map);

        // Non-existing sibling.
        $tree->add('root', new admin_category('four', 'Four'), 'five');
        $this->assertDebuggingCalled('Sibling five not found', DEBUG_DEVELOPER);

        $tree->add('root', new admin_category('five', 'Five'));
        $map = array();
        foreach ($tree->children as $child) {
            $map[] = $child->name;
        }
        $this->assertEquals(array('one', 'two', 'three', 'four', 'five'), $map);

        // Insert a node into the middle of the subcategory
        $tree->add('one', new admin_category('one-two', 'One-two'), 'one-three');
        $map = array();
        foreach ($one->children as $child) {
            $map[] = $child->name;
        }
        $this->assertEquals(array('one-one', 'one-two', 'one-three'), $map);

        // Check just siblings, not parents or children.
        $tree->add('one', new admin_category('one-four', 'One-four'), 'one');
        $this->assertDebuggingCalled('Sibling one not found', DEBUG_DEVELOPER);

        $tree->add('root', new admin_category('six', 'Six'), 'one-two');
        $this->assertDebuggingCalled('Sibling one-two not found', DEBUG_DEVELOPER);

        // Me! Me! I wanna be first!
        $tree->add('root', new admin_externalpage('zero', 'Zero', 'http://foo.bar'), 'one');
        $map = array();
        foreach ($tree->children as $child) {
            $map[] = $child->name;
        }
        $this->assertEquals(array('zero', 'one', 'two', 'three', 'four', 'five', 'six'), $map);
    }

    /**
     * @expectedException coding_exception
     */
    public function test_add_nodes_before_invalid1() {
        $tree = new admin_root(true);
        $tree->add('root', new admin_externalpage('foo', 'Foo', 'http://foo.bar'), array('moodle:site/config'));
    }

    /**
     * @expectedException coding_exception
     */
    public function test_add_nodes_before_invalid2() {
        $tree = new admin_root(true);
        $tree->add('root', new admin_category('bar', 'Bar'), '');
    }
}
