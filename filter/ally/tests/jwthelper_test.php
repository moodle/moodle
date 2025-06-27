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

namespace filter_ally;

use Firebase\JWT\Key;

/**
 * @package   filter_ally
 * @group     filter_ally
 * @group     ally
 */
final class jwthelper_test extends \advanced_testcase {

    protected function config_set_ok() {
        set_config('secret', 'WAzk9ohDeK', 'tool_ally');
    }

    /**
     * Validates JSON Web Token
     *
     * @param  string $token - JW Token
     * @param  int $userid
     * @param  int $courseid
     * @param  string $role
     * @return void
     * @throws \InvalidArgumentException
     * @throws \UnexpectedValueException
     * @throws \DomainException
     * @throws \Firebase\JWT\SignatureInvalidException
     * @throws \Firebase\JWT\BeforeValidException
     * @throws \Firebase\JWT\ExpiredException
     */
    protected function validate_token($token, $userid, $courseid, $roles) {
        global $CFG;
        if (!class_exists('\Firebase\JWT\JWT')) {
            /* @noinspection PhpIncludeInspection */
            require_once($CFG->dirroot . '/filter/ally/vendor/autoload.php');
        }

        $secret = get_config('tool_ally', 'secret');

        /* @noinspection PhpUnnecessaryFullyQualifiedNameInspection */

        $payload = \Firebase\JWT\JWT::decode($token, new Key($secret, 'HS256'));

        $this->assertObjectHasProperty('return_url', $payload);
        $this->assertObjectHasProperty('iat', $payload);
        $this->assertObjectHasProperty('user_id', $payload);
        $this->assertObjectHasProperty('course_id', $payload);
        $this->assertObjectHasProperty('locale', $payload);
        $this->assertObjectHasProperty('roles', $payload);

        $this->assertSame($payload->return_url, $CFG->wwwroot);
        if ($userid != null) {
            $userid = (int)$userid;
        }
        $this->assertSame($payload->user_id, $userid);
        $this->assertSame($payload->course_id, (int)$courseid);
        $this->assertSame($roles, $payload->roles);
    }

    public function test_jwttoken_false(): void {
        global $COURSE, $USER;
        $this->resetAfterTest();
        $this->setAdminUser();
        $token = \filter_ally\local\jwthelper::get_token($USER, $COURSE->id);
        $this->assertFalse($token);
    }

    public function test_jwttoken_ok(): void {
        global $COURSE, $USER;
        $this->setAdminUser();
        $this->resetAfterTest();
        $this->config_set_ok();

        $token = \filter_ally\local\jwthelper::get_token($USER, $COURSE->id);
        $this->assertNotFalse($token);
    }

    public function test_jwttoken_valid(): void {
        global $COURSE, $USER;
        $this->setAdminUser();
        $this->resetAfterTest();
        $this->config_set_ok();

        $token = \filter_ally\local\jwthelper::get_token($USER, $COURSE->id);
        $expectedrole = 'urn:lti:role:ims/lis/Instructor,urn:lti:role:ims/lis/Administrator';
        $this->validate_token($token, $USER->id, $COURSE->id, $expectedrole);
    }

    public function test_jwttoken_valid_teacher(): void {
        global $DB;

        $this->resetAfterTest();
        $this->config_set_ok();

        $course = $this->getDataGenerator()->create_course();
        $user   = $this->getDataGenerator()->create_user();
        $roleid = $DB->get_field('role', 'id', ['shortname' => 'editingteacher'], MUST_EXIST);

        $this->getDataGenerator()->enrol_user($user->id, $course->id, $roleid);

        $this->setUser($user);

        $token = \filter_ally\local\jwthelper::get_token($user, $course->id);

        $expectedrole = 'urn:lti:role:ims/lis/Instructor';
        $this->validate_token($token, $user->id, $course->id, $expectedrole);
    }

    public function test_jwttoken_valid_student(): void {
        global $DB;

        $this->resetAfterTest();
        $this->config_set_ok();

        $course = $this->getDataGenerator()->create_course();
        $user   = $this->getDataGenerator()->create_user();
        $roleid = $DB->get_field('role', 'id', ['shortname' => 'student'], MUST_EXIST);

        $this->getDataGenerator()->enrol_user($user->id, $course->id, $roleid);

        $unenrolledcourse = $this->getDataGenerator()->create_course();

        $this->setUser($user);

        $token = \filter_ally\local\jwthelper::get_token($user, $course->id);

        $expectedrole = 'urn:lti:role:ims/lis/Learner';
        $this->validate_token($token, $user->id, $course->id, $expectedrole);

        // Valid user accounts can guest access courses and still us Ally.
        $token = \filter_ally\local\jwthelper::get_token($user, $unenrolledcourse->id);

        $expectedrole = 'urn:lti:role:ims/lis/Learner';
        $this->validate_token($token, $user->id, $unenrolledcourse->id, $expectedrole);
    }

    public function test_jwttoken_valid_guest(): void {
        global $USER;

        $this->resetAfterTest();
        $this->config_set_ok();

        $course = $this->getDataGenerator()->create_course();

        $this->setGuestUser();

        $token = \filter_ally\local\jwthelper::get_token($USER, $course->id);

        $expectedrole = 'urn:lti:sysrole:ims/lis/None';
        $expecteduserid = null; // Special case for guest role and not logged in.
        $this->validate_token($token, $expecteduserid, $course->id, $expectedrole);

    }
}
