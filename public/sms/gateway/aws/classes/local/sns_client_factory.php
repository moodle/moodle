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

namespace smsgateway_aws\local;

use Aws\Sns\SnsClient;

/**
 * Factory for the AWS SNS client.
 *
 * Fetch it with \core\di so that tests can replace the client.
 *
 * @package    smsgateway_aws
 * @copyright  2026 Danrave Keh
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class sns_client_factory {
    /**
     * Create an SNS client.
     *
     * @param array $params The SnsClient constructor options.
     * @return SnsClient
     */
    public function create_client(array $params): SnsClient {
        return new SnsClient($params);
    }
}
