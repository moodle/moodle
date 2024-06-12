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
 * External functions test for moodlenet_get_share_info_activity.
 *
 * @package    core
 * @category   test
 * @copyright  2023 Huong Nguyen <huongnv13@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \core\external\moodlenet_get_share_info_activity
 */
class moodlenet_get_share_info_activity_test extends externallib_advanced_testcase {

    /**
     * Test the behaviour of moodlenet_get_share_info_activity().
     * @covers ::execute
     */
    public function test_moodlenet_get_share_info_activity(): void {
        global $CFG;
        $this->resetAfterTest();
        $this->setAdminUser();
        $CFG->enablesharingtomoodlenet = true;

        // Generate course and activities.
        $course = $this->getDataGenerator()->create_course();
        $activity1 = $this->getDataGenerator()->create_module('chat', ['course' => $course->id, 'name' => 'Chat activity']);
        $activity2 = $this->getDataGenerator()->create_module('assign', ['course' => $course->id, 'name' => 'Assign activity']);
        $activity3 = $this->getDataGenerator()->create_module('quiz', ['course' => $course->id, 'name' => 'Quiz activity']);

        // Create dummy enabled issuer.
        $issuer = \core\moodlenet\helpers::get_mock_issuer(1);

        // Test the 1st activity with no OAuth2 setup yet.
        $result = moodlenet_get_share_info_activity::execute($activity1->cmid);
        $result = external_api::clean_returnvalue(moodlenet_get_share_info_activity::execute_returns(), $result);
        $this->assertFalse($result['status']);
        $this->assertEmpty($result['name']);
        $this->assertEmpty($result['type']);
        $this->assertEmpty($result['server']);
        $this->assertEmpty($result['supportpageurl']);
        $this->assertNotEmpty($result['warnings']);
        $this->assertEquals(0, $result['warnings'][0]['item']);
        $this->assertEquals('errorissuernotset', $result['warnings'][0]['warningcode']);
        $this->assertEquals(get_string('moodlenet:issuerisnotset', 'moodle'), $result['warnings'][0]['message']);

        // Test the 1st activity with OAuth2 disabled.
        set_config('oauthservice', $issuer->get('id'), 'moodlenet');
        $issuer->set('enabled', 0);
        $irecord = $issuer->to_record();
        api::update_issuer($irecord);

        $result = moodlenet_get_share_info_activity::execute($activity1->cmid);
        $result = external_api::clean_returnvalue(moodlenet_get_share_info_activity::execute_returns(), $result);
        $this->assertFalse($result['status']);
        $this->assertEmpty($result['name']);
        $this->assertEmpty($result['type']);
        $this->assertEmpty($result['server']);
        $this->assertEmpty($result['supportpageurl']);
        $this->assertNotEmpty($result['warnings']);
        $this->assertEquals($issuer->get('id'), $result['warnings'][0]['item']);
        $this->assertEquals('errorissuernotenabled', $result['warnings'][0]['warningcode']);
        $this->assertEquals(get_string('moodlenet:issuerisnotenabled', 'moodle'), $result['warnings'][0]['message']);

        // Test the 1st activity with support url is set to the internal contact site support page.
        $issuer->set('enabled', 1);
        $irecord = $issuer->to_record();
        api::update_issuer($irecord);

        $expectedsupporturl = $CFG->wwwroot . '/user/contactsitesupport.php';
        $result = moodlenet_get_share_info_activity::execute($activity1->cmid);
        $result = external_api::clean_returnvalue(moodlenet_get_share_info_activity::execute_returns(), $result);
        $this->assertTrue($result['status']);
        $this->assertEquals($activity1->name, $result['name']);
        $this->assertEquals(get_string('modulename', 'mod_chat'), $result['type']);
        $this->assertEquals($issuer->get_display_name(), $result['server']);
        $this->assertEquals($expectedsupporturl, $result['supportpageurl']);

        // Test the 2nd activity with support url is set to the external contact site support page.
        $expectedsupporturl = 'https://moodle.org/';
        $CFG->supportpage = $expectedsupporturl;
        $result = moodlenet_get_share_info_activity::execute($activity2->cmid);
        $result = external_api::clean_returnvalue(moodlenet_get_share_info_activity::execute_returns(), $result);
        $this->assertTrue($result['status']);
        $this->assertEquals($activity2->name, $result['name']);
        $this->assertEquals(get_string('modulename', 'mod_assign'), $result['type']);
        $this->assertEquals($expectedsupporturl, $result['supportpageurl']);

        // Test the 3rd activity with contact site support is disabled.
        $CFG->supportavailability = CONTACT_SUPPORT_DISABLED;
        $result = moodlenet_get_share_info_activity::execute($activity3->cmid);
        $result = external_api::clean_returnvalue(moodlenet_get_share_info_activity::execute_returns(), $result);
        $this->assertTrue($result['status']);
        $this->assertEquals($activity3->name, $result['name']);
        $this->assertEquals(get_string('modulename', 'mod_quiz'), $result['type']);
        $this->assertEmpty($result['supportpageurl']);

        // Test with an invalid activity.
        // Get a random cmid that not in the created activity list.
        $cmids = [$activity1->cmid, $activity2->cmid, $activity3->cmid];
        do {
            $randomcmid = random_int(5, 25);
        } while (in_array($randomcmid, $cmids));
        $result = moodlenet_get_share_info_activity::execute($randomcmid);
        $result = external_api::clean_returnvalue(moodlenet_get_share_info_activity::execute_returns(), $result);
        $this->assertFalse($result['status']);
        $this->assertEmpty($result['name']);
        $this->assertEmpty($result['type']);
        $this->assertNotEmpty($result['warnings']);
        $this->assertEquals($randomcmid, $result['warnings'][0]['item']);
        $this->assertEquals('errorgettingactivityinformation', $result['warnings'][0]['warningcode']);
        $this->assertEquals(get_string('invalidcoursemodule', 'error'), $result['warnings'][0]['message']);
    }
}
