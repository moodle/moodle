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
        $this->field1 = $DB->insert_record('user_info_field', array(
                'shortname' => 'frogname', 'name' => 'Name of frog', 'categoryid' => $categoryid,
                'datatype' => 'text', 'signup' => 1, 'visible' => 1, 'required' => 1, 'sortorder' => 1));
        $this->field2 = $DB->insert_record('user_info_field', array(
                'shortname' => 'sometext', 'name' => 'Some text in textarea', 'categoryid' => $categoryid,
                'datatype' => 'textarea', 'signup' => 1, 'visible' => 1, 'required' => 1, 'sortorder' => 2));
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
        $this->assertCount(2, $result['profilefields']);
        $this->assertEquals('text', $result['profilefields'][0]['datatype']);
        $this->assertEquals('textarea', $result['profilefields'][1]['datatype']);
    }

    public function test_signup_user() {
        global $DB;

        $username = 'pepe';
        $password = 'abcdefAª.ªª!!3';
        $firstname = 'Pepe';
        $lastname = 'Pérez';
        $email = 'myemail@no.zbc';
        $city = 'Bcn';
        $country = 'ES';
        $customprofilefields = array(
            array(
                'type' => 'text',
                'name' => 'profile_field_frogname',
                'value' => 'random text',
            ),
            array(
                'type' => 'textarea',
                'name' => 'profile_field_sometext',
                'value' => json_encode(
                    array(
                        'text' => 'blah blah',
                        'format' => FORMAT_HTML
                    )
                ),
            )
        );

        // Create new user.
        $result = auth_email_external::signup_user($username, $password, $firstname, $lastname, $email, $city,  $country,
                                                    '', '', $customprofilefields);
        $result = external_api::clean_returnvalue(auth_email_external::signup_user_returns(), $result);
        $this->assertTrue($result['success']);
        $this->assertEmpty($result['warnings']);
        $user = $DB->get_record('user', array('username' => $username));
        $this->assertEquals($firstname, $user->firstname);
        $this->assertEquals($lastname, $user->lastname);
        $this->assertEquals($email, $user->email);
        $this->assertEquals($city, $user->city);
        $this->assertEquals($country, $user->country);
        $this->assertEquals(0, $user->confirmed);
        $this->assertEquals(current_language(), $user->lang);
        $this->assertEquals('email', $user->auth);
        $infofield = $DB->get_record('user_info_data', array('userid' => $user->id, 'fieldid' => $this->field1));
        $this->assertEquals($customprofilefields[0]['value'], $infofield->data);
        $infofield = $DB->get_record('user_info_data', array('userid' => $user->id, 'fieldid' => $this->field2));
        $this->assertEquals(json_decode($customprofilefields[1]['value'])->text, $infofield->data);

        // Try to create a user with the same username, email and password. We ommit also the profile fields.
        $password = 'abc';
        $result = auth_email_external::signup_user($username, $password, $firstname, $lastname, $email, $city,  $country,
                                                    '', '', $customprofilefields);
        $result = external_api::clean_returnvalue(auth_email_external::signup_user_returns(), $result);
        $this->assertFalse($result['success']);
        $this->assertCount(3, $result['warnings']);
        $expectederrors = array('username', 'email', 'password');
        $finalerrors = [];
        foreach ($result['warnings'] as $warning) {
            $finalerrors[] = $warning['item'];
        }
        $this->assertEquals($expectederrors, $finalerrors);

        // Do not pass the required profile fields.
        $this->expectException('invalid_parameter_exception');
        $result = auth_email_external::signup_user($username, $password, $firstname, $lastname, $email, $city,  $country);
    }
}
