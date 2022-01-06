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
 * Base class for unit tests for auth_manual.
 *
 * @package    auth_manual
 * @category   test
 * @copyright  2018 Carlos Escobedo <carlos@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot.'/auth/manual/auth.php');

use \core_privacy\local\request\writer;
use \core_privacy\local\request\transform;
use \auth_manual\privacy\provider;

/**
 * Unit tests for the auth_manual implementation of the privacy API.
 *
 * @copyright  2018 Carlos Escobedo <carlos@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class auth_manual_privacy_testcase extends \core_privacy\tests\provider_testcase {

    /** @var auth_plugin_manual Keeps the authentication plugin. */
    protected $authplugin;

    /**
     * Basic setup for these tests.
     */
    public function setUp(): void {
        $this->resetAfterTest(true);
        $this->authplugin = new auth_plugin_manual();
    }

    /**
     * Test to check export_user_preferences.
     * returns user preferences data.
     */
    public function test_export_user_preferences() {
        $user = $this->getDataGenerator()->create_user();
        $this->authplugin->user_update_password($user, 'MyPrivacytestPassword*');

        provider::export_user_preferences($user->id);
        $writer = writer::with_context(\context_system::instance());
        $prefs = $writer->get_user_preferences('auth_manual');
        $time = transform::datetime(get_user_preferences('auth_manual_passwordupdatetime', 0, $user->id));

        $this->assertEquals($time, $prefs->auth_manual_passwordupdatetime->value);
        $this->assertEquals(get_string('privacy:metadata:preference:passwordupdatetime', 'auth_manual'),
            $prefs->auth_manual_passwordupdatetime->description);
    }
}
