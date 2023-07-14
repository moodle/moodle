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

use core\http_client;
use stored_file;
use GuzzleHttp\Psr7\Request;

/**
 * Class matrix_endpoint_manager to manage the api endpoints of matrix provider.
 *
 * @package    communication_matrix
 * @copyright  2023 Safat Shahin <safat.shahin@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class matrix_events_manager {

    /**
     * @var string|false|mixed|object $matrixhomeserverurl The URL of the home server
     */
    public string $matrixhomeserverurl;

    /**
     * @var string $matrixwebclienturl The URL of the web client
     */
    public string $matrixwebclienturl;

    /**
     * @var string|false|mixed|object $matrixaccesstoken The access token of the matrix server
     */
    private string $matrixaccesstoken;

    /**
     * @var string $roomid The id of the room from matrix server
     */
    public string $roomid;

    /**
     * Matrix events constructor to get the room id and refresh token usage if required.
     *
     * @param string|null $roomid The id of the room from matrix server
     */
    public function __construct(?string $roomid = null) {
        if (!empty($roomid)) {
            $this->roomid = $roomid;
        }

        $this->matrixhomeserverurl = get_config('communication_matrix', 'matrixhomeserverurl');
        $this->matrixaccesstoken = get_config('communication_matrix', 'matrixaccesstoken');
        $this->matrixwebclienturl = get_config('communication_matrix', 'matrixelementurl');
    }

    /**
     * Get the current token in use.
     *
     * @return string
     */
    public function get_token(): string {
        return $this->matrixaccesstoken;
    }

    /**
     * Get the matrix api endpoint for creating a new room.
     *
     * @return string
     */
    public function get_create_room_endpoint(): string {
        return $this->matrixhomeserverurl . '/' . '_matrix/client/r0/createRoom';
    }

    /**
     * Get the matrix api endpoint for updating the room topic.
     *
     * @return string
     */
    public function get_update_room_topic_endpoint(): string {
        if (!empty($this->roomid)) {
            return $this->matrixhomeserverurl . '/' . '_matrix/client/r0/rooms' .
                '/' . urlencode($this->roomid) . '/' . 'state/m.room.topic/';
        }
    }

    /**
     * Get the matrix api endpoint for updating room name.
     *
     * @return string
     */
    public function get_update_room_name_endpoint(): string {
        if (!empty($this->roomid)) {
            return $this->matrixhomeserverurl . '/' . '_matrix/client/r0/rooms' .
                '/' . urlencode($this->roomid) . '/' . 'state/m.room.name/';
        }
    }

    /**
     * Get matrix api endpoint for getting room information.
     *
     * @return string
     */
    public function get_room_info_endpoint(): string {
        if (!empty($this->roomid)) {
            return $this->matrixhomeserverurl . '/_synapse/admin/v1/rooms/' . urlencode($this->roomid);
        }
    }

    /**
     * Get delete room endpoint.
     *
     * @return string
     */
    public function get_delete_room_endpoint(): string {
        if (!empty($this->roomid)) {
            return $this->matrixhomeserverurl . '/' . '_synapse/admin/v1/rooms/' . urlencode($this->roomid);
        }
    }

    /**
     * Get the matrix api endpoint for uploading a content to synapse server.
     */
    public function get_upload_content_endpoint(): string {
        return $this->matrixhomeserverurl . '/' . '_matrix/media/r0/upload/';
    }

    /**
     * Get the matrix api endpoint for updating room avatar.
     *
     * @return string
     */
    public function get_update_avatar_endpoint(): string {
        if (!empty($this->roomid)) {
            return $this->matrixhomeserverurl . '/' . '_matrix/client/r0/rooms' .
                '/' . urlencode($this->roomid) . '/' . 'state/m.room.avatar/';
        }
    }

    /**
     * Get the members of a room. Useful when performing actions where member needs to exist first.
     *
     * @return string
     */
    public function get_room_membership_joined_endpoint(): string {
        if (!empty($this->roomid)) {
            return $this->matrixhomeserverurl . '/' . '_matrix/client/r0/rooms' .
                '/' . urlencode($this->roomid) . '/' . 'joined_members';
        }
    }

    /**
     * Get the 'join' room membership endpoint. This adds users to a room.
     *
     * @return string
     */
    public function get_room_membership_join_endpoint(): string {
        if (!empty($this->roomid)) {
            return $this->matrixhomeserverurl . '/' . '_synapse/admin/v1/join' . '/' . urlencode($this->roomid);
        }
    }

    /**
     * Get the 'kick' room membership endpoint. This removes users from a room.
     *
     * @return string
     */
    public function get_room_membership_kick_endpoint(): string {
        if (!empty($this->roomid)) {
            return $this->matrixhomeserverurl . '/' . '_matrix/client/r0/rooms' .
                '/' . urlencode($this->roomid) . '/' . 'kick';
        }
    }

    /**
     * Get the matrix api endpoint for creating a new user.
     *
     * @param string $matrixuserid Matrix user id
     * @return string
     */
    public function get_create_user_endpoint(string $matrixuserid): string {
        return $this->matrixhomeserverurl . '/' . '_synapse/admin/v2/users/' . urlencode($matrixuserid);
    }

    /**
     * Get the matrix api endpoint for creating a new user.
     *
     * @param string $matrixuserid Matrix user id
     * @return string
     */
    public function get_user_info_endpoint(string $matrixuserid): string {
        return $this->matrixhomeserverurl . '/' . '_synapse/admin/v2/users/' . urlencode($matrixuserid);
    }

    /**
     * The http request for the api call.
     *
     * @param array $jsonarray The array of json
     * @param array $headers The array of headers
     * @param bool $httperror Enable or disable http error from response
     * @return \core\http_client
     */
    public function request(array $jsonarray = [], array $headers = [], bool $httperror = true): \core\http_client {
        $header = ['Authorization' => 'Bearer ' . $this->get_token()];
        $headers = array_merge($header, $headers);
        $response = new  \core\http_client([
            'http_errors' => $httperror,
            'headers' => $headers,
            'json' => $jsonarray,
        ]);
        return $response;
    }

    /**
     * Upload the content in the matrix/synapse server.
     *
     * @param null|stored_file $file The content to be uploaded
     * @return string|false
     */
    public function upload_matrix_content(?stored_file $file): bool|string {
        $headers = [
            'Authorization' => 'Bearer ' . $this->get_token(),
        ];
        $filecontent = null;
        $query = [];

        if ($file) {
            $filecontent = $file->get_content();
            $headers['Content-Type'] = $file->get_mimetype();
            $query['filename'] = $file->get_filename();
        }

        $client = new http_client();
        $request = new Request(
            'POST',
            $this->get_upload_content_endpoint(),
            $headers,
            $filecontent,
        );

        $response = $client->send($request, [
            'query' => $query,
        ]);
        $response = json_decode($response->getBody());
        if ($response) {
            return $response->content_uri;
        }
        return false;
    }

}
