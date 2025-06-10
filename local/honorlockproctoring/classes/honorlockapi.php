<?php
// This file is part of the honorlockproctoring module for Moodle - http://moodle.org/
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
namespace local_honorlockproctoring;

/**
 * Honorlock proctoring module.
 *
 * @package    local_honorlockproctoring
 * @copyright  2023 Honorlock (https://honorlock.com/)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class honorlockapi {

    /**
     * const string The component name.
     */
    private const COMPONENT_NAME = 'local_honorlockproctoring';

    /**
     * const string The API token cache key name.
     */
    private const HONORLOCK_API_TOKEN_CACHE_KEY = 'honorlock_api_token';

    /**
     * The global configuration instance.
     *
     * @var mixed hash-like object or single value, return false no config found
     */
    private $config;

    /**
     * The class constructor.
     *
     * @throws \dml_exception
     */
    public function __construct() {
        $this->config = get_config(self::COMPONENT_NAME);
    }

    /**
     * Get the `Honorlock API Token` in order to authenticate the API requests.
     *
     * @return string
     */
    private function get_token(): string {
        $honorlockapicache = \cache::make(self::COMPONENT_NAME, self::HONORLOCK_API_TOKEN_CACHE_KEY);
        $cachedtoken = $honorlockapicache->get(self::HONORLOCK_API_TOKEN_CACHE_KEY);

        // If a cache with the given key already exists.
        if ($cachedtoken && $cachedtoken['expiration_time'] > time()) {
            return $cachedtoken['token'];
        }

        $token = $this->generate_token();

        // In case of the installation process.
        if (!$token) {
            return '';
        }

        $expirationtime = time() + $token->expires_in;
        $cachedata = [
            'token' => $token->access_token,
            'expiration_time' => $expirationtime,
        ];

        $honorlockapicache->set(self::HONORLOCK_API_TOKEN_CACHE_KEY, $cachedata);

        return $token->access_token;
    }

    /**
     * Generates a new `Honorlock API` token.
     *
     * @return object|null
     */
    private function generate_token(): ?object {
        $honorlockclientid = $this->config->honorlock_client_id;
        $honorlockclientsecret = $this->config->honorlock_client_secret;
        $url = $this->config->honorlock_url;
        $curl = new \curl();

        $jsonresult = $curl->post(
            "$url/api/en/v1/token",
            [
                'client_id' => $honorlockclientid,
                'client_secret' => $honorlockclientsecret,
            ]
        );

        if (!isset((json_decode($jsonresult)->data))) {
            return null;
        }

        return json_decode($jsonresult)->data;
    }

    /**
     * Send request.
     *
     * @param string $type
     * @param string $endpoint
     * @param array $payload
     * @return object|mixed|null
     */
    public function send_request(string $type, string $endpoint, array $payload = []): ?object {
        $token = $this->get_token();
        $curl = new \curl();
        $curl->setHeader(
            [
                'Accept: application/json',
                "Authorization: Bearer $token",
            ]
        );

        $jsonresult = null;

        $url = $this->config->honorlock_url;
        if ($type === "get") {
            $jsonresult = $curl->get($url.$endpoint);
        }

        if ($type === "post") {
            $jsonresult = $curl->post($url.$endpoint, $payload);
        }

        if (!$jsonresult) {
            return null;
        }

        return json_decode($jsonresult);
    }
}
