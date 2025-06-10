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
 * PHPUnit Mhaairs util service tests.
 *
 * @package     block_mhaairs
 * @category    phpunit
 * @copyright   2014 Itamar Tzadok <itamar@substantialmethods.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once(dirname(__FILE__). '/lib.php');
require_once("$CFG->dirroot/blocks/mhaairs/externallib.php");

/**
 * PHPUnit mhaairs util service test case.
 *
 * @package     block_mhaairs
 * @category    phpunit
 * @group       block_mhaairs
 * @group       block_mhaairs_service
 * @group       block_mhaairs_utilservice
 * @copyright   2015 Itamar Tzadok <itamar@substantialmethods.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_mhaairs_utilservice_testcase extends block_mhaairs_testcase {

    /**
     * Get user info should fail when ssl is required and the connection
     * is not secured.
     *
     * @return void
     */
    public function test_get_user_info_no_ssl() {
        $callback = 'block_mhaairs_utilservice_external::get_user_info';
        $this->set_user('admin');

        // Require ssl.
        set_config('block_mhaairs_sslonly', 1);

        // Service params.
        $serviceparams = array(
            'token' => null, // Token.
            'identitytype' => null, // Identity type.
        );

        $result = call_user_func_array($callback, $serviceparams);
        $this->assertEquals(MHUserInfo::FAILURE, $result->status);
        $this->assertEquals('error: connection must be secured with SSL', $result->message);
    }

    /**
     * Get user info should fail if is passed an invalid token with secret.
     * It should be allowed to continue othewise.
     *
     * @return void
     */
    public function test_get_user_info_invalid_token() {
        $callback = 'block_mhaairs_utilservice_external::get_user_info';
        $this->set_user('admin');

        $secret = 'DF4#R66';
        $userid = $this->student1->username;
        $equal = true;

        // SECRET NOT CONFIGURED.

        // Invalid token missing userid.
        // Should fail with or without a correct secret.
        $this->assert_invalid_token('time='. MHUtil::get_time_stamp());
        $this->assert_invalid_token('time='. MHUtil::get_time_stamp(), $secret);

        // Invalid token with userid.
        // Should NOT fail with or without secret.
        $this->assert_invalid_token("userid=$userid", null, !$equal);
        $this->assert_invalid_token("userid=$userid", $secret, !$equal);

        // SECRET CONFIGURED.
        set_config('block_mhaairs_shared_secret', $secret);

        // Invalid token missing userid.
        // Should fail with or without a correct secret.
        $this->assert_invalid_token('time='. MHUtil::get_time_stamp());
        $this->assert_invalid_token('time='. MHUtil::get_time_stamp(), $secret);

        // Invalid token with userid.
        // Should fail with or without a correct secret.
        $this->assert_invalid_token("userid=$userid", null, $equal);
        $this->assert_invalid_token("userid=$userid", $secret, $equal);

        // Valid token, incorrect secret.
        $this->assert_invalid_token(MHUtil::create_token($userid), $secret. '7', $equal);

    }

    /**
     * Get user info should fail if the requested user is not found.
     * User can be requested by internal userid (identitytype = internal),
     * or by username (identitytype != internal).
     *
     * @return void
     */
    public function test_get_user_info_invalid_user() {
        $callback = 'block_mhaairs_utilservice_external::get_user_info';
        $this->set_user('admin');

        $secret = 'DF4#R66';
        $fakeuserid = 278939;
        $fakeusername = 'johndoe';
        $realuserid = $this->student1->id;
        $realusername = $this->student1->username;

        // Dataset for assert_user_not_found.
        // Each array item is a list of arguments the consitute a test case
        // where the service should fail with user not found message.
        // The list of arguments consists of user id, secret, identity type.
        $fixture = __DIR__. '/fixtures/tc_get_user_info_user_not_found.csv';
        $dataset = $this->createCsvDataSet(array('cases' => $fixture));
        $rows = $dataset->getTable('cases');
        $columns = $dataset->getTableMetaData('cases')->getColumns();

        $cases = array();
        for ($r = 0; $r < $rows->getRowCount(); $r++) {
            $cases[] = (object) array_combine($columns, $rows->getRow($r));
        }

        // Configure secret.
        set_config('block_mhaairs_shared_secret', $secret);

        foreach ($cases as $case) {
            $auserid = ${$case->userid};
            $this->assert_user_not_found($auserid, $secret, $case->identitytype);
        }

    }

    /**
     * Get user info should return user record and roles by course for the target user.
     * User can be requested by internal userid (identitytype = internal),
     * or by username (identitytype != internal).
     *
     * @return void
     */
    public function test_get_user_info_valid_user() {
        $callback = 'block_mhaairs_utilservice_external::get_user_info';
        $this->set_user('admin');

        $users = $this->add_users_and_enrolments('/fixtures/tc_get_user_info_users.csv');

        // Configure secret.
        $secret = 'DF4#R66';
        set_config('block_mhaairs_shared_secret', $secret);

        // Make sure we use the empty default.
        set_config('block_mhaairs_student_roles', '');
        set_config('block_mhaairs_instructor_roles', '');

        // Tese cases dataset.
        // User id, identity type.
        $fixture = __DIR__. '/fixtures/tc_get_user_info_user_found.csv';
        $dataset = $this->createCsvDataSet(array('cases' => $fixture));
        $rows = $dataset->getTable('cases');
        $columns = $dataset->getTableMetaData('cases')->getColumns();

        $cases = array();
        for ($r = 0; $r < $rows->getRowCount(); $r++) {
            $cases[] = (object) array_combine($columns, $rows->getRow($r));
        }

        foreach ($cases as $case) {
            $this->assert_user_found($case, $users, $secret);
        }

    }

    /**
     * Get user info should return user record and roles by course for the target user.
     * User can be requested by internal userid (identitytype = internal),
     * or by username (identitytype != internal).
     *
     * @return void
     */
    public function test_get_user_info_designated_roles() {
        global $DB;

        $this->set_user('admin');

        // Configure secret.
        $secret = 'DF4#R66';
        set_config('block_mhaairs_shared_secret', $secret);

        // Make sure we use specify role short names.
        set_config('block_mhaairs_student_roles', 'student');
        set_config('block_mhaairs_instructor_roles', 'teacher,editingteacher');

        $callback = 'block_mhaairs_utilservice_external::get_user_info';

        $users = $this->add_users_and_enrolments('/fixtures/tc_get_user_info_users.csv');

        // Tese cases dataset.
        // User id, identity type.
        $fixture = __DIR__. '/fixtures/tc_get_user_info_user_found.csv';
        $dataset = $this->createCsvDataSet(array('cases' => $fixture));
        $rows = $dataset->getTable('cases');
        $columns = $dataset->getTableMetaData('cases')->getColumns();

        $cases = array();
        for ($r = 0; $r < $rows->getRowCount(); $r++) {
            $cases[] = (object) array_combine($columns, $rows->getRow($r));
        }

        foreach ($cases as $case) {
            $this->assert_user_found($case, $users, $secret);
        }

    }

    /**
     * Get environment info should return envrionment info including system, server,
     * php version, db vendor and version, moodle version and plugin version.
     *
     * @return void
     */
    public function test_get_environment_info() {
        $this->set_user('admin');

        $callback = 'block_mhaairs_utilservice_external::get_environment_info';
        $result = call_user_func_array($callback, array());

        // Verify environment info.
        $this->assertEquals(true, !empty($result->system));
        $this->assertEquals(true, !empty($result->server));
        $this->assertEquals(true, !empty($result->phpversion));
        $this->assertEquals(true, !empty($result->dbvendor));
        $this->assertEquals(true, !empty($result->dbversion));
        $this->assertEquals(true, !empty($result->moodleversion));
        $this->assertEquals(true, !empty($result->pluginversion));
    }

    /**
     * Asserts invalid token against get_user_info with the specified token and secret.
     * If secret is omitted, try to take the secret from the configuration. The secret
     * parameter is used for creating the encoded token.
     *
     * @param string $token
     * @param string $secret
     * @param boolean $equal Determines the assertion type (assertEquals | assertNotEquals).
     * @return void
     */
    protected function assert_invalid_token($token, $secret = null, $equal = true) {
        // The service function.
        $callback = 'block_mhaairs_utilservice_external::get_user_info';

        // Encode the token.
        $encodedtoken = MHUtil::encode_token2($token, $secret);

        // Service params.
        $serviceparams = array(
            'token' => $encodedtoken, // Token.
            'identitytype' => null, // Identity type.
        );

        $result = call_user_func_array($callback, $serviceparams);
        if ($equal) {
            $this->assertEquals('error: token is invalid', $result->message);
            $this->assertEquals(MHUserInfo::FAILURE, $result->status);
        } else {
            $this->assertNotEquals('error: token is invalid', $result->message);
        }
    }

    /**
     * Asserts get user info failure on user not found.
     * The userid and secret parameters are used for creating an encoded token
     * of the target user.
     *
     * @param string $userid
     * @param string $secret
     * @param string $identitytype
     * @return void
     */
    protected function assert_user_not_found($userid, $secret = null, $identitytype = null) {
        // The service function.
        $callback = 'block_mhaairs_utilservice_external::get_user_info';
        $uservar = MHUtil::get_user_var($identitytype);

        // Create the token.
        $token = MHUtil::create_token($userid);
        $encodedtoken = MHUtil::encode_token2($token, $secret);

        // Service params.
        $serviceparams = array(
            'token' => $encodedtoken, // Token.
            'identitytype' => $identitytype, // Identity type.
        );

        $result = call_user_func_array($callback, $serviceparams);
        $this->assertEquals(MHUserInfo::FAILURE, $result->status);
        $this->assertEquals("error: user with $uservar '$userid' not found", $result->message);
        $this->assertEquals(array(), $result->user);
    }

    /**
     * Asserts successful calls to get user info.
     * The userid and secret parameters are used for creating an encoded token
     * of the target user.
     * The method checks the result status, message, user->username and number of courses.
     *
     * @param string $userid
     * @param string $secret
     * @param string $identitytype
     * @return void
     */
    protected function assert_user_found($case, $users, $secret) {
        global $CFG;

        // The service function.
        $callback = 'block_mhaairs_utilservice_external::get_user_info';
        $uservar = MHUtil::get_user_var($case->identitytype);

        // Get the user id.
        $internal = ($case->identitytype == 'internal');
        $userid = ($internal ? $users[$case->username]->id : $case->username);

        // Create the token.
        $token = MHUtil::create_token($userid);
        $encodedtoken = MHUtil::encode_token2($token, $secret);

        // Service params.
        $serviceparams = array(
            'token' => $encodedtoken, // Token.
            'identitytype' => $case->identitytype, // Identity type.
        );

        $result = call_user_func_array($callback, $serviceparams);
        $this->assertEquals(MHUserInfo::SUCCESS, $result->status);

        // Verify user info.
        $user = $users[$case->username];
        $this->assertEquals('', $result->message);
        $this->assertEquals($user->username, $result->user->username);
        $this->assertEquals($user->email, $result->user->email);

        // Verify course info.
        $coursecount = 0;
        foreach (array('tc2', 'tc3', 'tc4', 'tc5') as $tc) {
            if (!empty($case->$tc)) {
                $coursecount += count(explode(',', $case->$tc));
            }
        }
        $this->assertEquals($coursecount, count($result->courses));
    }

    /**
     * Adds users and enrolments from csv file and returns the list of users by username.
     *
     * @param string $filename
     * @return array
     */
    protected function add_users_and_enrolments($filename) {
        global $DB;

        // Users dataset.
        // Username, course (idnumber), editingteacher, teacher, student.
        $fixture = __DIR__. $filename;
        $dataset = $this->createCsvDataSet(array('cases' => $fixture));
        $rows = $dataset->getTable('cases');
        $columns = $dataset->getTableMetaData('cases')->getColumns();

        $cases = array();
        for ($r = 0; $r < $rows->getRowCount(); $r++) {
            $cases[] = (object) array_combine($columns, $rows->getRow($r));
        }

        // Add users and enrollments.
        $users = array();
        $courses = array();

        $roles = $DB->get_records_menu('role', array(), '', 'shortname,id');
        foreach ($cases as $case) {
            // Add the user if needed.
            if (!array_key_exists($case->username, $users)) {
                $userparams = array('username' => $case->username);
                $user = $this->getDataGenerator()->create_user($userparams);
                $users[$user->username] = $user;
            }
            $userid = $users[$case->username]->id;

            // Add the course if needed.
            if (!array_key_exists($case->course, $courses)) {
                $record = array('idnumber' => $case->course);
                $course = $this->getDataGenerator()->create_course($record);
                $courses[$course->idnumber] = $course;
            }
            $courseid = $courses[$case->course]->id;

            // Add enrollments.
            foreach ($roles as $shortname => $roleid) {
                if (!empty($case->$shortname)) {
                    $this->getDataGenerator()->enrol_user($userid, $courseid, $roleid);
                }
            }
        }

        return $users;
    }

}
