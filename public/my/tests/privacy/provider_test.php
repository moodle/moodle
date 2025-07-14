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
 * Unit tests for the core_my implementation of the privacy API.
 *
 * @package    core_my
 * @category   test
 * @copyright  2018 Zig Tan <zig@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_my\privacy;

defined('MOODLE_INTERNAL') || die();

use core_privacy\local\request\writer;
use core_my\privacy\provider;

/**
 * Unit tests for the core_my implementation of the privacy API.
 *
 * @copyright  2018 Zig Tan <zig@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class provider_test extends \core_privacy\tests\provider_testcase {

    /**
     * Test for provider::test_export_user_preferences().
     */
    public function test_export_user_preferences(): void {
        global $DB;

        // Test setup.
        $this->resetAfterTest(true);
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        // Add a user home page preference for the User.
        set_user_preference('user_home_page_preference', HOMEPAGE_MY);

        // Test the user preference exists.
        $params = [
            'userid' => $user->id,
            'name' => 'user_home_page_preference'
        ];

        $preferences = $DB->get_record('user_preferences', $params);
        $this->assertEquals('user_home_page_preference', $preferences->name);

        // Test the user preferences export contains 1 user preference record for the User.
        provider::export_user_preferences($user->id);
        $contextuser = \context_user::instance($user->id);
        $writer = writer::with_context($contextuser);
        $this->assertTrue($writer->has_any_data());

        $prefs = $writer->get_user_preferences('core_my');
        $this->assertCount(1, (array) $prefs);
        $this->assertEquals(HOMEPAGE_MY, $prefs->user_home_page_preference->value);
    }

}
