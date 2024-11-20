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

namespace core\moodlenet;

use core\http_client;
use core\oauth2\client;
use stored_file;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

/**
 * MoodleNet client which handles direct outbound communication with MoodleNet instances.
 *
 * @package   core
 * @copyright 2023 Michael Hawkins <michaelh@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class moodlenet_client {
    /**
     * @var string MoodleNet resource creation endpoint URI.
     */
    protected const API_CREATE_RESOURCE_URI = '/.pkg/@moodlenet/ed-resource/basic/v1/create';

    /**
     * @var string MoodleNet scope for creating resources.
     */
    public const API_SCOPE_CREATE_RESOURCE = '@moodlenet/ed-resource:write.own';


    /**
     * Constructor.
     *
     * @param http_client $httpclient The httpclient object being used to perform the share.
     * @param client $oauthclient The OAuth 2 client for the MoodleNet site being shared to.
     */
    public function __construct(
        protected http_client $httpclient,
        protected client $oauthclient,
    ) {
        // All properties promoted, nothing further required.
    }

    /**
     * Create a resource on MoodleNet which includes a file.
     *
     * @param stored_file $file The file data to send to MoodleNet.
     * @param string $resourcename The name of the resource being shared.
     * @param string $resourcedescription A description of the resource being shared.
     * @return \Psr\Http\Message\ResponseInterface The HTTP client response from MoodleNet.
     */
    public function create_resource_from_stored_file(
        stored_file $file,
        string $resourcename,
        string $resourcedescription,
    ): ResponseInterface {
        // This may take a long time if a lot of data is being shared.
        \core_php_time_limit::raise();

        $moodleneturl = $this->oauthclient->get_issuer()->get('baseurl');
        $apiurl = rtrim($moodleneturl, '/') . self::API_CREATE_RESOURCE_URI;
        $stream = $file->get_psr_stream();

        $requestdata = $this->prepare_file_share_request_data(
            $file->get_filename(),
            $file->get_mimetype(),
            $stream,
            $resourcename,
            $resourcedescription,
        );

        try {
            $response = $this->httpclient->request('POST', $apiurl, $requestdata);
        } finally {
            $stream->close(); // Always close the request stream ASAP. Or it will remain open till shutdown/destruct.
        }

        return $response;
    }

    /**
     * Prepare the request data required for sharing a file to MoodleNet.
     * This creates an array in the format used by \core\httpclient options to send a multipart request.
     *
     * @param string $filename Name of the file being shared.
     * @param string $mimetype Mime type of the file being shared.
     * @param StreamInterface $stream Stream of the file being shared.
     * @param string $resourcename The name of the resource being shared.
     * @param string $resourcedescription A description of the resource being shared.
     * @return array Data in the format required to send a file to MoodleNet using \core\httpclient.
     */
    protected function prepare_file_share_request_data(
        string $filename,
        string $mimetype,
        StreamInterface $stream,
        string $resourcename,
        string $resourcedescription,
    ): array {
        return [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->oauthclient->get_accesstoken()->token,
            ],
            'multipart' => [
                [
                    'name' => 'metadata',
                    'contents' => json_encode([
                        'name' => $resourcename,
                        'description' => $resourcedescription,
                    ]),
                    'headers' => [
                        'Content-Disposition' => 'form-data; name="."',
                    ],
                ],
                [
                    'name' => 'filecontents',
                    'contents' => $stream,
                    'headers' => [
                        'Content-Disposition' => 'form-data; name=".resource"; filename="' . $filename . '"',
                        'Content-Type' => $mimetype,
                        'Content-Transfer-Encoding' => 'binary',
                    ],
                ],
            ],
        ];
    }
}
