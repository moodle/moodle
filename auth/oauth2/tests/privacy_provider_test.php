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
 * Privacy test for the authentication oauth2
 *
 * @package    auth_oauth2
 * @category   test
 * @copyright  2018 Carlos Escobedo <carlos@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use \auth_oauth2\privacy\provider;
use \core_privacy\local\request\approved_contextlist;
use \core_privacy\local\request\writer;
use \core_privacy\tests\provider_testcase;

/**
 * Privacy test for the authentication oauth2
 *
 * @package    auth_oauth2
 * @category   test
 * @copyright  2018 Carlos Escobedo <carlos@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class auth_oauth2_privacy_testcase extends provider_testcase {
    /**
     * Set up method.
     */
    public function setUp() {
        $this->resetAfterTest();
        $this->setAdminUser();
    }

    /**
     * Check that a user context is returned if there is any user data for this user.
     */
    public function test_get_contexts_for_userid() {
        $user = $this->getDataGenerator()->create_user();
        $this->assertEmpty(provider::get_contexts_for_userid($user->id));

        $issuer = \core\oauth2\api::create_standard_issuer('google');
        $info = [];
        $info['username'] = 'gina';
        $info['email'] = 'gina@example.com';
        \auth_oauth2\api::link_login($info, $issuer, $user->id, false);

        $contextlist = provider::get_contexts_for_userid($user->id);
        // Check that we only get back one context.
        $this->assertCount(1, $contextlist);

        // Check that a context is returned is the expected.
        $usercontext = \context_user::instance($user->id);
        $this->assertEquals($usercontext->id, $contextlist->get_contextids()[0]);
    }

    /**
     * Test that user data is exported correctly.
     */
    public function test_export_user_data() {
        $user = $this->getDataGenerator()->create_user();
        $issuer = \core\oauth2\api::create_standard_issuer('google');
        $info = [];
        $info['username'] = 'gina';
        $info['email'] = 'gina@example.com';
        \auth_oauth2\api::link_login($info, $issuer, $user->id, false);
        $usercontext = \context_user::instance($user->id);

        $writer = writer::with_context($usercontext);
        $this->assertFalse($writer->has_any_data());
        $approvedlist = new approved_contextlist($user, 'auth_oauth2', [$usercontext->id]);
        provider::export_user_data($approvedlist);
        $data = $writer->get_data([get_string('privacy:metadata:auth_oauth2', 'auth_oauth2'), $issuer->get('name')]);
        $this->assertEquals($info['username'], $data->username);
        $this->assertEquals($info['email'], $data->email);
    }

    /**
     * Test deleting all user data for a specific context.
     */
    public function test_delete_data_for_all_users_in_context() {
        global $DB;

        $user1 = $this->getDataGenerator()->create_user();
        $issuer1 = \core\oauth2\api::create_standard_issuer('google');
        $info = [];
        $info['username'] = 'gina';
        $info['email'] = 'gina@example.com';
        \auth_oauth2\api::link_login($info, $issuer1, $user1->id, false);
        $user1context = \context_user::instance($user1->id);

        $user2 = $this->getDataGenerator()->create_user();
        $issuer2 = \core\oauth2\api::create_standard_issuer('microsoft');
        $info = [];
        $info['username'] = 'jerry';
        $info['email'] = 'jerry@example.com';
        \auth_oauth2\api::link_login($info, $issuer2, $user2->id, false);
        $user2context = \context_user::instance($user2->id);

        // Get all oauth2 accounts.
        $oauth2accounts = $DB->get_records('auth_oauth2_linked_login', array());
        // There should be two.
        $this->assertCount(2, $oauth2accounts);

        // Delete everything for the first user context.
        provider::delete_data_for_all_users_in_context($user1context);

        // Get all oauth2 accounts match with user1.
        $oauth2accounts = $DB->get_records('auth_oauth2_linked_login', ['userid' => $user1->id]);
        $this->assertCount(0, $oauth2accounts);

        // Get all oauth2 accounts.
        $oauth2accounts = $DB->get_records('auth_oauth2_linked_login', array());
        // There should be one.
        $this->assertCount(1, $oauth2accounts);
    }

    /**
     * This should work identical to the above test.
     */
    public function test_delete_data_for_user() {
        global $DB;

        $user1 = $this->getDataGenerator()->create_user();
        $issuer1 = \core\oauth2\api::create_standard_issuer('google');
        $info = [];
        $info['username'] = 'gina';
        $info['email'] = 'gina@example.com';
        \auth_oauth2\api::link_login($info, $issuer1, $user1->id, false);
        $user1context = \context_user::instance($user1->id);

        $user2 = $this->getDataGenerator()->create_user();
        $issuer2 = \core\oauth2\api::create_standard_issuer('microsoft');
        $info = [];
        $info['username'] = 'jerry';
        $info['email'] = 'jerry@example.com';
        \auth_oauth2\api::link_login($info, $issuer2, $user2->id, false);
        $user2context = \context_user::instance($user2->id);

        // Get all oauth2 accounts.
        $oauth2accounts = $DB->get_records('auth_oauth2_linked_login', array());
        // There should be two.
        $this->assertCount(2, $oauth2accounts);

        // Delete everything for the first user.
        $approvedlist = new approved_contextlist($user1, 'auth_oauth2', [$user1context->id]);
        provider::delete_data_for_user($approvedlist);

        // Get all oauth2 accounts match with user1.
        $oauth2accounts = $DB->get_records('auth_oauth2_linked_login', ['userid' => $user1->id]);
        $this->assertCount(0, $oauth2accounts);

        // Get all oauth2 accounts.
        $oauth2accounts = $DB->get_records('auth_oauth2_linked_login', array());
        // There should be one user.
        $this->assertCount(1, $oauth2accounts);
    }
}
