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

namespace communication_matrix;

use core\context;
use GuzzleHttp\Psr7\Response;

/**
 * Trait matrix_helper_trait to generate initial setup for matrix mock and associated helpers.
 *
 * @package    communication_matrix
 * @copyright  2023 Safat Shahin <safat.shahin@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
trait matrix_test_helper_trait {
    /**
     * @var string $accesstoken The token for matrix connection
     */
    protected string $accesstoken;

    /**
     * @var string $matrixhomeserverurl The server url of matrix synapse server
     */
    protected string $matrixhomeserverurl;

    /**
     * Initialize the mock configs in settings.
     *
     * @return void
     */
    protected function initialise_mock_configs(): void {
        $this->matrixhomeserverurl = TEST_COMMUNICATION_MATRIX_MOCK_SERVER;
        set_config('matrixhomeserverurl', $this->matrixhomeserverurl, 'communication_matrix');
        $request = $this->request();
        $response = $request->post($this->matrixhomeserverurl . '/backoffice/create-admin');
        $admindata = json_decode($response->getBody());
        $json = [
            'identifier' => [
                'type' => 'm.id.user',
                'user' => $admindata->user_id,
            ],
            'type' => 'm.login.password',
            'password' => $admindata->password,
        ];
        $request = $this->request($json);
        $response = $request->post($this->matrixhomeserverurl . '/_matrix/client/r0/login');
        $response = json_decode($response->getBody());
        if (empty($response->access_token)) {
            $this->markTestSkipped(
                'The matrix mock server is not responsive, can not continue the tests'
            );
        }
        $this->accesstoken = $response->access_token;
        set_config('matrixaccesstoken', $this->accesstoken, 'communication_matrix');
    }

    /**
     * Get the mock server url.
     *
     * @return string
     */
    public function get_matrix_server_url(): string {
        if (empty($this->matrixhomeserverurl)) {
            throw new \coding_exception('Can not get this information without initializing the mock server.');
        }
        return $this->matrixhomeserverurl;
    }

    /**
     * Get the matrix access token.
     *
     * @return string
     */
    public function get_matrix_access_token(): string {
        if (empty($this->accesstoken)) {
            throw new \coding_exception('Can not get this information without initializing the mock server.');
        }
        return $this->accesstoken;
    }

    /**
     * This test requires mock server to be present.
     *
     * @return void
     */
    protected function initialise_mock_server(): void {
        if (!defined('TEST_COMMUNICATION_MATRIX_MOCK_SERVER')) {
            $this->markTestSkipped(
                'The TEST_COMMUNICATION_MATRIX_MOCK_SERVER constant must be defined to run communication_matrix tests'
            );
        }
        $this->reset_mock();
        $this->initialise_mock_configs();
    }

    /**
     * Get matrix room data from matrix server.
     *
     * @param string $roomid The id of the room
     * @return \stdClass
     */
    public function get_matrix_room_data(string $roomid): \stdClass {
        $rooms = $this->backoffice_get_all_rooms();
        foreach ($rooms as $room) {
            if ($room->room_id === $roomid) {
                return $room;
            }
        }
    }

    /**
     * Get matrix user data from matrix server.
     *
     * @param string $roomid The id of the room
     * @param string $matrixuserid The id of the user
     * @return \stdClass
     */
    public function get_matrix_user_data(string $roomid, string $matrixuserid): \stdClass {
        $users = $this->backoffice_get_all_users();

        foreach ($users as $user) {
            if ($user->userid === $matrixuserid) {
                return $user;
            }
        }
    }

    /**
     * A backoffice call to get all registered users from our mock server.
     *
     * @return array
     */
    public function backoffice_get_all_users(): array {
        $client = new \core\http_client();

        return json_decode($client->get($this->get_backoffice_uri('users'))->getBody())->users;
    }

    /**
     * A backoffice method to create users and rooms on our mock server.
     *
     * @param array $users
     * @param array $rooms
     */
    public function backoffice_create_users_and_rooms(
        array $users = [],
        array $rooms = [],
    ): Response {
        $client = new \core\http_client();
        return $client->put(
            $this->get_backoffice_uri('create'),
            [
                'json' => [
                    'users' => $users,
                    'rooms' => $rooms,
                ],
            ],
        );
    }

    /**
     * The http request for the api call.
     *
     * @param array $jsonarray The array of json
     * @param array $headers The array of headers
     * @return \core\http_client
     */
    public function request(array $jsonarray = [], array $headers = []): \core\http_client {
        $response = new \core\http_client([
            'headers' => $headers,
            'json' => $jsonarray,
        ]);
        return $response;
    }

    /**
     * Get the URI of a backoffice endpoint on the mock server.
     *
     * @param string $endpoint
     * @return string
     */
    protected function get_backoffice_uri(string $endpoint): string {
        return $this->get_matrix_server_url() . '/backoffice/' . $endpoint;
    }

    /**
     * Fetch all rooms from the back office.
     *
     * @return array
     */
    public function backoffice_get_all_rooms(): array {
        $client = new \core\http_client();

        return json_decode($client->get($this->get_backoffice_uri('rooms'))->getBody())->rooms;
    }

    /**
     * Return the first room from the server.
     *
     * In most cases there is only one room.
     * @return \stdClass
     */
    public function backoffice_get_room(): \stdClass {
        // Fetch the room information from the server.
        $rooms = $this->backoffice_get_all_rooms();
        $this->assertCount(1, $rooms);
        $room = reset($rooms);
        return $room;
    }

    /**
     * Reset the mock server
     *
     * @return void
     */
    public function reset_mock(): void {
        if (defined('TEST_COMMUNICATION_MATRIX_MOCK_SERVER')) {
            $request = $this->request();
            $response = $request->post(TEST_COMMUNICATION_MATRIX_MOCK_SERVER . '/backoffice/reset');
            $response = json_decode($response->getBody());
            if (empty($response->reset)) {
                $this->markTestSkipped(
                    'The matrix mock server is not responsive, can not continue the tests'
                );
            }
        }
    }

    /**
     * Helper to create a room.
     *
     * @param null|string $component
     * @param null|string $itemtype
     * @param null|int $itemid
     * @param null|string $roomname
     * @param null|string $roomtopic
     * @param null|stored_file $roomavatar
     * @param array $members
     * @return api
     */
    protected function create_matrix_room(
        ?string $component = 'core_course',
        ?string $itemtype = 'example',
        ?int $itemid = 1,
        ?string $roomname = null,
        ?string $roomtopic = null,
        ?\stored_file $roomavatar = null,
        array $members = [],
        ?context $context = null,
    ): \core_communication\api {
        $context = $context ?? \core\context\system::instance();
        // Create a new room.
        $communication = \core_communication\api::load_by_instance(
            context: $context,
            component: $component,
            instancetype: $itemtype,
            instanceid: $itemid,
            provider: 'communication_matrix',
        );

        $communication->create_and_configure_room(
            communicationroomname: $roomname ?? 'Room name',
            avatar: $roomavatar,
            instance: (object) [
                'matrixroomtopic' => $roomtopic ?? 'A fun topic',
            ],
        );

        $communication->add_members_to_room($members);

        // Run the adhoc task.
        $this->run_all_adhoc_tasks();

        return \core_communication\api::load_by_instance(
            context: $context,
            component: $component,
            instancetype: $itemtype,
            instanceid: $itemid,
        );
    }
}
