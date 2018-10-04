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
 * Data provider tests.
 *
 * @package    core_webservice
 * @category   test
 * @copyright  2018 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
global $CFG;

use core_privacy\tests\provider_testcase;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\transform;
use core_privacy\local\request\writer;
use core_webservice\privacy\provider;
use core_privacy\local\request\approved_userlist;

require_once($CFG->dirroot . '/webservice/lib.php');

/**
 * Data provider testcase class.
 *
 * @package    core_webservice
 * @category   test
 * @copyright  2018 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_webservice_privacy_testcase extends provider_testcase {

    public function setUp() {
        $this->resetAfterTest();
    }

    public function test_get_contexts_for_userid() {
        $dg = $this->getDataGenerator();
        $u1 = $dg->create_user();
        $u2 = $dg->create_user();
        $u3 = $dg->create_user();
        $u4 = $dg->create_user();
        $u5 = $dg->create_user();
        $u1ctx = context_user::instance($u1->id);
        $u2ctx = context_user::instance($u2->id);
        $u3ctx = context_user::instance($u3->id);
        $u5ctx = context_user::instance($u5->id);

        $s = $this->create_service();
        $this->create_token(['userid' => $u1->id]);
        $this->create_token(['userid' => $u1->id]);
        $this->create_token(['userid' => $u2->id, 'creatorid' => $u3->id]);
        $this->create_service_user(['externalserviceid' => $s->id, 'userid' => $u5->id]);

        $contextids = provider::get_contexts_for_userid($u1->id)->get_contextids();
        $this->assertCount(1, $contextids);
        $this->assertTrue(in_array($u1ctx->id, $contextids));

        $contextids = provider::get_contexts_for_userid($u2->id)->get_contextids();
        $this->assertCount(1, $contextids);
        $this->assertTrue(in_array($u2ctx->id, $contextids));

        $contextids = provider::get_contexts_for_userid($u3->id)->get_contextids();
        $this->assertCount(1, $contextids);
        $this->assertTrue(in_array($u2ctx->id, $contextids));

        $contextids = provider::get_contexts_for_userid($u4->id)->get_contextids();
        $this->assertCount(0, $contextids);

        $contextids = provider::get_contexts_for_userid($u5->id)->get_contextids();
        $this->assertCount(1, $contextids);
        $this->assertTrue(in_array($u5ctx->id, $contextids));
    }

    public function test_delete_data_for_user() {
        global $DB;

        $dg = $this->getDataGenerator();
        $u1 = $dg->create_user();
        $u2 = $dg->create_user();
        $u1ctx = context_user::instance($u1->id);
        $u2ctx = context_user::instance($u2->id);

        $s = $this->create_service();
        $this->create_token(['userid' => $u1->id, 'creatorid' => $u2->id]);
        $this->create_token(['userid' => $u1->id]);
        $this->create_token(['userid' => $u2->id]);
        $this->create_service_user(['externalserviceid' => $s->id, 'userid' => $u1->id]);
        $this->create_service_user(['externalserviceid' => $s->id, 'userid' => $u2->id]);

        $this->assertEquals(2, $DB->count_records('external_tokens', ['userid' => $u1->id]));
        $this->assertEquals(1, $DB->count_records('external_tokens', ['userid' => $u2->id]));
        $this->assertTrue($DB->record_exists('external_services_users', ['userid' => $u1->id]));
        $this->assertTrue($DB->record_exists('external_services_users', ['userid' => $u2->id]));

        // Delete in another context, nothing happens.
        provider::delete_data_for_user(new approved_contextlist($u2, 'core_webservice', [$u1ctx->id]));
        $this->assertEquals(2, $DB->count_records('external_tokens', ['userid' => $u1->id]));
        $this->assertEquals(1, $DB->count_records('external_tokens', ['userid' => $u2->id]));
        $this->assertTrue($DB->record_exists('external_services_users', ['userid' => $u1->id]));
        $this->assertTrue($DB->record_exists('external_services_users', ['userid' => $u2->id]));

        // Delete in my context.
        provider::delete_data_for_user(new approved_contextlist($u2, 'core_webservice', [$u2ctx->id]));
        $this->assertEquals(2, $DB->count_records('external_tokens', ['userid' => $u1->id]));
        $this->assertEquals(0, $DB->count_records('external_tokens', ['userid' => $u2->id]));
        $this->assertTrue($DB->record_exists('external_services_users', ['userid' => $u1->id]));
        $this->assertFalse($DB->record_exists('external_services_users', ['userid' => $u2->id]));
    }

    public function test_delete_data_for_all_users_in_context() {
        global $DB;

        $dg = $this->getDataGenerator();
        $u1 = $dg->create_user();
        $u2 = $dg->create_user();
        $u1ctx = context_user::instance($u1->id);
        $u2ctx = context_user::instance($u2->id);

        $s = $this->create_service();
        $this->create_token(['userid' => $u1->id, 'creatorid' => $u2->id]);
        $this->create_token(['userid' => $u1->id]);
        $this->create_token(['userid' => $u2->id]);
        $this->create_service_user(['externalserviceid' => $s->id, 'userid' => $u1->id]);
        $this->create_service_user(['externalserviceid' => $s->id, 'userid' => $u2->id]);

        $this->assertEquals(2, $DB->count_records('external_tokens', ['userid' => $u1->id]));
        $this->assertEquals(1, $DB->count_records('external_tokens', ['userid' => $u2->id]));
        $this->assertTrue($DB->record_exists('external_services_users', ['userid' => $u1->id]));
        $this->assertTrue($DB->record_exists('external_services_users', ['userid' => $u2->id]));

        provider::delete_data_for_all_users_in_context($u2ctx);
        $this->assertEquals(2, $DB->count_records('external_tokens', ['userid' => $u1->id]));
        $this->assertEquals(0, $DB->count_records('external_tokens', ['userid' => $u2->id]));
        $this->assertTrue($DB->record_exists('external_services_users', ['userid' => $u1->id]));
        $this->assertFalse($DB->record_exists('external_services_users', ['userid' => $u2->id]));

        provider::delete_data_for_all_users_in_context($u1ctx);
        $this->assertEquals(0, $DB->count_records('external_tokens', ['userid' => $u1->id]));
        $this->assertEquals(0, $DB->count_records('external_tokens', ['userid' => $u2->id]));
        $this->assertFalse($DB->record_exists('external_services_users', ['userid' => $u1->id]));
        $this->assertFalse($DB->record_exists('external_services_users', ['userid' => $u2->id]));

    }

    public function test_export_data_for_user() {
        global $DB;

        $dg = $this->getDataGenerator();
        $u1 = $dg->create_user();
        $u2 = $dg->create_user();
        $u1ctx = context_user::instance($u1->id);
        $u2ctx = context_user::instance($u2->id);

        $path = [get_string('webservices', 'core_webservice')];
        $yearago = time() - YEARSECS;
        $hourago = time() - HOURSECS;

        $s = $this->create_service(['name' => 'Party time!']);
        $this->create_token(['userid' => $u1->id, 'timecreated' => $yearago]);
        $this->create_token(['userid' => $u1->id, 'creatorid' => $u2->id, 'iprestriction' => '127.0.0.1',
            'lastaccess' => $hourago]);
        $this->create_token(['userid' => $u2->id, 'iprestriction' => '192.168.1.0/24', 'lastaccess' => $yearago,
            'externalserviceid' => $s->id]);
        $this->create_service_user(['externalserviceid' => $s->id, 'userid' => $u2->id]);

        // User 1 exporting user 2 context does not give anything.
        writer::reset();
        provider::export_user_data(new approved_contextlist($u1, 'core_webservice', [$u2ctx->id]));
        $data = writer::with_context($u1ctx)->get_data($path);
        $this->assertEmpty($data);
        $data = writer::with_context($u1ctx)->get_related_data($path, 'created_by_you');
        $this->assertEmpty($data);
        $data = writer::with_context($u2ctx)->get_data($path);
        $this->assertEmpty($data);
        $data = writer::with_context($u2ctx)->get_related_data($path, 'created_by_you');
        $this->assertEmpty($data);

        // User 1 exporting their context.
        writer::reset();
        provider::export_user_data(new approved_contextlist($u1, 'core_webservice', [$u1ctx->id, $u2ctx->id]));
        $data = writer::with_context($u1ctx)->get_data($path);
        $this->assertFalse(isset($data->services_user));
        $this->assertCount(2, $data->tokens);
        $this->assertEquals(transform::datetime($yearago), $data->tokens[0]['created_on']);
        $this->assertEquals(null, $data->tokens[0]['ip_restriction']);
        $this->assertEquals(transform::datetime($hourago), $data->tokens[1]['last_access']);
        $this->assertEquals('127.0.0.1', $data->tokens[1]['ip_restriction']);
        $data = writer::with_context($u1ctx)->get_related_data($path, 'created_by_you');
        $this->assertEmpty($data);
        $data = writer::with_context($u2ctx)->get_data($path);
        $this->assertEmpty($data);
        $data = writer::with_context($u2ctx)->get_related_data($path, 'created_by_you');
        $this->assertEmpty($data);

        // User 2 exporting their context.
        writer::reset();
        provider::export_user_data(new approved_contextlist($u2, 'core_webservice', [$u1ctx->id, $u2ctx->id]));
        $data = writer::with_context($u2ctx)->get_data($path);
        $this->assertCount(1, $data->tokens);
        $this->assertEquals('Party time!', $data->tokens[0]['external_service']);
        $this->assertEquals(transform::datetime($yearago), $data->tokens[0]['last_access']);
        $this->assertEquals('192.168.1.0/24', $data->tokens[0]['ip_restriction']);
        $this->assertCount(1, $data->services_user);
        $this->assertEquals('Party time!', $data->services_user[0]['external_service']);
        $data = writer::with_context($u1ctx)->get_related_data($path, 'created_by_you');
        $this->assertCount(1, $data->tokens);
        $this->assertEquals(transform::datetime($hourago), $data->tokens[0]['last_access']);
        $this->assertEquals('127.0.0.1', $data->tokens[0]['ip_restriction']);
        $data = writer::with_context($u1ctx)->get_data($path);
        $this->assertEmpty($data);
        $data = writer::with_context($u2ctx)->get_related_data($path, 'created_by_you');
        $this->assertEmpty($data);
    }

    /**
     * Test that only users with a user context are fetched.
     */
    public function test_get_users_in_context() {

        $component = 'core_webservice';
        // Create user u1.
        $u1 = $this->getDataGenerator()->create_user();
        $u1ctx = context_user::instance($u1->id);
        // Create user u2.
        $u2 = $this->getDataGenerator()->create_user();
        $u2ctx = context_user::instance($u2->id);
        // Create user u3.
        $u3 = $this->getDataGenerator()->create_user();
        $u3ctx = context_user::instance($u3->id);
        // Create user u4.
        $u4 = $this->getDataGenerator()->create_user();
        $u4ctx = context_user::instance($u4->id);
        // Create user u5.
        $u5 = $this->getDataGenerator()->create_user();
        $u5ctx = context_user::instance($u5->id);

        // The lists of users for each user context ($u1ctx, $u2ctx, etc.) should be empty.
        // Related user data have not been created yet.
        $userlist1 = new \core_privacy\local\request\userlist($u1ctx, $component);
        provider::get_users_in_context($userlist1);
        $this->assertCount(0, $userlist1);
        $userlist2 = new \core_privacy\local\request\userlist($u2ctx, $component);
        provider::get_users_in_context($userlist2);
        $this->assertCount(0, $userlist2);
        $userlist3 = new \core_privacy\local\request\userlist($u3ctx, $component);
        provider::get_users_in_context($userlist3);
        $this->assertCount(0, $userlist3);
        $userlist4 = new \core_privacy\local\request\userlist($u4ctx, $component);
        provider::get_users_in_context($userlist4);
        $this->assertCount(0, $userlist4);
        $userlist5 = new \core_privacy\local\request\userlist($u5ctx, $component);
        provider::get_users_in_context($userlist5);
        $this->assertCount(0, $userlist5);

        // Create a webservice.
        $s = $this->create_service();
        // Create a ws token for u1.
        $this->create_token(['userid' => $u1->id]);
        // Create a ws token for u2, and u3 as the creator of the token.
        $this->create_token(['userid' => $u2->id, 'creatorid' => $u3->id]);
        // Create a service user (u4).
        $this->create_service_user(['externalserviceid' => $s->id, 'userid' => $u4->id]);

        // The list of users for userlist1 should return one user (u1).
        provider::get_users_in_context($userlist1);
        $this->assertCount(1, $userlist1);
        $expected = [$u1->id];
        $actual = $userlist1->get_userids();
        $this->assertEquals($expected, $actual);
        // The list of users for userlist2 should return one user (u2).
        provider::get_users_in_context($userlist2);
        $this->assertCount(1, $userlist2);
        $expected = [$u2->id];
        $actual = $userlist2->get_userids();
        $this->assertEquals($expected, $actual);
        // The list of users for userlist3 should return one user (u3).
        provider::get_users_in_context($userlist3);
        $this->assertCount(1, $userlist3);
        $expected = [$u3->id];
        $actual = $userlist3->get_userids();
        $this->assertEquals($expected, $actual);
        // The list of users for userlist4 should return one user (u4).
        provider::get_users_in_context($userlist4);
        $this->assertCount(1, $userlist4);
        $expected = [$u4->id];
        $actual = $userlist4->get_userids();
        $this->assertEquals($expected, $actual);
        // The list of users for userlist5 should not return any users.
        provider::get_users_in_context($userlist5);
        $this->assertCount(0, $userlist5);

        // The list of users should only return users in the user context.
        $systemcontext = context_system::instance();
        $userlist6 = new \core_privacy\local\request\userlist($systemcontext, $component);
        provider::get_users_in_context($userlist6);
        $this->assertCount(0, $userlist6);
    }

    /**
     * Test that data for users in approved userlist is deleted.
     */
    public function test_delete_data_for_users() {

        $component = 'core_webservice';
        // Create user u1.
        $u1 = $this->getDataGenerator()->create_user();
        $u1ctx = context_user::instance($u1->id);
        // Create user u2.
        $u2 = $this->getDataGenerator()->create_user();
        $u2ctx = context_user::instance($u2->id);
        // Create user u3.
        $u3 = $this->getDataGenerator()->create_user();
        $u3ctx = context_user::instance($u3->id);
        // Create user u4.
        $u4 = $this->getDataGenerator()->create_user();
        $u4ctx = context_user::instance($u4->id);
        // Create user u5.
        $u5 = $this->getDataGenerator()->create_user();
        $u5ctx = context_user::instance($u5->id);

        // Create a webservice.
        $s = $this->create_service();
        // Create a ws token for u1.
        $this->create_token(['userid' => $u1->id]);
        // Create a ws token for u2, and u3 as the creator of the token.
        $this->create_token(['userid' => $u2->id, 'creatorid' => $u3->id]);
        // Create a service user (u4).
        $this->create_service_user(['externalserviceid' => $s->id, 'userid' => $u4->id]);
        // Create a service user (u5).
        $this->create_service_user(['externalserviceid' => $s->id, 'userid' => $u5->id]);

        // The list of users for u1ctx should return one user (u1).
        $userlist1 = new \core_privacy\local\request\userlist($u1ctx, $component);
        provider::get_users_in_context($userlist1);
        $this->assertCount(1, $userlist1);
        // The list of users for u2ctx should return one user (u2).
        $userlist2 = new \core_privacy\local\request\userlist($u2ctx, $component);
        provider::get_users_in_context($userlist2);
        $this->assertCount(1, $userlist2);
        // The list of users for u3ctx should return one user (u3).
        $userlist3 = new \core_privacy\local\request\userlist($u3ctx, $component);
        provider::get_users_in_context($userlist3);
        $this->assertCount(1, $userlist3);
        // The list of users for u4ctx should return one user (u4).
        $userlist4 = new \core_privacy\local\request\userlist($u4ctx, $component);
        provider::get_users_in_context($userlist4);
        $this->assertCount(1, $userlist4);

        $approvedlist = new approved_userlist($u1ctx, $component, $userlist1->get_userids());
        // Delete using delete_data_for_user.
        provider::delete_data_for_users($approvedlist);
        // Re-fetch users in u1ctx - the user data should now be empty.
        $userlist1 = new \core_privacy\local\request\userlist($u1ctx, $component);
        provider::get_users_in_context($userlist1);
        $this->assertCount(0, $userlist1);

        $approvedlist = new approved_userlist($u2ctx, $component, $userlist2->get_userids());
        // Delete using delete_data_for_user.
        provider::delete_data_for_users($approvedlist);
        // Re-fetch users in u2ctx - the user data should now be empty.
        $userlist2 = new \core_privacy\local\request\userlist($u2ctx, $component);
        provider::get_users_in_context($userlist2);
        $this->assertCount(0, $userlist2);

        $approvedlist = new approved_userlist($u3ctx, $component, $userlist3->get_userids());
        // Delete using delete_data_for_user.
        provider::delete_data_for_users($approvedlist);
        // Re-fetch users in u3ctx - the user data should now be empty.
        $userlist3 = new \core_privacy\local\request\userlist($u3ctx, $component);
        provider::get_users_in_context($userlist3);
        $this->assertCount(0, $userlist3);

        $approvedlist = new approved_userlist($u4ctx, $component, $userlist3->get_userids());
        // Delete using delete_data_for_user.
        provider::delete_data_for_users($approvedlist);
        // Re-fetch users in u4ctx - the user data should now be empty.
        $userlist4 = new \core_privacy\local\request\userlist($u4ctx, $component);
        provider::get_users_in_context($userlist4);
        $this->assertCount(0, $userlist4);

        // The list of users for u5ctx should still return one user (u5).
        $userlist5 = new \core_privacy\local\request\userlist($u5ctx, $component);
        provider::get_users_in_context($userlist5);
        $this->assertCount(1, $userlist5);

        // User data should only be removed in the user context.
        $systemcontext = context_system::instance();
        $approvedlist = new approved_userlist($systemcontext, $component, $userlist5->get_userids());
        // Delete using delete_data_for_user.
        provider::delete_data_for_users($approvedlist);
        // Re-fetch users in u5ctx - the user data should still be present.
        $userlist5 = new \core_privacy\local\request\userlist($u5ctx, $component);
        provider::get_users_in_context($userlist5);
        $this->assertCount(1, $userlist5);
    }

    /**
     * Create a service.
     *
     * @param array $params The params.
     * @return stdClass
     */
    protected function create_service(array $params = []) {
        global $DB;
        static $i = 0;
        $record = (object) array_merge([
            'name' => 'Some service',
            'enabled' => '1',
            'requiredcapability' => '',
            'restrictedusers' => '0',
            'component' => 'core_webservice',
            'timecreated' => time(),
            'timemodified' => time(),
            'shortname' => 'service' . $i,
            'downloadfiles' => '1',
            'uploadfiles' => '1',
        ], $params);
        $record->id = $DB->insert_record('external_services', $record);
        return $record;
    }

    /**
     * Create a service user.
     *
     * @param array $params The params.
     * @return stdClass
     */
    protected function create_service_user(array $params) {
        global $DB, $USER;
        static $i = 0;
        $record = (object) array_merge([
            'externalserviceid' => null,
            'userid' => $USER->id,
            'validuntil' => time() + YEARSECS,
            'iprestriction' => '',
            'timecreated' => time(),
        ], $params);
        $record->id = $DB->insert_record('external_services_users', $record);
        return $record;
    }

    /**
     * Create a token.
     *
     * @param array $params The params.
     * @return stdClass
     */
    protected function create_token(array $params) {
        global $DB, $USER;
        $service = $DB->get_record('external_services', ['shortname' => MOODLE_OFFICIAL_MOBILE_SERVICE]);
        $record = (object) array_merge([
            'token' => random_string(64),
            'privatetoken' => random_string(64),
            'tokentype' => EXTERNAL_TOKEN_PERMANENT,
            'contextid' => SYSCONTEXTID,
            'externalserviceid' => $service->id,
            'userid' => $USER->id,
            'validuntil' => time() + YEARSECS,
            'iprestriction' => null,
            'sid' => null,
            'timecreated' => time(),
            'lastaccess' => time(),
            'creatorid' => $USER->id,
        ], $params);
        $record->id = $DB->insert_record('external_tokens', $record);
        return $record;
    }
}
