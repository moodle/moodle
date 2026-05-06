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

namespace core_admin\setting\settingpage;

use core_admin\setting;
use core_admin\setting\setting\configpasswordunmask;
use core_admin\setting\setting\configtext;
use core_admin\setting\tree\category;
use core_admin\setting\tree\root;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversFunction;

/**
 * Unit tests for the admin settingpage class.
 *
 * @package    core_admin
 * @category   test
 * @copyright  2013 David Mudrak <david@moodle.com>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[CoversClass(settingpage::class)]
#[CoversFunction('admin_find_write_settings')]
final class settingpage_test extends \advanced_testcase {
    #[\Override]
    public static function setUpBeforeClass(): void {
        global $CFG;
        require_once($CFG->libdir . '/adminlib.php');
        parent::setUpBeforeClass();
    }

    /**
     * Test that changes to config trigger events.
     */
    public function test_config_log_created_event(): void {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();

        $adminroot = new root(true);
        $adminroot->add('root', $one = new category('one', 'One'));
        $page = new settingpage('page', 'Page');
        $page->add(new configtext('text1', 'Text 1', '', ''));
        $page->add(new configpasswordunmask('pass1', 'Password 1', '', ''));
        $adminroot->add('one', $page);

        $sink = $this->redirectEvents();
        $data = ['s__text1' => 'sometext', 's__pass1' => ''];
        $this->save_config_data($adminroot, $data);

        $events = $sink->get_events();
        $sink->close();
        $event = array_pop($events);
        $this->assertInstanceOf('\core\event\config_log_created', $event);

        $sink = $this->redirectEvents();
        $data = ['s__text1' => 'other', 's__pass1' => 'nice password'];
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

        $DB->delete_records('config_log', []);

        $adminroot = new root(true);
        $adminroot->add('root', $one = new category('one', 'One'));
        $page = new settingpage('page', 'Page');
        $page->add(new configtext('text1', 'Text 1', '', ''));
        $page->add(new configpasswordunmask('pass1', 'Password 1', '', ''));
        $adminroot->add('one', $page);

        $this->assertEmpty($DB->get_records('config_log'));
        $data = ['s__text1' => 'sometext', 's__pass1' => ''];
        $count = $this->save_config_data($adminroot, $data);

        $this->assertEquals(2, $count);
        $records = $DB->get_records('config_log', [], 'id asc');
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

        $DB->delete_records('config_log', []);
        $data = ['s__text1' => 'other', 's__pass1' => 'nice password'];
        $count = $this->save_config_data($adminroot, $data);

        $this->assertEquals(2, $count);
        $records = $DB->get_records('config_log', [], 'id asc');
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

        $DB->delete_records('config_log', []);
        $data = ['s__text1' => '', 's__pass1' => ''];
        $count = $this->save_config_data($adminroot, $data);

        $this->assertEquals(2, $count);
        $records = $DB->get_records('config_log', [], 'id asc');
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

    /**
     * Helper to save config data via admin settings write flow.
     *
     * @param root $adminroot
     * @param array $data
     * @return int
     */
    protected function save_config_data(root $adminroot, array $data): int {
        $adminroot->errors = [];

        $settings = admin_find_write_settings($adminroot, $data);

        $count = 0;
        foreach ($settings as $fullname => $setting) {
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
}
