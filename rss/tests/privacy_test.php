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
 * Base class for unit tests for core_rss.
 *
 * @package    core_rss
 * @copyright  2018 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use \core_privacy\tests\provider_testcase;
use \core_rss\privacy\provider;
use \core_privacy\local\request\writer;
use \core_privacy\local\request\approved_contextlist;

/**
 * Unit tests for rss\classes\privacy\provider.php
 *
 * @copyright  2018 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_rss_testcase extends provider_testcase {

    /**
     * Basic setup for these tests.
     */
    public function setUp() {
        $this->resetAfterTest(true);
    }

    /**
     * Test getting the context for the user ID related to this plugin.
     */
    public function test_get_contexts_for_userid() {
        // Create user and RSS user keys.
        $user = $this->getDataGenerator()->create_user();
        $context = \context_user::instance($user->id);
        $key = get_user_key('rss', $user->id);

        $contextlist = provider::get_contexts_for_userid($user->id);
        $this->assertEquals($context->id, $contextlist->current()->id);
    }

    /**
     * Test that data is exported correctly for this plugin.
     */
    public function test_export_user_data() {
        global $DB;

        // Create user and RSS user keys.
        $user = $this->getDataGenerator()->create_user();
        $context = \context_user::instance($user->id);
        $keyvalue = get_user_key('rss', $user->id);
        $key = $DB->get_record('user_private_key', ['value' => $keyvalue]);

        // Validate exported data.
        $this->setUser($user);
        $writer = writer::with_context($context);
        $this->assertFalse($writer->has_any_data());
        $this->export_context_data_for_user($user->id, $context, 'core_rss');
        $userkeydata = $writer->get_related_data([], 'userkeys');
        $this->assertCount(1, $userkeydata->keys);
        $this->assertEquals($key->script, reset($userkeydata->keys)->script);
    }

    /**
     * Test for provider::delete_data_for_all_users_in_context().
     */
    public function test_delete_data_for_all_users_in_context() {
        global $DB;

        // Create user and RSS user keys.
        $user = $this->getDataGenerator()->create_user();
        $context = \context_user::instance($user->id);
        $keyvalue = get_user_key('rss', $user->id);
        $key = $DB->get_record('user_private_key', ['value' => $keyvalue]);

        // Before deletion, we should have 1 user_private_key.
        $count = $DB->count_records('user_private_key', ['script' => 'rss']);
        $this->assertEquals(1, $count);

        // Delete data.
        provider::delete_data_for_all_users_in_context($context);

        // After deletion, the user_private_key entries should have been deleted.
        $count = $DB->count_records('user_private_key', ['script' => 'rss']);
        $this->assertEquals(0, $count);
    }

    /**
     * Test for provider::delete_data_for_user().
     */
    public function test_delete_data_for_user() {
        global $DB;

        // Create user and RSS user keys.
        $user = $this->getDataGenerator()->create_user();
        $context = \context_user::instance($user->id);
        $keyvalue = get_user_key('rss', $user->id);
        $key = $DB->get_record('user_private_key', ['value' => $keyvalue]);

        // Before deletion, we should have 1 user_private_key.
        $count = $DB->count_records('user_private_key', ['script' => 'rss']);
        $this->assertEquals(1, $count);

        // Delete data.
        $contextlist = provider::get_contexts_for_userid($user->id);
        $approvedcontextlist = new approved_contextlist($user, 'rss', $contextlist->get_contextids());
        provider::delete_data_for_user($approvedcontextlist);

        // After deletion, the user_private_key entries should have been deleted.
        $count = $DB->count_records('user_private_key', ['script' => 'rss']);
        $this->assertEquals(0, $count);
    }
}
