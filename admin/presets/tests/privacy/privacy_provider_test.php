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

namespace core_adminpresets\privacy;

use context_system;
use context_user;
use core_privacy\local\metadata\collection;
use core_privacy\tests\provider_testcase;

/**
 * Tests for the privacy provider class.
 *
 * @package    core_adminpresets
 * @category   test
 * @copyright  2021 Sara Arjona (sara@moodle.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \core_adminpresets\privacy\provider
 */
final class privacy_provider_test extends provider_testcase {

    /**
     * Test for provider::get_metadata().
     * @covers ::get_metadata
     */
    public function test_get_metadata(): void {
        $collection = new collection('core_adminpresets');
        $newcollection = provider::get_metadata($collection);
        $itemcollection = $newcollection->get_collection();
        $this->assertCount(2, $itemcollection);

        // The expected metadata fields are covered by test_metadata_provider() in privacy/tests/provider_test.php.
    }

    /**
     * Test for provider::get_contexts_for_userid() doesn't return any context.
     * @covers ::get_contexts_for_userid
     */
    public function test_get_contexts_for_userid(): void {
        global $USER;

        $this->resetAfterTest();
        $this->setAdminUser();

        // Create a preset.
        $generator = $this->getDataGenerator()->get_plugin_generator('core_adminpresets');
        $generator->create_preset();

        $contextlist = provider::get_contexts_for_userid($USER->id);
        $this->assertEmpty($contextlist);
    }

    /**
     * Test for provider::get_users_in_context() doesn't return any user.
     * @covers ::get_users_in_context
     */
    public function test_get_users_in_context(): void {
        global $USER;

        $this->resetAfterTest();
        $this->setAdminUser();

        // Create a preset.
        $generator = $this->getDataGenerator()->get_plugin_generator('core_adminpresets');
        $generator->create_preset();

        $usercontext = context_user::instance($USER->id);
        $userlist = new \core_privacy\local\request\userlist($usercontext, 'core_adminpresets');
        \core_message\privacy\provider::get_users_in_context($userlist);
        $this->assertEmpty($userlist->get_userids());
    }


    /**
     * Test for provider::export_user_data().
     * @covers ::export_user_data
     */
    public function test_export_user_data(): void {
        global $USER;

        $this->resetAfterTest();
        $this->setAdminUser();

        // Create a preset.
        $generator = $this->getDataGenerator()->get_plugin_generator('core_adminpresets');
        $generator->create_preset();

        // Check data is not exported in user context.
        $usercontext = context_user::instance($USER->id);
        $this->export_context_data_for_user($USER->id, $usercontext, 'core_adminpresets');
        $writer = \core_privacy\local\request\writer::with_context($usercontext);

        $this->assertEmpty($writer->get_data([get_string('siteadminpresetspluginname', 'core_adminpresets')]));
        $this->assertEmpty($writer->get_all_metadata([]));
        $this->assertEmpty($writer->get_files([]));

        // Check data is not exported in system context either.
        $systemcontext = context_system::instance();
        $this->export_context_data_for_user($USER->id, $systemcontext, 'core_adminpresets');
        $writer = \core_privacy\local\request\writer::with_context($systemcontext);

        $this->assertEmpty($writer->get_data([get_string('siteadminpresetspluginname', 'core_adminpresets')]));
        $this->assertEmpty($writer->get_all_metadata([]));
        $this->assertEmpty($writer->get_files([]));
    }

    /**
     * Test for provider::delete_data_for_all_users_in_context().
     * @covers ::delete_data_for_all_users_in_context
     */
    public function test_delete_data_for_all_users_in_context(): void {
        global $DB, $USER;

        $this->resetAfterTest();
        $this->setAdminUser();

        $currentpresets = $DB->count_records('adminpresets');

        // Create a preset.
        $generator = $this->getDataGenerator()->get_plugin_generator('core_adminpresets');
        $generator->create_preset();
        $this->assertEquals($currentpresets + 1, $DB->count_records('adminpresets'));

        $usercontext = context_user::instance($USER->id);

        provider::delete_data_for_all_users_in_context($usercontext);

        // Confirm the presets haven't been removed.
        $this->assertEquals($currentpresets + 1, $DB->count_records('adminpresets'));
    }

    /**
     * Test for provider::delete_data_for_user().
     * @covers ::delete_data_for_user
     */
    public function test_delete_data_for_user(): void {
        global $DB, $USER;

        $this->resetAfterTest();
        $this->setAdminUser();

        $currentpresets = $DB->count_records('adminpresets');

        // Create a preset.
        $generator = $this->getDataGenerator()->get_plugin_generator('core_adminpresets');
        $generator->create_preset();
        $this->assertEquals($currentpresets + 1, $DB->count_records('adminpresets'));

        $usercontext = context_user::instance($USER->id);
        $contextlist = new \core_privacy\local\request\approved_contextlist($USER, 'core_adminpresets', [$usercontext->id]);
        provider::delete_data_for_user($contextlist);

        // Confirm the presets haven't been removed.
        $this->assertEquals($currentpresets + 1, $DB->count_records('adminpresets'));
    }

}
