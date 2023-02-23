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
 * Tests for the plugin privacy provider
 *
 * @package    tool_dataprivacy
 * @copyright  2020 Paul Holden <paulh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_dataprivacy\privacy;

defined('MOODLE_INTERNAL') || die();

use core_privacy\local\request\userlist;
use core_privacy\local\request\writer;
use core_privacy\tests\provider_testcase;
use tool_dataprivacy\api;
use tool_dataprivacy\local\helper;
use tool_dataprivacy\privacy\provider;

/**
 * Privacy provider tests
 *
 * @package    tool_dataprivacy
 * @copyright  2020 Paul Holden <paulh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider_test extends provider_testcase {

    /**
     * Test provider get_contexts_for_userid method
     *
     * @return void
     */
    public function test_get_contexts_for_userid() {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        $context = \context_user::instance($user->id);

        // Returned context list should contain a single item.
        $contextlist = $this->get_contexts_for_userid($user->id, 'tool_dataprivacy');
        $this->assertCount(1, $contextlist);

        // We should have the user context of our test user.
        $this->assertSame($context, $contextlist->current());
    }

    /**
     * Test provider get_users_in_context method
     *
     * @return void
     */
    public function test_get_users_in_context() {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        $context = \context_user::instance($user->id);

        $userlist = new userlist($context, 'tool_dataprivacy');
        provider::get_users_in_context($userlist);

        $this->assertEquals([$user->id], $userlist->get_userids());
    }

    /**
     * Test provider get_users_in_context method for a non-user context
     *
     * @return void
     */
    public function test_get_users_in_context_non_user_context() {
        $context = \context_system::instance();

        $userlist = new userlist($context, 'tool_dataprivacy');
        provider::get_users_in_context($userlist);

        $this->assertEmpty($userlist);
    }

    /**
     * Test provider export_user_data method
     *
     * @return void
     */
    public function test_export_user_data() {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        $context = \context_user::instance($user->id);

        $this->setUser($user);

        // Create an export request, approve it.
        $requestexport = api::create_data_request($user->id, api::DATAREQUEST_TYPE_EXPORT,
            'Please export my stuff');
        api::update_request_status($requestexport->get('id'), api::DATAREQUEST_STATUS_APPROVED);

        // Create a deletion request, reject it.
        $requestdelete = api::create_data_request($user->id, api::DATAREQUEST_TYPE_DELETE);
        api::update_request_status($requestdelete->get('id'), api::DATAREQUEST_STATUS_REJECTED, 0, 'Nope');

        $this->export_context_data_for_user($user->id, $context, 'tool_dataprivacy');

        /** @var \core_privacy\tests\request\content_writer $writer */
        $writer = writer::with_context($context);
        $this->assertTrue($writer->has_any_data());

        /** @var stdClass[] $data */
        $data = (array) $writer->get_data([
            get_string('privacyandpolicies', 'admin'),
            get_string('datarequests', 'tool_dataprivacy'),
        ]);

        $this->assertCount(2, $data);

        $strs = get_strings(['requesttypeexportshort', 'requesttypedeleteshort',
            'statusapproved', 'statusrejected', 'creationmanual'], 'tool_dataprivacy');

        // First item is the approved export request.
        $this->assertEquals($strs->requesttypeexportshort, $data[0]->type);
        $this->assertEquals($strs->statusapproved, $data[0]->status);
        $this->assertEquals($strs->creationmanual, $data[0]->creationmethod);
        $this->assertEquals($requestexport->get('comments'), $data[0]->comments);
        $this->assertEmpty($data[0]->dpocomment);
        $this->assertNotEmpty($data[0]->timecreated);

        // Next is the rejected deletion request.
        $this->assertEquals($strs->requesttypedeleteshort, $data[1]->type);
        $this->assertEquals($strs->statusrejected, $data[1]->status);
        $this->assertEquals($strs->creationmanual, $data[1]->creationmethod);
        $this->assertEmpty($data[1]->comments);
        $this->assertStringContainsString('Nope', $data[1]->dpocomment);
        $this->assertNotEmpty($data[1]->timecreated);
    }

    /**
     * Test class export_user_preferences method
     *
     * @return void
     */
    public function test_export_user_preferences() {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        // Set filters preference.
        $filters = [
            helper::FILTER_TYPE . ':' . api::DATAREQUEST_TYPE_EXPORT,
            helper::FILTER_STATUS . ':' . api::DATAREQUEST_STATUS_PENDING,
        ];
        set_user_preference(helper::PREF_REQUEST_FILTERS, json_encode($filters), $user);

        // Set paging preference.
        set_user_preference(helper::PREF_REQUEST_PERPAGE, 6, $user);

        provider::export_user_preferences($user->id);

        /** @var \core_privacy\tests\request\content_writer $writer */
        $writer = writer::with_context(\context_system::instance());
        $this->assertTrue($writer->has_any_data());

        /** @var stdClass[] $preferences */
        $preferences = (array) $writer->get_user_preferences('tool_dataprivacy');
        $this->assertCount(2, $preferences);

        $this->assertEquals((object) [
            'value' => '1:1, 2:0',
            'description' => 'Type: Export, Status: Pending',
        ], $preferences[helper::PREF_REQUEST_FILTERS]);

        $this->assertEquals(6, $preferences[helper::PREF_REQUEST_PERPAGE]->value);
    }
}
