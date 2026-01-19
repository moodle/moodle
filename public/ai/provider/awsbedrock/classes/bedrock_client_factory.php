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

namespace aiprovider_awsbedrock;

use Aws\BedrockRuntime\BedrockRuntimeClient;

/**
 * AWS Bedrock client factory.
 *
 * @package    aiprovider_awsbedrock
 * @copyright  2025 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class bedrock_client_factory {
    /**
     * Create an instance of BedrockRuntimeClient.
     *
     * @param string $region The AWS region.
     * @param string $key The AWS IAM API key.
     * @param string $secret The AWS IAM API secret.
     * @param string $version The API version.
     * @return BedrockRuntimeClient.
     */
    public function create_client(
        string $region,
        string $key,
        string $secret,
        string $version = 'latest'
    ): BedrockRuntimeClient {
        $credentials = [
            'key' => $key,
            'secret' => $secret,
        ];

        return new BedrockRuntimeClient([
            'region'      => $region,
            'version'     => $version,
            'credentials' => $credentials,
        ]);
    }
}
