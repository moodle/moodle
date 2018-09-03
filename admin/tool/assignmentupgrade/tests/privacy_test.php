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
 * Privacy tests for tool_assignmentupgrade.
 *
 * @package    tool_assignmentupgrade
 * @category   test
 * @copyright  2018 Zig Tan <zig@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use \core_privacy\tests\provider_testcase;
use \core_privacy\local\request\writer;
use \tool_assignmentupgrade\privacy\provider;

/**
 * Unit tests for tool_assignmentupgrade/classes/privacy/policy
 *
 * @copyright  2018 Zig Tan <zig@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_assignmentupgrade_privacy_testcase extends provider_testcase {

    /**
     * Overriding setUp() function to always reset after tests.
     */
    public function setUp() {
        $this->resetAfterTest(true);
    }

    /**
     * Test for provider::test_export_user_preferences().
     */
    public function test_export_user_preferences() {
        // Test setup.
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        // Add a user home page preference for the User.
        set_user_preference('tool_assignmentupgrade_perpage', '100', $user);

        // Test the user preference exists.
        $params = [
            'userid' => $user->id,
            'name' => 'tool_assignmentupgrade_perpage'
        ];

        // Test the user preferences export contains 1 user preference record for the User.
        provider::export_user_preferences($user->id);
        $contextuser = context_user::instance($user->id);
        $writer = writer::with_context($contextuser);
        $this->assertTrue($writer->has_any_data());

        $exportedpreferences = $writer->get_user_preferences('tool_assignmentupgrade');
        $this->assertCount(1, (array) $exportedpreferences);
        $this->assertEquals('100', $exportedpreferences->perpage->value);
    }

}
