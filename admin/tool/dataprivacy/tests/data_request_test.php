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
 * Tests for the data_request persistent.
 *
 * @package    tool_dataprivacy
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once('data_privacy_testcase.php');

use tool_dataprivacy\api;

/**
 * Tests for the data_request persistent.
 *
 * @package    tool_dataprivacy
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_dataprivacy_data_request_testcase extends data_privacy_testcase {

    /**
     * Data provider for testing is_resettable, and is_active.
     *
     * @return  array
     */
    public function status_state_provider() : array {
        return [
            [
                'state' => api::DATAREQUEST_STATUS_PENDING,
                'resettable' => false,
                'active' => false,
            ],
            [
                'state' => api::DATAREQUEST_STATUS_PREPROCESSING,
                'resettable' => false,
                'active' => false,
            ],
            [
                'state' => api::DATAREQUEST_STATUS_AWAITING_APPROVAL,
                'resettable' => true,
                'active' => true,
            ],
            [
                'state' => api::DATAREQUEST_STATUS_APPROVED,
                'resettable' => true,
                'active' => true,
            ],
            [
                'state' => api::DATAREQUEST_STATUS_PROCESSING,
                'resettable' => false,
                'active' => false,
            ],
            [
                'state' => api::DATAREQUEST_STATUS_COMPLETE,
                'resettable' => false,
                'active' => false,
            ],
            [
                'state' => api::DATAREQUEST_STATUS_CANCELLED,
                'resettable' => false,
                'active' => false,
            ],
            [
                'state' => api::DATAREQUEST_STATUS_REJECTED,
                'resettable' => true,
                'active' => false,
            ],
            [
                'state' => api::DATAREQUEST_STATUS_DOWNLOAD_READY,
                'resettable' => false,
                'active' => false,
            ],
            [
                'state' => api::DATAREQUEST_STATUS_EXPIRED,
                'resettable' => false,
                'active' => false,
            ],
        ];
    }

    /**
     * Test the pseudo states of a data request with an export request.
     *
     * @dataProvider        status_state_provider
     * @param       int     $status
     * @param       bool    $resettable
     * @param       bool    $active
     */
    public function test_pseudo_states_export(int $status, bool $resettable, bool $active) {
        $uut = new \tool_dataprivacy\data_request();
        $uut->set('status', $status);
        $uut->set('type', api::DATAREQUEST_TYPE_EXPORT);

        $this->assertEquals($resettable, $uut->is_resettable());
        $this->assertEquals($active, $uut->is_active());
    }

    /**
     * Test the pseudo states of a data request with a delete request.
     *
     * @dataProvider        status_state_provider
     * @param       int     $status
     * @param       bool    $resettable
     * @param       bool    $active
     */
    public function test_pseudo_states_delete(int $status, bool $resettable, bool $active) {
        $uut = new \tool_dataprivacy\data_request();
        $uut->set('status', $status);
        $uut->set('type', api::DATAREQUEST_TYPE_DELETE);

        $this->assertEquals($resettable, $uut->is_resettable());
        $this->assertEquals($active, $uut->is_active());
    }

    /**
     * Test the pseudo states of a data request.
     *
     * @dataProvider        status_state_provider
     * @param       int     $status
     */
    public function test_can_reset_others($status) {
        $uut = new \tool_dataprivacy\data_request();
        $uut->set('status', $status);
        $uut->set('type', api::DATAREQUEST_TYPE_OTHERS);

        $this->assertFalse($uut->is_resettable());
    }

    /**
     * Data provider for states which are not resettable.
     *
     * @return      array
     */
    public function non_resettable_provider() : array {
        $states = [];
        foreach ($this->status_state_provider() as $thisstatus) {
            if (!$thisstatus['resettable']) {
                $states[] = $thisstatus;
            }
        }

        return $states;
    }

    /**
     * Ensure that requests which are not resettable cause an exception to be thrown.
     *
     * @dataProvider        non_resettable_provider
     * @param       int     $status
     */
    public function test_non_resubmit_request($status) {
        $uut = new \tool_dataprivacy\data_request();
        $uut->set('status', $status);

        $this->expectException(\moodle_exception::class);
        $this->expectExceptionMessage(get_string('cannotreset', 'tool_dataprivacy'));

        $uut->resubmit_request();
    }

    /**
     * Ensure that a rejected request can be reset.
     */
    public function test_resubmit_request() {
        $this->resetAfterTest();

        $uut = new \tool_dataprivacy\data_request();
        $uut->set('status', api::DATAREQUEST_STATUS_REJECTED);
        $uut->set('type', api::DATAREQUEST_TYPE_DELETE);
        $uut->set('comments', 'Foo');
        $uut->set('requestedby', 42);
        $uut->set('dpo', 98);

        $newrequest = $uut->resubmit_request();

        $this->assertEquals('Foo', $newrequest->get('comments'));
        $this->assertEquals(42, $newrequest->get('requestedby'));
        $this->assertEquals(98, $newrequest->get('dpo'));
        $this->assertEquals(api::DATAREQUEST_STATUS_PENDING, $newrequest->get('status'));
        $this->assertEquals(api::DATAREQUEST_TYPE_DELETE, $newrequest->get('type'));

        $this->assertEquals(api::DATAREQUEST_STATUS_REJECTED, $uut->get('status'));
    }

    /**
     * Ensure that an active request can be reset.
     */
    public function test_resubmit_active_request() {
        $this->resetAfterTest();

        $uut = new \tool_dataprivacy\data_request();
        $uut->set('status', api::DATAREQUEST_STATUS_APPROVED);
        $uut->set('type', api::DATAREQUEST_TYPE_DELETE);
        $uut->set('comments', 'Foo');
        $uut->set('requestedby', 42);
        $uut->set('dpo', 98);

        $newrequest = $uut->resubmit_request();

        $this->assertEquals('Foo', $newrequest->get('comments'));
        $this->assertEquals(42, $newrequest->get('requestedby'));
        $this->assertEquals(98, $newrequest->get('dpo'));
        $this->assertEquals(api::DATAREQUEST_STATUS_PENDING, $newrequest->get('status'));
        $this->assertEquals(api::DATAREQUEST_TYPE_DELETE, $newrequest->get('type'));

        $this->assertEquals(api::DATAREQUEST_STATUS_REJECTED, $uut->get('status'));
    }

    /**
     * Create a data request for the user.
     *
     * @param   int     $userid
     * @param   int     $type
     * @param   int     $status
     * @return  data_request
     */
    public function create_request_for_user_with_status(int $userid, int $type, int $status) : data_request {
        $request = new data_request(0, (object) [
                'userid' => $userid,
                'type' => $type,
                'status' => $status,
            ]);

        $request->save();

        return $request;
    }
}
