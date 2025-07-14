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

namespace core\aws;
use Aws\AwsClient;

/**
 * AWS Client factory. Retrieves a client with moodle specific HTTP configuration.
 *
 * @copyright  2022 Catalyst IT
 * @author     Peter Burnett <peterburnett@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @deprecated Since Moodle 4.5
 * @todo       MDL-82459 Final deprecation in Moodle 5.0.
 */
class client_factory {
    /**
     * Get an AWS client with moodle specific HTTP configuration.
     *
     * @param string $class Fully qualified AWS classname e.g. \Aws\S3\S3Client
     * @param array $opts array of constructor options for AWS Client.
     * @return AwsClient
     * @deprecated Since Moodle 4.5
     */
    #[\core\attribute\deprecated(
        'client_factory::get_client()',
        since: '4.5',
        mdl: 'MDL-80962',
    )]
    public static function get_client(string $class, array $opts): AwsClient {
        \core\deprecation::emit_deprecation_if_present(__FUNCTION__);
        // Modify the opts to add HTTP timeouts.
        if (empty($opts['http'])) {
            $opts['http'] = ['connect_timeout' => HOURSECS];
        } else if (!array_key_exists('connect_timeout', $opts['http'])) {
            // Try not to override existing settings.
            $opts['http']['connect_timeout'] = HOURSECS;
        }

        // Blindly trust the call here. If it exceptions, the raw message is the most useful.
        $client = new $class($opts);
        if (!$client instanceof \Aws\AwsClient) {
            throw new \moodle_exception('clientnotfound', 'factor_sms');
        }

        // Now we can configure the proxy with the routing aware middleware.
        return aws_helper::configure_client_proxy($client);
    }
}
