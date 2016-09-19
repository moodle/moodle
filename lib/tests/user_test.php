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
 * Tests core_user class.
 *
 * @package    core
 * @copyright  2013 Rajesh Taneja <rajesh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Test core_user class.
 *
 * @package    core
 * @copyright  2013 Rajesh Taneja <rajesh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_user_testcase extends advanced_testcase {

    /**
     * Setup test data.
     */
    protected function setUp() {
        $this->resetAfterTest(true);
    }

    public function test_get_user() {
        global $CFG;


        // Create user and try fetach it with api.
        $user = $this->getDataGenerator()->create_user();
        $this->assertEquals($user, core_user::get_user($user->id, '*', MUST_EXIST));

        // Test noreply user.
        $CFG->noreplyuserid = null;
        $noreplyuser = core_user::get_noreply_user();
        $this->assertEquals(1, $noreplyuser->emailstop);
        $this->assertFalse(core_user::is_real_user($noreplyuser->id));
        $this->assertEquals($CFG->noreplyaddress, $noreplyuser->email);
        $this->assertEquals(get_string('noreplyname'), $noreplyuser->firstname);

        // Set user as noreply user and make sure noreply propery is set.
        core_user::reset_internal_users();
        $CFG->noreplyuserid = $user->id;
        $noreplyuser = core_user::get_noreply_user();
        $this->assertEquals(1, $noreplyuser->emailstop);
        $this->assertTrue(core_user::is_real_user($noreplyuser->id));

        // Test support user.
        core_user::reset_internal_users();
        $CFG->supportemail = null;
        $CFG->noreplyuserid = null;
        $supportuser = core_user::get_support_user();
        $adminuser = get_admin();
        $this->assertEquals($adminuser, $supportuser);
        $this->assertTrue(core_user::is_real_user($supportuser->id));

        // When supportemail is set.
        core_user::reset_internal_users();
        $CFG->supportemail = 'test@example.com';
        $supportuser = core_user::get_support_user();
        $this->assertEquals(core_user::SUPPORT_USER, $supportuser->id);
        $this->assertFalse(core_user::is_real_user($supportuser->id));

        // Set user as support user and make sure noreply propery is set.
        core_user::reset_internal_users();
        $CFG->supportuserid = $user->id;
        $supportuser = core_user::get_support_user();
        $this->assertEquals($user, $supportuser);
        $this->assertTrue(core_user::is_real_user($supportuser->id));
    }

    /**
     * Test get_user_by_username method.
     */
    public function test_get_user_by_username() {
        $record = array();
        $record['username'] = 'johndoe';
        $record['email'] = 'johndoe@example.com';
        $record['timecreated'] = time();

        // Create a default user for the test.
        $userexpected = $this->getDataGenerator()->create_user($record);

        // Assert that the returned user is the espected one.
        $this->assertEquals($userexpected, core_user::get_user_by_username('johndoe'));

        // Assert that a subset of fields is correctly returned.
        $this->assertEquals((object) $record, core_user::get_user_by_username('johndoe', 'username,email,timecreated'));

        // Assert that a user with a different mnethostid will no be returned.
        $this->assertFalse(core_user::get_user_by_username('johndoe', 'username,email,timecreated', 2));

        // Create a new user from a different host.
        $record['mnethostid'] = 2;
        $userexpected2 = $this->getDataGenerator()->create_user($record);

        // Assert that the new user is returned when specified the correct mnethostid.
        $this->assertEquals($userexpected2, core_user::get_user_by_username('johndoe', '*', 2));

        // Assert that a user not in the db return false.
        $this->assertFalse(core_user::get_user_by_username('janedoe'));
    }

    /**
     * Test require_active_user
     */
    public function test_require_active_user() {
        global $DB;

        // Create a default user for the test.
        $userexpected = $this->getDataGenerator()->create_user();

        // Simple case, all good.
        core_user::require_active_user($userexpected, true, true);

        // Set user not confirmed.
        $DB->set_field('user', 'confirmed', 0, array('id' => $userexpected->id));
        try {
            core_user::require_active_user($userexpected);
        } catch (moodle_exception $e) {
            $this->assertEquals('usernotconfirmed', $e->errorcode);
        }
        $DB->set_field('user', 'confirmed', 1, array('id' => $userexpected->id));

        // Set nologin auth method.
        $DB->set_field('user', 'auth', 'nologin', array('id' => $userexpected->id));
        try {
            core_user::require_active_user($userexpected, false, true);
        } catch (moodle_exception $e) {
            $this->assertEquals('suspended', $e->errorcode);
        }
        // Check no exceptions are thrown if we don't specify to check suspended.
        core_user::require_active_user($userexpected);
        $DB->set_field('user', 'auth', 'manual', array('id' => $userexpected->id));

        // Set user suspended.
        $DB->set_field('user', 'suspended', 1, array('id' => $userexpected->id));
        try {
            core_user::require_active_user($userexpected, true);
        } catch (moodle_exception $e) {
            $this->assertEquals('suspended', $e->errorcode);
        }
        // Check no exceptions are thrown if we don't specify to check suspended.
        core_user::require_active_user($userexpected);

        // Delete user.
        delete_user($userexpected);
        try {
            core_user::require_active_user($userexpected);
        } catch (moodle_exception $e) {
            $this->assertEquals('userdeleted', $e->errorcode);
        }

        // Use a not real user.
        $noreplyuser = core_user::get_noreply_user();
        try {
            core_user::require_active_user($noreplyuser, true);
        } catch (moodle_exception $e) {
            $this->assertEquals('invaliduser', $e->errorcode);
        }

        // Get the guest user.
        $guestuser = $DB->get_record('user', array('username' => 'guest'));
        try {
            core_user::require_active_user($guestuser, true);
        } catch (moodle_exception $e) {
            $this->assertEquals('guestsarenotallowed', $e->errorcode);
        }

    }

    /**
     * Test get_property_definition() method.
     */
    public function test_get_property_definition() {
        // Try to get a existing property.
        $properties = core_user::get_property_definition('id');
        $this->assertEquals($properties['type'], PARAM_INT);
        $properties = core_user::get_property_definition('username');
        $this->assertEquals($properties['type'], PARAM_USERNAME);

        // Invalid property.
        try {
            core_user::get_property_definition('fullname');
        } catch (coding_exception $e) {
            $this->assertRegExp('/Invalid property requested./', $e->getMessage());
        }

        // Empty parameter.
        try {
            core_user::get_property_definition('');
        } catch (coding_exception $e) {
            $this->assertRegExp('/Invalid property requested./', $e->getMessage());
        }
    }

    /**
     * Test validate() method.
     */
    public function test_validate() {

        // Create user with just with username and firstname.
        $record = array('username' => 's10', 'firstname' => 'Bebe Stevens');
        $validation = core_user::validate((object)$record);

        // Validate the user, should return true as the user data is correct.
        $this->assertTrue($validation);

        // Create user with incorrect data (invalid country and theme).
        $record = array('username' => 's1', 'firstname' => 'Eric Cartman', 'country' => 'UU', 'theme' => 'beise');

        // Should return an array with 2 errors.
        $validation = core_user::validate((object)$record);
        $this->assertArrayHasKey('country', $validation);
        $this->assertArrayHasKey('theme', $validation);
        $this->assertCount(2, $validation);

        // Create user with malicious data (xss).
        $record = array('username' => 's3', 'firstname' => 'Kyle<script>alert(1);<script> Broflovski');

        // Should return an array with 1 error.
        $validation = core_user::validate((object)$record);
        $this->assertCount(1, $validation);
        $this->assertArrayHasKey('firstname', $validation);
    }

    /**
     * Test clean_data() method.
     */
    public function test_clean_data() {
        $this->resetAfterTest(false);

        $user = new stdClass();
        $user->firstname = 'John <script>alert(1)</script> Doe';
        $user->username = 'john%#&~%*_doe';
        $user->email = ' john@testing.com ';
        $user->deleted = 'no';
        $user->description = '<b>A description <script>alert(123);</script>about myself.</b>';
        $usercleaned = core_user::clean_data($user);

        // Expected results.
        $this->assertEquals('John alert(1) Doe', $usercleaned->firstname);
        $this->assertEquals('john@testing.com', $usercleaned->email);
        $this->assertEquals(0, $usercleaned->deleted);
        $this->assertEquals('<b>A description <script>alert(123);</script>about myself.</b>', $user->description);
        $this->assertEquals('john_doe', $user->username);

        // Try to clean an invalid property (userfullname).
        $user->userfullname = 'John Doe';
        core_user::clean_data($user);
        $this->assertDebuggingCalled("The property 'userfullname' could not be cleaned.");
    }

    /**
     * Test clean_field() method.
     */
    public function test_clean_field() {

        // Create a 'malicious' user object/
        $user = new stdClass();
        $user->firstname = 'John <script>alert(1)</script> Doe';
        $user->username = 'john%#&~%*_doe';
        $user->email = ' john@testing.com ';
        $user->deleted = 'no';
        $user->description = '<b>A description <script>alert(123);</script>about myself.</b>';
        $user->userfullname = 'John Doe';

        // Expected results.
        $this->assertEquals('John alert(1) Doe', core_user::clean_field($user->firstname, 'firstname'));
        $this->assertEquals('john_doe', core_user::clean_field($user->username, 'username'));
        $this->assertEquals('john@testing.com', core_user::clean_field($user->email, 'email'));
        $this->assertEquals(0, core_user::clean_field($user->deleted, 'deleted'));
        $this->assertEquals('<b>A description <script>alert(123);</script>about myself.</b>', core_user::clean_field($user->description, 'description'));

        // Try to clean an invalid property (fullname).
        core_user::clean_field($user->userfullname, 'fullname');
        $this->assertDebuggingCalled("The property 'fullname' could not be cleaned.");
    }

    /**
     * Test get_property_type() method.
     */
    public function test_get_property_type() {

        // Fetch valid properties and verify if the type is correct.
        $type = core_user::get_property_type('username');
        $this->assertEquals(PARAM_USERNAME, $type);
        $type = core_user::get_property_type('email');
        $this->assertEquals(PARAM_RAW_TRIMMED, $type);
        $type = core_user::get_property_type('timezone');
        $this->assertEquals(PARAM_TIMEZONE, $type);

        // Try to fetch type of a non-existent properties.
        $nonexistingproperty = 'userfullname';
        $this->expectException('coding_exception');
        $this->expectExceptionMessage('Invalid property requested: ' . $nonexistingproperty);
        core_user::get_property_type($nonexistingproperty);
        $nonexistingproperty = 'mobilenumber';
        $this->expectExceptionMessage('Invalid property requested: ' . $nonexistingproperty);
        core_user::get_property_type($nonexistingproperty);
    }

    /**
     * Test get_property_null() method.
     */
    public function test_get_property_null() {
        // Fetch valid properties and verify if it is NULL_ALLOWED or NULL_NOT_ALLOWED.
        $property = core_user::get_property_null('username');
        $this->assertEquals(NULL_NOT_ALLOWED, $property);
        $property = core_user::get_property_null('password');
        $this->assertEquals(NULL_NOT_ALLOWED, $property);
        $property = core_user::get_property_null('imagealt');
        $this->assertEquals(NULL_ALLOWED, $property);
        $property = core_user::get_property_null('middlename');
        $this->assertEquals(NULL_ALLOWED, $property);

        // Try to fetch type of a non-existent properties.
        $nonexistingproperty = 'lastnamefonetic';
        $this->expectException('coding_exception');
        $this->expectExceptionMessage('Invalid property requested: ' . $nonexistingproperty);
        core_user::get_property_null($nonexistingproperty);
        $nonexistingproperty = 'midlename';
        $this->expectExceptionMessage('Invalid property requested: ' . $nonexistingproperty);
        core_user::get_property_null($nonexistingproperty);
    }

    /**
     * Test get_property_choices() method.
     */
    public function test_get_property_choices() {

        // Test against country property choices.
        $choices = core_user::get_property_choices('country');
        $this->assertArrayHasKey('AU', $choices);
        $this->assertArrayHasKey('BR', $choices);
        $this->assertArrayNotHasKey('WW', $choices);
        $this->assertArrayNotHasKey('TX', $choices);

        // Test against lang property choices.
        $choices = core_user::get_property_choices('lang');
        $this->assertArrayHasKey('en', $choices);
        $this->assertArrayNotHasKey('ww', $choices);
        $this->assertArrayNotHasKey('yy', $choices);

        // Test against theme property choices.
        $choices = core_user::get_property_choices('theme');
        $this->assertArrayHasKey('bootstrapbase', $choices);
        $this->assertArrayHasKey('clean', $choices);
        $this->assertArrayNotHasKey('unknowntheme', $choices);
        $this->assertArrayNotHasKey('wrongtheme', $choices);

        // Try to fetch type of a non-existent properties.
        $nonexistingproperty = 'language';
        $this->expectException('coding_exception');
        $this->expectExceptionMessage('Invalid property requested: ' . $nonexistingproperty);
        core_user::get_property_null($nonexistingproperty);
        $nonexistingproperty = 'coutries';
        $this->expectExceptionMessage('Invalid property requested: ' . $nonexistingproperty);
        core_user::get_property_null($nonexistingproperty);
    }

    /**
     * Test get_property_default().
     *
     *
     * @expectedException        coding_exception
     * @expectedExceptionMessage Invalid property requested, or the property does not has a default value.
     */
    public function test_get_property_default() {
        global $CFG;
        $this->resetAfterTest();

        $country = core_user::get_property_default('country');
        $this->assertEquals($CFG->country, $country);
        set_config('country', 'AU');
        core_user::reset_caches();
        $country = core_user::get_property_default('country');
        $this->assertEquals($CFG->country, $country);

        $lang = core_user::get_property_default('lang');
        $this->assertEquals($CFG->lang, $lang);
        set_config('lang', 'en');
        $lang = core_user::get_property_default('lang');
        $this->assertEquals($CFG->lang, $lang);

        $this->setTimezone('Europe/London', 'Pacific/Auckland');
        core_user::reset_caches();
        $timezone = core_user::get_property_default('timezone');
        $this->assertEquals('Europe/London', $timezone);
        $this->setTimezone('99', 'Pacific/Auckland');
        core_user::reset_caches();
        $timezone = core_user::get_property_default('timezone');
        $this->assertEquals('Pacific/Auckland', $timezone);

        core_user::get_property_default('firstname');
    }

    /**
     * Ensure that the noreply user is not cached.
     */
    public function test_get_noreply_user() {
        global $CFG;

        // Create a new fake language 'xx' with the 'noreplyname'.
        $langfolder = $CFG->dataroot . '/lang/xx';
        check_dir_exists($langfolder);
        $langconfig = "<?php\n\defined('MOODLE_INTERNAL') || die();";
        file_put_contents($langfolder . '/langconfig.php', $langconfig);
        $langconfig = "<?php\n\$string['noreplyname'] = 'XXX';";
        file_put_contents($langfolder . '/moodle.php', $langconfig);

        $CFG->lang='en';
        $enuser = \core_user::get_noreply_user();

        $CFG->lang='xx';
        $xxuser = \core_user::get_noreply_user();

        $this->assertNotEquals($enuser, $xxuser);
    }

}
