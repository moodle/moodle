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

use communication_matrix\local\command;
use core\http_client;
use DirectoryIterator;
use Exception;
use GuzzleHttp\Psr7\Response;

/**
 * The abstract class for a versioned API client for Matrix.
 *
 * Matrix uses a versioned API, and a handshake occurs between the Client (Moodle) and server, to determine the APIs available.
 *
 * This client represents a version-less API client.
 * Versions are implemented by combining the various features into a versionedclass.
 * See v1p1 for example.
 *
 * @package    communication_matrix
 * @copyright  2023 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class matrix_client {
    /** @var string $serverurl The URL of the home server */
    /** @var string $accesstoken The access token of the matrix server */

    /** @var http_client|null The client to use */
    protected static http_client|null $client = null;

    /**
     * Matrix events constructor to get the room id and refresh token usage if required.
     *
     * @param string $serverurl The URL of the API server
     * @param string $accesstoken The admin access token
     */
    protected function __construct(
        protected string $serverurl,
        protected string $accesstoken,
    ) {
    }

    /**
     * Return the versioned instance of the API.
     *
     * @param string $serverurl The URL of the API server
     * @param string $accesstoken The admin access token to use
     * @return matrix_client|null
     */
    public static function instance(
        string $serverurl,
        string $accesstoken,
    ): ?matrix_client {
        // Fetch the list of supported API versions.
        $clientversions = self::get_supported_versions();

        // Fetch the supported versions from the server.
        $serversupports = self::query_server_supports($serverurl);
        if ($serversupports === null) {
            // Unable to fetch the server versions.
            return null;
        }
        $serverversions = $serversupports->versions;

        // Calculate the intersections and sort to determine the highest combined version.
        $versions = array_intersect($clientversions, $serverversions);
        if (count($versions) === 0) {
            // No versions in common.
            throw new \moodle_exception('No supported Matrix API versions found.');
        }
        asort($versions);
        $version = array_key_last($versions);

        $classname = \communication_matrix\local\spec::class . '\\' . $version;

        return new $classname(
            $serverurl,
            $accesstoken,
        );
    }

    /**
     * Determine if the API supports a feature.
     *
     * If an Array is provided, this will return true if any of the specified features is implemented.
     *
     * @param string[]|string $feature The feature to check. This is in the form of a namespaced class.
     * @return bool
     */
    public function implements_feature(array|string $feature): bool {
        if (is_array($feature)) {
            foreach ($feature as $thisfeature) {
                if ($this->implements_feature($thisfeature)) {
                    return true;
                }
            }

            // None of the features are implemented in this API version.
            return false;
        }

        return in_array($feature, $this->get_supported_features());
    }

    /**
     * Get a list of the features supported by this client.
     *
     * @return string[]
     */
    public function get_supported_features(): array {
        $features = [];
        $class = static::class;
        do {
            $features = array_merge($features, class_uses($class));
            $class = get_parent_class($class);
        } while ($class);

        return $features;
    }

    /**
     * Require that the API supports a feature.
     *
     * If an Array is provided, this is treated as a require any of the features.
     *
     * @param string[]|string $feature The feature to test
     * @throws \moodle_exception
     */
    public function require_feature(array|string $feature): void {
        if (!$this->implements_feature($feature)) {
            if (is_array($feature)) {
                $features = implode(', ', $feature);
                throw new \moodle_exception(
                    "None of the possible feature are implemented in this Matrix Client: '{$features}'"
                );
            }
            throw new \moodle_exception("The requested feature is not implemented in this Matrix Client: '{$feature}'");
        }
    }

    /**
     * Require that the API supports a list of features.
     *
     * All features specified will be required.
     *
     * If an array is provided as one of the features, any of the items in the nested array will be required.
     *
     * @param string[]|array[] $features The list of features required
     *
     * Here is an example usage:
     * <code>
     * $matrixapi->require_features([
     *
     *     \communication_matrix\local\spec\features\create_room::class,
     *     [
     *         \communication_matrix\local\spec\features\get_room_info_v1::class,
     *         \communication_matrix\local\spec\features\get_room_info_v2::class,
     *     ]
     * ])
     * </code>
     */
    public function require_features(array $features): void {
        array_walk($features, [$this, 'require_feature']);
    }

    /**
     * Get the URL of the server.
     *
     * @return string
     */
    public function get_server_url(): string {
        return $this->serverurl;
    }

    /**
     * Query the supported versions, and any unstable features, from the server.
     *
     * Servers must implement the client versions API described here:
     * - https://spec.matrix.org/latest/client-server-api/#get_matrixclientversions
     *
     * @param string $serverurl The server base
     * @return null|\stdClass The list of supported versions and a list of enabled unstable features
     */
    protected static function query_server_supports(string $serverurl): ?\stdClass {
        // Attempt to return from the cache first.
        $cache = \cache::make('communication_matrix', 'serverversions');
        $serverkey = sha1($serverurl);
        if ($cache->get($serverkey)) {
            return $cache->get($serverkey);
        }

        // Not in the cache - fetch and store in the cache.
        try {
            $client = static::get_http_client();
            $response = $client->get("{$serverurl}/_matrix/client/versions");

            $supportsdata = json_decode(
                json: $response->getBody(),
                associative: false,
                flags: JSON_THROW_ON_ERROR,
            );

            $cache->set($serverkey, $supportsdata);
            return $supportsdata;
        } catch (\GuzzleHttp\Exception\TransferException $e) {
            return null;
        }
    }

    /**
     * Get the list of supported versions based on the available classes.
     *
     * @return array
     */
    public static function get_supported_versions(): array {
        $versions = [];
        $iterator = new DirectoryIterator(__DIR__ . '/local/spec');
        foreach ($iterator as $fileinfo) {
            if ($fileinfo->isDir()) {
                continue;
            }

            // Get the classname from the filename.
            $classname = substr($fileinfo->getFilename(), 0, -4);

            if (!preg_match('/^v\d+p\d+$/', $classname)) {
                // @codeCoverageIgnoreStart
                // This file does not fit the format v[MAJOR]p[MINOR]].
                continue;
                // @codeCoverageIgnoreEnd
            }

            $versions[$classname] = "v" . self::get_version_from_classname($classname);
        }

        return $versions;
    }

    /**
     * Get the current token in use.
     *
     * @return string
     */
    public function get_token(): string {
        return $this->accesstoken;
    }

    /**
     * Helper to fetch the HTTP Client for the instance.
     *
     * @return \core\http_client
     */
    protected function get_client(): \core\http_client {
        return static::get_http_client();
    }

    /**
     * Helper to fetch the HTTP Client.
     *
     * @return \core\http_client
     */
    protected static function get_http_client(): \core\http_client {
        if (static::$client !== null) {
            return static::$client;
        }
        // @codeCoverageIgnoreStart
        return new http_client();
        // @codeCoverageIgnoreEnd
    }

    /**
     * Execute the specified command.
     *
     * @param command $command
     * @return Response
     */
    protected function execute(
        command $command,
    ): Response {
        $client = $this->get_client();
        return $client->send(
            $command,
            $command->get_options(),
        );
    }

    /**
     * Get the API version of the current instance.
     *
     * @return string
     */
    public function get_version(): string {
        $reflect = new \ReflectionClass(static::class);
        $classname = $reflect->getShortName();
        return self::get_version_from_classname($classname);
    }

    /**
     * Normalise an API version from a classname.
     *
     * @param string $classname The short classname, omitting any namespace or file extension
     * @return string The normalised version
     */
    protected static function get_version_from_classname(string $classname): string {
        $classname = str_replace('v', '', $classname);
        $classname = str_replace('p', '.', $classname);
        return $classname;
    }

    /**
     * Check if the API version is at least the specified version.
     *
     * @param string $minversion The minimum API version required
     * @return bool
     */
    public function meets_version(string $minversion): bool {
        $thisversion = $this->get_version();
        return version_compare($thisversion, $minversion) >= 0;
    }

    /**
     * Assert that the API version is at least the specified version.
     *
     * @param string $minversion The minimum API version required
     * @throws Exception
     */
    public function requires_version(string $minversion): void {
        if ($this->meets_version($minversion)) {
            return;
        }

        throw new \moodle_exception("Matrix API version {$minversion} or higher is required for this command.");
    }
}
