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

use core\oauth2\api;
use core_external\external_api;
use externallib_advanced_testcase;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/lib/tests/moodlenet/helpers.php');
require_once($CFG->dirroot . '/webservice/tests/helpers.php');

/**
 * External functions test for moodlenet_send_activity.
 *
 * @package    core
 * @category   test
 * @copyright  2023 Huong Nguyen <huongnv13@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \core\external\moodlenet_send_activity
 */
class moodlenet_send_activity_test extends externallib_advanced_testcase {

    /**
     * Test the behaviour of moodlenet_send_activity().
     *
     * @covers ::execute
     */
    public function test_moodlenet_send_activity(): void {
        global $CFG;
        $this->resetAfterTest();
        $this->setAdminUser();

        // Generate data.
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $moduleinstance = $generator->create_module('assign', ['course' => $course->id]);
        $user = $generator->create_user();
        $generator->enrol_user($user->id, $course->id, 'student');

        // Create dummy issuer.
        $issuer = \core\moodlenet\helpers::get_mock_issuer(0);

        // Test with the experimental flag off.
        $result = moodlenet_send_activity::execute($issuer->get('id'), $moduleinstance->cmid, 0);
        $result = external_api::clean_returnvalue(moodlenet_send_activity::execute_returns(), $result);
        $this->assertFalse($result['status']);
        $this->assertNotEmpty($result['warnings']);
        $this->assertEquals('errorissuernotenabled', $result['warnings'][0]['warningcode']);

        $CFG->enablesharingtomoodlenet = true;

        // Test with invalid format.
        $result = moodlenet_send_activity::execute($issuer->get('id'), $moduleinstance->cmid, 5);
        $result = external_api::clean_returnvalue(moodlenet_send_activity::execute_returns(), $result);
        $this->assertFalse($result['status']);
        $this->assertNotEmpty($result['warnings']);
        $this->assertEquals('errorinvalidformat', $result['warnings'][0]['warningcode']);

        // Test with the user does not have permission.
        $this->setUser($user);
        $result = moodlenet_send_activity::execute($issuer->get('id'), $moduleinstance->cmid, 0);
        $result = external_api::clean_returnvalue(moodlenet_send_activity::execute_returns(), $result);
        $this->assertFalse($result['status']);
        $this->assertNotEmpty($result['warnings']);
        $this->assertEquals('errorpermission', $result['warnings'][0]['warningcode']);

        $this->setAdminUser();

        // Test with the issuer is not enabled.
        $result = moodlenet_send_activity::execute($issuer->get('id'), $moduleinstance->cmid, 0);
        $result = external_api::clean_returnvalue(moodlenet_send_activity::execute_returns(), $result);
        $this->assertFalse($result['status']);
        $this->assertNotEmpty($result['warnings']);
        $this->assertEquals('errorissuernotenabled', $result['warnings'][0]['warningcode']);

        // Test with the issuer is enabled but not set in the MN Outbound setting.
        $issuer->set('enabled', 1);
        $irecord = $issuer->to_record();
        api::update_issuer($irecord);
        $result = moodlenet_send_activity::execute($issuer->get('id'), $moduleinstance->cmid, 0);
        $result = external_api::clean_returnvalue(moodlenet_send_activity::execute_returns(), $result);
        $this->assertFalse($result['status']);
        $this->assertNotEmpty($result['warnings']);
        $this->assertEquals('errorissuernotenabled', $result['warnings'][0]['warningcode']);

        set_config('oauthservice', $issuer->get('id'), 'moodlenet');
        // Test with the issuer not yet authorized.
        $result = moodlenet_send_activity::execute($issuer->get('id'), $moduleinstance->cmid, 0);
        $result = external_api::clean_returnvalue(moodlenet_send_activity::execute_returns(), $result);
        $this->assertFalse($result['status']);
        $this->assertNotEmpty($result['warnings']);
        $this->assertEquals('erroroauthclient', $result['warnings'][0]['warningcode']);
        $this->assertEquals($issuer->get('id'), $result['warnings'][0]['item']);
        $this->assertEquals(get_string('moodlenet:issuerisnotauthorized', 'moodle'), $result['warnings'][0]['message']);
    }

    /**
     * Test execute_returns() method.
     *
     * @dataProvider return_resource_url_provider
     * @covers ::execute_returns
     */
    public function test_moodlenet_send_activity_return_resource_url(bool $state, string $resourceurl): void {
        $this->resetAfterTest();
        // Create dummy result with the resourceurl.
        $result = [
            'status' => true,
            'resourceurl' => $resourceurl,
            'warnings' => [],
        ];
        if (!$state) {
            $this->expectException(\invalid_response_exception::class);
        }
        $result = external_api::clean_returnvalue(moodlenet_send_activity::execute_returns(), $result);
        if ($state) {
            $this->assertEquals($resourceurl, $result['resourceurl']);
        }
    }

    /**
     * Provider for test_moodlenet_send_activity_return_resource_url().
     *
     * @return array Test data.
     */
    public static function return_resource_url_provider(): array {
        return [
            'Success 1' => [
                true,
                'https://moodlenet.example.com/drafts/view/testactivity_backup.mbz',
            ],
            'Success 2' => [
                true,
                'https://moodlenet.example.com/drafts/view/testactivity_backup with spaces.mbz',
            ],
            'Success 3' => [
                true,
                'https://moodlenet.example.com/drafts/view/testactivity_backup with " character.mbz',
            ],
            'Success 4' => [
                true,
                "https://moodlenet.example.com/drafts/view/testactivity_backup with ' character.mbz",
            ],
            'Success 5' => [
                true,
                'https://moodlenet.example.com/drafts/view/testactivity_backup with < and > characters.mbz',
            ],
            'Fail 1' => [
                false,
                'https://moodlenet.example.com/drafts/view/testactivity_backupwith<lang lang="en">a<a</lang>html.mbz',
            ],
        ];
    }
}
