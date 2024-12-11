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

namespace core\external;

defined('MOODLE_INTERNAL') || die();

use core\oauth2\api;
use core_external\external_api;
use externallib_advanced_testcase;

global $CFG;

require_once($CFG->dirroot . '/lib/tests/moodlenet/helpers.php');
require_once($CFG->dirroot . '/webservice/tests/helpers.php');

/**
 * External functions test for moodlenet_auth_check.
 *
 * @package    core
 * @category   test
 * @copyright  2023 Huong Nguyen <huongnv13@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \core\external\moodlenet_auth_check
 */
final class moodlenet_auth_check_test extends externallib_advanced_testcase {

    /**
     * Test the behaviour of moodlenet_auth_check().
     *
     * @covers ::execute
     */
    public function test_moodlenet_auth_check(): void {
        global $CFG;
        $this->resetAfterTest();
        $this->setAdminUser();
        $CFG->enablesharingtomoodlenet = true;

        // Generate data.
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $user = $generator->create_user();
        $generator->enrol_user($user->id, $course->id, 'student');

        // Create dummy issuer.
        $issuer = \core\moodlenet\helpers::get_mock_issuer(0);

        // Test with the user does not have permission.
        $this->setUser($user);
        $result = moodlenet_auth_check::execute($issuer->get('id'), $course->id);
        $result = external_api::clean_returnvalue(moodlenet_auth_check::execute_returns(), $result);
        $this->assertFalse($result['status']);
        $this->assertNotEmpty($result['warnings']);
        $this->assertEquals('errorpermission', $result['warnings'][0]['warningcode']);

        // Test with the issuer is not enabled.
        $this->setAdminUser();
        $result = moodlenet_auth_check::execute($issuer->get('id'), $course->id);
        $result = external_api::clean_returnvalue(moodlenet_auth_check::execute_returns(), $result);
        $this->assertFalse($result['status']);
        $this->assertNotEmpty($result['warnings']);
        $this->assertEquals('errorissuernotenabled', $result['warnings'][0]['warningcode']);

        // Test with the issuer is enabled and not logged in.
        $issuer->set('enabled', 1);
        $irecord = $issuer->to_record();
        api::update_issuer($irecord);
        set_config('oauthservice', $issuer->get('id'), 'moodlenet');
        $result = moodlenet_auth_check::execute($issuer->get('id'), $course->id);
        $result = external_api::clean_returnvalue(moodlenet_auth_check::execute_returns(), $result);
        $this->assertFalse($result['status']);
        $this->assertNotEmpty($result['loginurl']);
    }
}
