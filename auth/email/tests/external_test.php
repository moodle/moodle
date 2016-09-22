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
 * Auth email external functions tests.
 *
 * @package    auth_email
 * @category   external
 * @copyright  2016 Juan Leyva
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.2
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');

/**
 * External auth email API tests.
 *
 * @package     auth_email
 * @copyright   2016 Juan Leyva
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since       Moodle 3.2
 */
class auth_email_external_testcase extends externallib_advanced_testcase {

    /**
     * Set up for every test
     */
    public function setUp() {
        global $CFG, $DB;

        $this->resetAfterTest(true);
        $CFG->registerauth = 'email';

        $categoryid = $DB->insert_record('user_info_category', array('name' => 'Cat 1', 'sortorder' => 1));
        $field = $DB->insert_record('user_info_field', array(
                'shortname' => 'frogname', 'name' => 'Name of frog', 'categoryid' => $categoryid,
                'datatype' => 'text', 'signup' => 1, 'visible' => 1));
    }

    public function test_get_signup_settings() {
        global $CFG;

        $CFG->defaultcity = 'Bcn';
        $CFG->country = 'ES';
        $CFG->sitepolicy = 'https://moodle.org';

        $result = auth_email_external::get_signup_settings();
        $result = external_api::clean_returnvalue(auth_email_external::get_signup_settings_returns(), $result);

        // Check expected data.
        $this->assertEquals(array('firstname', 'lastname'), $result['namefields']);
        $this->assertEquals($CFG->defaultcity, $result['defaultcity']);
        $this->assertEquals($CFG->country, $result['country']);
        $this->assertEquals($CFG->sitepolicy, $result['sitepolicy']);
        $this->assertEquals(print_password_policy(), $result['passwordpolicy']);
        $this->assertNotContains('recaptchachallengehash', $result);
        $this->assertNotContains('recaptchachallengeimage', $result);
        $this->assertCount(1, $result['profilefields']);
        $this->assertEquals('text', $result['profilefields'][0]['datatype']);
    }

}
