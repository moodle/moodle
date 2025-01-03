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

namespace local_ai_manager\local;

use core\http_client;
use Firebase\JWT\JWT;
use Psr\Http\Client\ClientExceptionInterface;

/**
 * Helper class for providing the necessary extension functions to implement the authentication with Google OAuth for an AI tool.
 *
 * @package    local_ai_manager
 * @copyright  2024 ISB Bayern
 * @author     Philipp Memmel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class aitool_option_vertexai_authhandler {

    /**
     * Constructor for the auth handler.
     */
    public function __construct(
        /** @var int The ID of the instance being used. Will be used as key for the cache handling. */
            private readonly int $instanceid,
            /** @var string The serviceaccountinfo stringified JSON */
            private readonly string $serviceaccountinfo
    ) {
    }

    /**
     * Retrieves a fresh access token from the Google oauth endpoint.
     *
     * @return array of the form ['access_token' => 'xxx', 'expires' => 1730805678] containing the access token and the time at
     *  which the token expires. If there has been an error, the array is of the form
     *  ['error' => 'more detailed info about the error']
     */
    public function retrieve_access_token(): array {
        $clock = \core\di::get(\core\clock::class);
        $serviceaccountinfo = json_decode($this->serviceaccountinfo);
        $kid = $serviceaccountinfo->private_key_id;
        $privatekey = $serviceaccountinfo->private_key;
        $clientemail = $serviceaccountinfo->client_email;
        $jwtpayload = [
                'iss' => $clientemail,
                'sub' => $clientemail,
                'scope' => 'https://www.googleapis.com/auth/cloud-platform',
                'aud' => 'https://oauth2.googleapis.com/token',
                'iat' => $clock->time(),
                'exp' => $clock->time() + HOURSECS,
        ];
        $jwt = JWT::encode($jwtpayload, $privatekey, 'RS256', null, ['kid' => $kid]);

        $client = new http_client([
                'timeout' => get_config('local_ai_manager', 'requesttimeout'),
        ]);
        $options['query'] = [
                'assertion' => $jwt,
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
        ];

        try {
            $response = $client->post('https://oauth2.googleapis.com/token', $options);
        } catch (ClientExceptionInterface $exception) {
            return ['error' => $exception->getMessage()];
        }
        if ($response->getStatusCode() === 200) {
            $content = $response->getBody()->getContents();
            if (empty($content)) {
                return ['error' => 'Empty response'];
            }
            $content = json_decode($content, true);
            if (empty($content['access_token'])) {
                return ['error' => 'Response does not contain "access_token" key'];
            }
            return [
                    'access_token' => $content['access_token'],
                // We set the expiry time of the access token and reduce it by 10 seconds to avoid some errors caused
                // by different clocks on different servers, latency etc.
                    'expires' => $clock->time() + intval($content['expires_in']) - 10,
            ];
        } else {
            return ['error' => 'Response status code is not OK 200, but ' . $response->getStatusCode() . ': ' .
                    $response->getBody()->getContents()];
        }
    }

    /**
     * Gets an access token for accessing Vertex AI endpoints.
     *
     * This will check if the cached access token still has not expired. If cache is empty or the token has expired
     * a new access token will be fetched by calling {@see self::retrieve_access_token} and the new token will be stored
     * in the cache.
     *
     * @return string the access token as string, empty if no
     * @throws \moodle_exception if there is an error retrieving the access token.
     */
    public function get_access_token(): string {
        $clock = \core\di::get(\core\clock::class);
        $authcache = \cache::make('local_ai_manager', 'googleauth');
        $cachedauthinfo = $authcache->get($this->instanceid);
        if (empty($cachedauthinfo) || json_decode($cachedauthinfo)->expires < $clock->time()) {
            $authinfo = $this->retrieve_access_token();
            if (!empty($authinfo['error'])) {
                throw new \moodle_exception('exception_retrievingaccesstoken', 'local_ai_manager', '', '', $authinfo['error']);
            }
            $cachedauthinfo = json_encode($authinfo);
            $authcache->set($this->instanceid, $cachedauthinfo);
            $accesstoken = $authinfo['access_token'];
        } else {
            $accesstoken = json_decode($cachedauthinfo, true)['access_token'];
        }
        return $accesstoken;
    }

    /**
     * Refreshes the current access token.
     *
     * Clears the existing access token and retrieves a new one by invoking {@see self::get_access_token}.
     *
     * @return string the newly fetched access token as a string
     */
    public function refresh_access_token(): string {
        $this->clear_access_token();
        return $this->get_access_token();
    }

    /**
     * Clears the access token from the cache for the current instance.
     */
    public function clear_access_token(): void {
        $authcache = \cache::make('local_ai_manager', 'googleauth');
        $authcache->delete($this->instanceid);
    }

    /**
     * Regenerate a token if necessary.
     *
     * To check if it's necessary you need to pass over a request_response object that contains an answer from the API that you
     * tried to access with an access token before. If the request response shows that the token was expired, it will be
     * regenerated. You then can get it by calling {@see self::get_access_token()}.
     *
     * @param request_response $requestresponse the request_response object of the request before
     */
    public function is_expired_accesstoken_reason_for_failing(request_response $requestresponse): bool {
        if ($requestresponse->get_code() !== 401) {
            return false;
        }
        // We need to reset the stream, so we can again read it.
        $requestresponse->get_response()->rewind();
        $content = json_decode($requestresponse->get_response()->getContents(), true);
        return !empty(array_filter($content['error']['details'], fn($details) => $details['reason'] === 'ACCESS_TOKEN_EXPIRED'));
    }

    /**
     * Retrieves and checks the cache status from Google's AI Platform.
     *
     * Makes an HTTP GET request to the AI Platform cache configuration endpoint
     * using the project ID from the service account information. The method
     * verifies if the cache is enabled by checking the 'disableCache' key in the
     * response.
     *
     * @return bool true if the cache is enabled, false if the cache is disabled
     * @throws \moodle_exception if the HTTP request to retrieve the cache status fails.
     */
    public function get_google_cache_status(): bool {
        $client = new http_client([
                'timeout' => get_config('local_ai_manager', 'requesttimeout'),
        ]);

        $options['headers'] = [
                'Authorization' => 'Bearer ' . $this->get_access_token(),
        ];

        $serviceaccountinfo = json_decode($this->serviceaccountinfo);
        $projectid = trim($serviceaccountinfo->project_id);

        $response = $client->get('https://europe-west3-aiplatform.googleapis.com/v1beta1/projects/' . $projectid . '/cacheConfig',
                $options);
        if ($response->getStatusCode() !== 200) {
            throw new \moodle_exception('exception_retrievingcachestatus', 'local_ai_manager', '', '',
                    $response->getBody()->getContents());
        } else {
            $result = json_decode($response->getBody()->getContents(), true);
            return !array_key_exists('disableCache', $result);
        }
    }

    /**
     * Sets the Google cache status for the specified project.
     *
     * @param bool $status Determines whether the cache should be enabled or disabled.
     * @return bool Returns true if the cache status was successfully set, false otherwise.
     */
    public function set_google_cache_status(bool $status): bool {
        $client = new http_client([
                'timeout' => get_config('local_ai_manager', 'requesttimeout'),
        ]);
        $options['headers'] = [
                'Authorization' => 'Bearer ' . $this->get_access_token(),
        ];

        $serviceaccountinfo = json_decode($this->serviceaccountinfo);
        $projectid = trim($serviceaccountinfo->project_id);

        $data = [
                'name' => 'projects/' . $projectid . '/cacheConfig',
                'disableCache' => !$status,
        ];

        $options['body'] = json_encode($data);

        $response = $client->patch('https://europe-west3-aiplatform.googleapis.com/v1beta1/projects/' . $projectid . '/cacheConfig',
                $options);
        return $response->getStatusCode() === 200;
    }
}
