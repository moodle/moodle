<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

namespace core_admin\setting\tree;

use core\context\system;
use core\exception\coding_exception;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversFunction;

/**
 * Unit tests for the admin tree root class.
 *
 * @package    core_admin
 * @category   test
 * @copyright  2013 David Mudrak <david@moodle.com>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[CoversClass(root::class)]
#[CoversClass(category::class)]
#[CoversFunction('admin_get_root')]
final class root_test extends \advanced_testcase {
    #[\Override]
    public static function setUpBeforeClass(): void {
        global $CFG;
        require_once($CFG->libdir . '/adminlib.php');
        parent::setUpBeforeClass();
    }

    /**
     * Adding nodes into the admin tree.
     */
    public function test_add_nodes(): void {

        $tree = new root(true);
        $tree->add('root', $one = new category('one', 'One'));
        $tree->add('root', new category('three', 'Three'));
        $tree->add('one', new category('one-one', 'One-one'));
        $tree->add('one', new category('one-three', 'One-three'));

        // Check the order of nodes in the root.
        $map = [];
        foreach ($tree->children as $child) {
            $map[] = $child->name;
        }
        $this->assertEquals(['one', 'three'], $map);

        // Insert a node into the middle.
        $tree->add('root', new category('two', 'Two'), 'three');
        $map = [];
        foreach ($tree->children as $child) {
            $map[] = $child->name;
        }
        $this->assertEquals(['one', 'two', 'three'], $map);

        // Non-existing sibling.
        $tree->add('root', new category('four', 'Four'), 'five');
        $this->assertDebuggingCalled('Sibling five not found', DEBUG_DEVELOPER);

        $tree->add('root', new category('five', 'Five'));
        $map = [];
        foreach ($tree->children as $child) {
            $map[] = $child->name;
        }
        $this->assertEquals(['one', 'two', 'three', 'four', 'five'], $map);

        // Insert a node into the middle of the subcategory.
        $tree->add('one', new category('one-two', 'One-two'), 'one-three');
        $map = [];
        foreach ($one->children as $child) {
            $map[] = $child->name;
        }
        $this->assertEquals(['one-one', 'one-two', 'one-three'], $map);

        // Check just siblings, not parents or children.
        $tree->add('one', new category('one-four', 'One-four'), 'one');
        $this->assertDebuggingCalled('Sibling one not found', DEBUG_DEVELOPER);

        $tree->add('root', new category('six', 'Six'), 'one-two');
        $this->assertDebuggingCalled('Sibling one-two not found', DEBUG_DEVELOPER);

        // Me! Me! I wanna be first!
        $tree->add('root', new externalpage('zero', 'Zero', 'http://foo.bar'), 'one');
        $map = [];
        foreach ($tree->children as $child) {
            $map[] = $child->name;
        }
        $this->assertEquals(['zero', 'one', 'two', 'three', 'four', 'five', 'six'], $map);
    }

    public function test_add_nodes_before_invalid1(): void {
        $tree = new root(true);
        $this->expectException(coding_exception::class);
        $tree->add('root', new externalpage('foo', 'Foo', 'http://foo.bar'), ['moodle:site/config']);
    }

    public function test_add_nodes_before_invalid2(): void {
        $tree = new root(true);
        $this->expectException(coding_exception::class);
        $tree->add('root', new category('bar', 'Bar'), '');
    }

    /**
     * Verifies the $ADMIN global (adminroot cache) is properly reset when changing users,
     * which might occur naturally during cron.
     */
    public function test_adminroot_cache_reset(): void {
        $this->resetAfterTest();
        global $DB;
        // Current user is a manager at site context, which won't have access to the 'debugging' section of the admin tree.
        $manageruser = $this->getDataGenerator()->create_user();
        $context = system::instance();
        $managerrole = $DB->get_record('role', ['shortname' => 'manager']);
        role_assign($managerrole->id, $manageruser->id, $context->id);
        $this->setUser($manageruser);
        $adminroot = \admin_get_root();
        $section = $adminroot->locate('debugging');
        $this->assertEmpty($section);

        // Now, change the user to an admin user and confirm we get a new copy of the admin tree when next we ask for it.
        $adminuser = get_admin();
        $this->setUser($adminuser);
        $adminroot = \admin_get_root();
        $section = $adminroot->locate('debugging');
        $this->assertInstanceOf(\core_admin\setting\settingpage\settingpage::class, $section);
    }
}
