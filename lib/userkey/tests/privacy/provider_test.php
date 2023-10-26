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
 * Privacy tests for core_userkey.
 *
 * @package    core_userkey
 * @category   test
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_userkey\privacy;

defined('MOODLE_INTERNAL') || die();

use core_privacy\tests\provider_testcase;
use core_privacy\local\request\writer;
use core_userkey\privacy\provider;

/**
 * Privacy tests for core_userkey.
 *
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider_test extends provider_testcase {
    /**
     * Export for a user with no keys in the specified instance will not have any data exported.
     */
    public function test_export_userkeys_no_keys() {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        $context = \context_system::instance();

        provider::export_userkeys($context, [], 'core_tests');

        $this->assertFalse(writer::with_context($context)->has_any_data());
    }

    /**
     * Export for a user with a key against a script where no instance is specified.
     */
    public function test_export_userkeys_basic_key() {
        global $DB;
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        $key = get_user_key('core_tests', $user->id);

        $context = \context_system::instance();
        $subcontext = [];

        provider::export_userkeys($context, $subcontext, 'core_tests');

        $writer = writer::with_context($context);

        $this->assertTrue($writer->has_any_data());
        $exported = $writer->get_related_data($subcontext, 'userkeys');

        $this->assertCount(1, $exported->keys);

        $firstkey = reset($exported->keys);
        $this->assertEquals('core_tests', $firstkey->script);
        $this->assertEquals('', $firstkey->instance);
        $this->assertEquals('', $firstkey->iprestriction);
        $this->assertNotEmpty($firstkey->validuntil);
        $this->assertNotEmpty($firstkey->timecreated);

        provider::delete_userkeys('core_tests', $user->id);

        $this->assertCount(0, $DB->get_records('user_private_key'));
    }

    /**
     * Export for a user with a key against a script where additional data is specified.
     */
    public function test_export_userkeys_complex_key() {
        global $DB;
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        $key = get_user_key('core_tests', $user->id, 42, '127.0.0.1', 12345);

        $context = \context_system::instance();
        $subcontext = [];

        // Export all keys in core_tests.
        provider::export_userkeys($context, $subcontext, 'core_tests');

        $writer = writer::with_context($context);

        $this->assertTrue($writer->has_any_data());
        $exported = $writer->get_related_data($subcontext, 'userkeys');

        $this->assertCount(1, $exported->keys);

        $firstkey = reset($exported->keys);
        $this->assertEquals('core_tests', $firstkey->script);
        $this->assertEquals(42, $firstkey->instance);
        $this->assertEquals('127.0.0.1', $firstkey->iprestriction);
        $this->assertNotEmpty($firstkey->validuntil);
        $this->assertNotEmpty($firstkey->timecreated);

        provider::delete_userkeys('core_tests', $user->id);

        $this->assertCount(0, $DB->get_records('user_private_key'));
    }

    /**
     * Export for a user with a key against a script where no instance is specified.
     */
    public function test_export_userkeys_basic_key_without_filter() {
        global $DB;
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        $key = get_user_key('core_tests', $user->id);

        $context = \context_system::instance();
        $subcontext = [];

        provider::export_userkeys($context, $subcontext, 'core_tests');

        $writer = writer::with_context($context);

        $this->assertTrue($writer->has_any_data());
        $exported = $writer->get_related_data($subcontext, 'userkeys');

        $this->assertCount(1, $exported->keys);

        $firstkey = reset($exported->keys);
        $this->assertEquals('core_tests', $firstkey->script);
        $this->assertEquals('', $firstkey->instance);
        $this->assertEquals('', $firstkey->iprestriction);
        $this->assertNotEmpty($firstkey->validuntil);
        $this->assertNotEmpty($firstkey->timecreated);

        provider::delete_userkeys('core_tests', $user->id);

        $this->assertCount(0, $DB->get_records('user_private_key'));
    }

    /**
     * Export for a user with a key against a script where additional data is specified.
     */
    public function test_export_userkeys_complex_key_with_filter() {
        global $DB;
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        $key = get_user_key('core_tests', $user->id, 42, '127.0.0.1', 12345);

        $context = \context_system::instance();
        $subcontext = [];

        // Export all keys in core_tests against instance 43 - no keys.
        provider::export_userkeys($context, $subcontext, 'core_tests', 43);
        $writer = writer::with_context($context);
        $this->assertFalse($writer->has_any_data());

        // Export all keys in core_tests against instance 42.
        provider::export_userkeys($context, $subcontext, 'core_tests', 42);
        $writer = writer::with_context($context);
        $this->assertTrue($writer->has_any_data());
        $exported = $writer->get_related_data($subcontext, 'userkeys');

        $this->assertCount(1, $exported->keys);

        $firstkey = reset($exported->keys);
        $this->assertEquals('core_tests', $firstkey->script);
        $this->assertEquals(42, $firstkey->instance);
        $this->assertEquals('127.0.0.1', $firstkey->iprestriction);
        $this->assertNotEmpty($firstkey->validuntil);
        $this->assertNotEmpty($firstkey->timecreated);

        // Delete for instance 43 (no keys).
        provider::delete_userkeys('core_tests', $user->id, 43);
        $this->assertCount(1, $DB->get_records('user_private_key'));

        // Delete for instance 42.
        provider::delete_userkeys('core_tests', $user->id, 42);
        $this->assertCount(0, $DB->get_records('user_private_key'));
    }

    /**
     * Export for a user with keys against multiple scripts where additional data is specified.
     */
    public function test_export_userkeys_multiple_complex_key_with_filter() {
        global $DB;
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        $key = get_user_key('core_tests', $user->id, 42, '127.0.0.1', 12345);
        $key = get_user_key('core_userkey', $user->id, 99, '240.0.0.1', 54321);

        $context = \context_system::instance();
        $subcontext = [];

        // Export all keys in core_tests against instance 43 - no keys.
        provider::export_userkeys($context, $subcontext, 'core_tests', 43);
        $writer = writer::with_context($context);
        $this->assertFalse($writer->has_any_data());

        // Export all keys in core_tests against instance 42.
        provider::export_userkeys($context, $subcontext, 'core_tests', 42);
        $writer = writer::with_context($context);
        $this->assertTrue($writer->has_any_data());
        $exported = $writer->get_related_data($subcontext, 'userkeys');

        $this->assertCount(1, $exported->keys);

        $firstkey = reset($exported->keys);
        $this->assertEquals('core_tests', $firstkey->script);
        $this->assertEquals(42, $firstkey->instance);
        $this->assertEquals('127.0.0.1', $firstkey->iprestriction);
        $this->assertNotEmpty($firstkey->validuntil);
        $this->assertNotEmpty($firstkey->timecreated);

        // Delete for instance 43 (no keys).
        provider::delete_userkeys('core_tests', $user->id, 43);
        $this->assertCount(2, $DB->get_records('user_private_key'));

        // Delete for instance 42.
        provider::delete_userkeys('core_tests', $user->id, 42);
        $this->assertCount(1, $DB->get_records('user_private_key'));

        // Delete for instance 99.
        provider::delete_userkeys('core_tests', $user->id, 99);
        $this->assertCount(1, $DB->get_records('user_private_key'));

        // Delete for instance 99 of core_userkey too.
        provider::delete_userkeys('core_userkey', $user->id, 99);
        $this->assertCount(0, $DB->get_records('user_private_key'));
    }

    /**
     * Export for keys against multiple users.
     */
    public function test_export_userkeys_multiple_users() {
        global $DB;
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        $otheruser = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        $key = get_user_key('core_tests', $user->id, 42, '127.0.0.1', 12345);
        $key = get_user_key('core_tests', $otheruser->id, 42, '127.0.0.1', 12345);

        $context = \context_system::instance();
        $subcontext = [];

        // Export all keys in core_tests against instance 43 - no keys.
        provider::export_userkeys($context, $subcontext, 'core_tests', 43);
        $writer = writer::with_context($context);
        $this->assertFalse($writer->has_any_data());

        // Export all keys in core_tests against instance 42.
        provider::export_userkeys($context, $subcontext, 'core_tests', 42);
        $writer = writer::with_context($context);
        $this->assertTrue($writer->has_any_data());
        $exported = $writer->get_related_data($subcontext, 'userkeys');

        $this->assertCount(1, $exported->keys);

        $firstkey = reset($exported->keys);
        $this->assertEquals('core_tests', $firstkey->script);
        $this->assertEquals(42, $firstkey->instance);
        $this->assertEquals('127.0.0.1', $firstkey->iprestriction);
        $this->assertNotEmpty($firstkey->validuntil);
        $this->assertNotEmpty($firstkey->timecreated);

        // Delete for instance 43 (no keys).
        provider::delete_userkeys('core_tests', $user->id, 43);
        $this->assertCount(2, $DB->get_records('user_private_key'));

        // Delete for instance 42.
        provider::delete_userkeys('core_tests', $user->id, 42);
        $this->assertCount(1, $DB->get_records('user_private_key'));

        // Delete for instance 99.
        provider::delete_userkeys('core_tests', $user->id, 99);
        $this->assertCount(1, $DB->get_records('user_private_key'));
    }

    /**
     * Delete for all users in a script.
     */
    public function test_delete_all_userkeys_in_script() {
        global $DB;
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        $otheruser = $this->getDataGenerator()->create_user();

        $key = get_user_key('core_tests', $user->id, 42, '127.0.0.1', 12345);
        $key = get_user_key('core_tests', $user->id, 43, '127.0.0.1', 12345);
        $key = get_user_key('core_userkey', $user->id, 42, '127.0.0.1', 12345);
        $key = get_user_key('core_userkey', $user->id, 43, '127.0.0.1', 12345);
        $key = get_user_key('core_tests', $otheruser->id, 42, '127.0.0.1', 12345);
        $key = get_user_key('core_tests', $otheruser->id, 43, '127.0.0.1', 12345);
        $key = get_user_key('core_userkey', $otheruser->id, 42, '127.0.0.1', 12345);
        $key = get_user_key('core_userkey', $otheruser->id, 43, '127.0.0.1', 12345);

        $context = \context_system::instance();
        $subcontext = [];

        $this->assertCount(8, $DB->get_records('user_private_key'));

        // Delete for all of core_tests.
        provider::delete_userkeys('core_tests');
        $this->assertCount(4, $DB->get_records('user_private_key'));

        // Delete for all of core_userkey where instanceid = 42.
        provider::delete_userkeys('core_userkey', null, 42);
        $this->assertCount(2, $DB->get_records('user_private_key'));

        provider::delete_userkeys('core_userkey', $otheruser->id);
        $this->assertCount(1, $DB->get_records('user_private_key'));
    }
}
