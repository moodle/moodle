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

namespace core\output;

/**
 * Unit tests for the user_picture class.
 *
 * @package core
 * @copyright 2024 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \core\output\user_picture
 */
final class user_picture_test extends \advanced_testcase {

    /**
     * Assert appropriate debugging is emitted if required user fields are absent
     */
    public function test_constructor_missing_fields(): void {
        $user = get_admin();
        unset($user->picture);

        // Assert debugging notice when required field isn't present.
        $userpicture = new user_picture($user);
        $this->assertDebuggingCalled('Missing \'picture\' property in $user object, this is a performance problem that needs ' .
            'to be fixed by a developer. Please use the \core_user\fields API to get the full list of required fields.');
    }

    /**
     * Tests {@see user_picture::allow_view()} for a not-logged-in request.
     */
    public function test_allow_view_not_logged_in(): void {
        global $DB;

        $this->resetAfterTest();

        $adminid = $DB->get_field('user', 'id', ['username' => 'admin'], MUST_EXIST);

        // Default config allows user pictures when not logged in.
        $this->assertTrue(user_picture::allow_view($adminid));

        // Not allowed with either or both forcelogin options.
        set_config('forcelogin', 1);
        $this->assertFalse(user_picture::allow_view($adminid));
        set_config('forcelogin', 0);
        set_config('forceloginforprofileimage', 1);
        $this->assertFalse(user_picture::allow_view($adminid));
        set_config('forcelogin', 1);
        $this->assertFalse(user_picture::allow_view($adminid));
    }

    /**
     * Tests {@see user_picture::allow_view()} for a guest user request.
     */
    public function test_allow_view_guest(): void {
        global $DB;

        $this->resetAfterTest();
        $this->setGuestUser();

        $adminid = $DB->get_field('user', 'id', ['username' => 'admin'], MUST_EXIST);

        // Default config allows user pictures for guests.
        $this->assertTrue(user_picture::allow_view($adminid));

        // Not allowed with forceloginforprofileimage.
        set_config('forceloginforprofileimage', 1);
        $this->assertFalse(user_picture::allow_view($adminid));

        // Allowed by default with just forcelogin.
        set_config('forceloginforprofileimage', 0);
        set_config('forcelogin', 1);
        $this->assertTrue(user_picture::allow_view($adminid));

        // But would not be allowed if we change guest role to remove capability.
        $guestroleid = $DB->get_field('role', 'id', ['shortname' => 'guest'], MUST_EXIST);
        assign_capability('moodle/user:viewprofilepictures', CAP_INHERIT, $guestroleid,
            \context_system::instance()->id, true);
        $this->assertFalse(user_picture::allow_view($adminid));
    }

    /**
     * Tests {@see user_picture::allow_view()} for a logged in user.
     */
    public function test_allow_view_user(): void {
        global $DB;

        $this->resetAfterTest();

        $adminid = $DB->get_field('user', 'id', ['username' => 'admin'], MUST_EXIST);

        $generator = self::getDataGenerator();
        $user = $generator->create_user();
        $this->setUser($user);

        // Default config allows user pictures.
        $this->assertTrue(user_picture::allow_view($adminid));

        // Also allowed with either or both forcelogin option, because they are logged in.
        set_config('forcelogin', 1);
        $this->assertTrue(user_picture::allow_view($adminid));
        set_config('forcelogin', 0);
        set_config('forceloginforprofileimage', 1);
        $this->assertTrue(user_picture::allow_view($adminid));
        set_config('forcelogin', 1);
        $this->assertTrue(user_picture::allow_view($adminid));

        // But would not be allowed if we change user role to remove capability.
        $userroleid = $DB->get_field('role', 'id', ['shortname' => 'user'], MUST_EXIST);
        assign_capability('moodle/user:viewprofilepictures', CAP_INHERIT, $userroleid,
            \context_system::instance()->id, true);
        $this->assertFalse(user_picture::allow_view($adminid));

        // Except you are still allowed to view your own user picture.
        $this->assertTrue(user_picture::allow_view($user->id));
    }
}
