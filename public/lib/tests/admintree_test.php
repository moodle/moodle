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

namespace core;

use core_admin\setting\tree\category;
use core_admin\setting\tree\externalpage;
use core_admin\setting\tree\root as admin_root;
use core_admin\setting\settingpage\settingpage;
use core_admin\setting\setting\configpasswordunmask;
use core_admin\setting\setting\configtext;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir.'/adminlib.php');

/**
 * Provides the unit tests for admin tree functionality.
 *
 * @package     core
 * @category    test
 * @copyright   2013 David Mudrak <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class admintree_test extends \advanced_testcase {
    /**
     * Adding nodes into the admin tree.
     */
    public function test_add_nodes(): void {

        $tree = new admin_root(true);
        $tree->add('root', $one = new category('one', 'One'));
        $tree->add('root', new category('three', 'Three'));
        $tree->add('one', new category('one-one', 'One-one'));
        $tree->add('one', new category('one-three', 'One-three'));

        // Check the order of nodes in the root.
        $map = array();
        foreach ($tree->children as $child) {
            $map[] = $child->name;
        }
        $this->assertEquals(array('one', 'three'), $map);

        // Insert a node into the middle.
        $tree->add('root', new category('two', 'Two'), 'three');
        $map = array();
        foreach ($tree->children as $child) {
            $map[] = $child->name;
        }
        $this->assertEquals(array('one', 'two', 'three'), $map);

        // Non-existing sibling.
        $tree->add('root', new category('four', 'Four'), 'five');
        $this->assertDebuggingCalled('Sibling five not found', DEBUG_DEVELOPER);

        $tree->add('root', new category('five', 'Five'));
        $map = array();
        foreach ($tree->children as $child) {
            $map[] = $child->name;
        }
        $this->assertEquals(array('one', 'two', 'three', 'four', 'five'), $map);

        // Insert a node into the middle of the subcategory.
        $tree->add('one', new category('one-two', 'One-two'), 'one-three');
        $map = array();
        foreach ($one->children as $child) {
            $map[] = $child->name;
        }
        $this->assertEquals(array('one-one', 'one-two', 'one-three'), $map);

        // Check just siblings, not parents or children.
        $tree->add('one', new category('one-four', 'One-four'), 'one');
        $this->assertDebuggingCalled('Sibling one not found', DEBUG_DEVELOPER);

        $tree->add('root', new category('six', 'Six'), 'one-two');
        $this->assertDebuggingCalled('Sibling one-two not found', DEBUG_DEVELOPER);

        // Me! Me! I wanna be first!
        $tree->add('root', new externalpage('zero', 'Zero', 'http://foo.bar'), 'one');
        $map = array();
        foreach ($tree->children as $child) {
            $map[] = $child->name;
        }
        $this->assertEquals(array('zero', 'one', 'two', 'three', 'four', 'five', 'six'), $map);
    }

    public function test_add_nodes_before_invalid1(): void {
        $tree = new admin_root(true);
        $this->expectException(\coding_exception::class);
        $tree->add('root', new externalpage('foo', 'Foo', 'http://foo.bar'), array('moodle:site/config'));
    }

    public function test_add_nodes_before_invalid2(): void {
        $tree = new admin_root(true);
        $this->expectException(\coding_exception::class);
        $tree->add('root', new category('bar', 'Bar'), '');
    }

    /**
     * Test that changes to config trigger events.
     */
    public function test_config_log_created_event(): void {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();

        $adminroot = new admin_root(true);
        $adminroot->add('root', $one = new category('one', 'One'));
        $page = new settingpage('page', 'Page');
        $page->add(new configtext('text1', 'Text 1', '', ''));
        $page->add(new configpasswordunmask('pass1', 'Password 1', '', ''));
        $adminroot->add('one', $page);

        $sink = $this->redirectEvents();
        $data = array('s__text1' => 'sometext', 's__pass1' => '');
        $this->save_config_data($adminroot, $data);

        $events = $sink->get_events();
        $sink->close();
        $event = array_pop($events);
        $this->assertInstanceOf('\core\event\config_log_created', $event);

        $sink = $this->redirectEvents();
        $data = array('s__text1'=>'other', 's__pass1'=>'nice password');
        $count = $this->save_config_data($adminroot, $data);

        $events = $sink->get_events();
        $sink->close();
        $event = array_pop($events);
        $this->assertInstanceOf('\core\event\config_log_created', $event);
        // Verify password was nuked.
        $this->assertNotEquals($event->other['value'], 'nice password');

    }

    /**
     * Saving of values.
     */
    public function test_config_logging(): void {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();

        $DB->delete_records('config_log', array());

        $adminroot = new admin_root(true);
        $adminroot->add('root', $one = new category('one', 'One'));
        $page = new settingpage('page', 'Page');
        $page->add(new configtext('text1', 'Text 1', '', ''));
        $page->add(new configpasswordunmask('pass1', 'Password 1', '', ''));
        $adminroot->add('one', $page);

        $this->assertEmpty($DB->get_records('config_log'));
        $data = array('s__text1'=>'sometext', 's__pass1'=>'');
        $count = $this->save_config_data($adminroot, $data);

        $this->assertEquals(2, $count);
        $records = $DB->get_records('config_log', array(), 'id asc');
        $this->assertCount(2, $records);
        reset($records);
        $record = array_shift($records);
        $this->assertNull($record->plugin);
        $this->assertSame('text1', $record->name);
        $this->assertNull($record->oldvalue);
        $this->assertSame('sometext', $record->value);
        $record = array_shift($records);
        $this->assertNull($record->plugin);
        $this->assertSame('pass1', $record->name);
        $this->assertNull($record->oldvalue);
        $this->assertSame('', $record->value);

        $DB->delete_records('config_log', array());
        $data = array('s__text1'=>'other', 's__pass1'=>'nice password');
        $count = $this->save_config_data($adminroot, $data);

        $this->assertEquals(2, $count);
        $records = $DB->get_records('config_log', array(), 'id asc');
        $this->assertCount(2, $records);
        reset($records);
        $record = array_shift($records);
        $this->assertNull($record->plugin);
        $this->assertSame('text1', $record->name);
        $this->assertSame('sometext', $record->oldvalue);
        $this->assertSame('other', $record->value);
        $record = array_shift($records);
        $this->assertNull($record->plugin);
        $this->assertSame('pass1', $record->name);
        $this->assertSame('', $record->oldvalue);
        $this->assertSame('********', $record->value);

        $DB->delete_records('config_log', array());
        $data = array('s__text1'=>'', 's__pass1'=>'');
        $count = $this->save_config_data($adminroot, $data);

        $this->assertEquals(2, $count);
        $records = $DB->get_records('config_log', array(), 'id asc');
        $this->assertCount(2, $records);
        reset($records);
        $record = array_shift($records);
        $this->assertNull($record->plugin);
        $this->assertSame('text1', $record->name);
        $this->assertSame('other', $record->oldvalue);
        $this->assertSame('', $record->value);
        $record = array_shift($records);
        $this->assertNull($record->plugin);
        $this->assertSame('pass1', $record->name);
        $this->assertSame('********', $record->oldvalue);
        $this->assertSame('', $record->value);
    }

    protected function save_config_data(admin_root $adminroot, array $data) {
        $adminroot->errors = array();

        $settings = admin_find_write_settings($adminroot, $data);

        $count = 0;
        foreach ($settings as $fullname=>$setting) {
            /** @var $setting admin_setting */
            $original = $setting->get_setting();
            $error = $setting->write_setting($data[$fullname]);
            if ($error !== '') {
                $adminroot->errors[$fullname] = new \stdClass();
                $adminroot->errors[$fullname]->data  = $data[$fullname];
                $adminroot->errors[$fullname]->id    = $setting->get_id();
                $adminroot->errors[$fullname]->error = $error;
            } else {
                $setting->write_setting_flags($data);
            }
            if ($setting->post_write_settings($original)) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Verifies the $ADMIN global (adminroot cache) is properly reset when changing users, which might occur naturally during cron.
     */
    public function test_adminroot_cache_reset(): void {
        $this->resetAfterTest();
        global $DB;
        // Current user is a manager at site context, which won't have access to the 'debugging' section of the admin tree.
        $manageruser = $this->getDataGenerator()->create_user();
        $context = \context_system::instance();
        $managerrole = $DB->get_record('role', array('shortname' => 'manager'));
        role_assign($managerrole->id, $manageruser->id, $context->id);
        $this->setUser($manageruser);
        $adminroot = admin_get_root();
        $section = $adminroot->locate('debugging');
        $this->assertEmpty($section);

        // Now, change the user to an admin user and confirm we get a new copy of the admin tree when next we ask for it.
        $adminuser = get_admin();
        $this->setUser($adminuser);
        $adminroot = admin_get_root();
        $section = $adminroot->locate('debugging');
        $this->assertInstanceOf(settingpage::class, $section);
    }
}
