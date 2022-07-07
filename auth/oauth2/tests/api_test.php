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
 * Auth oauth2 api functions tests.
 *
 * @package     auth_oauth2
 * @copyright   2017 Damyon Wiese
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

/**
 * External auth oauth2 API tests.
 *
 * @package     auth_oauth2
 * @copyright   2017 Damyon Wiese
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class auth_oauth2_external_testcase extends advanced_testcase {

    /**
     * Test the cleaning of orphaned linked logins for all issuers.
     */
    public function test_clean_orphaned_linked_logins() {
        $this->resetAfterTest();
        $this->setAdminUser();

        $issuer = \core\oauth2\api::create_standard_issuer('google');
        \core\oauth2\api::create_standard_issuer('microsoft');

        $user = $this->getDataGenerator()->create_user();
        $info = [];
        $info['username'] = 'banana';
        $info['email'] = 'banana@example.com';
        \auth_oauth2\api::link_login($info, $issuer, $user->id, false);

        \core\oauth2\api::delete_issuer($issuer->get('id'));

        $linkedlogins = \auth_oauth2\api::get_linked_logins($user->id, $issuer);
        $this->assertCount(1, $linkedlogins);

        \auth_oauth2\api::clean_orphaned_linked_logins();

        $linkedlogins = \auth_oauth2\api::get_linked_logins($user->id, $issuer);
        $this->assertCount(0, $linkedlogins);

        $match = \auth_oauth2\api::match_username_to_user('banana', $issuer);
        $this->assertFalse($match);
    }

    /**
     * Test the cleaning of orphaned linked logins for a specific issuer.
     */
    public function test_clean_orphaned_linked_logins_with_issuer_id() {
        $this->resetAfterTest();
        $this->setAdminUser();

        $issuer1 = \core\oauth2\api::create_standard_issuer('google');
        $issuer2 = \core\oauth2\api::create_standard_issuer('microsoft');

        $user1 = $this->getDataGenerator()->create_user();
        $info = [];
        $info['username'] = 'banana';
        $info['email'] = 'banana@example.com';
        \auth_oauth2\api::link_login($info, $issuer1, $user1->id, false);

        $user2 = $this->getDataGenerator()->create_user();
        $info = [];
        $info['username'] = 'apple';
        $info['email'] = 'apple@example.com';
        \auth_oauth2\api::link_login($info, $issuer2, $user2->id, false);

        \core\oauth2\api::delete_issuer($issuer1->get('id'));

        \auth_oauth2\api::clean_orphaned_linked_logins($issuer1->get('id'));

        $linkedlogins = \auth_oauth2\api::get_linked_logins($user1->id, $issuer1);
        $this->assertCount(0, $linkedlogins);

        $linkedlogins = \auth_oauth2\api::get_linked_logins($user2->id, $issuer2);
        $this->assertCount(1, $linkedlogins);
    }

    /**
     * Test auto-confirming linked logins.
     */
    public function test_linked_logins() {
        $this->resetAfterTest();

        $this->setAdminUser();
        $issuer = \core\oauth2\api::create_standard_issuer('google');

        $user = $this->getDataGenerator()->create_user();

        $info = [];
        $info['username'] = 'banana';
        $info['email'] = 'banana@example.com';

        \auth_oauth2\api::link_login($info, $issuer, $user->id, false);

        // Try and match a user with a linked login.
        $match = \auth_oauth2\api::match_username_to_user('banana', $issuer);

        $this->assertEquals($user->id, $match->get('userid'));
        $linkedlogins = \auth_oauth2\api::get_linked_logins($user->id, $issuer);
        \auth_oauth2\api::delete_linked_login($linkedlogins[0]->get('id'));

        $match = \auth_oauth2\api::match_username_to_user('banana', $issuer);
        $this->assertFalse($match);

        $info = [];
        $info['username'] = 'apple';
        $info['email'] = 'apple@example.com';
        $info['firstname'] = 'Apple';
        $info['lastname'] = 'Fruit';
        $info['url'] = 'http://apple.com/';
        $info['alternamename'] = 'Beatles';

        $newuser = \auth_oauth2\api::create_new_confirmed_account($info, $issuer);

        $match = \auth_oauth2\api::match_username_to_user('apple', $issuer);

        $this->assertEquals($newuser->id, $match->get('userid'));
    }

    /**
     * Test that is_enabled correctly identifies when the plugin is enabled.
     */
    public function test_is_enabled() {
        $this->resetAfterTest();

        set_config('auth', 'manual,oauth2');
        $this->assertTrue(\auth_oauth2\api::is_enabled());
    }

    /**
     * Test that is_enabled correctly identifies when the plugin is disabled.
     */
    public function test_is_enabled_disabled() {
        $this->resetAfterTest();

        set_config('auth', 'manual');
        $this->assertFalse(\auth_oauth2\api::is_enabled());
    }
}
