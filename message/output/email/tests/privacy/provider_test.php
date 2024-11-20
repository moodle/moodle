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

namespace message_email\privacy;

use context_system;
use core_message_external;
use core_privacy\local\request\writer;
use core_privacy\tests\provider_testcase;

/**
 * Unit tests for message\output\email\classes\privacy\provider.php
 *
 * @package    message_email
 * @covers     \message_email\privacy\provider
 * @copyright  2018 Mihail Geshoski <mihail@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider_test extends provider_testcase {
    /**
     * Basic setup for these tests.
     */
    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest(true);
    }

    /**
     * Test returning metadata.
     */
    public function test_get_metadata(): void {
        $collection = new \core_privacy\local\metadata\collection('message_email');
        $collection = \message_email\privacy\provider::get_metadata($collection);
        $this->assertNotEmpty($collection);
    }

    /**
     * Test getting the context for the user ID related to this plugin.
     */
    public function test_get_contexts_for_userid(): void {
        $user = $this->getDataGenerator()->create_user();

        $contextlist = \message_email\privacy\provider::get_contexts_for_userid($user->id);
        $this->assertEmpty($contextlist);
    }

    /**
     * Test exporting user preferences
     */
    public function test_export_user_preferences(): void {
        global $CFG;

        require_once("{$CFG->dirroot}/message/externallib.php");

        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        // Submit configuration form, which adds the preferences..
        core_message_external::message_processor_config_form($user->id, 'email', [
            [
                'name' => 'email_email',
                'value' => 'alternate@example.com',
            ],
        ]);

        // Switch to admin user (so we can validate preferences of the correct user are being exported).
        $this->setAdminUser();

        provider::export_user_preferences($user->id);

        $writer = writer::with_context(context_system::instance());
        $this->assertTrue($writer->has_any_data());

        $preferences = $writer->get_user_preferences('message_email');
        $this->assertNotEmpty($preferences->email);

        $this->assertEquals('alternate@example.com', $preferences->email->value);
        $this->assertEquals(get_string('privacy:preference:email', 'message_email'), $preferences->email->description);
    }
}
