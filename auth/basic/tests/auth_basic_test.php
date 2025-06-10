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
 * Base class for unit tests for auth_basic.
 *
 * @package    auth_basic
 * @category   test
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot.'/auth/basic/auth.php');

class auth_basic_test extends advanced_testcase {
    /** @var auth_plugin_basic Keeps the authentication plugin. */
    protected $authplugin;
    protected function setUp(): void {
        $this->resetAfterTest(true);
        $this->authplugin = new \auth_plugin_basic();
    }
    public function test_login_user() {
        $loginsuccessful = $this->authplugin->user_login('username', 'password');
        self::assertFalse($loginsuccessful);
    }
}
